<?php use \App\Support\Roles; ?>
<?php use \App\Support\Statuses; ?>


<?php $__env->startSection('title', $ticket->ticket_code . ' - ' . $ticket->subject); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.custom-select-datepicker-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    /* ── Status & Priority pills ── */
    .tk-status{display:inline-block;padding:.22rem .7rem;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.3px}
    .tk-open{background:rgba(59,130,246,.12);color:#2563eb}
    .tk-in-progress,.tk-in_progress{background:rgba(139,92,246,.12);color:#7c3aed}
    .tk-on-hold,.tk-on_hold{background:rgba(245,158,11,.12);color:#d97706}
    .tk-resolved{background:rgba(16,185,129,.12);color:#059669}
    .tk-closed{background:rgba(107,114,128,.15);color:#6b7280}
    .pr-high{color:#ef4444;font-weight:700}
    .pr-medium{color:#f59e0b;font-weight:700}
    .pr-low{color:#10b981;font-weight:700}

    /* ── Ticket hero header ── */
    .tk-hero{border-radius:14px;background:linear-gradient(135deg,rgba(184,134,11,.07) 0%,rgba(212,168,67,.03) 100%);border:1px solid rgba(184,134,11,.15);padding:1.3rem 1.5rem;margin-bottom:1.2rem;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap}
    .tk-hero-left .tk-code-big{font-size:1.1rem;font-weight:800;letter-spacing:.5px;color:#b8860b;margin-bottom:.15rem}
    .tk-hero-left .tk-subject-big{font-size:1rem;font-weight:600;color:var(--bs-body-color);margin-bottom:.3rem}
    .tk-hero-left .tk-desc-preview{font-size:.78rem;color:#9ca3af;max-width:550px}
    .tk-hero-right{text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:.4rem}

    /* ── Detail grid ── */
    .detail-row{display:flex;padding:.55rem 0;border-bottom:1px solid rgba(0,0,0,.04);font-size:.8rem}
    .detail-row:last-child{border-bottom:none}
    .detail-lbl{width:42%;color:#8c8c8c;font-weight:500}
    .detail-val{width:58%;font-weight:600;color:var(--bs-body-color)}

    /* ── Comments ── */
    .comments-wrap{max-height:420px;overflow-y:auto;border-radius:10px;background:var(--bs-surface-bg-light,#fafafa);border:1px solid rgba(0,0,0,.06);padding:.8rem}
    .cmt-item{padding:.65rem .8rem;margin-bottom:.6rem;border-left:3px solid #b8860b;background:var(--bs-card-bg,#fff);border-radius:0 8px 8px 0;font-size:.8rem}
    .cmt-meta{font-size:.7rem;color:#9ca3af;margin-bottom:.2rem}
    .cmt-meta strong{color:var(--bs-body-color)}
    .cmt-form{display:flex;gap:.5rem;margin-top:.6rem}
    .cmt-form input{flex:1;border-radius:22px;border:1px solid #e2e2e2;padding:.4rem 1rem;font-size:.8rem}
    .cmt-form input:focus{border-color:#b8860b;outline:none;box-shadow:0 0 0 3px rgba(184,134,11,.12)}
    .cmt-form button{border-radius:22px;border:none;background:linear-gradient(135deg,#b8860b,#d4a843);color:#fff;padding:.4rem 1.1rem;font-size:.78rem;font-weight:600;cursor:pointer}
    .cmt-notice{font-size:.72rem;padding:.45rem .8rem;border-radius:8px;margin-top:.5rem}

    /* ── Action buttons ── */
    .action-stack{display:flex;flex-direction:column;gap:.45rem}
    .action-stack .act-btn{width:100%;justify-content:center;font-size:.78rem}

    /* ── Approval badge ── */
    .app-badge{display:inline-block;padding:.2rem .6rem;border-radius:14px;font-size:.7rem;font-weight:600}
    .app-pending{background:rgba(245,158,11,.12);color:#d97706}
    .app-approved{background:rgba(16,185,129,.12);color:#059669}
    .app-rejected{background:rgba(239,68,68,.12);color:#ef4444}

    /* ── CRM form (modals) ── */
    .crm-label{display:block;font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#8c8c8c;margin-bottom:.35rem}
    .crm-label.required::after{content:" *";color:#ef4444}
    .crm-input{border-radius:22px;border:1px solid #e2e2e2;padding:.45rem 1rem;font-size:.82rem;width:100%;transition:border .2s,box-shadow .2s;background:var(--bs-card-bg,#fff);color:var(--bs-body-color)}
    .crm-input:focus{border-color:#b8860b;box-shadow:0 0 0 3px rgba(184,134,11,.12);outline:none}
    select.crm-input{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    textarea.crm-input{border-radius:.6rem;min-height:80px}
    .crm-prefix{display:flex;align-items:center}
    .crm-prefix .pfx{background:linear-gradient(135deg,#b8860b,#d4a843);color:#fff;border-radius:22px 0 0 22px;padding:.45rem .85rem;font-size:.78rem;font-weight:600;white-space:nowrap}
    .crm-prefix .crm-input{border-radius:0 22px 22px 0;border-left:0}

    /* ── Modal glass headers ── */
    .modal .modal-content{border-radius:14px;overflow:hidden;border:none}
    .modal .modal-header-glass{background:linear-gradient(135deg,#b8860b 0%,#d4a843 100%);padding:.85rem 1.2rem}
    .modal .modal-header-glass .modal-title{color:#fff;font-size:.88rem;font-weight:700}
    .modal .modal-header-glass .modal-title i{margin-right:.4rem}
    .modal .modal-header-glass .btn-close{filter:brightness(0) invert(1)}
    .modal .modal-body{padding:1.2rem 1.3rem}
    .modal .modal-footer{border-top:1px solid rgba(0,0,0,.06);padding:.7rem 1.3rem}
    .modal-hdr-danger{background:linear-gradient(135deg,#dc3545 0%,#e74c5e 100%) !important}
    .modal-hdr-success{background:linear-gradient(135deg,#059669 0%,#10b981 100%) !important}
    .modal-hdr-warn{background:linear-gradient(135deg,#d97706 0%,#f59e0b 100%) !important}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="tk-hero">
        <div class="tk-hero-left">
            <div class="tk-code-big"><?php echo e($ticket->ticket_code); ?></div>
            <div class="tk-subject-big"><?php echo e($ticket->subject); ?></div>
            <div class="tk-desc-preview"><?php echo e(Str::limit($ticket->description, 150)); ?></div>
        </div>
        <div class="tk-hero-right">
            <?php $slug = Str::slug($ticket->status) ?>
            <span class="tk-status tk-<?php echo e($slug); ?>"><?php echo e($ticket->status); ?></span>
            <span class="pr-<?php echo e(Str::lower($ticket->priority)); ?>" style="font-size:.82rem"><?php echo e($ticket->priority); ?> Priority</span>
        </div>
    </div>

    
    <div class="pipe-filter-bar" style="margin-bottom:1.2rem;justify-content:flex-start">
        <a href="<?php echo e(route('pabs.tickets.index')); ?>" class="act-btn a-info"><i class="bx bx-arrow-back"></i> Back</a>

        <?php if(auth()->check() && auth()->user()->canEditModule('pabs-tickets')): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->created_by === auth()->id() || auth()->user()->hasRole([Roles::SUPER_ADMIN, Roles::CEO])): ?>
            <button class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bx bx-edit"></i> Edit Ticket</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assigned_to === auth()->id() && $ticket->approval_status === Statuses::APPROVAL_PENDING): ?>
            <button class="act-btn a-success" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="bx bx-check-circle"></i> Accept</button>
            <button class="act-btn a-warn" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bx bx-x-circle"></i> Reject</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->status !== Statuses::TICKET_CLOSED): ?>
            <button class="act-btn a-danger" data-bs-toggle="modal" data-bs-target="#closeModal"><i class="bx bx-lock"></i> Close Ticket</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(auth()->check() && auth()->user()->canDeleteInModule('pabs-tickets')): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->created_by === auth()->id() || auth()->user()->hasRole([Roles::SUPER_ADMIN, Roles::CEO])): ?>
            <form action="<?php echo e(route('pabs.tickets.destroy', $ticket)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="act-btn a-danger"><i class="bx bx-trash"></i> Delete</button>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            
            <div class="sec-card" style="margin-bottom:1.2rem">
                <div class="sec-hdr"><i class="bx bx-detail" style="color:#b8860b"></i> Description</div>
                <div class="sec-body" style="font-size:.84rem;line-height:1.7;white-space:pre-line"><?php echo e($ticket->description); ?></div>
            </div>

            
            <div class="sec-card">
                <div class="sec-hdr">
                    <span><i class="bx bx-chat" style="color:#b8860b"></i> Comments & Updates</span>
                    <?php if($ticket->assigned_to === auth()->id()): ?>
                        <small style="font-size:.7rem;color:#9ca3af;margin-left:.6rem">Only you and the creator can comment</small>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="sec-body">
                    <div class="comments-wrap">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ticket->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="cmt-item">
                                <div class="cmt-meta">
                                    <strong><?php echo e($comment->user->name); ?></strong> — <?php echo e($comment->created_at->format('M d, Y H:i')); ?>

                                </div>
                                <p style="margin:0"><?php echo e($comment->comment); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p style="color:#9ca3af;margin:0;text-align:center;padding:1.5rem 0"><i class="bx bx-message-dots" style="font-size:1.4rem;display:block;margin-bottom:.3rem"></i>No comments yet.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($ticket->status, [Statuses::TICKET_CLOSED]) && ($ticket->assigned_to === auth()->id() || $ticket->created_by === auth()->id())): ?>
                        <form action="<?php echo e(route('pabs.tickets.addComment', $ticket)); ?>" method="POST" class="cmt-form">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="comment" placeholder="Add comment…" required>
                            <button type="submit"><i class="bx bx-send"></i> Post</button>
                        </form>
                    <?php elseif(!in_array($ticket->status, [Statuses::TICKET_CLOSED]) && $ticket->assigned_to === null && $ticket->created_by === auth()->id()): ?>
                        <form action="<?php echo e(route('pabs.tickets.addComment', $ticket)); ?>" method="POST" class="cmt-form">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="comment" placeholder="Add comment…" required>
                            <button type="submit"><i class="bx bx-send"></i> Post</button>
                        </form>
                    <?php elseif(in_array($ticket->status, [Statuses::TICKET_CLOSED])): ?>
                        <div class="cmt-notice" style="background:rgba(59,130,246,.08);color:#2563eb">
                            <i class="bx bx-lock"></i> Comments are closed for this ticket.
                        </div>
                    <?php elseif($ticket->assigned_to !== auth()->id() && $ticket->created_by !== auth()->id()): ?>
                        <div class="cmt-notice" style="background:rgba(245,158,11,.08);color:#d97706">
                            <i class="bx bx-shield"></i> Only the assigned user and creator can comment on this ticket.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            
            <div class="sec-card" style="margin-bottom:1.2rem">
                <div class="sec-hdr"><i class="bx bx-info-circle" style="color:#b8860b"></i> Ticket Information</div>
                <div class="sec-body" style="padding:.3rem 1.1rem">
                    <div class="detail-row">
                        <div class="detail-lbl">Code</div>
                        <div class="detail-val" style="color:#b8860b"><?php echo e($ticket->ticket_code); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Section</div>
                        <div class="detail-val"><?php echo e($sections[$ticket->section_id] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Status</div>
                        <div class="detail-val"><span class="tk-status tk-<?php echo e(Str::slug($ticket->status)); ?>"><?php echo e($ticket->status); ?></span></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Priority</div>
                        <div class="detail-val"><span class="pr-<?php echo e(Str::lower($ticket->priority)); ?>"><?php echo e($ticket->priority); ?></span></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Approval</div>
                        <div class="detail-val">
                            <?php
                                $apCls = match($ticket->approval_status) {
                                    Statuses::APPROVAL_APPROVED => 'app-approved',
                                    Statuses::APPROVAL_REJECTED => 'app-rejected',
                                    default => 'app-pending',
                                };
                            ?>
                            <span class="app-badge <?php echo e($apCls); ?>"><?php echo e($ticket->approval_status); ?></span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Quote / Amount</div>
                        <div class="detail-val">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->quote_amount): ?>
                                PKR <?php echo e(number_format($ticket->quote_amount, 2)); ?>

                            <?php else: ?>
                                <span style="color:#9ca3af">N/A</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Est. Budget</div>
                        <div class="detail-val">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->total_cost): ?>
                                PKR <?php echo e(number_format($ticket->total_cost, 2)); ?>

                            <?php else: ?>
                                <span style="color:#9ca3af">N/A</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Created</div>
                        <div class="detail-val"><?php echo e($ticket->created_at->format('M d, Y H:i')); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-lbl">Created By</div>
                        <div class="detail-val"><?php echo e($ticket->creator->name); ?></div>
                    </div>
                </div>
            </div>

            
            <div class="sec-card">
                <div class="sec-hdr"><i class="bx bx-user-check" style="color:#b8860b"></i> Assignment</div>
                <div class="sec-body" style="padding:.3rem 1.1rem">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignee): ?>
                        <div class="detail-row">
                            <div class="detail-lbl">Assigned To</div>
                            <div class="detail-val"><?php echo e($ticket->assignee->name); ?></div>
                        </div>
                    <?php else: ?>
                        <p style="color:#9ca3af;font-size:.8rem;margin:.6rem 0">Not yet assigned</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->resolved_at): ?>
                        <div class="detail-row">
                            <div class="detail-lbl">Resolved</div>
                            <div class="detail-val"><?php echo e($ticket->resolved_at->format('M d, Y H:i')); ?></div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-glass">
                <h5 class="modal-title"><i class="bx bx-edit"></i> Edit Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('pabs.tickets.update', $ticket)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div style="margin-bottom:1rem">
                        <label class="crm-label required">Subject</label>
                        <input type="text" name="subject" class="crm-input" value="<?php echo e($ticket->subject); ?>" required>
                    </div>
                    <div style="margin-bottom:1rem">
                        <label class="crm-label required">Description</label>
                        <textarea name="description" class="crm-input" rows="4" required><?php echo e($ticket->description); ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div style="margin-bottom:1rem">
                                <label class="crm-label required">Priority</label>
                                <select name="priority" class="crm-input" required>
                                    <option value="HIGH" <?php echo e($ticket->priority == Statuses::PRIORITY_HIGH ? 'selected' : ''); ?>>High</option>
                                    <option value="MEDIUM" <?php echo e($ticket->priority == Statuses::PRIORITY_MEDIUM ? 'selected' : ''); ?>>Medium</option>
                                    <option value="LOW" <?php echo e($ticket->priority == Statuses::PRIORITY_LOW ? 'selected' : ''); ?>>Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="margin-bottom:1rem">
                                <label class="crm-label required">Status</label>
                                <select name="status" class="crm-input" required>
                                    <option value="OPEN" <?php echo e($ticket->status == Statuses::TICKET_OPEN ? 'selected' : ''); ?>>Open</option>
                                    <option value="IN PROGRESS" <?php echo e($ticket->status == Statuses::TICKET_IN_PROGRESS ? 'selected' : ''); ?>>In Progress</option>
                                    <option value="ON HOLD" <?php echo e($ticket->status == Statuses::TICKET_ON_HOLD ? 'selected' : ''); ?>>On Hold</option>
                                    <option value="RESOLVED" <?php echo e($ticket->status == Statuses::TICKET_RESOLVED ? 'selected' : ''); ?>>Resolved</option>
                                    <option value="CLOSED" <?php echo e($ticket->status == Statuses::TICKET_CLOSED ? 'selected' : ''); ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom:1rem">
                        <label class="crm-label">Assign To</label>
                        <select name="assigned_to" class="crm-input">
                            <option value="">— Unassigned —</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e($ticket->assigned_to == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div style="margin-bottom:1rem">
                                <label class="crm-label">Estimated Budget</label>
                                <div class="crm-prefix">
                                    <span class="pfx">PKR</span>
                                    <input type="number" name="total_cost" class="crm-input" placeholder="0.00" step="0.01" min="0" value="<?php echo e($ticket->total_cost ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="margin-bottom:1rem">
                                <label class="crm-label">Quote / Amount</label>
                                <div class="crm-prefix">
                                    <span class="pfx">PKR</span>
                                    <input type="number" name="quote_amount" class="crm-input" placeholder="0.00" step="0.01" min="0" value="<?php echo e($ticket->quote_amount ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="act-btn a-warn" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                    <button type="submit" class="act-btn a-primary"><i class="bx bx-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-glass modal-hdr-danger">
                <h5 class="modal-title"><i class="bx bx-lock"></i> Close Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('pabs.tickets.close', $ticket)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div style="text-align:center;padding:.8rem 0">
                        <i class="bx bx-error-circle" style="font-size:2.5rem;color:#ef4444;display:block;margin-bottom:.5rem"></i>
                        <p style="font-size:.88rem;margin:0">Are you sure you want to close this ticket?<br><small style="color:#9ca3af">It will no longer be editable.</small></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="act-btn a-warn" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                    <button type="submit" class="act-btn a-danger"><i class="bx bx-lock"></i> Close Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-glass modal-hdr-success">
                <h5 class="modal-title"><i class="bx bx-check-circle"></i> Accept Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('pabs.tickets.approve', $ticket)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <p style="font-size:.84rem;margin-bottom:1rem">Are you sure you want to accept this ticket?</p>
                    <div>
                        <label class="crm-label required">Approval Notes</label>
                        <textarea name="approval_notes" class="crm-input" rows="3" placeholder="Add any notes about accepting this ticket…" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="act-btn a-warn" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                    <button type="submit" class="act-btn a-success"><i class="bx bx-check"></i> Accept Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-glass modal-hdr-warn">
                <h5 class="modal-title"><i class="bx bx-x-circle"></i> Reject Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('pabs.tickets.reject', $ticket)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <p style="font-size:.84rem;margin-bottom:1rem">Are you sure you want to reject this ticket? You will be unassigned and the ticket will be reopened.</p>
                    <div>
                        <label class="crm-label required">Reason for Rejection</label>
                        <textarea name="approval_notes" class="crm-input" rows="3" placeholder="Please provide a reason for rejecting this ticket…" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="act-btn a-primary" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cancel</button>
                    <button type="submit" class="act-btn a-warn"><i class="bx bx-block"></i> Reject Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
<script>
$(function(){
    $('select.crm-input').select2({minimumResultsForSearch:10,width:'100%'});
    $(document).on('shown.bs.modal',function(e){
        var m=$(e.target);
        m.find('select.crm-input').each(function(){
            if(!$(this).data('select2'))$(this).select2({minimumResultsForSearch:10,width:'100%',dropdownParent:m});
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/pabs/tickets/show.blade.php ENDPATH**/ ?>