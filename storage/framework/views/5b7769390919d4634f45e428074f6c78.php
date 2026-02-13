<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Print - <?php echo e($periodStart->format('d M Y')); ?> to <?php echo e($periodEnd->format('d M Y')); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: white;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .company-subtext {
            font-size: 12px;
            color: #333;
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }

        .report-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            font-size: 11px;
            margin-bottom: 15px;
            background: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .info-value {
            color: #000;
            font-weight: 600;
            font-size: 12px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        th {
            background-color: #333;
            color: #fff;
            font-weight: bold;
            padding: 6px 3px;
            text-align: center;
            border: 1px solid #000;
            font-size: 10px;
            white-space: nowrap;
        }

        td {
            padding: 5px 3px;
            border: 1px solid #333;
            font-size: 10px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-left {
            text-align: left !important;
        }

        /* Status color coding */
        .status-p {
            background-color: #d4edda !important;
            color: #155724;
            font-weight: 600;
        }

        .status-l {
            background-color: #fff3cd !important;
            color: #856404;
            font-weight: 600;
        }

        .status-a {
            background-color: #f8d7da !important;
            color: #721c24;
            font-weight: 600;
        }

        .status-pl {
            background-color: #d1ecf1 !important;
            color: #0c5460;
            font-weight: 600;
        }

        .status-h {
            background-color: #e2e3e5 !important;
            color: #383d41;
            font-weight: 600;
        }

        .status-weekend {
            background-color: #dc3545 !important;
            color: #fff;
            font-weight: 600;
        }

        .terminated-row {
            background-color: #f5f5f5 !important;
            opacity: 0.85;
        }

        .terminated-badge {
            color: #921c1c;
            font-weight: 600;
            font-size: 9px;
            margin-left: 3px;
        }

        .weekend-header {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .totals-cell {
            font-weight: 700;
            background-color: #e9ecef !important;
        }

        .legend {
            margin: 12px 0;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .legend-title {
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border: 1px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 10px;
            }
            .print-buttons {
                display: none;
            }
            th {
                background-color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .weekend-header {
                background-color: #dc3545 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .status-p, .status-l, .status-a, .status-pl, .status-h, .status-weekend {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button onclick="window.print()">
            🖨️ Print This Page
        </button>
        <a href="<?php echo e(route('attendance.index')); ?>" class="back-btn">
            ← Back to Attendance
        </a>
    </div>

    <!-- HEADER SECTION -->
    <div class="header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="company-subtext">Employee Attendance System</div>
        <div class="report-title">📅 Attendance Record: <?php echo e($periodStart->format('d M Y')); ?> - <?php echo e($periodEnd->format('d M Y')); ?></div>
    </div>

    <!-- REPORT INFO SECTION -->
    <div class="report-info">
        <div class="info-item">
            <span class="info-label">Report Type:</span>
            <span class="info-value">Attendance Report</span>
        </div>
        <div class="info-item">
            <span class="info-label">Generated By:</span>
            <span class="info-value"><?php echo e(Auth::user()->name); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Date:</span>
            <span class="info-value"><?php echo e(now()->format('d M Y, h:i A')); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Total Employees:</span>
            <span class="info-value"><?php echo e(count($employeeData)); ?></span>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-title">Status Legend:</div>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-box status-p">P</span>
                <span>Present</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-l">L</span>
                <span>Late Arrival</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-a">A</span>
                <span>Absent</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-pl">PL</span>
                <span>Paid Leave</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-h">H</span>
                <span>Half Day</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-weekend">-</span>
                <span>Weekend/Holiday</span>
            </div>
        </div>
    </div>

    <!-- ATTENDANCE TABLE -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">S.No</th>
                <th rowspan="2" style="width: 40px;">ID</th>
                <th rowspan="2" style="width: 140px;">Name</th>
                <th rowspan="2" style="width: 110px;">Position</th>
                <th colspan="<?php echo e(count($dates)); ?>">Dates</th>
                <th colspan="5">Totals</th>
            </tr>
            <tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isWeekend = in_array($date->dayOfWeek, [0, 6]);
                    ?>
                    <th style="width: 18px;" class="<?php echo e($isWeekend ? 'weekend-header' : ''); ?>" title="<?php echo e($date->format('D, M d')); ?>">
                        <?php echo e($date->format('d')); ?>

                    </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <th style="width: 30px;">P</th>
                <th style="width: 30px;">L</th>
                <th style="width: 30px;">A</th>
                <th style="width: 30px;">PL</th>
                <th style="width: 30px;">H</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $employeeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e($employee['is_terminated'] ? 'terminated-row' : ''); ?>">
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($employee['id']); ?></td>
                    <td class="text-left">
                        <?php echo e($employee['name']); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($employee['is_terminated']): ?>
                            <span class="terminated-badge">(Terminated)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="text-left"><?php echo e($employee['position']); ?></td>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $dateKey = $date->format('Y-m-d');
                            $status = $employee['daily_attendance'][$dateKey] ?? '-';
                            $statusClass = match($status) {
                                'P' => 'status-p',
                                'L' => 'status-l',
                                'A' => 'status-a',
                                'PL' => 'status-pl',
                                'H' => 'status-h',
                                default => 'status-weekend'
                            };
                        ?>
                        <td class="<?php echo e($statusClass); ?>"><?php echo e($status); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <td class="totals-cell"><?php echo e($employee['totals']['P']); ?></td>
                    <td class="totals-cell"><?php echo e($employee['totals']['L']); ?></td>
                    <td class="totals-cell"><?php echo e($employee['totals']['A']); ?></td>
                    <td class="totals-cell"><?php echo e($employee['totals']['PL']); ?></td>
                    <td class="totals-cell"><?php echo e($employee['totals']['H']); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e(count($dates) + 9); ?>" style="text-align: center; padding: 20px;">
                        No employee data available for this period.
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <!-- Summary Section -->
    <div style="margin-top: 20px; font-size: 10px; color: #666;">
        <strong>Note:</strong> This report includes all trackable employees. Weekends (Saturday & Sunday) and public holidays are marked with (-) and highlighted in red.
    </div>
</body>
</html>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/attendance/print.blade.php ENDPATH**/ ?>