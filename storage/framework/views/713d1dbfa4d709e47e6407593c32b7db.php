<?php $__env->startSection('title', 'Trial Balance'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --acct-gold: #d4af37; --acct-surface: #f5f6fa;
    --acct-card-bg: #fff; --acct-border: #e8eaed; --acct-text: #1a1a2e; --acct-muted: #6b7280;
}
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }
.rpt-card {
    background: var(--acct-card-bg);
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 24px;
}
.rpt-card-header {
    background: #1e1e2e;
    color: #fff;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: .9rem;
    font-weight: 700;
}
.rpt-card-header i { color: var(--acct-gold); font-size: 1.1rem; }
.rpt-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.rpt-table thead th {
    background: #f8f9fa;
    border-bottom: 2px solid var(--acct-border);
    padding: 10px 16px;
    text-align: left;
    font-weight: 600;
    color: var(--acct-text);
    white-space: nowrap;
}
.rpt-table thead th.text-end { text-align: right; }
.rpt-table tbody td { padding: 9px 16px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.rpt-table tbody tr:last-child td { border-bottom: none; }
.rpt-table tbody tr:hover { background: #fafbff; }
.rpt-table tfoot td {
    padding: 11px 16px;
    font-weight: 700;
    font-size: .88rem;
    border-top: 2px solid var(--acct-border);
    background: #f8f9fa;
}
.type-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: .72rem;
    font-weight: 600;
}
.type-asset { background:#dbeafe; color:#1e40af; }
.type-liability { background:#fce7f3; color:#be185d; }
.type-equity { background:#ede9fe; color:#6b21a8; }
.type-revenue { background:#dcfce7; color:#15803d; }
.type-expense { background:#ffedd5; color:#c2410c; }
.balanced-badge { font-size:.8rem; padding:4px 12px; border-radius:20px; font-weight:600; }
.balanced-ok { background:#d1fae5; color:#065f46; }
.balanced-err { background:#fee2e2; color:#991b1b; }
.filter-bar {
    background: var(--acct-card-bg);
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
}
.filter-bar label { font-size:.78rem; font-weight:600; color:var(--acct-muted); display:block; margin-bottom:4px; }
.filter-bar input, .filter-bar select { font-size:.84rem; }
.print-btn { margin-left: auto; }
@media print {
    .acct-subnav, .filter-bar, .print-btn, nav, .sidebar, header { display:none!important; }
    .rpt-card { border:none; }
    body { background:#fff; }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="acct-page">

    
    <div class="d-flex align-items-center gap-3 mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#1e1e2e"><i class="bx bx-scale text-primary me-2"></i>Trial Balance</h4>
            <small class="text-muted">Summarised debit and credit totals per account</small>
        </div>
        <span class="balanced-badge <?php echo e($balanced ? 'balanced-ok' : 'balanced-err'); ?> ms-auto d-print-none">
            <?php echo e($balanced ? '✓ Balanced' : '⚠ Not Balanced'); ?>

        </span>
        <button class="btn btn-sm btn-outline-secondary print-btn d-print-none" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Print
        </button>
    </div>

    
    <form method="GET" class="filter-bar d-print-none">
        <div>
            <label>As of Date</label>
            <input type="date" name="as_of" class="form-control form-control-sm" value="<?php echo e($asOf); ?>" max="<?php echo e(now()->toDateString()); ?>">
        </div>
        <div>
            <label>Partner (optional)</label>
            <select name="partner_id" class="form-select form-select-sm" style="min-width:160px">
                <option value="">All Partners</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php echo e($partnerId == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-sm btn-primary align-self-end">
            <i class="bx bx-filter-alt me-1"></i> Apply
        </button>
    </form>

    
    <div class="d-none d-print-block text-center mb-3">
        <h5 class="fw-bold">Trial Balance — As of <?php echo e(\Carbon\Carbon::parse($asOf)->format('F j, Y')); ?></h5>
        <small class="text-muted">Generated <?php echo e(now()->format('M j, Y H:i')); ?></small>
    </div>

    
    <div class="rpt-card">
        <div class="rpt-card-header">
            <i class="bx bx-scale"></i>
            Trial Balance — As of <?php echo e(\Carbon\Carbon::parse($asOf)->format('F j, Y')); ?>

        </div>
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Account Name</th>
                    <th>Type</th>
                    <th class="text-end">Debit (Dr)</th>
                    <th class="text-end">Credit (Cr)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $typeClass = match($row->account_type) {
                        'Asset'     => 'type-asset',
                        'Liability' => 'type-liability',
                        'Equity'    => 'type-equity',
                        'Revenue'   => 'type-revenue',
                        'Expense'   => 'type-expense',
                        default     => '',
                    };
                ?>
                <tr>
                    <td><span class="font-monospace fw-semibold text-muted"><?php echo e($row->account_code); ?></span></td>
                    <td class="fw-semibold"><?php echo e($row->account_name); ?></td>
                    <td><span class="type-badge <?php echo e($typeClass); ?>"><?php echo e($row->account_type); ?></span></td>
                    <td class="text-end">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->total_debit > 0): ?>
                            <span style="color:#1e40af">$ <?php echo e(number_format($row->total_debit, 2)); ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->total_credit > 0): ?>
                            <span style="color:#15803d">$ <?php echo e(number_format($row->total_credit, 2)); ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No journal entries found for the selected criteria.</td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="fw-bold" style="color:#1e1e2e">Grand Total</td>
                    <td class="text-end" style="color:#1e40af">$ <?php echo e(number_format($grandDebit, 2)); ?></td>
                    <td class="text-end" style="color:#15803d">$ <?php echo e(number_format($grandCredit, 2)); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$balanced): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 d-print-none" style="border-radius:8px">
        <i class="bx bx-error-circle fs-5"></i>
        <div><strong>Imbalance detected:</strong> The difference between total debits and credits is
            <strong>$ <?php echo e(number_format(abs($grandDebit - $grandCredit), 2)); ?></strong>.
            Check for any unbalanced journal entries.
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/reports/trial-balance.blade.php ENDPATH**/ ?>