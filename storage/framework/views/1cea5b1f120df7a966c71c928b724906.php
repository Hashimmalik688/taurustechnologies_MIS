<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->count() > 0): ?>
    <div class="results-header">
        <h6>Results <span class="text-muted">(<?php echo e($results->total()); ?> records)</span></h6>
        <span class="text-muted" style="font-size: 0.82rem;">
            Showing <?php echo e($results->firstItem()); ?>–<?php echo e($results->lastItem()); ?> of <?php echo e($results->total()); ?>

        </span>
    </div>
    <div class="table-responsive">
        <table class="results-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Carrier</th>
                    <th>Coverage</th>
                    <th>Premium</th>
                    <th>Policy Type</th>
                    <th>Closer</th>
                    <th>Partner</th>
                    <th>Source</th>
                    <th>Team</th>
                    <th>Sale Date</th>
                    <th>QA</th>
                    <th>Manager</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($lead->id); ?></td>
                        <td><strong><?php echo e($lead->cn_name ?? '—'); ?></strong></td>
                        <td><?php echo e($lead->phone_number ?? '—'); ?></td>
                        <td><?php echo e($lead->state ?? '—'); ?></td>
                        <td>
                            <?php
                                $statusClass = match($lead->status) {
                                    'sale' => 'status-sale',
                                    'pending' => 'status-pending',
                                    'declined' => 'status-declined',
                                    'chargeback' => 'status-chargeback',
                                    'accepted' => 'status-accepted',
                                    default => 'status-default',
                                };
                            ?>
                            <span class="status-badge <?php echo e($statusClass); ?>"><?php echo e(ucfirst($lead->status ?? '—')); ?></span>
                        </td>
                        <td><?php echo e($lead->insurance_carrier_name ?? $lead->carrier_name ?? '—'); ?></td>
                        <td><?php echo e($lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 2) : '—'); ?></td>
                        <td><?php echo e($lead->monthly_premium ? '$' . number_format($lead->monthly_premium, 2) : '—'); ?></td>
                        <td><?php echo e($lead->policy_type ?? '—'); ?></td>
                        <td><?php echo e($lead->closer_user_name ?? $lead->closer_name ?? '—'); ?></td>
                        <td><?php echo e($lead->partner_name ?? '—'); ?></td>
                        <td><?php echo e($lead->source ?? '—'); ?></td>
                        <td><?php echo e($lead->team ?? '—'); ?></td>
                        <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—'); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->qa_status): ?>
                                <?php
                                    $qaClass = match($lead->qa_status) {
                                        'Good' => 'status-sale',
                                        'Avg' => 'status-pending',
                                        'Bad' => 'status-declined',
                                        default => 'status-default',
                                    };
                                ?>
                                <span class="status-badge <?php echo e($qaClass); ?>"><?php echo e($lead->qa_status); ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->manager_status): ?>
                                <?php
                                    $mgrClass = match($lead->manager_status) {
                                        'approved' => 'status-sale',
                                        'pending' => 'status-pending',
                                        'declined' => 'status-declined',
                                        'chargeback' => 'status-chargeback',
                                        default => 'status-default',
                                    };
                                ?>
                                <span class="status-badge <?php echo e($mgrClass); ?>"><?php echo e(ucfirst($lead->manager_status)); ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><?php echo e($lead->created_at ? \Carbon\Carbon::parse($lead->created_at)->format('M d, Y') : '—'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->hasPages()): ?>
        <div style="padding: 14px 20px; border-top: 1px solid #e9ecef; display: flex; justify-content: center;">
            <?php echo e($results->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="bx bx-search-alt"></i>
        <h6>No records found</h6>
        <p>Try adjusting your filters to find matching records</p>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/_results.blade.php ENDPATH**/ ?>