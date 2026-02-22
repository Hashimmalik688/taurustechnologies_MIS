<div class="row">
    <div class="col-md-6">
        <h6 class="text-primary">Basic Information</h6>
        <table class="table table-sm">
            <tr>
                <th width="40%">Name:</th>
                <td><strong><?php echo e($insuranceCarrier->name); ?></strong></td>
            </tr>
            <tr>
                <th>Payment Module:</th>
                <td>
                    <span class="badge bg-info">
                        <?php echo e(ucwords(str_replace('_', ' ', $insuranceCarrier->payment_module))); ?>

                    </span>
                </td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td><?php echo e($insuranceCarrier->phone ?: 'Not provided'); ?></td>
            </tr>
            <tr>
                <th>SSN Last 4:</th>
                <td><?php echo e($insuranceCarrier->ssn_last4 ? '***' . $insuranceCarrier->ssn_last4 : 'Not provided'); ?></td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insuranceCarrier->is_active): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-primary">Commission Details</h6>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insuranceCarrier->commissionBrackets && $insuranceCarrier->commissionBrackets->count() > 0): ?>
            <div class="d-flex flex-column gap-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarrier->commissionBrackets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bracket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <span class="badge bg-primary">
                            Ages <?php echo e($bracket->age_min); ?>-<?php echo e($bracket->age_max); ?>: <?php echo e(number_format($bracket->commission_percentage, 2)); ?>%
                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bracket->notes): ?>
                            <small class="text-muted d-block"><?php echo e($bracket->notes); ?></small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php elseif($insuranceCarrier->base_commission_percentage): ?>
            <span class="badge bg-secondary">
                <?php echo e(number_format($insuranceCarrier->base_commission_percentage, 2)); ?>% (All Ages)
            </span>
        <?php else: ?>
            <span class="text-muted">No commission structure defined</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insuranceCarrier->age_min || $insuranceCarrier->age_max): ?>
            <div class="mt-2">
                <small class="text-muted">
                    Age Range: <?php echo e($insuranceCarrier->age_min ?: '0'); ?> - <?php echo e($insuranceCarrier->age_max ?: '∞'); ?>

                </small>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insuranceCarrier->plan_types): ?>
    <div class="row mt-3">
        <div class="col-12">
            <h6 class="text-primary">Plan Types</h6>
            <div class="d-flex flex-wrap gap-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $insuranceCarrier->plan_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-light text-dark"><?php echo e($planType); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insuranceCarrier->calculation_notes): ?>
    <div class="row mt-3">
        <div class="col-12">
            <h6 class="text-primary">Calculation Notes</h6>
            <div class="alert alert-info">
                <?php echo e($insuranceCarrier->calculation_notes); ?>

            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Created: <?php echo e($insuranceCarrier->created_at->format('M d, Y')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insuranceCarrier->updated_at != $insuranceCarrier->created_at): ?>
                    | Updated: <?php echo e($insuranceCarrier->updated_at->format('M d, Y')); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </small>
            <div>
                <?php if(auth()->check() && auth()->user()->canEditModule('carriers')): ?>
                <a href="<?php echo e(route('admin.insurance-carriers.edit', $insuranceCarrier)); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="mdi mdi-pencil"></i> Edit
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/taurus-crm/resources/views/admin/insurance-carriers/show.blade.php ENDPATH**/ ?>