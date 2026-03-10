<?php $__env->startSection('title', 'Record a ChargeBack / Sales Return'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --acct-gold:       #d4af37;
    --acct-gold-dark:  #b8941f;
    --acct-gold-light: #f5ecd0;
    --acct-dark:       #1a1a1a;
    --acct-header-bg:  #2d2d2d;
    --cb-red:          #b71c1c;
    --cb-red-light:    #fce4ec;
}
.txn-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }
@media (max-width: 900px) { .txn-layout { grid-template-columns: 1fr; } }

.txn-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: 3px solid var(--cb-red);
    border-radius: 0 0 6px 6px;
    overflow: hidden;
}
.txn-card-header {
    background: var(--acct-header-bg);
    padding: 12px 20px;
    display: flex; align-items: center; gap: 10px;
}
.txn-card-header .txn-icon {
    width: 34px; height: 34px;
    background: rgba(183,28,28,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #ef9a9a; font-size: 1.1rem;
}
.txn-card-header .txn-title { font-size: .95rem; font-weight: 700; color: #fff; margin: 0; }
.txn-card-header .txn-sub   { font-size: .72rem; color: #888; margin: 0; }
.txn-card-body { padding: 22px 24px; }

.txn-field-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 5px;
    display: block;
}
.txn-card-body .form-control,
.txn-card-body .form-select {
    font-size: .875rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}
.txn-card-body .form-control:focus,
.txn-card-body .form-select:focus {
    border-color: var(--cb-red);
    box-shadow: 0 0 0 3px rgba(183,28,28,.15);
}
.txn-card-body .input-group-text {
    font-size: .85rem;
    font-weight: 700;
    color: var(--cb-red);
    background: var(--cb-red-light);
    border-color: #dee2e6;
}
.btn-txn-post {
    background: var(--cb-red);
    border: none;
    color: #fff;
    font-weight: 700;
    font-size: .85rem;
    padding: 9px 24px;
    border-radius: 4px;
    letter-spacing: .02em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background .15s;
}
.btn-txn-post:hover { background: #7f0000; }

.entry-preview-panel {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
    position: sticky;
    top: 80px;
}
.preview-header {
    background: var(--acct-header-bg);
    padding: 10px 14px;
    display: flex; align-items: center; gap: 8px;
}
.preview-header span { font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #aaa; }
.preview-body { padding: 14px 16px; }
.preview-body .preview-title { font-size: .72rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: #888; margin-bottom: 10px; }
.je-preview-table { width: 100%; font-size: .82rem; }
.je-preview-table th { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #aaa; padding: 3px 6px; border-bottom: 1px solid #f1f3f5; }
.je-preview-table td { padding: 5px 6px; vertical-align: middle; border-bottom: 1px solid #f9f9f9; }
.je-preview-table td .acct-name  { font-size: .82rem; font-weight: 600; color: #2d2d2d; }
.je-preview-table td .acct-code  { font-size: .7rem; color: #aaa; font-family: 'Courier New', monospace; }
.je-preview-table td.preview-dr  { font-family: 'Courier New', monospace; font-size: .88rem; color: #c62828; font-weight: 700; text-align: right; }
.je-preview-table td.preview-cr  { font-family: 'Courier New', monospace; font-size: .88rem; color: #1565c0; font-weight: 700; text-align: right; }
.je-preview-table td.empty       { color: #ddd; text-align: right; font-size: .8rem; }
.preview-body .preview-note { font-size: .73rem; color: #888; margin: 10px 0 0; line-height: 1.5; }
.preview-body .preview-note strong { color: var(--cb-red); }

.cb-alert-banner {
    background: #fff3e0;
    border: 1px solid #ffcc80;
    border-left: 4px solid #e65100;
    border-radius: 4px;
    padding: 8px 14px;
    font-size: .8rem;
    color: #7a3a00;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-flex align-items-center gap-3 mb-3" style="font-size:.82rem;color:#888;">
        <a href="<?php echo e(route('admin.accounting.journal.index')); ?>" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <i class="bx bx-book-open me-1"></i>Journal
        </a>
        <i class="bx bx-chevron-right"></i>
        <span style="color:#b71c1c;font-weight:600;">Record ChargeBack / Sales Return</span>
    </div>

    <div class="cb-alert-banner">
        <i class="bx bx-error-circle" style="font-size:1.1rem;flex-shrink:0;"></i>
        <span>
            A chargeback <strong>debits Sales Returns</strong> (reducing income) and <strong>credits Accounts Payable — Carriers</strong> (we now owe the carrier).
            This is the reversal of a previously recorded sale commission.
        </span>
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
                    <div class="txn-icon"><i class="bx bx-undo"></i></div>
                    <div>
                        <div class="txn-title">Record ChargeBack / Sales Return</div>
                        <div class="txn-sub">Dr Sales Returns · Cr A/P — Carriers</div>
                    </div>
                </div>
                <div class="txn-card-body">
                    <form method="POST" action="<?php echo e(route('admin.accounting.record-chargeback.store')); ?>" id="cbForm">
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
                            <label class="txn-field-label">Insurance Carrier <span class="text-danger">*</span></label>
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
                            <label class="txn-field-label">Insured Name</label>
                            <input type="text" name="insured_name"
                                   class="form-control <?php $__errorArgs = ['insured_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('insured_name')); ?>" placeholder="Name of the insured / policy holder">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['insured_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="mb-3">
                            <label class="txn-field-label">Gross ChargeBack Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="gross_amount" id="grossAmountInput"
                                       step="0.01" min="0.01"
                                       class="form-control <?php $__errorArgs = ['gross_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('gross_amount')); ?>" placeholder="Full chargeback / policy value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gross_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="row g-2 mb-3">
                            <div class="col-sm-5">
                                <label class="txn-field-label">Our Share %</label>
                                <div class="input-group">
                                    <input type="number" name="our_share_percentage" id="sharePercentageInput"
                                           step="0.01" min="0" max="100"
                                           class="form-control <?php $__errorArgs = ['our_share_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('our_share_percentage')); ?>" placeholder="e.g. 30">
                                    <span class="input-group-text">%</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['our_share_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <label class="txn-field-label">ChargeBack Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="shareAmountDisplay"
                                           step="0.01" min="0.01"
                                           class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('amount')); ?>" placeholder="Auto-calculated">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="mt-1" style="font-size:.7rem;color:#888;">Amount posted to ledger. Edit directly if no % set.</div>
                            </div>
                        </div>

                        
                        <input type="hidden" name="amount" id="amountHidden" value="<?php echo e(old('amount')); ?>">

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
                                <label class="txn-field-label">Policy / Reference #</label>
                                <input type="text" name="reference" class="form-control"
                                       value="<?php echo e(old('reference')); ?>" placeholder="Original policy or ref #">
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
                                      placeholder="e.g. ChargeBack on policy HP-2024-001 — carrier clawback"
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
                                <i class="bx bx-save"></i> Post ChargeBack Entry
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
                    <i class="bx bx-spreadsheet" style="color:#ef9a9a;font-size:1rem;"></i>
                    <span>Journal Entry Preview</span>
                </div>
                <div class="preview-body">
                    <div class="preview-title">What will be posted</div>
                    <table class="je-preview-table">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Dr</th>
                                <th>Cr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="acct-name">Sales Returns / Chargebacks</div>
                                    <div class="acct-code">4200 · Contra Revenue</div>
                                </td>
                                <td class="preview-dr" id="previewDr">—</td>
                                <td class="empty">—</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="acct-name">A/P — Carriers</div>
                                    <div class="acct-code">2100 · Accounts Payable</div>
                                </td>
                                <td class="empty">—</td>
                                <td class="preview-cr" id="previewCr">—</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="preview-note">
                        <strong>Dr</strong> Sales Returns reduces our revenue ·
                        <strong>Cr</strong> A/P Carriers records the obligation owed back to the carrier
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
    return '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function recalcShare() {
    var gross = parseFloat(document.getElementById('grossAmountInput').value);
    var pct   = parseFloat(document.getElementById('sharePercentageInput').value);
    var shareDisplay = document.getElementById('shareAmountDisplay');
    var amountHidden = document.getElementById('amountHidden');

    if (!isNaN(gross) && gross > 0 && !isNaN(pct) && pct >= 0) {
        var share = Math.round(gross * pct / 100 * 100) / 100;
        shareDisplay.value = share > 0 ? share.toFixed(2) : '';
        amountHidden.value  = share > 0 ? share.toFixed(2) : '';
    } else {
        amountHidden.value = shareDisplay.value;
    }
    updatePreview();
}

function updatePreview() {
    var amt = document.getElementById('amountHidden').value
               || document.getElementById('shareAmountDisplay').value;
    document.getElementById('previewDr').textContent = fmt(amt);
    document.getElementById('previewCr').textContent = fmt(amt);
}

document.getElementById('grossAmountInput').addEventListener('input', recalcShare);
document.getElementById('sharePercentageInput').addEventListener('input', recalcShare);
document.getElementById('shareAmountDisplay').addEventListener('input', function() {
    document.getElementById('amountHidden').value = this.value;
    updatePreview();
});
document.getElementById('partnerSelect').addEventListener('change', function() {
    loadCarriers(this.value, null);
});

document.getElementById('cbForm').addEventListener('submit', function() {
    var shareDisplay = document.getElementById('shareAmountDisplay');
    var amountHidden = document.getElementById('amountHidden');
    if (!amountHidden.value && shareDisplay.value) {
        amountHidden.value = shareDisplay.value;
    }
});

var initialPartner = document.getElementById('partnerSelect').value;
if (initialPartner) {
    loadCarriers(initialPartner, oldCarrierId);
}
updatePreview();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/quick/chargeback.blade.php ENDPATH**/ ?>