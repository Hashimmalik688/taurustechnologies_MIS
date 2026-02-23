<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Notifications'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('partials.pipeline-dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .notif-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .notif-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .notif-hdr h4 i { color: #d4af37; font-size: 1.2rem; }

    /* Filter pills */
    .ntf-tabs { display: flex; gap: .3rem; margin-bottom: .65rem; }
    .ntf-tab {
        font-size: .68rem; font-weight: 600; padding: .28rem .65rem;
        border-radius: 22px; border: 1px solid rgba(0,0,0,.08);
        background: var(--bs-card-bg); color: var(--bs-surface-600);
        text-decoration: none; transition: all .15s;
    }
    .ntf-tab:hover { border-color: #d4af37; color: #b89730; }
    .ntf-tab.active {
        background: linear-gradient(135deg, #d4af37, #e8c84a);
        color: #fff; border-color: #d4af37; font-weight: 700;
    }

    /* Date group header */
    .ntf-date-group {
        font-size: .65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: var(--bs-surface-400);
        padding: .35rem 0; margin-bottom: .35rem;
        border-bottom: 1px solid rgba(212,175,55,.12);
        display: flex; align-items: center; gap: .35rem;
    }
    .ntf-date-group::before {
        content: ''; width: 3px; height: 12px; border-radius: 2px;
        background: #d4af37; display: inline-block;
    }

    /* Notification card */
    .ntf-card {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .65rem .75rem; border-radius: .45rem;
        border: 1px solid transparent;
        transition: all .2s; position: relative; cursor: default;
        margin-bottom: .35rem;
    }
    .ntf-card:hover {
        background: rgba(212,175,55,.03);
        border-color: rgba(212,175,55,.1);
    }
    .ntf-card.unread {
        background: rgba(212,175,55,.04);
        border-left: 3px solid #d4af37;
    }
    .ntf-card.unread .ntf-title { font-weight: 700; }

    .ntf-icon {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0;
    }
    .ntf-icon.bg-primary { background: rgba(85,110,230,.12) !important; color: #556ee6; }
    .ntf-icon.bg-success { background: rgba(52,195,143,.12) !important; color: #1a8754; }
    .ntf-icon.bg-warning { background: rgba(241,180,76,.12) !important; color: #b87a14; }
    .ntf-icon.bg-danger { background: rgba(244,106,106,.12) !important; color: #c84646; }
    .ntf-icon.bg-info { background: rgba(80,165,241,.12) !important; color: #2b81c9; }

    .ntf-title { font-size: .78rem; font-weight: 600; color: var(--bs-body-color); margin-bottom: 2px; }
    .ntf-msg { font-size: .72rem; color: var(--bs-surface-500); margin-bottom: 2px; line-height: 1.4; }
    .ntf-time { font-size: .62rem; color: var(--bs-surface-400); display: flex; align-items: center; gap: .2rem; }

    /* Actions dropdown on hover */
    .ntf-actions { opacity: 0; transition: opacity .2s; position: absolute; top: .5rem; right: .5rem; }
    .ntf-card:hover .ntf-actions { opacity: 1; }
    .ntf-actions .dropdown-toggle::after { display: none; }

    /* Unread dot */
    .ntf-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #d4af37; position: absolute;
        top: .75rem; right: .75rem;
    }

    /* Empty state */
    .ntf-empty {
        text-align: center; padding: 3rem 1rem;
        color: var(--bs-surface-400);
    }
    .ntf-empty i { font-size: 2.5rem; opacity: .4; display: block; margin-bottom: .5rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="notif-hdr">
        <div>
            <h4><i class="bx bx-bell"></i> Notifications</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                <button type="button" class="act-btn a-primary" onclick="markAllAsRead()">
                    <i class="bx bx-check-double"></i> Mark all read
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <div class="kpi-lbl">Unread</div>
            <div class="kpi-val"><?php echo e($unreadCount); ?></div>
        </div>
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Total</div>
            <div class="kpi-val"><?php echo e($totalCount); ?></div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Read</div>
            <div class="kpi-val"><?php echo e($totalCount - $unreadCount); ?></div>
        </div>
    </div>

    
    <div class="ntf-tabs">
        <a href="<?php echo e(route('notifications.index', ['type' => 'all'])); ?>" class="ntf-tab <?php echo e($currentType == 'all' ? 'active' : ''); ?>">
            All
        </a>
        <a href="<?php echo e(route('notifications.index', ['type' => 'unread'])); ?>" class="ntf-tab <?php echo e($currentType == 'unread' ? 'active' : ''); ?>">
            Unread (<?php echo e($unreadCount); ?>)
        </a>
        <a href="<?php echo e(route('notifications.index', ['type' => 'read'])); ?>" class="ntf-tab <?php echo e($currentType == 'read' ? 'active' : ''); ?>">
            Read
        </a>
    </div>

    
    <div class="ex-card sec-card">
        <div class="pipe-hdr">
            <i class="bx bx-bell"></i> Notifications
            <span class="badge-count"><?php echo e($totalCount); ?> total</span>
        </div>
        <div class="sec-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($groupedNotifications->count() > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupedNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="ntf-date-group"><?php echo e($group['label']); ?></div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $group['notifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="ntf-card <?php echo e($notification->isUnread() ? 'unread' : ''); ?>" data-notification-id="<?php echo e($notification->id); ?>">
                            
                            <div class="ntf-icon bg-<?php echo e($notification->color ?? 'primary'); ?>">
                                <i class="bx <?php echo e($notification->icon ?? 'bx-bell'); ?>"></i>
                            </div>

                            
                            <div class="flex-grow-1" style="min-width:0">
                                <div class="ntf-title"><?php echo e($notification->title); ?></div>
                                <div class="ntf-msg"><?php echo e($notification->message); ?></div>
                                <div class="ntf-time">
                                    <i class="mdi mdi-clock-outline"></i>
                                    <?php echo e($notification->time_ago); ?>

                                </div>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->isUnread()): ?>
                                <span class="ntf-dot"></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div class="ntf-actions">
                                <div class="dropdown">
                                    <button class="act-btn a-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="padding:.15rem .3rem;font-size:.6rem">
                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="font-size:.75rem">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->isUnread()): ?>
                                            <li><a class="dropdown-item" href="#" onclick="markAsRead(<?php echo e($notification->id); ?>)"><i class="bx bx-check me-1"></i>Mark as read</a></li>
                                        <?php else: ?>
                                            <li><a class="dropdown-item" href="#" onclick="markAsUnread(<?php echo e($notification->id); ?>)"><i class="bx bx-mail-send me-1"></i>Mark as unread</a></li>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteNotification(<?php echo e($notification->id); ?>)"><i class="bx bx-trash me-1"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="d-flex justify-content-center mt-3" style="font-size:.72rem">
                    <?php echo e($notifications->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <div class="ntf-empty">
                    <i class="bx bx-bell-off"></i>
                    <p style="font-weight:600;font-size:.85rem;margin-bottom:.25rem">No notifications found</p>
                    <p style="font-size:.72rem">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function markAsRead(notificationId) {
            fetch('/api/notifications/' + notificationId + '/mark-read', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector('[data-notification-id="' + notificationId + '"]');
                    item.classList.remove('unread');
                    const dot = item.querySelector('.ntf-dot');
                    if (dot) dot.remove();
                    updateUnreadCount(data.unread_count);
                    showToast('success', data.message);
                }
            })
            .catch(error => { console.error('Error:', error); showToast('error', 'An error occurred'); });
        }

        function markAsUnread(notificationId) {
            fetch('/api/notifications/' + notificationId + '/mark-unread', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector('[data-notification-id="' + notificationId + '"]');
                    item.classList.add('unread');
                    if (!item.querySelector('.ntf-dot')) {
                        const dot = document.createElement('span');
                        dot.className = 'ntf-dot';
                        item.appendChild(dot);
                    }
                    updateUnreadCount(data.unread_count);
                    showToast('success', data.message);
                }
            })
            .catch(error => { console.error('Error:', error); showToast('error', 'An error occurred'); });
        }

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
                if (data.success) { window.location.reload(); }
            })
            .catch(error => { console.error('Error:', error); showToast('error', 'An error occurred'); });
        }

        function deleteNotification(notificationId) {
            if (!confirm('Are you sure you want to delete this notification?')) return;

            fetch('/api/notifications/' + notificationId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector('[data-notification-id="' + notificationId + '"]');
                    item.style.transition = 'opacity .3s, transform .3s';
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(20px)';
                    setTimeout(function() { item.remove(); }, 300);
                    showToast('success', data.message);
                }
            })
            .catch(error => { console.error('Error:', error); showToast('error', 'An error occurred'); });
        }

        function updateUnreadCount(count) {
            var badge = document.getElementById('notifBadge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-flex' : 'none';
            }
        }

        function showToast(type, message) {
            console.log(type + ': ' + message);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/notifications/index.blade.php ENDPATH**/ ?>