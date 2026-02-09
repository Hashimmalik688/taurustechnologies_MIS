@extends('layouts.partner')

@section('title') Partner Dashboard @endsection

@section('css')
<style>
    body {
        background: #f8f9fa;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        border-left: 4px solid;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card.blue { border-left-color: #667eea; }
    .stat-card.green { border-left-color: #38ef7d; }
    .stat-card.orange { border-left-color: #f5576c; }
    .stat-card.purple { border-left-color: #00f2fe; }
    .stat-card.gold { border-left-color: #f5af19; }
    .stat-card.indigo { border-left-color: #764ba2; }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 10px 0;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .table-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 15px;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    .table tbody tr {
        border-bottom: 1px solid #f8f9fa;
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .carrier-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .carrier-badge .carrier-name {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .state-pill {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.75rem;
        margin: 2px;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #212529;
    }
</style>
@endsection

@section('content')

<!-- Month Filter Row -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Partner Dashboard</h4>
            <div class="d-flex align-items-center gap-3">
                <label for="month-filter" class="form-label mb-0">Filter by Month:</label>
                <input type="month" id="month-filter" class="form-control" value="{{ $month }}" 
                       onchange="window.location.href='{{ route('partner.dashboard') }}?month=' + this.value">
            </div>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card blue">
            <div class="stat-label">Total Leads</div>
            <div class="stat-value">{{ number_format($totalLeads) }}</div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card green">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value">{{ number_format($totalSales) }}</div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card orange">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($pendingLeads) }}</div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card purple">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">${{ number_format($totalRevenue, 0) }}</div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card gold">
            <div class="stat-label">Partner Commission</div>
            <div class="stat-value">${{ number_format($partnerCommission, 0) }}</div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card indigo">
            <div class="stat-label">Taurus Share</div>
            <div class="stat-value">${{ number_format($taurusShareDollars, 0) }}</div>
        </div>
    </div>
</div>

<!-- Recent Leads Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Leads ({{ $month }})</h5>
                    <span class="badge bg-primary">{{ $recentLeads->count() }} Records</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client Name</th>
                            <th>Carrier</th>
                            <th>State</th>
                            <th>Premium</th>
                            <th>Coverage</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeads as $lead)
                            <tr>
                                <td><strong>#{{ $lead->id }}</strong></td>
                                <td>{{ $lead->cn_name ?? 'N/A' }}</td>
                                <td>
                                    @if($lead->insuranceCarrier)
                                        <span class="badge bg-secondary">{{ $lead->insuranceCarrier->name }}</span>
                                    @else
                                        {{ $lead->carrier_name ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>{{ $lead->state ?? 'N/A' }}</td>
                                <td><strong>${{ number_format($lead->monthly_premium ?? $lead->premium_amount ?? $lead->issued_premium ?? 0, 2) }}</strong></td>
                                <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                                <td>
                                    @if($lead->agent_commission)
                                        <span class="text-success"><strong>${{ number_format($lead->agent_commission, 2) }}</strong></span>
                                    @else
                                        <span class="text-muted">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lead->status === 'sale')
                                        <span class="badge-status bg-success">Sale</span>
                                    @elseif($lead->status === 'pending' || $lead->status === 'Pending')
                                        <span class="badge-status bg-warning text-dark">Pending</span>
                                    @elseif($lead->status === 'declined')
                                        <span class="badge-status bg-danger">Declined</span>
                                    @else
                                        <span class="badge-status bg-secondary">{{ ucfirst($lead->status ?? 'N/A') }}</span>
                                    @endif
                                </td>
                                <td>{{ $lead->created_at ? $lead->created_at->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No leads found for {{ $month }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Carriers & States -->
<div class="row mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Your Assigned Carriers & States</h5>
            </div>
            <div class="p-3">
                @if($carrierStates->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No carriers assigned yet. Please contact your administrator.
                    </div>
                @else
                    @foreach($carrierStates as $carrierId => $carrierData)
                        <div class="carrier-badge w-100">
                            <div class="row w-100">
                                <div class="col-md-6">
                                    <div class="carrier-name mb-2">
                                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                                        {{ $carrierData['carrier']->name }}
                                    </div>
                                    <div>
                                        <strong>{{ $carrierData['state_count'] }} States:</strong>
                                        @foreach($carrierData['states'] as $state)
                                            <span class="state-pill">{{ $state }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="mb-2"><strong>Settlement Rates:</strong></div>
                                    @if($carrierData['settlement_level_pct'])
                                        <span class="badge bg-success me-1">Level: {{ $carrierData['settlement_level_pct'] }}%</span>
                                    @endif
                                    @if($carrierData['settlement_graded_pct'])
                                        <span class="badge bg-info me-1">Graded: {{ $carrierData['settlement_graded_pct'] }}%</span>
                                    @endif
                                    @if($carrierData['settlement_gi_pct'])
                                        <span class="badge bg-warning me-1">GI: {{ $carrierData['settlement_gi_pct'] }}%</span>
                                    @endif
                                    @if($carrierData['settlement_modified_pct'])
                                        <span class="badge bg-secondary me-1">Modified: {{ $carrierData['settlement_modified_pct'] }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>



@endsection
