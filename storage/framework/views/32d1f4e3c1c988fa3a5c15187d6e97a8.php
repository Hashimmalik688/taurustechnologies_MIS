<?php $__env->startSection('title', $partner->name . ' · ' . ($carrier?->name ?? 'Unassigned') . ' — Ledger'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --acct-gold:       #d4af37;
    --acct-gold-dark:  #b8941f;
    --acct-gold-light: #f5ecd0;
    --acct-header-bg:  #2d2d2d;
}

/* Statement header */
.stmt-header {
    background: var(--acct-header-bg);
    border-bottom: 3px solid var(--acct-gold);
    border-radius: 6px 6px 0 0;
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
}
.stmt-header-left .stmt-label  { font-size:.65rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--acct-gold); margin-bottom:3px; }
.stmt-header-left .stmt-account-name { font-size:1.25rem; font-weight:700; color:#fff; line-height:1.2; }
.stmt-header-left .stmt-meta   { font-size:.78rem; color:#aaa; margin-top:4px; }
.stmt-header-right { text-align:right; }
.stmt-header-right .stmt-balance-label { font-size:.65rem; letter-spacing:.1em; text-transform:uppercase; color:#aaa; }
.stmt-header-right .stmt-balance-value { font-size:1.5rem; font-weight:700; font-family:'Courier New',monospace; color:#fff; line-height:1.2; }
.stmt-header-right .stmt-balance-value.bal-pos { color:#81c784; }
.stmt-header-right .stmt-balance-value.bal-neg { color:#ef9a9a; }
.stmt-header-right .stmt-balance-note { font-size:.72rem; color:#888; }
.stmt-carrier-badge {
    display:inline-block; background:rgba(212,175,55,.18); border:1px solid rgba(212,175,55,.4);
    color:var(--acct-gold); font-size:.72rem; font-weight:700; letter-spacing:.06em;
    text-transform:uppercase; padding:3px 10px; border-radius:3px; margin-top:5px;
}

/* Summary strip */
.stmt-summary-strip {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    border: 1px solid #dee2e6;
    border-top: none;
    background: #fff;
}
@media (max-width:576px) { .stmt-summary-strip { grid-template-columns: 1fr; } }
.stmt-summary-cell { padding:12px 18px; border-right:1px solid #dee2e6; position:relative; }
.stmt-summary-cell:last-child { border-right:none; }
.stmt-summary-cell::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.stmt-summary-cell.cell-dr::before  { background:#66bb6a; }
.stmt-summary-cell.cell-cr::before  { background:#ef5350; }
.stmt-summary-cell.cell-bal::before { background:var(--acct-gold); }
.stmt-cell-label { font-size:.71rem; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:#6c757d; margin-bottom:3px; }
.stmt-cell-value { font-size:1.15rem; font-weight:700; font-family:'Courier New',monospace; }
.stmt-cell-value.dr-amt  { color:#2e7d32; }
.stmt-cell-value.cr-amt  { color:#c62828; }
.stmt-cell-value.bal-pos { color:#2e7d32; }
.stmt-cell-value.bal-neg { color:#c62828; }

/* Ledger table */
.stmt-table-wrapper {
    border:1px solid #dee2e6;
    border-top:none;
    border-radius:0 0 6px 6px;
    overflow:hidden;
}
.stmt-table { margin:0; font-size:.855rem; width:100%; border-collapse:collapse; }
.stmt-table thead th {
    background:#2d2d2d; color:#ccc;
    font-size:.72rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase;
    padding:8px 12px; white-space:nowrap; border:none;
}
.stmt-table thead th.text-end { text-align:right; }
.stmt-table thead tr th:nth-child(6),
.stmt-table thead tr th:nth-child(7),
.stmt-table thead tr th:nth-child(8) { border-left:1px solid #444; }
.stmt-table tbody tr { border-bottom:1px solid #f1f3f5; transition:background .1s; }
.stmt-table tbody tr:hover { background:#fffef5; }
.stmt-table tbody tr.opening-row { background:#fffde7; font-style:italic; }
.stmt-table tbody td { padding:8px 12px; vertical-align:middle; color:#2d2d2d; }
.stmt-table tbody td.col-dr,
.stmt-table tbody td.col-cr,
.stmt-table tbody td.col-bal {
    font-family:'Courier New',monospace; font-size:.9rem; text-align:right; border-left:1px solid #f1f3f5;
}
.stmt-table tbody td.col-dr  .amt { color:#2e7d32; font-weight:600; }
.stmt-table tbody td.col-cr  .amt { color:#c62828; font-weight:600; }
.stmt-table tbody td.col-bal .bal-pos { color:#2e7d32; font-weight:700; }
.stmt-table tbody td.col-bal .bal-neg { color:#c62828; font-weight:700; }
.stmt-table tfoot td {
    background:#f8f9fa; border-top:2px solid var(--acct-gold);
    font-weight:700; padding:8px 12px; font-size:.85rem; font-family:'Courier New',monospace;
}
.stmt-table tfoot td.col-dr,
.stmt-table tfoot td.col-cr,
.stmt-table tfoot td.col-bal { text-align:right; border-left:1px solid #dee2e6; }
.entry-link {
    font-family:'Courier New',monospace; font-size:.82rem;
    color:var(--acct-gold-dark); font-weight:600; text-decoration:none;
}
.entry-link:hover { text-decoration:underline; color:var(--acct-gold); }
.acct-type-badge { display:inline-block; font-size:.68rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; padding:2px 7px; border-radius:3px; white-space:nowrap; }
.acct-badge-sale    { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.acct-badge-payment { background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; }
.acct-badge-opening { background:#fff8e1; color:#f57f17; border:1px solid #ffe082; }
.acct-badge-general { background:#f3e5f5; color:#6a1b9a; border:1px solid #ce93d8; }

@media print {
    .no-print { display:none !important; }
    .stmt-header { background:#2d2d2d !important; -webkit-print-color-adjust:exact; }
    body { font-size:11px; }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-flex align-items-center gap-2 mb-3 no-print" style="font-size:.82rem;color:#888;">
        <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <i class="bx bx-book-open me-1"></i>Journal
        </a>
        <i class="bx bx-chevron-right"></i>
        <a href="<?php echo e(route('admin.accounting.partner-ledger')); ?>" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            Partner Ledger
        </a>
        <i class="bx bx-chevron-right"></i>
        <a href="<?php echo e(route('admin.accounting.partner-ledger.show', $partner->id)); ?>"
           style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <?php echo e($partner->name); ?>

        </a>
        <i class="bx bx-chevron-right"></i>
        <span style="color:#495057;font-weight:600;"><?php echo e($carrier?->name ?? 'Unassigned'); ?></span>
    </div>

    
    <div class="d-flex justify-content-between align-items-center mb-3 no-print flex-wrap gap-2">
        <a href="<?php echo e(route('admin.accounting.partner-ledger.show', $partner->id)); ?>"
           class="btn btn-sm btn-outline-secondary" style="font-size:.8rem;">
            <i class="bx bx-arrow-back me-1"></i> All Carriers
        </a>
        <div class="d-flex gap-2">
            <?php if(auth()->check() && auth()->user()->canEditModule('accounting')): ?>
            <a href="<?php echo e(route('admin.accounting.record-sale')); ?>"
               class="btn btn-sm" style="background:var(--acct-gold);color:#1a1a1a;font-weight:600;font-size:.8rem;border:none;">
                <i class="bx bx-purchase-tag me-1"></i> Record Sale
            </a>
            <a href="<?php echo e(route('admin.accounting.record-payment')); ?>"
               class="btn btn-sm btn-outline-success" style="font-size:.8rem;">
                <i class="bx bx-money me-1"></i> Record Payment
            </a>
            <?php endif; ?>
            <button onclick="window.print()"
                    class="btn btn-sm btn-outline-secondary" style="font-size:.8rem;">
                <i class="bx bx-printer me-1"></i> Print
            </button>
        </div>
    </div>

    <?php
        $balanceClass = $closingBalance >= 0 ? 'bal-pos' : 'bal-neg';
        $typeMap = [
            'sale'             => ['label' => 'Sale',    'cls' => 'acct-badge-sale'],
            'payment_received' => ['label' => 'Payment', 'cls' => 'acct-badge-payment'],
            'opening_balance'  => ['label' => 'Opening', 'cls' => 'acct-badge-opening'],
            'general'          => ['label' => 'General', 'cls' => 'acct-badge-general'],
        ];
    ?>

    
    <div class="stmt-header">
        <div class="stmt-header-left">
            <div class="stmt-label">Account Statement · Accounts Receivable</div>
            <div class="stmt-account-name"><?php echo e($partner->name); ?></div>
            <div class="stmt-meta">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->code): ?>
                    <span style="color:var(--acct-gold);font-weight:600;"><?php echo e($partner->code); ?></span>
                    &nbsp;·&nbsp;
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($lines->count()); ?> transaction(s)
                &nbsp;·&nbsp; AR Account <strong style="color:#ddd;">1200</strong>
            </div>
            <div class="stmt-carrier-badge">
                <i class="bx bx-buildings" style="margin-right:4px;vertical-align:middle;"></i>
                <?php echo e($carrier?->name ?? 'Unassigned Carrier'); ?>

            </div>
        </div>
        <div class="stmt-header-right">
            <div class="stmt-balance-label">Closing Balance</div>
            <div class="stmt-balance-value <?php echo e($balanceClass); ?>">
                $<?php echo e(number_format(abs($closingBalance), 2)); ?>

            </div>
            <div class="stmt-balance-note">
                <?php echo e($closingBalance >= 0 ? 'Debit — partner owes us' : 'Credit — we owe partner'); ?>

            </div>
        </div>
    </div>

    
    <div class="stmt-summary-strip">
        <div class="stmt-summary-cell cell-dr">
            <div class="stmt-cell-label">Total Debits (Sales)</div>
            <div class="stmt-cell-value dr-amt">$<?php echo e(number_format($totalDr, 2)); ?></div>
        </div>
        <div class="stmt-summary-cell cell-cr">
            <div class="stmt-cell-label">Total Credits (Payments)</div>
            <div class="stmt-cell-value cr-amt">$<?php echo e(number_format($totalCr, 2)); ?></div>
        </div>
        <div class="stmt-summary-cell cell-bal">
            <div class="stmt-cell-label">Net Balance</div>
            <div class="stmt-cell-value <?php echo e($balanceClass); ?>">
                $<?php echo e(number_format(abs($closingBalance), 2)); ?>

                <small style="font-size:.65rem;font-family:sans-serif;color:#999;margin-left:2px;">
                    <?php echo e($closingBalance >= 0 ? 'Dr' : 'Cr'); ?>

                </small>
            </div>
        </div>
    </div>

    
    <div class="stmt-table-wrapper">
        <div class="table-responsive">
            <table class="stmt-table">
                <thead>
                    <tr>
                        <th style="width:100px">Date</th>
                        <th style="width:130px">Entry #</th>
                        <th style="width:90px">Type</th>
                        <th>Description / Narration</th>
                        <th style="width:100px">Reference</th>
                        <th class="text-end" style="width:120px">Debit (USD)</th>
                        <th class="text-end" style="width:120px">Credit (USD)</th>
                        <th class="text-end" style="width:130px">Balance (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $type  = $line->journalEntry->type;
                        $badge = $typeMap[$type] ?? ['label' => ucfirst($type), 'cls' => 'acct-badge-general'];
                    ?>
                    <tr class="<?php echo e($type === 'opening_balance' ? 'opening-row' : ''); ?>">
                        <td style="white-space:nowrap;font-size:.82rem;">
                            <?php echo e($line->journalEntry->entry_date->format('d M Y')); ?>

                        </td>
                        <td>
                            <a href="<?php echo e(route('admin.accounting.journal.show', $line->journalEntry->id)); ?>"
                               class="entry-link">
                                <?php echo e($line->journalEntry->entry_number); ?>

                            </a>
                        </td>
                        <td>
                            <span class="acct-type-badge <?php echo e($badge['cls']); ?>"><?php echo e($badge['label']); ?></span>
                        </td>
                        <td><?php echo e($line->journalEntry->description); ?></td>
                        <td>
                            <span style="font-size:.82rem;color:#888;">
                                <?php echo e($line->journalEntry->reference ?? '—'); ?>

                            </span>
                        </td>
                        <td class="col-dr">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->debit > 0): ?>
                                <span class="amt"><?php echo e(number_format($line->debit, 2)); ?></span>
                            <?php else: ?>
                                <span style="color:#ccc;">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="col-cr">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->credit > 0): ?>
                                <span class="amt"><?php echo e(number_format($line->credit, 2)); ?></span>
                            <?php else: ?>
                                <span style="color:#ccc;">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="col-bal">
                            <span class="<?php echo e($line->running_balance >= 0 ? 'bal-pos' : 'bal-neg'); ?>">
                                <?php echo e(number_format(abs($line->running_balance), 2)); ?>

                            </span>
                            <small style="font-size:.68rem;font-family:sans-serif;color:#999;margin-left:2px;">
                                <?php echo e($line->running_balance >= 0 ? 'Dr' : 'Cr'); ?>

                            </small>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5" style="font-size:.875rem;">
                            <i class="bx bx-folder-open d-block mb-2" style="font-size:2rem;color:#dee2e6;"></i>
                            <div style="color:#888;margin-bottom:6px;">
                                No transactions for
                                <strong><?php echo e($carrier?->name ?? 'Unassigned'); ?></strong>
                                yet.
                            </div>
                            <?php if(auth()->check() && auth()->user()->canEditModule('accounting')): ?>
                            <div style="font-size:.78rem;color:#aaa;">
                                Record a Sale or Payment with
                                <strong><?php echo e($carrier?->name ?? 'no carrier'); ?></strong>
                                selected to populate this ledger.
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lines->isNotEmpty()): ?>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end"
                            style="font-family:sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6c757d;">
                            Totals &amp; Closing Balance
                        </td>
                        <td class="col-dr" style="color:#2e7d32;"><?php echo e(number_format($totalDr, 2)); ?></td>
                        <td class="col-cr" style="color:#c62828;"><?php echo e(number_format($totalCr, 2)); ?></td>
                        <td class="col-bal">
                            <span class="<?php echo e($closingBalance >= 0 ? 'bal-pos' : 'bal-neg'); ?>" style="font-size:.95rem;">
                                <?php echo e(number_format(abs($closingBalance), 2)); ?>

                            </span>
                            <small style="font-size:.68rem;font-family:sans-serif;color:#999;margin-left:2px;">
                                <?php echo e($closingBalance >= 0 ? 'Dr' : 'Cr'); ?>

                            </small>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/partner-ledger/carrier-show.blade.php ENDPATH**/ ?>