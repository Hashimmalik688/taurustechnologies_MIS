<?php $__env->startSection('title'); ?>
    Users List
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Users
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            List
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="d-flex justify-content-end align-items-center p-2">
                    <div class="text-end mx-3">
                        <a class="btn btn-success btn-sm waves-effect waves-light" href="<?php echo e(route('users.create')); ?>">Add
                            User</a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>DOB</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($user->id); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->userDetail->phone ?? 'N/A'); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm password-field" 
                                               data-user-id="<?php echo e($user->id); ?>" 
                                               value="<?php echo e($user->userDetail->plain_password ?? ''); ?>" 
                                               placeholder="Enter password"
                                               style="min-width: 120px;">
                                    </td>
                                    <td><?php echo e($user->userDetail && $user->userDetail->dob ? \Carbon\Carbon::parse($user->userDetail->dob)->format('d M Y') : 'N/A'); ?></td>
                                    <td><?php echo e($user->userDetail && $user->userDetail->join_date ? \Carbon\Carbon::parse($user->userDetail->join_date)->format('d M Y') : 'N/A'); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif($user->status === 'inactive'): ?>
                                            <span class="badge bg-warning">Inactive</span>
                                        <?php elseif($user->status === 'suspended'): ?>
                                            <span class="badge bg-danger">Suspended</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Unknown</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-primary"><?php echo e($role->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin|Manager|HR')): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$user->roles->contains('name', 'Super Admin')): ?>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edit-user-<?php echo e($user->id); ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete(<?php echo e($user->id); ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="edit-user-<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="editUserLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?php echo e(route('users.update', $user->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name-<?php echo e($user->id); ?>" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name-<?php echo e($user->id); ?>" name="name"
                                    value="<?php echo e($user->name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email-<?php echo e($user->id); ?>" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email-<?php echo e($user->id); ?>" name="email"
                                    value="<?php echo e($user->email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="status-<?php echo e($user->id); ?>" class="form-label">Status</label>
                                <select class="form-control" id="status-<?php echo e($user->id); ?>" name="status" required>
                                    <option value="active" <?php echo e($user->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="inactive" <?php echo e($user->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    <option value="suspended" <?php echo e($user->status === 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="phone-<?php echo e($user->id); ?>" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone-<?php echo e($user->id); ?>" name="phone"
                                    value="<?php echo e($user->userDetail->phone ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="plain_password-<?php echo e($user->id); ?>" class="form-label">Password (Plaintext)</label>
                                <input type="text" class="form-control" id="plain_password-<?php echo e($user->id); ?>" name="plain_password"
                                    value="<?php echo e($user->userDetail->plain_password ?? ''); ?>" placeholder="Enter password for reference">
                                <small class="text-muted">This is for reference only, not for login authentication</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Roles</label>
                                <?php
                                    $currentRoles = $user->roles->pluck('name')->toArray();
                                ?>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Super Admin" id="modal-role-super-admin-<?php echo e($user->id); ?>" 
                                                <?php echo e(in_array('Super Admin', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-super-admin-<?php echo e($user->id); ?>">Super Admin</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Manager" id="modal-role-manager-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Manager', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-manager-<?php echo e($user->id); ?>">Manager</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="HR" id="modal-role-hr-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('HR', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-hr-<?php echo e($user->id); ?>">HR</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Employee" id="modal-role-employee-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Employee', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-employee-<?php echo e($user->id); ?>">Employee</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Co-ordinator" id="modal-role-co-ordinator-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Co-ordinator', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-co-ordinator-<?php echo e($user->id); ?>">Co-ordinator</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Agent" id="modal-role-agent-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Agent', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-agent-<?php echo e($user->id); ?>">Agent</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Vendor" id="modal-role-vendor-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Vendor', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-vendor-<?php echo e($user->id); ?>">Vendor</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-primary fw-bold">Peregrine Team</small>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Peregrine Closer" id="modal-role-peregrine-closer-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Peregrine Closer', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-peregrine-closer-<?php echo e($user->id); ?>">Peregrine Closer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Peregrine Validator" id="modal-role-peregrine-validator-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Peregrine Validator', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-peregrine-validator-<?php echo e($user->id); ?>">Peregrine Validator</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Verifier" id="modal-role-verifier-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Verifier', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-verifier-<?php echo e($user->id); ?>">Verifier</label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Ravens Closer" id="modal-role-ravens-closer-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Ravens Closer', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-ravens-closer-<?php echo e($user->id); ?>">Ravens Closer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Retention Officer" id="modal-role-retention-officer-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Retention Officer', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-retention-officer-<?php echo e($user->id); ?>">Retention Officer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Trainer" id="modal-role-trainer-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('Trainer', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-trainer-<?php echo e($user->id); ?>">Trainer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="QA" id="modal-role-qa-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('QA', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-qa-<?php echo e($user->id); ?>">QA</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="CEO" id="modal-role-ceo-<?php echo e($user->id); ?>"
                                                <?php echo e(in_array('CEO', $currentRoles) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="modal-role-ceo-<?php echo e($user->id); ?>">CEO</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password-<?php echo e($user->id); ?>" class="form-label">Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password-<?php echo e($user->id); ?>" name="password"
                                    placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation-<?php echo e($user->id); ?>" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation-<?php echo e($user->id); ?>" name="password_confirmation"
                                    placeholder="Confirm new password">
                            </div>
                            <div class="mb-3">
                                <label for="dob-<?php echo e($user->id); ?>" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob-<?php echo e($user->id); ?>" name="dob"
                                    value="<?php echo e($user->userDetail && $user->userDetail->dob ? \Carbon\Carbon::parse($user->userDetail->dob)->format('Y-m-d') : ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gender-<?php echo e($user->id); ?>" class="form-label">Gender</label>
                                <select class="form-control" id="gender-<?php echo e($user->id); ?>" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male"
                                        <?php echo e(($user->userDetail->gender ?? '') == 'Male' ? 'selected' : ''); ?>>Male</option>
                                    <option value="Female"
                                        <?php echo e(($user->userDetail->gender ?? '') == 'Female' ? 'selected' : ''); ?>>Female
                                    </option>
                                    <option value="Other"
                                        <?php echo e(($user->userDetail->gender ?? '') == 'Other' ? 'selected' : ''); ?>>Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="join_date-<?php echo e($user->id); ?>" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date-<?php echo e($user->id); ?>" name="join_date"
                                    value="<?php echo e($user->userDetail && $user->userDetail->join_date ? \Carbon\Carbon::parse($user->userDetail->join_date)->format('Y-m-d') : ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address-<?php echo e($user->id); ?>" class="form-label">Address</label>
                                <textarea class="form-control" id="address-<?php echo e($user->id); ?>" name="address" rows="3"><?php echo e($user->userDetail->address ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="city-<?php echo e($user->id); ?>" class="form-label">City</label>
                                <input type="text" class="form-control" id="city-<?php echo e($user->id); ?>" name="city"
                                    value="<?php echo e($user->userDetail->city ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="zoom_number-<?php echo e($user->id); ?>" class="form-label">Zoom Number</label>
                                <input type="text" class="form-control" id="zoom_number-<?php echo e($user->id); ?>" name="zoom_number"
                                    value="<?php echo e($user->zoom_number ?? ''); ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script>
        function confirmDelete(userId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/users/delete/${userId}`;

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Auto-save password field on blur
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.password-field').forEach(field => {
                field.addEventListener('blur', function() {
                    const userId = this.dataset.userId;
                    const password = this.value;

                    fetch(`/users/${userId}/update-password`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ plain_password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show subtle success indicator
                            this.style.borderColor = '#10b981';
                            setTimeout(() => {
                                this.style.borderColor = '';
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving password:', error);
                        this.style.borderColor = '#ef4444';
                    });
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .modal .form-check {
        margin-bottom: 0.25rem;
    }
    .modal .form-check-label {
        font-size: 0.875rem;
        font-weight: 500;
    }
    .modal .text-primary {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }
    .modal-dialog {
        max-width: 600px;
    }
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/users/index.blade.php ENDPATH**/ ?>