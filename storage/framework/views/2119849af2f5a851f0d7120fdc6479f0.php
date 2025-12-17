<!-- Bootstrap Css -->
<link href="<?php echo e(URL::asset('build/css/bootstrap.min.css')); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />

<!-- Icons Css -->
<link href="<?php echo e(URL::asset('build/css/icons.min.css')); ?>" rel="stylesheet" type="text/css" />

<!-- App Css -->
<link href="<?php echo e(URL::asset('build/css/app.min.css')); ?>" id="app-style" rel="stylesheet" type="text/css" />

<!-- Page specific CSS - must load AFTER Bootstrap -->
<?php echo $__env->yieldContent('css'); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/layouts/head-css.blade.php ENDPATH**/ ?>