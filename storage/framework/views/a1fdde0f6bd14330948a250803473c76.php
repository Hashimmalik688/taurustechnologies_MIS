<?php use \App\Support\Statuses; ?>

<?php $__env->startSection('title', 'Retention Management'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ── Retention Management ── */
.sl-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;}
.sl-page-title{font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:.4rem;}
.sl-page-title i{color:#d4af37;font-size:1.2rem;}
.sl-kpi-row{display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1rem;}
.sl-kpi-pill{display:flex;align-items:center;gap:.5rem;padding:.5rem .85rem;border-radius:22px;border:1px solid rgba(0,0,0,.06);background:rgba(255,255,255,.9);backdrop-filter:blur(12px);}
.sl-kpi-pill .kpi-icon{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:.9rem;}
.sl-kpi-pill .kpi-label{font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;line-height:1.1;}
.sl-kpi-pill .kpi-value{font-size:1.1rem;font-weight:800;line-height:1;}
.sl-card{background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border:1px solid rgba(0,0,0,.06);border-radius:16px;overflow:hidden;}
.sl-filter-pills{display:flex;align-items:center;gap:.4rem;padding:.6rem 1rem;border-bottom:1px solid rgba(0,0,0,.05);background:rgba(248,250,252,.6);flex-wrap:wrap;}
.sl-pill-select,.sl-pill-date{font-size:.72rem;font-weight:600;padding:.32rem .55rem;border-radius:22px!important;border:1px solid rgba(0,0,0,.08)!important;background:#fff;color:#475569;cursor:pointer;outline:none;transition:border-color .15s;}
.sl-pill-select{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .5rem center;padding-right:1.5rem;max-width:180px;}
.sl-pill-date{min-width:100px;max-width:130px;color-scheme:light;}
.sl-pill-select:focus,.sl-pill-date:focus{border-color:#d4af37!important;box-shadow:0 0 0 2px rgba(212,175,55,.12);}
.sl-pill-label{font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;}
.sl-pill-clear{font-size:.68rem;font-weight:600;color:#ef4444;text-decoration:none;padding:.25rem .5rem;border-radius:22px;border:1px solid rgba(239,68,68,.2);display:inline-flex;align-items:center;gap:2px;transition:all .15s;}
.sl-pill-clear:hover{background:rgba(239,68,68,.08);color:#dc2626;}
.sl-search-wrap{position:relative;display:flex;align-items:center;}
.sl-search-icon{position:absolute;left:.6rem;color:#94a3b8;font-size:.9rem;pointer-events:none;}
.sl-search-input{padding:.42rem .65rem .42rem 2rem;font-size:.78rem;border:1px solid rgba(0,0,0,.1);border-radius:22px;background:#fff;width:240px;outline:none;transition:border-color .15s;}
.sl-search-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);}
.sl-tabs{display:flex;gap:2px;padding:.5rem 1rem;border-bottom:1px solid rgba(0,0,0,.05);background:rgba(248,250,252,.35);flex-wrap:wrap;}
.sl-tab{display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .85rem;border-radius:22px;font-size:.72rem;font-weight:700;color:#64748b;background:transparent;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.sl-tab:hover{color:#d4af37;background:rgba(212,175,55,.06);}
.sl-tab.active{background:linear-gradient(135deg,#d4af37,#b8941f);color:#0f172a;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25);}
.sl-tab .badge{font-size:.6rem;padding:.15rem .4rem;border-radius:10px;font-weight:700;}
.sl-tab.active .badge{background:rgba(0,0,0,.15)!important;color:#fff!important;}
.sl-tbl-wrap{overflow-x:auto;overflow-y:auto;max-height:580px;scrollbar-width:thin;scrollbar-color:#d4af37 transparent;}
.sl-tbl-wrap::-webkit-scrollbar{width:5px;height:5px;}
.sl-tbl-wrap::-webkit-scrollbar-track{background:transparent;}
.sl-tbl-wrap::-webkit-scrollbar-thumb{background:#d4af37;border-radius:3px;}
.sl-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.78rem;}
.sl-tbl thead th{background:linear-gradient(180deg,#f8fafc 0%,#f1f5f9 100%);font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;padding:.45rem .55rem;border-bottom:1px solid rgba(212,175,55,.18);white-space:nowrap;position:sticky;top:0;z-index:10;}
.sl-tbl tbody td{padding:.38rem .55rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#334155;transition:background .12s;}
.sl-tbl tbody tr:hover td{background:rgba(212,175,55,.045);}
.sl-tbl tbody tr:nth-child(even) td{background:rgba(248,250,252,.45);}
.sl-tbl tbody tr:nth-child(even):hover td{background:rgba(212,175,55,.045);}
.sl-empty-row td{text-align:center;padding:2rem 0!important;color:#94a3b8;}
/* Dark themes */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi-pill{background:rgba(30,41,59,.65);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card{background:rgba(30,41,59,.65);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills{background:rgba(15,23,42,.4);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date{background:rgba(30,41,59,.8)!important;border-color:rgba(255,255,255,.1)!important;color:#cbd5e1;color-scheme:dark;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tabs{background:rgba(15,23,42,.3);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab{color:#94a3b8;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab:hover{color:#d4af37;background:rgba(212,175,55,.08);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab.active{color:#0f172a;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th{background:linear-gradient(180deg,rgba(15,23,42,.95),rgba(15,23,42,.9));color:#94a3b8;border-color:rgba(212,175,55,.12);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td{color:#cbd5e1;border-color:rgba(255,255,255,.04);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input{background:rgba(30,41,59,.8);border-color:rgba(255,255,255,.1);color:#e2e8f0;}
/* Recall button */
.a-btn{display:inline-flex;align-items:center;gap:2px;font-size:.65rem;font-weight:600;padding:.2rem .45rem;border-radius:.3rem;border:1px solid;cursor:pointer;text-decoration:none;transition:all .12s;}
.a-recall{background:rgba(139,92,246,.08);color:#7c3aed;border-color:rgba(139,92,246,.25);}.a-recall:hover{background:rgba(139,92,246,.18);}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 py-3" style="max-width:1600px;">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.82rem;">
            <i class="mdi mdi-check-all me-1"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="sl-topbar">
        <h5 class="sl-page-title"><i class="mdi mdi-shield-check-outline"></i> Retention Management</h5>
        <div class="sl-search-wrap">
            <i class="bx bx-search sl-search-icon"></i>
            <input type="text" id="retSearch" class="sl-search-input" placeholder="Search name, phone, carrier…" value="<?php echo e($search ?? ''); ?>">
        </div>
    </div>

    
    <div class="sl-kpi-row">
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-warning-subtle text-warning"><i class="bx bx-x-circle"></i></div>
            <div>
                <div class="kpi-label">Not Issued</div>
                <div class="kpi-value text-warning"><?php echo e($not_issued_count); ?></div>
            </div>
        </div>
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-danger-subtle text-danger"><i class="bx bx-credit-card-alt"></i></div>
            <div>
                <div class="kpi-label">Not Paid / FDFP</div>
                <div class="kpi-value text-danger"><?php echo e($not_paid_count); ?></div>
            </div>
        </div>
    </div>

    
    <div class="sl-card">

        
        <form method="GET" action="<?php echo e(route('retention.index')); ?>" id="retFilterForm" class="sl-filter-pills">
            <input type="hidden" name="search" id="retSearchHidden" value="<?php echo e($search ?? ''); ?>">
            <select name="month" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Months</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($m=1;$m<=12;$m++): ?>
                    <option value="<?php echo e($m); ?>" <?php echo e(($month ?? '') == $m ? 'selected' : ''); ?>>
                        <?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?>

                    </option>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <select name="year" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Years</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($y=now()->year;$y>=now()->year-5;$y--): ?>
                    <option value="<?php echo e($y); ?>" <?php echo e(($year ?? '') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" class="sl-pill-date" value="<?php echo e($date_from ?? ''); ?>" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" class="sl-pill-date" value="<?php echo e($date_to ?? ''); ?>" onchange="this.form.submit()">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','month','year','date_from','date_to'])): ?>
                <a href="<?php echo e(route('retention.index')); ?>" class="sl-pill-clear"><i class="bx bx-x"></i> Clear</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>

        
        <div class="sl-tabs" role="tablist">
            <a class="sl-tab active" data-bs-toggle="tab" href="#tab-not-issued" role="tab">
                <i class="bx bx-x-circle"></i> Not Issued
                <span class="badge bg-warning text-dark"><?php echo e($not_issued_count); ?></span>
            </a>
            <a class="sl-tab" data-bs-toggle="tab" href="#tab-not-paid" role="tab">
                <i class="bx bx-error-circle"></i> Not Paid / FDFP
                <span class="badge bg-danger"><?php echo e($not_paid_count); ?></span>
            </a>
        </div>

        
        <div class="tab-content">

            
            <div class="tab-pane show active" id="tab-not-issued" role="tabpanel">
                <div class="sl-tbl-wrap">
                    <table class="sl-tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Phone</th>
                                <th>Carrier</th>
                                <th>Closer</th>
                                <th>Sale Date</th>
                                <th>Not Issued Reason</th>
                                <th>Marked By</th>
                                <th>Marked At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $not_issued_leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td style="color:var(--bs-surface-400);"><?php echo e($loop->iteration); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('leads.show', $lead->id)); ?>" style="font-weight:600;font-size:.73rem;color:var(--bs-body-color);text-decoration:none;" target="_blank">
                                            <?php echo e($lead->cn_name ?? '—'); ?>

                                        </a>
                                    </td>
                                    <td style="font-size:.7rem;"><?php echo e($lead->phone_number ?? '—'); ?></td>
                                    <td><?php echo e($lead->carrier_name ?? '—'); ?></td>
                                    <td><?php echo e($lead->closer_name ?? '—'); ?></td>
                                    <td style="font-size:.7rem;"><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—'); ?></td>
                                    <td>
                                        <span class="badge bg-warning text-dark" style="font-size:.65rem;">
                                            <?php echo e(\App\Support\Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_issued_disposition] ?? $lead->not_issued_disposition ?? '—'); ?>

                                        </span>
                                    </td>
                                    <td style="font-size:.71rem;"><?php echo e($lead->notIssuedBy->name ?? '—'); ?></td>
                                    <td style="font-size:.7rem;"><?php echo e($lead->not_issued_at ? $lead->not_issued_at->format('M d, Y') : '—'); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->not_issued_resolved_at): ?>
                                            <span class="badge bg-success" style="font-size:.62rem;">Resolved</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger" style="font-size:.62rem;">Pending</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="<?php echo e(route('leads.show', $lead->id)); ?>" class="a-btn" style="font-size:.63rem;background:rgba(85,110,230,.1);color:#556ee6;border-color:rgba(85,110,230,.25);text-decoration:none;" target="_blank">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lead->recall_requested_at): ?>
                                                <button class="a-btn a-recall btn-recall-closer" data-id="<?php echo e($lead->id); ?>" data-name="<?php echo e($lead->cn_name); ?>" style="font-size:.63rem;">
                                                    <i class="bx bx-undo"></i> Recall
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-secondary" style="font-size:.58rem;">Recalled</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="sl-empty-row">
                                    <td colspan="11">
                                        <i class="bx bx-inbox" style="font-size:1.8rem;display:block;margin-bottom:.3rem;opacity:.4;"></i>
                                        No Not Issued leads for the selected period.
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($not_issued_leads->hasPages()): ?>
                    <div class="px-3 py-2"><?php echo e($not_issued_leads->withQueryString()->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="tab-pane" id="tab-not-paid" role="tabpanel">
                <div class="sl-tbl-wrap">
                    <table class="sl-tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Phone</th>
                                <th>Carrier</th>
                                <th>Closer</th>
                                <th>Sale Date</th>
                                <th>FDFP Type</th>
                                <th>Marked By</th>
                                <th>Marked At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $not_paid_leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td style="color:var(--bs-surface-400);"><?php echo e($loop->iteration); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('leads.show', $lead->id)); ?>" style="font-weight:600;font-size:.73rem;color:var(--bs-body-color);text-decoration:none;" target="_blank">
                                            <?php echo e($lead->cn_name ?? '—'); ?>

                                        </a>
                                    </td>
                                    <td style="font-size:.7rem;"><?php echo e($lead->phone_number ?? '—'); ?></td>
                                    <td><?php echo e($lead->carrier_name ?? '—'); ?></td>
                                    <td><?php echo e($lead->closer_name ?? '—'); ?></td>
                                    <td style="font-size:.7rem;"><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—'); ?></td>
                                    <td>
                                        <span class="badge bg-danger" style="font-size:.65rem;">
                                            <?php echo e(\App\Support\Statuses::FDFP_TYPES[$lead->not_paid_fdfp_type] ?? $lead->not_paid_fdfp_type ?? '—'); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->not_paid_fdfp_type === 'manual_action' && $lead->not_paid_manual_disposition): ?>
                                            <div style="font-size:.62rem;color:#94a3b8;margin-top:1px;">
                                                → <?php echo e(\App\Support\Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_paid_manual_disposition] ?? $lead->not_paid_manual_disposition); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td style="font-size:.71rem;"><?php echo e($lead->notPaidBy->name ?? '—'); ?></td>
                                    <td style="font-size:.7rem;"><?php echo e($lead->not_paid_at ? $lead->not_paid_at->format('M d, Y') : '—'); ?></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="<?php echo e(route('leads.show', $lead->id)); ?>" class="a-btn" style="font-size:.63rem;background:rgba(85,110,230,.1);color:#556ee6;border-color:rgba(85,110,230,.25);text-decoration:none;" target="_blank">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lead->recall_requested_at): ?>
                                                <button class="a-btn a-recall btn-recall-closer" data-id="<?php echo e($lead->id); ?>" data-name="<?php echo e($lead->cn_name); ?>" style="font-size:.63rem;">
                                                    <i class="bx bx-undo"></i> Recall
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-secondary" style="font-size:.58rem;">Recalled</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="sl-empty-row">
                                    <td colspan="10">
                                        <i class="bx bx-inbox" style="font-size:1.8rem;display:block;margin-bottom:.3rem;opacity:.4;"></i>
                                        No Not Paid / FDFP leads for the selected period.
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($not_paid_leads->hasPages()): ?>
                    <div class="px-3 py-2"><?php echo e($not_paid_leads->withQueryString()->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>

</div>


<div class="modal fade" id="recallModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(139,92,246,.04);border-bottom:1px solid rgba(139,92,246,.1);">
                <h6 class="modal-title mb-0" style="font-size:.85rem;color:#7c3aed;">
                    <i class="bx bx-undo me-1"></i> Send Back to Closer
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="recall-lead-name"></strong>
                </p>
                <p class="mb-3" style="font-size:.7rem;color:#7c3aed;background:rgba(139,92,246,.04);border:1px solid rgba(139,92,246,.12);border-radius:.4rem;padding:.5rem .65rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    This will send the lead back to the closer for re-dial. The closer will see the recall note on their dashboard.
                </p>
                <div class="mb-2">
                    <label class="form-label">Comment / Instructions <span class="text-danger">*</span></label>
                    <textarea id="recall-note" class="form-control" rows="3" placeholder="Why is this being sent back?" style="resize:none;"></textarea>
                    <div id="recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a comment.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background:var(--bs-surface-100);color:var(--bs-surface-500);border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-sm" id="recall-confirm-btn" style="background:rgba(139,92,246,.9);color:#fff;border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">
                    <i class="bx bx-undo me-1"></i> Send Back
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
(function () {
    // Search with debounce
    const searchInput  = document.getElementById('retSearch');
    const searchHidden = document.getElementById('retSearchHidden');
    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const val = this.value;
            searchTimer = setTimeout(function () {
                searchHidden.value = val;
                document.getElementById('retFilterForm').submit();
            }, 500);
        });
    }

    // Preserve active tab on reload via URL hash
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector('.sl-tab[href="' + hash + '"]');
        if (tab) {
            document.querySelectorAll('.sl-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => { p.classList.remove('show', 'active'); });
            tab.classList.add('active');
            const pane = document.querySelector(hash);
            if (pane) pane.classList.add('show', 'active');
        }
    }
    document.querySelectorAll('.sl-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            history.replaceState(null, '', this.getAttribute('href'));
        });
    });

    // ==== Recall / Send Back ====
    var recallLeadId = null;
    const recallModalEl = document.getElementById('recallModal');
    let recallModalInstance = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-recall-closer').forEach(btn => {
        btn.addEventListener('click', function() {
            recallLeadId = this.dataset.id;
            document.getElementById('recall-lead-name').textContent = this.dataset.name;
            document.getElementById('recall-note').value = '';
            document.getElementById('recall-note-error').style.display = 'none';

            if (recallModalInstance) recallModalInstance.dispose();
            recallModalInstance = new bootstrap.Modal(recallModalEl);
            recallModalInstance.show();
        });
    });

    // Cleanup recall modal on hide
    recallModalEl.addEventListener('hidden.bs.modal', function() {
        if (recallModalInstance) {
            recallModalInstance.dispose();
            recallModalInstance = null;
        }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    document.getElementById('recall-confirm-btn').addEventListener('click', function() {
        var note = document.getElementById('recall-note').value.trim();
        if (!note) { document.getElementById('recall-note-error').style.display = 'block'; return; }
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Sending…';
        fetch('/retention/' + recallLeadId + '/recall-to-closer', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ recall_note: note })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back';
            if (data.success) {
                if (recallModalInstance) recallModalInstance.hide();
                location.reload();
            }
            else alert(data.message || 'Error.');
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back'; alert('Error: ' + err.message); });
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/retention/index.blade.php ENDPATH**/ ?>