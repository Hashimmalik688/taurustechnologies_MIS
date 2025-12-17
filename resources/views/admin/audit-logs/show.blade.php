@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1 class="h3">Audit Log Details</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Action Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Log ID:</dt>
                        <dd class="col-sm-8">#{{ $auditLog->id }}</dd>

                        <dt class="col-sm-4">Action:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $auditLog->action)) }}</span>
                        </dd>

                        <dt class="col-sm-4">Date & Time:</dt>
                        <dd class="col-sm-8">{{ $auditLog->created_at->format('M d, Y H:i:s') }}</dd>

                        <dt class="col-sm-4">Timestamp:</dt>
                        <dd class="col-sm-8"><code>{{ $auditLog->created_at->timestamp }}</code></dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>User Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">User ID:</dt>
                        <dd class="col-sm-8">
                            @if ($auditLog->user)
                                <a href="{{ route('users.show', $auditLog->user->id) }}">{{ $auditLog->user->id }}</a>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">User Email:</dt>
                        <dd class="col-sm-8">
                            {{ $auditLog->user?->email ?? $auditLog->user_email ?? 'System' }}
                        </dd>

                        <dt class="col-sm-4">User Name:</dt>
                        <dd class="col-sm-8">
                            {{ $auditLog->user?->name ?? 'System' }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Affected Model</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Model Type:</dt>
                        <dd class="col-sm-8">
                            {{ $auditLog->model ?? 'N/A' }}
                        </dd>

                        <dt class="col-sm-4">Model ID:</dt>
                        <dd class="col-sm-8">
                            {{ $auditLog->model_id ?? 'N/A' }}
                        </dd>

                        <dt class="col-sm-4">Description:</dt>
                        <dd class="col-sm-8">
                            {{ $auditLog->description ?? 'No description' }}
                        </dd>
                    </dl>
                </div>
            </div>

            @if ($auditLog->changes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Changes Made</h5>
                    </div>
                    <div class="card-body">
                        <pre>{{ json_encode($auditLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h5>Request Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">IP Address:</dt>
                        <dd class="col-sm-7">
                            <code>{{ $auditLog->ip_address ?? 'N/A' }}</code>
                        </dd>

                        <dt class="col-sm-5">Browser:</dt>
                        <dd class="col-sm-7">
                            @if ($auditLog->user_agent)
                                <small>{{ Str::limit($auditLog->user_agent, 50) }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </dd>
                    </dl>

                    @if ($auditLog->user_agent)
                        <div class="mt-3">
                            <label class="form-label text-muted">Full User Agent:</label>
                            <code style="word-break: break-all; font-size: 0.75rem;">
                                {{ $auditLog->user_agent }}
                            </code>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-secondary">
                    <h5>Navigation</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('audit-logs.index', ['action' => $auditLog->action]) }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="fas fa-filter"></i> Show All "{{ ucfirst(str_replace('_', ' ', $auditLog->action)) }}"
                    </a>

                    @if ($auditLog->user)
                        <a href="{{ route('audit-logs.index', ['user_id' => $auditLog->user_id]) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-user"></i> Show All from {{ $auditLog->user->email }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    pre {
        background: #f4f4f4;
        padding: 12px;
        border-radius: 4px;
        max-height: 300px;
        overflow-y: auto;
        font-size: 0.85rem;
    }

    code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>
@endsection
