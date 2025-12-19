<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index" class="logo logo-dark">
                    <span class="logo-sm">
                        <span style="color: #d4af37; font-weight: 700; font-size: 1.5rem;">T</span>
                    </span>
                    <span class="logo-lg">
                        <span style="color: #d4af37; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px;">TAURUS CRM</span>
                    </span>
                </a>

                <a href="index" class="logo logo-light">
                    <span class="logo-sm">
                        <span style="color: #d4af37; font-weight: 700; font-size: 1.5rem;">T</span>
                    </span>
                    <span class="logo-lg">
                        <span style="color: #d4af37; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px; text-shadow: 0 0 20px rgba(212, 175, 55, 0.5);">TAURUS CRM</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">
            <!-- Company Branding -->
            <div class="me-4 d-none d-lg-block">
                <span style="color: #d4af37; font-weight: 800; font-size: 1.5rem; letter-spacing: 2px; text-shadow: 0 0 20px rgba(212, 175, 55, 0.5); text-transform: uppercase;">
                    TAURUS TECHNOLOGIES
                </span>
            </div>

            <!-- Chat Button -->
            <div class="dropdown d-inline-block me-2">
                <a href="{{ route('chat.index') }}" class="btn header-item noti-icon waves-effect" 
                   title="Team Chat" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="bx bx-message-square-dots"></i>
                    <span class="badge bg-success rounded-pill chat-badge" style="display: none;">0</span>
                </a>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge bg-danger rounded-pill notification-badge" style="display: none;">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications">@lang('translation.Notifications')</h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('notifications.index') }}" class="small"
                                    key="t-view-all">@lang('translation.View_All')</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;" id="notifications-container">
                        <!-- Notifications will be loaded here via AJAX -->
                        <div class="text-center p-4" id="notifications-loading">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="mt-2 text-muted">Loading notifications...</div>
                        </div>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center"
                            href="{{ route('notifications.index') }}">
                            <i class="mdi mdi-arrow-right-circle me-1"></i>
                            <span key="t-view-more">@lang('translation.View_More')</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ isset(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('build/images/users/avatar-1.jpg') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ ucfirst(Auth::user()->name) }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    {{-- <a class="dropdown-item" href="javascript:void(0)"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i> <span
                            key="t-profile">@lang('translation.Profile')</span></a>
                    <a class="dropdown-item" href="javascript:void(0)"><i
                            class="bx bx-wallet font-size-16 align-middle me-1"></i> <span
                            key="t-my-wallet">@lang('translation.My_Wallet')</span></a>
                    <a class="dropdown-item d-block" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target=".change-password"><span class="badge bg-success float-end">11</span><i
                            class="bx bx-wrench font-size-16 align-middle me-1"></i> <span
                            key="t-settings">@lang('translation.Settings')</span></a>
                    <a class="dropdown-item" href="javascript:void(0)"><i
                            class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span
                            key="t-lock-screen">@lang('translation.Lock_screen')</span></a>
                    <div class="dropdown-divider"></div> --}}
                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">@lang('translation.Logout')</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<!--  Change-Password example -->
<div class="modal fade change-password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="change-password">
                    @csrf
                    <input type="hidden" value="{{ Auth::user()->id }}" id="data_id">
                    <div class="mb-3">
                        <label for="current_password">Current Password <span class="text-danger">*</span></label>
                        <input id="current-password" type="password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            name="current_password" autocomplete="current_password"
                            placeholder="Enter Current Password" value="{{ old('current_password') }}">
                        <div class="text-danger" id="current_passwordError" data-ajax-feedback="current_password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="newpassword">New Password <span class="text-danger">*</span></label>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            autocomplete="new_password" placeholder="Enter New Password">
                        <div class="text-danger" id="passwordError" data-ajax-feedback="password"></div>
                    </div>

                    <div class="mb-3">
                        <label for="userpassword">Confirm Password <span class="text-danger">*</span></label>
                        <input id="password-confirm" type="password" class="form-control"
                            name="password_confirmation" autocomplete="new_password"
                            placeholder="Enter New Confirm password">
                        <div class="text-danger" id="password_confirmError" data-ajax-feedback="password-confirm">
                        </div>
                    </div>

                    <div class="mt-3 d-grid">
                        <button class="btn btn-primary waves-effect waves-light UpdatePassword"
                            data-id="{{ Auth::user()->id }}" type="submit">Update Password</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadTopbarNotifications();

        // Refresh notifications every 30 seconds
        setInterval(loadTopbarNotifications, 30000);
    });

    function loadTopbarNotifications() {
        fetch('/api/notifications/topbar', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationDropdown(data.notifications, data.unread_count);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notifications-loading').innerHTML = `
            <div class="text-center text-muted p-3">
                <i class="bx bx-error-circle"></i>
                <div>Failed to load notifications</div>
            </div>
        `;
            });
    }

    function updateNotificationDropdown(notifications, unreadCount) {
        const container = document.getElementById('notifications-container');
        const badge = document.querySelector('.notification-badge');

        // Update badge
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }

        // Update notifications list
        if (notifications.length === 0) {
            container.innerHTML = `
            <div class="text-center p-4 text-muted">
                <i class="bx bx-bell-off font-size-24"></i>
                <div class="mt-2">No notifications</div>
            </div>
        `;
            return;
        }

        let notificationsHtml = '';
        notifications.forEach(notification => {
            const isUnread = !notification.is_read;
            const unreadClass = isUnread ? 'bg-light' : '';

            notificationsHtml += `
            <a href="#" class="text-reset notification-item ${unreadClass}" 
               onclick="markNotificationAsRead(${notification.id})" 
               data-notification-id="${notification.id}">
                <div class="d-flex">
                    <div class="avatar-xs me-3">
                        ${notification.icon ? 
                            `<span class="avatar-title bg-${notification.color} rounded-circle font-size-16">
                                <i class="bx ${notification.icon}"></i>
                            </span>` :
                            `<span class="avatar-title bg-${notification.color} rounded-circle font-size-16">
                                <i class="bx bx-bell"></i>
                            </span>`
                        }
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mt-0 mb-1 ${isUnread ? 'fw-bold' : ''}">${notification.title}</h6>
                        <div class="font-size-12 text-muted">
                            <p class="mb-1">${notification.message}</p>
                            <p class="mb-0">
                                <i class="mdi mdi-clock-outline"></i> 
                                <span>${notification.time_ago}</span>
                            </p>
                        </div>
                    </div>
                    ${isUnread ? '<div class="ms-2"><span class="badge bg-primary rounded-pill">•</span></div>' : ''}
                </div>
            </a>
        `;
        });

        container.innerHTML = notificationsHtml;
    }

    function markNotificationAsRead(notificationId) {
        const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);

        // Only mark as read if it's currently unread
        if (!notificationItem.classList.contains('bg-light')) {
            return;
        }

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
                    notificationItem.classList.remove('bg-light');

                    // Remove unread indicator
                    const indicator = notificationItem.querySelector('.badge');
                    if (indicator) {
                        indicator.remove();
                    }

                    // Remove bold from title
                    const title = notificationItem.querySelector('h6');
                    if (title) {
                        title.classList.remove('fw-bold');
                    }

                    // Update badge count
                    const badge = document.querySelector('.notification-badge');
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
    }
</script>

<style>
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f6f6f6;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .notification-item:hover {
        background-color: #f8f9fa !important;
        text-decoration: none;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-badge {
        font-size: 10px;
        padding: 3px 6px;
        position: relative;
        top: -2px;
        left: -8px;
    }

    #notifications-container {
        max-height: 300px;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
</style>
