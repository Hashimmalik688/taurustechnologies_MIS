@extends('layouts.master')

@section('title')
    Manage Permissions - {{ $role->name }}
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
        .permission-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
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
            padding: 0 10px;
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
        .badge-view { background: var(--bs-status-leave); }
        .badge-edit { background: #0dcaf0; }
        .badge-full { background: #198754; }
        .badge-none { background: var(--bs-status-absent); }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('settings.permissions.index') }}">Permissions</a>
        @endslot
        @slot('title')
            {{ $role->name }} Permissions
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

    <form action="{{ route('settings.permissions.roles.update', $role) }}" method="POST" id="permissionForm">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">
                                    <i class="bx bx-shield-alt me-2" style="color: var(--bs-gold);"></i>
                                    {{ $role->name }} Role Permissions
                                </h4>
                                <p class="text-muted mb-0">Configure access levels for all CRM modules</p>
                            </div>
                            <div>
                                <a href="{{ route('settings.permissions.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Permissions
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Permission Levels:</strong>
                            <div class="mt-2">
                                <span class="legend-item">
                                    <span class="legend-badge badge-none"></span> None: No access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-view"></span> View: Read-only access
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-edit"></span> Edit: Can view and modify (create/update)
                                </span>
                                <span class="legend-item">
                                    <span class="legend-badge badge-full"></span> Full: Complete access (view/edit/delete)
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
                                    <div class="permission-row row align-items-center">
                                        <div class="col-md-5">
                                            <div class="module-name">{{ $module->name }}</div>
                                            @if ($module->description)
                                                <div class="module-description">{{ $module->description }}</div>
                                            @endif
                                        </div>
                                        <div class="col-md-7">
                                            <div class="row">
                                                @php
                                                    $currentLevel = $permissions[$module->slug]['permission_level'] ?? 'none';
                                                @endphp
                                                
                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="none" 
                                                               class="form-check-input permission-radio"
                                                               {{ $currentLevel === 'none' ? 'checked' : '' }}>
                                                        <div class="permission-label text-danger">None</div>
                                                    </label>
                                                </div>

                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="view" 
                                                               class="form-check-input permission-radio"
                                                               {{ $currentLevel === 'view' ? 'checked' : '' }}>
                                                        <div class="permission-label text-warning">View</div>
                                                    </label>
                                                </div>

                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="edit" 
                                                               class="form-check-input permission-radio"
                                                               {{ $currentLevel === 'edit' ? 'checked' : '' }}>
                                                        <div class="permission-label text-info">Edit</div>
                                                    </label>
                                                </div>

                                                <div class="col-3 col-permission">
                                                    <label class="form-check-label">
                                                        <input type="radio" 
                                                               name="permissions[{{ $module->slug }}]" 
                                                               value="full" 
                                                               class="form-check-input permission-radio"
                                                               {{ $currentLevel === 'full' ? 'checked' : '' }}>
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
                            <a href="{{ route('settings.permissions.index') }}" class="btn btn-secondary me-2">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Permissions
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
        // Auto-save functionality (optional - can be enabled later)
        // This would use AJAX to save without full page reload
    </script>
@endsection
