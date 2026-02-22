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
            background: linear-gradient(90deg, var(--bs-gradient-start), var(--bs-gradient-end));
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
            background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
            color: var(--bs-white);
            border: none;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
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
            background: var(--bs-ui-danger);
            color: var(--bs-white);
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

        /* ===== DARK MODE CHAT OVERRIDES ===== */
        [data-theme="dark"] .chat-messages {
            background: var(--bs-surface-900) !important;
        }
        [data-theme="dark"] .message-item.message-receiver .message-text {
            background: var(--bs-print-header-bg) !important;
            color: var(--bs-print-border) !important;
            box-shadow: none !important;
        }
        [data-theme="dark"] .message-item.message-sender .message-text {
            color: var(--bs-white, #fff) !important;
            box-shadow: none !important;
        }
        [data-theme="dark"] .message-username {
            color: var(--bs-surface-muted) !important;
        }
        [data-theme="dark"] .message-text {
            color: var(--bs-print-border) !important;
        }
        [data-theme="dark"] .chat-header,
        [data-theme="dark"] .chat-main-header {
            background: var(--bs-surface-800) !important;
            border-bottom: 1px solid var(--bs-surface-700) !important;
        }
        [data-theme="dark"] .chat-input-area,
        [data-theme="dark"] .chat-footer,
        [data-theme="dark"] .message-input-container {
            background: var(--bs-surface-800) !important;
            border-top: 1px solid var(--bs-surface-700) !important;
        }
        [data-theme="dark"] #messageInput {
            background: var(--bs-print-header-bg) !important;
            border: 1px solid var(--bs-surface-700) !important;
            color: var(--bs-print-border) !important;
        }
        [data-theme="dark"] #messageInput::placeholder {
            color: var(--bs-surface-500) !important;
        }
        [data-theme="dark"] .chat-sidebar {
            background: var(--bs-surface-800) !important;
            border-right: 1px solid var(--bs-surface-700) !important;
        }
        [data-theme="dark"] .conversation-item,
        [data-theme="dark"] .conversation-list-item {
            background: var(--bs-surface-800) !important;
            border-bottom: 1px solid var(--bs-surface-700) !important;
            color: var(--bs-print-border) !important;
        }
        [data-theme="dark"] .conversation-item:hover,
        [data-theme="dark"] .conversation-list-item:hover {
            background: var(--bs-print-header-bg) !important;
        }
        [data-theme="dark"] .conversation-item.active,
        [data-theme="dark"] .conversation-list-item.active {
            background: var(--bs-print-header-bg) !important;
        }
        [data-theme="dark"] .no-messages {
            color: var(--bs-surface-muted) !important;
        }
        [data-theme="dark"] .chat-search-input,
        [data-theme="dark"] #searchConversations,
        [data-theme="dark"] #searchCommunities,
        [data-theme="dark"] #searchPeople {
            background: var(--bs-print-header-bg) !important;
            border: 1px solid var(--bs-surface-700) !important;
            color: var(--bs-print-border) !important;
        }
        [data-theme="dark"] .announcement-item {
            background: var(--bs-surface-800) !important;
            color: var(--bs-print-border) !important;
            box-shadow: none !important;
        }
        [data-theme="dark"] .announcement-messages,
        [data-theme="dark"] #announcementMessages {
            background: var(--bs-surface-900) !important;
        }
        [data-theme="dark"] .announcement-input-area {
            background: var(--bs-surface-800) !important;
            border-top-color: var(--bs-surface-700) !important;
        }
        [data-theme="dark"] .announcement-input-area textarea,
        [data-theme="dark"] #announcementInput {
            background: var(--bs-print-header-bg) !important;
            border-color: var(--bs-surface-700) !important;
            color: var(--bs-print-border) !important;
        }
        [data-theme="dark"] .mention-highlight {
            background: rgba(212, 175, 55, 0.2) !important;
            color: var(--bs-gold) !important;
        }
        [data-theme="dark"] .file-attachment {
            background: var(--bs-print-header-bg) !important;
            border-color: var(--bs-surface-700) !important;
            color: var(--bs-print-border) !important;
        }
        [data-theme="dark"] .message-attachment a {
            background: var(--bs-print-header-bg) !important;
            color: var(--bs-surface-muted) !important;
        }
    </style>
