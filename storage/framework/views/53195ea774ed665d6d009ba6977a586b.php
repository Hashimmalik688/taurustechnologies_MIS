<div class="wbs-item" style="margin-left: <?php echo e($level * 20); ?>px;">
    <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <h6 class="text-white mb-1">
                <span class="badge bg-primary me-2"><?php echo e($item->code); ?></span>
                <?php echo e($item->name); ?>

            </h6>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->description): ?>
                <p class="text-muted-dark mb-2 small"><?php echo e($item->description); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="d-flex gap-3 flex-wrap">
                <small class="text-muted-dark">
                    <i class="bx bx-layer me-1"></i><?php echo e(ucfirst(str_replace('_', ' ', $item->level))); ?>

                </small>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->estimated_hours): ?>
                    <small class="text-muted-dark">
                        <i class="bx bx-time me-1"></i><?php echo e($item->estimated_hours); ?>h
                    </small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->estimated_cost): ?>
                    <small class="text-muted-dark">
                        <i class="bx bx-dollar me-1"></i><?php echo e(number_format($item->estimated_cost, 2)); ?>

                    </small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->children->count() > 0): ?>
    <div class="wbs-children">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('admin.epms.partials.wbs-item', ['item' => $child, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/epms/partials/wbs-item.blade.php ENDPATH**/ ?>