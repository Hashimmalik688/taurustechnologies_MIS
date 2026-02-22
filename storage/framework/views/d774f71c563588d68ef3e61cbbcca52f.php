<?php $__env->startSection('title'); ?>
    Public Holidays
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .holiday-card {
            transition: all 0.3s ease;
        }
        .holiday-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .holiday-date {
            font-size: 2rem;
            font-weight: bold;
        }
        .badge-upcoming {
            background-color: var(--bs-chart-primary);
        }
        .badge-past {
            background-color: var(--bs-surface-muted);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Admin
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Public Holidays
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Upcoming Holidays Section -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingHolidays->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary-subtle">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-calendar-star me-2"></i>Upcoming Holidays
                    </h5>
                    <div class="row">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upcomingHolidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mb-3">
                            <div class="card holiday-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="holiday-date text-primary">
                                                <?php echo e($holiday->date->format('d')); ?>

                                            </div>
                                            <div class="text-muted">
                                                <?php echo e($holiday->date->format('M Y')); ?>

                                            </div>
                                        </div>
                                        <span class="badge badge-upcoming"><?php echo e($holiday->date->diffForHumans()); ?></span>
                                    </div>
                                    <h6 class="mt-3 mb-1"><?php echo e($holiday->name); ?></h6>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->description): ?>
                                    <p class="text-muted mb-0 small"><?php echo e(Str::limit($holiday->description, 100)); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Holidays List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-calendar-multiple me-2"></i>All Public Holidays
                        </h4>
                        <a href="<?php echo e(route('admin.public-holidays.create')); ?>" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i>Add Holiday
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holidays->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Holiday Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($holiday->date->format('d M Y')); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-info"><?php echo e($holiday->date->format('l')); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo e($holiday->name); ?></strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->date->isPast()): ?>
                                            <span class="badge badge-past ms-2">Past</span>
                                        <?php elseif($holiday->date->isToday()): ?>
                                            <span class="badge bg-success ms-2">Today</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->description): ?>
                                            <small class="text-muted"><?php echo e(Str::limit($holiday->description, 50)); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
 <form class="d-inline" action="<?php echo e(route('admin.public-holidays.toggle', $holiday)); ?>" method="POST" >
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-link p-0">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->is_active): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if(auth()->check() && auth()->user()->canEditModule('holidays')): ?>
                                            <a href="<?php echo e(route('admin.public-holidays.edit', $holiday)); ?>" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if(auth()->check() && auth()->user()->canDeleteInModule('holidays')): ?>
                                            <form action="<?php echo e(route('admin.public-holidays.destroy', $holiday)); ?>" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this holiday?');"
 >
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="mdi mdi-delete"></i>
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

                    <div class="mt-3">
                        <?php echo e($holidays->links()); ?>

                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="mdi mdi-calendar-remove display-4 text-muted"></i>
                        <h5 class="mt-3">No holidays configured</h5>
                        <p class="text-muted">Add your first public holiday to manage attendance on special days.</p>
                        <a href="<?php echo e(route('admin.public-holidays.create')); ?>" class="btn btn-primary mt-3">
                            <i class="mdi mdi-plus me-1"></i>Add Holiday
                        </a>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/public-holidays/index.blade.php ENDPATH**/ ?>