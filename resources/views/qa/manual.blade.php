@extends('layouts.master')

@section('title', 'QA Manual Score')

@section('css')
<style>
/* ── Design tokens (match QA dashboard) ── */
:root {
  --qa-gold:#d4af37; --qa-gold-dim:rgba(212,175,55,.1);
  --qa-green:#34c38f; --qa-green-dim:rgba(52,195,143,.1);
  --qa-blue:#556ee6;  --qa-blue-dim:rgba(85,110,230,.1);
  --qa-red:#f46a6a;   --qa-red-dim:rgba(244,106,106,.1);
  --qa-warn:#f1b44c;
  --qa-muted:var(--bs-surface-400,#8a94a6);
  --qa-surface:var(--bs-surface-100,rgba(255,255,255,.03));
  --qa-border:1px solid rgba(255,255,255,.07);
  --qa-radius:.5rem; --qa-radius-lg:.75rem;
  --qa-shadow:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
}
.qm-wrap { max-width:1100px; margin:0 auto; padding:1rem; }

/* Header */
.qm-header {
  display:flex; align-items:center; justify-content:space-between;
  gap:.75rem; flex-wrap:wrap; margin-bottom:1.1rem;
}
.qm-title { display:flex; align-items:center; gap:.5rem; font-size:1rem; font-weight:700; margin:0; }
.qm-title i { color:var(--qa-gold); font-size:1.1rem; }
.qm-title .qm-badge {
  font-size:.58rem; font-weight:700; padding:.1rem .4rem; border-radius:1rem;
  background:var(--qa-gold-dim); color:#b89730; border:1px solid rgba(212,175,55,.3);
  text-transform:uppercase; letter-spacing:.3px;
}

/* Card */
.qm-card {
  background:var(--bs-card-bg); border:var(--qa-border);
  border-radius:var(--qa-radius); box-shadow:var(--qa-shadow);
  margin-bottom:.75rem;
}
.qm-card-hdr {
  display:flex; justify-content:space-between; align-items:center;
  padding:.6rem .9rem; border-bottom:1px solid rgba(255,255,255,.06);
}
.qm-card-hdr h6 {
  margin:0; font-size:.7rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.5px; color:var(--qa-muted);
  display:flex; align-items:center; gap:.35rem;
}
.qm-card-body { padding:.85rem .9rem; }

/* Controls row */
.qm-controls {
  display:grid; grid-template-columns:1fr 200px 180px auto;
  gap:.75rem; align-items:end; margin-bottom:.85rem;
}
@media(max-width:800px) { .qm-controls { grid-template-columns:1fr 1fr; } }
@media(max-width:500px) { .qm-controls { grid-template-columns:1fr; } }
.qm-field label { display:block; font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:var(--qa-muted); margin-bottom:.3rem; }
.qm-field select, .qm-field input[type=date] {
  width:100%; background:var(--bs-input-bg,rgba(255,255,255,.05));
  border:1px solid rgba(255,255,255,.1); border-radius:.35rem;
  color:inherit; font-size:.78rem; padding:.38rem .6rem;
  outline:none; transition:border-color .15s;
}
.qm-field select:focus, .qm-field input:focus { border-color:rgba(212,175,55,.5); }

/* Textarea */
.qm-textarea {
  width:100%; min-height:260px; background:var(--bs-input-bg,rgba(255,255,255,.05));
  border:1px solid rgba(255,255,255,.1); border-radius:.4rem;
  color:inherit; font-size:.72rem; font-family:'Courier New',monospace;
  padding:.65rem .75rem; resize:vertical; outline:none; line-height:1.55;
  transition:border-color .15s;
}
.qm-textarea:focus { border-color:rgba(212,175,55,.5); }

/* Button */
.qm-btn {
  display:inline-flex; align-items:center; gap:.35rem;
  font-size:.78rem; font-weight:700; padding:.48rem 1.1rem;
  border-radius:.4rem; border:none; cursor:pointer;
  transition:all .15s; white-space:nowrap;
}
.qm-btn-primary { background:var(--qa-gold); color:#fff; }
.qm-btn-primary:hover { background:#b89730; }
.qm-btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.qm-btn-ghost {
  background:transparent; color:var(--qa-muted);
  border:1px solid rgba(255,255,255,.1);
  font-size:.72rem; padding:.38rem .7rem;
  text-decoration:none;
}
.qm-btn-ghost:hover { color:var(--qa-gold); border-color:rgba(212,175,55,.4); text-decoration:none; }

/* Parse preview strip */
.qm-preview {
  display:flex; gap:1.2rem; flex-wrap:wrap;
  background:var(--qa-surface); border-radius:.35rem;
  padding:.55rem .75rem; margin-top:.7rem; font-size:.7rem;
  align-items:center; border:1px solid rgba(255,255,255,.06);
}
.qm-preview-item { display:flex; align-items:center; gap:.3rem; color:var(--qa-muted); }
.qm-preview-item strong { color:var(--qa-gold); }
.qm-preview-item i { font-size:.8rem; }

/* Hint */
.qm-hint { font-size:.67rem; color:var(--qa-muted); margin-bottom:.85rem; line-height:1.55; }
.qm-hint code { background:rgba(255,255,255,.06); border-radius:.25rem; padding:.05rem .28rem; font-size:.65rem; }

/* Loading overlay */
.qm-scoring { display:none; text-align:center; padding:2.5rem; }
.qm-scoring.show { display:block; }
.qm-spin {
  width:36px; height:36px; border-radius:50%; border:3px solid rgba(212,175,55,.2);
  border-top-color:var(--qa-gold); animation:qmSpin .7s linear infinite;
  margin:0 auto .9rem;
}
@keyframes qmSpin { to { transform:rotate(360deg); } }

/* ═══ RESULT PANEL ═══ */
.qm-result { display:none; }
.qm-result.show { display:block; }

/* Result header with score ring */
.qm-result-hero {
  display:flex; gap:1.2rem; align-items:center; flex-wrap:wrap;
  padding:.9rem 1rem; background:var(--bs-card-bg);
  border:var(--qa-border); border-radius:var(--qa-radius);
  box-shadow:var(--qa-shadow); margin-bottom:.75rem;
}
.qm-score-ring {
  width:80px; height:80px; border-radius:50%; flex-shrink:0;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  border:3px solid; flex-shrink:0;
}
.qm-score-ring .rval { font-size:1.5rem; font-weight:800; line-height:1; letter-spacing:-.03em; }
.qm-score-ring .rmax { font-size:.55rem; color:#94a3b8; margin-top:.1rem; }
.qm-score-ring.sr-excellent { border-color:#34c38f; background:rgba(52,195,143,.07); }
.qm-score-ring.sr-excellent .rval { color:#1a8754; }
.qm-score-ring.sr-good { border-color:#556ee6; background:rgba(85,110,230,.07); }
.qm-score-ring.sr-good .rval { color:#556ee6; }
.qm-score-ring.sr-average { border-color:#f1b44c; background:rgba(241,180,76,.07); }
.qm-score-ring.sr-average .rval { color:#b87a14; }
.qm-score-ring.sr-poor { border-color:#f46a6a; background:rgba(244,106,106,.07); }
.qm-score-ring.sr-poor .rval { color:#c84646; }

.qm-result-meta h5 { margin:0 0 .25rem; font-size:1rem; font-weight:700; }
.qm-result-meta .qm-disp {
  display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.3px; padding:.12rem .5rem; border-radius:1rem;
}
.qm-disp.d-excellent { background:rgba(52,195,143,.12); color:#1a8754; }
.qm-disp.d-good      { background:rgba(85,110,230,.12); color:#556ee6; }
.qm-disp.d-average   { background:rgba(241,180,76,.12); color:#b87a14; }
.qm-disp.d-poor      { background:rgba(244,106,106,.12); color:#c84646; }
.qm-disp.d-void-risk { background:rgba(124,105,239,.12); color:#5b49c7; }
.qm-disp.d-compliance-fail { background:rgba(244,106,106,.1); color:#c84646; border:1px solid rgba(244,106,106,.25); }
.qm-result-meta .qm-meta-row { display:flex; gap:1rem; flex-wrap:wrap; margin-top:.4rem; font-size:.69rem; color:var(--qa-muted); }
.qm-result-meta .qm-meta-row i { font-size:.75rem; }

/* Sale strip */
.qm-sale-strip {
  display:flex; gap:1.5rem; align-items:center; flex-wrap:wrap;
  background:rgba(52,195,143,.07); border:1px solid rgba(52,195,143,.25);
  border-radius:.4rem; padding:.65rem .9rem; margin-bottom:.75rem;
}
.qm-sale-strip .ss-icon { font-size:1.3rem; color:#34c38f; }
.qm-sale-item .sv { font-size:.9rem; font-weight:700; color:#1a8754; }
.qm-sale-item .sl { font-size:.57rem; text-transform:uppercase; letter-spacing:.3px; color:#94a3b8; margin-top:.05rem; }

/* Category bars */
.qm-cat-row { display:flex; align-items:center; gap:.6rem; margin-bottom:.42rem; }
.qm-cat-row:last-child { margin-bottom:0; }
.qm-cat-lbl { width:110px; font-size:.66rem; color:var(--qa-muted); text-align:right; flex-shrink:0; }
.qm-cat-track { flex:1; height:8px; background:rgba(255,255,255,.07); border-radius:4px; overflow:hidden; }
.qm-cat-fill { height:100%; border-radius:4px; transition:width .5s ease; }
.qm-cat-fill.f-green { background:linear-gradient(90deg,#34c38f,#6eddb8); }
.qm-cat-fill.f-blue  { background:linear-gradient(90deg,#556ee6,#8b9cf7); }
.qm-cat-fill.f-warn  { background:linear-gradient(90deg,#f1b44c,#f7d38a); }
.qm-cat-fill.f-red   { background:linear-gradient(90deg,#f46a6a,#f99898); }
.qm-cat-score { width:28px; font-size:.7rem; font-weight:600; text-align:right; flex-shrink:0; }

/* Compliance checklist */
.qm-check-item { display:flex; align-items:center; gap:.45rem; padding:.3rem 0; border-bottom:1px solid rgba(255,255,255,.04); font-size:.7rem; }
.qm-check-item:last-child { border-bottom:none; }
.qm-check-icon { width:16px; text-align:center; font-size:.82rem; flex-shrink:0; }
.qm-check-pass { color:#34c38f; }
.qm-check-fail { color:#f46a6a; }
.qm-check-na   { color:rgba(255,255,255,.2); }

/* Coaching */
.qm-coaching {
  background:rgba(212,175,55,.05); border-left:3px solid var(--qa-gold);
  border-radius:0 .35rem .35rem 0; padding:.6rem .8rem;
  font-size:.73rem; line-height:1.6; color:inherit;
}
.qm-coaching ul { margin:0; padding-left:1rem; }
.qm-coaching li { margin-bottom:.2rem; }

/* Collapsible */
.qm-collapsible-hdr { cursor:pointer; }
.qm-collapsible-hdr:hover h6 { color:var(--qa-gold); }
.qm-toggle-icon { font-size:.9rem; color:var(--qa-muted); flex-shrink:0; }

/* Badges */
.qm-badge-red   { display:inline-block; font-size:.6rem; font-weight:700; padding:.1rem .38rem; border-radius:.25rem; background:var(--qa-red-dim); color:#c84646; }
.qm-badge-green { display:inline-block; font-size:.6rem; font-weight:700; padding:.1rem .38rem; border-radius:.25rem; background:var(--qa-green-dim); color:#1a8754; }

/* View in dashboard link */
.qm-view-link {
  display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem;
  color:var(--qa-muted); border:1px solid rgba(255,255,255,.1);
  border-radius:.35rem; padding:.3rem .65rem; text-decoration:none;
  transition:all .15s; margin-left:auto;
}
.qm-view-link:hover { color:var(--qa-gold); border-color:rgba(212,175,55,.4); text-decoration:none; }

/* Toast */
.qa-toast {
  position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
  padding:.65rem 1rem; border-radius:.45rem; font-size:.78rem; font-weight:600;
  max-width:340px; transform:translateY(10px); opacity:0; transition:all .3s;
  box-shadow:0 4px 20px rgba(0,0,0,.3); pointer-events:none;
}
.qa-toast.show { transform:translateY(0); opacity:1; }
.qa-toast.success { background:#1a8754; color:#fff; }
.qa-toast.error   { background:#c84646; color:#fff; }
</style>
@endsection

@section('content')
<div class="qm-wrap">

    <!-- Header -->
    <div class="qm-header">
        <h5 class="qm-title">
            <i class="ri-upload-cloud-line"></i>
            Manual QA Score
            <span class="qm-badge">Paste Zoom Transcript</span>
        </h5>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <a href="{{ route('qa.upload') }}" class="qm-btn qm-btn-ghost">
                <i class="ri-mic-line"></i> Upload Audio
            </a>
            <a href="/qa/scoring" class="qm-btn qm-btn-ghost">
                <i class="ri-line-chart-line"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- How it works hint -->
    <div class="qm-hint">
        Open the call recording in <strong>Zoom → Phone System → Recordings</strong>, click the transcript icon,
        select all (<kbd>Ctrl+A</kbd>), copy, and paste below.
        The system auto-detects the agent name and customer from the Zoom format.<br>
        Uses <strong>Claude Sonnet 4.6</strong> for analysis — designed for sales QA only.
    </div>

    <!-- Input card -->
    <div class="qm-card">
        <div class="qm-card-hdr">
            <h6><i class="ri-file-text-line"></i> Transcript Input</h6>
            <span id="lineCount" style="font-size:.63rem;color:var(--qa-muted);">0 lines</span>
        </div>
        <div class="qm-card-body">
            <!-- Controls -->
            <div class="qm-controls">
                <div class="qm-field">
                    <label>Agent / Closer</label>
                    <select id="agentSelect">
                        <option value="">— Auto-detect from transcript —</option>
                        @foreach($agents as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="qm-field">
                    <label>Call Date</label>
                    <input type="date" id="callDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="qm-field">
                    <label>&nbsp;</label>
                    <button class="qm-btn qm-btn-primary" id="scoreBtn" onclick="scoreTranscript()">
                        <i class="ri-robot-line"></i> Score with AI
                    </button>
                </div>
            </div>

            <!-- Transcript textarea -->
            <textarea class="qm-textarea" id="transcriptInput"
                placeholder="Paste the Zoom transcript here...

Example format:
00:10 Sarah Garcia
Hey Loretta, this is Sarah speaking. How are you, ma'am?
00:14 13368519588
Mrs. Who?
00:15 Sarah Garcia
This is Sarah, your life insurance agent..."
                oninput="onTranscriptChange()"></textarea>

            <!-- Live parse preview -->
            <div class="qm-preview" id="parsePreview" style="display:none;">
                <div class="qm-preview-item"><i class="ri-user-line"></i> Agent: <strong id="pvAgent">—</strong></div>
                <div class="qm-preview-item"><i class="ri-time-line"></i> Duration: <strong id="pvDuration">—</strong></div>
                <div class="qm-preview-item"><i class="ri-chat-3-line"></i> Lines: <strong id="pvLines">—</strong></div>
                <div class="qm-preview-item" id="pvCustomer" style="display:none;"><i class="ri-group-line"></i> Customer: <strong id="pvCustomerName">—</strong></div>
            </div>
        </div>
    </div>

    <!-- Scoring spinner -->
    <div class="qm-card qm-scoring" id="scoringPanel">
        <div class="qm-spin"></div>
        <p style="font-size:.82rem;color:var(--qa-muted);margin:0;">Analyzing with Claude Sonnet 4.6… this takes 20–40 seconds.</p>
        <p style="font-size:.68rem;color:var(--qa-muted);margin:.3rem 0 0;">Checking 11 compliance items + 7 score categories.</p>
    </div>

    <!-- Result panel -->
    <div id="resultPanel" class="qm-result">

        <!-- Score hero -->
        <div class="qm-result-hero">
            <div class="qm-score-ring" id="rScoreRing">
                <div class="rval" id="rScore">—</div>
                <div class="rmax">/100</div>
            </div>
            <div class="qm-result-meta" style="flex:1;">
                <h5 id="rCustomerName">—</h5>
                <div>
                    <span class="qm-disp" id="rDisp">—</span>
                    <span id="rComplianceBadge" style="margin-left:.45rem;"></span>
                </div>
                <div class="qm-meta-row">
                    <span id="rAgent"><i class="ri-headphone-line"></i> —</span>
                    <span id="rDuration"><i class="ri-time-line"></i> —</span>
                    <span id="rDate"><i class="ri-calendar-line"></i> —</span>
                </div>
            </div>
            <a href="#" id="rDashboardLink" class="qm-view-link" target="_blank">
                <i class="ri-external-link-line"></i> View in Dashboard
            </a>
        </div>

        <!-- Sale strip -->
        <div class="qm-sale-strip" id="rSaleStrip" style="display:none;">
            <i class="ri-shield-check-fill ss-icon"></i>
            <div class="qm-sale-item" id="rSalePremium" style="display:none;"><div class="sv" id="rPremiumVal">—</div><div class="sl">Monthly Premium</div></div>
            <div class="qm-sale-item" id="rSaleCoverage" style="display:none;"><div class="sv" id="rCoverageVal">—</div><div class="sl">Coverage</div></div>
            <div class="qm-sale-item" id="rSaleCarrier" style="display:none;"><div class="sv" id="rCarrierVal">—</div><div class="sl">Carrier</div></div>
            <div class="qm-sale-item"><div class="sv" style="color:#34c38f;">✓ SALE</div><div class="sl">Status</div></div>
        </div>

        <!-- 2-col: category + compliance -->
        <div class="row g-2 mb-2">
            <div class="col-md-7">
                <div class="qm-card">
                    <div class="qm-card-hdr"><h6><i class="ri-equalizer-line"></i> Category Breakdown</h6></div>
                    <div class="qm-card-body" id="rCatBars"></div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="qm-card" style="height:100%;">
                    <div class="qm-card-hdr">
                        <h6><i class="ri-shield-check-line"></i> Compliance</h6>
                        <span id="rCompBadge"></span>
                    </div>
                    <div class="qm-card-body" id="rChecklist"></div>
                </div>
            </div>
        </div>

        <!-- Coaching notes (open by default) -->
        <div class="qm-card mb-2">
            <div class="qm-card-hdr qm-collapsible-hdr" onclick="toggleSection('rCoachBody', this)">
                <h6><i class="ri-lightbulb-line"></i> AI Coaching Notes</h6>
                <i class="ri-arrow-down-s-line qm-toggle-icon"></i>
            </div>
            <div class="qm-card-body" id="rCoachBody"></div>
        </div>

        <!-- Score again button -->
        <div style="text-align:center;margin-top:.5rem;margin-bottom:1.5rem;">
            <button class="qm-btn qm-btn-ghost" onclick="resetForm()">
                <i class="ri-refresh-line"></i> Score Another Call
            </button>
        </div>
    </div>

</div>
<div class="qa-toast" id="qaToast"></div>
@endsection

@section('script')
<script>
(function() {
    const COMP_LABELS = {
        C1_agent_identity:            'C1  Closer Introduction',
        C2_carrier_named:             'C2  Carrier Named',
        C3_product_type_stated:       'C3  Product Type Stated',
        C4_health_questions_complete: 'C4  Health Questions',
        C5_quote_and_coverage:        'C5  Quote & Coverage',
        C6_draft_date_confirmed:      'C6  Draft Date Confirmed',
        C7_end_of_call_consent:       'C7  End-of-Call Consent',
        C8_application_info_collected:'C8  Application Info',
        C9_customer_not_on_dnc:       'C9  DNC Honored',
        C10_agent_handles_objections: 'C10 Handles Objections',
        C11_appropriate_language:     'C11 Appropriate Language',
    };

    const CAT_LABELS = {
        opening: 'Opening', discovery: 'Discovery', presentation: 'Presentation',
        objection_handling: 'Obj. Handling', closing: 'Closing',
        soft_skills: 'Soft Skills', call_control: 'Call Control',
    };

    // ── Transcript parser (client-side preview) ───────────────────────
    function parseTranscriptPreview(text) {
        const lines  = text.split(/\r?\n/);
        const tsRe   = /^(\d{1,2}:\d{2})(?:\s+(.+))?$/;
        const phoneRe = /^\+?[\d\s\-().]{10,15}$/;
        let agentName = null, lastCustomerPhone = null;
        let agentCount = 0, customerCount = 0;
        let lastTs = 0;

        for (const raw of lines) {
            const line = raw.trim();
            if (!line) continue;
            const m = line.match(tsRe);
            if (m) {
                const [min, sec] = m[1].split(':').map(Number);
                lastTs = min * 60 + sec;
                const spk = (m[2] || '').trim();
                if (!spk) continue;
                if (phoneRe.test(spk) && /^\d/.test(spk)) {
                    customerCount++;
                    lastCustomerPhone = spk;
                } else {
                    agentCount++;
                    if (!agentName) agentName = spk;
                }
            }
        }

        return { agentName, lastTs, totalLines: lines.filter(l => l.trim()).length,
                 agentCount, customerCount };
    }

    function fmtSec(s) {
        const m = Math.floor(s / 60), sec = s % 60;
        return m > 0 ? `${m}m ${sec}s` : `${sec}s`;
    }

    // ── Live preview ─────────────────────────────────────────────────
    window.onTranscriptChange = function() {
        const text = document.getElementById('transcriptInput').value;
        const preview = document.getElementById('parsePreview');
        const lc = document.getElementById('lineCount');
        const lines = text.split(/\r?\n/).filter(l => l.trim());
        lc.textContent = lines.length + ' lines';

        if (text.trim().length < 50) { preview.style.display = 'none'; return; }

        const p = parseTranscriptPreview(text);
        preview.style.display = 'flex';
        document.getElementById('pvAgent').textContent    = p.agentName || 'Not detected';
        document.getElementById('pvDuration').textContent = fmtSec(p.lastTs);
        document.getElementById('pvLines').textContent    = p.totalLines;
    };

    // ── Score ────────────────────────────────────────────────────────
    window.scoreTranscript = function() {
        const raw = document.getElementById('transcriptInput').value.trim();
        if (!raw || raw.length < 200) {
            showToast('Please paste a transcript (minimum ~200 characters).', 'error');
            return;
        }

        const btn = document.getElementById('scoreBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line"></i> Scoring…';

        document.getElementById('scoringPanel').classList.add('show');
        document.getElementById('resultPanel').classList.remove('show');

        fetch('/qa/api/manual-score', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                transcript:    raw,
                agent_user_id: document.getElementById('agentSelect').value || null,
                call_date:     document.getElementById('callDate').value || null,
            }),
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('scoringPanel').classList.remove('show');
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-robot-line"></i> Score with AI';

            if (!d.success) {
                showToast('Error: ' + (d.message || 'Scoring failed'), 'error');
                return;
            }

            renderResult(d);
            document.getElementById('resultPanel').classList.add('show');
            document.getElementById('resultPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(e => {
            document.getElementById('scoringPanel').classList.remove('show');
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-robot-line"></i> Score with AI';
            showToast('Request failed: ' + e.message, 'error');
        });
    };

    // ── Render result ────────────────────────────────────────────────
    function scoreClass(s) {
        s = parseFloat(s);
        if (isNaN(s)) return '';
        return s >= 90 ? 'sr-excellent' : s >= 75 ? 'sr-good' : s >= 60 ? 'sr-average' : 'sr-poor';
    }

    function dispCls(d) {
        return 'd-' + (d || '').toLowerCase().replace(/_/g, '-');
    }
    function dispLabel(d) {
        return (d || 'N/A').replace(/_/g, ' ');
    }

    function catFillClass(score) {
        return score >= 8 ? 'f-green' : score >= 6 ? 'f-blue' : score >= 4 ? 'f-warn' : 'f-red';
    }

    function renderResult(d) {
        const r = d.result;
        const p = d.parsed;
        const score = r.total_score != null ? parseFloat(r.total_score).toFixed(0) : '—';
        const sc    = scoreClass(r.total_score);

        // Score ring
        const ring = document.getElementById('rScoreRing');
        ring.className = 'qm-score-ring ' + sc;
        document.getElementById('rScore').textContent = score;

        // Disposition
        const disp = document.getElementById('rDisp');
        disp.className = 'qm-disp ' + dispCls(r.disposition);
        disp.textContent = dispLabel(r.disposition);

        // Compliance badge (header)
        const failKeys = Object.keys(r.compliance_checks || {}).filter(k => r.compliance_checks[k] === false);
        const compBadge = document.getElementById('rComplianceBadge');
        compBadge.innerHTML = failKeys.length
            ? `<span class="qm-badge-red">${failKeys.length} COMPLIANCE FAIL${failKeys.length > 1 ? 'S' : ''}</span>`
            : `<span class="qm-badge-green">Compliant</span>`;

        // Meta
        document.getElementById('rCustomerName').textContent = r.customer_name || 'Customer';
        document.getElementById('rAgent').innerHTML   = `<i class="ri-headphone-line"></i> ${esc(p.agent_name || 'Unknown Agent')}`;
        document.getElementById('rDuration').innerHTML = `<i class="ri-time-line"></i> ${fmtSec(p.duration_seconds)}`;
        document.getElementById('rDate').innerHTML     = `<i class="ri-calendar-line"></i> ${document.getElementById('callDate').value || 'Today'}`;

        // Dashboard link
        document.getElementById('rDashboardLink').href = `/qa/scoring`;

        // Sale strip
        const saleStrip = document.getElementById('rSaleStrip');
        if (r.is_sale) {
            saleStrip.style.display = 'flex';
            if (r.monthly_premium) {
                document.getElementById('rSalePremium').style.display = '';
                document.getElementById('rPremiumVal').textContent = `$${parseFloat(r.monthly_premium).toFixed(2)}/mo`;
            }
            if (r.sale_amount) {
                document.getElementById('rSaleCoverage').style.display = '';
                document.getElementById('rCoverageVal').textContent = `$${parseFloat(r.sale_amount).toLocaleString()}`;
            }
            if (r.carrier_name) {
                document.getElementById('rSaleCarrier').style.display = '';
                document.getElementById('rCarrierVal').textContent = r.carrier_name;
            }
        } else {
            saleStrip.style.display = 'none';
        }

        // Category bars
        const catBars = document.getElementById('rCatBars');
        const sb = r.score_breakdown || {};
        catBars.innerHTML = Object.entries(CAT_LABELS).map(([key, label]) => {
            const val = sb[key] ?? 0;
            const pct = Math.min(100, (val / 10) * 100);
            return `<div class="qm-cat-row">
                <div class="qm-cat-lbl">${label}</div>
                <div class="qm-cat-track"><div class="qm-cat-fill ${catFillClass(val)}" style="width:${pct}%"></div></div>
                <div class="qm-cat-score">${val}</div>
            </div>`;
        }).join('');

        // Compliance checklist
        const checks = r.compliance_checks || {};
        const compCard = document.getElementById('rCompBadge');
        compCard.innerHTML = failKeys.length
            ? `<span class="qm-badge-red">${failKeys.length} failed</span>`
            : `<span class="qm-badge-green">All clear</span>`;

        const passKeys = Object.keys(checks).filter(k => checks[k] === true);
        const naKeys   = Object.keys(checks).filter(k => checks[k] === null);
        const sorted   = [...failKeys, ...naKeys, ...passKeys];

        const checklist = document.getElementById('rChecklist');
        if (!sorted.length) {
            checklist.innerHTML = '<div style="font-size:.7rem;color:var(--qa-muted);padding:.5rem 0;">No compliance data</div>';
        } else {
            checklist.innerHTML = sorted.map((k, i) => {
                const val    = checks[k];
                const isFail = val === false;
                const isPass = val === true;
                const [iconCls, wrapCls] = isPass  ? ['ri-checkbox-circle-fill', 'qm-check-pass']
                    : val === null ? ['ri-indeterminate-circle-line', 'qm-check-na']
                    :                ['ri-close-circle-fill',          'qm-check-fail'];
                const divider = (i === failKeys.length && failKeys.length > 0 && !isFail)
                    ? '<div style="height:1px;background:rgba(255,255,255,.06);margin:.2rem 0;"></div>' : '';
                return `${divider}<div class="qm-check-item" style="${isFail ? 'background:rgba(244,106,106,.05);border-radius:.25rem;padding:.3rem .35rem;margin:.08rem 0;' : isPass && failKeys.length ? 'opacity:.55;' : ''}">
                    <div class="qm-check-icon ${wrapCls}"><i class="${iconCls}"></i></div>
                    <div style="flex:1;color:${isFail ? '#f46a6a' : 'inherit'};font-weight:${isFail ? '600' : '400'};">${esc(COMP_LABELS[k] || k)}</div>
                </div>`;
            }).join('');
        }

        // Coaching notes
        const coachEl = document.getElementById('rCoachBody');
        const coaching = r.coaching_notes;
        if (!coaching) {
            coachEl.innerHTML = '<div style="font-size:.7rem;color:var(--qa-muted);">No coaching notes</div>';
        } else {
            const lines_c = Array.isArray(coaching)
                ? coaching
                : String(coaching).split(/\n|(?:^|\n)\s*[-•]\s*/g).filter(l => l.trim());
            coachEl.innerHTML = lines_c.length
                ? `<div class="qm-coaching"><ul>${lines_c.map(n => '<li>' + esc(String(n).trim()) + '</li>').join('')}</ul></div>`
                : '<div style="font-size:.7rem;color:var(--qa-muted);">No coaching notes</div>';
        }
    }

    // ── Collapsible ───────────────────────────────────────────────────
    window.toggleSection = function(id, hdr) {
        const sec  = document.getElementById(id);
        if (!sec) return;
        const icon = hdr && hdr.querySelector('.qm-toggle-icon');
        const open = sec.style.display !== 'none';
        sec.style.display = open ? 'none' : '';
        if (icon) icon.className = open ? 'ri-arrow-right-s-line qm-toggle-icon' : 'ri-arrow-down-s-line qm-toggle-icon';
    };

    // ── Reset ─────────────────────────────────────────────────────────
    window.resetForm = function() {
        document.getElementById('transcriptInput').value = '';
        document.getElementById('parsePreview').style.display = 'none';
        document.getElementById('lineCount').textContent = '0 lines';
        document.getElementById('resultPanel').classList.remove('show');
        document.getElementById('scoringPanel').classList.remove('show');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ── Helpers ───────────────────────────────────────────────────────
    function showToast(msg, type) {
        const t = document.getElementById('qaToast');
        t.innerHTML = msg;
        t.className = 'qa-toast ' + (type || 'success');
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => t.classList.remove('show'), 5000);
    }

    function esc(s) {
        if (!s) return '';
        const d = document.createElement('div');
        d.textContent = String(s);
        return d.innerHTML;
    }

    function fmtSec(s) {
        s = parseInt(s) || 0;
        const m = Math.floor(s / 60), sec = s % 60;
        return m > 0 ? `${m}m ${sec}s` : `${sec}s`;
    }

})();
</script>
@endsection
