<?php $__env->startSection('title'); ?>
    Manage Permissions - <?php echo e($role->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .permission-matrix {
            background: #fff;
            border-radius: 8px;
        }
        .permission-row {
            border-bottom: 1px solid #f0f0f0;
            padding: 12px 0;
            transition: background 0.2s;
        }
        .permission-row:hover {
            background: #f8f9fa;
        }
        .module-name {
            font-weight: 500;
            color: #495057;
        }
        .module-description {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .category-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), transparent);
            border-left: 4px solid #d4af37;
            padding: 12px 16px;
            margin: 20px 0 10px 0;
            border-radius: 4px;
        }
        .permission-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .permission-radio {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .permission-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 8px;
        }
        .col-permission {
            text-align: center;
            padding: 0 10px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        .legend-badge {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .badge-view { background: #ffc107; }
        .badge-edit { background: #0dcaf0; }
        .badge-full { background: #198754; }
        .badge-none { background: #dc3545; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <a href="<?php echo e(route('settings.permissions.index')); ?>">Permissions</a>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo e($role->name); ?> Permissions
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form action="<?php echo e(route('settings.permissions.roles.update', $role)); ?>" method="POST" id="permissionForm">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">
                                    <i class="bx bx-shield-alt me-2" style="color: #d4af37;"></i>
                                    <?php echo e($role->name); ?> Role Permissions
                                </h4>
                                <p class="text-muted mb-0">Configure access levels for all CRM modules</p>
                            </div>
                            <div>
                                <a href="<?php echo e(route('settings.permissions.index')); ?>" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Permissions
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Permission Levels:</strong>
                            <div class="mt-2">
                                <span class="legend-item">
                                    <span class="legend-badge badge-none"></span> None: No access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-view"></span> View: Read-only access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-edit"></span> Edit: Can view and modify (create/update)
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-full"></span> Full: Complete access (view/edit/delete)
                                </span>
                            </div>
                        </div>

                        <div class="permission-matrix">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modulesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $modules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="category-header">
                                    <h5 class="mb-0">
                                        <i class="bx bx-folder me-2"></i>
                                        <?php echo e($category); ?>

                                    </h5>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="permission-row row align-items-center">
                                        <div class="col-md-5">
                                            <div class="module-name"><?php echo e($module->name); ?></div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($module->description): ?>
                                                <div class="module-description"><?php echo e($module->description); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="row">
                                                <?php
                                                    $currentLevel = $permissions[$module->slug]['permission_level'] ?? 'none';
                                                ?>
                                                
                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="none" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($currentLevel === 'none' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-danger">None</div>
                                                    </label>
                                                </div>

                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="view" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($currentLevel === 'view' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-warning">View</div>
                                                    </label>
                                                </div>

                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="edit" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($currentLevel === 'edit' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-info">Edit</div>
                                                    </label>
                                                </div>

                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="full" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($currentLevel === 'full' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-success">Full</div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="<?php echo e(route('settings.permissions.index')); ?>" class="btn btn-secondary me-2">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Permissions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // Auto-save functionality (optional - can be enabled later)
        // This would use AJAX to save without full page reload
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/permissions/edit-role.blade.php ENDPATH**/ ?>