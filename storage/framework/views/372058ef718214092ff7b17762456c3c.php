<?php $__env->startSection('title'); ?> Carriers & States <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --pd-indigo: #4f46e5;
    --pd-green:  #059669;
    --pd-br:     .6rem;
    --pd-sh:     0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
}

/* Page header */
.pc-hdr{margin-bottom:1.25rem;}
.pc-hdr h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
.pc-hdr p{font-size:.84rem;color:#6b7280;margin:0;}

/* Card */
.pd-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);box-shadow:var(--pd-sh);overflow:hidden;margin-bottom:1rem;}
.pd-head{padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.06);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.pd-head h6{font-size:.88rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.35rem;color:#111827;}
.pd-head h6 i{color:var(--pd-indigo);}
.pd-count{background:rgba(79,70,229,.08);color:var(--pd-indigo);font-size:.7rem;font-weight:700;padding:.12rem .45rem;border-radius:.2rem;}
.pd-body{padding:1rem 1.1rem;}

/* Carrier card */
.pc-carrier{
    background:#fafafa;border:1px solid rgba(0,0,0,.07);border-radius:.55rem;
    padding:1.1rem 1.25rem;margin-bottom:.75rem;
    transition:border-color .15s,box-shadow .15s;
}
.pc-carrier:hover{border-color:rgba(79,70,229,.25);box-shadow:0 0 0 3px rgba(79,70,229,.04);}
.pc-carrier:last-child{margin-bottom:0;}

.pc-carrier-hdr{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem;}
.pc-carrier-name{font-size:1rem;font-weight:900;color:#111827;display:flex;align-items:center;gap:.5rem;}
.pc-carrier-name i{color:var(--pd-indigo);font-size:1.1rem;}

/* Commission rate badges */
.pc-rates{display:flex;flex-wrap:wrap;gap:.35rem;}
.pc-rate{
    font-size:.73rem;font-weight:700;padding:.22rem .6rem;border-radius:.3rem;
    display:inline-flex;align-items:center;gap:.25rem;
}
.pc-rate-level{background:rgba(5,150,105,.1);color:#065f46;border:1px solid rgba(5,150,105,.2);}
.pc-rate-graded{background:rgba(79,70,229,.08);color:#3730a3;border:1px solid rgba(79,70,229,.2);}
.pc-rate-gi{background:rgba(217,119,6,.1);color:#78350f;border:1px solid rgba(217,119,6,.2);}
.pc-rate-modified{background:rgba(2,132,199,.1);color:#075985;border:1px solid rgba(2,132,199,.2);}
.pc-rate-lbl{font-size:.62rem;font-weight:600;opacity:.7;text-transform:uppercase;letter-spacing:.2px;}

/* States grid */
.pc-states{display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.65rem;}
.pd-state-pill{
    display:inline-block;font-size:.65rem;font-weight:700;padding:.1rem .35rem;
    border-radius:.18rem;background:rgba(79,70,229,.08);color:var(--pd-indigo);
    letter-spacing:.15px;
}

/* Summary footer */
.pc-all-states-card{
    background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);
    box-shadow:var(--pd-sh);padding:1rem 1.25rem;
}
.pc-all-states-card h6{font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:.55rem;}

/* Empty */
.pd-empty{text-align:center;padding:3rem 1rem;}
.pd-empty i{font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.2;color:#9ca3af;}
.pd-empty p{font-size:.88rem;color:#9ca3af;margin:0;}

/* Dark theme */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card,
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pc-all-states-card{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pc-carrier{background:var(--bg-tertiary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.07));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pc-carrier-name{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pc-hdr h4{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pc-all-states-card h6{color:var(--text-muted,#888);}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="pc-hdr">
    <h4><i class="bx bx-briefcase" style="color:#4f46e5;margin-right:.35rem;"></i>Carriers &amp; States</h4>
    <p>Your authorized insurance carriers and the states you can sell in.</p>
</div>


<?php echo $__env->make('partner.partials.carrier-filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrierStates->isEmpty()): ?>
    <div class="pd-card">
        <div class="pd-empty">
            <i class="bx bx-inbox"></i>
            <p>No carriers linked to your account yet. Contact your manager to get set up.</p>
        </div>
    </div>
<?php else: ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="pd-card">
            <div class="pd-head">
                <h6><i class="bx bx-briefcase"></i> Authorized Carriers</h6>
                <span class="pd-count"><?php echo e($carrierStates->count()); ?></span>
            </div>
            <div class="pd-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carrierStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pc-carrier">
                    <div class="pc-carrier-hdr">
                        <div class="pc-carrier-name">
                            <i class="bx bx-building"></i>
                            <?php echo e($cs['carrier']->name ?? 'Unknown Carrier'); ?>

                        </div>
                        <div class="pc-rates">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cs['settlement_level_pct']): ?>
                            <span class="pc-rate pc-rate-level">
                                <span class="pc-rate-lbl">Level</span> <?php echo e($cs['settlement_level_pct']); ?>%
                            </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cs['settlement_graded_pct']): ?>
                            <span class="pc-rate pc-rate-graded">
                                <span class="pc-rate-lbl">Graded</span> <?php echo e($cs['settlement_graded_pct']); ?>%
                            </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cs['settlement_gi_pct']): ?>
                            <span class="pc-rate pc-rate-gi">
                                <span class="pc-rate-lbl">GI</span> <?php echo e($cs['settlement_gi_pct']); ?>%
                            </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cs['settlement_modified_pct']): ?>
                            <span class="pc-rate pc-rate-modified">
                                <span class="pc-rate-lbl">Modified</span> <?php echo e($cs['settlement_modified_pct']); ?>%
                            </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div style="font-size:.72rem;color:#9ca3af;font-weight:600;margin-bottom:.35rem;">
                        <i class="bx bx-map-pin" style="font-size:.8rem;"></i>
                        <?php echo e($cs['state_count']); ?> <?php echo e($cs['state_count'] == 1 ? 'state' : 'states'); ?> authorized
                    </div>
                    <div class="pc-states">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cs['states']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="pd-state-pill"><?php echo e($st); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        
        <div class="pd-card" style="margin-bottom:.75rem;">
            <div class="pd-head">
                <h6><i class="bx bx-bar-chart-alt-2"></i> Summary</h6>
            </div>
            <div class="pd-body" style="padding:.85rem 1.1rem;">
                <div style="display:flex;flex-direction:column;gap:.6rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .65rem;background:rgba(79,70,229,.05);border-radius:.35rem;border:1px solid rgba(79,70,229,.1);">
                        <span style="font-size:.78rem;font-weight:700;color:#6b7280;">Total Carriers</span>
                        <span style="font-size:1.1rem;font-weight:900;color:#4f46e5;"><?php echo e($activeCarriers->count()); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .65rem;background:rgba(5,150,105,.05);border-radius:.35rem;border:1px solid rgba(5,150,105,.1);">
                        <span style="font-size:.78rem;font-weight:700;color:#6b7280;">Total States</span>
                        <span style="font-size:1.1rem;font-weight:900;color:#059669;"><?php echo e($authorizedStates->count()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($authorizedStates->count() > 0): ?>
        <div class="pc-all-states-card">
            <h6>All Authorized States</h6>
            <div style="display:flex;flex-wrap:wrap;gap:.25rem;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $authorizedStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="pd-state-pill"><?php echo e($st); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.partner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/partner/carriers.blade.php ENDPATH**/ ?>