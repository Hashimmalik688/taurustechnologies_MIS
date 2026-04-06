@extends('layouts.master')
@section('title', 'Profit & Loss Statement')

@section('css')
<style>
:root { --acct-gold:#d4af37; --acct-surface:#f5f6fa; --acct-card-bg:#fff; --acct-border:#e8eaed; --acct-text:#1a1a2e; --acct-muted:#6b7280; }
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }
.rpt-card { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; overflow:hidden; margin-bottom:24px; }
.rpt-card-header { background:#1e1e2e; color:#fff; padding:14px 20px; display:flex; align-items:center; gap:10px; font-size:.9rem; font-weight:700; }
.rpt-card-header i { color:var(--acct-gold); font-size:1.1rem; }
.rpt-section-header { background:#f3f4f6; padding:9px 20px; font-size:.78rem; font-weight:700; color:var(--acct-muted); text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid var(--acct-border); }
.rpt-row { display:flex; justify-content:space-between; align-items:center; padding:10px 20px; border-bottom:1px solid #f5f5f5; font-size:.86rem; }
.rpt-row:last-child { border-bottom:none; }
.rpt-row-label { color:var(--acct-text); }
.rpt-row-value { font-weight: 600; }
.rpt-subtotal { background:#fafbff; display:flex; justify-content:space-between; padding:11px 20px; border-bottom:2px solid var(--acct-border); border-top:2px solid var(--acct-border); font-size:.88rem; font-weight:700; }
.rpt-total { background:#1e1e2e; color:#fff; display:flex; justify-content:space-between; padding:14px 20px; font-size:.95rem; font-weight:700; }
.text-profit { color:#059669; }
.text-loss { color:#dc2626; }
.kpi-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
@media(max-width:900px){ .kpi-strip{ grid-template-columns:repeat(2,1fr); } }
.kpi-card { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; padding:18px 20px; }
.kpi-label { font-size:.75rem; color:var(--acct-muted); font-weight:600; text-transform:uppercase; margin-bottom:4px; }
.kpi-value { font-size:1.4rem; font-weight:700; color:var(--acct-text); }
.filter-bar { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; }
.filter-bar label { font-size:.78rem; font-weight:600; color:var(--acct-muted); display:block; margin-bottom:4px; }
@media print { .acct-subnav,.filter-bar,.kpi-strip,nav,.sidebar,header { display:none!important; } body { background:#fff; } }
</style>
@endsection

@section('content')
@include('admin.accounting._nav')

<div class="acct-page">

    <div class="d-flex align-items-center gap-3 mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#1e1e2e"><i class="bx bx-line-chart text-success me-2"></i>Profit & Loss Statement</h4>
            <small class="text-muted">{{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}</small>
        </div>
        <button class="btn btn-sm btn-outline-secondary ms-auto d-print-none" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Print
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" class="filter-bar d-print-none">
        <div>
            <label>From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
        </div>
        <button type="submit" class="btn btn-sm btn-primary align-self-end">
            <i class="bx bx-filter-alt me-1"></i> Apply
        </button>
    </form>

    {{-- KPI Strip --}}
    <div class="kpi-strip d-print-none">
        <div class="kpi-card">
            <div class="kpi-label">Sales Revenue</div>
            <div class="kpi-value text-success">$ {{ number_format($salesIncome, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Sales Returns</div>
            <div class="kpi-value text-danger">$ {{ number_format($salesReturns, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Total Expenses</div>
            <div class="kpi-value" style="color:#d97706">$ {{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <div class="kpi-value {{ $netProfit >= 0 ? 'text-profit' : 'text-loss' }}">
                {{ $netProfit < 0 ? '(' : '' }}$ {{ number_format(abs($netProfit), 2) }}{{ $netProfit < 0 ? ')' : '' }}
            </div>
        </div>
    </div>

    {{-- P&L Report --}}
    <div class="rpt-card">
        <div class="rpt-card-header">
            <i class="bx bx-line-chart"></i>
            Profit & Loss — {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
        </div>

        {{-- Revenue Section --}}
        <div class="rpt-section-header">Revenue</div>
        <div class="rpt-row">
            <span class="rpt-row-label">Sales / Commission Income (4100)</span>
            <span class="rpt-row-value text-success">$ {{ number_format($salesIncome, 2) }}</span>
        </div>
        <div class="rpt-row" style="color:#b91c1c">
            <span class="rpt-row-label">Less: Sales Returns & Chargebacks (4200)</span>
            <span class="rpt-row-value">($ {{ number_format($salesReturns, 2) }})</span>
        </div>
        <div class="rpt-subtotal">
            <span>Gross Profit</span>
            <span class="{{ $grossProfit >= 0 ? 'text-profit' : 'text-loss' }}">$ {{ number_format($grossProfit, 2) }}</span>
        </div>

        {{-- Expenses Section --}}
        <div class="rpt-section-header">Operating Expenses</div>
        @forelse($expenseRows as $exp)
        <div class="rpt-row">
            <span class="rpt-row-label">
                <span class="text-muted font-monospace me-2" style="font-size:.78rem">{{ $exp->account_code }}</span>
                {{ $exp->account_name }}
            </span>
            <span class="rpt-row-value" style="color:#d97706">$ {{ number_format(max(0, $exp->total_debit - $exp->total_credit), 2) }}</span>
        </div>
        @empty
        <div class="rpt-row text-muted fst-italic">No expense entries recorded for this period.</div>
        @endforelse

        <div class="rpt-subtotal">
            <span>Total Expenses</span>
            <span style="color:#d97706">$ {{ number_format($totalExpenses, 2) }}</span>
        </div>

        {{-- Net --}}
        <div class="rpt-total">
            <span>NET {{ $netProfit >= 0 ? 'PROFIT' : 'LOSS' }}</span>
            <span class="{{ $netProfit >= 0 ? 'text-profit' : 'text-loss' }}">
                {{ $netProfit < 0 ? '(' : '' }}$ {{ number_format(abs($netProfit), 2) }}{{ $netProfit < 0 ? ')' : '' }}
            </span>
        </div>
    </div>

</div>
@endsection
