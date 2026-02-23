@extends('layouts.master')

@section('title')
    {{ $role->name }} Permissions
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .pm-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .pm-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .pm-page-hdr h5 i { color:var(--bs-gold,#d4af37) }

        .legend-bar { display:flex;gap:.75rem;flex-wrap:wrap;padding:.5rem .75rem;margin-bottom:.65rem;border-radius:.55rem;background:rgba(85,110,230,.03);border:1px solid rgba(85,110,230,.06);font-size:.72rem }
        .legend-item { display:flex;align-items:center;gap:.3rem }
        .legend-dot { width:10px;height:10px;border-radius:50% }
        .ld-none { background:#f46a6a }
        .ld-view { background:#f1b44c }
        .ld-edit { background:#556ee6 }
        .ld-full { background:#34c38f }

        .cat-hdr {
            display:flex;align-items:center;gap:.35rem;font-size:.78rem;font-weight:700;
            padding:.45rem .75rem;margin-top:.5rem;margin-bottom:.35rem;
            background:linear-gradient(135deg,rgba(212,175,55,.08),transparent);
            border-left:3px solid #d4af37;border-radius:4px;
            text-transform:uppercase;letter-spacing:.3px;
        }
        .cat-hdr i { opacity:.6;font-size:.95rem }

        .perm-row {
            display:flex;align-items:center;gap:.5rem;padding:.55rem .75rem;
            border-bottom:1px solid rgba(0,0,0,.03);transition:background .12s;
        }
        .perm-row:hover { background:rgba(212,175,55,.02) }
        .perm-row:last-child { border-bottom:none }
        .perm-name { flex:1;min-width:0 }
        .perm-name .pm-title { font-size:.78rem;font-weight:600;color:var(--bs-body-color) }
        .perm-name .pm-desc { font-size:.65rem;color:var(--bs-surface-500) }
        .perm-opts { display:flex;gap:.25rem }

        .perm-opt {
            display:flex;flex-direction:column;align-items:center;gap:.1rem;
            padding:.3rem .6rem;border-radius:8px;cursor:pointer;
            transition:all .12s;border:1px solid transparent;min-width:48px;
        }
        .perm-opt:hover { background:rgba(0,0,0,.02) }
        .perm-opt input[type="radio"] { width:16px;height:16px;accent-color:#d4af37;cursor:pointer }
        .perm-opt .opt-lbl { font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px }
        .perm-opt.opt-none .opt-lbl { color:#c84646 }
        .perm-opt.opt-view .opt-lbl { color:#b87a14 }
        .perm-opt.opt-edit .opt-lbl { color:#556ee6 }
        .perm-opt.opt-full .opt-lbl { color:#1a8754 }

        .perm-opt input[type="radio"]:checked ~ .opt-lbl { font-weight:800 }
    </style>
@endsection

@section('content')
    <div class="pm-page-hdr">
        <h5>
            <i class="bx bx-shield-alt-2"></i> {{ $role->name }}
            <span style="font-size:.72rem;color:var(--bs-surface-500);font-weight:400;margin-left:.2rem">Permissions</span>
        </h5>
        <div style="display:flex;gap:.35rem">
            <a href="{{ route('settings.permissions.index') }}" class="act-btn a-primary"><i class="bx bx-arrow-back"></i> Back</a>
            <button type="submit" form="permissionForm" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                <i class="bx bx-save" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Save
            </button>
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

    <div class="legend-bar">
        <div class="legend-item"><span class="legend-dot ld-none"></span> None: No access</div>
        <div class="legend-item"><span class="legend-dot ld-view"></span> View: Read-only</div>
        <div class="legend-item"><span class="legend-dot ld-edit"></span> Edit: View + Modify</div>
        <div class="legend-item"><span class="legend-dot ld-full"></span> Full: Complete access</div>
    </div>

    <form action="{{ route('settings.permissions.roles.update', $role) }}" method="POST" id="permissionForm">
        @csrf
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-grid-alt"></i> Module Permissions</h6>
                <span style="font-size:.65rem;color:var(--bs-surface-500)">{{ $role->name }}</span>
            </div>
            <div style="padding:0 0 .5rem">
                @foreach ($modulesByCategory as $category => $modules)
                    <div class="cat-hdr"><i class="bx bx-folder"></i> {{ $category }}</div>

                    @foreach ($modules as $module)
                        @php $currentLevel = $permissions[$module->slug]['permission_level'] ?? 'none'; @endphp
                        <div class="perm-row">
                            <div class="perm-name">
                                <div class="pm-title">{{ $module->name }}</div>
                                @if ($module->description)
                                    <div class="pm-desc">{{ $module->description }}</div>
                                @endif
                            </div>
                            <div class="perm-opts">
                                <label class="perm-opt opt-none">
                                    <input type="radio" name="permissions[{{ $module->slug }}]" value="none" {{ $currentLevel === 'none' ? 'checked' : '' }}>
                                    <span class="opt-lbl">None</span>
                                </label>
                                <label class="perm-opt opt-view">
                                    <input type="radio" name="permissions[{{ $module->slug }}]" value="view" {{ $currentLevel === 'view' ? 'checked' : '' }}>
                                    <span class="opt-lbl">View</span>
                                </label>
                                <label class="perm-opt opt-edit">
                                    <input type="radio" name="permissions[{{ $module->slug }}]" value="edit" {{ $currentLevel === 'edit' ? 'checked' : '' }}>
                                    <span class="opt-lbl">Edit</span>
                                </label>
                                <label class="perm-opt opt-full">
                                    <input type="radio" name="permissions[{{ $module->slug }}]" value="full" {{ $currentLevel === 'full' ? 'checked' : '' }}>
                                    <span class="opt-lbl">Full</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:.45rem;margin-top:.5rem">
            <a href="{{ route('settings.permissions.index') }}" class="act-btn a-danger"><i class="bx bx-x"></i> Cancel</a>
            <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.32rem .85rem">
                <i class="bx bx-save" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Save Permissions
            </button>
        </div>
    </form>
@endsection
