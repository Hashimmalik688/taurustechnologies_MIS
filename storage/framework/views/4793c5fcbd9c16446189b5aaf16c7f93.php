<?php $__env->startComponent('mail::message'); ?>
# Password Reset Notification

Hello <?php echo e($user->name); ?>,

Your password has been reset by an administrator in <?php echo e($appName); ?>.

**Your new password is:**  
<?php echo e($password); ?>


Please log in using this new password and change it immediately for your security.

<?php $__env->startComponent('mail::button', ['url' => $loginUrl]); ?>
Login to <?php echo e($appName); ?>

<?php echo $__env->renderComponent(); ?>

**Important Security Notes:**
- Change your password immediately after logging in
- Do not share your password with anyone
- If you did not request this password reset, please contact your administrator immediately

Thanks,  
<?php echo e($appName); ?> Team
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/taurus-crm/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>