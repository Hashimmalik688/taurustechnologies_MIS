{{-- File: resources/views/admin/salary/payslip.blade.php --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $salaryRecord->user->name }} - {{ $salaryRecord->month_name }} {{ $salaryRecord->salary_year }}
    </title>
    <style>
        @page {
            margin: 20px;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
        }

        .header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }

        .payslip-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            background: #f8f9fa;
            padding: 12px;
            border: 1px solid #dee2e6;
            margin: 20px 0;
        }

        .employee-info {
            width: 100%;
            margin-bottom: 25px;
        }

        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .employee-info .label {
            font-weight: bold;
            background: #f8f9fa;
            width: 30%;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            padding: 8px 12px;
            background: #e9ecef;
            border-left: 4px solid #007bff;
        }

        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }

        .breakdown-table th {
            background: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .breakdown-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .breakdown-table .description {
            color: #666;
        }

        .net-salary {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
            border-radius: 5px;
        }

        .net-salary-label {
            font-size: 16px;
            font-weight: bold;
            color: #155724;
            margin-bottom: 10px;
        }

        .net-salary-amount {
            font-size: 28px;
            font-weight: bold;
            color: #155724;
        }

        .summary-grid {
            display: table;
            width: 100%;
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin: 20px 0;
            border-radius: 3px;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #dee2e6;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-info {
            color: #17a2b8;
        }

        .text-warning {
            color: #ffc107;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ config('app.name', 'Your Company Name') }}</div>
                <div class="company-details">
                    123 Business Street, City, State 12345<br>
                    Phone: (555) 123-4567 | Email: hr@company.com<br>
                    www.yourcompany.com
                </div>
            </div>
        </div>

        {{-- Payslip Title --}}
        <div class="payslip-title">
            SALARY SLIP - {{ strtoupper($salaryRecord->month_name) }} {{ $salaryRecord->salary_year }}
        </div>

        {{-- Employee Information --}}
        <div class="employee-info">
            <table>
                <tr>
                    <td class="label">Employee Name:</td>
                    <td>{{ $salaryRecord->user->name }}</td>
                    <td class="label">Employee ID:</td>
                    <td>{{ $salaryRecord->user->id }}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>{{ $salaryRecord->user->email }}</td>
                    <td class="label">Department:</td>
                    <td>{{ $salaryRecord->user->department ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Pay Period:</td>
                    <td>{{ $salaryRecord->month_name }} {{ $salaryRecord->salary_year }}</td>
                    <td class="label">Generated On:</td>
                    <td>{{ now()->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td>{{ ucfirst($salaryRecord->status) }}</td>
                    <td class="label">Payment Date:</td>
                    <td>{{ $salaryRecord->paid_at ? $salaryRecord->paid_at->format('F d, Y') : 'Pending' }}</td>
                </tr>
            </table>
        </div>

        {{-- Combined Summary --}}
        <div class="section-title">Performance Summary</div>
        <div class="summary-grid">
            {{-- Attendance Summary --}}
            <div class="summary-item">
                <div class="summary-number">{{ $salaryRecord->working_days }}</div>
                <div class="summary-label">Working Days</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-{{ $salaryRecord->has_perfect_attendance ? 'success' : 'warning' }}">
                    {{ $salaryRecord->present_days }}
                </div>
                <div class="summary-label">Present Days</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-{{ $salaryRecord->leave_days > 0 ? 'danger' : 'success' }}">
                    {{ $salaryRecord->leave_days }}
                </div>
                <div class="summary-label">Leave Days</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-info">
                    {{ $salaryRecord->attendance_percentage }}%
                </div>
                <div class="summary-label">Attendance</div>
            </div>
            {{-- Sales Summary --}}
            <div class="summary-item">
                <div class="summary-number">{{ $salaryRecord->target_sales }}</div>
                <div class="summary-label">Sales Target</div>
            </div>
            <div class="summary-item">
                <div
                    class="summary-number text-{{ $salaryRecord->actual_sales >= $salaryRecord->target_sales ? 'success' : 'danger' }}">
                    {{ $salaryRecord->actual_sales }}
                </div>
                <div class="summary-label">Actual Sales</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-{{ $salaryRecord->extra_sales > 0 ? 'success' : 'info' }}">
                    {{ $salaryRecord->extra_sales }}
                </div>
                <div class="summary-label">Extra Sales</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-success">
                    Rs{{ number_format($salaryRecord->total_bonus, 0) }}
                </div>
                <div class="summary-label">Sales Bonus</div>
            </div>
        </div>

        {{-- Salary Breakdown --}}
        <div class="section-title">Salary Breakdown</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 20%;">Details</th>
                    <th style="width: 30%;">Amount (Rs)</th>
                </tr>
            </thead>
            <tbody>
                {{-- Basic Earnings --}}
                <tr>
                    <td><strong>BASIC EARNINGS</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Basic Salary</td>
                    <td class="description">Monthly base salary</td>
                    <td class="amount">{{ number_format($salaryRecord->basic_salary, 2) }}</td>
                </tr>

                {{-- Sales Performance --}}
                @if ($salaryRecord->total_bonus > 0)
                    <tr>
                        <td><strong>SALES PERFORMANCE</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Sales Bonus</td>
                        <td class="description">{{ $salaryRecord->extra_sales }} extra sales ×
                            Rs{{ number_format($salaryRecord->bonus_per_extra_sale, 2) }}</td>
                        <td class="amount text-success">Rs{{ number_format($salaryRecord->total_bonus, 2) }}</td>
                    </tr>
                @endif

                {{-- Attendance Adjustments --}}
                @if ($salaryRecord->attendance_bonus > 0 || $salaryRecord->attendance_deduction < 0)
                    <tr>
                        <td><strong>ATTENDANCE ADJUSTMENTS</strong></td>
                        <td></td>
                        <td></td>
                    </tr>

                    @if ($salaryRecord->attendance_bonus > 0)
                        <tr>
                            <td>Perfect Attendance Bonus</td>
                            <td class="description">No leaves this month</td>
                            <td class="amount text-success">{{ number_format($salaryRecord->attendance_bonus, 2) }}
                            </td>
                        </tr>
                    @endif

                    @if ($salaryRecord->attendance_deduction < 0)
                        <tr>
                            <td>Sandwich Rule Penalty</td>
                            <td class="description">
                                {{ $salaryRecord->leave_days }} leave day(s)
                                <small>Daily rate: Rs{{ number_format($salaryRecord->daily_salary, 2) }}</small>
                            </td>
                            <td class="amount text-danger">
                                Rs{{ number_format($salaryRecord->attendance_deduction, 2) }}
                            </td>
                        </tr>
                    @endif
                @endif

                {{-- Gross Salary --}}
                <tr style="background: #f8f9fa;">
                    <td><strong>GROSS SALARY</strong></td>
                    <td class="description">Basic + Bonuses + Adjustments</td>
                    <td class="amount"><strong>{{ number_format($salaryRecord->gross_salary, 2) }}</strong></td>
                </tr>

                {{-- Manual Deductions --}}
                @if ($salaryRecord->deductions->count() > 0)
                    <tr>
                        <td><strong>DEDUCTIONS</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @foreach ($salaryRecord->deductions as $deduction)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $deduction->type)) }}</td>
                            <td class="description">
                                {{ $deduction->description }}
                                @if ($deduction->is_percentage)
                                    ({{ $deduction->amount }}% of basic salary)
                                @endif
                            </td>
                            <td class="amount text-danger">-{{ number_format($deduction->calculated_amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background: #f8f9fa;">
                        <td><strong>TOTAL DEDUCTIONS</strong></td>
                        <td></td>
                        <td class="amount text-danger">
                            <strong>-{{ number_format($salaryRecord->total_deductions, 2) }}</strong>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Net Salary --}}
        <div class="net-salary">
            <div class="net-salary-label">NET SALARY</div>
            <div class="net-salary-amount">Rs{{ number_format($salaryRecord->net_salary, 2) }}</div>
        </div>

        {{-- Attendance Details --}}
        @if (isset($attendanceDetails) && $attendanceDetails->count() > 0)
            <div class="section-title">Attendance Details</div>
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 15%;">Day</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 20%;">Check In</th>
                        <th style="width: 20%;">Check Out</th>
                        <th style="width: 15%;">Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendanceDetails as $attendance)
                        <tr>
                            <td>{{ $attendance->date->format('M d') }}</td>
                            <td>{{ $attendance->date->format('D') }}</td>
                            <td>
                                <span
                                    style="
                            padding: 2px 6px; 
                            border-radius: 3px; 
                            font-size: 10px; 
                            background: {{ $attendance->status == 'present' ? '#28a745' : ($attendance->status == 'leave' ? '#dc3545' : '#ffc107') }}; 
                            color: white;
                        ">
                                    {{ strtoupper($attendance->status) }}
                                </span>
                            </td>
                            <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->working_hours ?? '-' }}h</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Sales Details --}}
        @if (isset($salesDetails) && $salesDetails->count() > 0)
            <div class="section-title">Sales Details</div>
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Date</th>
                        <th style="width: 40%;">Client/Lead</th>
                        <th style="width: 20%;">Deal Amount</th>
                        <th style="width: 20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salesDetails as $sale)
                        <tr>
                            <td>{{ $sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                {{ $sale->name ?? ($sale->company_name ?? 'Unknown Client') }}
                                @if ($sale->email)
                                    <br><small style="color: #666;">{{ $sale->email }}</small>
                                @endif
                            </td>
                            <td class="amount">
                                @if ($sale->deal_amount)
                                    Rs{{ number_format($sale->deal_amount, 2) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-center">
                                <span
                                    style="background: #28a745; color: white; padding: 2px 8px; border-radius: 3px; font-size: 10px;">
                                    CLOSED
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: #f8f9fa;">
                        <td colspan="2"><strong>TOTAL SALES VALUE</strong></td>
                        <td class="amount"><strong>Rs{{ number_format($salesDetails->sum('deal_amount'), 2) }}</strong>
                        </td>
                        <td class="text-center"><strong>{{ $salesDetails->count() }} deals</strong></td>
                    </tr>
                </tbody>
            </table>
        @endif

        {{-- Summary Notes --}}
        @if ($salaryRecord->notes)
            <div class="section-title">Calculation Notes</div>
            <div
                style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 3px; margin: 20px 0;">
                <strong>System Notes:</strong><br>
                {{ $salaryRecord->notes }}
            </div>
        @endif

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-left">
                <div style="margin-bottom: 50px;"><strong>Employee Signature:</strong></div>
                <div class="signature-line"></div>
                <div class="text-center">{{ $salaryRecord->user->name }}</div>
                <div class="text-center" style="font-size: 10px; color: #666;">Date: ________________</div>
            </div>
            <div class="signature-right">
                <div style="margin-bottom: 50px;"><strong>HR/Manager Signature:</strong></div>
                <div class="signature-line"></div>
                <div class="text-center">HR Department</div>
                <div class="text-center" style="font-size: 10px; color: #666;">Date: ________________</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div style="text-align: center;">
                <strong>CONFIDENTIAL DOCUMENT</strong><br>
                This payslip is generated electronically and is valid without signature.<br>
                <strong>Attendance Policy:</strong> Perfect attendance (no leaves) = Rs5,000 bonus. Leave rule: 1
                leave = 1 day salary deduction.<br>
                For any queries regarding this payslip, please contact HR Department at hr@company.com<br>
                Generated on {{ now()->format('F d, Y \a\t g:i A') }}
            </div>
        </div>
    </div>
</body>

</html>
