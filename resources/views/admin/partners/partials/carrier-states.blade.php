{{-- Partner-Carrier-State Settlement Management Component --}}
<div id="carrier-states-container">

    @foreach($insuranceCarriers as $carrier)
    <div class="cs-carrier" id="carrier-state-section-{{ $carrier->id }}">
        {{-- Carrier Header --}}
        <div class="cs-carrier-hdr">
            <div class="cs-carrier-name">
                <i class="bx bx-buildings"></i> {{ $carrier->name }}
                @if($carrier->payment_module)
                    <span class="cs-pay-tag">{{ ucfirst(str_replace('_', ' ', $carrier->payment_module)) }}</span>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:.3rem;margin-left:auto;">
                <button type="button" class="cs-copy-btn" onclick="copyStatesFrom({{ $carrier->id }})" title="Copy states & rates from another carrier">
                    <i class="bx bx-copy"></i> Copy from
                </button>
                <button type="button" class="cs-remove-btn" onclick="removeCarrierSection({{ $carrier->id }})" title="Remove carrier">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>

        {{-- State Selection --}}
        <div class="cs-field-group">
            <label class="cs-label">Licensed States for {{ $carrier->name }}</label>
            <select class="form-select select2-multiple"
                    name="carrier_states[{{ $carrier->id }}][]"
                    id="carrier_states_{{ $carrier->id }}"
                    multiple="multiple"
                    data-placeholder="Select states..."
                    onchange="updateStateSettlementFields({{ $carrier->id }})">
                @php
                    $us_states = ['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];
                    $existingStates = [];
                    if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                        $existingStates = $partnerCarrierStates[$carrier->id]->pluck('state')->toArray();
                    }
                @endphp
                @foreach($us_states as $state)
                    <option value="{{ $state }}" {{ in_array($state, $existingStates) ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
            <div class="cs-hint">Select all states where this partner is licensed for {{ $carrier->name }}</div>
        </div>

        {{-- Commission Rates --}}
        <div id="state-settlement-fields-{{ $carrier->id }}" class="mt-2">
            <div class="cs-rates-card">
                <div class="cs-rates-hdr">
                    <i class="bx bx-line-chart"></i>
                    <span>Commission Rates for {{ $carrier->name }}</span>
                    <small>Applies to all states</small>
                </div>
                <div class="cs-rates-body">
                    @php
                        $existingCarrierData = null;
                        if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id])) {
                            $existingCarrierData = $partnerCarrierStates[$carrier->id]->first();
                        }

                        // Build the 4 commission column definitions.
                        // If the carrier defines plan_types, use those as labels (up to 4).
                        // Otherwise fall back to the standard Level/Graded/GI/Modified labels.
                        $defaultCols = [
                            ['field' => 'level',    'label' => 'Level %',    'hint' => 'Standard settlement', 'placeholder' => '95.00'],
                            ['field' => 'graded',   'label' => 'Graded %',   'hint' => 'Graded/Modified',      'placeholder' => '75.00'],
                            ['field' => 'gi',       'label' => 'GI %',       'hint' => 'Guaranteed Issue',     'placeholder' => '60.00'],
                            ['field' => 'modified', 'label' => 'Modified %', 'hint' => 'Table shave rate',     'placeholder' => '85.00'],
                        ];

                        $planTypes = (is_array($carrier->plan_types) && count($carrier->plan_types) > 0)
                            ? array_values($carrier->plan_types)
                            : [];

                        $commissionCols = [];
                        foreach ($defaultCols as $i => $col) {
                            if (isset($planTypes[$i])) {
                                $col['label'] = $planTypes[$i] . ' %';
                                $col['hint']  = $planTypes[$i];
                            }
                            $commissionCols[] = $col;
                        }

                        $fieldValues = [
                            'level'    => $existingCarrierData ? $existingCarrierData->settlement_level_pct    : '',
                            'graded'   => $existingCarrierData ? $existingCarrierData->settlement_graded_pct   : '',
                            'gi'       => $existingCarrierData ? $existingCarrierData->settlement_gi_pct       : '',
                            'modified' => $existingCarrierData ? $existingCarrierData->settlement_modified_pct : '',
                        ];
                    @endphp
                    <div class="row g-2">
                        @foreach($commissionCols as $col)
                        <div class="col-md-3">
                            <label class="cs-label">{{ $col['label'] }}</label>
                            <input type="number"
                                   name="settlement_{{ $col['field'] }}[{{ $carrier->id }}]"
                                   class="cs-input"
                                   step="0.01" min="0" max="200"
                                   value="{{ $fieldValues[$col['field']] }}"
                                   placeholder="{{ $col['placeholder'] }}">
                            <div class="cs-hint">{{ $col['hint'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Selected states reference --}}
            <div id="selected-states-{{ $carrier->id }}" class="mt-2">
                @if(isset($partnerCarrierStates) && isset($partnerCarrierStates[$carrier->id]))
                    <div class="cs-state-tags">
                        @foreach($partnerCarrierStates[$carrier->id] as $stateRecord)
                            <span class="cs-state-pill">{{ $stateRecord->state }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Plan types --}}
        @if($carrier->plan_types && is_array($carrier->plan_types) && count($carrier->plan_types) > 0)
        <div class="cs-plans-row">
            <i class="bx bx-list-check"></i>
            <span>Plans:</span>
            @foreach($carrier->plan_types as $plan)
                <span class="cs-plan-pill">{{ $plan }}</span>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
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

.cs-copy-btn {
    font-size:.6rem; font-weight:600; color:#556ee6; cursor:pointer;
    display:inline-flex; align-items:center; gap:.2rem;
    border:1px solid rgba(85,110,230,.15); padding:.15rem .4rem; border-radius:.25rem;
    background:rgba(85,110,230,.04); transition:all .15s;
}
.cs-copy-btn:hover { background:rgba(85,110,230,.1); border-color:#556ee6; }

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
