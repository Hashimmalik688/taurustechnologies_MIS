<?php $__env->startSection('title', 'Executive Dashboard'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* Compact Dashboard Styles */
.page-header {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.page-title {
    font-size: 1.25rem;
    margin: 0;
}

.page-subtitle {
    font-size: 0.8rem;
    color: #6b7280;
}

.time-display {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.time-box {
    text-align: center;
}

.time-box .label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
}

.time-box .time {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--gold);
}

/* Compact Stat Cards */
.stat-card.compact {
    padding: 0.75rem;
}

.stat-card.compact .stat-icon {
    width: 32px;
    height: 32px;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.stat-card.compact .stat-value {
    font-size: 1.5rem;
}

.stat-card.compact .stat-label {
    font-size: 0.65rem;
}

/* Compact Status Boxes */
.status-box.compact {
    padding: 0.75rem;
}

.status-box.compact.bordered {
    border: 2px solid;
}

.status-box.compact.bordered.blue {
    border-color: #3b82f6;
}

.status-box.compact.bordered.green {
    border-color: #10b981;
}

.status-box.compact.bordered.yellow {
    border-color: #f59e0b;
}

.status-box.compact.bordered.red {
    border-color: #ef4444;
}

.status-box.compact .status-number {
    font-size: 1.5rem;
}

.status-box.compact .status-label {
    font-size: 0.65rem;
}

/* Compact Team Tabs */
.team-tab.compact {
    padding: 0.75rem;
}

.team-tab.compact .tab-title {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.team-tab.compact .tab-count {
    font-size: 1.5rem;
}

/* Compact Cards */
.compact-card {
    margin-bottom: 0;
}

.compact-card.bordered {
    border: 2px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.compact-header {
    padding: 0.5rem 0.75rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
}

.compact-header h6 {
    font-size: 0.85rem;
    font-weight: 600;
}

.compact-body {
    padding: 0.75rem;
}

/* Compact Table */
.table-sm th, .table-sm td {
    padding: 0.4rem;
    font-size: 0.8rem;
}

.table-sm thead th {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    background: #f8f9fa;
}

/* Mini Stat Values */
.mini-stat-value.small {
    font-size: 1.25rem;
    font-weight: 700;
}

.mini-stat-label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
}

/* Team Item Compact */
.team-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.4rem;
    margin-bottom: 0.35rem;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 2px solid var(--gold);
}

.team-item .name {
    font-weight: 600;
    font-size: 0.8rem;
    color: #1f2937;
}

.team-item .badge {
    font-size: 0.65rem;
}

/* Badge Sizes */
.badge-xs {
    font-size: 0.65rem;
    padding: 0.2rem 0.4rem;
}

/* Button Sizes */
.btn-xs {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Text Gold */
.text-gold {
    color: var(--gold) !important;
}

/* Retention Blocks */
.retention-block {
    background: white;
    border: 2px solid;
    border-radius: 8px;
    padding: 0.5rem;
    transition: all 0.2s;
}

.retention-block:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.retention-block.cb {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.05);
}

.retention-block.retained {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.05);
}

.retention-block.pending {
    border-color: #f59e0b;
    background: rgba(245, 158, 11, 0.05);
}

.ret-number {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}

.retention-block.cb .ret-number {
    color: #ef4444;
}

.retention-block.retained .ret-number {
    color: #10b981;
}

.retention-block.pending .ret-number {
    color: #f59e0b;
}

.ret-label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Chargebacks Display */
.cb-count {
    font-size: 2rem;
    font-weight: 700;
    color: #ef4444;
    line-height: 1;
}

.cb-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
    margin: 0.25rem 0;
}

.cb-amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: #ef4444;
}

/* Button Gold */
.btn-gold {
    background: var(--gold);
    color: white;
    border: none;
}

