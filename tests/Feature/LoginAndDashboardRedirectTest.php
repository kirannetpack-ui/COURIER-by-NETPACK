<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginAndDashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'super@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'super_admin',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'super@netpack.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->actingAs($user)->get('/admin/dashboard')->assertOk();
    }

    public function test_domestic_admin_redirects_to_domestic_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'domestic@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'domestic_admin',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'domestic@netpack.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/domestic/dashboard');
        $this->actingAs($user)->get('/domestic/dashboard')->assertOk();
    }

    public function test_staff_user_redirects_to_domestic_dashboard_without_403(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'staff',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'staff@netpack.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/domestic/dashboard');
        $this->actingAs($user)->get('/domestic/dashboard')->assertOk();
    }

    public function test_international_admin_redirects_to_international_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'intl@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'international_admin',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'intl@netpack.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/international/dashboard');
        $this->actingAs($user)->get('/international/dashboard')->assertOk();
    }

    public function test_client_and_customer_redirect_to_client_dashboard(): void
    {
        $client = User::factory()->create([
            'email' => 'client@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'client',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'client@netpack.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/client/dashboard');
        $this->actingAs($client)->get('/client/dashboard')->assertOk();

        $customer = User::factory()->create([
            'email' => 'cust@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'customer',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        $responseCust = $this->post('/login', [
            'email' => 'cust@netpack.test',
            'password' => 'password123',
        ]);

        $responseCust->assertRedirect('/client/dashboard');
        $this->actingAs($customer)->get('/client/dashboard')->assertOk();
    }

    public function test_cross_portal_intended_session_url_is_sanitized_preventing_403(): void
    {
        $domesticAdmin = User::factory()->create([
            'email' => 'domadmin@netpack.test',
            'password' => bcrypt('password123'),
            'user_type' => 'domestic_admin',
            'verification_status' => 'approved',
            'password_changed' => true,
        ]);

        // Simulate session having url.intended = /client/dashboard from a previous guest attempt
        $response = $this->withSession(['url.intended' => 'http://localhost/client/dashboard'])
            ->post('/login', [
                'email' => 'domadmin@netpack.test',
                'password' => 'password123',
            ]);

        // Must NOT redirect to /client/dashboard (which would cause 403)
        $response->assertRedirect('/domestic/dashboard');
        $this->assertNotSame('/client/dashboard', $response->headers->get('Location'));
    }

    public function test_home_controller_redirects_each_role_to_proper_dashboard(): void
    {
        $staff = User::factory()->create(['user_type' => 'staff', 'verification_status' => 'approved']);
        $this->actingAs($staff)->get('/dashboard')->assertRedirect(route('domestic.dashboard'));

        $seller = User::factory()->create(['user_type' => 'seller', 'verification_status' => 'approved']);
        $this->actingAs($seller)->get('/dashboard')->assertRedirect(route('seller.dashboard'));

        $rider = User::factory()->create(['user_type' => 'rider', 'verification_status' => 'approved']);
        $this->actingAs($rider)->get('/dashboard')->assertRedirect(route('rider.dashboard'));

        $client = User::factory()->create(['user_type' => 'client', 'verification_status' => 'approved']);
        $this->actingAs($client)->get('/dashboard')->assertRedirect(route('client.dashboard'));
    }
}
