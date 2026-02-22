<?php $__env->startSection('title'); ?>
    Settings
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('components.hub-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-cog"></i>Settings</h4>
            <p>Manage your system configuration, permissions, and tools</p>
        </div>

        <div class="hub-section-label">General</div>
        <div class="hub-grid">
            <?php if(auth()->check() && auth()->user()->canViewModule('settings')): ?>
            <a href="<?php echo e(route('settings.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-slider-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">System Settings</div>
                    <p class="hub-card-desc">Configure attendance, office networks, notifications, and general system behavior</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin')): ?>
            <a href="<?php echo e(route('settings.permissions.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-shield-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Permissions Manager</div>
                    <p class="hub-card-desc">Manage role-based permissions and user access controls across the system</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="hub-section-label">Tools</div>
        <div class="hub-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|Co-ordinator|CEO')): ?>
            <a href="<?php echo e(route('settings.reports.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-bar-chart-alt-2"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Reports</div>
                    <p class="hub-card-desc">Generate sales, partner, agent, and manager reports with advanced filters and CSV export</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('duplicate-checker')): ?>
            <a href="<?php echo e(route('admin.dupe-checker.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-copy-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Duplicate Checker</div>
                    <p class="hub-card-desc">Find and manage duplicate records in the system to keep your data clean</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('account-switch-log')): ?>
            <a href="<?php echo e(route('admin.account-switching-log')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-transfer"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Account Switch Log</div>
                    <p class="hub-card-desc">View audit trail of admin account impersonation and switching activity</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin')): ?>
            <a href="<?php echo e(route('settings.chat-shadow.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-show"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Chat Shadowing</div>
                    <p class="hub-card-desc">Monitor and review conversations between users in read-only mode</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/settings/hub.blade.php ENDPATH**/ ?>