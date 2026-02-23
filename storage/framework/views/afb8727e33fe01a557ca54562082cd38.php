<?php $__env->startSection('title'); ?> Partners Management <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ─── Partners Page ─── */
.pt-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.pt-page-hdr h5 { font-weight:800; font-size:1.1rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.pt-page-hdr .pt-sub { font-size:.7rem; font-weight:500; color:var(--bs-surface-500); margin-left:.25rem; }
.pt-add-btn { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)); color:#fff; border:none; padding:.45rem 1rem; border-radius:.5rem; font-size:.72rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem; text-decoration:none; transition:all .2s; box-shadow:0 2px 8px rgba(102,126,234,.25); }
.pt-add-btn:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(102,126,234,.35); color:#fff; }

/* KPI Row */
.pt-kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:.65rem; margin-bottom:1.25rem; }
.pt-kpi { background:var(--bs-card-bg); border-radius:.65rem; padding:.85rem 1rem; position:relative; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.pt-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:.65rem .65rem 0 0; }
.pt-kpi.k-blue::before { background:linear-gradient(90deg,var(--bs-gradient-start),var(--bs-gradient-end)); }
.pt-kpi.k-green::before { background:linear-gradient(90deg,#34c38f,#38ef7d); }
.pt-kpi.k-orange::before { background:linear-gradient(90deg,#f5b041,#f39c12); }
.pt-kpi.k-purple::before { background:linear-gradient(90deg,#764ba2,#667eea); }
.pt-kpi .k-icon { font-size:1.5rem; opacity:.15; position:absolute; right:.75rem; top:.75rem; }
.pt-kpi.k-blue .k-icon,.pt-kpi.k-blue .k-val { color:var(--bs-gradient-start); }
.pt-kpi.k-green .k-icon,.pt-kpi.k-green .k-val { color:#34c38f; }
.pt-kpi.k-orange .k-icon,.pt-kpi.k-orange .k-val { color:#f5b041; }
.pt-kpi.k-purple .k-icon,.pt-kpi.k-purple .k-val { color:#764ba2; }
.pt-kpi .k-val { font-size:1.5rem; font-weight:800; line-height:1; }
.pt-kpi .k-lbl { font-size:.62rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--bs-surface-500); margin-top:.25rem; }

/* Table Card */
.pt-card { background:var(--bs-card-bg); border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.04); overflow:hidden; }
.pt-card-hdr { padding:.75rem 1rem; border-bottom:1px solid var(--bs-surface-100); display:flex; justify-content:space-between; align-items:center; }
.pt-card-hdr h6 { font-weight:700; font-size:.78rem; color:var(--bs-surface-700); margin:0; }
.pt-search { border:1px solid var(--bs-surface-200); border-radius:.4rem; padding:.3rem .6rem; font-size:.7rem; width:200px; background:var(--bs-card-bg); }
.pt-search:focus { outline:none; border-color:var(--bs-gradient-start); box-shadow:0 0 0 2px rgba(102,126,234,.1); }

/* Table */
.pt-table { width:100%; border-collapse:collapse; }
.pt-table th { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--bs-surface-500); padding:.55rem .75rem; border-bottom:2px solid var(--bs-surface-100); background:var(--bs-surface-bg-light); }
.pt-table td { font-size:.72rem; padding:.6rem .75rem; border-bottom:1px solid var(--bs-surface-50); vertical-align:middle; color:var(--bs-surface-700); }
.pt-table tr:hover td { background:rgba(102,126,234,.02); }
.pt-table tr:last-child td { border-bottom:none; }

/* Partner Row Styles */
.pt-avatar { width:30px; height:30px; border-radius:.4rem; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:.55rem; color:#fff; flex-shrink:0; }
.pt-name-cell { display:flex; align-items:center; gap:.5rem; }
.pt-name { font-weight:700; font-size:.72rem; color:var(--bs-surface-800); }
.pt-code { font-size:.58rem; font-weight:600; padding:.12rem .4rem; border-radius:.25rem; background:rgba(102,126,234,.08); color:var(--bs-gradient-start); }
.pt-email { font-size:.68rem; color:var(--bs-surface-500); }
.pt-badge { font-size:.58rem; font-weight:600; padding:.15rem .45rem; border-radius:.3rem; }
.pt-badge.active { background:rgba(52,195,143,.1); color:#34c38f; }
.pt-badge.inactive { background:rgba(116,120,141,.1); color:#74788d; }
.pt-carrier-badge { font-size:.55rem; font-weight:600; padding:.1rem .35rem; border-radius:.2rem; background:rgba(85,110,230,.08); color:var(--bs-gradient-start); margin:.1rem; display:inline-block; }
.pt-commission { font-weight:700; color:var(--bs-gradient-end); font-size:.72rem; }
.pt-actions { display:flex; gap:.25rem; }
.pt-act-btn { width:26px; height:26px; border-radius:.35rem; display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); font-size:.7rem; cursor:pointer; transition:all .15s; text-decoration:none; }
.pt-act-btn:hover { transform:translateY(-1px); box-shadow:0 2px 6px rgba(0,0,0,.08); }
.pt-act-btn.view:hover { border-color:#17a2b8; color:#17a2b8; background:rgba(23,162,184,.05); }
.pt-act-btn.edit:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); background:rgba(102,126,234,.05); }
.pt-act-btn.delete:hover { border-color:#f46a6a; color:#f46a6a; background:rgba(244,106,106,.05); }
.pt-empty { text-align:center; padding:3rem 1rem; color:var(--bs-surface-500); }
.pt-empty i { font-size:3rem; display:block; margin-bottom:.75rem; opacity:.15; }
.pt-empty p { font-size:.78rem; margin:.25rem 0; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Admin <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Partners <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="pt-page-hdr">
    <h5><i class="bx bx-group"></i> Partners Management <span class="pt-sub">External partner network</span></h5>
    <a href="<?php echo e(route('admin.partners.create')); ?>" class="pt-add-btn"><i class="bx bx-plus"></i> Add Partner</a>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem;" role="alert">
    <i class="bx bx-check-circle me-1"></i> <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem;"></button>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem;" role="alert">
    <i class="bx bx-error me-1"></i> <?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem;"></button>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php
    $activeCount = $partners->where('is_active', true)->count();
    $inactiveCount = $partners->where('is_active', false)->count();
    $totalCarrierAssignments = $partners->sum(fn($p) => $p->carrierStates->pluck('insurance_carrier_id')->unique()->count());
    $totalStatesCovered = $partners->sum(fn($p) => $p->carrierStates->pluck('state')->unique()->count());
?>
<div class="pt-kpi-row">
    <div class="pt-kpi k-blue"><i class="bx bx-group k-icon"></i><div class="k-val"><?php echo e($partners->count()); ?></div><div class="k-lbl">Total Partners</div></div>
    <div class="pt-kpi k-green"><i class="bx bx-check-shield k-icon"></i><div class="k-val"><?php echo e($activeCount); ?></div><div class="k-lbl">Active</div></div>
    <div class="pt-kpi k-orange"><i class="bx bx-briefcase k-icon"></i><div class="k-val"><?php echo e($totalCarrierAssignments); ?></div><div class="k-lbl">Carrier Assignments</div></div>
    <div class="pt-kpi k-purple"><i class="bx bx-map k-icon"></i><div class="k-val"><?php echo e($totalStatesCovered); ?></div><div class="k-lbl">States Covered</div></div>
</div>

<div class="pt-card">
    <div class="pt-card-hdr">
        <h6><i class="bx bx-list-ul me-1"></i> All Partners</h6>
        <input type="text" class="pt-search" id="ptSearch" placeholder="Search partners..." autocomplete="off">
    </div>
    <div class="table-responsive">
        <table class="pt-table" id="ptTable">
            <thead>
                <tr>
                    <th>Partner</th>
                    <th>Code</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Our Commission</th>
                    <th>Carriers</th>
                    <th>States</th>
                    <th>Status</th>
                    <th>Login</th>
                    <th style="width:100px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $uniqueCarriers = $partner->carrierStates->pluck('insurance_carrier_id')->unique();
                    $totalStatesP = $partner->carrierStates->pluck('state')->unique()->count();
                    $hue = crc32($partner->name) % 360;
                    $ini = strtoupper(collect(explode(' ', $partner->name))->map(fn($w) => substr($w,0,1))->take(2)->join(''));
                ?>
                <tr>
                    <td>
                        <div class="pt-name-cell">
                            <div class="pt-avatar" style="background:hsl(<?php echo e($hue); ?>,55%,50%)"><?php echo e($ini); ?></div>
                            <span class="pt-name"><?php echo e($partner->name); ?></span>
                        </div>
                    </td>
                    <td><span class="pt-code"><?php echo e($partner->code); ?></span></td>
                    <td class="pt-email"><?php echo e($partner->email ?? '—'); ?></td>
                    <td style="font-size:.68rem"><?php echo e($partner->phone ?? '—'); ?></td>
                    <td><span class="pt-commission"><?php echo e(number_format($partner->our_commission_percentage ?? 0, 1)); ?>%</span></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $uniqueCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $cn = $partner->carrierStates->firstWhere('insurance_carrier_id', $cid)?->insuranceCarrier?->name; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cn): ?><span class="pt-carrier-badge"><?php echo e($cn); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($uniqueCarriers->isEmpty()): ?><span style="color:var(--bs-surface-400);font-size:.65rem">None</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="font-size:.72rem;font-weight:600"><?php echo e($totalStatesP); ?></td>
                    <td><span class="pt-badge <?php echo e($partner->is_active ? 'active' : 'inactive'); ?>"><?php echo e($partner->is_active ? 'Active' : 'Inactive'); ?></span></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->password): ?>
                            <span class="pt-badge active"><i class="bx bx-check" style="font-size:.55rem"></i> Set</span>
                        <?php else: ?>
                            <span class="pt-badge inactive"><i class="bx bx-x" style="font-size:.55rem"></i> No</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <div class="pt-actions">
                            <a href="<?php echo e(route('admin.partners.show', $partner->id)); ?>" class="pt-act-btn view" title="View"><i class="bx bx-show"></i></a>
                            <?php if(auth()->check() && auth()->user()->canEditModule('partners')): ?>
                            <a href="<?php echo e(route('admin.partners.edit', $partner->id)); ?>" class="pt-act-btn edit" title="Edit"><i class="bx bx-edit-alt"></i></a>
                            <?php endif; ?>
                            <?php if(auth()->check() && auth()->user()->canDeleteInModule('partners')): ?>
                            <form action="<?php echo e(route('admin.partners.destroy', $partner->id)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete <?php echo e(addslashes($partner->name)); ?>? This removes all carrier assignments.')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="pt-act-btn delete" title="Delete"><i class="bx bx-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10">
                        <div class="pt-empty">
                            <i class="bx bx-user-x"></i>
                            <p><strong>No Partners Found</strong></p>
                            <p>Click "Add Partner" to create your first partner</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.getElementById('ptSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#ptTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/partners/index.blade.php ENDPATH**/ ?>