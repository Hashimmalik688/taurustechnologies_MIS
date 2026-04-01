<?php $__env->startSection('title', 'QA — Upload & Score Call'); ?>

<?php $__env->startSection('css'); ?>
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
.qu-wrap { max-width:1100px; margin:0 auto; padding:1rem; }

/* ── Header ── */
.qu-header { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; margin-bottom:1.1rem; }
.qu-title  { display:flex; align-items:center; gap:.5rem; font-size:1rem; font-weight:700; margin:0; }
.qu-title i { color:var(--qa-gold); font-size:1.1rem; }
.qu-badge  { font-size:.58rem; font-weight:700; padding:.1rem .4rem; border-radius:1rem; background:var(--qa-blue-dim); color:#4a60d4; border:1px solid rgba(85,110,230,.3); text-transform:uppercase; letter-spacing:.3px; }

/* ── Card ── */
.qu-card { background:var(--bs-card-bg); border:var(--qa-border); border-radius:var(--qa-radius); box-shadow:var(--qa-shadow); margin-bottom:.75rem; }
.qu-card-hdr { display:flex; justify-content:space-between; align-items:center; padding:.6rem .9rem; border-bottom:1px solid rgba(255,255,255,.06); }
.qu-card-hdr h6 { margin:0; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--qa-muted); display:flex; align-items:center; gap:.35rem; }
.qu-card-body { padding:.85rem .9rem; }

/* ── Upload drop zone ── */
.qu-dropzone {
  border:2px dashed rgba(85,110,230,.35);
  border-radius:var(--qa-radius);
  padding:2.5rem 1rem;
  text-align:center;
  cursor:pointer;
  transition:border-color .2s, background .2s;
  user-select:none;
}
.qu-dropzone.drag-over, .qu-dropzone:hover { border-color:var(--qa-blue); background:var(--qa-blue-dim); }
/* Hidden file input — triggered programmatically via onclick/JS */
#qu-file { display:none; }
.qu-dropzone i { font-size:2.2rem; color:var(--qa-blue); margin-bottom:.6rem; display:block; }
.qu-dz-label { font-size:.8rem; color:var(--qa-muted); margin:0; }
.qu-dz-label strong { color:var(--qa-blue); }
.qu-file-name { font-size:.75rem; margin-top:.5rem; color:var(--qa-gold); font-weight:600; }

/* ── Controls ── */
.qu-controls { display:grid; grid-template-columns:1fr 200px 180px; gap:.75rem; align-items:end; margin-bottom:.85rem; }
@media(max-width:700px){ .qu-controls { grid-template-columns:1fr 1fr; } }
.qu-field label { display:block; font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:var(--qa-muted); margin-bottom:.3rem; }
.qu-field select, .qu-field input[type=date] {
  width:100%; background:var(--bs-input-bg,rgba(255,255,255,.05));
  border:1px solid rgba(255,255,255,.1); border-radius:.35rem;
  color:inherit; font-size:.78rem; padding:.38rem .6rem;
  outline:none; transition:border-color .15s;
}
.qu-field select:focus, .qu-field input:focus { border-color:rgba(85,110,230,.5); }

/* ── Swap speakers toggle ── */
.qu-toggle { display:flex; align-items:center; gap:.5rem; font-size:.75rem; color:var(--qa-muted); cursor:pointer; }
.qu-toggle input { accent-color:var(--qa-blue); width:14px; height:14px; }

/* ── Submit button ── */
.qu-btn {
  display:inline-flex; align-items:center; gap:.4rem;
  background:var(--qa-blue); color:#fff; font-size:.78rem; font-weight:600;
  padding:.5rem 1.2rem; border-radius:.4rem; border:none; cursor:pointer;
  transition:opacity .15s, transform .1s;
}
.qu-btn:hover { opacity:.9; }
.qu-btn:active { transform:scale(.97); }
.qu-btn:disabled { opacity:.45; cursor:not-allowed; }

