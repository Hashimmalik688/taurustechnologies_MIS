
<div id="carrier-states-container">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="cs-carrier d-none" id="carrier-state-section-<?php echo e($carrier->id); ?>">
        
        <div class="cs-carrier-hdr">
            <div class="cs-carrier-name">
                <i class="bx bx-buildings"></i> <?php echo e($carrier->name); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->payment_module): ?>
                    <span class="cs-pay-tag"><?php echo e(ucfirst(str_replace('_', ' ', $carrier->payment_module))); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button type="button" class="cs-remove-btn" onclick="removeCarrierSection(<?php echo e($carrier->id); ?>)" title="Remove carrier">
                <i class="bx bx-x"></i> Remove
            </button>
        </div>

        
        <div class="cs-field-group">
            <label class="cs-label">Licensed States for <?php echo e($carrier->name); ?></label>
            <select class="form-select select2-multiple"
                    name="carrier_states[<?php echo e($carrier->id); ?>][]"
                    id="carrier_states_<?php echo e($carrier->id); ?>"
                    multiple="multiple"
                    data-placeholder="Select states..."
                    onchange="updateStateSettlementFields(<?php echo e($carrier->id); ?>)">
                <?php
                    $us_states = ['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];
                    $existingStates = [];
                    if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                        $existingStates = $partnerCarrierStates[$carrier->id]->pluck('state')->toArray();
                    }
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $us_states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($state); ?>" <?php echo e(in_array($state, $existingStates) ? 'selected' : ''); ?>><?php echo e($state); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <div class="cs-hint">Select all states where this partner is licensed for <?php echo e($carrier->name); ?></div>
        </div>

        
        <div id="state-settlement-fields-<?php echo e($carrier->id); ?>" class="mt-2">
            <div class="cs-rates-card">
                <div class="cs-rates-hdr">
                    <i class="bx bx-line-chart"></i>
                    <span>Commission Rates for <?php echo e($carrier->name); ?></span>
                    <small>Applies to all states</small>
                </div>
                <div class="cs-rates-body">
                    <?php
                        $existingCarrierData = null;
                        if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                            $existingCarrierData = $partnerCarrierStates[$carrier->id]->first();
                        }
                    ?>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="cs-label">Level %</label>
                            <input type="number" name="settlement_level[<?php echo e($carrier->id); ?>]" class="cs-input" step="0.01" min="0" max="200" value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_level_pct : ''); ?>" placeholder="95.00">
                            <div class="cs-hint">Standard settlement</div>
                        </div>
                        <div class="col-md-3">
                            <label class="cs-label">Graded %</label>
                            <input type="number" name="settlement_graded[<?php echo e($carrier->id); ?>]" class="cs-input" step="0.01" min="0" max="200" value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_graded_pct : ''); ?>" placeholder="75.00">
                            <div class="cs-hint">Graded/Modified</div>
                        </div>
                        <div class="col-md-3">
                            <label class="cs-label">GI %</label>
                            <input type="number" name="settlement_gi[<?php echo e($carrier->id); ?>]" class="cs-input" step="0.01" min="0" max="200" value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_gi_pct : ''); ?>" placeholder="60.00">
                            <div class="cs-hint">Guaranteed Issue</div>
                        </div>
                        <div class="col-md-3">
                            <label class="cs-label">Modified %</label>
                            <input type="number" name="settlement_modified[<?php echo e($carrier->id); ?>]" class="cs-input" step="0.01" min="0" max="200" value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_modified_pct : ''); ?>" placeholder="85.00">
                            <div class="cs-hint">Table shave rate</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div id="selected-states-<?php echo e($carrier->id); ?>" class="mt-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])): ?>
                    <div class="cs-state-tags">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partnerCarrierStates[$carrier->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stateRecord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="cs-state-pill"><?php echo e($stateRecord->state); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->plan_types && is_array($carrier->plan_types) && count($carrier->plan_types) > 0): ?>
        <div class="cs-plans-row">
            <i class="bx bx-list-check"></i>
            <span>Plans:</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carrier->plan_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="cs-plan-pill"><?php echo e($plan); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<style>
