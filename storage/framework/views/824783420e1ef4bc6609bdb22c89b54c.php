<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title', 'Employee Management Sheet (E.M.S)'); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .ems-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .75rem; margin-bottom: .75rem;
    }
    .ems-hdr h4 {
        font-size: 1.1rem; font-weight: 700; margin: 0;
        display: flex; align-items: center; gap: .45rem;
    }
    .ems-hdr h4 i { color: #d4af37; font-size: 1.25rem; }
    .ems-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }
    .ems-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
    .ems-actions .act-btn { padding: .3rem .65rem; font-size: .7rem; border-radius: .35rem; }

    .emp-avatar {
        width: 38px; height: 38px; object-fit: cover; border-radius: .4rem;
        border: 1px solid var(--bs-surface-200); transition: transform .2s;
    }
    .emp-avatar:hover { transform: scale(1.6); position: relative; z-index: 10; }
    .emp-avatar-placeholder {
        width: 38px; height: 38px; border-radius: .4rem;
        background: linear-gradient(135deg, #d4af37, #b8922e); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem;
    }

    .ems-tabs {
        display: flex; gap: .25rem; padding: .5rem .75rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    .ems-tab {
        font-size: .72rem; font-weight: 600; padding: .32rem .7rem;
        border-radius: 22px; border: 1px solid rgba(0,0,0,.08);
        background: var(--bs-card-bg); color: var(--bs-surface-600);
        cursor: pointer; outline: none; transition: all .15s;
        display: inline-flex; align-items: center; gap: .25rem;
    }
    .ems-tab:hover { border-color: #d4af37; }
    .ems-tab.active {
        background: rgba(212,175,55,.12); border-color: rgba(212,175,55,.35);
        color: #b89730; font-weight: 700;
    }
    .ems-tab .t-count {
        font-size: .58rem; font-weight: 700;
        background: rgba(244,106,106,.15); color: #c84646;
        padding: .1rem .35rem; border-radius: 1rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgba(0,0,0,.08); border-radius: 22px;
        padding: .3rem .65rem; font-size: .72rem; width: 200px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); outline: none;
    }
    .dataTables_wrapper .dataTables_length label { font-size: .72rem; font-weight: 600; }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid rgba(0,0,0,.08); border-radius: .3rem;
        padding: .2rem 1.2rem .2rem .4rem; font-size: .72rem;
    }
    .dataTables_wrapper .dataTables_info { font-size: .68rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: .25rem .5rem; font-size: .68rem; border-radius: .3rem;
        border: 1px solid rgba(0,0,0,.06); margin: 0 1px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #d4af37, #e8c84a) !important;
        color: #fff !important; border-color: #d4af37 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
        background: rgba(212,175,55,.08) !important;
        border-color: #d4af37 !important; color: #b89730 !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="ems-hdr">
        <div>
            <h4><i class="bx bx-id-card"></i> Employee Management Sheet</h4>
            <p>Centralized employee records, passport images &amp; data management</p>
        </div>
        <div class="ems-actions">
            <button class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="bx bx-plus"></i> Add Employee
            </button>
            <button class="act-btn a-info" data-bs-toggle="modal" data-bs-target="#importEmployeeModal">
                <i class="bx bx-upload"></i> Import CSV
            </button>
            <a href="<?php echo e(route('employee.export')); ?>" class="act-btn a-success">
                <i class="bx bx-download"></i> Export CSV
            </a>
        </div>
    </div>

    <?php
        $terminatedEmployees = $employees->filter(fn($e) => $e->status === 'Terminated');
        $activeEmployees = $employees->filter(fn($e) => $e->status !== 'Terminated');
        $misCount = $employees->where('mis', 'Yes')->count();
        $activeCount = $employees->where('status', 'Active')->count();
    ?>

    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-group"></i></span>
            <span class="k-val"><?php echo e($activeEmployees->count()); ?></span>
            <span class="k-lbl">Current Employees</span>
        </div>
        <div class="kpi-card k-blue">
            <span class="k-icon"><i class="bx bx-desktop"></i></span>
            <span class="k-val"><?php echo e($misCount); ?></span>
            <span class="k-lbl">MIS Accounts</span>
        </div>
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-check-circle"></i></span>
            <span class="k-val"><?php echo e($activeCount); ?></span>
            <span class="k-lbl">Active</span>
        </div>
        <div class="kpi-card k-red">
            <span class="k-icon"><i class="bx bx-user-x"></i></span>
            <span class="k-val"><?php echo e($terminatedEmployees->count()); ?></span>
            <span class="k-lbl">Terminated</span>
        </div>
    </div>

    <div class="ex-card sec-card">
        <div class="ems-tabs" role="tablist">
            <button class="ems-tab active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeEmployees" type="button" role="tab" aria-controls="activeEmployees" aria-selected="true">
                <i class="bx bx-user-check"></i> Current Employees
            </button>
            <button class="ems-tab" id="terminated-tab" data-bs-toggle="tab" data-bs-target="#terminatedEmployees" type="button" role="tab" aria-controls="terminatedEmployees" aria-selected="false">
                <i class="bx bx-user-x"></i> Terminated
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($terminatedEmployees->count() > 0): ?>
                    <span class="t-count"><?php echo e($terminatedEmployees->count()); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </button>
        </div>

        <div class="sec-body">
            <div class="tab-content" id="emsTabContent">
                <div class="tab-pane fade show active" id="activeEmployees" role="tabpanel" aria-labelledby="active-tab">
                    <div class="table-responsive">
                        <table id="employeeTable" class="ex-tbl">
                            <thead>
                                <tr>
                                    <th>Sr#</th><th>Name</th><th>Email</th><th>DOB</th><th>Join Date</th>
                                    <th>Contact</th><th>Emergency</th><th>CNIC</th><th>Position</th>
                                    <th>Residence</th><th>Status</th><th>MIS</th><th>Photo</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activeEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $user = \App\Models\User::withTrashed()->where('email', $emp->email)->first();
                                    $dob = $user && $user->userDetail && $user->userDetail->dob ? \Carbon\Carbon::parse($user->userDetail->dob)->format('d M Y') : 'N/A';
                                    $joinDate = $user && $user->userDetail && $user->userDetail->join_date ? \Carbon\Carbon::parse($user->userDetail->join_date)->format('d M Y') : 'N/A';
                                ?>
                                <tr>
                                    <td><strong><?php echo e($i+1); ?></strong></td>
                                    <td><strong><?php echo e($emp->name); ?></strong></td>
                                    <td><a href="mailto:<?php echo e($emp->email); ?>" style="color:#556ee6;text-decoration:none;font-size:.72rem"><?php echo e($emp->email); ?></a></td>
                                    <td><?php echo e($dob); ?></td>
                                    <td><?php echo e($joinDate); ?></td>
                                    <td><?php echo e($emp->contact_info); ?></td>
                                    <td><?php echo e($emp->emergency_contact); ?></td>
                                    <td><?php echo e($emp->cnic); ?></td>
                                    <td><?php echo e($emp->position); ?></td>
                                    <td><?php echo e($emp->area_of_residence); ?></td>
                                    <td>
                                        <?php $sPill = match($emp->status) { 'Active' => 's-sale', 'Not Active' => 's-pending', 'Terminated' => 's-declined', default => 's-closed' }; ?>
                                        <span class="s-pill <?php echo e($sPill); ?>"><?php echo e($emp->status); ?></span>
                                    </td>
                                    <td>
                                        <span class="s-pill <?php echo e(($emp->mis === 'Yes') ? 's-sale' : 's-declined'); ?>"><?php echo e($emp->mis); ?></span>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->passport_image): ?>
                                            <img src="<?php echo e(asset('storage/'.$emp->passport_image)); ?>" alt="<?php echo e($emp->name); ?>" class="emp-avatar">
                                        <?php else: ?>
                                            <div class="emp-avatar-placeholder"><?php echo e(strtoupper(substr($emp->name, 0, 1))); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="act-btn a-warn" data-bs-toggle="modal" data-bs-target="#editEmployeeModal<?php echo e($emp->id); ?>" title="Edit"><i class="bx bx-edit"></i></button>
                                            <button class="act-btn a-danger" onclick="deleteEmployee(<?php echo e($emp->id); ?>, '<?php echo e(addslashes($emp->name)); ?>');" title="Delete"><i class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="14" class="text-center py-5">
                                    <i class="bx bx-inbox" style="font-size:2rem;color:#d4af37;opacity:.5"></i>
                                    <h6 class="mt-2 text-muted" style="font-size:.82rem">No Active Employees</h6>
                                </td></tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="terminatedEmployees" role="tabpanel" aria-labelledby="terminated-tab">
                    <div style="padding:.5rem .75rem;margin-bottom:.5rem;border-radius:.4rem;background:rgba(241,180,76,.06);border:1px solid rgba(241,180,76,.2);font-size:.72rem;color:#b87a14">
                        <i class="bx bx-info-circle me-1"></i>
                        Terminated employees with deactivated MIS accounts. You can <strong>restore</strong> or <strong>permanently delete</strong>.
                    </div>
                    <div class="table-responsive">
                        <table id="terminatedTable" class="ex-tbl">
                            <thead>
                                <tr><th>Sr#</th><th>Name</th><th>Email</th><th>Contact</th><th>CNIC</th><th>Position</th><th>DOT</th><th>MIS</th><th>Photo</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $terminatedEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($loop->iteration); ?></strong></td>
                                    <td><strong><?php echo e($emp->name); ?></strong></td>
                                    <td><a href="mailto:<?php echo e($emp->email); ?>" style="color:#556ee6;text-decoration:none;font-size:.72rem"><?php echo e($emp->email); ?></a></td>
                                    <td><?php echo e($emp->contact_info); ?></td>
                                    <td><?php echo e($emp->cnic); ?></td>
                                    <td><?php echo e($emp->position); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->date_of_termination): ?>
                                            <span class="v-badge v-red"><?php echo e(\Carbon\Carbon::parse($emp->date_of_termination)->format('d M Y')); ?></span>
                                        <?php else: ?> <span class="text-muted">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><span class="s-pill s-declined">No</span></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->passport_image): ?>
                                            <img src="<?php echo e(asset('storage/'.$emp->passport_image)); ?>" alt="<?php echo e($emp->name); ?>" class="emp-avatar" style="opacity:.6">
                                        <?php else: ?>
                                            <div class="emp-avatar-placeholder" style="opacity:.5"><?php echo e(strtoupper(substr($emp->name, 0, 1))); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form method="POST" action="<?php echo e(route('employee.restore', $emp->id)); ?>" class="d-inline" onsubmit="return confirm('Restore <?php echo e(addslashes($emp->name)); ?>?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="act-btn a-success"><i class="bx bx-undo"></i> Restore</button>
                                            </form>
                                            <button class="act-btn a-danger" onclick="permanentDelete(<?php echo e($emp->id); ?>, '<?php echo e(addslashes($emp->name)); ?>');"><i class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="10" class="text-center py-5">
                                    <i class="bx bx-check-circle" style="font-size:2rem;color:#1a8754;opacity:.5"></i>
                                    <h6 class="mt-2 text-muted" style="font-size:.82rem">No Terminated Employees</h6>
                                </td></tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="editEmployeeModal<?php echo e($emp->id); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('employee.update', $emp)); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header modal-header-glass">
                        <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?php echo e($emp->name); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo e($emp->email); ?>"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Contact info</label><input type="text" name="contact_info" class="form-control phone-number" value="<?php echo e($emp->contact_info); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Emergency Contact</label><input type="text" name="emergency_contact" class="form-control phone-number" value="<?php echo e($emp->emergency_contact); ?>"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">CNIC</label><input type="text" name="cnic" class="form-control" value="<?php echo e($emp->cnic); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Position</label><input type="text" name="position" class="form-control" value="<?php echo e($emp->position); ?>"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Area of Residence</label><input type="text" name="area_of_residence" class="form-control" value="<?php echo e($emp->area_of_residence); ?>"></div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active" <?php if($emp->status == 'Active'): ?> selected <?php endif; ?>>Active</option>
                                    <option value="Not Active" <?php if($emp->status == 'Not Active'): ?> selected <?php endif; ?>>Not Active</option>
                                    <option value="Terminated" <?php if($emp->status == 'Terminated'): ?> selected <?php endif; ?>>Terminated</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Date of Termination</label><input type="date" name="date_of_termination" class="form-control" value="<?php echo e($emp->date_of_termination ? (\Carbon\Carbon::parse($emp->date_of_termination)->format('Y-m-d')) : ''); ?>"></div>
                        </div>
                        <label class="form-label">Passport Image (WebP)</label>
                        <input type="file" name="passport_image" accept="image/webp" class="form-control">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->passport_image): ?>
                            <img src="<?php echo e(asset('storage/'.$emp->passport_image)); ?>" alt="Passport" class="emp-avatar mt-2" style="width:55px;height:55px;">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Import Modal -->
    <div class="modal fade" id="importEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('employee.import')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header modal-header-glass">
                        <h5 class="modal-title"><i class="bx bx-upload me-2"></i>Import Employees</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div style="padding:.5rem .75rem;margin-bottom:.75rem;border-radius:.4rem;background:rgba(80,165,241,.06);border:1px solid rgba(80,165,241,.2);font-size:.72rem;color:#2b81c9">
                            <i class="bx bx-info-circle me-1"></i>
                            <strong>CSV Format:</strong> Email (required), Name, Contact, Emergency, CNIC, Position, Residence, Status, MIS
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:.78rem;font-weight:600">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv,.xlsx,.txt" required>
                            <small class="text-muted" style="font-size:.68rem">Supported: CSV, XLSX, TXT</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-primary"><i class="bx bx-upload"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('employee.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header modal-header-glass">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Add New Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Contact</label><input type="text" name="contact_info" class="form-control phone-number"></div>
                            <div class="col-md-6"><label class="form-label">Emergency</label><input type="text" name="emergency_contact" class="form-control phone-number"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">CNIC</label><input type="text" name="cnic" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Position</label><input type="text" name="position" class="form-control"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Residence</label><input type="text" name="area_of_residence" class="form-control"></div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select"><option value="Active">Active</option><option value="Not Active">Not Active</option><option value="Terminated">Terminated</option></select>
                            </div>
                            <div class="col-md-3"><label class="form-label">DOT</label><input type="date" name="date_of_termination" class="form-control"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Passport Image (WebP)</label><input type="file" name="passport_image" accept="image/webp" class="form-control"></div>
                    </div>
                    <div class="modal-footer border-top" style="background:rgba(212,175,55,.03)">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="act-btn a-success"><i class="bx bx-save"></i> Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')); ?>"></script>
