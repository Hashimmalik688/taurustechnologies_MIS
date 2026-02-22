<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1 class="h3">Audit Log Details</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?php echo e(route('admin.audit-logs.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Action Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Log ID:</dt>
                        <dd class="col-sm-8">#<?php echo e($auditLog->id); ?></dd>

                        <dt class="col-sm-4">Action:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $auditLog->action))); ?></span>
                        </dd>

                        <dt class="col-sm-4">Date & Time:</dt>
                        <dd class="col-sm-8"><?php echo e($auditLog->created_at->format('M d, Y H:i:s')); ?></dd>

                        <dt class="col-sm-4">Timestamp:</dt>
                        <dd class="col-sm-8"><code><?php echo e($auditLog->created_at->timestamp); ?></code></dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>User Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">User ID:</dt>
                        <dd class="col-sm-8">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditLog->user): ?>
                                <a href="<?php echo e(route('users.show', $auditLog->user->id)); ?>"><?php echo e($auditLog->user->id); ?></a>
                            <?php else: ?>
                                <span class="text-muted">System</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </dd>

                        <dt class="col-sm-4">User Email:</dt>
                        <dd class="col-sm-8">
                            <?php echo e($auditLog->user?->email ?? $auditLog->user_email ?? 'System'); ?>

                        </dd>

                        <dt class="col-sm-4">User Name:</dt>
                        <dd class="col-sm-8">
                            <?php echo e($auditLog->user?->name ?? 'System'); ?>

                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Affected Model</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Model Type:</dt>
                        <dd class="col-sm-8">
                            <?php echo e($auditLog->model ?? 'N/A'); ?>

                        </dd>

                        <dt class="col-sm-4">Model ID:</dt>
                        <dd class="col-sm-8">
                            <?php echo e($auditLog->model_id ?? 'N/A'); ?>

                        </dd>

                        <dt class="col-sm-4">Description:</dt>
                        <dd class="col-sm-8">
                            <?php echo e($auditLog->description ?? 'No description'); ?>

                        </dd>
                    </dl>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditLog->changes): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Changes Made</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php echo e(json_encode($auditLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h5>Request Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">IP Address:</dt>
                        <dd class="col-sm-7">
                            <code><?php echo e($auditLog->ip_address ?? 'N/A'); ?></code>
                        </dd>

                        <dt class="col-sm-5">Browser:</dt>
                        <dd class="col-sm-7">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditLog->user_agent): ?>
                                <small><?php echo e(Str::limit($auditLog->user_agent, 50)); ?></small>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </dd>
                    </dl>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditLog->user_agent): ?>
                        <div class="mt-3">
                            <label class="form-label text-muted">Full User Agent:</label>
 <code class="u-fs-075 text-break">
                                <?php echo e($auditLog->user_agent); ?>

                            </code>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-secondary">
                    <h5>Navigation</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('admin.audit-logs.index', ['action' => $auditLog->action])); ?>" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="fas fa-filter"></i> Show All "<?php echo e(ucfirst(str_replace('_', ' ', $auditLog->action))); ?>"
                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditLog->user): ?>
                        <a href="<?php echo e(route('admin.audit-logs.index', ['user_id' => $auditLog->user_id])); ?>" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-user"></i> Show All from <?php echo e($auditLog->user->email); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    pre {
        background: var(--bs-surface-50);
        padding: 12px;
        border-radius: 4px;
        max-height: 300px;
        overflow-y: auto;
        font-size: 0.85rem;
    }

    code {
        background: var(--bs-surface-50);
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/audit-logs/show.blade.php ENDPATH**/ ?>