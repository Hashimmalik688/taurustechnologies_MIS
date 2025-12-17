@extends('layouts.master')

@section('title', 'Dock Section - Employee Fines')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDockModal">
                    <i class="mdi mdi-plus"></i> Add Dock Record
                </button>
            </div>
            <h4 class="page-title">Dock Section - Employee Fines</h4>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-cash-remove widget-icon bg-danger-lighten text-danger"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Total Docked">Total Docked</h5>
                <h3 class="mt-3 mb-3">Rs {{ number_format($stats['total_docked'], 2) }}</h3>
                <p class="mb-0 text-muted">
                    <span class="text-nowrap">For {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-file-document widget-icon bg-warning-lighten text-warning"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Total Records">Total Records</h5>
                <h3 class="mt-3 mb-3">{{ $stats['total_records'] }}</h3>
                <p class="mb-0 text-muted">
                    <span class="text-success me-2">{{ $stats['active_records'] }}</span>
                    <span class="text-nowrap">Active</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon bg-info-lighten text-info"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0">Employees</h5>
                <h3 class="mt-3 mb-3">{{ $employees->count() }}</h3>
                <p class="mb-0 text-muted">
                    <span class="text-nowrap">Total Active</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('dock.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Employee</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Dock Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Dock Records</h4>

                @if ($dockRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Employee</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Dock Date</th>
                                <th>Docked By</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dockRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>
                                    <a href="{{ route('dock.history', $record->user_id) }}" class="text-body fw-bold">
                                        {{ $record->user->name }}
                                    </a>
                                </td>
                                <td><strong>Rs {{ number_format($record->amount, 2) }}</strong></td>
                                <td>{{ Str::limit($record->reason, 40) }}</td>
                                <td>{{ $record->dock_date->format('d M Y') }}</td>
                                <td>{{ $record->dockedBy->name }}</td>
                                <td>
                                    @if ($record->status === 'active')
                                        <span class="badge bg-warning">Active</span>
                                    @elseif ($record->status === 'applied')
                                        <span class="badge bg-success">Applied</span>
                                    @else
                                        <span class="badge bg-secondary">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="editDock({{ json_encode($record) }})"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editDockModal">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    
                                    @if ($record->status === 'active')
                                    <form action="{{ route('dock.cancel', $record->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-warning" 
                                                onclick="return confirm('Cancel this dock record?')">
                                            <i class="mdi mdi-cancel"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('dock.destroy', $record->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Delete this dock record permanently?')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @if ($record->notes)
                            <tr>
                                <td colspan="8" class="text-muted small ps-5">
                                    <i class="mdi mdi-note-text"></i> {{ $record->notes }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $dockRecords->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="mdi mdi-information-outline" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">No dock records found for the selected period.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Dock Modal -->
<div class="modal fade" id="addDockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dock.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Dock Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee *</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Amount (Rs) *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Dock Date *</label>
                            <input type="date" name="dock_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Reason *</label>
                            <textarea name="reason" class="form-control" rows="3" required 
                                      placeholder="Enter reason for docking..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="2" 
                                      placeholder="Optional additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check"></i> Add Dock Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Dock Modal -->
<div class="modal fade" id="editDockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editDockForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Edit Dock Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <input type="text" id="edit_employee_name" class="form-control" disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Amount (Rs) *</label>
                            <input type="number" id="edit_amount" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Dock Date *</label>
                            <input type="date" id="edit_dock_date" name="dock_date" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Status *</label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="applied">Applied</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Reason *</label>
                            <textarea id="edit_reason" name="reason" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Additional Notes</label>
                            <textarea id="edit_notes" name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="mdi mdi-content-save"></i> Update Dock Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function editDock(record) {
    document.getElementById('editDockForm').action = '/dock/' + record.id;
    document.getElementById('edit_employee_name').value = record.user.name;
    document.getElementById('edit_amount').value = record.amount;
    document.getElementById('edit_dock_date').value = record.dock_date;
    document.getElementById('edit_status').value = record.status;
    document.getElementById('edit_reason').value = record.reason;
    document.getElementById('edit_notes').value = record.notes || '';
}
</script>
@endsection
