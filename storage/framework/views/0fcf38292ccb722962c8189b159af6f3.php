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
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="kpi-row">
    <div class="kpi-card k-gold ex-card">
        <i class="bx bx-dollar-circle k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($total_revenue, 0)); ?></div>
        <div class="k-lbl">Total Revenue</div>
    </div>
    <div class="kpi-card k-blue ex-card">
        <i class="bx bx-receipt k-icon"></i>
        <div class="k-val"><?php echo e($total_count); ?></div>
        <div class="k-lbl">Total Sales</div>
    </div>
    <div class="kpi-card k-teal ex-card">
        <i class="bx bx-bar-chart-alt-2 k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($total_count > 0 ? $total_revenue / $total_count : 0, 0)); ?></div>
        <div class="k-lbl">Avg / Sale</div>
    </div>
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-check-circle k-icon"></i>
        <div class="k-val"><?php echo e(number_format($good_percentage, 1)); ?>%</div>
        <div class="k-lbl">Quality Ratio</div>
    </div>
    <div class="kpi-card k-red ex-card">
        <i class="bx bx-x-circle k-icon"></i>
        <div class="k-val"><?php echo e(number_format($bad_percentage, 1)); ?>%</div>
        <div class="k-lbl">Bad Ratio</div>
    </div>
</div>


<div class="kpi-row">
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-check-double k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($good_revenue, 0)); ?></div>
        <div class="k-sub" style="color:#1a8754;"><?php echo e($good_count); ?> sales</div>
        <div class="k-lbl">Good Revenue</div>
    </div>
    <div class="kpi-card k-warn ex-card">
        <i class="bx bx-error-circle k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($average_revenue, 0)); ?></div>
        <div class="k-sub" style="color:#b87a14;"><?php echo e($average_count); ?> sales</div>
        <div class="k-lbl">Average Revenue</div>
    </div>
    <div class="kpi-card k-red ex-card">
        <i class="bx bx-x-circle k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($bad_revenue, 0)); ?></div>
        <div class="k-sub" style="color:#c84646;"><?php echo e($bad_count); ?> sales</div>
        <div class="k-lbl">Bad Revenue</div>
    </div>
    <div class="kpi-card k-purple ex-card">
        <i class="bx bx-help-circle k-icon"></i>
        <div class="k-val">$<?php echo e(number_format($unverified_revenue, 0)); ?></div>
        <div class="k-sub" style="color:#5b49c7;"><?php echo e($unverified_count); ?> sales</div>
        <div class="k-lbl">Unverified Revenue</div>
    </div>
</div>


