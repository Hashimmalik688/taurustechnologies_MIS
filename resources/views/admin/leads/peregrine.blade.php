@extends('layouts.master')

@section('title')
    Peregrine Leads
@endsection

@section('css')
<style>
    /* ── Leads Page Design System (.sl-* namespace, matching Sales) ── */

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
    .sl-search-wrap { position: relative; display: flex; align-items: center; }
    .sl-search-icon { position: absolute; left: .6rem; color: #94a3b8; font-size: .9rem; pointer-events: none; }
    .sl-search-input {
        padding: .42rem .65rem .42rem 2rem;
        font-size: .78rem; border: 1px solid rgba(0,0,0,.1);
        border-radius: 22px; background: #fff; width: 220px;
        outline: none; transition: border-color .15s;
    }
    .sl-search-input:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }

    /* Action buttons */
    .sl-btn {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .42rem .8rem; font-size: .75rem; font-weight: 700;
        border-radius: 22px; border: none; cursor: pointer;
        transition: all .15s; white-space: nowrap; text-decoration: none;
    }
    .sl-btn-add {
        background: linear-gradient(135deg, #d4af37, #b8941f); color: #0f172a;
    }
    .sl-btn-add:hover { background: linear-gradient(135deg, #e0c04c, #d4af37); transform: translateY(-1px); color: #0f172a; }
    .sl-btn-import {
        background: transparent; border: 1px solid rgba(0,0,0,.12); color: #475569;
    }
    .sl-btn-import:hover { border-color: #d4af37; color: #d4af37; }

    /* Card */
    .sl-card {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
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
    .sl-pill-select, .sl-pill-date {
        font-size: .72rem; font-weight: 600;
        padding: .32rem .55rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff; color: #475569;
        cursor: pointer; outline: none;
        transition: border-color .15s;
    }
    .sl-pill-select {
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .5rem center;
        padding-right: 1.5rem;
        max-width: 180px;
    }
    .sl-pill-date {
        min-width: 100px; max-width: 120px;
        color-scheme: light;
    }
    .sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
    .sl-pill-label {
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #94a3b8; margin-right: -2px;
    }
    .sl-pill-clear {
        font-size: .68rem; font-weight: 600; color: #ef4444;
        text-decoration: none; padding: .25rem .5rem;
        border-radius: 22px; border: 1px solid rgba(239,68,68,.2);
        display: inline-flex; align-items: center; gap: 2px;
        transition: all .15s;
    }
    .sl-result-count {
        font-size: .72rem; font-weight: 600; color: #94a3b8;
        margin-left: auto;
    }
    .sl-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }

    /* Table area */
    .sl-tbl-wrap {
        overflow-x: auto; overflow-y: auto;
        max-height: 560px;
        scrollbar-width: thin; scrollbar-color: #d4af37 transparent;
    }
    .sl-tbl-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
    .sl-tbl-wrap::-webkit-scrollbar-track { background: transparent; }
    .sl-tbl-wrap::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 3px; }

    .sl-tbl {
        width: 100%; border-collapse: separate; border-spacing: 0; font-size: .78rem;
    }
    .sl-tbl thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #64748b;
        padding: .45rem .45rem;
        border-bottom: 1px solid rgba(212,175,55,.18);
        white-space: nowrap;
        position: sticky; top: 0; z-index: 10;
    }
    .sl-tbl tbody td {
        padding: .35rem .45rem;
        border-bottom: 1px solid rgba(0,0,0,.04);
        vertical-align: middle; color: #334155;
        transition: background .12s;
    }
    .sl-tbl tbody tr { transition: background .12s; }
    .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.045); }
    .sl-tbl tbody tr:nth-child(even) td { background: rgba(248,250,252,.45); }
    .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.045); }

    /* Sticky first 3 columns */
    .sl-sticky-col { position: sticky; background: #fff; }
    .sl-col-1 { left: 0; z-index: 5; }
    .sl-col-2 { display:none !important; }
    .sl-col-3 { left: 40px; z-index: 6; }
    .sl-col-4 { left: 180px; z-index: 7; border-right: 2px solid rgba(212,175,55,.15); }
    .sl-tbl thead .sl-sticky-col { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); }
    .sl-tbl thead .sl-col-1 { z-index: 15; }
    .sl-tbl thead .sl-col-3 { z-index: 16; }
    .sl-tbl thead .sl-col-4 { z-index: 17; border-right: 2px solid rgba(212,175,55,.15); }
    .sl-tbl tbody tr:hover .sl-sticky-col { background: rgba(255,252,240,1); }
    .sl-tbl tbody tr:nth-child(even) .sl-sticky-col { background: #fafbfc; }
    .sl-tbl tbody tr:nth-child(even):hover .sl-sticky-col { background: rgba(255,252,240,1); }

    /* Pseudo-elements to cover inter-column gaps */
    .sl-col-1::after,
    .sl-col-3::after,
    .sl-col-3::before,
    .sl-col-4::before {
        content: '';
        position: absolute;
        top: -1px;
        bottom: -1px;
        width: 8px;
        background: inherit;
        pointer-events: none;
    }
    .sl-col-1::after { right: -8px; }
    .sl-col-3::before { left: -8px; }
    .sl-col-3::after { right: -8px; }
    .sl-col-4::before { left: -8px; }

    /* Action buttons */
    .sl-act-group { display: flex; gap: 4px; justify-content: center; }
    .sl-act-group .btn {
        width: 28px; height: 28px; padding: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; font-size: .68rem;
        border: none; color: #fff; transition: all .15s;
        box-shadow: 0 1px 3px rgba(0,0,0,.1);
    }
    .sl-act-group .btn:hover { transform: scale(1.1); box-shadow: 0 3px 10px rgba(0,0,0,.15); }
    .sl-act-group .btn-secondary { background: linear-gradient(135deg, #64748b, #475569); }
    .sl-act-group .btn-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .sl-act-group .btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .sl-act-group .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

    /* Editable comment */
    .sl-editable-comment {
        min-width: 150px; max-width: 300px;
        padding: 4px 8px; border-radius: 16px;
        border: 1px solid rgba(0,0,0,.06);
        font-size: .74rem; color: #475569;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .sl-editable-comment:focus {
        border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12);
    }

    /* Badges */
    .sl-tbl .badge { font-size: .68rem; font-weight: 600; padding: .25rem .55rem; border-radius: 22px; }
    .bg-purple { background-color: var(--bs-ui-purple, #6f42c1) !important; color: #fff !important; }

    /* Pagination */
    .sl-card .mt-3 { padding: 0 1rem .75rem; }
    .sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

    /* ── Dark mode ── */
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: #f1f5f9; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input:focus { border-color: #d4af37; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-btn-import { border-color: rgba(255,255,255,.1); color: #94a3b8; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-btn-import:hover { border-color: #d4af37; color: #d4af37; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills {
        background: rgba(15,23,42,.4); border-color: rgba(255,255,255,.05);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select,
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #cbd5e1;
        color-scheme: dark;
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-clear { border-color: rgba(239,68,68,.3); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th {
        background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9));
        color: #94a3b8; border-color: rgba(212,175,55,.12);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td {
        color: #cbd5e1; border-color: rgba(255,255,255,.04);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.02); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.06); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-sticky-col { background: #1e293b; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead .sl-sticky-col { background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9)); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover .sl-sticky-col { background: rgba(30,41,59,.9); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even) .sl-sticky-col { background: #1a2536; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even):hover .sl-sticky-col { background: rgba(30,41,59,.9); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-col-4 { border-right-color: rgba(212,175,55,.12); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-editable-comment {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.08); color: #e2e8f0;
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-editable-comment:focus { border-color: #d4af37; }

    /* Responsiveness */
    @media (max-width: 768px) {
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
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Top bar -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title"><i class="mdi mdi-bird"></i> Peregrine Leads</h5>
        </div>
        <div class="sl-topbar-right">
            <div class="sl-search-wrap">
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" id="leadsSearch" class="sl-search-input" placeholder="Search name, phone, SSN, carrier..." value="{{ request('search') }}">
            </div>
            <a href="{{ route('leads.create') }}" class="sl-btn sl-btn-add">
                <i class="bx bx-plus"></i> New Lead
            </a>
            <button type="button" class="sl-btn sl-btn-import" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-upload"></i> Import
            </button>
        </div>
    </div>

    <!-- Leads Card -->
    <div class="sl-card">
        <!-- Filter Pills -->
        <form method="GET" action="{{ route('leads.peregrine') }}" id="leadsFilterForm" class="sl-filter-pills">
            <select name="status" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="lead_closed" {{ request('status') == 'lead_closed' ? 'selected' : '' }}>Closed</option>
                <option value="lead_accepted" {{ request('status') == 'lead_accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="lead_pending" {{ request('status') == 'lead_pending' ? 'selected' : '' }}>Pending</option>
                <option value="lead_rejected" {{ request('status') == 'lead_rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="carrier" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Carriers</option>
                @foreach($carriers as $carrier)
                    <option value="{{ $carrier }}" {{ request('carrier') == $carrier ? 'selected' : '' }}>{{ $carrier }}</option>
                @endforeach
            </select>
            <select name="closer" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Closers</option>
                @foreach($closerNames as $closer)
                    <option value="{{ $closer }}" {{ request('closer') == $closer ? 'selected' : '' }}>{{ $closer }}</option>
                @endforeach
            </select>
            <select name="state" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" class="sl-pill-date" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" class="sl-pill-date" value="{{ request('date_to') }}" onchange="this.form.submit()">
            @if(request()->hasAny(['status','carrier','closer','state','date_from','date_to','search']))
                <a href="{{ route('leads.peregrine') }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="sl-result-count">{{ $leads->total() }} leads</span>
        </form>

        <!-- Table -->
        <div class="sl-tbl-wrap">
            <table class="sl-tbl" id="leadsTable">
                <thead>
                    <tr>
                        <th class="sl-sticky-col sl-col-1" style="width:40px">#</th>
                        <th class="sl-sticky-col sl-col-3" style="min-width:140px">Customer Name</th>
                        <th class="sl-sticky-col sl-col-4" style="min-width:120px">Actions</th>
                        <th>Phone Number</th>
                        <th>DOB</th>
                        <th>Smoker</th>
                        <th>DL #</th>
                        <th>Height</th>
                        <th>Weight</th>
                        <th>Birth Place</th>
                        <th>Medical Issue</th>
                        <th>Medications</th>
                        <th>Doc Name</th>
                        <th>S.S.N #</th>
                        <th>Street Address</th>
                        <th>State</th>
                        <th>Zip</th>
                        <th>Carrier</th>
                        <th>Coverage</th>
                        <th>Monthly Premium</th>
                        <th>Beneficiary</th>
                        <th>Emergency Contact</th>
                        <th>Initial Draft</th>
                        <th>Future Draft</th>
                        <th>Bank</th>
                        <th>Acc Type</th>
                        <th>Routing #</th>
                        <th>Acc #</th>
                        <th>Card Info</th>
                        <th>Policy Type</th>
                        <th>Source</th>
                        <th>Closer</th>
                        <th>Verified By</th>
                        <th>Balance / SS</th>
                        <th>SS Date</th>
                        <th>Preset Line</th>
                        <th style="min-width:180px">Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $index => $lead)
                        <tr>
                            <td class="sl-sticky-col sl-col-1"><strong>{{ $leads->firstItem() + $index }}</strong></td>
                            <td class="sl-sticky-col sl-col-3"><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td class="sl-sticky-col sl-col-4">
                                @php
                                    $zoomNumber = preg_replace('/[^\d\+]/', '', $lead->phone_number);
                                    $callUrl = 'zoomphonecall://' . urlencode($zoomNumber);
                                @endphp
                                <div class="sl-act-group">
                                    <button onclick="window.location.href='{{ $callUrl }}'" class="btn btn-secondary" title="Call">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @canEditModule('leads-peregrine')
                                    <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcanEditModule
                                    @canDeleteInModule('leads-peregrine')
                                    <form class="d-inline" action="{{ route('leads.delete', $lead->id) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($lead->cn_name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcanDeleteInModule
                                </div>
                            </td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td>{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $lead->smoker ? 'Yes' : 'No' }}</td>
                            <td>{{ $lead->driving_license_number ?? 'N/A' }}</td>
                            <td>{{ $lead->height ?? 'N/A' }}</td>
                            <td>{{ $lead->weight ?? 'N/A' }}</td>
                            <td>{{ $lead->birth_place ?? 'N/A' }}</td>
                            <td>{{ Str::limit($lead->medical_issue ?? 'N/A', 30) }}</td>
                            <td>{{ Str::limit($lead->medications ?? 'N/A', 30) }}</td>
                            <td>{{ $lead->doctor_name ?? 'N/A' }}</td>
                            <td>{{ $lead->ssn ? '***-**-' . substr($lead->ssn, -4) : 'N/A' }}</td>
                            <td>{{ Str::limit($lead->address ?? 'N/A', 40) }}</td>
                            <td>{{ $lead->state ?? 'N/A' }}</td>
                            <td>{{ $lead->zip_code ?? 'N/A' }}</td>
                            <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                            <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                            <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                            <td>{{ $lead->beneficiary ?? 'N/A' }}</td>
                            <td>{{ $lead->emergency_contact ?? 'N/A' }}</td>
                            <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $lead->bank_name ?? 'N/A' }}</td>
                            <td>{{ $lead->account_type ?? 'N/A' }}</td>
                            <td>{{ $lead->routing_number ?? 'N/A' }}</td>
                            <td>{{ $lead->acc_number ?? 'N/A' }}</td>
                            <td>{{ $lead->card_number ? '****' . substr($lead->card_number, -4) : 'N/A' }}</td>
                            <td>{{ $lead->policy_type ?? 'N/A' }}</td>
                            <td>{{ $lead->source ?? 'N/A' }}</td>
                            <td>
                                {{ $lead->closer_name ?? 'N/A' }}
                                @if($lead->closer_name && isset($peregrineClosers) && in_array($lead->closer_name, $peregrineClosers))
                                    <span class="badge bg-purple ms-1">Peregrine</span>
                                @endif
                            </td>
                            <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                            <td>{{ $lead->bank_balance ? '$' . number_format($lead->bank_balance, 2) : ($lead->ss_amount ? '$' . number_format($lead->ss_amount, 2) : 'N/A') }}</td>
                            <td>{{ $lead->ss_date ? \Carbon\Carbon::parse($lead->ss_date)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $lead->preset_line ?? 'N/A' }}</td>
                            <td>
                                <div contenteditable="true" class="sl-editable-comment" data-lead-id="{{ $lead->id }}">{{ $lead->comments ?? 'Click to add...' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="38" class="text-center py-4">
                                <i class="bx bx-user-plus" style="font-size:2rem;color:#94a3b8"></i>
                                <p class="mb-0 text-muted" style="font-size:.82rem">No leads found. Add or import leads to get started.</p>
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Leads</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload Excel File</label>
                            <input type="file" class="form-control" name="import_file" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Accepted formats: .xlsx, .xls, .csv (Max: <strong>100MB</strong>)</small>
                        </div>
                        <div class="alert alert-info mb-2">
                            <strong><i class="bx bx-info-circle"></i> Deduplication:</strong>
                            <small>System checks for duplicates using <strong>Phone Number</strong>, <strong>SSN</strong>, or <strong>Account Number</strong>. Existing leads will be updated with missing data.</small>
                        </div>
                        <div class="alert alert-success mb-0">
                            <strong><i class="bx bx-columns"></i> Flexible Column Names:</strong>
                            <small>
                                <ul class="mb-0" style="font-size:.85rem">
                                    <li><strong>Phone:</strong> "Phone Number", "Phone", "Cell Phone", "Mobile"</li>
                                    <li><strong>Name:</strong> "Customer Name", "Name", "CN Name"</li>
                                    <li><strong>DOB:</strong> "Date of Birth", "DOB", "Birth Date"</li>
                                    <li><strong>SSN:</strong> "SSN", "S.S.N #", "Social Security Number"</li>
                                    <li>Plus 40+ more variations automatically recognized!</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-upload me-1"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
@include('partials.sl-filter-assets')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Instant search with debounce
    const searchInput = document.getElementById('leadsSearch');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const form = document.getElementById('leadsFilterForm');
                let hidden = form.querySelector('input[name="search"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = 'search';
                    form.appendChild(hidden);
                }
                hidden.value = this.value.trim();
                form.submit();
            }, 600);
        });
        if (searchInput.value) {
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    }

    // Editable comments
    document.querySelectorAll('.sl-editable-comment').forEach(comment => {
        comment.addEventListener('blur', function() {
            const leadId = this.dataset.leadId;
            const newComment = this.textContent.trim();
            if (newComment === 'Click to add...') return;
            fetch(`/leads/${leadId}/update-comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ comments: newComment })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.style.borderColor = '#10b981';
                    setTimeout(() => { this.style.borderColor = ''; }, 1000);
                }
            })
            .catch(() => { this.style.borderColor = '#ef4444'; });
        });
        comment.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); this.blur(); }
        });
    });
});
</script>
@endsection