{{-- Partner-Carrier-State Settlement Management Component --}}
<div id="carrier-states-container">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleAllCarriers()">
            <i class="mdi mdi-eye me-1"></i>Show/Hide All Carriers
        </button>
        <button type="button" class="btn btn-success btn-sm" onclick="openCreateCarrierModal()">
            <i class="mdi mdi-plus me-1"></i>Add New Carrier
        </button>
    </div>

    @foreach($insuranceCarriers as $carrier)
    <div class="carrier-state-section d-none" id="carrier-state-section-{{ $carrier->id }}">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0">
                    <i class="mdi mdi-briefcase me-1"></i>
                    {{ $carrier->name }}
                </h6>
                @if($carrier->payment_module)
                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $carrier->payment_module)) }}</span>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCarrierSection({{ $carrier->id }})" title="Remove this carrier">
                <i class="mdi mdi-close"></i> Remove
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label">Licensed States for {{ $carrier->name }}</label>
            <select class="form-select select2-multiple" 
                    name="carrier_states[{{ $carrier->id }}][]" 
                    id="carrier_states_{{ $carrier->id }}" 
                    multiple="multiple"
                    data-placeholder="Select states where partner can work..."
                    onchange="updateStateSettlementFields({{ $carrier->id }})">
                @php
                    $us_states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
                    
                    // Get existing states for edit mode
                    $existingStates = [];
                    if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                        $existingStates = $partnerCarrierStates[$carrier->id]->pluck('state')->toArray();
                    }
                @endphp
                @foreach($us_states as $state)
                    <option value="{{ $state }}" {{ in_array($state, $existingStates) ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Select all states where this partner is licensed for {{ $carrier->name }}</small>
        </div>

        <div id="state-settlement-fields-{{ $carrier->id }}" class="mt-3">
            {{-- Carrier-level settlement percentages --}}
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="mdi mdi-percent me-1"></i>
                        Commission Rates for {{ $carrier->name }}
                    </h6>
                    <small>These rates apply to ALL states for this carrier</small>
                </div>
                <div class="card-body">
                    @php
                        $existingCarrierData = null;
                        if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                            $existingCarrierData = $partnerCarrierStates[$carrier->id]->first();
                        }
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Level %</label>
                            <input type="number" 
                                   name="settlement_level[{{ $carrier->id }}]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="{{ $existingCarrierData ? $existingCarrierData->settlement_level_pct : '' }}"
                                   placeholder="95.00">
                            <small class="text-muted">Standard settlement rate</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Graded %</label>
                            <input type="number" 
                                   name="settlement_graded[{{ $carrier->id }}]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="{{ $existingCarrierData ? $existingCarrierData->settlement_graded_pct : '' }}"
                                   placeholder="75.00">
                            <small class="text-muted">Graded/Modified settlement</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">GI %</label>
                            <input type="number" 
                                   name="settlement_gi[{{ $carrier->id }}]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="{{ $existingCarrierData ? $existingCarrierData->settlement_gi_pct : '' }}"
                                   placeholder="60.00">
                            <small class="text-muted">Guaranteed Issue rate</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Modified %</label>
                            <input type="number" 
                                   name="settlement_modified[{{ $carrier->id }}]" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   max="200"
                                   value="{{ $existingCarrierData ? $existingCarrierData->settlement_modified_pct : '' }}"
                                   placeholder="85.00">
                            <small class="text-muted">Modified/Table shave rate</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="mdi mdi-information me-1"></i>
                        <strong>Note:</strong> These commission rates will apply to ALL selected states for {{ $carrier->name }}.
                    </div>
                </div>
            </div>
            
            {{-- Show selected states for reference --}}
            <div id="selected-states-{{ $carrier->id }}" class="mt-3">
                @if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id]))
                    <div class="alert alert-light">
                        <strong>Licensed States:</strong>
                        @foreach($partnerCarrierStates[$carrier->id] as $stateRecord)
                            <span class="badge bg-primary me-1">{{ $stateRecord->state }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($carrier->plan_types && is_array($carrier->plan_types) && count($carrier->plan_types) > 0)
        <div class="mt-3">
            <small class="text-muted">
                <i class="mdi mdi-information-outline me-1"></i>
                <strong>Available Plan Types:</strong>
                @foreach($carrier->plan_types as $plan)
                    <span class="badge bg-secondary-subtle text-secondary">{{ $plan }}</span>
                @endforeach
            </small>
        </div>
        @endif
    </div>
    @endforeach
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