.btn-gold:hover {
    background: #b8941f;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        text-align: center;
    }

    .time-display {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bx bx-grid-alt"></i> Executive Dashboard</h1>
        <div class="page-subtitle">Welcome back! Here's what's happening with your sales today.</div>
    </div>
    <div class="time-display">
        <div class="time-box">
            <div class="label">USA</div>
            <div class="time" id="floridaTime">--:--:--</div>
        </div>
        <div class="time-box">
            <div class="label">Pakistan</div>
            <div class="time" id="pakistanTime">--:--:--</div>
        </div>
    </div>
    <div class="action-buttons">
        <button class="btn btn-outline-gold btn-sm" onclick="window.location.reload()">
            <i class="bx bx-refresh"></i> Refresh
        </button>
    </div>
</div>

<?php if(session('attendance_manual_needed')): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert" id="attendance-manual-banner" style="margin-bottom: 1rem;">
        <div style="flex:1">
            <strong>Mark Attendance:</strong>
            <div><?php echo e(session('attendance_manual_needed')); ?></div>
        </div>
        <div style="margin-left: 1rem">
            <button id="markAttendanceBtn" class="btn btn-gold btn-sm">Mark Attendance</button>
            <button id="markAttendanceForceBtn" class="btn btn-outline-secondary btn-sm">Mark (Force)</button>
        </div>
    </div>
    <script>
        (function(){
            const btn = document.getElementById('markAttendanceBtn');
            const btnForce = document.getElementById('markAttendanceForceBtn');
            const banner = document.getElementById('attendance-manual-banner');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function postMark(force) {
                btn.disabled = true;
                btnForce.disabled = true;

                fetch('<?php echo e(route('attendance.mark-manual.post')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ force_office: force ? 1 : 0 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // simple success feedback, then hide the banner
                        alert(data.message || 'Attendance marked successfully');
                        if (banner) banner.style.display = 'none';
                        // optionally reload to refresh attendance counts
                        setTimeout(() => location.reload(), 600);
                    } else {
                        alert(data.message || 'Could not mark attendance: ' + (data.debug_ip || ''));
                        btn.disabled = false;
                        btnForce.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error while marking attendance');
                    btn.disabled = false;
                    btnForce.disabled = false;
                });
            }

            btn && btn.addEventListener('click', function(){ postMark(false); });
            btnForce && btnForce.addEventListener('click', function(){
                if (confirm('Force mark attendance (this will override network check)?')) {
                    postMark(true);
                }
            });
        })();
    </script>
<?php endif; ?>

