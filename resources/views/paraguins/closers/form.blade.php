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
    <div class="col-md-3">
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
        <label class="form-label">Verified By</label>
        <div class="readonly-value">{{ $lead->account_verified_by ?? 'N/A' }}</div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Assigned Closer</label>
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
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" name="gender">
            <option value="">Select Gender</option>
            <option value="Male" {{ old('gender', $lead->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ old('gender', $lead->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Other" {{ old('gender', $lead->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
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
        <input type="text" class="form-control" name="state" value="{{ old('state', $lead->state ?? '') }}" placeholder="State" maxlength="2" required>
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
    <div class="col-md-7">
        <label for="doctor_name" class="form-label">Doctor Name</label>
        <input type="text" class="form-control" name="doctor_name" value="{{ old('doctor_name', $lead->doctor_name ?? '') }}" placeholder="Dr. Name">
    </div>
    <div class="col-md-6">
        <label for="medical_issue" class="form-label">Medical Conditions</label>
        <textarea class="form-control" name="medical_issue" rows="2" placeholder="Any conditions">{{ old('medical_issue', $lead->medical_issue ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label for="medications" class="form-label">Medications</label>
        <textarea class="form-control" name="medications" rows="2" placeholder="Current medications">{{ old('medications', $lead->medications ?? '') }}</textarea>
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
        <input type="text" class="form-control" name="policy_type" value="{{ old('policy_type', $lead->policy_type ?? '') }}" placeholder="Term Life, etc." required>
    </div>
    <div class="col-md-4">
        <label for="initial_draft_date" class="form-label required">Draft Date</label>
        <input type="date" class="form-control" name="initial_draft_date" value="{{ old('initial_draft_date', $lead->initial_draft_date ?? '') }}" required>
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
            <label for="beneficiaries[{{ $index }}][name]" class="form-label {{ $index === 0 ? 'required' : '' }}">
                Beneficiary Name {{ $index > 0 ? ($index + 1) : '' }}
            </label>
            <input type="text" class="form-control" name="beneficiaries[{{ $index }}][name]" 
                   value="{{ $beneficiary['name'] ?? '' }}" placeholder="Full name" {{ $index === 0 ? 'required' : '' }}>
        </div>
        <div class="col-md-3">
            <label for="beneficiaries[{{ $index }}][dob]" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" name="beneficiaries[{{ $index }}][dob]" 
                   value="{{ $beneficiary['dob'] ?? '' }}">
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
            newRow.innerHTML = `<div class="col-md-4"><label for="beneficiaries[${beneficiaryIndex}][name]" class="form-label">Beneficiary Name ${beneficiaryIndex + 1}</label><input type="text" class="form-control" name="beneficiaries[${beneficiaryIndex}][name]" placeholder="Full name"></div><div class="col-md-3"><label for="beneficiaries[${beneficiaryIndex}][dob]" class="form-label">Date of Birth</label><input type="date" class="form-control" name="beneficiaries[${beneficiaryIndex}][dob]"></div><div class="col-md-3"><label for="beneficiaries[${beneficiaryIndex}][relation]" class="form-label">Relation</label><select class="form-select" name="beneficiaries[${beneficiaryIndex}][relation]"><option value="">Select relation</option><option value="Spouse">Spouse</option><option value="Child">Child</option><option value="Parent">Parent</option><option value="Sibling">Sibling</option><option value="Grandchild">Grandchild</option><option value="Other">Other</option></select></div><div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary"><i class="bx bx-minus"></i> Remove</button></div>`;
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

@if(!isset($isValidator))
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
@endif

