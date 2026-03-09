@use('App\Support\Roles')
@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Attendance Overview')

@section('css')
<link href="{{ URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@include('partials.pipeline-dashboard-styles')
@include('partials.sl-filter-assets')
<style>
    /* ── Attendance Overrides ── */
    .att-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .att-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .att-hdr h4 i { color: #d4af37; font-size: 1.2rem; }
    .att-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }

    /* DataTable overrides */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgba(0,0,0,.08); border-radius: 22px;
        padding: .3rem .65rem; font-size: .72rem; width: 180px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); outline: none;
    }
    .dataTables_wrapper .dataTables_length label { font-size: .72rem; font-weight: 600; }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid rgba(0,0,0,.08); border-radius: .3rem;
        padding: .2rem 1.2rem .2rem .4rem; font-size: .72rem;
    }
    .dataTables_wrapper .dataTables_info { font-size: .68rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: .25rem .5rem; font-size: .68rem; border-radius: .3rem;
        border: 1px solid rgba(0,0,0,.06); margin: 0 1px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #d4af37, #e8c84a) !important;
        color: #fff !important; border-color: #d4af37 !important;
    }

    /* Absent list */
    .absent-row {
        display: flex; align-items: center; gap: .5rem; padding: .35rem 0;
        border-bottom: 1px solid rgba(0,0,0,.03);
    }
    .absent-row:last-child { border-bottom: none; }
    .abs-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        background: rgba(244,106,106,.12); color: #c84646;
        display: flex; align-items: center; justify-content: center;
        font-size: .6rem; font-weight: 700; flex-shrink: 0;
    }
    .absent-row .abs-name { font-size: .75rem; font-weight: 600; flex: 1; }

    /* Weekly trend */
    .trend-tbl { width: 100%; font-size: .72rem; }
    .trend-tbl th { font-size: .58rem; text-transform: uppercase; letter-spacing: .4px; color: var(--bs-surface-500); padding: .3rem .4rem; font-weight: 700; }
    .trend-tbl td { padding: .3rem .4rem; }
    .trend-tbl .progress { height: 4px; border-radius: 2px; }
