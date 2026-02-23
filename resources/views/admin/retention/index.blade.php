@extends('layouts.master')
@use('App\Support\Statuses')

@section('title')
    Retention Management
@endsection

@section('css')
<style>
    /* ── Retention Management Design System (.sl-* namespace, matching Sales) ── */

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

    /* KPI summary pills */
    .sl-kpi-row { display: flex; gap: .6rem; flex-wrap: wrap; }
    .sl-kpi-pill {
        display: flex; align-items: center; gap: .5rem;
        padding: .5rem .85rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.06);
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
    }
    .sl-kpi-pill .kpi-icon {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; font-size: .9rem;
    }
    .sl-kpi-pill .kpi-label {
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #94a3b8; line-height: 1.1;
    }
    .sl-kpi-pill .kpi-value { font-size: 1.1rem; font-weight: 800; line-height: 1; }

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
        flex-wrap: wrap;
    }
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
    .sl-pill-date {
        min-width: 100px; max-width: 130px;
        color-scheme: light;
    }
    .sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37 !important; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .sl-pill-label {
        font-size: .65rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .sl-pill-clear {
        font-size: .68rem; font-weight: 600; color: #ef4444;
        text-decoration: none; padding: .25rem .5rem;
        border-radius: 22px; border: 1px solid rgba(239,68,68,.2);
        display: inline-flex; align-items: center; gap: 2px;
        transition: all .15s;
    }
    .sl-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }

    /* Themed tabs */
    .sl-tabs {
        display: flex; gap: 2px; padding: .5rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
        background: rgba(248,250,252,.35);
        flex-wrap: wrap;
    }
    .sl-tab {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .4rem .85rem; border-radius: 22px;
        font-size: .72rem; font-weight: 700;
        color: #64748b; background: transparent;
        border: 1px solid transparent;
        cursor: pointer; text-decoration: none;
        transition: all .15s;
    }
    .sl-tab:hover { color: #d4af37; background: rgba(212,175,55,.06); }
    .sl-tab.active {
        background: linear-gradient(135deg, #d4af37, #b8941f);
        color: #0f172a; border-color: transparent;
        box-shadow: 0 2px 8px rgba(212,175,55,.25);
    }
    .sl-tab .badge {
        font-size: .6rem; padding: .15rem .4rem; border-radius: 10px;
        font-weight: 700;
    }
    .sl-tab.active .badge { background: rgba(0,0,0,.15) !important; color: #fff !important; }

    /* Table area */
    .sl-tbl-wrap {
        overflow-x: auto; overflow-y: auto;
        max-height: 560px;
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
        padding: .45rem .45rem;
        border-bottom: 1px solid rgba(212,175,55,.18);
        white-space: nowrap;
        position: sticky; top: 0; z-index: 10;
    }
    .sl-tbl tbody td {
        padding: .35rem .45rem;
        border-bottom: 1px solid rgba(0,0,0,.04);
        vertical-align: middle; color: #334155;
        transition: background .12s;
    }
    .sl-tbl tbody tr { transition: background .12s; }
    .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.045); }
    .sl-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.45); }
    .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.045); }

    /* Action buttons */
    .sl-act-group { display: flex; gap: 4px; justify-content: center; }
    .sl-act-group .btn {
        width: 28px; height: 28px; padding: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; font-size: .68rem;
        border: none; color: #fff; transition: all .15s;
        box-shadow: 0 1px 3px rgba(0,0,0,.1);
    }
    .sl-act-group .btn:hover { transform: scale(1.1); box-shadow: 0 3px 10px rgba(0,0,0,.15); }
    .sl-act-group .btn-success { background: linear-gradient(135deg, #10b981, #059669); }
    .sl-act-group .btn-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .sl-act-group .btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .sl-act-group .btn-outline-primary {
        background: transparent; border: 1px solid #3b82f6; color: #3b82f6;
    }
    .sl-act-group .btn-outline-primary:hover { background: #3b82f6; color: #fff; }
    .sl-act-group .btn-outline-info {
        background: transparent; border: 1px solid #06b6d4; color: #06b6d4;
    }
    .sl-act-group .btn-outline-info:hover { background: #06b6d4; color: #fff; }

    /* Themed status dropdown */
    .sl-status-select {
        font-size: .72rem; font-weight: 600;
        padding: .28rem .45rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; color: #475569;
        cursor: pointer; outline: none;
        transition: border-color .15s;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .4rem center;
        padding-right: 1.3rem;
        min-width: 110px;
    }
    .sl-status-select:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .sl-status-select.border-success { border-color: #10b981 !important; }

    /* Badges */
    .sl-tbl .badge { font-size: .68rem; font-weight: 600; padding: .25rem .55rem; border-radius: 22px; }

    /* Pagination */
    .sl-card .mt-3 { padding: 0 1rem .75rem; }
    .sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

    /* Disposition modal overrides */
    .disposition-radio:checked + .btn-outline-secondary {
        background-color: var(--bs-status-default, #6c757d);
        color: var(--bs-white); border-color: var(--bs-status-default, #6c757d);
    }
    .disposition-radio:checked + .btn-outline-success {
        background-color: var(--bs-ui-success, #198754);
        color: var(--bs-white); border-color: var(--bs-ui-success, #198754);
    }
    .btn-group { display: flex; border-radius: 0.375rem; }
    .btn-group .btn { border-radius: 0; border-right: 0; padding: 0.6rem 1.2rem; }
    .btn-group .btn:first-child { border-radius: 0.375rem 0 0 0.375rem; }
    .btn-group .btn:last-child { border-right: 1px solid currentColor; border-radius: 0 0.375rem 0.375rem 0; }
    .btn-group .btn-check:checked + .btn { font-weight: 600; }

    /* ── Dark mode ── */
    [data-theme="dark"] .sl-page-title { color: #f1f5f9; }
    [data-theme="dark"] .sl-search-input {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    [data-theme="dark"] .sl-search-input:focus { border-color: #d4af37; }
    [data-theme="dark"] .sl-kpi-pill {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-kpi-pill .kpi-label { color: #64748b; }
    [data-theme="dark"] .sl-card {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-filter-pills {
        background: rgba(15,23,42,.4); border-color: rgba(255,255,255,.05);
    }
    [data-theme="dark"] .sl-pill-select,
    [data-theme="dark"] .sl-pill-date {
        background: rgba(30,41,59,.8) !important; border-color: rgba(255,255,255,.1) !important; color: #cbd5e1;
        color-scheme: dark;
    }
    [data-theme="dark"] .sl-pill-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    [data-theme="dark"] .sl-pill-label { color: #64748b; }
    [data-theme="dark"] .sl-pill-clear { border-color: rgba(239,68,68,.3); }
    [data-theme="dark"] .sl-tabs { background: rgba(15,23,42,.3); border-color: rgba(255,255,255,.05); }
    [data-theme="dark"] .sl-tab { color: #94a3b8; }
    [data-theme="dark"] .sl-tab:hover { color: #d4af37; background: rgba(212,175,55,.08); }
    [data-theme="dark"] .sl-tab.active { color: #0f172a; }
    [data-theme="dark"] .sl-tbl thead th {
        background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9));
        color: #94a3b8; border-color: rgba(212,175,55,.12);
    }
    [data-theme="dark"] .sl-tbl tbody td { color: #cbd5e1; border-color: rgba(255,255,255,.04); }
    [data-theme="dark"] .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.02); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.06); }
    [data-theme="dark"] .sl-status-select {
        background-color: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }

    @media (max-width: 768px) {
        .sl-topbar { flex-direction: column; align-items: flex-start; }
        .sl-topbar-right { width: 100%; }
        .sl-search-input { width: 100% !important; }
        .sl-kpi-row { flex-direction: column; }
        .sl-tabs { gap: 4px; }
    }
</style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Top bar -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title"><i class="mdi mdi-shield-check-outline"></i> Retention Management</h5>
        </div>
        <div class="sl-topbar-right">
            <div class="sl-search-wrap">
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" id="retentionSearch" class="sl-search-input" placeholder="Search name, phone, carrier, closer..." value="{{ $search }}">
            </div>
        </div>
    </div>

    <!-- KPI summary pills -->
    <div class="sl-kpi-row mb-3">
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-danger-subtle text-danger"><i class="bx bx-error"></i></div>
            <div>
                <div class="kpi-label">Total Chargebacks</div>
                <div class="kpi-value text-danger">{{ $cb_count }}</div>
            </div>
        </div>
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-warning-subtle text-warning"><i class="bx bx-refresh"></i></div>
            <div>
                <div class="kpi-label">Rewrite</div>
                <div class="kpi-value text-warning">{{ $rewrite_count }}</div>
            </div>
        </div>
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-info-subtle text-info"><i class="bx bx-time-five"></i></div>
            <div>
                <div class="kpi-label">Yet to Retain</div>
                <div class="kpi-value text-info">{{ $yet_to_retain_count }}</div>
            </div>
        </div>
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-success-subtle text-success"><i class="bx bx-check-circle"></i></div>
            <div>
                <div class="kpi-label">Retained</div>
                <div class="kpi-value text-success">{{ $retained_count }}</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="sl-card">
        <!-- Filter Pills -->
        <form method="GET" action="{{ route('retention.index') }}" id="retFilterForm" class="sl-filter-pills">
            <select name="month" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Years</option>
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" class="sl-pill-date" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" class="sl-pill-date" value="{{ request('date_to') }}" onchange="this.form.submit()">
            @if(request()->hasAny(['search', 'month', 'year', 'date_from', 'date_to']))
                <a href="{{ route('retention.index') }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            @endif
        </form>

        <!-- Themed Tabs -->
        <div class="sl-tabs" role="tablist">
            <a class="sl-tab active" data-bs-toggle="tab" href="#yet-to-retain" role="tab">
                <i class="bx bx-time-five"></i> Yet to Retain
                <span class="badge bg-info">{{ $yet_to_retain_count }}</span>
            </a>
            <a class="sl-tab" data-bs-toggle="tab" href="#retained" role="tab">
                <i class="bx bx-check-circle"></i> Retained
                <span class="badge bg-success">{{ $retained_count }}</span>
            </a>
            <a class="sl-tab" data-bs-toggle="tab" href="#rewrite" role="tab">
                <i class="bx bx-refresh"></i> Rewrite
                <span class="badge bg-warning">{{ $rewrite_count }}</span>
            </a>
            <a class="sl-tab" data-bs-toggle="tab" href="#disposition" role="tab">
                <i class="bx bx-history"></i> Unissued
                <span class="badge bg-danger">{{ $disposition_count }}</span>
            </a>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- YET TO RETAIN TAB -->
            <div class="tab-pane active" id="yet-to-retain" role="tabpanel">
                <div class="sl-tbl-wrap">
                    <table class="sl-tbl">
                        <thead>
                            <tr>
                                <th style="min-width:90px">Call</th>
                                <th style="min-width:50px">ID</th>
                                <th style="min-width:100px">Sale Date</th>
                                <th style="min-width:150px">Customer Name</th>
                                <th style="min-width:120px">Phone</th>
                                <th style="min-width:110px">Closer</th>
                                <th style="min-width:110px">Carrier</th>
                                <th style="min-width:100px">Coverage</th>
                                <th style="min-width:90px">Premium</th>
                                <th style="min-width:120px">Retention Status</th>
                                <th style="min-width:100px">Type</th>
                                <th style="min-width:180px">Manager Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                                        @forelse($yet_to_retain_leads as $lead)
                                            @php
                                                $chargebackDate = $lead->chargeback_marked_date ? \Carbon\Carbon::parse($lead->chargeback_marked_date) : null;
                                                $daysAgo = $chargebackDate ? $chargebackDate->diffInDays(now()) : 0;
                                            @endphp
                                            <tr data-lead-id="{{ $lead->id }}" data-customer-name="{{ $lead->cn_name }}" data-phone="{{ $lead->phone_number }}" data-days-ago="{{ $daysAgo }}">
                                                <td>
                                                    <div class="sl-act-group">
                                                        <button class="btn btn-success dial-lead-btn" 
                                                                data-lead-id="{{ $lead->id }}"
                                                                data-phone="{{ $lead->phone_number }}"
                                                                data-customer-name="{{ $lead->cn_name }}"
                                                                data-days-ago="{{ $daysAgo }}"
                                                                title="Call Customer">
                                                            <i class="bx bx-phone-call"></i>
                                                        </button>
                                                        <button class="btn btn-info show-lead-details-btn" 
                                                                data-lead-id="{{ $lead->id }}"
                                                                data-lead-json="{{ htmlspecialchars(json_encode($lead->toArray()), ENT_QUOTES, 'UTF-8') }}"
                                                                title="View Details">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-primary" title="View Full Profile" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><strong>{{ $lead->id }}</strong></td>
                                                <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : 'N/A' }}</td>
                                                <td><strong>{{ $lead->cn_name }}</strong></td>
                                                <td>{{ $lead->phone_number }}</td>
                                                <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                                <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                                <td class="text-gold fw-semibold">${{ number_format($lead->coverage_amount ?? 0, 2) }}</td>
                                                <td class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                                <td>
                                                    <select class="sl-status-select retention-status-dropdown" 
                                                            data-lead-id="{{ $lead->id }}">
                                                        <option value="pending" {{ ($lead->retention_status == Statuses::RETENTION_PENDING || !$lead->retention_status) ? 'selected' : '' }}>
                                                            Yet to Retain
                                                        </option>
                                                        <option value="retained" {{ $lead->retention_status == Statuses::RETENTION_RETAINED ? 'selected' : '' }}>
                                                            Retained
                                                        </option>
                                                        <option value="rewrite" {{ $lead->retention_status == Statuses::RETENTION_REWRITE ? 'selected' : '' }}>
                                                            Rewrite
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    @if($lead->is_rewrite)
                                                        <span class="badge bg-warning"><i class="bx bx-refresh me-1"></i>REWRITE</span>
                                                    @else
                                                        <span class="badge bg-info"><i class="bx bx-redo me-1"></i>RECOVER</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted" style="font-size:.74rem">{{ $lead->manager_reason ?? 'No comments' }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4">
                                                    <i class="bx bx-inbox" style="font-size:2rem;color:#94a3b8"></i>
                                                    <p class="mb-0 text-muted" style="font-size:.82rem">No leads requiring retention</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- RETAINED TAB -->
                        <div class="tab-pane" id="retained" role="tabpanel">
                            <div class="sl-tbl-wrap">
                                <table class="sl-tbl">
                                    <thead>
                                        <tr>
                                            <th style="min-width:70px">Actions</th>
                                            <th style="min-width:50px">ID</th>
                                            <th style="min-width:100px">Sale Date</th>
                                            <th style="min-width:110px">Retained Date</th>
                                            <th style="min-width:150px">Customer Name</th>
                                            <th style="min-width:120px">Phone</th>
                                            <th style="min-width:110px">Closer</th>
                                            <th style="min-width:110px">Carrier</th>
                                            <th style="min-width:100px">Coverage</th>
                                            <th style="min-width:90px">Premium</th>
                                            <th style="min-width:90px">Status</th>
                                            <th style="min-width:180px">Manager Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($retained_leads as $lead)
                                            <tr>
                                                <td>
                                                    <div class="sl-act-group">
                                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-primary" title="View Details" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><strong>{{ $lead->id }}</strong></td>
                                                <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-calendar-check me-1"></i>{{ $lead->retained_at ? $lead->retained_at->format('M d, Y') : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td><strong>{{ $lead->cn_name }}</strong></td>
                                                <td>{{ $lead->phone_number }}</td>
                                                <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                                <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                                <td class="text-gold fw-semibold">${{ number_format($lead->coverage_amount ?? 0, 2) }}</td>
                                                <td class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>RETAINED</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted" style="font-size:.74rem">{{ $lead->manager_reason ?? 'No comments' }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4">
                                                    <i class="bx bx-inbox" style="font-size:2rem;color:#94a3b8"></i>
                                                    <p class="mb-0 text-muted" style="font-size:.82rem">No retained sales yet</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($retained_leads->hasPages())
                                <div class="mt-3">
                                    {{ $retained_leads->appends(['search' => $search, 'month' => $month, 'year' => $year, 'date_from' => $date_from ?? '', 'date_to' => $date_to ?? ''])->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- REWRITE TAB -->
                        <div class="tab-pane" id="rewrite" role="tabpanel">
                            <div class="sl-tbl-wrap">
                                <table class="sl-tbl">
                                    <thead>
                                        <tr>
                                            <th style="min-width:70px">Actions</th>
                                            <th style="min-width:50px">ID</th>
                                            <th style="min-width:100px">Sale Date</th>
                                            <th style="min-width:100px">CB Date</th>
                                            <th style="min-width:70px">Days Old</th>
                                            <th style="min-width:150px">Customer Name</th>
                                            <th style="min-width:120px">Phone</th>
                                            <th style="min-width:110px">Closer</th>
                                            <th style="min-width:110px">Carrier</th>
                                            <th style="min-width:100px">Coverage</th>
                                            <th style="min-width:90px">Premium</th>
                                            <th style="min-width:120px">Retention Status</th>
                                            <th style="min-width:180px">Manager Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rewrite_leads as $lead)
                                            <tr>
                                                <td>
                                                    <div class="sl-act-group">
                                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-primary" title="View Details" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><strong>{{ $lead->id }}</strong></td>
                                                <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ $lead->chargeback_marked_date ? $lead->chargeback_marked_date->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    @if($lead->chargeback_marked_date && $lead->sale_date)
                                                        <span class="badge bg-warning-subtle text-warning">{{ $lead->chargeback_marked_date->diffInDays($lead->sale_date) }} days</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td><strong>{{ $lead->cn_name }}</strong></td>
                                                <td>{{ $lead->phone_number }}</td>
                                                <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                                <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                                <td class="text-gold fw-semibold">${{ number_format($lead->coverage_amount ?? 0, 2) }}</td>
                                                <td class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                                <td>
                                                    <select class="sl-status-select retention-status-dropdown" 
                                                            data-lead-id="{{ $lead->id }}">
                                                        <option value="pending" {{ ($lead->retention_status == Statuses::RETENTION_PENDING || !$lead->retention_status) ? 'selected' : '' }}>
                                                            Pending
                                                        </option>
                                                        <option value="retained" {{ $lead->retention_status == Statuses::RETENTION_RETAINED ? 'selected' : '' }}>
                                                            Retained
                                                        </option>
                                                        <option value="lost" {{ $lead->retention_status == 'lost' ? 'selected' : '' }}>
                                                            Lost
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <span class="text-muted" style="font-size:.74rem">{{ $lead->manager_reason ?? 'No comments' }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center py-4">
                                                    <i class="bx bx-inbox" style="font-size:2rem;color:#94a3b8"></i>
                                                    <p class="mb-0 text-muted" style="font-size:.82rem">No rewrite cases (chargebacks ≥30 days old)</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($rewrite_leads->hasPages())
                                <div class="mt-3">
                                    {{ $rewrite_leads->appends(['search' => $search, 'month' => $month, 'year' => $year, 'date_from' => $date_from ?? '', 'date_to' => $date_to ?? ''])->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- UNISSUED TAB -->
                        <div class="tab-pane" id="disposition" role="tabpanel">
                            <div class="sl-tbl-wrap">
                                <table class="sl-tbl">
                                    <thead>
                                        <tr>
                                            <th style="min-width:80px">Actions</th>
                                            <th style="min-width:150px">Client Name</th>
                                            <th style="min-width:120px">Phone</th>
                                            <th style="min-width:100px">Sale Date</th>
                                            <th style="min-width:110px">Carrier</th>
                                            <th style="min-width:130px">Unissued Status</th>
                                            <th style="min-width:180px">Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($disposition_leads as $lead)
                                            <tr>
                                                <td>
                                                    <div class="sl-act-group">
                                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-primary" title="View Details" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#dispositionModal-{{ $lead->id }}" title="Set Unissued Status">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
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

                                            <!-- Unissued Modal -->
                                            <div class="modal fade" id="dispositionModal-{{ $lead->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                                    <div class="modal-content">
 <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%)">
                                                            <h5 class="modal-title">
                                                                <i class="mdi mdi-clipboard-list me-2"></i>Set Issuance Status - {{ $lead->cn_name }}
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="dispositionForm-{{ $lead->id }}" class="disposition-form">
                                                                @csrf
                                                                
                                                                <!-- Disposition Selection as Buttons -->
                                                                <div class="mb-4">
                                                                    <label class="form-label fw-bold mb-3">
                                                                        Select Disposition <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="btn-group w-100" role="group">
                                                                        <input type="radio" class="btn-check disposition-radio" name="issuance_disposition" id="notApplicable-{{ $lead->id }}" value="Not Applicable" {{ $lead->issuance_disposition == 'Not Applicable' ? 'checked' : '' }}>
 <label class="btn btn-outline-secondary flex-grow-1" for="notApplicable-{{ $lead->id }}" >
                                                                            <i class="mdi mdi-close-circle me-2"></i>Not Applicable
                                                                        </label>

                                                                        <input type="radio" class="btn-check disposition-radio" name="issuance_disposition" id="issued-{{ $lead->id }}" value="Issued" {{ $lead->issuance_disposition == Statuses::ISSUANCE_ISSUED ? 'checked' : '' }} data-lead-id="{{ $lead->id }}">
 <label class="btn btn-outline-success flex-grow-1" for="issued-{{ $lead->id }}" >
                                                                            <i class="mdi mdi-check-circle me-2"></i>Issued (Send to Bank Verification)
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="reason-{{ $lead->id }}" class="form-label fw-bold">Reason/Notes</label>
                                                                    <textarea id="reason-{{ $lead->id }}" name="issuance_reason" class="form-control" rows="3" placeholder="Enter reason for this disposition..." style="max-width: 100%;">{{ $lead->issuance_reason ?? '' }}</textarea>
                                                                    <div class="form-text">Maximum 1000 characters</div>
                                                                </div>

                                                                <!-- View Details Button -->
                                                                <div class="mb-3">
                                                                    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-info w-100" target="_blank">
                                                                        <i class="mdi mdi-information-outline me-2"></i>View Complete Lead Details
                                                                    </a>
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
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="bx bx-inbox" style="font-size:2rem;color:#94a3b8"></i>
                                                    <p class="mb-0 text-muted" style="font-size:.82rem">No incomplete issuance data available</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($disposition_leads->hasPages())
                                <div class="mt-3">
                                    {{ $disposition_leads->appends(['search' => $search, 'month' => $month, 'year' => $year, 'date_from' => $date_from ?? '', 'date_to' => $date_to ?? ''])->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
    </div>

    <!-- RAVENS 3-PHASE MODAL FOR RETENTION CALLS -->
    <div class="modal fade" id="callDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
 <div class="modal-header bg-gradient-gold" >
                    <h5 class="modal-title text-white"><i class="fas fa-phone-alt me-2"></i><span id="callModalStatus">Call Connected - Retention</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="callModalBody">

                    <!-- PHASE 1: CALL CONNECTED -->
 <div class="d-none" id="phase1" >
                        <div class="text-center py-5">
                            <div class="mb-4">
 <i class="fas fa-phone-alt text-success u-fs-4" ></i>
                            </div>
 <h3 class="mb-3 text-gold" id="callerName">Connecting...</h3>
                            <p class="lead mb-2" id="callerPhone"></p>
                            <p class="text-muted">Call in progress</p>
 <button type="button" class="btn btn-lg mt-4 bg-gradient-gold text-white" onclick="goToPhase2()">
                                Start Call Info <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- PHASE 2: ESSENTIAL FIELDS -->
 <div class="d-none" id="phase2" >
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i> Fill all required fields to unlock detailed information
                        </div>

                        <div class="row g-3">
                            <!-- Name & Phone (Read-only display) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <div class="p-2 bg-light rounded" id="displayName"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number:</label>
                                <div class="p-2 bg-light rounded" id="displayPhone"></div>
                            </div>

                            <!-- DOB -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">DOB: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control required-field" id="phase2_dob" required>
                            </div>

                            <!-- SSN -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">SSN: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required-field" id="phase2_ssn" placeholder="XXX-XX-XXXX" required>
                            </div>

                            <!-- Beneficiary -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Beneficiary: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required-field" id="phase2_beneficiary" required>
                            </div>

                            <!-- Carrier -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Carrier: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required-field" id="phase2_carrier" required>
                            </div>

                            <!-- Coverage Amount -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coverage Amount: <span class="text-danger">*</span></label>
                                <input type="number" class="form-control required-field" id="phase2_coverage" step="0.01" required>
                            </div>

                            <!-- Premium -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Premium: <span class="text-danger">*</span></label>
                                <input type="number" class="form-control required-field" id="phase2_premium" step="0.01" required>
                            </div>
                        </div>

                        <!-- Assignment Section -->
                        <div class="alert alert-warning mt-3 mb-3">
                            <strong><i class="fas fa-user-tag me-2"></i>Sale Assignment</strong> - Select policy carrier, partner/agent, and approved states
                        </div>
                        <div class="row g-3">
                            <!-- Policy Carrier -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Policy Carrier: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase2_policy_carrier" required>
                                    <option value="">Select Carrier</option>
                                    <option value="AMAM">AMAM</option>
                                    <option value="Combined">Combined</option>
                                    <option value="AIG">AIG</option>
                                    <option value="LBL">LBL</option>
                                </select>
                            </div>

                            <!-- Partner/Agent -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Partner/Agent: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase2_partner_agent" required>
                                    <option value="">Select Partner/Agent</option>
                                    <option value="partner_1">John Partner</option>
                                    <option value="agent_1">-- Agent Mike</option>
                                    <option value="agent_2">-- Agent Sarah</option>
                                    <option value="partner_2">Jane Partner</option>
                                    <option value="agent_3">-- Agent Tom</option>
                                </select>
                            </div>

                            <!-- States -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">State: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase2_approved_state" required>
                                    <option value="">Select State</option>
                                    <option value="FL">Florida</option>
                                    <option value="TX">Texas</option>
                                    <option value="CA">California</option>
                                    <option value="NY">New York</option>
                                    <option value="PA">Pennsylvania</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary" onclick="goToPhase1()">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
 <button type="button" class="btn btn-lg bg-gradient-gold text-white" id="showMoreBtn" disabled onclick="goToPhase3()">
                                <i class="fas fa-unlock me-2"></i> Show More Details
                            </button>
                        </div>
                    </div>

                    <!-- PHASE 3: FULL DETAILS WITH CHANGE TRACKING -->
 <div class="d-none" id="phase3" >
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i> All essential fields captured. Review and update complete information below.
                        </div>

                        <div class="row g-3">
                            <!-- Personal Information Section -->
                            <div class="col-12">
 <h5 class="border-bottom pb-2 mb-3 text-gold" >Personal Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_name"></div>
                                <input type="text" class="form-control form-control-sm" id="change_name" placeholder="Changes (if any, write same as above if no change)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_phone"></div>
                                <input type="text" class="form-control form-control-sm" id="change_phone" placeholder="Changes (if any, write same as above if no change)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date of Birth:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_dob"></div>
                                <input type="date" class="form-control form-control-sm" id="change_dob">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Gender:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_gender"></div>
                                <select class="form-select form-select-sm" id="change_gender">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Birth Place:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_birthplace"></div>
                                <input type="text" class="form-control form-control-sm" id="change_birthplace" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">SSN:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_ssn"></div>
                                <input type="text" class="form-control form-control-sm" id="change_ssn" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Smoker:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_smoker"></div>
                                <select class="form-select form-select-sm" id="change_smoker">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold">Height:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_height"></div>
                                <input type="text" class="form-control form-control-sm" id="change_height" placeholder="e.g., 5'10&quot;">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold">Weight:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_weight"></div>
                                <input type="text" class="form-control form-control-sm" id="change_weight" placeholder="e.g., 180">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Address:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_address"></div>
                                <input type="text" class="form-control form-control-sm" id="change_address" placeholder="Changes (if any)">
                            </div>

                            <!-- Medical Information Section -->
                            <div class="col-12 mt-4">
 <h5 class="border-bottom pb-2 mb-3 text-gold" >Medical Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medical Issue:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_medical_issue"></div>
                                <textarea class="form-control form-control-sm" id="change_medical_issue" rows="2" placeholder="Changes (if any)"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medications:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_medications"></div>
                                <textarea class="form-control form-control-sm" id="change_medications" rows="2" placeholder="Changes (if any)"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_doctor"></div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Address:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_doctor_address"></div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor_address" placeholder="Changes (if any)">
                            </div>

                            <!-- Policy Information Section -->
                            <div class="col-12 mt-4">
 <h5 class="border-bottom pb-2 mb-3 text-gold" >Policy Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Beneficiary:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_beneficiary"></div>
                                <input type="text" class="form-control form-control-sm" id="change_beneficiary" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Beneficiary DOB:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_beneficiary_dob"></div>
                                <input type="date" class="form-control form-control-sm" id="change_beneficiary_dob">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Policy Type:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_policy_type"></div>
                                <input type="text" class="form-control form-control-sm" id="change_policy_type" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Carrier:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_carrier"></div>
                                <input type="text" class="form-control form-control-sm" id="change_carrier" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coverage Amount:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_coverage"></div>
                                <input type="number" class="form-control form-control-sm" id="change_coverage" step="0.01" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monthly Premium:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_premium"></div>
                                <input type="number" class="form-control form-control-sm" id="change_premium" step="0.01" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Initial Draft Date:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_draft_date"></div>
                                <input type="date" class="form-control form-control-sm" id="change_draft_date">
                            </div>

                            <!-- Banking Information Section -->
                            <div class="col-12 mt-4">
 <h5 class="border-bottom pb-2 mb-3 text-gold" >Banking Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bank Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_bank_name"></div>
                                <input type="text" class="form-control form-control-sm" id="change_bank_name" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Type:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_account_type"></div>
                                <select class="form-select form-select-sm" id="change_account_type">
                                    <option value="">Select</option>
                                    <option value="Checking">Checking</option>
                                    <option value="Savings">Savings</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Routing Number:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_routing"></div>
                                <input type="text" class="form-control form-control-sm" id="change_routing" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Number:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_account"></div>
                                <input type="text" class="form-control form-control-sm" id="change_account" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Verified By:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_verified_by"></div>
                                <input type="text" class="form-control form-control-sm" id="change_verified_by" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bank Balance:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_balance"></div>
                                <input type="number" class="form-control form-control-sm" id="change_balance" step="0.01" placeholder="Changes (if any)">
                            </div>

                            <!-- Additional Information -->
                            <div class="col-12 mt-4">
 <h5 class="border-bottom pb-2 mb-3 text-gold" >Additional Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Source:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_source"></div>
                                <input type="text" class="form-control form-control-sm" id="change_source" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Closer Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_closer"></div>
                                <input type="text" class="form-control form-control-sm" id="change_closer" placeholder="Changes (if any)">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary" onclick="goToPhase2()">
                                <i class="fas fa-arrow-left me-2"></i> Back to Essential Fields
                            </button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-phone-slash me-1"></i> End Call (No Sale)</button>
                    <button type="button" class="btn btn-success" onclick="submitRetentionSale()"><i class="fas fa-save me-1"></i> Save Retention Sale</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@include('partials.sl-filter-assets')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Instant search with debounce
    const searchInput = document.getElementById('retentionSearch');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const form = document.getElementById('retFilterForm');
                let hidden = form.querySelector('input[name="search"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = 'search';
                    form.appendChild(hidden);
                }
                hidden.value = this.value.trim();
                form.submit();
            }, 600);
        });
        if (searchInput.value) {
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    }

    // Handle retention status dropdown change
    const statusDropdowns = document.querySelectorAll('.retention-status-dropdown');
    
    statusDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const leadId = this.getAttribute('data-lead-id');
            const newStatus = this.value;
            const originalValue = this.querySelector('option[selected]')?.value || 'pending';
            
            // Show loading state
            this.disabled = true;
            
            // Send AJAX request to update status
            fetch(`/retention/${leadId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success feedback
                    this.classList.add('border-success');
                    setTimeout(() => {
                        this.classList.remove('border-success');
                    }, 2000);
                    
                    // Update the selected attribute
                    this.querySelectorAll('option').forEach(opt => opt.removeAttribute('selected'));
                    this.querySelector(`option[value="${newStatus}"]`).setAttribute('selected', 'selected');
                    
                    // If marked as retained, reload after 1 second to move to retained tab
                    if (newStatus === 'retained') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    // Revert to original value on error
                    this.value = originalValue;
                    alert('Failed to update status. Please try again.');
                }
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.value = originalValue;
                this.disabled = false;
                alert('An error occurred. Please try again.');
            });
        });
    });

    // Preserve active tab on page reload
    const activeTab = localStorage.getItem('activeRetentionTab');
    if (activeTab) {
        const tabTrigger = document.querySelector(`.sl-tab[href="${activeTab}"]`);
        if (tabTrigger) {
            // Deactivate current active tab
            document.querySelectorAll('.sl-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active', 'show'));
            // Activate saved tab
            tabTrigger.classList.add('active');
            const pane = document.querySelector(activeTab);
            if (pane) pane.classList.add('active', 'show');
        }
    }

    // Save active tab to localStorage
    document.querySelectorAll('.sl-tab[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            // Update active class
            document.querySelectorAll('.sl-tab').forEach(t => t.classList.remove('active'));
            e.target.classList.add('active');
            localStorage.setItem('activeRetentionTab', e.target.getAttribute('href'));
        });
    });

    // Handle call button clicks with event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.dial-lead-btn')) {
            const button = e.target.closest('.dial-lead-btn');
            const leadId = parseInt(button.dataset.leadId);
            const phone = button.dataset.phone;
            const customerName = button.dataset.customerName;
            const daysAgo = parseInt(button.dataset.daysAgo);
            
            dialLead(leadId, phone, customerName, daysAgo, button);
        }

        // Handle show lead details button
        if (e.target.closest('.show-lead-details-btn')) {
            const button = e.target.closest('.show-lead-details-btn');
            const leadJsonStr = button.dataset.leadJson;
            const lead = JSON.parse(leadJsonStr);
            showLeadDetails(lead);
        }
    });
});

// Retention management - Ravens 3-phase modal system
var currentLeadId = (typeof window !== 'undefined' && typeof window.currentLeadId !== 'undefined')
    ? window.currentLeadId
    : null;
var currentLeadData = (typeof window !== 'undefined' && typeof window.currentLeadData !== 'undefined')
    ? window.currentLeadData
    : null;
var callModalInstance = (typeof window !== 'undefined' && typeof window.callModalInstance !== 'undefined')
    ? window.callModalInstance
    : null;
var isCallActive = (typeof window !== 'undefined' && typeof window.isCallActive !== 'undefined')
    ? window.isCallActive
    : false;
var currentEventId = (typeof window !== 'undefined' && typeof window.currentEventId !== 'undefined')
    ? window.currentEventId
    : null;

if (typeof window !== 'undefined') {
    window.currentLeadId = currentLeadId;
    window.currentLeadData = currentLeadData;
    window.callModalInstance = callModalInstance;
    window.isCallActive = isCallActive;
    window.currentEventId = currentEventId;
}

// Get user's zoom number
window.zoomNumber = '{{ Auth::user()->zoom_number ?? '' }}';

// Note: Polling is intentionally disabled on initial page load
// Calls are initiated via explicit "Call" button clicks in the retention table
// This prevents automatic modal popups when opening the retention management page

// Call event polling
function startCallPolling() {
    console.log('Starting call event polling for retention...');
    setInterval(checkForCallEvents, 2000);
    checkForCallEvents();
}

function checkForCallEvents() {
    fetch('/api/call-events/poll', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.has_call && data.event_id !== currentEventId && !isCallActive) {
            currentEventId = data.event_id;
            isCallActive = true;
            showCallModal(data);
        } else if (!data.has_call && isCallActive) {
            isCallActive = false;
        }
    })
    .catch(error => console.error('Polling error:', error));
}

function dialLead(leadId, phone, customerName, daysAgo, button) {
    if (!phone) {
        toastr.error('No phone number available', 'Error');
        return;
    }

    currentLeadId = leadId;
    
    // Track call initiation
    fetch('/api/call-logs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            lead_id: leadId,
            phone_number: phone,
            status: 'initiated'
        })
    });

    // Clean phone and initiate Zoom call
    const cleanPhone = phone.replace(/[^\d\+]/g, '');
    const zoomUrl = 'zoomphonecall://' + encodeURIComponent(cleanPhone);
    window.location.href = zoomUrl;

    toastr.info('Initiating call to ' + customerName + '...', 'Dialing');
}

// Show Ravens-style modal
function showCallModal(callData) {
    console.log('=== RETENTION CALL CONNECTED ===', callData);
    const leadData = callData.lead_data;
    currentLeadData = leadData;
    currentLeadId = leadData.id;

    // PHASE 1: Show caller identification
    document.getElementById('callerName').textContent = leadData.cn_name || 'Unknown Caller';
    document.getElementById('callerPhone').textContent = leadData.phone_number || 'No Number';

    // PHASE 2: Populate display and pre-fill fields
    document.getElementById('displayName').textContent = leadData.cn_name || 'N/A';
    document.getElementById('displayPhone').textContent = leadData.phone_number || 'N/A';
    document.getElementById('phase2_dob').value = leadData.date_of_birth || '';
    document.getElementById('phase2_ssn').value = leadData.ssn || '';
    document.getElementById('phase2_beneficiary').value = leadData.beneficiary || '';
    document.getElementById('phase2_carrier').value = leadData.carrier_name || '';
    document.getElementById('phase2_coverage').value = leadData.coverage_amount || '';
    document.getElementById('phase2_premium').value = leadData.monthly_premium || '';

    // Validate Phase 2 fields after populating
    validatePhase2Fields();

    // Show modal and start at Phase 1
    const modalElement = document.getElementById('callDetailsModal');
    callModalInstance = new bootstrap.Modal(modalElement);
    callModalInstance.show();
    goToPhase1();

    // Mark as read
    if (callData.event_id && !callData.event_id.toString().startsWith('test-')) {
        fetch(`/api/call-events/${callData.event_id}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    }
}

// Phase navigation
function goToPhase1() {
    document.getElementById('phase1').style.display = 'block';
    document.getElementById('phase2').style.display = 'none';
    document.getElementById('phase3').style.display = 'none';
}

function goToPhase2() {
    document.getElementById('phase1').style.display = 'none';
    document.getElementById('phase2').style.display = 'block';
    document.getElementById('phase3').style.display = 'none';
    validatePhase2Fields();
}

function goToPhase3() {
    populatePhase3WithData();
    document.getElementById('phase1').style.display = 'none';
    document.getElementById('phase2').style.display = 'none';
    document.getElementById('phase3').style.display = 'block';
}

// Populate Phase 3
function populatePhase3WithData() {
    if (!currentLeadData) return;
    
    // Personal info
    document.getElementById('orig_name').textContent = currentLeadData.cn_name || 'N/A';
    document.getElementById('orig_phone').textContent = currentLeadData.phone_number || 'N/A';
    document.getElementById('orig_dob').textContent = document.getElementById('phase2_dob').value || 'N/A';
    document.getElementById('orig_gender').textContent = currentLeadData.gender || 'N/A';
    document.getElementById('orig_birthplace').textContent = currentLeadData.birth_place || 'N/A';
    document.getElementById('orig_ssn').textContent = document.getElementById('phase2_ssn').value || 'N/A';
    document.getElementById('orig_smoker').textContent = currentLeadData.smoker == 1 ? 'Yes' : 'No';
    document.getElementById('orig_height').textContent = currentLeadData.height || 'N/A';
    document.getElementById('orig_weight').textContent = currentLeadData.weight ? currentLeadData.weight + ' lbs' : 'N/A';
    document.getElementById('orig_address').textContent = currentLeadData.address || 'N/A';
    
    // Medical info
    document.getElementById('orig_medical_issue').textContent = currentLeadData.medical_issue || 'N/A';
    document.getElementById('orig_medications').textContent = currentLeadData.medications || 'N/A';
    document.getElementById('orig_doctor').textContent = currentLeadData.doctor_name || 'N/A';
    document.getElementById('orig_doctor_address').textContent = currentLeadData.doctor_address || 'N/A';
    
    // Policy info
    document.getElementById('orig_beneficiary').textContent = document.getElementById('phase2_beneficiary').value || 'N/A';
    document.getElementById('orig_beneficiary_dob').textContent = currentLeadData.beneficiary_dob || 'N/A';
    document.getElementById('orig_policy_type').textContent = currentLeadData.policy_type || 'N/A';
    document.getElementById('orig_carrier').textContent = document.getElementById('phase2_carrier').value || 'N/A';
    document.getElementById('orig_coverage').textContent = document.getElementById('phase2_coverage').value || 'N/A';
    document.getElementById('orig_premium').textContent = document.getElementById('phase2_premium').value || 'N/A';
    document.getElementById('orig_draft_date').textContent = currentLeadData.initial_draft_date || 'N/A';
    
    // Banking info
    document.getElementById('orig_bank_name').textContent = currentLeadData.bank_name || 'N/A';
    document.getElementById('orig_account_type').textContent = currentLeadData.account_type || 'N/A';
    document.getElementById('orig_routing').textContent = currentLeadData.routing_number || 'N/A';
    document.getElementById('orig_account').textContent = currentLeadData.account_number || 'N/A';
    document.getElementById('orig_verified_by').textContent = currentLeadData.verified_by || 'N/A';
    document.getElementById('orig_balance').textContent = currentLeadData.bank_balance || 'N/A';
    
    // Additional
    document.getElementById('orig_source').textContent = currentLeadData.source || 'N/A';
    document.getElementById('orig_closer').textContent = currentLeadData.closer_name || 'N/A';
    
    // Pre-fill change inputs
    document.getElementById('change_name').value = currentLeadData.cn_name || '';
    document.getElementById('change_phone').value = currentLeadData.phone_number || '';
}

// Validate Phase 2
function validatePhase2Fields() {
    const requiredFields = document.querySelectorAll('#phase2 .required-field');
    let allFilled = true;

    requiredFields.forEach(field => {
        if (!field.value || field.value.trim() === '') {
            allFilled = false;
        }
    });

    const showMoreBtn = document.getElementById('showMoreBtn');
    if (allFilled) {
        showMoreBtn.disabled = false;
    } else {
        showMoreBtn.disabled = true;
    }
}

// Add event listeners
$(document).ready(function() {
    const requiredFields = document.querySelectorAll('#phase2 .required-field');
    requiredFields.forEach(field => {
        field.addEventListener('input', validatePhase2Fields);
        field.addEventListener('change', validatePhase2Fields);
    });
});

// Submit retention sale
function submitRetentionSale() {
    if (!currentLeadId) {
        toastr.error('No lead selected', 'Error');
        return;
    }

    // Collect Phase 2 data
    const phase2Data = {
        dob: document.getElementById('phase2_dob').value,
        ssn: document.getElementById('phase2_ssn').value,
        beneficiary: document.getElementById('phase2_beneficiary').value,
        carrier: document.getElementById('phase2_carrier').value,
        coverage: document.getElementById('phase2_coverage').value,
        premium: document.getElementById('phase2_premium').value,
        policy_carrier: document.getElementById('phase2_policy_carrier').value,
        partner_agent: document.getElementById('phase2_partner_agent').value,
        approved_state: document.getElementById('phase2_approved_state').value
    };

    // Collect Phase 3 changes
    const phase3Changes = {
        name: document.getElementById('change_name').value,
        phone: document.getElementById('change_phone').value,
        dob: document.getElementById('change_dob').value,
        gender: document.getElementById('change_gender').value,
        birthplace: document.getElementById('change_birthplace').value,
        ssn: document.getElementById('change_ssn').value,
        smoker: document.getElementById('change_smoker').value,
        height: document.getElementById('change_height').value,
        weight: document.getElementById('change_weight').value,
        address: document.getElementById('change_address').value,
        medical_issue: document.getElementById('change_medical_issue').value,
        medications: document.getElementById('change_medications').value,
        doctor: document.getElementById('change_doctor').value,
        doctor_address: document.getElementById('change_doctor_address').value,
        beneficiary: document.getElementById('change_beneficiary').value,
        beneficiary_dob: document.getElementById('change_beneficiary_dob').value,
        policy_type: document.getElementById('change_policy_type').value,
        carrier: document.getElementById('change_carrier').value,
        coverage: document.getElementById('change_coverage').value,
        premium: document.getElementById('change_premium').value,
        draft_date: document.getElementById('change_draft_date').value,
        bank_name: document.getElementById('change_bank_name').value,
        account_type: document.getElementById('change_account_type').value,
        routing: document.getElementById('change_routing').value,
        account: document.getElementById('change_account').value,
        verified_by: document.getElementById('change_verified_by').value,
        balance: document.getElementById('change_balance').value,
        source: document.getElementById('change_source').value,
        closer: document.getElementById('change_closer').value
    };

    toastr.info('Submitting retention sale...', 'Processing');

    $.ajax({
        url: `/leads/${currentLeadId}/retention-sale`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            sale_date: '{{ now()->toDateString() }}',
            notes: 'Retention sale completed',
            phase2: phase2Data,
            phase3: phase3Changes
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message, 'Success');
                if (callModalInstance) callModalInstance.hide();
                isCallActive = false;
                currentEventId = null;
                setTimeout(() => window.location.reload(), 2000);
            } else {
                toastr.error(response.message || 'Failed to save retention sale', 'Error');
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'An error occurred', 'Error');
        }
    });
}

// Test button
window.testRavensCallModal = function() {
    const testCallData = {
        event_id: 'test-' + Date.now(),
        lead_data: {
            id: 999999,
            cn_name: 'Test Retention Customer',
            phone_number: '+1-555-123-4567',
            date_of_birth: '1985-06-15',
            ssn: '123-45-6789',
            gender: 'Male',
            birth_place: 'Test City',
            smoker: 0,
            height_weight: '5ft 10in, 180 lbs',
            height: '5ft 10in',
            weight: '180',
            address: '123 Test St',
            beneficiary: 'Jane Beneficiary',
            carrier_name: 'Test Insurance',
            coverage_amount: '100000',
            monthly_premium: '75.50',
            closer_name: @json(Auth::user()->name ?? 'Test Closer'),
            source: 'Test Source'
        },
        call_connected_at: new Date().toISOString()
    };
    
    showCallModal(testCallData);
    toastr.info('Test modal opened', 'Test Mode');
}

// Disposition Modal Handling
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
            alert('Please select a disposition');
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
                    $('body').prepend(alertHtml);
                    
                    // Close modal
                    $(`#dispositionModal-${leadId}`).modal('hide');
                    
                    // If disposition is "Issued", redirect to bank verification
                    if (disposition === 'Issued') {
                        setTimeout(() => {
                            window.location.href = `/bank-verification?lead_id=${leadId}`;
                        }, 1500);
                    } else {
                        // Otherwise reload the page
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
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
        const leadId = $(this).attr('id').replace('dispositionModal-', '');
        if (leadId && $(`#issued-${leadId}`).length) {
            const disposition = $(`input[name="issuance_disposition"]:checked`).val();
            // Handle any additional logic if needed
        }
    });

    // Navigate to lead details page
    window.showLeadDetails = function(lead) {
        window.open('/leads/' + lead.id, '_blank');
    };
</script>
@endsection
