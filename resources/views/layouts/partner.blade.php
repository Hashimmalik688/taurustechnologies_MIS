<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Taurus Partner Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">

    {{-- Same CSS stack as main CRM so themes apply --}}
    <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/css/icons.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ URL::asset('css/light-theme.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/modern-white-theme.css') }}">

    <link rel="stylesheet" href="{{ URL::asset('css/themes.css') }}?v={{ filemtime(public_path('css/themes.css')) }}">
    @vite(['resources/css/custom-layout.css'])
    <link rel="stylesheet" href="{{ URL::asset('css/admin-ui.css') }}">

    {{-- Apply saved theme immediately --}}
    <script>
        (function(){
            var t = localStorage.getItem('theme') || 'light';
            if (t === 'dark') { t = 'midnight-black'; localStorage.setItem('theme', t); }
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <style>
        body{background:var(--bs-body-bg, var(--bs-surface-bg-light, #f0f2f5));color:var(--bs-body-color, inherit);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;}

        /* ══════════════════════════════════════════════════
           PARTNER PORTAL — PREMIUM TOPBAR v2
        ══════════════════════════════════════════════════ */

        /* ─── Main navbar shell ─── */
        .pp-nav{
            background:linear-gradient(135deg,#1a1035 0%,#2d1b69 40%,#4527a0 100%);
            padding:0;
            box-shadow:0 2px 0 rgba(255,255,255,.06), 0 8px 32px rgba(0,0,0,.45);
            position:sticky;top:0;z-index:1000;
        }

        /* ─── Top tier: brand + right controls ─── */
        .pp-nav-top{
            display:flex;justify-content:space-between;align-items:center;
            padding:.6rem 1.75rem;
            border-bottom:1px solid rgba(255,255,255,.07);
            background:rgba(0,0,0,.2);
        }

        /* ─── Brand ─── */
        .pp-brand{display:flex;align-items:center;gap:.75rem;text-decoration:none;color:#fff;}
        .pp-brand-icon{
            width:38px;height:38px;
            background:linear-gradient(135deg,rgba(255,255,255,.25),rgba(255,255,255,.1));
            border:1px solid rgba(255,255,255,.2);
            border-radius:.5rem;
            display:flex;align-items:center;justify-content:center;
            font-size:1.15rem;color:#fff;
            box-shadow:0 2px 8px rgba(0,0,0,.3);
        }
        .pp-brand-name{
            font-size:1.05rem;font-weight:900;letter-spacing:1.5px;
            text-transform:uppercase;color:#fff;
            text-shadow:0 1px 6px rgba(0,0,0,.4);
        }
        .pp-brand-divider{
            width:1px;height:22px;background:rgba(255,255,255,.2);margin:0 .3rem;
        }
        .pp-brand-sub{
            font-size:.72rem;font-weight:600;letter-spacing:.3px;
            color:rgba(255,255,255,.55);
            text-transform:uppercase;
        }

        /* ─── Right cluster ─── */
        .pp-nav-right{display:flex;align-items:center;gap:.55rem;}

        /* date chip */
        .pp-date-chip{
            font-size:.68rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;
            color:rgba(255,255,255,.45);
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.1);
            border-radius:999px;padding:.22rem .65rem;
            display:flex;align-items:center;gap:.3rem;
        }
        .pp-date-chip i{font-size:.8rem;}

        /* live pill */
        .pp-live-pill{
            font-size:.65rem;font-weight:800;letter-spacing:.8px;text-transform:uppercase;
            color:#6ee7b7;
            background:rgba(5,150,105,.2);
            border:1px solid rgba(5,150,105,.35);
            border-radius:999px;padding:.22rem .6rem;
            display:inline-flex;align-items:center;gap:.3rem;
        }
        .pp-live-dot{
            width:5px;height:5px;border-radius:50%;background:#6ee7b7;
            animation:pp-pulse-nav 1.8s ease-in-out infinite;
        }
        @keyframes pp-pulse-nav{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.35;transform:scale(.65);}}

        /* theme toggle */
        .pp-theme-btn{
            background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);
            color:#fff;width:34px;height:34px;border-radius:.4rem;
            display:flex;align-items:center;justify-content:center;
            cursor:pointer;transition:all .18s;font-size:1rem;
        }
        .pp-theme-btn:hover{background:rgba(255,255,255,.2);transform:scale(1.06);}

        /* user capsule */
        .pp-user-capsule{
            display:flex;align-items:center;gap:.5rem;
            background:rgba(255,255,255,.1);
            border:1px solid rgba(255,255,255,.15);
            border-radius:.5rem;
            padding:.3rem .7rem .3rem .3rem;
        }
        .pp-user-avatar{
            width:30px;height:30px;border-radius:.35rem;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            display:flex;align-items:center;justify-content:center;
            font-size:.78rem;font-weight:800;color:#fff;
            letter-spacing:.5px;
            box-shadow:0 2px 6px rgba(99,102,241,.4);
        }
        .pp-user-name{font-size:.82rem;font-weight:700;color:#fff;line-height:1.1;white-space:nowrap;}
        .pp-user-code{font-size:.63rem;font-weight:600;color:rgba(255,255,255,.45);letter-spacing:.3px;text-transform:uppercase;}

        /* logout btn */
        .pp-logout{
            background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);
            color:rgba(255,180,180,.9);padding:.33rem .65rem;
            border-radius:.4rem;font-size:.8rem;font-weight:700;
            cursor:pointer;transition:all .15s;
            display:inline-flex;align-items:center;gap:.25rem;
        }
        .pp-logout:hover{background:rgba(239,68,68,.3);color:#fff;border-color:rgba(239,68,68,.5);}

        /* ─── Bottom tier: navigation tabs ─── */
        .pp-nav-bottom{
            display:flex;align-items:center;
            padding:0 1.75rem;
            gap:.15rem;
            height:38px;
        }
        .pp-nav-tab{
            color:rgba(255,255,255,.55);text-decoration:none;
            font-size:.78rem;font-weight:700;letter-spacing:.3px;
            padding:0 .9rem;height:38px;
            display:inline-flex;align-items:center;gap:.35rem;
            border-bottom:2px solid transparent;
            transition:all .15s;
            text-transform:uppercase;
        }
        .pp-nav-tab:hover{color:rgba(255,255,255,.9);border-bottom-color:rgba(255,255,255,.3);}
        .pp-nav-tab.active{
            color:#fff;
            border-bottom-color:#a5b4fc;
            background:rgba(255,255,255,.05);
        }
        .pp-nav-tab i{font-size:.95rem;}

        /* nav right-side extras (injected from child views) */
        .pp-nav-spacer{flex:1;}
        .pp-nav-badge{
            font-size:.65rem;font-weight:800;padding:.15rem .5rem;
            border-radius:999px;display:inline-block;
            text-transform:uppercase;letter-spacing:.4px;
        }
        .pp-nav-badge-warn{background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.35);color:#fca5a5;}
        .pp-nav-badge-ok{background:rgba(5,150,105,.2);border:1px solid rgba(5,150,105,.35);color:#6ee7b7;}

        /* ─── Carrier filter pills ─── */
        .pp-carrier-filter{
            display:flex;align-items:center;flex-wrap:wrap;gap:.4rem;
            padding:.55rem 0 .65rem;
            border-bottom:1px solid rgba(0,0,0,.06);
            margin-bottom:1.1rem;
        }
        .pp-cf-label{
            font-size:.64rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;
            color:#9ca3af;margin-right:.2rem;display:flex;align-items:center;gap:.25rem;
        }
        .pp-cf-pill{
            font-size:.73rem;font-weight:700;padding:.25rem .7rem;border-radius:999px;
            background:#f3f4f6;border:1px solid rgba(0,0,0,.08);color:#6b7280;
            text-decoration:none;transition:all .15s;white-space:nowrap;
        }
        .pp-cf-pill:hover{background:#e5e7eb;color:#374151;border-color:rgba(0,0,0,.15);}
        .pp-cf-active{
            background:rgba(79,70,229,.1);border-color:rgba(79,70,229,.3);
            color:#4f46e5 !important;font-weight:800;
        }
        :is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-carrier-filter{border-color:var(--border-color,rgba(255,255,255,.06));}
        :is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-cf-pill{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.5);}
        :is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-cf-pill:hover{background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);}
        :is([data-theme="midnight-black"],[data-theme="emerald-glass"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-cf-active{background:rgba(99,102,241,.18);border-color:rgba(99,102,241,.4);color:#a5b4fc !important;}

        /* ─── Content ─── */
        .pp-content{padding:1.5rem 2rem;max-width:1500px;margin:0 auto;}

        /* ─── Footer ─── */
        .pp-footer{
            text-align:center;padding:1rem;font-size:.75rem;
            color:var(--bs-surface-500,#9ca3af);
            border-top:1px solid var(--bs-surface-200,#e5e7eb);
            margin-top:2rem;
        }

        @media(max-width:768px){
            .pp-nav-top{flex-wrap:wrap;gap:.4rem;padding:.6rem 1rem;}
            .pp-nav-bottom{padding:0 1rem;}
            .pp-user-capsule{display:none;}
            .pp-date-chip{display:none;}
            .pp-content{padding:.8rem;}
        }
    </style>
    @yield('css')
</head>
<body>
    <nav class="pp-nav">

        {{-- Top tier: Brand + User controls --}}
        <div class="pp-nav-top">
            <a href="{{ route('partner.dashboard') }}" class="pp-brand">
                <div class="pp-brand-icon"><i class="bx bx-building-house"></i></div>
                <span class="pp-brand-name">Taurus</span>
                <div class="pp-brand-divider"></div>
                <span class="pp-brand-sub">Partner Portal</span>
            </a>

            <div class="pp-nav-right">
                {{-- Live status --}}
                <span class="pp-live-pill"><span class="pp-live-dot"></span> Live</span>

                {{-- Current date --}}
                <span class="pp-date-chip"><i class="bx bx-calendar"></i> {{ now()->format('M j, Y') }}</span>

                {{-- Optional badges pushed from child views (e.g. balance alert) --}}
                @stack('nav-badges')

                {{-- Theme cycle --}}
                <button class="pp-theme-btn" onclick="toggleTheme()" title="Cycle theme">
                    <i class="bx bx-moon" id="themeIcon"></i>
                </button>

                {{-- User capsule --}}
                @php $AuthPartner = Auth::guard('partner')->user(); @endphp
                <div class="pp-user-capsule">
                    <div class="pp-user-avatar">{{ strtoupper(substr($AuthPartner->name ?? 'P', 0, 2)) }}</div>
                    <div>
                        <div class="pp-user-name">{{ $AuthPartner->name }}</div>
                        <div class="pp-user-code">{{ $AuthPartner->code }}</div>
                    </div>
                </div>

                {{-- Logout --}}
                <form action="{{ route('partner.logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="pp-logout" title="Logout"><i class="bx bx-log-out"></i> Logout</button>
                </form>
            </div>
        </div>

        {{-- Bottom tier: navigation tabs --}}
        <div class="pp-nav-bottom">
            <a href="{{ route('partner.dashboard') }}" class="pp-nav-tab {{ request()->routeIs('partner.dashboard') ? 'active' : '' }}">
                <i class="bx bx-grid-alt"></i> Dashboard
            </a>
            @php($ppType = $AuthPartner->type ?? 'partner')
            {{-- CC Partners (outsource firms) + their closers: submit & track sales --}}
            @if(in_array($ppType, ['cc_partner', 'closer']))
            <a href="{{ route('partner.sales.create') }}" class="pp-nav-tab {{ request()->routeIs('partner.sales.create') ? 'active' : '' }}">
                <i class="bx bx-plus-circle"></i> Submit Sale
            </a>
            <a href="{{ route('partner.submissions') }}" class="pp-nav-tab {{ request()->routeIs('partner.submissions') ? 'active' : '' }}">
                <i class="bx bx-list-check"></i> Submissions
            </a>
            @endif
            @if($ppType === 'cc_partner')
            <a href="{{ route('partner.closers.index') }}" class="pp-nav-tab {{ request()->routeIs('partner.closers.*') ? 'active' : '' }}">
                <i class="bx bx-group"></i> Closers
            </a>
            @endif
            {{-- Affiliate partners: revenue sales & ledger (unchanged) --}}
            @if($ppType === 'partner')
            <a href="{{ route('partner.sales') }}" class="pp-nav-tab {{ request()->routeIs('partner.sales') ? 'active' : '' }}">
                <i class="bx bx-trending-up"></i> Sales
            </a>
            <a href="{{ route('partner.ledger') }}" class="pp-nav-tab {{ request()->routeIs('partner.ledger') ? 'active' : '' }}">
                <i class="bx bx-receipt"></i> Ledger
            </a>
            @endif
            @unless(in_array($ppType, ['cc_partner', 'closer']))
            <a href="{{ route('partner.carriers') }}" class="pp-nav-tab {{ request()->routeIs('partner.carriers') ? 'active' : '' }}">
                <i class="bx bx-briefcase"></i> Carriers &amp; States
            </a>
            @endunless
            <div class="pp-nav-spacer"></div>
            {{-- Right-side nav extras from child views --}}
            @stack('nav-right')
        </div>

    </nav>

    <div class="pp-content">
        @yield('content')
    </div>

    <div class="pp-footer">&copy; {{ date('Y') }} Taurus CRM. All rights reserved.</div>

    {{-- Theme-aware overrides for partner portal --}}
    <style>
        /* Dark theme overrides for partner portal */
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-nav {
            box-shadow: 0 4px 24px rgba(0,0,0,.5);
            border-bottom: 2px solid rgba(255,255,255,.08);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pp-footer {
            border-color: var(--border-color, rgba(255,255,255,.08));
            color: var(--text-muted, #9ca3af);
        }
        /* Fix white surfaces on dark themes */
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-kpi {
            background: var(--bg-card, var(--bs-card-bg, #1a1a2e)) !important;
            border-color: var(--border-color, rgba(255,255,255,.08)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card {
            background: var(--bg-card, var(--bs-card-bg, #1a1a2e)) !important;
            border-color: var(--border-color, rgba(255,255,255,.08)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card-hdr {
            border-bottom-color: var(--border-color, rgba(255,255,255,.08)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier {
            background: var(--bg-tertiary, rgba(255,255,255,.04)) !important;
            border-color: var(--border-color, rgba(255,255,255,.08)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table thead th {
            color: var(--text-muted, #888) !important;
            border-bottom-color: var(--border-color, rgba(255,255,255,.08)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody td {
            color: var(--text-primary, #e0e0e0) !important;
            border-bottom-color: var(--border-color, rgba(255,255,255,.05)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-table tbody tr:hover {
            background: rgba(255,255,255,.04) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-kpi .k-lbl {
            color: var(--text-muted, #888) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-name {
            color: var(--text-primary, #e0e0e0) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-meta {
            color: var(--text-muted, #888) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-carrier-meta strong {
            color: var(--text-secondary, #aaa) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-state-pill {
            background: rgba(var(--accent-rgb, 102,126,234),.1) !important;
            color: var(--accent, var(--gold, #667eea)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-hdr h5 {
            color: var(--text-primary, #e0e0e0) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-hdr .pd-sub {
            color: var(--text-muted, #888) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card-hdr h6 {
            color: var(--text-primary, #e0e0e0) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-card-hdr .badge-count {
            background: rgba(var(--accent-rgb, 102,126,234),.12) !important;
            color: var(--accent, var(--gold, #667eea)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-filter-input {
            background: var(--bg-tertiary, rgba(255,255,255,.06)) !important;
            border-color: var(--border-color, rgba(255,255,255,.1)) !important;
            color: var(--text-primary, #e0e0e0) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-filter-label {
            color: var(--text-muted, #888) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-mark-btn {
            background: var(--bg-tertiary, rgba(255,255,255,.04)) !important;
            border-color: var(--border-color, rgba(255,255,255,.1)) !important;
            color: var(--text-secondary, #aaa) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-select-all {
            color: var(--accent, var(--gold, #667eea)) !important;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pd-empty {
            color: var(--text-muted, #666) !important;
        }
    </style>

    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // All available themes in cycle order
        var _allThemes = ['light', 'emerald-glass', 'midnight-black', 'ocean-blue', 'royal-purple', 'rose-gold', 'copper-steel'];

        function toggleTheme() {
            var html = document.documentElement;
            var current = html.getAttribute('data-theme') || 'light';
            var idx = _allThemes.indexOf(current);
            var next = _allThemes[(idx + 1) % _allThemes.length];
            var themeIcon = document.getElementById('themeIcon');

            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);

            if (themeIcon) {
                if (next === 'light') {
                    themeIcon.classList.remove('bx-sun');
                    themeIcon.classList.add('bx-moon');
                } else {
                    themeIcon.classList.remove('bx-moon');
                    themeIcon.classList.add('bx-sun');
                }
            }
        }

        // Set correct icon on load
        (function() {
            var saved = localStorage.getItem('theme') || 'light';
            var themeIcon = document.getElementById('themeIcon');
            if (saved !== 'light' && themeIcon) {
                themeIcon.classList.remove('bx-moon');
                themeIcon.classList.add('bx-sun');
            }
        })();
    </script>
    @yield('script')
</body>
</html>
