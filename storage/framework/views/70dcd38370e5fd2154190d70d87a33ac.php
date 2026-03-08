

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|CEO|Ravens Closer')): ?>
<style>
    /* ── Button ─────────────────────────────────────────── */
    .fl-btn {
        position: relative;
    }
    .fl-trigger {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #f97316 0%, #ec4899 50%, #8b5cf6 100%);
        box-shadow: 0 3px 12px rgba(236,72,153,.45), 0 1px 4px rgba(0,0,0,.2);
        cursor: pointer;
        font-size: 1.35rem;
        line-height: 1;
        transition: transform .15s, box-shadow .15s;
        animation: fl-pulse 2.8s ease-in-out infinite;
    }
    .fl-trigger:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 18px rgba(236,72,153,.6), 0 2px 6px rgba(0,0,0,.25);
        animation: none;
    }
    @keyframes fl-pulse {
        0%,100% { box-shadow: 0 3px 12px rgba(236,72,153,.45); }
        50%      { box-shadow: 0 3px 22px rgba(139,92,246,.7); }
    }

    /* ── Badge ──────────────────────────────────────────── */
    .fl-badge {
        position: absolute;
        top: -4px; right: -4px;
        min-width: 18px; height: 18px;
        background: #ef4444;
        color: #fff;
        font-size: .65rem;
        font-weight: 800;
        border-radius: 99px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        line-height: 1;
        pointer-events: none;
        border: 2px solid var(--bs-card-bg, #fff);
        box-shadow: 0 1px 4px rgba(0,0,0,.3);
    }
    .fl-badge.d-none { display: none !important; }

    /* ── Dropdown ───────────────────────────────────────── */
    .fl-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 245px;
        background: var(--bs-card-bg, #fff);
        border: 1px solid rgba(212,175,55,.18);
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0,0,0,.22);
        z-index: 9999;
        overflow: hidden;
    }
    .fl-dropdown.open { display: block; }

    .fl-header {
        padding: .65rem 1rem;
        border-bottom: 1px solid rgba(212,175,55,.12);
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .04em;
        background: linear-gradient(90deg, rgba(249,115,22,.08), rgba(139,92,246,.08));
        color: var(--bs-gold, #d4af37);
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .fl-header .fl-total {
        margin-left: auto;
        font-size: .63rem;
        font-weight: 600;
        color: var(--bs-surface-400, #94a3b8);
        background: rgba(0,0,0,.07);
        border-radius: 99px;
        padding: 2px 8px;
    }

    .fl-list {
        max-height: 240px;
        overflow-y: auto;
        padding: .35rem 0;
    }
    .fl-item {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: .4rem 1rem;
        font-size: .76rem;
        font-weight: 500;
        color: var(--bs-body-color);
        transition: background .12s;
    }
    .fl-item:hover { background: rgba(212,175,55,.06); }
    .fl-item-avatar {
        width: 24px; height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f97316, #ec4899);
        color: #fff;
        font-size: .62rem;
        font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .fl-empty {
        padding: 1.2rem 1rem;
        text-align: center;
        font-size: .73rem;
        color: #22c55e;
        font-weight: 700;
    }
    .fl-empty .fl-empty-icon {
        font-size: 1.4rem;
        display: block;
        margin-bottom: .3rem;
    }

    .fl-footer {
        padding: .4rem .75rem;
        border-top: 1px solid rgba(212,175,55,.1);
        font-size: .62rem;
        color: var(--bs-surface-400, #94a3b8);
        text-align: center;
    }

    /* ── Freeloader Popup ───────────────────────────────── */
    .fl-popup-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
    }
    .fl-popup-overlay.show { display: flex; }

    .fl-popup {
        background: var(--bs-card-bg, #1a1a2e);
        border-radius: 20px;
        padding: 2rem 2.2rem 1.6rem;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,.5),
                    0 0 0 1px rgba(236,72,153,.25),
                    0 0 40px rgba(139,92,246,.18);
        border: 1px solid rgba(236,72,153,.2);
        animation: fl-popup-in .35s cubic-bezier(.22,1,.36,1) forwards;
        position: relative;
        overflow: hidden;
    }
    .fl-popup::before {
        content: '';
        position: absolute;
        top: -40px; left: 50%;
        transform: translateX(-50%);
        width: 160px; height: 80px;
        background: radial-gradient(ellipse, rgba(236,72,153,.3) 0%, transparent 70%);
        pointer-events: none;
    }
    @keyframes fl-popup-in {
        from { opacity: 0; transform: scale(.85) translateY(20px); }
        to   { opacity: 1; transform: scale(1)  translateY(0); }
    }

    .fl-popup-emoji {
        font-size: 3rem;
        display: block;
        margin-bottom: .5rem;
        animation: fl-emoji-bounce 1s ease-in-out infinite alternate;
    }
    @keyframes fl-emoji-bounce {
        from { transform: translateY(0); }
        to   { transform: translateY(-6px); }
    }

    .fl-popup-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: .55rem;
        line-height: 1.3;
    }
    .fl-popup-body {
        font-size: .85rem;
        color: rgba(255,255,255,.72);
        line-height: 1.55;
        margin-bottom: 1.4rem;
    }
    .fl-popup-cta {
        display: inline-block;
        padding: .6rem 1.8rem;
        border-radius: 99px;
        background: linear-gradient(135deg, #f97316, #ec4899);
        color: #fff;
        font-size: .88rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(236,72,153,.4);
        transition: transform .15s, box-shadow .15s;
        letter-spacing: .02em;
    }
    .fl-popup-cta:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(236,72,153,.55);
    }
    .fl-popup-dismiss {
        display: block;
        margin: .75rem auto 0;
        background: none;
        border: none;
        color: rgba(255,255,255,.35);
        font-size: .72rem;
        cursor: pointer;
        transition: color .15s;
    }
    .fl-popup-dismiss:hover { color: rgba(255,255,255,.6); }
</style>

<div class="position-relative fl-btn" id="freeloadersWidget">
    <button class="fl-trigger" onclick="toggleFreeloarders(event)" title="Freeloaders — Ravens Closers with no sale today">
        😴
        <span class="fl-badge d-none" id="flBadge">0</span>
    </button>

    <div class="fl-dropdown" id="flDropdown">
        <div class="fl-header">
            😴 Freeloaders
            <span class="fl-total" id="flTotal"></span>
        </div>
        <div class="fl-list" id="flList">
            <div class="fl-item" style="justify-content:center;color:var(--bs-surface-400)">
                <i class="bx bx-loader-alt bx-spin"></i>&nbsp;Loading…
            </div>
        </div>
        <div class="fl-footer" id="flFooter">Refreshes every 60 s</div>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Ravens Closer')): ?>
<div class="fl-popup-overlay" id="flPopupOverlay">
    <div class="fl-popup" id="flPopup">
        <span class="fl-popup-emoji" id="flPopupEmoji">😴</span>
        <div class="fl-popup-title" id="flPopupTitle">Still sleeping?</div>
        <div class="fl-popup-body" id="flPopupBody">You haven't made a sale yet today.</div>
        <button class="fl-popup-cta" onclick="closeFlPopup()">Let's get that sale! 🚀</button>
        <button class="fl-popup-dismiss" onclick="closeFlPopup()">dismiss</button>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<script>
(function () {
    const POLL_MS = 60_000;
    let flData = { freeloaders: [], count: 0, total: 0 };

    // ── Popup messages ────────────────────────────────────────────────────
    // Each message: { emoji, title, body }  (alternates tease ↔ encourage)
    const FL_MESSAGES = [
        {
            emoji: '😴',
            title: 'Zero sales? Bold strategy.',
            body: "Let's see how that plays out on the leaderboard… just saying 👀"
        },
        {
            emoji: '📞',
            title: 'The phone called.',
            body: "It said you haven't picked it up enough today. One call is all it takes. Go get it. 💪"
        },
        {
            emoji: '😏',
            title: 'Even your shadow is doing more.',
            body: "It's been following you around all day waiting for a sale. Don't disappoint it."
        },
        {
            emoji: '🤔',
            title: "Legend has it…",
            body: "…there's this thing called a 'closed deal'. Some say it's possible. Why not find out today?"
        },
        {
            emoji: '🏆',
            title: "Champions don't wait for luck.",
            body: "They pick up the phone, they pitch, and they close. You know the drill. NOW GO."
        },
        {
            emoji: '👀',
            title: 'The board is watching.',
            body: "Your name is glowing on the freeloader list. Time to get it off. You've got this! 🔥"
        },
        {
            emoji: '🚀',
            title: "Today isn't over yet.",
            body: "One sale changes everything. Stop reading this and go make it happen. Seriously. GO!"
        },
        {
            emoji: '😅',
            title: "Zero for zero? Respect the consistency.",
            body: "But wouldn't it feel SO much better to close one? We both know the answer. Make the call."
        },
        {
            emoji: '💸',
            title: "Money doesn't close itself.",
            body: "But you can. You've done it before. Stop overthinking and dial that next number. 💪"
        },
        {
            emoji: '🎯',
            title: "One shot, one close.",
            body: "That's all you need. Everybody's rooting for you — now go prove them right! 🙌"
        },
    ];

    // Storage key — rotates daily so it resets each day
    const todayKey = 'fl_popup_' + new Date().toISOString().slice(0, 10);

    function getPopupShownCount() {
        return parseInt(sessionStorage.getItem(todayKey) || '0', 10);
    }
    function incPopupShownCount() {
        sessionStorage.setItem(todayKey, getPopupShownCount() + 1);
    }

    // ── Show popup ───────────────────────────────────────────────────────
    function showFlPopup() {
        const overlay = document.getElementById('flPopupOverlay');
        if (!overlay) return; // not a Ravens Closer

        const shown = getPopupShownCount();
        if (shown >= 3) return; // max 3 times per session

        const msg = FL_MESSAGES[Math.floor(Math.random() * FL_MESSAGES.length)];
        document.getElementById('flPopupEmoji').textContent  = msg.emoji;
        document.getElementById('flPopupTitle').textContent  = msg.title;
        document.getElementById('flPopupBody').textContent   = msg.body;

        overlay.classList.add('show');
        incPopupShownCount();
    }

    window.closeFlPopup = function () {
        document.getElementById('flPopupOverlay')?.classList.remove('show');
        // Schedule next random appearance (only if user is still a freeloader)
        scheduleNextPopup();
    };

    // Close on overlay click (outside the card)
    document.addEventListener('click', function (e) {
        const overlay = document.getElementById('flPopupOverlay');
        const popup   = document.getElementById('flPopup');
        if (overlay?.classList.contains('show') && !popup?.contains(e.target)) {
            window.closeFlPopup();
        }
    });

    // Random delay: 2–7 minutes after dismiss / page load
    function scheduleNextPopup() {
        if (getPopupShownCount() >= 3) return;
        const delayMs = (2 + Math.random() * 5) * 60 * 1000; // 2–7 min
        setTimeout(function () {
            // Only show if the user is still a freeloader at that moment
            if (flData.freeloaders.includes(FL_CURRENT_USER)) {
                showFlPopup();
            } else {
                // They closed a sale — celebrate quietly, no popup needed
            }
        }, delayMs);
    }

    // ── Widget render / fetch ────────────────────────────────────────────
    function renderFreeloarders() {
        const badge  = document.getElementById('flBadge');
        const list   = document.getElementById('flList');
        const total  = document.getElementById('flTotal');
        const footer = document.getElementById('flFooter');

        const count = flData.count;
        const names = flData.freeloaders;

        // Badge
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        // Total pill
        total.textContent = `${flData.total - count} / ${flData.total} sold`;

        // List
        if (count === 0) {
            list.innerHTML = `<div class="fl-empty">
                <span class="fl-empty-icon">🎉</span>
                No freeloaders today!<br>
                <span style="font-weight:400;color:var(--bs-surface-400)">Everyone's pulled their weight.</span>
            </div>`;
        } else {
            list.innerHTML = names.map(name => {
                const initials = name.split(' ').map(p => p[0]).join('').substring(0, 2).toUpperCase();
                return `<div class="fl-item">
                    <span class="fl-item-avatar">${escHtml(initials)}</span>
                    ${escHtml(name)}
                </div>`;
            }).join('');
        }

        // Footer timestamp
        const now = new Date();
        footer.textContent = `Last updated ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')} · refreshes every 60 s`;
    }

    function escHtml(str) {
        return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    let firstFetch = true;

    function fetchFreeloarders() {
        fetch('<?php echo e(route('api.freeloaders')); ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data) {
                flData = data;
                renderFreeloarders();
                // On first fetch, schedule the first popup if user is a freeloader
                if (firstFetch) {
                    firstFetch = false;
                    if (flData.freeloaders.includes(FL_CURRENT_USER)) {
                        scheduleNextPopup();
                    }
                }
            }
        })
        .catch(() => {}); // silent fail — non-critical widget
    }

    window.toggleFreeloarders = function (e) {
        e.stopPropagation();
        const dd = document.getElementById('flDropdown');
        dd.classList.toggle('open');
        // Close other dropdowns
        if (dd.classList.contains('open')) {
            document.getElementById('notificationDropdown')?.classList.remove('show');
        }
    };

    // Close on outside click
    document.addEventListener('click', function (e) {
        const widget = document.getElementById('freeloadersWidget');
        if (widget && !widget.contains(e.target)) {
            document.getElementById('flDropdown')?.classList.remove('open');
        }
    });

    // Current user name — used to detect if the logged-in Ravens Closer is a freeloader
    const FL_CURRENT_USER = '<?php echo e(addslashes(auth()->user()->name)); ?>';

    // Initial load + polling
    fetchFreeloarders();
    setInterval(fetchFreeloarders, POLL_MS);
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/taurus-crm/resources/views/components/freeloaders-widget.blade.php ENDPATH**/ ?>