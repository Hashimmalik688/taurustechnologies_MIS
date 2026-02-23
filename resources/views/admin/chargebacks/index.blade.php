@extends('layouts.master')

@section('title')
    Chargebacks Management
@endsection

@section('css')
<style>
    /* ── Chargebacks Page Design System (.sl-* namespace, matching Sales) ── */

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

    /* KPI summary pills */
    .sl-kpi-row { display: flex; gap: .6rem; flex-wrap: wrap; }
    .sl-kpi-pill {
        display: flex; align-items: center; gap: .5rem;
        padding: .5rem .85rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.06);
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
    }
    .sl-kpi-pill .kpi-icon {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; font-size: .9rem;
    }
    .sl-kpi-pill .kpi-label {
        font-size: .64rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #94a3b8; line-height: 1.1;
    }
    .sl-kpi-pill .kpi-value { font-size: 1.1rem; font-weight: 800; line-height: 1; }

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
        padding: .32rem .55rem; border-radius: 22px !important;
        border: 1px solid rgba(0,0,0,.08) !important;
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
    .sl-pill-date { min-width: 100px; max-width: 130px; color-scheme: light; }
    .sl-pill-select:focus, .sl-pill-date:focus { border-color: #d4af37 !important; box-shadow: 0 0 0 2px rgba(212,175,55,.12); }
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
    .sl-pill-clear:hover { background: rgba(239,68,68,.08); color: #dc2626; }
    .sl-result-count { font-size: .72rem; font-weight: 600; color: #94a3b8; margin-left: auto; }

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
    .sl-tbl tfoot td {
        padding: .45rem; font-weight: 700; color: #334155;
        border-top: 2px solid rgba(212,175,55,.18);
        background: rgba(248,250,252,.6);
    }

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
    .sl-act-group .btn-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }

    /* Badges */
    .sl-tbl .badge { font-size: .68rem; font-weight: 600; padding: .25rem .55rem; border-radius: 22px; }

    /* Pagination */
    .sl-card .mt-3 { padding: 0 1rem .75rem; }
    .sl-card .pagination svg { max-width: 16px !important; max-height: 16px !important; }

    /* ── Dark mode ── */
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title { color: #f1f5f9; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input {
        background: rgba(30,41,59,.8); border-color: rgba(255,255,255,.1); color: #e2e8f0;
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input:focus { border-color: #d4af37; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi-pill {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-kpi-pill .kpi-label { color: #64748b; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card {
        background: rgba(30,41,59,.65); border-color: rgba(255,255,255,.06);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills {
        background: rgba(15,23,42,.4); border-color: rgba(255,255,255,.05);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select,
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date {
        background: rgba(30,41,59,.8) !important; border-color: rgba(255,255,255,.1) !important; color: #cbd5e1;
        color-scheme: dark;
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-label { color: #64748b; }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-clear { border-color: rgba(239,68,68,.3); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th {
        background: linear-gradient(180deg, rgba(15,23,42,.95), rgba(15,23,42,.9));
        color: #94a3b8; border-color: rgba(212,175,55,.12);
    }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td { color: #cbd5e1; border-color: rgba(255,255,255,.04); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:hover td { background: rgba(212,175,55,.06); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even) td { background: rgba(255,255,255,.02); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody tr:nth-child(even):hover td { background: rgba(212,175,55,.06); }
    :is(:is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]),[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tfoot td {
        background: rgba(15,23,42,.6); border-color: rgba(212,175,55,.12); color: #e2e8f0;
    }

    @media (max-width: 768px) {
        .sl-topbar { flex-direction: column; align-items: flex-start; }
        .sl-topbar-right { width: 100%; }
        .sl-search-input { width: 100% !important; }
        .sl-kpi-row { flex-direction: column; }
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

    <!-- Top bar -->
    <div class="sl-topbar">
        <div class="sl-topbar-left">
            <h5 class="sl-page-title"><i class="bx bx-error"></i> Chargebacks</h5>
        </div>
        <div class="sl-topbar-right">
            <div class="sl-search-wrap">
                <i class="bx bx-search sl-search-icon"></i>
                <input type="text" id="cbSearch" class="sl-search-input" placeholder="Search name, phone, carrier, closer..." value="{{ $search }}">
            </div>
        </div>
    </div>

    <!-- KPI summary pills -->
    <div class="sl-kpi-row mb-3">
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-danger-subtle text-danger">
                <i class="bx bx-error"></i>
            </div>
            <div>
                <div class="kpi-label">Total Chargebacks</div>
                <div class="kpi-value text-danger">{{ $total_count }}</div>
            </div>
        </div>
        <div class="sl-kpi-pill">
            <div class="kpi-icon bg-warning-subtle text-warning">
                <i class="bx bx-dollar-circle"></i>
            </div>
            <div>
                <div class="kpi-label">Total Amount</div>
                <div class="kpi-value text-warning">${{ number_format($total_amount, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Chargebacks Card -->
    <div class="sl-card">
        <form method="GET" action="{{ route('chargebacks.index') }}" id="cbFilterForm" class="sl-filter-pills">
            <select name="month" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="sl-pill-select" onchange="this.form.submit()">
                <option value="">All Years</option>
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <span class="sl-pill-label">FROM</span>
            <input type="date" name="date_from" class="sl-pill-date" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <span class="sl-pill-label">TO</span>
            <input type="date" name="date_to" class="sl-pill-date" value="{{ request('date_to') }}" onchange="this.form.submit()">
            @if(request()->hasAny(['search', 'month', 'year', 'date_from', 'date_to']))
                <a href="{{ route('chargebacks.index') }}" class="sl-pill-clear" title="Clear filters"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="sl-result-count">{{ $chargebacks->total() }} chargebacks</span>
        </form>

        <div class="sl-tbl-wrap">
            <table class="sl-tbl">
                <thead>
                    <tr>
                        <th style="min-width:100px">Sale Date</th>
                        <th style="min-width:150px">Customer</th>
                        <th style="min-width:120px">Closer</th>
                        <th style="min-width:120px">Agent Assigned</th>
                        <th style="min-width:110px">Carrier</th>
                        <th style="min-width:120px">CB Amount</th>
                        <th style="min-width:180px">Comments</th>
                        <th style="min-width:180px">Manager Reason</th>
                        <th style="min-width:70px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chargebacks as $lead)
                        <tr>
                            <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $lead->phone_number ?? '' }}</small>
                            </td>
                            <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                            <td>{{ $lead->managedBy->name ?? 'Unassigned' }}</td>
                            <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                            <td><span class="badge bg-danger">${{ number_format($lead->monthly_premium ?? 0, 2) }}</span></td>
                            <td><span class="text-muted" style="font-size:.74rem">{{ Str::limit($lead->comments ?? 'No reason provided', 50) }}</span></td>
                            <td><span class="text-muted" style="font-size:.74rem">{{ $lead->manager_reason ?? 'No comments' }}</span></td>
                            <td>
                                <div class="sl-act-group">
                                    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-info" title="View Details" target="_blank">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bx bx-info-circle" style="font-size:2rem;color:#94a3b8"></i>
                                <p class="mb-0 text-muted" style="font-size:.82rem">No chargebacks found for the selected period</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($chargebacks->count() > 0)
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end">Total:</td>
                            <td><span class="badge bg-danger">${{ number_format($total_amount, 2) }}</span></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="mt-3">
            {{ $chargebacks->appends(['search' => $search, 'month' => $month, 'year' => $year, 'date_from' => $date_from ?? '', 'date_to' => $date_to ?? ''])->links() }}
        </div>
    </div>
@endsection

@section('script')
@include('partials.sl-filter-assets')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('cbSearch');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const form = document.getElementById('cbFilterForm');
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
});
</script>
@endsection
