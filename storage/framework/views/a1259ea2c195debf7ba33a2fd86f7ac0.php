<?php use \App\Support\Statuses; ?>


<?php $__env->startSection('title', 'Pending Contracts'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   Pending Contracts — Company Overview Style
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
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
.kpi-card.active { box-shadow: 0 0 0 2px var(--bs-gold, #d4af37); transform: translateY(-2px); }
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

.kpi-card.k-gray    { background: rgba(108,117,125,.05); }
.kpi-card.k-gray::before    { background: linear-gradient(90deg, #6c757d, #95a0a8); }
.kpi-card.k-gray .k-val, .kpi-card.k-gray .k-icon { color: #6c757d; }

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

/* ── Compact Table ── */
.ex-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.75rem;
}
.ex-tbl thead th {
    text-transform: uppercase;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.45rem 0.6rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
    position: sticky;
    top: 0;
    z-index: 1;
}
.ex-tbl tbody td {
    padding: 0.45rem 0.6rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    vertical-align: middle;
    white-space: nowrap;
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
.bd-mini.bd-gray   { background: rgba(108,117,125,.12); color: #6c757d; }
.bd-mini.bd-purple { background: rgba(124,105,239,.12); color: #5b49c7; }

/* Scrollable table wrapper */
.scroll-tbl { overflow-x: auto; overflow-y: auto; max-height: 550px; }
.scroll-tbl::-webkit-scrollbar { width: 3px; height: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }
.ex-tbl { min-width: 1100px; }

/* ── Filter Row ── */
.filter-form { display: flex; flex-wrap: wrap; gap: 0.4rem; padding: 0.5rem 0.75rem; }
.filter-form .f-input {
    border: 1px solid var(--bs-surface-300);
    border-radius: 1rem;
    padding: 0.28rem 0.6rem;
    font-size: 0.72rem;
    background: transparent;
    color: inherit;
    outline: none;
    transition: border-color .15s;
}
.filter-form .f-input:focus { border-color: var(--bs-gold, #d4af37); box-shadow: 0 0 0 2px rgba(212,175,55,.1); }

/* Pill-select & pill-date base */
.sl-pill-select, .sl-pill-date {
    font-size: .72rem; font-weight: 600;
    padding: .32rem .55rem; border-radius: 22px !important;
    border: 1px solid rgba(0,0,0,.08) !important;
    background: #fff; color: #475569;
    cursor: pointer; outline: none;
    transition: border-color .15s;
}
.sl-pill-select {
    -webkit-appearance: none; -moz-appearance: none; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .5rem center;
    padding-right: 1.5rem;
    max-width: 180px;
}
.sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37 !important; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

/* Dark mode — pill filters */
:is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select,
:is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date {
    background: rgba(30,41,59,.8) !important; border-color: rgba(255,255,255,.1) !important; color: #cbd5e1;
}
:is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E") !important;
}

.filter-form .f-btn {
    background: var(--bs-gold, #d4af37);
    border: none;
    border-radius: 1rem;
    padding: 0.28rem 0.7rem;
    font-size: 0.68rem;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    transition: opacity .15s;
}
.filter-form .f-btn:hover { opacity: .85; }
.filter-form .f-reset {
    background: transparent;
    border: 1px solid var(--bs-surface-300);
    border-radius: 1rem;
    padding: 0.28rem 0.6rem;
    font-size: 0.68rem;
    font-weight: 500;
    color: var(--bs-surface-500);
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.filter-form .f-reset:hover { border-color: var(--bs-gold); color: var(--bs-gold); }

/* Inline controls */
.ex-tbl .form-select-sm {
    -webkit-appearance: none; -moz-appearance: none; appearance: none;
    border-radius: 22px;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.22rem 1.4rem 0.22rem 0.5rem;
    border: 1px solid rgba(0,0,0,.08);
    background-color: transparent;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .45rem center;
    background-size: 8px 5px;
    cursor: pointer;
    color: inherit;
}
.ex-tbl .form-select-sm:focus {
    border-color: var(--bs-gold, #d4af37);
    box-shadow: 0 0 0 2px rgba(212,175,55,.1);
}

/* ── Action buttons ── */
.a-btn {
    border: none;
    border-radius: 0.3rem;
    padding: 0.18rem 0.4rem;
    font-size: 0.65rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
}
.a-btn.a-view { background: rgba(80,165,241,.1); color: #2b81c9; }
.a-btn.a-view:hover { background: rgba(80,165,241,.2); }
.a-btn.a-edit { background: rgba(212,175,55,.1); color: #b89730; }
.a-btn.a-edit:hover { background: rgba(212,175,55,.2); }
.a-btn.a-draft { background: rgba(124,105,239,.1); color: #7c69ef; border: 1px solid rgba(124,105,239,.15); }
.a-btn.a-draft:hover { background: rgba(124,105,239,.2); }
.a-btn.a-draft.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
.a-btn.a-paid { background: rgba(52,195,143,.12); color: #1a8754; border: 1px solid rgba(52,195,143,.25); }
.a-btn.a-paid:hover { background: rgba(52,195,143,.2); }
.a-btn.a-fdfp { background: rgba(244,106,106,.12); color: #c84646; border: 1px solid rgba(244,106,106,.25); }
.a-btn.a-fdfp:hover { background: rgba(244,106,106,.2); }
.iss-modal .modal-backdrop, .modal-backdrop.show { opacity: 0.3 !important; }

/* Pagination */
.ex-card .pagination { margin: 0; }
.ex-card .pagination .page-link {
    border-radius: 0.35rem; margin: 0 1px; font-size: 0.7rem;
    border: 1px solid var(--bs-surface-200); color: var(--bs-surface-500);
    padding: 0.2rem 0.5rem;
}
.ex-card .pagination .page-item.active .page-link {
    background: var(--bs-gold, #d4af37); border-color: var(--bs-gold); color: #fff;
}
.ex-card .pagination svg { max-width: 14px !important; max-height: 14px !important; }

@media(max-width:768px){
    .kpi-card .k-val { font-size: 1.1rem; }
    .filter-form { flex-direction: column; }
    .filter-form .f-input { width: 100%; }
}
/* Prominent page title */
.sl-page-title{font-size:1.35rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px;margin:0;}
.sl-page-title i{color:#d4af37;font-size:1.5rem;}
.sl-page-subtitle{font-size:.78rem;color:#94a3b8;margin:0;}
[data-bs-theme=dark] .sl-page-title,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="container-fluid px-0 pb-2" style="max-width:1600px;">
        <h1 class="sl-page-title"><i class="bx bx-file-find"></i> Pending Contracts</h1>
        <p class="sl-page-subtitle mt-1">Stage 5 — Monitor issuance status and assign followup officers</p>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:0.5rem; border:none; background:rgba(52,195,143,.08); color:#1a8754; font-size:.78rem; padding:.5rem .75rem;">
            <i class="bx bx-check-circle me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem;padding:.6rem;"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $totalLeads = $leads->total();
    ?>

    
    <div class="kpi-row">
        <a href="<?php echo e(route('issuance.index', array_merge(request()->except('issuance_status', 'followup_status', 'view'), ['issuance_status' => 'pending']))); ?>" class="kpi-card k-gray ex-card" style="text-decoration:none;cursor:pointer;">
            <i class="bx bx-loader-alt k-icon"></i>
            <div class="k-val"><?php echo e($kpiCounts['pending']); ?></div>
            <div class="k-lbl">Pending</div>
        </a>
        <a href="<?php echo e(route('issuance.index', array_merge(request()->except('issuance_status', 'followup_status', 'view'), ['issuance_status' => 'Issued']))); ?>" class="kpi-card k-green ex-card" style="text-decoration:none;cursor:pointer;">
            <i class="bx bx-check-circle k-icon"></i>
            <div class="k-val"><?php echo e($kpiCounts['issued']); ?></div>
            <div class="k-lbl">Issued</div>
        </a>
        <a href="<?php echo e(route('issuance.index', array_merge(request()->except('issuance_status', 'followup_status', 'view'), ['issuance_status' => 'Not Issued']))); ?>" class="kpi-card k-red ex-card" style="text-decoration:none;cursor:pointer;">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val"><?php echo e($kpiCounts['not_issued']); ?></div>
            <div class="k-lbl">Not Issued</div>
        </a>
        <a href="<?php echo e(route('issuance.index', array_merge(request()->except('issuance_status', 'followup_status', 'view'), ['issuance_status' => 'Issued', 'followup_status' => 'Yes']))); ?>" class="kpi-card k-teal ex-card" style="text-decoration:none;cursor:pointer;">
            <i class="bx bx-user-check k-icon"></i>
            <div class="k-val"><?php echo e($kpiCounts['ready_for_draft']); ?></div>
            <div class="k-lbl">Ready for Draft</div>
        </a>
        <a href="<?php echo e(route('issuance.index', ['view' => 'sent_to_draft'])); ?>" class="kpi-card k-gold ex-card <?php echo e(request('view') === 'sent_to_draft' ? 'active' : ''); ?>" style="text-decoration:none;cursor:pointer;">
            <i class="bx bx-send k-icon"></i>
            <div class="k-val"><?php echo e($kpiCounts['sent_to_draft']); ?></div>
            <div class="k-lbl">Sent to Draft</div>
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('view') === 'sent_to_draft'): ?>
    
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-send me-1"></i> Sent to Pending Draft</h6>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:0.62rem; color:var(--bs-surface-400);"><?php echo e(isset($sentToDraft) ? $sentToDraft->count() : 0); ?> records</span>
                <a href="<?php echo e(route('issuance.index')); ?>" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.1);font-size:.65rem;">
                    <i class="bx bx-arrow-back"></i> Back to Contracts
                </a>
            </div>
        </div>
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Sent At</th>
                        <th>Sent By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sentToDraft; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="color:var(--bs-surface-400);"><?php echo e($idx + 1); ?></td>
                            <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                            <td style="font-size:.7rem;"><?php echo e($lead->phone_number ?? '—'); ?></td>
                            <td style="font-size:.7rem;"><?php echo e($lead->pending_draft_at->format('M d, Y h:i A')); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->pendingDraftBy): ?>
                                    <span class="bd-mini bd-teal"><?php echo e($lead->pendingDraftBy->name); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-3" style="color:var(--bs-surface-400); font-size:.78rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem; opacity:.4;"></i>
                                <p class="mt-1 mb-0">No leads sent to pending draft yet</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-check"></i> Pending Contracts</h6>
            <span style="font-size:0.62rem; color:var(--bs-surface-400);"><?php echo e($totalLeads); ?> records</span>
        </div>
        <form method="GET" action="<?php echo e(route('issuance.index')); ?>" class="filter-form">
            <input type="text" name="search" class="f-input" style="min-width:160px;" placeholder="Search name, phone, carrier..." value="<?php echo e(request('search')); ?>">
            <input type="date" name="date_from" class="f-input" style="min-width:130px;" value="<?php echo e(request('date_from')); ?>" title="From date">
            <input type="date" name="date_to" class="f-input" style="min-width:130px;" value="<?php echo e(request('date_to')); ?>" title="To date">
            <select name="carrier" class="sl-pill-select">
                <option value="">All Carriers</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($carrier->id); ?>" <?php echo e(request('carrier') == $carrier->id ? 'selected' : ''); ?>><?php echo e($carrier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <select name="partner" class="sl-pill-select">
                <option value="">All Partners</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php echo e(request('partner') == $p->id ? 'selected' : ''); ?>><?php echo e($p->code ?: $p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <select name="issuance_status" class="sl-pill-select">
                <option value="">All Status</option>
                <option value="pending" <?php echo e(request('issuance_status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="Issued" <?php echo e(request('issuance_status') == 'Issued' ? 'selected' : ''); ?>>Issued</option>
                <option value="Not Issued" <?php echo e(request('issuance_status') == 'Not Issued' ? 'selected' : ''); ?>>Not Issued</option>
            </select>
            <select name="followup_status" class="sl-pill-select">
                <option value="">Followup</option>
                <option value="Yes" <?php echo e(request('followup_status') == 'Yes' ? 'selected' : ''); ?>>Yes</option>
                <option value="No" <?php echo e(request('followup_status') == 'No' ? 'selected' : ''); ?>>No</option>
            </select>
            <select name="policy_type" class="sl-pill-select">
                <option value="">Policy Type</option>
                <option value="G.I" <?php echo e(request('policy_type') == 'G.I' ? 'selected' : ''); ?>>G.I</option>
                <option value="Graded" <?php echo e(request('policy_type') == 'Graded' ? 'selected' : ''); ?>>Graded</option>
                <option value="Level" <?php echo e(request('policy_type') == 'Level' ? 'selected' : ''); ?>>Level</option>
                <option value="Modified" <?php echo e(request('policy_type') == 'Modified' ? 'selected' : ''); ?>>Modified</option>
            </select>
            <button type="submit" class="f-btn"><i class="bx bx-search"></i> Filter</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','carrier','partner','issuance_status','followup_status','policy_type','date_from','date_to'])): ?>
                <a href="<?php echo e(route('issuance.index')); ?>" class="f-reset"><i class="bx bx-reset"></i> Clear</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Closer</th>
                        <th>Sale Date</th>
                        <th>Carrier</th>
                        <th>Type</th>
                        <th>App ID</th>
                        <th>Policy #</th>
                        <th>Partner</th>
                        <th class="text-center">Coverage / Premium</th>
                        <th>Draft Dates</th>
                        <th class="text-center">Status</th>
                        <th style="min-width:130px;">Issued By</th>
                        <th style="min-width:120px;">Followup Person</th>
                        <th style="min-width:130px;">F/U Assigned By</th>
                        <th class="text-center">F/U</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="color:var(--bs-surface-400);"><?php echo e($leads->firstItem() + $idx); ?></td>
                            <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                            <td style="font-size:.7rem;"><?php echo e($lead->phone_number); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?>
                                    <span class="bd-mini bd-teal"><?php echo e($lead->closer_name); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.68rem; white-space:nowrap;"><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A'); ?></td>
                            <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                            <td><?php echo e($lead->policy_type ?? 'N/A'); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->app_id): ?>
                                    <code style="font-size:.7rem; color:var(--bs-primary);"><?php echo e($lead->app_id); ?></code>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400); font-size:.7rem;">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->policy_number || $lead->issued_policy_number): ?>
                                    <code style="font-size:.7rem;"><?php echo e($lead->policy_number ?? $lead->issued_policy_number); ?></code>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400); font-size:.7rem;">Not Set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->partner): ?>
                                    <span class="bd-mini bd-green"><?php echo e($lead->partner->name); ?></span>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="bd-mini bd-gold">$<?php echo e(number_format($lead->coverage_amount ?? 0, 0)); ?></span>
                                <span style="color:var(--bs-surface-400); margin:0 2px;">/</span>
                                <span class="bd-mini bd-blue">$<?php echo e(number_format($lead->monthly_premium ?? 0, 0)); ?></span>
                            </td>
                            <td style="font-size:.68rem;">
                                <div>I: <?php echo e($lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M j, Y') : '—'); ?></div>
                                <div>F: <?php echo e($lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M j, Y') : '—'); ?></div>
                            </td>
                            <td class="text-center">
                                <?php
                                    $statusBdClass = match($lead->issuance_status) {
                                        Statuses::ISSUANCE_ISSUED => 'bd-green',
                                        'Not Issued' => 'bd-red',
                                        'Incomplete' => 'bd-warn',
                                        default => 'bd-gray'
                                    };
                                ?>
                                <span class="bd-mini <?php echo e($statusBdClass); ?>"><?php echo e($lead->issuance_status ?? 'Pending'); ?></span>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issuedByUser): ?>
                                    <strong style="font-size:.72rem;"><?php echo e($lead->issuedByUser->name); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->issuance_date): ?>
                                        <div style="font-size:.62rem;color:var(--bs-surface-400);margin-top:1px">
                                            <?php echo e(\Carbon\Carbon::parse($lead->issuance_date)->format('M d, h:i A')); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);font-size:.7rem">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <select class="form-select form-select-sm followup-person-dropdown" data-lead-id="<?php echo e($lead->id); ?>" data-current-person="<?php echo e($lead->assigned_followup_person); ?>">
                                    <option value="">Select</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $followupUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($employee->id); ?>" <?php echo e($lead->assigned_followup_person == $employee->id ? 'selected' : ''); ?>>
                                            <?php echo e($employee->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->followupAssignedByUser): ?>
                                    <strong style="font-size:.72rem;"><?php echo e($lead->followupAssignedByUser->name); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->followup_assigned_at): ?>
                                        <div style="font-size:.62rem;color:var(--bs-surface-400);margin-top:1px">
                                            <?php echo e(\Carbon\Carbon::parse($lead->followup_assigned_at)->format('M d, h:i A')); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span style="color:var(--bs-surface-400);font-size:.7rem">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->followup_status === Statuses::MIS_YES): ?>
                                    <span class="bd-mini bd-green">Yes</span>
                                <?php else: ?>
                                    <span class="bd-mini bd-red">No</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $canSendToDraft = $lead->issuance_status === Statuses::ISSUANCE_ISSUED 
                                                   && $lead->followup_status === Statuses::MIS_YES;
                                    $isPending = !$lead->issuance_status || $lead->issuance_status === 'Pending' || $lead->issuance_status === 'Incomplete';
                                    $isIssued = $lead->issuance_status === Statuses::ISSUANCE_ISSUED;
                                    $isNotIssued = in_array($lead->issuance_status, array_keys(\App\Support\Statuses::NOT_ISSUED_DISPOSITIONS));
                                ?>
                                <div class="d-flex gap-1 flex-wrap">
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPending): ?>
                                        <button type="button" class="a-btn a-paid btn-mark-issued" 
                                            data-id="<?php echo e($lead->id); ?>" 
                                            data-name="<?php echo e($lead->cn_name); ?>"
                                            title="Mark as Issued">
                                            <i class="bx bx-check"></i> Issued
                                        </button>
                                        <button type="button" class="a-btn a-fdfp btn-mark-not-issued" 
                                            data-id="<?php echo e($lead->id); ?>" 
                                            data-name="<?php echo e($lead->cn_name); ?>"
                                            title="Mark as Not Issued">
                                            <i class="bx bx-x"></i> Not Issued
                                        </button>
                                    <?php elseif($isIssued): ?>
                                        
                                        <button type="button" class="a-btn a-fdfp btn-mark-not-issued" 
                                            data-id="<?php echo e($lead->id); ?>" 
                                            data-name="<?php echo e($lead->cn_name); ?>"
                                            title="Change to Not Issued">
                                            <i class="bx bx-x"></i> Not Issued
                                        </button>
                                    <?php elseif($isNotIssued): ?>
                                        
                                        <button type="button" class="a-btn a-paid btn-mark-issued" 
                                            data-id="<?php echo e($lead->id); ?>" 
                                            data-name="<?php echo e($lead->cn_name); ?>"
                                            title="Change to Issued">
                                            <i class="bx bx-check"></i> Issued
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSendToDraft): ?>
                                        <button type="button" class="a-btn a-draft btn-send-to-draft" 
                                            data-lead-id="<?php echo e($lead->id); ?>" 
                                            data-lead-name="<?php echo e($lead->cn_name); ?>"
                                            title="Send to Pending Draft">
                                            <i class="bx bx-right-arrow-alt"></i> Pending Draft
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    
                                    <button type="button" class="a-btn btn-send-back" 
                                        data-id="<?php echo e($lead->id); ?>" 
                                        data-name="<?php echo e($lead->cn_name); ?>"
                                        title="Send back to previous stage"
                                        style="background:rgba(220,53,69,.1);color:#dc3545;border-color:rgba(220,53,69,.25);">
                                        <i class="bx bx-arrow-back"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="18" class="text-center py-3" style="color:var(--bs-surface-400); font-size:.78rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem; opacity:.4;"></i>
                                <p class="mt-1 mb-0">No submission data available</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leads->hasPages()): ?>
            <div class="d-flex justify-content-center" style="padding:0.5rem;">
                <?php echo e($leads->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="modal fade" id="notIssuedModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title mb-0" style="font-size:.85rem;">
                        <i class="bx bx-x-circle me-1 text-danger"></i> Mark as Not Issued
                    </h6>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-3 py-3">
                    <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                        Lead: <strong id="ni-lead-name"></strong>
                    </p>
                    <p class="mb-2" style="font-size:.72rem;color:#c84646;">
                        <i class="bx bx-info-circle me-1"></i>
                        Lead will be sent to Retention for resolution.
                    </p>
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Disposition (Reason)</label>
                    <select id="ni-disposition" class="form-select form-select-sm">
                        <option value="">— Select disposition —</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $niDispositions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="ni-confirm-btn">Confirm Not Issued</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('script'); ?>
<script>
// Simple toast notification
function slToast(msg) {
    const t = document.createElement('div');
    t.innerHTML = '<i class="bx bx-check-circle" style="color:#1a8754;font-size:0.9rem;"></i> ' + msg;
    t.style.cssText = 'position:fixed;top:16px;right:16px;z-index:9999;background:var(--bs-card-bg);border:1px solid rgba(52,195,143,.3);border-radius:0.5rem;padding:0.5rem 0.85rem;font-size:0.75rem;box-shadow:0 4px 16px rgba(0,0,0,.1);display:flex;align-items:center;gap:0.4rem;';
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo e(csrf_token()); ?>';
    let currentNotIssuedId = null;
    let notIssuedModal = null;

    // ── Live search: debounce auto-submit on search input ──
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let debounceTimer = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.closest('form').submit();
            }, 400);
        });
    }
    
    // Get or create the modal instance (singleton pattern)
    function getNotIssuedModal() {
        if (!notIssuedModal) {
            const modalEl = document.getElementById('notIssuedModal');
            notIssuedModal = new bootstrap.Modal(modalEl);
            // Clean up backdrop on hide
            modalEl.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            });
        }
        return notIssuedModal;
    }
    
    // Handle followup person dropdown changes
    document.querySelectorAll('.followup-person-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            if (this.dataset.processing === 'true') return;
            
            const leadId = this.dataset.leadId;
            const followupPersonId = this.value;
            const currentPerson = this.dataset.currentPerson;
            
            this.dataset.processing = 'true';
            if (!confirm('Assign this person for followup?')) {
                this.value = currentPerson;
                this.dataset.processing = 'false';
                return;
            }
            
            this.disabled = true;
            fetch('/followup/' + leadId + '/assign-person', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ assigned_followup_person: followupPersonId })
            })
            .then(r => r.json())
            .then(data => {
                this.disabled = false;
                this.dataset.processing = 'false';
                if (data.success) {
                    slToast(data.message || 'Assigned successfully');
                    this.dataset.currentPerson = followupPersonId;
                }
            })
            .catch(err => {
                this.disabled = false;
                this.dataset.processing = 'false';
                this.value = currentPerson;
                alert('Failed to assign followup person');
            });
        });
    });

    // Mark as Issued button
    document.querySelectorAll('.btn-mark-issued').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.dataset.processing === 'true') return;
            
            const leadId = this.dataset.id;
            const leadName = this.dataset.name;
            const button = this;
            
            button.dataset.processing = 'true';
            if (!confirm('Mark "' + leadName + '" as Issued?')) {
                button.dataset.processing = 'false';
                return;
            }
            
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            
            fetch('/pending-contracts/' + leadId + '/mark-issued', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    slToast(data.message || 'Marked as Issued');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert(data.message || 'Failed');
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-check"></i> Issued';
                    button.dataset.processing = 'false';
                }
            })
            .catch(err => {
                alert('Failed to mark as Issued');
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-check"></i> Issued';
                button.dataset.processing = 'false';
            });
        });
    });

    // Mark as Not Issued button - opens modal
    document.querySelectorAll('.btn-mark-not-issued').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentNotIssuedId = this.dataset.id;
            document.getElementById('ni-lead-name').textContent = this.dataset.name;
            document.getElementById('ni-disposition').value = '';
            document.getElementById('ni-confirm-btn').disabled = false;
            document.getElementById('ni-confirm-btn').innerHTML = 'Confirm Not Issued';
            getNotIssuedModal().show();
        });
    });

    // Confirm Not Issued button in modal
    document.getElementById('ni-confirm-btn').addEventListener('click', function() {
        const disposition = document.getElementById('ni-disposition').value;
        if (!disposition) {
            alert('Please select a disposition.');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
        
        fetch('/pending-contracts/' + currentNotIssuedId + '/mark-not-issued', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ not_issued_disposition: disposition })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                getNotIssuedModal().hide();
                slToast(data.message || 'Marked as Not Issued');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert(data.message || 'Failed');
                document.getElementById('ni-confirm-btn').disabled = false;
                document.getElementById('ni-confirm-btn').innerHTML = 'Confirm Not Issued';
            }
        })
        .catch(err => {
            alert('Failed to mark as Not Issued');
            document.getElementById('ni-confirm-btn').disabled = false;
            document.getElementById('ni-confirm-btn').innerHTML = 'Confirm Not Issued';
        });
    });

    // Send to Pending Draft button
    document.querySelectorAll('.btn-send-to-draft').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.dataset.processing === 'true') return;
            
            const leadId = this.dataset.leadId;
            const leadName = this.dataset.leadName;
            const button = this;
            
            button.dataset.processing = 'true';
            if (!confirm('Send "' + leadName + '" to Pending Draft?')) {
                button.dataset.processing = 'false';
                return;
            }
            
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Sending...';
            
            fetch('/pending-contracts/' + leadId + '/send-to-pending-draft', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    slToast(data.message || 'Sent to Pending Draft');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert(data.message || 'Failed');
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-right-arrow-alt"></i> Pending Draft';
                    button.dataset.processing = 'false';
                }
            })
            .catch(err => {
                alert('Failed to send to Pending Draft');
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-right-arrow-alt"></i> Pending Draft';
                button.dataset.processing = 'false';
            });
        });
    });

    // Send Back to Previous Stage button
    document.querySelectorAll('.btn-send-back').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.dataset.processing === 'true') return;
            
            const leadId = this.dataset.id;
            const leadName = this.dataset.name;
            const button = this;
            
            button.dataset.processing = 'true';
            if (!confirm('Send "' + leadName + '" back to Submissions?')) {
                button.dataset.processing = 'false';
                return;
            }
            
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            
            fetch('/leads/' + leadId + '/send-to-previous-stage', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => {
                if (!r.ok) {
                    return r.text().then(text => {
                        throw new Error('Server error (' + r.status + '): ' + (text.substring(0, 100) || 'Unknown error'));
                    });
                }
                return r.json();
            })
            .then(data => {
                if (data.success) {
                    slToast(data.message || 'Sent to Submissions');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert(data.message || 'Failed');
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-arrow-back"></i>';
                    button.dataset.processing = 'false';
                }
            })
            .catch(err => {
                console.error('Send back failed:', err);
                alert('Failed to send back: ' + err.message);
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-arrow-back"></i>';
                button.dataset.processing = 'false';
            });
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/issuance/index.blade.php ENDPATH**/ ?>