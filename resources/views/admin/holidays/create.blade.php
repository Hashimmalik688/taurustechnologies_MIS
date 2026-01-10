@extends('layouts.master')

@section('title')
    Add Holiday
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.holidays.index') }}">Holidays</a>
        @endslot
        @slot('title')
            Add New Holiday
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="mdi mdi-calendar-plus me-2"></i>Add Public Holiday</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.holidays.store') }}" method="POST">
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
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
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
                                      placeholder="Optional description for this holiday">{{ old('description') }}</textarea>
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
                                       {{ old('is_recurring') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_recurring">
                                    Recurring Holiday (Annual)
                                </label>
                                <small class="form-text text-muted d-block">
                                    Check this for holidays that occur every year (e.g., National holidays, religious festivals)
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i>Add Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Add Examples -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Common Holidays (Quick Reference)</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Islamic Holidays:</strong> Eid-ul-Fitr, Eid-ul-Adha, Shab-e-Qadr</li>
                        <li><strong>National Days:</strong> Independence Day (Aug 14), Pakistan Day (Mar 23)</li>
                        <li><strong>Other:</strong> New Year's Day, Labour Day, Quaid-e-Azam's Birthday</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
