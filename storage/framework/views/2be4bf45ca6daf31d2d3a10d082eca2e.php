<?php $__env->startSection('title'); ?>
    Validator Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .modal-header-custom {
        background: linear-gradient(135deg, var(--bs-card-bg) 0%, rgba(212,175,55,.08) 100%);
        border-bottom: 1px solid rgba(212,175,55,.15);
        color: var(--bs-surface-800);
    }
    .modal-header-custom .modal-title { font-size: .85rem; font-weight: 700; }
    .modal-dialog-scrollable .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    .modal-xl { max-width: 1200px; }
    /* Toggle switch pill */
    .pipe-toggle {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .68rem; font-weight: 600; color: var(--bs-surface-500);
        padding: .25rem .6rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
        background: var(--bs-card-bg); cursor: pointer;
    }
    .pipe-toggle input { width: 14px; height: 14px; accent-color: #d4af37; cursor: pointer; }
    .pipe-toggle.active { border-color: rgba(212,175,55,.3); background: rgba(212,175,55,.06); color: #b89730; }
    /* Partner badge */
    .v-partner { display:inline-block;padding:.15rem .4rem;border-radius:10px;font-size:.62rem;font-weight:700;background:rgba(80,141,237,.1);color:#508ded;border:1px solid rgba(80,141,237,.15); }
    /* Action button group inline */
    .act-group { display:inline-flex; gap:.25rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <form method="GET" action="<?php echo e(route('validator.index')); ?>" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="<?php echo e(route('validator.index', ['filter' => 'today'])); ?>" class="pipe-pill <?php echo e($filter === 'today' ? 'active' : ''); ?>"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill <?php echo e($filter === 'custom' ? 'active' : ''); ?>" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:<?php echo e($filter === 'custom' ? 'flex' : 'none'); ?>;align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="<?php echo e(request('start_date')); ?>" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="<?php echo e(request('end_date')); ?>" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <label class="pipe-toggle <?php echo e(request('show_all_pending') ? 'active' : ''); ?>">
            <input type="checkbox" name="show_all_pending" value="1" <?php echo e(request('show_all_pending') ? 'checked' : ''); ?> onchange="document.getElementById('filterForm').submit()">
            Show all pending
        </label>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter !== 'today'): ?>
            <a href="<?php echo e(route('validator.index', ['filter' => 'today'])); ?>" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="ex-card" style="display:flex;align-items:center;gap:.5rem;padding:.55rem .85rem;margin-bottom:.65rem;background:rgba(16,185,129,.06);border-color:rgba(16,185,129,.15);">
            <i class="bx bx-check-circle" style="color:#10b981;font-size:1rem;"></i>
            <span style="font-size:.78rem;font-weight:600;color:#065f46;"><?php echo e(session('success')); ?></span>
            <button type="button" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#065f46;opacity:.6;font-size:1rem;" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="kpi-row">
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-send k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['submitted'] ?? 0); ?></div>
            <div class="k-lbl">Submitted</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time-five k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['pending'] ?? 0); ?></div>
            <div class="k-lbl">Pending</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['sales'] ?? 0); ?></div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-undo k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['returned'] ?? 0); ?></div>
            <div class="k-lbl">Returned</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val"><?php echo e($todayStats['declined'] ?? 0); ?></div>
            <div class="k-lbl">Declined</div>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#b87a14;">
            <i class="bx bx-check-shield" style="color:#f1b44c;"></i> Pending Validation
            <span class="badge-count"><?php echo e($pendingLeads->count()); ?></span>
        </div>
        <div class="scroll-tbl" style="max-height:400px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Verifier</th>
                        <th>Closer</th>
                        <th class="text-center">Partner</th>
                        <th class="text-end">Coverage</th>
                        <th>Submitted</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                            <td><?php echo e($lead->phone_number ?? 'N/A'); ?></td>
                            <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                            <td><?php echo e($lead->closer_name ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->assigned_partner): ?>
                                    <span class="v-partner"><?php echo e($lead->assigned_partner); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-end">$<?php echo e(number_format($lead->coverage_amount ?? 0, 0)); ?></td>
                            <td style="white-space:nowrap;"><?php echo e($lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A')); ?></td>
                            <td class="text-center">
                                <button type="button" class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($lead->id); ?>"><i class="bx bx-edit"></i> Review</button>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="editModal<?php echo e($lead->id); ?>" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <h5 class="modal-title">Validate Lead — <?php echo e($lead->cn_name); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('validator.update', $lead->id)); ?>" id="validatorForm<?php echo e($lead->id); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body u-overflow-y-auto" style="max-height: calc(100vh - 250px)">
                                            <?php echo $__env->make('peregrine.closers.form', ['lead' => $lead, 'isValidator' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        </div>
                                        <div class="modal-footer" style="gap:.3rem;">
                                            <button type="submit" class="act-btn a-success" style="padding:.35rem .7rem;"><i class="bx bx-check"></i> Mark as Sale</button>
                                            <button type="button" class="act-btn a-warn" style="padding:.35rem .7rem;" onclick="document.getElementById('forwardForm<?php echo e($lead->id); ?>').submit(); return false;"><i class="bx bx-send"></i> Home Office</button>
                                            <button type="button" class="act-btn a-danger" style="padding:.35rem .7rem;" onclick="document.getElementById('declineForm<?php echo e($lead->id); ?>').submit(); return false;"><i class="bx bx-x"></i> Declined</button>
                                            <button type="button" class="act-btn a-info" style="padding:.35rem .7rem;" onclick="returnToCloser<?php echo e($lead->id); ?>()"><i class="bx bx-arrow-back"></i> Return to Closer</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>

                                    
                                    <form method="POST" action="<?php echo e(route('validator.mark-forwarded', $lead->id)); ?>" id="forwardForm<?php echo e($lead->id); ?>" class="d-none">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('validator.mark-simple-declined', $lead->id)); ?>" id="declineForm<?php echo e($lead->id); ?>" class="d-none">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                    </form>
                                </div>
                            </div>
                        </div>

                        
                        <div class="modal fade" id="declineModal<?php echo e($lead->id); ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background:rgba(244,106,106,.08);border-bottom:1px solid rgba(244,106,106,.15);">
                                        <h5 class="modal-title" style="font-size:.85rem;font-weight:700;">Select Decline Reason</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('validator.mark-failed', $lead->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body">
                                            <p class="mb-3" style="font-size:.8rem;">Why is this lead being declined?</p>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Declined:POA', 'Declined:DNQ-Age', 'Declined:Declined SSN', 'Declined:Not Interested', 'Declined:DNC', 'Declined:Cannot Afford', 'Declined:DNQ-Health', 'Declined:Declined Banking', 'Declined:No Pitch (Not Interested)', 'Declined:No Answer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="decline_reason" id="dr_<?php echo e(Str::slug($reason)); ?>_<?php echo e($lead->id); ?>" value="<?php echo e($reason); ?>" required>
                                                <label class="form-check-label" for="dr_<?php echo e(Str::slug($reason)); ?>_<?php echo e($lead->id); ?>" style="font-size:.8rem;font-weight:600;"><?php echo e($reason); ?></label>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="act-btn a-danger" style="padding:.35rem .7rem;">Confirm Declined</button>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                            <i class="bx bx-inbox" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No pending leads for validation
                        </td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#508ded;">
            <i class="bx bx-building-house" style="color:#508ded;"></i> Sent to Home Office
            <span class="badge-count"><?php echo e($homeOfficeLeads->count()); ?></span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Customer Name</th>
                        <th>Closer</th>
                        <th class="text-center">Partner</th>
                        <th>Verifier</th>
                        <th class="text-end">Coverage</th>
                        <th>Submitted</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $homeOfficeLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong>#<?php echo e($lead->id); ?></strong></td>
                            <td><?php echo e($lead->cn_name); ?></td>
                            <td><?php echo e($lead->assignedCloser->name ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->assigned_partner): ?>
                                    <span class="v-partner"><?php echo e($lead->assigned_partner); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><?php echo e($lead->verifier->name ?? 'N/A'); ?></td>
                            <td class="text-end">$<?php echo e(number_format($lead->coverage_amount ?? 0, 0)); ?></td>
                            <td style="white-space:nowrap;"><?php echo e($lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A')); ?></td>
                            <td class="text-center">
                                <div class="act-group">
                                    <form method="POST" action="<?php echo e(route('validator.mark-home-office-sale', $lead->id)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <button type="submit" class="act-btn a-success" onclick="return confirm('Mark this lead as Sale?')"><i class="bx bx-check"></i> Sale</button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('validator.mark-simple-declined', $lead->id)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <button type="submit" class="act-btn a-danger" onclick="return confirm('Mark this lead as Declined?')"><i class="bx bx-x"></i> Decline</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-inbox"></i> No leads sent to home office</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#1a8754;">
            <i class="bx bx-check-circle" style="color:#34c38f;"></i> Completed Validations
            <span class="badge-count"><?php echo e($completedLeads->count()); ?></span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Closer</th>
                        <th class="text-center">Partner</th>
                        <th>Verifier</th>
                        <th class="text-center">Status</th>
                        <th>Validated By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $completedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                            <td><?php echo e($lead->closer_name ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->assigned_partner): ?>
                                    <span class="v-partner"><?php echo e($lead->assigned_partner); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><?php echo e($lead->account_verified_by ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status == 'sale'): ?>
                                    <span class="s-pill s-sale">Sale</span>
                                <?php elseif($lead->status == 'forwarded'): ?>
                                    <span class="s-pill s-forwarded">Forwarded</span>
                                <?php else: ?>
                                    <span class="s-pill s-declined"><?php echo e($lead->failure_reason ?? 'Failed'); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><?php echo e($lead->validator ? $lead->validator->name : 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No completed validations yet</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
    // No additional JS needed — filter bar uses direct links + form submit
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/validator/index.blade.php ENDPATH**/ ?>