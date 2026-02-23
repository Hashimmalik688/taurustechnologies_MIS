@extends('layouts.master')

@section('title')
    Manage Permissions - {{ $user->name }}
@endsection

@section('css')
<style>
/* ─── Permission Overrides ─── */
.po-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem;}
.po-hdr-left h5{font-weight:800;font-size:1rem;color:var(--bs-surface-800);display:flex;align-items:center;gap:.35rem;margin:0 0 .15rem 0;}
.po-hdr-left .po-roles{display:flex;align-items:center;gap:.25rem;flex-wrap:wrap;}
.po-role-badge{font-size:.52rem;font-weight:700;padding:.12rem .4rem;border-radius:.2rem;background:rgba(102,126,234,.08);color:var(--bs-gradient-start);}
.po-hdr-right{display:flex;gap:.3rem;}
.po-btn{font-size:.65rem;font-weight:600;padding:.35rem .75rem;border-radius:.4rem;display:inline-flex;align-items:center;gap:.25rem;transition:all .15s;text-decoration:none;cursor:pointer;border:none;}
.po-btn-back{background:var(--bs-card-bg);border:1px solid var(--bs-surface-200);color:var(--bs-surface-500);}
.po-btn-back:hover{border-color:var(--bs-surface-400);color:var(--bs-surface-600);}
.po-btn-save{background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end));color:#fff;box-shadow:0 2px 8px rgba(102,126,234,.25);}
.po-btn-save:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(102,126,234,.35);}

/* Info Card */
.po-info{background:var(--bs-card-bg);border-radius:.5rem;border:1px solid var(--bs-surface-100);padding:.6rem .8rem;margin-bottom:.6rem;box-shadow:0 1px 3px rgba(0,0,0,.03);}
.po-info-title{font-size:.65rem;font-weight:700;color:var(--bs-gradient-start);display:flex;align-items:center;gap:.25rem;margin-bottom:.35rem;}
.po-info-list{margin:0;padding-left:1rem;font-size:.6rem;color:var(--bs-surface-600);line-height:1.6;}
.po-info-list li{margin-bottom:.1rem;}

