{{-- ┌────────────────────────────────────────────────────────────────────────┐
     │  Chill Party Strip — Topbar (next to taurus.mis)                      │
     │  • Only shows Ravens Closers who have NOT made a sale today           │
     │  • Auto-scrolling ticker when >5 chips (loops forever)               │
     │  • Photo modal & motivational popup injected into <body> by JS        │
     │    → avoids sticky/stacking-context trapping issues                   │
     └────────────────────────────────────────────────────────────────────────┘ --}}

@auth
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

{{-- Strip markup lives inside th-left — modals injected into <body> by JS --}}
<div class="cp-topbar" id="chillPartyStrip">
    <span class="cp-topbar-label">Chill Party</span>
    <div class="cp-rail-wrap" id="cpRailWrap">
        <div class="cp-rail" id="cpRail">
            <span style="font-size:.65rem;color:#94a3b8">Loading…</span>
        </div>
    </div>
    <span class="cp-sold-badge d-none" id="cpSoldBadge">0 sold</span>
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

        @hasrole('Ravens Closer')
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
        @endhasrole

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
        var rail      = document.getElementById('cpRail');
        var soldBadge = document.getElementById('cpSoldBadge');
        var closers   = cpData.allClosers || [];
        var noSale    = closers.filter(function(c){ return !c.hasSale; });
        var sold      = closers.filter(function(c){ return  c.hasSale; });

        /* Sold badge */
        if (sold.length) {
            soldBadge.textContent = '✅ ' + sold.length + ' sold';
            soldBadge.classList.remove('d-none');
        } else {
            soldBadge.classList.add('d-none');
        }

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
            statusEl.innerHTML = '<span style="color:#16a34a">✅ Sale closed today</span>';
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

    /* ─────────────────────────────────────────────────────────────────────
       Fetch & poll
    ───────────────────────────────────────────────────────────────────── */
    var firstFetch = true;
    function fetchCpData() {
        fetch('{{ route('api.freeloaders') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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

    var FL_CURRENT_USER = '{{ addslashes(auth()->user()->name) }}';
    fetchCpData();
    setInterval(fetchCpData, POLL_MS);
})();
</script>
@endauth
