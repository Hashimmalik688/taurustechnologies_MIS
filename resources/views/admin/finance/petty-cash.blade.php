@extends('layouts.master')
@section('title', 'Petty Cash Ledger')
@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .form-page-hdr p{margin:2px 0 0;font-size:.72rem;color:var(--bs-surface-500)}
    .crm-label{font-size:.72rem;font-weight:600;color:var(--bs-surface-500);margin-bottom:.25rem}
    .crm-label.required::after{content:" *";color:#c84646}
    .crm-input{border:1px solid rgba(0,0,0,.08);border-radius:22px;padding:.38rem .75rem;font-size:.75rem;width:100%;background:var(--bs-card-bg);color:var(--bs-body-color);transition:border-color .15s}
    .crm-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none}
    select.crm-input{appearance:none;-webkit-appearance:none;border-radius:22px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    textarea.crm-input{border-radius:.6rem}
    .modal .modal-content{border-radius:14px;border:none;overflow:hidden}
    .modal .modal-header-glass{background:linear-gradient(135deg,rgba(212,175,55,.13),rgba(212,175,55,.04));padding:.75rem 1.1rem;border-bottom:1px solid rgba(212,175,55,.12)}
    .modal .modal-header-glass .modal-title{font-size:.88rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
    .modal .modal-header-glass .modal-title i{color:#d4af37}
    .modal .modal-body{padding:1rem 1.1rem}
    .modal .modal-footer{padding:.6rem 1.1rem;border-top:1px solid rgba(0,0,0,.04)}
    .info-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:22px;font-size:.72rem;font-weight:600;background:rgba(14,165,233,.08);color:#0ea5e9;border:1px solid rgba(14,165,233,.15)}
    .info-pill i{font-size:.85rem}
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Alerts --}}
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:12px">
        <i class="bx bx-check-circle me-1"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:12px">
        <i class="bx bx-x-circle me-1"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-wallet-alt"></i> Petty Cash Ledger</h4>
            <p>Manage petty cash entries — debit &amp; credit transactions</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('petty-cash.print', request()->query()) }}" target="_blank" class="act-btn a-info"><i class="bx bx-printer"></i> Print</a>
            <a href="{{ route('petty-cash.export', request()->query()) }}" class="act-btn a-info"><i class="bx bx-download"></i> Export</a>
            <button class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#addEntryModal"><i class="bx bx-plus"></i> Add Entry</button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="pipe-filter-bar mb-2">
        <form method="GET" action="{{ route('petty-cash.index') }}" class="d-flex flex-wrap align-items-end gap-2 w-100">
            <div style="min-width:140px">
                <label class="crm-label">Category</label>
                <select name="head" class="crm-input crm-select">
                    <option value="">All Categories</option>
                    @foreach($heads as $head)
                    <option value="{{ $head }}" {{ $selectedHead === $head ? 'selected' : '' }}>{{ $head }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:130px">
                <label class="crm-label">From Date</label>
                <input type="text" name="from_date" id="pcFromDate" class="crm-input crm-date" placeholder="Select" value="{{ $fromDate }}" autocomplete="off">
            </div>
            <div style="min-width:130px">
                <label class="crm-label">To Date</label>
                <input type="text" name="to_date" id="pcToDate" class="crm-input crm-date" placeholder="Select" value="{{ $toDate }}" autocomplete="off">
            </div>
            <button type="submit" class="pipe-pill" style="margin-top:auto"><i class="bx bx-filter-alt"></i> Filter</button>
            @if($selectedHead || $fromDate || $toDate)
            <a href="{{ route('petty-cash.index') }}" class="pipe-pill" style="margin-top:auto;background:rgba(239,68,68,.08);color:#ef4444;border-color:rgba(239,68,68,.15)"><i class="bx bx-x"></i> Clear</a>
            @endif
        </form>
    </div>

    {{-- Category/Date Info --}}
    @if($selectedHead)
    <div class="info-pill mb-2">
        <i class="bx bx-info-circle"></i>
        <strong>{{ $selectedHead }}</strong> &mdash;
        All time: <strong style="color:#ef4444">{{ number_format($categoryTotal, 2) }}</strong> |
        @if($fromDate && $toDate)
            {{ date('M d, Y', strtotime($fromDate)) }} – {{ date('M d, Y', strtotime($toDate)) }}:
        @else
            This month:
        @endif
        <strong style="color:#ef4444">{{ number_format($categoryMonthTotal, 2) }}</strong>
    </div>
    @endif
    @if($fromDate && $toDate && !$selectedHead)
    <div class="info-pill mb-2">
        <i class="bx bx-calendar-event"></i>
        Viewing from <strong>{{ date('M d, Y', strtotime($fromDate)) }}</strong> to <strong>{{ date('M d, Y', strtotime($toDate)) }}</strong>
    </div>
    @endif

    {{-- Stats KPI --}}
    @php
        if ($selectedHead || $fromDate || $toDate) {
            $totalDebit = $entries->sum('debit');
            $totalCredit = $entries->sum('credit');
            $currentBalance = $balanceMap[$entries->first()?->id] ?? 0;
        } else {
            $totalDebit = \App\Models\PettyCashLedger::sum('debit');
            $totalCredit = \App\Models\PettyCashLedger::sum('credit');
            $lastEntry = \App\Models\PettyCashLedger::orderBy('id', 'desc')->first();
            $currentBalance = $lastEntry ? $balanceMap[$lastEntry->id] : 0;
        }
    @endphp
    <div class="kpi-row" style="grid-template-columns:repeat(auto-fill,minmax(170px,1fr))">
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Total Debits (In)</div>
            <div class="kpi-val">{{ number_format($totalDebit, 2) }}</div>
        </div>
        <div class="kpi-card k-red">
            <div class="kpi-lbl">Total Credits (Out)</div>
            <div class="kpi-val">{{ number_format($totalCredit, 2) }}</div>
        </div>
        <div class="kpi-card k-gold">
            <div class="kpi-lbl">Running Balance</div>
            <div class="kpi-val">{{ number_format($currentBalance, 2) }}</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Total Entries</div>
            <div class="kpi-val">{{ $entries->count() }}</div>
        </div>
    </div>

    {{-- Entries Table --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr"><i class="bx bx-table"></i> All Entries</div>
        <div class="sec-body p-0">
            <div class="table-responsive">
                <table class="ex-tbl w-100">
                    <thead>
                        <tr>
                            <th style="width:50px" class="text-center">S.N.</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Head</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                            <th style="width:80px" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                        <tr>
                            <td class="text-center"><span class="v-badge">{{ $serialNumberMap[$entry->id] }}</span></td>
                            <td style="font-size:.7rem;color:var(--bs-surface-500)">{{ $entry->date->format('M d, Y') }}</td>
                            <td><strong style="font-size:.75rem">{{ $entry->description }}</strong></td>
                            <td><span class="v-badge">{{ $entry->head }}</span></td>
                            <td class="text-end">
                                @if($entry->debit > 0)
                                <span style="color:#10b981;font-weight:700;font-size:.75rem">{{ number_format($entry->debit, 2) }}</span>
                                @else
                                <span style="opacity:.3;font-size:.72rem">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($entry->credit > 0)
                                <span style="color:#ef4444;font-weight:700;font-size:.75rem">{{ number_format($entry->credit, 2) }}</span>
                                @else
                                <span style="opacity:.3;font-size:.72rem">—</span>
                                @endif
                            </td>
                            <td class="text-end"><strong style="color:#d4af37;font-size:.75rem">{{ number_format($balanceMap[$entry->id] ?? 0, 2) }}</strong></td>
                            <td class="text-center">
                                @canEditModule('petty-cash')
                                <button class="act-btn a-warn" style="padding:.15rem .4rem;font-size:.65rem" onclick="editEntry({{ $entry->id }})" title="Edit"><i class="bx bx-pencil"></i></button>
                                @endcanEditModule
                                @canDeleteInModule('petty-cash')
                                <button class="act-btn a-danger" style="padding:.15rem .4rem;font-size:.65rem" onclick="deleteEntry({{ $entry->id }})" title="Delete"><i class="bx bx-trash"></i></button>
                                @endcanDeleteInModule
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding:2.5rem 1rem;color:var(--bs-surface-500)">
                                <i class="bx bx-inbox" style="font-size:2rem;opacity:.3"></i><br>
                                <span style="font-size:.75rem">No entries found. Click "Add Entry" to create one.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Category Breakdown --}}
    @if(!$selectedHead)
    <div class="ex-card sec-card mt-2">
        <div class="sec-hdr"><i class="bx bx-layer"></i> Category Breakdown</div>
        <div class="sec-body p-0">
            <div class="table-responsive">
                <table class="ex-tbl w-100">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">All Time</th>
                            <th class="text-end">This Month</th>
                            <th class="text-center" style="width:70px">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($heads as $headItem)
                        @php
                            $categorySpent = \App\Models\PettyCashLedger::where('head', $headItem)->sum('credit');
                            $thisMonthSpent = \App\Models\PettyCashLedger::where('head', $headItem)->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('credit');
                        @endphp
                        <tr>
                            <td><span class="v-badge">{{ $headItem }}</span></td>
                            <td class="text-end"><span style="color:#ef4444;font-weight:700;font-size:.75rem">{{ number_format($categorySpent, 2) }}</span></td>
                            <td class="text-end"><span style="color:#f59e0b;font-weight:700;font-size:.75rem">{{ number_format($thisMonthSpent, 2) }}</span></td>
                            <td class="text-center"><a href="{{ route('petty-cash.index', ['head' => $headItem]) }}" class="act-btn a-info" style="padding:.15rem .45rem;font-size:.62rem"><i class="bx bx-search"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center" style="padding:1.5rem;font-size:.75rem;color:var(--bs-surface-500)">No categories found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Add/Edit Entry Modal --}}
<div class="modal fade" id="addEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-glass">
                <h5 class="modal-title"><i class="bx bx-plus-circle"></i> <span id="modalTitle">Add New Entry</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.65rem"></button>
            </div>
            <form id="entryForm" method="POST" action="{{ route('petty-cash.store') }}">
                @csrf
                <input type="hidden" id="methodInput" name="_method" value="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="crm-label required">Date</label>
                            <input type="text" class="crm-input crm-date" id="date" name="date" placeholder="Select date" required autocomplete="off">
                            <span class="text-danger" style="font-size:.65rem" id="dateError"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="crm-label required">Head / Category</label>
                            <input type="text" class="crm-input" id="head" name="head" placeholder="Enter category" required>
                            <span class="text-danger" style="font-size:.65rem" id="headError"></span>
                        </div>
                        <div class="col-12">
                            <label class="crm-label required">Description</label>
                            <textarea class="crm-input" id="description" name="description" rows="2" placeholder="Transaction details" required></textarea>
                            <span class="text-danger" style="font-size:.65rem" id="descriptionError"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="crm-label">Debit (Cash In)</label>
                            <div style="position:relative">
                                <span style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#10b981;font-weight:700;font-size:.72rem">+</span>
                                <input type="number" class="crm-input" id="debit" name="debit" placeholder="0.00" step="0.01" min="0" value="0" style="padding-left:1.4rem">
                            </div>
                            <span class="text-danger" style="font-size:.65rem" id="debitError"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="crm-label">Credit (Cash Out)</label>
                            <div style="position:relative">
                                <span style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#ef4444;font-weight:700;font-size:.72rem">−</span>
                                <input type="number" class="crm-input" id="credit" name="credit" placeholder="0.00" step="0.01" min="0" value="0" style="padding-left:1.4rem">
                            </div>
                            <span class="text-danger" style="font-size:.65rem" id="creditError"></span>
                        </div>
                    </div>
                    <div class="info-pill mt-3" style="display:block;text-align:center">
                        <i class="bx bx-info-circle"></i> Enter either Debit OR Credit, not both. Debit increases balance, Credit decreases it.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="act-btn a-danger" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                    <button type="submit" class="act-btn a-success" id="submitBtn"><i class="bx bx-save"></i> <span id="submitBtnText">Save Entry</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header modal-header-glass" style="background:linear-gradient(135deg,rgba(239,68,68,.1),rgba(239,68,68,.03))">
                <h5 class="modal-title"><i class="bx bx-trash-alt" style="color:#ef4444"></i> Delete Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.65rem"></button>
            </div>
            <div class="modal-body text-center" style="padding:1.5rem">
                <i class="bx bx-error-circle" style="font-size:2.5rem;color:#ef4444;opacity:.6"></i>
                <p style="font-size:.82rem;font-weight:600;margin:.75rem 0 .25rem">Delete this entry?</p>
                <p style="font-size:.68rem;color:var(--bs-surface-500)">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="act-btn a-info" data-bs-dismiss="modal">Cancel</button>
                <form class="d-inline" id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="act-btn a-danger"><i class="bx bx-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>
let editingId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Init Select2
    $('.crm-select').select2({minimumResultsForSearch:10,width:'100%'});

    // Init datepicker on filter dates
    $('.crm-date').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true});
    $('#date').datepicker('setDate', new Date());

    const tl = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tl.map(function(el){ return new bootstrap.Tooltip(el); });
});

