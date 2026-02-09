@extends('layouts.master')

@section('title')
    Chat Notification Settings
@endsection

@section('css')
    @vite(['resources/css/chat.css'])
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="bx bx-bell me-2"></i>
                    <h5 class="mb-0">Chat Notification Settings</h5>
                </div>
                
                <div class="card-body">
                    <form id="notificationPreferencesForm">
                        @csrf
                        
                        <!-- Message Notifications -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifyOnMessage" 
                                    name="notify_on_message" {{ $preferences->notify_on_message ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyOnMessage">
                                    <strong>Notify me of new messages</strong>
                                    <br>
                                    <small class="text-muted">Receive notifications for all incoming messages</small>
                                </label>
                            </div>
                        </div>

                        <!-- Mention Notifications -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifyOnMention" 
                                    name="notify_on_mention" {{ $preferences->notify_on_mention ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyOnMention">
                                    <strong>Notify me when mentioned</strong>
                                    <br>
                                    <small class="text-muted">Receive notifications only when someone mentions you (@username)</small>
                                </label>
                            </div>
                        </div>

                        <!-- Desktop Notifications -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifyDesktop" 
                                    name="notify_desktop" {{ $preferences->notify_desktop ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyDesktop">
                                    <strong>Enable desktop notifications</strong>
                                    <br>
                                    <small class="text-muted">Show notifications in your system notification panel</small>
                                </label>
                            </div>
                        </div>

                        <!-- Sound Notifications -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifySoundEnabled" 
                                    name="notify_sound_enabled" {{ $preferences->notify_sound_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifySoundEnabled">
                                    <strong>Play notification sound</strong>
                                    <br>
                                    <small class="text-muted">Play a sound when new messages arrive</small>
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Quiet Hours -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="quietHoursEnabled" 
                                    name="quiet_hours_enabled" {{ $preferences->quiet_hours_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="quietHoursEnabled">
                                    <strong>Enable quiet hours</strong>
                                    <br>
                                    <small class="text-muted">Disable notifications during specified time period</small>
                                </label>
                            </div>
                        </div>

                        <!-- Quiet Hours Times -->
                        <div id="quietHoursSection" style="display: {{ $preferences->quiet_hours_enabled ? 'block' : 'none' }};" class="ms-4 mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="quietHoursStart" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="quietHoursStart" 
                                        name="quiet_hours_start" value="{{ $preferences->quiet_hours_start }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="quietHoursEnd" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="quietHoursEnd" 
                                        name="quiet_hours_end" value="{{ $preferences->quiet_hours_end }}">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle"></i>
                                Notifications will be disabled between the specified times
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Test Notification -->
                        <div class="mb-4">
                            <button type="button" id="testNotificationBtn" class="btn btn-outline-primary">
                                <i class="bx bx-test-tube me-1"></i>
                                Test Notification
                            </button>
                            <small class="text-muted d-block mt-2">
                                Click to send yourself a test notification
                            </small>
                        </div>

                        <!-- Save Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>
                                Save Preferences
                            </button>
                            <a href="{{ url('/chat') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                        <div id="successMessage" class="alert alert-success mt-3" style="display: none;">
                            <i class="bx bx-check-circle me-2"></i>
                            <span id="successText">Preferences saved successfully!</span>
                        </div>

                        <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;">
                            <i class="bx bx-x-circle me-2"></i>
                            <span id="errorText">An error occurred while saving preferences</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quietHoursEnabledCheckbox = document.getElementById('quietHoursEnabled');
    const quietHoursSection = document.getElementById('quietHoursSection');
    const notificationForm = document.getElementById('notificationPreferencesForm');
    const testNotificationBtn = document.getElementById('testNotificationBtn');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    // Toggle quiet hours section visibility
    quietHoursEnabledCheckbox.addEventListener('change', function() {
        quietHoursSection.style.display = this.checked ? 'block' : 'none';
    });

    // Handle form submission
    notificationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Convert checkboxes to boolean
        data.notify_on_message = document.getElementById('notifyOnMessage').checked;
        data.notify_on_mention = document.getElementById('notifyOnMention').checked;
        data.notify_sound_enabled = document.getElementById('notifySoundEnabled').checked;
        data.notify_desktop = document.getElementById('notifyDesktop').checked;
        data.quiet_hours_enabled = document.getElementById('quietHoursEnabled').checked;

        fetch('/api/chat/notifications/preferences', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccessMessage('Preferences saved successfully!');
            } else {
                showErrorMessage(result.message || 'Failed to save preferences');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('An error occurred while saving preferences');
        });
    });

    // Test notification button
    testNotificationBtn.addEventListener('click', function() {
        if (!('Notification' in window)) {
            alert('Your browser does not support notifications');
            return;
        }

        if (Notification.permission === 'granted') {
            sendTestNotification();
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    sendTestNotification();
                }
            });
        }
    });

    function sendTestNotification() {
        const notification = new Notification('Taurus CRM Chat', {
            icon: '/images/logo.png',
            badge: '/images/logo.png',
            body: 'This is a test notification from your chat settings',
            tag: 'test-notification',
            requireInteraction: false,
        });

        notification.onclick = function() {
            window.focus();
            this.close();
        };
    }

    function showSuccessMessage(message) {
        document.getElementById('successText').textContent = message;
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);
    }

    function showErrorMessage(message) {
        document.getElementById('errorText').textContent = message;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    }
});
</script>
@endsection
