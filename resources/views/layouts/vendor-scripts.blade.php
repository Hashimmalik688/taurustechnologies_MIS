<!-- JAVASCRIPT -->
<script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<!-- Waves.js removed - not needed for modern Bootstrap -->

<!-- App js -->
<script src="{{ URL::asset('build/js/app.js') }}"></script>

<!-- Global Reverb/Echo bootstrap for all MIS pages -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
(function () {
    const reverbConfig = {
        key: '{{ env("REVERB_APP_KEY", "") }}',
        host: '{{ env("REVERB_HOST", "127.0.0.1") }}',
        port: {{ intval(env("REVERB_PORT", 8080)) }},
        scheme: '{{ env("REVERB_SCHEME", "http") }}',
    };

    function createEchoInstance() {
        if (window.Echo && typeof window.Echo.channel === 'function') {
            return true;
        }

        const EchoCtor = typeof window.Echo === 'function'
            ? window.Echo
            : (window.LaravelEcho && (window.LaravelEcho.Echo || window.LaravelEcho.default));

        if (!EchoCtor || !window.Pusher || !reverbConfig.key) {
            return false;
        }

        window.Echo = new EchoCtor({
            broadcaster: 'reverb',
            key: reverbConfig.key,
            wsHost: reverbConfig.host,
            wsPort: reverbConfig.port,
            wssPort: reverbConfig.port,
            forceTLS: reverbConfig.scheme === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        return !!(window.Echo && typeof window.Echo.channel === 'function');
    }

    createEchoInstance();

    if (!window.MISRealtime) {
        window.MISRealtime = {
            handlers: [],
            register: function (domains, handler) {
                const normalized = Array.isArray(domains) ? domains : ['*'];
                this.handlers.push({ domains: normalized, handler: handler });
            },
            emit: function (payload) {
                const payloadDomains = Array.isArray(payload && payload.domains)
                    ? payload.domains.map((d) => String(d).toLowerCase())
                    : [];

                this.handlers.forEach((entry) => {
                    const matchAny = entry.domains.includes('*');
                    const matchDomain = entry.domains.some((d) => payloadDomains.includes(String(d).toLowerCase()));
                    if (matchAny || matchDomain) {
                        try {
                            entry.handler(payload);
                        } catch (err) {
                            console.warn('MIS realtime handler error', err);
                        }
                    }
                });

                window.dispatchEvent(new CustomEvent('mis:realtime-update', { detail: payload }));
            }
        };
    }

    function subscribeMISChannel() {
        if (window.__misRealtimeSubscribed) {
            return true;
        }
        if (!window.Echo || typeof window.Echo.channel !== 'function') {
            return false;
        }

        window.__misRealtimeSubscribed = true;
        window.Echo.channel('mis.updates').listen('.mis.data.updated', function (event) {
            if (window.MISRealtime && typeof window.MISRealtime.emit === 'function') {
                window.MISRealtime.emit(event || {});
            }
        });
        return true;
    }

    if (!subscribeMISChannel()) {
        let tries = 0;
        const iv = setInterval(function () {
            tries += 1;
            createEchoInstance();
            if (subscribeMISChannel() || tries > 40) {
                clearInterval(iv);
            }
        }, 250);
    }
})();
</script>

<!-- Global Chat Notifications (badge counter only, no Echo needed) -->
<script src="{{ URL::asset('js/chat-notifications.js') }}?v={{ filemtime(public_path('js/chat-notifications.js')) }}"></script>

<!-- Global Password Change Handler -->
<script>
$(document).ready(function() {
    $('#change-password').on('submit', function(event) {
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#password').val();
        var password_confirm = $('#password-confirm').val();
        
        // Clear previous errors
        $('#current_passwordError').text('');
        $('#passwordError').text('');
        $('#password_confirmError').text('');
        
        $.ajax({
            url: "{{ url('update-password') }}" + "/" + Id,
            type: "POST",
            data: {
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $('#current_passwordError').text('');
                $('#passwordError').text('');
                $('#password_confirmError').text('');
                
                if (response.isSuccess == false) {
                    $('#current_passwordError').text(response.Message);
                } else if (response.isSuccess == true) {
                    // Show success message
                    alert('Password updated successfully!');
                    setTimeout(function() {
                        window.location.href = "{{ route('root') }}";
                    }, 1000);
                }
            },
            error: function(response) {
                if (response.responseJSON && response.responseJSON.errors) {
                    $('#current_passwordError').text(response.responseJSON.errors.current_password);
                    $('#passwordError').text(response.responseJSON.errors.password);
                    $('#password_confirmError').text(response.responseJSON.errors.password_confirmation);
                }
            }
        });
    });
});
</script>

<!-- Page specific scripts are yielded at the bottom of master layout (avoid duplicate yield) -->

<!-- Bottom scripts - for scripts that need to run last -->
@yield('script-bottom')