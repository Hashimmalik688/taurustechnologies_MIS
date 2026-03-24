<?php $__env->startSection('title', 'Accounting — Overview'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --acct-gold:      #d4af37;
    --acct-gold-dk:   #b8941f;
    --acct-surface:   #f5f6fa;
    --acct-card-bg:   #ffffff;
    --acct-border:    #e8eaed;
    --acct-text:      #1a1a2e;
    --acct-muted:     #6b7280;
}
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }

/* KPI Row */
.acct-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media(max-width:900px){ .acct-kpi-grid{ grid-template-columns: repeat(2,1fr); } }
.acct-kpi-card {
    background: var(--acct-card-bg);
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    padding: 20px 22px;
    position: relative;
    overflow: hidden;
}
.acct-kpi-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 10px 10px 0 0;
}
.acct-kpi-card.kpi-ar::after    { background: #10b981; }
.acct-kpi-card.kpi-sales::after { background: #d4af37; }
.acct-kpi-card.kpi-cb::after    { background: #ef4444; }
.acct-kpi-card.kpi-net::after   { background: #6366f1; }
.acct-kpi-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--acct-muted);
    margin-bottom: 6px;
}
.acct-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--acct-text);
    line-height: 1;
}
.acct-kpi-value.green  { color: #059669; }
.acct-kpi-value.red    { color: #dc2626; }
.acct-kpi-value.indigo { color: #4f46e5; }
.acct-kpi-sub {
    font-size: .75rem;
    color: var(--acct-muted);
    margin-top: 4px;
}
.acct-kpi-icon {
    position: absolute;
    right: 18px; top: 18px;
    font-size: 2rem;
    opacity: .12;
    color: var(--acct-text);
}

/* Two-column layout */
.acct-dash-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 20px;
}
@media(max-width:1100px){ .acct-dash-grid{ grid-template-columns: 1fr; } }

/* Cards */
.acct-card {
    background: var(--acct-card-bg);
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    overflow: hidden;
}
.acct-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
}
.acct-card-title {
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 7px;
}
.acct-card-title i { color: var(--acct-gold); font-size: 1rem; }

/* Recent entries table */
.acct-mini-table { width: 100%; border-collapse: collapse; }
.acct-mini-table th {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--acct-muted);
    padding: 10px 16px;
    border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
    white-space: nowrap;
}
.acct-mini-table td {
    font-size: .83rem;
    padding: 10px 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
    color: var(--acct-text);
}
.acct-mini-table tr:last-child td { border-bottom: none; }
.acct-mini-table tr:hover td { background: #fffef5; }
.entry-num {
    font-family: 'Courier New', monospace;
    font-size: .78rem;
    color: var(--acct-gold-dk);
    font-weight: 700;
    text-decoration: none;
}
.entry-num:hover { text-decoration: underline; color: var(--acct-gold-dk); }

/* Type badge */
.t-badge {
    display: inline-block;
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    padding: 2px 7px;
    border-radius: 3px;
}
.t-sale     { background: #dcfce7; color: #15803d; }
.t-payment  { background: #dbeafe; color: #1d4ed8; }
.t-opening  { background: #fef9c3; color: #92400e; }
.t-chargeback{ background: #fee2e2; color: #b91c1c; }
.t-general  { background: #ede9fe; color: #5b21b6; }

/* Partner balances */
.pb-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 20px;
    border-bottom: 1px solid #f3f4f6;
    font-size: .84rem;
}
.pb-row:last-child { border-bottom: none; }
.pb-name { font-weight: 600; color: var(--acct-text); }
.pb-bal {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: .88rem;
}
.pb-bal.pos { color: #059669; }
.pb-bal.neg { color: #dc2626; }

/* Quick actions */
.qa-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    padding: 16px;
}
.qa-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 11px 14px;
    border: 1px solid var(--acct-border);
    border-radius: 7px;
    font-size: .8rem;
    font-weight: 600;
    color: #374151;
    text-decoration: none;
    background: #fff;
    transition: border-color .15s, background .15s, color .15s;
}
.qa-btn:hover { border-color: var(--acct-gold); color: var(--acct-gold-dk); background: #fffef5; }
.qa-btn i { font-size: 1.1rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="acct-page">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" style="border-left:4px solid #059669;font-size:.875rem;border-radius:6px">
            <i class="bx bx-check-circle me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="acct-kpi-grid">
        <div class="acct-kpi-card kpi-ar">
            <div class="acct-kpi-label">Accounts Receivable</div>
            <div class="acct-kpi-value green">$<?php echo e(number_format($netAR, 2)); ?></div>
            <div class="acct-kpi-sub">Net AR balance</div>
            <i class="bx bx-wallet acct-kpi-icon"></i>
        </div>
        <div class="acct-kpi-card kpi-sales">
            <div class="acct-kpi-label">Total Sales</div>
            <div class="acct-kpi-value">$<?php echo e(number_format($totalSales, 2)); ?></div>
            <div class="acct-kpi-sub"><span class="text-success fw-semibold">$<?php echo e(number_format($salesThisMonth, 2)); ?></span> this month</div>
            <i class="bx bx-trending-up acct-kpi-icon"></i>
        </div>
        <div class="acct-kpi-card kpi-cb">
            <div class="acct-kpi-label">Chargebacks</div>
            <div class="acct-kpi-value red">$<?php echo e(number_format($totalChargebacks, 2)); ?></div>
            <div class="acct-kpi-sub"><span class="text-danger fw-semibold">$<?php echo e(number_format($chargesThisMonth, 2)); ?></span> this month</div>
            <i class="bx bx-undo acct-kpi-icon"></i>
        </div>
        <div class="acct-kpi-card kpi-net">
            <div class="acct-kpi-label">Payments Received</div>
            <div class="acct-kpi-value indigo">$<?php echo e(number_format($totalPayments, 2)); ?></div>
            <div class="acct-kpi-sub"><?php echo e($totalEntries); ?> total journal entries</div>
            <i class="bx bx-money acct-kpi-icon"></i>
        </div>
    </div>

    
    <div class="acct-dash-grid">

        
        <div style="display:flex;flex-direction:column;gap:20px">

            
            <div class="acct-card">
                <div class="acct-card-header">
                    <span class="acct-card-title"><i class="bx bx-bar-chart-alt-2"></i> Monthly Sales vs Chargebacks</span>
                    <span style="font-size:.74rem;color:var(--acct-muted)">Last 6 months</span>
                </div>
                <div style="padding:20px">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>

            
            <div class="acct-card">
                <div class="acct-card-header">
                    <span class="acct-card-title"><i class="bx bx-list-ul"></i> Recent Journal Entries</span>
                    <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" style="font-size:.78rem;color:var(--acct-gold-dk);text-decoration:none;font-weight:600">
                        View all <i class="bx bx-chevron-right"></i>
                    </a>
                </div>
                <div style="overflow-x:auto">
                    <table class="acct-mini-table">
                        <thead>
                            <tr>
                                <th>Entry #</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th>Posted By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('admin.accounting.journal.show', $e->id)); ?>" class="entry-num">
                                        <?php echo e($e->entry_number); ?>

                                    </a>
                                </td>
                                <td style="color:var(--acct-muted);white-space:nowrap">
                                    <?php echo e($e->entry_date->format('d M Y')); ?>

                                </td>
                                <td>
                                    <?php $tmap=['sale'=>'t-sale','payment_received'=>'t-payment','opening_balance'=>'t-opening','chargeback'=>'t-chargeback','general'=>'t-general'] ?>
                                    <?php $tlabel=['sale'=>'Sale','payment_received'=>'Payment','opening_balance'=>'Opening','chargeback'=>'Chargeback','general'=>'General'] ?>
                                    <span class="t-badge <?php echo e($tmap[$e->type] ?? 't-general'); ?>"><?php echo e($tlabel[$e->type] ?? $e->type); ?></span>
                                </td>
                                <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    <?php echo e($e->description); ?>

                                </td>
                                <td class="text-end" style="font-family:'Courier New',monospace;font-weight:700;white-space:nowrap">
                                    $<?php echo e(number_format($e->total_debit, 2)); ?>

                                </td>
                                <td style="color:var(--acct-muted);font-size:.78rem"><?php echo e($e->creator?->name ?? '—'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.85rem">No entries yet</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div style="display:flex;flex-direction:column;gap:20px">

            
            <div class="acct-card">
                <div class="acct-card-header">
                    <span class="acct-card-title"><i class="bx bx-zap"></i> Quick Actions</span>
                </div>
                <div class="qa-grid">
                    <a href="<?php echo e(route('admin.accounting.record-sale')); ?>" class="qa-btn">
                        <i class="bx bx-purchase-tag" style="color:#059669"></i> Record Sale
                    </a>
                    <a href="<?php echo e(route('admin.accounting.record-payment')); ?>" class="qa-btn">
                        <i class="bx bx-money" style="color:#2563eb"></i> Payment In
                    </a>
                    <a href="<?php echo e(route('admin.accounting.record-chargeback')); ?>" class="qa-btn">
                        <i class="bx bx-undo" style="color:#dc2626"></i> Chargeback
                    </a>
                    <a href="<?php echo e(route('admin.accounting.sales-ledger')); ?>" class="qa-btn">
                        <i class="bx bx-book-content" style="color:#d4af37"></i> Sales Ledger
                    </a>
                    <a href="<?php echo e(route('admin.accounting.partner-ledger')); ?>" class="qa-btn">
                        <i class="bx bx-user-circle" style="color:#7c3aed"></i> Partner Ledger
                    </a>
                    <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" class="qa-btn">
                        <i class="bx bx-list-ul" style="color:#0891b2"></i> All Journals
                    </a>
                </div>
            </div>

            
            <div class="acct-card" style="flex:1">
                <div class="acct-card-header">
                    <span class="acct-card-title"><i class="bx bx-group"></i> AR by Partner</span>
                    <a href="<?php echo e(route('admin.accounting.sales-ledger')); ?>" style="font-size:.78rem;color:var(--acct-gold-dk);text-decoration:none;font-weight:600">
                        Full ledger <i class="bx bx-chevron-right"></i>
                    </a>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $partnerBalances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="pb-row">
                    <div>
                        <div class="pb-name"><?php echo e($pb->partner_name); ?></div>
                        <small style="color:var(--acct-muted);font-size:.72rem">
                            Dr $<?php echo e(number_format($pb->total_dr,2)); ?> &nbsp;/&nbsp; Cr $<?php echo e(number_format($pb->total_cr,2)); ?>

                        </small>
                    </div>
                    <div class="pb-bal <?php echo e($pb->balance >= 0 ? 'pos' : 'neg'); ?>">
                        <?php echo e($pb->balance >= 0 ? '' : '-'); ?>$<?php echo e(number_format(abs($pb->balance),2)); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-muted py-4" style="font-size:.85rem">No AR balances</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    var ctx = document.getElementById('trendChart').getContext('2d');
    var months  = <?php echo json_encode($trend->pluck('month'), 15, 512) ?>;
    var sales   = <?php echo json_encode($trend->pluck('sales')->map(fn($v) => (float)$v), 15, 512) ?>;
    var charges = <?php echo json_encode($trend->pluck('chargebacks')->map(fn($v) => (float)$v), 15, 512) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Sales',
                    data: sales,
                    backgroundColor: 'rgba(212,175,55,0.75)',
                    borderColor: '#b8941f',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Chargebacks',
                    data: charges,
                    backgroundColor: 'rgba(239,68,68,0.6)',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' $' + parseFloat(ctx.raw).toLocaleString('en-US',{minimumFractionDigits:2})
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        font: { size: 10 },
                        callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
                    }
                },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/dashboard.blade.php ENDPATH**/ ?>