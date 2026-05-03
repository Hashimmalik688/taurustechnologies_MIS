@use('App\Support\Roles')
@extends('layouts.master')

@section('title')
    Team Chat
@endsection

@section('css')
    @vite(['resources/css/chat.css'])
    <style>
        .chat-container * { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
        .message-text, .chat-search-input, #messageInput, input, textarea, .form-control { -webkit-user-select: text !important; -moz-user-select: text !important; -ms-user-select: text !important; user-select: text !important; }
        .chat-container img, .chat-container button { -webkit-user-drag: none; -moz-user-drag: none; user-drag: none; }
        body { overflow-x: hidden; }
        /* Prevent FOUC — hide until CSS loads */
        .chat-wrapper { visibility: visible; }
        
        /* Community Announcement Pop-up Notification */
        .announcement-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 420px;
            background: var(--bs-card-bg);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
            z-index: 999999;
            animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow: hidden;
            display: none;
        }
        .announcement-popup.show {
            display: block;
        }
        .announcement-popup.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }
        @keyframes slideInRight {
            from { transform: translateX(450px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(450px); opacity: 0; }
        }
        .announcement-popup-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--bs-surface-200);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .announcement-popup-body {
            padding: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .announcement-popup-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            color: var(--bs-surface-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .announcement-popup-close:hover {
            background: var(--bs-surface-100);
            color: var(--bs-surface-600);
        }
        .announcement-popup-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #d4af37, #e8c84a);
            animation: progressBar 20s linear forwards;
        }
        @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        /* Floating Re-open Button */
        .announcement-float-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4af37, #c5a028);
            color: #fff;
            border: none;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
            cursor: pointer;
            z-index: 999998;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s;
            animation: pulse 2s ease-in-out infinite;
        }
        .announcement-float-btn.show {
            display: flex;
        }
        .announcement-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
        }
        .announcement-float-btn .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f46a6a;
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            border: 2px solid white;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* ── Master Search Results Panel ─────────────────── */
        .chat-search-box { position: relative; }
        .chat-search-results {
            position: absolute; z-index: 300; left: 0; right: 0; top: calc(100% + 4px);
            background: var(--bs-body-bg, #fff); border: 1px solid rgba(0,0,0,.08);
            border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,.13);
            max-height: 420px; overflow-y: auto;
        }
        .search-result-section {
            padding: .45rem .8rem .2rem; font-size: .6rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .5px; color: var(--bs-surface-400, #64748b);
            border-bottom: 1px solid rgba(0,0,0,.04); background: rgba(248,250,252,.6);
        }
        .search-result-item {
            display: flex; align-items: flex-start; gap: .55rem; padding: .55rem .8rem;
            cursor: pointer; transition: background .15s;
        }
        .search-result-item:hover { background: rgba(212,175,55,.07); }
        .search-result-avatar {
            width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
            background: rgba(212,175,55,.18); color: #a07a10; font-weight: 700; font-size: .75rem;
            display: flex; align-items: center; justify-content: center;
        }
        .search-result-info { flex: 1; min-width: 0; }
        .search-result-name { font-size: .75rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .search-result-preview { font-size: .68rem; color: var(--bs-surface-400, #64748b); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .search-no-results { padding: 1.1rem; text-align: center; font-size: .75rem; color: var(--bs-surface-400); }

        /* ── Read Ticks ───────────────────────────────────── */
        .msg-ticks { font-size: .8rem; color: #94a3b8; margin-left: 3px; vertical-align: middle; line-height: 1; }
        .msg-ticks.ticks-read { color: #34c38f; }

        /* ── Typing Indicator Bubble ───────────────────── */
        .typing-indicator-row {
            display: flex;
            align-items: flex-end;
            gap: .5rem;
            padding: .1rem 0 .25rem;
            animation: msgIn .2s ease;
        }
        .typing-indicator-row .typing-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg,#d4af37,#c5a028);
            color:#fff; display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.72rem; flex-shrink:0;
        }
        .typing-bubble {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: var(--bs-card-bg, #fff);
            border: 1px solid var(--bs-border-color, #e5e7eb);
            border-radius: 18px 18px 18px 4px;
            padding: 10px 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .typing-dots { display:inline-flex; gap:5px; align-items:center; }
        .typing-dots span {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%;
            background: #9ca3af;
            animation: typingBounce 1.4s infinite ease-in-out;
        }
        .typing-dots span:nth-child(1) { animation-delay: 0s; }
        .typing-dots span:nth-child(2) { animation-delay: .18s; }
        .typing-dots span:nth-child(3) { animation-delay: .36s; }
        @keyframes typingBounce {
            0%,60%,100% { transform: translateY(0); background: #9ca3af; }
            30% { transform: translateY(-6px); background: #4b5563; }
        }
        /* Optimistic (sending) message */
        .message-item.message-sending .message-bubble {
            opacity: 0.6;
        }
        .msg-sending-spinner {
            display: inline-block; width: 10px; height: 10px;
            border: 1.5px solid #94a3b8; border-top-color: transparent;
            border-radius: 50%; animation: spin .7s linear infinite;
            margin-left: 3px; vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Online Dot ───────────────────────────────────── */
        .online-dot {
            position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px;
            border-radius: 50%; background: #34c38f; border: 2px solid var(--bs-card-bg, #fff);
            pointer-events: none;
        }
        .conversation-avatar { position: relative; }

        /* ── Pinned Banner ────────────────────────────────── */
        .pinned-banner {
            display: flex; align-items: center; gap: .5rem;
            padding: .35rem .9rem; background: rgba(212,175,55,.1);
            border-bottom: 1px solid rgba(212,175,55,.3); font-size: .73rem;
            cursor: pointer;
        }
        .pinned-banner i { color: #d4af37; flex-shrink: 0; }
        .pinned-banner-text { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--bs-body-color); }
        .pinned-banner-close { background: none; border: none; padding: 0 .2rem; color: #94a3b8; font-size: .8rem; cursor: pointer; }
        .pinned-banner-close:hover { color: #f46a6a; }

        /* ── Reply Context Bar (above input) ─────────────── */
        .reply-context-bar {
            display: flex; align-items: center; gap: .5rem;
            padding: .35rem .9rem; background: rgba(212,175,55,.07);
            border-top: 1px solid rgba(212,175,55,.2); font-size: .73rem;
        }
        .reply-context-bar i { color: #d4af37; flex-shrink: 0; }
        .reply-context-text { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .reply-context-close { background: none; border: none; padding: 0 .25rem; color: #94a3b8; cursor: pointer; }
        .reply-context-close:hover { color: #f46a6a; }

        /* ── Reply Preview (inside message bubble) ────────── */
        .reply-preview {
            background: rgba(0,0,0,.06); border-left: 3px solid #d4af37;
            border-radius: 4px; padding: .25rem .5rem; margin-bottom: .35rem;
            font-size: .7rem; max-width: 100%;
        }
        .message-sender .reply-preview { background: rgba(255,255,255,.18); }
        .reply-preview-name { font-weight: 700; color: #d4af37; margin-bottom: .1rem; }
        .reply-preview-text { color: inherit; opacity: .8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ── Reaction Pills ───────────────────────────────── */
        .reaction-pills {
            display: flex; flex-wrap: wrap; gap: 3px; margin-top: 4px;
        }
        .reaction-pill {
            display: inline-flex; align-items: center; gap: 3px;
            background: rgba(0,0,0,.06); border: 1px solid rgba(0,0,0,.08);
            border-radius: 30px; padding: 1px 7px; font-size: .75rem;
            cursor: pointer; transition: all .15s; user-select: none;
        }
        .reaction-pill:hover { background: rgba(212,175,55,.15); border-color: rgba(212,175,55,.4); }
        .reaction-pill.my-reaction { background: rgba(212,175,55,.18); border-color: #d4af37; }
        .message-sender .reaction-pill { background: rgba(255,255,255,.2); }
        .message-sender .reaction-pill.my-reaction { background: rgba(255,255,255,.35); border-color: rgba(255,255,255,.7); }

        /* ── Reaction Picker Popup ────────────────────────── */
        .reaction-picker-popup {
            position: absolute; z-index: 400;
            background: var(--bs-body-bg, #fff); border: 1px solid rgba(0,0,0,.08);
            border-radius: 30px; box-shadow: 0 4px 20px rgba(0,0,0,.15);
            padding: 5px 8px; display: flex; gap: 4px; white-space: nowrap;
        }
        .reaction-picker-popup button {
            background: none; border: none; font-size: 1.3rem; cursor: pointer;
            border-radius: 50%; width: 36px; height: 36px; transition: transform .15s;
        }
        .reaction-picker-popup button:hover { transform: scale(1.3); background: rgba(0,0,0,.05); }

    </style>
@endsection
@section('content')

    <div class="chat-wrapper">
        <div class="chat-container">
            <!-- Pill Tab Bar — pipe-pill pattern (outside sidebar so it stays visible) -->
            <div class="chat-tab-bar">
                <button class="chat-tab-pill active" data-tab="chats"><i class="bx bx-message-dots"></i> Messages</button>
                <button class="chat-tab-pill" data-tab="groups"><i class="bx bx-group"></i> Groups</button>
                <button class="chat-tab-pill" data-tab="communities"><i class="bx bx-buildings"></i> Communities</button>
                <button class="chat-tab-pill" data-tab="people"><i class="bx bx-user"></i> People</button>
            </div>

            <div class="chat-body">
            <!-- LEFT PANEL — Conversations / Groups / Communities -->
            <div class="chat-sidebar">

                <!-- Chats Tab -->
                <div id="chats-tab" class="chat-sidebar-content active">
                    <div class="chat-sidebar-header">
                        <h5><i class="bx bx-message-dots"></i> Messages</h5>
                    </div>

                    <div class="chat-search-box">
                        <input type="text" id="searchConversations" placeholder="Search conversations & messages..." class="chat-search-input">
                        <div id="chatSearchResults" class="chat-search-results" style="display:none;"></div>
                    </div>

                    <div class="conversations-list" id="conversationsList" style="flex:1; overflow-y:auto; overflow-x:hidden;">
                        <div class="loading-conversations">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p style="font-size:.78rem; margin-top:.5rem;">Loading chats...</p>
                        </div>
                    </div>
                </div>

                <!-- Groups Tab -->
                <div id="groups-tab" class="chat-sidebar-content">
                    <div class="chat-sidebar-header">
                        <h5><i class="bx bx-group"></i> Groups</h5>
                        <button class="btn" data-bs-toggle="modal" data-bs-target="#newChatModal" title="Create new group">
                            <i class="bx bx-plus"></i> Create
                        </button>
                    </div>

                    <div class="chat-search-box">
                        <input type="text" id="searchGroups" placeholder="Search groups..." class="chat-search-input">
                    </div>

                    <div class="group-conversations-list" id="groupConversationsList" style="flex:1; overflow-y:auto; overflow-x:hidden;">
                        <!-- Group conversations will be loaded here -->
                    </div>
                </div>

                <!-- Communities Tab -->
                <div id="communities-tab" class="chat-sidebar-content">
                    <div class="chat-sidebar-header">
                        <h5><i class="bx bx-buildings"></i> Communities</h5>
                        @if(Auth::user()->hasRole([Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR]))
                            <button class="btn" data-bs-toggle="modal" data-bs-target="#newCommunityModal" title="Create community">
                                <i class="bx bx-plus"></i> New
                            </button>
                        @endif
                    </div>

                    <div class="chat-search-box">
                        <input type="text" id="searchCommunities" placeholder="Search communities..." class="chat-search-input">
                    </div>

                    <div class="communities-list" id="communitiesList" style="flex:1; overflow-y:auto; overflow-x:hidden;">
                        <!-- Communities will be loaded here -->
                    </div>
                </div>

                <!-- People Tab — hidden in sidebar, uses full page -->
                <div id="people-tab" class="chat-sidebar-content">
                    <!-- People tab uses full container width when active -->
                </div>
            </div>

            <!-- CHAT MAIN AREA -->
            <div class="chat-main" id="chatMain">
                <div class="chat-welcome">
                    <i class="bx bx-message-square-dots"></i>
                    <h4>Welcome to Taurus Chat</h4>
                    <p>Select a conversation to start messaging or create a new chat</p>
                </div>
            </div>

            <!-- PEOPLE FULL PAGE — hub-header + hub-grid pattern -->
            <div class="people-main" id="peopleMain" style="display: none;">
                <div class="people-header">
                    <div class="d-flex justify-content-between align-items-center" style="padding: 0 .5rem;">
                        <div>
                            <h2><i class="bx bx-user"></i> Team Directory</h2>
                            <p>Connect with your colleagues</p>
                        </div>
                        <div class="position-relative">
                            <i class="bx bx-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: .8rem; color: var(--bs-secondary-color, #6b7280); pointer-events: none;"></i>
                            <input type="text" id="searchPeopleCards" placeholder="Search people..." class="people-search-input">
                        </div>
                    </div>
                </div>
                <div style="flex:1; overflow-y:auto; padding: 0;">
                    <div id="peopleCards" class="people-cards-grid">
                        <!-- People cards will be loaded here -->
                    </div>
                </div>
            </div>
            </div> <!-- /.chat-body -->
        </div>
    </div>
    <div class="modal fade" id="newChatModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Create Group</h5>
 <p class="u-fs-13 text-surface-500" style="margin: 4px 0 0 0">Create a group chat</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Group Chat Section -->
                    <div id="groupChatSection">
 <div class="mb-4" >
 <label class="form-label u-fw-600 text-surface-700 mb-2" >Group Name</label>
 <input type="text" id="groupName" class="form-control u-rounded-8" placeholder="Enter group name..." >
                        </div>
                        
 <div class="mb-4" >
 <label class="form-label label-value" >Group Picture</label>
 <div class="d-flex align-items-center" style="gap: 16px">
 <div class="u-w-80 rounded-circle d-flex align-items-center justify-content-center text-white u-fw-700 overflow-hidden u-fs-32" id="groupAvatarPreview" style="height: 80px; background: linear-gradient(135deg, #d4af37, #c5a028)">
                                    <i class="bx bx-group" style="font-size: 36px;"></i>
                                </div>
 <div class="flex-grow-1" >
 <input type="file" class="form-control u-rounded-8" id="groupAvatar" accept="image/*" >
 <small class="d-block text-surface-500 u-fs-12" style="margin-top: 6px">Optional: Upload a profile picture for your group</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
 <label class="form-label u-fw-600 text-surface-700 mb-2" >Add Members</label>
 <input type="text" id="searchGroupUsers" class="form-control u-rounded-8" placeholder="Search and select members..." >
                        </div>

                        <div class="mb-3">
 <div id="groupUsersList" class="users-list u-overflow-y-auto" style="max-height: 250px">
                                <!-- Users will be populated here -->
                            </div>
                        </div>

                        <div id="selectedMembers">
 <label class="form-label u-fw-600 text-surface-700 text-surface-muted mb-3">Selected Members <span id="memberCount" >(0)</span></label>
 <div class="d-flex bg-surface-50 u-rounded-8 flex-wrap u-gap-8 border-surface-200" id="membersList" style="min-height: 36px; padding: 8px">
                                <!-- Selected members will appear here -->
                            </div>
                        </div>
                    </div>
                </div>
 <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
 <button type="button" class="btn btn-primary" id="createChatBtn">Create Group</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Group Management Modal - Redesigned -->
<div class="modal fade" id="groupManagementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
 <h5 class="modal-title u-fw-700 text-surface-700" >Manage Group</h5>
 <p class="u-fs-13 text-surface-500" style="margin: 4px 0 0 0">Edit group settings and members</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Group Information Section -->
                <div style="margin-bottom: 32px;">
 <h6 class="u-fw-700 text-surface-700 u-fs-14 text-surface-500 mb-3 text-uppercase u-ls-05">Group Information</h6>
                    
                    <div class="mb-3">
 <label class="label-value" >Group Picture</label>
 <div class="d-flex align-items-center" style="gap: 16px">
 <div class="rounded-circle d-flex align-items-center justify-content-center text-white u-fw-700 overflow-hidden u-w-60" id="groupAvatarPreview" style="height: 60px; background: linear-gradient(135deg, #d4af37, #c5a028); font-size: 20px">
                                <i class="bx bx-group" style="font-size: 24px;"></i>
                            </div>
 <div class="flex-grow-1" >
 <input type="file" class="form-control u-rounded-8 u-fs-13" id="groupAvatar" accept="image/*" >
 <small class="d-block text-surface-500 u-fs-11" style="margin-top: 4px">Upload a profile picture</small>
                            </div>
 <button type="button" class="btn text-white border-0 u-rounded-8 u-fw-600 u-cursor-pointer py-2 px-3 bg-ui-success u-ws-nowrap" onclick="updateGroupAvatar()">Save Picture</button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
 <label class="label-value" >Group Name</label>
 <input type="text" id="groupNameEdit" class="form-control u-rounded-8" placeholder="Enter group name" >
                    </div>
                    <div class="mb-3">
 <label class="label-value" >Created By</label>
 <p class="bg-surface-50 u-rounded-8 text-surface-500 u-fs-14 m-0" id="groupCreator" style="padding: 10px 12px">-</p>
                    </div>
 <div class="d-flex u-gap-8">
 <button type="button" class="btn flex-grow-1 text-white border-0 u-rounded-8 u-fw-600 u-cursor-pointer py-2 px-3" onclick="updateGroupName()" style="background: linear-gradient(135deg, #d4af37, #c5a028); transition: all 0.3s ease">Update Name</button>
 <button type="button" class="btn btn-outline-danger flex-grow-1 text-ui-danger-dark u-rounded-8 u-fw-600 u-cursor-pointer py-2 px-3 bg-transparent" onclick="deleteGroup()" id="deleteGroupBtn" style="border: 1px solid var(--bs-ui-danger)">Delete Group</button>
                    </div>
                </div>

                <!-- Members Management Section -->
                <div>
 <h6 class="u-fw-700 text-surface-700 u-fs-14 text-surface-500 mb-3 text-uppercase u-ls-05">Members</h6>
                    
                    <!-- Current Members -->
                    <div class="mb-4">
 <label class="u-fw-600 text-surface-700 d-block mb-3">Current Members</label>
 <div id="currentMembersList" class="members-list d-flex flex-column u-gap-8">
                            <!-- Members will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Add New Members -->
                    <div class="border-top-surface" style="padding-top: 16px">
 <label class="u-fw-600 text-surface-700 d-block mb-3">Add Members</label>
 <div id="availableUsersList" class="users-list u-overflow-y-auto u-max-h-200">
                            <!-- Available users will be loaded here -->
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

<!-- Forward Message Modal -->
<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="padding:14px 18px;border-bottom:1px solid rgba(0,0,0,.06);">
                <h6 class="modal-title mb-0" id="forwardModalLabel" style="font-size:.85rem;font-weight:600;">
                    <i class="bx bx-share" style="color:#556ee6;"></i> Forward to...
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size:.65rem;"></button>
            </div>
            <div class="modal-body p-0">
                <div style="padding:10px 14px;">
                    <input type="text" id="forwardSearchInput" class="form-control form-control-sm" placeholder="Search conversations..." style="font-size:.8rem;border-radius:8px;">
                </div>
                <div id="forwardConversationList" style="max-height:300px;overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>

<!-- GIF Picker Modal -->
<div class="modal fade" id="gifPickerModal" tabindex="-1" aria-labelledby="gifPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gifPickerModalLabel">
                    <i class="bx bx-image"></i> Choose a GIF
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Input -->
 <div class="mb-4" >
 <input type="text" id="gifSearchInput" class="form-control u-rounded-8" placeholder="Search GIFs (e.g., happy, dance, celebrate)..." style="border: 2px solid var(--bs-surface-200); padding: 12px 16px">
                </div>
                
                <!-- GIF Grid -->
 <div class="u-overflow-y-auto u-gap-12 u-max-h-400" id="gifGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); padding: 4px">
                    <!-- GIFs will be loaded here -->
 <div class="text-center" style="grid-column: 1 / -1; padding: 40px; color: var(--bs-secondary-color)">
                        <i class="bx bx-search mb-3 u-opacity-50" style="font-size: 48px"></i>
                        <p>Search for GIFs above</p>
                    </div>
                </div>
                
                <!-- Loading State -->
 <div class="d-none text-center" id="gifLoading" style="padding: 40px; color: var(--bs-secondary-color)">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading GIFs...</p>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bs-secondary-bg); border-top: 1px solid var(--bs-border-color); padding: 16px 24px;">
                <small style="color: var(--bs-secondary-color); margin-right: auto;">
                    <i class="bx bx-info-circle" style="margin-right: 4px;"></i>Powered by Tenor
                </small>
 <button type="button" class="btn u-rounded-8 u-fw-600 py-2 px-3" data-bs-dismiss="modal" style="background: var(--bs-body-bg); border: 1px solid var(--bs-border-color); color: var(--bs-body-color)">Close</button>
            </div>
        </div>
    </div>
</div>

    <!-- Community Members Modal -->
    <div class="modal fade" id="communityMembersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Manage Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="manageCommunityId">
                    
                    <!-- Add Member Section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Add New Member</label>
                        <div class="input-group">
                            <select class="form-select" id="newCommunityMemberSelect">
                                <option value="">Select user...</option>
                                <!-- Populated by JS -->
                            </select>
                            <button class="btn btn-primary" id="addCommunityMemberBtn">Add</button>
                        </div>
                    </div>

                    <!-- Current Members List -->
                    <div>
                        <label class="form-label fw-bold mb-2">Current Members</label>
 <div id="communityMembersList" class="list-group list-group-flush border rounded u-overflow-y-auto u-max-h-400">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Community Modal -->
    <div class="modal fade" id="editCommunityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold">Edit Community</h5>
                        <p class="text-muted small mb-0">Update community settings</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editCommunityId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Community Name</label>
                        <input type="text" class="form-control" id="editCommunityName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="editCommunityDescription" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Color</label>
                        <input type="color" class="form-control form-control-color w-100" id="editCommunityColor" style="height: 50px;">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
 <input class="form-check-input u-cursor-pointer" type="checkbox" id="editCommunityPostingRestricted" >
 <label class="form-check-label u-cursor-pointer" for="editCommunityPostingRestricted" >
                                <strong>Restrict Posting</strong>
                                <br>
                                <small class="text-muted">Only creator and authorized members can post announcements</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCommunitySettingsBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Context Menu (Hidden by default) -->
 <div id="communityContextMenu" class="dropdown-menu d-none position-fixed u-z-9999">
        <a class="dropdown-item" href="#" id="ctxEditCommunity">
            <i class="bx bx-edit me-2"></i> Edit Community
        </a>
        <a class="dropdown-item" href="#" id="ctxManageMembers">
            <i class="bx bx-user-plus me-2"></i> Manage Members
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="#" id="ctxDeleteCommunity">
            <i class="bx bx-trash me-2"></i> Delete Community
        </a>
    </div>

    <!-- New Community Modal - Redesigned -->
    <div class="modal fade" id="newCommunityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
 <h5 class="modal-title u-fw-700 text-surface-700" >Create Community</h5>
 <p class="u-fs-13 text-surface-500" style="margin: 4px 0 0 0">Organize groups and channels</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCommunityForm">
                        @csrf
 <div class="mb-4" >
 <label class="form-label label-value" >Community Name</label>
 <input type="text" class="form-control u-rounded-8" id="communityName" name="name" placeholder="e.g., Product Team" required >
                        </div>

 <div class="mb-4" >
 <label class="form-label label-value" >Description</label>
 <textarea class="form-control u-rounded-8" id="communityDescription" name="description" placeholder="What's this community about?" rows="2" ></textarea>
 <small class="d-block text-surface-500 u-fs-12" style="margin-top: 6px">Optional: Help members understand the purpose</small>
                        </div>

 <div class="mb-4" >
 <label class="form-label label-value" >Community Color</label>
 <div class="d-flex align-items-center u-gap-12">
 <div class="rounded-circle d-flex align-items-center justify-content-center text-white u-fw-700 u-w-60" id="communityColorPreview" style="height: 60px; background: #d4af37; font-size: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.15)">
                                    <i class="bx bx-bullhorn" style="font-size: 28px;"></i>
                                </div>
 <div class="flex-grow-1" >
 <input type="color" class="form-control form-control-color u-rounded-8 u-cursor-pointer w-100" id="communityColor" name="color" value="#667eea" style="height: 50px">
 <small class="d-block text-surface-500 u-fs-12" style="margin-top: 6px">Pick a color to represent this community</small>
                                </div>
                            </div>
                        </div>

 <div class="mb-4" >
 <label class="form-label label-value" >Add Members (Optional)</label>
 <div class="d-flex u-gap-8" style="margin-bottom: 10px">
 <select class="form-select u-rounded-8" id="communityMemberSelect" >
                                    <option value="">Select a member to add...</option>
                                </select>
 <button class="text-white border-0 u-rounded-8 u-cursor-pointer u-fw-600 py-2 px-3 u-ws-nowrap" type="button" id="addMemberToCommunityBtn" style="background: linear-gradient(135deg, #d4af37, #c5a028)">
                                    Add
                                </button>
                            </div>
 <div class="d-flex mt-2 flex-wrap u-gap-8" id="communityMembersDisplay">
                                <!-- Members will be added here -->
                            </div>
                        </div>
                        
                        <!-- Hidden input to store member IDs -->
                        <input type="hidden" id="communityMemberIds" name="member_ids" value="[]">
                    </form>
                </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
 <button type="button" class="btn btn-primary" id="createCommunityBtn">Create Community</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Announcement Pop-up Notification -->
    <div id="announcementPopup" class="announcement-popup">
        <button class="announcement-popup-close" onclick="closeAnnouncementPopup()">
            <i class="bx bx-x" style="font-size: 20px;"></i>
        </button>
        <div class="announcement-popup-header">
 <div class="rounded-circle d-flex align-items-center justify-content-center text-white u-w-40" id="popupCommunityIcon" style="height: 40px; background: #d4af37; font-size: 20px">
                <i class="bx bx-bullhorn"></i>
            </div>
 <div class="flex-grow-1" >
 <div class="u-fw-700 text-surface-700" id="popupCommunityName" style="font-size: 16px">Community</div>
 <div class="u-fs-12 text-surface-500 d-flex align-items-center u-gap-4">
 <i class="bx bx-bullhorn u-fs-13" ></i>
                    <span>New Announcement</span>
                </div>
            </div>
        </div>
        <div class="announcement-popup-body">
 <div class="d-inline-flex align-items-center u-rounded-12 u-fs-12 u-fw-600 u-gap-4 mb-3" id="popupPriorityBadge" style="padding: 4px 12px">
                <i class="bx bx-info-circle"></i>
                <span>Normal</span>
            </div>
 <div class="u-fw-700 text-surface-700 mb-2" id="popupAnnouncementTitle" style="font-size: 18px"></div>
            <div id="popupAnnouncementMessage" class="text-break text-surface-600" style="line-height: 1.6; white-space: pre-wrap"></div>
 <div class="u-fs-12 text-surface-muted mt-3" id="popupAnnouncementTime"></div>
        </div>
        <div class="announcement-popup-progress"></div>
    </div>

    <!-- Floating Re-open Button -->
    <button id="announcementFloatBtn" class="announcement-float-btn" onclick="showRecentAnnouncements()" title="View Recent Announcements">
        <i class="bx bx-bell"></i>
 <span id="announcementFloatBadge" class="notification-badge d-none" >1</span>
    </button>

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
 <h5 class="modal-title u-fw-700 text-surface-700" >Edit Announcement</h5>
 <p class="u-fs-13 text-surface-500" style="margin: 4px 0 0 0">Update your announcement details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editAnnouncementForm">
                        @csrf
 <div class="mb-4" >
 <label class="form-label label-value" >Title (Optional)</label>
 <input type="text" class="form-control u-rounded-8" id="editAnnouncementTitle" placeholder="Announcement title" >
                        </div>

 <div class="mb-4" >
 <label class="form-label label-value" >Message</label>
 <textarea class="form-control u-rounded-8" id="editAnnouncementMessage" placeholder="Type your announcement message..." rows="4" required style="resize: vertical"></textarea>
                        </div>

 <div class="mb-4" >
 <label class="form-label label-value" >Priority</label>
 <select class="form-select u-rounded-8" id="editAnnouncementPriority" >
                                <option value="info">Info</option>
                                <option value="normal" selected>Normal</option>
                                <option value="warning">Warning</option>
                                <option value="urgent">Urgent</option>
                            </select>
 <small class="d-block text-surface-500 u-fs-12" style="margin-top: 6px">Set the priority level for this announcement</small>
                        </div>
                    </form>
                </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
 <button type="button" class="btn btn-primary" id="updateAnnouncementBtn" onclick="updateAnnouncement()">
                        <i class="bx bx-save"></i> Update Announcement
                    </button>
                </div>
            </div>
        </div>
    </div>

@section('script')
<script>
// Chat Application v3.0 - Taurus CRM

// ===== GLOBAL VARIABLES (MUST BE DECLARED FIRST) =====
window.currentUserId = {{ auth()->id() }};
if (typeof window.currentUserName === 'undefined') {
    window.currentUserName = '{{ auth()->user()->name }}';
}
if (typeof window.userRoles === 'undefined') {
    window.userRoles = {!! json_encode(auth()->user()->roles->pluck('name')->toArray()) !!};
}
if (typeof window.isSuperAdmin === 'undefined') {
    window.isSuperAdmin = window.userRoles.includes('{{ Roles::SUPER_ADMIN }}') || window.userRoles.includes('{{ Roles::CEO }}');
}
if (typeof window.currentConversationId === 'undefined') {
    window.currentConversationId = null;
}
if (typeof window.currentConversationName === 'undefined') {
    window.currentConversationName = '';
}

// Translation variables
const translations = {
    noAnnouncementsYet: '{{ __('chat.no_announcements_yet') }}',
    beTheFirstToPost: '{{ __('chat.be_the_first_to_post') }}',
    onlyAuthorizedUsers: '{{ __('chat.only_authorized_users') }}'
};
if (typeof window.messagesRefreshInterval === 'undefined') {
    window.messagesRefreshInterval = null;
}
if (typeof window._typingPollInterval === 'undefined') {
    window._typingPollInterval = null;
}
if (typeof window.conversationsRefreshInterval === 'undefined') {
    window.conversationsRefreshInterval = null;
}
if (typeof window.conversationUsers === 'undefined') {
    window.conversationUsers = [];
}
if (typeof window.currentMentionMatch === 'undefined') {
    window.currentMentionMatch = null;
}
if (typeof window.selectedSuggestionIndex === 'undefined') {
    window.selectedSuggestionIndex = 0;
}
if (typeof window.communityMembersToAdd === 'undefined') {
    window.communityMembersToAdd = [];
}
if (typeof window.contextMenuCommunityId === 'undefined') {
    window.contextMenuCommunityId = null;
}
if (typeof window.contextMenuCommunityName === 'undefined') {
    window.contextMenuCommunityName = null;
}

// ===== MENTION SYSTEM - EVENT DELEGATION =====
// Uses document-level event delegation so mention autocomplete works
// regardless of when textarea elements are created/recreated by innerHTML
(function initMentionDelegation() {
    // Map of mention-enabled input IDs to their suggestion container IDs
    const MENTION_INPUTS = {
        'messageInput': 'mentionSuggestions',
        'announcementInput': 'announcementMentionSuggestions'
    };

    // Handle input events for @ mention detection
    document.addEventListener('input', function(e) {
        const containerId = MENTION_INPUTS[e.target.id];
        if (!containerId) return;

        const text = e.target.value;
        const cursorPos = e.target.selectionStart;
        const beforeCursor = text.substring(0, cursorPos);
        const lastAtPos = beforeCursor.lastIndexOf('@');

        if (lastAtPos !== -1) {
            const afterAt = beforeCursor.substring(lastAtPos + 1);
            // Allow typing after @ (no space unless bracket format)
            if (!afterAt.includes(' ') || afterAt.startsWith('[')) {
                const query = afterAt.replace(/^\[/, '');
                const suggestions = getMentionSuggestions(query);
                showMentionSuggestions(suggestions, containerId, e.target.id);
                window.currentMentionMatch = { start: lastAtPos, end: cursorPos };
            } else {
                const el = document.getElementById(containerId);
                if (el) el.style.display = 'none';
            }
        } else {
            const el = document.getElementById(containerId);
            if (el) el.style.display = 'none';
        }
    });

    // Handle keyboard navigation in mention suggestions
    document.addEventListener('keydown', function(e) {
        const containerId = MENTION_INPUTS[e.target.id];
        if (!containerId) return;

        const container = document.getElementById(containerId);
        if (!container || container.style.display === 'none') return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const items = container.querySelectorAll('.suggestion-item');
            if (items.length === 0) return;
            window.selectedSuggestionIndex = (window.selectedSuggestionIndex + 1) % items.length;
            updateActiveSuggestion(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const items = container.querySelectorAll('.suggestion-item');
            if (items.length === 0) return;
            window.selectedSuggestionIndex = (window.selectedSuggestionIndex - 1 + items.length) % items.length;
            updateActiveSuggestion(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const activeItem = container.querySelector('.suggestion-item.active');
            if (activeItem) {
                selectMention(activeItem.dataset.mention, e.target.id);
            }
            container.style.display = 'none';
        } else if (e.key === 'Escape') {
            container.style.display = 'none';
        }
    });

    // Close mention suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mention-suggestions') && !e.target.closest('textarea')) {
            document.querySelectorAll('.mention-suggestions').forEach(el => {
                el.style.display = 'none';
            });
        }
    });

    console.log('Mention delegation initialized');
})();

// ===== SIDEBAR TAB SWITCHING =====
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.chat-tab-pill');
    const tabContents = document.querySelectorAll('.chat-sidebar-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Hide all tab contents
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show selected tab
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
            
            // Handle sidebar and main area visibility
            const chatSidebar = document.querySelector('.chat-sidebar');
            const chatMain = document.getElementById('chatMain');
            const peopleMain = document.getElementById('peopleMain');
            
            if (tabName === 'people') {
                // Hide sidebar and chat main, show people page full-width
                chatSidebar.style.display = 'none';
                chatMain.style.display = 'none';
                peopleMain.style.display = 'flex';
                loadPeopleCardsForDisplay();
            } else {
                // Show sidebar and chat main, hide people page
                chatSidebar.style.display = 'flex';
                chatMain.style.display = 'flex';
                peopleMain.style.display = 'none';
            }
            
            // Load data based on tab
            if (tabName === 'communities') {
                loadCommunitiesForDisplay();
            } else if (tabName === 'groups') {
                loadGroupsForDisplay();
            }
        });
    });

    // Load initial data
    setTimeout(() => {
        loadConversations();
    }, 500);
});

// Load communities for display
let globalCommunities = [];
async function loadCommunitiesForDisplay() {
    try {
        const data = await apiCall('/api/chat/communities');
        globalCommunities = data.communities || [];
        renderCommunitiesList(globalCommunities);
        
        // Subscribe to all community announcement channels
        subscribeToCommunityAnnouncements(globalCommunities);
    } catch (error) {
        console.error('Error loading communities:', error);
    }
}

// Load groups for display
async function loadGroupsForDisplay() {
    try {
        const groupData = await apiCall('/api/chat/group-conversations');
        renderGroupConversationsList(groupData.conversations || []);
    } catch (error) {
        console.error('Error loading groups:', error);
    }
}

// Load people for card display
async function loadPeopleCardsForDisplay() {
    try {
        const data = await apiCall('/api/chat/users');
        renderPeopleCards(data.users || []);
    } catch (error) {
        console.error('Error loading people:', error);
        const container = document.getElementById('peopleCards');
        if (container) {
            container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--bs-ui-danger); padding: 40px;"><i class="bx bx-error" style="font-size: 48px; margin-bottom: 16px;"></i><br>Error loading people</div>';
        }
    }
}

// Render people as cards
function renderPeopleCards(users) {
    const container = document.getElementById('peopleCards');
    if (!container) return;

    if (users.length === 0) {
        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--bs-secondary-color, #6b7280); padding: 40px; font-size: .82rem;"><i class="bx bx-user" style="font-size: 48px; margin-bottom: 12px; display: block; color: var(--bs-border-color, #e5e7eb);"></i>No people available</div>';
        return;
    }

    container.innerHTML = users.map(user => `
        <div class="person-card" onclick="startPersonChat(${user.id}, '${user.name.replace(/'/g, "\\'")}')" 
             data-user-name="${user.name}" data-user-email="${user.email}">
            <div class="conversation-avatar" style="width: 52px; height: 52px; font-size: 1.1rem;">
                ${user.avatar ? 
                    `<img src="${user.avatar}" alt="${user.name}">` : 
                    user.name.charAt(0).toUpperCase()
                }
            </div>
            <div style="flex:1; min-width:0;">
                <div class="people-card-name">${user.name}</div>
                <p class="people-card-email">${user.email}</p>
            </div>
            <button class="person-chat-btn" onclick="event.stopPropagation(); startPersonChat(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                <i class="bx bx-message"></i> Chat
            </button>
        </div>
    `).join('');
    
    // Initialize search for people cards
    initializePeopleCardsSearch();
}

// Initialize people cards search functionality
function initializePeopleCardsSearch() {
    const searchInput = document.getElementById('searchPeopleCards');
    if (!searchInput) return;
    
    // Remove previous listeners
    searchInput.removeEventListener('input', handlePeopleCardsSearch);
    searchInput.addEventListener('input', handlePeopleCardsSearch);
}

// Handler for people cards search
function handlePeopleCardsSearch(e) {
    filterPeopleCards(e.target.value);
}

// Filter people cards by search term
function filterPeopleCards(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    const cards = document.querySelectorAll('.person-card');
    
    if (cards.length === 0) return;
    
    let visibleCount = 0;
    cards.forEach(card => {
        const userName = card.getAttribute('data-user-name')?.toLowerCase() || '';
        const userEmail = card.getAttribute('data-user-email')?.toLowerCase() || '';
        
        if (term === '' || userName.includes(term) || userEmail.includes(term)) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show "no results" message if needed
    const container = document.getElementById('peopleCards');
    let noResultsMsg = container.querySelector('.no-results-message');
    
    if (visibleCount === 0 && term !== '') {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.style.cssText = 'grid-column: 1/-1; text-align: center; color: var(--bs-secondary-color, #6b7280); padding: 30px; font-size: .78rem;';
            noResultsMsg.innerHTML = '<i class="bx bx-search" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--bs-border-color, #e5e7eb);"></i>No people found matching your search';
            container.appendChild(noResultsMsg);
        }
        noResultsMsg.style.display = 'block';
    } else {
        if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }
}

// Start person chat from card
function startPersonChat(userId, userName) {
    // Switch to Messages tab
    const messagesTab = document.querySelector('[data-tab="chats"]');
    if (messagesTab) {
        messagesTab.click();
    }
    
    // Start direct chat
    startDirectChat(userId, userName);
}

// Render people in sidebar
function renderPeopleList(users) {
    const listEl = document.getElementById('peopleList');
    if (users.length === 0) {
        listEl.innerHTML = '<div class="no-conversations"><p>No people available</p></div>';
        return;
    }

    listEl.innerHTML = users.map(user => `
        <button class="user-item" onclick="openPersonChat(${user.id}, '${user.name.replace(/'/g, "\\'")}', event)" data-user-id="${user.id}" data-user-name="${user.name}" data-user-email="${user.email}">
            <div class="user-avatar">
                ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}">` : `<span>${user.name.charAt(0).toUpperCase()}</span>`}
            </div>
            <div class="user-info">
                <div class="user-name">${user.name}</div>
                <div class="user-email">${user.email}</div>
            </div>
        </button>
    `).join('');
    
    // Initialize search for people
    initializePeopleSearch();
}

// Initialize people search functionality
function initializePeopleSearch() {
    const searchInput = document.getElementById('searchPeople');
    if (!searchInput) return;
    
    // Simply attach listener without cloning - clear previous listeners first
    searchInput.removeEventListener('input', handlePeopleSearch);
    searchInput.addEventListener('input', handlePeopleSearch);
}

// Handler for people search
function handlePeopleSearch(e) {
    filterPeopleList(e.target.value);
}

// Filter people by search term
function filterPeopleList(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    const userItems = document.querySelectorAll('#peopleList .user-item');
    
    if (userItems.length === 0) return;
    
    userItems.forEach(item => {
        const userName = item.getAttribute('data-user-name')?.toLowerCase() || '';
        const userEmail = item.getAttribute('data-user-email')?.toLowerCase() || '';
        
        if (term === '' || userName.includes(term) || userEmail.includes(term)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// Open person chat - navigate to Messages tab
function openPersonChat(userId, userName, event) {
    event.preventDefault();
    
    // Switch to Messages tab
    const messagesTab = document.querySelector('[data-tab="chats"]');
    if (messagesTab) {
        messagesTab.click();
    }
    
    // Start direct chat
    startDirectChat(userId, userName);
}

// Render communities in sidebar
function renderCommunitiesList(communities) {
    const listEl = document.getElementById('communitiesList');
    if (communities.length === 0) {
        listEl.innerHTML = `<div class="no-conversations"><p>No communities yet</p></div>`;
        return;
    }

    listEl.innerHTML = communities.map(community => {
        // Fix icon class - ensure we don't double-prefix with 'bx'
        let iconClass = community.icon || 'bx-group';
        if (!iconClass.startsWith('bx ')) {
            iconClass = 'bx ' + iconClass;
        }
        
        // Allow context menu for creator OR Super Admin
        const canManage = (community.created_by == window.currentUserId) || window.isSuperAdmin;
        
        const isCreator = community.created_by == window.currentUserId;
        const communityData = JSON.stringify(community);
        
        return `
        <div class="community-item community-item-${community.id}" 
             data-community='${communityData.replace(/'/g, "&apos;")}'
             data-is-creator="${isCreator}"
             onclick="selectCommunityFromData(this)"
             oncontextmenu="showCommunityContextMenu(event, ${community.id}, '${community.name.replace(/'/g, "\\'")}', ${canManage}); return false;"
             style="border-left: 3px solid ${community.color || '#d4af37'};">
            <div class="community-avatar" style="${community.avatar ? '' : 'background: ' + (community.color || '#d4af37') + ';'}">
                ${community.avatar ? '<img src="/storage/' + community.avatar + '" alt="' + community.name + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">' : '<i class="' + iconClass + '"></i>'}
            </div>
            <div class="community-info">
                <div class="community-name">${community.name}</div>
                <div class="community-description">
                    <i class="bx bx-bullhorn" style="font-size: 11px;"></i>
                    <span>Announcement Board</span>
                </div>
            </div>
            ${canManage ? `
                <button class="btn-delete-community btn-delete-${community.id}" 
                        data-community-id="${community.id}" 
                        data-community-name="${community.name}" 
                        title="Delete Community" 
                        style="flex-shrink: 0; background: #f46a6a; color: #fff; border: none; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 13px; opacity: 0; transition: opacity .15s;">
                    <i class="bx bx-trash"></i>
                </button>
            ` : ''}
        </div>
        `;
    }).join('');
    
    // Add event listeners for delete buttons and hover effects
    setTimeout(() => {
        const communitiesListEl = document.getElementById('communitiesList');
        if (communitiesListEl) {
            // Add hover effects to show edit and delete buttons
            communitiesListEl.querySelectorAll('.community-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const deleteBtn = this.querySelector('.btn-delete-community');
                    if (deleteBtn) deleteBtn.style.opacity = '1';
                });
                item.addEventListener('mouseleave', function() {
                    const deleteBtn = this.querySelector('.btn-delete-community');
                    if (deleteBtn) deleteBtn.style.opacity = '0';
                });
            });
            
            // Add click handlers for delete buttons
            communitiesListEl.querySelectorAll('.btn-delete-community').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const communityId = this.getAttribute('data-community-id');
                    const communityName = this.getAttribute('data-community-name');
                    deleteCommunityHandler(e, communityId, communityName);
                });
            });
        }
    }, 100);
}

// Render group conversations list
function renderGroupConversationsList(conversations) {
    const listEl = document.getElementById('groupConversationsList');
    if (conversations.length === 0) {
        listEl.innerHTML = '<div class="no-conversations"><p>No group chats yet</p></div>';
        return;
    }

    listEl.innerHTML = conversations.map(conv => {
        const safeName = (conv.name || 'Group Chat').replace(/'/g, "\\'");
        const avatarUrl = conv.avatar;
        const displayName = conv.name || 'Group Chat';
        
        // Create avatar HTML with proper image rendering
        let avatarHtml = '<i class="bx bx-group"></i>';
        if (avatarUrl) {
            avatarHtml = `<img src="${avatarUrl}" alt="${displayName}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\\'bx bx-group\\'></i>';">`;
        }
        
        return `
        <div class="conversation-item group-conversation-item ${conv.id === window.currentConversationId ? 'active' : ''}"
             onclick="selectConversation(${conv.id}, '${safeName}', this)">
            <div class="conversation-avatar">
                ${avatarHtml}
            </div>
            <div class="conversation-info">
                <div class="conversation-name">${displayName}</div>
                ${conv.latest_message ? `<div class="conversation-preview">${(conv.latest_message.message || '').substring(0, 40)}...</div>` : ''}
            </div>
            ${conv.updated_at ? `<div class="conversation-time">${conv.updated_at}</div>` : ''}
            ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
        </div>
        `;
    }).join('');
    
    // No need for JS hover effects - CSS handles it
}

// Helper function to select community from data attribute
function selectCommunityFromData(element) {
    try {
        const communityData = element.getAttribute('data-community');
        const isCreator = element.getAttribute('data-is-creator') === 'true';
        const community = JSON.parse(communityData.replace(/&apos;/g, "'"));
        selectCommunity(community, isCreator);
    } catch (error) {
        console.error('Error parsing community data:', error);
    }
}

// Select community
async function selectCommunity(community, isCreator = false) {
    try {
        const communityId = community.id;
        const communityName = community.name;
        const communityColor = community.color || '#d4af37';
        
        // Set current community context
        window.currentCommunityId = communityId;
        window.currentConversationId = null; // Communities don't use chat conversations
        window.currentConversationName = communityName;
        
        // Fetch announcements and check permissions
        let announcementsData = { announcements: [], can_post: false };
        try {
            announcementsData = await apiCall(`/api/chat/communities/${communityId}/announcements`);
            console.log('Announcements API Response:', announcementsData);
        } catch (e) {
            console.error('Could not load announcements:', e);
        }
        
        // Use the can_post value from API response
        const canPostAnnouncement = !!announcementsData.can_post;
        
        const chatMain = document.getElementById('chatMain');
        const avatarHtml = `<div class="chat-header-avatar" style="background: ${communityColor};"><i class="bx bx-bullhorn"></i></div>`;
        
        // Build the community interface
        chatMain.innerHTML = `
            <div class="chat-header">
                <div class="chat-header-info">
                    ${avatarHtml}
                    <div class="chat-header-title">
                        <h5>${communityName}</h5>
                        <p>
                            <i class="bx bx-bullhorn" style="font-size: 11px;"></i>
                            Announcement Board
                        </p>
                    </div>
                </div>
                ${isCreator || window.isSuperAdmin ? `
                    <div class="chat-header-actions">
                        <button onclick="openEditCommunityModal(${communityId})" class="btn" title="Edit Community Settings">
                            <i class="bx bx-cog"></i> Settings
                        </button>
                        <button onclick="openCommunityMemberManagement(${communityId}, '${communityName.replace(/'/g, "\\'")}')" class="btn" title="Manage Members">
                            <i class="bx bx-user-plus"></i> Members
                        </button>
                    </div>
                ` : ''}
            </div>
            <div class="announcement-messages" id="announcementMessages" style="flex: 1; overflow-y: auto; padding: 20px; background: var(--bs-surface-50, #f8f9fa);">
                <div id="announcementsContainer">
                    ${announcementsData.announcements.length > 0 ? 
                        renderAnnouncements(announcementsData.announcements, communityColor) : 
                        `<div class="no-messages" style="max-width: 500px; margin: 60px auto;">
                            <i class="bx bx-bullhorn" style="font-size: 64px; color: ${communityColor}; opacity: 0.3;"></i>
                            <h5 class="mt-3" style="font-weight: 600;">${translations.noAnnouncementsYet}</h5>
                            <p style="margin-top: 12px; line-height: 1.6;">
                                ${canPostAnnouncement ? translations.beTheFirstToPost : translations.onlyAuthorizedUsers}
                            </p>
                        </div>`
                    }
                </div>
            </div>
            ${canPostAnnouncement ? `
                <div class="chat-input-area" style="border-top: 1px solid var(--bs-surface-200, rgba(0,0,0,.08));">
                    <div style="display: flex; flex-direction: column; gap: 10px; position: relative;">
                        <textarea id="announcementInput" class="form-control" placeholder="Type @ to mention someone, @everyone to mention all..." rows="2" style="resize: vertical; min-height: 60px;"></textarea>
                        <div id="announcementMentionSuggestions" class="mention-suggestions" style="display: none;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="message-input-actions">
                                <button type="button" id="attachAnnouncementBtn" title="Attach file">
                                    <i class="bx bx-paperclip"></i>
                                </button>
                                <button type="button" id="emojiAnnouncementBtn" title="Add emoji">
                                    <i class="bx bx-smile"></i>
                                </button>
                            </div>
                            <button type="button" id="sendAnnouncementBtn" onclick="sendAnnouncement()" style="padding: 8px 18px; background: linear-gradient(135deg, ${communityColor}, ${adjustColor(communityColor, -20)}); color: #fff; border: none; border-radius: 20px; font-weight: 600; font-size: .75rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <i class="bx bx-paper-plane"></i>
                                Post Announcement
                            </button>
                        </div>
                    </div>
                    <input type="file" id="announcementFileInput" multiple style="display: none" accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                </div>
            ` : `
                <div style="border-top: 1px solid var(--bs-surface-200, rgba(0,0,0,.08)); background: var(--bs-surface-50, #f8f9fa); padding: 14px; text-align: center; color: var(--bs-surface-400, #adb5bd); font-size: .75rem;">
                    <i class="bx bx-lock-alt" style="font-size: 14px; vertical-align: middle; margin-right: 3px;"></i>
                    You do not have permission to post announcements. Contact an admin for access.
                </div>
            `}
        `;
        
        // Setup file attachment handler and Enter key if user can post
        if (canPostAnnouncement) {
            setTimeout(async () => {
                const attachBtn = document.getElementById('attachAnnouncementBtn');
                const fileInput = document.getElementById('announcementFileInput');
                const textarea = document.getElementById('announcementInput');
                
                if (attachBtn && fileInput) {
                    attachBtn.addEventListener('click', () => fileInput.click());
                }
                
                if (textarea) {
                    textarea.addEventListener('keydown', (e) => {
                        // Only prevent Enter if mention suggestions are not showing
                        const mentionContainer = document.getElementById('announcementMentionSuggestions');
                        if (e.key === 'Enter' && !e.shiftKey && (!mentionContainer || mentionContainer.style.display === 'none')) {
                            e.preventDefault();
                            sendAnnouncement();
                        }
                    });
                }
                
                // Load community members for mention suggestions
                try {
                    const response = await apiCall(`/api/communities/${communityId}/members`);
                    window.conversationUsers = (response.members || []).map(m => ({
                        id: m.id,
                        name: m.name,
                        avatar: m.avatar,
                        role: m.role || 'Member'
                    }));
                } catch (e) {
                    console.warn('Failed to load community members for mentions:', e);
                }
                
                // Mention autocomplete is handled by document-level event delegation
                console.log('Announcement mention ready, users loaded:', (window.conversationUsers || []).length);
            }, 100);
        }

        // Subscribe to real-time announcement updates
        subscribeToCommunityAnnouncements(communityId);
    } catch (error) {
        console.error('Error loading community:', error);
        alert('Failed to load community. Please try again.');
    }
}

// Render announcements
function renderAnnouncements(announcements, communityColor) {
    if (!announcements || announcements.length === 0) {
        return `<div class="no-messages" style="max-width: 500px; margin: 60px auto;">
            <i class="bx bx-bullhorn" style="font-size: 64px; color: ${communityColor}; opacity: 0.3;"></i>
            <h5 class="mt-3" style="font-weight: 600;">${translations.noAnnouncementsYet}</h5>
        </div>`;
    }
    
    return announcements.map(announcement => {
        const creatorName = announcement.created_by?.name || 'Unknown';
        const creatorId = announcement.created_by?.id;
        const avatar = announcement.created_by?.avatar 
            ? `${announcement.created_by.avatar}` 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(creatorName)}&background=${communityColor.replace('#', '')}&color=fff`;
        
        // Priority color mapping
        const priorityColors = {
            'urgent': '#f46a6a',
            'warning': '#f1b44c',
            'info': '#50a5f1',
            'normal': 'var(--bs-surface-500, #6c757d)'
        };
        const priorityIcons = {
            'urgent': 'bx-error-circle',
            'warning': 'bx-error',
            'info': 'bx-info-circle',
            'normal': 'bx-info-circle'
        };
        const priorityLabels = {
            'urgent': 'URGENT',
            'warning': 'Warning',
            'info': 'Info',
            'normal': 'Normal'
        };
        
        const priority = announcement.priority || 'normal';
        const priorityColor = priorityColors[priority];
        const priorityIcon = priorityIcons[priority];
        const priorityLabel = priorityLabels[priority];
        
        return `
            <div class="announcement-item" style="margin-bottom: 16px; padding: 16px; background: var(--bs-card-bg); border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.04); border-left: 4px solid ${priorityColor};">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <img src="${avatar}" alt="${creatorName}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(creatorName)}&background=${communityColor.replace('#', '')}&color=fff'" class="announcement-avatar" style="width: 38px; height: 38px; border: 2px solid ${communityColor};">
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                    <strong class="announcement-author">${creatorName}</strong>
                                    <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 7px; background: ${priorityColor}; color: #fff; border-radius: 10px; font-size: .6rem; font-weight: 700;">
                                        <i class="bx ${priorityIcon}" style="font-size: 11px;"></i>
                                        ${priorityLabel}
                                    </span>
                                </div>
                                <div class="announcement-time">${announcement.created_at_human}</div>
                            </div>
                            ${creatorId === window.currentUserId ? `
                                <div style="display: flex; gap: 4px;">
                                    <button onclick='editAnnouncement(${announcement.id}, ${JSON.stringify(announcement.title || "")}, ${JSON.stringify(announcement.message)}, "${announcement.priority || "normal"}")' style="background: none; border: none; color: #50a5f1; cursor: pointer; padding: 3px 6px;" title="Edit">
                                        <i class="bx bx-edit" style="font-size: 16px;"></i>
                                    </button>
                                    <button onclick="deleteAnnouncement(${announcement.id})" style="background: none; border: none; color: #f46a6a; cursor: pointer; padding: 3px 6px;" title="Delete">
                                        <i class="bx bx-trash" style="font-size: 16px;"></i>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                        ${announcement.title ? `<div style="font-weight: 600; color: ${communityColor}; margin-bottom: 6px; font-size: .85rem;">${escapeHtml(announcement.title)}</div>` : ''}
                        <div class="announcement-text">${formatMessageText(announcement.message)}</div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Send announcement
async function sendAnnouncement() {
    const textarea = document.getElementById('announcementInput');
    const message = textarea?.value.trim();
    
    if (!message) {
        alert('Please enter an announcement message');
        return;
    }
    
    if (!window.currentCommunityId) {
        alert('No community selected');
        return;
    }
    
    try {
        const sendBtn = document.getElementById('sendAnnouncementBtn');
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Posting...';
        }
        
        const result = await apiCall(`/api/chat/communities/${window.currentCommunityId}/announcements`, 'POST', {
            message: message,
            title: null,
            priority: 'normal'
        });
        
        if (result.success) {
            textarea.value = '';
            
            // Reload announcements using globalCommunities
            const announcementsData = await apiCall(`/api/chat/communities/${window.currentCommunityId}/announcements`);
            const container = document.getElementById('announcementsContainer');
            const community = globalCommunities.find(c => c.id === window.currentCommunityId);
            if (container && community) {
                container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || '#d4af37');
            }
            
            // Scroll to bottom
            const messagesDiv = document.getElementById('announcementMessages');
            if (messagesDiv) {
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        } else {
            throw new Error(result.message || 'Failed to post announcement');
        }
    } catch (error) {
        console.error('Error sending announcement:', error);
        alert('Failed to post announcement: ' + error.message);
    } finally {
        const sendBtn = document.getElementById('sendAnnouncementBtn');
        if (sendBtn) {
            sendBtn.disabled = false;
            const community = globalCommunities.find(c => c.id === window.currentCommunityId);
            const color = community?.color || '#d4af37';
            sendBtn.innerHTML = `<i class="bx bx-paper-plane"></i> Post Announcement`;
        }
    }
}

// Helper function to adjust color brightness
function adjustColor(color, amount) {
    const num = parseInt(color.replace('#', ''), 16);
    const r = Math.max(0, Math.min(255, (num >> 16) + amount));
    const g = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amount));
    const b = Math.max(0, Math.min(255, (num & 0x0000FF) + amount));
    return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
}

// Load community announcements
async function loadCommunityAnnouncements(conversationId, communityColor) {
    try {
        const messages = await apiCall(`/api/chat/conversations/${conversationId}/messages`);
        const container = document.getElementById('announcementsContainer');
        
        if (!messages.messages || messages.messages.length === 0) {
            container.innerHTML = `
                <div class="no-messages" style="max-width: 500px; margin: 60px auto;">
                    <i class="bx bx-bullhorn" style="font-size: 64px; color: ${communityColor}; opacity: 0.3;"></i>
                    <h5 class="mt-3" style="font-weight: 600;">${translations.noAnnouncementsYet}</h5>
                    <p style="margin-top: 12px;">${translations.beTheFirstToPost}</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = messages.messages.map(msg => `
            <div class="announcement-item" style="background: var(--bs-card-bg); border-left: 4px solid ${communityColor}; border-radius: 10px; padding: 14px; margin-bottom: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.04);">
                <div style="display: flex; align-items: start; gap: 10px;">
                    <div class="announcement-avatar" style="background: ${communityColor};">
                        ${(msg.user?.name || 'U').charAt(0).toUpperCase()}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                            <div>
                                <div class="announcement-author">${msg.user?.name || 'Unknown'}</div>
                                <div class="announcement-time">${msg.created_at}</div>
                            </div>
                            ${msg.user_id === window.currentUserId ? `
                                <button onclick="deleteAnnouncement(${msg.id})" style="background: none; border: none; color: #f46a6a; cursor: pointer; padding: 3px 6px;" title="Delete">
                                    <i class="bx bx-trash" style="font-size: 15px;"></i>
                                </button>
                            ` : ''}
                        </div>
                        <div class="announcement-text">${msg.message}</div>
                        ${msg.attachments && msg.attachments.length > 0 ? `
                            <div style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;">
                                ${msg.attachments.map(att => `
                                    <a href="${att.url}" target="_blank" class="file-attachment" style="font-size: .72rem;">
                                        <i class="bx bx-file"></i>
                                        ${att.file_name}
                                    </a>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
        
        // Scroll to bottom
        setTimeout(() => {
            const messagesEl = document.getElementById('announcementMessages');
            if (messagesEl) messagesEl.scrollTop = messagesEl.scrollHeight;
        }, 100);
    } catch (error) {
        console.error('Error loading announcements:', error);
    }
}

// Edit announcement
function editAnnouncement(announcementId, title, message, priority) {
    // Store announcement ID for update
    window.editingAnnouncementId = announcementId;
    
    // Populate modal fields
    document.getElementById('editAnnouncementTitle').value = title || '';
    document.getElementById('editAnnouncementMessage').value = message;
    document.getElementById('editAnnouncementPriority').value = priority || 'normal';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editAnnouncementModal'));
    modal.show();
}

// Update announcement
async function updateAnnouncement() {
    const announcementId = window.editingAnnouncementId;
    
    if (!announcementId || !window.currentCommunityId) {
        alert('Invalid announcement or community');
        return;
    }
    
    const title = document.getElementById('editAnnouncementTitle').value.trim();
    const message = document.getElementById('editAnnouncementMessage').value.trim();
    const priority = document.getElementById('editAnnouncementPriority').value;
    
    if (!message) {
        alert('Please enter an announcement message');
        return;
    }
    
    try {
        const updateBtn = document.getElementById('updateAnnouncementBtn');
        if (updateBtn) {
            updateBtn.disabled = true;
            updateBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Updating...';
        }
        
        const response = await fetch(`/api/chat/communities/${window.currentCommunityId}/announcements/${announcementId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                title: title || null,
                message: message,
                priority: priority,
                show_in_banner: true,
                is_active: true
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to update announcement');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editAnnouncementModal'));
            modal.hide();
            
            // Reload announcements
            try {
                const announcementsData = await apiCall(`/api/chat/communities/${window.currentCommunityId}/announcements`);
                const community = globalCommunities.find(c => c.id === window.currentCommunityId);
                const container = document.getElementById('announcementsContainer');
                
                if (container && community && announcementsData.announcements) {
                    container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || '#d4af37');
                }
            } catch (e) {
                console.error('Failed to reload announcements:', e);
            }
        }
    } catch (error) {
        console.error('Error updating announcement:', error);
        alert('Failed to update announcement: ' + error.message);
    } finally {
        const updateBtn = document.getElementById('updateAnnouncementBtn');
        if (updateBtn) {
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="bx bx-save"></i> Update Announcement';
        }
    }
}

// Delete announcement
async function deleteAnnouncement(announcementId) {
    if (!confirm('Are you sure you want to delete this announcement?')) return;
    
    if (!window.currentCommunityId) {
        alert('No community selected');
        return;
    }
    
    try {
        const response = await fetch(`/api/chat/communities/${window.currentCommunityId}/announcements/${announcementId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content,
            }
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to delete announcement');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Reload announcements
            try {
                const announcementsData = await apiCall(`/api/chat/communities/${window.currentCommunityId}/announcements`);
                const community = globalCommunities.find(c => c.id === window.currentCommunityId);
                const container = document.getElementById('announcementsContainer');
                
                if (container && community) {
                    if (announcementsData.announcements && announcementsData.announcements.length > 0) {
                        container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || '#d4af37');
                    } else {
                        container.innerHTML = `
                            <div class="no-messages" style="max-width: 500px; margin: 60px auto;">
                                <i class="bx bx-bullhorn" style="font-size: 64px; color: ${community.color || '#d4af37'}; opacity: 0.3;"></i>
                                <h5 class="mt-3" style="font-weight: 600;">${translations.noAnnouncementsYet}</h5>
                                <p style="margin-top: 12px;">${translations.beTheFirstToPost}</p>
                            </div>
                        `;
                    }
                }
            } catch (e) {
                console.error('Failed to reload announcements:', e);
            }
        }
    } catch (error) {
        console.error('Error deleting announcement:', error);
        alert('Failed to delete announcement: ' + error.message);
    }
}

// Retry loading community with ID
function retryLoadCommunity(communityId, isCreator) {
    // Find the community in globalCommunities array
    const community = globalCommunities.find(c => c.id === communityId);
    if (community) {
        selectCommunity(community, isCreator);
    } else {
        console.error('Community not found:', communityId);
        alert('Failed to retry. Please refresh the page.');
    }
}

// Delete community handler
function deleteCommunityHandler(event, communityId, communityName) {
    event.stopPropagation();
    if (confirm(`Are you sure you want to delete the community "${communityName}"? This action cannot be undone.`)) {
        apiCall(`/api/communities/${communityId}`, 'DELETE')
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Community deleted successfully');
                    loadCommunitiesForDisplay();
                    // Clear the chat main area
                    document.getElementById('chatMain').innerHTML = `
                        <div class="chat-welcome">
                            <i class="bx bx-message-square-dots"></i>
                            <h4>Welcome to Taurus Chat</h4>
                            <p>Select a conversation to start messaging or create a new chat</p>
                        </div>
                    `;
                } else {
                    alert('Error deleting community: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error deleting community:', error);
                alert('Error deleting community. Please try again.');
            });
    }
}

// API helper function
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

    const response = await fetch(url, options);

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

// Chat Application Variables - Already declared at top of script

// Load conversations
async function loadConversations() {
    try {
        const conversationsData = await apiCall('/api/chat/conversations');
        renderConversationsAndUsers(conversationsData.conversations, []);
        // Auto-open conversation specified in URL (?open=id)
        const openId = new URLSearchParams(window.location.search).get('open');
        if (openId && !window.currentConversationId) {
            const el = document.querySelector(`.conversation-item[data-conv-id="${openId}"]`);
            if (el) el.click();
        }
    } catch (error) {
        console.error('Error loading conversations:', error);
        const listEl = document.getElementById('conversationsList');
        listEl.innerHTML = '<div class="no-conversations"><p style="color: #f46a6a;">Error loading chats. Please refresh the page.</p><p>Check browser console for details</p></div>';
    }
}

// Render conversations and users together
function renderConversationsAndUsers(conversations, users) {
    const listEl = document.getElementById('conversationsList');
    
    if (!listEl) {
        console.error('conversationsList element not found!');
        return;
    }

    console.log('Rendering conversations. Total received:', conversations.length);
    
    // Filter out community conversations (they have community_id or are group chats with same name as a community)
    const nonCommunityConversations = conversations.filter(conv => {
        const hasCommId = !!conv.community_id;
        if (hasCommId) {
            console.log('Filtering out community conversation:', conv.name, 'community_id:', conv.community_id);
        }
        return !hasCommId;
    });
    
    console.log('After filtering, showing', nonCommunityConversations.length, 'non-community conversations');

    if (nonCommunityConversations.length === 0) {
        listEl.innerHTML = '<div class="no-conversations"><p>No chats available</p></div>';
        return;
    }

    let html = '';

    // Add existing conversations only (removed user list from sidebar)
    if (nonCommunityConversations.length > 0) {
        html += '<div class="sidebar-section-label">Recent</div>';
        
        try {
            html += nonCommunityConversations.map(conv => {
                const safeName = (conv.name || 'Unknown').replace(/'/g, "\\'");
                const avatarUrl = conv.avatar;
                const displayName = conv.name || 'Unknown';
                
                return `
            <div class="conversation-item ${conv.id === window.currentConversationId ? 'active' : ''}"
                 data-conv-id="${conv.id}"
                 onclick="selectConversation(${conv.id}, '${safeName}', this)">
                <div class="conversation-avatar">
                    ${avatarUrl ? `<img src="${avatarUrl}" alt="${displayName}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">` : displayName.charAt(0).toUpperCase()}
                    ${conv.is_online ? '<span class="online-dot"></span>' : ''}
                </div>
                <div class="conversation-info">
                    <div class="conversation-name">${displayName}</div>
                    ${conv.latest_message ? `<div class="conversation-preview">${(conv.latest_message.message || '').substring(0, 40)}...</div>` : ''}
                </div>
                ${conv.updated_at ? `<div class="conversation-time">${conv.updated_at}</div>` : ''}
                ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
            </div>
        `;
            }).join('');
        } catch (error) {
            console.error('Error generating conversation HTML:', error);
            html += '<div class="no-conversations"><p style="color: red;">Error rendering chats</p></div>';
        }
    }

    console.log('Setting innerHTML, HTML length:', html.length);
    listEl.innerHTML = html;
    console.log('Conversations rendered successfully');
}

// Old render function kept for backwards compatibility
function renderConversations(conversations) {
    const listEl = document.getElementById('conversationsList');

    if (conversations.length === 0) {
        listEl.innerHTML = '<div class="no-conversations"><p>No conversations yet<br>Click <i class="bx bx-edit"></i> to start chatting</p></div>';
        return;
    }

    listEl.innerHTML = conversations.map(conv => `
        <div class="conversation-item ${conv.id === window.currentConversationId ? 'active' : ''}"
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
    window.currentConversationId = conversationId;
    window.lastRenderedMessageId = 0; // Reset so refreshMessages re-renders for new conversation

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
    // Reset cursor state for this conversation
    window.chatOldestId = null;
    window.chatHasMore = false;

    try {
        const data = await apiCall(`/api/chat/conversations/${conversationId}/messages`);
        console.log('Messages data received:', data);
        
        // Load conversation details
        let communityId = null;
        try {
            const convResponse = await apiCall(`/api/chat/conversations/${conversationId}`);
            window.conversationUsers = convResponse.users || [];
            communityId = convResponse.conversation?.community_id || null;
        } catch (e) {
            console.warn('Failed to load conversation details:', e);
        }
        
        // Handle different possible data structures
        let messages = data.messages || data.data || [];
        let conversationType = data.conversation?.type || 'direct';

        // Store cursor state
        window.chatOldestId = data.oldest_id || (messages.length ? messages[0].id : null);
        window.chatHasMore = data.has_more || false;
        
        console.log('Rendering messages:', messages.length, 'Type:', conversationType, 'has_more:', window.chatHasMore);
        
        renderChatArea(conversationName, messages, conversationType, conversationId, communityId);

        // Show pinned message banner if any
        if (data.pinned_message) {
            setTimeout(() => showPinnedBanner(data.pinned_message), 100);
        }

        // Inject "Load older messages" button after render
        setTimeout(() => updateLoadOlderButton(), 50);
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Inject or remove the "Load older messages" button at the top of the chat
function updateLoadOlderButton() {
    const messagesEl = document.getElementById('chatMessages');
    if (!messagesEl) return;

    // Remove existing button if any
    const existing = messagesEl.querySelector('.load-older-btn-wrap');
    if (existing) existing.remove();

    if (!window.chatHasMore) return;

    const wrap = document.createElement('div');
    wrap.className = 'load-older-btn-wrap text-center py-2';
    wrap.innerHTML = `<button class="btn btn-sm btn-outline-secondary load-older-btn" onclick="loadOlderMessages()">
        <i class="bx bx-up-arrow-alt me-1"></i> Load older messages
    </button>`;
    messagesEl.prepend(wrap);
}

// Load older messages (cursor-based, prepend to chat)
async function loadOlderMessages() {
    const convId = window.currentConversationId;
    if (!convId || !window.chatOldestId) return;

    const btn = document.querySelector('.load-older-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Loading...';
    }

    try {
        const data = await apiCall(`/api/chat/conversations/${convId}/messages?before_id=${window.chatOldestId}`);
        const olderMessages = data.messages || data.data || [];

        window.chatHasMore = data.has_more || false;
        if (olderMessages.length > 0) {
            window.chatOldestId = data.oldest_id || olderMessages[0].id;
        }

        const messagesEl = document.getElementById('chatMessages');
        if (!messagesEl) return;

        // Record scroll height before prepend to preserve scroll position
        const prevScrollHeight = messagesEl.scrollHeight;

        // Remove the button wrap before prepending messages
        const btnWrap = messagesEl.querySelector('.load-older-btn-wrap');
        if (btnWrap) btnWrap.remove();

        // Prepend older messages HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = renderMessages(olderMessages);
        while (tempDiv.firstChild) {
            messagesEl.prepend(tempDiv.lastChild);
        }

        // Re-inject button at top if more messages exist
        updateLoadOlderButton();

        // Restore scroll position so the user stays where they were
        messagesEl.scrollTop = messagesEl.scrollHeight - prevScrollHeight;

    } catch (error) {
        console.error('Error loading older messages:', error);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-up-arrow-alt me-1"></i> Load older messages';
        }
    }
}

// Render chat area
async function renderChatArea(conversationName, messages, conversationType = 'direct', conversationId = null, communityId = null) {
    const chatMain = document.getElementById('chatMain');

    // Set the current conversation ID and name for message sending
    window.currentConversationId = conversationId;
    window.currentConversationName = conversationName;
    window.currentCommunityId = communityId;
    // Close in-chat search when switching conversations
    if (typeof closeInChatSearch === 'function') closeInChatSearch();

    // Check if this is a community conversation
    const isCommunity = communityId !== null && communityId !== undefined;

    // Get conversation avatar if available
    let avatarHtml = `<div class="chat-header-avatar" style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #d4af37, #c5a028); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px;">${conversationName.charAt(0).toUpperCase()}</div>`;
    try {
        const convData = await apiCall(`/api/chat/conversations/${conversationId}`);
        // For group/community conversations, use conversation avatar
        if (convData.conversation && convData.conversation.avatar) {
            avatarHtml = `<div class="chat-header-avatar" style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden;"><img src="${convData.conversation.avatar}" alt="${conversationName}" style="width: 100%; height: 100%; object-fit: cover;"></div>`;
        } 
        // For direct conversations, use the other user's avatar
        else if (convData.conversation && convData.conversation.type === 'direct' && convData.users && convData.users.length > 0) {
            const otherUser = convData.users.find(u => u.id !== window.currentUserId);
            if (otherUser && otherUser.avatar) {
                avatarHtml = `<div class="chat-header-avatar" style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden;"><img src="${otherUser.avatar}" alt="${conversationName}" style="width: 100%; height: 100%; object-fit: cover;"></div>`;
            }
        }
    } catch (e) {
        // Use default avatar if fetch fails
        console.warn('Failed to load conversation avatar:', e);
    }

    chatMain.innerHTML = `
        <div class="chat-header">
            <div class="chat-header-info">
                ${avatarHtml}
                <div class="chat-header-title">
                    <h5>${conversationName}</h5>
                    <p>${isCommunity ? 'Community Chat' : ''}</p>
                </div>
            </div>
            <div class="chat-header-actions">
                ${conversationType === 'group' && conversationId && !isCommunity ? `<button class="btn btn-sm btn-outline-primary me-2" onclick="openGroupManagement(${conversationId})" title="Manage Group"><i class="bx bx-cog"></i> Manage</button>` : ''}
                <button class="btn btn-sm btn-outline-secondary" id="inChatSearchToggle" onclick="toggleInChatSearch()" title="Search in this conversation"><i class="bx bx-search-alt-2"></i></button>
            </div>
        </div>
        <div class="in-chat-search-bar" id="inChatSearchBar">
            <i class="bx bx-search" style="color:var(--bs-secondary-color,#6b7280);font-size:.9rem;"></i>
            <input type="text" id="inChatSearchInput" placeholder="Search in conversation…" oninput="runInChatSearch(this.value)" onkeydown="inChatSearchKey(event)" autocomplete="off">
            <div class="in-chat-search-nav">
                <span id="inChatSearchCount"></span>
                <button onclick="stepInChatSearch(-1)" title="Previous"><i class="bx bx-chevron-up"></i></button>
                <button onclick="stepInChatSearch(1)" title="Next"><i class="bx bx-chevron-down"></i></button>
                <button onclick="closeInChatSearch()" title="Close"><i class="bx bx-x"></i></button>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            ${renderMessages(messages)}
        </div>

        <div id="pinnedBanner" class="pinned-banner" style="display:none;">
            <i class="bx bx-pin"></i>
            <span class="pinned-banner-text"></span>
            <button class="pinned-banner-close" title="Unpin" onclick="unpinCurrentMessage()">×</button>
        </div>

        <div id="replyContext" class="reply-context-bar" style="display:none;">
            <i class="bx bx-reply"></i>
            <span class="reply-context-text"></span>
            <button class="reply-context-close" onclick="cancelReply()">×</button>
        </div>

        <div class="chat-input-area">
            <div class="message-input-wrapper">
                <textarea id="messageInput" placeholder="Type @ to mention someone, @everyone to mention all..." rows="1"></textarea>
                <div id="mentionSuggestions" class="mention-suggestions" style="display: none;"></div>
                <div class="message-input-actions">
                    <button type="button" id="attachBtn" title="Attach file">
                        <i class="bx bx-paperclip"></i>
                    </button>
                    <button type="button" id="emojiBtn" title="Add emoji">
                        <i class="bx bx-smile"></i>
                    </button>
                    <button type="button" id="gifBtn" title="Add GIF">
                        <i class="bx bx-image"></i>
                    </button>
                    <button type="button" id="sendButton" title="Send message">
                        <i class="bx bx-send"></i>
                    </button>
                </div>
            </div>
            <input type="file" id="fileInput" multiple style="display: none" accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.txt,.zip,.rar,.mp3,.mp4,.wav,.m4a,.ogg,.webm,.png,.jpg,.jpeg,.gif,.bmp,.svg">
            <div id="attachmentPreview" class="attachment-preview-area" style="display:none;"></div>
        </div>
    `;

    // Scroll to bottom
    setTimeout(() => {
        const messagesEl = document.getElementById('chatMessages');
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }, 100);

    // Track the last rendered message ID so refreshMessages won't re-render on first poll
    window.lastRenderedMessageId = messages.length > 0 ? messages[messages.length - 1].id : 0;

    // Add event listeners
    document.getElementById('sendButton').addEventListener('click', sendMessage);
    document.getElementById('messageInput').addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            // Don't send if mention suggestions are showing (Enter selects mention)
            const mentionContainer = document.getElementById('mentionSuggestions');
            if (mentionContainer && mentionContainer.style.display !== 'none') return;
            e.preventDefault();
            sendMessage();
        }
    });
    document.getElementById('attachBtn').addEventListener('click', () => {
        document.getElementById('fileInput').click();
    });
    document.getElementById('fileInput').addEventListener('change', handleFileSelect);

    // Auto-expand textarea as user types, up to max-height
    const msgInput = document.getElementById('messageInput');
    window._chatAutoResizeFunc = function() {
        msgInput.style.height = 'auto';
        const newHeight = Math.min(msgInput.scrollHeight, 160);
        msgInput.style.height = newHeight + 'px';
    };
    msgInput.addEventListener('input', window._chatAutoResizeFunc);
    // Reset height after send
    window._chatAutoResizeReset = function() {
        msgInput.style.height = 'auto';
    };

    // ── Paste handler: images from clipboard (Ctrl+V) + Excel table formatting ──
    document.getElementById('messageInput').addEventListener('paste', function(e) {
        const clipData = e.clipboardData || window.clipboardData;
        const items    = clipData ? clipData.items : [];

        // Check clipboard contents
        const imageItems = [];
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.startsWith('image/')) imageItems.push(items[i]);
        }
        const plainRaw = clipData && clipData.getData ? (clipData.getData('text/plain') || '') : '';
        const htmlRaw  = clipData && clipData.getData ? (clipData.getData('text/html')  || '') : '';
        const hasTabularHtml = htmlRaw.includes('<table') || htmlRaw.includes('<TD') || htmlRaw.includes('<td');
        const plainLines   = plainRaw.split(/\r?\n/).filter(l => l !== '');
        const hasTabularTxt = plainLines.some(l => l.includes('\t'));

        // 1. Image paste — if we have images AND no meaningful text/table content, treat as image paste
        //    This handles screenshots and copied images from other apps
        const hasMeaningfulText = plainRaw.trim().length > 0 && !plainRaw.startsWith('file://') && !plainRaw.match(/^[\s\n]*$/);
        const hasTabularContent = hasTabularTxt || hasTabularHtml;
        const isPureImage = imageItems.length > 0 && !hasMeaningfulText && !hasTabularContent;
        
        if (isPureImage) {
            e.preventDefault();
            const dt = new DataTransfer();
            imageItems.forEach((item, idx) => {
                const file = item.getAsFile();
                if (file) {
                    const ext   = (file.type.split('/')[1] || 'png').split(';')[0];
                    const named = new File([file], `clipboard-image-${Date.now()}-${idx}.${ext}`, { type: file.type });
                    dt.items.add(named);
                }
            });
            if (dt.files.length > 0) {
                const fileInput = document.getElementById('fileInput');
                fileInput.files = dt.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            return;
        }

        // 2. Text / Excel paste
        //    If clipboard HTML contains a table (any column count), prefix with [[TABLE]]
        //    so formatMessageText() always renders it as an HTML table, not plain text.
        if (plainRaw || htmlRaw) {
            e.preventDefault();
            const plainLines    = plainRaw.split(/\r?\n/).filter(l => l !== '');
            const hasTabularTxt = plainLines.some(l => l.includes('\t'));
            const hasTabularHtml = htmlRaw.includes('<table') || htmlRaw.includes('<TD') || htmlRaw.includes('<td');
            const isTabular     = (hasTabularTxt || hasTabularHtml) && plainLines.length >= 2;

            // Prefix with [[TABLE]] so the renderer knows to build an HTML table
            const toInsert = isTabular ? '[[TABLE]]' + plainRaw : plainRaw;

            const ta    = e.target;
            const start = ta.selectionStart;
            const end   = ta.selectionEnd;
            ta.value = ta.value.substring(0, start) + toInsert + ta.value.substring(end);
            ta.selectionStart = ta.selectionEnd = start + toInsert.length;
            // Trigger auto-resize so pasted content expands the textarea
            if (window._chatAutoResizeFunc) window._chatAutoResizeFunc();
            // Close mention suggestions
            const mentionEl = document.getElementById('mentionSuggestions');
            if (mentionEl) mentionEl.style.display = 'none';
        }
    });

    // Add emoji button listener
    const emojiBtn = document.getElementById('emojiBtn');
    if (emojiBtn) {
        emojiBtn.addEventListener('click', () => {
            const input = document.getElementById('messageInput');
            if (input && window.EmojiPicker) {
                window.EmojiPicker.show(input);
            }
        });
    }
    
    // Add GIF button listener
    document.getElementById('gifBtn').addEventListener('click', showGifPicker);

    // Typing indicator — POST to server on input, poll server for others' typing state
    const msgInputEl = document.getElementById('messageInput');
    let _typingPostTimer = null;
    let _typingPosted = false;
    if (msgInputEl) {
        msgInputEl.addEventListener('input', () => {
            if (!window.currentConversationId) return;
            // Throttle: only POST once every 3s per burst
            if (!_typingPosted) {
                _typingPosted = true;
                fetch(`/chat/conversations/${window.currentConversationId}/typing`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'X-Requested-With': 'XMLHttpRequest' }
                }).catch(() => {});
            }
            clearTimeout(_typingPostTimer);
            _typingPostTimer = setTimeout(() => { _typingPosted = false; }, 3000);
        });
    }

    // Load mention users (event delegation handles the actual autocomplete)
    await loadMentionUsers();

    // Start auto-refresh for messages (5 seconds for near real-time updates)
    clearInterval(window.messagesRefreshInterval);
    // 15s poll — Echo handles real-time; this is just a safety net for missed events
    window.messagesRefreshInterval = setInterval(() => {
        if (window.currentConversationId) {
            refreshMessages();
        }
    }, 15000);

    // Typing status poll — check every 2s who is typing in this conversation
    clearInterval(window._typingPollInterval);
    window._typingPollInterval = setInterval(() => {
        const convId = window.currentConversationId;
        if (!convId) return;
        fetch(`/chat/conversations/${convId}/typing-status`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            if (data.typers && data.typers.length > 0) {
                showTypingIndicator(data.typers[0]);
            } else {
                // No one typing — hide indicator if shown
                const tr = document.getElementById('typingIndicatorRow');
                if (tr) { tr.remove(); }
            }
        })
        .catch(() => {});
    }, 2000);
}

// ── In-chat message search ────────────────────────────────────────────────
var _inChatSearchMatches = [];
var _inChatSearchIndex   = -1;

function toggleInChatSearch() {
    const bar   = document.getElementById('inChatSearchBar');
    const input = document.getElementById('inChatSearchInput');
    if (!bar) return;
    if (bar.classList.contains('visible')) {
        closeInChatSearch();
    } else {
        bar.classList.add('visible');
        if (input) input.focus();
    }
}

function closeInChatSearch() {
    clearInChatHighlights();
    const bar   = document.getElementById('inChatSearchBar');
    const input = document.getElementById('inChatSearchInput');
    const count = document.getElementById('inChatSearchCount');
    if (bar)   bar.classList.remove('visible');
    if (input) input.value = '';
    if (count) count.textContent = '';
    _inChatSearchMatches = [];
    _inChatSearchIndex   = -1;
}

function runInChatSearch(query) {
    clearInChatHighlights();
    _inChatSearchMatches = [];
    _inChatSearchIndex   = -1;
    document.getElementById('inChatSearchCount').textContent = '';
    if (!query || query.trim().length < 2) return;

    const term   = query.trim();
    const re     = new RegExp(term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
    const textEls = document.querySelectorAll('#chatMessages .message-text');

    textEls.forEach(el => {
        const original = el.innerHTML;
        // Highlight all matches within the element
        const highlighted = original.replace(/<[^>]+>/g, m => m)
            .replace(/(?<=>|^)([^<]+)/g, (seg) =>
                seg.replace(re, m => `<mark class="msg-search-highlight">${escapeHtml(m)}</mark>`)
            );
        if (highlighted !== original) {
            el.innerHTML = highlighted;
            el.querySelectorAll('.msg-search-highlight').forEach(mark => {
                _inChatSearchMatches.push(mark);
            });
        }
    });

    if (_inChatSearchMatches.length > 0) {
        _inChatSearchIndex = 0;
        _activateSearchMatch(0);
    }
    _updateSearchCount();
}

function _activateSearchMatch(idx) {
    _inChatSearchMatches.forEach(m => m.classList.remove('current'));
    if (_inChatSearchMatches[idx]) {
        _inChatSearchMatches[idx].classList.add('current');
        _inChatSearchMatches[idx].scrollIntoView({ block: 'center', behavior: 'smooth' });
    }
}

function stepInChatSearch(dir) {
    if (!_inChatSearchMatches.length) return;
    _inChatSearchIndex = (_inChatSearchIndex + dir + _inChatSearchMatches.length) % _inChatSearchMatches.length;
    _activateSearchMatch(_inChatSearchIndex);
    _updateSearchCount();
}

function _updateSearchCount() {
    const el = document.getElementById('inChatSearchCount');
    if (!el) return;
    el.textContent = !_inChatSearchMatches.length
        ? 'No results'
        : `${_inChatSearchIndex + 1} / ${_inChatSearchMatches.length}`;
}

function clearInChatHighlights() {
    document.querySelectorAll('#chatMessages .msg-search-highlight').forEach(mark => {
        const parent = mark.parentNode;
        if (!parent) return;
        parent.replaceChild(document.createTextNode(mark.textContent), mark);
        parent.normalize();
    });
}

function inChatSearchKey(e) {
    if (e.key === 'Enter') { stepInChatSearch(e.shiftKey ? -1 : 1); e.preventDefault(); }
    if (e.key === 'Escape') { closeInChatSearch(); }
}
// ─────────────────────────────────────────────────────────────────────────

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format message time (e.g., "10:30 AM PT")
function formatMessageTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: 'America/Los_Angeles' }) + ' PT';
}

// Helper function to get date label (Today, Yesterday, or formatted date)
function getDateLabel(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    const dateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const yesterdayOnly = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate());
    
    if (dateOnly.getTime() === todayOnly.getTime()) {
        return 'Today';
    } else if (dateOnly.getTime() === yesterdayOnly.getTime()) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined });
    }
}

