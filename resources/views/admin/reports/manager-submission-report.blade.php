@extends('layouts.master')

@section('title')
    Manager Submission Report
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
:root {
    --msr-gold:         #d4af37;
    --msr-gold-dim:     rgba(212,175,55,.12);
    --msr-green:        #22c55e;
    --msr-green-dim:    rgba(34,197,94,.11);
    --msr-red:          #ef4444;
    --msr-red-dim:      rgba(239,68,68,.11);
    --msr-indigo:       #6366f1;
    --msr-indigo-dim:   rgba(99,102,241,.12);
    --msr-surface:      var(--bs-card-bg, #ffffff);
    --msr-border:       rgba(0,0,0,.07);
    --msr-shadow:       0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --msr-text-1:       var(--bs-surface-900, #0f172a);
    --msr-text-2:       var(--bs-surface-700, #374151);
    --msr-text-3:       var(--bs-surface-500, #64748b);
    --msr-text-4:       var(--bs-surface-400, #94a3b8);
}

/* Header */
.msr-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:.6rem; flex-wrap:wrap; gap:.35rem; }
.msr-hdr-title { display:flex; align-items:center; gap:.45rem; flex-wrap:wrap; }
.msr-hdr-icon { width:28px; height:28px; border-radius:.4rem; flex-shrink:0; background:linear-gradient(135deg,var(--msr-gold),#b8941f); display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(212,175,55,.35); }
.msr-hdr-icon i { font-size:.95rem; color:#0f172a; }
.msr-hdr h5 { margin:0; font-size:.95rem; font-weight:800; color:var(--msr-text-1); }
.msr-hdr-sub { font-size:.67rem; color:var(--msr-text-3); font-weight:400; border-left:2px solid var(--msr-border); padding-left:.45rem; margin-left:.1rem; }
.msr-back { font-size:.7rem; font-weight:700; padding:.28rem .6rem; border-radius:20px; border:1.5px solid var(--msr-border); background:transparent; color:var(--msr-text-3); text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s; }
.msr-back:hover { border-color:var(--msr-gold); color:#92760d; }

/* Filter bar */
.msr-filter { display:flex; flex-wrap:wrap; gap:.4rem; align-items:flex-end; background:var(--msr-surface); border:1px solid var(--msr-border); border-radius:.55rem; padding:.5rem .7rem; margin-bottom:.7rem; box-shadow:var(--msr-shadow); }
.msr-filter label { font-size:.58rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:var(--msr-text-4); display:block; margin-bottom:.12rem; }
.msr-filter input[type=date] { font-size:.73rem; padding:.28rem .45rem; border-radius:.4rem; border:1.5px solid var(--msr-border); background:var(--bs-input-bg,#f8fafc); color:var(--msr-text-1); outline:none; transition:border-color .15s; }
.msr-filter input[type=date]:focus { border-color:var(--msr-gold); box-shadow:0 0 0 2px var(--msr-gold-dim); }
.msr-btn { font-size:.7rem; font-weight:700; padding:.3rem .65rem; border-radius:20px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s; text-decoration:none; }
.msr-btn-apply { background:linear-gradient(135deg,var(--msr-gold),#b8941f); color:#0f172a; }
.msr-btn-apply:hover { box-shadow:0 2px 10px rgba(212,175,55,.4); transform:translateY(-1px); }
.msr-btn-reset { background:transparent; border:1.5px solid var(--msr-border) !important; color:var(--msr-text-3); }
.msr-btn-reset:hover { border-color:var(--msr-gold) !important; color:#92760d; }

/* KPI strip */
.msr-kpis { display:grid; grid-template-columns:repeat(3,1fr); gap:.45rem; margin-bottom:.7rem; }
@media(max-width:600px) { .msr-kpis { grid-template-columns:1fr; } }
.msr-kpi { background:var(--msr-surface); border:1px solid var(--msr-border); border-radius:.55rem; padding:.55rem .75rem; position:relative; overflow:hidden; box-shadow:var(--msr-shadow); }
.msr-kpi::before { content:''; position:absolute; inset:0 auto 0 0; width:3.5px; border-radius:2px 0 0 2px; }
.msr-kpi-total::before   { background:linear-gradient(180deg,var(--msr-gold),#b8941f); }
.msr-kpi-contract::before { background:linear-gradient(180deg,var(--msr-green),#16a34a); }
.msr-kpi-declined::before { background:linear-gradient(180deg,var(--msr-red),#dc2626); }
.msr-kpi-lbl { font-size:.58rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:var(--msr-text-4); margin-bottom:.15rem; }
.msr-kpi-val { font-size:1.55rem; font-weight:900; color:var(--msr-text-1); line-height:1; }
.msr-kpi-sub { font-size:.63rem; color:var(--msr-text-3); margin-top:.18rem; }

/* Card */
.msr-card { background:var(--msr-surface); border:1px solid var(--msr-border); border-radius:.55rem; overflow:hidden; box-shadow:var(--msr-shadow); margin-bottom:1rem; }
.msr-card-head { display:flex; align-items:center; justify-content:space-between; padding:.55rem .75rem; border-bottom:1px solid var(--msr-border); }
.msr-card-head h6 { margin:0; font-size:.75rem; font-weight:800; color:var(--msr-text-1); display:flex; align-items:center; gap:.3rem; }
.msr-card-meta { font-size:.62rem; color:var(--msr-text-4); font-weight:600; }

/* Table */
.msr-tbl { width:100%; border-collapse:collapse; font-size:.72rem; }
.msr-tbl th { padding:.45rem .65rem; font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--msr-text-4); background:var(--bs-table-bg,#f8fafc); border-bottom:2px solid var(--msr-border); white-space:nowrap; }
.msr-tbl td { padding:.5rem .65rem; border-bottom:1px solid var(--msr-border); color:var(--msr-text-2); vertical-align:middle; }
.msr-tbl tbody tr:hover { background:rgba(212,175,55,.04); }
.msr-tbl tfoot td { padding:.5rem .65rem; font-weight:800; border-top:2px solid var(--msr-border); color:var(--msr-text-1); }
.tr { text-align:right; }

.msr-mgr-link { font-size:.75rem; font-weight:700; color:var(--msr-text-1); text-decoration:none; display:inline-flex; align-items:center; gap:.3rem; }
.msr-mgr-link:hover { color:var(--msr-gold); }
.msr-mgr-link i { font-size:.8rem; color:var(--msr-text-4); }
.msr-mgr-link:hover i { color:var(--msr-gold); }

/* Chips */
.msr-chip { display:inline-flex; align-items:center; gap:.18rem; padding:.18rem .42rem; border-radius:20px; font-size:.65rem; font-weight:700; white-space:nowrap; }
.msr-chip-green   { background:var(--msr-green-dim); color:#16a34a; }
.msr-chip-red     { background:var(--msr-red-dim); color:#dc2626; }
.msr-chip-gold    { background:var(--msr-gold-dim); color:#92760d; }
.msr-chip-indigo  { background:var(--msr-indigo-dim); color:#4338ca; }

.msr-empty { padding:2.5rem; text-align:center; color:var(--msr-text-4); }
.msr-empty i { font-size:2rem; display:block; margin-bottom:.5rem; }
</style>
@endsection

@push('scripts')
<script>
function setToday() {
    const today = new Date().toISOString().slice(0, 10);
    document.querySelector('input[name="date_from"]').value = today;
    document.querySelector('input[name="date_to"]').value = today;
    document.querySelector('.msr-filter').submit();
}
</script>
@endpush

@section('content')

{{-- Header --}}
<div class="msr-hdr">
    <div class="msr-hdr-title">
        <div class="msr-hdr-icon"><i class="bx bx-user-check"></i></div>
        <div>
            <h5>Manager Submission Report</h5>
            <span class="msr-hdr-sub">Tracks when managers approve sales to Pending Contract or mark them Declined</span>
        </div>
    </div>
    <a href="{{ route('settings.reports.hub') }}" class="msr-back">
        <i class="bx bx-arrow-back"></i> Reports Hub
    </a>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('settings.reports.manager-submission-report') }}" class="msr-filter">
    <div>
        <label>Action Date From</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}">
    </div>
    <div>
        <label>Action Date To</label>
        <input type="date" name="date_to" value="{{ $dateTo }}">
    </div>
    <div>
        <label>Carrier</label>
        <select name="carrier_id" style="font-size:.73rem;padding:.28rem .45rem;border-radius:.4rem;border:1.5px solid var(--msr-border);background:var(--bs-input-bg,#f8fafc);color:var(--msr-text-1);outline:none;min-width:130px">
            <option value="">All Carriers</option>
            @foreach($allCarriers as $c)
            <option value="{{ $c->id }}" {{ $carrierId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Partner</label>
        <select name="partner_name" style="font-size:.73rem;padding:.28rem .45rem;border-radius:.4rem;border:1.5px solid var(--msr-border);background:var(--bs-input-bg,#f8fafc);color:var(--msr-text-1);outline:none;min-width:120px">
            <option value="">All Partners</option>
            @foreach($allPartners as $p)
            <option value="{{ $p }}" {{ $partnerName === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Policy Type</label>
        <select name="policy_type" style="font-size:.73rem;padding:.28rem .45rem;border-radius:.4rem;border:1.5px solid var(--msr-border);background:var(--bs-input-bg,#f8fafc);color:var(--msr-text-1);outline:none;min-width:110px">
            <option value="">All Types</option>
            @foreach($allPolicyTypes as $pt)
            <option value="{{ $pt }}" {{ $policyType === $pt ? 'selected' : '' }}>{{ $pt }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="msr-btn msr-btn-apply">
        <i class="bx bx-filter-alt"></i> Apply
    </button>
    <button type="button" class="msr-btn msr-btn-reset" onclick="setToday()" title="Set both dates to today">
        <i class="bx bx-calendar-check"></i> Today
    </button>
    <a href="{{ route('settings.reports.manager-submission-report') }}" class="msr-btn msr-btn-reset">
        <i class="bx bx-x"></i> Reset
    </a>
    <div style="font-size:.63rem;color:var(--msr-text-3);align-self:flex-end;margin-left:.2rem">
        <strong style="color:var(--msr-text-2)">{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}</strong>
        &nbsp;→&nbsp;
        <strong style="color:var(--msr-text-2)">{{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</strong>
    </div>
</form>

{{-- KPI strip --}}
<div class="msr-kpis">
    <div class="msr-kpi msr-kpi-total">
        <div class="msr-kpi-lbl">Total Actions</div>
        <div class="msr-kpi-val">{{ number_format($grandTotal) }}</div>
        <div class="msr-kpi-sub">Approved + Declined by managers</div>
    </div>
    <div class="msr-kpi msr-kpi-contract">
        <div class="msr-kpi-lbl">Pending Contract</div>
        <div class="msr-kpi-val" style="color:#16a34a">{{ number_format($grandPendingContract) }}</div>
        <div class="msr-kpi-sub">Approved sales sent to contract</div>
    </div>
    <div class="msr-kpi msr-kpi-declined">
        <div class="msr-kpi-lbl">Declined</div>
        <div class="msr-kpi-val" style="color:#dc2626">{{ number_format($grandDeclined) }}</div>
        <div class="msr-kpi-sub">Sales marked declined</div>
    </div>
</div>

{{-- Main table --}}
<div class="msr-card">
    <div class="msr-card-head">
        <h6><i class="bx bx-group"></i> Manager Breakdown</h6>
        <span class="msr-card-meta">{{ $rows->count() }} manager{{ $rows->count() !== 1 ? 's' : '' }}</span>
    </div>

    @if($rows->isEmpty())
    <div class="msr-empty">
        <i class="bx bx-ghost"></i>
        <strong>No manager actions found</strong>
        <span style="display:block;font-size:.72rem">No sales were moved to Pending Contract or Declined in this date range.</span>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="msr-tbl">
            <thead>
                <tr>
                    <th style="width:26px">#</th>
                    <th style="min-width:180px">Manager</th>
                    <th class="tr" style="min-width:130px">Pending Contract</th>
                    <th class="tr" style="min-width:100px">Declined</th>
                    <th class="tr" style="min-width:80px">Total</th>
                    <th class="tr" style="min-width:80px">Decline Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                @php
                    $declineRate = $row->total > 0 ? round(($row->declined / $row->total) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="color:var(--msr-text-4);font-size:.65rem">{{ $i + 1 }}</td>
                    <td>
                        <a href="{{ route('settings.reports.manager-submission-report.drilldown', array_merge(['manager_id' => $row->manager_id, 'date_from' => $dateFrom, 'date_to' => $dateTo], array_filter(['carrier_id' => $carrierId, 'partner_name' => $partnerName, 'policy_type' => $policyType]))) }}"
                           class="msr-mgr-link">
                            <i class="bx bx-user"></i>
                            {{ $row->manager_name }}
                        </a>
                    </td>
                    <td class="tr">
                        <a href="{{ route('settings.reports.manager-submission-report.drilldown', array_merge(['manager_id' => $row->manager_id, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'action' => 'pending_contract'], array_filter(['carrier_id' => $carrierId, 'partner_name' => $partnerName, 'policy_type' => $policyType]))) }}"
                           style="text-decoration:none">
                            <span class="msr-chip msr-chip-green">
                                <i class="bx bx-check-circle"></i>
                                {{ number_format($row->pending_contract) }}
                            </span>
                        </a>
                    </td>
                    <td class="tr">
                        <a href="{{ route('settings.reports.manager-submission-report.drilldown', array_merge(['manager_id' => $row->manager_id, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'action' => 'declined'], array_filter(['carrier_id' => $carrierId, 'partner_name' => $partnerName, 'policy_type' => $policyType]))) }}"
                           style="text-decoration:none">
                            <span class="msr-chip msr-chip-red">
                                <i class="bx bx-x-circle"></i>
                                {{ number_format($row->declined) }}
                            </span>
                        </a>
                    </td>
                    <td class="tr">
                        <span class="msr-chip msr-chip-gold">{{ number_format($row->total) }}</span>
                    </td>
                    <td class="tr" style="font-size:.7rem;font-weight:700;color:{{ $declineRate > 30 ? '#dc2626' : 'var(--msr-text-3)' }}">
                        {{ $declineRate }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="font-size:.68rem;font-weight:800">
                        <i class="bx bx-sum" style="color:var(--msr-gold)"></i> TOTAL
                    </td>
                    <td class="tr">
                        <span class="msr-chip msr-chip-green">
                            <i class="bx bx-check-circle"></i>
                            {{ number_format($grandPendingContract) }}
                        </span>
                    </td>
                    <td class="tr">
                        <span class="msr-chip msr-chip-red">
                            <i class="bx bx-x-circle"></i>
                            {{ number_format($grandDeclined) }}
                        </span>
                    </td>
                    <td class="tr">
                        <span class="msr-chip msr-chip-gold">{{ number_format($grandTotal) }}</span>
                    </td>
                    <td class="tr" style="font-size:.7rem;font-weight:700;color:var(--msr-text-3)">
                        {{ $grandTotal > 0 ? round(($grandDeclined / $grandTotal) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

<div style="font-size:.62rem;color:var(--msr-text-4);margin-top:-.3rem">
    <i class="bx bx-info-circle"></i>
    Date range filters by the <strong>manager's action date</strong> (<code>pending_contract_at</code> or <code>declined_at</code>), not sale date.
    Click a manager's name or action count to view individual leads.
</div>

@endsection
