<?php $__env->startSection('title'); ?> Partner Dashboard <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════════════
   PARTNER DASHBOARD  ·  Clean & Focused
   ═══════════════════════════════════════════════════════════ */
:root {
    --pd-indigo:  #4f46e5;
    --pd-green:   #059669;
    --pd-red:     #dc2626;
    --pd-amber:   #d97706;
    --pd-teal:    #0d9488;
    --pd-br:      .6rem;
    --pd-sh:      0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
    --pd-ease:    cubic-bezier(.22,1,.36,1);
}

/* ── Push nav-badge from hero --*/
@keyframes pd-fadein{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
.pd-anim{animation:pd-fadein .45s var(--pd-ease) both;}
.pd-d1{animation-delay:.04s;}.pd-d2{animation-delay:.08s;}.pd-d3{animation-delay:.12s;}.pd-d4{animation-delay:.16s;}.pd-d5{animation-delay:.22s;}

/* ── Hero ── */
.pd-hero{
    background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);
    padding:1.75rem 2rem 1.5rem;
    position:relative;overflow:hidden;
    margin-bottom:1.5rem;
}
.pd-hero::after{
    content:'';position:absolute;width:380px;height:380px;
    background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);
    top:-80px;right:-60px;pointer-events:none;
}
.pd-hero-body{position:relative;z-index:1;}

