@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Policy Submission')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Policy Submission — Company Overview Style
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
.ex-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: box-shadow .2s;
}
.ex-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* ── KPI Stat Cards ── */
.kpi-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.kpi-card {
    flex: 1 1 80px;
    min-width: 75px;
    padding: 0.65rem 0.6rem;
    border-radius: 0.55rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.55rem 0.55rem 0 0;
}
.kpi-card .k-icon {
    font-size: 1rem;
    margin-bottom: 0.2rem;
    display: block;
    opacity: .7;
}
.kpi-card .k-val { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.kpi-card .k-lbl {
    font-size: 0.58rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 0.2rem;
}

/* KPI color variants */
.kpi-card.k-gold    { background: rgba(212,175,55,.06); }
.kpi-card.k-gold::before    { background: linear-gradient(90deg, #d4af37, #e8c84a); }
.kpi-card.k-gold .k-val, .kpi-card.k-gold .k-icon { color: #b89730; }

.kpi-card.k-green   { background: rgba(52,195,143,.06); }
.kpi-card.k-green::before   { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.kpi-card.k-green .k-val, .kpi-card.k-green .k-icon { color: #1a8754; }

.kpi-card.k-warn    { background: rgba(241,180,76,.06); }
.kpi-card.k-warn::before    { background: linear-gradient(90deg, #f1b44c, #f5cd7e); }
.kpi-card.k-warn .k-val, .kpi-card.k-warn .k-icon { color: #b87a14; }

.kpi-card.k-red     { background: rgba(244,106,106,.06); }
.kpi-card.k-red::before     { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.kpi-card.k-red .k-val, .kpi-card.k-red .k-icon { color: #c84646; }

.kpi-card.k-purple  { background: rgba(124,105,239,.06); }
.kpi-card.k-purple::before  { background: linear-gradient(90deg, #7c69ef, #a899f5); }
.kpi-card.k-purple .k-val, .kpi-card.k-purple .k-icon { color: #5b49c7; }

.kpi-card.k-blue    { background: rgba(85,110,230,.06); }
.kpi-card.k-blue::before    { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.kpi-card.k-blue .k-val, .kpi-card.k-blue .k-icon { color: #556ee6; }

.kpi-card.k-teal    { background: rgba(80,165,241,.06); }
.kpi-card.k-teal::before    { background: linear-gradient(90deg, #50a5f1, #8cc5f7); }
.kpi-card.k-teal .k-val, .kpi-card.k-teal .k-icon { color: #2b81c9; }

.kpi-card.k-gray    { background: rgba(108,117,125,.05); }
.kpi-card.k-gray::before    { background: linear-gradient(90deg, #6c757d, #95a0a8); }
.kpi-card.k-gray .k-val, .kpi-card.k-gray .k-icon { color: #6c757d; }

/* ── Section Cards ── */
.sec-card {
    padding: 0;
    margin-bottom: 0.65rem;
    overflow: hidden;
}
.sec-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    flex-wrap: wrap;
    gap: 0.4rem;
}
.sec-hdr h6 {
    margin: 0;
    font-size: 0.78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.sec-hdr h6 i { opacity: .6; font-size: 0.95rem; }

/* ── Compact Table ── */
.ex-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.75rem;
}
.ex-tbl thead th {
    text-transform: uppercase;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.45rem 0.6rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
    position: sticky;
    top: 0;
    z-index: 1;
}
.ex-tbl tbody td {
    padding: 0.45rem 0.6rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    vertical-align: middle;
    white-space: nowrap;
}
.ex-tbl tbody tr { transition: background .12s; }
.ex-tbl tbody tr:hover { background: rgba(212,175,55,.03); }

/* Badge mini */
.bd-mini {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    display: inline-block;
    min-width: 22px;
    text-align: center;
}
.bd-mini.bd-blue   { background: rgba(85,110,230,.12); color: #556ee6; }
.bd-mini.bd-green  { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-mini.bd-red    { background: rgba(244,106,106,.12); color: #c84646; }
.bd-mini.bd-warn   { background: rgba(241,180,76,.12); color: #b87a14; }
.bd-mini.bd-teal   { background: rgba(80,165,241,.12); color: #2b81c9; }
.bd-mini.bd-gold   { background: rgba(212,175,55,.12); color: #b89730; }
.bd-mini.bd-gray   { background: rgba(108,117,125,.12); color: #6c757d; }
.bd-mini.bd-purple { background: rgba(124,105,239,.12); color: #5b49c7; }

/* Scrollable table wrapper */
.scroll-tbl { overflow-x: auto; overflow-y: auto; max-height: 550px; }
.scroll-tbl::-webkit-scrollbar { width: 3px; height: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }
.ex-tbl { min-width: 1100px; }

/* ── Filter Row ── */
.filter-form { display: flex; flex-wrap: wrap; gap: 0.4rem; padding: 0.5rem 0.75rem; }
.filter-form .f-input {
    border: 1px solid var(--bs-surface-300);
    border-radius: 1rem;
    padding: 0.28rem 0.6rem;
    font-size: 0.72rem;
    background: transparent;
    color: inherit;
    outline: none;
    transition: border-color .15s;
}
.filter-form .f-input:focus { border-color: var(--bs-gold, #d4af37); box-shadow: 0 0 0 2px rgba(212,175,55,.1); }

/* Pill-select & pill-date base */
.sl-pill-select, .sl-pill-date {
    font-size: .72rem; font-weight: 600;
    padding: .32rem .55rem; border-radius: 22px !important;
    border: 1px solid rgba(0,0,0,.08) !important;
    background: #fff; color: #475569;
    cursor: pointer; outline: none;
    transition: border-color .15s;
}
.sl-pill-select {
    -webkit-appearance: none; -moz-appearance: none; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .5rem center;
    padding-right: 1.5rem;
    max-width: 180px;
}
.sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37 !important; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

/* Dark mode — pill filters */
[data-theme="dark"] .sl-pill-select,
[data-theme="dark"] .sl-pill-date {
    background: rgba(30,41,59,.8) !important; border-color: rgba(255,255,255,.1) !important; color: #cbd5e1;
}
[data-theme="dark"] .sl-pill-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E") !important;
}

.filter-form .f-btn {
    background: var(--bs-gold, #d4af37);
    border: none;
    border-radius: 1rem;
    padding: 0.28rem 0.7rem;
    font-size: 0.68rem;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    transition: opacity .15s;
}
.filter-form .f-btn:hover { opacity: .85; }
.filter-form .f-reset {
    background: transparent;
    border: 1px solid var(--bs-surface-300);
    border-radius: 1rem;
    padding: 0.28rem 0.6rem;
    font-size: 0.68rem;
    font-weight: 500;
    color: var(--bs-surface-500);
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.filter-form .f-reset:hover { border-color: var(--bs-gold); color: var(--bs-gold); }

/* Inline controls */
.ex-tbl .form-select-sm {
    -webkit-appearance: none; -moz-appearance: none; appearance: none;
    border-radius: 22px;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.22rem 1.4rem 0.22rem 0.5rem;
    border: 1px solid rgba(0,0,0,.08);
    background-color: transparent;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .45rem center;
    background-size: 8px 5px;
    cursor: pointer;
    color: inherit;
}
.ex-tbl .form-select-sm:focus {
    border-color: var(--bs-gold, #d4af37);
    box-shadow: 0 0 0 2px rgba(212,175,55,.1);
}

/* ── Action buttons ── */
.a-btn {
    border: none;
    border-radius: 0.3rem;
    padding: 0.18rem 0.4rem;
    font-size: 0.65rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
}
.a-btn.a-view { background: rgba(80,165,241,.1); color: #2b81c9; }
.a-btn.a-view:hover { background: rgba(80,165,241,.2); }
.a-btn.a-edit { background: rgba(212,175,55,.1); color: #b89730; }
.a-btn.a-edit:hover { background: rgba(212,175,55,.2); }

/* ── Modal ── */
.iss-modal .modal-dialog { max-width: 380px; }
.iss-modal .modal-content {
    border-radius: 0.6rem;
    border: 1px solid rgba(255,255,255,.08);
    overflow: hidden;
    background: var(--bs-card-bg);
    box-shadow: 0 8px 30px rgba(0,0,0,.15);
}
.iss-modal .modal-header {
    background: var(--bs-card-bg);
    padding: 0.65rem 0.85rem;
    border-bottom: 1px solid rgba(0,0,0,.06);
}
.iss-modal .modal-header .modal-title {
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.iss-modal .modal-header .modal-title i { color: var(--bs-gold, #d4af37); opacity: .7; font-size: 1rem; }
.iss-modal .modal-body { padding: 0.85rem; }
.iss-modal .modal-body .form-label { font-size: 0.75rem; font-weight: 600; margin-bottom: 0.3rem; }
.iss-modal .modal-body .form-control, .iss-modal .modal-body .form-select {
    font-size: 0.8rem; border-radius: 0.4rem; padding: 0.4rem 0.6rem;
}
.iss-modal .modal-body .form-control:focus, .iss-modal .modal-body .form-select:focus {
    border-color: var(--bs-gold, #d4af37); box-shadow: 0 0 0 2px rgba(212,175,55,.12);
}
.iss-modal .modal-footer { border-top: 1px solid rgba(0,0,0,.05); padding: 0.55rem 0.85rem; }
.iss-modal .st-btn {
    display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    padding: 0.5rem 0.75rem; border-radius: 0.45rem; border: 1px solid;
    font-size: 0.8rem; font-weight: 600; cursor: pointer; width: 100%;
    transition: all .15s; background: transparent;
}
.iss-modal .st-issued { background: rgba(52,195,143,.06); color: #1a8754; border-color: rgba(52,195,143,.25); }
.iss-modal .st-issued:hover { background: #1a8754; color: #fff; }
.iss-modal .st-incomplete { background: rgba(241,180,76,.06); color: #b87a14; border-color: rgba(241,180,76,.25); }
.iss-modal .st-incomplete:hover { background: #b87a14; color: #fff; }
.iss-modal .st-pending { background: rgba(108,117,125,.06); color: #6c757d; border-color: rgba(108,117,125,.25); }
.iss-modal .st-pending:hover { background: #6c757d; color: #fff; }
.iss-modal .btn-cancel {
    background: var(--bs-surface-100); color: var(--bs-surface-500); border: none;
    border-radius: 1rem; padding: 0.35rem 0.85rem; font-size: 0.74rem; font-weight: 600;
    cursor: pointer;
}
.iss-modal .btn-cancel:hover { background: var(--bs-surface-200); }
.iss-modal .modal-backdrop, .modal-backdrop.show { opacity: 0.3 !important; }

/* Pagination */
.ex-card .pagination { margin: 0; }
.ex-card .pagination .page-link {
    border-radius: 0.35rem; margin: 0 1px; font-size: 0.7rem;
    border: 1px solid var(--bs-surface-200); color: var(--bs-surface-500);
    padding: 0.2rem 0.5rem;
}
.ex-card .pagination .page-item.active .page-link {
    background: var(--bs-gold, #d4af37); border-color: var(--bs-gold); color: #fff;
}
.ex-card .pagination svg { max-width: 14px !important; max-height: 14px !important; }

@media(max-width:768px){
    .kpi-card .k-val { font-size: 1.1rem; }
    .filter-form { flex-direction: column; }
    .filter-form .f-input { width: 100%; }
}
</style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:0.5rem; border:none; background:rgba(52,195,143,.08); color:#1a8754; font-size:.78rem; padding:.5rem .75rem;">
            <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem;padding:.6rem;"></button>
        </div>
    @endif

    @php
        $totalLeads = $leads->total();
        $issuedCount = $leads->where('issuance_status', Statuses::ISSUANCE_ISSUED)->count();
        $incompleteCount = $leads->where('issuance_status', 'Incomplete')->count();
        $pendingCount = $leads->filter(fn($l) => !$l->issuance_status || $l->issuance_status === 'Pending')->count();
        $followupYes = $leads->where('followup_status', Statuses::MIS_YES)->count();
        $followupNo = $leads->filter(fn($l) => $l->followup_status !== Statuses::MIS_YES)->count();
        $withPartner = $leads->whereNotNull('partner_id')->count();
    @endphp

    {{-- KPI Row --}}
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-data k-icon"></i>
            <div class="k-val">{{ $totalLeads }}</div>
            <div class="k-lbl">Total Records</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-circle k-icon"></i>
            <div class="k-val">{{ $issuedCount }}</div>
            <div class="k-lbl">Issued</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time-five k-icon"></i>
            <div class="k-val">{{ $incompleteCount }}</div>
            <div class="k-lbl">Incomplete</div>
        </div>
        <div class="kpi-card k-gray ex-card">
            <i class="bx bx-loader-alt k-icon"></i>
            <div class="k-val">{{ $pendingCount }}</div>
            <div class="k-lbl">Pending</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-user-check k-icon"></i>
            <div class="k-val">{{ $followupYes }}</div>
            <div class="k-lbl">Followup Done</div>
        </div>
        <div class="kpi-card k-gold ex-card">
            <i class="bx bx-buildings k-icon"></i>
            <div class="k-val">{{ $withPartner }}</div>
            <div class="k-lbl">Partnered</div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-check"></i> Policy Submission & Followup</h6>
            <span style="font-size:0.62rem; color:var(--bs-surface-400);">{{ $totalLeads }} records</span>
        </div>
        <form method="GET" action="{{ route('issuance.index') }}" class="filter-form">
            <input type="text" name="search" class="f-input" style="min-width:160px;" placeholder="Search name, phone, carrier..." value="{{ request('search') }}">
            <select name="carrier" class="sl-pill-select">
                <option value="">All Carriers</option>
                @foreach($carriers as $carrier)
                    <option value="{{ $carrier->id }}" {{ request('carrier') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                @endforeach
            </select>
            <select name="issuance_status" class="sl-pill-select">
                <option value="">All Status</option>
                <option value="Issued" {{ request('issuance_status') == 'Issued' ? 'selected' : '' }}>Issued</option>
                <option value="Incomplete" {{ request('issuance_status') == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
            </select>
            <select name="followup_status" class="sl-pill-select">
                <option value="">Followup</option>
                <option value="Yes" {{ request('followup_status') == 'Yes' ? 'selected' : '' }}>Yes</option>
                <option value="No" {{ request('followup_status') == 'No' ? 'selected' : '' }}>No</option>
            </select>
            <select name="policy_type" class="sl-pill-select">
                <option value="">Policy Type</option>
                <option value="G.I" {{ request('policy_type') == 'G.I' ? 'selected' : '' }}>G.I</option>
                <option value="Graded" {{ request('policy_type') == 'Graded' ? 'selected' : '' }}>Graded</option>
                <option value="Level" {{ request('policy_type') == 'Level' ? 'selected' : '' }}>Level</option>
                <option value="Modified" {{ request('policy_type') == 'Modified' ? 'selected' : '' }}>Modified</option>
            </select>
            <button type="submit" class="f-btn"><i class="bx bx-search"></i> Filter</button>
            @if(request()->hasAny(['search','carrier','issuance_status','followup_status','policy_type']))
                <a href="{{ route('issuance.index') }}" class="f-reset"><i class="bx bx-reset"></i> Clear</a>
            @endif
        </form>
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Closer</th>
                        <th>Sale Date</th>
                        <th>Carrier</th>
                        <th>Type</th>
                        <th>Policy #</th>
                        <th>Partner</th>
                        <th class="text-center">Coverage / Premium</th>
                        <th>Draft Dates</th>
                        <th class="text-center">Status</th>
                        <th style="min-width:130px;">Issued By</th>
                        <th style="min-width:120px;">Followup Person</th>
                        <th style="min-width:130px;">F/U Assigned By</th>
                        <th class="text-center">F/U</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $idx => $lead)
                        <tr>
                            <td style="color:var(--bs-surface-400);">{{ $leads->firstItem() + $idx }}</td>
                            <td><strong>{{ $lead->cn_name }}</strong></td>
                            <td style="font-size:.7rem;">{{ $lead->phone_number }}</td>
                            <td>
                                @if($lead->closer_name)
                                    <span class="bd-mini bd-teal">{{ $lead->closer_name }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td style="font-size:.68rem; white-space:nowrap;">{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                            <td>{{ $lead->policy_type ?? 'N/A' }}</td>
                            <td>
                                @if($lead->issued_policy_number)
                                    <code style="font-size:.7rem;">{{ $lead->issued_policy_number }}</code>
                                @else
                                    <span style="color:var(--bs-surface-400); font-size:.7rem;">Not Set</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->partner)
                                    <span class="bd-mini bd-green">{{ $lead->partner->name }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="bd-mini bd-gold">${{ number_format($lead->coverage_amount ?? 0, 0) }}</span>
                                <span style="color:var(--bs-surface-400); margin:0 2px;">/</span>
                                <span class="bd-mini bd-blue">${{ number_format($lead->monthly_premium ?? 0, 0) }}</span>
                            </td>
                            <td style="font-size:.68rem;">
                                <div>I: {{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('m/d/y') : '—' }}</div>
                                <div>F: {{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('m/d/y') : '—' }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusBdClass = match($lead->issuance_status) {
                                        Statuses::ISSUANCE_ISSUED => 'bd-green',
                                        'Incomplete' => 'bd-warn',
                                        default => 'bd-gray'
                                    };
                                @endphp
                                <span class="bd-mini {{ $statusBdClass }}">{{ $lead->issuance_status ?? 'Pending' }}</span>
                            </td>
                            <td>
                                @if($lead->issuedByUser)
                                    <strong style="font-size:.72rem;">{{ $lead->issuedByUser->name }}</strong>
                                    @if($lead->issuance_date)
                                        <div style="font-size:.62rem;color:var(--bs-surface-400);margin-top:1px">
                                            {{ \Carbon\Carbon::parse($lead->issuance_date)->format('M d, h:i A') }}
                                        </div>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);font-size:.7rem">—</span>
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm followup-person-dropdown" data-lead-id="{{ $lead->id }}" data-current-person="{{ $lead->assigned_followup_person }}">
                                    <option value="">Select</option>
                                    @foreach($followupUsers as $employee)
                                        <option value="{{ $employee->id }}" {{ $lead->assigned_followup_person == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @if($lead->followupAssignedByUser)
                                    <strong style="font-size:.72rem;">{{ $lead->followupAssignedByUser->name }}</strong>
                                    @if($lead->followup_assigned_at)
                                        <div style="font-size:.62rem;color:var(--bs-surface-400);margin-top:1px">
                                            {{ \Carbon\Carbon::parse($lead->followup_assigned_at)->format('M d, h:i A') }}
                                        </div>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);font-size:.7rem">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($lead->followup_status === Statuses::MIS_YES)
                                    <span class="bd-mini bd-green">Yes</span>
                                @else
                                    <span class="bd-mini bd-red">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('issuance.show', $lead->id) }}" class="a-btn a-view" title="View">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <button type="button" class="a-btn a-edit" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $lead->id }}" title="Update">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center py-3" style="color:var(--bs-surface-400); font-size:.78rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem; opacity:.4;"></i>
                                <p class="mt-1 mb-0">No submission data available</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="d-flex justify-content-center" style="padding:0.5rem;">
                {{ $leads->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Modals rendered outside table to avoid rendering issues --}}
    @foreach($leads as $lead)
        <div class="modal fade iss-modal" id="statusModal-{{ $lead->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-check-circle"></i> {{ $lead->cn_name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.6rem;"></button>
                    </div>
                    <form id="issuance-form-{{ $lead->id }}" action="{{ route('issuance.updateIssuanceStatus', $lead->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="policy-number-{{ $lead->id }}" class="form-label">
                                    Policy Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="policy-number-{{ $lead->id }}" name="issued_policy_number" value="{{ $lead->issued_policy_number }}" required placeholder="Enter policy number">
                            </div>
                            <div class="mb-3">
                                <label for="partner-{{ $lead->id }}" class="form-label">
                                    Partner <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="partner-{{ $lead->id }}" name="partner_id" required>
                                    <option value="">Select Partner</option>
                                    @foreach($partners as $partner)
                                        <option value="{{ $partner->id }}" {{ $lead->partner_id == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->name }} ({{ $partner->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="d-grid gap-2" id="status-buttons-{{ $lead->id }}">
                                    <button type="submit" name="issuance_status" value="Issued" class="st-btn st-issued">
                                        <i class="bx bx-check-circle"></i> Issued
                                    </button>
                                    <button type="submit" name="issuance_status" value="Incomplete" class="st-btn st-incomplete">
                                        <i class="bx bx-time-five"></i> Incomplete
                                    </button>
                                    <button type="submit" name="issuance_status" value="Pending" class="st-btn st-pending pending-unassign-btn" data-lead-id="{{ $lead->id }}">
                                        <i class="bx bx-pause"></i> Pending (Unassign)
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="reason-{{ $lead->id }}" class="form-label">Reason</label>
                                <textarea class="form-control" id="reason-{{ $lead->id }}" name="issuance_reason" rows="2" placeholder="Add reason...">{{ $lead->issuance_reason }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@include('partials.sl-filter-assets')

@section('script')
<script>
$(document).ready(function() {
    // Handle followup person dropdown changes
    $('.followup-person-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const followupPersonId = $(this).val();
        const dropdown = $(this);

        if (confirm('Assign this person for followup?')) {
            dropdown.prop('disabled', true);
            $.ajax({
                url: '/followup/' + leadId + '/assign-person',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    assigned_followup_person: followupPersonId
                },
                success: function(response) {
                    if (response.success) {
                        slToast(response.message || 'Assigned successfully');
                        dropdown.data('current-person', followupPersonId);
                    }
                    dropdown.prop('disabled', false);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to assign followup person');
                    dropdown.prop('disabled', false);
                    dropdown.val(dropdown.data('current-person'));
                }
            });
        } else {
            dropdown.val(dropdown.data('current-person'));
        }
    });

    // Handle unlock field button (Super Admin only)
    $('.unlock-field').click(function(e) {
        e.preventDefault();
        const targetId = $(this).data('target');
        const leadId = targetId.split('-').pop();
        let fieldToUnlock = '';
        if (targetId.includes('policy-number')) fieldToUnlock = 'policy_number';
        else if (targetId.includes('partner')) fieldToUnlock = 'partner';
        else if (targetId.includes('status-buttons')) fieldToUnlock = 'status';

        if (!fieldToUnlock) { alert('Could not determine field to unlock'); return; }

        if (confirm('Unlock this field?')) {
            const btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: '/issuance/' + leadId + '/unlock-field',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', field: fieldToUnlock },
                success: function(response) {
                    if (response.success) {
                        slToast(response.message || 'Unlocked');
                        const targetField = $('#' + targetId);
                        if (targetId.includes('status-buttons')) {
                            targetField.find('button[type="submit"]').prop('disabled', false);
                        } else {
                            targetField.prop('readonly', false).prop('disabled', false);
                        }
                        btn.html('<i class="bx bx-check"></i>').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to unlock');
                    btn.prop('disabled', false);
                }
            });
        }
    });

    // Handle reset Issuance status button
    $('.reset-issuance-status').click(function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const button = $(this);

        if (confirm('Reset this Issuance status? All issuance info will be cleared.')) {
            button.prop('disabled', true);
            $.ajax({
                url: '/issuance/' + leadId + '/issuance-status/reset',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        slToast(response.message || 'Reset successfully');
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to reset');
                    button.prop('disabled', false);
                }
            });
        }
    });

    // Pending status with unassign partner
    let pendingStatusClicked = false;
    $('.pending-unassign-btn').on('click', function() { pendingStatusClicked = true; });
    $(document).on('submit', 'form', function() {
        if (pendingStatusClicked) {
            $(this).find('select[name="partner_id"]').val('');
            pendingStatusClicked = false;
        }
    });

    function slToast(msg) {
        const t = $('<div style="position:fixed;top:16px;right:16px;z-index:9999;background:var(--bs-card-bg);border:1px solid rgba(52,195,143,.3);border-radius:0.5rem;padding:0.5rem 0.85rem;font-size:0.75rem;box-shadow:0 4px 16px rgba(0,0,0,.1);display:flex;align-items:center;gap:0.4rem;animation:slToastIn .3s ease;"><i class="bx bx-check-circle" style="color:#1a8754;font-size:0.9rem;"></i>' + msg + '</div>');
        $('body').append(t);
        setTimeout(() => t.fadeOut(300, function(){ $(this).remove(); }), 3000);
    }
});
</script>
@endsection
