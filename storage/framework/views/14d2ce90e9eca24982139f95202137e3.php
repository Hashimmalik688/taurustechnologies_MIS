<?php $__env->startSection('title'); ?>
    Incomplete Issuance - Details
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <a href="<?php echo e(route('retention.incomplete')); ?>">Retention</a>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Incomplete Details
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="mdi mdi-file-document me-2"></i><?php echo e($lead->cn_name); ?> - Complete Details
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-account me-2"></i>Customer Information
                    </h5>
                    <a href="<?php echo e(route('retention.incomplete')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo e($lead->cn_name); ?></p>
                            <p><strong>Phone:</strong> <?php echo e($lead->phone_number); ?></p>
                            <p><strong>Secondary Phone:</strong> <?php echo e($lead->secondary_phone_number ?? 'N/A'); ?></p>
                            <p><strong>SSN:</strong> <?php echo e($lead->ssn ? '****'.substr($lead->ssn, -4) : 'N/A'); ?></p>
                            <p><strong>Date of Birth:</strong> <?php echo e($lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('M d, Y') : 'N/A'); ?></p>
                            <p><strong>State:</strong> <?php echo e($lead->state ?? 'N/A'); ?></p>
                            <p><strong>Zip Code:</strong> <?php echo e($lead->zip_code ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> <?php echo e($lead->address ?? 'N/A'); ?></p>
                            <p><strong>Gender:</strong> <?php echo e($lead->gender ?? 'N/A'); ?></p>
                            <p><strong>Smoker:</strong> <?php echo e($lead->smoker ?? 'N/A'); ?></p>
                            <p><strong>Height:</strong> <?php echo e($lead->height ?? 'N/A'); ?></p>
                            <p><strong>Weight:</strong> <?php echo e($lead->weight ? $lead->weight . ' lbs' : 'N/A'); ?></p>
                            <p><strong>Emergency Contact:</strong> <?php echo e($lead->emergency_contact ?? 'N/A'); ?></p>
                            <p><strong>Beneficiary:</strong> <?php echo e($lead->beneficiary ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-briefcase me-2"></i>Policy Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Carrier:</strong> <?php echo e($lead->carrier_name ?? 'N/A'); ?></p>
                            <p><strong>Policy Number:</strong> <?php echo e($lead->policy_number ?? 'N/A'); ?></p>
                            <p><strong>Policy Type:</strong> <?php echo e($lead->policy_type ?? 'N/A'); ?></p>
                            <p><strong>Coverage Amount:</strong> <span class="text-gold fw-semibold">$<?php echo e(number_format($lead->coverage_amount ?? 0, 2)); ?></span></p>
                            <p><strong>Monthly Premium:</strong> <span class="text-gold fw-semibold">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Sale Date:</strong> <?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></p>
                            <p><strong>Initial Draft Date:</strong> <?php echo e($lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A'); ?></p>
                            <p><strong>Future Draft Date:</strong> <?php echo e($lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A'); ?></p>
                            <p><strong>Closer:</strong> <?php echo e($lead->closer_name ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-bank me-2"></i>Bank Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Bank Name:</strong> <?php echo e($lead->bank_name ?? 'N/A'); ?></p>
                            <p><strong>Account Title:</strong> <?php echo e($lead->account_title ?? 'N/A'); ?></p>
                            <p><strong>Account Type:</strong> <?php echo e($lead->account_type ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Routing Number:</strong> <?php echo e($lead->routing_number ? '****'.substr($lead->routing_number, -4) : 'N/A'); ?></p>
                            <p><strong>Account Number:</strong> <?php echo e($lead->account_number ? '****'.substr($lead->account_number, -4) : 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-clipboard-list me-2"></i>Approval Status & Issuance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>QA Status:</strong> 
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->qa_status): ?>
                                    <span class="badge bg-info"><?php echo e($lead->qa_status); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p><strong>QA Reason:</strong> <?php echo e($lead->qa_reason ?? '—'); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Manager Status:</strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->manager_status): ?>
                                    <span class="badge bg-warning"><?php echo e($lead->manager_status); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p><strong>Manager Reason:</strong> <?php echo e($lead->manager_reason ?? '—'); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Issuance Status:</strong>
                                <span class="badge bg-danger"><?php echo e($lead->issuance_status ?? 'N/A'); ?></span>
                            </p>
                            <p><strong>Issuance Reason:</strong> <?php echo e($lead->issuance_reason ?? '—'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-history me-2"></i>Disposition Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issuance_disposition): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Disposition Channel:</strong>
                                    <?php
                                        $badgeClass = match($lead->issuance_disposition) {
                                            'Via Portal' => 'bg-success',
                                            'Via Email' => 'bg-info',
                                            'By Carrier' => 'bg-warning',
                                            'By Bank' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($lead->issuance_disposition); ?></span>
                                </p>
                                <p><strong>Disposition Date:</strong> <?php echo e($lead->issuance_disposition_date ? \Carbon\Carbon::parse($lead->issuance_disposition_date)->format('M d, Y h:i A') : 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Disposition Officer:</strong> <?php echo e($lead->dispositionOfficer->name ?? 'N/A'); ?></p>
                                <p><strong>Other Insurances Found:</strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->has_other_insurances): ?>
                                        <span class="badge bg-warning">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">No</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p><strong>Disposition Notes:</strong></p>
                            <div class="alert alert-light p-3">
                                <?php echo e($lead->issuance_reason ?? 'No notes recorded'); ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>No disposition recorded yet.</strong> This lead is awaiting disposition assignment.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/retention/incomplete-details.blade.php ENDPATH**/ ?>