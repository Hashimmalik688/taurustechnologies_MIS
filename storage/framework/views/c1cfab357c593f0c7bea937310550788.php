<?php $__env->startSection('title'); ?> Add New Partner <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════
   Add Partner — Executive Dashboard Theme
   ═══════════════════════════════════════════════════ */

/* Page Header */
.ap-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem; }
.ap-page-hdr h5 { font-weight:800; font-size:1.05rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.ap-back-btn { font-size:.68rem; font-weight:600; padding:.3rem .7rem; border-radius:.35rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.ap-back-btn:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); }

/* Card System */
.ap-card {
    background:var(--bs-card-bg); border-radius:.6rem; overflow:hidden;
    box-shadow:0 1px 4px rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.04);
    margin-bottom:.65rem;
}
.ap-card-hdr {
    display:flex; align-items:center; gap:.4rem;
    padding:.5rem .75rem; border-bottom:1px solid rgba(0,0,0,.05);
}
.ap-card-hdr h6 { font-weight:700; font-size:.78rem; color:var(--bs-surface-700); margin:0; }
.ap-card-hdr i { color:#556ee6; font-size:.9rem; }
.ap-card-body { padding:.75rem; }

/* Form Elements */
.ap-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--bs-surface-500); margin-bottom:.2rem; display:block; }
.ap-label.required::after { content:' *'; color:#c84646; font-weight:700; }
.ap-input {
    font-size:.72rem; border:1px solid rgba(0,0,0,.08); border-radius:.35rem;
    padding:.4rem .6rem; width:100%; background:var(--bs-card-bg); transition:all .2s;
}
.ap-input:focus { outline:none; border-color:#556ee6; box-shadow:0 0 0 2px rgba(85,110,230,.1); }
.ap-hint { font-size:.55rem; color:var(--bs-surface-400); margin-top:.15rem; }

/* Switch */
.ap-switch { display:flex; align-items:center; gap:.5rem; }
.ap-switch label { font-size:.72rem; font-weight:600; color:var(--bs-surface-600); cursor:pointer; }

/* Carrier section */
.ap-carrier-sec {
    border:1px solid rgba(0,0,0,.06); border-radius:.5rem; padding:.75rem;
    margin-bottom:.5rem; background:rgba(0,0,0,.01); transition:all .2s;
}
.ap-carrier-sec:hover { border-color:rgba(85,110,230,.2); }
.ap-carrier-sec h6 {
    font-weight:700; font-size:.78rem; color:#556ee6;
    display:flex; align-items:center; gap:.3rem; margin-bottom:.5rem;
}
.ap-carrier-sec h6 i { font-size:.85rem; }

/* State settlement rows */
.ap-settlement-row {
    background:rgba(0,0,0,.015); border:1px solid rgba(0,0,0,.04);
    border-radius:.35rem; padding:.5rem .6rem; margin-bottom:.3rem;
}
.ap-settlement-row h6 { font-size:.68rem; font-weight:700; color:var(--bs-surface-700); margin-bottom:.35rem; }

/* Buttons */
.ap-actions { display:flex; gap:.4rem; justify-content:flex-end; margin-top:.5rem; }
.ap-btn {
    font-size:.68rem; font-weight:600; padding:.4rem .9rem;
    border-radius:.4rem; border:none; cursor:pointer;
    display:inline-flex; align-items:center; gap:.25rem; transition:all .2s;
}
.ap-btn.primary {
    background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end));
    color:#fff; box-shadow:0 2px 8px rgba(102,126,234,.25);
}
.ap-btn.primary:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(102,126,234,.35); }
.ap-btn.secondary { background:var(--bs-card-bg); border:1px solid var(--bs-surface-200); color:var(--bs-surface-600); text-decoration:none; }
.ap-btn.secondary:hover { border-color:var(--bs-surface-400); color:var(--bs-surface-700); }

