@extends('layouts.master')

@section('title')
    Paraguins Leads
@endsection

@section('css')
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 12px;
    }
    .status-pending { background: #ffc107; color: #000; }
    .status-transferred { background: #17a2b8; color: white; }
    .status-sent { background: #28a745; color: white; }
    .status-sale { background: #007bff; color: white; }
    .status-failed { background: #dc3545; color: white; }
    .status-returned { background: #17a2b8; color: white; }
    .modal-header-custom {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #d4af37;
    }
    .modal-dialog-scrollable .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    .modal-xl {
        max-width: 1200px;
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f8f9fa;
    }
    .table td, .table th {
        color: #212529;
    }
    .bg-warning h5 {
        color: #000;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Paraguins @endslot
        @slot('title') My Leads @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Performance Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Leads</h6>
                    <h2 class="mb-0 fw-bold">{{ $allLeads->count() }}</h2>
                    <small>All assigned</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success">
                <div class="card-body text-center">
                    <h6 class="mb-2">Completed</h6>
                    <h2 class="mb-0 fw-bold">{{ $completedLeads->count() }}</h2>
                    <small>Closed & Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info">
                <div class="card-body text-center">
                    <h6 class="mb-2">Conversion</h6>
                    <h2 class="mb-0 fw-bold">
                        @php
                            $total = $allLeads->count();
                            $completed = $completedLeads->count();
                            $conversion = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        {{ $conversion }}%
                    </h2>
                    <small>Success rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body text-center">
                    <h6 class="mb-2">Pending</h6>
                    <h2 class="mb-0 fw-bold">{{ $pendingLeads->count() }}</h2>
                    <small>Awaiting action</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Failed</h6>
                    <h2 class="mb-0 fw-bold">{{ $failedLeads->count() }}</h2>
                    <small>Not completed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Leads -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bx bx-time-five me-2"></i>
                        Pending Leads 
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
                                    <th>Date</th>
                                    <th>Verifier</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingLeads as $lead)
                                    <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#leadModal{{ $lead->id }}">
                                        <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->date ?? 'N/A' }}</td>
                                        <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->status == 'returned')
                                                <span class="status-badge bg-info text-white">Returned</span>
                                            @elseif($lead->pending_reason)
                                                <span class="status-badge status-pending">{{ $lead->pending_reason }}</span>
                                            @else
                                                @php
                                                    $statusMap = [
                                                        'pending' => ['label' => 'Pending', 'class' => 'status-pending'],
                                                        'transferred' => ['label' => 'Pending', 'class' => 'status-pending'],
                                                    ];
                                                    $status = $statusMap[$lead->status] ?? ['label' => 'Pending', 'class' => 'status-pending'];
                                                @endphp
                                                <span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" type="button">
                                                <i class="bx bx-edit"></i> Fill Form
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal for this lead -->
                                    <div class="modal fade" id="leadModal{{ $lead->id }}" tabindex="-1" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header modal-header-custom">
                                                    <h5 class="modal-title">Complete Lead Information - {{ $lead->cn_name }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                                                    <form method="POST" action="{{ route('paraguins.closers.update', $lead->id) }}" id="leadForm{{ $lead->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        @include('paraguins.closers.form', ['lead' => $lead])
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#failModal{{ $lead->id }}">
                                                        <i class="bx bx-x-circle me-1"></i> Mark as Failed
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingModal{{ $lead->id }}">
                                                        <i class="bx bx-time-five me-1"></i> Mark as Pending
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bx bx-x me-1"></i> Cancel
                                                    </button>
                                                    <button type="submit" form="leadForm{{ $lead->id }}" class="btn btn-success">
                                                        <i class="bx bx-send me-1"></i> Submit to Validator
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pending Reason Modal -->
                                    <div class="modal fade" id="pendingModal{{ $lead->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Select Pending Reason</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('paraguins.closers.mark-pending', $lead->id) }}" id="pendingReasonForm{{ $lead->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <p class="mb-3">Why is this lead being marked as pending?</p>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="futurePotential{{ $lead->id }}" value="Pending:Future Potential" required>
                                                            <label class="form-check-label" for="futurePotential{{ $lead->id }}">
                                                                <strong>Pending:Future Potential</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="callback{{ $lead->id }}" value="Pending:Callback" required>
                                                            <label class="form-check-label" for="callback{{ $lead->id }}">
                                                                <strong>Pending:Callback</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingBanking{{ $lead->id }}" value="Pending:Pending Banking" required>
                                                            <label class="form-check-label" for="pendingBanking{{ $lead->id }}">
                                                                <strong>Pending:Pending Banking</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingValidation{{ $lead->id }}" value="Pending:Pending Validation" required>
                                                            <label class="form-check-label" for="pendingValidation{{ $lead->id }}">
                                                                <strong>Pending:Pending Validation</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning">Confirm Pending</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Failure Reason Modal -->
                                    <div class="modal fade" id="failModal{{ $lead->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title text-white">Select Failure Reason</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('paraguins.closers.mark-failed', $lead->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <p class="mb-3">Why is this lead being marked as failed?</p>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="poa{{ $lead->id }}" value="Failed:POA" required>
                                                            <label class="form-check-label" for="poa{{ $lead->id }}">
                                                                <strong>Failed:POA</strong> - Power of Attorney
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqAge{{ $lead->id }}" value="Failed:DNQ-Age" required>
                                                            <label class="form-check-label" for="dnqAge{{ $lead->id }}">
                                                                <strong>Failed:DNQ-Age</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedSSN{{ $lead->id }}" value="Failed:Declined SSN" required>
                                                            <label class="form-check-label" for="declinedSSN{{ $lead->id }}">
                                                                <strong>Failed:Declined SSN</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="notInterested{{ $lead->id }}" value="Failed:Not Interested" required>
                                                            <label class="form-check-label" for="notInterested{{ $lead->id }}">
                                                                <strong>Failed:Not Interested</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="dnc{{ $lead->id }}" value="Failed:DNC" required>
                                                            <label class="form-check-label" for="dnc{{ $lead->id }}">
                                                                <strong>Failed:DNC</strong> - Do Not Call
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="cannotAfford{{ $lead->id }}" value="Failed:Cannot Afford" required>
                                                            <label class="form-check-label" for="cannotAfford{{ $lead->id }}">
                                                                <strong>Failed:Cannot Afford</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqHealth{{ $lead->id }}" value="Failed:DNQ-Health" required>
                                                            <label class="form-check-label" for="dnqHealth{{ $lead->id }}">
                                                                <strong>Failed:DNQ-Health</strong> - Health Conditions
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedBanking{{ $lead->id }}" value="Failed:Declined Banking" required>
                                                            <label class="form-check-label" for="declinedBanking{{ $lead->id }}">
                                                                <strong>Failed:Declined Banking</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="noPitch{{ $lead->id }}" value="Failed:No Pitch (Not Interested)" required>
                                                            <label class="form-check-label" for="noPitch{{ $lead->id }}">
                                                                <strong>Failed:No Pitch (Not Interested)</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="noAnswer{{ $lead->id }}" value="Failed:No Answer" required>
                                                            <label class="form-check-label" for="noAnswer{{ $lead->id }}">
                                                                <strong>Failed:No Answer</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Failed</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <script>
                                    // Copy form data to pending form before submit
                                    document.getElementById('pendingReasonForm{{ $lead->id }}').addEventListener('submit', function(e) {
                                        const mainForm = document.getElementById('leadForm{{ $lead->id }}');
                                        const pendingForm = this;
                                        
                                        // Track which radio groups we've already added
                                        const addedRadios = new Set();
                                        
                                        // Copy all inputs from main form to pending form
                                        mainForm.querySelectorAll('input, select, textarea').forEach(function(input) {
                                            if (input.name && input.name !== '_token' && input.name !== '_method' && input.name !== 'pending_reason') {
                                                // Handle radio buttons specially - only add the checked one per group
                                                if (input.type === 'radio') {
                                                    if (input.checked && !addedRadios.has(input.name)) {
                                                        addedRadios.add(input.name);
                                                        let hidden = document.createElement('input');
                                                        hidden.type = 'hidden';
                                                        hidden.name = input.name;
                                                        hidden.value = input.value;
                                                        pendingForm.appendChild(hidden);
                                                    }
                                                } else {
                                                    // For non-radio inputs
                                                    let hidden = pendingForm.querySelector('input[name="' + input.name + '"]');
                                                    if (!hidden) {
                                                        hidden = document.createElement('input');
                                                        hidden.type = 'hidden';
                                                        hidden.name = input.name;
                                                        pendingForm.appendChild(hidden);
                                                    }
                                                    
                                                    if (input.type === 'checkbox') {
                                                        hidden.value = input.checked ? '1' : '0';
                                                    } else {
                                                        hidden.value = input.value || '';
                                                    }
                                                }
                                            }
                                        });
                                    });
                                    </script>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No pending leads</p>
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

    <!-- Completed/Sent Leads -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-black">
                    <h5 class="mb-0 text-black">
                        <i class="bx bx-check-circle me-2"></i>
                        Completed Leads 
                        <span class="badge bg-dark">{{ $completedLeads->count() }}</span>
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
                                    <th>Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedLeads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'closed' => ['label' => 'Closed', 'class' => 'status-sent'],
                                                    'sale' => ['label' => 'Sale', 'class' => 'status-sale'],
                                                ];
                                                $status = $statusMap[$lead->status] ?? ['label' => 'Closed', 'class' => 'status-sent'];
                                            @endphp
                                            <span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        </td>
                                        <td>{{ $lead->updated_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-1"></i>
                                            <p class="mb-0">No completed leads yet</p>
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

    <!-- Failed Leads -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0 text-white">
                        <i class="bx bx-x-circle me-2"></i>
                        Failed Leads 
                        <span class="badge bg-dark">{{ $failedLeads->count() }}</span>
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
                                    <th>Failure Reason</th>
                                    <th>Failed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failedLeads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge status-failed">
                                                @if($lead->status == 'declined')
                                                    {{ $lead->decline_reason ?? 'Declined' }}
                                                @else
                                                    {{ $lead->failure_reason ?? 'Failed' }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $lead->updated_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-smile fs-1"></i>
                                            <p class="mb-0">No failed leads</p>
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
