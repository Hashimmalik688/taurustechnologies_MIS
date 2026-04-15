<?php $__env->startSection('title'); ?>
    <?php echo e($rate->carrier_label); ?> — Carrier Sheet
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ════════════════════════════════════════════════════════
   CARRIER SHEET — Single carrier view
   ════════════════════════════════════════════════════════ */
:root {
    --cs-title: <?php echo e($rate->title_color); ?>;
    --cs-surface: var(--bs-card-bg, #ffffff);
    --cs-border: rgba(0,0,0,.07);
    --cs-shadow: 0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --cs-text-1: var(--bs-body-color, #0f172a);
    --cs-text-3: var(--bs-surface-500, #64748b);
    --cs-row-alt: #F0F4FF;
}

.cs-page { width:100%; }

/* ── Header / Title bar ────────────────────────────── */
.cs-title-bar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.5rem; padding:.7rem 1rem; border-radius:.55rem .55rem 0 0;
    background: var(--cs-title); color:#fff; margin-bottom:0;
}
.cs-title-bar h5 { margin:0; font-size:1rem; font-weight:800; color:#fff; }
.cs-back-db {
    font-size:.68rem; font-weight:700; padding:.22rem .55rem; border-radius:20px;
    border:1.5px solid rgba(255,255,255,.4); background:transparent; color:#fff;
    text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s;
}
.cs-back-db:hover { background:rgba(255,255,255,.15); color:#fff; }

/* ── Summary badges ────────────────────────────────── */
.cs-badges {
    display:flex; flex-wrap:wrap; gap:.45rem; padding:.6rem .8rem;
    background:var(--cs-surface); border:1px solid var(--cs-border); border-top:none;
    border-radius:0 0 .55rem .55rem; box-shadow:var(--cs-shadow); margin-bottom:1rem;
}
.cs-badge {
    display:flex; flex-direction:column; align-items:center; padding:.35rem .6rem;
    border-radius:.4rem; min-width:70px;
}
.cs-badge-val { font-size:.82rem; font-weight:800; color:#fff; line-height:1; }
.cs-badge-lbl { font-size:.5rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:rgba(255,255,255,.85); margin-top:.15rem; }

/* Badge colors */
.cs-badge-commission { background:#283593; }
.cs-badge-paid       { background:#2E7D32; }
.cs-badge-balance    { background:#4527A0; }
.cs-badge-cb-total   { background:#C62828; }
.cs-badge-apps       { background:#1565C0; }
.cs-badge-paid-cnt   { background:#2E7D32; }
.cs-badge-approved   { background:#F57F17; }
.cs-badge-cb-cnt     { background:#C62828; }
.cs-badge-declined   { background:#E65100; }

/* ── Filter bar ────────────────────────────────────── */
.cs-filter {
    display:flex; flex-wrap:wrap; gap:.5rem; align-items:flex-end;
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; padding:.5rem .8rem; margin-bottom:.7rem; box-shadow:var(--cs-shadow);
}
.cs-filter label {
    font-size:.56rem; font-weight:800; text-transform:uppercase;
    letter-spacing:.6px; color:var(--cs-text-3); display:block; margin-bottom:.12rem;
}
.cs-filter select, .cs-filter input[type=month], .cs-filter input[type=search] {
    font-size:.72rem; padding:.26rem .4rem; border-radius:.38rem;
    border:1.5px solid var(--cs-border); background:var(--bs-input-bg, #f8fafc);
    color:var(--cs-text-1); outline:none;
}
.cs-btn {
    font-size:.68rem; font-weight:700; padding:.3rem .65rem; border-radius:20px;
    border:none; cursor:pointer; display:inline-flex; align-items:center;
    gap:.22rem; transition:all .15s; text-decoration:none;
}
.cs-btn-primary { background:linear-gradient(135deg, var(--cs-title), #111); color:#fff; }
.cs-btn-primary:hover { box-shadow:0 2px 8px rgba(0,0,0,.3); color:#fff; }
.cs-btn-success { background:linear-gradient(135deg, #2E7D32, #1B5E20); color:#fff; }
.cs-btn-success:hover { box-shadow:0 2px 10px rgba(46,125,50,.4); color:#fff; }
.cs-btn-danger { background:linear-gradient(135deg, #C62828, #8b1a1a); color:#fff; }
.cs-btn-danger:hover { box-shadow:0 2px 10px rgba(198,40,40,.4); color:#fff; }
.cs-btn-outline { background:transparent; border:1.5px solid var(--cs-border)!important; color:var(--cs-text-3); }
.cs-btn-outline:hover { border-color:var(--cs-title)!important; color:var(--cs-title); }

/* ── Pinned opening rows ───────────────────────────── */
.cs-pinned-row td {
    font-size:.66rem; font-weight:700; border-bottom:2px solid var(--cs-border);
    white-space:nowrap;
}
.cs-pinned-bal  { background:#E8EAF6 !important; }
.cs-pinned-cb   { background:#FFF3CD !important; }
.cs-pinned-label {
    text-align:left !important; font-size:.64rem; font-weight:800;
    letter-spacing:.2px;
}
.cs-pinned-val {
    font-variant-numeric:tabular-nums;
}
.cs-inline-edit-wrap { display:flex; align-items:center; justify-content:center; gap:.3rem; }
.cs-inline-input {
    width:100px; padding:.15rem .3rem; font-size:.68rem; font-weight:700;
    border:1.5px solid #9fa8da; border-radius:.3rem; text-align:right;
    display:none;
}
.cs-inline-input.active { display:inline-block; }
.cs-save-btn {
    display:none; background:#283593; color:#fff; border:none; border-radius:.3rem;
    padding:.15rem .35rem; font-size:.6rem; cursor:pointer;
}
.cs-save-btn.active { display:inline-flex; align-items:center; gap:.15rem; }
.cs-no-period-note { font-size:.6rem; color:#999; font-style:italic; }

/* ── Data table ────────────────────────────────────── */
.cs-card {
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; overflow-x:auto; box-shadow:var(--cs-shadow);
}
.cs-dtable { width:100%; font-size:.7rem; border-collapse:collapse; min-width:900px; }
.cs-dtable thead th {
    background:var(--cs-title); color:#fff; font-weight:700; font-size:.58rem;
    text-transform:uppercase; letter-spacing:.4px; padding:.4rem .35rem;
    text-align:center; white-space:nowrap; border:none; position:sticky; top:0; z-index:2;
}
.cs-dtable tbody td {
    padding:.3rem .35rem; border-bottom:1px solid var(--cs-border);
    text-align:center; vertical-align:middle; color:var(--cs-text-1); white-space:nowrap;
}
.cs-dtable tbody tr:nth-child(even) { background:var(--cs-row-alt); }
.cs-dtable tbody tr:hover { background:rgba(0,0,0,.04); }

/* Status row colors */
.cs-dtable tr.cs-row-approved             { background:#ffffff !important; }
.cs-dtable tr.cs-row-approved:hover       { background:#f0f0f0 !important; }
.cs-dtable tr.cs-row-paid                 { background:#c8e6c9 !important; }
.cs-dtable tr.cs-row-paid:hover           { background:#a5d6a7 !important; }
.cs-dtable tr.cs-row-chargeback           { background:#ffcdd2 !important; }
.cs-dtable tr.cs-row-chargeback:hover     { background:#ef9a9a !important; }
.cs-dtable tr.cs-row-declined             { background:#e0e0e0 !important; }
.cs-dtable tr.cs-row-declined:hover       { background:#bdbdbd !important; }
/* Policy type badges */
.cs-ptype { display:inline-block; padding:.15rem .45rem; border-radius:3px; font-size:.58rem; font-weight:700; text-transform:uppercase; letter-spacing:.03em; }
.cs-ptype-level    { background:#BBDEFB; color:#0D47A1; }
.cs-ptype-graded   { background:#E1BEE7; color:#6A1B9A; }
.cs-ptype-gi       { background:#B2EBF2; color:#006064; }
.cs-ptype-modified { background:#FFE0B2; color:#E65100; }
.cs-ptype-default  { background:#F5F5F5; color:#424242; }

/* Status cell badges */
.cs-status {
    display:inline-block; padding:.15rem .4rem; border-radius:.25rem;
    font-weight:700; font-size:.6rem; text-transform:uppercase;
}
.cs-status-approved   { background:#FFF8E1; color:#856404; border:1px solid #ffe69c; }
.cs-status-paid       { background:#E8F5E9; color:#2E7D32; border:1px solid #a5d6a7; }
.cs-status-chargeback { background:#FFEBEE; color:#C62828; border:1px solid #ef9a9a; }
.cs-status-declined   { background:#FFE0B2; color:#E65100; border:1px solid #ffcc80; }

/* Override column */
.cs-override-indicator {
    font-size:.55rem; color:#F57F17; font-weight:700;
    cursor:help; margin-left:2px;
}

/* Align left for name/policy */
.cs-dtable td.cs-left, .cs-dtable th.cs-left { text-align:left; }

/* ── Action buttons in table ───────────────────────── */
.cs-row-actions {
    display:flex; gap:.2rem; justify-content:center;
}
.cs-row-btn {
    width:22px; height:22px; border-radius:.25rem; border:none; cursor:pointer;
    display:flex; align-items:center; justify-content:center; font-size:.65rem;
    transition:all .1s;
}
.cs-row-btn-edit { background:rgba(40,53,147,.1); color:#283593; }
.cs-row-btn-edit:hover { background:#283593; color:#fff; }
.cs-row-btn-del { background:rgba(198,40,40,.1); color:#C62828; }
.cs-row-btn-del:hover { background:#C62828; color:#fff; }

/* Money styling */
.cs-money { font-weight:700; font-variant-numeric:tabular-nums; }
.cs-money-pos { color:#2E7D32; }
.cs-money-neg { color:#C62828; }

/* ── Copy button ───────────────────────────────────── */
.cs-row-btn-copy { background:rgba(25,118,210,.08); color:#1976D2; }
.cs-row-btn-copy:hover { background:#1976D2; color:#fff; }
.cs-row-btn-copy.copied { background:#2E7D32 !important; color:#fff !important; }

/* ── Daily summary ─────────────────────────────────── */
.cs-daily { margin-top:1.2rem; }
.cs-daily h6 { font-size:.72rem; font-weight:800; color:var(--cs-text-1); margin-bottom:.5rem; }
.cs-daily-table { width:100%; max-width:400px; font-size:.68rem; border-collapse:collapse; }
.cs-daily-table th { background:var(--cs-title); color:#fff; padding:.3rem .5rem; font-size:.58rem; text-transform:uppercase; }
.cs-daily-table td { padding:.25rem .5rem; border-bottom:1px solid var(--cs-border); }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cs-page">
    
    <div class="cs-title-bar">
        <h5><i class="bx bx-spreadsheet me-2"></i> <?php echo e($rate->carrier_label); ?></h5>
        <a href="<?php echo e(route('settings.reports.carrier-sheet.dashboard', ['month' => $periodMonth])); ?>" class="cs-back-db">
            <i class="bx bx-arrow-back"></i> Dashboard
        </a>
    </div>

    
    <div class="cs-badges" id="summaryBadges">
        <div class="cs-badge cs-badge-commission">
            <span class="cs-badge-val" id="badge-commission"><?php echo e(number_format($summary['commission'], 2)); ?></span>
            <span class="cs-badge-lbl">Commission</span>
        </div>
        <div class="cs-badge cs-badge-paid">
            <span class="cs-badge-val" id="badge-paid"><?php echo e(number_format($summary['paid'], 2)); ?></span>
            <span class="cs-badge-lbl">Paid</span>
        </div>
        <div class="cs-badge cs-badge-balance">
            <span class="cs-badge-val" id="badge-balance"><?php echo e(number_format($summary['balance'], 2)); ?></span>
            <span class="cs-badge-lbl">Balance</span>
        </div>
        <div class="cs-badge cs-badge-cb-total">
            <span class="cs-badge-val" id="badge-cb-total"><?php echo e(number_format($summary['chargeback_total'], 2)); ?></span>
            <span class="cs-badge-lbl">Chargeback $</span>
        </div>
        <div style="width:1px; background:var(--cs-border); margin:0 .2rem;"></div>
        <div class="cs-badge cs-badge-apps">
            <span class="cs-badge-val" id="badge-total-apps"><?php echo e($summary['total_apps']); ?></span>
            <span class="cs-badge-lbl">Apps</span>
        </div>
        <div class="cs-badge cs-badge-paid-cnt">
            <span class="cs-badge-val" id="badge-paid-cnt"><?php echo e($summary['paid_count']); ?></span>
            <span class="cs-badge-lbl">Paid</span>
        </div>
        <div class="cs-badge cs-badge-approved">
            <span class="cs-badge-val" id="badge-approved"><?php echo e($summary['approved_count']); ?></span>
            <span class="cs-badge-lbl">Approved</span>
        </div>
        <div class="cs-badge cs-badge-cb-cnt">
            <span class="cs-badge-val" id="badge-cb-cnt"><?php echo e($summary['chargeback_count']); ?></span>
            <span class="cs-badge-lbl">CB</span>
        </div>
        <div class="cs-badge cs-badge-declined">
            <span class="cs-badge-val" id="badge-declined"><?php echo e($summary['declined_count']); ?></span>
            <span class="cs-badge-lbl">Declined</span>
        </div>
    </div>

    
    <form class="cs-filter" method="GET" action="<?php echo e(route('settings.reports.carrier-sheet.show', $rate)); ?>">
        <div>
            <label>Period</label>
            <select name="month" onchange="this.form.submit()">
                <option value="">All Time</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php echo e($periodMonth === $m ? 'selected' : ''); ?>>
                        <?php echo e(\Carbon\Carbon::parse($m)->format('F Y')); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <label>Search</label>
            <input type="search" id="tableSearch" placeholder="Name, Policy #, Type..." autocomplete="off" style="min-width:180px;">
        </div>
        <div>
            <label>Type</label>
            <select id="filterType">
                <option value="">All Types</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rate->getPolicyTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e(strtolower($pt)); ?>"><?php echo e(strtoupper(str_replace('_',' ',$pt))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <label>Status</label>
            <select id="filterStatus">
                <option value="">All Statuses</option>
                <option value="approved">Approved</option>
                <option value="paid">Paid</option>
                <option value="chargeback">Chargeback</option>
                <option value="declined">Declined</option>
            </select>
        </div>
        <div style="margin-left:auto; display:flex; gap:.4rem; align-items:flex-end;">
            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
            <button type="button" class="cs-btn cs-btn-success" data-bs-toggle="modal" data-bs-target="#addEntryModal">
                <i class="bx bx-plus"></i> Add Entry
            </button>
            <button type="button" class="cs-btn cs-btn-danger" data-bs-toggle="modal" data-bs-target="#manualCbModal">
                <i class="bx bx-minus-circle"></i> Add Manual CB
            </button>
            <?php endif; ?>
            <a href="<?php echo e(route('settings.reports.carrier-sheet.export', ['rate' => $rate, 'month' => $periodMonth])); ?>" class="cs-btn cs-btn-outline">
                <i class="bx bx-download"></i> Export CSV
            </a>
        </div>
    </form>

    
    <div class="cs-card">
        <table class="cs-dtable" id="carrierTable">
            <thead>
                <tr>
                    <th style="width:35px;">SR#</th>
                    <th>Date</th>
                    <th class="cs-left">Policy #</th>
                    <th class="cs-left">Name</th>
                    <th>FV</th>
                    <th>PRM</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Draft</th>
                    <th>Payment</th>
                    <th>Commission</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>CB</th>
                    <th style="width:28px;" title="Copy row"><i class="bx bx-copy" style="font-size:.7rem;"></i></th>
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <th style="width:50px;"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(true): ?>
                <tr class="cs-pinned-row cs-pinned-bal">
                    <td style="color:#9fa8da;">&#x2605;</td>
                    <td></td>
                    <td class="cs-pinned-label" colspan="10" style="color:#283593;">Opening Balance <span style="font-weight:400; color:#888;">(carried forward)</span></td>
                    <td class="cs-money cs-pinned-val <?php echo e($openingCb->opening_balance >= 0 ? 'cs-money-pos' : 'cs-money-neg'); ?>" id="pinned-bal-display">
                        <?php echo e(number_format($openingCb->opening_balance, 2)); ?>

                    </td>
                    <td></td>
                    <td></td>
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodMonth): ?>
                        <div class="cs-row-actions">
                            <button class="cs-row-btn cs-row-btn-edit" onclick="editPinnedBal()" id="pinnedBalEditBtn" title="Edit Opening Balance">
                                <i class="bx bx-pencil"></i>
                            </button>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodMonth): ?>
                <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                <tr id="pinnedBalEditRow" style="display:none; background:#E8EAF6;">
                    <td></td>
                    <td></td>
                    <td colspan="10" style="color:#555; font-size:.65rem; font-style:italic; text-align:left;">Enter the starting balance to carry forward (+ adds to balance, − subtracts)</td>
                    <td>
                        <input type="number" step="0.01" id="pinnedBalInput" value="<?php echo e($openingCb->opening_balance); ?>" class="form-control form-control-sm" style="width:110px; font-size:.72rem; text-align:right;">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <div style="display:flex; gap:.2rem;">
                            <button class="cs-row-btn cs-row-btn-edit" onclick="savePinnedBal()" title="Save"><i class="bx bx-check"></i></button>
                            <button class="cs-row-btn cs-row-btn-del" onclick="cancelPinnedBal()" title="Cancel"><i class="bx bx-x"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(true): ?>
                <tr class="cs-pinned-row cs-pinned-cb">
                    <td style="color:#ffe69c;">&#x2605;</td>
                    <td></td>
                    <td class="cs-pinned-label" colspan="10" style="color:#856404;">Opening Chargeback <span style="font-weight:400; color:#888;">(previous balance carried forward)</span></td>
                    <td></td>
                    <td class="cs-money cs-pinned-val cs-money-neg" id="pinned-cb-display">
                        <?php echo e($openingCb->amount > 0 ? number_format($openingCb->amount, 2) : ''); ?>

                    </td>
                    <td></td>
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodMonth): ?>
                        <div class="cs-row-actions">
                            <button class="cs-row-btn cs-row-btn-edit" onclick="editPinnedCb()" id="pinnedCbEditBtn" title="Edit Opening Chargeback">
                                <i class="bx bx-pencil"></i>
                            </button>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodMonth): ?>
                <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                <tr id="pinnedCbEditRow" style="display:none; background:#FFF3CD;">
                    <td></td>
                    <td></td>
                    <td colspan="10" style="color:#555; font-size:.65rem; font-style:italic; text-align:left;">Enter the previous chargeback balance to carry forward (subtracts from balance)</td>
                    <td></td>
                    <td>
                        <input type="number" step="0.01" min="0" id="pinnedCbInput" value="<?php echo e($openingCb->amount); ?>" class="form-control form-control-sm" style="width:110px; font-size:.72rem; text-align:right;">
                    </td>
                    <td></td>
                    <td>
                        <div style="display:flex; gap:.2rem;">
                            <button class="cs-row-btn cs-row-btn-edit" onclick="savePinnedCb()" title="Save"><i class="bx bx-check"></i></button>
                            <button class="cs-row-btn cs-row-btn-del" onclick="cancelPinnedCb()" title="Cancel"><i class="bx bx-x"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="cs-row-<?php echo e(strtolower($entry->status)); ?>" data-entry-id="<?php echo e($entry->id); ?>">
                    <td><?php echo e($entry->sr_number); ?></td>
                    <td><?php echo e($entry->entry_date?->format('d-M-y')); ?></td>
                    <td class="cs-left"><?php echo e($entry->policy_number); ?></td>
                    <td class="cs-left"><?php echo e($entry->name); ?></td>
                    <td><?php echo e($entry->face_value); ?></td>
                    <td class="cs-money"><?php echo e(number_format($entry->premium, 2)); ?></td>
                    <td>
                        <?php
                            $ptSlug = strtolower(trim($entry->policy_type ?? ''));
                            $ptClass = match(true) {
                                str_contains($ptSlug, 'level')    => 'cs-ptype-level',
                                str_contains($ptSlug, 'graded')   => 'cs-ptype-graded',
                                str_contains($ptSlug, 'gi') || str_contains($ptSlug, 'guaranteed') => 'cs-ptype-gi',
                                str_contains($ptSlug, 'modified') => 'cs-ptype-modified',
                                default => 'cs-ptype-default',
                            };
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->policy_type): ?>
                        <span class="cs-ptype <?php echo e($ptClass); ?>"><?php echo e($entry->policy_type); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <span class="cs-status cs-status-<?php echo e(strtolower($entry->status)); ?>"><?php echo e(ucfirst($entry->status)); ?></span>
                    </td>
                    <td><?php echo e($entry->draft_date?->format('d M')); ?></td>
                    <td><?php echo e($entry->payment_date?->format('d M')); ?></td>
                    <td class="cs-money <?php echo e($entry->commission ? 'cs-money-pos' : ''); ?>">
                        <?php echo e($entry->commission !== null ? number_format($entry->commission, 2) : ''); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->rate_override): ?>
                        <span class="cs-override-indicator" title="Rate override: <?php echo e($entry->rate_override); ?>">★</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="cs-money"><?php echo e($entry->paid_amount > 0 ? number_format($entry->paid_amount, 2) : ''); ?></td>
                    <td class="cs-money <?php echo e($entry->balance >= 0 ? 'cs-money-pos' : 'cs-money-neg'); ?>">
                        <?php echo e(number_format($entry->balance, 2)); ?>

                    </td>
                    <td class="cs-money <?php echo e($entry->chargeback_amount > 0 ? 'cs-money-neg' : ''); ?>">
                        <?php echo e($entry->chargeback_amount > 0 ? number_format($entry->chargeback_amount, 2) : ''); ?>

                    </td>
                    <td>
                        <button class="cs-row-btn cs-row-btn-copy"
                            title="Copy row to clipboard"
                            onclick="copyRow(this,
                                '<?php echo e($entry->sr_number); ?>',
                                '<?php echo e($entry->entry_date?->format('Y-m-d')); ?>',
                                '<?php echo e(addslashes($entry->policy_number)); ?>',
                                '<?php echo e(addslashes($entry->name)); ?>',
                                '<?php echo e(addslashes($entry->face_value)); ?>',
                                '<?php echo e(number_format($entry->premium, 2)); ?>',
                                '<?php echo e(addslashes($entry->policy_type)); ?>',
                                '<?php echo e(ucfirst($entry->status)); ?>',
                                '<?php echo e($entry->draft_date?->format('Y-m-d')); ?>',
                                '<?php echo e($entry->payment_date?->format('Y-m-d')); ?>',
                                '<?php echo e($entry->commission !== null ? number_format($entry->commission, 2) : ''); ?>',
                                '<?php echo e($entry->paid_amount > 0 ? number_format($entry->paid_amount, 2) : ''); ?>',
                                '<?php echo e(number_format($entry->balance, 2)); ?>',
                                '<?php echo e($entry->chargeback_amount > 0 ? number_format($entry->chargeback_amount, 2) : ''); ?>'
                            )">
                            <i class="bx bx-copy"></i>
                        </button>
                    </td>
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <td>
                        <div class="cs-row-actions">
                            <button class="cs-row-btn cs-row-btn-edit" onclick="editEntry(<?php echo e($entry->id); ?>)" title="Edit">
                                <i class="bx bx-pencil"></i>
                            </button>
                            <button class="cs-row-btn cs-row-btn-del" onclick="deleteEntry(<?php echo e($entry->id); ?>)" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="16" style="text-align:center; padding:2rem; color:var(--cs-text-3);">No entries yet. Click "Add Entry" or import an Excel file.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dailySummary->isNotEmpty()): ?>
    <div class="cs-daily">
        <h6><i class="bx bx-calendar me-1"></i> Daily Summary</h6>
        <div class="cs-card" style="max-width:420px;">
            <table class="cs-daily-table">
                <thead>
                    <tr><th>Date</th><th>Apps</th><th>Commission</th></tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dailySummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($day['date'] ? \Carbon\Carbon::parse($day['date'])->format('d M Y') : '—'); ?></td>
                        <td style="text-align:center;"><?php echo e($day['apps']); ?></td>
                        <td style="text-align:right;" class="cs-money cs-money-pos"><?php echo e(number_format($day['commission'], 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
<div class="modal fade" id="addEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--cs-title); color:#fff;">
                <h6 class="modal-title fw-bold"><i class="bx bx-plus me-1"></i> Add Entry — <?php echo e($rate->carrier_label); ?></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addEntryForm">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Date</label>
                            <input type="date" name="entry_date" class="form-control form-control-sm" value="<?php echo e(now()->format('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy #</label>
                            <input type="text" name="policy_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4" style="position:relative;">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Name</label>
                            <input type="text" name="name" id="add_name" class="form-control form-control-sm" autocomplete="off" placeholder="Type to search leads...">
                            <div id="leadSuggestions" style="display:none;position:absolute;z-index:9999;left:0;right:0;background:#fff;border:1px solid #dee2e6;border-radius:4px;box-shadow:0 4px 12px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Face Value</label>
                            <input type="text" name="face_value" class="form-control form-control-sm" placeholder="e.g. 5K">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Premium *</label>
                            <input type="number" step="0.01" name="premium" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy Type</label>
                            <select name="policy_type" class="form-select form-select-sm">
                                <option value="">—</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rate->getPolicyTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($pt); ?>"><?php echo e(strtoupper(str_replace('_', ' ', $pt))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Status *</label>
                            <select name="status" class="form-select form-select-sm" required>
                                <option value="approved">APPROVED</option>
                                <option value="paid">PAID</option>
                                <option value="chargeback">CHARGEBACK</option>
                                <option value="declined">DECLINED</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Draft Date</label>
                            <input type="date" name="draft_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Paid Amt</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control form-control-sm" value="0">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Chargeback $</label>
                            <input type="number" step="0.01" name="chargeback_amount" class="form-control form-control-sm" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Rate Override</label>
                            <input type="number" step="0.0001" name="rate_override" class="form-control form-control-sm" placeholder="optional">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm">
                        </div>
                    </div>
                    <input type="hidden" name="period_month" value="<?php echo e($periodMonth); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-success" onclick="submitAddEntry()">
                    <i class="bx bx-check me-1"></i> Add Entry
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--cs-title); color:#fff;">
                <h6 class="modal-title fw-bold"><i class="bx bx-pencil me-1"></i> Edit Entry</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEntryForm">
                    <input type="hidden" name="entry_id" id="edit_entry_id">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Date</label>
                            <input type="date" name="entry_date" id="edit_entry_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy #</label>
                            <input type="text" name="policy_number" id="edit_policy_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Face Value</label>
                            <input type="text" name="face_value" id="edit_face_value" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Premium</label>
                            <input type="number" step="0.01" name="premium" id="edit_premium" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy Type</label>
                            <select name="policy_type" id="edit_policy_type" class="form-select form-select-sm">
                                <option value="">—</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rate->getPolicyTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($pt); ?>"><?php echo e(strtoupper(str_replace('_', ' ', $pt))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Status</label>
                            <select name="status" id="edit_status" class="form-select form-select-sm">
                                <option value="approved">APPROVED</option>
                                <option value="paid">PAID</option>
                                <option value="chargeback">CHARGEBACK</option>
                                <option value="declined">DECLINED</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Draft Date</label>
                            <input type="date" name="draft_date" id="edit_draft_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Payment Date</label>
                            <input type="date" name="payment_date" id="edit_payment_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Paid Amt</label>
                            <input type="number" step="0.01" name="paid_amount" id="edit_paid_amount" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Chargeback $</label>
                            <input type="number" step="0.01" name="chargeback_amount" id="edit_chargeback_amount" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Rate Override</label>
                            <input type="number" step="0.0001" name="rate_override" id="edit_rate_override" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Commission <span style="font-size:.6rem;color:#e53935;font-weight:400;">override</span></label>
                            <input type="number" step="0.01" name="commission" id="edit_commission" class="form-control form-control-sm" placeholder="auto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Notes</label>
                            <input type="text" name="notes" id="edit_notes" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="submitEditEntry()">
                    <i class="bx bx-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
<div class="modal fade" id="manualCbModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#C62828; color:#fff;">
                <h6 class="modal-title fw-bold"><i class="bx bx-minus-circle me-1"></i> Add Manual Chargeback</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.68rem; color:#666; margin-bottom:.8rem;">For old chargebacks not in the sheet — just the key details. Commission will be $0.</p>
                <form id="manualCbForm">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Client Name *</label>
                            <input type="text" name="name" class="form-control form-control-sm" required placeholder="Full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Date *</label>
                            <input type="date" name="entry_date" class="form-control form-control-sm" required value="<?php echo e(now()->format('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Chargeback Amount *</label>
                            <input type="number" step="0.01" min="0.01" name="chargeback_amount" class="form-control form-control-sm" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy # <span style="color:#999;">(optional)</span></label>
                            <input type="text" name="policy_number" class="form-control form-control-sm" placeholder="If known">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Face Value <span style="color:#999;">(optional)</span></label>
                            <input type="text" name="face_value" class="form-control form-control-sm" placeholder="e.g. 10K">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Notes <span style="color:#999;">(optional)</span></label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Reason or reference">
                        </div>
                    </div>
                    
                    <input type="hidden" name="status" value="chargeback">
                    <input type="hidden" name="premium" value="0">
                    <input type="hidden" name="period_month" value="<?php echo e($periodMonth); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="submitManualCb()">
                    <i class="bx bx-check me-1"></i> Save Chargeback
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
// ── Copy row to clipboard ──────────────────────────────────────────────────
window.copyRow = function(btn, sr, date, policy, name, fv, prm, type, status, draft, payment, commission, paid, balance, cb) {
    const headers = ['SR#','Date','Policy #','Name','FV','Premium','Type','Status','Draft','Payment','Commission','Paid','Balance','CB'];
    const values  = [sr, date, policy, name, fv, prm, type, status, draft, payment, commission, paid, balance, cb];
    const tsv = headers.join('\t') + '\n' + values.join('\t');
    navigator.clipboard.writeText(tsv).then(function() {
        btn.classList.add('copied');
        const icon = btn.querySelector('i');
        icon.className = 'bx bx-check';
        setTimeout(function() {
            btn.classList.remove('copied');
            icon.className = 'bx bx-copy';
        }, 1500);
    }).catch(function() {
        // Fallback for older browsers
        const ta = document.createElement('textarea');
        ta.value = tsv;
        ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.classList.add('copied');
        const icon = btn.querySelector('i');
        icon.className = 'bx bx-check';
        setTimeout(function() {
            btn.classList.remove('copied');
            icon.className = 'bx bx-copy';
        }, 1500);
    });
};

(function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const RATE_ID = <?php echo e($rate->id); ?>;
    const BASE = "<?php echo e(url('settings/reports/carrier-sheet')); ?>";
    const PERIOD = "<?php echo e($periodMonth ?? ''); ?>";

    // ── Entries data store (for edit modal population) ──
    const entries = <?php echo json_encode($entries->keyBy('id'), 15, 512) ?>;

    // ── Helper: AJAX request ────────────────────────────
    async function ajax(url, method, data = null) {
        const opts = {
            method,
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        };
        if (data) opts.body = JSON.stringify(data);
        const res = await fetch(url, opts);
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Request failed');
        }
        return res.json();
    }

    // ── Update badges from summary object ───────────────
    function updateBadges(s) {
        if (!s) return;
        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };
        set('badge-commission', fmtMoney(s.commission));
        set('badge-paid', fmtMoney(s.paid));
        set('badge-balance', fmtMoney(s.balance));
        set('badge-cb-total', fmtMoney(s.chargeback_total));
        set('badge-total-apps', s.total_apps);
        set('badge-paid-cnt', s.paid_count);
        set('badge-approved', s.approved_count);
        set('badge-cb-cnt', s.chargeback_count);
        set('badge-declined', s.declined_count);
    }

    function fmtMoney(v) {
        return Number(v).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    // ── Add Entry ───────────────────────────────────────
    window.submitAddEntry = async function() {
        const form = document.getElementById('addEntryForm');
        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());
        // Convert empty strings to null for optional fields
        for (const k of ['rate_override']) {
            if (data[k] === '') data[k] = null;
        }

        try {
            const res = await ajax(`${BASE}/${RATE_ID}/entries`, 'POST', data);
            if (res.success) {
                updateBadges(res.summary);
                location.reload(); // simple refresh to rebuild table
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    };

    // ── Policy # search (client-side) ────────────────
    document.addEventListener('DOMContentLoaded', function() {
        // ── Lead name autocomplete ───────────────────
        const nameInput    = document.getElementById('add_name');
        const suggestions  = document.getElementById('leadSuggestions');
        let acTimer = null;

        if (nameInput) {
            nameInput.addEventListener('input', function() {
                clearTimeout(acTimer);
                const q = this.value.trim();
                if (q.length < 2) { suggestions.style.display = 'none'; return; }
                acTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`<?php echo e(route('settings.reports.carrier-sheet.lead-lookup')); ?>?q=` + encodeURIComponent(q), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        const leads = await res.json();
                        if (!leads.length) { suggestions.style.display = 'none'; return; }
                        suggestions.innerHTML = leads.map(l => `
                            <div class="lead-ac-item" data-lead='${JSON.stringify(l).replace(/'/g,"&#39;")}' style="padding:6px 10px;cursor:pointer;font-size:.8rem;border-bottom:1px solid #f0f0f0;">
                                <span style="font-weight:600;">${l.name}</span>
                                ${l.policy_number ? `<span style="color:#888;margin-left:6px;">${l.policy_number}</span>` : ''}
                                ${l.premium ? `<span style="color:#2E7D32;margin-left:6px;">$${l.premium}</span>` : ''}
                            </div>`).join('');
                        suggestions.style.display = 'block';
                        suggestions.querySelectorAll('.lead-ac-item').forEach(item => {
                            item.addEventListener('mouseenter', () => item.style.background = '#f0f7ff');
                            item.addEventListener('mouseleave', () => item.style.background = '');
                            item.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                const lead = JSON.parse(this.dataset.lead);
                                const form = document.getElementById('addEntryForm');
                                nameInput.value = lead.name || '';
                                if (lead.policy_number) form.querySelector('[name=policy_number]').value = lead.policy_number;
                                if (lead.face_value)    form.querySelector('[name=face_value]').value    = lead.face_value;
                                if (lead.premium)       form.querySelector('[name=premium]').value       = lead.premium;
                                if (lead.draft_date)    form.querySelector('[name=draft_date]').value    = lead.draft_date;
                                if (lead.payment_date)  form.querySelector('[name=payment_date]').value  = lead.payment_date;
                                if (lead.policy_type) {
                                    const sel = form.querySelector('[name=policy_type]');
                                    const opt = [...sel.options].find(o => o.value.toLowerCase() === lead.policy_type.toLowerCase());
                                    if (opt) sel.value = opt.value;
                                }
                                suggestions.style.display = 'none';
                            });
                        });
                    } catch(e) { suggestions.style.display = 'none'; }
                }, 280);
            });
            nameInput.addEventListener('blur', () => setTimeout(() => suggestions.style.display = 'none', 150));
            document.getElementById('addEntryModal').addEventListener('hidden.bs.modal', () => suggestions.style.display = 'none');
        }

        const searchInput = document.getElementById('tableSearch');
        const filterType   = document.getElementById('filterType');
        const filterStatus = document.getElementById('filterStatus');

        function applyFilters() {
            const needle = (searchInput ? searchInput.value.trim().toLowerCase() : '');
            const typeVal   = filterType   ? filterType.value.toLowerCase()   : '';
            const statusVal = filterStatus ? filterStatus.value.toLowerCase() : '';
            const rows = document.querySelectorAll('#carrierTable tbody tr[data-entry-id]');
            rows.forEach(function(row) {
                const rowText   = row.textContent.trim().toLowerCase();
                const typeCell  = row.querySelector('td:nth-child(7)');
                const statusCell= row.querySelector('td:nth-child(8)');
                const typeText  = typeCell   ? typeCell.textContent.trim().toLowerCase()   : '';
                const statusText= statusCell ? statusCell.textContent.trim().toLowerCase() : '';
                const matchSearch = !needle   || rowText.includes(needle);
                const matchType   = !typeVal  || typeText.includes(typeVal);
                const matchStatus = !statusVal|| statusText.includes(statusVal);
                row.style.display = (matchSearch && matchType && matchStatus) ? '' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (filterType)   filterType.addEventListener('change', applyFilters);
        if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    });

    // ── Edit Entry ──────────────────────────────────────
    // Status change helpers: auto-fill/clear chargeback amount
    document.addEventListener('DOMContentLoaded', function() {
        const statusSel = document.getElementById('edit_status');
        if (statusSel) {
            statusSel.addEventListener('change', function() {
                const cbField   = document.getElementById('edit_chargeback_amount');
                const paidField = document.getElementById('edit_paid_amount');
                if (this.value === 'chargeback') {
                    // Pre-fill CB with paid amount if CB is currently 0
                    if (parseFloat(cbField.value) === 0 && parseFloat(paidField.value) > 0) {
                        cbField.value = paidField.value;
                        cbField.focus();
                        cbField.select();
                    }
                } else if (this.value === 'paid' || this.value === 'approved') {
                    // Clear chargeback when reverting to paid/approved
                    // (paid_amount is preserved — it's just not counted for approved)
                    cbField.value = 0;
                }
            });
        }
    });

    window.editEntry = function(id) {
        const e = entries[id];
        if (!e) return alert('Entry not found');
        document.getElementById('edit_entry_id').value = id;
        document.getElementById('edit_entry_date').value = e.entry_date ? e.entry_date.substring(0, 10) : '';
        document.getElementById('edit_policy_number').value = e.policy_number || '';
        document.getElementById('edit_name').value = e.name || '';
        document.getElementById('edit_face_value').value = e.face_value || '';
        document.getElementById('edit_premium').value = e.premium || 0;
        document.getElementById('edit_policy_type').value = e.policy_type || '';
        document.getElementById('edit_status').value = e.status || 'approved';
        document.getElementById('edit_draft_date').value = e.draft_date ? e.draft_date.substring(0, 10) : '';
        document.getElementById('edit_payment_date').value = e.payment_date ? e.payment_date.substring(0, 10) : '';
        document.getElementById('edit_paid_amount').value = e.paid_amount || 0;
        document.getElementById('edit_chargeback_amount').value = e.chargeback_amount || 0;
        document.getElementById('edit_rate_override').value = e.rate_override || '';
        document.getElementById('edit_commission').value = e.commission !== null ? e.commission : '';
        document.getElementById('edit_notes').value = e.notes || '';
        new bootstrap.Modal(document.getElementById('editEntryModal')).show();
    };

    window.submitEditEntry = async function() {
        const form = document.getElementById('editEntryForm');
        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());
        const id = data.entry_id;
        delete data.entry_id;
        for (const k of ['rate_override', 'commission']) {
            if (data[k] === '') data[k] = null;
        }

        try {
            const res = await ajax(`${BASE}/entries/${id}`, 'PUT', data);
            if (res.success) {
                updateBadges(res.summary);
                location.reload();
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    };

    // ── Delete Entry ────────────────────────────────────
    window.deleteEntry = async function(id) {
        if (!confirm('Delete this entry?')) return;
        try {
            const res = await ajax(`${BASE}/entries/${id}`, 'DELETE');
            if (res.success) {
                updateBadges(res.summary);
                const row = document.querySelector(`tr[data-entry-id="${id}"]`);
                if (row) row.remove();
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    };

    // ── Opening Chargeback (pinned row) ───────────────────
    window.editPinnedCb = function() {
        document.getElementById('pinnedCbEditRow').style.display = '';
        document.getElementById('pinnedCbInput').focus();
    };
    window.cancelPinnedCb = function() {
        document.getElementById('pinnedCbEditRow').style.display = 'none';
    };
    window.savePinnedCb = async function() {
        const val = parseFloat(document.getElementById('pinnedCbInput').value) || 0;
        try {
            const res = await ajax(`${BASE}/${RATE_ID}/opening-chargeback`, 'PUT', {
                amount: val, period_month: PERIOD
            });
            if (res.success) {
                const disp = document.getElementById('pinned-cb-display');
                if (disp) disp.textContent = val > 0 ? val.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
                updateBadges(res.summary);
                cancelPinnedCb();
            }
        } catch (e) { alert('Error: ' + e.message); }
    };

    // ── Opening Balance (pinned row) ──────────────────────
    window.editPinnedBal = function() {
        document.getElementById('pinnedBalEditRow').style.display = '';
        document.getElementById('pinnedBalInput').focus();
    };
    window.cancelPinnedBal = function() {
        document.getElementById('pinnedBalEditRow').style.display = 'none';
    };
    window.savePinnedBal = async function() {
        const val = parseFloat(document.getElementById('pinnedBalInput').value) || 0;
        try {
            const res = await ajax(`${BASE}/${RATE_ID}/opening-balance`, 'PUT', {
                opening_balance: val, period_month: PERIOD
            });
            if (res.success) {
                const disp = document.getElementById('pinned-bal-display');
                if (disp) {
                    disp.textContent = val.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
                    disp.className = 'cs-money cs-pinned-val ' + (val >= 0 ? 'cs-money-pos' : 'cs-money-neg');
                }
                updateBadges(res.summary);
                cancelPinnedBal();
            }
        } catch (e) { alert('Error: ' + e.message); }
    };

    // ── Manual Chargeback ───────────────────────────────
    window.submitManualCb = async function() {
        const form = document.getElementById('manualCbForm');
        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());

        if (!data.name || !data.entry_date || !data.chargeback_amount) {
            return alert('Name, date, and chargeback amount are required.');
        }

        try {
            const res = await ajax(`${BASE}/${RATE_ID}/entries`, 'POST', data);
            if (res.success) {
                updateBadges(res.summary);
                bootstrap.Modal.getInstance(document.getElementById('manualCbModal'))?.hide();
                location.reload();
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    };
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/carrier-sheet/show.blade.php ENDPATH**/ ?>