function editEntry(id) {
    fetch('/petty-cash/' + id + '/edit')
        .then(function(r){ if(!r.ok) throw new Error('Failed'); return r.json(); })
        .then(function(data){
            document.querySelectorAll('[id$="Error"]').forEach(function(el){ el.textContent=''; });
            document.getElementById('date').value = data.date;
            document.getElementById('description').value = data.description;
            document.getElementById('head').value = data.head;
            document.getElementById('debit').value = data.debit || 0;
            document.getElementById('credit').value = data.credit || 0;
            document.getElementById('modalTitle').textContent = 'Edit Entry #' + data.id;
            document.getElementById('submitBtnText').textContent = 'Update Entry';
            document.getElementById('entryForm').action = '/petty-cash/' + id;
            document.getElementById('methodInput').value = 'PUT';
            editingId = id;
            new bootstrap.Modal(document.getElementById('addEntryModal')).show();
        })
        .catch(function(e){ console.error(e); alert('Failed to load entry.'); });
}

function deleteEntry(id) {
    document.getElementById('deleteForm').action = '/petty-cash/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('addEntryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('entryForm').reset();
    document.getElementById('modalTitle').textContent = 'Add New Entry';
    document.getElementById('submitBtnText').textContent = 'Save Entry';
    document.getElementById('entryForm').action = "{{ route('petty-cash.store') }}";
    document.getElementById('methodInput').value = 'POST';
    document.querySelectorAll('[id$="Error"]').forEach(function(el){ el.textContent=''; });
    document.getElementById('head').value = '';
    var fp = document.getElementById('date');
    if(fp) $(fp).datepicker('setDate', new Date());
    editingId = null;
});

document.getElementById('entryForm').addEventListener('submit', function(e) {
    document.querySelectorAll('[id$="Error"]').forEach(function(el){ el.textContent=''; });
    var debit = parseFloat(document.getElementById('debit').value) || 0;
    var credit = parseFloat(document.getElementById('credit').value) || 0;
    var headValue = document.getElementById('head').value.trim();
    if(!headValue){ e.preventDefault(); alert('Please enter a category'); return false; }
    if(debit === 0 && credit === 0){ e.preventDefault(); alert('Please enter either a Debit or Credit amount'); return false; }
    if(debit > 0 && credit > 0){ e.preventDefault(); alert('Please enter either Debit OR Credit, not both'); return false; }
    return true;
});
</script>
@endsection
