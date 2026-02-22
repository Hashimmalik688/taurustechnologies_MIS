<?php $__env->startSection('title'); ?> Edit Project - <?php echo e($project->name); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .form-section { background: var(--bs-white, #fff); border-radius: 16px; padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid var(--bs-surface-200); }
    .form-section h5 { color: var(--bs-surface-900); font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid var(--bs-print-bg-alt); }
    .form-section h5 i { color: var(--bs-gradient-start); margin-right: 8px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('epms.index')); ?>">EPMS</a> <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Edit: <?php echo e($project->name); ?> <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <form action="<?php echo e(route('epms.update', $project)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="form-section">
                    <h5><i class="bx bx-briefcase-alt"></i> Project Information</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Project Name *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(old('name', $project->name)); ?>" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">Select</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Web Application','Mobile App','API/Backend','Desktop App','Data/Analytics','AI/ML','DevOps','Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>" <?php echo e(old('category', $project->category) == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?php echo e(old('description', $project->description)); ?></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Objectives</label>
                            <textarea class="form-control" name="objectives" rows="2"><?php echo e(old('objectives', $project->objectives)); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tech Stack</label>
                            <input type="text" class="form-control" name="tech_stack" value="<?php echo e(old('tech_stack', $project->tech_stack)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Repository URL</label>
                            <input type="url" class="form-control" name="repository_url" value="<?php echo e(old('repository_url', $project->repository_url)); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-section">
                    <h5><i class="bx bx-cog"></i> Settings</h5>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['planning','in-progress','on-hold','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>" <?php echo e(old('status', $project->status) == $s ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('-', ' ', $s))); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Methodology *</label>
                        <select class="form-select" name="methodology" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['agile','kanban','waterfall','hybrid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m); ?>" <?php echo e(old('methodology', $project->methodology) == $m ? 'selected' : ''); ?>><?php echo e(ucfirst($m)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority *</label>
                        <select class="form-select" name="priority" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['low','medium','high','critical']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p); ?>" <?php echo e(old('priority', $project->priority) == $p ? 'selected' : ''); ?>><?php echo e(ucfirst($p)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo e(old('start_date', $project->start_date->format('Y-m-d'))); ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Deadline *</label>
                            <input type="date" class="form-control" name="deadline" value="<?php echo e(old('deadline', $project->deadline->format('Y-m-d'))); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Currency *</label>
                            <select class="form-select" name="currency" required>
                                <option value="PKR" <?php echo e(old('currency', $project->currency) == 'PKR' ? 'selected' : ''); ?>>PKR</option>
                                <option value="USD" <?php echo e(old('currency', $project->currency) == 'USD' ? 'selected' : ''); ?>>USD</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Budget</label>
                            <input type="number" class="form-control" name="budget" value="<?php echo e(old('budget', $project->budget)); ?>" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project Manager</label>
                        <select class="form-select" name="project_manager_id">
                            <option value="">Select PM</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e(old('project_manager_id', $project->project_manager_id) == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
 <button type="submit" class="btn btn-success flex-fill py-3 u-rounded-12 u-fw-600" >
                        <i class="bx bx-check me-1"></i> Update Project
                    </button>
 <a href="<?php echo e(route('epms.show', $project)); ?>" class="btn btn-outline-secondary py-3 u-rounded-12" >Cancel</a>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/epms/edit.blade.php ENDPATH**/ ?>