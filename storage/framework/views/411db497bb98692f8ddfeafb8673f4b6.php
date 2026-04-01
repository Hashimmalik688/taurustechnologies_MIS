<?php $__env->startSection('title'); ?>
    Reports
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        .rp-empty { text-align:center;padding:3rem 1rem;color:var(--bs-surface-500) }
        .rp-empty i { font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25 }
        .rp-empty h6 { font-size:.85rem;font-weight:700;margin-bottom:.25rem }
        .rp-empty p { font-size:.72rem }

        .loading-overlay {
            position:absolute;top:0;left:0;right:0;bottom:0;
            background:rgba(255,255,255,.8);display:flex;align-items:center;
            justify-content:center;z-index:10;border-radius:.55rem;
        }
        .loading-overlay .spinner-border { width:2rem;height:2rem }

        /* ── Results header ── */
        .rp-results-hdr {
            padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.06);
            display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.4rem;
        }
        .rp-results-hdr h6 { margin:0;font-size:.82rem;font-weight:700;display:flex;align-items:center;gap:.35rem }
        .rp-results-hdr h6 i { color:var(--bs-gold,#d4af37);font-size:.95rem }
        .rp-results-hdr h6 span { font-weight:400;color:var(--bs-surface-500);font-size:.72rem }
        .rp-results-meta { font-size:.7rem;color:var(--bs-surface-500) }

        /* ── Professional table ── */
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

        .rp-th-id { width:50px }
        .rp-th-name { min-width:140px }
        .rp-th-num { text-align:right }
        .rp-td-id { font-weight:600;color:var(--bs-surface-500);font-size:.68rem }
        .rp-td-name { font-weight:600 }
        .rp-td-mono { font-family:SFMono-Regular,Menlo,monospace;font-size:.68rem;letter-spacing:.3px }
        .rp-td-num { text-align:right;font-weight:600;font-variant-numeric:tabular-nums }

        /* ── Status badges ── */
        .rp-badge {
            font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;
            display:inline-block;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;
        }
        .rp-badge-sale { background:rgba(52,195,143,.12);color:#1a8754 }
        .rp-badge-pending { background:rgba(241,180,76,.12);color:#b87a14 }
        .rp-badge-declined { background:rgba(244,106,106,.12);color:#c84646 }
        .rp-badge-chargeback { background:rgba(220,38,38,.1);color:#b91c1c }
        .rp-badge-accepted { background:rgba(80,165,241,.12);color:#2b81c9 }
        .rp-badge-transferred { background:rgba(139,92,246,.1);color:#7c3aed }
        .rp-badge-returned { background:rgba(251,146,60,.1);color:#c2410c }
        .rp-badge-closed { background:rgba(108,117,125,.1);color:#4b5563 }
        .rp-badge-default { background:rgba(108,117,125,.08);color:#6c757d }

        /* ── Pagination ── */
        .rp-pagination { padding:.7rem .85rem;border-top:1px solid rgba(0,0,0,.04);display:flex;justify-content:center }
        .rp-pagination .pagination { margin:0;gap:2px }
        .rp-pagination .page-link {
            font-size:.7rem;padding:.28rem .55rem;border-radius:8px;
            border:1px solid rgba(0,0,0,.06);color:#475569;font-weight:600;
        }
        .rp-pagination .page-item.active .page-link {
            background:linear-gradient(135deg,#d4af37,#b8941f);border-color:#d4af37;color:#0f172a;
        }

        /* ── Print button ── */
        .rp-print-btn {
            display:inline-flex;align-items:center;gap:.3rem;
            font-size:.72rem;font-weight:600;padding:.3rem .65rem;
            border-radius:22px;border:1px solid rgba(0,0,0,.08);
            background:#fff;color:#475569;cursor:pointer;transition:all .15s;
        }
        .rp-print-btn:hover { border-color:#d4af37;color:#92760d;box-shadow:0 0 0 2px rgba(212,175,55,.12) }
        .rp-print-btn i { font-size:.85rem }

        /* ── Dark mode overrides ── */
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody td {
            color:#e2e8f0;border-bottom-color:rgba(255,255,255,.04);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody tr:hover td {
            background:rgba(212,175,55,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-results-hdr {
            border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-print-btn {
            background:rgba(30,41,59,.8);border-color:rgba(255,255,255,.1);color:#cbd5e1;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .loading-overlay {
            background:rgba(15,23,42,.8);
        }

        /* ── Print styles ── */
        @media print {
            body * { visibility:hidden }
            .rp-print-area, .rp-print-area * { visibility:visible }
            .rp-print-area {
                position:absolute;left:0;top:0;width:100%;
                background:#fff !important;color:#000 !important;
            }
            .rp-print-area .rp-print-header {
                display:block !important;text-align:center;padding:12px 0;
                border-bottom:2px solid #1e293b;margin-bottom:8px;
            }
            .rp-print-area .rp-print-header h4 { margin:0;font-size:14px;font-weight:700;color:#1e293b }
            .rp-print-area .rp-print-header p { margin:2px 0 0;font-size:10px;color:#64748b }
            .rp-print-area .rp-print-kpis {
                display:flex !important;justify-content:space-between;
                padding:8px 0;border-bottom:1px solid #e2e8f0;margin-bottom:6px;
                font-size:10px;
            }
            .rp-print-area .rp-print-kpis div { text-align:center }
            .rp-print-area .rp-print-kpis strong { display:block;font-size:13px }
            .rp-print-area .rp-table { font-size:8px }
            .rp-print-area .rp-table th { font-size:7px;padding:3px 4px;background:#f1f5f9 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact }
            .rp-print-area .rp-table td { padding:3px 4px;border-bottom:1px solid #e2e8f0 !important }
            .rp-badge { font-size:7px !important;padding:1px 4px !important;-webkit-print-color-adjust:exact;print-color-adjust:exact }
            .rp-pagination, .rp-print-btn, .rp-results-hdr { display:none !important }
            .rp-print-area .rp-print-footer {
                display:block !important;text-align:center;
                padding-top:8px;border-top:1px solid #e2e8f0;
                font-size:8px;color:#94a3b8;margin-top:8px;
            }
            @page { size:landscape;margin:10mm }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-bar-chart-alt-2"></i> Sales Reports
            <span class="rp-sub">Generate &amp; export</span>
        </h5>
        <a href="<?php echo e(route('settings.reports.hub')); ?>" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>

    
    <div class="ex-card pipe-filter-bar" style="margin-bottom:.65rem">
        <span class="pipe-pill-lbl">Type</span>
        <button class="pipe-pill" data-type="all">All Records</button>
        <button class="pipe-pill active" data-type="sales">Sales</button>
        <button class="pipe-pill" data-type="partner">Partner</button>
        <button class="pipe-pill" data-type="submissions">Manager Submissions</button>
        <button class="pipe-pill" data-type="chargebacks">Chargebacks</button>
        <button class="pipe-pill" data-type="retention">Retention</button>
        <button class="pipe-pill" data-type="issuance">Issuance</button>
    </div>

    
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr" id="filterToggle" style="cursor:pointer">
            <h6><i class="bx bx-filter-alt"></i> Filters</h6>
            <i class="bx bx-chevron-down" id="filterToggleIcon" style="font-size:1rem;opacity:.5;transition:transform .2s"></i>
        </div>
        <div class="sec-body" id="filterBody" style="padding:.75rem">
            <form id="reportForm">
                <input type="hidden" name="report_type" id="reportType" value="sales">

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.55rem">
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Closer</label>
                        <select name="closer_id" id="closerFilter" class="sl-pill-select">
                            <option value="">All Closers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $closers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Manager</label>
                        <select name="manager_id" id="managerFilter" class="sl-pill-select">
                            <option value="">All Managers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Carrier</label>
                        <select name="carrier_id" id="carrierFilter" class="sl-pill-select">
                            <option value="">All Carriers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($name); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <option value="__other__">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Partner</label>
                        <select name="partner_id" id="partnerFilter" class="sl-pill-select">
                            <option value="">All Partners</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partner); ?>"><?php echo e($partner); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Verifier</label>
                        <select name="verifier_id" id="verifierFilter" class="sl-pill-select">
                            <option value="">All Verifiers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $verifiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Lead Status</label>
                        <select name="status" id="statusFilter" class="sl-pill-select">
                            <option value="">All Statuses</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Team</label>
                        <select name="team" id="teamFilter" class="sl-pill-select">
                            <option value="">All Teams</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($team); ?>"><?php echo e($team); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Source</label>
                        <select name="source" id="sourceFilter" class="sl-pill-select">
                            <option value="">All Sources</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($source); ?>"><?php echo e($source); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">State</label>
                        <select name="state" id="stateFilter" class="sl-pill-select">
                            <option value="">All States</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">QA Status</label>
                        <select name="qa_status" id="qaStatusFilter" class="sl-pill-select">
                            <option value="">All</option>
                            <option value="Good">Good</option>
                            <option value="Avg">Avg</option>
                            <option value="Bad">Bad</option>
                            <option value="In Review">In Review</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Submission Status</label>
                        <select name="submission_status" id="managerStatusFilter" class="sl-pill-select">
                            <option value="">All</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="declined">Declined</option>
                            <option value="underwriting">Underwriting</option>
                            <option value="chargeback">Chargeback</option>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Created From</label>
                        <input type="text" name="date_from" id="dateFrom" class="pipe-pill-date sl-pill-date" placeholder="From">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Created To</label>
                        <input type="text" name="date_to" id="dateTo" class="pipe-pill-date sl-pill-date" placeholder="To">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Sale From</label>
                        <input type="text" name="sale_date_from" id="saleDateFrom" class="pipe-pill-date sl-pill-date" placeholder="From">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Sale To</label>
                        <input type="text" name="sale_date_to" id="saleDateTo" class="pipe-pill-date sl-pill-date" placeholder="To">
                    </div>
                </div>

                <div style="display:flex;gap:.4rem;margin-top:.65rem;align-items:center">
                    <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                        <i class="bx bx-search-alt" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Generate
                    </button>
                    <button type="button" class="pipe-pill-clear" id="resetFilters">
                        <i class="bx bx-reset"></i> Reset
                    </button>
                    <button type="button" class="act-btn a-success" id="exportCsv" style="margin-left:auto">
                        <i class="bx bx-download"></i> Export CSV
                    </button>
                    <button type="button" class="rp-print-btn" id="printReport" style="display:none">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="kpi-row" id="summaryRow" style="display:none">
        <div class="ex-card kpi-card k-gold">
            <i class="bx bx-file k-icon"></i>
            <div class="k-val" id="summaryTotal">0</div>
            <div class="k-lbl">Records</div>
        </div>
        <div class="ex-card kpi-card k-green">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val" id="summaryPremium">$0</div>
            <div class="k-lbl">Premium</div>
        </div>
        <div class="ex-card kpi-card k-blue">
            <i class="bx bx-shield k-icon"></i>
            <div class="k-val" id="summaryCoverage">$0</div>
            <div class="k-lbl">Coverage</div>
        </div>
        <div class="ex-card kpi-card k-purple">
            <i class="bx bx-trending-up k-icon"></i>
            <div class="k-val" id="summaryCommission">$0</div>
            <div class="k-lbl">Commission</div>
        </div>
        <div class="ex-card kpi-card k-teal">
            <i class="bx bx-wallet k-icon"></i>
            <div class="k-val" id="summaryRevenue">$0</div>
            <div class="k-lbl">Revenue</div>
        </div>
    </div>

    
    <div class="rp-print-area" id="printArea">
        <div class="rp-print-header" style="display:none">
            <h4>Taurus MIS — Report</h4>
            <p id="printSubtitle">Generated on <?php echo e(now()->format('M d, Y h:i A')); ?></p>
        </div>
        <div class="rp-print-kpis" style="display:none" id="printKpis">
            <div><strong id="printTotal">0</strong>Records</div>
            <div><strong id="printPremium">$0</strong>Premium</div>
            <div><strong id="printCoverage">$0</strong>Coverage</div>
            <div><strong id="printCommission">$0</strong>Commission</div>
            <div><strong id="printRevenue">$0</strong>Revenue</div>
        </div>
        <div class="ex-card sec-card rp-results" id="resultsCard" style="position:relative">
            <div id="resultsContent">
                <div class="rp-empty">
                    <i class="bx bx-bar-chart"></i>
                    <h6>Select filters and generate a report</h6>
                    <p>Use the filters above to customize your report</p>
                </div>
            </div>
        </div>
        <div class="rp-print-footer" style="display:none">
            Taurus Technologies — Confidential Report — Printed <?php echo e(now()->format('M d, Y')); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ═══════════════════════════════════════════════════════════════════
    //  Existing Report Logic
    // ═══════════════════════════════════════════════════════════════════
    const form = document.getElementById('reportForm');
    const resultsContent = document.getElementById('resultsContent');
    const resultsCard = document.getElementById('resultsCard');
    const summaryRow = document.getElementById('summaryRow');
    const reportTypeInput = document.getElementById('reportType');

    // Override native form.submit() so shared sl-filter-assets dropdowns
    // and date pickers trigger AJAX instead of a full page reload.
    form.submit = function() { loadReport(); };

    // Report type pills
    document.querySelectorAll('.pipe-pill[data-type]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pipe-pill[data-type]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            reportTypeInput.value = this.dataset.type;
        });
    });

    // Toggle filters
    document.getElementById('filterToggle').addEventListener('click', function() {
        const body = document.getElementById('filterBody');
        const icon = document.getElementById('filterToggleIcon');
        const isVisible = body.style.display !== 'none';
        body.style.display = isVisible ? 'none' : 'block';
        icon.style.transform = isVisible ? 'rotate(-90deg)' : '';
    });

    // Generate report
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        loadReport();
    });

    // Reset
    document.getElementById('resetFilters').addEventListener('click', function() {
        form.reset();
        document.querySelectorAll('.pipe-pill[data-type]').forEach(b => b.classList.remove('active'));
        document.querySelector('.pipe-pill[data-type="sales"]').classList.add('active');
        reportTypeInput.value = 'sales';
        summaryRow.style.display = 'none';
        document.getElementById('printReport').style.display = 'none';
        // Reset custom dropdowns
        document.querySelectorAll('.sl-cdd-trigger').forEach(t => {
            const firstOpt = t.closest('.sl-cdd')?.querySelector('.sl-cdd-opt');
            if (firstOpt) { t.textContent = firstOpt.textContent; }
        });
        resultsContent.innerHTML = '<div class="rp-empty"><i class="bx bx-bar-chart"></i><h6>Select filters and generate a report</h6><p>Use the filters above</p></div>';
    });

    // Export CSV
    document.getElementById('exportCsv').addEventListener('click', function() {
        const params = new URLSearchParams(new FormData(form));
        window.location.href = '<?php echo e(route("settings.reports.export")); ?>?' + params.toString();
    });

    // Pagination
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#resultsContent .pagination a');
        if (link) { e.preventDefault(); loadReport(link.href); }
    });

    function loadReport(url) {
        url = url || '<?php echo e(route("settings.reports.generate")); ?>';
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = '<div class="spinner-border text-warning"><span class="visually-hidden">Loading...</span></div>';
        resultsCard.appendChild(loader);

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const urlObj = new URL(url, window.location.origin);
        for (const [key, value] of params.entries()) {
            if (!urlObj.searchParams.has(key)) urlObj.searchParams.set(key, value);
        }

        fetch(urlObj.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            resultsContent.innerHTML = data.html;
            if (data.summary) {
                const fmt = (v) => Number(v).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                const fmtInt = (v) => Number(v).toLocaleString();
                summaryRow.style.display = 'flex';
                document.getElementById('summaryTotal').textContent = fmtInt(data.summary.total_records);
                document.getElementById('summaryPremium').textContent = '$' + fmt(data.summary.total_premium);
                document.getElementById('summaryCoverage').textContent = '$' + fmt(data.summary.total_coverage);
                document.getElementById('summaryCommission').textContent = '$' + fmt(data.summary.total_commission);
                document.getElementById('summaryRevenue').textContent = '$' + fmt(data.summary.total_revenue);

                // Sync print KPIs
                document.getElementById('printTotal').textContent = fmtInt(data.summary.total_records);
                document.getElementById('printPremium').textContent = '$' + fmt(data.summary.total_premium);
                document.getElementById('printCoverage').textContent = '$' + fmt(data.summary.total_coverage);
                document.getElementById('printCommission').textContent = '$' + fmt(data.summary.total_commission);
                document.getElementById('printRevenue').textContent = '$' + fmt(data.summary.total_revenue);
            }

            // Show print button when results exist
            const printBtn = document.getElementById('printReport');
            if (data.summary && data.summary.total_records > 0) {
                printBtn.style.display = '';
            } else {
                printBtn.style.display = 'none';
            }
        })
        .catch(err => {
            resultsContent.innerHTML = '<div class="rp-empty"><i class="bx bx-error-circle"></i><h6>Error loading report</h6><p>' + (err.message || 'Something went wrong') + '</p></div>';
        })
        .finally(() => {
            const o = resultsCard.querySelector('.loading-overlay');
            if (o) o.remove();
        });
    }

    // Print preview
    document.getElementById('printReport').addEventListener('click', function() {
        // Update print subtitle with the current active report type
        const activeType = document.querySelector('.pipe-pill.active');
        const typeLabel = activeType ? activeType.textContent.trim() : 'All Records';
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' });
        document.getElementById('printSubtitle').textContent = typeLabel + ' Report — Generated ' + dateStr;
        window.print();
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/index.blade.php ENDPATH**/ ?>