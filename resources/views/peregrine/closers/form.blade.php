<style>
    .readonly-value {
        background-color: #f8f9fa;
        padding: 10px 14px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        font-weight: 500;
    }
    .form-label {
        font-weight: 500;
        color: #2d2d2d;
        font-size: 0.875rem;
        margin-bottom: 6px;
    }
    .form-label.required:after {
        content: '*';
        color: #dc3545;
        margin-left: 4px;
    }
    .form-section-title {
        color: #1a1a1a;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #d4af37;
    }
</style>

<!-- Verifier Information (Read-Only) -->
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Verified Information:</strong> The following was collected by the verifier
</div>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <label class="form-label">Date</label>
        <div class="readonly-value">{{ $lead->date ?? 'N/A' }}</div>
    </div>
    <div class="col-md-3">
        <label class="form-label required">Customer Name</label>
        <input type="text" class="form-control" name="cn_name" value="{{ old('cn_name', $lead->cn_name ?? '') }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label required">Phone Number</label>
        <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number', $lead->phone_number ?? '') }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Verified Gender</label>
        <div class="readonly-value">{{ $lead->gender ?? 'N/A' }}</div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Verified By</label>
        <div class="readonly-value">{{ $lead->account_verified_by ?? 'N/A' }}</div>
    </div>
    <div class="col-md-1">
        <label class="form-label">Closer</label>
        <div class="readonly-value">{{ $lead->closer_name ?? 'N/A' }}</div>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-user me-2"></i>Personal Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="date_of_birth" class="form-label required">Date of Birth</label>
        <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label required">Gender</label>
        <select class="form-select" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" {{ strtoupper(old('gender', $lead->gender ?? '')) == 'MALE' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ strtoupper(old('gender', $lead->gender ?? '')) == 'FEMALE' ? 'selected' : '' }}>Female</option>
            <option value="Other" {{ strtoupper(old('gender', $lead->gender ?? '')) == 'OTHER' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="ssn" class="form-label required">SSN</label>
        <input type="text" class="form-control" name="ssn" value="{{ old('ssn', $lead->ssn ?? '') }}" placeholder="XXX-XX-XXXX" required>
    </div>
    <div class="col-md-6">
        <label for="address" class="form-label required">Full Address</label>
        <textarea class="form-control" name="address" rows="2" placeholder="Street address" required>{{ old('address', $lead->address ?? '') }}</textarea>
    </div>
    <div class="col-md-2">
        <label for="state" class="form-label required">State</label>
        <select class="form-select" name="state" id="state_select" required>
            <option value="">Select State</option>
            <option value="AL" {{ old('state', $lead->state ?? '') == 'AL' ? 'selected' : '' }}>Alabama (AL)</option>
            <option value="AK" {{ old('state', $lead->state ?? '') == 'AK' ? 'selected' : '' }}>Alaska (AK)</option>
            <option value="AZ" {{ old('state', $lead->state ?? '') == 'AZ' ? 'selected' : '' }}>Arizona (AZ)</option>
            <option value="AR" {{ old('state', $lead->state ?? '') == 'AR' ? 'selected' : '' }}>Arkansas (AR)</option>
            <option value="CA" {{ old('state', $lead->state ?? '') == 'CA' ? 'selected' : '' }}>California (CA)</option>
            <option value="CO" {{ old('state', $lead->state ?? '') == 'CO' ? 'selected' : '' }}>Colorado (CO)</option>
            <option value="CT" {{ old('state', $lead->state ?? '') == 'CT' ? 'selected' : '' }}>Connecticut (CT)</option>
            <option value="DE" {{ old('state', $lead->state ?? '') == 'DE' ? 'selected' : '' }}>Delaware (DE)</option>
            <option value="FL" {{ old('state', $lead->state ?? '') == 'FL' ? 'selected' : '' }}>Florida (FL)</option>
            <option value="GA" {{ old('state', $lead->state ?? '') == 'GA' ? 'selected' : '' }}>Georgia (GA)</option>
            <option value="HI" {{ old('state', $lead->state ?? '') == 'HI' ? 'selected' : '' }}>Hawaii (HI)</option>
            <option value="ID" {{ old('state', $lead->state ?? '') == 'ID' ? 'selected' : '' }}>Idaho (ID)</option>
            <option value="IL" {{ old('state', $lead->state ?? '') == 'IL' ? 'selected' : '' }}>Illinois (IL)</option>
            <option value="IN" {{ old('state', $lead->state ?? '') == 'IN' ? 'selected' : '' }}>Indiana (IN)</option>
            <option value="IA" {{ old('state', $lead->state ?? '') == 'IA' ? 'selected' : '' }}>Iowa (IA)</option>
            <option value="KS" {{ old('state', $lead->state ?? '') == 'KS' ? 'selected' : '' }}>Kansas (KS)</option>
            <option value="KY" {{ old('state', $lead->state ?? '') == 'KY' ? 'selected' : '' }}>Kentucky (KY)</option>
            <option value="LA" {{ old('state', $lead->state ?? '') == 'LA' ? 'selected' : '' }}>Louisiana (LA)</option>
            <option value="ME" {{ old('state', $lead->state ?? '') == 'ME' ? 'selected' : '' }}>Maine (ME)</option>
            <option value="MD" {{ old('state', $lead->state ?? '') == 'MD' ? 'selected' : '' }}>Maryland (MD)</option>
            <option value="MA" {{ old('state', $lead->state ?? '') == 'MA' ? 'selected' : '' }}>Massachusetts (MA)</option>
            <option value="MI" {{ old('state', $lead->state ?? '') == 'MI' ? 'selected' : '' }}>Michigan (MI)</option>
            <option value="MN" {{ old('state', $lead->state ?? '') == 'MN' ? 'selected' : '' }}>Minnesota (MN)</option>
            <option value="MS" {{ old('state', $lead->state ?? '') == 'MS' ? 'selected' : '' }}>Mississippi (MS)</option>
            <option value="MO" {{ old('state', $lead->state ?? '') == 'MO' ? 'selected' : '' }}>Missouri (MO)</option>
            <option value="MT" {{ old('state', $lead->state ?? '') == 'MT' ? 'selected' : '' }}>Montana (MT)</option>
            <option value="NE" {{ old('state', $lead->state ?? '') == 'NE' ? 'selected' : '' }}>Nebraska (NE)</option>
            <option value="NV" {{ old('state', $lead->state ?? '') == 'NV' ? 'selected' : '' }}>Nevada (NV)</option>
            <option value="NH" {{ old('state', $lead->state ?? '') == 'NH' ? 'selected' : '' }}>New Hampshire (NH)</option>
            <option value="NJ" {{ old('state', $lead->state ?? '') == 'NJ' ? 'selected' : '' }}>New Jersey (NJ)</option>
            <option value="NM" {{ old('state', $lead->state ?? '') == 'NM' ? 'selected' : '' }}>New Mexico (NM)</option>
            <option value="NY" {{ old('state', $lead->state ?? '') == 'NY' ? 'selected' : '' }}>New York (NY)</option>
            <option value="NC" {{ old('state', $lead->state ?? '') == 'NC' ? 'selected' : '' }}>North Carolina (NC)</option>
            <option value="ND" {{ old('state', $lead->state ?? '') == 'ND' ? 'selected' : '' }}>North Dakota (ND)</option>
            <option value="OH" {{ old('state', $lead->state ?? '') == 'OH' ? 'selected' : '' }}>Ohio (OH)</option>
            <option value="OK" {{ old('state', $lead->state ?? '') == 'OK' ? 'selected' : '' }}>Oklahoma (OK)</option>
            <option value="OR" {{ old('state', $lead->state ?? '') == 'OR' ? 'selected' : '' }}>Oregon (OR)</option>
            <option value="PA" {{ old('state', $lead->state ?? '') == 'PA' ? 'selected' : '' }}>Pennsylvania (PA)</option>
            <option value="RI" {{ old('state', $lead->state ?? '') == 'RI' ? 'selected' : '' }}>Rhode Island (RI)</option>
            <option value="SC" {{ old('state', $lead->state ?? '') == 'SC' ? 'selected' : '' }}>South Carolina (SC)</option>
            <option value="SD" {{ old('state', $lead->state ?? '') == 'SD' ? 'selected' : '' }}>South Dakota (SD)</option>
            <option value="TN" {{ old('state', $lead->state ?? '') == 'TN' ? 'selected' : '' }}>Tennessee (TN)</option>
            <option value="TX" {{ old('state', $lead->state ?? '') == 'TX' ? 'selected' : '' }}>Texas (TX)</option>
            <option value="UT" {{ old('state', $lead->state ?? '') == 'UT' ? 'selected' : '' }}>Utah (UT)</option>
            <option value="VT" {{ old('state', $lead->state ?? '') == 'VT' ? 'selected' : '' }}>Vermont (VT)</option>
            <option value="VA" {{ old('state', $lead->state ?? '') == 'VA' ? 'selected' : '' }}>Virginia (VA)</option>
            <option value="WA" {{ old('state', $lead->state ?? '') == 'WA' ? 'selected' : '' }}>Washington (WA)</option>
            <option value="WV" {{ old('state', $lead->state ?? '') == 'WV' ? 'selected' : '' }}>West Virginia (WV)</option>
            <option value="WI" {{ old('state', $lead->state ?? '') == 'WI' ? 'selected' : '' }}>Wisconsin (WI)</option>
            <option value="WY" {{ old('state', $lead->state ?? '') == 'WY' ? 'selected' : '' }}>Wyoming (WY)</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="zip_code" class="form-label required">Zip Code</label>
        <input type="text" class="form-control" name="zip_code" value="{{ old('zip_code', $lead->zip_code ?? '') }}" placeholder="Zip" maxlength="10" required>
    </div>
    <div class="col-md-2">
        <label for="birth_place" class="form-label">Birth Place</label>
        <input type="text" class="form-control" name="birth_place" value="{{ old('birth_place', $lead->birth_place ?? '') }}" placeholder="City, State">
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-heart me-2"></i>Health Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label for="height_weight" class="form-label">Height & Weight</label>
        <input type="text" class="form-control" name="height_weight" value="{{ old('height_weight', $lead->height_weight ?? '') }}" placeholder="5'10&quot;, 180 lbs">
    </div>
    <div class="col-md-2">
        <label for="smoker" class="form-label">Smoker</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="smoker" value="1" {{ old('smoker', $lead->smoker) ? 'checked' : '' }}>
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-md-4">
        <label for="doctor_name" class="form-label required">Doctor Name</label>
        <input type="text" class="form-control" name="doctor_name" value="{{ old('doctor_name', $lead->doctor_name ?? '') }}" placeholder="Dr. Name" required>
    </div>
    <div class="col-md-3">
        <label for="doctor_number" class="form-label required">Doctor Number</label>
        <input type="text" class="form-control" name="doctor_number" value="{{ old('doctor_number', $lead->doctor_number ?? '') }}" placeholder="Phone number" required>
    </div>
    <div class="col-md-5">
        <label for="doctor_address" class="form-label required">Doctor Address</label>
        <input type="text" class="form-control" name="doctor_address" value="{{ old('doctor_address', $lead->doctor_address ?? '') }}" placeholder="Address" required>
    </div>
    <div class="col-md-6">
        <label for="medical_issue" class="form-label required">Medical Conditions</label>
        <textarea class="form-control" name="medical_issue" rows="2" placeholder="Any conditions" required>{{ old('medical_issue', $lead->medical_issue ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label for="medications" class="form-label required">Medications</label>
        <textarea class="form-control" name="medications" rows="2" placeholder="Current medications" required>{{ old('medications', $lead->medications ?? '') }}</textarea>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-shield me-2"></i>Insurance Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="carrier_name" class="form-label">Carrier Name</label>
        <input type="text" class="form-control" name="carrier_name" value="{{ old('carrier_name', $lead->carrier_name ?? '') }}" placeholder="Insurance company">
    </div>
    <div class="col-md-4">
        <label for="policy_type" class="form-label required">Policy Type</label>
        <select class="form-select" name="policy_type" required>
            <option value="">Select Policy Type</option>
            <option value="Level" {{ old('policy_type', $lead->policy_type ?? '') == 'Level' ? 'selected' : '' }}>Level</option>
            <option value="Graded" {{ old('policy_type', $lead->policy_type ?? '') == 'Graded' ? 'selected' : '' }}>Graded</option>
            <option value="G.I" {{ old('policy_type', $lead->policy_type ?? '') == 'G.I' ? 'selected' : '' }}>G.I</option>
            <option value="Modified" {{ old('policy_type', $lead->policy_type ?? '') == 'Modified' ? 'selected' : '' }}>Modified</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="initial_draft_date" class="form-label required">Draft Date</label>
        <input type="date" class="form-control" name="initial_draft_date" value="{{ old('initial_draft_date', $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-4">
        <label for="coverage_amount" class="form-label required">Coverage Amount</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="1" class="form-control" name="coverage_amount" value="{{ old('coverage_amount', $lead->coverage_amount ?? '') }}" placeholder="Enter coverage amount" required>
        </div>
    </div>
    <div class="col-md-4">
        <label for="monthly_premium" class="form-label required">Monthly Premium</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" name="monthly_premium" value="{{ old('monthly_premium', $lead->monthly_premium ?? '') }}" placeholder="125.50" required>
        </div>
    </div>
    <div class="col-md-4">
        <label for="source" class="form-label">Lead Source</label>
        <input type="text" class="form-control" name="source" value="{{ old('source', $lead->source ?? '') }}" placeholder="Referral, Web, etc.">
    </div>
</div>

<!-- Multiple Beneficiaries Section -->
<h6 class="form-section-title"><i class="bx bx-heart me-2"></i>Beneficiary Information</h6>
<div id="beneficiaries-container" class="mb-3">
    @php
        // Check for old input first (from failed submission), then existing beneficiaries
        $existingBeneficiaries = old('beneficiaries', $lead->beneficiaries ?? []);
        // If no beneficiaries in JSON but old fields exist, migrate them
        if (empty($existingBeneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
            $existingBeneficiaries = [[
                'name' => $lead->beneficiary ?? '',
                'dob' => $lead->beneficiary_dob ?? '',
                'relation' => ''
            ]];
        }
        // Ensure at least one beneficiary row
        if (empty($existingBeneficiaries)) {
            $existingBeneficiaries = [['name' => '', 'dob' => '', 'relation' => '']];
        }
    @endphp
    
    @foreach($existingBeneficiaries as $index => $beneficiary)
    <div class="row g-3 mb-2 beneficiary-row" data-index="{{ $index }}">
        <div class="col-md-4">
            <label for="beneficiaries[{{ $index }}][name]" class="form-label required">
                Beneficiary Name {{ $index > 0 ? ($index + 1) : '' }}
            </label>
            <input type="text" class="form-control" name="beneficiaries[{{ $index }}][name]" 
                   value="{{ $beneficiary['name'] ?? '' }}" placeholder="Full name" required>
        </div>
        <div class="col-md-3">
            <label for="beneficiaries[{{ $index }}][dob]" class="form-label required">Date of Birth</label>
            <input type="date" class="form-control" name="beneficiaries[{{ $index }}][dob]" 
                   value="{{ $beneficiary['dob'] ?? '' }}" required>
        </div>
        <div class="col-md-3">
            <label for="beneficiaries[{{ $index }}][relation]" class="form-label">Relation</label>
            <select class="form-select" name="beneficiaries[{{ $index }}][relation]">
                <option value="">Select relation</option>
                <option value="Spouse" {{ ($beneficiary['relation'] ?? '') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                <option value="Child" {{ ($beneficiary['relation'] ?? '') == 'Child' ? 'selected' : '' }}>Child</option>
                <option value="Parent" {{ ($beneficiary['relation'] ?? '') == 'Parent' ? 'selected' : '' }}>Parent</option>
                <option value="Sibling" {{ ($beneficiary['relation'] ?? '') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                <option value="Grandchild" {{ ($beneficiary['relation'] ?? '') == 'Grandchild' ? 'selected' : '' }}>Grandchild</option>
                <option value="Other" {{ ($beneficiary['relation'] ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            @if($index === 0)
                <button type="button" class="btn btn-success w-100" id="add-beneficiary" title="Add Another Beneficiary">
                    <i class="bx bx-plus"></i> Add
                </button>
            @else
                <button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary">
                    <i class="bx bx-minus"></i> Remove
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>

<script>
(function() {
    let beneficiaryIndex = {{ count($existingBeneficiaries) }};
    let addButtonInitialized = false;
    
    // Add beneficiary - use once() to ensure single execution per click
    function initAddButton() {
        if (addButtonInitialized) return;
        addButtonInitialized = true;
        
        const addBtn = document.getElementById('add-beneficiary');
        if (!addBtn) return;
        
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const container = document.getElementById('beneficiaries-container');
            const newRow = document.createElement('div');
            newRow.className = 'row g-3 mb-2 beneficiary-row';
            newRow.setAttribute('data-index', beneficiaryIndex);
            newRow.innerHTML = `<div class="col-md-4"><label for="beneficiaries[${beneficiaryIndex}][name]" class="form-label required">Beneficiary Name ${beneficiaryIndex + 1}</label><input type="text" class="form-control" name="beneficiaries[${beneficiaryIndex}][name]" placeholder="Full name" required></div><div class="col-md-3"><label for="beneficiaries[${beneficiaryIndex}][dob]" class="form-label required">Date of Birth</label><input type="date" class="form-control" name="beneficiaries[${beneficiaryIndex}][dob]" required></div><div class="col-md-3"><label for="beneficiaries[${beneficiaryIndex}][relation]" class="form-label">Relation</label><select class="form-select" name="beneficiaries[${beneficiaryIndex}][relation]"><option value="">Select relation</option><option value="Spouse">Spouse</option><option value="Child">Child</option><option value="Parent">Parent</option><option value="Sibling">Sibling</option><option value="Grandchild">Grandchild</option><option value="Other">Other</option></select></div><div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary"><i class="bx bx-minus"></i> Remove</button></div>`;
            container.appendChild(newRow);
            beneficiaryIndex++;
            
            // Attach remove handler to new row only
            newRow.querySelector('.remove-beneficiary').addEventListener('click', function(e) {
                e.preventDefault();
                newRow.remove();
            }, { once: true });
        });
    }
    
    // Remove beneficiary (for existing rows)
    function initRemoveButtons() {
        document.querySelectorAll('.remove-beneficiary').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.beneficiary-row').remove();
            }, { once: true });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initAddButton();
            initRemoveButtons();
        });
    } else {
        initAddButton();
        initRemoveButtons();
    }
})();
</script>

<div class="row g-3 mb-3">

    <div class="col-md-4">
        <label for="bank_name" class="form-label required">Bank Name</label>
        <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', $lead->bank_name ?? '') }}" placeholder="Bank name" required>
    </div>
    <div class="col-md-4">
        <label for="account_type" class="form-label required">Account Type</label>
        <select class="form-select" name="account_type" required>
            <option value="">Select type</option>
            <option value="Checking" {{ old('account_type', $lead->account_type ?? '') == 'Checking' ? 'selected' : '' }}>Checking</option>
            <option value="Savings" {{ old('account_type', $lead->account_type ?? '') == 'Savings' ? 'selected' : '' }}>Savings</option>
            <option value="Card" {{ old('account_type', $lead->account_type ?? '') == 'Card' ? 'selected' : '' }}>Card</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="account_number" class="form-label required">Account Number</label>
        <input type="text" class="form-control" name="account_number" value="{{ old('account_number', $lead->account_number ?? '') }}" placeholder="Account number" required>
    </div>
    <div class="col-md-4">
        <label for="routing_number" class="form-label required">Routing Number</label>
        <input type="text" class="form-control" name="routing_number" value="{{ old('routing_number', $lead->routing_number ?? '') }}" placeholder="9 digits" required>
    </div>
    <div class="col-md-6">
        <label for="bank_balance" class="form-label">Bank Balance</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" name="bank_balance" value="{{ old('bank_balance', $lead->bank_balance ?? '') }}" placeholder="Balance">
        </div>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-credit-card me-2"></i>Card Info (Optional)</h6>
<div class="row g-3">
    <div class="col-md-6">
        <label for="card_number" class="form-label">Card Number</label>
        <input type="text" class="form-control" name="card_number" value="{{ old('card_number', $lead->card_number ?? '') }}" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
    </div>
    <div class="col-md-3">
        <label for="cvv" class="form-label">CVV</label>
        <input type="text" class="form-control" name="cvv" value="{{ old('cvv', $lead->cvv ?? '') }}" placeholder="XXX" maxlength="4">
    </div>
    <div class="col-md-3">
        <label for="expiry_date" class="form-label">Expiry</label>
        <input type="text" class="form-control" name="expiry_date" value="{{ old('expiry_date', $lead->expiry_date ?? '') }}">
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-briefcase me-2"></i>Partner Information</h6>
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <label for="assigned_partner" class="form-label">Assigned Partner</label>
        <input type="text" class="form-control" name="assigned_partner" id="assigned_partner" value="{{ old('assigned_partner', $lead->assigned_partner ?? '') }}" placeholder="Enter partner name">
    </div>
</div>

@if(!isset($isValidator))
<!-- Follow Up Schedule Section -->
<h6 class="form-section-title"><i class="bx bx-calendar-event me-2"></i>Follow Up Schedule</h6>
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <label for="followup_required" class="form-label required">Follow Up Required</label>
        <select class="form-select" name="followup_required" id="followup_required" required>
            <option value="">Select option...</option>
            <option value="1" {{ old('followup_required', $lead->followup_required ?? '') == '1' ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('followup_required', $lead->followup_required ?? '') === '0' || old('followup_required', $lead->followup_required ?? '') === 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>
    <div class="col-md-12" id="followup_schedule_fields" style="display: none;">
        <label for="followup_scheduled_at" class="form-label required">Follow Up Date & Time</label>
        <input type="datetime-local" class="form-control" name="followup_scheduled_at" id="followup_scheduled_at" value="{{ old('followup_scheduled_at', $lead->followup_scheduled_at ? \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('Y-m-d\TH:i') : '') }}">
        <small class="text-muted">When should the follow-up call be scheduled?</small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const followupRequired = document.getElementById('followup_required');
    const followupScheduleFields = document.getElementById('followup_schedule_fields');
    const followupScheduledAt = document.getElementById('followup_scheduled_at');
    
    function toggleFollowupFields() {
        if (followupRequired.value === '1') {
            followupScheduleFields.style.display = 'block';
            followupScheduledAt.setAttribute('required', 'required');
        } else {
            followupScheduleFields.style.display = 'none';
            followupScheduledAt.removeAttribute('required');
        }
    }
    
    // Initialize on page load
    toggleFollowupFields();
    
    // Listen for changes
    followupRequired.addEventListener('change', toggleFollowupFields);
});
</script>

<h6 class="form-section-title"><i class="bx bx-user-check me-2"></i>Assign Validator</h6>
<div class="row g-3">
    <div class="col-md-12">
        <label for="assigned_validator_id" class="form-label required">Select Validator</label>
        <select class="form-select" name="assigned_validator_id" required>
            <option value="">Choose Validator...</option>
            @foreach($validators ?? [] as $validator)
                <option value="{{ $validator->id }}" {{ old('assigned_validator_id', $lead->assigned_validator_id ?? '') == $validator->id ? 'selected' : '' }}>
                    {{ $validator->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
@else
<!-- Follow Up Schedule Information (Read-Only for Validator) -->
<h6 class="form-section-title"><i class="bx bx-calendar-event me-2"></i>Follow Up Schedule</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Follow Up Required</label>
        <div class="readonly-value">
            @if($lead->followup_required)
                <span class="badge bg-success">Yes</span>
            @else
                <span class="badge bg-secondary">No</span>
            @endif
        </div>
    </div>
    @if($lead->followup_required && $lead->followup_scheduled_at)
    <div class="col-md-6">
        <label class="form-label">Scheduled Date & Time</label>
        <div class="readonly-value">
            <i class="bx bx-calendar me-2"></i>{{ \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('M d, Y h:i A') }}
        </div>
    </div>
    @endif
</div>
@endif

