<?php $__env->startSection('title'); ?>
    Transaction Details
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .page-header {
            color: var(--bs-gold);
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .transaction-header {
            text-align: center;
            padding: 2rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.2);
        }

        .transaction-amount {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .amount-credit { color: var(--bs-ui-success); }
        .amount-debit { color: var(--bs-ui-danger); }

        .transaction-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            color: var(--bs-surface-400);
            font-size: 0.875rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            padding: 2rem;
        }

        .info-item {
            padding: 1rem;
            background: rgba(15, 23, 42, 0.4);
            border-radius: 8px;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .info-label {
            color: var(--bs-surface-400);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: var(--bs-surface-300);
            font-size: 1.1rem;
            font-weight: 600;
        }

        .timeline {
            position: relative;
            padding: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 2.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--bs-gold) 0%, rgba(212, 175, 55, 0.2) 100%);
        }

        .timeline-item {
            position: relative;
            padding-left: 3rem;
            padding-bottom: 2rem;
        }

        .timeline-icon {
            position: absolute;
            left: 1.75rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--bs-gold);
            border: 3px solid var(--bs-surface-800);
        }

        .timeline-content {
            background: rgba(15, 23, 42, 0.4);
            padding: 1rem;
            border-radius: 8px;
            border-left: 3px solid var(--bs-gold);
        }

        .timeline-time {
            color: var(--bs-surface-400);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .timeline-text {
            color: var(--bs-surface-300);
        }

        .badge-credit {
            background: linear-gradient(135deg, var(--bs-ui-success) 0%, var(--bs-ui-success-dark) 100%);
            color: var(--bs-white);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge-debit {
            background: linear-gradient(135deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%);
            color: var(--bs-white);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .action-btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--bs-ui-success) 0%, var(--bs-ui-success) 100%);
            color: var(--bs-white);
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%);
            color: var(--bs-white);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Ledger
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Transaction Details
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-file-document-outline"></i>
                Transaction Details
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="glassmorphism-card mb-4">
                <div class="transaction-header">
                    <div class="transaction-amount amount-credit">+$1,250.00</div>
                    <span class="badge-credit">CREDIT</span>
                    <div class="transaction-meta mt-3">
                        <span><i class="mdi mdi-calendar me-2"></i>September 28, 2025</span>
                        <span><i class="mdi mdi-pound me-2"></i>INV-001234</span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Category</div>
                        <div class="info-value">Commission</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Reference Number</div>
                        <div class="info-value">INV-001234</div>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <div class="info-label">Description</div>
                        <div class="info-value">Policy sale commission for large account. Client purchased comprehensive life insurance policy with $500,000 coverage.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Related Lead</div>
                        <div class="info-value">
 <a class="text-gold" href="#" >Lead #1234 - John Doe</a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Created By</div>
                        <div class="info-value">Admin User</div>
                    </div>
                </div>

                <div class="p-3 text-center" style="border-top: 1px solid rgba(212, 175, 55, 0.2);">
                    <?php if(auth()->check() && auth()->user()->canEditModule('general-ledger')): ?>
                    <button class="action-btn btn-edit">
                        <i class="mdi mdi-pencil me-1"></i>Edit Transaction
                    </button>
                    <?php endif; ?>
                    <?php if(auth()->check() && auth()->user()->canDeleteInModule('general-ledger')): ?>
                    <button class="action-btn btn-delete">
                        <i class="mdi mdi-delete me-1"></i>Delete Transaction
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glassmorphism-card">
                <div class="card-body">
 <h5 class="text-gold" style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3)">
                        <i class="mdi mdi-timeline-clock me-2"></i>Transaction Timeline
                    </h5>

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">September 28, 2025 - 10:30 AM</div>
                                <div class="timeline-text"><strong>Transaction Created</strong><br>Created by Admin User</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">September 28, 2025 - 10:35 AM</div>
                                <div class="timeline-text"><strong>Notification Sent</strong><br>Email notification sent</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">September 28, 2025 - 2:15 PM</div>
                                <div class="timeline-text"><strong>Transaction Verified</strong><br>Verified by Manager</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/ledger/show.blade.php ENDPATH**/ ?>