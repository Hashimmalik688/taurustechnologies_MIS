<?php $__env->startSection('title', 'View Account — ' . $account->account_name); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .form-page-hdr p{margin:2px 0 0;font-size:.72rem;color:var(--bs-surface-500)}
    .detail-lbl{font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.15rem}
    .detail-val{font-size:.82rem;font-weight:600;color:var(--bs-body-color);margin-bottom:.85rem}
    .balance-hero{font-size:1.55rem;font-weight:800;background:linear-gradient(135deg,#d4af37,#f0d878);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-book-bookmark"></i> <?php echo e($account->account_code); ?> — <?php echo e($account->account_name); ?></h4>
            <p>Account details & sub-accounts</p>
        </div>
        <div class="d-flex gap-2">
            <?php if(auth()->check() && auth()->user()->canEditModule('chart-of-accounts')): ?>
            <a href="<?php echo e(route('chart-of-accounts.edit', $account->id)); ?>" class="act-btn a-primary"><i class="bx bx-edit"></i> Edit</a>
            <?php endif; ?>
            <a href="<?php echo e(route('chart-of-accounts.index')); ?>" class="act-btn a-info"><i class="bx bx-arrow-back"></i> Back</a>
        </div>
    </div>

    
    <div class="kpi-row" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr))">
        <div class="kpi-card k-gold">
            <div class="kpi-lbl">Current Balance</div>
            <div class="kpi-val">$<?php echo e(number_format($account->current_balance, 2)); ?></div>
        </div>
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Opening Balance</div>
            <div class="kpi-val">$<?php echo e(number_format($account->opening_balance, 2)); ?></div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Status</div>
            <div class="kpi-val">
                <span class="s-pill <?php echo e($account->is_active ? 's-active' : 's-closed'); ?>"><?php echo e($account->is_active ? 'Active' : 'Inactive'); ?></span>
            </div>
        </div>
        <div class="kpi-card k-purple">
            <div class="kpi-lbl">Sub-Accounts</div>
            <div class="kpi-val"><?php echo e($account->childAccounts->count()); ?></div>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="sec-hdr"><i class="bx bx-info-circle"></i> Account Information</div>
        <div class="sec-body">
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Account Code</div>
                    <div class="detail-val"><?php echo e($account->account_code); ?></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Account Name</div>
                    <div class="detail-val"><?php echo e($account->account_name); ?></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Account Type</div>
                    <div class="detail-val"><span class="v-badge"><?php echo e($account->account_type); ?></span></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Category</div>
                    <div class="detail-val"><?php echo e($account->account_category ?? '—'); ?></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Parent Account</div>
                    <div class="detail-val">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->parentAccount): ?>
                            <a href="<?php echo e(route('chart-of-accounts.show', $account->parentAccount->id)); ?>" style="color:#d4af37;text-decoration:none;font-weight:600">
                                <?php echo e($account->parentAccount->account_code); ?> — <?php echo e($account->parentAccount->account_name); ?>

                            </a>
                        <?php else: ?>
                            <span style="opacity:.55">Top Level Account</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Current Balance</div>
                    <div class="detail-val"><span class="balance-hero">$<?php echo e(number_format($account->current_balance, 2)); ?></span></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->description): ?>
                <div class="col-12">
                    <div class="detail-lbl">Description</div>
                    <div class="detail-val" style="color:var(--bs-surface-400)"><?php echo e($account->description); ?></div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->childAccounts->count() > 0): ?>
    <div class="ex-card sec-card mt-2">
        <div class="sec-hdr"><i class="bx bx-folder-open"></i> Sub-Accounts (<?php echo e($account->childAccounts->count()); ?>)</div>
        <div class="sec-body p-0">
            <div class="table-responsive">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th style="width:60px">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $account->childAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($child->account_code); ?></strong></td>
                            <td><?php echo e($child->account_name); ?></td>
                            <td><span class="v-badge"><?php echo e($child->account_type); ?></span></td>
                            <td>$<?php echo e(number_format($child->current_balance, 2)); ?></td>
                            <td><span class="s-pill <?php echo e($child->is_active ? 's-active' : 's-closed'); ?>"><?php echo e($child->is_active ? 'Active' : 'Inactive'); ?></span></td>
                            <td><a href="<?php echo e(route('chart-of-accounts.show', $child->id)); ?>" class="act-btn a-info" style="padding:.18rem .55rem"><i class="bx bx-show"></i></a></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/chart-of-accounts/show.blade.php ENDPATH**/ ?>