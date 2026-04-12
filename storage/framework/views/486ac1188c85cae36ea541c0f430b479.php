<?php $__env->startSection('title'); ?>
    Carrier Sheet — Rates
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
:root {
    --cs-surface: var(--bs-card-bg, #ffffff);
    --cs-border: rgba(0,0,0,.07);
    --cs-shadow: 0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --cs-text-1: var(--bs-body-color, #0f172a);
    --cs-text-3: var(--bs-surface-500, #64748b);
}
.cs-page { width:100%; }
.cs-hdr {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.5rem; margin-bottom:1rem;
}
.cs-hdr-left { display:flex; align-items:center; gap:.6rem; }
.cs-hdr-icon {
    width:32px; height:32px; border-radius:.45rem;
    background:linear-gradient(135deg, #283593, #1a237e);
    display:flex; align-items:center; justify-content:center;
    box-shadow:0 2px 6px rgba(40,53,147,.35);
}
.cs-hdr-icon i { font-size:1rem; color:#fff; }
.cs-hdr h5 { margin:0; font-size:1rem; font-weight:800; color:var(--cs-text-1); }
.cs-back {
    font-size:.68rem; font-weight:700; padding:.26rem .55rem; border-radius:20px;
    border:1.5px solid var(--cs-border); background:transparent; color:var(--cs-text-3);
    text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s;
}
.cs-back:hover { border-color:#283593; color:#1a237e; }

.cs-card {
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; overflow-x:auto; box-shadow:var(--cs-shadow);
}
.cs-rates-table { width:100%; font-size:.72rem; border-collapse:collapse; min-width:700px; }
.cs-rates-table thead th {
    background:#283593; color:#fff; font-weight:700; font-size:.6rem;
    text-transform:uppercase; letter-spacing:.5px; padding:.45rem .5rem;
    text-align:center; white-space:nowrap;
}
.cs-rates-table tbody td {
    padding:.4rem .5rem; border-bottom:1px solid var(--cs-border);
    text-align:center; vertical-align:middle;
}
.cs-rates-table tbody tr:hover { background:rgba(40,53,147,.04); }
.cs-carrier-dot {
    width:12px; height:12px; border-radius:3px; display:inline-block; vertical-align:middle; margin-right:4px;
}
.cs-rate-input {
    width:72px; text-align:center; font-size:.72rem; padding:.2rem .3rem;
    border:1.5px solid var(--cs-border); border-radius:.35rem;
    background:var(--bs-input-bg, #f8fafc); color:var(--cs-text-1);
}
.cs-rate-input:focus {
    border-color:#283593; box-shadow:0 0 0 2px rgba(40,53,147,.15); outline:none;
}
.cs-rate-na { color:var(--cs-text-3); font-style:italic; }
.cs-save-btn {
    font-size:.64rem; font-weight:700; padding:.22rem .5rem; border-radius:20px;
    border:none; cursor:pointer; background:#2E7D32; color:#fff; transition:all .15s;
}
.cs-save-btn:hover { box-shadow:0 2px 8px rgba(46,125,50,.4); }
.cs-save-btn:disabled { opacity:.5; cursor:default; }
.cs-note {
    font-size:.68rem; color:var(--cs-text-3); padding:.6rem .8rem;
    border-top:1px solid var(--cs-border); background:#fffef5;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cs-page">
    <div class="cs-hdr">
        <div class="cs-hdr-left">
            <div class="cs-hdr-icon"><i class="bx bx-cog"></i></div>
            <h5>Commission Rates</h5>
        </div>
        <a href="<?php echo e(route('settings.reports.carrier-sheet.dashboard')); ?>" class="cs-back">
            <i class="bx bx-arrow-back"></i> Dashboard
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" style="font-size:.72rem;">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="cs-card">
        <table class="cs-rates-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Carrier / Partner</th>
                    <th>Level</th>
                    <th>Graded</th>
                    <th>GI</th>
                    <th>Modified</th>
                    <th>GI ×</th>
                    <th>Notes</th>
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <th></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align:left; font-weight:700;">
                        <span class="cs-carrier-dot" style="background:<?php echo e($carrier->title_color); ?>;"></span>
                        <?php echo e($carrier->carrier_label); ?>

                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->level_rate !== null): ?>
                            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                            <input type="number" step="0.0001" class="cs-rate-input" data-carrier="<?php echo e($carrier->id); ?>" data-field="level_rate" value="<?php echo e($carrier->level_rate); ?>">
                            <?php else: ?>
                            <?php echo e($carrier->level_rate); ?>

                            <?php endif; ?>
                        <?php else: ?>
                            <span class="cs-rate-na">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->graded_rate !== null): ?>
                            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                            <input type="number" step="0.0001" class="cs-rate-input" data-carrier="<?php echo e($carrier->id); ?>" data-field="graded_rate" value="<?php echo e($carrier->graded_rate); ?>">
                            <?php else: ?>
                            <?php echo e($carrier->graded_rate); ?>

                            <?php endif; ?>
                        <?php else: ?>
                            <span class="cs-rate-na">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->gi_rate !== null): ?>
                            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                            <input type="number" step="0.0001" class="cs-rate-input" data-carrier="<?php echo e($carrier->id); ?>" data-field="gi_rate" value="<?php echo e($carrier->gi_rate); ?>">
                            <?php else: ?>
                            <?php echo e($carrier->gi_rate); ?>

                            <?php endif; ?>
                        <?php else: ?>
                            <span class="cs-rate-na">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->modified_rate !== null): ?>
                            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                            <input type="number" step="0.0001" class="cs-rate-input" data-carrier="<?php echo e($carrier->id); ?>" data-field="modified_rate" value="<?php echo e($carrier->modified_rate); ?>">
                            <?php else: ?>
                            <?php echo e($carrier->modified_rate); ?>

                            <?php endif; ?>
                        <?php else: ?>
                            <span class="cs-rate-na">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                        <select class="cs-rate-input" data-carrier="<?php echo e($carrier->id); ?>" data-field="gi_multiplier" style="width:50px;">
                            <option value="9" <?php echo e($carrier->gi_multiplier == 9 ? 'selected' : ''); ?>>×9</option>
                            <option value="1" <?php echo e($carrier->gi_multiplier == 1 ? 'selected' : ''); ?>>×1</option>
                        </select>
                        <?php else: ?>
                        ×<?php echo e($carrier->gi_multiplier); ?>

                        <?php endif; ?>
                    </td>
                    <td style="font-size:.62rem; color:var(--cs-text-3); text-align:left; max-width:200px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($carrier->uses_hardcoded_rates): ?>
                            Hardcoded: Pref/Std/Super Pref = Level rate, Modified = Modified rate
                        <?php elseif($carrier->carrier_slug === 'sec-f1'): ?>
                            GI uses ×1 multiplier
                        <?php else: ?>
                            —
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
                    <td>
                        <button class="cs-save-btn" onclick="saveRate(<?php echo e($carrier->id); ?>, this)">
                            <i class="bx bx-save"></i> Save
                        </button>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        <div class="cs-note">
            <i class="bx bx-info-circle me-1"></i>
            <strong>Formula:</strong> Commission = Premium × Multiplier × Rate ÷ 2.
            Changing a rate will recalculate all entries for that carrier.
            GI Multiplier: SEC F-1 uses ×1, all others use ×9.
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
(function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    window.saveRate = async function(carrierId, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';

        // Gather all inputs for this carrier
        const inputs = document.querySelectorAll(`[data-carrier="${carrierId}"]`);
        const data = {};
        inputs.forEach(inp => {
            const field = inp.dataset.field;
            const val = inp.value;
            if (val === '' || val === null) {
                data[field] = null;
            } else {
                data[field] = inp.tagName === 'SELECT' ? parseInt(val) : parseFloat(val);
            }
        });

        try {
            const res = await fetch(`<?php echo e(url('settings/reports/carrier-sheet/rates')); ?>/${carrierId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            const json = await res.json();
            if (json.success) {
                btn.innerHTML = '<i class="bx bx-check"></i> Saved';
                btn.style.background = '#1B5E20';
                setTimeout(() => {
                    btn.innerHTML = '<i class="bx bx-save"></i> Save';
                    btn.style.background = '#2E7D32';
                    btn.disabled = false;
                }, 1500);
            } else {
                throw new Error(json.message || 'Save failed');
            }
        } catch (e) {
            alert('Error: ' + e.message);
            btn.innerHTML = '<i class="bx bx-save"></i> Save';
            btn.disabled = false;
        }
    };
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/carrier-sheet/rates.blade.php ENDPATH**/ ?>