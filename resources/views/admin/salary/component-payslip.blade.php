<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $component->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #d4af37;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 15px 0 5px 0;
        }
        .employee-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .info-group {
            margin-bottom: 10px;
        }
        .info-label {
            font-size: 11px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }
        .salary-period {
            font-size: 14px;
            color: #666;
            margin: 10px 0;
        }
        .component-type {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            margin: 10px 0;
        }
        .component-type.basic {
            background: #dbeafe;
            color: #0d47a1;
        }
        .component-type.bonus {
            background: #dbeafe;
            color: #1b5e20;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background: #f0f0f0;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 2px solid #d4af37;
            color: #333;
        }
        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 12px;
        }
        .table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .amount {
            font-weight: 600;
            min-width: 80px;
        }
        .deduction {
            color: #d32f2f;
        }
        .subtotal-row td {
            border-top: 2px solid #d4af37;
            border-bottom: 2px solid #d4af37;
            padding: 12px;
            font-weight: 600;
        }
        .total-row td {
            background: #e8e8e8;
            padding: 14px 12px;
            font-size: 14px;
            font-weight: 700;
            border: 2px solid #333;
        }
        .net-amount {
            color: #1b5e20;
            font-size: 16px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #333;
            margin: 20px 0 10px 0;
            border-left: 4px solid #d4af37;
            padding-left: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .signature-section {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 30px;
            font-size: 11px;
            font-weight: 600;
        }
        .payment-method {
            margin: 20px 0;
            padding: 15px;
            background: #fffbf0;
            border-left: 4px solid #d4af37;
            font-size: 12px;
        }
        .payment-method strong {
            display: block;
            margin-bottom: 5px;
        }
        .highlight-row td {
            background: #fffbf0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $company ?? 'TAURUS CRM' }}</div>
            <div class="document-title">PAYSLIP</div>
        </div>

        <!-- Employee Information -->
        <div class="employee-info">
            <div>
                <div class="info-group">
                    <div class="info-label">Employee Name</div>
                    <div class="info-value">{{ $component->user->name }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $component->user->email }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Salary Period</div>
                    <div class="salary-period">
                        {{ $component->month_name }} {{ $component->salary_year }}
                    </div>
                </div>
            </div>
            <div>
                <div class="info-group">
                    <div class="info-label">Component Type</div>
                    <div>
                        <span class="component-type {{ $component->component_type }}">
                            {{ ucfirst($component->component_type) }} Salary
                        </span>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Payment Date</div>
                    <div class="info-value">{{ $component->payment_date->format('d M Y') }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Generated On</div>
                    <div class="info-value">{{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Financial Details -->
        @if($component->component_type === 'basic')
            <!-- BASIC SALARY BREAKDOWN -->
            <div class="section-title">Salary Composition</div>
            <table class="table">
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right amount">Rs {{ number_format($component->basic_salary, 2) }}</td>
                </tr>
                @if($component->attendance_bonus > 0)
                <tr class="highlight-row">
                    <td>Attendance / Punctuality Bonus</td>
                    <td class="text-right amount" style="color: #1b5e20;">+ Rs {{ number_format($component->attendance_bonus, 2) }}</td>
                </tr>
                @endif
            </table>

            <div class="section-title">Attendance Details</div>
            <table class="table">
                <tr>
                    <td>Working Days</td>
                    <td class="text-right">{{ $component->working_days ?? 22 }}</td>
                </tr>
                <tr>
                    <td>Days Present</td>
                    <td class="text-right">{{ $component->present_days }}</td>
                </tr>
                <tr>
                    <td>Leave Days</td>
                    <td class="text-right">{{ $component->leave_days }}</td>
                </tr>                <tr>
                    <td>Half Days:</td>
                    <td class="text-right">{{ $component->half_days ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Late Days:</td>
                    <td class="text-right">{{ $component->late_days ?? 0 }}</td>
                </tr>                <tr>
                    <td>Half Days</td>
                    <td class="text-right">{{ $component->half_days ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Late Arrivals</td>
                    <td class="text-right">{{ $component->late_days }}</td>
                </tr>
                <tr>
                    <td>Daily Salary Rate</td>
                    <td class="text-right amount">Rs {{ number_format($component->daily_salary, 2) }}</td>
                </tr>
            </table>

            @if($component->attendance_deduction < 0 || $component->dock_deductions > 0)
            <div class="section-title">Deductions</div>
            <table class="table">
                @if($component->attendance_deduction < 0)
                <tr>
                    <td>Attendance Deduction</td>
                    <td class="text-right amount deduction">- Rs {{ number_format(abs($component->attendance_deduction), 2) }}</td>
                </tr>
                @endif
                @if($component->dock_deductions > 0)
                <tr>
                    <td>Dock Deductions</td>
                    <td class="text-right amount deduction">- Rs {{ number_format($component->dock_deductions, 2) }}</td>
                </tr>
                @endif
            </table>
            @endif

        @else
            <!-- BONUS SALARY BREAKDOWN -->
            @if($component->actual_sales !== null)
            <div class="section-title">Sales Performance</div>
            <table class="table">
                <tr>
                    <td>Target Sales</td>
                    <td class="text-right">{{ $component->target_sales }} units</td>
                </tr>
                <tr>
                    <td>Actual Sales</td>
                    <td class="text-right">{{ $component->actual_sales }} units</td>
                </tr>
                <tr>
                    <td>Chargebacks</td>
                    <td class="text-right">{{ $component->chargeback_count }} units</td>
                </tr>
                <tr>
                    <td>Net Approved Sales</td>
                    <td class="text-right amount">{{ $component->net_approved_sales }} units</td>
                </tr>
                @if($component->net_approved_sales >= $component->target_sales)
                <tr class="highlight-row">
                    <td>Extra Sales (Above Target)</td>
                    <td class="text-right">{{ $component->extra_sales }} units</td>
                </tr>
                <tr class="highlight-row">
                    <td>Bonus Per Extra Sale</td>
                    <td class="text-right amount">Rs {{ number_format($component->bonus_per_extra_sale, 2) }}</td>
                </tr>
                <tr class="highlight-row">
                    <td><strong>Bonus Amount</strong></td>
                    <td class="text-right amount" style="color: #1b5e20;"><strong>Rs {{ number_format($component->calculated_amount, 2) }}</strong></td>
                </tr>
                @else
                <tr class="highlight-row" style="background: #ffebee;">
                    <td><strong>Status</strong></td>
                    <td class="text-right"><strong style="color: #d32f2f;">Below Target - No Bonus</strong></td>
                </tr>
                @endif
            </table>
            @endif
        @endif

        <!-- Final Amount -->
        <table class="table" style="margin-top: 30px;">
            <tr class="subtotal-row">
                <td>Calculated Amount</td>
                <td class="text-right">Rs {{ number_format($component->calculated_amount, 2) }}</td>
            </tr>
            @if($component->deductions > 0)
            <tr class="subtotal-row">
                <td>Total Deductions</td>
                <td class="text-right deduction">- Rs {{ number_format($component->deductions, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>NET AMOUNT PAYABLE</td>
                <td class="text-right net-amount">Rs {{ number_format($component->net_amount, 2) }}</td>
            </tr>
        </table>

        <!-- Notes -->
        @if($component->notes)
        <div class="payment-method">
            <strong>Remarks:</strong>
            {{ $component->notes }}
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Employee Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">HR / Manager Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an electronically generated payslip. No signature is required.</p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }} | Payslip ID: {{ $component->id }}</p>
        </div>
    </div>
</body>
</html>
