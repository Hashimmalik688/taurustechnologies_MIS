<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petty Cash Ledger - <?php echo e(date('Y-m-d')); ?></title>
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
        <a href="<?php echo e(route('petty-cash.export', request()->query())); ?>">
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
            <span class="info-value"><?php echo e(Auth::user()->name); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Date Range:</span>
            <span class="info-value">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fromDate && $toDate): ?>
                    <?php echo e(date('M d, Y', strtotime($fromDate))); ?> - <?php echo e(date('M d, Y', strtotime($toDate))); ?>

                <?php else: ?>
                    All Records
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Report Date:</span>
            <span class="info-value"><?php echo e(date('m-d-Y')); ?></span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedHead): ?>
            <div class="info-item">
                <span class="info-label">Category:</span>
                <span class="info-value"><?php echo e($selectedHead); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Category Total:</span>
                <span class="info-value"><?php echo e(number_format($categoryTotal, 2)); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $entries->sortBy('date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="gl-no"><?php echo e($entry->serial_number); ?></td>
                    <td><?php echo e($entry->date->format('M d, Y')); ?></td>
                    <td><?php echo e($entry->head); ?></td>
                    <td class="description"><?php echo e($entry->description); ?></td>
                    <td class="amount">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->debit > 0): ?>
                            <?php echo e(number_format($entry->debit, 2)); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="amount">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->credit > 0): ?>
                            <?php echo e(number_format($entry->credit, 2)); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="balance"><?php echo e(number_format($balanceMap[$entry->id] ?? 0, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center p-4">No entries found</td>
                </tr>
            <?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entries->count() > 0): ?>
                <tr class="totals-row">
 <td class="text-end" colspan="4" >TOTALS</td>
                    <td class="amount"><?php echo e(number_format($entries->sum('debit'), 2)); ?></td>
                    <td class="amount"><?php echo e(number_format($entries->sum('credit'), 2)); ?></td>
                    <td class="balance">
                        <?php
                            $lastEntry = $entries->last();
                            $finalBalance = $balanceMap[$lastEntry->id] ?? 0;
                        ?>
                        <?php echo e(number_format($finalBalance, 2)); ?>

                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    
    <!-- FOOTER SECTION -->
    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>Printed on <?php echo e(date('F d, Y \a\t H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/finance/petty-cash-print.blade.php ENDPATH**/ ?>