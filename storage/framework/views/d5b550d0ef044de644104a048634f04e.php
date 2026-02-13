<?php $__env->startComponent('mail::message'); ?>
# Welcome to <?php echo e($appName); ?>


Hello <?php echo e($user->name); ?>,

Your account has been successfully created in <?php echo e($appName); ?>. Here are your login details:

**Email:** <?php echo e($user->email); ?>  
**Initial Password:** <?php echo e($password); ?>


Please log in to the system using the link below:

<?php $__env->startComponent('mail::button', ['url' => $loginUrl]); ?>
Login to <?php echo e($appName); ?>

<?php echo $__env->renderComponent(); ?>

**Important Security Notes:**
- Change your password immediately after your first login
- Do not share your password with anyone
- Always use HTTPS when accessing the system
- Log out when finished using the system

If you have any issues, please contact your administrator.

Thanks,  
<?php echo e($appName); ?> Team
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/taurus-crm/resources/views/emails/account-created.blade.php ENDPATH**/ ?>