<!-- Top Statistics Section with Right Sidebar -->
<div class="row g-2 mb-3">
    <!-- Left: 8 Stat Boxes -->
    <div class="col-lg-9">
        <!-- Top 4 Metrics -->
        <div class="row g-2 mb-2">
            <div class="col-lg-3 col-md-3 col-6">
                <div class="stat-card compact">
                    <div class="stat-icon gold">
                        <i class="bx bx-trending-up"></i>
                    </div>
                    <div class="stat-value" id="salesToday"><?php echo e($total_sales_today); ?></div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="stat-card compact">
                    <div class="stat-icon info">
                        <i class="bx bx-bar-chart-alt-2"></i>
                    </div>
                    <div class="stat-value" id="salesMTD"><?php echo e($total_monthly_sales); ?></div>
                    <div class="stat-label">MTD Sales</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="stat-card compact">
                    <div class="stat-icon success">
                        <i class="bx bx-dollar-circle"></i>
                    </div>
                    <div class="stat-value" id="revenue">$<?php echo e(number_format($total_revenue, 0)); ?></div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="stat-card compact">
                    <div class="stat-icon gold">
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="stat-value" id="activeTeam"><?php echo e(count($attendance)); ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
        </div>

        <!-- Bottom 4 Status Boxes -->
        <div class="row g-2 mb-2">
            <div class="col-lg-3 col-md-3 col-6">
                <div class="status-box blue compact bordered">
                    <div class="status-number" id="statusDone"><?php echo e($done_count); ?></div>
                    <div class="status-label">Submitted</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="status-box green compact bordered">
                    <div class="status-number" id="statusApproved"><?php echo e($approved_count); ?></div>
                    <div class="status-label">Approved</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="status-box yellow compact bordered">
                    <div class="status-number" id="statusUW"><?php echo e($underwriting_count); ?></div>
                    <div class="status-label">UW</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="status-box red compact bordered">
                    <div class="status-number" id="statusDeclined"><?php echo e($declined_count); ?></div>
                    <div class="status-label">Declined</div>
                </div>
            </div>
        </div>

        <!-- Live Team and Attendance Row (Below 4 Status Boxes) -->
        <div class="row g-2 mb-2">
            <!-- Live Team Performance (Left - 9 cols) -->
            <div class="col-lg-9">
                <div class="card compact-card bordered">
                    <div class="card-header compact-header d-flex justify-content-between align-items-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-gold btn-sm active" onclick="switchTeam('paraguins')" id="paraguinsTab">
                                Paraguins (<span id="paraguinsCount"><?php echo e($paraguins_count ?? 0); ?></span>)
                            </button>
                            <button type="button" class="btn btn-outline-gold btn-sm" onclick="switchTeam('ravens')" id="ravensTab">
                                Ravens (<span id="ravensCount"><?php echo e($ravens_count ?? 0); ?></span>)
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 1;">
                                    <tr>
                                        <th>Closer</th>
                                        <th class="text-center">Today</th>
                                        <th class="text-center">MTD</th>
                                        <th class="text-center">Approved</th>
                                        <th class="text-center">Declined</th>
                                        <th class="text-center">UW</th>
                                    </tr>
                                </thead>
                                <tbody id="closerTable">
                                    <?php $__empty_1 = true; $__currentLoopData = $sales_per_closer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $closer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="closer-row" data-team="<?php echo e($closer['team'] ?? ''); ?>">
                                        <td><i class="bx bx-user-circle me-1 text-gold"></i><?php echo e($closer['closer'] ?? 'N/A'); ?></td>
                                        <td class="text-center"><span class="badge badge-xs bg-info"><?php echo e($closer['today'] ?? 0); ?></span></td>
                                        <td class="text-center"><span class="badge badge-xs bg-primary"><?php echo e($closer['mtd'] ?? 0); ?></span></td>
                                        <td class="text-center"><span class="badge badge-xs bg-success"><?php echo e($closer['approvedMTD'] ?? 0); ?></span></td>
                                        <td class="text-center"><span class="badge badge-xs bg-danger"><?php echo e($closer['declinedMTD'] ?? 0); ?></span></td>
                                        <td class="text-center"><span class="badge badge-xs bg-warning"><?php echo e($closer['uwMTD'] ?? $closer['uw'] ?? $closer['underwriting'] ?? 0); ?></span></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">No closers data available</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance (Right - 3 cols) -->
            <div class="col-lg-3">
                <div class="card compact-card bordered">
                    <div class="card-header compact-header">
                        <h6 class="mb-0"><i class="bx bx-time-five"></i> Attendance</h6>
                    </div>
                    <div class="card-body compact-body">
                        <!-- Summary Counts - Compact Capsules -->
                        <div class="d-flex gap-2 justify-content-center mb-2">
                            <div class="px-3 py-1" style="background: rgba(16, 185, 129, 0.15); border-radius: 20px; border: 1px solid #10b981; display: inline-flex; align-items: center; gap: 6px;">
                                <span style="font-size: 1.1rem; font-weight: 700; color: #10b981;" id="presentCount"><?php echo e($present_count); ?></span>
                                <span style="font-size: 0.7rem; font-weight: 600; color: #065f46;">P</span>
                            </div>
                            <div class="px-3 py-1" style="background: rgba(239, 68, 68, 0.15); border-radius: 20px; border: 1px solid #ef4444; display: inline-flex; align-items: center; gap: 6px;">
                                <span style="font-size: 1.1rem; font-weight: 700; color: #ef4444;" id="absentCount"><?php echo e($absent_count); ?></span>
                                <span style="font-size: 0.7rem; font-weight: 600; color: #991b1b;">A</span>
                            </div>
                        </div>

                        <!-- Attendance Table -->
                        <div style="max-height: 130px; overflow-y: auto;">
                            <table class="table table-sm mb-0">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 1;">
                                    <tr>
                                        <th style="font-size: 0.7rem;">Name</th>
                                        <th class="text-center" style="font-size: 0.7rem;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTable" style="font-size: 0.75rem;">
                                    <?php $__empty_1 = true; $__currentLoopData = $attendance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $status = strtolower($att['status'] ?? '');
                                        $isPresent = in_array($status, ['present', 'p', 'on time', 'ontime', 'late', 'half day']);
                                        $badgeClass = $isPresent ? 'bg-success' : 'bg-danger';
                                        $statusText = $isPresent ? 'Present' : ucfirst($att['status'] ?? 'Absent');
                                    ?>
                                    <tr>
                                        <td><?php echo e($att['name'] ?? 'N/A'); ?></td>
                                        <td class="text-center"><span class="badge badge-xs <?php echo e($badgeClass); ?>"><?php echo e($statusText); ?></span></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="2" class="text-center py-3 text-muted">No attendance data</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Monthly Target + Attendance + Retention + Chargebacks -->
    <div class="col-lg-3">
        <!-- Monthly Target Pie Chart -->
        <div class="card compact-card bordered mb-2">
            <div class="card-header compact-header">
                <h6 class="mb-0"><i class="bx bx-target-lock"></i> Monthly Target</h6>
            </div>
            <div class="card-body compact-body text-center p-2">
                <canvas id="monthlyTargetChart" style="height: 140px; max-height: 140px;"></canvas>
                <div class="mt-2" style="font-size: 0.75rem;">
                    <div><span class="text-muted">Target:</span> <strong class="text-gold">500</strong></div>
                    <div><span class="text-muted">Achieved:</span> <strong class="text-success"><?php echo e($total_monthly_sales); ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Retention -->
        <div class="card compact-card bordered mb-2">
            <div class="card-header compact-header">
                <h6 class="mb-0"><i class="bx bx-refresh"></i> Retention</h6>
            </div>
            <div class="card-body compact-body">
                <div class="row g-1 text-center">
                    <div class="col-4">
                        <div class="retention-block cb">
                            <div class="ret-number" id="retCB"><?php echo e($retention_cb); ?></div>
                            <div class="ret-label">CB</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="retention-block retained">
                            <div class="ret-number" id="retRetained"><?php echo e($retention_retained); ?></div>
                            <div class="ret-label">Retained</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="retention-block pending">
                            <div class="ret-number" id="retPending"><?php echo e($retention_pending); ?></div>
                            <div class="ret-label">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chargebacks -->
        <div class="card compact-card bordered">
            <div class="card-header compact-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bx bx-error"></i> Chargebacks</h6>
                <a href="<?php echo e(route('chargebacks.index')); ?>" class="btn btn-xs btn-outline-gold">Details</a>
            </div>
            <div class="card-body compact-body">
                <div class="text-center">
                    <div class="cb-count" id="cbThis"><?php echo e($cb_this_count); ?></div>
                    <div class="cb-label">This Month</div>
                    <div class="cb-amount" id="cbThisAmt">$<?php echo e(number_format($cb_this_amt, 0)); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chargebacks Details Modal -->