// Helper function to get date key for grouping (YYYY-MM-DD)
function getDateKey(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return date.toISOString().split('T')[0];
}

// Helper function to format message text with mentions and GIFs
function formatMessageText(text) {
    if (!text) return '';

    // GIF message
    if (text.startsWith('[GIF]')) {
        const gifUrl = text.substring(5);
        return `<img src="${gifUrl}" alt="GIF" style="max-width: 300px; border-radius: 8px; display: block; margin: 4px 0;">`;
    }

    // Table: explicitly marked by paste handler with [[TABLE]] prefix
    const isMarkedTable = text.startsWith('[[TABLE]]');
    const rawText       = isMarkedTable ? text.substring(9) : text;
    const rawLines      = rawText.split(/\r?\n/).filter(l => l !== '');
    const hasTabs       = rawLines.some(l => l.includes('\t'));
    const isTsv         = isMarkedTable || (rawLines.length >= 2 && hasTabs);

    if (isTsv && rawLines.length > 0) {
        const rows     = rawLines.map(l => l.split('\t'));
        const colCount = Math.max(...rows.map(r => r.length));
        let html = '<div class="chat-table-wrap"><table class="chat-table"><tbody>';
        rows.forEach(row => {
            html += '<tr>';
            for (let c = 0; c < colCount; c++) {
                const cell = escapeHtml((row[c] || '').trim());
                html += `<td>${cell}</td>`;
            }
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    // Regular text
    let escaped = escapeHtml(rawText);
    escaped = escaped.replace(/@\[([^\]]+)\]/g, '<span class="mention-highlight">@$1</span>');
    escaped = escaped.replace(/@(everyone|\w+)/g, '<span class="mention-highlight">@$1</span>');
    escaped = escaped.replace(/\n/g, '<br>');
    return escaped;
}

// ─── Render reaction pills for a message ─────────────────────────────────────
function renderReactionPills(reactions, messageId, isSender) {
    if (!reactions || reactions.length === 0) return '';
    // Group by emoji
    const grouped = {};
    reactions.forEach(r => {
        const e = r.emoji;
        if (!grouped[e]) grouped[e] = { count: 0, users: [], reacted: false };
        grouped[e].count++;
        grouped[e].users.push(r.user?.name || '');
        if (r.user_id === window.currentUserId) grouped[e].reacted = true;
    });
    const pills = Object.entries(grouped).map(([emoji, g]) =>
        `<button class="reaction-pill ${g.reacted ? 'my-reaction' : ''}" onclick="reactToMessage(${messageId}, '${emoji}')" title="${g.users.join(', ')}">${emoji} ${g.count}</button>`
    ).join('');
    return `<div class="reaction-pills">${pills}</div>`;
}

// ─── Reaction picker ──────────────────────────────────────────────────────────
const REACTION_EMOJIS = ['👍','❤️','😂','😮','😢','😡','🎉','🔥'];
let _pickerEl = null;
let _pickerMsgId = null;

function showReactionPicker(messageId, btn) {
    // Close existing picker
    if (_pickerEl) { _pickerEl.remove(); _pickerEl = null; }
    if (_pickerMsgId === messageId) { _pickerMsgId = null; return; }
    _pickerMsgId = messageId;

    const picker = document.createElement('div');
    picker.className = 'reaction-picker-popup';
    picker.style.cssText = 'position:absolute;z-index:400;';
    picker.innerHTML = REACTION_EMOJIS.map(e =>
        `<button onclick="reactToMessage(${messageId},'${e}');this.closest('.reaction-picker-popup').remove();">${e}</button>`
    ).join('');

    // Position relative to button
    const msgEl = btn.closest('.message-item');
    msgEl.style.position = 'relative';
    msgEl.appendChild(picker);
    _pickerEl = picker;

    // Position above the button
    const btnRect = btn.getBoundingClientRect();
    const msgRect = msgEl.getBoundingClientRect();
    picker.style.bottom = (msgRect.bottom - btnRect.top + 4) + 'px';
    picker.style.right = '0';

    setTimeout(() => {
        document.addEventListener('click', function closePicker(e) {
            if (!picker.contains(e.target) && e.target !== btn) {
                picker.remove(); _pickerEl = null; _pickerMsgId = null;
                document.removeEventListener('click', closePicker);
            }
        });
    }, 0);
}

async function reactToMessage(messageId, emoji) {
    try {
        const data = await apiCall(`/api/chat/messages/${messageId}/react`, 'POST', { emoji });
        // Re-render reaction pills for this message
        const msgEl = document.querySelector(`.message-item[data-message-id="${messageId}"]`);
        if (msgEl) {
            const isSender = msgEl.classList.contains('message-sender');
            const existing = msgEl.querySelector('.reaction-pills');
            const newHtml = renderReactionPills(
                data.reactions.flatMap(r => Array.from({length: r.count}, (_, i) => ({
                    emoji: r.emoji,
                    user: { name: r.users[i] || '' },
                    user_id: r.reacted && i === 0 ? window.currentUserId : 0
                }))),
                messageId, isSender
            );
            if (existing) {
                existing.outerHTML = newHtml || '';
            } else {
                const timeEl = msgEl.querySelector('.message-time');
                if (timeEl && newHtml) timeEl.insertAdjacentHTML('afterend', newHtml);
            }
        }
    } catch (e) { console.error('React error', e); }
}

// ─── Reply to message ─────────────────────────────────────────────────────────
window.replyToId = null;

function startReply(messageId, senderName, preview) {
    window.replyToId = messageId;
    const bar = document.getElementById('replyContext');
    if (bar) {
        bar.querySelector('.reply-context-text').textContent = `${senderName}: ${preview}`;
        bar.style.display = 'flex';
    }
    const input = document.getElementById('messageInput');
    if (input) input.focus();
}

function cancelReply() {
    window.replyToId = null;
    const bar = document.getElementById('replyContext');
    if (bar) bar.style.display = 'none';
}

function scrollToMessage(messageId) {
    const el = document.querySelector(`.message-item[data-message-id="${messageId}"]`);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.style.transition = 'background .3s';
        el.style.background = 'rgba(212,175,55,.2)';
        setTimeout(() => el.style.background = '', 1200);
    }
}

async function searchJumpToMessage(conversationId, conversationName, messageId) {
    document.getElementById('chatSearchResults').style.display = 'none';
    document.getElementById('searchConversations').value = '';
    // Load the conversation first, then scroll to message
    await selectConversation(conversationId, conversationName, null);
    // Wait for DOM to render then scroll
    let attempts = 0;
    const tryScroll = setInterval(() => {
        const el = document.querySelector(`.message-item[data-message-id="${messageId}"]`);
        if (el) {
            clearInterval(tryScroll);
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.style.transition = 'background .4s';
            el.style.background = 'rgba(212,175,55,.25)';
            setTimeout(() => el.style.background = '', 2000);
        } else if (++attempts > 20) {
            clearInterval(tryScroll); // give up after 2s
        }
    }, 100);
}

// ─── Pin / unpin ──────────────────────────────────────────────────────────────
window._pinnedMessageId = null;

async function togglePin(messageId) {
    try {
        const msgEl = document.querySelector(`.message-item[data-message-id="${messageId}"]`);
        const isCurrentlyPinned = window._pinnedMessageId === messageId;
        if (isCurrentlyPinned) {
            await apiCall(`/api/chat/messages/${messageId}/pin`, 'DELETE');
            window._pinnedMessageId = null;
            const banner = document.getElementById('pinnedBanner');
            if (banner) banner.style.display = 'none';
        } else {
            const data = await apiCall(`/api/chat/messages/${messageId}/pin`, 'POST');
            window._pinnedMessageId = messageId;
            showPinnedBanner(data.pinned_message);
        }
    } catch (e) { console.error('Pin error', e); }
}

function showPinnedBanner(pinnedMsg) {
    const banner = document.getElementById('pinnedBanner');
    if (!banner || !pinnedMsg) return;
    banner.querySelector('.pinned-banner-text').textContent =
        `${pinnedMsg.user?.name || 'Unknown'}: ${(pinnedMsg.message || '📎 Attachment').substring(0, 80)}`;
    banner.style.display = 'flex';
    window._pinnedMessageId = pinnedMsg.id;
}

async function unpinCurrentMessage() {
    if (!window._pinnedMessageId) return;
    await togglePin(window._pinnedMessageId);
}

// ─── Online heartbeat ─────────────────────────────────────────────────────────
(function startHeartbeat() {
    const beat = () => fetch('/api/chat/heartbeat', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
        credentials: 'same-origin',
    }).catch(() => {});
    beat();
    setInterval(beat, 30000);
})();

