@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block">Reset Password for {{ $user->name }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
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

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Reset Password for {{ $user->email }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.reset-password', $user->id) }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                required
                                minlength="8"
                                placeholder="Enter new password (min 8 characters)"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                class="form-control @error('password_confirmation') is-invalid @enderror" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                minlength="8"
                                placeholder="Confirm new password"
                            >
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle"></i> Info:</strong>
                            <ul class="mb-0 mt-2">
                                <li>An email with the new password will be sent to {{ $user->email }}</li>
                                <li>The user should change this password after logging in</li>
                                <li>This action will be logged in the audit logs</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-key"></i> Reset Password & Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h5>User Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4">Current Role:</dt>
                        <dd class="col-sm-8">
                            @forelse ($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @empty
                                <span class="badge bg-secondary">No role assigned</span>
                            @endforelse
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $user->status === 'active' ? 'bg-success' : ($user->status === 'inactive' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Last Login:</dt>
                        <dd class="col-sm-8">
                            @if ($user->last_login_at)
                                {{ $user->last_login_at->format('M d, Y H:i') }}
                            @else
                                <em>Never logged in</em>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Last Login IP:</dt>
                        <dd class="col-sm-8">
                            {{ $user->last_login_ip ?? 'N/A' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .card-header h5 {
        margin-bottom: 0;
        color: #333;
    }
</style>
@endsection