<div class="row g-2">

    
    <div class="col-xl-9 col-lg-8">

        
        <div class="row g-2 mb-2">
            <div class="col-md-5">
                <div class="ex-card sec-card">
                    <div class="sec-hdr">
                        <h6><i class="bx bx-pie-chart-alt-2"></i> Revenue Share</h6>
                    </div>
                    <div class="sec-body" style="padding:0.4rem 0.5rem;">
                        <div id="revenueDonutChart"></div>
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
            <div class="sec-hdr">
                <h6><i class="bx bx-list-check"></i> Bank Verified Sales</h6>
                <div class="filter-tabs">
                    <button class="filter-tab-btn active" onclick="filterSales('all', this)">All (<?php echo e($total_count); ?>)</button>
                    <button class="filter-tab-btn" onclick="filterSales('good', this)">Good (<?php echo e($good_count); ?>)</button>
                    <button class="filter-tab-btn" onclick="filterSales('average', this)">Avg (<?php echo e($average_count); ?>)</button>
                    <button class="filter-tab-btn" onclick="filterSales('bad', this)">Bad (<?php echo e($bad_count); ?>)</button>
                    <button class="filter-tab-btn" onclick="filterSales('unverified', this)">Unverified (<?php echo e($unverified_count); ?>)</button>
                </div>
            </div>
            <div class="scroll-tbl">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Closer</th>
                            <th>Carrier</th>
                            <th class="text-center">Premium</th>
                            <th class="text-center">Revenue</th>
                            <th class="text-center">Status</th>
                            <th>Issued</th>
                        </tr>
                    </thead>
                    <tbody id="salesTable">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $issued_sales->sortByDesc('issuance_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $bvStatus = $sale->bank_verification_status;
                            $statusClass = match($bvStatus) {
                                \App\Support\Statuses::BANK_GOOD => 'bd-green',
                                \App\Support\Statuses::BANK_AVERAGE => 'bd-warn',
                                \App\Support\Statuses::BANK_BAD => 'bd-red',
                                default => 'bd-purple',
                            };
                            $statusLabel = match($bvStatus) {
                                \App\Support\Statuses::BANK_GOOD => 'Good',
                                \App\Support\Statuses::BANK_AVERAGE => 'Average',
                                \App\Support\Statuses::BANK_BAD => 'Bad',
                                default => 'Unverified',
                            };
                            $filterTag = match($bvStatus) {
                                \App\Support\Statuses::BANK_GOOD => 'good',
                                \App\Support\Statuses::BANK_AVERAGE => 'average',
                                \App\Support\Statuses::BANK_BAD => 'bad',
                                default => 'unverified',
                            };
                            $revenue = $sale->agent_revenue ?? $sale->monthly_premium ?? 0;
                        ?>
                        <tr class="sale-row" data-status="<?php echo e($filterTag); ?>">
                            <td style="color:var(--bs-surface-400);"><?php echo e($i + 1); ?></td>
                            <td><strong><?php echo e($sale->cn_name ?? 'N/A'); ?></strong></td>
                            <td><?php echo e($sale->closer_name ?? '—'); ?></td>
                            <td><?php echo e($sale->carrier_name ?? '—'); ?></td>
                            <td class="text-center"><span class="bd-mini bd-blue">$<?php echo e(number_format($sale->monthly_premium ?? 0, 0)); ?></span></td>
                            <td class="text-center"><span class="bd-mini bd-gold">$<?php echo e(number_format($revenue, 0)); ?></span></td>
                            <td class="text-center"><span class="bd-mini <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span></td>
                            <td style="color:var(--bs-surface-500);font-size:0.68rem;"><?php echo e($sale->issuance_date ? \Carbon\Carbon::parse($sale->issuance_date)->format('M d, Y') : '—'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No issued sales found</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="col-xl-3 col-lg-4">

        
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-analyse"></i> Revenue Ratios</h6>
            </div>
            <div class="sec-body">
                <div class="ratio-row">
                    <div class="ratio-block r-green">
                        <div class="r-val"><?php echo e(number_format($good_revenue_percentage, 1)); ?>%</div>
                        <div class="r-lbl">Good Rev</div>
                    </div>
                    <div class="ratio-block r-warn">
                        <div class="r-val"><?php echo e(number_format($average_revenue_percentage, 1)); ?>%</div>
                        <div class="r-lbl">Avg Rev</div>
                    </div>
                </div>
                <div class="ratio-row mt-2">
                    <div class="ratio-block r-red">
                        <div class="r-val"><?php echo e(number_format($bad_revenue_percentage, 1)); ?>%</div>
                        <div class="r-lbl">Bad Rev</div>
                    </div>
                    <div class="ratio-block r-purple">
                        <div class="r-val"><?php echo e(number_format($unverified_revenue_percentage, 1)); ?>%</div>
                        <div class="r-lbl">Unverified</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-doughnut-chart"></i> Sales Distribution</h6>
            </div>
            <div class="sec-body" style="padding:0.4rem 0.5rem;">
                <div id="salesCountChart"></div>
            </div>
        </div>

        
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-transfer"></i> Rev vs Count</h6>
            </div>
            <div class="sec-body" style="padding:0.4rem 0.5rem;">
                <div id="comparisonChart"></div>
            </div>
        </div>

    </div>
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

    // ── Revenue Donut ──
    const revenueVals = [<?php echo e($good_revenue); ?>, <?php echo e($average_revenue); ?>, <?php echo e($bad_revenue); ?>, <?php echo e($unverified_revenue); ?>];
    if (revenueVals.some(v => v > 0)) {
        new ApexCharts(document.querySelector('#revenueDonutChart'), {
            series: revenueVals,
            chart: { type: 'donut', height: 220, fontFamily: 'inherit' },
            labels: ['Good', 'Average', 'Bad', 'Unverified'],
            colors: ['#34c38f', '#f1b44c', '#f46a6a', '#7c69ef'],
            stroke: { width: 2, colors: [bgCard] },
            legend: { position: 'bottom', fontSize: '10px', labels: { colors: txtColor } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 }, formatter: (val) => val.toFixed(1) + '%' },
            plotOptions: { pie: { donut: { size: '58%', labels: {
                show: true, total: { show: true, label: 'Total', fontSize: '10px', color: txtColor,
                    formatter: () => '$<?php echo e(number_format($total_revenue, 0)); ?>'
                }
            } } } },
            tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => '$' + val.toLocaleString('en-US', {minimumFractionDigits: 2}) } }
        }).render();
    } else {
        document.querySelector('#revenueDonutChart').innerHTML = '<div style="text-align:center;padding:40px 0;color:' + txtColor + '"><i class="bx bx-pie-chart-alt-2" style="font-size:1.5rem;opacity:.4;"></i><p style="margin-top:4px;font-size:.72rem;">No data</p></div>';
    }

    // ── Monthly Revenue Stacked Bar ──
    <?php
        $sortedMonths = $monthly_data->sortKeys()->forget('Unknown');
        $monthLabels = $sortedMonths->keys()->map(function($m) { return \Carbon\Carbon::parse($m . '-01')->format('M Y'); })->values();
        $goodArr = $sortedMonths->pluck('good')->values();
        $avgArr = $sortedMonths->pluck('average')->values();
        $badArr = $sortedMonths->pluck('bad')->values();
        $unvArr = $sortedMonths->pluck('unverified')->values();
    ?>

    const monthLabels = <?php echo json_encode($monthLabels, 15, 512) ?>;
    if (monthLabels.length > 0) {
        new ApexCharts(document.querySelector('#monthlyRevenueChart'), {
            series: [
                { name: 'Good', data: <?php echo json_encode($goodArr, 15, 512) ?> },
                { name: 'Average', data: <?php echo json_encode($avgArr, 15, 512) ?> },
                { name: 'Bad', data: <?php echo json_encode($badArr, 15, 512) ?> },
                { name: 'Unverified', data: <?php echo json_encode($unvArr, 15, 512) ?> }
            ],
            chart: { type: 'bar', height: 220, stacked: true, fontFamily: 'inherit', toolbar: { show: false } },
            colors: ['#34c38f', '#f1b44c', '#f46a6a', '#7c69ef'],
            plotOptions: { bar: { borderRadius: 3, columnWidth: '55%' } },
            xaxis: { categories: monthLabels, labels: { style: { colors: txtColor, fontSize: '9px' }, rotate: -45 } },
            yaxis: { labels: { style: { colors: txtColor, fontSize: '9px' }, formatter: (val) => '$' + (val >= 1000 ? (val/1000).toFixed(1) + 'k' : val) } },
            legend: { position: 'top', fontSize: '10px', labels: { colors: txtColor } },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => '$' + val.toLocaleString('en-US', {minimumFractionDigits: 2}) } }
        }).render();
    } else {
        document.querySelector('#monthlyRevenueChart').innerHTML = '<div style="text-align:center;padding:40px 0;color:' + txtColor + '"><i class="bx bx-bar-chart-alt-2" style="font-size:1.5rem;opacity:.4;"></i><p style="margin-top:4px;font-size:.72rem;">No monthly data</p></div>';
    }

    // ── Sales Count Radial Bar ──
    const salesCounts = [<?php echo e($good_count); ?>, <?php echo e($average_count); ?>, <?php echo e($bad_count); ?>, <?php echo e($unverified_count); ?>];
    const salesTotal = salesCounts.reduce((a, b) => a + b, 0);
    if (salesTotal > 0) {
        const salesPcts = salesCounts.map(c => parseFloat(((c / salesTotal) * 100).toFixed(1)));
        new ApexCharts(document.querySelector('#salesCountChart'), {
            series: salesPcts,
            chart: { type: 'radialBar', height: 200, fontFamily: 'inherit' },
            labels: ['Good', 'Average', 'Bad', 'Unverified'],
            colors: ['#34c38f', '#f1b44c', '#f46a6a', '#7c69ef'],
            plotOptions: { radialBar: {
                hollow: { size: '30%' },
                track: { background: isDark ? '#334155' : '#f1f5f9', strokeWidth: '100%' },
                dataLabels: {
                    name: { fontSize: '10px', color: txtColor },
                    value: { fontSize: '12px', fontWeight: 700, color: isDark ? '#f1f5f9' : '#1e293b', formatter: (val) => val + '%' },
                    total: { show: true, label: 'Total', fontSize: '10px', color: txtColor, formatter: () => salesTotal }
                }
            } },
            legend: { show: true, position: 'bottom', fontSize: '10px', labels: { colors: txtColor },
                formatter: (name, opts) => name + ': ' + salesCounts[opts.seriesIndex]
            }
        }).render();
    } else {
        document.querySelector('#salesCountChart').innerHTML = '<div style="text-align:center;padding:30px 0;color:' + txtColor + '"><i class="bx bx-doughnut-chart" style="font-size:1.5rem;opacity:.4;"></i><p style="margin-top:4px;font-size:.72rem;">No data</p></div>';
    }

    // ── Revenue vs Count Comparison ──
    new ApexCharts(document.querySelector('#comparisonChart'), {
        series: [
            { name: 'Revenue', type: 'column', data: [<?php echo e($good_revenue); ?>, <?php echo e($average_revenue); ?>, <?php echo e($bad_revenue); ?>, <?php echo e($unverified_revenue); ?>] },
            { name: 'Count', type: 'line', data: [<?php echo e($good_count); ?>, <?php echo e($average_count); ?>, <?php echo e($bad_count); ?>, <?php echo e($unverified_count); ?>] }
        ],
        chart: { height: 200, fontFamily: 'inherit', toolbar: { show: false } },
        colors: ['#d4af37', '#556ee6'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        xaxis: { categories: ['Good', 'Avg', 'Bad', 'Unv'], labels: { style: { colors: txtColor, fontSize: '10px' } } },
        yaxis: [
            { labels: { style: { colors: txtColor, fontSize: '9px' }, formatter: (val) => '$' + (val >= 1000 ? (val/1000).toFixed(0) + 'k' : val) } },
            { opposite: true, labels: { style: { colors: txtColor, fontSize: '9px' } } }
        ],
        stroke: { width: [0, 2], curve: 'smooth' },
        markers: { size: [0, 4] },
        legend: { position: 'top', fontSize: '10px', labels: { colors: txtColor } },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();
});

// ── Sales Filter ──
function filterSales(status, btn) {
    document.querySelectorAll('.filter-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.sale-row').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/revenue-analytics/index.blade.php ENDPATH**/ ?>