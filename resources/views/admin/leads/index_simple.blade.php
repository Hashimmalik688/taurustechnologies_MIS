@extends('layouts.master')

@section('title')
    Leads Management
@endsection

@section('css')
    <style>
    .table-wrapper {
        overflow-x: auto;
        position: relative;
    }

    .locked-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .locked-table thead th:nth-child(1),
    .locked-table thead th:nth-child(2),
    .locked-table thead th:nth-child(3),
    .locked-table tbody td:nth-child(1),
    .locked-table tbody td:nth-child(2),
    .locked-table tbody td:nth-child(3) {
        position: sticky;
        background: white;
        z-index: 10;
    }

    .locked-table thead th:nth-child(1) { left: 0; z-index: 11; width: 50px; }
    .locked-table thead th:nth-child(2) { left: 50px; z-index: 11; min-width: 150px; }
    .locked-table thead th:nth-child(3) { left: 200px; z-index: 11; min-width: 180px; }

    .locked-table tbody td:nth-child(1) { left: 0; }
    .locked-table tbody td:nth-child(2) { left: 50px; }
    .locked-table tbody td:nth-child(3) { left: 200px; }

    .locked-table thead th {
        background: #f8f9fa !important;
        white-space: nowrap;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.85rem 0.6rem;
    }

    .locked-table tbody td {
        font-size: 0.95rem;
        padding: 0.55rem 0.6rem;
        vertical-align: middle;
    }

    .btn-group-actions {
        display: flex;
        gap: 0.25rem;
        flex-wrap: nowrap;
    }

    .btn-group-actions .btn {
        padding: 0.32rem 0.6rem;
        font-size: 0.95rem;
    }

    /* Slightly nicer font and spacing for leads table */
    .locked-table, .locked-table th, .locked-table td {
        font-family: Inter, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Leads
        @endslot
        @slot('title')
            Management
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">All Leads Database</h4>
                    <div>
                        <a href="{{ route('leads.create') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="fas fa-plus me-1"></i> Add New Lead
                        </a>
                        <button class="btn btn-outline-secondary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import me-1"></i> Import Leads
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search Bar -->
                    <form method="GET" action="{{ route('leads.index') }}" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, SSN, carrier..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="month" class="form-select">
                                    <option value="">All Months</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="year" class="form-select">
                                    <option value="">All Years</option>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Search</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary w-100"><i class="bx bx-reset"></i> Clear</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-wrapper">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle locked-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer Name</th>
                                    <th>Actions</th>
                                    <th>Phone Number</th>
                                    <th>DOB</th>
                                    <th>Smoker</th>
                                    <th>Driving License #</th>
                                    <th>Height</th>
                                    <th>Weight</th>
                                    <th>Birth Place</th>
                                    <th>Medical Issue</th>
                                    <th>Medications</th>
                                    <th>Doc Name</th>
                                    <th>S.S.N #</th>
                                    <th>Street Address</th>
                                    <th>State</th>
                                    <th>Zip Code</th>
                                    <th>Carrier Name</th>
                                    <th>Coverage Amount</th>
                                    <th>Monthly Premium</th>
                                    <th>Beneficiary</th>
                                    <th>Emergency Contact</th>
                                    <th>Initial Draft Date</th>
                                    <th>Future Draft Date</th>
                                    <th>Bank Name</th>
                                    <th>Acc Type</th>
                                    <th>Routing Number</th>
                                    <th>Acc Number</th>
                                    <th>Card Info</th>
                                    <th>Policy Type</th>
                                    <th>Source</th>
                                    <th>Closer Name</th>
                                    <th>Acc Verified By</th>
                                    <th>Bank Balance / SS Amount</th>
                                    <th>SS Date</th>
                                    <th>Preset Line #</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td><strong>{{ $lead->id }}</strong></td>
                                        <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                                        <td>
                                            @php
                                                $zoomNumber = preg_replace('/[^\d\+]/', '', $lead->phone_number);
                                                $callUrl = 'zoomphonecall://' . urlencode($zoomNumber);
                                            @endphp
                                            <div class="btn-group-actions">
                                                <button onclick="window.location.href='{{ $callUrl }}'" class="btn btn-outline-secondary btn-sm" title="Call">
                                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                                </button>
                                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-info btn-sm" title="View">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-{{ $lead->id }}" title="Delete">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="delete-{{ $lead->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Delete</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete <strong>{{ $lead->cn_name }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('leads.delete', $lead->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->smoker ? 'Yes' : 'No' }}</td>
                                        <td>{{ $lead->driving_license_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->height ?? 'N/A' }}</td>
                                        <td>{{ $lead->weight ?? 'N/A' }}</td>
                                        <td>{{ $lead->birth_place ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($lead->medical_issue ?? 'N/A', 30) }}</td>
                                        <td>{{ Str::limit($lead->medications ?? 'N/A', 30) }}</td>
                                        <td>{{ $lead->doctor_name ?? 'N/A' }}</td>
                                        <td>{{ $lead->ssn ? '***-**-' . substr($lead->ssn, -4) : 'N/A' }}</td>
                                        <td>{{ Str::limit($lead->address ?? 'N/A', 40) }}</td>
                                        <td>{{ $lead->state ?? 'N/A' }}</td>
                                        <td>{{ $lead->zip_code ?? 'N/A' }}</td>
                                        <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                        <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                                        <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                        <td>{{ $lead->beneficiary ?? 'N/A' }}</td>
                                        <td>{{ $lead->emergency_contact ?? 'N/A' }}</td>
                                        <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->bank_name ?? 'N/A' }}</td>
                                        <td>{{ $lead->account_type ?? 'N/A' }}</td>
                                        <td>{{ $lead->routing_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->acc_number ?? 'N/A' }}</td>
                                        <td>{{ $lead->card_number ? '****' . substr($lead->card_number, -4) : 'N/A' }}</td>
                                        <td>{{ $lead->policy_type ?? 'N/A' }}</td>
                                        <td>{{ $lead->source ?? 'N/A' }}</td>
                                        <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                        <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                                        <td>{{ $lead->bank_balance ? '$' . number_format($lead->bank_balance, 2) : ($lead->ss_amount ? '$' . number_format($lead->ss_amount, 2) : 'N/A') }}</td>
                                        <td>{{ $lead->ss_date ? \Carbon\Carbon::parse($lead->ss_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $lead->preset_line ?? 'N/A' }}</td>
                                        <td>
                                            <div contenteditable="true" class="editable-comment" data-lead-id="{{ $lead->id }}" style="min-width: 150px; max-width: 300px; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">{{ $lead->comments ?? 'Click to add...' }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="37" class="text-center py-4">
                                            <i class="bx bx-user-plus fs-1 text-muted"></i>
                                            <p class="mb-0 text-muted">No leads available. Add or import leads to get started.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $leads->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Leads</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload Excel File</label>
                            <input type="file" class="form-control" name="import_file" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Accepted formats: .xlsx, .xls, .csv (Max: 2MB)</small>
                        </div>
                        <div class="alert alert-info">
                            <small><strong>Note:</strong> Excel file should have columns: Phone Number, Customer Name, DOB, Gender, Address, SSN, etc.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-upload me-1"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Editable comments functionality
    document.querySelectorAll('.editable-comment').forEach(comment => {
        comment.addEventListener('blur', function() {
            const leadId = this.dataset.leadId;
            const newComment = this.textContent.trim();
            
            if (newComment === 'Click to add...') return;

            fetch(`/leads/${leadId}/update-comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ comments: newComment })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.style.borderColor = '#22c55e';
                    setTimeout(() => {
                        this.style.borderColor = '#ddd';
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error updating comment:', error);
                this.style.borderColor = '#ef4444';
            });
        });

        comment.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.blur();
            }
        });
    });
});
</script>
@endsection
