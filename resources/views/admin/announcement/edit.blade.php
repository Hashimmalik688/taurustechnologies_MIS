@extends('layouts.master')

@section('title', 'Edit Announcement')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-pencil"></i> Edit Announcement
            </h2>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">
                            <i class="bx bx-heading"></i> Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control @error('title') is-invalid @enderror"
                               placeholder="Enter announcement title (max 100 characters)"
                               maxlength="100"
                               value="{{ old('title', $announcement->title) }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-bold">
                            <i class="bx bx-message-dots"></i> Message <span class="text-danger">*</span>
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  class="form-control @error('message') is-invalid @enderror"
                                  placeholder="Enter announcement message (max 500 characters)"
                                  rows="5"
                                  maxlength="500"
                                  required>{{ old('message', $announcement->message) }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="animation" class="form-label fw-bold">
                                <i class="bx bx-play"></i> Animation <span class="text-danger">*</span>
                            </label>
                            <select id="animation" 
                                    name="animation" 
                                    class="form-select @error('animation') is-invalid @enderror"
                                    required>
                                <option value="">Select an animation style</option>
                                <option value="slide" {{ old('animation', $announcement->animation) === 'slide' ? 'selected' : '' }}>Slide Down</option>
                                <option value="fade" {{ old('animation', $announcement->animation) === 'fade' ? 'selected' : '' }}>Fade In</option>
                                <option value="bounce" {{ old('animation', $announcement->animation) === 'bounce' ? 'selected' : '' }}>Bounce</option>
                                <option value="wave" {{ old('animation', $announcement->animation) === 'wave' ? 'selected' : '' }}>Wave</option>
                            </select>
                            @error('animation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="community_id" class="form-label fw-bold">
                                <i class="bx bx-group"></i> Community <span class="text-muted">(optional)</span>
                            </label>
                            <select id="community_id" 
                                    name="community_id" 
                                    class="form-select @error('community_id') is-invalid @enderror">
                                <option value="">-- No Community --</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id', $announcement->community_id) == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle"></i> Selecting a community will force icon to "Important" and color to "Red"
                            </small>
                            @error('community_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                            </label>
                            <select id="background_color" 
                                    name="background_color" 
                                    class="form-select @error('background_color') is-invalid @enderror"
                                    required>
                                <option value="">Select a background color</option>
                                <option value="red" {{ old('background_color', $announcement->background_color) === 'red' ? 'selected' : '' }}>Red</option>
                                <option value="yellow" {{ old('background_color', $announcement->background_color) === 'yellow' ? 'selected' : '' }}>Yellow</option>
                                <option value="blue" {{ old('background_color', $announcement->background_color) === 'blue' ? 'selected' : '' }}>Blue</option>
                                <option value="green" {{ old('background_color', $announcement->background_color) === 'green' ? 'selected' : '' }}>Green</option>
                                <option value="purple" {{ old('background_color', $announcement->background_color) === 'purple' ? 'selected' : '' }}>Purple</option>
                                <option value="orange" {{ old('background_color', $announcement->background_color) === 'orange' ? 'selected' : '' }}>Orange</option>
                            </select>
                            @error('background_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="icon" class="form-label fw-bold">
                                <i class="bx bx-smile"></i> Icon <span class="text-danger">*</span>
                            </label>
                            <select id="icon" 
                                    name="icon" 
                                    class="form-select @error('icon') is-invalid @enderror"
                                    required>
                                <option value="">Select an icon</option>
                                <option value="warning" {{ old('icon', $announcement->icon) === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                                <option value="info" {{ old('icon', $announcement->icon) === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                                <option value="important" {{ old('icon', $announcement->icon) === 'important' ? 'selected' : '' }}>⭐ Important</option>
                                <option value="star" {{ old('icon', $announcement->icon) === 'star' ? 'selected' : '' }}>✭ Star</option>
                                <option value="check" {{ old('icon', $announcement->icon) === 'check' ? 'selected' : '' }}>✅ Check</option>
                                <option value="alert" {{ old('icon', $announcement->icon) === 'alert' ? 'selected' : '' }}>🔔 Alert</option>
                            </select>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="auto_dismiss" class="form-label fw-bold">
                                <i class="bx bx-time"></i> Auto Dismiss <span class="text-danger">*</span>
                            </label>
                            <select id="auto_dismiss" 
                                    name="auto_dismiss" 
                                    class="form-select @error('auto_dismiss') is-invalid @enderror"
                                    required>
                                <option value="">Select dismissal behavior</option>
                                <option value="never" {{ old('auto_dismiss', $announcement->auto_dismiss) === 'never' ? 'selected' : '' }}>Never (Sticky)</option>
                                <option value="5s" {{ old('auto_dismiss', $announcement->auto_dismiss) === '5s' ? 'selected' : '' }}>5 Seconds</option>
                                <option value="10s" {{ old('auto_dismiss', $announcement->auto_dismiss) === '10s' ? 'selected' : '' }}>10 Seconds</option>
                                <option value="30s" {{ old('auto_dismiss', $announcement->auto_dismiss) === '30s' ? 'selected' : '' }}>30 Seconds</option>
                            </select>
                            @error('auto_dismiss')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active"
                               value="1"
                               {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <i class="bx bx-check-circle"></i> Active Now
                        </label>
                        <small class="text-muted d-block mt-2">Activating this announcement will deactivate any other active announcements</small>
                    </div>

                    <!-- Info Panel -->
                    <div class="alert alert-info" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Created by:</strong> {{ $announcement->createdBy->name }} 
                        <br>
                        <strong>Published:</strong> {{ $announcement->published_at->format('M d, Y H:i') }}
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> Update Announcement
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
        max-width: 900px;
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
</style>
@endsection
