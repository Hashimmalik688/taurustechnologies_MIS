@extends('layouts.master')

@section('title', 'QA — Upload & Score Call')

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

/* ── Transcript panel ── */
.qu-transcript { color:var(--bs-body-color,#e0e0e0); }
.qu-transcript .t-line { padding:.15rem 0; border-bottom:1px solid rgba(255,255,255,.03); }
.qu-transcript .t-speaker { font-weight:700; margin-right:.35rem; }
.qu-transcript .t-agent    { color:var(--qa-blue); }
.qu-transcript .t-customer { color:var(--qa-gold); }
.qu-toggle-icon { display:inline-block; transition:transform .2s; }
.qu-toggle-icon.open { transform:rotate(0deg); }
.qu-toggle-icon:not(.open) { transform:rotate(-90deg); }
.collapsed { display:none; }

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
.qu-checks { display:grid; grid-template-columns:1fr; gap:.25rem; }
.qu-check-item { display:flex; align-items:flex-start; gap:.45rem; font-size:.7rem; }
.qu-check-dot  { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.dot-pass { background:var(--qa-green); }
.dot-fail { background:var(--qa-red); }
.dot-na   { background:rgba(255,255,255,.2); }

/* Coaching notes */
.qu-coaching { background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.07); border-radius:.4rem; padding:.7rem .85rem; font-size:.73rem; line-height:1.65; white-space:pre-wrap; }

/* View full detail link */
.qu-detail-link { font-size:.72rem; color:var(--qa-blue); text-decoration:none; }
.qu-detail-link:hover { text-decoration:underline; }

/* ── Part 2 audio add-on ── */
.qu-part2-wrap { margin-top:.65rem; }
.qu-part2-btn {
  display:inline-flex; align-items:center; gap:.35rem;
  font-size:.72rem; font-weight:600; color:var(--qa-muted);
  background:rgba(255,255,255,.04); border:1px dashed rgba(255,255,255,.13);
  border-radius:.4rem; padding:.35rem .75rem; cursor:pointer;
  transition:border-color .15s, color .15s;
}
.qu-part2-btn:hover { border-color:rgba(85,110,230,.4); color:var(--qa-blue); }
.qu-part2-zone {
  display:none; margin-top:.5rem;
  border:1px dashed rgba(85,110,230,.3); border-radius:.4rem;
  padding:.75rem 1rem; cursor:pointer;
  transition:border-color .15s, background .15s;
  font-size:.73rem; color:var(--qa-muted);
}
.qu-part2-zone:hover { border-color:var(--qa-blue); background:var(--qa-blue-dim); }
.qu-part2-zone.has-file { border-color:rgba(52,195,143,.4); color:var(--qa-green); }
.qu-part2-clear { font-size:.65rem; color:var(--qa-red); cursor:pointer; margin-left:.5rem; opacity:.7; }
.qu-part2-clear:hover { opacity:1; }

/* ── More parts (Part 3+) ── */
.qu-more-parts-toggle { margin-top:.55rem; }
.qu-extra-parts-wrap { margin-top:.5rem; }
.qu-extra-part-item {
  display:flex; align-items:center; gap:.55rem; flex-wrap:wrap;
  border:1px dashed rgba(85,110,230,.3); border-radius:.4rem;
  padding:.6rem .85rem; margin-bottom:.4rem; font-size:.73rem; color:var(--qa-muted);
  cursor:pointer; transition:border-color .15s, background .15s;
}
.qu-extra-part-item:hover { border-color:var(--qa-blue); background:var(--qa-blue-dim); }
.qu-extra-part-item.has-file { border-color:rgba(52,195,143,.4); color:var(--qa-green); cursor:default; }
.qu-extra-part-label { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.qu-extra-part-remove { font-size:.65rem; color:var(--qa-red); cursor:pointer; opacity:.7; flex-shrink:0; padding:.1rem .25rem; }
.qu-extra-part-remove:hover { opacity:1; }
.qu-add-extra-btn {
  display:inline-flex; align-items:center; gap:.35rem;
  font-size:.72rem; font-weight:600; color:var(--qa-muted);
  background:rgba(255,255,255,.04); border:1px dashed rgba(255,255,255,.13);
  border-radius:.4rem; padding:.35rem .75rem; cursor:pointer;
  transition:border-color .15s, color .15s;
}
.qu-add-extra-btn:hover { border-color:rgba(85,110,230,.4); color:var(--qa-blue); }

/* ── Sale Picker ── */
.qu-sale-picker { display:none; }
.qu-sale-picker.show { display:block; }
.qu-sale-list { max-height:260px; overflow-y:auto; display:flex; flex-direction:column; gap:.35rem; }
.qu-sale-item {
  display:grid; grid-template-columns:auto 1fr auto; gap:.5rem .75rem; align-items:center;
  padding:.55rem .75rem; border-radius:.4rem; cursor:pointer;
  border:1px solid rgba(255,255,255,.07); background:rgba(255,255,255,.02);
  transition:border-color .15s, background .15s; font-size:.73rem;
}
.qu-sale-item:hover { border-color:rgba(85,110,230,.35); background:rgba(85,110,230,.04); }
.qu-sale-item.selected { border-color:var(--qa-blue); background:rgba(85,110,230,.08); box-shadow:0 0 0 1px var(--qa-blue); }
.qu-sale-item .sale-radio { width:15px; height:15px; accent-color:var(--qa-blue); cursor:pointer; }
.qu-sale-item .sale-info { display:flex; flex-wrap:wrap; gap:.15rem .6rem; }
.qu-sale-item .sale-name { font-weight:600; color:var(--bs-body-color,#e0e0e0); }
.qu-sale-item .sale-meta { font-size:.65rem; color:var(--qa-muted); }
.qu-sale-item .sale-right { text-align:right; white-space:nowrap; }
.qu-sale-item .sale-premium { font-weight:700; color:var(--qa-green); font-size:.75rem; }
.qu-sale-item .sale-carrier { font-size:.6rem; color:var(--qa-muted); }
.qu-sale-item .sale-qa-badge { font-size:.55rem; font-weight:700; padding:.08rem .3rem; border-radius:.5rem; display:inline-block; margin-top:.15rem; }
.qu-sale-empty { text-align:center; padding:1.5rem 1rem; color:var(--qa-muted); font-size:.75rem; }
.qu-sale-loading { text-align:center; padding:1rem; color:var(--qa-muted); font-size:.72rem; }
.qu-linked-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.66rem; font-weight:700; padding:.18rem .55rem; border-radius:.8rem; background:rgba(85,110,230,.12); color:var(--qa-blue); border:1px solid rgba(85,110,230,.3); }

</style>
@endsection

@section('content')
<div class="qu-wrap">

  {{-- Header --}}
  <div class="qu-header">
    <h1 class="qu-title">
      <i class="bx bxs-microphone"></i>
      Upload &amp; Score Call
      <span class="qu-badge">AssemblyAI + Claude</span>
    </h1>
    <div class="d-flex gap-2">
      <a href="{{ route('qa.manual') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.72rem;">
        <i class="bx bx-text me-1"></i>Paste Transcript
      </a>
      <a href="{{ route('qa.scoring') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.72rem;">
        <i class="bx bx-bar-chart-alt-2 me-1"></i>Dashboard
      </a>
    </div>
  </div>

  {{-- Pipeline info strip --}}
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

  {{-- Upload form --}}
  <div class="qu-card">
    <div class="qu-card-hdr">
      <h6><i class="bx bx-upload"></i> Audio File</h6>
      <span style="font-size:.65rem;color:var(--qa-muted);">MP3, WAV, M4A, MP4, OGG, FLAC, AAC · Max 50 MB</span>
    </div>
    <div class="qu-card-body">

      {{-- Controls row --}}
      <div class="qu-controls">
        <div class="qu-field">
          <label>Agent</label>
          <select id="qu-agent">
            <option value="">— Unknown / unassigned —</option>
            @foreach($agents as $agent)
              <option value="{{ $agent->id }}">{{ $agent->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="qu-field">
          <label>Call Date</label>
          <input type="date" id="qu-date" value="{{ now()->toDateString() }}">
        </div>
        <div class="qu-field" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:.1rem;">
          <label for="qu-swap" class="qu-toggle" style="margin-bottom:.3rem;">
            <input type="checkbox" id="qu-swap">
            <span>Swap Speakers<br><span style="font-size:.6rem;opacity:.6;">Use if Customer speaks first</span></span>
          </label>
        </div>
      </div>

      {{-- Sale Picker — appears when agent + date are set --}}
      <div class="qu-sale-picker" id="qu-sale-picker">
        <div class="qu-card" style="margin-bottom:.75rem;">
          <div class="qu-card-hdr">
            <h6><i class="bx bx-link-alt"></i> Link to Sale</h6>
            <span id="qu-sale-count" style="font-size:.6rem;color:var(--qa-muted);"></span>
          </div>
          <div class="qu-card-body">
            <p style="font-size:.68rem;color:var(--qa-muted);margin:0 0 .5rem;">
              Select a sale to link this QA call. The QA status will be auto-set on the sale after scoring.
            </p>
            <div class="qu-sale-list" id="qu-sale-list">
              {{-- Populated by JS --}}
            </div>
          </div>
        </div>
      </div>

      {{-- Hidden file inputs --}}
      <input type="file" id="qu-file"  accept=".mp3,.wav,.m4a,.mp4,.ogg,.webm,.flac,.aac" style="display:none;" />
      <input type="file" id="qu-file2" accept=".mp3,.wav,.m4a,.mp4,.ogg,.webm,.flac,.aac" style="display:none;" />
      {{-- Extra parts dynamic file inputs are injected by JS --}}

      {{-- Drop zone (Part 1) --}}
      <div class="qu-dropzone" id="qu-dropzone" onclick="document.getElementById('qu-file').click()">
        <i class="bx bx-cloud-upload" id="qu-dz-icon"></i>
        <p class="qu-dz-label" id="qu-dz-label">
          <strong>Click to browse</strong> or drag &amp; drop your audio file here
        </p>
        <p class="qu-file-name" id="qu-file-name" style="display:none;"></p>
      </div>

      {{-- Part 2 add-on (shown after Part 1 is selected) --}}
      <div class="qu-part2-wrap" id="qu-part2-wrap" style="display:none;">
        <div class="qu-part2-btn" id="qu-part2-btn" onclick="document.getElementById('qu-file2').click()">
          <i class="bx bx-plus-circle"></i> Add Part 2 recording <span style="font-size:.6rem;opacity:.6;">(optional — if call was disconnected &amp; reconnected)</span>
        </div>
        <div class="qu-part2-zone" id="qu-part2-zone" style="display:none;">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;flex-wrap:wrap;">
            <div onclick="document.getElementById('qu-file2').click()" style="display:flex;align-items:center;gap:.4rem;cursor:pointer;flex:1;">
              <i class="bx bx-check-circle" style="margin-right:.2rem;"></i>
              <span id="qu-part2-name"></span>
            </div>
            <label class="qu-toggle" style="font-size:.65rem;cursor:pointer;white-space:nowrap;" title="Use if Customer speaks first in Part 2">
              <input type="checkbox" id="qu-swap2">
              <span>Swap Speakers P2<br><span style="font-size:.58rem;opacity:.6;">Customer speaks first</span></span>
            </label>
            <span class="qu-part2-clear" onclick="clearPart2(event)" title="Remove Part 2">✕</span>
          </div>
        </div>

        {{-- "More parts" checkbox — visible once Part 1 is picked --}}
        <div class="qu-more-parts-toggle" id="qu-more-parts-toggle">
          <label class="qu-toggle" style="cursor:pointer; font-size:.72rem;">
            <input type="checkbox" id="qu-has-more-parts" onchange="toggleMoreParts(this.checked)">
            <span>This call has more than 2 parts <span style="font-size:.6rem;opacity:.6;">(Part 3, 4, 5…)</span></span>
          </label>
        </div>

        {{-- Dynamic extra parts container --}}
        <div class="qu-extra-parts-wrap" id="qu-extra-parts-wrap" style="display:none;">
          <div id="qu-extra-parts-list"></div>
          <button type="button" class="qu-add-extra-btn" id="qu-add-extra-btn" onclick="addExtraPart()">
            <i class="bx bx-plus-circle"></i> Add Part <span id="qu-next-part-num">3</span>
          </button>
        </div>
      </div>
      <div class="qu-progress-wrap" id="qu-progress-wrap">
        <div class="qu-progress-bar-outer">
          <div class="qu-progress-bar-inner striped" id="qu-progress-bar" style="width:30%;"></div>
        </div>
        <p class="qu-status-msg" id="qu-status-msg">Uploading audio to AssemblyAI…</p>
      </div>

      {{-- Actions --}}
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

  {{-- Scored result panel (hidden until scoring completes) --}}
  <div class="qu-result" id="qu-result">

    <div class="row g-2">

      {{-- Score hero --}}
      <div class="col-md-3">
        <div class="qu-card h-100">
          <div class="qu-card-hdr"><h6><i class="bx bx-trophy"></i> Score</h6></div>
          <div class="qu-card-body qu-score-hero">
            <div class="qu-score-num" id="res-score">—</div>
            <div class="qu-score-label">Total Score</div>
            <div id="res-disp-badge" class="qu-disp-badge mt-1"></div>
            <div id="res-comp-badge" class="mt-2"></div>
            <div id="res-sale-badge" class="mt-1"></div>
            <div id="res-linked-sale" class="mt-2"></div>
          </div>
        </div>
      </div>

      {{-- Score breakdown --}}
      <div class="col-md-5">
        <div class="qu-card h-100">
          <div class="qu-card-hdr"><h6><i class="bx bx-bar-chart-alt-2"></i> Score Breakdown</h6></div>
          <div class="qu-card-body">
            <div class="qu-breakdown" id="res-breakdown"></div>
          </div>
        </div>
      </div>

      {{-- Compliance checks --}}
      <div class="col-md-4">
        <div class="qu-card h-100">
          <div class="qu-card-hdr"><h6><i class="bx bx-shield-check"></i> Compliance</h6></div>
          <div class="qu-card-body">
            <div class="qu-checks" id="res-checks"></div>
            <div id="res-violations" class="mt-2" style="font-size:.68rem;color:var(--qa-red);"></div>
          </div>
        </div>
      </div>

      {{-- Coaching notes --}}
      <div class="col-12">
        <div class="qu-card">
          <div class="qu-card-hdr">
            <h6><i class="bx bx-comment-dots"></i> AI Coaching Notes</h6>
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

      {{-- Transcript --}}
      <div class="col-12">
        <div class="qu-card">
          <div class="qu-card-hdr" style="cursor:pointer;" onclick="document.getElementById('res-transcript-body').classList.toggle('collapsed'); this.querySelector('.qu-toggle-icon').classList.toggle('open');">
            <h6><i class="bx bx-file"></i> Transcript <span id="res-transcript-lines" style="font-weight:400;opacity:.6;"></span></h6>
            <span class="qu-toggle-icon open" style="font-size:.7rem;color:var(--qa-muted);transition:transform .2s;">▼</span>
          </div>
          <div class="qu-card-body" id="res-transcript-body">
            <div class="qu-transcript" id="res-transcript" style="max-height:500px;overflow-y:auto;font-size:.75rem;line-height:1.65;white-space:pre-wrap;font-family:'Fira Code',monospace,Consolas,monospace;"></div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@section('script')
<script>
// ─────────────────────────────────────────────────────────────────
// QA Upload & Score — frontend logic
// Flow: pick file → upload + submit to AssemblyAI → poll status → display result
// ─────────────────────────────────────────────────────────────────

const POLL_INTERVAL_MS  = 5000; // 5 s between status polls
const MAX_POLL_ATTEMPTS = 180;  // 15 min max wait

let selectedFile  = null;
let selectedFile2 = null;
let extraParts    = []; // array of { file, swapSpeakers } — Parts 3, 4, 5…
let pollTimer     = null;
let pollAttempts  = 0;
let activeQaCallId = null;
let selectedLeadId = null; // Sale linked to this QA call

// ── Sale picker — fetch sales when agent + date change ───────────

const agentSelect = document.getElementById('qu-agent');
const dateInput   = document.getElementById('qu-date');
const salePicker  = document.getElementById('qu-sale-picker');
const saleList    = document.getElementById('qu-sale-list');
const saleCount   = document.getElementById('qu-sale-count');

agentSelect.addEventListener('change', fetchCloserSales);
dateInput.addEventListener('change', fetchCloserSales);

async function fetchCloserSales() {
  const agentId = agentSelect.value;
  const date    = dateInput.value;
  selectedLeadId = null;

  if (!agentId || !date) {
    salePicker.classList.remove('show');
    return;
  }

  salePicker.classList.add('show');
  saleList.innerHTML = '<div class="qu-sale-loading"><i class="bx bx-loader-alt bx-spin"></i> Loading sales…</div>';
  saleCount.textContent = '';

  try {
    const res  = await fetch(`/qa/api/closer-sales?agent_user_id=${encodeURIComponent(agentId)}&date=${encodeURIComponent(date)}`);
    const data = await res.json();

    if (!data.success || !data.sales || !data.sales.length) {
      saleList.innerHTML = '<div class="qu-sale-empty"><i class="bx bx-info-circle" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i>No sales found for this closer around this date.</div>';
      saleCount.textContent = '0 sales';
      return;
    }

    saleCount.textContent = data.sales.length + ' sale' + (data.sales.length !== 1 ? 's' : '');
    let html = '';
    for (const sale of data.sales) {
      const qaClass = sale.qa_status === 'Good' ? 'background:var(--qa-green-dim);color:var(--qa-green);' :
                      sale.qa_status === 'Bad'  ? 'background:var(--qa-red-dim);color:var(--qa-red);' :
                      sale.qa_status === 'Avg'  ? 'background:rgba(241,180,76,.1);color:var(--qa-warn);' : '';
      const qaBadge = sale.qa_status ? `<span class="qu-sale-item sale-qa-badge" style="${qaClass}">${escHtml(sale.qa_status)}</span>` : '';

      html += `
        <label class="qu-sale-item" data-lead-id="${sale.id}" onclick="selectSale(this, ${sale.id})">
          <input type="radio" name="sale_pick" class="sale-radio" value="${sale.id}">
          <div class="sale-info">
            <span class="sale-name">${escHtml(sale.cn_name)}</span>
            ${sale.phone ? `<span class="sale-meta">···${escHtml(sale.phone)}</span>` : ''}
            <span class="sale-meta">${escHtml(sale.sale_date)}</span>
            <span class="sale-meta">${escHtml(sale.coverage)}</span>
            <span class="sale-meta">${escHtml(sale.state)}</span>
            ${qaBadge}
          </div>
          <div class="sale-right">
            <div class="sale-premium">${escHtml(sale.premium)}</div>
            <div class="sale-carrier">${escHtml(sale.carrier)}</div>
          </div>
        </label>`;
    }
    saleList.innerHTML = html;

  } catch (err) {
    saleList.innerHTML = `<div class="qu-sale-empty" style="color:var(--qa-red);">Error loading sales: ${escHtml(err.message)}</div>`;
  }
}

function selectSale(el, leadId) {
  selectedLeadId = leadId;
  // Visual: highlight selected
  saleList.querySelectorAll('.qu-sale-item').forEach(i => i.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('.sale-radio').checked = true;
}

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

// Part 2 file input
const fileInput2 = document.getElementById('qu-file2');
fileInput2.addEventListener('change', () => {
  if (fileInput2.files && fileInput2.files.length) selectFile2(fileInput2.files[0]);
});

function selectFile2(file) {
  selectedFile2 = file;
  const sizeMB = (file.size / 1048576).toFixed(1);
  document.getElementById('qu-part2-btn').style.display  = 'none';
  const zone = document.getElementById('qu-part2-zone');
  zone.style.display = 'block';
  zone.classList.add('has-file');
  document.getElementById('qu-part2-name').textContent = `${file.name} (${sizeMB} MB)`;
}

function clearPart2(e) {
  e.stopPropagation();
  selectedFile2 = null;
  fileInput2.value = '';
  document.getElementById('qu-part2-zone').style.display = 'none';
  document.getElementById('qu-part2-zone').classList.remove('has-file');
  document.getElementById('qu-part2-btn').style.display  = '';
}

// ── Extra Parts (3+) ─────────────────────────────────────────────

function toggleMoreParts(checked) {
  document.getElementById('qu-extra-parts-wrap').style.display = checked ? '' : 'none';
  if (!checked) {
    // Clear any added extra parts
    extraParts = [];
    document.getElementById('qu-extra-parts-list').innerHTML = '';
    updateNextPartNum();
  }
}

function updateNextPartNum() {
  const num = extraParts.length + 3; // e.g. 0 items → next is "3"
  document.getElementById('qu-next-part-num').textContent = num;
}

function addExtraPart() {
  const idx = extraParts.length;
  extraParts.push({ file: null, swapSpeakers: false });

  // Create a hidden file input
  const input = document.createElement('input');
  input.type   = 'file';
  input.accept = '.mp3,.wav,.m4a,.mp4,.ogg,.webm,.flac,.aac';
  input.id     = `qu-extra-file-${idx}`;
  input.style.display = 'none';
  input.addEventListener('change', () => {
    if (input.files && input.files[0]) selectExtraFile(idx, input.files[0]);
  });
  document.body.appendChild(input);

  const partNum = idx + 3;
  const list    = document.getElementById('qu-extra-parts-list');

  const item = document.createElement('div');
  item.className = 'qu-extra-part-item';
  item.id        = `qu-extra-item-${idx}`;
  item.innerHTML = `
    <i class="bx bx-cloud-upload" style="color:var(--qa-blue);flex-shrink:0;"></i>
    <span class="qu-extra-part-label" id="qu-extra-label-${idx}">
      <strong style="color:var(--qa-blue);">Click to browse</strong> — Part ${partNum} audio file
    </span>
    <label class="qu-toggle" style="font-size:.63rem;cursor:pointer;white-space:nowrap;flex-shrink:0;" title="Use if Customer speaks first in Part ${partNum}">
      <input type="checkbox" id="qu-extra-swap-${idx}" onchange="extraParts[${idx}].swapSpeakers=this.checked">
      <span>Swap P${partNum}<br><span style="font-size:.57rem;opacity:.6;">Customer first</span></span>
    </label>
    <span class="qu-extra-part-remove" onclick="clearExtraPart(${idx})" title="Remove Part ${partNum}">✕</span>`;

  // Clicking the item (but not the Swap label or remove button) opens file picker
  item.addEventListener('click', (e) => {
    if (e.target.closest('label') || e.target.closest('.qu-extra-part-remove')) return;
    document.getElementById(`qu-extra-file-${idx}`).click();
  });

  list.appendChild(item);
  updateNextPartNum();
}

function selectExtraFile(idx, file) {
  extraParts[idx].file = file;
  const sizeMB = (file.size / 1048576).toFixed(1);
  const item   = document.getElementById(`qu-extra-item-${idx}`);
  const label  = document.getElementById(`qu-extra-label-${idx}`);
  const partNum = idx + 3;

  item.classList.add('has-file');
  label.innerHTML = `<i class="bx bx-check-circle" style="margin-right:.3rem;"></i>${escHtml(file.name)} <span style="opacity:.6;font-weight:400;">(${sizeMB} MB) — Part ${partNum}</span>`;
}

function clearExtraPart(idx) {
  extraParts[idx] = { file: null, swapSpeakers: false };

  const input = document.getElementById(`qu-extra-file-${idx}`);
  if (input) input.value = '';

  const item  = document.getElementById(`qu-extra-item-${idx}`);
  const label = document.getElementById(`qu-extra-label-${idx}`);
  const partNum = idx + 3;

  item.classList.remove('has-file');
  label.innerHTML = `<strong style="color:var(--qa-blue);">Click to browse</strong> — Part ${partNum} audio file`;
}

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

  // Show Part 2 add-on
  document.getElementById('qu-part2-wrap').style.display = '';

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
  hideResult();

  const formData = new FormData();
  formData.append('audio',         selectedFile);
  if (selectedFile2) formData.append('audio2', selectedFile2);
  // Append Parts 3+ (audio_extra[])
  for (let i = 0; i < extraParts.length; i++) {
    if (extraParts[i] && extraParts[i].file) {
      formData.append('audio_extra[]', extraParts[i].file);
    }
  }
  formData.append('agent_user_id', document.getElementById('qu-agent').value || '');
  formData.append('call_date',     document.getElementById('qu-date').value   || '');
  formData.append('swap_speakers', document.getElementById('qu-swap').checked ? '1' : '0');
  formData.append('_token',        '{{ csrf_token() }}');

  const extraCount = extraParts.filter(p => p && p.file).length;
  const totalParts = 1 + (selectedFile2 ? 1 : 0) + extraCount;
  const partLabel  = totalParts > 1 ? `all ${totalParts} audio files…` : 'audio file…';
  showProgress(10, `Uploading ${partLabel}`);;

  try {
    const res   = await fetch('{{ route("qa.api.upload.transcribe") }}', { method: 'POST', body: formData });
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

  const swap1      = document.getElementById('qu-swap').checked   ? 'swap_speakers=1'   : '';
  const swap2      = document.getElementById('qu-swap2')?.checked ? 'swap_speakers_2=1' : '';
  const swapExtra  = extraParts
    .map((p, i) => `swap_extra[${i}]=${p && p.swapSpeakers ? '1' : '0'}`)
    .join('&');
  const swapParts  = [swap1, swap2, swapExtra].filter(Boolean).join('&');
  const swapParam  = swapParts ? '?' + swapParts : '';

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
  const details = r.compliance_details || {};
  const checkLabels = {
    C1_agent_identity:'Agent Identity', C2_carrier_named:'Carrier Named',
    C3_product_type_stated:'Product Type', C4_health_questions_complete:'Health Questions',
    C5_quote_and_coverage:'Quote & Coverage', C6_draft_date_confirmed:'Draft Date',
    C7_recorded_consent:'Recorded Consent', C8_application_info_collected:'App Info',
    C9_customer_not_on_dnc:'Not on DNC', C10_agent_handles_objections:'Handles Objections',
    C11_appropriate_language:'Appropriate Language',
  };
  // Sort: failures first, then N/A, then passes
  const allKeys = Object.keys(checkLabels);
  const failKeys = allKeys.filter(k => checks[k] === false);
  const naKeys   = allKeys.filter(k => checks[k] === null || checks[k] === undefined);
  const passKeys = allKeys.filter(k => checks[k] === true);
  const sortedKeys = [...failKeys, ...naKeys, ...passKeys];

  let cHtml = '';
  for (const code of sortedKeys) {
    const lbl     = checkLabels[code];
    const val     = checks[code];
    const isFail  = val === false;
    const isPass  = val === true;
    const dot     = isPass ? 'dot-pass' : (isFail ? 'dot-fail' : 'dot-na');
    const txt     = isPass ? 'pass'     : (isFail ? 'fail'     : 'n/a');
    const reason  = details[code] || '';
    const reasonColor = isFail ? 'var(--qa-red)' : isPass ? 'var(--qa-green)' : 'rgba(255,255,255,.35)';
    cHtml += `<div class="qu-check-item" style="flex-direction:column;align-items:flex-start;padding:.35rem .4rem;border-radius:.3rem;${isFail ? 'background:rgba(244,106,106,.06);' : ''}">
      <div style="display:flex;align-items:center;gap:.45rem;width:100%;">
        <span class="qu-check-dot ${dot}" style="flex-shrink:0;margin-top:0;"></span>
        <span style="flex:1;font-weight:${isFail ? '600' : '400'};color:${isFail ? 'var(--qa-red)' : 'inherit'};">${lbl}</span>
        <span style="font-size:.6rem;font-weight:700;padding:.1rem .35rem;border-radius:.6rem;${isPass ? 'background:rgba(52,195,143,.12);color:var(--qa-green);' : isFail ? 'background:rgba(244,106,106,.12);color:var(--qa-red);' : 'background:rgba(255,255,255,.07);color:rgba(255,255,255,.35);'}">${txt}</span>
      </div>
      ${reason ? `<div style="font-size:.63rem;color:${reasonColor};margin-top:.22rem;padding-left:1.05rem;line-height:1.4;">${escHtml(reason)}</div>` : ''}
    </div>`;
  }
  document.getElementById('res-checks').innerHTML = cHtml;

  // Compliance violations summary
  const violations = r.compliance_failures || [];
  if (violations.length) {
    document.getElementById('res-violations').textContent = '⚠ ' + violations.length + ' failure' + (violations.length > 1 ? 's' : '') + ': ' + violations.join(', ');
  }

  // Coaching notes
  document.getElementById('res-coaching').textContent = r.coaching_notes || '—';
  document.getElementById('res-top-issue').textContent = r.top_issue   || '—';
  document.getElementById('res-customer').textContent  = r.customer_name || '—';
  document.getElementById('res-carrier').textContent   = r.carrier_name  || '—';

  // Link to full call detail
  const detailLink = document.getElementById('res-detail-link');
  detailLink.href  = `/qa/scoring?call=${data.qa_call_id}`;

  // Transcript
  const transcript = data.transcript || '';
  const transcriptEl = document.getElementById('res-transcript');
  const linesLabel   = document.getElementById('res-transcript-lines');
  if (transcript) {
    const lines = transcript.split('\n').filter(l => l.trim());
    linesLabel.textContent = `(${lines.length} lines)`;
    let html = '';
    for (const line of lines) {
      const agentMatch = line.match(/^(AGENT:)(.*)/);
      const custMatch  = line.match(/^(CUSTOMER:)(.*)/);
      if (agentMatch) {
        html += `<div class="t-line"><span class="t-speaker t-agent">AGENT:</span>${escHtml(agentMatch[2])}</div>`;
      } else if (custMatch) {
        html += `<div class="t-line"><span class="t-speaker t-customer">CUSTOMER:</span>${escHtml(custMatch[2])}</div>`;
      } else {
        html += `<div class="t-line">${escHtml(line)}</div>`;
      }
    }
    transcriptEl.innerHTML = html;
  } else {
    linesLabel.textContent = '';
    transcriptEl.innerHTML = '<span style="color:var(--qa-muted);font-style:italic;">No transcript available</span>';
  }

  // Show result panel
  document.getElementById('qu-result').classList.add('show');
  document.getElementById('qu-result').scrollIntoView({ behavior:'smooth', block:'nearest' });

  // Auto-link to selected sale
  if (selectedLeadId && data.qa_call_id) {
    linkSaleToCall(data.qa_call_id, selectedLeadId);
  }
}

// ── Link sale to QA call ─────────────────────────────────────────

async function linkSaleToCall(qaCallId, leadId) {
  const linkedEl = document.getElementById('res-linked-sale');
  linkedEl.innerHTML = '<span style="font-size:.65rem;color:var(--qa-muted);"><i class="bx bx-loader-alt bx-spin"></i> Linking to sale…</span>';

  try {
    const res  = await fetch(`/qa/api/calls/${qaCallId}/link-sale`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ lead_id: leadId }),
    });
    const data = await res.json();

    if (data.success) {
      const statusColor = data.qa_status === 'Good' ? 'var(--qa-green)' :
                          data.qa_status === 'Bad'  ? 'var(--qa-red)' :
                          data.qa_status === 'Avg'  ? 'var(--qa-warn)' : 'var(--qa-muted)';
      linkedEl.innerHTML = `
        <span class="qu-linked-badge"><i class="bx bx-link-alt"></i> Linked to ${escHtml(data.cn_name || 'Sale')}</span>
        <div style="font-size:.6rem;color:${statusColor};font-weight:600;margin-top:.2rem;">QA: ${escHtml(data.qa_status)}</div>
        <div style="font-size:.55rem;color:var(--qa-muted);margin-top:.1rem;max-width:200px;line-height:1.35;">${escHtml(data.qa_reason || '')}</div>`;

      // Update the sale picker item to show new QA status
      const saleItem = saleList.querySelector(`[data-lead-id="${leadId}"]`);
      if (saleItem) {
        saleItem.style.borderColor = statusColor;
      }
    } else {
      linkedEl.innerHTML = `<span style="font-size:.65rem;color:var(--qa-red);"><i class="bx bx-x-circle"></i> ${escHtml(data.message || 'Link failed')}</span>`;
    }
  } catch (err) {
    linkedEl.innerHTML = `<span style="font-size:.65rem;color:var(--qa-red);"><i class="bx bx-x-circle"></i> ${escHtml(err.message)}</span>`;
  }
}

// ── Helpers ──────────────────────────────────────────────────────

function scoreClass(score) {
  if (!score) return '';
  if (score >= 90) return 'excellent';
  if (score >= 75) return 'good';
  if (score >= 60) return 'average';
  return 'poor';
}

function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
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
  selectedFile2   = null;
  extraParts      = [];
  activeQaCallId  = null;
  selectedLeadId  = null;
  pollAttempts    = 0;

  fileInput.value          = '';
  fileInput2.value         = '';
  fileLabel.style.display  = 'none';
  fileLabel.innerHTML      = '';
  submitBtn.disabled       = true;

  // Restore dropzone to default prompt
  document.getElementById('qu-dz-icon').style.display  = '';
  document.getElementById('qu-dz-label').style.display = '';

  // Hide Part 2 add-on
  document.getElementById('qu-part2-wrap').style.display = 'none';
  document.getElementById('qu-part2-zone').style.display = 'none';
  document.getElementById('qu-part2-zone').classList.remove('has-file');
  document.getElementById('qu-part2-btn').style.display  = '';

  // Reset "more parts" checkbox + extra parts
  const moreCheck = document.getElementById('qu-has-more-parts');
  if (moreCheck) moreCheck.checked = false;
  document.getElementById('qu-extra-parts-wrap').style.display = 'none';
  document.getElementById('qu-extra-parts-list').innerHTML = '';
  updateNextPartNum();
  // Remove dynamic file inputs added for extra parts
  document.querySelectorAll('[id^="qu-extra-file-"]').forEach(el => el.remove());

  // Reset sale picker
  salePicker.classList.remove('show');
  saleList.innerHTML = '';
  saleCount.textContent = '';

  // Reset linked sale badge
  document.getElementById('res-linked-sale').innerHTML = '';

  document.getElementById('qu-progress-bar').style.background = '';
  hideProgress();
  hideResult();
}
</script>
@endsection
