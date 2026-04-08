@extends('layouts.master')

@section('title', 'QA Scoring Dashboard')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   QA Dashboard v3 — Enterprise Clean
   Design principles: hierarchy, whitespace, clarity
   ═══════════════════════════════════════════════════ */

/* ── Design Tokens ── */
:root {
  --qa-radius: 0.5rem;
  --qa-radius-lg: 0.75rem;
  --qa-border: 1px solid rgba(255,255,255,.07);
  --qa-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --qa-shadow-hover: 0 4px 16px rgba(0,0,0,.1);
  --qa-gold: #d4af37;
  --qa-gold-dim: rgba(212,175,55,.1);
  --qa-green: #34c38f; --qa-green-dim: rgba(52,195,143,.1);
  --qa-blue: #556ee6;  --qa-blue-dim: rgba(85,110,230,.1);
  --qa-red: #f46a6a;   --qa-red-dim: rgba(244,106,106,.1);
  --qa-warn: #f1b44c;  --qa-warn-dim: rgba(241,180,76,.1);
  --qa-purple: #7c69ef;--qa-purple-dim: rgba(124,105,239,.1);
  --qa-teal: #50a5f1;  --qa-teal-dim: rgba(80,165,241,.1);
  --qa-muted: var(--bs-surface-400, #8a94a6);
  --qa-surface: var(--bs-surface-100, rgba(255,255,255,.03));
  --qa-surface-border: var(--bs-surface-200, rgba(255,255,255,.06));
}

/* ── Base Card ── */
.qa-card {
  background: var(--bs-card-bg);
  border: var(--qa-border);
  border-radius: var(--qa-radius);
  box-shadow: var(--qa-shadow);
}
.qa-card-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: .6rem .9rem; border-bottom: 1px solid var(--qa-surface-border);
}
.qa-card-header h6 {
  margin: 0; font-size: .72rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .5px; color: var(--qa-muted);
  display: flex; align-items: center; gap: .35rem;
}
.qa-card-header h6 i { font-size: .85rem; }
.qa-card-body { padding: .75rem .9rem; }
.qa-card-body.p0 { padding: 0; }

