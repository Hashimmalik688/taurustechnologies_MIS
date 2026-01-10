@extends('layouts.master')

@section('title')
    Add Public Holiday
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.public-holidays.index') }}">Public Holidays</a>
        @endslot
        @slot('title')
            Add Holiday
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-calendar-plus me-2"></i>Add New Public Holiday
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.public-holidays.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date') }}" 
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
                                   value="{{ old('name') }}" 
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
                                      placeholder="Add any notes about this holiday...">{{ old('description') }}</textarea>
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Attendance will be skipped on this day)
                                </label>
                            </div>
                            <small class="text-muted">When active, employees won't be marked absent on this day</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i>Add Holiday
                            </button>
                            <a href="{{ route('admin.public-holidays.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-close me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Add Common Holidays -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-lightbulb-outline me-2"></i>Common Holidays for {{ date('Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Here are some common holidays you might want to add:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> New Year's Day (January 1)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Kashmir Day (February 5)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Pakistan Day (March 23)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Labour Day (May 1)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Independence Day (August 14)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Iqbal Day (November 9)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Quaid-e-Azam Day (December 25)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Eid ul-Fitr (Islamic calendar)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Eid ul-Adha (Islamic calendar)</li>
                        <li class="mb-2"><i class="mdi mdi-circle-small text-primary"></i> Eid Milad un-Nabi (Islamic calendar)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
