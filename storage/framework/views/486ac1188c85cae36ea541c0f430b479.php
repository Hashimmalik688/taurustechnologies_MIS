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
.cs-add-btn {
    font-size:.7rem; font-weight:700; padding:.32rem .7rem; border-radius:20px;
    border:none; cursor:pointer; display:inline-flex; align-items:center;
    gap:.22rem; background:linear-gradient(135deg, #283593, #1a237e); color:#fff;
    text-decoration:none; transition:all .15s;
}
.cs-add-btn:hover { box-shadow:0 2px 10px rgba(40,53,147,.4); transform:translateY(-1px); color:#fff; }
.cs-del-btn {
    font-size:.62rem; font-weight:700; padding:.2rem .42rem; border-radius:20px;
    border:none; cursor:pointer; background:#C62828; color:#fff; transition:all .15s;
    display:inline-flex; align-items:center; gap:.18rem;
}
.cs-del-btn:hover { background:#B71C1C; }
.cs-color-swatch {
    width:22px; height:22px; border-radius:4px; display:inline-block;
    border:1.5px solid rgba(0,0,0,.12); cursor:pointer; vertical-align:middle;
}
/* Color picker presets */
.cs-color-presets { display:flex; flex-wrap:wrap; gap:.3rem; margin-top:.35rem; }
.cs-color-preset {
    width:20px; height:20px; border-radius:3px; cursor:pointer;
    border:2px solid transparent; transition:border-color .1s;
}
.cs-color-preset:hover, .cs-color-preset.active { border-color:#000; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cs-page">
    <div class="cs-hdr">
        <div class="cs-hdr-left">
            <div class="cs-hdr-icon"><i class="bx bx-cog"></i></div>
            <h5>Commission Rates</h5>
        </div>
        <div style="display:flex; gap:.5rem; align-items:center;">
            <?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
            <button class="cs-add-btn" data-bs-toggle="modal" data-bs-target="#addCarrierModal">
                <i class="bx bx-plus"></i> Add Carrier
            </button>
            <?php endif; ?>
            <a href="<?php echo e(route('settings.reports.carrier-sheet.dashboard')); ?>" class="cs-back">
                <i class="bx bx-arrow-back"></i> Dashboard
            </a>
        </div>
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
                    <td style="white-space:nowrap;">
                        <button class="cs-save-btn" onclick="saveRate(<?php echo e($carrier->id); ?>, this)">
                            <i class="bx bx-save"></i> Save
                        </button>
                        <button class="cs-del-btn ms-1" onclick="deleteCarrier(<?php echo e($carrier->id); ?>, '<?php echo e(addslashes($carrier->carrier_label)); ?>', this)">
                            <i class="bx bx-trash"></i>
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


<?php if(auth()->check() && auth()->user()->canEditModule('carrier-sheet')): ?>
<div class="modal fade" id="addCarrierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="addCarrierForm">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bx bx-plus-circle me-1"></i> Add New Carrier</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Carrier Name <span class="text-danger">*</span></label>
                        <input type="text" name="carrier_label" id="ac_label" class="form-control form-control-sm"
                            placeholder="e.g. AMAM (F-1)" required>
                        <div class="form-text" style="font-size:.65rem;">This will appear as the sheet name on the dashboard.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Color <span class="text-danger">*</span></label>
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <span class="cs-color-swatch" id="ac_colorSwatch" style="background:#283593;" onclick="document.getElementById('ac_colorPicker').click()"></span>
                            <input type="color" id="ac_colorPicker" name="title_color" value="#283593"
                                style="width:0;height:0;opacity:0;position:absolute;pointer-events:none;">
                            <code id="ac_colorCode" style="font-size:.7rem;">#283593</code>
                        </div>
                        <div class="cs-color-presets" id="ac_presets">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['#283593','#1565C0','#2E7D32','#6A1B9A','#C62828','#E65100','#F57F17','#00695C','#37474F','#880E4F','#1A237E','#004D40']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="cs-color-preset" style="background:<?php echo e($c); ?>;" data-color="<?php echo e($c); ?>"></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
                <hr style="margin:.75rem 0;">
                <p style="font-size:.68rem; color:#64748b; margin-bottom:.5rem;"><strong>Commission Rates</strong> — leave blank if the carrier doesn't use that policy type.</p>
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold" style="font-size:.68rem;">Level</label>
                        <input type="number" step="0.0001" name="level_rate" class="form-control form-control-sm" placeholder="e.g. 0.6500">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold" style="font-size:.68rem;">Graded</label>
                        <input type="number" step="0.0001" name="graded_rate" class="form-control form-control-sm" placeholder="e.g. 0.5000">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold" style="font-size:.68rem;">GI</label>
                        <input type="number" step="0.0001" name="gi_rate" class="form-control form-control-sm" placeholder="e.g. 0.0610">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold" style="font-size:.68rem;">Modified</label>
                        <input type="number" step="0.0001" name="modified_rate" class="form-control form-control-sm" placeholder="e.g. 0.5000">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size:.68rem;">GI Multiplier</label>
                        <select name="gi_multiplier" class="form-select form-select-sm">
                            <option value="9">×9 (default)</option>
                            <option value="1">×1 (e.g. SEC)</option>
                        </select>
                    </div>
                </div>
                <div id="ac_error" class="alert alert-danger py-2 px-3 mt-2" style="font-size:.7rem; display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary" id="ac_submitBtn">
                    <i class="bx bx-check me-1"></i> Create Carrier
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
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
    // ── Delete carrier ─────────────────────────────────────────────────
    window.deleteCarrier = async function(carrierId, label, btn) {
        if (!confirm(`Delete carrier "${label}" and ALL its entries? This cannot be undone.`)) return;
        btn.disabled = true;
        try {
            const res = await fetch(`<?php echo e(url('settings/reports/carrier-sheet/rates')); ?>/${carrierId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await res.json();
            if (json.success) {
                btn.closest('tr').remove();
            } else {
                throw new Error(json.message || 'Delete failed');
            }
        } catch(e) {
            alert('Error: ' + e.message);
            btn.disabled = false;
        }
    };

    // ── Add carrier modal ──────────────────────────────────────────────
    const colorPicker  = document.getElementById('ac_colorPicker');
    const colorSwatch  = document.getElementById('ac_colorSwatch');
    const colorCode    = document.getElementById('ac_colorCode');
    const presets      = document.querySelectorAll('.cs-color-preset');

    if (colorPicker) {
        colorPicker.addEventListener('input', () => {
            colorSwatch.style.background = colorPicker.value;
            colorCode.textContent = colorPicker.value;
            presets.forEach(p => p.classList.toggle('active', p.dataset.color === colorPicker.value));
        });

        presets.forEach(p => {
            p.addEventListener('click', () => {
                colorPicker.value = p.dataset.color;
                colorSwatch.style.background = p.dataset.color;
                colorCode.textContent = p.dataset.color;
                presets.forEach(x => x.classList.remove('active'));
                p.classList.add('active');
            });
        });

        const acForm = document.getElementById('addCarrierForm');
        const acErr  = document.getElementById('ac_error');
        const acBtn  = document.getElementById('ac_submitBtn');

        acForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            acErr.style.display = 'none';
            acBtn.disabled = true;
            acBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Creating...';

            const fd = new FormData(acForm);
            const data = Object.fromEntries(fd.entries());
            // Remove empty strings for nullable fields
            ['level_rate','graded_rate','gi_rate','modified_rate'].forEach(k => {
                if (data[k] === '') data[k] = null;
            });

            try {
                const res = await fetch('<?php echo e(route("settings.reports.carrier-sheet.rates.store")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                const json = await res.json();
                if (json.success) {
                    // Reload page to show new row
                    window.location.reload();
                } else {
                    const msgs = json.errors
                        ? Object.values(json.errors).flat().join(' ')
                        : (json.message || 'Failed to create carrier');
                    acErr.textContent = msgs;
                    acErr.style.display = 'block';
                    acBtn.disabled = false;
                    acBtn.innerHTML = '<i class="bx bx-check me-1"></i> Create Carrier';
                }
            } catch(e) {
                acErr.textContent = 'Network error: ' + e.message;
                acErr.style.display = 'block';
                acBtn.disabled = false;
                acBtn.innerHTML = '<i class="bx bx-check me-1"></i> Create Carrier';
            }
        });
    }

})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/carrier-sheet/rates.blade.php ENDPATH**/ ?>