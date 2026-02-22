<?php $__env->startSection('title', 'PABS - Projects'); ?>

<?php $__env->startSection('css'); ?>
<style>
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-draft { background-color: var(--bs-surface-200); color: var(--bs-surface-600); }
    .status-scoping { background-color: var(--bs-surface-50); color: var(--bs-ui-info-dark); }
    .status-quoting { background-color: var(--bs-surface-100); color: var(--bs-ui-info-dark); }
    .status-pending-approval { background-color: var(--bs-surface-50); color: var(--bs-gold-dark); }
    .status-budget-allocated { background-color: var(--bs-surface-50); color: var(--bs-ui-success-dark); }
    .status-in-progress { background-color: var(--bs-surface-50); color: var(--bs-ui-info-dark); }
    .status-completed { background-color: var(--bs-surface-50); color: var(--bs-ui-success-dark); }
    .status-archived { background-color: var(--bs-surface-200); color: var(--bs-surface-600); }

    .section-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.2rem;
        font-size: 0.75rem;
        background-color: var(--bs-print-bg-alt);
        color: var(--bs-surface-700);
    }

    .priority-high { color: var(--bs-status-absent); font-weight: 600; }
    .priority-medium { color: var(--bs-status-late); font-weight: 600; }
    .priority-low { color: var(--bs-status-present); font-weight: 600; }

    .variance-flag {
        display: inline-block;
        background-color: var(--bs-status-absent);
        color: var(--bs-white);
        padding: 0.25rem 0.5rem;
        border-radius: 0.2rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('title'); ?> PABS - Project Authorization & Budget System <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Alert Messages -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Filter & Create Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Projects</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?php echo e(route('pabs.projects.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> New Project
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
                        <option value="DRAFT" <?php echo e(request('status') == 'DRAFT' ? 'selected' : ''); ?>>Draft</option>
                        <option value="SCOPING" <?php echo e(request('status') == 'SCOPING' ? 'selected' : ''); ?>>Scoping</option>
                        <option value="QUOTING" <?php echo e(request('status') == 'QUOTING' ? 'selected' : ''); ?>>Quoting</option>
                        <option value="PENDING APPROVAL" <?php echo e(request('status') == 'PENDING APPROVAL' ? 'selected' : ''); ?>>Pending Approval</option>
                        <option value="BUDGET ALLOCATED" <?php echo e(request('status') == 'BUDGET ALLOCATED' ? 'selected' : ''); ?>>Budget Allocated</option>
                        <option value="IN PROGRESS" <?php echo e(request('status') == 'IN PROGRESS' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="COMPLETED" <?php echo e(request('status') == 'COMPLETED' ? 'selected' : ''); ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search code or name" value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="12%">Code</th>
                        <th width="18%">Project Name</th>
                        <th width="12%">Section</th>
                        <th width="10%">Status</th>
                        <th width="10%">Priority</th>
                        <th width="12%">Approved Budget</th>
                        <th width="8%">Variance</th>
                        <th width="10%">Created By</th>
                        <th width="8%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($project->project_code); ?></strong>
                            </td>
                            <td>
                                <a href="<?php echo e(route('pabs.projects.show', $project)); ?>" class="text-decoration-none">
                                    <?php echo e(Str::limit($project->project_name, 25)); ?>

                                </a>
                            </td>
                            <td>
                                <span class="section-badge">
                                    <?php echo e($sections[$project->section_id] ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo e(Str::lower(str_replace(' ', '-', $project->status))); ?>">
                                    <?php echo e($project->status); ?>

                                </span>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->priority): ?>
                                    <span class="priority-<?php echo e(Str::lower($project->priority)); ?>">
                                        <?php echo e($project->priority); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->approved_budget): ?>
                                    $<?php echo e(number_format($project->approved_budget, 2)); ?>

                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->variance_flagged): ?>
                                    <span class="variance-flag">FLAGGED</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <small><?php echo e($project->creator->name ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('pabs.projects.show', $project)); ?>" class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="bx bx-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <span class="text-muted">No projects found.</span>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($projects->hasPages()): ?>
        <div class="mt-3">
            <?php echo e($projects->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Auto-submit filter form on select change
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pabs/projects/index.blade.php ENDPATH**/ ?>