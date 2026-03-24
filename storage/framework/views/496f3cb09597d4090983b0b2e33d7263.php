<?php $__env->startSection('title', 'Notepad'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ── Notepad shell layout ── */
.container-fluid:has(> #npShell) { padding: 0 !important; overflow: hidden !important; }
#page-content { overflow: hidden !important; }

/* ── Shell ── */
#npShell {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 62px);
    background: var(--bg-secondary);
    color: var(--text-primary);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    overflow: hidden;
    position: relative;
}

/* ── Menu Bar ── */
.np-menubar {
    display: flex;
    align-items: center;
    height: 34px;
    min-height: 34px;
    padding: 0 0.5rem;
    background: var(--bg-card, var(--bg-primary, #fff));
    border-bottom: 1px solid var(--border-color);
    user-select: none;
    z-index: 20;
    flex-shrink: 0;
}
.np-menu-item {
    position: relative;
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 0.75rem;
    font-size: 0.78rem;
    cursor: pointer;
    color: var(--text-secondary);
    border-radius: 3px;
    transition: background .12s, color .12s;
}
.np-menu-item:hover,
.np-menu-item.open {
    background: rgba(212,175,55,.18);
    color: var(--gold);
}
.np-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    background: var(--bg-card, var(--bg-primary, #fff));
    border: 1px solid var(--border-color);
    border-radius: 5px;
    min-width: 190px;
    box-shadow: 0 4px 24px rgba(0,0,0,.12), var(--shadow-md, 0 8px 28px rgba(0,0,0,.10));
    z-index: 200;
    padding: 4px 0;
}
.np-menu-item.open .np-dropdown { display: block; }
.np-dd-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.4rem 1rem;
    font-size: 0.77rem;
    cursor: pointer;
    color: var(--text-secondary);
    gap: 1.5rem;
    transition: background .1s, color .1s;
}
.np-dd-item:hover { background: rgba(212,175,55,.1); color: var(--gold); }
.np-dd-item.danger:hover { background: rgba(220,53,69,.12); color: #f46a6a; }
.np-dd-sep { height: 1px; background: var(--border-color); margin: 3px 0; }
.np-dd-kbd { font-size: 0.67rem; opacity: .5; }

/* Title area in menubar */
.np-title-area {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.np-title-display {
    font-size: 0.77rem;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 320px;
}
.np-title-display.unsaved::after {
    content: ' \25CF';
    color: var(--gold);
    opacity: 1;
}
.np-save-status {
    font-size: 0.68rem;
    min-width: 70px;
    text-align: right;
    white-space: nowrap;
    margin-right: 0.4rem;
    color: transparent;
}
.np-save-status.saving { color: var(--gold); }
.np-save-status.saved  { color: #34c38f; }
.np-save-status.error  { color: #f46a6a; }
.np-save-status.synced { color: #5b73e8; }
.np-save-status.conflict { color: #f1b44c; font-weight: 600; }

/* ── Main area ── */
.np-main {
    display: flex;
    flex: 1;
    overflow: hidden;
    min-height: 0;
}

/* ── Sidebar ── */
.np-sidebar {
    width: 230px;
    min-width: 230px;
    display: flex;
    flex-direction: column;
    background: var(--bg-card, var(--bg-primary, #fff));
    border-right: 1px solid var(--border-color);
    overflow: hidden;
    flex-shrink: 0;
}
.np-sidebar-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.45rem 0.7rem;
    border-bottom: 1px solid var(--border-color);
    min-height: 38px;
    flex-shrink: 0;
}
.np-sidebar-hdr > span {
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--text-muted);
    opacity: .7;
}
.btn-np-new {
    background: rgba(212,175,55,.12);
    border: 1px solid rgba(212,175,55,.3);
    color: var(--gold);
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background .12s;
    white-space: nowrap;
}
.btn-np-new:hover { background: rgba(212,175,55,.22); }

.np-search-wrap { padding: 0.4rem 0.5rem; border-bottom: 1px solid var(--border-color); flex-shrink: 0; }
.np-search-wrap input {
    width: 100%;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--text-primary);
    font-size: 0.73rem;
    padding: 0.26rem 0.5rem;
    outline: none;
}
.np-search-wrap input::placeholder { color: var(--text-muted); opacity: .6; }
.np-search-wrap input:focus { border-color: rgba(212,175,55,.5); }

.np-file-list { flex: 1; overflow-y: auto; overflow-x: hidden; }
.np-file-list::-webkit-scrollbar { width: 4px; }
.np-file-list::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 2px; }

.np-section-hdr {
    padding: 0.28rem 0.7rem;
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    opacity: .65;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    margin-top: 2px;
}
.np-file-item {
    display: flex;
    align-items: flex-start;
    gap: 0.45rem;
    padding: 0.5rem 0.7rem;
    cursor: pointer;
    border-bottom: 1px solid var(--border-color);
    transition: background .1s;
    position: relative;
}
.np-file-item:hover { background: var(--bg-tertiary); }
.np-file-item.active {
    background: rgba(212,175,55,.08);
    border-left: 3px solid var(--gold);
    padding-left: calc(0.7rem - 3px);
}
.np-fi-icon { font-size: 0.9rem; color: var(--gold); opacity: .65; margin-top: 2px; flex-shrink: 0; }
.np-file-item.shared-other .np-fi-icon { color: #34c38f; }
.np-fi-body { flex: 1; min-width: 0; }
.np-fi-title {
    font-size: 0.76rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--text-primary);
}
.np-fi-preview {
    font-size: 0.67rem;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}
.np-fi-time {
    font-size: 0.6rem;
    color: var(--text-muted);
    opacity: .55;
    flex-shrink: 0;
    margin-top: 2px;
}
.np-fi-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--gold);
    position: absolute;
    top: 0.55rem;
    right: 0.5rem;
    display: none;
}
.np-file-item.unsaved .np-fi-dot { display: block; }
.np-fi-shared-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
    font-size: 0.58rem;
    color: var(--gold);
    opacity: .65;
    margin-top: 2px;
}
.np-empty-list {
    text-align: center;
    padding: 2.5rem 1rem;
    font-size: 0.73rem;
    color: var(--text-muted);
    opacity: .5;
    line-height: 1.8;
}

/* ── Resize handle ── */
.np-sidebar-resize {
    width: 4px;
    cursor: col-resize;
    background: transparent;
    flex-shrink: 0;
    transition: background .15s;
}
.np-sidebar-resize:hover,
.np-sidebar-resize.dragging { background: rgba(212,175,55,.3); }

/* ── Editor pane ── */
.np-editor-pane { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

.np-editor-titlebar {
    display: flex;
    align-items: center;
    padding: 0 0.8rem;
    background: var(--bg-card, var(--bg-primary, #fff));
    border-bottom: 1px solid var(--border-color);
    min-height: 42px;
    flex-shrink: 0;
    gap: 0.5rem;
}
.np-editor-titlebar input[type=text] {
    flex: 1;
    background: transparent;
    border: none;
    color: var(--text-primary);
    font-size: 0.9rem;
    font-weight: 600;
    outline: none;
    min-width: 0;
}
.np-editor-titlebar input[type=text]::placeholder { color: var(--text-muted); opacity: .5; font-weight: 400; }
.np-editor-titlebar input[type=text][readonly] { cursor: default; opacity: .65; }

/* Title bar action buttons */
.np-tbar-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: var(--bg-tertiary, var(--bg-secondary, #f3f4f6));
    border: 1px solid var(--border-color);
    border-radius: 5px;
    color: var(--text-secondary);
    font-size: 0.72rem;
    padding: 0.22rem 0.6rem;
    cursor: pointer;
    transition: background .12s, border-color .12s, color .12s;
    white-space: nowrap;
    flex-shrink: 0;
}
.np-tbar-btn:hover {
    background: var(--bg-secondary, #f8f9fa);
    border-color: rgba(212,175,55,.4);
    color: var(--text-primary);
}
.np-tbar-btn.btn-share.is-shared {
    background: rgba(212,175,55,.12);
    border-color: rgba(212,175,55,.35);
    color: var(--gold);
}
.np-tbar-btn.btn-delete:hover { background: rgba(220,53,69,.1); border-color: rgba(220,53,69,.3); color: #f46a6a; }
.np-owner-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.68rem;
    color: #34c38f;
    background: rgba(52,195,143,.1);
    border: 1px solid rgba(52,195,143,.2);
    border-radius: 4px;
    padding: 0.18rem 0.5rem;
    flex-shrink: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 160px;
}

/* ── Textarea ── */
.np-textarea {
    flex: 1;
    width: 100%;
    background: var(--bg-primary);
    border: none;
    color: var(--text-primary);
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.7;
    padding: 1rem 1.2rem;
    resize: none;
    outline: none;
    tab-size: 4;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-word;
    min-height: 0;
}
.np-textarea::placeholder { color: var(--text-muted); opacity: .4; }
.np-textarea::selection { background: rgba(212,175,55,.22); }
.np-textarea::-webkit-scrollbar { width: 7px; }
.np-textarea::-webkit-scrollbar-track { background: transparent; }
.np-textarea::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 3px; }
.np-textarea.nowrap { white-space: pre; word-break: normal; overflow-x: auto; }
.np-textarea[readonly] { cursor: default; background: var(--bg-secondary); }

.np-no-note {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 0.7rem;
    opacity: .2;
    pointer-events: none;
}
.np-no-note i { font-size: 4rem; color: var(--text-muted); }
.np-no-note p { font-size: 0.8rem; margin: 0; color: var(--text-muted); }

/* ── Status bar ── */
.np-statusbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 0.8rem;
    height: 22px;
    min-height: 22px;
    background: var(--bg-card, var(--bg-primary, #fff));
    border-top: 1px solid var(--border-color);
    font-size: 0.65rem;
    color: var(--text-muted);
    user-select: none;
    flex-shrink: 0;
}
.np-sb-left, .np-sb-right { display: flex; gap: 1.2rem; }

/* ── Share / Font Modals ── */
.np-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 3000;
    align-items: center;
    justify-content: center;
}
.np-overlay.show { display: flex; }
.np-modal {
    background: var(--bg-card, var(--bg-primary, #fff));
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1.3rem;
    width: 340px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 32px rgba(0,0,0,.15), var(--shadow-md, 0 4px 16px rgba(0,0,0,.10));
}
.np-modal h6 {
    margin: 0 0 0.2rem;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.np-modal-sub {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-bottom: 0.9rem;
}
.np-modal-search {
    width: 100%;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--text-primary);
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
    outline: none;
    margin-bottom: 0.6rem;
}
.np-modal-search:focus { border-color: rgba(212,175,55,.5); }
.np-modal-search::placeholder { color: var(--text-muted); opacity: .5; }
.np-user-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    margin: 0 -0.1rem;
    max-height: 42vh;
}
.np-user-list::-webkit-scrollbar { width: 4px; }
.np-user-list::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 2px; }
.np-user-row {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.42rem 0.5rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background .1s;
}
.np-user-row:hover { background: var(--bg-tertiary); }
.np-user-row input[type=checkbox] {
    width: 15px; height: 15px;
    accent-color: var(--gold);
    cursor: pointer;
    flex-shrink: 0;
}
.np-user-row label {
    font-size: 0.77rem;
    color: var(--text-primary);
    cursor: pointer;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}
.np-modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.9rem;
    padding-top: 0.7rem;
    border-top: 1px solid var(--border-color);
    gap: 0.5rem;
}
.np-modal-footer .np-share-count {
    font-size: 0.7rem;
    color: var(--text-muted);
}
.np-modal-footer-btns { display: flex; gap: 0.5rem; }
.np-btn {
    font-size: 0.75rem;
    padding: 0.3rem 0.85rem;
    border-radius: 4px;
    cursor: pointer;
    border: 1px solid var(--border-color);
    background: var(--bg-secondary);
    color: var(--text-secondary);
    transition: background .12s;
}
.np-btn:hover { background: var(--bg-tertiary); }
.np-btn.primary {
    background: linear-gradient(135deg, #d4af37, #b8941f);
    border-color: transparent;
    color: #fff;
    font-weight: 600;
}
.np-btn.primary:hover { opacity: .9; }

/* Font modal */
.np-modal-sm { width: 280px; max-height: unset; }
.np-modal-sm label { font-size: 0.73rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem; }
.np-modal-sm select,
.np-modal-sm input[type=number] {
    width: 100%;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--text-primary);
    font-size: 0.8rem;
    padding: 0.38rem 0.6rem;
    margin-bottom: 0.75rem;
    outline: none;
}
.np-modal-sm select:focus,
.np-modal-sm input:focus { border-color: rgba(212,175,55,.5); }

/* inline rename */
input.np-rename-input {
    background: var(--bg-tertiary);
    border: 1px solid rgba(212,175,55,.45);
    border-radius: 2px;
    padding: 0 4px;
    width: 100%;
    font-size: 0.76rem;
    font-weight: 600;
    color: var(--text-primary);
    outline: none;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="npShell">

    
    <div class="np-menubar">
        <div class="np-menu-item" id="menuFile" onclick="nmToggle('menuFile')">
            File
            <div class="np-dropdown">
                <div class="np-dd-item" onclick="newNote()"><span>New</span><span class="np-dd-kbd">Ctrl+N</span></div>
                <div class="np-dd-sep"></div>
                <div class="np-dd-item" onclick="saveActive()"><span>Save</span><span class="np-dd-kbd">Ctrl+S</span></div>
                <div class="np-dd-item" onclick="downloadNote()"><span>Save as .txt</span></div>
            </div>
        </div>
        <div class="np-menu-item" id="menuFormat" onclick="nmToggle('menuFormat')">
            Format
            <div class="np-dropdown">
                <div class="np-dd-item" onclick="toggleWordWrap()"><span id="wrapLabel">&#10003;&nbsp; Word Wrap</span></div>
                <div class="np-dd-sep"></div>
                <div class="np-dd-item" onclick="openModal('fontModal')"><span>Font &amp; Size&#8230;</span></div>
            </div>
        </div>
        <div class="np-menu-item" id="menuView" onclick="nmToggle('menuView')">
            View
            <div class="np-dropdown">
                <div class="np-dd-item" onclick="toggleSidebar()"><span id="sidebarLabel">&#10003;&nbsp; Show Files</span></div>
                <div class="np-dd-sep"></div>
                <div class="np-dd-item" onclick="changeZoom(1)"><span>Zoom In</span><span class="np-dd-kbd">Ctrl++</span></div>
                <div class="np-dd-item" onclick="changeZoom(-1)"><span>Zoom Out</span><span class="np-dd-kbd">Ctrl+&#8722;</span></div>
                <div class="np-dd-item" onclick="resetZoom()"><span>Reset Zoom</span><span class="np-dd-kbd">Ctrl+0</span></div>
            </div>
        </div>

        <div class="np-title-area">
            <span class="np-title-display" id="npTitleDisplay">— No file open —</span>
        </div>
        <span class="np-save-status" id="npSaveStatus"></span>
    </div>

    
    <div class="np-main">
        
        <div class="np-sidebar" id="npSidebar">
            <div class="np-sidebar-hdr">
                <span>Files</span>
                <button class="btn-np-new" onclick="newNote()">+ New</button>
            </div>
            <div class="np-search-wrap">
                <input type="text" id="npSideSearch" placeholder="Search files…" oninput="filterFileList()">
            </div>
            <div class="np-file-list" id="npFileList">
                <div class="np-empty-list">No notes yet</div>
            </div>
        </div>

        <div class="np-sidebar-resize" id="npResize"></div>

        
        <div class="np-editor-pane">
            <div class="np-editor-titlebar" id="npTitleBar" style="display:none">
                <input type="text" id="npTitleInput" placeholder="Untitled" maxlength="255"
                       oninput="onTitleChange()"
                       onkeydown="if(event.key==='Enter')document.getElementById('npEditor').focus()">
                
                <button class="np-tbar-btn btn-share" id="npShareBtn" onclick="openShareModal()" style="display:none" title="Share with specific users">
                    <i class="bx bx-user-plus" id="npShareIcon"></i>
                    <span id="npShareLabel">Share</span>
                </button>
                
                <span class="np-owner-badge" id="npOwnerBadge" style="display:none">
                    <i class="bx bx-user"></i> <span id="npOwnerName"></span>
                </span>
                
                <button class="np-tbar-btn btn-delete" id="npDeleteBtn" onclick="deleteActive()" style="display:none" title="Delete this note">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </div>
            <textarea class="np-textarea" id="npEditor"
                      placeholder="Start typing…"
                      oninput="onEditorChange()"
                      onkeydown="handleEditorKey(event)"
                      style="display:none"></textarea>
            <div class="np-no-note" id="npNoNote">
                <i class="bx bx-notepad"></i>
                <p>Open a file or create a new one</p>
            </div>
        </div>
    </div>

    
    <div class="np-statusbar">
        <div class="np-sb-left">
            <span id="sbLine">Ln 1, Col 1</span>
            <span id="sbSel" style="display:none">Sel: <span id="sbSelCount">0</span></span>
        </div>
        <div class="np-sb-right">
            <span id="sbWords">0 words</span>
            <span id="sbChars">0 chars</span>
            <span id="sbZoom">100%</span>
            <span>UTF-8</span>
        </div>
    </div>
</div>


<div class="np-overlay" id="shareModal">
    <div class="np-modal">
        <h6><i class="bx bx-share-alt"></i> Share Note</h6>
        <p class="np-modal-sub">Choose who can view &amp; read this note</p>
        <input type="text" class="np-modal-search" id="shareUserSearch" placeholder="Search users…" oninput="filterShareUsers()">
        <div class="np-user-list" id="shareUserList">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $shareableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $su): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="np-user-row" data-uid="<?php echo e($su->id); ?>" data-name="<?php echo e(strtolower($su->name)); ?>" onclick="toggleShareUser(this)">
                <input type="checkbox" id="su<?php echo e($su->id); ?>" value="<?php echo e($su->id); ?>" onclick="event.stopPropagation()">
                <label for="su<?php echo e($su->id); ?>"><?php echo e($su->name); ?></label>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shareableUsers->isEmpty()): ?>
            <div style="text-align:center;padding:1.5rem;opacity:.3;font-size:.75rem">No other users found</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="np-modal-footer">
            <span class="np-share-count" id="shareCount">0 selected</span>
            <div class="np-modal-footer-btns">
                <button class="np-btn" onclick="closeModal('shareModal')">Cancel</button>
                <button class="np-btn primary" onclick="saveShares()">Save</button>
            </div>
        </div>
    </div>
</div>


<div class="np-overlay" id="fontModal">
    <div class="np-modal np-modal-sm">
        <h6><i class="bx bx-font"></i> Font &amp; Size</h6>
        <div style="margin-top:.75rem">
            <label>Font Family</label>
            <select id="fontFamily">
                <option value="'Consolas','Courier New',monospace">Consolas (default)</option>
                <option value="'Courier New',monospace">Courier New</option>
                <option value="'Fira Code',monospace">Fira Code</option>
                <option value="-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif">Segoe UI</option>
                <option value="Georgia,serif">Georgia</option>
            </select>
            <label>Font Size (px)</label>
            <input type="number" id="fontSize" min="8" max="48" value="14">
        </div>
        <div class="np-modal-footer">
            <span></span>
            <div class="np-modal-footer-btns">
                <button class="np-btn" onclick="closeModal('fontModal')">Cancel</button>
                <button class="np-btn primary" onclick="applyFont()">Apply</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
const CSRF = '<?php echo e(csrf_token()); ?>';

let notes         = [];
let activeId      = null;
let dirty         = false;
let autoSaveTimer = null;
let wordWrap      = true;
let fontSize      = 14;
let fontFamily    = "'Consolas','Courier New',monospace";
let zoomLevel     = 100;
let pollTimer     = null;
let lastSavedAt   = null;   // ISO timestamp of last successful save/poll
let savingInFlight = false;  // prevent overlapping save requests
const POLL_INTERVAL = 4000;  // poll shared notes every 4s

const gel = id => document.getElementById(id);
const npEditor       = gel('npEditor');
const npTitleInput   = gel('npTitleInput');
const npTitleBar     = gel('npTitleBar');
const npNoNote       = gel('npNoNote');
const npFileList     = gel('npFileList');
const npTitleDisplay = gel('npTitleDisplay');
const npSaveStatus   = gel('npSaveStatus');
const sbLine         = gel('sbLine');
const sbSel          = gel('sbSel');
const sbSelCount     = gel('sbSelCount');
const sbWords        = gel('sbWords');
const sbChars        = gel('sbChars');
const sbZoom         = gel('sbZoom');

/* ── Boot ── */
window.addEventListener('DOMContentLoaded', () => {
    // Strip outer padding for full-screen layout
    const cf = document.querySelector('.container-fluid');
    if (cf) { cf.style.padding = '0'; cf.style.overflow = 'hidden'; }
    const pc = document.getElementById('page-content');
    if (pc) pc.style.overflow = 'hidden';

    /* Seed from server */
    <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    notes.push({
        id:             <?php echo e($n->id); ?>,
        title:          <?php echo json_encode($n->title ?? 'Untitled'); ?>,
        content:        <?php echo json_encode($n->content ?? ''); ?>,
        is_shared:      <?php echo e($n->is_shared ? 'true' : 'false'); ?>,
        shared_with:    [<?php echo e($n->sharedWith->pluck('id')->join(',')); ?>],
        is_mine:        true,
        updated_at:     <?php echo json_encode($n->updated_at->toIso8601String()); ?>,
        updated_ago:    <?php echo json_encode($n->updated_at->diffForHumans()); ?>,
    });
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php $__currentLoopData = $sharedNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    notes.push({
        id:             <?php echo e($n->id); ?>,
        title:          <?php echo json_encode($n->title ?? 'Untitled'); ?>,
        content:        <?php echo json_encode($n->content ?? ''); ?>,
        is_shared:      true,
        shared_with:    [],
        is_mine:        false,
        can_edit:       true,
        owner:          <?php echo json_encode($n->user?->name ?? 'Someone'); ?>,
        updated_at:     <?php echo json_encode($n->updated_at->toIso8601String()); ?>,
        updated_ago:    <?php echo json_encode($n->updated_at->diffForHumans()); ?>,
    });
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    renderFileList();
    const myFirst = notes.find(n => n.is_mine);
    if (myFirst) openNote(myFirst.id);
    else if (notes.length) openNote(notes[0].id);

    setInterval(autoSave, 5000);

    /* Start polling for shared note updates */
    pollTimer = setInterval(pollActiveNote, POLL_INTERVAL);

    /* Restore localStorage backups on first load */
    restoreFromLocalBackup();

    /* Restore prefs */
    try {
        const pf = localStorage.getItem('np_font');
        if (pf) { const p = JSON.parse(pf); fontSize = p.size || 14; fontFamily = p.family || fontFamily; }
    } catch(e) {}
    wordWrap   = localStorage.getItem('np_wrap') !== '0';
    zoomLevel  = parseInt(localStorage.getItem('np_zoom') || '100') || 100;

    applyFontToEditor(); applyWordWrap(); applyZoom();
    setupResize(); setupClickOutside();
    updateStatusBar();
});

/* ══ FILE LIST ══ */
function renderFileList(filter) {
    filter = (filter || '').toLowerCase();
    const match = n =>
        !filter ||
        (n.title||'').toLowerCase().includes(filter) ||
        (n.content||'').toLowerCase().includes(filter);
    const mine  = notes.filter(n => n.is_mine && match(n));
    const theirs = notes.filter(n => !n.is_mine && match(n));

    if (!notes.length) {
        npFileList.innerHTML = '<div class="np-empty-list">No notes yet.<br>Click "+ New" to start.</div>';
        return;
    }
    if (!mine.length && !theirs.length) {
        npFileList.innerHTML = '<div class="np-empty-list">No match</div>';
        return;
    }

    const item = (n, isOther) => {
        const preview = (n.content||'').replace(/\n/g,' ').trim().slice(0,55);
        const active  = n.id === activeId ? 'active' : '';
        const unsaved = n.id === activeId && dirty ? 'unsaved' : '';
        const cls     = isOther ? 'shared-other' : '';
        const icon    = isOther ? 'bx-user-voice' : 'bx-file-blank';
        const dbl     = n.is_mine ? `ondblclick="startRename(${n.id})"` : '';
        const shareBadge = n.is_mine && n.is_shared
            ? `<div class="np-fi-shared-badge"><i class="bx bx-share-alt"></i> Shared</div>` : '';
        return `<div class="np-file-item ${active} ${unsaved} ${cls}" id="fi-${n.id}"
                     onclick="openNote(${n.id})" ${dbl}>
            <i class="bx ${icon} np-fi-icon"></i>
            <div class="np-fi-body">
                <div class="np-fi-title" id="fit-${n.id}">${esc(n.title||'Untitled')}</div>
                ${preview ? `<div class="np-fi-preview">${esc(preview)}</div>` : ''}
                ${shareBadge}
            </div>
            <span class="np-fi-time">${esc(n.updated_ago||'')}</span>
            <span class="np-fi-dot"></span>
        </div>`;
    };

    let html = '';
    if (mine.length)  { html += '<div class="np-section-hdr">My Files</div>'; html += mine.map(n => item(n, false)).join(''); }
    if (theirs.length){ html += '<div class="np-section-hdr" style="margin-top:4px">Shared with Me</div>'; html += theirs.map(n => item(n, true)).join(''); }
    npFileList.innerHTML = html;
}
function filterFileList() { renderFileList(gel('npSideSearch').value); }

/* ══ OPEN ══ */
async function openNote(id) {
    if (activeId && dirty) await saveNote(activeId, false);
    activeId = id; dirty = false;
    const note = notes.find(n => n.id === id);
    if (!note) return;

    npTitleInput.value        = note.title || '';
    npEditor.value            = note.content || '';
    npTitleBar.style.display  = 'flex';
    npEditor.style.display    = '';
    npNoNote.style.display    = 'none';

    const ro = !note.is_mine && !note.can_edit;
    npEditor.readOnly      = ro;
    npTitleInput.readOnly  = ro;
    npEditor.style.cursor  = ro ? 'default' : '';

    const shareBtn   = gel('npShareBtn');
    const ownerBadge = gel('npOwnerBadge');
    const deleteBtn  = gel('npDeleteBtn');

    if (note.is_mine) {
        shareBtn.style.display  = 'inline-flex';
        deleteBtn.style.display = 'inline-flex';
        ownerBadge.style.display = 'none';
        const cnt = note.shared_with ? note.shared_with.length : 0;
        shareBtn.classList.toggle('is-shared', cnt > 0);
        gel('npShareIcon').className = cnt > 0 ? 'bx bxs-user-check' : 'bx bx-user-plus';
        gel('npShareLabel').textContent = cnt > 0 ? `Shared (${cnt})` : 'Share';
    } else {
        shareBtn.style.display  = 'none';
        deleteBtn.style.display = 'none';
        ownerBadge.style.display = 'inline-flex';
        gel('npOwnerName').textContent = note.owner || 'Someone';
    }

    updateTitleDisplay(); renderFileList(gel('npSideSearch').value);
    updateStatusBar(); setSaveStatus(''); setDirty(false);
    if (!ro) npEditor.focus();
    // Track the timestamp of the note we just opened for polling comparison
    lastSavedAt = note.updated_at || null;
}

/* ══ NEW ══ */
async function newNote() {
    nmCloseAll();
    try {
        setSaveStatus('saving');
        const res  = await fetch('/notepad', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ title: 'Untitled', content: '' }),
        });
        const data = await res.json();
        if (data.success) {
            notes.unshift({ id: data.note.id, title: 'Untitled', content: '', is_shared: false, shared_with: [], is_mine: true, updated_at: data.note.updated_at, updated_ago: data.note.updated_ago });
            renderFileList();
            await openNote(data.note.id);
            setTimeout(() => { npTitleInput.focus(); npTitleInput.select(); }, 50);
            setSaveStatus('');
        }
    } catch(e) { setSaveStatus('error'); }
}

/* ══ SAVE ══ */
async function saveNote(id, showStatus, retryCount) {
    if (!id || savingInFlight) return;
    const note = notes.find(n => n.id === id);
    if (!note || (!note.is_mine && !note.can_edit)) return;
    savingInFlight = true;
    if (showStatus) setSaveStatus('saving');
    try {
        const res  = await fetch(`/notepad/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ title: note.title, content: note.content }),
        });
        const data = await res.json();
        if (data.success) {
            note.updated_ago = data.note.updated_ago;
            if (data.note.updated_at) note.updated_at = data.note.updated_at;
            lastSavedAt = note.updated_at;
            // Clear localStorage backup after successful save
            clearLocalBackup(id);
            if (id === activeId) { setDirty(false); if (showStatus) setSaveStatus('saved', 2500); }
            renderFileList(gel('npSideSearch').value);
        } else {
            // Save to localStorage as fallback
            saveLocalBackup(id, note);
            if (showStatus) setSaveStatus('error');
        }
    } catch(e) {
        // Network error — save to localStorage and retry once
        saveLocalBackup(id, note);
        if (showStatus) setSaveStatus('error');
        const retry = retryCount || 0;
        if (retry < 2) {
            setTimeout(() => { savingInFlight = false; saveNote(id, showStatus, retry + 1); }, 3000);
            return;
        }
    } finally {
        savingInFlight = false;
    }
}
function saveActive() { mnCloseAll(); if (activeId) saveNote(activeId, true); }
function autoSave()   { if (activeId && dirty) saveNote(activeId, true); }
// alias
function mnCloseAll() { nmCloseAll(); }

/* ══ DELETE ══ */
async function deleteActive() {
    if (!activeId) return;
    const note = notes.find(n => n.id === activeId);
    if (!note || !note.is_mine) return;
    if (!confirm(`Delete "${note.title || 'Untitled'}"? This cannot be undone.`)) return;
    try {
        const res  = await fetch(`/notepad/${activeId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            notes = notes.filter(n => n.id !== activeId);
            activeId = null; dirty = false;
            npTitleBar.style.display = 'none';
            npEditor.style.display   = 'none';
            npNoNote.style.display   = '';
            updateTitleDisplay(); renderFileList(); setSaveStatus('');
            const next = notes.find(n => n.is_mine);
            if (next) openNote(next.id);
            else if (notes.length) openNote(notes[0].id);
        }
    } catch(e) { alert('Delete failed.'); }
}

/* ══ SHARE MODAL ══ */
function openShareModal() {
    if (!activeId) return;
    const note = notes.find(n => n.id === activeId);
    if (!note || !note.is_mine) return;
    // Pre-check already-shared users
    const shared = note.shared_with || [];
    document.querySelectorAll('#shareUserList .np-user-row').forEach(row => {
        const uid = parseInt(row.dataset.uid);
        const cb  = row.querySelector('input[type=checkbox]');
        if (cb) cb.checked = shared.includes(uid);
        row.style.display = '';
    });
    gel('shareUserSearch').value = '';
    updateShareCount();
    openModal('shareModal');
}

function toggleShareUser(row) {
    const cb = row.querySelector('input[type=checkbox]');
    if (cb) cb.checked = !cb.checked;
    updateShareCount();
}

function filterShareUsers() {
    const q = gel('shareUserSearch').value.toLowerCase();
    document.querySelectorAll('#shareUserList .np-user-row').forEach(row => {
        row.style.display = row.dataset.name.includes(q) ? '' : 'none';
    });
}

function updateShareCount() {
    const cnt = document.querySelectorAll('#shareUserList input[type=checkbox]:checked').length;
    gel('shareCount').textContent = cnt === 0 ? 'Not shared' : `${cnt} user${cnt !== 1 ? 's' : ''} selected`;
}

async function saveShares() {
    if (!activeId) return;
    const note = notes.find(n => n.id === activeId);
    if (!note || !note.is_mine) return;
    const ids = [...document.querySelectorAll('#shareUserList input[type=checkbox]:checked')]
        .map(cb => parseInt(cb.value));
    try {
        setSaveStatus('saving');
        const res  = await fetch(`/notepad/${activeId}/shares`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ user_ids: ids }),
        });
        const data = await res.json();
        if (data.success) {
            note.shared_with = data.shared_user_ids;
            note.is_shared   = data.is_shared;
            const cnt = data.shared_user_ids.length;
            const shareBtn = gel('npShareBtn');
            shareBtn.classList.toggle('is-shared', cnt > 0);
            gel('npShareIcon').className = cnt > 0 ? 'bx bxs-user-check' : 'bx bx-user-plus';
            gel('npShareLabel').textContent = cnt > 0 ? `Shared (${cnt})` : 'Share';
            renderFileList(gel('npSideSearch').value);
            setSaveStatus('saved', 2000);
            closeModal('shareModal');
        } else { setSaveStatus('error'); }
    } catch(e) { setSaveStatus('error'); }
}

/* ══ EDITOR INPUT ══ */
function onEditorChange() {
    if (!activeId) return;
    const note = notes.find(n => n.id === activeId);
    if (!note || (!note.is_mine && !note.can_edit)) return;
    note.content = npEditor.value;
    setDirty(true);
    // Backup to localStorage on every change for data safety
    saveLocalBackup(activeId, note);
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => { if (activeId && dirty) saveNote(activeId, true); }, 5000);
    updateStatusBar();
}
function onTitleChange() {
    if (!activeId) return;
    const note = notes.find(n => n.id === activeId);
    if (!note || (!note.is_mine && !note.can_edit)) return;
    note.title = npTitleInput.value;
    setDirty(true);
    updateTitleDisplay();
    const fit = gel(`fit-${activeId}`);
    if (fit && fit.tagName !== 'INPUT') fit.textContent = npTitleInput.value || 'Untitled';
}

