/**
 * Taurus CRM — Client-side Security Layer
 * ─────────────────────────────────────────
 * 1. Disable text selection (except form inputs)
 * 2. Disable right-click context menu
 * 3. Disable copy / cut (except form inputs)
 * 4. Disable paste on non-editable areas
 * 5. Disable drag-start on page content
 * 6. Block keyboard shortcuts: F12, Ctrl+Shift+I/J/C, Ctrl+U, Ctrl+S,
 *    Ctrl+P, Ctrl+A (content), Ctrl+C/X (content), PrtScn
 * 7. Block print via beforeprint + @media print CSS
 * 8. Tab/window blur overlay — blurs content when tab loses focus
 * 9. DevTools size-heuristic detection
 * 10. Screenshot + DevTools alerts → AuditLog + Super Admin notification
 * 11. Visible warning toast shown on every blocked action
 */
(function () {
    'use strict';

    // ── CSRF token (injected by Laravel) ──────────────────────────────
    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content;
    const REPORT_URL  = '/api/security/report-suspect';

    // ── SELF-CONTAINED WARNING TOAST ──────────────────────────────────
    // No dependency on toastr/sweetalert — works on every page.
    var toastContainer = null;

    function ensureToastContainer() {
        if (toastContainer) return;
        toastContainer = document.createElement('div');
        toastContainer.id = 'crm-sec-toasts';
        Object.assign(toastContainer.style, {
            position:       'fixed',
            top:            '72px',       // below topbar
            right:          '18px',
            zIndex:         '1000000',
            display:        'flex',
            flexDirection:  'column',
            gap:            '8px',
            pointerEvents:  'none',
        });
        document.body.appendChild(toastContainer);
    }

    /**
     * showWarning(message, opts)
     *   opts.icon    — emoji prefix (default '🚫')
     *   opts.report  — trigger string to send to server (optional)
     *   opts.flash   — true to also do the red-border flash (default false)
     */
    function showWarning(message, opts) {
        opts = opts || {};
        ensureToastContainer();

        var toast = document.createElement('div');
        Object.assign(toast.style, {
            display:        'flex',
            alignItems:     'center',
            gap:            '10px',
            padding:        '11px 16px',
            borderRadius:   '10px',
            background:     'rgba(30, 8, 8, 0.93)',
            border:         '1px solid rgba(239,68,68,0.45)',
            color:          '#fca5a5',
            fontFamily:     'inherit',
            fontSize:       '.8rem',
            fontWeight:     '600',
            letterSpacing:  '0.01em',
            boxShadow:      '0 6px 24px rgba(0,0,0,0.45)',
            maxWidth:       '340px',
            pointerEvents:  'auto',
            opacity:        '0',
            transform:      'translateX(24px)',
            transition:     'opacity .22s ease, transform .22s ease',
            cursor:         'default',
            userSelect:     'none',
            lineHeight:     '1.35',
        });

        var icon = opts.icon || '🚫';
        toast.innerHTML =
            '<span style="font-size:1.15rem;flex-shrink:0;line-height:1">' + icon + '</span>' +
            '<span>' + message + '</span>';

        toastContainer.appendChild(toast);

        // Animate in
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                toast.style.opacity   = '1';
                toast.style.transform = 'translateX(0)';
            });
        });

        // Auto-dismiss after 3.2 s
        setTimeout(function () {
            toast.style.opacity   = '0';
            toast.style.transform = 'translateX(24px)';
            setTimeout(function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 260);
        }, 3200);

        // Also flash red border if requested
        if (opts.flash) flashBorder();
    }

    // ── Throttle toasts: same message won't stack within 2 s ──────────
    var toastCooldowns = {};
    function warnOnce(message, opts) {
        var key = message;
        if (toastCooldowns[key]) return;
        toastCooldowns[key] = true;
        setTimeout(function () { delete toastCooldowns[key]; }, 2000);
        showWarning(message, opts);
    }

    // ── Throttle: don't spam the server within the same session ───────
    const reported = {};
    function throttledReport(trigger) {
        if (reported[trigger]) return;
        reported[trigger] = true;
        setTimeout(function () { delete reported[trigger]; }, 60000);
        sendReport(trigger);
    }

    function sendReport(trigger) {
        if (!CSRF) return;
        fetch(REPORT_URL, {
            method: 'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                trigger:   trigger,
                url:       window.location.href,
                timestamp: new Date().toISOString(),
            }),
            keepalive: true,
        }).catch(function () { /* silent */ });
    }

    // ── Utility: is the event target a focusable/editable element? ────
    function isEditable(el) {
        if (!el) return false;
        const tag = (el.tagName || '').toLowerCase();
        return (
            tag === 'input' ||
            tag === 'textarea' ||
            tag === 'select' ||
            el.isContentEditable === true ||
            el.getAttribute('contenteditable') === 'true'
        );
    }

    // ── 1. DISABLE TEXT SELECTION (CSS injection) ─────────────────────
    var noSelectStyle = document.createElement('style');
    noSelectStyle.id  = 'crm-security-no-select';
    noSelectStyle.textContent = [
        'body {',
        '    -webkit-user-select: none !important;',
        '    -moz-user-select:    none !important;',
        '    -ms-user-select:     none !important;',
        '    user-select:         none !important;',
        '}',
        'input, textarea, select, [contenteditable] {',
        '    -webkit-user-select: text !important;',
        '    user-select:         text !important;',
        '}',
        '@media print {',
        '    body > * { display: none !important; }',
        '    body::before {',
        '        content: "Printing is disabled in Taurus CRM.";',
        '        display: block !important;',
        '        font-size: 1.6rem;',
        '        text-align: center;',
        '        padding: 120px 40px;',
        '        color: #1e293b;',
        '    }',
        '}',
    ].join('\n');
    document.head.appendChild(noSelectStyle);

    // ── 2. DISABLE RIGHT-CLICK ────────────────────────────────────────
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        throttledReport('rightclick_attempt');
        warnOnce('Right-click is disabled in Taurus CRM.', { icon: '🚫' });
        return false;
    });

    // ── 3. DISABLE COPY / CUT ─────────────────────────────────────────
    document.addEventListener('copy', function (e) {
        if (isEditable(e.target)) return;
        e.preventDefault();
        if (e.clipboardData) e.clipboardData.clearData();
        throttledReport('copy_attempt');
        warnOnce('Copying data is not allowed in Taurus CRM.', { icon: '🚫' });
    }, true);

    document.addEventListener('cut', function (e) {
        if (isEditable(e.target)) return;
        e.preventDefault();
        if (e.clipboardData) e.clipboardData.clearData();
        throttledReport('cut_attempt');
        warnOnce('Cutting data is not allowed in Taurus CRM.', { icon: '🚫' });
    }, true);

    // Paste is allowed everywhere — no restriction.

    // ── 5. DISABLE DRAG on page content ───────────────────────────────
    document.addEventListener('dragstart', function (e) {
        if (isEditable(e.target)) return;
        e.preventDefault();
        throttledReport('drag_attempt');
        warnOnce('Dragging content is disabled in Taurus CRM.', { icon: '🚫' });
    });

    // ── 6. KEYBOARD SHORTCUT BLOCKING ─────────────────────────────────
    document.addEventListener('keydown', function (e) {
        var key  = (e.key  || '').toLowerCase();
        var code = e.keyCode || e.which || 0;
        var ctrl = e.ctrlKey || e.metaKey;

        // ── PrintScreen ───────────────────────────────────────────────
        if (key === 'printscreen' || code === 44) {
            e.preventDefault();
            throttledReport('printscreen_key');
            warnOnce('⚠️ Screenshot attempt detected and logged.', { icon: '📸', flash: true });
            return false;
        }

        // ── F12 ───────────────────────────────────────────────────────
        if (code === 123) {
            e.preventDefault();
            throttledReport('f12_key');
            warnOnce('Developer Tools are disabled in Taurus CRM.', { icon: '🔒' });
            return false;
        }

        // ── Ctrl+Shift+I / J / C (DevTools panels) ───────────────────
        if (ctrl && e.shiftKey && ['i', 'j', 'c'].includes(key)) {
            e.preventDefault();
            throttledReport('devtools_shortcut');
            warnOnce('Developer Tools are disabled in Taurus CRM.', { icon: '🔒' });
            return false;
        }

        // ── Ctrl+U (View Source) ──────────────────────────────────────
        if (ctrl && key === 'u') {
            e.preventDefault();
            throttledReport('view_source');
            warnOnce('Viewing page source is disabled.', { icon: '🔒' });
            return false;
        }

        // ── Ctrl+S (Save page) ────────────────────────────────────────
        if (ctrl && key === 's') {
            e.preventDefault();
            throttledReport('save_page');
            warnOnce('Saving pages is disabled in Taurus CRM.', { icon: '🚫' });
            return false;
        }

        // ── Ctrl+P (Print) ────────────────────────────────────────────
        if (ctrl && key === 'p') {
            e.preventDefault();
            throttledReport('ctrl_p');
            warnOnce('Printing is disabled in Taurus CRM.', { icon: '🖨️' });
            return false;
        }

        // ── Ctrl+C / Ctrl+X / Ctrl+A outside form fields ─────────────
        if (ctrl && ['c', 'x', 'a'].includes(key) && !isEditable(document.activeElement)) {
            e.preventDefault();
            var triggerMap = { c: 'ctrl_c', x: 'ctrl_x', a: 'ctrl_a' };
            var msgs = { c: 'Copying data is not allowed.', x: 'Cutting data is not allowed.', a: 'Select-all is disabled on protected content.' };
            throttledReport(triggerMap[key]);
            warnOnce(msgs[key] || 'Action blocked.', { icon: '🚫' });
            return false;
        }

        // ── Win+Shift+S (Windows Snipping Tool — limited intercept) ───
        if (e.shiftKey && e.metaKey && key === 's') {
            e.preventDefault();
            throttledReport('printscreen_key');
            warnOnce('⚠️ Screenshot attempt detected and logged.', { icon: '📸', flash: true });
            return false;
        }

    }, true);

    // ── 7. BEFOREPRINT INTERCEPT ──────────────────────────────────────
    window.addEventListener('beforeprint', function () {
        throttledReport('ctrl_p');
        warnOnce('Printing is disabled in Taurus CRM.', { icon: '🖨️' });
    });

    // ── 8. TAB BLUR OVERLAY ───────────────────────────────────────────
    // Only triggers via visibilitychange (tab hidden) — NOT on window.blur,
    // because blur fires on any focus shift (address bar, Alt+Tab, other apps).
    // The 5-minute grace period means quick tab switching never shows the overlay;
    // it only appears when the user has genuinely left the page for an extended time.
    var blurOverlay   = null;
    var blurTimer     = null;
    var BLUR_DELAY_MS = 5 * 60 * 1000; // 5 minutes — only fires when truly away from desk

    function ensureBlurOverlay() {
        if (blurOverlay) return;
        blurOverlay = document.createElement('div');
        blurOverlay.id = 'crm-blur-overlay';
        Object.assign(blurOverlay.style, {
            position:            'fixed',
            inset:               '0',
            zIndex:              '999999',
            backdropFilter:      'blur(16px)',
            WebkitBackdropFilter:'blur(16px)',
            background:          'rgba(10, 15, 30, 0.72)',
            display:             'none',
            alignItems:          'center',
            justifyContent:      'center',
            flexDirection:       'column',
            gap:                 '14px',
            color:               '#f1f5f9',
            fontFamily:          'inherit',
            fontSize:            '1.05rem',
            fontWeight:          '600',
            letterSpacing:       '0.01em',
            userSelect:          'none',
            cursor:              'default',
        });
        blurOverlay.innerHTML =
            '<div style="font-size:3rem;line-height:1;opacity:.85">🔒</div>' +
            '<div>Content protected — return to this tab</div>' +
            '<div style="font-size:.72rem;font-weight:400;opacity:.6;margin-top:-6px">' +
                'Taurus CRM hides data after extended inactivity' +
            '</div>';
        document.body.appendChild(blurOverlay);
    }

    document.addEventListener('DOMContentLoaded', ensureBlurOverlay);
    if (document.readyState !== 'loading') ensureBlurOverlay();

    function showBlur() { ensureBlurOverlay(); blurOverlay.style.display = 'flex'; }
    function hideBlur() { if (blurOverlay) blurOverlay.style.display = 'none'; }

    // Only use visibilitychange — don't use window.blur (too sensitive)
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) { blurTimer = setTimeout(showBlur, BLUR_DELAY_MS); }
        else { clearTimeout(blurTimer); hideBlur(); }
    });

    // ── 9. DEVTOOLS SIZE-HEURISTIC DETECTION ─────────────────────────
    (function devToolsCheck() {
        var THRESHOLD     = 170;
        var wasOpen       = false;
        var POLL_INTERVAL = 3000;

        function check() {
            var wDiff  = window.outerWidth  - window.innerWidth;
            var hDiff  = window.outerHeight - window.innerHeight;
            var isOpen = wDiff > THRESHOLD || hDiff > THRESHOLD;

            if (isOpen && !wasOpen) {
                wasOpen = true;
                throttledReport('devtools_opened');
                warnOnce('⚠️ Developer Tools detected. This activity has been logged.', { icon: '🔍', flash: true });
            } else if (!isOpen && wasOpen) {
                wasOpen = false;
            }
        }

        if (typeof requestIdleCallback === 'function') {
            (function scheduleCheck() {
                requestIdleCallback(function () {
                    check();
                    setTimeout(scheduleCheck, POLL_INTERVAL);
                }, { timeout: POLL_INTERVAL });
            })();
        } else {
            setInterval(check, POLL_INTERVAL);
        }
    })();

    // ── RED BORDER FLASH (used for high-severity events) ──────────────
    function flashBorder() {
        var el   = document.body;
        var prev = el.style.outline;
        el.style.transition = 'outline 0.05s';
        el.style.outline    = '4px solid rgba(239,68,68,0.7)';
        setTimeout(function () {
            el.style.outline    = prev;
            el.style.transition = '';
        }, 400);
    }

})();
