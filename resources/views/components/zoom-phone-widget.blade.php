{{-- ┌─────────────────────────────────────────────────────────────────────────┐
     │  Zoom Phone Widget — window.zoomDial(number) helper                    │
     │  Opens /zoom/phone in a dedicated "zoomphone" browser tab              │
     │  and passes the number via localStorage for auto-dial.                  │
     │  Note: Floating FAB removed — dial via lead row buttons only.          │
     └─────────────────────────────────────────────────────────────────────────┘ --}}

<script>
(function () {
    // ══════════════════════════════════════════════════════════════════
    //  window.zoomDial(number)
    //  Called by ALL lead dial buttons across the CRM.
    //  Stores the number in localStorage, then opens/focuses the
    //  dedicated /zoom/phone tab.  The phone page listens for this
    //  storage event and dials automatically.
    // ══════════════════════════════════════════════════════════════════
    window.zoomDial = function (number) {
        if (!number) return;
        number = String(number).replace(/[^\d+]/g, '');
        if (!number) return;

        // 1. Always store in localStorage so a freshly-loading tab can pick it up
        localStorage.setItem('zpw_pending_dial', JSON.stringify({ number: number, ts: Date.now() }));

        // 2. Try to focus existing tab WITHOUT reloading it
        const existing = window.open('', 'zoomphone');
        const isLoaded = existing && existing.location && existing.location.href
                         && existing.location.href !== 'about:blank'
                         && !existing.location.href.endsWith('about:blank');

        if (isLoaded) {
            // Tab already open — send via BroadcastChannel so it dials immediately
            existing.focus();
            try {
                const bc = new BroadcastChannel('zpw_dial');
                bc.postMessage({ type: 'dial', number: number });
                bc.close();
            } catch(_) {}
        } else {
            // New/blank tab — navigate to phone page; localStorage will trigger dial after load
            existing.location.href = '{{ route('zoom.phone') }}';
        }
    };

    // Keep toggleZpWidget as a no-op for backward compatibility
    window.toggleZpWidget = function () {
        window.open('{{ route('zoom.phone') }}', 'zoomphone');
    };
})();
</script>
