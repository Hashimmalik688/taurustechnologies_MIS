<style>
/* ═══════════════════════════════════════════════════
   Pipeline Dashboard — Matching Executive Dashboard
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
.ex-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: box-shadow .2s;
}
.ex-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* ── KPI Stat Cards ── */
.kpi-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.kpi-card {
    flex: 1 1 80px;
    min-width: 75px;
    padding: 0.65rem 0.6rem;
    border-radius: 0.55rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.55rem 0.55rem 0 0;
}
.kpi-card .k-icon {
    font-size: 1rem;
    margin-bottom: 0.2rem;
    display: block;
    opacity: .7;
}
.kpi-card .k-val { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.kpi-card .k-lbl {
    font-size: 0.58rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 0.2rem;
}

/* KPI color variants */
.kpi-card.k-gold    { background: rgba(212,175,55,.06); }
.kpi-card.k-gold::before    { background: linear-gradient(90deg, #d4af37, #e8c84a); }
.kpi-card.k-gold .k-val, .kpi-card.k-gold .k-icon { color: #b89730; }

.kpi-card.k-blue    { background: rgba(85,110,230,.06); }
.kpi-card.k-blue::before    { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.kpi-card.k-blue .k-val, .kpi-card.k-blue .k-icon { color: #556ee6; }

.kpi-card.k-green   { background: rgba(52,195,143,.06); }
.kpi-card.k-green::before   { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.kpi-card.k-green .k-val, .kpi-card.k-green .k-icon { color: #1a8754; }

.kpi-card.k-teal    { background: rgba(80,165,241,.06); }
.kpi-card.k-teal::before    { background: linear-gradient(90deg, #50a5f1, #8cc5f7); }
.kpi-card.k-teal .k-val, .kpi-card.k-teal .k-icon { color: #2b81c9; }

.kpi-card.k-red     { background: rgba(244,106,106,.06); }
.kpi-card.k-red::before     { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.kpi-card.k-red .k-val, .kpi-card.k-red .k-icon { color: #c84646; }

.kpi-card.k-warn    { background: rgba(241,180,76,.06); }
.kpi-card.k-warn::before    { background: linear-gradient(90deg, #f1b44c, #f5cd7e); }
.kpi-card.k-warn .k-val, .kpi-card.k-warn .k-icon { color: #b87a14; }

.kpi-card.k-purple  { background: rgba(124,105,239,.06); }
.kpi-card.k-purple::before  { background: linear-gradient(90deg, #7c69ef, #a899f5); }
.kpi-card.k-purple .k-val, .kpi-card.k-purple .k-icon { color: #5b49c7; }

.kpi-card.k-gray    { background: rgba(108,117,125,.05); }
.kpi-card.k-gray::before    { background: linear-gradient(90deg, #6c757d, #95a0a8); }
.kpi-card.k-gray .k-val, .kpi-card.k-gray .k-icon { color: #6c757d; }

/* ── Section Cards ── */
.sec-card {
    padding: 0;
    margin-bottom: 0.65rem;
    overflow: hidden;
}
.sec-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    flex-wrap: wrap;
    gap: 0.4rem;
}
.sec-hdr h6 {
    margin: 0;
    font-size: 0.78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.sec-hdr h6 i { opacity: .6; font-size: 0.95rem; }
.sec-body { padding: 0.6rem 0.75rem; }

/* ── Compact Table ── */
.ex-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.75rem;
}
.ex-tbl thead th {
    text-transform: uppercase;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
    position: sticky;
    top: 0;
    z-index: 1;
}
.ex-tbl tbody td {
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    vertical-align: middle;
}
.ex-tbl tbody tr { transition: background .12s; }
.ex-tbl tbody tr:hover { background: rgba(212,175,55,.03); }
.ex-tbl tfoot td {
    padding: 0.4rem 0.5rem;
    border-top: 1px solid var(--bs-surface-200);
    font-weight: 600;
    background: var(--bs-surface-100);
}

/* ── Value Badge ── */
.v-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 0.3rem;
    display: inline-block;
    min-width: 24px;
    text-align: center;
}
.v-badge.v-blue   { background: rgba(85,110,230,.12); color: #556ee6; }
.v-badge.v-green  { background: rgba(52,195,143,.12); color: #1a8754; }
.v-badge.v-red    { background: rgba(244,106,106,.12); color: #c84646; }
.v-badge.v-warn   { background: rgba(241,180,76,.12); color: #b87a14; }
.v-badge.v-teal   { background: rgba(80,165,241,.12); color: #2b81c9; }
.v-badge.v-gray   { background: rgba(108,117,125,.1); color: #6c757d; }
.v-badge.v-purple { background: rgba(124,105,239,.12); color: #5b49c7; }
.v-badge.v-gold   { background: rgba(212,175,55,.12); color: #b89730; }

/* ── Scrollable table wrapper ── */
.scroll-tbl { max-height: 260px; overflow-y: auto; }
.scroll-tbl::-webkit-scrollbar { width: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

/* ── Bubble-Pill Filter Bar ── */
.pipe-filter-bar {
    display: flex; align-items: center; gap: .4rem;
    padding: .55rem .75rem;
    margin-bottom: 0.65rem;
    border-radius: 0.6rem;
    flex-wrap: wrap;
}
.pipe-pill {
    font-size: .72rem; font-weight: 600;
    padding: .32rem .7rem; border-radius: 22px;
    border: 1px solid rgba(0,0,0,.08);
    background: var(--bs-card-bg); color: var(--bs-surface-600);
    cursor: pointer; outline: none;
    transition: all .15s;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: .25rem;
}
.pipe-pill:hover { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.10); color: var(--bs-surface-700); }
.pipe-pill.active {
    background: rgba(212,175,55,.12); border-color: rgba(212,175,55,.35);
    color: #b89730; font-weight: 700;
}
.pipe-pill-date {
    font-size: .72rem; font-weight: 600;
    padding: .32rem .55rem; border-radius: 22px;
    border: 1px solid rgba(0,0,0,.08);
    background: var(--bs-card-bg); color: var(--bs-surface-600);
    cursor: pointer; outline: none; min-width: 120px;
    transition: border-color .15s;
}
.pipe-pill-date:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
.pipe-pill-lbl {
    font-size: .6rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--bs-surface-400); margin-right: -2px;
}
.pipe-pill-apply {
    font-size: .68rem; font-weight: 700; color: #fff;
    padding: .28rem .6rem; border-radius: 22px;
    border: none; background: linear-gradient(135deg, #d4af37, #e8c84a);
    cursor: pointer; transition: opacity .15s;
}
.pipe-pill-apply:hover { opacity: .85; }
.pipe-pill-clear {
    font-size: .68rem; font-weight: 600; color: #ef4444;
    text-decoration: none; padding: .25rem .5rem;
    border-radius: 22px; border: 1px solid rgba(239,68,68,.2);
    display: inline-flex; align-items: center; gap: 2px;
    transition: all .15s;
}
.pipe-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }
.pipe-filter-count {
    font-size: .68rem; font-weight: 600; color: var(--bs-surface-400);
    margin-left: auto;
}
.filter-bar {
    padding: 0.6rem 0.75rem;
    margin-bottom: 0.65rem;
}
.filter-bar .form-label { font-size: 0.7rem; font-weight: 600; margin-bottom: 0.25rem; }
.filter-bar .btn-group .btn { font-size: 0.72rem; padding: 0.3rem 0.65rem; }
.filter-bar .form-control { font-size: 0.75rem; padding: 0.3rem 0.5rem; }
.filter-bar .form-check-label { font-size: 0.72rem; }

/* ── Status Pill ── */
.s-pill {
    font-size: 0.62rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 1rem;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.s-pill.s-transferred { background: rgba(80,165,241,.12); color: #2b81c9; }
.s-pill.s-closed      { background: rgba(124,105,239,.12); color: #5b49c7; }
.s-pill.s-sale        { background: rgba(52,195,143,.12); color: #1a8754; }
.s-pill.s-declined    { background: rgba(244,106,106,.12); color: #c84646; }
.s-pill.s-pending     { background: rgba(241,180,76,.12); color: #b87a14; }
.s-pill.s-returned    { background: rgba(80,165,241,.12); color: #2b81c9; }
.s-pill.s-forwarded   { background: rgba(85,110,230,.12); color: #556ee6; }

/* ── Action Buttons ── */
.act-btn {
    font-size: 0.65rem;
    padding: 0.2rem 0.5rem;
    border-radius: 0.3rem;
    border: 1px solid;
    cursor: pointer;
    font-weight: 600;
    transition: all .15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
}
.act-btn.a-primary { border-color: rgba(85,110,230,.3); background: rgba(85,110,230,.06); color: #556ee6; }
.act-btn.a-primary:hover { background: rgba(85,110,230,.15); }
.act-btn.a-success { border-color: rgba(52,195,143,.3); background: rgba(52,195,143,.06); color: #1a8754; }
.act-btn.a-success:hover { background: rgba(52,195,143,.15); }
.act-btn.a-danger { border-color: rgba(244,106,106,.3); background: rgba(244,106,106,.06); color: #c84646; }
.act-btn.a-danger:hover { background: rgba(244,106,106,.15); }
.act-btn.a-warn { border-color: rgba(241,180,76,.3); background: rgba(241,180,76,.06); color: #b87a14; }
.act-btn.a-warn:hover { background: rgba(241,180,76,.15); }
.act-btn.a-info { border-color: rgba(80,165,241,.3); background: rgba(80,165,241,.06); color: #2b81c9; }
.act-btn.a-info:hover { background: rgba(80,165,241,.15); }

/* ── Section header stripe ── */
.pipe-hdr {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.75rem;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 1px solid rgba(0,0,0,.05);
}
.pipe-hdr i { font-size: 0.95rem; opacity: .6; }
.pipe-hdr .badge-count {
    margin-left: auto;
    font-size: 0.6rem;
    font-weight: 600;
    padding: 0.15rem 0.45rem;
    border-radius: 1rem;
    background: rgba(212,175,55,.12);
    color: #b89730;
}

/* Modal header override */
.modal-header-glass {
    background: linear-gradient(135deg, var(--bs-card-bg) 0%, rgba(212,175,55,.08) 100%);
    border-bottom: 1px solid rgba(212,175,55,.15);
}
.modal-header-glass .modal-title {
    font-size: 0.85rem;
    font-weight: 700;
}

/* ── Grid layout ── */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; }
@media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
</style>
<?php /**PATH /var/www/taurus-crm/resources/views/partials/pipeline-dashboard-styles.blade.php ENDPATH**/ ?>