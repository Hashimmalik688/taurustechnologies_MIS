<?php $__env->startSection('title'); ?>
    HR Operations
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('components.hub-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-user-check"></i>HR Operations</h4>
            <p>Manage employees, attendance, docking, and holidays</p>
        </div>

        <div class="hub-section-label">People</div>
        <div class="hub-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['Super Admin', 'Manager', 'Co-ordinator', 'HR', 'CEO'])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('ems')): ?>
                <a href="<?php echo e(route('employee.ems')); ?>" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-id-card"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Employee Management System</div>
                        <p class="hub-card-desc">View and manage employee profiles, roles, and organizational data</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['Super Admin', 'Manager', 'Co-ordinator', 'HR', 'CEO'])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('attendance')): ?>
                <a href="<?php echo e(route('attendance.index')); ?>" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Attendance</div>
                        <p class="hub-card-desc">Track daily check-ins, attendance reports, and time-off records</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="hub-section-label">Operations</div>
        <div class="hub-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['QA', 'HR', 'Super Admin', 'Manager', 'Co-ordinator', 'CEO'])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('dock')): ?>
                <a href="<?php echo e(route('dock.index')); ?>" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-dock-top"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Dock Management</div>
                        <p class="hub-card-desc">Manage salary docking records, deductions, and disciplinary adjustments</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['HR', 'Super Admin', 'Co-ordinator', 'CEO'])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('public-holidays')): ?>
                <a href="<?php echo e(route('admin.public-holidays.index')); ?>" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-calendar"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Public Holidays</div>
                        <p class="hub-card-desc">Configure public holidays and non-working days for attendance calculations</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/hr/hub.blade.php ENDPATH**/ ?>