<?php $__env->startSection('title'); ?>
    Verifier Submission
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .form-label.required::after {
        content: ' *';
        color: red;
    }
    input[readonly] {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Verification <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> <?php echo e(ucfirst($team ?? 'peregrine')); ?> - New Submission <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4 class="card-title mb-0 text-white"><i class="bx bx-clipboard me-2"></i>Verification Form</h4>
                    <a href="<?php echo e(route('verifier.dashboard')); ?>" class="btn btn-light btn-sm">
                        <i class="bx bx-list-ul me-1"></i> My Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(isset($team) ? route('verifier.store.team', ['team' => $team]) : route('verifier.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="team" value="<?php echo e($team ?? 'peregrine'); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label required">Date</label>
                                <input type="date" name="date" class="form-control <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date', now()->format('Y-m-d'))); ?>" readonly required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Verifier Name</label>
                                <input type="text" name="verifier_name" class="form-control" value="<?php echo e(auth()->user()->name); ?>" readonly tabindex="-1" required>
                                <input type="hidden" name="verifier_name" value="<?php echo e(auth()->user()->name); ?>">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label required">Live Closer</label>
                                <select name="closer_id" class="form-select <?php $__errorArgs = ['closer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select Live Closer</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $closers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $closer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($closer->id); ?>" <?php echo e(old('closer_id') == $closer->id ? 'selected' : ''); ?>><?php echo e($closer->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['closer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3 text-primary"><i class="bx bx-user me-2"></i>Customer Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Customer Name</label>
                                <input type="text" name="cn_name" class="form-control <?php $__errorArgs = ['cn_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('cn_name')); ?>" placeholder="Enter full name" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cn_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date_of_birth')); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Age</label>
                                <input type="number" id="age" name="age" class="form-control <?php $__errorArgs = ['age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('age')); ?>" placeholder="Auto-calculated" min="18" max="100" readonly tabindex="-1" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Gender</label>
                                <select name="gender" class="form-select <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo e(old('gender') == 'Male' ? 'selected' : ''); ?>>Male</option>
                                    <option value="Female" <?php echo e(old('gender') == 'Female' ? 'selected' : ''); ?>>Female</option>
                                    <option value="Other" <?php echo e(old('gender') == 'Other' ? 'selected' : ''); ?>>Other</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone_number')); ?>" placeholder="e.g., 555-123-4567" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Account Type</label>
                                <select name="account_type" class="form-select <?php $__errorArgs = ['account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select Account Type</option>
                                    <option value="Checking" <?php echo e(old('account_type') == 'Checking' ? 'selected' : ''); ?>>Checking</option>
                                    <option value="Savings" <?php echo e(old('account_type') == 'Savings' ? 'selected' : ''); ?>>Savings</option>
                                    <option value="Card" <?php echo e(old('account_type') == 'Card' ? 'selected' : ''); ?>>Card</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label required">Address</label>
                                <input type="text" name="address" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('address')); ?>" placeholder="Street address" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label required">State</label>
                                <select name="state" class="form-select <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">State</option>
                                    <option value="AL" <?php echo e(old('state') == 'AL' ? 'selected' : ''); ?>>Alabama (AL)</option>
                                    <option value="AK" <?php echo e(old('state') == 'AK' ? 'selected' : ''); ?>>Alaska (AK)</option>
                                    <option value="AZ" <?php echo e(old('state') == 'AZ' ? 'selected' : ''); ?>>Arizona (AZ)</option>
                                    <option value="AR" <?php echo e(old('state') == 'AR' ? 'selected' : ''); ?>>Arkansas (AR)</option>
                                    <option value="CA" <?php echo e(old('state') == 'CA' ? 'selected' : ''); ?>>California (CA)</option>
                                    <option value="CO" <?php echo e(old('state') == 'CO' ? 'selected' : ''); ?>>Colorado (CO)</option>
                                    <option value="CT" <?php echo e(old('state') == 'CT' ? 'selected' : ''); ?>>Connecticut (CT)</option>
                                    <option value="DE" <?php echo e(old('state') == 'DE' ? 'selected' : ''); ?>>Delaware (DE)</option>
                                    <option value="DC" <?php echo e(old('state') == 'DC' ? 'selected' : ''); ?>>District of Columbia (DC)</option>
                                    <option value="FL" <?php echo e(old('state') == 'FL' ? 'selected' : ''); ?>>Florida (FL)</option>
                                    <option value="GA" <?php echo e(old('state') == 'GA' ? 'selected' : ''); ?>>Georgia (GA)</option>
                                    <option value="HI" <?php echo e(old('state') == 'HI' ? 'selected' : ''); ?>>Hawaii (HI)</option>
                                    <option value="ID" <?php echo e(old('state') == 'ID' ? 'selected' : ''); ?>>Idaho (ID)</option>
                                    <option value="IL" <?php echo e(old('state') == 'IL' ? 'selected' : ''); ?>>Illinois (IL)</option>
                                    <option value="IN" <?php echo e(old('state') == 'IN' ? 'selected' : ''); ?>>Indiana (IN)</option>
                                    <option value="IA" <?php echo e(old('state') == 'IA' ? 'selected' : ''); ?>>Iowa (IA)</option>
                                    <option value="KS" <?php echo e(old('state') == 'KS' ? 'selected' : ''); ?>>Kansas (KS)</option>
                                    <option value="KY" <?php echo e(old('state') == 'KY' ? 'selected' : ''); ?>>Kentucky (KY)</option>
                                    <option value="LA" <?php echo e(old('state') == 'LA' ? 'selected' : ''); ?>>Louisiana (LA)</option>
                                    <option value="ME" <?php echo e(old('state') == 'ME' ? 'selected' : ''); ?>>Maine (ME)</option>
                                    <option value="MD" <?php echo e(old('state') == 'MD' ? 'selected' : ''); ?>>Maryland (MD)</option>
                                    <option value="MA" <?php echo e(old('state') == 'MA' ? 'selected' : ''); ?>>Massachusetts (MA)</option>
                                    <option value="MI" <?php echo e(old('state') == 'MI' ? 'selected' : ''); ?>>Michigan (MI)</option>
                                    <option value="MN" <?php echo e(old('state') == 'MN' ? 'selected' : ''); ?>>Minnesota (MN)</option>
                                    <option value="MS" <?php echo e(old('state') == 'MS' ? 'selected' : ''); ?>>Mississippi (MS)</option>
                                    <option value="MO" <?php echo e(old('state') == 'MO' ? 'selected' : ''); ?>>Missouri (MO)</option>
                                    <option value="MT" <?php echo e(old('state') == 'MT' ? 'selected' : ''); ?>>Montana (MT)</option>
                                    <option value="NE" <?php echo e(old('state') == 'NE' ? 'selected' : ''); ?>>Nebraska (NE)</option>
                                    <option value="NV" <?php echo e(old('state') == 'NV' ? 'selected' : ''); ?>>Nevada (NV)</option>
                                    <option value="NH" <?php echo e(old('state') == 'NH' ? 'selected' : ''); ?>>New Hampshire (NH)</option>
                                    <option value="NJ" <?php echo e(old('state') == 'NJ' ? 'selected' : ''); ?>>New Jersey (NJ)</option>
                                    <option value="NM" <?php echo e(old('state') == 'NM' ? 'selected' : ''); ?>>New Mexico (NM)</option>
                                    <option value="NY" <?php echo e(old('state') == 'NY' ? 'selected' : ''); ?>>New York (NY)</option>
                                    <option value="NC" <?php echo e(old('state') == 'NC' ? 'selected' : ''); ?>>North Carolina (NC)</option>
                                    <option value="ND" <?php echo e(old('state') == 'ND' ? 'selected' : ''); ?>>North Dakota (ND)</option>
                                    <option value="OH" <?php echo e(old('state') == 'OH' ? 'selected' : ''); ?>>Ohio (OH)</option>
                                    <option value="OK" <?php echo e(old('state') == 'OK' ? 'selected' : ''); ?>>Oklahoma (OK)</option>
                                    <option value="OR" <?php echo e(old('state') == 'OR' ? 'selected' : ''); ?>>Oregon (OR)</option>
                                    <option value="PA" <?php echo e(old('state') == 'PA' ? 'selected' : ''); ?>>Pennsylvania (PA)</option>
                                    <option value="RI" <?php echo e(old('state') == 'RI' ? 'selected' : ''); ?>>Rhode Island (RI)</option>
                                    <option value="SC" <?php echo e(old('state') == 'SC' ? 'selected' : ''); ?>>South Carolina (SC)</option>
                                    <option value="SD" <?php echo e(old('state') == 'SD' ? 'selected' : ''); ?>>South Dakota (SD)</option>
                                    <option value="TN" <?php echo e(old('state') == 'TN' ? 'selected' : ''); ?>>Tennessee (TN)</option>
                                    <option value="TX" <?php echo e(old('state') == 'TX' ? 'selected' : ''); ?>>Texas (TX)</option>
                                    <option value="UT" <?php echo e(old('state') == 'UT' ? 'selected' : ''); ?>>Utah (UT)</option>
                                    <option value="VT" <?php echo e(old('state') == 'VT' ? 'selected' : ''); ?>>Vermont (VT)</option>
                                    <option value="VA" <?php echo e(old('state') == 'VA' ? 'selected' : ''); ?>>Virginia (VA)</option>
                                    <option value="WA" <?php echo e(old('state') == 'WA' ? 'selected' : ''); ?>>Washington (WA)</option>
                                    <option value="WV" <?php echo e(old('state') == 'WV' ? 'selected' : ''); ?>>West Virginia (WV)</option>
                                    <option value="WI" <?php echo e(old('state') == 'WI' ? 'selected' : ''); ?>>Wisconsin (WI)</option>
                                    <option value="WY" <?php echo e(old('state') == 'WY' ? 'selected' : ''); ?>>Wyoming (WY)</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label required">Zip Code</label>
                                <input type="text" name="zip_code" class="form-control <?php $__errorArgs = ['zip_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('zip_code')); ?>" placeholder="Zip" maxlength="10" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['zip_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-send me-1"></i> Submit to Closer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    // Auto-calculate age from date of birth
    document.getElementById('date_of_birth').addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        
        if (!this.value || dob > today) {
            document.getElementById('age').value = '';
            return;
        }
        
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        // Adjust age if birthday hasn't occurred this year yet
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        document.getElementById('age').value = age;
    });
    
    // Trigger calculation on page load if DOB exists
    document.addEventListener('DOMContentLoaded', function() {
        const dobField = document.getElementById('date_of_birth');
        if (dobField.value) {
            dobField.dispatchEvent(new Event('change'));
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/verifier/create.blade.php ENDPATH**/ ?>