{{-- File: resources/views/salary/records.blade.php --}}

@extends('layouts.master')

@section('title', 'Salary Records')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            Salary Records
                        </h3>
                        <div class="d-flex gap-2">
                            <a href="{{ route('salary.index') }}" class="btn btn-primary">
                                <i class="fas fa-calculator me-1"></i>
                                Calculate New
                            </a>
                            <button class="btn btn-outline-success" onclick="exportRecords()">
                                <i class="fas fa-download me-1"></i>
                                Export
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Filters --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-light">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-filter me-2"></i>
                                            Filters
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('salary.records') }}" id="filterForm">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Employee</label>
                                                    <select name="employee" class="form-select">
                                                        <option value="">All Employees</option>
                                                        @foreach (\App\Models\User::role('Employee')->excludePartners()->orderBy('name')->get() as $emp)
                                                            <option value="{{ $emp->id }}"
                                                                {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                                                {{ $emp->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Month</label>
                                                    <select name="month" class="form-select">
                                                        <option value="">All Months</option>
                                                        @for ($i = 1; $i <= 12; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ request('month') == $i ? 'selected' : '' }}>
                                                                {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Year</label>
                                                    <select name="year" class="form-select">
                                                        <option value="">All Years</option>
                                                        @for ($i = date('Y'); $i >= date('Y') - 3; $i--)
                                                            <option value="{{ $i }}"
                                                                {{ request('year') == $i ? 'selected' : '' }}>
                                                                {{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="">All Status</option>
                                                        <option value="draft"
                                                            {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                                        </option>
                                                        <option value="calculated"
                                                            {{ request('status') == 'calculated' ? 'selected' : '' }}>
                                                            Calculated</option>
                                                        <option value="approved"
                                                            {{ request('status') == 'approved' ? 'selected' : '' }}>
                                                            Approved</option>
                                                        <option value="paid"
                                                            {{ request('status') == 'paid' ? 'selected' : '' }}>Paid
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-search"></i> Filter
                                                        </button>
                                                        <a href="{{ route('salary.records') }}"
                                                            class="btn btn-outline-secondary">
                                                            <i class="fas fa-times"></i> Clear
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Summary Cards --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">{{ $salaryRecords->total() }}</h5>
                                        <p class="card-text small">Total Records</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-success">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">
                                            Rs{{ number_format($salaryRecords->sum('net_salary'), 2) }}
                                        </h5>
                                        <p class="card-text small">Total Net Salaries</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-info">
                                    <div class="card-body">
                                        <h5 class="card-title text-info">
                                            Rs{{ number_format($salaryRecords->sum('total_bonus'), 2) }}
                                        </h5>
                                        <p class="card-text small">Total Bonuses</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-warning">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">
                                            Rs{{ number_format($salaryRecords->sum('total_deductions'), 2) }}
                                        </h5>
                                        <p class="card-text small">Total Deductions</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Records Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Period</th>
                                        <th>Basic Salary</th>
                                        <th>Sales</th>
                                        <th>Bonus</th>
                                        <th>Gross</th>
                                        <th>Deductions</th>
                                        <th>Net Salary</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salaryRecords as $record)
                                        <tr class="salary-record-row" data-record-id="{{ $record->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <img src="{{ $record->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name) }}"
                                                            alt="Avatar" class="rounded-circle"
                                                            style="width: 32px; height: 32px;">
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $record->user->name }}</div>
                                                        <small class="text-muted">{{ $record->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $record->month_name }} {{ $record->salary_year }}
                                                </div>
                                                <small class="text-muted">
                                                    Calculated:
                                                    {{ $record->calculated_at ? $record->calculated_at->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">
                                                    Rs{{ number_format($record->basic_salary, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span
                                                        class="badge {{ $record->actual_sales >= $record->target_sales ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $record->actual_sales }}/{{ $record->target_sales }}
                                                    </span>
                                                    @if ($record->extra_sales > 0)
                                                        <br><small class="text-success">+{{ $record->extra_sales }}
                                                            extra</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($record->total_bonus > 0)
                                                    <span class="fw-bold text-success">
                                                        Rs{{ number_format($record->total_bonus, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Rs0.00</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">
                                                    Rs{{ number_format($record->gross_salary, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($record->total_deductions > 0)
                                                    <span class="text-danger fw-bold">
                                                        -Rs{{ number_format($record->total_deductions, 2) }}
                                                    </span>
                                                    @if ($record->deductions->count() > 0)
                                                        <br><small class="text-muted">{{ $record->deductions->count() }}
                                                            item(s)</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Rs0.00</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark fs-6">
                                                    Rs{{ number_format($record->net_salary, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $record->status == 'paid' ? 'success' : ($record->status == 'approved' ? 'info' : ($record->status == 'calculated' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                                @if ($record->status == 'paid' && $record->paid_at)
                                                    <br><small
                                                        class="text-muted">{{ $record->paid_at->format('M d') }}</small>
                                                @elseif($record->status == 'approved' && $record->approved_at)
                                                    <br><small
                                                        class="text-muted">{{ $record->approved_at->format('M d') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('salary.show', $record) }}">
                                                                <i class="fas fa-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="addDeduction({{ $record->id }})">
                                                                <i class="fas fa-minus me-2"></i>Add Deduction
                                                            </button>
                                                        </li>
                                                        @if ($record->status == 'calculated')
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item text-info"
                                                                    onclick="approveRecord({{ $record->id }})">
                                                                    <i class="fas fa-check me-2"></i>Approve
                                                                </button>
                                                            </li>
                                                        @endif
                                                        @if ($record->status == 'approved')
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item text-success"
                                                                    onclick="markAsPaid({{ $record->id }})">
                                                                    <i class="fas fa-money-bill me-2"></i>Mark as Paid
                                                                </button>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item text-primary"
                                                                onclick="downloadPayslip({{ $record->id }})">
                                                                <i class="fas fa-file-pdf me-2"></i>Download Payslip
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                                                    <p class="mb-0">No salary records found.</p>
                                                    <a href="{{ route('salary.index') }}" class="btn btn-primary mt-2">
                                                        <i class="fas fa-calculator me-1"></i>
                                                        Calculate First Salary
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if ($salaryRecords->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $salaryRecords->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Deduction Modal --}}
    <div class="modal fade" id="addDeductionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDeductionForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="deduction_salary_record_id" name="salary_record_id">

                        <div class="mb-3">
                            <label for="deduction_type" class="form-label">Type</label>
                            <select id="deduction_type" name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="tax">Tax</option>
                                <option value="insurance">Insurance</option>
                                <option value="loan">Loan</option>
                                <option value="absence">Absence</option>
                                <option value="advance">Advance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="deduction_description" class="form-label">Description</label>
                            <input type="text" id="deduction_description" name="description" class="form-control"
                                required>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <label for="deduction_amount" class="form-label">Amount</label>
                                <input type="number" id="deduction_amount" name="amount" class="form-control"
                                    step="0.01" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_percentage"
                                        name="is_percentage">
                                    <label class="form-check-label" for="is_percentage">
                                        Percentage
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deduction_notes" class="form-label">Notes (Optional)</label>
                            <textarea id="deduction_notes" name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Deduction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addDeduction(recordId) {
            document.getElementById('deduction_salary_record_id').value = recordId;
            document.getElementById('addDeductionForm').reset();
            document.getElementById('deduction_salary_record_id').value = recordId; // Reset clears this, so set again
            new bootstrap.Modal(document.getElementById('addDeductionModal')).show();
        }

        function approveRecord(recordId) {
            if (confirm('Are you sure you want to approve this salary record?')) {
                fetch(`/salary/records/${recordId}/approve`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error approving record');
                        }
                    });
            }
        }

        function markAsPaid(recordId) {
            if (confirm('Are you sure you want to mark this salary as paid?')) {
                fetch(`/salary/records/${recordId}/mark-paid`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error marking as paid');
                        }
                    });
            }
        }

        function downloadPayslip(recordId) {
            window.open(`/salary/records/${recordId}/payslip`, '_blank');
        }

        function exportRecords() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        // Handle deduction form submission
        document.getElementById('addDeductionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const recordId = formData.get('salary_record_id');

            fetch(`/salary/records/${recordId}/deductions`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('addDeductionModal')).hide();
                        location.reload();
                    } else {
                        alert('Error adding deduction');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding deduction');
                });
        });

        // Auto-submit filter form on change
        document.querySelectorAll('#filterForm select').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
@endsection
