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
.cs-filter select, .cs-filter input[type=month] {
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
.cs-btn-outline { background:transparent; border:1.5px solid var(--cs-border)!important; color:var(--cs-text-3); }
.cs-btn-outline:hover { border-color:var(--cs-title)!important; color:var(--cs-title); }

/* ── Opening CB row ────────────────────────────────── */
.cs-opening-cb {
    display:flex; align-items:center; gap:.5rem; background:#FFF3CD;
    border:1px solid #ffe69c; border-radius:.45rem; padding:.35rem .7rem;
    margin-bottom:.5rem; font-size:.72rem;
}
.cs-opening-cb label { font-weight:700; margin:0; color:#856404; }
.cs-opening-cb input {
    width:100px; padding:.2rem .35rem; font-size:.72rem; border:1px solid #ffe69c;
    border-radius:.3rem; text-align:right;
}

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
.cs-dtable tr.cs-row-approved   { background:#FFF8E1 !important; }
.cs-dtable tr.cs-row-chargeback { background:#FFEBEE !important; }
.cs-dtable tr.cs-row-declined   { background:#FFF3E0 !important; }
.cs-dtable tr.cs-row-paid:nth-child(even) { background:var(--cs-row-alt); }

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
            <span class="cs-badge-val" id="badge-commission"><?php echo e(number_format($summary['commission'], 4)); ?></span>
            <span class="cs-badge-lbl">Commission</span>
        </div>
        <div class="cs-badge cs-badge-paid">
            <span class="cs-badge-val" id="badge-paid"><?php echo e(number_format($summary['paid'], 4)); ?></span>
            <span class="cs-badge-lbl">Paid</span>
        </div>
        <div class="cs-badge cs-badge-balance">
            <span class="cs-badge-val" id="badge-balance"><?php echo e(number_format($summary['balance'], 4)); ?></span>
            <span class="cs-badge-lbl">Balance</span>
        </div>
        <div class="cs-badge cs-badge-cb-total">
            <span class="cs-badge-val" id="badge-cb-total"><?php echo e(number_format($summary['chargeback_total'], 4)); ?></span>
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
        <div style="margin-left:auto; display:flex; gap:.4rem; align-items:flex-end;">
            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
            <button type="button" class="cs-btn cs-btn-success" data-bs-toggle="modal" data-bs-target="#addEntryModal">
                <i class="bx bx-plus"></i> Add Entry
            </button>
            <?php endif; ?>
            <a href="<?php echo e(route('settings.reports.carrier-sheet.export', ['rate' => $rate, 'month' => $periodMonth])); ?>" class="cs-btn cs-btn-outline">
                <i class="bx bx-download"></i> Export CSV
            </a>
        </div>
    </form>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openingCb): ?>
    <div class="cs-opening-cb">
        <label><i class="bx bx-error-circle me-1"></i> Opening Chargeback:</label>
        <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
        <input type="number" step="0.01" min="0" value="<?php echo e($openingCb->amount); ?>" id="openingCbAmount"
               onchange="updateOpeningCb(this.value)">
        <span style="font-size:.6rem; color:#856404;">(Previous chargeback balance carried forward)</span>
        <?php else: ?>
        <span class="fw-bold"><?php echo e(number_format($openingCb->amount, 4)); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
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
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <th style="width:50px;"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="cs-row-<?php echo e(strtolower($entry->status)); ?>" data-entry-id="<?php echo e($entry->id); ?>">
                    <td><?php echo e($entry->sr_number); ?></td>
                    <td><?php echo e($entry->entry_date?->format('d-M-y')); ?></td>
                    <td class="cs-left"><?php echo e($entry->policy_number); ?></td>
                    <td class="cs-left"><?php echo e($entry->name); ?></td>
                    <td><?php echo e($entry->face_value); ?></td>
                    <td class="cs-money"><?php echo e(number_format($entry->premium, 4)); ?></td>
                    <td>
                        <span style="text-transform:uppercase; font-size:.6rem; font-weight:600;"><?php echo e($entry->policy_type); ?></span>
                    </td>
                    <td>
                        <span class="cs-status cs-status-<?php echo e(strtolower($entry->status)); ?>"><?php echo e(ucfirst($entry->status)); ?></span>
                    </td>
                    <td><?php echo e($entry->draft_date?->format('d M')); ?></td>
                    <td><?php echo e($entry->payment_date?->format('d M')); ?></td>
                    <td class="cs-money <?php echo e($entry->commission ? 'cs-money-pos' : ''); ?>">
                        <?php echo e($entry->commission !== null ? number_format($entry->commission, 4) : ''); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->rate_override): ?>
                        <span class="cs-override-indicator" title="Rate override: <?php echo e($entry->rate_override); ?>">★</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="cs-money"><?php echo e($entry->paid_amount > 0 ? number_format($entry->paid_amount, 4) : ''); ?></td>
                    <td class="cs-money <?php echo e($entry->balance >= 0 ? 'cs-money-pos' : 'cs-money-neg'); ?>">
                        <?php echo e(number_format($entry->balance, 4)); ?>

                    </td>
                    <td class="cs-money <?php echo e($entry->chargeback_amount > 0 ? 'cs-money-neg' : ''); ?>">
                        <?php echo e($entry->chargeback_amount > 0 ? number_format($entry->chargeback_amount, 4) : ''); ?>

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
                <tr><td colspan="15" style="text-align:center; padding:2rem; color:var(--cs-text-3);">No entries yet. Click "Add Entry" or import an Excel file.</td></tr>
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
                        <td style="text-align:right;" class="cs-money cs-money-pos"><?php echo e(number_format($day['commission'], 4)); ?></td>
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
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:.68rem; font-weight:700;">Name</label>
                            <input type="text" name="name" class="form-control form-control-sm">
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
                        <div class="col-md-8">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
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
        return Number(v).toLocaleString('en-US', {minimumFractionDigits:4, maximumFractionDigits:4});
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

    // ── Edit Entry ──────────────────────────────────────
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
        document.getElementById('edit_notes').value = e.notes || '';
        new bootstrap.Modal(document.getElementById('editEntryModal')).show();
    };

    window.submitEditEntry = async function() {
        const form = document.getElementById('editEntryForm');
        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());
        const id = data.entry_id;
        delete data.entry_id;
        for (const k of ['rate_override']) {
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

    // ── Opening Chargeback ──────────────────────────────
    window.updateOpeningCb = async function(val) {
        if (!PERIOD) return alert('Please select a month first');
        try {
            const res = await ajax(`${BASE}/${RATE_ID}/opening-chargeback`, 'PUT', {
                amount: parseFloat(val) || 0,
                period_month: PERIOD
            });
            if (res.success) updateBadges(res.summary);
        } catch (e) {
            alert('Error: ' + e.message);
        }
    };
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/carrier-sheet/show.blade.php ENDPATH**/ ?>