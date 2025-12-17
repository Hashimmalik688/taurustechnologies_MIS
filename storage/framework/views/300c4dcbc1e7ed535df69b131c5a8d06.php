<?php $__env->startSection('title'); ?>
    Users List
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('/assets/libs/datatables/datatables.min.css')); ?>" rel="stylesheet" type="text/css" />
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

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                                <th>Status</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($user->id); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->userDetail->phone ?? 'N/A'); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php if($user->status === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif($user->status === 'inactive'): ?>
                                            <span class="badge bg-warning">Inactive</span>
                                        <?php elseif($user->status === 'suspended'): ?>
                                            <span class="badge bg-danger">Suspended</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-primary"><?php echo e($role->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td>
                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin|Manager')): ?>
                                            <?php if(!$user->roles->contains('name', 'Super Admin')): ?>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edit-user-<?php echo e($user->id); ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete(<?php echo e($user->id); ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo e($user->name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo e($user->email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active" <?php echo e($user->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="inactive" <?php echo e($user->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    <option value="suspended" <?php echo e($user->status === 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="<?php echo e($user->userDetail->phone ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="Super Admin" <?php echo e($user->hasRole('Super Admin') ? 'selected' : ''); ?>>Super Admin</option>
                                    <option value="Manager" <?php echo e($user->hasRole('Manager') ? 'selected' : ''); ?>>Manager</option>
                                    <option value="HR" <?php echo e($user->hasRole('HR') ? 'selected' : ''); ?>>HR</option>
                                    <option value="Employee" <?php echo e($user->hasRole('Employee') ? 'selected' : ''); ?>>Employee</option>
                                    <option value="Agent" <?php echo e($user->hasRole('Agent') ? 'selected' : ''); ?>>Agent</option>
                                    <option value="Vendor" <?php echo e($user->hasRole('Vendor') ? 'selected' : ''); ?>>Vendor</option>
                                    <optgroup label="Paraguins Team">
                                        <option value="Paraguins Closer" <?php echo e($user->hasRole('Paraguins Closer') ? 'selected' : ''); ?>>Paraguins Closer</option>
                                        <option value="Paraguins Validator" <?php echo e($user->hasRole('Paraguins Validator') ? 'selected' : ''); ?>>Paraguins Validator</option>
                                        <option value="Verifier" <?php echo e($user->hasRole('Verifier') ? 'selected' : ''); ?>>Verifier</option>
                                    </optgroup>
                                    <option value="Ravens Closer" <?php echo e($user->hasRole('Ravens Closer') ? 'selected' : ''); ?>>Ravens Closer</option>
                                    <option value="Retention Officer" <?php echo e($user->hasRole('Retention Officer') ? 'selected' : ''); ?>>Retention Officer</option>
                                    <option value="Trainer" <?php echo e($user->hasRole('Trainer') ? 'selected' : ''); ?>>Trainer</option>
                                    <option value="QA" <?php echo e($user->hasRole('QA') ? 'selected' : ''); ?>>QA</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob"
                                    value="<?php echo e($user->userDetail->dob ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender">
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
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date" name="join_date"
                                    value="<?php echo e($user->userDetail->join_date ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo e($user->userDetail->address ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="<?php echo e($user->userDetail->city ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="zoom_number" class="form-label">Zoom Number</label>
                                <input type="text" class="form-control" id="zoom_number" name="zoom_number"
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
    <script src="<?php echo e(URL::asset('/assets/libs/datatables/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/pages/datatables.init.js')); ?>"></script>
    <script>
        function confirmDelete(userId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/users/delete/${userId}`;

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/admin/users/index.blade.php ENDPATH**/ ?>