// Render messages
function renderMessages(messages) {
    if (messages.length === 0) {
        return '<div class="no-messages"><i class="bx bx-message-dots"></i><p>No messages yet. Start the conversation!</p></div>';
    }

    let html = '';
    let lastDateKey = '';
    
    messages.forEach((msg, index) => {
        const currentDateKey = getDateKey(msg.created_at);
        
        // Add date separator if date changed
        if (currentDateKey && currentDateKey !== lastDateKey) {
            const dateLabel = getDateLabel(msg.created_at);
            html += `
            <div class="message-date-separator" style="display:flex;align-items:center;justify-content:center;margin:1rem 0;gap:.75rem;">
                <div style="flex:1;height:1px;background:var(--bs-border-color, rgba(0,0,0,.1));"></div>
                <span style="font-size:.7rem;font-weight:600;color:var(--bs-secondary-color, #6b7280);background:var(--bs-body-bg, #fff);padding:.25rem .75rem;border-radius:20px;border:1px solid var(--bs-border-color, rgba(0,0,0,.1));">${dateLabel}</span>
                <div style="flex:1;height:1px;background:var(--bs-border-color, rgba(0,0,0,.1));"></div>
            </div>`;
            lastDateKey = currentDateKey;
        }
        
        const isSender = (msg.user_id || msg.user?.id) === window.currentUserId;
        const userName = escapeHtml(msg.user?.name || 'Unknown User');
        const userAvatar = msg.user?.avatar;
        const messageTime = formatMessageTime(msg.created_at);
        const avatarHtml = userAvatar 
            ? `<img src="${userAvatar}" alt="${userName}" class="message-avatar">` 
            : `<div class="message-avatar">${userName.charAt(0).toUpperCase()}</div>`;

        // Consecutive grouping: same sender, within 60 seconds, same date — hide avatar/name
        const prevMsg = index > 0 ? messages[index - 1] : null;
        const prevUserId = prevMsg ? (prevMsg.user_id || prevMsg.user?.id) : null;
        const timeDiff = prevMsg && prevMsg.created_at && msg.created_at
            ? Math.abs(new Date(msg.created_at) - new Date(prevMsg.created_at)) / 1000
            : Infinity;
        const isConsecutive = !!(prevMsg && prevUserId === (msg.user_id || msg.user?.id)
            && timeDiff < 60
            && getDateKey(msg.created_at) === getDateKey(prevMsg.created_at));

        html += `
        <div class="message-item ${isSender ? 'message-sender' : 'message-receiver'}${isConsecutive ? ' message-consecutive' : ''}" data-message-id="${msg.id}">
            ${!isSender ? avatarHtml : ''}
            <div class="message-bubble-wrap">
            <div class="message-content">
                ${!isSender && !isConsecutive ? `<div class="message-username">${userName}</div>` : ''}
                ${msg.forwarded_from_user_name ? `<div class="message-forwarded" style="font-size:.7rem;color:#8b5cf6;margin-bottom:4px;"><i class="bx bx-share" style="font-size:.7rem;"></i> Forwarded from <strong>${escapeHtml(msg.forwarded_from_user_name)}</strong></div>` : ''}
                ${msg.reply_to ? `<div class="reply-preview" onclick="scrollToMessage(${msg.reply_to.id})"><div class="reply-preview-name">${escapeHtml(msg.reply_to.user?.name || 'Unknown')}</div><div class="reply-preview-text">${escapeHtml((msg.reply_to.message || '\u{1f4ce} Attachment').substring(0, 80))}</div></div>` : ''}
                ${msg.message ? `<div class="message-text">${formatMessageText(msg.message)}</div>` : ''}
                ${msg.is_edited ? `<span class="message-edited" style="font-size:.6rem;color:#94a3b8;font-style:italic;">(edited)</span>` : ''}
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
                <div class="message-time">${messageTime}${isSender ? `<span class="msg-ticks ${msg.is_read ? 'ticks-read' : ''}"><i class="bx ${msg.is_read ? 'bx-check-double' : 'bx-check'}"></i></span><span data-ts="${msg.created_at}" style="display:none"></span>` : ''}</div>
                ${renderReactionPills(msg.reactions || [], msg.id, isSender)}
            </div>
            ${isSender ? `
            <div class="message-actions">
                <button onclick="startReply(${msg.id}, ${JSON.stringify(msg.user?.name||'')}, ${JSON.stringify((msg.message||'').substring(0,60))})" title="Reply"><i class="bx bx-reply"></i></button>
                <button onclick="showReactionPicker(${msg.id}, this)" title="React"><i class="bx bx-smile"></i></button>
                <button onclick="startEditMessage(${msg.id}, ${JSON.stringify(msg.message || '').replace(/"/g, '&quot;')})" title="Edit"><i class="bx bx-edit-alt"></i></button>
                <button onclick="showForwardPicker(${msg.id})" title="Forward"><i class="bx bx-share"></i></button>
                <button onclick="togglePin(${msg.id})" title="${msg.is_pinned ? 'Unpin' : 'Pin'}"><i class="bx bx-pin"></i></button>
                <button onclick="deleteMessage(${msg.id})" title="Delete"><i class="bx bx-trash"></i></button>
            </div>` : `
            <div class="message-actions">
                <button onclick="startReply(${msg.id}, ${JSON.stringify(msg.user?.name||'')}, ${JSON.stringify((msg.message||'').substring(0,60))})" title="Reply"><i class="bx bx-reply"></i></button>
                <button onclick="showReactionPicker(${msg.id}, this)" title="React"><i class="bx bx-smile"></i></button>
                <button onclick="showForwardPicker(${msg.id})" title="Forward"><i class="bx bx-share"></i></button>
                <button onclick="togglePin(${msg.id})" title="${msg.is_pinned ? 'Unpin' : 'Pin'}"><i class="bx bx-pin"></i></button>
            </div>`}
            </div>
        </div>
    `;
    });
    
    return html;
}

