@extends('layouts.master')

@section('title')
    My Phone
@endsection

@section('css')
<style>
    #page-content .container-fluid,
    #page-content { padding: 0 !important; }

    .zse-wrap {
        width: 100%;
        height: calc(100vh - 90px);
        min-height: 580px;
        background: #f4f5f7;
        display: flex;
        align-items: stretch;
    }

    #zoom-embeddable-phone-iframe {
        flex: 1;
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    /* Hide floating phone FAB — we ARE the phone page */
    .zpw-fab, .zpw-panel { display: none !important; }

    /* Token status bar */
    .zt-bar{display:flex;align-items:center;justify-content:space-between;padding:.4rem .85rem;font-size:.72rem;gap:.5rem;flex-wrap:wrap;border-bottom:1px solid rgba(0,0,0,.06);background:#fff;flex-shrink:0}
    .zt-bar.warn{background:#fff3cd;border-bottom-color:#ffc107}
    .zt-bar .zt-msg{display:flex;align-items:center;gap:.4rem;color:#4a5568}
    .zt-bar.warn .zt-msg{color:#856404;font-weight:600}
    .zt-btn-reauth{
        display:inline-flex;align-items:center;gap:.35rem;
        padding:.3rem .85rem;border-radius:7px;font-size:.72rem;font-weight:700;
        text-decoration:none;border:none;cursor:pointer;
        background:linear-gradient(135deg,#1a73e8,#0f5ecb);
        color:#fff;box-shadow:0 2px 6px rgba(26,115,232,.35);
        transition:all .15s;
        white-space:nowrap;
    }
    .zt-btn-reauth:hover{background:linear-gradient(135deg,#1558b0,#0a46a3);color:#fff;box-shadow:0 3px 10px rgba(26,115,232,.45);transform:translateY(-1px)}
    .zt-btn-reauth i{font-size:.95rem}
    /* Caller ID selector */
    .zt-cid-wrap{display:flex;align-items:center;gap:.4rem;font-size:.72rem}
    .zt-cid-wrap label{color:#64748b;font-weight:600;white-space:nowrap}
    .zt-cid-select{border:1px solid #d1d5db;border-radius:6px;padding:.25rem .5rem;font-size:.72rem;background:#f8f9fa;color:#1e293b;cursor:pointer;max-width:200px}
    .zt-cid-select:focus{outline:2px solid #1a73e8}
    .zt-cid-select:disabled{opacity:.6;cursor:wait}
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zt-cid-select{background:#0d1526;border-color:rgba(255,255,255,.15);color:#e2e8f0}
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zt-bar{background:#0d1526;border-bottom-color:rgba(255,255,255,.06)}
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zt-bar .zt-msg{color:#94a3b8}
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zt-bar.warn{background:#2d200a;border-bottom-color:#b38600}
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zt-bar.warn .zt-msg{color:#fbbf24}

    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],
        [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zse-wrap {
        background: #0a0f1e;
    }
</style>
@endsection

@section('content')
{{-- Token status bar --}}
<div class="zt-bar {{ $hasToken ? '' : 'warn' }}">
    <span class="zt-msg">
        @if($hasToken)
            <i class="bx bx-check-circle" style="color:#1a8754;font-size:.9rem"></i>
            Zoom Phone connected
        @else
            <i class="bx bx-error-circle" style="font-size:.9rem"></i>
            Token expired &mdash; calls will fail until you re-authorize
        @endif
    </span>
    {{-- Caller ID selector (populated dynamically from Zoom) --}}
    <div class="zt-cid-wrap">
        <label for="zt-cid-sel"><i class="bx bx-phone-outgoing"></i> Caller ID:</label>
        <select id="zt-cid-sel" class="zt-cid-select" disabled>
            <option value="">Loading...</option>
        </select>
    </div>
    <a href="{{ route('zoom.authorize') }}" class="zt-btn-reauth">
        <i class="bx bx-refresh"></i>
        Re-authorize Zoom
    </a>
</div>
<div class="zse-wrap">
    <iframe
        src="https://applications.zoom.us/integration/phone/embeddablephone/home"
        id="zoom-embeddable-phone-iframe"
        allow="clipboard-read; clipboard-write https://applications.zoom.us"
    ></iframe>
</div>
@endsection

@push('scripts')
<script>
const zpIframe = document.getElementById('zoom-embeddable-phone-iframe');

// Default caller ID — populated async from Zoom; updates once DIDs are loaded
window._zpCallerId = '';

/**
 * Fetch the current user's DIDs from Zoom API and populate the Caller ID dropdown.
 * Called once on page load.
 */
async function _zpLoadDids() {
    const sel = document.getElementById('zt-cid-sel');
    try {
        const resp = await fetch('{{ route('zoom.phone.my-dids') }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        const dids = data.dids || [];
        if (dids.length === 0) throw new Error('empty');

        sel.innerHTML = '';
        dids.forEach(function(d, i) {
            const opt = document.createElement('option');
            opt.value = d.number;
            opt.textContent = d.label + ' (' + d.number + ')'
                + (d.primary ? ' \u2605' : '');
            if (i === 0) opt.selected = true;
            sel.appendChild(opt);
        });
        sel.disabled = false;
        window._zpCallerId = sel.value;
        sel.addEventListener('change', function() { window._zpCallerId = this.value; });
        console.log('[ZoomPhone] ✅ Loaded ' + dids.length + ' caller IDs from Zoom (source: ' + data.source + ')');
    } catch (err) {
        sel.innerHTML = '<option value="">No caller IDs available</option>';
        sel.disabled = false;
        console.warn('[ZoomPhone] Could not load DIDs:', err);
    }
}
_zpLoadDids();

// Internal dial — NOT window.zoomDial, so the widget can't overwrite it
function _zpDial(number) {
    if (!number) return;
    number = String(number).replace(/[^\d+*#]/g, '');
    if (/^\d{10}$/.test(number)) number = '+1' + number;
    if (!number) return;
    // Prefer dropdown selection → last loaded default → empty (Zoom account default)
    const callerId = (document.getElementById('zt-cid-sel')?.value
                      || window._zpCallerId
                      || '');
    console.log('[ZoomPhone] 📞 Sending zp-make-call to iframe:', number, '| callerId:', callerId || '(zoom default)');
    zpIframe.contentWindow.postMessage({
        type: 'zp-make-call',
        data: { number, callerId, autoDial: true }
    }, 'https://applications.zoom.us');
}

// Also expose as window.zoomDial so it overrides the widget version on this page
window.zoomDial = _zpDial;

// ── 1. Zoom calls this when the iframe is ready ──────────────────────────
window.onZoomPhoneIframeApiReady = function() {
    console.log('[ZoomPhone] ✅ onZoomPhoneIframeApiReady fired — iframe ready');
    zpIframe.contentWindow.postMessage({
        type: 'zp-init-config',
        data: {
            enableSavingLog:             true,
            enableAutoLog:               false,
            enableContactSearching:      true,
            enableContactMatching:       true,
            enableAISummary:             true,
            disableInactiveTabCallEvent: true,
        }
    }, 'https://applications.zoom.us');

    // Dial any number queued before iframe was ready
    try {
        const raw = localStorage.getItem('zpw_pending_dial');
        if (raw) {
            const p = JSON.parse(raw);
            localStorage.removeItem('zpw_pending_dial');
            if (p && (Date.now() - p.ts) < 15000) {
                console.log('[ZoomPhone] 📋 Dialing queued number from localStorage:', p.number);
                setTimeout(() => _zpDial(p.number), 800);
            }
        }
    } catch(_) {}
};

// ── 2. Listen for all events from the Smart Embed ───────────────────────
window.addEventListener('message', async function(e) {
    if (!e.data || e.origin !== 'https://applications.zoom.us') return;
    const { type, data } = e.data;

    switch (type) {

        // User typed in Zoom keypad/SMS — search CRM contacts
        case 'zp-contact-search-event':
            try {
                const resp = await fetch(
                    `{{ route('zoom.phone.search-leads') }}?q=${encodeURIComponent(data.searchString)}`,
                    { headers: { 'Accept': 'application/json' } }
                );
                const leads = await resp.json();
                zpIframe.contentWindow.postMessage({
                    type: 'zp-contact-search-response',
                    data: {
                        responseId: data.requestId,
                        contacts: leads
                            .filter(l => l.phone_number)
                            .map(l => ({
                                number:      l.phone_number,
                                displayName: l.cn_name || 'Unknown'
                            }))
                    }
                }, 'https://applications.zoom.us');
            } catch(_) {}
            break;

        // Incoming/outgoing call — match phone numbers to CRM contacts
        case 'zp-contact-match-event':
            // Store the phone numbers being matched so the calling page can identify the lead
            // This fires before ringing/connected and is the most reliable source of caller info
            try {
                const nums = (data.phoneNumbers || [])
                    .map(n => (typeof n === 'object') ? (n.phoneNumber || n.number || '') : String(n))
                    .map(n => n.replace(/[^\d+]/g, ''))
                    .filter(n => n.length > 0);
                if (nums.length) {
                    localStorage.setItem('zpw_contact_match', JSON.stringify({ numbers: nums, ts: Date.now() }));
                    console.log('[ZoomPhone] 📋 contact-match phones:', nums);
                }
            } catch(_) {}
            try {
                const resp = await fetch('{{ route("zoom.phone.match-contacts") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ numbers: data.phoneNumbers || [] })
                });
                const contacts = await resp.json();
                zpIframe.contentWindow.postMessage({
                    type: 'zp-contact-match-response',
                    data: { responseId: data.requestId, contacts }
                }, 'https://applications.zoom.us');
            } catch(_) {}
            break;

        // Call log completed — auto-save to CRM
        case 'zp-call-log-completed-event':
            fetch('{{ route("zoom.phone.auto-log") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(data)
            }).catch(() => {});
            break;

        // ── Call status events — broadcast to calling page tab via localStorage ──
        case 'zp-call-ringing-event':
        case 'zp-call-connected-event':
        case 'zp-call-ended-event':
        case 'zp-call-voicemail-received-event':
            // Log full raw data so we can see every field Zoom sends
            console.log('[ZoomPhone] 📨 Event:', type, JSON.stringify(data));
            {
                // For ended events, check if result indicates voicemail
                const vmResults = ['voicemail', 'leftVoicemail', 'left_voicemail', 'no_answer_voicemail'];
                const isVoicemailEnded = type === 'zp-call-ended-event'
                    && data.result && vmResults.includes(String(data.result).toLowerCase());

                const status = type === 'zp-call-ringing-event'            ? 'ringing'
                             : type === 'zp-call-connected-event'          ? 'connected'
                             : type === 'zp-call-voicemail-received-event' ? 'voicemail'
                             : isVoicemailEnded                            ? 'voicemail'
                             : 'ended';

                // Extract caller number — caller can be an object {phoneNumber, name} or a plain string
                const dir = (data.direction || '').toLowerCase();
                let callerNumber = null;
                {
                    // Helper: pull phoneNumber from either an object or a plain string
                    const extractPhone = (v) => {
                        if (!v) return null;
                        const raw = (typeof v === 'object') ? (v.phoneNumber || v.number || '') : String(v);
                        const digits = raw.replace(/[^\d+]/g, '');
                        if (!digits) return null;
                        return /^\d{10}$/.test(digits) ? '+1' + digits : digits;
                    };

                    // First try the recent contact-match (fires before ringing, most reliable for external callers)
                    try {
                        const cm = JSON.parse(localStorage.getItem('zpw_contact_match') || 'null');
                        if (cm && (Date.now() - cm.ts) < 60000 && cm.numbers && cm.numbers.length) {
                            callerNumber = extractPhone(cm.numbers[0]);
                        }
                    } catch(_) {}
                    // Fall back to event caller field
                    if (!callerNumber) {
                        callerNumber = extractPhone(data.caller) || extractPhone(data.callerNumber);
                    }
                }
                if (type === 'zp-call-ringing-event') {
                    console.log('[ZoomPhone] 📲 Ringing | direction:', dir, '| callerNumber:', callerNumber, '| caller obj:', data.caller);
                }

                localStorage.setItem('zpw_call_status', JSON.stringify({
                    status,
                    callId:       data.callId,
                    direction:    dir,
                    callee:       data.callee,
                    caller:       data.caller,
                    callerNumber,
                    result:       data.result,
                    ts:           Date.now()
                }));
            }
            break;
    }
});

// ── 3. Click-to-dial API ─────────────────────────────────────────────────
// (window.zoomDial defined above as _zpDial alias)

// ── 4. Cross-tab dial: BroadcastChannel (primary) + storage event (fallback) ──
try {
    const _zpBC = new BroadcastChannel('zpw_dial');
    _zpBC.onmessage = function(e) {
        if (!e.data || e.data.type !== 'dial') return;
        console.log('[ZoomPhone] 📡 BroadcastChannel received dial:', e.data.number);
        _zpDial(e.data.number); // always attempt — postMessage is silent if iframe not ready
    };
    console.log('[ZoomPhone] ✅ BroadcastChannel listener active');
} catch(err) {
    console.warn('[ZoomPhone] BroadcastChannel not available:', err);
}

// Storage event fallback
window.addEventListener('storage', function(e) {
    if (e.key !== 'zpw_pending_dial' || !e.newValue) return;
    try {
        const p = JSON.parse(e.newValue);
        if (!p || (Date.now() - p.ts) > 15000) return;
        console.log('[ZoomPhone] 📦 storage event dial:', p.number);
        localStorage.removeItem('zpw_pending_dial');
        _zpDial(p.number);
    } catch(_) {}
});
</script>
@endpush
