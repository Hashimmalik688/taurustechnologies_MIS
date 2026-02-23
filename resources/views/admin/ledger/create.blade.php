@extends('layouts.master')
@section('title', 'Add Ledger Entry')
@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .form-section-title{font-size:.82rem;font-weight:700;color:#b89730;display:flex;align-items:center;gap:.35rem;margin-bottom:.75rem;padding-bottom:.45rem;border-bottom:1px solid rgba(212,175,55,.15)}
    .form-section-title i{font-size:1rem;opacity:.7}
    .crm-label{font-size:.72rem;font-weight:600;color:var(--bs-surface-500);margin-bottom:.25rem}
    .crm-label.required::after{content:" *";color:#c84646}
    .crm-input{border:1px solid rgba(0,0,0,.08);border-radius:22px;padding:.38rem .75rem;font-size:.75rem;width:100%;background:var(--bs-card-bg);color:var(--bs-body-color);transition:border-color .15s}
    .crm-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none}
    select.crm-input{appearance:none;-webkit-appearance:none;border-radius:22px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    textarea.crm-input{border-radius:.6rem}
    .type-sel{display:flex;gap:.65rem}
    .type-sel input[type=radio]{display:none}
    .type-sel label{flex:1;text-align:center;padding:.55rem;border:2px solid rgba(0,0,0,.06);border-radius:22px;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .15s;color:var(--bs-body-color)}
    .type-sel input:checked+label.lbl-debit{border-color:#ef4444;background:rgba(239,68,68,.08);color:#ef4444}
    .type-sel input:checked+label.lbl-credit{border-color:#10b981;background:rgba(16,185,129,.08);color:#10b981}
    .currency-pfx{position:relative}.currency-pfx::before{content:'$';position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#d4af37;font-weight:700;font-size:.75rem;pointer-events:none}.currency-pfx .crm-input{padding-left:1.6rem}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-book-add"></i> Add Ledger Entry</h4>
        </div>
        <a href="{{ route('ledger.index') }}" class="act-btn a-info"><i class="bx bx-arrow-back"></i> Back</a>
    </div>

    <form action="{{ route('ledger.store') }}" method="POST">
        @csrf
        <div class="ex-card sec-card">
            <div class="sec-body">
                <div class="form-section-title"><i class="bx bx-transfer-alt"></i> Transaction Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="crm-label">Related Lead (Optional)</label>
                        <select class="crm-input" id="lead_id" name="lead_id">
                            <option value="">Select Lead</option>
                            <option value="1">Lead #1 - John Doe</option>
                            <option value="2">Lead #2 - Jane Smith</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label required">Transaction Date</label>
                        <input type="text" class="crm-input crm-date @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" placeholder="Select date" value="{{ old('transaction_date', date('Y-m-d')) }}" required autocomplete="off">
                        @error('transaction_date')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label required">Type</label>
                        <div class="type-sel">
                            <div style="flex:1"><input type="radio" id="type_debit" name="type" value="debit" required><label for="type_debit" class="lbl-debit"><i class="bx bx-minus-circle"></i> Debit</label></div>
                            <div style="flex:1"><input type="radio" id="type_credit" name="type" value="credit" checked required><label for="type_credit" class="lbl-credit"><i class="bx bx-plus-circle"></i> Credit</label></div>
                        </div>
                        @error('type')<div class="invalid-feedback d-block" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label required">Amount</label>
                        <div class="currency-pfx">
                            <input type="number" step="0.01" min="0" class="crm-input @error('amount') is-invalid @enderror" name="amount" placeholder="0.00" value="{{ old('amount') }}" required>
                        </div>
                        @error('amount')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label required">Category</label>
                        <select class="crm-input @error('category') is-invalid @enderror" name="category" required>
                            <option value="">Select Category</option>
                            <option value="commission">Commission</option>
                            <option value="payment">Payment</option>
                            <option value="refund">Refund</option>
                            <option value="expense">Expense</option>
                            <option value="bonus">Bonus</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="other">Other</option>
                        </select>
                        @error('category')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="crm-label">Reference Number</label>
                        <input type="text" class="crm-input @error('reference_number') is-invalid @enderror" name="reference_number" placeholder="e.g., INV-001234" value="{{ old('reference_number') }}">
                        @error('reference_number')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="crm-label">Description</label>
                        <textarea class="crm-input @error('description') is-invalid @enderror" name="description" rows="3" placeholder="Enter description or notes...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback" style="font-size:.68rem">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('ledger.index') }}" class="act-btn a-danger"><i class="bx bx-x"></i> Cancel</a>
            <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Create Entry</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(function(){
    $('select.crm-input').select2({minimumResultsForSearch:10,width:'100%'});
    $('.crm-date').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true});
});
</script>
@endsection
