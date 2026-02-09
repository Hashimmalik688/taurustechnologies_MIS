<!-- Teams-Style Announcement Banner -->
<div id="teams-announcement-banner" class="teams-announcement-banner" style="display: none;">
    <div class="teams-announcement-content">
        <div class="teams-announcement-icon">
            <i class="bx bx-info-circle"></i>
        </div>
        <div class="teams-announcement-body">
            <div class="teams-announcement-title"></div>
            <div class="teams-announcement-message"></div>
        </div>
        <div class="teams-announcement-actions">
            @auth
                @if(Auth::user()->hasRole(['Super Admin', 'Co-ordinator']))
                    <button type="button" class="btn btn-sm btn-light teams-announcement-edit" title="Edit announcement">
                        <i class="bx bx-pencil"></i>
                    </button>
                @endif
            @endauth
            <button type="button" class="btn btn-sm btn-light teams-announcement-close" title="Close announcement">
                <i class="bx bx-x"></i>
            </button>
        </div>
    </div>
</div>

<style>
.teams-announcement-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    z-index: 9999;
    padding: 0.75rem 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    animation: slideDown 0.3s ease-out;
}

.teams-announcement-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    max-width: 1400px;
    margin: 0 auto;
}

.teams-announcement-icon {
    flex-shrink: 0;
    font-size: 1.25rem;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.teams-announcement-body {
    flex: 1;
}

.teams-announcement-title {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.teams-announcement-message {
    font-size: 0.85rem;
    opacity: 0.95;
    line-height: 1.4;
}

.teams-announcement-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.teams-announcement-edit,
.teams-announcement-close {
    background: rgba(255, 255, 255, 0.2) !important;
    border: none !important;
    color: white !important;
    padding: 0.4rem 0.6rem !important;
    font-size: 0.9rem !important;
    transition: all 0.2s ease;
}

.teams-announcement-edit:hover,
.teams-announcement-close:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    transform: scale(1.05);
}

/* Adjust body when banner is shown */
body.teams-announcement-active {
    padding-top: 60px;
}

body.teams-announcement-active #page-content {
    margin-top: 0;
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

/* Responsive */
@media (max-width: 768px) {
    .teams-announcement-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .teams-announcement-body {
        width: 100%;
    }
    
    .teams-announcement-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('teams-announcement-banner');
    const editBtn = banner.querySelector('.teams-announcement-edit');
    const closeBtn = banner.querySelector('.teams-announcement-close');
    let currentAnnouncementId = null;
    let dismissTimeout = null;

    // Fetch and display announcement
    function fetchAnnouncement() {
        fetch('/api/announcements/current')
            .then(response => response.json())
            .then(data => {
                if (data && data.id) {
                    displayAnnouncement(data);
                } else {
                    hideAnnouncement();
                }
            })
            .catch(error => console.log('Announcement fetch error:', error));
    }

    // Display announcement
    function displayAnnouncement(announcement) {
        currentAnnouncementId = announcement.id;
        
        // Check if dismissed in this session
        if (localStorage.getItem(`announcement_dismissed_${announcement.id}`)) {
            hideAnnouncement();
            return;
        }

        // Update content
        banner.querySelector('.teams-announcement-icon i').className = `bx bx-${getIconClass(announcement.icon)}`;
        banner.querySelector('.teams-announcement-title').textContent = announcement.title;
        banner.querySelector('.teams-announcement-message').textContent = announcement.message;

        // Apply color theme
        applyColorTheme(announcement.background_color);

        // Show banner
        banner.style.display = 'block';
        document.body.classList.add('teams-announcement-active');

        // Handle auto-dismiss
        if (announcement.auto_dismiss && announcement.auto_dismiss !== 'never') {
            const timeMs = parseInt(announcement.auto_dismiss) * 1000;
            if (dismissTimeout) clearTimeout(dismissTimeout);
            dismissTimeout = setTimeout(() => {
                dismissAnnouncement();
            }, timeMs);
        }
    }

    // Hide announcement
    function hideAnnouncement() {
        banner.style.display = 'none';
        document.body.classList.remove('teams-announcement-active');
        if (dismissTimeout) clearTimeout(dismissTimeout);
    }

    // Dismiss announcement
    function dismissAnnouncement() {
        if (currentAnnouncementId) {
            localStorage.setItem(`announcement_dismissed_${currentAnnouncementId}`, 'true');
        }
        banner.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => {
            hideAnnouncement();
            banner.style.animation = 'slideDown 0.3s ease-out';
        }, 300);
    }

    // Icon mapping
    function getIconClass(icon) {
        const iconMap = {
            'important': 'exclamation-circle',
            'warning': 'alert-circle',
            'info': 'info-circle',
            'check': 'check-circle',
            'star': 'star',
            'alert': 'error-circle'
        };
        return iconMap[icon] || 'info-circle';
    }

    // Apply color theme
    function applyColorTheme(color) {
        const colors = {
            'blue': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'red': 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
            'green': 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'yellow': 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            'purple': 'linear-gradient(135deg, #a855f7 0%, #9333ea 100%)',
            'orange': 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)'
        };
        banner.style.background = colors[color] || colors['blue'];
    }

    // Event listeners
    closeBtn.addEventListener('click', dismissAnnouncement);
    
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            // Open edit modal (implement with your existing modal system)
            const modal = new bootstrap.Modal(document.getElementById('editAnnouncementModal'));
            modal.show();
        });
    }

    // Initial fetch and polling
    fetchAnnouncement();
    setInterval(fetchAnnouncement, 30000); // Poll every 30 seconds

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (dismissTimeout) clearTimeout(dismissTimeout);
    });
});
</script>
