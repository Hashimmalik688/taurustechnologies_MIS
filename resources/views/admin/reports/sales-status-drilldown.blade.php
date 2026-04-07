@extends('layouts.master')

@section('title')
    Sales Status — {{ $carrierLabel }} · {{ $stageLabel }}
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ════════════════════════════════════════════════════════
   SALES STATUS DRILLDOWN — Design System
   ════════════════════════════════════════════════════════ */
:root {
    --ssd-gold:       #d4af37;
    --ssd-gold-dim:   rgba(212,175,55,.12);
    --ssd-gold-dark:  #92760d;
    --ssd-blue:       #3b82f6;
    --ssd-blue-dim:   rgba(59,130,246,.11);
    --ssd-green:      #22c55e;
    --ssd-green-dim:  rgba(34,197,94,.11);
    --ssd-rose:       #f43f5e;
    --ssd-rose-dim:   rgba(244,63,94,.09);
    --ssd-teal:       #0ea5a0;
    --ssd-teal-dim:   rgba(14,165,160,.11);
    --ssd-orange:     #f97316;
    --ssd-orange-dim: rgba(249,115,22,.11);
    --ssd-amber:      #f59e0b;
    --ssd-amber-dim:  rgba(245,158,11,.10);
    --ssd-slate:      #64748b;
    --ssd-slate-dim:  rgba(100,116,139,.10);
    --ssd-indigo:     #6366f1;
    --ssd-indigo-dim: rgba(99,102,241,.12);
    --ssd-surface:    var(--bs-card-bg, #ffffff);
    --ssd-border:     rgba(0,0,0,.07);
    --ssd-shadow:     0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --ssd-text-1:     var(--bs-surface-900, #0f172a);
    --ssd-text-2:     var(--bs-surface-700, #374151);
    --ssd-text-3:     var(--bs-surface-500, #64748b);
    --ssd-text-4:     var(--bs-surface-400, #94a3b8);
}

/* ── Header ───────────────────────────────────────────── */
.ssd-hdr {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .6rem; flex-wrap: wrap; gap: .35rem;
}
.ssd-hdr-left { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.ssd-back {
    font-size: .7rem; font-weight: 700; padding: .28rem .6rem; border-radius: 20px;
    border: 1.5px solid var(--ssd-border); background: transparent;
    color: var(--ssd-text-3); text-decoration: none;
    display: inline-flex; align-items: center; gap: .22rem; transition: all .15s; flex-shrink: 0;
}
.ssd-back:hover { border-color: var(--ssd-blue); color: #1d4ed8; }
.ssd-breadcrumb {
    font-size: .67rem; color: var(--ssd-text-4); display: flex; align-items: center; gap: .28rem;
}
.ssd-breadcrumb a { color: var(--ssd-text-3); text-decoration: none; }
.ssd-breadcrumb a:hover { color: #1d4ed8; }
.ssd-bc-sep { opacity: .4; }
.ssd-bc-current { color: var(--ssd-text-2); font-weight: 700; }

/* ── Context strip ────────────────────────────────────── */
.ssd-ctx {
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
    background: var(--ssd-surface); border: 1px solid var(--ssd-border);
    border-radius: .55rem; padding: .45rem .7rem; margin-bottom: .6rem;
    box-shadow: var(--ssd-shadow);
}
.ssd-ctx-title {
    font-size: .88rem; font-weight: 800; color: var(--ssd-text-1);
    display: flex; align-items: center; gap: .35rem;
}
.ssd-ctx-title i { color: var(--ssd-blue); }
.ssd-stage-badge {
    font-size: .66rem; font-weight: 800; padding: .12rem .38rem;
    border-radius: 12px; white-space: nowrap;
}
.ssd-ctx-sep { width: 1px; height: 18px; background: var(--ssd-border); margin: 0 .1rem; }
.ssd-ctx-range {
    font-size: .67rem; color: var(--ssd-text-3); display: flex; align-items: center; gap: .22rem;
}
.ssd-ctx-range strong { color: var(--ssd-text-2); font-weight: 700; }

/* Stage badge colors */
.ssd-badge-total   { background: var(--ssd-blue-dim);   color: #1d4ed8; }
.ssd-badge-pc      { background: var(--ssd-gold-dim);   color: var(--ssd-gold-dark); }
.ssd-badge-sub     { background: var(--ssd-indigo-dim); color: var(--ssd-indigo); }
.ssd-badge-issued  { background: var(--ssd-green-dim);  color: #16a34a; }
.ssd-badge-nissued { background: var(--ssd-rose-dim);   color: #e11d48; }
.ssd-badge-paid    { background: var(--ssd-teal-dim);   color: #0d9488; }
.ssd-badge-npaid   { background: var(--ssd-orange-dim); color: #c2410c; }
.ssd-badge-died    { background: var(--ssd-slate-dim);  color: #475569; }
.ssd-badge-declined { background: rgba(124,58,237,.1);  color: #7c3aed; }

/* ── KPI strip ────────────────────────────────────────── */
.ssd-kpis {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: .42rem; margin-bottom: .65rem;
}
@media(max-width:640px) { .ssd-kpis { grid-template-columns: 1fr 1fr; } }

.ssd-kpi {
    background: var(--ssd-surface); border: 1px solid var(--ssd-border);
    border-radius: .55rem; padding: .5rem .65rem;
    position: relative; overflow: hidden; box-shadow: var(--ssd-shadow);
}
.ssd-kpi::before {
    content: ''; position: absolute; inset: 0 auto 0 0;
    width: 3.5px; border-radius: 2px 0 0 2px;
}
.ssd-k-total::before   { background: linear-gradient(180deg, var(--ssd-blue),  #1d4ed8); }
.ssd-k-premium::before { background: linear-gradient(180deg, var(--ssd-green), #16a34a); }
.ssd-k-partner::before { background: linear-gradient(180deg, var(--ssd-gold),  #b8941f); }

.ssd-kpi-icon { position: absolute; right: .55rem; top: .5rem; font-size: 1.2rem; opacity: .06; }
.ssd-kpi-lbl {
    font-size: .57rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: .15rem;
}
.ssd-k-total .ssd-kpi-lbl   { color: #1d4ed8; }
.ssd-k-premium .ssd-kpi-lbl { color: #16a34a; }
.ssd-k-partner .ssd-kpi-lbl { color: var(--ssd-gold-dark); }
.ssd-kpi-val {
    font-size: 1.15rem; font-weight: 900; color: var(--ssd-text-1);
    line-height: 1; font-variant-numeric: tabular-nums; letter-spacing: -.01em;
}
.ssd-kpi-sub { font-size: .58rem; color: var(--ssd-text-4); margin-top: .12rem; }

/* ── Table card ───────────────────────────────────────── */
.ssd-card {
    background: var(--ssd-surface); border: 1px solid var(--ssd-border);
    border-radius: .55rem; overflow: hidden; box-shadow: var(--ssd-shadow);
}
.ssd-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: .44rem .7rem; border-bottom: 1px solid var(--ssd-border);
    background: rgba(248,250,252,.5);
}
.ssd-card-head h6 {
    margin: 0; font-size: .76rem; font-weight: 800; color: var(--ssd-text-1);
    display: flex; align-items: center; gap: .28rem;
}
.ssd-card-head h6 i { color: var(--ssd-blue); font-size: .85rem; }
.ssd-card-meta { font-size: .62rem; color: var(--ssd-text-4); }

/* ── Table ────────────────────────────────────────────── */
.ssd-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .71rem; }
.ssd-tbl thead th {
    padding: .36rem .55rem; font-size: .58rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .55px; color: var(--ssd-text-4);
    background: rgba(248,250,252,.95); border-bottom: 2px solid var(--ssd-border);
    white-space: nowrap; position: sticky; top: 0; z-index: 2;
}
.ssd-tbl thead th.tr { text-align: right; }
.ssd-tbl tbody td {
    padding: .38rem .55rem; border-bottom: 1px solid rgba(0,0,0,.025);
    vertical-align: middle;
}
.ssd-tbl tbody tr:last-child td { border-bottom: none; }
.ssd-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.4); }
.ssd-tbl tbody tr:hover td { background: rgba(59,130,246,.025) !important; }
.ssd-tbl tfoot td {
    padding: .42rem .55rem; border-top: 2px solid rgba(0,0,0,.08);
    font-weight: 800; background: rgba(59,130,246,.04);
}

/* ── Name cell ────────────────────────────────────────── */
.ssd-name { font-weight: 700; color: var(--ssd-text-1); font-size: .72rem; }
.ssd-sub  { font-size: .6rem; color: var(--ssd-text-4); margin-top: .04rem; }

/* ── Chips ────────────────────────────────────────────── */
.ssd-chip {
    display: inline-flex; align-items: center; gap: .16rem;
    font-size: .63rem; font-weight: 700; padding: .07rem .28rem; border-radius: 14px;
}
.ssd-chip-green  { background: var(--ssd-green-dim);  color: #15803d; }
.ssd-chip-teal   { background: var(--ssd-teal-dim);   color: #0d9488; }
.ssd-chip-rose   { background: var(--ssd-rose-dim);   color: #e11d48; }
.ssd-chip-amber  { background: var(--ssd-amber-dim);  color: #92400e; }
.ssd-chip-blue   { background: var(--ssd-blue-dim);   color: #1d4ed8; }
.ssd-chip-orange { background: var(--ssd-orange-dim); color: #c2410c; }
.ssd-chip-grey   { background: var(--ssd-slate-dim);  color: var(--ssd-text-4); font-style: italic; }

/* ── Status badge ─────────────────────────────────────── */
.ssd-status {
    font-size: .6rem; font-weight: 700; padding: .05rem .28rem;
    border-radius: 8px; white-space: nowrap;
}
.ssd-s-issued  { background: var(--ssd-green-dim);  color: #15803d; }
.ssd-s-pending { background: var(--ssd-amber-dim);  color: #92400e; }
.ssd-s-cb      { background: var(--ssd-rose-dim);   color: #e11d48; }
.ssd-s-default { background: var(--ssd-slate-dim);  color: var(--ssd-text-3); }

/* ── #index ───────────────────────────────────────────── */
.ssd-idx { color: var(--ssd-text-4); font-size: .62rem; text-align: center; width: 26px; }
.td-r { text-align: right; font-variant-numeric: tabular-nums; }

/* ── Empty ────────────────────────────────────────────── */
.ssd-empty { text-align: center; padding: 3rem 1rem; color: var(--ssd-text-4); }
.ssd-empty i { font-size: 2.5rem; display: block; margin-bottom: .5rem; opacity: .15; }
.ssd-empty strong { display: block; font-size: .82rem; margin-bottom: .25rem; color: var(--ssd-text-3); }

/* ── Footnote  ────────────────────────────────────────── */
.ssd-foot {
    font-size: .59rem; color: var(--ssd-text-4); text-align: center; margin-top: .5rem;
    display: flex; align-items: center; justify-content: center; gap: .22rem; flex-wrap: wrap;
}
.ssd-foot code { font-size: .58rem; background: rgba(0,0,0,.05); padding: .02rem .2rem; border-radius: 3px; color: var(--ssd-text-3); }

/* ── Dark mode ────────────────────────────────────────── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) {
    --ssd-surface: rgba(15,23,42,.55);
    --ssd-border:  rgba(255,255,255,.07);
    --ssd-shadow:  0 1px 4px rgba(0,0,0,.25), 0 0 0 1px rgba(255,255,255,.04);
    --ssd-text-1:  #f1f5f9;
    --ssd-text-2:  #cbd5e1;
    --ssd-text-3:  #94a3b8;
    --ssd-text-4:  #64748b;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .ssd-card-head { background: rgba(15,23,42,.4); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .ssd-tbl thead th,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .ssd-tbl tfoot td { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.06); }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .ssd-foot code { background: rgba(255,255,255,.07); color: #94a3b8; }
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],
    [data-theme="ocean-blue"],[data-theme="royal-purple"],
    [data-theme="rose-gold"],[data-theme="copper-steel"]) .ssd-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.018); }
</style>
@endsection

@section('content')
@php
$backParams = [
    'date_from'    => $dateFrom,
    'date_to'      => $dateTo,
    'date_field'   => $dateField,
    'carrier_name' => $carrierName ?? null,
    'carrier_id'   => $carrierId ?? null,
    'partner_id'   => $partnerId ?? null,
];
$backUrl = route('settings.reports.sales-status', $backParams);

$stageBadgeClass = match($stage) {
    'total_sales'      => 'ssd-badge-total',
    'pending_contract' => 'ssd-badge-pc',
    'submitted'        => 'ssd-badge-sub',
    'issued'           => 'ssd-badge-issued',
    'not_issued'       => 'ssd-badge-nissued',
    'paid'             => 'ssd-badge-paid',
    'not_paid'         => 'ssd-badge-npaid',
    'policy_died'      => 'ssd-badge-died',
    'declined'         => 'ssd-badge-declined',
    default            => 'ssd-badge-total',
};

$uniquePartners = $leads->whereNotNull('assigned_partner')->pluck('assigned_partner')->unique()->count();
@endphp

{{-- ── Header ────────────────────────────────────────────── --}}
<div class="ssd-hdr">
    <div class="ssd-hdr-left">
        <a href="{{ $backUrl }}" class="ssd-back"><i class="bx bx-arrow-back"></i> Back</a>
        <div class="ssd-breadcrumb">
            <a href="{{ route('settings.reports.hub') }}">Reports</a>
            <span class="ssd-bc-sep">/</span>
            <a href="{{ $backUrl }}">Sales Status</a>
            <span class="ssd-bc-sep">/</span>
            <span class="ssd-bc-current">{{ $carrierLabel }} · {{ $stageLabel }}</span>
        </div>
    </div>
</div>

{{-- ── Context Strip ─────────────────────────────────────── --}}
<div class="ssd-ctx">
    <div class="ssd-ctx-title">
        <i class="bx bx-building"></i>
        {{ $carrierLabel }}
    </div>
    @if($partnerLabel)
        <span class="ssd-stage-badge" style="background:rgba(212,175,55,.12);color:#92760d">
            {{ $partnerLabel }}
        </span>
    @endif
    <span class="ssd-stage-badge {{ $stageBadgeClass }}">{{ $stageLabel }}</span>
    <div class="ssd-ctx-sep"></div>
    <div class="ssd-ctx-range">
        <i class="bx bx-calendar" style="color:var(--ssd-blue);font-size:.82rem"></i>
        <strong>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : '—' }}</strong>
        <span>→</span>
        <strong>{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : '—' }}</strong>
        <span style="opacity:.5">· {{ $dateField === 'paid_at' ? 'paid date' : 'sale date' }}</span>
    </div>
    <div class="ssd-ctx-sep"></div>
    <span style="font-size:.65rem;color:var(--ssd-text-3)">
        {{ $totalSales }} lead{{ $totalSales !== 1 ? 's' : '' }}
    </span>
</div>

{{-- ── KPI Strip ─────────────────────────────────────────── --}}
<div class="ssd-kpis">
    <div class="ssd-kpi ssd-k-total">
        <i class="bx bx-list-ul ssd-kpi-icon"></i>
        <div class="ssd-kpi-lbl">Total Leads</div>
        <div class="ssd-kpi-val">{{ number_format($totalSales) }}</div>
        <div class="ssd-kpi-sub">In "{{ $stageLabel }}"</div>
    </div>
    <div class="ssd-kpi ssd-k-premium">
        <i class="bx bx-dollar-circle ssd-kpi-icon"></i>
        <div class="ssd-kpi-lbl">Total Premium</div>
        <div class="ssd-kpi-val">${{ number_format($totalPremium, 2) }}</div>
        <div class="ssd-kpi-sub">Monthly premium</div>
    </div>
    <div class="ssd-kpi ssd-k-partner">
        <i class="bx bx-group ssd-kpi-icon"></i>
        <div class="ssd-kpi-lbl">Partners</div>
        <div class="ssd-kpi-val">{{ $uniquePartners }}</div>
        <div class="ssd-kpi-sub">Unique partners involved</div>
    </div>
</div>

{{-- ── Leads Table ────────────────────────────────────────── --}}
<div class="ssd-card">
    <div class="ssd-card-head">
        <h6>
            <i class="bx bx-user-pin"></i>
            Individual Leads — {{ $carrierLabel }}
        </h6>
        <span class="ssd-card-meta">{{ $totalSales }} result{{ $totalSales !== 1 ? 's' : '' }}</span>
    </div>

    @if($leads->isEmpty())
    <div class="ssd-empty">
        <i class="bx bx-search-alt"></i>
        <strong>No leads found</strong>
        <span>No leads match this carrier + stage + date range.</span>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="ssd-tbl">
            <thead>
                <tr>
                    <th style="width:26px;text-align:center">#</th>
                    <th style="min-width:140px">Client Name</th>
                    <th style="min-width:105px">Partner</th>
                    <th class="tr" style="min-width:90px;text-align:right">Sale Date</th>
                    <th class="tr" style="min-width:90px;text-align:right">Paid Date</th>
                    <th class="tr" style="min-width:90px;text-align:right">Monthly Premium</th>
                    <th style="min-width:110px">Policy #</th>
                    <th style="min-width:90px">Issuance</th>
                    <th style="min-width:120px">Not Issued Reason</th>
                    <th style="min-width:100px">Closer</th>
                    <th style="min-width:80px">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $i => $lead)
                @php
                    /* Issuance chip */
                    $issClass = match($lead->issuance_status) {
                        'Issued'  => 'ssd-chip-green',
                        'Pending' => 'ssd-chip-amber',
                        default   => 'ssd-chip-grey',
                    };

                    /* Lead status chip */
                    $sClass = match($lead->status ?? '') {
                        'chargeback'   => 'ssd-s-cb',
                        'sale','active' => 'ssd-s-issued',
                        'pending'      => 'ssd-s-pending',
                        default        => 'ssd-s-default',
                    };

                    $niReason = $lead->not_issued_disposition
                        ? ucwords(str_replace('_', ' ', $lead->not_issued_disposition))
                        : null;
                @endphp
                <tr>
                    <td class="ssd-idx" style="text-align:center">{{ $i + 1 }}</td>
                    <td>
                        <a href="{{ route('leads.show', $lead->id) }}" style="text-decoration:none">
                            <div class="ssd-name">{{ $lead->cn_name ?? '—' }}</div>
                        </a>
                    </td>
                    <td>
                        @if($lead->assigned_partner)
                            <span class="ssd-chip ssd-chip-blue" style="font-size:.6rem">
                                {{ $lead->assigned_partner }}
                            </span>
                        @else
                            <span style="color:var(--ssd-text-4);font-size:.65rem">—</span>
                        @endif
                    </td>
                    <td class="td-r" style="font-size:.68rem;color:var(--ssd-text-2)">
                        {{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}
                    </td>
                    <td class="td-r" style="font-size:.68rem;color:var(--ssd-text-2)">
                        @if($lead->paid_at)
                            <span class="ssd-chip ssd-chip-teal" style="font-size:.6rem">
                                {{ \Carbon\Carbon::parse($lead->paid_at)->format('M d, Y') }}
                            </span>
                        @else
                            <span style="color:var(--ssd-text-4)">—</span>
                        @endif
                    </td>
                    <td class="td-r">
                        @if($lead->monthly_premium)
                            <span class="ssd-chip ssd-chip-green">
                                <i class="bx bx-dollar" style="font-size:.65rem"></i>{{ number_format($lead->monthly_premium, 2) }}
                            </span>
                        @else
                            <span style="color:var(--ssd-text-4)">—</span>
                        @endif
                    </td>
                    <td style="font-size:.67rem;color:var(--ssd-text-3);font-family:monospace">
                        {{ $lead->policy_number ?: '—' }}
                    </td>
                    <td>
                        <span class="ssd-chip {{ $issClass }}">
                            {{ $lead->issuance_status ?: '—' }}
                        </span>
                    </td>
                    <td style="font-size:.65rem;color:var(--ssd-text-3)">
                        @if($niReason)
                            <span class="ssd-chip ssd-chip-rose" style="font-size:.6rem;white-space:normal;line-height:1.3">
                                {{ $niReason }}
                            </span>
                        @else
                            <span style="color:var(--ssd-text-4)">—</span>
                        @endif
                    </td>
                    <td style="font-size:.66rem;color:var(--ssd-text-2)">
                        {{ $lead->closer_name ?: '—' }}
                    </td>
                    <td>
                        <span class="ssd-status {{ $sClass }}">
                            {{ ucfirst($lead->status ?? '—') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="font-size:.7rem">
                        <i class="bx bx-sum" style="color:var(--ssd-blue)"></i> TOTAL
                    </td>
                    <td class="td-r">
                        <span class="ssd-chip ssd-chip-green">
                            <i class="bx bx-dollar" style="font-size:.65rem"></i>{{ number_format($totalPremium, 2) }}
                        </span>
                    </td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

<div class="ssd-foot">
    <i class="bx bx-info-circle"></i>
    Stage: <strong>{{ $stageLabel }}</strong> · Carrier: <strong>{{ $carrierLabel }}</strong>
    · Date filter: <code>{{ $dateField === 'paid_at' ? 'paid_at' : 'sale_date' }}</code>
    · <a href="{{ $backUrl }}" style="color:var(--ssd-text-3);text-decoration:none">← Back to Sales Status Report</a>
</div>
@endsection
