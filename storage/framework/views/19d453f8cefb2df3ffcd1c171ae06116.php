<?php $__env->startSection('title'); ?>
    My Verifications
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <form method="GET" action="<?php echo e(route('verifier.dashboard')); ?>" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="<?php echo e(route('verifier.dashboard', ['filter' => 'today'])); ?>" class="pipe-pill <?php echo e($filter === 'today' ? 'active' : ''); ?>"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill <?php echo e($filter === 'custom' ? 'active' : ''); ?>" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:<?php echo e($filter === 'custom' ? 'flex' : 'none'); ?>;align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="date" name="start_date" class="pipe-pill-date" value="<?php echo e(request('start_date')); ?>">
            <span class="pipe-pill-lbl">TO</span>
            <input type="date" name="end_date" class="pipe-pill-date" value="<?php echo e(request('end_date')); ?>">
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter !== 'today'): ?>
            <a href="<?php echo e(route('verifier.dashboard', ['filter' => 'today'])); ?>" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-file k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['total_submitted'] ?? 0); ?></div>
            <div class="k-lbl">Total Submitted</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-user-pin k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['with_closer'] ?? 0); ?></div>
            <div class="k-lbl">With Closer</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-clipboard k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['with_validator'] ?? 0); ?></div>
            <div class="k-lbl">With Validator</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-double k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['sales'] ?? 0); ?></div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['declined'] ?? 0); ?></div>
            <div class="k-lbl">Declined</div>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-user-circle" style="color:#d4af37;"></i> Per-Closer Breakdown</h6>
            <span style="font-size:.6rem;color:var(--bs-surface-400);"><?php echo e($closerBreakdown->count()); ?> closer(s)</span>
        </div>
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Closer</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">With Closer</th>
                        <th class="text-center">With Validator</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Sales</th>
                        <th class="text-center">Declined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $closerBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($cb['name']); ?></strong></td>
                            <td class="text-center"><span class="v-badge v-blue"><?php echo e($cb['total']); ?></span></td>
                            <td class="text-center"><span class="v-badge v-teal"><?php echo e($cb['with_closer']); ?></span></td>
                            <td class="text-center"><span class="v-badge v-purple"><?php echo e($cb['with_validator']); ?></span></td>
                            <td class="text-center"><span class="v-badge v-warn"><?php echo e($cb['pending']); ?></span></td>
                            <td class="text-center"><span class="v-badge v-green"><?php echo e($cb['sales']); ?></span></td>
                            <td class="text-center"><span class="v-badge v-red"><?php echo e($cb['declined']); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No closer activity yet</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($closerBreakdown->count() > 0): ?>
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-center"><span class="v-badge v-blue"><?php echo e($closerBreakdown->sum('total')); ?></span></td>
                        <td class="text-center"><span class="v-badge v-teal"><?php echo e($closerBreakdown->sum('with_closer')); ?></span></td>
                        <td class="text-center"><span class="v-badge v-purple"><?php echo e($closerBreakdown->sum('with_validator')); ?></span></td>
                        <td class="text-center"><span class="v-badge v-warn"><?php echo e($closerBreakdown->sum('pending')); ?></span></td>
                        <td class="text-center"><span class="v-badge v-green"><?php echo e($closerBreakdown->sum('sales')); ?></span></td>
                        <td class="text-center"><span class="v-badge v-red"><?php echo e($closerBreakdown->sum('declined')); ?></span></td>
                    </tr>
                </tfoot>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-ul" style="color:#50a5f1;"></i> My Transferred Forms</h6>
            <a href="<?php echo e(route('verifier.create.team', 'peregrine')); ?>" class="act-btn a-success"><i class="bx bx-plus"></i> New Form</a>
        </div>
        <div class="scroll-tbl" style="max-height:400px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Date / Time</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Closer</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="white-space:nowrap;"><?php echo e($lead->verified_at ? $lead->verified_at->setTimezone('America/Denver')->format('M d, h:i A') : ($lead->created_at ? $lead->created_at->setTimezone('America/Denver')->format('M d, h:i A') : '—')); ?></td>
                            <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                            <td><?php echo e($lead->phone_number ?? '—'); ?></td>
                            <td><?php echo e($lead->closer_name ?? '—'); ?></td>
                            <td class="text-center">
                                <?php
                                    $sMap = [
                                        'transferred' => ['Transferred', 's-transferred'],
                                        'closed' => ['With Validator', 's-closed'],
                                        'sale' => ['Sale', 's-sale'],
                                        'declined' => [$lead->decline_reason ?? 'Declined', 's-declined'],
                                        'pending' => [$lead->pending_reason ?? 'Pending', 's-pending'],
                                        'returned' => ['Returned', 's-returned'],
                                    ];
                                    $s = $sMap[$lead->status] ?? [ucfirst($lead->status), 's-pending'];
                                ?>
                                <span class="s-pill <?php echo e($s[1]); ?>"><?php echo e($s[0]); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                            <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.3rem;"></i>
                            No forms submitted yet
                            <div style="margin-top:.4rem;"><a href="<?php echo e(route('verifier.create.team', 'peregrine')); ?>" class="act-btn a-primary"><i class="bx bx-plus"></i> Submit Your First Form</a></div>
                        </td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    // No additional JS needed — bubble pills use direct links/form submit
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/verifier/dashboard.blade.php ENDPATH**/ ?>