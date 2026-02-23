<?php $__env->startSection('title'); ?>
    My Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    /* Status mini-pills for sales table */
    .st-pill { display:inline-block;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:700;text-transform:capitalize; }
    .st-accepted { background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.15); }
    .st-underwritten { background:rgba(59,130,246,.1);color:#3b82f6;border:1px solid rgba(59,130,246,.15); }
    .st-pending { background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.15); }
    .st-declined { background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.15); }
    .st-chargeback { background:rgba(107,114,128,.1);color:#4b5563;border:1px solid rgba(107,114,128,.15); }
    /* Search input in filter bar */
    .pipe-search {
        font-size:.72rem; font-weight:600; padding:.32rem .55rem .32rem 1.8rem;
        border-radius:22px; border:1px solid rgba(0,0,0,.08);
        background:var(--bs-card-bg); color:var(--bs-surface-600);
        outline:none; min-width:160px; transition:border-color .15s;
    }
    .pipe-search:focus { border-color:#d4af37; box-shadow:0 0 0 2px rgba(212,175,55,.12); }
    .pipe-search-wrap { position:relative;display:inline-flex;align-items:center; }
    .pipe-search-wrap i { position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <form method="GET" action="<?php echo e(route('ravens.dashboard')); ?>" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="<?php echo e(route('ravens.dashboard', ['filter' => 'today'])); ?>" class="pipe-pill <?php echo e(($filter ?? 'today') === 'today' ? 'active' : ''); ?>"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill <?php echo e(($filter ?? '') === 'custom' ? 'active' : ''); ?>" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:<?php echo e(($filter ?? '') === 'custom' ? 'flex' : 'none'); ?>;align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="<?php echo e(request('start_date')); ?>" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="<?php echo e(request('end_date')); ?>" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <div class="pipe-search-wrap">
            <i class="bx bx-search"></i>
            <input type="text" name="search" class="pipe-search" value="<?php echo e($search ?? ''); ?>" placeholder="Search name, phone…">
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($filter ?? 'today') !== 'today' || !empty($search)): ?>
            <a href="<?php echo e(route('ravens.dashboard', ['filter' => 'today'])); ?>" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-phone k-icon"></i>
            <div class="k-val"><?php echo e($stats['dialed'] ?? 0); ?></div>
            <div class="k-lbl">Dialed</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-phone-call k-icon"></i>
            <div class="k-val"><?php echo e($stats['calls_connected'] ?? 0); ?></div>
            <div class="k-lbl">Connected</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val"><?php echo e($stats['sales'] ?? 0); ?></div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-gold ex-card">
            <i class="bx bx-trophy k-icon"></i>
            <div class="k-val"><?php echo e($stats['mtd_sales'] ?? 0); ?></div>
            <div class="k-lbl">MTD Sales</div>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#1a8754;">
            <i class="bx bx-dollar-circle" style="color:#34c38f;"></i> My Sales Records
            <span class="badge-count"><?php echo e($mySales->total() ?? 0); ?></span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($mySales) && $mySales->count() > 0): ?>
            
            <div style="display:flex;gap:.4rem;flex-wrap:wrap;padding:.3rem .65rem .5rem;">
                <span class="st-pill st-accepted"><i class="bx bx-check"></i> Accepted: <?php echo e($mySales->where('status','accepted')->count()); ?></span>
                <span class="st-pill st-underwritten"><i class="bx bx-edit"></i> Underwritten: <?php echo e($mySales->where('status','underwritten')->count()); ?></span>
                <span class="st-pill st-pending"><i class="bx bx-time"></i> Pending: <?php echo e($mySales->where('status','pending')->count()); ?></span>
                <span class="st-pill st-declined"><i class="bx bx-x"></i> Declined: <?php echo e($mySales->where('status','declined')->count()); ?></span>
            </div>

            <div class="scroll-tbl" style="max-height:400px;">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Coverage</th>
                            <th class="text-end">Premium</th>
                            <th>Carrier</th>
                            <th class="text-center">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mySales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($mySales->firstItem() + $index); ?></td>
                                <td>
                                    <strong><?php echo e($sale->cn_name ?? 'N/A'); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->phone_number): ?>
                                        <br><span style="font-size:.62rem;color:var(--bs-surface-400);"><?php echo e($sale->phone_number); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="white-space:nowrap;"><?php echo e($sale->sale_at ? $sale->sale_at->setTimezone('America/Denver')->format('M d, h:i A') : ($sale->sale_date ? $sale->sale_date->format('M d, Y') : 'N/A')); ?></td>
                                <td class="text-center">
                                    <?php $stClass = 'st-'.($sale->status ?? 'pending'); ?>
                                    <span class="st-pill <?php echo e($stClass); ?>"><?php echo e(ucfirst($sale->status ?? 'pending')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->qa_status): ?>
                                        <br><span style="font-size:.55rem;color:var(--bs-surface-400);">QA: <?php echo e($sale->qa_status); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->coverage_amount): ?>
                                        <strong>$<?php echo e(number_format($sale->coverage_amount, 0)); ?></strong>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-400);">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->monthly_premium): ?>
                                        <strong>$<?php echo e(number_format($sale->monthly_premium, 2)); ?></strong>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-400);">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($sale->carrier_name ?? 'N/A'); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('sales.index')); ?>?search=<?php echo e($sale->phone_number); ?>" class="act-btn a-primary" title="View in Sales"><i class="bx bx-show"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .65rem;border-top:1px solid rgba(0,0,0,.04);font-size:.68rem;color:var(--bs-surface-400);">
                <span>Showing <?php echo e($mySales->firstItem()); ?> to <?php echo e($mySales->lastItem()); ?> of <?php echo e($mySales->total()); ?></span>
                <div><?php echo e($mySales->links()); ?></div>
            </div>
        <?php else: ?>
            <div style="text-align:center;padding:2rem 1rem;color:var(--bs-surface-400);">
                <i class="bx bx-package" style="font-size:2rem;display:block;margin-bottom:.4rem;"></i>
                <p style="font-size:.8rem;font-weight:600;margin-bottom:.3rem;">No sales yet</p>
                <p style="font-size:.72rem;margin-bottom:.6rem;">Start calling leads to make your first sale!</p>
                <a href="<?php echo e(route('ravens.calling')); ?>" class="act-btn a-primary" style="padding:.35rem .75rem;">
                    <i class="bx bx-phone-call"></i> Start Calling
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#c84646;">
            <i class="bx bx-error-circle" style="color:#f46a6a;"></i> Declined & Chargebacks
            <span class="badge-count"><?php echo e($declinedChargebacks->count()); ?></span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-center">Status</th>
                        <th>Carrier</th>
                        <th class="text-end">Coverage</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $declinedChargebacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                            <td><?php echo e($lead->phone_number ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status === 'chargeback'): ?>
                                    <span class="st-pill st-chargeback">Chargeback</span>
                                <?php else: ?>
                                    <span class="st-pill st-declined">Declined</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                            <td class="text-end">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->coverage_amount): ?>
                                    $<?php echo e(number_format($lead->coverage_amount, 0)); ?>

                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;"><?php echo e($lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-smile"></i> No declined or chargebacks</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
    // Submit search on Enter key
    document.querySelector('.pipe-search')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('filterForm').submit(); }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/dashboard.blade.php ENDPATH**/ ?>