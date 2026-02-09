@extends('layouts.master')

@section('title', 'Live Analytics Dashboard')

@section('css')
<!-- Flatpickr CSS for Date Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .metric-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .filter-btn.active {
        background-color: #405189 !important;
        color: white !important;
        border-color: #405189 !important;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Live Analytics Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Live Analytics</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range Filters -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary filter-btn {{ $filter === 'today' ? 'active' : '' }}" data-filter="today">
                                    <i class="bx bx-calendar-alt me-1"></i> Today
                                </button>
                                <button type="button" class="btn btn-outline-primary filter-btn {{ $filter === 'custom' ? 'active' : '' }}" data-filter="custom" id="customRangeBtn">
                                    <i class="bx bx-calendar-edit me-1"></i> Custom Range
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="customDateRange" class="form-control" placeholder="Select date range" style="display: none; width: 250px;">
                                <button type="button" class="btn btn-success" id="refreshBtn">
                                    <i class="bx bx-refresh"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bx bx-time-five me-1"></i>
                                Last Updated: <strong id="last-updated">{{ now('America/Denver')->format('M d, Y h:i:s A') }} MT</strong>
                                <span class="ms-3"><i class="bx bx-revision text-success"></i> Auto-refreshing every 30 seconds</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validator Performance Table -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-clipboard-check text-info me-2"></i>Validator Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Validator Name</th>
                                        <th class="text-center">Total Assigned<br><small class="text-muted">(All Leads)</small></th>
                                        <th class="text-center">Pending<br><small class="text-muted">(Awaiting Review)</small></th>
                                        <th class="text-center">Submitted<br><small class="text-muted">(To Sales Mgmt)</small></th>
                                        <th class="text-center">Approved<br><small class="text-muted">(Sales)</small></th>
                                        <th class="text-center">Returned<br><small class="text-muted">(To Closer)</small></th>
                                        <th class="text-center">Declined</th>
                                        <th class="text-center">Processed<br><small class="text-muted">(Completed)</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($validatorBreakdown as $validator)
                                    <tr>
                                        <td><strong>{{ $validator['name'] }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info fs-6 metric-card" style="cursor: pointer;" data-drill="validator_total_assigned">
                                                {{ $validator['total_assigned'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary fs-6">
                                                {{ $validator['pending'] ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning-subtle text-warning fs-6">
                                                {{ $validator['submitted'] ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success fs-6 metric-card" style="cursor: pointer;" data-drill="validator_approved">
                                                {{ $validator['approved'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning-subtle text-warning fs-6 metric-card" style="cursor: pointer;" data-drill="validator_returned">
                                                {{ $validator['returned'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger-subtle text-danger fs-6 metric-card" style="cursor: pointer;" data-drill="validator_declined">
                                                {{ $validator['declined'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary fs-6">
                                                {{ $validator['total_processed'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="bx bx-info-circle me-1"></i> No validators found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center">
                                            <span class="badge bg-info fs-6">{{ $validatorFormMetrics['total_assigned'] }}</span>
                                        </th>
                                        <th class="text-center">
                                            <span class="badge bg-secondary fs-6">{{ $validatorBreakdown->sum('pending') ?? 0 }}</span>
                                        </th>
                                        <th class="text-center">
                                            <span class="badge bg-warning fs-6">{{ $validatorBreakdown->sum('submitted') ?? 0 }}</span>
                                        </th>
                                        <th class="text-center">
                                            <span class="badge bg-success fs-6">{{ $validatorFormMetrics['approved'] }}</span>
                                        </th>
                                        <th class="text-center">
                                            <span class="badge bg-warning fs-6">{{ $validatorFormMetrics['returned'] }}</span>
                                        </th>
                                        <th class="text-center">
                                            <span class="badge bg-danger fs-6">{{ $validatorFormMetrics['declined'] }}</span>
                                        </th>
                                        <th class="text-center">
                                            <span class="badge bg-primary fs-6">{{ $validatorFormMetrics['approved'] + $validatorFormMetrics['returned'] + $validatorFormMetrics['declined'] }}</span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Validator Performance (Hidden) -->
        <div class="row mt-2" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-user-check me-2"></i>Individual Validator Performance
                        </h5>
                    </div>
                    <div class="card-body" style="display: none;">
                        <!-- Hidden duplicate section -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Peregrine Closer Stats Section -->
        <div class="row mt-3">
            <div class="col-12">
                <h5 class="mb-3"><i class="bx bx-user-circle text-success"></i> Peregrine Closer Performance</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Closer Name</th>
                                        <th class="text-center">Total Assigned<br><small class="text-muted">(All Leads)</small></th>
                                        <th class="text-center">Pending<br><small class="text-muted">(In Progress)</small></th>
                                        <th class="text-center">Closed<br><small class="text-muted">(To Validator)</small></th>
                                        <th class="text-center">Sales<br><small class="text-muted">(Approved)</small></th>
                                        <th class="text-center">Returned<br><small class="text-muted">(Sent Back)</small></th>
                                        <th class="text-center">Declined<br><small class="text-muted">(Rejected)</small></th>
                                        <th class="text-center">Total Processed</th>
                                        <th class="text-center">Conversion Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($peregrineCloserBreakdown && $peregrineCloserBreakdown->count() > 0)
                                        @php
                                            $totals = [
                                                'total_assigned' => $peregrineCloserBreakdown->sum('total_assigned'),
                                                'pending' => $peregrineCloserBreakdown->sum('pending'),
                                                'closed' => $peregrineCloserBreakdown->sum('closed'),
                                                'sales' => $peregrineCloserBreakdown->sum('sales'),
                                                'returned' => $peregrineCloserBreakdown->sum('returned'),
                                                'declined' => $peregrineCloserBreakdown->sum('declined'),
                                                'total_processed' => $peregrineCloserBreakdown->sum('total_processed'),
                                            ];
                                            $avgConversionRate = $totals['total_processed'] > 0 ? round(($totals['sales'] / $totals['total_processed']) * 100, 1) : 0;
                                        @endphp
                                        @foreach($peregrineCloserBreakdown as $closer)
                                            <tr>
                                                <td><strong>{{ $closer['name'] }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary-subtle text-primary fs-6">{{ $closer['total_assigned'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning-subtle text-warning fs-6">{{ $closer['pending'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info-subtle text-info fs-6">{{ $closer['closed'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success fs-6">{{ $closer['sales'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary-subtle text-secondary fs-6">{{ $closer['returned'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger-subtle text-danger fs-6">{{ $closer['declined'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-dark-subtle text-dark fs-6">{{ $closer['total_processed'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $closer['conversion_rate'] >= 50 ? 'success' : ($closer['conversion_rate'] >= 30 ? 'warning' : 'danger') }} fs-6">
                                                        {{ $closer['conversion_rate'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $totals['total_assigned'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning fs-6">{{ $totals['pending'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-6">{{ $totals['closed'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-6">{{ $totals['sales'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary fs-6">{{ $totals['returned'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger fs-6">{{ $totals['declined'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-dark fs-6">{{ $totals['total_processed'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $avgConversionRate >= 50 ? 'success' : ($avgConversionRate >= 30 ? 'warning' : 'danger') }} fs-6">
                                                    {{ $avgConversionRate }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="bx bx-info-circle"></i> No Peregrine closers found
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verifier Metrics Section -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="bx bx-check-shield text-primary"></i> Verifier Forms</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>VERIFIER NAME</th>
                                        <th class="text-center">TOTAL SUBMITTED<br><small class="text-muted">(ALL LEADS)</small></th>
                                        <th class="text-center">SALE<br><small class="text-muted">(APPROVED)</small></th>
                                        <th class="text-center">PENDING CALLBACKS</th>
                                        <th class="text-center">DECLINED CALLS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($verifierBreakdown && $verifierBreakdown->count() > 0)
                                        @php
                                            $totals = [
                                                'total_submitted' => $verifierBreakdown->sum('total_submitted'),
                                                'transferred' => $verifierBreakdown->sum('transferred'),
                                                'pending_callbacks' => $verifierBreakdown->sum('pending_callbacks'),
                                                'declined_calls' => $verifierBreakdown->sum('declined_calls'),
                                                'marked_as_sale' => $verifierBreakdown->sum('marked_as_sale'),
                                            ];
                                        @endphp
                                        @foreach($verifierBreakdown as $verifier)
                                            <tr>
                                                <td><strong>{{ $verifier['name'] }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary-subtle text-primary fs-6">{{ $verifier['total_submitted'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success fs-6">{{ $verifier['marked_as_sale'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning-subtle text-warning fs-6">{{ $verifier['pending_callbacks'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger-subtle text-danger fs-6">{{ $verifier['declined_calls'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $totals['total_submitted'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-6">{{ $totals['marked_as_sale'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning fs-6">{{ $totals['pending_callbacks'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger fs-6">{{ $totals['declined_calls'] }}</span>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bx bx-info-circle"></i> No verifiers found
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QA Performance Table -->
        <div class="row mt-3">
            <div class="col-12">
                <h5 class="mb-3"><i class="bx bx-shield-quarter text-success"></i> Quality Assurance</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>QA REVIEWER NAME</th>
                                        <th class="text-center">TOTAL SALES<br><small class="text-muted">(REVIEWED)</small></th>
                                        <th class="text-center">PENDING<br><small class="text-muted">(AWAITING)</small></th>
                                        <th class="text-center">GOOD<br><small class="text-muted">(PASSED)</small></th>
                                        <th class="text-center">ISSUES<br><small class="text-muted">(AVG + BAD)</small></th>
                                        <th class="text-center">TOTAL REVIEWED<br><small class="text-muted">(COMPLETED)</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($qaBreakdown && $qaBreakdown->count() > 0)
                                        @php
                                            $totals = [
                                                'total_sales' => $qaBreakdown->sum('total_sales'),
                                                'pending' => $qaBreakdown->sum('pending'),
                                                'good' => $qaBreakdown->sum('good'),
                                                'issues' => $qaBreakdown->sum('issues'),
                                                'total_reviewed' => $qaBreakdown->sum('total_reviewed'),
                                            ];
                                        @endphp
                                        @foreach($qaBreakdown as $qa)
                                            <tr>
                                                <td><strong>{{ $qa['name'] }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary-subtle text-primary fs-6">{{ $qa['total_sales'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning-subtle text-warning fs-6">{{ $qa['pending'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success fs-6">{{ $qa['good'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger-subtle text-danger fs-6">{{ $qa['issues'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info-subtle text-info fs-6">{{ $qa['total_reviewed'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $totals['total_sales'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning fs-6">{{ $totals['pending'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-6">{{ $totals['good'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger fs-6">{{ $totals['issues'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-6">{{ $totals['total_reviewed'] }}</span>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="bx bx-info-circle"></i> No QA reviewers found
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Drill-Down Modal -->
<div class="modal fade" id="drillDownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="drillDownTitle">Detail View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="drillDownContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    let currentFilter = '{{ $filter }}';
    let customDateRange = null;

    // Initialize Flatpickr for custom date range
    const dateRangePicker = flatpickr("#customDateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                currentFilter = 'custom';
                updateFilters();
            }
        }
    });

    // Initialize custom date range if filter is 'custom'
    @if($filter === 'custom' && $startDate && $endDate)
        dateRangePicker.setDate(['{{ $startDate }}', '{{ $endDate }}']);
        document.getElementById('customDateRange').style.display = 'block';
    @endif

    // Filter button handlers
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            currentFilter = filter;
            
            if (filter === 'custom') {
                document.getElementById('customDateRange').style.display = 'block';
                dateRangePicker.open();
            } else {
                document.getElementById('customDateRange').style.display = 'none';
                updateFilters();
            }
        });
    });

    // Refresh button handler
    document.getElementById('refreshBtn').addEventListener('click', function() {
        updateFilters();
    });

    // Update dashboard with new filters
    function updateFilters() {
        const params = new URLSearchParams();
        params.append('filter', currentFilter);
        
        if (currentFilter === 'custom' && dateRangePicker.selectedDates.length === 2) {
            const startDate = dateRangePicker.selectedDates[0].toISOString().split('T')[0];
            const endDate = dateRangePicker.selectedDates[1].toISOString().split('T')[0];
            params.append('start_date', startDate);
            params.append('end_date', endDate);
            console.log('Custom filter dates:', startDate, endDate);
        } else if (currentFilter === 'custom') {
            console.warn('Custom filter selected but no dates chosen');
            alert('Please select a date range');
            return;
        }
        
        console.log('Updating filters with params:', params.toString());
        window.location.href = '{{ route('analytics.live') }}?' + params.toString();
    }

    // Drill-down handlers - wrapped in DOMContentLoaded to ensure DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.metric-card[data-drill]').forEach(card => {
            card.addEventListener('click', function() {
                const drillType = this.getAttribute('data-drill');
                console.log('Drill-down clicked:', drillType);
                showDrillDown(drillType);
            });
        });

        // Validator detail buttons
        document.querySelectorAll('.validator-detail-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const validatorId = this.getAttribute('data-validator-id');
                const validatorName = this.getAttribute('data-validator-name');
                showValidatorDetail(validatorId, validatorName);
            });
        });
    });

    function showDrillDown(type) {
        const modal = new bootstrap.Modal(document.getElementById('drillDownModal'));
        const params = new URLSearchParams();
        params.append('type', type);
        params.append('filter', currentFilter);
        
        if (currentFilter === 'custom' && dateRangePicker.selectedDates.length === 2) {
            const startDate = dateRangePicker.selectedDates[0].toISOString().split('T')[0];
            const endDate = dateRangePicker.selectedDates[1].toISOString().split('T')[0];
            params.append('start_date', startDate);
            params.append('end_date', endDate);
        }
        
        const titles = {
            'verifier_submitted': 'Verifier Submissions - Details',
            'verifier_pending': 'Pending Validations - Details',
            'qa_pending': 'QA Pending - Details',
            'qa_good': 'QA Good Reviews - Details',
            'qa_bad': 'QA Bad Reviews - Details',
            'validator_pending': 'Validator Pending - Details',
            'validator_total_assigned': 'Validator Total Assigned - Details',
            'validator_approved': 'Validator Approved - Details',
            'validator_returned': 'Validator Returned - Details',
            'validator_declined': 'Validator Declined - Details',
            'validator_submitted': 'Validator Submissions - Details',
            'sales_range': 'Sales - Details',
            'manager_pending': 'Manager Pending Approvals - Details',
            'manager_approved': 'Manager Approved - Details'
        };
        document.getElementById('drillDownTitle').textContent = titles[type] || 'Detail View';
        
        document.getElementById('drillDownContent').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        fetch('{{ route('analytics.drill-down') }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderDrillDownTable(data);
            })
            .catch(error => {
                document.getElementById('drillDownContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bx bx-error me-2"></i>Failed to load data. Please try again.
                    </div>
                `;
            });
    }

    function renderDrillDownTable(data) {
        if (data.count === 0) {
            document.getElementById('drillDownContent').innerHTML = `
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-2"></i>No records found for the selected period.
                </div>
            `;
            return;
        }
        
        // Check if this is validator summary data (has validator_name field)
        const isValidatorSummary = data.data.length > 0 && data.data[0].validator_name !== undefined;
        
        if (isValidatorSummary) {
            // Render validator summary table
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Validator Name</th>
                                <th>Lead Count</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.data.forEach(item => {
                html += `<tr>
                    <td><strong>${item.validator_name}</strong></td>
                    <td><span class="badge bg-primary fs-6">${item.lead_count}</span></td>
                </tr>`;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    <small class="text-muted">Total: ${data.count} validators</small>
                </div>
            `;
            
            document.getElementById('drillDownContent').innerHTML = html;
            return;
        }
        
        // Render regular lead detail table
        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.data.forEach(item => {
            html += `<tr>
                <td>${item.id}</td>
                <td>${item.cn_name || ''}</td>
                <td>${item.phone_number || 'N/A'}</td>
                <td>${item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'}</td>
                <td>
                    ${item.qa_status ? `<span class="badge bg-${item.qa_status === 'Good' ? 'success' : item.qa_status === 'Bad' ? 'danger' : 'warning'}">${item.qa_status}</span>` : ''}
                    ${item.manager_status ? `<span class="badge bg-${item.manager_status === 'approved' ? 'success' : item.manager_status === 'declined' ? 'danger' : 'warning'}">${item.manager_status}</span>` : ''}
                </td>
            </tr>`;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                <small class="text-muted">Showing ${data.count} records (limited to 50)</small>
            </div>
        `;
        
        document.getElementById('drillDownContent').innerHTML = html;
    }

    // Show validator detail breakdown
    function showValidatorDetail(validatorId, validatorName) {
        const modal = new bootstrap.Modal(document.getElementById('drillDownModal'));
        const params = new URLSearchParams();
        params.append('type', 'validator_breakdown');
        params.append('validator_id', validatorId);
        params.append('filter', currentFilter);
        
        if (currentFilter === 'custom' && dateRangePicker.selectedDates.length === 2) {
            const startDate = dateRangePicker.selectedDates[0].toISOString().split('T')[0];
            const endDate = dateRangePicker.selectedDates[1].toISOString().split('T')[0];
            params.append('start_date', startDate);
            params.append('end_date', endDate);
        }
        
        document.getElementById('drillDownTitle').textContent = `Validator Details: ${validatorName}`;
        
        document.getElementById('drillDownContent').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        fetch('{{ route('analytics.drill-down') }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderValidatorDetailTable(data, validatorName);
            })
            .catch(error => {
                document.getElementById('drillDownContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bx bx-error me-2"></i>Failed to load validator data. Please try again.
                    </div>
                `;
            });
    }

    // Render validator detail table
    function renderValidatorDetailTable(data, validatorName) {
        if (data.count === 0) {
            document.getElementById('drillDownContent').innerHTML = `
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-2"></i>No leads found for ${validatorName} in the selected period.
                </div>
            `;
            return;
        }
        
        let html = `
            <div class="mb-3">
                <h6 class="text-muted">Showing leads for: <strong>${validatorName}</strong></h6>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Assigned Closer</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.data.forEach(item => {
            const statusBadge = {
                'sale': 'success',
                'returned': 'warning',
                'declined': 'danger',
                'closed': 'info',
                'pending': 'secondary'
            };
            const badgeClass = statusBadge[item.status] || 'secondary';
            
            html += `<tr>
                <td>${item.id}</td>
                <td>${item.first_name || ''} ${item.last_name || ''}</td>
                <td>${item.phone_number || 'N/A'}</td>
                <td><span class="badge bg-${badgeClass}">${item.status || 'N/A'}</span></td>
                <td>${item.assigned_closer ? item.assigned_closer.name : 'N/A'}</td>
                <td>${item.updated_at ? new Date(item.updated_at).toLocaleString() : 'N/A'}</td>
            </tr>`;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                <small class="text-muted">Showing ${data.count} records (limited to 100)</small>
            </div>
        `;
        
        document.getElementById('drillDownContent').innerHTML = html;
    }
</script>
@endsection
