<?php $__env->startSection('title'); ?>
    EPMS Projects
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .project-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2);
        }
        .project-header {
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            color: white;
            padding: 20px;
        }
        .health-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .health-green { background: #28a745; color: white; }
        .health-yellow { background: #ffc107; color: #000; }
        .health-red { background: #dc3545; color: white; }
        .progress-ring {
            width: 80px;
            height: 80px;
        }
        .stats-widget {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            EPMS
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Projects
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="stats-widget">
                <h5 class="text-muted mb-2">Total Projects</h5>
                <h2 class="mb-0"><?php echo e($projects->count()); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-widget">
                <h5 class="text-muted mb-2">Active Projects</h5>
                <h2 class="mb-0 text-success"><?php echo e($projects->where('status', 'in-progress')->count()); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-widget">
                <h5 class="text-muted mb-2">On-Hold</h5>
                <h2 class="mb-0 text-warning"><?php echo e($projects->where('status', 'on-hold')->count()); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-widget">
                <h5 class="text-muted mb-2">Completed</h5>
                <h2 class="mb-0 text-info"><?php echo e($projects->where('status', 'completed')->count()); ?></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bx bx-briefcase-alt text-warning me-2"></i>All Projects
                        </h4>
                        <a href="<?php echo e(route('epms.create')); ?>" class="btn btn-success waves-effect waves-light">
                            <i class="fas fa-plus me-1"></i> New Project
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="projectsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Project Name</th>
                                    <th>Client</th>
                                    <th>Region</th>
                                    <th>Contract Value</th>
                                    <th>Progress</th>
                                    <th>Health</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('epms.show', $project)); ?>" class="fw-semibold text-dark">
                                                <?php echo e($project->name); ?>

                                            </a>
                                            <br>
                                            <small class="text-muted">PM: <?php echo e($project->projectManager->name ?? 'Unassigned'); ?></small>
                                        </td>
                                        <td>
                                            <?php echo e($project->client_name); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->region === 'US'): ?>
                                                <span class="badge bg-primary ms-1">🇺🇸</span>
                                            <?php else: ?>
                                                <span class="badge bg-success ms-1">🇵🇰</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><?php echo e($project->region); ?></td>
                                        <td>
                                            <strong><?php echo e($project->currency); ?> <?php echo e(number_format($project->contract_value, 2)); ?></strong>
                                            <br>
                                            <small class="text-muted">Margin: <?php echo e(number_format($project->margin_percentage, 1)); ?>%</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?php echo e($project->progress_percentage); ?>%">
                                                    </div>
                                                </div>
                                                <span class="text-muted small"><?php echo e(number_format($project->progress_percentage, 0)); ?>%</span>
                                            </div>
                                            <small class="text-muted"><?php echo e($project->completed_tasks); ?>/<?php echo e($project->total_tasks); ?> tasks</small>
                                        </td>
                                        <td>
                                            <span class="health-badge health-<?php echo e($project->health_score); ?>">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->health_score === 'green'): ?> ✓ On Track
                                                <?php elseif($project->health_score === 'yellow'): ?> ⚠ At Risk
                                                <?php else: ?> ✗ Delayed
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo e($project->deadline->format('M d, Y')); ?>

                                            <br>
                                            <small class="text-muted">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($project->days_remaining > 0): ?>
                                                    <?php echo e($project->days_remaining); ?> days left
                                                <?php elseif($project->days_remaining < 0): ?>
                                                    <span class="text-danger"><?php echo e(abs($project->days_remaining)); ?> days overdue</span>
                                                <?php else: ?>
                                                    <span class="text-warning">Due today!</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                                $statusColors = [
                                                    'planning' => 'secondary',
                                                    'in-progress' => 'primary',
                                                    'on-hold' => 'warning',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                            ?>
                                            <span class="badge bg-<?php echo e($statusColors[$project->status] ?? 'secondary'); ?>">
                                                <?php echo e(ucfirst($project->status)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('epms.show', $project)); ?>" 
                                                   class="btn btn-sm btn-primary" title="View Dashboard">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('epms.edit', $project)); ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('epms.destroy', $project)); ?>" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bx bx-briefcase-alt display-4 text-muted"></i>
                                            <p class="text-muted mt-3">No projects found. Create your first project to get started!</p>
                                            <a href="<?php echo e(route('epms.create')); ?>" class="btn btn-success">
                                                <i class="fas fa-plus me-1"></i> Create Project
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('/assets/libs/datatables/datatables.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('#projectsTable').DataTable({
                order: [[6, 'asc']], // Sort by deadline
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search projects..."
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/epms/index.blade.php ENDPATH**/ ?>