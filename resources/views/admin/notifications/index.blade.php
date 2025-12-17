@extends('layouts.master')

@section('title')
    @lang('translation.Notifications')
@endsection

@section('css')
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
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Notifications
        @endslot
    @endcomponent

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
                                    {{ $unreadCount }} Unread
                                    <span class="mx-2">|</span>
                                    {{ $totalCount }} Total
                                </small>
                            </div>
                            @if ($unreadCount > 0)
                                <button type="button" class="btn btn-soft-primary btn-sm" onclick="markAllAsRead()">
                                    <i class="bx bx-check-double me-1"></i> Mark all as read
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Filter tabs -->
                    <ul class="nav nav-pills filter-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link {{ $currentType == 'all' ? 'active' : '' }}"
                                href="{{ route('notifications.index', ['type' => 'all']) }}">
                                All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentType == 'unread' ? 'active' : '' }}"
                                href="{{ route('notifications.index', ['type' => 'unread']) }}">
                                Unread ({{ $unreadCount }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentType == 'read' ? 'active' : '' }}"
                                href="{{ route('notifications.index', ['type' => 'read']) }}">
                                Read
                            </a>
                        </li>
                    </ul>

                    <!-- Notifications list -->
                    @if ($groupedNotifications->count() > 0)
                        @foreach ($groupedNotifications as $group)
                            <div class="notification-group mb-4">
                                <div class="date-group-header">
                                    <h6 class="text-muted mb-0">{{ $group['label'] }}</h6>
                                </div>

                                @foreach ($group['notifications'] as $notification)
                                    <div class="notification-item {{ $notification->isUnread() ? 'unread' : 'read' }} p-3 mb-2 rounded position-relative"
                                        data-notification-id="{{ $notification->id }}">
                                        <div class="d-flex">
                                            <!-- Avatar/Icon -->
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                @if ($notification->icon)
                                                    <span
                                                        class="avatar-title bg-{{ $notification->color }} rounded-circle font-size-16">
                                                        <i class="bx {{ $notification->icon }}"></i>
                                                    </span>
                                                @else
                                                    <span
                                                        class="avatar-title bg-{{ $notification->color }} rounded-circle font-size-16">
                                                        <i class="bx bx-bell"></i>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mt-0 mb-1 notification-title">{{ $notification->title }}
                                                        </h6>
                                                        <div class="font-size-13 text-muted">
                                                            <p class="mb-1">{{ $notification->message }}</p>
                                                            <p class="mb-0">
                                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                                {{ $notification->time_ago }}
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
                                                                @if ($notification->isUnread())
                                                                    <li>
                                                                        <a class="dropdown-item" href="#"
                                                                            onclick="markAsRead({{ $notification->id }})">
                                                                            <i class="bx bx-check me-2"></i>
                                                                            Mark as read
                                                                        </a>
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <a class="dropdown-item" href="#"
                                                                            onclick="markAsUnread({{ $notification->id }})">
                                                                            <i class="bx bx-mail-send me-2"></i>
                                                                            Mark as unread
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#"
                                                                        onclick="deleteNotification({{ $notification->id }})">
                                                                        <i class="bx bx-trash me-2"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($notification->isUnread())
                                            <div class="position-absolute top-0 end-0 me-3 mt-3">
                                                <span class="badge bg-primary rounded-pill">•</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-lg-12">
                                {{ $notifications->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <!-- Empty state -->
                        <div class="empty-state">
                            <i class="bx bx-bell-off"></i>
                            <h4>No notifications found</h4>
                            <p class="text-muted">
                                @if ($currentType == 'unread')
                                    You have no unread notifications
                                @elseif($currentType == 'read')
                                    You have no read notifications
                                @else
                                    You have no notifications yet
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
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
            if (!confirm('@lang('translation.Are_you_sure_you_want_to_delete_this_notification')')) {
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
@endsection
