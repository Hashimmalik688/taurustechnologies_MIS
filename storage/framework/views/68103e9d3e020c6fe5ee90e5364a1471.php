<?php $__env->startSection('title', 'Approve Project - ' . $project->project_code); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('title'); ?> Project Approval Dashboard <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <!-- Project Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Project Details for Approval</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Code</h6>
                            <p><strong><?php echo e($project->project_code); ?></strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Section</h6>
                            <p><strong><?php echo e($sections[$project->section_id] ?? 'N/A'); ?></strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Project Name</h6>
                            <p><strong><?php echo e($project->project_name); ?></strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Description</h6>
                            <p><?php echo e($project->description); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Created By</h6>
                            <p><?php echo e($project->creator->name); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Created Date</h6>
                            <p><?php echo e($project->created_at->format('M d, Y H:i')); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Quotes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Vendor Quotes Comparison</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th class="text-end">Quote</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['a' => 'Vendor A', 'b' => 'Vendor B', 'c' => 'Vendor C']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php echo e(${'project'}->{'vendor_' . $key . '_name'} ?? $label); ?>

                                        </td>
                                        <td class="text-end">
                                            $<?php echo e(${'project'}->{'vendor_' . $key . '_quote'} ? number_format(${'project'}->{'vendor_' . $key . '_quote'}, 2) : '-'); ?>

                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($key === 'a' && $project->vendor_a_quote == $project->getLowestQuote() && $project->vendor_a_quote) || ($key === 'b' && $project->vendor_b_quote == $project->getLowestQuote() && $project->vendor_b_quote) || ($key === 'c' && $project->vendor_c_quote == $project->getLowestQuote() && $project->vendor_c_quote)): ?>
                                                <span class="badge bg-success">Lowest</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->getLowestQuote()): ?>
                        <div class="alert alert-info py-2">
                            <small>
                                <strong>Lowest Quote:</strong> $<?php echo e(number_format($project->getLowestQuote(), 2)); ?> |
                                <strong>Average Quote:</strong> $<?php echo e(number_format($project->getAverageQuote(), 2)); ?>

                            </small>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Approval History -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->approvals->count() > 0): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Approval History</h6>
                    </div>
                    <div class="card-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $project->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="alert alert-<?php echo e($approval->action === 'APPROVED' ? 'success' : ($approval->action === 'REJECTED' ? 'danger' : 'warning')); ?> py-2 mb-2">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong><?php echo e($approval->action); ?></strong> - <?php echo e($approval->approved_at->format('M d, Y H:i')); ?>

                                        <br><small class="text-muted">By: <?php echo e($approval->approver->name); ?></small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approval->approved_budget): ?>
                                            <small><strong>Budget:</strong> $<?php echo e(number_format($approval->approved_budget, 2)); ?></small>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approval->comments): ?>
                                    <hr class="my-2">
                                    <small><?php echo e($approval->comments); ?></small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Approval Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h6 class="card-title mb-0">CEO Approval Box</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('pabs.projects.processApproval', $project)); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <!-- Approval Status -->
                        <div class="mb-3">
                            <label class="form-label">Decision <span class="text-danger">*</span></label>
                            <div class="btn-group d-block mb-3" role="group">
                                <input type="radio" class="btn-check" name="approval_status" id="approved" value="APPROVED" checked>
                                <label class="btn btn-outline-success" for="approved">Approve</label>

                                <input type="radio" class="btn-check" name="approval_status" id="clarification" value="CLARIFICATION NEEDED">
                                <label class="btn btn-outline-warning" for="clarification">Clarification</label>

                                <input type="radio" class="btn-check" name="approval_status" id="rejected" value="REJECTED">
                                <label class="btn btn-outline-danger" for="rejected">Reject</label>
                            </div>
                        </div>

                        <!-- Approved Budget -->
                        <div class="mb-3" id="approvedBudgetDiv">
                            <label class="form-label">Approved Budget <span class="text-danger">*</span></label>
                            <input type="number" name="approved_budget" id="approvedBudget" class="form-control" placeholder="0.00" step="0.01" min="0" value="<?php echo e(old('approved_budget', $project->getLowestQuote() ? $project->getLowestQuote() : '')); ?>" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->getLowestQuote()): ?>
                                <small class="form-text text-muted">Lowest Quote: $<?php echo e(number_format($project->getLowestQuote(), 2)); ?></small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Target Deadline -->
                        <div class="mb-3" id="deadlineDiv">
                            <label class="form-label">Target Deadline <span class="text-danger">*</span></label>
                            <input type="date" name="target_deadline" id="deadline" class="form-control" value="<?php echo e(old('target_deadline')); ?>" required>
                        </div>

                        <!-- Priority -->
                        <div class="mb-3" id="priorityDiv">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="MEDIUM" selected>Medium</option>
                                <option value="HIGH">High</option>
                                <option value="LOW">Low</option>
                            </select>
                        </div>

                        <!-- Notes/Reason -->
                        <div class="mb-3">
                            <label class="form-label">Notes / Reason</label>
                            <textarea name="approval_notes" class="form-control" rows="4" placeholder="Add approval notes or reason for rejection/clarification..."></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100">Submit Decision</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="approval_status"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isApproved = this.value === 'APPROVED';
            document.getElementById('approvedBudgetDiv').style.display = isApproved ? 'block' : 'none';
            document.getElementById('deadlineDiv').style.display = isApproved ? 'block' : 'none';
            document.getElementById('priorityDiv').style.display = isApproved ? 'block' : 'none';
            
            // Update required attribute
            document.getElementById('approvedBudget').required = isApproved;
            document.getElementById('deadline').required = isApproved;
            document.getElementById('priority').required = isApproved;
        });
    });
    
    // Trigger on load
    document.getElementById('approved').dispatchEvent(new Event('change'));
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pabs/projects/approval.blade.php ENDPATH**/ ?>