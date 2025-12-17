@extends('layouts.master')

@section('title')
    Attendance History
@endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Attendance
        @endslot
        @slot('title')
            History & Reports
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters and Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-filter-variant me-2"></i>Filters & Summary
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary" onclick="exportData()">
                                    <i class="mdi mdi-download"></i> Export
                                </button>
                                <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                                    <i class="mdi mdi-arrow-left"></i> Back to Today
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('attendance.history') }}" id="filterForm">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">Employee</label>
                                <select class="form-select" name="user_id" id="user_id">
                                    <option value="">All Employees</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="">All Status</option>
                                    <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('attendance.history') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-info dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="mdi mdi-calendar-clock"></i> Quick Periods
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setDateRange('today')">Today</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setDateRange('yesterday')">Yesterday</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setDateRange('thisWeek')">This Week</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setDateRange('lastWeek')">Last Week</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setDateRange('thisMonth')">This Month</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setDateRange('lastMonth')">Last Month</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Summary Statistics -->
                                @if (isset($summaryStats))
                                    <div class="d-flex justify-content-end">
                                        <div class="text-end">
                                            <div class="d-flex gap-4">
                                                <div>
                                                    <div class="text-muted small">Total Records</div>
                                                    <div class="fw-bold">{{ $summaryStats['total_records'] }}</div>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Present</div>
                                                    <div class="fw-bold text-success">{{ $summaryStats['present'] }}</div>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Late</div>
                                                    <div class="fw-bold text-warning">{{ $summaryStats['late'] }}</div>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Avg Login</div>
                                                    <div class="fw-bold text-info">{{ $summaryStats['avg_login_time'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                Attendance Records
                                @if ($userId)
                                    - {{ $users->find($userId)->name ?? 'Unknown Employee' }}
                                @endif
                                <span class="badge badge-soft-primary ms-2">{{ $attendances->total() }} Total</span>
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="mdi mdi-dots-vertical"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="printTable()">
                                                <i class="mdi mdi-printer me-2"></i>Print Table
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportData()">
                                                <i class="mdi mdi-download me-2"></i>Export Data
                                            </a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#bulkActionModal">
                                                <i class="mdi mdi-checkbox-multiple-marked me-2"></i>Bulk Actions
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0 table-hover" id="historyTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Login Time</th>
                                        <th>Logout Time</th>
                                        <th>Working Hours</th>
                                        <th>Status</th>
                                        <th>IP Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $attendance)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input attendance-checkbox" type="checkbox"
                                                        value="{{ $attendance->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $attendance->date->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-3">
                                                        <span
                                                            class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            {{ substr($attendance->user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $attendance->user->name }}</h6>
                                                        <p class="text-muted mb-0 fs-13">{{ $attendance->user->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-info fs-12">
                                                    {{ $attendance->login_time ? $attendance->login_time->format('h:i A') : '' }}
                                                </span>
                                                <div class="text-muted fs-13">
                                                    {{ $attendance->login_time ? $attendance->login_time->format('D, M j') : '' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($attendance->logout_time)
                                                    <span class="badge badge-soft-secondary fs-12">
                                                        {{ $attendance->logout_time->format('h:i A') }}
                                                    </span>
                                                    <div class="text-muted fs-13">
                                                        {{ $attendance->logout_time->format('D, M j') }}
                                                    </div>
                                                @else
                                                    <span class="text-muted fs-13">
                                                        @if ($attendance->date->isToday())
                                                            Still working
                                                        @else
                                                            Not recorded
                                                        @endif
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($attendance->logout_time)
                                                    @php
                                                        $workingMinutes = $attendance->login_time->diffInMinutes(
                                                            $attendance->logout_time,
                                                        );
                                                        $hours = floor($workingMinutes / 60);
                                                        $minutes = $workingMinutes % 60;
                                                        $isOvertime = $workingMinutes > 480; // More than 8 hours
                                                    @endphp
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold {{ $isOvertime ? 'text-success' : '' }}">
                                                            {{ $hours }}h {{ $minutes }}m
                                                        </span>
                                                        @if ($isOvertime)
                                                            <small class="text-success">
                                                                <i class="mdi mdi-clock-plus-outline"></i>
                                                                +{{ floor(($workingMinutes - 480) / 60) }}h
                                                                {{ ($workingMinutes - 480) % 60 }}m
                                                            </small>
                                                        @endif
                                                    </div>
                                                @elseif($attendance->date->isToday())
                                                    @php
                                                        $currentMinutes = $attendance->login_time->diffInMinutes(now());
                                                        $currentHours = floor($currentMinutes / 60);
                                                        $currentMins = $currentMinutes % 60;
                                                    @endphp
                                                    <span class="text-info">
                                                        {{ $currentHours }}h {{ $currentMins }}m
                                                        <small class="d-block text-muted">ongoing</small>
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($attendance->status === 'present')
                                                    <span class="badge badge-soft-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>On Time
                                                    </span>
                                                @elseif($attendance->status === 'late')
                                                    <span class="badge badge-soft-warning">
                                                        <i class="mdi mdi-clock-alert me-1"></i>Late
                                                    </span>
                                                @elseif($attendance->status === 'absent')
                                                    <span class="badge badge-soft-danger">
                                                        <i class="mdi mdi-close-circle me-1"></i>Absent
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <code class="fs-13">{{ $attendance->ip_address }}</code>
                                                    @if ($attendance->ip_address === 'Manual Entry' || $attendance->ip_address === 'Manual Entry by Admin')
                                                        <small class="text-warning">
                                                            <i class="mdi mdi-hand-okay"></i> Manual
                                                        </small>
                                                    @else
                                                        <small class="text-success">
                                                            <i class="mdi mdi-wifi"></i> Network
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle card-drop"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="{{ route('attendance.employee-report', $attendance->user_id) }}">
                                                            <i
                                                                class="mdi mdi-account-details font-size-16 text-success me-1"></i>
                                                            View Employee Report
                                                        </a>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="viewDetails({{ $attendance->id }})">
                                                            <i class="mdi mdi-eye font-size-16 text-info me-1"></i> View
                                                            Details
                                                        </a>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="editRecord({{ $attendance->id }})">
                                                            <i class="mdi mdi-pencil font-size-16 text-primary me-1"></i>
                                                            Edit Record
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="deleteRecord({{ $attendance->id }})">
                                                            <i class="mdi mdi-delete font-size-16 me-1"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="text-muted">
                                    Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }}
                                    of {{ $attendances->total() }} results
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-end">
                                    {{ $attendances->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="mdi mdi-calendar-remove-outline font-size-48 mb-3 d-block"></i>
                                <h5>No attendance records found</h5>
                                <p>Try adjusting your filters or date range to see more results.</p>
                                <a href="{{ route('attendance.history') }}" class="btn btn-primary">
                                    <i class="mdi mdi-refresh"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Select an action to perform on selected attendance records:</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="bulkExport()">
                            <i class="mdi mdi-download me-2"></i>Export Selected
                        </button>
                        <button class="btn btn-outline-info" onclick="bulkUpdateStatus()">
                            <i class="mdi mdi-pencil me-2"></i>Update Status
                        </button>
                        <button class="btn btn-outline-danger" onclick="bulkDelete()">
                            <i class="mdi mdi-delete me-2"></i>Delete Selected
                        </button>
                    </div>
                    <div id="selected-count" class="text-muted mt-2">
                        No records selected
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        // Initialize DataTable
        $('#historyTable').DataTable({
            "pageLength": 25,
            "responsive": true,
            "order": [
                [1, "desc"]
            ], // Sort by date desc
            "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 8]
                } // Disable sorting for checkbox and action columns
            ],
            "paging": false, // Use Laravel pagination instead
            "info": false,
            "searching": true
        });

        // Date range shortcuts
        function setDateRange(period) {
            const today = new Date();
            let startDate, endDate;

            switch (period) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0];
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    startDate = endDate = yesterday.toISOString().split('T')[0];
                    break;
                case 'thisWeek':
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay());
                    startDate = startOfWeek.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
                case 'lastWeek':
                    const lastWeekEnd = new Date(today);
                    lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                    const lastWeekStart = new Date(lastWeekEnd);
                    lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                    startDate = lastWeekStart.toISOString().split('T')[0];
                    endDate = lastWeekEnd.toISOString().split('T')[0];
                    break;
                case 'thisMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
                case 'lastMonth':
                    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                    startDate = lastMonth.toISOString().split('T')[0];
                    endDate = lastMonthEnd.toISOString().split('T')[0];
                    break;
            }

            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
            document.getElementById('filterForm').submit();
        }

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.attendance-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        document.querySelectorAll('.attendance-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.attendance-checkbox:checked').length;
            const countElement = document.getElementById('selected-count');
            if (selected > 0) {
                countElement.textContent = `${selected} record(s) selected`;
                countElement.className = 'text-primary mt-2';
            } else {
                countElement.textContent = 'No records selected';
                countElement.className = 'text-muted mt-2';
            }
        }

        // Export functionality
        function exportData() {
            const form = document.getElementById('filterForm');
            const exportForm = form.cloneNode(true);
            exportForm.action = '{{ route('attendance.export') }}';
            exportForm.method = 'POST';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            exportForm.appendChild(csrfInput);

            document.body.appendChild(exportForm);
            exportForm.submit();
            document.body.removeChild(exportForm);
        }

        // Print table
        function printTable() {
            window.print();
        }

        // Record actions
        function viewDetails(id) {
            // Implementation for viewing details
            alert('View details for record ID: ' + id);
        }

        function editRecord(id) {
            // Implementation for editing record
            alert('Edit record ID: ' + id);
        }

        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                // Implementation for deleting record
                alert('Delete record ID: ' + id);
            }
        }

        // Bulk actions
        function bulkExport() {
            const selected = getSelectedIds();
            if (selected.length === 0) {
                alert('Please select records to export');
                return;
            }
            // Implementation for bulk export
            alert('Export ' + selected.length + ' records');
        }

        function bulkUpdateStatus() {
            const selected = getSelectedIds();
            if (selected.length === 0) {
                alert('Please select records to update');
                return;
            }
            // Implementation for bulk status update
            alert('Update status for ' + selected.length + ' records');
        }

        function bulkDelete() {
            const selected = getSelectedIds();
            if (selected.length === 0) {
                alert('Please select records to delete');
                return;
            }
            if (confirm(`Are you sure you want to delete ${selected.length} attendance records?`)) {
                // Implementation for bulk delete
                alert('Delete ' + selected.length + ' records');
            }
        }

        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.attendance-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }
    </script>
@endsection
