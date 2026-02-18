<?php $__env->startSection('title'); ?>
    Revenue Analytics
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    i[class*="mdi"] {
        color: inherit !important;
    }

    .text-success i[class*="mdi"],
    i[class*="mdi"].text-success {
        color: #198754 !important;
    }

    .text-warning i[class*="mdi"],
    i[class*="mdi"].text-warning {
        color: #ffc107 !important;
    }

    .text-danger i[class*="mdi"],
    i[class*="mdi"].text-danger {
        color: #dc3545 !important;
    }

    .text-secondary i[class*="mdi"],
    i[class*="mdi"].text-secondary {
        color: #6c757d !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Revenue Analytics
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Dashboard
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="mdi mdi-chart-line me-2"></i>Revenue Analytics
            </h2>
            <p class="text-muted">Complete revenue breakdown by verification status</p>
        </div>
    </div>

    <!-- Revenue Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <p class="text-muted small mb-1">Good Revenue</p>
                    <h4 class="text-success fw-bold mb-2">$<?php echo e(number_format($good_revenue, 2)); ?></h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted"><?php echo e($good_count); ?> sales</small>
                        <small class="text-success fw-semibold"><?php echo e(number_format($good_revenue_percentage, 1)); ?>%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($good_revenue_percentage); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <p class="text-muted small mb-1">Average Revenue</p>
                    <h4 class="text-warning fw-bold mb-2">$<?php echo e(number_format($average_revenue, 2)); ?></h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted"><?php echo e($average_count); ?> sales</small>
                        <small class="text-warning fw-semibold"><?php echo e(number_format($average_revenue_percentage, 1)); ?>%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo e($average_revenue_percentage); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body">
                    <p class="text-muted small mb-1">Bad Revenue</p>
                    <h4 class="text-danger fw-bold mb-2">$<?php echo e(number_format($bad_revenue, 2)); ?></h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted"><?php echo e($bad_count); ?> sales</small>
                        <small class="text-danger fw-semibold"><?php echo e(number_format($bad_revenue_percentage, 1)); ?>%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo e($bad_revenue_percentage); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <p class="text-muted small mb-1">Unverified Revenue</p>
                    <h4 class="text-info fw-bold mb-2">$<?php echo e(number_format($unverified_revenue, 2)); ?></h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted"><?php echo e($unverified_count); ?> sales</small>
                        <small class="text-info fw-semibold"><?php echo e(number_format($unverified_revenue_percentage, 1)); ?>%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo e($unverified_revenue_percentage); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #d4af37 0%, #b8a000 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50">Total Issued Revenue</p>
                            <h2 class="fw-bold mb-0">$<?php echo e(number_format($total_revenue, 2)); ?></h2>
                            <p class="mb-0 text-white-50"><?php echo e($total_count); ?> Total Sales</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-1">Average Per Sale</p>
                            <h4 class="fw-bold">$<?php echo e(number_format($total_count > 0 ? $total_revenue / $total_count : 0, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ratios & Percentages -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-percent me-2"></i>Sales Ratio by Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Good Sales</span>
                            <span class="text-success fw-bold"><?php echo e(number_format($good_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($good_percentage); ?>%;" title="<?php echo e($good_count); ?> sales"><?php echo e($good_count); ?></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Average Sales</span>
                            <span class="text-warning fw-bold"><?php echo e(number_format($average_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo e($average_percentage); ?>%;" title="<?php echo e($average_count); ?> sales"><?php echo e($average_count); ?></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Bad Sales</span>
                            <span class="text-danger fw-bold"><?php echo e(number_format($bad_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo e($bad_percentage); ?>%;" title="<?php echo e($bad_count); ?> sales"><?php echo e($bad_count); ?></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Unverified</span>
                            <span class="text-secondary fw-bold"><?php echo e(number_format($unverified_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: <?php echo e($unverified_percentage); ?>%;" title="<?php echo e($unverified_count); ?> sales"><?php echo e($unverified_count); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-chart-pie me-2"></i>Revenue Share by Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Good Revenue</span>
                            <span class="text-success fw-bold"><?php echo e(number_format($good_revenue_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($good_revenue_percentage); ?>%;">$<?php echo e(number_format($good_revenue, 0)); ?></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Average Revenue</span>
                            <span class="text-warning fw-bold"><?php echo e(number_format($average_revenue_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo e($average_revenue_percentage); ?>%;">$<?php echo e(number_format($average_revenue, 0)); ?></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Bad Revenue</span>
                            <span class="text-danger fw-bold"><?php echo e(number_format($bad_revenue_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo e($bad_revenue_percentage); ?>%;">$<?php echo e(number_format($bad_revenue, 0)); ?></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Unverified Revenue</span>
                            <span class="text-secondary fw-bold"><?php echo e(number_format($unverified_revenue_percentage, 1)); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: <?php echo e($unverified_revenue_percentage); ?>%;">$<?php echo e(number_format($unverified_revenue, 0)); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Quality Ratio (Good Sales)</p>
                    <h3 class="text-success fw-bold mb-0"><?php echo e(number_format($good_percentage, 1)); ?>%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Bad Sales Ratio</p>
                    <h3 class="text-danger fw-bold mb-0"><?php echo e(number_format($bad_percentage, 1)); ?>%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Good Revenue Ratio</p>
                    <h3 class="text-success fw-bold mb-0"><?php echo e(number_format($good_revenue_percentage, 1)); ?>%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Bad Revenue Ratio</p>
                    <h3 class="text-danger fw-bold mb-0"><?php echo e(number_format($bad_revenue_percentage, 1)); ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifier Forms Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-check-circle-outline me-2"></i>Verifier Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Verifier Name</th>
                                    <th class="text-center">Total Submitted</th>
                                    <th class="text-center">Transferred</th>
                                    <th class="text-center">Transfer Rate</th>
                                    <th class="text-center">Pending Callbacks</th>
                                    <th class="text-center">Declined Calls</th>
                                    <th class="text-center">Marked as Sale</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $verifier_stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $verifier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo e($verifier['name']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?php echo e($verifier['total_submitted']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?php echo e($verifier['transferred']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="progress" style="width: 100px; height: 20px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo e($verifier['transfer_rate']); ?>%;" title="<?php echo e(number_format($verifier['transfer_rate'], 1)); ?>%">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verifier['transfer_rate'] > 20): ?>
                                                            <small class="text-white fw-semibold"><?php echo e(number_format($verifier['transfer_rate'], 1)); ?>%</small>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning"><?php echo e($verifier['pending_callbacks']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger"><?php echo e($verifier['declined_calls']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?php echo e($verifier['marked_as_sale']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No verifiers available</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/revenue-analytics/index.blade.php ENDPATH**/ ?>