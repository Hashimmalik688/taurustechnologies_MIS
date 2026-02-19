<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">
    
    @include('layouts.head-css')

    <!-- Light Theme Stylesheet -->
    <link rel="stylesheet" href="{{ URL::asset('css/light-theme.css') }}">

    <!-- Modern White Theme - Complete Redesign -->
    <link rel="stylesheet" href="{{ URL::asset('css/modern-white-theme.css') }}">

    <!-- Dark Theme Stylesheet - Comprehensive -->
    <link rel="stylesheet" href="{{ URL::asset('css/dark-theme.css') }}?v={{ time() }}" id="dark-theme-style">

    <!-- Custom Layout Styles - Optimized -->
    @vite(['resources/css/custom-layout.css'])
    <!-- Admin UI overrides -->
    <link rel="stylesheet" href="{{ URL::asset('css/admin-ui.css') }}">
    
    <!-- Device Fingerprinting for Attendance Tracking -->
    <script src="{{ URL::asset('js/device-fingerprint.js') }}"></script>
    
    <!-- Global Chat Notifications will load after Echo in vendor-scripts -->
    <script>
        // Set current user ID globally for chat notifications
        window.currentUserId = {{ auth()->id() }};
    </script>
    
    <!-- Community Announcement Pop-up Styles -->
    <style>
        .ann-popup {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 10050;
            width: 380px;
            max-width: calc(100vw - 40px);
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            transform: translateX(420px);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
            pointer-events: none;
        }
        .ann-popup.visible {
            transform: translateX(0);
            opacity: 1;
            pointer-events: auto;
        }
        .ann-popup-progress {
            height: 3px;
            background: linear-gradient(90deg, var(--bs-gradient-start), var(--bs-gradient-end));
            transition: width 0.5s linear;
            width: 100%;
        }
        .ann-btn {
            position: fixed;
            bottom: 100px;
            right: 30px;
            z-index: 10049;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102,126,234,0.4);
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: all 0.3s ease;
        }
        .ann-btn.visible { display: flex; animation: annBtnPulse 2s ease-in-out infinite; }
        .ann-btn.visible.checked { animation: none; opacity: 0.6; }
        .ann-btn.visible.checked:hover { opacity: 1; }
        .ann-btn:hover { transform: scale(1.1); box-shadow: 0 6px 25px rgba(102,126,234,0.6); animation: none; }
        @keyframes annBtnPulse {
            0% { box-shadow: 0 4px 15px rgba(102,126,234,0.4); transform: scale(1); }
            25% { box-shadow: 0 0 20px 8px rgba(102,126,234,0.6); transform: scale(1.15); }
            50% { box-shadow: 0 4px 15px rgba(102,126,234,0.4); transform: scale(1); }
            75% { box-shadow: 0 0 20px 8px rgba(118,75,162,0.5); transform: scale(1.1); }
            100% { box-shadow: 0 4px 15px rgba(102,126,234,0.4); transform: scale(1); }
        }
        .ann-btn-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--bs-ui-danger);
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            border: 2px solid white;
        }
    </style>
    
    @yield('css')
</head>

