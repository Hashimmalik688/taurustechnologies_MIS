<?php use \App\Support\Roles; ?>


<?php $__env->startSection('title'); ?>
    Edit Lead
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════════
   Lead Edit — matches Lead Detail modern style
   ═══════════════════════════════════════════════════════ */
.ld-hero{background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.07);border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.06);margin-bottom:.75rem;overflow:hidden;position:relative;}
.ld-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--bs-gold) 0%,var(--bs-gold-dark) 40%,transparent 100%);}
.ld-hero-inner{display:flex;align-items:center;gap:1rem;padding:1rem 1.25rem;flex-wrap:wrap;}
.ld-avatar{width:52px;height:52px;min-width:52px;border-radius:50%;background:linear-gradient(135deg,var(--bs-gold) 0%,var(--bs-gold-dark) 100%);display:flex;align-items:center;justify-content:center;font-size:1.05rem;font-weight:800;color:#fff;letter-spacing:.5px;box-shadow:0 3px 10px rgba(212,175,55,.25);flex-shrink:0;}
.ld-identity{flex:1;min-width:220px;}
.ld-name{font-size:1.15rem;font-weight:800;margin:0 0 .35rem;color:var(--bs-surface-800);display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;}
.ld-pills{display:flex;gap:.35rem;flex-wrap:wrap;}
.ld-pill{font-size:.67rem;font-weight:600;padding:.22rem .6rem;border-radius:50px;display:inline-flex;align-items:center;gap:.22rem;white-space:nowrap;background:rgba(var(--bs-surface-rgb,128,128,128),.06);color:var(--bs-surface-500);border:1px solid rgba(var(--bs-surface-rgb,128,128,128),.08);}
.ld-pill i{font-size:.8rem;color:var(--bs-gold);}
.ld-pill-status{font-weight:700;text-transform:uppercase;letter-spacing:.3px;font-size:.6rem;}
.ld-pill-status.st-active,.ld-pill-status.st-accepted,.ld-pill-status.st-approved{background:rgba(52,195,143,.1);color:#1a8754;border-color:rgba(52,195,143,.2);}
.ld-pill-status.st-pending{background:rgba(241,180,76,.1);color:#b87a14;border-color:rgba(241,180,76,.2);}
.ld-pill-status.st-rejected,.ld-pill-status.st-declined,.ld-pill-status.st-cancelled{background:rgba(244,106,106,.1);color:#c84646;border-color:rgba(244,106,106,.2);}
.ld-pill-status.st-forwarded{background:rgba(85,110,230,.1);color:#556ee6;border-color:rgba(85,110,230,.2);}
.ld-pill-status.st-underwriting{background:rgba(124,105,239,.1);color:#5b49c7;border-color:rgba(124,105,239,.2);}
.ld-pill-status.st-completed{background:rgba(80,165,241,.1);color:#2b81c9;border-color:rgba(80,165,241,.2);}
.ld-pill-sale{background:rgba(52,195,143,.1);color:#1a8754;border-color:rgba(52,195,143,.2);}
.ld-hero-actions{display:flex;gap:.35rem;flex-wrap:wrap;align-items:center;flex-shrink:0;}
.ld-abtn{font-size:.7rem;font-weight:600;padding:.4rem .85rem;border-radius:50px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;transition:all .18s ease;white-space:nowrap;}
.ld-abtn-primary{background:linear-gradient(135deg,var(--bs-gold) 0%,var(--bs-gold-dark) 100%);color:#fff;box-shadow:0 2px 8px rgba(212,175,55,.3);}
.ld-abtn-primary:hover{box-shadow:0 4px 14px rgba(212,175,55,.4);color:#fff;transform:translateY(-1px);}
.ld-abtn-outline{background:rgba(var(--bs-surface-rgb,128,128,128),.05);color:var(--bs-surface-500);border:1px solid rgba(var(--bs-surface-rgb,128,128,128),.1);}
.ld-abtn-outline:hover{color:var(--bs-surface-700);background:rgba(var(--bs-surface-rgb,128,128,128),.1);}
.ld-abtn-danger{background:rgba(244,106,106,.08);color:#c84646;border:1px solid rgba(244,106,106,.15);}
.ld-card{background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.07);border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.04);margin-bottom:.6rem;overflow:hidden;transition:box-shadow .2s;}
.ld-card-hdr{display:flex;align-items:center;gap:.4rem;padding:.5rem .9rem;border-bottom:1px solid rgba(212,175,55,.08);background:linear-gradient(90deg,rgba(212,175,55,.04) 0%,transparent 50%);}
.ld-card-hdr h5{margin:0;font-size:.76rem;font-weight:700;color:var(--bs-gold-dark);display:flex;align-items:center;gap:.35rem;}
.ld-card-hdr h5 i{font-size:.92rem;opacity:.55;}
.ld-card-body{padding:.6rem .9rem;}
.ld-card.sale-card .ld-card-hdr{background:linear-gradient(90deg,rgba(52,195,143,.06) 0%,transparent 60%);border-bottom-color:rgba(52,195,143,.12);}
.ld-card.sale-card .ld-card-hdr h5{color:#1a8754;}
.ld-card.sale-card .ld-card-hdr h5 i{color:#34c38f;opacity:.9;}
.sale-notice{background:rgba(52,195,143,.06);border:1px solid rgba(52,195,143,.14);border-radius:8px;padding:.55rem .75rem;margin-bottom:.8rem;font-size:.73rem;color:#1a8754;line-height:1.55;}
.sale-notice strong{display:block;margin-bottom:.2rem;font-size:.76rem;}
.ef-grid{display:grid;grid-template-columns:1fr 1fr;gap:.35rem .9rem;}
.ef-grid.g3{grid-template-columns:1fr 1fr 1fr;}
.ef-grid.g1{grid-template-columns:1fr;}
.ef-f{padding:.25rem 0;}
.ef-f.full{grid-column:1/-1;}
.ef-sep{grid-column:1/-1;border-top:1px solid rgba(var(--bs-surface-rgb,128,128,128),.07);margin:.3rem 0;}
.ef-sub{grid-column:1/-1;font-size:.64rem;font-weight:700;color:var(--bs-surface-500);padding:.2rem 0 .05rem;display:flex;align-items:center;gap:.25rem;}
.ef-sub i{font-size:.78rem;opacity:.5;}
.ef-lbl{display:block;font-size:.56rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--bs-surface-400);margin-bottom:.22rem;}
.ef-lbl .req{color:#e04646;margin-left:.1rem;}
.ef-inp,.ef-sel,.ef-ta{width:100%;background:rgba(var(--bs-surface-rgb,128,128,128),.04);border:1px solid rgba(var(--bs-surface-rgb,128,128,128),.12);border-radius:6px;padding:.28rem .55rem;font-size:.78rem;font-weight:500;color:var(--bs-surface-700);transition:border-color .15s,box-shadow .15s;outline:none;}
.ef-inp:focus,.ef-sel:focus,.ef-ta:focus{border-color:var(--bs-gold);box-shadow:0 0 0 2px rgba(212,175,55,.12);background:rgba(212,175,55,.02);}
.ef-inp.is-invalid,.ef-sel.is-invalid{border-color:#e04646!important;}
.ef-ta{resize:vertical;min-height:60px;}
.ef-sel{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23999'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;padding-right:24px;}
.ef-err{font-size:.66rem;color:#e04646;margin-top:.1rem;}
.ef-hint{font-size:.63rem;color:var(--bs-surface-400);margin-top:.1rem;font-style:italic;}
.ld-cols{display:grid;grid-template-columns:1fr 1fr;gap:0 .7rem;align-items:start;}
@media(max-width:991px){.ld-cols{grid-template-columns:1fr;}.ef-grid.g3{grid-template-columns:1fr 1fr;}}
@media(max-width:575px){.ld-hero-inner{flex-direction:column;align-items:flex-start;}.ef-grid{grid-template-columns:1fr;}}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.82rem;">
        <i class="mdi mdi-check-all me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size:.82rem;">
        <i class="mdi mdi-block-helper me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<form method="POST" action="<?php echo e(route('leads.update', $lead->id)); ?>" id="editLeadForm">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    
    <div class="ld-hero">
        <div class="ld-hero-inner">
            <div class="ld-avatar">
                <?php echo e(strtoupper(substr($lead->cn_name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr(strstr($lead->cn_name ?? '', ' ') ?: '', 1, 1))); ?>

            </div>
            <div class="ld-identity">
                <h1 class="ld-name">
                    <?php echo e($lead->cn_name ?? 'Unnamed Lead'); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status): ?>
                        <span class="ld-pill ld-pill-status st-<?php echo e(strtolower($lead->status)); ?>"><?php echo e(ucfirst($lead->status)); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->sale_at || $lead->sale_date): ?>
                        <span class="ld-pill ld-pill-sale"><i class="mdi mdi-check-circle"></i> Sale Recorded</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h1>
                <div class="ld-pills">
                    <span class="ld-pill"><i class="mdi mdi-phone"></i> <?php echo e($lead->phone_number ?? 'No phone'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->state): ?> <span class="ld-pill"><i class="mdi mdi-map-marker"></i> <?php echo e($lead->state); ?></span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name): ?> <span class="ld-pill"><i class="mdi mdi-account-tie"></i> <?php echo e($lead->closer_name); ?></span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="ld-pill"><i class="mdi mdi-pound"></i> Lead #<?php echo e($lead->id); ?></span>
                </div>
            </div>
            <div class="ld-hero-actions">
                <button type="submit" class="ld-abtn ld-abtn-primary"><i class="mdi mdi-content-save"></i> Save Changes</button>
                <a href="<?php echo e(route('leads.show', $lead->id)); ?>" class="ld-abtn ld-abtn-outline"><i class="mdi mdi-eye"></i> View</a>
                <a href="<?php echo e(route('leads.index')); ?>" class="ld-abtn ld-abtn-outline"><i class="mdi mdi-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>

    
    <div class="ld-cols">

        
        <div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account"></i> Personal Information</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid">
                        <div class="ef-f">
                            <label class="ef-lbl">Full Name <span class="req">*</span></label>
                            <input type="text" name="cn_name" class="ef-inp <?php $__errorArgs = ['cn_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('cn_name', $lead->cn_name)); ?>" placeholder="Client full name" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cn_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ef-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Date of Birth</label>
                            <input type="date" id="date_of_birth_input" name="date_of_birth" class="ef-inp <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('date_of_birth', $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('Y-m-d') : '')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ef-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Age</label>
                            <input type="number" id="age_calc" name="age" class="ef-inp" value="<?php echo e(old('age', $lead->age)); ?>" placeholder="Auto-calculated" min="0" max="120" readonly style="background:#f8f9fa;cursor:default;">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Gender</label>
                            <select name="gender" class="ef-sel">
                                <option value="">Select...</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Male','Female','Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($g); ?>" <?php echo e(strtolower(old('gender', $lead->gender ?? '')) === strtolower($g) ? 'selected' : ''); ?>><?php echo e($g); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Birth Place</label>
                            <input type="text" name="birth_place" class="ef-inp" value="<?php echo e(old('birth_place', $lead->birth_place)); ?>" placeholder="e.g., Boston, MA">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">SSN</label>
                            <input type="text" name="ssn" id="ssn" class="ef-inp <?php $__errorArgs = ['ssn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('ssn', $lead->ssn)); ?>" placeholder="XXX-XX-XXXX" maxlength="11">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Height</label>
                            <input type="text" name="height" class="ef-inp" value="<?php echo e(old('height', $lead->height)); ?>" placeholder="5'10&quot;">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Weight (lbs)</label>
                            <input type="text" name="weight" class="ef-inp" value="<?php echo e(old('weight', $lead->weight)); ?>" placeholder="180">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Nicotine User</label>
                            <select name="smoker" class="ef-sel">
                                <option value="">Select...</option>
                                <option value="yes" <?php echo e(old('smoker', $lead->smoker) == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                <option value="no"  <?php echo e(old('smoker', $lead->smoker) == 'no'  ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Has Driving License</label>
                            <select name="driving_license" class="ef-sel">
                                <option value="">Select...</option>
                                <option value="1" <?php echo e(old('driving_license', $lead->driving_license) == '1' ? 'selected' : ''); ?>>Yes</option>
                                <option value="0" <?php echo e(old('driving_license', (string)$lead->driving_license) === '0' ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">DL Number</label>
                            <input type="text" name="driving_license_number" class="ef-inp"
                                value="<?php echo e(old('driving_license_number', $lead->driving_license_number)); ?>" placeholder="License number">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Lead Date</label>
                            <input type="date" name="date" class="ef-inp"
                                value="<?php echo e(old('date', $lead->date ? \Carbon\Carbon::parse($lead->date)->format('Y-m-d') : '')); ?>">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Source</label>
                            <input type="text" name="source" class="ef-inp" value="<?php echo e(old('source', $lead->source)); ?>" placeholder="e.g., sheet, referral">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-phone-in-talk"></i> Contact Information</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid">
                        <div class="ef-f">
                            <label class="ef-lbl">Primary Phone <span class="req">*</span></label>
                            <input type="tel" name="phone_number" id="phone_number" class="ef-inp <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('phone_number', $lead->phone_number)); ?>" placeholder="(555) 000-0000" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ef-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Secondary Phone</label>
                            <input type="tel" name="secondary_phone_number" class="ef-inp"
                                value="<?php echo e(old('secondary_phone_number', $lead->secondary_phone_number)); ?>" placeholder="(555) 000-0000">
                        </div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Address</label>
                            <textarea name="address" class="ef-ta" rows="2" placeholder="Full mailing address"><?php echo e(old('address', $lead->address)); ?></textarea>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">State</label>
                            <select name="state" class="ef-sel">
                                <option value="">Select state...</option>
                                <?php $states=['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming']; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>" <?php echo e(old('state', $lead->state) == $code ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Zip Code</label>
                            <input type="text" name="zip_code" class="ef-inp" value="<?php echo e(old('zip_code', $lead->zip_code)); ?>" placeholder="00000" maxlength="10">
                        </div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Emergency Contact</label>
                            <input type="text" name="emergency_contact" class="ef-inp"
                                value="<?php echo e(old('emergency_contact', $lead->emergency_contact)); ?>" placeholder="Name & phone number">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-heart-pulse"></i> Health Information</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid">
                        <div class="ef-f full">
                            <label class="ef-lbl">Medical Issues</label>
                            <textarea name="medical_issue" class="ef-ta" rows="2" placeholder="Describe any medical conditions or history"><?php echo e(old('medical_issue', $lead->medical_issue)); ?></textarea>
                        </div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Medications</label>
                            <textarea name="medications" class="ef-ta" rows="2" placeholder="List current medications"><?php echo e(old('medications', $lead->medications)); ?></textarea>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Has Other Insurances</label>
                            <select name="has_other_insurances" class="ef-sel">
                                <option value="">Select...</option>
                                <option value="1" <?php echo e(old('has_other_insurances', $lead->has_other_insurances) == '1' ? 'selected' : ''); ?>>Yes</option>
                                <option value="0" <?php echo e(old('has_other_insurances', (string)$lead->has_other_insurances) === '0' ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                        <div class="ef-sep"></div>
                        <div class="ef-sub"><i class="mdi mdi-doctor"></i> Primary Care Physician</div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Doctor Name</label>
                            <input type="text" name="doctor_name" class="ef-inp" value="<?php echo e(old('doctor_name', $lead->doctor_name)); ?>" placeholder="Dr. Full Name">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Doctor Phone</label>
                            <input type="text" name="doctor_number" class="ef-inp" value="<?php echo e(old('doctor_number', $lead->doctor_number)); ?>" placeholder="Doctor's phone number">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Doctor Address</label>
                            <input type="text" name="doctor_address" class="ef-inp" value="<?php echo e(old('doctor_address', $lead->doctor_address)); ?>" placeholder="Clinic / office address">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-shield-check"></i> Insurance &amp; Policy</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid">
                        <div class="ef-f">
                            <label class="ef-lbl">Carrier Name</label>
                            <input type="text" name="carrier_name" class="ef-inp" value="<?php echo e(old('carrier_name', $lead->carrier_name)); ?>" placeholder="Insurance carrier">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Plan / Policy Type</label>
                            <input type="text" name="policy_type" class="ef-inp" value="<?php echo e(old('policy_type', $lead->policy_type)); ?>" placeholder="e.g., Term Life, Whole Life">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Settlement Type</label>
                            <select name="settlement_type" class="ef-sel">
                                <option value="">Select...</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Level','Graded','Modified','GI','ROP','Simplified','Guaranteed','Traditional']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($st); ?>" <?php echo e(old('settlement_type', $lead->settlement_type) == $st ? 'selected' : ''); ?>><?php echo e($st); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Policy Number</label>
                            <input type="text" name="policy_number" class="ef-inp" value="<?php echo e(old('policy_number', $lead->policy_number)); ?>" placeholder="Policy #">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Coverage Amount ($)</label>
                            <input type="number" step="0.01" name="coverage_amount" class="ef-inp"
                                value="<?php echo e(old('coverage_amount', $lead->coverage_amount)); ?>" placeholder="0.00">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Monthly Premium ($)</label>
                            <input type="number" step="0.01" name="monthly_premium" class="ef-inp"
                                value="<?php echo e(old('monthly_premium', $lead->monthly_premium)); ?>" placeholder="0.00">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Initial Draft Date</label>
                            <input type="date" name="initial_draft_date" class="ef-inp"
                                value="<?php echo e(old('initial_draft_date', $lead->initial_draft_date)); ?>">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Future Draft Date</label>
                            <input type="date" name="future_draft_date" class="ef-inp"
                                value="<?php echo e(old('future_draft_date', $lead->future_draft_date)); ?>">
                        </div>
                    </div>

                    
                    <?php
                        $bens = old('beneficiaries', null);
                        if ($bens === null) {
                            $rawBens = $lead->beneficiaries;
                            // Handle cases where the field is a JSON string rather than a decoded array
                            if (is_string($rawBens)) {
                                $rawBens = json_decode($rawBens, true) ?: null;
                            }
                            if (!empty($rawBens) && is_array($rawBens)) {
                                $bens = $rawBens;
                            } elseif ($lead->beneficiary) {
                                $bens = [[
                                    'name'     => $lead->beneficiary,
                                    'dob'      => $lead->beneficiary_dob ? \Carbon\Carbon::parse($lead->beneficiary_dob)->format('Y-m-d') : '',
                                    'relation' => '',
                                ]];
                            }
                        }
                        $bens = is_array($bens) ? $bens : [];
                    ?>

                    <div class="ef-sep mt-2"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                        <div class="ef-sub mb-0"><i class="mdi mdi-account-heart"></i> Beneficiaries</div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBeneficiaryRow()">
                            <i class="mdi mdi-plus"></i> Add
                        </button>
                    </div>

                    <div id="beneficiaries-container">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $ben): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $ben = is_array($ben) ? $ben : ['name' => $ben, 'dob' => '', 'relation' => '']; ?>
                        <div class="beneficiary-row row g-2 mb-2 mx-0">
                            <div class="col-12 col-sm-4 px-1">
                                <input type="text" name="beneficiaries[<?php echo e($i); ?>][name]" class="ef-inp"
                                    placeholder="Full name" value="<?php echo e($ben['name'] ?? ''); ?>">
                            </div>
                            <div class="col-6 col-sm-3 px-1">
                                <input type="date" name="beneficiaries[<?php echo e($i); ?>][dob]" class="ef-inp"
                                    value="<?php echo e($ben['dob'] ?? ''); ?>">
                            </div>
                            <div class="col-6 col-sm-3 px-1">
                                <select name="beneficiaries[<?php echo e($i); ?>][relation]" class="ef-sel">
                                    <option value="">Relation</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Spouse','Child','Parent','Sibling','Grandchild','Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($rel); ?>" <?php echo e(($ben['relation'] ?? '') === $rel ? 'selected' : ''); ?>><?php echo e($rel); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-12 col-sm-2 px-1">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                    onclick="this.closest('.beneficiary-row').remove()">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div id="beneficiary-empty-hint" class="text-muted small px-1 mb-1"
                        style="<?php echo e(count($bens) ? 'display:none' : ''); ?>">No beneficiaries added yet.</div>

                </div>
            </div>

        </div>

        
        <div>

            
            <div class="ld-card sale-card">
                <div class="ld-card-hdr">
                    <h5><i class="mdi mdi-handshake"></i> Sale Assignment</h5>
                </div>
                <div class="ld-card-body">
                    <div class="sale-notice">
                        <strong><i class="mdi mdi-information-outline me-1"></i> Manually Record a Sale</strong>
                        If a closer made a sale but the Ravens form closed before they submitted (e.g. the webhook ended the call early), you can record the sale here. Set the <strong>Closer Name</strong> and <strong>Sale Date/Time</strong> — the lead will appear on the Sales page under that closer's name immediately.
                    </div>
                    <div class="ef-grid">
                        <div class="ef-f">
                            <label class="ef-lbl">Closer Name</label>
                            <select name="closer_name" class="ef-sel <?php $__errorArgs = ['closer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Select Closer --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $closers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $closer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($closer->name); ?>" <?php echo e(old('closer_name', $lead->closer_name) == $closer->name ? 'selected' : ''); ?>>
                                        <?php echo e($closer->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->closer_name && !$closers->contains('name', $lead->closer_name)): ?>
                                    <option value="<?php echo e($lead->closer_name); ?>" selected><?php echo e($lead->closer_name); ?> (archived)</option>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <div class="ef-hint">Tally will show on this closer's Sales page row.</div>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Sale Date &amp; Time</label>
                            <input type="datetime-local" name="sale_at" id="sale_at" class="ef-inp <?php $__errorArgs = ['sale_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('sale_at', $lead->sale_at ? $lead->sale_at->format('Y-m-d\TH:i') : '')); ?>">
                            <div class="ef-hint">Setting this marks the lead as a completed sale.</div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sale_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ef-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Sale Date (for filters)</label>
                            <input type="date" name="sale_date" id="sale_date" class="ef-inp"
                                value="<?php echo e(old('sale_date', $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('Y-m-d') : '')); ?>">
                            <div class="ef-hint">Auto-sets from Sale Date/Time if empty.</div>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Assigned Partner</label>
                            <select name="partner_id" class="ef-sel <?php $__errorArgs = ['partner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- None --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($partner->id); ?>" <?php echo e(old('partner_id', $lead->partner_id) == $partner->id ? 'selected' : ''); ?>>
                                        <?php echo e($partner->code); ?><?php echo e($partner->name ? ' — '.$partner->name : ''); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['partner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ef-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Team</label>
                            <input type="text" name="team" class="ef-inp" value="<?php echo e(old('team', $lead->team)); ?>" placeholder="e.g., Ravens, Peregrine">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Preset Line</label>
                            <input type="text" name="preset_line" class="ef-inp" value="<?php echo e(old('preset_line', $lead->preset_line)); ?>" placeholder="e.g., Line 1">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-clipboard-check"></i> Status &amp; Notes</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid">
                        <div class="ef-f">
                            <label class="ef-lbl">Lead Status</label>
                            <select name="status" class="ef-sel <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Select...</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['pending'=>'Pending','forwarded'=>'Forwarded','active'=>'Active','accepted'=>'Accepted','approved'=>'Approved','underwriting'=>'Underwriting','rejected'=>'Rejected','declined'=>'Declined','chargeback'=>'Chargeback','cancelled'=>'Cancelled','completed'=>'Completed','unassigned'=>'Unassigned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php echo e(old('status', $lead->status) == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ef-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Decline / Pending Reason</label>
                            <input type="text" name="decline_reason" class="ef-inp"
                                value="<?php echo e(old('decline_reason', $lead->decline_reason)); ?>" placeholder="Reason if declined or pending">
                        </div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Status Notes</label>
                            <textarea name="status_notes" class="ef-ta" rows="2" placeholder="Notes about current status"><?php echo e(old('status_notes', $lead->status_notes)); ?></textarea>
                        </div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Comments</label>
                            <textarea name="comments" class="ef-ta" rows="2" placeholder="General comments"><?php echo e(old('comments', $lead->comments)); ?></textarea>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])): ?>
                        <div class="ef-f full">
                            <label class="ef-lbl">Staff Notes</label>
                            <textarea name="staff_notes" class="ef-ta" rows="2" placeholder="Internal staff notes"><?php echo e(old('staff_notes', $lead->staff_notes)); ?></textarea>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-bank"></i> Banking Information</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid">
                        <div class="ef-f">
                            <label class="ef-lbl">Bank Name</label>
                            <input type="text" name="bank_name" class="ef-inp" value="<?php echo e(old('bank_name', $lead->bank_name)); ?>" placeholder="e.g., Chase, Wells Fargo">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Account Type</label>
                            <select name="account_type" class="ef-sel">
                                <option value="">Select...</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Checking','Savings','Money Market','Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $at): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($at); ?>" <?php echo e(old('account_type', $lead->account_type) == $at ? 'selected' : ''); ?>><?php echo e($at); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="ef-f full">
                            <label class="ef-lbl">Account Title</label>
                            <input type="text" name="account_title" class="ef-inp" value="<?php echo e(old('account_title', $lead->account_title)); ?>" placeholder="Name on the account">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Routing Number</label>
                            <input type="text" name="routing_number" class="ef-inp" value="<?php echo e(old('routing_number', $lead->routing_number)); ?>" placeholder="9-digit routing #">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Account Number</label>
                            <input type="text" name="acc_number" class="ef-inp" value="<?php echo e(old('acc_number', $lead->acc_number)); ?>" placeholder="Account number">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Bank Balance ($)</label>
                            <input type="number" step="0.01" name="bank_balance" class="ef-inp" value="<?php echo e(old('bank_balance', $lead->bank_balance)); ?>" placeholder="0.00">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Account Verified By</label>
                            <input type="text" name="account_verified_by" class="ef-inp" value="<?php echo e(old('account_verified_by', $lead->account_verified_by)); ?>" placeholder="e.g., check book, voided check">
                        </div>
                        <div class="ef-sep"></div>
                        <div class="ef-sub"><i class="mdi mdi-cash-multiple"></i> Social Security</div>
                        <div class="ef-f">
                            <label class="ef-lbl">SS Amount ($)</label>
                            <input type="number" step="0.01" name="ss_amount" class="ef-inp" value="<?php echo e(old('ss_amount', $lead->ss_amount)); ?>" placeholder="Monthly SS amount">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">SS Date</label>
                            <input type="date" name="ss_date" class="ef-inp" value="<?php echo e(old('ss_date', $lead->ss_date ? \Carbon\Carbon::parse($lead->ss_date)->format('Y-m-d') : '')); ?>">
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])): ?>
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-credit-card"></i> Card Information</h5></div>
                <div class="ld-card-body">
                    <div class="ef-grid g3">
                        <div class="ef-f">
                            <label class="ef-lbl">Card Number</label>
                            <input type="text" name="card_number" class="ef-inp" value="<?php echo e(old('card_number', $lead->card_number)); ?>" placeholder="XXXX XXXX XXXX XXXX">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">CVV</label>
                            <input type="text" name="cvv" class="ef-inp" value="<?php echo e(old('cvv', $lead->cvv)); ?>" placeholder="CVV" maxlength="4">
                        </div>
                        <div class="ef-f">
                            <label class="ef-lbl">Expiry Date</label>
                            <input type="text" name="expiry_date" class="ef-inp" value="<?php echo e(old('expiry_date', $lead->expiry_date)); ?>" placeholder="MM/YY">
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>

    
    <div class="ld-card">
        <div class="ld-card-body" style="display:flex;justify-content:flex-end;align-items:center;gap:.5rem;padding:.5rem .9rem;">
            <a href="<?php echo e(route('leads.index')); ?>" class="ld-abtn ld-abtn-outline"><i class="mdi mdi-arrow-left"></i> Back</a>
            <button type="button" class="ld-abtn ld-abtn-danger" onclick="resetForm()"><i class="mdi mdi-refresh"></i> Reset</button>
            <button type="submit" class="ld-abtn ld-abtn-primary"><i class="mdi mdi-content-save"></i> Save Changes</button>
        </div>
    </div>

</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
function resetForm() {
    if (confirm('Reset all unsaved changes?')) {
        document.getElementById('editLeadForm').reset();
    }
}

// SSN formatter
const ssnInput = document.getElementById('ssn');
if (ssnInput) {
    ssnInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').slice(0,9);
        if (v.length > 5) v = v.slice(0,3)+'-'+v.slice(3,5)+'-'+v.slice(5);
        else if (v.length > 3) v = v.slice(0,3)+'-'+v.slice(3);
        this.value = v;
    });
}

// Phone formatter (primary)
const phoneInput = document.getElementById('phone_number');
if (phoneInput) {
    phoneInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').slice(0,10);
        if (v.length > 6) v = '('+v.slice(0,3)+') '+v.slice(3,6)+'-'+v.slice(6);
        else if (v.length > 3) v = '('+v.slice(0,3)+') '+v.slice(3);
        else if (v.length) v = '('+v;
        this.value = v;
    });
}

// Auto-populate sale_date from sale_at
const saleAtInput = document.getElementById('sale_at');
const saleDateInput = document.getElementById('sale_date');
if (saleAtInput && saleDateInput) {
    saleAtInput.addEventListener('change', function() {
        if (this.value && !saleDateInput.value) {
            saleDateInput.value = this.value.split('T')[0];
        }
    });
}

// ── Age auto-calculation from DOB ──────────────────────────────────────
const dobInput = document.getElementById('date_of_birth_input');
const ageField = document.getElementById('age_calc');

function calcAgeFromDob(dobVal) {
    if (!dobVal) return '';
    const dob = new Date(dobVal);
    const now = new Date();
    let age = now.getFullYear() - dob.getFullYear();
    const m = now.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;
    return age >= 0 ? age : '';
}

if (dobInput && ageField) {
    dobInput.addEventListener('change', function() {
        ageField.value = calcAgeFromDob(this.value);
    });
    if (dobInput.value) ageField.value = calcAgeFromDob(dobInput.value);
}

// ── Multi-beneficiary add/remove ────────────────────────────────────────
let beneficiaryIndex = <?php echo e(count($bens)); ?>;
const RELATION_OPTIONS = ['Spouse','Child','Parent','Sibling','Grandchild','Other'];

function addBeneficiaryRow(data = {}) {
    const i = beneficiaryIndex++;
    const container = document.getElementById('beneficiaries-container');
    const hint = document.getElementById('beneficiary-empty-hint');
    if (hint) hint.style.display = 'none';

    const relOptions = '<option value="">Relation</option>' +
        RELATION_OPTIONS.map(r => `<option value="${r}">${r}</option>`).join('');

    const row = document.createElement('div');
    row.className = 'beneficiary-row row g-2 mb-2 mx-0';
    row.innerHTML = `
        <div class="col-12 col-sm-4 px-1">
            <input type="text" name="beneficiaries[${i}][name]" class="ef-inp" placeholder="Full name" value="${data.name || ''}">
        </div>
        <div class="col-6 col-sm-3 px-1">
            <input type="date" name="beneficiaries[${i}][dob]" class="ef-inp" value="${data.dob || ''}">
        </div>
        <div class="col-6 col-sm-3 px-1">
            <select name="beneficiaries[${i}][relation]" class="ef-sel">${relOptions}</select>
        </div>
        <div class="col-12 col-sm-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                onclick="this.closest('.beneficiary-row').remove(); updateBeneficiaryHint();">
                <i class="mdi mdi-trash-can-outline"></i>
            </button>
        </div>`;
    container.appendChild(row);
}

function updateBeneficiaryHint() {
    const container = document.getElementById('beneficiaries-container');
    const hint = document.getElementById('beneficiary-empty-hint');
    if (!hint) return;
    hint.style.display = container.querySelectorAll('.beneficiary-row').length === 0 ? '' : 'none';
}

// ── Form validation ──────────────────────────────────────────────────────
document.getElementById('editLeadForm').addEventListener('submit', function(e) {
    let ok = true;
    this.querySelectorAll('[required]').forEach(f => {
        if (!f.value.trim()) { ok = false; f.classList.add('is-invalid'); }
        else f.classList.remove('is-invalid');
    });
    if (!ok) { e.preventDefault(); alert('Please fill in all required fields.'); }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/leads/edit.blade.php ENDPATH**/ ?>