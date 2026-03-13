<?php $__env->startSection('title', 'QA Script Editor'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   QA Script Editor — Full control over the AI prompt
   ═══════════════════════════════════════════════════ */

.script-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem 2rem;
}

.script-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0 0.85rem;
    border-bottom: 1px solid rgba(255,255,255,.07);
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.script-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.script-header h5 i { opacity: .7; }

.script-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.sc-btn {
    font-size: .72rem;
    padding: .3rem .75rem;
    border-radius: .35rem;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    text-decoration: none;
}
.sc-btn:disabled { opacity: .5; cursor: not-allowed; }
.sc-btn-gold  { background: var(--bs-gold, #d4af37); color: #fff; }
.sc-btn-gold:hover:not(:disabled) { opacity: .85; }
.sc-btn-blue  { background: rgba(85,110,230,.12); color: #556ee6; border: 1px solid rgba(85,110,230,.3); }
.sc-btn-blue:hover:not(:disabled)  { background: rgba(85,110,230,.2); }
.sc-btn-red   { background: rgba(220,38,38,.10); color: #c84646; border: 1px solid rgba(220,38,38,.25); }
.sc-btn-red:hover:not(:disabled)   { background: rgba(220,38,38,.18); }
.sc-btn-gray  { background: rgba(108,117,125,.1); color: #6c757d; border: 1px solid rgba(108,117,125,.2); }
.sc-btn-gray:hover:not(:disabled)  { background: rgba(108,117,125,.18); }

.type-tabs {
    display: flex;
    gap: 0;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: .4rem;
    overflow: hidden;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.type-tab {
    padding: .35rem .85rem;
    font-size: .72rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    background: transparent;
    color: var(--bs-surface-500, #888);
    transition: all .15s;
    white-space: nowrap;
}
.type-tab.active { background: var(--bs-gold, #d4af37); color: #fff; }
.type-tab:not(.active):hover { background: rgba(212,175,55,.08); color: var(--bs-gold, #d4af37); }

/* Info panel */
.script-info {
    background: rgba(85,110,230,.06);
    border: 1px solid rgba(85,110,230,.15);
    border-radius: .5rem;
    padding: .75rem 1rem;
    margin-bottom: .75rem;
    font-size: .74rem;
    line-height: 1.6;
    color: var(--bs-surface-600, #6c757d);
}
.script-info strong { color: var(--bs-body-color); }
.script-info code {
    background: rgba(85,110,230,.1);
    color: #556ee6;
    padding: .1rem .35rem;
    border-radius: .25rem;
    font-size: .72rem;
}

.custom-badge {
    font-size: .6rem;
    font-weight: 700;
    padding: .15rem .45rem;
    border-radius: 1rem;
    display: inline-block;
}
.badge-custom  { background: rgba(52,195,143,.12); color: #1a8754; border: 1px solid rgba(52,195,143,.25); }
.badge-default { background: rgba(108,117,125,.1); color: #6c757d; border: 1px solid rgba(108,117,125,.2); }

/* Editor */
.editor-wrap {
    position: relative;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: .5rem;
    overflow: hidden;
}
.editor-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .4rem .75rem;
    background: rgba(255,255,255,.03);
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.editor-toolbar-left {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .72rem;
    color: var(--bs-surface-500, #888);
}
.char-count { font-size: .65rem; color: var(--bs-surface-400, #999); font-variant-numeric: tabular-nums; }

#promptEditor {
    width: 100%;
    min-height: 600px;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', 'Courier New', monospace;
    font-size: .76rem;
    line-height: 1.65;
    padding: 1rem;
    background: rgba(0,0,0,.18);
    color: var(--bs-body-color);
    border: none;
    resize: vertical;
    outline: none;
    white-space: pre;
    overflow-wrap: normal;
    overflow-x: auto;
    box-sizing: border-box;
}
#promptEditor:focus { box-shadow: inset 0 0 0 2px rgba(85,110,230,.25); }

/* Toast */
.qa-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    padding: .6rem 1.1rem;
    border-radius: .45rem;
    font-size: .78rem;
    font-weight: 600;
    z-index: 9999;
    transform: translateY(80px);
    opacity: 0;
    transition: all .3s;
    display: flex;
    align-items: center;
    gap: .5rem;
    max-width: 380px;
    box-shadow: 0 4px 16px rgba(0,0,0,.25);
}
.qa-toast.show { transform: translateY(0); opacity: 1; }
.qa-toast.success { background: #1a8754; color: #fff; }
.qa-toast.error   { background: #c84646; color: #fff; }

/* Toggle QA status bar */
.qa-status-bar {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .45rem .85rem;
    border-radius: .45rem;
    font-size: .74rem;
    font-weight: 600;
    margin-bottom: .75rem;
    border: 1px solid transparent;
    transition: all .3s;
}
.qa-status-bar.active  { background: rgba(52,195,143,.08); border-color: rgba(52,195,143,.25); color: #1a8754; }
.qa-status-bar.paused  { background: rgba(241,180,76,.08); border-color: rgba(241,180,76,.25); color: #b87a14; }
.qa-status-bar i { font-size: 1.1rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="script-wrap">

    <!-- ═══ Header ═══ -->
    <div class="script-header">
        <h5>
            <i class="ri-code-s-slash-line"></i> QA Scoring Script Editor
            <span id="customBadge" class="custom-badge <?php echo e($hasCustom ? 'badge-custom' : 'badge-default'); ?>">
                <?php echo e($hasCustom ? 'Custom Prompt' : 'Built-in Default'); ?>

            </span>
        </h5>
        <div class="script-controls">
            <a href="/qa/scoring" class="sc-btn sc-btn-gray"><i class="ri-arrow-left-line"></i> Back to Dashboard</a>
            <button id="resetBtn" class="sc-btn sc-btn-red" onclick="resetToDefault()" <?php echo e(!$hasCustom ? 'disabled' : ''); ?> title="Remove custom prompt and revert to built-in default">
                <i class="ri-refresh-line"></i> Reset to Default
            </button>
            <button id="saveBtn" class="sc-btn sc-btn-gold" onclick="saveScript()">
                <i class="ri-save-3-line"></i> Save Script
            </button>
        </div>
    </div>

    <!-- ═══ Prompt Type Tabs ═══ -->
    <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:.6rem; flex-wrap:wrap;">
        <div class="type-tabs">
            <button class="type-tab active">
                <i class="ri-file-text-line"></i> Scored (Pre-labeled Transcript)
            </button>
        </div>
        <span class="char-count">Characters: <strong id="charCount">0</strong></span>
    </div>

    <!-- ═══ Info Panel ═══ -->
    <div class="script-info">
        <strong>How it works:</strong>
        This is the prompt sent to the AI for every call. Edit it to change what our QA agent checks, how it scores, or what it extracts.
        Use these placeholders — they are replaced automatically at runtime:
        <code>{{TRANSCRIPT}}</code> — the call transcript &nbsp;|&nbsp;
        <code>{{DURATION_MINUTES}}</code> — call duration in minutes &nbsp;|&nbsp;
        <code>{{DURATION_SECONDS}}</code> — call duration in seconds.
        <br><br>
        <strong>Scored prompt</strong> — used with Zoom's built-in transcripts which already have AGENT/CUSTOMER labels.
        <br><br>
        ⚠️ Changes take effect for <em>future calls only</em>. Previously scored calls are not affected.
    </div>

    <!-- ═══ Editor ═══ -->
    <div class="editor-wrap">
        <div class="editor-toolbar">
            <div class="editor-toolbar-left">
                <i class="ri-file-code-line"></i>
                <span id="promptTypeLabel">Scored Prompt (pre-labeled transcript)</span>
            </div>
            <div style="display:flex;gap:.4rem;">
                <button class="sc-btn sc-btn-gray" onclick="copyToClipboard()" style="font-size:.65rem;padding:.2rem .5rem;" title="Copy prompt to clipboard">
                    <i class="ri-clipboard-line"></i> Copy
                </button>
            </div>
        </div>
        <textarea id="promptEditor" oninput="updateCharCount()" spellcheck="false"><?php echo e($template); ?></textarea>
    </div>

    <!-- Save confirmation note -->
    <div style="font-size:.68rem; color:var(--bs-surface-500); margin-top:.5rem; text-align:right;">
        Changes only affect future calls. The AI model is not re-trained — this is purely the instruction prompt.
    </div>

</div>

<!-- ═══ Toast ═══ -->
<div class="qa-toast" id="qaToast"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
(function() {

    let currentType = 'scored';
    let isDirty     = false;
    let isSaving    = false;

    // ── helpers defined first so they can be called anywhere ──────────
    function updateCharCount() {
        const el = document.getElementById('promptEditor');
        if (!el) return;
        document.getElementById('charCount').textContent = el.value.length.toLocaleString();
    }

    function showToast(msg, type) {
        const t = document.getElementById('qaToast');
        t.textContent = msg;
        t.className = 'qa-toast ' + (type || 'success');
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => t.classList.remove('show'), 4000);
    }

    // ── init ──────────────────────────────────────────────────────────
    updateCharCount();
    document.getElementById('promptEditor').addEventListener('input', function() {
        isDirty = true;
        updateCharCount();
    });

    // ── save ──────────────────────────────────────────────────────────
    window.saveScript = function() {
        if (isSaving) return;
        const content = document.getElementById('promptEditor').value.trim();
        if (!content) { showToast('Prompt cannot be empty.', 'error'); return; }
        if (!content.includes('{' + '{TRANSCRIPT}}')) {
            if (!confirm('The prompt does not contain {{TRANSCRIPT}} — the transcript will not be injected. Save anyway?')) return;
        }

        isSaving = true;
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';

        fetch('/qa/api/script', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ type: currentType, content })
        }).then(r => r.json()).then(d => {
            isSaving = false;
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line"></i> Save Script';
            if (d.success) {
                isDirty = false;
                showToast('✅ Script saved. New calls will use this prompt.', 'success');
                document.getElementById('customBadge').className = 'custom-badge badge-custom';
                document.getElementById('customBadge').textContent = 'Custom Prompt';
                document.getElementById('resetBtn').disabled = false;
            } else {
                showToast('Error: ' + (d.message || 'Unknown error'), 'error');
            }
        }).catch(e => {
            isSaving = false;
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line"></i> Save Script';
            showToast('Request failed: ' + e.message, 'error');
        });
    };

    // ── reset to default ──────────────────────────────────────────────
    window.resetToDefault = function() {
        if (!confirm('Reset this prompt to the built-in default? Your custom version will be permanently deleted.')) return;

        fetch('/qa/api/script/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ type: currentType })
        }).then(r => r.json()).then(d => {
            if (d.success) {
                document.getElementById('promptEditor').value = d.template || '';
                updateCharCount();
                isDirty = false;
                document.getElementById('customBadge').className = 'custom-badge badge-default';
                document.getElementById('customBadge').textContent = 'Built-in Default';
                document.getElementById('resetBtn').disabled = true;
                showToast('✅ Reset to built-in default.', 'success');
            } else {
                showToast('Error: ' + (d.message || 'Unknown error'), 'error');
            }
        }).catch(e => { showToast('Request failed: ' + e.message, 'error'); });
    };

    // ── copy ──────────────────────────────────────────────────────────
    window.copyToClipboard = function() {
        navigator.clipboard.writeText(document.getElementById('promptEditor').value)
            .then(() => showToast('Copied to clipboard!', 'success'))
            .catch(() => showToast('Could not copy — try Ctrl+A, Ctrl+C manually.', 'error'));
    };

    // ── expose for textarea oninput ───────────────────────────────────
    window.updateCharCount = updateCharCount;

    // ── keyboard shortcut ─────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveScript(); }
    });

})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/qa/script.blade.php ENDPATH**/ ?>