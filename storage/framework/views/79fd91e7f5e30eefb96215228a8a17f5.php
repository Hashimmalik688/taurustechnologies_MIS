<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?> | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(URL::asset('images/favicon.ico')); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: var(--bs-surface-bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .partner-navbar {
            background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .partner-navbar .navbar-brand {
            color: var(--bs-white) !important;
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
            color: var(--bs-white) !important;
            transform: translateY(-2px);
        }
        
        .partner-navbar .btn-logout {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: var(--bs-white);
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
            background: var(--bs-card-bg);
            padding: 15px 20px;
            border-radius: 10px;
            display: inline-block;
        }
        
        .partner-info strong {
            color: var(--bs-gradient-end);
        }
    </style>
    
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
    <!-- Partner Navigation -->
    <nav class="partner-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?php echo e(route('partner.dashboard')); ?>" class="navbar-brand">
                    <i class="fas fa-building me-2"></i>
                    TAURUS PARTNER PORTAL
                </a>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="partner-info">
                        <i class="fas fa-user-circle me-2"></i>
                        <strong><?php echo e(Auth::guard('partner')->user()->name); ?></strong>
                        <span class="text-muted ms-2">(<?php echo e(Auth::guard('partner')->user()->code); ?>)</span>
                    </div>
                    
                    <form action="<?php echo e(route('partner.logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
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
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php echo $__env->yieldContent('script'); ?>
</body>
</html>
<?php /**PATH /var/www/taurus-crm/resources/views/layouts/partner.blade.php ENDPATH**/ ?>