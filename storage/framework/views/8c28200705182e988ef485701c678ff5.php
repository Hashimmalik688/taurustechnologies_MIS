<?php $__env->startSection('title', 'Agent Performance — Zoom Calls'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* ── Page header ── */
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem}
.page-hdr h5{margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
.page-hdr h5 i{color:var(--bs-gold,#d4af37)}
.page-hdr .ph-sub{font-size:.72rem;color:var(--bs-surface-500);margin-left:.15rem}

/* ── Live indicator ── */
.live-badge{display:inline-flex;align-items:center;gap:.35rem;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;padding:.2rem .55rem;border-radius:20px;background:rgba(34,197,94,.12);color:#16a34a;border:1px solid rgba(34,197,94,.25)}
.live-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;animation:livePulse 1.4s ease-in-out infinite}
.live-badge.paused{background:rgba(100,116,139,.1);color:#64748b;border-color:rgba(100,116,139,.2)}
.live-badge.paused .live-dot{background:#94a3b8;animation:none}
@keyframes livePulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.75)}}

/* ── Last updated row ── */
.lu-row{display:flex;align-items:center;gap:1rem;padding:.45rem .75rem;font-size:.65rem;color:var(--bs-surface-500);border-bottom:1px solid rgba(0,0,0,.04);flex-wrap:wrap}
.lu-row .lu-time{font-weight:600;color:var(--bs-surface-700)}
.lu-countdown{display:inline-flex;align-items:center;gap:.25rem}
.lu-bar-wrap{width:80px;height:3px;background:rgba(0,0,0,.08);border-radius:3px;overflow:hidden;display:inline-block;vertical-align:middle;margin-left:.3rem}
.lu-bar{height:100%;background:var(--bs-gold,#d4af37);border-radius:3px;transition:width .95s linear}
.lu-manual{cursor:pointer;color:var(--bs-gold,#d4af37);text-decoration:underline;text-underline-offset:2px}

/* ── Flash animation on cell update ── */
@keyframes flashUp{0%{background:rgba(34,197,94,.18)}100%{background:transparent}}
@keyframes flashDown{0%{background:rgba(239,68,68,.12)}100%{background:transparent}}
.flash-up{animation:flashUp .9s ease-out}
.flash-down{animation:flashDown .9s ease-out}
.flash-new{animation:flashUp 1.2s ease-out}

/* ── Tab Pills ── */
.tab-row{display:flex;gap:.35rem;margin-bottom:.65rem;flex-wrap:wrap}
.tab-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:20px;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid var(--bs-surface-200,#e2e8f0);color:var(--bs-surface-500,#64748b);background:transparent;transition:all .15s}
.tab-pill:hover{border-color:rgba(212,175,55,.3);color:#b89730}
.tab-pill.active{background:linear-gradient(135deg,#d4af37,#c9a227);color:#fff;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25)}
.tab-pill i{font-size:.85rem}

/* ── Summary Cards ── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.65rem;margin-bottom:.65rem}
.stat-card{background:#fff;padding:.75rem 1rem;border-radius:.55rem;border:1px solid rgba(0,0,0,.06);box-shadow:0 1px 3px rgba(0,0,0,.03);transition:box-shadow .2s}
.stat-card-label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-500);margin-bottom:.25rem}
.stat-card-value{font-size:1.5rem;font-weight:700;color:var(--bs-surface-900);font-variant-numeric:tabular-nums;transition:color .3s}
.stat-card-icon{float:right;font-size:1.8rem;opacity:.15;margin-top:-.2rem}

/* ── Agent Table ── */
.ap-table{width:100%;border-collapse:separate;border-spacing:0;font-size:.73rem}
.ap-table thead th{
    padding:.55rem .65rem;font-size:.65rem;font-weight:700;text-transform:uppercase;
    letter-spacing:.5px;color:var(--bs-surface-500,#64748b);
    background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
    white-space:nowrap;position:sticky;top:0;z-index:2;
}
.ap-table thead th[data-sort]{cursor:pointer;user-select:none}
.ap-table thead th[data-sort]:hover{color:var(--bs-gold,#d4af37)}
.ap-table thead th .si{font-size:.55rem;opacity:.4;margin-left:.15rem}
.ap-table thead th.s-asc .si::after{content:'▲';opacity:1}
.ap-table thead th.s-desc .si::after{content:'▼';opacity:1}
.ap-table thead th:not(.s-asc):not(.s-desc) .si::after{content:'⇅'}
.ap-table tbody td{padding:.5rem .65rem;border-bottom:1px solid rgba(0,0,0,.035);color:var(--bs-surface-900,#1e293b);vertical-align:middle;transition:background .15s}
.ap-table tbody tr:hover td{background:rgba(212,175,55,.04)}
.ap-table tbody tr:last-child td{border-bottom:none}
.ap-num{text-align:right;font-variant-numeric:tabular-nums}
.ap-table tfoot td{padding:.6rem .65rem;font-size:.73rem;border-top:2px solid rgba(0,0,0,.09);background:rgba(212,175,55,.04);font-weight:700}

/* ── Rate bar ── */
.rate-bar{display:inline-block;width:44px;height:5px;background:rgba(0,0,0,.08);border-radius:3px;margin-left:5px;vertical-align:middle}
.rate-fill{height:100%;border-radius:3px;background:var(--bs-gold,#d4af37);display:block;transition:width .6s ease}

/* ── Date filter ── */
.df-row{display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap;padding:.7rem;border-bottom:1px solid rgba(0,0,0,.06)}
.df-row label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);display:block;margin-bottom:.2rem}
.df-row input[type=date]{font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff}

/* ── MOS badge ── */
.mos-badge{display:inline-flex;align-items:center;justify-content:center;min-width:36px;padding:.15rem .4rem;border-radius:4px;font-size:.68rem;font-weight:700;font-variant-numeric:tabular-nums}
.mos-good{background:rgba(26,135,84,.12);color:#1a8754}
.mos-fair{background:rgba(212,175,55,.12);color:#b89730}
.mos-poor{background:rgba(200,70,70,.12);color:#c84646}
.mos-none{color:var(--bs-surface-400);font-size:.6rem}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .mos-good{background:rgba(26,135,84,.2)}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .mos-fair{background:rgba(212,175,55,.2)}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .mos-poor{background:rgba(200,70,70,.2)}

/* ── Empty state ── */
.ap-empty{text-align:center;padding:3rem 1rem;color:var(--bs-surface-500)}
.ap-empty i{font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25}
.ap-empty h6{font-size:.85rem;font-weight:700;margin-bottom:.25rem}
.ap-empty p{font-size:.72rem}

/* ── Spinner (refresh loading) ── */
.ap-spinner{display:none;width:12px;height:12px;border:2px solid rgba(212,175,55,.3);border-top-color:#d4af37;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Dark themes ── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card{
    background:rgba(15,23,42,.6);border-color:rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card-label,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table thead th{
    color:#94a3b8;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card-value,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table tbody td{
    color:#e2e8f0;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table thead th{
    background:rgba(15,23,42,.6);border-bottom-color:rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table tbody td{
    border-bottom-color:rgba(255,255,255,.04);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table tfoot td{
    border-top-color:rgba(255,255,255,.1);color:#e2e8f0;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .df-row input[type=date]{
    background:rgba(15,23,42,.6);color:#e2e8f0;border-color:rgba(255,255,255,.1);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .lu-row{
    color:#94a3b8;border-bottom-color:rgba(255,255,255,.04);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .lu-row .lu-time{color:#e2e8f0}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="page-hdr">
    <h5>
        <i class="bx bx-video"></i> Zoom Call Logs
        <span class="ph-sub">Agent Performance</span>
    </h5>
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
        
        <span class="live-badge" id="liveBadge" title="Click to pause auto-refresh" style="cursor:pointer" onclick="toggleLive()">
            <span class="live-dot"></span>
            <span id="liveBadgeText">LIVE</span>
        </span>
        <a href="<?php echo e(route('settings.reports.zoom-diagnostics')); ?>" class="act-btn a-warn" style="font-size:.72rem;padding:.3rem .65rem" title="Diagnostics">
            <i class="bx bx-pulse"></i> Diagnostics
        </a>
        <a href="<?php echo e(route('settings.reports.hub')); ?>" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>
</div>


<div class="tab-row">
    <a href="<?php echo e(route('settings.reports.zoom-logs')); ?>" class="tab-pill">
        <i class="bx bx-list-ul"></i> Call Logs
    </a>
    <a href="<?php echo e(route('settings.reports.zoom-agent-performance')); ?>" class="tab-pill active">
        <i class="bx bx-bar-chart-alt-2"></i> Agent Performance
    </a>
</div>


<?php
    $totalSecs = $summaryTotalDuration;
    $sH = floor($totalSecs / 3600);
    $sM = floor(($totalSecs % 3600) / 60);
    $sS = $totalSecs % 60;
    $totalDurFmt = sprintf('%02d:%02d:%02d', $sH, $sM, $sS);
    $connectRate = $summaryTotalCalls > 0 ? round(($summaryAnswered / $summaryTotalCalls) * 100, 1) : 0;
?>
<div class="stats-grid">
    <div class="stat-card">
        <i class="bx bx-phone-outgoing stat-card-icon"></i>
        <div class="stat-card-label">Outbound Calls</div>
        <div class="stat-card-value" id="sc-total"><?php echo e(number_format($summaryTotalCalls)); ?></div>
    </div>
    <div class="stat-card">
        <i class="bx bx-time stat-card-icon"></i>
        <div class="stat-card-label">Total Talk Time</div>
        <div class="stat-card-value" id="sc-duration" style="font-size:1.3rem"><?php echo e($totalDurFmt); ?></div>
    </div>
    <div class="stat-card">
        <i class="bx bx-phone-call stat-card-icon"></i>
        <div class="stat-card-label">Connected</div>
        <div class="stat-card-value" id="sc-answered"><?php echo e(number_format($summaryAnswered)); ?></div>
    </div>
    <div class="stat-card">
        <i class="bx bx-user-check stat-card-icon"></i>
        <div class="stat-card-label">Connect Rate</div>
        <div class="stat-card-value" id="sc-rate"><?php echo e($connectRate); ?>%</div>
    </div>
    <div class="stat-card">
        <i class="bx bx-group stat-card-icon"></i>
        <div class="stat-card-label">Agents Tracked</div>
        <div class="stat-card-value" id="sc-agents"><?php echo e($agentKpis->count()); ?></div>
    </div>
</div>


<div class="ex-card sec-card">

    
    <form method="GET" action="<?php echo e(route('settings.reports.zoom-agent-performance')); ?>" id="apFilterForm">
        <div class="df-row">
            <div>
                <label>From (PT)</label>
                <input type="date" name="date_from" id="apDateFrom" value="<?php echo e($dateFrom ?? ''); ?>">
            </div>
            <div>
                <label>To (PT)</label>
                <input type="date" name="date_to" id="apDateTo" value="<?php echo e($dateTo ?? ''); ?>">
            </div>
            <button type="button" class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;border:none;cursor:pointer" onclick="setToday()">
                <i class="bx bx-calendar-check" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Today (PT)
            </button>
            <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                <i class="bx bx-filter-alt" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Apply
            </button>
            <a href="<?php echo e(route('settings.reports.zoom-agent-performance')); ?>" class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;text-decoration:none">
                <i class="bx bx-x" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Clear
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateFrom || $dateTo): ?>
            <span style="font-size:.65rem;color:var(--bs-surface-400);align-self:center">
                Showing <?php echo e(($dateFrom && $dateTo && $dateFrom !== $dateTo) ? $dateFrom . ' → ' . $dateTo : ($dateFrom ?: $dateTo)); ?> (PT)
            </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </form>

    
    <div class="lu-row" id="luRow">
        <span class="ap-spinner" id="apSpinner"></span>
        <span>Updated <span class="lu-time" id="luTime">just now</span></span>
        <span class="lu-countdown">
            Next refresh in <strong id="luCountdown">30</strong>s
            <span class="lu-bar-wrap"><span class="lu-bar" id="luBar" style="width:100%"></span></span>
        </span>
        <span class="lu-manual" onclick="doRefresh()"><i class="bx bx-refresh" style="font-size:.75rem;vertical-align:middle"></i> Refresh now</span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agentKpis->count() > 0): ?>

    
    <div style="overflow-x:auto">
        <table class="ap-table" id="apTable">
            <thead>
                <tr>
                    <th data-sort="name">Agent <span class="si"></span></th>
                    <th class="ap-num" data-sort="total_calls">Total <span class="si"></span></th>
                    <th class="ap-num" data-sort="answered" title="Calls where the lead actually picked up (talk time ≥10 s)">Connected <span class="si"></span></th>
                    <th class="ap-num" data-sort="missed" title="No answer + voicemail system drops (talk time &lt;10 s)">No Pickup <span class="si"></span></th>
                    <th class="ap-num" data-sort="declined">Declined <span class="si"></span></th>
                    <th class="ap-num" data-sort="voicemail">Voicemail <span class="si"></span></th>
                    <th class="ap-num" data-sort="recorded">Recorded <span class="si"></span></th>
                    <th class="ap-num" data-sort="total_duration">Talk Time <span class="si"></span></th>
                    <th class="ap-num" data-sort="avg_duration">Avg Duration <span class="si"></span></th>
                    <th class="ap-num" data-sort="connect_rate">Connect Rate <span class="si"></span></th>
                    <th class="ap-num" data-sort="avg_mos" title="Mean Opinion Score — voice call quality (1.0–5.0)">Avg MOS <span class="si"></span></th>
                </tr>
            </thead>
            <tbody id="apBody">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $agentKpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $cr      = $agent['total_calls'] > 0 ? round(($agent['answered'] / $agent['total_calls']) * 100, 1) : 0;
                    $avgSec  = $agent['total_calls'] > 0 ? round($agent['total_duration'] / $agent['total_calls']) : 0;
                    $tH = floor($agent['total_duration'] / 3600);
                    $tM = floor(($agent['total_duration'] % 3600) / 60);
                    $tS = $agent['total_duration'] % 60;
                    $talkFmt = $tH > 0 ? sprintf('%d:%02d:%02d', $tH, $tM, $tS) : sprintf('%02d:%02d', $tM, $tS);
                    $aM = floor($avgSec / 60); $aS = $avgSec % 60;
                    $avgFmt  = sprintf('%02d:%02d', $aM, $aS);
                ?>
                <tr
                    data-key="<?php echo e($agent['extension'] ?: $agent['name']); ?>"
                    data-name="<?php echo e($agent['name']); ?>"
                    data-total_calls="<?php echo e($agent['total_calls']); ?>"
                    data-answered="<?php echo e($agent['answered']); ?>"
                    data-missed="<?php echo e($agent['missed']); ?>"
                    data-declined="<?php echo e($agent['declined']); ?>"
                    data-voicemail="<?php echo e($agent['voicemail']); ?>"
                    data-recorded="<?php echo e($agent['recorded']); ?>"
                    data-total_duration="<?php echo e($agent['total_duration']); ?>"
                    data-avg_duration="<?php echo e($avgSec); ?>"
                    data-connect_rate="<?php echo e($cr); ?>"
                    data-avg_mos="<?php echo e($agent['avg_mos'] ?? ''); ?>"
                >
                    <td>
                        <div style="font-weight:600"><?php echo e($agent['name']); ?></div>
                        <div style="font-size:.6rem;color:var(--bs-surface-500)">Ext. <?php echo e($agent['extension']); ?></div>
                    </td>
                    <td class="ap-num"><strong><?php echo e(number_format($agent['total_calls'])); ?></strong></td>
                    <td class="ap-num"><span style="color:#1a8754;font-weight:600"><?php echo e(number_format($agent['answered'])); ?></span></td>
                    <td class="ap-num"><span style="color:#b87a14"><?php echo e(number_format($agent['missed'])); ?></span></td>
                    <td class="ap-num"><span style="color:#c84646"><?php echo e(number_format($agent['declined'])); ?></span></td>
                    <td class="ap-num"><span style="color:#6c757d"><?php echo e(number_format($agent['voicemail'])); ?></span></td>
                    <td class="ap-num"><span style="color:var(--bs-gold,#d4af37)"><?php echo e(number_format($agent['recorded'])); ?></span></td>
                    <td class="ap-num" style="white-space:nowrap;font-family:monospace;font-size:.7rem"><?php echo e($talkFmt); ?></td>
                    <td class="ap-num" style="white-space:nowrap;font-family:monospace;font-size:.7rem"><?php echo e($avgFmt); ?></td>
                    <td class="ap-num" style="white-space:nowrap">
                        <strong><?php echo e($cr); ?>%</strong>
                        <span class="rate-bar"><span class="rate-fill" style="width:<?php echo e(min($cr, 100)); ?>%"></span></span>
                    </td>
                    <td class="ap-num">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agent['avg_mos'] !== null): ?>
                            <?php
                                $mosClass = $agent['avg_mos'] >= 4.0 ? 'mos-good' : ($agent['avg_mos'] >= 3.6 ? 'mos-fair' : 'mos-poor');
                            ?>
                            <span class="mos-badge <?php echo e($mosClass); ?>"><?php echo e($agent['avg_mos']); ?></span>
                        <?php else: ?>
                            <span class="mos-none">&ndash;</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <?php if($agentKpis->count() > 1): ?>
            <?php
                $tot = [
                    'total_calls'    => $agentKpis->sum('total_calls'),
                    'answered'       => $agentKpis->sum('answered'),
                    'missed'         => $agentKpis->sum('missed'),
                    'declined'       => $agentKpis->sum('declined'),
                    'voicemail'      => $agentKpis->sum('voicemail'),
                    'recorded'       => $agentKpis->sum('recorded'),
                    'total_duration' => $agentKpis->sum('total_duration'),
                ];
                // Weighted avg MOS across all agents that have MOS data
                $mosAgents = $agentKpis->filter(fn($a) => $a['avg_mos'] !== null);
                $totAvgMos = $mosAgents->count() > 0
                    ? round($mosAgents->avg('avg_mos'), 1)
                    : null;
                $totCR  = $tot['total_calls'] > 0 ? round(($tot['answered'] / $tot['total_calls']) * 100, 1) : 0;
                $totAvg = $tot['total_calls'] > 0 ? round($tot['total_duration'] / $tot['total_calls']) : 0;
                $fH = floor($tot['total_duration'] / 3600);
                $fM = floor(($tot['total_duration'] % 3600) / 60);
                $fS = $tot['total_duration'] % 60;
                $totTalk = $fH > 0 ? sprintf('%d:%02d:%02d', $fH, $fM, $fS) : sprintf('%02d:%02d', $fM, $fS);
                $taM = floor($totAvg / 60); $taS = $totAvg % 60;
                $totAvgFmt = sprintf('%02d:%02d', $taM, $taS);
            ?>
            <tfoot id="apFoot">
                <tr>
                    <td>
                        TOTAL
                        <span style="font-size:.6rem;font-weight:400;color:var(--bs-surface-500)" id="agentCount">(<?php echo e($agentKpis->count()); ?> agents)</span>
                    </td>
                    <td class="ap-num"><?php echo e(number_format($tot['total_calls'])); ?></td>
                    <td class="ap-num"><span style="color:#1a8754"><?php echo e(number_format($tot['answered'])); ?></span></td>
                    <td class="ap-num"><span style="color:#b87a14"><?php echo e(number_format($tot['missed'])); ?></span></td>
                    <td class="ap-num"><span style="color:#c84646"><?php echo e(number_format($tot['declined'])); ?></span></td>
                    <td class="ap-num"><span style="color:#6c757d"><?php echo e(number_format($tot['voicemail'])); ?></span></td>
                    <td class="ap-num"><span style="color:var(--bs-gold,#d4af37)"><?php echo e(number_format($tot['recorded'])); ?></span></td>
                    <td class="ap-num" style="font-family:monospace;font-size:.7rem"><?php echo e($totTalk); ?></td>
                    <td class="ap-num" style="font-family:monospace;font-size:.7rem"><?php echo e($totAvgFmt); ?></td>
                    <td class="ap-num"><?php echo e($totCR); ?>%</td>
                    <td class="ap-num">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totAvgMos !== null): ?>
                            <?php $mosClass = $totAvgMos >= 4.0 ? 'mos-good' : ($totAvgMos >= 3.6 ? 'mos-fair' : 'mos-poor'); ?>
                            <span class="mos-badge <?php echo e($mosClass); ?>"><?php echo e($totAvgMos); ?></span>
                        <?php else: ?>
                            <span class="mos-none">&ndash;</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            </tfoot>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>
    </div>

    <?php else: ?>
    <div class="ap-empty" id="apEmpty">
        <i class="bx bx-user-x"></i>
        <h6>No Agent Data</h6>
        <p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateFrom || $dateTo): ?>
                No outbound calls found for the selected date range. Try adjusting the filter or click Clear.
            <?php else: ?>
                No outbound call data found. Zoom Phone webhook calls will appear here once captured.
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Helpers ──────────────────────────────────────────────────────────────────
function fmtDuration(secs) {
    const h = Math.floor(secs / 3600), m = Math.floor((secs % 3600) / 60), s = secs % 60;
    if (h > 0) return `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}
function fmtNum(n) { return n.toLocaleString(); }

function setToday() {
    const ptDate = new Intl.DateTimeFormat('en-CA', { timeZone: 'America/Los_Angeles' }).format(new Date());
    document.getElementById('apDateFrom').value = ptDate;
    document.getElementById('apDateTo').value   = ptDate;
    document.getElementById('apFilterForm').submit();
}

// ── Sort ─────────────────────────────────────────────────────────────────────
let sortCol = 'total_calls', sortDir = 'desc';
function renderIcons() {
    document.querySelectorAll('#apTable thead th[data-sort]').forEach(th => {
        th.classList.remove('s-asc','s-desc');
        if (th.dataset.sort === sortCol) th.classList.add(sortDir === 'asc' ? 's-asc' : 's-desc');
    });
}
function doSort(col) {
    sortDir = (sortCol === col && sortDir === 'desc') ? 'asc' : 'desc';
    sortCol = col;
    const tbody = document.getElementById('apBody');
    if (!tbody) return;
    Array.from(tbody.querySelectorAll('tr'))
        .sort((a, b) => {
            const va = a.dataset[col] ?? '', vb = b.dataset[col] ?? '';
            const na = parseFloat(va), nb = parseFloat(vb);
            const cmp = isNaN(na) ? va.localeCompare(vb) : na - nb;
            return sortDir === 'asc' ? cmp : -cmp;
        })
        .forEach(r => tbody.appendChild(r));
    renderIcons();
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#apTable thead th[data-sort]').forEach(th => {
        th.addEventListener('click', () => doSort(th.dataset.sort));
    });
    renderIcons();
});

// ── Live polling ──────────────────────────────────────────────────────────────
const REFRESH_SEC = 30;
const DATA_URL    = '<?php echo e(route('settings.reports.zoom-agent-performance.data')); ?>' +
                    window.location.search;

let liveActive  = true;
let countdownVal = REFRESH_SEC;
let countdownInterval = null;
let refreshTimeout    = null;

function toggleLive() {
    liveActive = !liveActive;
    const badge = document.getElementById('liveBadge');
    const text  = document.getElementById('liveBadgeText');
    if (liveActive) {
        badge.classList.remove('paused');
        text.textContent = 'LIVE';
        badge.title = 'Click to pause auto-refresh';
        scheduleNext();
    } else {
        badge.classList.add('paused');
        text.textContent = 'PAUSED';
        badge.title = 'Click to resume auto-refresh';
        clearTimeout(refreshTimeout);
        clearInterval(countdownInterval);
        document.getElementById('luBar').style.width = '0%';
        document.getElementById('luCountdown').textContent = '—';
    }
}

function startCountdown() {
    clearInterval(countdownInterval);
    countdownVal = REFRESH_SEC;
    const bar = document.getElementById('luBar');
    const cd  = document.getElementById('luCountdown');
    bar.style.transition = 'none';
    bar.style.width = '100%';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            bar.style.transition = `width ${REFRESH_SEC}s linear`;
            bar.style.width = '0%';
        });
    });
    countdownInterval = setInterval(() => {
        countdownVal--;
        cd.textContent = countdownVal > 0 ? countdownVal : '…';
        if (countdownVal <= 0) clearInterval(countdownInterval);
    }, 1000);
}

function scheduleNext() {
    clearTimeout(refreshTimeout);
    startCountdown();
    refreshTimeout = setTimeout(() => { if (liveActive) doRefresh(); }, REFRESH_SEC * 1000);
}

function doRefresh() {
    const spinner = document.getElementById('apSpinner');
    spinner.style.display = 'inline-block';

    fetch(DATA_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            applyUpdate(data);
            updateLastUpdated(data.generated_at);
            spinner.style.display = 'none';
            if (liveActive) scheduleNext();
        })
        .catch(() => {
            spinner.style.display = 'none';
            if (liveActive) scheduleNext();
        });
}

function updateLastUpdated(iso) {
    const d = new Date(iso);
    const fmt = new Intl.DateTimeFormat('en-US', {
        hour: 'numeric', minute: '2-digit', second: '2-digit',
        hour12: true, timeZone: 'America/Los_Angeles'
    });
    document.getElementById('luTime').textContent = fmt.format(d) + ' PT';
}

function flash(el, cls) {
    el.classList.remove('flash-up','flash-down','flash-new');
    void el.offsetWidth;
    el.classList.add(cls);
    el.addEventListener('animationend', () => el.classList.remove(cls), { once: true });
}

function applyUpdate(data) {
    // ── Summary cards ────────────────────────────────────────────
    const sTotal    = document.getElementById('sc-total');
    const sAnswered = document.getElementById('sc-answered');
    const sDuration = document.getElementById('sc-duration');
    const sRate     = document.getElementById('sc-rate');
    const sAgents   = document.getElementById('sc-agents');

    const newTotal = data.total_calls;
    const oldTotal = parseInt(sTotal.textContent.replace(/,/g,'')) || 0;
    if (newTotal !== oldTotal) { sTotal.textContent = fmtNum(newTotal); flash(sTotal, newTotal > oldTotal ? 'flash-up' : 'flash-down'); }

    const newAns = data.answered;
    const oldAns = parseInt(sAnswered.textContent.replace(/,/g,'')) || 0;
    if (newAns !== oldAns) { sAnswered.textContent = fmtNum(newAns); flash(sAnswered, newAns > oldAns ? 'flash-up' : 'flash-down'); }

    sDuration.textContent = fmtDuration(data.total_duration);

    const newRate = newTotal > 0 ? Math.round((newAns / newTotal) * 1000) / 10 : 0;
    sRate.textContent = newRate + '%';

    const newAgentCount = data.agents.length;
    sAgents.textContent = fmtNum(newAgentCount);

    // ── Agent table rows ─────────────────────────────────────────
    const tbody = document.getElementById('apBody');
    if (!tbody) return;

    const existingRows = {};
    tbody.querySelectorAll('tr[data-key]').forEach(r => { existingRows[r.dataset.key] = r; });

    const seenKeys = new Set();

    data.agents.forEach(agent => {
        const avgSec = agent.total_calls > 0 ? Math.round(agent.total_duration / agent.total_calls) : 0;
        const cr     = agent.total_calls > 0 ? Math.round((agent.answered / agent.total_calls) * 1000) / 10 : 0;
        const key    = agent.extension || agent.name;
        seenKeys.add(key);

        if (existingRows[key]) {
            // Update existing row — flash changed cells
            const row = existingRows[key];
            const cells = row.querySelectorAll('td');

            function setCellNum(idx, newVal, color) {
                const old = parseInt(row.dataset[Object.keys(row.dataset)[idx - 1]] ?? '0');
                const span = cells[idx].querySelector('span') || cells[idx];
                const fmtd = fmtNum(newVal);
                if (newVal !== old) {
                    if (color) span.textContent = fmtd;
                    else cells[idx].querySelector('strong') ? cells[idx].querySelector('strong').textContent = fmtd : (span.textContent = fmtd);
                    flash(cells[idx], newVal > old ? 'flash-up' : 'flash-down');
                }
            }

            const prev = {
                total_calls: parseInt(row.dataset.total_calls ?? 0),
                answered:    parseInt(row.dataset.answered ?? 0),
                missed:      parseInt(row.dataset.missed ?? 0),
                declined:    parseInt(row.dataset.declined ?? 0),
                voicemail:   parseInt(row.dataset.voicemail ?? 0),
                recorded:    parseInt(row.dataset.recorded ?? 0),
                total_duration: parseInt(row.dataset.total_duration ?? 0),
            };

            // Update data attrs
            row.dataset.total_calls    = agent.total_calls;
            row.dataset.answered       = agent.answered;
            row.dataset.missed         = agent.missed;
            row.dataset.declined       = agent.declined;
            row.dataset.voicemail      = agent.voicemail;
            row.dataset.recorded       = agent.recorded;
            row.dataset.total_duration = agent.total_duration;
            row.dataset.avg_duration   = avgSec;
            row.dataset.connect_rate   = cr;
            row.dataset.avg_mos        = agent.avg_mos ?? '';

            function updateCell(td, newVal, oldVal, fmt) {
                if (newVal === oldVal) return;
                const inner = td.querySelector('strong') || td.querySelector('span') || td;
                inner.textContent = fmt(newVal);
                flash(td, newVal > oldVal ? 'flash-up' : 'flash-down');
            }

            updateCell(cells[1], agent.total_calls, prev.total_calls, fmtNum);
            updateCell(cells[2], agent.answered, prev.answered, fmtNum);
            updateCell(cells[3], agent.missed, prev.missed, fmtNum);
            updateCell(cells[4], agent.declined, prev.declined, fmtNum);
            updateCell(cells[5], agent.voicemail, prev.voicemail, fmtNum);
            updateCell(cells[6], agent.recorded, prev.recorded, fmtNum);

            if (agent.total_duration !== prev.total_duration) {
                cells[7].textContent = fmtDuration(agent.total_duration);
            }
            cells[8].textContent = fmtDuration(avgSec);

            const crStrong = cells[9].querySelector('strong');
            const crFill   = cells[9].querySelector('.rate-fill');
            if (crStrong) crStrong.textContent = cr + '%';
            if (crFill)   crFill.style.width   = Math.min(cr, 100) + '%';

            // MOS cell (cells[10])
            if (cells[10]) {
                const mos = agent.avg_mos;
                if (mos !== null && mos !== undefined) {
                    const cls = mos >= 4.0 ? 'mos-good' : (mos >= 3.6 ? 'mos-fair' : 'mos-poor');
                    cells[10].innerHTML = `<span class="mos-badge ${cls}">${mos}</span>`;
                } else {
                    cells[10].innerHTML = '<span class="mos-none">&ndash;</span>';
                }
            }

        } else {
            // New agent — insert row
            const talkFmt = fmtDuration(agent.total_duration);
            const avgFmt  = fmtDuration(avgSec);
            const tr = document.createElement('tr');
            tr.dataset.key          = key;
            tr.dataset.name         = agent.name;
            tr.dataset.total_calls  = agent.total_calls;
            tr.dataset.answered     = agent.answered;
            tr.dataset.missed       = agent.missed;
            tr.dataset.declined     = agent.declined;
            tr.dataset.voicemail    = agent.voicemail;
            tr.dataset.recorded     = agent.recorded;
            tr.dataset.total_duration = agent.total_duration;
            tr.dataset.avg_duration = avgSec;
            tr.dataset.connect_rate = cr;
            tr.dataset.avg_mos      = agent.avg_mos ?? '';
            const mosTd = () => {
                const mos = agent.avg_mos;
                if (mos !== null && mos !== undefined) {
                    const cls = mos >= 4.0 ? 'mos-good' : (mos >= 3.6 ? 'mos-fair' : 'mos-poor');
                    return `<td class="ap-num"><span class="mos-badge ${cls}">${mos}</span></td>`;
                }
                return '<td class="ap-num"><span class="mos-none">&ndash;</span></td>';
            };
            tr.innerHTML = `
                <td><div style="font-weight:600">${agent.name}</div><div style="font-size:.6rem;color:var(--bs-surface-500)">Ext. ${agent.extension}</div></td>
                <td class="ap-num"><strong>${fmtNum(agent.total_calls)}</strong></td>
                <td class="ap-num"><span style="color:#1a8754;font-weight:600">${fmtNum(agent.answered)}</span></td>
                <td class="ap-num"><span style="color:#b87a14">${fmtNum(agent.missed)}</span></td>
                <td class="ap-num"><span style="color:#c84646">${fmtNum(agent.declined)}</span></td>
                <td class="ap-num"><span style="color:#6c757d">${fmtNum(agent.voicemail)}</span></td>
                <td class="ap-num"><span style="color:var(--bs-gold,#d4af37)">${fmtNum(agent.recorded)}</span></td>
                <td class="ap-num" style="white-space:nowrap;font-family:monospace;font-size:.7rem">${talkFmt}</td>
                <td class="ap-num" style="white-space:nowrap;font-family:monospace;font-size:.7rem">${avgFmt}</td>
                <td class="ap-num" style="white-space:nowrap"><strong>${cr}%</strong><span class="rate-bar"><span class="rate-fill" style="width:${Math.min(cr,100)}%"></span></span></td>
                ${mosTd()}
            `;
            tbody.appendChild(tr);
            Array.from(tr.querySelectorAll('td')).forEach(td => flash(td, 'flash-new'));
        }
    });

    // Re-sort after update
    doSort(sortCol);

    // Update footer total row
    const foot = document.getElementById('apFoot');
    if (foot) {
        const totCalls    = data.agents.reduce((s,a) => s + a.total_calls, 0);
        const totAnswered = data.agents.reduce((s,a) => s + a.answered, 0);
        const totMissed   = data.agents.reduce((s,a) => s + a.missed, 0);
        const totDeclined = data.agents.reduce((s,a) => s + a.declined, 0);
        const totVoice    = data.agents.reduce((s,a) => s + a.voicemail, 0);
        const totRecord   = data.agents.reduce((s,a) => s + a.recorded, 0);
        const totDur      = data.agents.reduce((s,a) => s + a.total_duration, 0);
        const totAvg      = totCalls > 0 ? Math.round(totDur / totCalls) : 0;
        const totCR       = totCalls > 0 ? Math.round((totAnswered / totCalls) * 1000) / 10 : 0;
        const cells = foot.querySelectorAll('td');
        cells[0].querySelector('#agentCount') && (foot.querySelector('#agentCount').textContent = `(${newAgentCount} agents)`);
        cells[1].textContent = fmtNum(totCalls);
        cells[2].innerHTML   = `<span style="color:#1a8754">${fmtNum(totAnswered)}</span>`;
        cells[3].innerHTML   = `<span style="color:#b87a14">${fmtNum(totMissed)}</span>`;
        cells[4].innerHTML   = `<span style="color:#c84646">${fmtNum(totDeclined)}</span>`;
        cells[5].innerHTML   = `<span style="color:#6c757d">${fmtNum(totVoice)}</span>`;
        cells[6].innerHTML   = `<span style="color:var(--bs-gold,#d4af37)">${fmtNum(totRecord)}</span>`;
        cells[7].textContent = fmtDuration(totDur);
        cells[8].textContent = fmtDuration(totAvg);
        cells[9].textContent = totCR + '%';
        // MOS footer — simple avg of agents with MOS data
        if (cells[10]) {
            const mosAgents = data.agents.filter(a => a.avg_mos !== null && a.avg_mos !== undefined);
            if (mosAgents.length > 0) {
                const avgMos = Math.round((mosAgents.reduce((s,a) => s + a.avg_mos, 0) / mosAgents.length) * 10) / 10;
                const cls = avgMos >= 4.0 ? 'mos-good' : (avgMos >= 3.6 ? 'mos-fair' : 'mos-poor');
                cells[10].innerHTML = `<span class="mos-badge ${cls}">${avgMos}</span>`;
            } else {
                cells[10].innerHTML = '<span class="mos-none">&ndash;</span>';
            }
        }
    }
}

// ── Boot ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    renderIcons();
    // Set initial "last updated" to page load time
    updateLastUpdated(new Date().toISOString());
    // Auto-pause if a non-today date range is selected (historical view)
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateFrom || $dateTo): ?>
    <?php
        $isToday = $todayPt === ($dateFrom ?? '') && $todayPt === ($dateTo ?? '');
    ?>
    <?php if(!$isToday): ?>
    toggleLive(); // pause — historical date range, no need to live-poll
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    scheduleNext();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/zoom-agent-performance.blade.php ENDPATH**/ ?>