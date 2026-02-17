<?php $__env->startSection('title'); ?>
    My Followup & Bank Verification
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Followup & Bank Verification
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            My Followup & Bank Verification
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="bx bx-user-check me-2"></i>My Followup & Bank Verification
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-table me-2"></i>Leads Assigned to Me
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('followup.my-followups')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, carrier..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="carrier" class="form-select">
                                    <option value="">All Carriers</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($carrier); ?>" <?php echo e(request('carrier') == $carrier ? 'selected' : ''); ?>><?php echo e($carrier); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="followup_status" class="form-select">
                                    <option value="">Followup Status</option>
                                    <option value="Yes" <?php echo e(request('followup_status') == 'Yes' ? 'selected' : ''); ?>>✅ Yes</option>
                                    <option value="No" <?php echo e(request('followup_status') == 'No' ? 'selected' : ''); ?>>❌ No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:150px;">Client Name</th>
                                    <th style="min-width:130px;">Phone</th>
                                    <th style="min-width:130px;">Closer</th>
                                    <th style="min-width:110px;">Sale Date</th>
                                    <th style="min-width:120px;">Carrier</th>
                                    <th style="min-width:120px;">Policy Type</th>
                                    <th style="min-width:150px;">Policy Number</th>
                                    <th style="min-width:120px;">Coverage</th>
                                    <th style="min-width:110px;">Premium</th>
                                    <th style="min-width:140px;">Followup Status</th>
                                    <th style="min-width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                                        <td><?php echo e($lead->phone_number); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?>
                                                <span class="badge bg-info"><?php echo e($lead->closer_name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></td>
                                        <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->policy_type ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issued_policy_number): ?>
                                                <span class="badge bg-primary"><?php echo e($lead->issued_policy_number); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Not Set</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-gold fw-semibold">$<?php echo e(number_format($lead->coverage_amount ?? 0, 2)); ?></td>
                                        <td class="text-gold fw-semibold">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                                        <td>
                                            <select class="form-select form-select-sm followup-status-dropdown" data-lead-id="<?php echo e($lead->id); ?>" data-current-status="<?php echo e($lead->followup_status); ?>">
                                                <option value="No" <?php echo e($lead->followup_status === 'No' ? 'selected' : ''); ?>>❌ No</option>
                                                <option value="Yes" <?php echo e($lead->followup_status === 'Yes' ? 'selected' : ''); ?>>✅ Yes</option>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('issuance.show', $lead->id)); ?>" class="btn btn-sm btn-info" title="View All Details">
                                                <i class="bx bx-show me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No followups assigned to you</p>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($leads->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Verification Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-bank me-2"></i>Bank Verification Assignments
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('followup.my-followups')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="bv_search" class="form-control" placeholder="Search by name, phone, carrier..." value="<?php echo e(request('bv_search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="bv_carrier" class="form-select">
                                    <option value="">All Carriers</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($carrier); ?>" <?php echo e(request('bv_carrier') == $carrier ? 'selected' : ''); ?>><?php echo e($carrier); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="bv_status" class="form-select">
                                    <option value="">Verification Status</option>
                                    <option value="Good" <?php echo e(request('bv_status') == 'Good' ? 'selected' : ''); ?>>Good</option>
                                    <option value="Average" <?php echo e(request('bv_status') == 'Average' ? 'selected' : ''); ?>>Average</option>
                                    <option value="Bad" <?php echo e(request('bv_status') == 'Bad' ? 'selected' : ''); ?>>Bad</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:150px;">Client Name</th>
                                    <th style="min-width:130px;">Phone</th>
                                    <th style="min-width:130px;">Closer</th>
                                    <th style="min-width:110px;">Sale Date</th>
                                    <th style="min-width:120px;">Carrier</th>
                                    <th style="min-width:120px;">Policy Type</th>
                                    <th style="min-width:150px;">Policy Number</th>
                                    <th style="min-width:120px;">Coverage</th>
                                    <th style="min-width:110px;">Premium</th>
                                    <th style="min-width:150px;">Assigned B.V</th>
                                    <th style="min-width:200px;">Comment</th>
                                    <th style="min-width:140px;">B.V Status</th>
                                    <th style="min-width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bankVerificationLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                                        <td><?php echo e($lead->phone_number); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?>
                                                <span class="badge bg-info"><?php echo e($lead->closer_name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></td>
                                        <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($lead->policy_type ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issued_policy_number): ?>
                                                <span class="badge bg-primary"><?php echo e($lead->issued_policy_number); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Not Set</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-gold fw-semibold">$<?php echo e(number_format($lead->coverage_amount ?? 0, 2)); ?></td>
                                        <td class="text-gold fw-semibold">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bankVerifier): ?>
                                                <span class="badge bg-success"><?php echo e($lead->bankVerifier->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Unassigned</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm bv-comment-input" data-lead-id="<?php echo e($lead->id); ?>" rows="2" placeholder="Enter comment..."><?php echo e($lead->bank_verification_comment); ?></textarea>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bank_verification_status): ?>
                                                <?php
                                                    $badgeClass = match($lead->bank_verification_status) {
                                                        'Good' => 'bg-success',
                                                        'Average' => 'bg-warning',
                                                        'Bad' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>
                                                <span class="badge <?php echo e($badgeClass); ?> fs-6"><?php echo e($lead->bank_verification_status); ?></span>
                                            <?php else: ?>
                                                <select class="form-select form-select-sm bv-status-dropdown" data-lead-id="<?php echo e($lead->id); ?>">
                                                    <option value="">Select Status</option>
                                                    <option value="Good">Good</option>
                                                    <option value="Average">Average</option>
                                                    <option value="Bad">Bad</option>
                                                </select>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success update-bv-btn" data-lead-id="<?php echo e($lead->id); ?>" title="Update B.V">
                                                <i class="bx bx-save me-1"></i>Update
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="13" class="text-center py-5 text-muted">
                                            <i class="bx bx-inbox fs-1 mb-3 d-block"></i>
                                            <p class="mb-0">No bank verification assignments found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($bankVerificationLeads->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
$(document).ready(function() {
    // Handle followup status dropdown changes
    $('.followup-status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const followupStatus = $(this).val();
        const dropdown = $(this);
        
        if (confirm('Are you sure you want to update the followup status?')) {
            dropdown.prop('disabled', true);
            
            $.ajax({
                url: `/followup/${leadId}/update-status`,
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    followup_status: followupStatus
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check me-2"></i>
                                <strong>Success!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Update the current status data attribute
                        dropdown.data('current-status', followupStatus);
                        dropdown.prop('disabled', false);
                        
                        // Auto-remove alert after 3 seconds
                        setTimeout(() => {
                            $('.alert').fadeOut();
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to update followup status');
                    dropdown.prop('disabled', false);
                    // Revert to previous selection
                    dropdown.val(dropdown.data('current-status'));
                }
            });
        } else {
            // Revert to previous selection
            dropdown.val(dropdown.data('current-status'));
        }
    });

    // Handle bank verification update button
    $('.update-bv-btn').click(function() {
        const leadId = $(this).data('lead-id');
        const button = $(this);
        const row = button.closest('tr');
        const comment = row.find('.bv-comment-input').val();
        const statusDropdown = row.find('.bv-status-dropdown');
        const status = statusDropdown.length ? statusDropdown.val() : null;
        
        if (!status && statusDropdown.length) {
            alert('Please select a status (Good/Average/Bad) before updating.');
            return;
        }
        
        if (confirm('Are you sure you want to update bank verification details? Status can only be set once by you.')) {
            button.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Saving...');
            
            $.ajax({
                url: `/followup/${leadId}/update-bank-verification`,
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    bank_verification_comment: comment,
                    bank_verification_status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message and reload to show badge instead of dropdown
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check me-2"></i>
                                <strong>Success!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(alertHtml);
                        
                        // Reload page after 1 second to show updated status badge
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to update bank verification');
                    button.prop('disabled', false).html('<i class="bx bx-save me-1"></i>Update');
                }
            });
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/followup/my-followups.blade.php ENDPATH**/ ?>