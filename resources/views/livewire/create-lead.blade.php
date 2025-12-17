<div>
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
        .form-control, .form-select {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.15);
        }
        .row-spacing {
            margin-bottom: 20px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #d4af37 0%, #f4d77a 100%);
            border: none;
            color: #1a1a1a;
            font-weight: 600;
            padding: 12px 40px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            color: #1a1a1a;
        }
        .form-helper-text {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 4px;
        }
        .section-icon {
            color: #d4af37;
            margin-right: 8px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <!-- Personal Information -->
            <div class="card lead-form-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bx bx-user section-icon"></i>
                        Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Basic Details -->
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        id="date" wire:model="date">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="cn_name" class="form-label required">Client Full Name</label>
                                    <input type="text" class="form-control @error('cn_name') is-invalid @enderror"
                                        id="cn_name" wire:model="cn_name" placeholder="Enter full legal name">
                                    @error('cn_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label required">Phone Number</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                        id="phone_number" wire:model="phone_number" placeholder="+1 (555) 000-0000">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label required">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                        id="date_of_birth" wire:model="date_of_birth">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror"
                                        id="gender" wire:model="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ssn" class="form-label required">Social Security Number</label>
                                    <input type="text" class="form-control @error('ssn') is-invalid @enderror"
                                        id="ssn" wire:model="ssn" placeholder="XXX-XX-XXXX">
                                    @error('ssn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address & Location -->
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="address" class="form-label required">Full Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                        id="address" wire:model="address" rows="2"
                                        placeholder="Street address, city, state, ZIP code"></textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="birth_place" class="form-label">Birth Place</label>
                                    <input type="text" class="form-control" id="birth_place"
                                        wire:model="birth_place" placeholder="City, State">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Health Information -->
                    <h6 class="form-section-title mt-4">Health Information</h6>
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="height_weight" class="form-label">Height & Weight</label>
                                    <input type="text" class="form-control" id="height_weight"
                                        wire:model="height_weight" placeholder="5'10&quot;, 180 lbs">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="smoker" class="form-label">Smoker Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="smoker"
                                            wire:model="smoker">
                                        <label class="form-check-label" for="smoker">
                                            {{ $smoker ? 'Yes' : 'No' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="doctor_name" class="form-label">Primary Care Physician</label>
                                    <input type="text" class="form-control" id="doctor_name"
                                        wire:model="doctor_name" placeholder="Dr. Name, Practice">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medical_issue" class="form-label">Medical Conditions</label>
                                    <textarea class="form-control" id="medical_issue"
                                        wire:model="medical_issue" rows="2"
                                        placeholder="List any pre-existing conditions or health issues"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medications" class="form-label">Current Medications</label>
                                    <textarea class="form-control" id="medications"
                                        wire:model="medications" rows="2"
                                        placeholder="List all medications currently taking"></textarea>
                                </div>
                            </div>
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
                    <!-- Policy Details -->
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="carrier_name" class="form-label">Carrier Name</label>
                                    <input type="text" class="form-control" id="carrier_name"
                                        wire:model="carrier_name" placeholder="Insurance company name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="policy_type" class="form-label required">Policy Type</label>
                                    <input type="text" class="form-control @error('policy_type') is-invalid @enderror"
                                        id="policy_type" wire:model="policy_type" placeholder="Term Life, Whole Life, etc.">
                                    @error('policy_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="initial_draft_date" class="form-label required">Initial Draft Date</label>
                                    <input type="date" class="form-control @error('initial_draft_date') is-invalid @enderror"
                                        id="initial_draft_date" wire:model="initial_draft_date">
                                    @error('initial_draft_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coverage & Premium -->
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="coverage_amount" class="form-label required">Coverage Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="1000" class="form-control @error('coverage_amount') is-invalid @enderror"
                                            id="coverage_amount" wire:model="coverage_amount" placeholder="250000">
                                        @error('coverage_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="monthly_premium" class="form-label required">Monthly Premium</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('monthly_premium') is-invalid @enderror"
                                            id="monthly_premium" wire:model="monthly_premium" placeholder="125.50">
                                        @error('monthly_premium')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="source" class="form-label">Lead Source</label>
                                    <input type="text" class="form-control" id="source" wire:model="source"
                                        placeholder="Referral, Web, Cold Call, etc.">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficiary Information -->
                    <h6 class="form-section-title mt-4">Beneficiary Information</h6>
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="beneficiary" class="form-label required">Beneficiary Name</label>
                                    <input type="text" class="form-control @error('beneficiary') is-invalid @enderror"
                                        id="beneficiary" wire:model="beneficiary" placeholder="Full legal name">
                                    @error('beneficiary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="beneficiary_dob" class="form-label">Beneficiary Date of Birth</label>
                                    <input type="date" class="form-control @error('beneficiary_dob') is-invalid @enderror"
                                        id="beneficiary_dob" wire:model="beneficiary_dob">
                                    @error('beneficiary_dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
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
                    <!-- Bank Details -->
                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label required">Bank Name</label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                        id="bank_name" wire:model="bank_name" placeholder="Bank name">
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="account_type" class="form-label required">Account Type</label>
                                    <select class="form-select @error('account_type') is-invalid @enderror"
                                        id="account_type" wire:model="account_type">
                                        <option value="">Select account type</option>
                                        <option value="Checking">Checking</option>
                                        <option value="Savings">Savings</option>
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="routing_number" class="form-label required">Routing Number</label>
                                    <input type="text" class="form-control @error('routing_number') is-invalid @enderror"
                                        id="routing_number" wire:model="routing_number" placeholder="9 digits">
                                    @error('routing_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_verified_by" class="form-label">Account Verified By</label>
                                    <input type="text" class="form-control" id="account_verified_by"
                                        wire:model="account_verified_by" placeholder="Verifier name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_balance" class="form-label">Bank Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="bank_balance"
                                            wire:model="bank_balance" placeholder="Current balance">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Information (Optional & Encrypted) -->
                    <h6 class="form-section-title mt-4">Payment Card Information (Optional)</h6>
                    <div class="alert alert-info" style="background-color: #f0f8ff; border-color: #d4af37; color: #1a1a1a;">
                        <i class="bx bx-lock-alt me-2"></i>
                        <strong>Secure:</strong> All payment card information is encrypted before storage.
                    </div>

                    <div class="row-spacing">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control @error('card_number') is-invalid @enderror"
                                        id="card_number" wire:model="card_number"
                                        placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
                                    @error('card_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control @error('cvv') is-invalid @enderror"
                                        id="cvv" wire:model="cvv" placeholder="XXX" maxlength="4">
                                    @error('cvv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control @error('expiry_date') is-invalid @enderror"
                                        id="expiry_date" wire:model="expiry_date"
                                        placeholder="MM/YYYY" maxlength="7">
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Information -->
            <div class="card lead-form-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bx bx-user-check section-icon"></i>
                        Assignment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="closer_name" class="form-label">Assigned Closer</label>
                                <input type="text" class="form-control" id="closer_name" wire:model="closer_name"
                                    placeholder="Enter closer name">
                                <small class="form-helper-text">The sales representative assigned to this lead</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 pt-3 border-top">
                        <button type="button" wire:click="save" class="btn btn-submit">
                            <i class="bx bx-save me-2"></i>
                            Create Lead
                        </button>
                        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bx bx-x me-1"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>