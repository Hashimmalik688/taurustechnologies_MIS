<?php $__env->startSection('title'); ?>
    Create Agent
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Agents
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Create Agent
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
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-account-plus me-2"></i>
                        Agent Information
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('agents.store')); ?>" id="agentForm">
                        <?php echo csrf_field(); ?>

                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    Basic Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">
                                        <i class="mdi mdi-account me-1"></i>
                                        Full Name
                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="name" name="name" value="<?php echo e(old('name')); ?>"
                                        placeholder="Enter full name" required>
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
                                    <label for="email" class="form-label required">
                                        <i class="mdi mdi-email me-1"></i>
                                        Email Address
                                    </label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="email" name="email" value="<?php echo e(old('email')); ?>"
                                        placeholder="Enter email address" required>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label required">
                                        <i class="mdi mdi-lock me-1"></i>
                                        Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="password" name="password" placeholder="Enter password" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('password')">
                                            <i class="mdi mdi-eye" id="password-icon"></i>
                                        </button>
                                    </div>
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
                                    <div class="form-text">
                                        <small class="text-muted">Password must be at least 8 characters long</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label required">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        State
                                    </label>
                                    <select id="state" name="state"
                                        class="form-select <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="">Select state...</option>
                                        <option value="Alabama" <?php echo e(old('state') == 'Alabama' ? 'selected' : ''); ?>>Alabama
                                        </option>
                                        <option value="Alaska" <?php echo e(old('state') == 'Alaska' ? 'selected' : ''); ?>>Alaska
                                        </option>
                                        <option value="Arizona" <?php echo e(old('state') == 'Arizona' ? 'selected' : ''); ?>>Arizona
                                        </option>
                                        <option value="Arkansas" <?php echo e(old('state') == 'Arkansas' ? 'selected' : ''); ?>>
                                            Arkansas</option>
                                        <option value="California" <?php echo e(old('state') == 'California' ? 'selected' : ''); ?>>
                                            California</option>
                                        <option value="Colorado" <?php echo e(old('state') == 'Colorado' ? 'selected' : ''); ?>>
                                            Colorado</option>
                                        <option value="Connecticut" <?php echo e(old('state') == 'Connecticut' ? 'selected' : ''); ?>>
                                            Connecticut</option>
                                        <option value="Delaware" <?php echo e(old('state') == 'Delaware' ? 'selected' : ''); ?>>
                                            Delaware</option>
                                        <option value="Florida" <?php echo e(old('state') == 'Florida' ? 'selected' : ''); ?>>Florida
                                        </option>
                                        <option value="Georgia" <?php echo e(old('state') == 'Georgia' ? 'selected' : ''); ?>>Georgia
                                        </option>
                                        <option value="Hawaii" <?php echo e(old('state') == 'Hawaii' ? 'selected' : ''); ?>>Hawaii
                                        </option>
                                        <option value="Idaho" <?php echo e(old('state') == 'Idaho' ? 'selected' : ''); ?>>Idaho
                                        </option>
                                        <option value="Illinois" <?php echo e(old('state') == 'Illinois' ? 'selected' : ''); ?>>
                                            Illinois</option>
                                        <option value="Indiana" <?php echo e(old('state') == 'Indiana' ? 'selected' : ''); ?>>Indiana
                                        </option>
                                        <option value="Iowa" <?php echo e(old('state') == 'Iowa' ? 'selected' : ''); ?>>Iowa</option>
                                        <option value="Kansas" <?php echo e(old('state') == 'Kansas' ? 'selected' : ''); ?>>Kansas
                                        </option>
                                        <option value="Kentucky" <?php echo e(old('state') == 'Kentucky' ? 'selected' : ''); ?>>
                                            Kentucky</option>
                                        <option value="Louisiana" <?php echo e(old('state') == 'Louisiana' ? 'selected' : ''); ?>>
                                            Louisiana</option>
                                        <option value="Maine" <?php echo e(old('state') == 'Maine' ? 'selected' : ''); ?>>Maine
                                        </option>
                                        <option value="Maryland" <?php echo e(old('state') == 'Maryland' ? 'selected' : ''); ?>>
                                            Maryland</option>
                                        <option value="Massachusetts"
                                            <?php echo e(old('state') == 'Massachusetts' ? 'selected' : ''); ?>>Massachusetts</option>
                                        <option value="Michigan" <?php echo e(old('state') == 'Michigan' ? 'selected' : ''); ?>>
                                            Michigan</option>
                                        <option value="Minnesota" <?php echo e(old('state') == 'Minnesota' ? 'selected' : ''); ?>>
                                            Minnesota</option>
                                        <option value="Mississippi" <?php echo e(old('state') == 'Mississippi' ? 'selected' : ''); ?>>
                                            Mississippi</option>
                                        <option value="Missouri" <?php echo e(old('state') == 'Missouri' ? 'selected' : ''); ?>>
                                            Missouri</option>
                                        <option value="Montana" <?php echo e(old('state') == 'Montana' ? 'selected' : ''); ?>>Montana
                                        </option>
                                        <option value="Nebraska" <?php echo e(old('state') == 'Nebraska' ? 'selected' : ''); ?>>
                                            Nebraska</option>
                                        <option value="Nevada" <?php echo e(old('state') == 'Nevada' ? 'selected' : ''); ?>>Nevada
                                        </option>
                                        <option value="New Hampshire"
                                            <?php echo e(old('state') == 'New Hampshire' ? 'selected' : ''); ?>>New Hampshire</option>
                                        <option value="New Jersey" <?php echo e(old('state') == 'New Jersey' ? 'selected' : ''); ?>>
                                            New Jersey</option>
                                        <option value="New Mexico" <?php echo e(old('state') == 'New Mexico' ? 'selected' : ''); ?>>
                                            New Mexico</option>
                                        <option value="New York" <?php echo e(old('state') == 'New York' ? 'selected' : ''); ?>>New
                                            York</option>
                                        <option value="North Carolina"
                                            <?php echo e(old('state') == 'North Carolina' ? 'selected' : ''); ?>>North Carolina
                                        </option>
                                        <option value="North Dakota"
                                            <?php echo e(old('state') == 'North Dakota' ? 'selected' : ''); ?>>North Dakota</option>
                                        <option value="Ohio" <?php echo e(old('state') == 'Ohio' ? 'selected' : ''); ?>>Ohio
                                        </option>
                                        <option value="Oklahoma" <?php echo e(old('state') == 'Oklahoma' ? 'selected' : ''); ?>>
                                            Oklahoma</option>
                                        <option value="Oregon" <?php echo e(old('state') == 'Oregon' ? 'selected' : ''); ?>>Oregon
                                        </option>
                                        <option value="Pennsylvania"
                                            <?php echo e(old('state') == 'Pennsylvania' ? 'selected' : ''); ?>>Pennsylvania</option>
                                        <option value="Rhode Island"
                                            <?php echo e(old('state') == 'Rhode Island' ? 'selected' : ''); ?>>Rhode Island</option>
                                        <option value="South Carolina"
                                            <?php echo e(old('state') == 'South Carolina' ? 'selected' : ''); ?>>South Carolina
                                        </option>
                                        <option value="South Dakota"
                                            <?php echo e(old('state') == 'South Dakota' ? 'selected' : ''); ?>>South Dakota</option>
                                        <option value="Tennessee" <?php echo e(old('state') == 'Tennessee' ? 'selected' : ''); ?>>
                                            Tennessee</option>
                                        <option value="Texas" <?php echo e(old('state') == 'Texas' ? 'selected' : ''); ?>>Texas
                                        </option>
                                        <option value="Utah" <?php echo e(old('state') == 'Utah' ? 'selected' : ''); ?>>Utah
                                        </option>
                                        <option value="Vermont" <?php echo e(old('state') == 'Vermont' ? 'selected' : ''); ?>>Vermont
                                        </option>
                                        <option value="Virginia" <?php echo e(old('state') == 'Virginia' ? 'selected' : ''); ?>>
                                            Virginia</option>
                                        <option value="Washington" <?php echo e(old('state') == 'Washington' ? 'selected' : ''); ?>>
                                            Washington</option>
                                        <option value="West Virginia"
                                            <?php echo e(old('state') == 'West Virginia' ? 'selected' : ''); ?>>West Virginia</option>
                                        <option value="Wisconsin" <?php echo e(old('state') == 'Wisconsin' ? 'selected' : ''); ?>>
                                            Wisconsin</option>
                                        <option value="Wyoming" <?php echo e(old('state') == 'Wyoming' ? 'selected' : ''); ?>>Wyoming
                                        </option>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['state'];
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
                                    <label for="phone" class="form-label">
                                        <i class="mdi mdi-phone me-1"></i>
                                        Phone Number
                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="phone" name="phone" value="<?php echo e(old('phone')); ?>"
                                        placeholder="(555) 123-4567">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
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

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ssn_last4" class="form-label">
                                        <i class="mdi mdi-shield-key me-1"></i>
                                        Last 4 of SSN
                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['ssn_last4'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ssn_last4" name="ssn_last4" value="<?php echo e(old('ssn_last4')); ?>"
                                        maxlength="4" pattern="[0-9]{4}" placeholder="1234">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ssn_last4'];
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

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">
                                        <i class="mdi mdi-calendar me-1"></i>
                                        Date of Birth
                                    </label>
                                    <input type="date" class="form-control <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="dob" name="dob" value="<?php echo e(old('dob')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dob'];
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
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label required">
                                        <i class="mdi mdi-home me-1"></i>
                                        Address
                                    </label>
                                    <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="address" name="address" rows="3"
                                        placeholder="Enter complete address" required><?php echo e(old('address')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address'];
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

                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-map-marker-multiple me-1"></i>
                                    Active States
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="active_states" class="form-label">
                                        <i class="mdi mdi-checkbox-multiple-marked me-1"></i>
                                        Select Active States
                                    </label>
                                    <select id="active_states" name="active_states[]"
                                        class="form-select select2-multiple <?php $__errorArgs = ['active_states'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        multiple="multiple" data-placeholder="Choose active states...">
                                        <?php
                                            $states = [
                                                'Alabama',
                                                'Alaska',
                                                'Arizona',
                                                'Arkansas',
                                                'California',
                                                'Colorado',
                                                'Connecticut',
                                                'Delaware',
                                                'Florida',
                                                'Georgia',
                                                'Hawaii',
                                                'Idaho',
                                                'Illinois',
                                                'Indiana',
                                                'Iowa',
                                                'Kansas',
                                                'Kentucky',
                                                'Louisiana',
                                                'Maine',
                                                'Maryland',
                                                'Massachusetts',
                                                'Michigan',
                                                'Minnesota',
                                                'Mississippi',
                                                'Missouri',
                                                'Montana',
                                                'Nebraska',
                                                'Nevada',
                                                'New Hampshire',
                                                'New Jersey',
                                                'New Mexico',
                                                'New York',
                                                'North Carolina',
                                                'North Dakota',
                                                'Ohio',
                                                'Oklahoma',
                                                'Oregon',
                                                'Pennsylvania',
                                                'Rhode Island',
                                                'South Carolina',
                                                'South Dakota',
                                                'Tennessee',
                                                'Texas',
                                                'Utah',
                                                'Vermont',
                                                'Virginia',
                                                'Washington',
                                                'West Virginia',
                                                'Wisconsin',
                                                'Wyoming',
                                            ];
                                            $oldActiveStates = old('active_states', []);
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($state); ?>"
                                                <?php echo e(in_array($state, $oldActiveStates) ? 'selected' : ''); ?>>
                                                <?php echo e($state); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['active_states'];
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
                                    <div class="form-text">
                                        <small class="text-muted">Select all states where this agent will be active</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-briefcase me-1"></i>
                                    Insurance Carriers & Commission Rates
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="alert alert-info flex-grow-1 me-3 mb-0">
                                            <i class="mdi mdi-information me-2"></i>
                                            Select carriers this agent will work with and set individual commission percentages for each. (Optional - can be added later)
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="window.open('<?php echo e(route('admin.insurance-carriers.index')); ?>', '_blank')">
                                                <i class="mdi mdi-view-list me-1"></i>
                                                View All Carriers
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCarrierModal">
                                                <i class="mdi mdi-plus me-1"></i>
                                                Add New Carrier
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($insuranceCarriers) && $insuranceCarriers->count() > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">
                                                            <input type="checkbox" id="selectAllCarriers" class="form-check-input">
                                                        </th>
                                                        <th width="30%">Carrier Name</th>
                                                        <th width="15%">Payment Module</th>
                                                        <th width="15%">Base Commission %</th>
                                                        <th width="15%">Agent Commission %</th>
                                                        <th width="20%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="selected_carriers[]" 
                                                                   value="<?php echo e($carrier->id); ?>" 
                                                                   class="form-check-input carrier-checkbox"
                                                                   onchange="toggleCommissionInput(this, <?php echo e($carrier->id); ?>)">
                                                        </td>
                                                        <td>
                                                            <strong><?php echo e($carrier->name); ?></strong>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->phone): ?>
                                                                <br><small class="text-muted"><i class="mdi mdi-phone"></i> <?php echo e($carrier->phone); ?></small>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?php echo e(ucwords(str_replace('_', ' ', $carrier->payment_module))); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary"><?php echo e($carrier->base_commission_percentage); ?>%</span>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   name="carrier_commissions[<?php echo e($carrier->id); ?>]" 
                                                                   id="commission_<?php echo e($carrier->id); ?>"
                                                                   class="form-control form-control-sm commission-input" 
                                                                   placeholder="<?php echo e($carrier->base_commission_percentage); ?>"
                                                                   step="0.01" 
                                                                   min="0" 
                                                                   max="100"
                                                                   disabled>
                                                            <small class="text-muted">Leave blank to use base rate</small>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-outline-info btn-sm" 
                                                                        onclick="editCarrier(<?php echo e($carrier->id); ?>)" 
                                                                        title="Edit Carrier">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                        onclick="deleteCarrier(<?php echo e($carrier->id); ?>, '<?php echo e($carrier->name); ?>')" 
                                                                        title="Delete Carrier">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="mdi mdi-alert me-2"></i>
                                            No insurance carriers available. Add a new carrier below or contact administrator.
                                        </div>
                                        
                                        
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="mdi mdi-plus-circle me-2"></i>Quick Add Insurance Carrier</h6>
                                            </div>
                                            <div class="card-body">
                                                <form id="quickAddCarrierForm" onsubmit="addCarrier(event)">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Carrier Name *</label>
                                                                <input type="text" class="form-control" id="carrier_name" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Base Commission %</label>
                                                                <input type="number" class="form-control" id="base_commission" step="0.01" min="0" max="100" placeholder="85.00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Age Min</label>
                                                                <input type="number" class="form-control" id="age_min" placeholder="18">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Age Max</label>
                                                                <input type="number" class="form-control" id="age_max" placeholder="80">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="mb-3">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="submit" class="btn btn-success d-block w-100">
                                                                    <i class="mdi mdi-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-map-marker-multiple me-1"></i>
                                    State-Specific Settlement Rates (Optional)
                                </h5>
                            </div>
                        </div>

                        <?php echo $__env->make('admin.agents.partials.carrier-states', ['insuranceCarriers' => $insuranceCarriers], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="mdi mdi-refresh me-1"></i>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i>
                                        Create Agent
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addCarrierModal" tabindex="-1" aria-labelledby="addCarrierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCarrierModalLabel">Add New Insurance Carrier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCarrierForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_carrier_name" class="form-label">Carrier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_carrier_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_payment_module" class="form-label">Payment Module <span class="text-danger">*</span></label>
                                <select class="form-select" id="modal_payment_module" name="payment_module" required>
                                    <option value="">Select Payment Module</option>
                                    <option value="on_draft">On Draft</option>
                                    <option value="on_issue">On Issue</option>
                                    <option value="as_earned">As Earned</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="modal_base_commission" class="form-label">Base Commission %</label>
                                <input type="number" class="form-control" id="modal_base_commission" name="base_commission_percentage" step="0.01" min="0" max="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="modal_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="modal_phone" name="phone">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="modal_ssn_last4" class="form-label">SSN Last 4</label>
                                <input type="text" class="form-control" id="modal_ssn_last4" name="ssn_last4" maxlength="4">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_age_min" class="form-label">Minimum Age</label>
                                <input type="number" class="form-control" id="modal_age_min" name="age_min" min="0" max="120">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_age_max" class="form-label">Maximum Age</label>
                                <input type="number" class="form-control" id="modal_age_max" name="age_max" min="0" max="120">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_plan_types" class="form-label">Plan Types</label>
                            <input type="text" class="form-control" id="modal_plan_types" name="plan_types" placeholder="e.g., Term, Whole Life, Universal">
                            <small class="text-muted">Separate multiple types with commas</small>
                        </div>
                        <div class="mb-3">
                            <label for="modal_calculation_notes" class="form-label">Calculation Notes</label>
                            <textarea class="form-control" id="modal_calculation_notes" name="calculation_notes" rows="3" placeholder="Any specific commission calculation notes..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modal_is_active" name="is_active" checked>
                            <label class="form-check-label" for="modal_is_active">
                                Active (Visible in dropdowns)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Carrier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="editCarrierModal" tabindex="-1" aria-labelledby="editCarrierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCarrierModalLabel">Edit Insurance Carrier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCarrierForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" id="edit_carrier_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_carrier_name" class="form-label">Carrier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_carrier_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_payment_module" class="form-label">Payment Module <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_payment_module" name="payment_module" required>
                                    <option value="">Select Payment Module</option>
                                    <option value="on_draft">On Draft</option>
                                    <option value="on_issue">On Issue</option>
                                    <option value="as_earned">As Earned</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_base_commission" class="form-label">Base Commission %</label>
                                <input type="number" class="form-control" id="edit_base_commission" name="base_commission_percentage" step="0.01" min="0" max="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_ssn_last4" class="form-label">SSN Last 4</label>
                                <input type="text" class="form-control" id="edit_ssn_last4" name="ssn_last4" maxlength="4">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_age_min" class="form-label">Minimum Age</label>
                                <input type="number" class="form-control" id="edit_age_min" name="age_min" min="0" max="120">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_age_max" class="form-label">Maximum Age</label>
                                <input type="number" class="form-control" id="edit_age_max" name="age_max" min="0" max="120">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_plan_types" class="form-label">Plan Types</label>
                            <input type="text" class="form-control" id="edit_plan_types" name="plan_types" placeholder="e.g., Term, Whole Life, Universal">
                            <small class="text-muted">Separate multiple types with commas</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_calculation_notes" class="form-label">Calculation Notes</label>
                            <textarea class="form-control" id="edit_calculation_notes" name="calculation_notes" rows="3" placeholder="Any specific commission calculation notes..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">
                                Active (Visible in dropdowns)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Carrier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="carrierDetailsModal" tabindex="-1" aria-labelledby="carrierDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carrierDetailsModalLabel">Carrier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="carrierDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for multiple select
            $('.select2-multiple').select2({
                placeholder: "Choose active states...",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5'
            });
        });

        // Function to toggle password visibility
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(inputId + '-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('mdi-eye');
                passwordIcon.classList.add('mdi-eye-off');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('mdi-eye-off');
                passwordIcon.classList.add('mdi-eye');
            }
        }

        // Function to add new carrier input
        function addCarrier() {
            const container = document.getElementById('carriers-container');
            const newCarrierGroup = document.createElement('div');
            newCarrierGroup.className = 'carrier-input-group mb-2';

            newCarrierGroup.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-truck"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="carriers[]" 
                           placeholder="Enter carrier name">
                    <button class="btn btn-outline-danger" type="button" onclick="removeCarrier(this)">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            `;

            container.appendChild(newCarrierGroup);
        }

        // Function to remove carrier input
        function removeCarrier(button) {
            const carrierGroups = document.querySelectorAll('.carrier-input-group');
            if (carrierGroups.length > 1) {
                button.closest('.carrier-input-group').remove();
            } else {
                // If it's the last one, just clear the input
                const input = button.closest('.carrier-input-group').querySelector('input');
                input.value = '';
            }
        }

        // Function to reset form
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                document.getElementById('agentForm').reset();
                $('.select2-multiple').val(null).trigger('change');

                // Reset carriers to just one input
                const container = document.getElementById('carriers-container');
                container.innerHTML = `
                    <div class="carrier-input-group mb-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-truck"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   name="carriers[]" 
                                   placeholder="Enter carrier name">
                            <button class="btn btn-outline-danger" type="button" onclick="removeCarrier(this)">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        // Form validation
        document.getElementById('agentForm').addEventListener('submit', function(e) {
            const activeStates = document.getElementById('active_states').selectedOptions;

            // Validate at least one active state is selected
            if (activeStates.length === 0) {
                e.preventDefault();
                alert('Please select at least one active state.');
                return false;
            }

            // Note: Carrier selection is now optional - agents can be created without carriers
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <style>
        .required::after {
            content: " *";
            color: red;
        }

        // Toggle commission input when carrier checkbox is checked/unchecked
        function toggleCommissionInput(checkbox, carrierId) {
            const commissionInput = document.getElementById('commission_' + carrierId);
            commissionInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                commissionInput.value = '';
            }
        }

        // Select all carriers
        document.getElementById('selectAllCarriers')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.carrier-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                const carrierId = checkbox.value;
                toggleCommissionInput(checkbox, carrierId);
            });
        });

        // Quick Add Carrier Function
        async function addCarrier(event) {
            event.preventDefault();
            
            const name = document.getElementById('carrier_name').value;
            const baseCommission = document.getElementById('base_commission').value || 85;
            const ageMin = document.getElementById('age_min').value || 18;
            const ageMax = document.getElementById('age_max').value || 80;
            
            if (!name.trim()) {
                alert('Please enter carrier name');
                return;
            }
            
            try {
                const response = await fetch('/api/carriers/quick-add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: name,
                        base_commission_percentage: baseCommission,
                        age_min: ageMin,
                        age_max: ageMax,
                        is_active: true,
                        plan_types: ['Term', 'Whole Life']
                    })
                });
                
                if (response.ok) {
                    alert('Carrier added successfully! Please refresh the page.');
                    document.getElementById('quickAddCarrierForm').reset();
                    // Optionally reload the page or update the carriers list
                    location.reload();
                } else {
                    alert('Error adding carrier. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error adding carrier. Please check your connection.');
            }
        }

        // Function to edit carrier
        async function editCarrier(carrierId) {
            try {
                const response = await fetch(`/admin/insurance-carriers/${carrierId}/edit`);
                if (response.ok) {
                    window.open(`/admin/insurance-carriers/${carrierId}/edit`, '_blank');
                } else {
                    // If the edit route fails, open modal instead
                    await loadCarrierInModal(carrierId, 'edit');
                }
            } catch (error) {
                console.error('Error:', error);
                await loadCarrierInModal(carrierId, 'edit');
            }
        }

        // Function to delete carrier
        async function deleteCarrier(carrierId, carrierName) {
            if (!confirm(`Are you sure you want to delete "${carrierName}"? This will affect all agents assigned to this carrier.`)) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/insurance-carriers/${carrierId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    alert('Carrier deleted successfully! Page will refresh.');
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Unable to delete carrier.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting carrier. Please try again.');
            }
        }

        // Function to view carrier details
        async function viewCarrierDetails(carrierId) {
            try {
                const response = await fetch(`/admin/insurance-carriers/${carrierId}`);
                if (response.ok) {
                    const html = await response.text();
                    document.getElementById('carrierDetailsContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('carrierDetailsModal')).show();
                } else {
                    alert('Unable to load carrier details.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading carrier details. Please try again.');
            }
        }

        // Function to load carrier data in modal
        async function loadCarrierInModal(carrierId, mode) {
            try {
                const response = await fetch(`/admin/insurance-carriers/${carrierId}`);
                if (response.ok) {
                    const carrier = await response.json();
                    if (mode === 'edit') {
                        // Populate edit modal
                        document.getElementById('edit_carrier_id').value = carrier.id;
                        document.getElementById('edit_carrier_name').value = carrier.name;
                        document.getElementById('edit_payment_module').value = carrier.payment_module || '';
                        document.getElementById('edit_base_commission').value = carrier.base_commission_percentage || '';
                        document.getElementById('edit_phone').value = carrier.phone || '';
                        document.getElementById('edit_ssn_last4').value = carrier.ssn_last4 || '';
                        document.getElementById('edit_age_min').value = carrier.age_min || '';
                        document.getElementById('edit_age_max').value = carrier.age_max || '';
                        document.getElementById('edit_plan_types').value = Array.isArray(carrier.plan_types) ? carrier.plan_types.join(', ') : carrier.plan_types || '';
                        document.getElementById('edit_calculation_notes').value = carrier.calculation_notes || '';
                        document.getElementById('edit_is_active').checked = carrier.is_active || false;
                        
                        new bootstrap.Modal(document.getElementById('editCarrierModal')).show();
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading carrier data.');
            }
        }

        // Handle add carrier form submission
        document.getElementById('addCarrierForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/admin/insurance-carriers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    alert('Carrier added successfully! Page will refresh.');
                    bootstrap.Modal.getInstance(document.getElementById('addCarrierModal')).hide();
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Please check your input.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error adding carrier. Please try again.');
            }
        });

        // Handle edit carrier form submission
        document.getElementById('editCarrierForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const carrierId = document.getElementById('edit_carrier_id').value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(`/admin/insurance-carriers/${carrierId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    alert('Carrier updated successfully! Page will refresh.');
                    bootstrap.Modal.getInstance(document.getElementById('editCarrierModal')).hide();
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Please check your input.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating carrier. Please try again.');
            }
        });

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            border: 1px solid var(--bs-surface-200) !important;
            border-radius: 0.375rem !important;
            min-height: 38px !important;
        }

        .select2-selection--multiple .select2-selection__choice {
            background-color: var(--bs-primary) !important;
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-white) !important;
            border-radius: 0.25rem !important;
        }

        .carrier-input-group {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
            color: var(--bs-white);
        }

        .text-primary {
            color: var(--bs-gradient-start) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--bs-ui-info) 0%, var(--bs-ui-purple) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/agents/create.blade.php ENDPATH**/ ?>