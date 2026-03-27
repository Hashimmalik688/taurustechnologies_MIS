<?php $__env->startSection('title', 'Ravens Validation'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .modal-header-custom {
        background: linear-gradient(135deg, var(--bs-card-bg) 0%, rgba(212,175,55,.08) 100%);
        border-bottom: 1px solid rgba(212,175,55,.15);
        color: var(--bs-surface-800);
    }
    .modal-header-custom .modal-title { font-size: .85rem; font-weight: 700; }
    .modal-xl { max-width: 1100px; }
    .modal-dialog-scrollable .modal-body { max-height: calc(100vh - 220px); overflow-y: auto; }

    .mgr-badge { display:inline-block;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:700;text-transform:capitalize; }
    .mgr-approved { background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2); }
    .mgr-declined { background:rgba(244,106,106,.1);color:#c84646;border:1px solid rgba(244,106,106,.2); }

    .act-group { display:inline-flex; gap:.25rem; flex-wrap:wrap; }
    .reviewed-row td { opacity:.75; }

    .pipe-toggle {
        display:inline-flex;align-items:center;gap:.35rem;
        font-size:.68rem;font-weight:600;color:var(--bs-surface-500);
        padding:.25rem .6rem;border-radius:22px;
        border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);cursor:pointer;
    }
    .pipe-toggle input { width:14px;height:14px;accent-color:#d4af37;cursor:pointer; }
    .pipe-toggle.active { border-color:rgba(212,175,55,.3);background:rgba(212,175,55,.06);color:#b89730; }

    .detail-tbl td { padding:.3rem .45rem;font-size:.78rem;border-bottom:1px solid rgba(0,0,0,.04); }
    .detail-tbl td:first-child { font-weight:600;color:var(--bs-surface-500);width:38%;white-space:nowrap; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<form method="GET" action="<?php echo e(route('ravens.validation.index')); ?>" id="filterForm" class="ex-card pipe-filter-bar">
    <a href="<?php echo e(route('ravens.validation.index', ['filter' => 'today'])); ?>"
       class="pipe-pill <?php echo e($filter === 'today' ? 'active' : ''); ?>">
        <i class="bx bx-calendar"></i> Today
    </a>
    <a href="<?php echo e(route('ravens.validation.index', ['filter' => 'week'])); ?>"
       class="pipe-pill <?php echo e($filter === 'week' ? 'active' : ''); ?>">
        <i class="bx bx-calendar-week"></i> This Week
    </a>
    <a href="<?php echo e(route('ravens.validation.index', ['filter' => 'month'])); ?>"
       class="pipe-pill <?php echo e($filter === 'month' ? 'active' : ''); ?>">
        <i class="bx bx-calendar-alt"></i> This Month
    </a>
    <span class="pipe-pill <?php echo e($filter === 'custom' ? 'active' : ''); ?>"
          onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'"
          style="cursor:pointer;">
        <i class="bx bx-calendar-event"></i> Custom Range
    </span>
    <span id="customRange" style="display:<?php echo e($filter === 'custom' ? 'flex' : 'none'); ?>;align-items:center;gap:.3rem;">
        <input type="hidden" name="filter" value="custom">
        <span class="pipe-pill-lbl">FROM</span>
        <input type="text" name="start_date" class="pipe-pill-date" value="<?php echo e(request('start_date')); ?>" placeholder="YYYY-MM-DD" readonly>
        <span class="pipe-pill-lbl">TO</span>
        <input type="text" name="end_date" class="pipe-pill-date" value="<?php echo e(request('end_date')); ?>" placeholder="YYYY-MM-DD" readonly>
        <button type="submit" class="pipe-pill-apply">Apply</button>
    </span>
    <span style="display:inline-flex;align-items:center;gap:.3rem;position:relative;">
        <i class="bx bx-search" style="position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none;"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="Search name, phone…"
               style="font-size:.72rem;font-weight:600;padding:.32rem .55rem .32rem 1.8rem;border-radius:22px;border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);color:var(--bs-surface-600);outline:none;min-width:165px;">
    </span>
    <label class="pipe-toggle <?php echo e($showAll ? 'active' : ''); ?>">
        <input type="checkbox" name="show_all" value="1"
               <?php echo e($showAll ? 'checked' : ''); ?>

               onchange="document.getElementById('filterForm').submit()">
        Show all pending
    </label>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter !== 'today' || $search): ?>
        <a href="<?php echo e(route('ravens.validation.index', ['filter' => 'today'])); ?>" class="pipe-pill-clear">
            <i class="bx bx-x"></i> Clear
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</form>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div id="flashMsg" class="ex-card" style="display:flex;align-items:center;gap:.5rem;padding:.55rem .85rem;margin-bottom:.65rem;background:rgba(16,185,129,.06);border-color:rgba(16,185,129,.15);">
        <i class="bx bx-check-circle" style="color:#10b981;font-size:1rem;"></i>
        <span style="font-size:.78rem;font-weight:600;color:#065f46;"><?php echo e(session('success')); ?></span>
        <button type="button" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#065f46;opacity:.6;font-size:1rem;" onclick="this.parentElement.remove()">&times;</button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="kpi-row">
    <div class="kpi-card k-warn ex-card">
        <i class="bx bx-time-five k-icon"></i>
        <div class="k-val"><?php echo e($todayStats['pending']); ?></div>
        <div class="k-lbl">Pending</div>
    </div>
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-send k-icon"></i>
        <div class="k-val"><?php echo e($todayStats['sent_to_policy']); ?></div>
        <div class="k-lbl">Valid</div>
    </div>
    <div class="kpi-card k-red ex-card">
        <i class="bx bx-x-circle k-icon"></i>
        <div class="k-val"><?php echo e($todayStats['kept_declined']); ?></div>
        <div class="k-lbl">Not Valid</div>
    </div>
    <div class="kpi-card ex-card" style="background:rgba(139,92,246,.06);border-color:rgba(139,92,246,.15);">
        <i class="bx bx-phone-outgoing k-icon" style="color:#8b5cf6;"></i>
        <div class="k-val" style="color:#7c3aed;"><?php echo e($todayStats['callback']); ?></div>
        <div class="k-lbl">Call Back</div>
    </div>
</div>


<div class="ex-card sec-card">
    <div class="pipe-hdr" style="color:#b87a14;">
        <i class="bx bx-check-shield" style="color:#f1b44c;"></i>
        Pending Validation
        <span class="badge-count"><?php echo e($pendingLeads->count()); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingLeads->isEmpty()): ?>
        <div style="padding:2rem;text-align:center;color:var(--bs-surface-400);font-size:.8rem;">
            <i class="bx bx-check-double" style="font-size:2.2rem;opacity:.25;display:block;margin-bottom:.5rem;"></i>
            No leads pending validation for this period.
        </div>
    <?php else: ?>
    <div class="scroll-tbl" style="max-height:520px;">
        <table class="ex-tbl">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th class="text-center">Mgr Status</th>
                    <th class="text-center" style="min-width:240px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pendingLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->recall_requested_at): ?>
                            <br><span style="display:inline-flex;align-items:center;gap:.25rem;background:rgba(139,92,246,.12);color:#7c3aed;border:1px solid rgba(139,92,246,.3);border-radius:10px;font-size:.6rem;font-weight:700;padding:.15rem .45rem;margin-top:.2rem;">
                                <i class="bx bx-phone-outgoing" style="font-size:.65rem;"></i> Callback
                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->recall_note): ?>
                                <br><span style="font-size:.6rem;color:#94a3b8;font-style:italic;"><?php echo e($lead->recall_note); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->state): ?>
                            <br><span style="font-size:.6rem;color:var(--bs-surface-400);"><?php echo e($lead->state); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td><?php echo e($lead->phone_number ?? '—'); ?></td>
                    <td class="text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->submission_status === 'approved'): ?>
                            <span class="mgr-badge mgr-approved"><i class="bx bx-check"></i> Approved</span>
                        <?php elseif($lead->submission_status === 'declined'): ?>
                            <span class="mgr-badge mgr-declined"><i class="bx bx-x"></i> Declined</span>
                        <?php else: ?>
                            <span class="mgr-badge" style="background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.2);"><i class="bx bx-time-five"></i> Pending</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="act-group">
                            <button type="button" class="act-btn a-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal<?php echo e($lead->id); ?>">
                                <i class="bx bx-show"></i> View
                            </button>
                            <form method="POST" action="<?php echo e(route('ravens.validation.mark-valid', $lead->id)); ?>"
                                  style="display:inline;"
                                  onsubmit="return confirm('Mark this lead as valid?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="act-btn a-success">
                                    <i class="bx bx-check-circle"></i> Valid
                                </button>
                            </form>
                            <button type="button" class="act-btn a-danger"
                                    onclick="openRecallModal(<?php echo e($lead->id); ?>, '<?php echo e(addslashes($lead->cn_name ?? 'N/A')); ?>')">
                                <i class="bx bx-x-circle"></i> Not Valid
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pendingLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $beneficiaries = $lead->beneficiaries ?? [];
    if (is_string($beneficiaries)) { $dec = json_decode($beneficiaries, true); $beneficiaries = is_array($dec) ? $dec : []; }
    if (!is_array($beneficiaries)) { $beneficiaries = []; }
    if (empty($beneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
        $beneficiaries = [['name' => $lead->beneficiary ?? '', 'dob' => $lead->beneficiary_dob ?? '', 'relation' => '']];
    }
?>
<div class="modal fade" id="detailModal<?php echo e($lead->id); ?>" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">
                    <i class="bx bx-user-circle" style="color:#d4af37;margin-right:.4rem;"></i>
                    <?php echo e($lead->cn_name); ?> &mdash;
                    <span class="mgr-badge <?php echo e($lead->submission_status === 'approved' ? 'mgr-approved' : ($lead->submission_status === 'declined' ? 'mgr-declined' : '')); ?>" style="margin-left:.3rem;<?php echo e($lead->submission_status === 'pending' ? 'background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.2);' : ''); ?>">
                        <?php echo e(ucfirst($lead->submission_status ?? '—')); ?>

                    </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-user"></i> Personal Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Full Name</td><td><?php echo e($lead->cn_name ?? '—'); ?></td></tr>
                                <tr><td>Date of Birth</td><td><?php echo e($lead->date_of_birth?->format('M d, Y') ?? '—'); ?></td></tr>
                                <tr><td>Age</td><td><?php echo e($lead->age ?? '—'); ?></td></tr>
                                <tr><td>Gender</td><td><?php echo e($lead->gender ?? '—'); ?></td></tr>
                                <tr><td>Birth Place</td><td><?php echo e($lead->birth_place ?? '—'); ?></td></tr>
                                <tr><td>SSN</td><td><?php echo e($lead->ssn ?? '—'); ?></td></tr>
                            </table>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-phone"></i> Contact Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Primary Phone</td><td><?php echo e($lead->phone_number ?? '—'); ?></td></tr>
                                <tr><td>Secondary Phone</td><td><?php echo e($lead->secondary_phone_number ?? '—'); ?></td></tr>
                                <tr><td>Address</td><td><?php echo e($lead->address ?? '—'); ?></td></tr>
                                <tr><td>State</td><td><?php echo e($lead->state ?? '—'); ?></td></tr>
                                <tr><td>Zip Code</td><td><?php echo e($lead->zip_code ?? '—'); ?></td></tr>
                                <tr><td>Emergency Contact</td><td><?php echo e($lead->emergency_contact ?? '—'); ?></td></tr>
                            </table>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-heart"></i> Health Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Nicotine User</td><td><?php echo e($lead->smoker ? 'Yes' : 'No'); ?></td></tr>
                                <tr><td>Height</td><td><?php echo e($lead->height ?? '—'); ?></td></tr>
                                <tr><td>Weight</td><td><?php echo e($lead->weight ? $lead->weight.' lbs' : '—'); ?></td></tr>
                                <tr><td>Driving License</td><td><?php echo e($lead->driving_license ? 'Yes' : 'No'); ?></td></tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->driving_license_number): ?>
                                <tr><td>DL Number</td><td><?php echo e($lead->driving_license_number); ?></td></tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <tr><td>Medical Issues</td><td><?php echo e($lead->medical_issue ?? 'None reported'); ?></td></tr>
                                <tr><td>Medications</td><td><?php echo e($lead->medications ?? 'None reported'); ?></td></tr>
                                <tr><td>Doctor Name</td><td><?php echo e($lead->doctor_name ?? '—'); ?></td></tr>
                                <tr><td>Doctor Phone</td><td><?php echo e($lead->doctor_number ?? '—'); ?></td></tr>
                                <tr><td>Doctor Address</td><td><?php echo e($lead->doctor_address ?? '—'); ?></td></tr>
                            </table>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-shield-check"></i> Policy Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Plan Type</td><td><?php echo e($lead->policy_type ?? '—'); ?></td></tr>
                                <tr><td>Policy Number</td><td><?php echo e($lead->policy_number ?? '—'); ?></td></tr>
                                <tr><td>Carrier</td><td><?php echo e($lead->carrier_name ?? '—'); ?></td></tr>
                                <tr><td>Coverage Amount</td><td><?php echo e($lead->coverage_amount ? '$'.number_format($lead->coverage_amount,0) : '—'); ?></td></tr>
                                <tr><td>Monthly Premium</td><td><?php echo e($lead->monthly_premium ? '$'.number_format($lead->monthly_premium,2).'/mo' : '—'); ?></td></tr>
                                <tr><td>Initial Draft Date</td><td><?php echo e($lead->initial_draft_date?->format('M d, Y') ?? '—'); ?></td></tr>
                                <tr><td>Future Draft Date</td><td><?php echo e($lead->future_draft_date?->format('M d, Y') ?? '—'); ?></td></tr>
                                <tr><td>Closer</td><td><?php echo e($lead->closer_name ?? '—'); ?></td></tr>
                                <tr><td>Sale Date</td><td><?php echo e($lead->sale_at?->setTimezone('America/Los_Angeles')->format('M d, Y h:i A') ?? $lead->sale_date?->format('M d, Y') ?? '—'); ?></td></tr>
                                <tr><td>Lead Source</td><td><?php echo e($lead->source ?? '—'); ?></td></tr>
                            </table>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-heart-circle"></i> Beneficiary Information</div>
                            <?php if(!empty($beneficiaries)): ?>
                                <?php $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bi => $bene): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($beneficiaries) > 1): ?>
                                        <div style="font-size:.65rem;font-weight:700;color:#b89730;margin-bottom:.2rem;">Beneficiary <?php echo e($bi + 1); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <table class="detail-tbl" style="width:100%;border-collapse:collapse;<?php echo e(!$loop->last ? 'margin-bottom:.5rem;' : ''); ?>">
                                        <tr><td>Name</td><td><?php echo e($bene['name'] ?? '—'); ?></td></tr>
                                        <tr><td>Relation</td><td><?php echo e($bene['relation'] ?? '—'); ?></td></tr>
                                        <tr><td>Date of Birth</td><td><?php try { echo !empty($bene['dob']) ? \Carbon\Carbon::parse($bene['dob'])->format('M d, Y') : '—'; } catch(\Exception $e) { echo '—'; } ?></td></tr>
                                    </table>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <span style="font-size:.78rem;color:var(--bs-surface-400);">No beneficiaries added</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-bank"></i> Bank Account</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Bank Name</td><td><?php echo e($lead->bank_name ?? '—'); ?></td></tr>
                                <tr><td>Account Type</td><td><?php echo e($lead->account_type ?? '—'); ?></td></tr>
                                <tr><td>Account Title</td><td><?php echo e($lead->account_title ?? '—'); ?></td></tr>
                                <tr><td>Routing #</td><td><?php echo e($lead->routing_number ?? '—'); ?></td></tr>
                                <tr><td>Account #</td><td><?php echo e($lead->acc_number ?? $lead->account_number ?? '—'); ?></td></tr>
                                <tr><td>Bank Balance</td><td><?php echo e($lead->bank_balance ? '$'.number_format($lead->bank_balance,2) : '—'); ?></td></tr>
                                <tr><td>SS Amount</td><td><?php echo e($lead->ss_amount ? '$'.number_format($lead->ss_amount,2) : '—'); ?></td></tr>
                                <tr><td>SS Date</td><td><?php echo e($lead->ss_date?->format('M d, Y') ?? '—'); ?></td></tr>
                                <tr><td>BV Status</td><td><?php echo e($lead->bank_verification_status ?? '—'); ?></td></tr>
                            </table>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-user-check"></i> Manager Review</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td>Status</td>
                                    <td><span class="mgr-badge <?php echo e($lead->submission_status === 'approved' ? 'mgr-approved' : ($lead->submission_status === 'declined' ? 'mgr-declined' : '')); ?>" style="<?php echo e($lead->submission_status === 'pending' ? 'background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.2);' : ''); ?>"><?php echo e(ucfirst($lead->submission_status ?? '—')); ?></span></td>
                                </tr>
                                <tr><td>Reviewed At</td><td><?php echo e($lead->submission_at?->setTimezone('America/Los_Angeles')->format('M d, Y h:i A') ?? '—'); ?></td></tr>
                                <tr><td>Reason</td><td><?php echo e($lead->submission_reason ?? '—'); ?></td></tr>
                            </table>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->manager_notes): ?>
                                <div style="margin-top:.5rem;padding:.5rem .65rem;background:rgba(212,175,55,.05);border-radius:.4rem;border:1px solid rgba(212,175,55,.12);font-size:.77rem;"><?php echo e($lead->manager_notes); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->staff_notes || $lead->comments || $lead->preset_line): ?>
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-note"></i> Notes</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->preset_line): ?><tr><td>Preset Line</td><td><?php echo e($lead->preset_line); ?></td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->comments): ?><tr><td>Comments</td><td><?php echo e($lead->comments); ?></td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->staff_notes): ?><tr><td>Staff Notes</td><td><?php echo e($lead->staff_notes); ?></td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
            </div>
            <div class="modal-footer" style="gap:.35rem;">
                <form method="POST" action="<?php echo e(route('ravens.validation.mark-valid', $lead->id)); ?>"
                      onsubmit="return confirm('Mark this lead as valid?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="act-btn a-success" style="padding:.35rem .9rem;">
                        <i class="bx bx-check-circle"></i> Valid
                    </button>
                </form>
                <button type="button" class="act-btn a-danger" style="padding:.35rem .9rem;"
                        onclick="closeDetailModal(<?php echo e($lead->id); ?>); openRecallModal(<?php echo e($lead->id); ?>, '<?php echo e(addslashes($lead->cn_name ?? 'N/A')); ?>')"
                >
                    <i class="bx bx-x-circle"></i> Not Valid
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="ex-card sec-card">
    <div class="pipe-hdr" style="color:#2b81c9;">
        <i class="bx bx-check-double" style="color:#50a5f1;"></i>
        Reviewed
        <span class="badge-count"><?php echo e($reviewedLeads->count()); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reviewedLeads->isEmpty()): ?>
        <div style="padding:1.5rem;text-align:center;color:var(--bs-surface-400);font-size:.78rem;">
            No leads reviewed in this period.
        </div>
    <?php else: ?>
    <div class="scroll-tbl" style="max-height:380px;">
        <table class="ex-tbl">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th class="text-center">Result</th>
                    <th>Validated By</th>
                    <th>Validated At</th>
                    <th>Note</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $reviewedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="reviewed-row">
                    <td><strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong></td>
                    <td style="font-size:.7rem;"><?php echo e($lead->phone_number ?? '—'); ?></td>
                    <td class="text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->ravens_validation_status === 'valid'): ?>
                            <span class="mgr-badge mgr-approved"><i class="bx bx-check"></i> Valid</span>
                        <?php else: ?>
                            <span class="mgr-badge mgr-declined"><i class="bx bx-x"></i> Not Valid</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="font-size:.7rem;"><?php echo e($lead->ravens_validated_by ?? '—'); ?></td>
                    <td style="font-size:.7rem;"><?php echo e($lead->ravens_validated_at?->setTimezone('America/Los_Angeles')->format('M d, h:i A') ?? '—'); ?></td>
                    <td style="font-size:.68rem;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?php echo e($lead->recall_note ?? ''); ?>">
                        <?php echo e($lead->recall_note ?? '—'); ?>

                    </td>
                    <td class="text-center">
                        <button class="a-btn btn-undo-validation" data-id="<?php echo e($lead->id); ?>" data-name="<?php echo e($lead->cn_name); ?>"
                                style="font-size:.62rem;background:rgba(245,158,11,.12);color:#b45309;border-color:rgba(245,158,11,.25);padding:.15rem .5rem;">
                            <i class="bx bx-undo me-1"></i> Undo
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="modal fade" id="recallModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3" style="background:rgba(244,106,106,.06);border-bottom:1px solid rgba(244,106,106,.15);">
                <h6 class="modal-title mb-0" style="font-size:.85rem;color:#c84646;">
                    <i class="bx bx-user-x me-1"></i> Assign Back to Closer
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-1" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="recall-lead-name"></strong>
                </p>
                <p class="mb-3" style="font-size:.7rem;color:#b45309;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:.4rem;padding:.5rem .65rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    This lead will be marked as <strong>Not Valid</strong> and assigned back to the closer with your note.
                </p>
                <div class="mb-2">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">
                        Reason / Comment <span class="text-danger">*</span>
                    </label>
                    <textarea id="recall-note" class="form-control form-control-sm" rows="3"
                              placeholder="Why is this sale being sent back? e.g. Missing info, client not reached, premiums don’t match…"
                              style="font-size:.72rem;resize:none;"></textarea>
                    <div id="recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a reason.</div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="recall-confirm-btn">
                    <i class="bx bx-x-circle me-1"></i> Confirm Not Valid
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => flash.remove(), 5000);

    document.querySelectorAll('.pipe-pill-date').forEach(function(el) {
        if (window.flatpickr) flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true });
    });

    // ─── Recall Modal ───────────────────────────────────────────────────────
    let _recallLeadId = null;

    function openRecallModal(leadId, leadName) {
        _recallLeadId = leadId;
        document.getElementById('recall-lead-name').textContent = leadName;
        document.getElementById('recall-note').value = '';
        document.getElementById('recall-note-error').style.display = 'none';
        new bootstrap.Modal(document.getElementById('recallModal')).show();
    }

    function closeDetailModal(leadId) {
        const el = document.getElementById('detailModal' + leadId);
        const inst = el ? bootstrap.Modal.getInstance(el) : null;
        if (inst) inst.hide();
    }

    document.getElementById('recall-confirm-btn').addEventListener('click', function() {
        const note = document.getElementById('recall-note').value.trim();
        if (!note) {
            document.getElementById('recall-note-error').style.display = 'block';
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

        fetch(`/ravens/validation/${_recallLeadId}/keep-declined`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ recall_note: note })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('recallModal')).hide();
                location.reload();
            } else {
                alert(data.message || 'Error.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-x-circle me-1"></i> Confirm Not Valid';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-x-circle me-1"></i> Confirm Not Valid';
        });
    });

    // ─── Undo Validation ────────────────────────────────────────────────────
    document.querySelectorAll('.btn-undo-validation').forEach(btn => {
        btn.addEventListener('click', function() {
            const id   = this.dataset.id;
            const name = this.dataset.name;
            if (!confirm(`Undo validation for "${name}"? This will move the lead back to the pending queue.`)) return;

            const el = this;
            el.disabled = true;
            el.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`/ravens/validation/${id}/undo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success || data.message) { location.reload(); }
                else { alert(data.message || 'Error undoing validation.'); el.disabled = false; el.innerHTML = '<i class="bx bx-undo me-1"></i> Undo'; }
            })
            .catch(err => {
                alert('Error: ' + err.message);
                el.disabled = false;
                el.innerHTML = '<i class="bx bx-undo me-1"></i> Undo';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/validation.blade.php ENDPATH**/ ?>