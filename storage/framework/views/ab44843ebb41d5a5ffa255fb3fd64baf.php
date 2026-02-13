<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1 class="h3">Audit Logs</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?php echo e(route('audit-logs.export')); ?><?php echo e(request()->getQueryString() ? '?' . request()->getQueryString() : ''); ?>" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('audit-logs.index')); ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="action" class="form-label">Action</label>
                    <select name="action" id="action" class="form-select">
                        <option value="">All Actions</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($act); ?>" <?php echo e(request('action') == $act ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst(str_replace('_', ' ', $act))); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>

                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>

                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>IP Address</th>
                        <th>Date/Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong>#<?php echo e($log->id); ?></strong></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->user): ?>
                                    <span class="badge bg-primary"><?php echo e($log->user->email); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e($log->user_email); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $log->action))); ?></span>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->model): ?>
                                    <?php echo e($log->model); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->model_id): ?>
                                        <small class="text-muted">#<?php echo e($log->model_id); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <code><?php echo e($log->ip_address ?? 'N/A'); ?></code>
                            </td>
                            <td><?php echo e($log->created_at->format('M d, Y H:i:s')); ?></td>
                            <td>
                                <a href="<?php echo e(route('audit-logs.show', $log->id)); ?>" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No audit logs found
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            <?php echo e($auditLogs->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/audit-logs/index.blade.php ENDPATH**/ ?>