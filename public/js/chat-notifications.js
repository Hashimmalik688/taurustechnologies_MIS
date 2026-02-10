// Global Chat Desktop Notifications
// Polls for new messages and shows native OS (Windows/Mac/Linux) notifications
// Works on ALL pages as long as a CRM tab is open

(function() {
    'use strict';

    // ─── Permission ───────────────────────────────────────────
    if ('Notification' in window && Notification.permission === 'default') {
        setTimeout(function() { Notification.requestPermission(); }, 3000);
    }

    // ─── Sound ────────────────────────────────────────────────
    var notifSound = null;
    try {
        notifSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNw==');
        notifSound.volume = 0.3;
    } catch(e) {}

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

    // ─── Show a single desktop notification ──────────────────
    function showDesktopNotification(senderName, message, conversationName, conversationId) {
        // Play sound
        if (notifSound) {
            notifSound.currentTime = 0;
            notifSound.play().catch(function() {});
        }

        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        var truncated = message && message.length > 120 ? message.substring(0, 120) + '...' : (message || 'New message');

        try {
            var notification = new Notification((senderName || 'Someone') + ' - Taurus CRM', {
                body: (conversationName ? conversationName + ': ' : '') + truncated,
                icon: '/images/favicon.ico',
                badge: '/images/favicon.ico',
                tag: 'chat-' + (conversationId || Date.now()),
                requireInteraction: false,
                silent: true
            });

            notification.onclick = function() {
                window.focus();
                window.location.href = '/chat';
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

    // ─── Start polling: first after 3s, then every 5s ────────
    setTimeout(pollNewMessages, 3000);
    setInterval(pollNewMessages, 5000);

    // ─── Public API (for chat page inline JS) ────────────────
    window.ChatNotify = {
        show: showDesktopNotification
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
