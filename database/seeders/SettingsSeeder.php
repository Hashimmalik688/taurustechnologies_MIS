<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'key' => 'office_networks',
                'value' => '192.168.1.0/24,10.0.0.0/16',
                'type' => 'array',
                'description' => 'Allowed office IP networks for attendance marking',
                'group' => 'attendance',
            ],
            [
                'key' => 'office_start_time',
                'value' => '09:00',
                'type' => 'string',
                'description' => 'Official office start time',
                'group' => 'attendance',
            ],
            [
                'key' => 'attendance_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic attendance marking',
                'group' => 'attendance',
            ],
            [
                'key' => 'late_time',
                'value' => '19:15',
                'type' => 'string',
                'description' => 'Fixed time after which attendance is marked late (e.g., 19:15 or 07:15 PM)',
                'group' => 'attendance',
            ],
            [
                'key' => 'allow_weekend_attendance',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Allow attendance marking on weekends',
                'group' => 'attendance',
            ],
            [
                'key' => 'shift_duration_hours',
                'value' => '10',
                'type' => 'string',
                'description' => 'Standard shift duration in hours (e.g., 10 for night shift)',
                'group' => 'attendance',
            ],
            [
                'key' => 'attendance_buffer_hours',
                'value' => '1',
                'type' => 'string',
                'description' => 'Buffer hours before and after shift for attendance marking',
                'group' => 'attendance',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
