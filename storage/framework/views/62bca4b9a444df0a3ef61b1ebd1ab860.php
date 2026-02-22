<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title'); ?>
    View Lead
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════════
   Lead Detail — Pill-based Modern CRM Profile
   ═══════════════════════════════════════════════════════ */

/* ── Hero Banner ── */
.ld-hero {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: .75rem;
    overflow: hidden;
    position: relative;
}
.ld-hero::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--bs-gold) 0%, var(--bs-gold-dark) 40%, transparent 100%);
}
.ld-hero-inner {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.25rem;
    flex-wrap: wrap;
}
.ld-avatar {
    width: 52px; height: 52px; min-width: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; font-weight: 800; color: #fff;
    letter-spacing: .5px;
    box-shadow: 0 3px 10px rgba(212,175,55,.25);
    flex-shrink: 0;
}
.ld-identity { flex: 1; min-width: 220px; }
.ld-name {
    font-size: 1.15rem; font-weight: 800; margin: 0 0 .35rem;
    color: var(--bs-surface-800);
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
}
/* pill chips under the name */
.ld-pills {
    display: flex; gap: .35rem; flex-wrap: wrap;
}
.ld-pill {
    font-size: .67rem; font-weight: 600;
    padding: .22rem .6rem;
    border-radius: 50px;
    display: inline-flex; align-items: center; gap: .22rem;
    white-space: nowrap;
    background: rgba(var(--bs-surface-rgb, 128,128,128), .06);
    color: var(--bs-surface-500);
    border: 1px solid rgba(var(--bs-surface-rgb, 128,128,128), .08);
}
.ld-pill i { font-size: .8rem; color: var(--bs-gold); }
.ld-pill-status {
    font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
    font-size: .6rem;
}
.ld-pill-status.st-accepted, .ld-pill-status.st-sale {
    background: rgba(52,195,143,.1); color: #1a8754; border-color: rgba(52,195,143,.2);
}
.ld-pill-status.st-pending {
    background: rgba(241,180,76,.1); color: #b87a14; border-color: rgba(241,180,76,.2);
}
.ld-pill-status.st-rejected, .ld-pill-status.st-declined {
    background: rgba(244,106,106,.1); color: #c84646; border-color: rgba(244,106,106,.2);
}
.ld-pill-status.st-closed, .ld-pill-status.st-transferred {
    background: rgba(80,165,241,.1); color: #2b81c9; border-color: rgba(80,165,241,.2);
}
.ld-pill-status.st-chargeback {
    background: rgba(244,106,106,.1); color: #c84646; border-color: rgba(244,106,106,.2);
}
.ld-pill-status.st-underwritten {
    background: rgba(124,105,239,.1); color: #5b49c7; border-color: rgba(124,105,239,.2);
}
.ld-pill-status.st-forwarded {
    background: rgba(85,110,230,.1); color: #556ee6; border-color: rgba(85,110,230,.2);
}

