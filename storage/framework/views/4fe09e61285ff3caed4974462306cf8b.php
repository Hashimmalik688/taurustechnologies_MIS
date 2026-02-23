<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title'); ?>
    Attendance History
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .hist-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .hist-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .hist-hdr h4 i { color: #d4af37; font-size: 1.2rem; }
    .hist-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }

    /* Quick-period pills */
    .qp-row { display: flex; flex-wrap: wrap; gap: .3rem; }
    .qp-pill {
        font-size: .64rem; font-weight: 600; padding: .22rem .55rem;
        border-radius: 22px; border: 1px solid rgba(0,0,0,.08);
        background: var(--bs-card-bg); color: var(--bs-surface-600);
        cursor: pointer; text-decoration: none; transition: all .15s;
    }
    .qp-pill:hover { border-color: #d4af37; color: #b89730; background: rgba(212,175,55,.06); }

    /* DataTable gold pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg,#d4af37,#e8c84a) !important;
        border-color: #d4af37 !important; color: #fff !important;
        border-radius: 6px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(212,175,55,.12) !important;
        border-color: rgba(212,175,55,.3) !important; color: #b89730 !important;
        border-radius: 6px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 22px; border: 1px solid rgba(0,0,0,.08);
        font-size: .75rem; padding: .2rem .4rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 22px; border: 1px solid rgba(0,0,0,.08);
        font-size: .75rem; padding: .25rem .6rem;
    }

    /* Working hours bar */
    .wh-bar-wrap { width: 60px; height: 5px; border-radius: 4px; background: rgba(0,0,0,.06); position: relative; }
    .wh-bar { height: 100%; border-radius: 4px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="hist-hdr">
        <div>
            <h4><i class="bx bx-history"></i> Attendance History & Reports</h4>
            <p>View, filter and export attendance records across all employees</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="act-btn a-info" onclick="exportData()"><i class="mdi mdi-download"></i> Export</button>
            <a href="<?php echo e(route('attendance.print-view')); ?>" class="act-btn a-success" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
            <a href="<?php echo e(route('attendance.index')); ?>" class="act-btn a-primary"><i class="mdi mdi-arrow-left"></i> Back to Today</a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.78rem;border-radius:.45rem" role="alert">
            <i class="mdi mdi-check-all me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.78rem;border-radius:.45rem" role="alert">
            <i class="mdi mdi-block-helper me-1"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($summaryStats)): ?>
    <div class="kpi-row">
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Total Records</div>
            <div class="kpi-val"><?php echo e($summaryStats['total_records']); ?></div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Present</div>
            <div class="kpi-val"><?php echo e($summaryStats['present']); ?></div>
        </div>
        <div class="kpi-card k-warn">
            <div class="kpi-lbl">Late</div>
            <div class="kpi-val"><?php echo e($summaryStats['late']); ?></div>
        </div>
        <div class="kpi-card k-teal">
            <div class="kpi-lbl">Avg Login</div>
            <div class="kpi-val"><?php echo e($summaryStats['avg_login_time']); ?></div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('attendance.history')); ?>" id="filterForm">
    <div class="ex-card pipe-filter-bar" style="flex-wrap:wrap">
        <span class="pipe-pill-lbl">From</span>
        <input type="text" class="pipe-pill-date sl-pill-date" name="start_date" id="start_date"
            value="<?php echo e($startDate); ?>" placeholder="Start date" style="min-width:110px">
        <span class="pipe-pill-lbl">To</span>
        <input type="text" class="pipe-pill-date sl-pill-date" name="end_date" id="end_date"
            value="<?php echo e($endDate); ?>" placeholder="End date" style="min-width:110px">

        <span class="pipe-pill-lbl">Employee</span>
        <select class="sl-pill-select" name="user_id" id="user_id" style="min-width:140px">
            <option value="">All Employees</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->id); ?>" <?php echo e($userId == $user->id ? 'selected' : ''); ?>>
                    <?php echo e($user->name); ?><?php echo e($user->trashed() ? ' (Terminated)' : ''); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>

        <span class="pipe-pill-lbl">Status</span>
        <select class="sl-pill-select" name="status" id="status" style="min-width:90px">
            <option value="">All Status</option>
            <option value="present" <?php echo e($status === 'present' ? 'selected' : ''); ?>>Present</option>
            <option value="late" <?php echo e($status === 'late' ? 'selected' : ''); ?>>Late</option>
            <option value="absent" <?php echo e($status === 'absent' ? 'selected' : ''); ?>>Absent</option>
        </select>

        <button type="submit" class="pipe-pill-apply"><i class="mdi mdi-magnify"></i> Apply</button>
        <a href="<?php echo e(route('attendance.history')); ?>" class="pipe-pill-clear"><i class="mdi mdi-close"></i> Reset</a>
    </div>
    </form>

    
    <div class="qp-row mb-3">
        <span class="pipe-pill-lbl" style="align-self:center;margin-right:.15rem">Quick:</span>
        <a href="#" class="qp-pill" onclick="setDateRange('today');return false">Today</a>
        <a href="#" class="qp-pill" onclick="setDateRange('yesterday');return false">Yesterday</a>
        <a href="#" class="qp-pill" onclick="setDateRange('thisWeek');return false">This Week</a>
        <a href="#" class="qp-pill" onclick="setDateRange('lastWeek');return false">Last Week</a>
        <a href="#" class="qp-pill" onclick="setDateRange('thisMonth');return false">This Month</a>
        <a href="#" class="qp-pill" onclick="setDateRange('lastMonth');return false">Last Month</a>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr">
            <i class="mdi mdi-table-large"></i>
            Attendance Records
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userId): ?>
                — <?php echo e($users->find($userId)->name ?? 'Unknown'); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="badge-count"><?php echo e($attendances->total()); ?> total</span>

            <div class="dropdown ms-2">
                <button class="act-btn a-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-size:.6rem">
                    <i class="mdi mdi-dots-vertical"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="font-size:.75rem">
                    <li><a class="dropdown-item" href="#" onclick="printTable()"><i class="mdi mdi-printer me-1"></i>Print</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportData()"><i class="mdi mdi-download me-1"></i>Export</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkActionModal"><i class="mdi mdi-checkbox-multiple-marked me-1"></i>Bulk Actions</a></li>
                </ul>
            </div>
        </div>

        <div class="sec-body" style="padding:.55rem .75rem">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->count() > 0): ?>
            <div class="table-responsive">
                <table class="ex-tbl" id="historyTable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:30px">
                                <input class="form-check-input" type="checkbox" id="selectAll" style="margin:0">
                            </th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <input class="form-check-input attendance-checkbox" type="checkbox" value="<?php echo e($attendance->id); ?>" style="margin:0">
                            </td>
                            <td>
                                <span style="font-weight:600"><?php echo e($attendance->date->format('M d, Y')); ?></span>
                                <span class="d-block" style="font-size:.65rem;color:var(--bs-surface-400)"><?php echo e($attendance->date->format('l')); ?></span>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->user): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary" style="width:26px;height:26px;font-size:.65rem;display:inline-flex;align-items:center;justify-content:center">
                                        <?php echo e(substr($attendance->user->name, 0, 1)); ?>

                                    </span>
                                    <div>
                                        <span style="font-weight:600;font-size:.78rem"><?php echo e($attendance->user->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->user->trashed()): ?>
                                            <span class="s-pill s-declined" style="margin-left:3px">Terminated</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="d-block" style="font-size:.65rem;color:var(--bs-surface-400)"><?php echo e($attendance->user->email); ?></span>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <em style="color:var(--bs-surface-400);font-size:.75rem">Unknown User</em>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->login_time): ?>
                                <span style="font-weight:600;font-size:.78rem"><?php echo e(\Carbon\Carbon::parse($attendance->login_time)->format('g:i A')); ?></span>
                                <span class="d-block" style="font-size:.63rem;color:var(--bs-surface-400)"><?php echo e(\Carbon\Carbon::parse($attendance->login_time)->format('D, M j')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                    <span style="font-weight:600;font-size:.78rem"><?php echo e(\Carbon\Carbon::parse($attendance->logout_time)->format('g:i A')); ?></span>
                                    <span class="d-block" style="font-size:.63rem;color:var(--bs-surface-400)"><?php echo e(\Carbon\Carbon::parse($attendance->logout_time)->format('D, M j')); ?></span>
                                <?php else: ?>
                                    <span style="font-size:.72rem;color:var(--bs-surface-400)">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->date->isToday()): ?> Still working <?php else: ?> Not recorded <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->logout_time): ?>
                                    <?php
                                        $workingMinutes = $attendance->login_time->diffInMinutes($attendance->logout_time);
                                        $hours = floor($workingMinutes / 60);
                                        $minutes = $workingMinutes % 60;
                                        $isOvertime = $workingMinutes > 480;
                                        $pct = min(100, round(($workingMinutes / 480) * 100));
                                    ?>
                                    <span style="font-weight:700;font-size:.78rem;<?php echo e($isOvertime ? 'color:#059669' : ''); ?>"><?php echo e($hours); ?>h <?php echo e($minutes); ?>m</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOvertime): ?>
                                        <span class="d-block" style="font-size:.6rem;color:#059669"><i class="mdi mdi-clock-plus-outline"></i> +<?php echo e(floor(($workingMinutes - 480) / 60)); ?>h <?php echo e(($workingMinutes - 480) % 60); ?>m OT</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="wh-bar-wrap mt-1">
                                        <div class="wh-bar" style="width:<?php echo e($pct); ?>%;background:<?php echo e($isOvertime ? '#059669' : ($pct >= 80 ? '#d4af37' : '#94a3b8')); ?>"></div>
                                    </div>
                                <?php elseif($attendance->date->isToday()): ?>
                                    <?php
                                        $now = \Carbon\Carbon::now('America/Denver');
                                        $loginTime = $attendance->login_time->copy()->setTimezone('America/Denver');
                                        $currentMinutes = abs($loginTime->diffInMinutes($now));
                                        $currentHours = floor($currentMinutes / 60);
                                        $currentMins = $currentMinutes % 60;
                                    ?>
                                    <span style="font-size:.75rem;color:var(--bs-info)"><?php echo e($currentHours); ?>h <?php echo e($currentMins); ?>m</span>
                                    <span class="d-block" style="font-size:.6rem;color:var(--bs-surface-400)">ongoing</span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-300)">&#8212;</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->status === 'present'): ?>
                                    <span class="s-pill s-sale"><i class="mdi mdi-check-circle me-1"></i>On Time</span>
                                <?php elseif($attendance->status === 'late'): ?>
                                    <span class="s-pill s-pending"><i class="mdi mdi-clock-alert me-1"></i>Late</span>
                                <?php elseif($attendance->status === 'absent'): ?>
                                    <span class="s-pill s-declined"><i class="mdi mdi-close-circle me-1"></i>Absent</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->ip_address === 'Manual Entry' || $attendance->ip_address === 'Manual Entry by Admin'): ?>
                                    <span class="s-pill s-pending"><i class="mdi mdi-hand-okay"></i> Manual</span>
                                <?php else: ?>
                                    <span class="s-pill s-sale"><i class="mdi mdi-wifi"></i> Network</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a href="#" class="act-btn a-primary" data-bs-toggle="dropdown"><i class="mdi mdi-dots-horizontal"></i></a>
                                    <div class="dropdown-menu dropdown-menu-end" style="font-size:.75rem">
                                        <a class="dropdown-item" href="<?php echo e(route('attendance.employee-report', $attendance->user_id)); ?>">
                                            <i class="mdi mdi-account-details text-success me-1"></i> Employee Report
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="viewDetails(<?php echo e($attendance->id); ?>)">
                                            <i class="mdi mdi-eye text-info me-1"></i> View Details
                                        </a>
                                        <?php if(auth()->check() && auth()->user()->canEditModule('attendance')): ?>
                                        <a class="dropdown-item" href="#" onclick="editRecord(<?php echo e($attendance->id); ?>)">
                                            <i class="mdi mdi-pencil text-primary me-1"></i> Edit
                                        </a>
                                        <?php endif; ?>
                                        <?php if(auth()->check() && auth()->user()->canDeleteInModule('attendance')): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="deleteRecord(<?php echo e($attendance->id); ?>)">
                                            <i class="mdi mdi-delete me-1"></i> Delete
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

            
            <div class="d-flex justify-content-between align-items-center mt-3" style="font-size:.72rem;color:var(--bs-surface-400)">
                <span>Showing <?php echo e($attendances->firstItem()); ?>–<?php echo e($attendances->lastItem()); ?> of <?php echo e($attendances->total()); ?></span>
                <div><?php echo e($attendances->appends(request()->query())->links()); ?></div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="mdi mdi-calendar-remove-outline d-block mb-2" style="font-size:2.5rem;opacity:.35;color:var(--bs-surface-400)"></i>
                <p style="font-weight:600;font-size:.85rem;color:var(--bs-surface-500)">No attendance records found</p>
                <p style="font-size:.72rem;color:var(--bs-surface-400)">Try adjusting your filters or date range.</p>
                <a href="<?php echo e(route('attendance.history')); ?>" class="act-btn a-primary"><i class="mdi mdi-refresh"></i> Reset Filters</a>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="modal fade" id="bulkActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-glass">
                    <h5 class="modal-title"><i class="mdi mdi-checkbox-multiple-marked me-1 text-warning"></i> Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:.78rem;color:var(--bs-surface-500)">Perform actions on selected attendance records:</p>
                    <div class="d-grid gap-2">
                        <button class="act-btn a-info w-100 justify-content-center py-2" onclick="bulkExport()">
                            <i class="mdi mdi-download me-1"></i>Export Selected
                        </button>
                        <?php if(auth()->check() && auth()->user()->canEditModule('attendance')): ?>
                        <button class="act-btn a-primary w-100 justify-content-center py-2" onclick="bulkUpdateStatus()">
                            <i class="mdi mdi-pencil me-1"></i>Update Status
                        </button>
                        <?php endif; ?>
                        <?php if(auth()->check() && auth()->user()->canDeleteInModule('attendance')): ?>
                        <button class="act-btn a-danger w-100 justify-content-center py-2" onclick="bulkDelete()">
                            <i class="mdi mdi-delete me-1"></i>Delete Selected
                        </button>
                        <?php endif; ?>
                    </div>
                    <div id="selected-count" class="text-muted mt-2" style="font-size:.72rem">No records selected</div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>

    <script>
        $(document).ready(function() {
            if (window.historyTableInitialized) return;
            window.historyTableInitialized = true;

            var tableElement = $('#historyTable');
            var dataRows = tableElement.find('tbody tr:not(:has(td[colspan]))');
            if (dataRows.length === 0) return;

            try {
                tableElement.DataTable({
                    "pageLength": 25,
                    "responsive": true,
                    "order": [[1, "desc"]],
                    "columnDefs": [{ "orderable": false, "targets": [0, 8] }],
                    "paging": false,
                    "info": false,
                    "searching": true
                });
            } catch (e) {
                console.error('History DataTable init error:', e);
            }
        });

        // Date range shortcuts
        function setDateRange(period) {
            const today = new Date();
            let startDate, endDate;

            switch (period) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0]; break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    startDate = endDate = yesterday.toISOString().split('T')[0]; break;
                case 'thisWeek':
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay());
                    startDate = startOfWeek.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0]; break;
                case 'lastWeek':
                    const lastWeekEnd = new Date(today);
                    lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                    const lastWeekStart = new Date(lastWeekEnd);
                    lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                    startDate = lastWeekStart.toISOString().split('T')[0];
                    endDate = lastWeekEnd.toISOString().split('T')[0]; break;
                case 'thisMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0]; break;
                case 'lastMonth':
                    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                    startDate = lastMonth.toISOString().split('T')[0];
                    endDate = lastMonthEnd.toISOString().split('T')[0]; break;
            }

            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
            // Update Flatpickr instances
            document.getElementById('start_date')._flatpickr?.setDate(startDate, false);
            document.getElementById('end_date')._flatpickr?.setDate(endDate, false);
            document.getElementById('filterForm').submit();
        }

        // Select all
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.attendance-checkbox').forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
        document.querySelectorAll('.attendance-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.attendance-checkbox:checked').length;
            const el = document.getElementById('selected-count');
            if (selected > 0) {
                el.textContent = selected + ' record(s) selected';
                el.className = 'text-primary mt-2';
                el.style.fontSize = '.72rem';
            } else {
                el.textContent = 'No records selected';
                el.className = 'text-muted mt-2';
                el.style.fontSize = '.72rem';
            }
        }

        function exportData() {
            const form = document.getElementById('filterForm');
            const exportForm = form.cloneNode(true);
            exportForm.action = '<?php echo e(route("attendance.export")); ?>';
            exportForm.method = 'POST';
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = '<?php echo e(csrf_token()); ?>';
            exportForm.appendChild(csrfInput);
            document.body.appendChild(exportForm);
            exportForm.submit();
            document.body.removeChild(exportForm);
        }

        function printTable() { window.print(); }

        function viewDetails(id) { alert('View details for record ID: ' + id); }
        function editRecord(id) { alert('Edit record ID: ' + id); }
        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                alert('Delete record ID: ' + id);
            }
        }

        function bulkExport() {
            const s = getSelectedIds();
            if (!s.length) { alert('Please select records to export'); return; }
            alert('Export ' + s.length + ' records');
        }
        function bulkUpdateStatus() {
            const s = getSelectedIds();
            if (!s.length) { alert('Please select records to update'); return; }
            alert('Update status for ' + s.length + ' records');
        }
        function bulkDelete() {
            const s = getSelectedIds();
            if (!s.length) { alert('Please select records to delete'); return; }
            if (confirm('Delete ' + s.length + ' attendance records?')) {
                alert('Delete ' + s.length + ' records');
            }
        }
        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.attendance-checkbox:checked')).map(cb => cb.value);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/attendance/history.blade.php ENDPATH**/ ?>