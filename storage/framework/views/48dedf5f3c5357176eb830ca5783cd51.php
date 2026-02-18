<?php $__env->startSection('title', 'Dock History - ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo e(route('dock.index')); ?>" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back to Dock Section
                </a>
            </div>
            <h4 class="page-title">Dock History - <?php echo e($user->name); ?></h4>
        </div>
    </div>
</div>

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

<!-- Dock History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Complete Dock History</h4>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dockRecords->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Dock Date</th>
                                <th>Month/Year</th>
                                <th>Docked By</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dockRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($record->id); ?></td>
                                <td><strong>Rs <?php echo e(number_format($record->amount, 2)); ?></strong></td>
                                <td><?php echo e(Str::limit($record->reason, 50)); ?></td>
                                <td><?php echo e($record->dock_date->format('d M Y')); ?></td>
                                <td><?php echo e(\Carbon\Carbon::create($record->dock_year, $record->dock_month)->format('M Y')); ?></td>
                                <td><?php echo e($record->dockedBy->name); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->status === 'active'): ?>
                                        <span class="badge bg-warning">Active</span>
                                    <?php elseif($record->status === 'applied'): ?>
                                        <span class="badge bg-success">Applied</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Cancelled</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($record->created_at->format('d M Y g:i A')); ?></td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->notes): ?>
                            <tr>
                                <td colspan="8" class="text-muted small ps-5">
                                    <i class="mdi mdi-note-text"></i> <?php echo e($record->notes); ?>

                                </td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="1"><strong>Summary:</strong></td>
                                <td colspan="7">
                                    <strong>Active Docks: Rs <?php echo e(number_format($totalDocked, 2)); ?></strong>
                                    | Total Records: <?php echo e($dockRecords->total()); ?>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3">
                    <?php echo e($dockRecords->links()); ?>

                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="mdi mdi-information-outline" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">No dock records found for this employee.</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/dock/history.blade.php ENDPATH**/ ?>