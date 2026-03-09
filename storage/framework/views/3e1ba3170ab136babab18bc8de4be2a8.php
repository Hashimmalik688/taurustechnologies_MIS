<?php $__env->startSection('title', 'PABS - Support Tickets'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.custom-select-datepicker-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .tk-status{display:inline-block;padding:.22rem .7rem;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.3px}
    .tk-open{background:rgba(59,130,246,.12);color:#2563eb}
    .tk-in-progress,.tk-in_progress{background:rgba(139,92,246,.12);color:#7c3aed}
    .tk-on-hold,.tk-on_hold{background:rgba(245,158,11,.12);color:#d97706}
    .tk-resolved{background:rgba(16,185,129,.12);color:#059669}
    .tk-closed{background:rgba(107,114,128,.15);color:#6b7280}
    .pr-high{color:#ef4444;font-weight:600}
    .pr-medium{color:#f59e0b;font-weight:600}
    .pr-low{color:#10b981;font-weight:600}
    .sec-tag{display:inline-block;padding:.18rem .55rem;border-radius:14px;font-size:.7rem;background:rgba(99,102,241,.1);color:#6366f1;font-weight:500}
    .tk-code{font-weight:700;color:var(--bs-body-color);font-size:.82rem;letter-spacing:.4px}
    .tk-subject{color:var(--bs-body-color);text-decoration:none;font-weight:500;font-size:.82rem;transition:color .2s}
    .tk-subject:hover{color:#b8860b}
    .tk-meta{font-size:.72rem;color:#9ca3af}
    .app-badge{display:inline-block;padding:.18rem .55rem;border-radius:14px;font-size:.7rem;font-weight:600}
    .app-pending{background:rgba(245,158,11,.12);color:#d97706}
    .app-approved{background:rgba(16,185,129,.12);color:#059669}
    .app-rejected{background:rgba(239,68,68,.12);color:#ef4444}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="kpi-row" style="grid-template-columns:repeat(6,1fr)">
        <div class="kpi-card k-blue">
            <div class="kpi-icon"><i class="bx bx-list-check"></i></div>
            <div class="kpi-label">Total Tickets</div>
            <div class="kpi-value"><?php echo e($kpis['total_tickets']); ?></div>
        </div>
        <div class="kpi-card k-teal">
            <div class="kpi-icon"><i class="bx bx-folder-open"></i></div>
            <div class="kpi-label">Open</div>
            <div class="kpi-value"><?php echo e($kpis['open_tickets']); ?></div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-icon"><i class="bx bx-check-circle"></i></div>
            <div class="kpi-label">Closed</div>
            <div class="kpi-value"><?php echo e($kpis['closed_tickets']); ?></div>
        </div>
        <div class="kpi-card k-red">
            <div class="kpi-icon"><i class="bx bx-error-circle"></i></div>
            <div class="kpi-label">High Priority</div>
            <div class="kpi-value"><?php echo e($kpis['high_priority']); ?></div>
        </div>
        <div class="kpi-card k-warn">
            <div class="kpi-icon"><i class="bx bx-minus-circle"></i></div>
            <div class="kpi-label">Medium</div>
            <div class="kpi-value"><?php echo e($kpis['medium_priority']); ?></div>
        </div>
        <div class="kpi-card k-gold">
            <div class="kpi-icon"><i class="bx bx-down-arrow-alt"></i></div>
            <div class="kpi-label">Low</div>
            <div class="kpi-value"><?php echo e($kpis['low_priority']); ?></div>
        </div>
    </div>

    
    <form method="GET" id="filterForm">
        <div class="pipe-filter-bar" style="margin-bottom:1.2rem">
            <select name="section_id" class="pipe-pill crm-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Sections</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e(request('section_id') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <select name="status" class="pipe-pill crm-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Status</option>
                <option value="OPEN" <?php echo e(request('status') == 'OPEN' ? 'selected' : ''); ?>>Open</option>
                <option value="IN PROGRESS" <?php echo e(request('status') == 'IN PROGRESS' ? 'selected' : ''); ?>>In Progress</option>
                <option value="ON HOLD" <?php echo e(request('status') == 'ON HOLD' ? 'selected' : ''); ?>>On Hold</option>
                <option value="RESOLVED" <?php echo e(request('status') == 'RESOLVED' ? 'selected' : ''); ?>>Resolved</option>
                <option value="CLOSED" <?php echo e(request('status') == 'CLOSED' ? 'selected' : ''); ?>>Closed</option>
            </select>
            <input type="text" name="search" class="pipe-pill" placeholder="🔍 Search code or subject…" value="<?php echo e(request('search')); ?>" style="min-width:200px">
            <button type="submit" class="act-btn a-primary" style="margin-left:auto"><i class="bx bx-filter-alt"></i> Filter</button>
            <a href="<?php echo e(route('pabs.tickets.create')); ?>" class="act-btn a-success"><i class="bx bx-plus"></i> New Ticket</a>
        </div>
    </form>

    
    <div class="sec-card">
        <div class="sec-hdr"><i class="bx bx-support" style="color:#b8860b"></i> Support Tickets</div>
        <div class="sec-body" style="padding:0;overflow-x:auto">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th style="width:11%">Code</th>
                        <th style="width:22%">Subject</th>
                        <th style="width:10%">Section</th>
                        <th style="width:10%">Status</th>
                        <th style="width:9%">Approval</th>
                        <th style="width:9%">Priority</th>
                        <th style="width:12%">Created By</th>
                        <th style="width:10%">Assigned</th>
                        <th style="width:7%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="tk-code"><?php echo e($ticket->ticket_code); ?></span></td>
                            <td>
                                <a href="<?php echo e(route('pabs.tickets.show', $ticket)); ?>" class="tk-subject">
                                    <?php echo e(Str::limit($ticket->subject, 30)); ?>

                                </a>
                            </td>
                            <td><span class="sec-tag"><?php echo e($sections[$ticket->section_id] ?? 'N/A'); ?></span></td>
                            <td>
                                <?php $slug = Str::slug($ticket->status) ?>
                                <span class="tk-status tk-<?php echo e($slug); ?>"><?php echo e($ticket->status); ?></span>
                            </td>
                            <td>
                                <?php
                                    $apCls = match($ticket->approval_status) {
                                        'APPROVED' => 'app-approved',
                                        'REJECTED' => 'app-rejected',
                                        default    => 'app-pending',
                                    };
                                ?>
                                <span class="app-badge <?php echo e($apCls); ?>"><?php echo e($ticket->approval_status); ?></span>
                            </td>
                            <td><span class="pr-<?php echo e(Str::lower($ticket->priority)); ?>"><?php echo e($ticket->priority); ?></span></td>
                            <td><span class="tk-meta"><?php echo e($ticket->creator->name); ?></span></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignee): ?>
                                    <span class="tk-meta"><?php echo e($ticket->assignee->name); ?></span>
                                <?php else: ?>
                                    <span class="tk-meta" style="opacity:.5">Unassigned</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('pabs.tickets.show', $ticket)); ?>" class="act-btn a-primary" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:2.5rem;color:#9ca3af">
                                <i class="bx bx-inbox" style="font-size:2rem;display:block;margin-bottom:.4rem"></i>
                                No tickets found.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
        <div style="margin-top:1rem"><?php echo e($tickets->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
<script>
$(function(){
    $('.crm-select').select2({minimumResultsForSearch:10,width:'style'}).on('change',function(){
        document.getElementById('filterForm').submit();
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pabs/tickets/index.blade.php ENDPATH**/ ?>