@extends('layouts.master')

@section('title')
    Edit Public Holiday
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.public-holidays.index') }}">Public Holidays</a>
        @endslot
        @slot('title')
            Edit Holiday
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-pencil me-2"></i>Edit Public Holiday
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.public-holidays.update', $holiday) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', $holiday->date->format('Y-m-d')) }}" 
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select the date of the public holiday</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $holiday->name) }}" 
                                   placeholder="e.g., New Year's Day, Eid ul-Fitr"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Add any notes about this holiday...">{{ old('description', $holiday->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       {{ old('is_active', $holiday->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Attendance will be skipped on this day)
                                </label>
                            </div>
                            <small class="text-muted">When active, employees won't be marked absent on this day</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i>Update Holiday
                            </button>
                            <a href="{{ route('admin.public-holidays.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-close me-1"></i>Cancel
                            </a>
                            <button type="button" 
                                    class="btn btn-danger float-end" 
                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this holiday?')) document.getElementById('delete-form').submit();">
                                <i class="mdi mdi-delete me-1"></i>Delete Holiday
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" 
                          action="{{ route('admin.public-holidays.destroy', $holiday) }}" 
                          method="POST" 
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Holiday Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-information-outline me-2"></i>Holiday Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Created:</strong> {{ $holiday->created_at->format('M d, Y g:i A') }}</p>
                            <p class="mb-2"><strong>Last Updated:</strong> {{ $holiday->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Days Until:</strong> 
                                @if($holiday->date->isFuture())
                                    {{ $holiday->date->diffForHumans() }}
                                @elseif($holiday->date->isToday())
                                    <span class="badge bg-success">Today</span>
                                @else
                                    <span class="badge bg-secondary">Past</span>
                                @endif
                            </p>
                            <p class="mb-2"><strong>Day of Week:</strong> {{ $holiday->date->format('l') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
