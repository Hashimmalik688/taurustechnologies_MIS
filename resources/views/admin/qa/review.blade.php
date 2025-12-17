@extends('layouts.master')

@section('title')
    QA Review
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            QA
        @endslot
        @slot('title')
            Review
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
                <i class="bx bx-check-double me-2"></i>QA Review
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Sales for QA Review
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('qa.review') }}" class="mb-4">
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
                                <select name="qa_status" class="form-select">
                                    <option value="">All QA Status</option>
                                    <option value="In Review" {{ request('qa_status') == 'In Review' ? 'selected' : '' }}>🔍 In Review</option>
                                    <option value="Approved" {{ request('qa_status') == 'Approved' ? 'selected' : '' }}>✅ Approved</option>
                                    <option value="Rejected" {{ request('qa_status') == 'Rejected' ? 'selected' : '' }}>❌ Rejected</option>
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
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary me-2"><i class="bx bx-search"></i> Filter</button>
                                <a href="{{ route('qa.review') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:150px;">Client Name</th>
                                    <th style="min-width:130px;">Closer</th>
                                    <th style="min-width:110px;">Sale Date</th>
                                    <th style="min-width:140px;">QA Status</th>
                                    <th style="min-width:250px;">QA Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->cn_name }}</strong></td>
                                        <td>
                                            @if($lead->closer_name)
                                                <span class="badge bg-info">{{ $lead->closer_name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->sale_date)
                                                {{ \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') }}
                                            @elseif($lead->sale_at)
                                                {{ \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') }}
                                            @elseif($lead->created_at)
                                                {{ \Carbon\Carbon::parse($lead->created_at)->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm qa-status-dropdown" 
                                                    data-lead-id="{{ $lead->id }}" 
                                                    style="min-width: 130px;">
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
                                                      rows="3" 
                                                      style="min-width: 220px;">{{ $lead->qa_reason ?? '' }}</textarea>
                                            <button class="btn btn-sm btn-primary mt-1 save-qa-reason" data-lead-id="{{ $lead->id }}">
                                                <i class="bx bx-save"></i> Save QA Review
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No sales data available for QA review</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($leads->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $leads->appends(['search' => request('search'), 'carrier' => request('carrier'), 'qa_status' => request('qa_status'), 'month' => request('month'), 'year' => request('year')])->links() }}
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
});
</script>
@endsection