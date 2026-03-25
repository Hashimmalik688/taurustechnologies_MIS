<?php $__env->startSection('title'); ?>
    Sales Operations Hub
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('components.hub-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-briefcase-alt"></i> Sales Operations</h4>
            <p>Sales records, QA review, policy submissions, bank verification &amp; analytics</p>
        </div>

        <?php $user = auth()->user(); ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canViewModule('sales') || $user->canViewModule('issuance') || $user->canViewModule('pendings-approved') || $user->canViewModule('pending-draft') || $user->canViewModule('paid-sales')): ?>
        <div class="hub-section-label">Records &amp; Pipeline</div>
        <div class="hub-grid">
            <?php if(auth()->check() && auth()->user()->canViewModule('sales')): ?>
            <a href="<?php echo e(route('sales.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-dollar-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Sales Records</div>
                    <p class="hub-card-desc">View, filter and manage all closed sales across teams</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('pendings-approved')): ?>
            <a href="<?php echo e(route('submissions.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-task"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Pendings Approved</div>
                    <p class="hub-card-desc">Manager-approved leads awaiting submission to Pending Contract</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('issuance')): ?>
            <a href="<?php echo e(route('issuance.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-send"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Pending Contract</div>
                    <p class="hub-card-desc">Track and process insurance policy submissions pending contract</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="<?php echo e(route('followup.my-followups')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-phone-outgoing"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">My Followups</div>
                    <p class="hub-card-desc">Issued leads assigned to you awaiting closer confirmation</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="<?php echo e(route('followup.followup-done')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Followup Done</div>
                    <p class="hub-card-desc">Leads confirmed by closers, ready for Pending Draft assignment</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="<?php echo e(route('followup.report')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Followup Report</div>
                    <p class="hub-card-desc">Review all followup assignments and outcomes</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('pending-draft')): ?>
            <a href="<?php echo e(route('pending-draft.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-time-five"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Pending Draft</div>
                    <p class="hub-card-desc">Leads awaiting first premium draft — mark Not Paid (FDFP) or Paid</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('paid-sales')): ?>
            <a href="<?php echo e(route('paid-sales.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-badge-check"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Paid Sales</div>
                    <p class="hub-card-desc">Successfully collected first draft — final paid sales records</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(auth()->check() && auth()->user()->canViewModule('qa-review')): ?>
        <div class="hub-section-label">Quality Assurance</div>
        <div class="hub-grid">
            <a href="<?php echo e(route('qa.review')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">QA Review</div>
                    <p class="hub-card-desc">Listen to calls and evaluate sales quality for each record</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="<?php echo e(route('qa.scoring')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-shield-quarter"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">QA Scoring</div>
                    <p class="hub-card-desc">Score cards, rubrics and agent performance benchmarks</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>
        <?php endif; ?>

        
        <?php if(auth()->check() && auth()->user()->canViewModule('bank-verification')): ?>
        <div class="hub-section-label">Verification</div>
        <div class="hub-grid">
            <a href="<?php echo e(route('bank-verification.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-shield"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Bank Verification</div>
                    <p class="hub-card-desc">Verify client banking details and update verification status</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>
        <?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canViewModule('revenue-analytics') || $user->canViewModule('live-analytics')): ?>
        <div class="hub-section-label">Analytics</div>
        <div class="hub-grid">
            <?php if(auth()->check() && auth()->user()->canViewModule('revenue-analytics')): ?>
            <a href="<?php echo e(route('revenue-analytics.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-line-chart"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Revenue Analytics</div>
                    <p class="hub-card-desc">Track revenue trends, carrier breakdown and monthly performance</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('live-analytics')): ?>
            <a href="<?php echo e(route('analytics.live')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-pulse"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Live Analytics</div>
                    <p class="hub-card-desc">Real-time sales activity and team performance dashboard</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/sales/hub.blade.php ENDPATH**/ ?>