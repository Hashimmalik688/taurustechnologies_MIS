<?php use \App\Support\Statuses; ?>

<?php $__env->startSection('title', 'Retention Management'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* ── Retention Management ── */
.sl-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem;flex-wrap:wrap;gap:.6rem;}
.sl-page-title{font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:.4rem;}
.sl-page-title i{color:#d4af37;font-size:1.2rem;}

/* KPI pills row */
.ret-kpi-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.85rem;}
.ret-kpi-pill{display:flex;align-items:center;gap:.45rem;padding:.45rem .8rem;border-radius:20px;border:1px solid rgba(0,0,0,.06);background:rgba(255,255,255,.9);backdrop-filter:blur(10px);cursor:default;transition:box-shadow .15s;}
.ret-kpi-pill:hover{box-shadow:0 2px 10px rgba(0,0,0,.07);}
.ret-kpi-pill .rk-icon{width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:.82rem;}
.ret-kpi-pill .rk-lbl{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;line-height:1.1;}
.ret-kpi-pill .rk-val{font-size:1rem;font-weight:800;line-height:1;}
.ret-kpi-pill.k-pending .rk-icon{background:rgba(148,163,184,.15);color:#64748b;}
.ret-kpi-pill.k-pending .rk-val{color:#64748b;}
.ret-kpi-pill.k-retained .rk-icon{background:rgba(52,195,143,.15);color:#1a8754;}
.ret-kpi-pill.k-retained .rk-val{color:#1a8754;}
.ret-kpi-pill.k-resold .rk-icon{background:rgba(85,110,230,.12);color:#556ee6;}
.ret-kpi-pill.k-resold .rk-val{color:#556ee6;}
.ret-kpi-pill.k-rewrite .rk-icon{background:rgba(241,180,76,.12);color:#b87a14;}
.ret-kpi-pill.k-rewrite .rk-val{color:#b87a14;}
.ret-kpi-pill.k-recalled .rk-icon{background:rgba(139,92,246,.12);color:#7c3aed;}
.ret-kpi-pill.k-recalled .rk-val{color:#7c3aed;}
.ret-kpi-pill.k-cancelled .rk-icon{background:rgba(244,106,106,.12);color:#c84646;}
.ret-kpi-pill.k-cancelled .rk-val{color:#c84646;}

/* Card & filter */
.sl-card{background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border:1px solid rgba(0,0,0,.06);border-radius:16px;overflow:hidden;}
.sl-filter-pills{display:flex;align-items:center;gap:.4rem;padding:.6rem 1rem;border-bottom:1px solid rgba(0,0,0,.05);background:rgba(248,250,252,.6);flex-wrap:wrap;}
.sl-pill-select,.sl-pill-date{font-size:.72rem;font-weight:600;padding:.32rem .55rem;border-radius:22px!important;border:1px solid rgba(0,0,0,.08)!important;background:#fff;color:#475569;cursor:pointer;outline:none;transition:border-color .15s;}
.sl-pill-select{-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .5rem center;padding-right:1.4rem;max-width:160px;}
.sl-pill-date{min-width:100px;max-width:125px;color-scheme:light;}
.sl-pill-select:focus,.sl-pill-date:focus{border-color:#d4af37!important;}
.sl-pill-label{font-size:.63rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;}
.sl-pill-clear{font-size:.68rem;font-weight:600;color:#ef4444;text-decoration:none;padding:.25rem .5rem;border-radius:22px;border:1px solid rgba(239,68,68,.2);display:inline-flex;align-items:center;gap:2px;}
.sl-pill-clear:hover{background:rgba(239,68,68,.08);}
.sl-search-wrap{position:relative;display:flex;align-items:center;}
.sl-search-icon{position:absolute;left:.6rem;color:#94a3b8;font-size:.9rem;pointer-events:none;}
.sl-search-input{padding:.4rem .65rem .4rem 2rem;font-size:.75rem;border:1px solid rgba(0,0,0,.1);border-radius:22px;background:#fff;width:210px;outline:none;}
.sl-search-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);}

/* Tabs */
.sl-tabs{display:flex;gap:2px;padding:.5rem 1rem;border-bottom:1px solid rgba(0,0,0,.05);background:rgba(248,250,252,.35);flex-wrap:wrap;align-items:center;justify-content:space-between;}
.sl-tab{display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .8rem;border-radius:22px;font-size:.72rem;font-weight:700;color:#64748b;background:transparent;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.sl-tab:hover{color:#d4af37;background:rgba(212,175,55,.06);}
.sl-tab.active{background:linear-gradient(135deg,#d4af37,#b8941f);color:#0f172a;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25);}
.sl-tab .badge{font-size:.58rem;padding:.12rem .38rem;border-radius:10px;font-weight:700;}
.sl-tab.active .badge{background:rgba(0,0,0,.15)!important;color:#fff!important;}

/* Table */
.sl-tbl-wrap{overflow-x:auto;overflow-y:auto;max-height:560px;scrollbar-width:thin;scrollbar-color:#d4af37 transparent;}
.sl-tbl-wrap::-webkit-scrollbar{width:4px;height:4px;}
.sl-tbl-wrap::-webkit-scrollbar-thumb{background:#d4af37;border-radius:3px;}
.sl-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.77rem;}
.sl-tbl thead th{background:linear-gradient(180deg,#f8fafc 0%,#f1f5f9 100%);font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;padding:.42rem .5rem;border-bottom:1px solid rgba(212,175,55,.18);white-space:nowrap;position:sticky;top:0;z-index:10;}
.sl-tbl tbody td{padding:.36rem .5rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#334155;}
.sl-tbl tbody tr:hover td{background:rgba(212,175,55,.04);}
.sl-empty-row td{text-align:center;padding:2rem 0!important;color:#94a3b8;}

/* Action buttons */
.a-btn{display:inline-flex;align-items:center;gap:2px;font-size:.63rem;font-weight:600;padding:.18rem .42rem;border-radius:.3rem;border:1px solid;cursor:pointer;text-decoration:none;transition:all .12s;white-space:nowrap;}
.a-view{background:rgba(85,110,230,.1);color:#556ee6;border-color:rgba(85,110,230,.25);}.a-view:hover{background:rgba(85,110,230,.2);}
.a-recall{background:rgba(139,92,246,.08);color:#7c3aed;border-color:rgba(139,92,246,.25);}.a-recall:hover{background:rgba(139,92,246,.18);}

/* Retention disposition badge (in table rows) */
.ret-status-badge{display:inline-flex;align-items:center;gap:.25rem;font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;text-transform:uppercase;letter-spacing:.3px;}
.rdb-pending{background:rgba(148,163,184,.12);color:#64748b;border:1px solid rgba(148,163,184,.25);}
.rdb-retained{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.2);}
.rdb-resold{background:rgba(85,110,230,.12);color:#556ee6;border:1px solid rgba(85,110,230,.2);}
.rdb-rewrite{background:rgba(241,180,76,.12);color:#b87a14;border:1px solid rgba(241,180,76,.25);}
.rdb-recalled_to_closer{background:rgba(139,92,246,.12);color:#7c3aed;border:1px solid rgba(139,92,246,.2);}
.rdb-cancelled{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.2);}

/* Disposition buttons in modal footer */
.ret-disp-btn{display:inline-flex;align-items:center;gap:.25rem;font-size:.67rem;font-weight:700;padding:.28rem .6rem;border-radius:1rem;border:2px solid;cursor:pointer;transition:all .15s;white-space:nowrap;background:transparent;}
.ret-disp-btn.disp-pending{color:#64748b;border-color:rgba(148,163,184,.4);}
.ret-disp-btn.disp-pending.active,.ret-disp-btn.disp-pending:hover{background:rgba(148,163,184,.15);border-color:#94a3b8;}
.ret-disp-btn.disp-retained{color:#1a8754;border-color:rgba(52,195,143,.4);}
.ret-disp-btn.disp-retained.active,.ret-disp-btn.disp-retained:hover{background:rgba(52,195,143,.15);border-color:#34c38f;}
.ret-disp-btn.disp-resold{color:#556ee6;border-color:rgba(85,110,230,.4);}
.ret-disp-btn.disp-resold.active,.ret-disp-btn.disp-resold:hover{background:rgba(85,110,230,.15);border-color:#556ee6;}
.ret-disp-btn.disp-rewrite{color:#b87a14;border-color:rgba(241,180,76,.4);}
.ret-disp-btn.disp-rewrite.active,.ret-disp-btn.disp-rewrite:hover{background:rgba(241,180,76,.15);border-color:#f1b44c;}
.ret-disp-btn.disp-recalled_to_closer{color:#7c3aed;border-color:rgba(139,92,246,.4);}
.ret-disp-btn.disp-recalled_to_closer.active,.ret-disp-btn.disp-recalled_to_closer:hover{background:rgba(139,92,246,.15);border-color:#8b5cf6;}
.ret-disp-btn.disp-cancelled{color:#c84646;border-color:rgba(244,106,106,.4);}
.ret-disp-btn.disp-cancelled.active,.ret-disp-btn.disp-cancelled:hover{background:rgba(244,106,106,.15);border-color:#f46a6a;}

/* Field highlight badge (cross-page updated indicator) */
.fh-badge{display:inline-flex;align-items:center;gap:.2rem;font-size:.58rem;font-weight:600;padding:.08rem .32rem;border-radius:8px;background:rgba(245,158,11,.12);color:#b45309;border:1px solid rgba(245,158,11,.25);white-space:nowrap;margin-left:.3rem;cursor:default;vertical-align:middle;}

/* Editable inputs inside modal detail table */
.ret-edit-input{font-size:.73rem!important;padding:.2rem .38rem!important;border-radius:.3rem!important;border:1px solid rgba(0,0,0,.12)!important;width:100%;min-width:100px;background:var(--bs-body-bg,#fff);color:var(--bs-body-color,#334155);}
.ret-edit-input:focus{border-color:#d4af37!important;box-shadow:0 0 0 2px rgba(212,175,55,.12)!important;outline:none!important;}
textarea.ret-edit-input{resize:vertical;min-height:54px;}
/* CURRENT value display above each field (mirrors calling form style) */
.ph-cur{display:flex;align-items:center;gap:.35rem;margin-bottom:.3rem;font-size:.72rem}
.ph-cur-tag{background:linear-gradient(135deg,#d4af37,#c5a028);color:#fff;font-size:.6rem;padding:.15rem .4rem;border-radius:6px;font-weight:700;letter-spacing:.3px}
.ph-cur-val{font-weight:700;color:var(--bs-heading-color);font-size:.78rem}

/* Save status feedback */
#dm-save-feedback{display:none;font-size:.68rem;font-weight:600;padding:.22rem .55rem;border-radius:.3rem;}

/* Recall note inline in modal footer */
#dm-recall-note-wrap{display:none;margin-top:.4rem;width:100%;}

/* Detail modal */
.modal-header-ret{background:linear-gradient(135deg,var(--bs-card-bg) 0%,rgba(212,175,55,.08) 100%);border-bottom:1px solid rgba(212,175,55,.15);}
.modal-header-ret .modal-title{font-size:.85rem;font-weight:700;}
.detail-tbl td{padding:.28rem .42rem;font-size:.77rem;border-bottom:1px solid rgba(0,0,0,.04);}
.detail-tbl td:first-child{font-weight:600;color:var(--bs-surface-500);width:38%;white-space:nowrap;}
.sec-hdr-mini{font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.45rem;}

/* Toggle disposed */
.disposed-toggle{display:inline-flex;align-items:center;gap:.3rem;font-size:.68rem;font-weight:600;color:var(--bs-surface-500);padding:.25rem .6rem;border-radius:22px;border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);cursor:pointer;text-decoration:none;transition:all .15s;}
.disposed-toggle:hover,.disposed-toggle.active{border-color:rgba(212,175,55,.3);color:#b89730;background:rgba(212,175,55,.06);}
.disposed-toggle input{width:14px;height:14px;accent-color:#d4af37;cursor:pointer;}

/* Dark themes */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ret-kpi-pill{background:rgba(30,41,59,.65);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card{background:rgba(30,41,59,.65);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills{background:rgba(15,23,42,.4);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date{background:rgba(30,41,59,.8)!important;border-color:rgba(255,255,255,.1)!important;color:#cbd5e1;color-scheme:dark;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tabs{background:rgba(15,23,42,.3);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab{color:#94a3b8;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab.active{color:#0f172a;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th{background:linear-gradient(180deg,rgba(15,23,42,.95),rgba(15,23,42,.9));color:#94a3b8;border-color:rgba(212,175,55,.12);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td{color:#cbd5e1;border-color:rgba(255,255,255,.04);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input{background:rgba(30,41,59,.8);border-color:rgba(255,255,255,.1);color:#e2e8f0;}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 py-3" style="max-width:1600px;">

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="font-size:.82rem;">
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


<div class="ret-kpi-row">
    <div class="ret-kpi-pill k-pending">
        <div class="rk-icon"><i class="bx bx-time-five"></i></div>
        <div><div class="rk-lbl">Pending</div><div class="rk-val"><?php echo e($kpi['pending'] ?? 0); ?></div></div>
    </div>
    <div class="ret-kpi-pill k-retained">
        <div class="rk-icon"><i class="bx bx-check-shield"></i></div>
        <div><div class="rk-lbl">Retained</div><div class="rk-val"><?php echo e($kpi['retained'] ?? 0); ?></div></div>
    </div>
    <div class="ret-kpi-pill k-resold">
        <div class="rk-icon"><i class="bx bx-store"></i></div>
        <div><div class="rk-lbl">Resold</div><div class="rk-val"><?php echo e($kpi['resold'] ?? 0); ?></div></div>
    </div>
    <div class="ret-kpi-pill k-rewrite">
        <div class="rk-icon"><i class="bx bx-edit-alt"></i></div>
        <div><div class="rk-lbl">Rewrite</div><div class="rk-val"><?php echo e($kpi['rewrite'] ?? 0); ?></div></div>
    </div>
    <div class="ret-kpi-pill k-recalled">
        <div class="rk-icon"><i class="bx bx-undo"></i></div>
        <div><div class="rk-lbl">Recalled</div><div class="rk-val"><?php echo e($kpi['recalled_to_closer'] ?? 0); ?></div></div>
    </div>
    <div class="ret-kpi-pill k-cancelled">
        <div class="rk-icon"><i class="bx bx-x-circle"></i></div>
        <div><div class="rk-lbl">Cancelled</div><div class="rk-val"><?php echo e($kpi['cancelled'] ?? 0); ?></div></div>
    </div>
</div>


<div class="sl-card">

    
    <form method="GET" action="<?php echo e(route('retention.index')); ?>" id="retFilterForm" class="sl-filter-pills">
        <input type="hidden" name="search" id="retSearchHidden" value="<?php echo e($search ?? ''); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($disposed): ?><input type="hidden" name="disposed" value="1"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <select name="month" class="sl-pill-select" onchange="this.form.submit()">
            <option value="">All Months</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($m=1;$m<=12;$m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e(($month ?? '') == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?></option>
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
        <div style="display:flex;gap:2px;flex-wrap:wrap;">
            <a class="sl-tab active" data-bs-toggle="tab" href="#tab-not-issued" role="tab">
                <i class="bx bx-x-circle"></i> Not Issued
                <span class="badge bg-warning text-dark"><?php echo e($not_issued_count); ?></span>
            </a>
            <a class="sl-tab" data-bs-toggle="tab" href="#tab-not-paid" role="tab">
                <i class="bx bx-error-circle"></i> Not Paid / FDFP
                <span class="badge bg-danger"><?php echo e($not_paid_count); ?></span>
            </a>
        </div>
        
        <a href="<?php echo e(route('retention.index', array_merge(request()->except('disposed'), $disposed ? [] : ['disposed' => 1]))); ?>"
           class="disposed-toggle <?php echo e($disposed ? 'active' : ''); ?>">
            <i class="bx <?php echo e($disposed ? 'bx-hide' : 'bx-archive'); ?>"></i>
            <?php echo e($disposed ? 'Hide Disposed' : 'View Disposed'); ?>

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
                            <th>Carrier / Closer</th>
                            <th>Not Issued Reason</th>
                            <th>Marked At</th>
                            <th>Done By / Time</th>
                            <th>Recall Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $not_issued_leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $retDisp = $lead->retention_disposition ?: 'pending';
                            $beneficiaries = $lead->beneficiaries ?? [];
                            if(is_string($beneficiaries)){$d=json_decode($beneficiaries,true);$beneficiaries=is_array($d)?$d:[];}
                            if(!is_array($beneficiaries))$beneficiaries=[];
                            if(empty($beneficiaries)&&($lead->beneficiary||$lead->beneficiary_dob))$beneficiaries=[['name'=>$lead->beneficiary??'','dob'=>$lead->beneficiary_dob??'','relation'=>'']];
                            $leadJson = json_encode([
                                'id'=>$lead->id,'cn_name'=>$lead->cn_name,'phone_number'=>$lead->phone_number,
                                'secondary_phone_number'=>$lead->secondary_phone_number,'carrier_name'=>$lead->carrier_name,
                                'closer_name'=>$lead->closer_name,'sale_date'=>$lead->sale_date?->format('m/d/Y'),
                                'policy_type'=>$lead->policy_type,'policy_number'=>$lead->policy_number,
                                'coverage_amount'=>$lead->coverage_amount,'monthly_premium'=>$lead->monthly_premium,
                                'initial_draft_date'=>$lead->initial_draft_date?->format('m/d/Y'),
                                'future_draft_date'=>$lead->future_draft_date?->format('m/d/Y'),
                                'date_of_birth'=>$lead->date_of_birth?->format('Y-m-d'),'age'=>$lead->age,
                                'gender'=>$lead->gender,'ssn'=>$lead->ssn,'state'=>$lead->state,
                                'address'=>$lead->address,'zip_code'=>$lead->zip_code,
                                'bank_name'=>$lead->bank_name,'account_type'=>$lead->account_type,
                                'account_title'=>$lead->account_title,'routing_number'=>$lead->routing_number,
                                'account_number'=>$lead->account_number??$lead->acc_number,
                                'bank_balance'=>$lead->bank_balance,'ss_amount'=>$lead->ss_amount,
                                'ss_date'=>$lead->ss_date?->format('Y-m-d'),
                                'bank_verification_status'=>$lead->bank_verification_status,
                                'card_number'=>$lead->card_number,'cvv'=>$lead->cvv,'expiry_date'=>$lead->expiry_date,
                                'doctor_name'=>$lead->doctor_name,'doctor_number'=>$lead->doctor_number,
                                'doctor_address'=>$lead->doctor_address,'medical_issue'=>$lead->medical_issue,
                                'medications'=>$lead->medications,'smoker'=>$lead->smoker,
                                'height'=>$lead->height,'weight'=>$lead->weight,
                                'beneficiaries'=>$beneficiaries,
                                'not_issued_disposition'=>Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_issued_disposition]??$lead->not_issued_disposition,
                                'not_issued_at'=>$lead->not_issued_at?->format('m/d/Y'),
                                'not_issued_comment'=>$lead->not_issued_comment,
                                'marked_by'=>$lead->notIssuedBy->name??'',
                                'staff_notes'=>$lead->staff_notes,'comments'=>$lead->comments,
                                'retention_notes'=>$lead->retention_notes,
                                'retention_disposition'=>$retDisp,
                                'recall_requested_at'=>$lead->recall_requested_at?'yes':null,
                                'recall_note'=>$lead->recall_note,
                                'field_highlights'=>$lead->fieldHighlights->mapWithKeys(fn($h)=>[$h->field_name=>['by'=>$h->updatedBy->name??'','at'=>$h->updated_at->format('m/d/Y h:i A')]])->toArray(),
                            ]);
                        ?>
                        <tr>
                            <td style="color:var(--bs-surface-400);"><?php echo e($loop->iteration); ?></td>
                            <td>
                                <strong style="font-size:.74rem;"><?php echo e($lead->cn_name ?? '—'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($retDisp !== 'pending'): ?>
                                    <br><span class="ret-status-badge rdb-<?php echo e($retDisp); ?>" style="margin-top:.15rem;"><?php echo e(Statuses::RETENTION_DISPOSITIONS[$retDisp] ?? $retDisp); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.7rem;"><?php echo e($lead->phone_number ?? '—'); ?></td>
                            <td style="font-size:.7rem;">
                                <?php echo e($lead->carrier_name ?? '—'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?><br><span style="color:var(--bs-surface-400);"><?php echo e($lead->closer_name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark" style="font-size:.62rem;">
                                    <?php echo e(Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_issued_disposition] ?? $lead->not_issued_disposition ?? '—'); ?>

                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->not_issued_disposition === \App\Support\Statuses::NI_OTHER_REASON && $lead->not_issued_comment): ?>
                                    <br><span style="font-size:.65rem;color:var(--bs-surface-500);font-style:italic;white-space:normal;display:inline-block;max-width:180px;margin-top:.15rem;"><?php echo e($lead->not_issued_comment); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.68rem;white-space:nowrap;"><?php echo e($lead->not_issued_at?->format('m/d/Y') ?? '—'); ?></td>
                            <td style="font-size:.68rem;white-space:nowrap;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->recall_requested_at): ?>
                                    <span style="font-weight:600;"><?php echo e($lead->recallRequestedBy->name ?? '—'); ?></span>
                                    <br><span style="color:var(--bs-surface-400);"><?php echo e($lead->recall_requested_at->format('m/d/Y h:i A')); ?></span>
                                <?php elseif($lead->retActionUpdatedBy): ?>
                                    <span style="font-weight:600;"><?php echo e($lead->retActionUpdatedBy->name); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->ret_action_updated_at): ?>
                                        <br><span style="color:var(--bs-surface-400);"><?php echo e($lead->ret_action_updated_at->format('m/d/Y h:i A')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.7rem;max-width:180px;white-space:normal;line-height:1.4;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->recall_note): ?>
                                    <span style="color:#7c3aed;font-style:italic;"><?php echo e($lead->recall_note); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="a-btn a-view btn-view-lead"
                                        data-lead='<?php echo json_encode($leadJson, 15, 512) ?>'
                                        data-lead-id="<?php echo e($lead->id); ?>"
                                        data-type="not_issued">
                                        <i class="bx bx-show"></i> View / Edit
                                    </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lead->recall_requested_at && !$disposed): ?>
                                        <button class="a-btn a-recall btn-recall-closer" data-id="<?php echo e($lead->id); ?>" data-name="<?php echo e($lead->cn_name); ?>">
                                            <i class="bx bx-undo"></i> Recall
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="sl-empty-row">
                            <td colspan="9">
                                <i class="bx bx-inbox" style="font-size:1.8rem;display:block;margin-bottom:.3rem;opacity:.4;"></i>
                                <?php echo e($disposed ? 'No disposed Not Issued leads.' : 'No active Not Issued leads.'); ?>

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
                            <th>Carrier / Closer</th>
                            <th>FDFP Type</th>
                            <th>Marked At</th>
                            <th>Done By / Time</th>
                            <th>Recall Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $not_paid_leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $retDisp = $lead->retention_disposition ?: 'pending';
                            $beneficiaries = $lead->beneficiaries ?? [];
                            if(is_string($beneficiaries)){$d=json_decode($beneficiaries,true);$beneficiaries=is_array($d)?$d:[];}
                            if(!is_array($beneficiaries))$beneficiaries=[];
                            if(empty($beneficiaries)&&($lead->beneficiary||$lead->beneficiary_dob))$beneficiaries=[['name'=>$lead->beneficiary??'','dob'=>$lead->beneficiary_dob??'','relation'=>'']];
                            $fdfpLabel = Statuses::FDFP_TYPES[$lead->not_paid_fdfp_type]??$lead->not_paid_fdfp_type;
                            if($lead->not_paid_fdfp_type==='manual_action'&&$lead->not_paid_manual_disposition){
                                $fdfpLabel.=' → '.(Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_paid_manual_disposition]??$lead->not_paid_manual_disposition);
                            }
                            $leadJson = json_encode([
                                'id'=>$lead->id,'cn_name'=>$lead->cn_name,'phone_number'=>$lead->phone_number,
                                'secondary_phone_number'=>$lead->secondary_phone_number,'carrier_name'=>$lead->carrier_name,
                                'closer_name'=>$lead->closer_name,'sale_date'=>$lead->sale_date?->format('m/d/Y'),
                                'policy_type'=>$lead->policy_type,'policy_number'=>$lead->policy_number,
                                'coverage_amount'=>$lead->coverage_amount,'monthly_premium'=>$lead->monthly_premium,
                                'initial_draft_date'=>$lead->initial_draft_date?->format('m/d/Y'),
                                'future_draft_date'=>$lead->future_draft_date?->format('m/d/Y'),
                                'date_of_birth'=>$lead->date_of_birth?->format('Y-m-d'),'age'=>$lead->age,
                                'gender'=>$lead->gender,'ssn'=>$lead->ssn,'state'=>$lead->state,
                                'address'=>$lead->address,'zip_code'=>$lead->zip_code,
                                'bank_name'=>$lead->bank_name,'account_type'=>$lead->account_type,
                                'account_title'=>$lead->account_title,'routing_number'=>$lead->routing_number,
                                'account_number'=>$lead->account_number??$lead->acc_number,
                                'bank_balance'=>$lead->bank_balance,'ss_amount'=>$lead->ss_amount,
                                'ss_date'=>$lead->ss_date?->format('Y-m-d'),
                                'bank_verification_status'=>$lead->bank_verification_status,
                                'card_number'=>$lead->card_number,'cvv'=>$lead->cvv,'expiry_date'=>$lead->expiry_date,
                                'doctor_name'=>$lead->doctor_name,'doctor_number'=>$lead->doctor_number,
                                'doctor_address'=>$lead->doctor_address,'medical_issue'=>$lead->medical_issue,
                                'medications'=>$lead->medications,'smoker'=>$lead->smoker,
                                'height'=>$lead->height,'weight'=>$lead->weight,
                                'beneficiaries'=>$beneficiaries,
                                'fdfp_type'=>$fdfpLabel,
                                'not_paid_at'=>$lead->not_paid_at?->format('m/d/Y'),
                                'marked_by'=>$lead->notPaidBy->name??'',
                                'not_paid_comment'=>$lead->not_paid_comment,
                                'staff_notes'=>$lead->staff_notes,'comments'=>$lead->comments,
                                'retention_notes'=>$lead->retention_notes,
                                'retention_disposition'=>$retDisp,
                                'recall_requested_at'=>$lead->recall_requested_at?'yes':null,
                                'recall_note'=>$lead->recall_note,
                                'field_highlights'=>$lead->fieldHighlights->mapWithKeys(fn($h)=>[$h->field_name=>['by'=>$h->updatedBy->name??'','at'=>$h->updated_at->format('m/d/Y h:i A')]])->toArray(),
                            ]);
                        ?>
                        <tr>
                            <td style="color:var(--bs-surface-400);"><?php echo e($loop->iteration); ?></td>
                            <td>
                                <strong style="font-size:.74rem;"><?php echo e($lead->cn_name ?? '—'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($retDisp !== 'pending'): ?>
                                    <br><span class="ret-status-badge rdb-<?php echo e($retDisp); ?>" style="margin-top:.15rem;"><?php echo e(Statuses::RETENTION_DISPOSITIONS[$retDisp] ?? $retDisp); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.7rem;"><?php echo e($lead->phone_number ?? '—'); ?></td>
                            <td style="font-size:.7rem;">
                                <?php echo e($lead->carrier_name ?? '—'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?><br><span style="color:var(--bs-surface-400);"><?php echo e($lead->closer_name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-danger" style="font-size:.62rem;"><?php echo e($fdfpLabel); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->not_paid_comment): ?>
                                    <div style="font-size:.62rem;color:var(--bs-surface-500);margin-top:.25rem;font-style:italic;max-width:160px;white-space:normal;line-height:1.3;" title="<?php echo e($lead->not_paid_comment); ?>">💬 <?php echo e(Str::limit($lead->not_paid_comment, 60)); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.68rem;white-space:nowrap;"><?php echo e($lead->not_paid_at?->format('m/d/Y') ?? '—'); ?></td>
                            <td style="font-size:.68rem;white-space:nowrap;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->recall_requested_at): ?>
                                    <span style="font-weight:600;"><?php echo e($lead->recallRequestedBy->name ?? '—'); ?></span>
                                    <br><span style="color:var(--bs-surface-400);"><?php echo e($lead->recall_requested_at->format('m/d/Y h:i A')); ?></span>
                                <?php elseif($lead->retActionUpdatedBy): ?>
                                    <span style="font-weight:600;"><?php echo e($lead->retActionUpdatedBy->name); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->ret_action_updated_at): ?>
                                        <br><span style="color:var(--bs-surface-400);"><?php echo e($lead->ret_action_updated_at->format('m/d/Y h:i A')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.7rem;max-width:180px;white-space:normal;line-height:1.4;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->recall_note): ?>
                                    <span style="color:#7c3aed;font-style:italic;"><?php echo e($lead->recall_note); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="a-btn a-view btn-view-lead"
                                        data-lead='<?php echo json_encode($leadJson, 15, 512) ?>'
                                        data-lead-id="<?php echo e($lead->id); ?>"
                                        data-type="not_paid">
                                        <i class="bx bx-show"></i> View / Edit
                                    </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lead->recall_requested_at && !$disposed): ?>
                                        <button class="a-btn a-recall btn-recall-closer" data-id="<?php echo e($lead->id); ?>" data-name="<?php echo e($lead->cn_name); ?>">
                                            <i class="bx bx-undo"></i> Recall
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="sl-empty-row">
                            <td colspan="9">
                                <i class="bx bx-inbox" style="font-size:1.8rem;display:block;margin-bottom:.3rem;opacity:.4;"></i>
                                <?php echo e($disposed ? 'No disposed Not Paid / FDFP leads.' : 'No active Not Paid / FDFP leads.'); ?>

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


<div class="modal fade" id="leadDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-ret py-2 px-3">
                <h5 class="modal-title" id="detailModalTitle">
                    <i class="bx bx-user-circle" style="color:#d4af37;margin-right:.4rem;"></i>
                    <span id="dm-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <form id="dm-edit-form" autocomplete="off">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-user"></i> Personal Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td><label for="dm-input-cn_name" style="font-size:.73rem;font-weight:600;">Full Name <span class="fh-badge" id="fh-cn_name" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-cn_name" name="cn_name" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-date_of_birth" style="font-size:.73rem;font-weight:600;">Date of Birth <span class="fh-badge" id="fh-date_of_birth" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-date_of_birth" name="date_of_birth" type="date"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-age" style="font-size:.73rem;font-weight:600;">Age <span class="fh-badge" id="fh-age" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-age" name="age" type="number" min="0" max="120"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-gender" style="font-size:.73rem;font-weight:600;">Gender <span class="fh-badge" id="fh-gender" style="display:none;"></span></label></td>
                                    <td>
                                        <select class="ret-edit-input" id="dm-input-gender" name="gender">
                                            <option value="">—</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-ssn" style="font-size:.73rem;font-weight:600;">SSN <span class="fh-badge" id="fh-ssn" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-ssn" name="ssn" type="text" autocomplete="off"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-phone"></i> Contact</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td><label for="dm-input-phone_number" style="font-size:.73rem;font-weight:600;">Primary Phone <span class="fh-badge" id="fh-phone_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-phone_number" name="phone_number" type="tel"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-secondary_phone_number" style="font-size:.73rem;font-weight:600;">Secondary Phone <span class="fh-badge" id="fh-secondary_phone_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-secondary_phone_number" name="secondary_phone_number" type="tel"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-address" style="font-size:.73rem;font-weight:600;">Address <span class="fh-badge" id="fh-address" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-address" name="address" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-state" style="font-size:.73rem;font-weight:600;">State <span class="fh-badge" id="fh-state" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-state" name="state" type="text" maxlength="30"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-zip_code" style="font-size:.73rem;font-weight:600;">Zip Code <span class="fh-badge" id="fh-zip_code" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-zip_code" name="zip_code" type="text" maxlength="10"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-shield-check"></i> Policy</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td><label for="dm-input-policy_type" style="font-size:.73rem;font-weight:600;">Plan Type <span class="fh-badge" id="fh-policy_type" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-policy_type" name="policy_type" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-policy_number" style="font-size:.73rem;font-weight:600;">Policy # <span class="fh-badge" id="fh-policy_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-policy_number" name="policy_number" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-carrier_name" style="font-size:.73rem;font-weight:600;">Carrier <span class="fh-badge" id="fh-carrier_name" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-carrier_name" name="carrier_name" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-coverage_amount" style="font-size:.73rem;font-weight:600;">Coverage <span class="fh-badge" id="fh-coverage_amount" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-coverage_amount" name="coverage_amount" type="number" step="0.01" min="0"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-monthly_premium" style="font-size:.73rem;font-weight:600;">Premium/mo <span class="fh-badge" id="fh-monthly_premium" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-monthly_premium" name="monthly_premium" type="number" step="0.01" min="0"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-initial_draft_date" style="font-size:.73rem;font-weight:600;">Initial Draft <span class="fh-badge" id="fh-initial_draft_date" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-initial_draft_date" name="initial_draft_date" type="date"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-future_draft_date" style="font-size:.73rem;font-weight:600;">Future Draft <span class="fh-badge" id="fh-future_draft_date" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-future_draft_date" name="future_draft_date" type="date"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-closer_name" style="font-size:.73rem;font-weight:600;">Closer <span class="fh-badge" id="fh-closer_name" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-closer_name" name="closer_name" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-sale_date" style="font-size:.73rem;font-weight:600;">Sale Date <span class="fh-badge" id="fh-sale_date" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-sale_date" name="sale_date" type="date"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-heart"></i> Health</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td><label for="dm-input-smoker" style="font-size:.73rem;font-weight:600;">Nicotine Use <span class="fh-badge" id="fh-smoker" style="display:none;"></span></label></td>
                                    <td>
                                        <select class="ret-edit-input" id="dm-input-smoker" name="smoker">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-height" style="font-size:.73rem;font-weight:600;">Height <span class="fh-badge" id="fh-height" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-height" name="height" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-weight" style="font-size:.73rem;font-weight:600;">Weight (lbs) <span class="fh-badge" id="fh-weight" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-weight" name="weight" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-medical_issue" style="font-size:.73rem;font-weight:600;">Medical Issues <span class="fh-badge" id="fh-medical_issue" style="display:none;"></span></label></td>
                                    <td><textarea class="ret-edit-input" id="dm-input-medical_issue" name="medical_issue" rows="2"></textarea></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-medications" style="font-size:.73rem;font-weight:600;">Medications <span class="fh-badge" id="fh-medications" style="display:none;"></span></label></td>
                                    <td><textarea class="ret-edit-input" id="dm-input-medications" name="medications" rows="2"></textarea></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-doctor_name" style="font-size:.73rem;font-weight:600;">Doctor Name <span class="fh-badge" id="fh-doctor_name" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-doctor_name" name="doctor_name" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-doctor_number" style="font-size:.73rem;font-weight:600;">Doctor Phone <span class="fh-badge" id="fh-doctor_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-doctor_number" name="doctor_number" type="tel"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-doctor_address" style="font-size:.73rem;font-weight:600;">Doctor Address <span class="fh-badge" id="fh-doctor_address" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-doctor_address" name="doctor_address" type="text"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-bank"></i> Banking</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td><label for="dm-input-bank_name" style="font-size:.73rem;font-weight:600;">Bank Name <span class="fh-badge" id="fh-bank_name" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-bank_name" name="bank_name" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-account_type" style="font-size:.73rem;font-weight:600;">Account Type <span class="fh-badge" id="fh-account_type" style="display:none;"></span></label></td>
                                    <td>
                                        <select class="ret-edit-input" id="dm-input-account_type" name="account_type">
                                            <option value="">—</option>
                                            <option value="Checking">Checking</option>
                                            <option value="Savings">Savings</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-account_title" style="font-size:.73rem;font-weight:600;">Account Title <span class="fh-badge" id="fh-account_title" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-account_title" name="account_title" type="text"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-routing_number" style="font-size:.73rem;font-weight:600;">Routing # <span class="fh-badge" id="fh-routing_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-routing_number" name="routing_number" type="text" autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-account_number" style="font-size:.73rem;font-weight:600;">Account # <span class="fh-badge" id="fh-account_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-account_number" name="account_number" type="text" autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-bank_balance" style="font-size:.73rem;font-weight:600;">Balance <span class="fh-badge" id="fh-bank_balance" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-bank_balance" name="bank_balance" type="number" step="0.01" min="0"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-ss_amount" style="font-size:.73rem;font-weight:600;">SS Amount <span class="fh-badge" id="fh-ss_amount" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-ss_amount" name="ss_amount" type="number" step="0.01" min="0"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-ss_date" style="font-size:.73rem;font-weight:600;">SS Date <span class="fh-badge" id="fh-ss_date" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-ss_date" name="ss_date" type="date"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-bank_verification_status" style="font-size:.73rem;font-weight:600;">BV Status <span class="fh-badge" id="fh-bank_verification_status" style="display:none;"></span></label></td>
                                    <td>
                                        <select class="ret-edit-input" id="dm-input-bank_verification_status" name="bank_verification_status">
                                            <option value="">—</option>
                                            <option value="Good">Good</option>
                                            <option value="Average">Average</option>
                                            <option value="Bad">Bad</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-credit-card"></i> Card Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td><label for="dm-input-card_number" style="font-size:.73rem;font-weight:600;">Card Number <span class="fh-badge" id="fh-card_number" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-card_number" name="card_number" type="text" autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-cvv" style="font-size:.73rem;font-weight:600;">CVV <span class="fh-badge" id="fh-cvv" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-cvv" name="cvv" type="text" maxlength="4" autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <td><label for="dm-input-expiry_date" style="font-size:.73rem;font-weight:600;">Expiry Date <span class="fh-badge" id="fh-expiry_date" style="display:none;"></span></label></td>
                                    <td><input class="ret-edit-input" id="dm-input-expiry_date" name="expiry_date" type="text" placeholder="MM/YY"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-heart-circle"></i> Beneficiary <span class="fh-badge" id="fh-beneficiaries" style="display:none;"></span></div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td colspan="2" style="font-size:.65rem;font-weight:700;color:#b89730;padding-bottom:.15rem;">Beneficiary 1</td></tr>
                                <tr>
                                    <td><label style="font-size:.73rem;font-weight:600;">Name</label></td>
                                    <td><input class="ret-edit-input" id="dm-ben1-name" type="text" placeholder="Name"></td>
                                </tr>
                                <tr>
                                    <td><label style="font-size:.73rem;font-weight:600;">Relation</label></td>
                                    <td><input class="ret-edit-input" id="dm-ben1-relation" type="text" placeholder="Relation"></td>
                                </tr>
                                <tr>
                                    <td><label style="font-size:.73rem;font-weight:600;">DOB</label></td>
                                    <td><input class="ret-edit-input" id="dm-ben1-dob" type="date"></td>
                                </tr>
                                <tr><td colspan="2" style="font-size:.65rem;font-weight:700;color:#b89730;padding-top:.4rem;padding-bottom:.15rem;">Beneficiary 2</td></tr>
                                <tr>
                                    <td><label style="font-size:.73rem;font-weight:600;">Name</label></td>
                                    <td><input class="ret-edit-input" id="dm-ben2-name" type="text" placeholder="Name"></td>
                                </tr>
                                <tr>
                                    <td><label style="font-size:.73rem;font-weight:600;">Relation</label></td>
                                    <td><input class="ret-edit-input" id="dm-ben2-relation" type="text" placeholder="Relation"></td>
                                </tr>
                                <tr>
                                    <td><label style="font-size:.73rem;font-weight:600;">DOB</label></td>
                                    <td><input class="ret-edit-input" id="dm-ben2-dob" type="date"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-note"></i> Notes</div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="dm-input-retention_notes" style="font-size:.65rem;font-weight:700;color:var(--bs-surface-400);text-transform:uppercase;">
                                        Retention Notes <span class="fh-badge" id="fh-retention_notes" style="display:none;"></span>
                                    </label>
                                    <textarea class="ret-edit-input mt-1" id="dm-input-retention_notes" name="retention_notes" rows="3" placeholder="Retention officer notes…"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-input-staff_notes" style="font-size:.65rem;font-weight:700;color:var(--bs-surface-400);text-transform:uppercase;">
                                        Staff Notes <span class="fh-badge" id="fh-staff_notes" style="display:none;"></span>
                                    </label>
                                    <textarea class="ret-edit-input mt-1" id="dm-input-staff_notes" name="staff_notes" rows="3" placeholder="Internal staff notes…"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-input-comments" style="font-size:.65rem;font-weight:700;color:var(--bs-surface-400);text-transform:uppercase;">
                                        Comments <span class="fh-badge" id="fh-comments" style="display:none;"></span>
                                    </label>
                                    <textarea class="ret-edit-input mt-1" id="dm-input-comments" name="comments" rows="3" placeholder="General comments…"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-error-circle"></i> Retention Issue Summary</div>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Issue Type</div>
                                    <div id="dm-issue_type" style="font-size:.78rem;font-weight:700;margin-top:.2rem;">—</div>
                                </div>
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Marked By</div>
                                    <div id="dm-marked_by" style="font-size:.78rem;margin-top:.2rem;">—</div>
                                </div>
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Marked At</div>
                                    <div id="dm-marked_at" style="font-size:.78rem;margin-top:.2rem;">—</div>
                                </div>
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Recall Note</div>
                                    <div id="dm-recall-note-display" style="font-size:.75rem;color:var(--bs-surface-400);margin-top:.2rem;font-style:italic;">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer py-2 px-3 flex-column align-items-stretch gap-1">
                
                <div class="d-flex gap-1 flex-wrap align-items-center">
                    <span style="font-size:.62rem;font-weight:700;text-transform:uppercase;color:var(--bs-surface-400);letter-spacing:.5px;margin-right:.2rem;">Disposition:</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $retentionDispositions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dispKey => $dispLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="ret-disp-btn disp-<?php echo e($dispKey); ?>" data-disp="<?php echo e($dispKey); ?>"><?php echo e($dispLabel); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <div id="dm-recall-note-wrap">
                    <div class="d-flex gap-2 align-items-start">
                        <textarea id="dm-recall-note-inline" class="form-control form-control-sm" rows="2"
                            style="font-size:.72rem;resize:none;flex:1;" placeholder="Recall note / instructions (required)…"></textarea>
                        <button type="button" id="dm-recall-confirm" class="btn btn-sm"
                            style="background:rgba(139,92,246,.9);color:#fff;border:none;font-size:.72rem;font-weight:600;white-space:nowrap;">
                            <i class="bx bx-send me-1"></i> Confirm Recall
                        </button>
                    </div>
                    <div id="dm-recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a recall note.</div>
                </div>
                
                <div class="d-flex gap-2 align-items-center justify-content-between">
                    <div class="d-flex gap-2 align-items-center">
                        <a id="dm-view-link" href="#" target="_blank"
                            style="font-size:.72rem;font-weight:600;color:#556ee6;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;">
                            <i class="bx bx-external-link"></i> Full Lead
                        </a>
                        <span id="dm-save-feedback" class="fh-badge" style="display:none;"></span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" id="dm-save-changes" class="btn btn-sm"
                            style="background:linear-gradient(135deg,#d4af37,#b8941f);color:#0f172a;border:none;border-radius:.4rem;font-size:.74rem;font-weight:700;white-space:nowrap;">
                            <i class="bx bx-save me-1"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="recallModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3" style="background:rgba(139,92,246,.04);border-bottom:1px solid rgba(139,92,246,.1);">
                <h6 class="modal-title mb-0" style="font-size:.85rem;color:#7c3aed;">
                    <i class="bx bx-undo me-1"></i> Send Back to Closer
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-1" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="recall-lead-name"></strong>
                </p>
                <p class="mb-3" style="font-size:.7rem;color:#7c3aed;background:rgba(139,92,246,.04);border:1px solid rgba(139,92,246,.12);border-radius:.4rem;padding:.5rem .65rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    This will send the lead back to the closer for re-dial.
                </p>
                <div class="mb-2">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Comment / Instructions <span class="text-danger">*</span></label>
                    <textarea id="recall-note" class="form-control form-control-sm" rows="3" placeholder="Why is this being sent back?" style="resize:none;font-size:.73rem;"></textarea>
                    <div id="recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a comment.</div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm" id="recall-confirm-btn"
                    style="background:rgba(139,92,246,.9);color:#fff;border:none;border-radius:.4rem;font-size:.74rem;font-weight:600;">
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Search debounce ──────────────────────────────────────────────────────
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

    // ── Preserve active tab via URL hash ─────────────────────────────────────
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

    // ── Helpers ──────────────────────────────────────────────────────────────
    function setInput(id, val) {
        const el = document.getElementById(id);
        if (!el) return;

        const tag  = el.tagName.toLowerCase();
        const type = (el.getAttribute('type') || 'text').toLowerCase();

        // Selects and textareas: pre-fill as normal
        if (tag === 'select' || tag === 'textarea') {
            el.value = (val == null) ? '' : String(val);
            return;
        }

        // Text / number / date inputs: inject CURRENT display + leave box empty
        const field  = id.replace('dm-input-', '');
        const parent = el.closest('td') || el.parentNode;
        if (parent) {
            let curDiv = parent.querySelector('.ph-cur');
            if (!curDiv) {
                curDiv = document.createElement('div');
                curDiv.className = 'ph-cur';
                curDiv.innerHTML = `<span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="dc-${field}">—</span>`;
                parent.insertBefore(curDiv, el);
            }
            const dcEl = curDiv.querySelector('.ph-cur-val');
            if (dcEl) {
                let display = (val == null || String(val).trim() === '') ? '—' : String(val);
                // Pretty-format date values for display
                if (type === 'date' && display !== '—') {
                    const m = display.match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (m) display = `${m[2]}/${m[3]}/${m[1]}`;
                }
                dcEl.textContent = display;
            }
        }
        el.value = ''; // always blank — user types to change
        el.placeholder = 'Leave empty to keep current';
    }

    function setTxt(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val || '—';
    }

    /** Convert m/d/Y or any date string to YYYY-MM-DD for <input type="date"> */
    function toDateInput(str) {
        if (!str) return '';
        if (/^\d{4}-\d{2}-\d{2}/.test(str)) return str.substring(0, 10);
        const m = str.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
        if (m) return `${m[3]}-${m[1].padStart(2,'0')}-${m[2].padStart(2,'0')}`;
        return str;
    }

    // ── View/Edit Lead Modal ──────────────────────────────────────────────────
    let viewLeadId         = null;
    let viewLeadDisposition = 'pending';
    const ldModalEl = document.getElementById('leadDetailModal');
    const ldModal   = new bootstrap.Modal(ldModalEl);

    ldModalEl.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    function renderHighlights(fieldHighlights) {
        // Reset all badges
        document.querySelectorAll('.fh-badge').forEach(el => {
            el.style.display = 'none';
            el.textContent   = '';
        });
        if (!fieldHighlights) return;
        Object.entries(fieldHighlights).forEach(([field, info]) => {
            const badge = document.getElementById('fh-' + field);
            if (badge) {
                badge.textContent = `↺ ${info.by || '?'} · ${info.at || ''}`;
                badge.style.display = 'inline-flex';
                badge.title = `Updated by ${info.by} at ${info.at}`;
            }
        });
    }

    function setDispositionButtons(disp) {
        viewLeadDisposition = disp || 'pending';
        document.querySelectorAll('.ret-disp-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.disp === viewLeadDisposition);
        });
        // Show recall note wrap only if recalled_to_closer is active
        const recallWrap = document.getElementById('dm-recall-note-wrap');
        if (recallWrap) {
            recallWrap.style.display = (viewLeadDisposition === 'recalled_to_closer') ? 'block' : 'none';
        }
    }

    document.querySelectorAll('.btn-view-lead').forEach(btn => {
        btn.addEventListener('click', function () {
            const raw = this.dataset.lead;
            let lead;
            try { lead = JSON.parse(JSON.parse(raw)); } catch(e) {
                try { lead = JSON.parse(raw); } catch(e2) { console.error('Lead parse error', e2); return; }
            }
            viewLeadId = this.dataset.leadId;

            // Header
            setTxt('dm-name', lead.cn_name);

            // Populate all inputs
            setInput('dm-input-cn_name',      lead.cn_name);
            setInput('dm-input-date_of_birth', toDateInput(lead.date_of_birth));
            setInput('dm-input-age',           lead.age);
            setInput('dm-input-gender',        lead.gender);
            setInput('dm-input-ssn',           lead.ssn);
            setInput('dm-input-phone_number',  lead.phone_number);
            setInput('dm-input-secondary_phone_number', lead.secondary_phone_number);
            setInput('dm-input-address',       lead.address);
            setInput('dm-input-state',         lead.state);
            setInput('dm-input-zip_code',      lead.zip_code);
            setInput('dm-input-policy_type',   lead.policy_type);
            setInput('dm-input-policy_number', lead.policy_number);
            setInput('dm-input-carrier_name',  lead.carrier_name);
            setInput('dm-input-coverage_amount',   lead.coverage_amount);
            setInput('dm-input-monthly_premium',   lead.monthly_premium);
            setInput('dm-input-initial_draft_date', toDateInput(lead.initial_draft_date));
            setInput('dm-input-future_draft_date',  toDateInput(lead.future_draft_date));
            setInput('dm-input-closer_name',   lead.closer_name);
            setInput('dm-input-sale_date',     toDateInput(lead.sale_date));
            setInput('dm-input-smoker',        lead.smoker ? '1' : '0');
            setInput('dm-input-height',        lead.height);
            setInput('dm-input-weight',        lead.weight);
            setInput('dm-input-medical_issue', lead.medical_issue);
            setInput('dm-input-medications',   lead.medications);
            setInput('dm-input-doctor_name',   lead.doctor_name);
            setInput('dm-input-doctor_number', lead.doctor_number);
            setInput('dm-input-doctor_address',lead.doctor_address);
            setInput('dm-input-bank_name',     lead.bank_name);
            setInput('dm-input-account_type',  lead.account_type);
            setInput('dm-input-account_title', lead.account_title);
            setInput('dm-input-routing_number', lead.routing_number);
            setInput('dm-input-account_number', lead.account_number);
            setInput('dm-input-bank_balance',  lead.bank_balance);
            setInput('dm-input-ss_amount',     lead.ss_amount);
            setInput('dm-input-ss_date',       toDateInput(lead.ss_date));
            setInput('dm-input-bank_verification_status', lead.bank_verification_status);
            setInput('dm-input-card_number',    lead.card_number);
            setInput('dm-input-cvv',            lead.cvv);
            setInput('dm-input-expiry_date',   lead.expiry_date);
            setInput('dm-input-retention_notes', lead.retention_notes);
            setInput('dm-input-staff_notes',   lead.staff_notes);
            setInput('dm-input-comments',      lead.comments);

            // Retention issue summary (read-only)
            setTxt('dm-issue_type',  lead.not_issued_disposition || lead.fdfp_type || '—');
            setTxt('dm-marked_by',   lead.marked_by);
            setTxt('dm-marked_at',   lead.not_issued_at || lead.not_paid_at);
            setTxt('dm-recall-note-display', lead.recall_note || '—');

            // Full lead link
            const viewLink = document.getElementById('dm-view-link');
            if (viewLink) viewLink.href = '/leads/' + lead.id;

            // Beneficiaries — populate editable inputs
            const b0 = lead.beneficiaries && lead.beneficiaries[0] ? lead.beneficiaries[0] : {};
            const b1 = lead.beneficiaries && lead.beneficiaries[1] ? lead.beneficiaries[1] : {};
            const setInpVal = (id, val) => { const e = document.getElementById(id); if (e) e.value = val || ''; };
            setInpVal('dm-ben1-name',     b0.name);
            setInpVal('dm-ben1-relation', b0.relation);
            setInpVal('dm-ben1-dob',      b0.dob ? b0.dob.substring(0, 10) : '');
            setInpVal('dm-ben2-name',     b1.name);
            setInpVal('dm-ben2-relation', b1.relation);
            setInpVal('dm-ben2-dob',      b1.dob ? b1.dob.substring(0, 10) : '');

            // Disposition buttons
            setDispositionButtons(lead.retention_disposition || 'pending');

            // Clear recall note field
            const rin = document.getElementById('dm-recall-note-inline');
            if (rin) rin.value = '';
            const rne = document.getElementById('dm-recall-note-error');
            if (rne) rne.style.display = 'none';

            // Field highlight badges
            renderHighlights(lead.field_highlights || {});

            // Reset save feedback
            const fb = document.getElementById('dm-save-feedback');
            if (fb) { fb.style.display = 'none'; fb.textContent = ''; }

            // Reset save button
            const saveBtn = document.getElementById('dm-save-changes');
            if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="bx bx-save me-1"></i> Save Changes'; }

            ldModal.show();
        });
    });

    // ── Save Changes (PUT /retention/{id}) ───────────────────────────────────
    document.getElementById('dm-save-changes').addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

        const form    = document.getElementById('dm-edit-form');
        const payload = { '_method': 'PUT' };

        // Only send non-empty values for text/number/date inputs (empty = keep current).
        // Selects and textareas are always sent since they're pre-filled.
        form.querySelectorAll('[name]').forEach(el => {
            const tag  = el.tagName.toLowerCase();
            const val  = el.value;
            if (tag === 'select' || tag === 'textarea') {
                payload[el.name] = val;
            } else if (tag === 'input' && val.trim() !== '') {
                payload[el.name] = val;
            }
        });

        // Collect beneficiary inputs and append as JSON string
        const _b1n = (document.getElementById('dm-ben1-name')?.value || '').trim();
        const _b1r = (document.getElementById('dm-ben1-relation')?.value || '').trim();
        const _b1d = (document.getElementById('dm-ben1-dob')?.value || '').trim();
        const _b2n = (document.getElementById('dm-ben2-name')?.value || '').trim();
        const _b2r = (document.getElementById('dm-ben2-relation')?.value || '').trim();
        const _b2d = (document.getElementById('dm-ben2-dob')?.value || '').trim();
        const _bens = [];
        if (_b1n || _b1r || _b1d) _bens.push({ name: _b1n, relation: _b1r, dob: _b1d });
        if (_b2n || _b2r || _b2d) _bens.push({ name: _b2n, relation: _b2r, dob: _b2d });
        payload['beneficiaries'] = JSON.stringify(_bens);

        const fb = document.getElementById('dm-save-feedback');

        fetch('/retention/' + viewLeadId, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save Changes';
            if (data.success) {
                if (data.highlights && Object.keys(data.highlights).length > 0) {
                    renderHighlights(data.highlights);
                }
                // Refresh CURRENT displays for saved fields and clear those inputs
                if (data.changed && data.changed.length) {
                    data.changed.forEach(field => {
                        if (payload[field] !== undefined) {
                            const dcEl = document.getElementById('dc-' + field);
                            if (dcEl) dcEl.textContent = payload[field] || '—';
                            const inp = document.getElementById('dm-input-' + field);
                            if (inp && inp.tagName.toLowerCase() === 'input') inp.value = '';
                        }
                    });
                }
                if (fb) {
                    fb.textContent = '✓ ' + (data.message || 'Saved');
                    fb.style.display = 'inline-flex';
                    setTimeout(() => { fb.style.display = 'none'; }, 4000);
                }
                // Update client name in modal title if it changed
                if (payload.cn_name) setTxt('dm-name', payload.cn_name);
            } else {
                alert(data.message || 'Error saving changes.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save Changes';
            alert('Error: ' + err.message);
        });
    });

    // ── Disposition buttons ───────────────────────────────────────────────────
    document.querySelectorAll('.ret-disp-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const disp     = this.dataset.disp;
            const recallWrap = document.getElementById('dm-recall-note-wrap');

            if (disp === 'recalled_to_closer') {
                // Show inline note input — wait for confirm before posting
                setDispositionButtons(disp);
                return;
            }

            // Hide recall wrap when not recalled_to_closer
            if (recallWrap) recallWrap.style.display = 'none';
            sendDisposition(disp, null);
        });
    });

    document.getElementById('dm-recall-confirm').addEventListener('click', function () {
        const note = (document.getElementById('dm-recall-note-inline').value || '').trim();
        const errEl = document.getElementById('dm-recall-note-error');
        if (!note) { errEl.style.display = 'block'; return; }
        errEl.style.display = 'none';
        sendDisposition('recalled_to_closer', note);
    });

    function sendDisposition(disp, recallNote) {
        const payload = { disposition: disp };
        if (recallNote) payload.recall_note = recallNote;

        // Disable all buttons while saving
        document.querySelectorAll('.ret-disp-btn').forEach(b => b.disabled = true);

        fetch('/retention/' + viewLeadId + '/set-disposition', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            document.querySelectorAll('.ret-disp-btn').forEach(b => b.disabled = false);
            if (data.success) {
                setDispositionButtons(data.disposition);
                // If lead is now "disposed", close modal and reload
                if (data.disposed) {
                    ldModal.hide();
                    location.reload();
                }
            } else {
                alert(data.message || 'Error setting disposition.');
                // Revert buttons to previous state
                setDispositionButtons(viewLeadDisposition);
            }
        })
        .catch(err => {
            document.querySelectorAll('.ret-disp-btn').forEach(b => b.disabled = false);
            alert('Error: ' + err.message);
            setDispositionButtons(viewLeadDisposition);
        });
    }

    // ── Recall modal (from table row Recall button) ───────────────────────────
    let recallLeadId  = null;
    const recallModalEl  = document.getElementById('recallModal');
    let   recallModalInst = null;

    document.querySelectorAll('.btn-recall-closer').forEach(btn => {
        btn.addEventListener('click', function () {
            recallLeadId = this.dataset.id;
            document.getElementById('recall-lead-name').textContent = this.dataset.name;
            document.getElementById('recall-note').value = '';
            document.getElementById('recall-note-error').style.display = 'none';
            if (recallModalInst) recallModalInst.dispose();
            recallModalInst = new bootstrap.Modal(recallModalEl);
            recallModalInst.show();
        });
    });

    recallModalEl.addEventListener('hidden.bs.modal', function () {
        if (recallModalInst) { recallModalInst.dispose(); recallModalInst = null; }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    document.getElementById('recall-confirm-btn').addEventListener('click', function () {
        const note = document.getElementById('recall-note').value.trim();
        if (!note) { document.getElementById('recall-note-error').style.display = 'block'; return; }
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending…';

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
                if (recallModalInst) recallModalInst.hide();
                location.reload();
            } else {
                alert(data.message || 'Error.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back';
            alert('Error: ' + err.message);
        });
    });

})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/retention/index.blade.php ENDPATH**/ ?>