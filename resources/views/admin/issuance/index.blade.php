@use('App\Support\Statuses')
@extends('layouts.master')

@section('title')
    Submission & Followup
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Submission
        @endslot
        @slot('title')
            Submission & Followup
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="mdi mdi-check-circle me-2"></i>Submission & Followup
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Submission & Followup List
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('issuance.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, carrier..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="carrier" class="form-select">
                                    <option value="">All Carriers</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}" {{ request('carrier') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="issuance_status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Issued" {{ request('issuance_status') == 'Issued' ? 'selected' : '' }}>Issued</option>
                                    <option value="Incomplete" {{ request('issuance_status') == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="followup_status" class="form-select">
                                    <option value="">Followup Status</option>
                                    <option value="Yes" {{ request('followup_status') == 'Yes' ? 'selected' : '' }}>✅ Yes</option>
                                    <option value="No" {{ request('followup_status') == 'No' ? 'selected' : '' }}>❌ No</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="policy_type" class="form-select">
                                    <option value="">Policy Type</option>
                                    <option value="G.I" {{ request('policy_type') == 'G.I' ? 'selected' : '' }}>G.I</option>
                                    <option value="Graded" {{ request('policy_type') == 'Graded' ? 'selected' : '' }}>Graded</option>
                                    <option value="Level" {{ request('policy_type') == 'Level' ? 'selected' : '' }}>Level</option>
                                    <option value="Modified" {{ request('policy_type') == 'Modified' ? 'selected' : '' }}>Modified</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="month" class="form-select">
                                    <option value="">Month</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="year" class="form-select">
                                    <option value="">Year</option>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
 <th class="u-min-w-150" >Client Name</th>
 <th class="u-min-w-130" >Phone</th>
 <th class="u-min-w-130" >Closer</th>
 <th class="u-min-w-110" >Sale Date</th>
 <th class="u-min-w-120" >Carrier</th>
 <th class="u-min-w-120" >Policy Type</th>
 <th class="u-min-w-150" >Policy Number</th>
 <th class="u-min-w-150" >Assigned Partner</th>
 <th class="u-min-w-120" >Coverage</th>
 <th class="u-min-w-110" >Premium</th>
 <th class="u-min-w-110" >Initial Draft</th>
 <th class="u-min-w-110" >Future Draft</th>
 <th class="u-min-w-120" >Status</th>
 <th class="u-min-w-200" >Reason</th>
 <th class="u-min-w-180" >Assigned Followup</th>
 <th class="u-min-w-140" >Followup Status</th>
 <th class="u-min-w-150" >Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name }}</strong></td>
                                        <td>{{ $lead->phone_number }}</td>
                                        <td>
                                            @if($lead->closer_name)
                                                <span class="badge bg-info">{{ $lead->closer_name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                        <td>{{ $lead->policy_type ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->issued_policy_number)
                                                <span class="badge bg-primary">{{ $lead->issued_policy_number }}</span>
                                            @else
                                                <span class="text-muted">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->partner)
                                                <span class="badge bg-success">{{ $lead->partner->name }} ({{ $lead->partner->code }})</span>
                                            @else
                                                <span class="text-muted">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td class="text-gold fw-semibold">${{ number_format($lead->coverage_amount ?? 0, 2) }}</td>
                                        <td class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                        <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($lead->issuance_status) {
                                                    Statuses::ISSUANCE_ISSUED => 'bg-success',
                                                    'Incomplete' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $lead->issuance_status ?? 'Unverified' }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $lead->issuance_reason ?? '—' }}</small>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm followup-person-dropdown" data-lead-id="{{ $lead->id }}" data-current-person="{{ $lead->assigned_followup_person }}">
                                                <option value="">Select Employee</option>
                                                @foreach($followupUsers as $employee)
                                                    <option value="{{ $employee->id }}" {{ $lead->assigned_followup_person == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            @php
                                                $followupBadge = $lead->followup_status === Statuses::MIS_YES ? 'bg-success' : 'bg-danger';
                                            @endphp
                                            <span class="badge {{ $followupBadge }}">{{ $lead->followup_status === Statuses::MIS_YES ? '✅ Yes' : '❌ No' }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('issuance.show', $lead->id) }}" class="btn btn-sm btn-info" title="View All Details">
                                                    <i class="bx bx-show me-1"></i>View
                                                </a>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $lead->id }}" title="Update Status">
                                                    <i class="bx bx-edit me-1"></i>Update
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Status Update Modal -->
                                    <div class="modal fade" id="statusModal-{{ $lead->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
 <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%)">
                                                    <h5 class="modal-title fw-semibold">
                                                        <i class="mdi mdi-check-circle me-2"></i>Update Status - {{ $lead->cn_name }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form id="issuance-form-{{ $lead->id }}" action="{{ route('issuance.updateIssuanceStatus', $lead->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <!-- Policy Number Field (Mandatory, Edit Once) -->
                                                        <div class="mb-3">
                                                            <label for="policy-number-{{ $lead->id }}" class="form-label fw-semibold">
                                                                Policy Number <span class="text-danger">*</span>
                                                            </label>
                                                            <input 
                                                                type="text" 
                                                                class="form-control" 
                                                                id="policy-number-{{ $lead->id }}" 
                                                                name="issued_policy_number" 
                                                                value="{{ $lead->issued_policy_number }}"
                                                                required
                                                                placeholder="Enter policy number"
                                                            >
                                                        </div>

                                                        <!-- Assigned Partner Field (Mandatory, Edit Once) -->
                                                        <div class="mb-3">
                                                            <label for="partner-{{ $lead->id }}" class="form-label fw-semibold">
                                                                Assigned Partner <span class="text-danger">*</span>
                                                            </label>
                                                            <select 
                                                                class="form-select" 
                                                                id="partner-{{ $lead->id }}" 
                                                                name="partner_id"
                                                                required
                                                            >
                                                                <option value="">Select Partner</option>
                                                                @foreach($partners as $partner)
                                                                    <option value="{{ $partner->id }}" {{ $lead->partner_id == $partner->id ? 'selected' : '' }}>
                                                                        {{ $partner->name }} ({{ $partner->code }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">
                                                                Select Status
                                                            </label>
                                                            <!-- Status buttons -->
                                                            <div class="d-grid gap-2" id="status-buttons-{{ $lead->id }}">
                                                                    <button type="submit" name="issuance_status" value="Issued" class="btn btn-success btn-lg">
                                                                        <i class="mdi mdi-check-circle me-2"></i>Issued
                                                                    </button>
                                                                    <button type="submit" name="issuance_status" value="Incomplete" class="btn btn-warning btn-lg">
                                                                        <i class="mdi mdi-clock-outline me-2"></i>Incomplete
                                                                    </button>
                                                                    <button type="submit" name="issuance_status" value="Pending" class="btn btn-secondary btn-lg pending-unassign-btn" data-lead-id="{{ $lead->id }}">
                                                                        <i class="mdi mdi-pause me-2"></i>Pending (Unassign Partner)
                                                                    </button>
                                                                </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="reason-{{ $lead->id }}" class="form-label fw-semibold">Reason</label>
                                                            <textarea class="form-control" id="reason-{{ $lead->id }}" name="issuance_reason" rows="3" placeholder="Add reason...">{{ $lead->issuance_reason }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No submission data available</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $leads->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Handle followup person dropdown changes
    $('.followup-person-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const followupPersonId = $(this).val();
        const dropdown = $(this);
        
        if (confirm('Are you sure you want to assign this person for followup?')) {
            dropdown.prop('disabled', true);
            
            $.ajax({
                url: `/followup/${leadId}/assign-person`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    assigned_followup_person: followupPersonId
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check me-2"></i>
                                <strong>Success!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Update the current person data attribute
                        dropdown.data('current-person', followupPersonId);
                        dropdown.prop('disabled', false);
                        
                        // Auto-remove alert after 3 seconds
                        setTimeout(() => {
                            $('.alert').fadeOut();
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to assign followup person');
                    dropdown.prop('disabled', false);
                    // Revert to previous selection
                    dropdown.val(dropdown.data('current-person'));
                }
            });
        } else {
            // Revert to previous selection
            dropdown.val(dropdown.data('current-person'));
        }
    });
    
    // Handle unlock field button (Super Admin only)
    $('.unlock-field').click(function(e) {
        e.preventDefault();
        const targetId = $(this).data('target');
        const leadId = targetId.split('-').pop(); // Extract lead ID from target ID
        const unlockBtn = $(this);
        
        // Determine which field to unlock based on target ID
        let fieldToUnlock = '';
        if (targetId.includes('policy-number')) {
            fieldToUnlock = 'policy_number';
        } else if (targetId.includes('partner')) {
            fieldToUnlock = 'partner';
        } else if (targetId.includes('status-buttons')) {
            fieldToUnlock = 'status';
        }
        
        if (!fieldToUnlock) {
            alert('Could not determine field to unlock');
            return;
        }
        
        if (confirm('Are you sure you want to unlock this field? You will be able to edit it.')) {
            unlockBtn.prop('disabled', true);
            
            $.ajax({
                url: `/issuance/${leadId}/unlock-field`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    field: fieldToUnlock
                },
                success: function(response) {
                    if (response.success) {
                        // Show success notification
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check me-2"></i>
                                <strong>Unlocked!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Update UI after successful unlock
                        const targetField = $('#' + targetId);
                        
                        // Check if target is status buttons container
                        if (targetId.includes('status-buttons')) {
                            // Enable all buttons in the container
                            targetField.find('button[type="submit"]').prop('disabled', false);
                        } else {
                            // Remove readonly/disabled attribute for regular fields
                            targetField.prop('readonly', false);
                            targetField.prop('disabled', false);
                        }
                        
                        // Change the button to "Unlocked" state (visual indicator)
                        unlockBtn.removeClass('btn-outline-primary').addClass('btn-outline-success');
                        unlockBtn.html('<i class="mdi mdi-check"></i> Unlocked');
                        unlockBtn.prop('disabled', true);
                        
                        // Update the badge
                        unlockBtn.closest('label').find('.badge').removeClass('bg-warning').addClass('bg-success').html('<i class="mdi mdi-lock-open"></i> Unlocked');
                        
                        // Focus on the field if it's an input/select
                        if (!targetId.includes('status-buttons')) {
                            targetField.focus();
                        }
                        
                        // Auto-remove alert after 3 seconds
                        setTimeout(() => {
                            $('.alert').fadeOut();
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to unlock field');
                    unlockBtn.prop('disabled', false);
                }
            });
        }
    });
    
    // Handle reset Issuance status button (Super Admin only)
    $('.reset-issuance-status').click(function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const button = $(this);
        
        if (confirm('Are you sure you want to reset this Issuance status? All issuance information will be cleared. This action is only available to Super Admin.')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: `/issuance/${leadId}/issuance-status/reset`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-undo me-2"></i>
                                <strong>Reset by Super Admin!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Reload the page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to reset Issuance status');
                    button.prop('disabled', false);
                }
            });
        }
    });
    
    // Handle unassign partner button
    $('.unassign-partner-btn').click(function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const button = $(this);
        
        if (confirm('Are you sure you want to unassign the partner from this lead? The partner will no longer see this lead in their portal.')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: `/leads/${leadId}/unassign-partner`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check me-2"></i>
                                <strong>Success!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Reload the page after 1.5 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to unassign partner');
                    button.prop('disabled', false);
                }
            });
        }
    // Handle pending status with unassign partner
    let pendingStatusClicked = false;
    
    $('.pending-unassign-btn').on('click', function(e) {
        pendingStatusClicked = true;
    });
    
    // Intercept form submission
    $(document).on('submit', 'form', function(e) {
        if (pendingStatusClicked) {
            // Clear the partner field for this form
            $(this).find('select[name="partner_id"]').val('');
            pendingStatusClicked = false;
        }
    });
});
</script>
@endsection

