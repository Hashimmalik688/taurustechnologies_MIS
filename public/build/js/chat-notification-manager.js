/**
 * Chat Notification Manager
 * Handles browser notifications for incoming chat messages
 */
class ChatNotificationManager {
    constructor() {
        this.preferences = null;
        this.audioContext = null;
        this.initialized = false;
    }

    /**
     * Initialize the notification manager
     */
    async init() {
        if (this.initialized) return;
        
        try {
            // Request notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                await Notification.requestPermission();
            }

            // Load user preferences
            await this.loadPreferences();
            
            // Register service worker for push notifications
            if ('serviceWorker' in navigator) {
                try {
                    const registration = await navigator.serviceWorker.register('/js/service-worker.js');
                    console.log('Service Worker registered:', registration);
                } catch (error) {
                    console.warn('Service Worker registration failed:', error);
                }
            }

            this.initialized = true;
            console.log('ChatNotificationManager initialized');
        } catch (error) {
            console.error('Failed to initialize ChatNotificationManager:', error);
        }
    }

    /**
     * Load user notification preferences
     */
    async loadPreferences() {
        try {
            const response = await fetch('/api/chat/notifications/preferences', {
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            if (data.success) {
                this.preferences = data.preferences;
            }
        } catch (error) {
            console.error('Failed to load notification preferences:', error);
        }
    }

    /**
     * Check if notifications should be shown
     */
    async shouldShowNotification(isMention = false) {
        if (!this.preferences) {
            await this.loadPreferences();
        }

        if (!this.preferences) {
            return true;
        }

        // Check if notifications are enabled for this type
        if (isMention && !this.preferences.notify_on_mention) {
            return false;
        }

        if (!isMention && !this.preferences.notify_on_message) {
            return false;
        }

        // Check if in quiet hours
        if (this.isInQuietHours()) {
            return false;
        }

        // Check if desktop notifications are enabled
        if (!this.preferences.notify_desktop) {
            return false;
        }

        return true;
    }

    /**
     * Check if current time is within quiet hours
     */
    isInQuietHours() {
        if (!this.preferences || !this.preferences.quiet_hours_enabled) {
            return false;
        }

        const now = new Date();
        const currentTime = String(now.getHours()).padStart(2, '0') + ':' + 
                           String(now.getMinutes()).padStart(2, '0');
        
        const start = this.preferences.quiet_hours_start;
        const end = this.preferences.quiet_hours_end;

        if (start < end) {
            // Normal case: 08:00 - 22:00
            return currentTime >= start && currentTime < end;
        } else {
            // Overnight case: 22:00 - 08:00
            return currentTime >= start || currentTime < end;
        }
    }

    /**
     * Show desktop notification
     */
    async showNotification(options = {}) {
        if (!('Notification' in window)) {
            console.warn('Browser does not support notifications');
            return;
        }

        if (Notification.permission !== 'granted') {
            console.warn('Notification permission not granted');
            return;
        }

        const {
            title = 'Taurus CRM',
            body = 'New message',
            icon = '/images/logo.png',
            badge = '/images/logo.png',
            tag = 'chat-notification',
            requireInteraction = false,
            data = {},
        } = options;

        try {
            const notification = new Notification(title, {
                icon,
                badge,
                body,
                tag,
                requireInteraction,
                data,
            });

            notification.onclick = () => {
                window.focus();
                if (data.conversationId) {
                    window.location.href = `/chat?conversation=${data.conversationId}`;
                }
                notification.close();
            };

            // Play notification sound if enabled
            if (this.preferences?.notify_sound_enabled) {
                this.playNotificationSound();
            }
        } catch (error) {
            console.error('Failed to show notification:', error);
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            // Create a simple beep sound using Web Audio API
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            const ctx = this.audioContext;
            const oscillator = ctx.createOscillator();
            const gainNode = ctx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(ctx.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, ctx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);

            oscillator.start(ctx.currentTime);
            oscillator.stop(ctx.currentTime + 0.1);
        } catch (error) {
            console.warn('Failed to play notification sound:', error);
            // Fallback: try to play audio file if available
            this.playAudioFile();
        }
    }

    /**
     * Play notification audio file (fallback)
     */
    playAudioFile() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(err => console.warn('Failed to play audio file:', err));
        } catch (error) {
            console.warn('Failed to play audio file:', error);
        }
    }

    /**
     * Handle incoming message notification
     */
    async handleNewMessage(message, conversation) {
        const shouldNotify = await this.shouldShowNotification(message.isMention);
        
        if (!shouldNotify) {
            return;
        }

        await this.showNotification({
            title: conversation.name || 'New Message',
            body: message.preview || message.message,
            tag: `chat-${conversation.id}`,
            data: {
                conversationId: conversation.id,
                messageId: message.id,
            },
        });
    }

    /**
     * Update preferences (called when user changes settings)
     */
    async updatePreferences() {
        await this.loadPreferences();
    }

    /**
     * Request notification permission
     */
    static async requestPermission() {
        if (!('Notification' in window)) {
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission === 'denied') {
            return false;
        }

        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }

    /**
     * Get notification permission status
     */
    static getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatNotificationManager;
}

// Create global instance
window.chatNotificationManager = new ChatNotificationManager();
