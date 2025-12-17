@extends('layouts.master')

@section('title')
    Team Chat
@endsection

@section('css')
    @vite(['resources/css/chat.css'])
@endsection

@section('content')
    <div class="chat-wrapper">
        <div class="chat-container">
            <!-- Conversations Sidebar -->
            <div class="chat-sidebar">
                <div class="chat-sidebar-header">
                    <h5 class="mb-0"><i class="bx bx-message-dots"></i> Chats</h5>
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        <i class="bx bx-edit-alt"></i>
                    </button>
                </div>

                <div class="chat-search-box">
                    <i class="bx bx-search"></i>
                    <input type="text" id="searchConversations" placeholder="Search messages or people" class="chat-search-input">
                </div>

                            <div class="conversations-list" id="conversationsList">
                                <div class="loading-conversations">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2">Loading chats...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="chat-main" id="chatMain">
                            <div class="chat-welcome">
                                <i class="bx bx-message-square-dots"></i>
                                <h4>Select a conversation</h4>
                                <p>Choose from your existing conversations or start a new one</p>
                            </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div class="modal fade" id="newChatModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Start New Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Chat Type Selection -->
                    <div class="mb-4">
                        <label class="form-label">Chat Type</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="chatType" id="directChat" value="direct" checked>
                            <label class="btn btn-outline-primary" for="directChat">
                                <i class="bx bx-user"></i> Direct Message
                            </label>
                            <input type="radio" class="btn-check" name="chatType" id="groupChat" value="group">
                            <label class="btn btn-outline-primary" for="groupChat">
                                <i class="bx bx-group"></i> Group Chat
                            </label>
                        </div>
                    </div>

                    <!-- Direct Chat Section -->
                    <div id="directChatSection">
                        <label class="form-label">Select User</label>
                        <div class="mb-3">
                            <input type="text" id="searchUsers" class="form-control" placeholder="Search users...">
                        </div>
                        <div id="usersList" class="users-list" style="max-height: 300px; overflow-y: auto;">
                            <!-- Users will be populated here -->
                        </div>
                    </div>

                    <!-- Group Chat Section -->
                    <div id="groupChatSection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" id="groupName" class="form-control" placeholder="Enter group name...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Members</label>
                            <input type="text" id="searchGroupUsers" class="form-control" placeholder="Search users...">
                        </div>
                        <div id="groupUsersList" class="users-list" style="max-height: 250px; overflow-y: auto;">
                            <!-- Users will be populated here -->
                        </div>
                        <div id="selectedMembers" class="mt-3">
                            <label class="form-label">Selected Members</label>
                            <div id="membersList" class="d-flex flex-wrap gap-2">
                                <!-- Selected members will appear here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createChatBtn">Create Chat</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Group Management Modal -->
<div class="modal fade" id="groupManagementModal" tabindex="-1" aria-labelledby="groupManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupManagementModalLabel">Manage Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Group Info Section -->
                    <div class="col-md-6">
                        <div class="group-info-section">
                            <h6>Group Information</h6>
                            <div class="form-group mb-3">
                                <label for="groupNameEdit">Group Name:</label>
                                <input type="text" id="groupNameEdit" class="form-control" placeholder="Enter group name">
                            </div>
                            <div class="form-group mb-3">
                                <label>Created by:</label>
                                <p id="groupCreator" class="form-control-plaintext">-</p>
                            </div>
                            <div class="group-actions">
                                <button type="button" class="btn btn-primary" onclick="updateGroupName()">Update Name</button>
                                <button type="button" class="btn btn-danger" onclick="deleteGroup()" id="deleteGroupBtn">Delete Group</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Members Management Section -->
                    <div class="col-md-6">
                        <div class="members-section">
                            <h6>Members Management</h6>
                            <div class="current-members mb-3">
                                <label>Current Members:</label>
                                <div id="currentMembersList" class="members-list">
                                    <!-- Members will be loaded here -->
                                </div>
                            </div>
                            
                            <div class="add-members">
                                <label>Add New Members:</label>
                                <div class="form-group">
                                    <input type="text" id="memberSearch" class="form-control" placeholder="Search users to add...">
                                </div>
                                <div id="availableUsersList" class="users-list mt-2" style="max-height: 200px; overflow-y: auto;">
                                    <!-- Available users will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
const currentUserId = {{ auth()->id() }};
let currentConversationId = null;
let currentConversationName = '';
let messagesRefreshInterval = null;
let conversationsRefreshInterval = null;

