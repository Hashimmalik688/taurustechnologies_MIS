@extends('layouts.master')

@section('title', 'Create Community')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-plus"></i> Create New Community
            </h2>
            <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.communities.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            <i class="bx bx-heading"></i> Community Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter community name"
                               value="{{ old('name') }}"
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
                                  maxlength="1000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="color" class="form-label fw-bold">
                            <i class="bx bx-palette"></i> Community Color <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <div id="colorPreviewCircle" style="width: 60px; height: 60px; border-radius: 50%; background: #667eea; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <i class="bx bx-bullhorn"></i>
                            </div>
                            <div style="flex: 1;">
                                <input type="color" 
                                       id="color" 
                                       name="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror"
                                       value="{{ old('color', '#667eea') }}"
                                       style="width: 100%; height: 50px; cursor: pointer;"
                                       required>
                                <small class="text-muted">Pick a color to represent this community</small>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Preview</label>
                        <div id="previewCommunity" class="p-3 rounded" style="background: #f3f4f6; border-left: 3px solid #667eea;">
                            <div class="d-flex align-items-center gap-3">
                                <div id="previewColorCircle" style="width: 44px; height: 44px; border-radius: 50%; background: #667eea; display: flex; align-items: center; justify-content: center; color: #fff;">
                                    <i class="bx bx-bullhorn" style="font-size: 20px;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <strong id="previewName">Community Name</strong>
                                    <p id="previewDesc" class="mb-0 small text-muted mt-1">Your description here</p>
                                    <div style="font-size: 11px; color: #9ca3af; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                                        <i class="bx bx-bullhorn" style="font-size: 12px;"></i>
                                        <span>Announcement Board</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> Create Community
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
    const previewName = document.getElementById('previewName');
    const previewDesc = document.getElementById('previewDesc');
    const previewColorCircle = document.getElementById('previewColorCircle');
    const colorPreviewCircle = document.getElementById('colorPreviewCircle');
    const previewCommunity = document.getElementById('previewCommunity');

    // Update preview name
    nameInput.addEventListener('input', function() {
        previewName.textContent = this.value || 'Community Name';
    });

    // Update preview description
    descInput.addEventListener('input', function() {
        previewDesc.textContent = this.value || 'Your description here';
    });

    // Update preview color
    colorInput.addEventListener('input', function() {
        const color = this.value;
        previewColorCircle.style.background = color;
        colorPreviewCircle.style.background = color;
        previewCommunity.style.borderLeftColor = color;
    });
});
</script>
@endsection