/* ── Progress / status ── */
.qu-progress-wrap { display:none; margin-top:1rem; }
.qu-progress-wrap.show { display:block; }
.qu-progress-bar-outer { height:6px; background:rgba(255,255,255,.08); border-radius:3px; overflow:hidden; margin-bottom:.5rem; }
.qu-progress-bar-inner { height:100%; background:var(--qa-blue); width:0%; transition:width .4s; border-radius:3px; }
.qu-progress-bar-inner.striped { background:repeating-linear-gradient(45deg,var(--qa-blue) 0,var(--qa-blue) 10px,rgba(85,110,230,.5) 10px,rgba(85,110,230,.5) 20px); background-size:40px 6px; animation:stripes .7s linear infinite; }
@keyframes stripes { to { background-position:40px 0; } }
.qu-status-msg { font-size:.75rem; color:var(--qa-muted); }
.qu-status-msg.error { color:var(--qa-red); }

/* ── Score result panel ── */
.qu-result { display:none; }
.qu-result.show { display:block; }

/* Score badge */
.qu-score-hero { text-align:center; padding:.75rem 0; }
.qu-score-num  { font-size:3rem; font-weight:800; line-height:1; }
.qu-score-num.excellent { color:var(--qa-green); }
.qu-score-num.good      { color:#5bc8a8; }
.qu-score-num.average   { color:var(--qa-warn); }
.qu-score-num.poor      { color:var(--qa-red); }
.qu-score-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--qa-muted); margin-top:.2rem; }
.qu-disp-badge { display:inline-block; font-size:.65rem; font-weight:700; padding:.15rem .55rem; border-radius:.8rem; text-transform:uppercase; letter-spacing:.3px; margin-top:.35rem; }
.disp-EXCELLENT      { background:var(--qa-green-dim); color:var(--qa-green); border:1px solid rgba(52,195,143,.3); }
.disp-GOOD           { background:rgba(91,200,168,.1); color:#5bc8a8; border:1px solid rgba(91,200,168,.3); }
.disp-AVERAGE        { background:rgba(241,180,76,.1); color:var(--qa-warn); border:1px solid rgba(241,180,76,.3); }
.disp-POOR           { background:var(--qa-red-dim); color:var(--qa-red); border:1px solid rgba(244,106,106,.3); }
.disp-VOID_RISK      { background:rgba(154,55,255,.12); color:#a66df5; border:1px solid rgba(154,55,255,.3); }
.disp-COMPLIANCE_FAIL{ background:var(--qa-red-dim); color:var(--qa-red); border:1px solid rgba(244,106,106,.3); }

/* Compliance badge */
.comp-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.68rem; font-weight:700; padding:.18rem .55rem; border-radius:.8rem; }
.comp-pass { background:var(--qa-green-dim); color:var(--qa-green); border:1px solid rgba(52,195,143,.3); }
.comp-fail { background:var(--qa-red-dim);   color:var(--qa-red);   border:1px solid rgba(244,106,106,.3); }

/* Score breakdown bars */
.qu-breakdown { display:grid; grid-template-columns:1fr 1fr; gap:.4rem .75rem; }
@media(max-width:600px){ .qu-breakdown { grid-template-columns:1fr; } }
.qu-bar-row { display:flex; align-items:center; gap:.5rem; }
.qu-bar-label { width:130px; font-size:.65rem; color:var(--qa-muted); white-space:nowrap; flex-shrink:0; }
.qu-bar-outer { flex:1; height:5px; background:rgba(255,255,255,.07); border-radius:3px; overflow:hidden; }
.qu-bar-inner { height:100%; background:var(--qa-blue); border-radius:3px; transition:width .5s; }
.qu-bar-val   { font-size:.65rem; font-weight:700; width:28px; text-align:right; }

/* Compliance checks grid */
.qu-checks { display:grid; grid-template-columns:1fr 1fr; gap:.35rem .75rem; }
@media(max-width:600px){ .qu-checks { grid-template-columns:1fr; } }
.qu-check-item { display:flex; align-items:center; gap:.45rem; font-size:.7rem; }
.qu-check-dot  { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.dot-pass { background:var(--qa-green); }
.dot-fail { background:var(--qa-red); }
.dot-na   { background:rgba(255,255,255,.2); }

/* Coaching notes */
.qu-coaching { background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.07); border-radius:.4rem; padding:.7rem .85rem; font-size:.73rem; line-height:1.65; white-space:pre-wrap; }

/* View full detail link */
.qu-detail-link { font-size:.72rem; color:var(--qa-blue); text-decoration:none; }
.qu-detail-link:hover { text-decoration:underline; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="qu-wrap">

  
  <div class="qu-header">
    <h1 class="qu-title">
      <i class="bx bxs-microphone"></i>
      Upload &amp; Score Call
      <span class="qu-badge">AssemblyAI + Claude</span>
    </h1>
    <div class="d-flex gap-2">
      <a href="<?php echo e(route('qa.manual')); ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.72rem;">
        <i class="bx bx-text me-1"></i>Paste Transcript
      </a>
      <a href="<?php echo e(route('qa.scoring')); ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.72rem;">
        <i class="bx bx-bar-chart-alt-2 me-1"></i>Dashboard
      </a>
    </div>
  </div>

  
  <div class="qu-card mb-3">
    <div class="qu-card-body" style="padding:.65rem .9rem;">
      <div class="d-flex gap-3 flex-wrap align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span style="width:22px;height:22px;border-radius:50%;background:var(--qa-blue-dim);border:1px solid rgba(85,110,230,.4);display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;color:var(--qa-blue);">1</span>
          <span style="font-size:.72rem;">Upload audio file</span>
        </div>
        <i class="bx bx-chevron-right" style="color:var(--qa-muted);"></i>
        <div class="d-flex align-items-center gap-2">
          <span style="width:22px;height:22px;border-radius:50%;background:var(--qa-blue-dim);border:1px solid rgba(85,110,230,.4);display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;color:var(--qa-blue);">2</span>
          <span style="font-size:.72rem;">AssemblyAI transcribes with speaker diarization</span>
        </div>
        <i class="bx bx-chevron-right" style="color:var(--qa-muted);"></i>
        <div class="d-flex align-items-center gap-2">
          <span style="width:22px;height:22px;border-radius:50%;background:var(--qa-blue-dim);border:1px solid rgba(85,110,230,.4);display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;color:var(--qa-blue);">3</span>
          <span style="font-size:.72rem;">Claude AI scores the transcript</span>
        </div>
        <i class="bx bx-chevron-right" style="color:var(--qa-muted);"></i>
        <div class="d-flex align-items-center gap-2">
          <span style="width:22px;height:22px;border-radius:50%;background:var(--qa-green-dim);border:1px solid rgba(52,195,143,.4);display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;color:var(--qa-green);">✓</span>
          <span style="font-size:.72rem;">QA result saved to dashboard</span>
        </div>
      </div>
    </div>
  </div>

  
  <div class="qu-card">
    <div class="qu-card-hdr">
      <h6><i class="bx bx-upload"></i> Audio File</h6>
      <span style="font-size:.65rem;color:var(--qa-muted);">MP3, WAV, M4A, MP4, OGG, FLAC, AAC · Max 50 MB</span>
    </div>
    <div class="qu-card-body">

      
      <div class="qu-controls">
        <div class="qu-field">
          <label>Agent</label>
          <select id="qu-agent">
            <option value="">— Unknown / unassigned —</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </select>
        </div>
        <div class="qu-field">
          <label>Call Date</label>
          <input type="date" id="qu-date" value="<?php echo e(now()->toDateString()); ?>">
        </div>
        <div class="qu-field" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:.1rem;">
          <label for="qu-swap" class="qu-toggle" style="margin-bottom:.3rem;">
            <input type="checkbox" id="qu-swap">
            <span>Swap Speakers<br><span style="font-size:.6rem;opacity:.6;">Use if Customer speaks first</span></span>
          </label>
        </div>
      </div>

      
      <input type="file" id="qu-file" accept=".mp3,.wav,.m4a,.mp4,.ogg,.webm,.flac,.aac" />

      
      <div class="qu-dropzone" id="qu-dropzone" onclick="document.getElementById('qu-file').click()">
        <i class="bx bx-cloud-upload" id="qu-dz-icon"></i>
        <p class="qu-dz-label" id="qu-dz-label">
          <strong>Click to browse</strong> or drag &amp; drop your audio file here
        </p>
        <p class="qu-file-name" id="qu-file-name" style="display:none;"></p>
      </div>

      
      <div class="qu-progress-wrap" id="qu-progress-wrap">
        <div class="qu-progress-bar-outer">
          <div class="qu-progress-bar-inner striped" id="qu-progress-bar" style="width:30%;"></div>
        </div>
        <p class="qu-status-msg" id="qu-status-msg">Uploading audio to AssemblyAI…</p>
      </div>

      
      <div class="d-flex gap-2 mt-3">
        <button class="qu-btn" id="qu-submit-btn" disabled>
          <i class="bx bx-play-circle"></i>
          Transcribe &amp; Score
        </button>
        <button class="qu-btn" id="qu-reset-btn" style="background:rgba(255,255,255,.07);color:var(--qa-muted);" onclick="resetForm()">
          <i class="bx bx-refresh"></i>
          Reset
        </button>
      </div>
    </div>
  </div>

  
  <div class="qu-result" id="qu-result">

    <div class="row g-2">

      
      <div class="col-md-3">
        <div class="qu-card h-100">
          <div class="qu-card-hdr"><h6><i class="bx bx-trophy"></i> Score</h6></div>
          <div class="qu-card-body qu-score-hero">
            <div class="qu-score-num" id="res-score">—</div>
            <div class="qu-score-label">Total Score</div>
            <div id="res-disp-badge" class="qu-disp-badge mt-1"></div>
            <div id="res-comp-badge" class="mt-2"></div>
            <div id="res-sale-badge" class="mt-1"></div>
          </div>
        </div>
      </div>

      
      <div class="col-md-5">
        <div class="qu-card h-100">
          <div class="qu-card-hdr"><h6><i class="bx bx-bar-chart-alt-2"></i> Score Breakdown</h6></div>
          <div class="qu-card-body">
            <div class="qu-breakdown" id="res-breakdown"></div>
          </div>
        </div>
      </div>

      
      <div class="col-md-4">
        <div class="qu-card h-100">
          <div class="qu-card-hdr"><h6><i class="bx bx-shield-check"></i> Compliance</h6></div>
          <div class="qu-card-body">
            <div class="qu-checks" id="res-checks"></div>
            <div id="res-violations" class="mt-2" style="font-size:.68rem;color:var(--qa-red);"></div>
          </div>
        </div>
      </div>

      
      <div class="col-12">
        <div class="qu-card">
          <div class="qu-card-hdr">
            <h6><i class="bx bx-comment-dots"></i> Coaching Notes</h6>
            <a href="#" class="qu-detail-link" id="res-detail-link" target="_blank">View Full Detail →</a>
          </div>
          <div class="qu-card-body">
            <div class="qu-coaching" id="res-coaching">—</div>
            <div class="mt-2 d-flex gap-3" style="font-size:.72rem;flex-wrap:wrap;">
              <span><span style="color:var(--qa-muted);">Top issue:</span> <span id="res-top-issue">—</span></span>
              <span><span style="color:var(--qa-muted);">Customer:</span> <span id="res-customer">—</span></span>
              <span><span style="color:var(--qa-muted);">Carrier:</span> <span id="res-carrier">—</span></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
// ─────────────────────────────────────────────────────────────────
// QA Upload & Score — frontend logic
// Flow: pick file → upload + submit to AssemblyAI → poll status → display result
// ─────────────────────────────────────────────────────────────────

const POLL_INTERVAL_MS  = 5000; // 5 s between status polls
const MAX_POLL_ATTEMPTS = 180;  // 15 min max wait

let selectedFile  = null;
let pollTimer     = null;
let pollAttempts  = 0;
let activeQaCallId = null;

// ── File picker / drag-drop ──────────────────────────────────────

const dropzone   = document.getElementById('qu-dropzone');
const fileInput  = document.getElementById('qu-file');
const fileLabel  = document.getElementById('qu-file-name');
const submitBtn  = document.getElementById('qu-submit-btn');

// File input change — fires when user picks a file via dialog
fileInput.addEventListener('change', () => {
  if (fileInput.files && fileInput.files.length) {
    selectFile(fileInput.files[0]);
  }
});

// Prevent default browser drag behaviour across the whole page
document.addEventListener('dragover',  (e) => e.preventDefault());
document.addEventListener('drop',      (e) => e.preventDefault());

// Dropzone visual highlight on drag
dropzone.addEventListener('dragenter', (e) => { e.preventDefault(); dropzone.classList.add('drag-over'); });
dropzone.addEventListener('dragover',  (e) => { e.preventDefault(); dropzone.classList.add('drag-over'); });
dropzone.addEventListener('dragleave', (e) => {
  // Only remove highlight when leaving the dropzone itself, not a child element
  if (!dropzone.contains(e.relatedTarget)) dropzone.classList.remove('drag-over');
});
dropzone.addEventListener('drop', (e) => {
  e.preventDefault();
  e.stopPropagation();
  dropzone.classList.remove('drag-over');
  const files = e.dataTransfer?.files;
  if (files && files.length) selectFile(files[0]);
});

function selectFile(file) {
  selectedFile = file;
  const sizeMB = (file.size / 1048576).toFixed(1);

  // Swap dropzone content: hide default prompt, show filename
  document.getElementById('qu-dz-icon').style.display  = 'none';
  document.getElementById('qu-dz-label').style.display = 'none';
  fileLabel.innerHTML = `<i class="bx bx-check-circle" style="color:var(--qa-green);margin-right:.35rem;"></i>${file.name} <span style="opacity:.6;font-weight:400;">(${sizeMB} MB)</span>`;
  fileLabel.style.display = 'block';

  submitBtn.disabled = false;
  submitBtn.focus();
}

// ── Submit ───────────────────────────────────────────────────────

submitBtn.addEventListener('click', () => {
  if (!selectedFile) return;
  startUpload();
});

async function startUpload() {
  submitBtn.disabled = true;
  showProgress(10, 'Uploading audio file…');
  hideResult();

  const formData = new FormData();
  formData.append('audio',         selectedFile);
  formData.append('agent_user_id', document.getElementById('qu-agent').value || '');
  formData.append('call_date',     document.getElementById('qu-date').value   || '');
  formData.append('swap_speakers', document.getElementById('qu-swap').checked ? '1' : '0');
  formData.append('_token',        '<?php echo e(csrf_token()); ?>');

  try {
    const res   = await fetch('<?php echo e(route("qa.api.upload.transcribe")); ?>', { method: 'POST', body: formData });
    const data  = await res.json();

    if (!data.success) {
      showError(data.message || 'Upload failed.');
      submitBtn.disabled = false;
      return;
    }

    activeQaCallId = data.qa_call_id;
    showProgress(30, 'Audio submitted to AssemblyAI. Waiting for transcription…');
    startPolling(data.qa_call_id);

  } catch (err) {
    showError('Network error: ' + err.message);
    submitBtn.disabled = false;
  }
}

// ── Polling ──────────────────────────────────────────────────────

function startPolling(qaCallId) {
  pollAttempts = 0;
  clearInterval(pollTimer);
  pollTimer = setInterval(() => pollStatus(qaCallId), POLL_INTERVAL_MS);
}

async function pollStatus(qaCallId) {
  pollAttempts++;
  if (pollAttempts > MAX_POLL_ATTEMPTS) {
    clearInterval(pollTimer);
    showError('Timeout: transcription took too long. Please check the QA dashboard for status.');
    submitBtn.disabled = false;
    return;
  }

  const swapParam = document.getElementById('qu-swap').checked ? '?swap_speakers=1' : '';

  try {
    const res  = await fetch(`/qa/api/transcription/${qaCallId}/status${swapParam}`);
    const data = await res.json();

    if (!data.success && data.status !== 'completed') {
      if (data.status === 'failed' || data.status === 'error' || data.status === 'scoring_failed') {
        clearInterval(pollTimer);
        showError(data.message || 'Processing failed.');
        submitBtn.disabled = false;
        return;
      }
    }

    // Update progress based on status
    if (data.status === 'queued') {
      showProgress(35, 'Queued at AssemblyAI…');
    } else if (data.status === 'processing') {
      const pct = Math.min(30 + pollAttempts * 2, 75);
      showProgress(pct, `Transcribing call… (attempt ${pollAttempts})`);
    } else if (data.status === 'completed') {
      clearInterval(pollTimer);
      showProgress(100, 'Done! Rendering result…');
      setTimeout(() => {
        hideProgress();
        renderResult(data);
        submitBtn.disabled = false;
      }, 600);
    }

  } catch (err) {
    // Network hiccup — keep polling
    console.warn('[QA:Poll] fetch error', err.message);
  }
}

// ── Render result ────────────────────────────────────────────────

function renderResult(data) {
  const r = data.result;
  if (!r) return;

  // Score
  const scoreEl = document.getElementById('res-score');
  scoreEl.textContent = r.total_score ?? '—';
  scoreEl.className   = 'qu-score-num ' + scoreClass(r.total_score);

  // Disposition badge
  const dispBadge = document.getElementById('res-disp-badge');
  dispBadge.textContent  = r.disposition?.replace(/_/g, ' ') ?? '—';
  dispBadge.className    = 'qu-disp-badge disp-' + (r.disposition ?? '');

  // Compliance
  const compBadge = document.getElementById('res-comp-badge');
  if (r.compliance_pass) {
    compBadge.innerHTML = '<span class="comp-badge comp-pass"><i class="bx bx-check"></i> Compliance PASS</span>';
  } else {
    compBadge.innerHTML = '<span class="comp-badge comp-fail"><i class="bx bx-x"></i> Compliance FAIL</span>';
  }

  // Sale badge
  const saleBadge = document.getElementById('res-sale-badge');
  if (r.is_sale) {
    const premium = r.monthly_premium ? ` · $${parseFloat(r.monthly_premium).toFixed(2)}/mo` : '';
    saleBadge.innerHTML = `<span style="font-size:.66rem;color:var(--qa-green);font-weight:700;">💰 SALE${premium}</span>`;
  } else {
    saleBadge.innerHTML = '';
  }

  // Score breakdown bars (max per category: 10 pts each out of 10)
  const breakdown = r.score_breakdown || {};
  const catMaxes  = { opening:10, discovery:10, presentation:10, objection_handling:10, closing:10, soft_skills:10, call_control:10 };
  const catLabels = {
    opening:'Opening', discovery:'Discovery', presentation:'Presentation',
    objection_handling:'Objections', closing:'Closing', soft_skills:'Soft Skills', call_control:'Call Control'
  };
  let bHtml = '';
  for (const [key, maxPts] of Object.entries(catMaxes)) {
    const val = breakdown[key] ?? 0;
    const pct = Math.min((val / maxPts) * 100, 100);
    bHtml += `
      <div class="qu-bar-row">
        <span class="qu-bar-label">${catLabels[key]}</span>
        <div class="qu-bar-outer"><div class="qu-bar-inner" style="width:${pct}%;"></div></div>
        <span class="qu-bar-val">${val}</span>
      </div>`;
  }
  document.getElementById('res-breakdown').innerHTML = bHtml;

  // Compliance checks
  const checks  = r.compliance_checks || {};
  const checkLabels = {
    C1_agent_identity:'Agent Identity', C2_carrier_named:'Carrier Named',
    C3_product_type_stated:'Product Type', C4_health_questions_complete:'Health Questions',
    C5_quote_and_coverage:'Quote & Coverage', C6_draft_date_confirmed:'Draft Date',
    C7_end_of_call_consent:'End-of-Call Consent', C8_application_info_collected:'App Info',
    C9_customer_not_on_dnc:'Not on DNC', C10_agent_handles_objections:'Handles Objections',
    C11_appropriate_language:'Appropriate Language',
  };
  let cHtml = '';
  for (const [code, lbl] of Object.entries(checkLabels)) {
    const val = checks[code];
    const dot = val === true ? 'dot-pass' : (val === false ? 'dot-fail' : 'dot-na');
    const txt = val === true ? 'pass' : (val === false ? 'fail' : 'n/a');
    cHtml += `<div class="qu-check-item"><span class="qu-check-dot ${dot}"></span><span>${lbl} <span style="opacity:.6;">(${txt})</span></span></div>`;
  }
  document.getElementById('res-checks').innerHTML = cHtml;

  // Compliance violations
  const violations = r.compliance_failures || [];
  if (violations.length) {
    document.getElementById('res-violations').textContent = '⚠ Failures: ' + violations.join(', ');
  }

  // Coaching notes
  document.getElementById('res-coaching').textContent = r.coaching_notes || '—';
  document.getElementById('res-top-issue').textContent = r.top_issue   || '—';
  document.getElementById('res-customer').textContent  = r.customer_name || '—';
  document.getElementById('res-carrier').textContent   = r.carrier_name  || '—';

  // Link to full call detail
  const detailLink = document.getElementById('res-detail-link');
  detailLink.href  = `/qa/scoring?call=${data.qa_call_id}`;

  // Show result panel
  document.getElementById('qu-result').classList.add('show');
  document.getElementById('qu-result').scrollIntoView({ behavior:'smooth', block:'nearest' });
}

// ── Helpers ──────────────────────────────────────────────────────

function scoreClass(score) {
  if (!score) return '';
  if (score >= 90) return 'excellent';
  if (score >= 75) return 'good';
  if (score >= 60) return 'average';
  return 'poor';
}

function showProgress(pct, msg) {
  const wrap = document.getElementById('qu-progress-wrap');
  const bar  = document.getElementById('qu-progress-bar');
  const txt  = document.getElementById('qu-status-msg');
  wrap.classList.add('show');
  bar.style.width = pct + '%';
  bar.classList.toggle('striped', pct < 100);
  txt.textContent = msg;
  txt.classList.remove('error');
}

function showError(msg) {
  const wrap = document.getElementById('qu-progress-wrap');
  const bar  = document.getElementById('qu-progress-bar');
  const txt  = document.getElementById('qu-status-msg');
  wrap.classList.add('show');
  bar.style.width = '100%';
  bar.classList.remove('striped');
  bar.style.background = 'var(--qa-red)';
  txt.textContent = '✕ ' + msg;
  txt.classList.add('error');
}

function hideProgress() {
  document.getElementById('qu-progress-wrap').classList.remove('show');
}

function hideResult() {
  document.getElementById('qu-result').classList.remove('show');
}

function resetForm() {
  clearInterval(pollTimer);
  selectedFile    = null;
  activeQaCallId  = null;
  pollAttempts    = 0;

  fileInput.value         = '';
  fileLabel.style.display = 'none';
  fileLabel.innerHTML     = '';
  submitBtn.disabled      = true;

  // Restore dropzone to default prompt
  document.getElementById('qu-dz-icon').style.display  = '';
  document.getElementById('qu-dz-label').style.display = '';

  document.getElementById('qu-progress-bar').style.background = '';
  hideProgress();
  hideResult();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/qa/upload.blade.php ENDPATH**/ ?>