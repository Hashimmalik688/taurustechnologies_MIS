<?php $__env->startSection('title'); ?>
    Verifier Submission
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* ── Verifier Submission Form — sl-* Design System ── */

    /* Top bar */
    .sl-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; flex-wrap: wrap; gap: .75rem;
    }
    .sl-topbar-left { display: flex; align-items: center; gap: .75rem; }
    .sl-page-title {
        font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-page-title i { color: #d4af37; font-size: 1.2rem; }
    .sl-topbar-right { display: flex; align-items: center; gap: .5rem; }

    /* Card */
    .sl-card {
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
    }
    .sl-card-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: .85rem 1.25rem;
        background: linear-gradient(135deg, rgba(212,175,55,.08), rgba(212,175,55,.02));
        border-bottom: 1px solid rgba(212,175,55,.12);
    }
    .sl-card-header h4 {
        font-size: .88rem; font-weight: 700; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-card-header h4 i { color: #d4af37; }
    .sl-card-body { padding: 1.5rem; }

    /* Section headers */
    .sl-section-title {
        font-size: .82rem; font-weight: 700; color: #d4af37;
        margin: 0 0 1rem 0; padding-bottom: .5rem;
        border-bottom: 1px solid rgba(212,175,55,.12);
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-section-title i { font-size: .95rem; }

    /* Form controls */
    .sl-form-label {
        font-size: .72rem; font-weight: 700; color: #475569;
        text-transform: uppercase; letter-spacing: .3px;
        margin-bottom: .35rem; display: block;
    }
    .sl-form-label .req { color: #ef4444; margin-left: 1px; }

    .sl-form-input, .sl-form-select {
        width: 100%;
        font-size: .8rem; font-weight: 500;
        padding: .55rem .85rem;
        border-radius: 22px !important;
        border: 1px solid rgba(0,0,0,.09) !important;
        background: #fff; color: #334155;
        outline: none;
        transition: all .2s;
    }
    .sl-form-input:focus, .sl-form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212,175,55,.12);
    }
    .sl-form-input::placeholder { color: #94a3b8; font-weight: 400; }

    .sl-form-input[readonly] {
        background: rgba(248,250,252,.8);
        color: #64748b;
        cursor: not-allowed;
    }

    .sl-form-select {
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        padding-right: 2rem;
        cursor: pointer;
    }

    .sl-form-group { margin-bottom: 1.1rem; }
    .sl-form-group .invalid-feedback { font-size: .7rem; margin-top: .25rem; }
    .sl-form-input.is-invalid, .sl-form-select.is-invalid {
        border-color: #ef4444 !important;
    }

    /* Divider */
    .sl-divider {
        border: none; border-top: 1px solid rgba(0,0,0,.05);
        margin: 1.5rem 0;
    }

    /* Submit button */
    .sl-btn-submit {
        display: inline-flex; align-items: center; gap: .4rem;
        background: linear-gradient(135deg, #d4af37, #b8941f);
        color: #0f172a; font-size: .82rem; font-weight: 700;
        padding: .6rem 1.5rem; border-radius: 22px;
        border: none; cursor: pointer;
        box-shadow: 0 4px 15px rgba(212,175,55,.3);
        transition: all .2s;
    }
    .sl-btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(212,175,55,.4);
    }
    .sl-btn-submit:active { transform: translateY(0); }

    .sl-btn-dash {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .75rem; font-weight: 600; color: #475569;
        padding: .42rem .85rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; text-decoration: none;
        transition: all .15s;
    }
    .sl-btn-dash:hover { border-color: #d4af37; color: #92760d; }

    /* Success alert */
    .sl-alert-success {
        display: flex; align-items: center; gap: .5rem;
        padding: .65rem 1rem; border-radius: 14px;
        background: rgba(16,185,129,.08);
        border: 1px solid rgba(16,185,129,.15);
        color: #065f46; font-size: .8rem; font-weight: 600;
        margin-bottom: 1rem;
    }
    .sl-alert-success i { color: #10b981; font-size: 1.1rem; }
    .sl-alert-close {
        margin-left: auto; background: none; border: none;
        color: #065f46; cursor: pointer; font-size: 1rem;
        opacity: .6; transition: opacity .15s;
    }
    .sl-alert-close:hover { opacity: 1; }

    /* ── Dark mode ── */
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: #e2e8f0; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card {
        background: rgba(30,41,59,.85); border-color: rgba(255,255,255,.06);
        box-shadow: 0 4px 24px rgba(0,0,0,.25);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card-header {
        background: linear-gradient(135deg, rgba(212,175,55,.06), rgba(212,175,55,.02));
        border-color: rgba(212,175,55,.1);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card-header h4 { color: #e2e8f0; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-section-title { border-color: rgba(212,175,55,.1); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-label { color: #94a3b8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-select {
        background: rgba(15,23,42,.6) !important; border-color: rgba(255,255,255,.1) !important;
        color: #e2e8f0;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input::placeholder { color: #475569; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input[readonly] {
        background: rgba(15,23,42,.4) !important; color: #64748b;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E") !important;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input:focus,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212,175,55,.15);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-divider { border-color: rgba(255,255,255,.06); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-btn-dash {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-alert-success {
        background: rgba(16,185,129,.1); border-color: rgba(16,185,129,.15); color: #6ee7b7;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-alert-success i { color: #34d399; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-alert-close { color: #6ee7b7; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h2 class="sl-page-title"><i class="bx bx-clipboard"></i> <?php echo e(ucfirst($team ?? 'peregrine')); ?> — New Submission</h2>
        </div>
        <div class="sl-topbar-right">
            <a href="<?php echo e(route('verifier.dashboard')); ?>" class="sl-btn-dash">
                <i class="bx bx-list-ul"></i> My Dashboard
            </a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="sl-alert-success">
            <i class="bx bx-check-circle"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="sl-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="sl-card">
        <div class="sl-card-header">
            <h4><i class="bx bx-edit-alt"></i> Verification Form</h4>
        </div>
        <div class="sl-card-body">
            <form method="POST" action="<?php echo e(isset($team) ? route('verifier.store.team', ['team' => $team]) : route('verifier.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="team" value="<?php echo e($team ?? 'peregrine'); ?>">

                <div class="row">
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Date <span class="req">*</span></label>
                        <input type="date" name="date" class="sl-form-input <?php $__errorArgs = ['date'];
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
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Verifier Name <span class="req">*</span></label>
                        <input type="text" class="sl-form-input" value="<?php echo e(auth()->user()->name); ?>" readonly tabindex="-1">
                        <input type="hidden" name="verifier_name" value="<?php echo e(auth()->user()->name); ?>">
                    </div>
                    <div class="col-md-5 sl-form-group">
                        <label class="sl-form-label">Live Closer <span class="req">*</span></label>
                        <select name="closer_id" class="sl-form-select <?php $__errorArgs = ['closer_id'];
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

                <hr class="sl-divider">
                <h5 class="sl-section-title"><i class="bx bx-user"></i> Customer Information</h5>

                <div class="row">
                    <div class="col-md-6 sl-form-group">
                        <label class="sl-form-label">Customer Name <span class="req">*</span></label>
                        <input type="text" name="cn_name" class="sl-form-input <?php $__errorArgs = ['cn_name'];
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
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Date of Birth <span class="req">*</span></label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="sl-form-input <?php $__errorArgs = ['date_of_birth'];
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
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Age <span class="req">*</span></label>
                        <input type="number" id="age" name="age" class="sl-form-input <?php $__errorArgs = ['age'];
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

                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Gender <span class="req">*</span></label>
                        <select name="gender" class="sl-form-select <?php $__errorArgs = ['gender'];
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
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Phone Number <span class="req">*</span></label>
                        <input type="tel" name="phone_number" class="sl-form-input <?php $__errorArgs = ['phone_number'];
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
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Account Type <span class="req">*</span></label>
                        <select name="account_type" class="sl-form-select <?php $__errorArgs = ['account_type'];
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

                    <div class="col-md-8 sl-form-group">
                        <label class="sl-form-label">Address <span class="req">*</span></label>
                        <input type="text" name="address" class="sl-form-input <?php $__errorArgs = ['address'];
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
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">State <span class="req">*</span></label>
                        <select name="state" class="sl-form-select <?php $__errorArgs = ['state'];
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
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Zip Code <span class="req">*</span></label>
                        <input type="text" name="zip_code" class="sl-form-input <?php $__errorArgs = ['zip_code'];
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

                <div class="text-end mt-3">
                    <button type="submit" class="sl-btn-submit">
                        <i class="bx bx-send"></i> Submit to Closer
                    </button>
                </div>
            </form>
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