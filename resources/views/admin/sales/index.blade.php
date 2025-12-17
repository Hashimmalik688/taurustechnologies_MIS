@extends('layouts.master')

@section('title')
    Sales Management
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Sales
        @endslot
        @slot('title')
            Management
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
                <i class="mdi mdi-briefcase-outline me-2"></i>Sales Management
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Sales List
                    </h5>
                    @if(!auth()->user()->hasRole('QA'))
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importOldDataModal">
                            <i class="bx bx-upload me-1"></i> Import Data
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('sales.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, carrier..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="carrier" class="form-select">
                                    <option value="">All Carriers</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier }}" {{ request('carrier') == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="chargeback" {{ request('status') == 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="policy_type" class="form-select">
                                    <option value="">Policy Type</option>
                                    <option value="Term" {{ request('policy_type') == 'Term' ? 'selected' : '' }}>Term</option>
                                    <option value="Whole Life" {{ request('policy_type') == 'Whole Life' ? 'selected' : '' }}>Whole Life</option>
                                    <option value="Universal" {{ request('policy_type') == 'Universal' ? 'selected' : '' }}>Universal</option>
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
                                    @if(auth()->user()->hasRole('QA'))
                                        {{-- QA View: Limited columns --}}
                                        <th style="min-width:150px;">Client Name</th>
                                        <th style="min-width:130px;">Phone</th>
                                        <th style="min-width:130px;">Closer</th>
                                        <th style="min-width:110px;">Sale Date</th>
                                        <th style="min-width:140px;">QA Status</th>
                                        <th style="min-width:200px;">QA Reason</th>
                                    @else
                                        {{-- Full View for other roles --}}
                                        <th class="text-center" style="min-width:120px;">Actions</th>
                                        <th style="min-width:150px;">Client Name</th>
                                        <th style="min-width:130px;">Phone</th>
                                        <th style="min-width:130px;">Closer</th>
                                        <th style="min-width:110px;">Sale Date</th>
                                        <th style="min-width:120px;">Carrier</th>
                                        <th style="min-width:120px;">Policy Type</th>
                                        <th style="min-width:120px;">Coverage</th>
                                        <th style="min-width:110px;">Premium</th>
                                        <th style="min-width:110px;">Initial Draft</th>
                                        <th style="min-width:110px;">Future Draft</th>
                                        <th style="min-width:140px;">QA Status</th>
                                        <th style="min-width:200px;">QA Reason</th>
                                        <th style="min-width:140px;">Manager Status</th>
                                        <th style="min-width:200px;">Manager Reason</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        @if(auth()->user()->hasRole('QA'))
                                            {{-- QA View: Limited data --}}
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
                                            <td>
                                                <select class="form-select form-select-sm qa-status-dropdown" data-lead-id="{{ $lead->id }}" style="min-width: 130px;">
                                                    <option value="In Review" {{ ($lead->qa_status ?? 'In Review') == 'In Review' ? 'selected' : '' }}>
                                                        🔍 In Review
                                                    </option>
                                                    <option value="Approved" {{ ($lead->qa_status ?? '') == 'Approved' ? 'selected' : '' }}>
                                                        ✅ Approved
                                                    </option>
                                                    <option value="Rejected" {{ ($lead->qa_status ?? '') == 'Rejected' ? 'selected' : '' }}>
                                                        ❌ Rejected
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm qa-reason-input" 
                                                          data-lead-id="{{ $lead->id }}" 
                                                          placeholder="Enter QA reason/comment..." 
                                                          rows="2" 
                                                          style="min-width: 180px;">{{ $lead->qa_reason ?? '' }}</textarea>
                                                <button class="btn btn-sm btn-primary mt-1 save-qa-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                            </td>
                                        @else
                                            {{-- Full View for other roles --}}
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @php
                                                        $zoomNumber = preg_replace('/[^\d\+]/', '', $lead->phone_number);
                                                        $callUrl = 'zoomphonecall://' . urlencode($zoomNumber);
                                                    @endphp
                                                    <button onclick="window.location.href='{{ $callUrl }}'" class="btn btn-outline-warning" title="Call">
                                                        <i class="fas fa-phone-alt"></i>
                                                    </button>
                                                    <a href="{{ route('sales.show', $lead->id) }}" class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sales.edit', $lead->id) }}" class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete-{{ $lead->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
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
                                            <td class="text-gold fw-semibold">${{ number_format($lead->coverage_amount ?? 0, 2) }}</td>
                                            <td class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                            <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <select class="form-select form-select-sm qa-status-dropdown" data-lead-id="{{ $lead->id }}" style="min-width: 130px;">
                                                    <option value="In Review" {{ ($lead->qa_status ?? 'In Review') == 'In Review' ? 'selected' : '' }}>🔍 In Review</option>
                                                    <option value="Approved" {{ ($lead->qa_status ?? '') == 'Approved' ? 'selected' : '' }}>✅ Approved</option>
                                                    <option value="Rejected" {{ ($lead->qa_status ?? '') == 'Rejected' ? 'selected' : '' }}>❌ Rejected</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm qa-reason-input" 
                                                          data-lead-id="{{ $lead->id }}" 
                                                          placeholder="QA comments..." 
                                                          rows="2" 
                                                          style="min-width: 180px;">{{ $lead->qa_reason ?? '' }}</textarea>
                                                <button class="btn btn-sm btn-primary mt-1 save-qa-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm manager-status-dropdown" data-lead-id="{{ $lead->id }}" style="min-width: 130px;">
                                                    <option value="pending" {{ ($lead->manager_status ?? 'pending') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                    <option value="approved" {{ ($lead->manager_status ?? '') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                                    <option value="declined" {{ ($lead->manager_status ?? '') == 'declined' ? 'selected' : '' }}>❌ Declined</option>
                                                    <option value="underwriting" {{ ($lead->manager_status ?? '') == 'underwriting' ? 'selected' : '' }}>📋 Underwriting</option>
                                                    <option value="chargeback" {{ ($lead->manager_status ?? '') == 'chargeback' ? 'selected' : '' }}>💳 Chargeback</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm manager-reason-input" 
                                                          data-lead-id="{{ $lead->id }}" 
                                                          placeholder="Manager comments..." 
                                                          rows="2" 
                                                          style="min-width: 180px;">{{ $lead->manager_reason ?? '' }}</textarea>
                                                <button class="btn btn-sm btn-success mt-1 save-manager-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                            </td>
                                        @endif
                                    </tr>

                                    @if(!auth()->user()->hasRole('QA'))
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="delete-{{ $lead->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-gold">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete <strong>{{ $lead->cn_name }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('sales.delete', $lead->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('QA') ? '6' : '15' }}" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No sales data available</p>
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

    <!-- Import Old Data Modal -->
    <div class="modal fade" id="importOldDataModal" tabindex="-1" aria-labelledby="importOldDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importOldDataModalLabel">
                            <i class="bx bx-upload"></i> Import Old Data from Google Sheets
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Export your Google Sheets data as Excel (.xlsx) or CSV (.csv)</li>
                                <li>Ensure the file includes all necessary columns (Phone Number, Customer Name, etc.)</li>
                                <li>Maximum file size: 2MB</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="import_file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="import_file" name="import_file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Accepted formats: .xlsx, .xls, .csv</div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Handle status dropdown changes for non-QA users
    $('.status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newStatus = $(this).val();
        const dropdown = $(this);
        
        // Send AJAX request to update status
        $.ajax({
            url: `/sales/${leadId}/status`,
            method: 'POST',
            data: {
                status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    dropdown.addClass('border-success');
                    setTimeout(() => {
                        dropdown.removeClass('border-success');
                    }, 2000);
                }
            },
            error: function() {
                alert('Failed to update status');
            }
        });
    });

    // Handle QA status dropdown changes
    $('.qa-status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newQaStatus = $(this).val();
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const dropdown = $(this);
        
        updateQaStatus(leadId, newQaStatus, qaReason, dropdown);
    });

    // Handle QA reason save button
    $('.save-qa-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const qaStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val();
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const button = $(this);
        
        updateQaStatus(leadId, qaStatus, qaReason, button);
    });

    // Handle Manager status dropdown changes
    $('.manager-status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newManagerStatus = $(this).val();
        const managerReason = $(`.manager-reason-input[data-lead-id="${leadId}"]`).val();
        const dropdown = $(this);
        
        updateManagerStatus(leadId, newManagerStatus, managerReason, dropdown);
    });

    // Handle Manager reason save button
    $('.save-manager-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const managerStatus = $(`.manager-status-dropdown[data-lead-id="${leadId}"]`).val();
        const managerReason = $(`.manager-reason-input[data-lead-id="${leadId}"]`).val();
        const button = $(this);
        
        updateManagerStatus(leadId, managerStatus, managerReason, button);
    });

    function updateQaStatus(leadId, qaStatus, qaReason, element) {
        element.prop('disabled', true);
        
        $.ajax({
            url: `/sales/${leadId}/qa-status`,
            method: 'POST',
            data: {
                qa_status: qaStatus,
                qa_reason: qaReason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    element.addClass('border-success');
                    setTimeout(() => {
                        element.removeClass('border-success');
                    }, 2000);
                    
                    // Show success message
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            <strong>Success!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('.breadcrumb-header').after(alertHtml);
                }
            },
            error: function() {
                alert('Failed to update QA status');
            },
            complete: function() {
                element.prop('disabled', false);
            }
        });
    }

    function updateManagerStatus(leadId, managerStatus, managerReason, element) {
        element.prop('disabled', true);
        
        $.ajax({
            url: `/sales/${leadId}/manager-status`,
            method: 'POST',
            data: {
                manager_status: managerStatus,
                manager_reason: managerReason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    element.addClass('border-success');
                    setTimeout(() => {
                        element.removeClass('border-success');
                    }, 2000);
                    
                    // Show success message
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            <strong>Success!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('.breadcrumb-header').after(alertHtml);
                }
            },
            error: function() {
                alert('Failed to update Manager status');
            },
            complete: function() {
                element.prop('disabled', false);
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-dropdown').forEach(select => {
        updateSelectColor(select);

        select.addEventListener('change', function() {
            const leadId = this.dataset.leadId;
            const newStatus = this.value;
            updateSelectColor(this);

            fetch(`/sales/${leadId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="mdi mdi-check-all me-2"></i>
                        Status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                }
            });
        });
    });

    function updateSelectColor(select) {
        const status = select.value;
        switch(status) {
            case 'pending':
                select.style.background = '#fef3c7';
                select.style.color = '#92400e';
                break;
            case 'approved':
                select.style.background = '#d1fae5';
                select.style.color = '#065f46';
                break;
            case 'declined':
                select.style.background = '#fee2e2';
                select.style.color = '#991b1b';
                break;
            case 'underwriting':
                select.style.background = '#dbeafe';
                select.style.color = '#1e40af';
                break;
            case 'chargeback':
                select.style.background = '#fce7f3';
                select.style.color = '#9f1239';
                break;
        }
    }
});
</script>
@endsection
