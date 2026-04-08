@extends('layouts.master')
@section('title', 'Sales Returns Ledger — Chargebacks')

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
.sl-header { margin-bottom: 22px; }
.sl-title { font-size: 1.3rem; font-weight: 800; color: var(--acct-text); margin: 0 0 3px; }
.sl-sub { font-size: .82rem; color: var(--acct-muted); margin: 0; }

.sl-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}
.sl-sum-card {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    padding: 18px 22px;
    position: relative; overflow: hidden;
}
.sl-sum-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0; width: 4px;
    border-radius: 10px 0 0 10px;
}
.sl-sum-card.total::before   { background: #b91c1c; }
.sl-sum-card.count::before   { background: #d97706; }
.sl-sum-card.cleared::before { background: #059669; }
.sl-sum-label { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--acct-muted); margin-bottom: 5px; }
.sl-sum-value { font-size: 1.45rem; font-weight: 800; font-family: 'Inter',system-ui,sans-serif; color: var(--acct-text); }
.sl-sum-value.red   { color: #b91c1c; }
.sl-sum-value.amber { color: #d97706; }
.sl-sum-value.green { color: #059669; }

.sl-card { background: #fff; border: 1px solid var(--acct-border); border-radius: 10px; overflow: hidden; }
.sl-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
}
.sl-card-title { font-size: .82rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #374151; display: flex; align-items: center; gap: 7px; }

.sl-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
.sl-table th { background: #f9fafb; padding: 9px 14px; text-align: left; font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--acct-muted); border-bottom: 1px solid var(--acct-border); white-space: nowrap; }
.sl-table td { padding: 11px 14px; border-bottom: 1px solid #f1f3f5; vertical-align: middle; }
.sl-table tr:last-child td { border-bottom: none; }
.sl-table tr:hover td { background: #fafbfc; }

.num-cell { font-family: 'Inter',system-ui,sans-serif; }
.num-cr { color: #b91c1c; font-weight: 700; }

.sl-filter {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    padding: 14px 20px;
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
    margin-bottom: 18px;
}
.sl-filter label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--acct-muted); display: block; margin-bottom: 4px; }
.sl-filter input, .sl-filter select {
    border: 1px solid var(--acct-border); border-radius: 6px; padding: 6px 10px;
    font-size: .82rem; color: var(--acct-text); background: #fff;
}
.sl-filter input:focus, .sl-filter select:focus { outline: none; border-color: var(--acct-gold); }
.sl-btn { background: var(--acct-gold); color: #1a1a2e; border: none; border-radius: 6px; padding: 7px 16px; font-size: .82rem; font-weight: 700; cursor: pointer; }
.sl-btn:hover { background: var(--acct-gold-dk); }

.partner-name { font-weight: 600; font-size: .84rem; color: var(--acct-text); }
.partner-code { font-size: .72rem; color: var(--acct-muted); font-family: monospace; }

.sl-empty { padding: 48px 24px; text-align: center; color: var(--acct-muted); }
.sl-empty i { font-size: 2.4rem; margin-bottom: 10px; display: block; color: #d1d5db; }

.badge-cleared { background: #dcfce7; color: #15803d; border-radius: 4px; padding: 2px 8px; font-size: .72rem; font-weight: 700; }
.badge-pending { background: #fef3c7; color: #b45309; border-radius: 4px; padding: 2px 8px; font-size: .72rem; font-weight: 700; }
.badge-return  { background: #fee2e2; color: #b91c1c; border-radius: 4px; padding: 2px 8px; font-size: .72rem; font-weight: 700; }
</style>
@endsection

@section('content')
@include('admin.accounting._nav')

<div class="acct-page">

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="sl-header d-flex justify-content-between align-items-start">
        <div>
            <h1 class="sl-title"><i class="bx bx-undo" style="color:#b91c1c"></i> Sales Returns Ledger</h1>
            <p class="sl-sub">All chargeback / sales return entries — Account 4200 Sales Returns</p>
        </div>
        <a href="{{ route('admin.accounting.sales-ledger') }}" class="sl-btn" style="background:#f1f5f9;color:#374151;font-weight:600;text-decoration:none">
            <i class="bx bx-arrow-back"></i> Sales Ledger
        </a>
    </div>

    {{-- ── Filters ──────────────────────────────────────────────────────── --}}
    <form method="GET" class="sl-filter">
        <div>
            <label>From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" style="width:140px">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" style="width:140px">
        </div>
        <div>
            <label>Partner</label>
            <select name="partner_id" style="min-width:160px">
                <option value="">All Partners</option>
                @foreach($partners as $p)
                    <option value="{{ $p->id }}" {{ $partnerId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Search</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Insured name, policy #…" style="min-width:200px">
        </div>
        <div>
            <button type="submit" class="sl-btn">Apply</button>
            @if($search || $partnerId)
                <a href="{{ route('admin.accounting.sales-returns') }}" style="margin-left:8px;font-size:.8rem;color:var(--acct-muted)">Clear</a>
            @endif
        </div>
    </form>

    {{-- ── KPI Summary Cards ────────────────────────────────────────────── --}}
    <div class="sl-summary">
        <div class="sl-sum-card total">
            <div class="sl-sum-label">Total Returns</div>
            <div class="sl-sum-value red">${{ number_format($totalAmount, 2) }}</div>
            <div style="font-size:.72rem;color:var(--acct-muted);margin-top:4px">Dr 4200 / Cr 1200 AR</div>
        </div>
        <div class="sl-sum-card count">
            <div class="sl-sum-label">Entries</div>
            <div class="sl-sum-value amber">{{ number_format($totalEntries) }}</div>
            <div style="font-size:.72rem;color:var(--acct-muted);margin-top:4px">Chargeback events</div>
        </div>
        <div class="sl-sum-card cleared">
            <div class="sl-sum-label">Recoveries Recorded</div>
            @php $clearedCount = $leadsByEntry->filter(fn($l) => $l->ledger_chargeback_paid_entry_id)->count(); @endphp
            <div class="sl-sum-value green">{{ $clearedCount }}</div>
            <div style="font-size:.72rem;color:var(--acct-muted);margin-top:4px">Marked paid from chargebacks</div>
        </div>
    </div>

    {{-- ── Partner Breakdown ────────────────────────────────────────────── --}}
    @if($partnerBreakdown->count())
    <div class="sl-card mb-4">
        <div class="sl-card-header">
            <span class="sl-card-title"><i class="bx bx-user-circle"></i> Partner Breakdown</span>
        </div>
        <div style="overflow-x:auto">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th class="text-end">Total Returns</th>
                        <th class="text-center">Entries</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partnerBreakdown as $row)
                    <tr>
                        <td>
                            <div class="partner-name">{{ $row->partner_name }}</div>
                            @if($row->partner_code)
                            <div class="partner-code">{{ $row->partner_code }}</div>
                            @endif
                        </td>
                        <td class="text-end num-cell num-cr">-${{ number_format($row->total_amount, 2) }}</td>
                        <td class="text-center" style="color:var(--acct-muted)">{{ $row->tx_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── All Sales Return Entries ─────────────────────────────────────── --}}
    <div class="sl-card">
        <div class="sl-card-header">
            <span class="sl-card-title"><i class="bx bx-list-ul"></i> All Sales Return Entries</span>
            <span style="font-size:.75rem;color:var(--acct-muted)">{{ number_format($totalEntries) }} entries</span>
        </div>
        @if($entries->count())
        <div style="overflow-x:auto">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>Entry #</th>
                        <th>Date</th>
                        <th>Insured / Description</th>
                        <th class="text-end">Amount (Dr)</th>
                        <th>Reference</th>
                        <th>Recovery</th>
                        <th>Recorded By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $je)
                    @php
                        $lead     = $leadsByEntry[$je->id] ?? null;
                        $cleared  = $lead && $lead->ledger_chargeback_paid_entry_id;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.accounting.journal.show', $je->id) }}"
                               class="font-monospace fw-semibold text-decoration-none"
                               style="color:#b91c1c;font-size:.82rem">
                                {{ $je->entry_number }}
                            </a>
                        </td>
                        <td style="font-size:.82rem;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($je->entry_date)->format('d M Y') }}
                        </td>
                        <td>
                            @if($je->insured_name)
                                <div style="font-weight:600;font-size:.84rem">{{ $je->insured_name }}</div>
                            @endif
                            <div style="font-size:.75rem;color:var(--acct-muted);word-break:break-word;white-space:normal;">
                                {{ $je->description }}
                            </div>
                        </td>
                        <td class="text-end num-cell num-cr">
                            -${{ number_format($je->total_debit, 2) }}
                        </td>
                        <td style="font-size:.78rem;color:var(--acct-muted)">{{ $je->reference ?? '—' }}</td>
                        <td>
                            @if($cleared)
                                <span class="badge-cleared"><i class="bx bx-check-circle"></i> Recovered</span>
                            @else
                                <span class="badge-pending">Pending Recovery</span>
                            @endif
                        </td>
                        <td style="font-size:.78rem;color:var(--acct-muted)">{{ $je->creator?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.accounting.journal.show', $je->id) }}"
                               class="btn btn-sm" style="padding:3px 10px;font-size:.75rem;background:#f1f5f9;color:#374151;border:1px solid #e2e8f0;border-radius:5px;text-decoration:none">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 d-flex justify-content-center">
            {{ $entries->withQueryString()->links() }}
        </div>
        @else
        <div class="sl-empty">
            <i class="bx bx-undo"></i>
            <p style="font-size:.9rem;font-weight:600;color:#374151;margin-bottom:4px">No sales returns yet</p>
            <p style="font-size:.82rem">Sales returns are created automatically when a paid sale is marked as Chargeback.</p>
        </div>
        @endif
    </div>

</div>
@endsection
