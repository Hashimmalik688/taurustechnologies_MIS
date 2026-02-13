<?php $__env->startSection('title'); ?>
    Partners Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Admin
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Partners
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary fw-bold mb-0">
                        <i class="mdi mdi-account-group me-2"></i>Partners Management
                    </h2>
                    <p class="text-muted">Manage external partners and their carrier assignments</p>
                </div>
                <a href="<?php echo e(route('admin.partners.create')); ?>" class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i>Add New Partner
                </a>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Partner Code</th>
                                    <th>Partner Name</th>
                                    <th>Email</th>
                                    <th>Carriers</th>
                                    <th>States</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $uniqueCarriers = $partner->carrierStates->pluck('insurance_carrier_id')->unique();
                                        $totalStates = $partner->carrierStates->pluck('state')->unique()->count();
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary"><?php echo e($partner->code); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        <?php echo e(substr($partner->name, 0, 2)); ?>

                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($partner->name); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->email): ?>
                                                <a href="mailto:<?php echo e($partner->email); ?>" class="text-muted">
                                                    <i class="mdi mdi-email me-1"></i><?php echo e($partner->email); ?>

                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                <?php echo e($uniqueCarriers->count()); ?> Carriers
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">
                                                <?php echo e($totalStates); ?> States
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->is_active): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="<?php echo e(route('admin.partners.show', $partner->id)); ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.partners.edit', $partner->id)); ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Partner">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.partners.destroy', $partner->id)); ?>" 
                                                      method="POST" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete <?php echo e($partner->name); ?>? This will remove all carrier assignments.');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Partner">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="mdi mdi-account-off mdi-48px mb-3 d-block"></i>
                                                <h5>No Partners Found</h5>
                                                <p>Click "Add New Partner" to create your first partner</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .avatar-sm {
            height: 2.5rem;
            width: 2.5rem;
        }

        .avatar-title {
            align-items: center;
            background-color: #556ee6;
            color: #fff;
            display: flex;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .bg-soft-primary {
            background-color: rgba(85, 110, 230, 0.1) !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/partners/index.blade.php ENDPATH**/ ?>