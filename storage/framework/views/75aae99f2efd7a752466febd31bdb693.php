<?php use \App\Support\Statuses; ?>


<?php $__env->startSection('title', 'Pendings Approved'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ── KPI Cards ── */
.kpi-row { display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem; }
.kpi-card { flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);transition:transform .15s,box-shadow .15s; }
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1;}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val{color:#b89730}
.kpi-card.k-green{background:rgba(52,195,143,.06)}.kpi-card.k-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}.kpi-card.k-green .k-val{color:#1a8754}
.kpi-card.k-red{background:rgba(244,106,106,.06)}.kpi-card.k-red::before{background:linear-gradient(90deg,#f46a6a,#f89b9b)}.kpi-card.k-red .k-val{color:#c84646}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val{color:#556ee6}

/* ── Section Card ── */
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;}

/* ── Table ── */
.ex-tbl{width:100%;font-size:.735rem;border-collapse:collapse;}
.ex-tbl thead th{padding:.35rem .6rem;font-weight:600;font-size:.68rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
.ex-tbl tbody td{padding:.4rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}

/* ── Status badges ── */
.bd-ni{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-resolved{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-pending{background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}

/* ── Action Buttons ── */
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.a-send{background:#34c38f20;color:#1a8754;border-color:#34c38f40;}.a-send:hover{background:#34c38f30;color:#1a8754;}
.a-ni{background:#f46a6a20;color:#c84646;border-color:#f46a6a40;}.a-ni:hover{background:#f46a6a30;color:#c84646;}
.a-resolve{background:#556ee620;color:#556ee6;border-color:#556ee640;}.a-resolve:hover{background:#556ee630;color:#556ee6;}

/* ── Filter bar ── */
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;}
.filter-form label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}.f-reset:hover{color:var(--bs-body-color);}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 py-3" style="max-width:1600px">

    
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h5 class="mb-0 fw-semibold" style="font-size:1rem;">
                <i class="bx bx-check-circle me-1" style="color:#34c38f;font-size:1.05rem;"></i>
                Pendings Approved
            </h5>
            <p class="mb-0" style="font-size:.68rem;color:var(--bs-surface-400);">
                Stage 2 — Manager-approved leads awaiting carrier submission
            </p>
        </div>
        <div class="d-flex gap-1 align-items-center">
            <a href="<?php echo e(route('issuance.index')); ?>" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.08);">
                <i class="bx bx-right-arrow-alt"></i> Pending Contract
            </a>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <div class="k-val"><?php echo e($totalCount); ?></div>
            <div class="k-lbl">Total</div>
        </div>
        <div class="kpi-card k-green">
            <div class="k-val"><?php echo e($readyCount); ?></div>
            <div class="k-lbl">Ready to Send</div>
        </div>
        <div class="kpi-card k-red">
            <div class="k-val"><?php echo e($notIssuedCount); ?></div>
            <div class="k-lbl">Not Issued</div>
        </div>
    </div>

    
    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-ul me-1"></i> Approved Leads</h6>
        </div>

        
        <form method="GET" action="<?php echo e(route('pendings-approved.index')); ?>" class="filter-form">
            <div>
                <label>Search</label>
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Name, phone, carrier…" style="width:160px;">
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
            <button type="submit" class="a-btn a-send" style="height:2rem;">
                <i class="bx bx-search-alt-2"></i> Filter
            </button>
            <a href="<?php echo e(route('pendings-approved.index')); ?>" class="f-reset">
                <i class="bx bx-reset"></i> Clear
            </a>
        </form>

        
        <div class="table-responsive" style="min-height:200px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Phone</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Closer</th>
                        <th>Sale Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isBlocked = !empty($lead->not_issued_at) && empty($lead->not_issued_resolved_at);
                            $wasResolved = !empty($lead->not_issued_at) && !empty($lead->not_issued_resolved_at);
                        ?>
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
                            <td><?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—'); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isBlocked): ?>
                                    <span class="bd-ni">
                                        Not Issued: <?php echo e(Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_issued_disposition] ?? $lead->not_issued_disposition); ?>

                                    </span>
                                    <div style="font-size:.6rem;color:var(--bs-surface-400);margin-top:.1rem;">
                                        by <?php echo e($lead->notIssuedBy->name ?? '?'); ?> · <?php echo e($lead->not_issued_at->diffForHumans()); ?>

                                    </div>
                                <?php elseif($wasResolved): ?>
                                    <span class="bd-resolved">Resolved</span>
                                <?php else: ?>
                                    <span class="bd-pending">Ready</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isBlocked): ?>
                                        @canDo('pendings-approved', 'edit')
                                        <button class="a-btn a-send btn-send-contract" data-id="<?php echo e($lead->id); ?>">
                                            <i class="bx bx-right-arrow-alt"></i> Send to Contract
                                        </button>
                                        @endcanDo
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isBlocked): ?>
                                        @canDo('pendings-approved', 'edit')
                                        <button class="a-btn a-ni btn-mark-ni" data-id="<?php echo e($lead->id); ?>" data-name="<?php echo e($lead->cn_name); ?>">
                                            <i class="bx bx-error-circle"></i> Not Issued
                                        </button>
                                        @endcanDo
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isBlocked): ?>
                                        @canDo('pendings-approved', 'edit')
                                        <button class="a-btn a-resolve btn-resolve-ni" data-id="<?php echo e($lead->id); ?>">
                                            <i class="bx bx-check"></i> Resolve
                                        </button>
                                        @endcanDo
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No leads in Pendings Approved for the selected period.
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


<div class="modal fade" id="niModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-error-circle me-1 text-danger"></i> Mark as Not Issued
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="ni-lead-name"></strong>
                </p>
                <label class="form-label" style="font-size:.72rem;font-weight:600;">Disposition Reason</label>
                <select id="ni-disposition" class="form-select form-select-sm">
                    <option value="">— Select reason —</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = Statuses::NOT_ISSUED_DISPOSITIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="ni-confirm-btn">Mark Not Issued</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
(function() {
    let currentLeadId = null;

    // Send to Contract
    document.querySelectorAll('.btn-send-contract').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (!confirm('Send this lead to Pending Contract?')) return;
            fetch(`/pendings-approved/${id}/send-to-contract`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { location.reload(); }
                else { alert(data.message); }
            });
        });
    });

    // Mark Not Issued — open modal
    document.querySelectorAll('.btn-mark-ni').forEach(btn => {
        btn.addEventListener('click', function() {
            currentLeadId = this.dataset.id;
            document.getElementById('ni-lead-name').textContent = this.dataset.name;
            document.getElementById('ni-disposition').value = '';
            new bootstrap.Modal(document.getElementById('niModal')).show();
        });
    });

    // Confirm Not Issued
    document.getElementById('ni-confirm-btn').addEventListener('click', function() {
        const disposition = document.getElementById('ni-disposition').value;
        if (!disposition) { alert('Please select a disposition reason.'); return; }
        fetch(`/pendings-approved/${currentLeadId}/mark-not-issued`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({not_issued_disposition: disposition})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert(data.message); }
        });
    });

    // Resolve Not Issued
    document.querySelectorAll('.btn-resolve-ni').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (!confirm('Mark this Not Issued block as resolved?')) return;
            fetch(`/pendings-approved/${id}/resolve-not-issued`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { location.reload(); }
                else { alert(data.message); }
            });
        });
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pendings-approved/index.blade.php ENDPATH**/ ?>