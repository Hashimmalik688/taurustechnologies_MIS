
<div id="carrier-states-container">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleAllCarriers()">
            <i class="mdi mdi-eye me-1"></i>Show/Hide All Carriers
        </button>
        <button type="button" class="btn btn-success btn-sm" onclick="openCreateCarrierModal()">
            <i class="mdi mdi-plus me-1"></i>Add New Carrier
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="carrier-state-section d-none" id="carrier-state-section-<?php echo e($carrier->id); ?>">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0">
                    <i class="mdi mdi-briefcase me-1"></i>
                    <?php echo e($carrier->name); ?>

                </h6>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->payment_module): ?>
                    <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $carrier->payment_module))); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCarrierSection(<?php echo e($carrier->id); ?>)" title="Remove this carrier">
                <i class="mdi mdi-close"></i> Remove
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label">Licensed States for <?php echo e($carrier->name); ?></label>
            <select class="form-select select2-multiple" 
                    name="carrier_states[<?php echo e($carrier->id); ?>][]" 
                    id="carrier_states_<?php echo e($carrier->id); ?>" 
                    multiple="multiple"
                    data-placeholder="Select states where partner can work..."
                    onchange="updateStateSettlementFields(<?php echo e($carrier->id); ?>)">
                <?php
                    $us_states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
                    
                    // Get existing states for edit mode
                    $existingStates = [];
                    if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                        $existingStates = $partnerCarrierStates[$carrier->id]->pluck('state')->toArray();
                    }
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $us_states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($state); ?>" <?php echo e(in_array($state, $existingStates) ? 'selected' : ''); ?>>
                        <?php echo e($state); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <small class="text-muted">Select all states where this partner is licensed for <?php echo e($carrier->name); ?></small>
        </div>

        <div id="state-settlement-fields-<?php echo e($carrier->id); ?>" class="mt-3">
            
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="mdi mdi-percent me-1"></i>
                        Commission Rates for <?php echo e($carrier->name); ?>

                    </h6>
                    <small>These rates apply to ALL states for this carrier</small>
                </div>
                <div class="card-body">
                    <?php
                        $existingCarrierData = null;
                        if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                            $existingCarrierData = $partnerCarrierStates[$carrier->id]->first();
                        }
                    ?>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Level %</label>
                            <input type="number" 
                                   name="settlement_level[<?php echo e($carrier->id); ?>]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_level_pct : ''); ?>"
                                   placeholder="95.00">
                            <small class="text-muted">Standard settlement rate</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Graded %</label>
                            <input type="number" 
                                   name="settlement_graded[<?php echo e($carrier->id); ?>]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_graded_pct : ''); ?>"
                                   placeholder="75.00">
                            <small class="text-muted">Graded/Modified settlement</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">GI %</label>
                            <input type="number" 
                                   name="settlement_gi[<?php echo e($carrier->id); ?>]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_gi_pct : ''); ?>"
                                   placeholder="60.00">
                            <small class="text-muted">Guaranteed Issue rate</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Modified %</label>
                            <input type="number" 
                                   name="settlement_modified[<?php echo e($carrier->id); ?>]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="<?php echo e($existingCarrierData ? $existingCarrierData->settlement_modified_pct : ''); ?>"
                                   placeholder="85.00">
                            <small class="text-muted">Modified/Table shave rate</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="mdi mdi-information me-1"></i>
                        <strong>Note:</strong> These commission rates will apply to ALL selected states for <?php echo e($carrier->name); ?>.
                    </div>
                </div>
            </div>
            
            
            <div id="selected-states-<?php echo e($carrier->id); ?>" class="mt-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])): ?>
                    <div class="alert alert-light">
                        <strong>Licensed States:</strong>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partnerCarrierStates[$carrier->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stateRecord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-primary me-1"><?php echo e($stateRecord->state); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->plan_types && is_array($carrier->plan_types) && count($carrier->plan_types) > 0): ?>
        <div class="mt-3">
            <small class="text-muted">
                <i class="mdi mdi-information-outline me-1"></i>
                <strong>Available Plan Types:</strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carrier->plan_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-secondary-subtle text-secondary"><?php echo e($plan); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </small>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<style>
    .carrier-state-section {
        border: 2px solid #e9ecef;
        padding: 20px;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .carrier-state-section:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
</style>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/partners/partials/carrier-states.blade.php ENDPATH**/ ?>