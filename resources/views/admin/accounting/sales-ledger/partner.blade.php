@extends('layouts.master')
@section('title', 'Sales Ledger — ' . $partner->name)

@section('css')
<style>
:root {
    --acct-gold:    #d4af37;
    --acct-gold-dk: #b8941f;
    --acct-surface: #f5f6fa;
    --acct-border:  #e8eaed;
    --acct-text:    #1a1a2e;
    --acct-muted:   #6b7280;
}
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }

/* Breadcrumb + header */
.sl-breadcrumb {
    font-size: .78rem;
    color: var(--acct-muted);
    margin-bottom: 14px;
}
.sl-breadcrumb a { color: var(--acct-gold-dk); text-decoration: none; font-weight: 600; }
.sl-breadcrumb a:hover { text-decoration: underline; }

.sl-partner-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
    background: #fff;
    border: 1px solid var(--acct-border);
    border-left: 5px solid var(--acct-gold);
    border-radius: 0 10px 10px 0;
    padding: 18px 22px;
}
.sl-partner-name { font-size: 1.2rem; font-weight: 800; color: var(--acct-text); margin: 0 0 3px; }
.sl-partner-meta { font-size: .8rem; color: var(--acct-muted); }

/* Summary strip */
.sl-summary-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
.sl-stat {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 8px;
    padding: 14px 18px;
}
.sl-stat-label { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--acct-muted); margin-bottom: 4px; }
.sl-stat-val { font-size: 1.3rem; font-weight: 800; font-family: 'Inter',system-ui,sans-serif; }
.sl-stat-val.dr    { color: #b45309; }
.sl-stat-val.cr    { color: #1d4ed8; }
.sl-stat-val.green { color: #059669; }
.sl-stat-val.red   { color: #dc2626; }

/* Filter bar */
.sl-filter-bar {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 8px;
    padding: 12px 18px;
    margin-bottom: 18px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.sl-filter-bar label {
    font-size: .73rem;
    font-weight: 700;
    color: var(--acct-muted);
    white-space: nowrap;
}
.sl-filter-bar .form-select, .sl-filter-bar .form-control {
    font-size: .82rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 5px 10px;
    height: auto;
}
.sl-filter-bar .form-select:focus, .sl-filter-bar .form-control:focus {
    border-color: var(--acct-gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,.15);
}

/* Ledger table */
.sl-ledger-card {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    overflow: hidden;
}
.sl-ledger-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 20px;
    border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
}
.sl-ledger-title {
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 7px;
}
.sl-ledger-title i { color: var(--acct-gold); }

.sl-table { width: 100%; border-collapse: collapse; }
.sl-table th {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--acct-muted);
    padding: 10px 16px;
    border-bottom: 2px solid var(--acct-border);
    background: #fafbfc;
    white-space: nowrap;
}
.sl-table td {
    padding: 11px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: .84rem;
    color: var(--acct-text);
    vertical-align: middle;
}
.sl-table tr:last-child td { border-bottom: none; }
.sl-table tbody tr:hover td { background: #fffef5; }

.entry-link {
    font-family: 'Courier New', monospace;
    font-size: .78rem;
    font-weight: 700;
    color: var(--acct-gold-dk);
    text-decoration: none;
}
.entry-link:hover { text-decoration: underline; }

.type-icon-sale     { color: #15803d; }
.type-icon-chargeback { color: #b91c1c; }
.type-icon-opening  { color: #92400e; }
.type-icon-payment  { color: #1d4ed8; }

.num { font-family: 'Courier New',monospace; font-weight: 700; font-size: .88rem; }
.num.dr  { color: #b45309; }
.num.cr  { color: #1d4ed8; }
.num.zero { color: #9ca3af; }
.num.pos  { color: #059669; }
.num.neg  { color: #dc2626; }

/* Total footer row */
.sl-table tfoot td {
    padding: 12px 16px;
    font-size: .84rem;
    font-weight: 700;
    background: #fafbfc;
    border-top: 2px solid var(--acct-gold);
    border-bottom: none;
    color: var(--acct-text);
}

/* Closing balance banner */
.sl-closing-banner {
    padding: 14px 20px;
    background: {{ $closingBalance >= 0 ? '#f0fdf4' : '#fef2f2' }};
    border-top: 1px solid {{ $closingBalance >= 0 ? '#bbf7d0' : '#fecaca' }};
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: .85rem;
}
.sl-closing-label { font-weight: 700; color: {{ $closingBalance >= 0 ? '#15803d' : '#b91c1c' }}; }
.sl-closing-val {
    font-size: 1.05rem;
    font-weight: 800;
    font-family: 'Courier New', monospace;
    color: {{ $closingBalance >= 0 ? '#059669' : '#dc2626' }};
}
</style>
@endsection

@section('content')
@include('admin.accounting._nav')

<div class="acct-page">

    {{-- Breadcrumb --}}
    <div class="sl-breadcrumb">
        <a href="{{ route('admin.accounting.sales-ledger') }}"><i class="bx bx-chevron-left"></i> Sales Ledger</a>
        &nbsp;/&nbsp; {{ $partner->name }}
    </div>

    {{-- Partner header --}}
    <div class="sl-partner-header">
        <div>
            <h1 class="sl-partner-name">{{ $partner->name }}</h1>
            <p class="sl-partner-meta">
                @if($partner->code) Code: <strong>{{ $partner->code }}</strong> &nbsp;·&nbsp; @endif
                Accounts Receivable — Sales Ledger
            </p>
        </div>
        <a href="{{ route('admin.accounting.partner-ledger.show', $partner->id) }}"
           style="font-size:.8rem;color:var(--acct-gold-dk);text-decoration:none;font-weight:600;align-self:center">
            <i class="bx bx-user-circle me-1"></i> Full Partner Ledger
        </a>
    </div>

    {{-- Summary strip --}}
    <div class="sl-summary-strip">
        <div class="sl-stat">
            <div class="sl-stat-label">Total Debits</div>
            <div class="sl-stat-val dr">${{ number_format($totalDr, 2) }}</div>
        </div>
        <div class="sl-stat">
            <div class="sl-stat-label">Total Credits</div>
            <div class="sl-stat-val cr">${{ number_format($totalCr, 2) }}</div>
        </div>
        <div class="sl-stat">
            <div class="sl-stat-label">Closing Balance</div>
            <div class="sl-stat-val {{ $closingBalance >= 0 ? 'green' : 'red' }}">
                ${{ number_format(abs($closingBalance), 2) }}
                <small style="font-size:.72rem;font-weight:500;color:var(--acct-muted)">{{ $closingBalance >= 0 ? 'Dr' : 'Cr' }}</small>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.accounting.sales-ledger.partner', $partner->id) }}" class="sl-filter-bar">
        <label>Filter:</label>
        <select name="carrier_id" class="form-select" style="max-width:180px">
            <option value="">All Carriers</option>
            @foreach($carriers as $c)
            <option value="{{ $c->id }}" {{ request('carrier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="max-width:145px" title="From date">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="max-width:145px" title="To date">
        <button type="submit" class="btn btn-sm" style="background:var(--acct-gold);color:#1a1a2e;font-weight:700;font-size:.79rem;border:none;padding:5px 16px">
            <i class="bx bx-search me-1"></i> Apply
        </button>
        @if(request()->hasAny(['carrier_id','date_from','date_to']))
        <a href="{{ route('admin.accounting.sales-ledger.partner', $partner->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size:.79rem">
            Clear
        </a>
        @endif
    </form>

    {{-- Ledger table --}}
    <div class="sl-ledger-card">
        <div class="sl-ledger-header">
            <span class="sl-ledger-title"><i class="bx bx-spreadsheet"></i> Transaction History</span>
            <span style="font-size:.75rem;color:var(--acct-muted)">{{ $lines->count() }} transactions</span>
        </div>

        @if($lines->count())
        <div style="overflow-x:auto">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Type</th>
                        <th>Insured / Description</th>
                        <th>Carrier</th>
                        <th>Reference</th>
                        <th class="text-end">Debit (Dr)</th>
                        <th class="text-end">Credit (Cr)</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $line)
                    <tr>
                        <td style="white-space:nowrap;color:var(--acct-muted)">
                            {{ \Carbon\Carbon::parse($line->entry_date)->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.accounting.journal.show', $line->entry_id) }}" class="entry-link">
                                {{ $line->entry_number }}
                            </a>
                        </td>
                        <td>
                            @if($line->type === 'sale')
                                <i class="bx bx-purchase-tag type-icon-sale" title="Sale"></i>
                                <span style="font-size:.75rem;color:#15803d;font-weight:600">Sale</span>
                            @elseif($line->type === 'chargeback')
                                <i class="bx bx-undo type-icon-chargeback" title="Chargeback"></i>
                                <span style="font-size:.75rem;color:#b91c1c;font-weight:600">Chargeback</span>
                            @elseif($line->type === 'payment_received')
                                <i class="bx bx-money type-icon-payment" title="Payment"></i>
                                <span style="font-size:.75rem;color:#1d4ed8;font-weight:600">Payment</span>
                            @else
                                <span style="font-size:.75rem;color:var(--acct-muted)">{{ ucfirst($line->type) }}</span>
                            @endif
                        </td>
                        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            @if($line->insured_name)
                                <span style="font-weight:600">{{ $line->insured_name }}</span>
                                @if($line->description && $line->description !== $line->insured_name)
                                    <br><small style="color:var(--acct-muted);font-size:.74rem">{{ $line->description }}</small>
                                @endif
                            @else
                                {{ $line->description ?? '—' }}
                            @endif
                        </td>
                        <td style="color:var(--acct-muted);font-size:.8rem">{{ $line->carrier_name ?? '—' }}</td>
                        <td style="font-family:'Courier New',monospace;font-size:.78rem;color:var(--acct-muted)">
                            {{ $line->reference ?? '—' }}
                        </td>
                        <td class="text-end">
                            @if($line->debit > 0)
                                <span class="num dr">${{ number_format($line->debit, 2) }}</span>
                            @else
                                <span class="num zero">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($line->credit > 0)
                                <span class="num cr">${{ number_format($line->credit, 2) }}</span>
                            @else
                                <span class="num zero">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="num {{ $line->running_balance > 0 ? 'pos' : ($line->running_balance < 0 ? 'neg' : 'zero') }}">
                                ${{ number_format(abs($line->running_balance), 2) }}
                                <small style="font-size:.65rem;font-weight:500;opacity:.7">{{ $line->running_balance > 0 ? 'Dr' : ($line->running_balance < 0 ? 'Cr' : '') }}</small>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;font-size:.78rem;color:var(--acct-muted);letter-spacing:.05em;text-transform:uppercase">TOTALS</td>
                        <td class="text-end"><span class="num dr">${{ number_format($totalDr, 2) }}</span></td>
                        <td class="text-end"><span class="num cr">${{ number_format($totalCr, 2) }}</span></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {{-- Closing balance banner --}}
        <div class="sl-closing-banner">
            <span class="sl-closing-label">Closing Balance</span>
            <span class="sl-closing-val">
                ${{ number_format(abs($closingBalance), 2) }}
                {{ $closingBalance >= 0 ? 'Dr' : 'Cr' }}
            </span>
        </div>
        @else
        <div style="text-align:center;padding:50px 20px;color:var(--acct-muted)">
            <i class="bx bx-spreadsheet" style="font-size:2.5rem;color:#e5e7eb;display:block;margin-bottom:10px"></i>
            <p style="font-size:.85rem">No transactions found for this partner with the current filters.</p>
        </div>
        @endif
    </div>
</div>
@endsection
