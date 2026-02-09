@extends('layouts.master')

@section('title')
    Validator Dashboard
@endsection

@section('css')
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 12px;
    }
    .status-pending { background: #ffc107; color: #000; }
    .status-closed { background: #17a2b8; color: white; }
    .status-sale { background: #28a745; color: white; }
    .status-failed { background: #dc3545; color: white; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Validator @endslot
        @slot('title') Validation Dashboard @endslot
    @endcomponent

    <!-- Date Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('validator.index') }}" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date Range</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="filter" id="filter_today" value="today" {{ $filter === 'today' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_today">Today</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_yesterday" value="yesterday" {{ $filter === 'yesterday' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_yesterday">Yesterday</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_week" value="week" {{ $filter === 'week' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_week">This Week</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_custom" value="custom" {{ $filter === 'custom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="filter_custom">Custom Range</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="customDateInputs" style="display: {{ $filter === 'custom' ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" id="customSubmitBtn" style="display: {{ $filter === 'custom' ? 'block' : 'none' }};">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="showAllPending" name="show_all_pending" value="1" {{ request('show_all_pending') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="form-check-label" for="showAllPending">
                                        <strong>Show all pending validation</strong> (ignore date filter for pending)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Today's Activity KPI Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="mdi mdi-chart-line"></i> 
                @if($filter === 'today')
                    Today's Activity
                @elseif($filter === 'yesterday')
                    Yesterday's Activity
                @elseif($filter === 'week')
                    This Week's Activity
                @else
                    Selected Period Activity
                @endif
                <small class="text-muted">({{ \Carbon\Carbon::parse($startDate)->timezone('America/Denver')->format('M d, Y g:i A') }} - {{ \Carbon\Carbon::parse($endDate)->timezone('America/Denver')->format('M d, Y g:i A') }} MT)</small>
            </h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="mdi mdi-clipboard-check text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['total_processed'] ?? 0 }}</h3>
                    <small class="text-muted">Processed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="mdi mdi-send text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['submitted'] ?? 0 }}</h3>
                    <small class="text-muted">Submitted to You</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['sales'] ?? 0 }}</h3>
                    <small class="text-muted">Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="mdi mdi-arrow-u-left-top text-info" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['returned'] ?? 0 }}</h3>
                    <small class="text-muted">Returned</small>
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
        <div class="col-md-2">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="mdi mdi-clock-alert text-secondary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2">{{ $todayStats['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending</small>
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
        <div class="col-md-2">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Assigned</h6>
                    <h2 class="mb-0 fw-bold">{{ $filteredTotal ?? 0 }}</h2>
                    <small>In selected period</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning">
                <div class="card-body text-center">
                    <h6 class="mb-2">Submitted to You</h6>
                    <h2 class="mb-0 fw-bold">{{ $submittedLeads->count() }}</h2>
                    <small>By closers</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success">
                <div class="card-body text-center">
                    <h6 class="mb-2">Sales</h6>
                    <h2 class="mb-0 fw-bold">{{ $salesLeads->count() }}</h2>
                    <small>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info">
                <div class="card-body text-center">
                    <h6 class="mb-2">Returned</h6>
                    <h2 class="mb-0 fw-bold">{{ $returnedLeads->count() }}</h2>
                    <small>Back to closer</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Declined</h6>
                    <h2 class="mb-0 fw-bold">{{ $declinedLeads->count() }}</h2>
                    <small>Declined</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Validation Leads -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bx bx-check-circle me-2"></i>
                        Pending Validation 
                        <span class="badge bg-dark">{{ $pendingLeads->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Verifier</th>
                                    <th>Closer</th>
                                    <th>Assigned Partner</th>
                                    <th>Coverage</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingLeads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                                        <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->assigned_partner)
                                                <span class="badge bg-primary">{{ $lead->assigned_partner }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                                        <td>{{ $lead->updated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $lead->id }}">
                                                <i class="bx bx-edit"></i> Edit
                                            </button>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal{{ $lead->id }}" tabindex="-1" data-bs-backdrop="static">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info">
                                                            <h5 class="modal-title">Validate Lead - {{ $lead->cn_name }}</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('validator.update', $lead->id) }}" id="validatorForm{{ $lead->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                                                                @include('peregrine.closers.form', ['lead' => $lead, 'isValidator' => true])
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="bx bx-check me-1"></i> Mark as Sale
                                                                </button>
                                                                <button type="button" class="btn btn-warning" onclick="document.getElementById('forwardForm{{ $lead->id }}').submit(); return false;">
                                                                    <i class="bx bx-send me-1"></i> Sent to Home Office
                                                                </button>
                                                                <button type="button" class="btn btn-danger" onclick="document.getElementById('declineForm{{ $lead->id }}').submit(); return false;">
                                                                    <i class="bx bx-x me-1"></i> Declined
                                                                </button>
                                                                <button type="button" class="btn btn-secondary" onclick="returnToCloser{{ $lead->id }}()">
                                                                    <i class="bx bx-arrow-back me-1"></i> Return to Closer
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                    <i class="bx bx-x me-1"></i> Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                        
                                                        <!-- Hidden forms for other actions -->
                                                        <form method="POST" action="{{ route('validator.mark-forwarded', $lead->id) }}" id="forwardForm{{ $lead->id }}" style="display:none;">
                                                            @csrf
                                                            @method('PUT')
                                                        </form>
                                                        <form method="POST" action="{{ route('validator.mark-simple-declined', $lead->id) }}" id="declineForm{{ $lead->id }}" style="display:none;">
                                                            @csrf
                                                            @method('PUT')
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Decline Reason Modal -->
                                            <div class="modal fade" id="declineModal{{ $lead->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h5 class="modal-title">Select Decline Reason</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('validator.mark-failed', $lead->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <p class="mb-3">Why is this lead being declined?</p>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="poa{{ $lead->id }}" value="Declined:POA" required>
                                                                    <label class="form-check-label" for="poa{{ $lead->id }}">
                                                                        <strong>Declined:POA</strong> - Power of Attorney
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="dnqAge{{ $lead->id }}" value="Declined:DNQ-Age" required>
                                                                    <label class="form-check-label" for="dnqAge{{ $lead->id }}">
                                                                        <strong>Declined:DNQ-Age</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="declinedSSN{{ $lead->id }}" value="Declined:Declined SSN" required>
                                                                    <label class="form-check-label" for="declinedSSN{{ $lead->id }}">
                                                                        <strong>Declined:Declined SSN</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="notInterested{{ $lead->id }}" value="Declined:Not Interested" required>
                                                                    <label class="form-check-label" for="notInterested{{ $lead->id }}">
                                                                        <strong>Declined:Not Interested</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="dnc{{ $lead->id }}" value="Declined:DNC" required>
                                                                    <label class="form-check-label" for="dnc{{ $lead->id }}">
                                                                        <strong>Declined:DNC</strong> - Do Not Call
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="cannotAfford{{ $lead->id }}" value="Declined:Cannot Afford" required>
                                                                    <label class="form-check-label" for="cannotAfford{{ $lead->id }}">
                                                                        <strong>Declined:Cannot Afford</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="dnqHealth{{ $lead->id }}" value="Declined:DNQ-Health" required>
                                                                    <label class="form-check-label" for="dnqHealth{{ $lead->id }}">
                                                                        <strong>Declined:DNQ-Health</strong> - Health Conditions
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="declinedBanking{{ $lead->id }}" value="Declined:Declined Banking" required>
                                                                    <label class="form-check-label" for="declinedBanking{{ $lead->id }}">
                                                                        <strong>Declined:Declined Banking</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="noPitch{{ $lead->id }}" value="Declined:No Pitch (Not Interested)" required>
                                                                    <label class="form-check-label" for="noPitch{{ $lead->id }}">
                                                                        <strong>Declined:No Pitch (Not Interested)</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="noAnswer{{ $lead->id }}" value="Declined:No Answer" required>
                                                                    <label class="form-check-label" for="noAnswer{{ $lead->id }}">
                                                                        <strong>Declined:No Answer</strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Confirm Declined</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <script>
                                            function returnToCloser{{ $lead->id }}() {
                                                if(confirm('Return this lead to closer for more information?')) {
                                                    const form = document.getElementById('validatorForm{{ $lead->id }}');
                                                    form.action = '{{ route('validator.return-to-closer', $lead->id) }}';
                                                    form.submit();
                                                }
                                            }
                                            </script>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No pending leads for validation</p>
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

    <!-- Home Office Leads (Secure View) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="mb-0">
                        <i class="bx bx-send me-2"></i>
                        Sent to Home Office 
                        <span class="badge bg-dark">{{ $homeOfficeLeads->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference ID</th>
                                    <th>Customer Name</th>
                                    <th>Closer Name</th>
                                    <th>Assigned Partner</th>
                                    <th>Verifier</th>
                                    <th>Coverage Amount</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($homeOfficeLeads as $lead)
                                    <tr>
                                        <td><strong>#{{ $lead->id }}</strong></td>
                                        <td>{{ $lead->cn_name }}</td>
                                        <td>{{ $lead->assignedCloser->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->assigned_partner)
                                                <span class="badge bg-primary">{{ $lead->assigned_partner }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->verifier->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                                        <td>{{ $lead->updated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <!-- Mark as Sale Form -->
                                            <form method="POST" action="{{ route('validator.mark-home-office-sale', $lead->id) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this lead as Sale?')">
                                                    <i class="bx bx-check me-1"></i> Mark as Sale
                                                </button>
                                            </form>
                                            
                                            <!-- Mark as Declined Form -->
                                            <form method="POST" action="{{ route('validator.mark-simple-declined', $lead->id) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Mark this lead as Declined?')">
                                                    <i class="bx bx-x me-1"></i> Mark as Declined
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No leads sent to home office</p>
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

    <!-- Completed Validations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-black">
                    <h5 class="mb-0 text-black">
                        <i class="bx bx-check-circle me-2"></i>
                        Completed Validations 
                        <span class="badge bg-dark">{{ $completedLeads->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Closer Name</th>
                                    <th>Assigned Partner</th>
                                    <th>Verifier</th>
                                    <th>Status</th>
                                    <th>Validated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedLeads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->assigned_partner)
                                                <span class="badge bg-primary">{{ $lead->assigned_partner }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->status == 'sale')
                                                <span class="status-badge status-sale">Sale</span>
                                            @elseif($lead->status == 'forwarded')
                                                <span class="status-badge status-sale">Forwarded</span>
                                            @else
                                                <span class="status-badge status-failed">{{ $lead->failure_reason ?? 'Failed' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->validator ? $lead->validator->name : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-1"></i>
                                            <p class="mb-0">No completed validations yet</p>
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
    document.addEventListener('DOMContentLoaded', function() {
        const filterRadios = document.querySelectorAll('input[name="filter"]');
        const customDateInputs = document.getElementById('customDateInputs');
        const customSubmitBtn = document.getElementById('customSubmitBtn');

        filterRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateInputs.style.display = 'flex';
                    customSubmitBtn.style.display = 'inline-block';
                } else {
                    customDateInputs.style.display = 'none';
                    customSubmitBtn.style.display = 'none';
                    if (this.value !== 'custom') {
                        this.form.submit();
                    }
                }
            });
        });
    });
</script>
@endsection
