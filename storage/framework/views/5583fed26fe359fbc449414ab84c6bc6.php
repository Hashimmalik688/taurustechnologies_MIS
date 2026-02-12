<?php $__env->startSection('title'); ?>
    Attendance Print View
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* Print-friendly styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            margin: 0;
            padding: 10mm;
        }
        
        .attendance-print-table {
            page-break-inside: auto;
        }
        
        .attendance-print-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .attendance-print-table thead {
            display: table-header-group;
        }
        
        .attendance-print-table tfoot {
            display: table-footer-group;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-body {
            padding: 0 !important;
        }
        
        /* Ensure table fits on page */
        .attendance-print-table {
            font-size: 9pt;
        }
        
        .attendance-print-table th,
        .attendance-print-table td {
            padding: 3px 2px !important;
        }
    }
    
    /* Screen styles */
    @media screen {
        .print-header {
            margin-bottom: 20px;
        }
    }
    
    /* Common styles */
    .attendance-print-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    
    .attendance-print-table th,
    .attendance-print-table td {
        border: 1px solid #000;
        padding: 6px 4px;
        text-align: center;
        font-size: 11px;
        vertical-align: middle;
    }
    
    .attendance-print-table thead th {
        background-color: #6f42c1;
        color: white;
        font-weight: 600;
        font-size: 10px;
    }
    
    .attendance-print-table tbody td {
        background-color: white;
    }
    
    .attendance-print-table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .attendance-print-table tbody tr:hover {
        background-color: #e9ecef;
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
        background-color: #f0f0f0 !important;
        color: #6c757d;
    }
    
    .totals-cell {
        font-weight: 700;
        background-color: #e9ecef !important;
    }
    
    .print-header {
        text-align: center;
        padding: 15px 0;
    }
    
    .print-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #333;
    }
    
    .print-header p {
        margin: 5px 0 0 0;
        font-size: 14px;
        color: #666;
    }
    
    .legend {
        margin: 15px 0;
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .legend-title {
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 13px;
    }
    
    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }
    
    .legend-box {
        width: 22px;
        height: 22px;
        border: 1px solid #000;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 10px;
    }
    
    @media print {
        .legend {
            page-break-inside: avoid;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Attendance
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Print View
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <!-- Filter Section (No Print) -->
            <div class="card no-print">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('attendance.print-view')); ?>" class="row g-3">
                        <div class="col-md-4">
                            <label for="month" class="form-label">
                                <i class="mdi mdi-calendar me-1"></i>
                                Select Month
                            </label>
                            <input type="month" class="form-control" id="month" name="month" 
                                   value="<?php echo e($month); ?>" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="department" class="form-label">
                                <i class="mdi mdi-office-building me-1"></i>
                                Department
                            </label>
                            <select class="form-select" id="department" name="department">
                                <option value="">All Departments</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept); ?>" <?php echo e($department == $dept ? 'selected' : ''); ?>>
                                        <?php echo e($dept); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="mdi mdi-filter me-1"></i>
                                Apply Filter
                            </button>
                            <button type="button" class="btn btn-success" onclick="window.print()">
                                <i class="mdi mdi-printer me-1"></i>
                                Print
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Print Content -->
            <div class="card">
                <div class="card-body">
                    <!-- Header -->
                    <div class="print-header">
                        <h3>Employee Attendance Record</h3>
                        <p><?php echo e($monthStart->format('F Y')); ?><?php echo e($department ? ' - ' . $department : ''); ?></p>
                        <p style="font-size: 12px; color: #999;">Printed: <?php echo e(now()->format('d M Y, h:i A')); ?></p>
                    </div>

                    <!-- Legend -->
                    <div class="legend no-print">
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

                    <!-- Attendance Table -->
                    <table class="attendance-print-table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 50px;">ID</th>
                                <th rowspan="2" style="width: 150px;">Name</th>
                                <th rowspan="2" style="width: 120px;">Position</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$department): ?>
                                    <th rowspan="2" style="width: 100px;">Department</th>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th colspan="<?php echo e($daysInMonth); ?>">Days of Month</th>
                                <th colspan="5">Totals</th>
                            </tr>
                            <tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                                    <th style="width: 20px;"><?php echo e($day); ?></th>
                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th style="width: 35px;">P</th>
                                <th style="width: 35px;">L</th>
                                <th style="width: 35px;">A</th>
                                <th style="width: 35px;">PL</th>
                                <th style="width: 35px;">H</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $employeeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($employee['id']); ?></td>
                                    <td class="text-left"><?php echo e($employee['name']); ?></td>
                                    <td class="text-left"><?php echo e($employee['position']); ?></td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$department): ?>
                                        <td class="text-left"><?php echo e($employee['department']); ?></td>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                                        <?php
                                            $status = $employee['daily_attendance'][$day] ?? '-';
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
                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    <td class="totals-cell"><?php echo e($employee['totals']['P']); ?></td>
                                    <td class="totals-cell"><?php echo e($employee['totals']['L']); ?></td>
                                    <td class="totals-cell"><?php echo e($employee['totals']['A']); ?></td>
                                    <td class="totals-cell"><?php echo e($employee['totals']['PL']); ?></td>
                                    <td class="totals-cell"><?php echo e($employee['totals']['H']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="<?php echo e($daysInMonth + ($department ? 8 : 9)); ?>" 
                                        style="text-align: center; padding: 20px;">
                                        No employee data available for this month.
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Summary Section (Print Only) -->
                    <div style="margin-top: 30px; display: none;" class="print-only">
                        <p style="font-size: 11px; color: #666;">
                            <strong>Note:</strong> This report includes all trackable roles. 
                            Weekends and public holidays are marked with (-).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .print-only {
                display: block !important;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    // Auto-print functionality (optional)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_print') === '1') {
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/attendance/print-view.blade.php ENDPATH**/ ?>