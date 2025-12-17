<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:admin'); // If you have role middleware
    }

    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
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

    public function testNetwork(Request $request)
    {
        $ipService = new \App\Services\IpDetectionService;
        $allIps = $ipService->getAllIpAddresses();
        $bestIp = $ipService->getBestIpForAttendance();
        $networks = Setting::get('office_networks', []);

        if (is_string($networks)) {
            $networks = explode(',', $networks);
        }

        $isInNetwork = false;
        $matchedNetwork = null;

        foreach ($networks as $network) {
            $network = trim($network);
            if ($this->ipInRange($bestIp, $network)) {
                $isInNetwork = true;
                $matchedNetwork = $network;
                break;
            }
        }

        $message = $isInNetwork ?
            "✅ Your IP ({$bestIp}) matches office network: {$matchedNetwork}" :
            "❌ Your IP ({$bestIp}) is NOT in any configured office network.";

        // Add helpful hints for localhost
        if ($allIps['is_localhost']) {
            $message .= "\n\n⚠️ You're on localhost. To get your real office IP:\n";
            $message .= "1. Deploy to your server, OR\n";
            $message .= "2. Visit whatismyipaddress.com from office, OR\n";
            $message .= "3. For testing, add '127.0.0.1' to allowed networks.";
        }

        return response()->json([
            'current_ip' => $bestIp,
            'all_detected_ips' => $allIps,
            'configured_networks' => $networks,
            'is_in_office_network' => $isInNetwork,
            'matched_network' => $matchedNetwork,
            'message' => $message,
            'is_localhost' => $allIps['is_localhost'],
        ]);
    }

    private function ipInRange($ip, $range)
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $bits] = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }
}
