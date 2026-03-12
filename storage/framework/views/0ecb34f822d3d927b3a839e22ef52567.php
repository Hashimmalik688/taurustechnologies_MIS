<?php $__env->startSection('title'); ?>
    System Settings
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .st-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .st-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .st-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .st-page-hdr .st-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        .f-label { display:block;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.25rem }
        .f-input { border-radius:12px;border:1px solid rgba(0,0,0,.08);padding:.45rem .65rem;font-size:.78rem;width:100%;transition:all .15s;background:var(--bs-card-bg) }
        .f-input:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none }
        select.f-input { appearance:auto }

        .f-switch { display:flex;align-items:center;gap:.5rem;padding:.35rem 0 }
        .f-switch input[type="checkbox"] { width:36px;height:20px;appearance:none;background:var(--bs-surface-300);border-radius:20px;position:relative;cursor:pointer;transition:background .2s;border:none;outline:none }
        .f-switch input[type="checkbox"]::after { content:'';position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:#fff;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.15) }
        .f-switch input[type="checkbox"]:checked { background:linear-gradient(135deg,#d4af37,#e8c84a) }
        .f-switch input[type="checkbox"]:checked::after { transform:translateX(16px) }
        .f-switch .sw-label { font-size:.75rem;font-weight:600;color:var(--bs-body-color) }

        .ip-card { border-radius:12px;padding:.85rem 1rem;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.04));border:1px solid rgba(212,175,55,.12);margin-bottom:.65rem }
        .ip-card h6 { font-size:.78rem;font-weight:700;color:#b89730;margin:0 0 .35rem }
        .ip-card code { font-size:.75rem;background:rgba(0,0,0,.04);padding:.15rem .4rem;border-radius:6px }

        .net-input-row { display:flex;gap:.35rem;margin-bottom:.35rem }
        .net-input-row input { flex:1 }
        .net-rm { border:1px solid rgba(244,106,106,.2);background:rgba(244,106,106,.04);color:#c84646;border-radius:8px;padding:.3rem .5rem;cursor:pointer;font-size:.72rem;transition:all .15s }
        .net-rm:hover { background:rgba(244,106,106,.12) }

        .grp-card { margin-bottom:.65rem }
        .grp-title { display:flex;align-items:center;gap:.35rem;font-size:.82rem;font-weight:700;color:var(--bs-body-color) }
        .grp-title i { color:#d4af37;font-size:1rem }

        .f-help { font-size:.65rem;color:var(--bs-surface-500);margin-top:.2rem }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="st-page-hdr">
        <h5>
            <i class="bx bx-cog"></i> System Settings
            <span class="st-sub">Configuration</span>
        </h5>
        <a href="<?php echo e(route('settings.hub')); ?>" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Settings Hub
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

    
    

    <form action="<?php echo e(route('settings.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupSettings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="ex-card sec-card grp-card">
                <div class="sec-hdr">
                    <h6>
                        <i class="bx bx-<?php echo e($group === 'attendance' ? 'time-five' : 'cog'); ?>"></i>
                        <?php echo e(ucwords(str_replace('_', ' ', $group))); ?> Settings
                    </h6>
                </div>
                <div class="sec-body" style="padding:.75rem">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->key !== 'late_threshold_minutes' && $setting->key !== 'office_networks'): ?>
                            <div style="<?php echo e($setting->type === 'array' ? 'grid-column:1/-1' : ''); ?>">
                                <label class="f-label">
                                    <?php echo e(ucwords(str_replace('_', ' ', str_replace($group . '_', '', $setting->key)))); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->description): ?>
                                        <i class="bx bx-info-circle" data-bs-toggle="tooltip" title="<?php echo e($setting->description); ?>" style="cursor:help;opacity:.5"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->type === 'boolean'): ?>
                                    <div class="f-switch">
                                        <input type="checkbox" id="<?php echo e($setting->key); ?>" name="settings[<?php echo e($setting->key); ?>]" <?php echo e($setting->value === 'true' ? 'checked' : ''); ?>>
                                        <span class="sw-label"><?php echo e($setting->value === 'true' ? 'Enabled' : 'Disabled'); ?></span>
                                    </div>
                                <?php elseif($setting->type === 'array' && $setting->key === 'office_networks'): ?>
                                    <div id="network-inputs">
                                        <?php
                                            $networks = is_string($setting->value) ? explode(',', $setting->value) : [$setting->value];
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="net-input-row network-input-group">
                                                <input type="text" class="f-input" name="settings[<?php echo e($setting->key); ?>][]" value="<?php echo e(trim($network)); ?>" placeholder="192.168.1.0/24">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index > 0): ?>
                                                    <button class="net-rm" type="button" onclick="removeNetwork(this)"><i class="bx bx-trash"></i></button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <button type="button" class="act-btn a-primary" style="margin-top:.2rem" onclick="addNetwork()"><i class="bx bx-plus"></i> Add Network</button>
                                    <div class="f-help">Enter IP addresses or CIDR ranges (e.g., 192.168.1.0/24)</div>
                                <?php else: ?>
                                    <input type="<?php echo e(in_array($setting->key, ['office_start_time','office_end_time','late_time']) ? 'time' : 'text'); ?>"
                                        class="f-input" id="<?php echo e($setting->key); ?>" name="settings[<?php echo e($setting->key); ?>]"
                                        value="<?php echo e($setting->value); ?>"
                                        <?php echo e($setting->key === 'shift_duration_hours' ? 'readonly style=background:var(--bs-surface-100);cursor:not-allowed title=Auto-calculated from start/end time' : ''); ?>

                                        <?php echo e(in_array($setting->key, ['office_start_time','office_end_time']) ? 'onchange=calcShiftHours()' : ''); ?>>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->description && $setting->type !== 'boolean' && $setting->type !== 'array'): ?>
                                    <div class="f-help"><?php echo e($setting->description); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div style="display:flex;justify-content:flex-end;gap:.45rem;margin-top:.35rem">
            <button type="button" class="act-btn a-danger" onclick="resetForm()"><i class="bx bx-reset"></i> Reset</button>
            <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.32rem .85rem">
                <i class="bx bx-save" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Save Settings
            </button>
        </div>
    </form>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canViewModule('allowed-devices')): ?>
    <div class="ex-card sec-card" style="margin-top:.65rem">
        <div class="sec-hdr">
            <h6><i class="bx bx-devices"></i> Allowed Devices
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pending->count()): ?>
                    <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem"><?php echo e($pending->count()); ?> pending</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h6>
        </div>
        <div class="sec-body" style="padding:.75rem">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pending->count()): ?>
                <p class="f-label" style="color:#b89730;margin-bottom:.4rem">Pending Approval</p>
                <div class="table-responsive" style="margin-bottom:1rem">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>Token</th><th>IP</th><th>First Seen</th><th></th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><code style="font-size:.7rem"><?php echo e($d->device_token); ?></code></td>
                                <td><?php echo e($d->last_seen_ip ?? '—'); ?></td>
                                <td><?php echo e($d->created_at->diffForHumans()); ?></td>
                                <td style="white-space:nowrap">
                                    <form action="<?php echo e(route('settings.devices.approve', $d)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="text" name="name" placeholder="Person" class="f-input" style="width:90px;display:inline-block;margin-right:.2rem">
                                        <input type="text" name="label" placeholder="Label" class="f-input" style="width:90px;display:inline-block;margin-right:.2rem">
                                        <button class="act-btn a-success" style="font-size:.68rem;padding:.2rem .45rem">Approve</button>
                                    </form>
                                    <form action="<?php echo e(route('settings.devices.destroy', $d)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Reject this device?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="act-btn a-danger" style="font-size:.68rem;padding:.2rem .45rem">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <form action="<?php echo e(route('settings.devices.store')); ?>" method="POST" style="margin-bottom:1rem">
                <?php echo csrf_field(); ?>
                <p class="f-label" style="margin-bottom:.4rem">Add a Device Manually</p>
                <div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:flex-end">
                    <div style="flex:2;min-width:180px">
                        <label class="f-label">Device Token (UUID)</label>
                        <input type="text" name="device_token" class="f-input" placeholder="Paste UUID from the pending page" required>
                    </div>
                    <div style="flex:1;min-width:120px">
                        <label class="f-label">Person</label>
                        <input type="text" name="name" class="f-input" placeholder="e.g. Hashim">
                    </div>
                    <div style="flex:1;min-width:120px">
                        <label class="f-label">Label</label>
                        <input type="text" name="label" class="f-input" placeholder="e.g. Office PC" required>
                    </div>
                    <div>
                        <button class="act-btn a-success" style="white-space:nowrap"><i class="bx bx-plus"></i> Add &amp; Approve</button>
                    </div>
                </div>
            </form>

            
            <p class="f-label" style="margin-bottom:.4rem">Approved Devices</p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approved->isEmpty()): ?>
                <p style="font-size:.75rem;color:var(--bs-surface-500)">No approved devices yet.</p>
            <?php else: ?>
                <div class="table-responsive" style="margin-bottom:1rem">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>Person</th><th>Label</th><th>Last Seen</th><th>IP</th><th style="width:1px"></th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <tr id="dev-row-<?php echo e($d->id); ?>">
                                <td><?php echo e($d->name ?? '—'); ?></td>
                                <td><?php echo e($d->label); ?></td>
                                <td style="color:var(--bs-surface-500)"><?php echo e($d->last_seen_at ? $d->last_seen_at->diffForHumans() : '—'); ?></td>
                                <td style="color:var(--bs-surface-500)"><?php echo e($d->last_seen_ip ?? '—'); ?></td>
                                <td style="white-space:nowrap;text-align:right">
                                    <button class="act-btn" style="font-size:.68rem;padding:.2rem .5rem" onclick="devEditOpen(<?php echo e($d->id); ?>)">
                                        <i class="bx bx-pencil" style="font-size:.8rem;vertical-align:middle"></i> Edit
                                    </button>
                                    <form action="<?php echo e(route('settings.devices.disable', $d)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="act-btn a-warning" style="font-size:.68rem;padding:.2rem .5rem">Disable</button>
                                    </form>
                                    <form action="<?php echo e(route('settings.devices.destroy', $d)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Remove this device?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="act-btn a-danger" style="font-size:.68rem;padding:.2rem .5rem">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            
                            <tr id="dev-edit-<?php echo e($d->id); ?>" style="display:none">
                                <td colspan="5" style="padding:.6rem .75rem;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.03));border-bottom:2px solid rgba(212,175,55,.2)">
                                    <form action="<?php echo e(route('settings.devices.update', $d)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr 2fr;gap:.5rem;align-items:end">
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Person</div>
                                                <input type="text" name="name" class="f-input" value="<?php echo e($d->name); ?>" placeholder="e.g. Hashim" style="margin:0">
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Label / Device</div>
                                                <input type="text" name="label" class="f-input" value="<?php echo e($d->label); ?>" placeholder="e.g. Office PC" style="margin:0" required>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Status</div>
                                                <select name="status" class="f-input" style="margin:0">
                                                    <option value="approved" <?php echo e($d->status === 'approved' ? 'selected' : ''); ?>>Approved</option>
                                                    <option value="disabled" <?php echo e($d->status === 'disabled' ? 'selected' : ''); ?>>Disabled</option>
                                                    <option value="pending"  <?php echo e($d->status === 'pending'  ? 'selected' : ''); ?>>Pending</option>
                                                    <option value="rejected" <?php echo e($d->status === 'rejected' ? 'selected' : ''); ?>>Rejected (permanent block)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Device Token (UUID)</div>
                                                <input type="text" name="device_token" class="f-input" value="<?php echo e($d->device_token); ?>" style="margin:0;font-family:monospace;font-size:.7rem" required>
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:.3rem;margin-top:.5rem;justify-content:flex-end">
                                            <button class="pipe-pill-apply" style="font-size:.72rem;padding:.28rem .7rem">
                                                <i class="bx bx-check" style="font-size:.85rem;vertical-align:middle"></i> Save
                                            </button>
                                            <button type="button" class="act-btn a-danger" style="font-size:.72rem;padding:.28rem .7rem" onclick="devEditClose(<?php echo e($d->id); ?>)">
                                                <i class="bx bx-x" style="font-size:.85rem;vertical-align:middle"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($disabled->count()): ?>
                <p class="f-label" style="margin-bottom:.4rem">Disabled Devices</p>
                <div class="table-responsive" style="margin-bottom:1rem">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>Person</th><th>Label</th><th style="width:1px"></th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $disabled; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr id="dev-row-<?php echo e($d->id); ?>">
                                <td><?php echo e($d->name ?? '—'); ?></td>
                                <td><?php echo e($d->label); ?></td>
                                <td style="white-space:nowrap;text-align:right">
                                    <button class="act-btn" style="font-size:.68rem;padding:.2rem .5rem" onclick="devEditOpen(<?php echo e($d->id); ?>)">
                                        <i class="bx bx-pencil" style="font-size:.8rem;vertical-align:middle"></i> Edit
                                    </button>
                                    <form action="<?php echo e(route('settings.devices.enable', $d)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="act-btn a-success" style="font-size:.68rem;padding:.2rem .5rem">Re-enable</button>
                                    </form>
                                    <form action="<?php echo e(route('settings.devices.destroy', $d)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Remove this device?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="act-btn a-danger" style="font-size:.68rem;padding:.2rem .5rem">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="dev-edit-<?php echo e($d->id); ?>" style="display:none">
                                <td colspan="3" style="padding:.6rem .75rem;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.03));border-bottom:2px solid rgba(212,175,55,.2)">
                                    <form action="<?php echo e(route('settings.devices.update', $d)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr 2fr;gap:.5rem;align-items:end">
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Person</div>
                                                <input type="text" name="name" class="f-input" value="<?php echo e($d->name); ?>" placeholder="e.g. Hashim" style="margin:0">
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Label / Device</div>
                                                <input type="text" name="label" class="f-input" value="<?php echo e($d->label); ?>" placeholder="e.g. Office PC" style="margin:0" required>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Status</div>
                                                <select name="status" class="f-input" style="margin:0">
                                                    <option value="approved" <?php echo e($d->status === 'approved' ? 'selected' : ''); ?>>Approved</option>
                                                    <option value="disabled" <?php echo e($d->status === 'disabled' ? 'selected' : ''); ?>>Disabled</option>
                                                    <option value="pending"  <?php echo e($d->status === 'pending'  ? 'selected' : ''); ?>>Pending</option>
                                                    <option value="rejected" <?php echo e($d->status === 'rejected' ? 'selected' : ''); ?>>Rejected (permanent block)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Device Token (UUID)</div>
                                                <input type="text" name="device_token" class="f-input" value="<?php echo e($d->device_token); ?>" style="margin:0;font-family:monospace;font-size:.7rem" required>
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:.3rem;margin-top:.5rem;justify-content:flex-end">
                                            <button class="pipe-pill-apply" style="font-size:.72rem;padding:.28rem .7rem">
                                                <i class="bx bx-check" style="font-size:.85rem;vertical-align:middle"></i> Save
                                            </button>
                                            <button type="button" class="act-btn a-danger" style="font-size:.72rem;padding:.28rem .7rem" onclick="devEditClose(<?php echo e($d->id); ?>)">
                                                <i class="bx bx-x" style="font-size:.85rem;vertical-align:middle"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rejected->count()): ?>
                <p class="f-label" style="margin-bottom:.4rem;color:#dc3545">Rejected Devices <span style="font-weight:400;font-size:.7rem;color:var(--bs-surface-500)">(permanently blocked — these tokens cannot log in ever again)</span></p>
                <div class="table-responsive" style="margin-bottom:1rem">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>Person</th><th>Label</th><th>Token</th><th style="width:1px"></th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rejected; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="opacity:.7">
                                <td><?php echo e($d->name ?? '—'); ?></td>
                                <td><?php echo e($d->label); ?></td>
                                <td style="font-family:monospace;font-size:.68rem"><?php echo e($d->device_token); ?></td>
                                <td style="white-space:nowrap;text-align:right">
                                    <form action="<?php echo e(route('settings.devices.update', $d)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Restore this device to Approved?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="name" value="<?php echo e($d->name); ?>">
                                        <input type="hidden" name="label" value="<?php echo e($d->label); ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <input type="hidden" name="device_token" value="<?php echo e($d->device_token); ?>">
                                        <button class="act-btn a-success" style="font-size:.68rem;padding:.2rem .5rem">Restore</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

        function addNetwork() {
            const c = document.getElementById('network-inputs');
            const d = document.createElement('div');
            d.className = 'net-input-row network-input-group';
            d.innerHTML = '<input type="text" class="f-input" name="settings[office_networks][]" placeholder="192.168.1.0/24"><button class="net-rm" type="button" onclick="removeNetwork(this)"><i class="bx bx-trash"></i></button>';
            c.appendChild(d);
        }

        function removeNetwork(btn) { btn.closest('.network-input-group').remove(); }
        function resetForm() { if (confirm('Reset all changes?')) location.reload(); }

        function calcShiftHours() {
            var start = document.getElementById('office_start_time');
            var end   = document.getElementById('office_end_time');
            var dur   = document.getElementById('shift_duration_hours');
            if (!start || !end || !dur) return;
            var sv = start.value, ev = end.value;
            if (!sv || !ev) return;
            var sm = parseInt(sv.split(':')[0])*60 + parseInt(sv.split(':')[1]);
            var em = parseInt(ev.split(':')[0])*60 + parseInt(ev.split(':')[1]);
            var diff = em - sm;
            if (diff <= 0) return;
            dur.value = (diff / 60).toFixed(1).replace(/\.0$/, '');
        }
        // Run on load so it reflects current values
        document.addEventListener('DOMContentLoaded', calcShiftHours);

        function toggleDevEdit(id) {
            var row = document.getElementById('dev-edit-' + id);
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
        function devEditOpen(id) {
            document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
            var editRow = document.getElementById('dev-edit-' + id);
            editRow.style.display = 'table-row';
            editRow.querySelector('input').focus();
        }
        function devEditClose(id) {
            document.getElementById('dev-edit-' + id).style.display = 'none';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
        });
        function devEditOpen(id) {
            // close any other open edit rows first
            document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
            var editRow = document.getElementById('dev-edit-' + id);
            editRow.style.display = 'table-row';
            editRow.querySelector('input').focus();
        }
        function devEditClose(id) {
            document.getElementById('dev-edit-' + id).style.display = 'none';
        }
        // Escape key closes any open edit row
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    const lbl = this.nextElementSibling;
                    if (lbl) lbl.textContent = this.checked ? 'Enabled' : 'Disabled';
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>