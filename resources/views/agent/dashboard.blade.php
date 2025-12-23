@extends('layouts.master')

@section('title')
    Agent Dashboard
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            My Sales Dashboard
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Welcome back, {{ $agent->name }}!</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Your sales performance overview</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Leads</p>
                            <h4 class="mb-0">{{ $stats['total_leads'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-user font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Today's Leads</p>
                            <h4 class="mb-0">{{ $stats['today_leads'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-calendar-check font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">This Month</p>
                            <h4 class="mb-0">{{ $stats['this_month_leads'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-bar-chart-alt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Sales Closed</p>
                            <h4 class="mb-0">{{ $stats['sold'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Sales Pipeline --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">My Sales Pipeline</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <p class="text-muted mb-2">Pending</p>
                                <h5 class="mb-0">{{ $stats['pending'] }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <p class="text-muted mb-2">In Progress</p>
                                <h5 class="mb-0">{{ $stats['contacted'] }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <p class="text-muted mb-2">Closed Won</p>
                                <h5 class="mb-0">{{ $stats['sold'] }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table align-middle table-nowrap">
                            <thead>
                                <tr>
                                    <th>Lead Name</th>
                                    <th>Status</th>
                                    <th>Premium</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-1">
                                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-dark">
                                                    {{ $lead->full_name ?? $lead->name ?? 'N/A' }}
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-0">{{ $lead->email ?? 'No email' }}</p>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'new' => 'primary',
                                                    'contacted' => 'info',
                                                    'qualified' => 'success',
                                                    'sold' => 'success',
                                                    'lost' => 'danger',
                                                    'pending' => 'warning'
                                                ];
                                                $color = $statusColors[$lead->status ?? 'new'] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($lead->status ?? 'New') }}</span>
                                        </td>
                                        <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                        <td>{{ $lead->created_at ? $lead->created_at->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No leads assigned yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Performance --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Monthly Performance</h4>

                    <div class="text-center">
                        <div class="mb-4">
                            <i class="bx bx-dollar-circle text-primary display-4"></i>
                        </div>
                        <h3>${{ number_format($monthlyRevenue, 2) }}</h3>
                        <p class="text-muted">Total Revenue This Month</p>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table align-middle table-nowrap table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-0">New Leads</h5>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <span class="badge bg-primary font-size-12">{{ $salesByStatus['new'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-0">Contacted</h5>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <span class="badge bg-info font-size-12">{{ $salesByStatus['contacted'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-0">Qualified</h5>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <span class="badge bg-success font-size-12">{{ $salesByStatus['qualified'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-0">Sold</h5>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <span class="badge bg-success font-size-12">{{ $salesByStatus['sold'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-0">Lost</h5>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <span class="badge bg-danger font-size-12">{{ $salesByStatus['lost'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- My Carriers & Commissions --}}
    @if($carrierCommissions->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">My Insurance Carriers & Commission Rates</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Carrier Name</th>
                                    <th>Payment Module</th>
                                    <th>Base Commission %</th>
                                    <th>My Commission %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carrierCommissions as $commission)
                                    <tr>
                                        <td>
                                            <strong>{{ $commission->insuranceCarrier->name }}</strong>
                                            @if($commission->insuranceCarrier->phone)
                                                <br><small class="text-muted"><i class="mdi mdi-phone"></i> {{ $commission->insuranceCarrier->phone }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucwords(str_replace('_', ' ', $commission->insuranceCarrier->payment_module)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $commission->insuranceCarrier->base_commission_percentage }}%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $commission->commission_percentage }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Quick Actions</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-primary">
                            <i class="bx bx-list-ul me-1"></i> View All My Leads
                        </a>
                        <a href="{{ route('chat.index') }}" class="btn btn-info">
                            <i class="bx bx-chat me-1"></i> Team Chat
                        </a>
                        <a href="{{ route('attendance.dashboard') }}" class="btn btn-success">
                            <i class="bx bx-time-five me-1"></i> My Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
