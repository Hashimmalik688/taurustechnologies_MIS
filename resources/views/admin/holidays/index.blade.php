@extends('layouts.master')

@section('title')
    Public Holidays
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Settings
        @endslot
        @slot('title')
            Public Holidays
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card border-primary">
                <div class="card-header bg-primary-subtle">
                    <h5 class="mb-0 text-primary"><i class="mdi mdi-calendar-star me-2"></i>Upcoming Holidays</h5>
                </div>
                <div class="card-body">
                    @if($upcomingHolidays->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingHolidays as $holiday)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $holiday->name }}</h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="mdi mdi-calendar"></i> {{ $holiday->date->format('M d, Y') }} ({{ $holiday->date->diffForHumans() }})
                                            </p>
                                            @if($holiday->description)
                                                <p class="mb-0 text-muted small">{{ Str::limit($holiday->description, 50) }}</p>
                                            @endif
                                        </div>
                                        @if($holiday->is_recurring)
                                            <span class="badge badge-soft-info">Recurring</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-calendar-blank text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">No upcoming holidays</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="mdi mdi-calendar-multiple me-2"></i>All Holidays</h5>
                    <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i>Add Holiday
                    </a>
                </div>
                <div class="card-body">
                    @if($holidays->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Holiday Name</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($holidays as $holiday)
                                        <tr>
                                            <td>
                                                <strong>{{ $holiday->date->format('M d, Y') }}</strong><br>
                                                <small class="text-muted">{{ $holiday->date->format('l') }}</small>
                                            </td>
                                            <td>{{ $holiday->name }}</td>
                                            <td>
                                                @if($holiday->description)
                                                    {{ Str::limit($holiday->description, 60) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($holiday->is_recurring)
                                                    <span class="badge badge-soft-info">Recurring</span>
                                                @else
                                                    <span class="badge badge-soft-secondary">One-time</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.holidays.edit', $holiday) }}" 
                                                   class="btn btn-sm btn-soft-primary">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.holidays.destroy', $holiday) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $holidays->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-blank text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3">No holidays configured</h5>
                            <p class="text-muted">Start by adding your first holiday</p>
                            <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary mt-2">
                                <i class="mdi mdi-plus me-1"></i>Add Holiday
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
