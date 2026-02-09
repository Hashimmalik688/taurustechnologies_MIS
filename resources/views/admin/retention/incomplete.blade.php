@extends('layouts.master')

@section('title')
    Issuance - Retention Management
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Retention
        @endslot
        @slot('title')
            Incomplete Issuance
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-soft-danger" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="mdi mdi-history me-2"></i>Incomplete Issuance - Retention
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Incomplete Issuance List
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('retention.incomplete') }}" class="mb-4">
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
                                <select name="disposition" class="form-select">
                                    <option value="">All Disposition</option>
                                    <option value="Via Portal" {{ request('disposition') == 'Via Portal' ? 'selected' : '' }}>Via Portal</option>
                                    <option value="Via Email" {{ request('disposition') == 'Via Email' ? 'selected' : '' }}>Via Email</option>
                                    <option value="By Carrier" {{ request('disposition') == 'By Carrier' ? 'selected' : '' }}>By Carrier</option>
                                    <option value="By Bank" {{ request('disposition') == 'By Bank' ? 'selected' : '' }}>By Bank</option>
                                </select>
                            </div>
                            <div class="col-md-2">
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
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="min-width:100px;">Actions</th>
                                    <th style="min-width:150px;">Client Name</th>
                                    <th style="min-width:130px;">Phone</th>
                                    <th style="min-width:110px;">Sale Date</th>
                                    <th style="min-width:120px;">Carrier</th>
                                    <th style="min-width:140px;">Disposition Status</th>
                                    <th style="min-width:200px;">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('retention.incompleteDetails', $lead->id) }}" class="btn btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#dispositionModal-{{ $lead->id }}" title="Set Disposition">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td><strong>{{ $lead->cn_name }}</strong></td>
                                        <td>{{ $lead->phone_number }}</td>
                                        <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->issuance_disposition)
                                                @php
                                                    $badgeClass = match($lead->issuance_disposition) {
                                                        'Via Portal' => 'bg-success',
                                                        'Via Email' => 'bg-info',
                                                        'By Carrier' => 'bg-warning',
                                                        'By Bank' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $lead->issuance_disposition }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $lead->issuance_reason ?? '—' }}</small>
                                        </td>
                                    </tr>

                                    <!-- Disposition Modal -->
                                    <div class="modal fade" id="dispositionModal-{{ $lead->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background: linear-gradient(135deg, #d4af37 0%, #b8a000 100%); color: white;">
                                                    <h5 class="modal-title">
                                                        <i class="mdi mdi-clipboard-list me-2"></i>Set Disposition - {{ $lead->cn_name }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="dispositionForm-{{ $lead->id }}" class="disposition-form">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="disposition-{{ $lead->id }}" class="form-label fw-bold">
                                                                Select Disposition Channel <span class="text-danger">*</span>
                                                            </label>
                                                            <select id="disposition-{{ $lead->id }}" name="issuance_disposition" class="form-select disposition-select" data-lead-id="{{ $lead->id }}" required>
                                                                <option value="">-- Choose One --</option>
                                                                <option value="Via Portal" {{ $lead->issuance_disposition == 'Via Portal' ? 'selected' : '' }}>📱 Via Portal</option>
                                                                <option value="Via Email" {{ $lead->issuance_disposition == 'Via Email' ? 'selected' : '' }}>📧 Via Email</option>
                                                                <option value="By Carrier" {{ $lead->issuance_disposition == 'By Carrier' ? 'selected' : '' }}>🏢 By Carrier</option>
                                                                <option value="By Bank" {{ $lead->issuance_disposition == 'By Bank' ? 'selected' : '' }}>🏦 By Bank</option>
                                                            </select>
                                                        </div>

                                                        <!-- Conditional: Check Other Insurances -->
                                                        <div class="check-other-insurances-container" id="checkContainer-{{ $lead->id }}" style="display: none;">
                                                            <div class="alert alert-info" role="alert">
                                                                <i class="mdi mdi-information me-2"></i>
                                                                <strong>Checking for other insurances...</strong>
                                                                <div class="spinner-border spinner-border-sm ms-2" role="status">
                                                                    <span class="visually-hidden">Loading...</span>
                                                                </div>
                                                            </div>
                                                            <div class="other-insurances-list mt-3" id="otherList-{{ $lead->id }}"></div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="reason-{{ $lead->id }}" class="form-label fw-bold">Reason/Notes</label>
                                                            <textarea id="reason-{{ $lead->id }}" name="issuance_reason" class="form-control" rows="4" placeholder="Enter reason for this disposition..." style="max-width: 100%;">{{ $lead->issuance_reason ?? '' }}</textarea>
                                                            <div class="form-text">Maximum 1000 characters</div>
                                                        </div>

                                                        <!-- Lead Details Summary (Read-only) -->
                                                        <div class="card bg-light mt-3">
                                                            <div class="card-header">
                                                                <h6 class="mb-0">Lead Summary</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Name:</strong> {{ $lead->cn_name }}</p>
                                                                        <p><strong>Phone:</strong> {{ $lead->phone_number }}</p>
                                                                        <p><strong>Carrier:</strong> {{ $lead->carrier_name ?? 'N/A' }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Policy Type:</strong> {{ $lead->policy_type ?? 'N/A' }}</p>
                                                                        <p><strong>Coverage:</strong> ${{ number_format($lead->coverage_amount ?? 0, 2) }}</p>
                                                                        <p><strong>Sale Date:</strong> {{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A' }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-success btn-sm save-disposition" data-lead-id="{{ $lead->id }}">
                                                        <i class="bx bx-save"></i> Save Disposition
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No incomplete issuance data available</p>
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
    // Handle disposition dropdown change
    $('.disposition-select').change(function() {
        const leadId = $(this).data('lead-id');
        const disposition = $(this).val();
        const container = $(`#checkContainer-${leadId}`);

        if (disposition === 'By Carrier' || disposition === 'By Bank') {
            container.show();
            checkOtherInsurances(leadId, disposition);
        } else {
            container.hide();
        }
    });

    // Check for other insurances via AJAX
    function checkOtherInsurances(leadId, disposition) {
        $.ajax({
            url: `/retention/check-other-insurances/${leadId}`,
            method: 'GET',
            data: { disposition: disposition },
            dataType: 'json',
            success: function(response) {
                const container = $(`#checkContainer-${leadId}`);
                const listDiv = $(`#otherList-${leadId}`);

                container.find('.alert').html(`
                    <i class="mdi mdi-check-circle me-2"></i>
                    <strong>${response.count === 0 ? 'No' : response.count} other insurance(s) found with ${disposition === 'By Carrier' ? 'this carrier' : 'this bank account'}</strong>
                `).removeClass('alert-info').addClass(response.count > 0 ? 'alert-warning' : 'alert-success');

                if (response.count > 0) {
                    let html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead class="table-light"><tr><th>Policy#</th><th>Carrier</th><th>Type</th><th>Sale Date</th></tr></thead><tbody>';
                    response.insurances.forEach(insurance => {
                        html += `<tr>
                            <td>${insurance.policy_number || 'N/A'}</td>
                            <td>${insurance.carrier_name || 'N/A'}</td>
                            <td>${insurance.policy_type || 'N/A'}</td>
                            <td>${insurance.sale_date ? new Date(insurance.sale_date).toLocaleDateString() : 'N/A'}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                    listDiv.html(html);
                } else {
                    listDiv.html('');
                }
            },
            error: function() {
                $(`#otherList-${leadId}`).html('<div class="alert alert-danger">Error checking other insurances</div>');
            }
        });
    }

    // Save disposition
    $('.save-disposition').click(function() {
        const leadId = $(this).data('lead-id');
        const form = $(`#dispositionForm-${leadId}`);
        const disposition = form.find('[name="issuance_disposition"]').val();
        const reason = form.find('[name="issuance_reason"]').val();

        if (!disposition) {
            alert('Please select a disposition channel');
            return;
        }

        $.ajax({
            url: `/retention/${leadId}/disposition`,
            method: 'POST',
            data: {
                issuance_disposition: disposition,
                issuance_reason: reason,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            <strong>Success!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('.card-header').after(alertHtml);
                    
                    // Close modal and reload
                    $(`#dispositionModal-${leadId}`).modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to save disposition';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });

    // Trigger check on modal show if disposition already selected
    $('.modal').on('show.bs.modal', function() {
        const leadId = $(this).attr('id').split('-')[2];
        const disposition = $(`#disposition-${leadId}`).val();
        if (disposition === 'By Carrier' || disposition === 'By Bank') {
            $(`#checkContainer-${leadId}`).show();
            checkOtherInsurances(leadId, disposition);
        }
    });
});
</script>
@endsection
