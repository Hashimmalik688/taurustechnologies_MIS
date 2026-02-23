@extends('layouts.master')

@section('title')
    Permission Management
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .pm-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .pm-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .pm-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .pm-page-hdr .pm-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        .role-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.65rem;margin-bottom:.65rem }
        .role-tile {
            border-radius:.55rem;padding:.85rem;border:1px solid rgba(0,0,0,.05);
            background:var(--bs-card-bg);transition:all .2s;position:relative;overflow:hidden;
        }
        .role-tile::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#d4af37,#e8c84a);border-radius:.55rem .55rem 0 0 }
        .role-tile:hover { box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px) }
        .role-tile h6 { font-size:.82rem;font-weight:700;margin:0 0 .2rem;display:flex;align-items:center;gap:.3rem }
        .role-tile h6 i { color:#d4af37;font-size:.95rem }
        .role-tile .role-mod { font-size:.65rem;color:var(--bs-surface-500);margin-bottom:.55rem }

        .perm-pills { display:flex;flex-wrap:wrap;gap:.25rem;margin-bottom:.65rem }
        .pp { font-size:.58rem;font-weight:700;padding:.15rem .4rem;border-radius:1rem;display:inline-flex;align-items:center;gap:.15rem }
        .pp i { font-size:.65rem }
        .pp.pp-full { background:rgba(52,195,143,.1);color:#1a8754 }
        .pp.pp-edit { background:rgba(85,110,230,.1);color:#556ee6 }
        .pp.pp-view { background:rgba(212,175,55,.1);color:#b89730 }
        .pp.pp-none { background:rgba(244,106,106,.08);color:#c84646 }
    </style>
@endsection

@section('content')
    <div class="pm-page-hdr">
        <h5>
            <i class="bx bx-shield-alt-2"></i> Permission Management
            <span class="pm-sub">Role-based access control</span>
        </h5>
        <div style="display:flex;gap:.35rem;align-items:center">
            <form action="{{ route('settings.permissions.clear-cache') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="act-btn a-warn" onclick="return confirm('Clear all permission caches?')">
                    <i class="bx bx-refresh"></i> Clear Cache
                </button>
            </form>
            <a href="{{ route('settings.hub') }}" class="act-btn a-primary"><i class="bx bx-arrow-back"></i> Settings</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:10px;border:none;background:rgba(52,195,143,.08);color:#1a8754">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:10px;border:none;background:rgba(244,106,106,.08);color:#c84646">
            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.6rem"></button>
        </div>
    @endif

    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr">
            <h6><i class="bx bx-key"></i> Role Permissions</h6>
            <span style="font-size:.65rem;color:var(--bs-surface-500)">Manage access for each role</span>
        </div>
        <div class="sec-body" style="padding:.75rem">
            <div class="role-grid">
                @foreach ($roles as $role)
                    <div class="role-tile">
                        <h6><i class="bx bx-shield-alt"></i> {{ $role->name }}</h6>
                        <div class="role-mod">{{ $roleStats[$role->id]['total_modules'] }} modules</div>

                        <div class="perm-pills">
                            @if ($roleStats[$role->id]['full_access'] > 0)
                                <span class="pp pp-full"><i class="bx bx-check-double"></i> {{ $roleStats[$role->id]['full_access'] }} Full</span>
                            @endif
                            @if ($roleStats[$role->id]['edit_access'] > 0)
                                <span class="pp pp-edit"><i class="bx bx-edit"></i> {{ $roleStats[$role->id]['edit_access'] }} Edit</span>
                            @endif
                            @if ($roleStats[$role->id]['view_access'] > 0)
                                <span class="pp pp-view"><i class="bx bx-show"></i> {{ $roleStats[$role->id]['view_access'] }} View</span>
                            @endif
                            @if ($roleStats[$role->id]['no_access'] > 0)
                                <span class="pp pp-none"><i class="bx bx-block"></i> {{ $roleStats[$role->id]['no_access'] }} None</span>
                            @endif
                        </div>

                        <a href="{{ route('settings.permissions.roles.edit', $role) }}" class="pipe-pill-apply" style="font-size:.68rem;padding:.25rem .6rem;display:inline-flex;align-items:center;gap:.2rem;width:100%;justify-content:center">
                            <i class="bx bx-cog"></i> Manage Permissions
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-user"></i> User Permission Overrides</h6>
        </div>
        <div class="sec-body" style="padding:.75rem">
            <div style="font-size:.78rem;color:var(--bs-body-color);line-height:1.5">
                To manage permissions for a specific user (override their role), go to
                <a href="{{ route('users.index') }}" style="color:#d4af37;font-weight:600">Users Management</a> and click "Manage Permissions".
            </div>
            <div style="margin-top:.5rem;padding:.45rem .65rem;border-radius:10px;background:rgba(85,110,230,.04);border:1px solid rgba(85,110,230,.08);font-size:.72rem;color:#556ee6">
                <i class="bx bx-info-circle me-1"></i>
                <strong>How it works:</strong> Users inherit role permissions by default. Override individual users if they need different access levels.
            </div>
        </div>
    </div>
@endsection
