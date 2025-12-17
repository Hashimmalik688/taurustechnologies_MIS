<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?> | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Meta Tags -->
    <meta content="Taurus Technologies CRM System" name="description" />
    <meta content="Taurus Technologies" name="author" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(URL::asset('images/favicon.ico')); ?>">
    
    <!-- Include head CSS -->
    <?php echo $__env->make('layouts.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Page specific CSS -->
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body class="<?php echo $__env->yieldContent('body-class'); ?>">
    
    <!-- Main Content -->
    <?php echo $__env->yieldContent('content'); ?>
    
    <!-- Vendor Scripts -->
    <?php echo $__env->make('layouts.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Page specific scripts -->
    <?php echo $__env->yieldContent('script'); ?>
    
</body>
</html><?php /**PATH C:\code\taurus-crm-master\resources\views/layouts/master-without-nav.blade.php ENDPATH**/ ?>