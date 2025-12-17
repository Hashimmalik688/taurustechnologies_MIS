@extends('layouts.master')

@section('title')
    Attendance Overview
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
            Overview
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

    <!-- Enhanced Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date"
                            value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date"
                            value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search Employee</label>
                        <input type="text" class="form-control" name="search_name"
                            placeholder="Enter employee name..." value="{{ $searchName ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All</option>
                            <option value="present" {{ ($searchStatus ?? '') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="late" {{ ($searchStatus ?? '') == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="absent" {{ ($searchStatus ?? '') == 'absent' ? 'selected' : '' }}>Absent</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-magnify"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="mdi mdi-refresh"></i> Reset Filters
                        </a>
                        <a href="{{ route('attendance.history') }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="mdi mdi-history"></i> View History
                        </a>
                        <button type="button" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                            <i class="mdi mdi-plus"></i> Manual Entry
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Employees</span>
                            <h4 class="mb-3">{{ $totalEmployees }}</h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="total-employees-chart" data-colors='["--bs-primary"]'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Present</span>
                            <h4 class="mb-3 text-success">{{ $presentCount }}</h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="badge badge-soft-success rounded-pill fs-12">
                                    {{ $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Late Arrivals</span>
                            <h4 class="mb-3 text-warning">{{ $lateCount }}</h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="badge badge-soft-warning rounded-pill fs-12">
                                    {{ $totalEmployees > 0 ? round(($lateCount / $totalEmployees) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Absent</span>
                            <h4 class="mb-3 text-danger">{{ $absentCount }}</h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="badge badge-soft-danger rounded-pill fs-12">
                                    {{ $totalEmployees > 0 ? round(($absentCount / $totalEmployees) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Attendance Details -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        Attendance Details
                        @if($startDate == $endDate)
                            - {{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }}
                        @else
                            - {{ \Carbon\Carbon::parse($startDate)->format('M j') }} to {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}
                        @endif
                        <span class="badge badge-soft-primary ms-2">{{ $attendanceDetails->count() }} Records</span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0" id="attendance-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Status</th>
                                    <th>Working Hours</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceDetails as $attendance)
                                    <tr>
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
                                                    <p class="text-muted mb-0">{{ $attendance->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($attendance->login_time)
                                                <span class="badge badge-soft-info">
                                                    {{ $attendance->login_time->format('h:i A') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attendance->logout_time)
                                                <span class="badge badge-soft-secondary">
                                                    {{ $attendance->logout_time->format('h:i A') }}
                                                </span>
                                            @else
                                                <span class="text-muted">Still working</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attendance->status === 'present')
                                                <span class="badge badge-soft-success">On Time</span>
                                            @elseif($attendance->status === 'late')
                                                <span class="badge badge-soft-warning">Late</span>
                                            @elseif($attendance->status === 'absent')
                                                <span class="badge badge-soft-danger">Absent</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attendance->login_time && $attendance->logout_time)
                                                @php
                                                    $workingHours = $attendance->login_time->diffInMinutes(
                                                        $attendance->logout_time,
                                                    );
                                                    $hours = floor($workingHours / 60);
                                                    $minutes = $workingHours % 60;
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m
                                            @elseif ($attendance->login_time)
                                                @php
                                                    $workingMinutes = $attendance->login_time->diffInMinutes(now());
                                                    $hours = floor($workingMinutes / 60);
                                                    $minutes = $workingMinutes % 60;
                                                @endphp
                                                <span class="text-info">{{ $hours }}h {{ $minutes }}m</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
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
                                                        <i class="mdi mdi-eye font-size-16 text-success me-1"></i> View
                                                        Details
                                                    </a>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="editAttendance({{ $attendance->id }})">
                                                        <i class="mdi mdi-pencil font-size-16 text-primary me-1"></i> Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-account-off-outline font-size-48 mb-3 d-block"></i>
                                                No attendance records found for this date.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absent Employees & Weekly Trends -->
        <div class="col-xl-4">
            <!-- Absent Employees -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        Absent Today
                        <span class="badge badge-soft-danger ms-2">{{ $absentEmployees->count() }}</span>
                    </h4>
                </div>
                <div class="card-body">
                    @forelse($absentEmployees as $employee)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                    {{ substr($employee->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $employee->name }}</h6>
                                <p class="text-muted mb-0 fs-13">{{ $employee->email }}</p>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-muted dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-horizontal"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#"
                                        onclick="markManualAttendance({{ $employee->id }})">
                                        <i class="mdi mdi-plus me-1"></i> Mark Present
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="mdi mdi-check-circle-outline font-size-24 mb-2 d-block text-success"></i>
                            All employees are present!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Weekly Trends -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Weekly Attendance Trend</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Present</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weeklyStats as $stat)
                                    <tr>
                                        <td>{{ $stat['date'] }}</td>
                                        <td>
                                            <span class="badge badge-soft-primary">
                                                {{ $stat['present'] + $stat['late'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" style="width: {{ $stat['percentage'] }}%">
                                                </div>
                                            </div>
                                            <span class="fs-12 text-muted">{{ $stat['percentage'] }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualEntryModalLabel">Manual Attendance Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="manualEntryForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="employee_select" class="form-label">Employee</label>
                            <select class="form-select" id="employee_select" name="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach ($allEmployees as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="attendance_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="attendance_date" name="date" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="login_time" class="form-label">Login Time</label>
                                    <input type="time" class="form-control" id="login_time" name="login_time"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logout_time" class="form-label">Logout Time</label>
                                    <input type="time" class="form-control" id="logout_time" name="logout_time">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status_select" class="form-label">Status</label>
                            <select class="form-select" id="status_select" name="status" required>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        // Initialize DataTable
        $('#attendance-table').DataTable({
            "pageLength": 25,
            "responsive": true,
            "order": [
                [1, "asc"]
            ], // Sort by login time
            "columnDefs": [{
                    "orderable": false,
                    "targets": [5]
                } // Disable sorting for action column
            ]
        });

        // Filter by date
        function filterByDate() {
            const date = document.getElementById('attendance-date').value;
            window.location.href = `{{ route('attendance.index') }}?date=${date}`;
        }

        // Manual entry form submission
        document.getElementById('manualEntryForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route('attendance.mark-manual') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving attendance.');
                });
        });

        // Quick mark present for absent employees
        function markManualAttendance(userId) {
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0');

            document.getElementById('employee_select').value = userId;
            document.getElementById('attendance_date').value = '{{ date("Y-m-d") }}';
            document.getElementById('login_time').value = currentTime;
            document.getElementById('status_select').value = 'late';

            new bootstrap.Modal(document.getElementById('manualEntryModal')).show();
        }
    </script>
@endsection