// API helper
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    };

    if (data && !(data instanceof FormData)) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    } else if (data) {
        options.body = data;
    }

    console.log('API Call:', { url, method, data, options });

    const response = await fetch(url, options);
    
    console.log('API Response:', {
        status: response.status,
        statusText: response.statusText,
        headers: Object.fromEntries(response.headers.entries())
    });
    
    if (!response.ok) {
        const errorText = await response.text();
        console.error('API Error Details:', {
            status: response.status,
            statusText: response.statusText,
            errorText: errorText,
            url: url,
            method: method
        });
        
        // Try to parse JSON error response
        let errorData;
        try {
            errorData = JSON.parse(errorText);
        } catch (e) {
            errorData = { message: errorText };
        }
        
        throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorData.message || errorText}`);
    }
    return await response.json();
}

// Load conversations
async function loadConversations() {
    try {
        console.log('Loading conversations...');
        const conversationsData = await apiCall('/api/chat/conversations');
        console.log('Conversations loaded:', conversationsData);
        const usersData = await apiCall('/api/chat/users');
        console.log('Users loaded:', usersData);
        renderConversationsAndUsers(conversationsData.conversations, usersData.users);
    } catch (error) {
        console.error('Error loading conversations:', error);
        const listEl = document.getElementById('conversationsList');
        listEl.innerHTML = '<div class="no-conversations"><p style="color: red;">Error loading chats. Please refresh the page.</p><p style="font-size: 12px; color: #666;">Check browser console for details</p></div>';
    }
}

// Render conversations and users together
function renderConversationsAndUsers(conversations, users) {
    const listEl = document.getElementById('conversationsList');

    if (conversations.length === 0 && users.length === 0) {
        listEl.innerHTML = '<div class="no-conversations"><p>No chats available</p></div>';
        return;
    }

    let html = '';

    // Add existing conversations first
    if (conversations.length > 0) {
        html += '<div class="sidebar-section-label">Recent</div>';
        html += conversations.map(conv => {
            const safeName = conv.name || 'Unknown';
            return `
            <div class="conversation-item ${conv.id === currentConversationId ? 'active' : ''}"
                 onclick="selectConversation(${conv.id}, '${safeName.replace(/'/g, "\\'")}', this)">
                <div class="conversation-avatar">${safeName.charAt(0).toUpperCase()}</div>
                <div class="conversation-info">
                    <div class="conversation-name">${safeName}</div>
                    ${conv.latest_message ? `<div class="conversation-preview">${(conv.latest_message.message || '').substring(0, 40)}...</div>` : ''}
                </div>
                ${conv.updated_at ? `<div class="conversation-time">${conv.updated_at}</div>` : ''}
                ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
            </div>
        `}).join('');
    }

    // Add available users
    if (users.length > 0) {
        html += '<div class="sidebar-section-label">Direct Messages</div>';
        html += users.map(user => {
            const safeName = user.name || 'Unknown User';
            return `
            <div class="user-item" onclick="startDirectChat(${user.id}, '${safeName.replace(/'/g, "\\'")}')">
                <div class="user-avatar">${safeName.charAt(0).toUpperCase()}</div>
                <div class="user-info">
                    <div class="user-name">${safeName}</div>
                </div>
            </div>
        `}).join('');
    }

    listEl.innerHTML = html;
}

// Old render function kept for backwards compatibility
function renderConversations(conversations) {
    const listEl = document.getElementById('conversationsList');

    if (conversations.length === 0) {
        listEl.innerHTML = '<div class="no-conversations"><p>No conversations yet<br>Click <i class="bx bx-edit"></i> to start chatting</p></div>';
        return;
    }

    listEl.innerHTML = conversations.map(conv => `
        <div class="conversation-item ${conv.id === currentConversationId ? 'active' : ''}"
             onclick="selectConversation(${conv.id}, '${conv.name.replace(/'/g, "\\'")}')">
            <div class="conversation-avatar">
                <div class="avatar-circle">${conv.name.charAt(0).toUpperCase()}</div>
            </div>
            <div class="conversation-info">
                <div class="conversation-name">${conv.name}</div>
                <div class="conversation-last-message">
                    ${conv.latest_message ? conv.latest_message.message.substring(0, 50) : 'No messages yet'}
                </div>
            </div>
            <div class="conversation-meta">
                <div class="conversation-time">${conv.updated_at}</div>
                ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
            </div>
        </div>
    `).join('');
}

// Select conversation
async function selectConversation(conversationId, conversationName, element) {
    currentConversationId = conversationId;

    // Update active state
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    if (element) {
        const conversationItem = element.closest ? element.closest('.conversation-item') : element;
        if (conversationItem) {
            conversationItem.classList.add('active');
        }
    }

    // Load messages
    await loadMessages(conversationId, conversationName);
}

// Load messages
async function loadMessages(conversationId, conversationName) {
    try {
        const data = await apiCall(`/api/chat/conversations/${conversationId}/messages`);
        renderChatArea(conversationName, data.messages.data, data.conversation?.type || 'direct', conversationId);
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Render chat area
function renderChatArea(conversationName, messages, conversationType = 'direct', conversationId = null) {
    const chatMain = document.getElementById('chatMain');

    chatMain.innerHTML = `
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-header-avatar">${conversationName.charAt(0).toUpperCase()}</div>
                <div class="chat-header-title">
                    <h5>${conversationName}</h5>
                    <p>Active</p>
                </div>
            </div>
            <div class="chat-header-actions">
                ${conversationType === 'group' && conversationId ? `<button class="btn btn-sm btn-outline-primary me-2" onclick="openGroupManagement(${conversationId})" title="Manage Group"><i class="bx bx-cog"></i> Manage</button>` : ''}
                <button class="btn btn-sm btn-light" title="More options"><i class="bx bx-dots-vertical-rounded"></i></button>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            ${renderMessages(messages)}
        </div>

        <div class="chat-input-area">
            <div class="message-input-wrapper">
                <textarea id="messageInput" placeholder="Type a message..." rows="1"></textarea>
                <div class="message-input-actions">
                    <button type="button" id="attachBtn" title="Attach file">
                        <i class="bx bx-paperclip"></i>
                    </button>
                    <button type="button" id="sendButton" title="Send message">
                        <i class="bx bx-send"></i>
                    </button>
                </div>
            </div>
            <input type="file" id="fileInput" multiple style="display: none" accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.txt,.zip,.rar,.mp3,.mp4,.wav,.m4a,.ogg,.webm,.png,.jpg,.jpeg,.gif,.bmp,.svg">
        </div>
    `;

    // Scroll to bottom
    setTimeout(() => {
        const messagesEl = document.getElementById('chatMessages');
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }, 100);

    // Add event listeners
    document.getElementById('sendButton').addEventListener('click', sendMessage);
    document.getElementById('messageInput').addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    document.getElementById('attachBtn').addEventListener('click', () => {
        document.getElementById('fileInput').click();
    });
    document.getElementById('fileInput').addEventListener('change', handleFileSelect);

    // Start auto-refresh for messages
    clearInterval(messagesRefreshInterval);
    messagesRefreshInterval = setInterval(() => {
        if (currentConversationId) {
            refreshMessages();
        }
    }, 5000);
}

// Render messages
function renderMessages(messages) {
    if (messages.length === 0) {
        return '<div class="no-messages"><i class="bx bx-message-dots"></i><p>No messages yet. Start the conversation!</p></div>';
    }

    return messages.map(msg => {
        const isSender = msg.user_id === currentUserId;
        return `
        <div class="message-item ${isSender ? 'message-sender' : 'message-receiver'}">
            <div class="message-avatar">${msg.user.name.charAt(0).toUpperCase()}</div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-sender">${msg.user.name}</span>
                    <span class="message-time">${new Date(msg.created_at).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</span>
                </div>
                ${msg.message ? `<div class="message-text">${msg.message}</div>` : ''}
                ${msg.attachments && msg.attachments.length > 0 ? `
                    <div class="message-attachment">
                        ${msg.attachments.map(att => {
                            if (att.mime_type.startsWith('image/')) {
                                return `<img src="/storage/${att.file_path}" alt="${att.file_name}" onclick="window.open('/storage/${att.file_path}', '_blank')" class="attachment-image">`;
                            } else if (att.mime_type.startsWith('audio/')) {
                                return `
                                    <div class="audio-message">
                                        <i class="bx bx-volume-full"></i>
                                        <div class="audio-info">
                                            <div class="audio-name">${att.file_name}</div>
                                            <audio controls preload="metadata" style="width: 100%; max-width: 300px; margin-top: 8px;">
                                                <source src="/storage/${att.file_path}" type="${att.mime_type}">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    </div>
                                `;
                            } else if (att.mime_type.startsWith('video/')) {
                                return `
                                    <div class="video-message">
                                        <video controls preload="metadata" style="max-width: 400px; border-radius: 8px;">
                                            <source src="/storage/${att.file_path}" type="${att.mime_type}">
                                            Your browser does not support the video element.
                                        </video>
                                        <div class="video-name">${att.file_name}</div>
                                    </div>
                                `;
                            } else if (att.mime_type === 'application/pdf') {
                                return `
                                    <a href="/storage/${att.file_path}" target="_blank" class="file-attachment pdf">
                                        <i class="bx bx-file-doc"></i>
                                        <span class="file-info">
                                            <span class="file-name">${att.file_name}</span>
                                            <span class="file-type">PDF Document</span>
                                        </span>
                                    </a>
                                `;
                            } else if (att.mime_type.includes('word') || att.file_name.endsWith('.doc') || att.file_name.endsWith('.docx')) {
                                return `
                                    <a href="/storage/${att.file_path}" target="_blank" class="file-attachment word">
                                        <i class="bx bx-file-doc"></i>
                                        <span class="file-info">
                                            <span class="file-name">${att.file_name}</span>
                                            <span class="file-type">Word Document</span>
                                        </span>
                                    </a>
                                `;
                            } else if (att.mime_type.includes('zip') || att.mime_type.includes('rar') || att.file_name.endsWith('.zip') || att.file_name.endsWith('.rar')) {
                                return `
                                    <a href="/storage/${att.file_path}" target="_blank" class="file-attachment archive">
                                        <i class="bx bx-archive"></i>
                                        <span class="file-info">
                                            <span class="file-name">${att.file_name}</span>
                                            <span class="file-type">Archive</span>
                                        </span>
                                    </a>
                                `;
                            } else {
                                return `
                                    <a href="/storage/${att.file_path}" target="_blank" class="file-attachment">
                                        <i class="bx bx-file"></i>
                                        <span class="file-info">
                                            <span class="file-name">${att.file_name}</span>
                                            <span class="file-type">File</span>
                                        </span>
                                    </a>
                                `;
                            }
                        }).join('')}
                    </div>
                ` : ''}
            </div>
            <div class="message-actions">
                <button onclick="deleteMessage(${msg.id})" title="Delete"><i class="bx bx-trash"></i></button>
            </div>
        </div>
    `;
    }).join('');
}

// Refresh messages
async function refreshMessages() {
    if (!currentConversationId) return;

    try {
        const data = await apiCall(`/api/chat/conversations/${currentConversationId}/messages`);
        const messagesEl = document.getElementById('chatMessages');
        const scrolledToBottom = messagesEl.scrollHeight - messagesEl.scrollTop === messagesEl.clientHeight;

        messagesEl.innerHTML = renderMessages(data.messages.data);

        if (scrolledToBottom) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }
    } catch (error) {
        console.error('Error refreshing messages:', error);
    }
}

// Delete message function
async function deleteMessage(messageId) {
    if (!confirm('Are you sure you want to delete this message?')) {
        return;
    }

    try {
        await apiCall(`/api/chat/messages/${messageId}`, 'DELETE');
        console.log('Message deleted successfully');
        await refreshMessages();
        loadConversations(); // Update conversation list
    } catch (error) {
        console.error('Error deleting message:', error);
        alert('Failed to delete message: ' + error.message);
    }
}

// Send message
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const message = input.value.trim();

    if (!message && fileInput.files.length === 0) {
        alert('Please enter a message or select a file');
        return;
    }
    
    if (!currentConversationId) {
        alert('Please select a conversation first');
        return;
    }

    console.log('Sending message to conversation:', currentConversationId);

    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    
    // Always append message as a string, even if empty
    formData.append('message', message || '');

    if (fileInput.files.length > 0) {
        for (let file of fileInput.files) {
            formData.append('attachments[]', file);
        }
    }

    try {
        const response = await apiCall('/api/chat/messages', 'POST', formData);
        console.log('Message sent successfully:', response);
        input.value = '';
        fileInput.value = '';
        input.placeholder = 'Type a message...'; // Reset placeholder
        await refreshMessages();
        loadConversations(); // Update conversation list
    } catch (error) {
        console.error('Error sending message:', error);
        console.error('Error details:', error.message, error.stack);
        
        // Show more specific error message
        let errorMessage = 'Failed to send message';
        if (error.message) {
            errorMessage += ': ' + error.message;
        }
        alert(errorMessage);
    }
}

// Handle file select
function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    if (files.length > 0) {
        const messageInput = document.getElementById('messageInput');
        
        // Show detailed file info
        const fileInfo = files.map(file => {
            const sizeKB = Math.round(file.size / 1024);
            const sizeDisplay = sizeKB > 1024 ? `${Math.round(sizeKB/1024)}MB` : `${sizeKB}KB`;
            return `${file.name} (${sizeDisplay})`;
        }).join(', ');
        
        messageInput.placeholder = `📎 ${files.length} file(s): ${fileInfo.substring(0, 80)}${fileInfo.length > 80 ? '...' : ''}`;
        
        // Validate file sizes (10MB limit)
        const oversizedFiles = files.filter(file => file.size > 10 * 1024 * 1024);
        if (oversizedFiles.length > 0) {
            alert(`Some files are too large (max 10MB): ${oversizedFiles.map(f => f.name).join(', ')}`);
            e.target.value = ''; // Clear selection
            messageInput.placeholder = 'Type a message...';
            return;
        }
        
        console.log('Files selected:', files.map(f => ({ name: f.name, type: f.type, size: f.size })));
    }
}

// Load users for new chat
async function loadUsers() {
    try {
        const data = await apiCall('/api/chat/users');
        const usersListEl = document.getElementById('usersList');

        usersListEl.innerHTML = data.users.map(user => `
            <div class="user-item" onclick="startDirectChat(${user.id})">
                <div class="user-avatar">
                    <div class="avatar-circle">${user.name.charAt(0).toUpperCase()}</div>
                </div>
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-email">${user.email}</div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Start direct chat
async function startDirectChat(userId, userName) {
    console.log('startDirectChat called with:', { userId, userName });
    try {
        const data = await apiCall('/api/chat/conversations/direct', 'POST', { user_id: userId });
        console.log('Conversation created/fetched:', data);

        // Try to close modal if it exists
        const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
        if (modal) modal.hide();

        // Immediately load the conversation
        if (data.conversation_id) {
            await loadMessages(data.conversation_id, userName || 'Chat');
            currentConversationId = data.conversation_id;
            
            // Reload conversations to update sidebar
            loadConversations();
        }
    } catch (error) {
        console.error('Error starting chat:', error);
    }
}

// Search conversations and people
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchConversations');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            let lastVisibleSection = null;

            document.querySelectorAll('#conversationsList > *').forEach(item => {
                // Handle section labels
                if (item.classList.contains('sidebar-section-label')) {
                    item.style.display = 'block'; // Keep visible for now
                    lastVisibleSection = item;
                    return;
                }

                // Handle conversation/user items
                if (item.classList.contains('conversation-item')) {
                    const name = item.querySelector('.conversation-name')?.textContent.toLowerCase() || '';
                    const email = item.querySelector('.conversation-last-message')?.textContent.toLowerCase() || '';
                    const matches = name.includes(query) || email.includes(query);
                    item.style.display = matches ? 'flex' : 'none';
                }
            });

            // Hide section labels if no items match
            document.querySelectorAll('.sidebar-section-label').forEach(label => {
                const nextItem = label.nextElementSibling;
                let hasVisibleItems = false;
                let current = label.nextElementSibling;
                while (current && !current.classList.contains('sidebar-section-label')) {
                    if (current.style.display !== 'none') {
                        hasVisibleItems = true;
                        break;
                    }
                    current = current.nextElementSibling;
                }
                label.style.display = hasVisibleItems ? 'block' : 'none';
            });
        });
    }

    // Search users in new chat modal
    const searchUsersInput = document.getElementById('searchUsers');
    if (searchUsersInput) {
        searchUsersInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.user-item').forEach(item => {
                const userName = item.querySelector('.user-name').textContent.toLowerCase();
                const userEmail = item.querySelector('.user-email').textContent.toLowerCase();
                const matches = userName.includes(query) || userEmail.includes(query);
                item.style.display = matches ? 'flex' : 'none';
            });
        });
    }
});

