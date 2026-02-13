<?php $__env->startSection('title', 'Salary Details - ' . $salaryRecord->user->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="<?php echo e(route('salary.records')); ?>" class="btn btn-outline-secondary me-3">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h3 class="mb-0">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>
                                    Salary Details
                                </h3>
                                <small class="text-muted">
                                    <?php echo e($salaryRecord->user->name); ?> - <?php echo e($salaryRecord->month_name); ?>

                                    <?php echo e($salaryRecord->salary_year); ?>

                                </small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->status == 'calculated'): ?>
                                <button class="btn btn-info" onclick="approveRecord(<?php echo e($salaryRecord->id); ?>)">
                                    <i class="fas fa-check me-1"></i>
                                    Approve
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->status == 'approved'): ?>
                                <button class="btn btn-success" onclick="markAsPaid(<?php echo e($salaryRecord->id); ?>)">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Mark as Paid
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <button class="btn btn-primary" onclick="downloadPayslip(<?php echo e($salaryRecord->id); ?>)">
                                <i class="fas fa-file-pdf me-1"></i>
                                Download Payslip
                            </button>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="row">
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    Employee Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="<?php echo e($salaryRecord->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($salaryRecord->user->name)); ?>"
                                        alt="Avatar" class="rounded-circle mb-2" style="width: 80px; height: 80px;">
                                    <h5 class="mb-1"><?php echo e($salaryRecord->user->name); ?></h5>
                                    <p class="text-muted mb-0"><?php echo e($salaryRecord->user->email); ?></p>
                                </div>

                                <hr>

                                <div class="row text-center">
                                    <div class="col-12 mb-2">
                                        <strong>Current Settings</strong>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Basic Salary</small>
                                            <strong
                                                class="text-primary">$<?php echo e(number_format($salaryRecord->user->basic_salary, 0)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Target Sales</small>
                                            <strong class="text-info"><?php echo e($salaryRecord->user->target_sales); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Bonus/Sale</small>
                                            <strong
                                                class="text-success">$<?php echo e(number_format($salaryRecord->user->bonus_per_extra_sale, 0)); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Status & Timeline
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Current Status</label>
                                    <div>
                                        <span
                                            class="badge bg-<?php echo e($salaryRecord->status == 'paid' ? 'success' : ($salaryRecord->status == 'approved' ? 'info' : ($salaryRecord->status == 'calculated' ? 'warning' : 'secondary'))); ?> fs-6">
                                            <?php echo e(ucfirst($salaryRecord->status)); ?>

                                        </span>
                                    </div>
                                </div>

                                <div class="timeline">
                                    <div class="timeline-item <?php echo e($salaryRecord->calculated_at ? 'completed' : ''); ?>">
                                        <div class="timeline-marker bg-warning"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Calculated</h6>
                                            <small class="text-muted">
                                                <?php echo e($salaryRecord->calculated_at ? $salaryRecord->calculated_at->format('M d, Y H:i') : 'Pending'); ?>

                                            </small>
                                        </div>
                                    </div>

                                    <div class="timeline-item <?php echo e($salaryRecord->approved_at ? 'completed' : ''); ?>">
                                        <div
                                            class="timeline-marker <?php echo e($salaryRecord->approved_at ? 'bg-info' : 'bg-light'); ?>">
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Approved</h6>
                                            <small class="text-muted">
                                                <?php echo e($salaryRecord->approved_at ? $salaryRecord->approved_at->format('M d, Y H:i') : 'Pending'); ?>

                                            </small>
                                        </div>
                                    </div>

                                    <div class="timeline-item <?php echo e($salaryRecord->paid_at ? 'completed' : ''); ?>">
                                        <div
                                            class="timeline-marker <?php echo e($salaryRecord->paid_at ? 'bg-success' : 'bg-light'); ?>">
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Paid</h6>
                                            <small class="text-muted">
                                                <?php echo e($salaryRecord->paid_at ? $salaryRecord->paid_at->format('M d, Y H:i') : 'Pending'); ?>

                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-8">
                        
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>
                                    Salary Breakdown
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-primary">Basic Salary</h6>
                                                <h4 class="text-primary mb-0">
                                                    $<?php echo e(number_format($salaryRecord->basic_salary, 2)); ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-info">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-info">Sales Performance</h6>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <span
                                                        class="badge bg-<?php echo e($salaryRecord->actual_sales >= $salaryRecord->target_sales ? 'success' : 'warning'); ?> me-2">
                                                        <?php echo e($salaryRecord->actual_sales); ?>

                                                    </span>
                                                    <span class="text-muted">of</span>
                                                    <span
                                                        class="badge bg-info ms-2"><?php echo e($salaryRecord->target_sales); ?></span>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->extra_sales > 0): ?>
                                                    <small class="text-success d-block mt-1">
                                                        <i class="fas fa-arrow-up"></i> +<?php echo e($salaryRecord->extra_sales); ?>

                                                        extra sales
                                                    </small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-success">Bonus Earned</h6>
                                                <h4 class="text-success mb-0">
                                                    $<?php echo e(number_format($salaryRecord->total_bonus, 2)); ?></h4>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->extra_sales > 0): ?>
                                                    <small class="text-muted d-block">
                                                        <?php echo e($salaryRecord->extra_sales); ?> ×
                                                        $<?php echo e(number_format($salaryRecord->bonus_per_extra_sale, 2)); ?>

                                                    </small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-dark">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Gross Salary</h6>
                                                <h4 class="mb-0">$<?php echo e(number_format($salaryRecord->gross_salary, 2)); ?>

                                                </h4>
                                                <small class="text-muted">Basic + Bonus</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card bg-dark text-white">
                                            <div class="card-body text-center">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <h6 class="mb-1">Gross Salary</h6>
                                                        <h5 class="mb-0">
                                                            $<?php echo e(number_format($salaryRecord->gross_salary, 2)); ?></h5>
                                                    </div>
                                                    <div class="col-md-1 text-center">
                                                        <i class="fas fa-minus fa-2x"></i>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <h6 class="mb-1 text-danger">Deductions</h6>
                                                        <h5 class="mb-0 text-danger">
                                                            $<?php echo e(number_format($salaryRecord->total_deductions, 2)); ?></h5>
                                                    </div>
                                                    <div class="col-md-1 text-center">
                                                        <i class="fas fa-equals fa-2x"></i>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <h6 class="mb-1 text-success">Net Salary</h6>
                                                        <h4 class="mb-0 text-success">
                                                            $<?php echo e(number_format($salaryRecord->net_salary, 2)); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Attendance Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-2">
                                        <div class="card border-secondary">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-1">Working Days</h6>
                                                <h4 class="mb-0"><?php echo e($salaryRecord->working_days); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card border-success">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-1">Present Days</h6>
                                                <h4 class="mb-0 text-success"><?php echo e($salaryRecord->present_days); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card border-danger">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-1">Full Leave</h6>
                                                <h4 class="mb-0 text-danger"><?php echo e($salaryRecord->leave_days); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card border-warning">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-1">Half Days</h6>
                                                <h4 class="mb-0 text-warning"><?php echo e($salaryRecord->half_days ?? 0); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card border-warning">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-1">Late Days</h6>
                                                <h4 class="mb-0 text-warning"><?php echo e($salaryRecord->late_days ?? 0); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card border-primary">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-1">Daily Salary</h6>
                                                <h6 class="mb-0 text-primary">$<?php echo e(number_format($salaryRecord->daily_salary, 2)); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->attendance_bonus > 0 || $salaryRecord->attendance_deduction < 0): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-info mb-0">
                                                <strong><i class="fas fa-info-circle me-2"></i>Attendance Impact:</strong>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->attendance_bonus > 0): ?>
                                                    <span class="text-success">+$<?php echo e(number_format($salaryRecord->attendance_bonus, 2)); ?> Punctuality Bonus</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->attendance_deduction < 0): ?>
                                                    <span class="text-danger">-$<?php echo e(number_format(abs($salaryRecord->attendance_deduction), 2)); ?> Deductions</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="card mb-4">
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-minus-circle me-2"></i>
                                    Deductions
                                </h5>
                                <button class="btn btn-sm btn-dark" onclick="addDeduction(<?php echo e($salaryRecord->id); ?>)">
                                    <i class="fas fa-plus me-1"></i>
                                    Add Deduction
                                </button>
                            </div>
                            <div class="card-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($salaryRecord->deductions->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>Calculated</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $salaryRecord->deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <span
                                                                class="badge bg-secondary"><?php echo e(ucfirst($deduction->type)); ?></span>
                                                        </td>
                                                        <td><?php echo e($deduction->description); ?></td>
                                                        <td>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($deduction->is_percentage): ?>
                                                                <?php echo e($deduction->amount); ?>%
                                                            <?php else: ?>
                                                                $<?php echo e(number_format($deduction->amount, 2)); ?>

                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </td>
                                                        <td class="text-danger fw-bold">
                                                            $<?php echo e(number_format($deduction->calculated_amount, 2)); ?>

                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                onclick="removeDeduction(<?php echo e($deduction->id); ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </tbody>
                                            <tfoot class="table-dark">
                                                <tr>
                                                    <th colspan="3">Total Deductions</th>
                                                    <th class="text-danger">
                                                        $<?php echo e(number_format($salaryRecord->total_deductions, 2)); ?></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No deductions added yet.</p>
                                        <button class="btn btn-sm btn-outline-primary mt-2"
                                            onclick="addDeduction(<?php echo e($salaryRecord->id); ?>)">
                                            Add First Deduction
                                        </button>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($salesDetails) && $salesDetails->count() > 0): ?>
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Sales Details - <?php echo e($salaryRecord->month_name); ?> <?php echo e($salaryRecord->salary_year); ?>

                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Lead/Client</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $salesDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A'); ?>

                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong><?php echo e($sale->name ?? ($sale->company_name ?? 'N/A')); ?></strong>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->email): ?>
                                                                    <br><small
                                                                        class="text-muted"><?php echo e($sale->email); ?></small>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->deal_amount): ?>
                                                                <span
                                                                    class="text-success fw-bold">$<?php echo e(number_format($sale->deal_amount, 2)); ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">N/A</span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">Closed</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3 p-3 bg-light rounded">
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <strong class="text-info"><?php echo e($salesDetails->count()); ?></strong>
                                                <div class="small text-muted">Total Sales</div>
                                            </div>
                                            <div class="col-md-4">
                                                <strong
                                                    class="text-success">$<?php echo e(number_format($salesDetails->sum('deal_amount'), 2)); ?></strong>
                                                <div class="small text-muted">Total Value</div>
                                            </div>
                                            <div class="col-md-4">
                                                <strong
                                                    class="text-primary">$<?php echo e(number_format($salesDetails->avg('deal_amount'), 2)); ?></strong>
                                                <div class="small text-muted">Average Deal</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addDeductionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDeductionForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" id="deduction_salary_record_id" name="salary_record_id"
                            value="<?php echo e($salaryRecord->id); ?>">

                        <div class="mb-3">
                            <label for="deduction_type" class="form-label">Type</label>
                            <select id="deduction_type" name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="tax">Income Tax</option>
                                <option value="insurance">Insurance</option>
                                <option value="loan">Loan Deduction</option>
                                <option value="absence">Absence/Late</option>
                                <option value="advance">Advance Payment</option>
                                <option value="provident_fund">Provident Fund</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="deduction_description" class="form-label">Description</label>
                            <input type="text" id="deduction_description" name="description" class="form-control"
                                placeholder="e.g., Income tax for <?php echo e($salaryRecord->month_name); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <label for="deduction_amount" class="form-label">Amount</label>
                                <input type="number" id="deduction_amount" name="amount" class="form-control"
                                    step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_percentage"
                                        name="is_percentage">
                                    <label class="form-check-label" for="is_percentage">
                                        Percentage
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deduction_notes" class="form-label">Notes (Optional)</label>
                            <textarea id="deduction_notes" name="notes" class="form-control" rows="2"
                                placeholder="Additional notes about this deduction..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Preview:</strong> This will deduct <span id="preview_amount">$0.00</span> from the
                            gross salary.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Deduction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: -22px;
            top: 20px;
            height: 20px;
            width: 2px;
            background-color: #dee2e6;
        }

        .timeline-marker {
            position: absolute;
            left: -26px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .timeline-item.completed .timeline-marker {
            box-shadow: 0 0 0 2px #fff;
        }
    </style>

    <script>
        function addDeduction(recordId) {
            document.getElementById('addDeductionForm').reset();
            document.getElementById('deduction_salary_record_id').value = recordId;
            new bootstrap.Modal(document.getElementById('addDeductionModal')).show();
        }

        function removeDeduction(deductionId) {
            if (confirm('Are you sure you want to remove this deduction?')) {
                fetch(`/salary/deductions/${deductionId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error removing deduction');
                        }
                    });
            }
        }

        function approveRecord(recordId) {
            if (confirm('Are you sure you want to approve this salary record?')) {
                fetch(`/salary/records/${recordId}/approve`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error approving record');
                        }
                    });
            }
        }

        function markAsPaid(recordId) {
            if (confirm('Are you sure you want to mark this salary as paid?')) {
                fetch(`/salary/records/${recordId}/mark-paid`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error marking as paid');
                        }
                    });
            }
        }

        function downloadPayslip(recordId) {
            window.open(`/salary/records/${recordId}/payslip`, '_blank');
        }

        // Handle deduction form submission
        document.getElementById('addDeductionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const recordId = formData.get('salary_record_id');

            fetch(`/salary/records/${recordId}/deductions`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('addDeductionModal')).hide();
                        location.reload();
                    } else {
                        alert('Error adding deduction');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding deduction');
                });
        });

        // Preview calculation
        function updatePreview() {
            const amount = parseFloat(document.getElementById('deduction_amount').value) || 0;
            const isPercentage = document.getElementById('is_percentage').checked;
            const basicSalary = <?php echo e($salaryRecord->basic_salary); ?>;

            let calculatedAmount;
            if (isPercentage) {
                calculatedAmount = (basicSalary * amount) / 100;
            } else {
                calculatedAmount = amount;
            }

            document.getElementById('preview_amount').textContent = '$' + calculatedAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        document.getElementById('deduction_amount').addEventListener('input', updatePreview);
        document.getElementById('is_percentage').addEventListener('change', updatePreview);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/salary/show.blade.php ENDPATH**/ ?>