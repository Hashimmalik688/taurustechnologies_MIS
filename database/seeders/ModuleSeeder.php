<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // HR Operations
            [
                'name' => 'E.M.S',
                'slug' => 'ems',
                'description' => 'Employee Management System - manage employee records and profiles',
                'category' => 'HR Operations',
                'sort_order' => 10,
            ],
            [
                'name' => 'Attendance',
                'slug' => 'attendance',
                'description' => 'Track and manage employee attendance',
                'category' => 'HR Operations',
                'sort_order' => 20,
            ],
            [
                'name' => 'Dock Management',
                'slug' => 'dock',
                'description' => 'Manage employee warnings, infractions, and disciplinary actions',
                'category' => 'HR Operations',
                'sort_order' => 30,
            ],
            [
                'name' => 'Public Holidays',
                'slug' => 'holidays',
                'description' => 'Manage public holidays and office closures',
                'category' => 'HR Operations',
                'sort_order' => 40,
            ],

            // Sales Operations
            [
                'name' => 'Peregrine Leads',
                'slug' => 'leads-peregrine',
                'description' => 'Manage Peregrine team leads',
                'category' => 'Sales Operations',
                'sort_order' => 45,
            ],
            [
                'name' => 'Raven Leads',
                'slug' => 'leads',
                'description' => 'Manage Ravens team leads and follow-ups',
                'category' => 'Sales Operations',
                'sort_order' => 50,
            ],
            [
                'name' => 'Sales Records',
                'slug' => 'sales',
                'description' => 'View and manage sales records and transactions',
                'category' => 'Sales Operations',
                'sort_order' => 60,
            ],
            [
                'name' => 'Policy Submission',
                'slug' => 'issuance',
                'description' => 'Manage policy issuance, submissions, and status updates',
                'category' => 'Sales Operations',
                'sort_order' => 70,
            ],
            [
                'name' => 'QA Review',
                'slug' => 'qa-review',
                'description' => 'Quality assurance review of sales and applications',
                'category' => 'Sales Operations',
                'sort_order' => 80,
            ],
            [
                'name' => 'Bank Verification',
                'slug' => 'bank-verification',
                'description' => 'Verify and manage bank account information',
                'category' => 'Sales Operations',
                'sort_order' => 90,
            ],
            [
                'name' => 'Revenue Analytics',
                'slug' => 'revenue-analytics',
                'description' => 'View revenue reports and analytics',
                'category' => 'Sales Operations',
                'sort_order' => 91,
            ],
            [
                'name' => 'Live Analytics',
                'slug' => 'live-analytics',
                'description' => 'Real-time business analytics dashboard',
                'category' => 'Sales Operations',
                'sort_order' => 92,
            ],

            // Specialized Operations
            [
                'name' => 'Peregrine Operations',
                'slug' => 'peregrine',
                'description' => 'Peregrine team verifier and closer operations',
                'category' => 'Specialized Operations',
                'sort_order' => 100,
            ],
            [
                'name' => 'Ravens Operations',
                'slug' => 'ravens',
                'description' => 'Ravens team calling and closing operations',
                'category' => 'Specialized Operations',
                'sort_order' => 110,
            ],
            [
                'name' => 'Retention & Chargebacks',
                'slug' => 'retention',
                'description' => 'Manage policy retention and customer follow-ups',
                'category' => 'Specialized Operations',
                'sort_order' => 120,
            ],
            [
                'name' => 'Chargebacks',
                'slug' => 'chargebacks',
                'description' => 'Track and manage policy chargebacks',
                'category' => 'Specialized Operations',
                'sort_order' => 130,
            ],

            // Partner Management
            [
                'name' => 'Partner Management',
                'slug' => 'partners',
                'description' => 'Manage insurance partners and agents',
                'category' => 'Partner Management',
                'sort_order' => 140,
            ],
            [
                'name' => 'Insurance Cluster',
                'slug' => 'carriers',
                'description' => 'Manage insurance carrier information and relationships',
                'category' => 'Partner Management',
                'sort_order' => 150,
            ],

            // Finance & Accounts
            [
                'name' => 'Payroll',
                'slug' => 'payroll',
                'description' => 'Manage employee payroll, salaries, and deductions',
                'category' => 'Finance & Accounts',
                'sort_order' => 160,
            ],
            [
                'name' => 'Chart of Accounts',
                'slug' => 'chart-of-accounts',
                'description' => 'Manage accounting chart of accounts',
                'category' => 'Finance & Accounts',
                'sort_order' => 170,
            ],
            [
                'name' => 'General Ledger',
                'slug' => 'general-ledger',
                'description' => 'View and manage general ledger entries',
                'category' => 'Finance & Accounts',
                'sort_order' => 180,
            ],
            [
                'name' => 'Petty Cash',
                'slug' => 'petty-cash',
                'description' => 'Manage petty cash transactions',
                'category' => 'Finance & Accounts',
                'sort_order' => 190,
            ],
            [
                'name' => 'PABS Tickets',
                'slug' => 'pabs-tickets',
                'description' => 'Manage PABS support tickets',
                'category' => 'Finance & Accounts',
                'sort_order' => 200,
            ],

            // System & Settings
            [
                'name' => 'Users MGMT',
                'slug' => 'users',
                'description' => 'Manage user accounts, roles, and permissions',
                'category' => 'System & Settings',
                'sort_order' => 230,
            ],
            [
                'name' => 'System Settings',
                'slug' => 'settings',
                'description' => 'Configure system-wide settings and preferences',
                'category' => 'System & Settings',
                'sort_order' => 240,
            ],
            [
                'name' => 'Audit Logs',
                'slug' => 'audit-logs',
                'description' => 'View system audit logs and user activity',
                'category' => 'System & Settings',
                'sort_order' => 250,
            ],
            [
                'name' => 'Duplicate Checker',
                'slug' => 'duplicate-checker',
                'description' => 'Check for duplicate records in the system',
                'category' => 'System & Settings',
                'sort_order' => 260,
            ],
            [
                'name' => 'Account Switch Log',
                'slug' => 'account-switch-log',
                'description' => 'Track account switching activity',
                'category' => 'System & Settings',
                'sort_order' => 261,
            ],

            // Project Management
            [
                'name' => 'Project Management',
                'slug' => 'epms',
                'description' => 'Employee Project Management System',
                'category' => 'Project Management',
                'sort_order' => 270,
            ],

            // Communication
            [
                'name' => 'Chat & Communities',
                'slug' => 'chat',
                'description' => 'Internal team chat and community messaging',
                'category' => 'Communication',
                'sort_order' => 280,
            ],
            [
                'name' => 'Community Announcements',
                'slug' => 'announcements',
                'description' => 'Create and manage community announcements',
                'category' => 'Communication',
                'sort_order' => 290,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['slug' => $module['slug']],
                $module
            );
        }

        $this->command->info('Modules seeded successfully!');
    }
}
