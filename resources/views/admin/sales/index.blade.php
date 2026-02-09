@extends('layouts.master')

@section('title')
    Sales Management
@endsection

@section('css')
<style>
    /* Fixed Table Container - Scrollable */
    .top-scrollbar-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        margin-bottom: 0;
        height: 20px;
    }
    
    .top-scrollbar-content {
        height: 1px;
    }
    
    /* Fixed Header Section  - Scrolls horizontally in sync with table */
    .table-header-fixed {
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #dee2e6;
        border-bottom: none;
        background: #f8f9fa;
    }
    
    .table-header-fixed table {
        margin-bottom: 0 !important;
        border-bottom: none !important;
    }
    
    .table-header-fixed thead th {
        background: #f8f9fa !important;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6 !important;
        white-space: nowrap;
        padding: 12px 8px;
    }
    
    /* Scrollable Table Body */
    .table-responsive {
        max-height: 450px;
        overflow-x: auto;
        overflow-y: auto;
        position: relative;
        border: 1px solid #dee2e6;
    }
    
    .table-responsive table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        margin-bottom: 0 !important;
    }
    
    .table-responsive table thead {
        display: none; /* Hide original thead since we have fixed header */
    }
    
    .table-responsive table td {
        white-space: nowrap;
        padding: 12px 8px;
    }
    
    /* Hide scrollbars on header (we have top scrollbar) */
    .table-header-fixed::-webkit-scrollbar {
        display: none;
    }
    .table-header-fixed {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Gold scrollbar styling */
    .top-scrollbar-wrapper::-webkit-scrollbar,
    .table-responsive::-webkit-scrollbar {
        height: 12px;
        width: 12px;
    }
    
    .top-scrollbar-wrapper::-webkit-scrollbar-track,
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }
    
    .top-scrollbar-wrapper::-webkit-scrollbar-thumb,
    .table-responsive::-webkit-scrollbar-thumb {
        background: #d4af37;
        border-radius: 6px;
    }
    
    .top-scrollbar-wrapper::-webkit-scrollbar-thumb:hover,
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #b8941f;
    }
