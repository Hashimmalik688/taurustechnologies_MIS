<?php $__env->startSection('title'); ?>
    Edit User
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Users
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Edit User
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

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('users.update', $user->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Name</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                                        placeholder="Enter Name">

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block">
                                            <?php echo e($message); ?>

                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                        placeholder="Enter Email ID">

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block">
                                            <?php echo e($message); ?>

                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="<?php echo e(old('phone', $user->userDetail->phone ?? '')); ?>"
                                        placeholder="Enter Phone">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="zoomNumber" class="form-label">Zoom Number</label>
                                    <input type="text" class="form-control" id="zoomNumber" name="zoom_number"
                                        value="<?php echo e(old('zoom_number', $user->userDetail->zoom_number ?? '')); ?>"
                                        placeholder="Enter Zoom number">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="plain_password" class="form-label">Password (Plaintext Reference)</label>
                                    <input type="text" class="form-control" id="plain_password" name="plain_password"
                                        value="<?php echo e(old('plain_password', $user->userDetail->plain_password ?? '')); ?>"
                                        placeholder="Enter password for reference">
                                    <small class="text-muted">This is for reference only, not for login</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="password" name="password" placeholder="Leave blank to keep current password">

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block">
                                            <?php echo e($message); ?>

                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <small class="text-muted">Leave blank if you don't want to change the password</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Roles</label>
                                    <?php
                                        $currentRoles = $user->roles->pluck('name')->toArray();
                                    ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Super Admin" id="role-super-admin" 
                                                    <?php echo e(in_array('Super Admin', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-super-admin">Super Admin</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Manager" id="role-manager"
                                                    <?php echo e(in_array('Manager', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-manager">Manager</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="HR" id="role-hr"
                                                    <?php echo e(in_array('HR', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-hr">HR</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Employee" id="role-employee"
                                                    <?php echo e(in_array('Employee', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-employee">Employee</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Co-ordinator" id="role-co-ordinator"
                                                    <?php echo e(in_array('Co-ordinator', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-co-ordinator">Co-ordinator</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Agent" id="role-agent"
                                                    <?php echo e(in_array('Agent', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-agent">Agent</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Vendor" id="role-vendor"
                                                    <?php echo e(in_array('Vendor', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-vendor">Vendor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="QA" id="role-qa"
                                                    <?php echo e(in_array('QA', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-qa">QA</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Trainer" id="role-trainer"
                                                    <?php echo e(in_array('Trainer', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-trainer">Trainer</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-primary">Peregrine Team</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Peregrine Closer" id="role-peregrine-closer"
                                                    <?php echo e(in_array('Peregrine Closer', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-peregrine-closer">Peregrine Closer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Peregrine Validator" id="role-peregrine-validator"
                                                    <?php echo e(in_array('Peregrine Validator', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-peregrine-validator">Peregrine Validator</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Verifier" id="role-verifier"
                                                    <?php echo e(in_array('Verifier', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-verifier">Verifier</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Ravens Closer" id="role-ravens-closer"
                                                    <?php echo e(in_array('Ravens Closer', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-ravens-closer">Ravens Closer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Retention Officer" id="role-retention-officer"
                                                    <?php echo e(in_array('Retention Officer', old('roles', $currentRoles)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role-retention-officer">Retention Officer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger mt-2">
                                            <?php echo e($message); ?>

                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">DOB</label>
                                    <input type="date" class="form-control" id="dob" name="dob"
                                        value="<?php echo e(old('dob', $user->dob ? $user->dob->format('Y-m-d') : '')); ?>">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="">Select gender...</option>
                                        <option value="Male" <?php echo e(old('gender', $user->userDetail->gender ?? '') == 'Male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="Female" <?php echo e(old('gender', $user->userDetail->gender ?? '') == 'Female' ? 'selected' : ''); ?>>Female</option>
                                        <option value="Other" <?php echo e(old('gender', $user->userDetail->gender ?? '') == 'Other' ? 'selected' : ''); ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="join-date" class="form-label">Join Date</label>
                                    <input type="date" class="form-control" id="join-date" name="join_date"
                                        value="<?php echo e(old('join_date', $user->userDetail->join_date ?? '')); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="<?php echo e(old('city', $user->userDetail->city ?? '')); ?>"
                                        placeholder="Enter City">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="<?php echo e(old('address', $user->userDetail->address ?? '')); ?>"
                                        placeholder="Enter Address">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-md">
                                <i class="mdi mdi-content-save me-1"></i>
                                Update User
                            </button>
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary w-md">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Back
                            </a>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .required::after {
        content: " *";
        color: red;
    }
    .form-check {
        margin-bottom: 0.5rem;
    }
    .form-check-label {
        font-weight: 500;
    }
    .text-primary {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.25rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>