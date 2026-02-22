<?php $__env->startSection('title'); ?>
    View Account - <?php echo e($account->account_name); ?>

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

        .detail-label {
            color: var(--bs-surface-400);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: var(--bs-surface-300);
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .balance-display {
            font-size: 2rem;
            font-weight: 700;
            color: var(--bs-gold);
        }

        .section-header {
            color: var(--bs-gold);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .gold-gradient-btn {
            background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%);
            border: none;
            color: var(--bs-surface-900);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .gold-gradient-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
            color: var(--bs-surface-900);
        }

        .btn-secondary-custom {
            background: rgba(100, 116, 139, 0.3);
            border: 1px solid rgba(100, 116, 139, 0.5);
            color: var(--bs-surface-300);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
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

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background: linear-gradient(135deg, var(--bs-ui-success) 0%, var(--bs-ui-success-dark) 100%);
            color: var(--bs-white);
        }

        .status-inactive {
            background: linear-gradient(135deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%);
            color: var(--bs-white);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Chart of Accounts
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            View Account
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="bx bx-book-bookmark"></i>
                <?php echo e($account->account_code); ?> - <?php echo e($account->account_name); ?>

            </h2>

            <div class="glassmorphism-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="section-header mb-0">
                            <i class="bx bx-info-circle"></i>
                            Account Details
                        </h5>
                        <div>
                            <?php if(auth()->check() && auth()->user()->canEditModule('chart-of-accounts')): ?>
                            <a href="<?php echo e(route('chart-of-accounts.edit', $account->id)); ?>" class="btn gold-gradient-btn me-2">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('chart-of-accounts.index')); ?>" class="btn btn-secondary-custom">
                                <i class="bx bx-arrow-back"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Account Code</div>
                            <div class="detail-value"><?php echo e($account->account_code); ?></div>

                            <div class="detail-label">Account Name</div>
                            <div class="detail-value"><?php echo e($account->account_name); ?></div>

                            <div class="detail-label">Account Type</div>
                            <div class="detail-value"><?php echo e($account->account_type); ?></div>

                            <div class="detail-label">Account Category</div>
                            <div class="detail-value"><?php echo e($account->account_category ?? 'N/A'); ?></div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-label">Parent Account</div>
                            <div class="detail-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->parentAccount): ?>
 <a class="text-gold" href="<?php echo e(route('chart-of-accounts.show', $account->parentAccount->id)); ?>" >
                                        <?php echo e($account->parentAccount->account_code); ?> - <?php echo e($account->parentAccount->account_name); ?>

                                    </a>
                                <?php else: ?>
                                    Top Level Account
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="detail-label">Opening Balance</div>
                            <div class="detail-value">$<?php echo e(number_format($account->opening_balance, 2)); ?></div>

                            <div class="detail-label">Current Balance</div>
                            <div class="detail-value">
                                <span class="balance-display">$<?php echo e(number_format($account->current_balance, 2)); ?></span>
                            </div>

                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge <?php echo e($account->is_active ? 'status-active' : 'status-inactive'); ?>">
                                    <?php echo e($account->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->description): ?>
                            <div class="col-md-12">
                                <div class="detail-label">Description</div>
                                <div class="detail-value"><?php echo e($account->description); ?></div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->childAccounts->count() > 0): ?>
                <div class="glassmorphism-card">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="bx bx-folder-open"></i>
                            Sub-Accounts (<?php echo e($account->childAccounts->count()); ?>)
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-dark-custom table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th>Type</th>
                                        <th>Current Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $account->childAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($child->account_code); ?></td>
                                            <td><?php echo e($child->account_name); ?></td>
                                            <td><?php echo e($child->account_type); ?></td>
                                            <td>$<?php echo e(number_format($child->current_balance, 2)); ?></td>
                                            <td>
                                                <span class="badge <?php echo e($child->is_active ? 'bg-success' : 'bg-danger'); ?>">
                                                    <?php echo e($child->is_active ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('chart-of-accounts.show', $child->id)); ?>" class="btn btn-sm btn-info">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/chart-of-accounts/show.blade.php ENDPATH**/ ?>