<?php $__env->startSection('title'); ?>
    View User
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .info-card {
        border-radius: 8px;
        box-shadow: 0px 0px 14px 4px #12263f24;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 15px;
        font-weight: 500;
        color: #495057;
    }

    .section-title {
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        color: #556ee6;
    }

    .user-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: white;
        font-weight: bold;
        margin: 0 auto 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-box {
        text-align: center;
        padding: 20px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    .badge-role {
        padding: 8px 15px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 20px;
    }

    .badge-manager {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .badge-employee {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .badge-active {
        background-color: #34c38f;
        color: white;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
    }

    .badge-inactive {
        background-color: #f46a6a;
        color: white;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Users
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            View User Details
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <!-- Left Column - User Info -->
        <div class="col-lg-4">
            <div class="card info-card">
                <div class="card-body">
                    <div class="user-avatar">
                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                    </div>

                    <div class="text-center mb-4">
                        <h4 class="mb-1"><?php echo e($user->name); ?></h4>
                        <p class="text-muted mb-2"><?php echo e($user->email); ?></p>
                        <div class="mb-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-role <?php echo e(in_array($role->name, ['Manager', 'Super Admin']) ? 'badge-manager' : 'badge-employee'); ?> me-1">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    <?php echo e($role->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->roles->isEmpty()): ?>
                                <span class="badge badge-role badge-employee">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    No Role Assigned
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->deleted_at): ?>
                                <span class="badge-inactive">
                                    <i class="mdi mdi-close-circle me-1"></i>Inactive
                                </span>
                            <?php else: ?>
                                <span class="badge-active">
                                    <i class="mdi mdi-check-circle me-1"></i>Active
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->userDetail): ?>
                        <div class="mb-3">
                            <div class="info-label">Phone</div>
                            <div class="info-value">
                                <i class="mdi mdi-phone me-1 text-primary"></i>
                                <?php echo e($user->userDetail->phone ?? 'Not provided'); ?>

                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Zoom Number</div>
                            <div class="info-value">
                                <i class="mdi mdi-video me-1 text-primary"></i>
                                <?php echo e($user->userDetail->zoom_number ?? 'Not provided'); ?>

                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Gender</div>
                            <div class="info-value">
                                <i class="mdi mdi-gender-<?php echo e(strtolower($user->userDetail->gender ?? 'male')); ?> me-1 text-primary"></i>
                                <?php echo e($user->userDetail->gender ?? 'Not specified'); ?>

                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">
                                <i class="mdi mdi-cake-variant me-1 text-primary"></i>
                                <?php echo e($user->dob ? $user->dob->format('M d, Y') : 'Not provided'); ?>

                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Join Date</div>
                            <div class="info-value">
                                <i class="mdi mdi-calendar me-1 text-primary"></i>
                                <?php echo e($user->userDetail->join_date ?? 'Not provided'); ?>

                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->userDetail->city || $user->userDetail->address): ?>
                            <div class="mb-3">
                                <div class="info-label">Location</div>
                                <div class="info-value">
                                    <i class="mdi mdi-map-marker me-1 text-primary"></i>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->userDetail->city): ?>
                                        <?php echo e($user->userDetail->city); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->userDetail->address): ?>
                                        <br><small class="text-muted"><?php echo e($user->userDetail->address); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="d-grid gap-2 mt-4">
                        <a href="<?php echo e(route('users.edit', $user->id)); ?>" class="btn btn-primary">
                            <i class="mdi mdi-pencil me-1"></i>
                            Edit User
                        </a>
                        <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Statistics & Activity -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-number">
                            <?php echo e($user->attendances()->count()); ?>

                        </div>
                        <div class="stat-label">
                            <i class="mdi mdi-calendar-check me-1"></i>
                            Total Attendance
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="stat-number">
                            <?php echo e($user->attendances()->whereMonth('created_at', now()->month)->count()); ?>

                        </div>
                        <div class="stat-label">
                            <i class="mdi mdi-calendar-month me-1"></i>
                            This Month
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                        <div class="stat-number">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->todayAttendance): ?>
                                <?php echo e($user->todayAttendance->check_in ? $user->todayAttendance->check_in->format('h:i A') : 'N/A'); ?>

                            <?php else: ?>
                                --
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="stat-label">
                            <i class="mdi mdi-clock-check me-1"></i>
                            Today Check-in
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="mdi mdi-history me-2"></i>
                        Recent Attendance
                    </h5>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->attendances()->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->attendances()->latest()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <i class="mdi mdi-calendar me-1 text-primary"></i>
                                                <?php echo e($attendance->created_at->format('M d, Y')); ?>

                                            </td>
                                            <td>
                                                <i class="mdi mdi-clock-in me-1 text-success"></i>
                                                <?php echo e($attendance->check_in ? $attendance->check_in->format('h:i A') : 'N/A'); ?>

                                            </td>
                                            <td>
                                                <i class="mdi mdi-clock-out me-1 text-danger"></i>
                                                <?php echo e($attendance->check_out ? $attendance->check_out->format('h:i A') : 'N/A'); ?>

                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->check_in && $attendance->check_out): ?>
                                                    <?php echo e($attendance->check_in->diffForHumans($attendance->check_out, true)); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Ongoing</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->check_out): ?>
                                                    <span class="badge bg-success">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">In Progress</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-remove display-4 text-muted"></i>
                            <p class="text-muted mt-3">No attendance records found</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="mdi mdi-information me-2"></i>
                        Account Information
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Account Created</div>
                            <div class="info-value">
                                <i class="mdi mdi-calendar-plus me-1 text-primary"></i>
                                <?php echo e($user->created_at->format('M d, Y h:i A')); ?>

                                <br>
                                <small class="text-muted"><?php echo e($user->created_at->diffForHumans()); ?></small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                <i class="mdi mdi-calendar-edit me-1 text-primary"></i>
                                <?php echo e($user->updated_at->format('M d, Y h:i A')); ?>

                                <br>
                                <small class="text-muted"><?php echo e($user->updated_at->diffForHumans()); ?></small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">User ID</div>
                            <div class="info-value">
                                <i class="mdi mdi-identifier me-1 text-primary"></i>
                                #<?php echo e(str_pad($user->id, 6, '0', STR_PAD_LEFT)); ?>

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Email Verified</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->email_verified_at): ?>
                                    <i class="mdi mdi-check-circle me-1 text-success"></i>
                                    <span class="text-success">Verified</span>
                                <?php else: ?>
                                    <i class="mdi mdi-close-circle me-1 text-danger"></i>
                                    <span class="text-danger">Not Verified</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/users/show.blade.php ENDPATH**/ ?>