</style>
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

    <!-- KPI Status Cards -->
    <div class="row mb-4">
        @foreach($statusCounts as $status => $count)
            @php
                $config = $statusColors[$status] ?? ['label' => ucfirst($status), 'gradient' => 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)', 'icon' => 'mdi-information'];
            @endphp
            <div class="col-md col-sm-6 mb-3">
                <div class="card border-0 shadow" style="background: {{ $config['gradient'] }} !important; min-height: 180px;">
                    <div class="card-body text-center p-4">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <i class="mdi {{ $config['icon'] }}" style="font-size: 2.5rem !important; color: #fff !important;"></i>
                        </div>
                        <h6 class="mb-2 fw-semibold text-uppercase" style="letter-spacing: 0.5px; color: #fff !important;">{{ $config['label'] }}</h6>
                        <h1 class="mb-0 fw-bold" style="color: #fff !important; font-size: 2.5rem;">{{ number_format($count) }}</h1>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Sales List
                    </h5>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" id="salesSearch" class="form-control form-control-sm" placeholder="Search by name, phone, carrier..." style="width: 250px;">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#manualSaleModal">
                            <i class="bx bx-plus-circle me-1"></i> Manual Entry
                        </button>
                        @if(!auth()->user()->hasRole('QA'))
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importOldDataModal">
                                <i class="bx bx-upload me-1"></i> Import Data
                            </button>
                        @endif
                    </div>
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
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Declined</option>
                                    <option value="underwritten" {{ request('status') == 'underwritten' ? 'selected' : '' }}>Underwriting</option>
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
                    
                    <!-- Top Scrollbar -->
                    <div class="top-scrollbar-wrapper" id="topScrollbar">
                        <div class="top-scrollbar-content" id="topScrollbarContent"></div>
                    </div>
                    
                    <!-- Fixed Table Headers (Outside scrollable area) -->
                    <div class="table-header-fixed" id="tableHeader">
                        <table class="table table-bordered table-sm mb-0" style="table-layout: fixed;">
                            <colgroup>
                                @if(auth()->user()->hasRole('QA'))
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:150px; width:150px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:220px; width:220px;">
                                @else
                                    <col style="min-width:220px; width:220px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:150px; width:150px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:220px; width:220px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:220px; width:220px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:200px; width:200px;">
                                @endif
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    @if(auth()->user()->hasRole('QA'))
                                        {{-- QA View: Limited columns --}}
                                        <th>Client Name</th>
                                        <th>Phone</th>
                                        <th>Closer</th>
                                        <th>Assigned Partner</th>
                                        <th>Sale Date</th>
                                        <th>QA Status</th>
                                        <th>QA Reason</th>
                                    @else
                                        {{-- Full View for other roles --}}
                                        <th class="text-center">Actions</th>
                                        <th>Client Name</th>
                                        <th>Phone</th>
                                        <th>Closer</th>
                                        <th>Assigned Partner</th>
                                        <th>Sale Date</th>
                                        <th>Carrier</th>
                                        <th>Policy Type</th>
                                        <th>Coverage</th>
                                        <th>Premium</th>
                                        <th>Settlement Type</th>
                                        <th>Initial Draft</th>
                                        <th>Future Draft</th>
                                        <th>QA Status</th>
                                        <th>QA Reason</th>
                                        <th>Manager Status</th>
                                        <th>Manager Reason</th>
                                        <th>Follow Up Required</th>
                                        <th>Follow Up Scheduled</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                    
                    <!-- Scrollable Table Body -->
                    <div class="table-responsive" id="tableWrapper">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle" id="salesTable" style="table-layout: fixed;">
                            <colgroup>
                                @if(auth()->user()->hasRole('QA'))
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:150px; width:150px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:220px; width:220px;">
                                @else
                                    <col style="min-width:220px; width:220px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:150px; width:150px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:140px; width:140px;">
                                    <col style="min-width:160px; width:160px;">
                                    <col style="min-width:220px; width:220px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:220px; width:220px;">
                                    <col style="min-width:180px; width:180px;">
                                    <col style="min-width:200px; width:200px;">
                                @endif
                            </colgroup>
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
                                            <td>
                                                @if($lead->assigned_partner)
                                                    <span class="badge bg-primary">{{ $lead->assigned_partner }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : ($lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : 'N/A') }}</td>
                                            <td>
                                                <select class="form-select form-select-sm qa-status-dropdown" data-lead-id="{{ $lead->id }}" data-current-status="{{ $lead->qa_status ?? 'Pending' }}" style="min-width: 130px;">
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
                                                          rows="2" 
                                                          style="min-width: 180px;">{{ $lead->qa_reason ?? '' }}</textarea>
                                                <button class="btn btn-sm btn-primary mt-1 save-qa-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                            </td>
                                        @else
                                            {{-- Full View for other roles --}}
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1" role="group">
                                                    @php
                                                        $zoomNumber = preg_replace('/[^\d\+]/', '', $lead->phone_number);
                                                        $callUrl = 'zoomphonecall://' . urlencode($zoomNumber);
                                                    @endphp
                                                    <a href="{{ route('sales.prettyPrint', $lead->id) }}" class="btn btn-success btn-sm" title="Pretty Print" target="_blank" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <button onclick="window.location.href='{{ $callUrl }}'" class="btn btn-warning btn-sm" title="Call" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                        <i class="fas fa-phone-alt"></i>
                                                    </button>
                                                    <a href="{{ route('sales.show', $lead->id) }}" class="btn btn-info btn-sm text-white" title="View" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sales.edit', $lead->id) }}" class="btn btn-primary btn-sm" title="Edit" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-{{ $lead->id }}" title="Delete" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
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
                                            <td>
                                                @if($lead->assigned_partner)
                                                    <span class="badge bg-primary">{{ $lead->assigned_partner }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : ($lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : 'N/A') }}</td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if($lead->carrier_name)
                                                        <small class="text-muted fw-semibold">Current: {{ $lead->carrier_name }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-1">
                                                        <select class="form-select form-select-sm editable-carrier" data-lead-id="{{ $lead->id }}" style="min-width: 120px;">
                                                            <option value="">-- None --</option>
                                                            @foreach($insuranceCarriers as $carrier)
                                                                <option value="{{ $carrier }}" {{ $lead->carrier_name == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="carrier" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if($lead->policy_type)
                                                        <small class="text-muted fw-semibold">Current: {{ $lead->policy_type }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-1">
                                                        <select class="form-select form-select-sm editable-policy-type" data-lead-id="{{ $lead->id }}" style="min-width: 110px;">
                                                            <option value="">-- None --</option>
                                                            <option value="G.I" {{ $lead->policy_type == 'G.I' ? 'selected' : '' }}>G.I</option>
                                                            <option value="Graded" {{ $lead->policy_type == 'Graded' ? 'selected' : '' }}>Graded</option>
                                                            <option value="Level" {{ $lead->policy_type == 'Level' ? 'selected' : '' }}>Level</option>
                                                            <option value="Modified" {{ $lead->policy_type == 'Modified' ? 'selected' : '' }}>Modified</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="policy_type" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if($lead->coverage_amount)
                                                        <small class="text-muted fw-semibold">Current: ${{ number_format($lead->coverage_amount, 2) }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-1">
                                                        <input type="number" step="0.01" class="form-control form-control-sm editable-coverage" data-lead-id="{{ $lead->id }}" value="{{ $lead->coverage_amount ?? '' }}" placeholder="0.00" style="min-width: 100px;">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="coverage" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if($lead->monthly_premium)
                                                        <small class="text-muted fw-semibold">Current: ${{ number_format($lead->monthly_premium, 2) }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-1">
                                                        <input type="number" step="0.01" class="form-control form-control-sm editable-premium" data-lead-id="{{ $lead->id }}" value="{{ $lead->monthly_premium ?? '' }}" placeholder="0.00" style="min-width: 90px;">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="premium" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($lead->settlement_type)
                                                    <span class="badge bg-{{ $lead->settlement_type == 'level' ? 'primary' : ($lead->settlement_type == 'graded' ? 'info' : ($lead->settlement_type == 'gi' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst($lead->settlement_type) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <select class="form-select form-select-sm qa-status-dropdown" data-lead-id="{{ $lead->id }}" data-current-status="{{ $lead->qa_status ?? 'Pending' }}" style="min-width: 130px;">
                                                    <option value="Pending" {{ ($lead->qa_status ?? 'Pending') == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                    <option value="Good" {{ ($lead->qa_status ?? '') == 'Good' ? 'selected' : '' }}>✅ Good</option>
                                                    <option value="Avg" {{ ($lead->qa_status ?? '') == 'Avg' ? 'selected' : '' }}>⚠️ Avg</option>
                                                    <option value="Bad" {{ ($lead->qa_status ?? '') == 'Bad' ? 'selected' : '' }}>❌ Bad</option>
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
                                                <select class="form-select form-select-sm manager-status-dropdown" data-lead-id="{{ $lead->id }}" data-current-status="{{ $lead->manager_status ?? 'pending' }}" style="min-width: 130px;">
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
                                                @if(auth()->user()->hasRole('Super Admin') && $lead->manager_status !== 'pending')
                                                    <button class="btn btn-sm btn-warning mt-1 reset-manager-status" data-lead-id="{{ $lead->id }}" title="Reset to Pending (Super Admin only)">
                                                        <i class="bx bx-undo"></i> Reset
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->followup_required)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->followup_required && $lead->followup_scheduled_at)
                                                    <span class="text-primary">
                                                        <i class="bx bx-calendar me-1"></i>
                                                        {{ \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('M d, Y h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
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
    // Dual Scrollbar Synchronization
    const topScrollbar = document.getElementById('topScrollbar');
    const tableWrapper = document.getElementById('tableWrapper');
    const topScrollbarContent = document.getElementById('topScrollbarContent');
    const table = document.getElementById('salesTable');
    
    // Function to update top scrollbar width
    function updateTopScrollbarWidth() {
        if (table && topScrollbarContent) {
            topScrollbarContent.style.width = table.offsetWidth + 'px';
        }
    }
    
    // Initial width setup
    updateTopScrollbarWidth();
    
    // Update on window resize
    window.addEventListener('resize', updateTopScrollbarWidth);
    
    // Get table header element
    const tableHeader = document.getElementById('tableHeader');
    
    // Synchronize scrolling: top scrollbar -> table body + header
    if (topScrollbar && tableWrapper) {
        topScrollbar.addEventListener('scroll', function() {
            if (!tableWrapper.scrollSyncing) {
                topScrollbar.scrollSyncing = true;
                tableWrapper.scrollLeft = topScrollbar.scrollLeft;
                if (tableHeader) {
                    tableHeader.scrollLeft = topScrollbar.scrollLeft;
                }
                setTimeout(() => {
                    topScrollbar.scrollSyncing = false;
                }, 50);
            }
        });
        
        // Synchronize scrolling: table body -> top scrollbar + header
        tableWrapper.addEventListener('scroll', function() {
            if (!topScrollbar.scrollSyncing) {
                tableWrapper.scrollSyncing = true;
                topScrollbar.scrollLeft = tableWrapper.scrollLeft;
                if (tableHeader) {
                    tableHeader.scrollLeft = tableWrapper.scrollLeft;
                }
                setTimeout(() => {
                    tableWrapper.scrollSyncing = false;
                }, 50);
            }
        });
    }
    
    // Realtime search functionality for sales table
    $('#salesSearch').on('keyup', function() {
        const searchValue = $(this).val().toLowerCase();
        $('.table-striped tbody tr').each(function() {
            const row = $(this);
            // Search across multiple columns: name, phone, carrier, closer, partner
            const clientName = row.find('td:nth-child(2)').text().toLowerCase() || row.find('td:nth-child(1)').text().toLowerCase();
            const phoneNumber = row.find('td:nth-child(3)').text().toLowerCase() || row.find('td:nth-child(2)').text().toLowerCase();
            const closer = row.find('td:nth-child(4)').text().toLowerCase() || row.find('td:nth-child(3)').text().toLowerCase();
            const partner = row.find('td:nth-child(5)').text().toLowerCase() || row.find('td:nth-child(4)').text().toLowerCase();
            const carrier = row.find('td:nth-child(7)').text().toLowerCase() || row.find('td:nth-child(6)').text().toLowerCase();
            
            // Check if any field matches the search
            if (clientName.includes(searchValue) || 
                phoneNumber.includes(searchValue) || 
                closer.includes(searchValue) || 
                partner.includes(searchValue) || 
                carrier.includes(searchValue)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // Handle inline field updates (carrier, policy_type, coverage, premium)
    $('.save-field-btn').click(function() {
        const button = $(this);
        const leadId = button.data('lead-id');
        const fieldType = button.data('field');
        let value = '';
        let fieldInput;
        
        // Get the value based on field type
        switch(fieldType) {
            case 'carrier':
                fieldInput = $(`.editable-carrier[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'policy_type':
                fieldInput = $(`.editable-policy-type[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'coverage':
                fieldInput = $(`.editable-coverage[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'premium':
                fieldInput = $(`.editable-premium[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
        }
        
        if (!value || value === '') {
            alert('Please enter a value');
            return;
        }
        
        button.prop('disabled', true);
        button.html('<i class="bx bx-loader bx-spin"></i>');
        
        $.ajax({
            url: `/sales/${leadId}/update-field`,
            method: 'POST',
            data: {
                field: fieldType,
                value: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.html('<i class="bx bx-check"></i>');
                    button.addClass('btn-success');
                    fieldInput.addClass('border-success');
                    
                    setTimeout(() => {
                        button.html('<i class="bx bx-check"></i>');
                        button.removeClass('btn-success');
                        fieldInput.removeClass('border-success');
                    }, 2000);
                    
                    // Show success toast
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
                            <i class="mdi mdi-check me-2"></i>
                            <strong>Updated!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('body').append(alertHtml);
                    setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
                }
            },
            error: function(xhr) {
                button.html('<i class="bx bx-check"></i>');
                alert('Failed to update field');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

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
        const currentStatus = $(this).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const dropdown = $(this);
        
        // Show confirmation dialog
        if (confirm(`Are you sure you want to change the QA status to "${newQaStatus}"?\n\nNote: Only ONE change is allowed for each lead.`)) {
            updateQaStatus(leadId, newQaStatus, currentStatus, qaReason, dropdown);
        } else {
            // Reset dropdown to previous value
            dropdown.val(currentStatus);
        }
    });

    // Handle QA reason save button
    $('.save-qa-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const qaStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val();
        const currentStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const button = $(this);
        
        // Show confirmation dialog
        if (confirm(`Are you sure you want to save QA review with status "${qaStatus}"?\n\nNote: Only ONE change is allowed for each lead.`)) {
            updateQaStatus(leadId, qaStatus, currentStatus, qaReason, button);
        }
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

    // Handle reset Manager status button (Super Admin only)
    $('.reset-manager-status').click(function() {
        const leadId = $(this).data('lead-id');
        const button = $(this);
        
        if (confirm('Are you sure you want to reset this Manager status to Pending? This action is only available to Super Admin.')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: `/sales/${leadId}/manager-status/reset`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Reset dropdown and data attribute
                        const dropdown = $(`.manager-status-dropdown[data-lead-id="${leadId}"]`);
                        dropdown.val('pending').data('current-status', 'pending');
                        $(`.manager-reason-input[data-lead-id="${leadId}"]`).val('');
                        
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
                    alert(response.message || 'Failed to reset Manager status');
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

<!-- Manual Sales Entry Modal -->
<div class="modal fade" id="manualSaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #d4af37 0%, #b8a000 100%); color: white;">
                <h5 class="modal-title fw-semibold">
                    <i class="mdi mdi-pencil-plus me-2"></i>Create Manual Sale Entry
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales.storeManual') }}" method="POST">
                @csrf
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row">
                        <!-- Customer Information Section -->
                        <div class="col-12">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-account-circle me-2"></i>Customer Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cn_name" class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cn_name" name="cn_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary" class="form-label fw-semibold">Beneficiary</label>
                            <input type="text" class="form-control" id="beneficiary" name="beneficiary" placeholder="Beneficiary name">
                        </div>

                        <!-- Policy Information Section -->
                        <div class="col-12 mt-2">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-file-document-outline me-2"></i>Policy Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="carrier_name" class="form-label fw-semibold">Carrier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="carrier_name" name="carrier_name" required placeholder="e.g., Guardian, AXA, etc.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="policy_type" class="form-label fw-semibold">Policy Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="policy_type" name="policy_type" required>
                                <option value="">Select Policy Type</option>
                                <option value="G.I">G.I</option>
                                <option value="Graded">Graded</option>
                                <option value="Level">Level</option>
                                <option value="Modified">Modified</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="policy_number" class="form-label fw-semibold">Policy Number</label>
                            <input type="text" class="form-control" id="policy_number" name="policy_number" placeholder="Policy #">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="coverage_amount" class="form-label fw-semibold">Coverage Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="coverage_amount" name="coverage_amount" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monthly_premium" class="form-label fw-semibold">Monthly Premium <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="monthly_premium" name="monthly_premium" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="settlement_type" class="form-label fw-semibold">Settlement Type</label>
                            <select class="form-select" id="settlement_type" name="settlement_type">
                                <option value="">Select Settlement Type</option>
                                <option value="level">Level</option>
                                <option value="graded">Graded</option>
                                <option value="gi">GI (Guaranteed Issue)</option>
                                <option value="modified">Modified</option>
                            </select>
                            <small class="text-muted">Settlement percentage type for commission calculation</small>
                        </div>

                        <!-- Transaction Information Section -->
                        <div class="col-12 mt-2">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-cash-multiple me-2"></i>Transaction Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_date" class="form-label fw-semibold">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="closer_name" class="form-label fw-semibold">Closer/Agent <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="closer_name" name="closer_name" required placeholder="Name of agent/closer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="accepted">Accepted</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="rejected">Rejected</option>
                                <option value="chargeback">Chargeback</option>
                            </select>
                        </div>

                        <!-- Bank Information Section -->
                        <div class="col-12 mt-2">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-bank me-2"></i>Bank Information (Optional)
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label fw-semibold">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="account_number" class="form-label fw-semibold">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account #">
                        </div>

                        <!-- Additional Notes -->
                        <div class="col-12 mb-3">
                            <label for="comments" class="form-label fw-semibold">Comments</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Add any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-save me-1"></i> Create Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
