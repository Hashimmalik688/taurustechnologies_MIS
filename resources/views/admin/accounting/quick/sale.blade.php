@extends('layouts.master')

@section('title', 'Record a Sale')

@section('css')
<style>
:root {
    --acct-gold:       #d4af37;
    --acct-gold-dark:  #b8941f;
    --acct-gold-light: #f5ecd0;
    --acct-dark:       #1a1a1a;
    --acct-header-bg:  #2d2d2d;
}
.txn-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }
@media (max-width: 900px) { .txn-layout { grid-template-columns: 1fr; } }

/* Form card */
.txn-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: 3px solid var(--acct-gold);
    border-radius: 0 0 6px 6px;
    overflow: hidden;
}
.txn-card-header {
    background: var(--acct-header-bg);
    padding: 12px 20px;
    display: flex; align-items: center; gap: 10px;
}
.txn-card-header .txn-icon {
    width: 34px; height: 34px;
    background: rgba(212,175,55,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--acct-gold); font-size: 1.1rem;
}
.txn-card-header .txn-title { font-size: .95rem; font-weight: 700; color: #fff; margin: 0; }
.txn-card-header .txn-sub   { font-size: .72rem; color: #888; margin: 0; }
.txn-card-body { padding: 22px 24px; }

.txn-field-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 5px;
    display: block;
}
.txn-card-body .form-control,
.txn-card-body .form-select {
    font-size: .875rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}
.txn-card-body .form-control:focus,
.txn-card-body .form-select:focus {
    border-color: var(--acct-gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,.18);
}
.txn-card-body .input-group-text {
    font-size: .85rem;
    font-weight: 700;
    color: var(--acct-gold-dark);
    background: var(--acct-gold-light);
    border-color: #dee2e6;
}
.btn-txn-post {
    background: var(--acct-gold);
    border: none;
    color: #1a1a1a;
    font-weight: 700;
    font-size: .85rem;
    padding: 9px 24px;
    border-radius: 4px;
    letter-spacing: .02em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background .15s;
}
.btn-txn-post:hover { background: var(--acct-gold-dark); color: #fff; }

/* Entry preview panel */
.entry-preview-panel {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
    position: sticky;
    top: 80px;
}
.preview-header {
    background: var(--acct-header-bg);
    padding: 10px 14px;
    display: flex; align-items: center; gap: 8px;
}
.preview-header span { font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #aaa; }
.preview-body { padding: 14px 16px; }
.preview-body .preview-title { font-size: .72rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: #888; margin-bottom: 10px; }
.je-preview-table { width: 100%; font-size: .82rem; }
.je-preview-table th { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #aaa; padding: 3px 6px; border-bottom: 1px solid #f1f3f5; }
.je-preview-table td { padding: 5px 6px; vertical-align: middle; border-bottom: 1px solid #f9f9f9; }
.je-preview-table td .acct-name  { font-size: .82rem; font-weight: 600; color: #2d2d2d; }
.je-preview-table td .acct-code  { font-size: .7rem; color: #aaa; font-family: 'Courier New', monospace; }
.je-preview-table td.preview-dr  { font-family: 'Courier New', monospace; font-size: .88rem; color: #2e7d32; font-weight: 700; text-align: right; }
.je-preview-table td.preview-cr  { font-family: 'Courier New', monospace; font-size: .88rem; color: #c62828; font-weight: 700; text-align: right; }
.je-preview-table td.empty       { color: #ddd; text-align: right; font-size: .8rem; }
.preview-body .preview-note { font-size: .73rem; color: #888; margin: 10px 0 0; line-height: 1.5; }
.preview-body .preview-note strong { color: var(--acct-gold-dark); }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb nav --}}
    <div class="d-flex align-items-center gap-3 mb-3" style="font-size:.82rem;color:#888;">
        <a href="{{ route('admin.accounting.journal.index') }}" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <i class="bx bx-book-open me-1"></i>Journal
        </a>
        <i class="bx bx-chevron-right"></i>
        <span style="color:#495057;font-weight:600;">Record Sale</span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3"
             style="border-left:4px solid #dc3545;border-radius:4px;font-size:.875rem;">
            <i class="bx bx-error-circle me-1"></i>
            @foreach($errors->all() as $err) {{ $err }}<br> @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="txn-layout">

        {{-- ── Form card ── --}}
        <div>
            <div class="txn-card">
                <div class="txn-card-header">
                    <div class="txn-icon"><i class="bx bx-purchase-tag"></i></div>
                    <div>
                        <div class="txn-title">Record a Sale / Policy</div>
                        <div class="txn-sub">Dr Accounts Receivable · Cr Sales Income</div>
                    </div>
                </div>
                <div class="txn-card-body">
                    <form method="POST" action="{{ route('admin.accounting.record-sale.store') }}" id="saleForm">
                        @csrf

                        <div class="mb-3">
                            <label class="txn-field-label">Partner / Client <span class="text-danger">*</span></label>
                            <select name="partner_id" id="partnerSelect"
                                    class="form-select @error('partner_id') is-invalid @enderror" required>
                                <option value="">— Select Partner —</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}"
                                            data-name="{{ $partner->name }}"
                                            {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                        {{ $partner->name }}
                                        @if($partner->code)  ·  {{ $partner->code }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('partner_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="txn-field-label">Insurance Carrier</label>
                            <select name="insurance_carrier_id" id="carrierSelect"
                                    class="form-select @error('insurance_carrier_id') is-invalid @enderror"
                                    disabled>
                                <option value="">— Select a partner first —</option>
                            </select>
                            @error('insurance_carrier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="txn-field-label">Insured Name</label>
                            <input type="text" name="insured_name"
                                   class="form-control @error('insured_name') is-invalid @enderror"
                                   value="{{ old('insured_name') }}" placeholder="Name of the insured / policy holder">
                            @error('insured_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="txn-field-label">Gross Sale Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="gross_amount" id="grossAmountInput"
                                       step="0.01" min="0.01"
                                       class="form-control @error('gross_amount') is-invalid @enderror"
                                       value="{{ old('gross_amount') }}" placeholder="Full policy / sale value">
                                @error('gross_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Share % + calculated share amount --}}
                        <div class="row g-2 mb-3">
                            <div class="col-sm-5">
                                <label class="txn-field-label">Our Share %</label>
                                <div class="input-group">
                                    <input type="number" name="our_share_percentage" id="sharePercentageInput"
                                           step="0.01" min="0" max="100"
                                           class="form-control @error('our_share_percentage') is-invalid @enderror"
                                           value="{{ old('our_share_percentage') }}" placeholder="e.g. 30">
                                    <span class="input-group-text">%</span>
                                    @error('our_share_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <label class="txn-field-label">Our Share Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="shareAmountDisplay"
                                           step="0.01" min="0.01"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           value="{{ old('amount') }}" placeholder="Auto-calculated">
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mt-1" style="font-size:.7rem;color:#888;">Ledger entries use this amount. Edit directly if no % is set.</div>
                            </div>
                        </div>

                        {{-- Hidden amount posted to server --}}
                        <input type="hidden" name="amount" id="amountHidden" value="{{ old('amount') }}">

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="txn-field-label">Entry Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date"
                                       class="form-control @error('entry_date') is-invalid @enderror"
                                       value="{{ old('entry_date', date('Y-m-d')) }}" required>
                                @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="txn-field-label">Policy / Reference #</label>
                                <input type="text" name="reference" class="form-control"
                                       value="{{ old('reference') }}" placeholder="Optional">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="txn-field-label">Description / Narration <span class="text-danger">*</span></label>
                            <textarea name="description" rows="2"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="e.g. Health policy sold – Policy # HP-2024-001"
                                      required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-txn-post">
                                <i class="bx bx-save"></i> Post Sale Entry
                            </button>
                            <a href="{{ route('admin.accounting.journal.index') }}"
                               class="btn btn-sm btn-outline-secondary" style="font-size:.82rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Entry preview ── --}}
        <div>
            <div class="entry-preview-panel">
                <div class="preview-header">
                    <i class="bx bx-spreadsheet" style="color:var(--acct-gold);font-size:1rem;"></i>
                    <span>Journal Entry Preview</span>
                </div>
                <div class="preview-body">
                    <div class="preview-title">What will be posted</div>
                    <table class="je-preview-table">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Dr</th>
                                <th>Cr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="acct-name">A/R — <span id="previewPartnerName">Partner</span></div>
                                    <div class="acct-code">1200 · Accounts Receivable</div>
                                </td>
                                <td class="preview-dr" id="previewDr">—</td>
                                <td class="empty">—</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="acct-name">Sales Income</div>
                                    <div class="acct-code">4100 · Operating Revenue</div>
                                </td>
                                <td class="empty">—</td>
                                <td class="preview-cr" id="previewCr">—</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="preview-note">
                        <strong>Dr</strong> debits partner A/R (they owe us more) ·
                        <strong>Cr</strong> credits sales income (revenue earned)
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
var oldCarrierId = '{{ old('insurance_carrier_id') }}';

function loadCarriers(partnerId, preselectId) {
    var sel = document.getElementById('carrierSelect');
    if (!partnerId) {
        sel.innerHTML = '<option value="">— Select a partner first —</option>';
        sel.disabled = true;
        return;
    }
    sel.innerHTML = '<option value="">Loading…</option>';
    sel.disabled = true;
    fetch('/admin/accounting/partner/' + partnerId + '/carriers')
        .then(function(r){ return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value="">— No Specific Carrier —</option>';
            data.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                if (preselectId && String(c.id) === String(preselectId)) {
                    opt.selected = true;
                }
                sel.appendChild(opt);
            });
            sel.disabled = false;
        });
}

function fmt(n) {
    var v = parseFloat(n);
    if (isNaN(v) || v <= 0) return '—';
    return '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function recalcShare() {
    var gross = parseFloat(document.getElementById('grossAmountInput').value);
    var pct   = parseFloat(document.getElementById('sharePercentageInput').value);
    var shareDisplay = document.getElementById('shareAmountDisplay');
    var amountHidden = document.getElementById('amountHidden');

    if (!isNaN(gross) && gross > 0 && !isNaN(pct) && pct >= 0) {
        var share = Math.round(gross * pct / 100 * 100) / 100;
        shareDisplay.value = share > 0 ? share.toFixed(2) : '';
        amountHidden.value  = share > 0 ? share.toFixed(2) : '';
    } else {
        // If percentage is not set, allow manual entry in the share display
        amountHidden.value = shareDisplay.value;
    }
    updatePreview();
}

function updatePreview() {
    var amt  = document.getElementById('amountHidden').value
                || document.getElementById('shareAmountDisplay').value;
    var sel  = document.getElementById('partnerSelect');
    var opt  = sel.options[sel.selectedIndex];
    var name = opt && opt.dataset.name ? opt.dataset.name : 'Partner';
    document.getElementById('previewPartnerName').textContent = name;
    document.getElementById('previewDr').textContent = fmt(amt);
    document.getElementById('previewCr').textContent = fmt(amt);
}

document.getElementById('grossAmountInput').addEventListener('input', recalcShare);
document.getElementById('sharePercentageInput').addEventListener('input', recalcShare);
document.getElementById('shareAmountDisplay').addEventListener('input', function() {
    // Manual override when no % is given
    document.getElementById('amountHidden').value = this.value;
    updatePreview();
});
document.getElementById('partnerSelect').addEventListener('change', function() {
    loadCarriers(this.value, null);
    updatePreview();
});

// Ensure hidden amount is in sync before submit
document.getElementById('saleForm').addEventListener('submit', function() {
    var shareDisplay = document.getElementById('shareAmountDisplay');
    var amountHidden = document.getElementById('amountHidden');
    if (!amountHidden.value && shareDisplay.value) {
        amountHidden.value = shareDisplay.value;
    }
});

// On page load: if a partner was previously selected (old() after validation), reload carriers
var initialPartner = document.getElementById('partnerSelect').value;
if (initialPartner) {
    loadCarriers(initialPartner, oldCarrierId);
}
updatePreview();
</script>
@endsection