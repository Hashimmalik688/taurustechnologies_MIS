@extends('layouts.master')

@section('title')
    Public Holidays
@endsection

@section('css')
    <style>
        .holiday-card {
            transition: all 0.3s ease;
        }
        .holiday-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .holiday-date {
            font-size: 2rem;
            font-weight: bold;
        }
        .badge-upcoming {
            background-color: #556ee6;
        }
        .badge-past {
            background-color: #74788d;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Admin
        @endslot
        @slot('title')
            Public Holidays
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Upcoming Holidays Section -->
    @if($upcomingHolidays->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary-subtle">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-calendar-star me-2"></i>Upcoming Holidays
                    </h5>
                    <div class="row">
                        @foreach($upcomingHolidays as $holiday)
                        <div class="col-md-4 mb-3">
                            <div class="card holiday-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="holiday-date text-primary">
                                                {{ $holiday->date->format('d') }}
                                            </div>
                                            <div class="text-muted">
                                                {{ $holiday->date->format('M Y') }}
                                            </div>
                                        </div>
                                        <span class="badge badge-upcoming">{{ $holiday->date->diffForHumans() }}</span>
                                    </div>
                                    <h6 class="mt-3 mb-1">{{ $holiday->name }}</h6>
                                    @if($holiday->description)
                                    <p class="text-muted mb-0 small">{{ Str::limit($holiday->description, 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Holidays List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-calendar-multiple me-2"></i>All Public Holidays
                        </h4>
                        <a href="{{ route('admin.public-holidays.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i>Add Holiday
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($holidays->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Holiday Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holidays as $holiday)
                                <tr>
                                    <td>
                                        <strong>{{ $holiday->date->format('d M Y') }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-info">{{ $holiday->date->format('l') }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $holiday->name }}</strong>
                                        @if($holiday->date->isPast())
                                            <span class="badge badge-past ms-2">Past</span>
                                        @elseif($holiday->date->isToday())
                                            <span class="badge bg-success ms-2">Today</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($holiday->description)
                                            <small class="text-muted">{{ Str::limit($holiday->description, 50) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.public-holidays.toggle', $holiday) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link p-0">
                                                @if($holiday->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.public-holidays.edit', $holiday) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.public-holidays.destroy', $holiday) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this holiday?');"
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
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
                        <i class="mdi mdi-calendar-remove display-4 text-muted"></i>
                        <h5 class="mt-3">No holidays configured</h5>
                        <p class="text-muted">Add your first public holiday to manage attendance on special days.</p>
                        <a href="{{ route('admin.public-holidays.create') }}" class="btn btn-primary mt-3">
                            <i class="mdi mdi-plus me-1"></i>Add Holiday
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
