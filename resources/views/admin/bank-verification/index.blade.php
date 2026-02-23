@extends('layouts.master')

@section('title') Bank Verification @endsection

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Bank Verification — Company Overview Style
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
.kpi-card .k-sub {
    font-size: 0.6rem;
    font-weight: 600;
    margin-top: 0.1rem;
    opacity: .8;
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
.sec-body { padding: 0.6rem 0.75rem; }

/* ── Compact Table ── */
.ex-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.75rem;
}
.ex-tbl thead th {
    text-transform: uppercase;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
    position: sticky;
    top: 0;
    z-index: 1;
}
.ex-tbl tbody td {
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    vertical-align: middle;
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
.bd-mini.bd-green  { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-mini.bd-red    { background: rgba(244,106,106,.12); color: #c84646; }
.bd-mini.bd-warn   { background: rgba(241,180,76,.12); color: #b87a14; }
.bd-mini.bd-gold   { background: rgba(212,175,55,.12); color: #b89730; }
.bd-mini.bd-purple { background: rgba(124,105,239,.12); color: #5b49c7; }
.bd-mini.bd-blue   { background: rgba(85,110,230,.12); color: #556ee6; }

/* Scrollable table wrapper */
.scroll-tbl { max-height: 420px; overflow-y: auto; }
.scroll-tbl::-webkit-scrollbar { width: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

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
.ex-tbl .form-select-sm, .ex-tbl .form-control-sm {
    border-radius: 22px;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.22rem 0.5rem;
    border: 1px solid rgba(0,0,0,.08);
    background-color: transparent;
    color: inherit;
}
.ex-tbl .form-select-sm {
    -webkit-appearance: none; -moz-appearance: none; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .45rem center;
    background-size: 8px 5px;
    padding-right: 1.4rem;
    cursor: pointer;
}
.ex-tbl .form-select-sm:focus, .ex-tbl .form-control-sm:focus {
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
.bv-modal .modal-content { border-radius: 0.6rem; border: none; overflow: hidden; }
.bv-modal .modal-header {
    background: var(--bs-card-bg);
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
}
.bv-modal .modal-header .modal-title {
    font-size: 0.82rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.bv-modal .modal-header .modal-title i { color: var(--bs-gold, #d4af37); opacity: .7; }
.bv-modal .modal-body { padding: 0.75rem; }
.bv-modal .modal-footer { border-top: 1px solid rgba(0,0,0,.05); padding: 0.5rem 0.75rem; }
.bv-modal .st-btn {
    display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    padding: 0.5rem 0.75rem; border-radius: 0.45rem; border: 1px solid;
    font-size: 0.78rem; font-weight: 600; cursor: pointer; width: 100%;
    transition: all .15s;
}
.bv-modal .st-good { background: rgba(52,195,143,.06); color: #1a8754; border-color: rgba(52,195,143,.25); }
.bv-modal .st-good:hover { background: #1a8754; color: #fff; }
.bv-modal .st-avg  { background: rgba(241,180,76,.06); color: #b87a14; border-color: rgba(241,180,76,.25); }
.bv-modal .st-avg:hover { background: #b87a14; color: #fff; }
.bv-modal .st-bad  { background: rgba(244,106,106,.06); color: #c84646; border-color: rgba(244,106,106,.25); }
.bv-modal .st-bad:hover { background: #c84646; color: #fff; }
.bv-modal textarea.form-control { border-radius: 0.4rem; font-size: 0.78rem; }
.bv-modal textarea.form-control:focus { border-color: var(--bs-gold, #d4af37); box-shadow: 0 0 0 2px rgba(212,175,55,.1); }
.bv-modal .btn-cancel {
    background: var(--bs-surface-100); color: var(--bs-surface-500); border: none;
    border-radius: 1rem; padding: 0.3rem 0.8rem; font-size: 0.72rem; font-weight: 600;
}

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

/* ── Toast ── */
.sl-toast {
    position: fixed; top: 16px; right: 16px; z-index: 9999;
    background: var(--bs-card-bg); color: inherit;
    border: 1px solid rgba(52,195,143,.3);
    border-radius: 0.5rem; padding: 0.5rem 0.85rem;
    font-size: 0.75rem; box-shadow: 0 4px 16px rgba(0,0,0,.1);
    display: flex; align-items: center; gap: 0.4rem;
    animation: slToastIn .3s ease;
}
.sl-toast i { color: #1a8754; font-size: 0.9rem; }
@keyframes slToastIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

@media(max-width:768px){
    .kpi-card .k-val { font-size: 1.1rem; }
    .filter-form { flex-direction: column; }
    .filter-form .f-input { width: 100%; }
}
</style>
@endsection

@section('content')
    @php
        $totalBv = $good_count + $average_count + $bad_count + $unverified_count;
        $goodPct = $totalBv > 0 ? round(($good_count / $totalBv) * 100, 1) : 0;
        $avgPct = $totalBv > 0 ? round(($average_count / $totalBv) * 100, 1) : 0;
        $badPct = $totalBv > 0 ? round(($bad_count / $totalBv) * 100, 1) : 0;
        $unvPct = $totalBv > 0 ? round(($unverified_count / $totalBv) * 100, 1) : 0;
    @endphp

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:0.5rem; border:none; background:rgba(52,195,143,.08); color:#1a8754; font-size:.78rem; padding:.5rem .75rem;">
            <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem;padding:.6rem;"></button>
        </div>
    @endif

    {{-- KPI Row --}}
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-data k-icon"></i>
            <div class="k-val">{{ $totalBv }}</div>
            <div class="k-lbl">Total Records</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-circle k-icon"></i>
            <div class="k-val">{{ $good_count }}</div>
            <div class="k-sub" style="color:#1a8754;">{{ $goodPct }}%</div>
            <div class="k-lbl">Good</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-error-circle k-icon"></i>
            <div class="k-val">{{ $average_count }}</div>
            <div class="k-sub" style="color:#b87a14;">{{ $avgPct }}%</div>
            <div class="k-lbl">Average</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val">{{ $bad_count }}</div>
            <div class="k-sub" style="color:#c84646;">{{ $badPct }}%</div>
            <div class="k-lbl">Bad</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-help-circle k-icon"></i>
            <div class="k-val">{{ $unverified_count }}</div>
            <div class="k-sub" style="color:#5b49c7;">{{ $unvPct }}%</div>
            <div class="k-lbl">Unverified</div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row g-2">
        {{-- LEFT: Charts --}}
        <div class="col-xl-9 col-lg-8">
            <div class="row g-2 mb-2">
                <div class="col-md-5">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr">
                            <h6><i class="bx bx-pie-chart-alt-2"></i> Distribution</h6>
                        </div>
                        <div class="sec-body" style="padding:0.4rem 0.5rem;">
                            <div id="bvDonutChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr">
                            <h6><i class="bx bx-bar-chart-alt-2"></i> Status Breakdown</h6>
                        </div>
                        <div class="sec-body" style="padding:0.4rem 0.5rem;">
                            <div id="bvBarChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Verification Table --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-list-ul"></i> Verification List</h6>
                    <span style="font-size:0.62rem; color:var(--bs-surface-400);">{{ $leads->total() }} records</span>
                </div>
                <form method="GET" action="{{ route('bank-verification.index') }}" class="filter-form">
                    <input type="text" name="search" class="f-input" style="min-width:160px;" placeholder="Search name, phone, policy..." value="{{ request('search') }}">
                    <select name="verification_status" class="sl-pill-select">
                        <option value="">All Status</option>
                        <option value="Good" {{ request('verification_status') == 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Average" {{ request('verification_status') == 'Average' ? 'selected' : '' }}>Average</option>
                        <option value="Bad" {{ request('verification_status') == 'Bad' ? 'selected' : '' }}>Bad</option>
                    </select>
                    <select name="month" class="sl-pill-select">
                        <option value="">Month</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                    <select name="year" class="sl-pill-select">
                        <option value="">Year</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="f-btn"><i class="bx bx-search"></i> Filter</button>
                    @if(request()->hasAny(['search','verification_status','month','year']))
                        <a href="{{ route('bank-verification.index') }}" class="f-reset"><i class="bx bx-reset"></i> Clear</a>
                    @endif
                </form>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Carrier</th>
                                <th>Policy #</th>
                                <th class="text-center">Premium</th>
                                <th>Issued</th>
                                <th style="min-width:110px;">Assigned B.V</th>
                                <th style="min-width:130px;">B.V Assigned By</th>
                                <th style="min-width:130px;">Comment</th>
                                <th style="min-width:90px;">Bank Status</th>
                                <th>Reason</th>
                                <th style="min-width:120px;">Verified By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $idx => $lead)
                                <tr>
                                    <td style="color:var(--bs-surface-400);">{{ $leads->firstItem() + $idx }}</td>
                                    <td><strong>{{ $lead->cn_name }}</strong></td>
                                    <td>{{ $lead->phone_number }}</td>
                                    <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                    <td><code style="font-size:.68rem;">{{ $lead->issued_policy_number ?? 'N/A' }}</code></td>
                                    <td class="text-center"><span class="bd-mini bd-gold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</span></td>
                                    <td style="white-space:nowrap; font-size:.7rem;">{{ $lead->issuance_date ? \Carbon\Carbon::parse($lead->issuance_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <select class="form-select form-select-sm assigned-bv-dropdown" data-lead-id="{{ $lead->id }}">
                                            <option value="">Unassigned</option>
                                            @foreach($bankVerifiers as $verifier)
                                                <option value="{{ $verifier->id }}" {{ $lead->assigned_bank_verifier == $verifier->id ? 'selected' : '' }}>{{ $verifier->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        @if($lead->bankVerifierAssignedByUser)
                                            <strong style="font-size:.72rem;">{{ $lead->bankVerifierAssignedByUser->name }}</strong>
                                            @if($lead->bank_verifier_assigned_at)
                                                <div style="font-size:.62rem;color:var(--bs-surface-400);margin-top:1px">
                                                    {{ \Carbon\Carbon::parse($lead->bank_verifier_assigned_at)->format('M d, h:i A') }}
                                                </div>
                                            @endif
                                        @else
                                            <span style="color:var(--bs-surface-400);font-size:.7rem">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm bv-comment-field" data-lead-id="{{ $lead->id }}" rows="1" placeholder="Add comment..." style="font-size:.7rem;">{{ $lead->bank_verification_comment }}</textarea>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm bv-status-select" data-lead-id="{{ $lead->id }}">
                                            <option value="">Not Set</option>
                                            <option value="Good" {{ $lead->bank_verification_status === 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Average" {{ $lead->bank_verification_status === 'Average' ? 'selected' : '' }}>Average</option>
                                            <option value="Bad" {{ $lead->bank_verification_status === 'Bad' ? 'selected' : '' }}>Bad</option>
                                        </select>
                                    </td>
                                    <td><small style="color:var(--bs-surface-400); font-size:.68rem;">{{ $lead->bank_verification_notes ?? '—' }}</small></td>
                                    <td>
                                        @if($lead->bankVerifiedByUser)
                                            <strong style="font-size:.72rem;">{{ $lead->bankVerifiedByUser->name }}</strong>
                                            @if($lead->bank_verification_date)
                                                <div style="font-size:.62rem;color:var(--bs-surface-400);margin-top:1px">
                                                    {{ \Carbon\Carbon::parse($lead->bank_verification_date)->format('M d, h:i A') }}
                                                </div>
                                            @endif
                                        @else
                                            <span style="color:var(--bs-surface-400);font-size:.7rem">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('bank-verification.show', $lead->id) }}" class="a-btn a-view" title="View">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <button class="a-btn a-edit" data-bs-toggle="modal" data-bs-target="#verificationModal-{{ $lead->id }}" title="Update">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Verification Modal -->
                                <div class="modal fade bv-modal" id="verificationModal-{{ $lead->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="bx bx-building-house"></i> {{ $lead->cn_name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.6rem;"></button>
                                            </div>
                                            <form action="{{ route('bank-verification.update', $lead->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <label class="form-label fw-semibold mb-2" style="font-size:.72rem;">Verification Status</label>
                                                    <div class="d-grid gap-2 mb-3">
                                                        <button type="submit" name="bank_verification_status" value="Good" class="st-btn st-good">
                                                            <i class="bx bx-check-circle"></i> Good
                                                        </button>
                                                        <button type="submit" name="bank_verification_status" value="Average" class="st-btn st-avg">
                                                            <i class="bx bx-error-circle"></i> Average
                                                        </button>
                                                        <button type="submit" name="bank_verification_status" value="Bad" class="st-btn st-bad">
                                                            <i class="bx bx-x-circle"></i> Bad
                                                        </button>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="notes-{{ $lead->id }}" class="form-label fw-semibold" style="font-size:.72rem;">Reason / Notes</label>
                                                        <textarea class="form-control" id="notes-{{ $lead->id }}" name="bank_verification_notes" rows="2" placeholder="Add notes..." style="font-size:.75rem;">{{ $lead->bank_verification_notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-3" style="color:var(--bs-surface-400); font-size:.78rem;">
                                        <i class="bx bx-inbox" style="font-size:1.5rem; opacity:.4;"></i>
                                        <p class="mt-1 mb-0">No approved & issued sales found</p>
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
        </div>

        {{-- RIGHT: Ratio Blocks --}}
        <div class="col-xl-3 col-lg-4">
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-analyse"></i> Verification Ratios</h6>
                </div>
                <div class="sec-body">
                    <div style="display:flex; gap:0.4rem;">
                        <div style="flex:1; text-align:center; padding:0.5rem 0.3rem; border-radius:0.45rem; border:1px solid rgba(52,195,143,.3); background:rgba(52,195,143,.04);">
                            <div style="font-size:1.2rem; font-weight:700; color:#1a8754;">{{ $goodPct }}%</div>
                            <div style="font-size:0.55rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); margin-top:0.15rem;">Good</div>
                        </div>
                        <div style="flex:1; text-align:center; padding:0.5rem 0.3rem; border-radius:0.45rem; border:1px solid rgba(241,180,76,.3); background:rgba(241,180,76,.04);">
                            <div style="font-size:1.2rem; font-weight:700; color:#b87a14;">{{ $avgPct }}%</div>
                            <div style="font-size:0.55rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); margin-top:0.15rem;">Average</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:0.4rem; margin-top:0.4rem;">
                        <div style="flex:1; text-align:center; padding:0.5rem 0.3rem; border-radius:0.45rem; border:1px solid rgba(244,106,106,.3); background:rgba(244,106,106,.04);">
                            <div style="font-size:1.2rem; font-weight:700; color:#c84646;">{{ $badPct }}%</div>
                            <div style="font-size:0.55rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); margin-top:0.15rem;">Bad</div>
                        </div>
                        <div style="flex:1; text-align:center; padding:0.5rem 0.3rem; border-radius:0.45rem; border:1px solid rgba(124,105,239,.3); background:rgba(124,105,239,.04);">
                            <div style="font-size:1.2rem; font-weight:700; color:#5b49c7;">{{ $unvPct }}%</div>
                            <div style="font-size:0.55rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); margin-top:0.15rem;">Unverified</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('partials.sl-filter-assets')

@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
$(document).ready(function() {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.getAttribute('data-theme') === 'dark';
    const txtColor = isDark ? '#94a3b8' : '#64748b';
    const bgCard = isDark ? '#1e293b' : '#fff';

    // Donut Chart
    const donutData = [{{ $good_count }}, {{ $average_count }}, {{ $bad_count }}, {{ $unverified_count }}];
    if (donutData.some(v => v > 0)) {
        new ApexCharts(document.querySelector('#bvDonutChart'), {
            series: donutData,
            chart: { type: 'donut', height: 200, fontFamily: 'inherit' },
            labels: ['Good', 'Average', 'Bad', 'Unverified'],
            colors: ['#34c38f', '#f1b44c', '#f46a6a', '#7c69ef'],
            stroke: { width: 2, colors: [bgCard] },
            legend: { position: 'bottom', fontSize: '10px', labels: { colors: txtColor } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 } },
            plotOptions: { pie: { donut: { size: '58%', labels: {
                show: true, total: { show: true, label: 'Total', fontSize: '10px', color: txtColor,
                    formatter: () => {{ $good_count + $average_count + $bad_count + $unverified_count }}
                }
            } } } },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        }).render();
    } else {
        document.querySelector('#bvDonutChart').innerHTML = '<div style="text-align:center;padding:40px 0;color:' + txtColor + '"><i class="bx bx-pie-chart-alt-2" style="font-size:1.5rem;opacity:.4;"></i><p style="margin-top:4px;font-size:.72rem;">No data</p></div>';
    }

    // Bar Chart
    new ApexCharts(document.querySelector('#bvBarChart'), {
        series: [{ name: 'Count', data: [{{ $good_count }}, {{ $average_count }}, {{ $bad_count }}, {{ $unverified_count }}] }],
        chart: { type: 'bar', height: 200, fontFamily: 'inherit', toolbar: { show: false } },
        colors: ['#34c38f', '#f1b44c', '#f46a6a', '#7c69ef'],
        plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '55%' } },
        xaxis: { categories: ['Good', 'Average', 'Bad', 'Unverified'], labels: { style: { colors: txtColor, fontSize: '10px' } } },
        yaxis: { labels: { style: { colors: txtColor, fontSize: '10px' } } },
        legend: { show: false },
        grid: { borderColor: isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)', strokeDashArray: 4 },
        dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700, colors: ['#fff'] } },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();

    // ── Handle assigned bank verifier dropdown change ──
    $('.assigned-bv-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const verifierId = $(this).val();
        const dropdown = $(this);

        if (confirm('Assign this bank verifier?')) {
            dropdown.prop('disabled', true);
            $.ajax({
                url: '/bank-verification/' + leadId + '/assign-verifier',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', assigned_bank_verifier: verifierId || null },
                success: function(response) {
                    if (response.success) { slToast(response.message); }
                    dropdown.prop('disabled', false);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to assign bank verifier');
                    dropdown.prop('disabled', false);
                    location.reload();
                }
            });
        } else { location.reload(); }
    });

    // ── Auto-save comment and status ──
    $('.bv-comment-field, .bv-status-select').on('change blur', function() {
        const row = $(this).closest('tr');
        const leadId = $(this).data('lead-id');
        const comment = row.find('.bv-comment-field').val();
        const status = row.find('.bv-status-select').val();

        clearTimeout(window.bvUpdateTimeout);
        window.bvUpdateTimeout = setTimeout(function() {
            $.ajax({
                url: '/bank-verification/' + leadId + '/update-assignment',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', bank_verification_comment: comment, bank_verification_status: status || null },
                success: function(response) { if (response.success) { slToast('Updated successfully'); } },
                error: function(xhr) { alert(xhr.responseJSON?.message || 'Failed to update'); }
            });
        }, 1000);
    });

    function slToast(msg) {
        const t = $('<div class="sl-toast"><i class="bx bx-check-circle"></i>' + msg + '</div>');
        $('body').append(t);
        setTimeout(() => t.fadeOut(300, function(){ $(this).remove(); }), 3000);
    }
});
</script>
@endsection
