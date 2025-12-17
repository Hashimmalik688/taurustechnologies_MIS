# Chat System Testing Guide

## Prerequisites
1. **Reverb Server Running**
   ```powershell
   php artisan reverb:start
   ```
   Should show: `Reverb server started on 0.0.0.0:8080`

2. **Web Server Running**
   ```powershell
   php artisan serve
   ```
   Or use XAMPP/Apache

3. **Database Connected**
   - Ensure MySQL is running
   - Check `.env` has correct `DB_*` settings

4. **Assets Built**
   ```powershell
   npm run build
   ```

## Test Plan

### 1. Basic Page Load (Critical)
**Steps:**
1. Login to the application
2. Navigate to `/chat`
3. Open browser DevTools (F12) → Console tab

**Expected Results:**
- ✅ Page loads without errors
- ✅ Chat header shows "Team Chat" with gold gradient
- ✅ Search box visible
- ✅ Loading spinner appears briefly
- ✅ User list appears on left sidebar
- ✅ No "Error loading chats" message
- ✅ Console shows: `Echo initialized with Reverb broadcaster`
- ✅ No 401/403/500 errors in Network tab

**API Calls to Verify (Network Tab):**
- `GET /sanctum/csrf-cookie` → 204 No Content
- `GET /api/chat/conversations` → 200 OK
- `GET /api/chat/users` → 200 OK

### 2. User List Display
**Steps:**
1. Check left sidebar after page loads

**Expected Results:**
- ✅ Shows "People" section label
- ✅ Lists all active users (including superadmin)
- ✅ Shows user avatar/initials
- ✅ Shows user name
- ✅ No status filter applied (all users visible)

### 3. Start New Conversation
**Steps:**
1. Click on a user from the "People" list
2. Check chat area on right

**Expected Results:**
- ✅ User item becomes highlighted/active
- ✅ Chat area shows conversation header with user name
- ✅ Message input box appears at bottom
- ✅ "No messages yet" displays in center (if new chat)
- ✅ Console shows: WebSocket subscription to private channel

**API Call:**
- `POST /api/chat/conversations/direct` → 200 OK with conversation data

### 4. Send Text Message
**Steps:**
1. Select an active conversation
2. Type a message in the input box
3. Press Enter or click Send button

**Expected Results:**
- ✅ Message appears in chat area immediately
- ✅ Message shows sender avatar, name, text, and timestamp
- ✅ Input box clears after sending
- ✅ Scroll position moves to bottom
- ✅ Other user sees message in real-time (test with 2 browsers)

**API Call:**
- `POST /api/chat/messages` → 200 OK

**WebSocket Event:**
- Console shows: `ChatMessageSent event received`

### 5. Send Image Attachment
**Steps:**
1. Click attachment icon (📎)
2. Select an image file (JPG, PNG, GIF)
3. Confirm upload

**Expected Results:**
- ✅ File upload progress indicator (if large file)
- ✅ Image appears in chat as thumbnail
- ✅ Click image opens full-size preview
- ✅ Other user sees image in real-time

**API Call:**
- `POST /api/chat/messages` (FormData with attachment)

### 6. Send Audio Attachment
**Steps:**
1. Click attachment icon
2. Select an audio file (MP3, WAV, M4A)
3. Confirm upload

**Expected Results:**
- ✅ Audio message appears with audio player controls
- ✅ Shows audio icon and filename
- ✅ Play button works
- ✅ Duration displays (if available)
- ✅ Other user sees audio player in real-time

**Supported MIME Types:**
- `audio/mpeg` (MP3)
- `audio/wav` (WAV)
- `audio/mp4` (M4A)
- `audio/ogg` (OGG)
- `audio/webm` (WEBM)

### 7. Search Conversations
**Steps:**
1. Type in search box at top of sidebar
2. Test with user names and message content

**Expected Results:**
- ✅ Conversation list filters in real-time
- ✅ Shows matching conversations/users
- ✅ Clear search shows all again

### 8. Real-Time Updates (WebSocket)
**Steps:**
1. Open chat in 2 different browsers (or incognito)
2. Login as different users
3. Send messages from one browser