<script>
$(document).ready(function() {
    if (!$.fn.dataTable.isDataTable('#employeeTable')) {
        $('#employeeTable').DataTable({
            responsive: false, pageLength: 25, order: [[0, 'asc']],
            language: { search: "_INPUT_", searchPlaceholder: "Search employees...", lengthMenu: "Show _MENU_ employees per page", info: "Showing _START_ to _END_ of _TOTAL_", infoEmpty: "No employees", infoFiltered: "(filtered from _MAX_)" },
            columnDefs: [{ orderable: false, targets: [10, 11, 12, 13] }]
        });
    }

    // Tab switching with manual active class + DataTable init for terminated
    document.querySelectorAll('.ems-tab').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            document.querySelectorAll('.ems-tab').forEach(function(t) { t.classList.remove('active'); });
            e.target.classList.add('active');
        });
    });

    $('button[data-bs-target="#terminatedEmployees"]').on('shown.bs.tab', function () {
        if (!$.fn.dataTable.isDataTable('#terminatedTable')) {
            $('#terminatedTable').DataTable({
                responsive: false, pageLength: 25, order: [[0, 'asc']],
                language: { search: "_INPUT_", searchPlaceholder: "Search terminated...", lengthMenu: "Show _MENU_ per page", info: "Showing _START_ to _END_ of _TOTAL_", infoEmpty: "None", infoFiltered: "(filtered from _MAX_)" },
                columnDefs: [{ orderable: false, targets: [7, 8, 9] }]
            });
        }
    });

    function formatPhoneNumber(v) { if (!v) return v; let d = v.replace(/\D/g, ''); if (d && d.startsWith('3') && !d.startsWith('03')) d = '0' + d; return d; }
    $(document).on('blur', '.phone-number', function() { let v = $(this).val(); if (v) $(this).val(formatPhoneNumber(v)); });
    $(document).on('submit', 'form', function() { $(this).find('.phone-number').each(function() { let v = $(this).val(); if (v) $(this).val(formatPhoneNumber(v)); }); });
});
function deleteEmployee(id, name) { if (confirm('Delete ' + name + '? This cannot be undone.')) { let f = document.createElement('form'); f.method = 'POST'; f.action = '/ems/' + id; f.innerHTML = '<?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>'; document.body.appendChild(f); f.submit(); } }
function permanentDelete(id, name) { if (confirm('PERMANENTLY delete ' + name + '? All records will be removed.')) { let f = document.createElement('form'); f.method = 'POST'; f.action = '/ems/' + id; f.innerHTML = '<?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>'; document.body.appendChild(f); f.submit(); } }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/employee/ems.blade.php ENDPATH**/ ?>