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
                'key' => 'late_threshold_minutes',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Minutes after start time to consider late',
                'group' => 'attendance',
            ],
            [
                'key' => 'allow_weekend_attendance',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Allow attendance marking on weekends',
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
