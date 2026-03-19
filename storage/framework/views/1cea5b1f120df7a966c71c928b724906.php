<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->count() > 0): ?>
    <div class="rp-results-hdr">
        <h6><i class="bx bx-table"></i> Results <span>(<?php echo e(number_format($results->total())); ?> records)</span></h6>
        <div class="rp-results-meta">
            <span>Showing <?php echo e($results->firstItem()); ?>–<?php echo e($results->lastItem()); ?> of <?php echo e(number_format($results->total())); ?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="rp-table" id="reportTable">
            <thead>
                <tr>
                    <th class="rp-th-id">#</th>
                    <th class="rp-th-name">Client Name</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Carrier</th>
                    <th class="rp-th-num">Coverage</th>
                    <th class="rp-th-num">Premium</th>
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
                        <td class="rp-td-id"><?php echo e($lead->id); ?></td>
                        <td class="rp-td-name"><?php echo e($lead->cn_name ?? '—'); ?></td>
                        <td class="rp-td-mono"><?php echo e($lead->phone_number ?? '—'); ?></td>
                        <td><?php echo e($lead->state ?? '—'); ?></td>
                        <td>
                            <?php
                                $statusClass = match(strtolower($lead->status ?? '')) {
                                    'sale' => 'rp-badge-sale',
                                    'pending' => 'rp-badge-pending',
                                    'declined' => 'rp-badge-declined',
                                    'chargeback' => 'rp-badge-chargeback',
                                    'accepted','underwritten' => 'rp-badge-accepted',
                                    'transferred' => 'rp-badge-transferred',
                                    'returned' => 'rp-badge-returned',
                                    'closed' => 'rp-badge-closed',
                                    'disposed' => 'rp-badge-declined',
                                    default => 'rp-badge-default',
                                };
                            ?>
                            <span class="rp-badge <?php echo e($statusClass); ?>"><?php echo e(ucfirst($lead->status ?? '—')); ?></span>
                        </td>
                        <td><?php echo e(trim($lead->insurance_carrier_name ?? $lead->carrier_name ?? '—')); ?></td>
                        <td class="rp-td-num"><?php echo e($lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 0) : '—'); ?></td>
                        <td class="rp-td-num"><?php echo e($lead->monthly_premium ? '$' . number_format($lead->monthly_premium, 2) : '—'); ?></td>
                        <td><?php echo e($lead->policy_type ?? '—'); ?></td>
                        <td><?php echo e($lead->closer_user_name ?? $lead->closer_name ?? '—'); ?></td>
                        <td><?php echo e($lead->partner_name ?? $lead->assigned_partner ?? '—'); ?></td>
                        <td><?php echo e($lead->source ?? '—'); ?></td>
                        <td><?php echo e($lead->team ?? '—'); ?></td>
                        <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—'); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->qa_status): ?>
                                <?php
                                    $qaClass = match($lead->qa_status) {
                                        'Good' => 'rp-badge-sale',
                                        'Avg' => 'rp-badge-pending',
                                        'Bad' => 'rp-badge-declined',
                                        default => 'rp-badge-default',
                                    };
                                ?>
                                <span class="rp-badge <?php echo e($qaClass); ?>"><?php echo e($lead->qa_status); ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->manager_status): ?>
                                <?php
                                    $mgrClass = match($lead->manager_status) {
                                        'approved' => 'rp-badge-sale',
                                        'pending' => 'rp-badge-pending',
                                        'declined' => 'rp-badge-declined',
                                        'chargeback' => 'rp-badge-chargeback',
                                        'underwriting' => 'rp-badge-accepted',
                                        default => 'rp-badge-default',
                                    };
                                ?>
                                <span class="rp-badge <?php echo e($mgrClass); ?>"><?php echo e(ucfirst($lead->manager_status)); ?></span>
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
        <div class="rp-pagination">
            <?php echo e($results->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <div class="rp-empty">
        <i class="bx bx-search-alt"></i>
        <h6>No records found</h6>
        <p>Try adjusting your filters to find matching records</p>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/_results.blade.php ENDPATH**/ ?>