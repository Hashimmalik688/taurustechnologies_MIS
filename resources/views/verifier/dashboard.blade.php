@extends('layouts.master')

@section('title')
    My Verifications
@endsection

@section('css')
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 12px;
    }
    .status-transferred { background: #17a2b8; color: white; }
    .status-xfer { background: #28a745; color: white; }
    .status-failed { background: #dc3545; color: white; }
    .status-pending { background: #ffc107; color: #000; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Verifier @endslot
        @slot('title') My Dashboard @endslot
    @endcomponent

    <!-- Date Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('verifier.dashboard') }}" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-7">
                                <label class="form-label fw-bold">Date Range</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="filter" id="filter_all" value="all" {{ $filter === 'all' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_all">All Time</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_today" value="today" {{ $filter === 'today' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_today">Today</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_yesterday" value="yesterday" {{ $filter === 'yesterday' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_yesterday">Yesterday</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_week" value="week" {{ $filter === 'week' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_week">This Week</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_custom" value="custom" {{ $filter === 'custom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="filter_custom">Custom</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="customDateInputs" style="display: {{ $filter === 'custom' ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Start</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">End</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" id="customSubmitBtn" style="display: {{ $filter === 'custom' ? 'block' : 'none' }};">
                                <button type="submit" class="btn btn-primary w-100">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Activity KPI Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="mdi mdi-chart-line"></i> 
                @if($filter === 'all')
                    All Time Activity
                @elseif($filter === 'today')
                    Today's Activity
                @elseif($filter === 'yesterday')
                    Yesterday's Activity
                @elseif($filter === 'week')
                    This Week's Activity
                @else
                    Selected Period Activity
                @endif
                @if($filter !== 'all')
                    <small class="text-muted">({{ \Carbon\Carbon::parse($startDate)->timezone('America/Denver')->format('M d, Y g:i A') }} - {{ \Carbon\Carbon::parse($endDate)->timezone('America/Denver')->format('M d, Y g:i A') }} MT)</small>
                @endif
            </h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="mdi mdi-account-check text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['total_verified'] ?? 0 }}</h3>
                    <small class="text-muted">Verified</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="mdi mdi-transfer text-info" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['transferred'] ?? 0 }}</h3>
                    <small class="text-muted">Transferred</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="mdi mdi-check-circle text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['closed'] ?? 0 }}</h3>
                    <small class="text-muted">Closed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="mdi mdi-currency-usd text-success" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['sales'] ?? 0 }}</h3>
                    <small class="text-muted">Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="mdi mdi-clock-alert text-secondary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="mdi mdi-close-circle text-danger" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['declined'] ?? 0 }}</h3>
                    <small class="text-muted">Declined</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Performance Stats Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3"><i class="mdi mdi-chart-box"></i> Overall Statistics</h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Forms</h6>
                    <h2 class="mb-0 fw-bold">{{ $filteredTotal ?? 0 }}</h2>
                    <small>In selected period</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success">
                <div class="card-body text-center">
                    <h6 class="mb-2">Success Rate</h6>
                    <h2 class="mb-0 fw-bold">
                        @php
                            $total = $leads->count();
                            $successful = $leads->whereIn('status', ['closed', 'sale'])->count();
                            $rate = $total > 0 ? round(($successful / $total) * 100) : 0;
                        @endphp
                        {{ $rate }}%
                    </h2>
                    <small>{{ $successful }} Sales / {{ $total }} total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body text-center">
                    <h6 class="mb-2">Pending Callbacks</h6>
                    <h2 class="mb-0 fw-bold">{{ $leads->where('status', 'pending')->count() }}</h2>
                    <small>{{ request('show_all_leads') ? 'All pending' : 'Current pending' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Declined Calls</h6>
                    <h2 class="mb-0 fw-bold">{{ $leads->whereIn('status', ['declined', 'rejected'])->count() }}</h2>
                    <small>In selected period</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4 class="card-title mb-0 text-white"><i class="bx bx-list-ul me-2"></i>My Transferred Forms</h4>
                    <a href="{{ route('verifier.create.team', 'peregrine') }}" class="btn btn-light btn-sm">
                        <i class="bx bx-plus me-1"></i> New Form
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Closer Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td>{{ $lead->date }}</td>
                                        <td><strong>{{ $lead->cn_name }}</strong></td>
                                        <td>{{ $lead->closer_name }}</td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'transferred' => ['label' => 'Transferred', 'class' => 'status-transferred'],
                                                    'closed' => ['label' => 'Closed', 'class' => 'status-xfer'],
                                                    'sale' => ['label' => 'Sale', 'class' => 'status-xfer'],
                                                    'declined' => ['label' => $lead->decline_reason ?? 'Declined', 'class' => 'status-failed'],
                                                    'rejected' => ['label' => $lead->failure_reason ?? 'Failed', 'class' => 'status-failed'],
                                                    'pending' => ['label' => $lead->pending_reason ?? 'Pending', 'class' => 'status-pending'],
                                                    'returned' => ['label' => 'Returned', 'class' => 'bg-info text-white'],
                                                ];
                                                $status = $statusMap[$lead->status] ?? ['label' => ucfirst($lead->status), 'class' => 'bg-secondary'];
                                            @endphp
                                            <span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        </td>
                                        <td>{{ $lead->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No forms submitted yet</p>
                                            <a href="{{ route('verifier.create.team', 'peregrine') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="bx bx-plus me-1"></i> Submit Your First Form
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Show/hide custom date inputs based on filter selection
    document.querySelectorAll('input[name="filter"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const customInputs = document.getElementById('customDateInputs');
            const customSubmitBtn = document.getElementById('customSubmitBtn');
            
            if (this.value === 'custom') {
                customInputs.style.display = 'block';
                customSubmitBtn.style.display = 'block';
            } else {
                customInputs.style.display = 'none';
                customSubmitBtn.style.display = 'none';
            }
        });
    });
</script>
@endsection