/* ══ INLINE RENAME ══ */
function startRename(id) {
    if (id !== activeId) openNote(id);
    const fitEl = gel(`fit-${id}`);
    if (!fitEl || fitEl.tagName === 'INPUT') return;
    const note  = notes.find(n => n.id === id);
    const inp   = document.createElement('input');
    inp.className = 'np-rename-input';
    inp.type = 'text'; inp.value = note.title || ''; inp.maxLength = 255;
    fitEl.replaceWith(inp); inp.focus(); inp.select();
    const finish = async () => {
        const val = inp.value.trim() || 'Untitled';
        note.title = val;
        if (id === activeId) { npTitleInput.value = val; updateTitleDisplay(); }
        const span = document.createElement('div');
        span.className = 'np-fi-title'; span.id = `fit-${id}`; span.textContent = val;
        inp.replaceWith(span);
        setDirty(true); await saveNote(id, false);
        renderFileList(gel('npSideSearch').value);
    };
    inp.addEventListener('blur', finish);
    inp.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); inp.blur(); }
        if (e.key === 'Escape') {
            const sp = document.createElement('div');
            sp.className = 'np-fi-title'; sp.id = `fit-${id}`; sp.textContent = note.title || 'Untitled';
            inp.replaceWith(sp);
        }
    });
}

