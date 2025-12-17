@extends('layouts.master')

@section('title')
    Employee Attendance Report
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Attendance
        @endslot
        @slot('title')
            Employee Report
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

    {{-- Filter Section --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-filter me-2"></i>
                        Filter Options
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('attendance.employee-report') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">
                                        <i class="mdi mdi-account me-1"></i>
                                        Select Employee
                                    </label>
                                    <select id="employee_id" name="employee_id" class="form-select">
                                        <option value="">All Employees</option>
                                        @if (isset($employees))
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">
                                        <i class="mdi mdi-calendar-start me-1"></i>
                                        Start Date
                                    </label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ request('start_date', date('Y-m-01')) }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">
                                        <i class="mdi mdi-calendar-end me-1"></i>
                                        End Date
                                    </label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ request('end_date', date('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="mdi mdi-magnify me-1"></i>
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                    <i class="mdi mdi-check-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Present</p>
                            <h4 class="mb-0">{{ $statistics['total_present'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                    <i class="mdi mdi-close-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Absent</p>
                            <h4 class="mb-0">{{ $statistics['total_absent'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                    <i class="mdi mdi-clock-outline"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Avg Hours/Day</p>
                            <h4 class="mb-0">{{ number_format($statistics['avg_hours'] ?? 0, 1) }}h</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                    <i class="mdi mdi-timer-outline"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Hours</p>
                            <h4 class="mb-0">{{ number_format($statistics['total_hours'] ?? 0, 1) }}h</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-table me-2"></i>
                        Attendance Records
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="exportToCsv()">
                            <i class="mdi mdi-file-delimited me-1"></i>
                            Export CSV
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="exportToPdf()">
                            <i class="mdi mdi-file-pdf me-1"></i>
                            Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-striped table-hover table-bordered dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($attendanceRecords) && count($attendanceRecords) > 0)
                                    @foreach ($attendanceRecords as $index => $record)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-account-circle me-2 fs-5 text-primary"></i>
                                                    {{ $record->user->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    <i class="mdi mdi-clock-in me-1"></i>
                                                    {{ $record->formatted_login_time }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning-subtle text-warning">
                                                    <i class="mdi mdi-clock-out me-1"></i>
                                                    {{ $record->formatted_logout_time }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $record->working_hours }}h</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusInfo = $record->status_with_color;
                                                    $badgeClass = match ($statusInfo['status']) {
                                                        'present' => 'bg-success',
                                                        'absent' => 'bg-danger',
                                                        'leave' => 'bg-warning',
                                                        'late' => 'bg-orange',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $statusInfo['label'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="mdi mdi-alert-circle-outline me-2 fs-4 text-muted"></i>
                                            <span class="text-muted">No attendance records found for the selected criteria.</span>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#attendanceTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [
                    [2, 'desc']
                ], // Sort by date descending
                pageLength: 25,
                language: {
                    emptyTable: "No attendance records found for the selected criteria.",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    infoEmpty: "Showing 0 to 0 of 0 records",
                    infoFiltered: "(filtered from _MAX_ total records)",
                    lengthMenu: "Show _MENU_ records",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        });

        // Export to CSV function
        function exportToCsv() {
            const table = $('#attendanceTable').DataTable();
            table.button('.buttons-csv').trigger();
        }

        // Export to PDF function
        function exportToPdf() {
            const table = $('#attendanceTable').DataTable();
            table.button('.buttons-pdf').trigger();
        }

        // Date validation
        document.getElementById('end_date').addEventListener('change', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = this.value;

            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                alert('End date cannot be before start date.');
                this.value = startDate;
            }
        });

        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDate = document.getElementById('end_date').value;

            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                alert('Start date cannot be after end date.');
                document.getElementById('end_date').value = startDate;
            }
        });
    </script>
@endsection

@section('css')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .avatar-sm {
            height: 3rem;
            width: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        .bg-success-subtle {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-orange {
            background-color: #fd7e14;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            border: 1px solid rgba(102, 126, 234, 0.3) !important;
            color: #667eea !important;
        }

        .dt-buttons .btn {
            margin-right: 5px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
    </style>
@endsection