</style>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-check-all me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-block-helper me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="att-hdr">
        <div>
            <h4><i class="bx bx-time-five"></i> Attendance Overview</h4>
            <p>Track daily check-ins, reports &amp; time records</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('attendance.history') }}" class="act-btn a-primary"><i class="mdi mdi-history"></i> History</a>
            <a href="{{ route('attendance.print-view') }}" class="act-btn a-success" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
            @canEditModule('attendance')
            <button type="button" class="act-btn a-warn" data-bs-toggle="modal" data-bs-target="#manualEntryModal"><i class="mdi mdi-plus"></i> Manual Entry</button>
            <button type="button" class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#bulkAttendanceModal"><i class="mdi mdi-calendar-edit"></i> Bulk Mark</button>
            @endcanEditModule
        </div>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-group"></i></span>
            <span class="k-val">{{ $totalEmployees }}</span>
            <span class="k-lbl">Total Staff</span>
        </div>
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-check-circle"></i></span>
            <span class="k-val">{{ $presentCount }}</span>
            <span class="k-lbl">Present ({{ $totalEmployees > 0 ? round(($presentCount/$totalEmployees)*100) : 0 }}%)</span>
        </div>
        <div class="kpi-card k-warn">
            <span class="k-icon"><i class="bx bx-time"></i></span>
            <span class="k-val">{{ $lateCount }}</span>
            <span class="k-lbl">Late ({{ $totalEmployees > 0 ? round(($lateCount/$totalEmployees)*100) : 0 }}%)</span>
        </div>
        <div class="kpi-card k-red">
            <span class="k-icon"><i class="bx bx-user-x"></i></span>
            <span class="k-val">{{ $absentCount }}</span>
            <span class="k-lbl">Absent ({{ $totalEmployees > 0 ? round(($absentCount/$totalEmployees)*100) : 0 }}%)</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('attendance.index') }}" id="filterForm">
    <div class="ex-card pipe-filter-bar">
        <span class="pipe-pill-lbl">DATE</span>
        <input type="text" class="pipe-pill-date sl-pill-date" name="start_date" value="{{ $startDate }}" placeholder="Start date">
        <span style="font-size:.65rem;color:var(--bs-surface-400)">TO</span>
        <input type="text" class="pipe-pill-date sl-pill-date" name="end_date" value="{{ $endDate }}" placeholder="End date">

        <span class="pipe-pill-lbl" style="margin-left:.5rem">EMPLOYEE</span>
        <input type="text" class="pipe-pill" name="search_name" placeholder="Search name..." value="{{ $searchName ?? '' }}" style="min-width:140px;border-radius:22px;padding:.32rem .65rem;font-size:.72rem;border:1px solid rgba(0,0,0,.08)">

        <span class="pipe-pill-lbl" style="margin-left:.5rem">STATUS</span>
        <select class="sl-pill-select" name="status" style="min-width:90px">
            <option value="">All</option>
            <option value="present" {{ ($searchStatus ?? '') == Statuses::ATTENDANCE_PRESENT ? 'selected' : '' }}>Present</option>
            <option value="late" {{ ($searchStatus ?? '') == Statuses::ATTENDANCE_LATE ? 'selected' : '' }}>Late</option>
            <option value="absent" {{ ($searchStatus ?? '') == Statuses::ATTENDANCE_ABSENT ? 'selected' : '' }}>Absent</option>
            <option value="half_day" {{ ($searchStatus ?? '') == 'half_day' ? 'selected' : '' }}>Half Day</option>
            <option value="paid_leave" {{ ($searchStatus ?? '') == 'paid_leave' ? 'selected' : '' }}>Paid Leave</option>
        </select>

        <button type="submit" class="pipe-pill-apply"><i class="mdi mdi-magnify"></i> Filter</button>

        <div class="d-flex gap-1 ms-1">
            <button type="button" class="pipe-pill" id="prevDayBtn"><i class="mdi mdi-chevron-left"></i> Prev</button>
            <button type="button" class="pipe-pill" id="nextDayBtn">Next <i class="mdi mdi-chevron-right"></i></button>
            <a href="{{ route('attendance.index') }}" class="pipe-pill-clear"><i class="mdi mdi-refresh"></i> Reset</a>
        </div>
    </div>
    </form>

    <div class="row g-2">
        <!-- Main Table -->
        <div class="col-xl-8">
            <div class="ex-card sec-card">
                <div class="pipe-hdr">
                    <i class="mdi mdi-table"></i>
                    @if($startDate == $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($startDate)->format('M j') }} – {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}
                    @endif
                    <span class="badge-count">{{ $attendanceDetails->count() }} records</span>
                </div>
                <div class="sec-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="ex-tbl" id="attendance-table">
                            <thead>
                                <tr>
                                    <th>Employee</th><th>Login</th><th>Logout</th><th>Status</th><th>Hours</th><th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceDetails as $attendance)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            @if($attendance->user)
                                            <div class="abs-avatar" style="background:rgba(85,110,230,.12);color:#556ee6">{{ substr($attendance->user->name, 0, 1) }}</div>
                                            <span style="font-size:.78rem;font-weight:600">{{ $attendance->user->name }}
                                                @if($attendance->user->trashed())
                                                    <span class="s-pill s-declined" style="font-size:.5rem">Ended</span>
                                                @endif
                                            </span>
                                            @else
                                            <span class="text-muted"><em>Deleted</em></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($attendance->login_time)
                                            <span class="v-badge v-teal">{{ $attendance->date ? $attendance->date->format('M d') : '' }} {{ \Carbon\Carbon::parse($attendance->login_time)->format('g:i A') }}</span>
                                        @else <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->logout_time)
                                            <span class="v-badge v-gray">{{ \Carbon\Carbon::parse($attendance->logout_time)->format('M d g:i A') }}</span>
                                        @else <span class="text-muted" style="font-size:.72rem">Still working</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $sPill = match($attendance->status) {
                                                Statuses::ATTENDANCE_PRESENT => 's-sale',
                                                Statuses::ATTENDANCE_LATE => 's-pending',
                                                Statuses::ATTENDANCE_ABSENT => 's-declined',
                                                'half_day' => 's-transferred',
                                                'paid_leave' => 's-forwarded',
                                                default => 's-closed'
                                            };
                                            $sLabel = match($attendance->status) {
                                                Statuses::ATTENDANCE_PRESENT => 'On Time',
                                                Statuses::ATTENDANCE_LATE => 'Late',
                                                Statuses::ATTENDANCE_ABSENT => 'Absent',
                                                'half_day' => 'Half Day',
                                                'paid_leave' => 'Paid Leave',
                                                default => $attendance->status
                                            };
                                        @endphp
                                        <span class="s-pill {{ $sPill }}">{{ $sLabel }}</span>
                                    </td>
                                    <td>
                                        @if($attendance->login_time)
                                            @if($attendance->logout_time)
                                                @php
                                                    $loginTime = \Carbon\Carbon::parse($attendance->login_time);
                                                    $logoutTime = \Carbon\Carbon::parse($attendance->logout_time);
                                                    if ($logoutTime->lt($loginTime)) $logoutTime->addDay();
                                                    $workingMinutes = $loginTime->diffInMinutes($logoutTime);
                                                    $hours = floor($workingMinutes / 60);
                                                    $minutes = $workingMinutes % 60;
                                                @endphp
                                                <span style="font-size:.75rem">{{ $hours }}h {{ $minutes }}m</span>
                                            @else
                                                <span style="font-size:.75rem;color:#556ee6;font-weight:600">{{ $attendance->getFormattedCurrentWorkingHours() }}</span>
                                            @endif
                                        @else <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @canEditModule('attendance')
                                        <div class="d-flex gap-1">
                                            <button type="button" class="act-btn a-primary" onclick="editAttendance({{ $attendance->id }})" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                            @canDeleteInModule('attendance')
                                            <button type="button" class="act-btn a-danger" onclick="deleteAttendance({{ $attendance->id }}, '{{ $attendance->user ? $attendance->user->name : 'Unknown' }}', '{{ $attendance->date->format('M d, Y') }}')" title="Delete"><i class="mdi mdi-delete"></i></button>
                                            @endcanDeleteInModule
                                        </div>
                                        @else <span class="text-muted">-</span>
                                        @endcanEditModule
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4">
                                    <i class="mdi mdi-account-off-outline" style="font-size:2rem;color:var(--bs-surface-300)"></i>
                                    <p class="text-muted mt-1" style="font-size:.78rem">No attendance records found.</p>
                                </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Absent Today -->
            <div class="ex-card sec-card" style="margin-bottom:.65rem">
                <div class="pipe-hdr">
                    <i class="mdi mdi-account-off-outline" style="color:#c84646"></i> Absent Today
                    <span class="badge-count" style="background:rgba(244,106,106,.12);color:#c84646">{{ $absentEmployees->count() }}</span>
                </div>
                <div class="sec-body" style="max-height:220px;overflow-y:auto">
                    @forelse($absentEmployees as $employee)
                    <div class="absent-row">
                        <div class="abs-avatar">{{ substr($employee->name, 0, 1) }}</div>
                        <span class="abs-name">{{ $employee->name }}</span>
                        @canEditModule('attendance')
                        <a href="#" class="act-btn a-success" onclick="markManualAttendance({{ $employee->id }})" style="font-size:.58rem"><i class="mdi mdi-plus"></i></a>
                        @endcanEditModule
                    </div>
                    @empty
                    <div class="text-center py-2">
                        <i class="bx bx-check-circle" style="font-size:1.2rem;color:#1a8754"></i>
                        <span style="font-size:.72rem;color:var(--bs-surface-500);display:block">All present!</span>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Weekly Trend -->
            <div class="ex-card sec-card">
                <div class="pipe-hdr">
                    <i class="mdi mdi-chart-timeline-variant" style="color:#556ee6"></i> Weekly Trend
                </div>
                <div class="sec-body" style="padding:0">
                    <table class="trend-tbl">
                        <thead><tr><th>Date</th><th>Present</th><th>Rate</th></tr></thead>
                        <tbody>
                            @foreach ($weeklyStats as $stat)
                            <tr>
                                <td>{{ $stat['date'] }}</td>
                                <td><span class="v-badge v-blue">{{ $stat['present'] + $stat['late'] }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:4px"><div class="progress-bar" style="width:{{ $stat['percentage'] }}%;background:#d4af37"></div></div>
                                        <span style="font-size:.62rem;min-width:26px;color:var(--bs-surface-500)">{{ $stat['percentage'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @canEditModule('attendance')
    <!-- Manual Entry Modal -->
    <div class="modal fade" id="manualEntryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-glass">
                    <h5 class="modal-title" style="font-size:.88rem"><i class="mdi mdi-plus-circle me-1"></i> Manual Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="manualEntryForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select class="form-select" id="employee_select" name="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach ($allEmployees as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="attendance_date" name="date" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Login Time</label><input type="time" class="form-control" id="login_time" name="login_time" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Logout Time</label><input type="time" class="form-control" id="logout_time" name="logout_time"></div>
                        </div>
                        <div class="mb-3 d-none" id="overnight_shift_alert">
                            <div style="padding:.5rem;border-radius:.4rem;background:rgba(80,165,241,.06);border:1px solid rgba(80,165,241,.2);font-size:.72rem;color:#2b81c9">
                                <i class="mdi mdi-information me-1"></i><strong>Overnight Shift Detected!</strong>
                                <p class="mb-0 mt-1" id="shift_duration_display" style="font-size:.68rem"></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status_select" name="status" required>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="half_day">Half Day</option>
                                <option value="paid_leave">Paid Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-success"><i class="mdi mdi-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-glass">
                    <h5 class="modal-title" style="font-size:.88rem">Edit Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAttendanceForm" method="POST" action="#">
                    @csrf
                    <input type="hidden" id="edit_attendance_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Employee</label><input type="text" class="form-control" id="edit_employee_name" readonly></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" class="form-control" id="edit_date" required></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Login</label><input type="time" class="form-control" id="edit_login" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Logout</label><input type="time" class="form-control" id="edit_logout"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_status" required>
                                <option value="present">Present</option><option value="late">Late</option>
                                <option value="absent">Absent</option><option value="half_day">Half Day</option>
                                <option value="paid_leave">Paid Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-primary"><i class="mdi mdi-check"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         BULK ATTENDANCE CALENDAR MODAL
         ══════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="bulkAttendanceModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-glass">
                    <h5 class="modal-title" style="font-size:.92rem"><i class="mdi mdi-calendar-edit me-1"></i> Bulk Attendance — Calendar Marking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="bulkModalCloseBtn"></button>
                </div>
                <div class="modal-body" style="padding:1rem">

                    <!-- Top controls -->
                    <div class="row g-2 mb-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.75rem">Employee</label>
                            <select class="form-select form-select-sm" id="bulk_employee_id">
                                <option value="">— Select Employee —</option>
                                @foreach($allEmployees as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size:.75rem">Month</label>
                            <input type="month" class="form-control form-control-sm" id="bulk_month" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size:.75rem">Default Login Time</label>
                            <input type="time" class="form-control form-control-sm" id="bulk_login_time" value="09:00">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="act-btn a-primary w-100" id="loadCalendarBtn" style="height:31px"><i class="mdi mdi-calendar-search"></i> Load</button>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex gap-2 flex-wrap mb-2 align-items-center" id="bulk_legend" style="font-size:.67rem">
                        <span style="background:#e8f5e9;color:#1a8754;padding:2px 8px;border-radius:10px;border:1px solid #c3e6cb">● Present</span>
                        <span style="background:#fff3cd;color:#856404;padding:2px 8px;border-radius:10px;border:1px solid #ffe69c">● Late</span>
                        <span style="background:#fff0e0;color:#c07000;padding:2px 8px;border-radius:10px;border:1px solid #ffd16a">● Half Day</span>
                        <span style="background:#e8f0fe;color:#3d5afe;padding:2px 8px;border-radius:10px;border:1px solid #b8c8ff">● Paid Leave</span>
                        <span style="background:#fdecea;color:#c62828;padding:2px 8px;border-radius:10px;border:1px solid #f9c4c4">● Absent</span>
                        <span style="background:#f5f5f5;color:#888;padding:2px 8px;border-radius:10px;border:1px solid #ddd">— Weekend/Holiday</span>
                        <div class="ms-auto d-flex gap-1 flex-wrap">
                            <button type="button" class="act-btn a-success" style="font-size:.6rem;padding:2px 7px" onclick="bulkQuickMark('present')" title="Mark all workdays as Present"><i class="mdi mdi-check-all"></i> All Present</button>
                            <button type="button" class="act-btn a-warn" style="font-size:.6rem;padding:2px 7px" onclick="bulkQuickMark('late')" title="Mark all workdays as Late"><i class="mdi mdi-alarm-check"></i> All Late</button>
                            <button type="button" class="act-btn a-danger" style="font-size:.6rem;padding:2px 7px" onclick="bulkQuickMark(null)" title="Clear all markings"><i class="mdi mdi-close-circle"></i> Clear All</button>
                        </div>
                    </div>

                    <!-- Info bar -->
                    <div id="bulk_info_bar" class="mb-2" style="font-size:.72rem;color:var(--bs-surface-500)">
                        Select an employee and month, then click <strong>Load</strong>.
                    </div>

                    <!-- Calendar grid -->
                    <div id="bulk_calendar_wrap" style="overflow-x:auto">
                        <div id="bulk_calendar_grid" style="min-width:580px"></div>
                    </div>

                    <!-- Summary counts -->
                    <div id="bulk_summary" class="d-flex gap-3 flex-wrap mt-2" style="font-size:.72rem;display:none!important"></div>

                </div>
                <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                    <div style="font-size:.7rem;color:var(--bs-surface-400)" id="bulk_change_count"></div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="act-btn a-success" id="saveBulkAttendanceBtn" disabled><i class="mdi mdi-content-save"></i> Save All Changes</button>
                </div>
            </div>
        </div>
    </div>
    @endcanEditModule
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    if (window.attendanceTableInitialized) return;
    window.attendanceTableInitialized = true;
    var t = $('#attendance-table');
    if (t.length === 0) return;
    var dataRows = t.find('tbody tr:not(:has(td[colspan]))');
    if (dataRows.length === 0) return;
    if ($.fn.DataTable.isDataTable('#attendance-table')) { try { t.DataTable().clear().destroy(); } catch(e) {} }
    try {
        t.DataTable({
            pageLength: 25, responsive: true, order: [[1, "asc"]],
            columnDefs: [{ orderable: false, targets: [5] }],
            language: { search: "Search:", searchPlaceholder: "Type to filter...", lengthMenu: "Show _MENU_", info: "_START_-_END_ of _TOTAL_", infoEmpty: "No records", paginate: { next: "Next", previous: "Prev" } },
            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>"
        });
    } catch(e) { console.error('DataTable error:', e); }
});

@canEditModule('attendance')
function checkOvernightShift() {    var l = document.getElementById('login_time').value, o = document.getElementById('logout_time').value;    var a = document.getElementById('overnight_shift_alert'), d = document.getElementById('shift_duration_display');
    if (l && o) {
        var lh = parseInt(l.split(':')[0]), oh = parseInt(o.split(':')[0]);
        if (lh >= 12 && oh < 12) {
            var lm = lh*60+parseInt(l.split(':')[1]), om = (oh+24)*60+parseInt(o.split(':')[1]);
            var dur = om - lm; d.textContent = 'Duration: ' + Math.floor(dur/60) + 'h ' + (dur%60) + 'm';
            a.style.display = 'block'; return;
        }
    }
    a.style.display = 'none';
}
document.getElementById('login_time')?.addEventListener('change', checkOvernightShift);
document.getElementById('logout_time')?.addEventListener('change', checkOvernightShift);

document.getElementById('manualEntryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this), btn = this.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    fetch('{{ route("attendance.mark-manual.post") }}', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(r => r.ok ? r.json() : r.text().then(t => { throw new Error(t); }))
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('manualEntryModal')).hide();
            alert(data.message || 'Saved');
            var cd = data.attendance?.date || document.getElementById('attendance_date').value;
            window.location.href = '{{ route("attendance.index") }}?start_date=' + cd + '&end_date=' + cd;
        } else { alert('Error: ' + (data.message || 'Failed')); btn.disabled = false; btn.textContent = 'Save'; }
    }).catch(err => { alert('Error: ' + err.message); btn.disabled = false; btn.textContent = 'Save'; });
});

