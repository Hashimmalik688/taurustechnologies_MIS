<div id="announcement-bar" class="announcement-bar announcement-hidden" style="display: none !important;">
    <div class="announcement-content">
        <i class="announcement-icon"></i>
        <div class="announcement-text">
            <h6 class="mb-0 announcement-title"></h6>
            <p class="mb-0 announcement-message"></p>
        </div>
        @auth
            @if(Auth::user()->hasRole(['Super Admin', 'Co-ordinator']))
                <button type="button" class="btn btn-sm btn-outline-light announcement-edit-btn" style="display: none;">
                    <i class="bx bx-pencil"></i> Edit
                </button>
            @endif
        @endauth
        <button type="button" class="btn-close btn-close-white announcement-close-btn" aria-label="Close"></button>
    </div>
</div>

<!-- Announcement Restore Button - Shows when announcement is hidden -->
<div id="announcement-restore-btn" class="announcement-restore-btn" style="display: none; z-index: 9998;">
    <button type="button" class="btn btn-sm btn-info announcement-restore-btn-toggle" title="Show announcement again">
        <i class="bx bx-up-arrow-circle"></i> Show Announcement
    </button>
</div>

<!-- Edit Announcement Modal -->
@auth
@if(Auth::user()->hasRole(['Super Admin', 'Co-ordinator']))
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAnnouncementForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="announcementTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="announcementTitle" name="title" maxlength="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="announcementMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="announcementMessage" name="message" rows="3" maxlength="500" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="announcementAnimation" class="form-label">Animation</label>
                            <select class="form-select" id="announcementAnimation" name="animation" required>
                                <option value="">Select Animation</option>
                                <option value="slide">Slide</option>
                                <option value="fade">Fade</option>
                                <option value="bounce">Bounce</option>
                                <option value="wave">Wave</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="announcementColor" class="form-label">Background Color</label>
                            <select class="form-select" id="announcementColor" name="background_color" required>
                                <option value="">Select Color</option>
                                <option value="red">Red</option>
                                <option value="yellow">Yellow</option>
                                <option value="blue">Blue</option>
                                <option value="green">Green</option>
                                <option value="purple">Purple</option>
                                <option value="orange">Orange</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="announcementIcon" class="form-label">Icon</label>
                            <select class="form-select" id="announcementIcon" name="icon" required>
                                <option value="">Select Icon</option>
                                <option value="warning">Warning</option>
                                <option value="info">Info</option>
                                <option value="important">Important</option>
                                <option value="star">Star</option>
                                <option value="check">Check</option>
                                <option value="alert">Alert</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="announcementDismiss" class="form-label">Auto Dismiss</label>
                            <select class="form-select" id="announcementDismiss" name="auto_dismiss" required>
                                <option value="">Select Option</option>
                                <option value="never">Never (sticky)</option>
                                <option value="5s">5 Seconds</option>
                                <option value="10s">10 Seconds</option>
                                <option value="30s">30 Seconds</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="announcementActive" name="is_active">
                        <label class="form-check-label" for="announcementActive">
                            Make Active Now
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAnnouncementBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endif
@endauth

