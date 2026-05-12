@extends('layouts.master')

@section('title')
    Policy Type Report
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
:root {
    --pt-gold:        #d4af37;
    --pt-gold-dim:    rgba(212,175,55,.12);
    --pt-gold-dark:   #92760d;
    --pt-teal:        #0891b2;
    --pt-teal-dim:    rgba(8,145,178,.12);
    --pt-indigo:      #6366f1;
    --pt-indigo-dim:  rgba(99,102,241,.12);
    --pt-green:       #16a34a;
    --pt-green-dim:   rgba(22,163,74,.11);
    --pt-surface:     var(--bs-card-bg, #ffffff);
    --pt-border:      rgba(0,0,0,.07);
    --pt-shadow:      0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --pt-text-1:      var(--bs-surface-900, #0f172a);
    --pt-text-2:      var(--bs-surface-700, #374151);
    --pt-text-3:      var(--bs-surface-500, #64748b);
    --pt-text-4:      var(--bs-surface-400, #94a3b8);
}
.pt-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:.6rem; flex-wrap:wrap; gap:.35rem; }
.pt-hdr-title { display:flex; align-items:center; gap:.45rem; flex-wrap:wrap; }
.pt-hdr-icon { width:28px; height:28px; border-radius:.4rem; flex-shrink:0; background:linear-gradient(135deg,var(--pt-teal),#0369a1); display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(8,145,178,.35); }
.pt-hdr-icon i { font-size:.95rem; color:#fff; }
.pt-hdr h5 { margin:0; font-size:.95rem; font-weight:800; color:var(--pt-text-1); }
.pt-hdr-sub { font-size:.67rem; color:var(--pt-text-3); font-weight:400; border-left:2px solid var(--pt-border); padding-left:.45rem; margin-left:.1rem; }
.pt-back { font-size:.7rem; font-weight:700; padding:.28rem .6rem; border-radius:20px; border:1.5px solid var(--pt-border); background:transparent; color:var(--pt-text-3); text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s; }
.pt-back:hover { border-color:var(--pt-teal); color:var(--pt-teal); }
.pt-filter { display:flex; flex-wrap:wrap; gap:.4rem; align-items:flex-end; background:var(--pt-surface); border:1px solid var(--pt-border); border-radius:.55rem; padding:.5rem .7rem; margin-bottom:.7rem; box-shadow:var(--pt-shadow); }
.pt-filter label { font-size:.58rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:var(--pt-text-4); display:block; margin-bottom:.12rem; }
.pt-filter input[type=date] { font-size:.73rem; padding:.28rem .45rem; border-radius:.4rem; border:1.5px solid var(--pt-border); background:var(--bs-input-bg,#f8fafc); color:var(--pt-text-1); outline:none; transition:border-color .15s; }
.pt-filter input[type=date]:focus { border-color:var(--pt-teal); box-shadow:0 0 0 2px var(--pt-teal-dim); }
.pt-btn { font-size:.7rem; font-weight:700; padding:.3rem .65rem; border-radius:20px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s; text-decoration:none; }
.pt-btn-apply { background:linear-gradient(135deg,var(--pt-teal),#0369a1); color:#fff; }
.pt-btn-apply:hover { box-shadow:0 2px 10px rgba(8,145,178,.4); transform:translateY(-1px); color:#fff; }
.pt-btn-reset { background:transparent; border:1.5px solid var(--pt-border) !important; color:var(--pt-text-3); }
.pt-btn-reset:hover { border-color:var(--pt-teal) !important; color:var(--pt-teal); }
.pt-daterange { font-size:.63rem; color:var(--pt-text-3); display:flex; align-items:center; gap:.22rem; align-self:flex-end; }
.pt-daterange strong { color:var(--pt-text-2); font-weight:700; }
.pt-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:.45rem; margin-bottom:.7rem; }
@media(max-width:860px){ .pt-kpis{grid-template-columns:repeat(2,1fr);} }
@media(max-width:480px){ .pt-kpis{grid-template-columns:1fr;} }
.pt-kpi { background:var(--pt-surface); border:1px solid var(--pt-border); border-radius:.55rem; padding:.55rem .75rem; position:relative; overflow:hidden; box-shadow:var(--pt-shadow); }
.pt-kpi::before { content:''; position:absolute; inset:0 auto 0 0; width:3.5px; border-radius:2px 0 0 2px; }
.pt-k-sales::before   { background:linear-gradient(180deg,var(--pt-teal),#0369a1); }
.pt-k-premium::before { background:linear-gradient(180deg,var(--pt-green),#15803d); }
.pt-k-revenue::before { background:linear-gradient(180deg,var(--pt-indigo),#4338ca); }
.pt-k-avg::before     { background:linear-gradient(180deg,var(--pt-gold),#b8941f); }
.pt-kpi-icon { position:absolute; right:.6rem; top:.5rem; font-size:1.3rem; opacity:.06; }
.pt-kpi-lbl { font-size:.57rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; margin-bottom:.18rem; }
.pt-k-sales .pt-kpi-lbl   { color:#0369a1; }
.pt-k-premium .pt-kpi-lbl { color:#15803d; }
.pt-k-revenue .pt-kpi-lbl { color:#4338ca; }
.pt-k-avg .pt-kpi-lbl     { color:var(--pt-gold-dark); }
.pt-kpi-val { font-size:1.25rem; font-weight:900; color:var(--pt-text-1); line-height:1; font-variant-numeric:tabular-nums; }
.pt-kpi-sub { font-size:.6rem; color:var(--pt-text-4); margin-top:.14rem; }
.pt-body { display:grid; grid-template-columns:1fr 210px; gap:.7rem; align-items:start; }
@media(max-width:780px){ .pt-body{grid-template-columns:1fr;} .pt-donut-panel{display:none;} }
.pt-card { background:var(--pt-surface); border:1px solid var(--pt-border); border-radius:.55rem; overflow:hidden; box-shadow:var(--pt-shadow); }
.pt-card-head { display:flex; align-items:center; justify-content:space-between; padding:.45rem .7rem; border-bottom:1px solid var(--pt-border); background:rgba(248,250,252,.5); }
.pt-card-head h6 { margin:0; font-size:.76rem; font-weight:800; color:var(--pt-text-1); display:flex; align-items:center; gap:.28rem; }
.pt-card-head h6 i { color:var(--pt-teal); font-size:.85rem; }
.pt-card-hint { font-size:.61rem; color:var(--pt-text-4); }
.pt-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:.71rem; }
.pt-tbl thead th { padding:.38rem .55rem; font-size:.58rem; font-weight:800; text-transform:uppercase; letter-spacing:.55px; color:var(--pt-text-4); background:rgba(248,250,252,.95); border-bottom:2px solid var(--pt-border); white-space:nowrap; position:sticky; top:0; z-index:2; }
.pt-tbl thead th.th-r { text-align:right; }
.pt-tbl tbody td { padding:.4rem .55rem; border-bottom:1px solid rgba(0,0,0,.025); vertical-align:middle; }
.pt-tbl tbody tr:last-child td { border-bottom:none; }
.pt-tbl tbody tr.pt-row { cursor:pointer; }
.pt-tbl tbody tr.pt-row:hover td { background:rgba(8,145,178,.035); }
.pt-tbl tfoot td { padding:.44rem .55rem; border-top:2px solid rgba(0,0,0,.08); font-weight:800; background:rgba(8,145,178,.04); }
.pt-rank { text-align:center; font-size:.63rem; font-weight:800; color:var(--pt-text-4); width:28px; }
.rank-1{color:var(--pt-gold);} .rank-2{color:#94a3b8;} .rank-3{color:#cd7f32;}
.pt-type-pill { display:inline-flex; align-items:center; gap:.2rem; font-size:.62rem; font-weight:800; padding:.06rem .32rem; border-radius:12px; white-space:nowrap; }
.pt-cp-cell { display:inline-flex; align-items:center; gap:.32rem; max-width:210px; }
.pt-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.pt-cname { font-weight:700; color:var(--pt-text-1); font-size:.72rem; white-space:nowrap; transition:color .12s; }
.pt-row:hover .pt-cname { color:var(--pt-teal); }
.pt-partner-badge { font-size:.58rem; font-weight:800; padding:.05rem .28rem; border-radius:10px; white-space:nowrap; }
.pt-pb-gold   { background:var(--pt-gold-dim);    color:var(--pt-gold-dark); }
.pt-pb-teal   { background:var(--pt-teal-dim);    color:var(--pt-teal); }
.pt-pb-indigo { background:var(--pt-indigo-dim);  color:var(--pt-indigo); }
.pt-pb-none   { background:rgba(100,116,139,.08); color:var(--pt-text-4); }
.pt-share { display:flex; align-items:center; gap:.32rem; }
.pt-bar-track { flex:1; height:5px; border-radius:5px; background:rgba(0,0,0,.06); overflow:hidden; min-width:44px; }
.pt-bar-fill  { height:100%; border-radius:5px; transition:width .4s ease; }
.pt-pct { font-size:.6rem; color:var(--pt-text-4); min-width:30px; text-align:right; font-weight:600; }
.td-r { text-align:right; font-variant-numeric:tabular-nums; }
.pt-sales-num { font-size:.82rem; font-weight:800; color:var(--pt-text-1); }
.pt-chip { display:inline-flex; align-items:center; gap:.16rem; font-size:.63rem; font-weight:700; padding:.08rem .3rem; border-radius:14px; }
.pt-chip i { font-size:.68rem; }
.pt-chip-green  { background:var(--pt-green-dim);  color:#15803d; }
.pt-chip-indigo { background:var(--pt-indigo-dim); color:var(--pt-indigo); }
.pt-chip-none   { background:rgba(100,116,139,.07); color:var(--pt-text-4); font-style:italic; }
th.th-revenue { color:var(--pt-indigo) !important; }
.pt-avg { font-size:.67rem; color:var(--pt-text-3); text-align:right; }
.pt-norev { font-size:.6rem; color:var(--pt-text-4); font-style:italic; display:inline-flex; align-items:center; gap:.15rem; }
.pt-donut-panel { background:var(--pt-surface); border:1px solid var(--pt-border); border-radius:.55rem; padding:.55rem .6rem; box-shadow:var(--pt-shadow); }
.pt-donut-title { font-size:.58rem; font-weight:800; text-transform:uppercase; letter-spacing:.55px; color:var(--pt-text-4); margin-bottom:.45rem; }
.pt-legend-row { display:flex; align-items:center; gap:.3rem; margin-bottom:.22rem; }
.pt-legend-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.pt-legend-name { font-size:.62rem; color:var(--pt-text-2); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.pt-legend-pct { font-size:.6rem; font-weight:800; color:var(--pt-text-3); }
.pt-empty { text-align:center; padding:3rem 1rem; color:var(--pt-text-4); }
.pt-empty i { font-size:2.5rem; display:block; margin-bottom:.5rem; opacity:.15; }
.pt-empty strong { display:block; font-size:.82rem; margin-bottom:.25rem; color:var(--pt-text-3); }
.pt-footnote { font-size:.59rem; color:var(--pt-text-4); text-align:center; margin-top:.5rem; display:flex; align-items:center; justify-content:center; gap:.22rem; }
.pt-footnote code { font-size:.58rem; background:rgba(0,0,0,.05); padding:.02rem .22rem; border-radius:3px; color:var(--pt-text-3); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) {
    --pt-surface:rgba(15,23,42,.55);--pt-border:rgba(255,255,255,.07);--pt-shadow:0 1px 4px rgba(0,0,0,.25),0 0 0 1px rgba(255,255,255,.04);
    --pt-text-1:#f1f5f9;--pt-text-2:#cbd5e1;--pt-text-3:#94a3b8;--pt-text-4:#64748b;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pt-tbl thead th{background:rgba(15,23,42,.8);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pt-tbl tfoot td{background:rgba(8,145,178,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pt-filter input[type=date]{background:rgba(15,23,42,.6);border-color:rgba(255,255,255,.09);color:#e2e8f0;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pt-card-head{background:rgba(15,23,42,.4);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pt-chip-green{background:rgba(34,197,94,.12);color:#4ade80;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pt-chip-indigo{background:rgba(99,102,241,.15);color:#a5b4fc;}
</style>
@endsection

@section('content')
@php
$typeColors = ['Level'=>'#16a34a','Graded'=>'#ea580c','G.I'=>'#0891b2','Modified'=>'#7c3aed','Unknown'=>'#94a3b8'];
$fallback   = ['#2563eb','#db2777','#d97706','#059669','#6d28d9','#dc2626'];
$badgeVar   = ['pt-pb-gold','pt-pb-teal','pt-pb-indigo'];
$grandTotal = $grandTotalSales;
@endphp

<div class="pt-hdr">
    <div class="pt-hdr-title">
        <div class="pt-hdr-icon"><i class="bx bx-category"></i></div>
        <div>
            <h5>Policy Type Report</h5>
            <span class="pt-hdr-sub">policy type · carrier · partner breakdown</span>
        </div>
    </div>
    <a href="{{ route('settings.reports.hub') }}" class="pt-back"><i class="bx bx-arrow-back"></i> Hub</a>
</div>

<div class="pt-filter">
    <form method="GET" action="{{ route('settings.reports.policy-type-report') }}" style="display:contents">
        <div><label for="pt-from">From</label><input type="date" id="pt-from" name="date_from" value="{{ $dateFrom }}"></div>
        <div><label for="pt-to">To</label><input type="date" id="pt-to" name="date_to" value="{{ $dateTo }}"></div>
        <div>
            <label for="pt-team">Team</label>
            <select id="pt-team" name="team" style="font-size:.73rem;padding:.28rem .45rem;border-radius:.4rem;border:1.5px solid var(--pt-border);background:var(--bs-input-bg,#f8fafc);color:var(--pt-text-1);outline:none;transition:border-color .15s;min-width:110px">
                <option value="">All Teams</option>
                <option value="peregrine" @selected(($team ?? '') === 'peregrine')>Peregrine</option>
                <option value="ravens"    @selected(($team ?? '') === 'ravens')>Ravens</option>
            </select>
        </div>
        <button type="submit" class="pt-btn pt-btn-apply" style="align-self:flex-end"><i class="bx bx-search-alt"></i> Apply</button>
        <a href="{{ route('settings.reports.policy-type-report') }}" class="pt-btn pt-btn-reset" style="align-self:flex-end"><i class="bx bx-reset"></i> Reset</a>
        @if($dateFrom || $dateTo || ($team ?? null))
        <span class="pt-daterange">
            <i class="bx bx-calendar-check" style="color:var(--pt-teal);font-size:.82rem"></i>
            <strong>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : '—' }}</strong>
            <span style="color:var(--pt-text-4)">→</span>
            <strong>{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : '—' }}</strong>
            @if($team ?? null)
                <span style="margin-left:.25rem;font-size:.62rem;font-weight:700;color:var(--pt-text-2)">· {{ ucfirst($team) }}</span>
            @endif
        </span>
        @endif
    </form>
</div>

<div class="pt-kpis">
    <div class="pt-kpi pt-k-sales">
        <i class="bx bx-bar-chart pt-kpi-icon"></i>
        <div class="pt-kpi-lbl">Total Sales</div>
        <div class="pt-kpi-val">{{ number_format($grandTotalSales) }}</div>
        <div class="pt-kpi-sub">{{ $policyData->count() }} combinations</div>
    </div>
    <div class="pt-kpi pt-k-premium">
        <i class="bx bx-dollar-circle pt-kpi-icon"></i>
        <div class="pt-kpi-lbl">Total Premium</div>
        <div class="pt-kpi-val">${{ number_format($grandTotalPremium, 2) }}</div>
        <div class="pt-kpi-sub">Monthly premium total</div>
    </div>
    <div class="pt-kpi pt-k-revenue">
        <i class="bx bx-trending-up pt-kpi-icon"></i>
        <div class="pt-kpi-lbl">Est. Revenue</div>
        <div class="pt-kpi-val">${{ number_format($grandTotalRevenue, 2) }}</div>
        <div class="pt-kpi-sub">premium × 9 × commission%</div>
    </div>
    <div class="pt-kpi pt-k-avg">
        <i class="bx bx-calculator pt-kpi-icon"></i>
        <div class="pt-kpi-lbl">Avg Premium / Sale</div>
        <div class="pt-kpi-val">${{ $grandTotalSales > 0 ? number_format($grandTotalPremium/$grandTotalSales,2) : '0.00' }}</div>
        <div class="pt-kpi-sub">Overall average</div>
    </div>
</div>

<div class="pt-body">

    <div class="pt-card">
        <div class="pt-card-head">
            <h6><i class="bx bx-table"></i> Policy Type · Carrier · Partner</h6>
            <span class="pt-card-hint">Click any row → individual sales</span>
        </div>

        @if($policyData->isEmpty())
        <div class="pt-empty">
            <i class="bx bx-bar-chart-alt-2"></i>
            <strong>No sales found</strong>
            <span>No sales for this date range.</span>
        </div>
        @else
        <div style="overflow-x:auto">
            <table class="pt-tbl">
                <thead>
                    <tr>
                        <th style="width:26px">#</th>
                        <th style="min-width:82px">Type</th>
                        <th style="min-width:190px">Carrier · Partner</th>
                        <th style="min-width:100px">Share</th>
                        <th class="th-r" style="min-width:55px">Sales</th>
                        <th class="th-r" style="min-width:115px">Monthly Premium</th>
                        <th class="th-r th-revenue" style="min-width:115px">
                            <span style="display:inline-flex;align-items:center;gap:.18rem"><i class="bx bx-trending-up"></i> Est. Revenue</span>
                        </th>
                        <th class="th-r" style="min-width:88px">Avg Premium</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($policyData as $idx => $row)
                    @php
                        $rank       = $idx + 1;
                        $pct        = $grandTotal > 0 ? round(($row->total_sales / $grandTotal) * 100, 1) : 0;
                        $typeColor  = $typeColors[$row->policy_type] ?? $fallback[$idx % count($fallback)];
                        $avgPremium = $row->total_sales > 0 ? $row->total_premium / $row->total_sales : 0;
                        $hasRev     = $row->total_revenue > 0;
                        $badgeClass = $row->assigned_partner
                            ? $badgeVar[abs(crc32($row->assigned_partner)) % count($badgeVar)]
                            : 'pt-pb-none';
                        $ddUrl = route('settings.reports.policy-type-report.drilldown', array_filter([
                            'date_from'        => $dateFrom,
                            'date_to'          => $dateTo,
                            'policy_type'      => $row->policy_type,
                            'carrier_name'     => $row->carrier_name ?? '',
                            'assigned_partner' => $row->assigned_partner ?? '',
                            'team'             => $team ?? null,
                        ]));
                    @endphp
                    <tr class="pt-row" onclick="window.location='{{ $ddUrl }}'">
                        <td class="pt-rank {{ $rank===1?'rank-1':($rank===2?'rank-2':($rank===3?'rank-3':'')) }}">
                            @if($rank===1)<i class="bx bxs-crown"></i>@else{{ $rank }}@endif
                        </td>
                        <td>
                            <span class="pt-type-pill" style="background:{{ $typeColor }}1a;color:{{ $typeColor }};border:1px solid {{ $typeColor }}33">
                                {{ $row->policy_type }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ $ddUrl }}" class="pt-cp-cell" onclick="event.stopPropagation()" style="text-decoration:none">
                                <span class="pt-dot" style="background:{{ $typeColor }};box-shadow:0 0 0 2px {{ $typeColor }}30"></span>
                                <span class="pt-cname">{{ $row->carrier_name ?: '—' }}</span>
                                @if($row->assigned_partner)
                                    <span class="pt-partner-badge {{ $badgeClass }}">{{ $row->assigned_partner }}</span>
                                @else
                                    <span class="pt-partner-badge pt-pb-none">—</span>
                                @endif
                            </a>
                        </td>
                        <td>
                            <div class="pt-share">
                                <div class="pt-bar-track"><div class="pt-bar-fill" style="width:{{ $pct }}%;background:{{ $typeColor }}"></div></div>
                                <span class="pt-pct">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="td-r"><span class="pt-sales-num">{{ number_format($row->total_sales) }}</span></td>
                        <td class="td-r">
                            <span class="pt-chip pt-chip-green"><i class="bx bx-dollar"></i>{{ number_format($row->total_premium, 2) }}</span>
                        </td>
                        <td class="td-r">
                            @if($hasRev)
                                <span class="pt-chip pt-chip-indigo"><i class="bx bx-trending-up"></i>{{ number_format($row->total_revenue, 2) }}</span>
                            @else
                                <span class="pt-norev"><i class="bx bx-minus"></i> no rate</span>
                            @endif
                        </td>
                        <td class="pt-avg">${{ number_format($avgPremium, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><span style="display:inline-flex;align-items:center;gap:.22rem;font-size:.7rem"><i class="bx bx-sum" style="color:var(--pt-teal)"></i> TOTAL</span></td>
                        <td></td>
                        <td class="td-r"><span class="pt-sales-num">{{ number_format($grandTotalSales) }}</span></td>
                        <td class="td-r"><span class="pt-chip pt-chip-green"><i class="bx bx-dollar"></i>{{ number_format($grandTotalPremium, 2) }}</span></td>
                        <td class="td-r">
                            @if($grandTotalRevenue > 0)
                                <span class="pt-chip pt-chip-indigo"><i class="bx bx-trending-up"></i>{{ number_format($grandTotalRevenue, 2) }}</span>
                            @else
                                <span class="pt-norev"><i class="bx bx-minus"></i> —</span>
                            @endif
                        </td>
                        <td class="pt-avg">${{ $grandTotalSales > 0 ? number_format($grandTotalPremium/$grandTotalSales,2) : '0.00' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    @if($policyData->isNotEmpty())
    @php
        $typeAgg   = $policyData->groupBy('policy_type')->map(fn($g)=>$g->sum('total_sales'))->sortDesc();
        $donutTot  = $typeAgg->sum();
        $innerR=52; $circum=2*M_PI*$innerR; $cumPct=0; $slices=[];
        foreach($typeAgg as $type=>$cnt){
            $p=$donutTot>0?$cnt/$donutTot:0;
            $c=$typeColors[$type]??'#94a3b8';
            $slices[]=['pct'=>$p,'color'=>$c,'cum'=>$cumPct,'type'=>$type,'cnt'=>$cnt];
            $cumPct+=$p;
        }
        $carrierAgg=$policyData->groupBy('carrier_name')->map(fn($g)=>$g->sum('total_sales'))->sortDesc()->take(5);
    @endphp
    <div class="pt-donut-panel">
        <div class="pt-donut-title">By Type</div>
        <svg viewBox="0 0 164 164" style="width:100%;max-width:164px;display:block;margin:0 auto">
            <circle cx="82" cy="82" r="{{ $innerR }}" fill="none" stroke="rgba(0,0,0,.05)" stroke-width="22"/>
            @foreach($slices as $sl)
            @php $dash=$circum*$sl['pct'];$gap=$circum-$dash;$off=$circum*.25-$circum*$sl['cum']; @endphp
            <circle cx="82" cy="82" r="{{ $innerR }}" fill="none" stroke="{{ $sl['color'] }}" stroke-width="22"
                stroke-dasharray="{{ number_format($dash,4,'.',''). ' '.number_format($gap,4,'.','')}}"
                stroke-dashoffset="{{ number_format($off,4,'.','')}}" />
            @endforeach
            <text x="82" y="77" text-anchor="middle" font-size="18" font-weight="900" fill="var(--pt-text-1)">{{ number_format($grandTotalSales) }}</text>
            <text x="82" y="92" text-anchor="middle" font-size="7.5" fill="var(--pt-text-4)" font-weight="700" letter-spacing=".6">SALES</text>
        </svg>
        <div style="margin-top:.5rem">
            @foreach($slices as $sl)
            @php $lp=$donutTot>0?round($sl['pct']*100,1):0 @endphp
            <div class="pt-legend-row">
                <span class="pt-legend-dot" style="background:{{ $sl['color'] }}"></span>
                <span class="pt-legend-name">{{ $sl['type'] }}</span>
                <span class="pt-legend-pct">{{ $lp }}%</span>
            </div>
            @endforeach
        </div>
        <div style="margin-top:.5rem;padding-top:.45rem;border-top:1px solid var(--pt-border)">
            <div class="pt-donut-title" style="margin-bottom:.3rem">Top Carriers</div>
            @foreach($carrierAgg as $cn=>$cnt)
            <div class="pt-legend-row">
                <span class="pt-legend-dot" style="background:var(--pt-teal)"></span>
                <span class="pt-legend-name">{{ $cn ?: '—' }}</span>
                <span class="pt-legend-pct">{{ $cnt }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<div class="pt-footnote">
    <i class="bx bx-info-circle"></i>
    All sales where <code>sale_at IS NOT NULL</code> · Revenue = <code>premium × 9 × commission%</code>
</div>
@endsection
