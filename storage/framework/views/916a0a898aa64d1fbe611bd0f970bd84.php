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

        /* Status Badges */
        .status-badge {
            font-size:.6rem;font-weight:700;padding:.2rem .5rem;border-radius:10px;
            display:inline-block;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;
        }
        .status-connected,
        .status-answered,
        .status-call_connected  { background:rgba(52,195,143,.12);color:#1a8754 }
        .status-completed  { background:rgba(80,165,241,.12);color:#2b81c9 }
        .status-no_answer,
        .status-no_one_answered  { background:rgba(241,180,76,.12);color:#b87a14 }
        .status-missed     { background:rgba(244,106,106,.12);color:#c84646 }
        .status-voicemail  { background:rgba(108,117,125,.12);color:#6c757d }
        .status-rejected   { background:rgba(244,106,106,.12);color:#c84646 }
        .status-busy       { background:rgba(241,180,76,.12);color:#b87a14 }
        .status-cancelled,
        .status-declined   { background:rgba(244,106,106,.12);color:#c84646 }

        /* Empty State */
        .rp-empty { text-align:center;padding:3rem 1rem;color:var(--bs-surface-500) }
        .rp-empty i { font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25 }
        .rp-empty h6 { font-size:.85rem;font-weight:700;margin-bottom:.25rem }
        .rp-empty p { font-size:.72rem }

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
            <span class="rp-sub">All Zoom Phone webhook events &bull; Matches Zoom dashboard</span>
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
                <?php echo e(gmdate('H:i:s', $stats['total_duration'])); ?>

            </div>
        </div>
        <div class="stat-card">
            <i class="bx bx-video stat-card-icon"></i>
            <div class="stat-card-label">With Recording</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['calls_with_recording'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-calendar-check stat-card-icon"></i>
            <div class="stat-card-label">Today's Calls</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['today_calls'])); ?></div>
        </div>
        <div class="stat-card">
            <i class="bx bx-phone-call stat-card-icon"></i>
            <div class="stat-card-label">Answered Calls</div>
            <div class="stat-card-value"><?php echo e(number_format($stats['answered_calls'])); ?></div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userStats): ?>
    <div style="margin-bottom:1rem">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
            <i class="bx bx-user-circle" style="color:var(--bs-gold,#d4af37);font-size:1.3rem"></i>
            <h6 style="margin:0;font-size:.85rem;font-weight:700;color:var(--bs-surface-700)">
                <?php echo e(request('user_filter')); ?> - Performance Breakdown
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
                <div class="stat-card-value" style="font-size:1.3rem"><?php echo e(gmdate('H:i:s', $userStats['total_talk_time'])); ?></div>
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
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">User</label>
                        <input type="text" name="user_filter" value="<?php echo e(request('user_filter')); ?>" 
                               placeholder="Email or extension..."
                               style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:150px">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Status</label>
                        <select name="call_status" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:140px">
                            <option value="">All Statuses</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('call_status') == $key ? 'selected' : ''); ?>>
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
                            <th>Date/Time</th>
                            <th>Caller</th>
                            <th>Callee</th>
                            <th>Type</th>
                            <th>Event</th>
                            <th>Status/Result</th>
                            <th>Duration</th>
                            <th>Recording</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="white-space:nowrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->call_start_time): ?>
                                        <div style="font-weight:600"><?php echo e($log->call_start_time->format('m/d/Y')); ?></div>
                                        <div style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e($log->call_start_time->format('g:i A')); ?></div>
                                    <?php else: ?>
                                        <div style="font-size:.65rem;color:var(--bs-surface-400)"><?php echo e($log->created_at->format('m/d/Y g:i A')); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->caller_name): ?>
                                        <div style="font-weight:600"><?php echo e($log->caller_name); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->caller_number): ?>
                                        <div style="font-family:monospace;font-size:.7rem;color:var(--bs-surface-600)"><?php echo e($log->caller_number); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->caller_email): ?>
                                        <div style="font-size:.65rem;color:var(--bs-surface-500)"><?php echo e($log->caller_email); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$log->caller_name && !$log->caller_number && !$log->caller_email): ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->callee_name): ?>
                                        <div style="font-weight:600"><?php echo e($log->callee_name); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->callee_number): ?>
                                        <div style="font-family:monospace;font-size:.7rem;color:var(--bs-surface-600)"><?php echo e($log->callee_number); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$log->callee_name && !$log->callee_number): ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size:.65rem;text-transform:uppercase;font-weight:600">
                                        <?php echo e($log->call_type ?? '—'); ?>

                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:.6rem;color:var(--bs-surface-600)"><?php echo e($log->event_type); ?></span>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->call_result): ?>
                                        <span class="status-badge status-<?php echo e(strtolower(str_replace(' ', '_', $log->call_result))); ?>">
                                            <?php echo e($log->call_result); ?>

                                        </span>
                                    <?php elseif($log->call_status): ?>
                                        <span class="status-badge status-<?php echo e($log->call_status); ?>">
                                            <?php echo e(str_replace('_', ' ', $log->call_status)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="rp-td-num">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->duration_seconds > 0): ?>
                                        <?php echo e(gmdate('i:s', $log->duration_seconds)); ?>

                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="text-align:center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->recording_url): ?>
                                        <a href="<?php echo e($log->recording_url); ?>" target="_blank" title="Play Recording">
                                            <i class="bx bx-play-circle" style="font-size:1.2rem;color:var(--bs-gold,#d4af37)"></i>
                                        </a>
                                    <?php else: ?>
                                        <span style="color:var(--bs-surface-300)">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <div style="display:flex;gap:.25rem;flex-wrap:wrap">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->zoom_call_id): ?>
                                            <button type="button" 
                                                    class="act-btn a-secondary" 
                                                    style="font-size:.65rem;padding:.2rem .45rem" 
                                                    title="Copy Zoom Call ID"
                                                    onclick="navigator.clipboard.writeText('<?php echo e($log->zoom_call_id); ?>'); this.innerHTML='<i class=\"bx bx-check\"></i>'; setTimeout(() => this.innerHTML='<i class=\"bx bx-copy\"></i>', 1000)">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->lead_id && $log->lead): ?>
                                            <a href="<?php echo e(route('leads.show', $log->lead_id)); ?>" 
                                               class="act-btn a-primary" 
                                               style="font-size:.65rem;padding:.2rem .45rem" 
                                               title="View in MIS: <?php echo e($log->lead->cn_name); ?>">
                                                <i class="bx bx-link-external"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search', 'agent_id', 'call_status', 'call_type', 'event_type', 'has_recording', 'date_from', 'date_to'])): ?>
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