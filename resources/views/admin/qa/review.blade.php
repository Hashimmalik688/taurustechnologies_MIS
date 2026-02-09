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

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="bx bx-check-double me-2"></i>QA Review
            </h2>
        </div>
    </div>

    <!-- QA Analytics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div style="font-size: 2rem; color: #6b7280; margin-bottom: 0.5rem;">
                        <i class="bx bx-bar-chart-alt-2"></i>
                    </div>
                    <h6 class="text-muted mb-2">Total Sales</h6>
                    <h3 class="text-gold fw-bold">{{ $qaAnalytics['total'] }}</h3>
                    <small class="text-muted">All sales reviewed</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body text-center">
                    <div style="font-size: 2rem; color: #f59e0b; margin-bottom: 0.5rem;">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <h6 class="text-muted mb-2">Pending</h6>
                    <h3 class="fw-bold" style="color: #f59e0b;">{{ $qaAnalytics['pending'] }}</h3>
                    <small class="text-muted">{{ $qaAnalytics['pending_percent'] }}% awaiting review</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body text-center">
                    <div style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem;">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <h6 class="text-muted mb-2">Good</h6>
                    <h3 class="fw-bold" style="color: #10b981;">{{ $qaAnalytics['good'] }}</h3>
                    <small class="text-muted">{{ $qaAnalytics['good_percent'] }}% passed</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body text-center">
                    <div style="font-size: 2rem; color: #ef4444; margin-bottom: 0.5rem;">
                        <i class="bx bx-x-circle"></i>
                    </div>
                    <h6 class="text-muted mb-2">Issues</h6>
                    <h3 class="fw-bold" style="color: #ef4444;">{{ $qaAnalytics['avg'] + $qaAnalytics['bad'] }}</h3>
                    <small class="text-muted">{{ $qaAnalytics['issues_percent'] }}% need attention</small>
                </div>
            </div>
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
                                    <option value="Pending" {{ request('qa_status') == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="Good" {{ request('qa_status') == 'Good' ? 'selected' : '' }}>✅ Good</option>
                                    <option value="Avg" {{ request('qa_status') == 'Avg' ? 'selected' : '' }}>⚠️ Avg</option>
                                    <option value="Bad" {{ request('qa_status') == 'Bad' ? 'selected' : '' }}>❌ Bad</option>
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
                                                    data-current-status="{{ $lead->qa_status ?? 'Pending' }}"
                                                    style="min-width: 130px;">
                                                <option value="Pending" {{ ($lead->qa_status ?? 'Pending') == 'Pending' ? 'selected' : '' }}>
                                                    ⏳ Pending
                                                </option>
                                                <option value="Good" {{ ($lead->qa_status ?? '') == 'Good' ? 'selected' : '' }}>
                                                    ✅ Good
                                                </option>
                                                <option value="Avg" {{ ($lead->qa_status ?? '') == 'Avg' ? 'selected' : '' }}>
                                                    ⚠️ Avg
                                                </option>
                                                <option value="Bad" {{ ($lead->qa_status ?? '') == 'Bad' ? 'selected' : '' }}>
                                                    ❌ Bad
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
                                            @if(auth()->user()->hasRole('Super Admin') && $lead->qa_status !== 'Pending')
                                                <button class="btn btn-sm btn-warning mt-1 reset-qa-status" data-lead-id="{{ $lead->id }}" title="Reset to Pending (Super Admin only)">
                                                    <i class="bx bx-undo"></i> Reset
                                                </button>
                                            @endif
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
        const currentStatus = $(this).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const dropdown = $(this);
        
        updateQaStatus(leadId, newQaStatus, currentStatus, qaReason, dropdown);
    });

    // Handle QA reason save button
    $('.save-qa-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const qaStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val();
        const currentStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const button = $(this);
        
        updateQaStatus(leadId, qaStatus, currentStatus, qaReason, button);
    });

    // Handle reset QA status button (Super Admin only)
    $('.reset-qa-status').click(function() {
        const leadId = $(this).data('lead-id');
        const button = $(this);
        
        if (confirm('Are you sure you want to reset this QA status to Pending? This action is only available to Super Admin.')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: `/sales/${leadId}/qa-status/reset`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Reset dropdown and data attribute
                        const dropdown = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`);
                        dropdown.val('Pending').data('current-status', 'Pending');
                        $(`.qa-reason-input[data-lead-id="${leadId}"]`).val('');
                        
                        button.addClass('btn-success');
                        setTimeout(() => {
                            button.removeClass('btn-success');
                        }, 2000);
                        
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-undo me-2"></i>
                                <strong>Reset by Super Admin!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.breadcrumb-header').after(alertHtml);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to reset QA status');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });

    function updateQaStatus(leadId, qaStatus, currentStatus, qaReason, element) {
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
                    // Update the data attribute with new status
                    $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status', qaStatus);
                    
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
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response.message) {
                    alert(response.message);
                } else {
                    alert('Failed to update QA status');
                }
                // Reset dropdown to previous value on error
                $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val(currentStatus);
            },
            complete: function() {
                element.prop('disabled', false);
            }
        });
    }
});
</script>
@endsection