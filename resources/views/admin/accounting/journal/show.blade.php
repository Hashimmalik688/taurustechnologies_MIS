@extends('layouts.master')

@section('title', $entry->entry_number . ' — Journal Voucher')

@section('css')
<style>
:root {
    --acct-gold:       #d4af37;
    --acct-gold-dark:  #b8941f;
    --acct-gold-light: #f5ecd0;
    --acct-dark:       #1a1a1a;
    --acct-header-bg:  #2d2d2d;
}
.voucher-wrap { max-width: 900px; margin: 0 auto; }

/* Voucher document card */
.voucher-doc {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
}

/* Document header */
.voucher-doc-header {
    background: var(--acct-header-bg);
    border-bottom: 3px solid var(--acct-gold);
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.vdh-left .vdh-label {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--acct-gold);
    margin-bottom: 2px;
}
.vdh-left .vdh-num {
    font-size: 1.25rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    color: #fff;
    line-height: 1.2;
}
.vdh-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Meta strip */
.voucher-meta-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
@media (max-width: 640px) { .voucher-meta-strip { grid-template-columns: repeat(2, 1fr); } }
.vm-cell {
    padding: 9px 14px;
    border-right: 1px solid #dee2e6;
    font-size: .82rem;
}
.vm-cell:last-child { border-right: none; }
.vm-label { font-size: .68rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: #aaa; margin-bottom: 2px; }
.vm-value { font-weight: 600; color: #1a1a2e; }

/* Lines table */
.voucher-table-wrapper { overflow-x: auto; }
.voucher-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .855rem;
}
.voucher-table thead th {
    background: #2d2d2d;
    color: #ccc;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    padding: 8px 12px;
    border: none;
    white-space: nowrap;
}
.voucher-table thead th:nth-child(6),
.voucher-table thead th:nth-child(7) { border-left: 1px solid #444; text-align: right; }
.voucher-table tbody tr { border-bottom: 1px solid #f1f3f5; }
.voucher-table tbody tr:hover { background: #fffef5; }
.voucher-table tbody td { padding: 7px 12px; vertical-align: middle; }
.voucher-table tbody tr:nth-child(even) td { background: #fafafa; }
.voucher-table tbody td.col-dr,
.voucher-table tbody td.col-cr {
    font-family: 'Courier New', monospace;
    font-size: .9rem;
    font-weight: 700;
    text-align: right;
    border-left: 1px solid #f1f3f5;
}
.voucher-table tbody td.col-dr .amt { color: #2e7d32; }
.voucher-table tbody td.col-cr .amt { color: #c62828; }
.acc-code {
    font-family: 'Courier New', monospace;
    font-size: .78rem;
    color: var(--acct-gold-dark);
    font-weight: 600;
    margin-right: 4px;
}
.narration-row td {
    background: #fffbea !important;
    font-style: italic;
    font-size: .8rem;
    color: #666;
    border-top: 1px dashed #f0e68c !important;
}
.voucher-table tfoot td {
    background: #f1f3f5;
    font-weight: 700;
    padding: 8px 12px;
    border-top: 2px solid var(--acct-gold);
    font-family: 'Courier New', monospace;
    font-size: .9rem;
}
.voucher-table tfoot td.col-dr { color: #2e7d32; text-align: right; border-left: 1px solid #dee2e6; }
.voucher-table tfoot td.col-cr { color: #c62828; text-align: right; border-left: 1px solid #dee2e6; }
.voucher-table tfoot td.balanced { font-size: .72rem; font-family: sans-serif; color: #888; }

/* Type badges */
.acct-type-badge { display:inline-block; font-size:.7rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; padding:2px 8px; border-radius:3px; }
.acct-badge-sale    { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.acct-badge-payment { background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; }
.acct-badge-opening { background:#fff8e1; color:#f57f17; border:1px solid #ffe082; }
.acct-badge-general { background:#f3e5f5; color:#6a1b9a; border:1px solid #ce93d8; }
.acct-badge-chargeback { background:#fce4ec; color:#b71c1c; border:1px solid #ef9a9a; }

.btn-acct-print {
    font-size: .8rem;
    font-weight: 600;
    padding: 5px 14px;
    background: var(--acct-gold);
    color: #1a1a1a;
    border: none;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none;
    transition: background .15s;
}
.btn-acct-print:hover { background: var(--acct-gold-dark); color: #fff; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="voucher-wrap">

        {{-- Toolbar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.accounting.journal.index') }}"
               class="btn btn-sm btn-outline-secondary" style="font-size:.8rem;">
                <i class="bx bx-arrow-back me-1"></i> Back to Journal
            </a>
            <a href="{{ route('admin.accounting.journal.print', $entry->id) }}"
               target="_blank" class="btn-acct-print">
                <i class="bx bx-printer"></i> Print / PDF
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 style="border-left:4px solid #198754;border-radius:4px;font-size:.875rem;">
                <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="voucher-doc">

            {{-- Document header --}}
            @php
                $typeMap = [
                    'sale'             => ['label' => 'Sale Entry',          'cls' => 'acct-badge-sale'],
                    'payment_received' => ['label' => 'Payment Received',    'cls' => 'acct-badge-payment'],
                    'opening_balance'  => ['label' => 'Opening Balance',     'cls' => 'acct-badge-opening'],
                    'chargeback'       => ['label' => 'ChargeBack',          'cls' => 'acct-badge-chargeback'],
                    'general'          => ['label' => 'General Journal',     'cls' => 'acct-badge-general'],
                ];
                $badge = $typeMap[$entry->type] ?? ['label' => $entry->type_label, 'cls' => 'acct-badge-general'];
            @endphp
            <div class="voucher-doc-header">
                <div class="vdh-left">
                    <div class="vdh-label">Journal Voucher</div>
                    <div class="vdh-num">{{ $entry->entry_number }}</div>
                </div>
                <div class="vdh-right">
                    <span class="acct-type-badge {{ $badge['cls'] }}">{{ $badge['label'] }}</span>
                    @if($entry->is_posted)
                        <span style="font-size:.7rem;background:rgba(25,135,84,.2);color:#81c784;border:1px solid #81c784;padding:2px 8px;border-radius:3px;font-weight:700;letter-spacing:.05em;">
                            ✓ POSTED
                        </span>
                    @endif
                </div>
            </div>

            {{-- Meta strip --}}
            <div class="voucher-meta-strip">
                <div class="vm-cell">
                    <div class="vm-label">Date</div>
                    <div class="vm-value">{{ $entry->entry_date->format('d M Y') }}</div>
                </div>
                @if($entry->insured_name)
                <div class="vm-cell">
                    <div class="vm-label">Insured Name</div>
                    <div class="vm-value" style="font-weight:600;">{{ $entry->insured_name }}</div>
                </div>
                @endif
                <div class="vm-cell">
                    <div class="vm-label">Reference</div>
                    <div class="vm-value" style="font-family:'Courier New',monospace;font-size:.82rem;">
                        {{ $entry->reference ?: '—' }}
                    </div>
                </div>
                <div class="vm-cell">
                    <div class="vm-label">{{ $entry->type === 'chargeback' ? 'ChargeBack Amount' : 'Our Share Amount' }}</div>
                    <div class="vm-value" style="font-family:'Courier New',monospace;color:var(--acct-gold-dark);">
                        ${{ number_format($entry->total_debit, 2) }}
                    </div>
                </div>
                @if($entry->gross_amount)
                <div class="vm-cell">
                    <div class="vm-label">{{ $entry->type === 'chargeback' ? 'Gross ChargeBack Amount' : 'Gross Sale Amount' }}</div>
                    <div class="vm-value" style="font-family:'Courier New',monospace;color:#6c757d;">
                        ${{ number_format($entry->gross_amount, 2) }}
                    </div>
                </div>
                @endif
                @if($entry->our_share_percentage)
                <div class="vm-cell">
                    <div class="vm-label">Our Share %</div>
                    <div class="vm-value" style="color:#6c757d;">
                        {{ rtrim(rtrim(number_format($entry->our_share_percentage, 4), '0'), '.') }}%
                    </div>
                </div>
                @endif
                <div class="vm-cell">
                    <div class="vm-label">Lines</div>
                    <div class="vm-value">{{ $entry->lines->count() }}</div>
                </div>
                <div class="vm-cell">
                    <div class="vm-label">Posted By</div>
                    <div class="vm-value" style="font-size:.8rem;">{{ $entry->creator?->name ?? '—' }}</div>
                </div>
            </div>

            {{-- Lines table --}}
            <div class="voucher-table-wrapper">
                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th style="width:28px">#</th>
                            <th style="width:180px">Account</th>
                            <th>Partner</th>
                            <th style="width:130px">Carrier</th>
                            <th>Narration</th>
                            <th style="width:130px">Debit (USD)</th>
                            <th style="width:130px">Credit (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entry->lines as $line)
                        <tr>
                            <td class="text-center" style="color:#bbb;font-size:.78rem;">{{ $loop->iteration }}</td>
                            <td>
                                <span class="acc-code">{{ $line->account->account_code }}</span>
                                <span style="font-size:.84rem;color:#2d2d2d;">{{ $line->account->account_name }}</span>
                            </td>
                            <td style="font-size:.83rem;color:#495057;">{{ $line->partner?->name ?? '—' }}</td>
                            <td style="font-size:.83rem;color:#6c757d;">{{ $line->carrier?->name ?? '—' }}</td>
                            <td style="font-size:.83rem;color:#6c757d;">{{ $line->description ?: $entry->description }}</td>
                            <td class="col-dr">
                                @if($line->debit > 0)
                                    <span class="amt">{{ number_format($line->debit, 2) }}</span>
                                @else
                                    <span style="color:#ddd;">—</span>
                                @endif
                            </td>
                            <td class="col-cr">
                                @if($line->credit > 0)
                                    <span class="amt">{{ number_format($line->credit, 2) }}</span>
                                @else
                                    <span style="color:#ddd;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="narration-row">
                            <td></td>
                            <td colspan="4">
                                <span style="color:#aaa;font-size:.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;margin-right:6px;">Being:</span>
                                {{ $entry->description }}
                            </td>
                            <td></td><td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        @php
                            $totalDr = $entry->lines->sum('debit');
                            $totalCr = $entry->lines->sum('credit');
                            $balanced = abs($totalDr - $totalCr) < 0.001;
                        @endphp
                        <tr>
                            <td colspan="5" class="text-end" style="font-family:sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#6c757d;">
                                Totals
                                @if($balanced)
                                    <span class="balanced ms-2" style="color:#2e7d32;font-size:.7rem;">
                                        ✓ Balanced
                                    </span>
                                @else
                                    <span class="balanced ms-2" style="color:#c62828;font-size:.7rem;">
                                        ✗ Unbalanced
                                    </span>
                                @endif
                            </td>
                            <td class="col-dr">{{ number_format($totalDr, 2) }}</td>
                            <td class="col-cr">{{ number_format($totalCr, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>{{-- /.voucher-doc --}}
    </div>{{-- /.voucher-wrap --}}
</div>
@endsection
