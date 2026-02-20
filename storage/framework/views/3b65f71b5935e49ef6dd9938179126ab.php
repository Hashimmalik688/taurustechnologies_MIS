<?php $__env->startSection('title'); ?> Bank Verification <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ── SL Design System ── */
.sl-topbar {
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 16px; padding: 18px 28px; margin-bottom: 24px;
    box-shadow: 0 4px 24px rgba(0,0,0,.12);
}
.sl-topbar-left { display: flex; align-items: center; gap: 14px; }
.sl-topbar-title { color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0; }
.sl-topbar-title i { color: #d4af37; font-size: 1.5rem; }
.sl-topbar-sub { color: rgba(255,255,255,.55); font-size: .82rem; margin: 0; }
.sl-topbar-right { display: flex; align-items: center; gap: 10px; }

/* ── Stat Cards ── */
.sl-stat {
    background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: 16px;
    padding: 20px 22px; position: relative; overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.sl-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.1); }
.sl-stat-icon {
    width: 46px; height: 46px; border-radius: 12px; display: flex;
    align-items: center; justify-content: center; font-size: 1.35rem; color: #fff; margin-bottom: 12px;
}
.sl-stat-label { font-size: .78rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
.sl-stat-value { font-size: 1.65rem; font-weight: 800; color: #1e293b; margin: 0; }
.sl-stat-bar { height: 4px; border-radius: 4px; background: #f1f5f9; margin-top: 10px; overflow: hidden; }
.sl-stat-bar-fill { height: 100%; border-radius: 4px; transition: width .6s ease; }

/* ── Chart Card ── */
.sl-chart-card {
    background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: 16px;
    padding: 22px; margin-bottom: 24px;
}
.sl-chart-title { font-size: .95rem; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
.sl-chart-title i { color: #d4af37; margin-right: 8px; }

/* ── Card ── */
.sl-card {
    background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.04); overflow: hidden; margin-bottom: 24px;
}
.sl-card-header {
    padding: 16px 22px; border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex; align-items: center; justify-content: space-between;
}
.sl-card-title { font-size: .95rem; font-weight: 700; color: #1e293b; margin: 0; }
.sl-card-title i { color: #d4af37; margin-right: 8px; }
.sl-card-body { padding: 20px 22px; }

/* ── Filters ── */
.sl-filter-row { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 18px; }
.sl-filter-input {
    border: 1px solid rgba(0,0,0,.1); border-radius: 22px; padding: 7px 16px;
    font-size: .82rem; background: #fff; transition: border-color .2s;
    outline: none;
}
.sl-filter-input:focus { border-color: #d4af37; box-shadow: 0 0 0 3px rgba(212,175,55,.12); }
.sl-filter-select {
    border: 1px solid rgba(0,0,0,.1); border-radius: 22px; padding: 7px 16px;
    font-size: .82rem; background: #fff; cursor: pointer; min-width: 120px;
}
.sl-filter-select:focus { border-color: #d4af37; box-shadow: 0 0 0 3px rgba(212,175,55,.12); outline: none; }
.sl-btn-filter {
    background: linear-gradient(135deg, #d4af37, #b8962e); color: #fff;
    border: none; border-radius: 22px; padding: 7px 22px; font-size: .82rem;
    font-weight: 600; cursor: pointer; transition: opacity .2s;
}
.sl-btn-filter:hover { opacity: .88; color: #fff; }
.sl-btn-reset {
    background: transparent; color: #64748b; border: 1px solid rgba(0,0,0,.1);
    border-radius: 22px; padding: 7px 18px; font-size: .82rem; font-weight: 500;
    cursor: pointer; transition: all .2s; text-decoration: none;
}
.sl-btn-reset:hover { background: #f8f9fa; color: #1e293b; }

/* ── Table ── */
.sl-tbl { width: 100%; border-collapse: separate; border-spacing: 0; }
.sl-tbl thead th {
    background: #f8f9fa; font-size: .75rem; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: .5px; padding: .6rem .7rem;
    border-bottom: 2px solid rgba(0,0,0,.06); white-space: nowrap;
}
.sl-tbl tbody td {
    padding: .55rem .7rem; font-size: .83rem; color: #334155;
    border-bottom: 1px solid rgba(0,0,0,.04); vertical-align: middle;
}
.sl-tbl tbody tr:hover { background: rgba(212,175,55,.04); }
.sl-tbl .sl-premium { color: #d4af37; font-weight: 700; }
.sl-tbl .form-select-sm, .sl-tbl .form-control-sm {
    border-radius: 10px; font-size: .8rem; border: 1px solid rgba(0,0,0,.1);
}
.sl-tbl .form-select-sm:focus, .sl-tbl .form-control-sm:focus {
    border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12);
}

/* ── Status Badges ── */
.sl-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 12px; border-radius: 22px; font-size: .72rem;
    font-weight: 600; letter-spacing: .3px;
}
.sl-badge-good { background: rgba(34,197,94,.1); color: #16a34a; }
.sl-badge-average { background: rgba(245,158,11,.1); color: #d97706; }
.sl-badge-bad { background: rgba(239,68,68,.1); color: #dc2626; }
.sl-badge-unset { background: rgba(100,116,139,.1); color: #64748b; }

/* ── Action Buttons ── */
.sl-btn-view, .sl-btn-update {
    border: none; border-radius: 22px; padding: 5px 14px; font-size: .76rem;
    font-weight: 600; cursor: pointer; transition: all .2s; text-decoration: none;
}
.sl-btn-view { background: rgba(59,130,246,.1); color: #2563eb; }
.sl-btn-view:hover { background: rgba(59,130,246,.2); color: #1d4ed8; }
.sl-btn-update { background: rgba(212,175,55,.1); color: #b8962e; }
.sl-btn-update:hover { background: rgba(212,175,55,.2); color: #96791f; }

/* ── Modal ── */
.sl-modal .modal-content { border-radius: 16px; border: none; overflow: hidden; }
.sl-modal .modal-header {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    padding: 16px 22px; border: none;
}
.sl-modal .modal-header .modal-title { color: #fff; font-weight: 700; font-size: .95rem; }
.sl-modal .modal-header .modal-title i { color: #d4af37; }
.sl-modal .modal-body { padding: 22px; }
.sl-modal .modal-footer { border-top: 1px solid rgba(0,0,0,.06); padding: 14px 22px; }
.sl-modal .sl-status-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 20px; border-radius: 12px; border: 2px solid transparent;
    font-size: .9rem; font-weight: 700; cursor: pointer; width: 100%;
    transition: all .2s;
}
.sl-modal .sl-status-good { background: rgba(34,197,94,.08); color: #16a34a; border-color: rgba(34,197,94,.2); }
.sl-modal .sl-status-good:hover { background: #16a34a; color: #fff; }
.sl-modal .sl-status-avg { background: rgba(245,158,11,.08); color: #d97706; border-color: rgba(245,158,11,.2); }
.sl-modal .sl-status-avg:hover { background: #d97706; color: #fff; }
.sl-modal .sl-status-bad { background: rgba(239,68,68,.08); color: #dc2626; border-color: rgba(239,68,68,.2); }
.sl-modal .sl-status-bad:hover { background: #dc2626; color: #fff; }
.sl-modal textarea.form-control { border-radius: 12px; font-size: .85rem; }
.sl-modal textarea.form-control:focus { border-color: #d4af37; box-shadow: 0 0 0 3px rgba(212,175,55,.12); }
.sl-modal .sl-btn-cancel {
    background: #f1f5f9; color: #64748b; border: none; border-radius: 22px;
    padding: 8px 22px; font-size: .84rem; font-weight: 600;
}

/* ── Pagination ── */
.sl-card .pagination { margin: 0; }
.sl-card .pagination .page-link {
    border-radius: 10px; margin: 0 2px; font-size: .82rem;
    border: 1px solid rgba(0,0,0,.08); color: #475569;
}
.sl-card .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #d4af37, #b8962e);
    border-color: #d4af37; color: #fff;
}
.sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

/* ── Toast ── */
.sl-toast {
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    background: #1a1a2e; color: #fff; border-radius: 12px; padding: 14px 22px;
    font-size: .85rem; box-shadow: 0 8px 32px rgba(0,0,0,.2);
    display: flex; align-items: center; gap: 10px;
    animation: slToastIn .35s ease;
}
.sl-toast i { color: #d4af37; font-size: 1.1rem; }
@keyframes slToastIn { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: translateY(0); } }

/* ── Dark Mode ── */
[data-bs-theme=dark] .sl-stat, [data-theme="dark"] .sl-stat { background: #1e293b; border-color: rgba(255,255,255,.06); }
[data-bs-theme=dark] .sl-stat-value, [data-theme="dark"] .sl-stat-value { color: #f1f5f9; }
[data-bs-theme=dark] .sl-stat-label, [data-theme="dark"] .sl-stat-label { color: #94a3b8; }
[data-bs-theme=dark] .sl-stat-bar, [data-theme="dark"] .sl-stat-bar { background: #334155; }
[data-bs-theme=dark] .sl-card, [data-theme="dark"] .sl-card { background: #1e293b; border-color: rgba(255,255,255,.06); }
[data-bs-theme=dark] .sl-card-title, [data-theme="dark"] .sl-card-title { color: #f1f5f9; }
[data-bs-theme=dark] .sl-chart-card, [data-theme="dark"] .sl-chart-card { background: #1e293b; border-color: rgba(255,255,255,.06); }
[data-bs-theme=dark] .sl-chart-title, [data-theme="dark"] .sl-chart-title { color: #f1f5f9; }
[data-bs-theme=dark] .sl-tbl thead th, [data-theme="dark"] .sl-tbl thead th { background: #0f172a; color: #94a3b8; border-color: rgba(255,255,255,.06); }
[data-bs-theme=dark] .sl-tbl tbody td, [data-theme="dark"] .sl-tbl tbody td { color: #cbd5e1; border-color: rgba(255,255,255,.04); }
[data-bs-theme=dark] .sl-tbl tbody tr:hover, [data-theme="dark"] .sl-tbl tbody tr:hover { background: rgba(212,175,55,.06); }
[data-bs-theme=dark] .sl-filter-input, [data-theme="dark"] .sl-filter-input,
[data-bs-theme=dark] .sl-filter-select, [data-theme="dark"] .sl-filter-select { background: #0f172a; color: #cbd5e1; border-color: rgba(255,255,255,.1); }
[data-bs-theme=dark] .sl-tbl .form-select-sm, [data-theme="dark"] .sl-tbl .form-select-sm,
[data-bs-theme=dark] .sl-tbl .form-control-sm, [data-theme="dark"] .sl-tbl .form-control-sm { background: #0f172a; color: #cbd5e1; border-color: rgba(255,255,255,.1); }

@media(max-width:768px){
    .sl-topbar { flex-direction: column; align-items: flex-start; gap: 10px; padding: 16px 18px; }
    .sl-filter-row { flex-direction: column; }
    .sl-filter-input, .sl-filter-select { width: 100%; }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- ── Topbar ── -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <div>
                <h1 class="sl-topbar-title"><i class="bx bx-building-house"></i> Bank Verification</h1>
                <p class="sl-topbar-sub">Approved & Issued Sales &mdash; Verification Status Management</p>
            </div>
        </div>
        <div class="sl-topbar-right">
            <span style="color:rgba(255,255,255,.45); font-size:.8rem;">
                <i class="bx bx-data" style="color:#d4af37;"></i>
                <?php echo e($leads->total()); ?> total records
            </span>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px; border:none; background:rgba(34,197,94,.1); color:#16a34a;">
            <i class="bx bx-check-circle me-2"></i><strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- ── KPI Stat Cards ── -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="sl-stat">
                <div class="sl-stat-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                    <i class="bx bx-check-circle"></i>
                </div>
                <p class="sl-stat-label">Good</p>
                <h3 class="sl-stat-value" style="color:#16a34a;"><?php echo e($good_count); ?></h3>
                <?php $totalBv = $good_count + $average_count + $bad_count + $unverified_count; $goodPct = $totalBv > 0 ? round(($good_count / $totalBv) * 100, 1) : 0; ?>
                <div class="sl-stat-bar"><div class="sl-stat-bar-fill" style="width:<?php echo e($goodPct); ?>%; background:#22c55e;"></div></div>
                <small style="color:#64748b; font-size:.72rem; margin-top:4px; display:block;"><?php echo e($goodPct); ?>% of total</small>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="sl-stat">
                <div class="sl-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    <i class="bx bx-error-circle"></i>
                </div>
                <p class="sl-stat-label">Average</p>
                <h3 class="sl-stat-value" style="color:#d97706;"><?php echo e($average_count); ?></h3>
                <?php $avgPct = $totalBv > 0 ? round(($average_count / $totalBv) * 100, 1) : 0; ?>
                <div class="sl-stat-bar"><div class="sl-stat-bar-fill" style="width:<?php echo e($avgPct); ?>%; background:#f59e0b;"></div></div>
                <small style="color:#64748b; font-size:.72rem; margin-top:4px; display:block;"><?php echo e($avgPct); ?>% of total</small>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="sl-stat">
                <div class="sl-stat-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                    <i class="bx bx-x-circle"></i>
                </div>
                <p class="sl-stat-label">Bad</p>
                <h3 class="sl-stat-value" style="color:#dc2626;"><?php echo e($bad_count); ?></h3>
                <?php $badPct = $totalBv > 0 ? round(($bad_count / $totalBv) * 100, 1) : 0; ?>
                <div class="sl-stat-bar"><div class="sl-stat-bar-fill" style="width:<?php echo e($badPct); ?>%; background:#ef4444;"></div></div>
                <small style="color:#64748b; font-size:.72rem; margin-top:4px; display:block;"><?php echo e($badPct); ?>% of total</small>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="sl-stat">
                <div class="sl-stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                    <i class="bx bx-help-circle"></i>
                </div>
                <p class="sl-stat-label">Unverified</p>
                <h3 class="sl-stat-value" style="color:#7c3aed;"><?php echo e($unverified_count); ?></h3>
                <?php $unvPct = $totalBv > 0 ? round(($unverified_count / $totalBv) * 100, 1) : 0; ?>
                <div class="sl-stat-bar"><div class="sl-stat-bar-fill" style="width:<?php echo e($unvPct); ?>%; background:#8b5cf6;"></div></div>
                <small style="color:#64748b; font-size:.72rem; margin-top:4px; display:block;"><?php echo e($unvPct); ?>% of total</small>
            </div>
        </div>
    </div>

    <!-- ── Donut Chart ── -->
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="sl-chart-card" style="min-height:320px;">
                <h6 class="sl-chart-title"><i class="bx bx-pie-chart-alt-2"></i>Verification Distribution</h6>
                <div id="bvDonutChart"></div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="sl-chart-card" style="min-height:320px;">
                <h6 class="sl-chart-title"><i class="bx bx-bar-chart-alt-2"></i>Status Breakdown</h6>
                <div id="bvBarChart"></div>
            </div>
        </div>
    </div>

    <!-- ── Leads Table ── -->
    <div class="sl-card">
        <div class="sl-card-header">
            <h5 class="sl-card-title"><i class="bx bx-list-ul"></i>Verification List</h5>
        </div>
        <div class="sl-card-body">
            <!-- Filters -->
            <form method="GET" action="<?php echo e(route('bank-verification.index')); ?>">
                <div class="sl-filter-row">
                    <input type="text" name="search" class="sl-filter-input" style="min-width:220px;" placeholder="Search name, phone, policy..." value="<?php echo e(request('search')); ?>">
                    <select name="verification_status" class="sl-filter-select">
                        <option value="">All Status</option>
                        <option value="Good" <?php echo e(request('verification_status') == 'Good' ? 'selected' : ''); ?>>Good</option>
                        <option value="Average" <?php echo e(request('verification_status') == 'Average' ? 'selected' : ''); ?>>Average</option>
                        <option value="Bad" <?php echo e(request('verification_status') == 'Bad' ? 'selected' : ''); ?>>Bad</option>
                    </select>
                    <select name="month" class="sl-filter-select">
                        <option value="">Month</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>><?php echo e(date('M', mktime(0, 0, 0, $m, 1))); ?></option>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <select name="year" class="sl-filter-select">
                        <option value="">Year</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e(request('year') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <button type="submit" class="sl-btn-filter"><i class="bx bx-search me-1"></i>Filter</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','verification_status','month','year'])): ?>
                        <a href="<?php echo e(route('bank-verification.index')); ?>" class="sl-btn-reset"><i class="bx bx-reset me-1"></i>Clear</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="sl-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Carrier</th>
                            <th>Policy #</th>
                            <th>Premium</th>
                            <th>Issued</th>
                            <th style="min-width:140px;">Assigned B.V</th>
                            <th style="min-width:180px;">Comment</th>
                            <th style="min-width:120px;">Bank Status</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td style="color:#94a3b8; font-size:.78rem;"><?php echo e($leads->firstItem() + $idx); ?></td>
                                <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                                <td><?php echo e($lead->phone_number); ?></td>
                                <td><?php echo e($lead->carrier_name ?? 'N/A'); ?></td>
                                <td><code style="font-size:.78rem;"><?php echo e($lead->issued_policy_number ?? 'N/A'); ?></code></td>
                                <td class="sl-premium">$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                                <td style="white-space:nowrap;"><?php echo e($lead->issuance_date ? \Carbon\Carbon::parse($lead->issuance_date)->format('M d, Y') : 'N/A'); ?></td>
                                <td>
                                    <select class="form-select form-select-sm assigned-bv-dropdown" data-lead-id="<?php echo e($lead->id); ?>">
                                        <option value="">Unassigned</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $bankVerifiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $verifier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($verifier->id); ?>" <?php echo e($lead->assigned_bank_verifier == $verifier->id ? 'selected' : ''); ?>><?php echo e($verifier->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea class="form-control form-control-sm bv-comment-field" data-lead-id="<?php echo e($lead->id); ?>" rows="1" placeholder="Add comment..."><?php echo e($lead->bank_verification_comment); ?></textarea>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm bv-status-select" data-lead-id="<?php echo e($lead->id); ?>">
                                        <option value="">Not Set</option>
                                        <option value="Good" <?php echo e($lead->bank_verification_status === 'Good' ? 'selected' : ''); ?>>Good</option>
                                        <option value="Average" <?php echo e($lead->bank_verification_status === 'Average' ? 'selected' : ''); ?>>Average</option>
                                        <option value="Bad" <?php echo e($lead->bank_verification_status === 'Bad' ? 'selected' : ''); ?>>Bad</option>
                                    </select>
                                </td>
                                <td><small class="text-muted"><?php echo e($lead->bank_verification_notes ?? '—'); ?></small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e(route('bank-verification.show', $lead->id)); ?>" class="sl-btn-view" title="View Details">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <button class="sl-btn-update" data-bs-toggle="modal" data-bs-target="#verificationModal-<?php echo e($lead->id); ?>" title="Update Status">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Verification Modal -->
                            <div class="modal fade sl-modal" id="verificationModal-<?php echo e($lead->id); ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="bx bx-building-house me-2"></i>Update &mdash; <?php echo e($lead->cn_name); ?>

                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?php echo e(route('bank-verification.update', $lead->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <div class="modal-body">
                                                <label class="form-label fw-semibold mb-3" style="font-size:.85rem;">Select Verification Status</label>
                                                <div class="d-grid gap-2 mb-4">
                                                    <button type="submit" name="bank_verification_status" value="Good" class="sl-status-btn sl-status-good">
                                                        <i class="bx bx-check-circle"></i> Good
                                                    </button>
                                                    <button type="submit" name="bank_verification_status" value="Average" class="sl-status-btn sl-status-avg">
                                                        <i class="bx bx-error-circle"></i> Average
                                                    </button>
                                                    <button type="submit" name="bank_verification_status" value="Bad" class="sl-status-btn sl-status-bad">
                                                        <i class="bx bx-x-circle"></i> Bad
                                                    </button>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="notes-<?php echo e($lead->id); ?>" class="form-label fw-semibold" style="font-size:.85rem;">Reason / Notes</label>
                                                    <textarea class="form-control" id="notes-<?php echo e($lead->id); ?>" name="bank_verification_notes" rows="3" placeholder="Add reason or notes..."><?php echo e($lead->bank_verification_notes); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="sl-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="12" class="text-center py-5">
                                    <i class="bx bx-inbox" style="font-size:2.5rem; color:#cbd5e1;"></i>
                                    <p class="text-muted mt-2 mb-0">No approved & issued sales found</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leads->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($leads->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<script>
$(document).ready(function() {
    // ── ApexCharts ──
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.getAttribute('data-theme') === 'dark';
    const txtColor = isDark ? '#94a3b8' : '#64748b';

    // Donut Chart
    const donutData = [<?php echo e($good_count); ?>, <?php echo e($average_count); ?>, <?php echo e($bad_count); ?>, <?php echo e($unverified_count); ?>];
    if (donutData.some(v => v > 0)) {
        new ApexCharts(document.querySelector('#bvDonutChart'), {
            series: donutData,
            chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
            labels: ['Good', 'Average', 'Bad', 'Unverified'],
            colors: ['#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'],
            stroke: { width: 2, colors: [isDark ? '#1e293b' : '#fff'] },
            legend: { position: 'bottom', fontSize: '12px', labels: { colors: txtColor } },
            dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 } },
            plotOptions: { pie: { donut: { size: '62%', labels: {
                show: true, total: { show: true, label: 'Total', fontSize: '13px', color: txtColor,
                    formatter: () => <?php echo e($good_count + $average_count + $bad_count + $unverified_count); ?>

                }
            } } } },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        }).render();
    }

    // Bar Chart
    new ApexCharts(document.querySelector('#bvBarChart'), {
        series: [{ name: 'Count', data: [<?php echo e($good_count); ?>, <?php echo e($average_count); ?>, <?php echo e($bad_count); ?>, <?php echo e($unverified_count); ?>] }],
        chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
        colors: ['#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'],
        plotOptions: { bar: { distributed: true, borderRadius: 8, columnWidth: '55%' } },
        xaxis: { categories: ['Good', 'Average', 'Bad', 'Unverified'], labels: { style: { colors: txtColor, fontSize: '12px' } } },
        yaxis: { labels: { style: { colors: txtColor, fontSize: '12px' } } },
        legend: { show: false },
        grid: { borderColor: isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)', strokeDashArray: 4 },
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700, colors: ['#fff'] } },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();

    // ── Handle assigned bank verifier dropdown change ──
    $('.assigned-bv-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const verifierId = $(this).val();
        const dropdown = $(this);

        if (confirm('Assign this bank verifier?')) {
            dropdown.prop('disabled', true);
            $.ajax({
                url: `/bank-verification/${leadId}/assign-verifier`,
                method: 'POST',
                data: { _token: '<?php echo e(csrf_token()); ?>', assigned_bank_verifier: verifierId || null },
                success: function(response) {
                    if (response.success) { slToast(response.message); }
                    dropdown.prop('disabled', false);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to assign bank verifier');
                    dropdown.prop('disabled', false);
                    location.reload();
                }
            });
        } else { location.reload(); }
    });

    // ── Auto-save comment and status ──
    $('.bv-comment-field, .bv-status-select').on('change blur', function() {
        const row = $(this).closest('tr');
        const leadId = $(this).data('lead-id');
        const comment = row.find('.bv-comment-field').val();
        const status = row.find('.bv-status-select').val();

        clearTimeout(window.bvUpdateTimeout);
        window.bvUpdateTimeout = setTimeout(function() {
            $.ajax({
                url: `/bank-verification/${leadId}/update-assignment`,
                method: 'POST',
                data: { _token: '<?php echo e(csrf_token()); ?>', bank_verification_comment: comment, bank_verification_status: status || null },
                success: function(response) { if (response.success) { slToast('Updated successfully'); } },
                error: function(xhr) { alert(xhr.responseJSON?.message || 'Failed to update details'); }
            });
        }, 1000);
    });

    // ── Toast notification ──
    function slToast(msg) {
        const t = $(`<div class="sl-toast"><i class="bx bx-check-circle"></i>${msg}</div>`);
        $('body').append(t);
        setTimeout(() => t.fadeOut(300, function(){ $(this).remove(); }), 3000);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/bank-verification/index.blade.php ENDPATH**/ ?>