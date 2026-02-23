@extends('layouts.master')

@section('title', 'Chat & Note Shadowing')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Chat Shadowing — Executive Dashboard Style
   ═══════════════════════════════════════════════════ */

/* Glass-card base (matching dashboard) */
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

/* Section card base */
.sec-card { padding: 0; overflow: hidden; }
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
.bd-mini.bd-gold  { background: rgba(212,175,55,.12); color: #b89730; }
.bd-mini.bd-blue  { background: rgba(85,110,230,.12); color: #556ee6; }
.bd-mini.bd-green { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-mini.bd-red   { background: rgba(244,106,106,.12); color: #c84646; }

/* Link button */
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
.link-btn:hover { border-color: var(--bs-gold, #d4af37); color: var(--bs-gold, #d4af37); }

/* ── KPI row ── */
.kpi-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.kpi-card {
    flex: 1 1 100px;
    min-width: 90px;
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
.kpi-card .k-val  { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.kpi-card .k-lbl  {
    font-size: 0.58rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 0.2rem;
}
.kpi-card.k-gold   { background: rgba(212,175,55,.06); }
.kpi-card.k-gold::before   { background: linear-gradient(90deg, #d4af37, #e8c84a); }
.kpi-card.k-gold .k-val, .kpi-card.k-gold .k-icon { color: #b89730; }
.kpi-card.k-blue   { background: rgba(85,110,230,.06); }
.kpi-card.k-blue::before   { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.kpi-card.k-blue .k-val, .kpi-card.k-blue .k-icon { color: #556ee6; }
.kpi-card.k-green  { background: rgba(52,195,143,.06); }
.kpi-card.k-green::before  { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.kpi-card.k-green .k-val, .kpi-card.k-green .k-icon { color: #1a8754; }
.kpi-card.k-red    { background: rgba(244,106,106,.06); }
.kpi-card.k-red::before    { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.kpi-card.k-red .k-val, .kpi-card.k-red .k-icon { color: #c84646; }
.kpi-card.k-purple { background: rgba(124,105,239,.06); }
.kpi-card.k-purple::before { background: linear-gradient(90deg, #7c69ef, #a899f5); }
.kpi-card.k-purple .k-val, .kpi-card.k-purple .k-icon { color: #5b49c7; }

/* ── Page Header ── */
.cs-page-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.65rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.cs-page-hdr h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.cs-page-hdr h5 i { color: var(--bs-gold, #d4af37); }
.cs-page-hdr .cs-sub {
    font-size: 0.72rem;
    color: var(--bs-surface-500);
    margin-left: 0.2rem;
}
.cs-live-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #34c38f;
    display: inline-block;
    margin-left: 6px;
    animation: cs-pulse 2s infinite;
}
@keyframes cs-pulse {
    0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(52,195,143,.4); }
    50% { opacity: .7; box-shadow: 0 0 0 4px rgba(52,195,143,0); }
}

/* ── Filters bar ── */
.cs-filters {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 0.65rem;
}
.cs-filters .form-control,
.cs-filters .form-select {
    font-size: 0.72rem;
    padding: 0.3rem 0.5rem;
    border-radius: 0.4rem;
    border: 1px solid var(--bs-surface-200);
    background: var(--bs-card-bg);
    max-width: 170px;
    height: 30px;
}
.cs-filters .form-control:focus,
.cs-filters .form-select:focus {
    border-color: var(--bs-gold, #d4af37);
    box-shadow: 0 0 0 2px rgba(212,175,55,.1);
}

/* ── Two Panel ── */
.cs-layout {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 0.65rem;
    height: calc(100vh - 230px);
    min-height: 480px;
}
@media (max-width: 992px) {
    .cs-layout { grid-template-columns: 1fr; height: auto; }
    .cs-conv-panel { max-height: 320px; }
    .cs-msg-panel { min-height: 420px; }
}

/* ── Conversation Panel ── */
.cs-conv-panel { display: flex; flex-direction: column; overflow: hidden; }
.cs-conv-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 0.35rem;
}
.cs-conv-scroll::-webkit-scrollbar { width: 3px; }
.cs-conv-scroll::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

.cs-conv {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.45rem 0.55rem;
    border-radius: 0.45rem;
    cursor: pointer;
    transition: all .12s;
    border: 1px solid transparent;
    margin-bottom: 2px;
}
.cs-conv:hover { background: rgba(0,0,0,.02); }
.cs-conv.active {
    background: rgba(212,175,55,.06);
    border-color: rgba(212,175,55,.15);
}
.cs-conv.deleted-conv {
    background: rgba(244,106,106,.03);
}
.cs-conv.deleted-conv .cs-conv-name {
    text-decoration: line-through;
    opacity: .65;
}
.cs-conv-av {
    width: 34px; height: 34px;
    border-radius: 0.45rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.65rem;
    color: #fff;
    flex-shrink: 0;
}
.cs-conv-av.t-direct { background: linear-gradient(135deg, #667eea, #764ba2); }
.cs-conv-av.t-group  { background: linear-gradient(135deg, #d4af37, #b8860b); }
.cs-conv-body { flex: 1; min-width: 0; }
.cs-conv-name {
    font-weight: 600;
    font-size: 0.75rem;
    color: var(--bs-body-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cs-conv-preview {
    font-size: 0.65rem;
    color: var(--bs-surface-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}
.cs-conv-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
    flex-shrink: 0;
}
.cs-conv-time {
    font-size: 0.58rem;
    color: var(--bs-surface-500);
}
.cs-conv-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--bs-surface-500);
    font-size: 0.78rem;
}
.cs-conv-empty i { font-size: 2rem; display: block; margin-bottom: 0.4rem; opacity: .25; }

.cs-conv-pag {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem;
    border-top: 1px solid rgba(0,0,0,.04);
    font-size: 0.62rem;
    color: var(--bs-surface-500);
}

/* ── Message Panel ── */
.cs-msg-panel { display: flex; flex-direction: column; overflow: hidden; }
.cs-msg-hdr {
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.cs-msg-hdr h6 {
    margin: 0;
    font-size: 0.82rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.cs-msg-hdr small {
    font-size: 0.62rem;
    color: var(--bs-surface-500);
}
.cs-msg-toolbar {
    display: flex;
    gap: 0.35rem;
    align-items: center;
    flex-wrap: wrap;
}
.cs-msg-toolbar .form-control,
.cs-msg-toolbar .form-select {
    font-size: 0.68rem;
    padding: 0.22rem 0.4rem;
    border-radius: 0.35rem;
    border: 1px solid var(--bs-surface-200);
    height: 26px;
    max-width: 120px;
}
.cs-msg-toolbar .form-control:focus,
.cs-msg-toolbar .form-select:focus {
    border-color: var(--bs-gold, #d4af37);
    box-shadow: 0 0 0 2px rgba(212,175,55,.08);
}
.cs-msg-toolbar .btn-filter {
    padding: 0.2rem 0.45rem;
    border-radius: 0.35rem;
    border: 1px solid var(--bs-surface-200);
    background: transparent;
    color: var(--bs-surface-500);
    font-size: 0.68rem;
    cursor: pointer;
    transition: all .12s;
}
.cs-msg-toolbar .btn-filter:hover { border-color: var(--bs-gold, #d4af37); color: var(--bs-gold, #d4af37); }

/* Banners */
.cs-banner {
    padding: 0.3rem 0.75rem;
    font-size: 0.65rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-shrink: 0;
}
.cs-banner.b-info {
    background: rgba(212,175,55,.05);
    border-bottom: 1px solid rgba(212,175,55,.1);
    color: #b89730;
}
.cs-banner.b-danger {
    background: rgba(244,106,106,.04);
    border-bottom: 1px solid rgba(244,106,106,.08);
    color: #c84646;
}
.cs-banner i { font-size: 0.78rem; }

/* Messages */
.cs-msg-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem;
}
.cs-msg-scroll::-webkit-scrollbar { width: 3px; }
.cs-msg-scroll::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

.cs-msg-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--bs-surface-500);
    text-align: center;
}
.cs-msg-empty i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: .2; }
.cs-msg-empty p { font-size: 0.78rem; margin: 0; }
.cs-msg-empty small { font-size: 0.65rem; margin-top: 0.2rem; }

.cs-date-sep {
    text-align: center;
    margin: 0.75rem 0 0.5rem;
    position: relative;
}
.cs-date-sep::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0; right: 0;
    height: 1px;
    background: var(--bs-surface-200);
}
.cs-date-sep span {
    position: relative;
    background: var(--bs-card-bg);
    padding: 0 0.6rem;
    font-size: 0.58rem;
    font-weight: 700;
    color: var(--bs-surface-500);
    text-transform: uppercase;
    letter-spacing: .4px;
}

.cs-msg {
    display: flex;
    gap: 0.45rem;
    margin-bottom: 0.45rem;
    align-items: flex-start;
    padding: 0.25rem 0.3rem;
    border-radius: 0.4rem;
    transition: background .12s;
}
.cs-msg:hover { background: rgba(0,0,0,.015); }
.cs-msg.deleted-msg {
    background: rgba(244,106,106,.03);
    border-left: 2px solid rgba(244,106,106,.25);
}
.cs-msg.deleted-msg .cs-msg-text { font-style: italic; opacity: .7; }
.cs-msg.new-msg {
    animation: cs-highlight .8s ease;
}
@keyframes cs-highlight {
    0% { background: rgba(212,175,55,.12); }
    100% { background: transparent; }
}

.cs-msg-av {
    width: 28px; height: 28px;
    border-radius: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.55rem;
    color: #fff;
    flex-shrink: 0;
}
.cs-msg-av img {
    width: 100%; height: 100%;
    border-radius: 0.4rem;
    object-fit: cover;
}
.cs-msg-body { flex: 1; min-width: 0; }
.cs-msg-sender {
    font-weight: 600;
    font-size: 0.7rem;
    color: var(--bs-body-color);
    margin-right: 0.35rem;
}
.cs-msg-ts {
    font-size: 0.58rem;
    color: var(--bs-surface-500);
}
.cs-msg-del-tag {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    font-size: 0.55rem;
    font-weight: 700;
    color: #c84646;
    background: rgba(244,106,106,.08);
    padding: 0.05rem 0.35rem;
    border-radius: 0.2rem;
    margin-left: 0.35rem;
}
.cs-msg-del-tag i { font-size: 0.6rem; }
.cs-msg-text {
    font-size: 0.75rem;
    color: var(--bs-body-color);
    line-height: 1.45;
    margin-top: 1px;
    word-break: break-word;
}

/* ── Attachments (matching chat system) ── */
.cs-att-wrap { margin-top: 0.3rem; }
.cs-att-img {
    max-width: 280px;
    max-height: 220px;
    border-radius: 0.4rem;
    border: 1px solid var(--bs-surface-200);
    cursor: pointer;
    transition: transform .15s;
    display: block;
    margin-top: 0.2rem;
}
.cs-att-img:hover { transform: scale(1.02); }
.cs-att-video {
    max-width: 360px;
    border-radius: 0.4rem;
    margin-top: 0.2rem;
}
.cs-att-audio {
    margin-top: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.cs-att-audio i { font-size: 1.1rem; color: var(--bs-gold, #d4af37); }
.cs-att-audio audio { max-width: 260px; height: 32px; }
.cs-att-file {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.55rem;
    border-radius: 0.35rem;
    background: var(--bs-surface-100);
    font-size: 0.68rem;
    color: var(--bs-body-color);
    text-decoration: none;
    border: 1px solid var(--bs-surface-200);
    transition: all .12s;
    margin-top: 0.2rem;
}
.cs-att-file:hover { border-color: var(--bs-gold, #d4af37); color: var(--bs-gold, #d4af37); }
.cs-att-file i { font-size: 0.85rem; color: var(--bs-gold, #d4af37); }
.cs-att-file .f-name { max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* GIF from [GIF]url messages */
.cs-gif-img {
    max-width: 300px;
    max-height: 260px;
    border-radius: 0.5rem;
    border: 1px solid var(--bs-surface-200);
    display: block;
    margin-top: 0.2rem;
    cursor: pointer;
    transition: transform .15s;
}
.cs-gif-img:hover { transform: scale(1.02); }

/* ── Footer pagination ── */
.cs-msg-pag {
    padding: 0.35rem 0.75rem;
    border-top: 1px solid rgba(0,0,0,.04);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    flex-shrink: 0;
}
.cs-msg-pag button {
    font-size: 0.62rem;
    padding: 0.15rem 0.5rem;
    border-radius: 0.3rem;
    border: 1px solid var(--bs-surface-200);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    transition: all .12s;
}
.cs-msg-pag button:hover:not(:disabled) { border-color: var(--bs-gold, #d4af37); color: var(--bs-gold, #d4af37); }
.cs-msg-pag button:disabled { opacity: .35; cursor: not-allowed; }
.cs-msg-pag span { font-size: 0.6rem; color: var(--bs-surface-500); }

/* Spinner */
.cs-spin {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: var(--bs-surface-500);
}
.cs-spin .spinner-border { width: 20px; height: 20px; border-width: 2px; }

/* ── Tab System ── */
.cs-tabs {
    display: flex;
    gap: 0;
    margin-bottom: 0.65rem;
    border-bottom: 2px solid rgba(0,0,0,.06);
}
.cs-tab-btn {
    padding: 0.45rem 1rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--bs-surface-500);
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: all .15s;
}
.cs-tab-btn:hover { color: var(--bs-body-color); }
.cs-tab-btn.active {
    color: var(--bs-gold, #d4af37);
    border-bottom-color: var(--bs-gold, #d4af37);
}
.cs-tab-btn i { font-size: 0.95rem; }
.cs-tab-btn .tab-count {
    font-size: 0.55rem;
    font-weight: 700;
    padding: 0.08rem 0.35rem;
    border-radius: 0.25rem;
    background: rgba(212,175,55,.1);
    color: #b89730;
}
.cs-tab-pane { display: none; }
.cs-tab-pane.active { display: block; }

/* ── Note Shadowing ── */
.ns-filters {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 0.65rem;
}
.ns-filters .form-control,
.ns-filters .form-select {
    font-size: 0.72rem;
    padding: 0.3rem 0.5rem;
    border-radius: 0.4rem;
    border: 1px solid var(--bs-surface-200);
    background: var(--bs-card-bg);
    max-width: 200px;
    height: 30px;
}
.ns-filters .form-control:focus,
.ns-filters .form-select:focus {
    border-color: var(--bs-gold, #d4af37);
    box-shadow: 0 0 0 2px rgba(212,175,55,.1);
}
.ns-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 0.75rem;
}
.ns-note {
    border-radius: 0.5rem;
    padding: 0.85rem;
    min-height: 160px;
    display: flex;
    flex-direction: column;
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    transition: transform .15s, box-shadow .15s;
    overflow: hidden;
}
.ns-note::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: rgba(0,0,0,.08);
    border-radius: 0.5rem 0.5rem 0 0;
}
.ns-note:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,.12), 0 2px 4px rgba(0,0,0,.06);
}
.ns-note-hdr {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.4rem;
}
.ns-user {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.ns-user-av {
    width: 22px; height: 22px;
    border-radius: 0.3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.5rem;
    color: #fff;
    flex-shrink: 0;
}
.ns-user-name {
    font-weight: 700;
    font-size: 0.68rem;
    color: rgba(0,0,0,.7);
}
.ns-note-id {
    font-size: 0.5rem;
    font-weight: 700;
    opacity: .35;
}
.ns-content {
    flex: 1;
    font-size: 0.75rem;
    line-height: 1.55;
    color: rgba(0,0,0,.75);
    white-space: pre-wrap;
    word-break: break-word;
    overflow-y: auto;
    max-height: 200px;
}
.ns-content::-webkit-scrollbar { width: 2px; }
.ns-content::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 2px; }
.ns-footer {
    margin-top: 0.45rem;
    padding-top: 0.35rem;
    border-top: 1px solid rgba(0,0,0,.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ns-time {
    font-size: 0.55rem;
    color: rgba(0,0,0,.4);
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.ns-time i { font-size: 0.65rem; }
.ns-color-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,.1);
}
.ns-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--bs-surface-500);
}
.ns-empty i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; opacity: .2; }
.ns-empty p { font-size: 0.78rem; margin: 0; }
.ns-empty small { font-size: 0.65rem; color: var(--bs-surface-500); }

/* ── Deleted Note Styles ── */
.ns-note.ns-deleted {
    opacity: .7;
    border: 1.5px dashed #e74c3c;
    position: relative;
}
.ns-note.ns-deleted::after {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
        135deg,
        transparent,
        transparent 10px,
        rgba(231,76,60,.03) 10px,
        rgba(231,76,60,.03) 20px
    );
    pointer-events: none;
    border-radius: 0.5rem;
}
.ns-del-badge {
    font-size: 0.5rem;
    font-weight: 700;
    padding: 0.1rem 0.35rem;
    border-radius: 0.2rem;
    background: rgba(231,76,60,.1);
    color: #e74c3c;
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
}
.ns-del-info {
    font-size: 0.52rem;
    color: #e74c3c;
    display: flex;
    align-items: center;
    gap: 0.15rem;
    margin-top: 2px;
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="cs-page-hdr">
    <h5>
        <i class="bx bx-show"></i> Shadowing Center
        <span class="cs-sub">Real-time monitoring</span>
        <span class="cs-live-dot" id="liveDot" title="Live updates active"></span>
    </h5>
    <a href="{{ route('settings.hub') }}" class="link-btn"><i class="bx bx-arrow-back"></i> Settings</a>
</div>

<!-- Tabs -->
<div class="cs-tabs">
    <button class="cs-tab-btn active" data-tab="chat" onclick="switchTab('chat')">
        <i class="bx bx-message-square-dots"></i> Chat Shadowing
    </button>
    <button class="cs-tab-btn" data-tab="notes" onclick="switchTab('notes')">
        <i class="bx bx-note"></i> Note Shadowing
        <span class="tab-count" id="notesTabCount">0</span>
    </button>
</div>

<!-- ═══ TAB 1: Chat Shadowing ═══ -->
<div class="cs-tab-pane active" id="tabChat">

<!-- KPI Stats -->
<div class="kpi-row" id="kpiRow">
    <div class="ex-card kpi-card k-gold">
        <i class="bx bx-conversation k-icon"></i>
        <div class="k-val" id="kpiTotal">&mdash;</div>
        <div class="k-lbl">Conversations</div>
    </div>
    <div class="ex-card kpi-card k-blue">
        <i class="bx bx-message-detail k-icon"></i>
        <div class="k-val" id="kpiDirect">&mdash;</div>
        <div class="k-lbl">Direct</div>
    </div>
    <div class="ex-card kpi-card k-purple">
        <i class="bx bx-group k-icon"></i>
        <div class="k-val" id="kpiGroup">&mdash;</div>
        <div class="k-lbl">Groups</div>
    </div>
    <div class="ex-card kpi-card k-red">
        <i class="bx bx-trash k-icon"></i>
        <div class="k-val" id="kpiDeleted">&mdash;</div>
        <div class="k-lbl">Deleted</div>
    </div>
    <div class="ex-card kpi-card k-green">
        <i class="bx bx-chat k-icon"></i>
        <div class="k-val" id="kpiMessages">&mdash;</div>
        <div class="k-lbl">Messages</div>
    </div>
</div>

<!-- Filters -->
<div class="cs-filters">
    <input type="text" class="form-control" id="fSearch" placeholder="Search user or group..." autocomplete="off">
    <select class="form-select" id="fType">
        <option value="all">All Types</option>
        <option value="direct">Direct</option>
        <option value="group">Group</option>
    </select>
    <select class="form-select" id="fStatus">
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="deleted">Deleted</option>
    </select>
</div>

<!-- Two-panel layout -->
<div class="cs-layout">
    <!-- Left: Conversations -->
    <div class="ex-card sec-card cs-conv-panel">
        <div class="sec-hdr">
            <h6><i class="bx bx-conversation"></i> Conversations</h6>
            <span class="bd-mini bd-gold" id="convCount">0</span>
        </div>
        <div class="cs-conv-scroll" id="convList">
            <div class="cs-spin" id="convSpin"><div class="spinner-border text-secondary"></div></div>
        </div>
        <div class="cs-conv-pag" id="convPag" style="display:none;">
            <button id="convPrev">&laquo;</button>
            <span id="convPageInfo">1 / 1</span>
            <button id="convNext">&raquo;</button>
        </div>
    </div>

    <!-- Right: Messages -->
    <div class="ex-card sec-card cs-msg-panel">
        <div class="cs-msg-hdr" id="msgHdr" style="display:none;">
            <div>
                <h6 id="msgTitle">&mdash;</h6>
                <small id="msgParticipants"></small>
            </div>
            <div class="cs-msg-toolbar">
                <input type="text" class="form-control" id="mSearch" placeholder="Search...">
                <input type="date" class="form-control" id="mFrom" title="From date">
                <input type="date" class="form-control" id="mTo" title="To date">
                <select class="form-select" id="mDelFilter" style="max-width: 100px;">
                    <option value="all">All</option>
                    <option value="only">Deleted</option>
                </select>
                <button class="btn-filter" id="mFilterBtn" title="Apply"><i class="bx bx-filter-alt"></i></button>
            </div>
        </div>
        <div class="cs-banner b-info" id="bannerReadonly" style="display:none;">
            <i class="bx bx-lock-alt"></i> Read-only &mdash; shadowing this conversation
        </div>
        <div class="cs-banner b-danger" id="bannerDeleted" style="display:none;">
            <i class="bx bx-trash"></i> <span id="bannerDeletedText">This conversation was deleted</span>
        </div>
        <div class="cs-msg-scroll" id="msgBody">
            <div class="cs-msg-empty" id="msgEmpty">
                <i class="bx bx-message-dots"></i>
                <p>Select a conversation</p>
                <small>Pick one from the left panel to view messages</small>
            </div>
        </div>
        <div class="cs-msg-pag" id="msgPag" style="display:none;">
            <button id="msgPrev">&laquo; Prev</button>
            <span id="msgPageInfo">Page 1</span>
            <button id="msgNext">Next &raquo;</button>
        </div>
    </div>
</div>
</div><!-- /tabChat -->

<!-- ═══ TAB 2: Note Shadowing ═══ -->
<div class="cs-tab-pane" id="tabNotes">
    <!-- Note KPIs -->
    <div class="kpi-row" id="noteKpiRow">
        <div class="ex-card kpi-card k-gold">
            <i class="bx bx-note k-icon"></i>
            <div class="k-val" id="nkTotal">&mdash;</div>
            <div class="k-lbl">Total Notes</div>
        </div>
        <div class="ex-card kpi-card k-blue">
            <i class="bx bx-user k-icon"></i>
            <div class="k-val" id="nkUsers">&mdash;</div>
            <div class="k-lbl">Users</div>
        </div>
        <div class="ex-card kpi-card k-green">
            <i class="bx bx-calendar k-icon"></i>
            <div class="k-val" id="nkToday">&mdash;</div>
            <div class="k-lbl">Today</div>
        </div>
        <div class="ex-card kpi-card k-red">
            <i class="bx bx-trash k-icon"></i>
            <div class="k-val" id="nkDeleted">&mdash;</div>
            <div class="k-lbl">Deleted</div>
        </div>
    </div>

    <!-- Note Filters -->
    <div class="ns-filters">
        <input type="text" class="form-control" id="nSearch" placeholder="Search note content..." autocomplete="off">
        <select class="form-select" id="nUserFilter">
            <option value="all">All Users</option>
        </select>
        <select class="form-select" id="nStatusFilter">
            <option value="all">All Notes</option>
            <option value="active">Active Only</option>
            <option value="deleted">Deleted Only</option>
        </select>
    </div>

    <!-- Notes Grid -->
    <div id="notesContainer">
        <div class="cs-spin" id="notesSpin"><div class="spinner-border text-secondary"></div></div>
    </div>
</div><!-- /tabNotes -->
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── State ──
    let convs = [], currentId = null, convPage = 1, convLast = 1;
    let msgPage = 1, msgLast = 1, searchTimer = null;
    let subscribedConvIds = new Set();
    let echoInstance = null;

    const $ = id => document.getElementById(id);

    // DOM
    const convList     = $('convList');
    const convSpin     = $('convSpin');
    const convCount    = $('convCount');
    const convPag      = $('convPag');
    const convPrev     = $('convPrev');
    const convNext     = $('convNext');
    const convPageInfo = $('convPageInfo');
    const fSearch      = $('fSearch');
    const fType        = $('fType');
    const fStatus      = $('fStatus');
    const msgHdr       = $('msgHdr');
    const msgBody      = $('msgBody');
    const msgEmpty     = $('msgEmpty');
    const msgTitle     = $('msgTitle');
    const msgParts     = $('msgParticipants');
    const msgPag       = $('msgPag');
    const msgPrev      = $('msgPrev');
    const msgNext      = $('msgNext');
    const msgPageInfo  = $('msgPageInfo');
    const bannerRO     = $('bannerReadonly');
    const bannerDel    = $('bannerDeleted');
    const bannerDelTxt = $('bannerDeletedText');
    const mSearch      = $('mSearch');
    const mFrom        = $('mFrom');
    const mTo          = $('mTo');
    const mDelFilter   = $('mDelFilter');
    const mFilterBtn   = $('mFilterBtn');
    const kpiTotal     = $('kpiTotal');
    const kpiDirect    = $('kpiDirect');
    const kpiGroup     = $('kpiGroup');
    const kpiDeleted   = $('kpiDeleted');
    const kpiMessages  = $('kpiMessages');

    // ── API Helper ──
    function api(url) {
        return fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json());
    }

    // ── Load Conversations ──
    function loadConversations(page = 1) {
        convSpin.style.display = 'flex';
        const p = new URLSearchParams({
            page, search: fSearch.value, type: fType.value, status: fStatus.value
        });
        api(`{{ url('settings/chat-shadow/conversations') }}?${p}`)
        .then(d => {
            convSpin.style.display = 'none';
            if (!d.success) return;
            convs = d.conversations.data;
            convPage = d.conversations.current_page;
            convLast = d.conversations.last_page;
            convCount.textContent = d.conversations.total;
            updateKPIs(convs, d.conversations.total);
            renderConversations();
            if (convLast > 1) {
                convPag.style.display = 'flex';
                convPrev.disabled = convPage <= 1;
                convNext.disabled = convPage >= convLast;
                convPageInfo.textContent = `${convPage} / ${convLast}`;
            } else {
                convPag.style.display = 'none';
            }
            subscribeToAllConversations();
        })
        .catch(() => {
            convSpin.style.display = 'none';
            convList.innerHTML = '<div class="cs-conv-empty"><i class="bx bx-error-circle"></i>Failed to load</div>';
        });
    }

    function updateKPIs(data, total) {
        kpiTotal.textContent = total;
        let direct = 0, group = 0, deleted = 0, msgs = 0;
        data.forEach(c => {
            if (c.type === 'direct') direct++;
            if (c.type === 'group') group++;
            if (c.is_deleted) deleted++;
            msgs += c.messages_count || 0;
        });
        kpiDirect.textContent = direct;
        kpiGroup.textContent = group;
        kpiDeleted.textContent = deleted;
        kpiMessages.textContent = msgs;
    }

    function renderConversations() {
        if (convs.length === 0) {
            convList.innerHTML = '<div class="cs-conv-empty"><i class="bx bx-conversation"></i>No conversations found</div>';
            return;
        }
        let html = '';
        convs.forEach(c => {
            const ini = c.display_name.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
            const active = c.id === currentId ? ' active' : '';
            const del = c.is_deleted ? ' deleted-conv' : '';
            const preview = c.latest_message
                ? `<b>${esc(c.latest_message.user_name)}:</b> ${esc(trunc(c.latest_message.message || 'Attachment', 35))}`
                : '<i style="opacity:.5">No messages</i>';
            const time = c.latest_message ? c.latest_message.created_at : c.updated_at;
            const delBadge = c.is_deleted ? '<span class="bd-mini bd-red" style="font-size:.5rem">DEL</span>' : '';
            const delMsgs = c.deleted_messages_count > 0 ? `<span style="font-size:.55rem;color:#c84646">${c.deleted_messages_count} del</span>` : '';
            html += `
                <div class="cs-conv${active}${del}" data-id="${c.id}" onclick="selectConv(${c.id})">
                    <div class="cs-conv-av t-${c.type}">
                        ${c.type === 'group' ? '<i class="bx bx-group" style="font-size:.9rem"></i>' : ini}
                    </div>
                    <div class="cs-conv-body">
                        <div class="cs-conv-name">${esc(c.display_name)}</div>
                        <div class="cs-conv-preview">${preview}</div>
                    </div>
                    <div class="cs-conv-meta">
                        <span class="cs-conv-time">${esc(time)}</span>
                        ${delBadge}
                        <span class="bd-mini bd-${c.type === 'direct' ? 'blue' : 'gold'}" style="font-size:.48rem">${c.type}</span>
                        <span style="font-size:.55rem;color:var(--bs-surface-500)">${c.messages_count} msgs</span>
                        ${delMsgs}
                    </div>
                </div>`;
        });
        convList.innerHTML = html;
    }

    // ── Select Conversation ──
    window.selectConv = function(id) {
        currentId = id;
        msgPage = 1;
        mSearch.value = '';
        mFrom.value = '';
        mTo.value = '';
        mDelFilter.value = 'all';
        document.querySelectorAll('.cs-conv').forEach(el => el.classList.remove('active'));
        const el = document.querySelector(`.cs-conv[data-id="${id}"]`);
        if (el) el.classList.add('active');
        loadMessages(id, 1);
    };

    // ── Load Messages ──
    function loadMessages(convId, page = 1) {
        msgEmpty.style.display = 'none';
        msgHdr.style.display = 'flex';
        bannerRO.style.display = 'flex';
        msgBody.innerHTML = '<div class="cs-spin"><div class="spinner-border text-secondary"></div></div>';

        const p = new URLSearchParams({
            page, search: mSearch.value, date_from: mFrom.value, date_to: mTo.value, show_deleted: mDelFilter.value
        });
        api(`{{ url('settings/chat-shadow/conversations') }}/${convId}/messages?${p}`)
        .then(d => {
            if (!d.success) return;
            const conv = d.conversation;
            msgTitle.textContent = conv.display_name;
            msgParts.textContent = conv.participants.map(p => p.name).join(', ');
            if (conv.is_deleted) {
                bannerDel.style.display = 'flex';
                bannerDelTxt.textContent = 'This conversation was deleted by the user';
            } else {
                bannerDel.style.display = 'none';
            }
            msgPage = d.messages.current_page;
            msgLast = d.messages.last_page;
            renderMessages(d.messages.data);
            if (msgLast > 1) {
                msgPag.style.display = 'flex';
                msgPrev.disabled = msgPage <= 1;
                msgNext.disabled = msgPage >= msgLast;
                msgPageInfo.textContent = `Page ${msgPage} of ${msgLast}`;
            } else {
                msgPag.style.display = 'none';
            }
        })
        .catch(() => {
            msgBody.innerHTML = '<div class="cs-msg-empty"><i class="bx bx-error-circle"></i><p>Failed to load messages</p></div>';
        });
    }

    function renderMessages(messages) {
        if (!messages || messages.length === 0) {
            msgBody.innerHTML = '<div class="cs-msg-empty"><i class="bx bx-message-x"></i><p>No messages found</p><small>Adjust filters or date range</small></div>';
            return;
        }
        let html = '', lastDate = null;
        messages.forEach(msg => {
            const dt = new Date(msg.created_at);
            const dateStr = dt.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
            const timeStr = dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            if (dateStr !== lastDate) {
                html += `<div class="cs-date-sep"><span>${dateStr}</span></div>`;
                lastDate = dateStr;
            }
            const u = msg.user || {};
            const ini = (u.name || '?').split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
            const avHtml = u.avatar
                ? `<img src="${escA(assetUrl(u.avatar))}" alt="${escA(u.name)}" onerror="this.style.display='none';this.parentNode.textContent='${ini}'">`
                : ini;
            const hue = hash(u.name || 'U') % 360;

            // Attachments
            let attHtml = '';
            if (msg.attachments && msg.attachments.length > 0) {
                attHtml = '<div class="cs-att-wrap">';
                msg.attachments.forEach(att => {
                    const url = att.file_path ? `/storage/${att.file_path}` : '#';
                    const mime = att.mime_type || '';
                    if (mime.startsWith('image/')) {
                        attHtml += `<img class="cs-att-img" src="${escA(url)}" alt="${escA(att.file_name || 'Image')}" onclick="window.open('${escA(url)}', '_blank')" loading="lazy">`;
                    } else if (mime.startsWith('video/')) {
                        attHtml += `<video class="cs-att-video" controls preload="metadata"><source src="${escA(url)}" type="${mime}"></video>`;
                    } else if (mime.startsWith('audio/')) {
                        attHtml += `<div class="cs-att-audio"><i class="bx bx-volume-full"></i><audio controls preload="metadata"><source src="${escA(url)}" type="${mime}"></audio></div>`;
                    } else {
                        const icon = mime === 'application/pdf' ? 'bx-file-doc' : (mime.includes('word') ? 'bx-file-doc' : (mime.includes('zip') || mime.includes('rar') ? 'bx-archive' : 'bx-file'));
                        attHtml += `<a href="${escA(url)}" target="_blank" class="cs-att-file"><i class="bx ${icon}"></i><span class="f-name">${esc(att.file_name || 'File')}</span></a>`;
                    }
                });
                attHtml += '</div>';
            }

            const isDel = msg.is_deleted;
            const delTag = isDel ? `<span class="cs-msg-del-tag"><i class="bx bx-trash"></i>Deleted${msg.deleted_at_formatted ? ' ' + esc(msg.deleted_at_formatted) : ''}</span>` : '';
            html += `
                <div class="cs-msg${isDel ? ' deleted-msg' : ''}" data-msg-id="${msg.id}">
                    <div class="cs-msg-av" style="background:hsl(${hue},50%,50%)">
                        ${avHtml}
                    </div>
                    <div class="cs-msg-body">
                        <span class="cs-msg-sender">${esc(u.name || 'Unknown')}</span>
                        <span class="cs-msg-ts">${timeStr}</span>
                        ${delTag}
                        ${formatMsgText(msg.message)}
                        ${attHtml}
                    </div>
                </div>`;
        });
        msgBody.innerHTML = html;
        msgBody.scrollTop = msgBody.scrollHeight;
    }

    // ── Event Listeners ──
    fSearch.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadConversations(1), 350); });
    fType.addEventListener('change', () => loadConversations(1));
    fStatus.addEventListener('change', () => loadConversations(1));
    mFilterBtn.addEventListener('click', () => { if (currentId) loadMessages(currentId, 1); });
    mSearch.addEventListener('keydown', e => { if (e.key === 'Enter' && currentId) loadMessages(currentId, 1); });
    convPrev.addEventListener('click', () => { if (convPage > 1) loadConversations(convPage - 1); });
    convNext.addEventListener('click', () => { if (convPage < convLast) loadConversations(convPage + 1); });
    msgPrev.addEventListener('click', () => { if (currentId && msgPage > 1) loadMessages(currentId, msgPage - 1); });
    msgNext.addEventListener('click', () => { if (currentId && msgPage < msgLast) loadMessages(currentId, msgPage + 1); });

    // ── Live Updates (Echo/Reverb) ──
    const echoConfig = {!! json_encode([
        'key' => env('REVERB_APP_KEY', ''),
        'host' => env('REVERB_HOST', '127.0.0.1'),
        'port' => intval(env('REVERB_PORT', 8080)),
        'scheme' => env('REVERB_SCHEME', 'http'),
        'forceTLS' => env('REVERB_SCHEME', 'http') === 'https',
    ]) !!};

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) return resolve();
            const s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function initEcho() {
        if (!echoConfig.key || echoInstance) return;
        Promise.resolve()
            .then(() => loadScript('https://js.pusher.com/7.2/pusher.min.js'))
            .then(() => loadScript('https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js'))
            .then(() => {
                try {
                    echoInstance = new Echo({
                        broadcaster: 'reverb',
                        key: echoConfig.key,
                        wsHost: echoConfig.host,
                        wsPort: echoConfig.port,
                        wssPort: echoConfig.port,
                        forceTLS: echoConfig.forceTLS,
                        enabledTransports: ['ws', 'wss'],
                    });
                    console.log('[ChatShadow] Echo initialized');
                    subscribeToAllConversations();
                } catch(e) {
                    console.warn('[ChatShadow] Echo init failed', e);
                }
            })
            .catch(e => console.warn('[ChatShadow] Script load failed', e));
    }

    function subscribeToAllConversations() {
        if (!echoInstance) return;
        convs.forEach(c => {
            if (subscribedConvIds.has(c.id)) return;
            subscribedConvIds.add(c.id);
            try {
                echoInstance.private(`chat.conversation.${c.id}`)
                    .listen('.message.sent', (e) => {
                        console.log('[ChatShadow] Live message in conv', c.id, e);
                        if (c.id === currentId) {
                            loadMessages(currentId, msgPage);
                        }
                        loadConversations(convPage);
                    });
            } catch(e) { /* ignore */ }
        });
    }

    // Fallback polling every 30s
    setInterval(() => {
        loadConversations(convPage);
        if (currentId) loadMessages(currentId, msgPage);
    }, 30000);

    // ── Helpers ──
    function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function formatMsgText(text) {
        if (!text) return '';
        // Detect [GIF]url pattern
        if (text.startsWith('[GIF]')) {
            const gifUrl = text.substring(5);
            return `<img class="cs-gif-img" src="${escA(gifUrl)}" alt="GIF" onclick="window.open('${escA(gifUrl)}', '_blank')" loading="lazy" onerror="this.outerHTML='<div class=cs-msg-text>[GIF failed to load]</div>'">`;
        }
        return `<div class="cs-msg-text">${esc(text)}</div>`;
    }
    function escA(s) { return (s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,"&#39;").replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function trunc(s, n) { return s && s.length > n ? s.substring(0,n) + '...' : s; }
    function hash(s) { let h=0; for(let i=0;i<(s||'').length;i++) h=s.charCodeAt(i)+((h<<5)-h); return Math.abs(h); }
    function assetUrl(p) { if (!p) return ''; if (p.startsWith('http')) return p; return '{{ asset("") }}' + p; }

    // ── Init ──
    loadConversations(1);
    initEcho();

    // ══════════════════════════════════════════════
    // TAB SYSTEM
    // ══════════════════════════════════════════════
    let notesLoaded = false;

    window.switchTab = function(tab) {
        document.querySelectorAll('.cs-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.cs-tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelector(`.cs-tab-btn[data-tab="${tab}"]`).classList.add('active');
        document.getElementById(tab === 'chat' ? 'tabChat' : 'tabNotes').classList.add('active');
        if (tab === 'notes' && !notesLoaded) {
            notesLoaded = true;
            loadNotes();
        }
    };

    // ══════════════════════════════════════════════
    // NOTE SHADOWING
    // ══════════════════════════════════════════════
    const notesContainer = $('notesContainer');
    const notesSpin      = $('notesSpin');
    const nSearch        = $('nSearch');
    const nUserFilter    = $('nUserFilter');
    const nkTotal        = $('nkTotal');
    const nkUsers        = $('nkUsers');
    const nkToday        = $('nkToday');
    const nkDeleted      = $('nkDeleted');
    const nStatusFilter  = $('nStatusFilter');
    const notesTabCount  = $('notesTabCount');
    let nsTimer = null;

    function loadNotes() {
        notesSpin.style.display = 'flex';
        const p = new URLSearchParams({
            search: nSearch.value,
            user_id: nUserFilter.value,
            status: nStatusFilter.value,
        });
        api(`{{ url('settings/chat-shadow/notes') }}?${p}`)
        .then(d => {
            notesSpin.style.display = 'none';
            if (!d.success) return;
            notesTabCount.textContent = d.total;
            nkTotal.textContent = d.total;

            // Populate user filter (only once or if changed)
            if (nUserFilter.options.length <= 1 && d.users.length > 0) {
                d.users.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.name;
                    nUserFilter.appendChild(opt);
                });
            }
            nkUsers.textContent = d.users.length;
            nkDeleted.textContent = d.deleted_count || 0;

            // Count today's notes
            const todayStr = new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            const todayCount = d.notes.filter(n => {
                // Match "Feb 22, 2026" format
                return n.created_at.startsWith(todayStr.replace(',', ''));
            }).length;
            nkToday.textContent = todayCount;

            renderNotes(d.notes);
        })
        .catch(() => {
            notesSpin.style.display = 'none';
            notesContainer.innerHTML = '<div class="ns-empty"><i class="bx bx-error-circle"></i><p>Failed to load notes</p></div>';
        });
    }

    function renderNotes(notes) {
        if (!notes || notes.length === 0) {
            notesContainer.innerHTML = '<div class="ns-empty"><i class="bx bx-note"></i><p>No sticky notes found</p><small>No users have created any notes yet</small></div>';
            return;
        }
        let html = '<div class="ns-grid">';
        notes.forEach(note => {
            const ini = (note.user_name || '?').split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
            const hue = hash(note.user_name || 'U') % 360;
            const bgColor = note.color || '#fffacd';
            // Determine if background is dark to adjust text color
            const textColor = isLightColor(bgColor) ? 'rgba(0,0,0,.75)' : 'rgba(255,255,255,.9)';
            const metaColor = isLightColor(bgColor) ? 'rgba(0,0,0,.4)' : 'rgba(255,255,255,.55)';
            const borderColor = isLightColor(bgColor) ? 'rgba(0,0,0,.06)' : 'rgba(255,255,255,.15)';

            const deletedClass = note.is_deleted ? ' ns-deleted' : '';
            const deletedBadge = note.is_deleted
                ? `<span class="ns-del-badge"><i class="bx bx-trash"></i> Deleted</span>`
                : '';
            const deletedInfo = note.is_deleted && note.deleted_ago
                ? `<div class="ns-del-info"><i class="bx bx-trash"></i> Deleted ${esc(note.deleted_ago)}</div>`
                : '';

            html += `
                <div class="ns-note${deletedClass}" style="background:${escA(bgColor)};color:${textColor}">
                    <div class="ns-note-hdr">
                        <div class="ns-user">
                            <div class="ns-user-av" style="background:hsl(${hue},50%,50%)">${ini}</div>
                            <span class="ns-user-name" style="color:${textColor}">${esc(note.user_name)}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.3rem">
                            ${deletedBadge}
                            <span class="ns-note-id" style="color:${metaColor}">#${note.id}</span>
                        </div>
                    </div>
                    <div class="ns-content" style="color:${textColor}">${esc(note.content)}</div>
                    <div class="ns-footer" style="border-top-color:${borderColor}">
                        <div class="ns-time" style="color:${metaColor}">
                            <span><i class="bx bx-plus-circle"></i> ${esc(note.created_ago)}</span>
                            <span><i class="bx bx-edit"></i> ${esc(note.updated_ago)}</span>
                            ${deletedInfo}
                        </div>
                        <div class="ns-color-dot" style="background:${escA(bgColor)}"></div>
                    </div>
                </div>`;
        });
        html += '</div>';
        notesContainer.innerHTML = html;
    }

    function isLightColor(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        const r = parseInt(hex.substr(0,2), 16);
        const g = parseInt(hex.substr(2,2), 16);
        const b = parseInt(hex.substr(4,2), 16);
        const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return lum > 0.5;
    }

    nSearch.addEventListener('input', () => { clearTimeout(nsTimer); nsTimer = setTimeout(() => loadNotes(), 350); });
    nUserFilter.addEventListener('change', () => loadNotes());
    nStatusFilter.addEventListener('change', () => loadNotes());

    // Polling for notes too
    setInterval(() => {
        if (notesLoaded) loadNotes();
    }, 30000);
});
</script>
@endsection
