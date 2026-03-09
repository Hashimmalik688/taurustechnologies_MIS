
<div id="carrier-states-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="alert alert-info mb-0 flex-grow-1 me-3">
            <i class="mdi mdi-information me-2"></i>
            For each carrier, specify which states this agent can work in and their settlement percentages (Level %, Graded %, GI %, Modified %).
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNewCarrierModal">
            <i class="mdi mdi-plus me-1"></i>Add New Carrier
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <div class="carrier-state-section mb-4 d-none bg-surface-bg-light border-surface-200" id="carrier-state-section-<?php echo e($carrier->id); ?>" style="padding: 15px; border-radius: 5px">
        <h6 class="mb-3">
            <i class="mdi mdi-map-marker-multiple me-1"></i>
            <?php echo e($carrier->name); ?> - State-Specific Settlements
        </h6>

        <div class="mb-3">
            <label class="form-label">Licensed States for <?php echo e($carrier->name); ?></label>
            <select class="form-select select2-multiple" 
                    name="carrier_states[<?php echo e($carrier->id); ?>][]" 
                    id="carrier_states_<?php echo e($carrier->id); ?>" 
                    multiple="multiple"
                    data-placeholder="Select states where agent can work..."
                    onchange="updateStateSettlementFields(<?php echo e($carrier->id); ?>)">
                <?php
                    $us_states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
                    
                    // Get existing states for edit mode
                    $existingStates = [];
                    if(isset($agentCarrierStates) && isset($agentCarrierStates[$carrier->id])) {
                        $existingStates = $agentCarrierStates[$carrier->id]->pluck('state')->toArray();
                    }
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $us_states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($state); ?>" <?php echo e(in_array($state, $existingStates) ? 'selected' : ''); ?>>
                        <?php echo e($state); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>

        <div id="state-settlement-fields-<?php echo e($carrier->id); ?>" class="mt-3">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($agentCarrierStates) && isset($agentCarrierStates[$carrier->id])): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $agentCarrierStates[$carrier->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stateRecord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="state-settlement-row mb-3" data-state="<?php echo e($stateRecord->state); ?>" data-carrier="<?php echo e($carrier->id); ?>">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <strong><?php echo e($stateRecord->state); ?></strong>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">Level %</label>
                                <input type="number" 
                                       name="settlement_level[<?php echo e($carrier->id); ?>][<?php echo e($stateRecord->state); ?>]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="<?php echo e($stateRecord->settlement_level_pct); ?>"
                                       placeholder="115.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">Graded %</label>
                                <input type="number" 
                                       name="settlement_graded[<?php echo e($carrier->id); ?>][<?php echo e($stateRecord->state); ?>]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="<?php echo e($stateRecord->settlement_graded_pct); ?>"
                                       placeholder="90.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">GI %</label>
                                <input type="number" 
                                       name="settlement_gi[<?php echo e($carrier->id); ?>][<?php echo e($stateRecord->state); ?>]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="<?php echo e($stateRecord->settlement_gi_pct); ?>"
                                       placeholder="70.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">Modified %</label>
                                <input type="number" 
                                       name="settlement_modified[<?php echo e($carrier->id); ?>][<?php echo e($stateRecord->state); ?>]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="<?php echo e($stateRecord->settlement_modified_pct); ?>"
                                       placeholder="95.00">
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
// Toggle carrier state section visibility when carrier is selected/deselected
function toggleCommissionInput(checkbox, carrierId) {
    const commissionInput = document.getElementById('commission_' + carrierId);
    const stateSection = document.getElementById('carrier-state-section-' + carrierId);
    
    if (checkbox.checked) {
        commissionInput.disabled = false;
        stateSection.classList.remove('d-none');
    } else {
        commissionInput.disabled = true;
        commissionInput.value = '';
        stateSection.classList.add('d-none');
        // Clear state selections
        const stateSelect = document.getElementById('carrier_states_' + carrierId);
        if (stateSelect) {
            $(stateSelect).val(null).trigger('change');
        }
    }
}

// Update settlement percentage input fields when states are selected
function updateStateSettlementFields(carrierId) {
    const stateSelect = document.getElementById('carrier_states_' + carrierId);
    const fieldsContainer = document.getElementById('state-settlement-fields-' + carrierId);
    
    if (!stateSelect || !fieldsContainer) return;
    
    const selectedStates = Array.from(stateSelect.selectedOptions).map(option => option.value);
    
    // Clear existing fields
    fieldsContainer.innerHTML = '';
    
    // Create input fields for each selected state
    selectedStates.forEach(state => {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'state-settlement-row mb-3';
        rowDiv.setAttribute('data-state', state);
        rowDiv.setAttribute('data-carrier', carrierId);
        
        rowDiv.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-2">
                    <strong>${state}</strong>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">Level %</label>
                    <input type="number" 
                           name="settlement_level[${carrierId}][${state}]" 
                           class="form-control form-control-sm" 
                           step="0.01" 
                           min="0" 
                           max="200"
                           placeholder="115.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">Graded %</label>
                    <input type="number" 
                           name="settlement_graded[${carrierId}][${state}]" 
                           class="form-control form-control-sm" 
                           step="0.01" 
                           min="0" 
                           max="200"
                           placeholder="90.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">GI %</label>
                    <input type="number" 
                           name="settlement_gi[${carrierId}][${state}]" 
                           class="form-control form-control-sm" 
                           step="0.01" 
                           min="0" 
                           max="200"
                           placeholder="70.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">Modified %</label>
                    <input type="number" 
                           name="settlement_modified[${carrierId}][${state}]" 
                           class="form-control form-control-sm" 
                           step="0.01" 
                           min="0" 
                           max="200"
                           placeholder="95.00">
                </div>
            </div>
        `;
        
        fieldsContainer.appendChild(rowDiv);
    });
}

// Initialize Select2 for multi-select dropdowns
$(document).ready(function() {
    $('.select2-multiple').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>


<div class="modal fade" id="addNewCarrierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-plus-circle me-2"></i>Add New Insurance Carrier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addNewCarrierForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="new_carrier_name" class="form-label fw-semibold">Carrier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_carrier_name" name="name" required placeholder="e.g., AIG, Securico, TransAmerica">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_carrier_payment_module" class="form-label fw-semibold">Payment Module</label>
                            <select class="form-select" id="new_carrier_payment_module" name="payment_module">
                                <option value="on_draft">On Draft</option>
                                <option value="on_issue">On Issue</option>
                                <option value="as_earned">As Earned</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="new_carrier_base_commission" class="form-label fw-semibold">Base Commission %</label>
                            <input type="number" class="form-control" id="new_carrier_base_commission" name="base_commission_percentage" step="0.01" min="0" max="200" placeholder="100.00">
                            <small class="text-muted">Fallback rate if agent-specific not set</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_carrier_phone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="new_carrier_phone" name="phone" placeholder="(555) 123-4567">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_carrier_ssn" class="form-label fw-semibold">SSN Last 4</label>
                            <input type="text" class="form-control" id="new_carrier_ssn" name="ssn_last4" maxlength="4" placeholder="1234">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_carrier_plan_types" class="form-label fw-semibold">Plan Types</label>
                        <input type="text" class="form-control" id="new_carrier_plan_types" name="plan_types" placeholder="Term, Whole Life, Universal, Final Expense">
                        <small class="text-muted">Comma-separated list of insurance plan types</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="new_carrier_is_active" name="is_active" checked>
                        <label class="form-check-label" for="new_carrier_is_active">
                            Active (Available for assignment)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-content-save me-1"></i>Save Carrier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle adding new carrier via AJAX
$('#addNewCarrierForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Saving...');
    
    $.ajax({
        url: '<?php echo e(route("admin.insurance-carriers.store")); ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.success) {
                // Show success message
                alert('Carrier added successfully! Refreshing page...');
                // Reload page to show new carrier
                location.reload();
            }
        },
        error: function(xhr) {
            alert('Error adding carrier: ' + (xhr.responseJSON?.message || 'Unknown error'));
            submitBtn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Save Carrier');
        }
    });
});
</script>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/agents/partials/carrier-states.blade.php ENDPATH**/ ?>