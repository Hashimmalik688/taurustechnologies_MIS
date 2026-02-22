<?php $__env->startSection('title', 'My Attendance'); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Attendance
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            My Attendance
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-body">
                    <h4 class="card-title">Last 30 Days</h4>
                    <p class="text-muted">Showing daily attendance for the previous 30 days.</p>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Login</th>
                                    <th>Logout</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $a = $day['attendance']; ?>
                                    <tr>
                                        <td><?php echo e(\Carbon\Carbon::parse($day['date'])->format('M d, Y')); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a): ?>
                                                <span class="badge bg-<?php echo e($a->status === 'present' ? 'success' : 
                                                    ($a->status === 'late' ? 'warning' : 
                                                    ($a->status === 'half_day' ? 'info' : 
                                                    ($a->status === 'paid_leave' ? 'primary' : 'danger')))); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $a->status))); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">No record</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><?php echo e($a ? ($a->formatted_login_time ?? ($a->login_time?->format('H:i') ?? 'N/A')) : '-'); ?></td>
                                        <td><?php echo e($a ? ($a->formatted_logout_time ?? ($a->logout_time?->format('H:i') ?? 'N/A')) : '-'); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->isStillWorking()): ?>
                                                    <span class="text-primary fw-semibold"><?php echo e($a->getFormattedCurrentWorkingHours()); ?></span>
                                                <?php else: ?>
                                                    <?php echo e($a->working_hours ?? 0); ?>h
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/attendance/me.blade.php ENDPATH**/ ?>