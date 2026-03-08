<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\AllowedDevice;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:admin'); // If you have role middleware
    }

    public function hub()
    {
        $user = auth()->user();
        if (!$user->canViewModule('settings') && !$user->canViewModule('reports') && !$user->canViewModule('duplicate-checker') && !$user->canViewModule('account-switch-log') && !$user->hasRole('Super Admin')) {
            abort(403, "You don't have permission to view any Settings module.");
        }
        return view('admin.settings.hub');
    }

    public function themes()
    {
        return view('admin.settings.themes');
    }

    public function index()
    {
        $keyOrder = ['office_start_time','office_end_time','late_time','shift_duration_hours','attendance_buffer_hours','attendance_enabled','allow_weekend_attendance','office_networks','late_threshold_minutes'];
        $settings = Setting::orderBy('group')->get()
            ->sortBy(fn($s) => array_search($s->key, $keyOrder) !== false ? array_search($s->key, $keyOrder) : 99)
            ->groupBy('group');
        $pending  = AllowedDevice::where('status', 'pending')->latest()->get();
        $approved = AllowedDevice::where('status', 'approved')->latest()->get();
        $disabled = AllowedDevice::where('status', 'disabled')->latest()->get();
        $rejected = AllowedDevice::where('status', 'rejected')->latest()->get();

        return view('admin.settings.index', compact('settings', 'pending', 'approved', 'disabled', 'rejected'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Handle different types
                if ($setting->type === 'boolean') {
                    $value = $request->has("settings.{$key}") ? 'true' : 'false';
                } elseif ($setting->type === 'array' && is_array($value)) {
                    $value = implode(',', array_filter($value));
                }

                Setting::set($key, $value, $setting->type, $setting->description, $setting->group);
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
