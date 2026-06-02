@extends('layouts.master')

@section('title')
    System Settings
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    @include('partials.sl-filter-assets')
    <style>
        .st-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .st-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .st-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .st-page-hdr .st-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        .f-label { display:block;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.25rem }
        .f-input { border-radius:12px;border:1px solid rgba(0,0,0,.08);padding:.45rem .65rem;font-size:.78rem;width:100%;transition:all .15s;background:var(--bs-card-bg) }
        .f-input:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none }
        select.f-input { appearance:auto }

        .f-switch { display:flex;align-items:center;gap:.5rem;padding:.35rem 0 }
        .f-switch input[type="checkbox"] { width:36px;height:20px;appearance:none;background:var(--bs-surface-300);border-radius:20px;position:relative;cursor:pointer;transition:background .2s;border:none;outline:none }
        .f-switch input[type="checkbox"]::after { content:'';position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:#fff;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.15) }
        .f-switch input[type="checkbox"]:checked { background:linear-gradient(135deg,#d4af37,#e8c84a) }
        .f-switch input[type="checkbox"]:checked::after { transform:translateX(16px) }
        .f-switch .sw-label { font-size:.75rem;font-weight:600;color:var(--bs-body-color) }

        .ip-card { border-radius:12px;padding:.85rem 1rem;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.04));border:1px solid rgba(212,175,55,.12);margin-bottom:.65rem }
        .ip-card h6 { font-size:.78rem;font-weight:700;color:#b89730;margin:0 0 .35rem }
        .ip-card code { font-size:.75rem;background:rgba(0,0,0,.04);padding:.15rem .4rem;border-radius:6px }

        .net-input-row { display:flex;gap:.35rem;margin-bottom:.35rem }
        .net-input-row input { flex:1 }
        .net-rm { border:1px solid rgba(244,106,106,.2);background:rgba(244,106,106,.04);color:#c84646;border-radius:8px;padding:.3rem .5rem;cursor:pointer;font-size:.72rem;transition:all .15s }
        .net-rm:hover { background:rgba(244,106,106,.12) }

        .grp-card { margin-bottom:.65rem }
        .grp-title { display:flex;align-items:center;gap:.35rem;font-size:.82rem;font-weight:700;color:var(--bs-body-color) }
        .grp-title i { color:#d4af37;font-size:1rem }

        .f-help { font-size:.65rem;color:var(--bs-surface-500);margin-top:.2rem }
    </style>
@endsection

@section('content')
    <div class="st-page-hdr">
        <h5>
            <i class="bx bx-cog"></i> System Settings
            <span class="st-sub">Configuration</span>
        </h5>
        <a href="{{ route('settings.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Settings Hub
        </a>
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

    {{-- Current IP Info --}}
    {{-- Removed: IP card & test-network UI (CRM access is now device-token based) --}}

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf

        @foreach ($settings as $group => $groupSettings)
            <div class="ex-card sec-card grp-card">
                <div class="sec-hdr">
                    <h6>
                        <i class="bx bx-{{ $group === 'attendance' ? 'time-five' : 'cog' }}"></i>
                        {{ ucwords(str_replace('_', ' ', $group)) }} Settings
                    </h6>
                </div>
                <div class="sec-body" style="padding:.75rem">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem">
                        @foreach ($groupSettings as $setting)
                            @if($setting->key !== 'late_threshold_minutes')
                            <div style="{{ $setting->type === 'array' ? 'grid-column:1/-1' : '' }}">
                                <label class="f-label">
                                    {{ ucwords(str_replace('_', ' ', str_replace($group . '_', '', $setting->key))) }}
                                    @if ($setting->description)
                                        <i class="bx bx-info-circle" data-bs-toggle="tooltip" title="{{ $setting->description }}" style="cursor:help;opacity:.5"></i>
                                    @endif
                                </label>

                                @if ($setting->type === 'boolean')
                                    <div class="f-switch">
                                        <input type="checkbox" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" {{ $setting->value === 'true' ? 'checked' : '' }}>
                                        <span class="sw-label">{{ $setting->value === 'true' ? 'Enabled' : 'Disabled' }}</span>
                                    </div>
                                @elseif($setting->type === 'array' && $setting->key === 'office_networks')
                                    <div id="network-inputs">
                                        @php
                                            $networks = is_string($setting->value) ? explode(',', $setting->value) : [$setting->value];
                                        @endphp
                                        @foreach ($networks as $index => $network)
                                            <div class="net-input-row network-input-group">
                                                <input type="text" class="f-input" name="settings[{{ $setting->key }}][]" value="{{ trim($network) }}" placeholder="192.168.1.0/24">
                                                @if ($index > 0)
                                                    <button class="net-rm" type="button" onclick="removeNetwork(this)"><i class="bx bx-trash"></i></button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="act-btn a-primary" style="margin-top:.2rem" onclick="addNetwork()"><i class="bx bx-plus"></i> Add Network</button>
                                    <div class="f-help">Enter IP addresses or CIDR ranges (e.g., 192.168.1.0/24)</div>
                                @elseif(str_contains($setting->key, 'zoom') || str_contains($setting->key, 'did'))
                                    <div style="background:rgba(0,0,0,.04);border:1px solid rgba(0,0,0,.08);border-radius:8px;padding:.65rem;font-family:monospace;font-size:.72rem;max-height:200px;overflow-y:auto;word-break:break-all">
                                        {{ $setting->value }}
                                    </div>
                                    <div class="f-help">Read-only — managed by Zoom integration</div>
                                @else
                                    <input type="{{ in_array($setting->key, ['office_start_time','office_end_time','late_time']) ? 'time' : 'text' }}"
                                        class="f-input" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]"
                                        value="{{ $setting->value }}"
                                        {{ $setting->key === 'shift_duration_hours' ? 'readonly style=background:var(--bs-surface-100);cursor:not-allowed title=Auto-calculated from start/end time' : '' }}
                                        {{ in_array($setting->key, ['office_start_time','office_end_time']) ? 'onchange=calcShiftHours()' : '' }}>
                                @endif

                                @if ($setting->description && $setting->type !== 'boolean' && $setting->type !== 'array')
                                    <div class="f-help">{{ $setting->description }}</div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div style="display:flex;justify-content:flex-end;gap:.45rem;margin-top:.35rem">
            <button type="button" class="act-btn a-danger" onclick="resetForm()"><i class="bx bx-reset"></i> Reset</button>
            <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.32rem .85rem">
                <i class="bx bx-save" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Save Settings
            </button>
        </div>
    </form>

    {{-- ── Allowed Devices ─────────────────────────────────────────── --}}
    @if(auth()->user()->canViewModule('allowed-devices'))
    <div class="ex-card sec-card" style="margin-top:.65rem">
        <div class="sec-hdr">
            <h6><i class="bx bx-devices"></i> Allowed Devices</h6>
        </div>
        <div class="sec-body" style="padding:.75rem">

            {{-- Manual Add --}}
            <form action="{{ route('settings.devices.store') }}" method="POST" style="margin-bottom:1rem">
                @csrf
                <div style="background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.04));border:1px solid rgba(212,175,55,.12);border-radius:12px;padding:1rem;margin-bottom:1rem">
                    <p class="f-label" style="margin-bottom:.5rem">Approve a Device</p>
                    <p style="font-size:.75rem;color:var(--bs-surface-500);margin-bottom:1rem">User sees their <strong>Device Token</strong> when they try to access. They send it to you. Paste it here and approve:</p>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:2;min-width:200px">
                            <label class="f-label">Device Token (UUID)</label>
                            <input type="text" name="device_token" class="f-input" placeholder="Paste UUID user sends you" required>
                        </div>
                        <div style="flex:1;min-width:120px">
                            <label class="f-label">Assign User</label>
                            <select name="user_id" class="f-input">
                                <option value="">— Select User —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;min-width:120px">
                            <label class="f-label">Device Label</label>
                            <input type="text" name="label" class="f-input" placeholder="e.g. US Laptop" required>
                        </div>
                        <div>
                            <button class="act-btn a-success" style="white-space:nowrap"><i class="bx bx-check"></i> Approve</button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Approved --}}
            <p class="f-label" style="margin-bottom:.4rem">Approved Devices</p>
            @if($approved->isEmpty())
                <p style="font-size:.75rem;color:var(--bs-surface-500)">No approved devices yet.</p>
            @else
                <div class="table-responsive" style="margin-bottom:1rem">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>User</th><th>Label</th><th>Last Seen</th><th>IP</th><th style="width:1px"></th></tr></thead>
                        <tbody>
                            @foreach($approved as $d)
                            {{-- view row --}}
                            <tr id="dev-row-{{ $d->id }}">
                                <td>
                                    @if($d->user)
                                        <strong>{{ $d->user->name }}</strong><br>
                                        <small style="color:var(--bs-surface-500)">{{ $d->name ?? '(no person label)' }}</small>
                                    @else
                                        <span style="color:var(--bs-surface-500)">{{ $d->name ?? '— Unassigned' }}</span>
                                    @endif
                                </td>
                                <td>{{ $d->label }}</td>
                                <td style="color:var(--bs-surface-500)">{{ $d->last_seen_at ? $d->last_seen_at->diffForHumans() : '—' }}</td>
                                <td style="color:var(--bs-surface-500)">{{ $d->last_seen_ip ?? '—' }}</td>
                                <td style="white-space:nowrap;text-align:right">
                                    <button class="act-btn" style="font-size:.68rem;padding:.2rem .5rem" onclick="devEditOpen({{ $d->id }})">
                                        <i class="bx bx-pencil" style="font-size:.8rem;vertical-align:middle"></i> Edit
                                    </button>
                                    <form action="{{ route('settings.devices.disable', $d) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="act-btn a-warning" style="font-size:.68rem;padding:.2rem .5rem">Disable</button>
                                    </form>
                                    <form action="{{ route('settings.devices.destroy', $d) }}" method="POST" style="display:inline" onsubmit="return confirm('Remove this device?')">
                                        @csrf @method('DELETE')
                                        <button class="act-btn a-danger" style="font-size:.68rem;padding:.2rem .5rem">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            {{-- edit row (approved table) --}}
                            <tr id="dev-edit-{{ $d->id }}" style="display:none">
                                <td colspan="5" style="padding:.6rem .75rem;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.03));border-bottom:2px solid rgba(212,175,55,.2)">
                                    <form action="{{ route('settings.devices.update', $d) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:.5rem;align-items:end">
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Assigned User</div>
                                                <select name="user_id" class="f-input" style="margin:0">
                                                    <option value="">— None —</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ $d->user_id === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Label / Device</div>
                                                <input type="text" name="label" class="f-input" value="{{ $d->label }}" placeholder="e.g. Office PC" style="margin:0" required>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Status</div>
                                                <select name="status" class="f-input" style="margin:0">
                                                    <option value="approved" {{ $d->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="disabled" {{ $d->status === 'disabled' ? 'selected' : '' }}>Disabled</option>
                                                    <option value="pending"  {{ $d->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                                    <option value="rejected" {{ $d->status === 'rejected' ? 'selected' : '' }}>Rejected (permanent block)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Device Token (UUID)</div>
                                                <input type="text" name="device_token" class="f-input" value="{{ $d->device_token }}" style="margin:0;font-family:monospace;font-size:.7rem" required>
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:.3rem;margin-top:.5rem;justify-content:flex-end">
                                            <button class="pipe-pill-apply" style="font-size:.72rem;padding:.28rem .7rem">
                                                <i class="bx bx-check" style="font-size:.85rem;vertical-align:middle"></i> Save
                                            </button>
                                            <button type="button" class="act-btn a-danger" style="font-size:.72rem;padding:.28rem .7rem" onclick="devEditClose({{ $d->id }})">
                                                <i class="bx bx-x" style="font-size:.85rem;vertical-align:middle"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Disabled --}}
            @if($disabled->count())
                <p class="f-label" style="margin-bottom:.4rem">Disabled Devices</p>
                <div class="table-responsive" style="margin-bottom:1rem">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>User</th><th>Label</th><th style="width:1px"></th></tr></thead>
                        <tbody>
                            @foreach($disabled as $d)
                            <tr id="dev-row-{{ $d->id }}">
                                <td>
                                    @if($d->user)
                                        <strong>{{ $d->user->name }}</strong><br>
                                        <small style="color:var(--bs-surface-500)">{{ $d->name ?? '(no person label)' }}</small>
                                    @else
                                        <span style="color:var(--bs-surface-500)">{{ $d->name ?? '— Unassigned' }}</span>
                                    @endif
                                </td>
                                <td>{{ $d->label }}</td>
                                <td style="white-space:nowrap;text-align:right">
                                    <button class="act-btn" style="font-size:.68rem;padding:.2rem .5rem" onclick="devEditOpen({{ $d->id }})">
                                        <i class="bx bx-pencil" style="font-size:.8rem;vertical-align:middle"></i> Edit
                                    </button>
                                    <form action="{{ route('settings.devices.enable', $d) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="act-btn a-success" style="font-size:.68rem;padding:.2rem .5rem">Re-enable</button>
                                    </form>
                                    <form action="{{ route('settings.devices.destroy', $d) }}" method="POST" style="display:inline" onsubmit="return confirm('Remove this device?')">
                                        @csrf @method('DELETE')
                                        <button class="act-btn a-danger" style="font-size:.68rem;padding:.2rem .5rem">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="dev-edit-{{ $d->id }}" style="display:none">
                                <td colspan="3" style="padding:.6rem .75rem;background:linear-gradient(135deg,rgba(212,175,55,.06),rgba(85,110,230,.03));border-bottom:2px solid rgba(212,175,55,.2)">
                                    <form action="{{ route('settings.devices.update', $d) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:.5rem;align-items:end">
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Assigned User</div>
                                                <select name="user_id" class="f-input" style="margin:0">
                                                    <option value="">— None —</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ $d->user_id === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Label / Device</div>
                                                <input type="text" name="label" class="f-input" value="{{ $d->label }}" placeholder="e.g. Office PC" style="margin:0" required>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Status</div>
                                                <select name="status" class="f-input" style="margin:0">
                                                    <option value="approved" {{ $d->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="disabled" {{ $d->status === 'disabled' ? 'selected' : '' }}>Disabled</option>
                                                    <option value="pending"  {{ $d->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                                    <option value="rejected" {{ $d->status === 'rejected' ? 'selected' : '' }}>Rejected (permanent block)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="f-label" style="margin-bottom:.2rem">Device Token (UUID)</div>
                                                <input type="text" name="device_token" class="f-input" value="{{ $d->device_token }}" style="margin:0;font-family:monospace;font-size:.7rem" required>
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:.3rem;margin-top:.5rem;justify-content:flex-end">
                                            <button class="pipe-pill-apply" style="font-size:.72rem;padding:.28rem .7rem">
                                                <i class="bx bx-check" style="font-size:.85rem;vertical-align:middle"></i> Save
                                            </button>
                                            <button type="button" class="act-btn a-danger" style="font-size:.72rem;padding:.28rem .7rem" onclick="devEditClose({{ $d->id }})">
                                                <i class="bx bx-x" style="font-size:.85rem;vertical-align:middle"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Rejected (permanently blocked) --}}
            @if($rejected->count())
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem">
                    <p class="f-label" style="color:#dc3545;margin-bottom:0">Rejected Devices <span style="font-weight:400;font-size:.7rem;color:var(--bs-surface-500)">(permanently blocked)</span></p>
                    <button type="button" class="act-btn" style="font-size:.68rem;padding:.2rem .5rem" onclick="toggleRejected()" id="rejected-toggle-btn">
                        <i class="bx bx-chevron-down" id="rejected-toggle-icon" style="font-size:.8rem;vertical-align:middle"></i> Show
                    </button>
                </div>
                <div class="table-responsive" style="margin-bottom:1rem;display:none !important" id="rejected-table-container">
                    <table class="table table-sm" style="font-size:.75rem">
                        <thead><tr><th>Person</th><th>Label</th><th>Token</th><th style="width:1px"></th></tr></thead>
                        <tbody>
                            @foreach($rejected as $d)
                            <tr style="opacity:.7">
                                <td>{{ $d->name ?? '—' }}</td>
                                <td>{{ $d->label }}</td>
                                <td style="font-family:monospace;font-size:.68rem">{{ $d->device_token }}</td>
                                <td style="white-space:nowrap;text-align:right;display:flex;gap:.2rem;justify-content:flex-end">
                                    <form action="{{ route('settings.devices.update', $d) }}" method="POST" style="display:inline" onsubmit="return confirm('Restore this device to Approved?')">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $d->name }}">
                                        <input type="hidden" name="label" value="{{ $d->label }}">
                                        <input type="hidden" name="status" value="approved">
                                        <input type="hidden" name="device_token" value="{{ $d->device_token }}">
                                        <button class="act-btn a-success" style="font-size:.68rem;padding:.2rem .5rem">Restore</button>
                                    </form>
                                    <form action="{{ route('settings.devices.destroy', $d) }}" method="POST" style="display:inline" onsubmit="return confirm('Permanently delete this device? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="act-btn a-danger" style="font-size:.68rem;padding:.2rem .5rem">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
    @endif
@endsection

@section('script')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

        function addNetwork() {
            const c = document.getElementById('network-inputs');
            const d = document.createElement('div');
            d.className = 'net-input-row network-input-group';
            d.innerHTML = '<input type="text" class="f-input" name="settings[office_networks][]" placeholder="192.168.1.0/24"><button class="net-rm" type="button" onclick="removeNetwork(this)"><i class="bx bx-trash"></i></button>';
            c.appendChild(d);
        }

        function removeNetwork(btn) { btn.closest('.network-input-group').remove(); }
        function resetForm() { if (confirm('Reset all changes?')) location.reload(); }

        function calcShiftHours() {
            var start = document.getElementById('office_start_time');
            var end   = document.getElementById('office_end_time');
            var dur   = document.getElementById('shift_duration_hours');
            if (!start || !end || !dur) return;
            var sv = start.value, ev = end.value;
            if (!sv || !ev) return;
            var sm = parseInt(sv.split(':')[0])*60 + parseInt(sv.split(':')[1]);
            var em = parseInt(ev.split(':')[0])*60 + parseInt(ev.split(':')[1]);
            var diff = em - sm;
            if (diff <= 0) return;
            dur.value = (diff / 60).toFixed(1).replace(/\.0$/, '');
        }
        // Run on load so it reflects current values
        document.addEventListener('DOMContentLoaded', calcShiftHours);

        function toggleDevEdit(id) {
            var row = document.getElementById('dev-edit-' + id);
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
        function devEditOpen(id) {
            document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
            var editRow = document.getElementById('dev-edit-' + id);
            editRow.style.display = 'table-row';
            editRow.querySelector('input').focus();
        }
        function devEditClose(id) {
            document.getElementById('dev-edit-' + id).style.display = 'none';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
        });
        function devEditOpen(id) {
            // close any other open edit rows first
            document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
            var editRow = document.getElementById('dev-edit-' + id);
            editRow.style.display = 'table-row';
            editRow.querySelector('input').focus();
        }
        function devEditClose(id) {
            document.getElementById('dev-edit-' + id).style.display = 'none';
        }
        // Escape key closes any open edit row
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.querySelectorAll('[id^="dev-edit-"]').forEach(r => r.style.display = 'none');
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    const lbl = this.nextElementSibling;
                    if (lbl) lbl.textContent = this.checked ? 'Enabled' : 'Disabled';
                });
            });
        });

        function toggleRejected() {
            const container = document.getElementById('rejected-table-container');
            const btn = document.getElementById('rejected-toggle-btn');
            
            if (container.style.display === 'none') {
                container.style.display = 'block';
                btn.innerHTML = '<i class="bx bx-chevron-up" style="font-size:.8rem;vertical-align:middle"></i> Hide';
            } else {
                container.style.display = 'none';
                btn.innerHTML = '<i class="bx bx-chevron-down" style="font-size:.8rem;vertical-align:middle"></i> Show';
            }
        }
    </script>
@endsection