// Refresh messages
async function refreshMessages() {
    if (!window.currentConversationId) return;

    try {
        if (typeof window.lastRenderedMessageId === 'undefined') window.lastRenderedMessageId = 0;
        const sinceId = window.lastRenderedMessageId;
        const url = sinceId > 0
            ? `/api/chat/conversations/${window.currentConversationId}/messages?since_id=${sinceId}`
            : `/api/chat/conversations/${window.currentConversationId}/messages`;
        const data = await apiCall(url);
        const isDelta = data.is_delta === true;

        // Handle different possible data structures
        const messages = data.messages || data.data || [];
        
        const messagesEl = document.getElementById('chatMessages');

        // Append-only refresh: only new messages animate in; existing messages are untouched

        const silentRender = (msgs) => {
            // Temporarily suppress the msgIn animation so bulk renders don't flash
            let styleEl = document.getElementById('no-msg-anim');
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = 'no-msg-anim';
                styleEl.textContent = '.message-item { animation: none !important; }';
                document.head.appendChild(styleEl);
            }
            messagesEl.innerHTML = renderMessages(msgs);
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    const s = document.getElementById('no-msg-anim');
                    if (s) s.remove();
                });
            });
        };

        // Update read receipts (ticks) for already-rendered sender messages
        if (data.read_up_to) {
            const readUpTo = new Date(data.read_up_to);
            messagesEl.querySelectorAll('.message-item.message-sender[data-message-id]').forEach(el => {
                const tickEl = el.querySelector('.msg-ticks');
                if (!tickEl || tickEl.classList.contains('ticks-read')) return;
                // Get message time from the element's data or rendered time text
                const msgId = parseInt(el.getAttribute('data-message-id'));
                // Use the message timestamp stored in the time element's data attribute if available
                const timeEl = el.querySelector('[data-ts]');
                const msgTime = timeEl ? new Date(timeEl.getAttribute('data-ts')) : null;
                if (msgTime && msgTime <= readUpTo) {
                    tickEl.classList.add('ticks-read');
                    tickEl.innerHTML = '<i class="bx bx-check-double"></i>';
                }
            });
        }

        if (messages.length === 0) {
            if (!isDelta && !messagesEl.querySelector('.message-item')) {
                messagesEl.innerHTML = '<div class="no-messages"><i class="bx bx-message-dots"></i><p>No messages yet. Start the conversation!</p></div>';
            }
            return;
        }

        if (window.lastRenderedMessageId === 0) {
            // Initial load: render all silently then scroll to bottom
            silentRender(messages);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            window.lastRenderedMessageId = messages[messages.length - 1].id;
        } else if (isDelta) {
            // Delta response: skip messages already in DOM, and skip own messages
            // still in-flight (optimistic bubble present) to avoid race-condition duplicates
            const newMessages = messages.filter(m => {
                if (messagesEl.querySelector(`[data-message-id="${m.id}"]`)) return false;
                // If we're mid-send and this is our own message, let sendMessage handle it
                if (window._sendingMessage && m.user_id === window.currentUserId) return false;
                return true;
            });
            if (newMessages.length > 0) {
                const atBottom = messagesEl.scrollHeight - messagesEl.scrollTop <= messagesEl.clientHeight + 4;
                const tmp = document.createElement('div');
                tmp.innerHTML = renderMessages(newMessages);
                tmp.querySelectorAll('.message-item').forEach(el => el.style.animation = 'none');
                while (tmp.firstChild) messagesEl.appendChild(tmp.firstChild);
                if (atBottom) messagesEl.scrollTop = messagesEl.scrollHeight;
            }
            // Always advance the cursor to the highest ID returned
            window.lastRenderedMessageId = messages[messages.length - 1].id;
            if (typeof window.lastMessageId === 'undefined') window.lastMessageId = 0;
            const latest = messages[messages.length - 1];
            if (latest.id > window.lastMessageId) {
                window.lastMessageId = latest.id;
                checkAndNotifyMentions(latest);
            }
        } else {
            // Check for deletions: rendered count vs actual
            const renderedCount = messagesEl.querySelectorAll('.message-item[data-message-id]').length;
            if (renderedCount !== messages.length) {
                // A message was deleted — silent full re-render
                const atBottom = messagesEl.scrollHeight - messagesEl.scrollTop <= messagesEl.clientHeight + 4;
                silentRender(messages);
                if (atBottom) messagesEl.scrollTop = messagesEl.scrollHeight;
                window.lastRenderedMessageId = messages[messages.length - 1].id;
            } else {
                // Append only new messages (these will animate in naturally)
                const newMessages = messages.filter(m =>
                    m.id > window.lastRenderedMessageId &&
                    !messagesEl.querySelector(`[data-message-id="${m.id}"]`)
                );
                if (newMessages.length > 0) {
                    const atBottom = messagesEl.scrollHeight - messagesEl.scrollTop <= messagesEl.clientHeight + 4;
                    const tmp = document.createElement('div');
                    tmp.innerHTML = renderMessages(newMessages);
                    tmp.querySelectorAll('.message-item').forEach(el => el.style.animation = 'none');
                    while (tmp.firstChild) messagesEl.appendChild(tmp.firstChild);
                    if (atBottom) messagesEl.scrollTop = messagesEl.scrollHeight;
                    window.lastRenderedMessageId = newMessages[newMessages.length - 1].id;

                    // Notify for new messages
                    if (typeof window.lastMessageId === 'undefined') window.lastMessageId = 0;
                    const latest = newMessages[newMessages.length - 1];
                    if (latest.id > window.lastMessageId) {
                        window.lastMessageId = latest.id;
                        checkAndNotifyMentions(latest);
                    }
                }
            }
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
        // Remove from DOM immediately (no round-trip wait)
        const el = document.querySelector(`.message-item[data-message-id="${messageId}"]`);
        if (el) el.remove();
        // Whisper to other participants so they see it removed instantly
        if (window._echoChannel) {
            try { window._echoChannel.whisper('message_action', { type: 'delete', id: messageId }); } catch(e) {}
        }
        loadConversations();
    } catch (error) {
        console.error('Error deleting message:', error);
        alert('Failed to delete message: ' + error.message);
    }
}

