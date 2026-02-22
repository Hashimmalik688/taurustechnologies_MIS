<?php $__env->startSection('title'); ?>
    My Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('css/light-theme.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        /* Modern card hover effects */
        .card {
            transition: all 0.3s ease;
        }
        
        .card.shadow-sm:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px);
        }

        /* Smooth counter animation */
        .counter-value {
            display: inline-block;
            transition: color 0.3s ease;
        }

        /* Custom badge styles */
        .badge.bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        /* Table row hover */
        .table-hover tbody tr {
            transition: background-color 0.2s ease;
        }

        /* Avatar gradient */
        .avatar-title.bg-gradient {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
        }

        /* Responsive gap adjustments */
        @media (max-width: 768px) {
            .gap-2 {
                gap: 0.5rem !important;
            }
        }
    </style>
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

    <!-- Quick Action Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo e(route('ravens.calling')); ?>" class="btn btn-primary btn-sm">
                    <i class="bx bx-phone me-1"></i> Start Calling
                </a>
                <a href="<?php echo e(route('attendance.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-time me-1"></i> My Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3">
        <!-- Dialed -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
 <p class="text-muted mb-2 text-uppercase u-fs-075 u-ls-05">Dialed Today</p>
                            <h3 class="mb-0 fw-bold">
                                <span class="counter-value" data-target="<?php echo e($stats['dialed_today'] ?? 0); ?>"><?php echo e($stats['dialed_today'] ?? 0); ?></span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-primary bg-gradient rounded-3">
                                <i class="bx bx-phone fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calls Connected -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
 <p class="text-muted mb-2 text-uppercase u-fs-075 u-ls-05">Connected</p>
                            <h3 class="mb-0 fw-bold">
                                <span class="counter-value" data-target="<?php echo e($stats['calls_connected'] ?? 0); ?>"><?php echo e($stats['calls_connected'] ?? 0); ?></span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-info bg-gradient rounded-3">
                                <i class="bx bx-phone-call fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
 <p class="text-muted mb-2 text-uppercase u-fs-075 u-ls-05">Sales Today</p>
                            <h3 class="mb-0 fw-bold text-success">
                                <span class="counter-value" data-target="<?php echo e($stats['sales_today'] ?? 0); ?>"><?php echo e($stats['sales_today'] ?? 0); ?></span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-success bg-gradient rounded-3">
                                <i class="bx bx-check-circle fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MTD Sale -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
 <p class="text-muted mb-2 text-uppercase u-fs-075 u-ls-05">MTD Sales</p>
                            <h3 class="mb-0 fw-bold" style="color: var(--gold, var(--bs-gold));">
                                <span class="counter-value" data-target="<?php echo e($stats['mtd_sales'] ?? 0); ?>"><?php echo e($stats['mtd_sales'] ?? 0); ?></span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title rounded-3" style="background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-bright) 100%);">
                                <i class="bx bx-trophy fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Sales Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bx bx-dollar-circle text-success me-2"></i>My Sales Records
                        </h5>
                        <span class="badge bg-success-subtle text-success u-fs-0875" style="padding: 0.5rem 0.75rem">
                            Total: <?php echo e($mySales->total() ?? 0); ?>

                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($mySales) && $mySales->count() > 0): ?>
                        <!-- Sales Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="card border border-success-subtle bg-success-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-success mb-1 fw-bold"><?php echo e($mySales->where('status', 'accepted')->count()); ?></h4>
                                        <p class="mb-0 text-success small">Accepted</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card border border-info-subtle bg-info-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-info mb-1 fw-bold"><?php echo e($mySales->where('status', 'underwritten')->count()); ?></h4>
                                        <p class="mb-0 text-info small">Underwritten</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card border border-warning-subtle bg-warning-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-warning mb-1 fw-bold"><?php echo e($mySales->where('status', 'pending')->count()); ?></h4>
                                        <p class="mb-0 text-warning small">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card border border-danger-subtle bg-danger-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-danger mb-1 fw-bold"><?php echo e($mySales->where('status', 'declined')->count()); ?></h4>
                                        <p class="mb-0 text-danger small">Declined</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="50">#</th>
                                        <th>Customer</th>
                                        <th>Sale Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Coverage</th>
                                        <th class="text-end">Premium</th>
                                        <th>Carrier</th>
                                        <th class="text-center" width="80">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mySales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center text-muted"><?php echo e($mySales->firstItem() + $index); ?></td>
                                            <td>
                                                <div class="fw-semibold"><?php echo e($sale->cn_name ?? 'N/A'); ?></div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->phone_number): ?>
                                                    <small class="text-muted"><?php echo e($sale->phone_number); ?></small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bx bx-calendar me-1"></i>
                                                    <?php echo e($sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A'); ?>

                                                </small>
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
                                                <span class="badge bg-<?php echo e($color); ?>-subtle text-<?php echo e($color); ?>"><?php echo e(ucfirst($sale->status)); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->qa_status): ?>
                                                    <br><small class="text-muted">QA: <?php echo e($sale->qa_status); ?></small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->coverage_amount): ?>
                                                    <span class="fw-semibold">$<?php echo e(number_format($sale->coverage_amount, 0)); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->monthly_premium): ?>
                                                    <span class="fw-semibold">$<?php echo e(number_format($sale->monthly_premium, 2)); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td><small><?php echo e($sale->carrier_name ?? 'N/A'); ?></small></td>
                                            <td class="text-center">
                                                <a href="<?php echo e(route('sales.index')); ?>?search=<?php echo e($sale->phone_number); ?>" 
                                                   class="btn btn-sm btn-light" 
                                                   title="View in Sales">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="text-muted small">
                                Showing <?php echo e($mySales->firstItem()); ?> to <?php echo e($mySales->lastItem()); ?> of <?php echo e($mySales->total()); ?>

                            </div>
                            <div><?php echo e($mySales->links()); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="avatar-xl mx-auto mb-3">
                                <div class="avatar-title bg-light rounded-circle">
                                    <i class="bx bx-package display-4 text-muted"></i>
                                </div>
                            </div>
                            <h5 class="text-muted">No sales yet</h5>
                            <p class="text-muted mb-3">Start calling leads to make your first sale!</p>
                            <a href="<?php echo e(route('ravens.calling')); ?>" class="btn btn-primary">
                                <i class="bx bx-phone-call me-1"></i> Start Calling
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/dashboard.blade.php ENDPATH**/ ?>