/* ── Page Header ── */
.qa-page-header {
  display: flex; align-items: center; justify-content: space-between;
  gap: .75rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.qa-page-title {
  display: flex; align-items: center; gap: .5rem;
  font-size: 1rem; font-weight: 700; margin: 0;
  white-space: nowrap;
}
.qa-page-title i { color: var(--qa-gold); font-size: 1.1rem; }
.qa-page-title .qa-info-btn { margin-left: .1rem; }

.qa-toolbar {
  display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
}
.qa-range-group {
  display: flex; align-items: center; gap: .3rem;
  background: var(--qa-surface); border: var(--qa-border);
  border-radius: var(--qa-radius); padding: .2rem .4rem;
}
.qa-range-group select,
.qa-range-group input[type=date] {
  background: transparent; border: none; outline: none;
  font-size: .7rem; color: inherit; padding: .15rem .2rem;
  cursor: pointer;
}
.qa-range-group .qa-sep { color: var(--qa-muted); font-size: .7rem; padding: 0 .1rem; }
.qa-range-group .qa-range-divider { width: 1px; height: 14px; background: var(--qa-surface-border); margin: 0 .2rem; }

.qa-action-btn {
  display: inline-flex; align-items: center; gap: .3rem;
  font-size: .7rem; font-weight: 600; padding: .32rem .65rem;
  border-radius: var(--qa-radius); border: 1px solid transparent;
  cursor: pointer; transition: opacity .15s, box-shadow .15s; white-space: nowrap;
  text-decoration: none;
}
.qa-action-btn:hover { opacity: .85; box-shadow: 0 2px 8px rgba(0,0,0,.15); text-decoration: none; }
.qa-action-btn i { font-size: .8rem; }
.qa-btn-primary   { background: var(--qa-gold); color: #fff; border-color: transparent; }
.qa-btn-danger    { background: var(--qa-red-dim); color: #c84646; border-color: rgba(244,106,106,.25); }
.qa-btn-success   { background: var(--qa-green-dim); color: #1a8754; border-color: rgba(52,195,143,.25); }
.qa-btn-secondary { background: var(--qa-blue-dim); color: var(--qa-blue); border-color: rgba(85,110,230,.25); }
.qa-btn-ghost     { background: transparent; color: var(--qa-muted); border-color: var(--qa-surface-border); }
.qa-btn-ghost:hover { color: var(--qa-gold); border-color: rgba(212,175,55,.4); }

/* ── KPI Strip ── */
.qa-kpi-strip {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: .6rem; margin-bottom: 1rem;
}
.qa-kpi {
  background: var(--bs-card-bg); border: var(--qa-border);
  border-radius: var(--qa-radius); padding: .9rem .75rem;
  position: relative; overflow: hidden;
  transition: transform .15s, box-shadow .15s;
}
.qa-kpi:hover { transform: translateY(-2px); box-shadow: var(--qa-shadow-hover); }
.qa-kpi::after {
  content: ''; position: absolute; bottom: 0; left: 0; right: 0;
  height: 2px; border-radius: 0 0 var(--qa-radius) var(--qa-radius);
}
.qa-kpi-icon { font-size: 1.1rem; margin-bottom: .35rem; display: block; }
.qa-kpi-value { font-size: 1.6rem; font-weight: 700; line-height: 1; letter-spacing: -.02em; }
.qa-kpi-label { font-size: .6rem; text-transform: uppercase; font-weight: 600; letter-spacing: .5px; color: var(--qa-muted); margin-top: .3rem; }
.qa-kpi-delta { font-size: .62rem; font-weight: 600; margin-top: .2rem; }

.qa-kpi.kpi-score  .qa-kpi-icon, .qa-kpi.kpi-score  .qa-kpi-value { color: var(--qa-gold); }
.qa-kpi.kpi-score::after  { background: var(--qa-gold); }
.qa-kpi.kpi-comply .qa-kpi-icon, .qa-kpi.kpi-comply .qa-kpi-value { color: var(--qa-green); }
.qa-kpi.kpi-comply::after { background: var(--qa-green); }
.qa-kpi.kpi-calls  .qa-kpi-icon, .qa-kpi.kpi-calls  .qa-kpi-value { color: var(--qa-blue); }
.qa-kpi.kpi-calls::after  { background: var(--qa-blue); }
.qa-kpi.kpi-sales  .qa-kpi-icon, .qa-kpi.kpi-sales  .qa-kpi-value { color: var(--qa-teal); }
.qa-kpi.kpi-sales::after  { background: var(--qa-teal); }
.qa-kpi.kpi-alert  .qa-kpi-icon, .qa-kpi.kpi-alert  .qa-kpi-value { color: var(--qa-red); }
.qa-kpi.kpi-alert::after  { background: var(--qa-red); }

/* Secondary stat strip */
.qa-stat-strip {
  display: flex; gap: 0; margin-bottom: 1rem;
  background: var(--bs-card-bg); border: var(--qa-border); border-radius: var(--qa-radius);
  overflow: hidden;
}
.qa-stat {
  flex: 1; padding: .55rem .75rem; text-align: center;
  border-right: 1px solid var(--qa-surface-border);
  position: relative;
}
.qa-stat:last-child { border-right: none; }
.qa-stat-val { font-size: .95rem; font-weight: 700; line-height: 1; }
.qa-stat-lbl { font-size: .58rem; text-transform: uppercase; font-weight: 600; letter-spacing: .4px; color: var(--qa-muted); margin-top: .2rem; }
.qa-stat.st-excellent   .qa-stat-val { color: #1a8754; }
.qa-stat.st-exceptional .qa-stat-val { color: #7c3aed; }
.qa-stat.st-good        .qa-stat-val { color: var(--qa-blue); }
.qa-stat.st-average     .qa-stat-val { color: #b87a14; }
.qa-stat.st-poor        .qa-stat-val { color: #c84646; }
.qa-stat.st-void      .qa-stat-val { color: var(--qa-purple); }
.qa-stat.st-aht       .qa-stat-val { color: var(--qa-muted); }

/* ── Disposition Badges ── */
.qa-disp { display:inline-block; padding:.1rem .38rem; border-radius:1rem; font-size:.58rem; font-weight:700; letter-spacing:.3px; text-transform:uppercase; line-height:1.4; }
.qa-disp.d-exceptional { background:rgba(124,58,237,.12); color:#7c3aed; border:1px solid rgba(124,58,237,.2); }
.qa-disp.d-excellent { background:rgba(52,195,143,.12); color:#1a8754; }
.qa-disp.d-good      { background:rgba(85,110,230,.12); color:#556ee6; }
.qa-disp.d-average   { background:rgba(241,180,76,.12); color:#b87a14; }
.qa-disp.d-poor      { background:rgba(244,106,106,.12); color:#c84646; }
.qa-disp.d-comp-fail { background:rgba(214,48,49,.1); color:#c84646; border:1px solid rgba(214,48,49,.2); }
.qa-cf-badge { font-size:.52rem !important; vertical-align:middle; margin-left:.2rem; opacity:.9; }
.qa-disp.d-void-risk { background:rgba(124,105,239,.12); color:#5b49c7; border:1px solid rgba(124,105,239,.2); }

/* Score value */
.qa-score { font-weight:700; font-size:.8rem; letter-spacing:-.01em; }
.qa-score.s-exceptional { color:#7c3aed; }
.qa-score.s-excellent { color:#1a8754; }
.qa-score.s-good      { color:var(--qa-blue); }
.qa-score.s-average   { color:#b87a14; }
.qa-score.s-poor      { color:#c84646; }

/* ── Tables ── */
.qa-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:.74rem; }
.qa-tbl thead th {
  text-transform:uppercase; font-size:.58rem; font-weight:700; letter-spacing:.5px;
  color: var(--qa-muted); padding:.45rem .7rem;
  border-bottom: 1px solid var(--qa-surface-border);
  background: var(--qa-surface); position:sticky; top:0; z-index:1; white-space:nowrap;
}
.qa-tbl tbody td { padding:.42rem .7rem; border-bottom: 1px solid var(--qa-surface-border); vertical-align:middle; }
.qa-tbl tbody tr:last-child td { border-bottom:none; }
.qa-tbl tbody tr { cursor:pointer; transition:background .1s; }
.qa-tbl tbody tr:hover { background: rgba(212,175,55,.03); }

.qa-scroll { overflow-y:auto; }
.qa-scroll::-webkit-scrollbar { width:3px; }
.qa-scroll::-webkit-scrollbar-thumb { background:var(--qa-surface-border); border-radius:3px; }

/* Mini badges */
.qa-badge { font-size:.6rem; font-weight:700; padding:.12rem .38rem; border-radius:.25rem; display:inline-block; min-width:20px; text-align:center; line-height:1.4; }
.qa-badge.b-green  { background:var(--qa-green-dim); color:#1a8754; }
.qa-badge.b-red    { background:var(--qa-red-dim); color:#c84646; }
.qa-badge.b-gray   { background:rgba(108,117,125,.08); color:#6c757d; }
.qa-badge.b-blue   { background:var(--qa-blue-dim); color:var(--qa-blue); }
.qa-badge.b-gold   { background:var(--qa-gold-dim); color:#b89730; }

/* ── Category Bars ── */
.qa-cat-row { display:flex; align-items:center; gap:.6rem; margin-bottom:.42rem; }
.qa-cat-row:last-child { margin-bottom:0; }
.qa-cat-lbl { width:108px; font-size:.66rem; color:var(--qa-muted); text-align:right; flex-shrink:0; }
.qa-cat-track { flex:1; height:8px; background:var(--qa-surface); border-radius:4px; overflow:hidden; }
.qa-cat-fill { height:100%; border-radius:4px; transition:width .45s ease; }
.qa-cat-fill.f-green  { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.qa-cat-fill.f-blue   { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.qa-cat-fill.f-warn   { background: linear-gradient(90deg, #f1b44c, #f7d38a); }
.qa-cat-fill.f-red    { background: linear-gradient(90deg, #f46a6a, #f99898); }
/* Per-category dedicated colors */
.qa-cat-fill.c-opening     { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.qa-cat-fill.c-discovery   { background: linear-gradient(90deg, #20c4dd, #5fd9ea); }
.qa-cat-fill.c-presentation{ background: linear-gradient(90deg, #d4af37, #f0d060); }
.qa-cat-fill.c-objections  { background: linear-gradient(90deg, #f46a6a, #f99898); }
.qa-cat-fill.c-closing     { background: linear-gradient(90deg, #7c3aed, #a87ff5); }
.qa-cat-fill.c-soft        { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.qa-cat-fill.c-control     { background: linear-gradient(90deg, #f1b44c, #f7d38a); }
.qa-cat-score { width:28px; font-size:.7rem; font-weight:600; text-align:right; flex-shrink:0; }

/* ── Compliance bars ── */
.qa-comp-row { display:flex; align-items:center; gap:.5rem; margin-bottom:.32rem; font-size:.68rem; }
.qa-comp-row:last-child { margin-bottom:0; }
.qa-comp-lbl { width:40px; font-weight:700; color:var(--qa-muted); font-size:.65rem; flex-shrink:0; }
.qa-comp-name { flex:0 0 130px; font-size:.65rem; color:var(--qa-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.qa-comp-track { flex:1; height:6px; background:var(--qa-surface); border-radius:3px; overflow:hidden; }
.qa-comp-fill { height:100%; background:linear-gradient(90deg,#ef4444,#f97a7a); border-radius:3px; transition:width .3s; }
.qa-comp-count { width:24px; text-align:right; font-weight:700; color:#c84646; font-size:.65rem; }

/* ── Closer Table row ── */
.qa-closer-rank { width:20px; font-size:.65rem; font-weight:700; color:var(--qa-muted); text-align:center; }

/* ── Chart containers ── */
.qa-chart-wrap { position:relative; }
.qa-chart-wrap.h160 { height:160px; }
.qa-chart-wrap.h200 { height:200px; }

/* ── Filter Tabs ── */
.qa-filter-bar {
  display:flex; gap:.3rem; flex-wrap:wrap; padding:.6rem .9rem;
  border-bottom: 1px solid var(--qa-surface-border);
}
.qa-filter-tab {
  font-size:.62rem; font-weight:600; padding:.22rem .55rem; border-radius:1rem;
  border:1px solid transparent; background:transparent; color:var(--qa-muted);
  cursor:pointer; transition:all .15s;
}
.qa-filter-tab:hover { color: var(--qa-gold); border-color:rgba(212,175,55,.3); }
.qa-filter-tab.active { background:var(--qa-gold-dim); border-color:rgba(212,175,55,.4); color:#b89730; font-weight:700; }

/* ── Pagination ── */
.qa-pagination { display:flex; justify-content:center; gap:.25rem; padding:.65rem; }
.qa-pagination button {
  font-size:.62rem; padding:.22rem .5rem; border-radius:.3rem;
  border:1px solid var(--qa-surface-border); background:transparent;
  color:var(--qa-muted); cursor:pointer; transition:all .15s; min-width:28px;
}
.qa-pagination button:hover { border-color:var(--qa-gold); color:var(--qa-gold); }
.qa-pagination button.active { background:var(--qa-gold); color:#fff; border-color:transparent; }
.qa-pagination button:disabled { opacity:.35; cursor:not-allowed; }

/* ── Agent header ── */
.qa-agent-banner {
  display:flex; align-items:center; gap:.85rem;
  padding: .85rem .9rem; flex-wrap:wrap;
}
.qa-agent-avatar {
  width:42px; height:42px; border-radius:50%; flex-shrink:0;
  background:var(--qa-gold); display:flex; align-items:center; justify-content:center;
  font-size:1.05rem; font-weight:700; color:#fff;
}
.qa-agent-meta h6 { margin:0; font-size:.88rem; font-weight:700; }
.qa-agent-meta p  { margin:0; font-size:.68rem; color:var(--qa-muted); }
.qa-agent-stats { display:flex; gap:1.5rem; margin-left:auto; flex-wrap:wrap; }
.qa-agent-stat .val { font-size:1.15rem; font-weight:700; line-height:1; }
.qa-agent-stat .lbl { font-size:.55rem; text-transform:uppercase; letter-spacing:.3px; color:var(--qa-muted); margin-top:.15rem; }

/* ── Call Detail Overlay ── */
.qa-overlay { position:fixed; inset:0; background:rgba(4,8,20,.72); z-index:10000; display:none; align-items:center; justify-content:center; padding:1rem; backdrop-filter:blur(8px) saturate(140%); }
.qa-overlay.show { display:flex; }
.qa-overlay-box {
  background:var(--bs-card-bg, #fff);
  border:1px solid rgba(0,0,0,.1);
  border-radius:1rem;
  width:100%; max-width:1060px; max-height:92vh; overflow-y:auto;
  position:relative;
  box-shadow:0 24px 64px rgba(0,0,0,.18);
  color:var(--bs-body-color); scrollbar-width:thin; scrollbar-color:rgba(0,0,0,.15) transparent;
}
.qa-overlay-box::-webkit-scrollbar { width:4px; }
.qa-overlay-box::-webkit-scrollbar-thumb { background:rgba(0,0,0,.2); border-radius:4px; }

.qa-overlay-head {
  display:flex; justify-content:space-between; align-items:flex-start;
  padding:.9rem 1rem .7rem; border-bottom:1px solid rgba(0,0,0,.07);
  background:rgba(0,0,0,.015);
}
.qa-overlay-close {
  background:none; border:none; font-size:1.3rem; cursor:pointer;
  color:var(--qa-muted); line-height:1; padding:.2rem; flex-shrink:0; margin-left:.5rem;
}
.qa-overlay-close:hover { color:var(--bs-body-color); }
.qa-overlay-body { padding:.9rem 1rem; }

/* ── Glass inner cards for the overlay ── */
.qu-card { background:var(--bs-light, #f8f9fa); border:1px solid rgba(0,0,0,.08); border-radius:.6rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:0; }
.qu-card-hdr { display:flex; justify-content:space-between; align-items:center; padding:.6rem .9rem; border-bottom:1px solid rgba(0,0,0,.07); background:rgba(0,0,0,.02); border-radius:.6rem .6rem 0 0; }
.qu-card-hdr h6 { margin:0; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--qa-muted); display:flex; align-items:center; gap:.35rem; }
.qu-card-body { padding:.85rem .9rem; }
.qu-score-hero { text-align:center; padding:.75rem 0; }
.qu-score-num { font-size:3rem; font-weight:800; line-height:1; filter:drop-shadow(0 0 8px currentColor); }
.qu-score-num.excellent { color:var(--qa-green); }
.qu-score-num.good      { color:#5bc8a8; }
.qu-score-num.average   { color:var(--qa-warn); }
.qu-score-num.poor      { color:var(--qa-red); }
.qu-score-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--qa-muted); margin-top:.2rem; }
.qu-breakdown { display:grid; grid-template-columns:1fr 1fr; gap:.4rem .75rem; }
@media(max-width:600px){ .qu-breakdown { grid-template-columns:1fr; } }
.qu-bar-row { display:flex; align-items:center; gap:.5rem; }
.qu-bar-label { width:110px; font-size:.65rem; color:var(--qa-muted); white-space:nowrap; flex-shrink:0; }
.qu-bar-outer { flex:1; height:5px; background:rgba(0,0,0,.08); border-radius:3px; overflow:hidden; }
.qu-bar-inner { height:100%; background:linear-gradient(90deg,var(--qa-blue),#7a9ef7); border-radius:3px; transition:width .5s; box-shadow:0 0 6px rgba(85,110,230,.5); }
.qu-bar-val   { font-size:.65rem; font-weight:700; width:26px; text-align:right; }
.qu-checks { display:grid; grid-template-columns:1fr 1fr; gap:.35rem .75rem; }
@media(max-width:600px){ .qu-checks { grid-template-columns:1fr; } }
.qu-check-item { display:flex; align-items:flex-start; gap:.45rem; font-size:.7rem; }
.qu-check-dot  { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.dot-pass { background:var(--qa-green); }
.dot-fail { background:var(--qa-red); }
.dot-na   { background:rgba(255,255,255,.2); }
.qu-coaching { background:rgba(212,175,55,.06); border:1px solid rgba(212,175,55,.18); border-radius:.4rem; padding:.7rem .85rem; font-size:.73rem; line-height:1.65; white-space:pre-wrap; box-shadow:inset 0 1px 0 rgba(212,175,55,.12); }
.qu-transcript .t-line { padding:.35rem 0; border-bottom:1px solid rgba(0,0,0,.07); line-height:1.7; }
.qu-transcript .t-speaker { font-weight:700; margin-right:.4rem; font-size:.7rem; letter-spacing:.03em; text-transform:uppercase; }
.qu-transcript .t-agent    { color:#3b5bdb; }
.qu-transcript .t-customer { color:#c47a00; }
.qu-transcript .t-text { color:#1e293b; font-size:.82rem; }
.qu-toggle-icon.open { transform:rotate(0deg); }
.qu-toggle-icon:not(.open) { transform:rotate(-90deg); }
.comp-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.68rem; font-weight:700; padding:.18rem .55rem; border-radius:.8rem; }
.comp-pass { background:var(--qa-green-dim); color:var(--qa-green); border:1px solid rgba(52,195,143,.3); }
.comp-fail { background:var(--qa-red-dim);   color:var(--qa-red);   border:1px solid rgba(244,106,106,.3); }
.collapsed { display:none; }

/* Score ring — big centered score */
.qa-score-ring {
  width:90px; height:90px; border-radius:50%;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  border:3px solid; margin:0 auto .5rem;
}
.qa-score-ring .ring-val { font-size:1.6rem; font-weight:800; line-height:1; letter-spacing:-.03em; }
.qa-score-ring .ring-max { font-size:.58rem; color:#94a3b8; margin-top:.1rem; }
.qa-score-ring.sr-exceptional { border-color:#7c3aed; background:rgba(124,58,237,.06); }
.qa-score-ring.sr-exceptional .ring-val { color:#7c3aed; }
.qa-score-ring.sr-excellent { border-color:#34c38f; background:rgba(52,195,143,.06); }
.qa-score-ring.sr-excellent .ring-val { color:#1a8754; }
.qa-score-ring.sr-good { border-color:#556ee6; background:rgba(85,110,230,.06); }
.qa-score-ring.sr-good .ring-val { color:#556ee6; }
.qa-score-ring.sr-average { border-color:#f1b44c; background:rgba(241,180,76,.06); }
.qa-score-ring.sr-average .ring-val { color:#b87a14; }
.qa-score-ring.sr-poor { border-color:#f46a6a; background:rgba(244,106,106,.06); }
.qa-score-ring.sr-poor .ring-val { color:#c84646; }

/* Checklist */
.qa-check-item { display:flex; align-items:center; gap:.45rem; padding:.28rem 0; border-bottom:1px solid rgba(0,0,0,.025); font-size:.7rem; }
.qa-check-item:last-child { border-bottom:none; }
.qa-check-icon { width:16px; text-align:center; font-size:.8rem; flex-shrink:0; }
.qa-check-pass { color:#34c38f; } .qa-check-fail { color:#f46a6a; } .qa-check-na { color:#cbd5e1; }

/* Coaching block */
.qa-coaching {
  background:#fffbf0; border-left:3px solid #d4af37;
  border-radius:0 .35rem .35rem 0; padding:.55rem .75rem;
  font-size:.72rem; line-height:1.55; color:#3d2e00;
}
.qa-coaching ul { margin:0; padding-left:1rem; }
.qa-coaching li { margin-bottom:.18rem; }

/* Transcript */
.qa-transcript { background:#f4f6f9; border-radius:.35rem; padding:.65rem .75rem; max-height:320px; overflow-y:auto; font-size:.71rem; line-height:1.65; color:#374151; }
.qa-transcript::-webkit-scrollbar { width:3px; }
.qa-transcript::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:3px; }
.qa-transcript .t-closer   { color:#4f6ef7; font-weight:600; }
.qa-transcript .t-customer { color:#059669; font-weight:600; }
.qa-transcript .t-unknown  { color:#9ca3af; font-weight:600; }
.qa-transcript .t-line { margin-bottom:.32rem; }

/* Call meta bar */
.qa-call-meta { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; font-size:.68rem; color:#64748b; }
.qa-call-meta-item { display:flex; align-items:center; gap:.22rem; }
.qa-call-meta-item i { font-size:.78rem; opacity:.65; }

/* ── Loading / Empty states ── */
.qa-loading { text-align:center; padding:3rem 1rem; color:var(--qa-muted); font-size:.78rem; }
.qa-spin {
  display:inline-block; width:20px; height:20px; border-radius:50%;
  border:2px solid var(--qa-surface-border); border-top-color:var(--qa-gold);
  animation:qaSpin .75s linear infinite; vertical-align:middle; margin-right:.5rem;
}
@keyframes qaSpin { to { transform:rotate(360deg); } }
.qa-empty { text-align:center; padding:2rem 1rem; color:var(--qa-muted); font-size:.72rem; }

/* Chart empty state */
.qa-chart-wrap { position:relative; }
.qa-chart-empty { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; color:var(--qa-muted); font-size:.72rem; gap:.3rem; pointer-events:none; }
.qa-chart-empty i { font-size:1.5rem; opacity:.4; }

/* Calls table — review-optimised */
.qa-calls-tbl tbody tr.qa-call-row { cursor:pointer; transition:background .12s; }
.qa-calls-tbl tbody tr.qa-call-row:hover { background:rgba(212,175,55,.05); }
.qa-call-customer { font-size:.77rem; font-weight:600; color:var(--qa-text); line-height:1.2; }
.qa-call-phone { font-size:.61rem; color:var(--qa-muted); margin-top:.06rem; }
.qa-score-cell { display:inline-flex; align-items:center; justify-content:center; width:36px; height:28px; border-radius:.3rem; font-size:.8rem; font-weight:700; }
.qa-score-cell.s-exceptional { background:rgba(124,58,237,.12); color:#7c3aed; }
.qa-score-cell.s-excellent { background:rgba(52,195,143,.12); color:#1a8754; }
.qa-score-cell.s-good      { background:rgba(85,110,230,.12); color:#556ee6; }
.qa-score-cell.s-average   { background:rgba(241,180,76,.12); color:#b87a14; }
.qa-score-cell.s-poor      { background:rgba(244,106,106,.12); color:#c84646; }
.qa-sale-yes  { font-size:.66rem; font-weight:600; color:#34c38f; white-space:nowrap; }
.qa-sale-no   { color:var(--qa-muted); font-size:.68rem; }
.qa-row-arrow { color:var(--qa-muted); font-size:.9rem; opacity:.5; transition:opacity .1s; }
.qa-call-row:hover .qa-row-arrow { opacity:1; }

/* Action bar inside overlay */
.qa-review-bar {
  display:flex; gap:.6rem; align-items:center; flex-wrap:wrap;
  padding:.7rem 1rem; background:rgba(0,0,0,.015); border-top:1px solid rgba(0,0,0,.07);
  border-bottom:1px solid rgba(0,0,0,.07); justify-content:flex-end;
}
.qa-review-btn {
  display:inline-flex; align-items:center; gap:.3rem; padding:.38rem .75rem;
  border-radius:.4rem; font-size:.72rem; font-weight:600; cursor:pointer;
  border:2px solid transparent; transition:all .15s; letter-spacing:.1px;
}
.qa-review-btn.rv-delete   { border-color:rgba(244,106,106,.3); color:#c84646; background:transparent; }
.qa-review-btn.rv-delete:hover { background:#c84646; color:#fff; border-color:#c84646; }

/* Sale info block */
.qa-sale-block {
  display:flex; gap:1.2rem; flex-wrap:wrap; padding:.7rem .9rem;
  background:rgba(52,195,143,.06); border:1px solid rgba(52,195,143,.25);
  border-radius:.45rem; margin-bottom:.75rem; align-items:center;
}
.qa-sale-block .sb-icon { font-size:1.4rem; color:#34c38f; flex-shrink:0; }
.qa-sale-block .sb-item { display:flex; flex-direction:column; }
.qa-sale-block .sb-val  { font-size:.88rem; font-weight:700; color:#1a8754; line-height:1.15; }
.qa-sale-block .sb-lbl  { font-size:.58rem; text-transform:uppercase; letter-spacing:.3px; color:#94a3b8; }

/* ── Back link ── */
.qa-back-link {
  display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem;
  color:var(--qa-muted); cursor:pointer; margin-bottom:.75rem;
  background:none; border:none; padding:.2rem 0; transition:color .15s;
}
.qa-back-link:hover { color:var(--qa-gold); }

/* Collapsible section toggle icon */
.qa-toggle-icon { font-size:.9rem; color:var(--qa-muted); flex-shrink:0; transition:transform .15s; }

/* ── Info modal ── */
.qa-info-btn {
  background:none; border:1px solid rgba(212,175,55,.3); color:var(--qa-gold);
  border-radius:50%; width:24px; height:24px; display:inline-flex;
  align-items:center; justify-content:center; font-size:.85rem;
  cursor:pointer; vertical-align:middle; transition:all .2s;
}
.qa-info-btn:hover { background:var(--qa-gold-dim); border-color:var(--qa-gold); }
.qa-info-modal { display:none; position:fixed; inset:0; z-index:10001; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1rem; }
.qa-info-modal.show { display:flex; }
.qa-info-box { background:#fff; border-radius:var(--qa-radius-lg); box-shadow:0 12px 40px rgba(0,0,0,.25); color:#1e293b; width:100%; max-width:680px; max-height:88vh; overflow-y:auto; padding:1.5rem; position:relative; scrollbar-width:thin; }
.qa-info-close { position:absolute; top:.85rem; right:.85rem; background:none; border:none; font-size:1.3rem; cursor:pointer; color:#9ca3af; line-height:1; }
.qa-info-close:hover { color:#374151; }
.qa-info-section { margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid #f1f5f9; }
.qa-info-section:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
.qa-info-section h6 { font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.5px; color:#b89730; margin:0 0 .45rem; display:flex; align-items:center; gap:.3rem; }
.qa-info-section p { font-size:.77rem; color:#64748b; margin:0 0 .4rem; line-height:1.55; }
.qa-info-section ol, .qa-info-section ul { font-size:.77rem; color:#64748b; margin:0 0 .4rem; padding-left:1.2rem; line-height:1.65; }
.qa-info-tbl { width:100%; font-size:.74rem; border-collapse:collapse; }
.qa-info-tbl td { padding:.3rem .5rem; border-bottom:1px solid #f1f5f9; color:#475569; vertical-align:top; }
.qa-info-tbl td:first-child { white-space:nowrap; width:130px; }
.qa-info-tbl tr:last-child td { border-bottom:none; }

/* ── Processing badge ── */
.qa-proc-badge { display:flex; gap:.4rem; justify-content:flex-end; padding:.35rem 0; font-size:.62rem; margin-top:.35rem; }

/* ── Calls Section Date Filter ── */
.qa-calls-filter-row {
  display: flex; align-items: center; justify-content: space-between;
  gap: .5rem; flex-wrap: wrap;
  padding: .45rem .75rem; border-bottom: 1px solid var(--qa-surface-border);
  background: var(--qa-surface);
}
.qa-filter-tabs-inline { display:flex; gap:.25rem; flex-wrap:wrap; align-items:center; flex:1; min-width:0; }
.qa-filter-tab-sm {
  font-size:.6rem; font-weight:600; padding:.18rem .48rem; border-radius:1rem;
  border:1px solid transparent; background:transparent; color:var(--qa-muted);
  cursor:pointer; transition:all .15s; white-space:nowrap; line-height:1.5;
}
.qa-filter-tab-sm:hover { color:var(--qa-gold); border-color:rgba(212,175,55,.3); }
.qa-filter-tab-sm.active { background:var(--qa-gold-dim); border-color:rgba(212,175,55,.45); color:#b89730; font-weight:700; }

/* Date pill picker */
.qa-date-pill {
  display: inline-flex; align-items: center; gap: 0;
  border: 1px solid var(--qa-surface-border);
  border-radius: 2rem; overflow: hidden;
  background: var(--bs-card-bg);
  transition: border-color .18s, box-shadow .18s;
  flex-shrink: 0;
}
.qa-date-pill:focus-within {
  border-color: rgba(212,175,55,.55);
  box-shadow: 0 0 0 3px rgba(212,175,55,.1);
}
.qa-date-pill .dp-icon {
  display:flex; align-items:center; justify-content:center;
  padding: .22rem .55rem .22rem .65rem;
  color: var(--qa-gold); font-size:.82rem; pointer-events:none;
  flex-shrink:0;
}
.qa-date-pill input[type="date"] {
  -webkit-appearance:none; appearance:none;
  border:none; outline:none; background:transparent;
  font-size:.68rem; font-weight:600; color: var(--bs-body-color);
  padding: .22rem .5rem .22rem 0;
  cursor:pointer; min-width:0; width:90px;
  font-family: inherit; line-height:1;
}
.qa-date-pill input[type="date"]::-webkit-calendar-picker-indicator {
  opacity:0; cursor:pointer; position:absolute; right:0; width:100%;
}
.qa-date-pill input[type="date"][value=""],
.qa-date-pill input[type="date"]:not([value]) { color: var(--qa-muted); }
.qa-date-pill .dp-placeholder {
  font-size:.68rem; color:var(--qa-muted); padding:.22rem .5rem .22rem 0;
  pointer-events:none; white-space:nowrap; user-select:none;
}
.qa-date-pill .dp-clear {
  display:none; align-items:center; justify-content:center;
  padding:.18rem .55rem .18rem .2rem;
  background:none; border:none; cursor:pointer; color:var(--qa-muted);
  font-size:.75rem; line-height:1; flex-shrink:0;
  transition: color .15s;
}
.qa-date-pill.has-date .dp-clear { display:flex; }
.qa-date-pill.has-date .dp-placeholder { display:none; }
.qa-date-pill .dp-clear:hover { color: var(--qa-red); }
.qa-date-pill.has-date { border-color: rgba(212,175,55,.4); background: var(--qa-gold-dim); }
.qa-date-pill.has-date .dp-icon { color:var(--qa-gold); }
.qa-date-pill.has-date input[type="date"] { color:var(--qa-gold); font-weight:700; }

@media (max-width:900px) {
  .qa-kpi-strip { grid-template-columns:repeat(3,1fr); }
  .qa-stat-strip .qa-stat:nth-child(n+4) { display:none; }
}
@media (max-width:600px) {
  .qa-kpi-strip { grid-template-columns:repeat(2,1fr); }
  .qa-toolbar { width:100%; }
  .qa-page-header { flex-direction:column; align-items:flex-start; }
}
</style>
@endsection

@php
    $myMode   = $myMode   ?? false;
    $myUserId = $myUserId ?? null;
    $myBackUrl = $myBackUrl ?? null;
@endphp

@section('content')

<!-- ═══ Page Header ═══ -->
<div class="qa-page-header">
    <h5 class="qa-page-title">
        @if($myMode)
            @if($myBackUrl)
                <a href="{{ $myBackUrl }}" class="qa-action-btn qa-btn-ghost" style="margin-right:.25rem;" title="Back to Dashboard"><i class="ri-arrow-left-s-line"></i></a>
            @endif
            <i class="ri-shield-star-line"></i> My QA Report
        @else
            <i class="ri-shield-star-line"></i> QA Scoring
            <button class="qa-info-btn" onclick="document.getElementById('qaInfoModal').classList.add('show')" title="How QA Scoring Works">
                <i class="ri-question-line"></i>
            </button>
        @endif
    </h5>
    <div class="qa-toolbar">
        <div class="qa-range-group">
            <select id="qaRangePreset" onchange="QA.presetChanged()">
                <option value="">Custom</option>
                <option value="today">Today</option>
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
                <option value="all">All Time</option>
            </select>
            <div class="qa-range-divider"></div>
            <input type="date" id="qaStartDate" onchange="QA.rangeChanged()">
            <span class="qa-sep">→</span>
            <input type="date" id="qaEndDate" onchange="QA.rangeChanged()">
        </div>
        <button class="qa-action-btn qa-btn-primary" onclick="QA.refresh()"><i class="ri-refresh-line"></i> Refresh</button>
        @if(!$myMode)
        <button class="qa-action-btn qa-btn-danger" id="rerunTodayBtn" onclick="QA.rerunToday()" title="Re-score today's calls"><i class="ri-restart-line"></i> Rerun</button>
        <button class="qa-action-btn qa-btn-success" id="qaToggleBtn" onclick="QA.toggleQa()" title="Pause/resume QA scoring"><i class="ri-pause-circle-line" id="qaToggleIcon"></i> <span id="qaToggleLabel">Active</span></button>
        <a href="/qa/script" class="qa-action-btn qa-btn-ghost" title="Edit AI scoring prompt"><i class="ri-code-s-slash-line"></i> Script</a>
        <a href="/qa/manual" class="qa-action-btn qa-btn-secondary" title="Manually paste &amp; score a Zoom transcript"><i class="ri-upload-cloud-line"></i> Manual</a>
        <a href="/qa/upload" class="qa-action-btn qa-btn-secondary" title="Upload audio recording — transcribe via AssemblyAI &amp; score with Claude"><i class="ri-mic-line"></i> Upload Recording</a>
        @endif
    </div>
</div>

<!-- ═══ Main Content ═══ -->
<div id="qa-content">
    <div class="qa-loading"><span class="qa-spin"></span> Loading dashboard…</div>
</div>

<!-- ═══ Call Detail Overlay ═══ -->
<div class="qa-overlay" id="qaOverlay" onclick="if(event.target===this)QA.closeDetail()">
    <div class="qa-overlay-box" id="qaOverlayBox">
        <div id="qaOverlayContent"></div>
    </div>
</div>

<!-- ═══ Info Modal ═══ -->
<div class="qa-info-modal" id="qaInfoModal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="qa-info-box">
        <button class="qa-info-close" onclick="document.getElementById('qaInfoModal').classList.remove('show')">&times;</button>

        <div class="qa-info-section">
            <h6><i class="ri-robot-2-line"></i> AI-Powered Quality Assurance</h6>
            <p>Every recorded sales call is automatically transcribed via Zoom's built-in transcription service and scored by AI against <strong>11 compliance codes</strong> and <strong>7 quality categories</strong>. The numeric score (0–100) reflects sales performance. Compliance is a hard gate — any single failure results in a <strong>COMPLIANCE FAIL</strong> disposition, overriding the score entirely.</p>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-timer-flash-line"></i> Call Eligibility</h6>
            <p><strong>Minimum duration: 7 minutes</strong> — Calls shorter than 7 minutes are automatically skipped as they do not represent meaningful sales conversations. Only calls with Zoom transcripts are scored.</p>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-shield-check-line"></i> 11 Compliance Codes</h6>
            <table class="qa-info-tbl">
                <tr><td colspan="2"><strong style="color:var(--bs-primary)">Call Handling</strong></td></tr>
                <tr><td><strong>C1</strong> Closer Introduction</td><td>Closer states their name and company name at any point in the call</td></tr>
                <tr><td><strong>C2</strong> Carrier Named</td><td>Actual insurance carrier name stated (e.g. AIG / Corebridge, Mutual of Omaha)</td></tr>
                <tr><td><strong>C3</strong> Product Type Stated</td><td>Closer clearly identifies product as life insurance (not just a "benefit")</td></tr>
                <tr><td><strong>C4</strong> Health Questions</td><td>Medications and health conditions both asked (any form qualifies)</td></tr>
                <tr><td><strong>C5</strong> Quote &amp; Coverage</td><td>Monthly premium and death benefit stated — exact amount or a range</td></tr>
                <tr><td><strong>C6</strong> Draft Date Confirmed</td><td>Payment draft date confirmed with customer</td></tr>
                <tr><td><strong>C7</strong> Recorded Consent</td><td>Confirms name, DOB, SSN &amp; draft consent — at ANY point during the call (Format A: script read-back, or Format B: item-by-item)</td></tr>
                <tr><td colspan="2"><strong style="color:var(--bs-primary)">Application Requirements</strong></td></tr>
                <tr><td><strong>C8</strong> Application Info</td><td>Collects name, DOB, address, bank info, SSN, beneficiary (no email/IVR required)</td></tr>
                <tr><td colspan="2"><strong style="color:var(--bs-primary)">Behavioral Compliance</strong></td></tr>
                <tr><td><strong>C9</strong> DNC Honored</td><td>Do-Not-Call requests honored immediately; n/a if none made</td></tr>
                <tr><td><strong>C10</strong> Agent Handles Objections</td><td>Fails only if closer ignores a firm repeated refusal or uses deceptive pressure</td></tr>
                <tr><td><strong>C11</strong> Appropriate Language</td><td>No inappropriate or unprofessional language used by the closer</td></tr>
            </table>
            <p style="font-size:.75rem;color:#94a3b8;margin-top:.4rem;"><em>Note: Waiting period disclosure is tracked informally and does not affect compliance.</em></p>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-bar-chart-grouped-line"></i> 7 Sales Quality Categories (0–100 scale)</h6>
            <table class="qa-info-tbl">
                <tr><td><strong>Opening</strong></td><td>Professional greeting, tone, rapport building (1-10)</td></tr>
                <tr><td><strong>Discovery</strong></td><td>Needs assessment, health/family questions, listening (1-10)</td></tr>
                <tr><td><strong>Presentation</strong></td><td>Product explanation, personalization, proper quote (1-10)</td></tr>
                <tr><td><strong>Objection Handling</strong></td><td>Rebuttal quality — rebuttals are expected on cold calls (1-10)</td></tr>
                <tr><td><strong>Closing</strong></td><td>Asking for sale, consent, application completion (1-10)</td></tr>
                <tr><td><strong>Soft Skills</strong></td><td>Empathy, patience, respect, sensitivity with seniors (1-10)</td></tr>
                <tr><td><strong>Call Control</strong></td><td>Conversation flow, redirecting tangents, pacing (1-10)</td></tr>
            </table>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-award-line"></i> Scoring &amp; Disposition</h6>
            <p style="font-size:.78rem;margin-bottom:.5rem;">Score is based on <strong>sales quality (0–100)</strong>. Compliance issues are shown as a badge alongside the score-based disposition — e.g. <span class="qa-disp d-good" style="font-size:.7rem;">GOOD</span> <span class="qa-disp d-comp-fail qa-cf-badge">⚠ C2</span>. A call can have a high score and still have a compliance issue flagged.</p>
            <table class="qa-info-tbl">
                <tr><td><span class="qa-disp d-exceptional">EXCEPTIONAL</span></td><td>100 — Perfect score, all sub-scores 10 (essentially never)</td></tr>
                <tr><td><span class="qa-disp d-excellent">EXCELLENT</span></td><td>90–99 — Near-perfect sales performance</td></tr>
                <tr><td><span class="qa-disp d-good">GOOD</span></td><td>70–89 — Skilled closer, quality technique</td></tr>
                <tr><td><span class="qa-disp d-average">AVERAGE</span></td><td>50–69 — Functional, follows script, needs coaching</td></tr>
                <tr><td><span class="qa-disp d-poor">POOR</span></td><td>&lt; 50 — Real errors or call breakdown</td></tr>
                <tr><td><span class="qa-disp d-comp-fail">VOID RISK</span></td><td>Misrepresentation or coerced sale</td></tr>
                <tr><td><span class="qa-disp d-comp-fail qa-cf-badge" style="font-size:.65rem;">⚠ Cx</span></td><td>Compliance badge — shown alongside score when a C1–C11 check failed</td></tr>
            </table>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-lightbulb-line"></i> How It Works</h6>
            <ol>
                <li><strong>Recording captured</strong> — Zoom webhook triggers automatic capture</li>
                <li><strong>Transcription fetched</strong> — Zoom or AssemblyAI transcript with Closer/Customer speaker labels</li>
                <li><strong>QA analysis</strong> — AI scores against 11 compliance checks + 7 quality categories</li>
                <li><strong>Results saved</strong> — Scores, compliance flags, and coaching notes stored</li>
                <li><strong>Dashboard updated</strong> — Real-time metrics and closer performance tracking</li>
            </ol>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
/* ═══════════════════════════════════════════════════
   QA Dashboard v2 — Unified Single Page Module
   Matches controller response structures exactly.
   ═══════════════════════════════════════════════════ */
;(function(){
"use strict";

const $ = s => document.querySelector(s);
const API_BASE = '/qa/api';

const S = {
    currentView: 'dashboard',
    currentRange: '30d',
    currentFilter: 'all',
    callsRange: '',
    currentPage: 1,
    agentId: null,
    refreshTimer: null,
    data: null,
    charts: {}
};

function api(url) {
    return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
}

/* ── Public API ── */
window.QA = {
    presetChanged() { 
        const preset = $('#qaRangePreset').value;
        const startEl = $('#qaStartDate');
        const endEl = $('#qaEndDate');
        
        if (preset) {
            S.currentRange = preset;
            // Clear custom date inputs when using preset
            startEl.value = '';
            endEl.value = '';
        }
        S.currentPage = 1; 
        S.currentFilter = 'all'; 
        this.load(); 
    },
    rangeChanged() { 
        const startEl = $('#qaStartDate');
        const endEl = $('#qaEndDate');
        const startDate = startEl.value;
        const endDate = endEl.value;
        
        if (startDate && endDate) {
            // Custom date range format: "YYYY-MM-DD,YYYY-MM-DD"
            S.currentRange = `${startDate},${endDate}`;
            $('#qaRangePreset').value = ''; // Clear preset selection
        } else if (startDate || endDate) {
            alert('Please select both start and end dates');
            return;
        } else {
            // Fall back to preset if no dates selected
            return;
        }
        
        S.currentPage = 1; 
        S.currentFilter = 'all'; 
        this.load(); 
    },
    refresh()      { this.load(); },
    load(silent)   { S.currentView === 'dashboard' ? loadDashboard(silent) : loadAgentDetail(S.agentId); },
    viewAgent(id)  { S.agentId = id; S.currentView = 'agent-detail'; loadAgentDetail(id); },
    backToDash()   { S.currentView = 'dashboard'; S.currentPage = 1; loadDashboard(); },
    filterCalls(f) { S.currentFilter = f; S.currentPage = 1; loadDashboard(); },
    filterCallsDate(dateStr) {
        // Single date: treat as "scored on that specific day"
        S.callsRange = dateStr || '';
        S.currentPage = 1;
        // Reflect pill state without waiting for re-render
        const pill = document.getElementById('callsDatePill');
        if (pill) pill.classList.toggle('has-date', !!dateStr);
        loadDashboard();
    },
    clearCallsDate() {
        const inp = document.getElementById('callsDateInput');
        if (inp) inp.value = '';
        S.callsRange = '';
        S.currentPage = 1;
        const pill = document.getElementById('callsDatePill');
        if (pill) pill.classList.remove('has-date');
        loadDashboard();
    },
    goPage(p)      { S.currentPage = p; loadDashboard(); },
    openDetail(id) { openCallDetail(id); },
    closeDetail()  { $('#qaOverlay').classList.remove('show'); document.body.style.overflow=''; },
    deleteCall(callId) {
        if (!confirm('Delete this QA record permanently? This cannot be undone.')) return;
        fetch(`/qa/api/calls/${callId}`, {
            method: 'DELETE',
            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        }).then(r => r.json()).then(d => {
            if (d.success) { this.closeDetail(); loadDashboard(); }
            else alert('Delete failed: ' + (d.message || 'Unknown error'));
        }).catch(e => alert('Request failed: ' + e.message));
    },

    rerunToday() {
        if (!confirm("Re-score all of today's completed calls with the latest AI prompt?\n\nThis resets their results and re-queues them. It may take several minutes.")) return;
        const btn = $('#rerunTodayBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spin" style="border-top-color:#c84646;display:inline-block;width:12px;height:12px;border:2px solid rgba(200,70,70,.3);border-top-color:#c84646;border-radius:50%;animation:qaSpin .7s linear infinite;vertical-align:middle;"></span> Queueing...';
        fetch('/qa/api/rerun-today', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        }).then(r => r.json()).then(d => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-restart-line"></i> Rerun Today';
            if (d.success) {
                alert(`✅ Queued ${d.count} call(s) for re-scoring. Refresh in a few minutes to see updated results.`);
                this.refresh();
            } else {
                alert('Error: ' + (d.error || 'Unknown error'));
            }
        }).catch(e => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-restart-line"></i> Rerun Today';
            alert('Request failed: ' + e.message);
        });
    },
    toggleQa() {
        const btn = $('#qaToggleBtn');
        btn.disabled = true;
        fetch('/qa/api/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        }).then(r => r.json()).then(d => {
            btn.disabled = false;
            updateQaToggleBtn(d.qa_enabled);
            alert(d.qa_enabled
                ? '▶️ QA scoring RESUMED. Future calls will be scored automatically.'
                : '⏸ QA scoring PAUSED. Future calls will NOT be scored (saves costs). Toggle again to resume.'
            );
        }).catch(e => {
            btn.disabled = false;
            alert('Toggle failed: ' + e.message);
        });
    }
};


/* ══════════════════════════════════════════════════
   MAIN DASHBOARD
   ══════════════════════════════════════════════════ */
function loadDashboard(silent) {
    const el = $('#qa-content');
    // Silent (auto-refresh): just show a small spinner on the Refresh button — don't wipe the content
    const refreshBtn = document.querySelector('[onclick="QA.refresh()"]');
    if (silent && el.innerHTML.trim() && !el.innerHTML.includes('qa-loading')) {
        if (refreshBtn) { refreshBtn.disabled = true; refreshBtn.innerHTML = '<span class="qa-spin" style="display:inline-block;width:11px;height:11px;border:2px solid rgba(255,255,255,.25);border-top-color:#fff;border-radius:50%;animation:qaSpin .65s linear infinite;vertical-align:middle;"></span> Refreshing…'; }
    } else {
        el.innerHTML = '<div class="qa-loading"><span class="qa-spin"></span> Loading dashboard…</div>';
    }

    const callsRangeParam = S.callsRange ? `&calls_range=${S.callsRange},${S.callsRange}` : '';
    const url = `${API_BASE}/overview?range=${S.currentRange}&page=${S.currentPage}&qa_filter=${S.currentFilter}${callsRangeParam}`;
    api(url).then(d => {
        // Restore Refresh button if it was in loading state
        const refreshBtn2 = document.querySelector('[onclick="QA.refresh()"]');
        if (refreshBtn2 && refreshBtn2.disabled) { refreshBtn2.disabled = false; refreshBtn2.innerHTML = '<i class="ri-refresh-line"></i> Refresh'; }
        S.data = d;
        const ts = d.team_stats    || {};
        const ex = d.extended_kpis || {};
        const ss = d.sales_summary || {};

        const avgScore    = ts.avg_score != null ? parseFloat(ts.avg_score).toFixed(1) : '—';
        const compRate    = ts.compliance_rate != null ? parseFloat(ts.compliance_rate).toFixed(0)+'%' : '—';
        const voidCount   = parseInt(ts.void_risks||0);
        const alertCls    = voidCount > 0 ? 'kpi-alert' : 'kpi-alert';
        const ahtStr      = ex.avg_handle_time ? fmtDuration(ex.avg_handle_time) : '—';

        el.innerHTML = `
            <!-- ── Hero KPI Strip ── -->
            <div class="qa-kpi-strip">
                <div class="qa-kpi kpi-score">
                    <span class="qa-kpi-icon"><i class="ri-bar-chart-2-line"></i></span>
                    <div class="qa-kpi-value">${avgScore}</div>
                    <div class="qa-kpi-label">Avg Score</div>
                </div>
                <div class="qa-kpi kpi-comply">
                    <span class="qa-kpi-icon"><i class="ri-shield-check-line"></i></span>
                    <div class="qa-kpi-value">${compRate}</div>
                    <div class="qa-kpi-label">Compliance</div>
                </div>
                <div class="qa-kpi kpi-calls">
                    <span class="qa-kpi-icon"><i class="ri-phone-line"></i></span>
                    <div class="qa-kpi-value">${ts.calls_scored||0}</div>
                    <div class="qa-kpi-label">Calls Scored</div>
                </div>
                <div class="qa-kpi kpi-sales">
                    <span class="qa-kpi-icon"><i class="ri-hand-coin-line"></i></span>
                    <div class="qa-kpi-value">${ss.total_sales||0}</div>
                    <div class="qa-kpi-label">Sales / $${formatNum(ss.total_premium||0)}/mo</div>
                </div>
                <div class="qa-kpi ${alertCls}">
                    <span class="qa-kpi-icon"><i class="ri-alert-line"></i></span>
                    <div class="qa-kpi-value">${voidCount}</div>
                    <div class="qa-kpi-label">Void Risks</div>
                </div>
            </div>

            <!-- ── Secondary Stat Strip ── -->
            <div class="qa-stat-strip">
                <div class="qa-stat st-exceptional">
                    <div class="qa-stat-val">${ts.exceptional_count||0}</div>
                    <div class="qa-stat-lbl">Exceptional</div>
                </div>
                <div class="qa-stat st-excellent">
                    <div class="qa-stat-val">${ts.excellent_count||0}</div>
                    <div class="qa-stat-lbl">Excellent</div>
                </div>
                <div class="qa-stat st-good">
                    <div class="qa-stat-val">${ts.good_count||0}</div>
                    <div class="qa-stat-lbl">Good</div>
                </div>
                <div class="qa-stat st-average">
                    <div class="qa-stat-val">${ts.average_count||0}</div>
                    <div class="qa-stat-lbl">Average</div>
                </div>
                <div class="qa-stat st-poor">
                    <div class="qa-stat-val">${ts.poor_count||0}</div>
                    <div class="qa-stat-lbl">Poor</div>
                </div>
                <div class="qa-stat st-poor">
                    <div class="qa-stat-val">${ts.compliance_fails||0}</div>
                    <div class="qa-stat-lbl">Comp Issues</div>
                </div>
                <div class="qa-stat st-aht">
                    <div class="qa-stat-val">${ahtStr}</div>
                    <div class="qa-stat-lbl">Avg Handle Time</div>
                </div>
                <div class="qa-stat st-aht">
                    <div class="qa-stat-val">$${formatNum(ss.avg_coverage||0)}</div>
                    <div class="qa-stat-lbl">Avg Coverage</div>
                </div>
                <div class="qa-stat st-aht">
                    <div class="qa-stat-val">$${formatNum(ss.avg_premium||0)}</div>
                    <div class="qa-stat-lbl">Avg Premium</div>
                </div>
            </div>

            <!-- ── Charts: Trend (primary) + Dispositions (secondary) ── -->
            <div class="row g-2 mb-2">
                <div class="col-md-8">
                    <div class="qa-card">
                        <div class="qa-card-header"><h6><i class="ri-line-chart-line"></i> Score Trend</h6></div>
                        <div class="qa-card-body"><div class="qa-chart-wrap h200"><canvas id="trendChart"></canvas></div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="qa-card">
                        <div class="qa-card-header"><h6><i class="ri-pie-chart-2-line"></i> Dispositions</h6></div>
                        <div class="qa-card-body"><div class="qa-chart-wrap h200"><canvas id="dispChart"></canvas></div></div>
                    </div>
                </div>
            </div>

            <!-- ── Category Averages + Compliance Breakdown ── -->
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="qa-card">
                        <div class="qa-card-header"><h6><i class="ri-equalizer-line"></i> Category Averages</h6></div>
                        <div class="qa-card-body" id="catBars"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="qa-card">
                        <div class="qa-card-header"><h6><i class="ri-shield-cross-line"></i> Compliance Failures</h6></div>
                        <div class="qa-card-body" id="compBreak"></div>
                    </div>
                </div>
            </div>

            <!-- ── Closer Performance (merged) ── -->
            <div class="qa-card mb-2">
                <div class="qa-card-header"><h6><i class="ri-team-line"></i> Closer Performance</h6></div>
                <div class="qa-card-body p0"><div class="qa-scroll" style="max-height:280px;" id="closerTbl"></div></div>
            </div>

            <!-- ── Scored Calls ── -->
            <div class="qa-card mb-2">
                <div class="qa-card-header">
                    <h6><i class="ri-phone-find-line"></i> Scored Calls
                        <span id="callsDateBadge" style="display:none;font-size:.58rem;font-weight:600;padding:.1rem .4rem;border-radius:1rem;background:var(--qa-gold-dim);color:#b89730;border:1px solid rgba(212,175,55,.35);margin-left:.35rem;"></span>
                    </h6>
                    <div id="callsDatePill" class="qa-date-pill">
                        <span class="dp-icon"><i class="ri-calendar-event-line"></i></span>
                        <span class="dp-placeholder">Pick a date…</span>
                        <input type="date"
                               id="callsDateInput"
                               title="Filter calls scored on this date"
                               onchange="QA.filterCallsDate(this.value)">
                        <button class="dp-clear" onclick="QA.clearCallsDate()" title="Clear date filter" aria-label="Clear date filter">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>
                <div class="qa-calls-filter-row" id="filterRow"></div>
                <div class="qa-card-body p0">
                    <div class="qa-scroll" style="max-height:420px;" id="callsTbl"></div>
                </div>
                <div id="callsPag"></div>
            </div>

            <div id="procStatus"></div>
        `;

        renderCharts(d);
        renderCategoryBars(d.team_category_averages, 'catBars');
        renderComplianceBreakdown(d.compliance_breakdown);
        renderCloserTable(d.closer_breakdown, d.agent_leaderboard);
        renderFilterRow();
        renderCallsTable(d.calls);
        renderPagination(d.calls_pagination);
        // Restore date pill + badge after re-render
        const callsDateInp  = document.getElementById('callsDateInput');
        const callsDatePill = document.getElementById('callsDatePill');
        const callsDateBadge = document.getElementById('callsDateBadge');
        if (callsDateInp && S.callsRange) {
            callsDateInp.value = S.callsRange;
            if (callsDatePill) callsDatePill.classList.add('has-date');
            if (callsDateBadge) {
                const dt = new Date(S.callsRange + 'T12:00:00');
                callsDateBadge.textContent = dt.toLocaleDateString('en-US', {month:'short', day:'numeric'});
                callsDateBadge.style.display = 'inline';
            }
        } else if (callsDateBadge) {
            callsDateBadge.style.display = 'none';
        }
        renderProcStatus(d);

    }).catch(e => {
        const refreshBtnErr = document.querySelector('[onclick="QA.refresh()"]');
        if (refreshBtnErr && refreshBtnErr.disabled) { refreshBtnErr.disabled = false; refreshBtnErr.innerHTML = '<i class="ri-refresh-line"></i> Refresh'; }
        el.innerHTML = `<div class="qa-empty"><i class="ri-error-warning-line"></i> Failed to load — ${esc(e.message)}</div>`;
    });
}


/* ── Charts ── */
function renderCharts(d) {
    Object.values(S.charts).forEach(c => c.destroy());
    S.charts = {};

    // score_trend: {date: avg_score} object
    const trendObj = d.score_trend || {};
    const trendDates = Object.keys(trendObj);
    const trendEl = document.getElementById('trendChart');
    const trendWrap = trendEl && trendEl.closest('.qa-chart-wrap');
    if (trendDates.length && trendEl) {
        trendEl.style.display = '';
        const vals = trendDates.map(k => parseFloat(trendObj[k]));
        const mn = Math.max(0, Math.min(...vals) - 10);
        S.charts.trend = new Chart(trendEl, {
            type: 'line',
            data: {
                labels: trendDates.map(d => { const dt=new Date(d+'T12:00:00'); return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'}); }),
                datasets: [{
                    label: 'Avg Score',
                    data: vals,
                    borderColor: '#d4af37',
                    backgroundColor: (ctx) => {
                        const chart = ctx.chart;
                        const {ctx: canvasCtx, chartArea} = chart;
                        if (!chartArea) return 'rgba(212,175,55,.08)';
                        const grad = canvasCtx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        grad.addColorStop(0, 'rgba(212,175,55,.28)');
                        grad.addColorStop(1, 'rgba(212,175,55,.02)');
                        return grad;
                    },
                    fill: true, tension: .4,
                    pointRadius: vals.length <= 10 ? 5 : 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#d4af37',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    borderWidth: 2.5
                }]
            },
            options: {
                ...chartOpts(mn, 100),
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` Score: ${ctx.parsed.y}`
                        }
                    }
                }
            }
        });
    } else if (trendWrap) {
        // Show empty state inside the chart wrap
        if(trendEl) trendEl.style.display = 'none';
        const existing = trendWrap.querySelector('.qa-chart-empty');
        if (!existing) {
            const em = document.createElement('div');
            em.className = 'qa-chart-empty';
            em.innerHTML = '<i class="ri-line-chart-line"></i><br>No trend data yet';
            trendWrap.appendChild(em);
        }
    }

    // disposition_chart: {EXCELLENT: count, ...} object
    const dispObj = d.disposition_chart || {};
    const dispKeys = Object.keys(dispObj);
    if (dispKeys.length && document.getElementById('dispChart')) {
        const colors = { EXCEPTIONAL:'#9b59b6', EXCELLENT:'#2ecc71', GOOD:'#3498db', AVERAGE:'#f39c12', POOR:'#e74c3c', COMPLIANCE_FAIL:'#c0392b', VOID_RISK:'#8e44ad' };
        S.charts.disp = new Chart(document.getElementById('dispChart'), {
            type: 'doughnut',
            data: {
                labels: dispKeys.map(k => dispLabel(k)),
                datasets: [{ data: dispKeys.map(k => dispObj[k]), backgroundColor: dispKeys.map(k => colors[k]||'#6c757d'), borderWidth: 2, borderColor: 'transparent', hoverOffset: 6 }]
            },
            options: { responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{ legend:{ position:'bottom', labels:{ boxWidth:10, font:{size:9.5}, padding:7 } }, tooltip:{ callbacks:{ label: ctx => ` ${ctx.label}: ${ctx.parsed} call${ctx.parsed !== 1 ? 's' : ''}` } } } }
        });
    }
}

function chartOpts(min, max) {
    return {
        responsive:true, maintainAspectRatio:false,
        scales:{
            x:{ grid:{display:false}, ticks:{font:{size:9}, color:'#888', maxRotation:0, maxTicksLimit:10} },
            y:{ min:min, max:max, grid:{color:'rgba(255,255,255,.04)'}, ticks:{font:{size:9}, color:'#888'}, border:{display:false} }
        },
        plugins:{ legend:{ display:false } }
    };
}


/* ── Category Bars ── */
const catMaxScores = { opening:10, discovery:10, presentation:10, objection_handling:10, closing:10, soft_skills:10, call_control:10 };
const catLabels = { opening:'Opening', discovery:'Discovery', presentation:'Presentation', objection_handling:'Obj. Handling', closing:'Closing', soft_skills:'Soft Skills', call_control:'Call Control' };

function renderCategoryBars(catsObj, containerId) {
    const el = document.getElementById(containerId || 'catBars');
    if (!el) return;
    if (!catsObj || typeof catsObj !== 'object') { el.innerHTML = '<div class="qa-empty">No data</div>'; return; }
    const keys = Object.keys(catsObj).filter(k => catMaxScores[k]);
    if (!keys.length) { el.innerHTML = '<div class="qa-empty">No data</div>'; return; }
    el.innerHTML = keys.map(k => {
        const val = parseFloat(catsObj[k] || 0);
        const maxVal = catMaxScores[k] || 10;
        const pct = Math.min(100, Math.max(0, (val / maxVal) * 100));
        const catColors = { opening:'c-opening', discovery:'c-discovery', presentation:'c-presentation', objection_handling:'c-objections', closing:'c-closing', soft_skills:'c-soft', call_control:'c-control' };
        const cls = catColors[k] || (pct >= 80 ? 'f-green' : pct >= 65 ? 'f-blue' : pct >= 50 ? 'f-warn' : 'f-red');
        return `<div class="qa-cat-row">
            <div class="qa-cat-lbl">${esc(catLabels[k]||k)}</div>
            <div class="qa-cat-track"><div class="qa-cat-fill ${cls}" style="width:${pct.toFixed(0)}%"></div></div>
            <div class="qa-cat-score">${val.toFixed(1)}</div>
        </div>`;
    }).join('');
}


/* ── Compliance Breakdown ── */
function renderComplianceBreakdown(items) {
    const el = document.getElementById('compBreak');
    if (!el) return;
    if (!items || !items.length) { el.innerHTML = '<div class="qa-empty">No compliance failures</div>'; return; }
    const maxFails = Math.max(...items.map(x => x.count || 0), 1);
    el.innerHTML = items.map(c => {
        const pct = ((c.count || 0) / maxFails * 100).toFixed(0);
        return `<div class="qa-comp-row">
            <div class="qa-comp-lbl">${esc(c.check_code)}</div>
            <div class="qa-comp-name" title="${esc(c.check_label||c.check_code)}">${esc(c.check_label||c.check_code)}</div>
            <div class="qa-comp-track"><div class="qa-comp-fill" style="width:${pct}%"></div></div>
            <div class="qa-comp-count">${c.count||0}</div>
        </div>`;
    }).join('');
}


/* ── Closer Performance (merged with leaderboard) ── */
function renderCloserTable(closers, leaderboard) {
    const el = document.getElementById('closerTbl');
    if (!el) return;
    const rows = (closers && closers.length) ? closers : (leaderboard || []);
    if (!rows.length) { el.innerHTML = '<div class="qa-empty">No performance data yet</div>'; return; }
    const useCloser = !!(closers && closers.length);
    el.innerHTML = `<table class="qa-tbl">
        <thead><tr>
            <th style="width:28px;">#</th>
            <th>Closer</th>
            <th style="width:60px;">Calls</th>
            <th style="width:100px;">Score</th>
            <th style="width:60px;">Sales</th>
            <th style="width:80px;">Rate</th>
            <th style="width:90px;">Compliance</th>
        </tr></thead>
        <tbody>${rows.map((c, i) => {
        const name     = useCloser ? c.closer_name : c.agent_name;
        const agentId  = useCloser ? c.closer_id : c.agent_user_id;
        const calls    = useCloser ? (c.total_calls||0) : (c.calls_scored||0);
        const avgScore = parseFloat(c.avg_score || 0);
        const sales    = useCloser ? (c.total_sales||0) : (c.sales_count||0);
        const saleRate = useCloser ? ((c.sale_rate||0)+'%') : '—';
        const compRate = useCloser && c.compliance_rate != null ? parseFloat(c.compliance_rate).toFixed(0) : null;
        const rankIcon = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `<span style="color:var(--qa-muted);">${i+1}</span>`;
        const scoreBarW = Math.min(100, isNaN(avgScore) ? 0 : avgScore);
        const scoreBarC = scoreClass(avgScore).replace('s-','f-') || 'f-red';
        return `<tr onclick="QA.viewAgent(${agentId})">
            <td style="text-align:center;font-size:.75rem;">${rankIcon}</td>
            <td>
                <div style="font-size:.77rem;font-weight:600;">${esc(name||'Unknown')}</div>
                <div style="font-size:.62rem;color:var(--qa-muted);">${calls} call${calls!==1?'s':''}</div>
            </td>
            <td style="font-size:.72rem;">${calls}</td>
            <td>
                <div style="display:flex;align-items:center;gap:.4rem;">
                    <span class="qa-score ${scoreClass(avgScore)}" style="font-size:.82rem;width:28px;display:inline-block;">${isNaN(avgScore)?'—':avgScore.toFixed(0)}</span>
                    <div style="flex:1;height:5px;background:var(--qa-surface);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:${scoreBarW}%;background:currentColor" class="qa-score ${scoreClass(avgScore)}"></div>
                    </div>
                </div>
            </td>
            <td><span class="qa-badge b-green" style="font-size:.68rem;">${sales}</span></td>
            <td style="font-size:.72rem;">${saleRate}</td>
            <td>${compRate !== null ? `<span class="qa-badge ${compRate>=80?'b-green':'b-red'}" style="font-size:.68rem;">${compRate}%</span>` : '<span style="color:var(--qa-muted);font-size:.7rem;">—</span>'}</td>
        </tr>`;
    }).join('')}</tbody></table>`;
}


/* ── Filter Bar ── */
function renderFilterRow() {
    const el = document.getElementById('filterRow');
    if (!el) return;
    const filters = [
        { key:'all',            label:'All' },
        { key:'sales_only',     label:'💰 Sales' },
        { key:'exceptional',    label:'Exceptional' },
        { key:'excellent',      label:'Excellent' },
        { key:'good',           label:'Good' },
        { key:'average',        label:'Average' },
        { key:'poor',           label:'Poor' },
        { key:'void_risk',      label:'⚠ Void Risk' },
        { key:'compliance_fail',label:'⛔ Comp Fail' }
    ];
    el.innerHTML = `<div class="qa-filter-tabs-inline">${
        filters.map(f =>
            `<button class="qa-filter-tab-sm ${S.currentFilter===f.key?'active':''}" onclick="QA.filterCalls('${f.key}')">${f.label}</button>`
        ).join('')
    }</div>`;
}


/* ── Calls Table — review-focused ── */
function renderCallsTable(calls) {
    const el = document.getElementById('callsTbl');
    if (!el) return;
    if (!calls || !calls.length) { el.innerHTML = '<div class="qa-empty"><i class="ri-phone-off-line" style="font-size:1.5rem;display:block;margin-bottom:.4rem;"></i>No calls found</div>'; return; }
    el.innerHTML = `<table class="qa-tbl qa-calls-tbl">
        <thead><tr>
            <th style="width:180px;">Customer</th>
            <th>Closer</th>
            <th style="width:110px;">Date</th>
            <th style="width:70px;">Score</th>
            <th style="width:140px;">Disposition</th>
            <th style="width:60px;">Sale</th>
            <th style="width:36px;"></th>
        </tr></thead>
        <tbody>${calls.map(c => {
        const sc = scoreClass(c.total_score);
        const score = c.total_score != null ? parseFloat(c.total_score).toFixed(0) : '—';
        return `<tr class="qa-call-row" onclick="QA.openDetail(${c.id})">
            <td>
                <div class="qa-call-customer">${esc(c.customer_name||'Unknown')}</div>
                <div class="qa-call-phone">${fmtPhone(c.callee_number)}</div>
            </td>
            <td style="font-size:.73rem;">${esc(c.agent_name||'—')}</td>
            <td style="font-size:.68rem;white-space:nowrap;color:var(--qa-muted);">${fmtTime(c.call_start_time)}</td>
            <td>
                <div class="qa-score-cell ${sc}">${score}</div>
            </td>
            <td>${renderDisp(c)}</td>
            <td>${c.is_sale
                ? '<span class="qa-sale-yes"><i class="ri-check-line"></i> Sale</span>'
                : '<span class="qa-sale-no">—</span>'}
            </td>
            <td style="text-align:center;"><i class="ri-arrow-right-s-line qa-row-arrow"></i></td>
        </tr>`;
    }).join('')}</tbody></table>`;
}


/* ── Pagination ── */
function renderPagination(pag) {
    const el = document.getElementById('callsPag');
    if (!el || !pag || pag.last_page <= 1) { if(el) el.innerHTML = ''; return; }
    let btns = '';
    btns += `<button ${pag.current_page<=1?'disabled':''} onclick="QA.goPage(${pag.current_page-1})">&laquo;</button>`;
    for (let p = 1; p <= pag.last_page; p++) {
        if (pag.last_page > 7 && Math.abs(p - pag.current_page) > 2 && p !== 1 && p !== pag.last_page) {
            if (p === 2 || p === pag.last_page - 1) btns += `<button disabled>...</button>`;
            continue;
        }
        btns += `<button class="${p===pag.current_page?'active':''}" onclick="QA.goPage(${p})">${p}</button>`;
    }
    btns += `<button ${pag.current_page>=pag.last_page?'disabled':''} onclick="QA.goPage(${pag.current_page+1})">&raquo;</button>`;
    el.innerHTML = `<div class="qa-pagination">${btns}</div>`;
}


/* ── Processing Status ── */
function renderProcStatus(d) {
    const el = document.getElementById('procStatus');
    if (!el) return;
    const parts = [];
    if (d.processing_now) parts.push(`<span class="qa-badge b-blue"><i class="ri-loader-4-line"></i> ${d.processing_now} processing</span>`);
    if (d.pending_count)  parts.push(`<span class="qa-badge b-gold">${d.pending_count} pending</span>`);
    if (d.failed_count)   parts.push(`<span class="qa-badge b-red">${d.failed_count} failed</span>`);
    el.innerHTML = parts.length ? `<div class="qa-proc-badge">${parts.join('')}</div>` : '';
}


/* ══════════════════════════════════════════════════
   AGENT DETAIL VIEW
   ══════════════════════════════════════════════════ */
function loadAgentDetail(agentId) {
    const el = $('#qa-content');
    el.innerHTML = '<div class="qa-loading"><span class="qa-spin"></span> Loading closer detail…</div>';

    api(`${API_BASE}/agents/${agentId}?range=${S.currentRange}`).then(d => {
        const a  = d.agent   || {};
        const sm = d.summary || {};
        const calls = d.calls || [];

        el.innerHTML = `
            <button class="qa-back-link" onclick="QA.backToDash()">
                <i class="ri-arrow-left-s-line"></i> Back to Dashboard
            </button>

            <div class="qa-card mb-2">
                <div class="qa-agent-banner">
                    <div class="qa-agent-avatar">${esc((a.name||'?')[0].toUpperCase())}</div>
                    <div class="qa-agent-meta">
                        <h6>${esc(a.name||'Unknown Closer')}</h6>
                        <p>${esc(a.email||'')}</p>
                    </div>
                    <div class="qa-agent-stats">
                        <div class="qa-agent-stat text-center">
                            <div class="val" style="color:var(--qa-blue)">${sm.calls_scored||0}</div>
                            <div class="lbl">Calls</div>
                        </div>
                        <div class="qa-agent-stat text-center">
                            <div class="val" style="color:var(--qa-gold)">${sm.avg_score != null ? parseFloat(sm.avg_score).toFixed(1) : '—'}</div>
                            <div class="lbl">Avg Score</div>
                        </div>
                        <div class="qa-agent-stat text-center">
                            <div class="val" style="color:var(--qa-green)">${sm.excellent_count||0}</div>
                            <div class="lbl">Excellent</div>
                        </div>
                        <div class="qa-agent-stat text-center">
                            <div class="val" style="color:var(--qa-red)">${sm.compliance_fails||0}</div>
                            <div class="lbl">Comp Issues</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="qa-card">
                        <div class="qa-card-header"><h6><i class="ri-equalizer-line"></i> Category Scores</h6></div>
                        <div class="qa-card-body" id="agentCatBars"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="qa-card">
                        <div class="qa-card-header"><h6><i class="ri-line-chart-line"></i> Score Trend</h6></div>
                        <div class="qa-card-body"><div class="qa-chart-wrap h160"><canvas id="agentTrendChart"></canvas></div></div>
                    </div>
                </div>
            </div>

            <div class="qa-card">
                <div class="qa-card-header"><h6><i class="ri-phone-line"></i> Calls <span style="font-weight:400;font-size:.65rem;margin-left:.3rem;">(${calls.length})</span></h6></div>
                <div class="qa-card-body p0">
                    <div class="qa-scroll" style="max-height:360px;" id="agentCallsTbl"></div>
                </div>
            </div>
        `;

        renderCategoryBars(d.category_averages, 'agentCatBars');

        Object.values(S.charts).forEach(c => c.destroy());
        S.charts = {};
        const trend = d.score_trend || [];
        if (trend.length && document.getElementById('agentTrendChart')) {
            const labels = trend.map(t => t.date);
            const values = trend.map(t => t.avg_score);
            S.charts.agentTrend = new Chart(document.getElementById('agentTrendChart'), {
                type: 'line',
                data: { labels, datasets: [{ label:'Score', data:values, borderColor:'#d4af37', backgroundColor:'rgba(212,175,55,.08)', fill:true, tension:.35, pointRadius:2, borderWidth:2 }] },
                options: chartOpts(0, 100)
            });
        }

        const act = document.getElementById('agentCallsTbl');
        if (act) {
            if (!calls.length) { act.innerHTML = '<div class="qa-empty">No calls</div>'; }
            else {
                act.innerHTML = `<table class="qa-tbl"><thead><tr>
                    <th>Customer</th><th>Date</th><th>Duration</th><th>Score</th><th>Disposition</th><th>Sale</th>
                </tr></thead><tbody>${calls.map(c => `<tr onclick="QA.openDetail(${c.id})">
                    <td style="font-size:.73rem;">${esc(c.customer_name||'Unknown')}</td>
                    <td style="font-size:.69rem;white-space:nowrap;">${fmtTime(c.call_start_time)}</td>
                    <td style="font-size:.69rem;">${fmtDuration(c.duration_seconds)}</td>
                    <td><span class="qa-score ${scoreClass(c.total_score)}">${c.total_score != null ? parseFloat(c.total_score).toFixed(0) : '—'}</span></td>
                    <td>${renderDisp(c)}</td>
                    <td>${c.is_sale ? '<span class="qa-badge b-green">YES</span>' : '<span class="qa-badge b-gray">NO</span>'}</td>
                </tr>`).join('')}</tbody></table>`;
            }
        }
    }).catch(e => { el.innerHTML = `<div class="qa-empty">Error: ${esc(e.message)}</div>`; });
}


/* ══════════════════════════════════════════════════
   CALL DETAIL OVERLAY
   ══════════════════════════════════════════════════ */
function openCallDetail(callId) {
    const overlay = $('#qaOverlay');
    const content = $('#qaOverlayContent');
    content.innerHTML = '<div class="qa-loading" style="color:#64748b;"><span class="qa-spin" style="border-top-color:#d4af37;border-color:#e2e8f0;"></span> Loading call…</div>';
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';

    const compLabels = {
        C1_agent_identity:            'C1  Closer Introduction',
        C2_carrier_named:             'C2  Carrier Named',
        C3_product_type_stated:       'C3  Product Type Stated',
        C4_health_questions_complete: 'C4  Health Questions',
        C5_quote_and_coverage:        'C5  Quote & Coverage',
        C6_draft_date_confirmed:      'C6  Draft Date Confirmed',
        C7_recorded_consent:          'C7  Recorded Consent',
        C8_application_info_collected:'C8  Application Info',
        C9_customer_not_on_dnc:       'C9  DNC Honored',
        C10_agent_handles_objections: 'C10 Handles Objections',
        C11_appropriate_language:     'C11 Appropriate Language',
    };

    api(`${API_BASE}/calls/${callId}`).then(d => {
        const c = d.call || {};
        const r = d.qa_result || {};
        const scoreBreak = r.score_breakdown || {};
        const compChecks   = r.compliance_checks || {};
        const compDetails  = r.compliance_details || {};
        const coaching     = r.coaching_notes;
        const transcript = d.transcript || [];
        const sc         = scoreClass(r.total_score);
        const ringCls    = sc.replace('s-', 'sr-') || 'sr-poor';
        const score      = r.total_score != null ? parseFloat(r.total_score).toFixed(0) : '—';
        const topIssue   = r.top_issue || null;
        const strengths  = r.strengths || [];
        const improvements = r.improvements || [];
        const voidReason = r.void_risk_reason || null;
        const compFailures = r.compliance_failures || [];
        const audioNote  = (r.informational_notes || {}).audio_quality || null;
        const dncJudge   = r.dnc_judge || {};
        const dncRisk    = dncJudge.risk_level || 'NONE';
        const dncVerdict = dncJudge.verdict    || 'Clean';
        const dncReason  = dncJudge.reasoning  || null;

        // Sale info block
        const saleBlock = c.is_sale ? `
            <div class="qa-sale-block">
                <i class="bx bx-shield-check sb-icon"></i>
                ${c.sale_amount    ? `<div class="sb-item"><div class="sb-val">$${parseFloat(c.sale_amount).toLocaleString()}</div><div class="sb-lbl">Coverage</div></div>` : ''}
                ${c.monthly_premium? `<div class="sb-item"><div class="sb-val">$${parseFloat(c.monthly_premium).toFixed(2)}/mo</div><div class="sb-lbl">Premium</div></div>` : ''}
                ${c.carrier_name   ? `<div class="sb-item"><div class="sb-val">${esc(c.carrier_name)}</div><div class="sb-lbl">Carrier</div></div>` : ''}
                <div class="sb-item"><div class="sb-val" style="color:#34c38f;">✓ SALE</div><div class="sb-lbl">Status</div></div>
            </div>` : '';

        // Void risk warning
        const voidBlock = (r.disposition === 'VOID_RISK' && voidReason) ? `
            <div style="background:rgba(244,106,106,.06);border:1px solid rgba(244,106,106,.2);border-radius:.5rem;padding:.65rem .85rem;margin-bottom:.5rem;">
                <div style="font-weight:700;color:var(--qa-red);font-size:.82rem;margin-bottom:.25rem;"><i class="bx bx-error"></i> Void Risk</div>
                <div style="font-size:.8rem;color:var(--bs-body-color);line-height:1.4;">${esc(voidReason)}</div>
            </div>` : '';

        // Audio quality warning
        const audioBlock = audioNote ? `
            <div style="background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.2);border-radius:.5rem;padding:.55rem .85rem;margin-bottom:.5rem;">
                <div style="font-weight:600;color:var(--qa-warn);font-size:.8rem;"><i class="bx bx-volume-mute"></i> Audio Quality Note</div>
                <div style="font-size:.78rem;color:var(--bs-body-color);margin-top:.15rem;">${esc(audioNote)}</div>
            </div>` : '';

        // Compliance (failures first, then NA, then passes) — dot/grid style matching upload page
        const compKeys = Object.keys(compChecks);
        const failKeys = compKeys.filter(k => compChecks[k] === false);
        const passKeys = compKeys.filter(k => compChecks[k] === true);
        const naKeys   = compKeys.filter(k => compChecks[k] === null);
        const sortedKeys = [...failKeys, ...naKeys, ...passKeys];

        const checklistHtml = !compKeys.length
            ? `<div style="font-size:.72rem;color:var(--qa-muted);padding:.5rem 0;">No compliance data</div>`
            : `<div style="display:grid;grid-template-columns:1fr;gap:.25rem;">${sortedKeys.map(k => {
                    const val     = compChecks[k];
                    const isPass  = val === true;
                    const isFail  = val === false;
                    const dot     = isPass ? 'dot-pass' : (isFail ? 'dot-fail' : 'dot-na');
                    const txt     = isPass ? 'pass'     : (isFail ? 'fail'     : 'n/a');
                    const reason  = compDetails[k] || '';
                    const reasonColor = isFail ? 'var(--qa-red)' : isPass ? 'var(--qa-green)' : 'rgba(255,255,255,.35)';
                    return `<div style="display:flex;flex-direction:column;align-items:flex-start;padding:.35rem .4rem;border-radius:.3rem;${isFail ? 'background:rgba(244,106,106,.06);' : ''}">
                        <div style="display:flex;align-items:center;gap:.45rem;width:100%;">
                            <span class="qu-check-dot ${dot}" style="flex-shrink:0;"></span>
                            <span style="flex:1;font-size:.7rem;font-weight:${isFail?'600':'400'};color:${isFail?'var(--qa-red)':'inherit'};">${esc(compLabels[k]||k)}</span>
                            <span style="font-size:.6rem;font-weight:700;padding:.1rem .35rem;border-radius:.6rem;${isPass?'background:rgba(52,195,143,.12);color:var(--qa-green);':isFail?'background:rgba(244,106,106,.12);color:var(--qa-red);':'background:rgba(255,255,255,.07);color:rgba(255,255,255,.35);'}">${esc(txt)}</span>
                        </div>
                        ${reason ? `<div style="font-size:.63rem;color:${reasonColor};margin-top:.22rem;padding-left:1.05rem;line-height:1.4;">${esc(reason)}</div>` : ''}
                    </div>`;
                }).join('')}
              ${failKeys.length ? `<div style="margin-top:.3rem;font-size:.67rem;color:var(--qa-red);">⚠ ${failKeys.length} failure${failKeys.length>1?'s':''}: ${failKeys.map(k=>(compLabels[k]||k).trim()).join(', ')}</div>` : ''}
              </div>`;

        // Score breakdown bars — qu-breakdown 2-col grid
        const catMaxes   = { opening:10, discovery:10, presentation:10, objection_handling:10, closing:10, soft_skills:10, call_control:10 };
        const catDisplay = { opening:'Opening', discovery:'Discovery', presentation:'Presentation', objection_handling:'Obj. Handling', closing:'Closing', soft_skills:'Soft Skills', call_control:'Call Control' };
        const barsHtml = scoreBreak && Object.keys(scoreBreak).length
            ? `<div class="qu-breakdown">${Object.entries(catMaxes).map(([key, maxPts]) => {
                    const val = parseFloat(scoreBreak[key] || 0);
                    const pct = Math.min((val / maxPts) * 100, 100);
                    return `<div class="qu-bar-row">
                        <span class="qu-bar-label">${esc(catDisplay[key]||key)}</span>
                        <div class="qu-bar-outer"><div class="qu-bar-inner" style="width:${pct.toFixed(0)}%;"></div></div>
                        <span class="qu-bar-val">${val.toFixed(0)}</span>
                    </div>`;
                }).join('')}</div>`
            : `<div style="font-size:.72rem;color:var(--qa-muted);">No breakdown data</div>`;

        // Score number CSS class
        const scoreNumCls = sc ? sc.replace('s-','') : 'poor';

        // Compliance badge
        const compBadgeHtml = r.compliance_pass === true
            ? '<span class="comp-badge comp-pass" style="margin-top:.3rem;display:inline-flex;"><i class="bx bx-check"></i> PASS</span>'
            : r.compliance_pass === false
                ? '<span class="comp-badge comp-fail" style="margin-top:.3rem;display:inline-flex;"><i class="bx bx-x"></i> FAIL</span>'
                : '';

        // Sale badge (score hero)
        const saleBadgeHtml = c.is_sale
            ? `<span style="font-size:.66rem;color:var(--qa-green);font-weight:700;margin-top:.25rem;display:block;">💰 SALE${c.monthly_premium ? ` · $${parseFloat(c.monthly_premium).toFixed(2)}/mo` : ''}</span>`
            : '';

        // Coaching notes
        let coachingText = '—';
        if (coaching) {
            coachingText = Array.isArray(coaching) ? coaching.join('\n') : String(coaching);
        }

        // DNC Judge card
        const dncColorMap = { HIGH: 'var(--qa-red)', MEDIUM: '#f97316', LOW: '#eab308', NONE: 'var(--qa-green)' };
        const dncBgMap    = { HIGH: 'rgba(244,106,106,.07)', MEDIUM: 'rgba(249,115,22,.06)', LOW: 'rgba(234,179,8,.06)', NONE: 'rgba(52,195,143,.05)' };
        const dncBorderMap= { HIGH: 'rgba(244,106,106,.25)', MEDIUM: 'rgba(249,115,22,.2)', LOW: 'rgba(234,179,8,.2)', NONE: 'rgba(52,195,143,.18)' };
        const dncIconMap  = { HIGH: 'bx-error', MEDIUM: 'bx-error-circle', LOW: 'bx-info-circle', NONE: 'bx-check-circle' };
        const dncColor    = dncColorMap[dncRisk]   || dncColorMap.NONE;
        const dncBg       = dncBgMap[dncRisk]      || dncBgMap.NONE;
        const dncBorder   = dncBorderMap[dncRisk]  || dncBorderMap.NONE;
        const dncIcon     = dncIconMap[dncRisk]    || dncIconMap.NONE;
        const dncBlock = `
            <div class="col-12">
                <div class="qu-card" style="border:1px solid ${dncBorder};background:${dncBg};">
                    <div class="qu-card-hdr" style="border-bottom:1px solid ${dncBorder};">
                        <h6 style="color:${dncColor};"><i class="bx ${dncIcon}"></i> DNC Risk Judge <span style="font-size:.65rem;font-weight:500;opacity:.7;">(standalone — does not affect score)</span></h6>
                        <span style="font-size:.7rem;font-weight:700;padding:.15rem .5rem;border-radius:1rem;background:${dncBg};color:${dncColor};border:1px solid ${dncBorder};">${esc(dncRisk)}</span>
                    </div>
                    <div class="qu-card-body" style="display:flex;gap:1.2rem;align-items:flex-start;flex-wrap:wrap;">
                        <div style="flex-shrink:0;">
                            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--qa-muted);margin-bottom:.2rem;">Verdict</div>
                            <div style="font-size:.88rem;font-weight:700;color:${dncColor};">${esc(dncVerdict)}</div>
                        </div>
                        ${dncReason ? `<div style="flex:1;min-width:200px;">
                            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--qa-muted);margin-bottom:.2rem;">AI Analysis</div>
                            <div style="font-size:.77rem;line-height:1.5;color:var(--bs-body-color);">${esc(dncReason)}</div>
                        </div>` : ''}
                    </div>
                </div>
            </div>`;

        // Transcript
        const transcriptLines = !transcript.length
            ? '<span style="color:var(--qa-muted);font-style:italic;">No transcript available</span>'
            : transcript.map(line => {
                const isAgent = line.speaker === 'AGENT';
                const isCust  = line.speaker === 'CUSTOMER';
                const spCls   = isAgent ? 't-agent' : isCust ? 't-customer' : '';
                const spLbl   = isAgent ? 'AGENT' : isCust ? 'CUSTOMER' : (line.speaker || 'UNKNOWN');
                return `<div class="t-line"><span class="t-speaker ${spCls}">${esc(spLbl)}:</span><span class="t-text">${esc(line.text)}</span></div>`;
            }).join('');

        content.innerHTML = `
            <!-- Overlay Header -->
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.9rem 1rem .7rem;border-bottom:1px solid rgba(255,255,255,.07);">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.95rem;font-weight:700;line-height:1.2;margin-bottom:.3rem;">${esc(c.customer_name||'Unknown Customer')}</div>
                    <div class="qa-call-meta">
                        <div class="qa-call-meta-item"><i class="bx bx-headphone"></i> ${esc(c.agent_name||'Unknown')}</div>
                        <div class="qa-call-meta-item"><i class="bx bx-phone"></i> ${fmtPhone(c.lead_phone||c.callee_number||c.caller_number)}</div>
                        <div class="qa-call-meta-item"><i class="bx bx-calendar"></i> ${fmtTime(c.call_start_time)}</div>
                        <div class="qa-call-meta-item"><i class="bx bx-time-five"></i> ${fmtDuration(c.duration_seconds)}</div>
                        ${c.carrier_name && !c.is_sale ? `<div class="qa-call-meta-item"><i class="bx bx-building"></i> ${esc(c.carrier_name)}</div>` : ''}
                        ${failKeys.length ? `<div class="qa-call-meta-item"><span class="qa-badge b-red">${failKeys.length} fail${failKeys.length>1?'s':''}</span></div>` : '<div class="qa-call-meta-item"><span class="qa-badge b-green">Compliant</span></div>'}
                    </div>
                </div>
                <button class="qa-overlay-close" onclick="QA.closeDetail()" style="font-size:1.5rem;">&times;</button>
            </div>

            <!-- Action Bar -->
            <div class="qa-review-bar">
                <button class="qa-review-btn rv-delete" onclick="QA.deleteCall(${callId})" title="Delete this QA record"><i class="bx bx-trash"></i> Delete</button>
            </div>

            <!-- Body -->
            <div style="padding:.85rem .9rem;">
                ${voidBlock}${audioBlock}${saleBlock}

                <div class="row g-2">

                    <!-- Score hero -->
                    <div class="col-md-3">
                        <div class="qu-card h-100">
                            <div class="qu-card-hdr"><h6><i class="bx bx-trophy"></i> Score</h6></div>
                            <div class="qu-card-body qu-score-hero">
                                <div class="qu-score-num ${scoreNumCls}">${score}</div>
                                <div class="qu-score-label">Total Score</div>
                                <div class="mt-1">${renderDisp(r)}</div>
                                ${compBadgeHtml}
                                ${saleBadgeHtml}
                            </div>
                        </div>
                    </div>

                    <!-- Score breakdown -->
                    <div class="col-md-5">
                        <div class="qu-card h-100">
                            <div class="qu-card-hdr"><h6><i class="bx bx-bar-chart-alt-2"></i> Score Breakdown</h6></div>
                            <div class="qu-card-body">${barsHtml}</div>
                        </div>
                    </div>

                    <!-- Compliance -->
                    <div class="col-md-4">
                        <div class="qu-card h-100">
                            <div class="qu-card-hdr"><h6><i class="bx bx-shield-check"></i> Compliance</h6></div>
                            <div class="qu-card-body">${checklistHtml}</div>
                        </div>
                    </div>

                    <!-- Coaching notes -->
                    <div class="col-12">
                        <div class="qu-card">
                            <div class="qu-card-hdr"><h6><i class="bx bx-comment-dots"></i> AI Coaching Notes</h6></div>
                            <div class="qu-card-body">
                                <div class="qu-coaching">${esc(coachingText)}</div>
                                <div class="mt-2 d-flex gap-3" style="font-size:.72rem;flex-wrap:wrap;">
                                    ${topIssue ? `<span><span style="color:var(--qa-muted);">Top issue:</span> <span style="color:var(--qa-red);">${esc(topIssue)}</span></span>` : ''}
                                    ${c.customer_name ? `<span><span style="color:var(--qa-muted);">Customer:</span> <span>${esc(c.customer_name)}</span></span>` : ''}
                                    ${c.carrier_name ? `<span><span style="color:var(--qa-muted);">Carrier:</span> <span>${esc(c.carrier_name)}</span></span>` : ''}
                                </div>
                                ${(strengths.length || improvements.length) ? `
                                <div class="mt-2 row g-2">
                                    ${strengths.length ? `<div class="col-md-6">
                                        <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--qa-green);margin-bottom:.25rem;">Strengths</div>
                                        <ul style="margin:0;padding-left:1.1rem;font-size:.72rem;line-height:1.55;">${strengths.map(s=>`<li>${esc(s)}</li>`).join('')}</ul>
                                    </div>` : ''}
                                    ${improvements.length ? `<div class="col-md-6">
                                        <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--qa-warn);margin-bottom:.25rem;">Areas for Improvement</div>
                                        <ul style="margin:0;padding-left:1.1rem;font-size:.72rem;line-height:1.55;">${improvements.map(s=>`<li>${esc(s)}</li>`).join('')}</ul>
                                    </div>` : ''}
                                </div>` : ''}
                            </div>
                        </div>
                    </div>

                    <!-- DNC Risk Judge -->
                    ${dncBlock}

                    <!-- Transcript (always open) -->
                    <div class="col-12">
                        <div class="qu-card">
                            <div class="qu-card-hdr">
                                <h6><i class="bx bx-file"></i> Transcript <span style="font-weight:400;opacity:.6;">(${transcript.length} lines)</span></h6>
                            </div>
                            <div class="qu-card-body">
                                <div class="qu-transcript" style="max-height:500px;overflow-y:auto;background:#fff;border:1px solid #e2e8f0;border-radius:.4rem;padding:.75rem 1rem;">${transcriptLines}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        `;



    }).catch(e => {
        content.innerHTML = `<div class="qa-empty" style="color:var(--qa-muted);">Error loading call: ${esc(e.message)}</div>`;
    });
}

function toggleSection(sectionId, headerEl) {
    const sec = document.getElementById(sectionId);
    if (!sec) return;
    const icon = headerEl && headerEl.querySelector('.qa-toggle-icon');
    const open = sec.style.display !== 'none';
    sec.style.display = open ? 'none' : '';
    if (icon) icon.className = open ? 'ri-arrow-right-s-line qa-toggle-icon' : 'ri-arrow-down-s-line qa-toggle-icon';
}


/* ══════════════════════════════════════════════════
   UTILITIES
   ══════════════════════════════════════════════════ */
function scoreClass(s) { s=parseFloat(s); if(isNaN(s)) return ''; return s>=100?'s-exceptional':s>=90?'s-excellent':s>=70?'s-good':s>=50?'s-average':'s-poor'; }
function dispClass(d) { return 'd-'+(d||'').toLowerCase().replace(/_/g,'-'); }
function dispLabel(d) { return (d||'N/A').replace(/_/g,' '); }
// renderDisp: shows score-based disposition + compliance fail badge when applicable
function renderDisp(c) {
    const disp = c.disposition || 'N/A';
    // Handle legacy COMPLIANCE_FAIL records gracefully
    const legacyFail = disp === 'COMPLIANCE_FAIL';
    const baseLbl  = legacyFail ? 'COMP FAIL' : dispLabel(disp);
    const baseCls  = legacyFail ? 'd-comp-fail' : dispClass(disp);
    let html = `<span class="qa-disp ${baseCls}">${esc(baseLbl)}</span>`;
    // For VOID_RISK, also show the score-based quality badge
    if (disp === 'VOID_RISK' && c.score_disposition) {
        const scoreCls = dispClass(c.score_disposition);
        const scoreLbl = dispLabel(c.score_disposition);
        html += ` <span class="qa-disp ${scoreCls}" title="Sales quality: ${esc(scoreLbl)}">${esc(scoreLbl)}</span>`;
    }
    // Add compliance fail badge for new records that have a score-based disposition
    if (!legacyFail && c.compliance_pass === false) {
        const codes = Array.isArray(c.compliance_failures) && c.compliance_failures.length
            ? c.compliance_failures.map(f => f.replace(/_.*/, '')).join(', ')
            : '!';
        html += ` <span class="qa-disp d-comp-fail qa-cf-badge" title="Compliance issue: ${esc(codes)}">⚠ ${esc(codes)}</span>`;
    }
    return html;
}
function fmtPhone(p) { if(!p) return '—'; p=String(p).replace(/\D/g,''); return p.length===10?`(${p.slice(0,3)}) ${p.slice(3,6)}-${p.slice(6)}`:p.length===11?`+${p[0]} (${p.slice(1,4)}) ${p.slice(4,7)}-${p.slice(7)}`:esc(String(p)); }
function fmtTime(t) { if(!t) return '—'; const d=new Date(t); const opts={timeZone:'America/Los_Angeles'}; const h=d.toLocaleTimeString('en-US',{...opts,hour:'numeric',minute:'2-digit',hour12:false}); const isMidnight=(h==='00:00'||h==='0:00'); return d.toLocaleDateString('en-US',{...opts,month:'short',day:'numeric'})+(isMidnight?'':' '+d.toLocaleTimeString('en-US',{...opts,hour:'numeric',minute:'2-digit'})+' PT'); }
function fmtDuration(s) { if(!s&&s!==0) return '—'; s=parseInt(s); const m=Math.floor(s/60); const sec=s%60; return m>0?m+'m '+sec+'s':sec+'s'; }
function formatNum(n) { if(!n&&n!==0) return '0'; n=parseFloat(n); return n>=1000?(n/1000).toFixed(1)+'k':n.toFixed(0); }
function esc(s) { if(!s) return ''; const d=document.createElement('div'); d.textContent=String(s); return d.innerHTML; }

function updateQaToggleBtn(enabled) {
    const btn  = $('#qaToggleBtn');
    const icon = $('#qaToggleIcon');
    const lbl  = $('#qaToggleLabel');
    if (!btn) return;
    btn.className = 'qa-action-btn ' + (enabled ? 'qa-btn-success' : 'qa-btn-secondary');
    icon.className = enabled ? 'ri-pause-circle-line' : 'ri-play-circle-line';
    lbl.textContent = enabled ? 'Active' : 'Paused';
    btn.title = enabled ? 'Click to PAUSE QA scoring' : 'Click to RESUME QA scoring';
}

function loadQaStatus() {
    api('/qa/api/qa-status').then(d => {
        updateQaToggleBtn(d.qa_enabled);
    }).catch(() => {});
}

/* ── Init ── */
@if($myMode && $myUserId)
// Personal QA report mode — auto-load only this closer's detail
const __myMode   = true;
const __myUserId = {{ (int) $myUserId }};
const __myBackUrl = @json($myBackUrl ?? null);
// Override backToDash to go back to the expected dashboard
QA.backToDash = function() {
    if (__myBackUrl) { window.location.href = __myBackUrl; }
};
S.currentView = 'agent-detail';
S.agentId = __myUserId;
S.currentRange = '30d';
loadAgentDetail(__myUserId);
@else
const __myMode = false;
loadDashboard();
loadQaStatus();
S.refreshTimer = setInterval(() => { if (S.currentView === 'dashboard') loadDashboard(true); }, 60000);
@endif

// Auto-open call detail if ?call= param is present (e.g. from upload page link)
const urlParams = new URLSearchParams(window.location.search);
const autoCallId = urlParams.get('call');
if (autoCallId && !isNaN(autoCallId)) {
    openCallDetail(parseInt(autoCallId));
    // Clean up the URL so refreshing doesn't re-open
    window.history.replaceState({}, '', window.location.pathname);
}

})();
</script>
@endsection
