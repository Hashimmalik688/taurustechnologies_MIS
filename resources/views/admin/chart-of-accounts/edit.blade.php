@extends('layouts.master')
@section('title', 'Edit Account')
@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .form-page-hdr { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.75rem; margin-bottom:.75rem; }
    .form-page-hdr h4 { font-size:1.1rem; font-weight:700; margin:0; display:flex; align-items:center; gap:.45rem; }
    .form-page-hdr h4 i { color:#d4af37; font-size:1.25rem; }
    .form-page-hdr p { margin:2px 0 0; font-size:.72rem; color:var(--bs-surface-500); }
    .form-section-title { font-size:.82rem; font-weight:700; color:#b89730; display:flex; align-items:center; gap:.35rem; margin-bottom:.75rem; padding-bottom:.45rem; border-bottom:1px solid rgba(212,175,55,.15); }
    .form-section-title i { font-size:1rem; opacity:.7; }
    .crm-label { font-size:.72rem; font-weight:600; color:var(--bs-surface-500); margin-bottom:.25rem; }
    .crm-label.required::after { content:" *"; color:#c84646; }
    .crm-input { border:1px solid rgba(0,0,0,.08); border-radius:22px; padding:.38rem .75rem; font-size:.75rem; width:100%; background:var(--bs-card-bg); color:var(--bs-body-color); transition:border-color .15s; }
    .crm-input:focus { border-color:#d4af37; box-shadow:0 0 0 2px rgba(212,175,55,.12); outline:none; }
    select.crm-input{appearance:none;-webkit-appearance:none;border-radius:22px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    textarea.crm-input { border-radius:.6rem; }
    .crm-check { accent-color:#d4af37; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-edit"></i> Edit Account</h4>
            <p>{{ $account->account_code }} — {{ $account->account_name }}</p>
        </div>
        <a href="{{ route('chart-of-accounts.index') }}" class="act-btn a-info"><i class="bx bx-arrow-back"></i> Back</a>
    </div>

    <form action="{{ route('chart-of-accounts.update', $account->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="ex-card sec-card">
            <div class="sec-body">
                <div class="form-section-title"><i class="bx bx-info-circle"></i> Account Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="crm-label required">Account Code</label>
                        <input type="text" class="crm-input @error('account_code') is-invalid @enderror" name="account_code" value="{{ old('account_code', $account->account_code) }}" required>
                        @error('account_code')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label required">Account Name</label>
                        <input type="text" class="crm-input @error('account_name') is-invalid @enderror" name="account_name" value="{{ old('account_name', $account->account_name) }}" required>
                        @error('account_name')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label required">Account Type</label>
                        <select class="crm-input @error('account_type') is-invalid @enderror" name="account_type" required>
                            <option value="">Select Type</option>
                            @foreach($accountTypes as $type)<option value="{{ $type }}" {{ old('account_type', $account->account_type) == $type ? 'selected' : '' }}>{{ $type }}</option>@endforeach
                        </select>
                        @error('account_type')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label">Account Category</label>
                        <select class="crm-input @error('account_category') is-invalid @enderror" name="account_category">
                            <option value="">Select Category</option>
                            @foreach($accountCategories as $category)<option value="{{ $category }}" {{ old('account_category', $account->account_category) == $category ? 'selected' : '' }}>{{ $category }}</option>@endforeach
                        </select>
                        @error('account_category')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label">Parent Account</label>
                        <select class="crm-input @error('parent_account_id') is-invalid @enderror" name="parent_account_id">
                            <option value="">None (Top Level)</option>
                            @foreach($parentAccounts as $parent)<option value="{{ $parent->id }}" {{ old('parent_account_id', $account->parent_account_id) == $parent->id ? 'selected' : '' }}>{{ $parent->account_code }} - {{ $parent->account_name }}</option>@endforeach
                        </select>
                        @error('parent_account_id')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label">Opening Balance</label>
                        <input type="number" step="0.01" class="crm-input @error('opening_balance') is-invalid @enderror" name="opening_balance" value="{{ old('opening_balance', $account->opening_balance) }}">
                        @error('opening_balance')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label class="crm-label">Description</label>
                        <textarea class="crm-input @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $account->description) }}</textarea>
                        @error('description')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label style="font-size:.72rem;font-weight:600;display:flex;align-items:center;gap:.35rem;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" class="crm-check" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                            Account is Active
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('chart-of-accounts.index') }}" class="act-btn a-danger"><i class="bx bx-x"></i> Cancel</a>
            <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Update Account</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script>$(function(){$('select.crm-input').select2({minimumResultsForSearch:10,width:'100%'})});</script>
@endsection
