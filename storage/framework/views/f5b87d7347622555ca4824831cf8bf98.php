<?php $__env->startSection('title', 'Partner Ledger'); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --acct-gold:       #d4af37;
    --acct-gold-dark:  #b8941f;
    --acct-gold-light: #f5ecd0;
    --acct-dark:       #1a1a1a;
    --acct-header-bg:  #2d2d2d;
}
.pl-module-header {
    background: var(--acct-header-bg);
    border-bottom: 3px solid var(--acct-gold);
    border-radius: 6px;
    padding: 16px 22px;
    margin-bottom: 22px;
}
.pl-module-header .pl-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--acct-gold);
    display: flex; align-items: center; gap: 8px;
    margin: 0 0 3px;
}
.pl-module-header .pl-sub { font-size: .78rem; color: #aaa; margin: 0; }

/* Search / select card */
.pl-select-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: 3px solid var(--acct-gold);
    border-radius: 0 0 6px 6px;
    padding: 24px 28px 28px;
    max-width: 560px;
    margin: 0 auto;
}
.pl-select-card .pl-form-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 6px;
    display: block;
}
.pl-select-card select.form-select {
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: .9rem;
    padding: 8px 12px;
    height: auto;
    background-color: #fafafa;
}
.pl-select-card select.form-select:focus {
    border-color: var(--acct-gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,.2);
    outline: none;
}
.btn-pl-view {
    background: var(--acct-gold);
    border: none;
    color: #1a1a1a;
    font-weight: 700;
    font-size: .85rem;
    padding: 8px 24px;
    border-radius: 4px;
    letter-spacing: .02em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background .15s;
}
.btn-pl-view:hover { background: var(--acct-gold-dark); color: #fff; }

/* Partner grid cards */
.pl-partner-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 24px;
}
.pl-partner-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    padding: 12px 14px;
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    position: relative;
    overflow: hidden;
}
.pl-partner-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: transparent;
    transition: background .15s;
}
.pl-partner-card:hover { border-color: var(--acct-gold); box-shadow: 0 2px 8px rgba(212,175,55,.2); transform: translateY(-1px); }
.pl-partner-card:hover::before { background: var(--acct-gold); }
.pl-partner-card .pc-name { font-size: .88rem; font-weight: 600; color: #2d2d2d; }
.pl-partner-card .pc-code { font-size: .74rem; font-family: 'Courier New', monospace; color: #aaa; margin-top: 2px; }
.pl-partner-card .pc-arrow { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #ccc; font-size: 1.1rem; transition: color .15s; }
.pl-partner-card:hover .pc-arrow { color: var(--acct-gold); }
.pl-search-box {
    position: relative;
    max-width: 300px;
}
.pl-search-box input {
    padding-left: 32px;
    font-size: .85rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    height: 34px;
}
.pl-search-box input:focus { border-color: var(--acct-gold); box-shadow: 0 0 0 3px rgba(212,175,55,.15); outline: none; }
.pl-search-box .search-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 1rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="container-fluid">

    
    <div class="pl-module-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="pl-title">
                    <i class="bx bx-user-account"></i>
                    Partner Account Ledger
                </div>
                <p class="pl-sub">Select a partner to view their full account statement</p>
            </div>
            <a href="<?php echo e(route('admin.accounting.journal.index')); ?>"
               class="btn btn-sm" style="font-size:.8rem;color:#ccc;border:1px solid #555;border-radius:4px;padding:5px 12px;">
                <i class="bx bx-arrow-back me-1"></i> Back to Journal
            </a>
        </div>
    </div>

    
    <div class="pl-select-card">
        <form method="GET" action="#" id="partnerSelectForm">
            <label class="pl-form-label">
                <i class="bx bx-search me-1" style="color:var(--acct-gold);"></i>
                Select a Partner Account
            </label>
            <select name="partner_id" id="partnerSelect" class="form-select mb-4" required>
                <option value="">— Choose Partner —</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($partner->id); ?>">
                        <?php echo e($partner->name); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partner->code): ?>  ·  <?php echo e($partner->code); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <button type="submit" class="btn-pl-view">
                <i class="bx bx-show-alt"></i> View Statement
            </button>
        </form>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partners->count() > 0): ?>
    <div style="max-width:860px;margin:0 auto;">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
            <span style="font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#888;">
                All Partners (<?php echo e($partners->count()); ?>)
            </span>
            <div class="pl-search-box">
                <i class="bx bx-search search-icon"></i>
                <input type="text" id="partnerSearch" class="form-control" placeholder="Search partner…">
            </div>
        </div>
        <div class="pl-partner-grid" id="partnerGrid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="pl-partner-card"
                 data-name="<?php echo e(strtolower($partner->name)); ?>"
                 data-code="<?php echo e(strtolower($partner->code ?? '')); ?>"
                 onclick="window.location.href='<?php echo e(route('admin.accounting.partner-ledger.show', $partner->id)); ?>'">
                <div class="pc-name"><?php echo e($partner->name); ?></div>
                <div class="pc-code"><?php echo e($partner->code ?? 'No Code'); ?></div>
                <i class="bx bx-chevron-right pc-arrow"></i>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div id="noPartnerMsg" class="text-center text-muted py-4 d-none" style="font-size:.85rem;">
            No partners match your search.
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
// Dropdown select → redirect
document.getElementById('partnerSelectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var id = document.getElementById('partnerSelect').value;
    if (id) {
        window.location.href = '<?php echo e(route("admin.accounting.partner-ledger.show", ["partnerId" => "__ID__"])); ?>'.replace('__ID__', id);
    }
});

// Grid search
document.getElementById('partnerSearch')?.addEventListener('input', function() {
    var q     = this.value.toLowerCase().trim();
    var cards = document.querySelectorAll('.pl-partner-card');
    var shown = 0;
    cards.forEach(function(card) {
        var match = card.dataset.name.includes(q) || card.dataset.code.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) shown++;
    });
    document.getElementById('noPartnerMsg').classList.toggle('d-none', shown > 0);

});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/partner-ledger/index.blade.php ENDPATH**/ ?>