// === Edit Message ===
window._editingMessageId = null;

function startEditMessage(messageId, currentText) {
    window._editingMessageId = messageId;
    const input = document.getElementById('messageInput');
    // Decode HTML entities
    const decoded = new DOMParser().parseFromString(currentText || '', 'text/html').body.textContent;
    input.value = decoded;
    input.focus();
    // Show edit indicator
    let indicator = document.getElementById('editIndicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'editIndicator';
        indicator.style.cssText = 'padding:6px 12px;background:rgba(85,110,230,.1);border-left:3px solid #556ee6;font-size:.75rem;color:#556ee6;display:flex;align-items:center;justify-content:space-between;';
        const inputArea = input.closest('.message-input-area') || input.parentElement;
        inputArea.insertBefore(indicator, inputArea.firstChild);
    }
    indicator.innerHTML = '<span><i class="bx bx-edit-alt"></i> Editing message</span><button onclick="cancelEdit()" style="background:none;border:none;color:#c84646;cursor:pointer;font-size:.8rem;"><i class="bx bx-x"></i> Cancel</button>';
    indicator.style.display = 'flex';
}

function cancelEdit() {
    window._editingMessageId = null;
    const input = document.getElementById('messageInput');
    input.value = '';
    const indicator = document.getElementById('editIndicator');
    if (indicator) indicator.style.display = 'none';
}

async function saveEditMessage() {
    const messageId = window._editingMessageId;
    const input = document.getElementById('messageInput');
    const newText = input.value.trim();
    if (!messageId || !newText) return;

    try {
        await apiCall(`/api/chat/messages/${messageId}`, 'PUT', { message: newText });
        cancelEdit();
        // Update DOM immediately
        const msgEl = document.querySelector(`.message-item[data-message-id="${messageId}"]`);
        if (msgEl) {
            const textEl = msgEl.querySelector('.message-text');
            if (textEl) textEl.innerHTML = formatMessageText(newText);
            if (!msgEl.querySelector('.message-edited')) {
                const timeEl = msgEl.querySelector('.message-time');
                if (timeEl) timeEl.insertAdjacentHTML('beforebegin', '<span class="message-edited" style="font-size:.6rem;color:#94a3b8;font-style:italic;">(edited)</span>');
            }
        }
        // Whisper edit to others
        if (window._echoChannel) {
            try { window._echoChannel.whisper('message_action', { type: 'edit', id: messageId, text: newText }); } catch(e) {}
        }
    } catch (error) {
        console.error('Error editing message:', error);
        alert('Failed to edit message: ' + error.message);
    }
}

// === Forward Message ===
window._forwardingMessageId = null;

function showForwardPicker(messageId) {
    window._forwardingMessageId = messageId;
    const modal = document.getElementById('forwardModal');
    if (!modal) return;
    const bsModal = new bootstrap.Modal(modal);
    // Load conversations into the picker
    loadForwardConversations();
    bsModal.show();
}

async function loadForwardConversations() {
    const list = document.getElementById('forwardConversationList');
    if (!list) return;
    list.innerHTML = '<div class="text-center py-3"><i class="bx bx-loader-alt bx-spin"></i></div>';

    try {
        const [directData, groupData] = await Promise.all([
            apiCall('/api/chat/conversations'),
            apiCall('/api/chat/group-conversations')
        ]);
        const directs = (directData.conversations || directData.data || []).filter(c => !c.community_id);
        const groups = groupData.conversations || groupData.data || [];
        const all = [...directs, ...groups].filter(c => c.id != window.currentConversationId);

        if (all.length === 0) {
            list.innerHTML = '<div class="text-center text-muted py-3">No other conversations</div>';
            return;
        }

        const searchInput = document.getElementById('forwardSearchInput');
        const renderList = (filter) => {
            const filtered = filter ? all.filter(c => {
                const name = (c.name || c.other_user?.name || '').toLowerCase();
                return name.includes(filter.toLowerCase());
            }) : all;
            list.innerHTML = filtered.map(c => {
                const name = escapeHtml(c.name || c.other_user?.name || 'Unknown');
                const isGroup = c.type === 'group';
                const icon = isGroup ? '<i class="bx bx-group" style="color:#556ee6;"></i>' : '<i class="bx bx-user" style="color:#34c38f;"></i>';
                return `<div class="forward-conv-item" onclick="forwardToConversation(${c.id})" style="padding:10px 14px;cursor:pointer;border-bottom:1px solid rgba(0,0,0,.06);display:flex;align-items:center;gap:10px;transition:background .15s;"
                    onmouseover="this.style.background='rgba(85,110,230,.06)'" onmouseout="this.style.background='transparent'">
                    ${icon}
                    <span style="font-size:.82rem;font-weight:500;">${name}</span>
                </div>`;
            }).join('');
        };
        renderList('');
        if (searchInput) {
            searchInput.value = '';
            searchInput.oninput = () => renderList(searchInput.value);
        }
    } catch (e) {
        list.innerHTML = '<div class="text-center text-danger py-3">Failed to load conversations</div>';
    }
}

