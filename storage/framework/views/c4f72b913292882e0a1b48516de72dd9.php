<?php $__env->startSection('title', 'Announcements Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-bell"></i> System Announcements
            </h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Announcement::class)): ?>
                <a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Create Announcement
                </a>
            <?php endif; ?>
        </div>

        <!-- Alerts -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message = session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i><?php echo e($message); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                <ul class="mb-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Announcements Table -->
        <div class="card">
            <div class="card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($announcements->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Animation</th>
                                    <th>Auto Dismiss</th>
                                    <th>Created By</th>
                                    <th>Published</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($announcement->title); ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-muted small" title="<?php echo e($announcement->message); ?>">
                                                <?php echo e(Str::limit($announcement->message, 50)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($announcement->is_active): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-gold text-surface-700">
                                                <?php echo e(ucfirst($announcement->animation)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?php echo e($announcement->auto_dismiss); ?></small>
                                        </td>
                                        <td>
                                            <small><?php echo e($announcement->createdBy->name ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?php echo e($announcement->published_at->format('M d, Y H:i') ?? '-'); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if(auth()->check() && auth()->user()->canEditModule('settings')): ?>
                                                <a href="<?php echo e(route('admin.announcements.edit', $announcement->id)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bx bx-pencil"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if(auth()->check() && auth()->user()->canDeleteInModule('settings')): ?>
 <form class="d-inline" method="POST" action="<?php echo e(route('admin.announcements.destroy', $announcement->id)); ?>" >
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($announcements->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bx bx-bell-off text-surface-300" style="font-size: 3rem"></i>
                        <p class="text-muted mt-3">No announcements yet. Create one to get started!</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .page-wrapper {
        padding: 1.5rem 0;
    }

    .page-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .btn-group {
        gap: 0.25rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/announcement/index.blade.php ENDPATH**/ ?>