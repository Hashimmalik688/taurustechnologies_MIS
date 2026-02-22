<?php $__env->startSection('title'); ?>
    Manage Permissions - <?php echo e($user->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .permission-matrix {
            background: var(--bs-white, #fff);
            border-radius: 8px;
        }
        .permission-row {
            border-bottom: 1px solid var(--bs-print-bg-alt);
            padding: 12px 0;
            transition: background 0.2s;
        }
        .permission-row:hover {
            background: var(--bs-surface-bg-light);
        }
        .module-name {
            font-weight: 500;
            color: var(--bs-surface-600);
        }
        .module-description {
            font-size: 0.875rem;
            color: var(--bs-status-default);
        }
        .category-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), transparent);
            border-left: 4px solid var(--bs-gold);
            padding: 12px 16px;
            margin: 20px 0 10px 0;
            border-radius: 4px;
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
            padding: 0 8px;
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
        .badge-inherit { background: var(--bs-status-default); }
        .badge-view { background: var(--bs-status-leave); }
        .badge-edit { background: var(--bs-info); }
        .badge-full { background: var(--bs-ui-success); }
        .badge-none { background: var(--bs-status-absent); }
        .inherited-badge {
            background: var(--bs-surface-200);
            color: var(--bs-status-default);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }
        .override-badge {
            background: var(--bs-surface-50);
            color: var(--bs-gold-dark);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <a href="<?php echo e(route('users.index')); ?>">Users</a>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo e($user->name); ?> - Permission Overrides
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

    <form action="<?php echo e(route('settings.permissions.users.update', $user)); ?>" method="POST" id="permissionForm">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">
 <i class="bx bx-user me-2 text-gold" ></i>
                                    <?php echo e($user->name); ?> - Permission Overrides
                                </h4>
                                <p class="text-muted mb-0">
                                    User Roles: 
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-secondary"><?php echo e($roleName); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Overrides
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>About Permission Overrides:</strong>
                            <div class="mt-2">
                                <ul class="mb-0">
                                    <li>By default, users inherit permissions from their assigned roles</li>
                                    <li>Use overrides to grant or restrict access for this specific user</li>
                                    <li>Select "Inherit from Role" to use the role's default permission</li>
                                    <li>Overridden permissions are marked with <span class="override-badge">OVERRIDE</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <div class="mt-2">
                                <span class="legend-item">
                                    <span class="legend-badge badge-inherit"></span> Inherit: Use role permission
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-none"></span> None: Explicitly deny access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-view"></span> View: Read-only access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-edit"></span> Edit: View and modify
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-full"></span> Full: Complete access
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
                                    <?php
                                        $permission = $permissions[$module->slug];
                                        $currentLevel = $permission['permission_level'];
                                        $source = $permission['source'];
                                        $isOverride = $source === 'override';
                                    ?>

                                    <div class="permission-row row align-items-center">
                                        <div class="col-md-4">
                                            <div class="module-name">
                                                <?php echo e($module->name); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOverride): ?>
                                                    <span class="override-badge">OVERRIDE</span>
                                                <?php else: ?>
                                                    <span class="inherited-badge">FROM ROLE</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($module->description): ?>
                                                <div class="module-description"><?php echo e($module->description); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="inherit" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e(!$isOverride ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-secondary">Inherit</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="none" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($isOverride && $currentLevel === 'none' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-danger">None</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="view" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($isOverride && $currentLevel === 'view' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-warning">View</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="edit" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($isOverride && $currentLevel === 'edit' ? 'checked' : ''); ?>>
                                                        <div class="permission-label text-info">Edit</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[<?php echo e($module->slug); ?>]" 
                                                               value="full" 
                                                               class="form-check-input permission-radio"
                                                               <?php echo e($isOverride && $currentLevel === 'full' ? 'checked' : ''); ?>>
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
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary me-2">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Overrides
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
        // Highlight changed permissions
        document.querySelectorAll('.permission-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const row = this.closest('.permission-row');
                row.style.background = themeColors.goldLight;
                setTimeout(() => {
                    row.style.background = '';
                }, 2000);
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/permissions/edit-user.blade.php ENDPATH**/ ?>