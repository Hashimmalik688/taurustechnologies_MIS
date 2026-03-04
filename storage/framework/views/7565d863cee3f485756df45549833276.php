

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
    .zpw-fab.active { background:#c84646;transform:rotate(45deg) }
    .zpw-fab.active:hover { background:#b03c3c }

    /* Panel */
    .zpw-panel {
        position:fixed;bottom:138px;right:20px;z-index:9999;
        width:320px;background:var(--bs-body-bg,#fff);
        border-radius:.65rem;box-shadow:0 8px 32px rgba(0,0,0,.18);
        border:1px solid rgba(0,0,0,.08);overflow:hidden;
        display:none;animation:zpwSlideUp .2s ease;
    }
    @keyframes zpwSlideUp { from { opacity:0;transform:translateY(10px) } to { opacity:1;transform:translateY(0) } }
    .zpw-panel.open { display:block }

    /* Panel header */
    .zpw-hdr {
        padding:.55rem .75rem;background:linear-gradient(135deg,#2D8CFF,#0052CC);color:#fff;
        display:flex;align-items:center;justify-content:space-between;
    }
    .zpw-hdr-title { font-size:.78rem;font-weight:700;display:flex;align-items:center;gap:.3rem }
    .zpw-hdr-actions { display:flex;align-items:center;gap:.4rem }
    .zpw-hdr-actions a,.zpw-hdr-actions button { color:rgba(255,255,255,.85);font-size:1.1rem;text-decoration:none;background:none;border:none;cursor:pointer;padding:0;line-height:1 }
    .zpw-hdr-actions a:hover,.zpw-hdr-actions button:hover { color:#fff }

    /* Panel body */
    .zpw-body { padding:.65rem .75rem }

    /* Dial input */
    .zpw-dial { display:flex;gap:.35rem;margin-bottom:.5rem }
    .zpw-dial input {
        flex:1;font-size:.76rem;padding:.35rem .55rem;
        border:1px solid rgba(0,0,0,.1);border-radius:.35rem;
        background:var(--bs-body-bg,#fff);color:var(--bs-surface-900);
    }
    .zpw-dial input:focus { outline:none;border-color:var(--bs-gold,#d4af37) }
    .zpw-dial button {
        padding:.35rem .6rem;font-size:.76rem;font-weight:600;border:none;
        border-radius:.35rem;background:#34c38f;color:#fff;cursor:pointer;
        display:flex;align-items:center;gap:.2rem;
    }
    .zpw-dial button:hover { background:#2ba97a }

    /* Quick contacts / recent */
    .zpw-section-title {
        font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;
        color:var(--bs-surface-400);margin-bottom:.3rem;padding:0 .1rem;
    }
    .zpw-list { max-height:180px;overflow-y:auto;margin-bottom:.35rem }
    .zpw-list::-webkit-scrollbar { width:3px }
    .zpw-list::-webkit-scrollbar-thumb { background:rgba(0,0,0,.08);border-radius:3px }

    .zpw-item {
        padding:.35rem .2rem;display:flex;align-items:center;justify-content:space-between;
        border-bottom:1px solid rgba(0,0,0,.03);font-size:.72rem;
    }
    .zpw-item:last-child { border-bottom:none }
    .zpw-item-name { font-weight:600;color:var(--bs-surface-800) }
    .zpw-item-sub { font-size:.62rem;color:var(--bs-surface-400) }
    .zpw-item-btn {
        padding:.15rem .35rem;font-size:.6rem;border:none;border-radius:.25rem;
        background:rgba(52,195,143,.1);color:#1a8754;cursor:pointer;
    }
    .zpw-item-btn:hover { background:rgba(52,195,143,.2) }

    /* Footer link */
    .zpw-footer {
        padding:.4rem .75rem;border-top:1px solid rgba(0,0,0,.06);text-align:center;
    }
    .zpw-footer a {
        font-size:.65rem;font-weight:600;color:var(--bs-gold,#d4af37);text-decoration:none;
    }
    .zpw-footer a:hover { text-decoration:underline }

    /* Dark theme */
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zpw-panel {
        background:rgba(15,23,42,.95);border-color:rgba(255,255,255,.08);
    }
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zpw-dial input {
        background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1);color:#e2e8f0;
    }
</style>


<button class="zpw-fab" id="zpwFab" onclick="toggleZpWidget()" title="Zoom Phone">
    <i class="bx bx-phone-call"></i>
</button>


<div class="zpw-panel" id="zpwPanel">
    <div class="zpw-hdr">
        <div class="zpw-hdr-title"><i class="bx bx-phone"></i> Zoom Phone</div>
        <div class="zpw-hdr-actions">
            <a href="<?php echo e(route('zoom.phone')); ?>" title="Full screen"><i class="bx bx-expand"></i></a>
            <button onclick="toggleZpWidget()" title="Close"><i class="bx bx-x"></i></button>
        </div>
    </div>
    <div class="zpw-body">
        
        <div class="zpw-dial">
            <input type="text" id="zpwDialInput" placeholder="Phone number…" onkeypress="if(event.key==='Enter')zpwDial()">
            <button onclick="zpwDial()"><i class="bx bx-phone-call"></i> Call</button>
        </div>

        
        <input type="text" class="zpw-dial" style="display:block;width:100%;margin-bottom:.5rem;padding:.35rem .55rem;font-size:.72rem;border:1px solid rgba(0,0,0,.1);border-radius:.35rem;background:var(--bs-body-bg,#fff);color:var(--bs-surface-900)"
               id="zpwSearch" placeholder="Search leads…" oninput="zpwSearchLeads(this.value)">

        
        <div id="zpwSearchResults" style="display:none">
            <div class="zpw-section-title">Search Results</div>
            <div class="zpw-list" id="zpwLeadList"></div>
        </div>

        
        <div id="zpwRecentSection">
            <div class="zpw-section-title">Recent Calls</div>
            <div class="zpw-list" id="zpwRecentList">
                <div style="text-align:center;padding:.5rem;font-size:.65rem;color:var(--bs-surface-400)">
                    <i class="bx bx-loader-alt bx-spin"></i> Loading…
                </div>
            </div>
        </div>
    </div>
    <div class="zpw-footer">
        <a href="<?php echo e(route('zoom.phone')); ?>"><i class="bx bx-expand"></i> Open Full Dialer</a>
    </div>
</div>

<script>
    // ── Toggle Widget ───────────────────────────────────────────────
    function toggleZpWidget() {
        const panel = document.getElementById('zpwPanel');
        const fab = document.getElementById('zpwFab');
        const isOpen = panel.classList.contains('open');

        if (isOpen) {
            panel.classList.remove('open');
            fab.classList.remove('active');
            fab.innerHTML = '<i class="bx bx-phone-call"></i>';
        } else {
            panel.classList.add('open');
            fab.classList.add('active');
            fab.innerHTML = '<i class="bx bx-x"></i>';
            loadRecentCalls();
        }
    }

    // ── Dial ────────────────────────────────────────────────────────
    function zpwDial(number) {
        number = number || document.getElementById('zpwDialInput').value;
        if (!number) return;
        number = number.replace(/[^\d+]/g, '');

        // Use Zoom Phone desktop app protocol
        window.open('zoomphonecall://' + number, '_self');
    }

    // ── Search Leads ────────────────────────────────────────────────
    let zpwSearchTimeout;
    function zpwSearchLeads(query) {
        clearTimeout(zpwSearchTimeout);
        const resultsDiv = document.getElementById('zpwSearchResults');
        const listDiv = document.getElementById('zpwLeadList');
        const recentDiv = document.getElementById('zpwRecentSection');

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            recentDiv.style.display = 'block';
            return;
        }

        resultsDiv.style.display = 'block';
        recentDiv.style.display = 'none';
        listDiv.innerHTML = '<div style="text-align:center;padding:.5rem;font-size:.65rem;color:var(--bs-surface-400)"><i class="bx bx-loader-alt bx-spin"></i></div>';

        zpwSearchTimeout = setTimeout(async () => {
            try {
                const resp = await fetch(`<?php echo e(route('zoom.phone.search-leads')); ?>?q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const leads = await resp.json();

                if (!leads.length) {
                    listDiv.innerHTML = '<div style="text-align:center;padding:.5rem;font-size:.65rem;color:var(--bs-surface-400)">No leads found</div>';
                    return;
                }

                listDiv.innerHTML = leads.map(l => {
                    const ph = l.phone_number || '';
                    return `
                        <div class="zpw-item">
                            <div>
                                <div class="zpw-item-name">${l.cn_name || '—'}</div>
                                <div class="zpw-item-sub">${ph} ${l.state ? '• ' + l.state : ''}</div>
                            </div>
                            ${ph ? `<button class="zpw-item-btn" onclick="zpwDial('${ph}')"><i class="bx bx-phone-call"></i></button>` : ''}
                        </div>
                    `;
                }).join('');
            } catch (e) {
                listDiv.innerHTML = '<div style="text-align:center;padding:.5rem;font-size:.65rem;color:#c84646">Search failed</div>';
            }
        }, 300);
    }

    // ── Load Recent Calls ───────────────────────────────────────────
    let recentLoaded = false;
    async function loadRecentCalls() {
        if (recentLoaded) return;
        const listDiv = document.getElementById('zpwRecentList');

        try {
            const resp = await fetch('<?php echo e(route("settings.reports.zoom-logs")); ?>?_widget=1&per_page=8', {
                headers: { 'Accept': 'text/html' },
            });
            // We'll just show a simple message + link since we can't easily parse the response
            listDiv.innerHTML = `
                <div style="text-align:center;padding:.75rem;font-size:.68rem;color:var(--bs-surface-500)">
                    <i class="bx bx-phone" style="font-size:1.5rem;opacity:.3;display:block;margin-bottom:.35rem"></i>
                    Calls auto-logged via Zoom webhooks.<br>
                    <a href="<?php echo e(route('settings.reports.zoom-logs')); ?>" style="color:var(--bs-gold,#d4af37);font-weight:600">View all call logs →</a>
                </div>
            `;
            recentLoaded = true;
        } catch (e) {
            listDiv.innerHTML = '<div style="text-align:center;padding:.5rem;font-size:.65rem;color:var(--bs-surface-400)">Could not load recent calls</div>';
        }
    }
</script>
<?php /**PATH /var/www/taurus-crm/resources/views/components/zoom-phone-widget.blade.php ENDPATH**/ ?>