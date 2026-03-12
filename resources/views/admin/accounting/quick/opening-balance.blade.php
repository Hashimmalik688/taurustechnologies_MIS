@extends('layouts.master')

@section('title', 'Opening Balance')

@section('css')
<style>
:root { --acct-gold:#d4af37; --acct-gold-dark:#b8941f; --acct-gold-light:#f5ecd0; --acct-dark:#1a1a1a; --acct-header-bg:#2d2d2d; }
.txn-layout { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
@media (max-width:900px) { .txn-layout { grid-template-columns:1fr; } }
.txn-card { background:#fff; border:1px solid #dee2e6; border-top:3px solid #fd7e14; border-radius:0 0 6px 6px; overflow:hidden; }
.txn-card-header { background:var(--acct-header-bg); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.txn-card-header .txn-icon { width:34px; height:34px; background:rgba(253,126,20,.15); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fd7e14; font-size:1.1rem; }
.txn-card-header .txn-title { font-size:.95rem; font-weight:700; color:#fff; margin:0; }
.txn-card-header .txn-sub { font-size:.72rem; color:#888; margin:0; }
.txn-card-body { padding:22px 24px; }
.txn-field-label { font-size:.72rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:#6c757d; margin-bottom:5px; display:block; }
.txn-card-body .form-control, .txn-card-body .form-select { font-size:.875rem; border:1px solid #dee2e6; border-radius:4px; }
.txn-card-body .form-control:focus, .txn-card-body .form-select:focus { border-color:var(--acct-gold); box-shadow:0 0 0 3px rgba(212,175,55,.18); }
.txn-card-body .input-group-text { font-size:.85rem; font-weight:700; color:var(--acct-gold-dark); background:var(--acct-gold-light); border-color:#dee2e6; }
.btn-txn-post { background:#fd7e14; border:none; color:#fff; font-weight:700; font-size:.85rem; padding:9px 24px; border-radius:4px; letter-spacing:.02em; display:inline-flex; align-items:center; gap:6px; transition:background .15s; }
.btn-txn-post:hover { background:#e96b00; color:#fff; }
/* Balance type radio buttons */
.bal-type-group { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.bal-type-option { position:relative; }
.bal-type-option input[type=radio] { position:absolute; opacity:0; width:0; height:0; }
.bal-type-option label {
    display:block; cursor:pointer; padding:12px 14px; border:2px solid #dee2e6; border-radius:6px;
    font-size:.85rem; transition:border-color .15s, background .15s; background:#fff;
}
.bal-type-option input:checked + label { border-color:var(--acct-gold); background:var(--acct-gold-light); }
.bal-type-option label .type-name { font-weight:700; color:#2d2d2d; display:block; margin-bottom:2px; }
.bal-type-option label .type-desc { font-size:.75rem; color:#888; }
.bal-type-option input:checked + label .type-name { color:var(--acct-gold-dark); }
/* Preview */
.entry-preview-panel { background:#fff; border:1px solid #dee2e6; border-radius:6px; overflow:hidden; position:sticky; top:80px; }
.preview-header { background:var(--acct-header-bg); padding:10px 14px; display:flex; align-items:center; gap:8px; }
.preview-header span { font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#aaa; }
.preview-body { padding:14px 16px; }
.preview-body .preview-title { font-size:.72rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:#888; margin-bottom:10px; }
.je-preview-table { width:100%; font-size:.82rem; }
.je-preview-table th { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#aaa; padding:3px 6px; border-bottom:1px solid #f1f3f5; }
.je-preview-table td { padding:5px 6px; vertical-align:middle; border-bottom:1px solid #f9f9f9; }
.je-preview-table td .acct-name { font-size:.82rem; font-weight:600; color:#2d2d2d; }
.je-preview-table td .acct-code { font-size:.7rem; color:#aaa; font-family:'Courier New',monospace; }
.je-preview-table td.preview-dr { font-family:'Courier New',monospace; font-size:.88rem; color:#2e7d32; font-weight:700; text-align:right; }
.je-preview-table td.preview-cr { font-family:'Courier New',monospace; font-size:.88rem; color:#c62828; font-weight:700; text-align:right; }
.je-preview-table td.empty { color:#ddd; text-align:right; font-size:.8rem; }
.preview-body .preview-note { font-size:.73rem; color:#888; margin:10px 0 0; line-height:1.5; }
.preview-body .preview-note strong { color:var(--acct-gold-dark); }
</style>
@endsection

@section('content')
@include('admin.accounting._nav')
<div class="container-fluid">

    <div class="d-flex align-items-center gap-3 mb-3" style="font-size:.82rem;color:#888;">
        <a href="{{ route('admin.accounting.journal.index') }}" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <i class="bx bx-book-open me-1"></i>Journal
        </a>
        <i class="bx bx-chevron-right"></i>
        <span style="color:#495057;font-weight:600;">Opening Balance</span>
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

        <div>
            <div class="txn-card">
                <div class="txn-card-header">
                    <div class="txn-icon"><i class="bx bx-transfer"></i></div>
                    <div>
                        <div class="txn-title">Record Opening Balance</div>
                        <div class="txn-sub">Bring forward existing balances for a partner</div>
                    </div>
                </div>
                <div class="txn-card-body">
                    <form method="POST" action="{{ route('admin.accounting.opening-balance.store') }}">
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

                        {{-- Balance type selector --}}
                        <div class="mb-3">
                            <label class="txn-field-label">Balance Type <span class="text-danger">*</span></label>
                            <div class="bal-type-group">
                                <div class="bal-type-option">
                                    <input type="radio" name="normal_balance" id="balDebit" value="debit"
                                           {{ old('normal_balance', 'debit') === 'debit' ? 'checked' : '' }}>
                                    <label for="balDebit">
                                        <span class="type-name">Debit Balance</span>
                                        <span class="type-desc">Partner owes <em>us</em> money</span>
                                    </label>
                                </div>
                                <div class="bal-type-option">
                                    <input type="radio" name="normal_balance" id="balCredit" value="credit"
                                           {{ old('normal_balance') === 'credit' ? 'checked' : '' }}>
                                    <label for="balCredit">
                                        <span class="type-name">Credit Balance</span>
                                        <span class="type-desc"><em>We</em> owe the partner money</span>
                                    </label>
                                </div>
                            </div>
                            @error('normal_balance') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="txn-field-label">Balance Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" id="amountInput"
                                       step="0.01" min="0.01"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount') }}" placeholder="0.00" required>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="txn-field-label">Effective Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date"
                                   class="form-control @error('entry_date') is-invalid @enderror"
                                   value="{{ old('entry_date', date('Y-m-d')) }}" required>
                            @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="txn-field-label">Notes</label>
                            <textarea name="description" rows="2" class="form-control"
                                      placeholder="e.g. Opening balance as at 01 Jan 2026">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-txn-post">
                                <i class="bx bx-save"></i> Post Opening Balance
                            </button>
                            <a href="{{ route('admin.accounting.journal.index') }}"
                               class="btn btn-sm btn-outline-secondary" style="font-size:.82rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="entry-preview-panel">
                <div class="preview-header">
                    <i class="bx bx-spreadsheet" style="color:var(--acct-gold);font-size:1rem;"></i>
                    <span>Journal Entry Preview</span>
                </div>
                <div class="preview-body">
                    <div class="preview-title">What will be posted</div>
                    <table class="je-preview-table" id="obPreviewTable">
                        <thead>
                            <tr><th>Account</th><th>Dr</th><th>Cr</th></tr>
                        </thead>
                        <tbody id="obPreviewBody">
                            {{-- filled by JS --}}
                        </tbody>
                    </table>
                    <div class="preview-note" id="obPreviewNote">
                        Select balance type and enter amount to see the journal entry.
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
                if (preselectId && String(c.id) === String(preselectId)) opt.selected = true;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        });
}

function fmt(n) {
    var v = parseFloat(n);
    if (isNaN(v) || v <= 0) return '—';
    return v.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
}
function updateOBPreview() {
    var amt     = document.getElementById('amountInput').value;
    var sel     = document.getElementById('partnerSelect');
    var opt     = sel.options[sel.selectedIndex];
    var name    = opt && opt.dataset.name ? opt.dataset.name : 'Partner';
    var isDebit = document.getElementById('balDebit').checked;
    var fmtAmt  = fmt(amt);
    var body    = document.getElementById('obPreviewBody');
    var note    = document.getElementById('obPreviewNote');

    if (isDebit) {
        body.innerHTML =
            '<tr><td><div class="acct-name">A/R — ' + name + '</div><div class="acct-code">1200 · Accounts Receivable</div></td>' +
            '<td class="preview-dr">' + fmtAmt + '</td><td class="empty">—</td></tr>' +
            '<tr><td><div class="acct-name">Opening Balance Equity</div><div class="acct-code">3900 · Owner Equity</div></td>' +
            '<td class="empty">—</td><td class="preview-cr">' + fmtAmt + '</td></tr>';
        note.innerHTML = '<strong>Dr</strong> A/R (partner has a debit balance — owes us) · <strong>Cr</strong> OBE equity offset';
    } else {
        body.innerHTML =
            '<tr><td><div class="acct-name">Opening Balance Equity</div><div class="acct-code">3900 · Owner Equity</div></td>' +
            '<td class="preview-dr">' + fmtAmt + '</td><td class="empty">—</td></tr>' +
            '<tr><td><div class="acct-name">A/R — ' + name + '</div><div class="acct-code">1200 · Accounts Receivable</div></td>' +
            '<td class="empty">—</td><td class="preview-cr">' + fmtAmt + '</td></tr>';
        note.innerHTML = '<strong>Dr</strong> OBE offset · <strong>Cr</strong> A/R (we owe the partner — credit balance)';
    }
}
document.getElementById('amountInput').addEventListener('input', updateOBPreview);
document.getElementById('partnerSelect').addEventListener('change', function() {
    loadCarriers(this.value, null);
    updateOBPreview();
});
document.getElementById('balDebit').addEventListener('change', updateOBPreview);
document.getElementById('balCredit').addEventListener('change', updateOBPreview);

var initialPartner = document.getElementById('partnerSelect').value;
if (initialPartner) loadCarriers(initialPartner, oldCarrierId);
updateOBPreview();
</script>
@endsection

