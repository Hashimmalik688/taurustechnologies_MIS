<?php $__env->startSection('title', 'Revenue Analytics'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   Revenue Analytics — Matching Company Overview Style
   ═══════════════════════════════════════════════════ */

/* Glass-card base (same as Company Overview) */
.ex-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: box-shadow .2s;
}
.ex-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* ── KPI Stat Cards ── */
.kpi-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.kpi-card {
    flex: 1 1 80px;
    min-width: 75px;
    padding: 0.65rem 0.6rem;
    border-radius: 0.55rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.55rem 0.55rem 0 0;
}
.kpi-card .k-icon {
    font-size: 1rem;
    margin-bottom: 0.2rem;
    display: block;
    opacity: .7;
}
.kpi-card .k-val { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.kpi-card .k-lbl {
    font-size: 0.58rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 0.2rem;
}
.kpi-card .k-sub {
    font-size: 0.6rem;
    font-weight: 600;
    margin-top: 0.1rem;
    opacity: .8;
}

/* KPI color variants */
.kpi-card.k-gold    { background: rgba(212,175,55,.06); }
.kpi-card.k-gold::before    { background: linear-gradient(90deg, #d4af37, #e8c84a); }
.kpi-card.k-gold .k-val, .kpi-card.k-gold .k-icon { color: #b89730; }

.kpi-card.k-green   { background: rgba(52,195,143,.06); }
.kpi-card.k-green::before   { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.kpi-card.k-green .k-val, .kpi-card.k-green .k-icon { color: #1a8754; }

.kpi-card.k-warn    { background: rgba(241,180,76,.06); }
.kpi-card.k-warn::before    { background: linear-gradient(90deg, #f1b44c, #f5cd7e); }
.kpi-card.k-warn .k-val, .kpi-card.k-warn .k-icon { color: #b87a14; }

.kpi-card.k-red     { background: rgba(244,106,106,.06); }
.kpi-card.k-red::before     { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.kpi-card.k-red .k-val, .kpi-card.k-red .k-icon { color: #c84646; }

.kpi-card.k-purple  { background: rgba(124,105,239,.06); }
.kpi-card.k-purple::before  { background: linear-gradient(90deg, #7c69ef, #a899f5); }
.kpi-card.k-purple .k-val, .kpi-card.k-purple .k-icon { color: #5b49c7; }

.kpi-card.k-blue    { background: rgba(85,110,230,.06); }
.kpi-card.k-blue::before    { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.kpi-card.k-blue .k-val, .kpi-card.k-blue .k-icon { color: #556ee6; }

.kpi-card.k-teal    { background: rgba(80,165,241,.06); }
.kpi-card.k-teal::before    { background: linear-gradient(90deg, #50a5f1, #8cc5f7); }
.kpi-card.k-teal .k-val, .kpi-card.k-teal .k-icon { color: #2b81c9; }

/* ── Section Cards ── */
.sec-card {
    padding: 0;
    margin-bottom: 0.65rem;
    overflow: hidden;
}
.sec-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    flex-wrap: wrap;
    gap: 0.4rem;
}
.sec-hdr h6 {
    margin: 0;
    font-size: 0.78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.sec-hdr h6 i { opacity: .6; font-size: 0.95rem; }
.sec-body { padding: 0.6rem 0.75rem; }

/* ── Compact Table ── */
.ex-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.75rem;
}
.ex-tbl thead th {
    text-transform: uppercase;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
    position: sticky;
    top: 0;
    z-index: 1;
}
.ex-tbl tbody td {
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    vertical-align: middle;
}
.ex-tbl tbody tr { transition: background .12s; }
.ex-tbl tbody tr:hover { background: rgba(212,175,55,.03); }

/* Badge mini */
.bd-mini {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    display: inline-block;
    min-width: 22px;
    text-align: center;
}
.bd-mini.bd-blue   { background: rgba(85,110,230,.12); color: #556ee6; }
.bd-mini.bd-green  { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-mini.bd-red    { background: rgba(244,106,106,.12); color: #c84646; }
.bd-mini.bd-warn   { background: rgba(241,180,76,.12); color: #b87a14; }
.bd-mini.bd-teal   { background: rgba(80,165,241,.12); color: #2b81c9; }
.bd-mini.bd-gold   { background: rgba(212,175,55,.12); color: #b89730; }
.bd-mini.bd-purple { background: rgba(124,105,239,.12); color: #5b49c7; }

/* Tab badge soft utilities */
.bg-soft-purple { background: rgba(124,105,239,.12) !important; }
.text-purple { color: #5b49c7 !important; }
.bg-soft-blue { background: rgba(85,110,230,.12) !important; }

/* Tab nav overrides */
#salesTab .nav-link {
    padding: 0.3rem 0.65rem;
    font-size: 0.72rem;
    font-weight: 600;
    border-radius: 0.35rem;
    color: var(--bs-surface-500);
    border: 1px solid transparent;
}
#salesTab .nav-link.active {
    background: rgba(85,110,230,.1);
    color: var(--bs-gradient-start, #556ee6);
    border-color: rgba(85,110,230,.2);
}

/* Scrollable table wrapper */
.scroll-tbl { max-height: 340px; overflow-y: auto; }
.scroll-tbl::-webkit-scrollbar { width: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

/* ── Filter Tabs ── */
.filter-tabs {
    display: flex;
    gap: 0.3rem;
}
.filter-tab-btn {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.22rem 0.6rem;
    border-radius: 1rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    transition: all .15s;
}
.filter-tab-btn.active {
    background: var(--bs-gold, #d4af37);
    border-color: var(--bs-gold);
    color: #fff;
}
.filter-tab-btn:hover:not(.active) {
    border-color: var(--bs-gold);
    color: var(--bs-gold);
}

/* ── Ratio blocks ── */
.ratio-row { display: flex; gap: 0.4rem; }
.ratio-block {
    flex: 1;
    text-align: center;
    padding: 0.5rem 0.3rem;
    border-radius: 0.45rem;
    border: 1px solid;
    transition: transform .15s;
}
.ratio-block:hover { transform: translateY(-1px); }
.ratio-block .r-val { font-size: 1.2rem; font-weight: 700; line-height: 1; }
.ratio-block .r-lbl {
    font-size: 0.55rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--bs-surface-500);
    margin-top: 0.15rem;
}
.ratio-block.r-green { border-color: rgba(52,195,143,.3); background: rgba(52,195,143,.04); }
.ratio-block.r-green .r-val { color: #1a8754; }
.ratio-block.r-warn  { border-color: rgba(241,180,76,.3); background: rgba(241,180,76,.04); }
.ratio-block.r-warn .r-val  { color: #b87a14; }
.ratio-block.r-red   { border-color: rgba(244,106,106,.3); background: rgba(244,106,106,.04); }
.ratio-block.r-red .r-val   { color: #c84646; }
.ratio-block.r-purple { border-color: rgba(124,105,239,.3); background: rgba(124,105,239,.04); }
.ratio-block.r-purple .r-val { color: #5b49c7; }

@media(max-width:768px){
    .kpi-card .k-val { font-size: 1.1rem; }
    .kpi-card .k-lbl { font-size: 0.52rem; }
}

/* ── Period nav bar ── */
.period-nav {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-bottom: 0.65rem;
    padding: 0.45rem 0.75rem;
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.period-nav .pn-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--bs-surface-700);
    margin-right: 0.25rem;
}
.pn-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.28rem 0.6rem;
    border-radius: 0.4rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-600);
    text-decoration: none;
    transition: all .15s;
    cursor: pointer;
    white-space: nowrap;
}
.pn-btn:hover { border-color: #d4af37; color: #b89730; }
.pn-btn.active { background: rgba(212,175,55,.1); border-color: #d4af37; color: #b89730; }
.pn-sep { color: var(--bs-surface-300); font-size: 0.75rem; }
.pn-custom { display: flex; align-items: center; gap: 0.3rem; margin-left: auto; }
.pn-custom input[type=date] {
    font-size: 0.68rem;
    padding: 0.25rem 0.45rem;
    border: 1px solid var(--bs-surface-300);
    border-radius: 0.35rem;
    background: var(--bs-card-bg);
    color: var(--bs-surface-700);
}
.pn-custom input[type=date]:focus { outline: none; border-color: #d4af37; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="period-nav">
    <span class="pn-title"><i class="bx bx-calendar" style="font-size:.9rem;opacity:.6;"></i> <?php echo e($periodLabel); ?></span>
    <span class="pn-sep">|</span>
    <a href="<?php echo e(route('revenue-analytics.index', ['month' => $prevMonth])); ?>" class="pn-btn">
        <i class="bx bx-chevron-left"></i> <?php echo e(\Carbon\Carbon::parse($prevMonth.'-01')->format('M Y')); ?>

    </a>
    <a href="<?php echo e(route('revenue-analytics.index', ['month' => $currentMonth])); ?>" class="pn-btn <?php echo e($activeMonth === $currentMonth ? 'active' : ''); ?>">
        <i class="bx bx-radio-circle-marked"></i> This Month
    </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeMonth < $currentMonth): ?>
    <a href="<?php echo e(route('revenue-analytics.index', ['month' => $nextMonth])); ?>" class="pn-btn">
        <?php echo e(\Carbon\Carbon::parse($nextMonth.'-01')->format('M Y')); ?> <i class="bx bx-chevron-right"></i>
    </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $quickMonths = collect();
        for ($i = 1; $i <= 3; $i++) {
            $m = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
            if ($m !== $prevMonth) $quickMonths->push($m);
        }
    ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $quickMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('revenue-analytics.index', ['month' => $qm])); ?>" class="pn-btn <?php echo e($activeMonth === $qm ? 'active' : ''); ?>" style="font-size:.65rem;">
        <?php echo e(\Carbon\Carbon::parse($qm.'-01')->format('M Y')); ?>

    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <span style="font-size:0.62rem;color:var(--bs-surface-400);margin-left:0.25rem;">
        <?php echo e($periodStart->format('M d')); ?> &ndash; <?php echo e($periodEnd->format('M d, Y')); ?>

    </span>

    <form method="GET" action="<?php echo e(route('revenue-analytics.index')); ?>" class="pn-custom">
        <span style="font-size:0.65rem;color:var(--bs-surface-500);">Custom:</span>
        <input type="date" name="start" value="<?php echo e(request('start', $periodStart->toDateString())); ?>">
        <span style="font-size:0.65rem;color:var(--bs-surface-400);">to</span>
        <input type="date" name="end" value="<?php echo e(request('end', $periodEnd->toDateString())); ?>">
        <button type="submit" class="pn-btn" style="border-color:#556ee6;color:#556ee6;"><i class="bx bx-filter-alt"></i> Apply</button>
    </form>
</div>


<div class="kpi-row">
    <div class="kpi-card k-purple ex-card">
        <i class="bx bx-time-five k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($projected_revenue, 0)); ?></div>
        <div class="k-sub" style="color:#5b49c7;"><?php echo e($pending_count); ?> drafts</div>
        <div class="k-lbl">Projected Revenue</div>
    </div>
    <div class="kpi-card k-teal ex-card">
        <i class="bx bx-loader-alt k-icon"></i>
        <div class="k-val"><?php echo e($pending_count); ?></div>
        <div class="k-lbl">Pending Drafts</div>
    </div>
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-money k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($pending_premium, 0)); ?></div>
        <div class="k-lbl">Drafts Premium</div>
    </div>
</div>


<div class="row g-2">

    
    <div class="col-xl-9 col-lg-8">

        
        <div class="row g-2 mb-2">
            <div class="col-md-5">
                <div class="ex-card sec-card">
                    <div class="sec-hdr">
                        <h6><i class="bx bx-pie-chart-alt-2"></i> Revenue by Partner</h6>
                    </div>
                    <div class="sec-body" style="padding:0.4rem 0.5rem;">
                        <div id="partnerDonutChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="ex-card sec-card">
                    <div class="sec-hdr">
                        <h6><i class="bx bx-bar-chart-alt-2"></i> Monthly Revenue</h6>
                    </div>
                    <div class="sec-body" style="padding:0.4rem 0.5rem;">
                        <div id="monthlyRevenueChart"></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ex-card sec-card">
            <div class="sec-hdr" style="gap:0.5rem;flex-wrap:wrap;">
                <ul class="nav nav-tabs nav-tabs-sm" id="salesTab" role="tablist" style="border:none;gap:0.3rem;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pendingPane" type="button" role="tab">
                            <i class="bx bx-time-five" style="font-size:.85rem;"></i> Pending Drafts
                            <span class="badge bg-soft-purple text-purple ms-1" style="font-size:.58rem;font-weight:700;"><?php echo e($pending_count); ?></span>
                        </button>
                    </li>

                </ul>
            </div>
            <div class="tab-content" id="salesTabContent">

                
                <div class="tab-pane fade show active" id="pendingPane" role="tabpanel">
                    <div class="scroll-tbl">
                        <table class="ex-tbl">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Closer</th>
                                    <th>Partner</th>
                                    <th>Carrier</th>
                                    <th class="text-center">Premium</th>
                                    <th class="text-center">Proj. Revenue</th>
                                    <th>Sale Date</th>
                                    <th>Followup</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pending_leads->sortByDesc('sale_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $rev = $lead_projected_revenues[$lead->id] ?? 0; ?>
                                <tr>
                                    <td style="color:var(--bs-surface-400);"><?php echo e($i + 1); ?></td>
                                    <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                                    <td><?php echo e($lead->closer_name ?? '—'); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->partner): ?>
                                            <span class="bd-mini bd-teal"><?php echo e($lead->partner->code ?? $lead->partner->name); ?></span>
                                        <?php else: ?>
                                            <span style="color:var(--bs-surface-400);">—</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><?php echo e($lead->carrier_name ?? '—'); ?></td>
                                    <td class="text-center"><span class="bd-mini bd-blue">$<?php echo e(number_format($lead->monthly_premium ?? 0, 0)); ?></span></td>
                                    <td class="text-center"><span class="bd-mini bd-purple">$<?php echo e(number_format($rev, 0)); ?></span></td>
                                    <td style="color:var(--bs-surface-500);font-size:0.68rem;"><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—'); ?></td>
                                    <td style="color:var(--bs-surface-500);font-size:0.68rem;"><?php echo e($lead->followup_done_at ? \Carbon\Carbon::parse($lead->followup_done_at)->format('M d') : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No pending drafts for this period</td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    <div class="col-xl-3 col-lg-4">
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-trophy"></i> Top Closers</h6>
                <span style="font-size:0.62rem;color:var(--bs-surface-400);">by projected revenue</span>
            </div>
            <div class="sec-body" style="padding:0.4rem 0.6rem;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $top_closers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $closer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $maxRev = $top_closers->first()['revenue'] ?? 1; $pct = $maxRev > 0 ? ($closer['revenue'] / $maxRev) * 100 : 0; ?>
                <div style="margin-bottom:0.5rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.15rem;">
                        <span style="font-size:0.72rem;font-weight:600;color:var(--bs-surface-700);display:flex;align-items:center;gap:0.3rem;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?><i class="bx bx-star" style="color:#d4af37;font-size:.85rem;"></i><?php elseif($idx === 1): ?><i class="bx bx-star" style="color:#94a3b8;font-size:.85rem;"></i><?php elseif($idx === 2): ?><i class="bx bx-star" style="color:#cd7f32;font-size:.85rem;"></i><?php else: ?><span style="width:.85rem;display:inline-block;font-size:.65rem;color:var(--bs-surface-400);"><?php echo e($idx+1); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php echo e(Str::limit($closer['name'], 18)); ?>

                        </span>
                        <div style="display:flex;align-items:center;gap:0.3rem;">
                            <span class="bd-mini bd-blue" style="font-size:.58rem;"><?php echo e($closer['count']); ?></span>
                            <span style="font-size:0.68rem;font-weight:700;color:#b89730;">$<?php echo e(number_format($closer['revenue'], 0)); ?></span>
                        </div>
                    </div>
                    <div style="height:4px;border-radius:2px;background:rgba(0,0,0,.06);overflow:hidden;">
                        <div style="width:<?php echo e(number_format($pct, 1)); ?>%;height:100%;border-radius:2px;background:linear-gradient(90deg,#d4af37,#e8c84a);"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;padding:1.5rem 0;color:var(--bs-surface-400);">
                    <i class="bx bx-trophy" style="font-size:1.4rem;opacity:.3;display:block;"></i>
                    <span style="font-size:.72rem;">No data yet</span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="ex-card sec-card" style="margin-bottom:0.65rem;margin-top:0.65rem;">
    <div class="sec-hdr">
        <h6><i class="bx bx-buildings"></i> Revenue by Partner &amp; Carrier</h6>
        <span style="font-size:0.62rem;color:var(--bs-surface-400);">Pending drafts grouped by partner → carrier (cluster)</span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $partner_carrier_breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div style="border-bottom:1px solid rgba(0,0,0,.04);padding:0.55rem 0.75rem;">
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.35rem;flex-wrap:wrap;">
            <span style="font-size:0.72rem;font-weight:700;color:var(--bs-surface-700);">
                <i class="bx bx-user-circle" style="font-size:0.85rem;opacity:.6;"></i>
                <?php echo e($pb['partner_name']); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pb['partner_code'] !== '—'): ?>
                    <span style="font-size:0.6rem;font-weight:600;padding:.1rem .35rem;border-radius:.2rem;background:rgba(102,126,234,.1);color:var(--bs-gradient-start);margin-left:.3rem;"><?php echo e($pb['partner_code']); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="bd-mini bd-gold ms-auto"><?php echo e($pb['total_count']); ?> drafts</span>
            <span class="bd-mini bd-green">$<?php echo e(number_format($pb['total_revenue'], 0)); ?></span>
        </div>
        <div class="scroll-tbl" style="max-height:180px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Carrier</th>
                        <th class="text-center">Sales</th>
                        <th class="text-center">Total Premium</th>
                        <th class="text-center">Revenue (Commission)</th>
                        <th class="text-center">% of Partner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pb['carriers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pct = $pb['total_revenue'] > 0 ? ($cb['revenue'] / $pb['total_revenue']) * 100 : 0; ?>
                    <tr>
                        <td><span style="font-weight:600;"><?php echo e($cb['carrier']); ?></span></td>
                        <td class="text-center"><span class="bd-mini bd-blue"><?php echo e($cb['count']); ?></span></td>
                        <td class="text-center"><span class="bd-mini bd-teal">$<?php echo e(number_format($cb['premium'], 0)); ?></span></td>
                        <td class="text-center"><span class="bd-mini bd-gold">$<?php echo e(number_format($cb['revenue'], 0)); ?></span></td>
                        <td class="text-center">
                            <div style="display:flex;align-items:center;gap:0.3rem;">
                                <div style="flex:1;height:5px;border-radius:3px;background:rgba(0,0,0,.06);overflow:hidden;">
                                    <div style="width:<?php echo e(number_format($pct, 1)); ?>%;height:100%;border-radius:3px;background:linear-gradient(90deg,#d4af37,#e8c84a);"></div>
                                </div>
                                <span style="font-size:0.62rem;font-weight:700;color:var(--bs-surface-600);min-width:32px;text-align:right;"><?php echo e(number_format($pct, 1)); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center;padding:2rem;color:var(--bs-surface-400);">
        <i class="bx bx-buildings" style="font-size:1.5rem;opacity:.3;display:block;margin-bottom:.4rem;"></i>
        <span style="font-size:0.75rem;">No pending drafts with partner assignments yet</span>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.getAttribute('data-theme') === 'dark';
    const txtColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
    const bgCard = isDark ? '#1e293b' : '#fff';

    // ── Revenue by Partner Donut ──────────────────────────────────────────
    <?php
        $partnerLabels = $partner_carrier_breakdown->pluck('partner_name');
        $partnerRevenues = $partner_carrier_breakdown->pluck('total_revenue')->map(fn($v) => round($v, 2));
        $partnerColors = ['#d4af37','#556ee6','#34c38f','#f1b44c','#f46a6a','#7c69ef','#50a5f1','#e83e8c'];
    ?>
    const partnerLabels = <?php echo json_encode($partnerLabels->values(), 15, 512) ?>;
    const partnerRevenues = <?php echo json_encode($partnerRevenues->values(), 15, 512) ?>;
    if (partnerRevenues.length > 0 && partnerRevenues.some(v => v > 0)) {
        new ApexCharts(document.querySelector('#partnerDonutChart'), {
            series: partnerRevenues,
            chart: { type: 'donut', height: 220, fontFamily: 'inherit' },
            labels: partnerLabels,
            colors: <?php echo json_encode($partnerColors, 15, 512) ?>,
            stroke: { width: 2, colors: [bgCard] },
            legend: { position: 'bottom', fontSize: '10px', labels: { colors: txtColor } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 }, formatter: (val) => val.toFixed(1) + '%' },
            plotOptions: { pie: { donut: { size: '58%', labels: {
                show: true, total: { show: true, label: 'Total', fontSize: '10px', color: txtColor,
                    formatter: () => '$<?php echo e(number_format($projected_revenue, 0)); ?>'
                }
            } } } },
            tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => '$' + val.toLocaleString('en-US', {minimumFractionDigits: 0}) } }
        }).render();
    } else {
        document.querySelector('#partnerDonutChart').innerHTML = '<div style="text-align:center;padding:40px 0;color:' + txtColor + '"><i class="bx bx-pie-chart-alt-2" style="font-size:1.5rem;opacity:.4;"></i><p style="margin-top:4px;font-size:.72rem;">No data</p></div>';
    }

    // ── Monthly Revenue Bar ───────────────────────────────────────────────
    <?php
        $sortedMonths = $monthly_data->sortKeys()->forget('Unknown');
        $monthLabels  = $sortedMonths->keys()->map(fn($m) => \Carbon\Carbon::parse($m.'-01')->format('M Y'))->values();
        $monthRevArr  = $sortedMonths->pluck('revenue')->values();
        $monthPremArr = $sortedMonths->pluck('premium')->values();
    ?>
    const monthLabels = <?php echo json_encode($monthLabels, 15, 512) ?>;
    if (monthLabels.length > 0) {
        new ApexCharts(document.querySelector('#monthlyRevenueChart'), {
            series: [
                { name: 'Revenue', type: 'column', data: <?php echo json_encode($monthRevArr, 15, 512) ?> },
                { name: 'Premium', type: 'line', data: <?php echo json_encode($monthPremArr, 15, 512) ?> }
            ],
            chart: { height: 220, fontFamily: 'inherit', toolbar: { show: false } },
            colors: ['#d4af37', '#556ee6'],
            plotOptions: { bar: { borderRadius: 3, columnWidth: '55%' } },
            stroke: { width: [0, 2], curve: 'smooth' },
            markers: { size: [0, 4] },
            xaxis: { categories: monthLabels, labels: { style: { colors: txtColor, fontSize: '9px' }, rotate: -45 } },
            yaxis: { labels: { style: { colors: txtColor, fontSize: '9px' }, formatter: (val) => '$' + (val >= 1000 ? (val/1000).toFixed(1) + 'k' : val) } },
            legend: { position: 'top', fontSize: '10px', labels: { colors: txtColor } },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => '$' + val.toLocaleString('en-US', {minimumFractionDigits: 0}) } }
        }).render();
    } else {
        document.querySelector('#monthlyRevenueChart').innerHTML = '<div style="text-align:center;padding:40px 0;color:' + txtColor + '"><i class="bx bx-bar-chart-alt-2" style="font-size:1.5rem;opacity:.4;"></i><p style="margin-top:4px;font-size:.72rem;">No monthly data</p></div>';
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/revenue-analytics/index.blade.php ENDPATH**/ ?>