@extends('layouts.master')

@section('title', 'PABS - Support Tickets')

@section('css')
<style>
    .border-left-primary { border-left: 4px solid #0d6efd; }
    .border-left-info { border-left: 4px solid #0dcaf0; }
    .border-left-success { border-left: 4px solid #198754; }
    .border-left-danger { border-left: 4px solid #dc3545; }
    .border-left-warning { border-left: 4px solid #ffc107; }

    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
    }

    .kpi-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 160px;
    }

    .kpi-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: inherit !important;
    }

    .kpi-icon i {
        color: inherit !important;
    }

    .kpi-icon.text-primary,
    .kpi-icon.text-primary i {
        color: #0d6efd !important;
    }

    .kpi-icon.text-info,
    .kpi-icon.text-info i {
        color: #0dcaf0 !important;
    }

    .kpi-icon.text-success,
    .kpi-icon.text-success i {
        color: #198754 !important;
    }

    .kpi-icon.text-danger,
    .kpi-icon.text-danger i {
        color: #dc3545 !important;
    }

    .kpi-icon.text-warning,
    .kpi-icon.text-warning i {
        color: #ffc107 !important;
    }

    .kpi-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 8px;
        text-align: center;
    }

    .kpi-value {
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-open { background-color: #cfe2ff; color: #084298; }
    .status-in-progress { background-color: #d1ecf1; color: #055160; }
    .status-on-hold { background-color: #fff3cd; color: #664d03; }
    .status-resolved { background-color: #d1e7dd; color: #0f5132; }
    .status-closed { background-color: #e2e3e5; color: #41464b; }

    .priority-high { color: #dc3545; font-weight: 600; }
    .priority-medium { color: #fd7e14; font-weight: 600; }
    .priority-low { color: #28a745; font-weight: 600; }

    .section-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.2rem;
        font-size: 0.75rem;
        background-color: #f0f0f0;
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') PABS - Support Tickets @endslot
    @endcomponent

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="row mb-4 g-3">
        <!-- Total Tickets -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-primary">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-primary">
                        <i class="bx bx-list-check"></i>
                    </div>
                    <p class="kpi-label">Total Tickets</p>
                    <p class="kpi-value text-primary">{{ $kpis['total_tickets'] }}</p>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-info">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-info">
                        <i class="bx bx-folder-open"></i>
                    </div>
                    <p class="kpi-label">Open Tickets</p>
                    <p class="kpi-value text-info">{{ $kpis['open_tickets'] }}</p>
                </div>
            </div>
        </div>

        <!-- Closed Tickets -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-success">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <p class="kpi-label">Closed Tickets</p>
                    <p class="kpi-value text-success">{{ $kpis['closed_tickets'] }}</p>
                </div>
            </div>
        </div>

        <!-- High Priority -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-danger">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-danger">
                        <i class="bx bx-up-arrow-alt"></i>
                    </div>
                    <p class="kpi-label">High Priority</p>
                    <p class="kpi-value text-danger">{{ $kpis['high_priority'] }}</p>
                </div>
            </div>
        </div>

        <!-- Medium Priority -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-warning">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-warning">
                        <i class="bx bx-minus-circle"></i>
                    </div>
                    <p class="kpi-label">Medium Priority</p>
                    <p class="kpi-value text-warning">{{ $kpis['medium_priority'] }}</p>
                </div>
            </div>
        </div>

        <!-- Low Priority -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-success">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-success">
                        <i class="bx bx-down-arrow-alt"></i>
                    </div>
                    <p class="kpi-label">Low Priority</p>
                    <p class="kpi-value text-success">{{ $kpis['low_priority'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Create Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Support Tickets</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('pabs.tickets.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> New Ticket
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="section_id" class="form-select form-select-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $id => $name)
                            <option value="{{ $id }}" {{ request('section_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>Open</option>
                        <option value="IN PROGRESS" {{ request('status') == 'IN PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="ON HOLD" {{ request('status') == 'ON HOLD' ? 'selected' : '' }}>On Hold</option>
                        <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                        <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search code or subject" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="12%">Code</th>
                        <th width="22%">Subject</th>
                        <th width="10%">Section</th>
                        <th width="10%">Status</th>
                        <th width="10%">Approval</th>
                        <th width="10%">Priority</th>
                        <th width="13%">Created By</th>
                        <th width="8%">Assigned</th>
                        <th width="5%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <strong>{{ $ticket->ticket_code }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('pabs.tickets.show', $ticket) }}" class="text-decoration-none">
                                    {{ Str::limit($ticket->subject, 30) }}
                                </a>
                            </td>
                            <td>
                                <span class="section-badge">
                                    {{ $sections[$ticket->section_id] ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ Str::lower($ticket->status) }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->approval_status === 'APPROVED' ? 'success' : ($ticket->approval_status === 'REJECTED' ? 'danger' : 'warning') }}">
                                    {{ $ticket->approval_status }}
                                </span>
                            </td>
                            <td>
                                <span class="priority-{{ Str::lower($ticket->priority) }}">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $ticket->creator->name }}</small>
                            </td>
                            <td>
                                @if($ticket->assignee)
                                    <small>{{ $ticket->assignee->name }}</small>
                                @else
                                    <small class="text-muted">Unassigned</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pabs.tickets.show', $ticket) }}" class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="bx bx-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <span class="text-muted">No tickets found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($tickets->hasPages())
        <div class="mt-3">
            {{ $tickets->links() }}
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endsection
