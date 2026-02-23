<?php $__env->startSection('title'); ?>
    Submission & Followup
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ── SL Design System ── */
.sl-topbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
}
.sl-topbar-left { display: flex; align-items: center; gap: 14px; }
.sl-page-title {
    font-size: 1.35rem; font-weight: 700; color: #1e293b;
    display: flex; align-items: center; gap: 8px; margin: 0;
}
.sl-page-title i { color: #d4af37; font-size: 1.5rem; }
.sl-page-subtitle { font-size: .78rem; color: #94a3b8; margin: 0; }

/* Stat cards */
.sl-stat-card {
    background: #fff; border-radius: 16px; padding: 18px 20px;
    border: 1px solid rgba(0,0,0,.06);
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    display: flex; align-items: center; gap: 14px;
    transition: all .2s;
}
.sl-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.sl-stat-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #fff;
}
.sl-stat-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.sl-stat-icon.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
.sl-stat-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
.sl-stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.sl-stat-icon.gold { background: linear-gradient(135deg, #d4af37, #b8972e); }
.sl-stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.sl-stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1.1; }
.sl-stat-label { font-size: .72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }

/* Tabs */
.sl-tabs {
    display: flex; gap: 4px; background: rgba(0,0,0,.04);
    border-radius: 22px; padding: 4px; margin-bottom: 16px;
    width: fit-content;
}
.sl-tab {
    padding: 8px 20px; border-radius: 20px; font-size: .82rem;
    font-weight: 600; cursor: pointer; color: #64748b;
    transition: all .2s; border: none; background: transparent;
}
.sl-tab.active { background: #fff; color: #d4af37; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
.sl-tab:hover:not(.active) { color: #1e293b; }
.sl-tab-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; height: 20px; border-radius: 10px;
    font-size: .68rem; font-weight: 700; margin-left: 6px; padding: 0 6px;
}
.sl-tab.active .sl-tab-badge { background: rgba(212,175,55,.15); color: #d4af37; }
.sl-tab:not(.active) .sl-tab-badge { background: rgba(0,0,0,.06); color: #94a3b8; }

/* Card */
.sl-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(0,0,0,.06);
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    overflow: hidden;
}
.sl-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid rgba(0,0,0,.05);
}
.sl-card-title {
    font-size: .92rem; font-weight: 700; color: #1e293b;
    display: flex; align-items: center; gap: 8px; margin: 0;
}
.sl-card-title i { color: #d4af37; }

/* Filter pills */
.sl-filter-pills {
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
    padding: 12px 20px; border-bottom: 1px solid rgba(0,0,0,.04);
}
.sl-pill-select {
    padding: 6px 28px 6px 12px; border-radius: 22px;
    font-size: .78rem; border: 1px solid rgba(0,0,0,.12); color: #475569;
    background: #fff; cursor: pointer; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
}
.sl-pill-select:focus { border-color: #d4af37; outline: none; box-shadow: 0 0 0 2px rgba(212,175,55,.15); }
.sl-search-pill {
    display: flex; align-items: center; gap: 6px;
    padding: 5px 14px; border-radius: 22px;
    border: 1px solid rgba(0,0,0,.1); background: #fff;
    font-size: .78rem; min-width: 200px;
}
.sl-search-pill i { color: #94a3b8; font-size: .9rem; }
.sl-search-pill input {
    border: none; outline: none; background: transparent;
    font-size: .78rem; color: #334155; width: 100%;
}
.sl-pill-clear {
    padding: 5px 12px; border-radius: 22px; font-size: .72rem;
    background: rgba(239,68,68,.08); color: #ef4444;
    text-decoration: none; font-weight: 600;
    display: flex; align-items: center; gap: 4px;
}
.sl-pill-clear:hover { background: rgba(239,68,68,.15); color: #ef4444; }
.sl-result-count { margin-left: auto; font-size: .75rem; color: #94a3b8; font-weight: 500; white-space: nowrap; }

/* Table */
.sl-tbl-wrap { overflow-x: auto; scrollbar-width: thin; scrollbar-color: #d4af37 transparent; }
.sl-tbl-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
.sl-tbl-wrap::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 10px; }
.sl-tbl {
    width: 100%; border-collapse: separate; border-spacing: 0; font-size: .78rem;
}
.sl-tbl thead th {
    padding: .55rem .6rem; font-weight: 700; font-size: .7rem;
    text-transform: uppercase; letter-spacing: .3px;
    color: #64748b; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid rgba(212,175,55,.18); white-space: nowrap;
}
.sl-tbl tbody td {
    padding: .45rem .6rem; border-bottom: 1px solid rgba(0,0,0,.04);
    vertical-align: middle; color: #334155;
}
.sl-tbl tbody tr { transition: background .12s; }
.sl-tbl tbody tr:hover td { background: rgba(212,175,55,.045); }
.sl-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.45); }
.sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.045); }

/* Badges */
.sl-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 20px; font-size: .7rem;
    font-weight: 600; white-space: nowrap;
}
.sl-badge-info { background: rgba(6,182,212,.1); color: #0891b2; }
.sl-badge-primary { background: rgba(59,130,246,.1); color: #2563eb; }
.sl-badge-success { background: rgba(34,197,94,.1); color: #16a34a; }
.sl-badge-warning { background: rgba(245,158,11,.1); color: #d97706; }
.sl-badge-danger { background: rgba(239,68,68,.1); color: #dc2626; }
.sl-badge-secondary { background: rgba(100,116,139,.1); color: #475569; }

/* Status toggle */
.sl-status-toggle {
    display: flex; gap: 2px; background: rgba(0,0,0,.04);
    border-radius: 16px; padding: 2px; width: fit-content;
}
.sl-status-option {
    padding: 4px 12px; border-radius: 14px; font-size: .72rem;
    font-weight: 600; cursor: pointer; border: none;
    background: transparent; color: #94a3b8; transition: all .15s;
}
.sl-status-option.active-yes { background: #22c55e; color: #fff; }
.sl-status-option.active-no { background: #ef4444; color: #fff; }

/* Action buttons */
.sl-act-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 12px; border-radius: 20px; font-size: .72rem;
    font-weight: 600; border: none; cursor: pointer; transition: all .15s;
}
.sl-act-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); }
.sl-act-btn-view { background: linear-gradient(135deg, #06b6d4, #0891b2); color: #fff; }
.sl-act-btn-save { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; }

/* BV form elements */
.sl-bv-textarea {
    border-radius: 12px; border: 1px solid rgba(0,0,0,.08);
    font-size: .75rem; padding: 6px 10px; resize: vertical;
    min-height: 38px; transition: border-color .15s;
}
.sl-bv-textarea:focus { border-color: #d4af37; outline: none; box-shadow: 0 0 0 2px rgba(212,175,55,.1); }
.sl-bv-status {
    padding: 4px 24px 4px 10px; border-radius: 16px; font-size: .72rem;
    border: 1px solid rgba(0,0,0,.08); font-weight: 600;
    appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 8px center;
}

/* Pagination */
.sl-card .pagination { margin: 0; }
.sl-card .pagination .page-link {
    border-radius: 10px; margin: 0 2px; font-size: .75rem;
    border: 1px solid rgba(0,0,0,.06); color: #64748b;
}
.sl-card .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #d4af37, #b8972e);
    border-color: transparent; color: #fff;
}
.sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

/* Empty state */
.sl-empty { text-align: center; padding: 48px 20px; color: #94a3b8; }
.sl-empty i { font-size: 2.5rem; margin-bottom: 8px; opacity: .5; }
.sl-empty p { font-size: .85rem; margin: 0; }

/* Toast */
.sl-toast {
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    padding: 12px 20px; border-radius: 14px; font-size: .82rem;
    font-weight: 600; color: #fff; box-shadow: 0 8px 24px rgba(0,0,0,.15);
    animation: slToastIn .3s ease-out;
    display: flex; align-items: center; gap: 8px;
}
.sl-toast-success { background: linear-gradient(135deg, #22c55e, #16a34a); }
.sl-toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); }
@keyframes slToastIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

/* Dark mode */
[data-bs-theme=dark] .sl-page-title, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: #f1f5f9; }
[data-bs-theme=dark] .sl-stat-card, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-stat-card { background: #1e293b; border-color: rgba(255,255,255,.06); }
[data-bs-theme=dark] .sl-stat-value, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-stat-value { color: #f1f5f9; }
[data-bs-theme=dark] .sl-card, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card { background: #1e293b; border-color: rgba(255,255,255,.06); }
[data-bs-theme=dark] .sl-card-title, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card-title { color: #f1f5f9; }
[data-bs-theme=dark] .sl-tbl thead th, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th { background: linear-gradient(180deg, #1e293b, #0f172a); color: #94a3b8; }
[data-bs-theme=dark] .sl-tbl tbody td, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td { color: #cbd5e1; border-bottom-color: rgba(255,255,255,.04); }
[data-bs-theme=dark] .sl-tbl tbody tr:hover td, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
[data-bs-theme=dark] .sl-tbl tbody tr:nth-child(even) td, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even) td { background: rgba(15,23,42,.3); }
[data-bs-theme=dark] .sl-pill-select, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select { background: #0f172a; border-color: rgba(255,255,255,.1); color: #cbd5e1; }
[data-bs-theme=dark] .sl-search-pill, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-pill { background: #0f172a; border-color: rgba(255,255,255,.1); }
[data-bs-theme=dark] .sl-search-pill input, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-pill input { color: #cbd5e1; }
[data-bs-theme=dark] .sl-tabs, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tabs { background: rgba(255,255,255,.04); }
[data-bs-theme=dark] .sl-tab.active, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab.active { background: #334155; color: #d4af37; }
[data-bs-theme=dark] .sl-bv-textarea, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-bv-textarea { background: #0f172a; border-color: rgba(255,255,255,.1); color: #cbd5e1; }
[data-bs-theme=dark] .sl-bv-status, :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-bv-status { background-color: #0f172a; border-color: rgba(255,255,255,.1); color: #cbd5e1; }

@media (max-width: 768px) {
    .sl-topbar { flex-direction: column; align-items: flex-start; }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Top Bar -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <div>
                <h1 class="sl-page-title"><i class="bx bx-file-find"></i>Submission & Followup</h1>
                <p class="sl-page-subtitle">Track policy followups & bank verification assignments</p>
            </div>
        </div>
    </div>

    <?php
        $followupYes = $leads->where('followup_status', 'Yes')->count();
        $followupNo = $leads->where('followup_status', 'No')->count();
        $followupTotal = $leads->total();
        $bvTotal = $bankVerificationLeads->total();
        $bvGood = $bankVerificationLeads->where('bank_verification_status', 'Good')->count();
        $bvBad = $bankVerificationLeads->where('bank_verification_status', 'Bad')->count();
    ?>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="sl-stat-card">
                <div class="sl-stat-icon gold"><i class="bx bx-list-check"></i></div>
                <div><div class="sl-stat-value"><?php echo e($followupTotal); ?></div><div class="sl-stat-label">My Followups</div></div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="sl-stat-card">
                <div class="sl-stat-icon green"><i class="bx bx-check-circle"></i></div>
                <div><div class="sl-stat-value"><?php echo e($followupYes); ?></div><div class="sl-stat-label">Completed</div></div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="sl-stat-card">
                <div class="sl-stat-icon red"><i class="bx bx-x-circle"></i></div>
                <div><div class="sl-stat-value"><?php echo e($followupNo); ?></div><div class="sl-stat-label">Pending</div></div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="sl-stat-card">
                <div class="sl-stat-icon blue"><i class="bx bx-shield-quarter"></i></div>
                <div><div class="sl-stat-value"><?php echo e($bvTotal); ?></div><div class="sl-stat-label">Bank Verifications</div></div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="sl-stat-card">
                <div class="sl-stat-icon green"><i class="bx bx-badge-check"></i></div>
                <div><div class="sl-stat-value"><?php echo e($bvGood); ?></div><div class="sl-stat-label">BV Good</div></div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="sl-stat-card">
                <div class="sl-stat-icon red"><i class="bx bx-error-alt"></i></div>
                <div><div class="sl-stat-value"><?php echo e($bvBad); ?></div><div class="sl-stat-label">BV Bad</div></div>
            </div>
        </div>
    </div>

    <!-- Section Tabs -->
    <div class="sl-tabs" id="sectionTabs">
        <button class="sl-tab active" data-target="followupSection">
            <i class="bx bx-list-check me-1"></i>Followups<span class="sl-tab-badge"><?php echo e($followupTotal); ?></span>
        </button>
        <button class="sl-tab" data-target="bvSection">
            <i class="bx bx-shield-quarter me-1"></i>Bank Verification<span class="sl-tab-badge"><?php echo e($bvTotal); ?></span>
        </button>
    </div>

    <!-- ═══ FOLLOWUP SECTION ═══ -->
    <div id="followupSection">
        <div class="sl-card">
            <div class="sl-card-header">
                <h5 class="sl-card-title"><i class="bx bx-user-check"></i>Leads Assigned to Me</h5>
            </div>
            <form method="GET" action="<?php echo e(route('followup.my-followups')); ?>" class="sl-filter-pills">
                <div class="sl-search-pill">
                    <i class="bx bx-search"></i>
                    <input type="text" name="search" placeholder="Search name, phone, carrier..." value="<?php echo e(request('search')); ?>">
                </div>
                <select name="carrier" class="sl-pill-select" onchange="this.form.submit()">
                    <option value="">All Carriers</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($carrier); ?>" <?php echo e(request('carrier') == $carrier ? 'selected' : ''); ?>><?php echo e($carrier); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <select name="followup_status" class="sl-pill-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Yes" <?php echo e(request('followup_status') == 'Yes' ? 'selected' : ''); ?>>Completed</option>
                    <option value="No" <?php echo e(request('followup_status') == 'No' ? 'selected' : ''); ?>>Pending</option>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','carrier','followup_status'])): ?>
                    <a href="<?php echo e(route('followup.my-followups')); ?>" class="sl-pill-clear"><i class="bx bx-x"></i> Clear</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="sl-result-count"><?php echo e($leads->total()); ?> leads</span>
            </form>
            <div class="sl-tbl-wrap">
                <table class="sl-tbl">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th style="min-width:140px">Client Name</th>
                            <th style="min-width:120px">Phone</th>
                            <th>Closer</th>
                            <th>Sale Date</th>
                            <th>Carrier</th>
                            <th>Policy Type</th>
                            <th>Policy #</th>
                            <th>Coverage</th>
                            <th>Premium</th>
                            <th style="min-width:110px">Status</th>
                            <th style="width:80px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($leads->firstItem() + $index); ?></strong></td>
                                <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                                <td><?php echo e($lead->phone_number); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?>
                                        <span class="sl-badge sl-badge-info"><?php echo e($lead->closer_name); ?></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></td>
                                <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                                <td><?php echo e($lead->policy_type ?? 'N/A'); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issued_policy_number): ?>
                                        <span class="sl-badge sl-badge-primary"><?php echo e($lead->issued_policy_number); ?></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8">Not Set</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="color:#d4af37;font-weight:600">$<?php echo e(number_format($lead->coverage_amount ?? 0, 2)); ?></td>
                                <td style="color:#d4af37;font-weight:600">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                                <td>
                                    <div class="sl-status-toggle">
                                        <button type="button" class="sl-status-option <?php echo e($lead->followup_status === 'No' ? 'active-no' : ''); ?>" data-lead-id="<?php echo e($lead->id); ?>" data-value="No">No</button>
                                        <button type="button" class="sl-status-option <?php echo e($lead->followup_status === 'Yes' ? 'active-yes' : ''); ?>" data-lead-id="<?php echo e($lead->id); ?>" data-value="Yes">Yes</button>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('issuance.show', $lead->id)); ?>" class="sl-act-btn sl-act-btn-view" title="View"><i class="bx bx-show"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="12">
                                    <div class="sl-empty"><i class="bx bx-inbox d-block"></i><p>No followups assigned to you</p></div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leads->hasPages()): ?>
                <div class="d-flex justify-content-center py-3"><?php echo e($leads->appends(request()->query())->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- ═══ BANK VERIFICATION SECTION ═══ -->
    <div id="bvSection" style="display:none">
        <div class="sl-card">
            <div class="sl-card-header">
                <h5 class="sl-card-title"><i class="bx bx-shield-quarter"></i>Bank Verification Assignments</h5>
            </div>
            <form method="GET" action="<?php echo e(route('followup.my-followups')); ?>" class="sl-filter-pills">
                <div class="sl-search-pill">
                    <i class="bx bx-search"></i>
                    <input type="text" name="bv_search" placeholder="Search name, phone, carrier..." value="<?php echo e(request('bv_search')); ?>">
                </div>
                <select name="bv_carrier" class="sl-pill-select" onchange="this.form.submit()">
                    <option value="">All Carriers</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($carrier); ?>" <?php echo e(request('bv_carrier') == $carrier ? 'selected' : ''); ?>><?php echo e($carrier); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <select name="bv_status" class="sl-pill-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Good" <?php echo e(request('bv_status') == 'Good' ? 'selected' : ''); ?>>Good</option>
                    <option value="Average" <?php echo e(request('bv_status') == 'Average' ? 'selected' : ''); ?>>Average</option>
                    <option value="Bad" <?php echo e(request('bv_status') == 'Bad' ? 'selected' : ''); ?>>Bad</option>
                </select>
                <?php if(request()->hasAny(['bv_search','bv_carrier','bv_status'])): ?>
                    <a href="<?php echo e(route('followup.my-followups')); ?>" class="sl-pill-clear"><i class="bx bx-x"></i> Clear</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="sl-result-count"><?php echo e($bankVerificationLeads->total()); ?> records</span>
            </form>
            <div class="sl-tbl-wrap">
                <table class="sl-tbl">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th style="min-width:140px">Client Name</th>
                            <th>Phone</th>
                            <th>Closer</th>
                            <th>Sale Date</th>
                            <th>Carrier</th>
                            <th>Policy Type</th>
                            <th>Policy #</th>
                            <th>Coverage</th>
                            <th>Premium</th>
                            <th>Assigned B.V</th>
                            <th style="min-width:160px">Comment</th>
                            <th style="min-width:100px">B.V Status</th>
                            <th style="width:80px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bankVerificationLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($bankVerificationLeads->firstItem() + $index); ?></strong></td>
                                <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                                <td><?php echo e($lead->phone_number); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?>
                                        <span class="sl-badge sl-badge-info"><?php echo e($lead->closer_name); ?></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></td>
                                <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                                <td><?php echo e($lead->policy_type ?? 'N/A'); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issued_policy_number): ?>
                                        <span class="sl-badge sl-badge-primary"><?php echo e($lead->issued_policy_number); ?></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8">Not Set</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="color:#d4af37;font-weight:600">$<?php echo e(number_format($lead->coverage_amount ?? 0, 2)); ?></td>
                                <td style="color:#d4af37;font-weight:600">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bankVerifier): ?>
                                        <span class="sl-badge sl-badge-success"><?php echo e($lead->bankVerifier->name); ?></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8">Unassigned</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <textarea class="sl-bv-textarea form-control bv-comment-input" data-lead-id="<?php echo e($lead->id); ?>" rows="1" placeholder="Add comment..."><?php echo e($lead->bank_verification_comment); ?></textarea>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bank_verification_status): ?>
                                        <?php
                                            $badgeCls = match($lead->bank_verification_status) {
                                                'Good' => 'sl-badge-success',
                                                'Average' => 'sl-badge-warning',
                                                'Bad' => 'sl-badge-danger',
                                                default => 'sl-badge-secondary'
                                            };
                                        ?>
                                        <span class="sl-badge <?php echo e($badgeCls); ?>"><?php echo e($lead->bank_verification_status); ?></span>
                                    <?php else: ?>
                                        <select class="sl-bv-status bv-status-dropdown" data-lead-id="<?php echo e($lead->id); ?>">
                                            <option value="">Select</option>
                                            <option value="Good">Good</option>
                                            <option value="Average">Average</option>
                                            <option value="Bad">Bad</option>
                                        </select>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <button class="sl-act-btn sl-act-btn-save update-bv-btn" data-lead-id="<?php echo e($lead->id); ?>" title="Update"><i class="bx bx-save"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="14">
                                    <div class="sl-empty"><i class="bx bx-inbox d-block"></i><p>No bank verification assignments found</p></div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bankVerificationLeads->hasPages()): ?>
                <div class="d-flex justify-content-center py-3"><?php echo e($bankVerificationLeads->appends(request()->query())->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
$(document).ready(function() {
    // Tab switching
    $('#sectionTabs .sl-tab').click(function() {
        $('#sectionTabs .sl-tab').removeClass('active');
        $(this).addClass('active');
        const target = $(this).data('target');
        $('#followupSection, #bvSection').hide();
        $('#' + target).show();
    });

    // Auto-activate BV tab if BV filters are active
    <?php if(request()->hasAny(['bv_search','bv_carrier','bv_status'])): ?>
        $('#sectionTabs .sl-tab').removeClass('active');
        $('#sectionTabs .sl-tab[data-target="bvSection"]').addClass('active');
        $('#followupSection').hide();
        $('#bvSection').show();
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    // Toast helper
    function showToast(type, msg) {
        const t = $('<div class="sl-toast sl-toast-' + type + '"><i class="bx ' + (type==='success'?'bx-check-circle':'bx-error') + '"></i>' + msg + '</div>');
        $('body').append(t);
        setTimeout(() => t.fadeOut(300, () => t.remove()), 3000);
    }

    // Followup status toggle
    $('.sl-status-option').click(function() {
        const leadId = $(this).data('lead-id');
        const value = $(this).data('value');
        const toggle = $(this).closest('.sl-status-toggle');
        toggle.find('.sl-status-option').removeClass('active-yes active-no');

        $.ajax({
            url: `/followup/${leadId}/update-status`,
            method: 'POST',
            data: { _token: '<?php echo e(csrf_token()); ?>', followup_status: value },
            success: function(response) {
                if (response.success) {
                    toggle.find('[data-value="' + value + '"]').addClass(value === 'Yes' ? 'active-yes' : 'active-no');
                    showToast('success', 'Status updated');
                }
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Failed to update');
                location.reload();
            }
        });
    });

    // Bank verification update
    $('.update-bv-btn').click(function() {
        const leadId = $(this).data('lead-id');
        const btn = $(this);
        const row = btn.closest('tr');
        const comment = row.find('.bv-comment-input').val();
        const statusDropdown = row.find('.bv-status-dropdown');
        const status = statusDropdown.length ? statusDropdown.val() : null;

        if (!status && statusDropdown.length) {
            showToast('error', 'Please select a status first');
            return;
        }

        btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');

        $.ajax({
            url: `/followup/${leadId}/update-bank-verification`,
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                bank_verification_comment: comment,
                bank_verification_status: status
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Failed to update');
                btn.prop('disabled', false).html('<i class="bx bx-save"></i>');
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/followup/my-followups.blade.php ENDPATH**/ ?>