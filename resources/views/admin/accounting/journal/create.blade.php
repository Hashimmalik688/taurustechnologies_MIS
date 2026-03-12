@extends('layouts.master')

@section('title', 'General Journal Entry')

@section('css')
<style>
    .line-row td { vertical-align: middle; }
    .totals-bar  { background: #f8f9fa; font-weight: 700; }
    .balance-ok   { color: #198754; }
    .balance-bad  { color: #dc3545; }
    .btn-remove { padding: 0.2rem 0.5rem; }
</style>
@endsection

@section('content')
@include('admin.accounting._nav')
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.accounting.journal.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
            <h4 class="mb-1 text-print-body u-fw-600">
                <i class="bx bx-edit me-2 text-gold"></i>
                General Journal Entry
            </h4>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bx bx-error-circle me-2"></i>
            @foreach($errors->all() as $err) {{ $err }}<br> @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.accounting.journal.store') }}" id="journalForm">
        @csrf

        {{-- Entry Header --}}
        <div class="card mb-3">
            <div class="card-header fw-semibold">Entry Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" class="form-control @error('entry_date') is-invalid @enderror"
                               value="{{ old('entry_date', date('Y-m-d')) }}" required>
                        @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
                               value="{{ old('description') }}" placeholder="Narration…" required>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Reference / Policy #</label>
                        <input type="text" name="reference" class="form-control"
                               value="{{ old('reference') }}" placeholder="Optional">
                    </div>
                </div>
            </div>
        </div>

        {{-- Lines --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Journal Lines</span>
                <button type="button" id="addLineBtn" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-plus me-1"></i> Add Line
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="linesTable">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:250px">Account <span class="text-danger">*</span></th>
                                <th style="min-width:200px">Partner (optional)</th>
                                <th style="min-width:130px" class="text-end">Debit</th>
                                <th style="min-width:130px" class="text-end">Credit</th>
                                <th style="min-width:200px">Line Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="linesBody">
                            {{-- Pre-fill 2 blank rows --}}
                            @for($i = 0; $i < 2; $i++)
                            <tr class="line-row">
                                <td>
                                    <select name="lines[{{ $i }}][account_id]" class="form-select form-select-sm" required>
                                        <option value="">— Select Account —</option>
                                        @foreach($accounts as $acct)
                                            <option value="{{ $acct->id }}" {{ old("lines.$i.account_id") == $acct->id ? 'selected' : '' }}>
                                                {{ $acct->account_code }} – {{ $acct->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="lines[{{ $i }}][partner_id]" class="form-select form-select-sm">
                                        <option value="">— None —</option>
                                        @foreach($partners as $p)
                                            <option value="{{ $p->id }}" {{ old("lines.$i.partner_id") == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="lines[{{ $i }}][debit]" step="0.01" min="0"
                                           class="form-control form-control-sm text-end line-debit"
                                           value="{{ old('lines.'.$i.'.debit', '0.00') }}">
                                </td>
                                <td>
                                    <input type="number" name="lines[{{ $i }}][credit]" step="0.01" min="0"
                                           class="form-control form-control-sm text-end line-credit"
                                           value="{{ old('lines.'.$i.'.credit', '0.00') }}">
                                </td>
                                <td>
                                    <input type="text" name="lines[{{ $i }}][description]" class="form-control form-control-sm"
                                           value="{{ old('lines.'.$i.'.description') }}" placeholder="Optional">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove" {{ $i < 2 ? 'style=opacity:.4' : '' }}>
                                        <i class="bx bx-x"></i>
                                    </button>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr class="totals-bar">
                                <td colspan="2" class="text-end">Totals</td>
                                <td class="text-end" id="totalDebit">0.00</td>
                                <td class="text-end" id="totalCredit">0.00</td>
                                <td colspan="2">
                                    <span id="balanceStatus" class="small"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" id="submitBtn" class="btn btn-dark" disabled>
                <i class="bx bx-save me-1"></i> Post Journal Entry
            </button>
            <a href="{{ route('admin.accounting.journal.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
(function () {
    // Account options HTML (re-used when adding rows)
    var accountOptions = `<option value="">— Select Account —</option>` +
        @foreach($accounts as $acct)
        `<option value="{{ $acct->id }}">{{ $acct->account_code }} – {{ addslashes($acct->account_name) }}</option>` +
        @endforeach
        ``;

    var partnerOptions = `<option value="">— None —</option>` +
        @foreach($partners as $p)
        `<option value="{{ $p->id }}">{{ addslashes($p->name) }}</option>` +
        @endforeach
        ``;

    var rowIndex = {{ max(2, old('lines') ? count(old('lines')) : 2) }};

    function makeRow(idx) {
        return `<tr class="line-row">
            <td><select name="lines[${idx}][account_id]" class="form-select form-select-sm" required>${accountOptions}</select></td>
            <td><select name="lines[${idx}][partner_id]" class="form-select form-select-sm">${partnerOptions}</select></td>
            <td><input type="number" name="lines[${idx}][debit]"  step="0.01" min="0" value="0.00" class="form-control form-control-sm text-end line-debit"></td>
            <td><input type="number" name="lines[${idx}][credit]" step="0.01" min="0" value="0.00" class="form-control form-control-sm text-end line-credit"></td>
            <td><input type="text" name="lines[${idx}][description]" class="form-control form-control-sm" placeholder="Optional"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove"><i class="bx bx-x"></i></button></td>
        </tr>`;
    }

    document.getElementById('addLineBtn').addEventListener('click', function () {
        document.getElementById('linesBody').insertAdjacentHTML('beforeend', makeRow(rowIndex++));
        recalculate();
    });

    document.getElementById('linesBody').addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove');
        if (!btn) return;
        var rows = document.querySelectorAll('#linesBody .line-row');
        if (rows.length <= 2) { alert('A journal entry needs at least 2 lines.'); return; }
        btn.closest('tr').remove();
        recalculate();
    });

    document.getElementById('linesBody').addEventListener('input', function (e) {
        if (e.target.classList.contains('line-debit') || e.target.classList.contains('line-credit')) {
            recalculate();
        }
    });

    function recalculate() {
        var dr = 0, cr = 0;
        document.querySelectorAll('.line-debit').forEach(function(el)  { dr += parseFloat(el.value) || 0; });
        document.querySelectorAll('.line-credit').forEach(function(el) { cr += parseFloat(el.value) || 0; });
        document.getElementById('totalDebit').textContent  = dr.toFixed(2);
        document.getElementById('totalCredit').textContent = cr.toFixed(2);
        var balanced = Math.abs(dr - cr) < 0.005 && dr > 0;
        var status   = document.getElementById('balanceStatus');
        var btn      = document.getElementById('submitBtn');
        if (balanced) {
            status.innerHTML = '<span class="balance-ok"><i class="bx bx-check-circle"></i> Balanced</span>';
            btn.disabled = false;
        } else {
            var diff = Math.abs(dr - cr).toFixed(2);
            status.innerHTML = '<span class="balance-bad"><i class="bx bx-error"></i> Off by ' + diff + '</span>';
            btn.disabled = true;
        }
    }

    recalculate();
})();
</script>
@endsection
