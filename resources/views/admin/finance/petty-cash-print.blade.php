<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petty Cash Ledger - {{ date('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            color: var(--bs-surface-700);
            background: white;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid var(--bs-surface-700);
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: var(--bs-surface-700);
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--bs-surface-500);
            margin-bottom: 20px;
        }
        
        .report-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            font-size: 13px;
            margin-bottom: 20px;
            background: var(--bs-surface-50);
            padding: 15px;
            border-radius: 4px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .info-value {
            flex: 1;
            text-align: right;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead th {
            background: var(--bs-surface-200);
            border: 1px solid var(--bs-surface-muted);
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: var(--bs-surface-700);
        }
        
        tbody td {
            border: 1px solid var(--bs-surface-200);
            padding: 10px 12px;
            font-size: 12px;
        }
        
        tbody tr:nth-child(even) {
            background: var(--bs-surface-50);
        }
        
        tbody tr:hover {
            background: var(--bs-print-bg-alt);
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .gl-no {
            font-weight: bold;
            text-align: center;
            width: 80px;
        }
        
        .description {
            text-transform: uppercase;
            font-weight: 500;
        }
        
        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            width: 120px;
        }
        
        .balance {
            text-align: right;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            width: 120px;
            background: var(--bs-print-bg-alt);
        }
        
        .totals-row {
            background: var(--bs-surface-200) !important;
            font-weight: bold;
        }
        
        .totals-row td {
            border-top: 2px solid var(--bs-surface-700);
            border-bottom: 2px solid var(--bs-surface-700);
            padding: 12px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: var(--bs-surface-500);
            border-top: 1px solid var(--bs-surface-200);
            padding-top: 15px;
        }
        
        @media print {
            body {
                padding: 0;
                background: white;
            }
            
            .no-print {
                display: none;
            }
            
            page {
                margin: 0;
                padding: 0;
            }
        }
        
        .print-buttons {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .print-buttons button,
        .print-buttons a {
            padding: 8px 15px;
            margin-left: 10px;
            border: 1px solid var(--bs-surface-200);
            background: white;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .print-buttons button:hover,
        .print-buttons a:hover {
            background: var(--bs-print-bg-alt);
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button onclick="window.print()">
            <i class="bx bx-printer"></i> Print
        </button>
        <a href="{{ route('petty-cash.export', request()->query()) }}">
            <i class="bx bx-download"></i> Download CSV
        </a>
        <button onclick="window.close()">Close</button>
    </div>
    
    <!-- HEADER SECTION -->
    <div class="header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="report-title">PETTY CASH LEDGER</div>
    </div>
    
    <!-- REPORT INFO SECTION -->
    <div class="report-info">
        <div class="info-item">
            <span class="info-label">Report Type:</span>
            <span class="info-value">Petty Cash Ledger</span>
        </div>
        <div class="info-item">
            <span class="info-label">User:</span>
            <span class="info-value">{{ Auth::user()->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Date Range:</span>
            <span class="info-value">
                @if($fromDate && $toDate)
                    {{ date('M d, Y', strtotime($fromDate)) }} - {{ date('M d, Y', strtotime($toDate)) }}
                @else
                    All Records
                @endif
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Report Date:</span>
            <span class="info-value">{{ date('m-d-Y') }}</span>
        </div>
        @if($selectedHead)
            <div class="info-item">
                <span class="info-label">Category:</span>
                <span class="info-value">{{ $selectedHead }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Category Total:</span>
                <span class="info-value">{{ number_format($categoryTotal, 2) }}</span>
            </div>
        @endif
    </div>
    
    <!-- DATA TABLE SECTION -->
    <table>
        <thead>
            <tr>
                <th class="gl-no">G/L No.</th>
                <th class="u-w-100">Date</th>
                <th>Head</th>
                <th>Description</th>
                <th class="amount">Debit</th>
                <th class="amount">Credit</th>
                <th class="balance">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries->sortBy('date') as $entry)
                <tr>
                    <td class="gl-no">{{ $entry->serial_number }}</td>
                    <td>{{ $entry->date->format('M d, Y') }}</td>
                    <td>{{ $entry->head }}</td>
                    <td class="description">{{ $entry->description }}</td>
                    <td class="amount">
                        @if($entry->debit > 0)
                            {{ number_format($entry->debit, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="amount">
                        @if($entry->credit > 0)
                            {{ number_format($entry->credit, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="balance">{{ number_format($balanceMap[$entry->id] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-4">No entries found</td>
                </tr>
            @endforelse
            
            @if($entries->count() > 0)
                <tr class="totals-row">
 <td class="text-end" colspan="4" >TOTALS</td>
                    <td class="amount">{{ number_format($entries->sum('debit'), 2) }}</td>
                    <td class="amount">{{ number_format($entries->sum('credit'), 2) }}</td>
                    <td class="balance">
                        @php
                            $lastEntry = $entries->last();
                            $finalBalance = $balanceMap[$lastEntry->id] ?? 0;
                        @endphp
                        {{ number_format($finalBalance, 2) }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <!-- FOOTER SECTION -->
    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>Printed on {{ date('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
