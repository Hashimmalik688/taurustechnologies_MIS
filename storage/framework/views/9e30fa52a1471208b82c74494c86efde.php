<?php $__env->startSection('title', 'My Dock Records'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   My Dock Records — Polished CRM Dashboard
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
.dk-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: box-shadow .2s;
}
.dk-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* ── Summary Banner ── */
.dk-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    padding: 0.85rem 1.2rem;
    margin-bottom: 0.75rem;
    border-left: 3px solid #f1b44c;
    background: linear-gradient(135deg, rgba(241,180,76,.06) 0%, rgba(241,180,76,.02) 100%);
}
.dk-banner .dk-user {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}
.dk-banner .dk-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f1b44c, #e8a020);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.dk-banner .dk-name {
    font-weight: 600;
    font-size: 0.92rem;
    line-height: 1.2;
}
.dk-banner .dk-role {
    font-size: 0.68rem;
    color: var(--bs-surface-500);
    padding: 0.15rem 0.5rem;
    background: rgba(241,180,76,.1);
    border-radius: 1rem;
    display: inline-block;
    margin-top: 2px;
}
.dk-banner .dk-total-val {
    text-align: right;
}
.dk-banner .dk-total-val .amt {
    font-size: 1.35rem;
    font-weight: 700;
    color: #c84646;
    line-height: 1;
}
.dk-banner .dk-total-val .lbl {
    font-size: 0.62rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 2px;
}

/* ── Stat Cards ── */
.dk-stats { display: flex; gap: 0.55rem; flex-wrap: wrap; margin-bottom: 0.75rem; }
.dk-stat {
    flex: 1 1 110px;
    min-width: 100px;
    padding: 0.7rem 0.65rem;
    border-radius: 0.55rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.dk-stat:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.dk-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.55rem 0.55rem 0 0;
}
.dk-stat .s-icon {
    font-size: 1.2rem;
    margin-bottom: 0.3rem;
    display: block;
    line-height: 1;
}
.dk-stat .s-val { font-size: 1.3rem; font-weight: 700; line-height: 1; }
.dk-stat .s-lbl {
    font-size: 0.6rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 0.2rem;
}

