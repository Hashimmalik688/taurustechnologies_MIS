@extends('layouts.master')

@section('title', 'Edit Community')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-pencil"></i> Edit Community
            </h2>
            <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.communities.update', $community->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            <i class="bx bx-heading"></i> Community Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter community name"
                               value="{{ old('name', $community->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            <i class="bx bx-message-dots"></i> Description <span class="text-muted">(optional)</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe the purpose of this community"
                                  rows="4"
                                  maxlength="1000">{{ old('description', $community->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="color" class="form-label fw-bold">
                            <i class="bx bx-palette"></i> Community Color <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <div id="colorPreviewCircle" style="width: 60px; height: 60px; border-radius: 50%; background: {{ $community->color ?? '#667eea' }}; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <i class="bx bx-bullhorn"></i>
                            </div>
                            <div style="flex: 1;">
                                <input type="color" 
                                       id="color" 
                                       name="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror"
                                       value="{{ old('color', $community->color ?? '#667eea') }}"
                                       style="width: 100%; height: 50px; cursor: pointer;"
                                       required>
                                <small class="text-muted">Pick a color to represent this community</small>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Posting Permissions Section -->
                    <div class="mb-4">
                        <hr class="my-4">
                        <h5 class="mb-3">
                            <i class="bx bx-lock-alt"></i> Posting Permissions
                        </h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="posting_restricted" 
                                   name="posting_restricted" 
                                   value="1"
                                   {{ old('posting_restricted', $community->posting_restricted) ? 'checked' : '' }}
                                   onchange="togglePostingMode()">
                            <label class="form-check-label" for="posting_restricted">
                                <strong>Restrict Posting (Only Selected People Can Post)</strong>
                                <br>
                                <small class="text-muted">
                                    When disabled, everyone in the community can post. When enabled, only people you select below can post.
                                </small>
                            </label>
                        </div>

                        <!-- Who Can Post Section -->
                        <div id="whoCanPostSection" style="display: {{ old('posting_restricted', $community->posting_restricted) ? 'block' : 'none' }};">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <strong><i class="bx bx-user-check"></i> People Who Can Post Announcements</strong>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Add Person with Posting Permission</label>
                                        <div class="input-group">
                                            <select id="postingMemberSelect" class="form-select">
                                                <option value="">Select a member...</option>
                                                @foreach($members as $member)
                                                    @if($member->id !== $community->created_by)
                                                        <option value="{{ $member->id }}" data-name="{{ $member->name }}" {{ $member->pivot->can_post ? 'disabled' : '' }}>
                                                            {{ $member->name }} {{ $member->pivot->can_post ? '(Already has access)' : '' }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-success" onclick="grantPostingAccess()">
                                                <i class="bx bx-plus"></i> Grant Access
                                            </button>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="bx bx-info-circle"></i> <strong>{{ $community->creator->name }}</strong> (Creator) always has posting access
                                    </div>

                                    <div id="postingMembersList">
                                        <strong class="d-block mb-2">Members with Posting Access:</strong>
                                        <div class="list-group">
                                            @foreach($members->where('pivot.can_post', true)->where('id', '!=', $community->created_by) as $member)
                                                <div class="list-group-item d-flex justify-content-between align-items-center" data-posting-user-id="{{ $member->id }}">
                                                    <div>
                                                        <i class="bx bx-user"></i> {{ $member->name }}
                                                        <small class="text-muted">({{ $member->email }})</small>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="revokePostingAccess({{ $member->id }}, '{{ $member->name }}')">
                                                        <i class="bx bx-x"></i> Revoke
                                                    </button>
                                                </div>
                                            @endforeach
                                            @if($members->where('pivot.can_post', true)->where('id', '!=', $community->created_by)->count() === 0)
                                                <div class="text-muted text-center py-3" id="noPostingMembers">
                                                    No members have been granted posting access yet.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Management Section -->
                    <div class="mb-4">
                        <hr class="my-4">
                        <h5 class="mb-3">
                            <i class="bx bx-users"></i> Member Management
                        </h5>
                        
                        <!-- Add Member -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Add Member</label>
                            <div class="input-group">
                                <select id="newMember" class="form-select">
                                    <option value="">Select a user to add...</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary" onclick="addMember()">
                                    <i class="bx bx-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        <!-- Current Members -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong>Current Members ({{ $members->count() }})</strong>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="membersTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($members as $member)
                                                <tr data-member-id="{{ $member->id }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-circle me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                                {{ substr($member->name, 0, 1) }}
                                                            </div>
                                                            {{ $member->name }}
                                                            @if($member->id === $community->created_by)
                                                                <span class="badge bg-warning ms-2">Creator</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $member->email }}</td>
                                                    <td>
                                                        @if($member->roles->isNotEmpty())
                                                            <span class="badge bg-info">{{ $member->roles->pluck('name')->join(', ') }}</span>
                                                        @else
                                                            <span class="text-muted">No role</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="removeMember({{ $member->id }}, '{{ $member->name }}')">
                                                            <i class="bx bx-trash"></i> Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No members yet</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Preview</label>
                        <div id="previewCommunity" class="p-3 rounded bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bx-group" id="previewIcon" style="font-size: 24px;"></i>
                                <div>
                                    <strong id="previewName">{{ $community->name }}</strong>
                                    <p id="previewDesc" class="mb-0 small text-muted mt-1">{{ $community->description ?: 'Your description here' }}</p>
                                </div>
                                <span id="previewColor" class="badge ms-auto" style="background-color: {{ getColorHex($community->color) }};">{{ ucfirst($community->color) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> Update Community
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .page-wrapper {
        padding: 1.5rem 0;
    }

    .page-content {
        max-width: 700px;
        margin: 0 auto;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }

    .form-label {
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-color: #e5e7eb;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }

    .badge {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const descInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const colorPreviewCircle = document.getElementById('colorPreviewCircle');

    // Update color preview on change
    if (colorInput) {
        colorInput.addEventListener('input', function() {
            const color = this.value;
            if (colorPreviewCircle) {
                colorPreviewCircle.style.background = color;
            }
        });
    }
});

// Member Management Functions
async function addMember() {
    const select = document.getElementById('newMember');
    const userId = select.value;
    
    if (!userId) {
        alert('Please select a user to add');
        return;
    }
    
    try {
        const response = await fetch('/api/communities/{{ $community->id }}/members', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload(); // Reload to show updated member list
        } else {
            alert('Error: ' + (data.message || 'Failed to add member'));
        }
    } catch (error) {
        console.error('Error adding member:', error);
        alert('Failed to add member. Please try again.');
    }
}

async function removeMember(userId, userName) {
    if (!confirm(`Are you sure you want to remove ${userName} from this community?`)) {
        return;
    }
    
    try {
        const response = await fetch('/api/communities/{{ $community->id }}/members/' + userId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Remove the row from the table
            const row = document.querySelector(`tr[data-member-id="${userId}"]`);
            if (row) {
                row.remove();
            }
            
            // Add user back to the select dropdown
            const select = document.getElementById('newMember');
            const option = document.createElement('option');
            option.value = userId;
            option.textContent = userName;
            select.appendChild(option);
            
            // Update member count
            const countElement = document.querySelector('.card-header strong');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent.match(/\d+/)[0]);
                countElement.textContent = `Current Members (${currentCount - 1})`;
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to remove member'));
        }
    } catch (error) {
        console.error('Error removing member:', error);
        alert('Failed to remove member. Please try again.');
    }
}

// Posting Permission Management
function togglePostingMode() {
    const checkbox = document.getElementById('posting_restricted');
    const section = document.getElementById('whoCanPostSection');
    if (section) {
        section.style.display = checkbox.checked ? 'block' : 'none';
    }
}

async function grantPostingAccess() {
    const select = document.getElementById('postingMemberSelect');
    const userId = select.value;
    const userName = select.options[select.selectedIndex]?.getAttribute('data-name');
    
    if (!userId) {
        alert('Please select a member');
        return;
    }
    
    try {
        const response = await fetch('/api/communities/{{ $community->id }}/members/' + userId + '/toggle-post', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.can_post) {
            // Add to list
            const list = document.querySelector('#postingMembersList .list-group');
            const noMembersMsg = document.getElementById('noPostingMembers');
            if (noMembersMsg) noMembersMsg.remove();
            
            const item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            item.setAttribute('data-posting-user-id', userId);
            item.innerHTML = `
                <div>
                    <i class="bx bx-user"></i> ${userName}
                </div>
                <button type="button" 
                        class="btn btn-sm btn-danger" 
                        onclick="revokePostingAccess(${userId}, '${userName}')">
                    <i class="bx bx-x"></i> Revoke
                </button>
            `;
            list.appendChild(item);
            
            // Disable option in select
            select.options[select.selectedIndex].disabled = true;
            select.options[select.selectedIndex].text += ' (Already has access)';
            select.value = '';
            
            alert('Posting access granted to ' + userName);
        } else {
            alert('Error: ' + (data.message || 'Failed to grant access'));
        }
    } catch (error) {
        console.error('Error granting access:', error);
        alert('Failed to grant access. Please try again.');
    }
}

async function revokePostingAccess(userId, userName) {
    if (!confirm(`Remove posting access from ${userName}?`)) {
        return;
    }
    
    try {
        const response = await fetch('/api/communities/{{ $community->id }}/members/' + userId + '/toggle-post', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && !data.can_post) {
            // Remove from list
            const item = document.querySelector(`[data-posting-user-id="${userId}"]`);
            if (item) item.remove();
            
            // Check if list is empty
            const list = document.querySelector('#postingMembersList .list-group');
            if (list.children.length === 0) {
                list.innerHTML = '<div class="text-muted text-center py-3" id="noPostingMembers">No members have been granted posting access yet.</div>';
            }
            
            // Re-enable option in select
            const select = document.getElementById('postingMemberSelect');
            for (let option of select.options) {
                if (option.value == userId) {
                    option.disabled = false;
                    option.text = option.getAttribute('data-name');
                    break;
                }
            }
            
            alert('Posting access revoked from ' + userName);
        } else {
            alert('Error: ' + (data.message || 'Failed to revoke access'));
        }
    } catch (error) {
        console.error('Error revoking access:', error);
        alert('Failed to revoke access. Please try again.');
    }
}

</script>

@php
    function getColorHex($color) {
        $colors = [
            'red' => '#dc3545',
            'yellow' => '#ffc107',
            'blue' => '#0d6efd',
            'green' => '#198754',
            'purple' => '#6f42c1',
            'orange' => '#fd7e14',
        ];
        return $colors[$color] ?? '#6f42c1';
    }
@endphp
@endsection

@section('script')
<script>
// Avatar preview
document.getElementById('communityAvatar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('communityAvatarPreview').innerHTML = 
                `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;" />`;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