/* Hero action buttons (pills) */
.ld-hero-actions {
    display: flex; gap: .35rem; flex-wrap: wrap;
    align-items: center; flex-shrink: 0;
}
.ld-abtn {
    font-size: .7rem; font-weight: 600;
    padding: .4rem .85rem; border-radius: 50px;
    border: none; cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: .3rem;
    transition: all .18s ease; white-space: nowrap;
}
.ld-abtn-call {
    background: linear-gradient(135deg, #34c38f, #2ba77a);
    color: #fff; box-shadow: 0 2px 8px rgba(52,195,143,.3);
}
.ld-abtn-call:hover { box-shadow: 0 4px 14px rgba(52,195,143,.4); color: #fff; transform: translateY(-1px); }
.ld-abtn-print {
    background: rgba(85,110,230,.08); color: #556ee6;
    border: 1px solid rgba(85,110,230,.15);
}
.ld-abtn-print:hover { background: rgba(85,110,230,.15); color: #556ee6; }
.ld-abtn-back {
    background: rgba(var(--bs-surface-rgb, 128,128,128),.05); color: var(--bs-surface-500);
    border: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.1);
}
.ld-abtn-back:hover { color: var(--bs-surface-700); background: rgba(var(--bs-surface-rgb, 128,128,128),.1); }

/* ── Pipeline Stepper (horizontal pills) ── */
.ld-pipeline {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: .75rem;
    padding: .65rem 1rem;
}
.ld-pipe-lbl {
    font-size: .58rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .7px;
    color: var(--bs-gold-dark); margin-bottom: .5rem;
    display: flex; align-items: center; gap: .3rem;
}
.ld-pipe-lbl i { font-size: .78rem; opacity: .6; }
.ld-stepper {
    display: flex; gap: .3rem; flex-wrap: wrap;
}
.ld-sp {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .3rem .65rem;
    border-radius: 50px;
    font-size: .62rem; font-weight: 600;
    background: rgba(var(--bs-surface-rgb, 128,128,128),.05);
    color: var(--bs-surface-400);
    border: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.08);
    transition: all .2s;
    white-space: nowrap;
}
.ld-sp i.sp-icon { font-size: .72rem; opacity: .45; }
.ld-sp .sp-check { display: none; }

/* Done step */
.ld-sp.s-done {
    background: rgba(212,175,55,.1);
    color: var(--bs-gold-dark);
    border-color: rgba(212,175,55,.25);
}
.ld-sp.s-done i.sp-icon { color: var(--bs-gold); opacity: .8; }
.ld-sp.s-done .sp-check { display: inline; font-size: .6rem; color: var(--bs-gold); }

/* Current step */
.ld-sp.s-current {
    background: rgba(52,195,143,.1);
    color: #1a8754;
    border-color: rgba(52,195,143,.25);
    animation: spGlow 2.5s ease-in-out infinite;
}
.ld-sp.s-current i.sp-icon { color: #34c38f; opacity: .9; }
.ld-sp.s-current .sp-check { display: inline; font-size: .6rem; color: #34c38f; }

/* Future step */
.ld-sp.s-future {
    opacity: .55;
    font-style: italic;
}

@keyframes spGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(52,195,143,.15); }
    50% { box-shadow: 0 0 0 4px rgba(52,195,143,0); }
}

/* ── Info Cards ── */
.ld-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
    margin-bottom: .6rem;
    overflow: hidden;
    transition: box-shadow .2s;
}
.ld-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.07); }
.ld-card-hdr {
    display: flex; align-items: center; gap: .4rem;
    padding: .5rem .9rem;
    border-bottom: 1px solid rgba(212,175,55,.08);
    background: linear-gradient(90deg, rgba(212,175,55,.04) 0%, transparent 50%);
}
.ld-card-hdr h5 {
    margin: 0; font-size: .76rem; font-weight: 700;
    color: var(--bs-gold-dark);
    display: flex; align-items: center; gap: .35rem;
}
.ld-card-hdr h5 i { font-size: .92rem; opacity: .55; }
.ld-card-body { padding: .6rem .9rem; }

/* ── Field Grid ── */
.ld-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 1rem;
}
.ld-grid.g3 { grid-template-columns: 1fr 1fr 1fr; }
.ld-grid.g1 { grid-template-columns: 1fr; }

.ld-f {
    padding: .4rem 0;
    border-bottom: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.04);
}
.ld-f:last-child { border-bottom: none; }
.ld-f.full { grid-column: 1 / -1; }

.ld-fl {
    display: block;
    font-size: .56rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
    color: var(--bs-surface-400);
    margin-bottom: .12rem;
}
.ld-fv {
    display: block;
    font-size: .8rem; font-weight: 500;
    color: var(--bs-surface-700);
    word-wrap: break-word;
}
.ld-fv.empty, .ld-fv .empty {
    color: var(--bs-surface-muted);
    font-style: italic; font-size: .75rem;
}

