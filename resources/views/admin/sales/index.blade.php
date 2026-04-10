@use('App\Support\Roles')
@use('App\Support\Statuses')
@extends('layouts.master')

@section('title')
    Sales Management
@endsection

@section('css')
<style>
    /* ── Sales Page Design System ── */

    /* Top bar */
    .sl-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; flex-wrap: wrap; gap: .75rem;
    }
    .sl-topbar-left { display: flex; align-items: center; gap: .75rem; }
    .sl-page-title {
        font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .sl-page-title i { color: #d4af37; font-size: 1.2rem; }
    .sl-topbar-right { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

    /* Search */
    .sl-search-wrap {
        position: relative; display: flex; align-items: center;
    }
    .sl-search-icon {
        position: absolute; left: .6rem; color: #94a3b8; font-size: .9rem; pointer-events: none;
    }
    .sl-search-input {
        padding: .42rem .65rem .42rem 2rem;
        font-size: .78rem; border: 1px solid rgba(0,0,0,.1);
        border-radius: 8px; background: #fff; width: 220px;
        outline: none; transition: border-color .15s;
    }
    .sl-search-input:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    /* Action buttons */
    .sl-btn {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .42rem .8rem; font-size: .75rem; font-weight: 700;
        border-radius: 8px; border: none; cursor: pointer;
        transition: all .15s; white-space: nowrap;
    }
    .sl-btn-add {
        background: linear-gradient(135deg, #d4af37, #b8941f);
        color: #0f172a;
    }
    .sl-btn-add:hover { background: linear-gradient(135deg, #e0c04c, #d4af37); transform: translateY(-1px); }
    .sl-btn-import {
        background: transparent; border: 1px solid rgba(0,0,0,.12); color: #475569;
    }
    .sl-btn-import:hover { border-color: #d4af37; color: #d4af37; }

    /* KPI Row */
    .sl-kpi-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: .75rem;
        margin-bottom: 1rem;
    }
    .sl-kpi {
        background: rgba(255,255,255,.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 10px;
        padding: .85rem 1rem;
        display: flex; align-items: center; gap: .75rem;
        transition: transform .15s, box-shadow .15s;
    }
    .sl-kpi:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.06); }
    .sl-kpi-link {
        text-decoration: none; color: inherit; display: block;
        border-radius: 10px;
    }
    .sl-kpi-link:hover .sl-kpi { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); cursor: pointer; }
    .sl-kpi.active-tab {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 2px rgba(212,175,55,.3), 0 4px 16px rgba(0,0,0,.08) !important;
        background: rgba(212,175,55,.06) !important;
    }
    .sl-kpi-icon {
        width: 38px; height: 38px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem; color: #fff; flex-shrink: 0;
    }
    .sl-kpi-icon.pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .sl-kpi-icon.approved { background: linear-gradient(135deg, #10b981, #059669); }
    .sl-kpi-icon.declined { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .sl-kpi-icon.uw { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .sl-kpi-info { display: flex; flex-direction: column; }
    .sl-kpi-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; }
    .sl-kpi-val { font-size: 1.35rem; font-weight: 800; color: #1e293b; line-height: 1.1; }

    /* Card */
    .sl-card {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 12px;
        overflow: hidden;
        padding-bottom: .75rem;
    }

    /* Filter Pills */
    .sl-filter-pills {
        display: flex; align-items: center; gap: .4rem;
        padding: .6rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
        background: rgba(248,250,252,.6);
        flex-wrap: wrap;
    }
    .sl-pill-select {
        font-size: .72rem; font-weight: 600;
        padding: .3rem .5rem; border-radius: 20px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; color: #475569;
        cursor: pointer; outline: none;
        transition: border-color .15s;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .45rem center;
        padding-right: 1.4rem;
    }
    .sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37; }
    .sl-pill-date {
        font-size: .72rem; font-weight: 600;
        padding: .3rem .5rem; border-radius: 20px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; color: #475569;
        cursor: pointer; outline: none;
        transition: border-color .15s;
        min-width: 120px;
        color-scheme: light;
    }
    .sl-pill-label {
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #94a3b8; margin-right: -2px;
    }
    .sl-pill-clear {
        font-size: .68rem; font-weight: 600; color: #ef4444;
        text-decoration: none; padding: .25rem .5rem;
        border-radius: 20px; border: 1px solid rgba(239,68,68,.2);
        display: inline-flex; align-items: center; gap: 2px;
        transition: all .15s;
    }
    .sl-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }
    .sl-pill-today {
        font-size: .68rem; font-weight: 600; color: #0ea5e9;
        background: transparent; padding: .25rem .6rem;
        border-radius: 20px; border: 1px solid rgba(14,165,233,.3);
        cursor: pointer; display: inline-flex; align-items: center;
        transition: all .15s;
    }
    .sl-pill-today:hover { background: rgba(14,165,233,.1); border-color: #0ea5e9; }

    /* Table area */
    .sl-tbl-wrap {
        overflow-x: auto;
        overflow-y: auto;
        max-height: calc(100vh - 320px);
        scrollbar-width: thin;
        scrollbar-color: #d4af37 transparent;
        padding-bottom: .25rem;
    }
    .sl-tbl-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
    .sl-tbl-wrap::-webkit-scrollbar-track { background: transparent; }
    .sl-tbl-wrap::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 3px; }

    .sl-tbl {
        width: 100%; border-collapse: separate; border-spacing: 0;
        font-size: .78rem;
    }
    .sl-tbl thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #64748b;
        padding: .65rem .6rem;
        border-bottom: 1px solid rgba(212,175,55,.18);
        white-space: nowrap;
        position: sticky; top: 0; z-index: 10;
    }
    .sl-tbl tbody td {
        padding: .55rem .6rem;
        border-bottom: 1px solid rgba(0,0,0,.04);
        vertical-align: middle;
        color: #334155;
        transition: background .12s;
    }
    .sl-tbl tbody tr { transition: background .12s; }
    .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.045); }
    .sl-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.45); }
    .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.045); }

    /* Sticky columns */
    .sl-sticky-col {
        position: sticky;
        z-index: 5;
        background: #fff;
    }
    .sl-tbl thead .sl-sticky-col { z-index: 15; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); }
    .sl-col-1 { left: 0; }
    .sl-col-2 { left: 160px; }
    .sl-col-3 { left: 310px; border-right: 2px solid rgba(212,175,55,.15); }
    .sl-tbl thead .sl-col-3 { border-right: 2px solid rgba(212,175,55,.15); }
    .sl-tbl tbody tr:hover .sl-sticky-col { background: rgba(255,252,240,1); }
    .sl-tbl tbody tr:nth-child(even) .sl-sticky-col { background: #fafbfc; }
    .sl-tbl tbody tr:nth-child(even):hover .sl-sticky-col { background: rgba(255,252,240,1); }

    /* Inline edit cells */
    .sl-edit-cell { display: flex; flex-direction: column; gap: 3px; }
    .sl-edit-cell small { font-size: .64rem; color: #94a3b8; font-weight: 500; }
    .sl-edit-row { display: flex; align-items: center; gap: 4px; }
    .sl-edit-row .form-control,
    .sl-edit-row .form-select {
        font-size: .74rem; padding: .28rem .45rem; border-radius: 20px;
        border: 1px solid rgba(0,0,0,.09); background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }
    .sl-edit-row .form-control:focus,
    .sl-edit-row .form-select:focus {
        border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.1);
    }
    .sl-edit-row .btn {
        padding: .22rem .4rem; font-size: .72rem; border-radius: 20px;
        border: none; background: linear-gradient(135deg, #10b981, #059669);
        color: #fff; transition: all .15s; flex-shrink: 0;
    }
    .sl-edit-row .btn:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(16,185,129,.25); }

    /* Bubble dropdowns (QA / Manager status) */
    .sl-bubble-select {
        font-size: .73rem; font-weight: 600;
        padding: .3rem .55rem; padding-right: 1.6rem;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,.09);
        background: #fff;
        color: #334155;
        cursor: pointer; outline: none;
        transition: border-color .15s, box-shadow .15s;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .5rem center;
        min-width: 110px;
    }
    .sl-bubble-select:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    /* Bubble textarea */
    .sl-bubble-textarea {
        font-size: .73rem; padding: .3rem .55rem;
        border-radius: 14px; border: 1px solid rgba(0,0,0,.09);
        background: #fff; color: #334155; resize: vertical;
        transition: border-color .15s, box-shadow .15s; outline: none;
        min-height: 32px;
    }
    .sl-bubble-textarea:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .sl-bubble-textarea::placeholder { color: #b0b8c4; font-weight: 400; }

    /* Save / action pill buttons */
    .sl-save-btn {
        display: inline-flex; align-items: center; gap: .25rem;
        font-size: .68rem; font-weight: 600;
        padding: .22rem .55rem; border-radius: 20px; border: none;
        cursor: pointer; transition: all .15s; margin-top: 4px;
        color: #fff;
    }
    .sl-save-btn.primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .sl-save-btn.primary:hover { box-shadow: 0 2px 8px rgba(59,130,246,.3); transform: translateY(-1px); }
    .sl-save-btn.success { background: linear-gradient(135deg, #10b981, #059669); }
    .sl-save-btn.success:hover { box-shadow: 0 2px 8px rgba(16,185,129,.3); transform: translateY(-1px); }
    .sl-save-btn.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .sl-save-btn.warning:hover { box-shadow: 0 2px 8px rgba(245,158,11,.3); transform: translateY(-1px); }

    /* Action buttons in table */
    .sl-act-group { display: flex; gap: 4px; justify-content: center; }
    .sl-act-group .btn {
        width: 28px; height: 28px; padding: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; font-size: .68rem;
        border: none; color: #fff; transition: all .15s;
        box-shadow: 0 1px 3px rgba(0,0,0,.1);
    }
    .sl-act-group .btn:hover { transform: scale(1.1); box-shadow: 0 3px 10px rgba(0,0,0,.15); }
    .sl-act-group .btn-success { background: linear-gradient(135deg, #10b981, #059669); }
    .sl-act-group .btn-warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
    .sl-act-group .btn-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .sl-act-group .btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .sl-act-group .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

    /* Badges */
    .sl-tbl .badge {
        font-size: .68rem; font-weight: 600;
        padding: .25rem .55rem; border-radius: 20px;
        letter-spacing: .2px;
    }

    /* Follow-up badge */
    .sl-follow-badge {
        display: inline-flex; align-items: center;
        font-size: .68rem; font-weight: 600;
        padding: .2rem .5rem; border-radius: 20px;
    }
    .sl-follow-badge.yes { background: rgba(16,185,129,.1); color: #059669; }
    .sl-follow-badge.no { background: rgba(100,116,139,.08); color: #64748b; }

    /* Pagination */
    .sl-card .mt-3 { padding: 0 1rem .75rem; }

    /* Peregrine badge */
    .bg-purple { background-color: var(--bs-ui-purple, #6f42c1) !important; color: #fff !important; }

    /* QA cleared ticker */
    .sl-qa-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 22px; height: 22px; border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff; font-size: .6rem; font-weight: 800;
        letter-spacing: .3px; line-height: 1;
        vertical-align: middle; margin-left: 4px;
        box-shadow: 0 1px 4px rgba(16,185,129,.45);
        flex-shrink: 0;
    }
    .sl-qa-badge-bad {
        display: inline-flex; align-items: center; justify-content: center;
        width: 22px; height: 22px; border-radius: 50%;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff; font-size: .6rem; font-weight: 800;
        letter-spacing: .3px; line-height: 1;
        vertical-align: middle; margin-left: 4px;
        box-shadow: 0 1px 4px rgba(239,68,68,.45);
        flex-shrink: 0;
    }

    /* ── Custom Dropdown (pill-shaped panels) ── */
    .sl-cdd { position: relative; display: inline-block; vertical-align: middle; }
    .sl-cdd select { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; overflow: hidden; }
    .sl-cdd-trigger {
        display: inline-flex; align-items: center; gap: .3rem;
        cursor: pointer; user-select: none; white-space: nowrap;
        transition: border-color .15s, box-shadow .15s;
    }
    .sl-cdd-trigger .sl-cdd-chevron {
        width: 10px; height: 6px; flex-shrink: 0; opacity: .45;
        transition: transform .2s;
    }
    .sl-cdd.open .sl-cdd-trigger .sl-cdd-chevron { transform: rotate(180deg); }
    .sl-cdd.open .sl-cdd-trigger { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    /* Dropdown panel */
    .sl-cdd-panel {
        position: absolute; top: calc(100% + 5px); left: 0;
        min-width: 100%; width: max-content;
        background: #fff;
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.06);
        z-index: 200;
        overflow: hidden;
        opacity: 0; transform: translateY(-4px); pointer-events: none;
        transition: opacity .15s, transform .15s;
        max-height: 240px; overflow-y: auto;
        scrollbar-width: thin; scrollbar-color: #d4af37 transparent;
    }
    .sl-cdd-panel::-webkit-scrollbar { width: 4px; }
    .sl-cdd-panel::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 2px; }
    .sl-cdd.open .sl-cdd-panel {
        opacity: 1; transform: translateY(0); pointer-events: auto;
    }
    .sl-cdd-option {
        padding: .4rem .65rem; font-size: .73rem; font-weight: 600;
        color: #475569; cursor: pointer;
        transition: background .1s, color .1s;
        white-space: nowrap;
    }
    .sl-cdd-option:hover { background: rgba(212,175,55,.08); color: #1e293b; }
    .sl-cdd-option.active {
        background: rgba(212,175,55,.14); color: #92710c;
    }
    .sl-cdd-option:first-child { padding-top: .5rem; }
    .sl-cdd-option:last-child { padding-bottom: .5rem; }

    /* Pill variant (filter bar) */
    .sl-cdd.pill .sl-cdd-trigger {
        font-size: .72rem; font-weight: 600;
        padding: .3rem .5rem; padding-right: .35rem;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; color: #475569;
    }
    /* Bubble variant (table inline) */
    .sl-cdd.bubble .sl-cdd-trigger {
        font-size: .73rem; font-weight: 600;
        padding: .3rem .55rem; padding-right: .4rem;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,.09);
        background: #fff; color: #334155;
        min-width: 100px;
    }
    /* Edit-row variant (inline edits inside table cells) */
    .sl-cdd.edit .sl-cdd-trigger {
        font-size: .74rem; font-weight: 500;
        padding: .28rem .45rem; padding-right: .35rem;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,.09);
        background: #fff; color: #334155;
        width: 100%;
    }
    .sl-cdd.edit .sl-cdd-trigger .sl-cdd-label { flex: 1; }

    /* ── Dark mode ── */
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: #f1f5f9; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input:focus { border-color: #d4af37; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-btn-import { border-color: rgba(255,255,255,.1); color: #94a3b8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-btn-import:hover { border-color: #d4af37; color: #d4af37; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi {
        background: rgba(30,41,59,.7); border-color: rgba(255,255,255,.06);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi-label { color: #94a3b8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi-val { color: #f1f5f9; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi.active-tab {
        background: rgba(212,175,55,.1) !important; border-color: #d4af37 !important;
        box-shadow: 0 0 0 2px rgba(212,175,55,.25), 0 4px 16px rgba(0,0,0,.2) !important;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills {
        background: rgba(15,23,42,.4); border-color: rgba(255,255,255,.05);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        color-scheme: dark;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-clear { border-color: rgba(239,68,68,.3); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-today { border-color: rgba(14,165,233,.3); color: #38bdf8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th {
        background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9));
        color: #94a3b8; border-color: rgba(212,175,55,.12);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td {
        color: #cbd5e1; border-color: rgba(255,255,255,.04);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.02); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.06); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-sticky-col { background: #1e293b; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead .sl-sticky-col { background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9)); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover .sl-sticky-col { background: rgba(30,41,59,.9); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even) .sl-sticky-col { background: #1a2536; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even):hover .sl-sticky-col { background: rgba(30,41,59,.9); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-col-3 { border-right-color: rgba(212,175,55,.12); }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-edit-cell small { color: #64748b; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-edit-row .form-control,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-edit-row .form-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-bubble-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-bubble-textarea {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-bubble-textarea::placeholder { color: #475569; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-follow-badge.no { background: rgba(100,116,139,.15); color: #94a3b8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-follow-badge.yes { background: rgba(16,185,129,.15); color: #34d399; }

    /* Dark mode: custom dropdown */
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd.pill .sl-cdd-trigger,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd.bubble .sl-cdd-trigger,
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd.edit .sl-cdd-trigger {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd.open .sl-cdd-trigger { border-color: #d4af37; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-panel {
        background: #1e293b; border-color: rgba(255,255,255,.08);
        box-shadow: 0 8px 28px rgba(0,0,0,.35), 0 2px 8px rgba(0,0,0,.2);
    }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-option { color: #94a3b8; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-option:hover { background: rgba(212,175,55,.1); color: #e2e8f0; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-option.active { background: rgba(212,175,55,.18); color: #fbbf24; }
    :is(:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-cdd-chevron path { fill: #64748b; }

    /* Responsiveness */
    @media (max-width: 992px) {
        .sl-kpi-row { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .sl-kpi-row { grid-template-columns: repeat(2, 1fr); }
        .sl-topbar { flex-direction: column; align-items: flex-start; }
        .sl-topbar-right { width: 100%; }
        .sl-search-input { width: 100% !important; }
    }
</style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Tab Cards -->
    @php
        $tabBaseQuery = request()->except('status', 'page');
    @endphp
    <div class="sl-kpi-row">
        {{-- All Sales --}}
        <a href="{{ route('sales.index', array_merge($tabBaseQuery, ['status' => 'all'])) }}" class="sl-kpi-link">
            <div class="sl-kpi {{ $activeTab === 'all' ? 'active-tab' : '' }}">
                <div class="sl-kpi-icon" style="background: linear-gradient(135deg, #64748b, #475569);">
                    <i class="mdi mdi-view-list"></i>
                </div>
                <div class="sl-kpi-info">
                    <span class="sl-kpi-label">All Sales</span>
                    <span class="sl-kpi-val">{{ number_format($statusCounts['all']) }}</span>
                </div>
            </div>
        </a>
        {{-- Pending Validation --}}
        <a href="{{ route('sales.index', array_merge($tabBaseQuery, ['status' => 'pending_validation'])) }}" class="sl-kpi-link">
            <div class="sl-kpi {{ $activeTab === 'pending_validation' ? 'active-tab' : '' }}">
                <div class="sl-kpi-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="mdi mdi-clock-outline"></i>
                </div>
                <div class="sl-kpi-info">
                    <span class="sl-kpi-label">Pending Validation</span>
                    <span class="sl-kpi-val">{{ number_format($statusCounts['pending_validation']) }}</span>
                </div>
            </div>
        </a>
        {{-- Validated --}}
        <a href="{{ route('sales.index', array_merge($tabBaseQuery, ['status' => 'validated'])) }}" class="sl-kpi-link">
            <div class="sl-kpi {{ $activeTab === 'validated' ? 'active-tab' : '' }}">
                <div class="sl-kpi-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="mdi mdi-check-circle-outline"></i>
                </div>
                <div class="sl-kpi-info">
                    <span class="sl-kpi-label">Validated</span>
                    <span class="sl-kpi-val">{{ number_format($statusCounts['validated']) }}</span>
                </div>
            </div>
        </a>
        {{-- Not Valid --}}
        <a href="{{ route('sales.index', array_merge($tabBaseQuery, ['status' => 'not_valid'])) }}" class="sl-kpi-link">
            <div class="sl-kpi {{ $activeTab === 'not_valid' ? 'active-tab' : '' }}">
                <div class="sl-kpi-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <i class="mdi mdi-close-circle-outline"></i>
                </div>
                <div class="sl-kpi-info">
                    <span class="sl-kpi-label">Not Valid</span>
                    <span class="sl-kpi-val">{{ number_format($statusCounts['not_valid']) }}</span>
                </div>
            </div>
        </a>
        {{-- Recall --}}
        <a href="{{ route('sales.index', array_merge($tabBaseQuery, ['status' => 'callback'])) }}" class="sl-kpi-link">
            <div class="sl-kpi {{ $activeTab === 'callback' ? 'active-tab' : '' }}">
                <div class="sl-kpi-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <i class="mdi mdi-phone-outgoing-outline"></i>
                </div>
                <div class="sl-kpi-info">
                    <span class="sl-kpi-label">Recall</span>
                    <span class="sl-kpi-val">{{ number_format($statusCounts['callback']) }}</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Top bar: Title + Actions -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title"><i class="mdi mdi-briefcase-outline"></i> Sales</h5>
        </div>
        <div class="sl-topbar-right">
            <form method="GET" action="{{ route('sales.index') }}" id="salesSearchForm" class="sl-search-wrap">
                {{-- Preserve existing filters when searching --}}
                @foreach(request()->except(['search', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" name="search" id="salesSearch" class="sl-search-input" placeholder="Search name, phone, carrier..." value="{{ request('search') }}">
            </form>
            <button type="button" class="sl-btn sl-btn-add" data-bs-toggle="modal" data-bs-target="#manualSaleModal">
                <i class="bx bx-plus"></i> New Sale
            </button>
            @if(!auth()->user()->hasRole(Roles::QA))
                <button type="button" class="sl-btn sl-btn-import" data-bs-toggle="modal" data-bs-target="#importOldDataModal">
                    <i class="bx bx-upload"></i> Import
                </button>
            @endif
        </div>
    </div>

    <!-- Sales Card -->
    <div class="sl-card">
        <!-- Filter Pills -->
        <form method="GET" action="{{ route('sales.index') }}" id="salesFilterForm" class="sl-filter-pills">
            {{-- Preserve active status tab when other filters change --}}
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <select name="carrier" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Carriers</option>
                @foreach($carriers as $carrier)
                    <option value="{{ $carrier }}" {{ request('carrier') == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                @endforeach
            </select>
            <select name="partner" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Partners</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ request('partner') == $partner->id ? 'selected' : '' }}>{{ $partner->code }} – {{ $partner->name }}</option>
                @endforeach
            </select>
            <select name="policy_type" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">Policy Type</option>
                <option value="G.I" {{ request('policy_type') == 'G.I' ? 'selected' : '' }}>G.I</option>
                <option value="Graded" {{ request('policy_type') == 'Graded' ? 'selected' : '' }}>Graded</option>
                <option value="Level" {{ request('policy_type') == 'Level' ? 'selected' : '' }}>Level</option>
                <option value="Modified" {{ request('policy_type') == 'Modified' ? 'selected' : '' }}>Modified</option>
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" id="filter_date_from" class="sl-pill-date" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" id="filter_date_to" class="sl-pill-date" value="{{ request('date_to') }}" onchange="this.form.submit()">
            <button type="button" class="sl-pill-today" onclick="setTodayFilter()" title="Show today's sales">Today</button>
            @if(request()->hasAny(['carrier','partner','status','policy_type','date_from','date_to','search']))
                <a href="{{ route('sales.index') }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            @endif
            @if(request('search'))
                <span class="sl-pill-label" style="margin-left: .5rem;">SEARCH:</span>
                <span class="sl-search-term" style="font-size:.72rem; font-weight:600; color:#1e293b; background: rgba(212,175,55,.15); padding:.25rem .5rem; border-radius:20px;">{{ request('search') }}</span>
            @endif
        </form>

        <!-- Table -->
        <div class="sl-tbl-wrap">
            <table class="sl-tbl" id="salesTable">
                <thead>
                    <tr>
                        @if(auth()->user()->hasRole(Roles::QA))
                            <th>Client Name</th>
                            <th>Phone</th>
                            <th>Closer</th>
                            <th>Assigned Partner</th>
                            <th>Sale Date</th>
                            <th>QA Status</th>
                            <th style="min-width:200px">QA Reason</th>
                        @else
                            <th class="text-center sl-sticky-col sl-col-1" style="min-width:160px">Actions</th>
                            <th class="sl-sticky-col sl-col-2" style="min-width:150px">Client Name</th>
                            <th class="sl-sticky-col sl-col-3" style="min-width:120px">Phone</th>
                            <th style="min-width:130px">Closer</th>
                            <th style="min-width:140px">Partner</th>
                            <th style="min-width:110px">Sale Date</th>
                            <th style="min-width:160px">Carrier</th>
                            <th style="min-width:160px">Policy Type</th>
                            <th style="min-width:160px">Coverage</th>
                            <th style="min-width:150px">Premium</th>
                            <th style="min-width:110px">Settlement</th>
                            <th style="min-width:110px">Initial Draft</th>
                            <th style="min-width:110px">Future Draft</th>
                            <th style="min-width:130px">QA Status</th>
                            <th style="min-width:200px">QA Reason</th>
                            <th style="min-width:140px">QA By</th>
                            <th style="min-width:130px">Status</th>
                            <th style="min-width:130px">Sub. By</th>
                            <th style="min-width:130px">Sub. At</th>
                            <th style="min-width:130px">Validator</th>
                            <th style="min-width:130px">Validated At</th>
                            <th style="min-width:120px">Val. Status</th>
                            <th style="min-width:130px">PC By</th>
                            <th style="min-width:130px">PC At</th>
                            <th style="min-width:130px">PD By</th>
                            <th style="min-width:130px">PD At</th>
                            <th style="min-width:100px">Follow Up</th>
                            <th style="min-width:160px">Scheduled</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        @if(auth()->user()->hasRole(Roles::QA))
                                            {{-- QA View: Limited data --}}
                                            <td>
                                                <strong>{{ $lead->cn_name }}</strong>
                                                @if(in_array($lead->qa_status ?? '', ['Good', 'Avg']))
                                                    <span class="sl-qa-badge" title="QA Cleared ({{ $lead->qa_status }})">QA</span>
                                                @elseif(($lead->qa_status ?? '') === 'Bad')
                                                    <span class="sl-qa-badge-bad" title="QA Bad">QA</span>
                                                @endif
                                            </td>
                                            <td>{{ $lead->phone_number }}</td>
                                            <td>
                                                @if($lead->closer_name)
                                                    <span class="badge bg-info">{{ $lead->closer_name }}</span>
                                                    @if($lead->team === 'peregrine')
                                                        <span class="badge bg-purple ms-1" title="Peregrine Team">Peregrine</span>
                                                    @elseif($lead->team === 'ravens')
                                                        <span class="badge bg-dark ms-1" title="Ravens Team"><i class="bx bxs-star me-1" style="font-size:.65rem"></i>Ravens</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->assigned_partner)
                                                    <span class="badge bg-primary">{{ $lead->assigned_partner }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : ($lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : 'N/A') }}</td>
                                            <td>
                                                <select class="sl-bubble-select qa-status-dropdown" data-lead-id="{{ $lead->id }}" data-current-status="{{ $lead->qa_status ?? 'Pending' }}">
                                                    <option value="Pending" {{ ($lead->qa_status ?? Statuses::QA_PENDING) == Statuses::QA_PENDING ? 'selected' : '' }}>Pending</option>
                                                    <option value="Good" {{ ($lead->qa_status ?? '') == Statuses::QA_GOOD ? 'selected' : '' }}>Good</option>
                                                    <option value="Avg" {{ ($lead->qa_status ?? '') == Statuses::QA_AVG ? 'selected' : '' }}>Avg</option>
                                                    <option value="Bad" {{ ($lead->qa_status ?? '') == Statuses::QA_BAD ? 'selected' : '' }}>Bad</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="sl-bubble-textarea qa-reason-input" 
                                                          data-lead-id="{{ $lead->id }}" 
                                                          placeholder="QA comments..." 
                                                          rows="1"
>{{ $lead->qa_reason ?? '' }}</textarea>
                                                <button class="sl-save-btn primary save-qa-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                            </td>
                                        @else
                                            {{-- Full View for other roles --}}
                                            <td class="text-center sl-sticky-col sl-col-1">
                                                <div class="sl-act-group">
                                                    @php
                                                        $isDeclinedOrInvalid = $lead->submission_status === Statuses::SUB_DECLINED
                                                            || $lead->ravens_validation_status === 'not_valid';
                                                    @endphp
                                                    <a href="{{ route('sales.prettyPrint', $lead->id) }}" class="btn btn-success btn-sm" title="Pretty Print" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <a href="{{ route('sales.show', $lead->id) }}" class="btn btn-info btn-sm text-white" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @canEditModule('sales')
                                                    <a href="{{ route('sales.edit', $lead->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($isDeclinedOrInvalid)
                                                    <button type="button"
                                                        class="btn btn-sm sl-assign-back-btn"
                                                        style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff;border:none;"
                                                        data-lead-id="{{ $lead->id }}"
                                                        data-lead-name="{{ addslashes($lead->cn_name) }}"
                                                        title="Recall — send back to closer for callback">
                                                        <i class="bx bx-phone-outgoing"></i>
                                                    </button>
                                                    @endif
                                                    @endcanEditModule
                                                    @canDeleteInModule('sales')
                                                    <button type="button" class="btn btn-danger btn-sm sl-delete-lead-btn"
                                                        data-lead-id="{{ $lead->id }}"
                                                        data-lead-name="{{ addslashes($lead->cn_name) }}"
                                                        data-delete-url="{{ route('sales.delete', $lead->id) }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endcanDeleteInModule
                                                </div>
                                            </td>
                                            <td class="sl-sticky-col sl-col-2">
                                                <strong>{{ $lead->cn_name }}</strong>
                                                @if(in_array($lead->qa_status ?? '', ['Good', 'Avg']))
                                                    <span class="sl-qa-badge" title="QA Cleared ({{ $lead->qa_status }})">QA</span>
                                                @elseif(($lead->qa_status ?? '') === 'Bad')
                                                    <span class="sl-qa-badge-bad" title="QA Bad">QA</span>
                                                @endif
                                            </td>
                                            <td class="sl-sticky-col sl-col-3">{{ $lead->phone_number }}</td>
                                            <td>
                                                @if($lead->closer_name)
                                                    <span class="badge bg-info">{{ $lead->closer_name }}</span>
                                                    @if($lead->team === 'peregrine')
                                                        <span class="badge bg-purple ms-1" title="Peregrine Team">Peregrine</span>
                                                    @elseif($lead->team === 'ravens')
                                                        <span class="badge bg-dark ms-1" title="Ravens Team"><i class="bx bxs-star me-1" style="font-size:.65rem"></i>Ravens</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                                {{-- Resale badge - clickable to show detailed history --}}
                                                @if(($lead->resale_count ?? 0) > 0)
                                                    <button type="button" 
                                                            class="badge bg-warning text-dark ms-1 border-0 resale-history-btn"
                                                            style="cursor:pointer"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#resaleHistoryModal"
                                                            data-customer-name="{{ $lead->cn_name }}"
                                                            data-phone="{{ $lead->phone_number }}"
                                                            data-current-closer="{{ $lead->closer_name }}"
                                                            data-resale-log="{{ json_encode($lead->resale_log) }}">
                                                        <i class="bx bx-refresh"></i> Re-sold &times;{{ $lead->resale_count }}
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->assigned_partner)
                                                        <small class="text-muted fw-semibold">{{ $lead->assigned_partner }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    @canEditModule('sales')
                                                    <div class="sl-edit-row">
                                                        <select class="form-select form-select-sm editable-partner" data-lead-id="{{ $lead->id }}">
                                                            <option value="">-- None --</option>
                                                            @foreach($partners as $pt)
                                                                <option value="{{ $pt->id }}" {{ $lead->partner_id == $pt->id ? 'selected' : '' }}>{{ $pt->code }}{{ $pt->name ? ' — '.$pt->name : '' }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="partner" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                    @endcanEditModule
                                                </div>
                                            </td>
                                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : ($lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : 'N/A') }}</td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->carrier_name)
                                                        <small class="text-muted fw-semibold">Current: {{ $lead->carrier_name }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="sl-edit-row">
                                                        <select class="form-select form-select-sm editable-carrier" data-lead-id="{{ $lead->id }}">
                                                            <option value="">-- None --</option>
                                                            @foreach($insuranceCarriers as $carrier)
                                                                <option value="{{ $carrier }}" {{ $lead->carrier_name == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="carrier" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->policy_type)
                                                        <small class="text-muted fw-semibold">Current: {{ $lead->policy_type }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="sl-edit-row">
                                                        <select class="form-select form-select-sm editable-policy-type" data-lead-id="{{ $lead->id }}" data-carrier="{{ $lead->carrier_name }}" data-current="{{ $lead->policy_type }}">
                                                            <option value="">-- None --</option>
                                                            <option value="G.I" {{ $lead->policy_type == 'G.I' ? 'selected' : '' }}>G.I</option>
                                                            <option value="Graded" {{ $lead->policy_type == 'Graded' ? 'selected' : '' }}>Graded</option>
                                                            <option value="Level" {{ $lead->policy_type == 'Level' ? 'selected' : '' }}>Level</option>
                                                            <option value="Modified" {{ $lead->policy_type == 'Modified' ? 'selected' : '' }}>Modified</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="policy_type" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->coverage_amount)
                                                        <small class="text-muted fw-semibold">Current: ${{ number_format($lead->coverage_amount, 2) }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="sl-edit-row">
                                                        <input type="number" step="0.01" class="form-control form-control-sm editable-coverage" data-lead-id="{{ $lead->id }}" value="{{ $lead->coverage_amount ?? '' }}" placeholder="0.00">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="coverage" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->monthly_premium)
                                                        <small class="text-muted fw-semibold">Current: ${{ number_format($lead->monthly_premium, 2) }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="sl-edit-row">
                                                        <input type="number" step="0.01" class="form-control form-control-sm editable-premium" data-lead-id="{{ $lead->id }}" value="{{ $lead->monthly_premium ?? '' }}" placeholder="0.00">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="premium" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($lead->settlement_type)
                                                    <span class="badge bg-{{ $lead->settlement_type == 'level' ? 'primary' : ($lead->settlement_type == 'graded' ? 'info' : ($lead->settlement_type == 'gi' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst($lead->settlement_type) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->initial_draft_date)
                                                        <small class="text-muted fw-semibold">Current: {{ \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="sl-edit-row">
                                                        <input type="date" class="form-control form-control-sm editable-initial-draft" data-lead-id="{{ $lead->id }}" value="{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : '' }}">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="initial_draft" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->future_draft_date)
                                                        <small class="text-muted fw-semibold">Current: {{ \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    <div class="sl-edit-row">
                                                        <input type="date" class="form-control form-control-sm editable-future-draft" data-lead-id="{{ $lead->id }}" value="{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('Y-m-d') : '' }}">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="future_draft" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <select class="sl-bubble-select qa-status-dropdown" data-lead-id="{{ $lead->id }}" data-current-status="{{ $lead->qa_status ?? 'Pending' }}">
                                                    <option value="Pending" {{ ($lead->qa_status ?? Statuses::QA_PENDING) == Statuses::QA_PENDING ? 'selected' : '' }}>Pending</option>
                                                    <option value="Good" {{ ($lead->qa_status ?? '') == Statuses::QA_GOOD ? 'selected' : '' }}>Good</option>
                                                    <option value="Avg" {{ ($lead->qa_status ?? '') == Statuses::QA_AVG ? 'selected' : '' }}>Avg</option>
                                                    <option value="Bad" {{ ($lead->qa_status ?? '') == Statuses::QA_BAD ? 'selected' : '' }}>Bad</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="sl-bubble-textarea qa-reason-input" 
                                                          data-lead-id="{{ $lead->id }}" 
                                                          placeholder="QA comments..." 
                                                          rows="1"
>{{ $lead->qa_reason ?? '' }}</textarea>
                                                <button class="sl-save-btn primary save-qa-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                            </td>
                                            <td>
                                                @if($lead->qaUser)
                                                    <strong style="font-size:.75rem">{{ $lead->qaUser->name }}</strong>
                                                    @if($lead->qa_reviewed_at)
                                                        <div style="font-size:.63rem;color:#94a3b8;margin-top:1px">
                                                            {{ \Carbon\Carbon::parse($lead->qa_reviewed_at)->format('M d, h:i A') }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->submission_status === Statuses::SUB_APPROVED)
                                                    <span style="display:inline-block;padding:.2rem .5rem;background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);border-radius:.25rem;font-size:.66rem;font-weight:600;">Approved</span>
                                                @elseif($lead->submission_status === Statuses::SUB_DECLINED)
                                                    <span style="display:inline-block;padding:.2rem .5rem;background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.25);border-radius:.25rem;font-size:.66rem;font-weight:600;">Declined</span>
                                                @elseif($lead->submission_status === Statuses::SUB_UNDERWRITING)
                                                    <span style="display:inline-block;padding:.2rem .5rem;background:rgba(85,110,230,.12);color:#556ee6;border:1px solid rgba(85,110,230,.25);border-radius:.25rem;font-size:.66rem;font-weight:600;">Underwriting</span>
                                                @else
                                                    <span style="display:inline-block;padding:.2rem .5rem;background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);border-radius:.25rem;font-size:.66rem;font-weight:600;">Pending</span>
                                                @endif
                                            </td>
                                            {{-- Submission Reviewer columns --}}
                                            <td>
                                                @if($lead->submissionReviewer)
                                                    <strong style="font-size:.75rem">{{ $lead->submissionReviewer->name }}</strong>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->submission_at)
                                                    <span style="font-size:.75rem">{{ \Carbon\Carbon::parse($lead->submission_at)->format('M d, Y') }}</span>
                                                    <div style="font-size:.63rem;color:#94a3b8;margin-top:1px">
                                                        {{ \Carbon\Carbon::parse($lead->submission_at)->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            {{-- Validator columns --}}
                                            <td>
                                                @if($lead->ravens_validated_by)
                                                    <strong style="font-size:.75rem">{{ $lead->ravens_validated_by }}</strong>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->ravens_validated_at)
                                                    <span style="font-size:.75rem">{{ \Carbon\Carbon::parse($lead->ravens_validated_at)->format('M d, Y') }}</span>
                                                    <div style="font-size:.63rem;color:#94a3b8;margin-top:1px">
                                                        {{ \Carbon\Carbon::parse($lead->ravens_validated_at)->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->ravens_validation_status === 'valid')
                                                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:.7rem;padding:.3em .65em">Valid</span>
                                                @elseif($lead->ravens_validation_status === 'not_valid')
                                                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.7rem;padding:.3em .65em">Not Valid</span>
                                                @elseif($lead->ravens_validated_at)
                                                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:.7rem;padding:.3em .65em">Valid</span>
                                                @else
                                                    <span class="badge" style="background:#fef9c3;color:#78350f;font-size:.7rem;padding:.3em .65em">Awaiting</span>
                                                @endif
                                            </td>
                                            {{-- Pending Contract columns --}}
                                            <td>
                                                @if($lead->pendingContractBy)
                                                    <strong style="font-size:.75rem">{{ $lead->pendingContractBy->name }}</strong>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->pending_contract_at)
                                                    <span style="font-size:.75rem">{{ \Carbon\Carbon::parse($lead->pending_contract_at)->format('M d, Y') }}</span>
                                                    <div style="font-size:.63rem;color:#94a3b8;margin-top:1px">
                                                        {{ \Carbon\Carbon::parse($lead->pending_contract_at)->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            {{-- Pending Draft columns --}}
                                            <td>
                                                @if($lead->pendingDraftBy)
                                                    <strong style="font-size:.75rem">{{ $lead->pendingDraftBy->name }}</strong>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->pending_draft_at)
                                                    <span style="font-size:.75rem">{{ \Carbon\Carbon::parse($lead->pending_draft_at)->format('M d, Y') }}</span>
                                                    <div style="font-size:.63rem;color:#94a3b8;margin-top:1px">
                                                        {{ \Carbon\Carbon::parse($lead->pending_draft_at)->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->followup_required !== null)
                                                        <small class="text-muted fw-semibold">{{ $lead->followup_required ? 'Yes' : 'No' }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    @canEditModule('sales')
                                                    <div class="sl-edit-row">
                                                        <select class="form-select form-select-sm editable-followup-required" data-lead-id="{{ $lead->id }}">
                                                            <option value="">-- None --</option>
                                                            <option value="1" {{ $lead->followup_required ? 'selected' : '' }}>Yes</option>
                                                            <option value="0" {{ $lead->followup_required === false || $lead->followup_required === 0 ? 'selected' : '' }}>No</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="followup_required" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                    @endcanEditModule
                                                </div>
                                            </td>
                                            <td>
                                                <div class="sl-edit-cell">
                                                    @if($lead->followup_required && $lead->followup_scheduled_at)
                                                        <small class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('M d, Y h:i A') }}</small>
                                                    @else
                                                        <small class="text-danger">Not set</small>
                                                    @endif
                                                    @canEditModule('sales')
                                                    <div class="sl-edit-row">
                                                        <input type="datetime-local" class="form-control form-control-sm editable-followup-scheduled" data-lead-id="{{ $lead->id }}"
                                                            value="{{ $lead->followup_scheduled_at ? \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('Y-m-d\TH:i') : '' }}">
                                                        <button class="btn btn-sm btn-success save-field-btn" data-lead-id="{{ $lead->id }}" data-field="followup_scheduled_at" title="Save">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </div>
                                                    @endcanEditModule
                                                </div>
                                            </td>
                                        @endif
                                    </tr>


                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole(Roles::QA) ? '7' : '23' }}" class="text-center" style="padding: 3rem 1rem; color: #94a3b8;">
                                            <i class="bx bx-inbox" style="font-size:2rem; display:block; margin-bottom:.5rem; opacity:.5"></i>
                                            No sales data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $leads->appends(request()->query())->links() }}
                    </div>
    </div>

    <!-- Resale History Modal -->
    <div class="modal fade" id="resaleHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h5 class="modal-title text-white fw-semibold">
                        <i class="bx bx-history me-2"></i>Resale History
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Customer</small>
                                <div class="fw-semibold" id="resaleCustomerName">-</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Phone</small>
                                <div class="fw-semibold" id="resalePhone">-</div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Current sale is highlighted in green</small>
                        </div>
                    </div>
                    <div class="p-3">
                        <h6 class="text-muted mb-3"><i class="bx bx-time-five me-1"></i>Complete Sales History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Closer</th>
                                        <th>Carrier</th>
                                        <th>Coverage</th>
                                        <th>Premium</th>
                                        <th>Sale Date</th>
                                        <th>Team</th>
                                    </tr>
                                </thead>
                                <tbody id="resaleHistoryBody">
                                    <!-- Populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recall Modal -->
    <div class="modal fade" id="assignBackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title mb-0" style="font-size:.85rem;">
                        <i class="bx bx-phone-outgoing me-1" style="color:#8b5cf6;"></i> Recall Lead for Callback
                    </h6>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-3 py-3">
                    <p class="mb-2" style="font-size:.78rem;color:var(--bs-body-color);">
                        Lead: <strong id="assign-back-lead-name"></strong>
                    </p>
                    <p class="mb-3" style="font-size:.72rem;color:#94a3b8;">
                        This will reset the lead back to Ravens validation queue and mark it as needing a callback.
                    </p>
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Reason for Recall <span style="color:#94a3b8;font-weight:400;">(decline reason — visible to closer)</span></label>
                    <textarea id="assign-back-note" class="form-control form-control-sm" rows="3" placeholder="e.g. Client said price too high, wants to compare options — follow up next week..." style="font-size:.72rem;resize:none;"></textarea>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm text-white" id="assign-back-confirm-btn" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);border:none;">
                        <i class="bx bx-phone-outgoing me-1"></i> Recall
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shared Delete Confirmation Modal -->
    @canDeleteInModule('sales')
    <div class="modal fade" id="sharedDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-gold">Remove Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove the sale for <strong id="sharedDeleteLeadName"></strong>?<br>
                    <small class="text-muted">The lead record will be kept; only the sale data will be cleared.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="sharedDeleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Remove Sale</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcanDeleteInModule

    <!-- Import Old Data Modal -->
    <div class="modal fade" id="importOldDataModal" tabindex="-1" aria-labelledby="importOldDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importOldDataModalLabel">
                            <i class="bx bx-upload"></i> Import Old Data from Google Sheets
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Export your Google Sheets data as Excel (.xlsx) or CSV (.csv)</li>
                                <li>Ensure the file includes all necessary columns (Phone Number, Customer Name, etc.)</li>
                                <li>Maximum file size: 2MB</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="import_file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="import_file" name="import_file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Accepted formats: .xlsx, .xls, .csv</div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
/* ── Recall Modal ── */
(function() {
    let assignBackLeadId = null;
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.sl-assign-back-btn');
        if (!btn) return;
        assignBackLeadId = btn.dataset.leadId;
        document.getElementById('assign-back-lead-name').textContent = btn.dataset.leadName;
        document.getElementById('assign-back-note').value = '';
        new bootstrap.Modal(document.getElementById('assignBackModal')).show();
    });
    document.getElementById('assign-back-confirm-btn')?.addEventListener('click', function() {
        if (!assignBackLeadId) return;
        const note = document.getElementById('assign-back-note').value.trim();
        this.disabled = true;
        this.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Saving...';
        fetch(`/sales/${assignBackLeadId}/assign-back`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ recall_note: note })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('assignBackModal')).hide();
                location.reload();
            } else {
                alert(data.message || 'Error processing recall');
                this.disabled = false;
                this.innerHTML = '<i class="bx bx-phone-outgoing me-1"></i> Recall';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            this.disabled = false;
            this.innerHTML = '<i class="bx bx-phone-outgoing me-1"></i> Recall';
        });
    });
})();

/* ── Shared Delete Modal ── */
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.sl-delete-lead-btn');
    if (!btn) return;    const modal = document.getElementById('sharedDeleteModal');
    if (!modal) return;
    document.getElementById('sharedDeleteLeadName').textContent = btn.dataset.leadName;
    document.getElementById('sharedDeleteForm').action = btn.dataset.deleteUrl;
    bootstrap.Modal.getOrCreateInstance(modal).show();
});

/* ── Custom Dropdown Init ── */
(function() {
    const chevronSvg = '<svg class="sl-cdd-chevron" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg"><path d="M0 0l5 6 5-6z" fill="#94a3b8"/></svg>';

    function initCustomSelect(sel) {
        if (sel.closest('.sl-cdd')) return; // already initialized

        // Determine variant
        let variant = 'pill';
        if (sel.classList.contains('sl-bubble-select') || sel.classList.contains('qa-status-dropdown') || sel.classList.contains('manager-status-dropdown')) variant = 'bubble';
        else if (sel.closest('.sl-edit-row')) variant = 'edit';

        // Wrapper
        const wrap = document.createElement('div');
        wrap.className = 'sl-cdd ' + variant;
        sel.parentNode.insertBefore(wrap, sel);
        wrap.appendChild(sel);

        // Trigger
        const trigger = document.createElement('div');
        trigger.className = 'sl-cdd-trigger';
        const label = document.createElement('span');
        label.className = 'sl-cdd-label';
        label.textContent = sel.options[sel.selectedIndex]?.text || '';
        trigger.appendChild(label);
        trigger.insertAdjacentHTML('beforeend', chevronSvg);
        wrap.appendChild(trigger);

        // Panel
        const panel = document.createElement('div');
        panel.className = 'sl-cdd-panel';
        buildOptions(sel, panel);
        wrap.appendChild(panel);

        // Toggle
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            closeAll(wrap);
            wrap.classList.toggle('open');
        });

        // Option click
        panel.addEventListener('click', function(e) {
            const opt = e.target.closest('.sl-cdd-option');
            if (!opt) return;
            const val = opt.dataset.value;
            sel.value = val;
            label.textContent = opt.textContent;
            panel.querySelectorAll('.sl-cdd-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
            wrap.classList.remove('open');
            // Fire both native change and jQuery change
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            $(sel).trigger('change');
            // Handle onchange attribute (auto-submit filters)
            if (sel.hasAttribute('onchange')) {
                const fn = new Function(sel.getAttribute('onchange'));
                fn.call(sel);
            }
        });
    }

    function buildOptions(sel, panel) {
        panel.innerHTML = '';
        Array.from(sel.options).forEach(function(opt) {
            const div = document.createElement('div');
            div.className = 'sl-cdd-option' + (opt.selected ? ' active' : '');
            div.dataset.value = opt.value;
            div.textContent = opt.text;
            panel.appendChild(div);
        });
    }

    function closeAll(except) {
        document.querySelectorAll('.sl-cdd.open').forEach(function(w) {
            if (w !== except) w.classList.remove('open');
        });
    }

    // Close on outside click
    document.addEventListener('click', function() { closeAll(); });

    // Init on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.sl-pill-select, .sl-bubble-select, .sl-edit-row .form-select').forEach(initCustomSelect);
    });

    // Expose for dynamic content
    window.initCustomSelect = initCustomSelect;
})();
</script>
<script>
$(document).ready(function() {
    // Server-side search functionality for sales table (searches all pages)
    let searchTimeout;
    $('#salesSearch').on('keyup', function(e) {
        // Submit on Enter key immediately
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#salesSearchForm').submit();
            return;
        }
        // Debounce: submit after 600ms of no typing
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#salesSearchForm').submit();
        }, 600);
    });
    
    // Clear search on Escape key
    $('#salesSearch').on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            if ($(this).data('had-value')) {
                $('#salesSearchForm').submit();
            }
        }
    }).on('focus', function() {
        $(this).data('had-value', $(this).val() !== '');
    });

    // Init inline policy-type selects with carrier-specific options on page load
    document.querySelectorAll('.editable-policy-type').forEach(function(el) {
        var carrier = el.dataset.carrier || '';
        var current = el.dataset.current || '';
        if (carrier) window.updatePlanTypeField(carrier, el, current);
    });

    // When carrier inline select changes, rebuild peer policy-type options in the same row
    $(document).on('change', '.editable-carrier', function() {
        var leadId  = $(this).data('lead-id');
        var carrier = this.value;
        var ptSel   = document.querySelector(`.editable-policy-type[data-lead-id="${leadId}"]`);
        if (ptSel) window.updatePlanTypeField(carrier || null, ptSel);
    });

    // New Sale modal: update policy_type options when carrier name is typed
    var newSaleCarrierEl = document.getElementById('carrier_name');
    var newSalePolicyEl  = document.getElementById('policy_type');
    if (newSaleCarrierEl && newSalePolicyEl) {
        newSaleCarrierEl.addEventListener('input', function() {
            window.updatePlanTypeField(this.value.trim() || null, newSalePolicyEl);
        });
    }

    // Handle inline field updates (carrier, policy_type, coverage, premium, draft dates)
    $('.save-field-btn').click(function() {
        const button = $(this);
        const leadId = button.data('lead-id');
        const fieldType = button.data('field');
        let value = '';
        let fieldInput;
        
        // Get the value based on field type
        switch(fieldType) {
            case 'carrier':
                fieldInput = $(`.editable-carrier[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'policy_type':
                fieldInput = $(`.editable-policy-type[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'coverage':
                fieldInput = $(`.editable-coverage[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'premium':
                fieldInput = $(`.editable-premium[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'initial_draft':
                fieldInput = $(`.editable-initial-draft[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'future_draft':
                fieldInput = $(`.editable-future-draft[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'partner':
                fieldInput = $(`.editable-partner[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'followup_required':
                fieldInput = $(`.editable-followup-required[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
            case 'followup_scheduled_at':
                fieldInput = $(`.editable-followup-scheduled[data-lead-id="${leadId}"]`);
                value = fieldInput.val();
                break;
        }
        
        if (!['partner', 'followup_required', 'followup_scheduled_at'].includes(fieldType) && (!value || value === '')) {
            alert('Please enter a value');
            return;
        }
        
        button.prop('disabled', true);
        button.html('<i class="bx bx-loader bx-spin"></i>');
        
        $.ajax({
            url: `/sales/${leadId}/update-field`,
            method: 'POST',
            data: {
                field: fieldType,
                value: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.html('<i class="bx bx-check"></i>');
                    button.addClass('btn-success');
                    fieldInput.addClass('border-success');
                    
                    setTimeout(() => {
                        button.html('<i class="bx bx-check"></i>');
                        button.removeClass('btn-success');
                        fieldInput.removeClass('border-success');
                    }, 2000);
                    
                    // Show success toast
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
                            <i class="mdi mdi-check me-2"></i>
                            <strong>Updated!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('body').append(alertHtml);
                    setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
                }
            },
            error: function(xhr) {
                button.html('<i class="bx bx-check"></i>');
                alert('Failed to update field');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Handle status dropdown changes for non-QA users
    $('.status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newStatus = $(this).val();
        const dropdown = $(this);
        
        // Send AJAX request to update status
        $.ajax({
            url: `/sales/${leadId}/status`,
            method: 'POST',
            data: {
                status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    dropdown.addClass('border-success');
                    setTimeout(() => {
                        dropdown.removeClass('border-success');
                    }, 2000);
                }
            },
            error: function() {
                alert('Failed to update status');
            }
        });
    });

    // Handle QA status dropdown changes
    $('.qa-status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newQaStatus = $(this).val();
        const currentStatus = $(this).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const dropdown = $(this);
        
        // Show confirmation dialog
        if (confirm(`Are you sure you want to change the QA status to "${newQaStatus}"?\n\nNote: Only ONE change is allowed for each lead.`)) {
            updateQaStatus(leadId, newQaStatus, currentStatus, qaReason, dropdown);
        } else {
            // Reset dropdown to previous value
            dropdown.val(currentStatus);
        }
    });

    // Handle QA reason save button
    $('.save-qa-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const qaStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val();
        const currentStatus = $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status');
        const qaReason = $(`.qa-reason-input[data-lead-id="${leadId}"]`).val();
        const button = $(this);
        
        // Show confirmation dialog
        if (confirm(`Are you sure you want to save QA review with status "${qaStatus}"?\n\nNote: Only ONE change is allowed for each lead.`)) {
            updateQaStatus(leadId, qaStatus, currentStatus, qaReason, button);
        }
    });

    // Manager status dropdown and save removed — decisions now happen on Pending Submission page.
    // Super Admin reset remains below.

    // Handle reset Manager status button (Super Admin only)
    $('.reset-manager-status').click(function() {
        const leadId = $(this).data('lead-id');
        const button = $(this);
        
        if (confirm('Are you sure you want to reset this Manager status to Pending? This action is only available to Super Admin.')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: `/sales/${leadId}/manager-status/reset`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Failed to reset Manager status');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });

    function updateQaStatus(leadId, qaStatus, currentStatus, qaReason, element) {
        element.prop('disabled', true);
        
        $.ajax({
            url: `/sales/${leadId}/qa-status`,
            method: 'POST',
            data: {
                qa_status: qaStatus,
                qa_reason: qaReason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update the data attribute with new status
                    $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).data('current-status', qaStatus);
                    
                    element.addClass('border-success');
                    setTimeout(() => {
                        element.removeClass('border-success');
                    }, 2000);
                    
                    // Show success message
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            <strong>Success!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('.breadcrumb-header').after(alertHtml);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response.message) {
                    alert(response.message);
                } else {
                    alert('Failed to update QA status');
                }
                // Reset dropdown to previous value on error
                $(`.qa-status-dropdown[data-lead-id="${leadId}"]`).val(currentStatus);
            },
            complete: function() {
                element.prop('disabled', false);
            }
        });
    }

    // updateManagerStatus removed — decisions now happen on Pending Submission page only.
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-dropdown').forEach(select => {
        updateSelectColor(select);

        select.addEventListener('change', function() {
            const leadId = this.dataset.leadId;
            const newStatus = this.value;
            updateSelectColor(this);

            fetch(`/sales/${leadId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="mdi mdi-check-all me-2"></i>
                        Status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                }
            });
        });
    });

    function updateSelectColor(select) {
        const status = select.value;
        switch(status) {
            case 'pending':
                select.style.background = themeColors.goldLight;
                select.style.color = themeColors.goldDark;
                break;
            case 'approved':
                select.style.background = themeColors.surface50;
                select.style.color = themeColors.successDark;
                break;
            case 'declined':
                select.style.background = themeColors.surface50;
                select.style.color = themeColors.dangerDark;
                break;
        }
    }
});

// Resale History Modal Handler
document.querySelectorAll('.resale-history-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const customerName = this.dataset.customerName;
        const phone = this.dataset.phone;
        const resaleLog = JSON.parse(this.dataset.resaleLog || '[]');
        
        document.getElementById('resaleCustomerName').textContent = customerName;
        document.getElementById('resalePhone').textContent = phone;
        
        const tbody = document.getElementById('resaleHistoryBody');
        tbody.innerHTML = '';
        
        resaleLog.forEach((sale, index) => {
            const row = document.createElement('tr');
            if (sale.is_current) {
                row.classList.add('table-success');
            }
            const teamBadge = sale.team === 'ravens' 
                ? '<span class="badge bg-dark"><i class="bx bxs-star me-1" style="font-size:.6rem"></i>Ravens</span>'
                : sale.team === 'peregrine'
                ? '<span class="badge bg-purple">Peregrine</span>'
                : '<span class="badge bg-secondary">' + (sale.team || 'N/A') + '</span>';
            
            const coverage = sale.coverage_amount ? '$' + Number(sale.coverage_amount).toLocaleString() : '-';
            const premium = sale.monthly_premium ? '$' + Number(sale.monthly_premium).toFixed(2) : '-';
            const currentBadge = sale.is_current ? ' <span class="badge bg-success ms-1">Current</span>' : '';
            
            row.innerHTML = `
                <td>${index + 1}</td>
                <td><span class="badge bg-info">${sale.closer_name || '-'}</span>${currentBadge}</td>
                <td>${sale.carrier_name || '-'}</td>
                <td>${coverage}</td>
                <td>${premium}</td>
                <td>${sale.created_at || '-'}</td>
                <td>${teamBadge}</td>
            `;
            tbody.appendChild(row);
        });
        
        if (resaleLog.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No sales found</td></tr>';
        }
    });
});

function setTodayFilter() {
    // Use Pacific Time for "Today" filter
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Los_Angeles' });
    document.getElementById('filter_date_from').value = today;
    document.getElementById('filter_date_to').value = today;
    document.getElementById('salesFilterForm').submit();
}
</script>

<!-- Manual Sales Entry Modal -->
<div class="modal fade" id="manualSaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
 <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%)">
                <h5 class="modal-title fw-semibold">
                    <i class="mdi mdi-pencil-plus me-2"></i>Create Manual Sale Entry
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales.storeManual') }}" method="POST">
                @csrf
 <div class="modal-body u-overflow-y-auto" style="max-height: 600px">
                    <div class="row">
                        <!-- Customer Information Section -->
                        <div class="col-12">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-account-circle me-2"></i>Customer Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cn_name" class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cn_name" name="cn_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary" class="form-label fw-semibold">Beneficiary</label>
                            <input type="text" class="form-control" id="beneficiary" name="beneficiary" placeholder="Beneficiary name">
                        </div>

                        <!-- Policy Information Section -->
                        <div class="col-12 mt-2">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-file-document-outline me-2"></i>Policy Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="carrier_name" class="form-label fw-semibold">Carrier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="carrier_name" name="carrier_name" required placeholder="e.g., Guardian, AXA, etc.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="policy_type" class="form-label fw-semibold">Policy Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="policy_type" name="policy_type" required>
                                <option value="">Select Policy Type</option>
                                <option value="G.I">G.I</option>
                                <option value="Graded">Graded</option>
                                <option value="Level">Level</option>
                                <option value="Modified">Modified</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="policy_number" class="form-label fw-semibold">Policy Number</label>
                            <input type="text" class="form-control" id="policy_number" name="policy_number" placeholder="Policy #">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="coverage_amount" class="form-label fw-semibold">Coverage Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="coverage_amount" name="coverage_amount" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monthly_premium" class="form-label fw-semibold">Monthly Premium <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="monthly_premium" name="monthly_premium" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="settlement_type" class="form-label fw-semibold">Settlement Type</label>
                            <select class="form-select" id="settlement_type" name="settlement_type">
                                <option value="">Select Settlement Type</option>
                                <option value="level">Level</option>
                                <option value="graded">Graded</option>
                                <option value="gi">GI (Guaranteed Issue)</option>
                                <option value="modified">Modified</option>
                            </select>
                            <small class="text-muted">Settlement percentage type for commission calculation</small>
                        </div>

                        <!-- Transaction Information Section -->
                        <div class="col-12 mt-2">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-cash-multiple me-2"></i>Transaction Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_date" class="form-label fw-semibold">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="closer_name" class="form-label fw-semibold">Closer/Agent <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="closer_name" name="closer_name" required placeholder="Name of agent/closer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="accepted">Accepted</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Bank Information Section -->
                        <div class="col-12 mt-2">
                            <h6 class="text-gold fw-semibold mb-3">
                                <i class="mdi mdi-bank me-2"></i>Bank Information (Optional)
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label fw-semibold">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="account_number" class="form-label fw-semibold">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account #">
                        </div>

                        <!-- Additional Notes -->
                        <div class="col-12 mb-3">
                            <label for="comments" class="form-label fw-semibold">Comments</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Add any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-save me-1"></i> Create Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
