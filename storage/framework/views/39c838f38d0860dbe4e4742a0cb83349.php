<?php $__env->startSection('title', 'Expense Tracker'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root { --acct-gold:#d4af37; --acct-surface:#f5f6fa; --acct-card-bg:#fff; --acct-border:#e8eaed; --acct-text:#1a1a2e; --acct-muted:#6b7280; }
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }
.rpt-card { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; overflow:hidden; margin-bottom:24px; }
.rpt-card-header { background:#1e1e2e; color:#fff; padding:14px 20px; display:flex; align-items:center; gap:10px; font-size:.9rem; font-weight:700; }
.rpt-card-header i { color:var(--acct-gold); font-size:1.1rem; }
.exp-table { width:100%; border-collapse:collapse; font-size:.82rem; }
.exp-table thead tr:first-child th { background:#1e1e2e; color:#fff; padding:10px 12px; text-align:center; font-size:.78rem; font-weight:600; border-right:1px solid rgba(255,255,255,.1); white-space:nowrap; }
.exp-table thead tr:first-child th:first-child { text-align:left; }
.exp-table thead tr:last-child th { background:#f3f4f6; padding:9px 12px; font-size:.75rem; color:var(--acct-muted); text-align:center; border-right:1px solid var(--acct-border); border-bottom:2px solid var(--acct-border); }
.exp-table thead tr:last-child th:first-child { text-align:left; }
.exp-table tbody td { padding:9px 12px; border-bottom:1px solid #f0f0f0; text-align:right; border-right:1px solid #f0f0f0; }
.exp-table tbody td:first-child { text-align:left; font-weight:600; border-right:2px solid var(--acct-border); }
.exp-table tbody tr:hover { background:#fafbff; }
.exp-table tfoot td { padding:11px 12px; font-weight:700; background:#f8f9fa; border-top:2px solid var(--acct-border); text-align:right; border-right:1px solid var(--acct-border); font-size:.84rem; }
.exp-table tfoot td:first-child { text-align:left; }
.zv { color:#d1d5db; font-size:.75rem; }
.gt { color:#1e1e2e; }
.filter-bar { background:var(--acct-card-bg); border:1px solid var(--acct-border); border-radius:10px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; }
.filter-bar label { font-size:.78rem; font-weight:600; color:var(--acct-muted); display:block; margin-bottom:4px; }
.empty-state { padding:48px 20px; text-align:center; color:var(--acct-muted); }
.empty-state i { font-size:2.5rem; display:block; margin-bottom:8px; opacity:.4; }
@media print { .acct-subnav,.filter-bar,nav,.sidebar,header { display:none!important; } body{ background:#fff; } }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="acct-page">

    <div class="d-flex align-items-center gap-3 mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#1e1e2e"><i class="bx bx-receipt me-2" style="color:#d97706"></i>Expense Tracker</h4>
            <small class="text-muted">Monthly breakdown of operating expenses — <?php echo e($year); ?></small>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grandTotal > 0): ?>
        <div class="ms-auto text-end d-print-none">
            <div style="font-size:.75rem;color:var(--acct-muted)">Total Expenses <?php echo e($year); ?></div>
            <div class="fw-bold" style="font-size:1.2rem;color:#d97706">$ <?php echo e(number_format($grandTotal, 2)); ?></div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <button class="btn btn-sm btn-outline-secondary d-print-none <?php echo e($grandTotal ? '' : 'ms-auto'); ?>" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Print
        </button>
    </div>

    
    <form method="GET" class="filter-bar d-print-none">
        <div>
            <label>Year</label>
            <select name="year" class="form-select form-select-sm" style="min-width:100px">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($year, $availableYears)): ?>
                    <option value="<?php echo e($year); ?>" selected><?php echo e($year); ?></option>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-sm btn-primary align-self-end">
            <i class="bx bx-filter-alt me-1"></i> Apply
        </button>
    </form>

    <div class="rpt-card">
        <div class="rpt-card-header">
            <i class="bx bx-receipt"></i>
            Expense Tracker — <?php echo e($year); ?>

        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expenseAccounts->isEmpty()): ?>
        <div class="empty-state">
            <i class="bx bx-store-alt"></i>
            <div class="fw-semibold mb-1">No Expense Accounts Found</div>
            <p class="text-muted small">Expense accounts (5100–5900) were seeded automatically. Record expenses using a General Journal Entry.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto">
            <table class="exp-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="min-width:200px">Account</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($mLabel); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <th style="background:#d4af37;color:#1a1a1a">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $expenseAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $acctTotal = $accountTotals[$acct->id] ?? 0; ?>
                    <tr>
                        <td>
                            <span class="text-muted font-monospace me-2" style="font-size:.74rem"><?php echo e($acct->account_code); ?></span>
                            <?php echo e($acct->account_name); ?>

                        </td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $amt = $matrix[$acct->id][$mNum] ?? 0; ?>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($amt > 0): ?>
                                <span style="color:#d97706">$ <?php echo e(number_format($amt, 2)); ?></span>
                            <?php else: ?>
                                <span class="zv">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <td style="font-weight:700;border-left:2px solid var(--acct-border);color:#d97706">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($acctTotal > 0): ?>
                                $ <?php echo e(number_format($acctTotal, 2)); ?>

                            <?php else: ?>
                                <span class="zv">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Monthly Total</td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $mTotal = $monthTotals[$mNum] ?? 0; ?>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mTotal > 0): ?>
                                $ <?php echo e(number_format($mTotal, 2)); ?>

                            <?php else: ?>
                                <span class="zv">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <td class="gt" style="border-left:2px solid var(--acct-border)">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grandTotal > 0): ?>
                                $ <?php echo e(number_format($grandTotal, 2)); ?>

                            <?php else: ?>
                                <span class="zv">$0</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grandTotal === 0.0 && !$expenseAccounts->isEmpty()): ?>
    <div class="alert alert-info d-flex align-items-center gap-2" style="border-radius:8px">
        <i class="bx bx-info-circle fs-5"></i>
        <div>
            No expense entries found for <strong><?php echo e($year); ?></strong>.
            To record an expense, use <a href="<?php echo e(route('admin.accounting.journal.create')); ?>">General Journal Entry</a>
            and debit one of the 5xxx expense accounts.
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/reports/expense-tracker.blade.php ENDPATH**/ ?>