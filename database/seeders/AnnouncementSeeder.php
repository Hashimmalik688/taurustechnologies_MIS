<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Announcement;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample announcements
        Announcement::create([
            'title' => '🚀 System Launch Announcement',
            'message' => 'Welcome to Taurus CRM! We are excited to announce the launch of our new Employee Management System (EMS) with real-time chat capabilities.',
            'animation' => 'slide',
            'background_color' => 'green',
            'icon' => 'check',
            'auto_dismiss' => '10s',
            'is_active' => false,
            'created_by' => 1,
            'published_at' => now(),
        ]);

        Announcement::create([
            'title' => '⚠️ Maintenance Notice',
            'message' => 'System maintenance is scheduled for tonight from 10 PM to 2 AM. Please save your work and log out before the maintenance window.',
            'animation' => 'fade',
            'background_color' => 'yellow',
            'icon' => 'warning',
            'auto_dismiss' => 'never',
            'is_active' => false,
            'created_by' => 1,
            'published_at' => now(),
        ]);
    }
}
