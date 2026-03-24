
<?php
    $acctRoute = Route::currentRouteName() ?? '';
?>

<div class="acct-subnav">
    <div class="acct-subnav-inner">
        
        <div class="acct-subnav-brand">
            <i class="bx bx-book-open"></i>
            <span>Accounting</span>
        </div>

        
        <nav class="acct-subnav-links">
            <a href="<?php echo e(route('admin.accounting.dashboard')); ?>"
               class="acct-nav-link <?php echo e(Str::startsWith($acctRoute,'admin.accounting.dashboard') ? 'active' : ''); ?>">
                <i class="bx bx-home-alt-2"></i> Overview
            </a>
            <a href="<?php echo e(route('admin.accounting.sales-ledger')); ?>"
               class="acct-nav-link <?php echo e(Str::startsWith($acctRoute,'admin.accounting.sales-ledger') ? 'active' : ''); ?>">
                <i class="bx bx-trending-up"></i> Sales Ledger
            </a>
            <a href="<?php echo e(route('admin.accounting.journal.index')); ?>"
               class="acct-nav-link <?php echo e($acctRoute === 'admin.accounting.journal.index' ? 'active' : ''); ?>">
                <i class="bx bx-list-ul"></i> Journal
            </a>
            <a href="<?php echo e(route('admin.accounting.partner-ledger')); ?>"
               class="acct-nav-link <?php echo e(Str::startsWith($acctRoute,'admin.accounting.partner-ledger') ? 'active' : ''); ?>">
                <i class="bx bx-user-circle"></i> Partner Ledger
            </a>
        </nav>

        
        <?php if(auth()->check() && auth()->user()->canEditModule('accounting')): ?>
        <div class="acct-subnav-actions">
            <div class="dropdown">
                <button class="acct-btn-new dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bx bx-plus"></i> New Entry
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="font-size:.84rem;min-width:200px">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.record-sale')); ?>">
                            <i class="bx bx-purchase-tag text-success"></i> Record Sale
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.record-payment')); ?>">
                            <i class="bx bx-money text-primary"></i> Payment Received
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.record-chargeback')); ?>">
                            <i class="bx bx-undo" style="color:#dc2626"></i> ChargeBack / Return
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.opening-balance')); ?>">
                            <i class="bx bx-reset text-warning"></i> Opening Balance
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.journal.create')); ?>">
                            <i class="bx bx-edit text-secondary"></i> General Journal Entry
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.acct-subnav {
    background: #1e1e2e;
    border-bottom: 2px solid #d4af37;
    position: sticky;
    top: 0;
    z-index: 100;
    margin-bottom: 0;
}
.acct-subnav-inner {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 0 20px;
    height: 48px;
}
.acct-subnav-brand {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .85rem;
    font-weight: 700;
    color: #d4af37;
    letter-spacing: .04em;
    padding-right: 20px;
    border-right: 1px solid rgba(255,255,255,.1);
    margin-right: 8px;
    white-space: nowrap;
}
.acct-subnav-brand i { font-size: 1.1rem; }
.acct-subnav-links {
    display: flex;
    align-items: center;
    gap: 0;
    flex: 1;
}
.acct-nav-link {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 0 14px;
    height: 48px;
    font-size: .8rem;
    font-weight: 600;
    color: #aaa;
    text-decoration: none;
    letter-spacing: .02em;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: color .15s, border-color .15s;
    white-space: nowrap;
}
.acct-nav-link i { font-size: .95rem; }
.acct-nav-link:hover { color: #fff; border-bottom-color: rgba(212,175,55,.4); }
.acct-nav-link.active { color: #d4af37; border-bottom-color: #d4af37; }
.acct-subnav-actions { margin-left: auto; }
.acct-btn-new {
    background: #d4af37;
    border: none;
    color: #1a1a1a;
    font-weight: 700;
    font-size: .78rem;
    padding: 6px 14px;
    border-radius: 4px;
    letter-spacing: .03em;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: background .15s;
}
.acct-btn-new:hover { background: #b8941f; }
.acct-btn-new.dropdown-toggle::after { margin-left: 4px; }
</style>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/_nav.blade.php ENDPATH**/ ?>