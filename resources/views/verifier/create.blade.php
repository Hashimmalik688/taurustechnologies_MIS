@extends('layouts.master')

@section('title')
    Verifier Submission
@endsection

@section('css')
<style>
    /* ── Verifier Submission Form — sl-* Design System ── */

    /* Top bar */
    .sl-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; flex-wrap: wrap; gap: .75rem;
    }
    .sl-topbar-left { display: flex; align-items: center; gap: .75rem; }
    .sl-page-title {
        font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-page-title i { color: #d4af37; font-size: 1.2rem; }
    .sl-topbar-right { display: flex; align-items: center; gap: .5rem; }

    /* Card */
    .sl-card {
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
    }
    .sl-card-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: .85rem 1.25rem;
        background: linear-gradient(135deg, rgba(212,175,55,.08), rgba(212,175,55,.02));
        border-bottom: 1px solid rgba(212,175,55,.12);
    }
    .sl-card-header h4 {
        font-size: .88rem; font-weight: 700; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-card-header h4 i { color: #d4af37; }
    .sl-card-body { padding: 1.5rem; }

    /* Section headers */
    .sl-section-title {
        font-size: .82rem; font-weight: 700; color: #d4af37;
        margin: 0 0 1rem 0; padding-bottom: .5rem;
        border-bottom: 1px solid rgba(212,175,55,.12);
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-section-title i { font-size: .95rem; }

    /* Form controls */
    .sl-form-label {
        font-size: .72rem; font-weight: 700; color: #475569;
        text-transform: uppercase; letter-spacing: .3px;
        margin-bottom: .35rem; display: block;
    }
    .sl-form-label .req { color: #ef4444; margin-left: 1px; }

    .sl-form-input, .sl-form-select {
        width: 100%;
        font-size: .8rem; font-weight: 500;
        padding: .55rem .85rem;
        border-radius: 22px !important;
        border: 1px solid rgba(0,0,0,.09) !important;
        background: #fff; color: #334155;
        outline: none;
        transition: all .2s;
    }
    .sl-form-input:focus, .sl-form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212,175,55,.12);
    }
    .sl-form-input::placeholder { color: #94a3b8; font-weight: 400; }

    .sl-form-input[readonly] {
        background: rgba(248,250,252,.8);
        color: #64748b;
        cursor: not-allowed;
    }

    .sl-form-select {
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        padding-right: 2rem;
        cursor: pointer;
    }

    .sl-form-group { margin-bottom: 1.1rem; }
    .sl-form-group .invalid-feedback { font-size: .7rem; margin-top: .25rem; }
    .sl-form-input.is-invalid, .sl-form-select.is-invalid {
        border-color: #ef4444 !important;
    }

    /* Divider */
    .sl-divider {
        border: none; border-top: 1px solid rgba(0,0,0,.05);
        margin: 1.5rem 0;
    }

    /* Submit button */
    .sl-btn-submit {
        display: inline-flex; align-items: center; gap: .4rem;
        background: linear-gradient(135deg, #d4af37, #b8941f);
        color: #0f172a; font-size: .82rem; font-weight: 700;
        padding: .6rem 1.5rem; border-radius: 22px;
        border: none; cursor: pointer;
        box-shadow: 0 4px 15px rgba(212,175,55,.3);
        transition: all .2s;
    }
    .sl-btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(212,175,55,.4);
    }
    .sl-btn-submit:active { transform: translateY(0); }

    .sl-btn-dash {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .75rem; font-weight: 600; color: #475569;
        padding: .42rem .85rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; text-decoration: none;
        transition: all .15s;
    }
    .sl-btn-dash:hover { border-color: #d4af37; color: #92760d; }

    /* Success alert */
    .sl-alert-success {
        display: flex; align-items: center; gap: .5rem;
        padding: .65rem 1rem; border-radius: 14px;
        background: rgba(16,185,129,.08);
        border: 1px solid rgba(16,185,129,.15);
        color: #065f46; font-size: .8rem; font-weight: 600;
        margin-bottom: 1rem;
    }
    .sl-alert-success i { color: #10b981; font-size: 1.1rem; }
    .sl-alert-close {
        margin-left: auto; background: none; border: none;
        color: #065f46; cursor: pointer; font-size: 1rem;
        opacity: .6; transition: opacity .15s;
    }
    .sl-alert-close:hover { opacity: 1; }

    /* ── Dark mode ── */
    [data-theme="dark"] .sl-page-title { color: #e2e8f0; }
    [data-theme="dark"] .sl-card {
        background: rgba(30,41,59,.85); border-color: rgba(255,255,255,.06);
        box-shadow: 0 4px 24px rgba(0,0,0,.25);
    }
    [data-theme="dark"] .sl-card-header {
        background: linear-gradient(135deg, rgba(212,175,55,.06), rgba(212,175,55,.02));
        border-color: rgba(212,175,55,.1);
    }
    [data-theme="dark"] .sl-card-header h4 { color: #e2e8f0; }
    [data-theme="dark"] .sl-section-title { border-color: rgba(212,175,55,.1); }
    [data-theme="dark"] .sl-form-label { color: #94a3b8; }
    [data-theme="dark"] .sl-form-input,
    [data-theme="dark"] .sl-form-select {
        background: rgba(15,23,42,.6) !important; border-color: rgba(255,255,255,.1) !important;
        color: #e2e8f0;
    }
    [data-theme="dark"] .sl-form-input::placeholder { color: #475569; }
    [data-theme="dark"] .sl-form-input[readonly] {
        background: rgba(15,23,42,.4) !important; color: #64748b;
    }
    [data-theme="dark"] .sl-form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E") !important;
    }
    [data-theme="dark"] .sl-form-input:focus,
    [data-theme="dark"] .sl-form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212,175,55,.15);
    }
    [data-theme="dark"] .sl-divider { border-color: rgba(255,255,255,.06); }
    [data-theme="dark"] .sl-btn-dash {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
    }
    [data-theme="dark"] .sl-alert-success {
        background: rgba(16,185,129,.1); border-color: rgba(16,185,129,.15); color: #6ee7b7;
    }
    [data-theme="dark"] .sl-alert-success i { color: #34d399; }
    [data-theme="dark"] .sl-alert-close { color: #6ee7b7; }
</style>
@endsection

@section('content')
    {{-- Top bar --}}
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h2 class="sl-page-title"><i class="bx bx-clipboard"></i> {{ ucfirst($team ?? 'peregrine') }} — New Submission</h2>
        </div>
        <div class="sl-topbar-right">
            <a href="{{ route('verifier.dashboard') }}" class="sl-btn-dash">
                <i class="bx bx-list-ul"></i> My Dashboard
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="sl-alert-success">
            <i class="bx bx-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="sl-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <div class="sl-card">
        <div class="sl-card-header">
            <h4><i class="bx bx-edit-alt"></i> Verification Form</h4>
        </div>
        <div class="sl-card-body">
            <form method="POST" action="{{ isset($team) ? route('verifier.store.team', ['team' => $team]) : route('verifier.store') }}">
                @csrf
                <input type="hidden" name="team" value="{{ $team ?? 'peregrine' }}">

                <div class="row">
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Date <span class="req">*</span></label>
                        <input type="date" name="date" class="sl-form-input @error('date') is-invalid @enderror" value="{{ old('date', now()->format('Y-m-d')) }}" readonly required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Verifier Name <span class="req">*</span></label>
                        <input type="text" class="sl-form-input" value="{{ auth()->user()->name }}" readonly tabindex="-1">
                        <input type="hidden" name="verifier_name" value="{{ auth()->user()->name }}">
                    </div>
                    <div class="col-md-5 sl-form-group">
                        <label class="sl-form-label">Live Closer <span class="req">*</span></label>
                        <select name="closer_id" class="sl-form-select @error('closer_id') is-invalid @enderror" required>
                            <option value="">Select Live Closer</option>
                            @foreach($closers as $closer)
                                <option value="{{ $closer->id }}" {{ old('closer_id') == $closer->id ? 'selected' : '' }}>{{ $closer->name }}</option>
                            @endforeach
                        </select>
                        @error('closer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="sl-divider">
                <h5 class="sl-section-title"><i class="bx bx-user"></i> Customer Information</h5>

                <div class="row">
                    <div class="col-md-6 sl-form-group">
                        <label class="sl-form-label">Customer Name <span class="req">*</span></label>
                        <input type="text" name="cn_name" class="sl-form-input @error('cn_name') is-invalid @enderror" value="{{ old('cn_name') }}" placeholder="Enter full name" required>
                        @error('cn_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Date of Birth <span class="req">*</span></label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="sl-form-input @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Age <span class="req">*</span></label>
                        <input type="number" id="age" name="age" class="sl-form-input @error('age') is-invalid @enderror" value="{{ old('age') }}" placeholder="Auto-calculated" min="18" max="100" readonly tabindex="-1" required>
                        @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Gender <span class="req">*</span></label>
                        <select name="gender" class="sl-form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Phone Number <span class="req">*</span></label>
                        <input type="tel" name="phone_number" class="sl-form-input @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" placeholder="e.g., 555-123-4567" required>
                        @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Account Type <span class="req">*</span></label>
                        <select name="account_type" class="sl-form-select @error('account_type') is-invalid @enderror" required>
                            <option value="">Select Account Type</option>
                            <option value="Checking" {{ old('account_type') == 'Checking' ? 'selected' : '' }}>Checking</option>
                            <option value="Savings" {{ old('account_type') == 'Savings' ? 'selected' : '' }}>Savings</option>
                            <option value="Card" {{ old('account_type') == 'Card' ? 'selected' : '' }}>Card</option>
                        </select>
                        @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8 sl-form-group">
                        <label class="sl-form-label">Address <span class="req">*</span></label>
                        <input type="text" name="address" class="sl-form-input @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Street address" required>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">State <span class="req">*</span></label>
                        <select name="state" class="sl-form-select @error('state') is-invalid @enderror" required>
                            <option value="">State</option>
                            <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alabama (AL)</option>
                            <option value="AK" {{ old('state') == 'AK' ? 'selected' : '' }}>Alaska (AK)</option>
                            <option value="AZ" {{ old('state') == 'AZ' ? 'selected' : '' }}>Arizona (AZ)</option>
                            <option value="AR" {{ old('state') == 'AR' ? 'selected' : '' }}>Arkansas (AR)</option>
                            <option value="CA" {{ old('state') == 'CA' ? 'selected' : '' }}>California (CA)</option>
                            <option value="CO" {{ old('state') == 'CO' ? 'selected' : '' }}>Colorado (CO)</option>
                            <option value="CT" {{ old('state') == 'CT' ? 'selected' : '' }}>Connecticut (CT)</option>
                            <option value="DE" {{ old('state') == 'DE' ? 'selected' : '' }}>Delaware (DE)</option>
                            <option value="DC" {{ old('state') == 'DC' ? 'selected' : '' }}>District of Columbia (DC)</option>
                            <option value="FL" {{ old('state') == 'FL' ? 'selected' : '' }}>Florida (FL)</option>
                            <option value="GA" {{ old('state') == 'GA' ? 'selected' : '' }}>Georgia (GA)</option>
                            <option value="HI" {{ old('state') == 'HI' ? 'selected' : '' }}>Hawaii (HI)</option>
                            <option value="ID" {{ old('state') == 'ID' ? 'selected' : '' }}>Idaho (ID)</option>
                            <option value="IL" {{ old('state') == 'IL' ? 'selected' : '' }}>Illinois (IL)</option>
                            <option value="IN" {{ old('state') == 'IN' ? 'selected' : '' }}>Indiana (IN)</option>
                            <option value="IA" {{ old('state') == 'IA' ? 'selected' : '' }}>Iowa (IA)</option>
                            <option value="KS" {{ old('state') == 'KS' ? 'selected' : '' }}>Kansas (KS)</option>
                            <option value="KY" {{ old('state') == 'KY' ? 'selected' : '' }}>Kentucky (KY)</option>
                            <option value="LA" {{ old('state') == 'LA' ? 'selected' : '' }}>Louisiana (LA)</option>
                            <option value="ME" {{ old('state') == 'ME' ? 'selected' : '' }}>Maine (ME)</option>
                            <option value="MD" {{ old('state') == 'MD' ? 'selected' : '' }}>Maryland (MD)</option>
                            <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Massachusetts (MA)</option>
                            <option value="MI" {{ old('state') == 'MI' ? 'selected' : '' }}>Michigan (MI)</option>
                            <option value="MN" {{ old('state') == 'MN' ? 'selected' : '' }}>Minnesota (MN)</option>
                            <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mississippi (MS)</option>
                            <option value="MO" {{ old('state') == 'MO' ? 'selected' : '' }}>Missouri (MO)</option>
                            <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Montana (MT)</option>
                            <option value="NE" {{ old('state') == 'NE' ? 'selected' : '' }}>Nebraska (NE)</option>
                            <option value="NV" {{ old('state') == 'NV' ? 'selected' : '' }}>Nevada (NV)</option>
                            <option value="NH" {{ old('state') == 'NH' ? 'selected' : '' }}>New Hampshire (NH)</option>
                            <option value="NJ" {{ old('state') == 'NJ' ? 'selected' : '' }}>New Jersey (NJ)</option>
                            <option value="NM" {{ old('state') == 'NM' ? 'selected' : '' }}>New Mexico (NM)</option>
                            <option value="NY" {{ old('state') == 'NY' ? 'selected' : '' }}>New York (NY)</option>
                            <option value="NC" {{ old('state') == 'NC' ? 'selected' : '' }}>North Carolina (NC)</option>
                            <option value="ND" {{ old('state') == 'ND' ? 'selected' : '' }}>North Dakota (ND)</option>
                            <option value="OH" {{ old('state') == 'OH' ? 'selected' : '' }}>Ohio (OH)</option>
                            <option value="OK" {{ old('state') == 'OK' ? 'selected' : '' }}>Oklahoma (OK)</option>
                            <option value="OR" {{ old('state') == 'OR' ? 'selected' : '' }}>Oregon (OR)</option>
                            <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pennsylvania (PA)</option>
                            <option value="RI" {{ old('state') == 'RI' ? 'selected' : '' }}>Rhode Island (RI)</option>
                            <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>South Carolina (SC)</option>
                            <option value="SD" {{ old('state') == 'SD' ? 'selected' : '' }}>South Dakota (SD)</option>
                            <option value="TN" {{ old('state') == 'TN' ? 'selected' : '' }}>Tennessee (TN)</option>
                            <option value="TX" {{ old('state') == 'TX' ? 'selected' : '' }}>Texas (TX)</option>
                            <option value="UT" {{ old('state') == 'UT' ? 'selected' : '' }}>Utah (UT)</option>
                            <option value="VT" {{ old('state') == 'VT' ? 'selected' : '' }}>Vermont (VT)</option>
                            <option value="VA" {{ old('state') == 'VA' ? 'selected' : '' }}>Virginia (VA)</option>
                            <option value="WA" {{ old('state') == 'WA' ? 'selected' : '' }}>Washington (WA)</option>
                            <option value="WV" {{ old('state') == 'WV' ? 'selected' : '' }}>West Virginia (WV)</option>
                            <option value="WI" {{ old('state') == 'WI' ? 'selected' : '' }}>Wisconsin (WI)</option>
                            <option value="WY" {{ old('state') == 'WY' ? 'selected' : '' }}>Wyoming (WY)</option>
                        </select>
                        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Zip Code <span class="req">*</span></label>
                        <input type="text" name="zip_code" class="sl-form-input @error('zip_code') is-invalid @enderror" value="{{ old('zip_code') }}" placeholder="Zip" maxlength="10" required>
                        @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="sl-btn-submit">
                        <i class="bx bx-send"></i> Submit to Closer
                    </button>
                </div>
            </form>
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
