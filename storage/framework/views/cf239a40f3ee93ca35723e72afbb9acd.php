<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title', 'Dock Management'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .dock-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .dock-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .dock-hdr h4 i { color: #d4af37; font-size: 1.2rem; }
    .dock-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }

    /* Info banner */
    .dock-info {
        background: rgba(80,165,241,.04); border: 1px solid rgba(80,165,241,.12);
        border-radius: .45rem; padding: .55rem .75rem; margin-bottom: .65rem;
        font-size: .7rem; color: #2b81c9; display: flex; align-items: flex-start; gap: .4rem;
    }
    .dock-info i { font-size: .85rem; flex-shrink: 0; margin-top: 1px; }
    .dock-info strong { color: #1a5a8c; }

    /* Notes row */
    .note-row td {
        background: rgba(212,175,55,.02) !important;
        font-size: .68rem; color: var(--bs-surface-500);
        padding: .25rem .75rem .25rem 2rem !important;
        border-bottom: 1px solid rgba(0,0,0,.03) !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-check-all me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-block-helper me-1"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Header -->
    <div class="dock-hdr">
        <div>
            <h4><i class="bx bx-wallet"></i> Dock Management</h4>
            <p>Employee fines &amp; salary deductions for <?php echo e(\Carbon\Carbon::create($year, $month)->format('F Y')); ?></p>
        </div>
        <button type="button" class="act-btn a-success" data-bs-toggle="modal" data-bs-target="#addDockModal"><i class="mdi mdi-plus"></i> Add Dock</button>
    </div>

    <!-- Info Banner -->
    <div class="dock-info">
        <i class="mdi mdi-information-outline"></i>
        <span><strong>Payroll Cycle:</strong> 26th of previous month → 25th of current month. Dock records are auto-assigned to the correct payroll month based on date.</span>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card k-red">
            <span class="k-icon"><i class="bx bx-money"></i></span>
            <span class="k-val">Rs <?php echo e(number_format($stats['total_docked'], 0)); ?></span>
            <span class="k-lbl">Total Docked</span>
        </div>
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-file"></i></span>
            <span class="k-val"><?php echo e($stats['total_records']); ?></span>
            <span class="k-lbl">Total Records</span>
        </div>
        <div class="kpi-card k-warn">
            <span class="k-icon"><i class="bx bx-error-circle"></i></span>
            <span class="k-val"><?php echo e($stats['active_records']); ?></span>
            <span class="k-lbl">Active</span>
        </div>
        <div class="kpi-card k-blue">
            <span class="k-icon"><i class="bx bx-group"></i></span>
            <span class="k-val"><?php echo e($employees->count()); ?></span>
            <span class="k-lbl">Employees</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="<?php echo e(route('dock.index')); ?>">
    <div class="ex-card pipe-filter-bar">
        <span class="pipe-pill-lbl">MONTH</span>
        <select class="sl-pill-select" name="month" style="min-width:100px">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?></option>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>

        <span class="pipe-pill-lbl" style="margin-left:.5rem">YEAR</span>
        <select class="sl-pill-select" name="year" style="min-width:75px">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>

        <span class="pipe-pill-lbl" style="margin-left:.5rem">EMPLOYEE</span>
        <select class="sl-pill-select" name="user_id" style="min-width:140px">
            <option value="">All Employees</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($emp->id); ?>" <?php echo e(request('user_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>

        <button type="submit" class="pipe-pill-apply"><i class="mdi mdi-magnify"></i> Filter</button>
        <a href="<?php echo e(route('dock.index')); ?>" class="pipe-pill-clear"><i class="mdi mdi-refresh"></i> Reset</a>
    </div>
    </form>

    <!-- Dock Records Table -->
    <div class="ex-card sec-card">
        <div class="pipe-hdr">
            <i class="mdi mdi-file-document-multiple"></i> Dock Records
            <span class="badge-count"><?php echo e($dockRecords->total()); ?></span>
        </div>
        <div class="sec-body" style="padding:0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dockRecords->count() > 0): ?>
            <div class="table-responsive">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th><th>Employee</th><th>Amount</th><th>Reason</th><th>Dock Date</th><th>Docked By</th><th>Status</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dockRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><span style="font-size:.72rem;color:var(--bs-surface-400)">#<?php echo e($record->id); ?></span></td>
                            <td>
                                <a href="<?php echo e(route('dock.history', $record->user_id)); ?>" style="font-weight:600;font-size:.78rem;color:inherit;text-decoration:none">
                                    <?php echo e($record->user?->name ?? 'Unknown'); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->user?->trashed()): ?>
                                        <span class="s-pill s-declined" style="font-size:.5rem">Ended</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </td>
                            <td><span class="v-badge v-red">Rs <?php echo e(number_format($record->amount, 2)); ?></span></td>
                            <td><span style="font-size:.72rem"><?php echo e(Str::limit($record->reason, 40)); ?></span></td>
                            <td><span style="font-size:.75rem"><?php echo e($record->dock_date->format('d M Y')); ?></span></td>
                            <td><span style="font-size:.72rem"><?php echo e($record->dockedBy?->name ?? 'Unknown'); ?></span></td>
                            <td>
                                <?php
                                    $dPill = match($record->status) {
                                        'active' => 's-pending',
                                        'applied' => 's-sale',
                                        'cancelled' => 's-closed',
                                        default => 's-closed'
                                    };
                                ?>
                                <span class="s-pill <?php echo e($dPill); ?>"><?php echo e(ucfirst($record->status)); ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if(auth()->check() && auth()->user()->canEditModule('dock')): ?>
                                    <button type="button" class="act-btn a-primary"
                                            data-record-id="<?php echo e($record->id); ?>"
                                            data-employee-name="<?php echo e($record->user?->name ?? 'Unknown'); ?>"
                                            data-amount="<?php echo e($record->amount); ?>"
                                            data-dock-date="<?php echo e($record->dock_date->format('Y-m-d')); ?>"
                                            data-status="<?php echo e($record->status); ?>"
                                            data-reason="<?php echo e($record->reason); ?>"
                                            data-notes="<?php echo e($record->notes ?? ''); ?>"
                                            onclick="editDock(this)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editDockModal"
                                            title="Edit"><i class="mdi mdi-pencil"></i></button>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->status === 'active'): ?>
                                    <form action="<?php echo e(route('dock.cancel', $record->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel this dock?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="act-btn a-warn" title="Cancel"><i class="mdi mdi-cancel"></i></button>
                                    </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(auth()->check() && auth()->user()->canDeleteInModule('dock')): ?>
                                    <form action="<?php echo e(route('dock.destroy', $record->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete permanently?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="act-btn a-danger" title="Delete"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->notes): ?>
                        <tr class="note-row">
                            <td colspan="8"><i class="mdi mdi-note-text me-1"></i><?php echo e($record->notes); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:.5rem .75rem"><?php echo e($dockRecords->appends(request()->query())->links('pagination::bootstrap-5')); ?></div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="mdi mdi-information-outline" style="font-size:2rem;color:var(--bs-surface-300)"></i>
                <p class="text-muted mt-1" style="font-size:.78rem">No dock records found for the selected period.</p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Add Dock Modal -->
    <div class="modal fade" id="addDockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="<?php echo e(route('dock.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header modal-header-glass">
                        <h5 class="modal-title" style="font-size:.88rem"><i class="mdi mdi-plus-circle me-1"></i> Add Dock Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Select Employee</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount (Rs) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Dock Date <span class="text-danger">*</span></label>
                                <input type="date" name="dock_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                                <small class="text-muted" style="font-size:.6rem"><i class="mdi mdi-information"></i> Auto-assigned to payroll month (26th-25th cycle)</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reason for docking..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Additional Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-success"><i class="mdi mdi-check"></i> Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Dock Modal -->
    <div class="modal fade" id="editDockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editDockForm" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="modal-header modal-header-glass">
                        <h5 class="modal-title" style="font-size:.88rem"><i class="mdi mdi-pencil me-1"></i> Edit Dock Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee</label>
                                <input type="text" id="edit_employee_name" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount (Rs) <span class="text-danger">*</span></label>
                                <input type="number" id="edit_amount" name="amount" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Dock Date <span class="text-danger">*</span></label>
                                <input type="date" id="edit_dock_date" name="dock_date" class="form-control" required>
                                <small class="text-muted" style="font-size:.6rem"><i class="mdi mdi-information"></i> Auto-assigned to payroll month</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="edit_status" name="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="applied">Applied</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea id="edit_reason" name="reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Additional Notes</label>
                                <textarea id="edit_notes" name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-primary"><i class="mdi mdi-content-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function editDock(button) {
    var id = button.getAttribute('data-record-id');
    document.getElementById('editDockForm').action = '/dock/' + id;
    document.getElementById('edit_employee_name').value = button.getAttribute('data-employee-name') || '';
    document.getElementById('edit_amount').value = button.getAttribute('data-amount') || 0;
    document.getElementById('edit_dock_date').value = button.getAttribute('data-dock-date') || '';
    document.getElementById('edit_status').value = button.getAttribute('data-status') || 'active';
    document.getElementById('edit_reason').value = button.getAttribute('data-reason') || '';
    document.getElementById('edit_notes').value = button.getAttribute('data-notes') || '';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/dock/index.blade.php ENDPATH**/ ?>