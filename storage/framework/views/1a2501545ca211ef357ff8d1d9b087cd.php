<?php use \App\Support\Roles; ?>
<?php use \App\Support\Statuses; ?>


<?php $__env->startSection('title', 'Attendance Overview'); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo e(URL::asset('/build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-check-all me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-block-helper me-1"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Header -->
    <div class="att-hdr">
        <div>
            <h4><i class="bx bx-time-five"></i> Attendance Overview</h4>
            <p>Track daily check-ins, reports &amp; time records</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('attendance.history')); ?>" class="act-btn a-primary"><i class="mdi mdi-history"></i> History</a>
            <a href="<?php echo e(route('attendance.print-view')); ?>" class="act-btn a-success" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasAnyRole([Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::HR])): ?>
            <button type="button" class="act-btn a-warn" data-bs-toggle="modal" data-bs-target="#manualEntryModal"><i class="mdi mdi-plus"></i> Manual Entry</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-group"></i></span>
            <span class="k-val"><?php echo e($totalEmployees); ?></span>
            <span class="k-lbl">Total Staff</span>
        </div>
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-check-circle"></i></span>
            <span class="k-val"><?php echo e($presentCount); ?></span>
            <span class="k-lbl">Present (<?php echo e($totalEmployees > 0 ? round(($presentCount/$totalEmployees)*100) : 0); ?>%)</span>
        </div>
        <div class="kpi-card k-warn">
            <span class="k-icon"><i class="bx bx-time"></i></span>
            <span class="k-val"><?php echo e($lateCount); ?></span>
            <span class="k-lbl">Late (<?php echo e($totalEmployees > 0 ? round(($lateCount/$totalEmployees)*100) : 0); ?>%)</span>
        </div>
        <div class="kpi-card k-red">
            <span class="k-icon"><i class="bx bx-user-x"></i></span>
            <span class="k-val"><?php echo e($absentCount); ?></span>
            <span class="k-lbl">Absent (<?php echo e($totalEmployees > 0 ? round(($absentCount/$totalEmployees)*100) : 0); ?>%)</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="<?php echo e(route('attendance.index')); ?>" id="filterForm">
    <div class="ex-card pipe-filter-bar">
        <span class="pipe-pill-lbl">DATE</span>
        <input type="text" class="pipe-pill-date sl-pill-date" name="start_date" value="<?php echo e($startDate); ?>" placeholder="Start date">
        <span style="font-size:.65rem;color:var(--bs-surface-400)">TO</span>
        <input type="text" class="pipe-pill-date sl-pill-date" name="end_date" value="<?php echo e($endDate); ?>" placeholder="End date">

        <span class="pipe-pill-lbl" style="margin-left:.5rem">EMPLOYEE</span>
        <input type="text" class="pipe-pill-date" name="search_name" placeholder="Search name..." value="<?php echo e($searchName ?? ''); ?>" style="min-width:140px">

        <span class="pipe-pill-lbl" style="margin-left:.5rem">STATUS</span>
        <select class="sl-pill-select" name="status" style="min-width:90px">
            <option value="">All</option>
            <option value="present" <?php echo e(($searchStatus ?? '') == Statuses::ATTENDANCE_PRESENT ? 'selected' : ''); ?>>Present</option>
            <option value="late" <?php echo e(($searchStatus ?? '') == Statuses::ATTENDANCE_LATE ? 'selected' : ''); ?>>Late</option>
            <option value="absent" <?php echo e(($searchStatus ?? '') == Statuses::ATTENDANCE_ABSENT ? 'selected' : ''); ?>>Absent</option>
            <option value="half_day" <?php echo e(($searchStatus ?? '') == 'half_day' ? 'selected' : ''); ?>>Half Day</option>
            <option value="paid_leave" <?php echo e(($searchStatus ?? '') == 'paid_leave' ? 'selected' : ''); ?>>Paid Leave</option>
        </select>

        <button type="submit" class="pipe-pill-apply"><i class="mdi mdi-magnify"></i> Filter</button>

        <div class="d-flex gap-1 ms-1">
            <button type="button" class="pipe-pill" id="prevDayBtn"><i class="mdi mdi-chevron-left"></i> Prev</button>
            <button type="button" class="pipe-pill" id="nextDayBtn">Next <i class="mdi mdi-chevron-right"></i></button>
            <a href="<?php echo e(route('attendance.index')); ?>" class="pipe-pill-clear"><i class="mdi mdi-refresh"></i> Reset</a>
        </div>
    </div>
    </form>

    <div class="row g-2">
        <!-- Main Table -->
        <div class="col-xl-8">
            <div class="ex-card sec-card">
                <div class="pipe-hdr">
                    <i class="mdi mdi-table"></i>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startDate == $endDate): ?>
                        <?php echo e(\Carbon\Carbon::parse($startDate)->format('M j, Y')); ?>

                    <?php else: ?>
                        <?php echo e(\Carbon\Carbon::parse($startDate)->format('M j')); ?> – <?php echo e(\Carbon\Carbon::parse($endDate)->format('M j, Y')); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="badge-count"><?php echo e($attendanceDetails->count()); ?> records</span>
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attendanceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->user): ?>
                                            <div class="abs-avatar" style="background:rgba(85,110,230,.12);color:#556ee6"><?php echo e(substr($attendance->user->name, 0, 1)); ?></div>
                                            <span style="font-size:.78rem;font-weight:600"><?php echo e($attendance->user->name); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->user->trashed()): ?>
                                                    <span class="s-pill s-declined" style="font-size:.5rem">Ended</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </span>
                                            <?php else: ?>
                                            <span class="text-muted"><em>Deleted</em></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->login_time): ?>
                                            <span class="v-badge v-teal"><?php echo e($attendance->date ? $attendance->date->format('M d') : ''); ?> <?php echo e(\Carbon\Carbon::parse($attendance->login_time)->format('g:i A')); ?></span>
                                        <?php else: ?> <span class="text-muted">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                            <span class="v-badge v-gray"><?php echo e(\Carbon\Carbon::parse($attendance->logout_time)->format('M d g:i A')); ?></span>
                                        <?php else: ?> <span class="text-muted" style="font-size:.72rem">Still working</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
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
                                        ?>
                                        <span class="s-pill <?php echo e($sPill); ?>"><?php echo e($sLabel); ?></span>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->login_time): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                                <?php
                                                    $loginTime = \Carbon\Carbon::parse($attendance->login_time);
                                                    $logoutTime = \Carbon\Carbon::parse($attendance->logout_time);
                                                    if ($logoutTime->lt($loginTime)) $logoutTime->addDay();
                                                    $workingMinutes = $loginTime->diffInMinutes($logoutTime);
                                                    $hours = floor($workingMinutes / 60);
                                                    $minutes = $workingMinutes % 60;
                                                ?>
                                                <span style="font-size:.75rem"><?php echo e($hours); ?>h <?php echo e($minutes); ?>m</span>
                                            <?php else: ?>
                                                <span style="font-size:.75rem;color:#556ee6;font-weight:600"><?php echo e($attendance->getFormattedCurrentWorkingHours()); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php else: ?> <span class="text-muted">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(auth()->check() && auth()->user()->canEditModule('attendance')): ?>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="act-btn a-primary" onclick="editAttendance(<?php echo e($attendance->id); ?>)" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                            <?php if(auth()->check() && auth()->user()->canDeleteInModule('attendance')): ?>
                                            <button type="button" class="act-btn a-danger" onclick="deleteAttendance(<?php echo e($attendance->id); ?>, '<?php echo e($attendance->user ? $attendance->user->name : 'Unknown'); ?>', '<?php echo e($attendance->date->format('M d, Y')); ?>')" title="Delete"><i class="mdi mdi-delete"></i></button>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?> <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center py-4">
                                    <i class="mdi mdi-account-off-outline" style="font-size:2rem;color:var(--bs-surface-300)"></i>
                                    <p class="text-muted mt-1" style="font-size:.78rem">No attendance records found.</p>
                                </td></tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                    <span class="badge-count" style="background:rgba(244,106,106,.12);color:#c84646"><?php echo e($absentEmployees->count()); ?></span>
                </div>
                <div class="sec-body" style="max-height:220px;overflow-y:auto">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $absentEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="absent-row">
                        <div class="abs-avatar"><?php echo e(substr($employee->name, 0, 1)); ?></div>
                        <span class="abs-name"><?php echo e($employee->name); ?></span>
                        <a href="#" class="act-btn a-success" onclick="markManualAttendance(<?php echo e($employee->id); ?>)" style="font-size:.58rem"><i class="mdi mdi-plus"></i></a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-2">
                        <i class="bx bx-check-circle" style="font-size:1.2rem;color:#1a8754"></i>
                        <span style="font-size:.72rem;color:var(--bs-surface-500);display:block">All present!</span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $weeklyStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($stat['date']); ?></td>
                                <td><span class="v-badge v-blue"><?php echo e($stat['present'] + $stat['late']); ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:4px"><div class="progress-bar" style="width:<?php echo e($stat['percentage']); ?>%;background:#d4af37"></div></div>
                                        <span style="font-size:.62rem;min-width:26px;color:var(--bs-surface-500)"><?php echo e($stat['percentage']); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                    <?php echo csrf_field(); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
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

