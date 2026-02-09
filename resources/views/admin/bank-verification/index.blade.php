@extends('layouts.master')

@section('title')
    Bank Verification
@endsection

@section('css')
<style>
    i[class*="mdi"] {
        color: inherit !important;
    }

    .text-success i[class*="mdi"],
    i[class*="mdi"].text-success {
        color: #198754 !important;
    }

    .text-warning i[class*="mdi"],
    i[class*="mdi"].text-warning {
        color: #ffc107 !important;
    }

    .text-danger i[class*="mdi"],
    i[class*="mdi"].text-danger {
        color: #dc3545 !important;
    }

    .text-secondary i[class*="mdi"],
    i[class*="mdi"].text-secondary {
        color: #6c757d !important;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Bank Verification
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
                <i class="mdi mdi-bank me-2"></i>Bank Verification
            </h2>
            <p class="text-muted">Approved & Issued Sales Awaiting Bank Verification</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 justify-content-center">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="d-flex flex-column align-items-center">
                <div class="mb-3 d-flex flex-column align-items-center justify-content-center" style="width: 180px; height: 180px; border-radius: 50%; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);">
                    <i class="mdi mdi-check-circle mb-2" style="font-size: 4rem; color: white;"></i>
                    <p class="text-uppercase fw-semibold mb-1 text-white" style="letter-spacing: 1.5px; font-size: 0.75rem;">GOOD</p>
                    <h1 class="fw-bold mb-0 text-white" style="font-size: 2.5rem;">{{ $good_count }}</h1>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="d-flex flex-column align-items-center">
                <div class="mb-3 d-flex flex-column align-items-center justify-content-center" style="width: 180px; height: 180px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);">
                    <i class="mdi mdi-alert-circle mb-2" style="font-size: 4rem; color: white;"></i>
                    <p class="text-uppercase fw-semibold mb-1 text-white" style="letter-spacing: 1.5px; font-size: 0.75rem;">AVERAGE</p>
                    <h1 class="fw-bold mb-0 text-white" style="font-size: 2.5rem;">{{ $average_count }}</h1>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="d-flex flex-column align-items-center">
                <div class="mb-3 d-flex flex-column align-items-center justify-content-center" style="width: 180px; height: 180px; border-radius: 50%; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);">
                    <i class="mdi mdi-close-circle mb-2" style="font-size: 4rem; color: white;"></i>
                    <p class="text-uppercase fw-semibold mb-1 text-white" style="letter-spacing: 1.5px; font-size: 0.75rem;">BAD</p>
                    <h1 class="fw-bold mb-0 text-white" style="font-size: 2.5rem;">{{ $bad_count }}</h1>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="d-flex flex-column align-items-center">
                <div class="mb-3 d-flex flex-column align-items-center justify-content-center" style="width: 180px; height: 180px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);">
                    <i class="mdi mdi-help-circle mb-2" style="font-size: 4rem; color: white;"></i>
                    <p class="text-uppercase fw-semibold mb-1 text-white" style="letter-spacing: 1.5px; font-size: 0.75rem;">UNVERIFIED</p>
                    <h1 class="fw-bold mb-0 text-white" style="font-size: 2.5rem;">{{ $unverified_count }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Bank Verification List
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('bank-verification.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, policy..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="verification_status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Good" {{ request('verification_status') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Average" {{ request('verification_status') == 'Average' ? 'selected' : '' }}>Average</option>
                                    <option value="Bad" {{ request('verification_status') == 'Bad' ? 'selected' : '' }}>Bad</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="month" class="form-select">
                                    <option value="">Month</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
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
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 150px;">Customer</th>
                                    <th style="min-width: 120px;">Phone</th>
                                    <th style="min-width: 100px;">Carrier</th>
                                    <th style="min-width: 100px;">Policy #</th>
                                    <th style="min-width: 100px;">Premium</th>
                                    <th style="min-width: 120px;">Issued Date</th>
                                    <th style="min-width: 150px;">Assigned B.V</th>
                                    <th style="min-width: 200px;">Comment</th>
                                    <th style="min-width: 120px;">Bank Status</th>
                                    <th style="min-width: 200px;">Bank Reason</th>
                                    <th style="min-width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name }}</strong></td>
                                        <td>{{ $lead->phone_number }}</td>
                                        <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                        <td>{{ $lead->issued_policy_number ?? 'N/A' }}</td>
                                        <td class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                        <td>{{ $lead->issuance_date ? \Carbon\Carbon::parse($lead->issuance_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <select class="form-select form-select-sm assigned-bv-dropdown" data-lead-id="{{ $lead->id }}">
                                                <option value="">Unassigned</option>
                                                @foreach($bankVerifiers as $verifier)
                                                    <option value="{{ $verifier->id }}" {{ $lead->assigned_bank_verifier == $verifier->id ? 'selected' : '' }}>
                                                        {{ $verifier->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm bv-comment-field" data-lead-id="{{ $lead->id }}" rows="2" placeholder="Enter comment...">{{ $lead->bank_verification_comment }}</textarea>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm bv-status-select" data-lead-id="{{ $lead->id }}">
                                                <option value="">Not Set</option>
                                                <option value="Good" {{ $lead->bank_verification_status === 'Good' ? 'selected' : '' }}>Good</option>
                                                <option value="Average" {{ $lead->bank_verification_status === 'Average' ? 'selected' : '' }}>Average</option>
                                                <option value="Bad" {{ $lead->bank_verification_status === 'Bad' ? 'selected' : '' }}>Bad</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $lead->bank_verification_notes ?? '—' }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('bank-verification.show', $lead->id) }}" class="btn btn-sm btn-info" title="View All Details">
                                                    <i class="bx bx-show me-1"></i>View
                                                </a>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#verificationModal-{{ $lead->id }}" title="Update Status">
                                                    <i class="bx bx-edit me-1"></i>Update
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Verification Modal -->
                                    <div class="modal fade" id="verificationModal-{{ $lead->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background: linear-gradient(135deg, #d4af37 0%, #b8a000 100%); color: white;">
                                                    <h5 class="modal-title fw-semibold">
                                                        <i class="mdi mdi-bank me-2"></i>Update Status - {{ $lead->cn_name }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('bank-verification.update', $lead->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Select Status</label>
                                                            <div class="d-grid gap-2">
                                                                <button type="submit" name="bank_verification_status" value="Good" class="btn btn-success btn-lg">
                                                                    <i class="mdi mdi-check-circle me-2"></i>Good
                                                                </button>
                                                                <button type="submit" name="bank_verification_status" value="Average" class="btn btn-warning btn-lg">
                                                                    <i class="mdi mdi-alert-circle me-2"></i>Average
                                                                </button>
                                                                <button type="submit" name="bank_verification_status" value="Bad" class="btn btn-danger btn-lg">
                                                                    <i class="mdi mdi-alert-octagon me-2"></i>Bad
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="notes-{{ $lead->id }}" class="form-label fw-semibold">Reason</label>
                                                            <textarea class="form-control" id="notes-{{ $lead->id }}" name="bank_verification_notes" rows="3" placeholder="Add reason...">{{ $lead->bank_verification_notes }}</textarea>
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
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No approved & issued sales found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($leads->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $leads->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Handle assigned bank verifier dropdown change
    $('.assigned-bv-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const verifierId = $(this).val();
        const dropdown = $(this);
        
        if (confirm('Assign this bank verifier?')) {
            dropdown.prop('disabled', true);
            
            $.ajax({
                url: `/bank-verification/${leadId}/assign-verifier`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    assigned_bank_verifier: verifierId || null
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        dropdown.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to assign bank verifier');
                    dropdown.prop('disabled', false);
                    location.reload();
                }
            });
        } else {
            location.reload();
        }
    });

    // Auto-save comment and status when changed
    $('.bv-comment-field, .bv-status-select').on('change blur', function() {
        const row = $(this).closest('tr');
        const leadId = $(this).data('lead-id');
        const comment = row.find('.bv-comment-field').val();
        const status = row.find('.bv-status-select').val();
        
        // Debounce to prevent too many requests
        clearTimeout(window.bvUpdateTimeout);
        window.bvUpdateTimeout = setTimeout(function() {
            $.ajax({
                url: `/bank-verification/${leadId}/update-assignment`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    bank_verification_comment: comment,
                    bank_verification_status: status || null
                },
                success: function(response) {
                    if (response.success) {
                        // Silently updated - no alert needed
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to update details');
                }
            });
        }, 1000);
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check me-2"></i>
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.card-body').first().prepend(alertHtml);
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 3000);
    }
});
</script>
@endsection
