<?php $__env->startSection('title', 'QA Script Editor'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ═══════════════════════════════════════════════════
   QA Script Editor — qa-* design system alignment
   ═══════════════════════════════════════════════════ */

/* CSS variables (mirrors dashboard) */
:root {
    --qa-gold: #d4af37;
    --qa-gold-dim: rgba(212,175,55,.08);
    --qa-gold-border: rgba(212,175,55,.25);
    --qa-green: #34c38f;
    --qa-blue: #556ee6;
    --qa-red: #f46a6a;
    --qa-surface: #1e2130;
    --qa-surface-border: rgba(255,255,255,.07);
    --qa-muted: #6c757d;
    --qa-text: var(--bs-body-color, #e2e8f0);
    --qa-radius: .45rem;
    --qa-radius-lg: .7rem;
}

/* ── Layout ── */
.qs-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem 2.5rem;
}

/* ── Page header (matches .qa-page-header) ── */
.qs-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .65rem 0 .85rem;
    border-bottom: 1px solid var(--qa-surface-border);
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: .5rem;
}
.qs-title {
    margin: 0;
    font-size: .95rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: .45rem;
    color: var(--qa-text);
}
.qs-title i { color: var(--qa-gold); opacity: .85; font-size: 1rem; }

/* ── Toolbar buttons (matches .qa-action-btn) ── */
.qs-toolbar { display: flex; gap: .45rem; align-items: center; flex-wrap: wrap; }
.qs-btn {
    font-size: .72rem;
    padding: .35rem .8rem;
    border-radius: var(--qa-radius);
    border: 1px solid transparent;
    cursor: pointer;
    font-weight: 600;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    text-decoration: none;
    white-space: nowrap;
}
.qs-btn:disabled { opacity: .45; cursor: not-allowed; }
.qs-btn-primary { background: var(--qa-gold); color: #1a1200; border-color: var(--qa-gold); }
.qs-btn-primary:hover:not(:disabled) { filter: brightness(1.08); color: #1a1200; }
.qs-btn-danger  { background: rgba(244,106,106,.1); color: #c84646; border-color: rgba(244,106,106,.25); }
.qs-btn-danger:hover:not(:disabled)  { background: rgba(244,106,106,.2); }
.qs-btn-ghost   { background: rgba(108,117,125,.08); color: var(--qa-muted); border-color: rgba(108,117,125,.18); }
.qs-btn-ghost:hover:not(:disabled)   { background: rgba(108,117,125,.16); }
.qs-btn-blue    { background: rgba(85,110,230,.1); color: #556ee6; border-color: rgba(85,110,230,.25); }
.qs-btn-blue:hover:not(:disabled)    { background: rgba(85,110,230,.2); }

/* ── Prompt type tabs (matches .qa-filter-bar) ── */
.qs-tabs {
    display: flex;
    align-items: center;
    gap: .25rem;
    padding: .25rem;
    background: var(--qa-surface);
    border: 1px solid var(--qa-surface-border);
    border-radius: var(--qa-radius);
    margin-bottom: .65rem;
    width: fit-content;
}
.qs-tab {
    padding: .3rem .8rem;
    font-size: .71rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    background: transparent;
    color: var(--qa-muted);
    border-radius: calc(var(--qa-radius) - 2px);
    transition: all .15s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}
.qs-tab.active { background: var(--qa-gold); color: #1a1200; }
.qs-tab:not(.active):hover { background: var(--qa-gold-dim); color: var(--qa-gold); }

/* ── Card (matches .qa-card) ── */
.qs-card {
    background: var(--qa-surface);
    border: 1px solid var(--qa-surface-border);
    border-radius: var(--qa-radius-lg);
    margin-bottom: .75rem;
    overflow: hidden;
}
.qs-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .55rem .9rem;
    border-bottom: 1px solid var(--qa-surface-border);
}
.qs-card-header h6 {
    margin: 0;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--qa-muted);
    display: flex;
    align-items: center;
    gap: .35rem;
}
.qs-card-header h6 i { font-size: .85rem; }
.qs-card-body { padding: .75rem .9rem; }

/* ── Prompt source badge ── */
.qs-badge {
    font-size: .6rem;
    font-weight: 700;
    padding: .15rem .45rem;
    border-radius: 1rem;
    display: inline-flex;
    align-items: center;
    gap: .2rem;
}
.qs-badge-custom  { background: rgba(52,195,143,.12); color: #1a8754; border: 1px solid rgba(52,195,143,.3); }
.qs-badge-default { background: rgba(108,117,125,.1); color: #6c757d; border: 1px solid rgba(108,117,125,.2); }

/* ── Info panel ── */
.qs-info {
    background: rgba(85,110,230,.06);
    border: 1px solid rgba(85,110,230,.15);
    border-radius: var(--qa-radius);
    padding: .7rem .95rem;
    font-size: .73rem;
    line-height: 1.65;
    color: var(--qa-muted);
    margin-bottom: .75rem;
}
.qs-info strong { color: var(--qa-text); }
.qs-info code {
    background: rgba(85,110,230,.1);
    color: #839cf8;
    padding: .1rem .35rem;
    border-radius: .25rem;
    font-size: .71rem;
}

/* ── Editor ── */
.qs-editor-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .35rem .8rem;
    background: rgba(255,255,255,.025);
    border-bottom: 1px solid var(--qa-surface-border);
    font-size: .68rem;
    color: var(--qa-muted);
}
.qs-char-count { font-variant-numeric: tabular-nums; }

#promptEditor {
    width: 100%;
    min-height: 580px;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', 'Courier New', monospace;
    font-size: .76rem;
    line-height: 1.7;
    padding: 1rem;
    background: rgba(0,0,0,.2);
    color: var(--bs-body-color, #e2e8f0);
    border: none;
    resize: vertical;
    outline: none;
    white-space: pre;
    overflow-wrap: normal;
    overflow-x: auto;
    box-sizing: border-box;
    display: block;
}
#promptEditor:focus { box-shadow: inset 0 0 0 2px rgba(212,175,55,.2); }

/* ── Toast (unchanged from original) ── */
.qa-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    padding: .6rem 1.1rem;
    border-radius: var(--qa-radius);
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
    box-shadow: 0 4px 16px rgba(0,0,0,.3);
}
.qa-toast.show { transform: translateY(0); opacity: 1; }
.qa-toast.success { background: #1a8754; color: #fff; }
.qa-toast.error   { background: #c84646; color: #fff; }

/* Auto-save status pill */
.qs-autosave-status {
    font-size: .63rem;
    color: var(--qa-muted);
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    transition: color .2s;
    white-space: nowrap;
}
.qs-autosave-status.dirty  { color: #f1b44c; }
.qs-autosave-status.saving { color: #556ee6; }
.qs-autosave-status.saved  { color: #34c38f; }
.qs-autosave-dot { width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block; flex-shrink:0; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="qs-wrap">

    <!-- ═══ Header ═══ -->
    <div class="qs-header">
        <h5 class="qs-title">
            <i class="ri-code-s-slash-line"></i>
            QA Script Editor
            <span id="customBadge" class="qs-badge <?php echo e($hasCustom ? 'qs-badge-custom' : 'qs-badge-default'); ?>">
                <i class="<?php echo e($hasCustom ? 'ri-edit-2-line' : 'ri-cpu-line'); ?>"></i>
                <?php echo e($hasCustom ? 'Custom Prompt' : 'Built-in Default'); ?>

            </span>
        </h5>
        <div class="qs-toolbar">
            <a href="/qa/scoring" class="qs-btn qs-btn-ghost">
                <i class="ri-arrow-left-line"></i> Dashboard
            </a>
            <button id="resetBtn" class="qs-btn qs-btn-danger" onclick="resetToDefault()" <?php echo e(!$hasCustom ? 'disabled' : ''); ?>>
                <i class="ri-refresh-line"></i> Reset to Default
            </button>
            <button class="qs-btn qs-btn-blue" onclick="copyToClipboard()">
                <i class="ri-clipboard-line"></i> Copy
            </button>
            <span class="qs-autosave-status" id="autoSaveStatus"><span class="qs-autosave-dot"></span> Saved</span>
            <button id="saveBtn" class="qs-btn qs-btn-primary" onclick="saveScript()">
                <i class="ri-save-3-line"></i> Save Script
            </button>
        </div>
    </div>

    <!-- ═══ Prompt Type Tabs ═══ -->
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.7rem;flex-wrap:wrap;">
        <div class="qs-tabs">
            <button class="qs-tab active">
                <i class="ri-file-text-line"></i> Scored Prompt
            </button>
        </div>
        <span class="qs-char-count" style="font-size:.65rem;color:var(--qa-muted);">
            Characters: <strong id="charCount">0</strong>
        </span>
    </div>

    <!-- ═══ Info Panel ═══ -->
    <div class="qs-info">
        <strong>How this works:</strong>
        This prompt is sent to the AI for every completed call. Edit it to change what the QA agent checks, how it scores, or what it extracts.
        Use these placeholders — they are replaced automatically at runtime:
        <code>{{TRANSCRIPT}}</code> — the full call transcript &nbsp;·&nbsp;
        <code>{{DURATION_MINUTES}}</code> — call length in minutes &nbsp;·&nbsp;
        <code>{{DURATION_SECONDS}}</code> — call length in seconds.
        <br><br>
        ⚠️ Changes take effect for <em>future calls only</em>. Previously scored calls are not retroactively updated.
    </div>

    <!-- ═══ Editor Card ═══ -->
    <div class="qs-card">
        <div class="qs-card-header">
            <h6><i class="ri-file-code-line"></i> Scored Prompt — Pre-labeled Transcript</h6>
            <span class="qs-char-count" style="font-size:.64rem;color:var(--qa-muted);"><span id="charCountInline">0</span> chars</span>
        </div>
        <div class="qs-editor-toolbar">
            <span><i class="ri-code-line" style="margin-right:.25rem;"></i> AI Instruction Prompt</span>
            <span class="qs-hint" style="margin:0;">Save: <kbd>Ctrl</kbd>+<kbd>S</kbd></span>
        </div>
        <textarea id="promptEditor" oninput="updateCharCount()" spellcheck="false"><?php echo e($template); ?></textarea>
    </div>

    <div class="qs-hint">
        Changes only affect future calls — this is a prompt, not a model fine-tune.
    </div>

</div>

<!-- ═══ Toast ═══ -->
<div class="qa-toast" id="qaToast"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
(function() {

    let currentType   = 'scored';
    let isDirty       = false;
    let isSaving      = false;
    let autoSaveTimer = null;
    const AUTO_SAVE_DELAY = 5000; // 5 s after last keystroke

    // ── helpers ────────────────────────────────────────────────────────
    function updateCharCount() {
        const el = document.getElementById('promptEditor');
        if (!el) return;
        const count = el.value.length.toLocaleString();
        const a = document.getElementById('charCount');
        const b = document.getElementById('charCountInline');
        if (a) a.textContent = count;
        if (b) b.textContent = count;
    }

    function setAutoSaveStatus(state, msg) {
        const el = document.getElementById('autoSaveStatus');
        if (!el) return;
        el.className = 'qs-autosave-status ' + (state || '');
        el.innerHTML = `<span class="qs-autosave-dot"></span> ${msg}`;
    }

    function showToast(msg, type) {
        const t = document.getElementById('qaToast');
        t.innerHTML = msg;
        t.className = 'qa-toast ' + (type || 'success');
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => t.classList.remove('show'), 4000);
    }

    // ── init (DOMContentLoaded to guarantee textarea value is readable) ─
    function init() {
        updateCharCount();

        const editor = document.getElementById('promptEditor');
        if (!editor) return;

        editor.addEventListener('input', function() {
            isDirty = true;
            updateCharCount();
            setAutoSaveStatus('dirty', 'Unsaved changes');

            // Debounced auto-save
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                if (isDirty && !isSaving) saveScript(true);
            }, AUTO_SAVE_DELAY);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ── save ───────────────────────────────────────────────────────────
    window.saveScript = function(isAuto) {
        if (isSaving) return;
        const content = document.getElementById('promptEditor').value.trim();
        if (!content) { showToast('Prompt cannot be empty.', 'error'); return; }
        if (!isAuto && !content.includes('{' + '{TRANSCRIPT}}')) {
            if (!confirm('The prompt does not contain {{TRANSCRIPT}} — the transcript will not be injected. Save anyway?')) return;
        }

        isSaving = true;
        clearTimeout(autoSaveTimer);
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line"></i> Saving…';
        setAutoSaveStatus('saving', 'Saving…');

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
                setAutoSaveStatus('saved', 'Saved');
                if (!isAuto) showToast('✅ Script saved — new calls will use this prompt.', 'success');
                const badge = document.getElementById('customBadge');
                badge.className = 'qs-badge qs-badge-custom';
                badge.innerHTML = '<i class="ri-edit-2-line"></i> Custom Prompt';
                document.getElementById('resetBtn').disabled = false;
            } else {
                setAutoSaveStatus('dirty', 'Save failed');
                showToast('Error: ' + (d.message || 'Unknown error'), 'error');
            }
        }).catch(e => {
            isSaving = false;
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line"></i> Save Script';
            setAutoSaveStatus('dirty', 'Save failed');
            showToast('Request failed: ' + e.message, 'error');
        });
    };

    // ── reset ──────────────────────────────────────────────────────────
    window.resetToDefault = function() {
        if (!confirm('Reset to the built-in default? Your custom version will be permanently deleted.')) return;

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
                setAutoSaveStatus('saved', 'Built-in default');
                const badge = document.getElementById('customBadge');
                badge.className = 'qs-badge qs-badge-default';
                badge.innerHTML = '<i class="ri-cpu-line"></i> Built-in Default';
                document.getElementById('resetBtn').disabled = true;
                showToast('✅ Reset to built-in default.', 'success');
            } else {
                showToast('Error: ' + (d.message || 'Unknown error'), 'error');
            }
        }).catch(e => { showToast('Request failed: ' + e.message, 'error'); });
    };

    // ── copy ───────────────────────────────────────────────────────────
    window.copyToClipboard = function() {
        navigator.clipboard.writeText(document.getElementById('promptEditor').value)
            .then(() => showToast('Copied to clipboard!', 'success'))
            .catch(() => showToast('Could not copy — try Ctrl+A, Ctrl+C manually.', 'error'));
    };

    window.updateCharCount = updateCharCount;

    // ── Ctrl+S: capture phase beats CRM global handlers ───────────────
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            e.stopImmediatePropagation();
            saveScript();
        }
    }, true); // useCapture = true

})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/qa/script.blade.php ENDPATH**/ ?>