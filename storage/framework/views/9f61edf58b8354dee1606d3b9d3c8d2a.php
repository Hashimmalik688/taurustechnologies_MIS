<?php $__env->startSection('title'); ?>
    Complete Lead Information
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Peregrine <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Complete Lead <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('peregrine.closers.update', $lead->id)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <style>
            .lead-form-card {
                border-radius: 10px;
                border: none;
                box-shadow: 0 2px 10px rgba(0,0,0,0.06);
                margin-bottom: 24px;
            }
            .lead-form-card .card-header {
                background: linear-gradient(135deg, var(--bs-print-body-dark) 0%, var(--bs-print-header-bg) 100%);
                border-radius: 10px 10px 0 0;
                padding: 16px 24px;
                border: none;
            }
            .lead-form-card .card-title {
                color: var(--bs-gold);
                font-weight: 600;
                font-size: 1.1rem;
                margin: 0;
            }
            .lead-form-card .card-body {
                padding: 28px;
            }
            .form-section-title {
                color: var(--bs-print-body-dark);
                font-size: 0.95rem;
                font-weight: 600;
                margin-bottom: 20px;
                padding-bottom: 8px;
                border-bottom: 2px solid var(--bs-gold);
                display: inline-block;
            }
            .form-label {
                font-weight: 500;
                color: var(--bs-print-header-bg);
                font-size: 0.875rem;
                margin-bottom: 6px;
            }
            .form-label.required:after {
                content: '*';
                color: var(--bs-status-absent);
                margin-left: 4px;
            }
            .form-control:disabled, .form-select:disabled {
                background-color: var(--bs-surface-200);
                opacity: 1;
            }
            .readonly-value {
                background-color: var(--bs-surface-bg-light);
                padding: 10px 14px;
                border-radius: 6px;
                border: 1px solid var(--bs-surface-200);
                font-weight: 500;
            }
        </style>

        <div class="row">
            <div class="col-12">
                <!-- Verifier Information (Read-Only) -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-check-circle section-icon"></i>
                            Verified Information (Read-Only)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> The following information was collected by the verifier and cannot be changed.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <div class="readonly-value"><?php echo e($lead->date ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Customer Name</label>
                                <div class="readonly-value"><?php echo e($lead->cn_name ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone Number</label>
                                <div class="readonly-value"><?php echo e($lead->phone_number ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Verified By</label>
                                <div class="readonly-value"><?php echo e($lead->account_verified_by ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assigned Closer</label>
                                <div class="readonly-value"><?php echo e($lead->closer_name ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-user section-icon"></i>
                            Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label required">Date of Birth</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="date_of_birth" name="date_of_birth" value="<?php echo e(old('date_of_birth', $lead->date_of_birth)); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label required">Gender</label>
                                <select class="form-select <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo e(old('gender', $lead->gender) == 'Male' ? 'selected' : ''); ?>>Male</option>
                                    <option value="Female" <?php echo e(old('gender', $lead->gender) == 'Female' ? 'selected' : ''); ?>>Female</option>
                                    <option value="Other" <?php echo e(old('gender', $lead->gender) == 'Other' ? 'selected' : ''); ?>>Other</option>
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
                                <label for="ssn" class="form-label required">Social Security Number</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ssn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ssn" name="ssn" value="<?php echo e(old('ssn', $lead->ssn)); ?>" placeholder="XXX-XX-XXXX" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ssn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <label for="address" class="form-label required">Full Address</label>
                                <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="address" name="address" rows="2" placeholder="Street address, city, state, ZIP code" required><?php echo e(old('address', $lead->address)); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="birth_place" class="form-label">Birth Place</label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place"
                                    value="<?php echo e(old('birth_place', $lead->birth_place)); ?>" placeholder="City, State">
                            </div>
                        </div>

                        <h6 class="form-section-title mt-4">Health Information</h6>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="height" class="form-label">Height</label>
                                <input type="text" class="form-control" id="height" name="height"
                                    value="<?php echo e(old('height', $lead->height)); ?>" placeholder="5'10&quot;">
                            </div>
                            <div class="col-md-2">
                                <label for="weight" class="form-label">Weight (lbs)</label>
                                <input type="text" class="form-control" id="weight" name="weight"
                                    value="<?php echo e(old('weight', $lead->weight)); ?>" placeholder="180">
                            </div>
                            <div class="col-md-2">
                                <label for="smoker" class="form-label">Smoker Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="smoker" name="smoker" value="1"
                                        <?php echo e(old('smoker', $lead->smoker) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="smoker">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label for="doctor_name" class="form-label">Primary Care Physician</label>
                                <input type="text" class="form-control" id="doctor_name" name="doctor_name"
                                    value="<?php echo e(old('doctor_name', $lead->doctor_name)); ?>" placeholder="Dr. Name, Practice">
                            </div>
                            <div class="col-md-6">
                                <label for="medical_issue" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medical_issue" name="medical_issue" rows="2"
                                    placeholder="List any pre-existing conditions"><?php echo e(old('medical_issue', $lead->medical_issue)); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="medications" class="form-label">Current Medications</label>
                                <textarea class="form-control" id="medications" name="medications" rows="2"
                                    placeholder="List all current medications"><?php echo e(old('medications', $lead->medications)); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insurance & Coverage Information -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-shield section-icon"></i>
                            Insurance & Coverage Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="carrier_name" class="form-label">Carrier Name</label>
                                <input type="text" class="form-control" id="carrier_name" name="carrier_name"
                                    value="<?php echo e(old('carrier_name', $lead->carrier_name)); ?>" placeholder="Insurance company">
                            </div>
                            <div class="col-md-4">
                                <label for="policy_type" class="form-label required">Policy Type</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['policy_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="policy_type" name="policy_type" value="<?php echo e(old('policy_type', $lead->policy_type)); ?>"
                                    placeholder="Term Life, Whole Life, etc." required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['policy_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="initial_draft_date" class="form-label required">Initial Draft Date</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['initial_draft_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="initial_draft_date" name="initial_draft_date" value="<?php echo e(old('initial_draft_date', $lead->initial_draft_date)); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['initial_draft_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="coverage_amount" class="form-label required">Coverage Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="1000" class="form-control <?php $__errorArgs = ['coverage_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="coverage_amount" name="coverage_amount" value="<?php echo e(old('coverage_amount', $lead->coverage_amount)); ?>"
                                        placeholder="250000" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['coverage_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="monthly_premium" class="form-label required">Monthly Premium</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['monthly_premium'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="monthly_premium" name="monthly_premium" value="<?php echo e(old('monthly_premium', $lead->monthly_premium)); ?>"
                                        placeholder="125.50" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['monthly_premium'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="source" class="form-label">Lead Source</label>
                                <input type="text" class="form-control" id="source" name="source"
                                    value="<?php echo e(old('source', $lead->source)); ?>" placeholder="Referral, Web, etc.">
                            </div>
                        </div>

                        <!-- Multiple Beneficiaries Section -->
                        <h6 class="form-section-title mt-4">Beneficiary Information</h6>
                        <div id="beneficiaries-container" class="mb-3">
                            <?php
                                // Check for old input first (from failed submission), then existing beneficiaries
                                $existingBeneficiaries = old('beneficiaries', $lead->beneficiaries ?? []);
                                // If no beneficiaries in JSON but old fields exist, migrate them
                                if (empty($existingBeneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
                                    $existingBeneficiaries = [[
                                        'name' => $lead->beneficiary ?? '',
                                        'dob' => $lead->beneficiary_dob ?? ''
                                    ]];
                                }
                                // Ensure at least one beneficiary row
                                if (empty($existingBeneficiaries)) {
                                    $existingBeneficiaries = [['name' => '', 'dob' => '']];
                                }
                            ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $existingBeneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row g-3 mb-2 beneficiary-row" data-index="<?php echo e($index); ?>">
                                <div class="col-md-5">
                                    <label for="beneficiaries[<?php echo e($index); ?>][name]" class="form-label <?php echo e($index === 0 ? 'required' : ''); ?>">
                                        Beneficiary Name <?php echo e($index > 0 ? ($index + 1) : ''); ?>

                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['beneficiaries.'.$index.'.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           name="beneficiaries[<?php echo e($index); ?>][name]" 
                                           value="<?php echo e(old('beneficiaries.'.$index.'.name', $beneficiary['name'] ?? '')); ?>" 
                                           placeholder="Full name" <?php echo e($index === 0 ? 'required' : ''); ?>>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['beneficiaries.'.$index.'.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="col-md-5">
                                    <label for="beneficiaries[<?php echo e($index); ?>][dob]" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control <?php $__errorArgs = ['beneficiaries.'.$index.'.dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           name="beneficiaries[<?php echo e($index); ?>][dob]" 
                                           value="<?php echo e(old('beneficiaries.'.$index.'.dob', $beneficiary['dob'] ?? '')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['beneficiaries.'.$index.'.dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index === 0): ?>
                                        <button type="button" class="btn btn-success w-100" id="add-beneficiary-edit" title="Add Another Beneficiary">
                                            <i class="bx bx-plus"></i> Add
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary">
                                            <i class="bx bx-minus"></i> Remove
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Banking & Payment Information -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-wallet section-icon"></i>
                            Banking & Payment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="bank_name" class="form-label required">Bank Name</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="bank_name" name="bank_name" value="<?php echo e(old('bank_name', $lead->bank_name)); ?>"
                                    placeholder="Bank name" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label for="account_type" class="form-label required">Account Type</label>
                                <select class="form-select <?php $__errorArgs = ['account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="account_type" name="account_type" required>
                                    <option value="">Select account type</option>
                                    <option value="Checking" <?php echo e(old('account_type', $lead->account_type) == 'Checking' ? 'selected' : ''); ?>>Checking</option>
                                    <option value="Savings" <?php echo e(old('account_type', $lead->account_type) == 'Savings' ? 'selected' : ''); ?>>Savings</option>
                                    <option value="Card" <?php echo e(old('account_type', $lead->account_type) == 'Card' ? 'selected' : ''); ?>>Card</option>
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
                            <div class="col-md-3">
                                <label for="account_number" class="form-label required">Account Number</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="account_number" name="account_number" value="<?php echo e(old('account_number', $lead->account_number)); ?>"
                                    placeholder="Account number" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label for="routing_number" class="form-label required">Routing Number</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['routing_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="routing_number" name="routing_number" value="<?php echo e(old('routing_number', $lead->routing_number)); ?>"
                                    placeholder="9 digits" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['routing_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label for="assigned_validator_id" class="form-label required">Assign to Validator</label>
                                <select class="form-select <?php $__errorArgs = ['assigned_validator_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="assigned_validator_id" name="assigned_validator_id" required>
                                    <option value="">Select Validator</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $validators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $validator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($validator->id); ?>" <?php echo e(old('assigned_validator_id', $lead->assigned_validator_id) == $validator->id ? 'selected' : ''); ?>>
                                            <?php echo e($validator->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['assigned_validator_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_balance" class="form-label">Bank Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="bank_balance" name="bank_balance"
                                        value="<?php echo e(old('bank_balance', $lead->bank_balance)); ?>" placeholder="Current balance">
                                </div>
                            </div>
                        </div>

                        <h6 class="form-section-title mt-4">Payment Card Information (Optional)</h6>
                        <div class="alert alert-info">
                            <i class="bx bx-lock-alt me-2"></i>
                            <strong>Secure:</strong> All payment card information is encrypted before storage.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="card_number" name="card_number" value="<?php echo e(old('card_number', $lead->card_number)); ?>"
                                    placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['cvv'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="cvv" name="cvv" value="<?php echo e(old('cvv', $lead->cvv)); ?>" placeholder="XXX" maxlength="4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cvv'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="expiry_date" name="expiry_date" value="<?php echo e(old('expiry_date', $lead->expiry_date)); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <h6 class="form-section-title mt-4">
                            <i class="bx bx-briefcase me-2"></i>
                            Partner Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="assigned_partner" class="form-label required">Assigned Partner</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['assigned_partner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="assigned_partner" name="assigned_partner" 
                                    value="<?php echo e(old('assigned_partner', $lead->assigned_partner)); ?>"
                                    placeholder="Enter partner name" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['assigned_partner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-save me-2"></i>
                                Submit to Validator
                            </button>
                            <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#pendingModal">
                                <i class="bx bx-time me-2"></i>
                                Mark as Pending
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#failedModal">
                                <i class="bx bx-x me-2"></i>
                                Mark as Failed
                            </button>
                            <a href="<?php echo e(route('peregrine.closers.index')); ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bx bx-arrow-back me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Pending Reason Modal -->
    <div class="modal fade" id="pendingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Select Pending Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?php echo e(route('peregrine.closers.mark-pending', $lead->id)); ?>" id="pendingForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <p class="mb-3">Why is this lead being marked as pending?</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="futurePotential" value="Pending:Future Potential" required>
                            <label class="form-check-label" for="futurePotential">
                                <strong>Pending:Future Potential</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="callback" value="Pending:Callback" required>
                            <label class="form-check-label" for="callback">
                                <strong>Pending:Callback</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingBanking" value="Pending:Pending Banking" required>
                            <label class="form-check-label" for="pendingBanking">
                                <strong>Pending:Pending Banking</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingValidation" value="Pending:Pending Validation" required>
                            <label class="form-check-label" for="pendingValidation">
                                <strong>Pending:Pending Validation</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Confirm Pending</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Failed Reason Modal -->
    <div class="modal fade" id="failedModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Select Failure Reason</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?php echo e(route('peregrine.closers.mark-failed', $lead->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <p class="mb-3">Why is this lead being marked as failed?</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="poa" value="Failed:POA" required>
                            <label class="form-check-label" for="poa">
                                <strong>Failed:POA</strong> - Power of Attorney
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqAge" value="Failed:DNQ-Age" required>
                            <label class="form-check-label" for="dnqAge">
                                <strong>Failed:DNQ-Age</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedSSN" value="Failed:Declined SSN" required>
                            <label class="form-check-label" for="declinedSSN">
                                <strong>Failed:Declined SSN</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="notInterested" value="Failed:Not Interested" required>
                            <label class="form-check-label" for="notInterested">
                                <strong>Failed:Not Interested</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnc" value="Failed:DNC" required>
                            <label class="form-check-label" for="dnc">
                                <strong>Failed:DNC</strong> - Do Not Call
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="cannotAfford" value="Failed:Cannot Afford" required>
                            <label class="form-check-label" for="cannotAfford">
                                <strong>Failed:Cannot Afford</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqHealth" value="Failed:DNQ-Health" required>
                            <label class="form-check-label" for="dnqHealth">
                                <strong>Failed:DNQ-Health</strong> - Health Conditions
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedBanking" value="Failed:Declined Banking" required>
                            <label class="form-check-label" for="declinedBanking">
                                <strong>Failed:Declined Banking</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="noPitch" value="Failed:No Pitch (Not Interested)" required>
                            <label class="form-check-label" for="noPitch">
                                <strong>Failed:No Pitch (Not Interested)</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="noAnswer" value="Failed:No Answer" required>
                            <label class="form-check-label" for="noAnswer">
                                <strong>Failed:No Answer</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Failed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Multiple Beneficiaries Management
        document.addEventListener('DOMContentLoaded', function() {
            let beneficiaryIndex = <?php echo e(count($existingBeneficiaries)); ?>;
            
            const addBeneficiaryBtn = document.getElementById('add-beneficiary-edit');
            if (addBeneficiaryBtn) {
                addBeneficiaryBtn.addEventListener('click', function() {
                    const container = document.getElementById('beneficiaries-container');
                    const newRow = document.createElement('div');
                    newRow.className = 'row g-3 mb-2 beneficiary-row';
                    newRow.setAttribute('data-index', beneficiaryIndex);
                    newRow.innerHTML = `
                        <div class="col-md-5">
                            <label for="beneficiaries[${beneficiaryIndex}][name]" class="form-label">Beneficiary Name ${beneficiaryIndex + 1}</label>
                            <input type="text" class="form-control" name="beneficiaries[${beneficiaryIndex}][name]" placeholder="Full name">
                        </div>
                        <div class="col-md-5">
                            <label for="beneficiaries[${beneficiaryIndex}][dob]" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="beneficiaries[${beneficiaryIndex}][dob]">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary">
                                <i class="bx bx-minus"></i> Remove
                            </button>
                        </div>
                    `;
                    container.appendChild(newRow);
                    beneficiaryIndex++;
                    
                    // Attach remove handler
                    newRow.querySelector('.remove-beneficiary').addEventListener('click', function() {
                        newRow.remove();
                    });
                });
            }
            
            // Remove beneficiary (for existing rows)
            document.querySelectorAll('.remove-beneficiary').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.beneficiary-row').remove();
                });
            });
        });

        // Copy form data to pending form before submit
        document.getElementById('pendingForm').addEventListener('submit', function(e) {
            const mainForm = document.querySelector('form[action*="update"]');
            const pendingForm = this;
            
            // Track which radio groups we've already added
            const addedRadios = new Set();
            
            // Copy all inputs from main form to pending form
            mainForm.querySelectorAll('input, select, textarea').forEach(function(input) {
                if (input.name && input.name !== '_token' && input.name !== '_method' && input.name !== 'pending_reason') {
                    // Handle radio buttons specially - only add the checked one per group
                    if (input.type === 'radio') {
                        if (input.checked && !addedRadios.has(input.name)) {
                            addedRadios.add(input.name);
                            let hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = input.name;
                            hidden.value = input.value;
                            pendingForm.appendChild(hidden);
                        }
                    } else {
                        // For non-radio inputs
                        let hidden = pendingForm.querySelector('input[name="' + input.name + '"]');
                        if (!hidden) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = input.name;
                            pendingForm.appendChild(hidden);
                        }
                        
                        if (input.type === 'checkbox') {
                            hidden.value = input.checked ? '1' : '0';
                        } else {
                            hidden.value = input.value || '';
                        }
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/peregrine/closers/edit.blade.php ENDPATH**/ ?>