**Expected Results:**
- ✅ Messages appear instantly in both browsers
- ✅ New conversation appears in sidebar automatically
- ✅ Unread count updates (if implemented)
- ✅ Typing indicators work (if implemented)

### 9. Delete Message
**Steps:**
1. Hover over own message
2. Click delete icon
3. Confirm deletion

**Expected Results:**
- ✅ Message removed from chat
- ✅ Other users see removal in real-time
- ✅ "Message deleted" placeholder (optional)

**API Call:**
- `DELETE /api/chat/messages/{id}` → 200 OK

### 10. Chat Backup
**Steps:**
1. Run backup command:
   ```powershell
   php artisan chat:backup
   ```

**Expected Results:**
- ✅ Creates JSON file in `storage/app/chat-backups/`
- ✅ Filename format: `chat-backup-YYYY-MM-DD.json`
- ✅ Contains all conversations, messages, participants, attachments
- ✅ Optional: with `--copy-attachments` flag, copies files to backup folder

## Common Issues & Troubleshooting

### Issue: "Error loading chats"
**Fix:**
1. Check if Reverb server is running
2. Clear cache: `php artisan config:clear`
3. Check `.env` has `BROADCAST_DRIVER=reverb`
4. Verify user is logged in: check session cookie in DevTools

### Issue: Messages not appearing in real-time
**Fix:**
1. Restart Reverb server: `php artisan reverb:start`
2. Check WebSocket connection in Console: should see "Echo initialized"
3. Verify port 8080 is accessible (firewall)
4. Check browser allows WebSocket connections

### Issue: Image/Audio uploads fail
**Fix:**
1. Check `storage/app/public/chat-attachments` folder exists
2. Verify symlink: `php artisan storage:link`
3. Check file permissions on storage folder
4. Verify `upload_max_filesize` in `php.ini` (default 10MB)

### Issue: CSRF token mismatch
**Fix:**
1. Clear browser cache and cookies
2. Verify `meta` tag in page source: `<meta name="csrf-token" content="...">`
3. Check session driver in `.env`: should be `file` or `database`, not `array`

### Issue: Sanctum 401 Unauthorized
**Fix:**
1. Verify user is authenticated on `/chat` page load
2. Check `config/sanctum.php` includes correct stateful domains
3. Ensure cookies are not blocked (SameSite issues)
4. Run: `php artisan config:cache`

## Performance Testing

### Load Test
1. Create 50+ conversations
2. Send 100+ messages in a conversation
3. Verify scrolling is smooth
4. Check memory usage in DevTools

### Concurrent Users
1. Open chat in 10+ browser tabs (different users)
2. Send messages simultaneously
3. Verify all receive updates
4. Check Reverb server console for errors

## Browser Compatibility
Test on:
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (macOS)
- ⚠️ Mobile browsers (responsive design)

## Security Testing
1. **CSRF Protection**: Try API call without token → should fail
2. **Authentication**: Access API endpoints without login → should 401
3. **Authorization**: Try to read other's private conversations → should fail
4. **File Upload**: Try uploading executable files → should reject
5. **XSS**: Try sending `<script>` tags in messages → should sanitize

## Acceptance Criteria
- [ ] All users can see each other in chat (no status filter)
- [ ] Text messages send and receive in real-time
- [ ] Image attachments upload and display correctly
- [ ] Audio attachments upload with playable controls
- [ ] No external dependencies (Pusher removed, pure Reverb)
- [ ] Chat works locally without internet connection
- [ ] Backup command exports all data to JSON
- [ ] Modern card-based UI with gold gradient header
- [ ] No console errors on page load
- [ ] All API calls return 200 OK (no 401/403/500)

## Next Steps (Optional Enhancements)
1. Schedule daily backups via Laravel scheduler
2. Add admin UI button to trigger backups
3. Implement file size limits for attachments
4. Add typing indicators
5. Add read receipts/message status
6. Add group chat creation UI
7. Add emoji picker
8. Add message reactions
9. Add notification sounds
10. Add mobile responsive design improvements
