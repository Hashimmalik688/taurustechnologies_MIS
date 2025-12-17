<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18"><?php echo e($title); ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php if(isset($li_1)): ?>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('root')); ?>"><?php echo e($li_1); ?></a></li>
                    <?php endif; ?>
                    <?php if(isset($li_2)): ?>
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?php echo e($li_2); ?></a></li>
                    <?php endif; ?>
                    <?php if(isset($title)): ?>
                        <li class="breadcrumb-item active"><?php echo e($title); ?></li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\code\taurus-crm-master\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>