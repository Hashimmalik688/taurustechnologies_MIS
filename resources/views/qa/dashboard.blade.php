@extends('layouts.master')

@section('title', 'QA Scoring Dashboard')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   QA Scoring Dashboard — CRM Theme Integrated
   ═══════════════════════════════════════════════════ */

/* Glass-card base (same as executive dashboard) */
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
.kpi-card .k-icon { font-size: 1rem; margin-bottom: 0.2rem; display: block; opacity: .7; }
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
.sec-card { padding: 0; margin-bottom: 0.65rem; overflow: hidden; }
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

/* Badge mini */
.bd-mini {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    display: inline-block;
    min-width: 22px;
    text-align: center;
}
.bd-mini.bd-blue   { background: rgba(85,110,230,.12); color: #556ee6; }
.bd-mini.bd-green  { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-mini.bd-red    { background: rgba(244,106,106,.12); color: #c84646; }
.bd-mini.bd-warn   { background: rgba(241,180,76,.12); color: #b87a14; }
.bd-mini.bd-teal   { background: rgba(80,165,241,.12); color: #2b81c9; }
.bd-mini.bd-gold   { background: rgba(212,175,55,.12); color: #b89730; }
.bd-mini.bd-purple { background: rgba(124,105,239,.12); color: #5b49c7; }

/* ── Scrollable table wrapper ── */
.scroll-tbl { max-height: 280px; overflow-y: auto; }
.scroll-tbl::-webkit-scrollbar { width: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

/* ── Link button ── */
.link-btn {
    font-size: 0.62rem;
    padding: 0.18rem 0.45rem;
    border-radius: 0.3rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.link-btn:hover { border-color: var(--bs-gold); color: var(--bs-gold); }

/* ── QA-specific components ── */

/* Disposition badges */
.qa-disp {
    display: inline-block;
    padding: 0.12rem 0.4rem;
    border-radius: 1rem;
    font-size: 0.58rem;
    font-weight: 700;
    letter-spacing: .3px;
    text-transform: uppercase;
}
.qa-disp.d-excellent { background: rgba(52,195,143,.12); color: #1a8754; }
.qa-disp.d-good      { background: rgba(85,110,230,.12); color: #556ee6; }
.qa-disp.d-average   { background: rgba(241,180,76,.12); color: #b87a14; }
.qa-disp.d-poor      { background: rgba(244,106,106,.12); color: #c84646; }
.qa-disp.d-comp-fail { background: rgba(214,48,49,.12); color: #c84646; border: 1px solid rgba(214,48,49,.2); }
.qa-disp.d-void-risk { background: rgba(124,105,239,.12); color: #5b49c7; border: 1px solid rgba(124,105,239,.2); }

/* Score inline */
.qa-score { font-weight: 700; font-size: 0.8rem; }
.qa-score.s-excellent { color: #1a8754; }
.qa-score.s-good      { color: #556ee6; }
.qa-score.s-average   { color: #b87a14; }
.qa-score.s-poor      { color: #c84646; }

/* Compliance dot */
.comp-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
.comp-dot.cd-pass { background: #34c38f; }
.comp-dot.cd-fail { background: #f46a6a; }

/* Score bar mini */
.qa-bar {
    height: 4px;
    background: var(--bs-surface-200);
    border-radius: 2px;
    overflow: hidden;
    width: 50px;
    display: inline-block;
    vertical-align: middle;
    margin-left: 4px;
}
.qa-bar .fill { height: 100%; border-radius: 2px; transition: width .3s; }
.qa-bar .fill.f-green  { background: #34c38f; }
.qa-bar .fill.f-blue   { background: #556ee6; }
.qa-bar .fill.f-warn   { background: #f1b44c; }
.qa-bar .fill.f-red    { background: #f46a6a; }

/* Page header bar */
.qa-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.65rem;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.qa-header h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.qa-header h5 i { opacity: .6; }
.qa-controls {
    display: flex;
    gap: 0.4rem;
    align-items: center;
}
.qa-controls select {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.35rem;
    border: 1px solid var(--bs-surface-300);
    background: var(--bs-card-bg);
    color: inherit;
    cursor: pointer;
}
.qa-controls .qa-btn {
    font-size: 0.68rem;
    padding: 0.25rem 0.6rem;
    border-radius: 0.35rem;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all .15s;
}
.qa-controls .qa-btn-gold { background: var(--bs-gold, #d4af37); color: #fff; }
.qa-controls .qa-btn-gold:hover { opacity: .85; }

/* Nav tabs for views */
.qa-nav {
    display: flex;
    gap: 0.25rem;
    margin-bottom: 0.65rem;
    flex-wrap: wrap;
}
.qa-nav-btn {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.3rem 0.7rem;
    border-radius: 1rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.qa-nav-btn:hover:not(.active) { border-color: var(--bs-gold); color: var(--bs-gold); }
.qa-nav-btn.active {
    background: var(--bs-gold, #d4af37);
    border-color: var(--bs-gold);
    color: #fff;
}
.qa-nav-btn i { font-size: 0.8rem; }

/* Alert feed */
.qa-alert-feed { max-height: 220px; overflow-y: auto; }
.qa-alert-feed::-webkit-scrollbar { width: 3px; }
.qa-alert-feed::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }
.qa-alert-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.35rem 0;
    border-bottom: 1px solid rgba(0,0,0,.03);
    font-size: 0.72rem;
    gap: 0.3rem;
}
.qa-alert-item:last-child { border-bottom: none; }

/* Issue list */
.qa-issue-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.3rem 0;
    border-bottom: 1px solid rgba(0,0,0,.03);
    font-size: 0.72rem;
}
.qa-issue-item:last-child { border-bottom: none; }
.qa-issue-count {
    background: var(--bs-surface-100);
    padding: 0.1rem 0.4rem;
    border-radius: 0.8rem;
    font-size: 0.6rem;
    font-weight: 700;
    color: var(--bs-surface-500);
}

/* Category bar */
.qa-cat-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.45rem;
}
.qa-cat-label {
    width: 100px;
    font-size: 0.68rem;
    color: var(--bs-surface-500);
    text-align: right;
    flex-shrink: 0;
}
.qa-cat-bar {
    flex: 1;
    height: 18px;
    background: var(--bs-surface-100);
    border-radius: 0.25rem;
    overflow: hidden;
    position: relative;
}
.qa-cat-bar .fill {
    height: 100%;
    border-radius: 0.25rem;
    transition: width .4s;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 6px;
    font-size: 0.6rem;
    font-weight: 700;
    color: #fff;
    min-width: 24px;
}
.qa-cat-val {
    width: 40px;
    font-size: 0.72rem;
    font-weight: 600;
    text-align: right;
}

/* Agent header card */
.qa-agent-hdr {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    margin-bottom: 0.65rem;
    flex-wrap: wrap;
}
.qa-agent-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--bs-gold, #d4af37);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.qa-agent-info h6 { margin: 0; font-size: 0.85rem; font-weight: 700; }
.qa-agent-info p { margin: 0; font-size: 0.7rem; color: var(--bs-surface-500); }
.qa-agent-kpis {
    display: flex;
    gap: 1rem;
    margin-left: auto;
    flex-wrap: wrap;
}
.qa-agent-kpi { text-align: center; }
.qa-agent-kpi .val { font-size: 1.1rem; font-weight: 700; line-height: 1; }
.qa-agent-kpi .lbl {
    font-size: 0.55rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .3px;
    color: var(--bs-surface-500);
    margin-top: 0.1rem;
}

/* Compliance checklist */
.qa-checklist-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0;
    font-size: 0.7rem;
    border-bottom: 1px solid rgba(0,0,0,.02);
}
.qa-checklist-item:last-child { border-bottom: none; }
.qa-check-icon { width: 16px; text-align: center; font-size: 0.75rem; }
.qa-check-pass { color: #34c38f; }
.qa-check-fail { color: #f46a6a; }
.qa-check-na   { color: var(--bs-surface-400); }

/* Coaching box */
.qa-coaching {
    background: var(--bs-surface-100);
    border-radius: 0.4rem;
    padding: 0.5rem 0.65rem;
    font-size: 0.72rem;
    line-height: 1.5;
    margin-bottom: 0.5rem;
}
.qa-coaching h6 {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--bs-gold, #d4af37);
    margin-bottom: 0.3rem;
}
.qa-coaching ul { margin: 0; padding-left: 1rem; }
.qa-coaching li { margin-bottom: 0.15rem; }

/* Transcript */
.qa-transcript {
    background: var(--bs-surface-100);
    border-radius: 0.4rem;
    padding: 0.65rem;
    max-height: 350px;
    overflow-y: auto;
    font-size: 0.72rem;
    line-height: 1.6;
}
.qa-transcript::-webkit-scrollbar { width: 3px; }
.qa-transcript::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }
.qa-transcript .t-line { margin-bottom: 0.3rem; }
.qa-transcript .t-agent { color: #556ee6; font-weight: 600; }
.qa-transcript .t-customer { color: #1a8754; font-weight: 600; }
.qa-transcript .t-unknown { color: var(--bs-surface-400); font-weight: 600; }

/* Overlay (call detail modal) */
.qa-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.qa-overlay.show { display: flex; }
.qa-overlay-box {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    width: 100%;
    max-width: 1050px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 8px 40px rgba(0,0,0,.15);
}
.qa-overlay-close {
    position: absolute;
    top: 8px;
    right: 12px;
    background: none;
    border: none;
    font-size: 1.1rem;
    cursor: pointer;
    color: var(--bs-surface-500);
    z-index: 10;
}
.qa-overlay-close:hover { color: var(--bs-gold); }

/* Pagination */
.qa-pagination {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
    margin-top: 0.65rem;
    padding: 0 0.75rem 0.65rem;
}
.qa-pagination button {
    font-size: 0.62rem;
    padding: 0.2rem 0.45rem;
    border-radius: 0.25rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    transition: all .15s;
}
.qa-pagination button:hover { border-color: var(--bs-gold); color: var(--bs-gold); }
.qa-pagination button.active { background: var(--bs-gold, #d4af37); color: #fff; border-color: var(--bs-gold); }
.qa-pagination button:disabled { opacity: .4; cursor: not-allowed; }

/* Loading / Empty */
.qa-loading {
    text-align: center;
    padding: 2rem;
    color: var(--bs-surface-400);
    font-size: 0.78rem;
}
.qa-loading .spin {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid var(--bs-surface-300);
    border-top-color: var(--bs-gold, #d4af37);
    border-radius: 50%;
    animation: qaSpin .7s linear infinite;
    margin-right: 6px;
    vertical-align: middle;
}
@keyframes qaSpin { to { transform: rotate(360deg); } }
.qa-empty {
    text-align: center;
    padding: 1.5rem;
    color: var(--bs-surface-400);
    font-size: 0.72rem;
}

/* Chart containers */
.qa-chart-wrap { position: relative; height: 180px; }

/* Compliance breakdown bars */
.qa-comp-bar-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.3rem;
    font-size: 0.68rem;
}
.qa-comp-bar-label { width: 90px; text-align: right; color: var(--bs-surface-500); flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.qa-comp-bar-track { flex: 1; height: 12px; background: var(--bs-surface-100); border-radius: 0.2rem; overflow: hidden; }
.qa-comp-bar-fill { height: 100%; background: #f46a6a; border-radius: 0.2rem; transition: width .3s; }
.qa-comp-bar-val { width: 24px; text-align: right; font-weight: 700; color: #c84646; font-size: 0.65rem; }

/* Responsive */
@media (max-width: 768px) {
    .qa-agent-kpis { margin-left: 0; }
    .qa-cat-label { width: 70px; }
}

/* QA Status badges (sales) */
.qa-st { display: inline-block; padding: 0.12rem 0.45rem; border-radius: 1rem; font-size: 0.58rem; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; }
.qa-st.st-good    { background: rgba(52,195,143,.12); color: #1a8754; }
.qa-st.st-avg     { background: rgba(241,180,76,.12); color: #b87a14; }
.qa-st.st-bad     { background: rgba(244,106,106,.12); color: #c84646; }
.qa-st.st-pending { background: rgba(108,117,125,.08); color: #6c757d; }
.qa-st.st-review  { background: rgba(85,110,230,.12); color: #556ee6; }

/* Manager status badges */
.mgr-st { display: inline-block; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-size: 0.55rem; font-weight: 700; text-transform: uppercase; }
.mgr-st.ms-approved  { background: rgba(52,195,143,.12); color: #1a8754; }
.mgr-st.ms-declined  { background: rgba(244,106,106,.12); color: #c84646; }
.mgr-st.ms-pending   { background: rgba(108,117,125,.08); color: #6c757d; }
.mgr-st.ms-chargeback { background: rgba(124,105,239,.12); color: #5b49c7; }

/* Sales filter row */
.qa-filter-row { display: flex; gap: 0.3rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.qa-filter-btn {
    font-size: 0.62rem; font-weight: 600; padding: 0.2rem 0.55rem; border-radius: 1rem;
    border: 1px solid var(--bs-surface-300); background: transparent; color: var(--bs-surface-500);
    cursor: pointer; transition: all .15s;
}
.qa-filter-btn:hover { border-color: var(--bs-gold); color: var(--bs-gold); }
.qa-filter-btn.active { background: var(--bs-gold, #d4af37); border-color: var(--bs-gold); color: #fff; }

/* Call detail header badges */
.qa-detail-meta { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; font-size: 0.68rem; color: var(--bs-surface-400); }
.qa-detail-meta .meta-item { display: flex; align-items: center; gap: 0.2rem; }
.qa-detail-meta .meta-item i { font-size: 0.8rem; opacity: .6; }

/* Overlay improvements */
.qa-overlay-box { scrollbar-width: thin; scrollbar-color: var(--bs-surface-300) transparent; }
.qa-overlay-box::-webkit-scrollbar { width: 4px; }
.qa-overlay-box::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 4px; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="qa-header">
    <h5><i class="bx bx-shield-quarter"></i> QA Scoring Dashboard</h5>
    <div class="qa-controls">
        <select id="qa-range" onchange="QA.onRangeChange()">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
        </select>
        <button class="qa-btn qa-btn-gold" onclick="QA.refresh()"><i class="bx bx-refresh"></i> Refresh</button>
    </div>
</div>

{{-- View Navigation Tabs --}}
<div class="qa-nav" id="qa-nav">
    <button class="qa-nav-btn active" data-view="overview" onclick="QA.nav('overview')">
        <i class="bx bx-tachometer"></i> Overview
    </button>
    <button class="qa-nav-btn" data-view="agents" onclick="QA.nav('agents')">
        <i class="bx bx-group"></i> Ravens Closers
    </button>
    <button class="qa-nav-btn" data-view="salesqa" onclick="QA.nav('salesqa')">
        <i class="bx bx-dollar-circle"></i> Sales QA
    </button>
    <button class="qa-nav-btn" data-view="compliance" onclick="QA.nav('compliance')">
        <i class="bx bx-error-circle"></i> Compliance
    </button>
    <button class="qa-nav-btn" data-view="voidrisks" onclick="QA.nav('voidrisks')">
        <i class="bx bx-shield-x"></i> Void Risks
    </button>
    <button class="qa-nav-btn" data-view="allcalls" onclick="QA.nav('allcalls')">
        <i class="bx bx-phone-call"></i> All Calls
    </button>
</div>

{{-- Dynamic Content Area --}}
<div id="qa-content">
    <div class="qa-loading"><span class="spin"></span> Loading QA data...</div>
</div>

{{-- Call Detail Overlay --}}
<div class="qa-overlay" id="qa-overlay" onclick="if(event.target===this)QA.closeOverlay()">
    <div class="qa-overlay-box">
        <button class="qa-overlay-close" onclick="QA.closeOverlay()"><i class="bx bx-x"></i></button>
        <div id="qa-overlay-inner"></div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
/* ═══════════════════════════════════════════════════════════════════
   QA Scoring Dashboard — SPA Logic (CRM-integrated)
   ═══════════════════════════════════════════════════════════════════ */
const QA = (function() {
    'use strict';

    let currentView = 'overview';
    let currentRange = 'today';
    let currentAgentId = null;
    let refreshTimer = null;
    let salesFilter = 'all';

    const $ = s => document.querySelector(s);
    const $$ = s => document.querySelectorAll(s);
    const content = () => $('#qa-content');

    // Theme-aware chart colors via window.themeColors bridge
    function cc() {
        const tc = window.themeColors || {};
        return {
            gold: tc.gold || '#d4af37',
            success: tc.success || '#34c38f',
            danger: tc.danger || '#f46a6a',
            warning: tc.warning || '#f1b44c',
            info: tc.info || '#50a5f1',
            purple: tc.purple || '#7c69ef',
            surface200: tc.surface200 || '#e9ecef',
            surface400: tc.surface400 || '#adb5bd',
            surface500: tc.surface500 || '#6c757d',
            primary: tc.chartPrimary || '#556ee6',
        };
    }

    /* ═══════ API ═══════════════════════════════════════════════ */
    async function api(path, params = {}) {
        const url = new URL(window.location.origin + '/qa/api/' + path);
        Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        if (!res.ok) throw new Error('API ' + res.status);
        return res.json();
    }

    /* ═══════ NAV ═══════════════════════════════════════════════ */
    function nav(view, agentId) {
        currentView = view;
        if (agentId) currentAgentId = agentId;
        $$('#qa-nav .qa-nav-btn').forEach(b =>
            b.classList.toggle('active',
                b.dataset.view === view || (view === 'agent-detail' && b.dataset.view === 'agents')
            )
        );
        loadView();
    }

    function onRangeChange() {
        currentRange = $('#qa-range').value;
        loadView();
    }

    function refresh() { loadView(); }

    function startAutoRefresh() {
        clearInterval(refreshTimer);
        refreshTimer = setInterval(() => { if (currentView === 'overview') loadView(); }, 60000);
    }

    /* ═══════ ROUTER ══════════════════════════════════════════ */
    async function loadView() {
        content().innerHTML = '<div class="qa-loading"><span class="spin"></span> Loading...</div>';
        try {
            switch (currentView) {
                case 'overview': await loadOverview(); break;
                case 'agents': await loadAgents(); break;
                case 'salesqa': await loadSalesQA(); break;
                case 'compliance': await loadCompliance(); break;
                case 'voidrisks': await loadVoidRisks(); break;
                case 'allcalls': await loadAllCalls(); break;
                case 'agent-detail': await loadAgentDetail(currentAgentId); break;
            }
        } catch (e) {
            content().innerHTML = '<div class="qa-empty">Error: ' + esc(e.message) + '</div>';
            console.error(e);
        }
    }

    /* ═══════ OVERVIEW ════════════════════════════════════════ */
    async function loadOverview() {
        const d = await api('overview', { range: currentRange });
        const ts = d.team_stats;
        const ek = d.extended_kpis;

        let html = '';

        // KPI Row 1: Primary Metrics
        html += `<div class="kpi-row">
            <div class="kpi-card k-gold ex-card"><i class="bx bx-bar-chart-alt-2 k-icon"></i><div class="k-val">${ts.calls_scored}</div><div class="k-lbl">Calls Scored</div></div>
            <div class="kpi-card k-blue ex-card"><i class="bx bx-trending-up k-icon"></i><div class="k-val">${ts.avg_score}</div><div class="k-lbl">Team Avg Score</div></div>
            <div class="kpi-card k-green ex-card"><i class="bx bx-check-shield k-icon"></i><div class="k-val">${ts.compliance_rate}%</div><div class="k-lbl">Compliance Rate</div></div>
            <div class="kpi-card k-teal ex-card"><i class="bx bx-check-double k-icon"></i><div class="k-val">${ek.passing_rate}%</div><div class="k-lbl">Pass Rate (80+)</div></div>
            <div class="kpi-card k-red ex-card"><i class="bx bx-error k-icon"></i><div class="k-val">${ts.compliance_fails}</div><div class="k-lbl">Comp. Fails</div></div>
            <div class="kpi-card k-purple ex-card"><i class="bx bx-shield-x k-icon"></i><div class="k-val">${ts.void_risks}</div><div class="k-lbl">Void Risks</div></div>
        </div>`;

        // KPI Row 2: Industry Metrics
        html += `<div class="kpi-row">
            <div class="kpi-card k-warn ex-card"><i class="bx bx-time-five k-icon"></i><div class="k-val">${fmtDuration(ek.avg_handle_time)}</div><div class="k-lbl">Avg Handle Time</div></div>
            <div class="kpi-card k-teal ex-card"><i class="bx bx-user-check k-icon"></i><div class="k-val">${ek.agents_scored}</div><div class="k-lbl">Closers Scored</div></div>
            <div class="kpi-card k-green ex-card"><i class="bx bx-star k-icon"></i><div class="k-val">${ts.excellent_count}</div><div class="k-lbl">Excellent Calls</div></div>
            <div class="kpi-card k-red ex-card"><i class="bx bx-phone-off k-icon"></i><div class="k-val">${ek.dnc_violations}</div><div class="k-lbl">DNC Violations</div></div>
            <div class="kpi-card k-warn ex-card"><i class="bx bx-microphone-off k-icon"></i><div class="k-val">${ek.recording_disclosure_fails}</div><div class="k-lbl">Rec. Discl. Fails</div></div>
            <div class="kpi-card k-gray ex-card"><i class="bx bx-loader-alt k-icon"></i><div class="k-val">${d.processing_now}</div><div class="k-lbl">Processing</div></div>
        </div>`;

        // KPI Row 3: Disposition Breakdown
        html += `<div class="kpi-row">
            <div class="kpi-card k-green ex-card"><i class="bx bx-trophy k-icon"></i><div class="k-val">${ts.excellent_count + ts.good_count}</div><div class="k-lbl">Good+ Calls</div></div>
            <div class="kpi-card k-warn ex-card"><i class="bx bx-minus-circle k-icon"></i><div class="k-val">${ts.average_count}</div><div class="k-lbl">Average Calls</div></div>
            <div class="kpi-card k-red ex-card"><i class="bx bx-down-arrow-circle k-icon"></i><div class="k-val">${ts.poor_count}</div><div class="k-lbl">Poor Calls</div></div>
            <div class="kpi-card k-gray ex-card"><i class="bx bx-skip-next k-icon"></i><div class="k-val">${ek.short_calls_skipped}</div><div class="k-lbl">Short (&lt;3m) Skipped</div></div>
            <div class="kpi-card k-blue ex-card"><i class="bx bx-bot k-icon"></i><div class="k-val">${ek.scored_by_gemini}</div><div class="k-lbl">Gemini Scored</div></div>
            <div class="kpi-card k-purple ex-card"><i class="bx bx-bot k-icon"></i><div class="k-val">${ek.scored_by_claude}</div><div class="k-lbl">Claude Scored</div></div>
        </div>`;

        // Charts Row
        html += `<div class="row g-2">
            <div class="col-lg-4"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-line-chart"></i> Score Trend (7d)</h6></div><div class="sec-body"><div class="qa-chart-wrap"><canvas id="qaTrendChart"></canvas></div></div></div></div>
            <div class="col-lg-4"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-doughnut-chart"></i> Disposition Breakdown</h6></div><div class="sec-body"><div class="qa-chart-wrap"><canvas id="qaDispChart"></canvas></div></div></div></div>
            <div class="col-lg-4"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-bar-chart"></i> Score Distribution</h6></div><div class="sec-body"><div class="qa-chart-wrap"><canvas id="qaDistChart"></canvas></div></div></div></div>
        </div>`;

        // Category Averages + Compliance Breakdown
        html += `<div class="row g-2">
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-slider-alt"></i> Team Category Averages</h6></div><div class="sec-body">${renderCategoryBars(d.team_category_averages)}</div></div></div>
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-error-circle"></i> Compliance Failure Breakdown</h6></div><div class="sec-body">${renderComplianceBreakdown(d.compliance_breakdown)}</div></div></div>
        </div>`;

        // Alerts + Issues
        html += `<div class="row g-2">
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-bell"></i> Recent Compliance Alerts</h6></div><div class="sec-body"><div class="qa-alert-feed">${renderAlertFeed(d.compliance_alerts)}</div></div></div></div>
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-bug"></i> Top Issues (AI-Identified)</h6></div><div class="sec-body">${renderIssues(d.top_issues)}</div></div></div>
        </div>`;

        // Closer Leaderboard
        html += `<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-crown"></i> Ravens Closer Leaderboard</h6></div><div class="scroll-tbl">${renderLeaderboard(d.agent_leaderboard)}</div></div>`;

        // Recent Calls
        html += `<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-phone-call"></i> Recent Scored Calls</h6></div><div class="scroll-tbl">${renderCallsTable(d.recent_calls)}</div></div>`;

        content().innerHTML = html;

        // Render charts with theme colors
        renderTrendChart(d.score_trend, d.compliance_trend);
        renderDispChart(d.disposition_chart, '#qaDispChart');
        renderDistChart(d.score_distribution);
    }

    /* ═══════ ALL RAVENS CLOSERS ════════════════════════════ */
    async function loadAgents() {
        const d = await api('overview', { range: currentRange });
        const agents = d.agent_leaderboard;

        let html = '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-group"></i> All Ravens Closers — Performance</h6></div>';
        if (!agents.length) {
            html += '<div class="sec-body"><div class="qa-empty">No Ravens Closers scored in this period</div></div>';
        } else {
            html += '<div class="scroll-tbl" style="max-height:500px"><table class="ex-tbl"><thead><tr>';
            html += '<th>#</th><th>Ravens Closer</th><th class="text-center">Calls</th><th class="text-center">Avg Score</th>';
            html += '<th class="text-center">Compliance</th><th class="text-center">AHT</th>';
            html += '<th class="text-center">Fails</th><th class="text-center">Void</th>';
            html += '<th class="text-center">Excellent</th><th></th></tr></thead><tbody>';
            agents.forEach((a, i) => {
                html += `<tr>
                    <td>${i + 1}</td>
                    <td><strong>${esc(a.agent_name)}</strong><div style="font-size:.6rem;color:var(--bs-surface-400)">${esc(a.agent_email || '')}</div></td>
                    <td class="text-center"><span class="bd-mini bd-blue">${a.calls_scored}</span></td>
                    <td class="text-center"><span class="qa-score ${scoreClass(a.avg_score)}">${a.avg_score}</span></td>
                    <td class="text-center"><span class="bd-mini ${a.compliance_rate >= 90 ? 'bd-green' : a.compliance_rate >= 70 ? 'bd-warn' : 'bd-red'}">${a.compliance_rate}%</span></td>
                    <td class="text-center" style="font-size:.7rem">${fmtDuration(a.avg_handle_time)}</td>
                    <td class="text-center">${a.compliance_fails > 0 ? '<span class="bd-mini bd-red">' + a.compliance_fails + '</span>' : '<span class="bd-mini bd-green">0</span>'}</td>
                    <td class="text-center">${a.void_risks > 0 ? '<span class="bd-mini bd-purple">' + a.void_risks + '</span>' : '0'}</td>
                    <td class="text-center"><span class="bd-mini bd-gold">${a.excellent_count}</span></td>
                    <td><button class="link-btn" onclick="QA.nav('agent-detail',${a.agent_user_id})">View</button></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        html += '</div>';
        content().innerHTML = html;
    }

    /* ═══════ SALES QA ═════════════════════════════════════ */
    let salesPage = 1;
    async function loadSalesQA(page) {
        if (page) salesPage = page;
        const d = await api('sales', { range: currentRange, page: salesPage, qa_filter: salesFilter });
        const s = d.summary;

        let html = '';

        // Summary KPIs
        html += `<div class="kpi-row">
            <div class="kpi-card k-gold ex-card"><i class="bx bx-dollar-circle k-icon"></i><div class="k-val">${s.total_sales}</div><div class="k-lbl">Total Sales</div></div>
            <div class="kpi-card k-green ex-card"><i class="bx bx-check-circle k-icon"></i><div class="k-val">${s.good_sales}</div><div class="k-lbl">Good (QA)</div></div>
            <div class="kpi-card k-warn ex-card"><i class="bx bx-minus-circle k-icon"></i><div class="k-val">${s.avg_sales}</div><div class="k-lbl">Average (QA)</div></div>
            <div class="kpi-card k-red ex-card"><i class="bx bx-x-circle k-icon"></i><div class="k-val">${s.bad_sales}</div><div class="k-lbl">Bad (QA)</div></div>
            <div class="kpi-card k-gray ex-card"><i class="bx bx-time-five k-icon"></i><div class="k-val">${s.pending_sales}</div><div class="k-lbl">Pending QA</div></div>
            <div class="kpi-card k-blue ex-card"><i class="bx bx-search-alt k-icon"></i><div class="k-val">${s.review_sales}</div><div class="k-lbl">In Review</div></div>
        </div>`;
        html += `<div class="kpi-row">
            <div class="kpi-card k-green ex-card"><i class="bx bx-check-double k-icon"></i><div class="k-val">${s.mgr_approved}</div><div class="k-lbl">Mgr Approved</div></div>
            <div class="kpi-card k-red ex-card"><i class="bx bx-block k-icon"></i><div class="k-val">${s.mgr_declined}</div><div class="k-lbl">Mgr Declined</div></div>
            <div class="kpi-card k-purple ex-card"><i class="bx bx-revision k-icon"></i><div class="k-val">${s.chargebacks}</div><div class="k-lbl">Chargebacks</div></div>
            <div class="kpi-card k-teal ex-card"><i class="bx bx-shield k-icon"></i><div class="k-val">$${s.avg_coverage || 0}</div><div class="k-lbl">Avg Coverage</div></div>
            <div class="kpi-card k-blue ex-card"><i class="bx bx-credit-card k-icon"></i><div class="k-val">$${s.avg_premium || 0}</div><div class="k-lbl">Avg Premium</div></div>
            <div class="kpi-card k-gold ex-card"><i class="bx bx-pie-chart-alt-2 k-icon"></i><div class="k-val">${s.total_sales > 0 ? Math.round((s.good_sales / s.total_sales) * 100) : 0}%</div><div class="k-lbl">Good Sale Rate</div></div>
        </div>`;

        // Filter buttons
        html += `<div class="qa-filter-row">
            <button class="qa-filter-btn ${salesFilter === 'all' ? 'active' : ''}" onclick="QA.setSalesFilter('all')">All</button>
            <button class="qa-filter-btn ${salesFilter === 'good' ? 'active' : ''}" onclick="QA.setSalesFilter('good')">Good</button>
            <button class="qa-filter-btn ${salesFilter === 'avg' ? 'active' : ''}" onclick="QA.setSalesFilter('avg')">Average</button>
            <button class="qa-filter-btn ${salesFilter === 'bad' ? 'active' : ''}" onclick="QA.setSalesFilter('bad')">Bad</button>
            <button class="qa-filter-btn ${salesFilter === 'pending' ? 'active' : ''}" onclick="QA.setSalesFilter('pending')">Pending</button>
            <button class="qa-filter-btn ${salesFilter === 'review' ? 'active' : ''}" onclick="QA.setSalesFilter('review')">In Review</button>
        </div>`;

        // Per-closer breakdown
        if (d.closer_breakdown && d.closer_breakdown.length) {
            html += '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-group"></i> Sales by Ravens Closer</h6></div>';
            html += '<div class="scroll-tbl" style="max-height:300px"><table class="ex-tbl"><thead><tr>';
            html += '<th>Ravens Closer</th><th class="text-center">Sales</th><th class="text-center">Good</th><th class="text-center">Avg</th><th class="text-center">Bad</th><th class="text-center">Pending</th><th class="text-center">Chargebacks</th><th class="text-center">Good Rate</th></tr></thead><tbody>';
            d.closer_breakdown.forEach(cb => {
                html += `<tr>
                    <td><strong>${esc(cb.closer_name)}</strong></td>
                    <td class="text-center"><span class="bd-mini bd-gold">${cb.total_sales}</span></td>
                    <td class="text-center"><span class="bd-mini bd-green">${cb.good_sales}</span></td>
                    <td class="text-center"><span class="bd-mini bd-warn">${cb.avg_sales}</span></td>
                    <td class="text-center"><span class="bd-mini bd-red">${cb.bad_sales}</span></td>
                    <td class="text-center"><span class="bd-mini bd-blue">${cb.pending_sales}</span></td>
                    <td class="text-center">${cb.chargebacks > 0 ? '<span class="bd-mini bd-purple">' + cb.chargebacks + '</span>' : '<span class="bd-mini bd-green">0</span>'}</td>
                    <td class="text-center"><span class="bd-mini ${cb.good_rate >= 80 ? 'bd-green' : cb.good_rate >= 60 ? 'bd-warn' : 'bd-red'}">${cb.good_rate}%</span></td>
                </tr>`;
            });
            html += '</tbody></table></div></div>';
        }

        // Sales list table
        html += '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-list-ul"></i> Sales Records</h6></div>';
        if (!d.sales.length) {
            html += '<div class="sec-body"><div class="qa-empty">No sales in this period</div></div>';
        } else {
            html += '<div class="scroll-tbl" style="max-height:500px"><table class="ex-tbl"><thead><tr>';
            html += '<th>Customer</th><th>Phone</th><th>Ravens Closer</th><th>Sale Date</th><th class="text-center">QA Status</th><th class="text-center">Mgr Status</th><th>Carrier</th><th class="text-center">Coverage</th><th class="text-center">Premium</th><th>State</th></tr></thead><tbody>';
            d.sales.forEach(sl => {
                html += `<tr>
                    <td><strong>${esc(sl.cn_name || 'N/A')}</strong></td>
                    <td style="font-size:.68rem">${esc(fmtPhone(sl.phone_number))}</td>
                    <td>${esc(sl.closer_name)}</td>
                    <td style="font-size:.68rem">${fmtTime(sl.sale_date)}</td>
                    <td class="text-center"><span class="qa-st ${qaStatusClass(sl.qa_status)}">${esc(sl.qa_status)}</span></td>
                    <td class="text-center"><span class="mgr-st ${mgrStatusClass(sl.manager_status)}">${esc(sl.manager_status)}</span></td>
                    <td style="font-size:.68rem">${esc(sl.carrier_name || '-')}</td>
                    <td class="text-center">${sl.coverage_amount ? '$' + Number(sl.coverage_amount).toLocaleString() : '-'}</td>
                    <td class="text-center">${sl.monthly_premium ? '$' + Number(sl.monthly_premium).toFixed(2) : '-'}</td>
                    <td style="font-size:.68rem">${esc(sl.state || '-')}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        html += renderPagination(d.pagination, 'QA.loadSalesPage');
        html += '</div>';

        content().innerHTML = html;
    }

    /* ═══════ COMPLIANCE ════════════════════════════════════ */
    async function loadCompliance() {
        const d = await api('overview', { range: currentRange });
        const alerts = d.compliance_alerts;

        let html = `<div class="row g-2 mb-2">
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-error-circle"></i> Failures by Check Type</h6></div><div class="sec-body">${renderComplianceBreakdown(d.compliance_breakdown)}</div></div></div>
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-line-chart"></i> Compliance Rate Trend (7d)</h6></div><div class="sec-body"><div class="qa-chart-wrap"><canvas id="qaCompTrend"></canvas></div></div></div></div>
        </div>`;

        html += '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-list-check"></i> Compliance Violations</h6></div>';
        if (!alerts.length) {
            html += '<div class="sec-body"><div class="qa-empty">No compliance violations in this period</div></div>';
        } else {
            html += '<div class="scroll-tbl" style="max-height:400px"><table class="ex-tbl"><thead><tr><th>Ravens Closer</th><th>Violation</th><th>Call</th><th>Time</th><th></th></tr></thead><tbody>';
            alerts.forEach(a => {
                html += `<tr>
                    <td><strong>${esc(a.agent_name)}</strong></td>
                    <td><span class="qa-disp d-comp-fail">${esc(a.check_code)}: ${esc(a.check_label)}</span></td>
                    <td>#${a.qa_call_id}</td>
                    <td style="font-size:.68rem">${fmtTime(a.flagged_at)}</td>
                    <td><button class="link-btn" onclick="QA.openCallDetail(${a.qa_call_id})">Review</button></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        html += '</div>';
        content().innerHTML = html;

        if (d.compliance_trend && Object.keys(d.compliance_trend).length) {
            const c = cc();
            new Chart($('#qaCompTrend'), {
                type: 'line',
                data: { labels: Object.keys(d.compliance_trend).map(d => d.slice(5)), datasets: [{ label: 'Compliance %', data: Object.values(d.compliance_trend).map(Number), borderColor: c.success, backgroundColor: c.success + '18', fill: true, tension: .3, pointRadius: 3, pointBackgroundColor: c.success }] },
                options: chartOpts(0, 100)
            });
        }
    }

    /* ═══════ VOID RISKS ═══════════════════════════════════ */
    async function loadVoidRisks() {
        const d = await api('overview', { range: currentRange });
        const voids = d.void_risks;

        let html = '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-shield-x"></i> Void Risk Calls — Urgent Review</h6></div>';
        if (!voids.length) {
            html += '<div class="sec-body"><div class="qa-empty">No void risks in this period</div></div>';
        } else {
            html += '<div class="scroll-tbl" style="max-height:500px"><table class="ex-tbl"><thead><tr><th>Ravens Closer</th><th class="text-center">Score</th><th>Customer</th><th>Risk Reason</th><th>Time</th><th></th></tr></thead><tbody>';
            voids.forEach(v => {
                html += `<tr>
                    <td><strong>${esc(v.agent_name)}</strong></td>
                    <td class="text-center"><span class="qa-disp d-void-risk">${v.total_score}</span></td>
                    <td style="font-size:.68rem">${esc(fmtPhone(v.callee_number))}</td>
                    <td style="max-width:300px;font-size:.68rem;color:#c84646">${esc(v.void_risk_reason || 'No reason')}</td>
                    <td style="font-size:.68rem">${fmtTime(v.call_start_time)}</td>
                    <td><button class="link-btn" onclick="QA.openCallDetail(${v.id})">Review</button></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        html += '</div>';
        content().innerHTML = html;
    }

    /* ═══════ ALL CALLS ═════════════════════════════════════ */
    let allCallsPage = 1;
    async function loadAllCalls(page) {
        if (page) allCallsPage = page;
        const d = await api('calls', { range: currentRange, page: allCallsPage });
        let html = '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-phone-call"></i> All Scored Calls</h6></div>';
        html += '<div class="scroll-tbl" style="max-height:500px">' + renderCallsTable(d.calls) + '</div>';
        html += renderPagination(d.pagination, 'QA.loadAllCallsPage');
        html += '</div>';
        content().innerHTML = html;
    }

    /* ═══════ CLOSER DETAIL ════════════════════════════════ */
    async function loadAgentDetail(agentId) {
        const d = await api('agents/' + agentId, { range: currentRange });
        const a = d.agent, s = d.summary, cats = d.category_averages;

        let html = `<button class="qa-nav-btn" onclick="QA.nav('agents')" style="margin-bottom:.5rem"><i class="bx bx-arrow-back"></i> Back to All Ravens Closers</button>`;

        // Closer header
        html += `<div class="ex-card qa-agent-hdr">
            <div class="qa-agent-avatar">${esc(a.name.charAt(0))}</div>
            <div class="qa-agent-info"><h6>${esc(a.name)}</h6><p>${esc(a.email)}</p></div>
            <div class="qa-agent-kpis">
                <div class="qa-agent-kpi"><div class="val" style="color:#556ee6">${s.calls_scored}</div><div class="lbl">Calls</div></div>
                <div class="qa-agent-kpi"><div class="val qa-score ${scoreClass(s.avg_score)}">${s.avg_score}</div><div class="lbl">Avg Score</div></div>
                <div class="qa-agent-kpi"><div class="val" style="color:#1a8754">${s.compliance_rate}%</div><div class="lbl">Compliance</div></div>
                <div class="qa-agent-kpi"><div class="val" style="color:#c84646">${s.compliance_fails}</div><div class="lbl">Fails</div></div>
                <div class="qa-agent-kpi"><div class="val" style="color:#5b49c7">${s.void_risks}</div><div class="lbl">Void</div></div>
                <div class="qa-agent-kpi"><div class="val" style="color:#b87a14">${fmtDuration(s.avg_handle_time)}</div><div class="lbl">AHT</div></div>
            </div>
        </div>`;

        // Per-closer KPI cards
        html += `<div class="kpi-row">
            <div class="kpi-card k-green ex-card"><i class="bx bx-trophy k-icon"></i><div class="k-val">${s.excellent_count}</div><div class="k-lbl">Excellent</div></div>
            <div class="kpi-card k-blue ex-card"><i class="bx bx-like k-icon"></i><div class="k-val">${s.good_count}</div><div class="k-lbl">Good</div></div>
            <div class="kpi-card k-warn ex-card"><i class="bx bx-minus-circle k-icon"></i><div class="k-val">${s.average_count}</div><div class="k-lbl">Average</div></div>
            <div class="kpi-card k-red ex-card"><i class="bx bx-down-arrow-circle k-icon"></i><div class="k-val">${s.poor_count}</div><div class="k-lbl">Poor</div></div>
            <div class="kpi-card k-teal ex-card"><i class="bx bx-up-arrow-alt k-icon"></i><div class="k-val">${s.max_score}</div><div class="k-lbl">Best</div></div>
            <div class="kpi-card k-gray ex-card"><i class="bx bx-down-arrow-alt k-icon"></i><div class="k-val">${s.min_score}</div><div class="k-lbl">Worst</div></div>
        </div>`;

        // Charts + categories
        html += `<div class="row g-2">
            <div class="col-lg-4"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-line-chart"></i> Score Trend (14d)</h6></div><div class="sec-body"><div class="qa-chart-wrap"><canvas id="qaAgentTrend"></canvas></div></div></div></div>
            <div class="col-lg-4"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-slider-alt"></i> Category Scores</h6></div><div class="sec-body">${renderCategoryBars(cats)}</div></div></div>
            <div class="col-lg-4"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-doughnut-chart"></i> Dispositions</h6></div><div class="sec-body"><div class="qa-chart-wrap"><canvas id="qaAgentDisp"></canvas></div></div></div></div>
        </div>`;

        // Compliance + issues
        html += `<div class="row g-2">
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-error-circle"></i> Compliance Failures</h6></div><div class="sec-body">${renderAgentCompBreakdown(d.compliance_breakdown)}</div></div></div>
            <div class="col-lg-6"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-bug"></i> Recurring Issues</h6></div><div class="sec-body">${renderIssues(d.recurring_issues)}</div></div></div>
        </div>`;

        // History tables
        html += `<div class="row g-2">
            <div class="col-lg-5"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-list-check"></i> Compliance History</h6></div><div class="scroll-tbl" style="max-height:300px">${renderCompHistory(d.compliance_history)}</div></div></div>
            <div class="col-lg-7"><div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-phone-call"></i> Call History</h6></div><div class="scroll-tbl" style="max-height:300px">${renderCallsTable(d.calls)}</div>${renderPagination(d.calls_pagination, 'QA.loadAgentPage')}</div></div>
        </div>`;

        content().innerHTML = html;

        // Charts
        if (d.score_trend && d.score_trend.length) {
            const c = cc();
            new Chart($('#qaAgentTrend'), {
                type: 'line',
                data: { labels: d.score_trend.map(s => s.date.slice(5)), datasets: [{ label: 'Score', data: d.score_trend.map(s => s.avg_score), borderColor: c.primary, backgroundColor: c.primary + '18', fill: true, tension: .3, pointRadius: 3, pointBackgroundColor: c.primary }] },
                options: chartOpts(0, 100)
            });
        }
        if (d.disposition_chart && Object.keys(d.disposition_chart).length) {
            renderDispChart(d.disposition_chart, '#qaAgentDisp');
        }
    }

    /* ═══════ CALL DETAIL OVERLAY ════════════════════════ */
    async function openCallDetail(id) {
        $('#qa-overlay').classList.add('show');
        $('#qa-overlay-inner').innerHTML = '<div class="qa-loading"><span class="spin"></span> Loading call...</div>';
        try {
            const d = await api('calls/' + id);
            const c = d.call, r = d.qa_result, t = d.transcript;

            let html = '<div style="padding:.75rem">';
            // Header with closer name, call info, customer numbers
            html += `<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.75rem;flex-wrap:wrap;gap:.4rem">
                <div><h6 style="margin:0;font-size:.85rem;font-weight:700"><i class="bx bx-user" style="color:var(--bs-gold);opacity:.7"></i> ${esc(c.agent_name)} — Call #${c.id}</h6>
                <div class="qa-detail-meta" style="margin-top:.25rem">
                    <span class="meta-item"><i class="bx bx-time"></i> ${fmtTime(c.call_start_time)}</span>
                    <span class="meta-item"><i class="bx bx-stopwatch"></i> ${fmtDuration(c.duration_seconds)}</span>
                    <span class="meta-item"><i class="bx bx-bot"></i> ${esc(c.scored_by || 'AI')}</span>
                    ${c.caller_number ? '<span class="meta-item"><i class="bx bx-phone-outgoing"></i> From: ' + esc(fmtPhone(c.caller_number)) + '</span>' : ''}
                    ${c.callee_number ? '<span class="meta-item"><i class="bx bx-phone-incoming"></i> To: ' + esc(fmtPhone(c.callee_number)) + '</span>' : ''}
                </div></div>
                ${r ? `<div style="display:flex;align-items:center;gap:.5rem"><span class="qa-disp d-${dispClass(r.disposition)}">${dispLabel(r.disposition)}</span><span class="qa-score ${scoreClass(r.total_score)}" style="font-size:1.2rem">${r.total_score}</span></div>` : ''}
            </div>`;

            if (r) {
                html += '<div class="row g-2"><div class="col-lg-7">';
                html += '<div class="ex-card sec-card" style="margin-bottom:.5rem"><div class="sec-hdr"><h6><i class="bx bx-slider-alt"></i> Score Breakdown</h6></div><div class="sec-body">' + renderScoreBars(r.score_breakdown) + '</div></div>';
                if (r.coaching_notes) html += `<div class="qa-coaching"><h6>Coaching Notes</h6>${esc(r.coaching_notes)}</div>`;
                if (r.top_issue) html += `<div class="qa-coaching"><h6>Top Issue</h6>${esc(r.top_issue)}</div>`;
                if (r.strengths && r.strengths.length) html += `<div class="qa-coaching"><h6>Strengths</h6><ul>${r.strengths.map(s => '<li>' + esc(s) + '</li>').join('')}</ul></div>`;
                if (r.improvements && r.improvements.length) html += `<div class="qa-coaching"><h6>Areas for Improvement</h6><ul>${r.improvements.map(s => '<li>' + esc(s) + '</li>').join('')}</ul></div>`;
                if (r.void_risk_reason) html += `<div class="qa-coaching" style="border-left:3px solid #7c69ef"><h6 style="color:#5b49c7">Void Risk Reason</h6>${esc(r.void_risk_reason)}</div>`;
                html += '</div><div class="col-lg-5">';
                html += '<div class="ex-card sec-card"><div class="sec-hdr"><h6><i class="bx bx-list-check"></i> Compliance Checklist</h6></div><div class="sec-body">' + renderChecklist(r.compliance_checks) + '</div></div>';
                html += '</div></div>';
            } else {
                html += '<div class="qa-empty">No QA result available</div>';
            }

            html += '<div class="ex-card sec-card" style="margin-top:.5rem"><div class="sec-hdr"><h6><i class="bx bx-conversation"></i> Transcript</h6></div><div class="sec-body">';
            if (t && t.length) {
                html += '<div class="qa-transcript">';
                t.forEach(ln => { html += `<div class="t-line"><span class="t-${ln.speaker === 'AGENT' ? 'agent' : ln.speaker === 'CUSTOMER' ? 'customer' : 'unknown'}">${ln.speaker === 'AGENT' ? 'CLOSER' : ln.speaker}:</span> ${esc(ln.text)}</div>`; });
                html += '</div>';
            } else html += '<div class="qa-empty">No transcript available</div>';
            html += '</div></div></div>';

            $('#qa-overlay-inner').innerHTML = html;
        } catch (e) {
            $('#qa-overlay-inner').innerHTML = '<div style="padding:1rem"><div class="qa-empty">Error: ' + esc(e.message) + '</div></div>';
        }
    }

    function closeOverlay() { $('#qa-overlay').classList.remove('show'); }

    /* ═══════ RENDER HELPERS ════════════════════════════════ */
    function renderCallsTable(calls) {
        if (!calls || !calls.length) return '<div class="qa-empty">No calls</div>';
        let h = '<table class="ex-tbl"><thead><tr><th>Ravens Closer</th><th>Customer</th><th>Time</th><th class="text-center">Dur.</th><th class="text-center">Disp.</th><th class="text-center">Score</th><th class="text-center">Comp.</th><th></th></tr></thead><tbody>';
        calls.forEach(c => {
            h += `<tr>
                <td>${esc(c.agent_name || 'Unknown')}</td>
                <td style="font-size:.68rem">${esc(fmtPhone(c.callee_number))}</td>
                <td style="font-size:.68rem">${fmtTime(c.call_start_time)}</td>
                <td class="text-center">${fmtDuration(c.duration_seconds)}</td>
                <td class="text-center"><span class="qa-disp d-${dispClass(c.disposition)}">${dispLabel(c.disposition)}</span></td>
                <td class="text-center"><span class="qa-score ${scoreClass(c.total_score)}">${c.total_score || 0}</span><div class="qa-bar"><div class="fill ${barClass(c.total_score)}" style="width:${c.total_score || 0}%"></div></div></td>
                <td class="text-center"><span class="comp-dot ${c.compliance_pass ? 'cd-pass' : 'cd-fail'}"></span></td>
                <td><button class="link-btn" onclick="QA.openCallDetail(${c.id})">View</button></td>
            </tr>`;
        });
        return h + '</tbody></table>';
    }

    function renderLeaderboard(agents) {
        if (!agents || !agents.length) return '<div class="qa-empty">No data</div>';
        let h = '<table class="ex-tbl"><thead><tr><th>#</th><th>Ravens Closer</th><th class="text-center">Calls</th><th class="text-center">Avg</th><th class="text-center">Compliance</th><th class="text-center">AHT</th><th></th></tr></thead><tbody>';
        agents.forEach((a, i) => {
            h += `<tr><td>${i + 1}</td><td><i class="bx bx-user-circle me-1" style="color:var(--bs-gold);opacity:.7"></i>${esc(a.agent_name)}</td>
                <td class="text-center"><span class="bd-mini bd-blue">${a.calls_scored}</span></td>
                <td class="text-center"><span class="qa-score ${scoreClass(a.avg_score)}">${a.avg_score}</span></td>
                <td class="text-center"><span class="bd-mini ${a.compliance_rate >= 90 ? 'bd-green' : a.compliance_rate >= 70 ? 'bd-warn' : 'bd-red'}">${a.compliance_rate}%</span></td>
                <td class="text-center" style="font-size:.68rem">${fmtDuration(a.avg_handle_time)}</td>
                <td><button class="link-btn" onclick="QA.nav('agent-detail',${a.agent_user_id})">View</button></td></tr>`;
        });
        return h + '</tbody></table>';
    }

    function renderAlertFeed(alerts) {
        if (!alerts || !alerts.length) return '<div class="qa-empty">No alerts</div>';
        return alerts.map(a => `<div class="qa-alert-item"><div><strong>${esc(a.agent_name)}</strong> <span class="qa-disp d-comp-fail" style="margin-left:.2rem">${esc(a.check_code)}</span></div><div style="font-size:.62rem;color:var(--bs-surface-400)">${fmtTime(a.flagged_at)}</div></div>`).join('');
    }

    function renderIssues(issues) {
        if (!issues || !Object.keys(issues).length) return '<div class="qa-empty">No issues</div>';
        return Object.entries(issues).map(([issue, count]) => `<div class="qa-issue-item"><span>${esc(issue)}</span><span class="qa-issue-count">${count}</span></div>`).join('');
    }

    function renderCategoryBars(cats) {
        const labels = { opening: 'Opening', discovery: 'Discovery', presentation: 'Presentation', objection_handling: 'Objection Handling', closing: 'Closing', soft_skills: 'Soft Skills', call_control: 'Call Control' };
        let h = '';
        Object.entries(labels).forEach(([k, l]) => {
            const v = parseFloat(cats[k]) || 0, pct = v * 10;
            const col = v >= 8 ? '#34c38f' : v >= 6 ? '#556ee6' : v >= 4 ? '#f1b44c' : '#f46a6a';
            h += `<div class="qa-cat-row"><div class="qa-cat-label">${l}</div><div class="qa-cat-bar"><div class="fill" style="width:${pct}%;background:${col}">${v.toFixed(1)}</div></div><div class="qa-cat-val">${v.toFixed(1)}/10</div></div>`;
        });
        return h;
    }

    function renderComplianceBreakdown(bd) {
        if (!bd || !bd.length) return '<div class="qa-empty">No compliance failures</div>';
        const mx = Math.max(...bd.map(b => b.count));
        return bd.map(b => `<div class="qa-comp-bar-row"><div class="qa-comp-bar-label" title="${esc(b.check_label)}">${esc(b.check_code)}</div><div class="qa-comp-bar-track"><div class="qa-comp-bar-fill" style="width:${mx > 0 ? (b.count / mx) * 100 : 0}%"></div></div><div class="qa-comp-bar-val">${b.count}</div></div>`).join('');
    }

    function renderAgentCompBreakdown(bd) {
        if (!bd || !bd.length) return '<div class="qa-empty">No compliance issues</div>';
        return bd.map(b => `<div class="qa-issue-item"><span><span class="bd-mini bd-red">${esc(b.check_code)}</span> ${esc(b.check_label)}</span><span class="qa-issue-count">${b.count}</span></div>`).join('');
    }

    function renderScoreBars(scores) {
        if (!scores) return '';
        const labels = { opening: 'Opening', discovery: 'Discovery', presentation: 'Presentation', objection_handling: 'Objections', closing: 'Closing', soft_skills: 'Soft Skills', call_control: 'Call Control' };
        let h = '';
        Object.entries(labels).forEach(([k, l]) => {
            const v = parseInt(scores[k]) || 0, pct = v * 10;
            const col = v >= 8 ? '#34c38f' : v >= 6 ? '#556ee6' : v >= 4 ? '#f1b44c' : '#f46a6a';
            h += `<div class="qa-cat-row"><div class="qa-cat-label">${l}</div><div class="qa-cat-bar"><div class="fill" style="width:${pct}%;background:${col}">${v}</div></div><div class="qa-cat-val">${v}/10</div></div>`;
        });
        return h;
    }

    function renderChecklist(checks) {
        if (!checks) return '<div class="qa-empty">No data</div>';
        const labels = {
            C1_recording_disclosure: 'Recording Disclosure', C2_agent_identity: 'Agent Identity',
            C3_carrier_named: 'Carrier Named', C4_not_government_program: 'Not Government Program',
            C5_product_type_stated: 'Product Type Stated', C6_waiting_period: 'Waiting Period',
            C7_premium_amount: 'Premium Amount', C8_coverage_amount: 'Coverage Amount',
            C9_health_questions: 'Health Questions', C10_beneficiary_collected: 'Beneficiary Collected',
            C11_prospect_verbal_consent: 'Verbal Consent', C12_dnc_honored: 'DNC Honored'
        };
        let h = '';
        Object.entries(labels).forEach(([k, l]) => {
            const v = checks[k] || 'na';
            const icon = v === 'pass' ? '&#10003;' : v === 'fail' ? '&#10007;' : '&mdash;';
            const cls = v === 'pass' ? 'qa-check-pass' : v === 'fail' ? 'qa-check-fail' : 'qa-check-na';
            h += `<div class="qa-checklist-item"><span class="qa-check-icon ${cls}">${icon}</span><span>${l}</span></div>`;
        });
        return h;
    }

    function renderCompHistory(history) {
        if (!history || !history.length) return '<div class="qa-empty">No compliance issues</div>';
        let h = '<table class="ex-tbl"><thead><tr><th>Violation</th><th>Call</th><th>Time</th></tr></thead><tbody>';
        history.forEach(r => { h += `<tr><td><span class="qa-disp d-comp-fail">${esc(r.check_code)}: ${esc(r.check_label)}</span></td><td>#${r.qa_call_id}</td><td style="font-size:.65rem">${fmtTime(r.flagged_at)}</td></tr>`; });
        return h + '</tbody></table>';
    }

    function renderPagination(pg, fn) {
        if (!pg || pg.last_page <= 1) return '';
        let h = '<div class="qa-pagination">';
        h += `<button ${pg.current_page <= 1 ? 'disabled' : ''} onclick="${fn}(${pg.current_page - 1})">&laquo;</button>`;
        for (let i = 1; i <= pg.last_page; i++) {
            if (pg.last_page > 7 && i > 2 && i < pg.last_page - 1 && Math.abs(i - pg.current_page) > 1) { if (i === 3 || i === pg.last_page - 2) h += '<button disabled>...</button>'; continue; }
            h += `<button class="${i === pg.current_page ? 'active' : ''}" onclick="${fn}(${i})">${i}</button>`;
        }
        h += `<button ${pg.current_page >= pg.last_page ? 'disabled' : ''} onclick="${fn}(${pg.current_page + 1})">&raquo;</button></div>`;
        return h;
    }

    /* ═══════ CHARTS ═══════════════════════════════════════ */
    function chartOpts(min, max) {
        const c = cc();
        return { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { min, max, grid: { color: c.surface200 + '40' }, ticks: { color: c.surface500, font: { size: 10 } } }, x: { grid: { display: false }, ticks: { color: c.surface500, font: { size: 10 } } } } };
    }

    function renderTrendChart(trend, compTrend) {
        const el = $('#qaTrendChart');
        if (!el || !trend || !Object.keys(trend).length) return;
        const c = cc();
        const ds = [{ label: 'Avg Score', data: Object.values(trend).map(Number), borderColor: c.primary, backgroundColor: c.primary + '18', fill: true, tension: .3, pointRadius: 3, pointBackgroundColor: c.primary }];
        if (compTrend && Object.keys(compTrend).length) {
            ds.push({ label: 'Compliance %', data: Object.values(compTrend).map(Number), borderColor: c.success, backgroundColor: 'transparent', borderDash: [4, 2], tension: .3, pointRadius: 2, pointBackgroundColor: c.success });
        }
        new Chart(el, { type: 'line', data: { labels: Object.keys(trend).map(d => d.slice(5)), datasets: ds }, options: chartOpts(0, 100) });
    }

    function renderDispChart(disp, sel) {
        const el = $(sel);
        if (!el || !disp || !Object.keys(disp).length) return;
        const c = cc();
        const cm = { EXCELLENT: c.success, GOOD: c.info, AVERAGE: c.warning, POOR: c.danger, COMPLIANCE_FAIL: '#d63031', VOID_RISK: c.purple };
        new Chart(el, { type: 'doughnut', data: { labels: Object.keys(disp).map(dispLabel), datasets: [{ data: Object.values(disp), backgroundColor: Object.keys(disp).map(k => cm[k] || '#999'), borderWidth: 1, borderColor: 'rgba(255,255,255,.1)' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: c.surface500, font: { size: 10 }, padding: 6, boxWidth: 10 } } } } });
    }

    function renderDistChart(dist) {
        const el = $('#qaDistChart');
        if (!el || !dist) return;
        const c = cc();
        new Chart(el, { type: 'bar', data: { labels: Object.keys(dist), datasets: [{ label: 'Calls', data: Object.values(dist), backgroundColor: [c.danger, '#e17055', c.warning, c.info, c.success], borderRadius: 4, barThickness: 24 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: c.surface200 + '40' }, ticks: { color: c.surface500, font: { size: 10 } } }, x: { grid: { display: false }, ticks: { color: c.surface500, font: { size: 10 } } } } } });
    }

    /* ═══════ UTILITIES ═══════════════════════════════════ */
    function scoreClass(s) { s = parseFloat(s) || 0; return s >= 90 ? 's-excellent' : s >= 75 ? 's-good' : s >= 60 ? 's-average' : 's-poor'; }
    function barClass(s) { s = parseFloat(s) || 0; return s >= 90 ? 'f-green' : s >= 75 ? 'f-blue' : s >= 60 ? 'f-warn' : 'f-red'; }
    function dispClass(d) { return !d ? 'poor' : ({ EXCELLENT: 'excellent', GOOD: 'good', AVERAGE: 'average', POOR: 'poor', COMPLIANCE_FAIL: 'comp-fail', VOID_RISK: 'void-risk' })[d] || 'poor'; }
    function dispLabel(d) { return !d ? 'N/A' : ({ EXCELLENT: 'EXCELLENT', GOOD: 'GOOD', AVERAGE: 'AVERAGE', POOR: 'POOR', COMPLIANCE_FAIL: 'COMP FAIL', VOID_RISK: 'VOID RISK' })[d] || d; }
    function qaStatusClass(s) { return ({ Good: 'st-good', Avg: 'st-avg', Bad: 'st-bad', Pending: 'st-pending', 'In Review': 'st-review' })[s] || 'st-pending'; }
    function mgrStatusClass(s) { return ({ approved: 'ms-approved', declined: 'ms-declined', pending: 'ms-pending', chargeback: 'ms-chargeback', underwriting: 'ms-pending' })[s] || 'ms-pending'; }
    function fmtPhone(p) { if (!p) return '-'; p = String(p).replace(/\D/g, ''); if (p.length === 11 && p[0] === '1') p = p.slice(1); if (p.length === 10) return '(' + p.slice(0,3) + ') ' + p.slice(3,6) + '-' + p.slice(6); return p; }
    function fmtTime(iso) { if (!iso) return '-'; const d = new Date(iso); return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' ' + d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }); }
    function fmtDuration(sec) { if (!sec) return '-'; sec = Math.round(sec); return Math.floor(sec / 60) + ':' + String(sec % 60).padStart(2, '0'); }
    function esc(str) { if (!str) return ''; const d = document.createElement('div'); d.textContent = String(str); return d.innerHTML; }

    /* ═══════ INIT ═══════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => { loadView(); startAutoRefresh(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOverlay(); });

    return {
        nav, onRangeChange, refresh, openCallDetail, closeOverlay,
        loadAllCallsPage: function(p) { allCallsPage = p; loadAllCalls(p); },
        loadAgentPage: function(p) { loadAgentDetail(currentAgentId); },
        loadSalesPage: function(p) { salesPage = p; loadSalesQA(p); },
        setSalesFilter: function(f) { salesFilter = f; salesPage = 1; loadSalesQA(); }
    };
})();
</script>
@endsection
