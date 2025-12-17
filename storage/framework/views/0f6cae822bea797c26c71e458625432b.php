

<?php $__env->startSection('title'); ?>
    Retention Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('public/css/light-theme.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Retention
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            My Dashboard
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <!-- Total Chargebacks -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Chargebacks</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['total_chargebacks'] ?? 0); ?>"><?php echo e($stats['total_chargebacks'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle align-self-center" style="background: rgba(244, 106, 106, 0.18);">
                                <span class="avatar-title rounded-circle fs-3" style="background: #f46a6a; color: white;">
                                    <i class="bx bx-error"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yet to Retain -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Yet to Retain</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['yet_to_retain'] ?? 0); ?>"><?php echo e($stats['yet_to_retain'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-warning align-self-center">
                                <span class="avatar-title bg-warning rounded-circle fs-3">
                                    <i class="bx bx-time text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retained Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Retained Today</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['retained_today'] ?? 0); ?>"><?php echo e($stats['retained_today'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-success align-self-center">
                                <span class="avatar-title bg-success rounded-circle fs-3">
                                    <i class="bx bx-check-circle text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retained MTD -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Retained MTD</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['retained_mtd'] ?? 0); ?>"><?php echo e($stats['retained_mtd'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle align-self-center" style="background: rgba(212, 175, 55, 0.18);">
                                <span class="avatar-title rounded-circle fs-3" style="background: var(--gold); color: white;">
                                    <i class="bx bx-trophy"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-2">
        <div class="col-lg-6">
            <div class="card bordered">
                <div class="card-body">
                    <h5 class="card-title">Attendance Summary (This Month)</h5>
                    <?php $a = $stats['attendance_summary'] ?? null; ?>
                    <div class="row text-center mt-3">
                        <div class="col-3">
                            <div class="h4"><?php echo e($a['total_records'] ?? 0); ?></div>
                            <div class="text-muted">Records</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-success"><?php echo e($a['present_days'] ?? 0); ?></div>
                            <div class="text-muted">Present</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-danger"><?php echo e($a['absent_days'] ?? 0); ?></div>
                            <div class="text-muted">Absent</div>
                        </div>
                        <div class="col-3">
                            <div class="h4"><?php echo e($a['late_days'] ?? 0); ?></div>
                            <div class="text-muted">Late</div>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <div class="h5"><?php echo e($a['total_working_hours'] ?? 0); ?> hrs</div>
                            <div class="text-muted">Total Hours</div>
                        </div>
                        <div class="col-6">
                            <div class="h5"><?php echo e($a['average_working_hours'] ?? 0); ?> hrs</div>
                            <div class="text-muted">Avg/Day</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bordered">
                <div class="card-body">
                    <h5 class="card-title">Today's Attendance</h5>
                    <?php $today = $stats['today_status'] ?? null; ?>
                    <?php if($today): ?>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?php echo e($today->status === 'present' ? 'success' : ($today->status === 'late' ? 'warning' : 'danger')); ?>"><?php echo e(ucfirst($today->status)); ?></span></p>
                        <p class="mb-1"><strong>Login:</strong> <?php echo e($today->formatted_login_time ?? $today->login_time?->format('H:i') ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Logout:</strong> <?php echo e($today->formatted_logout_time ?? $today->logout_time?->format('H:i') ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="text-muted">No attendance record for today.</p>
                        <a href="#" id="retentionMarkBtn" class="btn btn-gold btn-sm">Mark Attendance</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3">Ready to Work on Retentions?</h5>
                    <a href="<?php echo e(route('retention.index')); ?>" class="btn btn-primary btn-lg">
                        <i class="bx bx-refresh me-2"></i> Go to Retention Management
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rewrite Alert -->
    <?php if(($stats['rewrite_count'] ?? 0) > 0): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bx bx-error-circle fs-3 me-3"></i>
                <div>
                    <strong>Attention!</strong> There are <strong><?php echo e($stats['rewrite_count']); ?></strong> sales that need to be rewritten (30+ days old chargebacks).
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // Counter animation
        document.querySelectorAll('.counter-value').forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const updateCounter = () => {
                const current = +counter.innerText;
                const increment = target / 50;

                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const retentionBtn = document.getElementById('retentionMarkBtn');
            if (!retentionBtn) return;
            retentionBtn.addEventListener('click', function(e){
                e.preventDefault();
                if (!confirm('Mark your attendance now?')) return;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('<?php echo e(route('attendance.mark-manual.post')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ force_office: 0 })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        alert(data.message || 'Attendance marked');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        alert(data.message || 'Could not mark attendance');
                    }
                }).catch(err => { console.error(err); alert('Network error'); });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/retention/dashboard.blade.php ENDPATH**/ ?>