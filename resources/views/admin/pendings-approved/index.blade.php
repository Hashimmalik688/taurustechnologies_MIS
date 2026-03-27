@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Pending Submission')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Pending Submission — MIS Style
   ═══════════════════════════════════════════════════ */

/* ── KPI Cards ── */
.kpi-row { display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem; }
.kpi-card {
    flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;
    position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);
    transition:transform .15s,box-shadow .15s;background:var(--bs-card-bg);
    box-shadow:0 1px 4px rgba(0,0,0,.05);
}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.kpi-card .k-icon{font-size:1rem;margin-bottom:.2rem;display:block;opacity:.7;}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1;}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val,.kpi-card.k-gold .k-icon{color:#b89730}
.kpi-card.k-green{background:rgba(52,195,143,.06)}.kpi-card.k-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}.kpi-card.k-green .k-val,.kpi-card.k-green .k-icon{color:#1a8754}
.kpi-card.k-warn{background:rgba(241,180,76,.06)}.kpi-card.k-warn::before{background:linear-gradient(90deg,#f1b44c,#f5cd7e)}.kpi-card.k-warn .k-val,.kpi-card.k-warn .k-icon{color:#b87a14}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val,.kpi-card.k-blue .k-icon{color:#556ee6}
.kpi-card.k-red{background:rgba(244,106,106,.06)}.kpi-card.k-red::before{background:linear-gradient(90deg,#f46a6a,#f7908f)}.kpi-card.k-red .k-val,.kpi-card.k-red .k-icon{color:#c84646}
.kpi-card.k-purple{background:rgba(111,66,193,.06)}.kpi-card.k-purple::before{background:linear-gradient(90deg,#6f42c1,#9b7ed8)}.kpi-card.k-purple .k-val,.kpi-card.k-purple .k-icon{color:#6f42c1}

/* Clickable KPI */
a.kpi-link{text-decoration:none;color:inherit;display:contents;}
.kpi-card{cursor:pointer;}
.kpi-card.active{box-shadow:0 0 0 2px var(--bs-gold,#d4af37),0 4px 12px rgba(0,0,0,.1);transform:translateY(-2px);}

/* ── Section Card ── */
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;display:flex;align-items:center;gap:.3rem;}
.sec-hdr h6 i{opacity:.6;font-size:.95rem;}

/* ── Table ── */
.ex-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.735rem;min-width:1100px;}
.ex-tbl thead th{text-transform:uppercase;font-size:.62rem;font-weight:700;letter-spacing:.5px;color:var(--bs-surface-500);padding:.45rem .6rem;border-bottom:1px solid var(--bs-surface-200,rgba(0,0,0,.07));white-space:nowrap;background:var(--bs-surface-100,transparent);position:sticky;top:0;z-index:1;}
.ex-tbl tbody td{padding:.45rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.03);white-space:nowrap;}
.ex-tbl tbody tr{transition:background .12s;}
.ex-tbl tbody tr:hover{background:rgba(212,175,55,.03);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}

/* ── Badge styles ── */
.bd-mini{font-size:.6rem;font-weight:700;padding:.15rem .4rem;border-radius:.25rem;display:inline-block;min-width:22px;text-align:center;}
.bd-mini.bd-green{background:rgba(52,195,143,.12);color:#1a8754;} .bd-mini.bd-warn{background:rgba(241,180,76,.12);color:#b87a14;}
.bd-mini.bd-red{background:rgba(244,106,106,.12);color:#c84646;} .bd-mini.bd-blue{background:rgba(85,110,230,.12);color:#556ee6;}
.bd-mini.bd-gray{background:rgba(108,117,125,.12);color:#6c757d;}

.bd-ni{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-resolved{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-pending{background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}

/* ── Action Buttons ── */
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.a-send{background:rgba(52,195,143,.08);color:#1a8754;border-color:rgba(52,195,143,.25);}.a-send:hover{background:rgba(52,195,143,.18);}
.a-edit{background:rgba(212,175,55,.08);color:#b89730;border-color:rgba(212,175,55,.25);}.a-edit:hover{background:rgba(212,175,55,.18);}
.a-ni{background:rgba(244,106,106,.08);color:#c84646;border-color:rgba(244,106,106,.25);}.a-ni:hover{background:rgba(244,106,106,.18);}
.a-resolve{background:rgba(85,110,230,.08);color:#556ee6;border-color:rgba(85,110,230,.25);}.a-resolve:hover{background:rgba(85,110,230,.18);}
.a-recall{background:rgba(139,92,246,.08);color:#7c3aed;border-color:rgba(139,92,246,.25);}.a-recall:hover{background:rgba(139,92,246,.18);}

/* ── Filter bar ── */
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;border-radius:1rem;border:1px solid rgba(0,0,0,.08);}
.filter-form .form-control:focus,.filter-form .form-select:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.1);}
.filter-form label{font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}.f-reset:hover{color:var(--bs-body-color);}

/* ── Filter Pill Buttons (like Sales page) ── */
.sl-pill-today,.sl-pill-week,.sl-pill-month {
    background: transparent; border: 1px solid rgba(14,165,233,.3); color: #0ea5e9;
    padding: .25rem .6rem; border-radius: 999px; font-size: .7rem; font-weight: 500;
    cursor: pointer; transition: all .15s;
}
.sl-pill-today:hover,.sl-pill-week:hover,.sl-pill-month:hover { background: rgba(14,165,233,.1); border-color: #0ea5e9; }
.sl-pill-label { font-size: .6rem; text-transform: uppercase; color: var(--bs-surface-400); letter-spacing: .3px; font-weight: 600; }
.sl-pill-date { width: 120px; font-size: .72rem; padding: .25rem .5rem; border-radius: 1rem; border: 1px solid rgba(0,0,0,.08); }
.sl-pill-clear {
    font-size: .68rem; color: #f46a6a; text-decoration: none; display: inline-flex; align-items: center; gap: .2rem;
    padding: .25rem .5rem; border-radius: 1rem; border: 1px solid rgba(244,106,106,.2);
}
.sl-pill-clear:hover { background: rgba(244,106,106,.08); color: #c84646; }

/* ── Scrollable table ── */
.scroll-tbl{overflow-x:auto;overflow-y:auto;max-height:600px;}
.scroll-tbl::-webkit-scrollbar{width:3px;height:3px;}
.scroll-tbl::-webkit-scrollbar-thumb{background:var(--bs-surface-300);border-radius:3px;}

/* ── Modal ── */
.sub-modal .modal-content{border-radius:.6rem;border:1px solid rgba(255,255,255,.08);overflow:hidden;background:var(--bs-card-bg);box-shadow:0 8px 30px rgba(0,0,0,.15);}
.sub-modal .modal-header{background:var(--bs-card-bg);padding:.65rem .85rem;border-bottom:1px solid rgba(0,0,0,.06);}
.sub-modal .modal-header .modal-title{font-size:.85rem;font-weight:600;display:flex;align-items:center;gap:.35rem;}
.sub-modal .modal-header .modal-title i{color:var(--bs-gold,#d4af37);opacity:.7;font-size:1rem;}
.sub-modal .modal-body{padding:.85rem;}
.sub-modal .modal-body .form-label{font-size:.72rem;font-weight:600;margin-bottom:.3rem;}
.sub-modal .modal-body .form-control,.sub-modal .modal-body .form-select{font-size:.78rem;border-radius:.4rem;padding:.4rem .6rem;}
.sub-modal .modal-body .form-control:focus,.sub-modal .modal-body .form-select:focus{border-color:var(--bs-gold,#d4af37);box-shadow:0 0 0 2px rgba(212,175,55,.12);}
.sub-modal .modal-footer{border-top:1px solid rgba(0,0,0,.05);padding:.55rem .85rem;}

/* Pagination */
.sec-card .pagination{margin:0;}
.sec-card .pagination .page-link{border-radius:.35rem;margin:0 1px;font-size:.7rem;border:1px solid var(--bs-surface-200);color:var(--bs-surface-500);padding:.2rem .5rem;}
.sec-card .pagination .page-item.active .page-link{background:var(--bs-gold,#d4af37);border-color:var(--bs-gold);color:#fff;}
.sec-card .pagination svg{max-width:14px!important;max-height:14px!important;}
/* ── Prominent page title ── */
.sl-page-title{font-size:1.35rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px;margin:0;}
.sl-page-title i{color:#d4af37;font-size:1.5rem;}
.sl-page-subtitle{font-size:.78rem;color:#94a3b8;margin:0;}
[data-bs-theme=dark] .sl-page-title,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
/* Coverage edit */
.a-covr{background:rgba(16,185,129,.08);color:#059669;border-color:rgba(16,185,129,.25);padding:.18rem .35rem!important;}
.a-covr:hover{background:rgba(16,185,129,.18);}
</style>
@endsection

@section('content')
<div class="container-fluid" style="max-width:1600px">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="sl-page-title"><i class="bx bx-check-circle"></i> Pending Submission</h1>
            <p class="sl-page-subtitle mt-1">Assign details and send validated leads to Pending Contracts</p>
        </div>
        <div class="d-flex gap-1 align-items-center">
            <a href="{{ route('issuance.index') }}" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.08);font-size:.7rem;">
                <i class="bx bx-right-arrow-alt"></i> Pending Contracts
            </a>
        </div>
    </div>

    {{-- KPI Cards (clickable) --}}
    <div class="kpi-row">
        <a href="{{ route('submissions.index', array_merge(request()->only(['search','carrier','date_from','date_to']), ['status' => 'pending'])) }}" class="kpi-link">
            <div class="kpi-card k-warn {{ $status === 'pending' ? 'active' : '' }}">
                <i class="bx bx-timer k-icon"></i>
                <div class="k-val">{{ $pendingCount }}</div>
                <div class="k-lbl">Pending Approval</div>
            </div>
        </a>
        <a href="{{ route('submissions.index', array_merge(request()->only(['search','carrier','date_from','date_to']), ['status' => 'approved'])) }}" class="kpi-link">
            <div class="kpi-card k-green {{ $status === 'approved' ? 'active' : '' }}">
                <i class="bx bx-check-circle k-icon"></i>
                <div class="k-val">{{ $approvedCount }}</div>
                <div class="k-lbl">Approved</div>
            </div>
        </a>
        <a href="{{ route('submissions.index', array_merge(request()->only(['search','carrier','date_from','date_to']), ['status' => 'declined'])) }}" class="kpi-link">
            <div class="kpi-card k-red {{ $status === 'declined' ? 'active' : '' }}">
                <i class="bx bx-x-circle k-icon"></i>
                <div class="k-val">{{ $declinedCount }}</div>
                <div class="k-lbl">Declined</div>
            </div>
        </a>
        <a href="{{ route('submissions.index', array_merge(request()->only(['search','carrier','date_from','date_to']), ['status' => 'underwriting'])) }}" class="kpi-link">
            <div class="kpi-card k-purple {{ $status === 'underwriting' ? 'active' : '' }}">
                <i class="bx bx-file k-icon"></i>
                <div class="k-val">{{ $underwritingCount }}</div>
                <div class="k-lbl">Underwriting</div>
            </div>
        </a>
    </div>

    {{-- Main Table Card --}}
    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-check"></i> Validated Leads</h6>
            <span style="font-size:.62rem;color:var(--bs-surface-400);">{{ $leads->total() }} records</span>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('submissions.index') }}" class="filter-form" id="submissionsFilterForm">
            <input type="hidden" name="status" value="{{ $status }}">
            <div>
                <label>Search</label>
                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Name, phone, carrier…" style="width:160px;">
            </div>
            <div>
                <label>Carrier</label>
                <select name="carrier" class="form-select" style="width:130px;">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ $carrier == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="sl-pill-label">FROM</span>
                <input type="date" name="date_from" id="filter_date_from" class="sl-pill-date" value="{{ $dateFrom }}" onchange="this.form.submit()">
                <span class="sl-pill-label">TO</span>
                <input type="date" name="date_to" id="filter_date_to" class="sl-pill-date" value="{{ $dateTo }}" onchange="this.form.submit()">
                <button type="button" class="sl-pill-today" onclick="setTodayFilter()" title="Show today's submissions">Today</button>
                <button type="button" class="sl-pill-week" onclick="setThisWeekFilter()" title="Show this week's submissions">This Week</button>
                <button type="button" class="sl-pill-month" onclick="setThisMonthFilter()" title="Show this month's submissions">This Month</button>
            </div>
            @if(request()->hasAny(['search','carrier','date_from','date_to']))
                <a href="{{ route('submissions.index', ['status' => $status]) }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Closer</th>
                        <th>Sale Date</th>
                        <th>App ID</th>
                        <th>Status</th>
                        <th>Reviewed By</th>
                        <th>Reviewed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td style="color:var(--bs-surface-400);">{{ $loop->iteration + (($leads->currentPage() - 1) * $leads->perPage()) }}</td>
                            <td>
                                <a href="{{ route('issuance.show', $lead->id) }}" style="font-weight:600;font-size:.73rem;color:var(--bs-body-color);text-decoration:none;">
                                    {{ $lead->cn_name ?? '—' }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @if($lead->closer_name)
                                        <span class="bd-mini bd-blue">{{ $lead->closer_name }}</span>
                                    @else
                                        <span style="color:#94a3b8;font-size:.72rem;">—</span>
                                    @endif
                                    <button class="a-btn a-covr btn-edit-coverage"
                                        data-id="{{ $lead->id }}"
                                        data-name="{{ $lead->cn_name }}"
                                        data-coverage="{{ $lead->coverage_amount ?? '' }}"
                                        data-premium="{{ $lead->monthly_premium ?? '' }}"
                                        data-policytype="{{ $lead->policy_type ?? '' }}"
                                        data-carrier="{{ $lead->insurance_carrier_id ?? '' }}"
                                        title="Edit Coverage / Premium / Plan">
                                        <i class="bx bx-edit-alt" style="font-size:.75rem;"></i>
                                    </button>
                                </div>
                            </td>
                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}</td>
                            <td>
                                @if($lead->app_id)
                                    <span style="font-size:.72rem;font-weight:600;color:var(--bs-primary);">{{ $lead->app_id }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if(!$lead->submission_status || $lead->submission_status === 'pending')
                                    <span class="bd-mini bd-warn">Pending</span>
                                @elseif($lead->submission_status === 'approved')
                                    <span class="bd-mini bd-green">Approved</span>
                                @elseif($lead->submission_status === 'declined')
                                    <span class="bd-mini bd-red">Declined</span>
                                @elseif($lead->submission_status === 'underwriting')
                                    <span class="bd-mini bd-blue">Underwriting</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->submissionReviewer)
                                    <span style="font-size:.72rem;font-weight:600;">{{ $lead->submissionReviewer->name }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->submission_at)
                                    <span style="font-size:.72rem;">{{ \Carbon\Carbon::parse($lead->submission_at)->format('M d, h:i A') }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('sales.prettyPrint', $lead->id) }}" class="a-btn" style="font-size:.63rem;background:rgba(52,195,143,.08);color:#1a8754;border-color:rgba(52,195,143,.25);" target="_blank" title="Pretty Print">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                    <button class="a-btn a-edit btn-open-actions-modal"
                                        data-id="{{ $lead->id }}"
                                        data-name="{{ $lead->cn_name }}"
                                        data-policy="{{ $lead->policy_number ?? '' }}"
                                        data-partner="{{ $lead->assigned_partner ?? '' }}"
                                        data-appid="{{ $lead->app_id ?? '' }}"
                                        data-decision="{{ $lead->submission_status ?? '' }}"
                                        style="font-size:.63rem;">
                                        <i class="bx bx-pencil"></i> Manage
                                    </button>
                                    @if(!$lead->recall_requested_at)
                                        <button class="a-btn a-recall btn-recall-closer" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;">
                                            <i class="bx bx-undo"></i> Recall
                                        </button>
                                    @endif
                                    <button class="a-btn btn-send-back" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;background:rgba(220,53,69,.1);color:#dc3545;border-color:rgba(220,53,69,.25);">
                                        <i class="bx bx-arrow-back"></i> Back
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No validated leads in Submissions for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="px-3 py-2">{{ $leads->withQueryString()->links() }}</div>
        @endif
    </div>
</div>


{{-- Coverage / Premium / Plan Edit Modal --}}
<div class="modal fade" id="coverageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
        <div class="modal-content" style="border-radius:.75rem;border:1px solid rgba(212,175,55,.18);background:var(--bs-card-bg);box-shadow:0 8px 30px rgba(0,0,0,.18);overflow:hidden;">
            {{-- Header --}}
            <div class="modal-header py-2 px-3" style="border-bottom:1px solid rgba(212,175,55,.15);background:rgba(212,175,55,.06);">
                <span style="font-size:.78rem;font-weight:700;color:#b89730;display:flex;align-items:center;gap:.35rem;">
                    <i class="bx bx-edit-alt"></i> Edit Coverage / Premium / Plan
                </span>
                <button type="button" class="btn-close" style="font-size:.55rem;" data-bs-dismiss="modal"></button>
            </div>
            {{-- Body --}}
            <div class="px-3 pt-2 pb-1">
                <div style="font-size:.68rem;color:var(--bs-surface-400);margin-bottom:.6rem;">
                    <i class="bx bx-user me-1"></i><span id="coverage-lead-name" style="font-weight:600;color:var(--bs-body-color);"></span>
                </div>
                {{-- 3 fields in a compact grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.45rem .5rem;margin-bottom:.5rem;">
                    <div>
                        <label style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-400);display:block;margin-bottom:.2rem;">Coverage ($)</label>
                        <input type="number" step="0.01" id="coverage-amount" class="form-control form-control-sm" placeholder="50000" style="border-radius:.4rem;font-size:.76rem;">
                    </div>
                    <div>
                        <label style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-400);display:block;margin-bottom:.2rem;">Premium ($)</label>
                        <input type="number" step="0.01" id="coverage-premium" class="form-control form-control-sm" placeholder="75.50" style="border-radius:.4rem;font-size:.76rem;">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-400);display:block;margin-bottom:.2rem;">Plan / Policy Type</label>
                        <select id="coverage-policytype" class="form-select form-select-sm" style="border-radius:.4rem;font-size:.76rem;">
                            <option value="">— Select Plan —</option>
                            <option value="Level">Level</option>
                            <option value="Graded">Graded</option>
                            <option value="G.I">G.I (Guaranteed Issue)</option>
                            <option value="Modified">Modified</option>
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-400);display:block;margin-bottom:.2rem;">Carrier</label>
                        <select id="coverage-carrier" class="form-select form-select-sm" style="border-radius:.4rem;font-size:.76rem;">
                            <option value="">— Select Carrier —</option>
                            @foreach($carriers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{-- Footer --}}
            <div class="px-3 pb-3 pt-1 d-flex justify-content-end gap-2">
                <button type="button" data-bs-dismiss="modal" style="background:transparent;border:1px solid rgba(0,0,0,.1);border-radius:.4rem;padding:.28rem .7rem;font-size:.72rem;font-weight:600;color:var(--bs-surface-400);cursor:pointer;">Cancel</button>
                <button type="button" id="coverage-save-btn" style="background:linear-gradient(135deg,#d4af37,#b8941f);border:none;border-radius:.4rem;padding:.28rem .85rem;font-size:.72rem;font-weight:700;color:#0f172a;cursor:pointer;">
                    <i class="bx bx-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Manage Details Modal --}}
<div class="modal fade sub-modal" id="actionsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0">
                    <i class="bx bx-edit-alt"></i> Manage Submission
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="actions-lead-name"></strong>
                </p>

                {{-- Decision --}}
                <div class="mb-3">
                    <label class="form-label">Decision <span class="text-danger">*</span></label>
                    <select id="actions-decision" class="form-select">
                        <option value="">— Select Decision —</option>
                        <option value="approved">Approved</option>
                        <option value="declined">Declined</option>
                        <option value="underwriting">Underwriting</option>
                    </select>
                </div>

                {{-- App ID --}}
                <div class="mb-3" id="field-app-id">
                    <label class="form-label">App ID</label>
                    <input type="text" id="actions-app-id" class="form-control" placeholder="e.g. APP-2026-001">
                </div>

                {{-- Policy Number (only for Approved) --}}
                <div class="mb-3" id="field-policy-number" style="display:none;">
                    <label class="form-label">Policy Number</label>
                    <input type="text" id="actions-policy-number" class="form-control" placeholder="Enter policy number">
                </div>

                {{-- Partner (only for Approved) --}}
                <div class="mb-3" id="field-partner" style="display:none;">
                    <label class="form-label">Partner</label>
                    <select id="actions-partner" class="form-select">
                        <option value="">— Select Partner —</option>
                        @if(isset($partners))
                            @foreach($partners as $p)
                                <option value="{{ $p->name }}" data-partner-id="{{ $p->id }}">{{ $p->name }}{{ $p->code ? ' ('.$p->code.')' : '' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background:var(--bs-surface-100);color:var(--bs-surface-500);border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-sm" id="actions-save-btn" style="background:var(--bs-gold,#d4af37);color:#fff;border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">
                    <i class="bx bx-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Recall / Send Back to Closer Modal --}}
<div class="modal fade sub-modal" id="recallModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(139,92,246,.04);border-bottom:1px solid rgba(139,92,246,.1);">
                <h6 class="modal-title mb-0" style="font-size:.85rem;color:#7c3aed;">
                    <i class="bx bx-undo me-1"></i> Send Back to Closer
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="recall-lead-name"></strong>
                </p>
                <p class="mb-3" style="font-size:.7rem;color:#7c3aed;background:rgba(139,92,246,.04);border:1px solid rgba(139,92,246,.12);border-radius:.4rem;padding:.5rem .65rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    This will send the sale back to the closer for re-dial. The closer will see the recall note on their dashboard.
                </p>
                <div class="mb-2">
                    <label class="form-label">Comment / Instructions <span class="text-danger">*</span></label>
                    <textarea id="recall-note" class="form-control" rows="3" placeholder="Why is this being sent back?" style="resize:none;"></textarea>
                    <div id="recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a comment.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background:var(--bs-surface-100);color:var(--bs-surface-500);border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-sm" id="recall-confirm-btn" style="background:rgba(139,92,246,.9);color:#fff;border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">
                    <i class="bx bx-undo me-1"></i> Send Back
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function() {
    let currentLeadId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ==== Manage Details Modal ====
    const actionsModalEl = document.getElementById('actionsModal');
    let actionsModalInstance = null;

    document.querySelectorAll('.btn-open-actions-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentLeadId = this.dataset.id;
            document.getElementById('actions-lead-name').textContent = this.dataset.name;
            document.getElementById('actions-decision').value = this.dataset.decision || '';
            document.getElementById('actions-app-id').value = this.dataset.appid;
            document.getElementById('actions-policy-number').value = this.dataset.policy;
            document.getElementById('actions-partner').value = this.dataset.partner;

            // Show/hide conditional fields based on current decision
            const decision = this.dataset.decision || '';
            const policyField = document.getElementById('field-policy-number');
            const partnerField = document.getElementById('field-partner');
            if (decision === 'approved') {
                policyField.style.display = 'block';
                partnerField.style.display = 'block';
            } else {
                policyField.style.display = 'none';
                partnerField.style.display = 'none';
            }

            // Show modal
            if (actionsModalInstance) actionsModalInstance.dispose();
            actionsModalInstance = new bootstrap.Modal(actionsModalEl);
            actionsModalInstance.show();
        });
    });

    // Decision dropdown change handler
    document.getElementById('actions-decision').addEventListener('change', function() {
        const decision = this.value;
        const policyField = document.getElementById('field-policy-number');
        const partnerField = document.getElementById('field-partner');

        if (decision === 'approved') {
            policyField.style.display = 'block';
            partnerField.style.display = 'block';
        } else if (decision === 'declined' || decision === 'underwriting') {
            policyField.style.display = 'none';
            partnerField.style.display = 'none';
        } else {
            policyField.style.display = 'none';
            partnerField.style.display = 'none';
        }
    });

    // Proper modal cleanup on hide
    actionsModalEl.addEventListener('hidden.bs.modal', function() {
        if (actionsModalInstance) {
            actionsModalInstance.dispose();
            actionsModalInstance = null;
        }
        // Remove any lingering backdrops
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    // Save Details
    document.getElementById('actions-save-btn').addEventListener('click', function() {
        const decision = document.getElementById('actions-decision').value;
        const appId    = document.getElementById('actions-app-id').value.trim();
        const policy   = document.getElementById('actions-policy-number').value.trim();
        const partner  = document.getElementById('actions-partner').value;
        const partnerEl = document.getElementById('actions-partner');

        if (!decision) {
            alert('Please select a Decision.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Saving…';

        fetch('/submissions/' + currentLeadId + '/save-decision', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                submission_status:   decision,
                app_id:           appId || null,
                policy_number:    decision === 'approved' ? (policy || null) : null,
                assigned_partner: decision === 'approved' ? (partner || null) : null,
                partner_id:       (decision === 'approved' && partnerEl.selectedOptions[0]) ? partnerEl.selectedOptions[0].dataset.partnerId : null,
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save';
            if (data.success) {
                if (actionsModalInstance) actionsModalInstance.hide();
                location.reload();
            } else {
                alert(data.message || 'Error saving.');
            }
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save me-1"></i> Save'; alert('Error: ' + err.message); });
    });

    // ==== Recall / Send Back ====
    var recallLeadId = null;
    const recallModalEl = document.getElementById('recallModal');
    let recallModalInstance = null;

    document.querySelectorAll('.btn-recall-closer').forEach(btn => {
        btn.addEventListener('click', function() {
            recallLeadId = this.dataset.id;
            document.getElementById('recall-lead-name').textContent = this.dataset.name;
            document.getElementById('recall-note').value = '';
            document.getElementById('recall-note-error').style.display = 'none';

            if (recallModalInstance) recallModalInstance.dispose();
            recallModalInstance = new bootstrap.Modal(recallModalEl);
            recallModalInstance.show();
        });
    });

    // Cleanup recall modal on hide
    recallModalEl.addEventListener('hidden.bs.modal', function() {
        if (recallModalInstance) {
            recallModalInstance.dispose();
            recallModalInstance = null;
        }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    document.getElementById('recall-confirm-btn').addEventListener('click', function() {
        var note = document.getElementById('recall-note').value.trim();
        if (!note) { document.getElementById('recall-note-error').style.display = 'block'; return; }
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Sending…';
        fetch('/submissions/' + recallLeadId + '/recall-to-closer', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ recall_note: note })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back';
            if (data.success) {
                if (recallModalInstance) recallModalInstance.hide();
                location.reload();
            }
            else alert(data.message || 'Error.');
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back'; alert('Error: ' + err.message); });
    });

    // ==== Coverage / Premium / Plan Edit Modal ====
    var coverageLeadId = null;
    const coverageModalEl = document.getElementById('coverageModal');
    let coverageModalInstance = null;

    document.querySelectorAll('.btn-edit-coverage').forEach(btn => {
        btn.addEventListener('click', function() {
            coverageLeadId = this.dataset.id;
            document.getElementById('coverage-lead-name').textContent = this.dataset.name;
            document.getElementById('coverage-amount').value = this.dataset.coverage;
            document.getElementById('coverage-premium').value = this.dataset.premium;
            document.getElementById('coverage-policytype').value = this.dataset.policytype;
            document.getElementById('coverage-carrier').value = this.dataset.carrier || '';
            if (coverageModalInstance) coverageModalInstance.dispose();
            coverageModalInstance = new bootstrap.Modal(coverageModalEl);
            coverageModalInstance.show();
        });
    });

    coverageModalEl.addEventListener('hidden.bs.modal', function() {
        if (coverageModalInstance) { coverageModalInstance.dispose(); coverageModalInstance = null; }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    document.getElementById('coverage-save-btn').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Saving…';
        fetch('/submissions/' + coverageLeadId + '/update-coverage', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                coverage_amount:      document.getElementById('coverage-amount').value || null,
                monthly_premium:      document.getElementById('coverage-premium').value || null,
                policy_type:          document.getElementById('coverage-policytype').value.trim() || null,
                insurance_carrier_id: document.getElementById('coverage-carrier').value || null,
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save';
            if (data.success) { if (coverageModalInstance) coverageModalInstance.hide(); location.reload(); }
            else alert(data.message || 'Error saving.');
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save me-1"></i> Save'; alert('Error: ' + err.message); });
    });

    // ==== Live Search ====
    const liveSearchInput = document.querySelector('input[name="search"]');
    const liveSearchForm  = document.getElementById('submissionsFilterForm');
    let liveSearchTimer;
    if (liveSearchInput && liveSearchForm) {
        liveSearchInput.addEventListener('input', function() {
            clearTimeout(liveSearchTimer);
            liveSearchTimer = setTimeout(() => liveSearchForm.submit(), 450);
        });
    }

    // Send Back to Previous Stage (with debounce to prevent double-click)
    document.querySelectorAll('.btn-send-back').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var button = this;
            if (button.dataset.processing === 'true') return; // Prevent double-click
            
            var id = button.dataset.id;
            var name = button.dataset.name;
            
            button.dataset.processing = 'true';
            if (!confirm('Send "' + name + '" back to the previous stage?')) {
                button.dataset.processing = 'false';
                return;
            }
            
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            fetch('/leads/' + id + '/send-to-previous-stage', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-arrow-back"></i> Back';
                    button.dataset.processing = 'false';
                    alert(data.message || 'Error sending back.');
                }
            })
            .catch(err => {
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-arrow-back"></i> Back';
                button.dataset.processing = 'false';
                alert('Error: ' + err.message);
            });
        });
    });
})();

// Date filter functions (matching Sales page behavior)
function setTodayFilter() {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Los_Angeles' });
    document.getElementById('filter_date_from').value = today;
    document.getElementById('filter_date_to').value = today;
    document.getElementById('submissionsFilterForm').submit();
}

function setThisWeekFilter() {
    const now = new Date();
    const pacific = new Date(now.toLocaleString('en-US', { timeZone: 'America/Los_Angeles' }));
    const dayOfWeek = pacific.getDay();
    const sunday = new Date(pacific);
    sunday.setDate(pacific.getDate() - dayOfWeek);
    const saturday = new Date(pacific);
    saturday.setDate(pacific.getDate() + (6 - dayOfWeek));
    document.getElementById('filter_date_from').value = sunday.toISOString().split('T')[0];
    document.getElementById('filter_date_to').value = saturday.toISOString().split('T')[0];
    document.getElementById('submissionsFilterForm').submit();
}

function setThisMonthFilter() {
    const now = new Date();
    const pacific = new Date(now.toLocaleString('en-US', { timeZone: 'America/Los_Angeles' }));
    const firstDay = new Date(pacific.getFullYear(), pacific.getMonth(), 1);
    const lastDay = new Date(pacific.getFullYear(), pacific.getMonth() + 1, 0);
    document.getElementById('filter_date_from').value = firstDay.toISOString().split('T')[0];
    document.getElementById('filter_date_to').value = lastDay.toISOString().split('T')[0];
    document.getElementById('submissionsFilterForm').submit();
}
</script>
@endsection
