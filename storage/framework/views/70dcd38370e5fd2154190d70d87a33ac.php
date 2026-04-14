

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
<style>
/* ── Chip strip ───────────────────────────────────────────────────────── */
.cp-topbar {
    display: flex;
    align-items: center;
    gap: .6rem;
    border-left: 1px solid rgba(0,0,0,.09);
    padding-left: 1rem;
    margin-left: .5rem;
    flex-shrink: 0;
    min-width: 0;
    max-width: 480px;
}
.cp-topbar-label {
    font-size: .63rem;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #f97316;
    white-space: nowrap;
    flex-shrink: 0;
}
.cp-rail-wrap {
    position: relative;
    overflow: hidden;
    max-width: 360px;
    min-width: 60px;
    flex: 1 1 auto;
}
/* Fade edges */
.cp-rail-wrap::before,
.cp-rail-wrap::after {
    content: '';
    position: absolute;
    top: 0; bottom: 0;
    width: 24px;
    z-index: 2;
    pointer-events: none;
}
.cp-rail-wrap::before { left: 0;  background: linear-gradient(to right,  var(--cp-bg, #fff), transparent); }
.cp-rail-wrap::after  { right: 0; background: linear-gradient(to left, var(--cp-bg, #fff), transparent); }

/* Theme bg vars for the fade edges */
[data-theme="midnight-black"]  { --cp-bg: #050505; }
[data-theme="emerald-glass"]   { --cp-bg: #061209; }
[data-theme="ocean-blue"]      { --cp-bg: #060e1a; }
[data-theme="royal-purple"]    { --cp-bg: #0d0720; }
[data-theme="rose-gold"]       { --cp-bg: #1a0d12; }
[data-theme="copper-steel"]    { --cp-bg: #0d0d10; }

/* Static rail (few chips) */
.cp-rail {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
/* Scrolling ticker mode */
.cp-rail.ticker {
    display: flex;
    width: max-content;
    animation: cp-ticker linear infinite;
}
@keyframes cp-ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
/* Pause on hover */
.cp-rail-wrap:hover .cp-rail.ticker { animation-play-state: paused; }

/* Individual chip */
.cp-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 26px;
    border-radius: 99px;
    padding: 0 9px 0 3px;
    border: 1.5px solid rgba(249,115,22,.5);
    background: rgba(249,115,22,.07);
    white-space: nowrap;
    flex-shrink: 0;
    animation: cp-chip-pulse 3s ease-in-out infinite;
    transition: transform .15s;
}
.cp-chip:hover { transform: scale(1.06); animation: none; box-shadow: 0 2px 10px rgba(249,115,22,.3); }
@keyframes cp-chip-pulse {
    0%,100% { box-shadow: none; }
    50%      { box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
}
.cp-chip-photo {
    width: 20px; height: 20px; border-radius: 50%; object-fit: cover;
    border: 1.5px solid rgba(249,115,22,.45); flex-shrink: 0; display: block;
}
.cp-chip-init {
    width: 20px; height: 20px; border-radius: 50%;
    background: linear-gradient(135deg, #f97316, #ec4899);
    color: #fff; font-size: .52rem; font-weight: 800;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cp-chip-name {
    font-size: .69rem; font-weight: 700; color: #c2410c;
}
.cp-all-sold {
    font-size: .69rem; font-weight: 700; color: #16a34a; white-space: nowrap;
}
/* Toggle photo button */
.cp-photo-toggle {
    flex-shrink: 0;
    background: none;
    border: 1px solid rgba(249,115,22,.35);
    border-radius: 6px;
    padding: 2px 6px;
    cursor: pointer;
    font-size: .6rem;
    color: #f97316;
    line-height: 1;
    transition: background .15s, border-color .15s;
    display: inline-flex; align-items: center; gap: 3px;
}
.cp-photo-toggle:hover { background: rgba(249,115,22,.12); border-color: rgba(249,115,22,.7); }
.cp-photo-toggle.toggled { background: rgba(249,115,22,.15); border-color: rgba(249,115,22,.7); }
/* When photos hidden: hide img, show initials (only for chips that have a photo) */
.cp-photos-hidden .cp-chip.cp-has-photo .cp-chip-photo { display: none !important; }
.cp-photos-hidden .cp-chip.cp-has-photo .cp-chip-init  { display: inline-flex !important; }
/* Default: photo shown, initials hidden — only when chip has a photo */
.cp-chip.cp-has-photo .cp-chip-init { display: none; }
.cp-sold-badge {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .64rem; font-weight: 700; color: #16a34a;
    background: rgba(22,163,74,.08); border: 1px solid rgba(22,163,74,.25);
    border-radius: 99px; padding: 2px 8px; white-space: nowrap; flex-shrink: 0;
}

/* ── Closed pill + dropdown ────────────────────────────────────────────── */
.cp-closed-wrap {
    position: relative;
    flex-shrink: 0;
    border-left: 1px solid rgba(0,0,0,.09);
    padding-left: 1rem;
    margin-left: .25rem;
}
.cp-closed-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 26px;
    padding: 0 10px 0 7px;
    border-radius: 99px;
    border: 1.5px solid rgba(22,163,74,.45);
    background: rgba(22,163,74,.08);
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s, border-color .15s;
    user-select: none;
}
.cp-closed-pill:hover,
.cp-closed-pill.open {
    background: rgba(22,163,74,.16);
    border-color: rgba(22,163,74,.7);
}
.cp-closed-pill-avatars {
    display: flex;
    align-items: center;
}
.cp-closed-pill-avatars .cp-pav {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid rgba(22,163,74,.5);
    margin-left: -5px;
    flex-shrink: 0;
    background: linear-gradient(135deg,#16a34a,#10b981);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .45rem;
    font-weight: 800;
    color: #fff;
}
.cp-closed-pill-avatars .cp-pav:first-child { margin-left: 0; }
.cp-closed-pill-label {
    font-size: .68rem;
    font-weight: 700;
    color: #15803d;
}
.cp-closed-pill-caret {
    font-size: .55rem;
    color: #16a34a;
    opacity: .7;
    transition: transform .2s;
}
.cp-closed-pill.open .cp-closed-pill-caret { transform: rotate(180deg); }
/* Dropdown panel */
.cp-closed-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    min-width: 220px;
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(22,163,74,.2);
    border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,.18), 0 0 0 1px rgba(22,163,74,.08);
    z-index: 1050;
    overflow: hidden;
    animation: cpDdIn .18s ease-out;
}
.cp-closed-dropdown.open { display: block; }
@keyframes cpDdIn {
    from { opacity:0; transform: translateY(-6px) scale(.98); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
.cp-dd-header {
    padding: .55rem .85rem .4rem;
    font-size: .6rem;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: #16a34a;
    border-bottom: 1px solid rgba(22,163,74,.12);
    background: rgba(22,163,74,.05);
}
.cp-dd-list {
    max-height: 260px;
    overflow-y: auto;
    padding: .35rem 0;
}
.cp-dd-list::-webkit-scrollbar { width: 3px; }
.cp-dd-list::-webkit-scrollbar-thumb { background: rgba(22,163,74,.3); border-radius: 3px; }
.cp-dd-row {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .38rem .85rem;
    cursor: pointer;
    transition: background .12s;
}
.cp-dd-row:hover { background: rgba(22,163,74,.06); }
.cp-dd-photo {
    width: 32px; height: 32px; border-radius: 50%; object-fit: cover;
    border: 2px solid rgba(22,163,74,.4); flex-shrink: 0;
}
.cp-dd-init {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #10b981);
    color: #fff; font-size: .6rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; border: 2px solid rgba(22,163,74,.4);
}
.cp-dd-info { flex: 1; min-width: 0; }
.cp-dd-name {
    font-size: .75rem; font-weight: 700;
    color: var(--bs-body-color, #1e293b);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.cp-dd-sub {
    font-size: .62rem; color: #64748b; margin-top: 1px;
}
.cp-dd-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 22px; height: 22px;
    border-radius: 99px;
    background: linear-gradient(135deg, #16a34a, #10b981);
    color: #fff; font-size: .62rem; font-weight: 800;
    padding: 0 5px; flex-shrink: 0;
}
/* dark overrides */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],
    [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cp-closed-wrap {
    border-left-color: rgba(255,255,255,.08);
}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],
    [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cp-closed-pill-label {
    color: #4ade80;
}

/* dark chipname */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],
    [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cp-chip-name {
    color: #fb923c;
}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],
    [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cp-topbar {
    border-left-color: rgba(255,255,255,.08);
}
</style>


<div class="cp-topbar" id="chillPartyStrip">
    <span class="cp-topbar-label">Chill Party</span>
    <div class="cp-rail-wrap" id="cpRailWrap">
        <div class="cp-rail" id="cpRail">
            <span style="font-size:.65rem;color:#94a3b8">Loading…</span>
        </div>
    </div>
</div>


<div class="cp-closed-wrap d-none" id="cpClosedWrap">
    <div class="cp-closed-pill" id="cpClosedPill" onclick="window.toggleCpClosed()">
        <span class="cp-closed-pill-avatars" id="cpClosedAvatars"></span>
        <span class="cp-closed-pill-label" id="cpClosedLabel">0 closed</span>
        <span class="cp-closed-pill-caret">▾</span>
    </div>
    <div class="cp-closed-dropdown" id="cpClosedDropdown">
        <div class="cp-dd-header">🔥 Closed today</div>
        <div class="cp-dd-list" id="cpClosedList"></div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ─────────────────────────────────────────────────────────────────────
       Inject modals into <body> so they escape the sticky topbar stacking
       context. This is the key fix for position:fixed not working.
    ───────────────────────────────────────────────────────────────────── */
    function injectModals() {
        if (document.getElementById('cpPhotoOverlay')) return; // already injected

        /* Photo preview modal */
        var photoHtml = [
            '<div id="cpPhotoOverlay" style="',
                'display:none;position:fixed;inset:0;',
                'background:rgba(0,0,0,.65);',
                'z-index:999999;',
                'align-items:center;justify-content:center;',
                'backdrop-filter:blur(5px);',
            '">',
              '<div id="cpPhotoCard" style="',
                  'background:var(--bs-card-bg,#1a1a2e);',
                  'border-radius:18px;padding:1.5rem 1.5rem 1.2rem;',
                  'text-align:center;max-width:240px;width:90%;',
                  'box-shadow:0 20px 60px rgba(0,0,0,.5);',
                  'border:1px solid rgba(212,175,55,.2);',
              '">',
                '<div id="cpPhotoContent"></div>',
                '<div id="cpPhotoName"   style="font-size:.9rem;font-weight:700;color:var(--bs-body-color);margin:.6rem 0 .2rem"></div>',
                '<div id="cpPhotoStatus" style="font-size:.73rem;font-weight:600;margin-bottom:.8rem"></div>',
                '<button onclick="window.closeCpPhoto()" style="',
                    'background:none;border:1px solid rgba(100,116,139,.25);',
                    'border-radius:8px;color:#94a3b8;font-size:.72rem;',
                    'padding:.3rem .9rem;cursor:pointer;',
                '">Close</button>',
              '</div>',
            '</div>',
        ].join('');

        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Ravens Closer')): ?>
        /* Motivational popup — only for Ravens Closers */
        var popupHtml = [
            '<div id="flPopupOverlay" style="',
                'display:none;position:fixed;inset:0;',
                'background:rgba(0,0,0,.55);z-index:999998;',
                'align-items:center;justify-content:center;',
                'backdrop-filter:blur(3px);',
            '">',
              '<div id="flPopup" style="',
                  'background:var(--bs-card-bg,#1a1a2e);',
                  'border-radius:20px;padding:2rem 2.2rem 1.6rem;',
                  'max-width:400px;width:90%;text-align:center;',
                  'box-shadow:0 20px 60px rgba(0,0,0,.5),0 0 0 1px rgba(236,72,153,.25);',
                  'border:1px solid rgba(236,72,153,.2);position:relative;overflow:hidden;',
              '">',
                '<span id="flPopupEmoji"  style="font-size:3rem;display:block;margin-bottom:.5rem">🏖️</span>',
                '<div  id="flPopupTitle" style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:.55rem;line-height:1.3"></div>',
                '<div  id="flPopupBody"  style="font-size:.85rem;color:rgba(255,255,255,.72);line-height:1.55;margin-bottom:1.4rem"></div>',
                '<button onclick="window.closeFlPopup()" style="',
                    'display:inline-block;padding:.6rem 1.8rem;border-radius:99px;',
                    'background:linear-gradient(135deg,#f97316,#ec4899);',
                    'color:#fff;font-size:.88rem;font-weight:700;border:none;cursor:pointer;',
                '">Let\'s get that sale! 🚀</button>',
                '<button onclick="window.closeFlPopup()" style="',
                    'display:block;margin:.75rem auto 0;background:none;border:none;',
                    'color:rgba(255,255,255,.35);font-size:.72rem;cursor:pointer;',
                '">dismiss</button>',
              '</div>',
            '</div>',
        ].join('');
        document.body.insertAdjacentHTML('beforeend', popupHtml);
        <?php endif; ?>

        document.body.insertAdjacentHTML('beforeend', photoHtml);

        /* Close on outside click */
        document.addEventListener('click', function (e) {
            var po = document.getElementById('flPopupOverlay');
            if (po && po.style.display === 'flex' && !document.getElementById('flPopup').contains(e.target)) {
                window.closeFlPopup();
            }
            var pho = document.getElementById('cpPhotoOverlay');
            if (pho && pho.style.display === 'flex' && !document.getElementById('cpPhotoCard').contains(e.target)) {
                window.closeCpPhoto();
            }
            /* Close closed-pill dropdown on outside click */
            var cpw = document.getElementById('cpClosedWrap');
            if (cpw && !cpw.contains(e.target)) {
                window.closeCpClosed();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectModals);
    } else {
        injectModals();
    }

    /* ─────────────────────────────────────────────────────────────────────
       App state + polling
    ───────────────────────────────────────────────────────────────────── */
    var POLL_MS = 60000;
    var cpData  = { freeloaders: [], allClosers: [], count: 0, total: 0 };
    var TICKER_THRESHOLD = 5; // activate marquee when more than this many chips

    /* ─────────────────────────────────────────────────────────────────────
       Motivational popup messages
    ───────────────────────────────────────────────────────────────────── */
    var FL_MSG = [
        { e:'😴', t:'Zero sales? Bold strategy.',              b:"Let's see how that plays out on the leaderboard… just saying 👀" },
        { e:'📞', t:'The phone called.',                       b:"It said you haven't picked it up enough today. One call is all it takes. Go get it. 💪" },
        { e:'😏', t:'Even your shadow is doing more.',         b:"It's been following you around all day waiting for a sale. Don't disappoint it." },
        { e:'🤔', t:'Legend has it…',                          b:"…there's this thing called a 'closed deal'. Some say it's possible. Why not find out today?" },
        { e:'🏆', t:"Champions don't wait for luck.",          b:"They pick up the phone, they pitch, and they close. You know the drill. NOW GO." },
        { e:'👀', t:'The board is watching.',                  b:"Your name is on the Chill Party list. Time to get it off. You've got this! 🔥" },
        { e:'🚀', t:"Today isn't over yet.",                   b:"One sale changes everything. Stop reading this and go make it happen. Seriously. GO!" },
        { e:'😅', t:"Zero for zero? Respect the consistency.", b:"But wouldn't it feel SO much better to close one? Make the call." },
        { e:'💸', t:"Money doesn't close itself.",             b:"But you can. Stop overthinking and dial that next number. 💪" },
        { e:'🎯', t:"One shot, one close.",                    b:"That's all you need. Everybody's rooting for you — now prove them right! 🙌" },
    ];

    var POPUP_KEY = 'fl_popup_' + new Date().toISOString().slice(0,10);
    function popupCount()    { return parseInt(sessionStorage.getItem(POPUP_KEY) || '0', 10); }
    function incPopupCount() { sessionStorage.setItem(POPUP_KEY, popupCount() + 1); }

    function showFlPopup() {
        var overlay = document.getElementById('flPopupOverlay');
        if (!overlay || popupCount() >= 3) return;
        var msg = FL_MSG[Math.floor(Math.random() * FL_MSG.length)];
        document.getElementById('flPopupEmoji').textContent = msg.e;
        document.getElementById('flPopupTitle').textContent = msg.t;
        document.getElementById('flPopupBody').textContent  = msg.b;
        overlay.style.display = 'flex';
        incPopupCount();
    }
    window.closeFlPopup = function () {
        var o = document.getElementById('flPopupOverlay');
        if (o) o.style.display = 'none';
        scheduleNextPopup();
    };
    function scheduleNextPopup() {
        if (popupCount() >= 3) return;
        setTimeout(function () {
            if (cpData.freeloaders.indexOf(FL_CURRENT_USER) !== -1) showFlPopup();
        }, (2 + Math.random() * 5) * 60000);
    }

    /* ─────────────────────────────────────────────────────────────────────
       HTML escape
    ───────────────────────────────────────────────────────────────────── */
    function esc(s) {
        if (!s) return '';
        return String(s).replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    /* ─────────────────────────────────────────────────────────────────────
       Render chip strip + ticker logic
    ───────────────────────────────────────────────────────────────────── */
    function buildChipHtml(list) {
        return list.map(function(c) {
            var first    = esc(c.name.split(' ')[0]);
            var initials = c.name.split(' ').map(function(p){ return p[0]; }).join('').substring(0,2).toUpperCase();
            var avatar   = c.photo
                ? '<img class="cp-chip-photo" src="' + esc(c.photo) + '" alt="' + esc(c.name) + '">'
                : '<span class="cp-chip-init">' + esc(initials) + '</span>';
            return '<div class="cp-chip" title="' + esc(c.name) + '">' + avatar + '<span class="cp-chip-name">' + first + '</span></div>';
        }).join('');
    }

    function renderStrip() {
        var rail    = document.getElementById('cpRail');
        var closers = cpData.allClosers || [];
        var noSale       = closers.filter(function(c){ return !c.hasSale; });
        var sold         = closers.filter(function(c){ return  c.hasSale; });

/* ── Closed pill ────────────────────────────────────────────────── */
        var closedWrap = document.getElementById('cpClosedWrap');
        var closedList = document.getElementById('cpClosedList');
        var closedLabel  = document.getElementById('cpClosedLabel');
        var closedAvatars = document.getElementById('cpClosedAvatars');

        if (sold.length && closedWrap) {
            /* Update pill label */
            var totalSales = sold.reduce(function(sum, c){ return sum + (c.saleCount || 1); }, 0);
            closedLabel.textContent = sold.length + ' closed · ' + totalSales + ' sale' + (totalSales !== 1 ? 's' : '');

            /* Stacked mini-avatars in pill (max 4) */
            var avatarSlice = sold.slice(0, 4);
            closedAvatars.innerHTML = avatarSlice.map(function(c) {
                var initials = c.name.split(' ').map(function(p){ return p[0]; }).join('').substring(0,2).toUpperCase();
                return c.photo
                    ? '<img class="cp-pav" src="' + esc(c.photo) + '" alt="' + esc(c.name) + '">'
                    : '<span class="cp-pav">' + esc(initials) + '</span>';
            }).join('');

            /* Dropdown rows — sorted by saleCount desc */
            var sortedSold = sold.slice().sort(function(a,b){ return b.saleCount - a.saleCount; });
            closedList.innerHTML = sortedSold.map(function(c) {
                var initials = c.name.split(' ').map(function(p){ return p[0]; }).join('').substring(0,2).toUpperCase();
                var avatar   = c.photo
                    ? '<img class="cp-dd-photo" src="' + esc(c.photo) + '" alt="' + esc(c.name) + '">'
                    : '<div class="cp-dd-init">' + esc(initials) + '</div>';
                var saleWord = c.saleCount === 1 ? 'sale' : 'sales';
                return '<div class="cp-dd-row" onclick="window.showCpPhoto(' + JSON.stringify(c.name) + ',' + JSON.stringify(c.photo || '') + ');window.closeCpClosed();">'
                    + avatar
                    + '<div class="cp-dd-info">'
                        + '<div class="cp-dd-name">' + esc(c.name) + '</div>'
                        + '<div class="cp-dd-sub">Today\'s closer</div>'
                    + '</div>'
                    + '<span class="cp-dd-badge">' + c.saleCount + '</span>'
                    + '</div>';
            }).join('');

            closedWrap.classList.remove('d-none');
        } else if (closedWrap) {
            closedWrap.classList.add('d-none');
        }

        /* ── Chill Party strip ──────────────────────────────────────────── */
        if (!noSale.length) {
            rail.className = 'cp-rail';
            rail.style.animation = '';
            rail.innerHTML = '<span class="cp-all-sold">🎉 Everyone sold today!</span>';
            return;
        }

        var chipsHtml = buildChipHtml(noSale);

        if (noSale.length > TICKER_THRESHOLD) {
            /* Ticker mode — duplicate chips for seamless loop */
            rail.className = 'cp-rail ticker';
            rail.innerHTML = chipsHtml + chipsHtml; // duplicate
            /* Speed: ~60px/s — adjust duration based on how much content */
            var chipWidth = 90; // approx px per chip incl gap
            var totalPx   = noSale.length * chipWidth;
            var duration  = Math.max(8, totalPx / 60); // seconds
            rail.style.animationDuration = duration + 's';
        } else {
            /* Static mode */
            rail.className = 'cp-rail';
            rail.style.animation = '';
            rail.innerHTML = chipsHtml;
        }
    }

    /* ─────────────────────────────────────────────────────────────────────
       Photo preview — called by EMS "Photo" button
    ───────────────────────────────────────────────────────────────────── */
    window.showCpPhoto = function(name, photoUrl) {
        var overlay   = document.getElementById('cpPhotoOverlay');
        var contentEl = document.getElementById('cpPhotoContent');
        var nameEl    = document.getElementById('cpPhotoName');
        var statusEl  = document.getElementById('cpPhotoStatus');
        if (!overlay) return;

        nameEl.textContent = name;

        var closer = (cpData.allClosers || []).filter(function(c){ return c.name === name; })[0];
        if (closer && closer.hasSale) {
            var cnt = closer.saleCount || 1;
            statusEl.innerHTML = '<span style="color:#16a34a">🔥 ' + cnt + ' sale' + (cnt !== 1 ? 's' : '') + ' closed today</span>';
        } else if (closer) {
            statusEl.innerHTML = '<span style="color:#f97316">🏖️ In Chill Party — no sale yet</span>';
        } else {
            statusEl.textContent = '';
        }

        if (photoUrl) {
            contentEl.innerHTML = '<img src="' + esc(photoUrl) + '" alt="' + esc(name) + '" style="' +
                'width:150px;height:150px;border-radius:50%;object-fit:cover;' +
                'border:3px solid #f97316;box-shadow:0 0 24px rgba(249,115,22,.4);' +
                'display:block;margin:0 auto .8rem;">';
        } else {
            var init = name.split(' ').map(function(p){ return p[0]; }).join('').substring(0,2).toUpperCase();
            contentEl.innerHTML = '<div style="' +
                'width:150px;height:150px;border-radius:50%;margin:0 auto .8rem;' +
                'background:linear-gradient(135deg,#f97316,#ec4899);' +
                'display:flex;align-items:center;justify-content:center;' +
                'font-size:3rem;font-weight:800;color:#fff;border:3px solid #f97316;">' +
                esc(init) + '</div>';
        }

        overlay.style.display = 'flex';
    };
    window.closeCpPhoto = function() {
        var o = document.getElementById('cpPhotoOverlay');
        if (o) o.style.display = 'none';
    };

    /* ── Closed pill dropdown toggle ─────────────────────────────────── */
    window.toggleCpClosed = function() {
        var pill = document.getElementById('cpClosedPill');
        var dd   = document.getElementById('cpClosedDropdown');
        if (!pill || !dd) return;
        var isOpen = dd.classList.contains('open');
        if (isOpen) {
            dd.classList.remove('open');
            pill.classList.remove('open');
        } else {
            dd.classList.add('open');
            pill.classList.add('open');
        }
    };
    window.closeCpClosed = function() {
        var pill = document.getElementById('cpClosedPill');
        var dd   = document.getElementById('cpClosedDropdown');
        if (pill) pill.classList.remove('open');
        if (dd)   dd.classList.remove('open');
    };

    /* ─────────────────────────────────────────────────────────────────────
       Fetch & poll
    ───────────────────────────────────────────────────────────────────── */
    var firstFetch = true;
    function fetchCpData() {
        fetch('<?php echo e(route('api.freeloaders')); ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r){ return r.ok ? r.json() : null; })
        .then(function(data) {
            if (!data) return;
            cpData = {
                freeloaders: data.freeloaders || [],
                allClosers:  data.allClosers  || data.chillParty || [],
                count:       data.count  || 0,
                total:       data.total  || 0,
            };
            renderStrip();
            if (firstFetch) {
                firstFetch = false;
                if (cpData.freeloaders.indexOf(FL_CURRENT_USER) !== -1) scheduleNextPopup();
            }
        })
        .catch(function(){});
    }

    var FL_CURRENT_USER = '<?php echo e(addslashes(auth()->user()->name)); ?>';
    fetchCpData();
    setInterval(fetchCpData, POLL_MS);
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/taurus-crm/resources/views/components/freeloaders-widget.blade.php ENDPATH**/ ?>