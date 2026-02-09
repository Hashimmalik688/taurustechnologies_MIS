{{-- Agent-Carrier-State Settlement Management Component --}}
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

    @foreach($insuranceCarriers as $carrier)
    <div class="carrier-state-section mb-4 d-none" id="carrier-state-section-{{ $carrier->id }}" style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background-color: #f8f9fa;">
        <h6 class="mb-3">
            <i class="mdi mdi-map-marker-multiple me-1"></i>
            {{ $carrier->name }} - State-Specific Settlements
        </h6>

        <div class="mb-3">
            <label class="form-label">Licensed States for {{ $carrier->name }}</label>
            <select class="form-select select2-multiple" 
                    name="carrier_states[{{ $carrier->id }}][]" 
                    id="carrier_states_{{ $carrier->id }}" 
                    multiple="multiple"
                    data-placeholder="Select states where agent can work..."
                    onchange="updateStateSettlementFields({{ $carrier->id }})">
                @php
                    $us_states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
                    
                    // Get existing states for edit mode
                    $existingStates = [];
                    if(isset($agentCarrierStates) && isset($agentCarrierStates[$carrier->id])) {
                        $existingStates = $agentCarrierStates[$carrier->id]->pluck('state')->toArray();
                    }
                @endphp
                @foreach($us_states as $state)
                    <option value="{{ $state }}" {{ in_array($state, $existingStates) ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="state-settlement-fields-{{ $carrier->id }}" class="mt-3">
            {{-- Settlement percentage fields will be dynamically added here --}}
            @if(isset($agentCarrierStates) && isset($agentCarrierStates[$carrier->id]))
                @foreach($agentCarrierStates[$carrier->id] as $stateRecord)
                    <div class="state-settlement-row mb-3" data-state="{{ $stateRecord->state }}" data-carrier="{{ $carrier->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <strong>{{ $stateRecord->state }}</strong>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">Level %</label>
                                <input type="number" 
                                       name="settlement_level[{{ $carrier->id }}][{{ $stateRecord->state }}]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="{{ $stateRecord->settlement_level_pct }}"
                                       placeholder="115.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">Graded %</label>
                                <input type="number" 
                                       name="settlement_graded[{{ $carrier->id }}][{{ $stateRecord->state }}]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="{{ $stateRecord->settlement_graded_pct }}"
                                       placeholder="90.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">GI %</label>
                                <input type="number" 
                                       name="settlement_gi[{{ $carrier->id }}][{{ $stateRecord->state }}]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="{{ $stateRecord->settlement_gi_pct }}"
                                       placeholder="70.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-0">Modified %</label>
                                <input type="number" 
                                       name="settlement_modified[{{ $carrier->id }}][{{ $stateRecord->state }}]" 
                                       class="form-control form-control-sm" 
                                       step="0.01" 
                                       min="0" 
                                       max="200"
                                       value="{{ $stateRecord->settlement_modified_pct }}"
                                       placeholder="95.00">
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    @endforeach
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

{{-- Add New Carrier Modal --}}
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
                @csrf
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
        url: '{{ route("admin.insurance-carriers.store") }}',
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
