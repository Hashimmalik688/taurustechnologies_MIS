<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PartnerUserSeparationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function partners_are_excluded_from_user_queries()
    {
        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_partner' => false,
        ]);

        // Create user marked as partner (legacy)
        $legacyPartner = User::create([
            'name' => 'Legacy Partner',
            'email' => 'legacy@example.com',
            'password' => Hash::make('password'),
            'is_partner' => true,
        ]);

        // Test excludePartners scope
        $employees = User::excludePartners()->get();
        
        $this->assertCount(1, $employees);
        $this->assertEquals('Regular User', $employees->first()->name);
        $this->assertFalse($employees->contains($legacyPartner));
    }

    /** @test */
    public function partner_guard_uses_partners_table()
    {
        // Create partner in partners table
        $partner = Partner::create([
            'name' => 'Test Partner',
            'code' => 'T-1',
            'email' => 'partner@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // Attempt login via partner guard
        $this->post(route('partner.login.submit'), [
            'email' => 'partner@example.com',
            'password' => 'password',
        ]);

        // Assert partner is authenticated via partner guard
        $this->assertTrue(Auth::guard('partner')->check());
        $this->assertFalse(Auth::guard('web')->check());
    }

    /** @test */
    public function user_guard_uses_users_table()
    {
        // Create user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_partner' => false,
        ]);

        // Assign role
        $user->assignRole('Employee');

        // Attempt login via web guard
        $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        // Assert user is authenticated via web guard
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('partner')->check());
    }

    /** @test */
    public function partner_cannot_access_user_routes()
    {
        // Create and authenticate partner
        $partner = Partner::create([
            'name' => 'Test Partner',
            'code' => 'T-1',
            'email' => 'partner@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        Auth::guard('partner')->login($partner);

        // Try to access user dashboard
        $response = $this->get(route('root'));

        // Should be logged out and redirected
        $response->assertRedirect(route('partner.login'));
        $this->assertFalse(Auth::guard('partner')->check());
    }

    /** @test */
    public function user_cannot_access_partner_routes()
    {
        // Create and authenticate user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_partner' => false,
        ]);

        $user->assignRole('Employee');
        Auth::guard('web')->login($user);

        // Try to access partner dashboard
        $response = $this->get(route('partner.dashboard'));

        // Should be logged out and redirected
        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('web')->check());
    }

    /** @test */
    public function chat_users_exclude_partners()
    {
        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_partner' => false,
        ]);

        // Create legacy partner user
        $legacyPartner = User::create([
            'name' => 'Legacy Partner',
            'email' => 'legacy@example.com',
            'password' => Hash::make('password'),
            'is_partner' => true,
        ]);

        $user->assignRole('Employee');
        $this->actingAs($user);

        // Get chat users
        $response = $this->get('/chat/users');
        
        $response->assertStatus(200);
        $json = $response->json();
        
        // Regular user should not see themselves or partners
        $userNames = collect($json['users'])->pluck('name')->toArray();
        $this->assertNotContains('Legacy Partner', $userNames);
    }
}
