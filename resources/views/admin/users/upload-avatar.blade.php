@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1 class="h3">Upload Avatar</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Upload Profile Avatar</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.upload-avatar', auth()->id()) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Select Image <span class="text-danger">*</span></label>
                            <input 
                                type="file" 
                                class="form-control @error('avatar') is-invalid @enderror" 
                                id="avatar" 
                                name="avatar" 
                                accept="image/*"
                                required
                            >
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Accepted formats: JPG, PNG, GIF. Max size: 5MB
                            </small>
                        </div>

                        <div id="preview-container" style="display:none;" class="mb-3">
                            <label>Preview:</label>
                            <img id="preview-image" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ccc;">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Avatar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h5>Current Avatar</h5>
                </div>
                <div class="card-body text-center">
                    @if (auth()->user()->avatar && \Storage::disk('local')->exists(auth()->user()->avatar))
                        <img src="{{ \Storage::disk('local')->url(auth()->user()->avatar) }}" 
                             alt="Current Avatar" 
                             style="max-width: 250px; max-height: 250px; border-radius: 8px; border: 2px solid #ddd;">
                        <p class="mt-3 text-muted">File: {{ basename(auth()->user()->avatar) }}</p>
                    @else
                        <div style="width: 200px; height: 200px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-user" style="font-size: 80px; color: #999;"></i>
                        </div>
                        <p class="mt-3 text-muted">No avatar uploaded yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('preview-image').src = event.target.result;
            document.getElementById('preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
