<?php $__env->startSection('title'); ?> Bad Leads <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    /* Disposition pills */
    .disp-pill { display:inline-block;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:700; }
    .disp-no-answer { background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.15); }
    .disp-wrong-number { background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.15); }
    .disp-not-interested { background:rgba(107,114,128,.1);color:#4b5563;border:1px solid rgba(107,114,128,.15); }
    .disp-other { background:rgba(99,102,241,.1);color:#6366f1;border:1px solid rgba(99,102,241,.15); }
    /* Search input in filter bar */
    .pipe-search {
        font-size:.72rem; font-weight:600; padding:.32rem .55rem .32rem 1.8rem;
        border-radius:22px; border:1px solid rgba(0,0,0,.08);
        background:var(--bs-card-bg); color:var(--bs-surface-600);
        outline:none; min-width:160px; transition:border-color .15s;
    }
    .pipe-search:focus { border-color:#d4af37; box-shadow:0 0 0 2px rgba(212,175,55,.12); }
    .pipe-search-wrap { position:relative;display:inline-flex;align-items:center; }
    .pipe-search-wrap i { position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <form method="GET" action="<?php echo e(route('ravens.bad-leads')); ?>" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="<?php echo e(route('ravens.bad-leads', ['filter' => 'today'])); ?>" class="pipe-pill <?php echo e(($filter ?? 'today') === 'today' ? 'active' : ''); ?>"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill <?php echo e(($filter ?? '') === 'custom' ? 'active' : ''); ?>" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:<?php echo e(($filter ?? '') === 'custom' ? 'flex' : 'none'); ?>;align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="<?php echo e(request('start_date')); ?>" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="<?php echo e(request('end_date')); ?>" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <div class="pipe-search-wrap">
            <i class="bx bx-search"></i>
            <input type="text" name="search" class="pipe-search" value="<?php echo e($search ?? ''); ?>" placeholder="Search name, phone…">
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($filter ?? 'today') !== 'today' || !empty($search)): ?>
            <a href="<?php echo e(route('ravens.bad-leads', ['filter' => 'today'])); ?>" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    
    <div class="kpi-row">
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-error-circle k-icon"></i>
            <div class="k-val"><?php echo e($badStats['total'] ?? 0); ?></div>
            <div class="k-lbl">Total Disposed</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-phone-off k-icon"></i>
            <div class="k-val"><?php echo e($badStats['no_answer'] ?? 0); ?></div>
            <div class="k-lbl">No Answer</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val"><?php echo e($badStats['wrong_number'] ?? 0); ?></div>
            <div class="k-lbl">Wrong Number</div>
        </div>
        <div class="kpi-card k-gray ex-card">
            <i class="bx bx-block k-icon"></i>
            <div class="k-val"><?php echo e($badStats['not_interested'] ?? 0); ?></div>
            <div class="k-lbl">Not Interested</div>
        </div>
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-dots-horizontal-rounded k-icon"></i>
            <div class="k-val"><?php echo e($badStats['other'] ?? 0); ?></div>
            <div class="k-lbl">Other</div>
        </div>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#c84646;">
            <i class="bx bx-trash" style="color:#f46a6a;"></i> Disposed Contacts
            <span class="badge-count"><?php echo e($badLeads->total()); ?></span>
        </div>
        <div class="scroll-tbl" style="max-height:500px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lead Name</th>
                        <th>Phone</th>
                        <th class="text-center">Disposition</th>
                        <th>Disposed By</th>
                        <th>Date</th>
                        <th>Notes</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $badLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $badLead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr id="row-<?php echo e($badLead->lead_id); ?>">
                            <td><?php echo e($badLeads->firstItem() + $index); ?></td>
                            <td><strong><?php echo e($badLead->lead_name ?? 'N/A'); ?></strong></td>
                            <td><?php echo e($badLead->lead_phone ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php
                                    $dispClass = match($badLead->disposition) {
                                        'no_answer' => 'disp-no-answer',
                                        'wrong_number' => 'disp-wrong-number',
                                        'not_interested' => 'disp-not-interested',
                                        default => 'disp-other',
                                    };
                                ?>
                                <span class="disp-pill <?php echo e($dispClass); ?>"><?php echo e(\App\Models\BadLead::getDispositionLabel($badLead->disposition)); ?></span>
                            </td>
                            <td><?php echo e($badLead->disposedBy->name ?? 'Unknown'); ?></td>
                            <td style="white-space:nowrap;"><?php echo e($badLead->created_at->setTimezone('America/Denver')->format('M d, h:i A')); ?></td>
                            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo e($badLead->notes); ?>"><?php echo e($badLead->notes ?? '—'); ?></td>
                            <td class="text-center">
                                <button class="act-btn a-success" onclick="sendBackLead(<?php echo e($badLead->lead_id); ?>, this)" title="Send back to calling system">
                                    <i class="bx bx-undo"></i> Restore
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                            <i class="bx bx-check-circle" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No bad leads found
                        </td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($badLeads->hasPages()): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .65rem;border-top:1px solid rgba(0,0,0,.04);font-size:.68rem;color:var(--bs-surface-400);">
                <span>Showing <?php echo e($badLeads->firstItem()); ?> to <?php echo e($badLeads->lastItem()); ?> of <?php echo e($badLeads->total()); ?></span>
                <div><?php echo e($badLeads->links()); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php echo $__env->make('partials.sl-filter-assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
    // Submit search on Enter key
    document.querySelector('.pipe-search')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('filterForm').submit(); }
    });

    function sendBackLead(leadId, button) {
        if (!confirm('Are you sure you want to send this lead back to the calling system?')) return;

        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';

        fetch('<?php echo e(route('ravens.leads.restore')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ lead_id: leadId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = button.closest('tr');
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    const tbody = document.querySelector('.ex-tbl tbody');
                    if (tbody && tbody.children.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-check-circle" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No bad leads found</td></tr>';
                    }
                }, 300);
            } else {
                alert(data.message || 'Failed to restore lead');
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while restoring the lead');
            button.disabled = false;
            button.innerHTML = originalHtml;
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/bad-leads.blade.php ENDPATH**/ ?>