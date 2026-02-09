// Global Chat Notifications - Works on all pages
// This file handles real-time chat notifications across the entire application

// Chat toast notifications - Global
window.ChatToast = {
    show: function(message, conversationId, conversationName, senderId, senderName) {
        // Don't show notification for own messages
        if (window.currentUserId && senderId === window.currentUserId) return;
        
        // Show desktop notification if enabled and permitted
        this.showDesktopNotification(senderName, message, conversationName);
        
        let container = document.getElementById('chatToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'chatToastContainer';
            container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:10000;max-width:350px;';
            document.body.appendChild(container);
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'chat-toast-notification';
        toast.style.cssText = 'background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.15);padding:15px;margin-top:10px;animation:slideIn 0.3s ease;cursor:pointer;border-left:4px solid #556ee6;';
        
        const initial = senderName ? senderName.charAt(0).toUpperCase() : '?';
        const truncatedMsg = message && message.length > 60 ? message.substring(0, 60) + '...' : (message || '');
        
        toast.innerHTML = `
            <div style="display:flex;align-items:start;gap:10px;">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:16px;flex-shrink:0;">
                    ${initial}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:14px;color:#2c3e50;margin-bottom:2px;">${senderName || 'Unknown'}</div>
                    <div style="font-size:13px;color:#7f8c8d;margin-bottom:2px;font-style:italic;">${conversationName || 'Chat'}</div>
                    <div style="font-size:13px;color:#34495e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${truncatedMsg}</div>
                    <div style="margin-top:8px;display:flex;gap:8px;">
                        <button onclick="ChatToast.openChat('${toastId}', ${conversationId}, '${(conversationName || '').replace(/'/g, "\\'")}')" style="background:#556ee6;color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:12px;cursor:pointer;font-weight:500;">Open Chat</button>
                        <button onclick="ChatToast.dismiss('${toastId}')" style="background:#f1f3f4;color:#5a6169;border:none;padding:5px 12px;border-radius:6px;font-size:12px;cursor:pointer;font-weight:500;">Dismiss</button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(toast);
        setTimeout(() => ChatToast.dismiss(toastId), 10000);
        
        // Play notification sound
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNw==');
            audio.volume = 0.3;
            audio.play().catch(() => {});
        } catch(e) {
            console.warn('Could not play notification sound:', e);
        }
    },
    
    showDesktopNotification: function(senderName, message, conversationName) {
        // Check if Notification API is supported and permission is granted
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                const truncatedMsg = message && message.length > 100 ? message.substring(0, 100) + '...' : (message || 'New message');
                const notification = new Notification(`New message from ${senderName || 'Someone'}`, {
                    body: `${conversationName || 'Chat'}: ${truncatedMsg}`,
                    icon: '/images/favicon.ico',
                    badge: '/images/favicon.ico',
                    tag: `chat-${Date.now()}`,
                    requireInteraction: false
                });
                
                // Click handler to focus window and open chat
                notification.onclick = function() {
                    window.focus();
                    // Redirect to chat page
                    window.location.href = '/chat';
                    notification.close();
                };
                
                // Auto-close after 8 seconds
                setTimeout(() => notification.close(), 8000);
            } catch (e) {
                console.warn('Failed to show desktop notification:', e);
            }
        }
    },
    
    openChat: function(toastId, conversationId, conversationName) {
        // Navigate to chat page
        window.location.href = '/chat';
        this.dismiss(toastId);
    },
    
    dismiss: function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }
    }
};

// Add animation styles for toast notifications
if (!document.getElementById('chatToastStyles')) {
    const style = document.createElement('style');
    style.id = 'chatToastStyles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .chat-toast-notification:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }
    `;
    document.head.appendChild(style);
}

// Listen for message events from Laravel Echo (when Echo is available)
function setupGlobalChatListener() {
    if (typeof window.Echo !== 'undefined' && window.Echo) {
        try {
            // Get current user ID
            const currentUserId = window.currentUserId || (typeof Laravel !== 'undefined' ? Laravel.userId : null);
            
            if (currentUserId) {
                console.log('✅ Setting up global chat listener for user:', currentUserId);
                
                // Listen on the user's private channel for new messages
                window.Echo.private(`users.${currentUserId}`)
                    .listen('MessageSent', (e) => {
                        console.log('📩 New message received:', e);
                        
                        // Show toast notification
                        if (e.message && e.conversation) {
                            window.ChatToast.show(
                                e.message.message,
                                e.conversation.id,
                                e.conversation.name || e.conversation.title || 'Chat',
                                e.message.user_id,
                                e.message.user?.name || 'Someone'
                            );
                        }
                    });
                    
                console.log('✅ Global chat listener registered successfully');
            } else {
                console.warn('⚠️ Cannot setup chat listener - user ID not found');
            }
        } catch (error) {
            console.error('❌ Error setting up global chat listener:', error);
        }
    } else {
        console.warn('⚠️ Laravel Echo not available - chat notifications will not work in real-time');
    }
}

// Try to setup listener when Echo becomes available
if (typeof window.Echo !== 'undefined' && window.Echo) {
    setupGlobalChatListener();
} else {
    // Wait for Echo to be initialized
    let echoCheckAttempts = 0;
    const echoCheckInterval = setInterval(() => {
        echoCheckAttempts++;
        if (typeof window.Echo !== 'undefined' && window.Echo) {
            clearInterval(echoCheckInterval);
            setupGlobalChatListener();
        } else if (echoCheckAttempts > 50) { // Stop after 10 seconds (50 * 200ms)
            clearInterval(echoCheckInterval);
            console.warn('⚠️ Echo not initialized after 10 seconds - real-time chat notifications will not work');
        }
    }, 200);
}

console.log('✅ Global chat notifications initialized');
