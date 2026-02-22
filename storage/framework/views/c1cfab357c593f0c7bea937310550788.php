<?php $__env->startSection('title'); ?>
    Add New Partner
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        /* ===== Animated Background ===== */
        .partners-animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(135deg, var(--bs-gradient-start)10 0%, var(--bs-gradient-end)10 100%);
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: float 15s infinite ease-in-out;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
            top: -200px;
            right: -200px;
        }

        .orb-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, var(--bs-ui-warning), var(--bs-ui-danger));
            bottom: -175px;
            left: -175px;
            animation-delay: 7s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }

        .required::after {
            content: " *";
            color: var(--bs-ui-danger);
            font-weight: bold;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
            color: var(--bs-white);
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem;
            border: none;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: var(--bs-surface-600);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--bs-surface-200);
            border-radius: 10px;
            padding: 0.625rem 0.875rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--bs-gradient-start);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .form-check-input:checked {
            background-color: var(--bs-gradient-start);
            border-color: var(--bs-gradient-start);
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
            border: none;
            color: var(--bs-white);
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: var(--bs-white);
        }

        .btn-secondary {
            border-radius: 12px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            border: 2px solid var(--bs-surface-200) !important;
            border-radius: 10px !important;
            min-height: 42px !important;
            transition: all 0.3s ease !important;
        }

        .select2-container--focus .select2-selection {
            border-color: var(--bs-gradient-start) !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15) !important;
        }

        .select2-selection--multiple .select2-selection__choice {
            background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end)) !important;
            border: none !important;
            color: var(--bs-white) !important;
            border-radius: 8px !important;
            padding: 0.25rem 0.5rem !important;
        }

        .carrier-state-section {
            border: 2px solid var(--bs-surface-200);
            padding: 1.5rem;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.03), rgba(118, 75, 162, 0.03));
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .carrier-state-section:hover {
            border-color: var(--bs-gradient-start);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        }

        .carrier-state-section h6 {
            color: var(--bs-gradient-start);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .state-settlement-row {
            background-color: var(--bs-card-bg);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            border: 1px solid var(--bs-surface-200);
            transition: all 0.2s ease;
        }

        .state-settlement-row:hover {
            border-color: var(--bs-gradient-start);
            transform: translateX(5px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15));
            color: var(--bs-ui-success);
            border-left: 4px solid var(--bs-ui-success);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(238, 9, 121, 0.15), rgba(255, 106, 0, 0.15));
            color: var(--bs-ui-danger);
            border-left: 4px solid var(--bs-ui-danger);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <a href="<?php echo e(route('admin.partners.index')); ?>">Partners</a>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Add New Partner
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Animated Background -->
    <div class="partners-animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row">
        <div class="col-xl-12">
            <div class="card glass-card">
                <div class="card-header">
                    <h5>
                        <i class="mdi mdi-account-plus me-2"></i>
                        Create New Partner
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.partners.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Partner Name</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="name" name="name" value="<?php echo e(old('name')); ?>" required>
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
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label required">Partner Code</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="code" name="code" value="<?php echo e(old('code')); ?>" maxlength="10" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <small class="text-muted">Unique identifier for the partner (max 10 characters)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="email" name="email" value="<?php echo e(old('email')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
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
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="phone" name="phone" value="<?php echo e(old('phone')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
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
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ssn_last4" class="form-label">Last 4 SSN</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['ssn_last4'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="ssn_last4" name="ssn_last4" value="<?php echo e(old('ssn_last4')); ?>" 
                                           maxlength="4" pattern="[0-9]{4}">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ssn_last4'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <small class="text-muted">4 digits only</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="our_commission_percentage" class="form-label">
                                        <i class="mdi mdi-percent me-1"></i>
                                        Our Commission Percentage
                                    </label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['our_commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="our_commission_percentage" name="our_commission_percentage" 
                                        value="<?php echo e(old('our_commission_percentage', 0)); ?>"
                                        min="0" max="100" step="0.01" placeholder="e.g., 15.00">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['our_commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <small class="text-muted">Percentage of total revenue that partner owes us</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                </div>
                                <small class="text-muted">Inactive partners cannot be assigned to leads</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Carrier & State Assignments -->
                        <h5 class="mb-3">
                            <i class="mdi mdi-briefcase-outline me-2"></i>
                            Carrier & State Assignments
                        </h5>
                        <p class="text-muted mb-4">Assign insurance carriers and states to this partner</p>

                        <div id="carrier-states-container">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="carrier-state-section">
                                    <h6>
                                        <i class="mdi mdi-shield-check me-2"></i>
                                        <?php echo e($carrier->name); ?>

                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label">Select States</label>
                                        <select name="carrier_states[<?php echo e($carrier->id); ?>][]" 
                                                class="form-select state-select" 
                                                multiple="multiple" 
                                                data-carrier-id="<?php echo e($carrier->id); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('app.us_states', [
                                                'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
                                                'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD',
                                                'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ',
                                                'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC',
                                                'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'
                                            ]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <small class="text-muted">Select multiple states where partner is licensed</small>
                                    </div>

                                    <!-- Settlement percentages will be added dynamically -->
                                    <div class="settlement-inputs-container" data-carrier-id="<?php echo e($carrier->id); ?>">
                                        <!-- Dynamic inputs will appear here -->
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('admin.partners.index')); ?>" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="mdi mdi-content-save me-1"></i>Create Partner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for state selection
            $('.state-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select states...',
                allowClear: true
            });

            // When states are selected, create settlement input fields
            $('.state-select').on('change', function() {
                const carrierId = $(this).data('carrier-id');
                const selectedStates = $(this).val() || [];
                const container = $(`.settlement-inputs-container[data-carrier-id="${carrierId}"]`);
                
                // Clear existing inputs
                container.empty();

                if (selectedStates.length > 0) {
                    selectedStates.forEach(state => {
                        const stateRow = `
                            <div class="state-settlement-row">
                                <h6 class="mb-3"><strong>${state}</strong> - Settlement Percentages</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Level %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_level[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Graded %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_graded[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">GI %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_gi[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Modified %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_modified[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(stateRow);
                    });
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/partners/create.blade.php ENDPATH**/ ?>