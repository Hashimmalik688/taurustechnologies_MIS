
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ── Custom Dropdown (.sl-cdd) ── */
.sl-cdd { position: relative; display: inline-flex; }
.sl-cdd-trigger {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .72rem; font-weight: 600;
    padding: .32rem .55rem; padding-right: 1.4rem;
    border-radius: 22px;
    border: 1px solid rgba(0,0,0,.08);
    background: #fff; color: #475569;
    cursor: pointer; outline: none;
    transition: all .15s;
    white-space: nowrap;
    position: relative;
    max-width: 190px;
    text-overflow: ellipsis; overflow: hidden;
}
.sl-cdd-trigger::after {
    content: '';
    position: absolute; right: .5rem; top: 50%;
    transform: translateY(-50%);
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 5px solid #94a3b8;
    transition: transform .15s;
}
.sl-cdd-trigger.active::after { transform: translateY(-50%) rotate(180deg); }
.sl-cdd-trigger:hover, .sl-cdd-trigger.active {
    border-color: #d4af37;
    box-shadow: 0 0 0 2px rgba(212,175,55,.12);
}
.sl-cdd-panel {
    position: absolute; top: calc(100% + 6px); left: 0;
    min-width: 180px; max-width: 300px;
    max-height: 260px; overflow-y: auto;
    background: #fff;
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,.12);
    padding: 5px;
    z-index: 1050;
    display: none;
    scrollbar-width: thin; scrollbar-color: #d4af37 transparent;
}
.sl-cdd-panel::-webkit-scrollbar { width: 4px; }
.sl-cdd-panel::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 2px; }
.sl-cdd-panel.show { display: block; animation: slCddIn .15s ease-out; }
@keyframes slCddIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

