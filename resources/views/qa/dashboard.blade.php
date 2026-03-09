@extends('layouts.master')

@section('title', 'QA Scoring Dashboard')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   QA Scoring Dashboard v2 — Unified Single Page
   ═══════════════════════════════════════════════════ */

.ex-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: box-shadow .2s;
}
.ex-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* KPI Cards */
.kpi-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.kpi-card {
    flex: 1 1 80px; min-width: 75px; padding: 0.65rem 0.6rem;
    border-radius: 0.55rem; text-align: center; position: relative;
    overflow: hidden; border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:.55rem .55rem 0 0; }
.kpi-card .k-icon { font-size: 1rem; margin-bottom: 0.2rem; display: block; opacity: .7; }
.kpi-card .k-val { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.kpi-card .k-lbl { font-size:.58rem; text-transform:uppercase; font-weight:600; letter-spacing:.4px; color:var(--bs-surface-500); margin-top:.2rem; }

.kpi-card.k-gold    { background:rgba(212,175,55,.06); }
.kpi-card.k-gold::before    { background:linear-gradient(90deg,#d4af37,#e8c84a); }
.kpi-card.k-gold .k-val, .kpi-card.k-gold .k-icon { color:#b89730; }
.kpi-card.k-blue    { background:rgba(85,110,230,.06); }
.kpi-card.k-blue::before    { background:linear-gradient(90deg,#556ee6,#8b9cf7); }
.kpi-card.k-blue .k-val, .kpi-card.k-blue .k-icon { color:#556ee6; }
.kpi-card.k-green   { background:rgba(52,195,143,.06); }
.kpi-card.k-green::before   { background:linear-gradient(90deg,#34c38f,#6eddb8); }
.kpi-card.k-green .k-val, .kpi-card.k-green .k-icon { color:#1a8754; }
.kpi-card.k-teal    { background:rgba(80,165,241,.06); }
.kpi-card.k-teal::before    { background:linear-gradient(90deg,#50a5f1,#8cc5f7); }
.kpi-card.k-teal .k-val, .kpi-card.k-teal .k-icon { color:#2b81c9; }
.kpi-card.k-red     { background:rgba(244,106,106,.06); }
.kpi-card.k-red::before     { background:linear-gradient(90deg,#f46a6a,#f89b9b); }
.kpi-card.k-red .k-val, .kpi-card.k-red .k-icon { color:#c84646; }
.kpi-card.k-warn    { background:rgba(241,180,76,.06); }
.kpi-card.k-warn::before    { background:linear-gradient(90deg,#f1b44c,#f5cd7e); }
.kpi-card.k-warn .k-val, .kpi-card.k-warn .k-icon { color:#b87a14; }
.kpi-card.k-purple  { background:rgba(124,105,239,.06); }
.kpi-card.k-purple::before  { background:linear-gradient(90deg,#7c69ef,#a899f5); }
.kpi-card.k-purple .k-val, .kpi-card.k-purple .k-icon { color:#5b49c7; }
.kpi-card.k-gray    { background:rgba(108,117,125,.05); }
.kpi-card.k-gray::before    { background:linear-gradient(90deg,#6c757d,#95a0a8); }
.kpi-card.k-gray .k-val, .kpi-card.k-gray .k-icon { color:#6c757d; }

/* Section cards */
.sec-card { padding:0; margin-bottom:.65rem; overflow:hidden; }
.sec-hdr { display:flex; justify-content:space-between; align-items:center; padding:.5rem .75rem; border-bottom:1px solid rgba(0,0,0,.05); flex-wrap:wrap; gap:.4rem; }
.sec-hdr h6 { margin:0; font-size:.78rem; font-weight:600; display:flex; align-items:center; gap:.3rem; }
.sec-hdr h6 i { opacity:.6; font-size:.95rem; }
.sec-body { padding:.6rem .75rem; }

/* Tables */
.ex-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:.75rem; }
.ex-tbl thead th { text-transform:uppercase; font-size:.6rem; font-weight:700; letter-spacing:.5px; color:var(--bs-surface-500); padding:.4rem .5rem; border-bottom:1px solid var(--bs-surface-200); white-space:nowrap; background:var(--bs-surface-100); position:sticky; top:0; z-index:1; }
.ex-tbl tbody td { padding:.4rem .5rem; border-bottom:1px solid rgba(0,0,0,.03); vertical-align:middle; }
.ex-tbl tbody tr { transition:background .12s; }
.ex-tbl tbody tr:hover { background:rgba(212,175,55,.03); }

/* Badges */
.bd-mini { font-size:.6rem; font-weight:700; padding:.15rem .4rem; border-radius:.25rem; display:inline-block; min-width:22px; text-align:center; }
.bd-mini.bd-blue   { background:rgba(85,110,230,.12); color:#556ee6; }
.bd-mini.bd-green  { background:rgba(52,195,143,.12); color:#1a8754; }
.bd-mini.bd-red    { background:rgba(244,106,106,.12); color:#c84646; }
.bd-mini.bd-warn   { background:rgba(241,180,76,.12); color:#b87a14; }
.bd-mini.bd-teal   { background:rgba(80,165,241,.12); color:#2b81c9; }
.bd-mini.bd-gold   { background:rgba(212,175,55,.12); color:#b89730; }
.bd-mini.bd-purple { background:rgba(124,105,239,.12); color:#5b49c7; }
.bd-mini.bd-gray   { background:rgba(108,117,125,.08); color:#6c757d; }

.scroll-tbl { max-height:280px; overflow-y:auto; }
.scroll-tbl::-webkit-scrollbar { width:3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background:var(--bs-surface-300); border-radius:3px; }

.link-btn { font-size:.62rem; padding:.18rem .45rem; border-radius:.3rem; border:1px solid var(--bs-surface-300); background:transparent; color:var(--bs-surface-500); cursor:pointer; text-decoration:none; transition:all .15s; }
.link-btn:hover { border-color:var(--bs-gold); color:var(--bs-gold); }

/* Disposition badges */
.qa-disp { display:inline-block; padding:.12rem .4rem; border-radius:1rem; font-size:.58rem; font-weight:700; letter-spacing:.3px; text-transform:uppercase; }
.qa-disp.d-excellent { background:rgba(52,195,143,.12); color:#1a8754; }
.qa-disp.d-good      { background:rgba(85,110,230,.12); color:#556ee6; }
.qa-disp.d-average   { background:rgba(241,180,76,.12); color:#b87a14; }
.qa-disp.d-poor      { background:rgba(244,106,106,.12); color:#c84646; }
.qa-disp.d-comp-fail { background:rgba(214,48,49,.12); color:#c84646; border:1px solid rgba(214,48,49,.2); }
.qa-disp.d-void-risk { background:rgba(124,105,239,.12); color:#5b49c7; border:1px solid rgba(124,105,239,.2); }

.qa-score { font-weight:700; font-size:.8rem; }
.qa-score.s-excellent { color:#1a8754; }
.qa-score.s-good      { color:#556ee6; }
.qa-score.s-average   { color:#b87a14; }
.qa-score.s-poor      { color:#c84646; }

.comp-dot { display:inline-block; width:8px; height:8px; border-radius:50%; }
.comp-dot.cd-pass { background:#34c38f; }
.comp-dot.cd-fail { background:#f46a6a; }

.qa-bar { height:4px; background:var(--bs-surface-200); border-radius:2px; overflow:hidden; width:50px; display:inline-block; vertical-align:middle; margin-left:4px; }
.qa-bar .fill { height:100%; border-radius:2px; transition:width .3s; }
.qa-bar .fill.f-green { background:#34c38f; } .qa-bar .fill.f-blue { background:#556ee6; }
.qa-bar .fill.f-warn { background:#f1b44c; } .qa-bar .fill.f-red { background:#f46a6a; }

/* Header */
.qa-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.65rem; flex-wrap:wrap; gap:.4rem; }
.qa-header h5 { margin:0; font-size:.95rem; font-weight:700; display:flex; align-items:center; gap:.4rem; }
.qa-header h5 i { opacity:.6; }
.qa-controls { display:flex; gap:.4rem; align-items:center; }
.qa-controls select { font-size:.7rem; padding:.25rem .5rem; border-radius:.35rem; border:1px solid var(--bs-surface-300); background:var(--bs-card-bg); color:inherit; cursor:pointer; }
.qa-controls .qa-btn { font-size:.68rem; padding:.25rem .6rem; border-radius:.35rem; border:none; cursor:pointer; font-weight:600; transition:all .15s; }
.qa-controls .qa-btn-gold { background:var(--bs-gold,#d4af37); color:#fff; }
.qa-controls .qa-btn-gold:hover { opacity:.85; }

/* Category bars */
.qa-cat-row { display:flex; align-items:center; gap:.5rem; margin-bottom:.45rem; }
.qa-cat-label { width:100px; font-size:.68rem; color:var(--bs-surface-500); text-align:right; flex-shrink:0; }
.qa-cat-bar { flex:1; height:18px; background:var(--bs-surface-100); border-radius:.25rem; overflow:hidden; position:relative; }
.qa-cat-bar .fill { height:100%; border-radius:.25rem; transition:width .4s; display:flex; align-items:center; justify-content:flex-end; padding-right:6px; font-size:.6rem; font-weight:700; color:#fff; min-width:24px; }
.qa-cat-val { width:40px; font-size:.72rem; font-weight:600; text-align:right; }

/* Compliance breakdown bars */
.qa-comp-bar-row { display:flex; align-items:center; gap:.4rem; margin-bottom:.3rem; font-size:.68rem; }
.qa-comp-bar-label { width:90px; text-align:right; color:var(--bs-surface-500); flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.qa-comp-bar-track { flex:1; height:12px; background:var(--bs-surface-100); border-radius:.2rem; overflow:hidden; }
.qa-comp-bar-fill { height:100%; background:#f46a6a; border-radius:.2rem; transition:width .3s; }
.qa-comp-bar-val { width:24px; text-align:right; font-weight:700; color:#c84646; font-size:.65rem; }

/* Agent header card */
.qa-agent-hdr { display:flex; align-items:center; gap:.75rem; padding:.75rem; margin-bottom:.65rem; flex-wrap:wrap; }
.qa-agent-avatar { width:44px; height:44px; border-radius:50%; background:var(--bs-gold,#d4af37); display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:700; color:#fff; flex-shrink:0; }
.qa-agent-info h6 { margin:0; font-size:.85rem; font-weight:700; }
.qa-agent-info p { margin:0; font-size:.7rem; color:var(--bs-surface-500); }
.qa-agent-kpis { display:flex; gap:1rem; margin-left:auto; flex-wrap:wrap; }
.qa-agent-kpi { text-align:center; }
.qa-agent-kpi .val { font-size:1.1rem; font-weight:700; line-height:1; }
.qa-agent-kpi .lbl { font-size:.55rem; text-transform:uppercase; font-weight:600; letter-spacing:.3px; color:var(--bs-surface-500); margin-top:.1rem; }

/* Checklist */
.qa-checklist-item { display:flex; align-items:center; gap:.4rem; padding:.25rem 0; font-size:.7rem; border-bottom:1px solid rgba(0,0,0,.02); }
.qa-checklist-item:last-child { border-bottom:none; }
.qa-check-icon { width:16px; text-align:center; font-size:.75rem; }
.qa-check-pass { color:#34c38f; } .qa-check-fail { color:#f46a6a; } .qa-check-na { color:var(--bs-surface-400); }

/* Coaching box */
.qa-coaching { background:var(--bs-surface-100); border-radius:.4rem; padding:.5rem .65rem; font-size:.72rem; line-height:1.5; margin-bottom:.5rem; }
.qa-coaching h6 { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--bs-gold,#d4af37); margin-bottom:.3rem; }
.qa-coaching ul { margin:0; padding-left:1rem; }
.qa-coaching li { margin-bottom:.15rem; }

/* Transcript */
.qa-transcript { background:var(--bs-surface-100); border-radius:.4rem; padding:.65rem; max-height:350px; overflow-y:auto; font-size:.72rem; line-height:1.6; }
.qa-transcript::-webkit-scrollbar { width:3px; }
.qa-transcript::-webkit-scrollbar-thumb { background:var(--bs-surface-300); border-radius:3px; }
.qa-transcript .t-line { margin-bottom:.3rem; }
.qa-transcript .t-agent { color:#556ee6; font-weight:600; }
.qa-transcript .t-customer { color:#1a8754; font-weight:600; }
.qa-transcript .t-unknown { color:var(--bs-surface-400); font-weight:600; }

/* Call Detail Overlay — WHITE background */
.qa-overlay { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:10000; display:none; align-items:center; justify-content:center; padding:1rem; }
.qa-overlay.show { display:flex; }
.qa-overlay-box {
    background:#ffffff; border:1px solid #e0e0e0; border-radius:.6rem; width:100%; max-width:1050px;
    max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 12px 48px rgba(0,0,0,.35);
    padding:1rem; color:#1a1a2e;
    scrollbar-width:thin; scrollbar-color:#ccc transparent;
}
.qa-overlay-box::-webkit-scrollbar { width:4px; }
.qa-overlay-box::-webkit-scrollbar-thumb { background:#ccc; border-radius:4px; }
.qa-overlay-box .ex-card { background:#f8f9fa; border-color:#e9ecef; }
.qa-overlay-box .sec-hdr { border-bottom-color:#e9ecef; }
.qa-overlay-box .sec-hdr h6 { color:#1a1a2e; }
.qa-overlay-box .qa-cat-label { color:#555; }
.qa-overlay-box .qa-cat-val { color:#1a1a2e; }
.qa-overlay-box .qa-coaching { background:#fffbf0; border-left:3px solid #d4af37; color:#333; }
.qa-overlay-box .qa-coaching h6 { color:#b89730; }
.qa-overlay-box .qa-transcript { background:#f4f5f7; color:#333; }
.qa-overlay-box .qa-detail-meta { color:#666; }
.qa-overlay-box .qa-checklist-item { color:#333; }
.qa-overlay-box .qa-empty { color:#888; }
.qa-overlay-box .ex-tbl thead th { background:#f0f0f0; color:#555; border-bottom-color:#ddd; }
.qa-overlay-box .ex-tbl tbody td { color:#333; border-bottom-color:#eee; }
.qa-overlay-box .qa-cat-bar { background:#e9ecef; }
.qa-overlay-close { position:absolute; top:8px; right:12px; background:none; border:none; font-size:1.3rem; cursor:pointer; color:#888; z-index:10; }
.qa-overlay-close:hover { color:#b89730; }

/* Detail meta badges */
.qa-detail-meta { display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; font-size:.68rem; color:var(--bs-surface-400); }
.qa-detail-meta .meta-item { display:flex; align-items:center; gap:.2rem; }
.qa-detail-meta .meta-item i { font-size:.8rem; opacity:.6; }

/* Pagination */
.qa-pagination { display:flex; justify-content:center; gap:.25rem; margin-top:.65rem; padding:0 .75rem .65rem; }
.qa-pagination button { font-size:.62rem; padding:.2rem .45rem; border-radius:.25rem; border:1px solid var(--bs-surface-300); background:transparent; color:var(--bs-surface-500); cursor:pointer; transition:all .15s; }
.qa-pagination button:hover { border-color:var(--bs-gold); color:var(--bs-gold); }
.qa-pagination button.active { background:var(--bs-gold,#d4af37); color:#fff; border-color:var(--bs-gold); }
.qa-pagination button:disabled { opacity:.4; cursor:not-allowed; }

/* Filter buttons */
.qa-filter-row { display:flex; gap:.3rem; flex-wrap:wrap; margin-bottom:.65rem; }
.qa-filter-btn { font-size:.62rem; font-weight:600; padding:.2rem .55rem; border-radius:1rem; border:1px solid var(--bs-surface-300); background:transparent; color:var(--bs-surface-500); cursor:pointer; transition:all .15s; }
.qa-filter-btn:hover { border-color:var(--bs-gold); color:var(--bs-gold); }
.qa-filter-btn.active { background:var(--bs-gold,#d4af37); border-color:var(--bs-gold); color:#fff; }

/* Loading / Empty */
.qa-loading { text-align:center; padding:2rem; color:var(--bs-surface-400); font-size:.78rem; }
.qa-loading .spin { display:inline-block; width:18px; height:18px; border:2px solid var(--bs-surface-300); border-top-color:var(--bs-gold,#d4af37); border-radius:50%; animation:qaSpin .7s linear infinite; margin-right:6px; vertical-align:middle; }
@keyframes qaSpin { to { transform:rotate(360deg); } }
.qa-empty { text-align:center; padding:1.5rem; color:var(--bs-surface-400); font-size:.72rem; }

/* Chart containers */
.qa-chart-wrap { position:relative; height:180px; }

/* Issue items */
.qa-issue-item { display:flex; justify-content:space-between; align-items:center; padding:.3rem 0; border-bottom:1px solid rgba(0,0,0,.03); font-size:.72rem; }
.qa-issue-item:last-child { border-bottom:none; }
.qa-issue-count { background:var(--bs-surface-100); padding:.1rem .4rem; border-radius:.8rem; font-size:.6rem; font-weight:700; color:var(--bs-surface-500); }

/* Info button & modal */
.qa-info-btn { background:none; border:1px solid rgba(212,175,55,.35); color:var(--bs-gold,#d4af37); border-radius:50%; width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center; font-size:1rem; cursor:pointer; margin-left:.4rem; vertical-align:middle; transition:all .2s; }
.qa-info-btn:hover { background:rgba(212,175,55,.12); border-color:var(--bs-gold); }
.qa-info-modal { display:none; position:fixed; inset:0; z-index:10001; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1rem; }
.qa-info-modal.show { display:flex; }
.qa-info-box { background:#ffffff; border:1px solid #e0e0e0; border-radius:.75rem; box-shadow:0 8px 32px rgba(0,0,0,.25); color:#1a1a2e; width:100%; max-width:680px; max-height:85vh; overflow-y:auto; padding:1.5rem; position:relative; scrollbar-width:thin; }
.qa-info-box::-webkit-scrollbar { width:4px; }
.qa-info-box::-webkit-scrollbar-thumb { background:#ccc; border-radius:4px; }
.qa-info-close { position:absolute; top:.75rem; right:.75rem; background:none; border:none; font-size:1.4rem; cursor:pointer; color:#888; line-height:1; z-index:1; }
.qa-info-close:hover { color:#b89730; }
.qa-info-section { margin-bottom:1.1rem; padding-bottom:1rem; border-bottom:1px solid #eee; }
.qa-info-section:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
.qa-info-section h6 { font-weight:700; font-size:.82rem; text-transform:uppercase; letter-spacing:.5px; color:#b89730; margin:0 0 .45rem; }
.qa-info-section h6 i { margin-right:.3rem; opacity:.7; }
.qa-info-section p { font-size:.78rem; color:#555; margin:0 0 .4rem; line-height:1.5; }
.qa-info-section ol,.qa-info-section ul { font-size:.78rem; color:#555; margin:0 0 .4rem; padding-left:1.2rem; line-height:1.6; }
.qa-info-tbl { width:100%; font-size:.75rem; border-collapse:collapse; }
.qa-info-tbl td { padding:.3rem .5rem; border-bottom:1px solid #eee; color:#444; vertical-align:top; }
.qa-info-tbl td:first-child { white-space:nowrap; width:130px; }
.qa-info-tbl tr:last-child td { border-bottom:none; }

@media (max-width:768px) {
    .qa-agent-kpis { margin-left:0; }
    .qa-cat-label { width:70px; }
}
</style>
@endsection

@section('content')

<!-- ═══ Header ═══ -->
<div class="qa-header">
    <h5><i class="ri-shield-star-line"></i> QA Scoring Dashboard
        <button class="qa-info-btn" onclick="document.getElementById('qaInfoModal').classList.add('show')" title="How QA Scoring Works">
            <i class="ri-question-line"></i>
        </button>
    </h5>
    <div class="qa-controls">
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <select id="qaRangePreset" onchange="QA.presetChanged()" style="font-size:.7rem; padding:.25rem .5rem; border-radius:.35rem; border:1px solid var(--bs-surface-300); background:var(--bs-card-bg); color:inherit; cursor:pointer;">
                <option value="">Custom Range</option>
                <option value="today">Today</option>
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
                <option value="all">All Time</option>
            </select>
            <input type="date" id="qaStartDate" onchange="QA.rangeChanged()" style="font-size:.7rem; padding:.25rem .5rem; border-radius:.35rem; border:1px solid var(--bs-surface-300); background:var(--bs-card-bg); color:inherit;">
            <span style="color:var(--bs-surface-500); font-size:.7rem;">to</span>
            <input type="date" id="qaEndDate" onchange="QA.rangeChanged()" style="font-size:.7rem; padding:.25rem .5rem; border-radius:.35rem; border:1px solid var(--bs-surface-300); background:var(--bs-card-bg); color:inherit;">
        </div>
        <button class="qa-btn qa-btn-gold" onclick="QA.refresh()"><i class="ri-refresh-line"></i> Refresh</button>
        <button class="qa-btn" id="rerunTodayBtn" onclick="QA.rerunToday()" style="background:rgba(220,38,38,.12);color:#c84646;border:1px solid rgba(220,38,38,.25);" title="Re-score today&apos;s calls with the latest AI prompt"><i class="ri-restart-line"></i> Rerun Today</button>
    </div>
</div>

<!-- ═══ Main Content ═══ -->
<div id="qa-content">
    <div class="qa-loading"><span class="spin"></span> Loading dashboard...</div>
</div>

<!-- ═══ Call Detail Overlay ═══ -->
<div class="qa-overlay" id="qaOverlay">
    <div class="qa-overlay-box" id="qaOverlayBox">
        <button class="qa-overlay-close" onclick="QA.closeDetail()">&times;</button>
        <div id="qaOverlayContent"></div>
    </div>
</div>

<!-- ═══ Info Modal ═══ -->
<div class="qa-info-modal" id="qaInfoModal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="qa-info-box">
        <button class="qa-info-close" onclick="document.getElementById('qaInfoModal').classList.remove('show')">&times;</button>

        <div class="qa-info-section">
            <h6><i class="ri-robot-2-line"></i> AI-Powered Quality Assurance</h6>
            <p>Every recorded sales call is automatically transcribed via Zoom's built-in transcription service and scored by AI against <strong>17 compliance codes</strong> and <strong>7 quality categories</strong>. Calls are graded on a 100-point scale with automatic disposition assignment.</p>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-timer-flash-line"></i> Call Eligibility</h6>
            <p><strong>Minimum duration: 7 minutes</strong> — Calls shorter than 7 minutes are automatically skipped as they do not represent meaningful sales conversations. Only calls with Zoom transcripts are scored.</p>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-shield-check-line"></i> 17 Compliance Codes</h6>
            <table class="qa-info-tbl">
                <tr><td colspan="2"><strong style="color:var(--bs-primary)">Call Handling</strong></td></tr>
                <tr><td><strong>C1</strong> Closer Consent</td><td>Closer takes proper verbal consent from the customer</td></tr>
                <tr><td><strong>C2</strong> Agent Identity</td><td>Closer states full name and company</td></tr>
                <tr><td><strong>C3</strong> Carrier Named</td><td>Insurance carrier name clearly stated</td></tr>
                <tr><td><strong>C4</strong> Product Type</td><td>Identifies product as final expense / whole life</td></tr>
                <tr><td><strong>C5</strong> Health Questions</td><td>Complete and accurate health questions + medications asked</td></tr>
                <tr><td><strong>C6</strong> Proper Quote</td><td>Quote provided according to customer's health conditions</td></tr>
                <tr><td><strong>C7</strong> Coverage Amount</td><td>Death benefit / face amount stated and confirmed</td></tr>
                <tr><td><strong>C8</strong> Draft Date</td><td>Payment draft date confirmed with customer</td></tr>
                <tr><td><strong>C9</strong> End-of-Call Consent</td><td>Confirms date, full name, DOB, and SSN at end of call</td></tr>
                <tr><td><strong>C10</strong> Waiting Period</td><td>Graded/modified benefit period disclosed (if applicable)</td></tr>
                <tr><td colspan="2"><strong style="color:var(--bs-primary)">Application Requirements</strong></td></tr>
                <tr><td><strong>C11</strong> Application Info</td><td>Collects name, DOB, payment info, address, doctor, beneficiary, etc.</td></tr>
                <tr><td colspan="2"><strong style="color:var(--bs-primary)">Behavioral Compliance</strong></td></tr>
                <tr><td><strong>C12</strong> DNC Honored</td><td>Do-Not-Call requests honored immediately</td></tr>
                <tr><td><strong>C13</strong> No Aggression</td><td>Customer not aggressive during the call</td></tr>
                <tr><td><strong>C14</strong> Customer Interest</td><td>Customer not disinterested or deferring decision</td></tr>
                <tr><td><strong>C15</strong> No Pushy Sale</td><td>Agent does not pressure or confuse the customer</td></tr>
                <tr><td><strong>C16</strong> Appropriate Language</td><td>No inappropriate or unprofessional language used</td></tr>
                <tr><td><strong>C17</strong> No Abuse</td><td>Customer not abusive toward agent</td></tr>
            </table>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-bar-chart-grouped-line"></i> 7 Quality Categories (100pts)</h6>
            <table class="qa-info-tbl">
                <tr><td><strong>Opening</strong></td><td>Professional greeting, tone, rapport building (1-10)</td></tr>
                <tr><td><strong>Discovery</strong></td><td>Needs assessment, health/family questions, listening (1-10)</td></tr>
                <tr><td><strong>Presentation</strong></td><td>Product explanation, personalization, proper quote (1-10)</td></tr>
                <tr><td><strong>Objection Handling</strong></td><td>Pushback handling, reframing without pressure (1-10)</td></tr>
                <tr><td><strong>Closing</strong></td><td>Asking for sale, consent, application completion (1-10)</td></tr>
                <tr><td><strong>Soft Skills</strong></td><td>Empathy, patience, respect, sensitivity with seniors (1-10)</td></tr>
                <tr><td><strong>Call Control</strong></td><td>Conversation flow, redirecting tangents, pacing (1-10)</td></tr>
            </table>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-award-line"></i> Disposition Scale</h6>
            <table class="qa-info-tbl">
                <tr><td><span class="qa-disp d-excellent">EXCELLENT</span></td><td>90-100 — Exceptional call, exceeds all standards</td></tr>
                <tr><td><span class="qa-disp d-good">GOOD</span></td><td>75-89 — Solid performance, minor improvements possible</td></tr>
                <tr><td><span class="qa-disp d-average">AVERAGE</span></td><td>60-74 — Meets basic standards, needs coaching</td></tr>
                <tr><td><span class="qa-disp d-poor">POOR</span></td><td>&lt;60 — Below standard, immediate coaching required</td></tr>
                <tr><td><span class="qa-disp d-comp-fail">COMPLIANCE FAIL</span></td><td>Any C1-C17 compliance check failed</td></tr>
                <tr><td><span class="qa-disp d-void-risk">VOID RISK</span></td><td>Misrepresentation, confusion, or pressured sale</td></tr>
            </table>
        </div>

        <div class="qa-info-section">
            <h6><i class="ri-lightbulb-line"></i> How It Works</h6>
            <ol>
                <li><strong>Recording captured</strong> — Zoom webhook triggers automatic capture</li>
                <li><strong>Transcription fetched</strong> — Zoom's built-in transcript with speaker labels (AGENT:/CUSTOMER:)</li>
                <li><strong>AI analysis</strong> — Claude / Gemini scores against 17 compliance codes + 7 quality categories</li>
                <li><strong>Results saved</strong> — Scores, compliance flags, and coaching notes stored</li>
                <li><strong>Dashboard updated</strong> — Real-time metrics and agent performance tracking</li>
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
    load()         { S.currentView === 'dashboard' ? loadDashboard() : loadAgentDetail(S.agentId); },
    viewAgent(id)  { S.agentId = id; S.currentView = 'agent-detail'; loadAgentDetail(id); },
    backToDash()   { S.currentView = 'dashboard'; S.currentPage = 1; loadDashboard(); },
    filterCalls(f) { S.currentFilter = f; S.currentPage = 1; loadDashboard(); },
    goPage(p)      { S.currentPage = p; loadDashboard(); },
    openDetail(id) { openCallDetail(id); },
    closeDetail()  { $('#qaOverlay').classList.remove('show'); document.body.style.overflow=''; },
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
    }
};


/* ══════════════════════════════════════════════════
   MAIN DASHBOARD
   ══════════════════════════════════════════════════ */
function loadDashboard() {
    const el = $('#qa-content');
    el.innerHTML = '<div class="qa-loading"><span class="spin"></span> Loading dashboard...</div>';

    const url = `${API_BASE}/overview?range=${S.currentRange}&page=${S.currentPage}&qa_filter=${S.currentFilter}`;
    api(url).then(d => {
        S.data = d;
        // team_stats: {calls_scored, avg_score, compliance_rate, compliance_fails, void_risks, excellent_count, good_count, average_count, poor_count}
        const ts = d.team_stats || {};
        // extended_kpis: {avg_handle_time, agents_scored, passing_rate, ...}
        const ex = d.extended_kpis || {};
        // sales_summary: {total_sales, total_coverage, total_premium, avg_coverage, avg_premium}
        const ss = d.sales_summary || {};

        el.innerHTML = `
            <!-- KPI Row 1: Performance -->
            <div class="kpi-row">
                <div class="kpi-card k-blue">
                    <span class="k-icon"><i class="ri-phone-line"></i></span>
                    <div class="k-val">${ts.calls_scored||0}</div>
                    <div class="k-lbl">Calls Scored</div>
                </div>
                <div class="kpi-card k-gold">
                    <span class="k-icon"><i class="ri-bar-chart-2-line"></i></span>
                    <div class="k-val">${ts.avg_score != null ? parseFloat(ts.avg_score).toFixed(1) : '—'}</div>
                    <div class="k-lbl">Avg Score</div>
                </div>
                <div class="kpi-card k-green">
                    <span class="k-icon"><i class="ri-shield-check-line"></i></span>
                    <div class="k-val">${ts.compliance_rate != null ? parseFloat(ts.compliance_rate).toFixed(0) : '—'}%</div>
                    <div class="k-lbl">Compliance Rate</div>
                </div>
                <div class="kpi-card k-purple">
                    <span class="k-icon"><i class="ri-money-dollar-circle-line"></i></span>
                    <div class="k-val">${ss.total_sales||0}</div>
                    <div class="k-lbl">Sales Made</div>
                </div>
                <div class="kpi-card k-teal">
                    <span class="k-icon"><i class="ri-hand-coin-line"></i></span>
                    <div class="k-val">$${formatNum(ss.total_premium||0)}</div>
                    <div class="k-lbl">Total Premium</div>
                </div>
                <div class="kpi-card k-warn">
                    <span class="k-icon"><i class="ri-shield-line"></i></span>
                    <div class="k-val">$${formatNum(ss.total_coverage||0)}</div>
                    <div class="k-lbl">Total Coverage</div>
                </div>
            </div>

            <!-- KPI Row 2: Operational -->
            <div class="kpi-row">
                <div class="kpi-card k-blue">
                    <span class="k-icon"><i class="ri-timer-line"></i></span>
                    <div class="k-val">${ex.avg_handle_time ? fmtDuration(ex.avg_handle_time) : '—'}</div>
                    <div class="k-lbl">Avg Handle Time</div>
                </div>
                <div class="kpi-card k-green">
                    <span class="k-icon"><i class="ri-trophy-line"></i></span>
                    <div class="k-val">${ts.excellent_count||0}</div>
                    <div class="k-lbl">Excellent</div>
                </div>
                <div class="kpi-card k-teal">
                    <span class="k-icon"><i class="ri-thumb-up-line"></i></span>
                    <div class="k-val">${ts.good_count||0}</div>
                    <div class="k-lbl">Good</div>
                </div>
                <div class="kpi-card k-warn">
                    <span class="k-icon"><i class="ri-error-warning-line"></i></span>
                    <div class="k-val">${ts.poor_count||0}</div>
                    <div class="k-lbl">Poor</div>
                </div>
                <div class="kpi-card k-red">
                    <span class="k-icon"><i class="ri-alarm-warning-line"></i></span>
                    <div class="k-val">${ts.compliance_fails||0}</div>
                    <div class="k-lbl">Comp Fails</div>
                </div>
                <div class="kpi-card k-purple">
                    <span class="k-icon"><i class="ri-alert-line"></i></span>
                    <div class="k-val">${ts.void_risks||0}</div>
                    <div class="k-lbl">Void Risks</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-line-chart-line"></i> Score Trend</h6></div>
                        <div class="sec-body"><div class="qa-chart-wrap"><canvas id="trendChart"></canvas></div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-pie-chart-2-line"></i> Dispositions</h6></div>
                        <div class="sec-body"><div class="qa-chart-wrap"><canvas id="dispChart"></canvas></div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-bar-chart-grouped-line"></i> Score Distribution</h6></div>
                        <div class="sec-body"><div class="qa-chart-wrap"><canvas id="distChart"></canvas></div></div>
                    </div>
                </div>
            </div>

            <!-- Category Averages + Compliance Breakdown -->
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-equalizer-line"></i> Category Averages</h6></div>
                        <div class="sec-body" id="catBars"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-shield-cross-line"></i> Compliance Failure Breakdown</h6></div>
                        <div class="sec-body" id="compBreak"></div>
                    </div>
                </div>
            </div>

            <!-- Closer Performance + Agent Leaderboard -->
            <div class="row g-2 mb-2">
                <div class="col-md-7">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-team-line"></i> Closer Performance</h6></div>
                        <div class="sec-body" style="padding:0;"><div class="scroll-tbl" id="closerTbl"></div></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-medal-line"></i> Agent Leaderboard</h6></div>
                        <div class="sec-body" style="padding:0;"><div class="scroll-tbl" id="lbTbl"></div></div>
                    </div>
                </div>
            </div>

            <!-- Filters + Calls List -->
            <div class="ex-card sec-card">
                <div class="sec-hdr"><h6><i class="ri-phone-find-line"></i> Scored Calls</h6></div>
                <div class="sec-body" style="padding-bottom:0;"><div class="qa-filter-row" id="filterRow"></div></div>
                <div style="padding:0 .75rem;"><div class="scroll-tbl" style="max-height:400px;" id="callsTbl"></div></div>
                <div id="callsPag"></div>
            </div>

            <div id="procStatus" style="margin-top:.5rem;"></div>
        `;

        renderCharts(d);
        renderCategoryBars(d.team_category_averages, 'catBars');
        renderComplianceBreakdown(d.compliance_breakdown);
        renderCloserTable(d.closer_breakdown);
        renderLeaderboard(d.agent_leaderboard);
        renderFilterRow();
        renderCallsTable(d.calls);
        renderPagination(d.calls_pagination);
        renderProcStatus(d);

    }).catch(e => {
        el.innerHTML = `<div class="qa-empty"><i class="ri-error-warning-line"></i> Failed to load — ${esc(e.message)}</div>`;
    });
}


/* ── Charts ── */
function renderCharts(d) {
    Object.values(S.charts).forEach(c => c.destroy());
    S.charts = {};

    // score_trend is {date: avg_score} object
    const trendObj = d.score_trend || {};
    const trendDates = Object.keys(trendObj);
    if (trendDates.length && document.getElementById('trendChart')) {
        S.charts.trend = new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendDates,
                datasets: [{
                    label: 'Avg Score',
                    data: trendDates.map(k => trendObj[k]),
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212,175,55,.08)',
                    fill: true, tension: .3, pointRadius: 2, borderWidth: 2
                }]
            },
            options: chartOpts(0, 100)
        });
    }

    // disposition_chart is {EXCELLENT: count, GOOD: count} object
    const dispObj = d.disposition_chart || {};
    const dispKeys = Object.keys(dispObj);
    if (dispKeys.length && document.getElementById('dispChart')) {
        const colors = { EXCELLENT:'#34c38f', GOOD:'#556ee6', AVERAGE:'#f1b44c', POOR:'#f46a6a', COMPLIANCE_FAIL:'#d63031', VOID_RISK:'#7c69ef' };
        S.charts.disp = new Chart(document.getElementById('dispChart'), {
            type: 'doughnut',
            data: {
                labels: dispKeys.map(k => dispLabel(k)),
                datasets: [{ data: dispKeys.map(k => dispObj[k]), backgroundColor: dispKeys.map(k => colors[k]||'#6c757d'), borderWidth: 0 }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'right', labels:{ boxWidth:10, font:{size:10}, color:'#999' } } } }
        });
    }

    // score_distribution is {0-39: count, 40-59: count, ...} object
    const distObj = d.score_distribution || {};
    const distKeys = Object.keys(distObj);
    if (distKeys.length && document.getElementById('distChart')) {
        const distColors = distKeys.map(r => r.includes('90')?'#34c38f':r.includes('80')?'#556ee6':r.includes('60')?'#f1b44c':r.includes('40')?'#f46a6a':'#d63031');
        S.charts.dist = new Chart(document.getElementById('distChart'), {
            type: 'bar',
            data: {
                labels: distKeys,
                datasets: [{ data: distKeys.map(k => distObj[k]), backgroundColor: distColors, borderRadius: 3 }]
            },
            options: { ...chartOpts(), plugins:{ legend:{ display:false } } }
        });
    }
}

function chartOpts(min, max) {
    return {
        responsive:true, maintainAspectRatio:false,
        scales:{ x:{ grid:{display:false}, ticks:{font:{size:9}, color:'#999', maxRotation:0} }, y:{ min:min, max:max, grid:{color:'rgba(255,255,255,.04)'}, ticks:{font:{size:9}, color:'#999'} } },
        plugins:{ legend:{ display:false } }
    };
}


/* ── Category Bars ──
   Data is {opening: 7.5, discovery: 8.0, ...} object — raw scores out of max.
   Max scores: opening=10, discovery=10, presentation=10, objection_handling=10, closing=10, soft_skills=10, call_control=10
*/
const catMaxScores = { opening:10, discovery:10, presentation:10, objection_handling:10, closing:10, soft_skills:10, call_control:10 };
const catLabels = { opening:'Opening', discovery:'Discovery', presentation:'Presentation', objection_handling:'Objection Handling', closing:'Closing', soft_skills:'Soft Skills', call_control:'Call Control' };

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
        const cls = pct >= 80 ? 'f-green' : pct >= 65 ? 'f-blue' : pct >= 50 ? 'f-warn' : 'f-red';
        return `<div class="qa-cat-row">
            <div class="qa-cat-label">${esc(catLabels[k]||k)}</div>
            <div class="qa-cat-bar"><div class="fill ${cls}" style="width:${pct.toFixed(0)}%"></div></div>
            <div class="qa-cat-val">${val.toFixed(1)}/${maxVal}</div>
        </div>`;
    }).join('');
}


/* ── Compliance Breakdown ──
   Data is [{check_code, check_label, count}]
*/
function renderComplianceBreakdown(items) {
    const el = document.getElementById('compBreak');
    if (!el) return;
    if (!items || !items.length) { el.innerHTML = '<div class="qa-empty">No compliance failures</div>'; return; }
    const maxFails = Math.max(...items.map(x => x.count || 0), 1);
    el.innerHTML = items.map(c => {
        const pct = ((c.count || 0) / maxFails * 100).toFixed(0);
        return `<div class="qa-comp-bar-row">
            <div class="qa-comp-bar-label" title="${esc(c.check_label||c.check_code)}">${esc(c.check_code)}</div>
            <div class="qa-comp-bar-track"><div class="qa-comp-bar-fill" style="width:${pct}%"></div></div>
            <div class="qa-comp-bar-val">${c.count||0}</div>
        </div>`;
    }).join('');
}


/* ── Closer Performance Table ──
   Data is [{closer_name, total_calls, avg_score, total_sales, sale_rate, total_coverage, total_premium, void_risks}]
*/
function renderCloserTable(closers) {
    const el = document.getElementById('closerTbl');
    if (!el) return;
    if (!closers || !closers.length) { el.innerHTML = '<div class="qa-empty">No closer data</div>'; return; }
    el.innerHTML = `<table class="ex-tbl"><thead><tr>
        <th>Closer</th><th>Calls</th><th>Avg Score</th><th>Sales</th><th>Close Rate</th>
        <th>Coverage</th><th>Premium</th><th>Void Risk</th>
    </tr></thead><tbody>${closers.map(c => `<tr>
        <td><strong>${esc(c.closer_name)}</strong></td>
        <td>${c.total_calls}</td>
        <td><span class="qa-score ${scoreClass(c.avg_score)}">${parseFloat(c.avg_score).toFixed(1)}</span></td>
        <td><span class="bd-mini bd-green">${c.total_sales||0}</span></td>
        <td>${c.sale_rate||0}%</td>
        <td>$${formatNum(c.total_coverage||0)}</td>
        <td>$${formatNum(c.total_premium||0)}/mo</td>
        <td>${c.void_risks ? '<span class="bd-mini bd-red">'+c.void_risks+'</span>' : '<span class="bd-mini bd-gray">0</span>'}</td>
    </tr>`).join('')}</tbody></table>`;
}


/* ── Leaderboard ──
   Data is [{agent_user_id, agent_name, calls_scored, avg_score, sales_count}]
*/
function renderLeaderboard(agents) {
    const el = document.getElementById('lbTbl');
    if (!el) return;
    if (!agents || !agents.length) { el.innerHTML = '<div class="qa-empty">No agent data</div>'; return; }
    el.innerHTML = `<table class="ex-tbl"><thead><tr>
        <th>#</th><th>Agent</th><th>Calls</th><th>Avg</th><th>Sales</th>
    </tr></thead><tbody>${agents.map((a, i) => `<tr style="cursor:pointer" onclick="QA.viewAgent(${a.agent_user_id})">
        <td>${i === 0 ? '<i class="ri-vip-crown-line" style="color:#d4af37"></i>' : i+1}</td>
        <td>${esc(a.agent_name)}</td>
        <td>${a.calls_scored}</td>
        <td><span class="qa-score ${scoreClass(a.avg_score)}">${parseFloat(a.avg_score).toFixed(1)}</span></td>
        <td><span class="bd-mini bd-green">${a.sales_count||0}</span></td>
    </tr>`).join('')}</tbody></table>`;
}


/* ── Filter Row ── */
function renderFilterRow() {
    const el = document.getElementById('filterRow');
    if (!el) return;
    const filters = [
        { key:'all', label:'All Calls' },
        { key:'sales_only', label:'Sales Only' },
        { key:'excellent', label:'Excellent' },
        { key:'good', label:'Good' },
        { key:'average', label:'Average' },
        { key:'poor', label:'Poor' },
        { key:'void_risk', label:'Void Risk' },
        { key:'compliance_fail', label:'Comp Fail' }
    ];
    el.innerHTML = filters.map(f =>
        `<button class="qa-filter-btn ${S.currentFilter===f.key?'active':''}" onclick="QA.filterCalls('${f.key}')">${f.label}</button>`
    ).join('');
}


/* ── Calls Table ──
   Data from formatCallSummary: [{id, agent_name, caller_number, customer_name, callee_number,
     duration_seconds, call_start_time, disposition, total_score, is_sale, sale_amount, monthly_premium, carrier_name}]
*/
function renderCallsTable(calls) {
    const el = document.getElementById('callsTbl');
    if (!el) return;
    if (!calls || !calls.length) { el.innerHTML = '<div class="qa-empty">No calls found</div>'; return; }
    el.innerHTML = `<table class="ex-tbl"><thead><tr>
        <th>Customer</th><th>Phone</th><th>Closer</th><th>Date</th><th>Duration</th>
        <th>Score</th><th>Disposition</th><th>Sale</th><th>Carrier</th><th>Coverage</th><th>Premium</th>
    </tr></thead><tbody>${calls.map(c => `<tr style="cursor:pointer" onclick="QA.openDetail(${c.id})">
        <td><strong>${esc(c.customer_name||'Unknown')}</strong></td>
        <td>${fmtPhone(c.callee_number)}</td>
        <td>${esc(c.agent_name||'Unknown')}</td>
        <td>${fmtTime(c.call_start_time)}</td>
        <td>${fmtDuration(c.duration_seconds)}</td>
        <td><span class="qa-score ${scoreClass(c.total_score)}">${c.total_score != null ? parseFloat(c.total_score).toFixed(0) : '—'}</span></td>
        <td><span class="qa-disp ${dispClass(c.disposition)}">${dispLabel(c.disposition)}</span></td>
        <td>${c.is_sale ? '<span class="bd-mini bd-green">YES</span>' : '<span class="bd-mini bd-gray">NO</span>'}</td>
        <td>${c.carrier_name ? esc(c.carrier_name) : '—'}</td>
        <td>${c.sale_amount ? '$'+formatNum(c.sale_amount) : '—'}</td>
        <td>${c.monthly_premium ? '$'+formatNum(c.monthly_premium)+'/mo' : '—'}</td>
    </tr>`).join('')}</tbody></table>`;
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
    if (d.processing_now) parts.push(`<span class="bd-mini bd-blue"><i class="ri-loader-4-line"></i> ${d.processing_now} processing</span>`);
    if (d.pending_count) parts.push(`<span class="bd-mini bd-warn">${d.pending_count} pending</span>`);
    if (d.failed_count) parts.push(`<span class="bd-mini bd-red">${d.failed_count} failed</span>`);
    el.innerHTML = parts.length ? `<div style="display:flex;gap:.4rem;justify-content:flex-end;font-size:.65rem;">${parts.join('')}</div>` : '';
}


/* ══════════════════════════════════════════════════
   AGENT DETAIL VIEW
   Response: {agent: {id,name,email}, summary: {calls_scored, avg_score, excellent_count, compliance_fails, ...},
              category_averages: {opening:7,...}, score_trend: [{date,avg_score,calls_scored}], calls: [...], ...}
   ══════════════════════════════════════════════════ */
function loadAgentDetail(agentId) {
    const el = $('#qa-content');
    el.innerHTML = '<div class="qa-loading"><span class="spin"></span> Loading agent detail...</div>';

    api(`${API_BASE}/agents/${agentId}?range=${S.currentRange}`).then(d => {
        const a = d.agent || {};
        const sm = d.summary || {};
        const calls = d.calls || [];

        el.innerHTML = `
            <button class="link-btn" onclick="QA.backToDash()" style="margin-bottom:.5rem;">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </button>

            <div class="ex-card qa-agent-hdr">
                <div class="qa-agent-avatar">${esc((a.name||'?')[0])}</div>
                <div class="qa-agent-info">
                    <h6>${esc(a.name||'Unknown Agent')}</h6>
                    <p>${esc(a.email||'')}</p>
                </div>
                <div class="qa-agent-kpis">
                    <div class="qa-agent-kpi"><div class="val" style="color:#556ee6">${sm.calls_scored||0}</div><div class="lbl">Calls</div></div>
                    <div class="qa-agent-kpi"><div class="val" style="color:#d4af37">${sm.avg_score != null ? parseFloat(sm.avg_score).toFixed(1) : '—'}</div><div class="lbl">Avg Score</div></div>
                    <div class="qa-agent-kpi"><div class="val" style="color:#34c38f">${sm.excellent_count||0}</div><div class="lbl">Excellent</div></div>
                    <div class="qa-agent-kpi"><div class="val" style="color:#f46a6a">${sm.compliance_fails||0}</div><div class="lbl">Comp Fail</div></div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-equalizer-line"></i> Category Scores</h6></div>
                        <div class="sec-body" id="agentCatBars"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-line-chart-line"></i> Score Trend</h6></div>
                        <div class="sec-body"><div class="qa-chart-wrap"><canvas id="agentTrendChart"></canvas></div></div>
                    </div>
                </div>
            </div>

            <div class="ex-card sec-card">
                <div class="sec-hdr"><h6><i class="ri-phone-line"></i> Calls (${calls.length})</h6></div>
                <div style="padding:0 .75rem;">
                    <div class="scroll-tbl" style="max-height:350px;" id="agentCallsTbl"></div>
                </div>
            </div>
        `;

        // category_averages is {opening: 7.5, ...}
        renderCategoryBars(d.category_averages, 'agentCatBars');

        // score_trend is [{date, avg_score, calls_scored}] array
        Object.values(S.charts).forEach(c => c.destroy());
        S.charts = {};
        const trend = d.score_trend || [];
        if (trend.length && document.getElementById('agentTrendChart')) {
            S.charts.agentTrend = new Chart(document.getElementById('agentTrendChart'), {
                type: 'line',
                data: {
                    labels: trend.map(t => t.date),
                    datasets: [{ label:'Score', data:trend.map(t=>t.avg_score), borderColor:'#d4af37', backgroundColor:'rgba(212,175,55,.08)', fill:true, tension:.3, pointRadius:2, borderWidth:2 }]
                },
                options: chartOpts(0, 100)
            });
        }

        // Agent calls table
        const act = document.getElementById('agentCallsTbl');
        if (act) {
            if (!calls.length) { act.innerHTML = '<div class="qa-empty">No calls</div>'; }
            else {
                act.innerHTML = `<table class="ex-tbl"><thead><tr>
                    <th>Customer</th><th>Date</th><th>Duration</th><th>Score</th><th>Disposition</th><th>Sale</th>
                </tr></thead><tbody>${calls.map(c => `<tr style="cursor:pointer" onclick="QA.openDetail(${c.id})">
                    <td>${esc(c.customer_name||'Unknown')}</td>
                    <td>${fmtTime(c.call_start_time)}</td>
                    <td>${fmtDuration(c.duration_seconds)}</td>
                    <td><span class="qa-score ${scoreClass(c.total_score)}">${c.total_score != null ? parseFloat(c.total_score).toFixed(0) : '—'}</span></td>
                    <td><span class="qa-disp ${dispClass(c.disposition)}">${dispLabel(c.disposition)}</span></td>
                    <td>${c.is_sale ? '<span class="bd-mini bd-green">YES</span>' : '<span class="bd-mini bd-gray">NO</span>'}</td>
                </tr>`).join('')}</tbody></table>`;
            }
        }
    }).catch(e => { el.innerHTML = `<div class="qa-empty">Error: ${esc(e.message)}</div>`; });
}


/* ══════════════════════════════════════════════════
   CALL DETAIL OVERLAY
   Response: {call: {id, agent_name, caller_number, customer_name, duration_seconds, call_start_time, is_sale, carrier_name, ...},
              qa_result: {disposition, total_score, compliance_checks: {C1_recording_disclosure: bool,...},
                          score_breakdown: {opening: int,...}, coaching_notes: string, compliance_failures: [string,...], ...},
              compliance_flags: [{check_code, check_label}], transcript: [{speaker, text}]}
   ══════════════════════════════════════════════════ */
function openCallDetail(callId) {
    const overlay = $('#qaOverlay');
    const content = $('#qaOverlayContent');
    content.innerHTML = '<div class="qa-loading"><span class="spin"></span> Loading call detail...</div>';
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';

    api(`${API_BASE}/calls/${callId}`).then(d => {
        const c = d.call || {};
        const r = d.qa_result || {};
        const scoreBreak = r.score_breakdown || {};
        const compChecks = r.compliance_checks || {};
        const flags = d.compliance_flags || [];
        const coaching = r.coaching_notes;
        const transcript = d.transcript || [];

        content.innerHTML = `
            <div class="qa-detail-meta" style="margin-bottom:.65rem;">
                <div class="meta-item"><i class="ri-user-line"></i> ${esc(c.customer_name||'Unknown')}</div>
                <div class="meta-item"><i class="ri-phone-line"></i> ${fmtPhone(c.caller_number)}</div>
                <div class="meta-item"><i class="ri-headphone-line"></i> ${esc(c.agent_name||'Unknown')}</div>
                <div class="meta-item"><i class="ri-calendar-line"></i> ${fmtTime(c.call_start_time)}</div>
                <div class="meta-item"><i class="ri-time-line"></i> ${fmtDuration(c.duration_seconds)}</div>
                ${c.is_sale ? '<div class="meta-item"><i class="ri-money-dollar-circle-line"></i> <span class="bd-mini bd-green">SALE</span></div>' : ''}
                ${c.carrier_name ? '<div class="meta-item"><i class="ri-building-line"></i> '+esc(c.carrier_name)+'</div>' : ''}
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-3 text-center">
                    <div class="ex-card" style="padding:1rem;">
                        <div class="qa-score ${scoreClass(r.total_score)}" style="font-size:2rem;">${r.total_score != null ? parseFloat(r.total_score).toFixed(0) : '—'}</div>
                        <div><span class="qa-disp ${dispClass(r.disposition)}">${dispLabel(r.disposition)}</span></div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-equalizer-line"></i> Category Breakdown</h6></div>
                        <div class="sec-body" id="detailCats"></div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-shield-check-line"></i> Compliance Checklist</h6></div>
                        <div class="sec-body" id="detailChecklist"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ex-card sec-card">
                        <div class="sec-hdr"><h6><i class="ri-lightbulb-line"></i> AI Coaching Notes</h6></div>
                        <div class="sec-body" id="detailCoaching"></div>
                    </div>
                </div>
            </div>

            <div class="ex-card sec-card">
                <div class="sec-hdr"><h6><i class="ri-chat-1-line"></i> Transcript</h6></div>
                <div class="sec-body" id="detailTranscript"></div>
            </div>
        `;

        // Category breakdown — score_breakdown is {opening: int, discovery: int, ...}
        renderCategoryBars(scoreBreak, 'detailCats');

        // Compliance checklist — compliance_checks is {C1_recording_disclosure: bool, C2_agent_identity: bool, ...}
        const cl = document.getElementById('detailChecklist');
        if (cl) {
            const compLabels = {
                C1_recording_disclosure: 'C1 Recording Consent',
                C2_agent_identity: 'C2 Agent Identity',
                C3_carrier_named: 'C3 Carrier Named',
                C4_not_government_program: 'C4 Not Government',
                C5_product_type_stated: 'C5 Product Type',
                C6_waiting_period: 'C6 Waiting Period',
                C7_premium_amount: 'C7 Premium Amount',
                C8_coverage_amount: 'C8 Coverage Amount',
                C9_health_questions: 'C9 Health Questions',
                C10_beneficiary_collected: 'C10 Beneficiary',
                C11_prospect_verbal_consent: 'C11 Verbal Consent',
                C12_dnc_honored: 'C12 DNC Honored'
            };
            const compKeys = Object.keys(compChecks);
            if (!compKeys.length) { cl.innerHTML = '<div class="qa-empty">No compliance data</div>'; }
            else {
                cl.innerHTML = compKeys.map(k => {
                    const passed = compChecks[k];
                    const icon = passed === true ? 'ri-checkbox-circle-fill qa-check-pass'
                               : passed === null ? 'ri-indeterminate-circle-line qa-check-na'
                               : 'ri-close-circle-fill qa-check-fail';
                    return `<div class="qa-checklist-item">
                        <div class="qa-check-icon"><i class="${icon}"></i></div>
                        <div style="flex:1">${esc(compLabels[k]||k)}</div>
                    </div>`;
                }).join('');
            }
        }

        // Coaching notes — coaching_notes is a string (or possibly null)
        const co = document.getElementById('detailCoaching');
        if (co) {
            if (!coaching) { co.innerHTML = '<div class="qa-empty">No coaching notes</div>'; }
            else if (Array.isArray(coaching)) {
                co.innerHTML = `<div class="qa-coaching"><ul>${coaching.map(n => '<li>'+esc(n)+'</li>').join('')}</ul></div>`;
            } else {
                // It's a string — split by newlines or bullet points
                const lines = String(coaching).split(/\n|(?:^|\n)\s*[-•]\s*/g).filter(l => l.trim());
                co.innerHTML = `<div class="qa-coaching"><ul>${lines.map(n => '<li>'+esc(n.trim())+'</li>').join('')}</ul></div>`;
            }
        }

        // Transcript — is [{speaker, text}] array
        const tr = document.getElementById('detailTranscript');
        if (tr) {
            if (!transcript.length) { tr.innerHTML = '<div class="qa-empty">No transcript available</div>'; }
            else {
                tr.innerHTML = `<div class="qa-transcript">${transcript.map(line => {
                    const cls = line.speaker === 'AGENT' ? 't-agent' : line.speaker === 'CUSTOMER' ? 't-customer' : 't-unknown';
                    const label = line.speaker === 'AGENT' ? 'Agent' : line.speaker === 'CUSTOMER' ? 'Customer' : 'Unknown';
                    return `<div class="t-line"><span class="${cls}">${label}:</span> ${esc(line.text)}</div>`;
                }).join('')}</div>`;
            }
        }
    }).catch(e => {
        content.innerHTML = `<div class="qa-empty">Error loading call: ${esc(e.message)}</div>`;
    });
}


/* ══════════════════════════════════════════════════
   UTILITIES
   ══════════════════════════════════════════════════ */
function scoreClass(s) { s=parseFloat(s); if(isNaN(s)) return ''; return s>=90?'s-excellent':s>=75?'s-good':s>=60?'s-average':'s-poor'; }
function dispClass(d) { return 'd-'+(d||'').toLowerCase().replace(/_/g,'-'); }
function dispLabel(d) { return (d||'N/A').replace(/_/g,' '); }
function fmtPhone(p) { if(!p) return '—'; p=String(p).replace(/\D/g,''); return p.length===10?`(${p.slice(0,3)}) ${p.slice(3,6)}-${p.slice(6)}`:p.length===11?`+${p[0]} (${p.slice(1,4)}) ${p.slice(4,7)}-${p.slice(7)}`:esc(String(p)); }
function fmtTime(t) { if(!t) return '—'; const d=new Date(t); const opts={timeZone:'America/Denver'}; return d.toLocaleDateString('en-US',{...opts,month:'short',day:'numeric'})+' '+d.toLocaleTimeString('en-US',{...opts,hour:'numeric',minute:'2-digit'})+' MT'; }
function fmtDuration(s) { if(!s&&s!==0) return '—'; s=parseInt(s); const m=Math.floor(s/60); const sec=s%60; return m>0?m+'m '+sec+'s':sec+'s'; }
function formatNum(n) { if(!n&&n!==0) return '0'; n=parseFloat(n); return n>=1000?(n/1000).toFixed(1)+'k':n.toFixed(0); }
function esc(s) { if(!s) return ''; const d=document.createElement('div'); d.textContent=String(s); return d.innerHTML; }

/* ── Init ── */
loadDashboard();
S.refreshTimer = setInterval(() => { if (S.currentView === 'dashboard') loadDashboard(); }, 60000);

})();
</script>
@endsection
