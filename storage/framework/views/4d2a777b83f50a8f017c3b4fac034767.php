<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title'); ?>
    Bank Verification Details
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <a href="<?php echo e(route('bank-verification.index')); ?>">Bank Verification</a>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Details
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-gold fw-bold mb-0">
                    <i class="mdi mdi-bank me-2"></i>Bank Verification Details
                </h2>
                <a href="<?php echo e(route('bank-verification.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-muted mb-2">Current Status</h5>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bank_verification_status): ?>
                                <?php
                                    $badgeClass = match($lead->bank_verification_status) {
                                        'Pending' => 'bg-warning',
                                        'Issued' => 'bg-success',
                                        'UnIssued' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <h3><span class="badge <?php echo e($badgeClass); ?>"><?php echo e($lead->bank_verification_status); ?></span></h3>
                            <?php else: ?>
                                <h3><span class="badge bg-secondary">Unverified</span></h3>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bank_verification_date): ?>
                                <p class="text-muted small mb-0">Updated: <?php echo e(\Carbon\Carbon::parse($lead->bank_verification_date)->format('M d, Y h:i A')); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="bx bx-edit me-1"></i> Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-account-circle me-2"></i>Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-semibold" style="width: 40%;">Name:</td>
                            <td><?php echo e($lead->cn_name); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Phone:</td>
                            <td><?php echo e($lead->phone_number); ?></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->secondary_phone_number): ?>
                        <tr>
                            <td class="fw-semibold">Secondary Phone:</td>
                            <td><?php echo e($lead->secondary_phone_number); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <tr>
                            <td class="fw-semibold">Date of Birth:</td>
                            <td><?php echo e($lead->date_of_birth ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Address:</td>
                            <td><?php echo e($lead->address ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">State:</td>
                            <td><?php echo e($lead->state ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Zip Code:</td>
                            <td><?php echo e($lead->zip_code ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Policy Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-file-document-outline me-2"></i>Policy Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-semibold" style="width: 40%;">Carrier:</td>
                            <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Policy Number:</td>
                            <td><?php echo e($lead->policy_number ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Policy Type:</td>
                            <td><?php echo e($lead->policy_type ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Coverage Amount:</td>
                            <td class="text-gold fw-bold">$<?php echo e(number_format($lead->coverage_amount ?? 0, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Monthly Premium:</td>
                            <td class="text-gold fw-bold">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Sale Date:</td>
                            <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Issued Date:</td>
                            <td><?php echo e($lead->issuance_date ? \Carbon\Carbon::parse($lead->issuance_date)->format('M d, Y') : 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bank Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-bank me-2"></i>Bank Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-semibold" style="width: 40%;">Bank Name:</td>
                            <td><?php echo e($lead->bank_name ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Account Type:</td>
                            <td><?php echo e($lead->account_type ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Account Title:</td>
                            <td><?php echo e($lead->account_title ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Routing Number:</td>
                            <td><?php echo e($lead->routing_number ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Account Number:</td>
                            <td><?php echo e($lead->account_number ? '****' . substr($lead->account_number, -4) : 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Verification Details -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-clipboard-check me-2"></i>Verification Details
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-semibold" style="width: 40%;">Manager Status:</td>
                            <td><span class="badge bg-success"><?php echo e($lead->manager_status ?? 'N/A'); ?></span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Issuance Status:</td>
                            <td><span class="badge bg-info"><?php echo e($lead->issuance_status ?? 'N/A'); ?></span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Closer:</td>
                            <td><?php echo e($lead->closer_name ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Managed By:</td>
                            <td><?php echo e($lead->managed_by ?? 'N/A'); ?></td>
                        </tr>
                    </table>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issuance_reason): ?>
                        <div class="mt-3">
                            <p class="fw-semibold mb-1">Issuance Reason:</p>
                            <div class="alert alert-info mb-0">
                                <?php echo e($lead->issuance_reason); ?>

                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bank_verification_notes): ?>
                        <div class="mt-3">
                            <p class="fw-semibold mb-1">Verification Notes:</p>
                            <div class="alert alert-warning mb-0">
                                <?php echo e($lead->bank_verification_notes); ?>

                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Beneficiary Information -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->beneficiary): ?>
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-account-heart me-2"></i>Beneficiary Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-semibold" style="width: 20%;">Beneficiary Name:</td>
                            <td><?php echo e($lead->beneficiary); ?></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->beneficiary_dob): ?>
                        <tr>
                            <td class="fw-semibold">Beneficiary DOB:</td>
                            <td><?php echo e($lead->beneficiary_dob); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
 <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%)">
                    <h5 class="modal-title fw-semibold">
                        <i class="mdi mdi-bank me-2"></i>Update Status - <?php echo e($lead->cn_name); ?>

                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('bank-verification.update', $lead->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Status</label>
                            <div class="d-grid gap-2">
                                <button type="submit" name="bank_verification_status" value="Pending" class="btn btn-warning btn-lg">
                                    <i class="mdi mdi-clock-outline me-2"></i>Pending
                                </button>
                                <button type="submit" name="bank_verification_status" value="Issued" class="btn btn-success btn-lg">
                                    <i class="mdi mdi-check-circle me-2"></i>Issued
                                </button>
                                <button type="submit" name="bank_verification_status" value="UnIssued" class="btn btn-danger btn-lg">
                                    <i class="mdi mdi-close-circle me-2"></i>UnIssued
                                </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole(Roles::SUPER_ADMIN) && $lead->bank_verification_status !== null): ?>
                                    <button type="button" class="btn btn-info btn-lg reset-manager-status-banking" data-lead-id="<?php echo e($lead->id); ?>" data-bs-dismiss="modal">
                                        <i class="mdi mdi-undo me-2"></i>Reset Manager Status
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Reason</label>
                            <textarea class="form-control" id="notes" name="bank_verification_notes" rows="3" placeholder="Add reason..."><?php echo e($lead->bank_verification_notes); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
$(document).ready(function() {
    // Handle reset Manager status button for Banking (Super Admin only)
    $('.reset-manager-status-banking').click(function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const button = $(this);
        
        if (confirm('Are you sure you want to reset the Manager status to Pending? This action is only available to Super Admin.')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: `/sales/${leadId}/manager-status/reset`,
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-undo me-2"></i>
                                <strong>Reset by Super Admin!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(alertHtml);
                        
                        // Reload the page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to reset Manager status');
                    button.prop('disabled', false);
                }
            });
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/bank-verification/show.blade.php ENDPATH**/ ?>