<?php $__env->startSection('title', 'Account Switching Log'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="mb-4">Account Switching Log</h1>
    <p class="text-muted">Device fingerprints used by multiple users. This may indicate account sharing or suspicious logins.</p>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Device Fingerprint</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Login Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($log->device_fingerprint); ?></td>
                        <td><?php echo e($log->user ? $log->user->name : 'Unknown'); ?></td>
                        <td><?php echo e($log->user ? $log->user->email : $log->user_email); ?></td>
                        <td><?php echo e($log->ip_address); ?></td>
                        <td><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center">No suspicious account switching detected.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/account-switching-log.blade.php ENDPATH**/ ?>