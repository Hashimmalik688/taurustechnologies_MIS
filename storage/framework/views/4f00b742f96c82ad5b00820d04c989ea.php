<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petty Cash Ledger - <?php echo e(date('Y-m-d')); ?></title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Arial,sans-serif;color:#1a1a2e;background:#fff;padding:20px;font-size:13px}

        .print-buttons{margin-bottom:20px;text-align:right;display:flex;justify-content:flex-end;gap:10px}
        .print-buttons button,.print-buttons a{
            padding:9px 18px;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;
            text-decoration:none;display:inline-flex;align-items:center;gap:6px;color:#fff
        }
        .print-buttons .btn-print{background:linear-gradient(135deg,#b8860b,#d4a843)}
        .print-buttons .btn-csv{background:linear-gradient(135deg,#059669,#10b981)}
        .print-buttons .btn-close-pg{background:#6b7280}
        .print-buttons button:hover,.print-buttons a:hover{opacity:.9}

        .header{text-align:center;margin-bottom:28px;border-bottom:3px solid #1a1a2e;padding-bottom:14px}
        .company-name{font-size:26px;font-weight:800;color:#1a1a2e;margin-bottom:6px;letter-spacing:1.5px}
        .report-title{font-size:20px;font-weight:700;color:#555;margin-bottom:0}

        .report-info{display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:12px;margin-bottom:22px;background:#f8f9fa;padding:14px 18px;border:1px solid #dee2e6;border-radius:6px}
        .info-item{display:flex;justify-content:space-between}
        .info-label{font-weight:700;color:#555;width:140px}
        .info-value{flex:1;text-align:right;font-weight:600;color:#1a1a2e}

        table{width:100%;border-collapse:collapse;margin-top:18px}
        thead th{background:#2d2d3f;color:#fff;border:1px solid #1a1a2e;padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
        tbody td{border:1px solid #dee2e6;padding:9px 12px;font-size:12px}
        tbody tr:nth-child(even){background:#f8f9fa}
        tbody tr:hover{background:#fff3cd}

        .gl-no{font-weight:700;text-align:center;width:70px}
        .description{text-transform:uppercase;font-weight:500}
        .amount{text-align:right;font-family:'Courier New',monospace;width:110px}
        .balance{text-align:right;font-weight:700;font-family:'Courier New',monospace;width:120px;background:#fffbeb}

        .totals-row{background:#e9ecef !important;font-weight:700}
        .totals-row td{border-top:3px solid #1a1a2e;border-bottom:3px solid #1a1a2e;padding:12px}

        .footer{margin-top:28px;text-align:center;font-size:11px;color:#888;border-top:1px solid #dee2e6;padding-top:14px}

        @media print{
            body{padding:0;background:#fff}
            .no-print{display:none !important}
            thead th{background:#2d2d3f !important;color:#fff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .balance{background:#fffbeb !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            tbody tr:nth-child(even){background:#f8f9fa !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .totals-row{background:#e9ecef !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print</button>
        <a class="btn-csv" href="<?php echo e(route('petty-cash.export', request()->query())); ?>">📥 Download CSV</a>
        <button class="btn-close-pg" onclick="window.close()">✕ Close</button>
    </div>

    <div class="header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="report-title">PETTY CASH LEDGER</div>
    </div>

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

    <table>
        <thead>
            <tr>
                <th class="gl-no">G/L No.</th>
                <th>Date</th>
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
                    <td colspan="7" style="text-align:center;padding:24px;color:#888">No entries found</td>
                </tr>
            <?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entries->count() > 0): ?>
                <tr class="totals-row">
                    <td colspan="4" style="text-align:right">TOTALS</td>
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

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>Printed on <?php echo e(date('F d, Y \a\t h:i A')); ?></p>
    </div>
</body>
</html>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/finance/petty-cash-print.blade.php ENDPATH**/ ?>