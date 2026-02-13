

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - <?php echo e($salaryRecord->user->name); ?> - <?php echo e($salaryRecord->month_name); ?> <?php echo e($salaryRecord->salary_year); ?>

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
        
        <div class="header">
            <div class="company-info">
                <div class="company-name"><?php echo e(config('app.name', 'Your Company Name')); ?></div>
                <div class="company-details">
                    123 Business Street, City, State 12345<br>
                    Phone: (555) 123-4567 | Email: hr@company.com<br>
                    www.yourcompany.com
                </div>
            </div>
        </div>

        
        <div class="payslip-title">
            SALARY SLIP - <?php echo e(strtoupper($salaryRecord->month_name)); ?> <?php echo e($salaryRecord->salary_year); ?>

        </div>

        
        <div class="employee-info">
            <table>
                <tr>
                    <td class="label">Employee Name:</td>
                    <td><?php echo e($salaryRecord->user->name); ?></td>
                    <td class="label">Employee ID:</td>
                    <td><?php echo e($salaryRecord->user->id); ?></td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td><?php echo e($salaryRecord->user->email); ?></td>
                    <td class="label">Department:</td>
                    <td><?php echo e($salaryRecord->user->department ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">Pay Period:</td>
                    <td><?php echo e($salaryRecord->month_name); ?> <?php echo e($salaryRecord->salary_year); ?></td>
                    <td class="label">Generated On:</td>
                    <td><?php echo e(now()->format('F d, Y')); ?></td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td><?php echo e(ucfirst($salaryRecord->status)); ?></td>
                    <td class="label">Payment Date:</td>
                    <td><?php echo e($salaryRecord->paid_at ? $salaryRecord->paid_at->format('F d, Y') : 'Pending'); ?></td>
                </tr>
            </table>
        </div>

        
        <div class="section-title">Performance Summary</div>
        <div class="summary-grid">
            
            <div class="summary-item">
                <div class="summary-number"><?php echo e($salaryRecord->working_days); ?></div>
                <div class="summary-label">Working Days</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-<?php echo e($salaryRecord->has_perfect_attendance ? 'success' : 'warning'); ?>">
                    <?php echo e($salaryRecord->present_days); ?>

                </div>
                <div class="summary-label">Present Days</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-<?php echo e($salaryRecord->leave_days > 0 ? 'danger' : 'success'); ?>">
                    <?php echo e($salaryRecord->leave_days); ?>

                </div>
                <div class="summary-label">Leave Days (Full)</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-<?php echo e(($salaryRecord->half_days ?? 0) > 0 ? 'warning' : 'success'); ?>">
                    <?php echo e($salaryRecord->half_days ?? 0); ?>

                </div>
                <div class="summary-label">Half Days</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-<?php echo e(($salaryRecord->late_days ?? 0) > 0 ? 'warning' : 'success'); ?>">
                    <?php echo e($salaryRecord->late_days ?? 0); ?>

                </div>
                <div class="summary-label">Late Days</div>
            </div>
            <div class="summary-item">                <div class="summary-number text-info">
                    <?php echo e($salaryRecord->attendance_percentage); ?>%
                </div>
                <div class="summary-label">Attendance</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-number"><?php echo e($salaryRecord->target_sales); ?></div>
                <div class="summary-label">Sales Target</div>
            </div>
            <div class="summary-item">
                <div
                    class="summary-number text-<?php echo e($salaryRecord->actual_sales >= $salaryRecord->target_sales ? 'success' : 'danger'); ?>">
                    <?php echo e($salaryRecord->actual_sales); ?>

                </div>
                <div class="summary-label">Actual Sales</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-<?php echo e($salaryRecord->extra_sales > 0 ? 'success' : 'info'); ?>">
                    <?php echo e($salaryRecord->extra_sales); ?>

                </div>
                <div class="summary-label">Extra Sales</div>
            </div>
            <div class="summary-item">
                <div class="summary-number text-success">
                    Rs<?php echo e(number_format($salaryRecord->total_bonus, 0)); ?>

                </div>
                <div class="summary-label">Sales Bonus</div>
            </div>
        </div>

        
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
                
                <tr>
                    <td><strong>BASIC EARNINGS</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Basic Salary</td>
                    <td class="description">Monthly base salary</td>
                    <td class="amount"><?php echo e(number_format($salaryRecord->basic_salary, 2)); ?></td>
                </tr>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->total_bonus > 0): ?>
                    <tr>
                        <td><strong>SALES PERFORMANCE</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Sales Bonus</td>
                        <td class="description"><?php echo e($salaryRecord->extra_sales); ?> extra sales ×
                            Rs<?php echo e(number_format($salaryRecord->bonus_per_extra_sale, 2)); ?></td>
                        <td class="amount text-success">Rs<?php echo e(number_format($salaryRecord->total_bonus, 2)); ?></td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->attendance_bonus > 0 || $salaryRecord->attendance_deduction < 0): ?>
                    <tr>
                        <td><strong>ATTENDANCE ADJUSTMENTS</strong></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->attendance_bonus > 0): ?>
                        <tr>
                            <td>Perfect Attendance Bonus</td>
                            <td class="description">No leaves this month</td>
                            <td class="amount text-success"><?php echo e(number_format($salaryRecord->attendance_bonus, 2)); ?>

                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->attendance_deduction < 0): ?>
                        <tr>
                            <td>Sandwich Rule Penalty</td>
                            <td class="description">
                                <?php echo e($salaryRecord->leave_days); ?> leave day(s)
                                <small>Daily rate: Rs<?php echo e(number_format($salaryRecord->daily_salary, 2)); ?></small>
                            </td>
                            <td class="amount text-danger">
                                Rs<?php echo e(number_format($salaryRecord->attendance_deduction, 2)); ?>

                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <tr style="background: #f8f9fa;">
                    <td><strong>GROSS SALARY</strong></td>
                    <td class="description">Basic + Bonuses + Adjustments</td>
                    <td class="amount"><strong><?php echo e(number_format($salaryRecord->gross_salary, 2)); ?></strong></td>
                </tr>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->deductions->count() > 0): ?>
                    <tr>
                        <td><strong>DEDUCTIONS</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $salaryRecord->deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(ucfirst(str_replace('_', ' ', $deduction->type))); ?></td>
                            <td class="description">
                                <?php echo e($deduction->description); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($deduction->is_percentage): ?>
                                    (<?php echo e($deduction->amount); ?>% of basic salary)
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="amount text-danger">-<?php echo e(number_format($deduction->calculated_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <tr style="background: #f8f9fa;">
                        <td><strong>TOTAL DEDUCTIONS</strong></td>
                        <td></td>
                        <td class="amount text-danger">
                            <strong>-<?php echo e(number_format($salaryRecord->total_deductions, 2)); ?></strong>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>

        
        <div class="net-salary">
            <div class="net-salary-label">NET SALARY</div>
            <div class="net-salary-amount">Rs<?php echo e(number_format($salaryRecord->net_salary, 2)); ?></div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($attendanceDetails) && $attendanceDetails->count() > 0): ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attendanceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($attendance->date->format('M d')); ?></td>
                            <td><?php echo e($attendance->date->format('D')); ?></td>
                            <td>
                                <span
                                    style="
                            padding: 2px 6px; 
                            border-radius: 3px; 
                            font-size: 10px; 
                            background: <?php echo e($attendance->status == 'present' ? '#28a745' : ($attendance->status == 'leave' ? '#dc3545' : '#ffc107')); ?>; 
                            color: white;
                        ">
                                    <?php echo e(strtoupper($attendance->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e($attendance->check_in ? $attendance->check_in->format('H:i') : '-'); ?></td>
                            <td><?php echo e($attendance->check_out ? $attendance->check_out->format('H:i') : '-'); ?></td>
                            <td><?php echo e($attendance->working_hours ?? '-'); ?>h</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($salesDetails) && $salesDetails->count() > 0): ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $salesDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A'); ?></td>
                            <td>
                                <?php echo e($sale->name ?? ($sale->company_name ?? 'Unknown Client')); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->email): ?>
                                    <br><small style="color: #666;"><?php echo e($sale->email); ?></small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="amount">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->deal_amount): ?>
                                    Rs<?php echo e(number_format($sale->deal_amount, 2)); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span
                                    style="background: #28a745; color: white; padding: 2px 8px; border-radius: 3px; font-size: 10px;">
                                    CLOSED
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <tr style="background: #f8f9fa;">
                        <td colspan="2"><strong>TOTAL SALES VALUE</strong></td>
                        <td class="amount"><strong>Rs<?php echo e(number_format($salesDetails->sum('deal_amount'), 2)); ?></strong>
                        </td>
                        <td class="text-center"><strong><?php echo e($salesDetails->count()); ?> deals</strong></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->notes): ?>
            <div class="section-title">Calculation Notes</div>
            <div
                style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 3px; margin: 20px 0;">
                <strong>System Notes:</strong><br>
                <?php echo e($salaryRecord->notes); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="signature-section">
            <div class="signature-left">
                <div style="margin-bottom: 50px;"><strong>Employee Signature:</strong></div>
                <div class="signature-line"></div>
                <div class="text-center"><?php echo e($salaryRecord->user->name); ?></div>
                <div class="text-center" style="font-size: 10px; color: #666;">Date: ________________</div>
            </div>
            <div class="signature-right">
                <div style="margin-bottom: 50px;"><strong>HR/Manager Signature:</strong></div>
                <div class="signature-line"></div>
                <div class="text-center">HR Department</div>
                <div class="text-center" style="font-size: 10px; color: #666;">Date: ________________</div>
            </div>
        </div>

        
        <div class="footer">
            <div style="text-align: center;">
                <strong>CONFIDENTIAL DOCUMENT</strong><br>
                This payslip is generated electronically and is valid without signature.<br>
                <strong>Attendance Policy:</strong> Perfect attendance (no leaves) = Rs5,000 bonus. Leave rule: 1
                leave = 1 day salary deduction.<br>
                For any queries regarding this payslip, please contact HR Department at hr@company.com<br>
                Generated on <?php echo e(now()->format('F d, Y \a\t g:i A')); ?>

            </div>
        </div>
    </div>
</body>

</html>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/salary/payslip.blade.php ENDPATH**/ ?>