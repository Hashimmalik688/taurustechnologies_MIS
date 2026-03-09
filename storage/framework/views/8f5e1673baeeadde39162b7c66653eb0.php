<?php $__env->startSection('title'); ?> Add Insurance Carrier <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   Add Carrier — Executive Dashboard Theme
   ═══════════════════════════════════════════════════ */

/* Page Header */
.ac-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem; }
.ac-page-hdr h5 { font-weight:800; font-size:1.05rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.ac-back-btn { font-size:.68rem; font-weight:600; padding:.3rem .7rem; border-radius:.35rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.ac-back-btn:hover { border-color:#556ee6; color:#556ee6; }

/* Card System */
.ac-card {
    background:var(--bs-card-bg); border-radius:.6rem; overflow:hidden;
    box-shadow:0 1px 4px rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.04);
    margin-bottom:.65rem;
}
.ac-card-hdr {
    display:flex; align-items:center; gap:.4rem;
    padding:.5rem .75rem; border-bottom:1px solid rgba(0,0,0,.05);
}
.ac-card-hdr h6 { font-weight:700; font-size:.78rem; color:var(--bs-surface-700); margin:0; }
.ac-card-hdr i { color:#556ee6; font-size:.9rem; }
.ac-card-body { padding:.75rem; }

/* Form Elements */
.ac-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--bs-surface-500); margin-bottom:.2rem; display:block; }
.ac-label.required::after { content:' *'; color:#c84646; font-weight:700; }
.ac-input, .ac-select, .ac-textarea {
    font-size:.72rem; border:1px solid rgba(0,0,0,.08); border-radius:.35rem;
    padding:.4rem .6rem; width:100%; background:var(--bs-card-bg); transition:all .2s;
}
.ac-input:focus, .ac-select:focus, .ac-textarea:focus { outline:none; border-color:#556ee6; box-shadow:0 0 0 2px rgba(85,110,230,.1); }
.ac-hint { font-size:.55rem; color:var(--bs-surface-400); margin-top:.15rem; }

/* Switch */
.ac-switch { display:flex; align-items:center; gap:.5rem; }
.ac-switch label { font-size:.72rem; font-weight:600; color:var(--bs-surface-600); cursor:pointer; }

/* Bracket row */
.ac-bracket {
    border-left:3px solid #556ee6; border-radius:.4rem; padding:.55rem .65rem;
    margin-bottom:.4rem; background:rgba(85,110,230,.03); border:1px solid rgba(85,110,230,.08);
    border-left:3px solid #556ee6; transition:all .15s;
}
.ac-bracket:hover { border-color:rgba(85,110,230,.18); background:rgba(85,110,230,.05); }
.ac-bracket .ac-label { text-transform:none; letter-spacing:0; font-size:.6rem; }
.ac-bracket .ac-input { font-size:.68rem; padding:.3rem .45rem; }

/* Formula info bar */
.ac-formula-bar {
    display:flex; align-items:center; gap:.4rem; font-size:.65rem;
    padding:.4rem .65rem; border-radius:.35rem; margin-bottom:.5rem;
    background:rgba(85,110,230,.06); color:#556ee6; border:1px solid rgba(85,110,230,.1);
}
.ac-formula-bar i { font-size:.85rem; }
.ac-formula-bar strong { font-weight:700; }
.ac-formula-bar small { color:var(--bs-surface-500); }

/* Buttons */
.ac-actions { display:flex; gap:.4rem; justify-content:flex-end; margin-top:.75rem; }
.ac-btn {
    font-size:.68rem; font-weight:600; padding:.4rem .9rem;
    border-radius:.4rem; border:none; cursor:pointer;
    display:inline-flex; align-items:center; gap:.25rem; transition:all .2s;
}
.ac-btn.primary {
    background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end));
    color:#fff; box-shadow:0 2px 8px rgba(102,126,234,.25);
}
.ac-btn.primary:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(102,126,234,.35); }
.ac-btn.secondary { background:var(--bs-card-bg); border:1px solid var(--bs-surface-200); color:var(--bs-surface-600); text-decoration:none; }
.ac-btn.secondary:hover { border-color:var(--bs-surface-400); color:var(--bs-surface-700); }
.ac-btn.add-bracket { font-size:.62rem; padding:.25rem .55rem; border-radius:.3rem; background:rgba(85,110,230,.08); color:#556ee6; border:1px solid rgba(85,110,230,.12); }
.ac-btn.add-bracket:hover { background:rgba(85,110,230,.14); }
.ac-btn.remove { font-size:.6rem; padding:.2rem .4rem; border-radius:.25rem; background:rgba(244,106,106,.06); color:#f46a6a; border:1px solid rgba(244,106,106,.12); }
.ac-btn.remove:hover { background:rgba(244,106,106,.14); }

/* Alert */
.ac-alert {
    border-radius:.4rem; padding:.5rem .75rem; font-size:.72rem; margin-bottom:.65rem;
    border:none; border-left:3px solid;
}
.ac-alert.danger { background:rgba(244,106,106,.08); border-left-color:#f46a6a; color:#c84646; }
.ac-alert.danger ul { margin:0; padding-left:1rem; }
.ac-alert.danger li { font-size:.68rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- Page Header -->
<div class="ac-page-hdr">
    <h5><i class="bx bx-building-house" style="color:#556ee6"></i> Add New Carrier</h5>
    <a href="<?php echo e(route('admin.insurance-carriers.index')); ?>" class="ac-back-btn"><i class="bx bx-arrow-back"></i> Back to Carriers</a>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
<div class="ac-alert danger">
    <strong><i class="bx bx-error-circle me-1"></i>Please fix the following:</strong>
    <ul class="mt-1"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></ul>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<form action="<?php echo e(route('admin.insurance-carriers.store')); ?>" method="POST" id="carrierForm">
    <?php echo csrf_field(); ?>

    <!-- Basic Information -->
    <div class="ac-card">
        <div class="ac-card-hdr"><i class="bx bx-info-circle"></i><h6>Basic Information</h6></div>
        <div class="ac-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="ac-label required">Carrier Name</label>
                    <input type="text" class="ac-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name')); ?>" required placeholder="e.g., American Amicable">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.62rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ac-hint">Company name as it appears on policies</div>
                </div>
                <div class="col-md-3">
                    <label class="ac-label required">Payment Module</label>
                    <select class="ac-select <?php $__errorArgs = ['payment_module'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="payment_module" name="payment_module" required>
                        <option value="on_draft" <?php echo e(old('payment_module', 'on_draft') == 'on_draft' ? 'selected' : ''); ?>>On Draft</option>
                        <option value="on_issue" <?php echo e(old('payment_module') == 'on_issue' ? 'selected' : ''); ?>>On Issue</option>
                        <option value="as_earned" <?php echo e(old('payment_module') == 'as_earned' ? 'selected' : ''); ?>>As Earned</option>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['payment_module'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.62rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ac-hint">How the company gets paid</div>
                </div>
                <div class="col-md-3">
                    <label class="ac-label">Status</label>
                    <div class="ac-switch" style="margin-top:.3rem">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                        <label for="is_active">Active Carrier</label>
                    </div>
                    <div class="ac-hint">Visible in dropdowns when active</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Types -->
    <div class="ac-card">
        <div class="ac-card-hdr"><i class="bx bx-list-check"></i><h6>Plan Types</h6></div>
        <div class="ac-card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="ac-label">Available Plans</label>
                    <input type="text" class="ac-input <?php $__errorArgs = ['plan_types'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="plan_types" name="plan_types" value="<?php echo e(old('plan_types', 'G.I, Graded, Level, Modified')); ?>" placeholder="G.I, Graded, Level, Modified">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['plan_types'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.62rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ac-hint">Comma-separated list of plan types offered by this carrier</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Brackets -->
    <div class="ac-card">
        <div class="ac-card-hdr">
            <i class="bx bx-bar-chart-alt-2"></i><h6>Age-Based Commission Brackets</h6>
            <div class="ms-auto">
                <button type="button" class="ac-btn add-bracket" id="addBracket"><i class="bx bx-plus"></i> Add Bracket</button>
            </div>
        </div>
        <div class="ac-card-body">
            <div class="ac-formula-bar">
                <i class="bx bx-calculator"></i>
                <span><strong>Formula:</strong> Monthly Premium × 9 months × Commission %</span>
                <span style="margin-left:auto"><small>e.g. $100/mo × 9 × 75% = $675</small></span>
            </div>

            <div id="brackets-container">
                <!-- Dynamically added brackets -->
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="ac-label">Fallback Base Commission %</label>
                    <input type="number" step="0.01" min="0" max="100" class="ac-input <?php $__errorArgs = ['base_commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="base_commission_percentage" name="base_commission_percentage" value="<?php echo e(old('base_commission_percentage')); ?>" placeholder="75.00">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['base_commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.62rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ac-hint">Used when age doesn't match any bracket</div>
                </div>
                <div class="col-md-8">
                    <label class="ac-label">Calculation Notes</label>
                    <textarea class="ac-textarea <?php $__errorArgs = ['calculation_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="calculation_notes" name="calculation_notes" rows="2" placeholder="Any special rules, exceptions, or additional context"><?php echo e(old('calculation_notes')); ?></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['calculation_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size:.62rem"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="ac-actions">
        <a href="<?php echo e(route('admin.insurance-carriers.index')); ?>" class="ac-btn secondary"><i class="bx bx-x"></i> Cancel</a>
        <button type="submit" class="ac-btn primary"><i class="bx bx-save"></i> Create Carrier</button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
let bracketIndex = 0;

// Check if opened in modal mode
const urlParams = new URLSearchParams(window.location.search);
const isModal = urlParams.get('modal') === '1';

if (isModal) {
    // Hide page header in modal mode
    const hdr = document.querySelector('.ac-page-hdr');
    if (hdr) hdr.style.display = 'none';
}

document.getElementById('addBracket').addEventListener('click', function() {
    const container = document.getElementById('brackets-container');
    const newBracket = document.createElement('div');
    newBracket.className = 'ac-bracket';
    newBracket.setAttribute('data-index', bracketIndex);
    newBracket.innerHTML = `
        <div class="row align-items-end g-2">
            <div class="col-md-3">
                <label class="ac-label">Min Age</label>
                <input type="number" name="brackets[${bracketIndex}][age_min]" class="ac-input" min="0" max="120" required>
            </div>
            <div class="col-md-3">
                <label class="ac-label">Max Age</label>
                <input type="number" name="brackets[${bracketIndex}][age_max]" class="ac-input" min="0" max="120" required>
            </div>
            <div class="col-md-3">
                <label class="ac-label">Commission %</label>
                <input type="number" name="brackets[${bracketIndex}][commission_percentage]" class="ac-input" step="0.01" min="0" max="100" required>
            </div>
            <div class="col-md-2">
                <label class="ac-label">Notes</label>
                <input type="text" name="brackets[${bracketIndex}][notes]" class="ac-input" placeholder="Optional">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="ac-btn remove remove-bracket"><i class="bx bx-trash"></i></button>
            </div>
        </div>
    `;
    container.appendChild(newBracket);
    bracketIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.closest('.remove-bracket')) {
        if (confirm('Remove this bracket?')) {
            e.target.closest('.ac-bracket').remove();
        }
    }
});

// Handle form submission in modal mode
if (isModal) {
    const form = document.getElementById('carrierForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.opener) {
                    window.opener.postMessage({ type: 'carrierCreated', carrier: data.carrier }, '*');
                }
                alert('Carrier created successfully! The page will refresh.');
                window.close();
            } else {
                alert('Error: ' + (data.message || 'Failed to create carrier'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the carrier.');
        });
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/insurance-carriers/create.blade.php ENDPATH**/ ?>