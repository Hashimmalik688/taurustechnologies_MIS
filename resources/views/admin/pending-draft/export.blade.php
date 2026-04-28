<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Draft Report — {{ $tabLabel }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; background: #fff; padding: 24px; }

    /* ── Cover header ── */
    .rpt-header { border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 18px; }
    .rpt-company { font-size: 17px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    .rpt-title   { font-size: 14px; font-weight: bold; margin-top: 4px; }
    .rpt-meta    { font-size: 10px; color: #555; margin-top: 6px; display: flex; gap: 24px; flex-wrap: wrap; }
    .rpt-meta span b { color: #111; }

    /* ── Print button (screen only) ── */
    .print-bar { margin-bottom: 14px; }
    .print-bar button {
        padding: 6px 18px; font-size: 11px; font-weight: bold; cursor: pointer;
        background: #111; color: #fff; border: none; border-radius: 3px; letter-spacing: .3px;
    }
    .print-bar button:hover { background: #333; }
    @media print { .print-bar { display: none; } }

    /* ── Main table ── */
    table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    thead th {
        background: #111; color: #fff; padding: 6px 8px;
        text-align: left; font-size: 10px; text-transform: uppercase;
        letter-spacing: .5px; white-space: nowrap;
    }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
    tbody tr:nth-child(even) td { background: #f8f8f8; }
    tbody tr:last-child td { border-bottom: 2px solid #111; }
    .num  { text-align: right; }
    .ctr  { text-align: center; }
    .mono { font-family: monospace; font-size: 10px; }
    .st   { font-weight: bold; font-size: 10px; }

    /* ── State summary ── */
    .summary-section { margin-top: 28px; page-break-inside: avoid; }
    .summary-title { font-size: 12px; font-weight: bold; border-bottom: 1px solid #111; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: .5px; }
    .summary-table { width: auto; min-width: 260px; border-collapse: collapse; }
    .summary-table th { background: #111; color: #fff; padding: 5px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; }
    .summary-table td { padding: 5px 12px; border-bottom: 1px solid #ddd; }
    .summary-table tr:nth-child(even) td { background: #f8f8f8; }
    .summary-total td { font-weight: bold; border-top: 2px solid #111; border-bottom: none !important; }

    /* ── Footer ── */
    .rpt-footer { margin-top: 32px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 9px; color: #888; display: flex; justify-content: space-between; }
    @media print {
        body { padding: 12px; }
        .rpt-footer { position: fixed; bottom: 0; left: 12px; right: 12px; }
    }
</style>
</head>
<body>

<div class="print-bar">
    <button onclick="window.print()">&#128438; Print / Save as PDF</button>
</div>

{{-- ── Report Header ── --}}
<div class="rpt-header">
    <div class="rpt-company">Taurus Technologies — MIS</div>
    <div class="rpt-title">Pending Draft Report &mdash; {{ $tabLabel }}</div>
    <div class="rpt-meta">
        <span><b>Period:</b>
            {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M j, Y') : 'All' }}
            &ndash;
            {{ $dateTo   ? \Carbon\Carbon::parse($dateTo)->format('M j, Y')   : 'All' }}
        </span>
        @if($search)<span><b>Search:</b> {{ $search }}</span>@endif
        <span><b>Total Records:</b> {{ $leads->count() }}</span>
        <span><b>Exported:</b> {{ $exportedAt }}</span>
        <span><b>Exported By:</b> {{ auth()->user()->name ?? '—' }}</span>
    </div>
</div>

{{-- ── Main Table ── --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Client Name</th>
            <th>Phone</th>
            <th>State</th>
            <th>Closer</th>
            <th>Carrier</th>
            <th>Policy #</th>
            <th>Partner</th>
            <th class="num">Premium / Mo</th>
            <th>Sale Date</th>
            <th>Followup Done</th>
            @if($tab === 'not_paid')
            <th>FDFP Status</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($leads as $i => $lead)
        <tr>
            <td class="num" style="color:#888;">{{ $i + 1 }}</td>
            <td><strong>{{ $lead->cn_name ?? '—' }}</strong></td>
            <td class="mono">{{ $lead->phone_number ?? '—' }}</td>
            <td class="st ctr">{{ strtoupper(trim($lead->state ?? '')) ?: '—' }}</td>
            <td>{{ $lead->closer_name ?? '—' }}</td>
            <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—') }}</td>
            <td class="mono">{{ $lead->policy_number ?? '—' }}</td>
            <td>{{ $lead->partner->name ?? '—' }}</td>
            <td class="num">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M j, Y') : '—' }}</td>
            <td>{{ $lead->followup_done_at ? $lead->followup_done_at->format('M j, Y') : '—' }}</td>
            @if($tab === 'not_paid')
            <td>{{ $fdfpTypes[$lead->not_paid_fdfp_type] ?? $lead->not_paid_fdfp_type ?? '—' }}</td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="{{ $tab === 'not_paid' ? 12 : 11 }}" style="text-align:center;padding:20px;color:#888;">
                No records found for the selected period.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ── State Summary ── --}}
@if($leads->isNotEmpty())
<div class="summary-section">
    <div class="summary-title">Sales by State</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>State</th>
                <th style="text-align:right;">Sales Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stateCounts as $state => $count)
            <tr>
                <td><strong>{{ $state }}</strong></td>
                <td style="text-align:right;">{{ $count }}</td>
            </tr>
            @endforeach
            <tr class="summary-total">
                <td>TOTAL</td>
                <td style="text-align:right;">{{ $leads->count() }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<div class="rpt-footer">
    <span>Taurus Technologies MIS &mdash; Confidential</span>
    <span>Generated {{ $exportedAt }}</span>
</div>

</body>
</html>
