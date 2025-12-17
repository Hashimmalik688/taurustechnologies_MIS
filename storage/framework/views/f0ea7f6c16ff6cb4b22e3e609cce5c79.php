

<?php $__env->startSection('title'); ?>
    Validator Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 12px;
    }
    .status-pending { background: #ffc107; color: #000; }
    .status-closed { background: #17a2b8; color: white; }
    .status-sale { background: #28a745; color: white; }
    .status-failed { background: #dc3545; color: white; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Validator <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Validation Dashboard <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Performance Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary">
                <div class="card-body text-center">
                    <h6 class="mb-2">Total Assigned</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($allLeads->count()); ?></h2>
                    <small>All leads received</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success">
                <div class="card-body text-center">
                    <h6 class="mb-2">Sales</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($salesLeads->count()); ?></h2>
                    <small>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info">
                <div class="card-body text-center">
                    <h6 class="mb-2">Returned</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($returnedLeads->count()); ?></h2>
                    <small>Back to closer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger">
                <div class="card-body text-center">
                    <h6 class="mb-2">Declined</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($declinedLeads->count()); ?></h2>
                    <small>Declined</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Validation Leads -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bx bx-check-circle me-2"></i>
                        Pending Validation 
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
                                    <th>Verifier</th>
                                    <th>Closer</th>
                                    <th>Coverage</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pendingLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                                        <td><?php echo e($lead->phone_number ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->closer_name ?? 'N/A'); ?></td>
                                        <td>$<?php echo e(number_format($lead->coverage_amount ?? 0, 0)); ?></td>
                                        <td><?php echo e($lead->updated_at->format('M d, Y H:i')); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($lead->id); ?>">
                                                <i class="bx bx-edit"></i> Edit
                                            </button>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?php echo e($lead->id); ?>" tabindex="-1" data-bs-backdrop="static">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info">
                                                            <h5 class="modal-title">Validate Lead - <?php echo e($lead->cn_name); ?></h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="<?php echo e(route('validator.update', $lead->id)); ?>" id="validatorForm<?php echo e($lead->id); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PUT'); ?>
                                                            <div class="modal-body" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                                                                <?php echo $__env->make('paraguins.closers.form', ['lead' => $lead, 'isValidator' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="bx bx-check me-1"></i> Mark as Sale
                                                                </button>
                                                                <button type="button" class="btn btn-warning" onclick="document.getElementById('forwardForm<?php echo e($lead->id); ?>').submit(); return false;">
                                                                    <i class="bx bx-send me-1"></i> Sent to Home Office
                                                                </button>
                                                                <button type="button" class="btn btn-danger" onclick="document.getElementById('declineForm<?php echo e($lead->id); ?>').submit(); return false;">
                                                                    <i class="bx bx-x me-1"></i> Declined
                                                                </button>
                                                                <button type="button" class="btn btn-secondary" onclick="returnToCloser<?php echo e($lead->id); ?>()">
                                                                    <i class="bx bx-arrow-back me-1"></i> Return to Closer
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                    <i class="bx bx-x me-1"></i> Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                        
                                                        <!-- Hidden forms for other actions -->
                                                        <form method="POST" action="<?php echo e(route('validator.mark-forwarded', $lead->id)); ?>" id="forwardForm<?php echo e($lead->id); ?>" style="display:none;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PUT'); ?>
                                                        </form>
                                                        <form method="POST" action="<?php echo e(route('validator.mark-simple-declined', $lead->id)); ?>" id="declineForm<?php echo e($lead->id); ?>" style="display:none;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PUT'); ?>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Decline Reason Modal -->
                                            <div class="modal fade" id="declineModal<?php echo e($lead->id); ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h5 class="modal-title">Select Decline Reason</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="<?php echo e(route('validator.mark-failed', $lead->id)); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PUT'); ?>
                                                            <div class="modal-body">
                                                                <p class="mb-3">Why is this lead being declined?</p>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="poa<?php echo e($lead->id); ?>" value="Declined:POA" required>
                                                                    <label class="form-check-label" for="poa<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:POA</strong> - Power of Attorney
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="dnqAge<?php echo e($lead->id); ?>" value="Declined:DNQ-Age" required>
                                                                    <label class="form-check-label" for="dnqAge<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:DNQ-Age</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="declinedSSN<?php echo e($lead->id); ?>" value="Declined:Declined SSN" required>
                                                                    <label class="form-check-label" for="declinedSSN<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:Declined SSN</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="notInterested<?php echo e($lead->id); ?>" value="Declined:Not Interested" required>
                                                                    <label class="form-check-label" for="notInterested<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:Not Interested</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="dnc<?php echo e($lead->id); ?>" value="Declined:DNC" required>
                                                                    <label class="form-check-label" for="dnc<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:DNC</strong> - Do Not Call
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="cannotAfford<?php echo e($lead->id); ?>" value="Declined:Cannot Afford" required>
                                                                    <label class="form-check-label" for="cannotAfford<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:Cannot Afford</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="dnqHealth<?php echo e($lead->id); ?>" value="Declined:DNQ-Health" required>
                                                                    <label class="form-check-label" for="dnqHealth<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:DNQ-Health</strong> - Health Conditions
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio" name="decline_reason" id="declinedBanking<?php echo e($lead->id); ?>" value="Declined:Declined Banking" required>
                                                                    <label class="form-check-label" for="declinedBanking<?php echo e($lead->id); ?>">
                                                                        <strong>Declined:Declined Banking</strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Confirm Declined</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <script>
                                            function returnToCloser<?php echo e($lead->id); ?>() {
                                                if(confirm('Return this lead to closer for more information?')) {
                                                    const form = document.getElementById('validatorForm<?php echo e($lead->id); ?>');
                                                    form.action = '<?php echo e(route('validator.return-to-closer', $lead->id)); ?>';
                                                    form.submit();
                                                }
                                            }
                                            </script>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No pending leads for validation</p>
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

    <!-- Home Office Leads (Secure View) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="mb-0">
                        <i class="bx bx-send me-2"></i>
                        Sent to Home Office 
                        <span class="badge bg-dark"><?php echo e($homeOfficeLeads->count()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference ID</th>
                                    <th>Customer Name</th>
                                    <th>Closer Name</th>
                                    <th>Verifier</th>
                                    <th>Coverage Amount</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $homeOfficeLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong>#<?php echo e($lead->id); ?></strong></td>
                                        <td><?php echo e($lead->cn_name); ?></td>
                                        <td><?php echo e($lead->assignedCloser->name ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->verifier->name ?? 'N/A'); ?></td>
                                        <td>$<?php echo e(number_format($lead->coverage_amount ?? 0, 0)); ?></td>
                                        <td><?php echo e($lead->updated_at->format('M d, Y H:i')); ?></td>
                                        <td>
                                            <!-- Mark as Sale Form -->
                                            <form method="POST" action="<?php echo e(route('validator.mark-home-office-sale', $lead->id)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this lead as Sale?')">
                                                    <i class="bx bx-check me-1"></i> Mark as Sale
                                                </button>
                                            </form>
                                            
                                            <!-- Mark as Declined Form -->
                                            <form method="POST" action="<?php echo e(route('validator.mark-simple-declined', $lead->id)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Mark this lead as Declined?')">
                                                    <i class="bx bx-x me-1"></i> Mark as Declined
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox fs-1"></i>
                                            <p class="mb-0">No leads sent to home office</p>
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

    <!-- Completed Validations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-black">
                    <h5 class="mb-0 text-black">
                        <i class="bx bx-check-circle me-2"></i>
                        Completed Validations 
                        <span class="badge bg-dark"><?php echo e($completedLeads->count()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Closer Name</th>
                                    <th>Verifier</th>
                                    <th>Status</th>
                                    <th>Validated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $completedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                                        <td><?php echo e($lead->closer_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($lead->status == 'sale'): ?>
                                                <span class="status-badge status-sale">Sale</span>
                                            <?php elseif($lead->status == 'forwarded'): ?>
                                                <span class="status-badge status-sale">Forwarded</span>
                                            <?php else: ?>
                                                <span class="status-badge status-failed"><?php echo e($lead->failure_reason ?? 'Failed'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($lead->validator ? $lead->validator->name : 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-1"></i>
                                            <p class="mb-0">No completed validations yet</p>
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

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/validator/index.blade.php ENDPATH**/ ?>