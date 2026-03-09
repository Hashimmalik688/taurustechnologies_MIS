<?php $__env->startSection('title', 'Add Holiday'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .form-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .form-hdr h4 i { color: #d4af37; font-size: 1.2rem; }
    .f-label { font-size: .72rem; font-weight: 700; color: var(--bs-surface-500); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .25rem; }
    .f-input {
        width: 100%; font-size: .78rem; font-weight: 500; padding: .45rem .65rem;
        border-radius: 12px; border: 1px solid rgba(0,0,0,.08);
        background: var(--bs-card-bg); color: var(--bs-body-color);
        outline: none; transition: border-color .15s;
    }
    .f-input:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .f-input.is-invalid { border-color: #ef4444; }
    textarea.f-input { resize: vertical; min-height: 80px; }
    .f-switch { display: flex; align-items: center; gap: .5rem; font-size: .78rem; }
    .f-switch .form-check-input:checked { background-color: #d4af37; border-color: #d4af37; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="form-hdr">
        <h4><i class="bx bx-calendar-plus"></i> Add Holiday</h4>
        <a href="<?php echo e(route('admin.public-holidays.index')); ?>" class="act-btn a-primary"><i class="mdi mdi-arrow-left"></i> Back</a>
    </div>

    <div class="row">
        <div class="col-lg-7 offset-lg-2">
            <div class="ex-card sec-card">
                <div class="pipe-hdr"><i class="mdi mdi-calendar-plus"></i> New Holiday</div>
                <div class="sec-body">
                    <form action="<?php echo e(route('admin.public-holidays.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="date" class="f-label">Date <span class="text-danger">*</span></label>
                            <input type="text" class="f-input pipe-pill-date <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="date" name="date" value="<?php echo e(old('date')); ?>" placeholder="Select date" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block" style="font-size:.7rem"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="f-label">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" class="f-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name')); ?>" placeholder="e.g., New Year's Day, Eid ul-Fitr" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block" style="font-size:.7rem"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="f-label">Description</label>
                            <textarea class="f-input <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description" rows="3" placeholder="Optional description..."><?php echo e(old('description')); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block" style="font-size:.7rem"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="f-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                                <label for="is_active">Active (attendance will be skipped on this day)</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center" style="border-top:1px solid rgba(0,0,0,.05);padding-top:.75rem;margin-top:.5rem">
                            <a href="<?php echo e(route('admin.public-holidays.index')); ?>" class="act-btn a-danger"><i class="mdi mdi-close"></i> Cancel</a>
                            <button type="submit" class="act-btn a-success" style="padding:.3rem .8rem"><i class="mdi mdi-check"></i> Add Holiday</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/public-holidays/create.blade.php ENDPATH**/ ?>