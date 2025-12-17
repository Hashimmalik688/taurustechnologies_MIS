<?php $__env->startSection('title'); ?>
    Chargebacks Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .chargeback-card {
        transition: all 0.3s ease;
    }
    .chargeback-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .period-filter .btn {
        min-width: 120px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Chargebacks
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Management
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card chargeback-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                    <i class="bx bx-error"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Chargebacks</p>
                            <h4 class="mb-0"><?php echo e($total_count); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card chargeback-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                    <i class="bx bx-dollar-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Amount</p>
                            <h4 class="mb-0">$<?php echo e(number_format($total_amount, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chargebacks Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Chargebacks List</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('chargebacks.index')); ?>" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Search by name, phone, carrier, closer..." 
                                       value="<?php echo e($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="month" class="form-select form-select-sm">
                                    <option value="">All Months</option>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>>
                                            <?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?>

                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="year" class="form-select form-select-sm">
                                    <option value="">All Years</option>
                                    <?php for($y = now()->year; $y >= now()->year - 5; $y--): ?>
                                        <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bx bx-search-alt me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="<?php echo e(route('chargebacks.index')); ?>" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bx bx-reset me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sale Date</th>
                                    <th>Customer</th>
                                    <th>Closer</th>
                                    <th>Agent Assigned</th>
                                    <th>Carrier</th>
                                    <th>Chargeback Amount</th>
                                    <th>Comments</th>
                                    <th>Manager Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $chargebacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($lead->sale_date ? $lead->sale_date->format('M d, Y') : 'N/A'); ?></td>
                                        <td>
                                            <strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($lead->phone_number ?? ''); ?></small>
                                        </td>
                                        <td><?php echo e($lead->closer_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->managedBy->name ?? 'Unassigned'); ?></td>
                                        <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-danger fs-6">
                                                $<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e(Str::limit($lead->comments ?? 'No reason provided', 50)); ?></td>
                                        <td>
                                            <div class="p-2 bg-light rounded text-muted small"><?php echo e($lead->manager_reason ?? 'No comments'); ?></div>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('leads.show', $lead->id)); ?>"
                                               class="btn btn-sm btn-info"
                                               title="View Lead Details">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No chargebacks found for the selected period</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if($chargebacks->count() > 0): ?>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total:</td>
                                        <td>
                                            <span class="badge bg-danger fs-6">
                                                $<?php echo e(number_format($total_amount, 2)); ?>

                                            </span>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        <?php echo e($chargebacks->appends(['search' => $search, 'month' => $month, 'year' => $year])->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/admin/chargebacks/index.blade.php ENDPATH**/ ?>