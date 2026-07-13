@extends('layouts.partner')

@section('title') Submit Sale @endsection

@section('css')
<style>
    .pse-wrap{max-width:1000px;margin:0 auto;}
    .pse-hdr h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
    .pse-hdr p{font-size:.84rem;color:#6b7280;margin:0 0 1.25rem;}
    .pse-card{background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:.6rem;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:1.25rem 1.4rem;margin-bottom:1.2rem;}
    .pse-card h6{font-size:.72rem;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:#4f46e5;margin:0 0 1rem;}
    .pse-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.9rem 1rem;}
    @media(max-width:768px){.pse-grid{grid-template-columns:1fr;}}
    .pse-field label{display:block;font-size:.72rem;font-weight:700;color:#374151;margin-bottom:.3rem;}
    .pse-field label .req{color:#dc2626;}
    .pse-field input,.pse-field select,.pse-field textarea{width:100%;border:1px solid rgba(0,0,0,.14);border-radius:.4rem;padding:.45rem .6rem;font-size:.85rem;color:#111827;background:#fff;}
    .pse-field input:focus,.pse-field select:focus,.pse-field textarea:focus{outline:none;border-color:#4f46e5;box-shadow:0 0 0 2px rgba(79,70,229,.12);}
    .pse-actions{display:flex;justify-content:flex-end;gap:.6rem;}
    .pse-btn{background:#4f46e5;color:#fff;border:none;border-radius:.4rem;padding:.6rem 1.4rem;font-size:.85rem;font-weight:700;cursor:pointer;}
    .pse-btn:hover{background:#4338ca;}
    .pse-alert{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:.5rem;padding:.7rem 1rem;font-size:.82rem;margin-bottom:1rem;}
    .pse-alert ul{margin:.4rem 0 0;padding-left:1.1rem;}
</style>
@endsection

@section('content')
<div class="pse-wrap">
    <div class="pse-hdr">
        <h4>Submit a Sale</h4>
        <p>Fill this form for each sale you close. It enters the Taurus sales pipeline immediately and you can track its status under Submissions.</p>
    </div>

    @if($errors->any())
        <div class="pse-alert">
            Please correct the following:
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('partner.sales.store') }}" method="POST">
        @csrf

        <div class="pse-card">
            <h6>Customer</h6>
            <div class="pse-grid">
                <div class="pse-field"><label>Customer Name <span class="req">*</span></label><input type="text" name="cn_name" value="{{ old('cn_name') }}" required></div>
                <div class="pse-field"><label>Phone Number <span class="req">*</span></label><input type="text" name="phone_number" value="{{ old('phone_number') }}" required></div>
                <div class="pse-field"><label>Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"></div>
                <div class="pse-field"><label>SSN</label><input type="text" name="ssn" maxlength="11" value="{{ old('ssn') }}"></div>
                <div class="pse-field"><label>Birth Place</label><input type="text" name="birth_place" value="{{ old('birth_place') }}"></div>
                <div class="pse-field"><label>Smoker</label>
                    <select name="smoker">
                        <option value="">—</option>
                        <option value="no" @selected(old('smoker')==='no')>No</option>
                        <option value="yes" @selected(old('smoker')==='yes')>Yes</option>
                    </select>
                </div>
                <div class="pse-field" style="grid-column:1/-1;"><label>Address</label><input type="text" name="address" value="{{ old('address') }}"></div>
            </div>
        </div>

        <div class="pse-card">
            <h6>Policy</h6>
            <div class="pse-grid">
                <div class="pse-field"><label>Carrier Name</label><input type="text" name="carrier_name" value="{{ old('carrier_name') }}"></div>
                <div class="pse-field"><label>Policy Type</label><input type="text" name="policy_type" value="{{ old('policy_type') }}" placeholder="Term / Whole Life / Universal"></div>
                <div class="pse-field"><label>Coverage Amount</label><input type="number" step="0.01" name="coverage_amount" value="{{ old('coverage_amount') }}"></div>
                <div class="pse-field"><label>Monthly Premium</label><input type="number" step="0.01" name="monthly_premium" value="{{ old('monthly_premium') }}"></div>
                <div class="pse-field"><label>Beneficiary</label><input type="text" name="beneficiary" value="{{ old('beneficiary') }}"></div>
                <div class="pse-field"><label>Initial Draft Date</label><input type="date" name="initial_draft_date" value="{{ old('initial_draft_date') }}"></div>
            </div>
        </div>

        <div class="pse-card">
            <h6>Banking</h6>
            <div class="pse-grid">
                <div class="pse-field"><label>Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name') }}"></div>
                <div class="pse-field"><label>Account Type</label><input type="text" name="account_type" value="{{ old('account_type') }}" placeholder="Checking / Savings"></div>
                <div class="pse-field"><label>Routing Number</label><input type="text" name="routing_number" value="{{ old('routing_number') }}"></div>
                <div class="pse-field"><label>Bank Balance</label><input type="number" step="0.01" name="bank_balance" value="{{ old('bank_balance') }}"></div>
                <div class="pse-field"><label>Account Verified By</label><input type="text" name="account_verified_by" value="{{ old('account_verified_by') }}"></div>
            </div>
        </div>

        <div class="pse-card">
            <h6>Medical</h6>
            <div class="pse-grid">
                <div class="pse-field"><label>Height</label><input type="text" name="height" value="{{ old('height') }}"></div>
                <div class="pse-field"><label>Weight</label><input type="text" name="weight" value="{{ old('weight') }}"></div>
                <div class="pse-field"><label>Doctor Name</label><input type="text" name="doctor_name" value="{{ old('doctor_name') }}"></div>
                <div class="pse-field" style="grid-column:1/-1;"><label>Medical Issues</label><textarea name="medical_issue" rows="2">{{ old('medical_issue') }}</textarea></div>
                <div class="pse-field" style="grid-column:1/-1;"><label>Medications</label><textarea name="medications" rows="2">{{ old('medications') }}</textarea></div>
            </div>
        </div>

        <div class="pse-card">
            <h6>Sale</h6>
            <div class="pse-grid">
                <div class="pse-field"><label>Closer Name</label><input type="text" name="closer_name" value="{{ old('closer_name', $partner->name) }}"></div>
                <div class="pse-field"><label>Sale Date</label><input type="date" name="date" value="{{ old('date') }}"></div>
            </div>
        </div>

        <div class="pse-actions">
            <button type="submit" class="pse-btn"><i class="bx bx-check"></i> Submit Sale</button>
        </div>
    </form>
</div>
@endsection
