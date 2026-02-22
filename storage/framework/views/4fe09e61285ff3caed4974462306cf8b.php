<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title'); ?>
    Attendance History
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Attendance
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            History & Reports
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
                                <a href="<?php echo e(route('attendance.print-view')); ?>" class="btn btn-outline-success" target="_blank">
                                    <i class="mdi mdi-printer"></i> Print View
                                </a>
                                <a href="<?php echo e(route('attendance.index')); ?>" class="btn btn-primary">
                                    <i class="mdi mdi-arrow-left"></i> Back to Today
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="<?php echo e(route('attendance.history')); ?>" id="filterForm">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="<?php echo e($startDate); ?>" max="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="<?php echo e($endDate); ?>" max="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">Employee</label>
                                <select class="form-select" name="user_id" id="user_id">
                                    <option value="">All Employees</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php echo e($userId == $user->id ? 'selected' : ''); ?>>
                                            <?php echo e($user->name); ?><?php echo e($user->trashed() ? ' (Terminated)' : ''); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="">All Status</option>
                                    <option value="present" <?php echo e($status === 'present' ? 'selected' : ''); ?>>Present</option>
                                    <option value="late" <?php echo e($status === 'late' ? 'selected' : ''); ?>>Late</option>
                                    <option value="absent" <?php echo e($status === 'absent' ? 'selected' : ''); ?>>Absent</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i> Apply Filters
                                    </button>
                                    <a href="<?php echo e(route('attendance.history')); ?>" class="btn btn-outline-secondary">
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($summaryStats)): ?>
                                    <div class="d-flex justify-content-end">
                                        <div class="text-end">
                                            <div class="d-flex gap-4">
                                                <div>
                                                    <div class="text-muted small">Total Records</div>
                                                    <div class="fw-bold"><?php echo e($summaryStats['total_records']); ?></div>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Present</div>
                                                    <div class="fw-bold text-success"><?php echo e($summaryStats['present']); ?></div>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Late</div>
                                                    <div class="fw-bold text-warning"><?php echo e($summaryStats['late']); ?></div>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Avg Login</div>
                                                    <div class="fw-bold text-info"><?php echo e($summaryStats['avg_login_time']); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userId): ?>
                                    - <?php echo e($users->find($userId)->name ?? 'Unknown Employee'); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="badge badge-soft-primary ms-2"><?php echo e($attendances->total()); ?> Total</span>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->count() > 0): ?>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input attendance-checkbox" type="checkbox"
                                                        value="<?php echo e($attendance->id); ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?php echo e($attendance->date->format('M d, Y')); ?></span>
                                                    <small class="text-muted"><?php echo e($attendance->date->format('l')); ?></small>
                                                </div>
                                            </td>
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
                                                        <h6 class="mb-0"><?php echo e($attendance->user->name); ?>

                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->user->trashed()): ?>
                                                                <span class="badge bg-danger-subtle text-danger ms-1 u-fs-10">Terminated</span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </h6>
                                                        <p class="text-muted mb-0 fs-13"><?php echo e($attendance->user->email); ?>

                                                        </p>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-muted"><em>Unknown User</em></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-info fs-12">
                                                    <?php echo e($attendance->login_time ? \Carbon\Carbon::parse($attendance->login_time)->format('g:i A') : ''); ?>

                                                </span>
                                                <div class="text-muted fs-13">
                                                    <?php echo e($attendance->login_time ? \Carbon\Carbon::parse($attendance->login_time)->format('D, M j') : ''); ?>

                                                </div>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                                    <span class="badge badge-soft-secondary fs-12">
                                                        <?php echo e(\Carbon\Carbon::parse($attendance->logout_time)->format('g:i A')); ?>

                                                    </span>
                                                    <div class="text-muted fs-13">
                                                        <?php echo e(\Carbon\Carbon::parse($attendance->logout_time)->format('D, M j')); ?>

                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted fs-13">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->date->isToday()): ?>
                                                            Still working
                                                        <?php else: ?>
                                                            Not recorded
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                                    <?php
                                                        $workingMinutes = $attendance->login_time->diffInMinutes(
                                                            $attendance->logout_time,
                                                        );
                                                        $hours = floor($workingMinutes / 60);
                                                        $minutes = $workingMinutes % 60;
                                                        $isOvertime = $workingMinutes > 480; // More than 8 hours
                                                    ?>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold <?php echo e($isOvertime ? 'text-success' : ''); ?>">
                                                            <?php echo e($hours); ?>h <?php echo e($minutes); ?>m
                                                        </span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOvertime): ?>
                                                            <small class="text-success">
                                                                <i class="mdi mdi-clock-plus-outline"></i>
                                                                +<?php echo e(floor(($workingMinutes - 480) / 60)); ?>h
                                                                <?php echo e(($workingMinutes - 480) % 60); ?>m
                                                            </small>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                <?php elseif($attendance->date->isToday()): ?>
                                                    <?php
                                                        $now = \Carbon\Carbon::now('Asia/Karachi');
                                                        $loginTime = $attendance->login_time->copy()->setTimezone('Asia/Karachi');
                                                        $currentMinutes = abs($loginTime->diffInMinutes($now));
                                                        $currentHours = floor($currentMinutes / 60);
                                                        $currentMins = $currentMinutes % 60;
                                                    ?>
                                                    <span class="text-info">
                                                        <?php echo e($currentHours); ?>h <?php echo e($currentMins); ?>m
                                                        <small class="d-block text-muted">ongoing</small>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->status === 'present'): ?>
                                                    <span class="badge badge-soft-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>On Time
                                                    </span>
                                                <?php elseif($attendance->status === 'late'): ?>
                                                    <span class="badge badge-soft-warning">
                                                        <i class="mdi mdi-clock-alert me-1"></i>Late
                                                    </span>
                                                <?php elseif($attendance->status === 'absent'): ?>
                                                    <span class="badge badge-soft-danger">
                                                        <i class="mdi mdi-close-circle me-1"></i>Absent
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <code class="fs-13"><?php echo e($attendance->ip_address); ?></code>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->ip_address === 'Manual Entry' || $attendance->ip_address === 'Manual Entry by Admin'): ?>
                                                        <small class="text-warning">
                                                            <i class="mdi mdi-hand-okay"></i> Manual
                                                        </small>
                                                    <?php else: ?>
                                                        <small class="text-success">
                                                            <i class="mdi mdi-wifi"></i> Network
                                                        </small>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                                                            href="<?php echo e(route('attendance.employee-report', $attendance->user_id)); ?>">
                                                            <i
                                                                class="mdi mdi-account-details font-size-16 text-success me-1"></i>
                                                            View Employee Report
                                                        </a>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="viewDetails(<?php echo e($attendance->id); ?>)">
                                                            <i class="mdi mdi-eye font-size-16 text-info me-1"></i> View
                                                            Details
                                                        </a>
                                                        <?php if(auth()->check() && auth()->user()->canEditModule('attendance')): ?>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="editRecord(<?php echo e($attendance->id); ?>)">
                                                            <i class="mdi mdi-pencil font-size-16 text-primary me-1"></i>
                                                            Edit Record
                                                        </a>
                                                        <?php endif; ?>
                                                        <?php if(auth()->check() && auth()->user()->canDeleteInModule('attendance')): ?>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="deleteRecord(<?php echo e($attendance->id); ?>)">
                                                            <i class="mdi mdi-delete font-size-16 me-1"></i> Delete
                                                        </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="text-muted">
                                    Showing <?php echo e($attendances->firstItem()); ?> to <?php echo e($attendances->lastItem()); ?>

                                    of <?php echo e($attendances->total()); ?> results
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-end">
                                    <?php echo e($attendances->appends(request()->query())->links()); ?>

                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="mdi mdi-calendar-remove-outline font-size-48 mb-3 d-block"></i>
                                <h5>No attendance records found</h5>
                                <p>Try adjusting your filters or date range to see more results.</p>
                                <a href="<?php echo e(route('attendance.history')); ?>" class="btn btn-primary">
                                    <i class="mdi mdi-refresh"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                        <?php if(auth()->check() && auth()->user()->canEditModule('attendance')): ?>
                        <button class="btn btn-outline-info" onclick="bulkUpdateStatus()">
                            <i class="mdi mdi-pencil me-2"></i>Update Status
                        </button>
                        <?php endif; ?>
                        <?php if(auth()->check() && auth()->user()->canDeleteInModule('attendance')): ?>
                        <button class="btn btn-outline-danger" onclick="bulkDelete()">
                            <i class="mdi mdi-delete me-2"></i>Delete Selected
                        </button>
                        <?php endif; ?>
                    </div>
                    <div id="selected-count" class="text-muted mt-2">
                        No records selected
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Initialize DataTable with guard
        $(document).ready(function() {
            if (window.historyTableInitialized) {
                return;
            }
            window.historyTableInitialized = true;
            
            var tableElement = $('#historyTable');
            
            // Check if table has data rows
            var dataRows = tableElement.find('tbody tr:not(:has(td[colspan]))');
            if (dataRows.length === 0) {
                console.log('History table is empty, skipping DataTable initialization');
                return;
            }
            
            try {
                tableElement.DataTable({
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
                console.log('History DataTable initialized successfully');
            } catch (e) {
                console.error('History DataTable initialization error:', e);
            }
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
            exportForm.action = '<?php echo e(route('attendance.export')); ?>';
            exportForm.method = 'POST';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '<?php echo e(csrf_token()); ?>';
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/attendance/history.blade.php ENDPATH**/ ?>