@endsection
@section('content')

    <div class="chat-wrapper">
        <div class="chat-container">
            <!-- LEFT SIDEBAR - ICONS -->
            <div class="chat-icon-sidebar">
                <button class="chat-logo-btn" title="Taurus CRM">T</button>

                <div class="chat-icon-nav">
                    <button class="chat-icon-btn active" data-tab="chats" title="Messages">
                        <i class="bx bx-message-dots"></i>
                    </button>
                    <button class="chat-icon-btn" data-tab="groups" title="Groups">
                        <i class="bx bx-group"></i>
                    </button>
                    <button class="chat-icon-btn" data-tab="communities" title="Communities">
                        <i class="bx bx-buildings"></i>
                    </button>
                    <button class="chat-icon-btn" data-tab="people" title="People">
                        <i class="bx bx-user"></i>
                    </button>
                </div>

                <div class="chat-icon-bottom">
                </div>
            </div>

            <!-- MAIN SIDEBAR - CONVERSATIONS & COMMUNITIES -->
            <div class="chat-sidebar">
                <!-- Chats Tab -->
                <div id="chats-tab" class="chat-sidebar-content active">
                    <div class="chat-sidebar-header">
                        <h5>Messages</h5>
                    </div>

                    <div class="chat-search-box">
                        <input type="text" id="searchConversations" placeholder="Search conversations..." class="chat-search-input">
                    </div>

 <div class="conversations-list u-overflow-y-auto" id="conversationsList" style="max-height: calc(100vh - 220px); overflow-x: hidden">
                        <div class="loading-conversations">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
 <p class="u-fs-085 mt-2" >Loading chats...</p>
                        </div>
                    </div>
                </div>

                <!-- Groups Tab -->
 <div id="groups-tab" class="chat-sidebar-content" >
                    <div class="chat-sidebar-header">
                        <h5>Groups</h5>
                        <div class="btn-group">
 <button class="btn btn-primary border-0 u-fs-13 u-fw-600 text-white" data-bs-toggle="modal" data-bs-target="#newChatModal" title="Create new group" style="background: linear-gradient(135deg, var(--bs-gold), var(--bs-gold-dark)); padding: 6px 12px">
                                <i class="bx bx-plus" style="font-size: 16px;"></i> Create
                            </button>
                        </div>
                    </div>

                    <div class="chat-search-box">
                        <input type="text" id="searchGroups" placeholder="Search groups..." class="chat-search-input">
                    </div>

 <div class="group-conversations-list u-overflow-y-auto" id="groupConversationsList" style="max-height: calc(100vh - 220px); overflow-x: hidden">
                        <!-- Group conversations will be loaded here -->
                    </div>
                </div>

                <!-- Communities Tab -->
 <div id="communities-tab" class="chat-sidebar-content" >
                    <div class="chat-sidebar-header">
                        <h5>Communities</h5>
                        <div class="btn-group">
                            @if(Auth::user()->hasRole([Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR]))
                                <button class="btn" data-bs-toggle="modal" data-bs-target="#newCommunityModal" title="Create community">
                                    <i class="bx bx-plus"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="chat-search-box">
                        <input type="text" id="searchCommunities" placeholder="Search communities..." class="chat-search-input">
                    </div>

 <div class="communities-list u-overflow-y-auto" id="communitiesList" style="max-height: calc(100vh - 220px); overflow-x: hidden">
                        <!-- Communities will be loaded here -->
                    </div>
                </div>

                <!-- People Tab -->
 <div id="people-tab" class="chat-sidebar-content" >
                    <!-- People tab now uses full container width when active -->
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

            <!-- PEOPLE FULL PAGE -->
 <div class="people-main w-100 p-4" id="peopleMain" style="display: none; background: var(--chat-bg-secondary)">
                <div class="people-header" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid var(--chat-border-color);">
 <div class="d-flex justify-content-between align-items-center mx-auto" style="max-width: 1200px">
                        <div>
 <h2 class="u-fw-700 m-0" style="color: var(--chat-text-primary); font-size: 28px">Team Directory</h2>
                            <p style="color: var(--chat-text-secondary); margin: 5px 0 0 0; font-size: 16px;">Connect with your colleagues</p>
                        </div>
                        <div class="position-relative">
                            <input type="text" id="searchPeopleCards" placeholder="Search people..." class="u-fs-14" style="padding: 12px 20px 12px 45px; border: 2px solid var(--chat-border-color); border-radius: 25px; width: 300px; transition: all 0.3s ease; outline: none; background: var(--chat-bg-body); color: var(--chat-text-primary)" onFocus="this.style.borderColor='#DAA520';" onBlur="this.style.borderColor='var(--chat-border-color)';">
                            <i class="bx bx-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: var(--chat-text-secondary); font-size: 18px"></i>
                        </div>
                    </div>
                </div>
 <div class="people-content u-overflow-y-auto mx-auto" style="max-width: 1200px; max-height: calc(100vh - 280px); padding-right: 10px">
                    <div id="peopleCards" class="people-cards-grid p-0" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px">
                        <!-- People cards will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal - Redesigned -->
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
 <div class="u-w-80 rounded-circle d-flex align-items-center justify-content-center text-white u-fw-700 overflow-hidden u-fs-32" id="groupAvatarPreview" style="height: 80px; background: linear-gradient(135deg, var(--bs-ui-info), var(--bs-ui-info-dark))">
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
 <div class="modal-footer bg-surface-50 border-top-surface">
                    <button type="button" class="btn btn-secondary bg-white border-surface-200 text-surface-600" data-bs-dismiss="modal">Cancel</button>
 <button type="button" class="btn btn-primary border-0" id="createChatBtn" style="background: linear-gradient(135deg, var(--bs-ui-info-dark), var(--bs-ui-info-dark)); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3)">Create Group</button>
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
 <div class="rounded-circle d-flex align-items-center justify-content-center text-white u-fw-700 overflow-hidden u-w-60" id="groupAvatarPreview" style="height: 60px; background: linear-gradient(135deg, var(--bs-gold), var(--bs-gold-dark)); font-size: 20px">
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
 <button type="button" class="btn flex-grow-1 text-white border-0 u-rounded-8 u-fw-600 u-cursor-pointer py-2 px-3" onclick="updateGroupName()" style="background: linear-gradient(135deg, var(--bs-ui-info-dark), var(--bs-ui-info-dark)); transition: all 0.3s ease">Update Name</button>
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
 <div class="modal-footer bg-surface-50 border-top-surface">
 <button type="button" class="btn u-rounded-8 u-fw-600 bg-white py-2 px-3 border-surface-200 text-surface-600" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- GIF Picker Modal -->
