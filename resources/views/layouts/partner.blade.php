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
    <link rel="stylesheet" href="{{ URL::asset('css/dark-theme.css') }}?v={{ time() }}" id="dark-theme-style">
    <link rel="stylesheet" href="{{ URL::asset('css/themes.css') }}?v={{ time() }}">
    @vite(['resources/css/custom-layout.css'])
    <link rel="stylesheet" href="{{ URL::asset('css/admin-ui.css') }}">

    {{-- Apply saved theme immediately --}}
    <script>
        (function(){
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <style>
        body{background:var(--bs-surface-bg-light, #f0f2f5);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;}

        /* ─── Navbar ─── */
        .pp-nav{
            background:linear-gradient(135deg,var(--bs-gradient-start,#667eea) 0%,var(--bs-gradient-end,#764ba2) 100%);
            padding:0;box-shadow:0 2px 12px rgba(0,0,0,.12);position:sticky;top:0;z-index:1000;
        }
        .pp-nav-inner{display:flex;justify-content:space-between;align-items:center;padding:.6rem 1.2rem;}
        .pp-brand{display:flex;align-items:center;gap:.5rem;text-decoration:none;color:#fff;}
        .pp-brand-icon{width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-size:1rem;}
        .pp-brand-text{font-weight:800;font-size:.85rem;letter-spacing:.5px;}
        .pp-brand-sub{font-size:.5rem;font-weight:500;opacity:.7;display:block;margin-top:-2px;}

        .pp-nav-right{display:flex;align-items:center;gap:.6rem;}

        .pp-nav-link{
            color:rgba(255,255,255,.85);text-decoration:none;font-size:.68rem;font-weight:600;
            padding:.35rem .65rem;border-radius:.35rem;transition:all .15s;
            display:inline-flex;align-items:center;gap:.25rem;
        }
        .pp-nav-link:hover,.pp-nav-link.active{background:rgba(255,255,255,.15);color:#fff;}

        .pp-user-badge{
            background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.15);
            border-radius:.4rem;padding:.3rem .55rem;display:flex;align-items:center;gap:.35rem;
        }
        .pp-user-avatar{
            width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,.2);
            display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#fff;
        }
        .pp-user-name{font-size:.65rem;font-weight:600;color:#fff;line-height:1.1;}
        .pp-user-code{font-size:.5rem;color:rgba(255,255,255,.6);}

        .pp-logout{
            background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);
            color:#fff;padding:.3rem .55rem;border-radius:.35rem;font-size:.62rem;font-weight:600;
            cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;gap:.2rem;
        }
        .pp-logout:hover{background:rgba(255,255,255,.2);}

        /* ─── Content ─── */
        .pp-content{padding:1.2rem 1.5rem;max-width:1400px;margin:0 auto;}

        /* ─── Footer ─── */
        .pp-footer{text-align:center;padding:1rem;font-size:.55rem;color:var(--bs-surface-500,#9ca3af);border-top:1px solid var(--bs-surface-200,#e5e7eb);margin-top:2rem;}

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

    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @yield('script')
</body>
</html>
