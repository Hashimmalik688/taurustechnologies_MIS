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

                        <div id="preview-container" class="mb-3 d-none">
                            <label>Preview:</label>
 <img class="u-rounded-8 u-max-w-200 u-max-h-200 border-surface-200" id="preview-image" src="" alt="Preview">
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
 class="u-max-w-250" style="max-height: 250px; border: 2px solid var(--bs-surface-200)">
                        <p class="mt-3 text-muted">File: {{ basename(auth()->user()->avatar) }}</p>
                    @else
 <div class="u-rounded-8 d-flex align-items-center justify-content-center mx-auto bg-surface-200" style="width: 200px; height: 200px">
                            <i class="fas fa-user text-surface-muted" style="font-size: 80px"></i>
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
