@extends('layouts.master')
@section('title', 'Balance Sheet')

@section('css')
<style>
:root { --acct-gold:#d4af37; --acct-surface:#f5f6fa; --acct-card-bg:#fff; --acct-border:#e8eaed; --acct-text:#1a1a2e; --acct-muted:#6b7280; }
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }
.bs-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
@media(max-width:900px){ .bs-grid{ grid-template-columns:1fr; } }
.rpt-card { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; overflow:hidden; }
.rpt-card-header { padding:14px 20px; font-size:.88rem; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px; }
.rpt-card-header i { font-size:1.1rem; }
.header-asset { background:#1e40af; }
.header-liability { background:#be185d; }
.header-equity { background:#6b21a8; }
.rpt-section-header { background:#f3f4f6; padding:9px 20px; font-size:.75rem; font-weight:700; color:var(--acct-muted); text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid var(--acct-border); }
.rpt-row { display:flex; justify-content:space-between; padding:10px 20px; border-bottom:1px solid #f5f5f5; font-size:.84rem; }
.rpt-row:last-child { border-bottom:none; }
.rpt-subtotal { display:flex; justify-content:space-between; padding:11px 20px; background:#f8f9fa; font-size:.88rem; font-weight:700; border-top:2px solid var(--acct-border); }
.bs-check { background:#1e1e2e; color:#fff; border-radius:10px; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; font-size:.9rem; font-weight:700; }
.balanced-ok { color:#34d399; }
.balanced-err { color:#f87171; }
.filter-bar { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; }
.filter-bar label { font-size:.78rem; font-weight:600; color:var(--acct-muted); display:block; margin-bottom:4px; }
@media print { .acct-subnav,.filter-bar,nav,.sidebar,header { display:none!important; } body{ background:#fff; } }
</style>
@endsection

@section('content')
@include('admin.accounting._nav')

<div class="acct-page">

    <div class="d-flex align-items-center gap-3 mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#1e1e2e"><i class="bx bx-wallet me-2" style="color:#7c3aed"></i>Balance Sheet</h4>
            <small class="text-muted">As of {{ \Carbon\Carbon::parse($asOf)->format('F j, Y') }}</small>
        </div>
        <span class="ms-auto {{ $balanced ? 'text-success' : 'text-danger' }} fw-bold d-print-none" style="font-size:.85rem">
            {{ $balanced ? '✓ Balanced' : '⚠ Not Balanced' }}
        </span>
        <button class="btn btn-sm btn-outline-secondary d-print-none" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Print
        </button>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-bar d-print-none">
        <div>
            <label>As of Date</label>
            <input type="date" name="as_of" class="form-control form-control-sm" value="{{ $asOf }}" max="{{ now()->toDateString() }}">
        </div>
        <button type="submit" class="btn btn-sm btn-primary align-self-end">
            <i class="bx bx-filter-alt me-1"></i> Apply
        </button>
    </form>

    <div class="bs-grid">

        {{-- ASSETS --}}
        <div class="rpt-card">
            <div class="rpt-card-header header-asset">
                <i class="bx bx-trending-up"></i> Assets
            </div>
            <div class="rpt-section-header">Current Assets (1xxx)</div>
            @forelse($assetRows as $row)
            <div class="rpt-row">
                <span><span class="text-muted font-monospace me-2" style="font-size:.76rem">{{ $row->account_code }}</span>{{ $row->account_name }}</span>
                <span class="fw-semibold" style="color:#1e40af">$ {{ number_format(max(0, $row->total_debit - $row->total_credit), 2) }}</span>
            </div>
            @empty
            <div class="rpt-row text-muted fst-italic">No asset accounts with activity.</div>
            @endforelse
            <div class="rpt-subtotal">
                <span>Total Assets</span>
                <span style="color:#1e40af">$ {{ number_format($totalAssets, 2) }}</span>
            </div>
        </div>

        {{-- LIABILITIES + EQUITY --}}
        <div>
            <div class="rpt-card mb-3">
                <div class="rpt-card-header header-liability">
                    <i class="bx bx-credit-card"></i> Liabilities
                </div>
                <div class="rpt-section-header">Current Liabilities (2xxx)</div>
                @forelse($liabilityRows as $row)
                <div class="rpt-row">
                    <span><span class="text-muted font-monospace me-2" style="font-size:.76rem">{{ $row->account_code }}</span>{{ $row->account_name }}</span>
                    <span class="fw-semibold" style="color:#be185d">$ {{ number_format(max(0, $row->total_credit - $row->total_debit), 2) }}</span>
                </div>
                @empty
                <div class="rpt-row text-muted fst-italic">No liability accounts with activity.</div>
                @endforelse
                <div class="rpt-subtotal">
                    <span>Total Liabilities</span>
                    <span style="color:#be185d">$ {{ number_format($totalLiabilities, 2) }}</span>
                </div>
            </div>

            <div class="rpt-card">
                <div class="rpt-card-header header-equity">
                    <i class="bx bx-dollar-circle"></i> Equity
                </div>
                <div class="rpt-section-header">Owner's Equity (3xxx)</div>
                @forelse($equityRows as $row)
                <div class="rpt-row">
                    <span><span class="text-muted font-monospace me-2" style="font-size:.76rem">{{ $row->account_code }}</span>{{ $row->account_name }}</span>
                    <span class="fw-semibold" style="color:#6b21a8">$ {{ number_format(max(0, $row->total_credit - $row->total_debit), 2) }}</span>
                </div>
                @empty
                <div class="rpt-row text-muted fst-italic">No equity accounts with activity.</div>
                @endforelse
                <div class="rpt-row" style="background:#faf5ff">
                    <span class="fw-semibold">Retained Earnings (from P&L)</span>
                    <span class="fw-semibold {{ $retainedEarnings >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $retainedEarnings < 0 ? '(' : '' }}$ {{ number_format(abs($retainedEarnings), 2) }}{{ $retainedEarnings < 0 ? ')' : '' }}
                    </span>
                </div>
                <div class="rpt-subtotal">
                    <span>Total Equity</span>
                    <span style="color:#6b21a8">$ {{ number_format($totalEquity, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Balance check --}}
    <div class="bs-check">
        <div>
            <div style="font-size:.75rem;opacity:.7;font-weight:500;margin-bottom:2px">ACCOUNTING EQUATION CHECK</div>
            <div>Total Assets = Total Liabilities + Total Equity</div>
        </div>
        <div class="text-end">
            <div style="font-size:.75rem;opacity:.7;margin-bottom:2px">
                $ {{ number_format($totalAssets, 2) }} = $ {{ number_format($totalLiabilitiesAndEquity, 2) }}
            </div>
            <div class="{{ $balanced ? 'balanced-ok' : 'balanced-err' }}">
                {{ $balanced ? '✓ BALANCED' : '⚠ DIFFERENCE: $ ' . number_format(abs($totalAssets - $totalLiabilitiesAndEquity), 2) }}
            </div>
        </div>
    </div>

</div>
@endsection