/* Stat variants */
.dk-stat.s-active   { background: rgba(244,106,106,.06); }
.dk-stat.s-active::before   { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.dk-stat.s-active .s-val, .dk-stat.s-active .s-icon { color: #c84646; }

.dk-stat.s-applied  { background: rgba(52,195,143,.06); }
.dk-stat.s-applied::before  { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.dk-stat.s-applied .s-val, .dk-stat.s-applied .s-icon { color: #1a8754; }

.dk-stat.s-cancelled { background: rgba(108,117,125,.06); }
.dk-stat.s-cancelled::before { background: linear-gradient(90deg, #6c757d, #95a0a8); }
.dk-stat.s-cancelled .s-val, .dk-stat.s-cancelled .s-icon { color: #6c757d; }

.dk-stat.s-total    { background: rgba(85,110,230,.05); }
.dk-stat.s-total::before    { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.dk-stat.s-total .s-val, .dk-stat.s-total .s-icon { color: #556ee6; }

.dk-stat.s-amount   { background: rgba(241,180,76,.06); }
.dk-stat.s-amount::before   { background: linear-gradient(90deg, #f1b44c, #f5cd7e); }
.dk-stat.s-amount .s-val, .dk-stat.s-amount .s-icon { color: #b87a14; }

/* ── Chart Container ── */
.dk-chart {
    padding: 0.75rem 0.85rem 0.25rem;
    margin-bottom: 0.75rem;
}
.dk-chart .chart-hdr {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--bs-surface-600);
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.dk-chart .chart-hdr i { font-size: 1rem; opacity: .7; }

/* ── Records Table ── */
.dk-table-wrap {
    padding: 0.85rem;
}
.dk-table-wrap .tbl-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.6rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.dk-table-wrap .tbl-hdr h6 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.dk-table-wrap .tbl-hdr .badge-count {
    font-size: 0.65rem;
    background: rgba(241,180,76,.15);
    color: #b87a14;
    padding: 0.2rem 0.55rem;
    border-radius: 1rem;
    font-weight: 700;
}

.dk-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.78rem;
}
.dk-tbl thead th {
    text-transform: uppercase;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.5rem 0.65rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
}
.dk-tbl thead th:first-child { border-radius: 0.4rem 0 0 0; }
.dk-tbl thead th:last-child { border-radius: 0 0.4rem 0 0; }
.dk-tbl tbody td {
    padding: 0.55rem 0.65rem;
    border-bottom: 1px solid rgba(0,0,0,.04);
    vertical-align: middle;
}
.dk-tbl tbody tr {
    transition: background .12s;
}
.dk-tbl tbody tr:hover {
    background: rgba(241,180,76,.03);
}

/* Row elements */
.dk-amt {
    font-weight: 700;
    font-size: 0.8rem;
    color: #c84646;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.2rem 0.5rem;
    background: rgba(244,106,106,.08);
    border-radius: 0.3rem;
}
.dk-reason {
    max-width: 200px;
    line-height: 1.35;
}
.dk-reason .main { font-weight: 500; }
.dk-reason .note {
    font-size: 0.68rem;
    color: var(--bs-surface-500);
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}
.dk-by {
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.dk-by .by-avatar {
    width: 26px; height: 26px;
    border-radius: 50%;
    font-size: 0.6rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #556ee6;
    background: rgba(85,110,230,.1);
    flex-shrink: 0;
}
.dk-by .by-name { font-weight: 600; font-size: 0.76rem; }
.dk-by .by-time { font-size: 0.62rem; color: var(--bs-surface-500); }

/* Status pills */
.st-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.55rem;
    border-radius: 1.5rem;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: .3px;
    text-transform: uppercase;
}
.st-pill.st-active   { background: rgba(241,180,76,.12); color: #b87a14; }
.st-pill.st-applied  { background: rgba(52,195,143,.12); color: #1a8754; }
.st-pill.st-cancelled { background: rgba(108,117,125,.1); color: #6c757d; }

/* ── Empty State ── */
.dk-empty {
    text-align: center;
    padding: 3rem 1rem;
}
.dk-empty .empty-icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(52,195,143,.15), rgba(52,195,143,.05));
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
}
.dk-empty .empty-icon i {
    font-size: 1.8rem;
    color: #34c38f;
}
.dk-empty h6 { color: #34c38f; font-weight: 700; font-size: 0.95rem; }
.dk-empty p { font-size: 0.78rem; color: var(--bs-surface-500); margin: 0; }

/* ── Info Panel ── */
.dk-info {
    padding: 0.75rem 0.95rem;
    margin-bottom: 0.5rem;
    border-left: 3px solid #556ee6;
    background: linear-gradient(135deg, rgba(85,110,230,.05) 0%, transparent 100%);
}
.dk-info .info-title {
    font-size: 0.78rem;
    font-weight: 600;
    color: #556ee6;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.dk-info .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}
@media (max-width: 768px) {
    .dk-info .info-grid { grid-template-columns: 1fr; }
}
.dk-info .info-grid h6 {
    font-size: 0.72rem;
    font-weight: 700;
    margin-bottom: 0.35rem;
    color: var(--bs-surface-700);
}
.dk-info .info-grid ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.dk-info .info-grid li {
    font-size: 0.7rem;
    color: var(--bs-surface-500);
    padding: 0.15rem 0;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.dk-info .info-grid li i { font-size: 0.85rem; }

/* ── Pagination ── */
.dk-pagination {
    padding: 0.5rem 0.85rem;
}
.dk-pagination .pagination {
    margin: 0;
    gap: 0.2rem;
}
.dk-pagination .page-link {
    font-size: 0.72rem;
    padding: 0.3rem 0.6rem;
    border-radius: 0.3rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row g-3 mt-1">

        
        <div class="col-xl-5 col-lg-6">

            
            <div class="dk-card dk-banner">
                <div class="dk-user">
                    <div class="dk-avatar"><?php echo e(substr($user->name, 0, 1)); ?></div>
                    <div>
                        <div class="dk-name"><?php echo e($user->name); ?></div>
                        <span class="dk-role">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($role->name); ?><?php echo e(!$loop->last ? ', ' : ''); ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="dk-total-val">
                    <div class="amt">Rs <?php echo e(number_format($totalDocked, 0)); ?></div>
                    <div class="lbl">Active Docks</div>
                </div>
            </div>

            
            <div class="dk-stats">
                <div class="dk-stat s-active dk-card">
                    <i class="bx bx-error-circle s-icon"></i>
                    <div class="s-val"><?php echo e($statusCounts['active'] ?? 0); ?></div>
                    <div class="s-lbl">Active</div>
                </div>
                <div class="dk-stat s-applied dk-card">
                    <i class="bx bx-check-double s-icon"></i>
                    <div class="s-val"><?php echo e($statusCounts['applied'] ?? 0); ?></div>
                    <div class="s-lbl">Applied</div>
                </div>
                <div class="dk-stat s-cancelled dk-card">
                    <i class="bx bx-x-circle s-icon"></i>
                    <div class="s-val"><?php echo e($statusCounts['cancelled'] ?? 0); ?></div>
                    <div class="s-lbl">Cancelled</div>
                </div>
                <div class="dk-stat s-total dk-card">
                    <i class="bx bx-receipt s-icon"></i>
                    <div class="s-val"><?php echo e($totalRecords); ?></div>
                    <div class="s-lbl">Total</div>
                </div>
            </div>

            
            <div class="dk-stats">
                <div class="dk-stat s-active dk-card">
                    <div class="s-val" style="font-size:1rem">Rs <?php echo e(number_format($totalDocked, 0)); ?></div>
                    <div class="s-lbl">Active Amount</div>
                </div>
                <div class="dk-stat s-applied dk-card">
                    <div class="s-val" style="font-size:1rem">Rs <?php echo e(number_format($totalApplied, 0)); ?></div>
                    <div class="s-lbl">Applied Amount</div>
                </div>
                <div class="dk-stat s-amount dk-card">
                    <div class="s-val" style="font-size:1rem">Rs <?php echo e(number_format($totalDocked + $totalApplied, 0)); ?></div>
                    <div class="s-lbl">Lifetime Total</div>
                </div>
            </div>

            
            <div class="dk-card dk-chart">
                <div class="chart-hdr">
                    <i class="bx bx-trending-up"></i> Monthly Dock Trend
                </div>
                <div id="dockTrendChart"></div>
            </div>

            
            <div class="dk-card dk-chart">
                <div class="chart-hdr">
                    <i class="bx bx-pie-chart-alt-2"></i> Dock Reasons Breakdown
                </div>
                <div id="dockReasonChart"></div>
            </div>

            
            <div class="dk-card dk-info">
                <div class="info-title">
                    <i class="bx bx-info-circle"></i> Understanding Dock Records
                </div>
                <div class="info-grid">
                    <div>
                        <h6>Status Meanings</h6>
                        <ul>
                            <li><span class="st-pill st-active"><i class="bx bx-time-five"></i> Active</span> — Pending deduction</li>
                            <li><span class="st-pill st-applied"><i class="bx bx-check"></i> Applied</span> — Already deducted</li>
                            <li><span class="st-pill st-cancelled"><i class="bx bx-x"></i> Cancelled</span> — Removed</li>
                        </ul>
                    </div>
                    <div>
                        <h6>Important Notes</h6>
                        <ul>
                            <li><i class="bx bx-check-circle text-success"></i> Active docks deducted from next salary</li>
                            <li><i class="bx bx-check-circle text-success"></i> Applied docks won't be charged again</li>
                            <li><i class="bx bx-check-circle text-success"></i> Questions? Contact supervisor or HR</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        
        <div class="col-xl-7 col-lg-6">

            <div class="dk-card dk-table-wrap">
                <div class="tbl-hdr">
                    <h6>
                        <i class="bx bx-list-ul" style="font-size:1rem;opacity:.6"></i>
                        Dock Records
                    </h6>
                    <span class="badge-count"><?php echo e($dockRecords->total()); ?> records</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dockRecords->count() > 0): ?>
                <div class="table-responsive">
                    <table class="dk-tbl">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Applied By</th>
                                <th>Status</th>
                                <th>Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dockRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span style="font-weight:600"><?php echo e($record->dock_date->format('d')); ?></span>
                                    <span style="color:var(--bs-surface-500)"><?php echo e($record->dock_date->format('M Y')); ?></span>
                                </td>
                                <td>
                                    <span class="dk-amt">
                                        <i class="bx bx-minus"></i>
                                        Rs <?php echo e(number_format($record->amount, 0)); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="dk-reason">
                                        <div class="main"><?php echo e($record->reason); ?></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->notes): ?>
                                            <div class="note">
                                                <i class="bx bx-note"></i> <?php echo e(Str::limit($record->notes, 50)); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="dk-by">
                                        <div class="by-avatar"><?php echo e(substr($record->dockedBy->name ?? '?', 0, 1)); ?></div>
                                        <div>
                                            <div class="by-name"><?php echo e($record->dockedBy->name ?? 'Unknown'); ?></div>
                                            <div class="by-time"><?php echo e($record->created_at->format('g:i A')); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->status === 'active'): ?>
                                        <span class="st-pill st-active"><i class="bx bx-time-five"></i> Active</span>
                                    <?php elseif($record->status === 'applied'): ?>
                                        <span class="st-pill st-applied"><i class="bx bx-check"></i> Applied</span>
                                    <?php else: ?>
                                        <span class="st-pill st-cancelled"><i class="bx bx-x"></i> Cancelled</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="white-space:nowrap;font-size:.72rem">
                                    <?php echo e(\Carbon\Carbon::create($record->dock_year, $record->dock_month)->format('M Y')); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dockRecords->hasPages()): ?>
                <div class="dk-pagination">
                    <?php echo e($dockRecords->links()); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php else: ?>
                <div class="dk-empty">
                    <div class="empty-icon">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <h6>Great Job!</h6>
                    <p>You have no dock records. Keep up the excellent work!</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){

    /* ── Monthly Dock Trend (Bar Chart) ── */
    (function(){
        var labels = [];
        var amounts = [];
        var counts = [];

        <?php $__currentLoopData = $monthlyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            labels.push('<?php echo e(\Carbon\Carbon::create($m->dock_year, $m->dock_month)->format("M Y")); ?>');
            amounts.push(<?php echo e($m->total); ?>);
            counts.push(<?php echo e($m->count); ?>);
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        if (labels.length === 0) {
            document.getElementById('dockTrendChart').innerHTML =
                '<div style="text-align:center;padding:2rem 0;color:var(--bs-surface-400);font-size:.82rem">' +
                '<i class="bx bx-bar-chart-alt-2" style="font-size:1.5rem;display:block;margin-bottom:.3rem"></i>No trend data yet</div>';
            return;
        }

        new ApexCharts(document.querySelector('#dockTrendChart'), {
            chart: {
                type: 'bar',
                height: 200,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            series: [
                { name: 'Amount (Rs)', data: amounts },
                { name: 'Count', data: counts }
            ],
            xaxis: {
                categories: labels,
                labels: { style: { fontSize: '9px', colors: '#999' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: [
                {
                    title: { text: 'Amount (Rs)', style: { fontSize: '10px', fontWeight: 500 } },
                    labels: { style: { fontSize: '9px', colors: '#999' },
                        formatter: function(v){ return 'Rs ' + Math.round(v); }
                    }
                },
                {
                    opposite: true,
                    title: { text: 'Count', style: { fontSize: '10px', fontWeight: 500 } },
                    labels: { style: { fontSize: '9px', colors: '#999' } },
                    min: 0,
                    forceNiceScale: true
                }
            ],
            colors: ['#f46a6a', '#556ee6'],
            plotOptions: {
                bar: {
                    columnWidth: '55%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end'
                }
            },
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.2,
                    opacityFrom: 0.95,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            },
            grid: {
                borderColor: 'rgba(0,0,0,.06)',
                strokeDashArray: 3,
                padding: { left: 4, right: 4, top: -10 }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(v, opts){
                        return opts.seriesIndex === 0 ? 'Rs ' + v.toLocaleString() : v + ' docks';
                    }
                },
                style: { fontSize: '11px' }
            },
            legend: {
                position: 'top',
                fontSize: '10px',
                markers: { width: 8, height: 8, radius: 2 },
                itemMargin: { horizontal: 8 }
            },
            dataLabels: { enabled: false }
        }).render();
    })();

    /* ── Reason Breakdown Donut ── */
    (function(){
        var reasons = [];
        var amounts = [];

        <?php $__currentLoopData = $reasonBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            reasons.push(<?php echo json_encode(Str::limit($rb->reason, 20)); ?>);
            amounts.push(<?php echo e($rb->total); ?>);
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        if (reasons.length === 0) {
            document.getElementById('dockReasonChart').innerHTML =
                '<div style="text-align:center;padding:1.5rem 0;color:var(--bs-surface-400);font-size:.82rem">' +
                '<i class="bx bx-pie-chart-alt-2" style="font-size:1.5rem;display:block;margin-bottom:.3rem"></i>No data yet</div>';
            return;
        }

        var palette = ['#f46a6a','#f1b44c','#556ee6','#34c38f','#50a5f1','#7c69ef'];

        new ApexCharts(document.querySelector('#dockReasonChart'), {
            chart: {
                type: 'donut',
                height: 230,
                fontFamily: 'inherit'
            },
            series: amounts,
            labels: reasons,
            colors: palette.slice(0, reasons.length),
            plotOptions: {
                pie: {
                    donut: {
                        size: '62%',
                        labels: {
                            show: true,
                            name: { fontSize: '11px', fontWeight: 600 },
                            value: {
                                fontSize: '15px',
                                fontWeight: 700,
                                formatter: function(v){ return 'Rs ' + Number(v).toLocaleString(); }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '10px',
                                color: '#999',
                                formatter: function(w){
                                    var t = w.globals.seriesTotals.reduce(function(a,b){return a+b;},0);
                                    return 'Rs ' + t.toLocaleString();
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '10px',
                markers: { width: 8, height: 8, radius: 2 },
                itemMargin: { horizontal: 5, vertical: 2 }
            },
            stroke: { width: 2, colors: ['var(--bs-card-bg, #fff)'] },
            tooltip: {
                y: { formatter: function(v){ return 'Rs ' + v.toLocaleString(); } },
                style: { fontSize: '11px' }
            },
            dataLabels: { enabled: false }
        }).render();
    })();

});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/employee/dock-records.blade.php ENDPATH**/ ?>