

<?php $__env->startSection('title'); ?>
    My Verifications
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 12px;
    }
    .status-transferred { background: #17a2b8; color: white; }
    .status-xfer { background: #28a745; color: white; }
    .status-failed { background: #dc3545; color: white; }
    .status-pending { background: #ffc107; color: #000; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Verifier <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> My Dashboard <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Performance Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Forms</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($leads->count()); ?></h2>
                    <small>All submissions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success">
                <div class="card-body text-center">
                    <h6 class="mb-2">Success Rate</h6>
                    <h2 class="mb-0 fw-bold">
                        <?php
                            $total = $leads->count();
                            $successful = $leads->whereIn('status', ['closed', 'sale'])->count();
                            $rate = $total > 0 ? round(($successful / $total) * 100) : 0;
                        ?>
                        <?php echo e($rate); ?>%
                    </h2>
                    <small><?php echo e($successful); ?> Sales / <?php echo e($total); ?> total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body text-center">
                    <h6 class="mb-2">Pending Callbacks</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($leads->where('status', 'pending')->count()); ?></h2>
                    <small>Awaiting follow-up</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Declined Calls</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($leads->whereIn('status', ['declined', 'rejected'])->count()); ?></h2>
                    <small>Not interested</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4 class="card-title mb-0 text-white"><i class="bx bx-list-ul me-2"></i>My Transferred Forms</h4>
                    <a href="<?php echo e(route('verifier.create.team', 'paraguins')); ?>" class="btn btn-light btn-sm">
                        <i class="bx bx-plus me-1"></i> New Form
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Closer Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($lead->date); ?></td>
                                        <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                                        <td><?php echo e($lead->closer_name); ?></td>
                                        <td>
                                            <?php
                                                $statusMap = [
                                                    'transferred' => ['label' => 'Transferred', 'class' => 'status-transferred'],
                                                    'closed' => ['label' => 'Closed', 'class' => 'status-xfer'],
                                                    'sale' => ['label' => 'Sale', 'class' => 'status-xfer'],
                                                    'declined' => ['label' => $lead->decline_reason ?? 'Declined', 'class' => 'status-failed'],
                                                    'rejected' => ['label' => $lead->failure_reason ?? 'Failed', 'class' => 'status-failed'],
                                                    'pending' => ['label' => $lead->pending_reason ?? 'Pending', 'class' => 'status-pending'],
                                                    'returned' => ['label' => 'Returned', 'class' => 'bg-info text-white'],
                                                ];
                                                $status = $statusMap[$lead->status] ?? ['label' => ucfirst($lead->status), 'class' => 'bg-secondary'];
                                            ?>
                                            <span class="status-badge <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span>
                                        </td>
                                        <td><?php echo e($lead->created_at->format('M d, Y h:i A')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No forms submitted yet</p>
                                            <a href="<?php echo e(route('verifier.create.team', 'paraguins')); ?>" class="btn btn-primary btn-sm mt-2">
                                                <i class="bx bx-plus me-1"></i> Submit Your First Form
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/verifier/dashboard.blade.php ENDPATH**/ ?>