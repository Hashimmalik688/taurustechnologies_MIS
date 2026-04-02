@extends('layouts.master')

@section('title')
    My Dashboard
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    /* Status mini-pills for sales table */
    .st-pill { display:inline-block;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:700;text-transform:capitalize; }
    .st-accepted { background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.15); }
    .st-underwritten { background:rgba(59,130,246,.1);color:#3b82f6;border:1px solid rgba(59,130,246,.15); }
    .st-pending { background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.15); }
    .st-declined { background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.15); }
    .st-chargeback { background:rgba(107,114,128,.1);color:#4b5563;border:1px solid rgba(107,114,128,.15); }
    .st-recalled { background:rgba(139,92,246,.1);color:#7c3aed;border:1px solid rgba(139,92,246,.2); }
    /* Search input in filter bar */
    .pipe-search {
        font-size:.72rem; font-weight:600; padding:.32rem .55rem .32rem 1.8rem;
        border-radius:22px; border:1px solid rgba(0,0,0,.08);
        background:var(--bs-card-bg); color:var(--bs-surface-600);
        outline:none; min-width:160px; transition:border-color .15s;
    }
    .pipe-search:focus { border-color:#d4af37; box-shadow:0 0 0 2px rgba(212,175,55,.12); }
    .pipe-search-wrap { position:relative;display:inline-flex;align-items:center; }
    .pipe-search-wrap i { position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none; }
    /* Add Sale modal styles */
    .sl-modal .modal-content{border-radius:20px;overflow:hidden;border:none;box-shadow:0 12px 48px rgba(0,0,0,.18)}
    .sl-modal .modal-header{background:linear-gradient(135deg,#22c55e 0%,#16a34a 50%,#22c55e 100%);background-size:200% 200%;animation:shimmerGreen 3s ease infinite;padding:1rem 1.5rem}
    @keyframes shimmerGreen{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
    .sl-modal .modal-header .modal-title{color:#fff;font-size:1.05rem;font-weight:700;display:flex;align-items:center;gap:.5rem}
    .sl-modal .modal-header .btn-close{filter:brightness(0) invert(1);opacity:.8}
    .sl-modal .modal-body{padding:1.25rem}
    .sl-modal .modal-footer{padding:.75rem 1.25rem;border-top:1px solid rgba(0,0,0,.06);gap:.5rem;background:var(--bs-tertiary-bg,#fafafa)}
    .sl-modal .form-control,.sl-modal .form-select{border-radius:12px;font-size:.82rem;border:1.5px solid rgba(0,0,0,.08);transition:all .25s;padding:.45rem .75rem}
    .sl-modal .form-control:focus,.sl-modal .form-select:focus{border-color:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.12)}
    .sl-modal .form-label{font-size:.78rem;margin-bottom:.3rem;font-weight:600;color:var(--bs-heading-color)}
    .ph-field{background:var(--bs-tertiary-bg,#f8f9fa);border-radius:14px;padding:.75rem .85rem;border:1.5px solid transparent;transition:all .25s}
    .ph-field:hover{border-color:rgba(34,197,94,.15);background:rgba(34,197,94,.02)}
    .ph-field:focus-within{border-color:rgba(34,197,94,.3);box-shadow:0 2px 12px rgba(34,197,94,.06)}
    .ph-field label{font-size:.72rem!important;margin-bottom:.2rem!important;color:var(--bs-secondary-color)!important;font-weight:500!important}
    .ph-section{display:flex;align-items:center;gap:.5rem;padding:.55rem .85rem;border-radius:12px;background:linear-gradient(135deg,rgba(34,197,94,.06),rgba(34,197,94,.02));border:1px solid rgba(34,197,94,.1)}
    .ph-section i{color:#22c55e;font-size:1.1rem}
    .ph-section span{font-size:.85rem;font-weight:700;color:#16a34a}
    .ph-add-btn{border:1.5px dashed rgba(34,197,94,.3);background:transparent;border-radius:10px;padding:.35rem .8rem;font-size:.75rem;font-weight:600;color:#16a34a;cursor:pointer;transition:all .2s}
    .ph-add-btn:hover{background:rgba(34,197,94,.04);border-color:#22c55e}
    .mf-btn{border:none;border-radius:12px;padding:.45rem 1rem;font-size:.78rem;font-weight:700;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.35rem;letter-spacing:.2px}
    .mf-btn:hover{transform:translateY(-1px)}
    .mf-end{background:var(--bs-body-color,#323a46);color:#fff}
    .mf-end:hover{color:#fff}
    .mf-submit{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;box-shadow:0 2px 10px rgba(34,197,94,.2)}
    .mf-submit:hover{box-shadow:0 4px 16px rgba(34,197,94,.3);color:#fff}
</style>
@endsection

@section('content')
    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('ravens.dashboard') }}" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="{{ route('ravens.dashboard', ['filter' => 'today']) }}" class="pipe-pill {{ ($filter ?? 'today') === 'today' ? 'active' : '' }}"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill {{ ($filter ?? '') === 'custom' ? 'active' : '' }}" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:{{ ($filter ?? '') === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <div class="pipe-search-wrap">
            <i class="bx bx-search"></i>
            <input type="text" name="search" class="pipe-search" value="{{ $search ?? '' }}" placeholder="Search name, phone…">
        </div>
        @if(($filter ?? 'today') !== 'today' || !empty($search))
            <a href="{{ route('ravens.dashboard', ['filter' => 'today']) }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        @endif
        {{-- Add Sale button (right-aligned via margin-left:auto) --}}
        <button type="button" id="addSaleBtn"
            onclick="openAddSaleModal()"
            style="margin-left:auto;border:none;border-radius:22px;padding:.4rem 1.1rem;font-size:.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;box-shadow:0 2px 10px rgba(34,197,94,.2);transition:all .2s;">
            <i class="bx bx-plus-circle"></i> Add Sale
        </button>
    </form>

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-phone k-icon"></i>
            <div class="k-val">{{ $stats['dialed'] ?? 0 }}</div>
            <div class="k-lbl">Dialed</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-phone-call k-icon"></i>
            <div class="k-val">{{ $stats['calls_connected'] ?? 0 }}</div>
            <div class="k-lbl">Connected</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val">{{ $stats['sales'] ?? 0 }}</div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-gold ex-card">
            <i class="bx bx-trophy k-icon"></i>
            <div class="k-val">{{ $stats['mtd_sales'] ?? 0 }}</div>
            <div class="k-lbl">MTD Sales</div>
        </div>
    </div>

    {{-- My Sales Records --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#1a8754;">
            <i class="bx bx-dollar-circle" style="color:#34c38f;"></i> My Sales Records
            <span class="badge-count">{{ $mySales->total() ?? 0 }}</span>
        </div>

        @if(isset($mySales) && $mySales->count() > 0)
            {{-- Status Summary Chips (counts across full date range, not just current page) --}}
            <div style="display:flex;gap:.4rem;flex-wrap:wrap;padding:.3rem .65rem .5rem;">
                <span class="st-pill st-accepted"><i class="bx bx-check"></i> Approved: {{ $mySalesCounts->approved ?? 0 }}</span>
                <span class="st-pill st-underwritten"><i class="bx bx-edit"></i> Underwriting: {{ $mySalesCounts->underwriting ?? 0 }}</span>
                <span class="st-pill st-pending"><i class="bx bx-time"></i> Pending: {{ $mySalesCounts->pending ?? 0 }}</span>
                <span class="st-pill st-declined"><i class="bx bx-x"></i> Declined: {{ $mySalesCounts->declined ?? 0 }}</span>
                <span class="st-pill st-recalled"><i class="bx bx-undo"></i> Recalled: {{ $mySalesCounts->recalled ?? 0 }}</span>
            </div>

            <div class="scroll-tbl" style="max-height:400px;">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Coverage</th>
                            <th class="text-end">Premium</th>
                            <th>Carrier</th>
                            @if(!auth()->user()->hasRole('Ravens Closer'))
                            <th class="text-center">View</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mySales as $index => $sale)
                            <tr>
                                <td>{{ $mySales->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $sale->cn_name ?? 'N/A' }}</strong>
                                    @if($sale->phone_number)
                                        <br><span style="font-size:.62rem;color:var(--bs-surface-400);">{{ $sale->phone_number }}</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">{{ $sale->sale_at ? $sale->sale_at->setTimezone('America/Los_Angeles')->format('M d, h:i A') : ($sale->sale_date ? $sale->sale_date->format('M d, Y') : 'N/A') }}</td>
                                <td class="text-center">
                                    @php
                                        $isRecalled = !is_null($sale->recall_requested_at);
                                        // Show submission_status (set by manager on submission page)
                                        $subStatus = $sale->submission_status ?? 'pending';
                                        $subClassMap = [
                                            'approved'    => 'st-accepted',
                                            'declined'    => 'st-declined',
                                            'underwriting'=> 'st-underwritten',
                                            'pending'     => 'st-pending',
                                        ];
                                        $subLabelMap = [
                                            'approved'    => 'Approved',
                                            'declined'    => 'Declined',
                                            'underwriting'=> 'Underwriting',
                                            'pending'     => 'Pending',
                                        ];
                                        $stClass = $isRecalled ? 'st-recalled' : ($subClassMap[$subStatus] ?? 'st-pending');
                                        $stLabel = $isRecalled ? 'Recalled' : ($subLabelMap[$subStatus] ?? 'Pending');
                                    @endphp
                                    <span class="st-pill {{ $stClass }}">{{ $stLabel }}</span>
                                    @if($sale->qa_status)
                                        <br><span style="font-size:.55rem;color:var(--bs-surface-400);">QA: {{ $sale->qa_status }}</span>
                                    @endif
                                    @if($isRecalled && $sale->recall_note)
                                        <br><span style="font-size:.65rem;color:#7c3aed;font-style:italic;display:inline-block;margin-top:.15rem;max-width:180px;white-space:normal;line-height:1.3;" title="{{ $sale->recall_note }}">
                                            <i class="bx bx-message-rounded-dots" style="font-size:.6rem;"></i> {{ $sale->recall_note }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($sale->coverage_amount)
                                        <strong>${{ number_format($sale->coverage_amount, 0) }}</strong>
                                    @else
                                        <span style="color:var(--bs-surface-400);">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($sale->monthly_premium)
                                        <strong>${{ number_format($sale->monthly_premium, 2) }}</strong>
                                    @else
                                        <span style="color:var(--bs-surface-400);">—</span>
                                    @endif
                                </td>
                                <td>{{ $sale->carrier_name ?? 'N/A' }}</td>
                                @if(!auth()->user()->hasRole('Ravens Closer'))
                                <td class="text-center">
                                    <a href="{{ route('sales.index') }}?search={{ $sale->phone_number }}" class="act-btn a-primary" title="View in Sales"><i class="bx bx-show"></i></a>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .65rem;border-top:1px solid rgba(0,0,0,.04);font-size:.68rem;color:var(--bs-surface-400);">
                <span>Showing {{ $mySales->firstItem() }} to {{ $mySales->lastItem() }} of {{ $mySales->total() }}</span>
                <div>{{ $mySales->links() }}</div>
            </div>
        @else
            <div style="text-align:center;padding:2rem 1rem;color:var(--bs-surface-400);">
                <i class="bx bx-package" style="font-size:2rem;display:block;margin-bottom:.4rem;"></i>
                <p style="font-size:.8rem;font-weight:600;margin-bottom:.3rem;">No sales yet</p>
                <p style="font-size:.72rem;margin-bottom:.6rem;">Start calling leads to make your first sale!</p>
                <a href="{{ route('ravens.calling') }}" class="act-btn a-primary" style="padding:.35rem .75rem;">
                    <i class="bx bx-phone-call"></i> Start Calling
                </a>
            </div>
        @endif
    </div>

    {{-- Declined & Chargebacks --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#c84646;">
            <i class="bx bx-error-circle" style="color:#f46a6a;"></i> Declined & Chargebacks
            <span class="badge-count">{{ $declinedChargebacks->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-center">Status</th>
                        <th>Carrier</th>
                        <th class="text-end">Coverage</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($declinedChargebacks as $lead)
                        <tr>
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->recall_requested_at)
                                    <span class="st-pill st-recalled"><i class="bx bx-undo" style="font-size:.7rem;"></i> Recalled</span>
                                    @if($lead->recall_note)
                                        <br><span style="font-size:.65rem;color:#7c3aed;font-style:italic;display:inline-block;margin-top:.15rem;max-width:180px;white-space:normal;line-height:1.3;" title="{{ $lead->recall_note }}">
                                            <i class="bx bx-message-rounded-dots" style="font-size:.6rem;"></i> {{ $lead->recall_note }}
                                        </span>
                                    @endif
                                @elseif($lead->status === 'chargeback')
                                    <span class="st-pill st-chargeback">Chargeback</span>
                                @else
                                    <span class="st-pill st-declined">Declined</span>
                                @endif
                            </td>
                            <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                            <td class="text-end">
                                @if($lead->coverage_amount)
                                    ${{ number_format($lead->coverage_amount, 0) }}
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">{{ $lead->updated_at->setTimezone('America/Los_Angeles')->format('M d, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-smile"></i> No declined or chargebacks</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- ════ ADD SALE MODAL ════ --}}
{{-- Reuses the same call-form style (sl-modal / ph-field) but creates a brand-new lead --}}
<div class="modal fade sl-modal" id="addSaleModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Add New Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- ── SECTION: Essential Info ── --}}
                <div class="ph-section mb-3"><i class="bx bx-user"></i><span>Customer Information</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_cn_name" placeholder="Customer full name" oninput="this.value=this.value.replace(/[0-9]/g,'')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_phone_number" placeholder="Primary phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Secondary Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_secondary_phone" placeholder="Secondary phone">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ph-field">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ns_dob">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ph-field">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_gender">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">SSN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_ssn" placeholder="XXX-XX-XXXX">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_state">
                                <option value="">Select State</option>
                                @foreach($usStates as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Zip Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_zip" placeholder="Zip code">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_address" placeholder="Street address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_emergency_contact" placeholder="Emergency contact">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ph-field">
                            <label class="form-label">Birth Place <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_birth_place" placeholder="City / State">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ph-field">
                            <label class="form-label">Smoker</label>
                            <select class="form-select" id="ns_smoker">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ph-field">
                            <label class="form-label">Height <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_height" placeholder="5'10&quot;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ph-field">
                            <label class="form-label">Weight <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_weight" placeholder="lbs">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Driving License <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_driving_license">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Medical ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-plus-medical"></i><span>Medical Information</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Medical Issues <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="ns_medical_issue" rows="2" placeholder="Enter medical issues"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Medications <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="ns_medications" rows="2" placeholder="Enter medications"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Doctor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_doctor_name" placeholder="Doctor name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Doctor Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_doctor_number" placeholder="Doctor phone">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Doctor Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_doctor_address" placeholder="Doctor address">
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Policy ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-shield-quarter"></i><span>Policy Information</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <div class="ph-field">
                            <label class="form-label">Beneficiaries</label>
                            <div id="ns_beneficiaries_container"></div>
                            <button type="button" class="ph-add-btn mt-2" onclick="nsAddBeneficiary()">
                                <i class="bx bx-plus"></i> Add Beneficiary
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Policy Carrier <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_policy_carrier" data-carrier-partner-info='@json($carrierPartnerData)'>
                                <option value="">Select Carrier</option>
                                @foreach($carrierPartnerData as $cp)
                                    <option value="{{ $cp['carrier_id'] }}_{{ $cp['partner_id'] }}"
                                            data-carrier-name="{{ $cp['carrier_name'] }}"
                                            data-carrier-id="{{ $cp['carrier_id'] }}"
                                            data-partner-id="{{ $cp['partner_id'] }}"
                                            data-partner-name="{{ $cp['partner_name'] }}"
                                            data-states='@json($cp['states'])'>
                                        {{ $cp['display_name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Approved State <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_approved_state">
                                <option value="">Select Carrier First</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="ph-field">
                            <label class="form-label">Assigned Partner</label>
                            <input type="text" class="form-control" id="ns_assigned_partner" placeholder="Auto-filled from carrier selection" readonly>
                            <input type="hidden" id="ns_partner_id">
                            <input type="hidden" id="ns_insurance_carrier_id">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Policy Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="ns_policy_type">
                                <option value="">— Select Plan —</option>
                                <option value="G.I">G.I</option>
                                <option value="Graded">Graded</option>
                                <option value="Level">Level</option>
                                <option value="Modified">Modified</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Policy Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_policy_number" placeholder="Policy number">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Coverage Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="ns_coverage" step="0.01" placeholder="Amount">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Monthly Premium <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="ns_premium" step="0.01" placeholder="Amount">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Initial Draft Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ns_initial_draft_date">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Future Draft Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ns_future_draft_date">
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Banking ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-building-house"></i><span>Banking Information</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_bank_name" placeholder="Bank name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Account Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_account_title" placeholder="Account title">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_account_type">
                                <option value="">Select</option>
                                <option value="Checking">Checking</option>
                                <option value="Savings">Savings</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Routing Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_routing_number" placeholder="Routing number">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_account_number" placeholder="Account number">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Verified By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_account_verified_by" placeholder="Verifier name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Bank Balance <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="ns_bank_balance" step="0.01" placeholder="Balance">
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Card ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-credit-card"></i><span>Card Information</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Card Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_card_number" placeholder="Card number">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">CVV <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_cvv" placeholder="CVV" maxlength="4">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ph-field">
                            <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_expiry_date" placeholder="MM/YY">
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Additional ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-info-circle"></i><span>Additional Information</span></div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Source <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ns_source" placeholder="Lead source">
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Follow Up ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-calendar"></i><span>Follow Up Schedule</span></div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="ph-field">
                            <label class="form-label">Follow Up Required <span class="text-danger">*</span></label>
                            <select class="form-select" id="ns_followup_required" required>
                                <option value="">Select option…</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-none" id="ns_followup_datetime_wrap">
                        <div class="ph-field">
                            <label class="form-label">Follow Up Date &amp; Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ns_followup_scheduled_at">
                            <small class="text-muted" style="font-size:.7rem">When should the follow-up call be scheduled?</small>
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: Q&A ── --}}
                <div class="ph-section mb-3 mt-2"><i class="bx bx-chat"></i><span>Q &amp; A</span></div>
                <div class="row g-2">
                    <div class="col-12">
                        <div id="ns_qna_container"></div>
                        <button type="button" class="ph-add-btn mt-2" onclick="nsAddQna()">
                            <i class="bx bx-plus"></i> Add Question
                        </button>
                    </div>
                </div>

            </div>{{-- /modal-body --}}
            <div class="modal-footer">
                <button type="button" class="mf-btn mf-end" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                <button type="button" class="mf-btn mf-submit" onclick="nsSubmitSale()"><i class="bx bx-check-circle"></i> Submit Sale</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@include('partials.sl-filter-assets')
<script src="{{ URL::asset('build/libs/toastr/build/toastr.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('build/libs/toastr/build/toastr.min.css') }}" />
<script>
    // Submit search on Enter key
    document.querySelector('.pipe-search')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('filterForm').submit(); }
    });

    // ─── Add Sale Modal ───────────────────────────────────────────────
    const nsAllStates = @json($usStates);

    // Clear is-invalid styling as user fills fields
    document.getElementById('addSaleModal').addEventListener('input', function(e) {
        if (e.target.value && e.target.value.trim()) e.target.classList.remove('is-invalid');
    });
    document.getElementById('addSaleModal').addEventListener('change', function(e) {
        if (e.target.value && e.target.value.trim()) e.target.classList.remove('is-invalid');
    });

    function openAddSaleModal() {
        // Clear all fields
        const ids = [
            'ns_cn_name','ns_phone_number','ns_secondary_phone','ns_dob','ns_ssn',
            'ns_zip','ns_address','ns_emergency_contact','ns_birth_place',
            'ns_height','ns_weight','ns_medical_issue','ns_medications',
            'ns_doctor_name','ns_doctor_number','ns_doctor_address',
            'ns_policy_type','ns_policy_number','ns_coverage','ns_premium',
            'ns_initial_draft_date','ns_future_draft_date',
            'ns_bank_name','ns_account_title','ns_routing_number','ns_account_number',
            'ns_account_verified_by','ns_bank_balance',
            'ns_card_number','ns_cvv','ns_expiry_date','ns_source',
            'ns_assigned_partner','ns_partner_id','ns_insurance_carrier_id',
            'ns_followup_scheduled_at',
        ];
        ids.forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
        const selects = ['ns_gender','ns_state','ns_smoker','ns_driving_license','ns_policy_carrier','ns_approved_state','ns_account_type','ns_followup_required'];
        selects.forEach(id => { const el = document.getElementById(id); if (el) el.selectedIndex = 0; });
        // Hide followup datetime
        const followWrap = document.getElementById('ns_followup_datetime_wrap');
        if (followWrap) followWrap.classList.add('d-none');
        document.getElementById('ns_beneficiaries_container').innerHTML = '';
        document.getElementById('ns_qna_container').innerHTML = '';
        // Add one blank beneficiary row
        nsAddBeneficiary();

        bootstrap.Modal.getOrCreateInstance(document.getElementById('addSaleModal')).show();
    }

    // Carrier → partner + state filtering
    document.addEventListener('DOMContentLoaded', function () {
        const carrierSel  = document.getElementById('ns_policy_carrier');
        const stateSel    = document.getElementById('ns_approved_state');
        const partnerIn   = document.getElementById('ns_assigned_partner');
        const partnerIdIn = document.getElementById('ns_partner_id');
        const carrierIdIn = document.getElementById('ns_insurance_carrier_id');

        if (carrierSel) {
            carrierSel.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                stateSel.innerHTML = '<option value="">Select State</option>';
                partnerIn.value   = '';
                partnerIdIn.value = '';
                carrierIdIn.value = '';

                if (this.value) {
                    const approvedStates = JSON.parse(opt.dataset.states || '[]');
                    partnerIn.value   = opt.dataset.partnerName  || '';
                    partnerIdIn.value = opt.dataset.partnerId    || '';
                    carrierIdIn.value = opt.dataset.carrierId    || '';

                    // Rebuild plan type dropdown for selected carrier
                    window.updatePlanTypeField(
                        opt.dataset.carrierId || opt.textContent.trim(),
                        document.getElementById('ns_policy_type')
                    );

                    approvedStates.forEach(code => {
                        if (nsAllStates[code]) {
                            const o = document.createElement('option');
                            o.value = code;
                            o.textContent = nsAllStates[code];
                            stateSel.appendChild(o);
                        }
                    });
                    if (approvedStates.length === 0) {
                        stateSel.innerHTML = '<option value="">No approved states</option>';
                    }
                } else {
                    stateSel.innerHTML = '<option value="">Select Carrier First</option>';
                    // Reset plan types to defaults when no carrier selected
                    window.updatePlanTypeField(null, document.getElementById('ns_policy_type'));
                }
            });
        }
    });

    // Beneficiary management
    function nsAddBeneficiary() {
        const container = document.getElementById('ns_beneficiaries_container');
        const row = document.createElement('div');
        row.className = 'beneficiary-ns-row row mb-2 g-2';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm ns-ben-name" placeholder="Beneficiary Name">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control form-control-sm ns-ben-dob">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm ns-ben-relation">
                    <option value="">Relation</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Child">Child</option>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Grandchild">Grandchild</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-ns-row').remove()">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    // Q&A management
    function nsAddQna() {
        const container = document.getElementById('ns_qna_container');
        const row = document.createElement('div');
        row.className = 'ns-qna-row row mb-2 g-2';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm ns-qna-question" placeholder="Question">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control form-control-sm ns-qna-answer" placeholder="Answer">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.ns-qna-row').remove()">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        row.querySelector('.ns-qna-question').focus();
    }

    // Followup toggle
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'ns_followup_required') {
            const wrap = document.getElementById('ns_followup_datetime_wrap');
            const input = document.getElementById('ns_followup_scheduled_at');
            if (e.target.value === '1') {
                wrap.classList.remove('d-none');
                input.setAttribute('required', 'required');
            } else {
                wrap.classList.add('d-none');
                input.removeAttribute('required');
                input.value = '';
            }
        }
    });

    // Submit new sale
    function nsSubmitSale() {
        const name  = document.getElementById('ns_cn_name').value.trim();
        const phone = document.getElementById('ns_phone_number').value.trim();

        // ── Validate all required fields ────────────────────────────────────
        const required = [
            { id: 'ns_cn_name',             label: 'Full Name' },
            { id: 'ns_phone_number',         label: 'Phone Number' },
            { id: 'ns_secondary_phone',      label: 'Secondary Phone' },
            { id: 'ns_dob',                  label: 'Date of Birth' },
            { id: 'ns_gender',               label: 'Gender' },
            { id: 'ns_ssn',                  label: 'SSN' },
            { id: 'ns_state',                label: 'State' },
            { id: 'ns_zip',                  label: 'Zip Code' },
            { id: 'ns_address',              label: 'Address' },
            { id: 'ns_emergency_contact',    label: 'Emergency Contact' },
            { id: 'ns_birth_place',          label: 'Birth Place' },
            { id: 'ns_height',               label: 'Height' },
            { id: 'ns_weight',               label: 'Weight' },
            { id: 'ns_driving_license',      label: 'Driving License' },
            { id: 'ns_medical_issue',        label: 'Medical Issues' },
            { id: 'ns_medications',          label: 'Medications' },
            { id: 'ns_doctor_name',          label: 'Doctor Name' },
            { id: 'ns_doctor_number',        label: 'Doctor Phone' },
            { id: 'ns_doctor_address',       label: 'Doctor Address' },
            { id: 'ns_policy_carrier',       label: 'Policy Carrier' },
            { id: 'ns_approved_state',       label: 'Approved State' },
            { id: 'ns_policy_type',          label: 'Policy Type' },
            { id: 'ns_policy_number',        label: 'Policy Number' },
            { id: 'ns_coverage',             label: 'Coverage Amount' },
            { id: 'ns_premium',              label: 'Monthly Premium' },
            { id: 'ns_initial_draft_date',   label: 'Initial Draft Date' },
            { id: 'ns_future_draft_date',    label: 'Future Draft Date' },
            { id: 'ns_bank_name',            label: 'Bank Name' },
            { id: 'ns_account_title',        label: 'Account Title' },
            { id: 'ns_account_type',         label: 'Account Type' },
            { id: 'ns_routing_number',       label: 'Routing Number' },
            { id: 'ns_account_number',       label: 'Account Number' },
            { id: 'ns_account_verified_by',  label: 'Verified By' },
            { id: 'ns_bank_balance',         label: 'Bank Balance' },
            { id: 'ns_card_number',          label: 'Card Number' },
            { id: 'ns_cvv',                  label: 'CVV' },
            { id: 'ns_expiry_date',          label: 'Expiry Date' },
            { id: 'ns_source',               label: 'Source' },
            { id: 'ns_followup_required',    label: 'Follow Up Required' },
        ];

        let missing = [];
        required.forEach(f => {
            const el = document.getElementById(f.id);
            if (!el) return;
            const val = el.value.trim ? el.value.trim() : el.value;
            if (!val) {
                el.classList.add('is-invalid');
                missing.push(f.label);
            } else {
                el.classList.remove('is-invalid');
            }
        });

        // Name must not contain digits
        if (name && /\d/.test(name)) {
            document.getElementById('ns_cn_name').classList.add('is-invalid');
            if (!missing.includes('Full Name')) missing.push('Full Name (no numbers allowed)');
        }

        if (missing.length > 0) {
            toastr ? toastr.error('Please fill in: ' + missing.slice(0, 5).join(', ') + (missing.length > 5 ? ` (+${missing.length - 5} more)` : ''))
                   : alert('Please fill in: ' + missing.join(', '));
            // Scroll to first invalid field
            const firstInvalid = document.querySelector('#addSaleModal .is-invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Followup datetime required when followup = Yes
        const followupRequired = document.getElementById('ns_followup_required').value;
        if (followupRequired === '1') {
            const followupAt = document.getElementById('ns_followup_scheduled_at').value;
            if (!followupAt) {
                document.getElementById('ns_followup_scheduled_at').classList.add('is-invalid');
                toastr ? toastr.error('Please set a follow-up date & time.') : alert('Please set a follow-up date & time.');
                document.getElementById('ns_followup_scheduled_at').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
        }

        if (!confirm('Are you sure you want to submit this sale? This will be stored in leads and notified to QA and Managers.')) {
            return;
        }

        // Collect beneficiaries
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-ns-row').forEach(row => {
            const n = row.querySelector('.ns-ben-name')?.value?.trim();
            const d = row.querySelector('.ns-ben-dob')?.value;
            const r = row.querySelector('.ns-ben-relation')?.value;
            if (n) beneficiaries.push({ name: n, dob: d || null, relation: r || null });
        });

        // Collect Q&A pairs
        const qna = [];
        document.querySelectorAll('.ns-qna-row').forEach(row => {
            const q = row.querySelector('.ns-qna-question')?.value?.trim();
            const a = row.querySelector('.ns-qna-answer')?.value?.trim();
            if (q) qna.push({ question: q, answer: a || '' });
        });

        const payload = {
            cn_name:               document.getElementById('ns_cn_name').value || null,
            phone_number:          document.getElementById('ns_phone_number').value || null,
            secondary_phone_number:document.getElementById('ns_secondary_phone').value || null,
            date_of_birth:         document.getElementById('ns_dob').value || null,
            ssn:                   document.getElementById('ns_ssn').value || null,
            gender:                document.getElementById('ns_gender').value || null,
            state:                 document.getElementById('ns_approved_state').value || document.getElementById('ns_state').value || null,
            zip_code:              document.getElementById('ns_zip').value || null,
            address:               document.getElementById('ns_address').value || null,
            emergency_contact:     document.getElementById('ns_emergency_contact').value || null,
            birth_place:           document.getElementById('ns_birth_place').value || null,
            height:                document.getElementById('ns_height').value || null,
            weight:                document.getElementById('ns_weight').value || null,
            smoker:                document.getElementById('ns_smoker').value || null,
            driving_license:       document.getElementById('ns_driving_license').value || null,
            medical_issue:         document.getElementById('ns_medical_issue').value || null,
            medications:           document.getElementById('ns_medications').value || null,
            doctor_name:           document.getElementById('ns_doctor_name').value || null,
            doctor_number:         document.getElementById('ns_doctor_number').value || null,
            doctor_address:        document.getElementById('ns_doctor_address').value || null,
            beneficiaries:         beneficiaries,
            policy_type:           document.getElementById('ns_policy_type').value || null,
            policy_number:         document.getElementById('ns_policy_number').value || null,
            carrier_name:          document.getElementById('ns_policy_carrier').options[document.getElementById('ns_policy_carrier').selectedIndex]?.dataset?.carrierName || null,
            insurance_carrier_id:  document.getElementById('ns_insurance_carrier_id').value || null,
            coverage_amount:       document.getElementById('ns_coverage').value || null,
            monthly_premium:       document.getElementById('ns_premium').value || null,
            initial_draft_date:    document.getElementById('ns_initial_draft_date').value || null,
            future_draft_date:     document.getElementById('ns_future_draft_date').value || null,
            bank_name:             document.getElementById('ns_bank_name').value || null,
            account_title:         document.getElementById('ns_account_title').value || null,
            account_type:          document.getElementById('ns_account_type').value || null,
            routing_number:        document.getElementById('ns_routing_number').value || null,
            account_number:        document.getElementById('ns_account_number').value || null,
            account_verified_by:   document.getElementById('ns_account_verified_by').value || null,
            bank_balance:          document.getElementById('ns_bank_balance').value || null,
            card_number:           document.getElementById('ns_card_number').value || null,
            cvv:                   document.getElementById('ns_cvv').value || null,
            expiry_date:           document.getElementById('ns_expiry_date').value || null,
            source:                document.getElementById('ns_source').value || null,
            assigned_partner:      document.getElementById('ns_assigned_partner').value || null,
            partner_id:            document.getElementById('ns_partner_id').value || null,
            followup_required:     document.getElementById('ns_followup_required').value !== '' ? document.getElementById('ns_followup_required').value : null,
            followup_scheduled_at: document.getElementById('ns_followup_scheduled_at').value || null,
            closer_qna:            qna.length > 0 ? qna : null,
        };

        const btn = document.querySelector('#addSaleModal .mf-submit');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Submitting…'; }

        fetch('{{ route('ravens.leads.create-sale') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || 'Sale added successfully!');
                    if (data.is_repeat_sale) {
                        toastr.warning(data.repeat_sale_message, 'Repeat Sale Detected', { timeOut: 10000 });
                    }
                } else {
                    alert(data.message || 'Sale added successfully!');
                }
                bootstrap.Modal.getInstance(document.getElementById('addSaleModal'))?.hide();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.message || 'Failed to add sale.');
                } else {
                    alert(data.message || 'Failed to add sale.');
                }
            }
        })
        .catch(err => {
            console.error(err);
            if (typeof toastr !== 'undefined') {
                toastr.error('An error occurred. Please try again.');
            } else {
                alert('An error occurred. Please try again.');
            }
        })
        .finally(() => {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-check-circle"></i> Submit Sale'; }
        });
    }
</script>
@endsection
