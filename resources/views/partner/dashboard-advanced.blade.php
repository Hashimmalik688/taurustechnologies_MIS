@extends('layouts.partner')

@section('title') Partner Dashboard @endsection

@section('css')
<style>
/* ═══════════════════════════════════════════════════════════
   PARTNER DASHBOARD  ·  Clean & Focused
   ═══════════════════════════════════════════════════════════ */
:root {
    --pd-indigo:  #4f46e5;
    --pd-green:   #059669;
    --pd-red:     #dc2626;
    --pd-amber:   #d97706;
    --pd-teal:    #0d9488;
    --pd-br:      .6rem;
    --pd-sh:      0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
    --pd-ease:    cubic-bezier(.22,1,.36,1);
}

/* ── Push nav-badge from hero --*/
@keyframes pd-fadein{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
.pd-anim{animation:pd-fadein .45s var(--pd-ease) both;}
.pd-d1{animation-delay:.04s;}.pd-d2{animation-delay:.08s;}.pd-d3{animation-delay:.12s;}.pd-d4{animation-delay:.16s;}.pd-d5{animation-delay:.22s;}

/* ── Hero ── */
.pd-hero{
    background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);
    padding:1.75rem 2rem 1.5rem;
    position:relative;overflow:hidden;
    margin-bottom:1.5rem;
}
.pd-hero::after{
    content:'';position:absolute;width:380px;height:380px;
    background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);
    top:-80px;right:-60px;pointer-events:none;
}
.pd-hero-body{position:relative;z-index:1;}

