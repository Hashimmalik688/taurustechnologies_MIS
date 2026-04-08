<?php $__env->startSection('title', 'Record Payment Received'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root { --acct-gold:#d4af37; --acct-gold-dark:#b8941f; --acct-gold-light:#f5ecd0; --acct-dark:#1a1a1a; --acct-header-bg:#2d2d2d; }
.txn-layout { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
@media (max-width:900px) { .txn-layout { grid-template-columns:1fr; } }
.txn-card { background:#fff; border:1px solid #dee2e6; border-top:3px solid #17a2b8; border-radius:0 0 6px 6px; overflow:hidden; }
.txn-card-header { background:var(--acct-header-bg); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.txn-card-header .txn-icon { width:34px; height:34px; background:rgba(23,162,184,.15); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#17a2b8; font-size:1.1rem; }
.txn-card-header .txn-title { font-size:.95rem; font-weight:700; color:#fff; margin:0; }
.txn-card-header .txn-sub { font-size:.72rem; color:#888; margin:0; }
.txn-card-body { padding:22px 24px; }
.txn-field-label { font-size:.72rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:#6c757d; margin-bottom:5px; display:block; }
.txn-card-body .form-control, .txn-card-body .form-select { font-size:.875rem; border:1px solid #dee2e6; border-radius:4px; }
.txn-card-body .form-control:focus, .txn-card-body .form-select:focus { border-color:var(--acct-gold); box-shadow:0 0 0 3px rgba(212,175,55,.18); }
.txn-card-body .input-group-text { font-size:.85rem; font-weight:700; color:var(--acct-gold-dark); background:var(--acct-gold-light); border-color:#dee2e6; }
.btn-txn-post { background:#17a2b8; border:none; color:#fff; font-weight:700; font-size:.85rem; padding:9px 24px; border-radius:4px; letter-spacing:.02em; display:inline-flex; align-items:center; gap:6px; transition:background .15s; }
.btn-txn-post:hover { background:#138496; color:#fff; }
.entry-preview-panel { background:#fff; border:1px solid #dee2e6; border-radius:6px; overflow:hidden; position:sticky; top:80px; }
.preview-header { background:var(--acct-header-bg); padding:10px 14px; display:flex; align-items:center; gap:8px; }
.preview-header span { font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#aaa; }
.preview-body { padding:14px 16px; }
.preview-body .preview-title { font-size:.72rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:#888; margin-bottom:10px; }
.je-preview-table { width:100%; font-size:.82rem; }
.je-preview-table th { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#aaa; padding:3px 6px; border-bottom:1px solid #f1f3f5; }
.je-preview-table td { padding:5px 6px; vertical-align:middle; border-bottom:1px solid #f9f9f9; }
.je-preview-table td .acct-name { font-size:.82rem; font-weight:600; color:#2d2d2d; }
.je-preview-table td .acct-code { font-size:.7rem; color:#aaa; font-family:'Courier New',monospace; }
.je-preview-table td.preview-dr { font-family:'Courier New',monospace; font-size:.88rem; color:#2e7d32; font-weight:700; text-align:right; }
.je-preview-table td.preview-cr { font-family:'Courier New',monospace; font-size:.88rem; color:#c62828; font-weight:700; text-align:right; }
.je-preview-table td.empty { color:#ddd; text-align:right; font-size:.8rem; }
.preview-body .preview-note { font-size:.73rem; color:#888; margin:10px 0 0; line-height:1.5; }
.preview-body .preview-note strong { color:var(--acct-gold-dark); }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="container-fluid">

    <div class="d-flex align-items-center gap-3 mb-3" style="font-size:.82rem;color:#888;">
        <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <i class="bx bx-book-open me-1"></i>Journal
        </a>
        <i class="bx bx-chevron-right"></i>
        <span style="color:#495057;font-weight:600;">Payment Received</span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3"
             style="border-left:4px solid #dc3545;border-radius:4px;font-size:.875rem;">
            <i class="bx bx-error-circle me-1"></i>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($err); ?><br> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="txn-layout">

        <div>
            <div class="txn-card">
                <div class="txn-card-header">
                    <div class="txn-icon"><i class="bx bx-money"></i></div>
                    <div>
                        <div class="txn-title">Payment Received</div>
                        <div class="txn-sub">Dr Cash / Bank · Cr Accounts Receivable</div>
                    </div>
                </div>
                <div class="txn-card-body">
                    <form method="POST" action="<?php echo e(route('admin.accounting.record-payment.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="txn-field-label">Partner / Client <span class="text-danger">*</span></label>
                            <select name="partner_id" id="partnerSelect"
                                    class="form-select <?php $__errorArgs = ['partner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">— Select Partner —</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($partner->id); ?>"
                                            data-name="<?php echo e($partner->name); ?>"
                                            <?php echo e(old('partner_id') == $partner->id ? 'selected' : ''); ?>>
                                        <?php echo e($partner->name); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->code): ?>  ·  <?php echo e($partner->code); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['partner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="txn-field-label">Insurance Carrier</label>
                            <select name="insurance_carrier_id" id="carrierSelect"
                                    class="form-select <?php $__errorArgs = ['insurance_carrier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    disabled>
                                <option value="">— Select a partner first —</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['insurance_carrier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="txn-field-label">Amount Received <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" id="amountInput"
                                       step="0.01" min="0.01"
                                       class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('amount')); ?>" placeholder="0.00" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="txn-field-label">Entry Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date"
                                       class="form-control <?php $__errorArgs = ['entry_date'];
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
                            <div class="col-sm-6">
                                <label class="txn-field-label">Bank Ref / Cheque #</label>
                                <input type="text" name="reference" class="form-control"
                                       value="<?php echo e(old('reference')); ?>" placeholder="Optional">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="txn-field-label">Description / Narration <span class="text-danger">*</span></label>
                            <textarea name="description" rows="2"
                                      class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      placeholder="e.g. Payment received from partner against HP-2024-001"
                                      required><?php echo e(old('description')); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-txn-post">
                                <i class="bx bx-save"></i> Post Payment Entry
                            </button>
                            <a href="<?php echo e(route('admin.accounting.journal.index')); ?>"
                               class="btn btn-sm btn-outline-secondary" style="font-size:.82rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="entry-preview-panel">
                <div class="preview-header">
                    <i class="bx bx-spreadsheet" style="color:var(--acct-gold);font-size:1rem;"></i>
                    <span>Journal Entry Preview</span>
                </div>
                <div class="preview-body">
                    <div class="preview-title">What will be posted</div>
                    <table class="je-preview-table">
                        <thead>
                            <tr><th>Account</th><th>Dr</th><th>Cr</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="acct-name">Cash / Bank A/C</div>
                                    <div class="acct-code">1100 · Current Asset</div>
                                </td>
                                <td class="preview-dr" id="previewDr">—</td>
                                <td class="empty">—</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="acct-name">A/R — <span id="previewPartnerName">Partner</span></div>
                                    <div class="acct-code">1200 · Accounts Receivable</div>
                                </td>
                                <td class="empty">—</td>
                                <td class="preview-cr" id="previewCr">—</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="preview-note">
                        <strong>Dr</strong> Bank (cash in) ·
                        <strong>Cr</strong> clears partner A/R (reduces what partner owes)
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
var oldCarrierId = '<?php echo e(old('insurance_carrier_id')); ?>';

function loadCarriers(partnerId, preselectId) {
    var sel = document.getElementById('carrierSelect');
    if (!partnerId) {
        sel.innerHTML = '<option value="">— Select a partner first —</option>';
        sel.disabled = true;
        return;
    }
    sel.innerHTML = '<option value="">Loading…</option>';
    sel.disabled = true;
    fetch('/admin/accounting/partner/' + partnerId + '/carriers')
        .then(function(r){ return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value="">— No Specific Carrier —</option>';
            data.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                if (preselectId && String(c.id) === String(preselectId)) {
                    opt.selected = true;
                }
                sel.appendChild(opt);
            });
            sel.disabled = false;
        });
}

function fmt(n) {
    var v = parseFloat(n);
    if (isNaN(v) || v <= 0) return '—';
    return v.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
}
function updatePreview() {
    var amt  = document.getElementById('amountInput').value;
    var sel  = document.getElementById('partnerSelect');
    var opt  = sel.options[sel.selectedIndex];
    var name = opt && opt.dataset.name ? opt.dataset.name : 'Partner';
    document.getElementById('previewPartnerName').textContent = name;
    document.getElementById('previewDr').textContent = fmt(amt);
    document.getElementById('previewCr').textContent = fmt(amt);
}
document.getElementById('amountInput').addEventListener('input', updatePreview);
document.getElementById('partnerSelect').addEventListener('change', function() {
    loadCarriers(this.value, null);
    updatePreview();
});

var initialPartner = document.getElementById('partnerSelect').value;
if (initialPartner) {
    loadCarriers(initialPartner, oldCarrierId);
}
updatePreview();
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/quick/payment.blade.php ENDPATH**/ ?>