<body>
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div id="page-content">
        <!-- Top Header -->
        <div class="top-header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="bx bx-menu"></i>
                </button>
                <div class="company-branding d-none d-lg-block">
                    <span style="color: var(--bs-gold); font-weight: 800; font-size: 1.5rem; letter-spacing: 2px;">
                        TAURUS TECHNOLOGIES
                    </span>
                </div>
            </div>

            <div class="user-menu">
                <!-- USA Timer Display -->
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%); border-radius: 8px; color: white; font-weight: 600; font-size: 0.9rem;">
                    <i class="bx bx-time-five"></i>
                    <span style="font-size: 0.8rem; opacity: 0.9;">USA</span>
                    <span id="usaTimerDisplay">--:-- --</span>
                </div>

                <!-- Theme Toggle Button -->
                <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <i class="bx bx-moon" id="themeIcon"></i>
                </button>

                <!-- Chat Button -->
                <div style="position: relative; display: flex; align-items: center; gap: 8px;">
                    <a href="{{ route('chat.index') }}" class="notification-btn" title="Team Chat" style="text-decoration: none; display: flex; align-items: center; gap: 6px;">
                        <i class="bx bx-message-square-dots" style="font-size: 1.5rem;"></i>
                        <span style="font-weight: 600; font-size: 0.95rem;">Chat</span>
                        <span class="chat-badge notification-badge" style="background: var(--bs-ui-success); display: none; font-size: 0.85rem; padding: 4px 8px; min-width: 24px; text-align: center; font-weight: 700;">0</span>
                    </a>
                </div>

                <!-- Notifications -->
                <div style="position: relative;">
                    <button class="notification-btn" onclick="toggleNotifications()" style="display: flex; align-items: center; gap: 6px;">
                        <i class="bx bx-bell" style="font-size: 1.5rem;"></i>
                        <span style="font-weight: 600; font-size: 0.95rem;">Notif</span>
                        <span class="notification-badge" id="notifBadge" style="font-size: 0.85rem; padding: 4px 8px; min-width: 24px; text-align: center; font-weight: 700;">{{ Auth::user()->unread_notifications_count }}</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h6>Notifications</h6>
                            <button class="btn btn-sm btn-link" style="color: var(--gold); text-decoration: none; font-weight: 600;" onclick="markAllRead()">
                                Mark all read
                            </button>
                        </div>
                        <div id="notificationList">
                            <!-- Notifications will be loaded via AJAX -->
                            <div style="text-align: center; padding: 2rem; color: var(--bs-surface-400);">
                                <i class="bx bx-loader-alt bx-spin" style="font-size: 2rem;"></i>
                                <div style="margin-top: 0.5rem;">Loading notifications...</div>
                            </div>
                        </div>
                        <div style="padding: 1rem; text-align: center; border-top: 1px solid var(--bs-surface-200);">
                            <a href="{{ route('notifications.index') }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: var(--bs-surface-100); border-radius: 8px; cursor: pointer;" onclick="document.getElementById('profileSettingsModal').querySelector('.modal').classList.add('show'); document.getElementById('profileSettingsModal').querySelector('.modal').style.display='block';" data-bs-toggle="modal" data-bs-target="#profileSettingsModal">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="user-avatar">
                    @else
                        <div class="user-avatar" style="background: var(--gold); display: flex; align-items: center; justify-content: center; color: #111; font-weight: 700;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div style="text-align: left;">
                        <div style="font-weight: 600; font-size: 0.875rem;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--bs-surface-500);">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <!-- Profile Settings Button -->
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#profileSettingsModal">
                    <i class="bx bx-user-circle"></i>
                </button>

                <!-- Logout Button -->
                <a href="{{ route('logout.get') }}" class="btn btn-danger btn-sm">
                    <i class="bx bx-log-out me-1"></i> Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid" style="padding: 1.5rem;">
            @yield('content')
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Call Connected Popup Modal -->
    <!-- ============================================================== -->
    <div class="modal fade" id="callConnectedModal" tabindex="-1" aria-labelledby="callConnectedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="callConnectedModalLabel">Call Connected</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Connected with <strong id="callerName">...</strong>.</p>
                    <form id="callEmployeeForm">
                        <input type="hidden" id="employeeId" name="employee_id">
                        
                        <!-- Example for Name -->
                        <div class="mb-3">
                            <label for="employeeName" class="form-label" id="employeeNameLabel">Name:</label>
                            <input type="text" class="form-control" id="employeeName" name="name">
                        </div>

                        <!-- Example for Email -->
                        <div class="mb-3">
                            <label for="employeeEmail" class="form-label" id="employeeEmailLabel">Email:</label>
                            <input type="email" class="form-control" id="employeeEmail" name="email">
                        </div>

                        <!-- Add other employee fields here following the same pattern -->

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEmployeeButton">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Community Context Menu (Hidden by default) - Available globally -->
    <div id="communityContextMenu" class="dropdown-menu" style="display: none; position: fixed; z-index: 9999;">
        <a class="dropdown-item" href="#" id="ctxManageMembers">
            <i class="bx bx-user-plus me-2"></i> Manage Members
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="#" id="ctxDeleteCommunity">
            <i class="bx bx-trash me-2"></i> Delete Community
        </a>
    </div>

    @include('layouts.vendor-scripts')

    <script>
        /**
         * Mirror top horizontal scrollbar for wide tables inside `.table-wrapper`.
         * Creates a small scrollable div above each `.table-wrapper` and syncs scrollLeft both ways.
         */
        function initTableTopScrollMirrors() {
            const wrappers = document.querySelectorAll('.table-wrapper');
            wrappers.forEach(wrapper => {
                // skip if already has mirror
                if (wrapper.previousElementSibling && wrapper.previousElementSibling.classList && wrapper.previousElementSibling.classList.contains('table-scroll-top')) return;

                // create mirror container
                const mirror = document.createElement('div');
                mirror.className = 'table-scroll-top sticky-top';
                const inner = document.createElement('div');
                inner.className = 'scroll-inner';
                mirror.appendChild(inner);

                // insert above wrapper
                wrapper.parentNode.insertBefore(mirror, wrapper);

                // set inner width to match the scrollWidth of the wrapper's first child table
                const table = wrapper.querySelector('table');
                if (!table) return;
                const updateInnerWidth = () => { inner.style.width = table.scrollWidth + 'px'; };

                // sync scrolls
                mirror.addEventListener('scroll', () => { wrapper.scrollLeft = mirror.scrollLeft; });
                wrapper.addEventListener('scroll', () => { mirror.scrollLeft = wrapper.scrollLeft; });

                // update on images/fonts load and window resize
                window.addEventListener('resize', updateInnerWidth);
                // in case fonts/images change table width after initial render
                setTimeout(updateInnerWidth, 50);
                // observe changes to table size
                try {
                    const ro = new ResizeObserver(updateInnerWidth);
                    ro.observe(table);
                } catch (e) { /* ResizeObserver may not be available, that's OK */ }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initTableTopScrollMirrors();
            // re-init after a short delay in case table data is injected later
            setTimeout(initTableTopScrollMirrors, 250);
        });
    </script>
    <script>
        // Attendance check-in / check-out handlers in top header
        document.addEventListener('DOMContentLoaded', function(){
            const checkin = document.getElementById('btnCheckin');
            const checkout = document.getElementById('btnCheckout');
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!tokenMeta) return;
            const token = tokenMeta.getAttribute('content');

            function post(url, body) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body || {})
                }).then(r => r.json());
            }

                    if (checkin) {
                checkin.addEventListener('click', function(e){
                    e.preventDefault();
                    checkin.disabled = true;
                    post('{{ url('/attendance/check-in') }}', { force_office: 0 })
                        .then(data => {
                            if (data.success) {
                                alert(data.message || 'Checked in');
                                setTimeout(() => location.reload(), 500);
                            } else {
                                alert(data.message || 'Could not check in');
                                checkin.disabled = false;
                            }
                        }).catch(err => { console.error(err); alert('Network error'); checkin.disabled = false; });
                });
            }

            if (checkout) {
                checkout.addEventListener('click', function(e){
                    e.preventDefault();
                    checkout.disabled = true;
                    post('{{ url('/attendance/check-out') }}')
                        .then(data => {
                            if (data.success) {
                                alert(data.message || 'Checked out');
                                setTimeout(() => location.reload(), 500);
                            } else {
                                alert(data.message || 'Could not check out');
                                checkout.disabled = false;
                            }
                        }).catch(err => { console.error(err); alert('Network error'); checkout.disabled = false; });
                });
            }
        });
    </script>
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');

            // Save state
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Toggle Notifications
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const isShowing = dropdown.classList.contains('show');

            if (!isShowing) {
                loadNotifications();
            }

            dropdown.classList.toggle('show');
        }

        // Load notifications via AJAX
        function loadNotifications() {
            const listContainer = document.getElementById('notificationList');

            fetch('{{ route('api.notifications.topbar') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications.length === 0) {
                        listContainer.innerHTML = `
                            <div style="text-align: center; padding: 3rem 2rem; color: #94a3b8;">
                                <i class="bx bx-bell-off" style="font-size: 3rem; opacity: 0.5;"></i>
                                <div style="margin-top: 1rem; font-weight: 600;">No notifications</div>
                                <div style="font-size: 0.875rem; margin-top: 0.5rem;">You're all caught up!</div>
                            </div>
                        `;
                    } else {
                        listContainer.innerHTML = data.notifications.map(notif => `
                            <div class="notification-item ${notif.is_read ? '' : 'unread'}">
                                <div style="font-weight: 600; color: #111;">${notif.title}</div>
                                <div style="font-size: 0.875rem; color: #6b7280;">${notif.message}</div>
                                <div class="time">${notif.time_ago}</div>
                            </div>
                        `).join('');
                    }

                    // Update badge
                    document.getElementById('notifBadge').textContent = data.unread_count;
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    listContainer.innerHTML = `
                        <div style="text-align: center; padding: 2rem; color: #ef4444;">
                            <i class="bx bx-error-circle" style="font-size: 2rem;"></i>
                            <div style="margin-top: 0.5rem;">Failed to load notifications</div>
                        </div>
                    `;
                });
        }

        // Mark all notifications as read
        function markAllRead() {
            fetch('{{ route('api.notifications.mark-all-read') }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    document.getElementById('notifBadge').textContent = '0';
                }
            })
            .catch(error => console.error('Error marking notifications as read:', error));
        }

        // Close notifications when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const btn = event.target.closest('.notification-btn');

            if (!btn && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Theme Toggle Function
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            const themeIcon = document.getElementById('themeIcon');
            
            // Update theme
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            if (newTheme === 'dark') {
                themeIcon.classList.remove('bx-moon');
                themeIcon.classList.add('bx-sun');
            } else {
                themeIcon.classList.remove('bx-sun');
                themeIcon.classList.add('bx-moon');
            }
        }

        // Load saved theme on page load
        function loadTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            
            html.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark' && themeIcon) {
                themeIcon.classList.remove('bx-moon');
                themeIcon.classList.add('bx-sun');
            }
        }

        // Load theme immediately (before DOM ready)
        loadTheme();

        // Load saved sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
            }
            
            // Ensure theme is loaded
            loadTheme();
            
            // Community Context Menu Event Listeners - Global
            const ctxManageMembersBtn = document.getElementById('ctxManageMembers');
            if (ctxManageMembersBtn) {
                ctxManageMembersBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.openCommunityMemberManagement && window.contextMenuCommunityId) {
                        window.openCommunityMemberManagement(window.contextMenuCommunityId, window.contextMenuCommunityName);
                    } else {
                        alert('This feature is only available on the chat page.');
                        window.location.href = '/chat';
                    }
                });
            }

            const ctxDeleteCommunityBtn = document.getElementById('ctxDeleteCommunity');
            if (ctxDeleteCommunityBtn) {
                ctxDeleteCommunityBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.deleteCommunity && window.contextMenuCommunityId) {
                        window.deleteCommunity(window.contextMenuCommunityId, window.contextMenuCommunityName);
                    } else {
                        alert('This feature is only available on the chat page.');
                        window.location.href = '/chat';
                    }
                });
            }
            
            // Hide context menu when clicking outside
            document.addEventListener('click', function(e) {
                const menu = document.getElementById('communityContextMenu');
                if (menu && e.target !== menu && !menu.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        });

        // Mobile: Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>

    @yield('script')

    {{-- Real-time Call Event Listener --}}
    <script type="module">
        // Wait for Echo to be initialized by the chat script or another script
        function waitForEcho(callback) {
            const interval = setInterval(() => {
                if (window.Echo) {
                    clearInterval(interval);
                    callback(window.Echo);
                }
            }, 200);
        }

        function showCallPopup(eventData) {
            // Assuming eventData contains employee info, e.g., eventData.employee
            const employee = eventData.employee; 
            if (!employee) {
                console.warn('Call event received, but no employee data found.');
                return;
            }

            const modalElement = document.getElementById('callConnectedModal');
            const callModal = new bootstrap.Modal(modalElement);

            // Populate form with "old:" data in labels
            document.getElementById('callerName').innerText = employee.name || 'Unknown';
            document.getElementById('employeeId').value = employee.id;

            // Name field
            const nameLabel = document.getElementById('employeeNameLabel');
            const nameInput = document.getElementById('employeeName');
            nameLabel.innerHTML = `Name: <span class="text-muted fw-normal">(old: ${employee.name})</span>`;
            nameInput.value = employee.name;

            // Email field
            const emailLabel = document.getElementById('employeeEmailLabel');
            const emailInput = document.getElementById('employeeEmail');
            emailLabel.innerHTML = `Email: <span class="text-muted fw-normal">(old: ${employee.email})</span>`;
            emailInput.value = employee.email;

            // --- Add other fields here ---

            callModal.show();
        }

        waitForEcho((echo) => {
            if (!echo || typeof echo.channel !== 'function') {
                console.warn('Echo instance available but channel() missing; skipping call listener setup.');
                return;
            }

            echo.channel('call')
                .listen('.call.connected', (e) => {
                    console.log('Call Connected Event Received:', e);
                    showCallPopup(e);
                });
        });

        // Example of how to manually trigger for testing:
        // window.showCallPopup({ employee: { id: 1, name: 'John Doe', email: 'john.doe@example.com' } });

    </script>

    <!-- Chat Unread Count Script -->
    <script>
    // Load chat unread count immediately and then every 3 seconds
    function loadChatUnreadCount() {
        fetch('/api/chat/unread-count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                console.log('Chat unread count response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Chat unread count data:', data);
                if (data.success) {
                    // Use total_count which includes chat messages + announcements
                    const totalCount = data.total_count || data.unread_count || 0;
                    console.log('Updating badge with count:', totalCount, '(messages:', data.unread_count, ', announcements:', data.announcement_count, ')');
                    updateChatBadge(totalCount);
                } else {
                    console.log('API returned success: false');
                }
            })
            .catch(error => {
                console.error('Error loading chat unread count:', error);
            });
    }

    function updateChatBadge(unreadCount) {
        const badge = document.querySelector('.chat-badge');
        console.log('Badge element found:', badge);
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.style.display = 'inline';
                console.log('Badge updated to:', unreadCount);
            } else {
                badge.style.display = 'none';
                console.log('Badge hidden (count is 0)');
            }
        } else {
            console.log('Badge element not found');
        }
    }

    // Start loading immediately without waiting for DOM ready
    loadChatUnreadCount();
    
    // Then refresh every 3 seconds for real-time notifications
    setInterval(loadChatUnreadCount, 3000);
    </script>

    <!-- Profile Settings Modal -->
    <div class="modal fade" id="profileSettingsModal" tabindex="-1" aria-labelledby="profileSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileSettingsModalLabel">Profile Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-profile" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="profile_id" value="{{ Auth::id() }}">
                        
                        <!-- Avatar Preview -->
                        <div class="text-center mb-3">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" id="avatarPreview" alt="{{ Auth::user()->name }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div id="avatarPreview" style="width: 100px; height: 100px; border-radius: 50%; background: var(--gold); display: inline-flex; align-items: center; justify-content: center; color: #111; font-weight: 700; font-size: 2.5rem;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Avatar Upload -->
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text">JPG, JPEG, PNG, WebP. Max 2MB.</div>
                            <span class="text-danger" id="avatarError"></span>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="profile-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="profile-name" name="name" value="{{ Auth::user()->name }}" required>
                            <span class="text-danger" id="nameError"></span>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="profile-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="profile-email" name="email" value="{{ Auth::user()->email }}" required>
                            <span class="text-danger" id="emailError"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // USA Timer Display in Topbar
    function updateUSATimer() {
        const usaTime = new Date().toLocaleString('en-US', {
            timeZone: 'America/New_York',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        const timerElement = document.getElementById('usaTimerDisplay');
        if (timerElement) {
            timerElement.textContent = usaTime;
        }
    }

    // Initialize timer on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateUSATimer();
        // Update every second
        setInterval(updateUSATimer, 1000);
    });

    // Avatar Preview
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('avatarPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = event.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.cssText = 'width: 100px; height: 100px; border-radius: 50%; object-fit: cover;';
                    preview.replaceWith(img);
                    img.id = 'avatarPreview';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Profile Update Form
    document.getElementById('update-profile')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const profileId = document.getElementById('profile_id').value;
        
        // Clear previous errors
        document.getElementById('nameError').textContent = '';
        document.getElementById('emailError').textContent = '';
        document.getElementById('avatarError').textContent = '';
        
        fetch(`{{ url('update-profile') }}/${profileId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Check if response is successful
            if (!response.ok && response.status !== 200) {
                return response.json().then(data => {
                    throw { response: data };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.isSuccess) {
                alert('Profile updated successfully!');
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('profileSettingsModal'));
                if (modal) modal.hide();
                setTimeout(() => location.reload(), 500);
            } else {
                alert(data.Message || 'Failed to update profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.response && error.response.errors) {
                if (error.response.errors.name) document.getElementById('nameError').textContent = error.response.errors.name[0];
                if (error.response.errors.email) document.getElementById('emailError').textContent = error.response.errors.email[0];
                if (error.response.errors.avatar) document.getElementById('avatarError').textContent = error.response.errors.avatar[0];
            } else {
                alert('Error updating profile: ' + (error.message || 'Unknown error'));
            }
        });
    });
    </script>

    <script>
    // ===== COMMUNITY ANNOUNCEMENT NOTIFICATIONS (Polling) =====
    (function() {
        let lastPollTime = null;
        let dismissTimer = null;
        let seenIds = JSON.parse(localStorage.getItem('seenAnnIds') || '[]');
        // seenVersions tracks id:updated_at so edits are detected
        let seenVersions = JSON.parse(localStorage.getItem('seenAnnVersions') || '{}');
        let lastAnnouncement = null;
        let isChecked = localStorage.getItem('annChecked') === '1';

        // Restore last announcement from localStorage on every page load
        try {
            const stored = localStorage.getItem('lastAnnouncement');
            if (stored) {
                lastAnnouncement = JSON.parse(stored);
                if (lastAnnouncement && lastAnnouncement._storedAt) {
                    const age = Date.now() - lastAnnouncement._storedAt;
                    if (age > 3600000) { // 1 hour
                        lastAnnouncement = null;
                        localStorage.removeItem('lastAnnouncement');
                        localStorage.removeItem('annChecked');
                        isChecked = false;
                    }
                }
            }
        } catch(e) { lastAnnouncement = null; }

        const PRIORITY = {
            urgent:  { color: '#ef4444', icon: 'bx-error-circle', label: 'URGENT' },
            warning: { color: '#f59e0b', icon: 'bx-error',        label: 'Warning' },
            info:    { color: '#3b82f6', icon: 'bx-info-circle',  label: 'Info' },
            normal:  { color: '#6b7280', icon: 'bx-info-circle',  label: 'Normal' },
        };

        function renderPopup(ann) {
            const popup = document.getElementById('annPopup');
            if (!popup) return;

            const p = PRIORITY[ann.priority] || PRIORITY.normal;

            document.getElementById('annCommunity').textContent = ann.community_name;
            document.getElementById('annIcon').style.background = ann.community_color || '#667eea';

            const badge = document.getElementById('annPriority');
            badge.style.background = p.color;
            badge.innerHTML = '<i class="bx ' + p.icon + '"></i> ' + p.label;

            const titleEl = document.getElementById('annTitle');
            if (ann.title) { titleEl.textContent = ann.title; titleEl.style.display = 'block'; }
            else { titleEl.style.display = 'none'; }

            document.getElementById('annMsg').textContent = ann.message;

            // Update link to go to community section in chat
            const link = document.getElementById('annLink');
            if (link && ann.community_id) {
                link.href = '/chat?community=' + ann.community_id;
            }
        }

        function showPopup(ann, withCountdown) {
            renderPopup(ann);
            const popup = document.getElementById('annPopup');

            // Save to localStorage so it persists across pages
            lastAnnouncement = ann;
            try {
                localStorage.setItem('lastAnnouncement', JSON.stringify(Object.assign({}, ann, { _storedAt: Date.now() })));
            } catch(e) {}

            // Mark as unchecked (new/updated content) — button will pulse
            isChecked = false;
            localStorage.setItem('annChecked', '0');
            const btn = document.getElementById('annBtn');
            btn.classList.add('visible');
            btn.classList.remove('checked');

            if (withCountdown) {
                document.getElementById('annProgress').style.width = '100%';
                popup.classList.add('visible');

                let remaining = 100;
                const bar = document.getElementById('annProgress');
                clearInterval(dismissTimer);
                dismissTimer = setInterval(() => {
                    remaining -= 2.5;
                    bar.style.width = Math.max(0, remaining) + '%';
                    if (remaining <= 0) {
                        clearInterval(dismissTimer);
                        closePopup();
                    }
                }, 500);
            }
        }

        function closePopup() {
            const popup = document.getElementById('annPopup');
            if (popup) popup.classList.remove('visible');
            clearInterval(dismissTimer);
        }

        function reopenPopup() {
            // Re-read from localStorage in case it was updated on another tab/page
            try {
                const stored = localStorage.getItem('lastAnnouncement');
                if (stored) lastAnnouncement = JSON.parse(stored);
            } catch(e) {}

            if (lastAnnouncement) {
                // User clicked the button → mark as checked, stop pulsing
                isChecked = true;
                localStorage.setItem('annChecked', '1');
                const btn = document.getElementById('annBtn');
                btn.classList.add('checked');

                showPopup(lastAnnouncement, true);
                // Keep checked state even though showPopup sets unchecked — override
                setTimeout(function() {
                    btn.classList.add('checked');
                    localStorage.setItem('annChecked', '1');
                }, 10);
            }
        }

        function isNewOrEdited(ann) {
            // Check if this announcement is brand new or has been edited since we last saw it
            const versionKey = String(ann.id);
            const lastSeen = seenVersions[versionKey];
            const currentVersion = ann.updated_at || ann.created_at;

            if (!lastSeen || lastSeen !== currentVersion) {
                // New or edited — update the version tracker
                seenVersions[versionKey] = currentVersion;
                localStorage.setItem('seenAnnVersions', JSON.stringify(seenVersions));
                return true;
            }
            return false;
        }

        function pollAnnouncements() {
            const url = lastPollTime
                ? '/api/chat/announcements/poll?since=' + encodeURIComponent(lastPollTime)
                : '/api/chat/announcements/poll';

            fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                if (data.server_time) lastPollTime = data.server_time;

                if (data.announcements && data.announcements.length > 0) {
                    for (let i = data.announcements.length - 1; i >= 0; i--) {
                        const ann = data.announcements[i];
                        if (isNewOrEdited(ann)) {
                            showPopup(ann, true);
                        }
                    }
                }
            })
            .catch(() => {});
        }

        window.closeAnnPopup = closePopup;
        window.reopenAnnPopup = reopenPopup;

        document.addEventListener('DOMContentLoaded', function() {
            // If there's a stored announcement, show the floating button immediately
            if (lastAnnouncement) {
                const btn = document.getElementById('annBtn');
                btn.classList.add('visible');
                if (isChecked) btn.classList.add('checked');
            }

            // Start polling: first after 2s, then every 10s
            setTimeout(pollAnnouncements, 2000);
            setInterval(pollAnnouncements, 10000);
        });
    })();
    </script>

    <!-- Announcement Pop-up -->
    <div id="annPopup" class="ann-popup">
        <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bs-surface-200);">
            <div id="annIcon" style="width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; flex-shrink:0;">
                <i class="bx bx-bullhorn" style="font-size:18px;"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:12px; color:var(--bs-surface-muted); font-weight:500;">New Announcement</div>
                <div id="annCommunity" style="font-weight:700; font-size:15px; color:#111; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></div>
            </div>
            <button onclick="closeAnnPopup()" style="background:none; border:none; color:var(--bs-surface-muted); cursor:pointer; font-size:22px; padding:0; line-height:1;">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div style="padding:14px 16px;">
            <span id="annPriority" style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:600; color:white; margin-bottom:10px;"></span>
            <h6 id="annTitle" style="font-weight:700; font-size:15px; color:#111; margin:8px 0 6px;"></h6>
            <p id="annMsg" style="font-size:13px; color:#4b5563; line-height:1.6; margin:0; max-height:120px; overflow-y:auto;"></p>
        </div>
        <div style="padding:10px 16px; background:var(--bs-surface-50); border-top:1px solid var(--bs-surface-200);">
            <div id="annProgress" class="ann-popup-progress"></div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:8px;">
                <span style="font-size:12px; color:var(--bs-surface-muted);">Just now</span>
                <a id="annLink" href="/chat" style="color:var(--bs-gradient-start); font-weight:600; font-size:13px; text-decoration:none;">View in Community →</a>
            </div>
        </div>
    </div>

    <!-- Floating Re-open Button (above sticky notes button) -->
    <button id="annBtn" class="ann-btn" onclick="reopenAnnPopup()" title="Recent Announcement">
        <i class="bx bx-bell"></i>
    </button>

    <!-- Include Sticky Notes Component -->
    @include('components.sticky-notes')

    @stack('scripts')

</body>
</html>
