<?php $__env->startSection('title'); ?> Sales <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --pd-indigo: #4f46e5;
    --pd-green:  #059669;
    --pd-br:     .6rem;
    --pd-sh:     0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
}

/* Page header */
.ps-hdr{margin-bottom:1.25rem;}
.ps-hdr h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
.ps-hdr p{font-size:.84rem;color:#6b7280;margin:0;}

/* Period filter (mirrors dashboard) */
.pd-period-form{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;padding:.6rem .85rem;background:#f8fafc;border:1px solid rgba(0,0,0,.07);border-radius:.45rem;margin-bottom:1rem;}
.pd-period-label{font-size:.66rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#9ca3af;}
.pd-period-input{background:#fff;border:1px solid rgba(0,0,0,.12);border-radius:.3rem;padding:.3rem .55rem;font-size:.82rem;color:#374151;}
.pd-period-btn{background:rgba(79,70,229,.08);border:1px solid rgba(79,70,229,.2);color:#4f46e5;padding:.3rem .7rem;border-radius:.3rem;font-size:.8rem;font-weight:700;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:.25rem;}
.pd-period-btn:hover{background:rgba(79,70,229,.16);}
.pd-period-btn-reset{background:rgba(220,38,38,.07);border-color:rgba(220,38,38,.2);color:#dc2626;}
.pd-period-btn-reset:hover{background:rgba(220,38,38,.14);}

/* Stats strip */
.pd-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:.85rem;margin-bottom:1.25rem;}
@media(max-width:768px){.pd-stats{grid-template-columns:1fr 1fr;}}
.pd-stat{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);padding:.85rem 1rem;box-shadow:var(--pd-sh);display:flex;align-items:center;gap:.75rem;}
.pd-stat-icon{width:38px;height:38px;border-radius:.4rem;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.si-violet{background:rgba(109,40,217,.1);color:#6d28d9;}
.si-green{background:rgba(5,150,105,.1);color:#059669;}
.si-amber{background:rgba(217,119,6,.1);color:#d97706;}
.pd-stat-val{font-size:1.3rem;font-weight:900;letter-spacing:-.3px;line-height:1;color:#111827;}
.pd-stat-lbl{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-top:.1rem;}
.pd-stat-sub{font-size:.7rem;color:#d1d5db;}

/* Card */
.pd-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);box-shadow:var(--pd-sh);overflow:hidden;margin-bottom:1rem;}
.pd-head{padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.06);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.pd-head h6{font-size:.88rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.35rem;color:#111827;}
.pd-head h6 i{color:var(--pd-indigo);}
.pd-count{background:rgba(79,70,229,.08);color:var(--pd-indigo);font-size:.7rem;font-weight:700;padding:.12rem .45rem;border-radius:.2rem;}
.pd-body{padding:1rem 1.1rem;}

/* Table */
.pd-table{width:100%;border-collapse:collapse;font-size:.83rem;}
.pd-table thead th{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;border-bottom:1px solid rgba(0,0,0,.08);padding:.55rem .85rem;background:#f9fafb;white-space:nowrap;}
.pd-table tbody td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#374151;}
.pd-table tfoot td{padding:.6rem .85rem;font-weight:700;border-top:2px solid rgba(0,0,0,.08);background:#fafafa;font-size:.82rem;}
.pd-table tbody tr:hover{background:rgba(79,70,229,.022);}
.pd-table tbody tr:last-child td{border-bottom:none;}

/* Status chips */
.sc{font-size:.66rem;font-weight:700;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;text-transform:capitalize;}
.sc-ok{background:#d1fae5;color:#065f46;}
.sc-warn{background:#fef9c3;color:#713f12;}
.sc-info{background:#dbeafe;color:#1e3a8a;}
.sc-danger{background:#fee2e2;color:#7f1d1d;}
.sc-def{background:#f3f4f6;color:#374151;}

/* State pill */
.pd-state-pill{display:inline-block;font-size:.62rem;font-weight:700;padding:.08rem .32rem;border-radius:.18rem;background:rgba(79,70,229,.08);color:var(--pd-indigo);letter-spacing:.15px;}

/* Colors */
.col-cr{color:#059669;font-weight:700;}
.col-dr{color:#4f46e5;font-weight:700;}
.col-dim{color:#d1d5db;}

/* Micro bar */
.mbar{height:3px;border-radius:999px;background:#e5e7eb;margin-top:.28rem;overflow:hidden;}
.mbar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#0d9488,#34d399);max-width:100%;}

/* Empty */
.pd-empty{text-align:center;padding:2.5rem 1rem;}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.4rem;opacity:.2;color:#9ca3af;}
.pd-empty p{font-size:.84rem;color:#9ca3af;margin:0;}

/* Dark themes */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card,.pd-stat{background:var(--bg-card,#1e1e2e)!important;border-color:var(--border-color,rgba(255,255,255,.08))!important;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table thead th{background:var(--bg-secondary,#16162a);color:var(--text-muted,#888);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody td{color:var(--text-primary,#ddd);border-color:var(--border-color,rgba(255,255,255,.04));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-period-form{background:var(--bg-secondary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-period-input{background:var(--bg-tertiary,rgba(255,255,255,.06));border-color:var(--border-color,rgba(255,255,255,.1));color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-stat-val{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ps-hdr h4{color:var(--text-primary,#e0e0e0);}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="ps-hdr">
    <h4><i class="bx bx-trending-up" style="color:#059669;margin-right:.35rem;"></i>Sales</h4>
    <p>Your leads and sales performance for the selected period.</p>
</div>


<form method="GET" action="<?php echo e(route('partner.sales')); ?>" class="pd-period-form" id="ps-filter-form">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrierId): ?><input type="hidden" name="carrier_id" value="<?php echo e($carrierId); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <span class="pd-period-label"><i class="bx bx-calendar-alt"></i> Period</span>
    <select class="pd-period-input" id="ps-filter-mode" style="width:auto;" onchange="toggleFilterMode()">
        <option value="month" <?php echo e(!request('date_from') ? 'selected' : ''); ?>>Month</option>
        <option value="range" <?php echo e(request('date_from') ? 'selected' : ''); ?>>Date range</option>
    </select>
    <span id="ps-month-wrap" style="<?php echo e(request('date_from') ? 'display:none' : 'display:flex'); ?>;align-items:center;gap:.4rem;">
        <input type="month" name="month" class="pd-period-input" value="<?php echo e($month); ?>">
    </span>
    <span id="ps-range-wrap" style="<?php echo e(request('date_from') ? 'display:flex' : 'display:none'); ?>;align-items:center;gap:.35rem;">
        <input type="date" name="date_from" class="pd-period-input" style="width:130px;" value="<?php echo e(request('date_from')); ?>" placeholder="From">
        <span style="color:#9ca3af;font-size:.8rem;">→</span>
        <input type="date" name="date_to"   class="pd-period-input" style="width:130px;" value="<?php echo e(request('date_to')); ?>" placeholder="To">
    </span>

    <span class="pd-period-label" style="margin-left:.35rem;"><i class="bx bx-filter"></i> Status</span>
    <select name="status" class="pd-period-input" style="width:auto;">
        <option value="">All</option>
        <option value="pending"   <?php echo e(request('status') === 'pending'   ? 'selected' : ''); ?>>Pending</option>
        <option value="accepted"  <?php echo e(request('status') === 'accepted'  ? 'selected' : ''); ?>>Approved</option>
        <option value="sale"      <?php echo e(request('status') === 'sale'      ? 'selected' : ''); ?>>Sale</option>
        <option value="declined"  <?php echo e(request('status') === 'declined'  ? 'selected' : ''); ?>>Declined</option>
        <option value="chargeback" <?php echo e(request('status') === 'chargeback' ? 'selected' : ''); ?>>Chargeback</option>
        <option value="closed"    <?php echo e(request('status') === 'closed'    ? 'selected' : ''); ?>>Closed</option>
    </select>

    <input type="text" name="search" class="pd-period-input" style="width:160px;" placeholder="Search customer…" value="<?php echo e(request('search')); ?>">

    <button type="submit" class="pd-period-btn"><i class="bx bx-filter-alt"></i> Apply</button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('date_from') || request('status') || request('search') || (request('month') && request('month') !== now()->format('Y-m'))): ?>
    <a href="<?php echo e(route('partner.sales', $carrierId ? ['carrier_id' => $carrierId] : [])); ?>" class="pd-period-btn pd-period-btn-reset" style="text-decoration:none;"><i class="bx bx-reset"></i></a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</form>


<?php echo $__env->make('partner.partials.carrier-filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="pd-stats">
    <div class="pd-stat">
        <div class="pd-stat-icon si-violet"><i class="bx bx-file"></i></div>
        <div>
            <div class="pd-stat-val"><?php echo e($monthlyLeads); ?></div>
            <div class="pd-stat-lbl">Leads this period</div>
            <div class="pd-stat-sub"><?php echo e($pendingLeads); ?> pending</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-green"><i class="bx bx-check-shield"></i></div>
        <div>
            <div class="pd-stat-val"><?php echo e($totalSales); ?></div>
            <div class="pd-stat-lbl">Sales this period</div>
            <div class="pd-stat-sub"><?php echo e($monthlyLeads > 0 ? number_format($totalSales / $monthlyLeads * 100, 1) : 0); ?>% close rate</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-amber"><i class="bx bx-wallet"></i></div>
        <div>
            <div class="pd-stat-val">$<?php echo e(number_format($revenueByCarrier->sum('partner_share'), 0)); ?></div>
            <div class="pd-stat-lbl">Your earned share</div>
            <div class="pd-stat-sub">After <?php echo e($taurusPct); ?>% Taurus fee</div>
        </div>
    </div>
</div>


<div class="row g-3">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revenueByCarrier->count() > 0 && !$carrierId): ?>
    <div class="col-12">
        <div class="pd-card">
            <div class="pd-head">
                <h6><i class="bx bx-bar-chart-alt-2"></i> Revenue by Carrier</h6>
            </div>
            <div class="pd-body" style="padding:0;">
                <?php $maxR = $revenueByCarrier->max('partner_share') ?: 1; ?>
                <table class="pd-table">
                    <thead><tr><th>Carrier</th><th class="text-end">Your Share</th><th class="text-end">Sales</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $revenueByCarrier->sortByDesc('partner_share'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div style="font-weight:700;font-size:.82rem;"><?php echo e($r['carrier']->name ?? 'Unknown'); ?></div>
                                <div class="mbar"><div class="mbar-fill" style="width:<?php echo e(min(100, ($r['partner_share'] / $maxR) * 100)); ?>%;"></div></div>
                            </td>
                            <td class="text-end col-cr">$<?php echo e(number_format($r['partner_share'], 0)); ?></td>
                            <td class="text-end" style="color:#6b7280;"><?php echo e($r['sales_count']); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="text-end col-cr">$<?php echo e(number_format($revenueByCarrier->sum('partner_share'), 0)); ?></td>
                            <td class="text-end"><?php echo e($revenueByCarrier->sum('sales_count')); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="col-12">
        <div class="pd-card">
            <div class="pd-head">
                <h6><i class="bx bx-list-ul"></i> Leads &amp; Sales</h6>
                <span class="pd-count"><?php echo e($leads->count()); ?></span>
            </div>
            <div style="overflow-x:auto;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leads->count() > 0): ?>
                <div style="padding:.35rem .85rem;font-size:.68rem;color:#9ca3af;background:#fafafa;border-bottom:1px solid rgba(0,0,0,.04);">
                    <span style="color:#a78bfa;font-weight:800;">~</span> = estimated (premium &times; 9 &times; carrier%) — finalised once policy is accepted
                </div>
                <table class="pd-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$carrierId): ?><th>Carrier</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th>State</th>
                            <th>Status</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Your Share</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $scCls = match(strtolower($lead->status ?? '')) {
                                'sale','approved','accepted','done' => 'sc-ok',
                                'pending' => 'sc-warn',
                                'issued'  => 'sc-info',
                                'declined','cancelled' => 'sc-danger',
                                default => 'sc-def',
                            };
                            $statusLabel = match(strtolower($lead->status ?? '')) {
                                'sale','approved','accepted','done' => 'Approved',
                                'pending'   => 'Pending',
                                'issued'    => 'Issued',
                                'declined'  => 'Declined',
                                'cancelled' => 'Cancelled',
                                default => ucfirst($lead->status ?? '—'),
                            };
                            $comm    = (float)($lead->agent_commission ?? 0);
                            $premium = (float)($lead->monthly_premium ?? 0);
                            $cPct    = (float)($lead->insuranceCarrier->base_commission_percentage ?? 0);
                            $isEst = false;
                            if ($comm <= 0 && $premium > 0 && $cPct > 0) {
                                $comm  = $premium * 9 * ($cPct / 100);
                                $isEst = true;
                            }
                            $hasComm = $comm > 0;
                            $share   = $hasComm ? $comm - ($comm * $taurusPct / 100) : null;
                        ?>
                        <tr>
                            <td>
                                <div style="font-weight:700;font-size:.84rem;"><?php echo e($lead->cn_name ?? '—'); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($premium > 0): ?><div style="font-size:.7rem;color:#9ca3af;">$<?php echo e(number_format($premium,2)); ?>/mo</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$carrierId): ?>
                            <td style="font-size:.8rem;color:#6b7280;"><?php echo e($lead->insuranceCarrier->name ?? '—'); ?></td>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <td><span class="pd-state-pill"><?php echo e($lead->state ?? '—'); ?></span></td>
                            <td><span class="sc <?php echo e($scCls); ?>"><?php echo e($statusLabel); ?></span></td>
                            <td class="text-end">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasComm): ?>
                                <span class="<?php echo e($isEst ? '' : 'col-dr'); ?>" style="<?php echo e($isEst ? 'color:#a78bfa;' : ''); ?>"
                                      <?php if($isEst): ?> title="Estimated: premium × 9 × <?php echo e($cPct); ?>%" <?php endif; ?>>
                                    <?php echo e($isEst ? '~' : ''); ?>$<?php echo e(number_format($comm, 2)); ?>

                                </span>
                                <?php else: ?><span class="col-dim">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($share !== null): ?>
                                <span class="<?php echo e($isEst ? '' : 'col-cr'); ?>" style="<?php echo e($isEst ? 'color:#818cf8;' : ''); ?>"
                                      <?php if($isEst): ?> title="Est. partner share after <?php echo e($taurusPct); ?>% fee" <?php endif; ?>>
                                    <?php echo e($isEst ? '~' : ''); ?>$<?php echo e(number_format($share, 2)); ?>

                                </span>
                                <?php else: ?><span class="col-dim">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.75rem;color:#9ca3af;white-space:nowrap;"><?php echo e($lead->created_at->format('M d, Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="pd-empty"><i class="bx bx-inbox"></i><p>No leads for this period<?php echo e($carrierId ? ' and carrier' : ''); ?>.</p></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
function toggleFilterMode() {
    var mode = document.getElementById('ps-filter-mode').value;
    var mw = document.getElementById('ps-month-wrap');
    var rw = document.getElementById('ps-range-wrap');
    if (mode === 'range') {
        mw.style.display = 'none';
        rw.style.display = 'flex';
        var inp = document.querySelector('[name="month"]');
        if (inp) inp.value = '';
    } else {
        mw.style.display = 'flex';
        rw.style.display = 'none';
        ['date_from','date_to'].forEach(function(n){
            var el = document.querySelector('[name="'+n+'"]');
            if (el) el.value = '';
        });
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.partner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/partner/sales.blade.php ENDPATH**/ ?>