// New chat button

// Initial load
loadConversations();

// Auto-refresh conversations every 30 seconds
conversationsRefreshInterval = setInterval(loadConversations, 30000);

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(messagesRefreshInterval);
    clearInterval(conversationsRefreshInterval);
});

// -------------------------
// Laravel Echo (Pusher) setup for self-hosted laravel-websockets
// This uses CDN scripts (pusher-js + laravel-echo) so you don't have to
// install npm packages immediately. It is safe if broadcasting isn't
// configured — the code will try to initialize Echo and silently fail.
// -------------------------

// Configuration from environment (100% local Reverb, no Pusher)
const echoConfig = {!! json_encode([
    'key' => env('REVERB_APP_KEY', ''),
    'host' => env('REVERB_HOST', '127.0.0.1'),
    'port' => intval(env('REVERB_PORT', 8080)),
    'scheme' => env('REVERB_SCHEME', 'http'),
    'forceTLS' => env('REVERB_SCHEME', 'http') === 'https',
]) !!};

let echoInstance = null;
let subscribedChannel = null;

function initEcho() {
    // Load Pusher and Echo from CDN if not already present
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) return resolve();
            const s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    // Only attempt if a key exists
    if (!echoConfig.key) return;

    // If Echo already initialized, return
    if (window.Echo || echoInstance) return;

    Promise.resolve()
        .then(() => loadScript('https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js'))
        .then(() => {
            try {
                echoInstance = new Echo({
                    broadcaster: 'reverb',
                    key: echoConfig.key,
                    wsHost: echoConfig.host,
                    wsPort: echoConfig.port,
                    wssPort: echoConfig.port,
                    forceTLS: echoConfig.forceTLS,
                    enabledTransports: ['ws', 'wss'],
                });
            } catch (e) {
                console.warn('Echo initialization failed', e);
            }
        })
        .catch(err => console.warn('Failed to load Echo/Pusher scripts', err));
}

