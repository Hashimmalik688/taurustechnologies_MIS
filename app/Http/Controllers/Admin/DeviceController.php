<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return redirect()->route('settings.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_token' => ['required', 'string', 'max:100', 'unique:allowed_devices,device_token'],
            'name'         => ['nullable', 'string', 'max:255'],
            'label'        => ['required', 'string', 'max:255'],
        ]);

        AllowedDevice::create([
            'device_token' => $request->device_token,
            'name'         => $request->name,
            'label'        => $request->label,
            'added_by'     => auth()->id(),
            'status'       => 'approved',
        ]);

        return redirect()->back()->with('success', 'Device "' . $request->label . '" approved.');
    }

    public function approve(AllowedDevice $device)
    {
        $request = request();
        $request->validate([
            'name'  => ['nullable', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $device->update([
            'status' => 'approved',
            'added_by' => auth()->id(),
            'name'   => $request->name  ?: $device->name,
            'label'  => $request->label ?: $device->label,
        ]);

        return redirect()->back()->with('success', 'Device approved.');
    }

    public function update(Request $request, AllowedDevice $device)
    {
        $request->validate([
            'name'         => ['nullable', 'string', 'max:255'],
            'label'        => ['required', 'string', 'max:255'],
            'status'       => ['required', 'in:approved,pending,disabled,rejected'],
            'device_token' => ['required', 'string', 'max:100', 'unique:allowed_devices,device_token,' . $device->id],
        ]);

        $device->update([
            'name'         => request('name'),
            'label'        => request('label'),
            'status'       => request('status'),
            'device_token' => request('device_token'),
        ]);

        return redirect()->back()->with('success', 'Device "' . $device->fresh()->label . '" updated.');
    }

    public function disable(AllowedDevice $device)
    {
        $device->update(['status' => 'disabled']);
        return redirect()->back()->with('success', 'Device "' . $device->label . '" disabled.');
    }

    public function enable(AllowedDevice $device)
    {
        $device->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Device "' . $device->label . '" re-enabled.');
    }

    public function destroy(AllowedDevice $device)
    {
        // Mark as rejected instead of deleting — the token stays in the DB
        // permanently blocked so the same browser can never re-register.
        $label = $device->label;
        $device->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Device "' . $label . '" rejected and permanently blocked.');
    }

    public function activate(Request $request)
    {
        $request->validate(['device_token' => ['required', 'string', 'max:100']]);

        $device = AllowedDevice::where('device_token', $request->device_token)
            ->where('status', 'approved')
            ->first();

        if (! $device) {
            return response('<!DOCTYPE html><html><body style="font-family:sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;height:100vh;text-align:center"><div><h2>Not Approved Yet</h2><p style="color:#9ca3af">Your token has not been approved by your administrator yet.<br>Please wait and try again.</p><a href="/" style="color:#68d391">← Go Back</a></div></body></html>', 403)->header('Content-Type', 'text/html');
        }

        $cookie = \Symfony\Component\HttpFoundation\Cookie::create(
            name:     \App\Http\Middleware\RestrictToAllowedDevice::COOKIE,
            value:    $device->device_token,
            expire:   time() + 60 * 60 * 24 * 365 * 5,
            path:     '/',
            secure:   $request->isSecure(),
            httpOnly: true,
            sameSite: 'Lax',
        );

        $device->updateQuietly(['last_seen_ip' => $request->ip(), 'last_seen_at' => now()]);

        return redirect('/')->withCookie($cookie);
    }

    public function myDevices(Request $request)
    {
        $cookieName = \App\Http\Middleware\RestrictToAllowedDevice::COOKIE;
        $token = $request->cookie($cookieName);
        $device = $token ? AllowedDevice::where('device_token', $token)->first() : null;

        return view('my-devices', compact('device', 'token'));
    }

    public function updateMyDeviceName(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        $cookieName = \App\Http\Middleware\RestrictToAllowedDevice::COOKIE;
        $token = $request->cookie($cookieName);
        $device = $token ? AllowedDevice::where('device_token', $token)->first() : null;

        if (! $device || $device->status !== 'approved') {
            return redirect()->back()->with('error', 'Device not found or not approved.');
        }

        $device->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Device name updated.');
    }
}
