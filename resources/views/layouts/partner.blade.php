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

    <link rel="stylesheet" href="{{ URL::asset('css/themes.css') }}?v={{ time() }}">
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

        /* ─── Navbar ─── */
        .pp-nav{
            background:linear-gradient(135deg,var(--bs-gradient-start,#667eea) 0%,var(--bs-gradient-end,#764ba2) 100%);
            padding:0;box-shadow:0 4px 20px rgba(0,0,0,.35);position:sticky;top:0;z-index:1000;
            border-bottom:2px solid rgba(255,255,255,.18);
        }
        .pp-nav-inner{display:flex;justify-content:space-between;align-items:center;padding:.75rem 1.5rem;background:rgba(0,0,0,.15);}
        .pp-brand{display:flex;align-items:center;gap:.6rem;text-decoration:none;color:#fff;}
        .pp-brand-icon{width:40px;height:40px;background:rgba(255,255,255,.25);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;}
        .pp-brand-text{font-weight:800;font-size:1.15rem;letter-spacing:.5px;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.3);}
        .pp-brand-sub{font-size:.72rem;font-weight:600;opacity:.9;display:block;margin-top:-2px;color:rgba(255,255,255,.9);}

        .pp-nav-right{display:flex;align-items:center;gap:.75rem;}

        .pp-nav-link{
            color:#fff;text-decoration:none;font-size:.88rem;font-weight:600;
            padding:.45rem .85rem;border-radius:.4rem;transition:all .15s;
            display:inline-flex;align-items:center;gap:.3rem;text-shadow:0 1px 2px rgba(0,0,0,.2);
        }
        .pp-nav-link:hover,.pp-nav-link.active{background:rgba(255,255,255,.22);color:#fff;}

        .pp-user-badge{
            background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.22);
            border-radius:.45rem;padding:.4rem .7rem;display:flex;align-items:center;gap:.45rem;
        }
        .pp-user-avatar{
            width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.3);
            display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:700;color:#fff;
        }
        .pp-user-name{font-size:.85rem;font-weight:600;color:#fff;line-height:1.2;}
        .pp-user-code{font-size:.68rem;color:rgba(255,255,255,.6);}

        .pp-logout{
            background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);
            color:#fff;padding:.4rem .75rem;border-radius:.4rem;font-size:.82rem;font-weight:600;
            cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;gap:.25rem;
        }
        .pp-logout:hover{background:rgba(255,255,255,.3);}

        /* ─── Theme Toggle ─── */
        .pp-theme-btn{
            background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);
            color:#fff;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;
            justify-content:center;cursor:pointer;transition:all .2s;font-size:1.1rem;
        }
        .pp-theme-btn:hover{background:rgba(255,255,255,.3);transform:scale(1.08);}

        /* ─── Content ─── */
        .pp-content{padding:1.5rem 2rem;max-width:1500px;margin:0 auto;}

        /* ─── Footer ─── */
        .pp-footer{text-align:center;padding:1.2rem;font-size:.78rem;color:var(--bs-surface-500,#9ca3af);border-top:1px solid var(--bs-surface-200,#e5e7eb);margin-top:2rem;}

        /* Mobile */
        @media(max-width:768px){
            .pp-nav-inner{flex-wrap:wrap;gap:.4rem;}
            .pp-user-badge{display:none;}
            .pp-content{padding:.8rem;}
        }
    </style>
    @yield('css')
</head>
<body>
    <nav class="pp-nav">
        <div class="pp-nav-inner">
            <a href="{{ route('partner.dashboard') }}" class="pp-brand">
                <div class="pp-brand-icon"><i class="bx bx-building-house"></i></div>
                <div>
                    <span class="pp-brand-text">TAURUS</span>
                    <span class="pp-brand-sub">Partner Portal</span>
                </div>
            </a>

            <div class="pp-nav-right">
                <a href="{{ route('partner.dashboard') }}" class="pp-nav-link active"><i class="bx bx-grid-alt"></i> Dashboard</a>

                {{-- Theme Toggle --}}
                <button class="pp-theme-btn" onclick="toggleTheme()" title="Switch Theme">
                    <i class="bx bx-moon" id="themeIcon"></i>
                </button>

                <div class="pp-user-badge">
                    <div class="pp-user-avatar">{{ strtoupper(substr(Auth::guard('partner')->user()->name, 0, 1)) }}</div>
                    <div>
                        <div class="pp-user-name">{{ Auth::guard('partner')->user()->name }}</div>
                        <div class="pp-user-code">{{ Auth::guard('partner')->user()->code }}</div>
                    </div>
                </div>

                <form action="{{ route('partner.logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="pp-logout"><i class="bx bx-log-out"></i> Logout</button>
                </form>
            </div>
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
