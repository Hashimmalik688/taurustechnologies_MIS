<?php $__env->startSection('title'); ?>
    Followup Report
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ══════════════════════════════════════════════
   Followup Report — Page Design
   ══════════════════════════════════════════════ */

/* Hero filter bar */
.fr-filter-hero {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.06);
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    padding: 22px 24px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.fr-filter-hero::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #d4af37, #e8c84a, #d4af37);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
}
@keyframes shimmer { 0%,100%{background-position:0%} 50%{background-position:200%} }
.fr-filter-label {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .8px; color: #94a3b8; margin-bottom: 14px;
    display: flex; align-items: center; gap: 6px;
}
.fr-filter-label i { font-size: 1rem; color: #d4af37; }
.fr-filter-row { display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end; }
.fr-filter-group { display: flex; flex-direction: column; gap: 4px; }
.fr-filter-group label { font-size: .72rem; font-weight: 600; color: #64748b; }
.fr-filter-group select,
.fr-filter-group input[type=date] {
    border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 9px 14px; font-size: .82rem; background: #f8fafc;
    color: #1e293b; min-width: 150px; cursor: pointer;
}
.fr-filter-group select:focus,
.fr-filter-group input[type=date]:focus { outline: none; border-color: #d4af37; box-shadow: 0 0 0 3px rgba(212,175,55,.12); }
.fr-btn {
    padding: 9px 22px; border-radius: 10px; border: none;
    font-size: .82rem; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
    transition: opacity .2s, transform .15s;
}
.fr-btn:active { transform: scale(.97); }
.fr-btn-primary { background: linear-gradient(135deg, #d4af37, #b8972e); color: #fff; }
.fr-btn-ghost   { background: #f1f5f9; color: #64748b; }
.fr-btn:hover   { opacity: .88; }

/* Active filter chips */
.fr-active-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
.fr-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(212,175,55,.1); color: #92710a;
    border: 1px solid rgba(212,175,55,.25);
    border-radius: 20px; padding: 3px 12px; font-size: .72rem; font-weight: 600;
}
.fr-chip i { font-size: .85rem; }

/* Stat cards */
.fr-stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px; }
@media(max-width:900px) { .fr-stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:500px) { .fr-stat-grid { grid-template-columns: 1fr 1fr; } }
.fr-stat-card {
    background: #fff; border-radius: 16px; padding: 20px;
    border: 1px solid rgba(0,0,0,.06);
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    display: flex; align-items: center; gap: 14px;
    transition: transform .2s, box-shadow .2s;
}
.fr-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.08); }
.fr-stat-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: #fff; flex-shrink: 0;
}
.fr-stat-icon.grey    { background: linear-gradient(135deg, #94a3b8, #64748b); }
.fr-stat-icon.blue    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.fr-stat-icon.amber   { background: linear-gradient(135deg, #f59e0b, #d97706); }
.fr-stat-icon.green   { background: linear-gradient(135deg, #22c55e, #15803d); }
.fr-stat-value { font-size: 1.65rem; font-weight: 800; color: #1e293b; line-height: 1; }
.fr-stat-label { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-top: 4px; }

/* Table card */
.fr-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(0,0,0,.06);
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    overflow: hidden;
}
.fr-card-header {
    padding: 18px 22px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
}
.fr-card-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0; }
.fr-card-sub   { font-size: .73rem; color: #94a3b8; margin: 2px 0 0; }
.fr-count-badge {
    background: #f1f5f9; color: #475569;
    border-radius: 8px; padding: 5px 12px;
    font-size: .75rem; font-weight: 700;
}

.fr-table { width: 100%; border-collapse: collapse; }
.fr-table thead tr {
    background: linear-gradient(135deg, #1e293b, #334155);
}
.fr-table thead th {
    padding: 13px 16px; font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px; color: #fff;
    white-space: nowrap;
}
.fr-table thead th.col-center { text-align: center; }
.fr-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .12s; }
.fr-table tbody tr:last-child { border-bottom: none; }
.fr-table tbody tr:hover { background: #fafbfc; }
.fr-table tbody td { padding: 14px 16px; font-size: .84rem; color: #1e293b; vertical-align: middle; }
.fr-table tbody td.col-center { text-align: center; }

/* Rank */
.fr-rank {
    width: 30px; height: 30px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 800;
}
.fr-rank.r1 { background: linear-gradient(135deg,#d4af37,#b8972e); color:#fff; }
.fr-rank.r2 { background: linear-gradient(135deg,#94a3b8,#64748b); color:#fff; }
.fr-rank.r3 { background: linear-gradient(135deg,#cd7f32,#9a5f20); color:#fff; }
.fr-rank.rn { background: #f1f5f9; color: #64748b; }

/* Metrics */
.fr-total-pill {
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #fff; border-radius: 20px;
    padding: 5px 16px; font-size: .84rem; font-weight: 800; min-width: 50px;
}
.fr-num-pill {
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 20px; padding: 5px 14px;
    font-size: .82rem; font-weight: 700; min-width: 44px;
}
.fr-num-pill.amber  { background: #fef3c7; color: #92400e; }
.fr-num-pill.green  { background: #dcfce7; color: #14532d; }
.fr-num-pill.zero   { background: #f1f5f9; color: #94a3b8; }

/* Progress bar */
.fr-prog-label { font-size: .68rem; color: #94a3b8; margin-bottom: 5px; }
.fr-prog-track { background: #f1f5f9; border-radius: 6px; height: 8px; width: 100%; overflow: hidden; }
.fr-prog-fill  { height: 8px; border-radius: 6px; transition: width .4s ease; }
.fr-prog-fill.green  { background: linear-gradient(90deg,#22c55e,#16a34a); }
.fr-prog-fill.amber  { background: linear-gradient(90deg,#f59e0b,#d97706); }
.fr-prog-fill.red    { background: linear-gradient(90deg,#ef4444,#dc2626); }

/* Agent name */
.fr-agent-name  { font-weight: 700; color: #1e293b; font-size: .88rem; }

/* Unassigned banner */
.fr-unassigned-banner {
    background: linear-gradient(135deg, #fff7ed, #fef9f0);
    border: 1px solid rgba(249,115,22,.2);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 20px; flex-wrap: wrap;
}
.fr-unassigned-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    background: linear-gradient(135deg,#f97316,#ea580c);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.3rem;
}
.fr-unassigned-count { font-size: 1.3rem; font-weight: 800; color: #c2410c; }
.fr-unassigned-desc  { font-size: .76rem; color: #9a3412; margin: 2px 0 0; }

/* Empty state */
.fr-empty { padding: 60px 20px; text-align: center; color: #94a3b8; }
.fr-empty i { font-size: 3rem; display: block; margin-bottom: 12px; }
.fr-empty p { font-size: .9rem; margin: 0; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3 px-4">

    
    <div class="d-flex align-items-center gap-3 mb-4">
        <div>
            <h1 style="font-size:1.35rem;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
                <i class="bx bx-bar-chart-alt-2" style="color:#d4af37;font-size:1.5rem;"></i>
                Followup Report
            </h1>
            <p style="font-size:.76rem;color:#94a3b8;margin:2px 0 0;">Followup assignment numbers per agent — pending &amp; completed breakdown</p>
        </div>
    </div>

    
    <div class="fr-filter-hero">
        <div class="fr-filter-label"><i class="bx bx-filter-alt"></i> Filter by Assignment Date</div>
        <form method="GET" action="<?php echo e(route('followup.report')); ?>" class="fr-filter-form">
            <div class="fr-filter-row">
                <div class="fr-filter-group">
                    <label>From</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="fr-filter-group">
                    <label>To</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="fr-filter-group" style="flex-direction:row;align-items:flex-end;gap:8px;">
                    <button type="button" class="fr-btn fr-btn-ghost" onclick="setFollowupToday()">
                        <i class="bx bx-calendar-check"></i> Today
                    </button>
                    <button type="submit" class="fr-btn fr-btn-primary">
                        <i class="bx bx-search-alt"></i> Apply
                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('date_from') || request('date_to')): ?>
                        <a href="<?php echo e(route('followup.report')); ?>" class="fr-btn fr-btn-ghost">
                            <i class="bx bx-x"></i> Clear
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('date_from') || request('date_to')): ?>
            <div class="fr-active-filters">
                <?php if(request('date_from') && request('date_to')): ?>
                    <span class="fr-chip"><i class="bx bx-calendar-alt"></i>
                        <?php echo e(\Carbon\Carbon::parse(request('date_from'))->format('M d, Y')); ?>

                        &rarr;
                        <?php echo e(\Carbon\Carbon::parse(request('date_to'))->format('M d, Y')); ?>

                    </span>
                <?php elseif(request('date_from')): ?>
                    <span class="fr-chip"><i class="bx bx-calendar-alt"></i> From <?php echo e(\Carbon\Carbon::parse(request('date_from'))->format('M d, Y')); ?></span>
                <?php elseif(request('date_to')): ?>
                    <span class="fr-chip"><i class="bx bx-calendar-alt"></i> Until <?php echo e(\Carbon\Carbon::parse(request('date_to'))->format('M d, Y')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </div>

    <script>
    function setFollowupToday() {
        var today = '<?php echo e($today); ?>';
        var form  = document.querySelector('.fr-filter-form');
        form.querySelector('[name=date_from]').value = today;
        form.querySelector('[name=date_to]').value   = today;
        form.submit();
    }
    </script>

    
    <div class="fr-stat-grid">
        <div class="fr-stat-card">
            <div class="fr-stat-icon grey"><i class="bx bx-user-x"></i></div>
            <div>
                <div class="fr-stat-value"><?php echo e(number_format($totalUnassigned)); ?></div>
                <div class="fr-stat-label">Not Yet Assigned</div>
            </div>
        </div>
        <div class="fr-stat-card">
            <div class="fr-stat-icon blue"><i class="bx bx-user-check"></i></div>
            <div>
                <div class="fr-stat-value"><?php echo e(number_format($grandTotal)); ?></div>
                <div class="fr-stat-label">Total Assigned</div>
            </div>
        </div>
        <div class="fr-stat-card">
            <div class="fr-stat-icon amber"><i class="bx bx-time-five"></i></div>
            <div>
                <div class="fr-stat-value"><?php echo e(number_format($grandPending)); ?></div>
                <div class="fr-stat-label">Followup Pending</div>
            </div>
        </div>
        <div class="fr-stat-card">
            <div class="fr-stat-icon green"><i class="bx bx-check-circle"></i></div>
            <div>
                <div class="fr-stat-value"><?php echo e(number_format($grandDone)); ?></div>
                <div class="fr-stat-label">Followup Done</div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalUnassigned > 0): ?>
    <div class="fr-unassigned-banner">
        <div class="fr-unassigned-icon"><i class="bx bx-error-circle"></i></div>
        <div>
            <div class="fr-unassigned-count"><?php echo e($totalUnassigned); ?> <?php echo e(Str::plural('lead', $totalUnassigned)); ?></div>
            <div class="fr-unassigned-desc">approved &amp; ready for followup but not yet assigned to anyone</div>
        </div>
        <?php if(auth()->check() && auth()->user()->canViewModule('issuance')): ?>
        <div class="ms-auto">
            <a href="<?php echo e(route('issuance.index')); ?>" class="fr-btn fr-btn-primary" style="font-size:.75rem;padding:7px 16px;">
                <i class="bx bx-link-external"></i> Go to Policy Submission
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="fr-card">
        <div class="fr-card-header">
            <div>
                <p class="fr-card-title">Agent Followup Breakdown</p>
                <p class="fr-card-sub">Sorted by total assigned — showing pending vs done per agent</p>
            </div>
            <span class="fr-count-badge"><?php echo e(count($summary)); ?> <?php echo e(Str::plural('agent', count($summary))); ?></span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($summary) > 0): ?>
        <div class="table-responsive">
            <table class="fr-table">
                <thead>
                    <tr>
                        <th style="width:52px;">#</th>
                        <th>Agent</th>
                        <th class="col-center" style="width:130px;">Total Assigned</th>
                        <th class="col-center" style="width:130px;">
                            <i class="bx bx-time-five" style="margin-right:3px;"></i>Pending
                        </th>
                        <th class="col-center" style="width:110px;">
                            <i class="bx bx-check-circle" style="margin-right:3px;"></i>Done
                        </th>
                        <th style="min-width:170px;">Completion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uid => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $donePct   = $data['total'] > 0 ? round(($data['done']    / $data['total']) * 100) : 0;
                        $rankClass = $rank === 1 ? 'r1' : ($rank === 2 ? 'r2' : ($rank === 3 ? 'r3' : 'rn'));
                        $barClass  = $donePct >= 75 ? 'green' : ($donePct >= 40 ? 'amber' : 'red');
                    ?>
                    <tr>
                        <td><span class="fr-rank <?php echo e($rankClass); ?>"><?php echo e($rank); ?></span></td>
                        <td><div class="fr-agent-name"><?php echo e($data['name']); ?></div></td>
                        <td class="col-center">
                            <span class="fr-total-pill"><?php echo e($data['total']); ?></span>
                        </td>
                        <td class="col-center">
                            <span class="fr-num-pill <?php echo e($data['pending'] > 0 ? 'amber' : 'zero'); ?>">
                                <?php echo e($data['pending']); ?>

                            </span>
                        </td>
                        <td class="col-center">
                            <span class="fr-num-pill <?php echo e($data['done'] > 0 ? 'green' : 'zero'); ?>">
                                <?php echo e($data['done']); ?>

                            </span>
                        </td>
                        <td>
                            <div class="fr-prog-label"><?php echo e($donePct); ?>% complete</div>
                            <div class="fr-prog-track">
                                <div class="fr-prog-fill <?php echo e($barClass); ?>" style="width:<?php echo e($donePct); ?>%;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php $rank++; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div style="padding:14px 22px; background:#f8fafc; border-top:2px solid #e2e8f0; display:flex; align-items:center; gap:32px; flex-wrap:wrap;">
            <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748b;">Grand Totals</span>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:.75rem;color:#94a3b8;">Assigned:</span>
                <strong style="color:#1e293b;"><?php echo e($grandTotal); ?></strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:.75rem;color:#94a3b8;">Pending:</span>
                <strong style="color:#92400e;"><?php echo e($grandPending); ?></strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:.75rem;color:#94a3b8;">Done:</span>
                <strong style="color:#14532d;"><?php echo e($grandDone); ?></strong>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grandTotal > 0): ?>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:.75rem;color:#94a3b8;">Overall:</span>
                <strong style="color:#1e293b;"><?php echo e(round(($grandDone / $grandTotal) * 100)); ?>% complete</strong>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php else: ?>
        <div class="fr-empty">
            <i class="bx bx-task"></i>
            <p>No followup assignments found<?php echo e((request('month') || request('year')) ? ' for the selected period.' : '.'); ?></p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/followup/report.blade.php ENDPATH**/ ?>