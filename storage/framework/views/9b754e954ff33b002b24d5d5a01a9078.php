<?php $__env->startSection('title'); ?>
    Edit Public Holiday
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <a href="<?php echo e(route('admin.public-holidays.index')); ?>">Public Holidays</a>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Edit Holiday
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-pencil me-2"></i>Edit Public Holiday
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.public-holidays.update', $holiday)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="date" 
                                   name="date" 
                                   value="<?php echo e(old('date', $holiday->date->format('Y-m-d'))); ?>" 
                                   required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <small class="text-muted">Select the date of the public holiday</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name', $holiday->name)); ?>" 
                                   placeholder="e.g., New Year's Day, Eid ul-Fitr"
                                   required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Add any notes about this holiday..."><?php echo e(old('description', $holiday->description)); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       <?php echo e(old('is_active', $holiday->is_active) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (Attendance will be skipped on this day)
                                </label>
                            </div>
                            <small class="text-muted">When active, employees won't be marked absent on this day</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i>Update Holiday
                            </button>
                            <a href="<?php echo e(route('admin.public-holidays.index')); ?>" class="btn btn-secondary">
                                <i class="mdi mdi-close me-1"></i>Cancel
                            </a>
                            <?php if(auth()->check() && auth()->user()->canDeleteInModule('holidays')): ?>
                            <button type="button" 
                                    class="btn btn-danger float-end" 
                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this holiday?')) document.getElementById('delete-form').submit();">
                                <i class="mdi mdi-delete me-1"></i>Delete Holiday
                            </button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <form id="delete-form" 
                          action="<?php echo e(route('admin.public-holidays.destroy', $holiday)); ?>" 
                          method="POST" 
 >
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                    </form>
                </div>
            </div>

            <!-- Holiday Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-information-outline me-2"></i>Holiday Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Created:</strong> <?php echo e($holiday->created_at->format('M d, Y g:i A')); ?></p>
                            <p class="mb-2"><strong>Last Updated:</strong> <?php echo e($holiday->updated_at->format('M d, Y g:i A')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Days Until:</strong> 
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->date->isFuture()): ?>
                                    <?php echo e($holiday->date->diffForHumans()); ?>

                                <?php elseif($holiday->date->isToday()): ?>
                                    <span class="badge bg-success">Today</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Past</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p class="mb-2"><strong>Day of Week:</strong> <?php echo e($holiday->date->format('l')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/public-holidays/edit.blade.php ENDPATH**/ ?>