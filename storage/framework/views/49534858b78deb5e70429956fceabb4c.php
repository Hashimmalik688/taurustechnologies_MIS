<?php $__env->startSection('title', 'Sales Ledger — ' . $partner->name); ?>

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

/* Breadcrumb + header */
.sl-breadcrumb {
    font-size: .78rem;
    color: var(--acct-muted);
    margin-bottom: 14px;
}
.sl-breadcrumb a { color: var(--acct-gold-dk); text-decoration: none; font-weight: 600; }
.sl-breadcrumb a:hover { text-decoration: underline; }

.sl-partner-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
    background: #fff;
    border: 1px solid var(--acct-border);
    border-left: 5px solid var(--acct-gold);
    border-radius: 0 10px 10px 0;
    padding: 18px 22px;
}
.sl-partner-name { font-size: 1.2rem; font-weight: 800; color: var(--acct-text); margin: 0 0 3px; }
.sl-partner-meta { font-size: .8rem; color: var(--acct-muted); }

/* Summary strip */
.sl-summary-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
.sl-stat {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 8px;
    padding: 14px 18px;
}
.sl-stat-label { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--acct-muted); margin-bottom: 4px; }
.sl-stat-val { font-size: 1.3rem; font-weight: 800; font-family: 'Inter',system-ui,sans-serif; }
.sl-stat-val.dr    { color: #b45309; }
.sl-stat-val.cr    { color: #1d4ed8; }
.sl-stat-val.green { color: #059669; }
.sl-stat-val.red   { color: #dc2626; }

/* Filter bar */
.sl-filter-bar {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 8px;
    padding: 12px 18px;
    margin-bottom: 18px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.sl-filter-bar label {
    font-size: .73rem;
    font-weight: 700;
    color: var(--acct-muted);
    white-space: nowrap;
}
.sl-filter-bar .form-select, .sl-filter-bar .form-control {
    font-size: .82rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 5px 10px;
    height: auto;
}
.sl-filter-bar .form-select:focus, .sl-filter-bar .form-control:focus {
    border-color: var(--acct-gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,.15);
}

/* Ledger table */
.sl-ledger-card {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    overflow: hidden;
}
.sl-ledger-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 20px;
    border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
}
.sl-ledger-title {
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 7px;
}
.sl-ledger-title i { color: var(--acct-gold); }

.sl-table { width: 100%; border-collapse: collapse; }
.sl-table th {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--acct-muted);
    padding: 10px 16px;
    border-bottom: 2px solid var(--acct-border);
    background: #fafbfc;
    white-space: nowrap;
}
.sl-table td {
    padding: 11px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: .84rem;
    color: var(--acct-text);
    vertical-align: middle;
}
.sl-table tr:last-child td { border-bottom: none; }
.sl-table tbody tr:hover td { background: #fffef5; }

.entry-link {
    font-family: 'Courier New', monospace;
    font-size: .78rem;
    font-weight: 700;
    color: var(--acct-gold-dk);
    text-decoration: none;
}
.entry-link:hover { text-decoration: underline; }

.type-icon-sale     { color: #15803d; }
.type-icon-chargeback { color: #b91c1c; }
.type-icon-opening  { color: #92400e; }
.type-icon-payment  { color: #1d4ed8; }

.num { font-family: 'Courier New',monospace; font-weight: 700; font-size: .88rem; }
.num.dr  { color: #b45309; }
.num.cr  { color: #1d4ed8; }
.num.zero { color: #9ca3af; }
.num.pos  { color: #059669; }
.num.neg  { color: #dc2626; }

/* Total footer row */
.sl-table tfoot td {
    padding: 12px 16px;
    font-size: .84rem;
    font-weight: 700;
    background: #fafbfc;
    border-top: 2px solid var(--acct-gold);
    border-bottom: none;
    color: var(--acct-text);
}

/* Closing balance banner */
.sl-closing-banner {
    padding: 14px 20px;
    background: <?php echo e($closingBalance >= 0 ? '#f0fdf4' : '#fef2f2'); ?>;
    border-top: 1px solid <?php echo e($closingBalance >= 0 ? '#bbf7d0' : '#fecaca'); ?>;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: .85rem;
}
.sl-closing-label { font-weight: 700; color: <?php echo e($closingBalance >= 0 ? '#15803d' : '#b91c1c'); ?>; }
.sl-closing-val {
    font-size: 1.05rem;
    font-weight: 800;
    font-family: 'Courier New', monospace;
    color: <?php echo e($closingBalance >= 0 ? '#059669' : '#dc2626'); ?>;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="acct-page">

    
    <div class="sl-breadcrumb">
        <a href="<?php echo e(route('admin.accounting.sales-ledger')); ?>"><i class="bx bx-chevron-left"></i> Sales Ledger</a>
        &nbsp;/&nbsp; <?php echo e($partner->name); ?>

    </div>

    
    <div class="sl-partner-header">
        <div>
            <h1 class="sl-partner-name"><?php echo e($partner->name); ?></h1>
            <p class="sl-partner-meta">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->code): ?> Code: <strong><?php echo e($partner->code); ?></strong> &nbsp;·&nbsp; <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                Accounts Receivable — Sales Ledger
            </p>
        </div>
        <a href="<?php echo e(route('admin.accounting.partner-ledger.show', $partner->id)); ?>"
           style="font-size:.8rem;color:var(--acct-gold-dk);text-decoration:none;font-weight:600;align-self:center">
            <i class="bx bx-user-circle me-1"></i> Full Partner Ledger
        </a>
    </div>

    
    <div class="sl-summary-strip">
        <div class="sl-stat">
            <div class="sl-stat-label">Total Debits</div>
            <div class="sl-stat-val dr">$<?php echo e(number_format($totalDr, 2)); ?></div>
        </div>
        <div class="sl-stat">
            <div class="sl-stat-label">Total Credits</div>
            <div class="sl-stat-val cr">$<?php echo e(number_format($totalCr, 2)); ?></div>
        </div>
        <div class="sl-stat">
            <div class="sl-stat-label">Closing Balance</div>
            <div class="sl-stat-val <?php echo e($closingBalance >= 0 ? 'green' : 'red'); ?>">
                $<?php echo e(number_format(abs($closingBalance), 2)); ?>

                <small style="font-size:.72rem;font-weight:500;color:var(--acct-muted)"><?php echo e($closingBalance >= 0 ? 'Dr' : 'Cr'); ?></small>
            </div>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.accounting.sales-ledger.partner', $partner->id)); ?>" class="sl-filter-bar">
        <label>Filter:</label>
        <select name="carrier_id" class="form-select" style="max-width:180px">
            <option value="">All Carriers</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php echo e(request('carrier_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>
        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-control" style="max-width:145px" title="From date">
        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-control" style="max-width:145px" title="To date">
        <button type="submit" class="btn btn-sm" style="background:var(--acct-gold);color:#1a1a2e;font-weight:700;font-size:.79rem;border:none;padding:5px 16px">
            <i class="bx bx-search me-1"></i> Apply
        </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['carrier_id','date_from','date_to'])): ?>
        <a href="<?php echo e(route('admin.accounting.sales-ledger.partner', $partner->id)); ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.79rem">
            Clear
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    
    <div class="sl-ledger-card">
        <div class="sl-ledger-header">
            <span class="sl-ledger-title"><i class="bx bx-spreadsheet"></i> Transaction History</span>
            <span style="font-size:.75rem;color:var(--acct-muted)"><?php echo e($lines->count()); ?> transactions</span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lines->count()): ?>
        <div style="overflow-x:auto">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Type</th>
                        <th>Insured / Description</th>
                        <th>Carrier</th>
                        <th>Reference</th>
                        <th class="text-end">Debit (Dr)</th>
                        <th class="text-end">Credit (Cr)</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="white-space:nowrap;color:var(--acct-muted)">
                            <?php echo e(\Carbon\Carbon::parse($line->entry_date)->format('d M Y')); ?>

                        </td>
                        <td>
                            <a href="<?php echo e(route('admin.accounting.journal.show', $line->entry_id)); ?>" class="entry-link">
                                <?php echo e($line->entry_number); ?>

                            </a>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->type === 'sale'): ?>
                                <i class="bx bx-purchase-tag type-icon-sale" title="Sale"></i>
                                <span style="font-size:.75rem;color:#15803d;font-weight:600">Sale</span>
                            <?php elseif($line->type === 'chargeback'): ?>
                                <i class="bx bx-undo type-icon-chargeback" title="Chargeback"></i>
                                <span style="font-size:.75rem;color:#b91c1c;font-weight:600">Chargeback</span>
                            <?php elseif($line->type === 'payment_received'): ?>
                                <i class="bx bx-money type-icon-payment" title="Payment"></i>
                                <span style="font-size:.75rem;color:#1d4ed8;font-weight:600">Payment</span>
                            <?php else: ?>
                                <span style="font-size:.75rem;color:var(--acct-muted)"><?php echo e(ucfirst($line->type)); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->insured_name): ?>
                                <span style="font-weight:600"><?php echo e($line->insured_name); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->description && $line->description !== $line->insured_name): ?>
                                    <br><small style="color:var(--acct-muted);font-size:.74rem"><?php echo e($line->description); ?></small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <?php echo e($line->description ?? '—'); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td style="color:var(--acct-muted);font-size:.8rem"><?php echo e($line->carrier_name ?? '—'); ?></td>
                        <td style="font-family:'Courier New',monospace;font-size:.78rem;color:var(--acct-muted)">
                            <?php echo e($line->reference ?? '—'); ?>

                        </td>
                        <td class="text-end">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->debit > 0): ?>
                                <span class="num dr">$<?php echo e(number_format($line->debit, 2)); ?></span>
                            <?php else: ?>
                                <span class="num zero">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->credit > 0): ?>
                                <span class="num cr">$<?php echo e(number_format($line->credit, 2)); ?></span>
                            <?php else: ?>
                                <span class="num zero">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="text-end">
                            <span class="num <?php echo e($line->running_balance > 0 ? 'pos' : ($line->running_balance < 0 ? 'neg' : 'zero')); ?>">
                                $<?php echo e(number_format(abs($line->running_balance), 2)); ?>

                                <small style="font-size:.65rem;font-weight:500;opacity:.7"><?php echo e($line->running_balance > 0 ? 'Dr' : ($line->running_balance < 0 ? 'Cr' : '')); ?></small>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;font-size:.78rem;color:var(--acct-muted);letter-spacing:.05em;text-transform:uppercase">TOTALS</td>
                        <td class="text-end"><span class="num dr">$<?php echo e(number_format($totalDr, 2)); ?></span></td>
                        <td class="text-end"><span class="num cr">$<?php echo e(number_format($totalCr, 2)); ?></span></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="sl-closing-banner">
            <span class="sl-closing-label">Closing Balance</span>
            <span class="sl-closing-val">
                $<?php echo e(number_format(abs($closingBalance), 2)); ?>

                <?php echo e($closingBalance >= 0 ? 'Dr' : 'Cr'); ?>

            </span>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:50px 20px;color:var(--acct-muted)">
            <i class="bx bx-spreadsheet" style="font-size:2.5rem;color:#e5e7eb;display:block;margin-bottom:10px"></i>
            <p style="font-size:.85rem">No transactions found for this partner with the current filters.</p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/sales-ledger/partner.blade.php ENDPATH**/ ?>