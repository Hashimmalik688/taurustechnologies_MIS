@extends('layouts.master')

@section('title')
    Manager Submission — {{ $managerName ?? 'All Managers' }}
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
:root {
    --dd-gold:      #d4af37;
    --dd-gold-dim:  rgba(212,175,55,.12);
    --dd-green:     #22c55e;
    --dd-green-dim: rgba(34,197,94,.11);
    --dd-red:       #ef4444;
    --dd-red-dim:   rgba(239,68,68,.11);
    --dd-indigo:    #6366f1;
    --dd-indigo-dim:rgba(99,102,241,.12);
    --dd-amber:     #f59e0b;
    --dd-amber-dim: rgba(245,158,11,.12);
    --dd-rose:      #f43f5e;
    --dd-surface:   var(--bs-card-bg, #ffffff);
    --dd-border:    rgba(0,0,0,.07);
    --dd-shadow:    0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --dd-text-1:    var(--bs-surface-900, #0f172a);
    --dd-text-2:    var(--bs-surface-700, #374151);
    --dd-text-3:    var(--bs-surface-500, #64748b);
    --dd-text-4:    var(--bs-surface-400, #94a3b8);
}

/* Header */
.dd-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:.6rem; flex-wrap:wrap; gap:.35rem; }
.dd-hdr-title { display:flex; align-items:center; gap:.45rem; flex-wrap:wrap; }
.dd-hdr-icon { width:28px; height:28px; border-radius:.4rem; flex-shrink:0; background:linear-gradient(135deg,var(--dd-gold),#b8941f); display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(212,175,55,.35); }
.dd-hdr-icon i { font-size:.95rem; color:#0f172a; }
.dd-hdr h5 { margin:0; font-size:.95rem; font-weight:800; color:var(--dd-text-1); }
.dd-hdr-sub { font-size:.67rem; color:var(--dd-text-3); font-weight:400; border-left:2px solid var(--dd-border); padding-left:.45rem; margin-left:.1rem; }
.dd-back { font-size:.7rem; font-weight:700; padding:.28rem .6rem; border-radius:20px; border:1.5px solid var(--dd-border); background:transparent; color:var(--dd-text-3); text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s; }
.dd-back:hover { border-color:var(--dd-gold); color:#92760d; }

/* Filter tabs */
.dd-tabs { display:flex; gap:.35rem; margin-bottom:.65rem; flex-wrap:wrap; }
.dd-tab { font-size:.68rem; font-weight:700; padding:.28rem .65rem; border-radius:20px; border:1.5px solid var(--dd-border); text-decoration:none; color:var(--dd-text-3); transition:all .15s; }
.dd-tab:hover { border-color:var(--dd-gold); color:#92760d; }
.dd-tab.active { background:linear-gradient(135deg,var(--dd-gold),#b8941f); color:#0f172a; border-color:transparent; }

/* KPI strip */
.dd-kpis { display:grid; grid-template-columns:repeat(5,1fr); gap:.45rem; margin-bottom:.7rem; }
@media(max-width:1000px) { .dd-kpis { grid-template-columns:repeat(3,1fr); } }
@media(max-width:600px)  { .dd-kpis { grid-template-columns:repeat(2,1fr); } }
@media(max-width:380px)  { .dd-kpis { grid-template-columns:1fr; } }
.dd-kpi { background:var(--dd-surface); border:1px solid var(--dd-border); border-radius:.55rem; padding:.55rem .75rem; position:relative; overflow:hidden; box-shadow:var(--dd-shadow); }
.dd-kpi::before { content:''; position:absolute; inset:0 auto 0 0; width:3.5px; border-radius:2px 0 0 2px; }
.dd-kpi-total::before    { background:linear-gradient(180deg,var(--dd-gold),#b8941f); }
.dd-kpi-contract::before { background:linear-gradient(180deg,var(--dd-green),#16a34a); }
.dd-kpi-declined::before { background:linear-gradient(180deg,var(--dd-red),#dc2626); }
.dd-kpi-premium::before  { background:linear-gradient(180deg,var(--dd-indigo),#4338ca); }
.dd-kpi-commission::before { background:linear-gradient(180deg,#f59e0b,#d97706); }
.dd-kpi-icon { font-size:1.2rem; margin-bottom:.18rem; display:block; }
.dd-kpi-lbl { font-size:.58rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:var(--dd-text-4); margin-bottom:.15rem; }
.dd-kpi-val { font-size:1.45rem; font-weight:900; color:var(--dd-text-1); line-height:1; }
.dd-kpi-sub { font-size:.6rem; color:var(--dd-text-3); margin-top:.18rem; }

/* Card */
.dd-card { background:var(--dd-surface); border:1px solid var(--dd-border); border-radius:.55rem; overflow:hidden; box-shadow:var(--dd-shadow); margin-bottom:1rem; }
.dd-card-head { display:flex; align-items:center; justify-content:space-between; padding:.55rem .75rem; border-bottom:1px solid var(--dd-border); }
.dd-card-head h6 { margin:0; font-size:.75rem; font-weight:800; color:var(--dd-text-1); display:flex; align-items:center; gap:.3rem; }
.dd-card-meta { font-size:.62rem; color:var(--dd-text-4); font-weight:600; }

/* Table */
.dd-tbl { width:100%; border-collapse:collapse; font-size:.72rem; }
.dd-tbl th { padding:.45rem .65rem; font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--dd-text-4); background:var(--bs-table-bg,#f8fafc); border-bottom:2px solid var(--dd-border); white-space:nowrap; }
.dd-tbl td { padding:.5rem .65rem; border-bottom:1px solid var(--dd-border); color:var(--dd-text-2); vertical-align:middle; }
.dd-tbl tbody tr:hover { background:rgba(212,175,55,.035); }
.dd-tbl tfoot td { padding:.5rem .65rem; font-weight:800; border-top:2px solid var(--dd-border); color:var(--dd-text-1); }
.td-r { text-align:right; }
.dd-idx { color:var(--dd-text-4); font-size:.65rem; }
.dd-name { font-weight:700; font-size:.75rem; color:var(--dd-text-1); }
.dd-sub { font-size:.62rem; color:var(--dd-text-4); margin-top:.06rem; }
.dd-policy { font-size:.62rem; background:var(--dd-gold-dim); color:#92760d; padding:.1rem .32rem; border-radius:4px; font-weight:700; letter-spacing:.3px; }

/* Chips */
.dd-chip { display:inline-flex; align-items:center; gap:.18rem; padding:.18rem .42rem; border-radius:20px; font-size:.65rem; font-weight:700; white-space:nowrap; }
.dd-chip-green  { background:var(--dd-green-dim); color:#16a34a; }
.dd-chip-red    { background:var(--dd-red-dim); color:#dc2626; }
.dd-chip-amber  { background:var(--dd-amber-dim); color:#d97706; }
.dd-chip-indigo { background:var(--dd-indigo-dim); color:#4338ca; }
.dd-chip-grey   { background:rgba(100,116,139,.09); color:var(--dd-text-3); }
.dd-chip-gold   { background:var(--dd-gold-dim); color:#92760d; }

/* Empty */
.dd-empty { padding:2.5rem; text-align:center; color:var(--dd-text-4); }
.dd-empty i { font-size:2rem; display:block; margin-bottom:.5rem; }

/* Footer note */
.dd-foot { font-size:.62rem; color:var(--dd-text-4); margin-top:-.3rem; }
</style>
@endsection

@section('content')

{{-- Header --}}
<div class="dd-hdr">
    <div class="dd-hdr-title">
        <div class="dd-hdr-icon"><i class="bx bx-user-check"></i></div>
        <div>
            <h5>{{ $managerName ?? 'All Managers' }}</h5>
            <span class="dd-hdr-sub">Manager Submission Drilldown
                @if($dateFrom && $dateTo)
                    &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} → {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                @endif
            </span>
        </div>
    </div>
    <a href="{{ route('settings.reports.manager-submission-report', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="dd-back">
        <i class="bx bx-arrow-back"></i> Back to Report
    </a>
</div>

{{-- Action filter tabs --}}
@php
    $baseParams = array_filter(['manager_id' => $managerId, 'date_from' => $dateFrom, 'date_to' => $dateTo]);
@endphp
<div class="dd-tabs">
    <a href="{{ route('settings.reports.manager-submission-report.drilldown', $baseParams) }}"
       class="dd-tab {{ !$actionFilter ? 'active' : '' }}">
        <i class="bx bx-list-ul"></i> All Actions ({{ $totalLeads }})
    </a>
    <a href="{{ route('settings.reports.manager-submission-report.drilldown', array_merge($baseParams, ['action' => 'pending_contract'])) }}"
       class="dd-tab {{ $actionFilter === 'pending_contract' ? 'active' : '' }}">
        <i class="bx bx-check-circle"></i> Pending Contract ({{ $totalPendingContract }})
    </a>
    <a href="{{ route('settings.reports.manager-submission-report.drilldown', array_merge($baseParams, ['action' => 'declined'])) }}"
       class="dd-tab {{ $actionFilter === 'declined' ? 'active' : '' }}">
        <i class="bx bx-x-circle"></i> Declined ({{ $totalDeclined }})
    </a>
</div>

{{-- KPI Strip --}}
<div class="dd-kpis">
    <div class="dd-kpi dd-kpi-total">
        <i class="bx bx-list-ul dd-kpi-icon" style="color:var(--dd-gold)"></i>
        <div class="dd-kpi-lbl">Total Records</div>
        <div class="dd-kpi-val">{{ number_format($totalLeads) }}</div>
        <div class="dd-kpi-sub">
            @if($actionFilter === 'pending_contract') Approved to Pending Contract
            @elseif($actionFilter === 'declined') Marked Declined
            @else Approved + Declined
            @endif
        </div>
    </div>
    <div class="dd-kpi dd-kpi-contract">
        <i class="bx bx-check-circle dd-kpi-icon" style="color:#16a34a"></i>
        <div class="dd-kpi-lbl">Pending Contract</div>
        <div class="dd-kpi-val" style="color:#16a34a">{{ number_format($totalPendingContract) }}</div>
        <div class="dd-kpi-sub">Approved to contract</div>
    </div>
    <div class="dd-kpi dd-kpi-declined">
        <i class="bx bx-x-circle dd-kpi-icon" style="color:#dc2626"></i>
        <div class="dd-kpi-lbl">Declined</div>
        <div class="dd-kpi-val" style="color:#dc2626">{{ number_format($totalDeclined) }}</div>
        <div class="dd-kpi-sub">Marked declined</div>
    </div>
    <div class="dd-kpi dd-kpi-premium">
        <i class="bx bx-dollar-circle dd-kpi-icon" style="color:var(--dd-indigo)"></i>
        <div class="dd-kpi-lbl">Total Premium</div>
        <div class="dd-kpi-val" style="color:var(--dd-indigo)">${{ number_format($totalPremium, 2) }}</div>
        <div class="dd-kpi-sub">Monthly premium</div>
    </div>
    <div class="dd-kpi dd-kpi-commission">
        <i class="bx bx-coin dd-kpi-icon" style="color:#d97706"></i>
        <div class="dd-kpi-lbl">Total Commission</div>
        <div class="dd-kpi-val" style="color:#d97706">${{ number_format($totalCommission, 2) }}</div>
        <div class="dd-kpi-sub">Calculated commission</div>
    </div>
</div>

{{-- Table --}}
<div class="dd-card">
    <div class="dd-card-head">
        <h6>
            <i class="bx bx-list-ul"></i>
            Individual Leads —
            @if($actionFilter === 'pending_contract') Pending Contract
            @elseif($actionFilter === 'declined') Declined
            @else All Actions
            @endif
            @if($managerName) · {{ $managerName }} @endif
        </h6>
        <span class="dd-card-meta">{{ $totalLeads }} record{{ $totalLeads !== 1 ? 's' : '' }}</span>
    </div>

    @if($leads->isEmpty())
    <div class="dd-empty">
        <i class="bx bx-ghost"></i>
        <strong>No leads found</strong>
        <span style="display:block;font-size:.72rem">No records match the selected filters.</span>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="dd-tbl">
            <thead>
                <tr>
                    <th style="width:26px">#</th>
                    <th style="min-width:150px">Client</th>
                    <th style="min-width:120px">Carrier</th>
                    <th style="min-width:80px">Policy Type</th>
                    <th style="min-width:100px">Policy #</th>
                    <th class="td-r" style="min-width:110px">Monthly Premium</th>
                    <th class="td-r" style="min-width:100px">Commission</th>
                    <th style="min-width:100px">Closer</th>
                    <th style="min-width:85px">Sale Date</th>
                    <th style="min-width:120px">Manager Action</th>
                    <th style="min-width:90px">Issuance Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $i => $lead)
                @php
                    $policyLabel = $lead->settlement_type ?: $lead->policy_type ?: '—';
                    $hasPendingContract = !is_null($lead->pending_contract_at);
                    $hasDeclined        = !is_null($lead->declined_at);
                @endphp
                <tr>
                    <td class="dd-idx">{{ $i + 1 }}</td>

                    {{-- Client --}}
                    <td>
                        <div class="dd-name">{{ $lead->cn_name ?: '—' }}</div>
                        <div class="dd-sub">ID #{{ $lead->id }}</div>
                    </td>

                    {{-- Carrier --}}
                    <td style="font-size:.68rem;color:var(--dd-text-2)">
                        {{ $lead->carrier_name ?: '—' }}
                        @if($lead->assigned_partner)
                            <div class="dd-sub">{{ $lead->assigned_partner }}</div>
                        @endif
                    </td>

                    {{-- Policy type --}}
                    <td>
                        <span class="dd-policy">{{ strtoupper($policyLabel) }}</span>
                    </td>

                    {{-- Policy number --}}
                    <td style="font-size:.68rem;color:var(--dd-text-2)">
                        {{ $lead->policy_number ?: '—' }}
                    </td>

                    {{-- Monthly premium --}}
                    <td class="td-r">
                        @if($lead->monthly_premium > 0)
                            <span class="dd-chip dd-chip-green">
                                <i class="bx bx-dollar" style="font-size:.68rem"></i>{{ number_format($lead->monthly_premium, 2) }}
                            </span>
                        @else
                            <span class="dd-chip dd-chip-grey">—</span>
                        @endif
                    </td>

                    {{-- Commission --}}
                    <td class="td-r">
                        @if($lead->eff_revenue > 0)
                            <span class="dd-chip dd-chip-amber">
                                <i class="bx bx-dollar" style="font-size:.68rem"></i>{{ number_format($lead->eff_revenue, 2) }}
                            </span>
                        @else
                            <span style="font-size:.65rem;color:var(--dd-text-4)">—</span>
                        @endif
                    </td>

                    {{-- Closer --}}
                    <td style="font-size:.68rem;color:var(--dd-text-2)">{{ $lead->closer_name ?: '—' }}</td>

                    {{-- Sale date --}}
                    <td style="font-size:.67rem;color:var(--dd-text-3)">
                        {{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}
                    </td>

                    {{-- Manager action --}}
                    <td>
                        @if($hasPendingContract && $hasDeclined)
                            {{-- Both set (edge case) --}}
                            <span class="dd-chip dd-chip-green" title="Pending Contract: {{ \Carbon\Carbon::parse($lead->pending_contract_at)->format('M d, Y') }}">
                                <i class="bx bx-check-circle"></i> Contract
                            </span>
                            <span class="dd-chip dd-chip-red" title="Declined: {{ \Carbon\Carbon::parse($lead->declined_at)->format('M d, Y') }}" style="margin-left:.2rem">
                                <i class="bx bx-x-circle"></i> Declined
                            </span>
                        @elseif($hasPendingContract)
                            <span class="dd-chip dd-chip-green" title="{{ \Carbon\Carbon::parse($lead->pending_contract_at)->format('M d, Y g:i A') }}">
                                <i class="bx bx-check-circle"></i> Pending Contract
                            </span>
                            <div class="dd-sub" style="margin-top:.22rem">{{ \Carbon\Carbon::parse($lead->pending_contract_at)->format('M d, Y') }}</div>
                        @elseif($hasDeclined)
                            <span class="dd-chip dd-chip-red" title="{{ \Carbon\Carbon::parse($lead->declined_at)->format('M d, Y g:i A') }}">
                                <i class="bx bx-x-circle"></i> Declined
                            </span>
                            <div class="dd-sub" style="margin-top:.22rem">{{ \Carbon\Carbon::parse($lead->declined_at)->format('M d, Y') }}</div>
                        @else
                            <span class="dd-chip dd-chip-grey">—</span>
                        @endif
                    </td>

                    {{-- Issuance status --}}
                    <td>
                        @php
                            $st = $lead->issuance_status;
                            $stClass = match(strtolower($st ?? '')) {
                                'issued'  => 'dd-chip-green',
                                'paid'    => 'dd-chip-green',
                                'pending' => 'dd-chip-amber',
                                default   => 'dd-chip-grey',
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
                    <td colspan="5"></td>
                    <td class="td-r">
                        <span class="dd-chip dd-chip-green">
                            <i class="bx bx-dollar" style="font-size:.68rem"></i>{{ number_format($totalPremium, 2) }}
                        </span>
                    </td>
                    <td class="td-r">
                        <span class="dd-chip dd-chip-amber">
                            <i class="bx bx-dollar" style="font-size:.68rem"></i>{{ number_format($totalCommission, 2) }}
                        </span>
                    </td>
                    <td colspan="4" style="font-size:.62rem;color:var(--dd-text-4)">
                        {{ $totalLeads }} lead{{ $totalLeads !== 1 ? 's' : '' }} &nbsp;·&nbsp;
                        <span style="color:#16a34a;font-weight:700">{{ $totalPendingContract }} contract</span>
                        &nbsp;·&nbsp;
                        <span style="color:#dc2626;font-weight:700">{{ $totalDeclined }} declined</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

<div class="dd-foot">
    <i class="bx bx-info-circle"></i>
    Showing leads where the manager set <strong>Pending Contract</strong> or <strong>Declined</strong>.
    Hover over an action chip to see the exact timestamp.
    Sale date filter applies to the lead's original <code>sale_date</code> field.
</div>

@endsection
