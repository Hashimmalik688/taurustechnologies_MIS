<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title'); ?>
    Users Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link href="<?php echo e(URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        .u-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .u-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .u-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .u-page-hdr .u-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background:linear-gradient(135deg,#d4af37,#e8c84a)!important;
            border-color:#d4af37!important;color:#fff!important;border-radius:6px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background:rgba(212,175,55,.08)!important;border-color:rgba(212,175,55,.2)!important;
            color:#b89730!important;border-radius:6px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius:22px;border:1px solid rgba(0,0,0,.08);padding:.32rem .55rem;font-size:.75rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius:22px;border:1px solid rgba(0,0,0,.08);padding:.25rem .5rem;font-size:.72rem;
        }
        .dataTables_wrapper .dataTables_info { font-size:.72rem;color:var(--bs-surface-500) }

        .pw-inline {
            font-size:.72rem;padding:.22rem .45rem;border-radius:8px;
            border:1px solid rgba(0,0,0,.06);background:var(--bs-card-bg);
            width:130px;transition:border-color .15s;
        }
        .pw-inline:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none }

        .f-label { display:block;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.25rem }
        .f-input { border-radius:12px;border:1px solid rgba(0,0,0,.08);padding:.45rem .65rem;font-size:.78rem;width:100%;transition:all .15s;background:var(--bs-card-bg) }
        .f-input:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none }
        textarea.f-input { resize:vertical;min-height:60px }
        select.f-input { appearance:auto }

        .role-grid { display:grid;grid-template-columns:1fr 1fr;gap:.15rem .75rem }
        .role-check { display:flex;align-items:center;gap:.35rem;padding:.2rem 0;font-size:.78rem }
        .role-check input[type="checkbox"] { width:15px;height:15px;border-radius:4px;accent-color:#d4af37;cursor:pointer }
        .role-check label { cursor:pointer;font-weight:500;color:var(--bs-body-color) }
        .role-section-lbl { font-size:.65rem;font-weight:700;color:#556ee6;text-transform:uppercase;letter-spacing:.3px;margin-top:.35rem;margin-bottom:.15rem;grid-column:1/-1 }

        .del-warn { text-align:center;padding:1.25rem;font-size:.82rem;color:var(--bs-body-color) }
        .del-warn i { font-size:2.5rem;color:#f46a6a;display:block;margin-bottom:.5rem }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="u-page-hdr">
        <h5>
            <i class="bx bx-group"></i> Users Management
            <span class="u-sub"><?php echo e(count($users)); ?> users</span>
        </h5>
        <a href="<?php echo e(route('users.create')); ?>" class="act-btn a-success" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-plus"></i> Add User
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:10px;border:none;background:rgba(52,195,143,.08);color:#1a8754">
            <i class="bx bx-check-circle me-1"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:10px;border:none;background:rgba(244,106,106,.08);color:#c84646">
            <i class="bx bx-error-circle me-1"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="kpi-row">
        <div class="ex-card kpi-card k-gold">
            <i class="bx bx-group k-icon"></i>
            <div class="k-val"><?php echo e(count($users)); ?></div>
            <div class="k-lbl">Total Users</div>
        </div>
        <div class="ex-card kpi-card k-green">
            <i class="bx bx-check-circle k-icon"></i>
            <div class="k-val"><?php echo e($users->where('status','active')->count()); ?></div>
            <div class="k-lbl">Active</div>
        </div>
        <div class="ex-card kpi-card k-warn">
            <i class="bx bx-pause-circle k-icon"></i>
            <div class="k-val"><?php echo e($users->where('status','inactive')->count()); ?></div>
            <div class="k-lbl">Inactive</div>
        </div>
        <div class="ex-card kpi-card k-red">
            <i class="bx bx-block k-icon"></i>
            <div class="k-val"><?php echo e($users->where('status','suspended')->count()); ?></div>
            <div class="k-lbl">Suspended</div>
        </div>
    </div>

    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-table"></i> All Users</h6>
            <span class="badge-count" style="font-size:.6rem;padding:.15rem .45rem;border-radius:1rem;background:rgba(212,175,55,.12);color:#b89730"><?php echo e(count($users)); ?></span>
        </div>
        <div class="sec-body" style="padding:.5rem .65rem">
            <table id="datatable" class="ex-tbl w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>DOB</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($user->id); ?></td>
                            <td style="font-weight:600"><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->userDetail->phone ?? '—'); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <input type="text" class="pw-inline password-field"
                                       data-user-id="<?php echo e($user->id); ?>"
                                       value="<?php echo e($user->userDetail->plain_password ?? ''); ?>"
                                       placeholder="Set password">
                            </td>
                            <td><?php echo e($user->userDetail && $user->userDetail->dob ? \Carbon\Carbon::parse($user->userDetail->dob)->format('d M Y') : '—'); ?></td>
                            <td><?php echo e($user->userDetail && $user->userDetail->join_date ? \Carbon\Carbon::parse($user->userDetail->join_date)->format('d M Y') : '—'); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status === 'active'): ?>
                                    <span class="s-pill s-sale">Active</span>
                                <?php elseif($user->status === 'inactive'): ?>
                                    <span class="s-pill s-pending">Inactive</span>
                                <?php elseif($user->status === 'suspended'): ?>
                                    <span class="s-pill s-declined">Suspended</span>
                                <?php else: ?>
                                    <span class="s-pill" style="background:rgba(108,117,125,.1);color:#6c757d">Unknown</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="v-badge v-blue" style="margin:1px 0"><?php echo e($role->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="white-space:nowrap">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin')): ?>
                                    <a href="<?php echo e(route('settings.permissions.users.edit', $user)); ?>" class="act-btn a-warning" title="Manage Permissions" style="color:#d4af37">
                                        <i class="bx bx-shield-alt"></i>
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(auth()->check() && auth()->user()->canEditModule('users')): ?>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#edit-user-<?php echo e($user->id); ?>" class="act-btn a-primary" title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->check() && auth()->user()->canDeleteInModule('users')): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->id !== auth()->id()): ?>
                                        <button type="button" class="act-btn a-danger" onclick="confirmDelete(<?php echo e($user->id); ?>)" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="edit-user-<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" style="max-width:600px">
                <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
                    <div class="modal-header modal-header-glass">
                        <h5 class="modal-title" style="font-size:.85rem;font-weight:700">
                            <i class="bx bx-edit-alt" style="color:#d4af37;margin-right:.3rem"></i> Edit — <?php echo e($user->name); ?>

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.6rem"></button>
                    </div>
                    <form action="<?php echo e(route('users.update', $user->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="modal-body" style="max-height:70vh;overflow-y:auto;padding:1rem 1.25rem">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem">
                                <div>
                                    <label class="f-label">Name</label>
                                    <input type="text" class="f-input" name="name" value="<?php echo e($user->name); ?>" required>
                                </div>
                                <div>
                                    <label class="f-label">Email</label>
                                    <input type="email" class="f-input" name="email" value="<?php echo e($user->email); ?>" required>
                                </div>
                                <div>
                                    <label class="f-label">Status</label>
                                    <select class="f-input" name="status" required>
                                        <option value="active" <?php echo e($user->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                        <option value="inactive" <?php echo e($user->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                        <option value="suspended" <?php echo e($user->status === 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="f-label">Phone</label>
                                    <input type="text" class="f-input" name="phone" value="<?php echo e($user->userDetail->phone ?? ''); ?>">
                                </div>
                                <div style="grid-column:1/-1">
                                    <label class="f-label">Password (Plaintext Reference)</label>
                                    <input type="text" class="f-input" name="plain_password" value="<?php echo e($user->userDetail->plain_password ?? ''); ?>" placeholder="For reference only">
                                </div>
                            </div>

                            <div style="margin-top:.75rem">
                                <label class="f-label">Roles</label>
                                <?php $currentRoles = $user->roles->pluck('name')->toArray(); ?>
                                <div class="role-grid" style="background:rgba(0,0,0,.015);border-radius:10px;padding:.5rem .65rem">
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::SUPER_ADMIN); ?>" id="mr-sa-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::SUPER_ADMIN, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-sa-<?php echo e($user->id); ?>">Super Admin</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::MANAGER); ?>" id="mr-mg-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::MANAGER, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-mg-<?php echo e($user->id); ?>">Manager</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::HR); ?>" id="mr-hr-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::HR, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-hr-<?php echo e($user->id); ?>">HR</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::EMPLOYEE); ?>" id="mr-em-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::EMPLOYEE, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-em-<?php echo e($user->id); ?>">Employee</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::COORDINATOR); ?>" id="mr-co-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::COORDINATOR, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-co-<?php echo e($user->id); ?>">Co-ordinator</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::CEO); ?>" id="mr-ceo-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::CEO, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-ceo-<?php echo e($user->id); ?>">CEO</label>
                                    </div>
                                    <div class="role-section-lbl">Peregrine Team</div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::PEREGRINE_CLOSER); ?>" id="mr-pc-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::PEREGRINE_CLOSER, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-pc-<?php echo e($user->id); ?>">Peregrine Closer</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::PEREGRINE_VALIDATOR); ?>" id="mr-pv-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::PEREGRINE_VALIDATOR, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-pv-<?php echo e($user->id); ?>">Peregrine Validator</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::VERIFIER); ?>" id="mr-vr-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::VERIFIER, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-vr-<?php echo e($user->id); ?>">Verifier</label>
                                    </div>
                                    <div class="role-section-lbl">Other Roles</div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::RAVENS_CLOSER); ?>" id="mr-rc-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::RAVENS_CLOSER, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-rc-<?php echo e($user->id); ?>">Ravens Closer</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::RETENTION_OFFICER); ?>" id="mr-ro-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::RETENTION_OFFICER, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-ro-<?php echo e($user->id); ?>">Retention Officer</label>
                                    </div>
                                    <div class="role-check">
                                        <input type="checkbox" name="roles[]" value="<?php echo e(Roles::QA); ?>" id="mr-qa-<?php echo e($user->id); ?>" <?php echo e(in_array(Roles::QA, $currentRoles) ? 'checked' : ''); ?>>
                                        <label for="mr-qa-<?php echo e($user->id); ?>">QA</label>
                                    </div>
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;margin-top:.75rem">
                                <div>
                                    <label class="f-label">Login Password</label>
                                    <input type="password" class="f-input" name="password" placeholder="Leave blank to keep">
                                </div>
                                <div>
                                    <label class="f-label">Confirm Password</label>
                                    <input type="password" class="f-input" name="password_confirmation" placeholder="Confirm new password">
                                </div>
                                <div>
                                    <label class="f-label">Date of Birth</label>
                                    <input type="text" class="f-input sl-pill-date" name="dob" value="<?php echo e($user->userDetail && $user->userDetail->dob ? \Carbon\Carbon::parse($user->userDetail->dob)->format('Y-m-d') : ''); ?>" placeholder="Select date">
                                </div>
                                <div>
                                    <label class="f-label">Gender</label>
                                    <select class="f-input" name="gender">
                                        <option value="">Select</option>
                                        <option value="Male" <?php echo e(($user->userDetail->gender ?? '') == 'Male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="Female" <?php echo e(($user->userDetail->gender ?? '') == 'Female' ? 'selected' : ''); ?>>Female</option>
                                        <option value="Other" <?php echo e(($user->userDetail->gender ?? '') == 'Other' ? 'selected' : ''); ?>>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="f-label">Join Date</label>
                                    <input type="text" class="f-input sl-pill-date" name="join_date" value="<?php echo e($user->userDetail && $user->userDetail->join_date ? \Carbon\Carbon::parse($user->userDetail->join_date)->format('Y-m-d') : ''); ?>" placeholder="Select date">
                                </div>
                                <div>
                                    <label class="f-label">City</label>
                                    <input type="text" class="f-input" name="city" value="<?php echo e($user->userDetail->city ?? ''); ?>">
                                </div>
                                <div style="grid-column:1/-1">
                                    <label class="f-label">Address</label>
                                    <textarea class="f-input" name="address" rows="2"><?php echo e($user->userDetail->address ?? ''); ?></textarea>
                                </div>
                                <div>
                                    <label class="f-label">Zoom Number</label>
                                    <input type="text" class="f-input" name="zoom_number" value="<?php echo e($user->zoom_number ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top:1px solid rgba(0,0,0,.05);padding:.65rem 1.25rem">
                            <button type="button" class="act-btn a-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.32rem .75rem">
                                <i class="bx bx-save" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
                <div class="modal-header modal-header-glass">
                    <h5 class="modal-title" style="font-size:.85rem;font-weight:700">
                        <i class="bx bx-trash" style="color:#f46a6a;margin-right:.3rem"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:.6rem"></button>
                </div>
                <div class="modal-body">
                    <div class="del-warn">
                        <i class="bx bx-error-circle"></i>
                        Are you sure you want to delete this user?<br>
                        <small style="color:var(--bs-surface-500);font-size:.72rem">This action cannot be undone.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(0,0,0,.05);padding:.65rem 1rem;justify-content:center;gap:.5rem">
                    <button type="button" class="act-btn a-primary" data-bs-dismiss="modal" style="padding:.3rem .75rem">Cancel</button>
                    <form class="d-inline" id="deleteForm" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="act-btn a-danger" style="padding:.3rem .75rem">
                            <i class="bx bx-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script>
        function confirmDelete(userId) {
            document.getElementById('deleteForm').action = '/users/delete/' + userId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            $('#datatable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                language: { search: '', searchPlaceholder: 'Search users...' }
            });

            document.querySelectorAll('.password-field').forEach(field => {
                field.addEventListener('blur', function() {
                    const userId = this.dataset.userId;
                    const el = this;

                    fetch('/users/' + userId + '/update-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ plain_password: this.value })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            el.style.borderColor = '#34c38f';
                            setTimeout(() => { el.style.borderColor = ''; }, 1200);
                        }
                    })
                    .catch(() => {
                        el.style.borderColor = '#f46a6a';
                        setTimeout(() => { el.style.borderColor = ''; }, 1200);
                    });
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/users/index.blade.php ENDPATH**/ ?>