/* Alert */
.ap-alert {
    border-radius:.4rem; padding:.5rem .75rem; font-size:.72rem; margin-bottom:.65rem;
    border:none; border-left:3px solid;
}
.ap-alert.danger { background:rgba(244,106,106,.08); border-left-color:#f46a6a; color:#c84646; }
.ap-alert.danger ul { margin:0; padding-left:1rem; }
.ap-alert.danger li { font-size:.68rem; }

/* Select2 overrides */
.select2-container { width:100% !important; }
.select2-selection { border:1px solid rgba(0,0,0,.08) !important; border-radius:.35rem !important; min-height:34px !important; font-size:.72rem !important; }
.select2-selection--multiple .select2-selection__choice { background:linear-gradient(135deg,#556ee6,#764ba2) !important; border:none !important; color:#fff !important; border-radius:.25rem !important; font-size:.58rem !important; padding:.1rem .3rem !important; }
.select2-container--focus .select2-selection { border-color:#556ee6 !important; box-shadow:0 0 0 2px rgba(85,110,230,.1) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- Page Header -->
<div class="ap-page-hdr">
    <h5><i class="bx bx-user-plus"></i> Add New Partner</h5>
    <a href="<?php echo e(route('admin.partners.index')); ?>" class="ap-back-btn"><i class="bx bx-arrow-back"></i> Partners</a>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="ap-alert danger">
        <i class="bx bx-error-circle me-1"></i> <strong>Please fix the following errors:</strong>
        <ul class="mt-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<form action="<?php echo e(route('admin.partners.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <!-- Basic Information -->
    <div class="ap-card">
        <div class="ap-card-hdr"><i class="bx bx-user"></i><h6>Basic Information</h6></div>
        <div class="ap-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="ap-label required">Partner Name</label>
                    <input type="text" class="ap-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="name" value="<?php echo e(old('name')); ?>" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.6rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="ap-label required">Partner Code</label>
                    <input type="text" class="ap-input <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="code" value="<?php echo e(old('code')); ?>" maxlength="10" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.6rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ap-hint">Unique identifier (max 10 chars), e.g. E-1, Y-1, F-1</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact & Commission -->
    <div class="ap-card">
        <div class="ap-card-hdr"><i class="bx bx-id-card"></i><h6>Contact & Commission</h6></div>
        <div class="ap-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="ap-label">Email</label>
                    <input type="email" class="ap-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="email" value="<?php echo e(old('email')); ?>" placeholder="partner@example.com">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.6rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="ap-label">Phone</label>
                    <input type="text" class="ap-input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="phone" value="<?php echo e(old('phone')); ?>" placeholder="(555) 123-4567">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.6rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="ap-label">Last 4 SSN</label>
                    <input type="text" class="ap-input <?php $__errorArgs = ['ssn_last4'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="ssn_last4" value="<?php echo e(old('ssn_last4')); ?>" maxlength="4" pattern="[0-9]{4}" placeholder="1234">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ssn_last4'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.6rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ap-hint">4 digits only</div>
                </div>
                <div class="col-md-4">
                    <label class="ap-label">Our Commission %</label>
                    <input type="number" class="ap-input <?php $__errorArgs = ['our_commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="our_commission_percentage" value="<?php echo e(old('our_commission_percentage', 0)); ?>"
                           min="0" max="100" step="0.01" placeholder="0.00">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['our_commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.6rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ap-hint">% of total revenue partner owes us</div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div>
                        <div class="ap-switch">
                            <input class="form-check-input" type="checkbox" id="is_active"
                                   name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                            <label for="is_active">Active Partner</label>
                        </div>
                        <div class="ap-hint">Inactive partners cannot be assigned to leads</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carrier & State Assignments -->
    <div class="ap-card">
        <div class="ap-card-hdr"><i class="bx bx-briefcase"></i><h6>Carrier & State Assignments</h6></div>
        <div class="ap-card-body">
            <div style="font-size:.62rem;color:var(--bs-surface-500);padding:.35rem .5rem;background:rgba(85,110,230,.04);border-radius:.3rem;border-left:3px solid #556ee6;margin-bottom:.6rem;">
                <i class="bx bx-info-circle me-1"></i> Assign insurance carriers and select states for this partner
            </div>

            <div id="carrier-states-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="ap-carrier-sec">
                        <h6><i class="bx bx-shield-quarter"></i> <?php echo e($carrier->name); ?></h6>

                        <div class="mb-2">
                            <label class="ap-label">Select States</label>
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
                            <div class="ap-hint">Select multiple states where partner is licensed</div>
                        </div>

                        <div class="settlement-inputs-container" data-carrier-id="<?php echo e($carrier->id); ?>"></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div class="ap-actions">
        <a href="<?php echo e(route('admin.partners.index')); ?>" class="ap-btn secondary"><i class="bx bx-x"></i> Cancel</a>
        <button type="submit" class="ap-btn primary"><i class="bx bx-save"></i> Create Partner</button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.state-select').select2({ theme:'bootstrap-5', placeholder:'Select states...', allowClear:true });

    $('.state-select').on('change', function() {
        const carrierId = $(this).data('carrier-id');
        const selectedStates = $(this).val() || [];
        const container = $(`.settlement-inputs-container[data-carrier-id="${carrierId}"]`);
        container.empty();

        if (selectedStates.length > 0) {
            selectedStates.forEach(state => {
                container.append(`
                    <div class="ap-settlement-row">
                        <h6>${state} — Settlement Percentages</h6>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="ap-label">Level %</label>
                                <input type="number" step="0.01" min="0" max="100" class="ap-input"
                                       name="settlement_level[${carrierId}][${state}]" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="ap-label">Graded %</label>
                                <input type="number" step="0.01" min="0" max="100" class="ap-input"
                                       name="settlement_graded[${carrierId}][${state}]" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="ap-label">GI %</label>
                                <input type="number" step="0.01" min="0" max="100" class="ap-input"
                                       name="settlement_gi[${carrierId}][${state}]" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="ap-label">Modified %</label>
                                <input type="number" step="0.01" min="0" max="100" class="ap-input"
                                       name="settlement_modified[${carrierId}][${state}]" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                `);
            });
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/partners/create.blade.php ENDPATH**/ ?>