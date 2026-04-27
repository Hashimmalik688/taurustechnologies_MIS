<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add individual module entries for each report so permissions
     * can be granted per-report rather than all-or-nothing via 'reports'.
     */
    public function up(): void
    {
        $modules = [
            [
                'name'        => 'Sales Export Report',
                'slug'        => 'report-sales-export',
                'description' => 'Generate & export sales, partner, chargeback, retention and issuance reports',
                'category'    => 'Reports',
                'sort_order'  => 500,
            ],
            [
                'name'        => 'Submission Performance',
                'slug'        => 'report-submission-performance',
                'description' => 'Carrier-wise breakdown of approved sales - total submissions & premium per carrier',
                'category'    => 'Reports',
                'sort_order'  => 501,
            ],
            [
                'name'        => 'Policy Type Report',
                'slug'        => 'report-policy-type',
                'description' => 'Sales breakdown by policy type (Level, Graded, G.I, Modified)',
                'category'    => 'Reports',
                'sort_order'  => 502,
            ],
            [
                'name'        => 'Sales Status Report',
                'slug'        => 'report-sales-status',
                'description' => 'All pipeline stages per carrier in one view',
                'category'    => 'Reports',
                'sort_order'  => 503,
            ],
            [
                'name'        => 'Dialer Report',
                'slug'        => 'report-disposition',
                'description' => 'Per-closer breakdown of End Call & Save & Exit dispositions',
                'category'    => 'Reports',
                'sort_order'  => 504,
            ],
            [
                'name'        => 'Closer Performance Report',
                'slug'        => 'report-closer',
                'description' => 'Per-closer sales metrics - sales, approved, declined, paid & chargeback counts',
                'category'    => 'Reports',
                'sort_order'  => 505,
            ],
            [
                'name'        => 'Manager Submission Report',
                'slug'        => 'report-manager-submission',
                'description' => 'Per-manager count of sales approved to Pending Contract or Declined',
                'category'    => 'Reports',
                'sort_order'  => 506,
            ],
            [
                'name'        => 'Peregrine Team Report',
                'slug'        => 'report-peregrine-team',
                'description' => 'PJC submissions, Closer pipeline & Validator outcomes',
                'category'    => 'Reports',
                'sort_order'  => 507,
            ],
            [
                'name'        => 'Zoom Logs',
                'slug'        => 'report-zoom-logs',
                'description' => 'Call recordings, durations & Zoom session history',
                'category'    => 'Reports',
                'sort_order'  => 508,
            ],
        ];

        $now = now();
        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $module['slug']],
                array_merge($module, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        $slugs = [
            'report-sales-export',
            'report-submission-performance',
            'report-policy-type',
            'report-sales-status',
            'report-disposition',
            'report-closer',
            'report-manager-submission',
            'report-peregrine-team',
            'report-zoom-logs',
        ];
        DB::table('modules')->whereIn('slug', $slugs)->delete();
    }
};