/* Pill-shaped badges inside field values */
.ld-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .68rem; font-weight: 600;
    padding: .18rem .55rem;
    border-radius: 50px;
}
.ld-badge-green { background: rgba(52,195,143,.1); color: #1a8754; }
.ld-badge-blue  { background: rgba(80,165,241,.1); color: #2b81c9; }
.ld-badge-gold  { background: rgba(212,175,55,.1); color: #b8972e; }
.ld-badge-warn  { background: rgba(241,180,76,.1); color: #b87a14; }
.ld-badge-red   { background: rgba(244,106,106,.1); color: #c84646; }
.ld-badge-purple { background: rgba(124,105,239,.1); color: #5b49c7; }
.ld-badge-muted { background: rgba(var(--bs-surface-rgb, 128,128,128),.06); color: var(--bs-surface-500); }

/* Dividers & sub-headers */
.ld-sep {
    grid-column: 1 / -1;
    border-top: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.07);
    margin: .3rem 0;
}
.ld-sub {
    grid-column: 1 / -1;
    font-size: .64rem; font-weight: 700;
    color: var(--bs-surface-500);
    padding: .2rem 0 .05rem;
    display: flex; align-items: center; gap: .25rem;
}
.ld-sub i { font-size: .78rem; opacity: .5; }

/* ── Two-column layout ── */
.ld-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 .7rem;
    align-items: start;
}

/* ── Responsive ── */
@media (max-width: 991px) {
    .ld-cols { grid-template-columns: 1fr; }
    .ld-stepper { gap: .25rem; }
    .ld-grid.g3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 575px) {
    .ld-hero-inner { flex-direction: column; align-items: flex-start; }
    .ld-grid { grid-template-columns: 1fr; }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.82rem;">
            <i class="mdi mdi-check-all me-1"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php
        $steps = [
            ['key'=>'sale',      'label'=>'Sale Made',            'icon'=>'mdi-handshake'],
            ['key'=>'submit',    'label'=>'Submitted',            'icon'=>'mdi-file-upload'],
            ['key'=>'issuance',  'label'=>'Policy Issuance',      'icon'=>'mdi-file-document-check'],
            ['key'=>'followup',  'label'=>'Client Follow-up',     'icon'=>'mdi-phone-in-talk'],
            ['key'=>'banking',   'label'=>'Banking Verified',     'icon'=>'mdi-bank-check'],
            ['key'=>'draft',     'label'=>'Draft Confirmation',   'icon'=>'mdi-check-circle',   'future'=>true],
            ['key'=>'commission','label'=>'Commission',           'icon'=>'mdi-currency-usd',   'future'=>true],
            ['key'=>'paid',      'label'=>'Paid',                 'icon'=>'mdi-cash-check',     'future'=>true],
            ['key'=>'recovery',  'label'=>'Advance Recovery',     'icon'=>'mdi-refresh',        'future'=>true],
        ];

        $done = [];
        $isSale = in_array($insurance->status, ['sale','accepted']);
        if ($isSale) { $done[] = 'sale'; }
        if ($insurance->status === 'underwritten' || $isSale) {
            $done[] = 'sale'; $done[] = 'submit';
        }
        $hasIssuance = ($insurance->policy_number || $insurance->issued_policy_number) && ($insurance->partner_id || $insurance->assigned_partner);
        if ($hasIssuance) {
            $done[] = 'sale'; $done[] = 'submit'; $done[] = 'issuance';
        }
        $hasFollowup = in_array($insurance->followup_status, ['Yes','No','Completed','yes','no','completed']) && ($insurance->assigned_followup_person || $insurance->followup_assigned_by);
        if ($hasFollowup) {
            $done[] = 'sale'; $done[] = 'submit'; $done[] = 'issuance'; $done[] = 'followup';
        }
        $hasBV = in_array(strtolower($insurance->bank_verification_status ?? ''), ['bv verified','verified']);
        if ($hasBV) {
            $done[] = 'sale'; $done[] = 'submit'; $done[] = 'issuance'; $done[] = 'followup'; $done[] = 'banking';
        }
        $done = array_unique($done);
        $currentStep = null;
        foreach ($steps as $s) {
            if (!in_array($s['key'], $done)) { $currentStep = $s['key']; break; }
        }
    ?>

    
    <div class="ld-hero">
        <div class="ld-hero-inner">
            
            <div class="ld-avatar">
                <?php echo e(strtoupper(substr($insurance->cn_name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr(strstr($insurance->cn_name ?? '', ' ') ?: '', 1, 1))); ?>

            </div>
            
            <div class="ld-identity">
                <h1 class="ld-name">
                    <?php echo e($insurance->cn_name ?? 'Unnamed Lead'); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->status): ?>
                        <span class="ld-pill ld-pill-status st-<?php echo e(strtolower($insurance->status)); ?>"><?php echo e(ucfirst($insurance->status)); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h1>
                <div class="ld-pills">
                    <span class="ld-pill"><i class="mdi mdi-phone"></i> <?php echo e($insurance->phone_number ?? 'No phone'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->secondary_phone_number): ?>
                        <span class="ld-pill"><i class="mdi mdi-phone-plus"></i> <?php echo e($insurance->secondary_phone_number); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="ld-pill"><i class="mdi mdi-map-marker"></i> <?php echo e($insurance->state ?? 'N/A'); ?> <?php echo e($insurance->zip_code ?? ''); ?></span>
                    <span class="ld-pill"><i class="mdi mdi-clock-outline"></i> <?php echo e($insurance->created_at ? $insurance->created_at->format('M d, Y') : 'N/A'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->team): ?>
                        <span class="ld-pill"><i class="mdi mdi-account-group"></i> <?php echo e($insurance->team); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            
            <div class="ld-hero-actions">
                <button onclick="makeZoomCall()" class="ld-abtn ld-abtn-call"><i class="mdi mdi-phone"></i> Call Now</button>
                <a href="<?php echo e(route('sales.prettyPrint', $insurance->id)); ?>" class="ld-abtn ld-abtn-print" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
                <a href="<?php echo e(route('leads.index')); ?>" class="ld-abtn ld-abtn-back"><i class="mdi mdi-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>

    
    <div class="ld-pipeline">
        <div class="ld-pipe-lbl"><i class="mdi mdi-timeline-check"></i> Live & Health Pipeline</div>
        <div class="ld-stepper">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isDone = in_array($step['key'], $done);
                    $isCurr = $step['key'] === $currentStep;
                    $isFuture = !empty($step['future']);
                    $cls = $isDone ? 's-done' : ($isCurr ? 's-current' : ($isFuture ? 's-future' : ''));
                ?>
                <span class="ld-sp <?php echo e($cls); ?>">
                    <span class="sp-check">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDone): ?> <i class="mdi mdi-check-bold"></i>
                        <?php elseif($isCurr): ?> <i class="mdi mdi-dots-horizontal"></i>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <i class="mdi <?php echo e($step['icon']); ?> sp-icon"></i>
                    <?php echo e($step['label']); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="ld-cols">
        
        <div>
            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account"></i> Personal Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Full Name</span>
                            <span class="ld-fv <?php echo e($insurance->cn_name ? '' : 'empty'); ?>"><?php echo e($insurance->cn_name ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Date of Birth</span>
                            <span class="ld-fv <?php echo e($insurance->date_of_birth ? '' : 'empty'); ?>"><?php echo e($insurance->date_of_birth ? \Carbon\Carbon::parse($insurance->date_of_birth)->format('M d, Y') : 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Age</span>
                            <span class="ld-fv <?php echo e($insurance->age ? '' : 'empty'); ?>"><?php echo e($insurance->age ?? 'N/A'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Gender</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->gender): ?>
                                    <span class="ld-badge ld-badge-blue"><?php echo e($insurance->gender); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Birth Place</span>
                            <span class="ld-fv <?php echo e($insurance->birth_place ? '' : 'empty'); ?>"><?php echo e($insurance->birth_place ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">SSN</span>
                            <span class="ld-fv <?php echo e($insurance->ssn ? '' : 'empty'); ?>"><?php echo e($insurance->ssn ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-phone-in-talk"></i> Contact Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Primary Phone</span>
                            <span class="ld-fv <?php echo e($insurance->phone_number ? '' : 'empty'); ?>"><?php echo e($insurance->phone_number ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Secondary Phone</span>
                            <span class="ld-fv <?php echo e($insurance->secondary_phone_number ? '' : 'empty'); ?>"><?php echo e($insurance->secondary_phone_number ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Address</span>
                            <span class="ld-fv <?php echo e($insurance->address ? '' : 'empty'); ?>"><?php echo e($insurance->address ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">State</span>
                            <span class="ld-fv <?php echo e($insurance->state ? '' : 'empty'); ?>"><?php echo e($insurance->state ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Zip Code</span>
                            <span class="ld-fv <?php echo e($insurance->zip_code ? '' : 'empty'); ?>"><?php echo e($insurance->zip_code ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Emergency Contact</span>
                            <span class="ld-fv <?php echo e($insurance->emergency_contact ? '' : 'empty'); ?>"><?php echo e($insurance->emergency_contact ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-heart-pulse"></i> Health Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Nicotine User</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->smoker !== null): ?>
                                    <span class="ld-badge <?php echo e($insurance->smoker ? 'ld-badge-warn' : 'ld-badge-green'); ?>"><?php echo e($insurance->smoker ? 'Yes' : 'No'); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Height</span>
                            <span class="ld-fv <?php echo e($insurance->height ? '' : 'empty'); ?>"><?php echo e($insurance->height ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Weight</span>
                            <span class="ld-fv <?php echo e($insurance->weight ? '' : 'empty'); ?>"><?php echo e($insurance->weight ? $insurance->weight . ' lbs' : 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Driving License</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->driving_license !== null): ?>
                                    <span class="ld-badge ld-badge-blue"><?php echo e($insurance->driving_license ? 'Yes' : 'No'); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->driving_license_number): ?>
                        <div class="ld-f full">
                            <span class="ld-fl">DL Number</span>
                            <span class="ld-fv"><?php echo e($insurance->driving_license_number); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="ld-f full">
                            <span class="ld-fl">Medical Issues</span>
                            <span class="ld-fv <?php echo e($insurance->medical_issue ? '' : 'empty'); ?>"><?php echo e($insurance->medical_issue ?? 'None reported'); ?></span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Medications</span>
                            <span class="ld-fv <?php echo e($insurance->medications ? '' : 'empty'); ?>"><?php echo e($insurance->medications ?? 'None reported'); ?></span>
                        </div>
                        <div class="ld-sep"></div>
                        <div class="ld-sub"><i class="mdi mdi-doctor"></i> Primary Care Physician</div>
                        <div class="ld-f full">
                            <span class="ld-fl">Doctor Name</span>
                            <span class="ld-fv <?php echo e($insurance->doctor_name ? '' : 'empty'); ?>"><?php echo e($insurance->doctor_name ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Doctor Phone</span>
                            <span class="ld-fv <?php echo e($insurance->doctor_number ? '' : 'empty'); ?>"><?php echo e($insurance->doctor_number ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Doctor Address</span>
                            <span class="ld-fv <?php echo e($insurance->doctor_address ? '' : 'empty'); ?>"><?php echo e($insurance->doctor_address ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-shield-check"></i> Policy Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Plan Type</span>
                            <span class="ld-fv <?php echo e($insurance->policy_type ? '' : 'empty'); ?>"><?php echo e($insurance->policy_type ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Policy Number</span>
                            <span class="ld-fv <?php echo e($insurance->policy_number ? '' : 'empty'); ?>"><?php echo e($insurance->policy_number ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Carrier Name</span>
                            <span class="ld-fv <?php echo e(($insurance->insuranceCarrier || $insurance->carrier_name) ? '' : 'empty'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->insuranceCarrier): ?>
                                    <?php echo e($insurance->insuranceCarrier->name); ?>

                                <?php else: ?>
                                    <?php echo e($insurance->carrier_name ?? 'Not provided'); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Coverage Amount</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->coverage_amount): ?>
                                    <span class="ld-badge ld-badge-blue">$<?php echo e(number_format($insurance->coverage_amount, 0)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Monthly Premium</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->monthly_premium): ?>
                                    <span class="ld-badge ld-badge-green">$<?php echo e(number_format($insurance->monthly_premium, 2)); ?>/mo</span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Initial Draft Date</span>
                            <span class="ld-fv <?php echo e($insurance->initial_draft_date ? '' : 'empty'); ?>"><?php echo e($insurance->initial_draft_date ? \Carbon\Carbon::parse($insurance->initial_draft_date)->format('M d, Y') : 'Not set'); ?></span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Future Draft Date</span>
                            <span class="ld-fv <?php echo e($insurance->future_draft_date ? '' : 'empty'); ?>"><?php echo e($insurance->future_draft_date ? \Carbon\Carbon::parse($insurance->future_draft_date)->format('M d, Y') : 'Not set'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account-heart"></i> Beneficiary Information</h5></div>
                <div class="ld-card-body">
                    <?php
                        $beneficiaries = $insurance->beneficiaries ?? [];
                        if (is_string($beneficiaries)) {
                            $decoded = json_decode($beneficiaries, true);
                            $beneficiaries = is_array($decoded) ? $decoded : [];
                        }
                        if (!is_array($beneficiaries)) { $beneficiaries = []; }
                        if (empty($beneficiaries) && ($insurance->beneficiary || $insurance->beneficiary_dob)) {
                            $beneficiaries = [[
                                'name' => $insurance->beneficiary ?? '',
                                'dob' => $insurance->beneficiary_dob ?? '',
                                'relation' => ''
                            ]];
                        }
                    ?>
                    <?php if(!empty($beneficiaries)): ?>
                        <?php $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($beneficiaries) > 1): ?>
                                <div style="font-size:.68rem; font-weight:700; color:var(--bs-gold-dark); margin-bottom:.25rem;">Beneficiary <?php echo e($index + 1); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="ld-grid g3" style="<?php echo e(!$loop->last ? 'margin-bottom:.5rem; padding-bottom:.5rem; border-bottom:1px solid rgba(var(--bs-surface-rgb,128,128,128),.06);' : ''); ?>">
                                <div class="ld-f">
                                    <span class="ld-fl">Name</span>
                                    <span class="ld-fv <?php echo e(!empty($beneficiary['name']) ? '' : 'empty'); ?>"><?php echo e($beneficiary['name'] ?? 'Not provided'); ?></span>
                                </div>
                                <div class="ld-f">
                                    <span class="ld-fl">Relation</span>
                                    <span class="ld-fv <?php echo e(!empty($beneficiary['relation']) ? '' : 'empty'); ?>"><?php echo e($beneficiary['relation'] ?? 'Not provided'); ?></span>
                                </div>
                                <div class="ld-f">
                                    <span class="ld-fl">Date of Birth</span>
                                    <span class="ld-fv <?php echo e(!empty($beneficiary['dob']) ? '' : 'empty'); ?>"><?php echo e(!empty($beneficiary['dob']) ? \Carbon\Carbon::parse($beneficiary['dob'])->format('M d, Y') : 'Not provided'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <span class="ld-fv empty">No beneficiaries added</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div>
            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-clipboard-check"></i> Status & Assignment</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Lead Status</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->status): ?>
                                    <span class="ld-pill ld-pill-status st-<?php echo e(strtolower($insurance->status)); ?>"><?php echo e(ucfirst($insurance->status)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Team</span>
                            <span class="ld-fv <?php echo e($insurance->team ? '' : 'empty'); ?>"><?php echo e($insurance->team ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Closer Name</span>
                            <span class="ld-fv <?php echo e($insurance->closer_name ? '' : 'empty'); ?>"><?php echo e($insurance->closer_name ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Managed By</span>
                            <span class="ld-fv <?php echo e($insurance->managedBy ? '' : 'empty'); ?>"><?php echo e($insurance->managedBy->name ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned Partner</span>
                            <span class="ld-fv <?php echo e($insurance->partner ? '' : 'empty'); ?>"><?php echo e($insurance->partner->name ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned Validator</span>
                            <span class="ld-fv <?php echo e($insurance->assignedValidator ? '' : 'empty'); ?>"><?php echo e($insurance->assignedValidator->name ?? 'Not assigned'); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->sale_date || $insurance->sale_at): ?>
                        <div class="ld-f full">
                            <span class="ld-fl">Sale Date</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->sale_date): ?>
                                    <?php echo e(\Carbon\Carbon::parse($insurance->sale_date)->format('M d, Y')); ?>

                                <?php elseif($insurance->sale_at): ?>
                                    <?php echo e(\Carbon\Carbon::parse($insurance->sale_at)->format('M d, Y')); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->decline_reason || $insurance->pending_reason): ?>
                        <div class="ld-f full">
                            <span class="ld-fl">Status Reason</span>
                            <span class="ld-fv"><?php echo e($insurance->decline_reason ?? $insurance->pending_reason ?? 'N/A'); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ld-sep"></div>
                    <div class="ld-grid g3">
                        <div class="ld-f">
                            <span class="ld-fl">Lead Source</span>
                            <span class="ld-fv <?php echo e($insurance->source ? '' : 'empty'); ?>"><?php echo e($insurance->source ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Preset Line</span>
                            <span class="ld-fv <?php echo e($insurance->preset_line ? '' : 'empty'); ?>"><?php echo e($insurance->preset_line ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Comments</span>
                            <span class="ld-fv <?php echo e($insurance->comments ? '' : 'empty'); ?>"><?php echo e($insurance->comments ?? 'None'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-bank"></i> Bank Account</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Bank Name</span>
                            <span class="ld-fv <?php echo e($insurance->bank_name ? '' : 'empty'); ?>"><?php echo e($insurance->bank_name ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Account Type</span>
                            <span class="ld-fv <?php echo e($insurance->account_type ? '' : 'empty'); ?>"><?php echo e($insurance->account_type ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Account Title</span>
                            <span class="ld-fv <?php echo e($insurance->account_title ? '' : 'empty'); ?>"><?php echo e($insurance->account_title ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Bank Balance</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_balance): ?>
                                    <span class="ld-badge ld-badge-blue">$<?php echo e(number_format($insurance->bank_balance, 2)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Routing Number</span>
                            <span class="ld-fv <?php echo e($insurance->routing_number ? '' : 'empty'); ?>"><?php echo e($insurance->routing_number ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Account Number</span>
                            <span class="ld-fv <?php echo e($insurance->acc_number ? '' : 'empty'); ?>"><?php echo e($insurance->acc_number ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->account_verified_by): ?>
                    <div class="ld-sep"></div>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Verified By</span>
                            <span class="ld-fv"><?php echo e($insurance->account_verified_by); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->ss_amount || $insurance->ss_date): ?>
                    <div class="ld-sep"></div>
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">SS Amount</span>
                            <span class="ld-fv <?php echo e($insurance->ss_amount ? '' : 'empty'); ?>"><?php echo e($insurance->ss_amount ? '$' . number_format($insurance->ss_amount, 2) : 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">SS Date</span>
                            <span class="ld-fv <?php echo e($insurance->ss_date ? '' : 'empty'); ?>"><?php echo e($insurance->ss_date ? \Carbon\Carbon::parse($insurance->ss_date)->format('M d, Y') : 'Not provided'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_verification_status || $insurance->bank_verification_notes): ?>
                    <div class="ld-sep"></div>
                    <div class="ld-sub"><i class="mdi mdi-check-decagram"></i> Bank Verification</div>
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_verification_status): ?>
                                    <span class="ld-badge <?php echo e(in_array(strtolower($insurance->bank_verification_status), ['verified','bv verified']) ? 'ld-badge-green' : 'ld-badge-warn'); ?>"><?php echo e(ucfirst($insurance->bank_verification_status)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Pending</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Verified By</span>
                            <span class="ld-fv <?php echo e($insurance->bankVerifier ? '' : 'empty'); ?>"><?php echo e($insurance->bankVerifier->name ?? 'Not assigned'); ?></span>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_verification_notes): ?>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv"><?php echo e($insurance->bank_verification_notes); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->card_number || $insurance->cvv || $insurance->expiry_date): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-credit-card"></i> Card Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid g3">
                        <div class="ld-f">
                            <span class="ld-fl">Card Number</span>
                            <span class="ld-fv <?php echo e($insurance->card_number ? '' : 'empty'); ?>"><?php echo e($insurance->card_number ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">CVV</span>
                            <span class="ld-fv <?php echo e($insurance->cvv ? '' : 'empty'); ?>"><?php echo e($insurance->cvv ?? 'Not provided'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Expiry</span>
                            <span class="ld-fv <?php echo e($insurance->expiry_date ? '' : 'empty'); ?>"><?php echo e($insurance->expiry_date ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->followup_required || $insurance->followup_scheduled_at): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-calendar-clock"></i> Follow-Up Schedule</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Required</span>
                            <span class="ld-fv">
                                <span class="ld-badge <?php echo e($insurance->followup_required ? 'ld-badge-warn' : 'ld-badge-green'); ?>"><?php echo e($insurance->followup_required ? 'Yes' : 'No'); ?></span>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned To</span>
                            <span class="ld-fv <?php echo e($insurance->followupPerson ? '' : 'empty'); ?>"><?php echo e($insurance->followupPerson->name ?? 'Not assigned'); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->followup_scheduled_at): ?>
                        <div class="ld-f">
                            <span class="ld-fl">Scheduled</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->followup_scheduled_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv <?php echo e($insurance->followup_status ? '' : 'empty'); ?>"><?php echo e($insurance->followup_status ?? 'Pending'); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-check-decagram"></i> QA Review</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">QA Status</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->qa_status): ?>
                                    <span class="ld-badge <?php echo e($insurance->qa_status == 'Approved' ? 'ld-badge-green' : ($insurance->qa_status == 'Rejected' ? 'ld-badge-red' : 'ld-badge-warn')); ?>"><?php echo e($insurance->qa_status); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not reviewed</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Reviewed By</span>
                            <span class="ld-fv <?php echo e($insurance->qaUser ? '' : 'empty'); ?>"><?php echo e($insurance->qaUser->name ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">QA Notes</span>
                            <span class="ld-fv <?php echo e($insurance->qa_reason ? '' : 'empty'); ?>"><?php echo e($insurance->qa_reason ?? 'No notes'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retention_status || $insurance->retention_notes): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account-reactivate"></i> Retention</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retention_status): ?>
                                    <span class="ld-badge ld-badge-blue"><?php echo e($insurance->retention_status); ?></span>
                                <?php else: ?>
                                    <span class="empty">N/A</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Officer</span>
                            <span class="ld-fv <?php echo e($insurance->retentionOfficer ? '' : 'empty'); ?>"><?php echo e($insurance->retentionOfficer->name ?? 'Not assigned'); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retained_at): ?>
                        <div class="ld-f">
                            <span class="ld-fl">Retained Date</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->retained_at)->format('M d, Y')); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Is Rewrite</span>
                            <span class="ld-fv"><span class="ld-badge <?php echo e($insurance->is_rewrite ? 'ld-badge-warn' : 'ld-badge-muted'); ?>"><?php echo e($insurance->is_rewrite ? 'Yes' : 'No'); ?></span></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retention_notes): ?>
                        <div class="ld-f full">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv"><?php echo e($insurance->retention_notes); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->chargeback_marked_date): ?>
                        <div class="ld-f full">
                            <span class="ld-fl">Chargeback Date</span>
                            <span class="ld-fv" style="color:#c84646;"><?php echo e(\Carbon\Carbon::parse($insurance->chargeback_marked_date)->format('M d, Y h:i A')); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issuance_status || $insurance->assigned_agent_id): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-file-document-check"></i> Issuance</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issuance_status): ?>
                                    <span class="ld-badge <?php echo e($insurance->issuance_status == 'issued' ? 'ld-badge-green' : 'ld-badge-warn'); ?>"><?php echo e(ucfirst($insurance->issuance_status)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned Agent</span>
                            <span class="ld-fv <?php echo e($insurance->assignedAgent ? '' : 'empty'); ?>"><?php echo e($insurance->assignedAgent->name ?? 'Not assigned'); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issued_policy_number): ?>
                        <div class="ld-f">
                            <span class="ld-fl">Issued Policy #</span>
                            <span class="ld-fv"><?php echo e($insurance->issued_policy_number); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Issuance Date</span>
                            <span class="ld-fv <?php echo e($insurance->issuance_date ? '' : 'empty'); ?>"><?php echo e($insurance->issuance_date ? \Carbon\Carbon::parse($insurance->issuance_date)->format('M d, Y') : 'Not set'); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issuance_reason): ?>
                        <div class="ld-f full">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv"><?php echo e($insurance->issuance_reason); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->agent_commission || $insurance->agent_revenue || $insurance->settlement_percentage): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-currency-usd"></i> Revenue & Commission</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid g3">
                        <div class="ld-f">
                            <span class="ld-fl">Commission</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->agent_commission): ?>
                                    <span class="ld-badge ld-badge-green">$<?php echo e(number_format($insurance->agent_commission, 2)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Revenue</span>
                            <span class="ld-fv">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->agent_revenue): ?>
                                    <span class="ld-badge ld-badge-blue">$<?php echo e(number_format($insurance->agent_revenue, 2)); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Settlement %</span>
                            <span class="ld-fv <?php echo e($insurance->settlement_percentage ? '' : 'empty'); ?>"><?php echo e($insurance->settlement_percentage ? $insurance->settlement_percentage . '%' : 'Not set'); ?></span>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->commission_calculation_notes): ?>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv"><?php echo e($insurance->commission_calculation_notes); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->commission_calculated_at): ?>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Calculated At</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->commission_calculated_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->staff_notes || $insurance->manager_notes): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-note-text"></i> Notes</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid g1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->staff_notes): ?>
                        <div class="ld-f">
                            <span class="ld-fl">Staff Notes</span>
                            <span class="ld-fv"><?php echo e($insurance->staff_notes); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->manager_notes): ?>
                        <div class="ld-f">
                            <span class="ld-fl">Manager Notes</span>
                            <span class="ld-fv"><?php echo e($insurance->manager_notes); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-timeline-clock"></i> Timeline</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Created</span>
                            <span class="ld-fv"><?php echo e($insurance->created_at ? $insurance->created_at->format('M d, Y h:i A') : 'N/A'); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Last Updated</span>
                            <span class="ld-fv"><?php echo e($insurance->updated_at ? $insurance->updated_at->format('M d, Y h:i A') : 'N/A'); ?></span>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->verified_at): ?>
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Verified At</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->verified_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Verified By</span>
                            <span class="ld-fv <?php echo e($insurance->verifier ? '' : 'empty'); ?>"><?php echo e($insurance->verifier->name ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->validated_at): ?>
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Validated At</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->validated_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Validated By</span>
                            <span class="ld-fv <?php echo e($insurance->validator ? '' : 'empty'); ?>"><?php echo e($insurance->validator->name ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->transferred_at): ?>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Transferred At</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->transferred_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->closed_at): ?>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Closed At</span>
                            <span class="ld-fv"><?php echo e(\Carbon\Carbon::parse($insurance->closed_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->declined_at): ?>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Declined At</span>
                            <span class="ld-fv" style="color:#c84646;"><?php echo e(\Carbon\Carbon::parse($insurance->declined_at)->format('M d, Y h:i A')); ?></span>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function makeZoomCall() {
            const phoneNumber = '<?php echo e($insurance->phone_number ?? ''); ?>';
            const sanitizedZoomNumber = '<?php echo e(Auth::user()->sanitized_zoom_number ?? ''); ?>';

            if (!phoneNumber) {
                alert('No phone number available for this lead.');
                return;
            }
            if (!sanitizedZoomNumber) {
                alert('You do not have a Zoom phone number configured.');
                return;
            }

            const cleanNumber = phoneNumber.replace(/[\s\-\(\)]/g, '');
            const zoomUrl = `zoomphonenumber://call?to=${cleanNumber}`;
            window.location.href = zoomUrl;
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/leads/show.blade.php ENDPATH**/ ?>