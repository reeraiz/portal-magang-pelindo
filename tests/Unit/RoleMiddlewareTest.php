<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_intern_is_redirected_away_from_admin_dashboard(): void
    {
        $intern = User::factory()->create(['role' => 'intern']);

        $response = $this->actingAs($intern)->get('/admin/dashboard');
        $response->assertRedirect('/intern/absensi');
    }

    public function test_admin_is_redirected_away_from_intern_absensi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/intern/absensi');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_pembimbing_can_access_admin_dashboard(): void
    {
        $mentor = User::factory()->create(['role' => 'pembimbing']);

        $response = $this->actingAs($mentor)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
