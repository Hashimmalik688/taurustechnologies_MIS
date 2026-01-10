@extends('layouts.master')

@section('title')
    Edit Holiday
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.holidays.index') }}">Holidays</a>
        @endslot
        @slot('title')
            Edit Holiday
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="mdi mdi-calendar-edit me-2"></i>Edit Holiday</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.holidays.update', $holiday) }}" method="POST">
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
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $holiday->name) }}" 
                                   placeholder="e.g., New Year's Day, Eid-ul-Fitr" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Optional description for this holiday">{{ old('description', $holiday->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_recurring" 
                                       name="is_recurring" 
                                       value="1"
                                       {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_recurring">
                                    Recurring Holiday (Annual)
                                </label>
                                <small class="form-text text-muted d-block">
                                    Check this for holidays that occur every year
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i>Update Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
