<?php $__env->startSection('title', 'Salary Components - Basic & Bonus Sheets'); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<style>
    .component-badge-basic {
        background: #0d6efd;
        color: white;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .component-badge-bonus {
        background: #198754;
        color: white;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .status-badge {
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .status-calculated { background: #ffc107; color: black; }
    .status-approved { background: #17a2b8; color: white; }
    .status-paid { background: #28a745; color: white; }
    .status-draft { background: #6c757d; color: white; }
    .payment-date {
        background: #e7f3ff;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .amount-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .amount-row .label {
        font-weight: 500;
        color: #6b7280;
    }
    .amount-row .value {
        font-weight: 600;
        color: #111827;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1" style="color: #d4af37;">
                        <i class="bx bx-wallet me-2"></i>
                        Salary Components (Basic & Bonus Sheets)
                    </h1>
                    <p class="text-muted">Two-payment salary structure: Basic on 10th, Bonus on 20th</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('salary.components')); ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="employee" class="form-select">
                        <option value="">-- All Employees --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php if(request('employee') == $emp->id): ?> selected <?php endif; ?>><?php echo e($emp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">-- All Months --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php if(request('month') == $i): ?> selected <?php endif; ?>><?php echo e(Carbon\Carbon::create()->month($i)->format('F')); ?></option>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <option value="">-- All Years --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = now()->year; $i >= 2020; $i--): ?>
                            <option value="<?php echo e($i); ?>" <?php if(request('year') == $i): ?> selected <?php endif; ?>><?php echo e($i); ?></option>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Component</label>
                    <select name="component_type" class="form-select">
                        <option value="">-- All Components --</option>
                        <option value="basic" <?php if(request('component_type') == 'basic'): ?> selected <?php endif; ?>>Basic Salary</option>
                        <option value="bonus" <?php if(request('component_type') == 'bonus'): ?> selected <?php endif; ?>>Bonus Salary</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- All Status --</option>
                        <option value="calculated" <?php if(request('status') == 'calculated'): ?> selected <?php endif; ?>>Calculated</option>
                        <option value="approved" <?php if(request('status') == 'approved'): ?> selected <?php endif; ?>>Approved</option>
                        <option value="paid" <?php if(request('status') == 'paid'): ?> selected <?php endif; ?>>Paid</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Components Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bx bx-table me-2" style="color: #d4af37;"></i> Salary Components</h5>
        </div>
        <div class="card-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($components->isEmpty()): ?>
                <div class="text-center py-5" style="opacity: 0.6;">
                    <i class="bx bx-inbox" style="font-size: 4rem; color: #d4af37;"></i>
                    <h5 class="mt-3 text-muted">No Salary Components Found</h5>
                    <p class="text-muted">Calculate salaries to generate basic and bonus salary sheets</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Type</th>
                                <th>Payment Date</th>
                                <th>Calculated</th>
                                <th>Approved</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($component->user->name); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo e($component->user->email); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo e($component->month_name); ?></strong><br>
                                    <small class="text-muted"><?php echo e($component->salary_year); ?></small>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($component->component_type === 'basic'): ?>
                                        <span class="component-badge-basic">
                                            <i class="bx bx-money me-1"></i>Basic Salary
                                        </span>
                                    <?php else: ?>
                                        <span class="component-badge-bonus">
                                            <i class="bx bx-gift me-1"></i>Bonus Salary
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <span class="payment-date">
                                        <i class="bx bx-calendar me-1"></i><?php echo e($component->payment_date->format('d M Y')); ?>

                                    </span>
                                </td>
                                <td>
                                    <strong>Rs<?php echo e(number_format($component->calculated_amount, 2)); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($component->deductions > 0): ?>
                                        <br><small class="text-danger">- Rs<?php echo e(number_format($component->deductions, 2)); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($component->approved_amount): ?>
                                        <strong>Rs<?php echo e(number_format($component->approved_amount, 2)); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">--</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: #10b981;">Rs<?php echo e(number_format($component->net_amount, 2)); ?></strong>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo e($component->status); ?>">
                                        <?php echo e(ucfirst($component->status)); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($component->paid_at): ?>
                                        <br><small class="text-muted"><?php echo e($component->paid_at->format('d M Y')); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('salary.component.show', $component->id)); ?>" class="btn btn-sm btn-info" title="View Details">
                                            <i class="bx bx-eye"></i>
                                        </a>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($component->status === 'calculated'): ?>
                                            <form action="<?php echo e(route('salary.component.approve', $component->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this salary component?')">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($component->status === 'approved'): ?>
                                            <form action="<?php echo e(route('salary.component.mark-paid', $component->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-primary" title="Mark as Paid" onclick="return confirm('Mark this salary component as paid?')">
                                                    <i class="bx bx-money"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        
                                        <a href="<?php echo e(route('salary.component.payslip', $component->id)); ?>" class="btn btn-sm btn-warning" title="Download Payslip">
                                            <i class="bx bx-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bx bx-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="text-muted mt-2">No components match your filters</p>
                                </td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-4">
                    <?php echo e($components->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/salary/components.blade.php ENDPATH**/ ?>