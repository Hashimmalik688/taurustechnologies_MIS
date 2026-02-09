<!-- Chat Toast Notification Template -->
<div id="chatToastContainer" style="position:fixed;bottom:20px;right:20px;z-index:10000;max-width:350px;"></div>

<script src="{{ asset('js/emoji-picker.min.js') }}"></script>
<script>
// Chat Toast Notifications
window.ChatToast = {
    show: function(message, conversationId, conversationName, senderId, senderName) {
        if (senderId === currentUserId) return; // Don't show for own messages
        
        const container = document.getElementById('chatToastContainer');
        const toastId = 'toast-' + Date.now();
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'chat-toast-notification';
        toast.style.cssText = 'background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.15);padding:15px;margin-top:10px;animation:slideIn 0.3s ease;cursor:pointer;border-left:4px solid #556ee6;';
        
        toast.innerHTML = `
            <div style="display:flex;align-items:start;gap:10px;">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:16px;flex-shrink:0;">
                    ${senderName.charAt(0).toUpperCase()}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:14px;color:#2c3e50;margin-bottom:2px;">${senderName}</div>
                    <div style="font-size:13px;color:#7f8c8d;margin-bottom:2px;font-style:italic;">${conversationName}</div>
                    <div style="font-size:13px;color:#34495e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${message.length > 60 ? message.substring(0, 60) + '...' : message}</div>
                    <div style="margin-top:8px;display:flex;gap:8px;">
                        <button onclick="ChatToast.reply('${toastId}', ${conversationId}, '${conversationName}')" style="background:#556ee6;color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:12px;cursor:pointer;font-weight:500;">Reply</button>
                        <button onclick="ChatToast.dismiss('${toastId}')" style="background:#f1f3f4;color:#5a6169;border:none;padding:5px 12px;border-radius:6px;font-size:12px;cursor:pointer;font-weight:500;">Dismiss</button>
                    </div>
                </div>
            </div>
        `;
        
        // Click toast to open conversation
        toast.addEventListener('click', (e) => {
            if (e.target.tagName !== 'BUTTON') {
                loadMessages(conversationId, conversationName);
                currentConversationId = conversationId;
                ChatToast.dismiss(toastId);
            }
        });
        
        container.appendChild(toast);
        
        // Auto dismiss after 10 seconds
        setTimeout(() => ChatToast.dismiss(toastId), 10000);
        
        // Play notification sound
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGS58OScTgwOUKnm8LloHAU1ldf0ynUrBS13yO/ckEAKFGCz6emoVRQJRp/h8rtoHwYrc8nw3I0+ChFbs+rxplkYCD2a3va6YxwFM5PX9MlyLAUmc8nx25A+ChNftOrpqFUUC0af4PG7aB8GM3PK8N2OPQsTW7Tq8aZZGAg7mdz2vWQcBDWU1vPJdC0FJnPK8tuPPgsUX7Tq6ahUFAtFn+Dxu2cfBjByz/HbkEAKFl216vGmWhgIMpnc9r1lHAQ1k9b0yXUtBSZzy/PbjT4LE1+06umqVRQKRZ/h8rxoHgczcs/w3ZA/Chdes+jxqFoXCDSa2/a7ZRwFNZTX9Ml0LQUlc8rz2Y0+CxJctOrwp1UVCkSg4PK7aB8GM3HK8dybPwoRXrTp76lWGQc2mdz3uWcdBTWU1/PJcy0FJ3LL8tmPOwsUYLLo7qhVFQtFnuDxu2gfBTJxyPDdjT4LFF6z6O+oVhcIM5jc9rpnHQY1k9f1yHMtBSdxy/LZkD4KFGGy6O6oVRUKRp/h8rxoHwU0ccjw3I8+ChRes+jvqFYXCDSZ3Pe6aBwFNJLW88l0LAUmc8ry2Y8/CxRgsujuqFQVCkSf4PG8aB8FNHHJ8N2OPwsUXrPo76dWFwc0mdz2umgcBDOT1vLIciwGJnPK8tmOPwsUYbPo7qlVFQpGn+DxvGgfBDVxyPDdjj8LFGCy6O+oVhcINJnc9r1oHAQzktXzyXMrBilzy/LZjz4LFGGy6O6pVBcIRp/g8bxoIAY0ccnw3ZA+CxNes+jvp1YYBzWZ3Pa8aRwFNJLW88l0LAUpcs3x2ZA+CxRfsunwqVQWCkah4fK8aB8FNHHJ8N2PPgsVX7Xo76lVGAg2mdz3vGodBjWR1vPIcywFK3LL89mOPgsUXrLo7qhVFgtGoODxvGkeBzVzyPDdjj8KFWCz6PCpVRgIN5nc9r1pHQY1ktb0yHQrBSpzy/LZkT4MFF+z6e6oVBUKRqDg8rpoHgY0csnw3I8/ChVgs+jwqVUWCTia3Pa8aR0GM5PW88hzLAUpd8nw2o9AChVgs+jvqFYWCUah4fK8aB8GMnLI8d2OPwoWYLTp8KlVFQk5mt32u2kdBjST1/TJcywGKHXM8tuRPwoXYLLp76hUFgpGoODyu2gfBTNxyPDdjj4LFWCy6PCnVhUJOJrc9rxpHAY0k9b0yXMsBil1y/LajD8KF1+06e+pVRYKRqDf8rxpHQYyccjw3Y8+ChVes+jwqVYWCTma3fa8aRwGNZLW9MlzKwYpdsvx2o0/ChZftOjvqVUVCkag4PK7aB8GN' +
            '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');
            audio.volume = 0.3;
            audio.play().catch(() => {});
        } catch(e) {}
    },
    
    reply: function(toastId, conversationId, conversationName) {
        loadMessages(conversationId, conversationName);
        currentConversationId = conversationName;
        document.getElementById('messageInput').focus();
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

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize emoji picker button
document.addEventListener('DOMContentLoaded', function() {
    // Add emoji button to message input if exists
    const messageInputContainer = document.querySelector('.message-input-container') || document.querySelector('#messageInput')?.parentElement;
    if (messageInputContainer && !document.getElementById('emojiPickerBtn')) {
        const emojiBtn = document.createElement('button');
        emojiBtn.id = 'emojiPickerBtn';
        emojiBtn.type = 'button';
        emojiBtn.innerHTML = '😊';
        emojiBtn.style.cssText = 'border:none;background:transparent;font-size:20px;padding:8px;cursor:pointer;border-radius:6px;transition:background 0.2s;';
        emojiBtn.onmouseenter = () => emojiBtn.style.background = '#f0f0f0';
        emojiBtn.onmouseleave = () => emojiBtn.style.background = 'transparent';
        emojiBtn.onclick = () => {
            const input = document.getElementById('messageInput');
            if (input) window.EmojiPicker.show(input);
        };
        
        // Insert before send button or at end
        const sendBtn = document.getElementById('sendButton');
        if (sendBtn) {
            sendBtn.parentElement.insertBefore(emojiBtn, sendBtn);
        } else {
            messageInputContainer.appendChild(emojiBtn);
        }
    }
});

// Fix conversation search
document.getElementById('searchConversations')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const conversations = document.querySelectorAll('.conversation-item');
    
    conversations.forEach(conv => {
        const name = conv.querySelector('.conversation-name')?.textContent.toLowerCase() || '';
        const lastMsg = conv.querySelector('.conversation-last-message')?.textContent.toLowerCase() || '';
        
        if (name.includes(searchTerm) || lastMsg.includes(searchTerm)) {
            conv.style.display = '';
        } else {
            conv.style.display = 'none';
        }
    });
});
</script>
