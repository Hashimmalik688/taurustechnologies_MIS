<?php

namespace App\Console\Commands;

use App\Models\AllowedDevice;
use Illuminate\Console\Command;

class DeviceApprove extends Command
{
    protected $signature   = 'device:approve {token? : Device token to approve (omit to list pending)}
                                             {--name= : Person name to assign}
                                             {--label= : Device label}';
    protected $description = 'Approve a pending device token (use when locked out of the UI)';

    public function handle(): int
    {
        $token = $this->argument('token');

        if (! $token) {
            $pending = AllowedDevice::where('status', 'pending')->get();
            if ($pending->isEmpty()) {
                $this->info('No pending devices.');
                return 0;
            }
            $this->table(
                ['ID', 'Token', 'IP', 'First seen'],
                $pending->map(fn($d) => [$d->id, $d->device_token, $d->last_seen_ip, $d->created_at->diffForHumans()])
            );
            return 0;
        }

        $device = AllowedDevice::where('device_token', $token)->first();

        if (! $device) {
            $this->error("Token not found: {$token}");
            return 1;
        }

        $device->update([
            'status' => 'approved',
            'name'   => $this->option('name')  ?: $device->name,
            'label'  => $this->option('label') ?: $device->label,
        ]);

        $this->info("Device approved: {$device->label} ({$device->device_token})");
        return 0;
    }
}
