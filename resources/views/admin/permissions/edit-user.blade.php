@extends('layouts.master')

@section('title')
    Manage Permissions - {{ $user->name }}
@endsection

@section('css')
    <style>
        .permission-matrix {
            background: #fff;
            border-radius: 8px;
        }
        .permission-row {
            border-bottom: 1px solid var(--bs-print-bg-alt);
            padding: 12px 0;
            transition: background 0.2s;
        }
        .permission-row:hover {
            background: var(--bs-surface-bg-light);
        }
        .module-name {
            font-weight: 500;
            color: #495057;
        }
        .module-description {
            font-size: 0.875rem;
            color: var(--bs-status-default);
        }
        .category-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), transparent);
            border-left: 4px solid var(--bs-gold);
            padding: 12px 16px;
            margin: 20px 0 10px 0;
            border-radius: 4px;
        }
        .permission-radio {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .permission-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 8px;
        }
        .col-permission {
            text-align: center;
            padding: 0 8px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        .legend-badge {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .badge-inherit { background: var(--bs-status-default); }
        .badge-view { background: var(--bs-status-leave); }
        .badge-edit { background: #0dcaf0; }
        .badge-full { background: #198754; }
        .badge-none { background: var(--bs-status-absent); }
        .inherited-badge {
            background: #e9ecef;
            color: var(--bs-status-default);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }
        .override-badge {
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('users.index') }}">Users</a>
        @endslot
        @slot('title')
            {{ $user->name }} - Permission Overrides
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

    <form action="{{ route('settings.permissions.users.update', $user) }}" method="POST" id="permissionForm">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">
                                    <i class="bx bx-user me-2" style="color: var(--bs-gold);"></i>
                                    {{ $user->name }} - Permission Overrides
                                </h4>
                                <p class="text-muted mb-0">
                                    User Roles: 
                                    @foreach ($userRoles as $roleName)
                                        <span class="badge bg-secondary">{{ $roleName }}</span>
                                    @endforeach
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Overrides
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>About Permission Overrides:</strong>
                            <div class="mt-2">
                                <ul class="mb-0">
                                    <li>By default, users inherit permissions from their assigned roles</li>
                                    <li>Use overrides to grant or restrict access for this specific user</li>
                                    <li>Select "Inherit from Role" to use the role's default permission</li>
                                    <li>Overridden permissions are marked with <span class="override-badge">OVERRIDE</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <div class="mt-2">
                                <span class="legend-item">
                                    <span class="legend-badge badge-inherit"></span> Inherit: Use role permission
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-none"></span> None: Explicitly deny access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-view"></span> View: Read-only access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-edit"></span> Edit: View and modify
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-full"></span> Full: Complete access
                                </span>
                            </div>
                        </div>

                        <div class="permission-matrix">
                            @foreach ($modulesByCategory as $category => $modules)
                                <div class="category-header">
                                    <h5 class="mb-0">
                                        <i class="bx bx-folder me-2"></i>
                                        {{ $category }}
                                    </h5>
                                </div>

                                @foreach ($modules as $module)
                                    @php
                                        $permission = $permissions[$module->slug];
                                        $currentLevel = $permission['permission_level'];
                                        $source = $permission['source'];
                                        $isOverride = $source === 'override';
                                    @endphp

                                    <div class="permission-row row align-items-center">
                                        <div class="col-md-4">
                                            <div class="module-name">
                                                {{ $module->name }}
                                                @if ($isOverride)
                                                    <span class="override-badge">OVERRIDE</span>
                                                @else
                                                    <span class="inherited-badge">FROM ROLE</span>
                                                @endif
                                            </div>
                                            @if ($module->description)
                                                <div class="module-description">{{ $module->description }}</div>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="inherit" 
                                                               class="form-check-input permission-radio"
                                                               {{ !$isOverride ? 'checked' : '' }}>
                                                        <div class="permission-label text-secondary">Inherit</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="none" 
                                                               class="form-check-input permission-radio"
                                                               {{ $isOverride && $currentLevel === 'none' ? 'checked' : '' }}>
                                                        <div class="permission-label text-danger">None</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="view" 
                                                               class="form-check-input permission-radio"
                                                               {{ $isOverride && $currentLevel === 'view' ? 'checked' : '' }}>
                                                        <div class="permission-label text-warning">View</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="edit" 
                                                               class="form-check-input permission-radio"
                                                               {{ $isOverride && $currentLevel === 'edit' ? 'checked' : '' }}>
                                                        <div class="permission-label text-info">Edit</div>
                                                    </label>
                                                </div>

                                                <div class="col col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="full" 
                                                               class="form-check-input permission-radio"
                                                               {{ $isOverride && $currentLevel === 'full' ? 'checked' : '' }}>
                                                        <div class="permission-label text-success">Full</div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Overrides
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        // Highlight changed permissions
        document.querySelectorAll('.permission-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const row = this.closest('.permission-row');
                row.style.background = '#fffbea';
                setTimeout(() => {
                    row.style.background = '';
                }, 2000);
            });
        });
    </script>
@endsection
