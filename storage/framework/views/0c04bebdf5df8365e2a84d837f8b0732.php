<?php $__env->startSection('title', 'Sales Ledger — Accounts Receivable'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --acct-gold:    #d4af37;
    --acct-gold-dk: #b8941f;
    --acct-surface: #f5f6fa;
    --acct-border:  #e8eaed;
    --acct-text:    #1a1a2e;
    --acct-muted:   #6b7280;
}
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }

.sl-header {
    margin-bottom: 22px;
}
.sl-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--acct-text);
    margin: 0 0 3px;
}
.sl-sub { font-size: .82rem; color: var(--acct-muted); margin: 0; }

/* Summary bar */
.sl-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}
.sl-sum-card {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    padding: 18px 22px;
    position: relative;
    overflow: hidden;
}
.sl-sum-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 4px;
    border-radius: 10px 0 0 10px;
}
.sl-sum-card.dr::before  { background: #d4af37; }
.sl-sum-card.cr::before  { background: #2563eb; }
.sl-sum-card.bal::before { background: #059669; }
.sl-sum-label { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--acct-muted); margin-bottom: 5px; }
.sl-sum-value { font-size: 1.45rem; font-weight: 800; font-family: 'Inter',system-ui,sans-serif; color: var(--acct-text); }
.sl-sum-value.dr  { color: #b45309; }
.sl-sum-value.cr  { color: #1d4ed8; }
.sl-sum-value.green { color: #059669; }

/* Partner table card */
.sl-card {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    overflow: hidden;
}
.sl-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
}
.sl-card-title {
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 7px;
}
.sl-card-title i { color: var(--acct-gold); }

.sl-table { width: 100%; border-collapse: collapse; }
.sl-table th {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--acct-muted);
    padding: 10px 18px;
    border-bottom: 2px solid var(--acct-border);
    background: #fafbfc;
    white-space: nowrap;
}
.sl-table td {
    padding: 13px 18px;
    border-bottom: 1px solid #f3f4f6;
    font-size: .85rem;
    color: var(--acct-text);
    vertical-align: middle;
}
.sl-table tr:last-child td { border-bottom: none; }
.sl-table tbody tr { transition: background .1s; }
.sl-table tbody tr:hover td { background: #fffef5; }

.partner-name { font-weight: 700; color: var(--acct-text); }
.partner-code { font-size: .72rem; color: var(--acct-muted); font-family: 'Courier New',monospace; }
.num-cell {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: .88rem;
    white-space: nowrap;
}
.num-dr  { color: #92400e; }
.num-cr  { color: #1d4ed8; }
.num-pos { color: #059669; }
.num-neg { color: #dc2626; }

.bal-pill {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}
.bal-pill.pos { background: #dcfce7; color: #15803d; }
.bal-pill.neg { background: #fee2e2; color: #b91c1c; }
.bal-pill.zero { background: #f3f4f6; color: #6b7280; }

.btn-view-ledger {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .76rem;
    font-weight: 600;
    padding: 4px 12px;
    border: 1px solid var(--acct-gold);
    color: var(--acct-gold-dk);
    background: transparent;
    border-radius: 5px;
    text-decoration: none;
    transition: background .15s, color .15s;
}
.btn-view-ledger:hover { background: var(--acct-gold); color: #fff; }

/* empty state */
.sl-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--acct-muted);
}
.sl-empty i { font-size: 3rem; color: #e5e7eb; display: block; margin-bottom: 10px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="acct-page">

    <div class="sl-header">
        <h1 class="sl-title"><i class="bx bx-trending-up" style="color:var(--acct-gold)"></i> Sales Ledger</h1>
        <p class="sl-sub">Accounts Receivable sub-ledger — outstanding balances per partner</p>
    </div>

    
    <div class="sl-summary">
        <div class="sl-sum-card dr">
            <div class="sl-sum-label">Total Debited (Sales)</div>
            <div class="sl-sum-value dr">$<?php echo e(number_format($totalDr, 2)); ?></div>
        </div>
        <div class="sl-sum-card cr">
            <div class="sl-sum-label">Total Credited (Paid / Returns)</div>
            <div class="sl-sum-value cr">$<?php echo e(number_format($totalCr, 2)); ?></div>
        </div>
        <div class="sl-sum-card bal">
            <div class="sl-sum-label">Net AR Balance</div>
            <div class="sl-sum-value <?php echo e($totalBalance >= 0 ? 'green' : ''); ?>">$<?php echo e(number_format(abs($totalBalance), 2)); ?>

                <small style="font-size:.75rem;font-weight:500;color:var(--acct-muted)"> <?php echo e($totalBalance >= 0 ? 'Dr' : 'Cr'); ?></small>
            </div>
        </div>
    </div>

    
    <div class="sl-card">
        <div class="sl-card-header">
            <span class="sl-card-title"><i class="bx bx-group"></i> Partner AR Balances</span>
            <span style="font-size:.75rem;color:var(--acct-muted)"><?php echo e($partnersWithAR->count()); ?> partners</span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partnersWithAR->count()): ?>
        <div style="overflow-x:auto">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th class="text-end">Debit (Dr)</th>
                        <th class="text-end">Credit (Cr)</th>
                        <th class="text-center">Balance</th>
                        <th class="text-center">Transactions</th>
                        <th class="text-center">Last Activity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partnersWithAR; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="partner-name"><?php echo e($row->partner_name); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->partner_code): ?>
                            <div class="partner-code"><?php echo e($row->partner_code); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="text-end num-cell num-dr">$<?php echo e(number_format($row->total_dr, 2)); ?></td>
                        <td class="text-end num-cell num-cr">$<?php echo e(number_format($row->total_cr, 2)); ?></td>
                        <td class="text-center">
                            <?php $bal = (float)$row->balance; ?>
                            <span class="bal-pill <?php echo e($bal > 0 ? 'pos' : ($bal < 0 ? 'neg' : 'zero')); ?>">
                                <?php echo e($bal != 0 ? ($bal > 0 ? '' : '-') . '$' . number_format(abs($bal),2) : '$0.00'); ?>

                                <?php echo e($bal > 0 ? 'Dr' : ($bal < 0 ? 'Cr' : '')); ?>

                            </span>
                        </td>
                        <td class="text-center" style="color:var(--acct-muted)"><?php echo e($row->tx_count); ?></td>
                        <td class="text-center" style="color:var(--acct-muted);font-size:.8rem">
                            <?php echo e($row->last_activity ? \Carbon\Carbon::parse($row->last_activity)->format('d M Y') : '—'); ?>

                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.accounting.sales-ledger.partner', $row->partner_id)); ?>" class="btn-view-ledger">
                                <i class="bx bx-spreadsheet"></i> View Ledger
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sl-empty">
            <i class="bx bx-book-open"></i>
            <p style="font-size:.9rem;font-weight:600;color:#374151;margin-bottom:4px">No AR transactions yet</p>
            <p style="font-size:.82rem">Record a sale to see balances here.</p>
            <a href="<?php echo e(route('admin.accounting.record-sale')); ?>" class="btn btn-sm mt-2" style="background:var(--acct-gold);color:#1a1a2e;font-weight:700">
                <i class="bx bx-plus me-1"></i> Record Sale
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="sl-card mt-4">
        <div class="sl-card-header">
            <span class="sl-card-title"><i class="bx bx-list-ul"></i> All Credit Entries</span>
            <span style="font-size:.75rem;color:var(--acct-muted)"><?php echo e(number_format($allEntriesTotal)); ?> entries (sales &amp; returns)</span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allEntries->count()): ?>
        <div style="overflow-x:auto">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>Entry #</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Insured / Description</th>
                        <th class="text-end">Amount</th>
                        <th>Reference</th>
                        <th>Recorded By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $je): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $isSale = $je->type === 'sale'; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('admin.accounting.journal.show', $je->id)); ?>"
                               class="font-monospace fw-semibold text-decoration-none"
                               style="color:#1e40af;font-size:.82rem">
                                <?php echo e($je->entry_number); ?>

                            </a>
                        </td>
                        <td style="font-size:.82rem;white-space:nowrap">
                            <?php echo e(\Carbon\Carbon::parse($je->entry_date)->format('d M Y')); ?>

                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSale): ?>
                                <span style="background:#dcfce7;color:#15803d;border-radius:4px;padding:2px 8px;font-size:.72rem;font-weight:700">Sale</span>
                            <?php else: ?>
                                <span style="background:#fee2e2;color:#b91c1c;border-radius:4px;padding:2px 8px;font-size:.72rem;font-weight:700">Return</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($je->insured_name): ?>
                                <div style="font-weight:600;font-size:.84rem"><?php echo e($je->insured_name); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div style="font-size:.75rem;color:var(--acct-muted)" class="text-truncate" style="max-width:260px">
                                <?php echo e($je->description); ?>

                            </div>
                        </td>
                        <td class="text-end num-cell <?php echo e($isSale ? 'num-dr' : 'num-neg'); ?>">
                            <?php echo e($isSale ? '' : '-'); ?>$<?php echo e(number_format($je->total_debit, 2)); ?>

                        </td>
                        <td style="font-size:.78rem;color:var(--acct-muted)"><?php echo e($je->reference ?? '—'); ?></td>
                        <td style="font-size:.78rem;color:var(--acct-muted)"><?php echo e($je->creator?->name ?? '—'); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.accounting.journal.show', $je->id)); ?>"
                               class="btn-view-ledger py-1">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3 d-flex justify-content-center">
            <?php echo e($allEntries->withQueryString()->links()); ?>

        </div>
        <?php else: ?>
        <div class="sl-empty">
            <i class="bx bx-receipt"></i>
            <p style="font-size:.9rem;font-weight:600;color:#374151;margin-bottom:4px">No entries yet</p>
            <p style="font-size:.82rem">Post paid sales to the ledger from the <a href="<?php echo e(route('admin.paid-sales.index')); ?>">Paid Sales</a> page.</p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/sales-ledger/index.blade.php ENDPATH**/ ?>