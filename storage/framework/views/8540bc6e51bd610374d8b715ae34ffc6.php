<?php $__env->startSection('title'); ?>
    Peregrine Closers
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 12px;
    }
    .status-pending { background: #ffc107; color: #000; }
    .status-transferred { background: #17a2b8; color: white; }
    .status-sent { background: #28a745; color: white; }
    .status-sale { background: #007bff; color: white; }
    .status-failed { background: #dc3545; color: white; }
    .status-returned { background: #17a2b8; color: white; }
    .modal-header-custom {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #d4af37;
    }
    .modal-dialog-scrollable .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    .modal-xl {
        max-width: 1200px;
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f8f9fa;
    }
    .table td, .table th {
        color: #212529;
    }
    .bg-warning h5 {
        color: #000;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Peregrine <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Peregrine Closers <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Date Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('peregrine.closers.index')); ?>" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date Range</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="filter" id="filter_today" value="today" <?php echo e($filter === 'today' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_today">Today</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_yesterday" value="yesterday" <?php echo e($filter === 'yesterday' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_yesterday">Yesterday</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_week" value="week" <?php echo e($filter === 'week' ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="btn btn-outline-primary" for="filter_week">This Week</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="filter_custom" value="custom" <?php echo e($filter === 'custom' ? 'checked' : ''); ?>>
                                    <label class="btn btn-outline-primary" for="filter_custom">Custom Range</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="customDateInputs" style="display: <?php echo e($filter === 'custom' ? 'block' : 'none'); ?>;">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="<?php echo e(request('start_date')); ?>">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php echo e(request('end_date')); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" id="customSubmitBtn" style="display: <?php echo e($filter === 'custom' ? 'block' : 'none'); ?>;">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="showAllPending" name="show_all_pending" value="1" <?php echo e(request('show_all_pending') ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
                                    <label class="form-check-label" for="showAllPending">
                                        <strong>Show all pending leads</strong> (ignore date filter for pending)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Today's Activity KPI Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="mdi mdi-chart-line"></i> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter === 'today'): ?>
                    Today's Activity
                <?php elseif($filter === 'yesterday'): ?>
                    Yesterday's Activity
                <?php elseif($filter === 'week'): ?>
                    This Week's Activity
                <?php else: ?>
                    Selected Period Activity
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <small class="text-muted">(<?php echo e(\Carbon\Carbon::parse($startDate)->timezone('America/Denver')->format('M d, Y g:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($endDate)->timezone('America/Denver')->format('M d, Y g:i A')); ?> MT)</small>
            </h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="mdi mdi-account-multiple text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['total_assigned'] ?? 0); ?></h3>
                    <small class="text-muted">Assigned</small>
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
                    <i class="mdi mdi-arrow-u-left-top text-secondary" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['returned'] ?? 0); ?></h3>
                    <small class="text-muted">Returned</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="mdi mdi-close-circle text-danger" style="font-size: 2rem;"></i>
                    <h3 class="mb-0 fw-bold mt-2"><?php echo e($todayStats['declined'] ?? 0); ?></h3>
                    <small class="text-muted">Rejected</small>
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
        <div class="col-md-2">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Leads</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($filteredTotal ?? 0); ?></h2>
                    <small>In selected period</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success">
                <div class="card-body text-center">
                    <h6 class="mb-2">Completed</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($completedLeads->count()); ?></h2>
                    <small>Closed & Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info">
                <div class="card-body text-center">
                    <h6 class="mb-2">Conversion</h6>
                    <h2 class="mb-0 fw-bold">
                        <?php
                            $total = ($filteredTotal ?? 0);
                            $completed = $completedLeads->count();
                            $conversion = $total > 0 ? round(($completed / $total) * 100) : 0;
                        ?>
                        <?php echo e($conversion); ?>%
                    </h2>
                    <small>Success rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body text-center">
                    <h6 class="mb-2">Pending</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($pendingLeads->count()); ?></h2>
                    <small><?php echo e(request('show_all_pending') ? 'All pending' : 'Current pending'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Failed</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($failedLeads->count()); ?></h2>
                    <small>In selected period</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Leads -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bx bx-time-five me-2"></i>
                        Pending Leads 
                        <span class="badge bg-dark"><?php echo e($pendingLeads->count()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Verifier</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#leadModal<?php echo e($lead->id); ?>">
                                        <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                                        <td><?php echo e($lead->phone_number ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->date ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status == 'returned'): ?>
                                                <span class="status-badge bg-info text-white">Returned</span>
                                            <?php elseif($lead->pending_reason): ?>
                                                <span class="status-badge status-pending"><?php echo e($lead->pending_reason); ?></span>
                                            <?php else: ?>
                                                <?php
                                                    $statusMap = [
                                                        'pending' => ['label' => 'Pending', 'class' => 'status-pending'],
                                                        'transferred' => ['label' => 'Pending', 'class' => 'status-pending'],
                                                    ];
                                                    $status = $statusMap[$lead->status] ?? ['label' => 'Pending', 'class' => 'status-pending'];
                                                ?>
                                                <span class="status-badge <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" type="button">
                                                <i class="bx bx-edit"></i> Fill Form
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal for this lead -->
                                    <div class="modal fade" id="leadModal<?php echo e($lead->id); ?>" tabindex="-1" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header modal-header-custom">
                                                    <h5 class="modal-title">Complete Lead Information - <?php echo e($lead->cn_name); ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                                                    <form method="POST" action="<?php echo e(route('peregrine.closers.update', $lead->id)); ?>" id="leadForm<?php echo e($lead->id); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                        <?php echo $__env->make('peregrine.closers.form', ['lead' => $lead], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#failModal<?php echo e($lead->id); ?>">
                                                        <i class="bx bx-x-circle me-1"></i> Mark as Failed
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingModal<?php echo e($lead->id); ?>">
                                                        <i class="bx bx-time-five me-1"></i> Mark as Pending
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bx bx-x me-1"></i> Cancel
                                                    </button>
                                                    <button type="submit" form="leadForm<?php echo e($lead->id); ?>" class="btn btn-success">
                                                        <i class="bx bx-send me-1"></i> Submit to Validator
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pending Reason Modal -->
                                    <div class="modal fade" id="pendingModal<?php echo e($lead->id); ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Select Pending Reason</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="<?php echo e(route('peregrine.closers.mark-pending', $lead->id)); ?>" id="pendingReasonForm<?php echo e($lead->id); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <div class="modal-body">
                                                        <p class="mb-3">Why is this lead being marked as pending?</p>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="futurePotential<?php echo e($lead->id); ?>" value="Pending:Future Potential" required>
                                                            <label class="form-check-label" for="futurePotential<?php echo e($lead->id); ?>">
                                                                <strong>Pending:Future Potential</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="callback<?php echo e($lead->id); ?>" value="Pending:Callback" required>
                                                            <label class="form-check-label" for="callback<?php echo e($lead->id); ?>">
                                                                <strong>Pending:Callback</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingBanking<?php echo e($lead->id); ?>" value="Pending:Pending Banking" required>
                                                            <label class="form-check-label" for="pendingBanking<?php echo e($lead->id); ?>">
                                                                <strong>Pending:Pending Banking</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="pending_reason" id="pendingValidation<?php echo e($lead->id); ?>" value="Pending:Pending Validation" required>
                                                            <label class="form-check-label" for="pendingValidation<?php echo e($lead->id); ?>">
                                                                <strong>Pending:Pending Validation</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning">Confirm Pending</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Failure Reason Modal -->
                                    <div class="modal fade" id="failModal<?php echo e($lead->id); ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title text-white">Select Failure Reason</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="<?php echo e(route('peregrine.closers.mark-failed', $lead->id)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <div class="modal-body">
                                                        <p class="mb-3">Why is this lead being marked as failed?</p>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="poa<?php echo e($lead->id); ?>" value="Failed:POA" required>
                                                            <label class="form-check-label" for="poa<?php echo e($lead->id); ?>">
                                                                <strong>Failed:POA</strong> - Power of Attorney
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqAge<?php echo e($lead->id); ?>" value="Failed:DNQ-Age" required>
                                                            <label class="form-check-label" for="dnqAge<?php echo e($lead->id); ?>">
                                                                <strong>Failed:DNQ-Age</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedSSN<?php echo e($lead->id); ?>" value="Failed:Declined SSN" required>
                                                            <label class="form-check-label" for="declinedSSN<?php echo e($lead->id); ?>">
                                                                <strong>Failed:Declined SSN</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="notInterested<?php echo e($lead->id); ?>" value="Failed:Not Interested" required>
                                                            <label class="form-check-label" for="notInterested<?php echo e($lead->id); ?>">
                                                                <strong>Failed:Not Interested</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="dnc<?php echo e($lead->id); ?>" value="Failed:DNC" required>
                                                            <label class="form-check-label" for="dnc<?php echo e($lead->id); ?>">
                                                                <strong>Failed:DNC</strong> - Do Not Call
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="cannotAfford<?php echo e($lead->id); ?>" value="Failed:Cannot Afford" required>
                                                            <label class="form-check-label" for="cannotAfford<?php echo e($lead->id); ?>">
                                                                <strong>Failed:Cannot Afford</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqHealth<?php echo e($lead->id); ?>" value="Failed:DNQ-Health" required>
                                                            <label class="form-check-label" for="dnqHealth<?php echo e($lead->id); ?>">
                                                                <strong>Failed:DNQ-Health</strong> - Health Conditions
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedBanking<?php echo e($lead->id); ?>" value="Failed:Declined Banking" required>
                                                            <label class="form-check-label" for="declinedBanking<?php echo e($lead->id); ?>">
                                                                <strong>Failed:Declined Banking</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="noPitch<?php echo e($lead->id); ?>" value="Failed:No Pitch (Not Interested)" required>
                                                            <label class="form-check-label" for="noPitch<?php echo e($lead->id); ?>">
                                                                <strong>Failed:No Pitch (Not Interested)</strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="failure_reason" id="noAnswer<?php echo e($lead->id); ?>" value="Failed:No Answer" required>
                                                            <label class="form-check-label" for="noAnswer<?php echo e($lead->id); ?>">
                                                                <strong>Failed:No Answer</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Failed</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <script>
                                    // Copy form data to pending form before submit
                                    document.getElementById('pendingReasonForm<?php echo e($lead->id); ?>').addEventListener('submit', function(e) {
                                        const mainForm = document.getElementById('leadForm<?php echo e($lead->id); ?>');
                                        const pendingForm = this;
                                        
                                        // Track which radio groups we've already added
                                        const addedRadios = new Set();
                                        
                                        // Copy all inputs from main form to pending form
                                        mainForm.querySelectorAll('input, select, textarea').forEach(function(input) {
                                            if (input.name && input.name !== '_token' && input.name !== '_method' && input.name !== 'pending_reason') {
                                                // Handle radio buttons specially - only add the checked one per group
                                                if (input.type === 'radio') {
                                                    if (input.checked && !addedRadios.has(input.name)) {
                                                        addedRadios.add(input.name);
                                                        let hidden = document.createElement('input');
                                                        hidden.type = 'hidden';
                                                        hidden.name = input.name;
                                                        hidden.value = input.value;
                                                        pendingForm.appendChild(hidden);
                                                    }
                                                } else {
                                                    // For non-radio inputs
                                                    let hidden = pendingForm.querySelector('input[name="' + input.name + '"]');
                                                    if (!hidden) {
                                                        hidden = document.createElement('input');
                                                        hidden.type = 'hidden';
                                                        hidden.name = input.name;
                                                        pendingForm.appendChild(hidden);
                                                    }
                                                    
                                                    if (input.type === 'checkbox') {
                                                        hidden.value = input.checked ? '1' : '0';
                                                    } else {
                                                        hidden.value = input.value || '';
                                                    }
                                                }
                                            }
                                        });
                                    });
                                    </script>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No pending leads</p>
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

    <!-- Completed/Sent Leads -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-black">
                    <h5 class="mb-0 text-black">
                        <i class="bx bx-check-circle me-2"></i>
                        Completed Leads 
                        <span class="badge bg-dark"><?php echo e($completedLeads->count()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Verifier</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $completedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                                        <td><?php echo e($lead->phone_number ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                                        <td>
                                            <?php
                                                $statusMap = [
                                                    'closed' => ['label' => 'Closed', 'class' => 'status-sent'],
                                                    'sale' => ['label' => 'Sale', 'class' => 'status-sale'],
                                                ];
                                                $status = $statusMap[$lead->status] ?? ['label' => 'Closed', 'class' => 'status-sent'];
                                            ?>
                                            <span class="status-badge <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span>
                                        </td>
                                        <td><?php echo e($lead->updated_at->format('M d, Y g:i A')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-1"></i>
                                            <p class="mb-0">No completed leads yet</p>
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

    <!-- Failed Leads -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0 text-white">
                        <i class="bx bx-x-circle me-2"></i>
                        Failed Leads 
                        <span class="badge bg-dark"><?php echo e($failedLeads->count()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Verifier</th>
                                    <th>Failure Reason</th>
                                    <th>Failed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $failedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                                        <td><?php echo e($lead->phone_number ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="status-badge status-failed">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status == 'declined'): ?>
                                                    <?php echo e($lead->manager_reason ?? $lead->decline_reason ?? 'Declined by Manager'); ?>

                                                <?php else: ?>
                                                    <?php echo e($lead->decline_reason ?? 'Failed'); ?>

                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </span>
                                        </td>
                                        <td><?php echo e($lead->updated_at->format('M d, Y g:i A')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-smile fs-1"></i>
                                            <p class="mb-0">No failed leads</p>
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

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/peregrine/closers/index.blade.php ENDPATH**/ ?>