/* ══ DIRTY / STATUS ══ */
function setDirty(v) {
    dirty = v;
    npTitleDisplay.classList.toggle('unsaved', v && !!activeId);
    const fi = gel(`fi-${activeId}`);
    if (fi) fi.classList.toggle('unsaved', v);
}
function setSaveStatus(type, clearMs) {
    npSaveStatus.className = 'np-save-status';
    const map = { saving: 'Saving…', saved: 'Saved ✓', error: 'Save failed' };
    npSaveStatus.textContent = map[type] || '';
    if (type) npSaveStatus.classList.add(type);
    if (clearMs) setTimeout(() => { npSaveStatus.textContent = ''; npSaveStatus.className = 'np-save-status'; }, clearMs);
}
function updateTitleDisplay() {
    if (!activeId) { npTitleDisplay.textContent = '— No file open —'; npTitleDisplay.classList.remove('unsaved'); return; }
    const note = notes.find(n => n.id === activeId);
    npTitleDisplay.textContent = (note?.title || 'Untitled') + '.txt';
    npTitleDisplay.classList.toggle('unsaved', dirty);
}
function updateStatusBar() {
    const txt = npEditor.value || '', pos = npEditor.selectionStart || 0;
    const lines = txt.split('\n'); let row = 1, col = 1, counted = 0;
    for (let i = 0; i < lines.length; i++) {
        if (counted + lines[i].length >= pos) { row = i+1; col = pos - counted + 1; break; }
        counted += lines[i].length + 1;
    }
    sbLine.textContent = `Ln ${row}, Col ${col}`;
    const sel = (npEditor.selectionEnd||0) - (npEditor.selectionStart||0);
    sbSel.style.display = sel > 0 ? '' : 'none'; sbSelCount.textContent = sel;
    const words = txt.trim() ? txt.trim().split(/\s+/).length : 0;
    sbWords.textContent = `${words} word${words!==1?'s':''}`;
    sbChars.textContent = `${txt.length} char${txt.length!==1?'s':''}`;
}
npEditor.addEventListener('keyup', updateStatusBar);
npEditor.addEventListener('click', updateStatusBar);
npEditor.addEventListener('select', updateStatusBar);

