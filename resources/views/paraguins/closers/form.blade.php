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
        <input type="text" class="form-control" name="cn_name" value="{{ $lead->cn_name ?? '' }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label required">Phone Number</label>
        <input type="text" class="form-control" name="phone_number" value="{{ $lead->phone_number ?? '' }}" required>
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
        <input type="date" class="form-control" name="date_of_birth" value="{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('Y-m-d') : '' }}" required>
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" name="gender">
            <option value="">Select Gender</option>
            <option value="Male" {{ ($lead->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ ($lead->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Other" {{ ($lead->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="ssn" class="form-label required">SSN</label>
        <input type="text" class="form-control" name="ssn" value="{{ $lead->ssn ?? '' }}" placeholder="XXX-XX-XXXX" required>
    </div>
    <div class="col-md-6">
        <label for="address" class="form-label required">Full Address</label>
        <textarea class="form-control" name="address" rows="2" placeholder="Street address" required>{{ $lead->address ?? '' }}</textarea>
    </div>
    <div class="col-md-2">
        <label for="state" class="form-label required">State</label>
        <input type="text" class="form-control" name="state" value="{{ $lead->state ?? '' }}" placeholder="State" maxlength="2" required>
    </div>
    <div class="col-md-2">
        <label for="zip_code" class="form-label required">Zip Code</label>
        <input type="text" class="form-control" name="zip_code" value="{{ $lead->zip_code ?? '' }}" placeholder="Zip" maxlength="10" required>
    </div>
    <div class="col-md-2">
        <label for="birth_place" class="form-label">Birth Place</label>
        <input type="text" class="form-control" name="birth_place" value="{{ $lead->birth_place ?? '' }}" placeholder="City, State">
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-heart me-2"></i>Health Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label for="height_weight" class="form-label">Height & Weight</label>
        <input type="text" class="form-control" name="height_weight" value="{{ $lead->height_weight ?? '' }}" placeholder="5'10&quot;, 180 lbs">
    </div>
    <div class="col-md-2">
        <label for="smoker" class="form-label">Smoker</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="smoker" value="1" {{ $lead->smoker ? 'checked' : '' }}>
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-md-7">
        <label for="doctor_name" class="form-label">Doctor Name</label>
        <input type="text" class="form-control" name="doctor_name" value="{{ $lead->doctor_name ?? '' }}" placeholder="Dr. Name">
    </div>
    <div class="col-md-6">
        <label for="medical_issue" class="form-label">Medical Conditions</label>
        <textarea class="form-control" name="medical_issue" rows="2" placeholder="Any conditions">{{ $lead->medical_issue ?? '' }}</textarea>
    </div>
    <div class="col-md-6">
        <label for="medications" class="form-label">Medications</label>
        <textarea class="form-control" name="medications" rows="2" placeholder="Current medications">{{ $lead->medications ?? '' }}</textarea>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-shield me-2"></i>Insurance Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="carrier_name" class="form-label">Carrier Name</label>
        <input type="text" class="form-control" name="carrier_name" value="{{ $lead->carrier_name ?? '' }}" placeholder="Insurance company">
    </div>
    <div class="col-md-4">
        <label for="policy_type" class="form-label required">Policy Type</label>
        <input type="text" class="form-control" name="policy_type" value="{{ $lead->policy_type ?? '' }}" placeholder="Term Life, etc." required>
    </div>
    <div class="col-md-4">
        <label for="initial_draft_date" class="form-label required">Draft Date</label>
        <input type="date" class="form-control" name="initial_draft_date" value="{{ $lead->initial_draft_date ?? '' }}" required>
    </div>
    <div class="col-md-4">
        <label for="coverage_amount" class="form-label required">Coverage Amount</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="1" class="form-control" name="coverage_amount" value="{{ $lead->coverage_amount ?? '' }}" placeholder="Enter coverage amount" required>
        </div>
    </div>
    <div class="col-md-4">
        <label for="monthly_premium" class="form-label required">Monthly Premium</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" name="monthly_premium" value="{{ $lead->monthly_premium ?? '' }}" placeholder="125.50" required>
        </div>
    </div>
    <div class="col-md-4">
        <label for="source" class="form-label">Lead Source</label>
        <input type="text" class="form-control" name="source" value="{{ $lead->source ?? '' }}" placeholder="Referral, Web, etc.">
    </div>
    <div class="col-md-6">
        <label for="beneficiary" class="form-label required">Beneficiary Name</label>
        <input type="text" class="form-control" name="beneficiary" value="{{ $lead->beneficiary ?? '' }}" placeholder="Full name" required>
    </div>
    <div class="col-md-6">
        <label for="beneficiary_dob" class="form-label">Beneficiary DOB</label>
        <input type="date" class="form-control" name="beneficiary_dob" value="{{ $lead->beneficiary_dob ?? '' }}">
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-wallet me-2"></i>Banking Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="bank_name" class="form-label required">Bank Name</label>
        <input type="text" class="form-control" name="bank_name" value="{{ $lead->bank_name ?? '' }}" placeholder="Bank name" required>
    </div>
    <div class="col-md-4">
        <label for="account_type" class="form-label required">Account Type</label>
        <select class="form-select" name="account_type" required>
            <option value="">Select type</option>
            <option value="Checking" {{ ($lead->account_type ?? '') == 'Checking' ? 'selected' : '' }}>Checking</option>
            <option value="Savings" {{ ($lead->account_type ?? '') == 'Savings' ? 'selected' : '' }}>Savings</option>
            <option value="Card" {{ ($lead->account_type ?? '') == 'Card' ? 'selected' : '' }}>Card</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="account_number" class="form-label required">Account Number</label>
        <input type="text" class="form-control" name="account_number" value="{{ $lead->account_number ?? '' }}" placeholder="Account number" required>
    </div>
    <div class="col-md-4">
        <label for="routing_number" class="form-label required">Routing Number</label>
        <input type="text" class="form-control" name="routing_number" value="{{ $lead->routing_number ?? '' }}" placeholder="9 digits" required>
    </div>
    <div class="col-md-6">
        <label for="bank_balance" class="form-label">Bank Balance</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" name="bank_balance" value="{{ $lead->bank_balance ?? '' }}" placeholder="Balance">
        </div>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-credit-card me-2"></i>Card Info (Optional)</h6>
<div class="row g-3">
    <div class="col-md-6">
        <label for="card_number" class="form-label">Card Number</label>
        <input type="text" class="form-control" name="card_number" value="{{ $lead->card_number ?? '' }}" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
    </div>
    <div class="col-md-3">
        <label for="cvv" class="form-label">CVV</label>
        <input type="text" class="form-control" name="cvv" value="{{ $lead->cvv ?? '' }}" placeholder="XXX" maxlength="4">
    </div>
    <div class="col-md-3">
        <label for="expiry_date" class="form-label">Expiry</label>
        <input type="text" class="form-control" name="expiry_date" value="{{ $lead->expiry_date ?? '' }}" placeholder="MM/YYYY" maxlength="7">
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
                <option value="{{ $validator->id }}" {{ ($lead->assigned_validator_id ?? '') == $validator->id ? 'selected' : '' }}>
                    {{ $validator->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
@endif