function subscribeToConversation(conversationId) {
    if (!echoInstance) initEcho();

    // Unsubscribe previous
    try {
        if (subscribedChannel && echoInstance) {
            echoInstance.leave(`private-chat.conversation.${subscribedChannel}`);
        }
    } catch (e) { /* ignore */ }

    subscribedChannel = conversationId;

    // Wait until Echo is ready
    const waitForEcho = setInterval(() => {
        if (!echoInstance) return;

        clearInterval(waitForEcho);

        try {
            echoInstance.private(`chat.conversation.${conversationId}`)
                .listen('.message.sent', (e) => {
                    // Received a new message — refresh messages if same conversation
                    if (conversationId === currentConversationId) {
                        // Append new message to chat area
                        refreshMessages();
                        loadConversations(); // update latest message in list
                    }
                });
        } catch (e) {
            console.warn('Failed to subscribe to conversation channel', e);
        }
    }, 200);
}

// Group chat functionality
let selectedMembers = new Set();

document.addEventListener('DOMContentLoaded', function() {
    // Chat type toggle
    document.querySelectorAll('input[name="chatType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const directSection = document.getElementById('directChatSection');
            const groupSection = document.getElementById('groupChatSection');
            
            if (this.value === 'direct') {
                directSection.style.display = 'block';
                groupSection.style.display = 'none';
            } else {
                directSection.style.display = 'none';
                groupSection.style.display = 'block';
                loadUsersForGroup();
            }
        });
    });

    // Create chat button
    document.getElementById('createChatBtn').addEventListener('click', function() {
        const chatType = document.querySelector('input[name="chatType"]:checked').value;
        if (chatType === 'group') {
            createGroupChat();
        }
        // Direct chats are handled by clicking on users directly
    });

    // Modal shown event
    document.getElementById('newChatModal').addEventListener('shown.bs.modal', function() {
        loadUsersForDirectChat();
        selectedMembers.clear();
        updateSelectedMembersList();
    });
});

