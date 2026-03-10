<?php $__env->startSection('title', 'General Journal Entry'); ?>

<?php $__env->startSection('css'); ?>
<style>
    .line-row td { vertical-align: middle; }
    .totals-bar  { background: #f8f9fa; font-weight: 700; }
    .balance-ok   { color: #198754; }
    .balance-bad  { color: #dc3545; }
    .btn-remove { padding: 0.2rem 0.5rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
            <h4 class="mb-1 text-print-body u-fw-600">
                <i class="bx bx-edit me-2 text-gold"></i>
                General Journal Entry
            </h4>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bx bx-error-circle me-2"></i>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($err); ?><br> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.accounting.journal.store')); ?>" id="journalForm">
        <?php echo csrf_field(); ?>

        
        <div class="card mb-3">
            <div class="card-header fw-semibold">Entry Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" class="form-control <?php $__errorArgs = ['entry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('entry_date', date('Y-m-d'))); ?>" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['entry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('description')); ?>" placeholder="Narration…" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Reference / Policy #</label>
                        <input type="text" name="reference" class="form-control"
                               value="<?php echo e(old('reference')); ?>" placeholder="Optional">
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Journal Lines</span>
                <button type="button" id="addLineBtn" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-plus me-1"></i> Add Line
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="linesTable">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:250px">Account <span class="text-danger">*</span></th>
                                <th style="min-width:200px">Partner (optional)</th>
                                <th style="min-width:130px" class="text-end">Debit</th>
                                <th style="min-width:130px" class="text-end">Credit</th>
                                <th style="min-width:200px">Line Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="linesBody">
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 2; $i++): ?>
                            <tr class="line-row">
                                <td>
                                    <select name="lines[<?php echo e($i); ?>][account_id]" class="form-select form-select-sm" required>
                                        <option value="">— Select Account —</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($acct->id); ?>" <?php echo e(old("lines.$i.account_id") == $acct->id ? 'selected' : ''); ?>>
                                                <?php echo e($acct->account_code); ?> – <?php echo e($acct->account_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="lines[<?php echo e($i); ?>][partner_id]" class="form-select form-select-sm">
                                        <option value="">— None —</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($p->id); ?>" <?php echo e(old("lines.$i.partner_id") == $p->id ? 'selected' : ''); ?>>
                                                <?php echo e($p->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="lines[<?php echo e($i); ?>][debit]" step="0.01" min="0"
                                           class="form-control form-control-sm text-end line-debit"
                                           value="<?php echo e(old('lines.'.$i.'.debit', '0.00')); ?>">
                                </td>
                                <td>
                                    <input type="number" name="lines[<?php echo e($i); ?>][credit]" step="0.01" min="0"
                                           class="form-control form-control-sm text-end line-credit"
                                           value="<?php echo e(old('lines.'.$i.'.credit', '0.00')); ?>">
                                </td>
                                <td>
                                    <input type="text" name="lines[<?php echo e($i); ?>][description]" class="form-control form-control-sm"
                                           value="<?php echo e(old('lines.'.$i.'.description')); ?>" placeholder="Optional">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove" <?php echo e($i < 2 ? 'style=opacity:.4' : ''); ?>>
                                        <i class="bx bx-x"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="totals-bar">
                                <td colspan="2" class="text-end">Totals</td>
                                <td class="text-end" id="totalDebit">0.00</td>
                                <td class="text-end" id="totalCredit">0.00</td>
                                <td colspan="2">
                                    <span id="balanceStatus" class="small"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" id="submitBtn" class="btn btn-dark" disabled>
                <i class="bx bx-save me-1"></i> Post Journal Entry
            </button>
            <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
(function () {
    // Account options HTML (re-used when adding rows)
    var accountOptions = `<option value="">— Select Account —</option>` +
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        `<option value="<?php echo e($acct->id); ?>"><?php echo e($acct->account_code); ?> – <?php echo e(addslashes($acct->account_name)); ?></option>` +
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        ``;

    var partnerOptions = `<option value="">— None —</option>` +
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        `<option value="<?php echo e($p->id); ?>"><?php echo e(addslashes($p->name)); ?></option>` +
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        ``;

    var rowIndex = <?php echo e(max(2, old('lines') ? count(old('lines')) : 2)); ?>;

    function makeRow(idx) {
        return `<tr class="line-row">
            <td><select name="lines[${idx}][account_id]" class="form-select form-select-sm" required>${accountOptions}</select></td>
            <td><select name="lines[${idx}][partner_id]" class="form-select form-select-sm">${partnerOptions}</select></td>
            <td><input type="number" name="lines[${idx}][debit]"  step="0.01" min="0" value="0.00" class="form-control form-control-sm text-end line-debit"></td>
            <td><input type="number" name="lines[${idx}][credit]" step="0.01" min="0" value="0.00" class="form-control form-control-sm text-end line-credit"></td>
            <td><input type="text" name="lines[${idx}][description]" class="form-control form-control-sm" placeholder="Optional"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove"><i class="bx bx-x"></i></button></td>
        </tr>`;
    }

    document.getElementById('addLineBtn').addEventListener('click', function () {
        document.getElementById('linesBody').insertAdjacentHTML('beforeend', makeRow(rowIndex++));
        recalculate();
    });

    document.getElementById('linesBody').addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove');
        if (!btn) return;
        var rows = document.querySelectorAll('#linesBody .line-row');
        if (rows.length <= 2) { alert('A journal entry needs at least 2 lines.'); return; }
        btn.closest('tr').remove();
        recalculate();
    });

    document.getElementById('linesBody').addEventListener('input', function (e) {
        if (e.target.classList.contains('line-debit') || e.target.classList.contains('line-credit')) {
            recalculate();
        }
    });

    function recalculate() {
        var dr = 0, cr = 0;
        document.querySelectorAll('.line-debit').forEach(function(el)  { dr += parseFloat(el.value) || 0; });
        document.querySelectorAll('.line-credit').forEach(function(el) { cr += parseFloat(el.value) || 0; });
        document.getElementById('totalDebit').textContent  = dr.toFixed(2);
        document.getElementById('totalCredit').textContent = cr.toFixed(2);
        var balanced = Math.abs(dr - cr) < 0.005 && dr > 0;
        var status   = document.getElementById('balanceStatus');
        var btn      = document.getElementById('submitBtn');
        if (balanced) {
            status.innerHTML = '<span class="balance-ok"><i class="bx bx-check-circle"></i> Balanced</span>';
            btn.disabled = false;
        } else {
            var diff = Math.abs(dr - cr).toFixed(2);
            status.innerHTML = '<span class="balance-bad"><i class="bx bx-error"></i> Off by ' + diff + '</span>';
            btn.disabled = true;
        }
    }

    recalculate();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/journal/create.blade.php ENDPATH**/ ?>