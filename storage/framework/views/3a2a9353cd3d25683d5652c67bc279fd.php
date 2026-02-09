<!-- Sticky Notes Floating Button & Modal -->
<div id="sticky-notes-wrapper">
    <!-- Floating Button -->
    <button id="sticky-notes-btn" class="btn btn-warning rounded-circle shadow-lg" 
            style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 9999; border: none;">
        <i class="bx bx-note" style="font-size: 24px;"></i>
    </button>

    <!-- Sticky Notes Container (Hidden by default) -->
    <div id="sticky-notes-container" style="display: none; position: fixed; bottom: 100px; right: 30px; z-index: 9998; width: 350px; max-height: 500px; overflow-y: auto; background: rgba(255,255,255,0.95); border-radius: 10px; backdrop-filter: blur(10px); padding: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
        <!-- Notes will be rendered here -->\n    </div>

    <!-- Add Note Button (visible when container is shown) -->
    <button id="add-sticky-note-btn" class="btn btn-sm btn-success" 
            style="display: none; position: fixed; bottom: 100px; right: 30px; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
        <i class="bx bx-plus"></i> New Note
    </button>
</div>

<style>
.sticky-note {
    background: #ffd700;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    position: fixed;
    min-height: 120px;
    width: 300px;
    cursor: move;
    user-select: none;
    transition: opacity 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(0,0,0,0.1);
    box-sizing: border-box;
    z-index: 10000;
}

.sticky-note.dragging {
    z-index: 10001 !important;
    transition: none !important;
    opacity: 0.9;
}

.sticky-note:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

.sticky-note-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    padding-bottom: 5px;
    cursor: move;
}

.sticky-note-content {
    min-height: 60px;
    max-height: 200px;
    background: transparent;
    border: none;
    width: 100%;
    resize: vertical;
    font-size: 14px;
    font-family: 'Segoe Print', 'Comic Sans MS', cursive;
    cursor: text;
    outline: none;
    overflow-y: auto;
    line-height: 1.4;
}

.sticky-note-actions button {
    padding: 2px 8px;
    font-size: 12px;
    margin-left: 5px;
}

.color-picker {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}

.color-option {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.color-option:hover {
    transform: scale(1.2);
}

.color-option.active {
    border-color: #000;
}

#sticky-notes-container::-webkit-scrollbar {
    width: 8px;
}

#sticky-notes-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#sticky-notes-container::-webkit-scrollbar-thumb {
    background: #d4af37;
    border-radius: 10px;
}

#sticky-notes-container::-webkit-scrollbar-thumb:hover {
    background: #b8941f;
}

/* Prevent selection and scrolling during drag */
body.dragging {
    overflow: hidden;
    user-select: none !important;
}

/* Hide scrollbars during drag */
body.dragging #sticky-notes-container {
    overflow: hidden;
}

