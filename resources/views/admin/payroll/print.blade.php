<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Print - {{ \Carbon\Carbon::createFromFormat('!m', $month)->format('F') }} {{ $year }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Calibri,'Segoe UI',Arial,sans-serif;font-size:13px;line-height:1.5;color:#1a1a2e;background:#fff;padding:20px}

        .print-buttons{text-align:center;margin-bottom:20px;display:flex;gap:10px;justify-content:center}
        .print-buttons button,.print-buttons a{
            padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;
            text-decoration:none;display:inline-flex;align-items:center;gap:6px;color:#fff
        }
        .print-buttons .btn-print{background:linear-gradient(135deg,#b8860b,#d4a843)}
        .print-buttons .btn-back{background:#6b7280}
        .print-buttons button:hover,.print-buttons a:hover{opacity:.9}

        .header{text-align:center;margin-bottom:22px;border-bottom:3px solid #1a1a2e;padding-bottom:14px}
        .company-name{font-size:24px;font-weight:800;color:#1a1a2e;margin-bottom:4px;letter-spacing:1.5px}
        .company-subtext{font-size:12px;color:#555;margin-bottom:8px}
        .report-title{font-size:15px;font-weight:600;color:#555;margin-bottom:4px}
        .payment-due{font-size:13px;font-weight:600;color:#059669;margin-bottom:0}

        .report-info{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;font-size:12px;margin-bottom:18px;background:#f8f9fa;padding:12px;border:1px solid #dee2e6;border-radius:6px}
        .info-item{display:flex;flex-direction:column}
        .info-label{font-weight:700;color:#555;margin-bottom:2px;font-size:11px}
        .info-value{color:#1a1a2e;font-weight:600;font-size:13px}

        table{width:100%;border-collapse:collapse;margin-bottom:22px;border:1px solid #1a1a2e}
        th{background:#2d2d3f;color:#fff;font-weight:700;padding:8px 5px;text-align:center;border:1px solid #1a1a2e;font-size:11px;white-space:nowrap}
        td{padding:7px 5px;border:1px solid #adb5bd;font-size:12px}
        tr:nth-child(even){background:#f8f9fa}
        .number{text-align:right;font-family:Calibri,'Courier New',monospace}
        .employee-name{font-weight:600;text-align:left}
        .text-center{text-align:center}
        .text-end{text-align:right}

        .summary-section{margin-top:22px;padding:16px;background:#f8f9fa;border:2px solid #1a1a2e;border-radius:6px}
        .summary-title{font-size:16px;font-weight:800;color:#1a1a2e;margin-bottom:14px;border-bottom:2px solid #555;padding-bottom:6px}
        .summary-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
        .summary-item{display:flex;flex-direction:column}
        .summary-label{font-size:12px;font-weight:700;color:#555;margin-bottom:4px}
        .summary-value{font-size:16px;font-weight:800;color:#1a1a2e}

        .badge{display:inline-block;padding:2px 6px;border:1px solid #1a1a2e;border-radius:3px;font-size:10px;font-weight:700}
        .badge-yes{background:#d4edda;color:#155724}
        .badge-no{background:#2d2d3f;color:#fff}
        .manual-tag{font-size:10px;background:#fffbeb;color:#b8860b;padding:2px 6px;border-radius:3px;margin-left:4px;font-weight:600}

        tfoot tr{background:#e9ecef !important;font-weight:700;border-top:3px solid #1a1a2e}
        tfoot td{font-weight:700;border:1px solid #1a1a2e}

        .footer{text-align:center;font-size:11px;color:#888;margin-top:30px;border-top:2px solid #1a1a2e;padding-top:12px}

        @media print{
            .no-print{display:none !important}
            body{padding:0}
            th{background:#2d2d3f !important;color:#fff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            tr:nth-child(even){background:#f8f9fa !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            tfoot tr{background:#e9ecef !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .badge-no{background:#2d2d3f !important;color:#fff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .badge-yes{background:#d4edda !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print This Page</button>
        <a href="{{ route('payroll.index') }}" class="btn-back">← Back to Payroll</a>
    </div>

    <div class="header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="company-subtext">Payroll System</div>
        <div class="report-title">📅 Payroll Period: {{ $payrollPeriod }}</div>
        <div class="payment-due">💰 Payment Date: Salary paid 15 days after period ends (approx. {{ \Carbon\Carbon::create($year, $month, 25)->addDays(15)->format('F d, Y') }})</div>
    </div>

    <div class="report-info">
        <div class="info-item">
            <span class="info-label">Report Type:</span>
            <span class="info-value">Payroll Summary</span>
        </div>
        <div class="info-item">
            <span class="info-label">Generated By:</span>
            <span class="info-value">{{ Auth::user()->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Total Employees:</span>
            <span class="info-value">{{ count($payrollData) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:25px">Sr#</th>
                <th>Employee Name</th>
                <th>Join Date</th>
                <th>Basic Salary</th>
                <th>Per Day Wage</th>
                <th>Punctuality</th>
                <th>Total</th>
                <th style="width:42px">Full</th>
                <th style="width:42px">Half</th>
                <th style="width:42px">Late</th>
                <th style="width:48px">Qual.</th>
                <th>Dock</th>
                <th>Deduct.</th>
                <th>Net Salary</th>
                <th>Advance</th>
                <th>Payable</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrollData as $index => $data)
                <tr @if(isset($data['isManual']) && $data['isManual']) style="background-color:rgba(184,134,11,.06) !important" @endif>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="employee-name">
                        @if(isset($data['isManual']) && $data['isManual'])
                            {{ $data['employeeName'] }}
                            <span class="manual-tag">MANUAL</span>
                        @else
                            {{ $data['employee']->name }}
                            @if(!empty($data['isTerminated']))
                                <span style="font-size:10px;background:#fde8e8;color:#dc2626;padding:2px 6px;border-radius:3px;margin-left:4px;font-weight:600;border:1px solid #fca5a5">TERMINATED</span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">{{ $data['joinDate'] }}</td>
                    <td class="number">{{ number_format($data['basicSalary'], 2) }}</td>
                    <td class="number">{{ number_format($data['perDayWage'], 2) }}</td>
                    <td class="number">{{ number_format($data['punctualityBonus'], 2) }}</td>
                    <td class="number">{{ number_format($data['total'], 2) }}</td>
                    <td class="text-center">{{ $data['fullDays'] }}</td>
                    <td class="text-center">{{ $data['halfDays'] }}</td>
                    <td class="text-center">{{ $data['lateDays'] }}</td>
                    <td class="text-center">
                        <span class="badge {{ $data['isQualified'] ? 'badge-yes' : 'badge-no' }}">
                            {{ $data['isQualified'] ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="number">{{ number_format($data['dockAmount'], 2) }}</td>
                    <td class="number">{{ number_format($data['otherDeductions'], 2) }}</td>
                    <td class="number" style="font-weight:700">{{ number_format($data['netSalary'], 2) }}</td>
                    <td class="number">{{ number_format($data['advance'], 2) }}</td>
                    <td class="number" style="font-weight:700">{{ number_format($data['payable'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center" style="padding:20px">
                        <strong>No active employees found for the selected period</strong>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end" style="padding:8px 5px"><strong>TOTALS:</strong></td>
                <td class="number"><strong>{{ number_format($totalBasicSalary, 2) }}</strong></td>
                <td class="text-center">-</td>
                <td class="number"><strong>{{ number_format($totalPunctuality, 2) }}</strong></td>
                <td class="number"><strong>{{ number_format($totalTotal ?? ($totalBasicSalary + $totalPunctuality), 2) }}</strong></td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="number"><strong>{{ number_format($totalDock, 2) }}</strong></td>
                <td class="number"><strong>{{ number_format($totalDeductions - $totalDock, 2) }}</strong></td>
                <td class="number"><strong>{{ number_format($totalNetSalary, 2) }}</strong></td>
                <td class="number"><strong>{{ number_format($totalAdvance ?? 0, 2) }}</strong></td>
                <td class="number"><strong>{{ number_format($totalPayable ?? $totalNetSalary, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-section">
        <div class="summary-title">PAYROLL SUMMARY — {{ \Carbon\Carbon::createFromFormat('!m', $month)->format('F') }} {{ $year }}</div>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total Employees</span>
                <span class="summary-value">{{ count($payrollData) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Basic Salary</span>
                <span class="summary-value">Rs {{ number_format($totalBasicSalary, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Punctuality Bonus</span>
                <span class="summary-value">Rs {{ number_format($totalPunctuality, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Dock Amount</span>
                <span class="summary-value">Rs {{ number_format($totalDock, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Deductions</span>
                <span class="summary-value">Rs {{ number_format($totalDeductions, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Net Salary</span>
                <span class="summary-value">Rs {{ number_format($totalNetSalary, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Advance</span>
                <span class="summary-value">Rs {{ number_format($totalAdvance ?? 0, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Payable</span>
                <span class="summary-value">Rs {{ number_format($totalPayable ?? $totalNetSalary, 0) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Employees Qualified</span>
                <span class="summary-value">{{ $qualifiedForBonus }} of {{ count($payrollData) }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>This is a system-generated document. Please verify all figures before processing.</strong></p>
        <p style="margin-top:6px">Generated: {{ now()->format('F d, Y h:i A') }}</p>
        <p style="margin-top:4px;font-style:italic">Taurus Technologies — Management Information System</p>
    </div>
</body>
</html>
