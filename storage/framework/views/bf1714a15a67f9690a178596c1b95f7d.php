<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?> | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(URL::asset('images/favicon.ico')); ?>">
    
    <?php echo $__env->make('layouts.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Light Theme Stylesheet -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('css/light-theme.css')); ?>">

    <!-- Modern White Theme - Complete Redesign -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('css/modern-white-theme.css')); ?>">

    <!-- Custom Layout Styles - Optimized -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/custom-layout.css']); ?>
    <!-- Admin UI overrides -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('css/admin-ui.css')); ?>">
    
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
    <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div id="page-content">
        <!-- Top Header -->
        <div class="top-header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="bx bx-menu"></i>
                </button>
                <div class="company-branding d-none d-lg-block">
                    <span style="color: #d4af37; font-weight: 800; font-size: 1.5rem; letter-spacing: 2px;">
                        TAURUS TECHNOLOGIES
                    </span>
                </div>
            </div>

            <div class="user-menu">
                <!-- Notifications -->
                <div style="position: relative;">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="bx bx-bell" style="font-size: 1.25rem;"></i>
                        <span class="notification-badge" id="notifBadge"><?php echo e(Auth::user()->unread_notifications_count); ?></span>
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
                            <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                                <i class="bx bx-loader-alt bx-spin" style="font-size: 2rem;"></i>
                                <div style="margin-top: 0.5rem;">Loading notifications...</div>
                            </div>
                        </div>
                        <div style="padding: 1rem; text-align: center; border-top: 1px solid #e5e7eb;">
                            <a href="<?php echo e(route('notifications.index')); ?>" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #f3f4f6; border-radius: 8px;">
                    <?php if(Auth::user()->avatar): ?>
                        <img src="<?php echo e(Auth::user()->avatar); ?>" alt="<?php echo e(Auth::user()->name); ?>" class="user-avatar">
                    <?php else: ?>
                        <div class="user-avatar" style="background: var(--gold); display: flex; align-items: center; justify-content: center; color: #111; font-weight: 700;">
                            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                        </div>
                    <?php endif; ?>
                    <div style="text-align: left;">
                        <div style="font-weight: 600; font-size: 0.875rem;"><?php echo e(Auth::user()->name); ?></div>
                        <div style="font-size: 0.75rem; color: #6b7280;"><?php echo e(Auth::user()->email); ?></div>
                    </div>
                </div>

                <!-- Logout Button -->
                <a href="<?php echo e(route('logout.get')); ?>" class="btn btn-danger btn-sm">
                    <i class="bx bx-log-out me-1"></i> Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid" style="padding: 1.5rem;">
            <?php echo $__env->yieldContent('content'); ?>
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

    <?php echo $__env->make('layouts.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                    post('<?php echo e(url('/attendance/check-in')); ?>', { force_office: 0 })
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
                    post('<?php echo e(url('/attendance/check-out')); ?>')
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

            fetch('<?php echo e(route('api.notifications.topbar')); ?>')
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
            fetch('<?php echo e(route('api.notifications.mark-all-read')); ?>', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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

        // Load saved sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
            }
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

    <?php echo $__env->yieldContent('script'); ?>

    
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
            echo.channel('call')
                .listen('.call.connected', (e) => {
                    console.log('Call Connected Event Received:', e);
                    showCallPopup(e);
                });
        });

        // Example of how to manually trigger for testing:
        // window.showCallPopup({ employee: { id: 1, name: 'John Doe', email: 'john.doe@example.com' } });

    </script>

</body>
</html>
<?php /**PATH C:\code\taurus-crm-master\resources\views/layouts/master.blade.php ENDPATH**/ ?>