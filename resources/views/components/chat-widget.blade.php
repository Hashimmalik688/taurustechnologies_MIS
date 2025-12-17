<!-- Team Chat Widget -->
<link rel="stylesheet" href="{{ asset('build/css/chat-widget.css') }}">

<!-- Chat Toggle Button -->
<button id="chat-toggle-btn" onclick="toggleChatWidget()">
    <i class="bx bx-message-rounded-dots"></i>
    <span class="chat-unread-badge" id="chat-unread-count" style="display: none;">0</span>
</button>

<!-- Chat Widget -->
<div id="chat-widget">
    <!-- Conversations View -->
    <div id="chat-conversations-view">
        <div class="chat-header">
            <h3>Team Chat</h3>
            <div class="chat-header-actions">
                <button onclick="showNewChatModal()" title="New Chat">
                    <i class="bx bx-plus"></i>
                </button>
                <button onclick="toggleChatWidget()" title="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>

        <div class="chat-search">
            <input type="text" id="chat-search-input" placeholder="Search conversations..." oninput="searchConversations(this.value)">
        </div>

        <div class="chat-conversations" id="chat-conversations-list">
            <div class="chat-loading">
                <i class="bx bx-loader-alt bx-spin"></i>
            </div>
        </div>
    </div>

    <!-- Messages View -->
    <div id="chat-messages-view" class="chat-messages-view">
        <div class="chat-messages-header">
            <button class="chat-back-btn" onclick="backToConversations()">
                <i class="bx bx-arrow-back"></i>
            </button>
            <div class="conversation-avatar" id="current-chat-avatar">
                <span id="current-chat-avatar-text">A</span>
            </div>
            <div class="conversation-info">
                <div class="conversation-name" id="current-chat-name">Loading...</div>
            </div>
        </div>

        <div class="chat-messages" id="chat-messages-container">
            <!-- Messages will be loaded here -->
        </div>

        <div class="chat-input-container">
            <div class="chat-input-wrapper">
                <textarea id="chat-message-input" rows="1" placeholder="Type a message..." onkeydown="handleChatInputKeydown(event)"></textarea>
                <button class="chat-attach-btn" onclick="triggerFileUpload()">
                    <i class="bx bx-paperclip"></i>
                </button>
                <input type="file" id="chat-file-input" multiple hidden accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" onchange="handleFileSelect(event)">
            </div>
            <button id="chat-send-btn" onclick="sendChatMessage()">
                <i class="bx bx-send"></i>
            </button>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div id="new-chat-modal" class="chat-modal">
    <div class="chat-modal-content">
        <div class="chat-modal-header">
            <h4>Start New Chat</h4>
            <button class="chat-modal-close" onclick="closeNewChatModal()">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="chat-modal-body" id="new-chat-users-list">
            <div class="chat-loading">
                <i class="bx bx-loader-alt bx-spin"></i>
            </div>
        </div>
    </div>
</div>

<script>
// Chat Widget State
let chatState = {
    currentConversationId: null,
    conversations: [],
    messages: [],
    users: [],
    isOpen: false,
    selectedFiles: [],
};

// Toggle chat widget
function toggleChatWidget() {
    const widget = document.getElementById('chat-widget');
    chatState.isOpen = !chatState.isOpen;

    if (chatState.isOpen) {
        widget.classList.add('show');
        loadConversations();
    } else {
        widget.classList.remove('show');
    }
}

// Load conversations
async function loadConversations() {
    try {
        const response = await fetch('/api/chat/conversations', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            chatState.conversations = data.conversations;
            renderConversations();
            updateUnreadCount();
        }
    } catch (error) {
        console.error('Error loading conversations:', error);
    }
}

