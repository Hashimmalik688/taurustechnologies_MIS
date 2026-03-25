@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Pending Submission')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Pending Submission — MIS Style
   ═══════════════════════════════════════════════════ */

/* ── KPI Cards ── */
.kpi-row { display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem; }
.kpi-card {
    flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;
    position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);
    transition:transform .15s,box-shadow .15s;background:var(--bs-card-bg);
    box-shadow:0 1px 4px rgba(0,0,0,.05);
}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.kpi-card .k-icon{font-size:1rem;margin-bottom:.2rem;display:block;opacity:.7;}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1;}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val,.kpi-card.k-gold .k-icon{color:#b89730}
.kpi-card.k-green{background:rgba(52,195,143,.06)}.kpi-card.k-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}.kpi-card.k-green .k-val,.kpi-card.k-green .k-icon{color:#1a8754}
.kpi-card.k-warn{background:rgba(241,180,76,.06)}.kpi-card.k-warn::before{background:linear-gradient(90deg,#f1b44c,#f5cd7e)}.kpi-card.k-warn .k-val,.kpi-card.k-warn .k-icon{color:#b87a14}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val,.kpi-card.k-blue .k-icon{color:#556ee6}

/* ── Section Card ── */
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;display:flex;align-items:center;gap:.3rem;}
.sec-hdr h6 i{opacity:.6;font-size:.95rem;}

/* ── Table ── */
.ex-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.735rem;min-width:1100px;}
.ex-tbl thead th{text-transform:uppercase;font-size:.62rem;font-weight:700;letter-spacing:.5px;color:var(--bs-surface-500);padding:.45rem .6rem;border-bottom:1px solid var(--bs-surface-200,rgba(0,0,0,.07));white-space:nowrap;background:var(--bs-surface-100,transparent);position:sticky;top:0;z-index:1;}
.ex-tbl tbody td{padding:.45rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.03);white-space:nowrap;}
.ex-tbl tbody tr{transition:background .12s;}
.ex-tbl tbody tr:hover{background:rgba(212,175,55,.03);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}