<div class="modal fade" id="chargebacksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-error"></i> Chargebacks Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter by Period</label>
                        <select class="form-select form-select-sm" id="cbPeriodFilter">
                            <option value="this_month" selected>This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="this_year">This Year</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Closer</th>
                                <th>Customer</th>
                                <th>Policy #</th>
                                <th>Amount</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jan 15, 2025</td>
                                <td>John Doe</td>
                                <td>Michael Brown</td>
                                <td>POL-12345</td>
                                <td><span class="text-danger fw-bold">$850</span></td>
                                <td><span class="badge bg-warning">Card Declined</span></td>
                            </tr>
                            <tr>
                                <td>Jan 18, 2025</td>
                                <td>Jane Smith</td>
                                <td>Sarah Johnson</td>
                                <td>POL-12389</td>
                                <td><span class="text-danger fw-bold">$1,200</span></td>
                                <td><span class="badge bg-danger">Insufficient Funds</span></td>
                            </tr>
                            <tr>
                                <td>Jan 20, 2025</td>
                                <td>Mike Wilson</td>
                                <td>David Lee</td>
                                <td>POL-12401</td>
                                <td><span class="text-danger fw-bold">$950</span></td>
                                <td><span class="badge bg-warning">Customer Dispute</span></td>
                            </tr>
                            <tr>
                                <td>Jan 22, 2025</td>
                                <td>John Doe</td>
                                <td>Emily Davis</td>
                                <td>POL-12456</td>
                                <td><span class="text-danger fw-bold">$1,100</span></td>
                                <td><span class="badge bg-danger">Insufficient Funds</span></td>
                            </tr>
                            <tr>
                                <td>Jan 25, 2025</td>
                                <td>Sarah Connor</td>
                                <td>James Taylor</td>
                                <td>POL-12502</td>
                                <td><span class="text-danger fw-bold">$750</span></td>
                                <td><span class="badge bg-warning">Card Declined</span></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="4" class="text-end fw-bold">Total Chargebacks:</td>
                                <td colspan="2"><span class="text-danger fw-bold fs-6">$12,450</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-gold btn-sm"><i class="bx bx-download"></i> Export</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentTeam = 'live';
