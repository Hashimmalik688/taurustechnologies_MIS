<?php $__env->startSection('title'); ?>
    Public Holidays
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Settings
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Public Holidays
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card border-primary">
                <div class="card-header bg-primary-subtle">
                    <h5 class="mb-0 text-primary"><i class="mdi mdi-calendar-star me-2"></i>Upcoming Holidays</h5>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingHolidays->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upcomingHolidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo e($holiday->name); ?></h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="mdi mdi-calendar"></i> <?php echo e($holiday->date->format('M d, Y')); ?> (<?php echo e($holiday->date->diffForHumans()); ?>)
                                            </p>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->description): ?>
                                                <p class="mb-0 text-muted small"><?php echo e(Str::limit($holiday->description, 50)); ?></p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->is_recurring): ?>
                                            <span class="badge badge-soft-info">Recurring</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="mdi mdi-calendar-blank text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">No upcoming holidays</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="mdi mdi-calendar-multiple me-2"></i>All Holidays</h5>
                    <a href="<?php echo e(route('admin.holidays.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i>Add Holiday
                    </a>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holidays->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Holiday Name</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($holiday->date->format('M d, Y')); ?></strong><br>
                                                <small class="text-muted"><?php echo e($holiday->date->format('l')); ?></small>
                                            </td>
                                            <td><?php echo e($holiday->name); ?></td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->description): ?>
                                                    <?php echo e(Str::limit($holiday->description, 60)); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->is_recurring): ?>
                                                    <span class="badge badge-soft-info">Recurring</span>
                                                <?php else: ?>
                                                    <span class="badge badge-soft-secondary">One-time</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if(auth()->check() && auth()->user()->canEditModule('holidays')): ?>
                                                <a href="<?php echo e(route('admin.holidays.edit', $holiday)); ?>" 
                                                   class="btn btn-sm btn-soft-primary">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if(auth()->check() && auth()->user()->canDeleteInModule('holidays')): ?>
                                                <form action="<?php echo e(route('admin.holidays.destroy', $holiday)); ?>" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-soft-danger">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <?php echo e($holidays->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-blank text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3">No holidays configured</h5>
                            <p class="text-muted">Start by adding your first holiday</p>
                            <a href="<?php echo e(route('admin.holidays.create')); ?>" class="btn btn-primary mt-2">
                                <i class="mdi mdi-plus me-1"></i>Add Holiday
                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/holidays/index.blade.php ENDPATH**/ ?>