@extends('layouts.master')

@section('title', 'Followup Done')

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
.stage-trail {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: .75rem;
    font-size: .64rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.stage-trail .st-step {
    display: inline-flex;
    align-items: center;
    padding: .28rem .7rem;
    background: var(--bs-card-bg);
    border: 1px solid rgba(0,0,0,.08);
    color: var(--bs-surface-400);
    white-space: nowrap;
}
.stage-trail .st-step:first-child { border-radius: .35rem 0 0 .35rem; }
.stage-trail .st-step:last-child  { border-radius: 0 .35rem .35rem 0; }
.stage-trail .st-step.active {
    background: rgba(85,110,230,.12);
    color: #556ee6;
    border-color: rgba(85,110,230,.3);
}
.stage-trail .st-sep {
    font-size: .65rem;
    color: var(--bs-surface-300);
    border-top: 1px solid rgba(0,0,0,.08);
    border-bottom: 1px solid rgba(0,0,0,.08);
    padding: 0 .1rem;
    line-height: 2rem;
}
.in-draft-badge { display:inline-block;padding:.18rem .45rem;border-radius:.3rem;font-size:.62rem;font-weight:700;background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25); }
.paid-badge     { display:inline-block;padding:.18rem .45rem;border-radius:.3rem;font-size:.62rem;font-weight:700;background:rgba(52,195,143,.1);color:#1a8754;border:1px solid rgba(52,195,143,.25); }
.not-paid-badge { display:inline-block;padding:.18rem .45rem;border-radius:.3rem;font-size:.62rem;font-weight:700;background:rgba(244,106,106,.1);color:#c84646;border:1px solid rgba(244,106,106,.25); }
.a-btn { display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s; }
.a-send { background:#34c38f20;color:#1a8754;border-color:#34c38f40; }
</style>
@endsection

@section('content')

{{-- Pipeline Stage Trail --}}
<div class="stage-trail">
    <span class="st-step"><i class="bx bx-check-shield me-1"></i>Validated</span>
    <span class="st-sep"><i class="bx bx-chevron-right"></i></span>
    <span class="st-step"><i class="bx bx-task me-1"></i>Pending Submission</span>
    <span class="st-sep"><i class="bx bx-chevron-right"></i></span>
    <span class="st-step"><i class="bx bx-file-blank me-1"></i>Pending Contract</span>
    <span class="st-sep"><i class="bx bx-chevron-right"></i></span>
    <span class="st-step"><i class="bx bx-send me-1"></i>Issued</span>
    <span class="st-sep"><i class="bx bx-chevron-right"></i></span>
    <span class="st-step active"><i class="bx bx-check-circle me-1"></i>Followup Done</span>
    <span class="st-sep"><i class="bx bx-chevron-right"></i></span>
    <span class="st-step"><i class="bx bx-time-five me-1"></i>Pending Draft</span>
    <span class="st-sep"><i class="bx bx-chevron-right"></i></span>
    <span class="st-step"><i class="bx bx-money me-1"></i>Paid</span>
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-2">
    <div>
        <h5 class="mb-0 fw-semibold" style="font-size:1rem;">
            <i class="bx bx-check-double me-1" style="color:#556ee6;font-size:1.1rem;"></i>
            Followup Done
        </h5>
        <p class="mb-0" style="font-size:.68rem;color:var(--bs-surface-400);">
            Closer confirmed policy with client — awaiting Pending Draft processing.
        </p>
    </div>
    <div class="d-flex gap-1">
        <a href="{{ route('pending-draft.index') }}" class="a-btn a-send" style="font-size:.7rem;">
            <i class="bx bx-time-five"></i> Pending Draft
        </a>
        <a href="{{ route('issuance.index') }}" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.08);font-size:.7rem;">
            <i class="bx bx-left-arrow-alt"></i> Pending Contract
        </a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="kpi-row">
    <div class="kpi-card k-gold ex-card">
        <i class="bx bx-list-check k-icon" style="color:#d4af37;"></i>
        <div class="k-val" style="color:#b89730;">{{ $totalCount }}</div>
        <div class="k-lbl">Total</div>
    </div>
    <div class="kpi-card ex-card" style="background:rgba(241,180,76,.06);border-color:rgba(241,180,76,.15);">
        <i class="bx bx-time-five k-icon" style="color:#f1b44c;"></i>
        <div class="k-val" style="color:#b87a14;">{{ $pendingDraftCount }}</div>
        <div class="k-lbl">Awaiting Draft</div>
    </div>
    <div class="kpi-card k-blue ex-card">
        <i class="bx bx-check-shield k-icon" style="color:#556ee6;"></i>
        <div class="k-val" style="color:#556ee6;">{{ $totalCount - $pendingDraftCount }}</div>
        <div class="k-lbl">In Draft+</div>
    </div>
</div>

{{-- Main Card --}}
<div class="ex-card sec-card">
    <div class="pipe-hdr" style="color:#556ee6;">
        <i class="bx bx-check-double" style="color:#556ee6;"></i>
        Followup Confirmed Leads
        <span class="badge-count" style="background:rgba(85,110,230,.12);color:#556ee6;">{{ $totalCount }}</span>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('followup.followup-done') }}"
          style="display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);">
        <div>
            <label style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);display:block;margin-bottom:.15rem;">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}"
                   placeholder="Name, phone, closer…" style="width:155px;font-size:.72rem;">
        </div>
        <div>
            <label style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);display:block;margin-bottom:.15rem;">Carrier</label>
            <select name="carrier" class="form-select form-select-sm" style="width:130px;font-size:.72rem;">
                <option value="">All Carriers</option>
                @foreach($carriers as $c)
                    <option value="{{ $c->id }}" {{ $carrier == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);display:block;margin-bottom:.15rem;">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" style="width:135px;font-size:.72rem;">
        </div>
        <div>
            <label style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);display:block;margin-bottom:.15rem;">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" style="width:135px;font-size:.72rem;">
        </div>
        <button type="submit" class="a-btn" style="background:rgba(85,110,230,.2);color:#556ee6;border-color:rgba(85,110,230,.3);height:2rem;font-size:.7rem;">
            <i class="bx bx-search-alt-2"></i> Filter
        </button>
        <a href="{{ route('followup.followup-done') }}"
           style="font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;">
            <i class="bx bx-reset"></i> Clear
        </a>
    </form>

    {{-- Table --}}
    <div class="scroll-tbl" style="max-height:600px;">
        <table class="ex-tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Phone</th>
                    <th>Carrier</th>
                    <th>App ID</th>
                    <th>Policy #</th>
                    <th>Premium</th>
                    <th>Closer</th>
                    <th>Done By</th>
                    <th>Followup Date</th>
                    <th>Bank Verified</th>
                    <th>Stage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                @php
                    if ($lead->paid_at) { $stage = 'paid'; }
                    elseif ($lead->not_paid_at) { $stage = 'not_paid'; }
                    elseif ($lead->pending_draft_at) { $stage = 'draft'; }
                    else { $stage = 'done'; }
                @endphp
                <tr>
                    <td style="color:var(--bs-surface-400);font-size:.68rem;">{{ $lead->id }}</td>
                    <td>
                        <a href="{{ route('issuance.show', $lead->id) }}"
                           style="font-weight:600;font-size:.73rem;text-decoration:none;color:var(--bs-body-color);">
                            {{ $lead->cn_name ?? '—' }}
                        </a>
                        @if($lead->state)
                            <br><span style="font-size:.6rem;color:var(--bs-surface-400);">{{ $lead->state }}</span>
                        @endif
                    </td>
                    <td style="font-size:.72rem;">{{ $lead->phone_number ?? '—' }}</td>
                    <td style="font-size:.72rem;">{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—') }}</td>
                    <td style="font-size:.72rem;font-weight:600;">{{ $lead->app_id ?? '—' }}</td>
                    <td style="font-size:.72rem;">{{ $lead->policy_number ?? '—' }}</td>
                    <td style="font-size:.72rem;">${{ number_format($lead->monthly_premium, 2) }}</td>
                    <td style="font-size:.72rem;">{{ $lead->closer_name ?? '—' }}</td>
                    <td style="font-size:.72rem;">{{ $lead->followupDoneBy->name ?? '—' }}</td>
                    <td style="font-size:.72rem;white-space:nowrap;">
                        {{ $lead->followup_done_at ? $lead->followup_done_at->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        @php $bv = $lead->bank_verification_status; @endphp
                        @if($bv === 'Good')
                            <span style="font-size:.62rem;background:rgba(52,195,143,.1);color:#1a8754;border:1px solid rgba(52,195,143,.25);padding:.18rem .4rem;border-radius:.3rem;font-weight:700;">Good</span>
                        @elseif($bv === 'Average')
                            <span style="font-size:.62rem;background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);padding:.18rem .4rem;border-radius:.3rem;font-weight:700;">Average</span>
                        @elseif($bv === 'Bad')
                            <span style="font-size:.62rem;background:rgba(244,106,106,.1);color:#c84646;border:1px solid rgba(244,106,106,.25);padding:.18rem .4rem;border-radius:.3rem;font-weight:700;">Bad</span>
                        @else
                            <span style="font-size:.62rem;color:var(--bs-surface-400);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($stage === 'paid')
                            <span class="paid-badge"><i class="bx bx-check-circle" style="font-size:.7rem;"></i> Paid</span>
                        @elseif($stage === 'not_paid')
                            <span class="not-paid-badge"><i class="bx bx-error-circle" style="font-size:.7rem;"></i> Not Paid</span>
                        @elseif($stage === 'draft')
                            <span class="in-draft-badge"><i class="bx bx-time-five" style="font-size:.7rem;"></i> Pending Draft</span>
                        @else
                            <span style="font-size:.62rem;background:rgba(85,110,230,.08);color:#556ee6;border:1px solid rgba(85,110,230,.2);padding:.18rem .4rem;border-radius:.3rem;font-weight:700;">
                                <i class="bx bx-check-double" style="font-size:.7rem;"></i> Followup Done
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('issuance.show', $lead->id) }}"
                           style="display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;background:rgba(85,110,230,.1);color:#556ee6;border:1px solid rgba(85,110,230,.2);text-decoration:none;">
                            <i class="bx bx-show"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center py-5" style="color:var(--bs-surface-400);font-size:.78rem;">
                        <i class="bx bx-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3;"></i>
                        No followup-done leads for the selected period.
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

@endsection
