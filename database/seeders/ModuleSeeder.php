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
            // 1. Company Overview (Executive Dashboard)
            [
                'name' => 'Company Overview',
                'slug' => 'dashboard',
                'description' => 'Executive dashboard with company-wide metrics and KPIs',
                'category' => 'Company Overview',
                'sort_order' => 10,
            ],

            // 2. Sales Operations
            [
                'name' => 'Peregrine Leads',
                'slug' => 'leads-peregrine',
                'description' => 'Manage Peregrine team leads',
                'category' => 'Sales Operations',
                'sort_order' => 20,
            ],
            [
                'name' => 'Raven Leads',
                'slug' => 'leads',
                'description' => 'Manage Ravens team leads and follow-ups',
                'category' => 'Sales Operations',
                'sort_order' => 30,
            ],
            [
                'name' => 'QA Review',
                'slug' => 'qa-review',
                'description' => 'Quality assurance review of sales and applications',
                'category' => 'Sales Operations',
                'sort_order' => 40,
            ],
            [
                'name' => 'Sales Records',
                'slug' => 'sales',
                'description' => 'View and manage sales records and transactions',
                'category' => 'Sales Operations',
                'sort_order' => 50,
            ],
            [
                'name' => 'Policy Submission',
                'slug' => 'issuance',
                'description' => 'Manage policy issuance, submissions, and status updates',
                'category' => 'Sales Operations',
                'sort_order' => 60,
            ],
            [
                'name' => 'Bank Verification',
                'slug' => 'bank-verification',
                'description' => 'Verify and manage bank account information',
                'category' => 'Sales Operations',
                'sort_order' => 70,
            ],
            [
                'name' => 'Revenue Analytics',
                'slug' => 'revenue-analytics',
                'description' => 'View revenue reports and analytics',
                'category' => 'Sales Operations',
                'sort_order' => 80,
            ],
            [
                'name' => 'Live Analytics',
                'slug' => 'live-analytics',
                'description' => 'Real-time business analytics dashboard',
                'category' => 'Sales Operations',
                'sort_order' => 90,
            ],

            // 3. Retention & Chargebacks
            [
                'name' => 'Retention Management',
                'slug' => 'retention',
                'description' => 'Manage policy retention, dashboard, and customer follow-ups',
                'category' => 'Retention & Chargebacks',
                'sort_order' => 100,
            ],
            [
                'name' => 'Chargebacks',
                'slug' => 'chargebacks',
                'description' => 'Track and manage policy chargebacks',
                'category' => 'Retention & Chargebacks',
                'sort_order' => 110,
            ],

            // 4. Peregrine Operations
            [
                'name' => 'Peregrine Operations',
                'slug' => 'peregrine',
                'description' => 'Peregrine team verifier, closer, and validator operations',
                'category' => 'Peregrine Operations',
                'sort_order' => 120,
            ],
            [
                'name' => 'Peregrine Dashboard',
                'slug' => 'peregrine-dashboard',
                'description' => 'Peregrine team overview dashboard',
                'category' => 'Peregrine Operations',
                'sort_order' => 121,
            ],
            [
                'name' => 'Verifier Form',
                'slug' => 'peregrine-verifier',
                'description' => 'Peregrine verifier form and submissions',
                'category' => 'Peregrine Operations',
                'sort_order' => 122,
            ],
            [
                'name' => 'Peregrine Closers',
                'slug' => 'peregrine-closers',
                'description' => 'Peregrine closer operations and lead management',
                'category' => 'Peregrine Operations',
                'sort_order' => 123,
            ],
            [
                'name' => 'Validation Dashboard',
                'slug' => 'peregrine-validation',
                'description' => 'Peregrine lead validation and approval dashboard',
                'category' => 'Peregrine Operations',
                'sort_order' => 124,
            ],

            // 5. Ravens Operations
            [
                'name' => 'Ravens Operations',
                'slug' => 'ravens',
                'description' => 'Ravens team calling, dashboard, and closing operations',
                'category' => 'Ravens Operations',
                'sort_order' => 130,
            ],
            [
                'name' => 'Ravens Dashboard',
                'slug' => 'ravens-dashboard',
                'description' => 'Ravens team overview dashboard',
                'category' => 'Ravens Operations',
                'sort_order' => 131,
            ],
            [
                'name' => 'Ravens Calling',
                'slug' => 'ravens-calling',
                'description' => 'Ravens outbound calling interface',
                'category' => 'Ravens Operations',
                'sort_order' => 132,
            ],
            [
                'name' => 'Bad Leads',
                'slug' => 'ravens-bad-leads',
                'description' => 'Manage and review bad/rejected leads',
                'category' => 'Ravens Operations',
                'sort_order' => 133,
            ],
            [
                'name' => 'Followups & Bank Verification',
                'slug' => 'ravens-followups',
                'description' => 'Lead followups and bank verification tracking',
                'category' => 'Ravens Operations',
                'sort_order' => 134,
            ],

            // 6. HR Operations
            [
                'name' => 'HR Operations',
                'slug' => 'hr',
                'description' => 'HR Operations hub — employee management, attendance, dock, and holidays',
                'category' => 'HR Operations',
                'sort_order' => 135,
            ],
            [
                'name' => 'E.M.S',
                'slug' => 'ems',
                'description' => 'Employee Management System - manage employee records and profiles',
                'category' => 'HR Operations',
                'sort_order' => 140,
            ],
            [
                'name' => 'Attendance',
                'slug' => 'attendance',
                'description' => 'Track and manage employee attendance',
                'category' => 'HR Operations',
                'sort_order' => 150,
            ],
            [
                'name' => 'Dock Management',
                'slug' => 'dock',
                'description' => 'Manage employee warnings, infractions, and disciplinary actions',
                'category' => 'HR Operations',
                'sort_order' => 160,
            ],
            [
                'name' => 'Public Holidays',
                'slug' => 'public-holidays',
                'description' => 'Manage public/national holidays for attendance calculations',
                'category' => 'HR Operations',
                'sort_order' => 175,
            ],

            // 7. Project Management
            [
                'name' => 'Project Management',
                'slug' => 'epms',
                'description' => 'Employee Project Management System',
                'category' => 'Project Management',
                'sort_order' => 180,
            ],

            // 8. Partner Management
            [
                'name' => 'Partner Management',
                'slug' => 'partners',
                'description' => 'Manage insurance partners and agents',
                'category' => 'Partner Management',
                'sort_order' => 190,
            ],
            [
                'name' => 'Insurance Cluster',
                'slug' => 'carriers',
                'description' => 'Manage insurance carrier information and relationships',
                'category' => 'Partner Management',
                'sort_order' => 200,
            ],

            // 9. Settings
            [
                'name' => 'System Settings',
                'slug' => 'settings',
                'description' => 'Configure system-wide settings and preferences',
                'category' => 'Settings',
                'sort_order' => 210,
            ],
            [
                'name' => 'Duplicate Checker',
                'slug' => 'duplicate-checker',
                'description' => 'Check for duplicate records in the system',
                'category' => 'Settings',
                'sort_order' => 220,
            ],
            [
                'name' => 'Account Switch Log',
                'slug' => 'account-switch-log',
                'description' => 'Track account switching activity',
                'category' => 'Settings',
                'sort_order' => 230,
            ],

            // 10. Finance & Accounts
            [
                'name' => 'Finance & Accounts',
                'slug' => 'finance',
                'description' => 'Finance & Accounts hub — chart of accounts, ledger, petty cash, payroll, PABS tickets',
                'category' => 'Finance & Accounts',
                'sort_order' => 235,
            ],
            [
                'name' => 'Chart of Accounts',
                'slug' => 'chart-of-accounts',
                'description' => 'Manage accounting chart of accounts',
                'category' => 'Finance & Accounts',
                'sort_order' => 240,
            ],
            [
                'name' => 'General Ledger',
                'slug' => 'general-ledger',
                'description' => 'View and manage general ledger entries',
                'category' => 'Finance & Accounts',
                'sort_order' => 250,
            ],
            [
                'name' => 'Petty Cash',
                'slug' => 'petty-cash',
                'description' => 'Manage petty cash transactions',
                'category' => 'Finance & Accounts',
                'sort_order' => 260,
            ],
            [
                'name' => 'Payroll',
                'slug' => 'payroll',
                'description' => 'Manage employee payroll, salaries, and deductions',
                'category' => 'Finance & Accounts',
                'sort_order' => 270,
            ],
            [
                'name' => 'PABS Tickets',
                'slug' => 'pabs-tickets',
                'description' => 'Manage PABS support tickets',
                'category' => 'Finance & Accounts',
                'sort_order' => 280,
            ],

            // 11. Users Management
            [
                'name' => 'Users MGMT',
                'slug' => 'users',
                'description' => 'Manage user accounts, roles, and permissions',
                'category' => 'Users Management',
                'sort_order' => 290,
            ],

            // Additional Settings modules
            [
                'name' => 'Chat Shadowing',
                'slug' => 'chat-shadow',
                'description' => 'Monitor & review user conversations and notes in read-only mode',
                'category' => 'Settings',
                'sort_order' => 225,
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'description' => 'Generate sales, partner, agent & manager reports with CSV export',
                'category' => 'Settings',
                'sort_order' => 226,
            ],

            // 12. Communication
            [
                'name' => 'Team Chat',
                'slug' => 'chat',
                'description' => 'Real-time team messaging, group chats & community announcements',
                'category' => 'Communication',
                'sort_order' => 300,
            ],

            // Additional Company Overview modules
            [
                'name' => 'Team Dashboards',
                'slug' => 'team-dashboards',
                'description' => 'Peregrine & Ravens team performance dashboards',
                'category' => 'Company Overview',
                'sort_order' => 15,
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
