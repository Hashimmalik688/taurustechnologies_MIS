<?php $__env->startSection('title'); ?>
    Leads Hub
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('components.hub-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-clipboard"></i> Leads</h4>
            <p>Peregrine leads, Raven leads &amp; bad lead management</p>
        </div>

        <?php $user = auth()->user(); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canViewModule('leads-peregrine') || $user->canViewModule('leads')): ?>
        <div class="hub-section-label">Team Leads</div>
        <div class="hub-grid">
            <?php if(auth()->check() && auth()->user()->canViewModule('leads-peregrine')): ?>
            <a href="<?php echo e(route('leads.peregrine')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-user-voice"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Peregrine Leads</div>
                    <p class="hub-card-desc">View and manage all Peregrine team leads and applications</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('leads')): ?>
            <a href="<?php echo e(route('leads.index')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-briefcase"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Raven Leads</div>
                    <p class="hub-card-desc">View and manage all Ravens team leads and follow-ups</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(auth()->check() && auth()->user()->canViewModule('ravens-bad-leads')): ?>
        <div class="hub-section-label">Lead Management</div>
        <div class="hub-grid">
            <a href="<?php echo e(route('ravens.bad-leads')); ?>" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-x-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Bad Leads</div>
                    <p class="hub-card-desc">Review and manage rejected or unqualified leads</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/leads/hub.blade.php ENDPATH**/ ?>