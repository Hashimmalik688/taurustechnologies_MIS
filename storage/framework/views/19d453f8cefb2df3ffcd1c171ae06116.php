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

    <!-- Date Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('verifier.dashboard')); ?>" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-7">
                                <label class="form-label fw-bold">Date Range</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="filter" id="filter_all" value="all" <?php echo e($filter === 'all' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_all">All Time</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_today" value="today" <?php echo e($filter === 'today' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_today">Today</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_yesterday" value="yesterday" <?php echo e($filter === 'yesterday' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_yesterday">Yesterday</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_week" value="week" <?php echo e($filter === 'week' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_week">This Week</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_custom" value="custom" <?php echo e($filter === 'custom' ? 'checked' : ''); ?>>
                                    <label class="btn btn-outline-primary" for="filter_custom">Custom</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="customDateInputs" style="display: <?php echo e($filter === 'custom' ? 'block' : 'none'); ?>;">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Start</label>
                                        <input type="date" class="form-control" name="start_date" value="<?php echo e(request('start_date')); ?>">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">End</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php echo e(request('end_date')); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" id="customSubmitBtn" style="display: <?php echo e($filter === 'custom' ? 'block' : 'none'); ?>;">
                                <button type="submit" class="btn btn-primary w-100">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Activity KPI Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="mdi mdi-chart-line"></i> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter === 'all'): ?>
                    All Time Activity
                <?php elseif($filter === 'today'): ?>
                    Today's Activity
                <?php elseif($filter === 'yesterday'): ?>
                    Yesterday's Activity
                <?php elseif($filter === 'week'): ?>
                    This Week's Activity
                <?php else: ?>
                    Selected Period Activity
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter !== 'all'): ?>
                    <small class="text-muted">(<?php echo e(\Carbon\Carbon::parse($startDate)->timezone('America/Denver')->format('M d, Y g:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($endDate)->timezone('America/Denver')->format('M d, Y g:i A')); ?> MT)</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="mdi mdi-account-check text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['total_verified'] ?? 0); ?></h3>
                    <small class="text-muted">Verified</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="mdi mdi-transfer text-info" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['transferred'] ?? 0); ?></h3>
                    <small class="text-muted">Transferred</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="mdi mdi-check-circle text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['closed'] ?? 0); ?></h3>
                    <small class="text-muted">Closed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="mdi mdi-currency-usd text-success" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['sales'] ?? 0); ?></h3>
                    <small class="text-muted">Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="mdi mdi-clock-alert text-secondary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['pending'] ?? 0); ?></h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="mdi mdi-close-circle text-danger" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['declined'] ?? 0); ?></h3>
                    <small class="text-muted">Declined</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Performance Stats Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3"><i class="mdi mdi-chart-box"></i> Overall Statistics</h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Forms</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($filteredTotal ?? 0); ?></h2>
                    <small>In selected period</small>
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
                    <small><?php echo e(request('show_all_leads') ? 'All pending' : 'Current pending'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Declined Calls</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($leads->whereIn('status', ['declined', 'rejected'])->count()); ?></h2>
                    <small>In selected period</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4 class="card-title mb-0 text-white"><i class="bx bx-list-ul me-2"></i>My Transferred Forms</h4>
                    <a href="<?php echo e(route('verifier.create.team', 'peregrine')); ?>" class="btn btn-light btn-sm">
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                            <a href="<?php echo e(route('verifier.create.team', 'peregrine')); ?>" class="btn btn-primary btn-sm mt-2">
                                                <i class="bx bx-plus me-1"></i> Submit Your First Form
                                            </a>
                                        </td>
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

<?php $__env->startSection('script'); ?>
<script>
    // Show/hide custom date inputs based on filter selection
    document.querySelectorAll('input[name="filter"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const customInputs = document.getElementById('customDateInputs');
            const customSubmitBtn = document.getElementById('customSubmitBtn');
            
            if (this.value === 'custom') {
                customInputs.style.display = 'block';
                customSubmitBtn.style.display = 'block';
            } else {
                customInputs.style.display = 'none';
                customSubmitBtn.style.display = 'none';
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/verifier/dashboard.blade.php ENDPATH**/ ?>