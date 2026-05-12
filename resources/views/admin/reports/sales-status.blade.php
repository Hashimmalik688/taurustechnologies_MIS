@extends('layouts.master')

@section('title')
    Sales Status Report
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ════════════════════════════════════════════════════════
   SALES STATUS REPORT — Design System
   One pivot table replacing 8 separate reports
   ════════════════════════════════════════════════════════ */

:root {
    --ss-gold:         #d4af37;
    --ss-gold-dim:     rgba(212,175,55,.12);
    --ss-gold-dark:    #92760d;
    --ss-green:        #22c55e;
    --ss-green-dim:    rgba(34,197,94,.11);
    --ss-indigo:       #6366f1;
    --ss-indigo-dim:   rgba(99,102,241,.12);
    --ss-blue:         #3b82f6;
    --ss-blue-dim:     rgba(59,130,246,.11);
    --ss-teal:         #0ea5a0;
    --ss-teal-dim:     rgba(14,165,160,.12);
    --ss-orange:       #f97316;
    --ss-orange-dim:   rgba(249,115,22,.12);
    --ss-rose:         #f43f5e;
    --ss-rose-dim:     rgba(244,63,94,.09);
    --ss-amber:        #f59e0b;
    --ss-amber-dim:    rgba(245,158,11,.10);
    --ss-slate:        #64748b;
    --ss-slate-dim:    rgba(100,116,139,.10);
    --ss-surface:      var(--bs-card-bg, #ffffff);
    --ss-border:       rgba(0,0,0,.07);
    --ss-shadow:       0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --ss-text-1:       var(--bs-surface-900, #0f172a);
    --ss-text-2:       var(--bs-surface-700, #374151);
    --ss-text-3:       var(--bs-surface-500, #64748b);
    --ss-text-4:       var(--bs-surface-400, #94a3b8);
}

/* ── Page header ──────────────────────────────────────── */
.ss-hdr {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .6rem; flex-wrap: wrap; gap: .35rem;
}
.ss-hdr-title { display: flex; align-items: center; gap: .45rem; flex-wrap: wrap; }
.ss-hdr-icon {
    width: 28px; height: 28px; border-radius: .4rem; flex-shrink: 0;
    background: linear-gradient(135deg, var(--ss-blue), #1d4ed8);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 6px rgba(59,130,246,.35);
}
.ss-hdr-icon i { font-size: .95rem; color: #fff; }
.ss-hdr h5 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--ss-text-1); }
.ss-hdr-sub {
    font-size: .67rem; color: var(--ss-text-3); font-weight: 400;
    border-left: 2px solid var(--ss-border); padding-left: .45rem; margin-left: .1rem;
}
.ss-back {
    font-size: .7rem; font-weight: 700; padding: .28rem .6rem; border-radius: 20px;
    border: 1.5px solid var(--ss-border); background: transparent;
    color: var(--ss-text-3); text-decoration: none; display: inline-flex;
    align-items: center; gap: .22rem; transition: all .15s;
}
.ss-back:hover { border-color: var(--ss-blue); color: #1d4ed8; }

/* ── Filter bar ───────────────────────────────────────── */
.ss-filter {
    display: flex; flex-wrap: wrap; gap: .4rem; align-items: flex-end;
    background: var(--ss-surface); border: 1px solid var(--ss-border);
    border-radius: .55rem; padding: .5rem .7rem; margin-bottom: .7rem;
    box-shadow: var(--ss-shadow);
}
.ss-filter label {
    font-size: .58rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .6px; color: var(--ss-text-4); display: block; margin-bottom: .12rem;
}
.ss-filter input[type=date],
.ss-filter select {
    font-size: .73rem; padding: .28rem .45rem; border-radius: .4rem;
    border: 1.5px solid var(--ss-border); background: var(--bs-input-bg, #f8fafc);
    color: var(--ss-text-1); outline: none; transition: border-color .15s;
}
.ss-filter input[type=date]:focus,
.ss-filter select:focus { border-color: var(--ss-blue); box-shadow: 0 0 0 2px var(--ss-blue-dim); }
.ss-btn {
    font-size: .7rem; font-weight: 700; padding: .3rem .65rem; border-radius: 20px;
    border: none; cursor: pointer; display: inline-flex; align-items: center;
    gap: .22rem; transition: all .15s; text-decoration: none;
}
.ss-btn-apply { background: linear-gradient(135deg, var(--ss-blue), #1d4ed8); color: #fff; }
.ss-btn-apply:hover { box-shadow: 0 2px 10px rgba(59,130,246,.4); transform: translateY(-1px); }
.ss-btn-reset { background: transparent; border: 1.5px solid var(--ss-border) !important; color: var(--ss-text-3); }
.ss-btn-reset:hover { border-color: var(--ss-blue) !important; color: #1d4ed8; }
.ss-daterange {
    font-size: .63rem; color: var(--ss-text-3); display: flex; align-items: center;
    gap: .22rem; margin-left: .1rem; align-self: flex-end;
}
.ss-daterange strong { color: var(--ss-text-2); font-weight: 700; }

/* ── KPI Strip ────────────────────────────────────────── */
.ss-kpis {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: .45rem; margin-bottom: .7rem;
}
@media(max-width:860px) { .ss-kpis { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px) { .ss-kpis { grid-template-columns: 1fr; } }

.ss-kpi {
    background: var(--ss-surface); border: 1px solid var(--ss-border);
    border-radius: .55rem; padding: .55rem .75rem; position: relative;
    overflow: hidden; box-shadow: var(--ss-shadow);
}
.ss-kpi::before {
    content: ''; position: absolute; inset: 0 auto 0 0; width: 3.5px;
    border-radius: 2px 0 0 2px;
}
.ss-kpi-total::before   { background: linear-gradient(180deg, var(--ss-blue),   #1d4ed8); }
.ss-kpi-issued::before  { background: linear-gradient(180deg, var(--ss-green),  #16a34a); }
.ss-kpi-paid::before    { background: linear-gradient(180deg, var(--ss-teal),   #0d9488); }
.ss-kpi-nissued::before { background: linear-gradient(180deg, var(--ss-rose),   #e11d48); }

.ss-kpi-icon { position: absolute; right: .6rem; top: .5rem; font-size: 1.3rem; opacity: .06; font-weight: 900; }
.ss-kpi-lbl {
    font-size: .57rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: .18rem;
}
.ss-kpi-total .ss-kpi-lbl   { color: #1d4ed8; }
.ss-kpi-issued .ss-kpi-lbl  { color: #16a34a; }
.ss-kpi-paid .ss-kpi-lbl    { color: #0d9488; }
.ss-kpi-nissued .ss-kpi-lbl { color: #e11d48; }
.ss-kpi-val {
    font-size: 1.25rem; font-weight: 900; color: var(--ss-text-1);
    line-height: 1; font-variant-numeric: tabular-nums; letter-spacing: -.01em;
}
.ss-kpi-sub { font-size: .6rem; color: var(--ss-text-4); margin-top: .14rem; }

/* ── Main card ────────────────────────────────────────── */
.ss-card {
    background: var(--ss-surface); border: 1px solid var(--ss-border);
    border-radius: .55rem; overflow: hidden; box-shadow: var(--ss-shadow);
    margin-bottom: .5rem;
}
.ss-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: .45rem .7rem; border-bottom: 1px solid var(--ss-border);
    background: rgba(248,250,252,.5);
}
.ss-card-head h6 {
    margin: 0; font-size: .76rem; font-weight: 800; color: var(--ss-text-1);
    display: flex; align-items: center; gap: .28rem;
}
.ss-card-head h6 i { color: var(--ss-blue); font-size: .85rem; }
.ss-card-hint { font-size: .61rem; color: var(--ss-text-4); }

/* ── Pivot table ──────────────────────────────────────── */
.ss-tbl {
    width: 100%; border-collapse: separate; border-spacing: 0; font-size: .71rem;
    min-width: 780px;
}
.ss-tbl thead th {
    padding: .38rem .5rem; font-size: .57rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .5px; color: var(--ss-text-4);
    background: rgba(248,250,252,.95); border-bottom: 2px solid var(--ss-border);
    white-space: nowrap; position: sticky; top: 0; z-index: 2; text-align: right;
}
.ss-tbl thead th.th-carrier { text-align: left; }
.ss-tbl thead th.th-total   { color: var(--ss-blue) !important; }
.ss-tbl thead th.th-issued  { color: #16a34a !important; }
.ss-tbl thead th.th-paid    { color: #0d9488 !important; }
.ss-tbl thead th.th-nissued { color: #e11d48 !important; }
.ss-tbl thead th.th-npaid   { color: var(--ss-orange) !important; }
.ss-tbl thead th.th-died    { color: var(--ss-slate) !important; }
.ss-tbl thead th.th-declined { color: #7c3aed !important; }

.ss-tbl tbody td {
    padding: .38rem .5rem; border-bottom: 1px solid rgba(0,0,0,.025);
    vertical-align: middle; text-align: right;
}
.ss-tbl tbody td.td-carrier { text-align: left; }
.ss-tbl tbody tr:last-child td { border-bottom: none; }
.ss-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.35); }
.ss-tbl tbody tr:hover td { background: rgba(59,130,246,.025) !important; }

.ss-tbl tfoot td {
    padding: .44rem .5rem; border-top: 2px solid rgba(0,0,0,.08);
    font-weight: 800; background: rgba(59,130,246,.04);
    text-align: right;
}
.ss-tbl tfoot td.td-carrier { text-align: left; }

/* ── Carrier cell ─────────────────────────────────────── */
.ss-carrier-cell {
    display: flex; align-items: center; gap: .38rem; min-width: 140px; flex-wrap: wrap;
}
.ss-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.ss-cname {
    font-weight: 700; color: var(--ss-text-1); font-size: .72rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;
}
/* ── Partner badge ────────────────────────────────────── */
.ss-partner-badge {
    font-size: .58rem; font-weight: 800; padding: .05rem .28rem; border-radius: 10px;
    white-space: nowrap; letter-spacing: .15px; flex-shrink: 0;
}
.ss-pb-gold   { background: var(--ss-gold-dim);   color: var(--ss-gold-dark); }
.ss-pb-teal   { background: var(--ss-teal-dim);   color: var(--ss-teal); }
.ss-pb-indigo { background: var(--ss-indigo-dim); color: var(--ss-indigo); }
.ss-pb-none   { background: rgba(100,116,139,.08); color: var(--ss-text-4); font-weight: 600; }

/* ── Stage cell ───────────────────────────────────────── */
.ss-stage-link {
    display: inline-flex; align-items: center; justify-content: flex-end;
    text-decoration: none; font-variant-numeric: tabular-nums;
    font-size: .73rem; font-weight: 700; min-width: 38px;
    padding: .1rem .2rem; border-radius: 5px; transition: all .12s;
    color: var(--ss-text-2);
}
.ss-stage-link:hover { background: rgba(59,130,246,.1); color: var(--ss-blue); text-decoration: none; }
.ss-stage-link.sz-zero { color: var(--ss-text-4); font-weight: 400; font-size: .68rem; }

/* Stage color variants on hover */
.ss-col-total   .ss-stage-link:hover { background: var(--ss-blue-dim);   color: #1d4ed8; }
.ss-col-pc      .ss-stage-link:hover { background: var(--ss-gold-dim);   color: var(--ss-gold-dark); }
.ss-col-sub     .ss-stage-link:hover { background: var(--ss-indigo-dim); color: var(--ss-indigo); }
.ss-col-issued  .ss-stage-link:hover { background: var(--ss-green-dim);  color: #16a34a; }
.ss-col-nissued .ss-stage-link:hover { background: var(--ss-rose-dim);   color: #e11d48; }
.ss-col-paid    .ss-stage-link:hover { background: var(--ss-teal-dim);   color: #0d9488; }
.ss-col-npaid   .ss-stage-link:hover { background: var(--ss-orange-dim); color: #c2410c; }
.ss-col-died    .ss-stage-link:hover { background: var(--ss-slate-dim);  color: #475569; }
.ss-col-declined .ss-stage-link:hover { background: rgba(124,58,237,.08); color: #7c3aed; }

/* ── Progress bar strip ───────────────────────────────── */
.ss-prog-cell { min-width: 80px; }
.ss-prog {
    display: flex; align-items: center; gap: .28rem;
    justify-content: flex-end;
}
.ss-prog-track {
    width: 46px; height: 5px; border-radius: 5px; background: rgba(0,0,0,.06);
    overflow: hidden; flex-shrink: 0;
}
.ss-prog-fill { height: 100%; border-radius: 5px; }
.ss-pct { font-size: .6rem; color: var(--ss-text-4); font-weight: 600; min-width: 30px; text-align: right; }

/* ── Rank ─────────────────────────────────────────────── */
.ss-rank {
    text-align: center; font-size: .62rem; font-weight: 800;
    color: var(--ss-text-4); width: 24px;
}
.rank-1 { color: var(--ss-gold); }
.rank-2 { color: #94a3b8; }
.rank-3 { color: #cd7f32; }

/* ── Footer note ──────────────────────────────────────── */
.ss-footnote {
    font-size: .59rem; color: var(--ss-text-4); text-align: center; margin-top: .5rem;
    display: flex; align-items: center; justify-content: center; gap: .22rem; flex-wrap: wrap;
}
.ss-footnote code {
    font-size: .58rem; background: rgba(0,0,0,.05); padding: .02rem .22rem;
    border-radius: 3px; color: var(--ss-text-3);
}

/* ── Empty state ──────────────────────────────────────── */
.ss-empty { text-align: center; padding: 3rem 1rem; color: var(--ss-text-4); }
.ss-empty i { font-size: 2.5rem; display: block; margin-bottom: .5rem; opacity: .15; }
.ss-empty strong { display: block; font-size: .82rem; margin-bottom: .25rem; color: var(--ss-text-3); }

/* ── Dark mode ────────────────────────────────────────── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) {
    --ss-surface:  rgba(15,23,42,.55);
    --ss-border:   rgba(255,255,255,.07);
    --ss-shadow:   0 1px 4px rgba(0,0,0,.25), 0 0 0 1px rgba(255,255,255,.04);
    --ss-text-1:   #f1f5f9;
    --ss-text-2:   #cbd5e1;
    --ss-text-3:   #94a3b8;
    --ss-text-4:   #64748b;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .ss-tbl thead th,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .ss-tbl tfoot td { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.06); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .ss-card-head { background: rgba(15,23,42,.4); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .ss-filter input[type=date],
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .ss-filter select { background: rgba(15,23,42,.6); border-color: rgba(255,255,255,.09); color: #e2e8f0; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"])
    .ss-footnote code { background: rgba(255,255,255,.07); color: #94a3b8; }
</style>
@endsection

@section('content')
@php
$palette = [
    '#e11d48','#2563eb','#16a34a','#ea580c','#7c3aed',
    '#0891b2','#d97706','#db2777','#059669','#6d28d9',
    '#dc2626','#1d4ed8','#065f46','#9333ea',
];

$stageKeys = ['total_sales','submitted','pending_contract','issued','not_issued','paid','not_paid','policy_died','declined'];
$stageLabels = [
    'total_sales'      => 'Total Sales',
    'pending_contract' => 'Pending Contract',
    'submitted'        => 'Submitted',
    'issued'           => 'Issued',
    'not_issued'       => 'Not Issued',
    'paid'             => 'Paid',
    'not_paid'         => 'Not Paid',
    'policy_died'      => 'Policy Died / CB',
    'declined'         => 'Declined',
];
$stageIcons = [
    'total_sales'      => 'bx-check-double',
    'pending_contract' => 'bx-time-five',
    'submitted'        => 'bx-send',
    'issued'           => 'bx-badge-check',
    'not_issued'       => 'bx-x-circle',
    'paid'             => 'bx-dollar-circle',
    'not_paid'         => 'bx-error-circle',
    'policy_died'      => 'bx-ghost',
    'declined'         => 'bx-block',
];
$stageCols = [
    'total_sales'      => 'ss-col-total',
    'pending_contract' => 'ss-col-pc',
    'submitted'        => 'ss-col-sub',
    'issued'           => 'ss-col-issued',
    'not_issued'       => 'ss-col-nissued',
    'paid'             => 'ss-col-paid',
    'not_paid'         => 'ss-col-npaid',
    'policy_died'      => 'ss-col-died',
    'declined'         => 'ss-col-declined',
];
$thClasses = [
    'total_sales'      => 'th-total',
    'pending_contract' => '',
    'submitted'        => '',
    'issued'           => 'th-issued',
    'not_issued'       => 'th-nissued',
    'paid'             => 'th-paid',
    'not_paid'         => 'th-npaid',
    'policy_died'      => 'th-died',
    'declined'         => 'th-declined',
];

$grandTotal = $grandTotals['total_sales'] ?? 0;
@endphp

{{-- ── Page Header ──────────────────────────────────────── --}}
<div class="ss-hdr">
    <div class="ss-hdr-title">
        <div class="ss-hdr-icon"><i class="bx bx-table"></i></div>
        <div>
            <h5>Sales Status Report</h5>
            <span class="ss-hdr-sub">carrier breakdown · all pipeline stages in one view</span>
        </div>
    </div>
    <a href="{{ route('settings.reports.hub') }}" class="ss-back">
        <i class="bx bx-arrow-back"></i> Hub
    </a>
</div>

{{-- ── Filter Bar ────────────────────────────────────────── --}}
<div class="ss-filter">
    <form method="GET" action="{{ route('settings.reports.sales-status') }}" style="display:contents">
        <div>
            <label for="ss-from">From</label>
            <input type="date" id="ss-from" name="date_from" value="{{ $dateFrom }}">
        </div>
        <div>
            <label for="ss-to">To</label>
            <input type="date" id="ss-to" name="date_to" value="{{ $dateTo }}">
        </div>
        <div>
            <label for="ss-field">Date Field</label>
            <select id="ss-field" name="date_field" style="min-width:110px">
                <option value="sale_date" @selected($dateField === 'sale_date')>Sale Date</option>
                <option value="paid_at"   @selected($dateField === 'paid_at')>Paid Date</option>
            </select>
        </div>
        <div>
            <label for="ss-carrier">Carrier</label>
            <select id="ss-carrier" name="carrier_id" style="min-width:150px">
                <option value="">— All Carriers —</option>
                @foreach($allCarriers as $c)
                    <option value="{{ $c->id }}" @selected($carrierId == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="ss-team">Team</label>
            <select id="ss-team" name="team" style="min-width:110px">
                <option value="">All Teams</option>
                <option value="peregrine" @selected(($team ?? '') === 'peregrine')>Peregrine</option>
                <option value="ravens"    @selected(($team ?? '') === 'ravens')>Ravens</option>
            </select>
        </div>
        <button type="submit" class="ss-btn ss-btn-apply" style="align-self:flex-end">
            <i class="bx bx-search-alt"></i> Apply
        </button>
        <a href="{{ route('settings.reports.sales-status') }}" class="ss-btn ss-btn-reset" style="align-self:flex-end">
            <i class="bx bx-reset"></i> Reset
        </a>
        @if($dateFrom || $dateTo)
        <span class="ss-daterange">
            <i class="bx bx-calendar-check" style="color:var(--ss-blue);font-size:.82rem"></i>
            <strong>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : '—' }}</strong>
            <span style="color:var(--ss-text-4)">→</span>
            <strong>{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : '—' }}</strong>
            <span style="opacity:.5;margin-left:.1rem">using {{ $dateField === 'paid_at' ? 'paid date' : 'sale date' }}</span>
        </span>
        @endif
    </form>
</div>

{{-- ── KPI Strip ─────────────────────────────────────────── --}}
<div class="ss-kpis">
    <div class="ss-kpi ss-kpi-total">
        <i class="bx bx-check-double ss-kpi-icon"></i>
        <div class="ss-kpi-lbl">Total Sales</div>
        <div class="ss-kpi-val">{{ number_format($grandTotals['total_sales'] ?? 0) }}</div>
        <div class="ss-kpi-sub">All leads with a sale recorded</div>
    </div>
    <div class="ss-kpi ss-kpi-issued">
        <i class="bx bx-badge-check ss-kpi-icon"></i>
        <div class="ss-kpi-lbl">Issued</div>
        <div class="ss-kpi-val">{{ number_format($grandTotals['issued'] ?? 0) }}</div>
        <div class="ss-kpi-sub">
            @php $issPct = ($grandTotals['total_sales']??0) > 0 ? round(($grandTotals['issued']??0) / $grandTotals['total_sales'] * 100, 1) : 0 @endphp
            {{ $issPct }}% issuance rate
        </div>
    </div>
    <div class="ss-kpi ss-kpi-paid">
        <i class="bx bx-dollar-circle ss-kpi-icon"></i>
        <div class="ss-kpi-lbl">Paid</div>
        <div class="ss-kpi-val">{{ number_format($grandTotals['paid'] ?? 0) }}</div>
        <div class="ss-kpi-sub">
            @php $paidPct = ($grandTotals['total_sales']??0) > 0 ? round(($grandTotals['paid']??0) / $grandTotals['total_sales'] * 100, 1) : 0 @endphp
            {{ $paidPct }}% payment rate
        </div>
    </div>
    <div class="ss-kpi ss-kpi-nissued">
        <i class="bx bx-x-circle ss-kpi-icon"></i>
        <div class="ss-kpi-lbl">Not Issued</div>
        <div class="ss-kpi-val">{{ number_format($grandTotals['not_issued'] ?? 0) }}</div>
        <div class="ss-kpi-sub">
            @php $niPct = ($grandTotals['total_sales']??0) > 0 ? round(($grandTotals['not_issued']??0) / $grandTotals['total_sales'] * 100, 1) : 0 @endphp
            {{ $niPct }}% not issued
        </div>
    </div>
</div>

{{-- ── Pivot Table ────────────────────────────────────────── --}}
<div class="ss-card">
    <div class="ss-card-head">
        <h6>
            <i class="bx bx-building"></i>
            Sales Status by Carrier
            @if($carrierId)
                <span style="font-size:.65rem;font-weight:400;color:var(--ss-text-3);margin-left:.3rem">
                    — filtered: {{ $allCarriers->firstWhere('id', $carrierId)?->name ?? 'Selected Carrier' }}
                </span>
            @endif
        </h6>
        <span class="ss-card-hint">Click any number → drilldown to individual leads</span>
    </div>

    @if($carriersData->isEmpty())
    <div class="ss-empty">
        <i class="bx bx-bar-chart-alt-2"></i>
        <strong>No sales found</strong>
        <span>No leads with a sale recorded for this date range and filter.</span>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="ss-tbl">
            <thead>
                <tr>
                    <th style="width:24px;text-align:center">#</th>
                    <th class="th-carrier" style="min-width:160px;text-align:left">Carrier</th>
                    @foreach($stageKeys as $sk)
                    <th class="{{ $thClasses[$sk] }}" style="min-width:68px" title="{{ $stageLabels[$sk] }}">
                        <i class="bx {{ $stageIcons[$sk] }}" style="font-weight:400"></i>
                        {{ $sk === 'policy_died' ? 'Died/CB' : $stageLabels[$sk] }}
                    </th>
                    @endforeach
                    <th style="min-width:88px;text-align:right">Share</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carriersData as $row)
                @php
                    $rank   = $loop->iteration;
                    $color  = $palette[($rank - 1) % count($palette)];
                    $pct    = $grandTotal > 0 ? round(($row->total_sales / $grandTotal) * 100, 1) : 0;
                    $baseParams = array_filter([
                        'date_from'    => $dateFrom,
                        'date_to'      => $dateTo,
                        'date_field'   => $dateField,
                        'carrier_name' => $row->carrier_name,
                        'partner_id'   => $row->partner_id ?? 'none',
                        'team'         => $team ?? null,
                    ]);
                    $badgeVariants = ['ss-pb-gold','ss-pb-teal','ss-pb-indigo'];
                    $badgeClass = $row->partner_id ? $badgeVariants[$row->partner_id % count($badgeVariants)] : 'ss-pb-none';
                @endphp
                <tr>
                    {{-- Rank --}}
                    <td class="ss-rank" style="text-align:center">
                        <span class="{{ $rank===1?'rank-1':($rank===2?'rank-2':($rank===3?'rank-3':'')) }}">{{ $rank }}</span>
                    </td>
                    {{-- Carrier · Partner --}}
                    <td class="td-carrier">
                        <div class="ss-carrier-cell">
                            <span class="ss-dot" style="background:{{ $color }};box-shadow:0 0 0 2px {{ $color }}30"></span>
                            <span class="ss-cname" title="{{ $row->carrier_name }}">{{ $row->carrier_name }}</span>
                            @if($row->assigned_partner)
                                <span class="ss-partner-badge {{ $badgeClass }}">{{ $row->assigned_partner }}</span>
                            @else
                                <span class="ss-partner-badge ss-pb-none">No Partner</span>
                            @endif
                        </div>
                    </td>
                    {{-- Stage cells --}}
                    @foreach($stageKeys as $sk)
                    @php
                        $cnt = $row->{$sk} ?? 0;
                        $ddUrl = route('settings.reports.sales-status.drilldown', array_merge($baseParams, ['stage' => $sk]));
                    @endphp
                    <td class="{{ $stageCols[$sk] }}">
                        @if($cnt > 0)
                        <a href="{{ $ddUrl }}" class="ss-stage-link" title="{{ $stageLabels[$sk] }}: {{ $cnt }} leads">
                            {{ number_format($cnt) }}
                        </a>
                        @else
                        <span class="ss-stage-link sz-zero">—</span>
                        @endif
                    </td>
                    @endforeach
                    {{-- Share bar --}}
                    <td class="ss-prog-cell">
                        <div class="ss-prog">
                            <div class="ss-prog-track">
                                <div class="ss-prog-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                            </div>
                            <span class="ss-pct">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="td-carrier">
                        <span style="display:inline-flex;align-items:center;gap:.22rem;font-size:.7rem">
                            <i class="bx bx-sum" style="color:var(--ss-blue)"></i> TOTAL
                        </span>
                    </td>
                    @foreach($stageKeys as $sk)
                    @php $tot = $grandTotals[$sk] ?? 0; @endphp
                    <td>
                        <span style="font-size:.74rem;font-variant-numeric:tabular-nums">
                            {{ number_format($tot) }}
                        </span>
                    </td>
                    @endforeach
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

<div class="ss-footnote">
    <i class="bx bx-info-circle"></i>
    Sourced from all leads with <code>sale_at IS NOT NULL</code>.
    Date filter applied to <code>{{ $dateField === 'paid_at' ? 'paid_at' : 'sale_date' }}</code>.
    Click any number to drill down to the individual leads behind it.
</div>
@endsection
