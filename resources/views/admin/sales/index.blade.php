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
        grid-template-columns: repeat(4, 1fr);
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

    /* Table area */
    .sl-tbl-wrap {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 560px;
        scrollbar-width: thin;
        scrollbar-color: #d4af37 transparent;
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
    [data-theme="dark"] .sl-page-title { color: #f1f5f9; }
    [data-theme="dark"] .sl-search-input {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    [data-theme="dark"] .sl-search-input:focus { border-color: #d4af37; }
    [data-theme="dark"] .sl-btn-import { border-color: rgba(255,255,255,.1); color: #94a3b8; }
    [data-theme="dark"] .sl-btn-import:hover { border-color: #d4af37; color: #d4af37; }
    [data-theme="dark"] .sl-kpi {
        background: rgba(30,41,59,.7); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-kpi-label { color: #94a3b8; }
    [data-theme="dark"] .sl-kpi-val { color: #f1f5f9; }
    [data-theme="dark"] .sl-card {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    [data-theme="dark"] .sl-filter-pills {
        background: rgba(15,23,42,.4); border-color: rgba(255,255,255,.05);
    }
    [data-theme="dark"] .sl-pill-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    [data-theme="dark"] .sl-pill-date {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        color-scheme: dark;
    }
    [data-theme="dark"] .sl-pill-clear { border-color: rgba(239,68,68,.3); }
    [data-theme="dark"] .sl-tbl thead th {
        background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9));
        color: #94a3b8; border-color: rgba(212,175,55,.12);
    }
    [data-theme="dark"] .sl-tbl tbody td {
        color: #cbd5e1; border-color: rgba(255,255,255,.04);
    }
    [data-theme="dark"] .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.02); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.06); }
    [data-theme="dark"] .sl-sticky-col { background: #1e293b; }
    [data-theme="dark"] .sl-tbl thead .sl-sticky-col { background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9)); }
    [data-theme="dark"] .sl-tbl tbody tr:hover .sl-sticky-col { background: rgba(30,41,59,.9); }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even) .sl-sticky-col { background: #1a2536; }
    [data-theme="dark"] .sl-tbl tbody tr:nth-child(even):hover .sl-sticky-col { background: rgba(30,41,59,.9); }
    [data-theme="dark"] .sl-col-3 { border-right-color: rgba(212,175,55,.12); }
    [data-theme="dark"] .sl-edit-cell small { color: #64748b; }
    [data-theme="dark"] .sl-edit-row .form-control,
    [data-theme="dark"] .sl-edit-row .form-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    [data-theme="dark"] .sl-bubble-select {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    [data-theme="dark"] .sl-bubble-textarea {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    [data-theme="dark"] .sl-bubble-textarea::placeholder { color: #475569; }
    [data-theme="dark"] .sl-follow-badge.no { background: rgba(100,116,139,.15); color: #94a3b8; }
    [data-theme="dark"] .sl-follow-badge.yes { background: rgba(16,185,129,.15); color: #34d399; }

    /* Dark mode: custom dropdown */
    [data-theme="dark"] .sl-cdd.pill .sl-cdd-trigger,
    [data-theme="dark"] .sl-cdd.bubble .sl-cdd-trigger,
    [data-theme="dark"] .sl-cdd.edit .sl-cdd-trigger {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
    }
    [data-theme="dark"] .sl-cdd.open .sl-cdd-trigger { border-color: #d4af37; }
    [data-theme="dark"] .sl-cdd-panel {
        background: #1e293b; border-color: rgba(255,255,255,.08);
        box-shadow: 0 8px 28px rgba(0,0,0,.35), 0 2px 8px rgba(0,0,0,.2);
    }
    [data-theme="dark"] .sl-cdd-option { color: #94a3b8; }
    [data-theme="dark"] .sl-cdd-option:hover { background: rgba(212,175,55,.1); color: #e2e8f0; }
    [data-theme="dark"] .sl-cdd-option.active { background: rgba(212,175,55,.18); color: #fbbf24; }
    [data-theme="dark"] .sl-cdd-chevron path { fill: #64748b; }

    /* Responsiveness */
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

    <!-- KPI Cards -->
    <div class="sl-kpi-row">
        @foreach($statusCounts as $status => $count)
            @php
                $iconMap = ['pending' => 'pending', 'accepted' => 'approved', 'rejected' => 'declined', 'underwritten' => 'uw'];
                $mdiMap = ['pending' => 'mdi-clock-outline', 'accepted' => 'mdi-check-circle', 'rejected' => 'mdi-close-circle', 'underwritten' => 'mdi-file-document-edit'];
                $labelMap = ['pending' => 'Pending', 'accepted' => 'Approved', 'rejected' => 'Declined', 'underwritten' => 'Underwriting'];
            @endphp
            <div class="sl-kpi">
                <div class="sl-kpi-icon {{ $iconMap[$status] ?? 'pending' }}">
                    <i class="mdi {{ $mdiMap[$status] ?? 'mdi-information' }}"></i>
                </div>
                <div class="sl-kpi-info">
                    <span class="sl-kpi-label">{{ $labelMap[$status] ?? ucfirst($status) }}</span>
                    <span class="sl-kpi-val">{{ number_format($count) }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Top bar: Title + Actions -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title"><i class="mdi mdi-briefcase-outline"></i> Sales</h5>
        </div>
        <div class="sl-topbar-right">
            <div class="sl-search-wrap">
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" id="salesSearch" class="sl-search-input" placeholder="Search name, phone, carrier...">
            </div>
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
            <select name="carrier" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Carriers</option>
                @foreach($carriers as $carrier)
                    <option value="{{ $carrier }}" {{ request('carrier') == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                @endforeach
            </select>
            <select name="status" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == Statuses::LEAD_PENDING ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ request('status') == Statuses::LEAD_ACCEPTED ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == Statuses::LEAD_REJECTED ? 'selected' : '' }}>Declined</option>
                <option value="underwritten" {{ request('status') == Statuses::LEAD_UNDERWRITTEN ? 'selected' : '' }}>Underwriting</option>
            </select>
            <select name="policy_type" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">Policy Type</option>
                <option value="G.I" {{ request('policy_type') == 'G.I' ? 'selected' : '' }}>G.I</option>
                <option value="Graded" {{ request('policy_type') == 'Graded' ? 'selected' : '' }}>Graded</option>
                <option value="Level" {{ request('policy_type') == 'Level' ? 'selected' : '' }}>Level</option>
                <option value="Modified" {{ request('policy_type') == 'Modified' ? 'selected' : '' }}>Modified</option>
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" class="sl-pill-date" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" class="sl-pill-date" value="{{ request('date_to') }}" onchange="this.form.submit()">
            @if(request()->hasAny(['carrier','status','policy_type','date_from','date_to']))
                <a href="{{ route('sales.index') }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
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
                            <th style="min-width:140px">Mgr Status</th>
                            <th style="min-width:200px">Mgr Reason</th>
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
                                            <td><strong>{{ $lead->cn_name }}</strong></td>
                                            <td>{{ $lead->phone_number }}</td>
                                            <td>
                                                @if($lead->closer_name)
                                                    <span class="badge bg-info">{{ $lead->closer_name }}</span>
                                                    @if(isset($peregrineClosers) && in_array($lead->closer_name, $peregrineClosers))
                                                        <span class="badge bg-purple ms-1" title="Peregrine Closer">Peregrine</span>
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
                                                        $zoomNumber = preg_replace('/[^\d\+]/', '', $lead->phone_number);
                                                        $callUrl = 'zoomphonecall://' . urlencode($zoomNumber);
                                                    @endphp
                                                    <a href="{{ route('sales.prettyPrint', $lead->id) }}" class="btn btn-success btn-sm" title="Pretty Print" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <button onclick="window.location.href='{{ $callUrl }}'" class="btn btn-warning btn-sm" title="Call">
                                                        <i class="fas fa-phone-alt"></i>
                                                    </button>
                                                    <a href="{{ route('sales.show', $lead->id) }}" class="btn btn-info btn-sm text-white" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @canEditModule('sales')
                                                    <a href="{{ route('sales.edit', $lead->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endcanEditModule
                                                    @canDeleteInModule('sales')
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-{{ $lead->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endcanDeleteInModule
                                                </div>
                                            </td>
                                            <td class="sl-sticky-col sl-col-2"><strong>{{ $lead->cn_name }}</strong></td>
                                            <td class="sl-sticky-col sl-col-3">{{ $lead->phone_number }}</td>
                                            <td>
                                                @if($lead->closer_name)
                                                    <span class="badge bg-info">{{ $lead->closer_name }}</span>
                                                    @if(isset($peregrineClosers) && in_array($lead->closer_name, $peregrineClosers))
                                                        <span class="badge bg-purple ms-1" title="Peregrine Closer">Peregrine</span>
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
                                                        <select class="form-select form-select-sm editable-policy-type" data-lead-id="{{ $lead->id }}">
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
                                            <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</td>
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
                                                <select class="sl-bubble-select manager-status-dropdown" data-lead-id="{{ $lead->id }}" data-current-status="{{ $lead->manager_status ?? 'pending' }}">
                                                    <option value="pending" {{ ($lead->manager_status ?? Statuses::MGR_PENDING) == Statuses::MGR_PENDING ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ ($lead->manager_status ?? '') == Statuses::MGR_APPROVED ? 'selected' : '' }}>Approved</option>
                                                    <option value="declined" {{ ($lead->manager_status ?? '') == Statuses::MGR_DECLINED ? 'selected' : '' }}>Declined</option>
                                                    <option value="underwriting" {{ ($lead->manager_status ?? '') == Statuses::MGR_UNDERWRITING ? 'selected' : '' }}>Underwriting</option>
                                                    <option value="chargeback" {{ ($lead->manager_status ?? '') == Statuses::MGR_CHARGEBACK ? 'selected' : '' }}>Chargeback</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="sl-bubble-textarea manager-reason-input" 
                                                          data-lead-id="{{ $lead->id }}" 
                                                          placeholder="Manager comments..." 
                                                          rows="1"
>{{ $lead->manager_reason ?? '' }}</textarea>
                                                <button class="sl-save-btn success save-manager-reason" data-lead-id="{{ $lead->id }}">
                                                    <i class="bx bx-save"></i> Save
                                                </button>
                                                @if(auth()->user()->hasRole(Roles::SUPER_ADMIN) && $lead->manager_status !== Statuses::MGR_PENDING)
                                                    <button class="sl-save-btn warning reset-manager-status" data-lead-id="{{ $lead->id }}" title="Reset to Pending (Super Admin only)">
                                                        <i class="bx bx-undo"></i> Reset
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->followup_required)
                                                    <span class="sl-follow-badge yes">Yes</span>
                                                @else
                                                    <span class="sl-follow-badge no">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->followup_required && $lead->followup_scheduled_at)
                                                    <span class="text-primary">
                                                        <i class="bx bx-calendar me-1"></i>
                                                        {{ \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('M d, Y h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>

                                    @if(!auth()->user()->hasRole(Roles::QA))
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="delete-{{ $lead->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-gold">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete <strong>{{ $lead->cn_name }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
 <form class="d-inline" action="{{ route('sales.delete', $lead->id) }}" method="POST" >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole(Roles::QA) ? '7' : '19' }}" class="text-center" style="padding: 3rem 1rem; color: #94a3b8;">
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
    // Realtime search functionality for sales table
    $('#salesSearch').on('keyup', function() {
        const searchValue = $(this).val().toLowerCase();
        $('.sl-tbl tbody tr').each(function() {
            const row = $(this);
            // Search across multiple columns: name, phone, carrier, closer, partner
            const clientName = row.find('td:nth-child(2)').text().toLowerCase() || row.find('td:nth-child(1)').text().toLowerCase();
            const phoneNumber = row.find('td:nth-child(3)').text().toLowerCase() || row.find('td:nth-child(2)').text().toLowerCase();
            const closer = row.find('td:nth-child(4)').text().toLowerCase() || row.find('td:nth-child(3)').text().toLowerCase();
            const partner = row.find('td:nth-child(5)').text().toLowerCase() || row.find('td:nth-child(4)').text().toLowerCase();
            const carrier = row.find('td:nth-child(7)').text().toLowerCase() || row.find('td:nth-child(6)').text().toLowerCase();
            
            // Check if any field matches the search
            if (clientName.includes(searchValue) || 
                phoneNumber.includes(searchValue) || 
                closer.includes(searchValue) || 
                partner.includes(searchValue) || 
                carrier.includes(searchValue)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // Handle inline field updates (carrier, policy_type, coverage, premium)
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
        }
        
        if (!value || value === '') {
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

    // Handle Manager status dropdown changes
    $('.manager-status-dropdown').change(function() {
        const leadId = $(this).data('lead-id');
        const newManagerStatus = $(this).val();
        const managerReason = $(`.manager-reason-input[data-lead-id="${leadId}"]`).val();
        const dropdown = $(this);
        
        updateManagerStatus(leadId, newManagerStatus, managerReason, dropdown);
    });

    // Handle Manager reason save button
    $('.save-manager-reason').click(function() {
        const leadId = $(this).data('lead-id');
        const managerStatus = $(`.manager-status-dropdown[data-lead-id="${leadId}"]`).val();
        const managerReason = $(`.manager-reason-input[data-lead-id="${leadId}"]`).val();
        const button = $(this);
        
        updateManagerStatus(leadId, managerStatus, managerReason, button);
    });

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
                        // Reset dropdown and data attribute
                        const dropdown = $(`.manager-status-dropdown[data-lead-id="${leadId}"]`);
                        dropdown.val('pending').data('current-status', 'pending');
                        $(`.manager-reason-input[data-lead-id="${leadId}"]`).val('');
                        
                        button.addClass('btn-success');
                        setTimeout(() => {
                            button.removeClass('btn-success');
                        }, 2000);
                        
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-undo me-2"></i>
                                <strong>Reset by Super Admin!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.breadcrumb-header').after(alertHtml);
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

    function updateManagerStatus(leadId, managerStatus, managerReason, element) {
        element.prop('disabled', true);
        
        $.ajax({
            url: `/sales/${leadId}/manager-status`,
            method: 'POST',
            data: {
                manager_status: managerStatus,
                manager_reason: managerReason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
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
            error: function() {
                alert('Failed to update Manager status');
            },
            complete: function() {
                element.prop('disabled', false);
            }
        });
    }
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
            case 'underwriting':
                select.style.background = themeColors.surface50;
                select.style.color = themeColors.infoDark;
                break;
            case 'chargeback':
                select.style.background = themeColors.surface50;
                select.style.color = themeColors.dangerDark;
                break;
        }
    }
});
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
                                <option value="chargeback">Chargeback</option>
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
