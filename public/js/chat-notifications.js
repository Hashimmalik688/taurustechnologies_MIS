// Global Chat Desktop Notifications
// Polls for new messages and shows native OS (Windows/Mac/Linux) notifications
// Works on ALL pages as long as a CRM tab is open

(function() {
    'use strict';

    // ─── Permission ───────────────────────────────────────────
    if ('Notification' in window && Notification.permission === 'default') {
        setTimeout(function() { Notification.requestPermission(); }, 3000);
    }

    // ─── Sound (Web Audio API - reliable cross-browser) ──────
    function playNotificationSound() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            // First beep
            var osc1 = ctx.createOscillator();
            var gain1 = ctx.createGain();
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.frequency.value = 880; // A5 note
            osc1.type = 'sine';
            gain1.gain.setValueAtTime(0.3, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc1.start(ctx.currentTime);
            osc1.stop(ctx.currentTime + 0.15);

            // Second beep (slightly higher, after a short pause)
            var osc2 = ctx.createOscillator();
            var gain2 = ctx.createGain();
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.frequency.value = 1100; // ~C#6 note
            osc2.type = 'sine';
            gain2.gain.setValueAtTime(0.3, ctx.currentTime + 0.18);
            gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
            osc2.start(ctx.currentTime + 0.18);
            osc2.stop(ctx.currentTime + 0.35);

            // Cleanup
            setTimeout(function() { ctx.close(); }, 500);
        } catch(e) {
            // Fallback: try Audio element
            try {
                var a = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjqP1fPPgjMGKHi+7+OZURE=');
                a.volume = 0.3;
                a.play().catch(function(){});
            } catch(e2) {}
        }
    }

    // ─── Track which messages we already notified about ───────
    var notifiedIds = {};
    try {
        var stored = sessionStorage.getItem('chatNotifiedIds');
        if (stored) notifiedIds = JSON.parse(stored);
    } catch(e) {}

    function saveNotifiedIds() {
        try {
            // Keep only last 100 IDs to prevent storage bloat
            var keys = Object.keys(notifiedIds);
            if (keys.length > 100) {
                var sorted = keys.sort(function(a,b){ return a - b; });
                for (var i = 0; i < sorted.length - 100; i++) {
                    delete notifiedIds[sorted[i]];
                }
            }
            sessionStorage.setItem('chatNotifiedIds', JSON.stringify(notifiedIds));
        } catch(e) {}
    }

    // ─── Poll timestamp ──────────────────────────────────────
    var lastPollTime = null;

    // ─── Check if user is viewing the conversation on chat page ─
    function isViewingConversation(conversationId) {
        // window.currentConversationId is set by the chat page when a conversation is open
        if (window.currentConversationId && window.currentConversationId == conversationId) {
            // Also check if the window/tab is focused
            if (document.hasFocus()) {
                return true;
            }
        }
        return false;
    }

    // ─── Show a single desktop notification ──────────────────
    function showDesktopNotification(senderName, message, conversationName, conversationId) {
        // Skip if user is actively viewing this conversation
        if (isViewingConversation(conversationId)) return;

        // Play sound
        playNotificationSound();

        // Update the chat badge immediately
        if (typeof loadChatUnreadCount === 'function') {
            loadChatUnreadCount();
        }

        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        var truncated = message && message.length > 120 ? message.substring(0, 120) + '...' : (message || 'New message');

        try {
            var notification = new Notification((senderName || 'Someone') + ' - Taurus CRM', {
                body: (conversationName ? conversationName + ': ' : '') + truncated,
                icon: '/images/favicon.ico',
                badge: '/images/favicon.ico',
                tag: 'chat-' + (conversationId || Date.now()),
                requireInteraction: false
            });

            notification.onclick = function() {
                window.focus();
                window.location.href = '/chat' + (conversationId ? '?open=' + conversationId : '');
                notification.close();
            };

            setTimeout(function() { notification.close(); }, 6000);
        } catch(e) {}
    }

    // ─── Poll for new messages ───────────────────────────────
    function pollNewMessages() {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) return; // Not logged in / no CSRF

        var url = '/api/chat/new-messages';
        if (lastPollTime) {
            url += '?since=' + encodeURIComponent(lastPollTime);
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfMeta.content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) return;

            if (data.server_time) lastPollTime = data.server_time;

            var messages = data.messages || [];
            for (var i = 0; i < messages.length; i++) {
                var msg = messages[i];
                if (notifiedIds[msg.id]) continue; // Already notified

                notifiedIds[msg.id] = true;

                // Update chat page UI (sidebar + open conversation) if on chat page
                if (typeof window._chatUIUpdate === 'function') {
                    window._chatUIUpdate(msg);
                }

                showDesktopNotification(
                    msg.sender_name,
                    msg.message,
                    msg.conversation_name,
                    msg.conversation_id
                );
            }

            if (messages.length > 0) saveNotifiedIds();
        })
        .catch(function() { /* silent */ });
    }

    // ─── Start polling: first after 2s, then every 4s ────────
    setTimeout(pollNewMessages, 2000);
    setInterval(pollNewMessages, 4000);

    // ─── Public API (for chat page inline JS) ────────────────
    window.ChatNotify = {
        show: showDesktopNotification,
        playSound: playNotificationSound
    };

    window.ChatToast = {
        show: function(message, conversationId, conversationName, senderId, senderName) {
            if (window.currentUserId && senderId == window.currentUserId) return;
            showDesktopNotification(senderName, message, conversationName, conversationId);
        },
        dismiss: function() {},
        openChat: function() { window.location.href = '/chat'; }
    };

})();
