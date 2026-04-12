<?php $__env->startSection('title'); ?>
    Carrier Sheet — Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ════════════════════════════════════════════════════════
   CARRIER SHEET DASHBOARD  — D.B replica
   ════════════════════════════════════════════════════════ */
:root {
    --cs-indigo:  #283593; --cs-green: #2E7D32; --cs-purple: #4527A0;
    --cs-red:     #C62828; --cs-blue:  #1565C0; --cs-amber:  #F57F17;
    --cs-orange:  #E65100;
    --cs-surface: var(--bs-card-bg, #ffffff);
    --cs-border:  rgba(0,0,0,.07);
    --cs-shadow:  0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --cs-text-1:  var(--bs-body-color, #0f172a);
    --cs-text-2:  var(--bs-surface-700, #374151);
    --cs-text-3:  var(--bs-surface-500, #64748b);
}
.cs-page { width: 100%; }

/* ── Header ────────────────────────────────────────── */
.cs-hdr {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem;
}
.cs-hdr-left { display: flex; align-items: center; gap: .6rem; }
.cs-hdr-icon {
    width: 32px; height: 32px; border-radius: .45rem; flex-shrink: 0;
    background: linear-gradient(135deg, var(--cs-indigo), #1a237e);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 6px rgba(40,53,147,.35);
}
.cs-hdr-icon i { font-size: 1rem; color: #fff; }
.cs-hdr h5 { margin:0; font-size:1rem; font-weight:800; color:var(--cs-text-1); }
.cs-hdr-sub {
    font-size: .68rem; color: var(--cs-text-3); font-weight: 400;
    border-left: 2px solid var(--cs-border); padding-left: .5rem; margin-left: .1rem;
}
.cs-back {
    font-size:.7rem; font-weight:700; padding:.28rem .6rem; border-radius:20px;
    border:1.5px solid var(--cs-border); background:transparent; color:var(--cs-text-3);
    text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s;
}
.cs-back:hover { border-color: var(--cs-indigo); color: #1a237e; }

/* ── Filter bar ────────────────────────────────────── */
.cs-filter {
    display:flex; flex-wrap:wrap; gap:.5rem; align-items:flex-end;
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; padding:.55rem .8rem; margin-bottom:1rem;
    box-shadow:var(--cs-shadow);
}
.cs-filter label {
    font-size:.58rem; font-weight:800; text-transform:uppercase;
    letter-spacing:.6px; color:var(--cs-text-3); display:block; margin-bottom:.12rem;
}
.cs-filter select, .cs-filter input[type=month] {
    font-size:.73rem; padding:.28rem .45rem; border-radius:.4rem;
    border:1.5px solid var(--cs-border); background:var(--bs-input-bg, #f8fafc);
    color:var(--cs-text-1); outline:none; transition:border-color .15s;
}
.cs-filter select:focus, .cs-filter input:focus {
    border-color:var(--cs-indigo); box-shadow:0 0 0 2px rgba(40,53,147,.15);
}
.cs-btn {
    font-size:.7rem; font-weight:700; padding:.32rem .7rem; border-radius:20px;
    border:none; cursor:pointer; display:inline-flex; align-items:center;
    gap:.22rem; transition:all .15s; text-decoration:none;
}
.cs-btn-primary { background:linear-gradient(135deg, var(--cs-indigo), #1a237e); color:#fff; }
.cs-btn-primary:hover { box-shadow:0 2px 10px rgba(40,53,147,.4); transform:translateY(-1px); color:#fff; }
.cs-btn-outline { background:transparent; border:1.5px solid var(--cs-border)!important; color:var(--cs-text-3); }
.cs-btn-outline:hover { border-color:var(--cs-indigo)!important; color:#1a237e; }
.cs-btn-success { background:linear-gradient(135deg, #2E7D32, #1B5E20); color:#fff; }
.cs-btn-success:hover { box-shadow:0 2px 10px rgba(46,125,50,.4); color:#fff; }
.cs-btn-danger { background:linear-gradient(135deg, #C62828, #B71C1C); color:#fff; }

/* ── Table ─────────────────────────────────────────── */
.cs-card {
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; box-shadow:var(--cs-shadow); overflow:hidden;
}
.cs-table { width:100%; font-size:.73rem; border-collapse:collapse; }
.cs-table thead th {
    background:#1a237e; color:#fff; font-weight:700; font-size:.62rem;
    text-transform:uppercase; letter-spacing:.5px; padding:.45rem .5rem;
    text-align:center; white-space:nowrap; border:none;
}
.cs-table tbody td {
    padding:.38rem .5rem; border-bottom:1px solid var(--cs-border);
    text-align:center; vertical-align:middle; color:var(--cs-text-1);
}
.cs-table tbody tr:hover { background: rgba(40,53,147,.04); }

/* ── Grand total row ───────────────────────────────── */
.cs-table .cs-total td {
    background: #1B5E20; color: #fff; font-weight: 800; font-size: .74rem;
    border: none; padding: .5rem;
}

/* ── Number formatting ─────────────────────────────── */
.cs-money { font-weight:700; font-variant-numeric:tabular-nums; }
.cs-money-pos { color:#2E7D32; }
.cs-money-neg { color:#C62828; }

/* ── Status count pills ────────────────────────────── */
.cs-pill {
    display:inline-block; min-width:28px; padding:.14rem .4rem;
    border-radius:.25rem; font-weight:700; font-size:.65rem;
    text-align:center; color:#fff;
}
.cs-pill-apps  { background:var(--cs-blue); }
.cs-pill-paid  { background:var(--cs-green); }
.cs-pill-appr  { background:var(--cs-amber); }
.cs-pill-cb    { background:var(--cs-red); }
.cs-pill-dec   { background:var(--cs-orange); }

/* ── Carrier link ──────────────────────────────────── */
.cs-carrier-link {
    font-weight:700; color:var(--cs-text-1); text-decoration:none;
    display:flex; align-items:center; gap:.3rem; white-space:nowrap;
}
.cs-carrier-link:hover { color:var(--cs-indigo); }
.cs-carrier-dot {
    width:10px; height:10px; border-radius:2px; flex-shrink:0;
}

/* ── Actions row ───────────────────────────────────── */
.cs-actions {
    display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cs-page">
    
    <div class="cs-hdr">
        <div class="cs-hdr-left">
            <div class="cs-hdr-icon"><i class="bx bx-spreadsheet"></i></div>
            <div>
                <h5>Commission Dashboard</h5>
                <span class="cs-hdr-sub">Carrier commission tracking workbook</span>
            </div>
        </div>
        <a href="<?php echo e(route('settings.reports.hub')); ?>" class="cs-back"><i class="bx bx-arrow-back"></i> Reports Hub</a>
    </div>

    
    <form class="cs-filter" method="GET" action="<?php echo e(route('settings.reports.carrier-sheet.dashboard')); ?>">
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
            <button type="button" class="cs-btn cs-btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-import"></i> Import .xlsx
            </button>
            <?php endif; ?>
            <a href="<?php echo e(route('settings.reports.carrier-sheet.rates')); ?>" class="cs-btn cs-btn-outline">
                <i class="bx bx-cog"></i> Rates
            </a>
        </div>
    </form>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('import_results')): ?>
    <div class="alert alert-info alert-dismissible fade show" style="font-size:.75rem;">
        <strong>Import Results:</strong>
        <ul class="mb-0 mt-1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = session('import_results'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($r['sheet']); ?>: <?php echo e($r['status'] === 'imported' ? $r['imported'].' imported, '.$r['skipped'].' skipped' : 'Skipped — '.$r['reason']); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="cs-card">
        <table class="cs-table">
            <thead>
                <tr>
                    <th style="text-align:left; width:30px;">#</th>
                    <th style="text-align:left;">Carrier</th>
                    <th>Total Apps</th>
                    <th>Paid</th>
                    <th>Approved</th>
                    <th>Chargeback</th>
                    <th>Declined</th>
                    <th>Commission ($)</th>
                    <th>Paid Amt ($)</th>
                    <th>Chargeback ($)</th>
                    <th>Balance ($)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align:left;"><?php echo e($i + 1); ?></td>
                    <td style="text-align:left;">
                        <a href="<?php echo e(route('settings.reports.carrier-sheet.show', ['rate' => $row['carrier']->id, 'month' => $periodMonth])); ?>" class="cs-carrier-link">
                            <span class="cs-carrier-dot" style="background:<?php echo e($row['carrier']->title_color); ?>;"></span>
                            <?php echo e($row['carrier']->carrier_label); ?>

                        </a>
                    </td>
                    <td><span class="cs-pill cs-pill-apps"><?php echo e($row['total_apps']); ?></span></td>
                    <td><span class="cs-pill cs-pill-paid"><?php echo e($row['paid_count']); ?></span></td>
                    <td><span class="cs-pill cs-pill-appr"><?php echo e($row['approved_count']); ?></span></td>
                    <td><span class="cs-pill cs-pill-cb"><?php echo e($row['chargeback_count']); ?></span></td>
                    <td><span class="cs-pill cs-pill-dec"><?php echo e($row['declined_count']); ?></span></td>
                    <td class="cs-money <?php echo e($row['commission'] >= 0 ? 'cs-money-pos' : 'cs-money-neg'); ?>">
                        <?php echo e(number_format($row['commission'], 4)); ?>

                    </td>
                    <td class="cs-money"><?php echo e(number_format($row['paid'], 4)); ?></td>
                    <td class="cs-money cs-money-neg"><?php echo e(number_format($row['chargeback_total'], 4)); ?></td>
                    <td class="cs-money <?php echo e($row['balance'] >= 0 ? 'cs-money-pos' : 'cs-money-neg'); ?>">
                        <?php echo e(number_format($row['balance'], 4)); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="cs-total">
                    <td colspan="2" style="text-align:left;">GRAND TOTAL</td>
                    <td><span class="cs-pill cs-pill-apps"><?php echo e($totals['total_apps']); ?></span></td>
                    <td><span class="cs-pill cs-pill-paid"><?php echo e($totals['paid_count']); ?></span></td>
                    <td><span class="cs-pill cs-pill-appr"><?php echo e($totals['approved_count']); ?></span></td>
                    <td><span class="cs-pill cs-pill-cb"><?php echo e($totals['chargeback_count']); ?></span></td>
                    <td><span class="cs-pill cs-pill-dec"><?php echo e($totals['declined_count']); ?></span></td>
                    <td class="cs-money" style="color:#fff;"><?php echo e(number_format($totals['commission'], 4)); ?></td>
                    <td class="cs-money" style="color:#fff;"><?php echo e(number_format($totals['paid'], 4)); ?></td>
                    <td class="cs-money" style="color:#fff;"><?php echo e(number_format($totals['chargeback_total'], 4)); ?></td>
                    <td class="cs-money" style="color:#fff;"><?php echo e(number_format($totals['balance'], 4)); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="<?php echo e(route('settings.reports.carrier-sheet.import')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bx bx-import me-1"></i> Import Excel Workbook</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.72rem; color:var(--cs-text-3);">
                    Upload the Carrier Sheet workbook (.xlsx). Each sheet (T.A F-1, AIG Y-1, etc.) will be imported into its matching carrier.
                    The RATES and D.B sheets are skipped automatically.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:.72rem;">Excel File</label>
                    <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                </div>
                <div class="alert alert-warning py-2 px-3" style="font-size:.68rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    Period is auto-detected from each row's date. Commission &amp; balance are calculated from rates. No existing data is deleted — new rows are appended.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-check me-1"></i> Import</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/carrier-sheet/dashboard.blade.php ENDPATH**/ ?>