/**
 * Service Worker for Chat Notifications
 * Handles notifications when the app is in the background
 */

// Handle push notifications
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        icon: data.icon || '/images/logo.png',
        badge: data.badge || '/images/logo.png',
        body: data.body || 'New notification from Taurus CRM',
        tag: data.tag || 'chat-notification',
        requireInteraction: data.requireInteraction || false,
        data: data.data || {},
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Taurus CRM', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    event.notification.close();

    const data = event.notification.data || {};
    const conversationId = data.conversationId;

    // Find existing chat window
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(clientList => {
            // Check if a chat window is already open
            for (let client of clientList) {
                if (client.url.includes('/chat') && 'focus' in client) {
                    client.focus();
                    if (conversationId) {
                        client.postMessage({
                            type: 'NAVIGATE_TO_CONVERSATION',
                            conversationId: conversationId,
                        });
                    }
                    return client;
                }
            }
            
            // If no chat window, open one
            if (clients.openWindow) {
                const url = conversationId 
                    ? `/chat?conversation=${conversationId}`
                    : '/chat';
                return clients.openWindow(url);
            }
        })
    );
});

// Handle notification close
self.addEventListener('notificationclose', event => {
    console.log('Notification closed:', event.notification.tag);
});

// Install event
self.addEventListener('install', event => {
    console.log('Service Worker installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', event => {
    console.log('Service Worker activating...');
    event.waitUntil(clients.claim());
});

// Message event for handling communication from the page
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
