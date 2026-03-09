<?php $__env->startSection('title'); ?>
    Allowed Devices
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .dv-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .dv-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .dv-hdr h5 i { color:var(--bs-gold,#d4af37) }

        .dv-table { width:100%;border-collapse:collapse;font-size:.78rem }
        .dv-table th { font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);padding:.45rem .75rem;border-bottom:1px solid rgba(0,0,0,.06);white-space:nowrap }
        .dv-table td { padding:.55rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle }
        .dv-table tr:last-child td { border-bottom:none }
        .dv-table tr:hover td { background:rgba(212,175,55,.03) }

        .tok-cell { font-family:"Courier New",monospace;font-size:.68rem;color:#68d391;background:rgba(0,0,0,.06);padding:.15rem .45rem;border-radius:6px;word-break:break-all }

        .add-card { border-radius:14px;padding:1rem 1.25rem;margin-bottom:.85rem;background:linear-gradient(135deg,rgba(212,175,55,.05),rgba(85,110,230,.03));border:1px solid rgba(212,175,55,.12) }
        .add-card h6 { font-size:.82rem;font-weight:700;margin:0 0 .65rem;display:flex;align-items:center;gap:.35rem }
        .add-card h6 i { color:#d4af37 }

        .pending-card { border-radius:14px;padding:1rem 1.25rem;margin-bottom:.85rem;background:rgba(241,180,76,.05);border:1px solid rgba(241,180,76,.2) }
        .pending-card h6 { font-size:.82rem;font-weight:700;margin:0 0 .65rem;display:flex;align-items:center;gap:.35rem;color:#b87a14 }

        .f-label { display:block;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.25rem }
        .f-input { border-radius:10px;border:1px solid rgba(0,0,0,.08);padding:.42rem .65rem;font-size:.78rem;width:100%;transition:all .15s;background:var(--bs-card-bg) }
        .f-input:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none }

        .how-it-works { border-radius:12px;padding:.75rem 1rem;background:rgba(85,110,230,.04);border:1px solid rgba(85,110,230,.1);margin-bottom:.85rem;font-size:.75rem;line-height:1.7;color:var(--bs-body-color) }
        .how-it-works strong { color:var(--bs-body-color) }
        .how-it-works code { background:rgba(0,0,0,.06);padding:.1rem .35rem;border-radius:4px;font-size:.7rem }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="dv-hdr">
        <h5><i class="bx bx-devices"></i> Allowed Devices</h5>
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

    
    <div class="how-it-works">
        <strong><i class="bx bx-info-circle" style="color:#556ee6"></i> How device access works:</strong>
        When someone visits the CRM from a new browser/machine, the server automatically assigns them a one-time <code>device token</code> (stored as an HttpOnly cookie — invisible to JavaScript). Their browser is then blocked until you approve that token here. Once approved, they can log in normally. Clearing cookies or using a different browser re-triggers approval.
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pending->isNotEmpty()): ?>
    <div class="pending-card">
        <h6><i class="bx bx-time"></i> Pending Approvals
            <span class="v-badge v-warn" style="margin-left:.25rem"><?php echo e($pending->count()); ?></span>
        </h6>
        <table class="dv-table">
            <thead>
                <tr>
                    <th>Device Token</th>
                    <th>Last IP</th>
                    <th>First Seen</th>
                    <th>Approve As</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><span class="tok-cell"><?php echo e($device->device_token); ?></span></td>
                    <td style="font-family:monospace;font-size:.7rem"><?php echo e($device->last_seen_ip ?? '—'); ?></td>
                    <td style="font-size:.72rem;color:var(--bs-surface-500)"><?php echo e($device->created_at->diffForHumans()); ?></td>
                    <td>
                        <form action="<?php echo e(route('settings.devices.approve', $device)); ?>" method="POST" style="display:flex;gap:.35rem;align-items:center">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="name" class="f-input" placeholder="Person name" style="width:130px" value="<?php echo e($device->name); ?>">
                            <input type="text" name="label" class="f-input" placeholder="Device label" style="width:160px" value="<?php echo e($device->label); ?>">
                            <button type="submit" class="act-btn a-green" style="font-size:.67rem;white-space:nowrap">
                                <i class="bx bx-check"></i> Approve
                            </button>
                        </form>
                    </td>
                    <td>
                        <form action="<?php echo e(route('settings.devices.destroy', $device)); ?>" method="POST" style="display:inline"
                            onsubmit="return confirm('Reject and delete this pending device?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="act-btn a-danger" style="font-size:.67rem;padding:.2rem .5rem">
                                <i class="bx bx-x"></i> Reject
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="add-card">
        <h6><i class="bx bx-plus-circle"></i> Manually Add Device Token</h6>
        <p style="font-size:.72rem;color:var(--bs-surface-500);margin-bottom:.65rem">
            If a device token was generated before you upgraded (or reported by a user), add it here directly.
        </p>
        <form action="<?php echo e(route('settings.devices.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:.55rem;align-items:flex-end">
                <div>
                    <label class="f-label">Device Token</label>
                    <input type="text" name="device_token" class="f-input <?php $__errorArgs = ['device_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                        value="<?php echo e(old('device_token')); ?>" required maxlength="100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['device_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:.67rem;color:#c84646;margin-top:.2rem"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="f-label">Assigned To (Person)</label>
                    <input type="text" name="name" class="f-input" placeholder="e.g. John Smith" value="<?php echo e(old('name')); ?>" maxlength="255">
                </div>
                <div>
                    <label class="f-label">Device Label</label>
                    <input type="text" name="label" class="f-input <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="e.g. Sales Floor PC #3"
                        value="<?php echo e(old('label')); ?>" required maxlength="255">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:.67rem;color:#c84646;margin-top:.2rem"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.38rem .85rem">
                        <i class="bx bx-check"></i> Add & Approve
                    </button>
                </div>
            </div>
        </form>
    </div>

    
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr">
            <h6><i class="bx bx-check-shield"></i> Approved Devices
                <span class="v-badge v-green" style="margin-left:.4rem"><?php echo e($approved->total()); ?></span>
            </h6>
        </div>
        <div class="sec-body" style="padding:0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approved->isEmpty()): ?>
                <div style="padding:2rem;text-align:center;font-size:.8rem;color:var(--bs-surface-500)">
                    <i class="bx bx-devices" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
                    No approved devices yet.
                </div>
            <?php else: ?>
                <div style="overflow-x:auto">
                    <table class="dv-table">
                        <thead>
                            <tr>
                                <th>Person</th>
                                <th>Label</th>
                                <th>Device Token</th>
                                <th>Last Seen</th>
                                <th>Last IP</th>
                                <th>Added By</th>
                                <th>Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-weight:600"><?php echo e($device->name ?? '—'); ?></td>
                                <td style="font-size:.72rem;color:var(--bs-surface-500)"><?php echo e($device->label); ?></td>
                                <td><span class="tok-cell"><?php echo e($device->device_token); ?></span></td>
                                <td style="color:var(--bs-surface-500);font-size:.72rem">
                                    <?php echo e($device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never'); ?>

                                </td>
                                <td style="font-family:monospace;font-size:.7rem;color:var(--bs-surface-500)">
                                    <?php echo e($device->last_seen_ip ?? '—'); ?>

                                </td>
                                <td style="font-size:.72rem"><?php echo e($device->addedBy?->name ?? '—'); ?></td>
                                <td style="color:var(--bs-surface-500);font-size:.72rem"><?php echo e($device->created_at->format('d M Y')); ?></td>
                                <td>
                                    <form action="<?php echo e(route('settings.devices.disable', $device)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="act-btn a-warn" style="font-size:.67rem;padding:.2rem .5rem">
                                            <i class="bx bx-block"></i> Disable
                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('settings.devices.destroy', $device)); ?>" method="POST" style="display:inline"
                                        onsubmit="return confirm('Remove device: <?php echo e(addslashes($device->label)); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="act-btn a-danger" style="font-size:.67rem;padding:.2rem .5rem">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approved->hasPages()): ?>
                    <div style="padding:.6rem 1rem"><?php echo e($approved->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($disabled->isNotEmpty()): ?>
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-block"></i> Disabled Devices
                <span class="v-badge v-red" style="margin-left:.4rem"><?php echo e($disabled->count()); ?></span>
            </h6>
        </div>
        <div class="sec-body" style="padding:0;overflow-x:auto">
            <table class="dv-table">
                <thead>
                    <tr><th>Person</th><th>Label</th><th>Token</th><th>Last IP</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $disabled; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($device->name ?? '—'); ?></td>
                        <td style="font-size:.72rem;color:var(--bs-surface-500)"><?php echo e($device->label); ?></td>
                        <td><span class="tok-cell"><?php echo e($device->device_token); ?></span></td>
                        <td style="font-family:monospace;font-size:.7rem;color:var(--bs-surface-500)"><?php echo e($device->last_seen_ip ?? '—'); ?></td>
                        <td>
                            <form action="<?php echo e(route('settings.devices.enable', $device)); ?>" method="POST" style="display:inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="act-btn a-green" style="font-size:.67rem;padding:.2rem .5rem">
                                    <i class="bx bx-check"></i> Re-enable
                                </button>
                            </form>
                            <form action="<?php echo e(route('settings.devices.destroy', $device)); ?>" method="POST" style="display:inline"
                                onsubmit="return confirm('Permanently delete this device record?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="act-btn a-danger" style="font-size:.67rem;padding:.2rem .5rem">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('title'); ?>
    Allowed Devices
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .dv-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .dv-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .dv-hdr h5 i { color:var(--bs-gold,#d4af37) }

        .dv-table { width:100%;border-collapse:collapse;font-size:.78rem }
        .dv-table th { font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);padding:.45rem .75rem;border-bottom:1px solid rgba(0,0,0,.06);white-space:nowrap }
        .dv-table td { padding:.55rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle }
        .dv-table tr:last-child td { border-bottom:none }
        .dv-table tr:hover td { background:rgba(212,175,55,.03) }

        .tok-cell { font-family:"Courier New",monospace;font-size:.72rem;color:#68d391;background:rgba(0,0,0,.06);padding:.15rem .45rem;border-radius:6px;word-break:break-all }

        .add-card { border-radius:14px;padding:1rem 1.25rem;margin-bottom:.85rem;background:linear-gradient(135deg,rgba(212,175,55,.05),rgba(85,110,230,.03));border:1px solid rgba(212,175,55,.12) }
        .add-card h6 { font-size:.82rem;font-weight:700;margin:0 0 .65rem;display:flex;align-items:center;gap:.35rem }
        .add-card h6 i { color:#d4af37 }

        .f-label { display:block;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.25rem }
        .f-input { border-radius:10px;border:1px solid rgba(0,0,0,.08);padding:.42rem .65rem;font-size:.78rem;width:100%;transition:all .15s;background:var(--bs-card-bg) }
        .f-input:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none }

        .my-tok-card { border-radius:12px;padding:.75rem 1rem;background:rgba(104,211,145,.06);border:1px solid rgba(104,211,145,.15);margin-bottom:.85rem }
        .my-tok-card h6 { font-size:.75rem;font-weight:700;color:#48bb78;margin:0 0 .3rem;display:flex;align-items:center;gap:.3rem }
        .my-tok-card code { font-size:.75rem;word-break:break-all;background:rgba(0,0,0,.06);padding:.2rem .5rem;border-radius:6px;cursor:pointer }
        .my-tok-card small { font-size:.68rem;color:var(--bs-surface-500) }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="dv-hdr">
        <h5><i class="bx bx-devices"></i> Allowed Devices</h5>
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

    
    <div class="my-tok-card">
        <h6><i class="bx bx-laptop"></i> Your Device Token (this browser)</h6>
        <div>
            <code id="my-token" onclick="copyMyToken()" title="Click to copy">Loading...</code>
            <button class="act-btn a-primary" style="font-size:.67rem;padding:.2rem .55rem;margin-left:.4rem" onclick="copyMyToken()">
                <i class="bx bx-copy"></i> Copy
            </button>
        </div>
        <small>This is the token for the current browser on this machine. You can add it below.</small>
    </div>

    
    <div class="add-card">
        <h6><i class="bx bx-plus-circle"></i> Add Allowed Device</h6>
        <form action="<?php echo e(route('settings.devices.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:.55rem;align-items:flex-end">
                <div>
                    <label class="f-label">Device Token</label>
                    <input type="text" name="device_token" id="token-input" class="f-input <?php $__errorArgs = ['device_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="dev_1234567890_abc123xyz"
                        value="<?php echo e(old('device_token')); ?>" required maxlength="100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['device_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:.67rem;color:#c84646;margin-top:.2rem"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="f-label">Assigned To (Person)</label>
                    <input type="text" name="name" class="f-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="e.g. John Smith"
                        value="<?php echo e(old('name')); ?>" maxlength="255">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:.67rem;color:#c84646;margin-top:.2rem"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="f-label">Label / Device Description</label>
                    <input type="text" name="label" class="f-input <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="e.g. Sales Floor PC #3"
                        value="<?php echo e(old('label')); ?>" required maxlength="255">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:.67rem;color:#c84646;margin-top:.2rem"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="display:flex;gap:.35rem">
                    <button type="button" class="act-btn a-warn" style="font-size:.72rem" onclick="pasteMyToken()">
                        <i class="bx bx-paste"></i> Paste Mine
                    </button>
                    <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.35rem .85rem">
                        <i class="bx bx-check" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Add
                    </button>
                </div>
            </div>
        </form>
    </div>

    
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-ul"></i> Approved Devices
                <span class="v-badge v-warn" style="margin-left:.4rem"><?php echo e($devices->total()); ?></span>
            </h6>
        </div>
        <div class="sec-body" style="padding:0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devices->isEmpty()): ?>
                <div style="padding:2rem;text-align:center;font-size:.8rem;color:var(--bs-surface-500)">
                    <i class="bx bx-devices" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
                    No devices approved yet. Add one above.
                </div>
            <?php else: ?>
                <div style="overflow-x:auto">
                    <table class="dv-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Assigned To</th>
                                <th>Label</th>
                                <th>Device Token</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th>Last IP</th>
                                <th>Added By</th>
                                <th>Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="color:var(--bs-surface-500);font-size:.7rem"><?php echo e($device->id); ?></td>
                                <td style="font-weight:600"><?php echo e($device->name ?? '—'); ?></td>
                                <td style="color:var(--bs-surface-500);font-size:.72rem"><?php echo e($device->label); ?></td>
                                <td><span class="tok-cell"><?php echo e($device->device_token); ?></span></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($device->is_active): ?>
                                        <span class="v-badge v-green">Active</span>
                                    <?php else: ?>
                                        <span class="v-badge v-red">Disabled</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="color:var(--bs-surface-500);font-size:.72rem">
                                    <?php echo e($device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never'); ?>

                                </td>
                                <td style="font-family:monospace;font-size:.7rem;color:var(--bs-surface-500)">
                                    <?php echo e($device->last_seen_ip ?? '—'); ?>

                                </td>
                                <td style="font-size:.72rem"><?php echo e($device->addedBy?->name ?? '—'); ?></td>
                                <td style="color:var(--bs-surface-500);font-size:.72rem"><?php echo e($device->created_at->format('d M Y')); ?></td>
                                <td>
                                    <form action="<?php echo e(route('settings.devices.toggle', $device)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="act-btn <?php echo e($device->is_active ? 'a-warn' : 'a-green'); ?>" style="font-size:.67rem;padding:.2rem .5rem"
                                            title="<?php echo e($device->is_active ? 'Disable' : 'Enable'); ?> this device">
                                            <i class="bx bx-<?php echo e($device->is_active ? 'block' : 'check'); ?>"></i>
                                            <?php echo e($device->is_active ? 'Disable' : 'Enable'); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('settings.devices.destroy', $device)); ?>" method="POST" style="display:inline"
                                        onsubmit="return confirm('Remove device: <?php echo e(addslashes($device->label)); ?>? This will immediately block access from that device.')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="act-btn a-danger" style="font-size:.67rem;padding:.2rem .5rem">
                                            <i class="bx bx-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($devices->hasPages()): ?>
                    <div style="padding:.6rem 1rem"><?php echo e($devices->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    // Read device_id from localStorage (same as device-fingerprint.js generates)
    (function(){
        var id = localStorage.getItem('device_id');
        if(!id){
            id = 'dev_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('device_id', id);
        }
        var el = document.getElementById('my-token');
        if(el) el.textContent = id;
        window._myDeviceToken = id;
    })();

    function copyMyToken(){
        if(!window._myDeviceToken) return;
        navigator.clipboard.writeText(window._myDeviceToken).then(function(){
            var el = document.getElementById('my-token');
            var orig = el.textContent;
            el.textContent = 'Copied!';
            setTimeout(function(){ el.textContent = orig; }, 2000);
        });
    }

    function pasteMyToken(){
        var input = document.getElementById('token-input');
        if(input && window._myDeviceToken){
            input.value = window._myDeviceToken;
            input.focus();
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/settings/devices.blade.php ENDPATH**/ ?>