// Render conversations list
function renderConversations() {
    const container = document.getElementById('chat-conversations-list');

    if (chatState.conversations.length === 0) {
        container.innerHTML = `
            <div class="chat-empty">
                <i class="bx bx-message-square-x"></i>
                <p>No conversations yet.<br>Start a new chat!</p>
            </div>
        `;
        return;
    }

    container.innerHTML = chatState.conversations.map(conv => `
        <div class="conversation-item ${conv.id === chatState.currentConversationId ? 'active' : ''}"
             onclick="openConversation(${conv.id})">
            <div class="conversation-avatar">
                ${conv.avatar ? `<img src="${conv.avatar}" alt="${conv.name}">` : `<span>${conv.name.charAt(0).toUpperCase()}</span>`}
            </div>
            <div class="conversation-info">
                <div class="conversation-name">${conv.name}</div>
                ${conv.latest_message ? `
                    <div class="conversation-last-message">
                        ${conv.latest_message.user_name}: ${conv.latest_message.message || 'Sent a file'}
                    </div>
                ` : '<div class="conversation-last-message">No messages yet</div>'}
            </div>
            <div class="conversation-meta">
                ${conv.latest_message ? `<div class="conversation-time">${conv.latest_message.created_at}</div>` : ''}
                ${conv.unread_count > 0 ? `<div class="conversation-unread">${conv.unread_count}</div>` : ''}
            </div>
        </div>
    `).join('');
}

// Open conversation
async function openConversation(conversationId) {
    chatState.currentConversationId = conversationId;
    const conversation = chatState.conversations.find(c => c.id === conversationId);

    if (!conversation) return;

    // Update UI
    document.getElementById('current-chat-name').textContent = conversation.name;
    document.getElementById('current-chat-avatar-text').textContent = conversation.name.charAt(0).toUpperCase();

    // Show messages view
    document.getElementById('chat-conversations-view').style.display = 'none';
    document.getElementById('chat-messages-view').classList.add('active');

    // Load messages
    await loadMessages(conversationId);
}

// Load messages
async function loadMessages(conversationId) {
    const container = document.getElementById('chat-messages-container');
    container.innerHTML = '<div class="chat-loading"><i class="bx bx-loader-alt bx-spin"></i></div>';

    try {
        const response = await fetch(`/api/chat/conversations/${conversationId}/messages`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            chatState.messages = data.messages.data || [];
            renderMessages();
            scrollToBottom();

            // Reload conversations to update unread count
            loadConversations();
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Render messages
function renderMessages() {
    const container = document.getElementById('chat-messages-container');
    const currentUserId = {{ Auth::id() }};

    if (chatState.messages.length === 0) {
        container.innerHTML = `
            <div class="chat-empty">
                <i class="bx bx-message"></i>
                <p>No messages yet.<br>Say hello!</p>
            </div>
        `;
        return;
    }

    container.innerHTML = chatState.messages.map(msg => `
        <div class="message-item ${msg.user_id === currentUserId ? 'own' : ''}">
            <div class="message-avatar">
                ${msg.user.avatar ? `<img src="${msg.user.avatar}" alt="${msg.user.name}">` : `<span>${msg.user.name.charAt(0).toUpperCase()}</span>`}
            </div>
            <div class="message-content">
                ${msg.user_id !== currentUserId ? `<div class="message-sender">${msg.user.name}</div>` : ''}
                ${msg.message ? `<div class="message-text">${escapeHtml(msg.message)}</div>` : ''}
                ${msg.attachments && msg.attachments.length > 0 ? renderAttachments(msg.attachments) : ''}
                <div class="message-time">${formatTime(msg.created_at)}</div>
            </div>
        </div>
    `).join('');
}

// Render attachments
function renderAttachments(attachments) {
    return attachments.map(att => {
        if (att.mime_type.startsWith('image/')) {
            return `<img src="${att.url}" class="message-image" alt="${att.file_name}">`;
        } else {
            return `
                <a href="${att.url}" target="_blank" class="message-file">
                    <i class="bx bx-file"></i>
                    <div>
                        <div style="font-size: 12px; font-weight: 600;">${att.file_name}</div>
                        <div style="font-size: 11px; opacity: 0.7;">${att.file_size_human}</div>
                    </div>
                </a>
            `;
        }
    }).join('');
}

// Send message
async function sendChatMessage() {
    const input = document.getElementById('chat-message-input');
    const message = input.value.trim();

    if (!message && chatState.selectedFiles.length === 0) return;
    if (!chatState.currentConversationId) return;

    const formData = new FormData();
    formData.append('conversation_id', chatState.currentConversationId);
    if (message) formData.append('message', message);

    // Add files
    chatState.selectedFiles.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file);
    });

    try {
        const response = await fetch('/api/chat/messages', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token'),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            input.value = '';
            chatState.selectedFiles = [];
            document.getElementById('chat-file-input').value = '';
            await loadMessages(chatState.currentConversationId);
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
}

// Handle input keydown
function handleChatInputKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendChatMessage();
    }
}

