<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Notifications'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .notification-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            text-decoration: none;
        }

        .notification-item.unread {
            background-color: #f8f9fb;
            border-left-color: #556ee6;
        }

        .notification-item.unread .notification-title {
            font-weight: 600;
        }

        .notification-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .notification-item:hover .notification-actions {
            opacity: 1;
        }

        .date-group-header {
            background: linear-gradient(45deg, #556ee6, #74788d);
            background-size: 100% 2px;
            background-repeat: no-repeat;
            background-position: bottom;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .notification-stats {
            background: linear-gradient(135deg, #556ee6 0%, #74788d 100%);
            color: white;
            border-radius: 10px;
        }

        .filter-tabs .nav-link {
            border: none;
            padding: 8px 20px;
            margin: 0 5px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .filter-tabs .nav-link.active {
            background-color: #556ee6;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #74788d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Dashboard
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Notifications
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header with stats and actions -->
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">Notifications</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="notification-stats d-inline-block px-3 py-2 me-3">
                                <small>
                                    <i class="bx bx-bell me-1"></i>
                                    <?php echo e($unreadCount); ?> Unread
                                    <span class="mx-2">|</span>
                                    <?php echo e($totalCount); ?> Total
                                </small>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                                <button type="button" class="btn btn-soft-primary btn-sm" onclick="markAllAsRead()">
                                    <i class="bx bx-check-double me-1"></i> Mark all as read
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Filter tabs -->
                    <ul class="nav nav-pills filter-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($currentType == 'all' ? 'active' : ''); ?>"
                                href="<?php echo e(route('notifications.index', ['type' => 'all'])); ?>">
                                All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($currentType == 'unread' ? 'active' : ''); ?>"
                                href="<?php echo e(route('notifications.index', ['type' => 'unread'])); ?>">
                                Unread (<?php echo e($unreadCount); ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($currentType == 'read' ? 'active' : ''); ?>"
                                href="<?php echo e(route('notifications.index', ['type' => 'read'])); ?>">
                                Read
                            </a>
                        </li>
                    </ul>

                    <!-- Notifications list -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($groupedNotifications->count() > 0): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupedNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="notification-group mb-4">
                                <div class="date-group-header">
                                    <h6 class="text-muted mb-0"><?php echo e($group['label']); ?></h6>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $group['notifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="notification-item <?php echo e($notification->isUnread() ? 'unread' : 'read'); ?> p-3 mb-2 rounded position-relative"
                                        data-notification-id="<?php echo e($notification->id); ?>">
                                        <div class="d-flex">
                                            <!-- Avatar/Icon -->
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->icon): ?>
                                                    <span
                                                        class="avatar-title bg-<?php echo e($notification->color); ?> rounded-circle font-size-16">
                                                        <i class="bx <?php echo e($notification->icon); ?>"></i>
                                                    </span>
                                                <?php else: ?>
                                                    <span
                                                        class="avatar-title bg-<?php echo e($notification->color); ?> rounded-circle font-size-16">
                                                        <i class="bx bx-bell"></i>
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mt-0 mb-1 notification-title"><?php echo e($notification->title); ?>

                                                        </h6>
                                                        <div class="font-size-13 text-muted">
                                                            <p class="mb-1"><?php echo e($notification->message); ?></p>
                                                            <p class="mb-0">
                                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                                <?php echo e($notification->time_ago); ?>

                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="notification-actions ms-3">
                                                        <div class="dropdown">
                                                            <button class="btn btn-link text-muted font-size-16 p-1"
                                                                type="button" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->isUnread()): ?>
                                                                    <li>
                                                                        <a class="dropdown-item" href="#"
                                                                            onclick="markAsRead(<?php echo e($notification->id); ?>)">
                                                                            <i class="bx bx-check me-2"></i>
                                                                            Mark as read
                                                                        </a>
                                                                    </li>
                                                                <?php else: ?>
                                                                    <li>
                                                                        <a class="dropdown-item" href="#"
                                                                            onclick="markAsUnread(<?php echo e($notification->id); ?>)">
                                                                            <i class="bx bx-mail-send me-2"></i>
                                                                            Mark as unread
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#"
                                                                        onclick="deleteNotification(<?php echo e($notification->id); ?>)">
                                                                        <i class="bx bx-trash me-2"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->isUnread()): ?>
                                            <div class="position-absolute top-0 end-0 me-3 mt-3">
                                                <span class="badge bg-primary rounded-pill">•</span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-lg-12">
                                <?php echo e($notifications->appends(request()->query())->links()); ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Empty state -->
                        <div class="empty-state">
                            <i class="bx bx-bell-off"></i>
                            <h4>No notifications found</h4>
                            <p class="text-muted">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentType == 'unread'): ?>
                                    You have no unread notifications
                                <?php elseif($currentType == 'read'): ?>
                                    You have no read notifications
                                <?php else: ?>
                                    You have no notifications yet
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // Mark single notification as read
        function markAsRead(notificationId) {
            fetch(`/api/notifications/${notificationId}/mark-read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                        item.classList.remove('unread');
                        item.classList.add('read');

                        // Remove unread indicator
                        const indicator = item.querySelector('.badge');
                        if (indicator) {
                            indicator.remove();
                        }

                        // Update unread count in topbar
                        updateUnreadCount(data.unread_count);

                        // Show success message
                        showToast('success', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                });
        }

        // Mark single notification as unread
        function markAsUnread(notificationId) {
            fetch(`/api/notifications/${notificationId}/mark-unread`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                        item.classList.remove('read');
                        item.classList.add('unread');

                        // Add unread indicator
                        if (!item.querySelector('.badge')) {
                            const indicator = document.createElement('div');
                            indicator.className = 'position-absolute top-0 end-0 me-3 mt-3';
                            indicator.innerHTML = '<span class="badge bg-primary rounded-pill">•</span>';
                            item.appendChild(indicator);
                        }

                        updateUnreadCount(data.unread_count);
                        showToast('success', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                });
        }

        // Mark all notifications as read
        function markAllAsRead() {
            fetch('/api/notifications/mark-all-read', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to update UI
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                });
        }

        // Delete notification
        function deleteNotification(notificationId) {
            if (!confirm('<?php echo app('translator')->get('translation.Are_you_sure_you_want_to_delete_this_notification'); ?>')) {
                return;
            }

            fetch(`/api/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove notification from DOM
                        const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                        item.remove();

                        showToast('success', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                });
        }

        // Update unread count in topbar
        function updateUnreadCount(count) {
            const badge = document.querySelector('#page-header-notifications-dropdown .badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline' : 'none';
            }
        }

        // Show toast notification
        function showToast(type, message) {
            // Implement your toast notification here
            // You can use libraries like Toastr, SweetAlert2, or custom implementation
            console.log(`${type}: ${message}`);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/notifications/index.blade.php ENDPATH**/ ?>