@extends('layouts.master')

@section('title')
    Edit Lead
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Leads
        @endslot
        @slot('title')
            Edit Lead
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-account-edit me-2"></i>
                        Edit Lead Information
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('leads.update', $lead->id) }}" id="leadForm">
                        @csrf
                        @method('PUT')

                        {{-- Basic Information Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    Basic Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label required">
                                        <i class="mdi mdi-calendar me-1"></i>
                                        Date
                                    </label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        id="date" name="date" value="{{ old('date', $lead->date) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label required">
                                        <i class="mdi mdi-phone me-1"></i>
                                        Phone Number
                                    </label>
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                                        id="phone_number" name="phone_number"
                                        value="{{ old('phone_number', $lead->phone_number) }}"
                                        placeholder="Enter phone number" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cn_name" class="form-label required">
                                        <i class="mdi mdi-account me-1"></i>
                                        Client Name
                                    </label>
                                    <input type="text" class="form-control @error('cn_name') is-invalid @enderror"
                                        id="cn_name" name="cn_name" value="{{ old('cn_name', $lead->cn_name) }}"
                                        placeholder="Enter client name" required>
                                    @error('cn_name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label required">
                                        <i class="mdi mdi-cake me-1"></i>
                                        Date of Birth
                                    </label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                        id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $lead->date_of_birth) }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="height_weight" class="form-label">
                                        <i class="mdi mdi-human-male-height me-1"></i>
                                        Height/Weight
                                    </label>
                                    <input type="text" class="form-control @error('height_weight') is-invalid @enderror"
                                        id="height_weight" name="height_weight"
                                        value="{{ old('height_weight', $lead->height_weight) }}"
                                        placeholder="e.g., 5'10\" / 180 lbs">
                                    @error('height_weight')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_place" class="form-label">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        Birth Place
                                    </label>
                                    <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                                        id="birth_place" name="birth_place"
                                        value="{{ old('birth_place', $lead->birth_place) }}"
                                        placeholder="Enter birth place">
                                    @error('birth_place')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ssn" class="form-label">
                                        <i class="mdi mdi-card-account-details me-1"></i>
                                        SSN
                                    </label>
                                    <input type="text" class="form-control @error('ssn') is-invalid @enderror"
                                        id="ssn" name="ssn" value="{{ old('ssn', $lead->ssn) }}"
                                        placeholder="XXX-XX-XXXX">
                                    @error('ssn')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="source" class="form-label">
                                        <i class="mdi mdi-source-branch me-1"></i>
                                        Source
                                    </label>
                                    <input type="text" class="form-control @error('source') is-invalid @enderror"
                                        id="source" name="source" value="{{ old('source', $lead->source) }}"
                                        placeholder="Enter lead source">
                                    @error('source')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        <i class="mdi mdi-home me-1"></i>
                                        Address
                                    </label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                        name="address" rows="3"
                                        placeholder="Enter complete address">{{ old('address', $lead->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Medical Information Section --}}
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-hospital-box me-1"></i>
                                    Medical Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medical_issue" class="form-label">
                                        <i class="mdi mdi-hospital me-1"></i>
                                        Medical Issue
                                    </label>
                                    <textarea class="form-control @error('medical_issue') is-invalid @enderror"
                                        id="medical_issue" name="medical_issue" rows="3"
                                        placeholder="Enter medical issues">{{ old('medical_issue', $lead->medical_issue) }}</textarea>
                                    @error('medical_issue')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medications" class="form-label">
                                        <i class="mdi mdi-pill me-1"></i>
                                        Medications
                                    </label>
                                    <textarea class="form-control @error('medications') is-invalid @enderror"
                                        id="medications" name="medications" rows="3"
                                        placeholder="Enter medications">{{ old('medications', $lead->medications) }}</textarea>
                                    @error('medications')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="doctor_name" class="form-label">
                                        <i class="mdi mdi-stethoscope me-1"></i>
                                        Doctor Name
                                    </label>
                                    <input type="text" class="form-control @error('doctor_name') is-invalid @enderror"
                                        id="doctor_name" name="doctor_name"
                                        value="{{ old('doctor_name', $lead->doctor_name) }}"
                                        placeholder="Enter doctor name">
                                    @error('doctor_name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Insurance Information Section --}}
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-shield-account me-1"></i>
                                    Insurance Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="carrier_name" class="form-label">
                                        <i class="mdi mdi-truck me-1"></i>
                                        Carrier Name
                                    </label>
                                    <input type="text" class="form-control @error('carrier_name') is-invalid @enderror"
                                        id="carrier_name" name="carrier_name"
                                        value="{{ old('carrier_name', $lead->carrier_name) }}"
                                        placeholder="Enter carrier name">
                                    @error('carrier_name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="policy_type" class="form-label">
                                        <i class="mdi mdi-file-document me-1"></i>
                                        Policy Type
                                    </label>
                                    <input type="text" class="form-control @error('policy_type') is-invalid @enderror"
                                        id="policy_type" name="policy_type"
                                        value="{{ old('policy_type', $lead->policy_type) }}"
                                        placeholder="Enter policy type">
                                    @error('policy_type')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="coverage_amount" class="form-label">
                                        <i class="mdi mdi-currency-usd me-1"></i>
                                        Coverage Amount
                                    </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('coverage_amount') is-invalid @enderror"
                                        id="coverage_amount" name="coverage_amount"
                                        value="{{ old('coverage_amount', $lead->coverage_amount) }}"
                                        placeholder="Enter coverage amount">
                                    @error('coverage_amount')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="monthly_premium" class="form-label">
                                        <i class="mdi mdi-cash-multiple me-1"></i>
                                        Monthly Premium
                                    </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('monthly_premium') is-invalid @enderror"
                                        id="monthly_premium" name="monthly_premium"
                                        value="{{ old('monthly_premium', $lead->monthly_premium) }}"
                                        placeholder="Enter monthly premium">
                                    @error('monthly_premium')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="initial_draft_date" class="form-label">
                                        <i class="mdi mdi-calendar-clock me-1"></i>
                                        Initial Draft Date
                                    </label>
                                    <input type="date"
                                        class="form-control @error('initial_draft_date') is-invalid @enderror"
                                        id="initial_draft_date" name="initial_draft_date"
                                        value="{{ old('initial_draft_date', $lead->initial_draft_date) }}">
                                    @error('initial_draft_date')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="beneficiary" class="form-label">
                                        <i class="mdi mdi-account-heart me-1"></i>
                                        Beneficiary
                                    </label>
                                    <input type="text" class="form-control @error('beneficiary') is-invalid @enderror"
                                        id="beneficiary" name="beneficiary"
                                        value="{{ old('beneficiary', $lead->beneficiary) }}"
                                        placeholder="Enter beneficiary name">
                                    @error('beneficiary')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Banking Information Section --}}
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-bank me-1"></i>
                                    Banking Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">
                                        <i class="mdi mdi-bank me-1"></i>
                                        Bank Name
                                    </label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                        id="bank_name" name="bank_name"
                                        value="{{ old('bank_name', $lead->bank_name) }}"
                                        placeholder="Enter bank name">
                                    @error('bank_name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_type" class="form-label">
                                        <i class="mdi mdi-credit-card me-1"></i>
                                        Account Type
                                    </label>
                                    <input type="text" class="form-control @error('account_type') is-invalid @enderror"
                                        id="account_type" name="account_type"
                                        value="{{ old('account_type', $lead->account_type) }}"
                                        placeholder="e.g., Checking, Savings">
                                    @error('account_type')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="routing_number" class="form-label">
                                        <i class="mdi mdi-numeric me-1"></i>
                                        Routing Number
                                    </label>
                                    <input type="text" class="form-control @error('routing_number') is-invalid @enderror"
                                        id="routing_number" name="routing_number"
                                        value="{{ old('routing_number', $lead->routing_number) }}"
                                        placeholder="Enter routing number">
                                    @error('routing_number')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_balance" class="form-label">
                                        <i class="mdi mdi-cash me-1"></i>
                                        Bank Balance
                                    </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('bank_balance') is-invalid @enderror"
                                        id="bank_balance" name="bank_balance"
                                        value="{{ old('bank_balance', $lead->bank_balance) }}"
                                        placeholder="Enter bank balance">
                                    @error('bank_balance')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="account_verified_by" class="form-label">
                                        <i class="mdi mdi-account-check me-1"></i>
                                        Account Verified By
                                    </label>
                                    <input type="text"
                                        class="form-control @error('account_verified_by') is-invalid @enderror"
                                        id="account_verified_by" name="account_verified_by"
                                        value="{{ old('account_verified_by', $lead->account_verified_by) }}"
                                        placeholder="Enter verifier name">
                                    @error('account_verified_by')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Sales Information Section --}}
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-account-tie me-1"></i>
                                    Sales Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="closer_name" class="form-label">
                                        <i class="mdi mdi-account-tie me-1"></i>
                                        Closer Name
                                    </label>
                                    <input type="text" class="form-control @error('closer_name') is-invalid @enderror"
                                        id="closer_name" name="closer_name"
                                        value="{{ old('closer_name', $lead->closer_name) }}"
                                        placeholder="Enter closer name">
                                    @error('closer_name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status Information Section --}}
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-clipboard-check me-1"></i>
                                    Status Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label required">
                                        <i class="mdi mdi-tag me-1"></i>
                                        Status
                                    </label>
                                    <select id="status" name="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="">Select status...</option>
                                        <option value="pending"
                                            {{ old('status', $lead->status) == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="forwarded"
                                            {{ old('status', $lead->status) == 'forwarded' ? 'selected' : '' }}>Forwarded
                                        </option>
                                        <option value="active"
                                            {{ old('status', $lead->status) == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="cancelled"
                                            {{ old('status', $lead->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                                        </option>
                                        <option value="completed"
                                            {{ old('status', $lead->status) == 'completed' ? 'selected' : '' }}>Completed
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="status_notes" class="form-label">
                                        <i class="mdi mdi-note-text me-1"></i>
                                        Status Notes
                                    </label>
                                    <textarea class="form-control @error('status_notes') is-invalid @enderror"
                                        id="status_notes" name="status_notes" rows="4"
                                        placeholder="Enter status notes or comments">{{ old('status_notes', $lead->status_notes) }}</textarea>
                                    @error('status_notes')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Submit Section --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i>
                                        Cancel
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="mdi mdi-refresh me-1"></i>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i>
                                        Update Lead
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Function to reset form
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
                document.getElementById('leadForm').reset();
            }
        }

        // Form validation
        document.getElementById('leadForm').addEventListener('submit', function(e) {
            const requiredFields = ['date', 'phone_number', 'cn_name', 'date_of_birth', 'status'];
            let isValid = true;

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
        });

        // SSN formatting
        document.getElementById('ssn').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) value = value.slice(0, 9);

            if (value.length > 5) {
                value = value.slice(0, 3) + '-' + value.slice(3, 5) + '-' + value.slice(5);
            } else if (value.length > 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }

            e.target.value = value;
        });

        // Phone number formatting
        document.getElementById('phone_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);

            if (value.length > 6) {
                value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6);
            } else if (value.length > 3) {
                value = '(' + value.slice(0, 3) + ') ' + value.slice(3);
            } else if (value.length > 0) {
                value = '(' + value;
            }

            e.target.value = value;
        });
    </script>
@endsection

@section('css')
    <style>
        .required::after {
            content: " *";
            color: red;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .text-primary {
            color: #667eea !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .border-bottom {
            border-bottom: 2px solid #e9ecef !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
@endsection