/* Smooth transitions for notes */
.sticky-note {
    transition: opacity 0.2s ease, box-shadow 0.2s ease, transform 0.1s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stickyNotesBtn = document.getElementById('sticky-notes-btn');
    const stickyNotesContainer = document.getElementById('sticky-notes-container');
    const addStickyNoteBtn = document.getElementById('add-sticky-note-btn');
    let notesVisible = false;
    let notes = [];

    const colors = [
        { name: 'yellow', value: '#fffacd' },
        { name: 'pink', value: '#ffd9e8' },
        { name: 'blue', value: '#d4e9f7' },
        { name: 'green', value: '#d4edda' },
        { name: 'orange', value: '#ffe5cc' },
        { name: 'purple', value: '#e8d4f0' }
    ];

    // Toggle sticky notes visibility
    stickyNotesBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        notesVisible = !notesVisible;
        if (notesVisible) {
            loadNotes();
            addStickyNoteBtn.style.display = 'block';
        } else {
            addStickyNoteBtn.style.display = 'none';
            // Hide all floating notes
            document.querySelectorAll('.sticky-note').forEach(note => {
                note.style.display = 'none';
            });
        }
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && notesVisible) {
            notesVisible = false;
            addStickyNoteBtn.style.display = 'none';
            // Hide all floating notes
            document.querySelectorAll('.sticky-note').forEach(note => {
                note.style.display = 'none';
            });
        }
    });

    // Add new note
    addStickyNoteBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        createNote();
    });

    // Drag and drop functionality
    let draggedNote = null;
    let offsetX = 0;
    let offsetY = 0;
    let isDragging = false;

    function enableDragging(noteEl) {
        const header = noteEl.querySelector('.sticky-note-header');
        
        header.addEventListener('mousedown', function(e) {
            if (e.target.closest('.sticky-note-actions')) return; // Don't drag when clicking actions
            
            isDragging = true;
            draggedNote = noteEl;
            
            // Use direct mouse position since all notes are fixed positioned
            offsetX = e.clientX - parseInt(noteEl.style.left || '0');
            offsetY = e.clientY - parseInt(noteEl.style.top || '0');
            
            noteEl.classList.add('dragging');
            document.body.classList.add('dragging');
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });
    }

    document.addEventListener('mousemove', function(e) {
        if (!draggedNote || !isDragging) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        // Calculate new position relative to viewport
        let newX = e.clientX - offsetX;
        let newY = e.clientY - offsetY;
        
        // Keep within viewport bounds with padding
        const padding = 10;
        newX = Math.max(padding, Math.min(window.innerWidth - 300 - padding, newX));
        newY = Math.max(padding, Math.min(window.innerHeight - draggedNote.offsetHeight - padding, newY));
        
        // Update position
        draggedNote.style.left = newX + 'px';
        draggedNote.style.top = newY + 'px';
    });

    document.addEventListener('mouseup', function(e) {
        if (!draggedNote || !isDragging) return;
        
        isDragging = false;
        draggedNote.classList.remove('dragging');
        document.body.classList.remove('dragging');
        document.body.style.userSelect = '';
        
        // Save the position
        const noteId = draggedNote.dataset.noteId;
        if (noteId) {
            localStorage.setItem(`sticky-note-pos-${noteId}`, JSON.stringify({
                left: draggedNote.style.left,
                top: draggedNote.style.top
            }));
        }
        
        draggedNote = null;
    });

    // Load notes from server
    function loadNotes() {
        fetch('/sticky-notes', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            notes = data;
            renderNotes();
        })
        .catch(error => console.error('Error loading notes:', error));
    }

    // Create new note
    function createNote() {
        fetch('/sticky-notes', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                content: 'New note...',
                color: '#fffacd'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notes.push(data.note);
                renderNotes();
            }
        })
        .catch(error => console.error('Error creating note:', error));
    }

    // Update note
    function updateNote(noteId, updates) {
        fetch(`/sticky-notes/${noteId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(updates)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const index = notes.findIndex(n => n.id === noteId);
                if (index !== -1) {
                    notes[index] = { ...notes[index], ...updates };
                }
            }
        })
        .catch(error => console.error('Error updating note:', error));
    }

    // Delete note
    function deleteNote(noteId) {
        if (!confirm('Delete this note?')) return;

        fetch(`/sticky-notes/${noteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notes = notes.filter(n => n.id !== noteId);
                renderNotes();
            }
        })
        .catch(error => console.error('Error deleting note:', error));
    }

    // Render all notes
    function renderNotes() {
        // Remove existing notes
        document.querySelectorAll('.sticky-note').forEach(note => note.remove());
        
        notes.forEach((note, index) => {
            const noteEl = document.createElement('div');
            noteEl.className = 'sticky-note';
            noteEl.style.backgroundColor = note.color || '#fffacd';
            noteEl.dataset.noteId = note.id;
            
            // Position notes in a cascade if no saved position
            const savedPos = localStorage.getItem(`sticky-note-pos-${note.id}`);
            if (savedPos) {
                const pos = JSON.parse(savedPos);
                noteEl.style.left = pos.left;
                noteEl.style.top = pos.top;
            } else {
                // Smart default positioning - cascade from top-right
                const baseX = window.innerWidth - 320;
                const baseY = 100;
                noteEl.style.left = (baseX - (index * 20)) + 'px';
                noteEl.style.top = (baseY + (index * 20)) + 'px';
            }
            
            // Show/hide based on current visibility state
            noteEl.style.display = notesVisible ? 'block' : 'none';

            noteEl.innerHTML = `
                <div class="sticky-note-header">
                    <small class="text-muted">${new Date(note.created_at).toLocaleDateString()}</small>
                    <div class="sticky-note-actions">
                        <button class="btn btn-sm btn-danger delete-note" data-note-id="${note.id}">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
                <textarea class="sticky-note-content" data-note-id="${note.id}">${note.content || ''}</textarea>
                <div class="color-picker">
                    ${colors.map(c => `
                        <div class="color-option ${c.value === note.color ? 'active' : ''}" 
                             style="background-color: ${c.value};" 
                             data-note-id="${note.id}" 
                             data-color="${c.value}"></div>
                    `).join('')}
                </div>
            `;

            // Append to body instead of container
            document.body.appendChild(noteEl);
            
            // Enable dragging for this note
            enableDragging(noteEl);
        });

        // Attach event listeners
        document.querySelectorAll('.sticky-note-content').forEach(textarea => {
            // Prevent dragging when focusing on textarea
            textarea.addEventListener('mousedown', function(e) {
                e.stopPropagation();
            });
            
            textarea.addEventListener('focus', function() {
                this.style.cursor = 'text';
                this.parentElement.style.cursor = 'default';
            });
            
            textarea.addEventListener('blur', function() {
                this.style.cursor = 'text';
                this.parentElement.style.cursor = 'move';
                const noteId = parseInt(this.dataset.noteId);
                if (noteId) {
                    updateNote(noteId, { content: this.value });
                }
            });
            
            // Auto-resize textarea
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        document.querySelectorAll('.delete-note').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const noteId = parseInt(this.dataset.noteId);
                if (noteId) {
                    deleteNote(noteId);
                }
            });
        });

        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const noteId = parseInt(this.dataset.noteId);
                const color = this.dataset.color;
                if (noteId && color) {
                    updateNote(noteId, { color: color });
                    
                    // Update UI immediately
                    const noteEl = document.querySelector(`[data-note-id="${noteId}"].sticky-note`);
                    if (noteEl) {
                        noteEl.style.backgroundColor = color;
                    }
                    
                    // Update active state
                    const parent = this.parentElement;
                    parent.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }
});
</script>
<?php /**PATH /var/www/taurus-crm/resources/views/components/sticky-notes.blade.php ENDPATH**/ ?>