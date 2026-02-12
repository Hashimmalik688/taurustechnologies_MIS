<?php $__env->startSection('title'); ?>
    My Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('css/light-theme.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Ravens
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            My Dashboard
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <!-- Dialed -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Dialed</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['dialed_today'] ?? 0); ?>"><?php echo e($stats['dialed_today'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-primary align-self-center">
                                <span class="avatar-title bg-primary rounded-circle fs-3">
                                    <i class="bx bx-phone text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calls Connected -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Calls Connected</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['calls_connected'] ?? 0); ?>"><?php echo e($stats['calls_connected'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-info align-self-center">
                                <span class="avatar-title bg-info rounded-circle fs-3">
                                    <i class="bx bx-phone-call text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Sales Today</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['sales_today'] ?? 0); ?>"><?php echo e($stats['sales_today'] ?? 0); ?></span>
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

        <!-- MTD Sale -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">MTD Sale</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?php echo e($stats['mtd_sales'] ?? 0); ?>"><?php echo e($stats['mtd_sales'] ?? 0); ?></span>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($today): ?>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?php echo e($today->status === 'present' ? 'success' : ($today->status === 'late' ? 'warning' : 'danger')); ?>"><?php echo e(ucfirst($today->status)); ?></span></p>
                        <p class="mb-1"><strong>Login:</strong> <?php echo e($today->formatted_login_time ?? ($today->login_time ? \Carbon\Carbon::parse($today->login_time)->format('g:i A') : 'N/A')); ?></p>
                        <p class="mb-1"><strong>Logout:</strong> <?php echo e($today->formatted_logout_time ?? ($today->logout_time ? \Carbon\Carbon::parse($today->logout_time)->format('g:i A') : 'N/A')); ?></p>
                    <?php else: ?>
                        <p class="text-muted">No attendance record for today.</p>
                        <a href="#" id="RavensMarkBtn" class="btn btn-gold btn-sm">Mark Attendance</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3">Ready to Make Calls?</h5>
                    <a href="<?php echo e(route('ravens.calling')); ?>" class="btn btn-primary btn-lg">
                        <i class="bx bx-phone me-2"></i> Start Calling Leads
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- My Sales Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-dollar-circle me-2"></i>My Sales Records
                    </h4>
                    <div>
                        <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            Total: <?php echo e($mySales->total() ?? 0); ?>

                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($mySales) && $mySales->count() > 0): ?>
                        <!-- Sales Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h4 class="text-success"><?php echo e($mySales->where('status', 'accepted')->count()); ?></h4>
                                        <p class="mb-0 text-muted">Accepted</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h4 class="text-info"><?php echo e($mySales->where('status', 'underwritten')->count()); ?></h4>
                                        <p class="mb-0 text-muted">Underwritten</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h4 class="text-warning"><?php echo e($mySales->where('status', 'pending')->count()); ?></h4>
                                        <p class="mb-0 text-muted">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-danger">
                                    <div class="card-body text-center">
                                        <h4 class="text-danger"><?php echo e($mySales->where('status', 'declined')->count()); ?></h4>
                                        <p class="mb-0 text-muted">Declined</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">#</th>
                                        <th>Customer Name</th>
                                        <th>Sale Date</th>
                                        <th>Status</th>
                                        <th>Coverage</th>
                                        <th>Premium</th>
                                        <th>Carrier</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mySales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($mySales->firstItem() + $index); ?></td>
                                            <td>
                                                <strong><?php echo e($sale->cn_name ?? 'N/A'); ?></strong>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->phone_number): ?>
                                                    <br><small class="text-muted"><?php echo e($sale->phone_number); ?></small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="bx bx-calendar me-1"></i>
                                                <?php echo e($sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A'); ?>

                                            </td>
                                            <td>
                                                <?php
                                                    $statusColors = [
                                                        'accepted' => 'success',
                                                        'underwritten' => 'info',
                                                        'pending' => 'warning',
                                                        'declined' => 'danger',
                                                        'chargeback' => 'dark',
                                                    ];
                                                    $color = $statusColors[$sale->status] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo e($color); ?>"><?php echo e(ucfirst($sale->status)); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->qa_status): ?>
                                                    <br><small class="text-muted">QA: <?php echo e($sale->qa_status); ?></small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->coverage_amount): ?>
                                                    <strong>$<?php echo e(number_format($sale->coverage_amount, 0)); ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->monthly_premium): ?>
                                                    $<?php echo e(number_format($sale->monthly_premium, 2)); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td><?php echo e($sale->carrier_name ?? 'N/A'); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo e(route('sales.index')); ?>?search=<?php echo e($sale->phone_number); ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View in Sales">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing <?php echo e($mySales->firstItem()); ?> to <?php echo e($mySales->lastItem()); ?> of <?php echo e($mySales->total()); ?>

                            </div>
                            <div><?php echo e($mySales->links()); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bx bx-package fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No sales yet. Start calling to make your first sale!</p>
                            <a href="<?php echo e(route('ravens.calling')); ?>" class="btn btn-primary mt-2">
                                <i class="bx bx-phone-call me-1"></i> Go to Calling System
                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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
                const empBtn = document.getElementById('RavensMarkBtn');
                if (!empBtn) return;
                empBtn.addEventListener('click', function(e){
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

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/dashboard.blade.php ENDPATH**/ ?>