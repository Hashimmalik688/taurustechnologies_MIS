<?php $__env->startSection('title', 'Transaction Details'); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .detail-lbl{font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.15rem}
    .detail-val{font-size:.82rem;font-weight:600;color:var(--bs-body-color);margin-bottom:.85rem}
    .amount-hero{font-size:2rem;font-weight:800;letter-spacing:-1px}
    .amount-credit{color:#10b981}.amount-debit{color:#ef4444}
    .tl-wrap{position:relative;padding-left:1.8rem}
    .tl-wrap::before{content:'';position:absolute;left:.55rem;top:0;bottom:0;width:2px;background:linear-gradient(180deg,#d4af37,rgba(212,175,55,.15))}
    .tl-item{position:relative;padding-bottom:1.2rem}
    .tl-item::before{content:'';position:absolute;left:-1.25rem;top:.25rem;width:10px;height:10px;border-radius:50%;background:#d4af37;border:2px solid var(--bs-card-bg)}
    .tl-time{font-size:.65rem;color:var(--bs-surface-500)}
    .tl-text{font-size:.75rem;color:var(--bs-body-color)}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-receipt"></i> Transaction Details</h4>
        </div>
        <a href="<?php echo e(route('ledger.index')); ?>" class="act-btn a-info"><i class="bx bx-arrow-back"></i> Back</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            
            <div class="ex-card sec-card mb-2">
                <div class="sec-body text-center" style="padding:1.5rem">
                    <div class="amount-hero amount-credit">+$1,250.00</div>
                    <span class="s-pill s-active mt-1">CREDIT</span>
                    <div class="d-flex justify-content-center gap-3 mt-2" style="font-size:.72rem;color:var(--bs-surface-500)">
                        <span><i class="bx bx-calendar me-1"></i>September 28, 2025</span>
                        <span><i class="bx bx-hash me-1"></i>INV-001234</span>
                    </div>
                </div>
            </div>

            
            <div class="ex-card sec-card">
                <div class="sec-hdr"><i class="bx bx-info-circle"></i> Transaction Info</div>
                <div class="sec-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-lbl">Category</div>
                            <div class="detail-val"><span class="v-badge">Commission</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-lbl">Reference Number</div>
                            <div class="detail-val">INV-001234</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-lbl">Description</div>
                            <div class="detail-val" style="color:var(--bs-surface-400)">Policy sale commission for large account. Client purchased comprehensive life insurance policy with $500,000 coverage.</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-lbl">Related Lead</div>
                            <div class="detail-val"><a href="#" style="color:#d4af37;text-decoration:none;font-weight:600">Lead #1234 - John Doe</a></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-lbl">Created By</div>
                            <div class="detail-val">Admin User</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="d-flex justify-content-end gap-2 mt-2">
                <?php if(auth()->check() && auth()->user()->canEditModule('general-ledger')): ?>
                <button class="act-btn a-success"><i class="bx bx-edit"></i> Edit</button>
                <?php endif; ?>
                <?php if(auth()->check() && auth()->user()->canDeleteInModule('general-ledger')): ?>
                <button class="act-btn a-danger"><i class="bx bx-trash"></i> Delete</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="ex-card sec-card">
                <div class="sec-hdr"><i class="bx bx-time-five"></i> Timeline</div>
                <div class="sec-body">
                    <div class="tl-wrap">
                        <div class="tl-item">
                            <div class="tl-time">Sep 28, 2025 — 10:30 AM</div>
                            <div class="tl-text"><strong>Transaction Created</strong><br>Created by Admin User</div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-time">Sep 28, 2025 — 10:35 AM</div>
                            <div class="tl-text"><strong>Notification Sent</strong><br>Email notification sent</div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-time">Sep 28, 2025 — 2:15 PM</div>
                            <div class="tl-text"><strong>Transaction Verified</strong><br>Verified by Manager</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/ledger/show.blade.php ENDPATH**/ ?>