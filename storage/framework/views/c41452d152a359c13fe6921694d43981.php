<?php $__env->startSection('title'); ?> Insurance Clusters <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   Insurance Clusters — Executive Dashboard Theme
   ═══════════════════════════════════════════════════ */

/* Page Header */
.cl-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem; }
.cl-page-hdr h5 { font-weight:800; font-size:1.05rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.cl-page-hdr .cl-sub { font-size:.68rem; font-weight:500; color:var(--bs-surface-500); }
.cl-add-btn { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)); color:#fff; border:none; padding:.35rem .8rem; border-radius:.4rem; font-size:.68rem; font-weight:600; display:inline-flex; align-items:center; gap:.25rem; text-decoration:none; transition:all .2s; box-shadow:0 2px 8px rgba(102,126,234,.25); }
.cl-add-btn:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(102,126,234,.35); color:#fff; }

/* KPI Row — matching exec dashboard */
.cl-kpi-row { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:.75rem; }
.cl-kpi {
    flex:1 1 80px; min-width:75px; padding:.65rem .6rem;
    border-radius:.55rem; text-align:center; position:relative; overflow:hidden;
    background:var(--bs-card-bg); border:1px solid rgba(0,0,0,.04);
    box-shadow:0 1px 4px rgba(0,0,0,.05); transition:transform .15s,box-shadow .15s;
}
.cl-kpi:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.08); }
.cl-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:.55rem .55rem 0 0; }
.cl-kpi .k-icon { font-size:1rem; margin-bottom:.2rem; display:block; opacity:.7; }
.cl-kpi .k-val { font-size:1.35rem; font-weight:700; line-height:1; }
.cl-kpi .k-lbl { font-size:.55rem; text-transform:uppercase; font-weight:600; letter-spacing:.4px; color:var(--bs-surface-500); margin-top:.2rem; }