// File handling
function triggerFileUpload() {
    document.getElementById('chat-file-input').click();
}

function handleFileSelect(event) {
    chatState.selectedFiles = Array.from(event.target.files);
    // You could show preview here
}

// Back to conversations
function backToConversations() {
    document.getElementById('chat-conversations-view').style.display = 'flex';
    document.getElementById('chat-messages-view').classList.remove('active');
    chatState.currentConversationId = null;
}

// New chat modal
async function showNewChatModal() {
    const modal = document.getElementById('new-chat-modal');
    modal.classList.add('show');

    // Load users
    try {
        const response = await fetch('/api/chat/users', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            chatState.users = data.users;
            renderUsersList();
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

function renderUsersList() {
    const container = document.getElementById('new-chat-users-list');

    container.innerHTML = chatState.users.map(user => `
        <div class="user-select-item" onclick="startChatWithUser(${user.id})">
            <div class="conversation-avatar" style="width: 40px; height: 40px; font-size: 16px;">
                ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}">` : `<span>${user.name.charAt(0).toUpperCase()}</span>`}
            </div>
            <div class="conversation-info">
                <div class="conversation-name">${user.name}</div>
                <div class="conversation-last-message">${user.email}</div>
            </div>
        </div>
    `).join('');
}

async function startChatWithUser(userId) {
    try {
        const response = await fetch('/api/chat/conversations/direct', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ user_id: userId }),
        });

        const data = await response.json();

        if (data.success) {
            closeNewChatModal();
            await loadConversations();
            openConversation(data.conversation_id);
        }
    } catch (error) {
        console.error('Error creating conversation:', error);
    }
}

function closeNewChatModal() {
    document.getElementById('new-chat-modal').classList.remove('show');
}

// Search conversations
function searchConversations(query) {
    if (!query) {
        renderConversations();
        return;
    }

    const filtered = chatState.conversations.filter(conv =>
        conv.name.toLowerCase().includes(query.toLowerCase())
    );

    const container = document.getElementById('chat-conversations-list');
    // Render filtered results (similar to renderConversations but with filtered array)
}

// Update unread count
function updateUnreadCount() {
    const totalUnread = chatState.conversations.reduce((sum, conv) => sum + conv.unread_count, 0);
    const badge = document.getElementById('chat-unread-count');

    if (totalUnread > 0) {
        badge.textContent = totalUnread > 99 ? '99+' : totalUnread;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

// Utility functions
function scrollToBottom() {
    const container = document.getElementById('chat-messages-container');
    container.scrollTop = container.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    return date.toLocaleDateString();
}

// Auto-refresh conversations every 30 seconds
setInterval(() => {
    if (chatState.isOpen && !chatState.currentConversationId) {
        loadConversations();
    }
}, 30000);

// Auto-refresh messages every 5 seconds when in a conversation
setInterval(() => {
    if (chatState.isOpen && chatState.currentConversationId) {
        loadMessages(chatState.currentConversationId);
    }
}, 5000);
</script>
