<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Print - {{ \Carbon\Carbon::createFromFormat('!m', $month)->format('F') }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #000;
            background: white;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 26px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .company-subtext {
            font-size: 13px;
            color: #333;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .payment-due {
            font-size: 14px;
            font-weight: 600;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .report-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            font-size: 12px;
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .info-value {
            color: #000;
            font-weight: 600;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border: 1px solid #000;
        }

        th {
            background-color: #333;
            color: #fff;
            font-weight: bold;
            padding: 9px 6px;
            text-align: center;
            border: 1px solid #000;
            font-size: 12px;
            white-space: nowrap;
        }

        td {
            padding: 7px 6px;
            border: 1px solid #333;
            font-size: 12px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .number {
            text-align: right;
            font-family: Calibri, 'Courier New', monospace;
        }

        .employee-name {
            font-weight: 600;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .summary-section {
            margin-top: 25px;
            padding: 18px;
            background: #f5f5f5;
            border: 2px solid #000;
        }

        .summary-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .print-buttons {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .print-buttons button,
        .print-buttons a {
            padding: 10px 20px;
            background: #333;
            color: white;
            border: 1px solid #000;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .print-buttons button:hover,
        .print-buttons a:hover {
            background: #000;
        }

        .back-btn {
            background: #666 !important;
        }

        .back-btn:hover {
            background: #555 !important;
        }

        .no-print {
            print-break-after: avoid;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .print-buttons {
                display: none;
            }
            th {
                background-color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border: 1px solid #000;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-yes {
            background: #e6e6e6;
            color: #000;
        }

        .badge-no {
            background: #333;
            color: #fff;
        }

        tfoot tr {
            background: #e6e6e6 !important;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        tfoot td {
            font-weight: bold;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button onclick="window.print()">
            <i class="bx bx-printer"></i> Print This Page
        </button>
        <a href="{{ route('payroll.index') }}" class="back-btn">
            <i class="bx bx-arrow-back"></i> Back to Payroll
        </a>
    </div>

    <!-- HEADER SECTION -->
    <div class="header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="company-subtext">Payroll System</div>
        <div class="report-title">📅 Payroll Period: {{ $payrollPeriod }}</div>
        <div class="payment-due">💰 Payment Date: Salary paid 15 days after period ends (approx. {{ \Carbon\Carbon::create($year, $month, 25)->addDays(15)->format('F d, Y') }})</div>
    </div>

    <!-- REPORT INFO SECTION -->
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

    <!-- PAYROLL TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">Sr#</th>
                <th style="width: 100px;">Employee Name</th>
                <th style="width: 65px;">Join Date</th>
                <th style="width: 65px;">Basic Salary</th>
                <th style="width: 60px;">Per Day Wage</th>
                <th style="width: 60px;">Punctuality</th>
                <th style="width: 60px;">Total</th>
                <th style="width: 45px;">Full Days</th>
                <th style="width: 45px;">Half Days</th>
                <th style="width: 45px;">Late</th>
                <th style="width: 50px;">Qualified</th>
                <th style="width: 65px;">Dock Amount</th>
                <th style="width: 65px;">Other Deduct.</th>
                <th style="width: 65px;">Net Salary</th>
                <th style="width: 60px;">Advance</th>
                <th style="width: 65px;">Payable</th>
            </tr>
        </thead>
        <tbody>
        <tbody>
            @forelse($payrollData as $index => $data)
                <tr @if(isset($data['isManual']) && $data['isManual']) style="background-color: rgba(255, 193, 7, 0.1);" @endif>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="employee-name">
                        @if(isset($data['isManual']) && $data['isManual'])
                            {{ $data['employeeName'] }}
                            <span style="font-size: 10px; color: #856404; background: #fff3cd; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">MANUAL</span>
                        @else
                            {{ $data['employee']->name }}
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
                    <td class="number" style="font-weight: bold;">{{ number_format($data['netSalary'], 2) }}</td>
                    <td class="number">{{ number_format($data['advance'], 2) }}</td>
                    <td class="number" style="font-weight: bold;">{{ number_format($data['payable'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center" style="padding: 20px;">
                        <strong>No active employees found for the selected period</strong>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-center" style="text-align: right; padding: 8px 5px;"><strong>TOTALS:</strong></td>
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

    <!-- SUMMARY SECTION -->
    <div class="summary-section">
        <div class="summary-title">PAYROLL SUMMARY - {{ \Carbon\Carbon::createFromFormat('!m', $month)->format('F') }} {{ $year }}</div>
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

    <!-- FOOTER -->
    <div style="margin-top: 35px; text-align: center; border-top: 2px solid #000; padding-top: 12px; color: #333; font-size: 11px;">
        <p><strong>This is a system-generated document. Please verify all figures before processing.</strong></p>
        <p style="margin-top: 8px;">Generated: {{ now()->format('F d, Y H:i:s') }}</p>
        <p style="margin-top: 5px; font-style: italic;">Taurus Technologies - Management Information System</p>
    </div>
</body>
</html>
