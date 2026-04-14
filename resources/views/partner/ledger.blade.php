@extends('layouts.partner')

@section('title') Ledger @endsection

@section('css')
<style>
:root {
    --pd-indigo: #4f46e5;
    --pd-green:  #059669;
    --pd-br:     .6rem;
    --pd-sh:     0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
}

/* Page header */
.pl-hdr{margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem;}
.pl-hdr-left h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
.pl-hdr-left p{font-size:.84rem;color:#6b7280;margin:0;}

/* Balance pill */
.pl-balance{
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.5rem 1rem;border-radius:.45rem;border:1px solid;
    font-size:.86rem;font-weight:700;white-space:nowrap;
}
.pl-balance.owe{background:#fef2f2;color:#7f1d1d;border-color:#fecaca;}
.pl-balance.credit{background:#f0fdf4;color:#14532d;border-color:#bbf7d0;}
.pl-balance.zero{background:#f9fafb;color:#6b7280;border-color:#e5e7eb;}
.pl-balance i{font-size:1.05rem;}
.pl-balance .amt{font-weight:900;}

/* Filter form */
.pd-filter-form{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;padding:.55rem .85rem;background:#f8fafc;border:1px solid rgba(0,0,0,.07);border-radius:.45rem;margin-bottom:1rem;}
.pd-filter-label{font-size:.66rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#9ca3af;}
.pd-filter-input{background:#fff;border:1px solid rgba(0,0,0,.12);border-radius:.3rem;padding:.3rem .55rem;font-size:.82rem;color:#374151;}
.pd-filter-btn{background:rgba(79,70,229,.08);border:1px solid rgba(79,70,229,.2);color:#4f46e5;padding:.3rem .7rem;border-radius:.3rem;font-size:.8rem;font-weight:700;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:.25rem;}
.pd-filter-btn:hover{background:rgba(79,70,229,.16);}
.pd-filter-btn-reset{background:rgba(220,38,38,.07);border-color:rgba(220,38,38,.2);color:#dc2626;}
.pd-filter-btn-reset:hover{background:rgba(220,38,38,.14);}

/* Card */
.pd-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);box-shadow:var(--pd-sh);overflow:hidden;}
.pd-head{padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.06);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.pd-head h6{font-size:.88rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.35rem;color:#111827;}
.pd-head h6 i{color:var(--pd-indigo);}
.pd-count{background:rgba(79,70,229,.08);color:var(--pd-indigo);font-size:.7rem;font-weight:700;padding:.12rem .45rem;border-radius:.2rem;}

/* Table */
.pd-table{width:100%;border-collapse:collapse;font-size:.83rem;}
.pd-table thead th{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;border-bottom:1px solid rgba(0,0,0,.08);padding:.55rem .85rem;background:#f9fafb;white-space:nowrap;position:sticky;top:0;z-index:1;}
.pd-table tbody td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#374151;}
.pd-table tfoot td{padding:.6rem .85rem;font-weight:700;border-top:2px solid rgba(0,0,0,.1);background:#f0f0f5;font-size:.82rem;}
.pd-table tbody tr:hover{background:rgba(79,70,229,.022);}
.pd-table tbody tr:last-child td{border-bottom:none;}

/* Type chips */
.tc{font-size:.62rem;font-weight:800;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;letter-spacing:.3px;text-transform:uppercase;}
.tc-sale{background:#d1fae5;color:#065f46;}
.tc-pay{background:#dbeafe;color:#1e3a8a;}
.tc-cb{background:#fef3c7;color:#78350f;}
.tc-other{background:#f3f4f6;color:#374151;}

/* Balance cols */
.col-dr{color:#4f46e5;font-weight:700;}
.col-cr{color:#059669;font-weight:700;}
.col-dim{color:#d1d5db;}
.rb-pos{color:#dc2626;font-weight:700;}
.rb-neg{color:#059669;font-weight:700;}
.rb-zero{color:#9ca3af;}

/* Empty */
.pd-empty{text-align:center;padding:3rem 1rem;}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.5rem;opacity:.2;color:#9ca3af;}
.pd-empty p{font-size:.84rem;color:#9ca3af;margin:0;}

/* Note */
.pl-note{padding:.5rem .85rem;font-size:.73rem;color:#92400e;background:#fffbeb;border-bottom:1px solid rgba(245,158,11,.2);display:flex;align-items:center;gap:.4rem;}
.pl-note i{flex-shrink:0;}

/* Row count bar */
.pl-count-bar{padding:.4rem .85rem;font-size:.7rem;color:#9ca3af;background:#fafafa;border-bottom:1px solid rgba(0,0,0,.04);}

/* Dark themes */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table thead th{background:var(--bg-secondary,#16162a);color:var(--text-muted,#888);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody td{color:var(--text-primary,#ddd);border-color:var(--border-color,rgba(255,255,255,.04));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tfoot td{background:var(--bg-secondary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-filter-form{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-filter-input{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1);color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-hdr-left h4{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-balance.owe{background:rgba(220,38,38,.12);border-color:rgba(220,38,38,.25);color:#fca5a5;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-balance.credit{background:rgba(5,150,105,.12);border-color:rgba(5,150,105,.25);color:#6ee7b7;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-note{background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.2);color:#fde68a;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-count-bar{background:var(--bg-secondary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.04));}
</style>
@endsection

@section('content')

<div class="pl-hdr">
    <div class="pl-hdr-left">
        <h4><i class="bx bx-receipt" style="color:#4f46e5;margin-right:.35rem;"></i>Ledger</h4>
        <p>Complete transaction history — all debits, credits, and running balance.</p>
    </div>
    @php
        $balClass = $currentBalance > 0 ? 'owe' : ($currentBalance < 0 ? 'credit' : 'zero');
        $balIcon  = $currentBalance > 0 ? 'bx-error-circle' : ($currentBalance < 0 ? 'bx-check-shield' : 'bx-check');
        $balLabel = $currentBalance > 0 ? 'Owed to Taurus' : ($currentBalance < 0 ? 'Credit Balance' : 'Balanced');
    @endphp
    <div class="pl-balance {{ $balClass }}">
        <i class="bx {{ $balIcon }}"></i>
        <span>{{ $balLabel }}: <span class="amt">
            {{ $currentBalance != 0 ? ($currentBalance > 0 ? '' : '−') . '$' . number_format(abs($currentBalance), 2) : '$0.00' }}
        </span></span>
    </div>
</div>

{{-- Date range filter --}}
<form method="GET" action="{{ route('partner.ledger') }}" class="pd-filter-form">
    @if($carrierId)<input type="hidden" name="carrier_id" value="{{ $carrierId }}">@endif
    <span class="pd-filter-label"><i class="bx bx-calendar"></i> Date Range</span>
    <input type="date" name="date_from" class="pd-filter-input" style="width:135px;" value="{{ $dateFrom }}" placeholder="From">
    <span style="color:#9ca3af;font-size:.8rem;">→</span>
    <input type="date" name="date_to"   class="pd-filter-input" style="width:135px;" value="{{ $dateTo }}" placeholder="To">
    <button type="submit" class="pd-filter-btn"><i class="bx bx-filter-alt"></i> Apply</button>
    @if($dateFrom || $dateTo)
    <a href="{{ route('partner.ledger', $carrierId ? ['carrier_id' => $carrierId] : []) }}" class="pd-filter-btn pd-filter-btn-reset" style="text-decoration:none;"><i class="bx bx-reset"></i> All time</a>
    @endif
    @if(!$dateFrom && !$dateTo)
    <span style="font-size:.7rem;color:#9ca3af;margin-left:.25rem;">Showing all entries</span>
    @endif
</form>

{{-- Carrier filter pills --}}
@include('partner.partials.carrier-filter')

{{-- Ledger table --}}
<div class="pd-card">
    <div class="pd-head">
        <h6><i class="bx bx-history"></i> Transaction History</h6>
        <div style="display:flex;align-items:center;gap:.5rem;">
            @if($carrierId)
            <span style="font-size:.72rem;color:#6b7280;font-style:italic;">Filtered: {{ $activeCarriers->where('id', $carrierId)->first()['name'] ?? 'Carrier' }}</span>
            @endif
            <span class="pd-count">{{ $ledgerEntries->count() }}</span>
        </div>
    </div>

    @if($ledgerEntries->count() > 0)
    <div class="pl-note">
        <i class="bx bx-info-circle"></i>
        Running balance reflects your AR account with Taurus.
        <strong>Positive = you owe Taurus</strong> &nbsp;·&nbsp;
        <strong>Negative = Taurus owes you</strong> &nbsp;·&nbsp;
        Chargebacks are shared losses and do not affect this balance.
    </div>
    <div class="pl-count-bar">{{ $ledgerEntries->count() }} {{ $ledgerEntries->count() == 1 ? 'entry' : 'entries' }}
        @if($dateFrom || $dateTo) · {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : 'All' }} → {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : 'Present' }}@endif
    </div>
    <div style="overflow-x:auto;max-height:75vh;overflow-y:auto;">
        <table class="pd-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Carrier</th>
                    <th>Reference / Note</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th class="text-end">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledgerEntries as $txn)
                @php
                    $tk  = strtolower(str_replace([' ','_','-'], '', $txn['type'] ?? ''));
                    $tc  = match(true) {
                        str_contains($tk, 'sale') && !str_contains($tk, 'return') => 'tc-sale',
                        str_contains($tk, 'payment') => 'tc-pay',
                        str_contains($tk, 'chargeback') || str_contains($tk, 'return') => 'tc-cb',
                        default => 'tc-other',
                    };
                    $rb  = $txn['running_balance'];
                    $rbCls = $rb > 0 ? 'rb-pos' : ($rb < 0 ? 'rb-neg' : 'rb-zero');
                    $rbPfx = $rb > 0 ? '+' : ($rb < 0 ? '−' : '');
                @endphp
                <tr>
                    <td style="white-space:nowrap;color:#6b7280;font-size:.78rem;">{{ \Carbon\Carbon::parse($txn['date'])->format('M d, Y') }}</td>
                    <td><span class="tc {{ $tc }}">{{ str_replace('_', ' ', $txn['type'] ?? '—') }}</span></td>
                    <td style="font-size:.8rem;color:#6b7280;">{{ $txn['carrier'] ?? '—' }}</td>
                    <td style="font-size:.78rem;color:#6b7280;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        @if($txn['reference']) <span style="font-weight:600;color:#374151;">{{ $txn['reference'] }}</span> @endif
                        @if($txn['reference'] && $txn['description']) &nbsp;·&nbsp; @endif
                        {{ \Illuminate\Support\Str::limit($txn['description'] ?? '', 35) }}
                    </td>
                    <td class="text-end {{ ($txn['debit'] ?? 0) > 0 ? 'col-dr' : 'col-dim' }}">{{ ($txn['debit'] ?? 0) > 0 ? '$' . number_format($txn['debit'], 2) : '—' }}</td>
                    <td class="text-end {{ ($txn['credit'] ?? 0) > 0 ? 'col-cr' : 'col-dim' }}">{{ ($txn['credit'] ?? 0) > 0 ? '$' . number_format($txn['credit'], 2) : '—' }}</td>
                    <td class="text-end">
                        <span class="{{ $rbCls }}">{{ $rbPfx }}${{ number_format(abs($rb), 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php $finalBal = $ledgerEntries->last()['running_balance'] ?? 0; @endphp
                <tr>
                    <td colspan="4">Closing Balance</td>
                    <td class="text-end">
                        ${{ number_format($ledgerEntries->sum('debit'), 2) }}
                    </td>
                    <td class="text-end">
                        ${{ number_format($ledgerEntries->sum('credit'), 2) }}
                    </td>
                    <td class="text-end">
                        <span class="{{ $finalBal > 0 ? 'rb-pos' : ($finalBal < 0 ? 'rb-neg' : 'rb-zero') }}">
                            {{ $finalBal > 0 ? '+' : ($finalBal < 0 ? '−' : '') }}${{ number_format(abs($finalBal), 2) }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="pd-empty">
        <i class="bx bx-receipt"></i>
        <p>No ledger entries found{{ ($dateFrom || $dateTo || $carrierId) ? ' for the selected filters.' : ' yet.' }}</p>
        @if($dateFrom || $dateTo || $carrierId)
        <p style="margin-top:.5rem;">
            <a href="{{ route('partner.ledger') }}" style="color:#4f46e5;text-decoration:none;font-size:.82rem;font-weight:700;">
                <i class="bx bx-reset"></i> Clear all filters
            </a>
        </p>
        @endif
    </div>
    @endif
</div>

@endsection
