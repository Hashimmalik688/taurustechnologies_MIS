<?php $__env->startSection('title', 'Employee Management Sheet (E.M.S)'); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<style>
    .employee-avatar {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        transition: transform 0.2s;
    }
    .employee-avatar:hover {
        transform: scale(1.5);
        cursor: pointer;
        z-index: 1000;
        position: relative;
    }
    .no-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 700;
        font-size: 18px;
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .stats-card .icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .import-zone {
        background: #f8f9fa;
        border: 2px dashed #d4af37;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s;
    }
    .import-zone:hover {
        background: #fff;
        border-color: #c49b2e;
    }
    .badge-status {
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .badge-active { background: #10b981; color: white; }
    .badge-inactive { background: #ef4444; color: white; }
    .badge-pending { background: #f59e0b; color: white; }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #d4af37;
        border-bottom: 3px solid #d4af37;
        background: transparent;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        color: #333;
        border-bottom: 3px solid #ddd;
    }
    .terminated-count {
        background: #ef4444;
        color: white;
        border-radius: 50%;
        padding: 0.15rem 0.5rem;
        font-size: 0.7rem;
        margin-left: 0.4rem;
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
                        <i class="bx bx-id-card me-2"></i>
                        Employee Management Sheet
                    </h1>
                    <p class="text-muted">Centralized employee records, passport images, and data management</p>
                </div>
                <div class="d-flex gap-2">
                    <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Trainer')): ?>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                            <i class="bx bx-plus me-1"></i> Add Employee
                        </button>
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importEmployeeModal">
                            <i class="bx bx-upload me-1"></i> Import CSV
                        </button>
                    <?php endif; ?>
                    <a href="<?php echo e(route('employee.export')); ?>" class="btn btn-success">
                        <i class="bx bx-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Employees</h6>
                        <small class="text-white d-block"><?php echo e($employees->count()); ?> - <?php echo e($employees->where('status', 'Terminated')->count()); ?> = <strong class="fs-5"><?php echo e($employees->count() - $employees->where('status', 'Terminated')->count()); ?></strong></small>
                    </div>
                    <i class="bx bx-user icon"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">MIS Accounts</h6>
                        <h2 class="mb-0"><?php echo e($employees->where('mis', 'Yes')->count()); ?></h2>
                    </div>
                    <i class="bx bx-desktop icon"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Active</h6>
                        <h2 class="mb-0"><?php echo e($employees->where('status', 'Active')->count()); ?></h2>
                    </div>
                    <i class="bx bx-check-circle icon"></i>
                </div>
            </div>
        </div>
    </div>


    <!-- Employee Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs card-header-tabs" id="emsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeEmployees" type="button" role="tab">
                        <i class="bx bx-user-check me-1"></i> Current Employees
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="terminated-tab" data-bs-toggle="tab" data-bs-target="#terminatedEmployees" type="button" role="tab">
                        <i class="bx bx-user-x me-1"></i> Terminated
                        <?php
                            $terminatedEmployees = $employees->filter(fn($e) => $e->status === 'Terminated');
                            $activeEmployees = $employees->filter(fn($e) => $e->status !== 'Terminated');
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($terminatedEmployees->count() > 0): ?>
                            <span class="terminated-count"><?php echo e($terminatedEmployees->count()); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="emsTabContent">
                <!-- Active Employees Tab -->
                <div class="tab-pane fade show active" id="activeEmployees" role="tabpanel">
            <div class="table-responsive">
                <table id="employeeTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sr#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>DOB</th>
                            <th>Join Date</th>
                            <th>Contact</th>
                            <th>Emergency</th>
                            <th>CNIC</th>
                            <th>Position</th>
                            <th>Residence</th>
                            <th>Status</th>
                            <th>MIS</th>
                            <th>Photo</th>
                            <th>Actions</th>
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
                            <td><a href="mailto:<?php echo e($emp->email); ?>" class="text-decoration-none"><?php echo e($emp->email); ?></a></td>
                            <td><?php echo e($dob); ?></td>
                            <td><?php echo e($joinDate); ?></td>
                            <td><?php echo e($emp->contact_info); ?></td>
                            <td><?php echo e($emp->emergency_contact); ?></td>
                            <td><?php echo e($emp->cnic); ?></td>
                            <td><?php echo e($emp->position); ?></td>
                            <td><?php echo e($emp->area_of_residence); ?></td>
                            <td>
                                <?php
                                    $statusClass = match($emp->status) {
                                        'Active' => 'badge-success',
                                        'Not Active' => 'badge-warning',
                                        'Terminated' => 'badge-danger',
                                        default => 'badge-secondary',
                                    };
                                    $statusIcon = match($emp->status) {
                                        'Active' => 'bx-check-circle',
                                        'Not Active' => 'bx-time',
                                        'Terminated' => 'bx-x-circle',
                                        default => 'bx-help-circle',
                                    };
                                ?>
                                <span class="badge <?php echo e($statusClass); ?>" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                                    <i class="bx <?php echo e($statusIcon); ?> me-1"></i><?php echo e($emp->status); ?>

                                </span>
                            </td>
                            <td>
                                <?php
                                    $misClass = ($emp->mis === 'Yes') ? 'badge-success' : 'badge-danger';
                                    $misIcon = ($emp->mis === 'Yes') ? 'bx-check-circle' : 'bx-x-circle';
                                ?>
                                <span class="badge <?php echo e($misClass); ?>" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                                    <i class="bx <?php echo e($misIcon); ?> me-1"></i><?php echo e($emp->mis); ?>

                                </span>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->passport_image): ?>
                                    <img src="<?php echo e(asset('storage/'.$emp->passport_image)); ?>" alt="<?php echo e($emp->name); ?>" class="employee-avatar" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                <?php else: ?>
                                    <div class="no-avatar"><?php echo e(strtoupper(substr($emp->name, 0, 1))); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Trainer')): ?>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEmployeeModal<?php echo e($emp->id); ?>" title="Edit Employee">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteEmployee(<?php echo e($emp->id); ?>, '<?php echo e($emp->name); ?>');" title="Delete Employee">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <!-- Edit Modal (one per employee) -->
                        <div class="modal fade" id="editEmployeeModal<?php echo e($emp->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="<?php echo e(route('employee.update', $emp)); ?>" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header bg-light border-bottom">
                                            <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Employee</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo e($emp->name); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?php echo e($emp->email); ?>">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Contact info</label>
                                                    <input type="text" name="contact_info" class="form-control phone-number" value="<?php echo e($emp->contact_info); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Emergency Contact</label>
                                                    <input type="text" name="emergency_contact" class="form-control phone-number" value="<?php echo e($emp->emergency_contact); ?>">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">CNIC</label>
                                                    <input type="text" name="cnic" class="form-control" value="<?php echo e($emp->cnic); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Position</label>
                                                    <input type="text" name="position" class="form-control" value="<?php echo e($emp->position); ?>">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Area of Residence</label>
                                                    <input type="text" name="area_of_residence" class="form-control" value="<?php echo e($emp->area_of_residence); ?>">
                                                </div>
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
                                                <div class="col-md-6">
                                                    <label class="form-label">Date of Termination (DOT)</label>
                                                    <input type="date" name="date_of_termination" class="form-control" value="<?php echo e($emp->date_of_termination ? (\Carbon\Carbon::parse($emp->date_of_termination)->format('Y-m-d')) : ''); ?>">
                                                </div>
                                            </div>
                                                <label class="form-label">Passport Size Image (WebP only)</label>
                                                <input type="file" name="passport_image" accept="image/webp" class="form-control">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->passport_image): ?>
                                                    <img src="<?php echo e(asset('storage/'.$emp->passport_image)); ?>" alt="Passport" style="width:60px;height:60px;object-fit:cover;border-radius:4px;margin-top:8px;">
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="14" class="text-center py-5">
                                <div style="opacity: 0.5;">
                                    <i class="bx bx-inbox" style="font-size: 4rem; color: #d4af37;"></i>
                                    <h5 class="mt-3 text-muted">No Active Employees</h5>
                                    <p class="text-muted">Click "Add Employee" button above to create your first employee record</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
                </div>

                <!-- Terminated Employees Tab -->
                <div class="tab-pane fade" id="terminatedEmployees" role="tabpanel">
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        These are employees who have been terminated and their MIS accounts deactivated. You can <strong>restore</strong> them back to active status or <strong>permanently delete</strong> their records.
                    </div>
                    <div class="table-responsive">
                        <table id="terminatedTable" class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>CNIC</th>
                                    <th>Position</th>
                                    <th>DOT</th>
                                    <th>MIS</th>
                                    <th>Photo</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $terminatedEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($loop->iteration); ?></strong></td>
                                    <td><strong><?php echo e($emp->name); ?></strong></td>
                                    <td><a href="mailto:<?php echo e($emp->email); ?>" class="text-decoration-none"><?php echo e($emp->email); ?></a></td>
                                    <td><?php echo e($emp->contact_info); ?></td>
                                    <td><?php echo e($emp->cnic); ?></td>
                                    <td><?php echo e($emp->position); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->date_of_termination): ?>
                                            <strong class="text-danger"><?php echo e(\Carbon\Carbon::parse($emp->date_of_termination)->format('d M Y')); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                                            <i class="bx bx-x-circle me-1"></i>No
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emp->passport_image): ?>
                                            <img src="<?php echo e(asset('storage/'.$emp->passport_image)); ?>" alt="<?php echo e($emp->name); ?>" class="employee-avatar" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                        <?php else: ?>
                                            <div class="no-avatar" style="opacity:0.5;"><?php echo e(strtoupper(substr($emp->name, 0, 1))); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Trainer')): ?>
                                        <div class="d-flex gap-1">
                                            <form method="POST" action="<?php echo e(route('employee.restore', $emp->id)); ?>" class="d-inline" onsubmit="return confirm('Restore <?php echo e($emp->name); ?> back to active status?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="Restore Employee">
                                                    <i class="bx bx-undo"></i> Restore
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-outline-danger" onclick="permanentDelete(<?php echo e($emp->id); ?>, '<?php echo e($emp->name); ?>');" title="Permanently Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div style="opacity: 0.5;">
                                            <i class="bx bx-check-circle" style="font-size: 4rem; color: #10b981;"></i>
                                            <h5 class="mt-3 text-muted">No Terminated Employees</h5>
                                            <p class="text-muted">All employees are currently active</p>
                                        </div>
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
</div>

    <!-- Import Employee Modal -->
    <div class="modal fade" id="importEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('employee.import')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title"><i class="bx bx-upload me-2"></i>Import Employees from CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>CSV Format Requirements:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Email address (required)</li>
                                <li>Name, Contact, Emergency Contact</li>
                                <li>CNIC, Position, Area of Residence</li>
                                <li>Status (Active/Not Active/Terminated)</li>
                                <li>MIS (Yes/No)</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv,.xlsx,.txt" required>
                            <small class="text-muted">Supported: CSV, XLSX, TXT</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-upload me-1"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('employee.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Add New Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Contact info</label>
                                <input type="text" name="contact_info" class="form-control phone-number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Contact</label>
                                <input type="text" name="emergency_contact" class="form-control phone-number">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">CNIC</label>
                                <input type="text" name="cnic" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Area of Residence</label>
                                <input type="text" name="area_of_residence" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                    <option value="Terminated">Terminated</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">DOT</label>
                                <input type="date" name="date_of_termination" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Passport Size Image (WebP only)</label>
                            <input type="file" name="passport_image" accept="image/webp" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Save Employee
                        </button>
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
    // Initialize DataTable only once
    let table = null;
    
    if (!$.fn.dataTable.isDataTable('#employeeTable')) {
        table = $('#employeeTable').DataTable({
            responsive: false,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search employees...",
                lengthMenu: "Show _MENU_ employees per page",
                info: "Showing _START_ to _END_ of _TOTAL_ employees",
                infoEmpty: "No employees available",
                infoFiltered: "(filtered from _MAX_ total employees)"
            },
            columnDefs: [
                { orderable: false, targets: [10, 11] }
            ]
        });
    } else {
        table = $('#employeeTable').DataTable();
    }

    // Initialize terminated table when tab shown
    $('button[data-bs-target="#terminatedEmployees"]').on('shown.bs.tab', function () {
        if (!$.fn.dataTable.isDataTable('#terminatedTable')) {
            $('#terminatedTable').DataTable({
                responsive: false,
                pageLength: 25,
                order: [[0, 'asc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search terminated...",
                    lengthMenu: "Show _MENU_ per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ terminated employees",
                    infoEmpty: "No terminated employees",
                    infoFiltered: "(filtered from _MAX_ total)"
                },
                columnDefs: [
                    { orderable: false, targets: [7, 8] }
                ]
            });
        }
    });

    // Format phone numbers - add 0 prefix if starts with 3
    function formatPhoneNumber(value) {
        if (!value) return value;
        // Remove any non-digit characters
        let digits = value.replace(/\D/g, '');
        // Add 0 prefix if it starts with 3 and doesn't already start with 0
        if (digits && digits.startsWith('3') && !digits.startsWith('03')) {
            digits = '0' + digits;
        }
        return digits;
    }

    // Apply phone formatting to all phone inputs on blur
    $(document).on('blur', '.phone-number', function() {
        let value = $(this).val();
        if (value) {
            $(this).val(formatPhoneNumber(value));
        }
    });

    // Format phone numbers before form submission
    $(document).on('submit', 'form', function() {
        $(this).find('.phone-number').each(function() {
            let value = $(this).val();
            if (value) {
                $(this).val(formatPhoneNumber(value));
            }
        });
    });
});

// Delete employee function
function deleteEmployee(empId, empName) {
    if (confirm(`Are you sure you want to delete ${empName}? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/ems/${empId}`;
        form.innerHTML = `
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Permanently delete terminated employee
function permanentDelete(empId, empName) {
    if (confirm(`⚠️ PERMANENTLY delete ${empName}?\n\nThis will remove ALL records including attendance history. This cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/ems/${empId}`;
        form.innerHTML = `
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/employee/ems.blade.php ENDPATH**/ ?>