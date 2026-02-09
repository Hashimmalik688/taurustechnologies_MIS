<!-- Chat Notification Preferences Modal -->
<div class="modal fade" id="chatNotificationModal" tabindex="-1" aria-labelledby="chatNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="chatNotificationModalLabel">
                    <i class="bx bx-bell"></i> Notification Preferences
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Loading State -->
                <div id="notificationLoadingState" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading preferences...</p>
                </div>

                <!-- Content -->
                <div id="notificationModalContent" style="display: none;">
                    <!-- Notification Permission Status -->
                    <div id="permissionAlert" class="alert alert-info" style="display: none;">
                        <i class="bx bx-info-circle"></i>
                        <small id="permissionAlertText">
                            Enable notifications in your browser settings to receive desktop notifications
                        </small>
                        <button type="button" class="btn btn-sm btn-outline-primary float-end" 
                            id="enableNotificationsBtn" style="display: none;">
                            Enable
                        </button>
                    </div>

                    <!-- Quick Toggles -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                id="quickNotifyMessage" data-setting="notify_on_message">
                            <label class="form-check-label" for="quickNotifyMessage">
                                Notify on all messages
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                id="quickNotifyMention" data-setting="notify_on_mention">
                            <label class="form-check-label" for="quickNotifyMention">
                                Notify on mentions only
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                id="quickNotifySound" data-setting="notify_sound_enabled">
                            <label class="form-check-label" for="quickNotifySound">
                                Play notification sound
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                id="quickNotifyDesktop" data-setting="notify_desktop">
                            <label class="form-check-label" for="quickNotifyDesktop">
                                Desktop notifications
                            </label>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="testNotificationModalBtn">
                            <i class="bx bx-test-tube"></i> Test Notification
                        </button>
                        <a href="{{ url('/chat/notification-settings') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-cog"></i> Full Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Settings Header Button -->
<style>
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }

    .notification-badge.hidden {
        display: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const modal = document.getElementById('chatNotificationModal');
    const modalContent = document.getElementById('notificationModalContent');
    const loadingState = document.getElementById('notificationLoadingState');
    const permissionAlert = document.getElementById('permissionAlert');
    const enableNotificationsBtn = document.getElementById('enableNotificationsBtn');
    const testNotificationBtn = document.getElementById('testNotificationModalBtn');
    const notificationToggles = document.querySelectorAll('.notification-toggle');

    // Load preferences when modal is shown
    if (modal) {
        modal.addEventListener('show.bs.modal', async function() {
            await loadPreferences();
        });
    }

    // Enable notifications button
    if (enableNotificationsBtn) {
        enableNotificationsBtn.addEventListener('click', async function() {
            const permission = await window.chatNotificationManager.constructor.requestPermission();
            if (permission) {
                permissionAlert.style.display = 'none';
            }
        });
    }

    // Test notification button
    if (testNotificationButton) {
        testNotificationBtn.addEventListener('click', async function() {
            await window.chatNotificationManager.showNotification({
                title: 'Taurus CRM Chat',
                body: 'This is a test notification',
                tag: 'test-notification',
            });
        });
    }

    // Handle toggle changes
    notificationToggles.forEach(toggle => {
        toggle.addEventListener('change', async function() {
            const setting = this.dataset.setting;
            const value = this.checked;

            try {
                const response = await fetch('/api/chat/notifications/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('input[name="_token"]')?.value || 
                                       document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({
                        [setting]: value,
                    }),
                });

                const data = await response.json();
                if (data.success) {
                    // Update notification manager preferences
                    if (window.chatNotificationManager) {
                        await window.chatNotificationManager.updatePreferences();
                    }
                }
            } catch (error) {
                console.error('Failed to update preferences:', error);
                this.checked = !value; // Revert on error
            }
        });
    });

    async function loadPreferences() {
        try {
            loadingState.style.display = 'block';
            modalContent.style.display = 'none';

            const response = await fetch('/api/chat/notifications/preferences', {
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            if (data.success && data.preferences) {
                const prefs = data.preferences;

                // Update toggles
                document.getElementById('quickNotifyMessage').checked = prefs.notify_on_message;
                document.getElementById('quickNotifyMention').checked = prefs.notify_on_mention;
                document.getElementById('quickNotifySound').checked = prefs.notify_sound_enabled;
                document.getElementById('quickNotifyDesktop').checked = prefs.notify_desktop;

                // Check notification permission
                const permissionStatus = window.chatNotificationManager?.constructor?.getPermissionStatus?.();
                if (permissionStatus === 'denied') {
                    permissionAlert.style.display = 'block';
                    document.getElementById('permissionAlertText').textContent = 
                        'Notifications are blocked in your browser. Please enable them in settings.';
                    enableNotificationsBtn.style.display = 'none';
                } else if (permissionStatus === 'default') {
                    permissionAlert.style.display = 'block';
                    document.getElementById('permissionAlertText').textContent = 
                        'Click below to enable notifications';
                    enableNotificationsBtn.style.display = 'inline-block';
                } else {
                    permissionAlert.style.display = 'none';
                }

                loadingState.style.display = 'none';
                modalContent.style.display = 'block';
            }
        } catch (error) {
            console.error('Failed to load preferences:', error);
            loadingState.innerHTML = '<p class="text-danger">Failed to load preferences</p>';
        }
    }
});
</script>