.sl-cdd-search {
    width: calc(100% - 8px); margin: 2px 4px 4px;
    padding: .32rem .55rem;
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 12px;
    font-size: .72rem; outline: none;
    color: #334155; background: rgba(248,250,252,.8);
}
.sl-cdd-search:focus { border-color: #d4af37; }
.sl-cdd-search::placeholder { color: #94a3b8; }

.sl-cdd-opt {
    display: block; width: 100%;
    padding: .35rem .65rem;
    border-radius: 12px;
    font-size: .73rem; font-weight: 500;
    color: #334155; cursor: pointer;
    transition: all .1s;
    border: none; background: none;
    text-align: left;
    white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
    margin: 1px 0;
}
.sl-cdd-opt:hover { background: rgba(212,175,55,.1); color: #92760d; }
.sl-cdd-opt.selected {
    background: linear-gradient(135deg, #d4af37, #b8941f);
    color: #fff; font-weight: 700;
}

/* Status color dots for QA options */
.sl-cdd-opt .sl-dot {
    display: inline-block; width: 7px; height: 7px;
    border-radius: 50%; margin-right: 5px; vertical-align: middle;
}

/* ── Flatpickr Theme Overrides ── */
.flatpickr-calendar {
    border-radius: 16px !important;
    box-shadow: 0 12px 40px rgba(0,0,0,.12) !important;
    border: 1px solid rgba(0,0,0,.06) !important;
    font-family: inherit !important;
    overflow: hidden;
}
.flatpickr-months {
    border-radius: 16px 16px 0 0;
    padding-top: 4px;
}
.flatpickr-day {
    border-radius: 10px !important;
    font-weight: 500;
}
.flatpickr-day.selected, .flatpickr-day.selected:hover,
.flatpickr-day.startRange, .flatpickr-day.endRange {
    background: #d4af37 !important;
    border-color: #d4af37 !important;
    color: #0f172a !important;
    font-weight: 700;
}
.flatpickr-day:hover {
    background: rgba(212,175,55,.12) !important;
    border-color: rgba(212,175,55,.2) !important;
}
.flatpickr-day.today {
    border-color: #d4af37 !important;
}
.flatpickr-current-month {
    font-weight: 700 !important;
}

/* ── Dark mode — dropdowns ── */
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-trigger {
    background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-trigger::after { border-top-color: #64748b; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-panel {
    background: #1e293b; border-color: rgba(255,255,255,.08);
    box-shadow: 0 12px 40px rgba(0,0,0,.35);
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-search {
    background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-search::placeholder { color: #475569; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-opt { color: #cbd5e1; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-opt:hover { background: rgba(212,175,55,.15); color: #d4af37; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-opt.selected {
    background: linear-gradient(135deg, #d4af37, #b8941f); color: #0f172a;
}

/* ── Dark mode — Flatpickr ── */
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-calendar {
    background: #1e293b !important;
    border-color: rgba(255,255,255,.08) !important;
    box-shadow: 0 12px 40px rgba(0,0,0,.4) !important;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-day { color: #cbd5e1 !important; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-day:hover {
    background: rgba(212,175,55,.15) !important;
    border-color: rgba(212,175,55,.2) !important;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-day.today { border-color: #d4af37 !important; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-months,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-weekdays,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-month {
    background: rgba(15,23,42,.9) !important;
    color: #e2e8f0 !important;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-weekday { color: #94a3b8 !important; }
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-current-month .flatpickr-monthDropdown-months,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-current-month input.cur-year {
    color: #e2e8f0 !important; background: transparent !important;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-prev-month svg,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-next-month svg,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-prev-month:hover svg,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-next-month:hover svg {
    fill: #94a3b8 !important;
}
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-day.flatpickr-disabled,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-day.prevMonthDay,
:is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .flatpickr-day.nextMonthDay {
    color: #475569 !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ═══ Custom Dropdown: auto-transforms .sl-pill-select ═══
    document.querySelectorAll('.sl-pill-select').forEach(function(select) {
        var wrapper = document.createElement('div');
        wrapper.className = 'sl-cdd';
        select.parentNode.insertBefore(wrapper, select);

        // Trigger
        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'sl-cdd-trigger';
        var selOpt = select.options[select.selectedIndex];
        trigger.textContent = selOpt ? selOpt.textContent.trim() : 'Select…';

        // Panel
        var panel = document.createElement('div');
        panel.className = 'sl-cdd-panel';

        // Search (if > 5 options)
        var searchInput = null;
        if (select.options.length > 5) {
            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'sl-cdd-search';
            searchInput.placeholder = 'Type to search…';
            panel.appendChild(searchInput);
        }

        // Options list
        var list = document.createElement('div');
        list.className = 'sl-cdd-list';
        Array.from(select.options).forEach(function(opt) {
            var item = document.createElement('div');
            item.className = 'sl-cdd-opt' + (opt.selected ? ' selected' : '');
            item.textContent = opt.textContent.trim();
            item.setAttribute('data-value', opt.value);
            item.addEventListener('click', function() {
                select.value = opt.value;
                trigger.textContent = opt.textContent.trim();
                list.querySelectorAll('.sl-cdd-opt').forEach(function(o) { o.classList.remove('selected'); });
                item.classList.add('selected');
                panel.classList.remove('show');
                trigger.classList.remove('active');
                // Submit parent form
                var form = select.closest('form');
                if (form) form.submit();
            });
            list.appendChild(item);
        });
        panel.appendChild(list);

        // Search filtering
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var q = this.value.toLowerCase();
                list.querySelectorAll('.sl-cdd-opt').forEach(function(item) {
                    item.style.display = item.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
                });
            });
            searchInput.addEventListener('click', function(e) { e.stopPropagation(); });
        }

        // Toggle
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.sl-cdd-panel.show').forEach(function(p) {
                if (p !== panel) { p.classList.remove('show'); }
            });
            document.querySelectorAll('.sl-cdd-trigger.active').forEach(function(t) {
                if (t !== trigger) { t.classList.remove('active'); }
            });
            panel.classList.toggle('show');
            trigger.classList.toggle('active');
            if (searchInput && panel.classList.contains('show')) {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                setTimeout(function() { searchInput.focus(); }, 60);
            }
        });

        // Assemble
        select.style.display = 'none';
        wrapper.appendChild(trigger);
        wrapper.appendChild(panel);
        wrapper.appendChild(select);
    });

    // Close all on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sl-cdd')) {
            document.querySelectorAll('.sl-cdd-panel.show').forEach(function(p) { p.classList.remove('show'); });
            document.querySelectorAll('.sl-cdd-trigger.active').forEach(function(t) { t.classList.remove('active'); });
        }
    });

    // ═══ Flatpickr: attach rounded calendar popup to .sl-pill-date ═══
    document.querySelectorAll('.sl-pill-date').forEach(function(input) {
        var parentForm = input.closest('form');
        flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: false,
            allowInput: true,
            disableMobile: true,
            clickOpens: true,
            onChange: function(dates, dateStr) {
                if (dateStr && parentForm) {
                    setTimeout(function() { parentForm.submit(); }, 80);
                }
            }
        });
    });

    // ═══ Flatpickr: attach to .pipe-pill-date (pipeline pages with Apply button) ═══
    document.querySelectorAll('.pipe-pill-date').forEach(function(input) {
        flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: false,
            allowInput: true,
            disableMobile: true,
            clickOpens: true
        });
    });

    // ═══ QA Bubble Select: color-coded status ═══
    function colorQaBubble(sel) {
        var map = { Pending: '#d97706', Good: '#059669', Avg: '#6366f1', Bad: '#dc2626' };
        sel.style.color = map[sel.value] || '#334155';
        sel.style.borderColor = (map[sel.value] || 'rgba(0,0,0,.09)') + (map[sel.value] ? '44' : '');
        sel.style.fontWeight = '700';
    }
    document.querySelectorAll('.qa-status-dropdown').forEach(function(sel) {
        colorQaBubble(sel);
        sel.addEventListener('change', function() { colorQaBubble(sel); });
    });
});
</script>
<?php /**PATH /var/www/taurus-crm/resources/views/partials/sl-filter-assets.blade.php ENDPATH**/ ?>