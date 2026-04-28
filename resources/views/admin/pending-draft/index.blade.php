@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Pending Draft')

@section('css')
<style>
/* ── KPI Cards ── */
.pd-kpi-row { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
.pd-kpi-card {
    flex: 1 1 180px;
    min-width: 160px;
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: .75rem;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
}
.pd-kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.pd-kpi-card.active { border-color: var(--bs-primary); box-shadow: 0 0 0 2px rgba(212,175,55,.2); }
.pd-kpi-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.pd-kpi-icon.i-gold { background: rgba(212,175,55,.12); color: #d4af37; }
.pd-kpi-icon.i-blue { background: rgba(85,110,230,.12); color: #556ee6; }
.pd-kpi-icon.i-red { background: rgba(244,106,106,.12); color: #f46a6a; }
.pd-kpi-icon.i-green { background: rgba(52,195,143,.12); color: #34c38f; }
.pd-kpi-info .k-val { font-size: 1.75rem; font-weight: 700; line-height: 1.1; color: var(--bs-body-color); }
.pd-kpi-info .k-lbl { font-size: .72rem; color: var(--bs-surface-400); text-transform: uppercase; letter-spacing: .5px; margin-top: .15rem; }

/* ── Modern Card ── */
.pd-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: .75rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.pd-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .85rem 1.25rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    flex-wrap: wrap;
    gap: .5rem;
}
.pd-card-header h6 { margin: 0; font-size: .88rem; font-weight: 600; display: flex; align-items: center; gap: .5rem; }
.pd-card-header h6 i { color: #f1b44c; font-size: 1.1rem; }

/* ── Tabs ── */
.pd-tabs-wrap { padding: .75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,.05); background: rgba(0,0,0,.01); }
.pd-tabs { display: inline-flex; gap: .25rem; background: rgba(0,0,0,.03); padding: .25rem; border-radius: .5rem; }
.pd-tab {
    padding: .45rem 1rem;
    font-size: .75rem;
    font-weight: 600;
    border-radius: .35rem;
    cursor: pointer;
    text-decoration: none;
    color: var(--bs-surface-500);
    background: transparent;
    border: none;
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: .35rem;
}
.pd-tab:hover { color: var(--bs-body-color); background: rgba(255,255,255,.5); }
.pd-tab.active { background: var(--bs-card-bg); color: var(--bs-primary); box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.pd-tab .badge { font-size: .65rem; padding: .2rem .4rem; border-radius: .25rem; font-weight: 600; }
.pd-tab .badge-blue { background: rgba(85,110,230,.15); color: #556ee6; }
.pd-tab .badge-red { background: rgba(244,106,106,.15); color: #f46a6a; }

/* ── Filters ── */
.pd-filters {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem;
    padding: 1rem 1.25rem;
    align-items: flex-end;
    border-bottom: 1px solid rgba(0,0,0,.04);
}
.pd-filter-group { display: flex; flex-direction: column; gap: .25rem; }
.pd-filter-group label {
    font-size: .68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--bs-surface-400);
}
.pd-filter-group input, .pd-filter-group select {
    font-size: .75rem;
    padding: .4rem .65rem;
    border: 1px solid rgba(0,0,0,.1);
    border-radius: .4rem;
    background: var(--bs-card-bg);
    color: var(--bs-body-color);
    height: 2.2rem;
}
.pd-filter-group input:focus, .pd-filter-group select:focus {
    outline: none;
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 2px rgba(212,175,55,.15);
}
.pd-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .45rem .85rem;
    font-size: .75rem;
    font-weight: 600;
    border-radius: .4rem;
    border: none;
    cursor: pointer;
    transition: all .15s;
    height: 2.2rem;
}
.pd-filter-btn.btn-search { background: var(--bs-primary); color: #fff; }
.pd-filter-btn.btn-search:hover { filter: brightness(1.1); }
.pd-filter-btn.btn-clear { background: transparent; color: var(--bs-surface-400); text-decoration: none; }
.pd-filter-btn.btn-clear:hover { color: var(--bs-body-color); }

/* ── Table ── */
.pd-table { width: 100%; font-size: .78rem; border-collapse: collapse; }
.pd-table thead th {
    padding: .65rem 1rem;
    font-weight: 600;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--bs-surface-400);
    white-space: nowrap;
    border-bottom: 1px solid rgba(0,0,0,.07);
    background: rgba(0,0,0,.01);
}
.pd-table tbody td {
    padding: .75rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid rgba(0,0,0,.04);
}
.pd-table tbody tr:hover { background: rgba(212,175,55,.03); }
.pd-table tbody tr:last-child td { border-bottom: 0; }

/* ── Badges ── */
.bd-mini {
    display: inline-block;
    font-size: .68rem;
    padding: .22rem .55rem;
    border-radius: .3rem;
    font-weight: 600;
    white-space: nowrap;
}
.bd-teal { background: rgba(80,165,241,.12); color: #3a8fd2; }
.bd-gold { background: rgba(212,175,55,.12); color: #b89730; }
.bd-blue { background: rgba(85,110,230,.12); color: #556ee6; }
.bd-green { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-red { background: rgba(244,106,106,.12); color: #c84646; }
.bd-gray { background: rgba(108,117,125,.12); color: #6c757d; }
.bd-orange { background: rgba(241,180,76,.12); color: #b87a14; }

/* ── Action Buttons ── */
.a-btn {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .35rem .65rem;
    border-radius: .4rem;
    font-size: .72rem;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.a-btn:disabled { opacity: .6; cursor: not-allowed; }
.a-paid { background: rgba(52,195,143,.12); color: #1a8754; border-color: rgba(52,195,143,.3); }
.a-paid:hover:not(:disabled) { background: rgba(52,195,143,.2); }
.a-fdfp { background: rgba(244,106,106,.12); color: #c84646; border-color: rgba(244,106,106,.3); }
.a-fdfp:hover:not(:disabled) { background: rgba(244,106,106,.2); }
.a-died { background: rgba(108,117,125,.12); color: #495057; border-color: rgba(108,117,125,.3); }
.a-died:hover:not(:disabled) { background: rgba(108,117,125,.2); }
.a-clear { background: rgba(85,110,230,.12); color: #556ee6; border-color: rgba(85,110,230,.3); }
.a-clear:hover:not(:disabled) { background: rgba(85,110,230,.2); }
.a-back { background: rgba(220,53,69,.08); color: #dc3545; border-color: rgba(220,53,69,.2); }
.a-back:hover:not(:disabled) { background: rgba(220,53,69,.15); }

/* ── Empty State ── */
.pd-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    color: var(--bs-surface-400);
}
.pd-empty i { font-size: 2.5rem; opacity: .3; margin-bottom: .75rem; display: block; }
.pd-empty p { font-size: .8rem; margin: 0; }

/* ── Meta Info ── */
.pd-meta { font-size: .65rem; color: var(--bs-surface-400); margin-top: .15rem; }
/* Prominent page title */
.sl-page-title{font-size:1.35rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px;margin:0;}
.sl-page-title i{color:#d4af37;font-size:1.5rem;}
.sl-page-subtitle{font-size:.78rem;color:#94a3b8;margin:0;}
[data-bs-theme=dark] .sl-page-title,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
</style>
@endsection

@section('content')
<div class="container-fluid px-3 py-3" style="max-width:1600px">

    <!-- Sales Flow Navigation -->
    <x-sales-flow-navigation currentStage="draft" />

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="sl-page-title"><i class="bx bx-time-five"></i> Pending Draft</h1>
            <p class="sl-page-subtitle mt-1">Stage 6 — Awaiting first premium draft confirmation</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('pending-draft.export', array_filter(['tab' => $tab, 'search' => $search, 'carrier' => $carrier, 'partner' => $partner, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
               target="_blank"
               class="a-btn"
               style="background:rgba(212,175,55,.1);color:#b89730;border:1px solid rgba(212,175,55,.3);">
                <i class="bx bx-printer"></i> Export Report
            </a>
            <a href="{{ route('paid-sales.index') }}" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.1);">
                <i class="bx bx-badge-check"></i> View Paid Sales
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="pd-kpi-row">
        <a href="{{ route('pending-draft.index') }}" class="pd-kpi-card {{ !$tab || $tab === 'all' ? 'active' : '' }}" style="text-decoration:none;">
            <div class="pd-kpi-icon i-gold"><i class="bx bx-receipt"></i></div>
            <div class="pd-kpi-info">
                <div class="k-val">{{ $totalCount }}</div>
                <div class="k-lbl">Total Leads</div>
            </div>
        </a>
        <a href="{{ route('pending-draft.index', ['tab' => 'pending']) }}" class="pd-kpi-card {{ $tab === 'pending' ? 'active' : '' }}" style="text-decoration:none;">
            <div class="pd-kpi-icon i-blue"><i class="bx bx-hourglass"></i></div>
            <div class="pd-kpi-info">
                <div class="k-val">{{ $pendingCount }}</div>
                <div class="k-lbl">Awaiting Draft</div>
            </div>
        </a>
        <a href="{{ route('pending-draft.index', ['tab' => 'not_paid']) }}" class="pd-kpi-card {{ $tab === 'not_paid' ? 'active' : '' }}" style="text-decoration:none;">
            <div class="pd-kpi-icon i-red"><i class="bx bx-error-circle"></i></div>
            <div class="pd-kpi-info">
                <div class="k-val">{{ $notPaidCount }}</div>
                <div class="k-lbl">Not Paid (FDFP)</div>
            </div>
        </a>
    </div>

    {{-- Main Card --}}
    <div class="pd-card">
        <div class="pd-card-header">
            <h6><i class="bx bx-list-ul"></i> Pending Draft Queue</h6>
            <span style="font-size:.72rem;color:var(--bs-surface-400);">{{ $leads->total() }} records</span>
        </div>

        {{-- Tabs --}}
        <div class="pd-tabs-wrap">
            <div class="pd-tabs">
                <a href="{{ route('pending-draft.index', ['tab' => 'pending']) }}" class="pd-tab {{ $tab === 'pending' ? 'active' : '' }}">
                    <i class="bx bx-hourglass"></i> Awaiting Draft
                    <span class="badge badge-blue">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('pending-draft.index', ['tab' => 'not_paid']) }}" class="pd-tab {{ $tab === 'not_paid' ? 'active' : '' }}">
                    <i class="bx bx-error-alt"></i> Not Paid / FDFP
                    <span class="badge badge-red">{{ $notPaidCount }}</span>
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('pending-draft.index') }}" class="pd-filters">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="pd-filter-group">
                <label>Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Name, phone, policy#..." style="width:180px;">
            </div>
            <div class="pd-filter-group">
                <label>Carrier</label>
                <select name="carrier" style="width:140px;">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ $carrier == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pd-filter-group">
                <label>Partner</label>
                <select name="partner" style="width:140px;">
                    <option value="">All Partners</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}" {{ $partner == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pd-filter-group">
                <label>From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" style="width:145px;">
            </div>
            <div class="pd-filter-group">
                <label>To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" style="width:145px;">
            </div>
            <button type="submit" class="pd-filter-btn btn-search"><i class="bx bx-search"></i> Filter</button>
            @if(request()->hasAny(['search', 'carrier', 'partner', 'date_from', 'date_to']))
                <a href="{{ route('pending-draft.index', ['tab' => $tab]) }}" class="pd-filter-btn btn-clear"><i class="bx bx-x"></i> Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Closer</th>
                        <th>Carrier</th>
                        <th>Policy #</th>
                        <th>Partner</th>
                        <th class="text-center">Premium</th>
                        <th class="text-center">Commission</th>
                        <th>Followup Done</th>
                        <th>Sent to Draft</th>
                        @if($tab === 'not_paid')
                        <th>FDFP Status</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $idx => $lead)
                        <tr>
                            <td style="color:var(--bs-surface-400);font-size:.72rem;">{{ $leads->firstItem() + $idx }}</td>
                            <td>
                                <strong style="font-size:.8rem;">{{ $lead->cn_name ?? '—' }}</strong>
                            </td>
                            <td style="font-size:.75rem;">{{ $lead->phone_number ?? '—' }}</td>
                            <td>
                                @if($lead->closer_name)
                                    <span class="bd-mini bd-teal">{{ $lead->closer_name }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—') }}</td>
                            <td>
                                @if($lead->policy_number)
                                    <code style="font-size:.7rem;color:var(--bs-primary);">{{ $lead->policy_number }}</code>
                                @else
                                    <span style="color:var(--bs-surface-400);font-size:.7rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->partner)
                                    <span class="bd-mini bd-green">{{ $lead->partner->name }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="bd-mini bd-gold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</span>
                            </td>
                            <td class="text-center">
                                @if(($lead->calculated_commission ?? 0) > 0)
                                    <span style="color:#722ed1;font-weight:600;font-size:.78rem;">${{ number_format($lead->calculated_commission, 2) }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->followup_done_at)
                                    <span style="font-size:.75rem;">{{ $lead->followup_done_at->format('M d, Y') }}</span>
                                    @if($lead->followupDoneBy)
                                        <div class="pd-meta">by {{ $lead->followupDoneBy->name }}</div>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->pending_draft_at)
                                    <span style="font-size:.75rem;">{{ $lead->pending_draft_at->format('M d, Y') }}</span>
                                    @if($lead->pendingDraftBy)
                                        <div class="pd-meta">by {{ $lead->pendingDraftBy->name }}</div>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            @if($tab === 'not_paid')
                            <td>
                                <span class="bd-mini bd-red">
                                    {{ $fdfpTypes[$lead->not_paid_fdfp_type] ?? $lead->not_paid_fdfp_type ?? 'Unknown' }}
                                </span>
                                @if($lead->not_paid_fdfp_type === 'manual_action' && $lead->not_paid_manual_disposition)
                                    <div class="pd-meta">→ {{ $niDispositions[$lead->not_paid_manual_disposition] ?? $lead->not_paid_manual_disposition }}</div>
                                @endif
                                @if($lead->not_paid_comment)
                                    <div class="pd-meta" style="font-style:italic;margin-top:2px;" title="{{ $lead->not_paid_comment }}">
                                        💬 {{ Str::limit($lead->not_paid_comment, 60) }}
                                    </div>
                                @endif
                                @if($lead->notPaidBy)
                                    <div class="pd-meta">by {{ $lead->notPaidBy->name }}</div>
                                @endif
                            </td>
                            @endif
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @canDeleteInModule('pending-draft')
                                        <button class="a-btn a-paid btn-mark-paid" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-badge-check"></i> Paid
                                        </button>
                                        <button class="a-btn a-died btn-policy-died" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-x-circle"></i> Died
                                        </button>
                                    @endcanDeleteInModule
                                    @canEditModule('pending-draft')
                                        @if(empty($lead->not_paid_at))
                                            <button class="a-btn a-fdfp btn-mark-fdfp" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                                <i class="bx bx-error"></i> FDFP
                                            </button>
                                        @else
                                            <button class="a-btn a-clear btn-clear-np" data-id="{{ $lead->id }}">
                                                <i class="bx bx-undo"></i> Clear
                                            </button>
                                        @endif
                                        <button class="a-btn a-back btn-send-back" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-arrow-back"></i>
                                        </button>
                                    @endcanEditModule
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tab === 'not_paid' ? 13 : 12 }}">
                                <div class="pd-empty">
                                    <i class="bx bx-inbox"></i>
                                    <p>No leads in this queue for the selected period.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="px-3 py-2" style="border-top:1px solid rgba(0,0,0,.04);">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- FDFP Modal --}}
<div class="modal fade" id="fdfpModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-error me-1 text-danger"></i> Mark as Not Paid (FDFP)
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="fdfp-lead-name"></strong>
                </p>
                <div class="mb-2">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">FDFP Type</label>
                    <select id="fdfp-type" class="form-select form-select-sm">
                        <option value="">— Select type —</option>
                        @foreach($fdfpTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="manual-disposition-wrap" style="display:none;">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Manual Action — Select Disposition</label>
                    <select id="fdfp-manual" class="form-select form-select-sm">
                        <option value="">— Select disposition —</option>
                        @foreach($niDispositions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-2">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Comment <span style="font-weight:400;color:var(--bs-surface-400);">(optional)</span></label>
                    <textarea id="fdfp-comment" class="form-control form-control-sm" rows="2" maxlength="1000" placeholder="Describe the issue..."></textarea>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="fdfp-confirm-btn">Confirm Not Paid</button>
            </div>
        </div>
    </div>
</div>

{{-- Policy Died Modal --}}
<div class="modal fade" id="pdModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-x-circle me-1 text-secondary"></i> Mark as Policy Died
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="pd-lead-name"></strong>
                </p>
                <p class="mb-2" style="font-size:.72rem;color:#c84646;">
                    <i class="bx bx-info-circle me-1"></i>
                    Policy Died leads are re-dialable. The lead will be reset to <strong>Active</strong> and return to the Ravens queue.
                </p>
                <label class="form-label" style="font-size:.72rem;font-weight:600;">Reason</label>
                <select id="pd-reason" class="form-select form-select-sm">
                    <option value="">— Select reason —</option>
                    @foreach(\App\Support\Statuses::POLICY_DIED_REASONS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-dark" id="pd-confirm-btn">Confirm Policy Died</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let currentId = null;
    let fdfpModal = null;
    let pdModal = null;

    // ── Live search: debounce auto-submit on search input ──
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let debounceTimer = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.closest('form').submit();
            }, 400);
        });
    }

    // Toast notification
    function slToast(msg) {
        const t = document.createElement('div');
        t.innerHTML = '<i class="bx bx-check-circle" style="color:#1a8754;font-size:0.9rem;"></i> ' + msg;
        t.style.cssText = 'position:fixed;top:16px;right:16px;z-index:9999;background:var(--bs-card-bg);border:1px solid rgba(52,195,143,.3);border-radius:0.5rem;padding:0.5rem 0.85rem;font-size:0.75rem;box-shadow:0 4px 16px rgba(0,0,0,.1);display:flex;align-items:center;gap:0.4rem;';
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    // Get modal instances (singleton pattern to prevent backdrop issues)
    function getFdfpModal() {
        if (!fdfpModal) {
            const modalEl = document.getElementById('fdfpModal');
            fdfpModal = new bootstrap.Modal(modalEl);
            modalEl.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            });
        }
        return fdfpModal;
    }

    function getPdModal() {
        if (!pdModal) {
            const modalEl = document.getElementById('pdModal');
            pdModal = new bootstrap.Modal(modalEl);
            modalEl.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            });
        }
        return pdModal;
    }

    // POST helper
    function postAction(url, data, button, originalHtml) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                slToast(d.message || 'Success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(d.message || 'Error');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                    button.dataset.processing = 'false';
                }
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            if (button) {
                button.disabled = false;
                button.innerHTML = originalHtml;
                button.dataset.processing = 'false';
            }
        });
    }

    // Mark Paid
    document.querySelectorAll('.btn-mark-paid').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.dataset.processing === 'true') return;
            
            const name = this.dataset.name;
            this.dataset.processing = 'true';
            
            if (!confirm('Mark "' + name + '" as Paid and move to Paid Sales?')) {
                this.dataset.processing = 'false';
                return;
            }
            
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            
            postAction('/pending-draft/' + this.dataset.id + '/mark-paid', {}, this, originalHtml);
        });
    });

    // FDFP Modal
    document.querySelectorAll('.btn-mark-fdfp').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentId = this.dataset.id;
            document.getElementById('fdfp-lead-name').textContent = this.dataset.name;
            document.getElementById('fdfp-type').value = '';
            document.getElementById('fdfp-manual').value = '';
            document.getElementById('fdfp-comment').value = '';
            document.getElementById('manual-disposition-wrap').style.display = 'none';
            document.getElementById('fdfp-confirm-btn').disabled = false;
            document.getElementById('fdfp-confirm-btn').innerHTML = 'Confirm Not Paid';
            getFdfpModal().show();
        });
    });

    document.getElementById('fdfp-type').addEventListener('change', function() {
        document.getElementById('manual-disposition-wrap').style.display = this.value === 'manual_action' ? 'block' : 'none';
    });

    document.getElementById('fdfp-confirm-btn').addEventListener('click', function() {
        const type = document.getElementById('fdfp-type').value;
        const manual = document.getElementById('fdfp-manual').value;
        const comment = document.getElementById('fdfp-comment').value.trim();
        if (!type) { alert('Please select an FDFP type.'); return; }
        if (type === 'manual_action' && !manual) { alert('Please select a manual action disposition.'); return; }
        
        this.disabled = true;
        this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
        
        fetch('/pending-draft/' + currentId + '/mark-not-paid', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({not_paid_fdfp_type: type, not_paid_manual_disposition: manual || null, not_paid_comment: comment || null})
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                getFdfpModal().hide();
                slToast(d.message || 'Marked as Not Paid');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(d.message || 'Error');
                document.getElementById('fdfp-confirm-btn').disabled = false;
                document.getElementById('fdfp-confirm-btn').innerHTML = 'Confirm Not Paid';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            document.getElementById('fdfp-confirm-btn').disabled = false;
            document.getElementById('fdfp-confirm-btn').innerHTML = 'Confirm Not Paid';
        });
    });

    // Policy Died Modal
    document.querySelectorAll('.btn-policy-died').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentId = this.dataset.id;
            document.getElementById('pd-lead-name').textContent = this.dataset.name;
            document.getElementById('pd-reason').value = '';
            document.getElementById('pd-confirm-btn').disabled = false;
            document.getElementById('pd-confirm-btn').innerHTML = 'Confirm Policy Died';
            getPdModal().show();
        });
    });

    document.getElementById('pd-confirm-btn').addEventListener('click', function() {
        const reason = document.getElementById('pd-reason').value;
        if (!reason) { alert('Please select a reason.'); return; }
        
        this.disabled = true;
        this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
        
        fetch('/pending-draft/' + currentId + '/mark-policy-died', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({policy_died_reason: reason})
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                getPdModal().hide();
                slToast(d.message || 'Marked as Policy Died');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(d.message || 'Error');
                document.getElementById('pd-confirm-btn').disabled = false;
                document.getElementById('pd-confirm-btn').innerHTML = 'Confirm Policy Died';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            document.getElementById('pd-confirm-btn').disabled = false;
            document.getElementById('pd-confirm-btn').innerHTML = 'Confirm Policy Died';
        });
    });

    // Clear Not Paid
    document.querySelectorAll('.btn-clear-np').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.dataset.processing === 'true') return;
            
            this.dataset.processing = 'true';
            if (!confirm('Clear the Not Paid flag?')) {
                this.dataset.processing = 'false';
                return;
            }
            
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            
            postAction('/pending-draft/' + this.dataset.id + '/clear-not-paid', {}, this, originalHtml);
        });
    });

    // Send Back
    document.querySelectorAll('.btn-send-back').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.dataset.processing === 'true') return;
            
            const name = this.dataset.name;
            this.dataset.processing = 'true';
            
            if (!confirm('Send "' + name + '" back to Followup stage?')) {
                this.dataset.processing = 'false';
                return;
            }
            
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            
            postAction('/leads/' + this.dataset.id + '/send-to-previous-stage', {}, this, originalHtml);
        });
    });
});
</script>
@endsection
