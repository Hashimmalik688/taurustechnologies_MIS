<?php $__env->startSection('title'); ?> Ledger <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --pd-indigo: #4f46e5;
    --pd-green:  #059669;
    --pd-br:     .6rem;
    --pd-sh:     0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
}

/* Page header */
.pl-hdr{margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem;}
.pl-hdr-left h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
.pl-hdr-left p{font-size:.84rem;color:#6b7280;margin:0;}

/* Balance pill */
.pl-balance{
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.5rem 1rem;border-radius:.45rem;border:1px solid;
    font-size:.86rem;font-weight:700;white-space:nowrap;
}
.pl-balance.owe{background:#fef2f2;color:#7f1d1d;border-color:#fecaca;}
.pl-balance.credit{background:#f0fdf4;color:#14532d;border-color:#bbf7d0;}
.pl-balance.zero{background:#f9fafb;color:#6b7280;border-color:#e5e7eb;}
.pl-balance i{font-size:1.05rem;}
.pl-balance .amt{font-weight:900;}

/* Filter form */
.pd-filter-form{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;padding:.55rem .85rem;background:#f8fafc;border:1px solid rgba(0,0,0,.07);border-radius:.45rem;margin-bottom:1rem;}
.pd-filter-label{font-size:.66rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#9ca3af;}
.pd-filter-input{background:#fff;border:1px solid rgba(0,0,0,.12);border-radius:.3rem;padding:.3rem .55rem;font-size:.82rem;color:#374151;}
.pd-filter-btn{background:rgba(79,70,229,.08);border:1px solid rgba(79,70,229,.2);color:#4f46e5;padding:.3rem .7rem;border-radius:.3rem;font-size:.8rem;font-weight:700;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:.25rem;}
.pd-filter-btn:hover{background:rgba(79,70,229,.16);}
.pd-filter-btn-reset{background:rgba(220,38,38,.07);border-color:rgba(220,38,38,.2);color:#dc2626;}
.pd-filter-btn-reset:hover{background:rgba(220,38,38,.14);}

/* Card */
.pd-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);box-shadow:var(--pd-sh);overflow:hidden;}
.pd-head{padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.06);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.pd-head h6{font-size:.88rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.35rem;color:#111827;}
.pd-head h6 i{color:var(--pd-indigo);}
.pd-count{background:rgba(79,70,229,.08);color:var(--pd-indigo);font-size:.7rem;font-weight:700;padding:.12rem .45rem;border-radius:.2rem;}

/* Table */
.pd-table{width:100%;border-collapse:collapse;font-size:.83rem;}
.pd-table thead th{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;border-bottom:1px solid rgba(0,0,0,.08);padding:.55rem .85rem;background:#f9fafb;white-space:nowrap;position:sticky;top:0;z-index:1;}
.pd-table tbody td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#374151;}
.pd-table tfoot td{padding:.6rem .85rem;font-weight:700;border-top:2px solid rgba(0,0,0,.1);background:#f0f0f5;font-size:.82rem;}
.pd-table tbody tr:hover{background:rgba(79,70,229,.022);}
.pd-table tbody tr:last-child td{border-bottom:none;}

/* Type chips */
.tc{font-size:.62rem;font-weight:800;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;letter-spacing:.3px;text-transform:uppercase;}
.tc-sale{background:#d1fae5;color:#065f46;}
.tc-pay{background:#dbeafe;color:#1e3a8a;}
.tc-cb{background:#fef3c7;color:#78350f;}
.tc-other{background:#f3f4f6;color:#374151;}

/* Balance cols */
.col-dr{color:#4f46e5;font-weight:700;}
.col-cr{color:#059669;font-weight:700;}
.col-dim{color:#d1d5db;}
.rb-pos{color:#059669;font-weight:700;}
.rb-neg{color:#dc2626;font-weight:700;}
.rb-zero{color:#9ca3af;}

/* Empty */
.pd-empty{text-align:center;padding:3rem 1rem;}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.5rem;opacity:.2;color:#9ca3af;}
.pd-empty p{font-size:.84rem;color:#9ca3af;margin:0;}

/* Note */
.pl-note{padding:.5rem .85rem;font-size:.73rem;color:#92400e;background:#fffbeb;border-bottom:1px solid rgba(245,158,11,.2);display:flex;align-items:center;gap:.4rem;}
.pl-note i{flex-shrink:0;}

/* Row count bar */
.pl-count-bar{padding:.4rem .85rem;font-size:.7rem;color:#9ca3af;background:#fafafa;border-bottom:1px solid rgba(0,0,0,.04);}

/* Dark themes */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table thead th{background:var(--bg-secondary,#16162a);color:var(--text-muted,#888);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody td{color:var(--text-primary,#ddd);border-color:var(--border-color,rgba(255,255,255,.04));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tfoot td{background:var(--bg-secondary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-filter-form{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-filter-input{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1);color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-hdr-left h4{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-balance.owe{background:rgba(220,38,38,.12);border-color:rgba(220,38,38,.25);color:#fca5a5;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-balance.credit{background:rgba(5,150,105,.12);border-color:rgba(5,150,105,.25);color:#6ee7b7;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-note{background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.2);color:#fde68a;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pl-count-bar{background:var(--bg-secondary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.04));}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="pl-hdr">
    <div class="pl-hdr-left">
        <h4><i class="bx bx-receipt" style="color:#4f46e5;margin-right:.35rem;"></i>Ledger</h4>
        <p>Complete transaction history — all debits, credits, and running balance.</p>
    </div>
    <?php
        $balClass = $currentBalance > 0 ? 'owe' : ($currentBalance < 0 ? 'credit' : 'zero');
        $balIcon  = $currentBalance > 0 ? 'bx-error-circle' : ($currentBalance < 0 ? 'bx-check-shield' : 'bx-check');
        $balLabel = $currentBalance > 0 ? 'Owed to Taurus' : ($currentBalance < 0 ? 'Credit Balance' : 'Balanced');
    ?>
    <div class="pl-balance <?php echo e($balClass); ?>">
        <i class="bx <?php echo e($balIcon); ?>"></i>
        <span><?php echo e($balLabel); ?>: <span class="amt">
            <?php echo e($currentBalance != 0 ? ($currentBalance > 0 ? '' : '−') . '$' . number_format(abs($currentBalance), 2) : '$0.00'); ?>

        </span></span>
    </div>
</div>


<form method="GET" action="<?php echo e(route('partner.ledger')); ?>" class="pd-filter-form">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrierId): ?><input type="hidden" name="carrier_id" value="<?php echo e($carrierId); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <span class="pd-filter-label"><i class="bx bx-calendar"></i> Date Range</span>
    <input type="date" name="date_from" class="pd-filter-input" style="width:135px;" value="<?php echo e($dateFrom); ?>" placeholder="From">
    <span style="color:#9ca3af;font-size:.8rem;">→</span>
    <input type="date" name="date_to"   class="pd-filter-input" style="width:135px;" value="<?php echo e($dateTo); ?>" placeholder="To">
    <button type="submit" class="pd-filter-btn"><i class="bx bx-filter-alt"></i> Apply</button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateFrom || $dateTo): ?>
    <a href="<?php echo e(route('partner.ledger', $carrierId ? ['carrier_id' => $carrierId] : [])); ?>" class="pd-filter-btn pd-filter-btn-reset" style="text-decoration:none;"><i class="bx bx-reset"></i> All time</a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$dateFrom && !$dateTo): ?>
    <span style="font-size:.7rem;color:#9ca3af;margin-left:.25rem;">Showing all entries</span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</form>


<?php echo $__env->make('partner.partials.carrier-filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="pd-card">
    <div class="pd-head">
        <h6><i class="bx bx-history"></i> Transaction History</h6>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrierId): ?>
            <span style="font-size:.72rem;color:#6b7280;font-style:italic;">Filtered: <?php echo e($activeCarriers->where('id', $carrierId)->first()['name'] ?? 'Carrier'); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="pd-count"><?php echo e($ledgerEntries->count()); ?></span>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ledgerEntries->count() > 0): ?>
    <div class="pl-note">
        <i class="bx bx-info-circle"></i>
        Running balance follows standard double-entry (Dr/Cr) — same as your MIS ledger.
        <strong>Dr (positive) = Taurus holds this amount against your account</strong> &nbsp;·&nbsp;
        <strong>Cr (negative) = credit in your favour</strong>
    </div>
    <div class="pl-count-bar"><?php echo e($ledgerEntries->count()); ?> <?php echo e($ledgerEntries->count() == 1 ? 'entry' : 'entries'); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateFrom || $dateTo): ?> · <?php echo e($dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : 'All'); ?> → <?php echo e($dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : 'Present'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div style="overflow-x:auto;max-height:75vh;overflow-y:auto;">
        <table class="pd-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Carrier</th>
                    <th>Reference / Note</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th class="text-end">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ledgerEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $tk  = strtolower(str_replace([' ','_','-'], '', $txn['type'] ?? ''));
                    $tc  = match(true) {
                        str_contains($tk, 'sale') && !str_contains($tk, 'return') => 'tc-sale',
                        str_contains($tk, 'payment') => 'tc-pay',
                        str_contains($tk, 'chargeback') || str_contains($tk, 'return') => 'tc-cb',
                        default => 'tc-other',
                    };
                    $rb  = $txn['running_balance'];
                    $rbCls = $rb > 0 ? 'rb-pos' : ($rb < 0 ? 'rb-neg' : 'rb-zero');
                    $rbPfx = $rb > 0 ? '+' : ($rb < 0 ? '−' : '');
                ?>
                <tr>
                    <td style="white-space:nowrap;color:#6b7280;font-size:.78rem;"><?php echo e(\Carbon\Carbon::parse($txn['date'])->format('M d, Y')); ?></td>
                    <td><span class="tc <?php echo e($tc); ?>"><?php echo e(str_replace('_', ' ', $txn['type'] ?? '—')); ?></span></td>
                    <td style="font-size:.8rem;color:#6b7280;"><?php echo e($txn['carrier'] ?? '—'); ?></td>
                    <td style="font-size:.78rem;color:#6b7280;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($txn['reference']): ?> <span style="font-weight:600;color:#374151;"><?php echo e($txn['reference']); ?></span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($txn['reference'] && $txn['description']): ?> &nbsp;·&nbsp; <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php echo e(\Illuminate\Support\Str::limit($txn['description'] ?? '', 35)); ?>

                    </td>
                    <td class="text-end <?php echo e(($txn['debit'] ?? 0) > 0 ? 'col-dr' : 'col-dim'); ?>"><?php echo e(($txn['debit'] ?? 0) > 0 ? '$' . number_format($txn['debit'], 2) : '—'); ?></td>
                    <td class="text-end <?php echo e(($txn['credit'] ?? 0) > 0 ? 'col-cr' : 'col-dim'); ?>"><?php echo e(($txn['credit'] ?? 0) > 0 ? '$' . number_format($txn['credit'], 2) : '—'); ?></td>
                    <td class="text-end">
                        <span class="<?php echo e($rbCls); ?>"><?php echo e($rbPfx); ?>$<?php echo e(number_format(abs($rb), 2)); ?><small style="font-size:.65rem;font-weight:500;opacity:.65;margin-left:.2rem;"><?php echo e($rb > 0 ? 'Dr' : ($rb < 0 ? 'Cr' : '')); ?></small></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <?php $finalBal = $ledgerEntries->last()['running_balance'] ?? 0; ?>
                <tr>
                    <td colspan="4">Closing Balance</td>
                    <td class="text-end">
                        $<?php echo e(number_format($ledgerEntries->sum('debit'), 2)); ?>

                    </td>
                    <td class="text-end">
                        $<?php echo e(number_format($ledgerEntries->sum('credit'), 2)); ?>

                    </td>
                    <td class="text-end">
                        <span class="<?php echo e($finalBal > 0 ? 'rb-pos' : ($finalBal < 0 ? 'rb-neg' : 'rb-zero')); ?>">
                            <?php echo e($finalBal > 0 ? '+' : ($finalBal < 0 ? '−' : '')); ?>$<?php echo e(number_format(abs($finalBal), 2)); ?><small style="font-size:.65rem;font-weight:500;opacity:.65;margin-left:.2rem;"><?php echo e($finalBal > 0 ? 'Dr' : ($finalBal < 0 ? 'Cr' : '')); ?></small>
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="pd-empty">
        <i class="bx bx-receipt"></i>
        <p>No ledger entries found<?php echo e(($dateFrom || $dateTo || $carrierId) ? ' for the selected filters.' : ' yet.'); ?></p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateFrom || $dateTo || $carrierId): ?>
        <p style="margin-top:.5rem;">
            <a href="<?php echo e(route('partner.ledger')); ?>" style="color:#4f46e5;text-decoration:none;font-size:.82rem;font-weight:700;">
                <i class="bx bx-reset"></i> Clear all filters
            </a>
        </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.partner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/partner/ledger.blade.php ENDPATH**/ ?>