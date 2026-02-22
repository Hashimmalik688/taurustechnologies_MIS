@use('App\Support\Roles')
@use('App\Support\Statuses')
@extends('layouts.master')

@section('title')
    QA Review
@endsection

@section('css')
<style>
    /* ── QA Review — Premium Design System (.sl-* namespace) ── */

    /* Top bar */
    .sl-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; flex-wrap: wrap; gap: .75rem;
    }
    .sl-topbar-left { display: flex; align-items: center; gap: .75rem; }
    .sl-page-title {
        font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-page-title i { color: #d4af37; font-size: 1.2rem; }
    .sl-topbar-right { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

    /* Search */
    .sl-search-wrap { position: relative; display: flex; align-items: center; }
    .sl-search-icon { position: absolute; left: .6rem; color: #94a3b8; font-size: .9rem; pointer-events: none; }
    .sl-search-input {
        padding: .42rem .65rem .42rem 2rem;
        font-size: .78rem; border: 1px solid rgba(0,0,0,.1);
        border-radius: 22px; background: #fff; width: 260px;
        outline: none; transition: border-color .15s;
    }
    .sl-search-input:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    /* KPI Row */
    .sl-kpi-row {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: .75rem; margin-bottom: 1rem;
    }
    .sl-kpi {
        background: rgba(255,255,255,.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
        padding: .85rem 1rem;
        display: flex; align-items: center; gap: .75rem;
        transition: transform .15s, box-shadow .15s;
    }
    .sl-kpi:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.06); }
    .sl-kpi-icon {
        width: 42px; height: 42px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem; color: #fff; flex-shrink: 0;
    }
    .sl-kpi-icon.total { background: linear-gradient(135deg, #d4af37, #b8941f); }
    .sl-kpi-icon.pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .sl-kpi-icon.good { background: linear-gradient(135deg, #10b981, #059669); }
    .sl-kpi-icon.issues { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .sl-kpi-info { display: flex; flex-direction: column; }
    .sl-kpi-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; }
    .sl-kpi-val { font-size: 1.35rem; font-weight: 800; color: #1e293b; line-height: 1.1; }
    .sl-kpi-sub { font-size: .6rem; color: #94a3b8; font-weight: 500; }

    /* Charts Row */
    .sl-charts-row {
        display: grid; grid-template-columns: 1fr 1fr 1fr;
        gap: .75rem; margin-bottom: 1rem;
    }
    .sl-chart-card {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
        padding: 1rem 1.1rem;
        overflow: hidden;
    }
    .sl-chart-title {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #64748b; margin-bottom: .75rem;
        display: flex; align-items: center; gap: .35rem;
    }
    .sl-chart-title i { color: #d4af37; font-size: .9rem; }

    /* Card */
    .sl-card {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
        overflow: hidden;
    }

    /* Filter Pills */
    .sl-filter-pills {
        display: flex; align-items: center; gap: .4rem;
        padding: .6rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
        background: rgba(248,250,252,.6);
        border-radius: 16px 16px 0 0;
        flex-wrap: wrap;
    }
    .sl-pill-select, .sl-pill-date {
        font-size: .72rem; font-weight: 600;
        padding: .32rem .55rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
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
    .sl-pill-date {
        min-width: 130px; color-scheme: light;
    }
    .sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .sl-pill-label {
        font-size: .62rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #94a3b8; margin-right: -2px;
    }
    .sl-pill-clear {
        font-size: .68rem; font-weight: 600; color: #ef4444;
        text-decoration: none; padding: .25rem .5rem;
        border-radius: 22px; border: 1px solid rgba(239,68,68,.2);
        display: inline-flex; align-items: center; gap: 2px;
        transition: all .15s;
    }
    .sl-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }
    .sl-result-count {
        font-size: .72rem; font-weight: 600; color: #94a3b8;
        margin-left: auto;
    }

    /* Table area */
    .sl-tbl-wrap {
        overflow-x: auto; overflow-y: auto;
        max-height: 520px;
        scrollbar-width: thin; scrollbar-color: #d4af37 transparent;
    }
    .sl-tbl-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
    .sl-tbl-wrap::-webkit-scrollbar-track { background: transparent; }
    .sl-tbl-wrap::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 3px; }

    .sl-tbl {
        width: 100%; border-collapse: separate; border-spacing: 0; font-size: .78rem;
    }
    .sl-tbl thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #64748b;
        padding: .65rem .6rem;
        border-bottom: 1px solid rgba(212,175,55,.18);
        white-space: nowrap;
        position: sticky; top: 0; z-index: 10;
    }
    .sl-tbl tbody td {
        padding: .55rem .6rem;
        border-bottom: 1px solid rgba(0,0,0,.04);
        vertical-align: middle; color: #334155;
        transition: background .12s;
    }
    .sl-tbl tbody tr { transition: background .12s; }
    .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.045); }
    .sl-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.45); }
    .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.045); }

    /* Bubble dropdowns (QA status) */
    .sl-bubble-select {
        font-size: .73rem; font-weight: 600;
        padding: .3rem .55rem; padding-right: 1.6rem;
        border-radius: 22px;
        border: 1px solid rgba(0,0,0,.09);
        background: #fff; color: #334155;
        cursor: pointer; outline: none;
        transition: border-color .15s, box-shadow .15s;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .5rem center;
        min-width: 110px;
    }
    .sl-bubble-select:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    /* Bubble textarea */
    .sl-bubble-textarea {
        font-size: .73rem; padding: .35rem .65rem;
        border-radius: 16px; border: 1px solid rgba(0,0,0,.09);
        background: #fff; color: #334155; resize: vertical;
        transition: border-color .15s, box-shadow .15s; outline: none;
        min-height: 36px; width: 100%;
    }
    .sl-bubble-textarea:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .sl-bubble-textarea::placeholder { color: #b0b8c4; font-weight: 400; }

    /* Save / action pill buttons */
    .sl-save-btn {
        display: inline-flex; align-items: center; gap: .25rem;
        font-size: .68rem; font-weight: 600;
        padding: .22rem .55rem; border-radius: 22px; border: none;
        cursor: pointer; transition: all .15s; margin-top: 4px;
        color: #fff;
    }
    .sl-save-btn.primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .sl-save-btn.primary:hover { box-shadow: 0 2px 8px rgba(59,130,246,.3); transform: translateY(-1px); }
    .sl-save-btn.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .sl-save-btn.warning:hover { box-shadow: 0 2px 8px rgba(245,158,11,.3); transform: translateY(-1px); }

    /* Closer badge */
    .sl-closer-badge {
        display: inline-flex; align-items: center;
        font-size: .68rem; font-weight: 600;
        padding: .2rem .5rem; border-radius: 22px;
        background: rgba(6,182,212,.1); color: #0891b2;
    }

    /* Carrier badge */
    .sl-carrier-badge {
        display: inline-flex; align-items: center;
        font-size: .68rem; font-weight: 600;
        padding: .18rem .45rem; border-radius: 22px;
        background: rgba(212,175,55,.1); color: #92760d;
    }

    /* QA Status badges */
    .sl-qa-good { color: #059669; }
    .sl-qa-pending { color: #d97706; }
    .sl-qa-avg { color: #6366f1; }
    .sl-qa-bad { color: #dc2626; }

    /* Pagination */
    .sl-card .mt-3 { padding: 0 1rem .75rem; }
    .sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

    /* ── Dark mode ── */
    [data-theme="dark"] .sl-page-title { color: #f1f5f9; }
    [data-theme="dark"] .sl-search-input {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    [data-theme="dark"] .sl-search-input:focus { border-color: #d4af37; }
    [data-theme="dark"] .sl-kpi {
        background: rgba(30,41,59,.7); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-kpi-label { color: #94a3b8; }
    [data-theme="dark"] .sl-kpi-val { color: #f1f5f9; }
    [data-theme="dark"] .sl-kpi-sub { color: #64748b; }
    [data-theme="dark"] .sl-chart-card {
        background: rgba(30,41,59,.7); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-chart-title { color: #94a3b8; }
    [data-theme="dark"] .sl-card {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-filter-pills {
        background: rgba(15,23,42,.4); border-color: rgba(255,255,255,.05);
    }
    [data-theme="dark"] .sl-pill-select,
    [data-theme="dark"] .sl-pill-date {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        color-scheme: dark;
    }
    [data-theme="dark"] .sl-pill-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    [data-theme="dark"] .sl-pill-clear { border-color: rgba(239,68,68,.3); }
    [data-theme="dark"] .sl-tbl thead th {
        background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9));
        color: #94a3b8; border-color: rgba(212,175,55,.12);
    }
    [data-theme="dark"] .sl-tbl tbody td {
        color: #cbd5e1; border-color: rgba(255,255,255,.04);
    }
    [data-theme="dark"] .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.02); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.06); }
    [data-theme="dark"] .sl-bubble-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    [data-theme="dark"] .sl-bubble-textarea {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    [data-theme="dark"] .sl-bubble-textarea::placeholder { color: #475569; }
    [data-theme="dark"] .sl-closer-badge { background: rgba(6,182,212,.15); color: #22d3ee; }
    [data-theme="dark"] .sl-carrier-badge { background: rgba(212,175,55,.15); color: #d4af37; }

    /* Responsiveness */
    @media (max-width: 992px) {
        .sl-charts-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .sl-kpi-row { grid-template-columns: repeat(2, 1fr); }
        .sl-topbar { flex-direction: column; align-items: flex-start; }
        .sl-topbar-right { width: 100%; }
        .sl-search-input { width: 100% !important; }
    }
</style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert" style="border-radius:14px">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Top bar -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title"><i class="mdi mdi-clipboard-check-outline"></i> QA Review</h5>
        </div>
        <div class="sl-topbar-right">
            <div class="sl-search-wrap">
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" id="qaSearch" class="sl-search-input" placeholder="Search name, phone, carrier, closer..." value="{{ request('search') }}">
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="sl-kpi-row">
        <div class="sl-kpi">
            <div class="sl-kpi-icon total"><i class="mdi mdi-chart-bar"></i></div>
            <div class="sl-kpi-info">
                <span class="sl-kpi-label">Total Sales</span>
                <span class="sl-kpi-val">{{ number_format($qaAnalytics['total']) }}</span>
            </div>
        </div>
        <div class="sl-kpi">
            <div class="sl-kpi-icon pending"><i class="mdi mdi-clock-outline"></i></div>
            <div class="sl-kpi-info">
                <span class="sl-kpi-label">Pending</span>
                <span class="sl-kpi-val">{{ number_format($qaAnalytics['pending']) }}</span>
                <span class="sl-kpi-sub">{{ $qaAnalytics['pending_percent'] }}% awaiting review</span>
            </div>
        </div>
        <div class="sl-kpi">
            <div class="sl-kpi-icon good"><i class="mdi mdi-check-circle"></i></div>
            <div class="sl-kpi-info">
                <span class="sl-kpi-label">Good</span>
                <span class="sl-kpi-val">{{ number_format($qaAnalytics['good']) }}</span>
                <span class="sl-kpi-sub">{{ $qaAnalytics['good_percent'] }}% passed</span>
            </div>
        </div>
        <div class="sl-kpi">
            <div class="sl-kpi-icon issues"><i class="mdi mdi-alert-circle"></i></div>
            <div class="sl-kpi-info">
                <span class="sl-kpi-label">Issues (Avg + Bad)</span>
                <span class="sl-kpi-val">{{ number_format($qaAnalytics['avg'] + $qaAnalytics['bad']) }}</span>
                <span class="sl-kpi-sub">{{ $qaAnalytics['issues_percent'] }}% flagged</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="sl-charts-row">
        <!-- QA Status Distribution (Donut) -->
        <div class="sl-chart-card">
            <div class="sl-chart-title"><i class="mdi mdi-chart-donut"></i> Status Distribution</div>
            <div id="statusDonutChart" style="min-height:220px"></div>
        </div>

        <!-- Top Closers by QA (Bar) -->
        <div class="sl-chart-card">
            <div class="sl-chart-title"><i class="mdi mdi-account-group"></i> Top Closers</div>
            <div id="closerBarChart" style="min-height:220px"></div>
        </div>

        <!-- Daily Trend (Area) -->
        <div class="sl-chart-card">
            <div class="sl-chart-title"><i class="mdi mdi-chart-timeline-variant"></i> 14-Day Trend</div>
            <div id="dailyTrendChart" style="min-height:220px"></div>
        </div>
    </div>

    <!-- QA Table Card -->
    <div class="sl-card">
        <!-- Filter Pills -->
        <form method="GET" action="{{ route('qa.review') }}" id="qaFilterForm" class="sl-filter-pills">
            <select name="carrier" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Carriers</option>
                @foreach($carriers as $carrier)
                    <option value="{{ $carrier }}" {{ request('carrier') == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                @endforeach
            </select>
            <select name="qa_status" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All QA Status</option>
                <option value="Pending" {{ request('qa_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Good" {{ request('qa_status') == 'Good' ? 'selected' : '' }}>Good</option>
                <option value="Avg" {{ request('qa_status') == 'Avg' ? 'selected' : '' }}>Avg</option>
                <option value="Bad" {{ request('qa_status') == 'Bad' ? 'selected' : '' }}>Bad</option>
            </select>
            <select name="closer" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Closers</option>
                @foreach($closers as $closer)
                    <option value="{{ $closer }}" {{ request('closer') == $closer ? 'selected' : '' }}>{{ $closer }}</option>
                @endforeach
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" class="sl-pill-date" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" class="sl-pill-date" value="{{ request('date_to') }}" onchange="this.form.submit()">
            @if(request()->hasAny(['carrier','qa_status','closer','date_from','date_to','search']))
                <a href="{{ route('qa.review') }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="sl-result-count">{{ $leads->total() }} sales</span>
        </form>

        <!-- Table -->
        <div class="sl-tbl-wrap">
            <table class="sl-tbl" id="qaTable">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="min-width:150px">Client Name</th>
                        <th style="min-width:110px">Carrier</th>
                        <th style="min-width:120px">Closer</th>
                        <th style="min-width:95px">Sale Date</th>
                        <th style="min-width:100px">Premium</th>
                        <th style="min-width:130px">QA Status</th>
                        <th style="min-width:280px">QA Reason / Notes</th>
                        <th style="min-width:150px">Reviewed By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $index => $lead)
                        <tr>
                            <td><strong>{{ $leads->firstItem() + $index }}</strong></td>
                            <td>
                                <strong>{{ $lead->cn_name }}</strong>
                                @if($lead->phone_number && $lead->phone_number !== 'N/A')
                                    <div style="font-size:.65rem;color:#94a3b8;margin-top:1px">{{ $lead->phone_number }}</div>
                                @endif
                            </td>
                            <td>
                                @if($lead->carrier_name)
                                    <span class="sl-carrier-badge">{{ $lead->carrier_name }}</span>
                                @else
                                    <span style="color:#94a3b8">--</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->closer_name)
                                    <span class="sl-closer-badge">{{ $lead->closer_name }}</span>
                                @else
                                    <span style="color:#94a3b8">--</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->sale_date)
                                    {{ \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') }}
                                @elseif($lead->sale_at)
                                    {{ \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($lead->monthly_premium)
                                    <strong>${{ number_format($lead->monthly_premium, 2) }}</strong>
                                @else
                                    <span style="color:#94a3b8">--</span>
                                @endif
                            </td>
                            <td>
                                <select class="sl-bubble-select qa-status-dropdown"
                                        data-lead-id="{{ $lead->id }}"
                                        data-current-status="{{ $lead->qa_status ?? Statuses::QA_PENDING }}">
                                    <option value="Pending" {{ ($lead->qa_status ?? Statuses::QA_PENDING) == Statuses::QA_PENDING ? 'selected' : '' }}>Pending</option>
                                    <option value="Good" {{ ($lead->qa_status ?? '') == Statuses::QA_GOOD ? 'selected' : '' }}>Good</option>
                                    <option value="Avg" {{ ($lead->qa_status ?? '') == Statuses::QA_AVG ? 'selected' : '' }}>Avg</option>
                                    <option value="Bad" {{ ($lead->qa_status ?? '') == Statuses::QA_BAD ? 'selected' : '' }}>Bad</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="sl-bubble-textarea qa-reason-input"
                                          data-lead-id="{{ $lead->id }}"
                                          placeholder="Enter QA reason/comment..."
                                          rows="2">{{ $lead->qa_reason ?? '' }}</textarea>
                                <div style="display:flex;gap:4px">
                                    <button class="sl-save-btn primary save-qa-reason" data-lead-id="{{ $lead->id }}">
                                        <i class="bx bx-save"></i> Save
                                    </button>
                                    @if(auth()->user()->hasRole(Roles::SUPER_ADMIN) && $lead->qa_status !== Statuses::QA_PENDING)
                                        <button class="sl-save-btn warning reset-qa-status" data-lead-id="{{ $lead->id }}" title="Reset to Pending">
                                            <i class="bx bx-undo"></i> Reset
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($lead->qaUser)
                                    <strong style="font-size:.78rem">{{ $lead->qaUser->name }}</strong>
                                    @if($lead->qa_reviewed_at)
                                        <div style="font-size:.65rem;color:#94a3b8;margin-top:2px">
                                            {{ \Carbon\Carbon::parse($lead->qa_reviewed_at)->format('M d, Y') }}
                                            <br>{{ \Carbon\Carbon::parse($lead->qa_reviewed_at)->format('h:i A') }}
                                        </div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.75rem">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bx bx-inbox" style="font-size:2rem;color:#94a3b8"></i>
                                <p class="mb-0 text-muted" style="font-size:.82rem">No sales data available for QA review</p>
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
@endsection

@section('script')
@include('partials.sl-filter-assets')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
$(document).ready(function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const txtColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';

    // ── Status Donut Chart ──
    new ApexCharts(document.querySelector('#statusDonutChart'), {
        chart: { type: 'donut', height: 220, background: 'transparent' },
        series: [{{ $qaAnalytics['good'] }}, {{ $qaAnalytics['pending'] }}, {{ $qaAnalytics['avg'] }}, {{ $qaAnalytics['bad'] }}],
        labels: ['Good', 'Pending', 'Avg', 'Bad'],
        colors: ['#10b981', '#f59e0b', '#6366f1', '#ef4444'],
        stroke: { width: 2, colors: [isDark ? '#1e293b' : '#fff'] },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: { fontSize: '12px', color: txtColor },
                        value: { fontSize: '18px', fontWeight: 800, color: isDark ? '#f1f5f9' : '#1e293b' },
                        total: {
                            show: true, label: 'Total',
                            fontSize: '11px', color: txtColor,
                            fontWeight: 700,
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a,b) => a+b, 0);
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px', labels: { colors: txtColor }, markers: { radius: 12 } },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();

    // ── Top Closers Bar Chart ──
    const closerNames = @json($closerStats->pluck('closer_name'));
    const closerGood = @json($closerStats->pluck('good'));
    const closerPending = @json($closerStats->pluck('pending'));
    const closerIssues = @json($closerStats->pluck('issues'));

    new ApexCharts(document.querySelector('#closerBarChart'), {
        chart: { type: 'bar', height: 220, stacked: true, background: 'transparent', toolbar: { show: false } },
        series: [
            { name: 'Good', data: closerGood },
            { name: 'Pending', data: closerPending },
            { name: 'Issues', data: closerIssues }
        ],
        colors: ['#10b981', '#f59e0b', '#ef4444'],
        plotOptions: {
            bar: { horizontal: true, barHeight: '55%', borderRadius: 4, borderRadiusApplication: 'end' }
        },
        xaxis: {
            categories: closerNames,
            labels: { style: { fontSize: '10px', colors: txtColor } }
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: txtColor }, maxWidth: 100 } },
        grid: { borderColor: gridColor, strokeDashArray: 3 },
        legend: { position: 'top', fontSize: '10px', labels: { colors: txtColor }, markers: { radius: 12 } },
        dataLabels: { enabled: false },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();

    // ── Daily Trend Area Chart ──
    const trendDays = @json($dailyTrend->pluck('day'));
    const trendTotal = @json($dailyTrend->pluck('total'));
    const trendGood = @json($dailyTrend->pluck('good'));
    const trendIssues = @json($dailyTrend->pluck('issues'));

    new ApexCharts(document.querySelector('#dailyTrendChart'), {
        chart: { type: 'area', height: 220, background: 'transparent', toolbar: { show: false }, sparkline: { enabled: false } },
        series: [
            { name: 'Total', data: trendTotal },
            { name: 'Good', data: trendGood },
            { name: 'Issues', data: trendIssues }
        ],
        colors: ['#d4af37', '#10b981', '#ef4444'],
        xaxis: {
            categories: trendDays.map(d => {
                const dt = new Date(d + 'T00:00:00');
                return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            labels: { style: { fontSize: '9px', colors: txtColor }, rotate: -45 }
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: txtColor } } },
        grid: { borderColor: gridColor, strokeDashArray: 3 },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .35, opacityTo: .05 } },
        legend: { position: 'top', fontSize: '10px', labels: { colors: txtColor }, markers: { radius: 12 } },
        dataLabels: { enabled: false },
        tooltip: { theme: isDark ? 'dark' : 'light', x: { show: true } }
    }).render();

    // ── Instant search with debounce ──
    let debounceTimer;
    $('#qaSearch').on('input', function() {
        clearTimeout(debounceTimer);
        const val = this.value.trim();
        debounceTimer = setTimeout(() => {
            const form = $('#qaFilterForm');
            let hidden = form.find('input[name="search"]');
            if (!hidden.length) {
                hidden = $('<input type="hidden" name="search">');
                form.append(hidden);
            }
            hidden.val(val);
            form.submit();
        }, 600);
    });

    // ── Handle QA status dropdown changes ──
    $('.qa-status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newQaStatus = $(this).val();
        const currentStatus = $(this).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        updateQaStatus(leadId, newQaStatus, currentStatus, qaReason, $(this));
    });

    // ── Handle QA reason save button ──
    $('.save-qa-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const qaStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val();
        const currentStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        updateQaStatus(leadId, qaStatus, currentStatus, qaReason, $(this));
    });

    // ── Handle reset QA status button (Super Admin only) ──
    $('.reset-qa-status').click(function() {
        const leadId = $(this).data('lead-id');
        const button = $(this);

        if (confirm('Reset this QA status to Pending?')) {
            button.prop('disabled', true);
            $.ajax({
                url: `/sales/${leadId}/qa-status/reset`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        const dropdown = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`);
                        dropdown.val('Pending').data('current-status', 'Pending');
                        $(`.qa-reason-input[data-lead-id="${leadId}"]`).val('');
                        showToast('success', response.message || 'QA status reset.');
                    }
                },
                error: function(xhr) {
                    showToast('error', xhr.responseJSON?.message || 'Failed to reset QA status');
                },
                complete: function() { button.prop('disabled', false); }
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
                    $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status', qaStatus);
                    element.css('border-color', '#10b981');
                    setTimeout(() => { element.css('border-color', ''); }, 2000);
                    showToast('success', response.message || 'QA status updated.');
                }
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Failed to update QA status');
                $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val(currentStatus);
            },
            complete: function() { element.prop('disabled', false); }
        });
    }

    function showToast(type, msg) {
        const bg = type === 'success' ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#ef4444,#dc2626)';
        const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        const html = `<div style="position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;padding:.7rem 1rem;
            border-radius:14px;background:${bg};color:#fff;font-size:.82rem;font-weight:600;
            box-shadow:0 8px 30px rgba(0,0,0,.18);display:flex;align-items:center;gap:.5rem;
            animation:slideIn .3s ease-out" class="sl-toast">
            <i class="bx ${icon}" style="font-size:1.1rem"></i>${msg}
        </div>`;
        const $el = $(html).appendTo('body');
        setTimeout(() => { $el.fadeOut(300, function(){ $(this).remove(); }); }, 3500);
    }
});
</script>
<style>
@keyframes slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>
@endsection
