<?php $__env->startSection('title', 'Public Holidays'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .ph-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .ph-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .ph-hdr h4 i { color: #d4af37; font-size: 1.2rem; }
    .ph-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }

    /* Upcoming holiday cards */
    .up-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .65rem; margin-bottom: .85rem; }
    .up-card {
        background: #fff; border-radius: .55rem; padding: .85rem;
        border: 1px solid rgba(0,0,0,.04);
        box-shadow: 0 1px 4px rgba(0,0,0,.03);
        display: flex; gap: .65rem; align-items: flex-start;
        transition: all .2s ease;
    }
    .up-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(212,175,55,.10); border-color: rgba(212,175,55,.18); }
    .up-date {
        min-width: 48px; text-align: center; padding: .35rem .25rem;
        background: linear-gradient(135deg, rgba(212,175,55,.08), rgba(232,200,74,.06));
        border-radius: .4rem; flex-shrink: 0;
    }
    .up-date .ud-day { font-size: 1.3rem; font-weight: 800; color: #d4af37; line-height: 1; }
    .up-date .ud-month { font-size: .55rem; text-transform: uppercase; letter-spacing: .5px; color: var(--bs-surface-500); font-weight: 700; }
    .up-info { flex: 1; min-width: 0; }
    .up-info h6 { font-size: .78rem; font-weight: 700; margin: 0 0 .15rem; }
    .up-info .up-desc { font-size: .68rem; color: var(--bs-surface-500); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .up-badge { font-size: .55rem; padding: .15rem .4rem; border-radius: 10px; background: rgba(85,110,230,.08); color: #556ee6; font-weight: 700; white-space: nowrap; }

    /* Toggle button */
    .toggle-btn {
        background: none; border: none; padding: 0; cursor: pointer;
        transition: opacity .2s;
    }
    .toggle-btn:hover { opacity: .75; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-check-all me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Header -->
    <div class="ph-hdr">
        <div>
            <h4><i class="bx bx-calendar-star"></i> Public Holidays</h4>
            <p>Manage holiday calendar for attendance calculations</p>
        </div>
        <a href="<?php echo e(route('admin.public-holidays.create')); ?>" class="act-btn a-success"><i class="mdi mdi-plus"></i> Add Holiday</a>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-calendar"></i></span>
            <span class="k-val"><?php echo e($holidays->total()); ?></span>
            <span class="k-lbl">Total Holidays</span>
        </div>
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-check-circle"></i></span>
            <span class="k-val"><?php echo e($upcomingHolidays->count()); ?></span>
            <span class="k-lbl">Upcoming</span>
        </div>
        <div class="kpi-card k-blue">
            <span class="k-icon"><i class="bx bx-calendar-check"></i></span>
            <span class="k-val"><?php echo e($holidays->where('is_active', true)->count()); ?></span>
            <span class="k-lbl">Active</span>
        </div>
    </div>

    <!-- Upcoming Holidays -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingHolidays->count() > 0): ?>
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="pipe-hdr">
            <i class="mdi mdi-calendar-star" style="color:#d4af37"></i> Upcoming Holidays
            <span class="badge-count"><?php echo e($upcomingHolidays->count()); ?></span>
        </div>
        <div class="sec-body">
            <div class="up-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upcomingHolidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="up-card">
                    <div class="up-date">
                        <div class="ud-day"><?php echo e($holiday->date->format('d')); ?></div>
                        <div class="ud-month"><?php echo e($holiday->date->format('M')); ?></div>
                    </div>
                    <div class="up-info">
                        <h6><?php echo e($holiday->name); ?></h6>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->description): ?>
                            <p class="up-desc" title="<?php echo e($holiday->description); ?>"><?php echo e(Str::limit($holiday->description, 60)); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="up-badge"><?php echo e($holiday->date->diffForHumans()); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- All Holidays Table -->
    <div class="ex-card sec-card">
        <div class="pipe-hdr">
            <i class="mdi mdi-calendar-multiple"></i> All Holidays
            <span class="badge-count"><?php echo e($holidays->total()); ?></span>
        </div>
        <div class="sec-body" style="padding:0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holidays->count() > 0): ?>
            <div class="table-responsive">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Date</th><th>Day</th><th>Holiday Name</th><th>Description</th><th>Status</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><span style="font-weight:700;font-size:.78rem"><?php echo e($holiday->date->format('d M Y')); ?></span></td>
                            <td><span class="v-badge v-blue"><?php echo e($holiday->date->format('l')); ?></span></td>
                            <td>
                                <span style="font-weight:600;font-size:.78rem"><?php echo e($holiday->name); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->date->isPast()): ?>
                                    <span class="s-pill s-closed" style="font-size:.5rem;margin-left:.3rem">Past</span>
                                <?php elseif($holiday->date->isToday()): ?>
                                    <span class="s-pill s-sale" style="font-size:.5rem;margin-left:.3rem">Today</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->description): ?>
                                    <span style="font-size:.72rem;color:var(--bs-surface-500)"><?php echo e(Str::limit($holiday->description, 50)); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <form class="d-inline" action="<?php echo e(route('admin.public-holidays.toggle', $holiday)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="toggle-btn">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($holiday->is_active): ?>
                                            <span class="s-pill s-sale">Active</span>
                                        <?php else: ?>
                                            <span class="s-pill s-closed">Inactive</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if(auth()->check() && auth()->user()->canEditModule('holidays')): ?>
                                    <a href="<?php echo e(route('admin.public-holidays.edit', $holiday)); ?>" class="act-btn a-primary" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <?php endif; ?>
                                    <?php if(auth()->check() && auth()->user()->canDeleteInModule('holidays')): ?>
                                    <form action="<?php echo e(route('admin.public-holidays.destroy', $holiday)); ?>" method="POST" onsubmit="return confirm('Delete this holiday?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="act-btn a-danger" title="Delete"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:.5rem .75rem"><?php echo e($holidays->links()); ?></div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="mdi mdi-calendar-remove" style="font-size:2rem;color:var(--bs-surface-300)"></i>
                <p class="text-muted mt-1" style="font-size:.78rem">No holidays configured yet.</p>
                <a href="<?php echo e(route('admin.public-holidays.create')); ?>" class="act-btn a-success"><i class="mdi mdi-plus"></i> Add Holiday</a>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/public-holidays/index.blade.php ENDPATH**/ ?>