async function forwardToConversation(conversationId) {
    if (!window._forwardingMessageId) return;
    try {
        await apiCall(`/api/chat/messages/${window._forwardingMessageId}/forward`, 'POST', { conversation_id: conversationId });
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('forwardModal'));
        if (modal) modal.hide();
        window._forwardingMessageId = null;
        // If target is current conversation, refresh
        if (conversationId == window.currentConversationId) {
            await refreshMessages();
        }
        loadConversations();
    } catch (e) {
        console.error('Forward error:', e);
        alert('Failed to forward message: ' + e.message);
    }
}

// === GIF Picker Functions ===
const GIPHY_API_KEY = 'GlVGYHkr3WSBnllca54iNt0yFbjz7L65'; // Free Giphy API key with better regional support
let gifSearchTimeout = null;

// Show GIF picker modal
function showGifPicker() {
    const modal = new bootstrap.Modal(document.getElementById('gifPickerModal'));
    modal.show();
    
    // Load trending GIFs initially
    loadTrendingGifs();
    
    // Setup search handler
    const searchInput = document.getElementById('gifSearchInput');
    searchInput.value = '';
    searchInput.removeEventListener('input', handleGifSearch);
    searchInput.addEventListener('input', handleGifSearch);
}

// Handle GIF search with debounce
function handleGifSearch(e) {
    const query = e.target.value.trim();
    
    // Clear previous timeout
    clearTimeout(gifSearchTimeout);
    
    // Debounce search
    gifSearchTimeout = setTimeout(() => {
        if (query.length > 0) {
            searchGifs(query);
        } else {
            loadTrendingGifs();
        }
    }, 500);
}

// Load trending GIFs
async function loadTrendingGifs() {
    const gridEl = document.getElementById('gifGrid');
    const loadingEl = document.getElementById('gifLoading');
    
    try {
        gridEl.style.display = 'none';
        loadingEl.style.display = 'block';
        
        const response = await fetch(`https://api.giphy.com/v1/gifs/trending?api_key=${GIPHY_API_KEY}&limit=20&rating=g`);
        const data = await response.json();
        
        renderGifs(data.data);
    } catch (error) {
        console.error('Error loading trending GIFs:', error);
        gridEl.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--bs-danger);"><i class="bx bx-error-circle" style="font-size: 48px; margin-bottom: 12px;"></i><p>Failed to load GIFs. Please try again.</p></div>';
        gridEl.style.display = 'grid';
    } finally {
        loadingEl.style.display = 'none';
    }
}

// Search GIFs
async function searchGifs(query) {
    const gridEl = document.getElementById('gifGrid');
    const loadingEl = document.getElementById('gifLoading');
    
    try {
        gridEl.style.display = 'none';
        loadingEl.style.display = 'block';
        
        const response = await fetch(`https://api.giphy.com/v1/gifs/search?api_key=${GIPHY_API_KEY}&q=${encodeURIComponent(query)}&limit=20&rating=g`);
        const data = await response.json();
        
        if (data.data && data.data.length > 0) {
            renderGifs(data.data);
        } else {
            gridEl.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--bs-secondary-color);"><i class="bx bx-search" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i><p>No GIFs found for "' + query + '"</p></div>';
            gridEl.style.display = 'grid';
        }
    } catch (error) {
        console.error('Error searching GIFs:', error);
        gridEl.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--bs-danger);"><i class="bx bx-error-circle" style="font-size: 48px; margin-bottom: 12px;"></i><p>Failed to search GIFs. Please try again.</p></div>';
        gridEl.style.display = 'grid';
    } finally {
        loadingEl.style.display = 'none';
    }
}

// Render GIFs in grid
function renderGifs(gifs) {
    const gridEl = document.getElementById('gifGrid');
    
    gridEl.innerHTML = gifs.map(gif => {
        // Giphy API structure: use fixed_height_small for preview, original for full
        const previewUrl = gif.images.fixed_height_small.url;
        const fullUrl = gif.images.original.url;
        
        return `
            <div onclick="selectGif('${fullUrl}')" style="
                cursor: pointer; 
                border-radius: 8px; 
                overflow: hidden; 
                aspect-ratio: 1; 
                background: var(--bs-secondary-bg);
                transition: transform 0.2s, box-shadow 0.2s;
                position: relative;
            " onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(218, 165, 32, 0.3)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                <img src="${previewUrl}" alt="GIF" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        `;
    }).join('');
    
    gridEl.style.display = 'grid';
}

// Select and send GIF
async function selectGif(gifUrl) {
    if (!window.currentConversationId) {
        alert('Please select a conversation first');
        return;
    }
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('gifPickerModal'));
    modal.hide();
    
    try {
        // Send GIF URL as message with special marker
        const formData = new FormData();
        formData.append('conversation_id', window.currentConversationId);
        formData.append('message', `[GIF]${gifUrl}`);
        
        await apiCall('/api/chat/messages', 'POST', formData);
        await refreshMessages();
        loadConversations();
    } catch (error) {
        console.error('Error sending GIF:', error);
        alert('Failed to send GIF: ' + error.message);
    }
}

// Send message
async function sendMessage() {
    // Prevent double-send
    if (window._sendingMessage) return;
    window._sendingMessage = true;

    // If editing, save edit instead
    if (window._editingMessageId) {
        window._sendingMessage = false;
        return saveEditMessage();
    }

    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const message = input.value.trim();

    if (!message && fileInput.files.length === 0) {
        window._sendingMessage = false;
        return;
    }

    if (!window.currentConversationId) {
        window._sendingMessage = false;
        return;
    }

    const formData = new FormData();
    formData.append('conversation_id', window.currentConversationId);
    formData.append('message', message || '');
    if (window.replyToId) formData.append('reply_to_id', window.replyToId);
    if (fileInput.files.length > 0) {
        for (let file of fileInput.files) formData.append('attachments[]', file);
    }

    // ── Optimistic render: show bubble instantly ──────────────────────────────
    const optimisticId = 'opt-' + Date.now();
    const optimisticText = message;
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true, timeZone:'America/Los_Angeles' }) + ' PT';

    // Clear input immediately
    input.value = '';
    if (window._chatAutoResizeReset) window._chatAutoResizeReset();
    clearAttachmentPreview();
    cancelReply();

    const messagesEl = document.getElementById('chatMessages');
    if (messagesEl && message) {
        const typingRow = document.getElementById('typingIndicatorRow');
        if (typingRow) typingRow.remove();
        const placeholder = messagesEl.querySelector('.no-messages');
        if (placeholder) placeholder.remove();

        const tmp = document.createElement('div');
        tmp.innerHTML = `
            <div class="message-item message-sender" data-optimistic-id="${optimisticId}" style="position:relative;opacity:0.65;">
                <div class="message-content">
                    <div class="message-text">${formatMessageText(optimisticText)}</div>
                    <div class="message-time">${timeStr}<span class="msg-ticks"><i class="bx bx-time-five" style="font-size:.75rem;"></i></span></div>
                </div>
            </div>`;
        while (tmp.firstChild) messagesEl.appendChild(tmp.firstChild);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    try {
        const response = await apiCall('/api/chat/messages', 'POST', formData);

        if (response && response.message) {
            const msg = response.message;
            if (msg.id > (window.lastRenderedMessageId || 0)) {
                window.lastRenderedMessageId = msg.id;
            }

            // Update the optimistic element in-place (most reliable)
            const optEl = document.querySelector(`[data-optimistic-id="${optimisticId}"]`);
            if (optEl) {
                // Remove any copy the poll may have race-appended before we responded
                if (messagesEl) {
                    messagesEl.querySelectorAll(`[data-message-id="${msg.id}"]`).forEach(el => el.remove());
                }
                optEl.setAttribute('data-message-id', msg.id);
                optEl.removeAttribute('data-optimistic-id');
                optEl.style.opacity = '1';
                // Replace clock icon with single grey tick + data-ts for read receipt
                const timeEl = optEl.querySelector('.message-time');
                if (timeEl) {
                    timeEl.innerHTML = `${timeStr}<span class="msg-ticks"><i class="bx bx-check"></i></span><span data-ts="${msg.created_at || now.toISOString()}" style="display:none"></span>`;
                }
            } else if (messagesEl && !messagesEl.querySelector(`[data-message-id="${msg.id}"]`)) {
                // Optimistic bubble was wiped by a refresh poll — append real message
                const tmp = document.createElement('div');
                tmp.innerHTML = renderMessages([msg]);
                while (tmp.firstChild) messagesEl.appendChild(tmp.firstChild);
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }
        }
        updateConversationPreview(window.currentConversationId, optimisticText);
    } catch (error) {
        console.error('Error sending message:', error);
        const optEl = document.querySelector(`[data-optimistic-id="${optimisticId}"]`);
        if (optEl) optEl.remove();
        input.value = optimisticText;
        if (window._chatAutoResizeFunc) window._chatAutoResizeFunc();
        alert('Failed to send. Please try again.');
    } finally {
        window._sendingMessage = false;
    }
}

// Update a conversation's preview text and move it to top without full sidebar reload
function updateConversationPreview(conversationId, messageText) {
    const listEl = document.getElementById('conversationsList');
    if (!listEl) return;

    const items = listEl.querySelectorAll('.conversation-item');
    let targetItem = null;

    items.forEach(item => {
        // Find the item whose onclick contains this conversation ID
        const onclickAttr = item.getAttribute('onclick') || '';
        if (onclickAttr.includes(`selectConversation(${conversationId}`)) {
            targetItem = item;
        }
    });

    if (targetItem) {
        // Update preview text
        const preview = targetItem.querySelector('.conversation-preview');
        if (preview) {
            preview.textContent = (messageText || '📎 Attachment').substring(0, 40) + '...';
        }
        // Update time
        const time = targetItem.querySelector('.conversation-time');
        if (time) {
            time.textContent = 'Just now';
        }
        // Move to top (after the section label if present)
        const sectionLabel = listEl.querySelector('.sidebar-section-label');
        if (sectionLabel && sectionLabel.nextSibling) {
            listEl.insertBefore(targetItem, sectionLabel.nextSibling);
        } else {
            listEl.prepend(targetItem);
        }
    }
}

// Handle file select with preview
function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    const previewArea = document.getElementById('attachmentPreview');
    
    if (files.length > 0) {
        const messageInput = document.getElementById('messageInput');
        
        // Validate file sizes (10MB limit)
        const oversizedFiles = files.filter(file => file.size > 10 * 1024 * 1024);
        if (oversizedFiles.length > 0) {
            alert(`Some files are too large (max 10MB): ${oversizedFiles.map(f => f.name).join(', ')}`);
            e.target.value = '';
            clearAttachmentPreview();
            return;
        }
        
        // Build preview HTML
        let previewHtml = '<div class="preview-container">';
        files.forEach((file, idx) => {
            const sizeKB = Math.round(file.size / 1024);
            const sizeDisplay = sizeKB > 1024 ? `${Math.round(sizeKB/1024)}MB` : `${sizeKB}KB`;
            
            if (file.type.startsWith('image/')) {
                // Image preview
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const imgEl = document.getElementById(`preview-img-${idx}`);
                    if (imgEl) imgEl.src = ev.target.result;
                };
                reader.readAsDataURL(file);
                previewHtml += `
                    <div class="preview-item image-preview">
                        <img id="preview-img-${idx}" src="" alt="${file.name}" style="width:52px;height:52px;object-fit:contain;border-radius:4px;background:rgba(0,0,0,.05);">
                        <button type="button" class="preview-remove" onclick="removePreviewItem(${idx})" title="Remove"><i class="bx bx-x"></i></button>
                        <span class="preview-name">${file.name.substring(0, 20)}${file.name.length > 20 ? '...' : ''}</span>
                    </div>`;
            } else {
                // File icon preview
                let icon = 'bx-file';
                if (file.type.startsWith('video/')) icon = 'bx-video';
                else if (file.type.startsWith('audio/')) icon = 'bx-volume-full';
                else if (file.type.includes('pdf')) icon = 'bx-file-pdf';
                else if (file.type.includes('word') || file.name.match(/\.docx?$/)) icon = 'bx-file-doc';
                else if (file.type.includes('zip') || file.type.includes('rar')) icon = 'bx-archive';
                
                previewHtml += `
                    <div class="preview-item file-preview">
                        <i class="bx ${icon}"></i>
                        <button type="button" class="preview-remove" onclick="removePreviewItem(${idx})" title="Remove"><i class="bx bx-x"></i></button>
                        <span class="preview-name">${file.name.substring(0, 15)}${file.name.length > 15 ? '...' : ''}</span>
                        <span class="preview-size">${sizeDisplay}</span>
                    </div>`;
            }
        });
        previewHtml += '<button type="button" class="preview-clear-all" onclick="clearAttachmentPreview()" title="Remove attachment"><i class="bx bx-x"></i></button></div>';
        
        previewArea.innerHTML = previewHtml;
        previewArea.style.display = 'block';
        messageInput.focus();
        
        console.log('Files selected:', files.map(f => ({ name: f.name, type: f.type, size: f.size })));
    } else {
        clearAttachmentPreview();
    }
}

// Clear attachment preview
function clearAttachmentPreview() {
    const previewArea = document.getElementById('attachmentPreview');
    const fileInput = document.getElementById('fileInput');
    if (previewArea) {
        previewArea.innerHTML = '';
        previewArea.style.display = 'none';
    }
    if (fileInput) fileInput.value = '';
}

// Remove individual preview item (clears all for now since FileList is immutable)
function removePreviewItem(idx) {
    clearAttachmentPreview();
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
            window.currentConversationId = data.conversation_id;
            
            // Reload conversations to update sidebar
            loadConversations();
        }
    } catch (error) {
        console.error('Error starting chat:', error);
    }
}

// ─── Master Search (API-powered) ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchConversations');
    const searchResultsEl = document.getElementById('chatSearchResults');
    let searchTimer = null;

    if (searchInput && searchResultsEl) {
        searchInput.addEventListener('input', (e) => {
            const q = e.target.value.trim();
            clearTimeout(searchTimer);
            if (!q) {
                searchResultsEl.style.display = 'none';
                return;
            }
            if (q.length < 2) return;
            searchTimer = setTimeout(async () => {
                try {
                    const data = await apiCall(`/api/chat/search?query=${encodeURIComponent(q)}`);
                    renderSearchResults(data, q);
                } catch (err) { console.error('Search error:', err); }
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.closest('.chat-search-box').contains(e.target)) {
                searchResultsEl.style.display = 'none';
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchResultsEl.style.display = 'none';
                searchInput.value = '';
            }
        });
    }

    function renderSearchResults(data, q) {
        if (!searchResultsEl) return;
        const convs = data.conversations || [];
        const msgs  = data.messages || [];
        if (!convs.length && !msgs.length) {
            searchResultsEl.innerHTML = `<div class="search-no-results"><i class="bx bx-search"></i> No results for "${q.replace(/</g,'&lt;')}"</div>`;
            searchResultsEl.style.display = 'block';
            return;
        }
        let html = '';
        if (convs.length) {
            html += '<div class="search-result-section">Conversations</div>';
            html += convs.map(c => {
                const name = (c.name || 'Direct Message').replace(/</g,'&lt;');
                return `<div class="search-result-item" onclick="selectConversation(${c.id},'${name.replace(/'/g,"\\'")  }',null);document.getElementById('chatSearchResults').style.display='none';document.getElementById('searchConversations').value=''">
                    <div class="search-result-avatar">${name.charAt(0).toUpperCase()}</div>
                    <div class="search-result-info">
                        <div class="search-result-name">${name}</div>
                        ${c.latest_message ? `<div class="search-result-preview">${(c.latest_message.message||'').replace(/</g,'&lt;').substring(0,60)}</div>` : ''}
                    </div>
                </div>`;
            }).join('');
        }
        if (msgs.length) {
            html += '<div class="search-result-section">Messages</div>';
            html += msgs.map(m => {
                const sender   = (m.user?.name   || 'Unknown').replace(/</g,'&lt;');
                const convName = (m.conversation?.name || 'Direct Message').replace(/</g,'&lt;');
                const preview  = (m.message || '').replace(/</g,'&lt;').substring(0, 80);
                return `<div class="search-result-item" onclick="searchJumpToMessage(${m.conversation_id},'${convName.replace(/'/g,"\\'")}',${m.id})">
                    <div class="search-result-avatar">${sender.charAt(0).toUpperCase()}</div>
                    <div class="search-result-info">
                        <div class="search-result-name">${sender} <span style="font-weight:400;font-size:.65rem;color:var(--bs-surface-400)">in ${convName}</span></div>
                        <div class="search-result-preview">${preview}</div>
                    </div>
                </div>`;
            }).join('');
        }
        searchResultsEl.innerHTML = html;
        searchResultsEl.style.display = 'block';
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

// Initial load is handled in DOMContentLoaded listener above (line ~426)
// Removed duplicate call here to prevent refresh loop

// Replace 60s conversation list poll with WebSocket push
// ConversationUpdated fires on user.{id} channel whenever a recipient gets a new message
initEcho().then(echo => {
    if (!echo) return;
    try {
        echo.private('user.{{ Auth::id() }}').listen('.conversation.updated', function (e) {
            // Only refresh if we're not already viewing that conversation
            if (window.currentConversationId && String(e.conversation_id) === String(window.currentConversationId)) return;
            loadConversations();
        });
    } catch (err) {
        console.warn('[Echo] ConversationUpdated listener failed:', err);
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(window.messagesRefreshInterval);
    clearInterval(window._typingPollInterval);
});

// -------------------------
// Laravel Echo + Reverb real-time setup
// -------------------------

if (typeof echoConfig === 'undefined') {
    var echoConfig = {!! json_encode([
        'key'      => env('REVERB_APP_KEY', ''),
        'host'     => env('REVERB_HOST', '127.0.0.1'),
        'port'     => intval(env('REVERB_PORT', 8080)),
        'scheme'   => env('REVERB_SCHEME', 'http'),
        'forceTLS' => env('REVERB_SCHEME', 'http') === 'https',
    ]) !!};
}

if (typeof echoInstance === 'undefined') { var echoInstance = null; }
if (typeof subscribedChannel === 'undefined') { var subscribedChannel = null; }
// Stored channel object — reused for all whispers so we never re-subscribe
if (typeof window._echoChannel === 'undefined') { window._echoChannel = null; }

// Returns a Promise that resolves when Echo is fully initialised.
// Calling initEcho() multiple times is safe — returns the same promise.
var _echoInitPromise = null;
function initEcho() {
    if (_echoInitPromise) return _echoInitPromise;
    if (!echoConfig.key) return Promise.resolve(null);

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) { resolve(); return; }
            const s = document.createElement('script');
            s.src = src; s.onload = resolve; s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    _echoInitPromise = loadScript('https://js.pusher.com/8.4.0/pusher.min.js')
        .then(() => loadScript('https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js'))
        .then(() => {
            if (echoInstance) return echoInstance;
            echoInstance = new Echo({
                broadcaster:        'reverb',
                key:                echoConfig.key,
                wsHost:             echoConfig.host,
                wsPort:             echoConfig.port,
                wssPort:            echoConfig.port,
                forceTLS:           echoConfig.forceTLS,
                enabledTransports:  ['ws', 'wss'],
                authEndpoint:       '/broadcasting/auth',
                auth: { headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                }},
            });
            console.log('[Echo] Initialized');
            return echoInstance;
        })
        .catch(err => { console.warn('[Echo] Init failed:', err); return null; });

    return _echoInitPromise;
}

function subscribeToConversation(conversationId) {
    // Unsubscribe previous channel immediately (sync, best-effort)
    if (subscribedChannel && echoInstance) {
        try { echoInstance.leave(`chat.conversation.${subscribedChannel}`); } catch(e) {}
    }
    window._echoChannel = null;
    subscribedChannel = conversationId;

    initEcho().then(echo => {
        if (!echo) { console.warn('[Echo] Not available, real-time disabled'); return; }
        if (subscribedChannel !== conversationId) return; // conversation changed while waiting

        try {
            // Store channel object so whispers can reuse it without re-subscribing
            window._echoChannel = echo.private(`chat.conversation.${conversationId}`)
                .subscribed(() => console.log(`[Echo] Subscribed to chat.conversation.${conversationId}`))
                .error(err  => console.error(`[Echo] Auth error for chat.conversation.${conversationId}:`, err))

                .listen('.message.sent', (e) => {
                    if (conversationId !== window.currentConversationId) return;
                    const newId = e.id;
                    if (!newId) return;

                    const messagesEl = document.getElementById('chatMessages');
                    if (!messagesEl) return;

                    // Always skip if already in DOM (covers optimistic & poll duplicates)
                    if (messagesEl.querySelector(`[data-message-id="${newId}"]`)) {
                        window.lastRenderedMessageId = Math.max(window.lastRenderedMessageId || 0, newId);
                        return;
                    }

                    // Own message still in-flight as optimistic bubble — let sendMessage handle
                    if (window._sendingMessage && e.user_id === window.currentUserId) {
                        window.lastRenderedMessageId = Math.max(window.lastRenderedMessageId || 0, newId);
                        return;
                    }

                    // Append new message from other user (no animation flash)
                    const placeholder = messagesEl.querySelector('.no-messages');
                    if (placeholder) placeholder.remove();
                    const tmp = document.createElement('div');
                    tmp.innerHTML = renderMessages([e]);
                    const atBottom = messagesEl.scrollHeight - messagesEl.scrollTop <= messagesEl.clientHeight + 60;
                    // Suppress entry animation for Echo-delivered messages
                    tmp.querySelectorAll('.message-item').forEach(el => el.style.animation = 'none');
                    while (tmp.firstChild) messagesEl.appendChild(tmp.firstChild);
                    if (atBottom) messagesEl.scrollTop = messagesEl.scrollHeight;
                    window.lastRenderedMessageId = Math.max(window.lastRenderedMessageId || 0, newId);

                    // Remove typing indicator if visible
                    const tr = document.getElementById('typingIndicatorRow');
                    if (tr) tr.remove();

                    // Play sound if window not focused
                    if (!document.hasFocus() && window.ChatNotify?.playSound) window.ChatNotify.playSound();
                })

                .listen('.message.read', (e) => {
                    // Other user read the conversation — update all our sent ticks to double-blue
                    if (conversationId !== window.currentConversationId) return;
                    const readAt = new Date(e.read_at);
                    const messagesEl = document.getElementById('chatMessages');
                    if (!messagesEl) return;
                    messagesEl.querySelectorAll('.message-item.message-sender[data-message-id]').forEach(el => {
                        const tickEl = el.querySelector('.msg-ticks');
                        if (!tickEl || tickEl.classList.contains('ticks-read')) return;
                        const tsEl = el.querySelector('[data-ts]');
                        if (tsEl && new Date(tsEl.getAttribute('data-ts')) <= readAt) {
                            tickEl.classList.add('ticks-read');
                            tickEl.innerHTML = '<i class="bx bx-check-double"></i>';
                        }
                    });
                })

                .listenForWhisper('message_action', (e) => {
                    if (conversationId !== window.currentConversationId) return;
                    if (e.type === 'delete') {
                        const el = document.querySelector(`.message-item[data-message-id="${e.id}"]`);
                        if (el) el.remove();
                    } else if (e.type === 'edit' && e.text) {
                        const msgEl = document.querySelector(`.message-item[data-message-id="${e.id}"]`);
                        if (msgEl) {
                            const textEl = msgEl.querySelector('.message-text');
                            if (textEl) textEl.innerHTML = formatMessageText(e.text);
                            if (!msgEl.querySelector('.message-edited')) {
                                const timeEl = msgEl.querySelector('.message-time');
                                if (timeEl) timeEl.insertAdjacentHTML('beforebegin', '<span class="message-edited" style="font-size:.6rem;color:#94a3b8;font-style:italic;">(edited)</span>');
                            }
                        }
                    }
                });
        } catch (e) {
            console.warn('[Echo] Subscribe failed:', e);
        }
    });
}

// Subscribe to community announcement channels
let subscribedCommunityChannels = [];

function subscribeToCommunityAnnouncements(communityIdOrArray) {
    let communityIds = [];
    if (typeof communityIdOrArray === 'number') {
        communityIds = [communityIdOrArray];
    } else if (Array.isArray(communityIdOrArray)) {
        communityIds = communityIdOrArray.map(c => c.id).filter(id => id);
    } else {
        return;
    }
    if (communityIds.length === 0) return;

    initEcho().then(echo => {
        if (!echo) return;

        try {
            // Subscribe to each community's announcement channel
            communityIds.forEach(communityId => {
                // Skip if already subscribed
                if (subscribedCommunityChannels.includes(communityId)) return;
                
                try {
                    echoInstance.private(`community.${communityId}`)
                        .listen('.announcement.posted', async (data) => {
                            console.log('Community announcement received:', data);
                            
                            // Show pop-up notification
                            showAnnouncementPopup(data);
                            
                            // Refresh announcements if viewing this community
                            if (window.currentCommunityId === communityId) {
                                try {
                                    const announcementsData = await apiCall(`/api/chat/communities/${communityId}/announcements`);
                                    const container = document.getElementById('announcementsContainer');
                                    const community = globalCommunities.find(c => c.id === communityId);
                                    
                                    if (container && community && announcementsData.announcements) {
                        container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || '#d4af37');
                                        // Scroll to bottom
                                        const messagesDiv = document.getElementById('announcementMessages');
                                        if (messagesDiv) {
                                            messagesDiv.scrollTop = messagesDiv.scrollHeight;
                                        }
                                    }
                                } catch (e) {
                                    console.error('Failed to refresh announcements:', e);
                                }
                            }
                            
                            // Update communities list
                            loadCommunitiesForDisplay();
                        });
                    
                    subscribedCommunityChannels.push(communityId);
                    console.log(`Subscribed to community ${communityId} announcements`);
                } catch (e) {
                    console.warn(`Failed to subscribe to community ${communityId} announcements:`, e);
                }
            });
        } catch (e) {
            console.warn('Failed to subscribe to community announcement channels', e);
        }
    }, 200);
}

