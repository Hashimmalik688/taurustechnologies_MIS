<?php $__env->startSection('title'); ?> Bad Leads <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(URL::asset('build/libs/toastr/build/toastr.min.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Ravens <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Bad Leads <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Bad Leads - Disposed Contacts</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Disposition</th>
                                    <th>Disposed By</th>
                                    <th>Date</th>
                                    <th>Notes</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $badLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $badLead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($badLeads->firstItem() + $index); ?></td>
                                        <td><?php echo e($badLead->lead_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($badLead->lead_phone ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php if($badLead->disposition === 'no_answer'): ?> bg-warning
                                                <?php elseif($badLead->disposition === 'wrong_number'): ?> bg-danger
                                                <?php else: ?> bg-secondary
                                                <?php endif; ?>">
                                                <?php echo e(\App\Models\BadLead::getDispositionLabel($badLead->disposition)); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($badLead->disposedBy->name ?? 'Unknown'); ?></td>
                                        <td><?php echo e($badLead->created_at->format('M d, Y H:i')); ?></td>
                                        <td><?php echo e($badLead->notes ?? '-'); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success" onclick="sendBackLead(<?php echo e($badLead->lead_id); ?>, this)" title="Send back to calling system">
                                                <i class="bx bx-undo"></i> Send Back
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No bad leads found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <?php echo e($badLeads->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/toastr/build/toastr.min.js')); ?>"></script>
    <script>
        function sendBackLead(leadId, button) {
            if (!confirm('Are you sure you want to send this lead back to the calling system?')) {
                return;
            }

            // Disable button and show loading
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Sending...';

            fetch('<?php echo e(route('ravens.leads.restore')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    lead_id: leadId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Lead restored successfully');
                    // Remove the row from table after successful restoration
                    button.closest('tr').remove();
                    
                    // Check if table is now empty
                    const tbody = document.querySelector('tbody');
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `<tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bx bx-info-circle fs-3"></i>
                                <p class="mb-0">No bad leads found</p>
                            </td>
                        </tr>`;
                    }
                } else {
                    toastr.error(data.message || 'Failed to restore lead');
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while restoring the lead');
                button.disabled = false;
                button.innerHTML = originalHtml;
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/bad-leads.blade.php ENDPATH**/ ?>