@extends('layouts.master')

@section('title')
    Policy Type Drilldown — {{ $policyType }}
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ════════════════════════════════════════════════════════
   POLICY TYPE DRILLDOWN — Design System
   ════════════════════════════════════════════════════════ */
:root {
    --dd-gold:       #d4af37;
    --dd-gold-dim:   rgba(212,175,55,.12);
    --dd-gold-dark:  #92760d;
    --dd-green:      #22c55e;
    --dd-green-dim:  rgba(34,197,94,.11);
    --dd-indigo:     #6366f1;
    --dd-indigo-dim: rgba(99,102,241,.12);
    --dd-teal:       #0891b2;
    --dd-teal-dim:   rgba(8,145,178,.12);
    --dd-surface:    var(--bs-card-bg, #ffffff);
    --dd-border:     rgba(0,0,0,.07);
    --dd-shadow:     0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --dd-text-1:     var(--bs-surface-900, #0f172a);
    --dd-text-2:     var(--bs-surface-700, #374151);
    --dd-text-3:     var(--bs-surface-500, #64748b);
    --dd-text-4:     var(--bs-surface-400, #94a3b8);
}

/* ── Breadcrumb header ────────────────────────────────── */
.dd-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: .6rem; flex-wrap: wrap; gap: .35rem; }
.dd-hdr-left { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.dd-back {
    font-size: .7rem; font-weight: 700; padding: .28rem .6rem; border-radius: 20px;
    border: 1.5px solid var(--dd-border); background: transparent;
    color: var(--dd-text-3); text-decoration: none; display: inline-flex; align-items: center; gap: .22rem; transition: all .15s; flex-shrink: 0;
}
.dd-back:hover { border-color: var(--dd-teal); color: var(--dd-teal); }
.dd-breadcrumb { font-size: .67rem; color: var(--dd-text-4); display: flex; align-items: center; gap: .28rem; }
.dd-breadcrumb a { color: var(--dd-text-3); text-decoration: none; }
.dd-breadcrumb a:hover { color: var(--dd-teal); }
.dd-bc-sep { opacity: .4; }
.dd-bc-current { color: var(--dd-text-2); font-weight: 700; }

/* ── Context strip ────────────────────────────────────── */
.dd-ctx {
    display: flex; align-items: center; gap: .45rem; flex-wrap: wrap;
    background: var(--dd-surface); border: 1px solid var(--dd-border);
    border-radius: .55rem; padding: .45rem .7rem; margin-bottom: .6rem; box-shadow: var(--dd-shadow);
}
.dd-ctx-type { font-size: .88rem; font-weight: 800; color: var(--dd-text-1); display: flex; align-items: center; gap: .35rem; }
.dd-ctx-type i { color: var(--dd-teal); }
.dd-type-badge { font-size: .7rem; font-weight: 800; padding: .14rem .42rem; border-radius: 12px; background: var(--dd-teal-dim); color: var(--dd-teal); }
.dd-ctx-sep { width: 1px; height: 18px; background: var(--dd-border); margin: 0 .1rem; }
.dd-ctx-range { font-size: .67rem; color: var(--dd-text-3); display: flex; align-items: center; gap: .22rem; }
.dd-ctx-range strong { color: var(--dd-text-2); font-weight: 700; }

/* ── KPI strip ────────────────────────────────────────── */
.dd-kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: .42rem; margin-bottom: .65rem; }
@media(max-width:700px) { .dd-kpis { grid-template-columns: repeat(2,1fr); } }
@media(max-width:440px) { .dd-kpis { grid-template-columns: 1fr; } }

.dd-kpi { background: var(--dd-surface); border: 1px solid var(--dd-border); border-radius: .55rem; padding: .5rem .65rem; position: relative; overflow: hidden; box-shadow: var(--dd-shadow); }
.dd-kpi::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 3.5px; border-radius: 2px 0 0 2px; }
.dd-k-sales::before   { background: linear-gradient(180deg,var(--dd-teal),#0369a1); }
.dd-k-premium::before { background: linear-gradient(180deg,var(--dd-green),#16a34a); }
.dd-k-revenue::before { background: linear-gradient(180deg,var(--dd-indigo),#4338ca); }

.dd-kpi-icon { position: absolute; right: .55rem; top: .5rem; font-size: 1.2rem; opacity: .06; }
.dd-kpi-lbl { font-size: .57rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; margin-bottom: .15rem; }
.dd-k-sales .dd-kpi-lbl   { color: #0369a1; }
.dd-k-premium .dd-kpi-lbl { color: #16a34a; }
.dd-k-revenue .dd-kpi-lbl { color: #4338ca; }
.dd-kpi-val { font-size: 1.15rem; font-weight: 900; color: var(--dd-text-1); line-height: 1; font-variant-numeric: tabular-nums; letter-spacing: -.01em; }
.dd-kpi-sub { font-size: .58rem; color: var(--dd-text-4); margin-top: .12rem; }

/* ── Table card ───────────────────────────────────────── */
.dd-card { background: var(--dd-surface); border: 1px solid var(--dd-border); border-radius: .55rem; overflow: hidden; box-shadow: var(--dd-shadow); }
.dd-card-head { display: flex; align-items: center; justify-content: space-between; padding: .44rem .7rem; border-bottom: 1px solid var(--dd-border); background: rgba(248,250,252,.5); }
.dd-card-head h6 { margin: 0; font-size: .76rem; font-weight: 800; color: var(--dd-text-1); display: flex; align-items: center; gap: .28rem; }
.dd-card-head h6 i { color: var(--dd-teal); font-size: .85rem; }
.dd-card-meta { font-size: .62rem; color: var(--dd-text-4); }

/* ── Table ────────────────────────────────────────────── */
.dd-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .71rem; }
.dd-tbl thead th { padding: .36rem .55rem; font-size: .58rem; font-weight: 800; text-transform: uppercase; letter-spacing: .55px; color: var(--dd-text-4); background: rgba(248,250,252,.95); border-bottom: 2px solid var(--dd-border); white-space: nowrap; position: sticky; top: 0; z-index: 2; }
.dd-tbl thead th.tr { text-align: right; }
.dd-tbl thead th.th-rev { color: var(--dd-indigo) !important; }
.dd-tbl tbody td { padding: .38rem .55rem; border-bottom: 1px solid rgba(0,0,0,.025); vertical-align: middle; }
.dd-tbl tbody tr:last-child td { border-bottom: none; }
.dd-tbl tfoot td { padding: .42rem .55rem; border-top: 2px solid rgba(0,0,0,.08); font-weight: 800; background: rgba(8,145,178,.04); }
.dd-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.4); }
.dd-tbl tbody tr:hover td { background: rgba(8,145,178,.04) !important; }

/* ── Cells ────────────────────────────────────────────── */
.dd-name { font-weight: 700; color: var(--dd-text-1); font-size: .72rem; }
.dd-sub  { font-size: .6rem; color: var(--dd-text-4); margin-top: .05rem; }
.td-r { text-align: right; font-variant-numeric: tabular-nums; }

.dd-chip { display: inline-flex; align-items: center; gap: .16rem; font-size: .63rem; font-weight: 700; padding: .08rem .3rem; border-radius: 14px; }
.dd-chip-green  { background: var(--dd-green-dim); color: #15803d; }
.dd-chip-indigo { background: var(--dd-indigo-dim); color: var(--dd-indigo); }
.dd-chip-grey   { background: rgba(100,116,139,.07); color: var(--dd-text-4); font-style: italic; }
.dd-chip-amber  { background: rgba(245,158,11,.1); color: #92400e; }

.dd-policy { font-size: .6rem; font-weight: 700; padding: .05rem .28rem; border-radius: 8px; border: 1px solid var(--dd-border); color: var(--dd-teal); white-space: nowrap; background: var(--dd-teal-dim); }
.dd-idx { color: var(--dd-text-4); font-size: .62rem; text-align: center; width: 26px; }

/* ── Empty ────────────────────────────────────────────── */
.dd-empty { text-align: center; padding: 3rem 1rem; color: var(--dd-text-4); }
.dd-empty i { font-size: 2.5rem; display: block; margin-bottom: .5rem; opacity: .15; }
.dd-empty strong { display: block; font-size: .82rem; margin-bottom: .25rem; color: var(--dd-text-3); }

/* ── Footnote ─────────────────────────────────────────── */
.dd-foot { font-size: .59rem; color: var(--dd-text-4); text-align: center; margin-top: .5rem; display: flex; align-items: center; justify-content: center; gap: .22rem; flex-wrap: wrap; }
.dd-foot code { font-size: .58rem; background: rgba(0,0,0,.05); padding: .02rem .2rem; border-radius: 3px; color: var(--dd-text-3); }

/* ── Dark mode ────────────────────────────────────────── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) {
    --dd-surface:  rgba(15,23,42,.55);
    --dd-border:   rgba(255,255,255,.07);
    --dd-shadow:   0 1px 4px rgba(0,0,0,.25), 0 0 0 1px rgba(255,255,255,.04);
    --dd-text-1:   #f1f5f9;
    --dd-text-2:   #cbd5e1;
    --dd-text-3:   #94a3b8;
    --dd-text-4:   #64748b;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-card-head { background: rgba(15,23,42,.4); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-tbl thead th { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.06); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-tbl tfoot td { background: rgba(8,145,178,.06); border-color: rgba(255,255,255,.08); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.018); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-chip-green  { background: rgba(34,197,94,.13); color: #4ade80; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-chip-indigo { background: rgba(99,102,241,.15); color: #a5b4fc; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .dd-policy { background: rgba(8,145,178,.15); color: #22d3ee; }
</style>
@endsection

@section('content')
@php
$backParams     = array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'team' => $team ?? null]);
$backUrl        = route('settings.reports.policy-type-report', $backParams);
$avgPremPerSale = $totalSales > 0 ? $totalPremium / $totalSales : 0;
$avgRevPerSale  = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

$typeColors = [
    'Level'    => '#16a34a',
    'Graded'   => '#ea580c',
    'G.I'      => '#0891b2',
    'Modified' => '#7c3aed',
    'Unknown'  => '#94a3b8',
];
$typeColor = $typeColors[$policyType] ?? '#0891b2';
@endphp

{{-- ── Breadcrumb Header ──────────────────────────────── --}}
<div class="dd-hdr">
    <div class="dd-hdr-left">
        <a href="{{ $backUrl }}" class="dd-back"><i class="bx bx-arrow-back"></i> Back</a>
        <div class="dd-breadcrumb">
            <a href="{{ route('settings.reports.hub') }}">Reports</a>
            <span class="dd-bc-sep">/</span>
            <a href="{{ $backUrl }}">Policy Type Report</a>
            <span class="dd-bc-sep">/</span>
            <span class="dd-bc-current">{{ $policyType }}</span>
        </div>
    </div>
</div>

{{-- ── Context Strip ──────────────────────────────────── --}}
<div class="dd-ctx">
    <div class="dd-ctx-type">
        <i class="bx bx-category"></i>
        <span>Policy Type</span>
        <span class="dd-type-badge" style="background:{{ $typeColor }}20;color:{{ $typeColor }}">{{ $policyType }}</span>
    </div>
    @if(!empty($carrierLabel))
    <div class="dd-ctx-sep"></div>
    <div class="dd-ctx-type" style="font-size:.78rem">
        <i class="bx bx-building" style="color:var(--dd-teal)"></i>
        <span style="font-size:.67rem;color:var(--dd-text-3)">Carrier</span>
        <span class="dd-type-badge" style="background:var(--dd-teal-dim);color:var(--dd-teal)">{{ $carrierLabel }}</span>
    </div>
    @endif
    @if(!empty($partnerLabel))
    <div class="dd-ctx-sep"></div>
    <div class="dd-ctx-type" style="font-size:.78rem">
        <i class="bx bx-user-pin" style="color:var(--dd-gold-dark)"></i>
        <span style="font-size:.67rem;color:var(--dd-text-3)">Partner</span>
        <span class="dd-type-badge" style="background:var(--dd-gold-dim);color:var(--dd-gold-dark)">{{ $partnerLabel }}</span>
    </div>
    @endif
    <div class="dd-ctx-sep"></div>
    <div class="dd-ctx-range">
        <i class="bx bx-calendar" style="color:var(--dd-teal);font-size:.82rem"></i>
        <strong>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : '—' }}</strong>
        <span>→</span>
        <strong>{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : '—' }}</strong>
    </div>
    <div class="dd-ctx-sep"></div>
    <span style="font-size:.65rem;color:var(--dd-text-3)">{{ $totalSales }} sale{{ $totalSales !== 1 ? 's' : '' }} in range</span>
</div>

{{-- ── KPI Strip ──────────────────────────────────────── --}}
<div class="dd-kpis">
    <div class="dd-kpi dd-k-sales">
        <i class="bx bx-bar-chart dd-kpi-icon"></i>
        <div class="dd-kpi-lbl">Total Sales</div>
        <div class="dd-kpi-val">{{ number_format($totalSales) }}</div>
        <div class="dd-kpi-sub">{{ $policyType }} policy</div>
    </div>
    <div class="dd-kpi dd-k-premium">
        <i class="bx bx-dollar-circle dd-kpi-icon"></i>
        <div class="dd-kpi-lbl">Total Premium</div>
        <div class="dd-kpi-val">${{ number_format($totalPremium, 2) }}</div>
        <div class="dd-kpi-sub">Avg ${{ number_format($avgPremPerSale, 2) }} / sale</div>
    </div>
    <div class="dd-kpi dd-k-revenue">
        <i class="bx bx-trending-up dd-kpi-icon"></i>
        <div class="dd-kpi-lbl">Est. Revenue</div>
        <div class="dd-kpi-val">${{ number_format($totalRevenue, 2) }}</div>
        <div class="dd-kpi-sub">Avg ${{ number_format($avgRevPerSale, 2) }} / sale</div>
    </div>
</div>

{{-- ── Sales Table ─────────────────────────────────────── --}}
<div class="dd-card">
    <div class="dd-card-head">
        <h6>
            <i class="bx bx-list-ul"></i>
            Individual Sales — <span style="color:{{ $typeColor }}">{{ $policyType }}</span>
        </h6>
        <span class="dd-card-meta">{{ $totalSales }} record{{ $totalSales !== 1 ? 's' : '' }}</span>
    </div>

    @if($leads->isEmpty())
    <div class="dd-empty">
        <i class="bx bx-ghost"></i>
        <strong>No sales found</strong>
        <span>No {{ $policyType }} sales for this date range.</span>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="dd-tbl">
            <thead>
                <tr>
                    <th style="width:26px">#</th>
                    <th style="min-width:145px">Client</th>
                    <th style="min-width:40px">Team</th>
                    <th style="min-width:120px">Carrier</th>
                    <th style="min-width:110px">Partner</th>
                    <th style="min-width:45px">State</th>
                    <th style="min-width:105px">Policy #</th>
                    <th class="tr" style="min-width:110px">Monthly Premium</th>
                    <th class="tr th-rev" style="min-width:110px">
                        <span style="display:inline-flex;align-items:center;gap:.18rem">
                            <i class="bx bx-trending-up"></i> Est. Revenue
                        </span>
                    </th>
                    <th style="min-width:95px">Closer</th>
                    <th style="min-width:82px">Sale Date</th>
                    <th style="min-width:90px">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $i => $lead)
                @php
                    $hasRevenue = $lead->eff_revenue > 0;
                @endphp
                <tr>
                    <td class="dd-idx">{{ $i + 1 }}</td>
                    {{-- Client --}}
                    <td>
                        <div class="dd-name">{{ $lead->cn_name ?: '—' }}</div>
                        <div class="dd-sub">#{{ $lead->id }}</div>
                    </td>
                    {{-- Team --}}
                    <td>
                        @if($lead->team === 'peregrine')
                            <span class="badge bg-purple" title="Peregrine" style="font-size:.58rem;padding:.1rem .35rem">P</span>
                        @elseif($lead->team === 'ravens')
                            <span class="badge bg-dark" title="Ravens" style="font-size:.58rem;padding:.1rem .35rem">R</span>
                        @else
                            <span style="color:var(--dd-text-4,#94a3b8);font-size:.65rem">—</span>
                        @endif
                    </td>
                    {{-- Carrier --}}
                    <td style="font-size:.7rem;color:var(--dd-text-2);font-weight:600">
                        {{ $lead->carrier_name ?: '—' }}
                    </td>
                    {{-- Partner --}}
                    <td>
                        @if($lead->assigned_partner)
                            <span style="font-size:.65rem;font-weight:700;color:var(--dd-text-2)">{{ $lead->assigned_partner }}</span>
                        @else
                            <span style="font-size:.65rem;color:var(--dd-text-4)">—</span>
                        @endif
                    </td>
                    {{-- State --}}
                    <td style="font-size:.7rem;font-weight:700;color:var(--dd-text-2)">{{ $lead->state ?: '—' }}</td>
                    {{-- Policy number --}}
                    <td style="font-size:.68rem;color:var(--dd-text-2)">{{ $lead->policy_number ?: '—' }}</td>
                    {{-- Monthly premium --}}
                    <td class="td-r">
                        <span class="dd-chip dd-chip-green">
                            <i class="bx bx-dollar" style="font-size:.68rem"></i>{{ number_format($lead->monthly_premium, 2) }}
                        </span>
                    </td>
                    {{-- Est. revenue --}}
                    <td class="td-r">
                        @if($hasRevenue)
                            <span class="dd-chip dd-chip-indigo">
                                <i class="bx bx-trending-up" style="font-size:.68rem"></i>{{ number_format($lead->eff_revenue, 2) }}
                            </span>
                        @else
                            <span class="dd-chip dd-chip-grey">— no rate</span>
                        @endif
                    </td>
                    {{-- Closer --}}
                    <td style="font-size:.68rem;color:var(--dd-text-2)">{{ $lead->closer_name ?: '—' }}</td>
                    {{-- Sale date --}}
                    <td style="font-size:.67rem;color:var(--dd-text-3)">
                        {{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}
                    </td>
                    {{-- Status --}}
                    <td>
                        @php
                            $st = $lead->issuance_status;
                            $stClass = match(strtolower($st ?? '')) {
                                'issued' => 'dd-chip-green',
                                'paid'   => 'dd-chip-green',
                                default  => 'dd-chip-grey',
                            };
                        @endphp
                        <span class="dd-chip {{ $stClass }}" style="font-size:.59rem;text-transform:capitalize">
                            {{ $st ?: 'pending' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6"></td>
                    <td style="font-size:.68rem;font-weight:800">
                        <span style="display:inline-flex;align-items:center;gap:.2rem">
                            <i class="bx bx-sum" style="color:var(--dd-teal)"></i> TOTAL
                        </span>
                    </td>
                    <td class="td-r">
                        <span class="dd-chip dd-chip-green">
                            <i class="bx bx-dollar" style="font-size:.68rem"></i>{{ number_format($totalPremium, 2) }}
                        </span>
                    </td>
                    <td class="td-r">
                        @if($totalRevenue > 0)
                        <span class="dd-chip dd-chip-indigo">
                            <i class="bx bx-trending-up" style="font-size:.68rem"></i>{{ number_format($totalRevenue, 2) }}
                        </span>
                        @else
                        <span class="dd-chip dd-chip-grey">— no rate</span>
                        @endif
                    </td>
                    <td colspan="3" style="font-size:.62rem;color:var(--dd-text-4)">
                        avg ${{ number_format($avgRevPerSale, 2) }} rev / sale
                        &nbsp;·&nbsp; avg ${{ number_format($avgPremPerSale, 2) }} prem / sale
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

<div class="dd-foot">
    <i class="bx bx-info-circle"></i>
    Revenue = <code>monthly_premium × 9 × settlement%</code>. "No rate" = no commission % configured.
</div>
@endsection