// Load users for direct chat
async function loadUsersForDirectChat() {
    try {
        const data = await apiCall('/api/chat/users');
        renderUsersList(data.users, 'usersList', false);
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Load users for group chat
async function loadUsersForGroup() {
    try {
        const data = await apiCall('/api/chat/users');
        renderUsersList(data.users, 'groupUsersList', true);
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Render users list
function renderUsersList(users, containerId, isGroupMode) {
    const container = document.getElementById(containerId);
    
    container.innerHTML = users.map(user => `
        <div class="user-item ${isGroupMode ? 'group-user-item' : ''}" 
             data-user-id="${user.id}" 
             data-user-name="${user.name}"
             onclick="${isGroupMode ? `toggleGroupMember(${user.id}, '${user.name.replace(/'/g, "\\'")}')` : `startDirectChat(${user.id}, '${user.name.replace(/'/g, "\\'")}'); bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();`}">
            <div class="user-avatar">${user.name.charAt(0).toUpperCase()}</div>
            <div class="user-info">
                <div class="user-name">${user.name}</div>
                <div class="user-email">${user.email}</div>
            </div>
            ${isGroupMode ? '<div class="user-select-indicator"><i class="bx bx-check"></i></div>' : ''}
        </div>
    `).join('');
}

// Toggle group member selection
function toggleGroupMember(userId, userName) {
    if (selectedMembers.has(userId)) {
        selectedMembers.delete(userId);
    } else {
        selectedMembers.add(userId);
    }
    
    // Update UI
    const userItem = document.querySelector(`[data-user-id="${userId}"]`);
    userItem.classList.toggle('selected');
    
    updateSelectedMembersList();
}

// Update selected members list
function updateSelectedMembersList() {
    const membersList = document.getElementById('membersList');
    const createBtn = document.getElementById('createChatBtn');
    
    if (selectedMembers.size === 0) {
        membersList.innerHTML = '<span class="text-muted">No members selected</span>';
        createBtn.disabled = true;
    } else {
        membersList.innerHTML = Array.from(selectedMembers).map(userId => {
            const userItem = document.querySelector(`[data-user-id="${userId}"]`);
            const userName = userItem.dataset.userName;
            return `
                <span class="badge bg-primary">
                    ${userName}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                            onclick="toggleGroupMember(${userId}, '${userName}')"></button>
                </span>
            `;
        }).join('');
        createBtn.disabled = false;
    }
}

// Create group chat
async function createGroupChat() {
    const groupName = document.getElementById('groupName').value.trim();
    
    if (!groupName) {
        alert('Please enter a group name');
        return;
    }
    
    if (selectedMembers.size === 0) {
        alert('Please select at least one member');
        return;
    }
    
    try {
        const data = await apiCall('/api/chat/groups', 'POST', {
            name: groupName,
            user_ids: Array.from(selectedMembers)
        });
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();
        
        // Load the new group conversation
        await loadMessages(data.conversation_id, groupName);
        currentConversationId = data.conversation_id;
        
        // Refresh conversations list
        loadConversations();
        
        // Clear form
        document.getElementById('groupName').value = '';
        selectedMembers.clear();
        updateSelectedMembersList();
        
    } catch (error) {
        console.error('Error creating group:', error);
        alert('Failed to create group chat');
    }
}

// Group Management Functions
let currentGroupData = null;
let currentGroupMembers = [];

async function openGroupManagement(conversationId) {
    try {
        // Get group details
        const groupData = await apiCall(`/api/chat/conversations/${conversationId}`);
        const membersData = await apiCall(`/api/chat/conversations/${conversationId}/members`);
        const availableUsers = await apiCall('/api/chat/users');
        
        currentGroupData = groupData.conversation;
        currentGroupMembers = membersData.members;
        
        // Populate modal with group data
        document.getElementById('groupNameEdit').value = currentGroupData.name;
        document.getElementById('groupCreator').textContent = currentGroupData.creator?.name || 'Unknown';
        
        // Show/hide delete button based on permissions
        const deleteBtn = document.getElementById('deleteGroupBtn');
        if (currentUserId && parseInt(currentGroupData.created_by) === parseInt(currentUserId)) {
            deleteBtn.style.display = 'inline-block';
        } else {
            deleteBtn.style.display = 'none';
        }
        
        // Load current members
        renderCurrentMembers();
        
        // Load available users to add
        renderAvailableUsers(availableUsers.users);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('groupManagementModal'));
        modal.show();
        
    } catch (error) {
        console.error('Error opening group management:', error);
        alert('Failed to load group details: ' + error.message);
    }
}

function renderCurrentMembers() {
    const container = document.getElementById('currentMembersList');
    
    container.innerHTML = currentGroupMembers.map(member => `
        <div class="member-item d-flex justify-content-between align-items-center p-2 border rounded mb-2">
            <div class="d-flex align-items-center">
                <div class="member-avatar me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">${member.name.charAt(0).toUpperCase()}</div>
                <div>
                    <div class="member-name fw-bold">${member.name}</div>
                    <small class="text-muted">${member.email}</small>
                </div>
            </div>
            <div class="member-actions">
                ${parseInt(member.id) === parseInt(currentGroupData.created_by) ? '<span class="badge bg-primary">Creator</span>' : ''}
                ${currentUserId && parseInt(member.id) !== parseInt(currentUserId) && parseInt(member.id) !== parseInt(currentGroupData.created_by) && parseInt(currentGroupData.created_by) === parseInt(currentUserId) ? 
                    `<button class="btn btn-sm btn-outline-danger ms-2" onclick="removeMember(${member.id})" title="Remove member"><i class="bx bx-x"></i></button>` : ''}
            </div>
        </div>
    `).join('');
}

function renderAvailableUsers(users) {
    const container = document.getElementById('availableUsersList');
    const memberIds = currentGroupMembers.map(m => m.id);
    const availableUsers = users.filter(user => !memberIds.includes(user.id));
    
    if (availableUsers.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No users available to add</p>';
        return;
    }
    
    container.innerHTML = availableUsers.map(user => `
        <div class="user-item d-flex justify-content-between align-items-center p-2 border rounded mb-2 available-user" style="cursor: pointer;" onclick="addMemberToGroup(${user.id})">
            <div class="d-flex align-items-center">
                <div class="user-avatar me-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">${user.name.charAt(0).toUpperCase()}</div>
                <div>
                    <div class="user-name">${user.name}</div>
                    <small class="text-muted">${user.email}</small>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-primary"><i class="bx bx-plus"></i></button>
        </div>
    `).join('');
}

async function updateGroupName() {
    const newName = document.getElementById('groupNameEdit').value.trim();
    
    if (!newName) {
        alert('Please enter a group name');
        return;
    }
    
    if (!currentGroupData || typeof currentUserId === 'undefined') {
        alert('Unable to update group. Please refresh the page.');
        console.error('Missing data:', { currentGroupData, currentUserId });
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(currentUserId)) {
        alert('Only the group creator can change the group name');
        return;
    }
    
    try {
        await apiCall(`/api/chat/conversations/${currentGroupData.id}`, 'PUT', {
            name: newName
        });
        
        alert('Group name updated successfully');
        currentGroupData.name = newName;
        loadConversations(); // Refresh conversation list
        
        // Update chat header if this is the current conversation
        if (currentConversationId === currentGroupData.id) {
            loadConversation(currentConversationId, newName);
        }
        
    } catch (error) {
        console.error('Error updating group name:', error);
        alert('Failed to update group name: ' + error.message);
    }
}

async function addMemberToGroup(userId) {
    if (!currentGroupData || !currentUserId) {
        alert('Unable to add member. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(currentUserId)) {
        alert('Only the group creator can add members');
        return;
    }
    
    try {
        await apiCall(`/api/chat/conversations/${currentGroupData.id}/members`, 'POST', {
            user_id: userId
        });
        
        // Refresh members list
        const membersData = await apiCall(`/api/chat/conversations/${currentGroupData.id}/members`);
        const availableUsers = await apiCall('/api/chat/users');
        
        currentGroupMembers = membersData.members;
        renderCurrentMembers();
        renderAvailableUsers(availableUsers.users);
        
    } catch (error) {
        console.error('Error adding member:', error);
        alert('Failed to add member: ' + error.message);
    }
}

async function removeMember(userId) {
    if (!currentGroupData || !currentUserId) {
        alert('Unable to remove member. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(currentUserId)) {
        alert('Only the group creator can remove members');
        return;
    }
    
    if (!confirm('Are you sure you want to remove this member from the group?')) {
        return;
    }
    
    try {
        await apiCall(`/api/chat/conversations/${currentGroupData.id}/members/${userId}`, 'DELETE');
        
        // Refresh members list
        const membersData = await apiCall(`/api/chat/conversations/${currentGroupData.id}/members`);
        const availableUsers = await apiCall('/api/chat/users');
        
        currentGroupMembers = membersData.members;
        renderCurrentMembers();
        renderAvailableUsers(availableUsers.users);
        
    } catch (error) {
        console.error('Error removing member:', error);
        alert('Failed to remove member: ' + error.message);
    }
}

async function deleteGroup() {
    if (!currentGroupData || !currentUserId) {
        alert('Unable to delete group. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(currentUserId)) {
        alert('Only the group creator can delete the group');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
        return;
    }
    
    try {
        await apiCall(`/api/chat/conversations/${currentGroupData.id}`, 'DELETE');
        
        alert('Group deleted successfully');
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('groupManagementModal')).hide();
        
        // Refresh conversations and clear chat if this was the active conversation
        if (currentConversationId === currentGroupData.id) {
            currentConversationId = null;
            document.getElementById('chatMain').innerHTML = '<div class="no-conversation"><div class="text-center p-4"><i class="bx bx-chat" style="font-size: 3rem; color: #ccc;"></i><p class="text-muted mt-2">Select a conversation to start chatting</p></div></div>';
        }
        
        loadConversations();
        
    } catch (error) {
        console.error('Error deleting group:', error);
        alert('Failed to delete group: ' + error.message);
    }
}

// Enhanced selectConversation to subscribe to Echo channel
const _selectConversation = selectConversation;
selectConversation = async function(conversationId, conversationName) {
    await _selectConversation(conversationId, conversationName);
    try { subscribeToConversation(conversationId); } catch (e) { console.warn(e); }
}

// Initialize Echo on page load (non-blocking)
initEcho();
</script>
@endsection