/* ── Badge styles ── */
.bd-mini{font-size:.6rem;font-weight:700;padding:.15rem .4rem;border-radius:.25rem;display:inline-block;min-width:22px;text-align:center;}
.bd-mini.bd-green{background:rgba(52,195,143,.12);color:#1a8754;} .bd-mini.bd-warn{background:rgba(241,180,76,.12);color:#b87a14;}
.bd-mini.bd-red{background:rgba(244,106,106,.12);color:#c84646;} .bd-mini.bd-blue{background:rgba(85,110,230,.12);color:#556ee6;}
.bd-mini.bd-gray{background:rgba(108,117,125,.12);color:#6c757d;}

.bd-ni{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-resolved{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-pending{background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}

/* ── Action Buttons ── */
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.a-send{background:rgba(52,195,143,.08);color:#1a8754;border-color:rgba(52,195,143,.25);}.a-send:hover{background:rgba(52,195,143,.18);}
.a-edit{background:rgba(212,175,55,.08);color:#b89730;border-color:rgba(212,175,55,.25);}.a-edit:hover{background:rgba(212,175,55,.18);}
.a-ni{background:rgba(244,106,106,.08);color:#c84646;border-color:rgba(244,106,106,.25);}.a-ni:hover{background:rgba(244,106,106,.18);}
.a-resolve{background:rgba(85,110,230,.08);color:#556ee6;border-color:rgba(85,110,230,.25);}.a-resolve:hover{background:rgba(85,110,230,.18);}
.a-recall{background:rgba(139,92,246,.08);color:#7c3aed;border-color:rgba(139,92,246,.25);}.a-recall:hover{background:rgba(139,92,246,.18);}

/* ── Filter bar ── */
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;border-radius:1rem;border:1px solid rgba(0,0,0,.08);}
.filter-form .form-control:focus,.filter-form .form-select:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.1);}
.filter-form label{font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}.f-reset:hover{color:var(--bs-body-color);}

/* ── Scrollable table ── */
.scroll-tbl{overflow-x:auto;overflow-y:auto;max-height:600px;}
.scroll-tbl::-webkit-scrollbar{width:3px;height:3px;}
.scroll-tbl::-webkit-scrollbar-thumb{background:var(--bs-surface-300);border-radius:3px;}

/* ── Modal ── */
.sub-modal .modal-content{border-radius:.6rem;border:1px solid rgba(255,255,255,.08);overflow:hidden;background:var(--bs-card-bg);box-shadow:0 8px 30px rgba(0,0,0,.15);}
.sub-modal .modal-header{background:var(--bs-card-bg);padding:.65rem .85rem;border-bottom:1px solid rgba(0,0,0,.06);}
.sub-modal .modal-header .modal-title{font-size:.85rem;font-weight:600;display:flex;align-items:center;gap:.35rem;}
.sub-modal .modal-header .modal-title i{color:var(--bs-gold,#d4af37);opacity:.7;font-size:1rem;}
.sub-modal .modal-body{padding:.85rem;}
.sub-modal .modal-body .form-label{font-size:.72rem;font-weight:600;margin-bottom:.3rem;}
.sub-modal .modal-body .form-control,.sub-modal .modal-body .form-select{font-size:.78rem;border-radius:.4rem;padding:.4rem .6rem;}
.sub-modal .modal-body .form-control:focus,.sub-modal .modal-body .form-select:focus{border-color:var(--bs-gold,#d4af37);box-shadow:0 0 0 2px rgba(212,175,55,.12);}
.sub-modal .modal-footer{border-top:1px solid rgba(0,0,0,.05);padding:.55rem .85rem;}

/* Pagination */
.sec-card .pagination{margin:0;}
.sec-card .pagination .page-link{border-radius:.35rem;margin:0 1px;font-size:.7rem;border:1px solid var(--bs-surface-200);color:var(--bs-surface-500);padding:.2rem .5rem;}
.sec-card .pagination .page-item.active .page-link{background:var(--bs-gold,#d4af37);border-color:var(--bs-gold);color:#fff;}
.sec-card .pagination svg{max-width:14px!important;max-height:14px!important;}
</style>
@endsection

@section('content')
<div class="container-fluid" style="max-width:1600px">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h5 class="mb-0 fw-semibold" style="font-size:1rem;">
                <i class="bx bx-check-circle me-1" style="color:#34c38f;font-size:1.05rem;"></i>
                Pending Submission
            </h5>
            <p class="mb-0" style="font-size:.68rem;color:var(--bs-surface-400);margin-top:.1rem;">Assign details and send validated leads to Pending Contracts</p>
        </div>
        <div class="d-flex gap-1 align-items-center">
            <a href="{{ route('issuance.index') }}" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.08);font-size:.7rem;">
                <i class="bx bx-right-arrow-alt"></i> Pending Contracts
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <i class="bx bx-data k-icon"></i>
            <div class="k-val">{{ $totalCount }}</div>
            <div class="k-lbl">Total</div>
        </div>
        <div class="kpi-card k-green">
            <i class="bx bx-check-double k-icon"></i>
            <div class="k-val">{{ $readyCount }}</div>
            <div class="k-lbl">Ready to Send</div>
        </div>
        <div class="kpi-card k-warn">
            <i class="bx bx-edit k-icon"></i>
            <div class="k-val">{{ $needsInfoCount }}</div>
            <div class="k-lbl">Needs Info</div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-check"></i> Validated Leads</h6>
            <span style="font-size:.62rem;color:var(--bs-surface-400);">{{ $totalCount }} records</span>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('submissions.index') }}" class="filter-form">
            <div>
                <label>Search</label>
                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Name, phone, carrier…" style="width:160px;">
            </div>
            <div>
                <label>Carrier</label>
                <select name="carrier" class="form-select" style="width:130px;">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ $carrier == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" style="width:135px;">
            </div>
            <div>
                <label>To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" style="width:135px;">
            </div>
            <button type="submit" class="a-btn a-send" style="height:2rem;">
                <i class="bx bx-search-alt-2"></i> Filter
            </button>
            <a href="{{ route('submissions.index') }}" class="f-reset">
                <i class="bx bx-reset"></i> Clear
            </a>
        </form>

        {{-- Table --}}
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Phone</th>
                        <th>Closer</th>
                        <th>Sale Date</th>
                        <th>Carrier</th>
                        <th>Policy Type</th>
                        <th>Coverage</th>
                        <th>Premium</th>
                        <th>Settlement</th>
                        <th>Initial Draft</th>
                        <th>Future Draft</th>
                        <th>QA Status</th>
                        <th>QA By</th>
                        <th>Validator</th>
                        <th>Validated At</th>
                        <th>App ID</th>
                        <th>Policy Number</th>
                        <th>Partner</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        @php
                            $isBlocked   = !empty($lead->not_issued_at) && empty($lead->not_issued_resolved_at);
                            $wasResolved = !empty($lead->not_issued_at) && !empty($lead->not_issued_resolved_at);
                            $isReady     = !empty($lead->policy_number) && !empty($lead->assigned_partner);
                        @endphp
                        <tr>
                            <td style="color:var(--bs-surface-400);">{{ $loop->iteration + (($leads->currentPage() - 1) * $leads->perPage()) }}</td>
                            <td>
                                <a href="{{ route('issuance.show', $lead->id) }}" style="font-weight:600;font-size:.73rem;color:var(--bs-body-color);text-decoration:none;">
                                    {{ $lead->cn_name ?? '—' }}
                                </a>
                            </td>
                            <td>{{ $lead->phone_number ?? '—' }}</td>
                            <td>
                                @if($lead->closer_name)
                                    <span class="bd-mini bd-blue">{{ $lead->closer_name }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem;">—</span>
                                @endif
                            </td>
                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}</td>
                            <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—') }}</td>
                            <td>{{ $lead->policy_type ?? '—' }}</td>
                            <td>{{ $lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 0) : '—' }}</td>
                            <td>${{ number_format($lead->monthly_premium, 2) }}</td>
                            <td>{{ $lead->settlement_type ?? '—' }}</td>
                            <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : '—' }}</td>
                            <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : '—' }}</td>
                            {{-- QA --}}
                            <td>
                                @if($lead->qa_status === 'Good')
                                    <span class="bd-mini bd-green">Good</span>
                                @elseif($lead->qa_status === 'Avg')
                                    <span class="bd-mini bd-warn">Avg</span>
                                @elseif($lead->qa_status === 'Bad')
                                    <span class="bd-mini bd-red">Bad</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->qaUser)
                                    <strong style="font-size:.72rem">{{ $lead->qaUser->name }}</strong>
                                    @if($lead->qa_reviewed_at)
                                        <div style="font-size:.58rem;color:#94a3b8;">{{ \Carbon\Carbon::parse($lead->qa_reviewed_at)->format('M d, h:i A') }}</div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                @endif
                            </td>
                            {{-- Validator --}}
                            <td>
                                @if($lead->ravens_validated_by)
                                    <strong style="font-size:.72rem">{{ $lead->ravens_validated_by }}</strong>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->ravens_validated_at)
                                    <span style="font-size:.72rem">{{ \Carbon\Carbon::parse($lead->ravens_validated_at)->format('M d, Y') }}</span>
                                    <div style="font-size:.58rem;color:#94a3b8;">{{ \Carbon\Carbon::parse($lead->ravens_validated_at)->format('h:i A') }}</div>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                @endif
                            </td>
                            {{-- Submission fields --}}
                            <td style="font-weight:600;font-size:.72rem;">{{ $lead->app_id ?? '—' }}</td>
                            <td style="font-size:.72rem;">{{ $lead->policy_number ?? '—' }}</td>
                            <td>
                                @if($lead->assigned_partner)
                                    <span class="bd-mini" style="background:rgba(212,175,55,.12);color:#b89730;">{{ $lead->assigned_partner }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.72rem">—</span>
                                @endif
                            </td>
                            <td>
                                @if($isBlocked)
                                    <span class="bd-ni">Not Issued</span>
                                    <div style="font-size:.58rem;color:var(--bs-surface-400);margin-top:.1rem;">
                                        {{ $lead->notIssuedBy->name ?? '?' }} · {{ $lead->not_issued_at ? $lead->not_issued_at->diffForHumans() : '' }}
                                    </div>
                                @elseif($wasResolved)
                                    <span class="bd-resolved">Resolved</span>
                                @elseif($isReady)
                                    <span class="bd-mini bd-green">Ready</span>
                                @else
                                    <span class="bd-pending">Needs Info</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($isReady && !$isBlocked)
                                        <button class="a-btn a-send btn-send-contract" data-id="{{ $lead->id }}" style="font-size:.63rem;">
                                            <i class="bx bx-right-arrow-alt"></i> Send
                                        </button>
                                    @endif
                                    <button class="a-btn a-edit btn-open-actions-modal"
                                        data-id="{{ $lead->id }}"
                                        data-name="{{ $lead->cn_name }}"
                                        data-policy="{{ $lead->policy_number ?? '' }}"
                                        data-partner="{{ $lead->assigned_partner ?? '' }}"
                                        data-appid="{{ $lead->app_id ?? '' }}"
                                        style="font-size:.63rem;">
                                        <i class="bx bx-pencil"></i> Manage
                                    </button>
                                    @if(!$isBlocked)
                                        <button class="a-btn a-ni btn-mark-ni" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;">
                                            <i class="bx bx-error-circle"></i> NI
                                        </button>
                                    @endif
                                    @if(!$lead->recall_requested_at)
                                        <button class="a-btn a-recall btn-recall-closer" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;">
                                            <i class="bx bx-undo"></i> Recall
                                        </button>
                                    @endif
                                    @if($isBlocked)
                                        <button class="a-btn a-resolve btn-resolve-ni" data-id="{{ $lead->id }}" style="font-size:.63rem;">
                                            <i class="bx bx-check"></i> Resolve
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="21" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No validated leads in Submissions for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="px-3 py-2">{{ $leads->withQueryString()->links() }}</div>
        @endif
    </div>
</div>


{{-- Manage Details Modal --}}
<div class="modal fade sub-modal" id="actionsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0">
                    <i class="bx bx-edit-alt"></i> Manage Submission
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="actions-lead-name"></strong>
                </p>

                {{-- App ID --}}
                <div class="mb-3">
                    <label class="form-label">App ID</label>
                    <input type="text" id="actions-app-id" class="form-control" placeholder="e.g. APP-2026-001">
                </div>

                {{-- Policy Number --}}
                <div class="mb-3">
                    <label class="form-label">Policy Number</label>
                    <input type="text" id="actions-policy-number" class="form-control" placeholder="Enter policy number">
                </div>

                {{-- Partner --}}
                <div class="mb-3">
                    <label class="form-label">Partner</label>
                    <select id="actions-partner" class="form-select">
                        <option value="">— Select Partner —</option>
                        @if(isset($partners))
                            @foreach($partners as $p)
                                <option value="{{ $p->name }}" data-partner-id="{{ $p->id }}">{{ $p->name }}{{ $p->code ? ' ('.$p->code.')' : '' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background:var(--bs-surface-100);color:var(--bs-surface-500);border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-sm" id="actions-save-btn" style="background:var(--bs-gold,#d4af37);color:#fff;border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">
                    <i class="bx bx-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Not Issued Modal --}}
<div class="modal fade sub-modal" id="niModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-error-circle me-1" style="color:#c84646;"></i> Mark as Not Issued
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="ni-lead-name"></strong>
                </p>
                <label class="form-label">Disposition Reason</label>
                <select id="ni-disposition" class="form-select">
                    <option value="">— Select reason —</option>
                    @foreach(Statuses::NOT_ISSUED_DISPOSITIONS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background:var(--bs-surface-100);color:var(--bs-surface-500);border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-sm" id="ni-confirm-btn" style="background:rgba(244,106,106,.9);color:#fff;border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Mark Not Issued</button>
            </div>
        </div>
    </div>
</div>

{{-- Recall / Send Back to Closer Modal --}}
<div class="modal fade sub-modal" id="recallModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(139,92,246,.04);border-bottom:1px solid rgba(139,92,246,.1);">
                <h6 class="modal-title mb-0" style="font-size:.85rem;color:#7c3aed;">
                    <i class="bx bx-undo me-1"></i> Send Back to Closer
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="recall-lead-name"></strong>
                </p>
                <p class="mb-3" style="font-size:.7rem;color:#7c3aed;background:rgba(139,92,246,.04);border:1px solid rgba(139,92,246,.12);border-radius:.4rem;padding:.5rem .65rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    This will send the sale back to the closer for re-dial. The closer will see the recall note on their dashboard.
                </p>
                <div class="mb-2">
                    <label class="form-label">Comment / Instructions <span class="text-danger">*</span></label>
                    <textarea id="recall-note" class="form-control" rows="3" placeholder="Why is this being sent back?" style="resize:none;"></textarea>
                    <div id="recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a comment.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background:var(--bs-surface-100);color:var(--bs-surface-500);border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-sm" id="recall-confirm-btn" style="background:rgba(139,92,246,.9);color:#fff;border:none;border-radius:1rem;padding:.35rem .85rem;font-size:.74rem;font-weight:600;">
                    <i class="bx bx-undo me-1"></i> Send Back
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function() {
    let currentLeadId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ==== Manage Details Modal ====
    document.querySelectorAll('.btn-open-actions-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentLeadId = this.dataset.id;
            document.getElementById('actions-lead-name').textContent = this.dataset.name;
            document.getElementById('actions-app-id').value = this.dataset.appid;
            document.getElementById('actions-policy-number').value = this.dataset.policy;
            document.getElementById('actions-partner').value = this.dataset.partner;
            new bootstrap.Modal(document.getElementById('actionsModal')).show();
        });
    });

    // Save Details
    document.getElementById('actions-save-btn').addEventListener('click', function() {
        const appId   = document.getElementById('actions-app-id').value.trim();
        const policy  = document.getElementById('actions-policy-number').value.trim();
        const partner = document.getElementById('actions-partner').value;
        const partnerEl = document.getElementById('actions-partner');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Saving…';

        fetch('/submissions/' + currentLeadId + '/save-decision', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                app_id:           appId || null,
                policy_number:    policy || null,
                assigned_partner: partner || null,
                partner_id:       partnerEl.selectedOptions[0] ? partnerEl.selectedOptions[0].dataset.partnerId : null,
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save';
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('actionsModal')).hide();
                location.reload();
            } else {
                alert(data.message || 'Error saving.');
            }
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save me-1"></i> Save'; alert('Error: ' + err.message); });
    });

    // ==== Send to Contract ====
    document.querySelectorAll('.btn-send-contract').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            if (!confirm('Send this lead to Pending Contracts?')) return;
            fetch('/submissions/' + id + '/send-to-contract', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => { if (data.success) location.reload(); else alert(data.message); });
        });
    });

    // ==== Not Issued ====
    document.querySelectorAll('.btn-mark-ni').forEach(btn => {
        btn.addEventListener('click', function() {
            currentLeadId = this.dataset.id;
            document.getElementById('ni-lead-name').textContent = this.dataset.name;
            document.getElementById('ni-disposition').value = '';
            new bootstrap.Modal(document.getElementById('niModal')).show();
        });
    });

    document.getElementById('ni-confirm-btn').addEventListener('click', function() {
        var disposition = document.getElementById('ni-disposition').value;
        if (!disposition) { alert('Please select a disposition reason.'); return; }
        fetch('/submissions/' + currentLeadId + '/mark-not-issued', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ not_issued_disposition: disposition })
        })
        .then(r => r.json())
        .then(data => { if (data.success) location.reload(); else alert(data.message); });
    });

    // ==== Resolve Not Issued ====
    document.querySelectorAll('.btn-resolve-ni').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Resolve this Not Issued block?')) return;
            fetch('/submissions/' + this.dataset.id + '/resolve-not-issued', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => { if (data.success) location.reload(); else alert(data.message); });
        });
    });

    // ==== Recall / Send Back ====
    var recallLeadId = null;
    document.querySelectorAll('.btn-recall-closer').forEach(btn => {
        btn.addEventListener('click', function() {
            recallLeadId = this.dataset.id;
            document.getElementById('recall-lead-name').textContent = this.dataset.name;
            document.getElementById('recall-note').value = '';
            document.getElementById('recall-note-error').style.display = 'none';
            new bootstrap.Modal(document.getElementById('recallModal')).show();
        });
    });

    document.getElementById('recall-confirm-btn').addEventListener('click', function() {
        var note = document.getElementById('recall-note').value.trim();
        if (!note) { document.getElementById('recall-note-error').style.display = 'block'; return; }
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Sending…';
        fetch('/submissions/' + recallLeadId + '/recall-to-closer', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ recall_note: note })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back';
            if (data.success) { bootstrap.Modal.getInstance(document.getElementById('recallModal')).hide(); location.reload(); }
            else alert(data.message || 'Error.');
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back'; alert('Error: ' + err.message); });
    });
})();
</script>
@endsection
