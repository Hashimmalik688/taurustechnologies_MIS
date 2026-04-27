@extends('layouts.master')

@section('title')
    PJC Submission
@endsection

@section('css')
<style>
    /* ── PJC Submission Form — sl-* Design System ── */

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
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: #e2e8f0; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card {
        background: rgba(30,41,59,.85); border-color: rgba(255,255,255,.06);
        box-shadow: 0 4px 24px rgba(0,0,0,.25);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card-header {
        background: linear-gradient(135deg, rgba(212,175,55,.06), rgba(212,175,55,.02));
        border-color: rgba(212,175,55,.1);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card-header h4 { color: #e2e8f0; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-section-title { border-color: rgba(212,175,55,.1); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-label { color: #94a3b8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-select {
        background: rgba(15,23,42,.6) !important; border-color: rgba(255,255,255,.1) !important;
        color: #e2e8f0;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input::placeholder { color: #475569; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input[readonly] {
        background: rgba(15,23,42,.4) !important; color: #64748b;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E") !important;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-input:focus,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212,175,55,.15);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-divider { border-color: rgba(255,255,255,.06); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-btn-dash {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-alert-success {
        background: rgba(16,185,129,.1); border-color: rgba(16,185,129,.15); color: #6ee7b7;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-alert-success i { color: #34d399; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-alert-close { color: #6ee7b7; }
</style>
@endsection

@section('content')
    {{-- Top bar --}}
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h2 class="sl-page-title"><i class="bx bx-clipboard"></i> Peregrine — PJC New Submission</h2>
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
            <h4><i class="bx bx-edit-alt"></i> PJC Submission Form</h4>
        </div>
        <div class="sl-card-body">
            <form method="POST" action="{{ isset($team) ? route('verifier.store.team', ['team' => $team]) : route('verifier.store') }}">
                @csrf
                <input type="hidden" name="team" value="{{ $team ?? 'peregrine' }}">
                <input type="hidden" name="cn_name" id="cn_name" value="{{ old('cn_name', trim(old('first_name','') . ' ' . old('last_name',''))) }}">

                {{-- ── Header row ── --}}
                <div class="row g-2 mb-2">
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Date <span class="req">*</span></label>
                        <input type="date" name="date" class="sl-form-input @error('date') is-invalid @enderror" value="{{ old('date', now()->format('Y-m-d')) }}" readonly required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">PJC Name <span class="req">*</span></label>
                        <input type="text" class="sl-form-input" value="{{ auth()->user()->name }}" readonly tabindex="-1">
                        <input type="hidden" name="verifier_name" value="{{ auth()->user()->name }}">
                    </div>
                    <div class="col-md-6 sl-form-group">
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

                {{-- Row 1: Name + DOB + Age + Gender + Phone --}}
                <div class="row g-2 mb-2">
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">First Name <span class="req">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="sl-form-input @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="First name" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Last Name <span class="req">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="sl-form-input @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Last name" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Date of Birth <span class="req">*</span></label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="sl-form-input @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-1 sl-form-group">
                        <label class="sl-form-label">Age <span class="req">*</span></label>
                        <input type="number" id="age" name="age" class="sl-form-input @error('age') is-invalid @enderror" value="{{ old('age') }}" placeholder="—" min="18" max="100" readonly tabindex="-1" required>
                        @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Gender <span class="req">*</span></label>
                        <select name="gender" class="sl-form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select</option>
                            <option value="Male"   {{ old('gender') == 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other"  {{ old('gender') == 'Other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Phone Number <span class="req">*</span></label>
                        <input type="tel" name="phone_number" class="sl-form-input @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" placeholder="555-123-4567" required>
                        @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Row 2: Address + State + Zip + Account Type --}}
                <div class="row g-2 mb-2">
                    <div class="col-md-5 sl-form-group">
                        <label class="sl-form-label">Address <span class="req">*</span></label>
                        <input type="text" name="address" class="sl-form-input @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Street address" required>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">State <span class="req">*</span></label>
                        <select name="state" class="sl-form-select @error('state') is-invalid @enderror" required>
                            <option value="">State</option>
                            @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'DC','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $abbr => $name)
                                <option value="{{ $abbr }}" {{ old('state') == $abbr ? 'selected' : '' }}>{{ $abbr }}</option>
                            @endforeach
                        </select>
                        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 sl-form-group">
                        <label class="sl-form-label">Zip Code <span class="req">*</span></label>
                        <input type="text" name="zip_code" class="sl-form-input @error('zip_code') is-invalid @enderror" value="{{ old('zip_code') }}" placeholder="Zip" maxlength="10" required>
                        @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Account Type <span class="req">*</span></label>
                        <select name="account_type" class="sl-form-select @error('account_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="Checking" {{ old('account_type') == 'Checking' ? 'selected' : '' }}>Checking</option>
                            <option value="Savings"  {{ old('account_type') == 'Savings'  ? 'selected' : '' }}>Savings</option>
                            <option value="Card"     {{ old('account_type') == 'Card'     ? 'selected' : '' }}>Card</option>
                        </select>
                        @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Row 3: Medications --}}
                <div class="row g-2">
                    <div class="col-md-12 sl-form-group">
                        <label class="sl-form-label">Medications <span class="req">*</span></label>
                        <input type="text" name="medications" class="sl-form-input @error('medications') is-invalid @enderror" value="{{ old('medications') }}" placeholder="List all current medications" required>
                        @error('medications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="sl-divider">
                <h5 class="sl-section-title"><i class="bx bx-plus-medical"></i> Doctor Information</h5>

                <div class="row g-2">
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Doctor Name <span class="req">*</span></label>
                        <input type="text" name="doctor_name" class="sl-form-input @error('doctor_name') is-invalid @enderror" value="{{ old('doctor_name') }}" placeholder="Dr. Full Name" required>
                        @error('doctor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 sl-form-group">
                        <label class="sl-form-label">Doctor Address <span class="req">*</span></label>
                        <input type="text" name="doctor_address" class="sl-form-input @error('doctor_address') is-invalid @enderror" value="{{ old('doctor_address') }}" placeholder="Clinic / office address" required>
                        @error('doctor_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="sl-divider">
                <h5 class="sl-section-title"><i class="bx bx-bank"></i> Banking &amp; Beneficiary</h5>

                <div class="row g-2">
                    <div class="col-md-3 sl-form-group">
                        <label class="sl-form-label">Bank Name <span class="req">*</span></label>
                        <input type="text" name="bank_name" class="sl-form-input @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" placeholder="e.g., Chase" required>
                        @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5 sl-form-group">
                        <label class="sl-form-label">Bank Address <span class="req">*</span></label>
                        <input type="text" name="bank_address" class="sl-form-input @error('bank_address') is-invalid @enderror" value="{{ old('bank_address') }}" placeholder="Bank branch address" required>
                        @error('bank_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 sl-form-group">
                        <label class="sl-form-label">Beneficiary Name <span class="req">*</span></label>
                        <input type="text" name="beneficiary" class="sl-form-input @error('beneficiary') is-invalid @enderror" value="{{ old('beneficiary') }}" placeholder="Full name of beneficiary" required>
                        @error('beneficiary')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
    // Keep cn_name in sync with first + last name
    (function() {
        function syncCnName() {
            var first = (document.getElementById('first_name').value || '').trim();
            var last  = (document.getElementById('last_name').value  || '').trim();
            document.getElementById('cn_name').value = [first, last].filter(Boolean).join(' ');
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('first_name').addEventListener('input', syncCnName);
            document.getElementById('last_name').addEventListener('input',  syncCnName);
            syncCnName();
        });
    })();

    // Auto-calculate age from date of birth
    document.addEventListener('DOMContentLoaded', function() {
        var dobField = document.getElementById('date_of_birth');
        function calcAge() {
            var dob = new Date(dobField.value);
            var today = new Date();
            if (!dobField.value || dob > today) { document.getElementById('age').value = ''; return; }
            var age = today.getFullYear() - dob.getFullYear();
            var m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            document.getElementById('age').value = age;
        }
        dobField.addEventListener('change', calcAge);
        if (dobField.value) calcAge();
    });
</script>
@endsection
