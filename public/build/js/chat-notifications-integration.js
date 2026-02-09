/**
 * Chat Notifications Integration
 * Integrates the ChatNotificationManager with the chat application
 */

document.addEventListener('DOMContentLoaded', async function() {
    // Initialize notification manager
    if (window.chatNotificationManager) {
        await window.chatNotificationManager.init();
    }

    // Listen for new messages from Laravel Echo
    if (window.Echo) {
        const userId = window.currentUserId || 
                      document.querySelector('[data-user-id]')?.dataset.userId ||
                      document.querySelector('meta[name="user-id"]')?.content;

        if (userId) {
            // Subscribe to private channel for direct messages
            window.Echo.private(`chat.user.${userId}`)
                .listen('MessageSent', async (event) => {
                    await handleIncomingMessage(event);
                })
                .listen('MessageStatusChanged', (event) => {
                    console.log('Message status changed:', event);
                })
                .error((error) => {
                    console.error('Error listening to messages:', error);
                });
        }
    }

    /**
     * Handle incoming message event
     */
    async function handleIncomingMessage(event) {
        if (!window.chatNotificationManager) {
            return;
        }

        const message = event.message || event;
        const conversation = event.conversation || {};

        // Check if it's a mention
        const currentUsername = window.currentUsername || 
                               document.querySelector('meta[name="user-name"]')?.content ||
                               'User';
        const isMention = message.message && message.message.includes(`@${currentUsername}`);

        // Show notification if should notify
        const shouldNotify = await window.chatNotificationManager.shouldShowNotification(isMention);
        
        if (shouldNotify) {
            await window.chatNotificationManager.showNotification({
                title: conversation.name || message.user?.name || 'New Message',
                body: truncateMessage(message.message, 100),
                tag: `chat-${conversation.id || 'direct'}`,
                data: {
                    conversationId: conversation.id,
                    messageId: message.id,
                    userId: message.user_id,
                },
                requireInteraction: isMention, // Require interaction for mentions
            });
        }
    }

    /**
     * Truncate message to fit in notification
     */
    function truncateMessage(message, maxLength = 100) {
        if (!message) return 'New message';
        if (message.length > maxLength) {
            return message.substring(0, maxLength) + '...';
        }
        return message;
    }

    // Add notification settings button to chat header (if exists)
    addNotificationSettingsButton();
});

/**
 * Add notification settings button to chat header
 */
function addNotificationSettingsButton() {
    // Try to find chat header
    const chatHeader = document.querySelector('.chat-header, [data-chat-header]');
    
    if (!chatHeader) {
        console.log('Chat header not found, notification button not added');
        return;
    }

    // Check if button already exists
    if (document.getElementById('notificationSettingsBtn')) {
        return;
    }

    // Create button
    const btn = document.createElement('button');
    btn.id = 'notificationSettingsBtn';
    btn.className = 'btn btn-icon position-relative';
    btn.type = 'button';
    btn.title = 'Notification Settings';
    btn.setAttribute('data-bs-toggle', 'modal');
    btn.setAttribute('data-bs-target', '#chatNotificationModal');
    btn.innerHTML = '<i class="bx bx-bell"></i>';

    // Insert button into header (try different selectors)
    const headerActions = chatHeader.querySelector('.header-actions, [data-header-actions], .chat-header-actions');
    
    if (headerActions) {
        headerActions.appendChild(btn);
    } else {
        // Insert at the end of header
        chatHeader.appendChild(btn);
    }
}

// Expose notification manager for testing
window.showTestNotification = async function() {
    if (window.chatNotificationManager) {
        await window.chatNotificationManager.showNotification({
            title: 'Taurus CRM',
            body: 'This is a test notification',
            tag: 'test',
        });
    }
};

export default {
    handleIncomingMessage: handleIncomingMessage,
    addNotificationSettingsButton: addNotificationSettingsButton,
};
