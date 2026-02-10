<?php $__env->startSection('title'); ?>
    Attendance Overview
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        /* Modern DataTable Styling */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 600;
            color: #495057;
            margin-right: 10px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 0.5rem 1rem;
            width: 300px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #556ee6;
            box-shadow: 0 0 0 0.2rem rgba(85,110,230,0.15);
            outline: none;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_length label {
            font-weight: 600;
            color: #495057;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 0.45rem 2rem 0.45rem 0.75rem;
            margin: 0 0.75rem;
            font-size: 0.95rem;
            background: white;
            transition: all 0.3s ease;
        }
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #556ee6;
            outline: none;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 1.5em;
            font-size: 0.9rem;
            color: #74788d;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1.5em;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.9rem;
            margin: 0 3px;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #556ee6 0%, #4d63d4 100%);
            color: white !important;
            border-color: #556ee6;
            box-shadow: 0 2px 6px rgba(85,110,230,0.3);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #f8f9fc;
            border-color: #556ee6;
            color: #556ee6 !important;
        }
        .dataTables_wrapper .row {
            margin-bottom: 1rem;
        }
        /* Table styling improvements */
        #attendance-table {
            border-collapse: separate;
            border-spacing: 0;
        }
        #attendance-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border: none;
        }
        #attendance-table tbody tr {
            transition: all 0.2s ease;
        }
        #attendance-table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        #attendance-table tbody td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        .btn-soft-primary {
            background-color: rgba(85, 110, 230, 0.1);
            border-color: rgba(85, 110, 230, 0.2);
            color: #556ee6;
        }
        .btn-soft-primary:hover {
            background-color: #556ee6;
            border-color: #556ee6;
            color: white;
        }
        .btn-soft-danger {
            background-color: rgba(244, 106, 106, 0.1);
            border-color: rgba(244, 106, 106, 0.2);
            color: #f46a6a;
        }
        .btn-soft-danger:hover {
            background-color: #f46a6a;
            border-color: #f46a6a;
            color: white;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Attendance
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Overview
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Enhanced Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('attendance.index')); ?>" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date"
                            value="<?php echo e($startDate); ?>" max="<?php echo e(date('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date"
                            value="<?php echo e($endDate); ?>" max="<?php echo e(date('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search Employee</label>
                        <input type="text" class="form-control" name="search_name"
                            placeholder="Enter employee name..." value="<?php echo e($searchName ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All</option>
                            <option value="present" <?php echo e(($searchStatus ?? '') == 'present' ? 'selected' : ''); ?>>Present</option>
                            <option value="late" <?php echo e(($searchStatus ?? '') == 'late' ? 'selected' : ''); ?>>Late</option>
                            <option value="absent" <?php echo e(($searchStatus ?? '') == 'absent' ? 'selected' : ''); ?>>Absent</option>
                            <option value="half_day" <?php echo e(($searchStatus ?? '') == 'half_day' ? 'selected' : ''); ?>>Half Day</option>
                            <option value="paid_leave" <?php echo e(($searchStatus ?? '') == 'paid_leave' ? 'selected' : ''); ?>>Paid Leave</option>
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
                        <button type="button" class="btn btn-sm btn-outline-info" id="prevDayBtn">
                            <i class="mdi mdi-chevron-left"></i> Previous Day
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info ms-2" id="nextDayBtn">
                            <i class="mdi mdi-chevron-right"></i> Next Day
                        </button>
                        <a href="<?php echo e(route('attendance.index')); ?>" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="mdi mdi-refresh"></i> Reset Filters
                        </a>
                        <a href="<?php echo e(route('attendance.history')); ?>" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="mdi mdi-history"></i> View History
                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('Super Admin|Co-ordinator')): ?>
                        <button type="button" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                            <i class="mdi mdi-plus"></i> Manual Entry
                        </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                            <h4 class="mb-3"><?php echo e($totalEmployees); ?></h4>
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
                            <h4 class="mb-3 text-success"><?php echo e($presentCount); ?></h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="badge badge-soft-success rounded-pill fs-12">
                                    <?php echo e($totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100, 1) : 0); ?>%
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
                            <h4 class="mb-3 text-warning"><?php echo e($lateCount); ?></h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="badge badge-soft-warning rounded-pill fs-12">
                                    <?php echo e($totalEmployees > 0 ? round(($lateCount / $totalEmployees) * 100, 1) : 0); ?>%
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
                            <h4 class="mb-3 text-danger"><?php echo e($absentCount); ?></h4>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="badge badge-soft-danger rounded-pill fs-12">
                                    <?php echo e($totalEmployees > 0 ? round(($absentCount / $totalEmployees) * 100, 1) : 0); ?>%
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startDate == $endDate): ?>
                            - <?php echo e(\Carbon\Carbon::parse($startDate)->format('F j, Y')); ?>

                        <?php else: ?>
                            - <?php echo e(\Carbon\Carbon::parse($startDate)->format('M j')); ?> to <?php echo e(\Carbon\Carbon::parse($endDate)->format('M j, Y')); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="badge badge-soft-primary ms-2"><?php echo e($attendanceDetails->count()); ?> Records</span>
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attendanceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->user): ?>
                                                <div class="avatar-xs me-3">
                                                    <span
                                                        class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                        <?php echo e(substr($attendance->user->name, 0, 1)); ?>

                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($attendance->user->name); ?></h6>
                                                    <p class="text-muted mb-0"><?php echo e($attendance->user->email); ?></p>
                                                </div>
                                                <?php else: ?>
                                                <span class="text-muted"><em>User Deleted</em></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->login_time): ?>
                                                <span class="badge badge-soft-info">
                                                    <?php echo e($attendance->date ? $attendance->date->format('M d, Y') : ''); ?><br>
                                                    <?php echo e(\Carbon\Carbon::parse($attendance->login_time)->format('g:i A')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                                <span class="badge badge-soft-secondary">
                                                    <?php echo e(\Carbon\Carbon::parse($attendance->logout_time)->format('M d, Y g:i A')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Still working</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->status === 'present'): ?>
                                                <span class="badge badge-soft-success">On Time</span>
                                            <?php elseif($attendance->status === 'late'): ?>
                                                <span class="badge badge-soft-warning">Late</span>
                                            <?php elseif($attendance->status === 'absent'): ?>
                                                <span class="badge badge-soft-danger">Absent</span>
                                            <?php elseif($attendance->status === 'half_day'): ?>
                                                <span class="badge badge-soft-info">Half Day</span>
                                            <?php elseif($attendance->status === 'paid_leave'): ?>
                                                <span class="badge badge-soft-primary">Paid Leave</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->login_time): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                                    <?php
                                                        $loginTime = \Carbon\Carbon::parse($attendance->login_time);
                                                        $logoutTime = \Carbon\Carbon::parse($attendance->logout_time);
                                                        
                                                        // Handle night shift - if logout is before login, add a day
                                                        if ($logoutTime->lt($loginTime)) {
                                                            $logoutTime->addDay();
                                                        }
                                                        
                                                        $workingMinutes = $loginTime->diffInMinutes($logoutTime);
                                                        $hours = floor($workingMinutes / 60);
                                                        $minutes = $workingMinutes % 60;
                                                    ?>
                                                    <?php echo e($hours); ?>h <?php echo e($minutes); ?>m
                                                <?php else: ?>
                                                    
                                                    <span class="text-primary fw-semibold"><?php echo e($attendance->getFormattedCurrentWorkingHours()); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('Super Admin|Co-ordinator')): ?>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-soft-primary" 
                                                    onclick="editAttendance(<?php echo e($attendance->id); ?>)" 
                                                    title="Edit Attendance">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-soft-danger" 
                                                    onclick="deleteAttendance(<?php echo e($attendance->id); ?>, '<?php echo e($attendance->user ? $attendance->user->name : 'Unknown User'); ?>', '<?php echo e($attendance->date->format('M d, Y')); ?>')" 
                                                    title="Delete Attendance">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-account-off-outline font-size-48 mb-3 d-block"></i>
                                                No attendance records found for this date.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                        <span class="badge badge-soft-danger ms-2"><?php echo e($absentEmployees->count()); ?></span>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $absentEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                    <?php echo e(substr($employee->name, 0, 1)); ?>

                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?php echo e($employee->name); ?></h6>
                                <p class="text-muted mb-0 fs-13"><?php echo e($employee->email); ?></p>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-muted dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-horizontal"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#"
                                        onclick="markManualAttendance(<?php echo e($employee->id); ?>)">
                                        <i class="mdi mdi-plus me-1"></i> Mark Present
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted py-3">
                            <i class="mdi mdi-check-circle-outline font-size-24 mb-2 d-block text-success"></i>
                            All employees are present!
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $weeklyStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($stat['date']); ?></td>
                                        <td>
                                            <span class="badge badge-soft-primary">
                                                <?php echo e($stat['present'] + $stat['late']); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" style="width: <?php echo e($stat['percentage']); ?>%">
                                                </div>
                                            </div>
                                            <span class="fs-12 text-muted"><?php echo e($stat['percentage']); ?>%</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel" aria-hidden="true">
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                                    <input type="time" class="form-control" id="login_time" name="login_time" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logout_time" class="form-label">Logout Time</label>
                                    <input type="time" class="form-control" id="logout_time" name="logout_time">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="overnight_shift_alert" style="display: none;">
                            <div class="alert alert-info mb-0">
                                <i class="mdi mdi-information me-2"></i>
                                <strong>Overnight Shift Detected!</strong>
                                <p class="mb-0 mt-1 small">This shift crosses midnight. It will be saved to the start date with correct duration calculation.</p>
                                <p class="mb-0 small" id="shift_duration_display"></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status_select" class="form-label">Status</label>
                            <select class="form-select" id="status_select" name="status" required>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="half_day">Half Day</option>
                                <option value="paid_leave">Paid Leave</option>
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

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAttendanceForm" method="POST" action="#">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="edit_attendance_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <input type="text" class="form-control" id="edit_employee_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Attendance Date</label>
                            <input type="date" class="form-control" id="edit_date" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_login" class="form-label">Login Time</label>
                                    <input type="time" class="form-control" id="edit_login" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_logout" class="form-label">Logout Time</label>
                                    <input type="time" class="form-control" id="edit_logout">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" required>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="half_day">Half Day</option>
                                <option value="paid_leave">Paid Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!-- DataTables Bootstrap 4 -->
    <link href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet">
    <!-- Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Safe DataTable initialization - only once per page load
            if (window.attendanceTableInitialized) {
                return; // Already initialized, skip
            }
            window.attendanceTableInitialized = true;
            
            var tableElement = $('#attendance-table');
            
            // Verify table exists and has proper structure
            if (tableElement.length === 0) {
                console.warn('Attendance table not found');
                return;
            }
            
            // Check if table has actual data rows (not just empty state)
            var dataRows = tableElement.find('tbody tr:not(:has(td[colspan]))');
            if (dataRows.length === 0) {
                console.log('Table is empty, skipping DataTable initialization');
                return;
            }
            
            // If DataTable already exists, destroy it carefully
            if ($.fn.DataTable.isDataTable('#attendance-table')) {
                try {
                    tableElement.DataTable().clear().destroy();
                } catch (e) {
                    console.warn('Error destroying existing DataTable:', e);
                }
            }
            
            // Initialize DataTable
            try {
                tableElement.DataTable({
                    "pageLength": 25,
                    "responsive": true,
                    "order": [[1, "asc"]], // Sort by login time
                    "columnDefs": [{
                        "orderable": false,
                        "targets": [5] // Disable sorting for action column
                    }],
                    "language": {
                        "search": "Search:",
                        "searchPlaceholder": "Type to filter...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ records",
                        "infoEmpty": "No records available",
                        "infoFiltered": "(filtered from _MAX_ total records)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    },
                    "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                           "<'row'<'col-sm-12'tr>>" +
                           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });
                console.log('DataTable initialized successfully');
            } catch (e) {
                console.error('DataTable initialization error:', e);
            }
        });

        // Overnight shift detection for manual entry
        function checkOvernightShift() {
            const loginTime = document.getElementById('login_time').value;
            const logoutTime = document.getElementById('logout_time').value;
            const alertDiv = document.getElementById('overnight_shift_alert');
            const durationDisplay = document.getElementById('shift_duration_display');

            if (loginTime && logoutTime) {
                const [loginHour, loginMin] = loginTime.split(':').map(Number);
                const [logoutHour, logoutMin] = logoutTime.split(':').map(Number);
                
                // Calculate if it's overnight (login in evening, logout in morning)
                const isOvernight = loginHour >= 12 && logoutHour < 12;
                
                if (isOvernight) {
                    // Calculate duration across midnight
                    const loginMinutes = loginHour * 60 + loginMin;
                    const logoutMinutes = (logoutHour + 24) * 60 + logoutMin; // Add 24h for next day
                    const durationMinutes = logoutMinutes - loginMinutes;
                    const hours = Math.floor(durationMinutes / 60);
                    const minutes = durationMinutes % 60;
                    
                    durationDisplay.textContent = `Calculated Duration: ${hours}h ${minutes}m (${loginTime} → ${logoutTime} next day)`;
                    alertDiv.style.display = 'block';
                } else {
                    alertDiv.style.display = 'none';
                }
            } else {
                alertDiv.style.display = 'none';
            }
        }

        // Add event listeners for time inputs
        document.getElementById('login_time')?.addEventListener('change', checkOvernightShift);
        document.getElementById('logout_time')?.addEventListener('change', checkOvernightShift);

        // Filter by date
        function filterByDate() {
            const date = document.getElementById('attendance-date').value;
            window.location.href = `<?php echo e(route('attendance.index')); ?>?date=${date}`;
        }

        // Manual entry form submission
        document.getElementById('manualEntryForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const loginTime = document.getElementById('login_time').value;
            const logoutTime = document.getElementById('logout_time').value;
            
            // Debug: Log form data
            console.log('Manual Entry Form Data:', {
                user_id: formData.get('user_id'),
                date: formData.get('date'),
                login_time: formData.get('login_time'),
                logout_time: formData.get('logout_time'),
                status: formData.get('status')
            });
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            // Check if it's an overnight shift
            let isOvernightShift = false;
            if (loginTime && logoutTime) {
                const loginHour = parseInt(loginTime.split(':')[0]);
                const logoutHour = parseInt(logoutTime.split(':')[0]);
                // If login is evening (after 12pm) and logout is morning (before 12pm)
                isOvernightShift = loginHour >= 12 && logoutHour < 12;
            }

            fetch('<?php echo e(route('attendance.mark-manual.post')); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Close the modal
                        bootstrap.Modal.getInstance(document.getElementById('manualEntryModal')).hide();
                        
                        // Show success message with overnight shift info
                        let message = data.message;
                        if (data.attendance && data.attendance.is_overnight) {
                            message += `\n\nOvernight Shift Detected:\n` +
                                      `Start: ${data.attendance.login_time}\n` +
                                      `End: ${data.attendance.logout_time} (next day)\n` +
                                      `Duration: ${data.attendance.duration_hours}h`;
                        }
                        alert(message);
                        
                        // Reload page with the date filter set to the created attendance date
                        const createdDate = data.attendance.date || document.getElementById('attendance_date').value;
                        window.location.href = `<?php echo e(route('attendance.index')); ?>?start_date=${createdDate}&end_date=${createdDate}`;
                    } else {
                        alert('Error: ' + (data.message || 'Failed to save attendance'));
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Save Attendance';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving attendance. Please check the console for details.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Save Attendance';
                });
        });

        // Quick mark present for absent employees
        function markManualAttendance(userId) {
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0');

            document.getElementById('employee_select').value = userId;
            document.getElementById('attendance_date').value = '<?php echo e(date("Y-m-d")); ?>';
            document.getElementById('login_time').value = currentTime;
            document.getElementById('status_select').value = 'late';

            new bootstrap.Modal(document.getElementById('manualEntryModal')).show();
        }

        // Edit Attendance
        function editAttendance(id) {
            console.log('Editing attendance ID:', id);
            // Set form action to prevent default submission to current URL
            document.getElementById('editAttendanceForm').action = `/attendance/${id}/update`;
            
            fetch(`/attendance/${id}/json`)
                .then(res => {
                    console.log('Response status:', res.status);
                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.success) {
                        document.getElementById('edit_attendance_id').value = data.attendance.id;
                        document.getElementById('edit_employee_name').value = data.attendance.user_name;
                        document.getElementById('edit_date').value = data.attendance.date;
                        document.getElementById('edit_login').value = data.attendance.login_time || '';
                        document.getElementById('edit_logout').value = data.attendance.logout_time || '';
                        document.getElementById('edit_status').value = data.attendance.status;
                        new bootstrap.Modal(document.getElementById('editAttendanceModal')).show();
                    } else {
                        alert('Error: ' + (data.message || 'Could not load attendance'));
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    alert('Error loading attendance: ' + err.message);
                });
        }

        document.getElementById('editAttendanceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_attendance_id').value;
            const dateValue = document.getElementById('edit_date').value;
            const loginValue = document.getElementById('edit_login').value;
            const logoutValue = document.getElementById('edit_logout').value;
            const statusValue = document.getElementById('edit_status').value;

            console.log('Form values:', { id, dateValue, loginValue, logoutValue, statusValue });

            if (!dateValue || !loginValue || !statusValue) {
                alert('Please fill in all required fields (Date, Login Time, Status)');
                return;
            }

            const payload = {
                date: dateValue,
                login_time: loginValue,
                logout_time: logoutValue,
                status: statusValue
            };

            console.log('Sending JSON payload:', payload);

            fetch(`/attendance/${id}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                console.log('Update result:', data);
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Update failed'));
                }
            })
            .catch(err => {
                console.error('Update error:', err);
                alert('Error: ' + err.message);
            });
        });

        // Delete attendance record
        function deleteAttendance(id, employeeName, date) {
            if (!confirm(`Are you sure you want to delete attendance for ${employeeName} on ${date}?\n\nThis action cannot be undone.`)) {
                return;
            }

            fetch(`/attendance/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Attendance record deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete attendance'));
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                alert('Error deleting attendance: ' + err.message);
            });
        }

        // Previous/Next Day Navigation
        document.getElementById('prevDayBtn')?.addEventListener('click', function() {
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            
            if (startDateInput && startDateInput.value) {
                const currentDate = new Date(startDateInput.value);
                currentDate.setDate(currentDate.getDate() - 1);
                
                const newDate = currentDate.toISOString().split('T')[0];
                startDateInput.value = newDate;
                endDateInput.value = newDate;
                
                document.getElementById('filterForm').submit();
            }
        });

        document.getElementById('nextDayBtn')?.addEventListener('click', function() {
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            
            if (startDateInput && startDateInput.value) {
                const currentDate = new Date(startDateInput.value);
                currentDate.setDate(currentDate.getDate() + 1);
                
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                // Don't allow navigation beyond today
                if (currentDate <= today) {
                    const newDate = currentDate.toISOString().split('T')[0];
                    startDateInput.value = newDate;
                    endDateInput.value = newDate;
                    
                    document.getElementById('filterForm').submit();
                } else {
                    alert('Cannot navigate to future dates');
                }
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/attendance/index.blade.php ENDPATH**/ ?>