.cl-kpi.k-blue { background:rgba(85,110,230,.06); }
.cl-kpi.k-blue::before { background:linear-gradient(90deg,#556ee6,#8b9cf7); }
.cl-kpi.k-blue .k-val,.cl-kpi.k-blue .k-icon { color:#556ee6; }

.cl-kpi.k-green { background:rgba(52,195,143,.06); }
.cl-kpi.k-green::before { background:linear-gradient(90deg,#34c38f,#6eddb8); }
.cl-kpi.k-green .k-val,.cl-kpi.k-green .k-icon { color:#1a8754; }

.cl-kpi.k-orange { background:rgba(241,180,76,.06); }
.cl-kpi.k-orange::before { background:linear-gradient(90deg,#f1b44c,#f5cd7e); }
.cl-kpi.k-orange .k-val,.cl-kpi.k-orange .k-icon { color:#b87a14; }

.cl-kpi.k-purple { background:rgba(124,105,239,.06); }
.cl-kpi.k-purple::before { background:linear-gradient(90deg,#7c69ef,#a899f5); }
.cl-kpi.k-purple .k-val,.cl-kpi.k-purple .k-icon { color:#5b49c7; }

/* Charts Row */
.cl-charts-row { display:grid; grid-template-columns:1fr 1fr; gap:.65rem; margin-bottom:.75rem; }
@media (max-width:768px) { .cl-charts-row { grid-template-columns:1fr; } }
.cl-chart-card {
    background:var(--bs-card-bg); border-radius:.6rem; overflow:hidden;
    box-shadow:0 1px 4px rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.04);
}
.cl-chart-hdr {
    display:flex; justify-content:space-between; align-items:center;
    padding:.5rem .75rem; border-bottom:1px solid rgba(0,0,0,.05);
}
.cl-chart-hdr h6 { margin:0; font-size:.75rem; font-weight:700; display:flex; align-items:center; gap:.3rem; color:var(--bs-surface-700); }
.cl-chart-hdr h6 i { opacity:.6; font-size:.9rem; }
.cl-chart-body { padding:.6rem .75rem; }
.cl-chart-body canvas { max-height:180px; }

/* Search/Filter */
.cl-filters { display:flex; gap:.4rem; margin-bottom:.65rem; flex-wrap:wrap; align-items:center; }
.cl-filter-input { border:1px solid rgba(0,0,0,.08); border-radius:.4rem; padding:.3rem .6rem; font-size:.7rem; background:var(--bs-card-bg); transition:all .2s; }
.cl-filter-input:focus { outline:none; border-color:#556ee6; box-shadow:0 0 0 2px rgba(85,110,230,.1); }
.cl-result-count { font-size:.6rem; color:var(--bs-surface-500); font-weight:600; }

/* Cards Grid */
.cl-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:.65rem; }

/* Carrier Card — exec style */
.cl-card {
    background:var(--bs-card-bg); border-radius:.6rem; overflow:hidden;
    box-shadow:0 1px 4px rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.04);
    transition:all .2s;
}
.cl-card:hover { border-color:rgba(85,110,230,.2); box-shadow:0 4px 16px rgba(0,0,0,.08); transform:translateY(-2px); }
.cl-card-top { padding:.6rem .75rem; border-bottom:1px solid rgba(0,0,0,.04); display:flex; justify-content:space-between; align-items:flex-start; }
.cl-carrier-name { font-weight:700; font-size:.78rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.3rem; }
.cl-carrier-name i { color:#556ee6; }
.cl-carrier-badge { font-size:.52rem; font-weight:700; padding:.1rem .35rem; border-radius:.2rem; border:1px solid transparent; }
.cl-carrier-badge.active { background:rgba(52,195,143,.1); color:#1a8754; border-color:rgba(52,195,143,.15); }
.cl-carrier-badge.inactive { background:rgba(108,117,125,.08); color:#6c757d; border-color:rgba(108,117,125,.12); }
.cl-partner-tag { font-size:.58rem; font-weight:600; padding:.12rem .4rem; border-radius:.25rem; background:rgba(85,110,230,.08); color:#556ee6; margin-top:.25rem; display:inline-flex; align-items:center; gap:.2rem; border:1px solid rgba(85,110,230,.1); }

.cl-card-body { padding:.65rem .75rem; }
.cl-stats-row { display:grid; grid-template-columns:1fr 1fr; gap:.4rem; margin-bottom:.5rem; }
.cl-stat { text-align:center; padding:.4rem; border-radius:.35rem; background:rgba(0,0,0,.02); border:1px solid rgba(0,0,0,.03); }
.cl-stat .val { font-size:1.1rem; font-weight:700; line-height:1; }
.cl-stat .lbl { font-size:.52rem; font-weight:600; text-transform:uppercase; color:var(--bs-surface-500); margin-top:.15rem; }
.cl-stat.blue .val { color:#556ee6; }
.cl-stat.green .val { color:#1a8754; }

.cl-rates-row { display:grid; grid-template-columns:repeat(4,1fr); gap:.25rem; margin-bottom:.4rem; }
.cl-rate { text-align:center; padding:.25rem; border-radius:.25rem; background:rgba(0,0,0,.02); border:1px solid rgba(0,0,0,.03); }
.cl-rate .r-lbl { font-size:.48rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--bs-surface-500); }
.cl-rate .r-val { font-size:.72rem; font-weight:700; color:var(--bs-surface-700); }

.cl-meta { font-size:.58rem; color:var(--bs-surface-500); margin-bottom:.3rem; }
.cl-meta strong { color:var(--bs-surface-600); }

/* State tags — FIXED for white theme: explicit border + rgba bg */
.cl-state-tags { display:flex; flex-wrap:wrap; gap:.15rem; }
.cl-state-tag {
    font-size:.5rem; font-weight:700; padding:.1rem .3rem; border-radius:.2rem;
    background:rgba(85,110,230,.1); color:#556ee6;
    border:1px solid rgba(85,110,230,.15);
    letter-spacing:.3px;
}
.cl-plan-tag {
    font-size:.5rem; font-weight:700; padding:.1rem .3rem; border-radius:.2rem;
    background:rgba(52,195,143,.1); color:#1a8754;
    border:1px solid rgba(52,195,143,.15);
    letter-spacing:.3px;
}

.cl-card-footer { padding:.4rem .75rem; border-top:1px solid rgba(0,0,0,.04); display:flex; justify-content:flex-end; gap:.25rem; flex-wrap:wrap; }
.cl-action { font-size:.58rem; font-weight:600; padding:.18rem .45rem; border-radius:.25rem; border:1px solid rgba(0,0,0,.08); background:var(--bs-card-bg); color:var(--bs-surface-500); cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.2rem; text-decoration:none; }
.cl-action:hover { transform:translateY(-1px); }
.cl-action.edit:hover { border-color:#556ee6; color:#556ee6; }
.cl-action.edit-carrier:hover { border-color:#2b81c9; color:#2b81c9; }
.cl-action.delete { color:#c84646; border-color:rgba(244,106,106,.15); }
.cl-action.delete:hover { background:rgba(244,106,106,.05); border-color:#f46a6a; }

.cl-empty { text-align:center; padding:3rem 1rem; color:var(--bs-surface-500); }
.cl-empty i { font-size:3rem; display:block; margin-bottom:.75rem; opacity:.15; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

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

<!-- KPI Row -->
<div class="cl-kpi-row">
    <div class="cl-kpi k-blue"><i class="bx bx-briefcase k-icon"></i><div class="k-val"><?php echo e($totalCarriers); ?></div><div class="k-lbl">Total Carriers</div></div>
    <div class="cl-kpi k-green"><i class="bx bx-group k-icon"></i><div class="k-val"><?php echo e($totalPartners); ?></div><div class="k-lbl">Active Partners</div></div>
    <div class="cl-kpi k-orange"><i class="bx bx-map k-icon"></i><div class="k-val"><?php echo e($totalStates); ?></div><div class="k-lbl">States Covered</div></div>
    <div class="cl-kpi k-purple"><i class="bx bx-file k-icon"></i><div class="k-val"><?php echo e($totalLeads); ?></div><div class="k-lbl">Total Leads</div></div>
</div>

<!-- Charts Row -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($partnerCarriers) > 0): ?>
<div class="cl-charts-row">
    <div class="cl-chart-card">
        <div class="cl-chart-hdr"><h6><i class="bx bx-bar-chart-alt-2"></i> States per Carrier</h6></div>
        <div class="cl-chart-body"><canvas id="statesChart"></canvas></div>
    </div>
    <div class="cl-chart-card">
        <div class="cl-chart-hdr"><h6><i class="bx bx-pie-chart-alt"></i> Leads Distribution</h6></div>
        <div class="cl-chart-body"><canvas id="leadsChart"></canvas></div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<!-- Search -->
<div class="cl-filters">
    <input type="text" class="cl-filter-input" id="clSearch" placeholder="Search carriers or partners..." style="width:240px" autocomplete="off">
    <span class="cl-result-count" id="clCount"><?php echo e(count($partnerCarriers)); ?> assignment<?php echo e(count($partnerCarriers) != 1 ? 's' : ''); ?></span>
</div>

<!-- Cards Grid -->
<div class="cl-grid" id="clGrid">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $partnerCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="cl-card" data-search="<?php echo e(strtolower($pc['carrier']->name . ' ' . $pc['partner']->name)); ?>">
        <div class="cl-card-top">
            <div>
                <div class="cl-carrier-name">
                    <i class="bx bx-shield-quarter"></i>
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
            <div class="cl-meta" style="margin-bottom:.2rem"><strong>Settlement Rates</strong></div>
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
            <div class="cl-meta" style="margin-top:.35rem"><strong>Plan Types</strong></div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Search with live count
document.getElementById('clSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('#clGrid .cl-card').forEach(c => {
        const show = (c.dataset.search || '').includes(q);
        c.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const countEl = document.getElementById('clCount');
    if (countEl) countEl.textContent = visible + ' assignment' + (visible !== 1 ? 's' : '');
});

// Charts
<?php
    $chartData = collect($partnerCarriers)->map(function($pc) {
        return [
            'label' => $pc['carrier']->name . ' (' . $pc['partner']->name . ')',
            'short' => $pc['carrier']->name,
            'states' => $pc['state_count'],
            'leads' => $pc['leads_count'],
        ];
    })->values();
?>
<?php if(count($partnerCarriers) > 0): ?>
document.addEventListener('DOMContentLoaded', function() {
    const data = <?php echo json_encode($chartData, 15, 512) ?>;

    const colors = ['rgba(85,110,230,.7)','rgba(52,195,143,.7)','rgba(241,180,76,.7)','rgba(124,105,239,.7)','rgba(244,106,106,.7)','rgba(80,165,241,.7)','rgba(212,175,55,.7)'];
    const bgColors = data.map((_,i) => colors[i % colors.length]);

    // States bar chart
    const statesCtx = document.getElementById('statesChart');
    if (statesCtx) {
        new Chart(statesCtx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.short),
                datasets: [{ label: 'States', data: data.map(d => d.states), backgroundColor: bgColors, borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 10 }, stepSize: 5 }, grid: { color: 'rgba(0,0,0,.04)' } },
                    x: { ticks: { font: { size: 9, weight: '600' } }, grid: { display: false } }
                }
            }
        });
    }

    // Leads doughnut chart
    const leadsCtx = document.getElementById('leadsChart');
    if (leadsCtx) {
        const totalLeads = data.reduce((s,d) => s + d.leads, 0);
        if (totalLeads > 0) {
            new Chart(leadsCtx, {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{ data: data.map(d => d.leads), backgroundColor: bgColors, borderWidth: 0, hoverOffset: 6 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: true, cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 9, weight: '600' }, padding: 8, usePointStyle: true, pointStyle: 'circle' } }
                    }
                }
            });
        } else {
            leadsCtx.parentElement.innerHTML = '<div style="text-align:center;padding:2rem 0;color:var(--bs-surface-400);font-size:.72rem;"><i class="bx bx-bar-chart" style="font-size:1.5rem;display:block;margin-bottom:.3rem;opacity:.2"></i>No leads data yet</div>';
        }
    }
});
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/insurance-carriers/index.blade.php ENDPATH**/ ?>