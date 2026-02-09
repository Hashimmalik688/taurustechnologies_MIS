<?php $__env->startSection('title'); ?>
    My Dock Records
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('css/light-theme.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Employee
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            My Dock Records
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Summary Card -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-white mb-1"><?php echo e($user->name); ?></h5>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($role->name); ?><?php echo e(!$loop->last ? ', ' : ''); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h2 class="text-white mb-0">Rs <?php echo e(number_format($totalDocked, 2)); ?></h2>
                            <small>Total Active Docks</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dock Records Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">My Dock Records</h4>
                    <p class="text-muted">View all your dock records including reasons and who applied them.</p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dockRecords->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                    <th>Applied By</th>
                                    <th>Status</th>
                                    <th>Month Applied</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dockRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($record->dock_date->format('d M Y')); ?></td>
                                    <td>
                                        <span class="badge bg-danger fs-6">Rs <?php echo e(number_format($record->amount, 2)); ?></span>
                                    </td>
                                    <td>
                                        <div class="reason-text">
                                            <?php echo e($record->reason); ?>

                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->notes): ?>
                                            <small class="text-muted d-block mt-1">
                                                <i class="mdi mdi-note-text"></i> <?php echo e($record->notes); ?>

                                            </small>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <?php echo e(substr($record->dockedBy->name, 0, 1)); ?>

                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($record->dockedBy->name); ?></h6>
                                                <small class="text-muted"><?php echo e($record->created_at->format('g:i A')); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->status === 'active'): ?>
                                            <span class="badge bg-warning">Active</span>
                                        <?php elseif($record->status === 'applied'): ?>
                                            <span class="badge bg-success">Applied to Salary</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Cancelled</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><?php echo e(\Carbon\Carbon::create($record->dock_year, $record->dock_month)->format('M Y')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-soft-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Active Docks</h5>
                                    <h3 class="text-warning">Rs <?php echo e(number_format($totalDocked, 2)); ?></h3>
                                    <small class="text-muted">This amount will be deducted from your salary</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-soft-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Records</h5>
                                    <h3 class="text-info"><?php echo e($dockRecords->total()); ?></h3>
                                    <small class="text-muted">All dock records (active, applied, cancelled)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <?php echo e($dockRecords->links()); ?>

                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                <i class="bx bx-check-circle" style="font-size: 32px;"></i>
                            </div>
                        </div>
                        <h5 class="text-success">Great Job!</h5>
                        <p class="text-muted">You have no dock records. Keep up the excellent work!</p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Panel -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-soft-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Understanding Dock Records
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-dark">Status Meanings:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><span class="badge bg-warning me-2">Active</span>Pending deduction from salary</li>
                                <li><span class="badge bg-success me-2">Applied</span>Already deducted from salary</li>
                                <li><span class="badge bg-secondary me-2">Cancelled</span>Dock was cancelled/removed</li>
                            </ul>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-dark">Important Information:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="mdi mdi-check-circle text-success me-2"></i>Active docks will be deducted from your next salary</li>
                                <li><i class="mdi mdi-check-circle text-success me-2"></i>Applied docks have already been deducted and won't be charged again</li>
                                <li><i class="mdi mdi-check-circle text-success me-2"></i>If you have questions about any dock, contact your supervisor or HR</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    // Add any JavaScript if needed
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/employee/dock-records.blade.php ENDPATH**/ ?>