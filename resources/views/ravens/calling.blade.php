@extends('layouts.master')

@section('title')
    Ravens Calling System
@endsection

@section('css')
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('build/libs/toastr/build/toastr.min.css') }}" />
    <style>
        /* ── sl-calling: Ravens Calling System ── */
        .sl-topbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem}
        .sl-topbar h1{font-size:1.25rem;font-weight:700;margin:0;color:var(--bs-heading-color,#323a46)}
        .sl-topbar .sl-subtitle{font-size:.82rem;color:var(--bs-secondary-color,#6c757d);margin-left:.5rem;font-weight:400}
        .sl-topbar .sl-actions{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap}

        .sl-card{background:var(--bs-card-bg,#fff);border:1px solid var(--bs-border-color,#e9ecef);border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04)}
        .sl-card-header{padding:.875rem 1.25rem;border-bottom:1px solid var(--bs-border-color,#e9ecef);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem}
        .sl-card-header h2{font-size:1rem;font-weight:600;margin:0;color:var(--bs-heading-color)}
        .sl-card-body{padding:1rem 1.25rem}

        .sl-search{display:flex;gap:.35rem;align-items:center}
        .sl-search input{border:1px solid var(--bs-border-color);border-radius:22px;padding:.35rem .85rem;font-size:.82rem;min-width:200px;background:var(--bs-input-bg,#fff);color:var(--bs-body-color)}
        .sl-search input:focus{outline:none;border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.15)}
        .sl-search button,.sl-search a{border:none;border-radius:22px;padding:.35rem .65rem;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:.25rem}

        .sl-btn{border:none;border-radius:22px;padding:.4rem 1rem;font-size:.82rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;transition:all .2s}
        .sl-btn-gold{background:linear-gradient(135deg,#d4af37,#c5a028);color:#fff}
        .sl-btn-gold:hover{filter:brightness(1.08);color:#fff}
        .sl-btn-success{background:var(--bs-success);color:#fff}
        .sl-btn-success:hover{filter:brightness(1.08);color:#fff}
        .sl-btn-success.active{background:var(--bs-danger)!important}
        .sl-btn-outline{background:transparent;border:1px solid var(--bs-border-color);color:var(--bs-body-color)}
        .sl-btn-outline:hover{border-color:#d4af37;color:#d4af37}
        .sl-btn-danger{background:var(--bs-danger);color:#fff}
        .sl-btn-danger:hover{filter:brightness(1.08);color:#fff}
        .sl-btn-warning{background:var(--bs-warning);color:#212529}
        .sl-btn-secondary{background:var(--bs-secondary-bg,#6c757d);color:#fff}

        .sl-alert{border-radius:12px;padding:.65rem 1rem;font-size:.82rem;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem}
        .sl-alert-warning{background:rgba(255,193,7,.1);border:1px solid rgba(255,193,7,.25);color:var(--bs-warning-text,#856404)}
        .sl-alert a.sl-btn{font-size:.78rem;padding:.3rem .75rem}

        .sl-legend{display:flex;align-items:center;gap:.75rem;padding:.5rem .75rem;border-radius:10px;background:var(--bs-tertiary-bg,#f8f9fa);font-size:.78rem;color:var(--bs-secondary-color);margin-bottom:.75rem;flex-wrap:wrap}
        .sl-legend strong{color:var(--bs-heading-color)}

        .sl-table{width:100%;border-collapse:separate;border-spacing:0;font-size:.84rem}
        .sl-table thead th{background:var(--bs-tertiary-bg,#f8f9fa);padding:.6rem .75rem;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-secondary-color);border-bottom:1px solid var(--bs-border-color);white-space:nowrap}
        .sl-table thead th:first-child{border-radius:10px 0 0 0}
        .sl-table thead th:last-child{border-radius:0 10px 0 0}
        .sl-table tbody td{padding:.55rem .75rem;border-bottom:1px solid var(--bs-border-color);vertical-align:middle;color:var(--bs-body-color)}
        .sl-table tbody tr:last-child td{border-bottom:none}
        .sl-table tbody tr:hover{background:rgba(212,175,55,.04)}

        .sl-table .callback-note-input{border:1px solid var(--bs-border-color);border-radius:22px;padding:.3rem .7rem;font-size:.8rem;width:100%;background:var(--bs-input-bg,#fff);color:var(--bs-body-color)}
        .sl-table .callback-note-input:focus{outline:none;border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12)}

        /* Dial tracking badges */
        .dial-badges{display:flex;gap:3px;flex-wrap:wrap;align-items:center;justify-content:center}
        .dial-badge{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;font-size:.65rem;font-weight:700;color:#fff;cursor:default;position:relative}
        .dial-badge.is-mine{outline:2px solid var(--bs-surface-900);outline-offset:1px}
        .dial-badge .dial-time{display:none;position:absolute;bottom:100%;left:50%;transform:translateX(-50%);background:var(--bs-surface-700,#3b3b3b);color:#fff;padding:2px 8px;border-radius:8px;font-size:.7rem;white-space:nowrap;z-index:100}
        .dial-badge:hover .dial-time{display:block}

        /* Dial button */
        .dial-btn{border:none;border-radius:22px;padding:.35rem .85rem;font-size:.8rem;font-weight:600;background:var(--bs-primary);color:#fff;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.3rem}
        .dial-btn:hover{transform:scale(1.05);filter:brightness(1.08);color:#fff}

        /* Row states */
        .lead-row.calling{background-color:rgba(52,195,143,.08)!important;border-left:3px solid var(--bs-success)}
        .lead-row.dialed{opacity:.55}
        .lead-row.dialed-by-me{background-color:rgba(78,115,223,.05)!important;border-left:3px solid var(--bs-primary)}
        .lead-row.dialed-by-others{background-color:rgba(231,74,59,.03)!important}

        .bg-purple{background-color:var(--bs-ui-purple,#6f42c1)!important;color:#fff!important}

        .sl-pagination{display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;font-size:.82rem}
        .sl-pagination .page-link{border-radius:8px!important}
        .sl-result-count{font-size:.78rem;color:var(--bs-secondary-color)}

        /* ── Modal overrides ── */
        .sl-modal .modal-content{border-radius:20px;overflow:hidden;border:none;box-shadow:0 12px 48px rgba(0,0,0,.18)}
        .sl-modal .modal-header{background:linear-gradient(135deg,#d4af37 0%,#b8962e 50%,#d4af37 100%);background-size:200% 200%;animation:shimmerGold 3s ease infinite;padding:1rem 1.5rem;position:relative;overflow:hidden}
        .sl-modal .modal-header::after{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 60%);pointer-events:none}
        .sl-modal .modal-header .modal-title{color:#fff;font-size:1.05rem;font-weight:700;letter-spacing:.3px;display:flex;align-items:center;gap:.5rem}
        .sl-modal .modal-header .btn-close{filter:brightness(0) invert(1);opacity:.8;transition:opacity .2s}
        .sl-modal .modal-header .btn-close:hover{opacity:1}
        .sl-modal .modal-body{padding:1.5rem;position:relative}
        .sl-modal .modal-footer{padding:.75rem 1.25rem;border-top:1px solid rgba(0,0,0,.06);gap:.5rem;background:var(--bs-tertiary-bg,#fafafa)}
        .sl-modal .form-control,.sl-modal .form-select{border-radius:12px;font-size:.82rem;border:1.5px solid rgba(0,0,0,.08);transition:all .25s;padding:.45rem .75rem}
        .sl-modal .form-control:focus,.sl-modal .form-select:focus{border-color:#d4af37;box-shadow:0 0 0 3px rgba(212,175,55,.12);transform:translateY(-1px)}
        .sl-modal .badge{border-radius:8px;font-size:.68rem;font-weight:600;padding:.25rem .5rem}
        .sl-modal h5.text-gold{color:#d4af37!important;font-size:.92rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
        .sl-modal .form-label{font-size:.78rem;margin-bottom:.3rem;font-weight:600;color:var(--bs-heading-color)}
        .sl-modal .alert{border-radius:14px;font-size:.82rem;border:none}
        .sl-modal .btn{border-radius:12px}

        /* Phase transitions */
        .phase-enter{animation:phaseSlideIn .35s cubic-bezier(.22,1,.36,1) forwards}
        @keyframes phaseSlideIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        @keyframes shimmerGold{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}

        /* Phase 1 — Call Connected */
        .p1-wrap{text-align:center;padding:2.5rem 1rem}
        .p1-ring{width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,rgba(212,175,55,.12),rgba(212,175,55,.04));display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.25rem;position:relative;animation:ringPulse 2s ease-in-out infinite}
        .p1-ring::before{content:'';position:absolute;inset:-6px;border-radius:50%;border:2px solid rgba(212,175,55,.2);animation:ringExpand 2s ease-in-out infinite}
        .p1-ring::after{content:'';position:absolute;inset:-14px;border-radius:50%;border:1.5px solid rgba(212,175,55,.08);animation:ringExpand 2s ease-in-out .4s infinite}
        .p1-ring i{font-size:2.8rem;color:#d4af37;animation:phoneWiggle 1.2s ease-in-out infinite}
        @keyframes ringPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.04)}}
        @keyframes ringExpand{0%{transform:scale(.9);opacity:1}100%{transform:scale(1.15);opacity:0}}
        @keyframes phoneWiggle{0%,100%{transform:rotate(0deg)}15%{transform:rotate(12deg)}30%{transform:rotate(-10deg)}45%{transform:rotate(8deg)}60%{transform:rotate(-4deg)}75%{transform:rotate(2deg)}}
        .p1-name{font-size:1.4rem;font-weight:800;color:#d4af37;margin-bottom:.35rem;letter-spacing:.2px}
        .p1-phone{font-size:1rem;font-weight:600;color:var(--bs-body-color);margin-bottom:.2rem;font-variant-numeric:tabular-nums}
        .p1-status{font-size:.78rem;color:var(--bs-secondary-color);display:flex;align-items:center;justify-content:center;gap:.35rem;margin-bottom:1.5rem}
        .p1-status .dot{width:7px;height:7px;border-radius:50%;background:#34c759;animation:dotBlink 1.4s infinite}
        @keyframes dotBlink{0%,100%{opacity:1}50%{opacity:.3}}
        .p1-cta{background:linear-gradient(135deg,#d4af37,#c5a028);color:#fff;border:none;border-radius:14px;padding:.65rem 2rem;font-size:.88rem;font-weight:700;cursor:pointer;transition:all .25s;display:inline-flex;align-items:center;gap:.45rem;box-shadow:0 4px 16px rgba(212,175,55,.25)}
        .p1-cta:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(212,175,55,.35);color:#fff}
        .p1-cta:active{transform:translateY(0)}

        /* Phase field cards */
        .ph-field{background:var(--bs-tertiary-bg,#f8f9fa);border-radius:14px;padding:.75rem .85rem;border:1.5px solid transparent;transition:all .25s}
        .ph-field:hover{border-color:rgba(212,175,55,.15);background:rgba(212,175,55,.02)}
        .ph-field:focus-within{border-color:rgba(212,175,55,.3);box-shadow:0 2px 12px rgba(212,175,55,.06)}
        .ph-cur{display:flex;align-items:center;gap:.35rem;margin-bottom:.4rem;font-size:.72rem}
        .ph-cur-tag{background:linear-gradient(135deg,#d4af37,#c5a028);color:#fff;font-size:.6rem;padding:.15rem .4rem;border-radius:6px;font-weight:700;letter-spacing:.3px}
        .ph-cur-val{font-weight:700;color:var(--bs-heading-color);font-size:.78rem}
        .ph-field label{font-size:.72rem!important;margin-bottom:.2rem!important;color:var(--bs-secondary-color)!important;font-weight:500!important}
        .ph-field .form-control,.ph-field .form-select{border-radius:10px;font-size:.8rem;border:1.5px solid rgba(0,0,0,.06);background:var(--bs-card-bg,#fff)}

        /* Section headers for Phase 3 */
        .ph-section{display:flex;align-items:center;gap:.5rem;padding:.55rem .85rem;border-radius:12px;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(212,175,55,.02));border:1px solid rgba(212,175,55,.1);margin-top:.5rem}
        .ph-section i{color:#d4af37;font-size:1.1rem}
        .ph-section span{font-size:.85rem;font-weight:700;color:#d4af37}

        /* Phase nav buttons */
        .ph-nav{display:flex;justify-content:center;gap:.65rem;margin-top:1.25rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,.04)}
        .ph-nav-btn{border:none;border-radius:12px;padding:.5rem 1.2rem;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.35rem}
        .ph-nav-back{background:transparent;border:1.5px solid rgba(0,0,0,.1);color:var(--bs-body-color)}
        .ph-nav-back:hover{border-color:#d4af37;color:#d4af37}
        .ph-nav-next{background:linear-gradient(135deg,#d4af37,#c5a028);color:#fff;box-shadow:0 3px 12px rgba(212,175,55,.2)}
        .ph-nav-next:hover{transform:translateY(-1px);box-shadow:0 5px 18px rgba(212,175,55,.3);color:#fff}

        /* Phase indicator dots */
        .ph-dots{display:flex;justify-content:center;gap:.4rem;margin-bottom:1rem}
        .ph-dot{width:8px;height:8px;border-radius:50%;background:rgba(0,0,0,.1);transition:all .3s}
        .ph-dot.active{width:24px;border-radius:8px;background:linear-gradient(135deg,#d4af37,#c5a028)}

        /* Footer action buttons */
        .mf-btn{border:none;border-radius:12px;padding:.45rem 1rem;font-size:.78rem;font-weight:700;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.35rem;letter-spacing:.2px}
        .mf-btn:hover{transform:translateY(-1px)}
        .mf-dispose{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;box-shadow:0 2px 10px rgba(239,68,68,.2)}
        .mf-dispose:hover{box-shadow:0 4px 16px rgba(239,68,68,.3);color:#fff}
        .mf-end{background:var(--bs-body-color,#323a46);color:#fff;box-shadow:0 2px 8px rgba(0,0,0,.12)}
        .mf-end:hover{box-shadow:0 4px 14px rgba(0,0,0,.2);color:#fff}
        .mf-save{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;box-shadow:0 2px 10px rgba(245,158,11,.2)}
        .mf-save:hover{box-shadow:0 4px 16px rgba(245,158,11,.3);color:#fff}
        .mf-submit{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;box-shadow:0 2px 10px rgba(34,197,94,.2)}
        .mf-submit:hover{box-shadow:0 4px 16px rgba(34,197,94,.3);color:#fff}

        /* Phase content tip alert */
        .ph-tip{display:flex;align-items:center;gap:.55rem;padding:.6rem 1rem;border-radius:14px;font-size:.78rem;font-weight:500;margin-bottom:1rem}
        .ph-tip-info{background:rgba(59,130,246,.06);color:#2563eb}
        .ph-tip-info i{font-size:1.1rem;color:#3b82f6}
        .ph-tip-success{background:rgba(34,197,94,.06);color:#16a34a}
        .ph-tip-success i{font-size:1.1rem;color:#22c55e}

        /* Beneficiary add button */
        .ph-add-btn{border:1.5px dashed rgba(212,175,55,.3);background:transparent;border-radius:10px;padding:.35rem .8rem;font-size:.75rem;font-weight:600;color:#d4af37;cursor:pointer;transition:all .2s}
        .ph-add-btn:hover{background:rgba(212,175,55,.04);border-color:#d4af37}

        /* Phase content */
        .sl-phase-title{font-size:2.5rem;color:var(--bs-success)}

        /* Dropdown menu in footer */
        .mf-dropdown .dropdown-menu{border-radius:14px;padding:.35rem;border:1px solid rgba(0,0,0,.06);box-shadow:0 8px 24px rgba(0,0,0,.1)}
        .mf-dropdown .dropdown-item{border-radius:10px;font-size:.8rem;padding:.45rem .75rem;font-weight:500;transition:background .15s}
        .mf-dropdown .dropdown-item:hover{background:rgba(239,68,68,.06);color:#dc2626}
        .mf-dropdown .dropdown-item i{font-size:1rem;width:1.2rem;text-align:center}

        /* Auto-dial button states */
        .auto-dial-btn{position:relative;min-width:150px}
        .auto-dial-btn.active{background:var(--bs-danger)!important;border-color:var(--bs-danger)!important}

        /* Dark mode tweaks */
        :is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-card{background:var(--bs-card-bg);border-color:var(--bs-border-color)}
        :is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-legend{background:var(--bs-tertiary-bg)}
        :is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-table thead th{background:var(--bs-tertiary-bg)}
        :is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-search input{background:var(--bs-input-bg);color:var(--bs-body-color);border-color:var(--bs-border-color)}
        :is([data-theme="dark"],[data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-modal .modal-content{background:var(--bs-card-bg)}
    </style>
@endsection

@section('content')
    @php
        $hasZoomToken = \App\Models\ZoomToken::where('user_id', Auth::id())
            ->where('expires_at', '>', now())
            ->exists();
    @endphp

    <!-- Topbar -->
    <div class="sl-topbar">
        <div>
            <h1><i class="bx bx-phone-call me-2" style="color:#d4af37"></i>Ravens Calling System
                <span class="sl-subtitle">{{ $leads->total() }} lead{{ $leads->total() !== 1 ? 's' : '' }}</span>
            </h1>
        </div>
        <div class="sl-actions">
            <form action="{{ route('ravens.calling') }}" method="GET" class="sl-search">
                <input type="text" name="search" placeholder="Search name or phone…" value="{{ request('search') }}">
                <button type="submit" class="sl-btn sl-btn-gold"><i class="bx bx-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('ravens.calling') }}" class="sl-btn sl-btn-outline" title="Clear"><i class="bx bx-x"></i></a>
                @endif
            </form>
            <button id="autoDialBtn" class="sl-btn sl-btn-success auto-dial-btn">
                <i class="bx bx-play-circle"></i>
                <span id="autoDialText">Start Auto-Dial</span>
            </button>
        </div>
    </div>

    @if(!$hasZoomToken)
    <div class="sl-alert sl-alert-warning">
        <i class="bx bx-phone-off" style="font-size:1.1rem"></i>
        <span><strong>Zoom Phone Not Connected!</strong> Connect your account to make calls.</span>
        <a href="/zoom/authorize" class="sl-btn sl-btn-gold" style="margin-left:auto"><i class="bx bx-link-external"></i> Connect Zoom</a>
    </div>
    @endif

    <div class="sl-card">
        <div class="sl-card-header">
            <h2>Leads to Call</h2>
            <div class="sl-legend" style="margin:0;padding:.35rem .65rem">
                <strong><i class="bx bx-info-circle"></i> Dial Tracking:</strong>
                <span><span class="dial-badge is-mine d-inline-flex" style="width:18px;height:18px;background:var(--bs-primary);font-size:.5rem">ME</span> You</span>
                <span><span class="dial-badge d-inline-flex" style="width:18px;height:18px;background:var(--bs-danger);font-size:.5rem">AB</span> Others</span>
                <span style="opacity:.6">Hover badge for details · Auto-refreshes every 30s</span>
            </div>
        </div>
        <div class="sl-card-body" style="padding:.5rem .75rem">
            <div class="table-responsive">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Customer Name</th>
                            <th style="width:240px">Callback Note <span style="font-weight:400;opacity:.6">(3-day auto-clear)</span></th>
                            <th style="width:90px;text-align:center">Dialed</th>
                            <th style="width:100px;text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="leadsTableBody">
                        @forelse($leads as $index => $lead)
                            <tr class="lead-row" data-lead-id="{{ $lead->id }}" data-phone="{{ $lead->phone_number }}" data-secondary-phone="{{ $lead->secondary_phone_number ?? '' }}">
                                <td>{{ $leads->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                                    @if($lead->sale_at && $lead->closer_name)
                                        <span class="badge bg-success ms-1" style="border-radius:8px;font-size:.68rem">Sale by {{ $lead->closer_name }} · {{ $lead->sale_at->format('M d, Y') }}</span>
                                    @endif
                                    @if(
                                        ($lead->closer_name && isset($peregrineClosers) && in_array($lead->closer_name, $peregrineClosers)) ||
                                        (strtolower($lead->team ?? '') === 'peregrine') ||
                                        (stripos($lead->assigned_partner ?? '', 'peregrine') !== false)
                                    )
                                        <span class="badge bg-purple ms-1" style="border-radius:8px;font-size:.68rem">Peregrine</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $showNote = false;
                                        $noteValue = '';
                                        if ($lead->callback_note && $lead->callback_note_updated_at) {
                                            $noteAge = $lead->callback_note_updated_at->diffInDays(now(), false);
                                            if ($noteAge < 3) {
                                                $showNote = true;
                                                $noteValue = $lead->callback_note;
                                            }
                                        }
                                    @endphp
                                    <input type="text" class="callback-note-input" data-lead-id="{{ $lead->id }}"
                                        value="{{ $noteValue }}" placeholder="e.g., callback 2pm"
                                        onblur="saveCallbackNote({{ $lead->id }}, this.value)">
                                    @if($showNote && $lead->callback_note_updated_at)
                                        <small class="text-muted d-block mt-1" style="font-size:.72rem">
                                            <i class="bx bx-time-five"></i> {{ $lead->callback_note_updated_at->diffForHumans() }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dial-badges" id="dial-badges-{{ $lead->id }}"></div>
                                </td>
                                <td class="text-center">
                                    <button class="dial-btn" onclick="makeCall('{{ $lead->id }}', '{{ $lead->phone_number }}', this)">
                                        <i class="bx bx-phone-call"></i> Call
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color:var(--bs-secondary-color)">
                                    <i class="bx bx-info-circle" style="font-size:1.5rem;display:block;margin-bottom:.25rem"></i>
                                    No leads available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($leads->hasPages())
            <div class="sl-pagination">
                <span class="sl-result-count">Showing {{ $leads->firstItem() }}–{{ $leads->lastItem() }} of {{ $leads->total() }}</span>
                {{ $leads->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- PHASED CALL POPUP MODAL -->
    <div class="modal fade sl-modal" id="callDetailsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-phone-call me-2"></i><span id="callModalStatus">Call Connected</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="callModalBody">

                    <!-- Phase Indicator Dots -->
                    <div class="ph-dots" id="phaseIndicator">
                        <span class="ph-dot active" data-phase="1"></span>
                        <span class="ph-dot" data-phase="2"></span>
                        <span class="ph-dot" data-phase="3"></span>
                    </div>

                    <!-- PHASE 1: CALL CONNECTED -->
                    <div class="d-none" id="phase1">
                        <div class="p1-wrap phase-enter">
                            <div class="p1-ring">
                                <i class="bx bx-phone-call"></i>
                            </div>
                            <div class="p1-name" id="callerName">Connecting...</div>
                            <div class="p1-phone" id="callerPhone"></div>
                            <div class="p1-status"><span class="dot"></span> Call in progress</div>
                            <button type="button" class="p1-cta" onclick="goToPhase2()">
                                Start Call Info <i class="bx bx-right-arrow-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- PHASE 2: ESSENTIAL FIELDS -->
                    <div class="d-none" id="phase2">
                        <div class="phase-enter">
                            <div class="ph-tip ph-tip-info">
                                <i class="bx bx-edit"></i> <span>Review and update the customer's information as needed</span>
                            </div>

                            <div class="row g-2">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayName">-</span></div>
                                        <label>Name</label>
                                        <input type="text" class="form-control" id="phase2_name" placeholder="Leave empty if no change">
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayPhone">-</span></div>
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control" id="phase2_phone" placeholder="Leave empty if no change">
                                    </div>
                                </div>

                                <!-- Secondary Phone -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displaySecondaryPhone">-</span></div>
                                        <label>Secondary Phone</label>
                                        <input type="text" class="form-control" id="phase2_secondary_phone" placeholder="Leave empty if no change">
                                    </div>
                                </div>

                                <!-- State -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayState">-</span></div>
                                        <label>State</label>
                                        <select class="form-select" id="phase2_state">
                                            <option value="">Select State</option>
                                            @foreach($usStates as $code => $name)
                                                <option value="{{ $code }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Zip -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayZipCode">-</span></div>
                                        <label>Zip Code</label>
                                        <input type="text" class="form-control" id="phase2_zip" placeholder="Leave empty if no change">
                                    </div>
                                </div>

                                <!-- DOB -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayDOB">-</span></div>
                                        <label>Date of Birth</label>
                                        <input type="date" class="form-control" id="phase2_dob">
                                    </div>
                                </div>

                                <!-- SSN -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displaySSN">-</span></div>
                                        <label>SSN</label>
                                        <input type="text" class="form-control" id="phase2_ssn" placeholder="XXX-XX-XXXX">
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayAddress">-</span></div>
                                        <label>Address</label>
                                        <input type="text" class="form-control" id="phase2_address" placeholder="Enter address">
                                    </div>
                                </div>

                                <!-- Emergency Contact -->
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayEmergencyContact">-</span></div>
                                        <label>Emergency Contact</label>
                                        <input type="text" class="form-control" id="phase2_emergency_contact" placeholder="Leave empty if no change">
                                    </div>
                                </div>

                                <!-- Beneficiary -->
                                <div class="col-12">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayBeneficiary" style="color:#16a34a">-</span></div>
                                        <label>Beneficiaries</label>
                                        <div id="beneficiaries-container-ravens"></div>
                                        <button type="button" class="ph-add-btn mt-2" onclick="window.addBeneficiaryRow()">
                                            <i class="bx bx-plus"></i> Add Beneficiary
                                        </button>
                                    </div>
                                </div>

                                <!-- Carrier -->
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayCarrier">-</span></div>
                                        <label>Policy Carrier</label>
                                        <select class="form-select" id="phase2_carrier">
                                            <option value="">Select Carrier</option>
                                            @foreach($insuranceCarriers as $carrier)
                                                <option value="{{ $carrier }}">{{ $carrier }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Coverage -->
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayCoverage">-</span></div>
                                        <label>Coverage Amount</label>
                                        <input type="number" class="form-control" id="phase2_coverage" step="0.01" placeholder="Amount">
                                    </div>
                                </div>

                                <!-- Premium -->
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="displayPremium">-</span></div>
                                        <label>Monthly Premium</label>
                                        <input type="number" class="form-control" id="phase2_premium" step="0.01" placeholder="Amount">
                                    </div>
                                </div>
                            </div>

                            <div class="ph-nav">
                                <button type="button" class="ph-nav-btn ph-nav-back" onclick="goToPhase1()">
                                    <i class="bx bx-left-arrow-alt"></i> Back
                                </button>
                                <button type="button" class="ph-nav-btn ph-nav-next" id="showMoreBtn" onclick="goToPhase3()">
                                    Continue <i class="bx bx-right-arrow-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PHASE 3: FULL DETAILS WITH CHANGE TRACKING -->
                    <div class="d-none" id="phase3">
                        <div class="phase-enter">
                            <div class="ph-tip ph-tip-success">
                                <i class="bx bx-check-circle"></i> <span>Essential fields captured — review complete details below</span>
                            </div>

                            <div class="row g-2">
                                <!-- Personal Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-user"></i><span>Personal Information</span></div></div>

                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_name"></span></div>
                                        <label>Name</label>
                                        <input type="text" class="form-control form-control-sm" id="change_name" placeholder="Enter new name if changed">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_phone"></span></div>
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control form-control-sm" id="change_phone" placeholder="Enter new phone if changed">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_secondary_phone"></span></div>
                                        <label>Secondary Phone</label>
                                        <input type="text" class="form-control form-control-sm" id="change_secondary_phone" placeholder="Enter secondary phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_state"></span></div>
                                        <label>State</label>
                                        <select class="form-select form-select-sm" id="change_state">
                                            <option value="">Select State</option>
                                            @foreach($usStates as $state)
                                                <option value="{{ $state }}">{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_zip"></span></div>
                                        <label>Zip Code</label>
                                        <input type="text" class="form-control form-control-sm" id="change_zip" placeholder="Enter zip code">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_dob"></span></div>
                                        <label>Date of Birth</label>
                                        <input type="date" class="form-control form-control-sm" id="change_dob">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_gender"></span></div>
                                        <label>Gender</label>
                                        <select class="form-select form-select-sm" id="change_gender">
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_birthplace"></span></div>
                                        <label>Birth Place</label>
                                        <input type="text" class="form-control form-control-sm" id="change_birthplace" placeholder="Enter birth place">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_ssn"></span></div>
                                        <label>SSN</label>
                                        <input type="text" class="form-control form-control-sm" id="change_ssn" placeholder="Enter SSN">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_smoker"></span></div>
                                        <label>Smoker</label>
                                        <select class="form-select form-select-sm" id="change_smoker">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_height"></span></div>
                                        <label>Height</label>
                                        <input type="text" class="form-control form-control-sm" id="change_height" placeholder="5'10&quot;">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_weight"></span></div>
                                        <label>Weight</label>
                                        <input type="text" class="form-control form-control-sm" id="change_weight" placeholder="180">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_driving_license"></span></div>
                                        <label>Driving License</label>
                                        <select class="form-select form-select-sm" id="change_driving_license">
                                            <option value="">Select</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_address"></span></div>
                                        <label>Address</label>
                                        <input type="text" class="form-control form-control-sm" id="change_address" placeholder="Enter address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_emergency_contact"></span></div>
                                        <label>Emergency Contact</label>
                                        <input type="text" class="form-control form-control-sm" id="change_emergency_contact" placeholder="Enter emergency contact">
                                    </div>
                                </div>

                                <!-- Medical Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-plus-medical"></i><span>Medical Information</span></div></div>

                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_medical_issue"></span></div>
                                        <label>Medical Issue</label>
                                        <textarea class="form-control form-control-sm" id="change_medical_issue" rows="2" placeholder="Enter medical issues"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_medications"></span></div>
                                        <label>Medications</label>
                                        <textarea class="form-control form-control-sm" id="change_medications" rows="2" placeholder="Enter medications"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_doctor"></span></div>
                                        <label>Doctor Name</label>
                                        <input type="text" class="form-control form-control-sm" id="change_doctor" placeholder="Enter doctor name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_doctor_phone"></span></div>
                                        <label>Doctor Phone</label>
                                        <input type="text" class="form-control form-control-sm" id="change_doctor_phone" placeholder="Enter doctor phone">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_doctor_address"></span></div>
                                        <label>Doctor Address</label>
                                        <input type="text" class="form-control form-control-sm" id="change_doctor_address" placeholder="Enter doctor address">
                                    </div>
                                </div>

                                <!-- Policy Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-shield-quarter"></i><span>Policy Information</span></div></div>

                                <div class="col-12">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_beneficiary"></span></div>
                                        <label>Beneficiaries</label>
                                        <div id="beneficiaries-container-phase3" class="mb-2"></div>
                                        <button type="button" class="ph-add-btn" onclick="window.addBeneficiaryRowPhase3()">
                                            <i class="bx bx-plus"></i> Add Beneficiary
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_policy_type"></span></div>
                                        <label>Policy Type</label>
                                        <input type="text" class="form-control form-control-sm" id="change_policy_type" placeholder="Enter policy type">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_policy_number"></span></div>
                                        <label>Policy Number</label>
                                        <input type="text" class="form-control form-control-sm" id="change_policy_number" placeholder="Enter policy number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_carrier"></span></div>
                                        <label>Carrier</label>
                                        <select class="form-select form-select-sm" id="change_carrier">
                                            <option value="">Select Carrier</option>
                                            @foreach($insuranceCarriers as $carrier)
                                                <option value="{{ $carrier }}">{{ $carrier }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_coverage"></span></div>
                                        <label>Coverage Amount</label>
                                        <input type="number" class="form-control form-control-sm" id="change_coverage" step="0.01" placeholder="Enter amount">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_premium"></span></div>
                                        <label>Monthly Premium</label>
                                        <input type="number" class="form-control form-control-sm" id="change_premium" step="0.01" placeholder="Enter amount">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_draft_date"></span></div>
                                        <label>Initial Draft Date</label>
                                        <input type="date" class="form-control form-control-sm" id="change_draft_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_future_draft_date"></span></div>
                                        <label>Future Draft Date</label>
                                        <input type="date" class="form-control form-control-sm" id="change_future_draft_date">
                                    </div>
                                </div>

                                <!-- Banking Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-building-house"></i><span>Banking Information</span></div></div>

                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_bank_name"></span></div>
                                        <label>Bank Name</label>
                                        <input type="text" class="form-control form-control-sm" id="change_bank_name" placeholder="Enter bank name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_account_title"></span></div>
                                        <label>Account Title</label>
                                        <input type="text" class="form-control form-control-sm" id="change_account_title" placeholder="Enter account title">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_account_type"></span></div>
                                        <label>Account Type</label>
                                        <select class="form-select form-select-sm" id="change_account_type">
                                            <option value="">Select</option>
                                            <option value="Checking">Checking</option>
                                            <option value="Savings">Savings</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_routing"></span></div>
                                        <label>Routing Number</label>
                                        <input type="text" class="form-control form-control-sm" id="change_routing" placeholder="Enter routing number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_account"></span></div>
                                        <label>Account Number</label>
                                        <input type="text" class="form-control form-control-sm" id="change_account" placeholder="Enter account number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_verified_by"></span></div>
                                        <label>Verified By</label>
                                        <input type="text" class="form-control form-control-sm" id="change_verified_by" placeholder="Enter verifier name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_balance"></span></div>
                                        <label>Bank Balance</label>
                                        <input type="number" class="form-control form-control-sm" id="change_balance" step="0.01" placeholder="Enter balance">
                                    </div>
                                </div>

                                <!-- Card Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-credit-card"></i><span>Card Information</span></div></div>

                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_card_number"></span></div>
                                        <label>Card Number</label>
                                        <input type="text" class="form-control form-control-sm" id="change_card_number" placeholder="Enter card number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_cvv"></span></div>
                                        <label>CVV</label>
                                        <input type="text" class="form-control form-control-sm" id="change_cvv" placeholder="CVV" maxlength="4">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_expiry_date"></span></div>
                                        <label>Expiry Date</label>
                                        <input type="text" class="form-control form-control-sm" id="change_expiry_date" placeholder="MM/YY">
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-info-circle"></i><span>Additional Information</span></div></div>

                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_closer"></span></div>
                                        <label>Closer Name</label>
                                        <input type="text" class="form-control form-control-sm" id="change_closer" placeholder="Changes (if any)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <div class="ph-cur"><span class="ph-cur-tag">CURRENT</span> <span class="ph-cur-val" id="orig_source"></span></div>
                                        <label>Source</label>
                                        <input type="text" class="form-control form-control-sm" id="change_source" placeholder="Lead source">
                                    </div>
                                </div>

                                <!-- Sale Assignment -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-user-check"></i><span>Sale Assignment</span></div></div>

                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <label>Policy Carrier</label>
                                        <select class="form-select" id="phase3_policy_carrier" data-carrier-partner-info='@json($carrierPartnerData)'>
                                            <option value="">Select Carrier</option>
                                            @foreach($carrierPartnerData as $cp)
                                                <option value="{{ $cp['carrier_id'] }}_{{ $cp['partner_id'] }}" 
                                                        data-carrier-name="{{ $cp['carrier_name'] }}" 
                                                        data-partner-id="{{ $cp['partner_id'] }}"
                                                        data-partner-name="{{ $cp['partner_name'] }}"
                                                        data-states='@json($cp['states'])'>
                                                    {{ $cp['display_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ph-field">
                                        <label>State</label>
                                        <select class="form-select" id="phase3_approved_state">
                                            <option value="">Select Carrier First</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Partner Information -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-briefcase"></i><span>Partner Information</span></div></div>

                                <div class="col-md-12">
                                    <div class="ph-field">
                                        <label>Assigned Partner</label>
                                        <input type="text" class="form-control" id="phase3_assigned_partner" placeholder="Auto-filled from carrier selection" readonly>
                                        <input type="hidden" id="phase3_partner_id">
                                    </div>
                                </div>

                                <!-- Follow Up -->
                                <div class="col-12"><div class="ph-section"><i class="bx bx-calendar"></i><span>Follow Up Schedule</span></div></div>

                                <div class="col-md-12">
                                    <div class="ph-field">
                                        <label>Follow Up Required</label>
                                        <select class="form-select" id="phase3_followup_required">
                                            <option value="">Select option...</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 d-none" id="followup_datetime_field">
                                    <div class="ph-field">
                                        <label>Follow Up Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="phase3_followup_scheduled_at">
                                        <small style="font-size:.7rem;color:var(--bs-secondary-color);margin-top:.25rem;display:block">When should the follow-up call be scheduled?</small>
                                    </div>
                                </div>
                            </div>

                            <script>
                            (function() {
                                const followupRequired = document.getElementById('phase3_followup_required');
                                const followupDatetimeField = document.getElementById('followup_datetime_field');
                                const followupScheduledAt = document.getElementById('phase3_followup_scheduled_at');
                                
                                if (followupRequired) {
                                    followupRequired.addEventListener('change', function() {
                                        if (this.value === '1') {
                                            followupDatetimeField.style.display = 'block';
                                            followupScheduledAt.setAttribute('required', 'required');
                                        } else {
                                            followupDatetimeField.style.display = 'none';
                                            followupScheduledAt.removeAttribute('required');
                                        }
                                    });
                                }

                                // Carrier-Partner State Filtering
                                const carrierSelect = document.getElementById('phase3_policy_carrier');
                                const stateSelect = document.getElementById('phase3_approved_state');
                                const partnerInput = document.getElementById('phase3_assigned_partner');
                                const partnerIdInput = document.getElementById('phase3_partner_id');
                                const allStates = @json($usStates);

                                if (carrierSelect) {
                                    carrierSelect.addEventListener('change', function() {
                                        const selectedOption = this.options[this.selectedIndex];
                                        
                                        // Clear state dropdown
                                        stateSelect.innerHTML = '<option value="">Select State</option>';
                                        
                                        if (this.value) {
                                            // Get approved states for this carrier-partner combo
                                            const approvedStates = JSON.parse(selectedOption.dataset.states || '[]');
                                            const partnerName = selectedOption.dataset.partnerName;
                                            const partnerId = selectedOption.dataset.partnerId;
                                            
                                            // Update assigned partner field
                                            if (partnerInput) {
                                                partnerInput.value = partnerName;
                                            }
                                            if (partnerIdInput) {
                                                partnerIdInput.value = partnerId;
                                            }
                                            
                                            // Populate states dropdown with approved states only
                                            approvedStates.forEach(stateCode => {
                                                if (allStates[stateCode]) {
                                                    const option = document.createElement('option');
                                                    option.value = stateCode;
                                                    option.textContent = allStates[stateCode];
                                                    stateSelect.appendChild(option);
                                                }
                                            });
                                            
                                            if (approvedStates.length === 0) {
                                                stateSelect.innerHTML = '<option value="">No approved states</option>';
                                            }
                                        } else {
                                            stateSelect.innerHTML = '<option value="">Select Carrier First</option>';
                                            if (partnerInput) partnerInput.value = '';
                                            if (partnerIdInput) partnerIdInput.value = '';
                                        }
                                    });
                                }
                            })();
                            </script>

                            <div class="ph-nav">
                                <button type="button" class="ph-nav-btn ph-nav-back" onclick="goToPhase2()">
                                    <i class="bx bx-left-arrow-alt"></i> Back to Essential Fields
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <!-- Disposition Dropdown (on the left) -->
                    <div class="btn-group dropup me-auto mf-dropdown">
                        <button type="button" class="mf-btn mf-dispose dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-block"></i> Dispose Lead
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="disposeCurrentLead('no_answer'); return false;"><i class="bx bx-phone-off me-2"></i> No Answer</a></li>
                            <li><a class="dropdown-item" href="#" onclick="disposeCurrentLead('wrong_number'); return false;"><i class="bx bx-x-circle me-2"></i> Wrong Number</a></li>
                            <li><a class="dropdown-item" href="#" onclick="disposeCurrentLead('wrong_details'); return false;"><i class="bx bx-error me-2"></i> Wrong Details</a></li>
                        </ul>
                    </div>
                    
                    <!-- Action buttons (on the right) -->
                    <button type="button" class="mf-btn mf-end" onclick="closeCallModal()"><i class="bx bx-phone-off"></i> End Call</button>
                    <button type="button" class="mf-btn mf-save" onclick="saveAndExit()"><i class="bx bx-save"></i> Save & Exit</button>
                    <button type="button" class="mf-btn mf-submit" onclick="submitSale()"><i class="bx bx-check-circle"></i> Submit Sale</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script src="{{ URL::asset('build/libs/toastr/build/toastr.min.js') }}"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
<script>
    // Use window scope for global variables to avoid conflicts
    window.autoDialActive = false;
    window.currentLeadIndex = 0;
    window.dialedLeads = new Set(); // Local cache, synced from server
    window.beneficiaryIndexRavens = 0;
    window.isCallActive = false;
    window.autoDialTimeout = null;
    window.currentEventId = null;
    window.pollInterval = null;
    window.currentLeadData = null;
    window.autoSaveInterval = null; // Auto-save form data every 30 seconds
    window.dialStatusData = {}; // Server-synced dial status

    // ===== PERSISTENT DIAL TRACKING =====
    
    /**
     * Load dial status from server and render badges.
     * Called on page load and periodically to show real-time updates from other closers.
     */
    function loadDialStatus() {
        fetch('/ravens/leads/dial-status', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.dialStatusData = data.dials || {};
                renderDialBadges(data.dials, data.current_user_id);
            }
        })
        .catch(error => console.error('Failed to load dial status:', error));
    }

    /**
     * Render dial badges on each lead row showing who dialed it.
     */
    function renderDialBadges(dials, currentUserId) {
        // Clear all existing badges and row highlights
        document.querySelectorAll('.dial-badges').forEach(el => el.innerHTML = '');
        document.querySelectorAll('.lead-row').forEach(row => {
            row.classList.remove('dialed-by-me', 'dialed-by-others', 'dialed');
        });

        // Rebuild local dialedLeads set from server data
        window.dialedLeads = new Set();

        for (const [leadId, dialers] of Object.entries(dials)) {
            const badgeContainer = document.getElementById('dial-badges-' + leadId);
            const row = document.querySelector(`.lead-row[data-lead-id="${leadId}"]`);
            if (!badgeContainer) continue;

            let dialedByMe = false;
            let dialedByOthers = false;

            dialers.forEach(dialer => {
                const badge = document.createElement('span');
                badge.className = 'dial-badge' + (dialer.is_mine ? ' is-mine' : '');
                badge.style.backgroundColor = dialer.color;
                const countLabel = dialer.count > 1 ? ' ×' + dialer.count : '';
                badge.title = dialer.user_name + ' at ' + dialer.dialed_at + (dialer.count > 1 ? ' (' + dialer.count + ' dials)' : '');
                badge.innerHTML = dialer.initials + '<span class="dial-time">' + dialer.user_name + ' - ' + dialer.dialed_at + countLabel + '</span>';
                badgeContainer.appendChild(badge);

                if (dialer.is_mine) {
                    dialedByMe = true;
                    window.dialedLeads.add(leadId);
                } else {
                    dialedByOthers = true;
                }
            });

            // Apply row highlights
            if (row) {
                if (dialedByMe) {
                    row.classList.add('dialed-by-me');
                } else if (dialedByOthers) {
                    row.classList.add('dialed-by-others');
                }
            }
        }
    }

    /**
     * Record a dial to the server (persists to DB).
     */
    function recordDial(leadId, outcome = 'dialed') {
        fetch('/ravens/leads/record-dial', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ lead_id: leadId, outcome: outcome })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Dial recorded for lead', leadId);
                // Refresh dial status to show updated badges
                loadDialStatus();
            }
        })
        .catch(error => console.error('Failed to record dial:', error));
    }

    // Load dial status on page load
    loadDialStatus();
    
    // Refresh dial status every 30 seconds to see other closers' activity
    setInterval(loadDialStatus, 30000);

    // TEST: Ensure JavaScript is loading
    console.log('✅ Ravens calling script loaded');
    
    // Test function to verify modal functionality
    window.testRavensModal = function() {
        console.log('🧪 Testing Ravens modal functionality...');
        const modalElement = document.getElementById('callDetailsModal');
        console.log('🎭 Modal element:', modalElement);
        
        if (modalElement) {
            try {
                const modal = new bootstrap.Modal(modalElement);
                console.log('✅ Bootstrap modal object created successfully');
                modal.show();
                console.log('✅ Modal.show() called - modal should be visible');
                
                // Auto-close after 3 seconds for testing
                setTimeout(() => {
                    modal.hide();
                    console.log('🚪 Test modal closed automatically');
                }, 3000);
                
            } catch (error) {
                console.error('❌ Error in modal test:', error);
            }
        } else {
            console.error('❌ Modal element not found!');
        }
    };
    
    // Direct modal test with data - bypasses API call
    window.testRavensModalWithData = function() {
        console.log('🧪 Testing Ravens modal with sample data (bypassing API)...');
        
        const testCallData = {
            event_id: 'test-' + Date.now(),
            lead_data: {
                id: 999,
                cn_name: 'Test Customer',
                phone_number: '1234567890',
                date_of_birth: '1990-01-01',
                ssn: '123-45-6789'
            },
            lead_id: 999,
            status: 'connected'
        };
        
        console.log('🧪 Calling showCallModal directly with test data...');
        if (typeof showCallModal === 'function') {
            showCallModal(testCallData);
        } else {
            console.error('❌ showCallModal function not found!');
        }
    };
    
    // Make test available in console
    console.log('💡 Test commands available:');
    console.log('  - testRavensModal() - Basic modal test');
    console.log('  - testRavensModalWithData() - Full modal test with data');
    console.log('  - testRavensFormOpen() - Test full form with first lead');

    // Test function to open Ravens form directly with real lead data
    window.testRavensFormOpen = function() {
        console.log('🧪 TEST: Opening Ravens form for first lead...');
        const firstRow = document.querySelector('.lead-row');
        if (!firstRow) {
            alert('No leads available to test');
            return;
        }
        const leadId = firstRow.getAttribute('data-lead-id');
        const phone = firstRow.getAttribute('data-phone');
        const name = firstRow.querySelector('strong').textContent;
        
        console.log('🧪 TEST: Lead selected:', { leadId, phone, name });
        toastr.info('Opening test form in 2 seconds...', 'Test Mode');
        
        setTimeout(() => {
            console.log('🧪 Calling showRavensFormForCall...');
            showRavensFormForCall(leadId, phone, name, 'connected', 0);
        }, 2000);
    }

    // Save callback note for a lead
    window.saveCallbackNote = function(leadId, note) {
        console.log('💾 Saving callback note for lead', leadId, ':', note);

        fetch('{{ route('ravens.leads.save-callback-note') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: leadId,
                note: note
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Callback note saved:', data.message);
                // Update the timestamp display if note was saved
                if (data.note && data.updated_at) {
                    const input = document.querySelector(`input[data-lead-id="${leadId}"]`);
                    if (input) {
                        const existingTimestamp = input.nextElementSibling;
                        if (existingTimestamp && existingTimestamp.tagName === 'SMALL') {
                            existingTimestamp.innerHTML = `<i class="bx bx-time-five"></i> ${data.updated_at}`;
                        } else {
                            // Create timestamp display
                            const timestamp = document.createElement('small');
                            timestamp.className = 'text-muted d-block mt-1';
                            timestamp.innerHTML = `<i class="bx bx-time-five"></i> ${data.updated_at}`;
                            input.parentNode.insertBefore(timestamp, input.nextSibling);
                        }
                    }
                } else {
                    // Clear timestamp if note was cleared
                    const input = document.querySelector(`input[data-lead-id="${leadId}"]`);
                    if (input) {
                        const existingTimestamp = input.nextElementSibling;
                        if (existingTimestamp && existingTimestamp.tagName === 'SMALL') {
                            existingTimestamp.remove();
                        }
                    }
                }
            } else {
                console.error('❌ Failed to save callback note:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error saving callback note:', error);
        });
    }

    // Unified call function - uses proper Zoom API integration
    // Shows Ravens form after 10-second delay when call is initiated
    window.makeCall = function(leadId, phoneNumber, button) {
        console.log('makeCall called with:', leadId, phoneNumber);
        
        if (!phoneNumber) {
            alert('No phone number available for this lead');
            return;
        }

        // Show loading state
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Connecting...';

        // Use the proper Zoom API integration
        fetch(`/zoom/dial/${leadId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            // Check if response is not OK (4xx or 5xx)
            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('ZOOM_NOT_AUTHORIZED');
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Zoom API response:', data);
            
            if (data.success) {
                console.log('✅ Desktop call initiated - Zoom will fire webhooks when call connects');
                
                // Open Zoom Phone desktop app using an invisible link click
                // This prevents page navigation and keeps polling active
                if (data.zoom_url) {
                    const link = document.createElement('a');
                    link.href = data.zoom_url;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    console.log('📞 Zoom Phone link clicked:', data.zoom_url);
                }
                
                // Mark lead as dialed
                window.dialedLeads.add(leadId);
                
                // Record dial persistently to server
                recordDial(leadId, 'dialed');
                
                // Update UI
                const row = button.closest('.lead-row');
                if (row) {
                    row.classList.add('calling');
                    setTimeout(() => {
                        row.classList.remove('calling');
                        row.classList.add('dialed');
                    }, 1000);
                }
                
                console.log('📞 Call initiated - Opening Zoom Phone for ' + data.lead_name);
                
                // Start monitoring for webhook-triggered status updates
                startRealCallDetection(leadId, phoneNumber, data.lead_name);
                
            } else {
                if (data.error && data.error.includes('not authorized')) {
                    alert('❌ Zoom Not Connected\n\nRedirecting to connect your Zoom account...');
                    window.location.href = '/zoom/authorize';
                } else {
                    alert(`❌ API Error: ${data.error || 'Unknown error'}`);
                }
            }
        })
        .catch(error => {
            console.error('API request failed:', error);
            if (error.message === 'ZOOM_NOT_AUTHORIZED') {
                if (confirm('⚠️ Zoom Phone Not Connected\n\nYou need to connect your Zoom Phone account to make calls.\n\nClick OK to connect now.')) {
                    window.location.href = '/zoom/authorize';
                }
            } else {
                alert('❌ Connection failed: ' + error.message + '\n\nPlease try again or contact support.');
            }
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    // Get user's zoom number
    window.zoomNumber = '{{ Auth::user()->zoom_number ?? '' }}';
    window.sanitizedZoomNumber = '{{ Auth::user()->sanitized_zoom_number ?? '' }}';

    // Echo disabled - using API-based call monitoring instead
    // Safety check for echo references
    if (window.Echo && typeof window.Echo.channel === 'function') {
        console.log('Echo available but disabled for stability');
    }
    console.log('Echo disabled - using API-based call monitoring instead');

    // Auto-dial toggle button
    document.getElementById('autoDialBtn').addEventListener('click', function() {
        window.autoDialActive = !window.autoDialActive;
        const btn = this;
        const text = document.getElementById('autoDialText');
        const icon = btn.querySelector('i');

        if (window.autoDialActive) {
            btn.classList.add('active');
            text.textContent = 'Stop Auto-Dial';
            icon.className = 'bx bx-stop-circle me-1';
            window.currentLeadIndex = 0;
            autoDialNext();
        } else {
            btn.classList.remove('active');
            text.textContent = 'Start Auto-Dial';
            icon.className = 'bx bx-play-circle me-1';
            if (autoDialTimeout) {
                clearTimeout(autoDialTimeout);
            }
            // Remove calling highlight
            document.querySelectorAll('.lead-row').forEach(row => {
                row.classList.remove('calling');
            });
        }
    });

    // Auto-dial next lead
    function autoDialNext() {
        if (!window.autoDialActive) return;
        if (window.isCallActive) {
            console.log('Call in progress, waiting...');
            return;
        }

        const rows = document.querySelectorAll('.lead-row');

        // Find next undailed lead
        while (window.currentLeadIndex < rows.length) {
            const row = rows[window.currentLeadIndex];
            const leadId = row.dataset.leadId;

            if (!window.dialedLeads.has(leadId)) {
                // Found undailed lead, dial it
                const phone = row.dataset.phone;
                const dialBtn = row.querySelector('.dial-btn');

                // Highlight current row
                document.querySelectorAll('.lead-row').forEach(r => r.classList.remove('calling'));
                row.classList.add('calling');

                // Scroll to current lead
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Dial the lead
                makeCall(leadId, phone, dialBtn);
                return;
            }

            window.currentLeadIndex++;
        }

        // All leads dialed
        window.autoDialActive = false;
        const btn = document.getElementById('autoDialBtn');
        const text = document.getElementById('autoDialText');
        const icon = btn.querySelector('i');
        btn.classList.remove('active');
        text.textContent = 'Start Auto-Dial';
        icon.className = 'bx bx-play-circle me-1';

        alert('All leads have been dialed!');
    }

    // Test function for Zoom protocol
    function testZoomProtocol() {
        console.log('Testing Zoom protocol...');
        
        const testNumber = '2393871921'; // Hashim's number
        const zoomUrl = 'zoomphonecall://' + testNumber;
        
        console.log('Test Zoom URL:', zoomUrl);
        
        // Show confirmation
        const confirmed = confirm(`Testing Zoom Phone protocol with number: ${testNumber}\n\nThis will attempt to dial Hashim Shabbir.\n\nClick OK to test, Cancel to abort.`);
        
        if (confirmed) {
            toastr.info('Testing Zoom Phone protocol...', 'Test Mode');
            
            try {
                // Try multiple methods
                console.log('Method 1: window.location.href');
                window.location.href = zoomUrl;
                
                setTimeout(() => {
                    console.log('Method 2: Creating a link and clicking it');
                    const link = document.createElement('a');
                    link.href = zoomUrl;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }, 500);
                
                setTimeout(() => {
                    console.log('Method 3: Using iframe');
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = zoomUrl;
                    document.body.appendChild(iframe);
                    
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);
                }, 1000);
                
            } catch (error) {
                console.error('Error testing Zoom:', error);
                alert('Error: ' + error.message);
            }
        }
    }

    // New flow: Show form after 10 seconds, monitor for call end
    window.startRealCallDetection = function(leadId, phoneNumber, leadName) {
        console.log("📞 Starting call detection (show form after 12 seconds)...", { leadId, phoneNumber, leadName });
        
        let isMonitoringActive = true;
        let formShown = false;
        let checkInterval = null;
        
        // Store current call info
        window.currentCallInfo = { leadId, phoneNumber, leadName };
        
        // SHOW LOADING MESSAGE
        toastr.info('Call in progress... Form will open in 12 seconds', 'Please Wait', {
            timeOut: 12000,
            progressBar: true
        });
        
        // Show form after 12 seconds - GUARANTEED
        const formTimer = setTimeout(() => {
            if (isMonitoringActive) {
                console.log('⏰ 12 seconds passed - FORCE showing Ravens form NOW');
                formShown = true;
                
                // Direct form opening - bypass complex checks
                console.log('🔍 Opening form directly for lead:', leadId);
                showRavensFormForCall(leadId, phoneNumber, leadName, 'connected', 0);
            } else {
                console.log('⚠️ Form timer fired but monitoring was stopped');
            }
        }, 12000); // 12 seconds
        
        // Start polling to detect call end (but only close form if it's already shown)
        function checkCallStatus() {
            if (!isMonitoringActive) {
                console.log('⏹️ Monitoring stopped - clearing interval');
                if (checkInterval) clearInterval(checkInterval);
                return;
            }
            
            console.log(`🔍 Checking call status for lead ${leadId}... (form shown: ${formShown})`);
            
            fetch(`/zoom/call-status/${leadId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Call status response:', data);
                    
                    if (data.success) {
                        const status = data.status || data.call_status;
                        console.log(`Current status: ${status}`);
                        
                        // Check for ended states - comprehensive webhook detection
                        if (status === 'ended' || status === 'completed' || status === 'failed' || 
                            status === 'cancelled' || status === 'missed' || status === 'voicemail' || 
                            status === 'rejected' || status === 'busy' || status === 'hangup' || 
                            status === 'disconnected' || status === 'timeout') {
                            
                            console.log(`❌ Call ended detected via webhook! Status: ${status}, Form shown: ${formShown}`);
                            
                            // Only close form if it's already shown
                            if (formShown) {
                                console.log('🚪 Closing form - call ended after form was shown');
                                isMonitoringActive = false;
                                if (checkInterval) clearInterval(checkInterval);
                                closeRavensForm();
                                toastr.info(`Call ended`, 'Call Completed');
                            } else {
                                // Form not shown yet - cancel timer so form will never appear
                                console.log('⛔ Call ended before form shown - canceling 12-second timer');
                                isMonitoringActive = false;
                                clearTimeout(formTimer); // Cancel the 12-second form display timer
                                if (checkInterval) clearInterval(checkInterval);
                                toastr.info(`Call ${status} before form opened`, 'Call Ended');
                            }
                        } else {
                            console.log(`✅ Call still active (status: ${status})`);
                        }
                    } else {
                        console.warn('⚠️ Status check returned success=false');
                    }
                })
                .catch(error => {
                    console.error('❌ Status check failed:', error);
                });
        }
        
        // Poll every 2 seconds to detect call end
        console.log('▶️ Starting status polling every 2 seconds');
        checkInterval = setInterval(checkCallStatus, 2000);
        
        // Initial check immediately
        checkCallStatus();
        
        // Cleanup after 10 minutes
        setTimeout(() => {
            if (isMonitoringActive) {
                console.log('⏰ Call monitoring timeout - stopping detection');
                isMonitoringActive = false;
                clearTimeout(formTimer);
                if (checkInterval) clearInterval(checkInterval);
            }
        }, 600000); // 10 minutes
    }
    
    // Close the Ravens form when call ends
    window.closeRavensForm = function() {
        console.log('🚪 Closing Ravens form');
        
        // Stop auto-save and save one final time
        if (window.autoSaveInterval) {
            clearInterval(window.autoSaveInterval);
            window.autoSaveInterval = null;
            console.log('🛑 Auto-save interval cleared');
        }
        autoSaveFormData(true); // Final save before closing
        
        const modalElement = document.getElementById('callDetailsModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
        window.currentCallInfo = null;
        window.currentLeadData = null;
    }
    
    // Show Ravens form when call is connected
    window.showRavensFormForCall = function(leadId, phoneNumber, leadName, callStatus, duration) {
        console.log(`🎯 FORCING Ravens form to show NOW: ${leadName}`);
        console.log('🔍 Debug: leadId=' + leadId + ', phoneNumber=' + phoneNumber + ', callStatus=' + callStatus);
        
        toastr.success(`Opening form for: ${leadName}`, 'Call Form Ready', { timeOut: 3000 });
        
        // Fetch full lead data from the server to populate the form
        console.log('🌐 Fetching lead data from:', `/ravens/leads/${leadId}/data`);
        fetch(`/ravens/leads/${leadId}/data`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
            .then(response => {
                console.log('🌐 API Response Status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(leadData => {
                console.log('📋 Got full lead data:', leadData);
                console.log('🔍 Checking if showCallModal function exists...');
                console.log('showCallModal type:', typeof showCallModal);
                
                // Create proper call event data for the modal
                const callEventData = {
                    event_id: 'call_' + Date.now(),
                    lead_data: {
                        id: leadData.id || leadId,
                        cn_name: leadData.cn_name || leadData.name || leadName,
                        phone_number: leadData.phone_number || phoneNumber,
                        date_of_birth: leadData.date_of_birth || '',
                        ssn: leadData.ssn || '',
                        gender: leadData.gender || '',
                        beneficiaries: leadData.beneficiaries || [],
                        carrier_name: leadData.carrier_name || leadData.carrier || '',
                        coverage_amount: leadData.coverage_amount || leadData.coverage || '',
                        monthly_premium: leadData.monthly_premium || leadData.premium || '',
                        birth_place: leadData.birth_place || '',
                        smoker: leadData.smoker || 0,
                        height_weight: leadData.height_weight || '',
                        height: leadData.height || '',
                        weight: leadData.weight || '',
                        address: leadData.address || '',
                        medical_issue: leadData.medical_issue || '',
                        medications: leadData.medications || '',
                        doctor_name: leadData.doctor_name || '',
                        doctor_address: leadData.doctor_address || '',
                        policy_type: leadData.policy_type || '',
                        initial_draft_date: leadData.initial_draft_date || '',
                        bank_name: leadData.bank_name || '',
                        account_type: leadData.account_type || '',
                        routing_number: leadData.routing_number || '',
                        account_number: leadData.account_number || '',
                        verified_by: leadData.verified_by || '',
                        bank_balance: leadData.bank_balance || '',
                        closer_name: leadData.closer_name || ''
                    },
                    lead_id: leadId,
                    status: callStatus,
                    caller_number: 'User',
                    callee_number: phoneNumber,
                    call_connected_at: new Date().toISOString()
                };
                
                // Show the Ravens form modal with full data
                if (typeof showCallModal === 'function') {
                    console.log('✅ Opening Ravens form with full lead data');
                    console.log('🔍 Call event data:', callEventData);
                    showCallModal(callEventData);
                } else {
                    console.error('❌ showCallModal function not found! Redirecting to lead details');
                    // Fallback - redirect to lead details
                    window.location.href = `/ravens/leads/${leadId}`;
                }
            })
            .catch(error => {
                console.error('❌ Failed to fetch lead data:', error);
                console.error('API endpoint might be broken or lead ID invalid');
                console.error('ERROR DETAILS:', error.message, error.stack);
                
                // STILL SHOW THE MODAL with whatever data we have
                console.log('⚠️ Showing modal anyway with minimal data due to fetch error...');
                toastr.warning('Could not load all lead details, showing basic form', 'Warning');
                
                // Try to show modal anyway with minimal data
                const callEventData = {
                    event_id: 'call_' + Date.now(),
                    lead_data: {
                        id: leadId,
                        cn_name: leadName,
                        phone_number: phoneNumber,
                        beneficiaries: []
                    },
                    lead_id: leadId,
                    status: callStatus
                };

                console.log('🔍 Checking if showCallModal exists:', typeof showCallModal);
                if (typeof showCallModal === 'function') {
                    console.log('✅ Calling showCallModal with minimal data');
                    showCallModal(callEventData);
                } else {
                    console.error('❌ showCallModal function not available - major JS error');
                    alert('Ravens form function not found. Please refresh the page and try again.');
                }
            });
    }
    
    // Check if call was actually connected (not just ringing/rejected)
    function checkIfCallWasConnected(leadId, phoneNumber, leadName, callDuration) {
        console.log(`Checking if call was connected. Duration: ${callDuration}ms`);
        
        // Use API to determine if call was actually connected
        fetch('/api/call-status/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: leadId,
                call_duration: callDuration,
                user_interacted: true // User returned focus, indicating interaction
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Call status check result:', data);
            
            if (data.is_connected) {
                console.log(`Call confirmed as connected (confidence: ${data.confidence}%) - showing Ravens form`);
                
                // Get lead data for Ravens form
                return fetch('/api/leads/' + leadId, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
            } else {
                console.log(`Call likely not connected (confidence: ${data.confidence}%) - no Ravens form`);
                return null;
            }
        })
        .then(response => {
            if (!response) return null;
            return response.json();
        })
        .then(leadData => {
            if (!leadData) return;
            
            const callEventData = {
                event_id: "connected-call-" + Date.now(),
                lead_data: leadData,
                lead_id: leadId,
                status: "connected",
                caller_number: "User",
                callee_number: phoneNumber,
                call_connected_at: new Date().toISOString()
            };
            
            if (typeof showCallModal === "function") {
                console.log("Opening Ravens form for:", leadName || "Lead #" + leadId);
                showCallModal(callEventData);
            } else {
                console.error("showCallModal function not available");
            }
        })
        .catch(error => {
            console.error('Call status check failed:', error);
            // Fallback to simple timing if API fails
            if (callDuration >= 15000) { // More conservative fallback
                console.log('API failed, using fallback timing detection');
                // Simple fallback implementation here if needed
            }
        });
    }
    
    // Open Ravens form with API call data
    function openRavensFormViaApi(leadId, phoneNumber) {
        fetch(`/api/leads/${leadId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch lead data');
            }
            return response.json();
        })
        .then(leadData => {
            const callEventData = {
                event_id: 'api-call-' + Date.now(),
                lead_data: leadData,
                call_connected_at: new Date().toISOString(),
                caller_number: 'API User',
                callee_number: phoneNumber
            };
            
            console.log('Opening Ravens form via API for:', leadData.cn_name || 'Lead #' + leadId);
            
            if (typeof showCallModal === 'function') {
                showCallModal(callEventData);
                alert('✅ Ravens Form Opened!\n\nFill out the details while on your call.');
            } else {
                alert('✅ Call detected but Ravens form unavailable. Please refresh the page.');
            }
        })
        .catch(error => {
            console.error('Failed to open Ravens form:', error);
            alert('❌ Could not open Ravens form. Please try refreshing the page.');
        });
    }
    
    // Smart call connection detection (fallback method)
    function detectCallConnection(leadId, phoneNumber) {
        let checkCount = 0;
        const maxChecks = 20; // Check for up to 40 seconds
        
        // Store original page title and visibility state
        const originalTitle = document.title;
        const originalVisibility = document.visibilityState;
        
        console.log('Starting call connection detection...');
        
        // Method 1: Monitor page/window focus changes
        const handleVisibilityChange = () => {
            if (document.visibilityState === 'visible' && window.isCallActive === false) {
                console.log('Page became visible - user likely returned from Zoom');
                // Wait a moment, then check if call is connected
                setTimeout(() => {
                    checkCallConnection();
                }, 1000);
            }
        };
        
        // Method 2: Monitor window focus
        const handleWindowFocus = () => {
            if (window.isCallActive === false) {
                console.log('Window gained focus - user likely returned from call');
                setTimeout(() => {
                    checkCallConnection();
                }, 500);
            }
        };
        
        // Method 3: Periodic intelligent checking
        const checkCallConnection = () => {
            if (window.isCallActive) return; // Already handled
            
            checkCount++;
            console.log(`Call connection check ${checkCount}/${maxChecks}`);
            
            // Ask user if call is connected (more intelligently timed)
            const message = `Call connection check for ${phoneNumber}:

` +
                          `• Is your call currently connected?
` +
                          `• If yes, click OK to open Ravens form
` +
                          `• If no, click Cancel to keep waiting
` +
                          `• Check ${checkCount} of ${maxChecks}`;
            
            const isConnected = confirm(message);
            
            if (isConnected) {
                window.isCallActive = true;
                cleanup();
                openRavensForm(leadId, phoneNumber);
            } else if (checkCount >= maxChecks) {
                // Final attempt
                const forceOpen = confirm('Maximum checks reached. Open Ravens form anyway?');
                if (forceOpen) {
                    window.isCallActive = true;
                    cleanup();
                    openRavensForm(leadId, phoneNumber);
                } else {
                    cleanup();
                    console.log('Call connection detection stopped by user');
                }
            } else {
                // Continue checking with increasing delays
                const delay = Math.min(2000 + (checkCount * 500), 8000); // 2s to 8s delays
                setTimeout(checkCallConnection, delay);
            }
        };
        
        // Cleanup function
        const cleanup = () => {
            document.removeEventListener('visibilitychange', handleVisibilityChange);
            window.removeEventListener('focus', handleWindowFocus);
            document.title = originalTitle;
        };
        
        // Set up event listeners
        document.addEventListener('visibilitychange', handleVisibilityChange);
        window.addEventListener('focus', handleWindowFocus);
        
        // Change page title to indicate call in progress
        document.title = '📞 Call in Progress - ' + originalTitle;
        
        // Start the first check after initial delay
        setTimeout(checkCallConnection, 5000); // Wait 5 seconds initially
    }
    
    // Fallback clipboard copy function
    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Phone number copied to clipboard!');
        } catch (err) {
            alert('Could not copy to clipboard. Please manually copy: ' + text);
        }
        document.body.removeChild(textArea);
    }
    
    // Open Ravens form
    function openRavensForm(leadId, phoneNumber) {
        // Create mock call data for the Ravens form
        const callEventData = {
            event_id: 'manual-dial-' + Date.now(),
            lead_data: {
                id: leadId,
                phone_number: phoneNumber,
                cn_name: 'Lead #' + leadId
            },
            call_connected_at: new Date().toISOString(),
            caller_number: phoneNumber,
            callee_number: phoneNumber
        };
        
        console.log('Opening Ravens form for lead:', leadId);
        if (typeof showCallModal === 'function') {
            showCallModal(callEventData);
            alert('Ravens form opened! Fill out the details while on the call.');
        } else {
            alert('Ravens form function not available. Please refresh the page.');
        }
    }

    // ===== LOCAL POLLING SYSTEM FOR CALL POPUP (DEPRECATED - Using Zoom API polling instead) =====
    // Disabled in favor of aggressive Zoom API polling in startRealCallDetection()
    
    // function startCallPolling() {
    //     console.log('Starting call event polling...');
    //     window.pollInterval = setInterval(checkForCallEvents, 2000);
    //     checkForCallEvents(); // Check immediately
    // }

    // function checkForCallEvents() {
    //     fetch('/api/call-events/poll', {
    //         headers: {
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //             'Accept': 'application/json'
    //         }
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.has_call && data.event_id !== window.currentEventId) {
    //             window.currentEventId = data.event_id;
    //             showCallModal(data);
    //         }
    //     })
    //     .catch(error => console.error('Polling error:', error));
    // }

    // ===== PHASE NAVIGATION SYSTEM =====
    // Note: currentLeadData is already declared globally at line 477 as window.currentLeadData

    function closeCallModal() {
        // Stop auto-save and save one final time before closing
        if (window.autoSaveInterval) {
            clearInterval(window.autoSaveInterval);
            window.autoSaveInterval = null;
            console.log('🛑 Auto-save interval cleared');
        }
        
        // Final save before closing
        autoSaveFormData(true); // true = silent save on close
        
        const modalElement = document.getElementById('callDetailsModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            } else {
                // Fallback if modal instance not found
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
        console.log('Call modal closed');
    }

    function updatePhaseDots(activePhase) {
        document.querySelectorAll('#phaseIndicator .ph-dot').forEach(dot => {
            dot.classList.toggle('active', parseInt(dot.dataset.phase) === activePhase);
        });
    }

    function goToPhase1() {
        console.log('📋 Switching to Phase 1');
        const phase1 = document.getElementById('phase1');
        const phase2 = document.getElementById('phase2');
        const phase3 = document.getElementById('phase3');
        
        if (phase1) {
            phase1.classList.remove('d-none');
            phase1.style.display = 'block';
        }
        if (phase2) { phase2.classList.add('d-none'); phase2.style.display = 'none'; }
        if (phase3) { phase3.classList.add('d-none'); phase3.style.display = 'none'; }
        updatePhaseDots(1);
    }

    function goToPhase2() {
        console.log('Navigating to Phase 2...');
        const phase1 = document.getElementById('phase1');
        const phase2 = document.getElementById('phase2');
        const phase3 = document.getElementById('phase3');
        if (phase1) { phase1.classList.add('d-none'); phase1.style.display = 'none'; }
        if (phase2) { phase2.classList.remove('d-none'); phase2.style.display = 'block'; }
        if (phase3) { phase3.classList.add('d-none'); phase3.style.display = 'none'; }
        updatePhaseDots(2);
    }

    function goToPhase3_old() {
        // Superseded by goToPhase3() below
    }

    // Second goToPhase1 removed - using the primary definition above

    // Add beneficiary row dynamically
    window.addBeneficiaryRow = function() {
        const container = document.getElementById('beneficiaries-container-ravens');
        if (!container) {
            console.error('Beneficiary container not found');
            return;
        }
        
        const index = container.querySelectorAll('.beneficiary-ravens-row').length;
        const row = document.createElement('div');
        row.className = 'beneficiary-ravens-row row mb-2 g-2';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control beneficiary-name-ravens" 
                       placeholder="Beneficiary Name" required>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control beneficiary-dob-ravens" required>
            </div>
            <div class="col-md-3">
                <select class="form-select beneficiary-relation-ravens">
                    <option value="">Relation</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Child">Child</option>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Grandchild">Grandchild</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-ravens-row').remove()">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        console.log('✅ Beneficiary row added');
    }

    // Add beneficiary row for Phase 3
    window.addBeneficiaryRowPhase3 = function() {
        const container = document.getElementById('beneficiaries-container-phase3');
        if (!container) {
            console.error('Phase 3 Beneficiary container not found');
            return;
        }
        
        const row = document.createElement('div');
        row.className = 'beneficiary-phase3-row row mb-2 g-2';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm beneficiary-name-phase3" 
                       placeholder="Beneficiary Name">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control form-control-sm beneficiary-dob-phase3">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm beneficiary-relation-phase3">
                    <option value="">Relation</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Child">Child</option>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Grandchild">Grandchild</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-phase3-row').remove()">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        console.log('✅ Phase 3 Beneficiary row added');
    }

    // REMOVED DUPLICATE BROKEN showCallModal FUNCTION
    
    function goToPhase3() {
        populatePhase3WithData();
        copyBeneficiariesFromPhase2ToPhase3();
        const phase1 = document.getElementById('phase1');
        const phase2 = document.getElementById('phase2');
        const phase3 = document.getElementById('phase3');
        if (phase1) { phase1.classList.add('d-none'); phase1.style.display = 'none'; }
        if (phase2) { phase2.classList.add('d-none'); phase2.style.display = 'none'; }
        if (phase3) { phase3.classList.remove('d-none'); phase3.style.display = 'block'; }
        updatePhaseDots(3);
    }

    // Copy beneficiaries from Phase 2 to Phase 3
    function copyBeneficiariesFromPhase2ToPhase3() {
        const phase2Container = document.getElementById('beneficiaries-container-ravens');
        const phase3Container = document.getElementById('beneficiaries-container-phase3');
        
        if (!phase2Container || !phase3Container) {
            console.error('Beneficiary containers not found');
            return;
        }

        // Clear Phase 3 container
        phase3Container.innerHTML = '';

        // Get all beneficiaries from Phase 2
        const phase2Rows = phase2Container.querySelectorAll('.beneficiary-ravens-row');
        
        if (phase2Rows.length === 0) {
            // Add one empty row if no beneficiaries
            window.addBeneficiaryRowPhase3();
            return;
        }

        // Copy each beneficiary to Phase 3
        phase2Rows.forEach((phase2Row) => {
            const name = phase2Row.querySelector('.beneficiary-name-ravens')?.value || '';
            const dob = phase2Row.querySelector('.beneficiary-dob-ravens')?.value || '';
            const relation = phase2Row.querySelector('.beneficiary-relation-ravens')?.value || '';

            const row = document.createElement('div');
            row.className = 'beneficiary-phase3-row row mb-2 g-2';
            row.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm beneficiary-name-phase3" 
                           placeholder="Beneficiary Name" value="${name}">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control form-control-sm beneficiary-dob-phase3" value="${dob}">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm beneficiary-relation-phase3">
                        <option value="">Relation</option>
                        <option value="Spouse" ${relation === 'Spouse' ? 'selected' : ''}>Spouse</option>
                        <option value="Child" ${relation === 'Child' ? 'selected' : ''}>Child</option>
                        <option value="Parent" ${relation === 'Parent' ? 'selected' : ''}>Parent</option>
                        <option value="Sibling" ${relation === 'Sibling' ? 'selected' : ''}>Sibling</option>
                        <option value="Grandchild" ${relation === 'Grandchild' ? 'selected' : ''}>Grandchild</option>
                        <option value="Other" ${relation === 'Other' ? 'selected' : ''}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-phase3-row').remove()">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            `;
            phase3Container.appendChild(row);
        });

        console.log(`✅ Copied ${phase2Rows.length} beneficiaries to Phase 3`);
    }

    function populatePhase3WithData() {
        const ld = window.currentLeadData;

        // Helper to format date for display
        const formatDate = (dateStr) => {
            if (!dateStr) return 'N/A';
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            } catch {
                return dateStr || 'N/A';
            }
        };

        // Helper to format date for input (YYYY-MM-DD)
        const formatDateInput = (dateStr) => {
            if (!dateStr) return '';
            try {
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
                const date = new Date(dateStr);
                return date.toISOString().split('T')[0];
            } catch {
                return '';
            }
        };

        // Personal Information
        document.getElementById('orig_name').textContent = ld.cn_name || 'N/A';
        document.getElementById('orig_phone').textContent = ld.phone_number || 'N/A';
        document.getElementById('orig_secondary_phone').textContent = ld.secondary_phone_number || 'N/A';
        document.getElementById('orig_state').textContent = ld.state || 'N/A';
        document.getElementById('orig_zip').textContent = ld.zip_code || 'N/A';
        document.getElementById('orig_dob').textContent = formatDate(ld.date_of_birth);
        document.getElementById('orig_gender').textContent = ld.gender || 'N/A';
        document.getElementById('orig_birthplace').textContent = ld.birth_place || 'N/A';
        document.getElementById('orig_ssn').textContent = document.getElementById('phase2_ssn').value || ld.ssn || 'N/A';
        document.getElementById('orig_smoker').textContent = ld.smoker == 1 ? 'Yes' : 'No';
        document.getElementById('orig_height').textContent = ld.height || 'N/A';
        document.getElementById('orig_weight').textContent = ld.weight ? ld.weight + ' lbs' : 'N/A';
        document.getElementById('orig_driving_license').textContent = ld.driving_license || 'N/A';
        // Address fallback: use address, else state, else birth place
        let addressDisplay3 = ld.address;
        if (!addressDisplay3 || addressDisplay3.trim() === '') {
            addressDisplay3 = ld.state || ld.birth_place || 'N/A';
        }
        document.getElementById('orig_address').textContent = addressDisplay3;
        document.getElementById('orig_emergency_contact').textContent = ld.emergency_contact || 'N/A';

        // Medical Information
        document.getElementById('orig_medical_issue').textContent = ld.medical_issue || 'N/A';
        document.getElementById('orig_medications').textContent = ld.medications || 'N/A';
        document.getElementById('orig_doctor').textContent = ld.doctor_name || 'N/A';
        document.getElementById('orig_doctor_phone').textContent = ld.doctor_number || 'N/A';
        document.getElementById('orig_doctor_address').textContent = ld.doctor_address || 'N/A';

        // Policy Information
        // Show all current beneficiaries (names and DOBs if available)
        let beneficiariesDisplay = 'N/A';
        if (ld.beneficiaries && ld.beneficiaries.length > 0) {
            beneficiariesDisplay = ld.beneficiaries.map(b => {
                if (b.dob) {
                    return b.name + ' (' + formatDate(b.dob) + ')';
                }
                return b.name;
            }).join(', ');
        } else if (ld.beneficiary_raw) {
            beneficiariesDisplay = ld.beneficiary_raw;
        }
        document.getElementById('orig_beneficiary').textContent = beneficiariesDisplay;
        // Note: orig_beneficiary_dob display has been removed in favor of showing all beneficiaries with DOBs
        document.getElementById('orig_policy_type').textContent = ld.policy_type || 'N/A';
        document.getElementById('orig_policy_number').textContent = ld.policy_number || 'N/A';
        document.getElementById('orig_carrier').textContent = document.getElementById('phase2_carrier').value || 'N/A';
        document.getElementById('orig_coverage').textContent = document.getElementById('phase2_coverage').value ? '$' + parseFloat(document.getElementById('phase2_coverage').value).toLocaleString() : 'N/A';
        document.getElementById('orig_premium').textContent = document.getElementById('phase2_premium').value ? '$' + parseFloat(document.getElementById('phase2_premium').value).toFixed(2) : 'N/A';
        document.getElementById('orig_draft_date').textContent = formatDate(ld.initial_draft_date);
        document.getElementById('orig_future_draft_date').textContent = formatDate(ld.future_draft_date);

        // Banking Information
        document.getElementById('orig_bank_name').textContent = ld.bank_name || 'N/A';
        document.getElementById('orig_account_title').textContent = ld.account_title || 'N/A';
        document.getElementById('orig_account_type').textContent = ld.account_type || 'N/A';
        document.getElementById('orig_routing').textContent = ld.routing_number || 'N/A';
        document.getElementById('orig_account').textContent = ld.account_number || 'N/A';
        document.getElementById('orig_verified_by').textContent = ld.verified_by || 'N/A';
        document.getElementById('orig_balance').textContent = ld.bank_balance ? '$' + parseFloat(ld.bank_balance).toFixed(2) : 'N/A';

        // Card Information
        document.getElementById('orig_card_number').textContent = ld.card_number || 'N/A';
        document.getElementById('orig_cvv').textContent = ld.cvv || 'N/A';
        document.getElementById('orig_expiry_date').textContent = ld.expiry_date || 'N/A';

        // Additional Information
        document.getElementById('orig_closer').textContent = ld.closer_name || 'N/A';
        document.getElementById('orig_source').textContent = ld.source || 'N/A';

        // Pre-fill change inputs with Phase 2 data
        document.getElementById('change_name').value = ld.cn_name || '';
        document.getElementById('change_phone').value = ld.phone_number || '';
        document.getElementById('change_secondary_phone').value = ld.secondary_phone_number || '';
        document.getElementById('change_state').value = ld.state || '';
        document.getElementById('change_zip').value = ld.zip_code || '';
        document.getElementById('change_dob').value = formatDateInput(document.getElementById('phase2_dob').value);
        document.getElementById('change_ssn').value = document.getElementById('phase2_ssn').value || '';
        document.getElementById('change_emergency_contact').value = ld.emergency_contact || '';
        // Beneficiary is now handled separately in beneficiaries array
        document.getElementById('change_carrier').value = document.getElementById('phase2_carrier').value || '';
        document.getElementById('change_coverage').value = document.getElementById('phase2_coverage').value || '';
        document.getElementById('change_premium').value = document.getElementById('phase2_premium').value || '';
        document.getElementById('change_future_draft_date').value = formatDateInput(ld.future_draft_date);
        document.getElementById('change_doctor_phone').value = ld.doctor_number || '';
        document.getElementById('change_driving_license').value = ld.driving_license || '';
        document.getElementById('change_height').value = ld.height || '';
        document.getElementById('change_weight').value = ld.weight || '';
        document.getElementById('change_card_number').value = ld.card_number || '';
        document.getElementById('change_cvv').value = ld.cvv || '';
        document.getElementById('change_expiry_date').value = ld.expiry_date || '';
        document.getElementById('change_policy_number').value = ld.policy_number || '';
        document.getElementById('change_account_title').value = ld.account_title || '';
        document.getElementById('change_source').value = ld.source || '';
    }

    function validatePhase2Fields() {
        // All fields are now optional, always enable the Continue button
        const showMoreBtn = document.getElementById('showMoreBtn');
        if (showMoreBtn) {
            showMoreBtn.disabled = false;
            showMoreBtn.classList.remove('btn-secondary');
        }
    }

    function showCallModal(callData) {
        console.log('=== CALL CONNECTED ===', callData);
        console.log('🔍 Attempting to show Ravens modal...');
        
        const leadData = callData.lead_data;
        window.currentLeadData = leadData;

        // PHASE 1: Show caller identification - Check if elements exist first
        const callerNameEl = document.getElementById('callerName');
        const callerPhoneEl = document.getElementById('callerPhone');
        
        if (callerNameEl) {
            callerNameEl.textContent = leadData.cn_name || 'Unknown Caller';
            console.log('✅ Caller name set:', leadData.cn_name);
        } else {
            console.error('❌ callerName element not found!');
        }
        
        if (callerPhoneEl) {
            callerPhoneEl.textContent = leadData.phone_number || 'No Number';
            console.log('✅ Caller phone set:', leadData.phone_number);
        } else {
            console.error('❌ callerPhone element not found!');
        }

        // Helper to format date for display
        const formatDateDisplay = (dateStr) => {
            if (!dateStr) return 'Not available';
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            } catch {
                return dateStr;
            }
        };

        // Helper to format date for input (YYYY-MM-DD)
        const formatDateInput = (dateStr) => {
            if (!dateStr) return '';
            try {
                // If already in YYYY-MM-DD format, return as-is
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
                // Otherwise parse and format
                const date = new Date(dateStr);
                return date.toISOString().split('T')[0];
            } catch {
                return '';
            }
        };

        // PHASE 2: Populate CURRENT VALUE displays (read-only)
        // Use safe element access to prevent crashes if elements don't exist
        const safeSetText = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            } else {
                console.warn(`⚠️ Element not found: ${id}`);
            }
        };
        
        const safeSetValue = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
            } else {
                console.warn(`⚠️ Element not found: ${id}`);
            }
        };
        
        safeSetText('displayName', leadData.cn_name || 'Not available');
        safeSetText('displayPhone', leadData.phone_number || 'Not available');
        safeSetText('displaySecondaryPhone', leadData.secondary_phone_number || 'N/A');
        safeSetText('displayState', leadData.state || 'N/A');
        safeSetText('displayZipCode', leadData.zip_code || 'N/A');
        safeSetText('displayDOB', formatDateDisplay(leadData.date_of_birth));
        safeSetText('displaySSN', leadData.ssn || 'Not available');
        // Address fallback: use address, else state, else birth place
        let addressDisplay = leadData.address;
        if (!addressDisplay || addressDisplay.trim() === '') {
            addressDisplay = leadData.state || leadData.birth_place || 'Not available';
        }
        safeSetText('displayAddress', addressDisplay);
        safeSetText('displayEmergencyContact', leadData.emergency_contact || 'N/A');
        
        // Handle beneficiaries display - show as comma-separated list
        // Show all current beneficiaries (names and DOBs if available)
        let beneficiariesDisplay = 'Not available';
        if (leadData.beneficiaries && leadData.beneficiaries.length > 0) {
            beneficiariesDisplay = leadData.beneficiaries.map(b => {
                if (b.dob) {
                    return b.name + ' (' + formatDateDisplay(b.dob) + ')';
                }
                return b.name;
            }).join(', ');
        } else if (leadData.beneficiary_raw) {
            beneficiariesDisplay = leadData.beneficiary_raw;
        }
        safeSetText('displayBeneficiary', beneficiariesDisplay);
        
        safeSetText('displayCarrier', leadData.carrier_name || 'Not available');
        safeSetText('displayCoverage', leadData.coverage_amount ? '$' + parseFloat(leadData.coverage_amount).toLocaleString() : 'Not available');
        safeSetText('displayPremium', leadData.monthly_premium ? '$' + parseFloat(leadData.monthly_premium).toFixed(2) : 'Not available');
        safeSetText('displayAccountNumber', leadData.account_number || 'Not available');

        // PHASE 2: Pre-fill CHANGES fields with existing values (user can modify)
        safeSetValue('phase2_name', '');
        safeSetValue('phase2_phone', '');
        safeSetValue('phase2_secondary_phone', leadData.secondary_phone_number || '');
        safeSetValue('phase2_state', leadData.state || '');
        safeSetValue('phase2_zip', leadData.zip_code || '');
        safeSetValue('phase2_dob', formatDateInput(leadData.date_of_birth));
        safeSetValue('phase2_ssn', leadData.ssn || '');
        // Pre-fill address field with fallback
        let addressValue = leadData.address;
        if (!addressValue || addressValue.trim() === '') {
            addressValue = leadData.state || leadData.birth_place || '';
        }
        safeSetValue('phase2_address', addressValue);
        safeSetValue('phase2_emergency_contact', leadData.emergency_contact || '');
        
        // Clear existing beneficiary rows
        const beneficiaryContainer = document.getElementById('beneficiaries-container-ravens');
        if (beneficiaryContainer) {
            beneficiaryContainer.innerHTML = '';
            window.beneficiaryIndexRavens = 0;
            
            // Populate beneficiaries from lead data
            if (leadData.beneficiaries && leadData.beneficiaries.length > 0) {
                leadData.beneficiaries.forEach((beneficiary, index) => {
                const row = document.createElement('div');
                row.className = 'beneficiary-ravens-row row mb-2 g-2';
                row.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" class="form-control beneficiary-name-ravens" 
                               placeholder="Beneficiary Name" value="${beneficiary.name || ''}" required>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control beneficiary-dob-ravens" 
                               value="${formatDateInput(beneficiary.dob || '')}" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select beneficiary-relation-ravens">
                            <option value="">Relation</option>
                            <option value="Spouse" ${(beneficiary.relation || '') === 'Spouse' ? 'selected' : ''}>Spouse</option>
                            <option value="Child" ${(beneficiary.relation || '') === 'Child' ? 'selected' : ''}>Child</option>
                            <option value="Parent" ${(beneficiary.relation || '') === 'Parent' ? 'selected' : ''}>Parent</option>
                            <option value="Sibling" ${(beneficiary.relation || '') === 'Sibling' ? 'selected' : ''}>Sibling</option>
                            <option value="Grandchild" ${(beneficiary.relation || '') === 'Grandchild' ? 'selected' : ''}>Grandchild</option>
                            <option value="Other" ${(beneficiary.relation || '') === 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-beneficiary-ravens">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                `;
                beneficiaryContainer.appendChild(row);
                window.beneficiaryIndexRavens++;
            });
            } else {
                // Add one empty beneficiary row
                const row = document.createElement('div');
                row.className = 'beneficiary-ravens-row row mb-2 g-2';
                row.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" class="form-control beneficiary-name-ravens" 
                               placeholder="Beneficiary Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control beneficiary-dob-ravens" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select beneficiary-relation-ravens">
                            <option value="">Relation</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Child">Child</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Grandchild">Grandchild</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-beneficiary-ravens">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                `;
                beneficiaryContainer.appendChild(row);
                window.beneficiaryIndexRavens++;
            }
        } else {
            console.warn('⚠️ beneficiaries-container-ravens element not found');
        }
        
        safeSetValue('phase2_carrier', leadData.carrier_name || '');
        safeSetValue('phase2_coverage', leadData.coverage_amount || '');
        safeSetValue('phase2_premium', leadData.monthly_premium || '');

        // Validate Phase 2 fields after populating
        if (typeof validatePhase2Fields === 'function') {
            validatePhase2Fields();
        }

        // Show modal and start at Phase 1
        const modalElement = document.getElementById('callDetailsModal');
        console.log('🎭 Modal element found:', modalElement);
        
        if (!modalElement) {
            console.error('❌ Modal element not found! Cannot show Ravens form.');
            return;
        }
        
        try {
            console.log('🔍 Checking Bootstrap availability:', typeof bootstrap);
            if (typeof bootstrap === 'undefined') {
                console.error('❌ Bootstrap is not available! This is the problem.');
                throw new Error('Bootstrap not available');
            }
            
            const modal = new bootstrap.Modal(modalElement);
            console.log('🎭 Bootstrap modal created:', modal);
            
            // CRITICAL: Show the modal first, then make sure phase1 is visible
            modal.show();
            console.log('✅ Modal.show() called');
            
            // Start auto-save interval (save every 30 seconds)
            if (window.autoSaveInterval) {
                clearInterval(window.autoSaveInterval);
            }
            window.autoSaveInterval = setInterval(() => {
                autoSaveFormData(false); // Show auto-save notification
            }, 30000); // Every 30 seconds
            console.log('💾 Auto-save started (every 30 seconds)');
            
            // Ensure phase1 is visible after modal shows
            setTimeout(() => {
                console.log('🎭 Making sure phase1 is visible...');
                goToPhase1();
                
                // Double-check phase1 visibility
                const phase1 = document.getElementById('phase1');
                if (phase1) {
                    phase1.classList.remove('d-none');
                    phase1.style.display = 'block';
                    console.log('🔧 Phase1 forced visible');
                }
            }, 100);
            
            console.log('✅ Ravens modal should now be visible with phase1');
            
            // Check if modal is actually visible after a longer delay to allow animation
            setTimeout(() => {
                const isVisible = modalElement.classList.contains('show');
                const computedStyle = window.getComputedStyle(modalElement);
                console.log('🔍 Modal visibility check:');
                console.log('  - Has "show" class:', isVisible);
                console.log('  - Display style:', computedStyle.display);
                console.log('  - Visibility style:', computedStyle.visibility);
                console.log('  - Opacity style:', computedStyle.opacity);
                console.log('  - Z-index:', computedStyle.zIndex);
                
                // Check if modal backdrop exists
                const backdrop = document.querySelector('.modal-backdrop');
                console.log('  - Backdrop exists:', !!backdrop);
                
                // Check phase1 visibility specifically
                const phase1 = document.getElementById('phase1');
                if (phase1) {
                    const phase1Style = window.getComputedStyle(phase1);
                    console.log('  - Phase1 display:', phase1Style.display);
                    console.log('  - Phase1 visibility:', phase1Style.visibility);
                }
                
                if (!isVisible || computedStyle.display === 'none') {
                    console.error('❌ Modal is not visible! There may be a CSS or Bootstrap issue.');
                    
                    // Force show the modal using direct DOM manipulation
                    console.log('🔧 Attempting manual modal visibility fix...');
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                    modalElement.setAttribute('aria-hidden', 'false');
                    modalElement.setAttribute('aria-modal', 'true');
                    modalElement.setAttribute('role', 'dialog');
                    
                    // Ensure modal is above everything else
                    modalElement.style.zIndex = '9999';
                    
                    // Also make sure phase1 is visible
                    if (phase1) {
                        phase1.style.display = 'block';
                        console.log('🔧 Phase1 also forced visible');
                    }
                    
                    console.log('🔧 Manual fix applied, checking again...');
                    setTimeout(() => {
                        const newStyle = window.getComputedStyle(modalElement);
                        console.log('🔍 After manual fix - Display:', newStyle.display, 'Visibility:', newStyle.visibility);
                    }, 100);
                } else {
                    console.log('✅ Modal appears to be visible correctly');
                }
            }, 800);
            
        } catch (error) {
            console.error('❌ Error showing modal:', error);
            console.log('💡 Trying fallback method...');
            // Fallback: try using jQuery if Bootstrap modal fails
            if (typeof $ !== 'undefined') {
                $('#callDetailsModal').modal('show');
                console.log('✅ Fallback: jQuery modal shown');
                goToPhase1();
                
                // Start auto-save interval for fallback case too
                if (window.autoSaveInterval) {
                    clearInterval(window.autoSaveInterval);
                }
                window.autoSaveInterval = setInterval(() => {
                    autoSaveFormData(false); // Show auto-save notification
                }, 30000); // Every 30 seconds
                console.log('💾 Auto-save started (fallback, every 30 seconds)');
            } else {
                console.error('❌ Both Bootstrap and jQuery modal methods failed');
            }
        }

        // Mark as read
        if (callData.event_id && !callData.event_id.toString().startsWith('test-')) {
            fetch(`/api/call-events/${callData.event_id}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        }
    }

    // Old polling disabled - using Zoom API polling instead
    // startCallPolling();

    // Test function to manually trigger Ravens call modal (accessible from sidebar)
    window.testRavensCallModal = function() {
        console.log('Ravens test button clicked');
        const testCallData = {
            event_id: 'test-' + Date.now(),
            lead_data: {
                id: 999999,
                cn_name: 'John Test Ravens Customer',
                phone_number: '+1-555-987-6543',
                date_of_birth: '1985-06-15',
                ssn: '123-45-6789',
                gender: 'Male',
                birth_place: 'Test City',
                smoker: 0,
                height_weight: '5ft 10in, 180 lbs',
                height: '5ft 10in',
                weight: '180',
                address: '123 Test Street, Test City, TX 12345',
                beneficiary: 'Jane Test Beneficiary',
                carrier_name: 'Test Insurance Co',
                coverage_amount: '100000',
                monthly_premium: '75.50',
                closer_name: @json(Auth::user()->name ?? 'Test Closer'),
            },
            call_connected_at: new Date().toISOString()
        };
        
        console.log('Calling showCallModal with:', testCallData);
        showCallModal(testCallData);
        toastr.info('Test Ravens modal opened with sample data', 'Test Mode');
    }

    /**
     * Monitor call connection intelligently
     */
    function startCallConnectionMonitor(leadId, phoneNumber) {
        let checkAttempts = 0;
        const maxAttempts = 10; // Check for 20 seconds (2 sec intervals)
        
        const checkConnection = () => {
            checkAttempts++;
            
            // Ask user if call connected after reasonable time
            if (checkAttempts === 3) { // After 6 seconds
                const isConnected = confirm('Is your call connected? Click OK if the call connected successfully, or Cancel if not connected yet.');
                
                if (isConnected) {
                    triggerRavensFormForLead(leadId, phoneNumber);
                    return;
                }
            }
            
            // Auto-trigger after 15 seconds as fallback
            if (checkAttempts >= 8) {
                console.log('Auto-triggering Ravens form after 15 seconds');
                triggerRavensFormForLead(leadId, phoneNumber);
                return;
            }
            
            // Continue checking
            if (checkAttempts < maxAttempts) {
                setTimeout(checkConnection, 2000);
            }
        };
        
        // Start checking after 2 seconds
        setTimeout(checkConnection, 2000);
    }

    /**
     * Trigger Ravens form popup for a specific lead (manual trigger)
     */
    function triggerRavensFormForLead(leadId, phoneNumber) {
        // Fetch lead data first
        fetch(`/api/leads/${leadId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(leadData => {
            if (leadData) {
                // Create call event data similar to webhook format
                const callEventData = {
                    event_id: 'manual-dial-' + Date.now(),
                    lead_data: leadData,
                    call_connected_at: new Date().toISOString(),
                    caller_number: phoneNumber,
                    callee_number: phoneNumber
                };
                
                console.log('Triggering Ravens form for lead:', leadData.cn_name || leadData.name || 'Unknown');
                showCallModal(callEventData);
                toastr.success('Call connected - Ravens form opened', 'Call Connected');
            }
        })
        .catch(error => {
            console.error('Failed to fetch lead data:', error);
            toastr.error('Could not load lead information');
        });
    }
    
    /**
     * Auto-save form data silently (called every 30 seconds and on form close)
     */
    function autoSaveFormData(isSilent = false) {
        const leadId = window.currentLeadData?.id;
        
        if (!leadId) {
            console.log('⚠️ Auto-save skipped: No lead ID');
            return;
        }

        // Collect beneficiary data
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-phase3-row').forEach((row) => {
            const name = row.querySelector('.beneficiary-name-phase3')?.value;
            const dob = row.querySelector('.beneficiary-dob-phase3')?.value;
            const relation = row.querySelector('.beneficiary-relation-phase3')?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null, relation: relation || null });
            }
        });
        
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('change_phone')?.value || null,
            secondary_phone_number: document.getElementById('change_secondary_phone')?.value || null,
            state: document.getElementById('change_state')?.value || null,
            zip_code: document.getElementById('change_zip')?.value || null,
            date_of_birth: document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('change_gender')?.value || null,
            address: document.getElementById('change_address')?.value || null,
            emergency_contact: document.getElementById('change_emergency_contact')?.value || null,
            driving_license: document.getElementById('change_driving_license')?.value || null,
            birth_place: document.getElementById('change_birthplace')?.value || null,
            height: document.getElementById('change_height')?.value || null,
            weight: document.getElementById('change_weight')?.value || null,
            smoker: document.getElementById('change_smoker')?.value || null,
            medical_issue: document.getElementById('change_medical_issue')?.value || null,
            medications: document.getElementById('change_medications')?.value || null,
            doctor_name: document.getElementById('change_doctor')?.value || null,
            doctor_number: document.getElementById('change_doctor_phone')?.value || null,
            doctor_address: document.getElementById('change_doctor_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            future_draft_date: document.getElementById('change_future_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            card_number: document.getElementById('change_card_number')?.value || null,
            cvv: document.getElementById('change_cvv')?.value || null,
            expiry_date: document.getElementById('change_expiry_date')?.value || null,
            closer_name: document.getElementById('change_closer')?.value || null,
            policy_number: document.getElementById('change_policy_number')?.value || null,
            account_title: document.getElementById('change_account_title')?.value || null,
            source: document.getElementById('change_source')?.value || null,
        };
        
        // Check if any data has actually been entered (besides default values)
        const hasData = Object.values(formData).some(val => {
            if (val === null || val === '' || val === leadId) return false;
            if (Array.isArray(val) && val.length === 0) return false;
            return true;
        });
        
        if (!hasData) {
            if (!isSilent) {
                console.log('⚠️ Auto-save skipped: No data entered yet');
            }
            return;
        }
        
        // Send to server
        fetch('/ravens/leads/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (!isSilent) {
                    console.log('💾 Auto-save successful');
                    toastr.success('Form data saved', 'Auto-saved', { timeOut: 2000 });
                }
            } else {
                console.error('⚠️ Auto-save failed:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Auto-save error:', error);
        });
    }

    /**
     * Save and Exit - Save lead data without marking as sale
     */
    function saveAndExit() {
        // Collect all form data from Phase 3
        const leadId = window.currentLeadData?.id;
        
        if (!leadId) {
            toastr.error('Lead ID not found');
            return;
        }

        // Collect beneficiary data from Phase 3
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-phase3-row').forEach((row) => {
            const name = row.querySelector('.beneficiary-name-phase3')?.value;
            const dob = row.querySelector('.beneficiary-dob-phase3')?.value;
            const relation = row.querySelector('.beneficiary-relation-phase3')?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null, relation: relation || null });
            }
        });
        
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('change_phone')?.value || null,
            secondary_phone_number: document.getElementById('change_secondary_phone')?.value || null,
            state: document.getElementById('change_state')?.value || null,
            zip_code: document.getElementById('change_zip')?.value || null,
            date_of_birth: document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('change_gender')?.value || null,
            address: document.getElementById('change_address')?.value || null,
            emergency_contact: document.getElementById('change_emergency_contact')?.value || null,
            driving_license: document.getElementById('change_driving_license')?.value || null,
            birth_place: document.getElementById('change_birthplace')?.value || null,
            height: document.getElementById('change_height')?.value || null,
            weight: document.getElementById('change_weight')?.value || null,
            smoker: document.getElementById('change_smoker')?.value || null,
            medical_issue: document.getElementById('change_medical_issue')?.value || null,
            medications: document.getElementById('change_medications')?.value || null,
            doctor_name: document.getElementById('change_doctor')?.value || null,
            doctor_number: document.getElementById('change_doctor_phone')?.value || null,
            doctor_address: document.getElementById('change_doctor_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            future_draft_date: document.getElementById('change_future_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            card_number: document.getElementById('change_card_number')?.value || null,
            cvv: document.getElementById('change_cvv')?.value || null,
            expiry_date: document.getElementById('change_expiry_date')?.value || null,
            closer_name: document.getElementById('change_closer')?.value || null,
            policy_number: document.getElementById('change_policy_number')?.value || null,
            account_title: document.getElementById('change_account_title')?.value || null,
            source: document.getElementById('change_source')?.value || null,
        };
        
        // Send to server
        fetch('/ravens/leads/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Lead information saved successfully');
                // Close modal
                closeCallModal();
            } else {
                toastr.error(data.message || 'Failed to save lead information');
            }
        })
        .catch(error => {
            console.error('Error saving lead:', error);
            toastr.error('An error occurred while saving');
        });
    }
    
    /**
     * Submit Sale - Mark lead as sold and send to sales section
     */
    function submitSale() {
        const leadId = window.currentLeadData?.id;
        
        if (!leadId) {
            toastr.error('Lead ID not found');
            return;
        }
        
        // All fields are now optional, skip required field validation
        
        // Collect beneficiary data from Phase 3
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-phase3-row').forEach((row) => {
            const name = row.querySelector('.beneficiary-name-phase3')?.value;
            const dob = row.querySelector('.beneficiary-dob-phase3')?.value;
            const relation = row.querySelector('.beneficiary-relation-phase3')?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null, relation: relation || null });
            }
        });
        
        // Collect all form data
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('phase2_name')?.value || document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('phase2_phone')?.value || document.getElementById('change_phone')?.value || null,
            secondary_phone_number: document.getElementById('phase2_secondary_phone')?.value || document.getElementById('change_secondary_phone')?.value || null,
            state: document.getElementById('phase2_state')?.value || document.getElementById('change_state')?.value || null,
            zip_code: document.getElementById('phase2_zip')?.value || document.getElementById('change_zip')?.value || null,
            date_of_birth: document.getElementById('phase2_dob')?.value || document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('phase2_ssn')?.value || document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('phase2_gender')?.value || document.getElementById('change_gender')?.value || null,
            address: document.getElementById('phase2_address')?.value || document.getElementById('change_address')?.value || null,
            emergency_contact: document.getElementById('phase2_emergency_contact')?.value || document.getElementById('change_emergency_contact')?.value || null,
            driving_license: document.getElementById('change_driving_license')?.value || null,
            birth_place: document.getElementById('change_birthplace')?.value || null,
            height: document.getElementById('change_height')?.value || null,
            weight: document.getElementById('change_weight')?.value || null,
            smoker: document.getElementById('change_smoker')?.value || null,
            medical_issue: document.getElementById('change_medical_issue')?.value || null,
            medications: document.getElementById('change_medications')?.value || null,
            doctor_name: document.getElementById('change_doctor')?.value || null,
            doctor_number: document.getElementById('change_doctor_phone')?.value || null,
            doctor_address: document.getElementById('change_doctor_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('phase2_carrier')?.value || document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('phase2_coverage')?.value || document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('phase2_premium')?.value || document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            future_draft_date: document.getElementById('change_future_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('phase2_account_number')?.value || document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            card_number: document.getElementById('change_card_number')?.value || null,
            cvv: document.getElementById('change_cvv')?.value || null,
            expiry_date: document.getElementById('change_expiry_date')?.value || null,
            closer_name: @json(Auth::user()->name ?? 'Unknown'),
            policy_number: document.getElementById('change_policy_number')?.value || null,
            account_title: document.getElementById('change_account_title')?.value || null,
            source: document.getElementById('change_source')?.value || null,
            
            // Extract carrier and partner info from the combined value
            insurance_carrier_id: (() => {
                const carrierSelect = document.getElementById('phase3_policy_carrier');
                const selectedOption = carrierSelect?.options[carrierSelect.selectedIndex];
                return selectedOption?.dataset?.carrierName || null;
            })(),
            partner_id: document.getElementById('phase3_partner_id')?.value || null,
            assigned_partner: document.getElementById('phase3_assigned_partner')?.value || null,
            state: document.getElementById('phase3_approved_state')?.value || null,
            
            followup_required: document.getElementById('phase3_followup_required')?.value || null,
            followup_scheduled_at: document.getElementById('phase3_followup_scheduled_at')?.value || null,
        };
        
        // Confirm submission
        if (!confirm('Are you sure you want to submit this sale? This will move the lead to the sales section and notify QA and managers.')) {
            return;
        }
        
        // Send to server
        fetch('/ravens/leads/submit-sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Sale submitted successfully!');
                
                // Show warning if this is a repeat sale
                if (data.is_repeat_sale) {
                    toastr.warning(data.repeat_sale_message, 'Repeat Sale Detected', {
                        timeOut: 10000
                    });
                }
                
                // Close modal
                closeCallModal();
                
                // Reload page to refresh leads list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(data.message || 'Failed to submit sale');
            }
        })
        .catch(error => {
            console.error('Error submitting sale:', error);
            toastr.error('An error occurred while submitting sale');
        });
    }
    
    // Professional Zoom API Integration ✅
    // - Uses real OAuth authentication with Zoom
    // - Professional call status monitoring via Zoom API
    // - Ravens form appears only when call is verified as completed
    // - No popups or confirmations - direct professional calling
    
    // Beneficiary management for Ravens form
    document.addEventListener('DOMContentLoaded', function() {
        let beneficiaryIndexRavens = 1;
        
        const addBeneficiaryBtn = document.getElementById('add-beneficiary-ravens');
        if (addBeneficiaryBtn) {
            addBeneficiaryBtn.addEventListener('click', function() {
                const container = document.getElementById('beneficiaries-ravens-container');
                const newRow = document.createElement('div');
                newRow.className = 'row g-2 mb-2 beneficiary-ravens-row';
                newRow.setAttribute('data-index', beneficiaryIndexRavens);
                newRow.innerHTML = `
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-sm" name="beneficiaries[${beneficiaryIndexRavens}][name]" placeholder="Beneficiary Name ${beneficiaryIndexRavens + 1}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control form-control-sm" name="beneficiaries[${beneficiaryIndexRavens}][dob]" placeholder="DOB">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-beneficiary-ravens">
                            <i class="bx bx-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                beneficiaryIndexRavens++;
                
                // Attach remove handler
                newRow.querySelector('.remove-beneficiary-ravens').addEventListener('click', function() {
                    newRow.remove();
                });
            });
        }
        
        // Remove beneficiary (for existing rows)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-beneficiary-ravens')) {
                e.target.closest('.beneficiary-ravens-row').remove();
            }
        });
    });

    /**
     * Dispose current lead with a disposition reason
     */
    function disposeCurrentLead(disposition) {
        if (!window.currentLeadData || !window.currentLeadData.id) {
            toastr.error('No active lead to dispose');
            return;
        }

        const dispositionLabels = {
            'no_answer': 'No Answer',
            'wrong_number': 'Wrong Number',
            'wrong_details': 'Wrong Details'
        };

        const confirmMessage = `Are you sure you want to dispose this lead as "${dispositionLabels[disposition]}"?`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        // Optional: Ask for notes
        const notes = prompt('Add notes (optional):');

        // Send disposition request
        fetch('{{ route('ravens.leads.dispose') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: window.currentLeadData.id,
                disposition: disposition,
                notes: notes || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Lead disposed successfully as ' + data.disposition);
                
                // Close modal and remove from list
                $('#callingModal').modal('hide');
                window.currentLeadData = null;
                window.isCallActive = false;
                
                // Reload page to refresh lead list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(data.message || 'Failed to dispose lead');
            }
        })
        .catch(error => {
            console.error('Error disposing lead:', error);
            toastr.error('An error occurred while disposing the lead');
        });
    }
    
</script>
@endsection