<div class="modal fade" id="gifPickerModal" tabindex="-1" aria-labelledby="gifPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
 <div class="modal-content overflow-hidden border-0" style="border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%); border-bottom: none; padding: 20px 24px;">
 <h5 class="modal-title text-white u-fw-700 m-0" id="gifPickerModalLabel" style="font-size: 20px">
                    <i class="bx bx-image" style="margin-right: 8px;"></i>Choose a GIF
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: var(--bs-body-bg);">
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
 <div class="rounded-circle d-flex align-items-center justify-content-center text-white u-fw-700 u-w-60" id="communityColorPreview" style="height: 60px; background: var(--bs-gradient-start); font-size: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.15)">
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
 <button class="text-white border-0 u-rounded-8 u-cursor-pointer u-fw-600 py-2 px-3 u-ws-nowrap" type="button" id="addMemberToCommunityBtn" style="background: var(--bs-ui-info)">
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
 <div class="modal-footer bg-surface-50 border-top-surface">
 <button type="button" class="btn u-rounded-8 u-fw-600 bg-white py-2 px-3 border-surface-200 text-surface-600" data-bs-dismiss="modal">Cancel</button>
 <button type="button" class="btn btn-primary border-0 u-fw-600" id="createCommunityBtn" style="background: linear-gradient(135deg, var(--bs-ui-info-dark), var(--bs-ui-info-dark)); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); padding: 10px 24px">Create Community</button>
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
 <div class="rounded-circle d-flex align-items-center justify-content-center text-white u-w-40" id="popupCommunityIcon" style="height: 40px; background: var(--bs-gradient-start); font-size: 20px">
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
 <div class="modal-footer bg-surface-50 border-top-surface">
 <button type="button" class="btn u-rounded-8 u-fw-600 bg-white py-2 px-3 border-surface-200 text-surface-600" data-bs-dismiss="modal">Cancel</button>
 <button type="button" class="btn btn-primary border-0 u-fw-600" id="updateAnnouncementBtn" onclick="updateAnnouncement()" style="background: linear-gradient(135deg, var(--bs-ui-info-dark), var(--bs-ui-info-dark)); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); padding: 10px 24px">
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
    const tabButtons = document.querySelectorAll('.chat-icon-btn');
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
                // Hide chat sidebar and main area, show people page
                chatSidebar.style.display = 'none';
                chatMain.style.display = 'none';
                peopleMain.style.display = 'block';
                loadPeopleCardsForDisplay();
            } else {
                // Show chat sidebar and main area, hide people page
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
        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--chat-text-secondary); padding: 60px; font-size: 18px;"><i class="bx bx-user" style="font-size: 64px; margin-bottom: 20px; display: block;"></i>No people available</div>';
        return;
    }

    container.innerHTML = users.map(user => `
        <div class="person-card" onclick="startPersonChat(${user.id}, '${user.name.replace(/'/g, "\\'")}')" 
             data-user-name="${user.name}" data-user-email="${user.email}"
             style="
                background: var(--chat-bg-body); 
                border-radius: 16px; 
                padding: 24px; 
                text-align: center; 
                cursor: pointer; 
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border: 1px solid var(--chat-border-color);
                position: relative;
                overflow: hidden;
                color: var(--chat-text-primary);
             "
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(218, 165, 32, 0.2)';"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';">
            
            <!-- Gold gradient accent -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(135deg, ${themeColors.gold} 0%, ${themeColors.goldDark} 100%);"></div>
            
            <!-- Profile Picture -->
            <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 4px solid var(--chat-bg-secondary); box-shadow: 0 4px 12px rgba(218, 165, 32, 0.15);">
                    ${user.avatar ? 
                        `<img src="${user.avatar}" alt="${user.name}" style="width: 100%; height: 100%; object-fit: cover;">` : 
                        `<div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 32px;">${user.name.charAt(0).toUpperCase()}</div>`
                    }
                </div>
            </div>
            
            <!-- User Info -->
            <div style="margin-bottom: 16px;">
                <h3 style="color: var(--chat-text-primary); font-weight: 600; margin: 0 0 4px 0; font-size: 18px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="user-name">${user.name}</h3>
                <p style="color: var(--chat-text-secondary); margin: 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="user-email">${user.email}</p>
            </div>
            
            <!-- Action Button -->
            <div style="margin-top: 20px;">
                <button style="
                    background: linear-gradient(135deg, ${themeColors.gold} 0%, ${themeColors.goldDark} 100%); 
                    color: white; 
                    border: none; 
                    padding: 10px 20px; 
                    border-radius: 20px; 
                    font-weight: 600; 
                    font-size: 14px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    width: 100%;
                    box-shadow: 0 2px 8px rgba(218, 165, 32, 0.3);
                " onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)';" onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                    <i class="bx bx-message" style="margin-right: 6px;"></i>Start Chat
                </button>
            </div>
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
            noResultsMsg.style.cssText = 'grid-column: 1/-1; text-align: center; color: var(--chat-text-secondary); padding: 40px; font-size: 16px;';
            noResultsMsg.innerHTML = '<i class="bx bx-search" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>No people found matching your search';
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
        listEl.innerHTML = `<div style="padding: 20px; text-align: center; color: ${themeColors.surfaceMuted}; font-size: 14px;">No people available</div>`;
        return;
    }

    listEl.innerHTML = users.map(user => `
        <button class="user-item" onclick="openPersonChat(${user.id}, '${user.name.replace(/'/g, "\\'")}', event)" data-user-id="${user.id}" data-user-name="${user.name}" data-user-email="${user.email}" style="width: 100%; text-align: left; background: none; border: none; padding: 10px; margin: 5px 0; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="user-avatar" style="width: 45px; height: 45px; flex-shrink: 0;">
                    ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">` : `<div style="width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, ${themeColors.gradientStart} 0%, ${themeColors.gradientEnd} 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px;">${user.name.charAt(0).toUpperCase()}</div>`}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="user-name" style="font-weight: 600; color: ${themeColors.surface700}; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${user.name}</div>
                    <div class="user-email" style="font-size: 12px; color: ${themeColors.surfaceMuted}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${user.email}</div>
                </div>
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
        listEl.innerHTML = `<div style="padding: 20px; text-align: center; color: ${themeColors.surfaceMuted}; font-size: 14px;">No communities yet</div>`;
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
             style="position: relative; display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s; border-left: 3px solid ${community.color || themeColors.gradientStart};">
            <div class="community-avatar" style="${community.avatar ? '' : 'background: ' + (community.color || themeColors.gradientStart) + ';'} flex-shrink: 0; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                ${community.avatar ? '<img src="/storage/' + community.avatar + '" alt="' + community.name + '" style="width: 100%; height: 100%; object-fit: cover;">' : '<i class="' + iconClass + '"></i>'}
            </div>
            <div class="community-info" style="flex: 1; min-width: 0;">
                <div class="community-name" style="font-weight: 600;">${community.name}</div>
                <div style="font-size: 11px; color: ${themeColors.surfaceMuted}; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                    <i class="bx bx-bullhorn" style="font-size: 12px;"></i>
                    <span>Announcement Board</span>
                </div>
            </div>
            ${canManage ? `
                <button class="btn-delete-community btn-delete-${community.id}" 
                        data-community-id="${community.id}" 
                        data-community-name="${community.name}" 
                        title="Delete Community" 
                        style="flex-shrink: 0; background: ${themeColors.danger}; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 14px; transition: all 0.2s; opacity: 0;">
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
                    this.style.background = themeColors.surface100;
                    const deleteBtn = this.querySelector('.btn-delete-community');
                    if (deleteBtn) deleteBtn.style.opacity = '1';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.background = 'transparent';
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
                
                // Add hover effects
                btn.addEventListener('mouseenter', function() {
                    this.style.background = themeColors.dangerDark;
                });
                btn.addEventListener('mouseleave', function() {
                    this.style.background = themeColors.danger;
                });
            });
        }
    }, 100);
}

// Render group conversations list
function renderGroupConversationsList(conversations) {
    const listEl = document.getElementById('groupConversationsList');
    if (conversations.length === 0) {
        listEl.innerHTML = `<div style="padding: 20px; text-align: center; color: ${themeColors.surfaceMuted}; font-size: 14px;">No group chats yet</div>`;
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
             onclick="selectConversation(${conv.id}, '${safeName}', this)"
             style="position: relative; display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
            <div class="conversation-avatar" style="flex-shrink: 0; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; background: ${themeColors.gradientStart}; color: white; font-size: 18px;">
                ${avatarHtml}
            </div>
            <div class="conversation-info" style="flex: 1; min-width: 0;">
                <div class="conversation-name">${displayName}</div>
                ${conv.latest_message ? `<div class="conversation-preview" style="font-size: 13px; color: ${themeColors.surfaceMuted}; margin-top: 2px;">${(conv.latest_message.message || '').substring(0, 40)}...</div>` : ''}
            </div>
            ${conv.updated_at ? `<div class="conversation-time" style="font-size: 12px; color: ${themeColors.surfaceMuted};">${conv.updated_at}</div>` : ''}
            ${conv.unread_count > 0 ? `<span class="unread-badge" style="background: ${themeColors.danger}; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; margin-left: 4px;">${conv.unread_count}</span>` : ''}
        </div>
        `;
    }).join('');
    
    // Add hover effects for group conversations
    setTimeout(() => {
        const groupListEl = document.getElementById('groupConversationsList');
        if (groupListEl) {
            groupListEl.querySelectorAll('.group-conversation-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.background = themeColors.surface100;
                    }
                });
                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.background = 'transparent';
                    }
                });
            });
        }
    }, 100);
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
        const communityColor = community.color || themeColors.gradientStart;
        
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
        const avatarHtml = `<div class="chat-header-avatar" style="width: 44px; height: 44px; border-radius: 50%; background: ${communityColor}; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;"><i class="bx bx-bullhorn"></i></div>`;
        
        // Build the community interface
        chatMain.innerHTML = `
            <div class="chat-header">
                <div class="chat-header-info">
                    ${avatarHtml}
                    <div class="chat-header-title">
                        <h5>${communityName}</h5>
                        <p style="font-size: 12px; color: ${themeColors.surfaceMuted}; display: flex; align-items: center; gap: 4px;">
                            <i class="bx bx-bullhorn" style="font-size: 13px;"></i>
                            Announcement Board
                        </p>
                    </div>
                </div>
                ${isCreator || window.isSuperAdmin ? `
                    <div style="display: flex; gap: 8px;">
                        <button onclick="openEditCommunityModal(${communityId})" class="btn btn-sm btn-outline-primary" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-weight: 600;" title="Edit Community Settings">
                            <i class="bx bx-cog" style="font-size: 16px;"></i>
                            <span>Settings</span>
                        </button>
                        <button onclick="openCommunityMemberManagement(${communityId}, '${communityName.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-secondary" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-weight: 600;" title="Manage Members">
                            <i class="bx bx-user-plus" style="font-size: 16px;"></i>
                            <span>Members</span>
                        </button>
                    </div>
                ` : ''}
            </div>
            <div class="announcement-messages" id="announcementMessages" style="flex: 1; overflow-y: auto; padding: 20px; background: ${themeColors.surface50};">
                <div id="announcementsContainer">
                    ${announcementsData.announcements.length > 0 ? 
                        renderAnnouncements(announcementsData.announcements, communityColor) : 
                        `<div style="max-width: 500px; margin: 60px auto; text-align: center;">
                            <i class="bx bx-bullhorn" style="font-size: 64px; color: ${communityColor}; opacity: 0.3;"></i>
                            <h5 class="mt-3" style="color: ${themeColors.surface700}; font-weight: 600;">${translations.noAnnouncementsYet}</h5>
                            <p style="color: ${themeColors.surface500}; margin-top: 12px; line-height: 1.6;">
                                ${canPostAnnouncement ? translations.beTheFirstToPost : translations.onlyAuthorizedUsers}
                            </p>
                        </div>`
                    }
                </div>
            </div>
            ${canPostAnnouncement ? `
                <div class="announcement-input-area" style="border-top: 2px solid ${themeColors.surface200}; background: var(--bs-card-bg); padding: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 12px; position: relative;">
                        <textarea id="announcementInput" placeholder="Type @ to mention someone, @everyone to mention all..." rows="2" style="width: 100%; padding: 12px; border: 1px solid ${themeColors.surface300}; border-radius: 8px; font-size: 14px; resize: vertical; min-height: 60px;"></textarea>
                        <div id="announcementMentionSuggestions" class="mention-suggestions" style="display: none;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; gap: 8px;">
                                <button type="button" id="attachAnnouncementBtn" title="Attach file" style="padding: 8px 12px; background: ${themeColors.surface100}; border: 1px solid ${themeColors.surface300}; border-radius: 6px; cursor: pointer; color: ${themeColors.surface500};">
                                    <i class="bx bx-paperclip" style="font-size: 18px;"></i>
                                </button>
                                <button type="button" id="emojiAnnouncementBtn" title="Add emoji" style="padding: 8px 12px; background: ${themeColors.surface100}; border: 1px solid ${themeColors.surface300}; border-radius: 6px; cursor: pointer; color: ${themeColors.surface500};">
                                    <i class="bx bx-smile" style="font-size: 18px;"></i>
                                </button>
                            </div>
                            <button type="button" id="sendAnnouncementBtn" onclick="sendAnnouncement()" style="padding: 10px 24px; background: linear-gradient(135deg, ${communityColor}, ${adjustColor(communityColor, -20)}); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <i class="bx bx-paper-plane" style="font-size: 16px;"></i>
                                Post Announcement
                            </button>
                        </div>
                    </div>
                    <input type="file" id="announcementFileInput" multiple style="display: none" accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                </div>
            ` : `
                <div style="border-top: 2px solid ${themeColors.surface200}; background: ${themeColors.surface50}; padding: 16px; text-align: center; color: ${themeColors.surfaceMuted}; font-size: 13px;">
                    <i class="bx bx-lock-alt" style="font-size: 16px; vertical-align: middle; margin-right: 4px;"></i>
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
        return `<div style="max-width: 500px; margin: 60px auto; text-align: center;">
            <i class="bx bx-bullhorn" style="font-size: 64px; color: ${communityColor}; opacity: 0.3;"></i>
            <h5 class="mt-3" style="color: ${themeColors.surface700}; font-weight: 600;">${translations.noAnnouncementsYet}</h5>
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
            'urgent': themeColors.danger,
            'warning': themeColors.warning,
            'info': themeColors.info,
            'normal': themeColors.surface500
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
            <div class="announcement-item" style="margin-bottom: 24px; padding: 20px; background: var(--bs-card-bg); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid ${priorityColor};">
                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                    <img src="${avatar}" alt="${creatorName}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(creatorName)}&background=${communityColor.replace('#', '')}&color=fff'" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid ${communityColor};">
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <strong style="color: ${themeColors.surface700}; font-size: 14px;">${creatorName}</strong>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: ${priorityColor}; color: white; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                        <i class="bx ${priorityIcon}" style="font-size: 13px;"></i>
                                        ${priorityLabel}
                                    </span>
                                </div>
                                <div style="font-size: 12px; color: ${themeColors.surfaceMuted};">${announcement.created_at_human}</div>
                            </div>
                            ${creatorId === window.currentUserId ? `
                                <div style="display: flex; gap: 8px;">
                                    <button onclick='editAnnouncement(${announcement.id}, ${JSON.stringify(announcement.title || "")}, ${JSON.stringify(announcement.message)}, "${announcement.priority || "normal"}")' style="background: none; border: none; color: ${themeColors.info}; cursor: pointer; padding: 4px 8px;" title="Edit">
                                        <i class="bx bx-edit" style="font-size: 18px;"></i>
                                    </button>
                                    <button onclick="deleteAnnouncement(${announcement.id})" style="background: none; border: none; color: ${themeColors.danger}; cursor: pointer; padding: 4px 8px;" title="Delete">
                                        <i class="bx bx-trash" style="font-size: 18px;"></i>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                        ${announcement.title ? `<div style="font-weight: 600; color: ${communityColor}; margin-bottom: 8px; font-size: 15px;">${escapeHtml(announcement.title)}</div>` : ''}
                        <div style="color: ${themeColors.surface600}; line-height: 1.6; white-space: pre-wrap; word-wrap: break-word;">${formatMessageText(announcement.message)}</div>
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
                container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || themeColors.gradientStart);
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
            const color = community?.color || themeColors.gradientStart;
            sendBtn.innerHTML = `<i class="bx bx-paper-plane" style="font-size: 16px;"></i> Post Announcement`;
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
                <div style="max-width: 500px; margin: 60px auto; text-align: center;">
                    <i class="bx bx-bullhorn" style="font-size: 64px; color: ${communityColor}; opacity: 0.3;"></i>
                    <h5 class="mt-3" style="color: ${themeColors.surface700}; font-weight: 600;">${translations.noAnnouncementsYet}</h5>
                    <p style="color: ${themeColors.surface500}; margin-top: 12px;">${translations.beTheFirstToPost}</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = messages.messages.map(msg => `
            <div class="announcement-item" style="background: var(--bs-card-bg); border-left: 4px solid ${communityColor}; border-radius: 8px; padding: 16px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: start; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: ${communityColor}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px; flex-shrink: 0;">
                        ${(msg.user?.name || 'U').charAt(0).toUpperCase()}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <div>
                                <div style="font-weight: 600; color: ${themeColors.surface700}; font-size: 14px;">${msg.user?.name || 'Unknown'}</div>
                                <div style="font-size: 12px; color: ${themeColors.surfaceMuted};">${msg.created_at}</div>
                            </div>
                            ${msg.user_id === window.currentUserId ? `
                                <button onclick="deleteAnnouncement(${msg.id})" style="background: none; border: none; color: ${themeColors.danger}; cursor: pointer; padding: 4px 8px;" title="Delete">
                                    <i class="bx bx-trash" style="font-size: 16px;"></i>
                                </button>
                            ` : ''}
                        </div>
                        <div style="color: var(--bs-body-color); font-size: 14px; line-height: 1.6; white-space: pre-wrap;">${msg.message}</div>
                        ${msg.attachments && msg.attachments.length > 0 ? `
                            <div style="margin-top: 12px; display: flex; flex-wrap: gap: 8px;">
                                ${msg.attachments.map(att => `
                                    <a href="${att.url}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; background: ${themeColors.surface100}; border-radius: 6px; text-decoration: none; color: ${themeColors.surface500}; font-size: 13px;">
                                        <i class="bx bx-file" style="font-size: 16px;"></i>
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
                    container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || themeColors.gradientStart);
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
                        container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || themeColors.gradientStart);
                    } else {
                        container.innerHTML = `
                            <div style="max-width: 500px; margin: 60px auto; text-align: center;">
                                <i class="bx bx-bullhorn" style="font-size: 64px; color: ${community.color || themeColors.gradientStart}; opacity: 0.3;"></i>
                                <h5 class="mt-3" style="color: ${themeColors.surface700}; font-weight: 600;">${translations.noAnnouncementsYet}</h5>
                                <p style="color: ${themeColors.surface500}; margin-top: 12px;">${translations.beTheFirstToPost}</p>
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

// Chat Application Variables - Already declared at top of script

// Load conversations
async function loadConversations() {
    try {
        console.log('Loading conversations...');
        const conversationsData = await apiCall('/api/chat/conversations');
        console.log('Conversations loaded:', conversationsData);
        console.log('Filtering out community conversations (those with community_id)...');
        const usersData = await apiCall('/api/chat/users');
        console.log('Users loaded:', usersData);
        renderConversationsAndUsers(conversationsData.conversations, usersData.users);
    } catch (error) {
        console.error('Error loading conversations:', error);
        const listEl = document.getElementById('conversationsList');
        listEl.innerHTML = '<div class="no-conversations"><p style="color: red;">Error loading chats. Please refresh the page.</p><p style="font-size: 12px; color: ${themeColors.surface500};">Check browser console for details</p></div>';
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
                 onclick="selectConversation(${conv.id}, '${safeName}', this)">
                <div class="conversation-avatar">
                    ${avatarUrl ? `<img src="${avatarUrl}" alt="${displayName}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">` : displayName.charAt(0).toUpperCase()}
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
    window.lastMessagesHash = null; // Reset hash to force render when switching conversations

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
        let conversationObj = data.conversation || {};
        
        console.log('Rendering messages:', messages.length, 'Type:', conversationType);
        
        renderChatArea(conversationName, messages, conversationType, conversationId, communityId);
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Render chat area
async function renderChatArea(conversationName, messages, conversationType = 'direct', conversationId = null, communityId = null) {
    const chatMain = document.getElementById('chatMain');

    // Set the current conversation ID and name for message sending
    window.currentConversationId = conversationId;
    window.currentConversationName = conversationName;
    window.currentCommunityId = communityId;

    // Check if this is a community conversation
    const isCommunity = communityId !== null && communityId !== undefined;

    // Get conversation avatar if available
    let avatarHtml = `<div class="chat-header-avatar" style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--bs-gold), var(--bs-gold-dark)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px;">${conversationName.charAt(0).toUpperCase()}</div>`;
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
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            ${renderMessages(messages)}
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

    // Load mention users (event delegation handles the actual autocomplete)
    await loadMentionUsers();

    // Start auto-refresh for messages (5 seconds for near real-time updates)
    clearInterval(window.messagesRefreshInterval);
    window.messagesRefreshInterval = setInterval(() => {
        if (window.currentConversationId) {
            refreshMessages();
        }
    }, 5000); // 5 seconds - animation removed so refresh is invisible
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format message text with mentions and GIFs
function formatMessageText(text) {
    if (!text) return '';
    
    // Check if this is a GIF message
    if (text.startsWith('[GIF]')) {
        const gifUrl = text.substring(5); // Remove [GIF] prefix
        return `<img src="${gifUrl}" alt="GIF" style="max-width: 300px; border-radius: 8px; display: block; margin: 4px 0;">`;
    }
    
    // First escape HTML
    let escaped = escapeHtml(text);
    
    // Highlight @[Full Name] mentions (multi-word names in brackets)
    escaped = escaped.replace(/@\[([^\]]+)\]/g, '<span class="mention-highlight">@$1</span>');
    
    // Highlight @word mentions (single-word including @everyone)
    escaped = escaped.replace(/@(everyone|\w+)/g, '<span class="mention-highlight">@$1</span>');
    
    return escaped;
}

// Render messages
function renderMessages(messages) {
    if (messages.length === 0) {
        return '<div class="no-messages"><i class="bx bx-message-dots"></i><p>No messages yet. Start the conversation!</p></div>';
    }

    return messages.map(msg => {
        const isSender = msg.user_id === window.currentUserId;
        const userName = escapeHtml(msg.user?.name || 'Unknown User');
        const userAvatar = msg.user?.avatar;
        const avatarHtml = userAvatar 
            ? `<img src="${userAvatar}" alt="${userName}" class="message-avatar">` 
            : `<div class="message-avatar">${userName.charAt(0).toUpperCase()}</div>`;
        return `
        <div class="message-item ${isSender ? 'message-sender' : 'message-receiver'}" data-message-id="${msg.id}" style="position: relative;">
            ${!isSender ? avatarHtml : ''}
            <div class="message-content">
                ${!isSender ? `<div class="message-username">${userName}</div>` : ''}
                ${msg.message ? `<div class="message-text">${formatMessageText(msg.message)}</div>` : ''}
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
                <div class="message-actions">
                    <button onclick="deleteMessage(${msg.id})" title="Delete"><i class="bx bx-trash"></i></button>
                </div>
            </div>
        </div>
    `;
    }).join('');
}

// Refresh messages
async function refreshMessages() {
    if (!window.currentConversationId) return;

    try {
        const data = await apiCall(`/api/chat/conversations/${window.currentConversationId}/messages`);
        
        // Handle different possible data structures
        const messages = data.messages || data.data || [];
        
        const messagesEl = document.getElementById('chatMessages');
        
        // Smart comparison: Only update if messages changed
        // Track message count and last message ID to prevent unnecessary re-renders
        if (typeof window.lastMessagesHash === 'undefined') {
            window.lastMessagesHash = null;
        }
        
        // Create a simple hash from message count and last message ID
        const currentHash = messages.length > 0 
            ? `${messages.length}-${messages[messages.length - 1].id}`
            : '0-0';
        
        // Only update DOM if messages actually changed
        if (window.lastMessagesHash !== currentHash) {
            const scrolledToBottom = messagesEl.scrollHeight - messagesEl.scrollTop === messagesEl.clientHeight;

            messagesEl.innerHTML = renderMessages(messages);

            if (scrolledToBottom) {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }
            
            window.lastMessagesHash = currentHash;
        }
        
        // Track last message ID for notifications
        if (typeof window.lastMessageId === 'undefined') {
            window.lastMessageId = 0;
        }
        
        // Check for new messages and mentions
        if (messages.length > 0) {
            const latest = messages[messages.length - 1];
            if (latest.id > window.lastMessageId) {
                window.lastMessageId = latest.id;
                // Only notify if looking at a different conversation or window hidden
                checkAndNotifyMentions(latest);
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
        console.log('Message deleted successfully');
        await refreshMessages();
        loadConversations(); // Update conversation list
    } catch (error) {
        console.error('Error deleting message:', error);
        alert('Failed to delete message: ' + error.message);
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
    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const message = input.value.trim();

    if (!message && fileInput.files.length === 0) {
        alert('Please enter a message or select a file');
        return;
    }
    
    if (!window.currentConversationId) {
        alert('Please select a conversation first');
        return;
    }

    console.log('Sending message to conversation:', window.currentConversationId);
    console.log('Current community ID:', window.currentCommunityId);

    const formData = new FormData();
    formData.append('conversation_id', window.currentConversationId);
    
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
        } else if (error.response) {
            try {
                const errorData = await error.response.json();
                errorMessage += ': ' + (errorData.message || 'Unknown error');
            } catch (e) {
                errorMessage += ': Server error';
            }
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
            window.currentConversationId = data.conversation_id;
            
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

// Initial load is handled in DOMContentLoaded listener above (line ~426)
// Removed duplicate call here to prevent refresh loop

// Auto-refresh conversations every 60 seconds (reduced frequency to prevent fading)
window.conversationsRefreshInterval = setInterval(loadConversations, 60000);

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(window.messagesRefreshInterval);
    clearInterval(window.conversationsRefreshInterval);
});

// -------------------------
// Laravel Echo (Pusher) setup for self-hosted laravel-websockets
// This uses CDN scripts (pusher-js + laravel-echo) so you don't have to
// install npm packages immediately. It is safe if broadcasting isn't
// configured — the code will try to initialize Echo and silently fail.
// -------------------------

// Configuration from environment (100% local Reverb, no Pusher)
// Use var to prevent "already declared" errors if script is loaded twice
if (typeof echoConfig === 'undefined') {
    var echoConfig = {!! json_encode([
        'key' => env('REVERB_APP_KEY', ''),
        'host' => env('REVERB_HOST', '127.0.0.1'),
        'port' => intval(env('REVERB_PORT', 8080)),
        'scheme' => env('REVERB_SCHEME', 'http'),
        'forceTLS' => env('REVERB_SCHEME', 'http') === 'https',
    ]) !!};
}

// Prevent redeclaration if script loads multiple times
if (typeof echoInstance === 'undefined') {
    var echoInstance = null;
}
if (typeof subscribedChannel === 'undefined') {
    var subscribedChannel = null;
}

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
            echoInstance.leave(`chat.conversation.${subscribedChannel}`);
        }
    } catch (e) { /* ignore */ }

    subscribedChannel = conversationId;

    // Wait until Echo is ready
    const waitForEcho = setInterval(() => {
        if (!echoInstance) return;

        clearInterval(waitForEcho);

        try {
            // Subscribe to the private channel using the correct channel name from MessageSent event
            echoInstance.private(`chat.conversation.${conversationId}`)
                .listen('.message.sent', (e) => {
                    console.log('Message received via Echo:', e);
                    // Received a new message — refresh messages if same conversation
                    if (conversationId === window.currentConversationId) {
                        // Append new message to chat area
                        refreshMessages();
                        // Don't call loadConversations() here - it causes fade-in on every message
                        // The 60-second interval will update the conversation list periodically
                    }
                });
        } catch (e) {
            console.warn('Failed to subscribe to conversation channel', e);
        }
    }, 200);
}

// Subscribe to community announcement channels
let subscribedCommunityChannels = [];

function subscribeToCommunityAnnouncements(communityIdOrArray) {
    if (!echoInstance) initEcho();
    
    // Handle both single ID and array of communities
    let communityIds = [];
    if (typeof communityIdOrArray === 'number') {
        communityIds = [communityIdOrArray];
    } else if (Array.isArray(communityIdOrArray)) {
        communityIds = communityIdOrArray.map(c => c.id).filter(id => id);
    } else {
        return;
    }
    
    if (communityIds.length === 0) return;

    // Wait until Echo is ready
    const waitForEcho = setInterval(() => {
        if (!echoInstance) return;
        clearInterval(waitForEcho);

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
                                        container.innerHTML = renderAnnouncements(announcementsData.announcements, community.color || themeColors.gradientStart);
                                        
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
            'urgent': { color: themeColors.danger, icon: 'bx-error-circle', label: 'URGENT' },
            'warning': { color: themeColors.warning, icon: 'bx-error', label: 'Warning' },
            'info': { color: themeColors.info, icon: 'bx-info-circle', label: 'Info' },
            'normal': { color: themeColors.surface500, icon: 'bx-info-circle', label: 'Normal' }
        };
        
        const priority = announcement.priority || 'normal';
        const priorityInfo = priorityData[priority];
        
        // Find community color
        const community = globalCommunities.find(c => c.id === communityId);
        const communityColor = community?.color || themeColors.gradientStart;
        
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
        'urgent': { color: themeColors.danger, icon: 'bx-error-circle', label: 'URGENT' },
        'warning': { color: themeColors.warning, icon: 'bx-error', label: 'Warning' },
        'info': { color: themeColors.info, icon: 'bx-info-circle', label: 'Info' },
        'normal': { color: themeColors.surface500, icon: 'bx-info-circle', label: 'Normal' }
    };
    
    const priority = latest.priority || 'normal';
    const priorityInfo = priorityData[priority];
    
    document.getElementById('popupCommunityName').textContent = latest.community_name || 'Community';
    document.getElementById('popupCommunityIcon').style.background = latest.community_color || themeColors.gradientStart;
    
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

// Message input action button styles
if (!document.getElementById('chatInputStyles')) {
    const style = document.createElement('style');
    style.id = 'chatInputStyles';
    style.textContent = `
        .message-input-actions button {
            background: transparent;
            border: none;
            padding: 8px;
            font-size: 20px;
            color: ${themeColors.surfaceMuted};
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .message-input-actions button:hover {
            background: ${themeColors.printBgAlt};
            color: ${themeColors.chartPrimary};
        }
    `;
    document.head.appendChild(style);
}

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
                    `<img src="${s.avatar || 'https://via.placeholder.com/24'}" alt="${s.name}" class="rounded-circle">
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
            listEl.innerHTML = `<div style="padding: 12px; text-align: center; color: ${themeColors.surfaceMuted}; font-size: 0.85rem;">No communities</div>`;
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
                <div class="community-avatar" style="${community.avatar ? '' : 'background: ' + (community.color || themeColors.gradientStart) + ';'} width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
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
            display.innerHTML = `<p style="color: ${themeColors.surfaceMuted}; font-size: 13px;">No members added yet</p>`;
            return;
        }
        
        display.innerHTML = window.communityMembersToAdd.map(member => `
            <div style="background: var(--bs-light); padding: 6px 12px; border-radius: 20px; display: flex; align-items: center; gap: 8px; font-size: 13px;">
                <span>${member.name}</span>
                <button type="button" onclick="removeMemberFromCommunity(${member.id})" style="background: none; border: none; color: ${themeColors.surface500}; cursor: pointer; padding: 0; font-size: 16px;">&times;</button>
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
                    colorPreview.style.background = themeColors.gradientStart;
                }
                const colorInput = document.getElementById('communityColor');
                if (colorInput) {
                    colorInput.value = themeColors.gradientStart;
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
    document.getElementById('editCommunityColor').value = community.color || themeColors.gradientStart;
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

// ===== MENTION NOTIFICATIONS =====

// Send desktop notification for mentions
function sendMentionNotification(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const notification = new Notification(title, {
            body: body,
            icon: '/images/logo-icon.png'
        });
        
        notification.onclick = function() {
            window.focus();
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
                message.message.substring(0, 100) + (message.message.length > 100 ? '...' : '')
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
