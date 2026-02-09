<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .partner-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .partner-navbar .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }
        
        .partner-navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .partner-navbar .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .partner-navbar .btn-logout {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .partner-navbar .btn-logout:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .main-content {
            padding: 30px 0;
        }
        
        .partner-info {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            display: inline-block;
        }
        
        .partner-info strong {
            color: #764ba2;
        }
    </style>
    
    @yield('css')
</head>

<body>
    <!-- Partner Navigation -->
    <nav class="partner-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('partner.dashboard') }}" class="navbar-brand">
                    <i class="fas fa-building me-2"></i>
                    TAURUS PARTNER PORTAL
                </a>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="partner-info">
                        <i class="fas fa-user-circle me-2"></i>
                        <strong>{{ Auth::guard('partner')->user()->name }}</strong>
                        <span class="text-muted ms-2">({{ Auth::guard('partner')->user()->code }})</span>
                    </div>
                    
                    <form action="{{ route('partner.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-logout">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid px-4">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('script')
</body>
</html>
