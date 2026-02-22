<!-- Sticky Notes — floating draggable notes, triggered from top bar -->
<style>
/* ── Floating Sticky Note ──────────────────────── */
.sn-note {
    position: fixed;
    width: 280px;
    min-height: 160px;
    border-radius: 4px 4px 12px 12px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
    z-index: 10000;
    display: none;
    flex-direction: column;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    transition: box-shadow 0.2s ease;
    overflow: hidden;
}
.sn-note.visible { display: flex; }
.sn-note:hover { box-shadow: 0 12px 36px rgba(0,0,0,0.18), 0 4px 12px rgba(0,0,0,0.08); }
.sn-note.dragging {
    z-index: 10001 !important;
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
    opacity: 0.92;
    transition: none !important;
}

/* Top Color Strip */
.sn-strip {
    height: 6px;
    flex-shrink: 0;
    transition: background 0.2s;
}

/* Header */
.sn-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px 4px;
    cursor: move;
    user-select: none;
}
.sn-date {
    font-size: 0.65rem;
    font-weight: 600;
    color: #94a3b8;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.sn-btns {
    display: flex;
    gap: 2px;
    opacity: 0;
    transition: opacity 0.15s;
}
.sn-note:hover .sn-btns { opacity: 1; }
.sn-btns button {
    background: none;
    border: none;
    width: 22px;
    height: 22px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.8rem;
    color: #64748b;
    transition: all 0.15s;
    padding: 0;
}
.sn-btns button:hover { background: rgba(0,0,0,0.06); }
.sn-btns .sn-del:hover { background: rgba(239,68,68,0.12); color: #ef4444; }

/* Content */
.sn-body {
    flex: 1;
    padding: 4px 12px 6px;
}
.sn-text {
    width: 100%;
    min-height: 70px;
    max-height: 200px;
    background: transparent;
    border: none;
    resize: none;
    font-size: 0.82rem;
    line-height: 1.6;
    color: #334155;
    outline: none;
    overflow-y: auto;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    cursor: text;
}
.sn-text::placeholder { color: #b0b8c8; }

/* Color Picker Row */
.sn-palette {
    display: flex;
    gap: 6px;
    padding: 6px 12px 10px;
    border-top: 1px solid rgba(0,0,0,0.04);
}
.sn-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.15s;
    flex-shrink: 0;
}
.sn-dot:hover { transform: scale(1.25); }
.sn-dot.active { border-color: #475569; box-shadow: 0 0 0 2px rgba(71,85,105,0.15); }

/* ── Color Themes ──────────────────────────────── */
.sn-note[data-color="#fffacd"] { background: #fffef5; }
.sn-note[data-color="#fffacd"] .sn-strip { background: linear-gradient(90deg, #f0c800, #d4af37); }

.sn-note[data-color="#ffd9e8"] { background: #fff5f9; }
.sn-note[data-color="#ffd9e8"] .sn-strip { background: linear-gradient(90deg, #f472b6, #ec4899); }

.sn-note[data-color="#d4e9f7"] { background: #f0f7ff; }
.sn-note[data-color="#d4e9f7"] .sn-strip { background: linear-gradient(90deg, #60a5fa, #3b82f6); }

.sn-note[data-color="#d4edda"] { background: #f0fdf4; }
.sn-note[data-color="#d4edda"] .sn-strip { background: linear-gradient(90deg, #4ade80, #22c55e); }

.sn-note[data-color="#ffe5cc"] { background: #fff8f0; }
.sn-note[data-color="#ffe5cc"] .sn-strip { background: linear-gradient(90deg, #fb923c, #f97316); }

.sn-note[data-color="#e8d4f0"] { background: #faf5ff; }
.sn-note[data-color="#e8d4f0"] .sn-strip { background: linear-gradient(90deg, #c084fc, #a855f7); }

/* ── New Note FAB ──────────────────────────────── */
#snFab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 10002;
    display: none;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #d4af37, #c49b2c);
    color: #fff;
    border: none;
    border-radius: 24px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(212,175,55,0.35);
    transition: all 0.2s;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}
#snFab.visible { display: inline-flex; }
#snFab:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(212,175,55,0.45);
}
#snFab i { font-size: 1rem; }

/* ── Drag helpers ──────────────────────────────── */
body.sn-dragging {
    user-select: none !important;
    cursor: move !important;
}
</style>

<!-- New Note FAB button -->
<button id="snFab"><i class="bx bx-plus"></i> New Note</button>

<script>
(function() {
    'use strict';

    function initStickyNotes() {
        var toggleBtn = document.getElementById('sticky-notes-toggle');
        var fab       = document.getElementById('snFab');

        if (!toggleBtn) {
            console.warn('[StickyNotes] Toggle button not found in topbar');
            return;
        }
        if (!fab) {
            console.warn('[StickyNotes] FAB button not found');
            return;
        }

        var notesVisible = false;
        var notes = [];
        var colors = [
            { name: 'yellow', value: '#fffacd' },
            { name: 'pink',   value: '#ffd9e8' },
            { name: 'blue',   value: '#d4e9f7' },
            { name: 'green',  value: '#d4edda' },
            { name: 'orange', value: '#ffe5cc' },
            { name: 'purple', value: '#e8d4f0' }
        ];

        var draggedNote = null, offsetX = 0, offsetY = 0, isDragging = false;

        /* ── CSRF Token ─────────────────────────── */
        function getToken() {
            var el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : '';
        }

        /* ── Toggle from top-bar ────────────────── */
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            notesVisible = !notesVisible;

            if (notesVisible) {
                toggleBtn.style.color = 'var(--gold, #d4af37)';
                toggleBtn.style.background = 'rgba(212,175,55,0.1)';
                fab.classList.add('visible');
                loadNotes();
            } else {
                toggleBtn.style.color = '';
                toggleBtn.style.background = '';
                fab.classList.remove('visible');
                hideAllNotes();
            }
        });

        /* Escape to hide */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && notesVisible) {
                notesVisible = false;
                toggleBtn.style.color = '';
                toggleBtn.style.background = '';
                fab.classList.remove('visible');
                hideAllNotes();
            }
        });

        /* FAB: new note */
        fab.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            createNote();
        };

        /* ── Drag system ────────────────────────── */
        document.addEventListener('mousemove', function(e) {
            if (!draggedNote || !isDragging) return;
            e.preventDefault();
            var newX = e.clientX - offsetX;
            var newY = e.clientY - offsetY;
            newX = Math.max(5, Math.min(window.innerWidth - 290, newX));
            newY = Math.max(5, Math.min(window.innerHeight - 80, newY));
            draggedNote.style.left = newX + 'px';
            draggedNote.style.top  = newY + 'px';
        });

        document.addEventListener('mouseup', function() {
            if (!draggedNote || !isDragging) return;
            isDragging = false;
            draggedNote.classList.remove('dragging');
            document.body.classList.remove('sn-dragging');
            var noteId = draggedNote.getAttribute('data-note-id');
            if (noteId) {
                try {
                    localStorage.setItem('sn-pos-' + noteId, JSON.stringify({
                        left: draggedNote.style.left,
                        top: draggedNote.style.top
                    }));
                } catch(ex) {}
            }
            draggedNote = null;
        });

        /* ── AJAX helpers ───────────────────────── */
        function apiRequest(url, method, body) {
            var opts = {
                method: method || 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': getToken(),
                    'Accept': 'application/json'
                }
            };
            if (body) {
                opts.headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(body);
            }
            return fetch(url, opts).then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status + ' on ' + method + ' ' + url);
                return r.json();
            });
        }

        function loadNotes() {
            apiRequest('/sticky-notes', 'GET')
            .then(function(data) {
                notes = Array.isArray(data) ? data : [];
                renderNotes();
            })
            .catch(function(err) { console.error('[StickyNotes] load:', err); });
        }

        function createNote() {
            apiRequest('/sticky-notes', 'POST', { content: 'New note...', color: '#fffacd' })
            .then(function(data) {
                if (data && data.success && data.note) {
                    notes.push(data.note);
                    renderNotes();
                }
            })
            .catch(function(err) { console.error('[StickyNotes] create:', err); });
        }

        function updateNote(noteId, updates) {
            apiRequest('/sticky-notes/' + noteId, 'PUT', updates)
            .then(function(data) {
                if (data && data.success) {
                    for (var i = 0; i < notes.length; i++) {
                        if (notes[i].id === noteId) {
                            for (var k in updates) { notes[i][k] = updates[k]; }
                            break;
                        }
                    }
                }
            })
            .catch(function(err) { console.error('[StickyNotes] update:', err); });
        }

        function deleteNote(noteId) {
            if (!confirm('Delete this note?')) return;
            apiRequest('/sticky-notes/' + noteId, 'DELETE')
            .then(function(data) {
                if (data && data.success) {
                    notes = notes.filter(function(n) { return n.id !== noteId; });
                    renderNotes();
                }
            })
            .catch(function(err) { console.error('[StickyNotes] delete:', err); });
        }

        /* ── Hide all ───────────────────────────── */
        function hideAllNotes() {
            var els = document.querySelectorAll('.sn-note');
            for (var i = 0; i < els.length; i++) els[i].parentNode.removeChild(els[i]);
        }

        /* ── Render ─────────────────────────────── */
        function renderNotes() {
            hideAllNotes();
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

            for (var idx = 0; idx < notes.length; idx++) {
                (function(note, index) {
                    var el = document.createElement('div');
                    el.className = 'sn-note' + (notesVisible ? ' visible' : '');
                    el.setAttribute('data-note-id', note.id);
                    el.setAttribute('data-color', note.color || '#fffacd');

                    /* Position: saved or cascade */
                    var savedRaw = null;
                    try { savedRaw = localStorage.getItem('sn-pos-' + note.id); } catch(ex) {}
                    if (savedRaw) {
                        try {
                            var pos = JSON.parse(savedRaw);
                            el.style.left = pos.left;
                            el.style.top  = pos.top;
                        } catch(ex) {
                            el.style.left = (Math.max(100, window.innerWidth - 340 - (index * 30))) + 'px';
                            el.style.top  = (80 + (index * 25)) + 'px';
                        }
                    } else {
                        el.style.left = (Math.max(100, window.innerWidth - 340 - (index * 30))) + 'px';
                        el.style.top  = (80 + (index * 25)) + 'px';
                    }

                    var dateStr = '';
                    try {
                        var d = new Date(note.created_at);
                        dateStr = months[d.getMonth()] + ' ' + d.getDate();
                    } catch(ex) { dateStr = 'Just now'; }

                    var dotHtml = '';
                    for (var ci = 0; ci < colors.length; ci++) {
                        var c = colors[ci];
                        var act = (c.value === (note.color || '#fffacd')) ? ' active' : '';
                        dotHtml += '<div class="sn-dot' + act + '" style="background:' + c.value + '" data-id="' + note.id + '" data-color="' + c.value + '"></div>';
                    }

                    el.innerHTML =
                        '<div class="sn-strip"></div>' +
                        '<div class="sn-head">' +
                            '<span class="sn-date">' + dateStr + '</span>' +
                            '<div class="sn-btns">' +
                                '<button class="sn-del" data-id="' + note.id + '" title="Delete"><i class="bx bx-trash"></i></button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="sn-body">' +
                            '<textarea class="sn-text" data-id="' + note.id + '" placeholder="Write something...">' + (note.content || '') + '</textarea>' +
                        '</div>' +
                        '<div class="sn-palette">' + dotHtml + '</div>';

                    document.body.appendChild(el);

                    /* Dragging */
                    var head = el.querySelector('.sn-head');
                    head.addEventListener('mousedown', function(e) {
                        if (e.target.closest && e.target.closest('.sn-btns')) return;
                        isDragging = true;
                        draggedNote = el;
                        offsetX = e.clientX - parseInt(el.style.left || '0');
                        offsetY = e.clientY - parseInt(el.style.top  || '0');
                        el.classList.add('dragging');
                        document.body.classList.add('sn-dragging');
                        e.preventDefault();
                    });

                    /* Textarea */
                    var ta = el.querySelector('.sn-text');
                    ta.style.height = 'auto';
                    ta.style.height = Math.max(70, ta.scrollHeight) + 'px';
                    ta.addEventListener('mousedown', function(e) { e.stopPropagation(); });
                    ta.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = this.scrollHeight + 'px';
                    });
                    ta.addEventListener('blur', function() {
                        var nid = parseInt(this.getAttribute('data-id'));
                        if (nid) updateNote(nid, { content: this.value });
                    });

                    /* Delete */
                    var delBtn = el.querySelector('.sn-del');
                    delBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var nid = parseInt(this.getAttribute('data-id'));
                        if (nid) deleteNote(nid);
                    });

                    /* Color dots */
                    var dots = el.querySelectorAll('.sn-dot');
                    for (var di = 0; di < dots.length; di++) {
                        dots[di].addEventListener('click', function(e) {
                            e.stopPropagation();
                            var nid = parseInt(this.getAttribute('data-id'));
                            var col = this.getAttribute('data-color');
                            if (!nid || !col) return;
                            updateNote(nid, { color: col });
                            el.setAttribute('data-color', col);
                            var siblings = this.parentElement.querySelectorAll('.sn-dot');
                            for (var si = 0; si < siblings.length; si++) siblings[si].classList.remove('active');
                            this.classList.add('active');
                        });
                    }
                })(notes[idx], idx);
            }
        }
    }

    /* Run when DOM is ready */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStickyNotes);
    } else {
        initStickyNotes();
    }
})();
</script>
