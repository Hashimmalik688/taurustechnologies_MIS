@extends('layouts.master')

@section('title')
    Submission Performance
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ════════════════════════════════════════════════════════
   SUBMISSION PERFORMANCE — Design System
   Aesthetic: Dark-accented data dashboard, insurance-grade
   ════════════════════════════════════════════════════════ */

/* ── Variables & Resets ───────────────────────────────── */
:root {
    --sp-gold:         #d4af37;
    --sp-gold-dim:     rgba(212,175,55,.12);
    --sp-gold-dark:    #92760d;
    --sp-teal:         #0ea5a0;
    --sp-teal-dim:     rgba(14,165,160,.12);
    --sp-indigo:       #6366f1;
    --sp-indigo-dim:   rgba(99,102,241,.12);
    --sp-orange:       #f97316;
    --sp-orange-dim:   rgba(249,115,22,.12);
    --sp-green:        #22c55e;
    --sp-green-dim:    rgba(34,197,94,.11);
    --sp-red:          #ef4444;
    --sp-surface:      var(--bs-card-bg, #ffffff);
    --sp-border:       rgba(0,0,0,.07);
    --sp-shadow:       0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --sp-text-1:       var(--bs-surface-900, #0f172a);
    --sp-text-2:       var(--bs-surface-700, #374151);
    --sp-text-3:       var(--bs-surface-500, #64748b);
    --sp-text-4:       var(--bs-surface-400, #94a3b8);
}

/* ── Page header ─────────────────────────────────────── */
.sp-hdr {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .6rem; flex-wrap: wrap; gap: .35rem;
}
.sp-hdr-title {
    display: flex; align-items: center; gap: .45rem; flex-wrap: wrap;
}
.sp-hdr-icon {
    width: 28px; height: 28px; border-radius: .4rem; flex-shrink: 0;
    background: linear-gradient(135deg, var(--sp-gold), #b8941f);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 6px rgba(212,175,55,.35);
}
.sp-hdr-icon i { font-size: .95rem; color: #0f172a; }
.sp-hdr h5 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--sp-text-1); }
.sp-hdr-sub {
    font-size: .67rem; color: var(--sp-text-3); font-weight: 400;
    border-left: 2px solid var(--sp-border); padding-left: .45rem; margin-left: .1rem;
}
.sp-back {
    font-size: .7rem; font-weight: 700; padding: .28rem .6rem; border-radius: 20px;
    border: 1.5px solid var(--sp-border); background: transparent;
    color: var(--sp-text-3); text-decoration: none; display: inline-flex;
    align-items: center; gap: .22rem; transition: all .15s;
}
.sp-back:hover { border-color: var(--sp-gold); color: var(--sp-gold-dark); }

/* ── Filter bar ──────────────────────────────────────── */
.sp-filter {
    display: flex; flex-wrap: wrap; gap: .4rem; align-items: flex-end;
    background: var(--sp-surface); border: 1px solid var(--sp-border);
    border-radius: .55rem; padding: .5rem .7rem; margin-bottom: .7rem;
    box-shadow: var(--sp-shadow);
}
.sp-filter label {
    font-size: .58rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .6px; color: var(--sp-text-4); display: block; margin-bottom: .12rem;
}
.sp-filter input[type=date] {
    font-size: .73rem; padding: .28rem .45rem; border-radius: .4rem;
    border: 1.5px solid var(--sp-border); background: var(--bs-input-bg, #f8fafc);
    color: var(--sp-text-1); outline: none; transition: border-color .15s;
}
.sp-filter input[type=date]:focus { border-color: var(--sp-gold); box-shadow: 0 0 0 2px var(--sp-gold-dim); }
.sp-btn {
    font-size: .7rem; font-weight: 700; padding: .3rem .65rem; border-radius: 20px;
    border: none; cursor: pointer; display: inline-flex; align-items: center;
    gap: .22rem; transition: all .15s; text-decoration: none;
}
.sp-btn-apply { background: linear-gradient(135deg, var(--sp-gold), #b8941f); color: #0f172a; }
.sp-btn-apply:hover { box-shadow: 0 2px 10px rgba(212,175,55,.4); transform: translateY(-1px); }
.sp-btn-reset { background: transparent; border: 1.5px solid var(--sp-border) !important; color: var(--sp-text-3); }
.sp-btn-reset:hover { border-color: var(--sp-gold) !important; color: var(--sp-gold-dark); }
.sp-daterange {
    font-size: .63rem; color: var(--sp-text-3); display: flex; align-items: center;
    gap: .22rem; margin-left: .1rem; align-self: flex-end;
}
.sp-daterange strong { color: var(--sp-text-2); font-weight: 700; }

/* ── KPI Strip ───────────────────────────────────────── */
.sp-kpis {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: .45rem; margin-bottom: .7rem;
}
@media(max-width:860px) { .sp-kpis { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px) { .sp-kpis { grid-template-columns: 1fr; } }

.sp-kpi {
    background: var(--sp-surface); border: 1px solid var(--sp-border);
    border-radius: .55rem; padding: .55rem .75rem; position: relative;
    overflow: hidden; box-shadow: var(--sp-shadow);
}
.sp-kpi::before {
    content: ''; position: absolute; inset: 0 auto 0 0; width: 3.5px;
    border-radius: 2px 0 0 2px;
}
.sp-kpi-submissions::before { background: linear-gradient(180deg, var(--sp-gold), #b8941f); }
.sp-kpi-premium::before     { background: linear-gradient(180deg, var(--sp-green), #16a34a); }
.sp-kpi-revenue::before     { background: linear-gradient(180deg, var(--sp-indigo), #4338ca); }
.sp-kpi-avg::before         { background: linear-gradient(180deg, var(--sp-teal), #0d9488); }

.sp-kpi-icon {
    position: absolute; right: .6rem; top: .5rem; font-size: 1.3rem; opacity: .06;
    font-weight: 900;
}
.sp-kpi-lbl {
    font-size: .57rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: .18rem;
}
.sp-kpi-submissions .sp-kpi-lbl { color: var(--sp-gold-dark); }
.sp-kpi-premium .sp-kpi-lbl     { color: #16a34a; }
.sp-kpi-revenue .sp-kpi-lbl     { color: #4338ca; }
.sp-kpi-avg .sp-kpi-lbl         { color: #0d9488; }

.sp-kpi-val {
    font-size: 1.25rem; font-weight: 900; color: var(--sp-text-1);
    line-height: 1; font-variant-numeric: tabular-nums; letter-spacing: -.01em;
}
.sp-kpi-sub { font-size: .6rem; color: var(--sp-text-4); margin-top: .14rem; }

/* ── Body layout ─────────────────────────────────────── */
.sp-body {
    display: grid; grid-template-columns: 1fr 204px;
    gap: .7rem; align-items: start;
}
@media(max-width:780px) {
    .sp-body { grid-template-columns: 1fr; }
    .sp-donut-panel { display: none; }
}

/* ── Main card ───────────────────────────────────────── */
.sp-card {
    background: var(--sp-surface); border: 1px solid var(--sp-border);
    border-radius: .55rem; overflow: hidden; box-shadow: var(--sp-shadow);
}
.sp-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: .45rem .7rem; border-bottom: 1px solid var(--sp-border);
    background: rgba(248,250,252,.5);
}
.sp-card-head h6 {
    margin: 0; font-size: .76rem; font-weight: 800; color: var(--sp-text-1);
    display: flex; align-items: center; gap: .28rem;
}
.sp-card-head h6 i { color: var(--sp-gold); font-size: .85rem; }
.sp-card-hint { font-size: .61rem; color: var(--sp-text-4); }

/* ── Table ───────────────────────────────────────────── */
.sp-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .71rem; }
.sp-tbl thead th {
    padding: .38rem .58rem; font-size: .58rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .55px; color: var(--sp-text-4);
    background: rgba(248,250,252,.95); border-bottom: 2px solid var(--sp-border);
    white-space: nowrap; position: sticky; top: 0; z-index: 2;
}
.sp-tbl thead th.th-r { text-align: right; }
.sp-tbl tbody td {
    padding: .4rem .58rem; border-bottom: 1px solid rgba(0,0,0,.025);
    vertical-align: middle;
}
.sp-tbl tbody tr:last-child td { border-bottom: none; }
.sp-tbl tbody tr.sp-row { cursor: pointer; }
.sp-tbl tbody tr.sp-row:hover td { background: rgba(212,175,55,.035); }
.sp-tbl tfoot td {
    padding: .44rem .58rem; border-top: 2px solid rgba(0,0,0,.08);
    font-weight: 800; background: rgba(212,175,55,.04);
}

/* ── Rank cell ───────────────────────────────────────── */
.sp-rank {
    text-align: center; font-size: .63rem; font-weight: 800;
    color: var(--sp-text-4); width: 28px;
}
.rank-1 { color: var(--sp-gold); }
.rank-2 { color: #94a3b8; }
.rank-3 { color: #cd7f32; }

/* ── Carrier·Partner cell ────────────────────────────── */
.sp-carrier-link {
    display: inline-flex; align-items: center; gap: .38rem;
    text-decoration: none; max-width: 260px;
}
.sp-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.sp-cname {
    font-weight: 700; color: var(--sp-text-1); font-size: .72rem;
    white-space: nowrap; transition: color .12s;
}
.sp-row:hover .sp-cname { color: var(--sp-gold-dark); }
.sp-partner-badge {
    font-size: .58rem; font-weight: 800; padding: .06rem .3rem; border-radius: 10px;
    white-space: nowrap; letter-spacing: .2px;
}
.sp-pb-gold  { background: var(--sp-gold-dim);   color: var(--sp-gold-dark); }
.sp-pb-teal  { background: var(--sp-teal-dim);   color: var(--sp-teal); }
.sp-pb-indigo{ background: var(--sp-indigo-dim); color: var(--sp-indigo); }
.sp-pb-none  { background: rgba(100,116,139,.08); color: var(--sp-text-4); }

/* ── Share bar ───────────────────────────────────────── */
.sp-share { display: flex; align-items: center; gap: .32rem; }
.sp-bar-track {
    flex: 1; height: 5px; border-radius: 5px; background: rgba(0,0,0,.06);
    overflow: hidden; min-width: 50px;
}
.sp-bar-fill { height: 100%; border-radius: 5px; transition: width .4s ease; }
.sp-pct { font-size: .6rem; color: var(--sp-text-4); min-width: 30px; text-align: right; font-weight: 600; }

/* ── Number cells ────────────────────────────────────── */
.td-r { text-align: right; font-variant-numeric: tabular-nums; }
.sp-sales-num { font-size: .82rem; font-weight: 800; color: var(--sp-text-1); }

/* ── Premium chip ────────────────────────────────────── */
.sp-chip {
    display: inline-flex; align-items: center; gap: .16rem;
    font-size: .63rem; font-weight: 700; padding: .08rem .32rem; border-radius: 14px;
}
.sp-chip-green  { background: var(--sp-green-dim);  color: #15803d; }
.sp-chip-indigo { background: var(--sp-indigo-dim); color: var(--sp-indigo); }
.sp-chip-gold   { background: var(--sp-gold-dim);   color: var(--sp-gold-dark); }
.sp-chip-none   { background: rgba(100,116,139,.07); color: var(--sp-text-4); }

.sp-chip i { font-size: .68rem; }

/* ── Revenue column header accent ───────────────────── */
th.th-revenue { color: var(--sp-indigo) !important; }

/* ── Avg-per-sale sub ────────────────────────────────── */
.sp-avg { font-size: .67rem; color: var(--sp-text-3); text-align: right; }

/* ── No-commission note ──────────────────────────────── */
.sp-norev {
    font-size: .6rem; color: var(--sp-text-4); font-style: italic;
    display: inline-flex; align-items: center; gap: .15rem;
}

/* ── Donut panel ─────────────────────────────────────── */
.sp-donut-panel {
    background: var(--sp-surface); border: 1px solid var(--sp-border);
    border-radius: .55rem; padding: .55rem .6rem; box-shadow: var(--sp-shadow);
}
.sp-donut-title {
    font-size: .58rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .55px; color: var(--sp-text-4); margin-bottom: .45rem;
}
.sp-legend-row { display: flex; align-items: center; gap: .3rem; margin-bottom: .22rem; }
.sp-legend-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.sp-legend-name {
    font-size: .62rem; color: var(--sp-text-2); flex: 1;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.sp-legend-name .lp { opacity: .5; font-weight: 400; }
.sp-legend-pct { font-size: .6rem; font-weight: 800; color: var(--sp-text-3); }

/* ── Empty state ─────────────────────────────────────── */
.sp-empty { text-align: center; padding: 3rem 1rem; color: var(--sp-text-4); }
.sp-empty i { font-size: 2.5rem; display: block; margin-bottom: .5rem; opacity: .15; }
.sp-empty strong { display: block; font-size: .82rem; margin-bottom: .25rem; color: var(--sp-text-3); }
.sp-empty span { font-size: .7rem; }

/* ── Footer note ─────────────────────────────────────── */
.sp-footnote {
    font-size: .59rem; color: var(--sp-text-4); text-align: center; margin-top: .5rem;
    display: flex; align-items: center; justify-content: center; gap: .22rem;
}
.sp-footnote code {
    font-size: .58rem; background: rgba(0,0,0,.05); padding: .02rem .22rem;
    border-radius: 3px; color: var(--sp-text-3);
}

/* ── Dark mode overrides ─────────────────────────────── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) {
    --sp-surface:  rgba(15,23,42,.55);
    --sp-border:   rgba(255,255,255,.07);
    --sp-shadow:   0 1px 4px rgba(0,0,0,.25), 0 0 0 1px rgba(255,255,255,.04);
    --sp-text-1:   #f1f5f9;
    --sp-text-2:   #cbd5e1;
    --sp-text-3:   #94a3b8;
    --sp-text-4:   #64748b;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-tbl thead th {
    background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-tbl tfoot td {
    background: rgba(212,175,55,.06); border-color: rgba(255,255,255,.08);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-filter input[type=date] {
    background: rgba(15,23,42,.6); border-color: rgba(255,255,255,.09); color: #e2e8f0;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-card-head {
    background: rgba(15,23,42,.4);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-chip-green { background: rgba(34,197,94,.12); color: #4ade80; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-chip-indigo { background: rgba(99,102,241,.15); color: #a5b4fc; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-chip-gold { background: rgba(212,175,55,.15); color: #fcd34d; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .sp-footnote code { background: rgba(255,255,255,.07); color: #94a3b8; }
</style>
@endsection

@section('content')
@php
/* Vivid, high-contrast palette — each index is visually distinct */
$palette = [
    '#e11d48', /* 0 crimson       */
    '#2563eb', /* 1 royal blue    */
    '#16a34a', /* 2 emerald       */
    '#ea580c', /* 3 orange        */
    '#7c3aed', /* 4 violet        */
    '#0891b2', /* 5 cyan          */
    '#d97706', /* 6 amber         */
    '#db2777', /* 7 rose          */
    '#059669', /* 8 teal          */
    '#6d28d9', /* 9 purple        */
    '#dc2626', /* 10 red          */
    '#1d4ed8', /* 11 blue         */
];

/* Partner badge variant cycling */
$badgeVariants = ['sp-pb-gold','sp-pb-teal','sp-pb-indigo'];

$grandTotal = $grandTotalSales;
@endphp

{{-- ── Page Header ─────────────────────────────────────── --}}
<div class="sp-hdr">
    <div class="sp-hdr-title">
        <div class="sp-hdr-icon"><i class="bx bx-award"></i></div>
        <div>
            <h5>Submission Performance</h5>
            <span class="sp-hdr-sub">carrier &amp; partner breakdown · approved sales → Pending Contract</span>
        </div>
    </div>
    <a href="{{ route('settings.reports.hub') }}" class="sp-back">
        <i class="bx bx-arrow-back"></i> Hub
    </a>
</div>

{{-- ── Filter Bar ───────────────────────────────────────── --}}
<div class="sp-filter">
    <form method="GET" action="{{ route('settings.reports.submission-performance') }}" style="display:contents">
        <div>
            <label for="sp-from">From</label>
            <input type="date" id="sp-from" name="date_from" value="{{ $dateFrom }}">
        </div>
        <div>
            <label for="sp-to">To</label>
            <input type="date" id="sp-to" name="date_to" value="{{ $dateTo }}">
        </div>
        <button type="submit" class="sp-btn sp-btn-apply" style="align-self:flex-end">
            <i class="bx bx-search-alt"></i> Apply
        </button>
        <a href="{{ route('settings.reports.submission-performance') }}" class="sp-btn sp-btn-reset" style="align-self:flex-end">
            <i class="bx bx-reset"></i> Reset
        </a>
        @if($dateFrom || $dateTo)
        <span class="sp-daterange">
            <i class="bx bx-calendar-check" style="color:var(--sp-gold);font-size:.82rem"></i>
            <strong>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : '—' }}</strong>
            <span style="color:var(--sp-text-4)">→</span>
            <strong>{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : '—' }}</strong>
        </span>
        @endif
    </form>
</div>

{{-- ── KPI Strip ─────────────────────────────────────────── --}}
<div class="sp-kpis">
    {{-- Submissions --}}
    <div class="sp-kpi sp-kpi-submissions">
        <i class="bx bx-check-double sp-kpi-icon"></i>
        <div class="sp-kpi-lbl">Total Submissions</div>
        <div class="sp-kpi-val">{{ number_format($grandTotalSales) }}</div>
        <div class="sp-kpi-sub">Sent to Pending Contract</div>
    </div>
    {{-- Premium --}}
    <div class="sp-kpi sp-kpi-premium">
        <i class="bx bx-dollar-circle sp-kpi-icon"></i>
        <div class="sp-kpi-lbl">Total Premium</div>
        <div class="sp-kpi-val">${{ number_format($grandTotalPremium, 2) }}</div>
        <div class="sp-kpi-sub">Monthly premium / all carriers</div>
    </div>
    {{-- Revenue --}}
    <div class="sp-kpi sp-kpi-revenue">
        <i class="bx bx-trending-up sp-kpi-icon"></i>
        <div class="sp-kpi-lbl">Est. Revenue</div>
        <div class="sp-kpi-val">${{ number_format($grandTotalRevenue, 2) }}</div>
        <div class="sp-kpi-sub">Σ (premium × 9 × commission%)</div>
    </div>
    {{-- Avg --}}
    <div class="sp-kpi sp-kpi-avg">
        <i class="bx bx-bar-chart-alt-2 sp-kpi-icon"></i>
        <div class="sp-kpi-lbl">Avg Revenue / Sale</div>
        <div class="sp-kpi-val">${{ $grandTotalSales > 0 ? number_format($grandTotalRevenue / $grandTotalSales, 2) : '0.00' }}</div>
        <div class="sp-kpi-sub">
            @if($grandTotalRevenue > 0 && $grandTotalPremium > 0)
                ~{{ number_format(($grandTotalRevenue / ($grandTotalPremium * 9)) * 100, 1) }}% avg commission
            @else
                Across all carriers
            @endif
        </div>
    </div>
</div>

{{-- ── Body Grid ─────────────────────────────────────────── --}}
<div class="sp-body">

    {{-- ── Table ──────────────────────────────────────────── --}}
    <div class="sp-card">
        <div class="sp-card-head">
            <h6><i class="bx bx-building"></i> Carrier · Partner Breakdown</h6>
            <span class="sp-card-hint">Click row → view individual sales detail</span>
        </div>

        @if($carriersData->isEmpty())
        <div class="sp-empty">
            <i class="bx bx-bar-chart-alt-2"></i>
            <strong>No submissions found</strong>
            <span>No leads sent to Pending Contract for this date range.</span>
        </div>
        @else
        <div style="overflow-x:auto">
            <table class="sp-tbl">
                <thead>
                    <tr>
                        <th style="width:26px">#</th>
                        <th style="min-width:175px">Carrier · Partner</th>
                        <th style="min-width:100px">Share</th>
                        <th class="th-r" style="min-width:58px">Sales</th>
                        <th class="th-r" style="min-width:108px">Monthly Premium</th>
                        <th class="th-r th-revenue" style="min-width:115px">
                            <span style="display:inline-flex;align-items:center;gap:.18rem">
                                <i class="bx bx-trending-up"></i> Est. Revenue
                            </span>
                        </th>
                        <th class="th-r" style="min-width:85px">Avg / Sale</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carriersData as $idx => $row)
                    @php
                        $rank   = $idx + 1;
                        $pct    = $grandTotal > 0 ? round(($row->total_sales / $grandTotal) * 100, 1) : 0;
                        $color  = $palette[$idx % count($palette)];
                        $avgSale  = $row->total_sales > 0 ? $row->total_premium / $row->total_sales : 0;
                        $hasRev   = $row->total_revenue > 0;

                        /* Build drilldown URL — use text values so merged rows always match */
                        $pcParams = ['date_from' => $dateFrom, 'date_to' => $dateTo];
                        if ($row->carrier_name)     $pcParams['carrier_name']     = $row->carrier_name;
                        $pcParams['assigned_partner'] = $row->assigned_partner ?? '';
                        $pcUrl = route('settings.reports.submission-performance.drilldown', $pcParams);

                        /* Partner badge variant — hash from text since partner_id may be absent */
                        $badgeClass = $row->assigned_partner ? $badgeVariants[abs(crc32($row->assigned_partner)) % count($badgeVariants)] : 'sp-pb-none';
                    @endphp
                    <tr class="sp-row" onclick="window.location='{{ $pcUrl }}'">
                        {{-- Rank --}}
                        <td class="sp-rank {{ $rank===1?'rank-1':($rank===2?'rank-2':($rank===3?'rank-3':'')) }}">
                            @if($rank===1)<i class="bx bxs-crown"></i>@else{{ $rank }}@endif
                        </td>
                        {{-- Carrier · Partner --}}
                        <td>
                            <a href="{{ $pcUrl }}" class="sp-carrier-link" onclick="event.stopPropagation()">
                                <span class="sp-dot" style="background:{{ $color }};box-shadow:0 0 0 2px {{ $color }}30"></span>
                                <span class="sp-cname">{{ $row->carrier_name ?: 'Unknown' }}</span>
                                @if($row->assigned_partner)
                                    <span class="sp-partner-badge {{ $badgeClass }}">{{ $row->assigned_partner }}</span>
                                @else
                                    <span class="sp-partner-badge sp-pb-none">—</span>
                                @endif
                            </a>
                        </td>
                        {{-- Share bar --}}
                        <td>
                            <div class="sp-share">
                                <div class="sp-bar-track">
                                    <div class="sp-bar-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                                </div>
                                <span class="sp-pct">{{ $pct }}%</span>
                            </div>
                        </td>
                        {{-- Sales --}}
                        <td class="td-r">
                            <span class="sp-sales-num">{{ number_format($row->total_sales) }}</span>
                        </td>
                        {{-- Monthly Premium --}}
                        <td class="td-r">
                            <span class="sp-chip sp-chip-green">
                                <i class="bx bx-dollar"></i>{{ number_format($row->total_premium, 2) }}
                            </span>
                        </td>
                        {{-- Est. Revenue --}}
                        <td class="td-r">
                            @if($hasRev)
                                <span class="sp-chip sp-chip-indigo">
                                    <i class="bx bx-trending-up"></i>{{ number_format($row->total_revenue, 2) }}
                                </span>
                            @else
                                <span class="sp-norev"><i class="bx bx-minus"></i> no rate</span>
                            @endif
                        </td>
                        {{-- Avg / Sale --}}
                        <td class="sp-avg">${{ number_format($avgSale, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <span style="display:inline-flex;align-items:center;gap:.22rem;font-size:.7rem">
                                <i class="bx bx-sum" style="color:var(--sp-gold)"></i> TOTAL
                            </span>
                        </td>
                        <td></td>
                        <td class="td-r">
                            <span class="sp-sales-num">{{ number_format($grandTotalSales) }}</span>
                        </td>
                        <td class="td-r">
                            <span class="sp-chip sp-chip-green">
                                <i class="bx bx-dollar"></i>{{ number_format($grandTotalPremium, 2) }}
                            </span>
                        </td>
                        <td class="td-r">
                            @if($grandTotalRevenue > 0)
                            <span class="sp-chip sp-chip-indigo">
                                <i class="bx bx-trending-up"></i>{{ number_format($grandTotalRevenue, 2) }}
                            </span>
                            @else
                            <span class="sp-norev"><i class="bx bx-minus"></i> —</span>
                            @endif
                        </td>
                        <td class="sp-avg">
                            ${{ $grandTotalSales > 0 ? number_format($grandTotalPremium / $grandTotalSales, 2) : '0.00' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Donut Chart Panel ────────────────────────────── --}}
    @if($carriersData->isNotEmpty())
    <div class="sp-donut-panel">
        <div class="sp-donut-title">Sales Distribution</div>
        @php
            $innerR       = 52;
            $circum       = 2 * M_PI * $innerR;
            $cumPct       = 0;
            $donutSlices  = [];
            foreach ($carriersData as $i => $r) {
                $p = $grandTotal > 0 ? $r->total_sales / $grandTotal : 0;
                $donutSlices[] = ['pct' => $p, 'color' => $palette[$i % count($palette)], 'cum' => $cumPct];
                $cumPct += $p;
            }
        @endphp
        <svg viewBox="0 0 164 164" style="width:100%;max-width:164px;display:block;margin:0 auto">
            <circle cx="82" cy="82" r="{{ $innerR }}" fill="none" stroke="rgba(0,0,0,.05)" stroke-width="22"/>
            @foreach($donutSlices as $sl)
            @php
                $dash     = $circum * $sl['pct'];
                $gap      = $circum - $dash;
                $startOff = $circum * 0.25 - $circum * $sl['cum'];
            @endphp
            <circle cx="82" cy="82" r="{{ $innerR }}" fill="none"
                stroke="{{ $sl['color'] }}" stroke-width="22"
                stroke-dasharray="{{ number_format($dash,4,'.',''). ' '.number_format($gap,4,'.','')}}"
                stroke-dashoffset="{{ number_format($startOff,4,'.','')}}" />
            @endforeach
            <text x="82" y="77" text-anchor="middle" font-size="18" font-weight="900" fill="var(--sp-text-1)">
                {{ number_format($grandTotalSales) }}
            </text>
            <text x="82" y="92" text-anchor="middle" font-size="7.5" fill="var(--sp-text-4)" font-weight="700" letter-spacing=".6">
                SALES
            </text>
        </svg>
        {{-- Legend --}}
        <div style="margin-top:.5rem">
            @foreach($carriersData->take(7) as $i => $r)
            @php $lPct = $grandTotal > 0 ? round(($r->total_sales / $grandTotal) * 100, 1) : 0 @endphp
            <div class="sp-legend-row">
                <span class="sp-legend-dot" style="background:{{ $palette[$i % count($palette)] }}"></span>
                <span class="sp-legend-name">
                    {{ $r->carrier_name ?: 'Unknown' }}
                    @if($r->assigned_partner)<span class="lp"> · {{ $r->assigned_partner }}</span>@endif
                </span>
                <span class="sp-legend-pct">{{ $lPct }}%</span>
            </div>
            @endforeach
            @if($carriersData->count() > 7)
            <div style="font-size:.58rem;color:var(--sp-text-4);margin-top:.18rem;padding-left:1rem">
                +{{ $carriersData->count() - 7 }} more
            </div>
            @endif
        </div>
    </div>
    @endif

</div>{{-- /.sp-body --}}

<div class="sp-footnote">
    <i class="bx bx-info-circle"></i>
    Read-only — sourced from leads where <code>pending_contract_at IS NOT NULL</code>.
    Revenue = <code>monthly_premium × 9 × commission%</code> stored per-lead.
</div>
@endsection