/* top row */
.pd-hero-top{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;}
.pd-hero-name{font-size:1.35rem;font-weight:900;color:#fff;letter-spacing:-.3px;line-height:1.1;}
.pd-hero-meta{margin-top:.3rem;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;}
.pd-chip{font-size:.66rem;font-weight:700;padding:.18rem .55rem;border-radius:999px;text-transform:uppercase;letter-spacing:.5px;}
.pd-chip-glass{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.72);}
.pd-chip-green{background:rgba(5,150,105,.25);border:1px solid rgba(5,150,105,.45);color:#6ee7b7;display:inline-flex;align-items:center;gap:.3rem;}
.pd-chip-red{background:rgba(220,38,38,.2);border:1px solid rgba(220,38,38,.35);color:#fca5a5;}
.pd-chip-amber{background:rgba(245,158,11,.18);border:1px solid rgba(245,158,11,.3);color:#fde68a;}
.pd-pulse{width:5px;height:5px;border-radius:50%;background:#6ee7b7;display:inline-block;animation:pd-pulse-dot 1.8s ease-in-out infinite;}
@keyframes pd-pulse-dot{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.35;transform:scale(.6);}}

/* period form */
.pd-period-form{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;}
.pd-period-label{font-size:.66rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.35);}
.pd-period-input{
    background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
    border-radius:.35rem;padding:.35rem .6rem;font-size:.82rem;color:#fff;width:140px;
}
.pd-period-input:focus{outline:none;border-color:rgba(255,255,255,.4);background:rgba(255,255,255,.12);}
.pd-period-btn{
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;
    padding:.35rem .75rem;border-radius:.35rem;font-size:.8rem;font-weight:600;cursor:pointer;
    transition:background .15s;display:inline-flex;align-items:center;gap:.25rem;
}
.pd-period-btn:hover{background:rgba(255,255,255,.22);}

/* KPI grid */
.pd-hero-kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:.85rem;}
@media(max-width:768px){.pd-hero-kpis{grid-template-columns:1fr 1fr;}}

.pd-kpi{
    background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    border-radius:var(--pd-br);padding:.9rem 1.1rem;position:relative;overflow:hidden;
    transition:background .2s,transform .2s var(--pd-ease);cursor:default;
}
.pd-kpi:hover{background:rgba(255,255,255,.1);transform:translateY(-1px);}
.pd-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;border-radius:2px 2px 0 0;}
.pd-kpi.k-indigo::before{background:linear-gradient(90deg,#6366f1,#a5b4fc);}
.pd-kpi.k-green::before{background:linear-gradient(90deg,#059669,#34d399);}
.pd-kpi.k-slate::before{background:linear-gradient(90deg,#475569,#94a3b8);}

.pd-kpi-icon{font-size:1.1rem;margin-bottom:.4rem;opacity:.45;}
.pd-kpi.k-indigo .pd-kpi-icon{color:#a5b4fc;}
.pd-kpi.k-green .pd-kpi-icon{color:#34d399;}
.pd-kpi.k-slate .pd-kpi-icon{color:#94a3b8;}

.pd-kpi-val{font-size:1.75rem;font-weight:900;color:#fff;letter-spacing:-.8px;line-height:1;margin-bottom:.3rem;}
.pd-kpi-lbl{font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:rgba(255,255,255,.38);}
.pd-kpi-sub{font-size:.72rem;color:rgba(255,255,255,.3);margin-top:.2rem;}

/* progress micro-bar */
.pd-bar{height:2px;background:rgba(255,255,255,.1);border-radius:999px;margin-top:.6rem;overflow:hidden;}
.pd-bar-fill{height:100%;border-radius:999px;}
.k-indigo .pd-bar-fill{background:linear-gradient(90deg,#6366f1,#a5b4fc);}
.k-green .pd-bar-fill{background:linear-gradient(90deg,#059669,#34d399);}

/* Chargeback info note */
.pd-cb-note{
    margin-top:.85rem;
    padding:.55rem .85rem;
    background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);
    border-radius:.4rem;
    font-size:.76rem;color:rgba(255,255,255,.55);
    display:flex;align-items:center;gap:.5rem;
}
.pd-cb-note strong{color:#fde68a;}
.pd-cb-note i{color:#fbbf24;font-size:.9rem;flex-shrink:0;}

/* ── Balance alert ── */
.pd-balance-alert{
    display:flex;align-items:center;gap:.65rem;
    padding:.8rem 1.25rem;border-radius:var(--pd-br);
    margin-bottom:1.25rem;border:1px solid;
    font-size:.86rem;font-weight:600;
    box-shadow:var(--pd-sh);
}
.pd-balance-alert.owe{background:#fef2f2;color:#7f1d1d;border-color:#fecaca;}
.pd-balance-alert.credit{background:#f0fdf4;color:#14532d;border-color:#bbf7d0;}
.pd-balance-alert i{font-size:1.05rem;flex-shrink:0;}
.pd-balance-alert .amt{font-weight:900;font-size:.95rem;}

/* ── Stats strip ── */
.pd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;margin-bottom:1.25rem;}
@media(max-width:900px){.pd-stats{grid-template-columns:repeat(2,1fr);}}

.pd-stat{
    background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);
    padding:.85rem 1rem;box-shadow:var(--pd-sh);
    display:flex;align-items:center;gap:.75rem;
    transition:transform .18s var(--pd-ease),box-shadow .18s;
}
.pd-stat:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.09);}
.pd-stat-icon{
    width:38px;height:38px;border-radius:.4rem;
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;
}
.si-violet{background:rgba(109,40,217,.1);color:#6d28d9;}
.si-green{background:rgba(5,150,105,.1);color:#059669;}
.si-amber{background:rgba(217,119,6,.1);color:#d97706;}
.si-blue{background:rgba(2,132,199,.1);color:#0284c7;}
.pd-stat-val{font-size:1.3rem;font-weight:900;letter-spacing:-.3px;line-height:1;color:#111827;}
.pd-stat-lbl{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-top:.1rem;}
.pd-stat-sub{font-size:.7rem;color:#d1d5db;}

/* ── Card ── */
.pd-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pd-br);box-shadow:var(--pd-sh);overflow:hidden;}
.pd-head{padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.06);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.pd-head h6{font-size:.88rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.35rem;color:#111827;}
.pd-head h6 i{color:var(--pd-indigo);font-size:1rem;}
.pd-count{background:rgba(79,70,229,.08);color:var(--pd-indigo);font-size:.7rem;font-weight:700;padding:.12rem .45rem;border-radius:.2rem;}
.pd-body{padding:1rem 1.1rem;}
.pd-body.np{padding:0;}

/* ── Carrier cards ── */
.pd-carriers{display:grid;grid-template-columns:repeat(auto-fill,minmax(185px,1fr));gap:.6rem;}
.pd-carrier-card{
    background:#fafafa;border:1px solid rgba(0,0,0,.07);border-radius:.45rem;
    padding:.75rem .9rem;transition:border-color .15s,box-shadow .15s;
}
.pd-carrier-card:hover{border-color:rgba(79,70,229,.3);box-shadow:0 0 0 3px rgba(79,70,229,.05);}
.pd-carrier-name{font-size:.88rem;font-weight:800;color:#111827;margin-bottom:.45rem;}
.pd-state-pill{
    display:inline-block;font-size:.62rem;font-weight:700;padding:.08rem .32rem;
    border-radius:.18rem;background:rgba(79,70,229,.08);color:var(--pd-indigo);
    margin:.06rem .04rem 0 0;letter-spacing:.15px;
}
.pd-carrier-meta{font-size:.72rem;color:#9ca3af;margin-top:.35rem;}

.pd-states-footer{
    padding:.55rem 1.1rem;background:#fafafa;
    border-top:1px solid rgba(0,0,0,.06);
    display:flex;flex-wrap:wrap;gap:.25rem;align-items:center;
}

/* ── Tables ── */
.pd-table{width:100%;border-collapse:collapse;font-size:.83rem;}
.pd-table thead th{
    font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;
    color:#9ca3af;border-bottom:1px solid rgba(0,0,0,.08);
    padding:.55rem .85rem;background:#f9fafb;white-space:nowrap;
}
.pd-table tbody td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#374151;}
.pd-table tfoot td{padding:.6rem .85rem;font-weight:700;border-top:2px solid rgba(0,0,0,.08);background:#fafafa;font-size:.82rem;}
.pd-table tbody tr:hover{background:rgba(79,70,229,.022);}
.pd-table tbody tr:last-child td{border-bottom:none;}

/* Type chips */
.tc{font-size:.62rem;font-weight:800;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;letter-spacing:.3px;text-transform:uppercase;}
.tc-sale{background:#d1fae5;color:#065f46;}
.tc-pay{background:#dbeafe;color:#1e3a8a;}
.tc-cb{background:#fef3c7;color:#78350f;}
.tc-other{background:#f3f4f6;color:#374151;}

/* Status chips */
.sc{font-size:.66rem;font-weight:700;padding:.14rem .42rem;border-radius:.22rem;display:inline-block;text-transform:capitalize;}
.sc-ok{background:#d1fae5;color:#065f46;}
.sc-warn{background:#fef9c3;color:#713f12;}
.sc-info{background:#dbeafe;color:#1e3a8a;}
.sc-danger{background:#fee2e2;color:#7f1d1d;}
.sc-def{background:#f3f4f6;color:#374151;}

/* Balance cols */
.col-dr{color:#4f46e5;font-weight:700;}
.col-cr{color:#059669;font-weight:700;}
.col-dim{color:#d1d5db;}
.rb-pos{color:#dc2626;font-weight:700;font-size:.78rem;}
.rb-neg{color:#059669;font-weight:700;font-size:.78rem;}
.rb-zero{color:#9ca3af;font-size:.78rem;}

/* Micro bar in table */
.mbar{height:3px;border-radius:999px;background:#e5e7eb;margin-top:.28rem;overflow:hidden;}
.mbar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--pd-teal),#34d399);max-width:100%;}

/* Empty state */
.pd-empty{text-align:center;padding:2rem 1rem;}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.4rem;opacity:.2;color:#9ca3af;}
.pd-empty p{font-size:.84rem;color:#9ca3af;margin:0;}

/* Quick-link shortcut cards */
.pd-shortcut{transition:transform .18s,box-shadow .18s,border-color .18s;}
.pd-shortcut:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.09);border-color:rgba(79,70,229,.25);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-shortcut div[style*="color:#111827"]{color:var(--text-primary,#e0e0e0)!important;}

/* Dark themes */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table thead th{background:var(--bg-secondary,#16162a);color:var(--text-muted,#888);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody td{color:var(--text-primary,#ddd);border-color:var(--border-color,rgba(255,255,255,.04));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-stat{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-stat-val{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-card{background:var(--bg-tertiary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-name{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-states-footer{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-balance-alert.owe{background:rgba(220,38,38,.1);border-color:rgba(220,38,38,.25);color:#fca5a5;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-balance-alert.credit{background:rgba(5,150,105,.1);border-color:rgba(5,150,105,.25);color:#6ee7b7;}
</style>
@endsection

@section('content')
@php
    $earnedRatio = $projectedRevenue > 0 ? min(100, ($earnedRevenue / $projectedRevenue) * 100) : 0;
    $taurusPct   = $partner->our_commission_percentage ?? 15;
@endphp

{{-- ═══════════════════════════════════════════
     HERO
═══════════════════════════════════════════ --}}
<div class="pd-hero pd-anim">
    <div class="pd-hero-body">

        <div class="pd-hero-top">
            <div>
                <div class="pd-hero-name">{{ $partner->name }}</div>
                <div class="pd-hero-meta">
                    <span class="pd-chip pd-chip-glass">{{ $partner->code }}</span>
                    <span class="pd-chip pd-chip-glass">{{ number_format($totalContracts) }} sales</span>
                    <span class="pd-chip pd-chip-green"><span class="pd-pulse"></span> Live</span>
                    @if($chargebacks > 0)
                    <span class="pd-chip pd-chip-amber">CB: ${{ number_format($chargebacks,0) }} shared</span>
                    @endif
                </div>
            </div>
            <form method="GET" action="{{ route('partner.dashboard') }}" class="pd-period-form" id="pd-filter-form">
                <span class="pd-period-label">View by</span>
                <select class="pd-period-input" id="pd-filter-mode" style="width:auto;padding-right:1.6rem;" onchange="toggleFilterMode()">
                    <option value="month" {{ !request('date_from') ? 'selected' : '' }}>Month</option>
                    <option value="range" {{ request('date_from') ? 'selected' : '' }}>Date range</option>
                </select>

                {{-- Month mode --}}
                <span id="pd-month-wrap" style="{{ request('date_from') ? 'display:none' : 'display:flex' }};align-items:center;gap:.4rem;">
                    <input type="month" name="month" class="pd-period-input" value="{{ $month }}">
                </span>

                {{-- Date range mode --}}
                <span id="pd-range-wrap" style="{{ request('date_from') ? 'display:flex' : 'display:none' }};align-items:center;gap:.35rem;">
                    <input type="date" name="date_from" class="pd-period-input" style="width:130px;" value="{{ request('date_from') }}" placeholder="From">
                    <span style="color:rgba(255,255,255,.35);font-size:.8rem;">→</span>
                    <input type="date" name="date_to" class="pd-period-input" style="width:130px;" value="{{ request('date_to') }}" placeholder="To">
                </span>

                <button type="submit" class="pd-period-btn"><i class="bx bx-filter-alt"></i> Apply</button>
                @if(request('date_from') || request('month'))
                <a href="{{ route('partner.dashboard') }}" class="pd-period-btn" style="text-decoration:none;background:rgba(255,100,100,.15);border-color:rgba(255,100,100,.3);"><i class="bx bx-reset"></i></a>
                @endif
            </form>
        </div>

        <div class="pd-hero-kpis">
            {{-- Earned Revenue --}}
            <div class="pd-kpi k-green pd-anim pd-d1">
                <i class="bx bx-check-circle pd-kpi-icon"></i>
                <div class="pd-kpi-val">$<span class="cval" data-target="{{ (int)$earnedRevenue }}">{{ number_format($earnedRevenue,0) }}</span></div>
                <div class="pd-kpi-lbl">Earned Revenue</div>
                <div class="pd-kpi-sub">Commissions confirmed &amp; paid out</div>
                <div class="pd-bar"><div class="pd-bar-fill" style="width:{{ $earnedRatio }}%;"></div></div>
            </div>

            {{-- Your Share (after Taurus cut) --}}
            <div class="pd-kpi k-indigo pd-anim pd-d2">
                <i class="bx bx-wallet pd-kpi-icon"></i>
                <div class="pd-kpi-val">$<span class="cval" data-target="{{ (int)$partnerEarnedShare }}">{{ number_format($partnerEarnedShare,0) }}</span></div>
                <div class="pd-kpi-lbl">Your (Partner) Net Share</div>
                <div class="pd-kpi-sub">Your cut after {{ $taurusPct }}% Taurus fee</div>
                <div class="pd-bar"><div class="pd-bar-fill" style="width:{{ $earnedRatio }}%;"></div></div>
            </div>

            {{-- Ledger Balance --}}
            <div class="pd-kpi k-slate pd-anim pd-d3">
                <i class="bx bx-receipt pd-kpi-icon"></i>
                <div class="pd-kpi-val"
                     style="color:{{ $currentBalance > 0 ? '#fbbf24' : ($currentBalance < 0 ? '#34d399' : '#94a3b8') }};">
                    {{ $currentBalance > 0 ? '+' : ($currentBalance < 0 ? '−' : '') }}$<span class="cval" data-target="{{ (int)abs($currentBalance) }}">{{ number_format(abs($currentBalance),0) }}</span>
                </div>
                <div class="pd-kpi-lbl">{{ $currentBalance > 0 ? 'Ledger — Owed to Taurus' : ($currentBalance < 0 ? 'Ledger — Credit' : 'Ledger Balance') }}</div>
                <div class="pd-kpi-sub">Excludes chargebacks (shared)</div>
            </div>
        </div>

        @if($chargebacks > 0)
        <div class="pd-cb-note pd-anim pd-d4">
            <i class="bx bx-info-circle"></i>
            <span>
                <strong>${{ number_format($chargebacks,2) }}</strong> in chargebacks (sales returns) this period.
            </span>
        </div>
        @endif
    </div>
</div>

{{-- Balance alert --}}
@if($currentBalance > 0)
<div class="pd-balance-alert owe pd-anim">
    <i class="bx bx-error-circle"></i>
    <span>Your ledger shows <span class="amt">${{ number_format($currentBalance,2) }}</span> owed to Taurus — this is from prior advances or settlements (not chargebacks).</span>
</div>
@elseif($currentBalance < 0)
<div class="pd-balance-alert credit pd-anim">
    <i class="bx bx-check-shield"></i>
    <span>You have a credit balance of <span class="amt">${{ number_format(abs($currentBalance),2) }}</span> with Taurus — applied to your next settlement.</span>
</div>
@endif

{{-- Carrier filter pills --}}
@include('partner.partials.carrier-filter')

{{-- ═══════════════════════════════════════════
     STATS STRIP
═══════════════════════════════════════════ --}}
<div class="pd-stats pd-anim pd-d2">
    <div class="pd-stat">
        <div class="pd-stat-icon si-violet"><i class="bx bx-trending-up"></i></div>
        <div>
            <div class="pd-stat-val cval" data-target="{{ $monthlyContracts }}">{{ $monthlyContracts }}</div>
            <div class="pd-stat-lbl">Sales this period</div>
            <div class="pd-stat-sub">{{ number_format($totalContracts) }} all-time</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-green"><i class="bx bx-check-shield"></i></div>
        <div>
            <div class="pd-stat-val cval" data-target="{{ $issuedContracts }}">{{ $issuedContracts }}</div>
            <div class="pd-stat-lbl">Issued</div>
            <div class="pd-stat-sub">{{ $notIssuedContracts }} not issued &middot; {{ $pendingContracts }} pending</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-amber"><i class="bx bx-trending-up"></i></div>
        <div>
            <div class="pd-stat-val">${{ number_format($partnerProjectedShare,0) }}</div>
            <div class="pd-stat-lbl">Partner Projected Share</div>
            <div class="pd-stat-sub">Your cut after {{ $taurusPct }}% Taurus fee</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-blue"><i class="bx bx-buildings"></i></div>
        <div>
            <div class="pd-stat-val">{{ $activeCarriers->count() }}</div>
            <div class="pd-stat-lbl">Active carriers</div>
            <div class="pd-stat-sub">{{ $activeCarriers->sum('state_count') }} states</div>
        </div>
    </div>
    <div class="pd-stat">
        <div class="pd-stat-icon si-red" style="background:rgba(239,68,68,.15);color:#ef4444;"><i class="bx bx-undo"></i></div>
        <div>
            <div class="pd-stat-val">${{ number_format($chargebacks,0) }}</div>
            <div class="pd-stat-lbl">Sales Returns</div>
            <div class="pd-stat-sub">Chargebacks this period</div>
        </div>
    </div>
</div>


{{-- Quick links to the split-out sections --}}
<div class="row g-3 pd-anim pd-d4" style="margin-top:1rem;">
    <div class="col-md-4">
        <a href="{{ route('partner.carriers', request()->only(['carrier_id'])) }}" class="pd-card pd-shortcut text-decoration-none d-flex align-items-center gap-3 p-3">
            <div class="pd-stat-icon si-violet" style="flex-shrink:0;"><i class="bx bx-briefcase"></i></div>
            <div>
                <div style="font-size:.88rem;font-weight:800;color:#111827;">Carriers &amp; States</div>
                <div style="font-size:.72rem;color:#9ca3af;margin-top:.1rem;">{{ $activeCarriers->count() }} {{ $activeCarriers->count() == 1 ? 'carrier' : 'carriers' }} linked</div>
            </div>
            <i class="bx bx-chevron-right ms-auto" style="color:#d1d5db;font-size:1.1rem;"></i>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('partner.sales', request()->only(['carrier_id','month','date_from','date_to'])) }}" class="pd-card pd-shortcut text-decoration-none d-flex align-items-center gap-3 p-3">
            <div class="pd-stat-icon si-green" style="flex-shrink:0;"><i class="bx bx-trending-up"></i></div>
            <div>
                <div style="font-size:.88rem;font-weight:800;color:#111827;">Sales</div>
                <div style="font-size:.72rem;color:#9ca3af;margin-top:.1rem;">{{ $issuedContracts }} issued &middot; {{ $pendingContracts }} pending this period</div>
            </div>
            <i class="bx bx-chevron-right ms-auto" style="color:#d1d5db;font-size:1.1rem;"></i>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('partner.ledger', request()->only(['carrier_id'])) }}" class="pd-card pd-shortcut text-decoration-none d-flex align-items-center gap-3 p-3">
            <div class="pd-stat-icon si-blue" style="flex-shrink:0;"><i class="bx bx-receipt"></i></div>
            <div>
                <div style="font-size:.88rem;font-weight:800;color:#111827;">Ledger</div>
                <div style="font-size:.72rem;color:#9ca3af;margin-top:.1rem;">
                    {{ $currentBalance > 0 ? 'Owed: $'.number_format($currentBalance,2) : ($currentBalance < 0 ? 'Credit: $'.number_format(abs($currentBalance),2) : 'Balance: $0') }}
                </div>
            </div>
            <i class="bx bx-chevron-right ms-auto" style="color:#d1d5db;font-size:1.1rem;"></i>
        </a>
    </div>
</div>

@endsection

@section('script')
<script>
function toggleFilterMode() {
    var mode = document.getElementById('pd-filter-mode').value;
    var mw = document.getElementById('pd-month-wrap');
    var rw = document.getElementById('pd-range-wrap');
    if (mode === 'range') {
        mw.style.display = 'none';
        rw.style.display = 'flex';
        document.querySelector('[name="month"]').value = '';
    } else {
        mw.style.display = 'flex';
        rw.style.display = 'none';
        document.querySelector('[name="date_from"]').value = '';
        document.querySelector('[name="date_to"]').value = '';
    }
}

(function(){
    'use strict';
    document.querySelectorAll('.cval').forEach(function(el){
        var target = +el.getAttribute('data-target') || 0;
        if(!target){ el.textContent = '0'; return; }
        var s=0, steps=Math.ceil(900/16), inc=target/steps;
        var t=setInterval(function(){
            s+=inc;
            if(s>=target){ el.textContent=target.toLocaleString(); clearInterval(t); }
            else{ el.textContent=Math.floor(s).toLocaleString(); }
        },16);
    });
    setTimeout(function(){
        document.querySelectorAll('.pd-bar-fill').forEach(function(b){
            var w=b.style.width; b.style.width='0';
            requestAnimationFrame(function(){
                b.style.transition='width 1s cubic-bezier(.22,1,.36,1)';
                b.style.width=w;
            });
        });
    },250);
})();
</script>
@endsection
