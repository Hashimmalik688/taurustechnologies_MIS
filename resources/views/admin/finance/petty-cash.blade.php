@extends('layouts.master')

@section('title', 'Petty Cash Ledger')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bx bx-wallet-alt me-2"></i>
                        Petty Cash Ledger
                    </h1>
                    <p class="text-muted mt-2">Manage petty cash entries with debit and credit transactions</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEntryModal">
                    <i class="bx bx-plus me-1"></i> Add Entry
                </button>
            </div>
        </div>
    </div>

    <!-- Search/Filter by Category and Date -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="{{ route('petty-cash.index') }}" class="d-flex gap-2 flex-wrap align-items-end">
                <!-- Category Filter -->
                <div>
                    <label class="form-label small mb-2">Category</label>
                    <select name="head" class="form-select form-select-sm" style="min-width: 150px;">
                        <option value="">-- All Categories --</option>
                        @foreach($heads as $head)
                            <option value="{{ $head }}" {{ $selectedHead === $head ? 'selected' : '' }}>
                                {{ $head }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From Filter -->
                <div>
                    <label class="form-label small mb-2">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}" style="min-width: 140px;">
                </div>

                <!-- Date To Filter -->
                <div>
                    <label class="form-label small mb-2">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}" style="min-width: 140px;">
                </div>

                <!-- Action Buttons -->
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-search me-1"></i> Filter
                </button>
                @if($selectedHead || $fromDate || $toDate)
                    <a href="{{ route('petty-cash.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-x me-1"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Category Total Alert -->
    @if($selectedHead)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>{{ $selectedHead }}</strong> | 
                    Total spent all time: <strong class="text-danger">{{ number_format($categoryTotal, 2) }}</strong> | 
                    @if($fromDate && $toDate)
                        Date range ({{ date('M d, Y', strtotime($fromDate)) }} - {{ date('M d, Y', strtotime($toDate)) }}):
                    @else
                        This month:
                    @endif
                    <strong class="text-danger">{{ number_format($categoryMonthTotal, 2) }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Date Range Summary -->
    @if($fromDate && $toDate && !$selectedHead)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bx bx-calendar-event me-2"></i>
                    Viewing entries from <strong>{{ date('M d, Y', strtotime($fromDate)) }}</strong> to <strong>{{ date('M d, Y', strtotime($toDate)) }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
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

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Debits</p>
                            <h4 class="mb-0 text-success">{{ number_format($totalDebit, 2) }}</h4>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="bx bx-arrow-from-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Credits</p>
                            <h4 class="mb-0 text-danger">{{ number_format($totalCredit, 2) }}</h4>
                        </div>
                        <div class="text-danger" style="font-size: 2rem;">
                            <i class="bx bx-arrow-to-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Running Balance</p>
                            <h4 class="mb-0" style="color: {{ $currentBalance >= 0 ? '#28a745' : '#dc3545' }}">
                                {{ number_format($currentBalance, 2) }}
                            </h4>
                        </div>
                        <div style="font-size: 2rem; color: {{ $currentBalance >= 0 ? '#28a745' : '#dc3545' }};">
                            <i class="bx bx-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Entries</p>
                            <h4 class="mb-0 text-info">{{ $entries->count() }}</h4>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="bx bx-list-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            <strong>Success!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-x-circle me-2"></i>
            <strong>Error!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Petty Cash Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bx bx-table me-2"></i>
                    All Entries
                </h5>
            </div>
            <div>
                <a href="{{ route('petty-cash.print', request()->query()) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-printer me-1"></i>
                    Print
                </a>
                <a href="{{ route('petty-cash.export', request()->query()) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-download me-1"></i>
                    Export CSV
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">S.N.</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Head/Category</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                            <th class="text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $serialNumberMap[$entry->id] }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $entry->date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $entry->description }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $entry->head }}</span>
                                </td>
                                <td class="text-end">
                                    @if($entry->debit > 0)
                                        <span class="text-success fw-bold">{{ number_format($entry->debit, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($entry->credit > 0)
                                        <span class="text-danger fw-bold">{{ number_format($entry->credit, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ number_format($balanceMap[$entry->id] ?? 0, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light" 
                                            onclick="editEntry({{ $entry->id }})"
                                            data-bs-toggle="tooltip" 
                                            title="Edit">
                                        <i class="bx bx-pencil text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light" 
                                            onclick="deleteEntry({{ $entry->id }})"
                                            data-bs-toggle="tooltip" 
                                            title="Delete">
                                        <i class="bx bx-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <p class="text-muted mb-0">
                                        <i class="bx bx-inbox" style="font-size: 2rem;"></i><br>
                                        No entries found. Click "Add Entry" to create one.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Summary (when not filtered) -->
    @if(!$selectedHead)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0">
                    <i class="bx bx-layer me-2"></i>
                    Category Breakdown
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total Spent (All Time)</th>
                                <th class="text-end">This Month</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($heads as $head)
                                @php
                                    $categorySpent = \App\Models\PettyCashLedger::where('head', $head)->sum('credit');
                                    $thisMonthSpent = \App\Models\PettyCashLedger::where('head', $head)
                                        ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
                                        ->sum('credit');
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $head }}</span>
                                    </td>
                                    <td class="text-end text-danger fw-bold">{{ number_format($categorySpent, 2) }}</td>
                                    <td class="text-end text-warning fw-bold">{{ number_format($thisMonthSpent, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('petty-cash.index', ['head' => $head]) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="bx bx-search"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No categories found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Add/Edit Entry Modal -->
<div class="modal fade" id="addEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title">
                    <i class="bx bx-plus-circle me-2"></i>
                    <span id="modalTitle">Add New Entry</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="entryForm" method="POST" action="{{ route('petty-cash.store') }}">
                @csrf
                <input type="hidden" id="methodInput" name="_method" value="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" required>
                            <span class="text-danger small" id="dateError"></span>
                        </div>
                        <div class="col-md-6">
                            <label for="head" class="form-label">Head/Category <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="head" name="head" placeholder="Enter head/category" required>
                            <span class="text-danger small" id="headError"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Enter transaction details" required></textarea>
                        <span class="text-danger small" id="descriptionError"></span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="debit" class="form-label">Debit (Cash In)</label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                <input type="number" class="form-control" id="debit" name="debit" placeholder="0.00" step="0.01" min="0" value="0">
                            </div>
                            <span class="text-danger small" id="debitError"></span>
                        </div>
                        <div class="col-md-6">
                            <label for="credit" class="form-label">Credit (Cash Out)</label>
                            <div class="input-group">
                                <span class="input-group-text">-</span>
                                <input type="number" class="form-control" id="credit" name="credit" placeholder="0.00" step="0.01" min="0" value="0">
                            </div>
                            <span class="text-danger small" id="creditError"></span>
                        </div>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Note:</strong> Enter either Debit OR Credit, not both. Debit increases balance, Credit decreases it.
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bx bx-save me-1"></i>
                        <span id="submitBtnText">Save Entry</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-bottom">
                <h5 class="modal-title">
                    <i class="bx bx-trash-alt me-2"></i>
                    Delete Entry
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-center my-3">
                    <i class="bx bx-question-mark" style="font-size: 2rem; color: #dc3545;"></i>
                </p>
                <p class="text-center">Are you sure you want to delete this entry?</p>
                <p class="text-muted text-center"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let editingId = null;

    // Export to CSV
    function exportToCSV() {
        const table = document.querySelector('table');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        let csv = [];
        rows.forEach(row => {
            let cells = Array.from(row.querySelectorAll('td, th'));
            let rowData = cells.map(cell => {
                let text = cell.textContent.trim();
                // Remove icon tags and clean up text
                text = text.replace(/<[^>]*>/g, '').trim();
                // Escape quotes and wrap in quotes if contains comma
                text = text.includes(',') ? '"' + text.replace(/"/g, '""') + '"' : text;
                return text;
            }).filter((_, i) => i < cells.length - 1); // Exclude Action column
            csv.push(rowData.join(','));
        });

        // Create and download file
        const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
        const link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', 'petty-cash-' + new Date().toISOString().split('T')[0] + '.csv');
        link.click();
    }

    // Set today's date as default on page load
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Edit entry - load data into modal
    function editEntry(id) {
        fetch(`/petty-cash/${id}/edit`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load entry');
                return response.json();
            })
            .then(data => {
                // Clear previous errors
                document.querySelectorAll('[id$="Error"]').forEach(el => el.textContent = '');
                
                // Populate form fields
                document.getElementById('date').value = data.date;
                document.getElementById('description').value = data.description;
                document.getElementById('head').value = data.head;
                document.getElementById('debit').value = data.debit || 0;
                document.getElementById('credit').value = data.credit || 0;
                
                // Update modal title and form setup for edit
                document.getElementById('modalTitle').textContent = 'Edit Entry #' + data.id;
                document.getElementById('submitBtnText').textContent = 'Update Entry';
                
                // Set form action to the update route
                document.getElementById('entryForm').action = `/petty-cash/${id}`;
                document.getElementById('methodInput').value = 'PUT';
                
                editingId = id;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('addEntryModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load entry. Please try again.');
            });
    }

    // Delete entry - show confirmation
    function deleteEntry(id) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/petty-cash/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Reset form on modal close
    document.getElementById('addEntryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('entryForm').reset();
        document.getElementById('modalTitle').textContent = 'Add New Entry';
        document.getElementById('submitBtnText').textContent = 'Save Entry';
        document.getElementById('entryForm').action = "{{ route('petty-cash.store') }}";
        document.getElementById('methodInput').value = 'POST';
        
        // Clear errors
        document.querySelectorAll('[id$="Error"]').forEach(el => el.textContent = '');
        
        // Reset head input
        document.getElementById('head').value = '';
        
        // Reset date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
        
        editingId = null;
    });

    // Handle form submission with validation
    document.getElementById('entryForm').addEventListener('submit', function(e) {
        // Clear previous errors
        document.querySelectorAll('[id$="Error"]').forEach(el => el.textContent = '');
        
        const debit = parseFloat(document.getElementById('debit').value) || 0;
        const credit = parseFloat(document.getElementById('credit').value) || 0;
        const headValue = document.getElementById('head').value.trim();
        
        if (!headValue) {
            e.preventDefault();
            alert('Please enter a category');
            return false;
        }
        
        // Validate that at least one value is entered
        if (debit === 0 && credit === 0) {
            e.preventDefault();
            alert('Please enter either a Debit or Credit amount');
            return false;
        }
        
        // Validate that both debit and credit aren't entered
        if (debit > 0 && credit > 0) {
            e.preventDefault();
            alert('Please enter either Debit OR Credit, not both');
            return false;
        }
        
        // Let form submit normally
        return true;
    });
</script>
@endpush

@push('styles')
<style>
    .table hover-effect tr {
        transition: background-color 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
    }
    
    .card {
        transition: box-shadow 0.2s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endpush
@endsection
