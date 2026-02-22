<?php $__env->startSection('title'); ?>
    Finance & Accounts
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('components.hub-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-dollar-circle"></i>Finance & Accounts</h4>
            <p>Manage accounting, payroll, ledgers, and financial operations</p>
        </div>

        <div class="hub-section-label">Accounting</div>
        <div class="hub-grid">
            <?php if(auth()->check() && auth()->user()->canViewModule('chart-of-accounts')): ?>
            <a href="<?php echo e(route('chart-of-accounts.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-list-ul"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Chart of Accounts</div>
                    <p class="hub-card-desc">Manage account categories, codes, and the organizational chart of accounts</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('general-ledger')): ?>
            <a href="<?php echo e(route('ledger.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-book-open"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">General Ledger</div>
                    <p class="hub-card-desc">View and manage all financial transactions, journal entries, and account balances</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['Super Admin', 'Co-ordinator', 'CEO'])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('petty-cash')): ?>
                <a href="<?php echo e(route('petty-cash.index')); ?>" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-wallet"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Petty Cash</div>
                        <p class="hub-card-desc">Track small cash expenditures, reimbursements, and petty cash fund balances</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="hub-section-label">Payroll & Tickets</div>
        <div class="hub-grid">
            <?php if(auth()->check() && auth()->user()->canViewModule('payroll')): ?>
            <a href="<?php echo e(route('payroll.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-credit-card-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Payroll</div>
                    <p class="hub-card-desc">Process employee salaries, generate payslips, and manage payroll periods</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('pabs-tickets')): ?>
            <a href="<?php echo e(route('pabs.tickets.index')); ?>" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-message-square-error"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">PABS Tickets</div>
                    <p class="hub-card-desc">Manage payment and billing support tickets and resolve financial inquiries</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/finance/hub.blade.php ENDPATH**/ ?>