/* top row */
.pd-hero-top{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;}
.pd-hero-name{font-size:1.35rem;font-weight:900;color:#fff;letter-spacing:-.3px;line-height:1.1;}
.pd-hero-meta{margin-top:.3rem;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;}
.pd-chip{font-size:.66rem;font-weight:700;padding:.18rem .55rem;border-radius:999px;text-transform:uppercase;letter-spacing:.5px;}
.pd-chip-glass{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.72);}
.pd-chip-green{background:rgba(5,150,105,.25);border:1px solid rgba(5,150,105,.45);color:#6ee7b7;display:inline-flex;align-items:center;gap:.3rem;}
.pd-chip-red{background:rgba(220,38,38,.2);border:1px solid rgba(220,38,38,.35);color:#fca5a5;}
.pd-chip-amber{background:rgba(245,158,11,.18);border:1px solid rgba(245,158,11,.3);color:#fde68a;}
.pd-pulse{width:5px;height:5px;border-radius:50%;background:#6ee7b7;display:inline-block;animation:pd-pulse-dot 1.8s ease-in-out infinite;}
@keyframes pd-pulse-dot{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.35;transform:scale(.6);}}

/* period form */
.pd-period-form{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;}
.pd-period-label{font-size:.66rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.35);}
.pd-period-input{
    background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
    border-radius:.35rem;padding:.35rem .6rem;font-size:.82rem;color:#fff;width:140px;
}
.pd-period-input:focus{outline:none;border-color:rgba(255,255,255,.4);background:rgba(255,255,255,.12);}
.pd-period-btn{
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;
    padding:.35rem .75rem;border-radius:.35rem;font-size:.8rem;font-weight:600;cursor:pointer;
    transition:background .15s;display:inline-flex;align-items:center;gap:.25rem;
}
.pd-period-btn:hover{background:rgba(255,255,255,.22);}

/* KPI grid */
.pd-hero-kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:.85rem;}
@media(max-width:768px){.pd-hero-kpis{grid-template-columns:1fr 1fr;}}

.pd-kpi{
    background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    border-radius:var(--pd-br);padding:.9rem 1.1rem;position:relative;overflow:hidden;
    transition:background .2s,transform .2s var(--pd-ease);cursor:default;
}
.pd-kpi:hover{background:rgba(255,255,255,.1);transform:translateY(-1px);}
.pd-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;border-radius:2px 2px 0 0;}
.pd-kpi.k-indigo::before{background:linear-gradient(90deg,#6366f1,#a5b4fc);}
.pd-kpi.k-green::before{background:linear-gradient(90deg,#059669,#34d399);}
.pd-kpi.k-slate::before{background:linear-gradient(90deg,#475569,#94a3b8);}

.pd-kpi-icon{font-size:1.1rem;margin-bottom:.4rem;opacity:.45;}
.pd-kpi.k-indigo .pd-kpi-icon{color:#a5b4fc;}
.pd-kpi.k-green .pd-kpi-icon{color:#34d399;}
.pd-kpi.k-slate .pd-kpi-icon{color:#94a3b8;}

.pd-kpi-val{font-size:1.75rem;font-weight:900;color:#fff;letter-spacing:-.8px;line-height:1;margin-bottom:.3rem;}
.pd-kpi-lbl{font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:rgba(255,255,255,.38);}
.pd-kpi-sub{font-size:.72rem;color:rgba(255,255,255,.3);margin-top:.2rem;}

/* progress micro-bar */
.pd-bar{height:2px;background:rgba(255,255,255,.1);border-radius:999px;margin-top:.6rem;overflow:hidden;}
.pd-bar-fill{height:100%;border-radius:999px;}
.k-indigo .pd-bar-fill{background:linear-gradient(90deg,#6366f1,#a5b4fc);}
.k-green .pd-bar-fill{background:linear-gradient(90deg,#059669,#34d399);}

/* Chargeback info note */
.pd-cb-note{
    margin-top:.85rem;
    padding:.55rem .85rem;
    background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);
    border-radius:.4rem;
    font-size:.76rem;color:rgba(255,255,255,.55);
    display:flex;align-items:center;gap:.5rem;
}
.pd-cb-note strong{color:#fde68a;}
.pd-cb-note i{color:#fbbf24;font-size:.9rem;flex-shrink:0;}

/* ── Balance alert ── */
.pd-balance-alert{
    display:flex;align-items:center;gap:.65rem;
    padding:.8rem 1.25rem;border-radius:var(--pd-br);
    margin-bottom:1.25rem;border:1px solid;
    font-size:.86rem;font-weight:600;
    box-shadow:var(--pd-sh);
}
.pd-balance-alert.owe{background:#fef2f2;color:#7f1d1d;border-color:#fecaca;}
.pd-balance-alert.credit{background:#f0fdf4;color:#14532d;border-color:#bbf7d0;}
.pd-balance-alert i{font-size:1.05rem;flex-shrink:0;}
.pd-balance-alert .amt{font-weight:900;font-size:.95rem;}

/* ── Stats strip ── */
.pd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;margin-bottom:1.25rem;}
@media(max-width:900px){.pd-stats{grid-template-columns:repeat(2,1fr);}}

.pd-stat{
    background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);
    padding:.85rem 1rem;box-shadow:var(--pd-sh);
    display:flex;align-items:center;gap:.75rem;
    transition:transform .18s var(--pd-ease),box-shadow .18s;
}
.pd-stat:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.09);}
.pd-stat-icon{
    width:38px;height:38px;border-radius:.4rem;
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;
}
.si-violet{background:rgba(109,40,217,.1);color:#6d28d9;}
.si-green{background:rgba(5,150,105,.1);color:#059669;}
.si-amber{background:rgba(217,119,6,.1);color:#d97706;}
.si-blue{background:rgba(2,132,199,.1);color:#0284c7;}
.pd-stat-val{font-size:1.3rem;font-weight:900;letter-spacing:-.3px;line-height:1;color:#111827;}
.pd-stat-lbl{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-top:.1rem;}
.pd-stat-sub{font-size:.7rem;color:#d1d5db;}

/* ── Card ── */
.pd-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);box-shadow:var(--pd-sh);overflow:hidden;}
.pd-head{padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.06);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.pd-head h6{font-size:.88rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.35rem;color:#111827;}
.pd-head h6 i{color:var(--pd-indigo);font-size:1rem;}
.pd-count{background:rgba(79,70,229,.08);color:var(--pd-indigo);font-size:.7rem;font-weight:700;padding:.12rem .45rem;border-radius:.2rem;}
.pd-body{padding:1rem 1.1rem;}
.pd-body.np{padding:0;}

/* ── Carrier cards ── */
.pd-carriers{display:grid;grid-template-columns:repeat(auto-fill,minmax(185px,1fr));gap:.6rem;}
.pd-carrier-card{
    background:#fafafa;border:1px solid rgba(0,0,0,.07);border-radius:.45rem;
    padding:.75rem .9rem;transition:border-color .15s,box-shadow .15s;
}
.pd-carrier-card:hover{border-color:rgba(79,70,229,.3);box-shadow:0 0 0 3px rgba(79,70,229,.05);}
.pd-carrier-name{font-size:.88rem;font-weight:800;color:#111827;margin-bottom:.45rem;}
.pd-state-pill{
    display:inline-block;font-size:.62rem;font-weight:700;padding:.08rem .32rem;
    border-radius:.18rem;background:rgba(79,70,229,.08);color:var(--pd-indigo);
    margin:.06rem .04rem 0 0;letter-spacing:.15px;
}
.pd-carrier-meta{font-size:.72rem;color:#9ca3af;margin-top:.35rem;}

.pd-states-footer{
    padding:.55rem 1.1rem;background:#fafafa;
    border-top:1px solid rgba(0,0,0,.06);
    display:flex;flex-wrap:wrap;gap:.25rem;align-items:center;
}

/* ── Tables ── */
.pd-table{width:100%;border-collapse:collapse;font-size:.83rem;}
.pd-table thead th{
    font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;
    color:#9ca3af;border-bottom:1px solid rgba(0,0,0,.08);
    padding:.55rem .85rem;background:#f9fafb;white-space:nowrap;
}
.pd-table tbody td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#374151;}
.pd-table tfoot td{padding:.6rem .85rem;font-weight:700;border-top:2px solid rgba(0,0,0,.08);background:#fafafa;font-size:.82rem;}
.pd-table tbody tr:hover{background:rgba(79,70,229,.022);}
.pd-table tbody tr:last-child td{border-bottom:none;}

/* Type chips */
.tc{font-size:.62rem;font-weight:800;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;letter-spacing:.3px;text-transform:uppercase;}
.tc-sale{background:#d1fae5;color:#065f46;}
.tc-pay{background:#dbeafe;color:#1e3a8a;}
.tc-cb{background:#fef3c7;color:#78350f;}
.tc-other{background:#f3f4f6;color:#374151;}

/* Status chips */
.sc{font-size:.66rem;font-weight:700;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;text-transform:capitalize;}
.sc-ok{background:#d1fae5;color:#065f46;}
.sc-warn{background:#fef9c3;color:#713f12;}
.sc-info{background:#dbeafe;color:#1e3a8a;}
.sc-danger{background:#fee2e2;color:#7f1d1d;}
.sc-def{background:#f3f4f6;color:#374151;}

/* Balance cols */
.col-dr{color:#4f46e5;font-weight:700;}
.col-cr{color:#059669;font-weight:700;}
.col-dim{color:#d1d5db;}
.rb-pos{color:#dc2626;font-weight:700;font-size:.78rem;}
.rb-neg{color:#059669;font-weight:700;font-size:.78rem;}
.rb-zero{color:#9ca3af;font-size:.78rem;}

/* Micro bar in table */
.mbar{height:3px;border-radius:999px;background:#e5e7eb;margin-top:.28rem;overflow:hidden;}
.mbar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--pd-teal),#34d399);max-width:100%;}

/* Empty state */
.pd-empty{text-align:center;padding:2rem 1rem;}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.4rem;opacity:.2;color:#9ca3af;}
.pd-empty p{font-size:.84rem;color:#9ca3af;margin:0;}

/* Dark themes */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table thead th{background:var(--bg-secondary,#16162a);color:var(--text-muted,#888);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody td{color:var(--text-primary,#ddd);border-color:var(--border-color,rgba(255,255,255,.04));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-stat{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-stat-val{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-card{background:var(--bg-tertiary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-name{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-states-footer{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-balance-alert.owe{background:rgba(220,38,38,.1);border-color:rgba(220,38,38,.25);color:#fca5a5;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-balance-alert.credit{background:rgba(5,150,105,.1);border-color:rgba(5,150,105,.25);color:#6ee7b7;}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $earnedRatio = $projectedRevenue > 0 ? min(100, ($earnedRevenue / $projectedRevenue) * 100) : 0;
    $taurusPct   = $partner->our_commission_percentage ?? 15;
?>


<div class="pd-hero pd-anim">
    <div class="pd-hero-body">

        <div class="pd-hero-top">
            <div>
                <div class="pd-hero-name"><?php echo e($partner->name); ?></div>
                <div class="pd-hero-meta">
                    <span class="pd-chip pd-chip-glass"><?php echo e($partner->code); ?></span>
                    <span class="pd-chip pd-chip-glass"><?php echo e(number_format($totalLeads)); ?> leads total</span>
                    <span class="pd-chip pd-chip-green"><span class="pd-pulse"></span> Live</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($chargebacks > 0): ?>
                    <span class="pd-chip pd-chip-amber">CB: $<?php echo e(number_format($chargebacks,0)); ?> shared</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('partner.dashboard')); ?>" class="pd-period-form" id="pd-filter-form">
                <span class="pd-period-label">View by</span>
                <select class="pd-period-input" id="pd-filter-mode" style="width:auto;padding-right:1.6rem;" onchange="toggleFilterMode()">
                    <option value="month" <?php echo e(!request('date_from') ? 'selected' : ''); ?>>Month</option>
                    <option value="range" <?php echo e(request('date_from') ? 'selected' : ''); ?>>Date range</option>
                </select>

                
                <span id="pd-month-wrap" style="<?php echo e(request('date_from') ? 'display:none' : ''); ?>display:flex;align-items:center;gap:.4rem;">
                    <input type="month" name="month" class="pd-period-input" value="<?php echo e($month); ?>">
                </span>

                
                <span id="pd-range-wrap" style="<?php echo e(!request('date_from') ? 'display:none' : ''); ?>display:flex;align-items:center;gap:.35rem;">
                    <input type="date" name="date_from" class="pd-period-input" style="width:130px;" value="<?php echo e(request('date_from')); ?>" placeholder="From">
                    <span style="color:rgba(255,255,255,.35);font-size:.8rem;">→</span>
                    <input type="date" name="date_to" class="pd-period-input" style="width:130px;" value="<?php echo e(request('date_to')); ?>" placeholder="To">
                </span>

                <button type="submit" class="pd-period-btn"><i class="bx bx-filter-alt"></i> Apply</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('date_from') || request('month')): ?>
                <a href="<?php echo e(route('partner.dashboard')); ?>" class="pd-period-btn" style="text-decoration:none;background:rgba(255,100,100,.15);border-color:rgba(255,100,100,.3);"><i class="bx bx-reset"></i></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </form>
        </div>

        <div class="pd-hero-kpis">
            
            <div class="pd-kpi k-green pd-anim pd-d1">
                <i class="bx bx-check-circle pd-kpi-icon"></i>
                <div class="pd-kpi-val">$<span class="cval" data-target="<?php echo e((int)$earnedRevenue); ?>"><?php echo e(number_format($earnedRevenue,0)); ?></span></div>
                <div class="pd-kpi-lbl">Earned Revenue</div>
                <div class="pd-kpi-sub"><?php echo e(number_format($earnedRatio,0)); ?>% of projected collected</div>
                <div class="pd-bar"><div class="pd-bar-fill" style="width:<?php echo e($earnedRatio); ?>%;"></div></div>
            </div>

            
            <div class="pd-kpi k-indigo pd-anim pd-d2">
                <i class="bx bx-wallet pd-kpi-icon"></i>
                <div class="pd-kpi-val">$<span class="cval" data-target="<?php echo e((int)$partnerEarnedShare); ?>"><?php echo e(number_format($partnerEarnedShare,0)); ?></span></div>
                <div class="pd-kpi-lbl">Your Net Share</div>
                <div class="pd-kpi-sub">After <?php echo e($taurusPct); ?>% Taurus fee</div>
                <div class="pd-bar"><div class="pd-bar-fill" style="width:<?php echo e($earnedRatio); ?>%;"></div></div>
            </div>

            
            <div class="pd-kpi k-slate pd-anim pd-d3">
                <i class="bx bx-receipt pd-kpi-icon"></i>
                <div class="pd-kpi-val"
                     style="color:<?php echo e($currentBalance > 0 ? '#fbbf24' : ($currentBalance < 0 ? '#34d399' : '#94a3b8')); ?>;">
                    <?php echo e($currentBalance > 0 ? '+' : ($currentBalance < 0 ? '−' : '')); ?>$<span class="cval" data-target="<?php echo e((int)abs($currentBalance)); ?>"><?php echo e(number_format(abs($currentBalance),0)); ?></span>
                </div>
                <div class="pd-kpi-lbl"><?php echo e($currentBalance > 0 ? 'Ledger — Owed to Taurus' : ($currentBalance < 0 ? 'Ledger — Credit' : 'Ledger Balance')); ?></div>
                <div class="pd-kpi-sub">Excludes chargebacks (shared)</div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($chargebacks > 0): ?>
        <div class="pd-cb-note pd-anim pd-d4">
            <i class="bx bx-info-circle"></i>
            <span>
                <strong>$<?php echo e(number_format($chargebacks,2)); ?></strong> in chargebacks this period —
                these are shared industry losses between Taurus and you. They do <strong>not</strong> count toward your ledger balance.
            </span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentBalance > 0): ?>
<div class="pd-balance-alert owe pd-anim">
    <i class="bx bx-error-circle"></i>
    <span>Your ledger shows <span class="amt">$<?php echo e(number_format($currentBalance,2)); ?></span> owed to Taurus — this is from prior advances or settlements (not chargebacks).</span>
</div>
<?php elseif($currentBalance < 0): ?>
<div class="pd-balance-alert credit pd-anim">
    <i class="bx bx-check-shield"></i>
    <span>You have a credit balance of <span class="amt">$<?php echo e(number_format(abs($currentBalance),2)); ?></span> with Taurus — applied to your next settlement.</span>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="pd-stats pd-anim pd-d2">
    <div class="pd-stat">
        <div class="pd-stat-icon si-violet"><i class="bx bx-file"></i></div>
        <div>
            <div class="pd-stat-val cval" data-target="<?php echo e($monthlyLeads); ?>"><?php echo e($monthlyLeads); ?></div>
            <div class="pd-stat-lbl">Leads this period</div>
            <div class="pd-stat-sub"><?php echo e(number_format($totalLeads)); ?> all-time</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-green"><i class="bx bx-check-shield"></i></div>
        <div>
            <div class="pd-stat-val cval" data-target="<?php echo e($totalSales); ?>"><?php echo e($totalSales); ?></div>
            <div class="pd-stat-lbl">Sales this period</div>
            <div class="pd-stat-sub"><?php echo e($pendingLeads); ?> pending</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-amber"><i class="bx bx-trending-up"></i></div>
        <div>
            <div class="pd-stat-val">$<?php echo e(number_format($partnerProjectedShare,0)); ?></div>
            <div class="pd-stat-lbl">Projected your share</div>
            <div class="pd-stat-sub">After <?php echo e($taurusPct); ?>% deduction</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-blue"><i class="bx bx-buildings"></i></div>
        <div>
            <div class="pd-stat-val"><?php echo e($activeCarriers->count()); ?></div>
            <div class="pd-stat-lbl">Active carriers</div>
            <div class="pd-stat-sub"><?php echo e($authorizedStates->count()); ?> states</div>
        </div>
    </div>
</div>


<div class="row g-3 pd-anim pd-d3 mb-0">

    <div class="col-lg-4">
        <div class="pd-card h-100">
            <div class="pd-head">
                <h6><i class="bx bx-briefcase"></i> Carriers &amp; States</h6>
                <span class="pd-count"><?php echo e($activeCarriers->count()); ?></span>
            </div>
            <div class="pd-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeCarriers->count() > 0): ?>
                <div class="pd-carriers">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="pd-carrier-card">
                        <div class="pd-carrier-name"><?php echo e($c['name']); ?></div>
                        <div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $c['states']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="pd-state-pill"><?php echo e($st); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="pd-carrier-meta"><?php echo e($c['state_count']); ?> <?php echo e($c['state_count'] == 1 ? 'state' : 'states'); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php else: ?>
                <div class="pd-empty"><i class="bx bx-inbox"></i><p>No carriers linked yet</p></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($authorizedStates->count() > 0): ?>
            <div class="pd-states-footer">
                <span style="font-size:.64rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;white-space:nowrap;margin-right:.3rem;">All</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $authorizedStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="pd-state-pill"><?php echo e($st); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="pd-card h-100">
            <div class="pd-head">
                <h6><i class="bx bx-history"></i> Ledger Activity</h6>
                <span style="font-size:.7rem;color:#9ca3af;font-weight:600;">Running balance · excl. chargebacks</span>
            </div>
            <div class="pd-body np" style="overflow-x:auto;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentTransactions->count() > 0): ?>
                <?php $runBal = 0; ?>
                <table class="pd-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Carrier / Note</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentTransactions->take(15); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $runBal += ($txn['debit'] ?? 0) - ($txn['credit'] ?? 0);
                            $tk = strtolower(str_replace([' ','_','-'],'',$txn['type'] ?? ''));
                            $tc = match(true) {
                                str_contains($tk,'sale') && !str_contains($tk,'return') => 'tc-sale',
                                str_contains($tk,'payment') => 'tc-pay',
                                str_contains($tk,'chargeback') || str_contains($tk,'return') => 'tc-cb',
                                default => 'tc-other',
                            };
                        ?>
                        <tr>
                            <td style="white-space:nowrap;color:#6b7280;font-size:.78rem;"><?php echo e(\Carbon\Carbon::parse($txn['date'])->format('M d')); ?></td>
                            <td><span class="tc <?php echo e($tc); ?>"><?php echo e(str_replace('_',' ',$txn['type'] ?? '—')); ?></span></td>
                            <td style="font-size:.8rem;color:#6b7280;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?php echo e($txn['carrier'] ?? ''); ?><?php echo e(($txn['carrier'] && ($txn['description'] ?? $txn['reference'] ?? '')) ? ' · ' : ''); ?><?php echo e(\Illuminate\Support\Str::limit($txn['description'] ?? $txn['reference'] ?? '', 22)); ?>

                            </td>
                            <td class="text-end <?php echo e(($txn['debit']??0)>0?'col-dr':'col-dim'); ?>"><?php echo e(($txn['debit']??0)>0?'$'.number_format($txn['debit'],2):'—'); ?></td>
                            <td class="text-end <?php echo e(($txn['credit']??0)>0?'col-cr':'col-dim'); ?>"><?php echo e(($txn['credit']??0)>0?'$'.number_format($txn['credit'],2):'—'); ?></td>
                            <td class="text-end">
                                <span class="<?php echo e($runBal>0?'rb-pos':($runBal<0?'rb-neg':'rb-zero')); ?>">
                                    <?php echo e($runBal>0?'+':($runBal<0?'−':'')); ?>$<?php echo e(number_format(abs($runBal),2)); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="pd-empty"><i class="bx bx-receipt"></i><p>No ledger entries yet</p></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 pd-anim pd-d4" style="margin-top:1.25rem;">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revenueByCarrier->count() > 0): ?>
    <div class="col-lg-4">
        <div class="pd-card">
            <div class="pd-head">
                <h6><i class="bx bx-bar-chart-alt-2"></i> Revenue by Carrier</h6>
            </div>
            <div class="pd-body np">
                <?php $maxR = $revenueByCarrier->max('partner_share') ?: 1; ?>
                <table class="pd-table">
                    <thead><tr><th>Carrier</th><th class="text-end">Your Share</th><th class="text-end">Sales</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $revenueByCarrier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div style="font-weight:700;font-size:.82rem;"><?php echo e($r['carrier']->name ?? 'Unknown'); ?></div>
                                <div class="mbar"><div class="mbar-fill" style="width:<?php echo e($maxR>0?min(100,($r['partner_share']/$maxR)*100):0); ?>%;"></div></div>
                            </td>
                            <td class="text-end col-cr">$<?php echo e(number_format($r['partner_share'],0)); ?></td>
                            <td class="text-end" style="color:#6b7280;"><?php echo e($r['sales_count']); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="text-end col-cr">$<?php echo e(number_format($revenueByCarrier->sum('partner_share'),0)); ?></td>
                            <td class="text-end"><?php echo e($revenueByCarrier->sum('sales_count')); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="<?php echo e($revenueByCarrier->count() > 0 ? 'col-lg-8' : 'col-lg-12'); ?>">
        <div class="pd-card">
            <div class="pd-head">
                <h6><i class="bx bx-list-ul"></i> Recent Leads &amp; Sales</h6>
                <span class="pd-count"><?php echo e($recentLeads->count()); ?></span>
            </div>
            <div class="pd-body np" style="overflow-x:auto;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentLeads->count() > 0): ?>
                <div style="padding:.4rem .85rem;font-size:.68rem;color:#9ca3af;background:#fafafa;border-bottom:1px solid rgba(0,0,0,.04);">
                    <span style="color:#a78bfa;font-weight:800;">~</span> = estimated (premium &times; 9 &times; carrier%) — finalised once policy is accepted
                </div>
                <table class="pd-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Carrier</th>
                            <th>State</th>
                            <th>Status</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Your Share</th>
                            <th>Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $scCls = match(strtolower($lead->status ?? '')) {
                                'sale','approved','accepted','done' => 'sc-ok',
                                'pending' => 'sc-warn',
                                'issued'  => 'sc-info',
                                'declined','cancelled' => 'sc-danger',
                                default => 'sc-def',
                            };
                            $comm      = (float)($lead->agent_commission ?? 0);
                            $premium   = (float)($lead->monthly_premium ?? 0);
                            $carrierPct = (float)($lead->insuranceCarrier->base_commission_percentage ?? 0);

                            // If agent_commission is not yet set, estimate from premium × 9 × carrier%
                            $isEstimate = false;
                            if ($comm <= 0 && $premium > 0 && $carrierPct > 0) {
                                $comm = $premium * 9 * ($carrierPct / 100);
                                $isEstimate = true;
                            }
                            $hasComm = $comm > 0;
                            $share   = $hasComm ? $comm - ($comm * $taurusPct / 100) : null;
                        ?>
                        <tr>
                            <td>
                                <div style="font-weight:700;"><?php echo e($lead->first_name); ?> <?php echo e($lead->last_name); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($premium > 0): ?><div style="font-size:.7rem;color:#9ca3af;">$<?php echo e(number_format($premium,2)); ?>/mo premium</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="font-size:.8rem;color:#6b7280;"><?php echo e($lead->insuranceCarrier->name ?? '—'); ?></td>
                            <td><span class="pd-state-pill" style="font-size:.7rem;padding:.12rem .4rem;"><?php echo e($lead->state ?? '—'); ?></span></td>
                            <td><span class="sc <?php echo e($scCls); ?>"><?php echo e(ucfirst($lead->status ?? '—')); ?></span></td>
                            <td class="text-end">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasComm): ?>
                                    <span class="<?php echo e($isEstimate ? '' : 'col-dr'); ?>" style="<?php echo e($isEstimate ? 'color:#a78bfa;' : ''); ?>"
                                          <?php if($isEstimate): ?> title="Estimated: premium × 9 × <?php echo e($carrierPct); ?>% carrier rate" <?php endif; ?>>
                                        <?php echo e($isEstimate ? '~' : ''); ?>$<?php echo e(number_format($comm, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span style="color:#d1d5db;font-size:.78rem;">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($share !== null): ?>
                                    <span class="<?php echo e($isEstimate ? '' : 'col-cr'); ?>" style="<?php echo e($isEstimate ? 'color:#818cf8;' : ''); ?>"
                                          <?php if($isEstimate): ?> title="Estimated partner share after <?php echo e($taurusPct); ?>% Taurus fee" <?php endif; ?>>
                                        <?php echo e($isEstimate ? '~' : ''); ?>$<?php echo e(number_format($share, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span style="color:#d1d5db;font-size:.78rem;">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->commission_paid_to_partner): ?>
                                    <span class="tc tc-sale">Paid</span>
                                <?php elseif($hasComm && !$isEstimate): ?>
                                    <span class="tc tc-other">Unpaid</span>
                                <?php else: ?>
                                    <span class="tc" style="background:rgba(167,139,250,.1);color:#a78bfa;font-size:.6rem;">Est.</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="pd-empty"><i class="bx bx-inbox"></i><p>No leads this period</p></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
function toggleFilterMode() {
    var mode = document.getElementById('pd-filter-mode').value;
    var mw = document.getElementById('pd-month-wrap');
    var rw = document.getElementById('pd-range-wrap');
    if (mode === 'range') {
        mw.style.display = 'none';
        rw.style.display = 'flex';
        document.querySelector('[name="month"]').value = '';
    } else {
        mw.style.display = 'flex';
        rw.style.display = 'none';
        document.querySelector('[name="date_from"]').value = '';
        document.querySelector('[name="date_to"]').value = '';
    }
}

(function(){
    'use strict';
    document.querySelectorAll('.cval').forEach(function(el){
        var target = +el.getAttribute('data-target') || 0;
        if(!target){ el.textContent = '0'; return; }
        var s=0, steps=Math.ceil(900/16), inc=target/steps;
        var t=setInterval(function(){
            s+=inc;
            if(s>=target){ el.textContent=target.toLocaleString(); clearInterval(t); }
            else{ el.textContent=Math.floor(s).toLocaleString(); }
        },16);
    });
    setTimeout(function(){
        document.querySelectorAll('.pd-bar-fill,.mbar-fill').forEach(function(b){
            var w=b.style.width; b.style.width='0';
            requestAnimationFrame(function(){
                b.style.transition='width 1s cubic-bezier(.22,1,.36,1)';
                b.style.width=w;
            });
        });
    },250);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.partner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/partner/dashboard-advanced.blade.php ENDPATH**/ ?>