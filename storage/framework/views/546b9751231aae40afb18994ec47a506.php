<?php use \App\Support\Roles; ?>

<?php $__env->startSection('title', 'Payroll'); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.custom-select-datepicker-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .form-page-hdr p{margin:2px 0 0;font-size:.72rem;color:var(--bs-surface-500)}
    .crm-label{font-size:.72rem;font-weight:600;color:var(--bs-surface-500);margin-bottom:.25rem}
    .crm-label.required::after{content:" *";color:#c84646}
    .crm-input{border:1px solid rgba(0,0,0,.08);border-radius:22px;padding:.38rem .75rem;font-size:.75rem;width:100%;background:var(--bs-card-bg);color:var(--bs-body-color);transition:border-color .15s}
    .crm-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none}
    select.crm-input{appearance:none;-webkit-appearance:none;border-radius:22px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    textarea.crm-input{border-radius:.6rem}
    .modal .modal-content{border-radius:14px;border:none;overflow:hidden}
    .modal .modal-header-glass{background:linear-gradient(135deg,rgba(212,175,55,.13),rgba(212,175,55,.04));padding:.75rem 1.1rem;border-bottom:1px solid rgba(212,175,55,.12)}
    .modal .modal-header-glass .modal-title{font-size:.88rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
    .modal .modal-header-glass .modal-title i{color:#d4af37}
    .modal .modal-body{padding:1rem 1.1rem}
    .modal .modal-footer{padding:.6rem 1.1rem;border-top:1px solid rgba(0,0,0,.04)}
    .info-box{padding:.65rem .85rem;border-radius:10px;font-size:.72rem;border:1px solid rgba(14,165,233,.12);background:rgba(14,165,233,.04);color:var(--bs-body-color);margin-bottom:.75rem}
    .info-box i{color:#0ea5e9}
    .info-box strong{color:var(--bs-body-color)}
    .info-box code{background:rgba(212,175,55,.1);color:#d4af37;padding:1px 5px;border-radius:4px;font-size:.68rem}
    .amt{font-weight:700;font-size:.75rem;color:#d4af37}
    .amt-neg{color:#ef4444}
    .amt-pos{color:#10b981}
    .wd-input{font-size:1.1rem;color:#d4af37;text-align:center;font-weight:700;width:70px;border-radius:22px}
    .tbl-scroll{overflow-x:auto}
    .ex-tbl td,.ex-tbl th{white-space:nowrap;text-align:center}
    .ex-tbl td:nth-child(2){text-align:left}
    .brk-row{display:flex;gap:.5rem;flex-wrap:wrap;font-size:.68rem}
    .brk-row div{flex:1;min-width:140px;padding:.35rem .55rem;border-radius:6px;background:rgba(0,0,0,.02);border:1px solid rgba(0,0,0,.03)}
    @media print{.pipe-filter-bar,.kpi-row,.form-page-hdr .act-btn,.info-box,.modal{display:none!important}.ex-tbl{font-size:.7rem}}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:12px">
        <i class="bx bx-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:12px">
        <i class="bx bx-error me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:12px">
        <i class="bx bx-error me-1"></i> <strong>Errors:</strong>
        <ul class="mb-0 mt-1"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-receipt"></i> Payroll</h4>
            <p>Monthly salary overview — <?php echo e($periodDisplay); ?></p>
        </div>
        <div class="d-flex gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasAnyRole([Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR])): ?>
            <button class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#addManualEntryModal"><i class="bx bx-plus"></i> Manual Entry</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('payroll.print', ['month' => request('month', now()->month), 'year' => request('year', now()->year)])); ?>" class="act-btn a-success" target="_blank"><i class="bx bx-printer"></i> Print</a>
        </div>
    </div>

    
    <div class="kpi-row" style="grid-template-columns:repeat(auto-fill,minmax(170px,1fr))">
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Total Employees</div>
            <div class="kpi-val"><?php echo e($employees->count() + $manualEntries->count()); ?></div>
            <div style="font-size:.6rem;color:var(--bs-surface-500);margin-top:2px">Active: <?php echo e($employees->where('status', 'Active')->count()); ?> | Manual: <?php echo e($manualEntries->count()); ?></div>
        </div>
        <div class="kpi-card k-gold">
            <div class="kpi-lbl">Total Basic Salary</div>
            <div class="kpi-val">Rs <?php echo e(number_format($totalBasicSalary, 0)); ?></div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Qualified for Bonus</div>
            <div class="kpi-val"><?php echo e($qualifiedForBonus); ?></div>
        </div>
        <div class="kpi-card k-teal">
            <div class="kpi-lbl">Total Bonus</div>
            <div class="kpi-val">Rs <?php echo e(number_format($totalBonus, 0)); ?></div>
        </div>
    </div>

    
    <div class="info-box">
        <i class="bx bx-info-circle me-1"></i>
        <strong>📅 Period: <?php echo e($periodDisplay); ?></strong> — <?php echo e($startDate->format('M d, Y')); ?> to <?php echo e($endDate->format('M d, Y')); ?>

        &nbsp;|&nbsp; 💰 Payment ~<?php echo e($endDate->copy()->addDays(15)->format('M d, Y')); ?>

        &nbsp;|&nbsp; ⚡ Figures calculated live until saved. Saving creates a <code>salary_records</code> entry.
    </div>

    
    <div class="pipe-filter-bar mb-2">
        <form method="GET" action="<?php echo e(route('payroll.index')); ?>" class="d-flex flex-wrap align-items-end gap-2">
            <div style="min-width:110px">
                <label class="crm-label">Month</label>
                <select name="month" class="crm-input crm-select">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo e($i); ?>" <?php if($month == $i): ?> selected <?php endif; ?>><?php echo e(Carbon\Carbon::create()->month((int) $i)->format('F')); ?></option>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div style="min-width:80px">
                <label class="crm-label">Year</label>
                <select name="year" class="crm-input crm-select">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($y = now()->year; $y >= 2020; $y--): ?>
                    <option value="<?php echo e($y); ?>" <?php if($year == $y): ?> selected <?php endif; ?>><?php echo e($y); ?></option>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <button type="submit" class="pipe-pill" style="margin-top:auto"><i class="bx bx-filter-alt"></i> Filter</button>
        </form>
        <form method="POST" action="<?php echo e(route('payroll.working-days.update')); ?>" class="d-flex align-items-end gap-2 ms-auto">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="month" value="<?php echo e($month); ?>">
            <input type="hidden" name="year" value="<?php echo e($year); ?>">
            <div>
                <label class="crm-label">Working Days</label>
                <input type="number" name="working_days" class="crm-input wd-input" value="<?php echo e($totalWorkingDays); ?>" min="1" max="31" required <?php if(!auth()->user()->hasAnyRole([Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR])): ?> readonly <?php endif; ?>>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasAnyRole([Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR])): ?>
            <button type="submit" class="pipe-pill" style="margin-top:auto"><i class="bx bx-save"></i> Set</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </div>

    
    <div class="mb-2">
        <input type="text" id="payrollSearch" class="crm-input" placeholder="Search employees..." style="max-width:280px">
    </div>

    
    <div class="ex-card sec-card">
        <div class="sec-hdr"><i class="bx bx-table"></i> Payroll Details <small style="font-weight:400;color:var(--bs-surface-500);margin-left:.5rem"><?php echo e($startDate->format('Y-m-d')); ?> to <?php echo e($endDate->format('Y-m-d')); ?></small></div>
        <div class="sec-body p-0">
            <div class="tbl-scroll">
                <table class="ex-tbl w-100" id="payrollTbl">
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Employee</th>
                            <th>Join Date</th>
                            <th>Basic</th>
                            <th>Per Day</th>
                            <th>Punct.(P)</th>
                            <th>Total</th>
                            <th>Full</th>
                            <th>Half</th>
                            <th>Late</th>
                            <th>Qual.</th>
                            <th>Dock</th>
                            <th>Deductions</th>
                            <th>Net</th>
                            <th>Advance</th>
                            <th>Payable</th>
                            <th style="width:50px">Act</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalPunctuality = 0; $totalGross = 0; $totalDockAmount = 0;
                            $totalOtherDeductions = 0; $totalNetSalary = 0; $totalAdvance = 0; $totalPayable = 0;
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $basicSalary = $employee->basic_salary ?? 0;
                            $joinDate = $employee->userDetail && $employee->userDetail->join_date ? \Carbon\Carbon::parse($employee->userDetail->join_date)->format('d M Y') : 'N/A';
                            $joiningDate = $employee->userDetail && $employee->userDetail->join_date ? \Carbon\Carbon::parse($employee->userDetail->join_date) : null;
                            $workingDaysInPeriod = $totalWorkingDays;
                            $perDayWage = $basicSalary / max($workingDaysInPeriod, 1);
                            $attSummary = $attendanceSummaries[$employee->id] ?? ['full_days' => 0, 'half_days' => 0, 'late_days' => 0];
                            $fullDays = $attSummary['full_days'];
                            $halfDays = $attSummary['half_days'];
                            $lateDays = $attSummary['late_days'];
                            $eligibleDays = $fullDays + $halfDays + $lateDays;
                            if ($joiningDate) {
                                if ($joiningDate->gt($endDate)) { $eligibleDays = 0; }
                                elseif ($joiningDate->between($startDate, $endDate)) {
                                    $maxAllowedDays = 0; $current = $joiningDate->copy();
                                    while ($current->lte($endDate)) { if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) { $maxAllowedDays++; } $current->addDay(); }
                                    $eligibleDays = min($eligibleDays, $maxAllowedDays);
                                }
                            }
                            $earnedSalary = $eligibleDays * $perDayWage;
                            $isQualified = true;
                            if ($halfDays >= 2) { $isQualified = false; }
                            elseif ($lateDays >= 4) { $isQualified = false; }
                            else { if ($halfDays == 1) { $isQualified = ($fullDays >= $workingDaysInPeriod - 1); } elseif ($halfDays == 0) { $isQualified = ($fullDays >= $workingDaysInPeriod); } }
                            $punctualityBonus = 0;
                            if ($isQualified && $employee->punctuality_bonus && $employee->punctuality_bonus > 0) { $punctualityBonus = $employee->punctuality_bonus; }
                            if ($employee->override_punctuality_bonus && $employee->override_punctuality_bonus > 0) { $punctualityBonus = $employee->override_punctuality_bonus; $isQualified = true; }
                            $total = $earnedSalary + $punctualityBonus;
                            $bonus = 0;
                            if ($employee->is_sales_employee) {
                                $actualSales = \App\Models\Lead::where(function($q) use ($employee) { $q->where('managed_by', $employee->id)->orWhere('closer_name', $employee->name); })->where('status', 'accepted')->whereBetween('sale_date', [$startDate, $endDate])->count();
                                $target = $employee->target_sales ?? 20; $bonusPerSale = $employee->bonus_per_extra_sale ?? 0;
                                if ($actualSales > $target) { $bonus = ($actualSales - $target) * $bonusPerSale; }
                            }
                            $otherAllowances = $employee->other_allowances ?? 0;
                            $dockAmount = \App\Models\DockRecord::where('user_id', $employee->id)->whereDate('dock_date', '>=', $startDate->format('Y-m-d'))->whereDate('dock_date', '<=', $endDate->format('Y-m-d'))->where('status', 'active')->sum('amount');
                            $grossSalary = $total + $bonus + $otherAllowances;
                            $taxDeduction = $employee->tax_deduction ?? 0;
                            $otherDeductions = $employee->other_deductions ?? 0;
                            $totalDeductions = $taxDeduction + $otherDeductions + $dockAmount;
                            $netSalary = $grossSalary - $totalDeductions;
                            $advance = $employee->salary_advance ?? 0;
                            $payable = $netSalary - $advance;
                            $totalPunctuality += $punctualityBonus; $totalGross += $total; $totalDockAmount += $dockAmount;
                            $totalOtherDeductions += ($taxDeduction + $otherDeductions); $totalNetSalary += $netSalary; $totalAdvance += $advance; $totalPayable += $payable;
                        ?>
                        <tr <?php if($employee->trashed()): ?> style="border-left:3px solid #ef4444;opacity:.85" <?php endif; ?>>
                            <td><strong><?php echo e($index + 1); ?></strong></td>
                            <td style="text-align:left"><strong style="font-size:.75rem"><?php echo e($employee->name); ?></strong><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($employee->trashed()): ?> <span class="v-badge" style="font-size:.55rem;background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.25);margin-left:4px">Terminated</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                            <td style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e($joinDate); ?></td>
                            <td class="amt"><?php echo e(number_format($basicSalary, 2)); ?></td>
                            <td style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e(number_format($perDayWage, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($punctualityBonus, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($total, 2)); ?></td>
                            <td><span class="s-pill s-active" style="font-size:.6rem"><?php echo e($fullDays); ?></span></td>
                            <td><span class="s-pill" style="font-size:.6rem;background:rgba(245,158,11,.1);color:#f59e0b;border-color:rgba(245,158,11,.18)"><?php echo e($halfDays); ?></span></td>
                            <td><span class="v-badge"><?php echo e($lateDays); ?></span></td>
                            <td><span class="s-pill <?php echo e($isQualified ? 's-active' : 's-closed'); ?>" style="font-size:.6rem"><?php echo e($isQualified ? 'Yes' : 'No'); ?></span></td>
                            <td class="amt amt-neg"><?php echo e(number_format($dockAmount, 2)); ?></td>
                            <td class="amt amt-neg"><?php echo e(number_format($taxDeduction + $otherDeductions, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($netSalary, 2)); ?></td>
                            <td class="amt amt-neg"><?php echo e(number_format($advance, 2)); ?></td>
                            <td><strong class="amt amt-pos" style="font-size:.82rem"><?php echo e(number_format($payable, 2)); ?></strong></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasAnyRole([Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR])): ?>
                                <button class="act-btn a-warn" style="padding:.15rem .4rem;font-size:.62rem" data-bs-toggle="modal" data-bs-target="#editPayrollModal<?php echo e($employee->id); ?>" title="Edit"><i class="bx bx-edit"></i></button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="editPayrollModal<?php echo e($employee->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="<?php echo e(route('payroll.update', $employee->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header modal-header-glass">
                                            <h5 class="modal-title"><i class="bx bx-edit"></i> Edit — <?php echo e($employee->name); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.65rem"></button>
                                        </div>
                                        <div class="modal-body" style="max-height:70vh;overflow-y:auto">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem"><i class="bx bx-dollar-circle" style="font-size:.9rem;opacity:.7"></i> Salary</div>
                                                    <div class="mb-2">
                                                        <label class="crm-label required">Basic Salary (Rs)</label>
                                                        <input type="number" name="basic_salary" step="0.01" class="crm-input" value="<?php echo e($basicSalary); ?>" required>
                                                    </div>
                                                    <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem;margin-top:.75rem"><i class="bx bx-calendar-check" style="font-size:.9rem;opacity:.7"></i> Attendance</div>
                                                    <div class="mb-2">
                                                        <label class="crm-label">Punctuality Bonus (Rs)</label>
                                                        <input type="number" name="punctuality_bonus" step="0.01" class="crm-input" value="<?php echo e($employee->punctuality_bonus ?? 0); ?>">
                                                    </div>
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-4">
                                                            <label class="crm-label required">Full Days</label>
                                                            <input type="number" name="full_days" class="crm-input" value="<?php echo e($fullDays); ?>" min="0" max="31" required>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="crm-label required">Half Days</label>
                                                            <input type="number" name="half_days" class="crm-input" value="<?php echo e($halfDays); ?>" min="0" max="31" required>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="crm-label required">Late Count</label>
                                                            <input type="number" name="late_days" class="crm-input" value="<?php echo e($lateDays); ?>" min="0" max="31" required>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="current_month" value="<?php echo e($month); ?>">
                                                    <input type="hidden" name="current_year" value="<?php echo e($year); ?>">
                                                    <div class="info-box" style="font-size:.65rem;margin-top:.5rem">
                                                        <strong>Punctuality Rules:</strong> 2+ half=❌ | 4+ late=❌ | 1 half OK if ≥<?php echo e($totalWorkingDays - 1); ?> full
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="crm-label">Override Punctuality (Rs)</label>
                                                        <input type="number" name="override_punctuality_bonus" step="0.01" class="crm-input" value="<?php echo e($employee->override_punctuality_bonus ?? 0); ?>">
                                                        <small style="font-size:.6rem;color:var(--bs-surface-500)">Leave 0 for auto-calc</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem"><i class="bx bx-minus-circle" style="font-size:.9rem;opacity:.7"></i> Deductions & Allowances</div>
                                                    <div class="mb-2">
                                                        <label class="crm-label">Tax Deduction (Rs)</label>
                                                        <input type="number" name="tax_deduction" step="0.01" class="crm-input" value="<?php echo e($employee->tax_deduction ?? 0); ?>">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="crm-label">Other Deductions (Rs)</label>
                                                        <input type="number" name="other_deductions" step="0.01" class="crm-input" value="<?php echo e($employee->other_deductions ?? 0); ?>">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="crm-label">Other Allowances (Rs)</label>
                                                        <input type="number" name="other_allowances" step="0.01" class="crm-input" value="<?php echo e($employee->other_allowances ?? 0); ?>">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="crm-label">Salary Advance (Rs)</label>
                                                        <input type="number" name="salary_advance" step="0.01" class="crm-input" value="<?php echo e($employee->salary_advance ?? 0); ?>">
                                                    </div>
                                                    <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem;margin-top:.75rem"><i class="bx bx-calculator" style="font-size:.9rem;opacity:.7"></i> Summary</div>
                                                    <div class="brk-row">
                                                        <div>Basic: <strong>Rs <?php echo e(number_format($basicSalary, 2)); ?></strong></div>
                                                        <div>Punct.: <strong>Rs <?php echo e(number_format($punctualityBonus, 2)); ?></strong></div>
                                                        <div>Sales Bonus: <strong>Rs <?php echo e(number_format($bonus, 2)); ?></strong></div>
                                                        <div style="color:#10b981">Allowances: <strong>Rs <?php echo e(number_format($otherAllowances, 2)); ?></strong></div>
                                                        <div style="color:#ef4444">Deductions: <strong>Rs <?php echo e(number_format($totalDeductions, 2)); ?></strong></div>
                                                        <div style="color:#ef4444">Advance: <strong>Rs <?php echo e(number_format($advance, 2)); ?></strong></div>
                                                        <div style="color:#10b981;font-weight:700;font-size:.75rem">Payable: <strong>Rs <?php echo e(number_format($payable, 2)); ?></strong></div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <label class="crm-label">Notes</label>
                                                        <textarea name="payroll_notes" class="crm-input" rows="2" maxlength="500"><?php echo e($employee->payroll_notes ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="act-btn a-danger" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                                            <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="17" class="text-center" style="padding:2rem;color:var(--bs-surface-500)"><i class="bx bx-inbox" style="font-size:2rem;opacity:.3"></i><br><span style="font-size:.75rem">No employees found</span></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($manualEntries->isNotEmpty()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $manualEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $basicSalary = $entry->basic_salary ?? 0;
                            $joinDate = $entry->join_date ? \Carbon\Carbon::parse($entry->join_date)->format('d M Y') : 'N/A';
                            $perDayWage = $basicSalary / max($totalWorkingDays, 1);
                            $fullDays = $entry->full_days ?? 0; $halfDays = $entry->half_days ?? 0; $lateDays = $entry->late_days ?? 0;
                            $eligibleDays = $fullDays + $halfDays + $lateDays; $earnedSalary = $eligibleDays * $perDayWage;
                            $punctualityBonus = ($entry->is_qualified && $entry->punctuality_bonus) ? $entry->punctuality_bonus : 0;
                            $total = $earnedSalary + $punctualityBonus;
                            $dockAmount = $entry->dock_amount ?? 0; $otherDeductions = $entry->other_deductions ?? 0;
                            $otherAllowances = $entry->other_allowances ?? 0;
                            $netSalary = $total + $otherAllowances - $dockAmount - $otherDeductions;
                            $salaryAdvance = $entry->salary_advance ?? 0; $payable = $netSalary - $salaryAdvance;
                            $totalPunctuality += $punctualityBonus; $totalGross += $total; $totalDockAmount += $dockAmount;
                            $totalOtherDeductions += $otherDeductions; $totalNetSalary += $netSalary; $totalAdvance += $salaryAdvance; $totalPayable += $payable;
                        ?>
                        <tr style="border-left:3px solid #f59e0b">
                            <td><?php echo e($employees->count() + $loop->iteration); ?></td>
                            <td style="text-align:left"><strong style="font-size:.75rem"><?php echo e($entry->employee_name); ?></strong> <span class="v-badge" style="font-size:.55rem;background:rgba(245,158,11,.1);color:#f59e0b">M</span></td>
                            <td style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e($joinDate); ?></td>
                            <td class="amt"><?php echo e(number_format($basicSalary, 2)); ?></td>
                            <td style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e(number_format($perDayWage, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($punctualityBonus, 2)); ?></td>
                            <td class="amt"><strong><?php echo e(number_format($total, 2)); ?></strong></td>
                            <td><span class="s-pill s-active" style="font-size:.6rem"><?php echo e($fullDays); ?></span></td>
                            <td><span class="s-pill" style="font-size:.6rem;background:rgba(245,158,11,.1);color:#f59e0b;border-color:rgba(245,158,11,.18)"><?php echo e($halfDays); ?></span></td>
                            <td><span class="v-badge"><?php echo e($lateDays); ?></span></td>
                            <td><span class="s-pill <?php echo e($entry->is_qualified ? 's-active' : 's-closed'); ?>" style="font-size:.6rem"><?php echo e($entry->is_qualified ? 'Yes' : 'No'); ?></span></td>
                            <td class="amt amt-neg"><?php echo e(number_format($dockAmount, 2)); ?></td>
                            <td class="amt amt-neg"><?php echo e(number_format($otherDeductions, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($netSalary, 2)); ?></td>
                            <td class="amt amt-neg"><?php echo e(number_format($salaryAdvance, 2)); ?></td>
                            <td><strong class="amt amt-pos" style="font-size:.82rem"><?php echo e(number_format($payable, 2)); ?></strong></td>
                            <td>
                                <?php if(auth()->check() && auth()->user()->canEditModule('payroll')): ?>
                                <button class="act-btn a-warn" style="padding:.15rem .4rem;font-size:.62rem" data-bs-toggle="modal" data-bs-target="#editManualEntryModal<?php echo e($entry->id); ?>"><i class="bx bx-edit"></i></button>
                                <?php endif; ?>
                                <?php if(auth()->check() && auth()->user()->canDeleteInModule('payroll')): ?>
                                <button class="act-btn a-danger" style="padding:.15rem .4rem;font-size:.62rem" onclick="if(confirm('Delete manual entry for <?php echo e($entry->employee_name); ?>?')){document.getElementById('delete-manual-<?php echo e($entry->id); ?>').submit();}"><i class="bx bx-trash"></i></button>
                                <form id="delete-manual-<?php echo e($entry->id); ?>" action="<?php echo e(route('payroll.manual.destroy', $entry->id)); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                <?php endif; ?>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="editManualEntryModal<?php echo e($entry->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="<?php echo e(route('payroll.manual.update', $entry->id)); ?>">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div class="modal-header modal-header-glass">
                                            <h5 class="modal-title"><i class="bx bx-edit"></i> Edit Manual — <?php echo e($entry->employee_name); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.65rem"></button>
                                        </div>
                                        <div class="modal-body" style="max-height:70vh;overflow-y:auto">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="mb-2"><label class="crm-label required">Employee Name</label><input type="text" name="employee_name" class="crm-input" value="<?php echo e($entry->employee_name); ?>" required></div>
                                                    <div class="mb-2"><label class="crm-label">Join Date</label><input type="text" name="join_date" class="crm-input crm-date" value="<?php echo e($entry->join_date ? $entry->join_date->format('Y-m-d') : ''); ?>" placeholder="YYYY-MM-DD" autocomplete="off"></div>
                                                    <div class="mb-2"><label class="crm-label required">Basic Salary (Rs)</label><input type="number" name="basic_salary" step="0.01" class="crm-input" value="<?php echo e($entry->basic_salary); ?>" required></div>
                                                    <div class="mb-2"><label class="crm-label">Punctuality Bonus</label><input type="number" name="punctuality_bonus" step="0.01" class="crm-input" value="<?php echo e($entry->punctuality_bonus); ?>"></div>
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-4"><label class="crm-label">Full</label><input type="number" name="full_days" class="crm-input" value="<?php echo e($entry->full_days); ?>" min="0"></div>
                                                        <div class="col-4"><label class="crm-label">Half</label><input type="number" name="half_days" class="crm-input" value="<?php echo e($entry->half_days); ?>" min="0"></div>
                                                        <div class="col-4"><label class="crm-label">Late</label><input type="number" name="late_days" class="crm-input" value="<?php echo e($entry->late_days); ?>" min="0"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2"><label class="crm-label">Qualified</label><select name="is_qualified" class="crm-input crm-select"><option value="0" <?php echo e(!$entry->is_qualified ? 'selected' : ''); ?>>No</option><option value="1" <?php echo e($entry->is_qualified ? 'selected' : ''); ?>>Yes</option></select></div>
                                                    <div class="mb-2"><label class="crm-label">Dock Amount</label><input type="number" name="dock_amount" step="0.01" class="crm-input" value="<?php echo e($entry->dock_amount); ?>"></div>
                                                    <div class="mb-2"><label class="crm-label">Other Deductions</label><input type="number" name="other_deductions" step="0.01" class="crm-input" value="<?php echo e($entry->other_deductions); ?>"></div>
                                                    <div class="mb-2"><label class="crm-label">Other Allowances</label><input type="number" name="other_allowances" step="0.01" class="crm-input" value="<?php echo e($entry->other_allowances); ?>"></div>
                                                    <div class="mb-2"><label class="crm-label">Salary Advance</label><input type="number" name="salary_advance" step="0.01" class="crm-input" value="<?php echo e($entry->salary_advance); ?>"></div>
                                                    <div class="mb-2"><label class="crm-label">Notes</label><textarea name="notes" class="crm-input" rows="2"><?php echo e($entry->notes); ?></textarea></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="act-btn a-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($employees->isNotEmpty() || $manualEntries->isNotEmpty()): ?>
                    <tfoot>
                        <tr style="font-weight:700;font-size:.75rem">
                            <td colspan="3" style="text-align:right;padding-right:.5rem">TOTAL:</td>
                            <td class="amt"><?php echo e(number_format($totalBasicSalary, 2)); ?></td>
                            <td></td>
                            <td class="amt"><?php echo e(number_format($totalPunctuality, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($totalGross, 2)); ?></td>
                            <td></td><td></td><td></td><td></td>
                            <td class="amt amt-neg"><?php echo e(number_format($totalDockAmount, 2)); ?></td>
                            <td class="amt amt-neg"><?php echo e(number_format($totalOtherDeductions, 2)); ?></td>
                            <td class="amt"><?php echo e(number_format($totalNetSalary, 2)); ?></td>
                            <td class="amt amt-neg"><?php echo e(number_format($totalAdvance, 2)); ?></td>
                            <td class="amt amt-pos"><?php echo e(number_format($totalPayable, 2)); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addManualEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('payroll.manual.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="payroll_month" value="<?php echo e($month); ?>">
                <input type="hidden" name="payroll_year" value="<?php echo e($year); ?>">
                <div class="modal-header modal-header-glass">
                    <h5 class="modal-title"><i class="bx bx-plus-circle"></i> Add Manual Payroll Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.65rem"></button>
                </div>
                <div class="modal-body" style="max-height:70vh;overflow-y:auto">
                    <div class="info-box" style="font-size:.68rem"><i class="bx bx-info-circle me-1"></i> For ex-employees or individuals without MIS accounts. Same calculation formula applies.</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem"><i class="bx bx-user" style="font-size:.9rem;opacity:.7"></i> Employee Info</div>
                            <div class="mb-2"><label class="crm-label required">Employee Name</label><input type="text" name="employee_name" class="crm-input" placeholder="Full name" required></div>
                            <div class="mb-2"><label class="crm-label">Join Date</label><input type="text" name="join_date" class="crm-input crm-date" placeholder="YYYY-MM-DD" autocomplete="off"></div>
                            <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem;margin-top:.75rem"><i class="bx bx-dollar-circle" style="font-size:.9rem;opacity:.7"></i> Salary</div>
                            <div class="mb-2"><label class="crm-label required">Basic Salary (Rs)</label><input type="number" name="basic_salary" step="0.01" class="crm-input" placeholder="e.g. 40000" required></div>
                            <div class="mb-2"><label class="crm-label">Punctuality Bonus (Rs)</label><input type="number" name="punctuality_bonus" step="0.01" class="crm-input" value="5000"></div>
                            <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem;margin-top:.75rem"><i class="bx bx-calendar-check" style="font-size:.9rem;opacity:.7"></i> Attendance</div>
                            <div class="row g-2 mb-2">
                                <div class="col-4"><label class="crm-label required">Full Days</label><input type="number" name="full_days" class="crm-input" value="0" min="0" max="31"></div>
                                <div class="col-4"><label class="crm-label required">Half Days</label><input type="number" name="half_days" class="crm-input" value="0" min="0" max="31"></div>
                                <div class="col-4"><label class="crm-label required">Late Days</label><input type="number" name="late_days" class="crm-input" value="0" min="0" max="31"></div>
                            </div>
                            <div class="mb-2"><label class="crm-label">Qualified?</label><select name="is_qualified" class="crm-input crm-select"><option value="0">No</option><option value="1">Yes</option></select></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem"><i class="bx bx-calculator" style="font-size:.9rem;opacity:.7"></i> Deductions & Allowances</div>
                            <div class="mb-2"><label class="crm-label">Dock Amount (Rs)</label><input type="number" name="dock_amount" step="0.01" class="crm-input" value="0"></div>
                            <div class="mb-2"><label class="crm-label">Other Deductions (Rs)</label><input type="number" name="other_deductions" step="0.01" class="crm-input" value="0"></div>
                            <div class="mb-2"><label class="crm-label">Other Allowances (Rs)</label><input type="number" name="other_allowances" step="0.01" class="crm-input" value="0"></div>
                            <div class="mb-2"><label class="crm-label">Salary Advance (Rs)</label><input type="number" name="salary_advance" step="0.01" class="crm-input" value="0"></div>
                            <div class="form-section-title" style="font-size:.78rem;font-weight:700;color:#b89730;border-bottom:1px solid rgba(212,175,55,.12);padding-bottom:.35rem;margin-bottom:.5rem;margin-top:.75rem"><i class="bx bx-note" style="font-size:.9rem;opacity:.7"></i> Notes</div>
                            <div class="mb-2"><label class="crm-label">Notes</label><textarea name="notes" class="crm-input" rows="3" placeholder="Optional notes..."></textarea></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="act-btn a-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')); ?>"></script>
<script>
$(function(){
    $('.crm-select').select2({minimumResultsForSearch:10,width:'100%',dropdownAutoWidth:false});
    $('.crm-date').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true});
    $(document).on('shown.bs.modal',function(e){
        var m=$(e.target);
        m.find('.crm-select').each(function(){if(!$(this).data('select2'))$(this).select2({minimumResultsForSearch:10,width:'100%',dropdownParent:m})});
        m.find('.crm-date').each(function(){if(!$(this).data('datepicker'))$(this).datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true,container:m})});
    });
});
document.getElementById('payrollSearch').addEventListener('keyup', function() {
    var s = this.value.toLowerCase();
    document.querySelectorAll('#payrollTbl tbody tr').forEach(function(r) {
        var n = r.querySelector('td:nth-child(2)');
        r.style.display = n && n.textContent.toLowerCase().includes(s) ? '' : 'none';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/payroll/index.blade.php ENDPATH**/ ?>