let salesChart = null;
let allData = null;

// Server-side data
const serverData = {
    totalSalesToday: <?php echo e($total_sales_today); ?>,
    done: <?php echo e($done_count); ?>,
    totalRevenue: <?php echo e($total_revenue); ?>,
    approved: <?php echo e($approved_count); ?>,
    underwriting: <?php echo e($underwriting_count); ?>,
    declined: <?php echo e($declined_count); ?>,
    salesPerCloser: <?php echo json_encode($sales_per_closer, 15, 512) ?>,
    attendance: <?php echo json_encode($attendance, 15, 512) ?>,
    retention: {
        cb: <?php echo e($retention_cb); ?>,
        retained: <?php echo e($retention_retained); ?>,
        pending: <?php echo e($retention_pending); ?>

    },
    chargebacks: {
        thisMonth: {
            count: <?php echo e($cb_this_count); ?>,
            amount: <?php echo e($cb_this_amt); ?>

        },
        lastMonth: {
            count: <?php echo e($cb_last_count); ?>,
            amount: <?php echo e($cb_last_amt); ?>

        }
    }
};

// Update Clocks
function updateClocks() {
    const now = new Date();

    // Florida (EST/EDT)
    const floridaTime = now.toLocaleTimeString('en-US', {
        timeZone: 'America/New_York',
        hour12: false
    });
    $('#floridaTime').text(floridaTime);

    // Pakistan (PKT)
    const pakistanTime = now.toLocaleTimeString('en-US', {
        timeZone: 'Asia/Karachi',
        hour12: false
    });
    $('#pakistanTime').text(pakistanTime);
}

// Load Data
function loadData() {
    updateUI(serverData);
}

function updateUI(d) {
    // Top Metrics
    $('#salesToday').text(d.totalSalesToday || 0);
    $('#salesMTD').text(d.done || d.TOTAL || 0);
    $('#revenue').text('$' + fmt(d.totalRevenue || 0));
    $('#activeTeam').text((d.attendance || []).length);
    
    // Status Cards
    $('#statusDone').text(d.done || 0);
    $('#statusApproved').text(d.approved || 0);
    $('#statusUW').text(d.underwriting || d.UW || 0);
    $('#statusDeclined').text(d.declined || 0);

    // Call Center Metrics - Calculate from salesPerCloser data
    const totalSales = d.done || 0;
    const totalClosers = (d.salesPerCloser || []).length;
    const avgSalesPerCloser = totalClosers > 0 ? Math.round(totalSales / totalClosers) : 0;
    const conversionRate = totalSales > 0 ? ((d.approved || 0) / totalSales * 100).toFixed(1) : 0;

    $('#totalCalls').text(totalSales);
    $('#connectedCalls').text(d.approved || 0);
    $('#avgCallTime').text(avgSalesPerCloser);
    $('#conversionRate').text(conversionRate + '%');
    
    // Team Data
    if (d.salesPerCloser) {
        const live = d.salesPerCloser.filter(c => (c.team || '').toLowerCase() === 'live');
        const reselling = d.salesPerCloser.filter(c => (c.team || '').toLowerCase() === 'reselling');

        $('#liveCount').text(live.length);
        $('#resellingCount').text(reselling.length);

        renderClosers(currentTeam === 'live' ? live : reselling);
        updateCharts(d.salesPerCloser);
        allData = d;
    }
    
    // Attendance
    if (d.attendance) renderTeam(d.attendance);
    
    // Retention
    const ret = d.retention || d.retentionTracking || {};
    $('#retCB').text(ret.cb || ret.CB || 0);
    $('#retRetained').text(ret.retained || 0);
    $('#retPending').text(ret.pending || ret.yetToRetain || 0);
    
    // Chargebacks
    if (d.chargebacks) {
        $('#cbThis').text(d.chargebacks.thisMonth?.count || 0);
        $('#cbThisAmt').text('$' + fmt(d.chargebacks.thisMonth?.amount || 0));
        $('#cbLast').text(d.chargebacks.lastMonth?.count || 0);
        $('#cbLastAmt').text('$' + fmt(d.chargebacks.lastMonth?.amount || 0));
    }
}

