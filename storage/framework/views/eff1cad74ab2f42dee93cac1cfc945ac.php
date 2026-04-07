<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Voucher — <?php echo e($entry->entry_number); ?></title>
    <style>
        *  { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a2e;
            background: #fff;
            padding: 28px 32px;
        }

        /* ── Buttons ─────────────────────────────────────────────────── */
        .print-buttons {
            display: flex; gap: 10px; justify-content: center;
            margin-bottom: 24px;
        }
        .print-buttons button,
        .print-buttons a {
            padding: 9px 20px; border: none; border-radius: 6px;
            cursor: pointer; font-size: 13px; font-weight: 600;
            text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px; color: #fff;
        }
        .btn-print   { background: linear-gradient(135deg,#1a1a2e,#2d2d3f); }
        .btn-back    { background: #6b7280; }
        .print-buttons button:hover, .print-buttons a:hover { opacity: .88; }

        /* ── Header ──────────────────────────────────────────────────── */
        .doc-header {
            text-align: center;
            border-bottom: 3px double #1a1a2e;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .company-name  { font-size: 22px; font-weight: 800; letter-spacing: 1.5px; }
        .doc-title     { font-size: 15px; font-weight: 700; color: #444; margin-top: 4px; }
        .doc-subtitle  { font-size: 11px; color: #777; margin-top: 2px; }

        /* ── Meta strip ──────────────────────────────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #1a1a2e;
        }
        .meta-table td {
            padding: 5px 10px;
            border: 1px solid #adb5bd;
            font-size: 12px;
            vertical-align: top;
        }
        .meta-label { font-size: 10px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: .04em; display: block; }
        .meta-value { font-weight: 600; color: #1a1a2e; display: block; margin-top: 1px; }
        .badge {
            display: inline-block; padding: 2px 7px;
            border-radius: 3px; font-size: 10px; font-weight: 700;
            border: 1px solid #1a1a2e;
        }
        .badge-sale    { background: #cfe2ff; color: #084298; }
        .badge-payment { background: #d1e7dd; color: #0a3622; }
        .badge-opening { background: #e2e3e5; color: #41464b; }
        .badge-general { background: #fff3cd; color: #664d03; }

        /* ── Lines table ─────────────────────────────────────────────── */
        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .lines-table th {
            background: #2d2d3f;
            color: #fff;
            font-weight: 700;
            padding: 7px 8px;
            border: 1px solid #2d2d3f;
            font-size: 11px;
            white-space: nowrap;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .lines-table td {
            padding: 6px 8px;
            border: 1px solid #adb5bd;
            font-size: 12px;
            vertical-align: middle;
        }
        .lines-table tbody tr:nth-child(even) {
            background: #f8f9fa;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .narration-row td {
            background: #fffbea !important;
            font-style: italic;
            color: #555;
            font-size: 11px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .lines-table tfoot td {
            background: #e9ecef !important;
            font-weight: 700;
            border: 1px solid #1a1a2e;
            border-top: 2px solid #1a1a2e;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .acc-code    { font-family: 'Courier New', monospace; font-size: 11px; color: #555; }
        .dr-amt      { color: #146c43; font-family: 'Courier New', monospace; font-weight: 700; }
        .cr-amt      { color: #b02a37; font-family: 'Courier New', monospace; font-weight: 700; }

        /* ── Signature row ───────────────────────────────────────────── */
        .sig-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            margin-top: 40px;
            padding-top: 10px;
        }
        .sig-box { text-align: center; }
        .sig-line {
            border-top: 1px solid #1a1a2e;
            padding-top: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #444;
        }

        /* ── Footer ──────────────────────────────────────────────────── */
        .doc-footer {
            margin-top: 30px;
            border-top: 2px solid #1a1a2e;
            padding-top: 8px;
            text-align: center;
            font-size: 10px;
            color: #888;
        }

        @media print {
            .no-print    { display: none !important; }
            body         { padding: 10px 14px; }
            .lines-table th     { background: #2d2d3f !important; color:#fff!important; }
            .lines-table tbody tr:nth-child(even) { background: #f8f9fa !important; }
            .lines-table tfoot td { background:#e9ecef!important; }
            .narration-row td   { background:#fffbea!important; }
        }
    </style>
</head>
<body>

    
    <div class="print-buttons no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <a href="<?php echo e(route('admin.accounting.journal.show', $entry->id)); ?>" class="btn-back">← Back</a>
    </div>

    
    <div class="doc-header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="doc-title">JOURNAL VOUCHER</div>
        <div class="doc-subtitle">Double-Entry Accounting Record</div>
    </div>

    
    <table class="meta-table">
        <tr>
            <td style="width:20%">
                <span class="meta-label">Voucher No.</span>
                <span class="meta-value"><?php echo e($entry->entry_number); ?></span>
            </td>
            <td style="width:18%">
                <span class="meta-label">Date</span>
                <span class="meta-value"><?php echo e($entry->entry_date->format('d M Y')); ?></span>
            </td>
            <td style="width:17%">
                <span class="meta-label">Type</span>
                <span class="meta-value">
                    <?php
                        $cls = match($entry->type) {
                            'sale'             => 'badge-sale',
                            'payment_received' => 'badge-payment',
                            'opening_balance'  => 'badge-opening',
                            default            => 'badge-general',
                        };
                    ?>
                    <span class="badge <?php echo e($cls); ?>"><?php echo e($entry->type_label); ?></span>
                </span>
            </td>
            <td style="width:20%">
                <span class="meta-label">Reference / Policy #</span>
                <span class="meta-value"><?php echo e($entry->reference ?: '—'); ?></span>
            </td>
            <td style="width:25%">
                <span class="meta-label">Narration</span>
                <span class="meta-value"><?php echo e($entry->description); ?></span>
            </td>
        </tr>
    </table>

    
    <table class="lines-table">
        <thead>
            <tr>
                <th style="width:32px" class="text-center">#</th>
                <th style="width:110px">Account Code</th>
                <th>Account Name</th>
                <th>Partner / Client</th>
                <th style="width:110px">Carrier</th>
                <th>Narration</th>
                <th class="text-right" style="width:115px">Debit</th>
                <th class="text-right" style="width:115px">Credit</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $entry->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center" style="color:#888"><?php echo e($loop->iteration); ?></td>
                <td><span class="acc-code"><?php echo e($line->account->account_code); ?></span></td>
                <td><?php echo e($line->account->account_name); ?></td>
                <td><?php echo e($line->partner?->name ?? '—'); ?></td>
                <td><?php echo e($line->carrier?->name ?? '—'); ?></td>
                <td><?php echo e($line->description ?: $entry->description); ?></td>
                <td class="text-right dr-amt">
                    <?php echo e($line->debit > 0 ? number_format($line->debit, 2) : ''); ?>

                </td>
                <td class="text-right cr-amt">
                    <?php echo e($line->credit > 0 ? number_format($line->credit, 2) : ''); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <tr class="narration-row">
                <td></td>
                <td colspan="5">Being: <?php echo e($entry->description); ?></td>
                <td></td><td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Total</td>
                <td class="text-right dr-amt"><?php echo e(number_format($entry->lines->sum('debit'), 2)); ?></td>
                <td class="text-right cr-amt"><?php echo e(number_format($entry->lines->sum('credit'), 2)); ?></td>
            </tr>
        </tfoot>
    </table>

    
    <div class="sig-row">
        <div class="sig-box">
            <div style="height:36px"></div>
            <div class="sig-line">Prepared By</div>
            <div style="font-size:11px;color:#555;margin-top:3px"><?php echo e($entry->creator?->name ?? '—'); ?></div>
        </div>
        <div class="sig-box">
            <div style="height:36px"></div>
            <div class="sig-line">Checked By</div>
        </div>
        <div class="sig-box">
            <div style="height:36px"></div>
            <div class="sig-line">Approved By</div>
        </div>
    </div>

    
    <div class="doc-footer">
        Generated on <?php echo e(now()->format('d M Y, h:i A')); ?> &nbsp;·&nbsp;
        <?php echo e($entry->entry_number); ?> &nbsp;·&nbsp;
        Taurus Technologies — Accounting System
    </div>

</body>
</html>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/journal/print.blade.php ENDPATH**/ ?>