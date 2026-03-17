<?php $__env->startSection('title', 'Followup Done'); ?>

<?php $__env->startSection('css'); ?>
<style>
.kpi-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem;}
.kpi-card{flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);transition:transform .15s;}
.kpi-card:hover{transform:translateY(-2px);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1;}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val{color:#b89730}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val{color:#556ee6}
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;}
.ex-tbl{width:100%;font-size:.735rem;border-collapse:collapse;}
.ex-tbl thead th{padding:.35rem .6rem;font-weight:600;font-size:.68rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
.ex-tbl tbody td{padding:.4rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}
.bd-fd{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;}
.filter-form label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}
.info-pill{display:inline-flex;align-items:center;gap:.25rem;font-size:.65rem;background:rgba(85,110,230,.1);color:#556ee6;border:1px solid rgba(85,110,230,.2);padding:.2rem .5rem;border-radius:.3rem;}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 py-3" style="max-width:1600px">

    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h5 class="mb-0 fw-semibold" style="font-size:1rem;">
                <i class="bx bx-check-circle me-1" style="color:#556ee6;font-size:1.05rem;"></i>
                Followup Done
            </h5>
            <p class="mb-0" style="font-size:.68rem;color:var(--bs-surface-400);">
                Stage 5 — Policy papers confirmed by closer. Awaiting manager's Pending Draft assignment.
            </p>
        </div>
        <a href="<?php echo e(route('pending-draft.index')); ?>" class="a-btn" style="background:rgba(241,180,76,.15);color:#b87a14;border-color:rgba(241,180,76,.3);">
            <i class="bx bx-time-five"></i> Pending Draft Queue
        </a>
    </div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="info-pill">
            <i class="bx bx-info-circle"></i>
            These leads have been confirmed by the closer. Move them to Pending Draft when bank details are verified.
        </span>
    </div>

    
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <div class="k-val"><?php echo e($totalCount); ?></div>
            <div class="k-lbl">Total</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="k-val"><?php echo e($pendingDraftCount); ?></div>
            <div class="k-lbl">Not yet in Draft</div>
        </div>
    </div>

    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-check me-1"></i> Followup Confirmed Leads</h6>
        </div>

        
        <form method="GET" action="<?php echo e(route('followup.followup-done')); ?>" class="filter-form">
            <div>
                <label>Search</label>
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Name, phone…" style="width:150px;">
            </div>
            <div>
                <label>Carrier</label>
                <select name="carrier" class="form-select" style="width:130px;">
                    <option value="">All Carriers</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e($carrier == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div>
                <label>From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e($dateFrom); ?>" style="width:135px;">
            </div>
            <div>
                <label>To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e($dateTo); ?>" style="width:135px;">
            </div>
            <button type="submit" class="a-btn" style="background:rgba(85,110,230,.2);color:#556ee6;border-color:rgba(85,110,230,.3);height:2rem;">
                <i class="bx bx-search-alt-2"></i> Filter
            </button>
            <a href="<?php echo e(route('followup.followup-done')); ?>" class="f-reset"><i class="bx bx-reset"></i> Clear</a>
        </form>

        <div class="table-responsive">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Closer</th>
                        <th>Followup Done By</th>
                        <th>Done At</th>
                        <th>Bank Verification</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="color:var(--bs-surface-400);"><?php echo e($lead->id); ?></td>
                            <td>
                                <a href="<?php echo e(route('issuance.show', $lead->id)); ?>" style="font-weight:500;font-size:.73rem;">
                                    <?php echo e($lead->cn_name ?? '—'); ?>

                                </a>
                            </td>
                            <td><?php echo e($lead->phone_number ?? '—'); ?></td>
                            <td><?php echo e($lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—')); ?></td>
                            <td>$<?php echo e(number_format($lead->monthly_premium, 2)); ?></td>
                            <td><?php echo e($lead->closer_name ?? '—'); ?></td>
                            <td><?php echo e($lead->followupDoneBy->name ?? '—'); ?></td>
                            <td><?php echo e($lead->followup_done_at ? $lead->followup_done_at->format('M d, Y') : '—'); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->bank_verification_status === 'verified'): ?>
                                    <span class="bd-fd">Verified</span>
                                <?php elseif($lead->bank_verification_status): ?>
                                    <span style="font-size:.65rem;color:var(--bs-surface-400);"><?php echo e($lead->bank_verification_status); ?></span>
                                <?php else: ?>
                                    <span style="font-size:.65rem;color:var(--bs-surface-400);">Pending</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->pending_draft_at): ?>
                                    <span class="bd-fd">In Draft Queue</span>
                                <?php else: ?>
                                    <span style="font-size:.65rem;background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);padding:.2rem .5rem;border-radius:.3rem;">Awaiting</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('issuance.show', $lead->id)); ?>" class="a-btn" style="background:rgba(85,110,230,.12);color:#556ee6;border-color:rgba(85,110,230,.25);">
                                    <i class="bx bx-show"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No followup-done leads at this time.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leads->hasPages()): ?>
            <div class="px-3 py-2"><?php echo e($leads->withQueryString()->links()); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/followup/done.blade.php ENDPATH**/ ?>