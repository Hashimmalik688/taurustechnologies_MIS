@extends('layouts.partner')

@section('title') Partner Dashboard @endsection

@section('css')
<link href="{{ URL::asset('css/light-theme.css') }}" rel="stylesheet" />
<style>
/* ═══════════════════════════════════════════════════════════════
   PARTNER COMMAND CENTER  ·  Premium Financial Aesthetic v2
   ═══════════════════════════════════════════════════════════════ */
:root {
    --pp-indigo:   #4f46e5;
    --pp-emerald:  #059669;
    --pp-red:      #dc2626;
    --pp-amber:    #d97706;
    --pp-teal:     #0d9488;
    --pp-sky:      #0284c7;
    --pp-hero-bg:  linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);
    --pp-card-br:  .65rem;
    --pp-card-sh:  0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.06);
    --pp-anim:     cubic-bezier(.22,1,.36,1);
}
.pp-page{padding:0;}
/* ── HERO ── */
.pp-hero{background:var(--pp-hero-bg);padding:2rem 2.25rem 1.75rem;position:relative;overflow:hidden;margin-bottom:1.75rem;}
.pp-hero::before{content:'';position:absolute;inset:0;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");background-size:180px;pointer-events:none;opacity:.4;}
.pp-hero::after{content:'';position:absolute;width:400px;height:400px;background:radial-gradient(circle,rgba(99,102,241,.18) 0%,transparent 70%);top:-100px;right:-80px;pointer-events:none;}
.pp-hero-inner{position:relative;z-index:1;}
.pp-hero-top{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem;}
.pp-hero-greeting{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.45);margin-bottom:.2rem;}
.pp-hero-name{font-size:1.45rem;font-weight:800;color:#fff;letter-spacing:-.3px;line-height:1.2;}
.pp-hero-meta{margin-top:.3rem;display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;}
.pp-hero-badge{font-size:.7rem;font-weight:700;padding:.2rem .6rem;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.75);letter-spacing:.5px;text-transform:uppercase;}
.pp-hero-badge.live{background:rgba(5,150,105,.25);border-color:rgba(5,150,105,.5);color:#6ee7b7;display:inline-flex;align-items:center;gap:.3rem;}
.pp-pulse{width:6px;height:6px;border-radius:50%;background:#6ee7b7;display:inline-block;animation:pulse 1.8s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.7);}}
.pp-hero-filter{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;}
.pp-hero-filter-label{font-size:.68rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);}
.pp-hero-input{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:.4rem;padding:.4rem .7rem;font-size:.84rem;color:#fff;width:148px;transition:border-color .2s;}
.pp-hero-input:focus{outline:none;border-color:rgba(255,255,255,.4);background:rgba(255,255,255,.12);}
.pp-hero-btn{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;padding:.4rem .85rem;border-radius:.4rem;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;gap:.3rem;}
.pp-hero-btn:hover{background:rgba(255,255,255,.2);}
/* hero kpis */
.pp-hero-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;}
@media(max-width:992px){.pp-hero-kpis{grid-template-columns:repeat(2,1fr);}}
@media(max-width:576px){.pp-hero-kpis{grid-template-columns:1fr 1fr;}}
.pp-hkpi{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:var(--pp-card-br);padding:1.1rem 1.25rem;position:relative;overflow:hidden;transition:transform .2s var(--pp-anim),background .2s;}
.pp-hkpi:hover{background:rgba(255,255,255,.1);transform:translateY(-2px);}
.pp-hkpi::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;}
.pp-hkpi.h-indigo::before{background:linear-gradient(90deg,#6366f1,#818cf8);}
.pp-hkpi.h-emerald::before{background:linear-gradient(90deg,#059669,#34d399);}
.pp-hkpi.h-red::before{background:linear-gradient(90deg,#dc2626,#f87171);}
.pp-hkpi.h-amber::before{background:linear-gradient(90deg,#d97706,#fbbf24);}
.pp-hkpi-icon{font-size:1.5rem;margin-bottom:.5rem;opacity:.5;}
.pp-hkpi.h-indigo .pp-hkpi-icon{color:#818cf8;}
.pp-hkpi.h-emerald .pp-hkpi-icon{color:#34d399;}
.pp-hkpi.h-red .pp-hkpi-icon{color:#f87171;}
.pp-hkpi.h-amber .pp-hkpi-icon{color:#fbbf24;}
.pp-hkpi-val{font-size:2rem;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1;margin-bottom:.3rem;}
.pp-hkpi-lbl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.4);}
.pp-hkpi-sub{font-size:.75rem;color:rgba(255,255,255,.35);margin-top:.25rem;}
.pp-ratio-bar{margin-top:.65rem;height:3px;background:rgba(255,255,255,.1);border-radius:999px;overflow:hidden;}
.pp-ratio-fill{height:100%;border-radius:999px;transition:width .8s var(--pp-anim);}
.h-emerald .pp-ratio-fill{background:linear-gradient(90deg,#059669,#34d399);}
.h-indigo .pp-ratio-fill{background:linear-gradient(90deg,#6366f1,#818cf8);}
/* SPLIT BANNER */
.pp-split-banner{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.75rem;}
@media(max-width:576px){.pp-split-banner{grid-template-columns:1fr;}}
.pp-split-card{border-radius:var(--pp-card-br);padding:1.1rem 1.4rem;display:flex;align-items:center;gap:1rem;border:1px solid transparent;box-shadow:var(--pp-card-sh);}
.pp-split-card.earned{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-color:#bbf7d0;}
.pp-split-card.pending{background:linear-gradient(135deg,#fffbeb,#fef9c3);border-color:#fde68a;}
.pp-split-icon{width:46px;height:46px;border-radius:.55rem;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;}
.pp-split-card.earned .pp-split-icon{background:#bbf7d0;color:#065f46;}
.pp-split-card.pending .pp-split-icon{background:#fde68a;color:#78350f;}
.pp-split-lbl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;}
.pp-split-val{font-size:1.55rem;font-weight:900;letter-spacing:-.5px;}
.pp-split-card.earned .pp-split-val{color:#065f46;}
.pp-split-card.pending .pp-split-val{color:#78350f;}
.pp-split-note{font-size:.78rem;color:#9ca3af;margin-top:.1rem;}
/* ALERT */
.pp-alert{border-radius:var(--pp-card-br);padding:.85rem 1.25rem;display:flex;align-items:center;gap:.65rem;font-size:.88rem;font-weight:600;margin-bottom:1.75rem;border:1px solid;box-shadow:var(--pp-card-sh);}
.pp-alert.owe{background:#fef2f2;color:#7f1d1d;border-color:#fecaca;}
.pp-alert.credit{background:#f0fdf4;color:#14532d;border-color:#bbf7d0;}
.pp-alert i{font-size:1.1rem;flex-shrink:0;}
.pp-alert-amount{font-size:1rem;font-weight:900;}
/* MINI KPIs */
.pp-mini-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem;}
@media(max-width:992px){.pp-mini-kpis{grid-template-columns:repeat(2,1fr);}}
.pp-mini{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pp-card-br);padding:1rem 1.15rem;box-shadow:var(--pp-card-sh);display:flex;align-items:center;gap:.9rem;transition:transform .2s var(--pp-anim),box-shadow .2s;}
.pp-mini:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.1);}
.pp-mini-icon{width:42px;height:42px;border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.pp-mini-icon.i-violet{background:rgba(109,40,217,.1);color:#6d28d9;}
.pp-mini-icon.i-sky{background:rgba(2,132,199,.1);color:#0284c7;}
.pp-mini-icon.i-emerald{background:rgba(5,150,105,.1);color:#059669;}
.pp-mini-icon.i-gold{background:rgba(217,119,6,.1);color:#d97706;}
.pp-mini-val{font-size:1.45rem;font-weight:900;letter-spacing:-.4px;line-height:1;}
.pp-mini-lbl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-top:.15rem;}
.pp-mini-sub{font-size:.75rem;color:#d1d5db;margin-top:.1rem;}
/* CARD */
.pp-card{background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:var(--pp-card-br);box-shadow:var(--pp-card-sh);overflow:hidden;margin-bottom:1.5rem;}
.pp-card-head{padding:.9rem 1.2rem;border-bottom:1px solid rgba(0,0,0,.06);display:flex;justify-content:space-between;align-items:center;background:#fafafa;}
.pp-card-head h6{font-size:.92rem;font-weight:800;margin:0;display:flex;align-items:center;gap:.4rem;color:#111827;}
.pp-card-head h6 i{font-size:1.05rem;color:var(--pp-indigo);}
.pp-card-body{padding:1.1rem 1.2rem;}
.pp-card-body.np{padding:0;}
.pp-chip{font-size:.68rem;font-weight:700;padding:.18rem .5rem;border-radius:.25rem;display:inline-block;}
.pp-chip-indigo{background:rgba(79,70,229,.1);color:#4338ca;}
.pp-chip-count{background:rgba(79,70,229,.08);color:var(--pp-indigo);font-size:.72rem;padding:.15rem .5rem;border-radius:.25rem;font-weight:700;}
/* CARRIERS */
.pp-carriers-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;}
.pp-carrier-card{background:#fafafa;border:1px solid rgba(0,0,0,.07);border-radius:.5rem;padding:.9rem 1rem;transition:border-color .2s,box-shadow .2s;}
.pp-carrier-card:hover{border-color:rgba(79,70,229,.3);box-shadow:0 0 0 3px rgba(79,70,229,.05);}
.pp-carrier-tag{display:inline-flex;align-items:center;gap:.35rem;background:rgba(79,70,229,.1);color:var(--pp-indigo);font-size:.78rem;font-weight:700;padding:.3rem .65rem;border-radius:.35rem;margin-bottom:.6rem;}
.pp-carrier-name-lg{font-size:.95rem;font-weight:800;color:#111827;margin-bottom:.5rem;}
.pp-state-pill{display:inline-block;font-size:.65rem;font-weight:700;padding:.1rem .38rem;border-radius:.2rem;background:rgba(79,70,229,.08);color:var(--pp-indigo);margin:.08rem .06rem 0 0;letter-spacing:.2px;}
.pp-carrier-count{font-size:.75rem;color:#9ca3af;margin-top:.45rem;}
.pp-all-states-row{padding:.7rem 1.2rem;background:#fafafa;border-top:1px solid rgba(0,0,0,.06);display:flex;flex-wrap:wrap;gap:.3rem;align-items:center;}
/* TABLES */
.pp-table{width:100%;border-collapse:collapse;font-size:.84rem;}
.pp-table thead th{font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;border-bottom:1px solid rgba(0,0,0,.08);padding:.65rem .85rem;background:#f9fafb;white-space:nowrap;}
.pp-table tbody td{padding:.65rem .85rem;border-bottom:1px solid rgba(0,0,0,.04);vertical-align:middle;color:#374151;}
.pp-table tfoot td{padding:.65rem .85rem;font-weight:700;border-top:2px solid rgba(0,0,0,.08);background:#fafafa;}
.pp-table tbody tr:hover{background:rgba(79,70,229,.025);}
.pp-table tbody tr:last-child td{border-bottom:none;}
/* chips */
.txn-chip{font-size:.65rem;font-weight:800;padding:.18rem .5rem;border-radius:.25rem;display:inline-block;letter-spacing:.3px;text-transform:uppercase;}
.txn-sale{background:#d1fae5;color:#065f46;}
.txn-payment{background:#dbeafe;color:#1e3a8a;}
.txn-chargeback{background:#fee2e2;color:#7f1d1d;}
.txn-return{background:#fef3c7;color:#78350f;}
.txn-other{background:#f3f4f6;color:#374151;}
.sc-success{background:#d1fae5;color:#065f46;}
.sc-warning{background:#fef9c3;color:#713f12;}
.sc-info{background:#dbeafe;color:#1e3a8a;}
.sc-danger{background:#fee2e2;color:#7f1d1d;}
.sc-secondary{background:#f3f4f6;color:#374151;}
.status-chip{font-size:.68rem;font-weight:700;padding:.18rem .5rem;border-radius:.25rem;display:inline-block;text-transform:capitalize;}
.col-debit{color:#4f46e5;font-weight:700;}
.col-credit{color:#059669;font-weight:700;}
.col-dim{color:#d1d5db;}
.run-bal-pos{color:#dc2626;font-weight:700;font-size:.8rem;}
.run-bal-neg{color:#059669;font-weight:700;font-size:.8rem;}
.run-bal-zero{color:#9ca3af;font-size:.8rem;}
/* YTD QUAD */
.pp-ytd-quad{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid rgba(0,0,0,.06);}
.pp-ytd-cell{padding:1rem .8rem;text-align:center;border-right:1px solid rgba(0,0,0,.06);border-bottom:1px solid rgba(0,0,0,.06);}
.pp-ytd-cell:nth-child(2n){border-right:none;}
.pp-ytd-cell:nth-last-child(-n+2){border-bottom:none;}
.pp-ytd-val{font-size:1.25rem;font-weight:900;letter-spacing:-.3px;}
.pp-ytd-lbl{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-top:.2rem;}
.pp-ytd-footer{padding:.6rem .9rem;background:#fafafa;text-align:center;font-size:.78rem;color:#9ca3af;}
/* REV BAR */
.rev-bar{height:4px;border-radius:999px;background:#e5e7eb;margin-top:.35rem;overflow:hidden;}
.rev-bar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--pp-teal),#34d399);max-width:100%;}
/* EMPTY */
.pp-empty{text-align:center;padding:2.5rem 1rem;color:#d1d5db;}
.pp-empty i{font-size:2.2rem;display:block;margin-bottom:.5rem;opacity:.25;}
.pp-empty p{font-size:.88rem;color:#9ca3af;margin:0;}
/* ANIMATIONS */
@keyframes pp-fadein{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);}}
.pp-anim{animation:pp-fadein .5s var(--pp-anim) both;}
.pp-anim-d1{animation-delay:.05s;}
.pp-anim-d2{animation-delay:.1s;}
.pp-anim-d3{animation-delay:.15s;}
.pp-anim-d4{animation-delay:.2s;}
.pp-anim-d5{animation-delay:.25s;}
/* DARK THEME */
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-card{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-card-head{background:var(--bg-secondary,#16162a);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-card-head h6{color:var(--text-primary,#e0e0e0);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-table thead th{background:var(--bg-secondary,#16162a);color:var(--text-muted,#888);border-color:var(--border-color,rgba(255,255,255,.06));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-table tbody td{color:var(--text-primary,#ddd);border-color:var(--border-color,rgba(255,255,255,.04));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-mini{background:var(--bg-card,#1e1e2e);border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-carrier-card{background:var(--bg-tertiary,rgba(255,255,255,.04));border-color:var(--border-color,rgba(255,255,255,.08));}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-alert.owe{background:rgba(220,38,38,.12);border-color:rgba(220,38,38,.25);color:#fca5a5;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-alert.credit{background:rgba(5,150,105,.12);border-color:rgba(5,150,105,.25);color:#6ee7b7;}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-split-card.earned{background:rgba(5,150,105,.12);border-color:rgba(5,150,105,.25);}
:is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-split-card.pending{background:rgba(217,119,6,.1);border-color:rgba(217,119,6,.25);}
</style>
@endsection

@section('content')
<div class="pp-page">

{{-- ══════════════════════════════════════════════
     HERO COMMAND PANEL
════════════════════════════════════════════════ --}}
<div class="pp-hero pp-anim">
    <div class="pp-hero-inner">
        <div class="pp-hero-top">
            <div class="pp-hero-identity">
                <div class="pp-hero-greeting">Partner Portal</div>
                <div class="pp-hero-name">{{ $partner->name }}</div>
                <div class="pp-hero-meta">
                    <span class="pp-hero-badge">Code: {{ $partner->code }}</span>
                    <span class="pp-hero-badge">{{ number_format($totalLeads) }} Leads</span>
                    <span class="pp-hero-badge live"><span class="pp-pulse"></span> Live Data</span>
                </div>
            </div>
            <form method="GET" action="{{ route('partner.dashboard') }}" class="pp-hero-filter">
                <span class="pp-hero-filter-label">Period</span>
                <input type="month" name="month" class="pp-hero-input" value="{{ $month }}">
                <button type="submit" class="pp-hero-btn"><i class="bx bx-filter-alt"></i> Apply</button>
            </form>
        </div>

        @php $earnedRatio = $projectedRevenue > 0 ? min(100, ($earnedRevenue / $projectedRevenue) * 100) : 0; @endphp
        <div class="pp-hero-kpis">
            <div class="pp-hkpi h-indigo pp-anim pp-anim-d1">
                <i class="bx bx-trending-up pp-hkpi-icon"></i>
                <div class="pp-hkpi-val">$<span class="counter-value" data-target="{{ (int)$projectedRevenue }}">{{ number_format($projectedRevenue,0) }}</span></div>
                <div class="pp-hkpi-lbl">Projected Revenue</div>
                <div class="pp-hkpi-sub">Issued · not yet paid</div>
                <div class="pp-ratio-bar"><div class="pp-ratio-fill" style="width:{{ $earnedRatio }}%;"></div></div>
            </div>
            <div class="pp-hkpi h-emerald pp-anim pp-anim-d2">
                <i class="bx bx-check-circle pp-hkpi-icon"></i>
                <div class="pp-hkpi-val">$<span class="counter-value" data-target="{{ (int)$earnedRevenue }}">{{ number_format($earnedRevenue,0) }}</span></div>
                <div class="pp-hkpi-lbl">Earned Revenue</div>
                <div class="pp-hkpi-sub">{{ number_format($earnedRatio,0) }}% of projected received</div>
                <div class="pp-ratio-bar"><div class="pp-ratio-fill" style="width:{{ $earnedRatio }}%;"></div></div>
            </div>
            <div class="pp-hkpi h-red pp-anim pp-anim-d3">
                <i class="bx bx-error pp-hkpi-icon"></i>
                <div class="pp-hkpi-val">$<span class="counter-value" data-target="{{ (int)$chargebacks }}">{{ number_format($chargebacks,0) }}</span></div>
                <div class="pp-hkpi-lbl">Chargebacks</div>
                <div class="pp-hkpi-sub">Returns &amp; reversals</div>
            </div>
            <div class="pp-hkpi h-amber pp-anim pp-anim-d4">
                <i class="bx bx-wallet pp-hkpi-icon"></i>
                <div class="pp-hkpi-val" style="color:{{ $currentBalance > 0 ? '#fbbf24' : ($currentBalance < 0 ? '#34d399' : '#fff') }};">
                    {{ $currentBalance > 0 ? '+' : ($currentBalance < 0 ? '−' : '') }}$<span class="counter-value" data-target="{{ (int)abs($currentBalance) }}">{{ number_format(abs($currentBalance),0) }}</span>
                </div>
                <div class="pp-hkpi-lbl">{{ $currentBalance > 0 ? 'Amount Due' : ($currentBalance < 0 ? 'Credit Balance' : 'Ledger Balance') }}</div>
                <div class="pp-hkpi-sub">From AR ledger</div>
            </div>
        </div>
    </div>
</div>

{{-- Balance alert --}}
@if($currentBalance > 0)
<div class="pp-alert owe pp-anim">
    <i class="bx bx-error-circle"></i>
    <div>You currently owe <span class="pp-alert-amount">${{ number_format($currentBalance,2) }}</span> to Taurus. Please settle to unlock full commission disbursement.</div>
</div>
@elseif($currentBalance < 0)
<div class="pp-alert credit pp-anim">
    <i class="bx bx-check-shield"></i>
    <div>You have a credit of <span class="pp-alert-amount">${{ number_format(abs($currentBalance),2) }}</span> — will be applied to your next settlement.</div>
</div>
@endif

{{-- ══════════════════════════════════════════════
     COMMISSION SPLIT BANNER
════════════════════════════════════════════════ --}}
<div class="pp-split-banner pp-anim pp-anim-d1">
    <div class="pp-split-card earned">
        <div class="pp-split-icon"><i class="bx bx-badge-check"></i></div>
        <div>
            <div class="pp-split-lbl">Your Earned Commission</div>
            <div class="pp-split-val">${{ number_format($commissionPaid,2) }}</div>
            <div class="pp-split-note">Confirmed paid · {{ $partner->our_commission_percentage ?? 15 }}% Taurus fee applied</div>
        </div>
    </div>
    <div class="pp-split-card pending">
        <div class="pp-split-icon"><i class="bx bx-time-five"></i></div>
        <div>
            <div class="pp-split-lbl">Pending Commission</div>
            <div class="pp-split-val">${{ number_format($commissionUnpaid,2) }}</div>
            <div class="pp-split-note">Awaiting disbursement · Your net share: ${{ number_format($partnerEarnedShare,2) }}</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     SECONDARY KPI ROW
════════════════════════════════════════════════ --}}
<div class="pp-mini-kpis pp-anim pp-anim-d2">
    <div class="pp-mini">
        <div class="pp-mini-icon i-violet"><i class="bx bx-file"></i></div>
        <div>
            <div class="pp-mini-val counter-value" data-target="{{ $monthlyLeads }}">{{ $monthlyLeads }}</div>
            <div class="pp-mini-lbl">Leads This Period</div>
            <div class="pp-mini-sub">All-time: {{ number_format($totalLeads) }}</div>
        </div>
    </div>
    <div class="pp-mini">
        <div class="pp-mini-icon i-emerald"><i class="bx bx-check-shield"></i></div>
        <div>
            <div class="pp-mini-val counter-value" data-target="{{ $totalSales }}">{{ $totalSales }}</div>
            <div class="pp-mini-lbl">Sales This Period</div>
            <div class="pp-mini-sub">{{ $pendingLeads }} pending</div>
        </div>
    </div>
    <div class="pp-mini">
        <div class="pp-mini-icon i-sky"><i class="bx bx-line-chart"></i></div>
        <div>
            <div class="pp-mini-val">${{ number_format($partnerProjectedShare,0) }}</div>
            <div class="pp-mini-lbl">Projected Your Share</div>
            <div class="pp-mini-sub">After {{ $partner->our_commission_percentage ?? 15 }}% deduction</div>
        </div>
    </div>
    <div class="pp-mini">
        <div class="pp-mini-icon i-gold"><i class="bx bx-buildings"></i></div>
        <div>
            <div class="pp-mini-val counter-value" data-target="{{ $activeCarriers->count() }}">{{ $activeCarriers->count() }}</div>
            <div class="pp-mini-lbl">Active Carriers</div>
            <div class="pp-mini-sub">{{ $authorizedStates->count() }} states authorized</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     CARRIERS + REVENUE BREAKDOWN
════════════════════════════════════════════════ --}}
<div class="row g-3 pp-anim pp-anim-d3 mb-0">
    <div class="col-lg-5">
        <div class="pp-card h-100">
            <div class="pp-card-head">
                <h6><i class="bx bx-briefcase"></i> Active Carriers</h6>
                <span class="pp-chip-count">{{ $activeCarriers->count() }}</span>
            </div>
            <div class="pp-card-body">
                @if($activeCarriers->count() > 0)
                <div class="pp-carriers-grid">
                    @foreach($activeCarriers as $c)
                    <div class="pp-carrier-card">
                        <div class="pp-carrier-tag"><i class="bx bx-buildings" style="font-size:.85rem;"></i> Carrier</div>
                        <div class="pp-carrier-name-lg">{{ $c['name'] }}</div>
                        <div>@foreach($c['states'] as $st)<span class="pp-state-pill">{{ $st }}</span>@endforeach</div>
                        <div class="pp-carrier-count">{{ $c['state_count'] }} state{{ $c['state_count'] != 1 ? 's' : '' }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="pp-empty"><i class="bx bx-inbox"></i><p>No carriers linked yet</p></div>
                @endif
            </div>
            @if($authorizedStates->count() > 0)
            <div class="pp-all-states-row">
                <span style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;white-space:nowrap;margin-right:.4rem;">All States</span>
                @foreach($authorizedStates as $st)<span class="pp-state-pill">{{ $st }}</span>@endforeach
            </div>
            @endif
        </div>
    </div>
    <div class="col-lg-7">
        <div class="pp-card h-100">
            <div class="pp-card-head">
                <h6><i class="bx bx-bar-chart-alt-2"></i> Revenue Breakdown by Carrier</h6>
            </div>
            <div class="pp-card-body np">
                @if($revenueByCarrier->count() > 0)
                @php $maxShare = $revenueByCarrier->max('partner_share') ?: 1; @endphp
                <table class="pp-table">
                    <thead>
                        <tr>
                            <th>Carrier</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Your Share</th>
                            <th class="text-end">Taurus Cut</th>
                            <th class="text-end">Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByCarrier as $r)
                        <tr>
                            <td>
                                <div style="font-weight:700;">{{ $r['carrier']->name ?? 'Unknown' }}</div>
                                <div class="rev-bar"><div class="rev-bar-fill" style="width:{{ $maxShare > 0 ? min(100,($r['partner_share']/$maxShare)*100) : 0 }}%;"></div></div>
                            </td>
                            <td class="text-end">${{ number_format($r['total_revenue'],0) }}</td>
                            <td class="text-end col-credit">${{ number_format($r['partner_share'],0) }}</td>
                            <td class="text-end" style="color:#dc2626;">${{ number_format($r['our_share'],0) }}</td>
                            <td class="text-end"><span class="pp-chip pp-chip-indigo">{{ $r['sales_count'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-end"><strong>${{ number_format($revenueByCarrier->sum('total_revenue'),0) }}</strong></td>
                            <td class="text-end col-credit">${{ number_format($revenueByCarrier->sum('partner_share'),0) }}</td>
                            <td class="text-end" style="color:#dc2626;">${{ number_format($revenueByCarrier->sum('our_share'),0) }}</td>
                            <td class="text-end"><strong>{{ $revenueByCarrier->sum('sales_count') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <div class="pp-empty"><i class="bx bx-inbox"></i><p>No paid carrier data yet</p></div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     LEDGER + YTD + TOP STATES
════════════════════════════════════════════════ --}}
<div class="row g-3 pp-anim pp-anim-d4" style="margin-top:1.5rem;">
    <div class="col-lg-8">
        <div class="pp-card">
            <div class="pp-card-head">
                <h6><i class="bx bx-history"></i> Ledger Transactions</h6>
                <span style="font-size:.72rem;color:#9ca3af;font-weight:600;">Last 20 entries · running balance</span>
            </div>
            <div class="pp-card-body np" style="overflow-x:auto;">
                @if($recentTransactions->count() > 0)
                @php $runBal = 0; @endphp
                <table class="pp-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Carrier</th>
                            <th>Ref</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions->take(20) as $txn)
                        @php
                            $runBal += ($txn['debit'] ?? 0) - ($txn['credit'] ?? 0);
                            $typeKey = strtolower(str_replace([' ','_','-'],'', $txn['type'] ?? ''));
                            $chip = match(true) {
                                str_contains($typeKey,'sale') && !str_contains($typeKey,'return') => 'txn-sale',
                                str_contains($typeKey,'payment') => 'txn-payment',
                                str_contains($typeKey,'chargeback') => 'txn-chargeback',
                                str_contains($typeKey,'return') => 'txn-return',
                                default => 'txn-other',
                            };
                        @endphp
                        <tr>
                            <td style="white-space:nowrap;color:#6b7280;font-size:.8rem;">{{ \Carbon\Carbon::parse($txn['date'])->format('M d, Y') }}</td>
                            <td><span class="txn-chip {{ $chip }}">{{ str_replace('_',' ',$txn['type'] ?? 'general') }}</span></td>
                            <td style="font-size:.82rem;">{{ $txn['carrier'] ?? '—' }}</td>
                            <td style="color:#9ca3af;font-size:.78rem;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ \Illuminate\Support\Str::limit($txn['description'] ?? $txn['reference'] ?? '—', 28) }}</td>
                            <td class="text-end {{ ($txn['debit'] ?? 0) > 0 ? 'col-debit' : 'col-dim' }}">{{ ($txn['debit'] ?? 0) > 0 ? '$'.number_format($txn['debit'],2) : '—' }}</td>
                            <td class="text-end {{ ($txn['credit'] ?? 0) > 0 ? 'col-credit' : 'col-dim' }}">{{ ($txn['credit'] ?? 0) > 0 ? '$'.number_format($txn['credit'],2) : '—' }}</td>
                            <td class="text-end">
                                <span class="{{ $runBal > 0 ? 'run-bal-pos' : ($runBal < 0 ? 'run-bal-neg' : 'run-bal-zero') }}">
                                    {{ $runBal > 0 ? '+' : ($runBal < 0 ? '−' : '') }}${{ number_format(abs($runBal),2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="pp-empty"><i class="bx bx-receipt"></i><p>No transactions recorded yet</p></div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-3">
        <div class="pp-card">
            <div class="pp-card-head">
                <h6><i class="bx bx-calendar-alt"></i> Year to Date &mdash; {{ $ytdMetrics['year'] }}</h6>
            </div>
            <div class="pp-ytd-quad">
                <div class="pp-ytd-cell">
                    <div class="pp-ytd-val" style="color:var(--pp-indigo);">${{ number_format($ytdMetrics['projected_revenue'],0) }}</div>
                    <div class="pp-ytd-lbl">Projected</div>
                </div>
                <div class="pp-ytd-cell">
                    <div class="pp-ytd-val" style="color:var(--pp-emerald);">${{ number_format($ytdMetrics['earned_revenue'],0) }}</div>
                    <div class="pp-ytd-lbl">Earned</div>
                </div>
                <div class="pp-ytd-cell">
                    <div class="pp-ytd-val" style="color:var(--pp-red);">${{ number_format($ytdMetrics['chargebacks'],0) }}</div>
                    <div class="pp-ytd-lbl">Chargebacks</div>
                </div>
                <div class="pp-ytd-cell">
                    <div class="pp-ytd-val" style="color:var(--pp-teal);">${{ number_format($ytdMetrics['partner_earned_share'],0) }}</div>
                    <div class="pp-ytd-lbl">Your Share</div>
                </div>
            </div>
            <div class="pp-ytd-footer">Taurus retains <strong>{{ $ytdMetrics['taurus_share_pct'] }}%</strong> &mdash; <strong>${{ number_format($ytdMetrics['taurus_earned_share'],0) }}</strong> this year</div>
        </div>

        @if($revenueByState->count() > 0)
        <div class="pp-card flex-grow-1">
            <div class="pp-card-head">
                <h6><i class="bx bx-map-pin"></i> Top States</h6>
            </div>
            <div class="pp-card-body np">
                @php $maxState = $revenueByState->max('partner_share') ?: 1; @endphp
                <table class="pp-table">
                    <thead><tr><th>State</th><th class="text-end">Your Share</th><th class="text-end">Sales</th></tr></thead>
                    <tbody>
                        @foreach($revenueByState->take(6) as $s)
                        <tr>
                            <td>
                                <span class="pp-state-pill" style="font-size:.75rem;padding:.2rem .55rem;">{{ $s['state'] ?? 'N/A' }}</span>
                                <div class="rev-bar" style="margin-top:.3rem;"><div class="rev-bar-fill" style="width:{{ min(100,($s['partner_share']/$maxState)*100) }}%;"></div></div>
                            </td>
                            <td class="text-end col-credit">${{ number_format($s['partner_share'],0) }}</td>
                            <td class="text-end"><span class="pp-chip pp-chip-indigo">{{ $s['sales_count'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════
     RECENT LEADS TABLE
════════════════════════════════════════════════ --}}
<div class="pp-card pp-anim pp-anim-d5" style="margin-top:1.5rem;">
    <div class="pp-card-head">
        <h6><i class="bx bx-list-ul"></i> Recent Leads &amp; Sales</h6>
        <span class="pp-chip-count">{{ $recentLeads->count() }}</span>
    </div>
    <div class="pp-card-body np" style="overflow-x:auto;">
        @if($recentLeads->count() > 0)
        <table class="pp-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Carrier</th>
                    <th>State</th>
                    <th>Status</th>
                    <th class="text-end">Premium/mo</th>
                    <th class="text-end">Commission</th>
                    <th class="text-end">Your Share</th>
                    <th>Disbursed</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentLeads as $lead)
                @php
                    $sc = match(strtolower($lead->status ?? '')) {
                        'sale','approved','done','accepted' => 'sc-success',
                        'pending' => 'sc-warning',
                        'issued'  => 'sc-info',
                        'declined','cancelled' => 'sc-danger',
                        default => 'sc-secondary',
                    };
                    $fullComm = $lead->agent_commission ?? 0;
                    $myShare  = $fullComm - ($fullComm * ($partner->our_commission_percentage ?? 15) / 100);
                @endphp
                <tr>
                    <td style="color:#d1d5db;font-size:.76rem;">{{ $lead->id }}</td>
                    <td>
                        <div style="font-weight:700;">{{ $lead->first_name }} {{ $lead->last_name }}</div>
                        @if($lead->dob)<div style="font-size:.74rem;color:#9ca3af;">DOB: {{ $lead->dob }}</div>@endif
                    </td>
                    <td style="font-size:.82rem;">{{ $lead->insuranceCarrier->name ?? '—' }}</td>
                    <td><span class="pp-state-pill" style="font-size:.72rem;padding:.15rem .45rem;">{{ $lead->state ?? '—' }}</span></td>
                    <td><span class="status-chip {{ $sc }}">{{ ucfirst($lead->status ?? '—') }}</span></td>
                    <td class="text-end">${{ number_format($lead->monthly_premium ?? 0,2) }}</td>
                    <td class="text-end col-debit">${{ number_format($fullComm,2) }}</td>
                    <td class="text-end col-credit">${{ number_format($myShare,2) }}</td>
                    <td>
                        @if($lead->commission_paid_to_partner)
                            <span class="txn-chip txn-sale">Paid</span>
                        @else
                            <span class="txn-chip txn-other">Pending</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;color:#9ca3af;font-size:.78rem;">{{ $lead->created_at?->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="pp-empty"><i class="bx bx-inbox"></i><p>No leads found in this period</p></div>
        @endif
    </div>
</div>

</div>{{-- .pp-page --}}
@endsection

@section('script')
<script>
(function(){
    'use strict';
    /* Counter animation */
    document.querySelectorAll('.counter-value').forEach(function(el){
        var target = +el.getAttribute('data-target') || 0;
        if(target === 0){ el.textContent = '0'; return; }
        var start = 0, dur = 900, step = 16, steps = Math.ceil(dur/step), inc = target/steps;
        var timer = setInterval(function(){
            start += inc;
            if(start >= target){ el.textContent = target.toLocaleString(); clearInterval(timer); }
            else { el.textContent = Math.floor(start).toLocaleString(); }
        }, step);
    });
    /* Progress bars — animated after slight delay so transition is visible */
    setTimeout(function(){
        document.querySelectorAll('.rev-bar-fill, .pp-ratio-fill').forEach(function(bar){
            var w = bar.style.width;
            bar.style.width = '0';
            requestAnimationFrame(function(){
                bar.style.transition = 'width 1s cubic-bezier(.22,1,.36,1)';
                bar.style.width = w;
            });
        });
    }, 300);
})();
</script>
@endsection
