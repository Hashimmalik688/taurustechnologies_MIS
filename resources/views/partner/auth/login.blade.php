<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Partner Login | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Taurus CRM Partner Portal" name="description" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    {{-- Same CSS stack as main CRM --}}
    <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/css/icons.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ URL::asset('css/light-theme.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/modern-white-theme.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/dark-theme.css') }}?v={{ time() }}" id="dark-theme-style">
    <link rel="stylesheet" href="{{ URL::asset('css/themes.css') }}?v={{ time() }}">
    @vite(['resources/css/custom-layout.css'])
    <link rel="stylesheet" href="{{ URL::asset('css/admin-ui.css') }}">

    <script>
        (function(){
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{
            min-height:100vh;display:flex;align-items:center;justify-content:center;
            background:linear-gradient(135deg,var(--bs-gradient-start,#667eea) 0%,var(--bs-gradient-end,#764ba2) 100%);
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
            position:relative;overflow:hidden;
        }

        /* Floating shapes */
        .shape{position:absolute;border-radius:50%;opacity:.07;background:#fff;}
        .shape-1{width:300px;height:300px;top:-80px;left:-80px;animation:float 20s ease-in-out infinite;}
        .shape-2{width:200px;height:200px;bottom:-60px;right:-60px;animation:float 16s ease-in-out infinite reverse;}
        .shape-3{width:120px;height:120px;top:40%;left:10%;animation:float 12s ease-in-out infinite 2s;}
        .shape-4{width:80px;height:80px;bottom:20%;right:15%;animation:float 14s ease-in-out infinite 4s;}
        @keyframes float{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-30px) rotate(5deg)}}

        /* Employee link */
        .emp-link{
            position:fixed;top:16px;right:16px;z-index:100;
            background:rgba(255,255,255,.92);color:var(--bs-gradient-start,#667eea);
            padding:8px 18px;border-radius:30px;font-size:.72rem;font-weight:600;
            text-decoration:none;display:inline-flex;align-items:center;gap:6px;
            box-shadow:0 4px 20px rgba(0,0,0,.12);backdrop-filter:blur(10px);
            transition:all .3s;border:1px solid rgba(255,255,255,.3);
        }
        .emp-link:hover{transform:translateY(-2px);box-shadow:0 6px 28px rgba(0,0,0,.18);color:var(--bs-gradient-start,#667eea);}

        /* Card */
        .login-card{
            background:rgba(255,255,255,.97);backdrop-filter:blur(20px);
            border-radius:18px;width:400px;max-width:92vw;position:relative;z-index:10;
            box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;
        }

        /* Header */
        .lc-header{
            background:linear-gradient(135deg,var(--bs-gradient-start,#667eea) 0%,var(--bs-gradient-end,#764ba2) 100%);
            padding:28px 30px 24px;text-align:center;color:#fff;position:relative;
        }
        .lc-header::after{
            content:'';position:absolute;width:160px;height:160px;
            background:rgba(255,255,255,.06);border-radius:50%;
            top:-80px;right:-40px;
        }
        .lc-logo{
            width:60px;height:60px;background:rgba(255,255,255,.15);
            border-radius:50%;display:inline-flex;align-items:center;justify-content:center;
            margin-bottom:12px;border:2px solid rgba(255,255,255,.2);
        }
        .lc-logo i{font-size:28px;color:#fff;}
        .lc-header h2{font-size:1.3rem;font-weight:700;margin-bottom:2px;}
        .lc-header p{font-size:.72rem;opacity:.85;margin:0;}

        /* Body */
        .lc-body{padding:28px 30px 24px;}
        .lc-alert{
            padding:10px 14px;border-radius:8px;margin-bottom:16px;
            display:flex;align-items:center;gap:8px;
            background:#fff5f5;color:#e53e3e;border:1px solid #fed7d7;
            font-size:.72rem;font-weight:500;
        }
        .lc-group{margin-bottom:16px;}
        .lc-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500,#6b7280);margin-bottom:6px;display:block;}
        .lc-input-wrap{position:relative;}
        .lc-input-wrap i.icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:1rem;color:var(--bs-surface-muted,#9ca3af);z-index:1;}
        .lc-input{
            width:100%;border:1.5px solid var(--bs-surface-200,#e5e7eb);border-radius:10px;
            padding:11px 14px 11px 38px;font-size:.78rem;
            background:var(--bs-surface-50,#f9fafb);transition:all .2s;
        }
        .lc-input:focus{outline:none;border-color:var(--bs-gradient-start,#667eea);background:var(--bs-card-bg,#fff);box-shadow:0 0 0 3px rgba(102,126,234,.1);}
        .lc-toggle{
            position:absolute;right:12px;top:50%;transform:translateY(-50%);
            cursor:pointer;color:var(--bs-surface-muted,#9ca3af);font-size:1rem;transition:color .2s;background:none;border:none;padding:0;
        }
        .lc-toggle:hover{color:var(--bs-gradient-start,#667eea);}

        .lc-submit{
            width:100%;background:linear-gradient(135deg,var(--bs-gradient-start,#667eea) 0%,var(--bs-gradient-end,#764ba2) 100%);
            color:#fff;border:none;border-radius:10px;padding:12px;
            font-size:.78rem;font-weight:600;cursor:pointer;
            transition:all .3s;box-shadow:0 4px 14px rgba(102,126,234,.35);
            display:flex;align-items:center;justify-content:center;gap:6px;
        }
        .lc-submit:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(102,126,234,.45);}
        .lc-submit:active{transform:translateY(0);}

        .lc-footer{text-align:center;margin-top:20px;font-size:.6rem;color:var(--bs-surface-muted,#9ca3af);}

        /* Mobile */
        @media(max-width:480px){
            .login-card{border-radius:14px;}
            .lc-header{padding:22px 20px 18px;}
            .lc-body{padding:22px 20px 18px;}
            .emp-link{top:10px;right:10px;padding:6px 12px;font-size:.65rem;}
        }
    </style>
</head>
<body>
    <!-- Floating shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>

    <!-- Employee Login -->
    <a href="{{ route('login') }}" class="emp-link"><i class="bx bx-briefcase"></i> Employee Login</a>

    <!-- Login Card -->
    <div class="login-card">
        <div class="lc-header">
            <div class="lc-logo"><i class="bx bx-handshake"></i></div>
            <h2>Partner Portal</h2>
            <p>Sign in to access your dashboard</p>
        </div>

        <div class="lc-body">
            @if ($errors->any())
            <div class="lc-alert">
                <i class="bx bx-error-circle" style="font-size:1rem;flex-shrink:0"></i>
                <div>@foreach ($errors->all() as $error) {{ $error }} @endforeach</div>
            </div>
            @endif

            <form method="POST" action="{{ route('partner.login.submit') }}">
                @csrf

                <div class="lc-group">
                    <label for="email" class="lc-label">Email Address</label>
                    <div class="lc-input-wrap">
                        <i class="bx bx-envelope icon"></i>
                        <input id="email" type="email" class="lc-input" name="email" value="{{ old('email') }}" required autofocus placeholder="partner@example.com">
                    </div>
                </div>

                <div class="lc-group">
                    <label for="password" class="lc-label">Password</label>
                    <div class="lc-input-wrap">
                        <i class="bx bx-lock-alt icon"></i>
                        <input id="password" type="password" class="lc-input" name="password" required placeholder="Enter your password">
                        <button type="button" class="lc-toggle" onclick="togglePwd()"><i class="bx bx-show" id="pwdIcon"></i></button>
                    </div>
                </div>

                <button type="submit" class="lc-submit"><i class="bx bx-log-in"></i> Login to Dashboard</button>
            </form>

            <div class="lc-footer">&copy; {{ date('Y') }} Taurus CRM. All rights reserved.</div>
        </div>
    </div>

    <script>
    function togglePwd(){
        const p=document.getElementById('password'),i=document.getElementById('pwdIcon');
        if(p.type==='password'){p.type='text';i.className='bx bx-hide';}
        else{p.type='password';i.className='bx bx-show';}
    }
    </script>
</body>
</html>
