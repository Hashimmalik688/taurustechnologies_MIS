@extends('layouts.master')

@section('title')
    Zoom Phone
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .zp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .zp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .zp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .zp-page-hdr .zp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        /* Layout grid */
        .zp-grid { display:grid;grid-template-columns:340px 1fr;gap:.75rem;min-height:calc(100vh - 140px) }
        @media(max-width:991px) { .zp-grid { grid-template-columns:1fr } }

        /* Cards */
        .zp-card { background:#fff;border-radius:.55rem;border:1px solid rgba(0,0,0,.06);box-shadow:0 1px 3px rgba(0,0,0,.03);overflow:hidden }
        .zp-card-hdr { padding:.65rem .85rem;border-bottom:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between }
        .zp-card-hdr h6 { margin:0;font-size:.82rem;font-weight:700;display:flex;align-items:center;gap:.35rem }
        .zp-card-hdr h6 i { color:var(--bs-gold,#d4af37);font-size:1.1rem }
        .zp-card-body { padding:.75rem .85rem }

        /* Quick dial */
        .zp-dial-input { display:flex;gap:.4rem }
        .zp-dial-input input { flex:1;font-size:.78rem;padding:.4rem .65rem;border:1px solid rgba(0,0,0,.12);border-radius:.4rem;background:var(--bs-body-bg,#fff);color:var(--bs-surface-900) }
        .zp-dial-input input:focus { outline:none;border-color:var(--bs-gold,#d4af37);box-shadow:0 0 0 2px rgba(212,175,55,.15) }
        .zp-dial-btn { padding:.4rem .75rem;font-size:.78rem;font-weight:600;border:none;border-radius:.4rem;background:var(--bs-gold,#d4af37);color:#fff;cursor:pointer;display:flex;align-items:center;gap:.25rem;white-space:nowrap }
        .zp-dial-btn:hover { background:#c9a532 }
        .zp-dial-btn i { font-size:1rem }

        /* Search */
        .zp-search { width:100%;font-size:.78rem;padding:.4rem .65rem;border:1px solid rgba(0,0,0,.12);border-radius:.4rem;background:var(--bs-body-bg,#fff);color:var(--bs-surface-900);margin-bottom:.5rem }
        .zp-search:focus { outline:none;border-color:var(--bs-gold,#d4af37);box-shadow:0 0 0 2px rgba(212,175,55,.15) }

        /* Lead list */
        .zp-lead-item { padding:.5rem .65rem;border-bottom:1px solid rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;gap:.4rem;cursor:pointer;transition:background .15s }
        .zp-lead-item:hover { background:rgba(212,175,55,.04) }
        .zp-lead-item:last-child { border-bottom:none }
        .zp-lead-name { font-size:.76rem;font-weight:600;color:var(--bs-surface-900) }
        .zp-lead-phone { font-size:.68rem;color:var(--bs-surface-500) }
        .zp-lead-meta { font-size:.62rem;color:var(--bs-surface-400) }
        .zp-call-btn { padding:.25rem .5rem;font-size:.68rem;font-weight:600;border:none;border-radius:.35rem;background:rgba(52,195,143,.12);color:#1a8754;cursor:pointer;display:flex;align-items:center;gap:.2rem;white-space:nowrap }
        .zp-call-btn:hover { background:rgba(52,195,143,.25) }

        /* Recent calls */
        .zp-call-item { padding:.45rem .65rem;border-bottom:1px solid rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;gap:.4rem }
        .zp-call-item:last-child { border-bottom:none }
        .zp-call-caller { font-size:.74rem;font-weight:600;color:var(--bs-surface-900) }
        .zp-call-callee { font-size:.66rem;color:var(--bs-surface-500) }
        .zp-call-time { font-size:.64rem;color:var(--bs-surface-400);text-align:right }
        .zp-call-dur { font-size:.6rem;font-weight:600;padding:.12rem .35rem;border-radius:4px;background:rgba(52,195,143,.1);color:#1a8754;display:inline-block }
        .zp-redial-btn { padding:.2rem .45rem;font-size:.62rem;border:1px solid rgba(0,0,0,.08);border-radius:.3rem;background:transparent;color:var(--bs-surface-600);cursor:pointer;margin-top:.25rem }
        .zp-redial-btn:hover { background:rgba(212,175,55,.06);border-color:var(--bs-gold,#d4af37);color:var(--bs-gold) }

        /* Embed container */
        .zp-embed-wrap { position:relative;min-height:600px;display:flex;flex-direction:column }
        .zp-embed-frame { flex:1;width:100%;min-height:550px;border:0 }
        .zp-embed-loading { position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--bs-body-bg,#fff) }
        .zp-embed-loading .spinner { width:36px;height:36px;border:3px solid rgba(0,0,0,.08);border-top-color:var(--bs-gold,#d4af37);border-radius:50%;animation:spin .7s linear infinite }
        @keyframes spin { to { transform:rotate(360deg) } }
        .zp-embed-loading p { margin:.75rem 0 0;font-size:.78rem;color:var(--bs-surface-500) }

        /* Status badge */
        .zp-status { font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;text-transform:uppercase;letter-spacing:.4px }
        .zp-status-ok { background:rgba(52,195,143,.12);color:#1a8754 }
        .zp-status-warn { background:rgba(241,180,76,.12);color:#b87a14 }
        .zp-status-err { background:rgba(244,106,106,.12);color:#c84646 }

        /* Empty state */
        .zp-empty { text-align:center;padding:2rem 1rem;color:var(--bs-surface-400) }
        .zp-empty i { font-size:2.5rem;opacity:.3;margin-bottom:.5rem }
        .zp-empty p { font-size:.72rem }

        /* Scrollable lists */
        .zp-scroll { max-height:280px;overflow-y:auto }
        .zp-scroll::-webkit-scrollbar { width:4px }
        .zp-scroll::-webkit-scrollbar-thumb { background:rgba(0,0,0,.1);border-radius:4px }

        /* Dark theme */
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zp-card {
            background:rgba(15,23,42,.6);border-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zp-card-hdr {
            border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zp-lead-item:hover,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zp-call-item:hover {
            background:rgba(255,255,255,.03);
        }
    </style>
@endsection

@section('content')
    <div class="zp-page-hdr">
        <h5>
            <i class="bx bx-phone"></i> Zoom Phone
            <span class="zp-sub">Smart Embed &bull; Click-to-dial from CRM</span>
        </h5>
        <div style="display:flex;gap:.5rem;align-items:center">
            <a href="{{ route('settings.reports.zoom-logs') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
                <i class="bx bx-bar-chart-alt-2"></i> Call Logs
            </a>
        </div>
    </div>

    <div class="zp-grid">
        {{-- LEFT PANEL --}}
        <div>
            {{-- Quick Dial --}}
            <div class="zp-card" style="margin-bottom:.65rem">
                <div class="zp-card-hdr">
                    <h6><i class="bx bx-dialpad-alt"></i> Quick Dial</h6>
                </div>
                <div class="zp-card-body">
                    <div class="zp-dial-input">
                        <input type="text" id="dialNumber" placeholder="Enter phone number…" onkeypress="if(event.key==='Enter')dialNumber()">
                        <button class="zp-dial-btn" onclick="dialNumber()">
                            <i class="bx bx-phone-call"></i> Call
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lead Search --}}
            <div class="zp-card" style="margin-bottom:.65rem">
                <div class="zp-card-hdr">
                    <h6><i class="bx bx-search"></i> Search Leads</h6>
                </div>
                <div class="zp-card-body">
                    <input type="text" class="zp-search" id="leadSearch" placeholder="Name, phone number…" oninput="searchLeads(this.value)">
                    <div id="leadResults" class="zp-scroll">
                        <div class="zp-empty">
                            <i class="bx bx-search"></i>
                            <p>Type to search leads</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Calls --}}
            <div class="zp-card">
                <div class="zp-card-hdr">
                    <h6><i class="bx bx-history"></i> Recent Calls</h6>
                    <a href="{{ route('settings.reports.zoom-logs') }}" style="font-size:.65rem;color:var(--bs-gold,#d4af37);text-decoration:none;font-weight:600">View All →</a>
                </div>
                <div class="zp-scroll">
                    @forelse($recentCalls as $call)
                        <div class="zp-call-item">
                            <div style="flex:1;min-width:0">
                                <div class="zp-call-caller">{{ $call->caller_name ?: $call->caller_number ?: '—' }}</div>
                                <div class="zp-call-callee">→ {{ $call->callee_name ?: $call->callee_number ?: '—' }}</div>
                                @if($call->callee_number)
                                    <button class="zp-redial-btn" onclick="dialNumber('{{ $call->callee_number }}')">
                                        <i class="bx bx-revision"></i> Redial
                                    </button>
                                @endif
                            </div>
                            <div>
                                <div class="zp-call-time">{{ $call->call_start_time ? $call->call_start_time->format('M d, h:i A') : '—' }}</div>
                                @if($call->duration_seconds)
                                    <div class="zp-call-dur" style="margin-top:.2rem">{{ gmdate('i:s', $call->duration_seconds) }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="zp-empty">
                            <i class="bx bx-phone-off"></i>
                            <p>No recent calls</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL — Zoom Phone --}}
        <div class="zp-card">
            <div class="zp-card-hdr">
                <h6><i class="bx bx-phone"></i> Zoom Phone</h6>
                <div style="display:flex;gap:.4rem;align-items:center">
                    <span id="zoomStatus" class="zp-status zp-status-ok">Ready</span>
                </div>
            </div>
            <div style="padding:0;position:relative;min-height:600px">
                {{-- Zoom Phone Web Client --}}
                <iframe
                    id="zoomPhoneFrame"
                    src="https://app.zoom.us/wc/phone"
                    style="width:100%;height:600px;border:0;border-radius:0 0 .55rem .55rem"
                    allow="microphone;camera;autoplay;clipboard-write;clipboard-read"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-forms allow-popups-to-escape-sandbox allow-downloads"
                ></iframe>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('zoomStatus').className = 'zp-status zp-status-ok';
            document.getElementById('zoomStatus').textContent = 'Ready';
        });

        // ── Override global zoomDial on this page to use the full-page iframe ─
        window.zoomDial = function(number) {
            number = (number || document.getElementById('dialNumber').value || '').replace(/[^\d+]/g, '');
            if (!number) return;

            document.getElementById('dialNumber').value = number;

            const statusEl = document.getElementById('zoomStatus');
            statusEl.className = 'zp-status zp-status-warn';
            statusEl.textContent = 'Dialing…';

            const frame = document.getElementById('zoomPhoneFrame');
            if (frame) {
                const sendDial = () => {
                    frame.contentWindow.postMessage({ type: 'dial', number: number }, 'https://app.zoom.us');
                };
                if (frame.dataset.zpwReady === 'true') {
                    sendDial();
                } else {
                    frame.addEventListener('load', () => {
                        frame.dataset.zpwReady = 'true';
                        setTimeout(sendDial, 600);
                    }, { once: true });
                }
            }

            setTimeout(() => {
                statusEl.className = 'zp-status zp-status-ok';
                statusEl.textContent = 'Ready';
            }, 3000);
        };

        // ── Convenience alias ────────────────────────────────────────────
        function dialNumber(number) { window.zoomDial(number); }

        // ── Mark iframe as ready when it loads ───────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            const frame = document.getElementById('zoomPhoneFrame');
            if (frame) {
                frame.addEventListener('load', () => { frame.dataset.zpwReady = 'true'; });
            }
        });

        // ── Lead Search ─────────────────────────────────────────────────
        let searchTimeout;
        function searchLeads(query) {
            clearTimeout(searchTimeout);
            const container = document.getElementById('leadResults');

            if (query.length < 2) {
                container.innerHTML = '<div class="zp-empty"><i class="bx bx-search"></i><p>Type to search leads</p></div>';
                return;
            }

            container.innerHTML = '<div style="text-align:center;padding:1rem"><div class="spinner" style="width:20px;height:20px;margin:0 auto"></div></div>';

            searchTimeout = setTimeout(async () => {
                try {
                    const resp = await fetch(`{{ route('zoom.phone.search-leads') }}?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const leads = await resp.json();

                    if (!leads.length) {
                        container.innerHTML = '<div class="zp-empty"><i class="bx bx-user-x"></i><p>No leads found</p></div>';
                        return;
                    }

                    container.innerHTML = leads.map(lead => {
                        const phone = lead.phone_number || '—';
                        const phone2 = lead.secondary_phone_number || '';
                        return `
                            <div class="zp-lead-item">
                                <div style="min-width:0;flex:1">
                                    <div class="zp-lead-name">${lead.cn_name || '—'}</div>
                                    <div class="zp-lead-phone">${phone}${phone2 ? ' / ' + phone2 : ''}</div>
                                    <div class="zp-lead-meta">${lead.state || ''} ${lead.carrier_name ? '• ' + lead.carrier_name : ''}</div>
                                </div>
                                <div style="display:flex;flex-direction:column;gap:.2rem">
                                    ${phone !== '—' ? `<button class="zp-call-btn" onclick="dialNumber('${phone}')"><i class="bx bx-phone-call"></i> Call</button>` : ''}
                                    ${phone2 ? `<button class="zp-call-btn" onclick="dialNumber('${phone2}')" style="background:rgba(80,165,241,.12);color:#2b81c9"><i class="bx bx-phone"></i> Alt</button>` : ''}
                                </div>
                            </div>
                        `;
                    }).join('');
                } catch (e) {
                    console.error('Lead search failed:', e);
                    container.innerHTML = '<div class="zp-empty"><i class="bx bx-error"></i><p>Search failed</p></div>';
                }
            }, 300);
        }
    </script>
@endsection
