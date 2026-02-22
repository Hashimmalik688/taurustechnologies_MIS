<?php $__env->startSection('title', 'My Attendance'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   Employee Attendance Dashboard — Polished CRM Style
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
.att-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: box-shadow .2s;
}
.att-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* ── Check-in Banner ── */
.checkin-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    padding: 0.75rem 1.1rem;
    margin-bottom: 1rem;
    border-left: 3px solid var(--bs-gold, #d4af37);
    background: linear-gradient(135deg, rgba(212,175,55,.06) 0%, rgba(212,175,55,.02) 100%);
}

.checkin-banner .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.8rem;
    letter-spacing: .2px;
}
.status-pill.present { background: rgba(52,195,143,.12); color: #1a8754; }
.status-pill.late    { background: rgba(241,180,76,.14); color: #b87a14; }
.status-pill.absent  { background: rgba(244,106,106,.12); color: #c84646; }
.status-pill.default { background: rgba(130,130,130,.08); color: var(--bs-surface-500); }

.btn-action {
    border: none;
    padding: 0.4rem 1.15rem;
    border-radius: 0.4rem;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all .18s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.btn-action:disabled { opacity: .45; cursor: not-allowed; }
.btn-action.btn-in  { background: var(--bs-gold, #d4af37); color: #fff; }
.btn-action.btn-in:hover:not(:disabled)  { background: #c49f2e; box-shadow: 0 3px 10px rgba(212,175,55,.30); }
.btn-action.btn-out { background: var(--bs-surface-500, #6c757d); color: #fff; }
.btn-action.btn-out:hover:not(:disabled) { background: var(--bs-surface-600, #565e64); box-shadow: 0 3px 10px rgba(100,100,100,.20); }

/* ── Stat Cards ── */
.stats-row { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.stat-card {
    flex: 1 1 100px;
    min-width: 95px;
    padding: 0.7rem 0.65rem;
    border-radius: 0.55rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.55rem 0.55rem 0 0;
}

/* Color variants with transparency */
.stat-card.s-total   { background: rgba(85,110,230,.06); }
.stat-card.s-total::before   { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.stat-card.s-total .stat-num { color: #556ee6; }

.stat-card.s-present { background: rgba(52,195,143,.08); }
.stat-card.s-present::before { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.stat-card.s-present .stat-num { color: #1a8754; }

.stat-card.s-late    { background: rgba(241,180,76,.08); }
.stat-card.s-late::before    { background: linear-gradient(90deg, #f1b44c, #f5cd7e); }
.stat-card.s-late .stat-num  { color: #b87a14; }

.stat-card.s-absent  { background: rgba(244,106,106,.08); }
.stat-card.s-absent::before  { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.stat-card.s-absent .stat-num { color: #c84646; }

.stat-card.s-half    { background: rgba(80,165,241,.08); }
.stat-card.s-half::before    { background: linear-gradient(90deg, #50a5f1, #8cc5f7); }
.stat-card.s-half .stat-num  { color: #2b81c9; }

.stat-card.s-leave   { background: rgba(124,105,239,.08); }
.stat-card.s-leave::before   { background: linear-gradient(90deg, #7c69ef, #a899f5); }
.stat-card.s-leave .stat-num { color: #5b49c7; }

.stat-card.s-hours   { background: rgba(85,110,230,.05); }
.stat-card.s-hours::before   { background: linear-gradient(90deg, #556ee6, #7b91ec); }
.stat-card.s-hours .stat-num { color: #556ee6; }

.stat-num  { font-size: 1.4rem; font-weight: 700; line-height: 1; }
.stat-lbl  { font-size: 0.62rem; text-transform: uppercase; font-weight: 600; letter-spacing: .4px; color: var(--bs-surface-500); margin-top: 0.25rem; }

/* ── Chart Container ── */
.chart-wrap {
    padding: 0.75rem 0.85rem 0.25rem;
    margin-bottom: 1rem;
}
.chart-wrap .chart-title {
    font-size: 0.82rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--bs-surface-600);
}
#attendanceChart { min-height: 180px; }

/* ── Calendar ── */
.att-calendar {
    padding: 0.85rem;
    margin-bottom: 0.5rem;
}
.cal-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.65rem;
}
.cal-nav h5 { margin: 0; font-size: 0.92rem; font-weight: 600; }
.cal-nav .btn { font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 0.35rem; }
.cal-nav small { font-size: 0.65rem; }

.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.35rem;
}
.cal-hdr {
    text-align: center;
    font-weight: 700;
    font-size: 0.65rem;
    padding: 0.3rem 0;
    color: var(--bs-surface-500);
    text-transform: uppercase;
    letter-spacing: .5px;
    background: var(--bs-surface-100);
    border-radius: 0.25rem;
}

.cal-day {
    aspect-ratio: 1;
    border: 1px solid var(--bs-surface-200);
    border-radius: 0.4rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: default;
    position: relative;
    transition: border-color .15s, box-shadow .15s, background .15s;
    background: var(--bs-card-bg);
    padding: 0.15rem;
}
.cal-day:hover { box-shadow: 0 2px 8px rgba(0,0,0,.07); }
.cal-day.other-month { opacity: 0.2; pointer-events: none; }

/* Status coloring — vivid borders + soft translucent bg */
.cal-day.today   { border: 2px solid var(--bs-gold, #d4af37); background: rgba(212,175,55,.06); }
.cal-day.present { border-color: #34c38f; background: rgba(52,195,143,.07); }
.cal-day.late    { border-color: #f1b44c; background: rgba(241,180,76,.07); }
.cal-day.absent  { border-color: #f46a6a; background: rgba(244,106,106,.07); }
.cal-day.half_day { border-color: #50a5f1; background: rgba(80,165,241,.07); }
.cal-day.paid_leave { border-color: #7c69ef; background: rgba(124,105,239,.07); }
.cal-day.holiday { border-color: #564ab1; background: rgba(86,74,177,.07); }

.cal-day .d-num  { font-weight: 700; font-size: 0.78rem; line-height: 1; }
.cal-day .d-stat { font-size: 0.48rem; text-transform: uppercase; font-weight: 700; letter-spacing: .3px; margin-top: 1px; }
.cal-day .d-time { font-size: 0.44rem; color: var(--bs-surface-500); line-height: 1.15; margin-top: 1px; }

/* Status text colors inside calendar */
.cal-day.present .d-stat { color: #1a8754; }
.cal-day.late .d-stat    { color: #b87a14; }
.cal-day.absent .d-stat  { color: #c84646; }
.cal-day.half_day .d-stat { color: #2b81c9; }
.cal-day.paid_leave .d-stat { color: #5b49c7; }
.cal-day.holiday .d-stat { color: #564ab1; }

/* ── Legend ── */
.cal-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    padding: 0.5rem 0.85rem;
    font-size: 0.65rem;
    color: var(--bs-surface-500);
}
.cal-legend span {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}
.cal-legend .dot {
    width: 8px; height: 8px;
    border-radius: 2px;
    display: inline-block;
}
.dot.c-present { background: #34c38f; }
.dot.c-late    { background: #f1b44c; }
.dot.c-absent  { background: #f46a6a; }
.dot.c-half    { background: #50a5f1; }
.dot.c-leave   { background: #7c69ef; }
.dot.c-holiday { background: #564ab1; }
.dot.c-today   { background: #d4af37; }

/* ── Pay period note ── */
.pp-note {
    font-size: 0.68rem;
    color: var(--bs-surface-500);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row g-3 mt-1">
        
        <div class="col-xl-5 col-lg-6">

            
            <div class="att-card checkin-banner">
                <div>
                    <div class="status-pill <?php echo e($todayAttendance ? $todayAttendance->status : 'default'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todayAttendance): ?>
                            <i class="bx bx-check-circle"></i>
                            <?php echo e(ucfirst($todayAttendance->status)); ?>

                            — <?php echo e($todayAttendance->login_time ? \Carbon\Carbon::parse($todayAttendance->login_time)->format('h:i A') : 'N/A'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todayAttendance->logout_time): ?>
                                <span style="opacity:.6">→ <?php echo e(\Carbon\Carbon::parse($todayAttendance->logout_time)->format('h:i A')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <i class="bx bx-minus-circle"></i> No record today
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todayAttendance && $todayAttendance->logout_time): ?>
                        <div style="font-size:.72rem;color:var(--bs-surface-500);margin-top:0.2rem;padding-left:.75rem">
                            <?php echo e($todayAttendance->working_hours ?? 0); ?> hrs worked
                        </div>
                    <?php elseif(!$canCheckout && $todayAttendance && $todayAttendance->login_time && !$todayAttendance->logout_time): ?>
                        <div style="font-size:.7rem;color:#c84646;margin-top:0.2rem;padding-left:.75rem">
                            <i class="bx bx-info-circle"></i> Checkout closed (6 AM cutoff)
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button id="btnCheckin" class="btn-action btn-in" <?php if($todayAttendance && $todayAttendance->login_time): ?> disabled <?php endif; ?>>
                        <i class="bx bx-log-in"></i> Check In
                    </button>
                    <button id="btnCheckout" class="btn-action btn-out" <?php if(!$canCheckout): ?> disabled <?php endif; ?>>
                        <i class="bx bx-log-out"></i> Check Out
                    </button>
                </div>
            </div>

            
            <div class="pp-note">
                <i class="bx bx-info-circle"></i> Pay period: 26th–25th cycle
            </div>

            
            <div class="stats-row">
                <div class="stat-card s-total att-card">
                    <div class="stat-num"><?php echo e($stats['total_days']); ?></div>
                    <div class="stat-lbl">Days</div>
                </div>
                <div class="stat-card s-present att-card">
                    <div class="stat-num"><?php echo e($stats['present']); ?></div>
                    <div class="stat-lbl">Present</div>
                </div>
                <div class="stat-card s-late att-card">
                    <div class="stat-num"><?php echo e($stats['late']); ?></div>
                    <div class="stat-lbl">Late</div>
                </div>
                <div class="stat-card s-absent att-card">
                    <div class="stat-num"><?php echo e($stats['absent']); ?></div>
                    <div class="stat-lbl">Absent</div>
                </div>
            </div>

            
            <div class="stats-row">
                <div class="stat-card s-half att-card">
                    <div class="stat-num"><?php echo e($stats['half_day'] ?? 0); ?></div>
                    <div class="stat-lbl">Half Day</div>
                </div>
                <div class="stat-card s-leave att-card">
                    <div class="stat-num"><?php echo e($stats['paid_leave'] ?? 0); ?></div>
                    <div class="stat-lbl">Paid Leave</div>
                </div>
                <div class="stat-card s-hours att-card">
                    <div class="stat-num"><?php echo e($stats['total_hours']); ?></div>
                    <div class="stat-lbl">Total Hrs</div>
                </div>
                <div class="stat-card s-hours att-card">
                    <div class="stat-num"><?php echo e($stats['avg_hours']); ?></div>
                    <div class="stat-lbl">Avg Hrs/Day</div>
                </div>
            </div>

            
            <div class="att-card chart-wrap">
                <div id="attendanceChart"></div>
            </div>

            
            <div class="att-card chart-wrap">
                <div id="breakdownChart"></div>
            </div>

        </div>

        
        <div class="col-xl-7 col-lg-6">

            <div class="att-card att-calendar">
                <div class="cal-nav">
                    <button class="btn btn-outline-secondary"
                        onclick="window.location.href='?month=<?php echo e(\Carbon\Carbon::parse($currentMonth)->subMonth()->format('Y-m')); ?>'">
                        <i class="bx bx-chevron-left"></i> Prev
                    </button>
                    <div class="text-center">
                        <h5><?php echo e(\Carbon\Carbon::parse($currentMonth)->format('F Y')); ?></h5>
                        <small class="text-muted"><?php echo e($payPeriodLabel); ?></small>
                    </div>
                    <button class="btn btn-outline-secondary"
                        onclick="window.location.href='?month=<?php echo e(\Carbon\Carbon::parse($currentMonth)->addMonth()->format('Y-m')); ?>'">
                        Next <i class="bx bx-chevron-right"></i>
                    </button>
                </div>

                <div class="cal-grid">
                    
                    <div class="cal-hdr">Mon</div>
                    <div class="cal-hdr">Tue</div>
                    <div class="cal-hdr">Wed</div>
                    <div class="cal-hdr">Thu</div>
                    <div class="cal-hdr">Fri</div>
                    <div class="cal-hdr">Sat</div>
                    <div class="cal-hdr">Sun</div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $calendar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $week; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $cls = ['cal-day'];
                                if (!$day['isCurrentMonth']) $cls[] = 'other-month';
                                if ($day['isToday']) $cls[] = 'today';
                                if ($day['holiday']) {
                                    $cls[] = 'holiday';
                                } elseif ($day['attendance']) {
                                    $cls[] = $day['attendance']->status;
                                }
                                $tip = $day['holiday']
                                    ? 'Holiday: ' . $day['holiday']->name
                                    : ($day['attendance'] ? ucfirst(str_replace('_', ' ', $day['attendance']->status)) : '');
                            ?>
                            <div class="<?php echo e(implode(' ', $cls)); ?>" title="<?php echo e($tip); ?>">
                                <div class="d-num"><?php echo e($day['date']->format('j')); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['holiday']): ?>
                                    <div class="d-stat" style="color:#564ab1">
                                        <i class="bx bx-calendar-star" style="font-size:.45rem"></i> HOLIDAY
                                    </div>
                                    <div class="d-time"><?php echo e(Str::limit($day['holiday']->name, 15)); ?></div>
                                <?php elseif($day['attendance']): ?>
                                    <div class="d-stat">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $day['attendance']->status))); ?>

                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['attendance']->login_time || $day['attendance']->logout_time): ?>
                                        <div class="d-time">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['attendance']->login_time): ?>
                                                <?php echo e(\Carbon\Carbon::parse($day['attendance']->login_time)->format('g:i A')); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['attendance']->logout_time): ?>
                                                <br><?php echo e(\Carbon\Carbon::parse($day['attendance']->logout_time)->format('g:i A')); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="att-card cal-legend">
                <span><i class="dot c-present"></i> Present</span>
                <span><i class="dot c-late"></i> Late</span>
                <span><i class="dot c-absent"></i> Absent</span>
                <span><i class="dot c-half"></i> Half Day</span>
                <span><i class="dot c-leave"></i> Paid Leave</span>
                <span><i class="dot c-holiday"></i> Holiday</span>
                <span><i class="dot c-today"></i> Today</span>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){

    /* ── Attendance Trend Chart ── */
    (function(){
        var labels = [];
        var hours  = [];
        var colors = [];
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $calendar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $week; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($day['isCurrentMonth'] && $day['attendance'] && $day['attendance']->working_hours): ?>
                    labels.push('<?php echo e($day["date"]->format("M j")); ?>');
                    hours.push(<?php echo e(round($day['attendance']->working_hours, 1)); ?>);
                    <?php if($day['attendance']->status === 'late'): ?>
                        colors.push('#f1b44c');
                    <?php elseif($day['attendance']->status === 'absent'): ?>
                        colors.push('#f46a6a');
                    <?php else: ?>
                        colors.push('#34c38f');
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        if (labels.length === 0) {
            document.getElementById('attendanceChart').innerHTML =
                '<div style="text-align:center;padding:2rem 0;color:var(--bs-surface-400);font-size:.82rem"><i class="bx bx-bar-chart-alt-2" style="font-size:1.5rem;display:block;margin-bottom:.3rem"></i>No data for chart yet</div>';
            return;
        }

        var options = {
            chart: {
                type: 'area',
                height: 185,
                toolbar: { show: false },
                fontFamily: 'inherit',
                zoom: { enabled: false }
            },
            series: [{ name: 'Hours', data: hours }],
            xaxis: {
                categories: labels,
                labels: { style: { fontSize: '9px', colors: '#999' }, rotate: -45, rotateAlways: labels.length > 10 },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                max: Math.max(12, Math.max.apply(null, hours) + 1),
                labels: { style: { fontSize: '9px', colors: '#999' } },
                title: { text: 'Hours', style: { fontSize: '10px', fontWeight: 500 } }
            },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            colors: ['#34c38f'],
            markers: {
                size: 3,
                colors: colors,
                strokeWidth: 0,
                hover: { size: 5 }
            },
            grid: {
                borderColor: 'rgba(0,0,0,.06)',
                strokeDashArray: 3,
                padding: { left: 4, right: 4, top: -10 }
            },
            tooltip: {
                y: { formatter: function(v){ return v + ' hrs'; } },
                marker: { show: true },
                style: { fontSize: '11px' }
            },
            dataLabels: { enabled: false }
        };

        new ApexCharts(document.querySelector('#attendanceChart'), options).render();
    })();

    /* ── Attendance Breakdown Donut ── */
    (function(){
        var present = <?php echo e($stats['present']); ?>;
        var late    = <?php echo e($stats['late']); ?>;
        var absent  = <?php echo e($stats['absent']); ?>;
        var halfDay = <?php echo e($stats['half_day'] ?? 0); ?>;
        var paidLeave = <?php echo e($stats['paid_leave'] ?? 0); ?>;
        var total = present + late + absent + halfDay + paidLeave;

        if (total === 0) {
            document.getElementById('breakdownChart').innerHTML =
                '<div style="text-align:center;padding:1.5rem 0;color:var(--bs-surface-400);font-size:.82rem"><i class="bx bx-pie-chart-alt-2" style="font-size:1.5rem;display:block;margin-bottom:.3rem"></i>No breakdown data</div>';
            return;
        }

        var donutOpts = {
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'inherit'
            },
            series: [present, late, absent, halfDay, paidLeave],
            labels: ['Present', 'Late', 'Absent', 'Half Day', 'Paid Leave'],
            colors: ['#34c38f', '#f1b44c', '#f46a6a', '#50a5f1', '#7c69ef'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '62%',
                        labels: {
                            show: true,
                            name: { fontSize: '12px', fontWeight: 600 },
                            value: { fontSize: '16px', fontWeight: 700 },
                            total: {
                                show: true,
                                label: 'Total Days',
                                fontSize: '11px',
                                color: '#999',
                                formatter: function(w){ return w.globals.seriesTotals.reduce(function(a,b){return a+b;},0); }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '11px',
                markers: { width: 8, height: 8, radius: 2 },
                itemMargin: { horizontal: 6, vertical: 2 }
            },
            stroke: { width: 2, colors: ['var(--bs-card-bg, #fff)'] },
            tooltip: { style: { fontSize: '11px' } },
            dataLabels: { enabled: false }
        };

        new ApexCharts(document.querySelector('#breakdownChart'), donutOpts).render();
    })();

    /* ── Check-in / Check-out ── */
    var checkin  = document.getElementById('btnCheckin');
    var checkout = document.getElementById('btnCheckout');
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!tokenMeta) return;
    var token = tokenMeta.getAttribute('content');

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
            body: JSON.stringify(body || {})
        }).then(function(r){ return r.json(); });
    }

    if (checkin) {
        checkin.addEventListener('click', function(e){
            e.preventDefault();
            if (checkin.disabled) return;
            checkin.disabled = true;
            checkin.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Checking in…';
            post('<?php echo e(url("/attendance/check-in")); ?>', { force_office: 0 })
                .then(function(data){
                    if (data.success) { setTimeout(function(){ location.reload(); }, 500); }
                    else { alert(data.message || 'Could not check in'); checkin.disabled = false; checkin.innerHTML = '<i class="bx bx-log-in"></i> Check In'; }
                }).catch(function(err){
                    console.error(err); alert('Network error');
                    checkin.disabled = false; checkin.innerHTML = '<i class="bx bx-log-in"></i> Check In';
                });
        });
    }

    if (checkout) {
        checkout.addEventListener('click', function(e){
            e.preventDefault();
            if (checkout.disabled) return;
            checkout.disabled = true;
            checkout.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Checking out…';
            post('<?php echo e(url("/attendance/check-out")); ?>')
                .then(function(data){
                    if (data.success) { setTimeout(function(){ location.reload(); }, 500); }
                    else { alert(data.message || 'Could not check out'); checkout.disabled = false; checkout.innerHTML = '<i class="bx bx-log-out"></i> Check Out'; }
                }).catch(function(err){
                    console.error(err); alert('Network error');
                    checkout.disabled = false; checkout.innerHTML = '<i class="bx bx-log-out"></i> Check Out';
                });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/attendance/dashboard.blade.php ENDPATH**/ ?>