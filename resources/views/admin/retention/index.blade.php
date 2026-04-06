@extends('layouts.master')
@use('App\Support\Statuses')

@section('title', 'Retention Management')

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ── Retention Management ── */
.sl-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem;flex-wrap:wrap;gap:.6rem;}
.sl-page-title{font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:.4rem;}
.sl-page-title i{color:#d4af37;font-size:1.2rem;}

/* KPI pills row */
.ret-kpi-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.85rem;}
.ret-kpi-pill{display:flex;align-items:center;gap:.45rem;padding:.45rem .8rem;border-radius:20px;border:1px solid rgba(0,0,0,.06);background:rgba(255,255,255,.9);backdrop-filter:blur(10px);cursor:default;transition:box-shadow .15s;}
.ret-kpi-pill:hover{box-shadow:0 2px 10px rgba(0,0,0,.07);}
.ret-kpi-pill .rk-icon{width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:.82rem;}
.ret-kpi-pill .rk-lbl{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;line-height:1.1;}
.ret-kpi-pill .rk-val{font-size:1rem;font-weight:800;line-height:1;}
.ret-kpi-pill.k-pending .rk-icon{background:rgba(241,180,76,.15);color:#b87a14;}
.ret-kpi-pill.k-pending .rk-val{color:#b87a14;}
.ret-kpi-pill.k-inprog .rk-icon{background:rgba(85,110,230,.12);color:#556ee6;}
.ret-kpi-pill.k-inprog .rk-val{color:#556ee6;}
.ret-kpi-pill.k-waitcx .rk-icon{background:rgba(80,165,241,.12);color:#2b81c9;}
.ret-kpi-pill.k-waitcx .rk-val{color:#2b81c9;}
.ret-kpi-pill.k-recalled .rk-icon{background:rgba(139,92,246,.12);color:#7c3aed;}
.ret-kpi-pill.k-recalled .rk-val{color:#7c3aed;}
.ret-kpi-pill.k-fixed .rk-icon{background:rgba(52,195,143,.12);color:#1a8754;}
.ret-kpi-pill.k-fixed .rk-val{color:#1a8754;}
.ret-kpi-pill.k-cancelled .rk-icon{background:rgba(244,106,106,.12);color:#c84646;}
.ret-kpi-pill.k-cancelled .rk-val{color:#c84646;}

/* Card & filter */
.sl-card{background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border:1px solid rgba(0,0,0,.06);border-radius:16px;overflow:hidden;}
.sl-filter-pills{display:flex;align-items:center;gap:.4rem;padding:.6rem 1rem;border-bottom:1px solid rgba(0,0,0,.05);background:rgba(248,250,252,.6);flex-wrap:wrap;}
.sl-pill-select,.sl-pill-date{font-size:.72rem;font-weight:600;padding:.32rem .55rem;border-radius:22px!important;border:1px solid rgba(0,0,0,.08)!important;background:#fff;color:#475569;cursor:pointer;outline:none;transition:border-color .15s;}
.sl-pill-select{-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .5rem center;padding-right:1.4rem;max-width:160px;}
.sl-pill-date{min-width:100px;max-width:125px;color-scheme:light;}
.sl-pill-select:focus,.sl-pill-date:focus{border-color:#d4af37!important;}
.sl-pill-label{font-size:.63rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;}
.sl-pill-clear{font-size:.68rem;font-weight:600;color:#ef4444;text-decoration:none;padding:.25rem .5rem;border-radius:22px;border:1px solid rgba(239,68,68,.2);display:inline-flex;align-items:center;gap:2px;}
.sl-pill-clear:hover{background:rgba(239,68,68,.08);}
.sl-search-wrap{position:relative;display:flex;align-items:center;}
.sl-search-icon{position:absolute;left:.6rem;color:#94a3b8;font-size:.9rem;pointer-events:none;}
.sl-search-input{padding:.4rem .65rem .4rem 2rem;font-size:.75rem;border:1px solid rgba(0,0,0,.1);border-radius:22px;background:#fff;width:210px;outline:none;}
.sl-search-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);}

/* Tabs */
.sl-tabs{display:flex;gap:2px;padding:.5rem 1rem;border-bottom:1px solid rgba(0,0,0,.05);background:rgba(248,250,252,.35);flex-wrap:wrap;align-items:center;justify-content:space-between;}
.sl-tab{display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .8rem;border-radius:22px;font-size:.72rem;font-weight:700;color:#64748b;background:transparent;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.sl-tab:hover{color:#d4af37;background:rgba(212,175,55,.06);}
.sl-tab.active{background:linear-gradient(135deg,#d4af37,#b8941f);color:#0f172a;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25);}
.sl-tab .badge{font-size:.58rem;padding:.12rem .38rem;border-radius:10px;font-weight:700;}
.sl-tab.active .badge{background:rgba(0,0,0,.15)!important;color:#fff!important;}

/* Table */
.sl-tbl-wrap{overflow-x:auto;overflow-y:auto;max-height:560px;scrollbar-width:thin;scrollbar-color:#d4af37 transparent;}
.sl-tbl-wrap::-webkit-scrollbar{width:4px;height:4px;}
.sl-tbl-wrap::-webkit-scrollbar-thumb{background:#d4af37;border-radius:3px;}
.sl-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.77rem;}
.sl-tbl thead th{background:linear-gradient(180deg,#f8fafc 0%,#f1f5f9 100%);font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;padding:.42rem .5rem;border-bottom:1px solid rgba(212,175,55,.18);white-space:nowrap;position:sticky;top:0;z-index:10;}
.sl-tbl tbody td{padding:.36rem .5rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#334155;}
.sl-tbl tbody tr:hover td{background:rgba(212,175,55,.04);}
.sl-empty-row td{text-align:center;padding:2rem 0!important;color:#94a3b8;}

/* Action buttons */
.a-btn{display:inline-flex;align-items:center;gap:2px;font-size:.63rem;font-weight:600;padding:.18rem .42rem;border-radius:.3rem;border:1px solid;cursor:pointer;text-decoration:none;transition:all .12s;white-space:nowrap;}
.a-view{background:rgba(85,110,230,.1);color:#556ee6;border-color:rgba(85,110,230,.25);}.a-view:hover{background:rgba(85,110,230,.2);}
.a-call{background:rgba(52,195,143,.1);color:#1a8754;border-color:rgba(52,195,143,.25);}.a-call:hover{background:rgba(52,195,143,.2);}
.a-recall{background:rgba(139,92,246,.08);color:#7c3aed;border-color:rgba(139,92,246,.25);}.a-recall:hover{background:rgba(139,92,246,.18);}

/* Action status badge */
.ret-status-badge{display:inline-flex;align-items:center;gap:.25rem;font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;text-transform:uppercase;letter-spacing:.3px;}
.rsb-pending{background:rgba(241,180,76,.12);color:#b87a14;border:1px solid rgba(241,180,76,.25);}
.rsb-in_progress{background:rgba(85,110,230,.12);color:#556ee6;border:1px solid rgba(85,110,230,.2);}
.rsb-waiting_on_cx{background:rgba(80,165,241,.12);color:#2b81c9;border:1px solid rgba(80,165,241,.2);}
.rsb-recalled{background:rgba(139,92,246,.12);color:#7c3aed;border:1px solid rgba(139,92,246,.2);}
.rsb-fixed{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.2);}
.rsb-cancelled{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.2);}

/* Detail modal */
.modal-header-ret{background:linear-gradient(135deg,var(--bs-card-bg) 0%,rgba(212,175,55,.08) 100%);border-bottom:1px solid rgba(212,175,55,.15);}
.modal-header-ret .modal-title{font-size:.85rem;font-weight:700;}
.detail-tbl td{padding:.28rem .42rem;font-size:.77rem;border-bottom:1px solid rgba(0,0,0,.04);}
.detail-tbl td:first-child{font-weight:600;color:var(--bs-surface-500);width:38%;white-space:nowrap;}
.sec-hdr-mini{font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.45rem;}

/* Toggle disposed */
.disposed-toggle{display:inline-flex;align-items:center;gap:.3rem;font-size:.68rem;font-weight:600;color:var(--bs-surface-500);padding:.25rem .6rem;border-radius:22px;border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);cursor:pointer;text-decoration:none;transition:all .15s;}
.disposed-toggle:hover,.disposed-toggle.active{border-color:rgba(212,175,55,.3);color:#b89730;background:rgba(212,175,55,.06);}
.disposed-toggle input{width:14px;height:14px;accent-color:#d4af37;cursor:pointer;}

/* Zoom warning */
.zoom-warn{display:flex;align-items:center;gap:.6rem;padding:.55rem .85rem;margin-bottom:.7rem;background:rgba(241,180,76,.08);border:1px solid rgba(241,180,76,.25);border-radius:.6rem;font-size:.75rem;font-weight:600;color:#b87a14;}

/* Dark themes */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ret-kpi-pill{background:rgba(30,41,59,.65);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card{background:rgba(30,41,59,.65);border-color:rgba(255,255,255,.06);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-filter-pills{background:rgba(15,23,42,.4);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-select,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-pill-date{background:rgba(30,41,59,.8)!important;border-color:rgba(255,255,255,.1)!important;color:#cbd5e1;color-scheme:dark;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tabs{background:rgba(15,23,42,.3);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab{color:#94a3b8;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tab.active{color:#0f172a;}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl thead th{background:linear-gradient(180deg,rgba(15,23,42,.95),rgba(15,23,42,.9));color:#94a3b8;border-color:rgba(212,175,55,.12);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-tbl tbody td{color:#cbd5e1;border-color:rgba(255,255,255,.04);}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search-input{background:rgba(30,41,59,.8);border-color:rgba(255,255,255,.1);color:#e2e8f0;}
</style>
@endsection

@section('content')
<div class="container-fluid px-3 py-3" style="max-width:1600px;">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="font-size:.82rem;">
        <i class="mdi mdi-check-all me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Top bar --}}
<div class="sl-topbar">
    <h5 class="sl-page-title"><i class="mdi mdi-shield-check-outline"></i> Retention Management</h5>
    <div class="sl-search-wrap">
        <i class="bx bx-search sl-search-icon"></i>
        <input type="text" id="retSearch" class="sl-search-input" placeholder="Search name, phone, carrier…" value="{{ $search ?? '' }}">
    </div>
</div>

{{-- Zoom warning --}}
@if(!$hasZoomToken)
<div class="zoom-warn">
    <i class="bx bx-phone-off" style="font-size:1rem;"></i>
    <span><strong>Zoom Phone not connected.</strong> Connect your account to enable call buttons.</span>
    <a href="/zoom/authorize" class="ms-auto" style="font-size:.72rem;padding:.25rem .65rem;background:rgba(241,180,76,.15);border:1px solid rgba(241,180,76,.3);border-radius:1rem;color:#b87a14;text-decoration:none;font-weight:700;">
        <i class="bx bx-link-external me-1"></i> Connect
    </a>
</div>
@endif

{{-- KPI Row --}}
<div class="ret-kpi-row">
    <div class="ret-kpi-pill k-pending">
        <div class="rk-icon"><i class="bx bx-time-five"></i></div>
        <div><div class="rk-lbl">Pending</div><div class="rk-val">{{ $kpi['pending'] ?? 0 }}</div></div>
    </div>
    <div class="ret-kpi-pill k-inprog">
        <div class="rk-icon"><i class="bx bx-loader-alt"></i></div>
        <div><div class="rk-lbl">In Progress</div><div class="rk-val">{{ $kpi['in_progress'] ?? 0 }}</div></div>
    </div>
    <div class="ret-kpi-pill k-waitcx">
        <div class="rk-icon"><i class="bx bx-user-voice"></i></div>
        <div><div class="rk-lbl">Waiting On Cx</div><div class="rk-val">{{ $kpi['waiting_on_cx'] ?? 0 }}</div></div>
    </div>
    <div class="ret-kpi-pill k-recalled">
        <div class="rk-icon"><i class="bx bx-undo"></i></div>
        <div><div class="rk-lbl">Recalled</div><div class="rk-val">{{ $kpi['recalled'] ?? 0 }}</div></div>
    </div>
    <div class="ret-kpi-pill k-fixed">
        <div class="rk-icon"><i class="bx bx-check-circle"></i></div>
        <div><div class="rk-lbl">Fixed</div><div class="rk-val">{{ $kpi['fixed'] ?? 0 }}</div></div>
    </div>
    <div class="ret-kpi-pill k-cancelled">
        <div class="rk-icon"><i class="bx bx-x-circle"></i></div>
        <div><div class="rk-lbl">Cancelled</div><div class="rk-val">{{ $kpi['cancelled'] ?? 0 }}</div></div>
    </div>
</div>

{{-- Main card --}}
<div class="sl-card">

    {{-- Filters --}}
    <form method="GET" action="{{ route('retention.index') }}" id="retFilterForm" class="sl-filter-pills">
        <input type="hidden" name="search" id="retSearchHidden" value="{{ $search ?? '' }}">
        @if($disposed)<input type="hidden" name="disposed" value="1">@endif
        <select name="month" class="sl-pill-select" onchange="this.form.submit()">
            <option value="">All Months</option>
            @for($m=1;$m<=12;$m++)
                <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
            @endfor
        </select>
        <select name="year" class="sl-pill-select" onchange="this.form.submit()">
            <option value="">All Years</option>
            @for($y=now()->year;$y>=now()->year-5;$y--)
                <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <span class="sl-pill-label">FROM</span>
        <input type="date" name="date_from" class="sl-pill-date" value="{{ $date_from ?? '' }}" onchange="this.form.submit()">
        <span class="sl-pill-label">TO</span>
        <input type="date" name="date_to" class="sl-pill-date" value="{{ $date_to ?? '' }}" onchange="this.form.submit()">
        @if(request()->hasAny(['search','month','year','date_from','date_to']))
            <a href="{{ route('retention.index') }}" class="sl-pill-clear"><i class="bx bx-x"></i> Clear</a>
        @endif
    </form>

    {{-- Tabs + disposed toggle --}}
    <div class="sl-tabs" role="tablist">
        <div style="display:flex;gap:2px;flex-wrap:wrap;">
            <a class="sl-tab active" data-bs-toggle="tab" href="#tab-not-issued" role="tab">
                <i class="bx bx-x-circle"></i> Not Issued
                <span class="badge bg-warning text-dark">{{ $not_issued_count }}</span>
            </a>
            <a class="sl-tab" data-bs-toggle="tab" href="#tab-not-paid" role="tab">
                <i class="bx bx-error-circle"></i> Not Paid / FDFP
                <span class="badge bg-danger">{{ $not_paid_count }}</span>
            </a>
        </div>
        {{-- Toggle disposed --}}
        <a href="{{ route('retention.index', array_merge(request()->except('disposed'), $disposed ? [] : ['disposed' => 1])) }}"
           class="disposed-toggle {{ $disposed ? 'active' : '' }}">
            <i class="bx {{ $disposed ? 'bx-hide' : 'bx-archive' }}"></i>
            {{ $disposed ? 'Hide Disposed' : 'View Disposed' }}
        </a>
    </div>

    {{-- Tab content --}}
    <div class="tab-content">

        {{-- NOT ISSUED TAB --}}
        <div class="tab-pane show active" id="tab-not-issued" role="tabpanel">
            <div class="sl-tbl-wrap">
                <table class="sl-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Phone</th>
                            <th>Carrier / Closer</th>
                            <th>Not Issued Reason</th>
                            <th>Marked At</th>
                            <th>Done By / Time</th>
                            <th>Recall Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($not_issued_leads as $lead)
                        @php
                            $retStatus = $lead->ret_action_status ?: 'pending';
                            $beneficiaries = $lead->beneficiaries ?? [];
                            if(is_string($beneficiaries)){$d=json_decode($beneficiaries,true);$beneficiaries=is_array($d)?$d:[];}
                            if(!is_array($beneficiaries))$beneficiaries=[];
                            if(empty($beneficiaries)&&($lead->beneficiary||$lead->beneficiary_dob))$beneficiaries=[['name'=>$lead->beneficiary??'','dob'=>$lead->beneficiary_dob??'','relation'=>'']];
                            $leadJson = json_encode([
                                'id'=>$lead->id,'cn_name'=>$lead->cn_name,'phone_number'=>$lead->phone_number,
                                'secondary_phone_number'=>$lead->secondary_phone_number,'carrier_name'=>$lead->carrier_name,
                                'closer_name'=>$lead->closer_name,'sale_date'=>$lead->sale_date?->format('m/d/Y'),
                                'policy_type'=>$lead->policy_type,'policy_number'=>$lead->policy_number,
                                'coverage_amount'=>$lead->coverage_amount,'monthly_premium'=>$lead->monthly_premium,
                                'initial_draft_date'=>$lead->initial_draft_date?->format('m/d/Y'),
                                'future_draft_date'=>$lead->future_draft_date?->format('m/d/Y'),
                                'date_of_birth'=>$lead->date_of_birth?->format('m/d/Y'),'age'=>$lead->age,
                                'gender'=>$lead->gender,'ssn'=>$lead->ssn,'state'=>$lead->state,
                                'address'=>$lead->address,'zip_code'=>$lead->zip_code,
                                'bank_name'=>$lead->bank_name,'account_type'=>$lead->account_type,
                                'account_title'=>$lead->account_title,'routing_number'=>$lead->routing_number,
                                'account_number'=>$lead->account_number??$lead->acc_number,
                                'bank_balance'=>$lead->bank_balance,'ss_amount'=>$lead->ss_amount,
                                'ss_date'=>$lead->ss_date?->format('m/d/Y'),
                                'bank_verification_status'=>$lead->bank_verification_status,
                                'card_number'=>$lead->card_number,'cvv'=>$lead->cvv,'expiry_date'=>$lead->expiry_date,
                                'doctor_name'=>$lead->doctor_name,'doctor_number'=>$lead->doctor_number,
                                'doctor_address'=>$lead->doctor_address,'medical_issue'=>$lead->medical_issue,
                                'medications'=>$lead->medications,'smoker'=>$lead->smoker,
                                'height'=>$lead->height,'weight'=>$lead->weight,
                                'beneficiaries'=>$beneficiaries,
                                'not_issued_disposition'=>Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_issued_disposition]??$lead->not_issued_disposition,
                                'not_issued_at'=>$lead->not_issued_at?->format('m/d/Y'),
                                'marked_by'=>$lead->notIssuedBy->name??'',
                                'staff_notes'=>$lead->staff_notes,'comments'=>$lead->comments,
                                'ret_action_status'=>$retStatus,
                                'recall_requested_at'=>$lead->recall_requested_at?'yes':null,
                            ]);
                        @endphp
                        <tr>
                            <td style="color:var(--bs-surface-400);">{{ $loop->iteration }}</td>
                            <td>
                                <strong style="font-size:.74rem;">{{ $lead->cn_name ?? '—' }}</strong>
                                @if($lead->recall_requested_at)
                                    <br><span class="ret-status-badge rsb-recalled" style="margin-top:.15rem;"><i class="bx bx-undo" style="font-size:.6rem;"></i> Recalled</span>
                                @elseif($retStatus !== 'pending')
                                    <br><span class="ret-status-badge rsb-{{ $retStatus }}" style="margin-top:.15rem;">{{ Statuses::RET_ACTION_STATUSES[$retStatus] ?? $retStatus }}</span>
                                @endif
                            </td>
                            <td style="font-size:.7rem;">{{ $lead->phone_number ?? '—' }}</td>
                            <td style="font-size:.7rem;">
                                {{ $lead->carrier_name ?? '—' }}
                                @if($lead->closer_name)<br><span style="color:var(--bs-surface-400);">{{ $lead->closer_name }}</span>@endif
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark" style="font-size:.62rem;">
                                    {{ Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_issued_disposition] ?? $lead->not_issued_disposition ?? '—' }}
                                </span>
                            </td>
                            <td style="font-size:.68rem;white-space:nowrap;">{{ $lead->not_issued_at?->format('m/d/Y') ?? '—' }}</td>
                            <td style="font-size:.68rem;white-space:nowrap;">
                                @if($lead->recall_requested_at)
                                    <span style="font-weight:600;">{{ $lead->recallRequestedBy->name ?? '—' }}</span>
                                    <br><span style="color:var(--bs-surface-400);">{{ $lead->recall_requested_at->format('m/d/Y h:i A') }}</span>
                                @elseif($lead->retActionUpdatedBy)
                                    <span style="font-weight:600;">{{ $lead->retActionUpdatedBy->name }}</span>
                                    @if($lead->ret_action_updated_at)
                                        <br><span style="color:var(--bs-surface-400);">{{ $lead->ret_action_updated_at->format('m/d/Y h:i A') }}</span>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td style="font-size:.7rem;max-width:180px;white-space:normal;line-height:1.4;">
                                @if($lead->recall_note)
                                    <span style="color:#7c3aed;font-style:italic;">{{ $lead->recall_note }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="a-btn a-call btn-call-lead"
                                        data-phone="{{ preg_replace('/\D/', '', $lead->phone_number ?? '') }}"
                                        title="Call {{ $lead->cn_name }}">
                                        <i class="bx bx-phone-call"></i>
                                    </button>
                                    <button type="button" class="a-btn a-view btn-view-lead"
                                        data-lead='@json($leadJson)'
                                        data-lead-id="{{ $lead->id }}"
                                        data-type="not_issued">
                                        <i class="bx bx-show"></i> View
                                    </button>
                                    @if(!$lead->recall_requested_at && !$disposed)
                                        <button class="a-btn a-recall btn-recall-closer" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-undo"></i> Recall
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="sl-empty-row">
                            <td colspan="9">
                                <i class="bx bx-inbox" style="font-size:1.8rem;display:block;margin-bottom:.3rem;opacity:.4;"></i>
                                {{ $disposed ? 'No disposed Not Issued leads.' : 'No active Not Issued leads.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($not_issued_leads->hasPages())
                <div class="px-3 py-2">{{ $not_issued_leads->withQueryString()->links() }}</div>
            @endif
        </div>

        {{-- NOT PAID TAB --}}
        <div class="tab-pane" id="tab-not-paid" role="tabpanel">
            <div class="sl-tbl-wrap">
                <table class="sl-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Phone</th>
                            <th>Carrier / Closer</th>
                            <th>FDFP Type</th>
                            <th>Marked At</th>
                            <th>Done By / Time</th>
                            <th>Recall Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($not_paid_leads as $lead)
                        @php
                            $retStatus = $lead->ret_action_status ?: 'pending';
                            $beneficiaries = $lead->beneficiaries ?? [];
                            if(is_string($beneficiaries)){$d=json_decode($beneficiaries,true);$beneficiaries=is_array($d)?$d:[];}
                            if(!is_array($beneficiaries))$beneficiaries=[];
                            if(empty($beneficiaries)&&($lead->beneficiary||$lead->beneficiary_dob))$beneficiaries=[['name'=>$lead->beneficiary??'','dob'=>$lead->beneficiary_dob??'','relation'=>'']];
                            $fdfpLabel = Statuses::FDFP_TYPES[$lead->not_paid_fdfp_type]??$lead->not_paid_fdfp_type;
                            if($lead->not_paid_fdfp_type==='manual_action'&&$lead->not_paid_manual_disposition){
                                $fdfpLabel.=' → '.(Statuses::NOT_ISSUED_DISPOSITIONS[$lead->not_paid_manual_disposition]??$lead->not_paid_manual_disposition);
                            }
                            $leadJson = json_encode([
                                'id'=>$lead->id,'cn_name'=>$lead->cn_name,'phone_number'=>$lead->phone_number,
                                'secondary_phone_number'=>$lead->secondary_phone_number,'carrier_name'=>$lead->carrier_name,
                                'closer_name'=>$lead->closer_name,'sale_date'=>$lead->sale_date?->format('m/d/Y'),
                                'policy_type'=>$lead->policy_type,'policy_number'=>$lead->policy_number,
                                'coverage_amount'=>$lead->coverage_amount,'monthly_premium'=>$lead->monthly_premium,
                                'initial_draft_date'=>$lead->initial_draft_date?->format('m/d/Y'),
                                'future_draft_date'=>$lead->future_draft_date?->format('m/d/Y'),
                                'date_of_birth'=>$lead->date_of_birth?->format('m/d/Y'),'age'=>$lead->age,
                                'gender'=>$lead->gender,'ssn'=>$lead->ssn,'state'=>$lead->state,
                                'address'=>$lead->address,'zip_code'=>$lead->zip_code,
                                'bank_name'=>$lead->bank_name,'account_type'=>$lead->account_type,
                                'account_title'=>$lead->account_title,'routing_number'=>$lead->routing_number,
                                'account_number'=>$lead->account_number??$lead->acc_number,
                                'bank_balance'=>$lead->bank_balance,'ss_amount'=>$lead->ss_amount,
                                'ss_date'=>$lead->ss_date?->format('m/d/Y'),
                                'bank_verification_status'=>$lead->bank_verification_status,
                                'card_number'=>$lead->card_number,'cvv'=>$lead->cvv,'expiry_date'=>$lead->expiry_date,
                                'doctor_name'=>$lead->doctor_name,'doctor_number'=>$lead->doctor_number,
                                'doctor_address'=>$lead->doctor_address,'medical_issue'=>$lead->medical_issue,
                                'medications'=>$lead->medications,'smoker'=>$lead->smoker,
                                'height'=>$lead->height,'weight'=>$lead->weight,
                                'beneficiaries'=>$beneficiaries,
                                'fdfp_type'=>$fdfpLabel,
                                'not_paid_at'=>$lead->not_paid_at?->format('m/d/Y'),
                                'marked_by'=>$lead->notPaidBy->name??'',
                                'not_paid_comment'=>$lead->not_paid_comment,
                                'staff_notes'=>$lead->staff_notes,'comments'=>$lead->comments,
                                'ret_action_status'=>$retStatus,
                                'recall_requested_at'=>$lead->recall_requested_at?'yes':null,
                            ]);
                        @endphp
                        <tr>
                            <td style="color:var(--bs-surface-400);">{{ $loop->iteration }}</td>
                            <td>
                                <strong style="font-size:.74rem;">{{ $lead->cn_name ?? '—' }}</strong>
                                @if($lead->recall_requested_at)
                                    <br><span class="ret-status-badge rsb-recalled" style="margin-top:.15rem;"><i class="bx bx-undo" style="font-size:.6rem;"></i> Recalled</span>
                                @elseif($retStatus !== 'pending')
                                    <br><span class="ret-status-badge rsb-{{ $retStatus }}" style="margin-top:.15rem;">{{ Statuses::RET_ACTION_STATUSES[$retStatus] ?? $retStatus }}</span>
                                @endif
                            </td>
                            <td style="font-size:.7rem;">{{ $lead->phone_number ?? '—' }}</td>
                            <td style="font-size:.7rem;">
                                {{ $lead->carrier_name ?? '—' }}
                                @if($lead->closer_name)<br><span style="color:var(--bs-surface-400);">{{ $lead->closer_name }}</span>@endif
                            </td>
                            <td>
                                <span class="badge bg-danger" style="font-size:.62rem;">{{ $fdfpLabel }}</span>
                                @if($lead->not_paid_comment)
                                    <div style="font-size:.62rem;color:var(--bs-surface-500);margin-top:.25rem;font-style:italic;max-width:160px;white-space:normal;line-height:1.3;" title="{{ $lead->not_paid_comment }}">💬 {{ Str::limit($lead->not_paid_comment, 60) }}</div>
                                @endif
                            </td>
                            <td style="font-size:.68rem;white-space:nowrap;">{{ $lead->not_paid_at?->format('m/d/Y') ?? '—' }}</td>
                            <td style="font-size:.68rem;white-space:nowrap;">
                                @if($lead->recall_requested_at)
                                    <span style="font-weight:600;">{{ $lead->recallRequestedBy->name ?? '—' }}</span>
                                    <br><span style="color:var(--bs-surface-400);">{{ $lead->recall_requested_at->format('m/d/Y h:i A') }}</span>
                                @elseif($lead->retActionUpdatedBy)
                                    <span style="font-weight:600;">{{ $lead->retActionUpdatedBy->name }}</span>
                                    @if($lead->ret_action_updated_at)
                                        <br><span style="color:var(--bs-surface-400);">{{ $lead->ret_action_updated_at->format('m/d/Y h:i A') }}</span>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td style="font-size:.7rem;max-width:180px;white-space:normal;line-height:1.4;">
                                @if($lead->recall_note)
                                    <span style="color:#7c3aed;font-style:italic;">{{ $lead->recall_note }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="a-btn a-call btn-call-lead"
                                        data-phone="{{ preg_replace('/\D/', '', $lead->phone_number ?? '') }}"
                                        title="Call {{ $lead->cn_name }}">
                                        <i class="bx bx-phone-call"></i>
                                    </button>
                                    <button type="button" class="a-btn a-view btn-view-lead"
                                        data-lead='@json($leadJson)'
                                        data-lead-id="{{ $lead->id }}"
                                        data-type="not_paid">
                                        <i class="bx bx-show"></i> View
                                    </button>
                                    @if(!$lead->recall_requested_at && !$disposed)
                                        <button class="a-btn a-recall btn-recall-closer" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-undo"></i> Recall
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="sl-empty-row">
                            <td colspan="9">
                                <i class="bx bx-inbox" style="font-size:1.8rem;display:block;margin-bottom:.3rem;opacity:.4;"></i>
                                {{ $disposed ? 'No disposed Not Paid / FDFP leads.' : 'No active Not Paid / FDFP leads.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($not_paid_leads->hasPages())
                <div class="px-3 py-2">{{ $not_paid_leads->withQueryString()->links() }}</div>
            @endif
        </div>

    </div>{{-- /tab-content --}}
</div>{{-- /sl-card --}}

</div>

{{-- ═══ View Lead Detail Modal ═══ --}}
<div class="modal fade" id="leadDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-ret py-2 px-3">
                <h5 class="modal-title" id="detailModalTitle">
                    <i class="bx bx-user-circle" style="color:#d4af37;margin-right:.4rem;"></i>
                    <span id="dm-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <div class="row g-3">
                    {{-- Personal --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-user"></i> Personal Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Full Name</td><td id="dm-cn_name">—</td></tr>
                                <tr><td>Date of Birth</td><td id="dm-date_of_birth">—</td></tr>
                                <tr><td>Age</td><td id="dm-age">—</td></tr>
                                <tr><td>Gender</td><td id="dm-gender">—</td></tr>
                                <tr><td>SSN</td><td id="dm-ssn">—</td></tr>
                            </table>
                        </div>
                    </div>
                    {{-- Contact --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-phone"></i> Contact</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Primary Phone</td><td id="dm-phone_number">—</td></tr>
                                <tr><td>Secondary Phone</td><td id="dm-secondary_phone_number">—</td></tr>
                                <tr><td>Address</td><td id="dm-address">—</td></tr>
                                <tr><td>State</td><td id="dm-state">—</td></tr>
                                <tr><td>Zip Code</td><td id="dm-zip_code">—</td></tr>
                            </table>
                        </div>
                    </div>
                    {{-- Policy --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-shield-check"></i> Policy</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Plan Type</td><td id="dm-policy_type">—</td></tr>
                                <tr><td>Policy #</td><td id="dm-policy_number">—</td></tr>
                                <tr><td>Carrier</td><td id="dm-carrier_name">—</td></tr>
                                <tr><td>Coverage</td><td id="dm-coverage_amount">—</td></tr>
                                <tr><td>Premium</td><td id="dm-monthly_premium">—</td></tr>
                                <tr><td>Initial Draft</td><td id="dm-initial_draft_date">—</td></tr>
                                <tr><td>Future Draft</td><td id="dm-future_draft_date">—</td></tr>
                                <tr><td>Closer</td><td id="dm-closer_name">—</td></tr>
                                <tr><td>Sale Date</td><td id="dm-sale_date">—</td></tr>
                            </table>
                        </div>
                    </div>
                    {{-- Health --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-heart"></i> Health</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Nicotine</td><td id="dm-smoker">—</td></tr>
                                <tr><td>Height</td><td id="dm-height">—</td></tr>
                                <tr><td>Weight</td><td id="dm-weight">—</td></tr>
                                <tr><td>Medical Issues</td><td id="dm-medical_issue">—</td></tr>
                                <tr><td>Medications</td><td id="dm-medications">—</td></tr>
                                <tr><td>Doctor Name</td><td id="dm-doctor_name">—</td></tr>
                                <tr><td>Doctor Phone</td><td id="dm-doctor_number">—</td></tr>
                                <tr><td>Doctor Address</td><td id="dm-doctor_address">—</td></tr>
                            </table>
                        </div>
                    </div>
                    {{-- Bank --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-bank"></i> Banking</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Bank Name</td><td id="dm-bank_name">—</td></tr>
                                <tr><td>Account Type</td><td id="dm-account_type">—</td></tr>
                                <tr><td>Account Title</td><td id="dm-account_title">—</td></tr>
                                <tr><td>Routing #</td><td id="dm-routing_number">—</td></tr>
                                <tr><td>Account #</td><td id="dm-account_number">—</td></tr>
                                <tr><td>Balance</td><td id="dm-bank_balance">—</td></tr>
                                <tr><td>SS Amount</td><td id="dm-ss_amount">—</td></tr>
                                <tr><td>SS Date</td><td id="dm-ss_date">—</td></tr>
                                <tr><td>BV Status</td><td id="dm-bank_verification_status">—</td></tr>
                            </table>
                        </div>
                    </div>
                    {{-- Card Information --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-credit-card"></i> Card Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Card Number</td><td id="dm-card_number">—</td></tr>
                                <tr><td>CVV</td><td id="dm-cvv">—</td></tr>
                                <tr><td>Expiry Date</td><td id="dm-expiry_date">—</td></tr>
                            </table>
                        </div>
                    </div>
                    {{-- Beneficiary --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-heart-circle"></i> Beneficiary</div>
                            <div id="dm-beneficiaries">—</div>
                        </div>
                    </div>
                    {{-- Retention Issue --}}
                    <div class="col-12">
                        <div class="ex-card" style="padding:.8rem;">
                            <div class="sec-hdr-mini"><i class="bx bx-error-circle"></i> Retention Issue</div>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Issue Type</div>
                                    <div id="dm-issue_type" style="font-size:.78rem;font-weight:700;margin-top:.2rem;">—</div>
                                </div>
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Marked By</div>
                                    <div id="dm-marked_by" style="font-size:.78rem;margin-top:.2rem;">—</div>
                                </div>
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Marked At</div>
                                    <div id="dm-marked_at" style="font-size:.78rem;margin-top:.2rem;">—</div>
                                </div>
                                <div class="col-md-3">
                                    <div style="font-size:.65rem;font-weight:600;color:var(--bs-surface-400);text-transform:uppercase;">Notes</div>
                                    <div id="dm-notes" style="font-size:.75rem;color:var(--bs-surface-400);margin-top:.2rem;font-style:italic;">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 justify-content-between">
                {{-- Left: call + link --}}
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm" id="dm-call-btn"
                        style="background:rgba(52,195,143,.1);color:#1a8754;border:1px solid rgba(52,195,143,.25);border-radius:.4rem;font-size:.74rem;font-weight:600;display:inline-flex;align-items:center;gap:.3rem;">
                        <i class="bx bx-phone-call"></i> Call
                    </button>
                    <a id="dm-view-link" href="#" target="_blank"
                        style="font-size:.72rem;font-weight:600;color:#556ee6;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;">
                        <i class="bx bx-external-link"></i> Full Lead
                    </a>
                </div>
                {{-- Right: status update + close --}}
                <div class="d-flex gap-2 align-items-center">
                    <select id="dm-status-select" class="form-select form-select-sm" style="font-size:.72rem;width:auto;border-radius:.4rem;">
                        @foreach(Statuses::RET_ACTION_STATUSES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="dm-status-save" class="btn btn-sm"
                        style="background:linear-gradient(135deg,#d4af37,#b8941f);color:#0f172a;border:none;border-radius:.4rem;font-size:.74rem;font-weight:700;white-space:nowrap;">
                        <i class="bx bx-save me-1"></i> Save Status
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Recall Modal ═══ --}}
<div class="modal fade" id="recallModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3" style="background:rgba(139,92,246,.04);border-bottom:1px solid rgba(139,92,246,.1);">
                <h6 class="modal-title mb-0" style="font-size:.85rem;color:#7c3aed;">
                    <i class="bx bx-undo me-1"></i> Send Back to Closer
                </h6>
                <button type="button" class="btn-close" style="font-size:.65rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-1" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="recall-lead-name"></strong>
                </p>
                <p class="mb-3" style="font-size:.7rem;color:#7c3aed;background:rgba(139,92,246,.04);border:1px solid rgba(139,92,246,.12);border-radius:.4rem;padding:.5rem .65rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    This will send the lead back to the closer for re-dial.
                </p>
                <div class="mb-2">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Comment / Instructions <span class="text-danger">*</span></label>
                    <textarea id="recall-note" class="form-control form-control-sm" rows="3" placeholder="Why is this being sent back?" style="resize:none;font-size:.73rem;"></textarea>
                    <div id="recall-note-error" style="display:none;font-size:.65rem;color:#c84646;margin-top:.2rem;">Please enter a comment.</div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm" id="recall-confirm-btn"
                    style="background:rgba(139,92,246,.9);color:#fff;border:none;border-radius:.4rem;font-size:.74rem;font-weight:600;">
                    <i class="bx bx-undo me-1"></i> Send Back
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Search debounce ──────────────────────────────────────────────────────
    const searchInput  = document.getElementById('retSearch');
    const searchHidden = document.getElementById('retSearchHidden');
    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const val = this.value;
            searchTimer = setTimeout(function () {
                searchHidden.value = val;
                document.getElementById('retFilterForm').submit();
            }, 500);
        });
    }

    // ── Preserve active tab via URL hash ────────────────────────────────────
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector('.sl-tab[href="' + hash + '"]');
        if (tab) {
            document.querySelectorAll('.sl-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => { p.classList.remove('show', 'active'); });
            tab.classList.add('active');
            const pane = document.querySelector(hash);
            if (pane) pane.classList.add('show', 'active');
        }
    }
    document.querySelectorAll('.sl-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            history.replaceState(null, '', this.getAttribute('href'));
        });
    });

    // ── Call button ──────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-call-lead').forEach(btn => {
        btn.addEventListener('click', function () {
            const phone = this.dataset.phone;
            if (!phone) { alert('No phone number available.'); return; }
            if (window.zoomDial) {
                window.zoomDial(phone);
            } else {
                window.open('tel:' + phone);
            }
        });
    });

    // ── View Lead Modal ──────────────────────────────────────────────────────
    let viewLeadId = null;
    const ldModalEl = document.getElementById('leadDetailModal');
    const ldModal   = new bootstrap.Modal(ldModalEl);

    ldModalEl.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    function setTxt(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val || '—';
    }

    function fmtDate(str) {
        if (!str) return '—';
        const m = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
        return m ? `${m[2]}/${m[3]}/${m[1]}` : str;
    }

    function formatSSN(str) {
        if (!str) return '—';
        const digits = str.replace(/\D/g, '');
        if (digits.length === 9) return `${digits.slice(0,3)}-${digits.slice(3,5)}-${digits.slice(5)}`;
        return str;
    }

    document.querySelectorAll('.btn-view-lead').forEach(btn => {
        btn.addEventListener('click', function () {
            const raw = this.dataset.lead;
            let lead;
            try { lead = JSON.parse(JSON.parse(raw)); } catch(e) {
                try { lead = JSON.parse(raw); } catch(e2) { console.error('Lead parse error', e2); return; }
            }
            viewLeadId = this.dataset.leadId;

            setTxt('dm-name',         lead.cn_name);
            setTxt('dm-cn_name',      lead.cn_name);
            setTxt('dm-date_of_birth',lead.date_of_birth);
            setTxt('dm-age',          lead.age);
            setTxt('dm-gender',       lead.gender);
            setTxt('dm-ssn',          formatSSN(lead.ssn));
            setTxt('dm-phone_number', lead.phone_number);
            setTxt('dm-secondary_phone_number', lead.secondary_phone_number);
            setTxt('dm-address',      lead.address);
            setTxt('dm-state',        lead.state);
            setTxt('dm-zip_code',     lead.zip_code);
            setTxt('dm-policy_type',  lead.policy_type);
            setTxt('dm-policy_number',lead.policy_number);
            setTxt('dm-carrier_name', lead.carrier_name);
            setTxt('dm-coverage_amount', lead.coverage_amount ? '$' + Number(lead.coverage_amount).toLocaleString() : '—');
            setTxt('dm-monthly_premium', lead.monthly_premium ? '$' + Number(lead.monthly_premium).toFixed(2) + '/mo' : '—');
            setTxt('dm-initial_draft_date', lead.initial_draft_date);
            setTxt('dm-future_draft_date',  lead.future_draft_date);
            setTxt('dm-closer_name',  lead.closer_name);
            setTxt('dm-sale_date',    lead.sale_date);
            setTxt('dm-smoker',       lead.smoker ? 'Yes (Nicotine User)' : 'No');
            setTxt('dm-height',       lead.height);
            setTxt('dm-weight',       lead.weight ? lead.weight + ' lbs' : null);
            setTxt('dm-medical_issue',lead.medical_issue);
            setTxt('dm-medications',  lead.medications);
            setTxt('dm-doctor_name',  lead.doctor_name);
            setTxt('dm-doctor_number',lead.doctor_number);
            setTxt('dm-doctor_address',lead.doctor_address);
            setTxt('dm-bank_name',    lead.bank_name);
            setTxt('dm-account_type', lead.account_type);
            setTxt('dm-account_title',lead.account_title);
            setTxt('dm-routing_number',lead.routing_number);
            setTxt('dm-account_number',lead.account_number);
            setTxt('dm-bank_balance', lead.bank_balance ? '$' + Number(lead.bank_balance).toFixed(2) : null);
            setTxt('dm-ss_amount',    lead.ss_amount ? '$' + Number(lead.ss_amount).toFixed(2) : null);
            setTxt('dm-ss_date',      lead.ss_date);
            setTxt('dm-bank_verification_status', lead.bank_verification_status);
            setTxt('dm-card_number',  lead.card_number);
            setTxt('dm-cvv',          lead.cvv);
            setTxt('dm-expiry_date',  lead.expiry_date);

            // Issue type
            const issueEl = document.getElementById('dm-issue_type');
            if (issueEl) issueEl.textContent = lead.not_issued_disposition || lead.fdfp_type || '—';
            setTxt('dm-marked_by',    lead.marked_by);
            setTxt('dm-marked_at',    lead.not_issued_at || lead.not_paid_at);
            const notesArr = [lead.not_paid_comment, lead.staff_notes, lead.comments].filter(Boolean);
            setTxt('dm-notes', notesArr.join(' | ') || null);

            // Beneficiaries
            const bEl = document.getElementById('dm-beneficiaries');
            if (bEl) {
                if (lead.beneficiaries && lead.beneficiaries.length) {
                    bEl.innerHTML = lead.beneficiaries.map((b, i) => `
                        <table class="detail-tbl" style="width:100%;border-collapse:collapse;${i>0?'margin-top:.4rem':''}">
                            ${lead.beneficiaries.length > 1 ? `<tr><td colspan="2" style="font-weight:700;color:#b89730;font-size:.65rem;">Beneficiary ${i+1}</td></tr>` : ''}
                            <tr><td>Name</td><td>${b.name||'—'}</td></tr>
                            <tr><td>Relation</td><td>${b.relation||'—'}</td></tr>
                            <tr><td>DOB</td><td>${fmtDate(b.dob)}</td></tr>
                        </table>`).join('');
                } else {
                    bEl.textContent = 'No beneficiaries added';
                }
            }

            // Call button in modal
            const callBtn = document.getElementById('dm-call-btn');
            if (callBtn) {
                const phone = (lead.phone_number || '').replace(/\D/g, '');
                callBtn.onclick = () => {
                    if (window.zoomDial) window.zoomDial(phone);
                    else window.open('tel:' + phone);
                };
            }

            // View link
            const viewLink = document.getElementById('dm-view-link');
            if (viewLink) viewLink.href = '/leads/' + lead.id;

            // Status select
            const statusSel = document.getElementById('dm-status-select');
            if (statusSel) statusSel.value = lead.ret_action_status || 'pending';

            ldModal.show();
        });
    });

    // ── Save status from modal ───────────────────────────────────────────────
    document.getElementById('dm-status-save').addEventListener('click', function () {
        const newStatus = document.getElementById('dm-status-select').value;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

        fetch('/retention/' + viewLeadId + '/action-status', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ ret_action_status: newStatus })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save Status';
            if (data.success) {
                ldModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Error saving status.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save me-1"></i> Save Status';
            alert('Error: ' + err.message);
        });
    });

    // ── Recall modal ─────────────────────────────────────────────────────────
    let recallLeadId = null;
    const recallModalEl  = document.getElementById('recallModal');
    let   recallModalInst = null;

    document.querySelectorAll('.btn-recall-closer').forEach(btn => {
        btn.addEventListener('click', function () {
            recallLeadId = this.dataset.id;
            document.getElementById('recall-lead-name').textContent = this.dataset.name;
            document.getElementById('recall-note').value = '';
            document.getElementById('recall-note-error').style.display = 'none';
            if (recallModalInst) recallModalInst.dispose();
            recallModalInst = new bootstrap.Modal(recallModalEl);
            recallModalInst.show();
        });
    });

    recallModalEl.addEventListener('hidden.bs.modal', function () {
        if (recallModalInst) { recallModalInst.dispose(); recallModalInst = null; }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    document.getElementById('recall-confirm-btn').addEventListener('click', function () {
        const note = document.getElementById('recall-note').value.trim();
        if (!note) { document.getElementById('recall-note-error').style.display = 'block'; return; }
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending…';

        fetch('/retention/' + recallLeadId + '/recall-to-closer', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ recall_note: note })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back';
            if (data.success) {
                if (recallModalInst) recallModalInst.hide();
                location.reload();
            } else {
                alert(data.message || 'Error.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-undo me-1"></i> Send Back';
            alert('Error: ' + err.message);
        });
    });

})();
</script>
@endsection
