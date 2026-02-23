<?php $__env->startSection('title'); ?> Insurance Clusters <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ─── Clusters Page ─── */
.cl-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.cl-page-hdr h5 { font-weight:800; font-size:1.1rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.cl-page-hdr .cl-sub { font-size:.7rem; font-weight:500; color:var(--bs-surface-500); margin-left:.25rem; }
.cl-add-btn { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)); color:#fff; border:none; padding:.4rem .9rem; border-radius:.45rem; font-size:.7rem; font-weight:600; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; transition:all .2s; box-shadow:0 2px 8px rgba(102,126,234,.25); }
.cl-add-btn:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(102,126,234,.35); color:#fff; }

/* KPI Row */
.cl-kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:.6rem; margin-bottom:1.25rem; }
.cl-kpi { background:var(--bs-card-bg); border-radius:.6rem; padding:.75rem .9rem; position:relative; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.cl-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:.6rem .6rem 0 0; }
.cl-kpi.k-blue::before { background:linear-gradient(90deg,var(--bs-gradient-start),var(--bs-gradient-end)); }
.cl-kpi.k-green::before { background:linear-gradient(90deg,#34c38f,#38ef7d); }
.cl-kpi.k-orange::before { background:linear-gradient(90deg,#f5b041,#f39c12); }
.cl-kpi.k-purple::before { background:linear-gradient(90deg,#764ba2,#667eea); }
.cl-kpi .k-icon { font-size:1.4rem; opacity:.15; position:absolute; right:.6rem; top:.6rem; }
.cl-kpi.k-blue .k-icon,.cl-kpi.k-blue .k-val { color:var(--bs-gradient-start); }
.cl-kpi.k-green .k-icon,.cl-kpi.k-green .k-val { color:#34c38f; }
.cl-kpi.k-orange .k-icon,.cl-kpi.k-orange .k-val { color:#f5b041; }
.cl-kpi.k-purple .k-icon,.cl-kpi.k-purple .k-val { color:#764ba2; }
.cl-kpi .k-val { font-size:1.4rem; font-weight:800; line-height:1; }
.cl-kpi .k-lbl { font-size:.6rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--bs-surface-500); margin-top:.2rem; }

/* Search/Filter */
.cl-filters { display:flex; gap:.4rem; margin-bottom:.75rem; flex-wrap:wrap; }
.cl-filter-input { border:1px solid var(--bs-surface-200); border-radius:.4rem; padding:.3rem .6rem; font-size:.7rem; background:var(--bs-card-bg); }
.cl-filter-input:focus { outline:none; border-color:var(--bs-gradient-start); box-shadow:0 0 0 2px rgba(102,126,234,.1); }

/* Cards Grid */
.cl-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:.75rem; }

/* Carrier Card */
.cl-card { background:var(--bs-card-bg); border-radius:.65rem; box-shadow:0 1px 4px rgba(0,0,0,.04); overflow:hidden; transition:all .2s; border:1.5px solid var(--bs-surface-100); }
.cl-card:hover { border-color:var(--bs-gradient-start); box-shadow:0 4px 16px rgba(102,126,234,.1); transform:translateY(-2px); }
.cl-card-top { padding:.7rem .85rem; border-bottom:1px solid var(--bs-surface-50); display:flex; justify-content:space-between; align-items:flex-start; }
.cl-carrier-name { font-weight:700; font-size:.78rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.3rem; }
.cl-carrier-badge { font-size:.52rem; font-weight:700; padding:.1rem .35rem; border-radius:.2rem; }
.cl-carrier-badge.active { background:rgba(52,195,143,.1); color:#34c38f; }
.cl-carrier-badge.inactive { background:rgba(116,120,141,.1); color:#74788d; }
.cl-partner-tag { font-size:.58rem; font-weight:600; padding:.12rem .4rem; border-radius:.25rem; background:rgba(102,126,234,.08); color:var(--bs-gradient-start); margin-top:.25rem; display:inline-flex; align-items:center; gap:.2rem; }

.cl-card-body { padding:.75rem .85rem; }
.cl-stats-row { display:grid; grid-template-columns:1fr 1fr; gap:.4rem; margin-bottom:.6rem; }
.cl-stat { text-align:center; padding:.4rem; border-radius:.35rem; background:var(--bs-surface-bg-light); }
.cl-stat .val { font-size:1.1rem; font-weight:800; line-height:1; }
.cl-stat .lbl { font-size:.52rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); margin-top:.15rem; }
.cl-stat.blue .val { color:var(--bs-gradient-start); }
.cl-stat.green .val { color:#34c38f; }

.cl-rates-row { display:grid; grid-template-columns:repeat(4,1fr); gap:.3rem; margin-bottom:.5rem; }
.cl-rate { text-align:center; padding:.3rem; border-radius:.3rem; background:var(--bs-surface-bg-light); }
.cl-rate .r-lbl { font-size:.5rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); }
.cl-rate .r-val { font-size:.72rem; font-weight:700; color:var(--bs-surface-700); }

.cl-meta { font-size:.58rem; color:var(--bs-surface-500); margin-bottom:.35rem; }
.cl-meta strong { color:var(--bs-surface-600); }
.cl-state-tags { display:flex; flex-wrap:wrap; gap:.15rem; }
.cl-state-tag { font-size:.5rem; font-weight:600; padding:.08rem .3rem; border-radius:.2rem; background:rgba(85,110,230,.06); color:var(--bs-gradient-start); }
.cl-plan-tag { font-size:.5rem; font-weight:600; padding:.08rem .3rem; border-radius:.2rem; background:rgba(52,195,143,.06); color:#34c38f; }

.cl-card-footer { padding:.5rem .85rem; border-top:1px solid var(--bs-surface-50); display:flex; justify-content:flex-end; gap:.3rem; flex-wrap:wrap; }
.cl-action { font-size:.6rem; font-weight:600; padding:.2rem .5rem; border-radius:.3rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.2rem; text-decoration:none; }
.cl-action:hover { transform:translateY(-1px); }
.cl-action.edit:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); }
.cl-action.edit-carrier:hover { border-color:#17a2b8; color:#17a2b8; }
.cl-action.delete { color:#f46a6a; border-color:rgba(244,106,106,.2); }
.cl-action.delete:hover { background:rgba(244,106,106,.05); border-color:#f46a6a; }

.cl-empty { text-align:center; padding:3rem 1rem; color:var(--bs-surface-500); }
.cl-empty i { font-size:3rem; display:block; margin-bottom:.75rem; opacity:.15; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Analytics <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Insurance Clusters <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="cl-page-hdr">
    <h5><i class="bx bx-shield-quarter"></i> Insurance Clusters <span class="cl-sub">Carrier & partner performance</span></h5>
    <a href="<?php echo e(route('admin.insurance-carriers.create')); ?>" class="cl-add-btn"><i class="bx bx-plus"></i> Add Carrier</a>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem" role="alert">
    <i class="bx bx-check-circle me-1"></i> <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem"></button>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="cl-kpi-row">
    <div class="cl-kpi k-blue"><i class="bx bx-briefcase k-icon"></i><div class="k-val"><?php echo e($totalCarriers); ?></div><div class="k-lbl">Total Carriers</div></div>
    <div class="cl-kpi k-green"><i class="bx bx-group k-icon"></i><div class="k-val"><?php echo e($totalPartners); ?></div><div class="k-lbl">Active Partners</div></div>
    <div class="cl-kpi k-orange"><i class="bx bx-map k-icon"></i><div class="k-val"><?php echo e($totalStates); ?></div><div class="k-lbl">States Covered</div></div>
    <div class="cl-kpi k-purple"><i class="bx bx-file k-icon"></i><div class="k-val"><?php echo e($totalLeads); ?></div><div class="k-lbl">Total Leads</div></div>
</div>

<div class="cl-filters">
    <input type="text" class="cl-filter-input" id="clSearch" placeholder="Search carriers or partners..." style="width:240px" autocomplete="off">
</div>

<div class="cl-grid" id="clGrid">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $partnerCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="cl-card" data-search="<?php echo e(strtolower($pc['carrier']->name . ' ' . $pc['partner']->name)); ?>">
        <div class="cl-card-top">
            <div>
                <div class="cl-carrier-name">
                    <i class="bx bx-shield-quarter" style="color:var(--bs-gradient-start)"></i>
                    <?php echo e($pc['carrier']->name); ?>

                </div>
                <div class="cl-partner-tag">
                    <i class="bx bx-user"></i> <?php echo e($pc['partner']->name); ?>

                </div>
            </div>
            <span class="cl-carrier-badge <?php echo e($pc['carrier']->is_active ? 'active' : 'inactive'); ?>">
                <?php echo e($pc['carrier']->is_active ? 'Active' : 'Inactive'); ?>

            </span>
        </div>

        <div class="cl-card-body">
            <div class="cl-stats-row">
                <div class="cl-stat blue">
                    <div class="val"><?php echo e($pc['state_count']); ?></div>
                    <div class="lbl">States</div>
                </div>
                <div class="cl-stat green">
                    <div class="val"><?php echo e($pc['leads_count']); ?></div>
                    <div class="lbl">Leads</div>
                </div>
            </div>

            <div class="cl-meta"><strong>Payment:</strong> <?php echo e(ucwords(str_replace('_', ' ', $pc['carrier']->payment_module))); ?> &middot; <strong>Base:</strong> <?php echo e($pc['carrier']->base_commission_percentage ?? 0); ?>%</div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pc['avg_level'] || $pc['avg_graded'] || $pc['avg_gi'] || $pc['avg_modified']): ?>
            <div class="cl-meta" style="margin-bottom:.25rem"><strong>Settlement Rates</strong></div>
            <div class="cl-rates-row">
                <div class="cl-rate"><div class="r-lbl">Level</div><div class="r-val"><?php echo e($pc['avg_level'] ? number_format($pc['avg_level'],1).'%' : '—'); ?></div></div>
                <div class="cl-rate"><div class="r-lbl">Graded</div><div class="r-val"><?php echo e($pc['avg_graded'] ? number_format($pc['avg_graded'],1).'%' : '—'); ?></div></div>
                <div class="cl-rate"><div class="r-lbl">GI</div><div class="r-val"><?php echo e($pc['avg_gi'] ? number_format($pc['avg_gi'],1).'%' : '—'); ?></div></div>
                <div class="cl-rate"><div class="r-lbl">Modified</div><div class="r-val"><?php echo e($pc['avg_modified'] ? number_format($pc['avg_modified'],1).'%' : '—'); ?></div></div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="cl-meta"><strong>Licensed States (<?php echo e($pc['state_count']); ?>)</strong></div>
            <div class="cl-state-tags">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pc['states']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="cl-state-tag"><?php echo e($state); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pc['carrier']->plan_types && is_array($pc['carrier']->plan_types) && count($pc['carrier']->plan_types) > 0): ?>
            <div class="cl-meta" style="margin-top:.4rem"><strong>Plan Types</strong></div>
            <div class="cl-state-tags">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pc['carrier']->plan_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="cl-plan-tag"><?php echo e($plan); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="cl-card-footer">
            <?php if(auth()->check() && auth()->user()->canEditModule('carriers')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pc['partner']->is_partner_model) && $pc['partner']->is_partner_model): ?>
                <a href="<?php echo e(route('admin.partners.edit', $pc['partner']->id)); ?>" class="cl-action edit"><i class="bx bx-user-circle"></i> Partner</a>
            <?php else: ?>
                <a href="<?php echo e(route('agents.edit', $pc['partner']->id)); ?>" class="cl-action edit"><i class="bx bx-user-circle"></i> Agent</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('admin.insurance-carriers.edit', $pc['carrier']->id)); ?>" class="cl-action edit-carrier"><i class="bx bx-edit-alt"></i> Carrier</a>
            <?php endif; ?>
            <?php if(auth()->check() && auth()->user()->canDeleteInModule('carriers')): ?>
            <form action="<?php echo e(route('admin.insurance-carriers.destroy', $pc['carrier']->id)); ?>" method="POST" style="display:inline" onsubmit="return confirm('PERMANENTLY DELETE carrier <?php echo e(addslashes($pc['carrier']->name)); ?>? This removes ALL partner assignments and cannot be undone!')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="cl-action delete"><i class="bx bx-trash"></i> Delete</button>
            </form>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pc['partner']->is_partner_model) && $pc['partner']->is_partner_model): ?>
            <form action="<?php echo e(route('admin.partners.remove-carrier-assignment', [$pc['partner']->id, $pc['carrier']->id])); ?>" method="POST" style="display:inline" onsubmit="return confirm('Remove <?php echo e(addslashes($pc['carrier']->name)); ?> from <?php echo e(addslashes($pc['partner']->name)); ?>?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="cl-action delete"><i class="bx bx-unlink"></i> Unassign</button>
            </form>
            <?php else: ?>
            <form action="<?php echo e(route('admin.partners.remove-carrier-assignment', [$pc['partner']->id, $pc['carrier']->id])); ?>" method="POST" style="display:inline" onsubmit="return confirm('Remove <?php echo e(addslashes($pc['carrier']->name)); ?> from <?php echo e(addslashes($pc['partner']->name)); ?>?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="cl-action delete"><i class="bx bx-unlink"></i> Unassign</button>
            </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="grid-column:1/-1">
        <div class="cl-empty">
            <i class="bx bx-briefcase-alt"></i>
            <p style="font-size:.78rem;font-weight:600">No Carrier Assignments Found</p>
            <p style="font-size:.68rem">Add carriers through the Partner management page</p>
            <a href="<?php echo e(route('admin.partners.index')); ?>" class="cl-add-btn" style="margin-top:.5rem"><i class="bx bx-group"></i> Go to Partners</a>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.getElementById('clSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#clGrid .cl-card').forEach(c => {
        c.style.display = (c.dataset.search || '').includes(q) ? '' : 'none';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/insurance-carriers/index.blade.php ENDPATH**/ ?>