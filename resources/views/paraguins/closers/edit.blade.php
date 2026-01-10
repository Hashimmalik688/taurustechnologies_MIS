@extends('layouts.master')

@section('title')
    Complete Lead Information
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Paraguins @endslot
        @slot('title') Complete Lead @endslot
    @endcomponent

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('paraguins.closers.update', $lead->id) }}">
        @csrf
        @method('PUT')

        <style>
            .lead-form-card {
                border-radius: 10px;
                border: none;
                box-shadow: 0 2px 10px rgba(0,0,0,0.06);
                margin-bottom: 24px;
            }
            .lead-form-card .card-header {
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
                border-radius: 10px 10px 0 0;
                padding: 16px 24px;
                border: none;
            }
            .lead-form-card .card-title {
                color: #d4af37;
                font-weight: 600;
                font-size: 1.1rem;
                margin: 0;
            }
            .lead-form-card .card-body {
                padding: 28px;
            }
            .form-section-title {
                color: #1a1a1a;
                font-size: 0.95rem;
                font-weight: 600;
                margin-bottom: 20px;
                padding-bottom: 8px;
                border-bottom: 2px solid #d4af37;
                display: inline-block;
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
            .form-control:disabled, .form-select:disabled {
                background-color: #e9ecef;
                opacity: 1;
            }
            .readonly-value {
                background-color: #f8f9fa;
                padding: 10px 14px;
                border-radius: 6px;
                border: 1px solid #e0e0e0;
                font-weight: 500;
            }
        </style>

        <div class="row">
            <div class="col-12">
                <!-- Verifier Information (Read-Only) -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-check-circle section-icon"></i>
                            Verified Information (Read-Only)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> The following information was collected by the verifier and cannot be changed.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <div class="readonly-value">{{ $lead->date ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Customer Name</label>
                                <div class="readonly-value">{{ $lead->cn_name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone Number</label>
                                <div class="readonly-value">{{ $lead->phone_number ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Verified By</label>
                                <div class="readonly-value">{{ $lead->account_verified_by ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assigned Closer</label>
                                <div class="readonly-value">{{ $lead->closer_name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-user section-icon"></i>
                            Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label required">Date of Birth</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $lead->date_of_birth) }}" required>
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', $lead->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $lead->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $lead->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="ssn" class="form-label required">Social Security Number</label>
                                <input type="text" class="form-control @error('ssn') is-invalid @enderror"
                                    id="ssn" name="ssn" value="{{ old('ssn', $lead->ssn) }}" placeholder="XXX-XX-XXXX" required>
                                @error('ssn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label for="address" class="form-label required">Full Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" rows="2" placeholder="Street address, city, state, ZIP code" required>{{ old('address', $lead->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="birth_place" class="form-label">Birth Place</label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place"
                                    value="{{ old('birth_place', $lead->birth_place) }}" placeholder="City, State">
                            </div>
                        </div>

                        <h6 class="form-section-title mt-4">Health Information</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="height_weight" class="form-label">Height & Weight</label>
                                <input type="text" class="form-control" id="height_weight" name="height_weight"
                                    value="{{ old('height_weight', $lead->height_weight) }}" placeholder="5'10&quot;, 180 lbs">
                            </div>
                            <div class="col-md-2">
                                <label for="smoker" class="form-label">Smoker Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="smoker" name="smoker" value="1"
                                        {{ old('smoker', $lead->smoker) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="smoker">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label for="doctor_name" class="form-label">Primary Care Physician</label>
                                <input type="text" class="form-control" id="doctor_name" name="doctor_name"
                                    value="{{ old('doctor_name', $lead->doctor_name) }}" placeholder="Dr. Name, Practice">
                            </div>
                            <div class="col-md-6">
                                <label for="medical_issue" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medical_issue" name="medical_issue" rows="2"
                                    placeholder="List any pre-existing conditions">{{ old('medical_issue', $lead->medical_issue) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="medications" class="form-label">Current Medications</label>
                                <textarea class="form-control" id="medications" name="medications" rows="2"
                                    placeholder="List all current medications">{{ old('medications', $lead->medications) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insurance & Coverage Information -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-shield section-icon"></i>
                            Insurance & Coverage Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="carrier_name" class="form-label">Carrier Name</label>
                                <input type="text" class="form-control" id="carrier_name" name="carrier_name"
                                    value="{{ old('carrier_name', $lead->carrier_name) }}" placeholder="Insurance company">
                            </div>
                            <div class="col-md-4">
                                <label for="policy_type" class="form-label required">Policy Type</label>
                                <input type="text" class="form-control @error('policy_type') is-invalid @enderror"
                                    id="policy_type" name="policy_type" value="{{ old('policy_type', $lead->policy_type) }}"
                                    placeholder="Term Life, Whole Life, etc." required>
                                @error('policy_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="initial_draft_date" class="form-label required">Initial Draft Date</label>
                                <input type="date" class="form-control @error('initial_draft_date') is-invalid @enderror"
                                    id="initial_draft_date" name="initial_draft_date" value="{{ old('initial_draft_date', $lead->initial_draft_date) }}" required>
                                @error('initial_draft_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="coverage_amount" class="form-label required">Coverage Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="1000" class="form-control @error('coverage_amount') is-invalid @enderror"
                                        id="coverage_amount" name="coverage_amount" value="{{ old('coverage_amount', $lead->coverage_amount) }}"
                                        placeholder="250000" required>
                                    @error('coverage_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="monthly_premium" class="form-label required">Monthly Premium</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('monthly_premium') is-invalid @enderror"
                                        id="monthly_premium" name="monthly_premium" value="{{ old('monthly_premium', $lead->monthly_premium) }}"
                                        placeholder="125.50" required>
                                    @error('monthly_premium')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="source" class="form-label">Lead Source</label>
                                <input type="text" class="form-control" id="source" name="source"
                                    value="{{ old('source', $lead->source) }}" placeholder="Referral, Web, etc.">
                            </div>
                        </div>

                        <!-- Multiple Beneficiaries Section -->
                        <h6 class="form-section-title mt-4">Beneficiary Information</h6>
                        <div id="beneficiaries-container" class="mb-3">
                            @php
                                // Check for old input first (from failed submission), then existing beneficiaries
                                $existingBeneficiaries = old('beneficiaries', $lead->beneficiaries ?? []);
                                // If no beneficiaries in JSON but old fields exist, migrate them
                                if (empty($existingBeneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
                                    $existingBeneficiaries = [[
                                        'name' => $lead->beneficiary ?? '',
                                        'dob' => $lead->beneficiary_dob ?? ''
                                    ]];
                                }
                                // Ensure at least one beneficiary row
                                if (empty($existingBeneficiaries)) {
                                    $existingBeneficiaries = [['name' => '', 'dob' => '']];
                                }
                            @endphp
                            
                            @foreach($existingBeneficiaries as $index => $beneficiary)
                            <div class="row g-3 mb-2 beneficiary-row" data-index="{{ $index }}">
                                <div class="col-md-5">
                                    <label for="beneficiaries[{{ $index }}][name]" class="form-label {{ $index === 0 ? 'required' : '' }}">
                                        Beneficiary Name {{ $index > 0 ? ($index + 1) : '' }}
                                    </label>
                                    <input type="text" class="form-control @error('beneficiaries.'.$index.'.name') is-invalid @enderror" 
                                           name="beneficiaries[{{ $index }}][name]" 
                                           value="{{ old('beneficiaries.'.$index.'.name', $beneficiary['name'] ?? '') }}" 
                                           placeholder="Full name" {{ $index === 0 ? 'required' : '' }}>
                                    @error('beneficiaries.'.$index.'.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="beneficiaries[{{ $index }}][dob]" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('beneficiaries.'.$index.'.dob') is-invalid @enderror" 
                                           name="beneficiaries[{{ $index }}][dob]" 
                                           value="{{ old('beneficiaries.'.$index.'.dob', $beneficiary['dob'] ?? '') }}">
                                    @error('beneficiaries.'.$index.'.dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    @if($index === 0)
                                        <button type="button" class="btn btn-success w-100" id="add-beneficiary-edit" title="Add Another Beneficiary">
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
                    </div>
                </div>

                <!-- Banking & Payment Information -->
                <div class="card lead-form-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-wallet section-icon"></i>
                            Banking & Payment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="bank_name" class="form-label required">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    id="bank_name" name="bank_name" value="{{ old('bank_name', $lead->bank_name) }}"
                                    placeholder="Bank name" required>
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label for="account_type" class="form-label required">Account Type</label>
                                <select class="form-select @error('account_type') is-invalid @enderror" id="account_type" name="account_type" required>
                                    <option value="">Select account type</option>
                                    <option value="Checking" {{ old('account_type', $lead->account_type) == 'Checking' ? 'selected' : '' }}>Checking</option>
                                    <option value="Savings" {{ old('account_type', $lead->account_type) == 'Savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="Card" {{ old('account_type', $lead->account_type) == 'Card' ? 'selected' : '' }}>Card</option>
                                </select>
                                @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label for="account_number" class="form-label required">Account Number</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                    id="account_number" name="account_number" value="{{ old('account_number', $lead->account_number) }}"
                                    placeholder="Account number" required>
                                @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label for="routing_number" class="form-label required">Routing Number</label>
                                <input type="text" class="form-control @error('routing_number') is-invalid @enderror"
                                    id="routing_number" name="routing_number" value="{{ old('routing_number', $lead->routing_number) }}"
                                    placeholder="9 digits" required>
                                @error('routing_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label for="assigned_validator_id" class="form-label required">Assign to Validator</label>
                                <select class="form-select @error('assigned_validator_id') is-invalid @enderror" id="assigned_validator_id" name="assigned_validator_id" required>
                                    <option value="">Select Validator</option>
                                    @foreach($validators as $validator)
                                        <option value="{{ $validator->id }}" {{ old('assigned_validator_id', $lead->assigned_validator_id) == $validator->id ? 'selected' : '' }}>
                                            {{ $validator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_validator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bank_balance" class="form-label">Bank Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="bank_balance" name="bank_balance"
                                        value="{{ old('bank_balance', $lead->bank_balance) }}" placeholder="Current balance">
                                </div>
                            </div>
                        </div>

                        <h6 class="form-section-title mt-4">Payment Card Information (Optional)</h6>
                        <div class="alert alert-info">
                            <i class="bx bx-lock-alt me-2"></i>
                            <strong>Secure:</strong> All payment card information is encrypted before storage.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control @error('card_number') is-invalid @enderror"
                                    id="card_number" name="card_number" value="{{ old('card_number', $lead->card_number) }}"
                                    placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
                                @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control @error('cvv') is-invalid @enderror"
                                    id="cvv" name="cvv" value="{{ old('cvv', $lead->cvv) }}" placeholder="XXX" maxlength="4">
                                @error('cvv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control @error('expiry_date') is-invalid @enderror"
                                    id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $lead->expiry_date) }}">
                                @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-save me-2"></i>
                                Submit to Validator
                            </button>
                            <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#pendingModal">
                                <i class="bx bx-time me-2"></i>
                                Mark as Pending
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#failedModal">
                                <i class="bx bx-x me-2"></i>
                                Mark as Failed
                            </button>
                            <a href="{{ route('paraguins.closers.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bx bx-arrow-back me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Pending Reason Modal -->
    <div class="modal fade" id="pendingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Select Pending Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('paraguins.closers.mark-pending', $lead->id) }}" id="pendingForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p class="mb-3">Why is this lead being marked as pending?</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="futurePotential" value="Pending:Future Potential" required>
                            <label class="form-check-label" for="futurePotential">
                                <strong>Pending:Future Potential</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="callback" value="Pending:Callback" required>
                            <label class="form-check-label" for="callback">
                                <strong>Pending:Callback</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingBanking" value="Pending:Pending Banking" required>
                            <label class="form-check-label" for="pendingBanking">
                                <strong>Pending:Pending Banking</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingValidation" value="Pending:Pending Validation" required>
                            <label class="form-check-label" for="pendingValidation">
                                <strong>Pending:Pending Validation</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Confirm Pending</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Failed Reason Modal -->
    <div class="modal fade" id="failedModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Select Failure Reason</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('paraguins.closers.mark-failed', $lead->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p class="mb-3">Why is this lead being marked as failed?</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="poa" value="Failed:POA" required>
                            <label class="form-check-label" for="poa">
                                <strong>Failed:POA</strong> - Power of Attorney
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqAge" value="Failed:DNQ-Age" required>
                            <label class="form-check-label" for="dnqAge">
                                <strong>Failed:DNQ-Age</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedSSN" value="Failed:Declined SSN" required>
                            <label class="form-check-label" for="declinedSSN">
                                <strong>Failed:Declined SSN</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="notInterested" value="Failed:Not Interested" required>
                            <label class="form-check-label" for="notInterested">
                                <strong>Failed:Not Interested</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnc" value="Failed:DNC" required>
                            <label class="form-check-label" for="dnc">
                                <strong>Failed:DNC</strong> - Do Not Call
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="cannotAfford" value="Failed:Cannot Afford" required>
                            <label class="form-check-label" for="cannotAfford">
                                <strong>Failed:Cannot Afford</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqHealth" value="Failed:DNQ-Health" required>
                            <label class="form-check-label" for="dnqHealth">
                                <strong>Failed:DNQ-Health</strong> - Health Conditions
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedBanking" value="Failed:Declined Banking" required>
                            <label class="form-check-label" for="declinedBanking">
                                <strong>Failed:Declined Banking</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="noPitch" value="Failed:No Pitch (Not Interested)" required>
                            <label class="form-check-label" for="noPitch">
                                <strong>Failed:No Pitch (Not Interested)</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="noAnswer" value="Failed:No Answer" required>
                            <label class="form-check-label" for="noAnswer">
                                <strong>Failed:No Answer</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Failed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Multiple Beneficiaries Management
        document.addEventListener('DOMContentLoaded', function() {
            let beneficiaryIndex = {{ count($existingBeneficiaries) }};
            
            const addBeneficiaryBtn = document.getElementById('add-beneficiary-edit');
            if (addBeneficiaryBtn) {
                addBeneficiaryBtn.addEventListener('click', function() {
                    const container = document.getElementById('beneficiaries-container');
                    const newRow = document.createElement('div');
                    newRow.className = 'row g-3 mb-2 beneficiary-row';
                    newRow.setAttribute('data-index', beneficiaryIndex);
                    newRow.innerHTML = `
                        <div class="col-md-5">
                            <label for="beneficiaries[${beneficiaryIndex}][name]" class="form-label">Beneficiary Name ${beneficiaryIndex + 1}</label>
                            <input type="text" class="form-control" name="beneficiaries[${beneficiaryIndex}][name]" placeholder="Full name">
                        </div>
                        <div class="col-md-5">
                            <label for="beneficiaries[${beneficiaryIndex}][dob]" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="beneficiaries[${beneficiaryIndex}][dob]">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary">
                                <i class="bx bx-minus"></i> Remove
                            </button>
                        </div>
                    `;
                    container.appendChild(newRow);
                    beneficiaryIndex++;
                    
                    // Attach remove handler
                    newRow.querySelector('.remove-beneficiary').addEventListener('click', function() {
                        newRow.remove();
                    });
                });
            }
            
            // Remove beneficiary (for existing rows)
            document.querySelectorAll('.remove-beneficiary').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.beneficiary-row').remove();
                });
            });
        });

        // Copy form data to pending form before submit
        document.getElementById('pendingForm').addEventListener('submit', function(e) {
            const mainForm = document.querySelector('form[action*="update"]');
            const pendingForm = this;
            
            // Track which radio groups we've already added
            const addedRadios = new Set();
            
            // Copy all inputs from main form to pending form
            mainForm.querySelectorAll('input, select, textarea').forEach(function(input) {
                if (input.name && input.name !== '_token' && input.name !== '_method' && input.name !== 'pending_reason') {
                    // Handle radio buttons specially - only add the checked one per group
                    if (input.type === 'radio') {
                        if (input.checked && !addedRadios.has(input.name)) {
                            addedRadios.add(input.name);
                            let hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = input.name;
                            hidden.value = input.value;
                            pendingForm.appendChild(hidden);
                        }
                    } else {
                        // For non-radio inputs
                        let hidden = pendingForm.querySelector('input[name="' + input.name + '"]');
                        if (!hidden) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = input.name;
                            pendingForm.appendChild(hidden);
                        }
                        
                        if (input.type === 'checkbox') {
                            hidden.value = input.checked ? '1' : '0';
                        } else {
                            hidden.value = input.value || '';
                        }
                    }
                }
            });
        });
    </script>
@endsection
