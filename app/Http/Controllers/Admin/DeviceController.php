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
}