<style>
    .announcement-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        margin: 0;
        border-radius: 0;
        animation: slideDown 0.5s ease-out;
        width: 100%;
        background-color: #0d6efd !important;
        color: white !important;
    }

    .announcement-bar.announce-bg-red {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .announcement-bar.announce-bg-yellow {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    .announcement-bar.announce-bg-blue {
        background-color: #0d6efd !important;
        color: white !important;
    }

    .announcement-bar.announce-bg-green {
        background-color: #198754 !important;
        color: white !important;
    }

    .announcement-bar.announce-bg-purple {
        background-color: #6f42c1 !important;
        color: white !important;
    }

    .announcement-bar.announce-bg-orange {
        background-color: #fd7e14 !important;
        color: white !important;
    }

    .announcement-bar:hover .announcement-edit-btn {
        display: inline-block !important;
    }

    .announcement-content {
        padding: 15px 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
        box-sizing: border-box;
        flex-wrap: wrap;
        color: inherit;
    }

    .announcement-text {
        flex: 1 1 auto;
        min-width: 200px;
        color: inherit;
    }

    .announcement-icon {
        font-size: 24px;
        min-width: 24px;
        flex-shrink: 0;
        color: inherit;
    }

    .announcement-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        padding: 0;
        line-height: 1.3;
        color: inherit;
    }

    .announcement-message {
        font-size: 14px;
        opacity: 0.95;
        margin: 4px 0 0 0;
        padding: 0;
        line-height: 1.4;
        color: inherit;
    }

    .announcement-edit-btn {
        white-space: nowrap;
        display: none;
        cursor: pointer;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: white !important;
    }

    .announcement-edit-btn:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    .announcement-bar .btn-close {
        flex-shrink: 0;
        margin-left: auto;
        filter: brightness(0) invert(1);
        opacity: 0.7;
    }

    .announcement-bar .btn-close:hover {
        opacity: 1;
    }

    /* Restore Button */
    .announcement-restore-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9998;
        animation: slideUp 0.3s ease-out;
    }

    .announcement-restore-btn button {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .announcement-restore-btn button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    /* Animations */
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-100%);
            opacity: 0;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes wave {
        0%, 100% {
            transform: skewX(0deg);
        }
        50% {
            transform: skewX(2deg);
        }
    }

    .announce-slide {
        animation: slideDown 0.5s ease-out;
    }

    .announce-fade {
        animation: fadeIn 0.5s ease-out;
    }

    .announce-bounce {
        animation: bounce 0.6s ease-in-out;
    }

    .announce-wave {
        animation: wave 0.5s ease-in-out 3;
    }

    .announcement-hidden {
        display: none !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let announcementBar = document.getElementById('announcement-bar');
    let editModal = null;
    let currentAnnouncementId = null;
    let autoDismissTimer = null;
    let pollInterval = null;
    const DISMISSAL_STORAGE_PREFIX = 'announcement_dismissed_';
    
    // Initialize modal if it exists
    const editModalElement = document.getElementById('editAnnouncementModal');
    if (editModalElement) {
        editModal = new bootstrap.Modal(editModalElement);
    }

    // Use event delegation for close button (works even after DOM replacement)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.announcement-bar .btn-close')) {
            e.preventDefault();
            e.stopPropagation();
            dismissAnnouncement();
        }
        
        // Handle edit button click with event delegation
        if (e.target.closest('.announcement-edit-btn')) {
            e.preventDefault();
            e.stopPropagation();
            if (currentAnnouncementId) {
                // Fetch the current announcement data
                fetch('{{ route("api.announcements.current") }}')
                    .then(response => response.json())
                    .then(announcement => {
                        loadAnnouncementForEdit(announcement);
                        if (editModal) {
                            editModal.show();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading announcement:', error);
                        alert('Error loading announcement for edit');
                    });
            }
            return false;
        }
        
        // Handle restore button click
        if (e.target.closest('.announcement-restore-btn-toggle')) {
            e.preventDefault();
            e.stopPropagation();
            showAnnouncement();
            return false;
        }
    });

    function dismissAnnouncement() {
        announcementBar.classList.add('announcement-hidden');
        announcementBar.style.display = 'none';
        
        // Store dismissal in localStorage so it persists during session
        if (currentAnnouncementId) {
            localStorage.setItem(DISMISSAL_STORAGE_PREFIX + currentAnnouncementId, 'true');
        }
        
        // Show restore button when announcement is dismissed
        const restoreBtn = document.getElementById('announcement-restore-btn');
        if (restoreBtn) {
            restoreBtn.style.display = 'block';
            // Make sure it stays visible by removing any display:none rules
            restoreBtn.style.removeProperty('display');
            restoreBtn.style.display = 'block';
        }
        
        // Clear any pending auto-dismiss timer
        if (autoDismissTimer) {
            clearTimeout(autoDismissTimer);
            autoDismissTimer = null;
        }
    }

    function showAnnouncement() {
        announcementBar.classList.remove('announcement-hidden');
        announcementBar.style.display = 'block';
        announcementBar.style.removeProperty('display');
        announcementBar.style.display = 'block';
        
        // Clear dismissal state when user manually restores
        if (currentAnnouncementId) {
            localStorage.removeItem(DISMISSAL_STORAGE_PREFIX + currentAnnouncementId);
        }
        
        const restoreBtn = document.getElementById('announcement-restore-btn');
        if (restoreBtn) {
            restoreBtn.style.display = 'none';
        }
    }

    function isDismissed(announcementId) {
        return localStorage.getItem(DISMISSAL_STORAGE_PREFIX + announcementId) === 'true';
    }

    // Fetch and display current announcement
    function fetchAnnouncement() {
        fetch('{{ route("api.announcements.current") }}')
            .then(response => response.json())
            .then(announcement => {
                if (announcement) {
                    displayAnnouncement(announcement);
                } else {
                    announcementBar.classList.add('announcement-hidden');
                }
            })
            .catch(error => console.error('Error fetching announcement:', error));
    }

    function displayAnnouncement(announcement) {
        const wasChanged = currentAnnouncementId !== announcement.id;
        currentAnnouncementId = announcement.id;

        // Check if user has dismissed this announcement
        if (isDismissed(announcement.id)) {
            announcementBar.classList.add('announcement-hidden');
            announcementBar.style.display = 'none';
            const restoreBtn = document.getElementById('announcement-restore-btn');
            if (restoreBtn) {
                restoreBtn.style.display = 'block';
            }
            return; // Don't display if user dismissed it
        }

        // Update content
        document.querySelector('.announcement-title').textContent = announcement.title;
        document.querySelector('.announcement-message').textContent = announcement.message;
        document.querySelector('.announcement-icon').className = 'announcement-icon ' + announcement.icon_class;

        // Remove old classes
        announcementBar.className = 'announcement-bar';

        // Add color and animation classes
        announcementBar.classList.add(announcement.background_class, announcement.animation_class);
        announcementBar.style.display = 'block';
        announcementBar.classList.remove('announcement-hidden');

        // Setup edit button visibility for admins
        const editBtn = document.querySelector('.announcement-edit-btn');
        if (editBtn) {
            editBtn.style.display = 'none'; // Hidden by default, shown on hover
        }

        // Add hover listeners to bar itself
        announcementBar.removeEventListener('mouseenter', showEditButton);
        announcementBar.removeEventListener('mouseleave', hideEditButton);
        announcementBar.addEventListener('mouseenter', showEditButton);
        announcementBar.addEventListener('mouseleave', hideEditButton);

        // Clear previous auto-dismiss timer if it exists
        if (autoDismissTimer) {
            clearTimeout(autoDismissTimer);
            autoDismissTimer = null;
        }

        // Auto-dismiss only if setting changed or new announcement
        if (wasChanged && announcement.auto_dismiss !== 'never') {
            const duration = parseInt(announcement.auto_dismiss) * 1000;
            autoDismissTimer = setTimeout(() => {
                dismissAnnouncement();
            }, duration);
        }

        // Hide restore button when announcement is shown
        const restoreBtn = document.getElementById('announcement-restore-btn');
        if (restoreBtn) {
            restoreBtn.style.display = 'none';
        }
    }

    function showEditButton() {
        const editBtn = document.querySelector('.announcement-edit-btn');
        if (editBtn) {
            editBtn.style.display = 'inline-block';
        }
    }

    function hideEditButton() {
        const editBtn = document.querySelector('.announcement-edit-btn');
        if (editBtn) {
            editBtn.style.display = 'none';
        }
    }

    function loadAnnouncementForEdit(announcement) {
        try {
            document.getElementById('announcementTitle').value = announcement.title;
            document.getElementById('announcementMessage').value = announcement.message;
            document.getElementById('announcementAnimation').value = announcement.animation;
            document.getElementById('announcementColor').value = announcement.background_color;
            document.getElementById('announcementIcon').value = announcement.icon;
            document.getElementById('announcementDismiss').value = announcement.auto_dismiss;
            document.getElementById('announcementActive').checked = true;

            // Store the announcement ID for later use
            window.currentAnnouncementId = announcement.id;
        } catch (error) {
            console.error('Error loading announcement for edit:', error);
        }
    }

    function updateAnnouncement(announcementId) {
        const form = document.getElementById('editAnnouncementForm');
        const title = document.getElementById('announcementTitle').value;
        const message = document.getElementById('announcementMessage').value;
        const animation = document.getElementById('announcementAnimation').value;
        const backgroundColor = document.getElementById('announcementColor').value;
        const icon = document.getElementById('announcementIcon').value;
        const autoDismiss = document.getElementById('announcementDismiss').value;
        const isActive = document.getElementById('announcementActive').checked;

        // Create JSON data instead of FormData for better control
        const data = {
            title: title,
            message: message,
            animation: animation,
            background_color: backgroundColor,
            icon: icon,
            auto_dismiss: autoDismiss,
            is_active: isActive ? 1 : 0,
        };

        const url = '{{ route("admin.announcements.update", ":id") }}'.replace(':id', announcementId);

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (editModal) {
                    editModal.hide();
                }
                // Clear dismissal state since announcement changed
                if (currentAnnouncementId) {
                    localStorage.removeItem(DISMISSAL_STORAGE_PREFIX + currentAnnouncementId);
                }
                fetchAnnouncement();
                // Show success message if available
                if (typeof showSuccessToast === 'function') {
                    showSuccessToast('Announcement updated successfully!');
                } else {
                    alert('Announcement updated successfully!');
                }
            } else {
                alert('Error updating announcement: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating announcement:', error);
            alert('Error updating announcement. Please try again.');
        });
    }

    // Save button handler
    const saveBtn = document.getElementById('saveAnnouncementBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Validate form first
            const form = document.getElementById('editAnnouncementForm');
            if (!form) {
                alert('Form not found');
                return;
            }

            // Check if all required fields are filled
            const title = document.getElementById('announcementTitle').value.trim();
            const message = document.getElementById('announcementMessage').value.trim();
            const animation = document.getElementById('announcementAnimation').value;
            const color = document.getElementById('announcementColor').value;
            const icon = document.getElementById('announcementIcon').value;
            const dismiss = document.getElementById('announcementDismiss').value;

            if (!title || !message || !animation || !color || !icon || !dismiss) {
                alert('Please fill in all required fields');
                return;
            }

            // Get the announcement ID that was stored when loading the form
            const announcementId = window.currentAnnouncementId;
            if (!announcementId) {
                alert('Announcement ID not found');
                return;
            }

            // Call update function
            updateAnnouncement(announcementId);
            return false;
        });
    }

    // Load announcement on page load
    fetchAnnouncement();

    // Refresh announcement every 30 seconds
    pollInterval = setInterval(fetchAnnouncement, 30000);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
        if (autoDismissTimer) {
            clearTimeout(autoDismissTimer);
        }
    });
});
</script>
