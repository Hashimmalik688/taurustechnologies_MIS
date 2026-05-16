@extends('layouts.master')

@section('title') My Device @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') My Records @endslot
    @slot('title') My Device @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-lg-6">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header bg-primary">
                <h5 class="mb-0 text-white"><i class="bx bx-devices me-2"></i>This Device</h5>
            </div>
            <div class="card-body">

                @if($device && $device->status === 'approved')

                    <p class="text-muted mb-4">This is the device token stored in your current browser. You can give it a name so your admin can identify it easily (e.g. "My Work Laptop", "Home PC").</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Device Token</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" id="deviceToken"
                                   value="{{ $device->device_token }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                <i class="bx bx-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted">Share this with your admin if you ever get locked out.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <div><span class="badge bg-success fs-6">Approved</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Seen</label>
                        <div class="text-muted">{{ $device->last_seen_at ? \Carbon\Carbon::parse($device->last_seen_at)->format('M d, Y h:i A') : 'N/A' }}</div>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('my-devices.name') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Device Name <small class="text-muted">(optional, for your reference)</small></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="e.g. My Work Laptop, Home PC, Office Desktop"
                                   value="{{ old('name', $device->name) }}" maxlength="255">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Save Name
                        </button>
                    </form>

                @elseif($device && $device->status === 'pending')

                    <div class="text-center py-4">
                        <div class="fs-1 mb-3">⏳</div>
                        <h5>Device Pending Approval</h5>
                        <p class="text-muted">Your device is waiting for admin approval.</p>
                        <div class="bg-light rounded p-3 mt-3 font-monospace text-break">{{ $device->device_token }}</div>
                        <small class="text-muted d-block mt-2">Send this token to your administrator.</small>
                    </div>

                @else

                    <div class="text-center py-4">
                        <div class="fs-1 mb-3">❓</div>
                        <h5>No Device Token Found</h5>
                        <p class="text-muted">Your browser does not have a device token cookie. Try logging out and back in.</p>
                        @if($token)
                            <div class="bg-light rounded p-3 mt-3 font-monospace text-break">{{ $token }}</div>
                            <small class="text-muted d-block mt-2">Token found but not in database — contact your admin.</small>
                        @endif
                    </div>

                @endif

            </div>
        </div>

    </div>
</div>

<script>
function copyToken() {
    var val = document.getElementById('deviceToken').value;
    navigator.clipboard.writeText(val).then(function() {
        var btn = document.querySelector('#deviceToken + button');
        btn.innerHTML = '<i class="bx bx-check"></i>';
        setTimeout(function() { btn.innerHTML = '<i class="bx bx-copy"></i>'; }, 2000);
    });
}
</script>
@endsection
