@extends('layouts.master')

@section('title')
    Verifier Submission
@endsection

@section('css')
<style>
    .form-label.required::after {
        content: ' *';
        color: red;
    }
    input[readonly] {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Verification @endslot
        @slot('title') {{ ucfirst($team ?? 'paraguins') }} - New Submission @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4 class="card-title mb-0 text-white"><i class="bx bx-clipboard me-2"></i>Verification Form</h4>
                    <a href="{{ route('verifier.dashboard') }}" class="btn btn-light btn-sm">
                        <i class="bx bx-list-ul me-1"></i> My Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($team) ? route('verifier.store.team', ['team' => $team]) : route('verifier.store') }}">
                        @csrf
                        <input type="hidden" name="team" value="{{ $team ?? 'paraguins' }}">
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label required">Date</label>
                                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->format('Y-m-d')) }}" readonly required>
                                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Verifier Name</label>
                                <input type="text" name="verifier_name" class="form-control" value="{{ auth()->user()->name }}" readonly tabindex="-1" required>
                                <input type="hidden" name="verifier_name" value="{{ auth()->user()->name }}">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label required">Live Closer</label>
                                <select name="closer_id" class="form-select @error('closer_id') is-invalid @enderror" required>
                                    <option value="">Select Live Closer</option>
                                    @foreach($closers as $closer)
                                        <option value="{{ $closer->id }}" {{ old('closer_id') == $closer->id ? 'selected' : '' }}>{{ $closer->name }}</option>
                                    @endforeach
                                </select>
                                @error('closer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3 text-primary"><i class="bx bx-user me-2"></i>Customer Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Customer Name</label>
                                <input type="text" name="cn_name" class="form-control @error('cn_name') is-invalid @enderror" value="{{ old('cn_name') }}" placeholder="Enter full name" required>
                                @error('cn_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Age</label>
                                <input type="number" id="age" name="age" class="form-control @error('age') is-invalid @enderror" value="{{ old('age') }}" placeholder="Auto-calculated" min="18" max="100" readonly tabindex="-1" required>
                                @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Gender</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" placeholder="e.g., 555-123-4567" required>
                                @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Account Type</label>
                                <select name="account_type" class="form-select @error('account_type') is-invalid @enderror" required>
                                    <option value="">Select Account Type</option>
                                    <option value="Checking" {{ old('account_type') == 'Checking' ? 'selected' : '' }}>Checking</option>
                                    <option value="Savings" {{ old('account_type') == 'Savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="Card" {{ old('account_type') == 'Card' ? 'selected' : '' }}>Card</option>
                                </select>
                                @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label required">Address</label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Street address" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label required">State</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" placeholder="State" maxlength="2" required>
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label required">Zip Code</label>
                                <input type="text" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror" value="{{ old('zip_code') }}" placeholder="Zip" maxlength="10" required>
                                @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-send me-1"></i> Submit to Closer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Auto-calculate age from date of birth
    document.getElementById('date_of_birth').addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        
        if (!this.value || dob > today) {
            document.getElementById('age').value = '';
            return;
        }
        
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        // Adjust age if birthday hasn't occurred this year yet
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        document.getElementById('age').value = age;
    });
    
    // Trigger calculation on page load if DOB exists
    document.addEventListener('DOMContentLoaded', function() {
        const dobField = document.getElementById('date_of_birth');
        if (dobField.value) {
            dobField.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
