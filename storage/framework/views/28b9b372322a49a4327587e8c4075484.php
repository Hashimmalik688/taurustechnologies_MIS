<?php $__env->startSection('title'); ?>
    Duplicate Leads
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* Shared design system from index_simple */
    .sl-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; flex-wrap: wrap; gap: .75rem;
    }
    .sl-topbar-left { display: flex; align-items: center; gap: .75rem; }
    .sl-page-title {
        font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-page-title i { color: #d4af37; font-size: 1.2rem; }
    .sl-topbar-right { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

    .sl-search-wrap { position: relative; display: flex; align-items: center; }
    .sl-search-icon { position: absolute; left: .6rem; color: #94a3b8; font-size: .9rem; pointer-events: none; }
    .sl-search-input {
        padding: .42rem .65rem .42rem 2rem;
        font-size: .78rem; border: 1px solid rgba(0,0,0,.1);
        border-radius: 22px; background: #fff; width: 220px;
        outline: none; transition: border-color .15s;
    }
    .sl-search-input:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    .sl-btn {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .42rem .8rem; font-size: .75rem; font-weight: 700;
        border-radius: 22px; border: none; cursor: pointer;
        transition: all .15s; white-space: nowrap; text-decoration: none;
    }
    .sl-btn-outline {
        background: transparent; border: 1px solid rgba(0,0,0,.12); color: #475569;
    }
    .sl-btn-outline:hover { border-color: #d4af37; color: #d4af37; }

    .sl-card {
        background: rgba(255,255,255,.9); backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06); border-radius: 16px; overflow: hidden;
    }
    .sl-filter-pills {
        display: flex; align-items: center; gap: .4rem;
        padding: .6rem 1rem; border-bottom: 1px solid rgba(0,0,0,.05);
        background: rgba(248,250,252,.6); flex-wrap: wrap;
    }
    .sl-pill-clear {
        font-size: .68rem; font-weight: 600; color: #ef4444; text-decoration: none;
        padding: .25rem .5rem; border-radius: 22px; border: 1px solid rgba(239,68,68,.2);
        display: inline-flex; align-items: center; gap: 2px; transition: all .15s;
    }
    .sl-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }
    .sl-result-count { font-size: .72rem; font-weight: 600; color: #94a3b8; margin-left: auto; }
    .sl-search-pill {
        font-size: .72rem; font-weight: 600; color: #475569; padding: .32rem .75rem;
        border-radius: 22px; border: 1px solid rgba(0,0,0,.08);
        background: #fff; display: inline-flex; align-items: center; gap: .35rem;
    }

    .sl-tbl-wrap {
        overflow-x: auto; overflow-y: auto; max-height: 580px;
        scrollbar-width: thin; scrollbar-color: #d4af37 transparent;
    }
    .sl-tbl-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
    .sl-tbl-wrap::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 3px; }
    .sl-tbl {
        width: 100%; border-collapse: separate; border-spacing: 0; font-size: .78rem;
    }
    .sl-tbl thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #64748b; padding: .45rem .6rem;
        border-bottom: 1px solid rgba(212,175,55,.18);
        white-space: nowrap; position: sticky; top: 0; z-index: 10;
    }
    .sl-tbl tbody td {
        padding: .38rem .6rem; border-bottom: 1px solid rgba(0,0,0,.04);
        vertical-align: middle; color: #334155;
    }
    .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.04); }
    .sl-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.45); }
    .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.04); }

    /* Duplicate-specific styles */
    .dup-badge {
        font-size: .64rem; font-weight: 700; padding: .18rem .45rem;
        border-radius: 22px; background: rgba(239,68,68,.1); color: #dc2626;
        display: inline-flex; align-items: center; gap: 3px;
    }
    .dup-newer-link {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .72rem; font-weight: 700; color: #2563eb; text-decoration: none;
        padding: .2rem .5rem; border-radius: 22px;
        border: 1px solid rgba(37,99,235,.2); transition: all .15s;
    }
    .dup-newer-link:hover { background: rgba(37,99,235,.07); color: #1d4ed8; border-color: rgba(37,99,235,.4); }
    .dup-phone { font-family: monospace; font-size: .76rem; color: #475569; }
    .dup-grp-sep td {
        background: rgba(212,175,55,.06) !important;
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #94a3b8; padding: .25rem .6rem;
        border-top: 1px solid rgba(212,175,55,.15);
    }

    /* View Tabs */
    .sl-view-tabs {
        display: flex; gap: .35rem; margin-bottom: .75rem;
        border-bottom: 2px solid rgba(212,175,55,.15); padding-bottom: 0;
    }
    .sl-view-tab {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .45rem .85rem; font-size: .76rem; font-weight: 700;
        border-radius: 10px 10px 0 0; border: 1px solid transparent;
        border-bottom: none; text-decoration: none; color: #64748b;
        background: transparent; transition: all .15s;
        position: relative; bottom: -2px;
    }
    .sl-view-tab:hover { color: #d4af37; background: rgba(212,175,55,.07); }
    .sl-view-tab.active {
        color: #0f172a; background: #fff;
        border-color: rgba(212,175,55,.25); border-bottom-color: #fff;
    }
    .sl-tab-count {
        font-size: .65rem; font-weight: 800; padding: .1rem .38rem;
        border-radius: 22px; background: rgba(100,116,139,.12); color: #64748b;
    }
    .sl-tab-count.sl-tab-warn { background: rgba(239,68,68,.1); color: #dc2626; }

    /* Pagination */
    .sl-card .mt-3 { padding: 0 1rem .75rem; }
    .sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

    /* Dark mode */
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: var(--text-primary, #f1f5f9); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input { background: var(--bg-primary, #1a1a1a); border-color: var(--border-color, #333); color: var(--text-primary, #e2e8f0); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card { background: var(--bg-card, #1f1f1f); border-color: var(--border-color, #333); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills { background: var(--bg-tertiary, #2d2d2d); border-color: var(--border-color, #333); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th { background: var(--bg-tertiary, #2d2d2d) !important; color: var(--text-secondary, #94a3b8); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td { color: var(--text-primary, #cbd5e1); border-color: rgba(255,255,255,.04); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover td { background: var(--bg-tertiary, #2d2d2d); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-view-tab.active { background: var(--bg-card, #1f1f1f); color: var(--text-primary, #f1f5f9); border-color: var(--border-color, #333); border-bottom-color: var(--bg-card, #1f1f1f); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-view-tab { color: var(--text-muted, #64748b); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-view-tabs { border-bottom-color: var(--border-color, rgba(212,175,55,.12)); }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-pill { background: var(--bg-primary); border-color: var(--border-color, #333); color: var(--text-secondary); }

    @media (max-width: 768px) {
        .sl-topbar { flex-direction: column; align-items: flex-start; }
        .sl-topbar-right { width: 100%; }
        .sl-search-input { width: 100% !important; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- View Tabs -->
    <div class="sl-view-tabs">
        <a href="<?php echo e(route('leads.index')); ?>" class="sl-view-tab">
            <i class="bx bx-data"></i> Raven Leads
        </a>
        <a href="<?php echo e(route('leads.duplicates')); ?>" class="sl-view-tab active">
            <i class="bx bx-copy-alt"></i> Duplicates
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($duplicateCount > 0): ?>
                <span class="sl-tab-count sl-tab-warn"><?php echo e($duplicateCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
    </div>

    <!-- Top bar -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title">
                <i class="bx bx-copy-alt"></i> Duplicate Leads
            </h5>
            <small class="text-muted" style="font-size:.72rem">
                Older entries hidden from the main view — the latest record per phone number is shown there.
            </small>
        </div>
        <div class="sl-topbar-right">
            <div class="sl-search-wrap">
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" id="dupSearch" class="sl-search-input" placeholder="Search name, phone, SSN..." value="<?php echo e(request('search')); ?>">
            </div>
            <a href="<?php echo e(route('leads.index')); ?>" class="sl-btn sl-btn-outline">
                <i class="bx bx-arrow-back"></i> Back to Leads
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="sl-card">
        <!-- Filter row -->
        <form method="GET" action="<?php echo e(route('leads.duplicates')); ?>" id="dupFilterForm" class="sl-filter-pills">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('search')): ?>
                <span class="sl-search-pill"><i class="bx bx-search" style="font-size:.75rem"></i><?php echo e(request('search')); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search'])): ?>
                <a href="<?php echo e(route('leads.duplicates')); ?>" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="sl-result-count"><?php echo e($duplicates->total()); ?> duplicate records</span>
        </form>

        <!-- Table -->
        <div class="sl-tbl-wrap">
            <table class="sl-tbl">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Duplicate Record</th>
                        <th>Phone Number</th>
                        <th>Status</th>
                        <th>Carrier</th>
                        <th>Closer</th>
                        <th>Imported / Created</th>
                        <th>Current Record (Newer)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $prevPhone = null; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $duplicates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->phone_number !== $prevPhone): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prevPhone !== null): ?>
                                <tr class="dup-grp-sep"><td colspan="8">— Next duplicate group —</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php $prevPhone = $lead->phone_number; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <tr>
                            <td><strong><?php echo e($duplicates->firstItem() + $index); ?></strong></td>
                            <td>
                                <div style="font-weight:600;font-size:.78rem"><?php echo e($lead->cn_name ?? 'N/A'); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->ssn): ?>
                                    <div style="font-size:.68rem;color:#94a3b8">SSN: ***-**-<?php echo e(substr($lead->ssn, -4)); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="dup-badge"><i class="bx bx-copy"></i> Duplicate</span>
                            </td>
                            <td class="dup-phone"><?php echo e($lead->phone_number); ?></td>
                            <td>
                                <span class="badge bg-secondary" style="font-size:.65rem;border-radius:22px">
                                    <?php echo e(ucfirst($lead->status ?? 'N/A')); ?>

                                </span>
                            </td>
                            <td style="font-size:.74rem"><?php echo e($lead->carrier_name ?? '—'); ?></td>
                            <td style="font-size:.74rem"><?php echo e($lead->closer_name ?? '—'); ?></td>
                            <td style="font-size:.72rem;color:#94a3b8">
                                <?php echo e($lead->created_at ? $lead->created_at->format('M d, Y') : 'N/A'); ?>

                                <div style="font-size:.65rem">ID #<?php echo e($lead->id); ?></div>
                            </td>
                            <td>
                                <?php $canonical = $canonicalLeads[$lead->phone_number] ?? null; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canonical && $canonical->id !== $lead->id): ?>
                                    <a href="<?php echo e(route('leads.show', $canonical->id)); ?>" class="dup-newer-link">
                                        <i class="bx bx-link-external"></i>
                                        <?php echo e($canonical->cn_name ?? 'View Record'); ?>

                                        <span style="font-size:.65rem;color:#64748b;font-weight:600">
                                            (<?php echo e($canonical->created_at ? $canonical->created_at->format('M d, Y') : ''); ?> · ID #<?php echo e($canonical->id); ?>)
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <span style="font-size:.72rem;color:#94a3b8">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bx bx-check-circle" style="font-size:2.5rem;color:#10b981"></i>
                                <p class="mb-0 mt-2" style="font-weight:700;color:#10b981">No duplicates found!</p>
                                <p class="text-muted mb-0" style="font-size:.78rem">Every phone number in the database has a unique latest record.</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            <?php echo e($duplicates->appends(request()->query())->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('dupSearch');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const form = document.getElementById('dupFilterForm');
                let hidden = form.querySelector('input[name="search"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = 'search';
                    form.appendChild(hidden);
                }
                hidden.value = this.value.trim();
                form.submit();
            }, 600);
        });
        if (searchInput.value) {
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/leads/duplicates.blade.php ENDPATH**/ ?>