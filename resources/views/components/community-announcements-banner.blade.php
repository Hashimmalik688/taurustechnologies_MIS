<!-- Community Announcements Banner - Auto-rotating announcements from user's communities -->
<div id="community-announcements-banner" class="community-announcements-banner" style="display: none;">
    <div class="announcements-container">
        <div class="announcements-carousel">
            <!-- Individual announcements will be added here -->
        </div>
        <div class="announcements-controls">
            <button class="control-btn prev-btn" title="Previous announcement">
                <i class="bx bx-chevron-left"></i>
            </button>
            <div class="announcements-dots"></div>
            <button class="control-btn next-btn" title="Next announcement">
                <i class="bx bx-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<style>
.community-announcements-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    z-index: 9998;
    padding: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.announcements-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.announcements-carousel {
    flex: 1;
    overflow: hidden;
    position: relative;
    min-height: 80px;
    padding: 15px 20px;
}

.announcement-item {
    display: none;
    animation: slideIn 0.4s ease-out;
}

.announcement-item.active {
    display: flex;
    align-items: center;
    gap: 15px;
}

.announcement-item-icon {
    font-size: 28px;
    min-width: 28px;
    flex-shrink: 0;
}

.announcement-item-content {
    flex: 1;
}

.announcement-item-community {
    font-size: 0.75rem;
    opacity: 0.85;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 2px;
}

.announcement-item-title {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 3px;
}

.announcement-item-message {
    font-size: 0.85rem;
    opacity: 0.9;
    line-height: 1.4;
    max-width: 600px;
}

.announcement-item-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.2s ease;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.announcement-item-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.announcements-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-right: 20px;
    flex-shrink: 0;
}

.control-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 1.1rem;
}

.control-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.05);
}

.control-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.announcements-dots {
    display: flex;
    gap: 6px;
    min-width: 40px;
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    transition: all 0.2s ease;
}

.dot.active {
    background: white;
    width: 24px;
    border-radius: 4px;
}

.dot:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Color variations by priority */
.community-announcements-banner.priority-urgent {
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
}

.community-announcements-banner.priority-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.community-announcements-banner.priority-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(-20px);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .announcements-container {
        flex-wrap: wrap;
    }

    .announcements-carousel {
        padding: 12px 10px;
        min-height: auto;
        width: 100%;
    }

    .announcement-item-message {
        display: none;
    }

    .announcements-controls {
        padding-right: 10px;
        width: 100%;
        justify-content: center;
        margin-top: 8px;
    }

    .control-btn {
        width: 28px;
        height: 28px;
        font-size: 0.9rem;
    }

    .dot {
        width: 6px;
        height: 6px;
    }

    .dot.active {
        width: 18px;
    }
}

/* Hide banner if no announcements */
.community-announcements-banner.empty {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('community-announcements-banner');
    const carousel = banner.querySelector('.announcements-carousel');
    const dotsContainer = banner.querySelector('.announcements-dots');
    const prevBtn = banner.querySelector('.prev-btn');
    const nextBtn = banner.querySelector('.next-btn');
    
    let announcements = [];
    let currentIndex = 0;
    let autoRotateInterval = null;
    const ROTATION_INTERVAL = 10000; // 10 seconds

    /**
     * Fetch user's community announcements
     */
    function fetchAnnouncements() {
        fetch('/api/chat/user/community-announcements')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.announcements.length > 0) {
                    announcements = data.announcements;
                    renderAnnouncements();
                    startAutoRotate();
                    banner.classList.remove('empty');
                    banner.style.display = 'block';
                } else {
                    hideAnnouncements();
                }
            })
            .catch(error => {
                console.log('Error fetching announcements:', error);
                hideAnnouncements();
            });
    }

    /**
     * Render all announcements in carousel
     */
    function renderAnnouncements() {
        carousel.innerHTML = '';
        dotsContainer.innerHTML = '';

        announcements.forEach((announcement, index) => {
            // Create announcement item
            const item = document.createElement('div');
            item.className = 'announcement-item' + (index === 0 ? ' active' : '');
            item.innerHTML = `
                <div class="announcement-item-icon">
                    <i class="bx bx-${announcement.priority_icon}"></i>
                </div>
                <div class="announcement-item-content">
                    <div class="announcement-item-community">${announcement.community_name}</div>
                    <div class="announcement-item-title">${announcement.title}</div>
                    <div class="announcement-item-message">${announcement.message}</div>
                    <small style="opacity: 0.75;">Posted ${announcement.created_at}</small>
                </div>
                <button type="button" class="announcement-item-close" title="Close this announcement">
                    <i class="bx bx-x"></i>
                </button>
            `;
            carousel.appendChild(item);

            // Create dot
            const dot = document.createElement('div');
            dot.className = 'dot' + (index === 0 ? ' active' : '');
            dot.onclick = () => goToSlide(index);
            dotsContainer.appendChild(dot);

            // Close button listener
            item.querySelector('.announcement-item-close').addEventListener('click', () => {
                dismissAnnouncement(announcement.id);
            });
        });

        updateControlsState();
    }

    /**
     * Show specific slide
     */
    function goToSlide(index) {
        if (index >= 0 && index < announcements.length) {
            currentIndex = index;
            updateSlide();
            resetAutoRotate();
        }
    }

    /**
     * Update slide display
     */
    function updateSlide() {
        const items = carousel.querySelectorAll('.announcement-item');
        const dots = dotsContainer.querySelectorAll('.dot');

        items.forEach((item, index) => {
            item.classList.toggle('active', index === currentIndex);
        });

        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });

        // Update banner color based on priority
        const announcement = announcements[currentIndex];
        banner.className = `community-announcements-banner priority-${announcement.priority}`;

        updateControlsState();
    }

    /**
     * Update button states
     */
    function updateControlsState() {
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex === announcements.length - 1;
    }

    /**
     * Next announcement
     */
    nextBtn.addEventListener('click', () => {
        if (currentIndex < announcements.length - 1) {
            goToSlide(currentIndex + 1);
        }
    });

    /**
     * Previous announcement
     */
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            goToSlide(currentIndex - 1);
        }
    });

    /**
     * Auto-rotate announcements
     */
    function startAutoRotate() {
        autoRotateInterval = setInterval(() => {
            if (currentIndex < announcements.length - 1) {
                goToSlide(currentIndex + 1);
            } else {
                goToSlide(0);
            }
        }, ROTATION_INTERVAL);
    }

    /**
     * Reset auto-rotate timer
     */
    function resetAutoRotate() {
        if (autoRotateInterval) {
            clearInterval(autoRotateInterval);
            startAutoRotate();
        }
    }

    /**
     * Dismiss announcement
     */
    function dismissAnnouncement(announcementId) {
        announcements = announcements.filter(a => a.id !== announcementId);
        
        if (announcements.length === 0) {
            hideAnnouncements();
        } else {
            if (currentIndex >= announcements.length) {
                currentIndex = announcements.length - 1;
            }
            renderAnnouncements();
            updateSlide();
        }
    }

    /**
     * Hide banner
     */
    function hideAnnouncements() {
        banner.classList.add('empty');
        banner.style.display = 'none';
        if (autoRotateInterval) {
            clearInterval(autoRotateInterval);
        }
    }

    // Initial fetch and polling
    fetchAnnouncements();
    setInterval(fetchAnnouncements, 60000); // Poll every 60 seconds

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (autoRotateInterval) clearInterval(autoRotateInterval);
    });
});
</script>
