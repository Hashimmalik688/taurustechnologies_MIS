<?php $__env->startSection('title', 'PABS - Support Tickets'); ?>

<?php $__env->startSection('css'); ?>
<style>
    .border-left-primary { border-left: 4px solid #0d6efd; }
    .border-left-info { border-left: 4px solid #0dcaf0; }
    .border-left-success { border-left: 4px solid #198754; }
    .border-left-danger { border-left: 4px solid #dc3545; }
    .border-left-warning { border-left: 4px solid #ffc107; }

    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
    }

    .kpi-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 160px;
    }

    .kpi-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: inherit !important;
    }

    .kpi-icon i {
        color: inherit !important;
    }

    .kpi-icon.text-primary,
    .kpi-icon.text-primary i {
        color: #0d6efd !important;
    }

    .kpi-icon.text-info,
    .kpi-icon.text-info i {
        color: #0dcaf0 !important;
    }

    .kpi-icon.text-success,
    .kpi-icon.text-success i {
        color: #198754 !important;
    }

    .kpi-icon.text-danger,
    .kpi-icon.text-danger i {
        color: #dc3545 !important;
    }

    .kpi-icon.text-warning,
    .kpi-icon.text-warning i {
        color: #ffc107 !important;
    }

    .kpi-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 8px;
        text-align: center;
    }

    .kpi-value {
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-open { background-color: #cfe2ff; color: #084298; }
    .status-in-progress { background-color: #d1ecf1; color: #055160; }
    .status-on-hold { background-color: #fff3cd; color: #664d03; }
    .status-resolved { background-color: #d1e7dd; color: #0f5132; }
    .status-closed { background-color: #e2e3e5; color: #41464b; }

    .priority-high { color: #dc3545; font-weight: 600; }
    .priority-medium { color: #fd7e14; font-weight: 600; }
    .priority-low { color: #28a745; font-weight: 600; }

    .section-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.2rem;
        font-size: 0.75rem;
        background-color: #f0f0f0;
        color: #333;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('title'); ?> PABS - Support Tickets <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Alert Messages -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- KPI Cards -->
    <div class="row mb-4 g-3">
        <!-- Total Tickets -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-primary">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-primary">
                        <i class="bx bx-list-check"></i>
                    </div>
                    <p class="kpi-label">Total Tickets</p>
                    <p class="kpi-value text-primary"><?php echo e($kpis['total_tickets']); ?></p>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-info">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-info">
                        <i class="bx bx-folder-open"></i>
                    </div>
                    <p class="kpi-label">Open Tickets</p>
                    <p class="kpi-value text-info"><?php echo e($kpis['open_tickets']); ?></p>
                </div>
            </div>
        </div>

        <!-- Closed Tickets -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-success">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <p class="kpi-label">Closed Tickets</p>
                    <p class="kpi-value text-success"><?php echo e($kpis['closed_tickets']); ?></p>
                </div>
            </div>
        </div>

        <!-- High Priority -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-danger">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-danger">
                        <i class="bx bx-up-arrow-alt"></i>
                    </div>
                    <p class="kpi-label">High Priority</p>
                    <p class="kpi-value text-danger"><?php echo e($kpis['high_priority']); ?></p>
                </div>
            </div>
        </div>

        <!-- Medium Priority -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-warning">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-warning">
                        <i class="bx bx-minus-circle"></i>
                    </div>
                    <p class="kpi-label">Medium Priority</p>
                    <p class="kpi-value text-warning"><?php echo e($kpis['medium_priority']); ?></p>
                </div>
            </div>
        </div>

        <!-- Low Priority -->
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card border-left-success">
                <div class="card-body kpi-card">
                    <div class="kpi-icon text-success">
                        <i class="bx bx-down-arrow-alt"></i>
                    </div>
                    <p class="kpi-label">Low Priority</p>
                    <p class="kpi-value text-success"><?php echo e($kpis['low_priority']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Create Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Support Tickets</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?php echo e(route('pabs.tickets.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> New Ticket
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="section_id" class="form-select form-select-sm">
                        <option value="">All Sections</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>" <?php echo e(request('section_id') == $id ? 'selected' : ''); ?>>
                                <?php echo e($name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="OPEN" <?php echo e(request('status') == 'OPEN' ? 'selected' : ''); ?>>Open</option>
                        <option value="IN PROGRESS" <?php echo e(request('status') == 'IN PROGRESS' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="ON HOLD" <?php echo e(request('status') == 'ON HOLD' ? 'selected' : ''); ?>>On Hold</option>
                        <option value="RESOLVED" <?php echo e(request('status') == 'RESOLVED' ? 'selected' : ''); ?>>Resolved</option>
                        <option value="CLOSED" <?php echo e(request('status') == 'CLOSED' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search code or subject" value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="12%">Code</th>
                        <th width="22%">Subject</th>
                        <th width="10%">Section</th>
                        <th width="10%">Status</th>
                        <th width="10%">Approval</th>
                        <th width="10%">Priority</th>
                        <th width="13%">Created By</th>
                        <th width="8%">Assigned</th>
                        <th width="5%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($ticket->ticket_code); ?></strong>
                            </td>
                            <td>
                                <a href="<?php echo e(route('pabs.tickets.show', $ticket)); ?>" class="text-decoration-none">
                                    <?php echo e(Str::limit($ticket->subject, 30)); ?>

                                </a>
                            </td>
                            <td>
                                <span class="section-badge">
                                    <?php echo e($sections[$ticket->section_id] ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo e(Str::lower($ticket->status)); ?>">
                                    <?php echo e($ticket->status); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($ticket->approval_status === 'APPROVED' ? 'success' : ($ticket->approval_status === 'REJECTED' ? 'danger' : 'warning')); ?>">
                                    <?php echo e($ticket->approval_status); ?>

                                </span>
                            </td>
                            <td>
                                <span class="priority-<?php echo e(Str::lower($ticket->priority)); ?>">
                                    <?php echo e($ticket->priority); ?>

                                </span>
                            </td>
                            <td>
                                <small><?php echo e($ticket->creator->name); ?></small>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignee): ?>
                                    <small><?php echo e($ticket->assignee->name); ?></small>
                                <?php else: ?>
                                    <small class="text-muted">Unassigned</small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('pabs.tickets.show', $ticket)); ?>" class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="bx bx-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <span class="text-muted">No tickets found.</span>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
        <div class="mt-3">
            <?php echo e($tickets->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pabs/tickets/index.blade.php ENDPATH**/ ?>