// ===== COMMUNITY ANNOUNCEMENT POP-UP NOTIFICATIONS =====

// Store recent announcements in session storage
function getRecentAnnouncements() {
    try {
        const stored = sessionStorage.getItem('recentAnnouncements');
        return stored ? JSON.parse(stored) : [];
    } catch (e) {
        return [];
    }
}

function saveRecentAnnouncement(announcement) {
    try {
        let recent = getRecentAnnouncements();
        // Add new announcement at the beginning
        recent.unshift(announcement);
        // Keep only last 5 announcements
        recent = recent.slice(0, 5);
        sessionStorage.setItem('recentAnnouncements', JSON.stringify(recent));
        
        // Update floating button badge
        updateFloatingButtonBadge();
    } catch (e) {
        console.error('Error saving recent announcement:', e);
    }
}

function updateFloatingButtonBadge() {
    const recent = getRecentAnnouncements();
    const floatBtn = document.getElementById('announcementFloatBtn');
    const badge = document.getElementById('announcementFloatBadge');
    
    if (recent.length > 0) {
        floatBtn.classList.add('show');
        badge.style.display = 'flex';
        badge.textContent = recent.length;
    } else {
        floatBtn.classList.remove('show');
        badge.style.display = 'none';
    }
}

// Show announcement pop-up notification
let currentPopupTimeout = null;
function showAnnouncementPopup(data) {
    try {
        const popup = document.getElementById('announcementPopup');
        if (!popup) return;
        
        // Clear existing timeout
        if (currentPopupTimeout) {
            clearTimeout(currentPopupTimeout);
        }
        
        // Extract announcement data
        const announcement = data.announcement || data;
        const communityName = data.community_name || 'Community';
        const communityId = data.community_id;
        
        // Priority colors and icons
        const priorityData = {
            'urgent': { color: '#f46a6a', icon: 'bx-error-circle', label: 'URGENT' },
            'warning': { color: '#f1b44c', icon: 'bx-error', label: 'Warning' },
            'info': { color: '#50a5f1', icon: 'bx-info-circle', label: 'Info' },
            'normal': { color: '#6c757d', icon: 'bx-info-circle', label: 'Normal' }
        };
        
        const priority = announcement.priority || 'normal';
        const priorityInfo = priorityData[priority];
        
        // Find community color
        const community = globalCommunities.find(c => c.id === communityId);
        const communityColor = community?.color || '#d4af37';
        
        // Update popup content
        document.getElementById('popupCommunityName').textContent = communityName;
        document.getElementById('popupCommunityIcon').style.background = communityColor;
        
        const priorityBadge = document.getElementById('popupPriorityBadge');
        priorityBadge.style.background = priorityInfo.color;
        priorityBadge.style.color = 'white';
        priorityBadge.innerHTML = `<i class="bx ${priorityInfo.icon}"></i><span>${priorityInfo.label}</span>`;
        
        const titleEl = document.getElementById('popupAnnouncementTitle');
        if (announcement.title) {
            titleEl.textContent = announcement.title;
            titleEl.style.display = 'block';
        } else {
            titleEl.style.display = 'none';
        }
        
        document.getElementById('popupAnnouncementMessage').textContent = announcement.message;
        document.getElementById('popupAnnouncementTime').textContent = 'Just now';
        
        // Save to recent announcements
        saveRecentAnnouncement({
            ...announcement,
            community_name: communityName,
            community_id: communityId,
            community_color: communityColor,
            timestamp: Date.now()
        });
        
        // Show popup with animation
        popup.classList.remove('hiding');
        popup.classList.add('show');
        
        // Play sound notification if available
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjqP1fPPgjMGKHi+7+OZURE=');
            audio.volume = 0.3;
            audio.play().catch(() => {});
        } catch (e) {}
        
        // Auto-dismiss after 20 seconds
        currentPopupTimeout = setTimeout(() => {
            closeAnnouncementPopup();
        }, 20000);
        
    } catch (error) {
        console.error('Error showing announcement popup:', error);
    }
}

// Close announcement pop-up
function closeAnnouncementPopup() {
    const popup = document.getElementById('announcementPopup');
    if (!popup) return;
    
    popup.classList.add('hiding');
    
    setTimeout(() => {
        popup.classList.remove('show', 'hiding');
    }, 300);
    
    if (currentPopupTimeout) {
        clearTimeout(currentPopupTimeout);
        currentPopupTimeout = null;
    }
}

// Show recent announcements when floating button is clicked
function showRecentAnnouncements() {
    const recent = getRecentAnnouncements();
    
    if (recent.length === 0) {
        return;
    }
    
    // Show the most recent announcement
    const latest = recent[0];
    
    // Re-show the popup with the latest announcement
    const popup = document.getElementById('announcementPopup');
    if (!popup) return;
    
    const priorityData = {
        'urgent': { color: '#f46a6a', icon: 'bx-error-circle', label: 'URGENT' },
        'warning': { color: '#f1b44c', icon: 'bx-error', label: 'Warning' },
        'info': { color: '#50a5f1', icon: 'bx-info-circle', label: 'Info' },
        'normal': { color: '#6c757d', icon: 'bx-info-circle', label: 'Normal' }
    };
    
    const priority = latest.priority || 'normal';
    const priorityInfo = priorityData[priority];
    
    document.getElementById('popupCommunityName').textContent = latest.community_name || 'Community';
    document.getElementById('popupCommunityIcon').style.background = latest.community_color || '#d4af37';
    
    const priorityBadge = document.getElementById('popupPriorityBadge');
    priorityBadge.style.background = priorityInfo.color;
    priorityBadge.style.color = 'white';
    priorityBadge.innerHTML = `<i class="bx ${priorityInfo.icon}"></i><span>${priorityInfo.label}</span>`;
    
    const titleEl = document.getElementById('popupAnnouncementTitle');
    if (latest.title) {
        titleEl.textContent = latest.title;
        titleEl.style.display = 'block';
    } else {
        titleEl.style.display = 'none';
    }
    
    document.getElementById('popupAnnouncementMessage').textContent = latest.message;
    
    // Show relative time
    const timestamp = latest.timestamp || Date.now();
    const now = Date.now();
    const diffMinutes = Math.floor((now - timestamp) / 60000);
    let timeText = 'Just now';
    if (diffMinutes >= 60) {
        const hours = Math.floor(diffMinutes / 60);
        timeText = `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (diffMinutes > 0) {
        timeText = `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''} ago`;
    }
    document.getElementById('popupAnnouncementTime').textContent = timeText;
    
    // Show popup
    popup.classList.remove('hiding');
    popup.classList.add('show');
    
    // Clear badge after showing
    setTimeout(() => {
        sessionStorage.removeItem('recentAnnouncements');
        updateFloatingButtonBadge();
    }, 1000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update floating button on page load
    updateFloatingButtonBadge();
});

// Group chat functionality
let selectedMembers = new Set();

document.addEventListener('DOMContentLoaded', function() {
    // Create chat button - only for group creation now
    document.getElementById('createChatBtn').addEventListener('click', function() {
        createGroupChat();
    });

    // Modal shown event
    document.getElementById('newChatModal').addEventListener('shown.bs.modal', function() {
        loadUsersForGroup();
        selectedMembers.clear();
        updateSelectedMembersList();
    });

    // Add search functionality for group members
    const searchGroupUsersInput = document.getElementById('searchGroupUsers');
    if (searchGroupUsersInput) {
        searchGroupUsersInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const userItems = document.querySelectorAll('#groupUsersList .user-item');
            
            userItems.forEach(item => {
                const userName = item.dataset.userName?.toLowerCase() || '';
                const userEmail = item.querySelector('.user-email')?.textContent.toLowerCase() || '';
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
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

// Load communities for group chat
async function loadCommunitiesForGroupChat() {
    try {
        const data = await apiCall('/api/chat/communities');
        const select = document.getElementById('groupCommunity');
        
        // Keep the default option
        const defaultOption = select.querySelector('option');
        select.innerHTML = '';
        select.appendChild(defaultOption);
        
        // Add communities
        if (data.communities && data.communities.length > 0) {
            data.communities.forEach(community => {
                const option = document.createElement('option');
                option.value = community.id;
                option.textContent = community.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading communities:', error);
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
            <div class="user-avatar">
                ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">` : user.name.charAt(0).toUpperCase()}
            </div>
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
        // Use FormData to handle file upload
        const formData = new FormData();
        formData.append('name', groupName);
        
        // Add color
        const colorInput = document.getElementById('communityColor');
        if (colorInput) {
            formData.append('color', colorInput.value);
        }
        
        // Add avatar file if selected
        const avatarInput = document.getElementById('groupAvatar');
        if (avatarInput && avatarInput.files.length > 0) {
            formData.append('avatar', avatarInput.files[0]);
        }
        
        // Add user IDs
        selectedMembers.forEach(userId => {
            formData.append('user_ids[]', userId);
        });
        
        const response = await fetch('/api/chat/groups', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content
            },
            body: formData
        });
        
        console.log('Group creation response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Group creation failed:', errorText);
            throw new Error('Failed to create group: ' + response.status);
        }
        
        const data = await response.json();
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();
        
        // Load the new group conversation
        await loadMessages(data.conversation_id, groupName);
        window.currentConversationId = data.conversation_id;
        
        // Refresh conversations list and groups list
        loadConversations();
        loadGroupsForDisplay(); // Reload groups to show new group with avatar
        
        // Clear form
        document.getElementById('groupName').value = '';
        if (avatarInput) avatarInput.value = '';
        document.getElementById('groupAvatarPreview').innerHTML = '<i class="bx bx-group" style="font-size: 36px;"></i>';
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
        
        // Set group avatar preview
        const avatarPreview = document.getElementById('groupAvatarPreview');
        if (currentGroupData.avatar) {
            avatarPreview.innerHTML = `<img src="${currentGroupData.avatar}" style="width: 100%; height: 100%; object-fit: cover;" />`;
        } else {
            avatarPreview.innerHTML = '<i class="bx bx-group" style="font-size: 24px;"></i>';
        }
        
        // Show/hide delete button based on permissions
        const deleteBtn = document.getElementById('deleteGroupBtn');
        if (window.currentUserId && parseInt(currentGroupData.created_by) === parseInt(window.currentUserId)) {
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
                <div class="member-avatar me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; overflow: hidden;">
                    ${member.avatar ? `<img src="${member.avatar}" alt="${member.name}" style="width: 100%; height: 100%; object-fit: cover;">` : member.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div class="member-name fw-bold">${member.name}</div>
                    <small class="text-muted">${member.email}</small>
                </div>
            </div>
            <div class="member-actions">
                ${parseInt(member.id) === parseInt(currentGroupData.created_by) ? '<span class="badge bg-primary">Creator</span>' : ''}
                ${window.currentUserId && parseInt(member.id) !== parseInt(window.currentUserId) && parseInt(member.id) !== parseInt(currentGroupData.created_by) && parseInt(currentGroupData.created_by) === parseInt(window.currentUserId) ? 
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
                <div class="user-avatar me-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; overflow: hidden;">
                    ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}" style="width: 100%; height: 100%; object-fit: cover;">` : user.name.charAt(0).toUpperCase()}
                </div>
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
    
    if (!currentGroupData || typeof window.currentUserId === 'undefined') {
        alert('Unable to update group. Please refresh the page.');
        console.error('Missing data:', { currentGroupData, currentUserId: window.currentUserId });
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(window.currentUserId)) {
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
        if (window.currentConversationId === currentGroupData.id) {
            loadMessages(window.currentConversationId, newName);
        }
        
    } catch (error) {
        console.error('Error updating group name:', error);
        alert('Failed to update group name: ' + error.message);
    }
}

async function updateGroupAvatar() {
    const fileInput = document.getElementById('groupAvatar');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a picture first');
        return;
    }
    
    if (!currentGroupData || typeof window.currentUserId === 'undefined') {
        alert('Unable to update group. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(window.currentUserId)) {
        alert('Only the group creator can change the group picture');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('avatar', file);
        
        const response = await fetch(`/api/chat/conversations/${currentGroupData.id}/avatar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content
            },
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Failed to upload avatar');
        }
        
        const data = await response.json();
        
        if (data.success) {
            alert('Group picture updated successfully');
            
            // Update the preview in the modal
            const avatarPreview = document.getElementById('groupAvatarPreview');
            if (data.avatar) {
                avatarPreview.innerHTML = `<img src="${data.avatar}" style="width: 100%; height: 100%; object-fit: cover;" />`;
                currentGroupData.avatar = data.avatar; // Update current group data
            }
            
            // Refresh conversation list and groups list
            loadConversations();
            loadGroupsForDisplay();
            
            // Update preview if this is the current conversation
            if (window.currentConversationId === currentGroupData.id) {
                loadMessages(window.currentConversationId, currentGroupData.name);
            }
        } else {
            throw new Error(data.message || 'Failed to update picture');
        }
        
    } catch (error) {
        console.error('Error updating group avatar:', error);
        alert('Failed to update group picture: ' + error.message);
    }
}

async function addMemberToGroup(userId) {
    if (!currentGroupData || !window.currentUserId) {
        alert('Unable to add member. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(window.currentUserId)) {
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
    if (!currentGroupData || !window.currentUserId) {
        alert('Unable to remove member. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(window.currentUserId)) {
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
    if (!currentGroupData || !window.currentUserId) {
        alert('Unable to delete group. Please refresh the page.');
        return;
    }
    
    if (parseInt(currentGroupData.created_by) !== parseInt(window.currentUserId)) {
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
        if (window.currentConversationId === currentGroupData.id) {
            window.currentConversationId = null;
            document.getElementById('chatMain').innerHTML = '<div class="no-conversation"><div class="text-center p-4"><i class="bx bx-chat" style="font-size: 3rem; color: var(--bs-surface-300);"></i><p class="text-muted mt-2">Select a conversation to start chatting</p></div></div>';
        }
        
        // Refresh both conversations and groups lists
        loadConversations();
        loadGroupsForDisplay();
        
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

// ===============================
// CHAT ENHANCEMENTS
// ===============================
// Note: Chat search is handled in the DOMContentLoaded event listener above

// Add emoji button when message input is rendered
const originalLoadMessages = loadMessages;
loadMessages = async function(conversationId, conversationName) {
    await originalLoadMessages(conversationId, conversationName);
    
    // Add emoji button if not already exists
    setTimeout(() => {
        const actionsDiv = document.querySelector('.message-input-actions');
        const attachBtn = document.getElementById('attachBtn');
        
        if (actionsDiv && attachBtn && !document.getElementById('emojiPickerBtn')) {
            const emojiBtn = document.createElement('button');
            emojiBtn.id = 'emojiPickerBtn';
            emojiBtn.type = 'button';
            emojiBtn.title = 'Add emoji';
            emojiBtn.innerHTML = '<i class="bx bx-smile"></i>';
            emojiBtn.onclick = () => {
                const input = document.getElementById('messageInput');
                if (input && window.EmojiPicker) {
                    window.EmojiPicker.show(input);
                }
            };
            
            // Insert between attach and send buttons
            actionsDiv.insertBefore(emojiBtn, attachBtn.nextSibling);
        }
    }, 100);
};

// Chat page: global chat-notifications.js handles desktop notifications via polling.
// No need to duplicate notification logic here. Just keep ChatToast as a no-op
// to prevent errors if anything calls it.
window.ChatToast = {
    show: function() {},
    dismiss: function() {},
    reply: function(toastId, conversationId, conversationName) {
        if (typeof loadMessages === 'function') {
            loadMessages(conversationId, conversationName);
            window.currentConversationId = conversationId;
        }
        document.getElementById('messageInput')?.focus();
    }
};

// Message input action button styles are in chat.css

// ==========================================
// MENTION AUTOCOMPLETE
// ==========================================

    // Get users in current conversation (or community members for community channels)
    async function loadConversationUsers() {
        if (!window.currentConversationId && !window.currentCommunityId) {
            console.warn('No conversation or community ID set for mention loading');
            return;
        }
        
        try {
            if (window.currentConversationId) {
                // Try the dedicated users endpoint first
                try {
                    const response = await apiCall(`/api/chat/conversations/${window.currentConversationId}/users`);
                    console.log('Users endpoint response:', response);
                    window.conversationUsers = response.users || [];
                } catch (e1) {
                    console.warn('Users endpoint failed, trying conversation details:', e1);
                    // Fallback: get users from conversation details endpoint
                    try {
                        const response = await apiCall(`/api/chat/conversations/${window.currentConversationId}`);
                        console.log('Conversation details response:', response);
                        window.conversationUsers = response.users || [];
                    } catch (e2) {
                        console.warn('Conversation details also failed:', e2);
                    }
                }
                
                // Final fallback: if still empty, load all chat users
                if (!window.conversationUsers || window.conversationUsers.length === 0) {
                    console.warn('No users found from conversation endpoints, loading all chat users as fallback');
                    try {
                        const allUsersResponse = await apiCall('/api/chat/users');
                        window.conversationUsers = (allUsersResponse.users || []).map(u => ({
                            id: u.id,
                            name: u.name,
                            avatar: u.avatar,
                            role: u.role || 'Member'
                        }));
                    } catch (e3) {
                        console.error('All user loading methods failed:', e3);
                    }
                }
            } else if (window.currentCommunityId) {
                const response = await apiCall(`/api/communities/${window.currentCommunityId}/members`);
                window.conversationUsers = (response.members || []).map(m => ({
                    id: m.id,
                    name: m.name,
                    avatar: m.avatar,
                    role: m.role || 'Member'
                }));
            }
            console.log('Final conversation users for mentions:', window.conversationUsers?.length, window.conversationUsers);
        } catch (e) {
            console.error('Failed to load conversation users:', e);
        }
    }

    // Load users for mention autocomplete (event delegation handles the actual input/keydown)
    async function loadMentionUsers() {
        if (!window.conversationUsers || window.conversationUsers.length === 0) {
            await loadConversationUsers();
        } else {
            console.log('Using existing conversation users for mentions:', window.conversationUsers.length);
        }
    }

    // Get mention suggestions
    function getMentionSuggestions(query) {
        const suggestions = [];
        const lowerQuery = (query || '').toLowerCase();
        
        // Add @everyone option (show always when query is empty, or when matches)
        if (!lowerQuery || 'everyone'.startsWith(lowerQuery)) {
            suggestions.push({
                name: 'everyone',
                type: 'special',
                icon: '👥'
            });
        }
        
        // Add users - match by contains (not just startsWith) for better findability
        (window.conversationUsers || []).forEach(user => {
            const userName = (user.name || '').toLowerCase();
            if (!lowerQuery || userName.includes(lowerQuery)) {
                suggestions.push({
                    name: user.name,
                    type: 'user',
                    avatar: user.avatar,
                    role: user.role
                });
            }
        });
        
        return suggestions.slice(0, 10); // Limit to 10 suggestions
    }

    // Show mention suggestions
    function showMentionSuggestions(suggestions, containerId, inputId) {
        containerId = containerId || 'mentionSuggestions';
        inputId = inputId || 'messageInput';
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (suggestions.length === 0) {
            container.style.display = 'none';
            return;
        }
        
        container.innerHTML = suggestions.map((s, i) => `
            <div class="suggestion-item ${i === 0 ? 'active' : ''}" data-index="${i}" data-mention="${s.name}">
                ${s.type === 'special' ?
                    `<span>${s.icon}</span><div class="user-name">@${s.name}</div>` :
                    `<img src="${s.avatar || ''}" alt="${s.name}" width="26" height="26" onerror="this.style.display='none'">
                     <div>
                        <div class="user-name">@${s.name}</div>
                        <div class="user-role">${s.role || 'Member'}</div>
                     </div>`
                }
            </div>
        `).join('');
        
        container.style.display = 'block';
        window.selectedSuggestionIndex = 0;
        
        // Add click handlers
        container.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', () => selectMention(item.dataset.mention, inputId));
        });
    }

    // Handle mention selection
    function selectMention(name, inputId) {
        const input = document.getElementById(inputId || 'messageInput');
        if (!input) return;
        const text = input.value;
        const cursorPos = input.selectionStart;
        
        // Find the @ position
        let atPos = text.lastIndexOf('@', cursorPos - 1);
        if (atPos === -1) return;
        
        // Use @[Name] format for multi-word names, @name for single words
        const hasSpace = name.includes(' ');
        const mentionText = hasSpace ? `@[${name}]` : `@${name}`;
        
        // Replace from @ to cursor
        const before = text.substring(0, atPos);
        const after = text.substring(cursorPos);
        const newText = before + mentionText + ' ' + after;
        
        input.value = newText;
        input.focus();
        input.setSelectionRange(newText.length - after.length, newText.length - after.length);
        
        // Hide suggestions
        const suggestionsEl = document.getElementById(inputId === 'announcementInput' ? 'announcementMentionSuggestions' : 'mentionSuggestions');
        if (suggestionsEl) suggestionsEl.style.display = 'none';
    }

    function updateActiveSuggestion(items) {
        items.forEach((item, i) => {
            item.classList.toggle('active', i === window.selectedSuggestionIndex);
        });
    }

    // Load communities
    async function loadCommunities() {
        try {
            const response = await apiCall('/api/chat/communities');
            if (response && response.length > 0) {
                renderCommunities(response);
            }
        } catch (error) {
            console.log('Error loading communities:', error);
        }
    }

    // Render communities list
    function renderCommunities(communities) {
        const listEl = document.getElementById('communitiesList');
        
        if (communities.length === 0) {
            listEl.innerHTML = `<div style="padding: 12px; text-align: center; color: var(--bs-surface-400, #adb5bd); font-size: 0.85rem;">No communities</div>`;
            return;
        }

        listEl.innerHTML = communities.map(community => {
            // Fix icon class - ensure we don't double-prefix with 'bx'
            let iconClass = community.icon || 'bx-group';
            // If icon doesn't start with 'bx ', add it
            if (!iconClass.startsWith('bx ')) {
                iconClass = 'bx ' + iconClass;
            }
            
            // Allow context menu for creator OR Super Admin
            const canManage = (community.created_by == window.currentUserId) || window.isSuperAdmin;
            const isCreator = community.created_by == window.currentUserId;
            const communityData = JSON.stringify(community);
            
            return `
            <div class="community-item" 
                 data-community='${communityData.replace(/'/g, "&apos;")}'
                 data-is-creator="${isCreator}"
                 onclick="selectCommunityFromData(this)"
                 oncontextmenu="showCommunityContextMenu(event, ${community.id}, '${community.name.replace(/'/g, "\\'")}', ${canManage}); return false;">
                <div class="community-avatar" style="${community.avatar ? '' : 'background: ' + (community.color || '#d4af37') + ';'} width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    ${community.avatar ? '<img src="/storage/' + community.avatar + '" alt="' + community.name + '" style="width: 100%; height: 100%; object-fit: cover;">' : '<i class="' + iconClass + '"></i>'}
                </div>
                <div class="community-info">
                    <div class="community-name">${community.name}</div>
                </div>
            </div>
        `;
        }).join('');
    }
    // Load communities - will be called after community creation or when modal opens
    // Note: Initial loading uses loadCommunitiesForDisplay() which is called from DOMContentLoaded

    // Store member IDs for community creation - Already declared at top of script

    // Load users for community member selection
    async function loadUsersForCommunityCreation() {
        try {
            const response = await fetch('/api/chat/users');
            if (!response.ok) {
                console.error('Failed to load users:', response.status);
                return;
            }
            const data = await response.json();
            console.log('Loaded users for community:', data);
            
            const select = document.getElementById('communityMemberSelect');
            
            if (!select) {
                console.error('communityMemberSelect element not found');
                return;
            }
            
            // Clear existing options
            select.innerHTML = '';
            
            // Add placeholder option
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = 'Select a member to add...';
            select.appendChild(placeholderOption);
            
            // Add user options
            if (data.success && data.users && data.users.length > 0) {
                data.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    select.appendChild(option);
                });
                console.log('Added', data.users.length, 'users to select');
            } else {
                console.warn('No users available or data.users is empty');
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    // Add member to community being created
    document.getElementById('addMemberToCommunityBtn')?.addEventListener('click', function() {
        const select = document.getElementById('communityMemberSelect');
        const userId = parseInt(select.value);
        const userName = select.options[select.selectedIndex].text;
        
        if (!userId) {
            alert('Please select a member');
            return;
        }
        
        // Check if already added
        if (window.communityMembersToAdd.find(m => m.id === userId)) {
            alert('This member is already added');
            return;
        }
        
        window.communityMembersToAdd.push({ id: userId, name: userName });
        renderCommunityMembers();
        select.value = '';
    });

    function renderCommunityMembers() {
        const display = document.getElementById('communityMembersDisplay');
        if (window.communityMembersToAdd.length === 0) {
            display.innerHTML = `<p style="color: var(--bs-surface-400, #adb5bd); font-size: 13px;">No members added yet</p>`;
            return;
        }
        
        display.innerHTML = window.communityMembersToAdd.map(member => `
            <div style="background: var(--bs-light); padding: 6px 12px; border-radius: 20px; display: flex; align-items: center; gap: 8px; font-size: 13px;">
                <span>${member.name}</span>
                <button type="button" onclick="removeMemberFromCommunity(${member.id})" style="background: none; border: none; color: var(--bs-surface-500, #6c757d); cursor: pointer; padding: 0; font-size: 16px;">&times;</button>
            </div>
        `).join('');
        
        document.getElementById('communityMemberIds').value = JSON.stringify(window.communityMembersToAdd.map(m => m.id));
    }

    window.removeMemberFromCommunity = function(userId) {
        window.communityMembersToAdd = window.communityMembersToAdd.filter(m => m.id !== userId);
        renderCommunityMembers();
    };

    // Load users when modal opens
    document.getElementById('newCommunityModal')?.addEventListener('show.bs.modal', function() {
        console.log('Create Community modal opened, loading users...');
        loadUsersForCommunityCreation();
        window.communityMembersToAdd = [];  // Reset members
        renderCommunityMembers();
    });

    // Create community button handler
    document.getElementById('createCommunityBtn')?.addEventListener('click', async function() {
        const form = document.getElementById('createCommunityForm');
        const formData = new FormData(form);
        
        // Add member IDs to form data
        formData.append('member_ids', JSON.stringify(window.communityMembersToAdd.map(m => m.id)));
        
        try {
            const response = await fetch('/api/communities', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            // Check if response is ok
            if (!response.ok) {
                const text = await response.text();
                console.error('Server Error:', text);
                alert('Error creating community. Server returned: ' + response.status);
                return;
            }
            
            const data = await response.json();
            if (data.success) {
                form.reset();
                // Reset color preview to default
                const colorPreview = document.getElementById('communityColorPreview');
                if (colorPreview) {
                    colorPreview.style.background = '#d4af37';
                }
                const colorInput = document.getElementById('communityColor');
                if (colorInput) {
                    colorInput.value = '#d4af37';
                }
                window.communityMembersToAdd = [];
                renderCommunityMembers();
                bootstrap.Modal.getInstance(document.getElementById('newCommunityModal')).hide();
                loadCommunitiesForDisplay();
                alert('Community created successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to create community'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error creating community: ' + error.message);
        }
    });
    
    // Community color preview
    document.getElementById('communityColor')?.addEventListener('input', function(e) {
        const color = e.target.value;
        const preview = document.getElementById('communityColorPreview');
        if (preview) {
            preview.style.background = color;
        }
    });
    
    // Group avatar preview - initialize immediately
    const groupAvatarInput = document.getElementById('groupAvatar');
    if (groupAvatarInput) {
        groupAvatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('groupAvatarPreview');
                    if (preview) {
                        preview.innerHTML = 
                            `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;" />`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

// ==========================================
// COMMUNITY EVENT LISTENERS
// ===========================================

// Initialize community management when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Context Menu Event Listeners
    document.addEventListener('click', function(e) {
        // Hide context menu when clicking outside
        const menu = document.getElementById('communityContextMenu');
        if (menu && e.target !== menu && !menu.contains(e.target)) {
            menu.style.display = 'none';
        }
    });

    const ctxManageMembersBtn = document.getElementById('ctxManageMembers');
    if (ctxManageMembersBtn) {
        ctxManageMembersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openCommunityMemberManagement(window.contextMenuCommunityId, window.contextMenuCommunityName);
        });
    }

    const ctxEditCommunityBtn = document.getElementById('ctxEditCommunity');
    if (ctxEditCommunityBtn) {
        ctxEditCommunityBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openEditCommunityModal(window.contextMenuCommunityId);
        });
    }

    const ctxDeleteCommunityBtn = document.getElementById('ctxDeleteCommunity');
    if (ctxDeleteCommunityBtn) {
        ctxDeleteCommunityBtn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteCommunity(window.contextMenuCommunityId, window.contextMenuCommunityName);
        });
    }
    
    // Add Member Button
    const addMemberBtn = document.getElementById('addCommunityMemberBtn');
    if (addMemberBtn) {
        addMemberBtn.addEventListener('click', function() {
            const userId = document.getElementById('newCommunityMemberSelect').value;
            const communityId = document.getElementById('manageCommunityId').value;
            if (userId && communityId) {
                addCommunityMember(communityId, userId);
            }
        });
    }
    
    // Save Community Settings Button
    const saveCommunitySettingsBtn = document.getElementById('saveCommunitySettingsBtn');
    if (saveCommunitySettingsBtn) {
        saveCommunitySettingsBtn.addEventListener('click', function() {
            saveCommunitySettings();
        });
    }
});

