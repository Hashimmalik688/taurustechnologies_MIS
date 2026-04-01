<?php $__env->startSection('title'); ?>
    Zoom Call Logs
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        /* Stats Cards */
        .stats-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.65rem;margin-bottom:.65rem }
        .stat-card { background:#fff;padding:.75rem 1rem;border-radius:.55rem;border:1px solid rgba(0,0,0,.06);box-shadow:0 1px 3px rgba(0,0,0,.03) }
        .stat-card-label { font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-500);margin-bottom:.25rem }
        .stat-card-value { font-size:1.55rem;font-weight:700;color:var(--bs-surface-900);font-variant-numeric:tabular-nums }
        .stat-card-icon { float:right;font-size:1.8rem;opacity:.15;margin-top:-.2rem }

        /* Table Styles */
        .rp-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.73rem }
        .rp-table thead th {
            padding:.55rem .65rem;font-size:.65rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.5px;color:var(--bs-surface-500,#64748b);
            background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
            white-space:nowrap;position:sticky;top:0;z-index:2;
        }
        .rp-table tbody td {
            padding:.5rem .65rem;border-bottom:1px solid rgba(0,0,0,.035);
            color:var(--bs-surface-900,#1e293b);vertical-align:middle;
        }
        .rp-table tbody tr:hover td { background:rgba(212,175,55,.04) }
        .rp-table tbody tr:last-child td { border-bottom:none }
        .rp-td-num { text-align:right;font-variant-numeric:tabular-nums }

        /* Status Badges — Zoom-normalized values */
        .status-badge {
            font-size:.6rem;font-weight:700;padding:.2rem .5rem;border-radius:10px;
            display:inline-block;text-transform:capitalize;letter-spacing:.4px;white-space:nowrap;
        }
        /* Connected / Auto Recorded / Recorded = green */
        .status-connected,
        .status-auto_recorded,
        .status-recorded         { background:rgba(52,195,143,.12);color:#1a8754 }
        /* Call Failed = red */
        .status-call_failed      { background:rgba(244,106,106,.12);color:#c84646 }
        /* Cancelled = orange */
        .status-cancelled        { background:rgba(241,180,76,.12);color:#b87a14 }
        /* No Answer = orange */
        .status-no_answer        { background:rgba(241,180,76,.12);color:#b87a14 }
        /* Busy = orange */
        .status-busy             { background:rgba(241,180,76,.12);color:#b87a14 }
        /* Declined / Rejected = red */
        .status-declined         { background:rgba(244,106,106,.12);color:#c84646 }
        /* Abandoned = grey-red */
        .status-abandoned        { background:rgba(244,106,106,.08);color:#c84646 }
        /* Voicemail = grey */
        .status-voicemail        { background:rgba(108,117,125,.12);color:#6c757d }
        /* Recording icon in duration cell */
        .dur-recorded { color:var(--bs-gold,#d4af37);text-decoration:none;font-weight:600 }
        .dur-pending  { color:#94a3b8;text-decoration:none }

        /* Empty State */
        .rp-empty { text-align:center;padding:3rem 1rem;color:var(--bs-surface-500) }
        .rp-empty i { font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25 }
        .rp-empty h6 { font-size:.85rem;font-weight:700;margin-bottom:.25rem }
        .rp-empty p { font-size:.72rem }

        /* Tab Pills (match account-switching-log style) */
        .tab-row{display:flex;gap:.35rem;margin-bottom:.65rem;flex-wrap:wrap}
        .tab-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:20px;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid var(--bs-surface-200,#e2e8f0);color:var(--bs-surface-500,#64748b);background:transparent;transition:all .15s}
        .tab-pill:hover{border-color:rgba(212,175,55,.3);color:#b89730}
        .tab-pill.active{background:linear-gradient(135deg,#d4af37,#c9a227);color:#fff;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25)}
        .tab-pill i{font-size:.85rem}

        /* Pagination */
        .pagination { display:flex;gap:.25rem;justify-content:center;padding:1rem;flex-wrap:wrap }
        .page-link { padding:.3rem .6rem;font-size:.72rem;border:1px solid rgba(0,0,0,.1);border-radius:6px;text-decoration:none;color:var(--bs-surface-700) }
        .page-link:hover { background:rgba(212,175,55,.08) }
        .page-item.active .page-link { background:var(--bs-gold,#d4af37);color:#fff;border-color:var(--bs-gold,#d4af37) }

        /* Dark theme adjustments */
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card {
            background:rgba(15,23,42,.6);border-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card-label {
            color:#94a3b8;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card-value {
            color:#e2e8f0;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody td {
            color:#e2e8f0;border-bottom-color:rgba(255,255,255,.04);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-video"></i> Zoom Call Logs
            <span class="rp-sub">One row per call &bull; Times shown in Pacific Time (PT)</span>
        </h5>
        <div style="display:flex;gap:.5rem">
            <a href="<?php echo e(route('settings.reports.zoom-diagnostics')); ?>" class="act-btn a-warn" style="font-size:.72rem;padding:.3rem .65rem" title="View webhook diagnostics">
                <i class="bx bx-pulse"></i> Diagnostics
            </a>
            <a href="<?php echo e(route('settings.reports.hub')); ?>" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
                <i class="bx bx-arrow-back"></i> Reports
            </a>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['has_any_filter']): ?>
    <div style="margin-bottom:.35rem">
        <span style="font-size:.65rem;color:var(--bs-surface-500);font-weight:600;text-transform:uppercase;letter-spacing:.5px">
            <i class="bx bx-filter-alt" style="vertical-align:middle"></i> Filtered Results
        </span>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="stats-grid">
        <div class="stat-card">
            <i class="bx bx-phone stat-card-icon"></i>
            <div class="stat-card-label">Total Calls</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['total_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-time stat-card-icon"></i>
            <div class="stat-card-label">Total Duration</div>
            <div class="stat-card-value">
                <?php
                    $totalSecs = $stats['total_duration'];
                    $hours = floor($totalSecs / 3600);
                    $mins = floor(($totalSecs % 3600) / 60);
                    $secs = $totalSecs % 60;
                ?>
                <?php echo e(sprintf('%02d:%02d:%02d', $hours, $mins, $secs)); ?>

            </div>
        </div>
        <div class="stat-card">
            <i class="bx bx-link stat-card-icon"></i>
            <div class="stat-card-label">Connected</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['connected_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-phone-call stat-card-icon"></i>
            <div class="stat-card-label">Answered</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['answered_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-x-circle stat-card-icon"></i>
            <div class="stat-card-label">Declined / Rejected</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['declined_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-phone-off stat-card-icon"></i>
            <div class="stat-card-label">Missed / No Answer</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['missed_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-voicemail stat-card-icon"></i>
            <div class="stat-card-label">Voicemail</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['voicemail_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-disc stat-card-icon"></i>
            <div class="stat-card-label">Auto Recorded</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['auto_recorded'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-microphone stat-card-icon"></i>
            <div class="stat-card-label">Recorded</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['recorded_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-phone-outgoing stat-card-icon"></i>
            <div class="stat-card-label">Outbound</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['outbound_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-phone-incoming stat-card-icon"></i>
            <div class="stat-card-label">Inbound</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['inbound_calls'])); ?></div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$stats['has_date_filter'] && isset($stats['today_calls'])): ?>
        <div class="stat-card">
            <i class="bx bx-calendar-check stat-card-icon"></i>
            <div class="stat-card-label">Today's Calls</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['today_calls'])); ?></div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userStats): ?>
    <div style="margin-bottom:1rem">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
            <i class="bx bx-user-circle" style="color:var(--bs-gold,#d4af37);font-size:1.3rem"></i>
            <h6 style="margin:0;font-size:.85rem;font-weight:700;color:var(--bs-surface-700)">
                <?php
                    $selectedAgentOption = $agentOptions->firstWhere('extension', request('agent_filter'));
                    $selectedAgentLabel = $selectedAgentOption
                        ? $selectedAgentOption['name'] . ' (Ext. ' . $selectedAgentOption['extension'] . ')'
                        : request('agent_filter');
                ?>
                <?php echo e($selectedAgentLabel); ?> - Performance Breakdown
            </h6>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="bx bx-phone-outgoing stat-card-icon"></i>
                <div class="stat-card-label">Dialed</div>
                <div class="stat-card-value"><?php echo e(number_format($userStats['total_dialed'])); ?></div>
            </div>
            
            <div class="stat-card">
                <i class="bx bx-phone-call stat-card-icon"></i>
                <div class="stat-card-label">Connected</div>
                <div class="stat-card-value"><?php echo e(number_format($userStats['connected'])); ?></div>
                <div style="font-size:.6rem;color:var(--bs-surface-500);margin-top:.15rem">
                    <?php echo e($userStats['total_dialed'] > 0 ? round(($userStats['connected'] / $userStats['total_dialed']) * 100, 1) : 0); ?>% connect rate
                </div>
            </div>
            
            <div class="stat-card">
                <i class="bx bx-phone-off stat-card-icon"></i>
                <div class="stat-card-label">Failed/Missed</div>
                <div class="stat-card-value"><?php echo e(number_format($userStats['failed'])); ?></div>
                <div style="font-size:.6rem;color:var(--bs-surface-500);margin-top:.15rem">
                    No answer, rejected, busy
                </div>
            </div>
            
            <div class="stat-card">
                <i class="bx bx-video stat-card-icon"></i>
                <div class="stat-card-label">Recorded</div>
                <div class="stat-card-value"><?php echo e(number_format($userStats['with_recording'])); ?></div>
                <div style="font-size:.6rem;color:var(--bs-surface-500);margin-top:.15rem">
                    <?php echo e($userStats['connected'] > 0 ? round(($userStats['with_recording'] / $userStats['connected']) * 100, 1) : 0); ?>% of connected
                </div>
            </div>
            
            <div class="stat-card">
                <i class="bx bx-time-five stat-card-icon"></i>
                <div class="stat-card-label">Recording > 5min</div>
                <div class="stat-card-value"><?php echo e(number_format($userStats['recordings_over_5min'])); ?></div>
                <div style="font-size:.6rem;color:var(--bs-surface-500);margin-top:.15rem">
                    Quality conversations
                </div>
            </div>
            
            <div class="stat-card">
                <i class="bx bx-stopwatch stat-card-icon"></i>
                <div class="stat-card-label">Total Talk Time</div>
                <div class="stat-card-value" style="font-size:1.3rem">
                    <?php
                        $talkSecs = $userStats['total_talk_time'];
                        $talkH = floor($talkSecs / 3600);
                        $talkM = floor(($talkSecs % 3600) / 60);
                        $talkS = $talkSecs % 60;
                    ?>
                    <?php echo e(sprintf('%02d:%02d:%02d', $talkH, $talkM, $talkS)); ?>

                </div>
                <div style="font-size:.6rem;color:var(--bs-surface-500);margin-top:.15rem">
                    Hours:Minutes:Seconds
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['total_calls'] > 0 && $stats['total_calls'] < 300): ?>
    <div class="alert alert-info" style="font-size:.75rem;padding:.75rem 1rem;margin-bottom:1rem;border-radius:12px;background:#e3f2fd;border:1px solid #90caf9">
        <i class="bx bx-info-circle" style="font-size:1.1rem;vertical-align:middle"></i>
        <strong>Webhook Coverage:</strong> This page displays all webhook events captured from Zoom Phone, matching Zoom's dashboard view. 
        If you see fewer events than in Zoom, enable additional webhook subscriptions: <em>Callee phone is ringing</em>, <em>Callee missed a call</em>, <em>Callee rejected a call</em>, <em>Voicemail received</em>.
        <br><small style="margin-top:.25rem;display:block">
            Currently tracking: <strong><?php echo e(number_format($stats['total_calls'])); ?></strong> webhook events
        </small>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="tab-row" style="margin-bottom:.65rem">
        <a href="<?php echo e(route('settings.reports.zoom-logs')); ?>" class="tab-pill active">
            <i class="bx bx-list-ul"></i> Call Logs
        </a>
        <a href="<?php echo e(route('settings.reports.zoom-agent-performance')); ?>" class="tab-pill">
            <i class="bx bx-bar-chart-alt-2"></i> Agent Performance
        </a>
    </div>

    
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-body" style="padding:.75rem">
            <form method="GET" action="<?php echo e(route('settings.reports.zoom-logs')); ?>" id="filterForm">
                <div style="display:flex;gap:.55rem;align-items:flex-end;flex-wrap:wrap">
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Search</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                               placeholder="Name, phone, call ID..."
                               style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:200px">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Agent</label>
                        <select name="agent_filter" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:150px">
                            <option value="">All Agents</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $agentOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($opt['extension']); ?>" <?php echo e(request('agent_filter') == $opt['extension'] ? 'selected' : ''); ?>>
                                    <?php echo e($opt['name']); ?> (Ext. <?php echo e($opt['extension']); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Status</label>
                        <select name="call_result" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:140px">
                            <option value="">All Statuses</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('call_result') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Type</label>
                        <select name="call_type" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:110px">
                            <option value="">All Types</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('call_type') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Event</label>
                        <select name="event_type" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:180px">
                            <option value="">All Events</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('event_type') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Recording</label>
                        <select name="has_recording" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:110px">
                            <option value="">Both</option>
                            <option value="yes" <?php echo e(request('has_recording') == 'yes' ? 'selected' : ''); ?>>Yes</option>
                            <option value="no" <?php echo e(request('has_recording') == 'no' ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">From</label>
                        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" 
                               style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">To</label>
                        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" 
                               style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff">
                    </div>
                    <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                        <i class="bx bx-filter-alt" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Filter
                    </button>
                    <a href="<?php echo e(route('settings.reports.zoom-logs')); ?>" class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;text-decoration:none">
                        <i class="bx bx-x" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($callLogs->count() > 0): ?>
            <div style="overflow-x:auto">
                <table class="rp-table">
                    <thead>
                        <tr>
                            <th>Date/Time (PT)</th>
                            <th>Direction</th>
                            <th>Agent</th>
                            <th>Contact</th>
                            <th>Call Result</th>
                            <th>Duration</th>
                            <th>Lead</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                // ── Call Result normalization (raw DB → Zoom-style labels) ──
                                $resultNorm = [
                                    // Connected variants
                                    'connected'      => ['label' => 'Connected',    'css' => 'connected'],
                                    'Call connected' => ['label' => 'Connected',    'css' => 'connected'],
                                    'answered'       => ['label' => 'Connected',    'css' => 'connected'],
                                    'Recorded'       => ['label' => 'Auto Recorded','css' => 'auto_recorded'],
                                    'Auto Recorded'  => ['label' => 'Auto Recorded','css' => 'auto_recorded'],
                                    // Failed variants
                                    'call_failed'    => ['label' => 'Call Failed',  'css' => 'call_failed'],
                                    'Call failed'    => ['label' => 'Call Failed',  'css' => 'call_failed'],
                                    'Call Failed'    => ['label' => 'Call Failed',  'css' => 'call_failed'],
                                    // Cancelled variants
                                    'Call Cancel'    => ['label' => 'Cancelled',    'css' => 'cancelled'],
                                    'canceled'       => ['label' => 'Cancelled',    'css' => 'cancelled'],
                                    'Cancelled'      => ['label' => 'Cancelled',    'css' => 'cancelled'],
                                    // No Answer
                                    'No Answer'      => ['label' => 'No Answer',    'css' => 'no_answer'],
                                    'no_answer'      => ['label' => 'No Answer',    'css' => 'no_answer'],
                                    // Busy / Declined
                                    'Busy'           => ['label' => 'Busy',         'css' => 'busy'],
                                    'Rejected'       => ['label' => 'Declined',     'css' => 'declined'],
                                    'rejected'       => ['label' => 'Declined',     'css' => 'declined'],
                                    // Abandoned / Voicemail
                                    'abandoned'      => ['label' => 'Abandoned',    'css' => 'abandoned'],
                                    'Abandoned'      => ['label' => 'Abandoned',    'css' => 'abandoned'],
                                    'voicemail'      => ['label' => 'Voicemail',    'css' => 'voicemail'],
                                    'Voicemail'      => ['label' => 'Voicemail',    'css' => 'voicemail'],
                                ];
                                $rawResult  = $log->call_result;
                                $resultInfo = isset($resultNorm[$rawResult]) ? $resultNorm[$rawResult] : null;
                                $resultLabel = $resultInfo ? $resultInfo['label'] : ($rawResult ? ucwords(str_replace(['_','-'], ' ', $rawResult)) : null);
                                $resultCss   = $resultInfo ? $resultInfo['css']  : ($rawResult ? strtolower(str_replace([' ','-'], '_', $rawResult)) : null);

                                // Smart direction detection: use stored call_type but correct from event name
                                $eventType = $log->event_type ?? '';
                                $isCalleeEvent = str_contains($eventType, 'callee') || str_contains($eventType, 'voicemail');
                                $isCallerEvent = str_contains($eventType, 'caller') || str_contains($eventType, 'callout');
                                
                                $direction = strtolower($log->call_type ?? 'unknown');
                                // Override wrong defaults using event name
                                if ($isCalleeEvent && $direction !== 'inbound') {
                                    $direction = 'inbound';
                                } elseif ($isCallerEvent && $direction !== 'outbound') {
                                    $direction = 'outbound';
                                }
                                
                                // Agent = our internal user, Contact = external party
                                if ($direction === 'inbound') {
                                    $agentName = $log->agent ? $log->agent->name : ($log->callee_name ?? null);
                                    $agentDetail = $log->callee_extension ? 'Ext. ' . $log->callee_extension : ($log->callee_email ?? '');
                                    $contactName = $log->caller_name ?? null;
                                    $contactNumber = $log->caller_number ?? $log->caller_did_number ?? null;
                                } else {
                                    $agentName = $log->agent ? $log->agent->name : ($log->caller_name ?? null);
                                    $agentDetail = $log->caller_extension ? 'Ext. ' . $log->caller_extension : ($log->caller_email ?? '');
                                    $contactName = $log->callee_name ?? null;
                                    $contactNumber = $log->callee_number ?? $log->callee_did_number ?? null;
                                }
                            ?>
                            <tr>
                                <td style="white-space:nowrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->call_start_time): ?>
                                        <?php $mt = $log->call_start_time->copy()->shiftTimezone('UTC')->setTimezone($displayTz); ?>
                                        <div style="font-weight:600"><?php echo e($mt->format('m/d/Y')); ?></div>
                                        <div style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e($mt->format('g:i A')); ?> PT</div>
                                    <?php else: ?>
                                        <div style="font-size:.65rem;color:var(--bs-surface-400)"><?php echo e($log->created_at->format('m/d/Y g:i A')); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($direction === 'inbound'): ?>
                                        <span style="font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:8px;background:rgba(52,195,143,.12);color:#1a8754;text-transform:uppercase;white-space:nowrap">
                                            <i class="bx bx-phone-incoming" style="font-size:.7rem;vertical-align:middle"></i> IN
                                        </span>
                                    <?php elseif($direction === 'outbound'): ?>
                                        <span style="font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:8px;background:rgba(80,165,241,.12);color:#2b81c9;text-transform:uppercase;white-space:nowrap">
                                            <i class="bx bx-phone-outgoing" style="font-size:.7rem;vertical-align:middle"></i> OUT
                                        </span>
                                    <?php elseif($direction === 'internal'): ?>
                                        <span style="font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:8px;background:rgba(108,117,125,.12);color:#6c757d;text-transform:uppercase;white-space:nowrap">
                                            <i class="bx bx-transfer" style="font-size:.7rem;vertical-align:middle"></i> INT
                                        </span>
                                    <?php else: ?>
                                        <span style="font-size:.6rem;color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agentName): ?>
                                        <div style="font-weight:600;white-space:nowrap"><?php echo e(Str::limit($agentName, 20)); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agentDetail): ?>
                                        <div style="font-size:.6rem;color:var(--bs-surface-500)"><?php echo e($agentDetail); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$agentName && !$agentDetail): ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contactName): ?>
                                        <div style="font-weight:600"><?php echo e($contactName); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contactNumber): ?>
                                        <div style="font-family:monospace;font-size:.7rem;color:var(--bs-surface-600)"><?php echo e($contactNumber); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$contactName && !$contactNumber): ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resultLabel): ?>
                                        <span class="status-badge status-<?php echo e($resultCss); ?>">
                                            <?php echo e($resultLabel); ?>

                                        </span>
                                    <?php elseif($log->call_status): ?>
                                        <span class="status-badge status-<?php echo e($log->call_status); ?>">
                                            <?php echo e(str_replace('_', ' ', $log->call_status)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="rp-td-num" style="white-space:nowrap">
                                    <?php
                                        $dur = $log->duration_seconds ?? 0;
                                        $dH  = floor($dur / 3600);
                                        $dM  = floor(($dur % 3600) / 60);
                                        $dS  = $dur % 60;
                                        $durFmt = $dH > 0
                                            ? sprintf('%d:%02d:%02d', $dH, $dM, $dS)
                                            : sprintf('%02d:%02d', $dM, $dS);
                                        $hasRealUrl = $log->recording_url
                                            && !in_array($log->recording_url, ['pending_api_fetch','not_available']);
                                        $hasPending = $log->recording_url === 'pending_api_fetch';
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasRealUrl): ?>
                                        <a href="<?php echo e(route('zoom.recording.play', $log->id)); ?>" target="_blank"
                                           class="dur-recorded" title="Play recording">
                                            <i class="bx bx-play-circle" style="font-size:.9rem;vertical-align:middle"></i>
                                            <?php echo e($dur > 0 ? $durFmt : '—'); ?>

                                        </a>
                                    <?php elseif($hasPending): ?>
                                        <a href="<?php echo e(route('zoom.recording.play', $log->id)); ?>" target="_blank"
                                           class="dur-pending" title="Recording available — click to load">
                                            <i class="bx bx-cloud-download" style="font-size:.9rem;vertical-align:middle"></i>
                                            <?php echo e($dur > 0 ? $durFmt : '—'); ?>

                                        </a>
                                    <?php elseif($dur > 0): ?>
                                        <?php echo e($durFmt); ?>

                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->lead_id && $log->lead): ?>
                                        <a href="<?php echo e(route('leads.show', $log->lead_id)); ?>" 
                                           style="font-size:.65rem;font-weight:600;color:var(--bs-gold,#d4af37);text-decoration:none"
                                           title="<?php echo e($log->lead->cn_name); ?>">
                                            <?php echo e(Str::limit($log->lead->cn_name, 18)); ?>

                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->lead->state): ?>
                                            <div style="font-size:.55rem;color:var(--bs-surface-500)"><?php echo e($log->lead->state); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php elseif($log->agent_id): ?>
                                        <span style="font-size:.55rem;color:var(--bs-surface-400)" title="Agent matched but no lead linked">
                                            <i class="bx bx-link" style="font-size:.6rem"></i> Agent only
                                        </span>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-300)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->lead_id && $log->lead): ?>
                                        <a href="<?php echo e(route('leads.show', $log->lead_id)); ?>" 
                                           class="act-btn a-primary" 
                                           style="font-size:.65rem;padding:.2rem .45rem" 
                                           title="View Lead: <?php echo e($log->lead->cn_name); ?>">
                                            <i class="bx bx-link-external"></i>
                                        </a>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-300)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            
            <div style="padding:1rem;border-top:1px solid rgba(0,0,0,.06)">
                <?php echo e($callLogs->links()); ?>

            </div>
        <?php else: ?>
            <div class="rp-empty">
                <i class="bx bx-phone-off"></i>
                <h6>No Call Logs Found</h6>
                <p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search', 'agent_filter', 'call_result', 'call_type', 'event_type', 'has_recording', 'date_from', 'date_to'])): ?>
                        Try adjusting your filters
                    <?php else: ?>
                        Call logs will appear here when Zoom webhooks capture call data
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/zoom-logs.blade.php ENDPATH**/ ?>