/* ══ POLLING — real-time shared note sync ══ */
async function pollActiveNote() {
    if (!activeId || savingInFlight) return;
    const note = notes.find(n => n.id === activeId);
    if (!note) return;
    // Poll any note that is shared (owner's shared notes OR notes shared with me)
    const isSharedNote = note.is_shared || !note.is_mine;
    if (!isSharedNote) return;
    try {
        const res = await fetch(`/notepad/${activeId}/poll`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const remoteTime = new Date(data.updated_at).getTime();
        const localTime  = note.updated_at ? new Date(note.updated_at).getTime() : 0;

        // Only update if remote is newer than what we know
        if (remoteTime > localTime) {
            if (dirty) {
                // Local has unsaved changes — show conflict indicator, don't overwrite
                showSyncConflict();
            } else {
                // Safe to update — no local edits pending
                note.title   = data.title;
                note.content = data.content;
                note.updated_at  = data.updated_at;
                note.updated_ago = data.updated_ago;
                // Refresh editor if this note is currently open
                if (activeId === note.id) {
                    const cursorPos = npEditor.selectionStart;
                    npEditor.value     = note.content || '';
                    npTitleInput.value = note.title || '';
                    // Try to restore cursor position
                    npEditor.selectionStart = npEditor.selectionEnd = Math.min(cursorPos, npEditor.value.length);
                    updateTitleDisplay();
                    updateStatusBar();
                }
                renderFileList(gel('npSideSearch').value);
                showSyncUpdate();
            }
        }
    } catch(e) { /* network error — silently skip this poll cycle */ }
}
function showSyncUpdate() {
    npSaveStatus.className = 'np-save-status synced';
    npSaveStatus.textContent = 'Updated from server ↓';
    setTimeout(() => { npSaveStatus.textContent = ''; npSaveStatus.className = 'np-save-status'; }, 2500);
}
function showSyncConflict() {
    npSaveStatus.className = 'np-save-status conflict';
    npSaveStatus.textContent = '⚠ Remote change — save to keep yours';
    // Don't auto-clear — keep visible until next save
}

/* ══ LOCAL STORAGE BACKUP (data safety) ══ */
function saveLocalBackup(id, note) {
    try {
        const key = `np_backup_${id}`;
        localStorage.setItem(key, JSON.stringify({
            id:      note.id,
            title:   note.title,
            content: note.content,
            ts:      Date.now(),
        }));
    } catch(e) { /* localStorage full or unavailable */ }
}
function clearLocalBackup(id) {
    try { localStorage.removeItem(`np_backup_${id}`); } catch(e) {}
}
function restoreFromLocalBackup() {
    try {
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (!key || !key.startsWith('np_backup_')) continue;
            const backup = JSON.parse(localStorage.getItem(key));
            if (!backup || !backup.id) continue;
            const note = notes.find(n => n.id === backup.id);
            if (note) {
                const backupTime  = backup.ts || 0;
                const noteTime    = note.updated_at ? new Date(note.updated_at).getTime() : 0;
                // Only restore if backup is newer than server data
                if (backupTime > noteTime) {
                    note.title   = backup.title;
                    note.content = backup.content;
                    // Mark dirty so it gets auto-saved to server
                    if (activeId === note.id) {
                        npEditor.value     = note.content || '';
                        npTitleInput.value  = note.title || '';
                        setDirty(true);
                    }
                    console.log(`[Notepad] Restored backup for note ${note.id}`);
                } else {
                    // Server data is newer, discard backup
                    localStorage.removeItem(key);
                }
            }
        }
    } catch(e) { console.warn('[Notepad] Backup restore failed:', e); }
}

