<?php $__env->startSection('title', 'Create Project - PABS'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('title'); ?> Create New Project <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Create Project Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Step 1: Initiation (The Request)</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('pabs.projects.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <!-- Section -->
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="section_id" class="form-select <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Select Section --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e(old('section_id') == $id ? 'selected' : ''); ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['section_id'];
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

                        <!-- Project Name -->
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Job Title / Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="project_name" class="form-control <?php $__errorArgs = ['project_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="e.g., Office Renovation - 3rd Floor" value="<?php echo e(old('project_name')); ?>" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['project_name'];
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

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Problem Statement / Justification <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      placeholder="Describe the problem and why this project is needed..." required><?php echo e(old('description')); ?></textarea>
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

                        <!-- Total Budget (Optional) -->
                        <div class="mb-3">
                            <label for="total_budget" class="form-label">Estimated Total Budget</label>
                            <input type="number" name="total_budget" id="total_budget" class="form-control <?php $__errorArgs = ['total_budget'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="0.00" step="0.01" min="0" value="<?php echo e(old('total_budget')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['total_budget'];
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

                        <!-- Submit -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo e(route('pabs.projects.index')); ?>" class="btn btn-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">Create Project</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="card-title mb-0">Workflow Overview</h6>
                </div>
                <div class="card-body">
                    <ol class="small ps-3">
                        <li><strong>Initiation (DRAFT)</strong> - Submit request with job title and justification</li>
                        <li><strong>Scoping</strong> - Lead conducts survey and uploads requirement documents</li>
                        <li><strong>Quoting</strong> - Procurement enters market rates from 3 vendors</li>
                        <li><strong>Approval</strong> - CEO reviews and approves with budget and deadline</li>
                        <li><strong>Allocation</strong> - Accounts confirms funds are available</li>
                        <li><strong>Execution (In Progress)</strong> - Work is completed with progress updates</li>
                        <li><strong>Completion</strong> - Submit actual cost and final notes; variance flagged if needed</li>
                    </ol>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">11 Organizational Domains</h6>
                </div>
                <div class="card-body">
                    <small>
                        <ul class="ps-3 mb-0">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($name); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pabs/projects/create.blade.php ENDPATH**/ ?>