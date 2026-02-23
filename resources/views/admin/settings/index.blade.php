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
    <div class="ip-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6><i class="bx bx-globe me-1"></i> Your IP Address</h6>
                <div style="font-size:.75rem;color:var(--bs-body-color)">
                    <strong>Current IP:</strong> <code id="current-ip">{{ app(\App\Services\IpDetectionService::class)->getBestIpForAttendance() }}</code>
                    <span id="ip-type-badge" class="v-badge v-warn" style="margin-left:.35rem">Checking...</span>
                </div>
                <div id="network-status" style="font-size:.72rem;color:var(--bs-surface-500);margin-top:.2rem">Checking network status...</div>
                <div id="ip-details" class="d-none" style="margin-top:.35rem">
                    <small style="font-size:.68rem;color:var(--bs-surface-500)"><div id="all-ips"></div></small>
                </div>
            </div>
            <div style="display:flex;gap:.35rem">
                <button class="act-btn a-primary" onclick="testNetworkConnection()"><i class="bx bx-refresh"></i> Test</button>
                <button class="act-btn a-info" onclick="toggleIpDetails()"><i class="bx bx-info-circle"></i> Details</button>
            </div>
        </div>

        <div id="localhost-help" class="d-none" style="margin-top:.65rem;padding:.55rem .75rem;background:rgba(241,180,76,.06);border:1px solid rgba(241,180,76,.12);border-radius:10px;font-size:.72rem">
            <strong style="color:#b87a14"><i class="bx bx-error-triangle"></i> Dev Environment</strong>
            <div style="margin-top:.25rem;color:var(--bs-body-color)">Running on localhost. Visit <a href="https://whatismyipaddress.com" target="_blank" style="color:#d4af37">whatismyipaddress.com</a> from office, or add <code>127.0.0.1</code> for testing.</div>
            <button class="act-btn a-warn" style="margin-top:.35rem" onclick="addLocalhostToSettings()"><i class="bx bx-plus"></i> Add 127.0.0.1</button>
        </div>
    </div>

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
                                @else
                                    <input type="{{ $setting->key === 'office_start_time' || $setting->key === 'late_time' ? 'time' : 'text' }}"
                                        class="f-input" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
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
@endsection

@section('script')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

        function testNetworkConnection() {
            const statusEl = document.getElementById('network-status');
            const ipEl = document.getElementById('current-ip');
            const badgeEl = document.getElementById('ip-type-badge');
            statusEl.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Testing...';

            fetch('{{ route("settings.test-network") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                ipEl.textContent = data.current_ip;
                if (data.is_in_office_network) {
                    statusEl.innerHTML = '<i class="bx bx-check-circle" style="color:#1a8754"></i> In Office Network (' + data.matched_network + ')';
                    badgeEl.textContent = 'Office'; badgeEl.className = 'v-badge v-green';
                } else {
                    statusEl.innerHTML = '<i class="bx bx-x-circle" style="color:#c84646"></i> Outside Office Network';
                    badgeEl.textContent = data.is_localhost ? 'Localhost' : 'External';
                    badgeEl.className = data.is_localhost ? 'v-badge v-warn' : 'v-badge v-red';
                }
                document.getElementById('localhost-help').style.display = data.is_localhost ? 'block' : 'none';
                const allIpsDiv = document.getElementById('all-ips');
                let html = '<strong>Detected IPs:</strong><br>';
                for (const [k, v] of Object.entries(data.all_detected_ips)) { if (v) html += k + ': <code>' + v + '</code><br>'; }
                html += '<strong>Networks:</strong> <code>' + data.configured_networks.join(', ') + '</code>';
                allIpsDiv.innerHTML = html;
            })
            .catch(() => {
                statusEl.innerHTML = '<i class="bx bx-error" style="color:#f46a6a"></i> Test Failed';
                badgeEl.textContent = 'Error'; badgeEl.className = 'v-badge v-red';
            });
        }

        function toggleIpDetails() {
            const d = document.getElementById('ip-details');
            d.classList.toggle('d-none');
        }

        function addLocalhostToSettings() {
            const inputs = document.querySelectorAll('input[name="settings[office_networks][]"]');
            let has = false;
            inputs.forEach(i => { if (i.value.trim() === '127.0.0.1') has = true; });
            if (!has) { addNetwork(); const all = document.querySelectorAll('input[name="settings[office_networks][]"]'); all[all.length-1].value = '127.0.0.1'; alert('Added 127.0.0.1. Save settings!'); }
            else alert('127.0.0.1 already exists.');
        }

        function addNetwork() {
            const c = document.getElementById('network-inputs');
            const d = document.createElement('div');
            d.className = 'net-input-row network-input-group';
            d.innerHTML = '<input type="text" class="f-input" name="settings[office_networks][]" placeholder="192.168.1.0/24"><button class="net-rm" type="button" onclick="removeNetwork(this)"><i class="bx bx-trash"></i></button>';
            c.appendChild(d);
        }

        function removeNetwork(btn) { btn.closest('.network-input-group').remove(); }
        function resetForm() { if (confirm('Reset all changes?')) location.reload(); }

        document.addEventListener('DOMContentLoaded', function() {
            testNetworkConnection();
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    const lbl = this.nextElementSibling;
                    if (lbl) lbl.textContent = this.checked ? 'Enabled' : 'Disabled';
                });
            });
        });
    </script>
@endsection