/* ══ FORMAT / VIEW ══ */
function toggleWordWrap() {
    nmCloseAll(); wordWrap = !wordWrap; applyWordWrap();
    localStorage.setItem('np_wrap', wordWrap ? '1' : '0');
}
function applyWordWrap() {
    npEditor.classList.toggle('nowrap', !wordWrap);
    gel('wrapLabel').innerHTML = (wordWrap ? '&#10003;&nbsp; ' : '&nbsp;&nbsp;&nbsp;&nbsp;') + 'Word Wrap';
}
function applyFontToEditor() {
    npEditor.style.fontFamily = fontFamily;
    npEditor.style.fontSize   = Math.round(fontSize * zoomLevel / 100) + 'px';
}
function openModal(id)  { gel(id).classList.add('show'); }
function closeModal(id) { gel(id).classList.remove('show'); }
function applyFont() {
    fontSize = parseInt(gel('fontSize').value) || 14;
    fontFamily = gel('fontFamily').value;
    applyFontToEditor();
    localStorage.setItem('np_font', JSON.stringify({ size: fontSize, family: fontFamily }));
    closeModal('fontModal');
}
function changeZoom(d) { nmCloseAll(); zoomLevel = Math.min(300, Math.max(50, zoomLevel + d*10)); applyZoom(); localStorage.setItem('np_zoom', zoomLevel); }
function resetZoom()   { nmCloseAll(); zoomLevel = 100; applyZoom(); localStorage.setItem('np_zoom', 100); }
function applyZoom()   { applyFontToEditor(); sbZoom.textContent = zoomLevel + '%'; }
function toggleSidebar() {
    nmCloseAll();
    const s = gel('npSidebar'), r = gel('npResize'), show = s.style.display === 'none';
    s.style.display = show ? '' : 'none';
    r.style.display = show ? '' : 'none';
    gel('sidebarLabel').innerHTML = (show ? '&#10003;&nbsp; ' : '&nbsp;&nbsp;&nbsp;&nbsp;') + 'Show Files';
}