function checkOvernightShift() {
    var l = document.getElementById('login_time').value, o = document.getElementById('logout_time').value;
    var a = document.getElementById('overnight_shift_alert'), d = document.getElementById('shift_duration_display');
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
    fetch('<?php echo e(route("attendance.mark-manual.post")); ?>', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
    .then(r => r.ok ? r.json() : r.text().then(t => { throw new Error(t); }))
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('manualEntryModal')).hide();
            alert(data.message || 'Saved');
            var cd = data.attendance?.date || document.getElementById('attendance_date').value;
            window.location.href = '<?php echo e(route("attendance.index")); ?>?start_date=' + cd + '&end_date=' + cd;
        } else { alert('Error: ' + (data.message || 'Failed')); btn.disabled = false; btn.textContent = 'Save'; }
    }).catch(err => { alert('Error: ' + err.message); btn.disabled = false; btn.textContent = 'Save'; });
});

function markManualAttendance(userId) {
    var now = new Date(), t = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
    document.getElementById('employee_select').value = userId;
    document.getElementById('attendance_date').value = '<?php echo e(date("Y-m-d")); ?>';
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
    fetch('/attendance/' + id + '/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }, body: JSON.stringify(payload) })
    .then(r => r.json()).then(data => {
        if (data.success) { bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal')).hide(); location.reload(); }
        else alert('Error: ' + (data.message || 'Failed'));
    }).catch(err => alert('Error: ' + err.message));
});

function deleteAttendance(id, name, date) {
    if (!confirm('Delete attendance for ' + name + ' on ' + date + '?')) return;
    fetch('/attendance/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json' } })
    .then(r => r.json()).then(data => { if (data.success) { alert('Deleted'); location.reload(); } else alert('Error: ' + (data.message || 'Failed')); })
    .catch(err => alert('Error: ' + err.message));
}

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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/attendance/index.blade.php ENDPATH**/ ?>