// Request notification permission for desktop notifications - FOR ALL USERS
if ('Notification' in window) {
    if (Notification.permission === 'default') {
        console.log('Requesting notification permission...');
        Notification.requestPermission().then(permission => {
            console.log('Notification permission:', permission);
            if (permission === 'granted') {
                console.log('✅ Desktop notifications enabled for chat');
            }
        });
    } else if (Notification.permission === 'granted') {
        console.log('✅ Notification permission already granted');
    } else {
        console.log('❌ Notification permission denied - user will not see desktop notifications');
    }
}

// Show Community Context Menu - For creators and Super Admins (GLOBAL)
window.showCommunityContextMenu = function(event, communityId, communityName, isCreator) {
    event.preventDefault();
    event.stopPropagation();
    
    // Store community info
    window.contextMenuCommunityId = communityId;
    window.contextMenuCommunityName = communityName;
    
    const menu = document.getElementById('communityContextMenu');
    if (!menu) return;
    
    // Allow if user is the creator OR is a Super Admin
    if (!isCreator && !window.isSuperAdmin) {
        console.warn('User is not the community creator or Super Admin - cannot manage this community');
        return;
    }
    
    menu.style.display = 'block';
    menu.style.left = event.pageX + 'px';
    menu.style.top = event.pageY + 'px';
}

// Delete Community - Only for creators (GLOBAL)
window.deleteCommunity = async function(communityId, communityName) {
    if (!confirm(`Are you sure you want to delete "${communityName}"? This cannot be undone.`)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/communities/${communityId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', 'Community deleted successfully');
            // Hide the context menu
            document.getElementById('communityContextMenu').style.display = 'none';
            // Reload communities list
            loadCommunitiesForDisplay();
        } else {
            showAlert('danger', data.message || 'Failed to delete community');
        }
    } catch (error) {
        console.error('Error deleting community:', error);
        showAlert('danger', 'Error deleting community: ' + error.message);
    }
}

// Open Member Management Modal (GLOBAL)
window.openCommunityMemberManagement = async function(communityId, communityName) {
    const manageCommunityIdInput = document.getElementById('manageCommunityId');
    if (!manageCommunityIdInput) {
        console.error('manageCommunityId input not found');
        return;
    }
    
    manageCommunityIdInput.value = communityId;
    const modal = new bootstrap.Modal(document.getElementById('communityMembersModal'));
    modal.show();
    
    // Load members
    await loadCommunityMembers(communityId);
    
    // Load users for select dropdown
    await loadUsersForSelect();
}

// Load members for a community
async function loadCommunityMembers(communityId) {
    const list = document.getElementById('communityMembersList');
    if (!list) return;
    
    list.innerHTML = '<div class="text-center p-3"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</div>';
    
    try {
        const response = await fetch(`/api/communities/${communityId}/members`);
        const data = await response.json();
        
        if (data.success) {
            if (!data.members || data.members.length === 0) {
                list.innerHTML = '<div class="text-center p-3 text-muted">No members yet</div>';
                return;
            }
            
            const communityCreatorId = data.created_by;
            
            list.innerHTML = data.members.map(member => `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="user-avatar me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; overflow: hidden; font-weight: bold;">
                             ${member.avatar ? `<img src="${member.avatar}" alt="${member.name}" style="width: 100%; height: 100%; object-fit: cover;">` : member.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">
                                ${member.name}
                                ${member.id == communityCreatorId ? '<span class="badge bg-secondary ms-1" style="font-size: 10px;">Creator</span>' : ''}
                            </div>
                            <small class="text-muted">${member.email}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" ${member.can_post ? 'checked' : ''} 
                                   onchange="toggleMemberPosting(${communityId}, ${member.id})" 
                                   style="cursor: pointer;" 
                                   title="${member.can_post ? 'Can post' : 'Cannot post'}">
                            <label class="form-check-label small text-muted" style="cursor: pointer;">
                                ${member.can_post ? 'Can post' : 'Restricted'}
                            </label>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeCommunityMember(${communityId}, ${member.id})" title="Remove member">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div class="text-center p-3 text-danger">Error loading members</div>';
        }
    } catch (error) {
        console.error('Error loading community members:', error);
        list.innerHTML = '<div class="text-center p-3 text-danger">Error loading members</div>';
    }
}

// Load available users for member selection
async function loadUsersForSelect() {
    try {
        const response = await fetch('/api/chat/users');
        const data = await response.json();
        const select = document.getElementById('newCommunityMemberSelect');
        
        if (!select) {
            console.error('newCommunityMemberSelect element not found');
            return;
        }
        
        // Keep first option (placeholder)
        const firstOption = select.options[0];
        select.innerHTML = '';
        if (firstOption) {
            select.appendChild(firstOption);
        } else {
            // Add placeholder option if none exists
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Select user...';
            select.appendChild(option);
        }
        
        if (data.users && data.users.length > 0) {
            data.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading users for select:', error);
    }
}

// Add a new member to community
async function addCommunityMember(communityId, userId) {
    try {
        const response = await fetch(`/api/communities/${communityId}/members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload the members list
            await loadCommunityMembers(communityId);
            // Reset the select dropdown
            document.getElementById('newCommunityMemberSelect').value = '';
            showAlert('success', 'Member added successfully');
        } else {
            showAlert('danger', data.message || 'Failed to add member');
        }
    } catch (error) {
        console.error('Error adding community member:', error);
        showAlert('danger', 'Error adding member: ' + error.message);
    }
}

// Remove a member from community
async function removeCommunityMember(communityId, userId) {
    if (!confirm('Are you sure you want to remove this member?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/communities/${communityId}/members/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload the members list
            await loadCommunityMembers(communityId);
            showAlert('success', 'Member removed successfully');
        } else {
            showAlert('danger', data.message || 'Failed to remove member');
        }
    } catch (error) {
        console.error('Error removing community member:', error);
        showAlert('danger', 'Error removing member: ' + error.message);
    }
}

// Toggle member posting permission
async function toggleMemberPosting(communityId, userId) {
    try {
        const response = await fetch(`/api/communities/${communityId}/members/${userId}/toggle-post`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message || 'Permission updated');
            // Reload to reflect changes
            await loadCommunityMembers(communityId);
        } else {
            showAlert('danger', data.message || 'Failed to update permission');
            // Reload to revert checkbox state
            await loadCommunityMembers(communityId);
        }
    } catch (error) {
        console.error('Error toggling member posting:', error);
        showAlert('danger', 'Error updating permission');
        // Reload to revert checkbox state
        await loadCommunityMembers(communityId);
    }
}

// Open Edit Community Modal
async function openEditCommunityModal(communityId) {
    const community = globalCommunities.find(c => c.id === communityId);
    if (!community) {
        showAlert('danger', 'Community not found');
        return;
    }
    
    // Populate form fields
    document.getElementById('editCommunityId').value = community.id;
    document.getElementById('editCommunityName').value = community.name;
    document.getElementById('editCommunityDescription').value = community.description || '';
    document.getElementById('editCommunityColor').value = community.color || '#d4af37';
    document.getElementById('editCommunityPostingRestricted').checked = community.posting_restricted || false;
    
    // Hide context menu
    document.getElementById('communityContextMenu').style.display = 'none';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editCommunityModal'));
    modal.show();
}

// Save Community Settings
async function saveCommunitySettings() {
    const communityId = document.getElementById('editCommunityId').value;
    const name = document.getElementById('editCommunityName').value.trim();
    const description = document.getElementById('editCommunityDescription').value.trim();
    const color = document.getElementById('editCommunityColor').value;
    const postingRestricted = document.getElementById('editCommunityPostingRestricted').checked;
    
    if (!name) {
        showAlert('danger', 'Community name is required');
        return;
    }
    
    try {
        const saveBtn = document.getElementById('saveCommunitySettingsBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        }
        
        const response = await fetch(`/api/communities/${communityId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: name,
                description: description,
                color: color,
                posting_restricted: postingRestricted
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCommunityModal'));
            modal.hide();
            
            // Reload communities list
            await loadCommunitiesForDisplay();
            
            showAlert('success', 'Community updated successfully');
        } else {
            showAlert('danger', data.message || 'Failed to update community');
        }
    } catch (error) {
        console.error('Error saving community settings:', error);
        showAlert('danger', 'Error updating community: ' + error.message);
    } finally {
        const saveBtn = document.getElementById('saveCommunitySettingsBtn');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = 'Save Changes';
        }
    }
}

// ===== TYPING INDICATOR =====
let typingClearTimer = null;
let _typingName = null;
function showTypingIndicator(name) {
    _typingName = name;
    const messagesEl = document.getElementById('chatMessages');
    if (!messagesEl) return;

    // Remove existing typing row if present
    const existing = document.getElementById('typingIndicatorRow');
    if (existing) existing.remove();

    const initial = name.replace(/</g,'&lt;').charAt(0).toUpperCase();
    const row = document.createElement('div');
    row.id = 'typingIndicatorRow';
    row.className = 'typing-indicator-row';
    row.innerHTML = `
        <div class="typing-avatar" title="${name.replace(/</g,'&lt;')}">${initial}</div>
        <div class="typing-bubble">
            <span class="typing-dots"><span></span><span></span><span></span></span>
        </div>`;
    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;

    clearTimeout(typingClearTimer);
    typingClearTimer = setTimeout(() => {
        const el = document.getElementById('typingIndicatorRow');
        if (el) el.remove();
        _typingName = null;
    }, 3000);
}

// ===== MENTION NOTIFICATIONS =====

// Send desktop notification for mentions
function sendMentionNotification(title, body, conversationId) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const convId = conversationId || window.currentConversationId;
        const notification = new Notification(title, {
            body: body,
            icon: '/images/logo-icon.png',
            tag: 'chat-mention-' + (convId || 'general'),
            renotify: true,
        });
        
        notification.onclick = function() {
            window.focus();
            if (convId) window.location.href = '/chat?open=' + convId;
            notification.close();
        };
    }
}

// Check message for mentions and notify
function checkAndNotifyMentions(message) {
    if (message && message.message) {
        const msg = message.message.toLowerCase();
        const currentUserNameLower = (currentUserName || '').toLowerCase();
        
        // Check for @[Full Name] format, @username format, or @everyone
        const isMentioned = msg.includes('@everyone') ||
            msg.includes('@' + currentUserNameLower) ||
            msg.includes('@[' + currentUserNameLower + ']');
        
        if (isMentioned) {
            sendMentionNotification(
                `New mention from ${message.user?.name || 'Someone'}`,
                message.message.substring(0, 100) + (message.message.length > 100 ? '...' : ''),
                message.conversation_id
            );
        }
    }
}

// ===== ALERT HELPER =====

// Show alert notifications
function showAlert(type, message) {
    // Try to use existing toast function if available
    if (typeof toast === 'function') {
        toast(type, message);
    } else if (typeof toastr !== 'undefined') {
        // Fallback to toastr if available
        toastr[type](message);
    } else {
        // Last resort: browser alert
        alert(message);
    }
}

</script>

<!-- External Scripts -->
<script src="{{ asset('js/emoji-picker.min.js') }}"></script>

@endsection
