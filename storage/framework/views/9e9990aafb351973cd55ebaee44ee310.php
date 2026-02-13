<?php $__env->startComponent('mail::message'); ?>
# Role Change Notification

Hello <?php echo e($user->name); ?>,

Your role in <?php echo e($appName); ?> has been changed.

**Previous Role:** <?php echo e($oldRole); ?>  
**New Role:** <?php echo e($newRole); ?>


This change may affect your permissions and what you can access in the system. Please refresh your browser or log in again to see the updated interface.

If you have any questions or if this change was unexpected, please contact your administrator.

Thanks,  
<?php echo e($appName); ?> Team
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/taurus-crm/resources/views/emails/role-changed.blade.php ENDPATH**/ ?>