/* ══ DOWNLOAD ══ */
function downloadNote() {
    nmCloseAll();
    if (!activeId) return;
    const note = notes.find(n => n.id === activeId);
    if (!note) return;
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([note.content||''], { type: 'text/plain;charset=utf-8' }));
    a.download = (note.title || 'Untitled') + '.txt';
    a.click(); URL.revokeObjectURL(a.href);
}

/* ══ KEYBOARD ══ */
function handleEditorKey(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        const s = npEditor.selectionStart, end = npEditor.selectionEnd;
        npEditor.value = npEditor.value.substring(0, s) + '    ' + npEditor.value.substring(end);
        npEditor.selectionStart = npEditor.selectionEnd = s + 4;
        onEditorChange();
    }
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.np-overlay.show').forEach(m => m.classList.remove('show'));
    }
    if (e.ctrlKey || e.metaKey) {
        if (e.key === 'n') { e.preventDefault(); newNote(); }
        if (e.key === 's') { e.preventDefault(); saveActive(); }
        if (e.key === '=' || e.key === '+') { e.preventDefault(); changeZoom(1); }
        if (e.key === '-') { e.preventDefault(); changeZoom(-1); }
        if (e.key === '0') { e.preventDefault(); resetZoom(); }
    }
});

/* ══ MENUS ══ */
function nmToggle(id) {
    const el = gel(id); const was = el.classList.contains('open');
    nmCloseAll(); if (!was) el.classList.add('open');
}
function nmCloseAll() { document.querySelectorAll('.np-menu-item.open').forEach(m => m.classList.remove('open')); }
function setupClickOutside() {
    document.addEventListener('click', e => {
        if (!e.target.closest('.np-menu-item')) nmCloseAll();
        if (!e.target.closest('.np-modal') && !e.target.closest('.np-tbar-btn')) {
            // Don't close modals on arbitrary clicks outside, only via overlay click
        }
    }, true);
    document.querySelectorAll('.np-overlay').forEach(ov => {
        ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('show'); });
    });
}