/* ═══ Carrier-States Partial — Executive Theme ═══ */
.cs-carrier {
    border:1px solid rgba(0,0,0,.06); border-radius:.5rem; padding:.75rem;
    margin-bottom:.5rem; background:rgba(0,0,0,.01); transition:all .2s;
}
.cs-carrier:hover { border-color:rgba(85,110,230,.18); box-shadow:0 2px 8px rgba(85,110,230,.06); }

.cs-carrier-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.55rem; }
.cs-carrier-name { font-weight:700; font-size:.78rem; color:#556ee6; display:flex; align-items:center; gap:.35rem; }
.cs-carrier-name i { font-size:.9rem; }
.cs-pay-tag {
    font-size:.52rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px;
    padding:.1rem .35rem; border-radius:.2rem;
    background:rgba(80,165,241,.1); color:#50a5f1; border:1px solid rgba(80,165,241,.12);
}

.cs-remove-btn {
    font-size:.6rem; font-weight:600; color:#f46a6a; cursor:pointer;
    display:inline-flex; align-items:center; gap:.2rem;
    border:1px solid rgba(244,106,106,.12); padding:.15rem .4rem; border-radius:.25rem;
    background:rgba(244,106,106,.04); transition:all .15s;
}
.cs-remove-btn:hover { background:rgba(244,106,106,.1); border-color:#f46a6a; }

.cs-field-group { margin-bottom:.5rem; }
.cs-label { font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--bs-surface-500); margin-bottom:.2rem; display:block; }
.cs-hint { font-size:.52rem; color:var(--bs-surface-400); margin-top:.1rem; }

.cs-input {
    font-size:.7rem; border:1px solid rgba(0,0,0,.08); border-radius:.3rem;
    padding:.35rem .5rem; width:100%; background:var(--bs-card-bg); transition:all .2s;
}
.cs-input:focus { outline:none; border-color:#556ee6; box-shadow:0 0 0 2px rgba(85,110,230,.1); }

/* Rates Card */
.cs-rates-card { border:1px solid rgba(85,110,230,.1); border-radius:.45rem; overflow:hidden; }
.cs-rates-hdr {
    display:flex; align-items:center; gap:.35rem; padding:.4rem .65rem;
    background:linear-gradient(135deg,rgba(85,110,230,.08),rgba(118,75,162,.06));
    font-size:.68rem; font-weight:700; color:#556ee6;
}
.cs-rates-hdr i { font-size:.85rem; }
.cs-rates-hdr small { margin-left:auto; font-size:.52rem; font-weight:600; color:var(--bs-surface-400); }
.cs-rates-body { padding:.6rem .65rem; }

/* State pills — white-theme-safe */
.cs-state-tags { display:flex; flex-wrap:wrap; gap:.2rem; }
.cs-state-pill {
    font-size:.52rem; font-weight:700; padding:.1rem .3rem; border-radius:.2rem;
    background:rgba(85,110,230,.1); color:#556ee6; border:1px solid rgba(85,110,230,.12);
}

/* Plan pills — white-theme-safe */
.cs-plans-row {
    display:flex; align-items:center; gap:.3rem; flex-wrap:wrap;
    margin-top:.45rem; font-size:.58rem; color:var(--bs-surface-400);
}
.cs-plans-row i { font-size:.75rem; color:var(--bs-surface-300); }
.cs-plan-pill {
    font-size:.5rem; font-weight:700; padding:.08rem .3rem; border-radius:.2rem;
    background:rgba(52,195,143,.08); color:#1a8754; border:1px solid rgba(52,195,143,.12);
}
</style>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/partners/partials/carrier-states.blade.php ENDPATH**/ ?>