function markManualAttendance(userId) {
    var now = new Date(), t = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
    document.getElementById('employee_select').value = userId;
    document.getElementById('attendance_date').value = '{{ date("Y-m-d") }}';
    document.getElementById('login_time').value = t;
    document.getElementById('status_select').value = 'late';
    new bootstrap.Modal(document.getElementById('manualEntryModal')).show();
}

function editAttendance(id) {
    document.getElementById('editAttendanceForm').action = '/attendance/' + id + '/update';
    fetch('/attendance/' + id + '/json').then(r => r.json()).then(data => {
        if (data.success) {
            document.getElementById('edit_attendance_id').value = data.attendance.id;
            document.getElementById('edit_employee_name').value = data.attendance.user_name;
            document.getElementById('edit_date').value = data.attendance.date;
            document.getElementById('edit_login').value = data.attendance.login_time || '';
            document.getElementById('edit_logout').value = data.attendance.logout_time || '';
            document.getElementById('edit_status').value = data.attendance.status;
            new bootstrap.Modal(document.getElementById('editAttendanceModal')).show();
        } else alert('Error: ' + (data.message || 'Could not load'));
    }).catch(err => alert('Error: ' + err.message));
}

document.getElementById('editAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var id = document.getElementById('edit_attendance_id').value;
    var payload = { date: document.getElementById('edit_date').value, login_time: document.getElementById('edit_login').value, logout_time: document.getElementById('edit_logout').value, status: document.getElementById('edit_status').value };
    fetch('/attendance/' + id + '/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(payload) })
    .then(r => r.json()).then(data => {
        if (data.success) { bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal')).hide(); location.reload(); }
        else alert('Error: ' + (data.message || 'Failed'));
    }).catch(err => alert('Error: ' + err.message));
});

function deleteAttendance(id, name, date) {
    if (!confirm('Delete attendance for ' + name + ' on ' + date + '?')) return;
    fetch('/attendance/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } })
    .then(r => r.json()).then(data => { if (data.success) { alert('Deleted'); location.reload(); } else alert('Error: ' + (data.message || 'Failed')); })
    .catch(err => alert('Error: ' + err.message));
}
@endcanEditModule

document.getElementById('prevDayBtn')?.addEventListener('click', function() {
    var s = document.querySelector('input[name="start_date"]'), e = document.querySelector('input[name="end_date"]');
    if (s && s.value) {
        var p = s.value.split('-').map(Number), d = new Date(p[0], p[1]-1, p[2]);
        d.setDate(d.getDate() - 1);
        var nd = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
        s.value = nd; e.value = nd; document.getElementById('filterForm').submit();
    }
});
document.getElementById('nextDayBtn')?.addEventListener('click', function() {
    var s = document.querySelector('input[name="start_date"]'), e = document.querySelector('input[name="end_date"]');
    if (s && s.value) {
        var p = s.value.split('-').map(Number), d = new Date(p[0], p[1]-1, p[2]);
        d.setDate(d.getDate() + 1);
        var today = new Date(); today.setHours(0,0,0,0);
        if (d <= today) {
            var nd = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            s.value = nd; e.value = nd; document.getElementById('filterForm').submit();
        } else alert('Cannot navigate to future');
    }
});

// ═══════════════════════════════════════════════════════
// BULK ATTENDANCE CALENDAR
// ═══════════════════════════════════════════════════════
@canEditModule('attendance')
(function() {
    var bulkDays = {};
    var bulkPeriodStart = null, bulkPeriodEnd = null;
    var STATUS_CYCLE = [null, 'present', 'late', 'half_day', 'paid_leave', 'absent'];
    var STATUS_STYLE = {
        present:    { bg: '#e8f5e9', color: '#1a8754', border: '#c3e6cb' },
        late:       { bg: '#fff3cd', color: '#856404', border: '#ffe69c' },
        half_day:   { bg: '#fff0e0', color: '#c07000', border: '#ffd16a' },
        paid_leave: { bg: '#e8f0fe', color: '#3d5afe', border: '#b8c8ff' },
        absent:     { bg: '#fdecea', color: '#c62828', border: '#f9c4c4' },
    };

    function getStatusLabel(st) {
        return { present: 'Present', late: 'Late', half_day: 'Half Day', paid_leave: 'Paid Leave', absent: 'Absent' }[st] || (st || '—');
    }

    function formatDate(d) {
        return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    }

    function formatMonthLabel(mon) {
        var d = new Date(mon + '-01');
        return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    }

    function loadCalendar() {
        var uid = document.getElementById('bulk_employee_id').value;
        var mon = document.getElementById('bulk_month').value;
        if (!uid || !mon) { alert('Please select an employee and a month.'); return; }
        var btn = document.getElementById('loadCalendarBtn');
        btn.disabled = true; btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Loading...';
        fetch('/attendance/bulk-month-data?user_id=' + encodeURIComponent(uid) + '&month=' + encodeURIComponent(mon), {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false; btn.innerHTML = '<i class="mdi mdi-calendar-search"></i> Load';
            if (!data.success) { alert(data.message || 'Failed to load.'); return; }
            renderCalendar(data);
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="mdi mdi-calendar-search"></i> Load'; alert('Error: ' + err.message); });
    }

    function renderCalendar(data) {
        bulkDays = {};
        bulkPeriodStart = data.period_start || null;
        bulkPeriodEnd   = data.period_end   || null;
        data.days.forEach(function(d) {
            bulkDays[d.date] = Object.assign({}, d, { current_status: d.status, original_status: d.status });
        });
        var periodLbl = data.period_label || formatMonthLabel(data.month);
        document.getElementById('bulk_info_bar').innerHTML =
            '<i class="mdi mdi-account me-1"></i><strong>' + (data.employee_name || '') + '</strong>' +
            ' &nbsp;<span style="color:#d4af37;font-weight:600">&#128197; ' + periodLbl + '</span>' +
            ' &nbsp;<span class="text-muted" style="font-size:.63rem">Click to cycle &nbsp;|&nbsp; Dbl-click to clear</span>';
        buildCalendarGrid(data.period_start, data.period_end);
        updateSummary();
        document.getElementById('saveBulkAttendanceBtn').disabled = true;
    }

    function buildCalendarGrid(periodStartStr, periodEndStr) {
        var grid = document.getElementById('bulk_calendar_grid');
        var dates = Object.keys(bulkDays).sort();
        if (!dates.length) { grid.innerHTML = '<p class="text-muted text-center p-3">No days found.</p>'; return; }

        // Use the exact pay period boundaries provided by the server
        var firstDate = periodStartStr ? new Date(periodStartStr + 'T00:00:00') : new Date(dates[0] + 'T00:00:00');
        var lastDate  = periodEndStr   ? new Date(periodEndStr   + 'T00:00:00') : new Date(dates[dates.length-1] + 'T00:00:00');

        // Pad to nearest Monday / Sunday
        var start = new Date(firstDate);
        var dow = start.getDay(); start.setDate(start.getDate() - (dow === 0 ? 6 : dow - 1));
        var end = new Date(lastDate);
        var endDow = end.getDay(); if (endDow !== 0) end.setDate(end.getDate() + (7 - endDow));

        var dayNames = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

        // Month-header groups: find first day of each month inside the period for column spanning
        var html = '<table style="width:100%;border-collapse:separate;border-spacing:3px" id="bulk_cal_table">';

        // Month label row — scan rows to find month boundaries
        // Build rows first so we know spans, then prepend a header
        var rows = [];
        var cur = new Date(start);
        while (cur <= end) {
            var week = [];
            for (var i = 0; i < 7; i++) {
                week.push(new Date(cur));
                cur.setDate(cur.getDate() + 1);
            }
            rows.push(week);
        }

        // Month label spanning row
        html += '<thead>';
        // Weekday names row
        html += '<tr>';
        dayNames.forEach(function(d, idx) {
            var isWeekend = idx >= 5;
            html += '<th style="text-align:center;font-size:.65rem;padding:4px 0;color:' + (isWeekend ? '#c0b090':'var(--bs-surface-500)') + ';font-weight:700;">' + d + '</th>';
        });
        html += '</tr></thead><tbody>';

        rows.forEach(function(week) {
            html += '<tr>';
            week.forEach(function(day) {
                var dk = formatDate(day);
                var inPeriod = day >= firstDate && day <= lastDate;
                if (!inPeriod) {
                    // Out-of-period padding cell — show day number greyed
                    html += '<td style="height:70px"><div style="height:66px;border-radius:6px;background:transparent;display:flex;align-items:center;justify-content:center"><span style="font-size:.65rem;color:#d0d0d0">' + day.getDate() + '</span></div></td>';
                } else {
                    var dayData = bulkDays[dk];
                    html += dayData ? buildDayCell(dayData, dk, day.getDate()) : '<td style="height:70px"></td>';
                }
            });
            html += '</tr>';
        });

        html += '</tbody></table>';
        grid.innerHTML = html;
        attachCellHandlers();
    }

    function buildDayCell(day, dateKey, dayNum) {
        if (day.is_weekend || day.is_holiday) {
            var lbl = day.is_holiday ? 'Holiday' : 'Weekend';
            return '<td style="height:70px"><div style="height:66px;background:#f8f9fa;border-radius:6px;border:1px solid #eee;display:flex;flex-direction:column;align-items:center;justify-content:center">' +
                   '<span style="font-size:.75rem;color:#bbb;font-weight:700">' + dayNum + '</span>' +
                   '<span style="font-size:.55rem;color:#ccc;margin-top:1px">' + lbl + '</span></div></td>';
        }
        var st = day.current_status;
        // Safe fallback — if status is unrecognised or null, use neutral style
        var sty = (st && STATUS_STYLE[st]) ? STATUS_STYLE[st] : { bg: '#fff', color: '#999', border: '#dee2e6' };
        var modified = st !== day.original_status;
        var badge = st
            ? '<span style="font-size:.62rem;font-weight:700;color:' + sty.color + ';display:block;margin-top:2px;line-height:1.2">' + getStatusLabel(st) + '</span>'
            : '<span style="font-size:.6rem;color:#ccc;display:block;margin-top:3px">tap</span>';
        var dot = modified ? '<span style="position:absolute;top:4px;right:5px;width:6px;height:6px;background:#d4af37;border-radius:50%;display:inline-block"></span>' : '';
        return '<td style="height:70px;position:relative">' +
               '<div class="bulk-day-cell" data-date="' + dateKey + '" style="cursor:pointer;height:66px;border-radius:6px;text-align:center;padding:8px 3px;' +
               'border:1.5px solid ' + sty.border + ';background:' + sty.bg + ';position:relative;transition:box-shadow .12s" ' +
               'title="' + dateKey + (st ? ' — ' + getStatusLabel(st) : '') + '">' +
               dot +
               '<div style="font-size:.72rem;color:' + sty.color + ';font-weight:700">' + dayNum + '</div>' +
               badge +
               '</div></td>';
    }

    function attachCellHandlers() {
        document.querySelectorAll('#bulk_cal_table .bulk-day-cell').forEach(function(cell) {
            cell.addEventListener('click', function() { cycleStatus(this.dataset.date); });
            cell.addEventListener('dblclick', function(e) { e.preventDefault(); clearStatus(this.dataset.date); });
        });
    }

    function cycleStatus(dateKey) {
        var day = bulkDays[dateKey];
        if (!day || day.is_weekend || day.is_holiday) return;
        var idx = STATUS_CYCLE.indexOf(day.current_status);
        day.current_status = STATUS_CYCLE[(idx + 1) % STATUS_CYCLE.length];
        if (day.current_status && !day.login_time) {
            day.login_time = document.getElementById('bulk_login_time').value || '09:00';
        }
        refreshCell(dateKey);
        updateSummary();
    }

    function clearStatus(dateKey) {
        var day = bulkDays[dateKey];
        if (!day || day.is_weekend || day.is_holiday) return;
        day.current_status = null;
        refreshCell(dateKey);
        updateSummary();
    }

    function refreshCell(dateKey) {
        var day = bulkDays[dateKey];
        var cell = document.querySelector('.bulk-day-cell[data-date="' + dateKey + '"]');
        if (!cell) return;
        var td = cell.closest('td');
        if (!td) return;
        var d = new Date(dateKey + 'T00:00:00');
        var newHtml = buildDayCell(day, dateKey, d.getDate());
        var tmp = document.createElement('table'); tmp.innerHTML = '<tbody><tr>' + newHtml + '</tr></tbody>';
        var newTd = tmp.querySelector('td');
        td.parentNode.replaceChild(newTd, td);
        // Re-attach event on new cell
        var nc = newTd.querySelector('.bulk-day-cell');
        if (nc) {
            nc.addEventListener('click', function() { cycleStatus(this.dataset.date); });
            nc.addEventListener('dblclick', function(e) { e.preventDefault(); clearStatus(this.dataset.date); });
        }
    }

    function updateSummary() {
        var counts = {}, changes = 0;
        Object.values(bulkDays).forEach(function(d) {
            if (d.is_weekend || d.is_holiday) return;
            if (d.current_status) counts[d.current_status] = (counts[d.current_status] || 0) + 1;
            if (d.current_status !== d.original_status) changes++;
        });
        var sum = document.getElementById('bulk_summary');
        sum.style.cssText = 'display:flex!important';
        var parts = Object.entries(counts).filter(([,v]) => v > 0).map(function([k,v]) {
            var s = STATUS_STYLE[k] || {};
            return '<span style="background:' + (s.bg||'#f0f0f0') + ';color:' + (s.color||'#333') + ';padding:2px 10px;border-radius:10px;border:1px solid ' + (s.border||'#ddd') + '">' + getStatusLabel(k) + ': <strong>' + v + '</strong></span>';
        });
        sum.innerHTML = parts.join('') || '<span class="text-muted">No days marked yet.</span>';
        document.getElementById('bulk_change_count').textContent = changes > 0 ? changes + ' pending change(s)' : '';
        document.getElementById('saveBulkAttendanceBtn').disabled = changes === 0;
    }

    function saveBulkAttendance() {
        var uid = document.getElementById('bulk_employee_id').value;
        if (!uid) { alert('No employee selected.'); return; }
        var entries = [];
        Object.values(bulkDays).forEach(function(d) {
            if (d.is_weekend || d.is_holiday) return;
            if (d.current_status !== d.original_status && d.current_status) {
                entries.push({ date: d.date, status: d.current_status, login_time: d.login_time || document.getElementById('bulk_login_time').value || '09:00', logout_time: d.logout_time || null });
            }
        });
        if (!entries.length) { alert('No changes to save.'); return; }
        var btn = document.getElementById('saveBulkAttendanceBtn');
        btn.disabled = true; btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Saving...';
        fetch('{{ route("attendance.bulk-mark.post") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ user_id: uid, entries: entries })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false; btn.innerHTML = '<i class="mdi mdi-content-save"></i> Save All Changes';
            if (data.success) {
                // Mark saved entries as original so diff resets
                entries.forEach(function(e) { if (bulkDays[e.date]) { bulkDays[e.date].original_status = e.status; } });
                updateSummary();
                // Show toast / re-fetch calendar silently
                document.getElementById('bulk_info_bar').innerHTML += ' &nbsp;<span style="color:#1a8754;font-weight:600"><i class="mdi mdi-check-circle"></i> ' + (data.message || 'Saved!') + '</span>';
                // Reload from server to confirm
                var uid2 = document.getElementById('bulk_employee_id').value, mon2 = document.getElementById('bulk_month').value;
                if (uid2 && mon2) {
                    fetch('/attendance/bulk-month-data?user_id=' + encodeURIComponent(uid2) + '&month=' + encodeURIComponent(mon2), {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    }).then(r => r.json()).then(function(d2) { if (d2.success) renderCalendar(d2); });
                }
            } else {
                var errMsg = Array.isArray(data.errors) && data.errors.length ? data.errors.join('\n') : (data.message || 'Save failed.');
                alert('Some errors occurred:\n' + errMsg);
            }
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="mdi mdi-content-save"></i> Save All Changes'; alert('Error: ' + err.message); });
    }

    // Quick-mark all workdays in current view
    function quickMarkAll(status) {
        Object.keys(bulkDays).forEach(function(dk) {
            var d = bulkDays[dk];
            if (!d.is_weekend && !d.is_holiday) {
                d.current_status = status;
                if (status && !d.login_time) d.login_time = document.getElementById('bulk_login_time').value || '09:00';
            }
        });
        buildCalendarGrid(bulkPeriodStart, bulkPeriodEnd);
        updateSummary();
    }

    document.getElementById('loadCalendarBtn')?.addEventListener('click', loadCalendar);
    document.getElementById('saveBulkAttendanceBtn')?.addEventListener('click', saveBulkAttendance);

    // Expose quick-mark for optional buttons
    window.bulkQuickMark = quickMarkAll;

    document.getElementById('bulkAttendanceModal')?.addEventListener('hidden.bs.modal', function() {
        bulkDays = {};
        bulkPeriodStart = null; bulkPeriodEnd = null;
        document.getElementById('bulk_calendar_grid').innerHTML = '';
        document.getElementById('bulk_info_bar').innerHTML = 'Select an employee and month, then click <strong>Load</strong>.';
        document.getElementById('bulk_summary').style.cssText = 'display:none!important';
        document.getElementById('bulk_change_count').textContent = '';
        document.getElementById('saveBulkAttendanceBtn').disabled = true;
    });
})();
@endcanEditModule
</script>
@endsection
