<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadDeduplicationService;
use App\Http\Controllers\Admin\RavensDashboardController;
use App\Support\Teams;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Regression coverage for the CC Partner team exclusion fixes.
 *
 * CC Partner (team=cc_partner) leads are pre-vetted outsourced sales that must
 * never be treated like raw uncalled leads: they must stay excluded from the
 * dedup service's auto-merge/delete, and from the Ravens cold-calling queue,
 * exactly like Peregrine team leads already are. See
 * app/Services/LeadDeduplicationService.php and
 * app/Http/Controllers/Admin/RavensDashboardController::calling().
 */
class CcPartnerTeamExclusionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dedup_excludes_both_peregrine_and_cc_partner_duplicate_phone_leads()
    {
        foreach ([Teams::PEREGRINE, Teams::CC_PARTNER] as $team) {
            Lead::create([
                'cn_name' => "Dup A ({$team})",
                'closer_name' => 'Closer A',
                'phone_number' => '5551234567',
                'team' => $team,
            ]);
            Lead::create([
                'cn_name' => "Dup B ({$team})",
                'closer_name' => 'Closer B',
                'phone_number' => '5551234567',
                'team' => $team,
            ]);
        }

        $countBefore = Lead::where('phone_number', '5551234567')->count();
        $this->assertEquals(4, $countBefore);

        app(LeadDeduplicationService::class)->deduplicateByPhone();

        $countAfter = Lead::where('phone_number', '5551234567')->count();
        $this->assertEquals(
            $countBefore,
            $countAfter,
            'Peregrine and CC Partner duplicate-phone leads must never be auto-merged/deleted.'
        );
    }

    /** @test */
    public function dedup_still_merges_duplicate_phone_leads_outside_the_protected_teams()
    {
        Lead::create([
            'cn_name' => 'Dup A',
            'closer_name' => 'Closer A',
            'phone_number' => '5559876543',
            'team' => Teams::RAVENS,
        ]);
        Lead::create([
            'cn_name' => 'Dup B',
            'closer_name' => 'Closer B',
            'phone_number' => '5559876543',
            'team' => Teams::RAVENS,
        ]);

        app(LeadDeduplicationService::class)->deduplicateByPhone();

        $this->assertEquals(
            1,
            Lead::where('phone_number', '5559876543')->count(),
            'Non-protected duplicate-phone leads should still be merged as before.'
        );
    }

    /** @test */
    public function ravens_calling_queue_excludes_cc_partner_leads()
    {
        $user = User::create([
            'name' => 'Ravens Test User',
            'email' => 'ravens-test-user@example.com',
            'password' => Hash::make('password'),
            'is_partner' => false,
        ]);

        Lead::create([
            'cn_name' => 'CC Partner Lead',
            'closer_name' => 'Falcon Closer',
            'phone_number' => '5551112222',
            'team' => Teams::CC_PARTNER,
            'assigned_partner' => 'Falcon OS',
            'status' => 'pending',
        ]);

        $this->actingAs($user);
        $view = app(RavensDashboardController::class)->calling(new Request());
        $leads = $view->getData()['leads'];

        $phones = collect($leads->items())->pluck('phone_number');
        $this->assertNotContains(
            '5551112222',
            $phones,
            'CC Partner leads must not appear in the Ravens cold-calling queue.'
        );
    }
}
