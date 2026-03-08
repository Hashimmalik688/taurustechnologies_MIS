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
        height: calc(100vh - 60px);
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

    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],
        [data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .zse-wrap {
        background: #0a0f1e;
    }
</style>
@endsection

@section('content')
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

// Internal dial — NOT window.zoomDial, so the widget can't overwrite it
function _zpDial(number) {
    if (!number) return;
    number = String(number).replace(/[^\d+*#]/g, '');
    if (/^\d{10}$/.test(number)) number = '+1' + number;
    if (!number) return;
    console.log('[ZoomPhone] 📞 Sending zp-make-call to iframe:', number);
    zpIframe.contentWindow.postMessage({
        type: 'zp-make-call',
        data: { number, callerId: '', autoDial: true }
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