/* ══ SIDEBAR RESIZE ══ */
function setupResize() {
    const handle = gel('npResize'), sidebar = gel('npSidebar');
    let drag = false, startX = 0, startW = 0;
    handle.addEventListener('mousedown', e => {
        drag = true; startX = e.clientX; startW = sidebar.offsetWidth;
        handle.classList.add('dragging');
        document.body.style.cssText += ';cursor:col-resize!important;user-select:none!important';
    });
    document.addEventListener('mousemove', e => {
        if (!drag) return;
        const w = Math.min(420, Math.max(160, startW + (e.clientX - startX)));
        sidebar.style.width = sidebar.style.minWidth = w + 'px';
    });
    document.addEventListener('mouseup', () => {
        if (!drag) return; drag = false;
        handle.classList.remove('dragging');
        document.body.style.cursor = ''; document.body.style.userSelect = '';
    });
}

/* ══ HELPERS ══ */
function esc(s) { const d = document.createElement('div'); d.textContent = String(s||''); return d.innerHTML; }
window.addEventListener('beforeunload', e => {
    if (dirty && activeId) {
        const note = notes.find(n => n.id === activeId);
        if (note) {
            // Backup to localStorage immediately
            saveLocalBackup(activeId, note);
            // Try sendBeacon for a reliable final save (non-blocking)
            try {
                const formData = new FormData();
                formData.append('_token', CSRF);
                formData.append('_method', 'PUT');
                formData.append('title', note.title || '');
                formData.append('content', note.content || '');
                navigator.sendBeacon(`/notepad/${activeId}`, formData);
            } catch(ex) {}
        }
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/notepad/index.blade.php ENDPATH**/ ?>