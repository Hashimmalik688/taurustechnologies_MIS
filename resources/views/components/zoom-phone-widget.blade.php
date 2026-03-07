{{-- ┌─────────────────────────────────────────────────────────────────────────┐
     │  Floating Zoom Phone Smart Embed Widget — appears on every CRM page   │
     │  Bottom-right FAB → opens embedded Zoom Phone dialer                  │
     │  Clicking ANY lead "Call" button auto-dials via postMessage to iframe  │
     └─────────────────────────────────────────────────────────────────────────┘ --}}

<style>
    /* FAB button */
    .zpw-fab {
        position:fixed;bottom:80px;right:20px;z-index:9998;
        width:48px;height:48px;border-radius:50%;border:none;
        background:var(--bs-gold,#d4af37);color:#fff;font-size:22px;
        box-shadow:0 4px 14px rgba(212,175,55,.35);cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        transition:all .2s ease;
    }
    .zpw-fab:hover { transform:scale(1.08);box-shadow:0 6px 20px rgba(212,175,55,.45) }
    .zpw-fab.active { background:#0052CC }
    .zpw-fab.active:hover { background:#003a99 }

    /* Panel */
    .zpw-panel {
        position:fixed;bottom:138px;right:20px;z-index:9999;
        width:375px;background:var(--bs-body-bg,#fff);
        border-radius:.65rem;box-shadow:0 8px 32px rgba(0,0,0,.18);
        border:1px solid rgba(0,0,0,.08);overflow:hidden;
        display:none;flex-direction:column;animation:zpwSlideUp .2s ease;
    }
    @keyframes zpwSlideUp { from { opacity:0;transform:translateY(10px) } to { opacity:1;transform:translateY(0) } }
    .zpw-panel.open { display:flex }

    /* Panel header */
    .zpw-hdr {
        padding:.55rem .75rem;background:linear-gradient(135deg,#2D8CFF,#0052CC);color:#fff;
        display:flex;align-items:center;justify-content:space-between;flex-shrink:0;
    }
    .zpw-hdr-title { font-size:.78rem;font-weight:700;display:flex;align-items:center;gap:.3rem }
    .zpw-hdr-badge { font-size:.58rem;padding:.1rem .4rem;border-radius:8px;background:rgba(255,255,255,.2);margin-left:.3rem;letter-spacing:.3px }
    .zpw-hdr-actions { display:flex;align-items:center;gap:.4rem }
    .zpw-hdr-actions a,.zpw-hdr-actions button { color:rgba(255,255,255,.85);font-size:1.1rem;text-decoration:none;background:none;border:none;cursor:pointer;padding:0;line-height:1 }
    .zpw-hdr-actions a:hover,.zpw-hdr-actions button:hover { color:#fff }

    /* Quick-dial bar */
    .zpw-quickdial {
        display:flex;gap:.35rem;padding:.45rem .65rem;
        border-bottom:1px solid rgba(0,0,0,.07);flex-shrink:0;
        background:var(--bs-body-bg,#fff);
    }
    .zpw-quickdial input {
        flex:1;font-size:.75rem;padding:.3rem .5rem;
        border:1px solid rgba(0,0,0,.1);border-radius:.35rem;
        background:transparent;color:var(--bs-surface-900);
    }
    .zpw-quickdial input:focus { outline:none;border-color:var(--bs-gold,#d4af37) }
    .zpw-quickdial button {
        padding:.3rem .6rem;font-size:.75rem;font-weight:600;border:none;
        border-radius:.35rem;background:#34c38f;color:#fff;cursor:pointer;
        display:flex;align-items:center;gap:.2rem;white-space:nowrap;
    }
    .zpw-quickdial button:hover { background:#2ba97a }

    /* Iframe container */
    .zpw-iframe-wrap { position:relative;flex:1;min-height:490px }
    .zpw-iframe-wrap iframe { display:block;width:100%;height:490px;border:0;transition:opacity .3s }
    .zpw-iframe-loading {
        position:absolute;inset:0;display:flex;flex-direction:column;
        align-items:center;justify-content:center;
        background:var(--bs-body-bg,#fff);gap:.5rem;pointer-events:none;
    }
    .zpw-loading-spinner {
        width:28px;height:28px;border:3px solid rgba(0,0,0,.08);
        border-top-color:#2D8CFF;border-radius:50%;animation:zpwSpin .7s linear infinite;
    }
    @keyframes zpwSpin { to { transform:rotate(360deg) } }
    .zpw-iframe-loading p { margin:0;font-size:.72rem;color:var(--bs-surface-500) }

    /* Dark themes */
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],
        [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zpw-panel {
        background:rgba(15,23,42,.95);border-color:rgba(255,255,255,.08);
    }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],
        [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zpw-quickdial {
        border-bottom-color:rgba(255,255,255,.07);
    }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],
        [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zpw-quickdial input {
        background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1);color:#e2e8f0;
    }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],
        [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zpw-iframe-loading {
        background:rgba(15,23,42,.95);
    }
</style>

{{-- FAB --}}
<button class="zpw-fab" id="zpwFab" onclick="toggleZpWidget()" title="Zoom Phone">
    <i class="bx bx-phone-call"></i>
</button>

{{-- Panel --}}
<div class="zpw-panel" id="zpwPanel">
    <div class="zpw-hdr">
        <div class="zpw-hdr-title">
            <i class="bx bx-phone"></i> Zoom Phone
            <span class="zpw-hdr-badge">Smart Embed</span>
        </div>
        <div class="zpw-hdr-actions">
            <a href="{{ route('zoom.phone') }}" title="Open full-page dialer" target="_blank"><i class="bx bx-expand"></i></a>
            <button onclick="toggleZpWidget()" title="Close"><i class="bx bx-x"></i></button>
        </div>
    </div>

    {{-- Quick-dial bar: number shown here after clicking any lead Call button --}}
    <div class="zpw-quickdial">
        <input type="text" id="zpwDialInput" placeholder="Number auto-fills when you click Call on a lead…"
               onkeypress="if(event.key==='Enter') window.zoomDial(document.getElementById('zpwDialInput').value)">
        <button onclick="window.zoomDial(document.getElementById('zpwDialInput').value)">
            <i class="bx bx-phone-call"></i> Call
        </button>
    </div>

    {{-- Zoom Phone Smart Embed iframe (lazy-loaded on first panel open) --}}
    <div class="zpw-iframe-wrap">
        <div class="zpw-iframe-loading" id="zpwIframeLoading">
            <div class="zpw-loading-spinner"></div>
            <p>Loading Zoom Phone…</p>
        </div>
        <iframe
            id="zpwZoomFrame"
            src="about:blank"
            allow="microphone;camera;autoplay;clipboard-write;clipboard-read;display-capture"
            sandbox="allow-scripts allow-same-origin allow-popups allow-forms allow-popups-to-escape-sandbox allow-downloads"
            style="opacity:0"
        ></iframe>
    </div>
</div>

<script>
(function () {
    // ══════════════════════════════════════════════════════════════════
    //  window.zoomDial(number)
    //  ─────────────────────────────────────────────────────────────────
    //  Global function called by ALL lead/sales/retention/ravens dial
    //  buttons across the entire CRM.  Sends the number to the embedded
    //  Zoom Phone Smart Embed iframe via postMessage.
    // ══════════════════════════════════════════════════════════════════
    window.zoomDial = function (number) {
        if (!number) return;
        number = String(number).replace(/[^\d+]/g, '');
        if (!number) return;

        // Open the widget panel if closed
        const panel = document.getElementById('zpwPanel');
        const fab   = document.getElementById('zpwFab');
        if (panel && !panel.classList.contains('open')) {
            panel.classList.add('open');
            if (fab) { fab.classList.add('active'); fab.innerHTML = '<i class="bx bx-phone-call"></i>'; }
            _zpwLoadFrame(); // lazy-load iframe
        }

        // Show number in quick-dial input for visual feedback
        const input = document.getElementById('zpwDialInput');
        if (input) input.value = number;

        // Prefer the full-page iframe (phone-embed page) if we're on that page;
        // otherwise use the widget iframe.
        const frame = document.getElementById('zoomPhoneFrame') || document.getElementById('zpwZoomFrame');
        if (!frame) return;

        const sendDial = () => {
            // Zoom Phone Smart Embed postMessage protocol
            frame.contentWindow.postMessage({ type: 'dial', number: number }, 'https://app.zoom.us');
        };

        if (frame.dataset.zpwReady === 'true') {
            sendDial();
        } else {
            frame.addEventListener('load', () => {
                frame.dataset.zpwReady = 'true';
                setTimeout(sendDial, 600); // brief pause for Zoom client to initialise
            }, { once: true });
        }
    };

    // ── Toggle panel open / closed ──────────────────────────────────
    window.toggleZpWidget = function () {
        const panel = document.getElementById('zpwPanel');
        const fab   = document.getElementById('zpwFab');
        if (!panel) return;

        if (panel.classList.contains('open')) {
            panel.classList.remove('open');
            fab.classList.remove('active');
            fab.innerHTML = '<i class="bx bx-phone-call"></i>';
        } else {
            panel.classList.add('open');
            fab.classList.add('active');
            fab.innerHTML = '<i class="bx bx-phone-call"></i>';
            _zpwLoadFrame();
        }
    };

    // ── Lazy-load the iframe (only on first open to avoid unnecessary auth) ─
    let _zpwLoading = false;
    function _zpwLoadFrame() {
        const frame = document.getElementById('zpwZoomFrame');
        if (!frame || _zpwLoading || frame.dataset.zpwReady === 'true') return;
        // Already has a real src (e.g. set by a previous load attempt)?
        if (frame.src && frame.src !== 'about:blank' && !frame.src.endsWith(window.location.pathname)) return;

        _zpwLoading = true;
        frame.src = 'https://app.zoom.us/wc/phone';
        frame.addEventListener('load', () => {
            frame.dataset.zpwReady = 'true';
            frame.style.opacity = '1';
            _zpwLoading = false;
            const loader = document.getElementById('zpwIframeLoading');
            if (loader) loader.style.display = 'none';
        }, { once: true });
    }
})();
</script>