function switchTeam(team) {
    currentTeam = team;
    $('#paraguinsTab, #ravensTab').removeClass('active');
    $('#' + team + 'Tab').addClass('active');

    // Filter table rows by team
    const rows = document.querySelectorAll('.closer-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const rowTeam = row.getAttribute('data-team');
        if (!rowTeam || rowTeam === team) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // If no rows visible, show message
    const tbody = document.getElementById('closerTable');
    const emptyRow = tbody.querySelector('.empty-row');
    if (visibleCount === 0) {
        if (!emptyRow) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="6" class="text-center py-3 text-muted">No closers in this team</td></tr>';
        }
    } else {
        if (emptyRow) {
            emptyRow.remove();
        }
    }
}

function renderClosers(closers) {
    const tbody = $('#closerTable');
    tbody.empty();

    if (!closers || closers.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center py-3 text-muted">No closers in this team</td></tr>');
        return;
    }

    closers.forEach(c => {
        tbody.append(`
            <tr>
                <td><i class="bx bx-user-circle me-1 text-gold"></i>${c.closer || 'N/A'}</td>
                <td class="text-center"><span class="badge badge-xs bg-info">${c.today || 0}</span></td>
                <td class="text-center"><span class="badge badge-xs bg-primary">${c.mtd || 0}</span></td>
                <td class="text-center"><span class="badge badge-xs bg-success">${c.approvedMTD || 0}</span></td>
                <td class="text-center"><span class="badge badge-xs bg-danger">${c.declinedMTD || 0}</span></td>
                <td class="text-center"><span class="badge badge-xs bg-warning">${c.uwMTD || c.uw || c.underwriting || 0}</span></td>
            </tr>
        `);
    });
}

function renderTeam(team) {
    const list = $('#teamList');
    list.empty();
    let present = 0, absent = 0;

    team.forEach(t => {
        const status = (t.status || '').toLowerCase();
        const isPresent = ['present','p','on time'].includes(status);
        if (isPresent) present++; else if (status === 'absent') absent++;

        list.append(`<div class="team-item">
            <span class="name"><i class="bx ${isPresent ? 'bx-check-circle' : 'bx-x-circle'} me-2 ${isPresent ? 'text-success' : 'text-danger'}"></i>${t.name}</span>
            <span class="badge ${isPresent ? 'bg-success' : 'bg-danger'}">${isPresent ? 'Present' : 'Absent'}</span>
        </div>`);
    });

    $('#presentBadge').text(present);
    $('#absentBadge').text(absent);
}

function updateCharts(salesData) {
    // Monthly Target Chart with real data
    const ctx = document.getElementById('monthlyTargetChart');
    if (salesChart) salesChart.destroy();

    const currentMTD = serverData.done || 0; // Real data from webhook
    const target = 500;
    const percentage = Math.min((currentMTD / target) * 100, 100);

    salesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Achieved', 'Remaining'],
            datasets: [{
                data: [currentMTD, Math.max(target - currentMTD, 0)],
                backgroundColor: [
                    '#10b981', // Green for achieved
                    '#fbbf24'  // Yellow for remaining
                ],
                borderColor: [
                    '#10b981',
                    '#fbbf24'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ' + value;
                        }
                    }
                }
            }
        },
        plugins: [{
            id: 'centerText',
            beforeDraw: function(chart) {
                const width = chart.width;
                const height = chart.height;
                const ctx = chart.ctx;
                ctx.restore();

                const fontSize = (height / 80).toFixed(2);
                ctx.font = "bold " + fontSize + "em sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#d4af37";

                const text = currentMTD + "";
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2 - 10;

                ctx.fillText(text, textX, textY);

                ctx.font = fontSize * 0.5 + "em sans-serif";
                ctx.fillStyle = "#6b7280";
                const subText = percentage.toFixed(0) + "%";
                const subTextX = Math.round((width - ctx.measureText(subText).width) / 2);
                const subTextY = height / 2 + 15;

                ctx.fillText(subText, subTextX, subTextY);
                ctx.save();
            }
        }]
    });
}

function fmt(n) {
    return new Intl.NumberFormat().format(Math.round(n));
}

$(document).ready(function() {
    updateClocks();
    setInterval(updateClocks, 1000);

    loadData();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/index.blade.php ENDPATH**/ ?>