/* Legend */
.po-legend{display:flex;gap:.6rem;flex-wrap:wrap;padding:.45rem .75rem;background:var(--bs-card-bg);border-radius:.4rem;border:1px solid var(--bs-surface-100);margin-bottom:.6rem;}
.po-legend-item{display:flex;align-items:center;gap:.25rem;font-size:.55rem;font-weight:600;color:var(--bs-surface-600);}
.po-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.po-dot.inherit{background:var(--bs-surface-400);}
.po-dot.none{background:var(--bs-ui-danger,#f46a6a);}
.po-dot.view{background:var(--bs-ui-warning,#f5b041);}
.po-dot.edit{background:var(--bs-ui-info,#50a5f1);}
.po-dot.full{background:var(--bs-ui-success,#34c38f);}

/* Category */
.po-category{margin-bottom:.4rem;}
.po-cat-hdr{
    background:linear-gradient(135deg,rgba(102,126,234,.06),transparent);
    border-left:3px solid var(--bs-gradient-start);
    padding:.4rem .65rem;border-radius:0 .35rem .35rem 0;
    font-weight:700;font-size:.7rem;color:var(--bs-surface-700);
    display:flex;align-items:center;gap:.3rem;margin-bottom:.15rem;
}
.po-cat-hdr i{color:var(--bs-gradient-start);font-size:.85rem;}

/* Module Row */
.po-row{
    display:flex;align-items:center;padding:.4rem .65rem;
    border-bottom:1px solid var(--bs-surface-50);
    transition:background .15s;gap:.5rem;
}
.po-row:hover{background:var(--bs-surface-bg-light);}
.po-row:last-child{border-bottom:none;}
.po-row-info{flex:1;min-width:0;}
.po-mod-name{font-weight:600;font-size:.68rem;color:var(--bs-surface-700);display:flex;align-items:center;gap:.3rem;}
.po-mod-desc{font-size:.55rem;color:var(--bs-surface-muted);margin-top:.05rem;}
.po-tag{font-size:.45rem;font-weight:700;padding:.08rem .3rem;border-radius:.15rem;text-transform:uppercase;letter-spacing:.3px;}
.po-tag.override{background:rgba(212,175,55,.1);color:var(--bs-gold-dark,#b8860b);}
.po-tag.from-role{background:var(--bs-surface-100);color:var(--bs-surface-500);}

/* Radio Group */
.po-radios{display:flex;gap:.15rem;flex-shrink:0;}
.po-radio-opt{position:relative;}
.po-radio-opt input[type="radio"]{position:absolute;opacity:0;pointer-events:none;}
.po-radio-label{
    display:flex;align-items:center;justify-content:center;
    width:52px;height:28px;border-radius:.3rem;
    font-size:.5rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px;
    cursor:pointer;transition:all .15s;
    border:1.5px solid var(--bs-surface-200);
    background:var(--bs-card-bg);color:var(--bs-surface-400);
}
.po-radio-opt input[type="radio"]:checked + .po-radio-label.lbl-inherit{
    background:var(--bs-surface-100);border-color:var(--bs-surface-400);color:var(--bs-surface-600);
}
.po-radio-opt input[type="radio"]:checked + .po-radio-label.lbl-none{
    background:rgba(244,106,106,.08);border-color:var(--bs-ui-danger,#f46a6a);color:var(--bs-ui-danger,#f46a6a);
}
.po-radio-opt input[type="radio"]:checked + .po-radio-label.lbl-view{
    background:rgba(245,176,65,.08);border-color:var(--bs-ui-warning,#f5b041);color:var(--bs-ui-warning,#d4960a);
}
.po-radio-opt input[type="radio"]:checked + .po-radio-label.lbl-edit{
    background:rgba(80,165,241,.08);border-color:var(--bs-ui-info,#50a5f1);color:var(--bs-ui-info,#50a5f1);
}
.po-radio-opt input[type="radio"]:checked + .po-radio-label.lbl-full{
    background:rgba(52,195,143,.08);border-color:var(--bs-ui-success,#34c38f);color:var(--bs-ui-success,#34c38f);
}
.po-radio-label:hover{border-color:var(--bs-surface-300);background:var(--bs-surface-50);}

/* Filter/Search */
.po-filter-row{display:flex;gap:.4rem;align-items:center;margin-bottom:.5rem;flex-wrap:wrap;}
.po-search{border:1px solid var(--bs-surface-200);border-radius:.35rem;padding:.3rem .5rem .3rem 1.8rem;font-size:.68rem;background:var(--bs-card-bg);width:220px;position:relative;}
.po-search:focus{outline:none;border-color:var(--bs-gradient-start);box-shadow:0 0 0 2px rgba(102,126,234,.1);}
.po-search-wrap{position:relative;}
.po-search-wrap i{position:absolute;left:.5rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:var(--bs-surface-400);}
.po-quick-btn{font-size:.52rem;font-weight:600;padding:.2rem .5rem;border-radius:.25rem;border:1px solid var(--bs-surface-200);background:var(--bs-card-bg);color:var(--bs-surface-500);cursor:pointer;transition:all .15s;}
.po-quick-btn:hover{border-color:var(--bs-gradient-start);color:var(--bs-gradient-start);}

/* Bottom actions */
.po-bottom{display:flex;justify-content:flex-end;gap:.3rem;margin-top:.6rem;padding:.5rem 0;border-top:1px solid var(--bs-surface-100);}

@media(max-width:768px){
    .po-row{flex-direction:column;align-items:flex-start;gap:.3rem;}
    .po-radios{width:100%;justify-content:space-between;}
    .po-radio-label{width:auto;flex:1;}
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
    <div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.72rem;border-radius:.45rem" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.45rem;padding:.7rem"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.72rem;border-radius:.45rem" role="alert">
        <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.45rem;padding:.7rem"></button>
    </div>
    @endif

    <form action="{{ route('settings.permissions.users.update', $user) }}" method="POST" id="permissionForm">
        @csrf

        <div class="po-hdr">
            <div class="po-hdr-left">
                <h5><i class="bx bx-shield-alt-2" style="color:var(--bs-gradient-start)"></i> {{ $user->name }} — Permission Overrides</h5>
                <div class="po-roles">
                    <span style="font-size:.55rem;color:var(--bs-surface-500);font-weight:600">Roles:</span>
                    @foreach ($userRoles as $roleName)
                        <span class="po-role-badge">{{ $roleName }}</span>
                    @endforeach
                </div>
            </div>
            <div class="po-hdr-right">
                <a href="{{ route('users.index') }}" class="po-btn po-btn-back"><i class="bx bx-arrow-back"></i> Back</a>
                <button type="submit" class="po-btn po-btn-save"><i class="bx bx-save"></i> Save Overrides</button>
            </div>
        </div>

        {{-- Info + Legend --}}
        <div class="po-info">
            <div class="po-info-title"><i class="bx bx-info-circle"></i> About Permission Overrides</div>
            <ul class="po-info-list">
                <li>By default, users inherit permissions from their assigned roles</li>
                <li>Use overrides to grant or restrict access for this specific user</li>
                <li>Select "Inherit" to use the role's default permission</li>
                <li>Overridden permissions are marked with <span class="po-tag override" style="font-size:.5rem">OVERRIDE</span></li>
            </ul>
        </div>

        <div class="po-legend">
            <div class="po-legend-item"><span class="po-dot inherit"></span> Inherit: Use role permission</div>
            <div class="po-legend-item"><span class="po-dot none"></span> None: Explicitly deny access</div>
            <div class="po-legend-item"><span class="po-dot view"></span> View: Read-only access</div>
            <div class="po-legend-item"><span class="po-dot edit"></span> Edit: View and modify</div>
            <div class="po-legend-item"><span class="po-dot full"></span> Full: Complete access</div>
        </div>

        {{-- Filter Bar --}}
        <div class="po-filter-row">
            <div class="po-search-wrap">
                <i class="bx bx-search"></i>
                <input type="text" class="po-search" id="poSearch" placeholder="Search modules..." autocomplete="off">
            </div>
            <button type="button" class="po-quick-btn" onclick="setAllTo('inherit')">All Inherit</button>
            <button type="button" class="po-quick-btn" onclick="setAllTo('full')">All Full</button>
            <button type="button" class="po-quick-btn" onclick="setAllTo('none')">All None</button>
        </div>

        {{-- Permission Matrix --}}
        @foreach ($modulesByCategory as $category => $modules)
            <div class="po-category" data-category="{{ $category }}">
                <div class="po-cat-hdr"><i class="bx bx-folder-open"></i> {{ $category }}</div>

                @foreach ($modules as $module)
                    @php
                        $permission = $permissions[$module->slug];
                        $currentLevel = $permission['permission_level'];
                        $source = $permission['source'];
                        $isOverride = $source === 'override';
                    @endphp

                    <div class="po-row" data-module="{{ strtolower($module->name) }}">
                        <div class="po-row-info">
                            <div class="po-mod-name">
                                {{ $module->name }}
                                @if ($isOverride)
                                    <span class="po-tag override">OVERRIDE</span>
                                @else
                                    <span class="po-tag from-role">FROM ROLE</span>
                                @endif
                            </div>
                            @if ($module->description)
                                <div class="po-mod-desc">{{ $module->description }}</div>
                            @endif
                        </div>

                        <div class="po-radios">
                            <div class="po-radio-opt">
                                <input type="radio" name="permissions[{{ $module->slug }}]" value="inherit" id="p_{{ $module->slug }}_inherit"
                                       {{ !$isOverride ? 'checked' : '' }}>
                                <label for="p_{{ $module->slug }}_inherit" class="po-radio-label lbl-inherit">Inherit</label>
                            </div>
                            <div class="po-radio-opt">
                                <input type="radio" name="permissions[{{ $module->slug }}]" value="none" id="p_{{ $module->slug }}_none"
                                       {{ $isOverride && $currentLevel === 'none' ? 'checked' : '' }}>
                                <label for="p_{{ $module->slug }}_none" class="po-radio-label lbl-none">None</label>
                            </div>
                            <div class="po-radio-opt">
                                <input type="radio" name="permissions[{{ $module->slug }}]" value="view" id="p_{{ $module->slug }}_view"
                                       {{ $isOverride && $currentLevel === 'view' ? 'checked' : '' }}>
                                <label for="p_{{ $module->slug }}_view" class="po-radio-label lbl-view">View</label>
                            </div>
                            <div class="po-radio-opt">
                                <input type="radio" name="permissions[{{ $module->slug }}]" value="edit" id="p_{{ $module->slug }}_edit"
                                       {{ $isOverride && $currentLevel === 'edit' ? 'checked' : '' }}>
                                <label for="p_{{ $module->slug }}_edit" class="po-radio-label lbl-edit">Edit</label>
                            </div>
                            <div class="po-radio-opt">
                                <input type="radio" name="permissions[{{ $module->slug }}]" value="full" id="p_{{ $module->slug }}_full"
                                       {{ $isOverride && $currentLevel === 'full' ? 'checked' : '' }}>
                                <label for="p_{{ $module->slug }}_full" class="po-radio-label lbl-full">Full</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        <div class="po-bottom">
            <a href="{{ route('users.index') }}" class="po-btn po-btn-back"><i class="bx bx-x"></i> Cancel</a>
            <button type="submit" class="po-btn po-btn-save"><i class="bx bx-save"></i> Save Overrides</button>
        </div>
    </form>
@endsection

@section('script')
<script>
// Search filter
document.getElementById('poSearch')?.addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('.po-row').forEach(r => {
        r.style.display = (r.dataset.module || '').includes(q) ? '' : 'none';
    });
    // Show/hide category headers that have no visible rows
    document.querySelectorAll('.po-category').forEach(c => {
        const visible = c.querySelectorAll('.po-row[style=""], .po-row:not([style])').length;
        c.style.display = visible > 0 || q === '' ? '' : 'none';
    });
});

// Bulk set all permissions
function setAllTo(val) {
    if(!confirm('Set ALL modules to "' + val + '"?')) return;
    document.querySelectorAll('input[type="radio"][value="' + val + '"]').forEach(r => r.checked = true);
}

// Highlight changed rows
document.querySelectorAll('.po-radios input[type="radio"]').forEach(r => {
    r.addEventListener('change', function(){
        const row = this.closest('.po-row');
        row.style.background = 'rgba(102,126,234,.05)';
        setTimeout(() => row.style.background = '', 1500);
    });
});
</script>
@endsection
