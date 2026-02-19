@extends('layouts.master')

@section('title')
    Permission Management
@endsection

@section('css')
    <style>
        .role-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #e3e6f0;
        }
        .role-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
        }
        .permission-stat {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            margin: 2px;
        }
        .stat-full { background: #d4edda; color: #155724; }
        .stat-edit { background: #cce5ff; color: #004085; }
        .stat-view { background: #fff3cd; color: #856404; }
        .stat-none { background: #f8d7da; color: #721c24; }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Settings
        @endslot
        @slot('title')
            Permission Management
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-1">Role Permissions</h4>
                            <p class="text-muted mb-0">Manage granular access control for each role across all CRM modules</p>
                        </div>
                        <div>
                            <form action="{{ route('settings.permissions.clear-cache') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm" 
                                        onclick="return confirm('Clear all permission caches? This will refresh permissions for all users.')">
                                    <i class="bx bx-refresh me-1"></i> Clear Cache
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card role-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <i class="bx bx-shield-alt me-2" style="color: var(--bs-gold);"></i>
                                                    {{ $role->name }}
                                                </h5>
                                                <p class="text-muted small mb-0">
                                                    {{ $roleStats[$role->id]['total_modules'] }} modules available
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            @if ($roleStats[$role->id]['full_access'] > 0)
                                                <span class="permission-stat stat-full">
                                                    <i class="bx bx-check-double"></i> {{ $roleStats[$role->id]['full_access'] }} Full
                                                </span>
                                            @endif
                                            @if ($roleStats[$role->id]['edit_access'] > 0)
                                                <span class="permission-stat stat-edit">
                                                    <i class="bx bx-edit"></i> {{ $roleStats[$role->id]['edit_access'] }} Edit
                                                </span>
                                            @endif
                                            @if ($roleStats[$role->id]['view_access'] > 0)
                                                <span class="permission-stat stat-view">
                                                    <i class="bx bx-show"></i> {{ $roleStats[$role->id]['view_access'] }} View
                                                </span>
                                            @endif
                                            @if ($roleStats[$role->id]['no_access'] > 0)
                                                <span class="permission-stat stat-none">
                                                    <i class="bx bx-block"></i> {{ $roleStats[$role->id]['no_access'] }} None
                                                </span>
                                            @endif
                                        </div>

                                        <a href="{{ route('settings.permissions.roles.edit', $role) }}" 
                                           class="btn btn-sm btn-outline-primary w-100">
                                            <i class="bx bx-cog me-1"></i> Manage Permissions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bx bx-user me-2" style="color: var(--bs-gold);"></i>
                        User Permission Overrides
                    </h5>
                    <p class="text-muted mb-3">
                        To manage permissions for a specific user (override their role permissions), go to
                        <a href="{{ route('users.index') }}">Users Management</a>, select a user, and click "Manage Permissions".
                    </p>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>How it works:</strong> By default, users inherit permissions from their assigned roles. 
                        You can override permissions for individual users if they need different access levels than their role provides.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
