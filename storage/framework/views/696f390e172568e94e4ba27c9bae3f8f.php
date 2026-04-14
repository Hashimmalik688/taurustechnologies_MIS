
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeCarriers->count() > 1): ?>
<div class="pp-carrier-filter">
    <?php
        $allUrl  = request()->fullUrlWithQuery(['carrier_id' => null]);
        $isAll   = !$carrierId;
    ?>
    <span class="pp-cf-label"><i class="bx bx-filter-alt"></i> Carrier</span>
    <a href="<?php echo e($allUrl); ?>"
       class="pp-cf-pill <?php echo e($isAll ? 'pp-cf-active' : ''); ?>">
        All
    </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeCarriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $url = request()->fullUrlWithQuery(['carrier_id' => $c['id']]); ?>
    <a href="<?php echo e($url); ?>"
       class="pp-cf-pill <?php echo e($carrierId == $c['id'] ? 'pp-cf-active' : ''); ?>">
        <?php echo e($c['name']); ?>

    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/taurus-crm/resources/views/partner/partials/carrier-filter.blade.php ENDPATH**/ ?>