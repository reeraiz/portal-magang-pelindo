<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    public function test_admin_can_verify_absensi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $intern = User::factory()->create(['role' => 'intern']);

        $attendance = Attendance::create([
            'user_id' => $intern->id,
            'date' => '2026-07-06',
            'check_in' => '08:00:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post('/admin/verify-absensi/'.$attendance->id, [
            'status' => 'verified',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'verified',
        ]);
    }

    public function test_admin_can_verify_logbook_with_feedback(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $intern = User::factory()->create(['role' => 'intern']);

        $logbook = Logbook::create([
            'user_id' => $intern->id,
            'date' => '2026-07-06',
            'time' => '10:00:00',
            'title' => 'Laporan Harian',
            'description' => 'Mengerjakan tugas',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post('/admin/verify-logbook/'.$logbook->id, [
            'action' => 'approve',
            'feedback' => 'Kerja bagus, lanjutkan!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('logbooks', [
            'id' => $logbook->id,
            'status' => 'verified',
            'feedback' => 'Kerja bagus, lanjutkan!',
        ]);
    }

    public function test_pembimbing_can_only_verify_assigned_interns_logbook(): void
    {
        $mentorA = User::factory()->create(['role' => 'pembimbing']);
        $mentorB = User::factory()->create(['role' => 'pembimbing']);

        $internA = User::factory()->create(['role' => 'intern', 'mentor_id' => $mentorA->id]);
        $internB = User::factory()->create(['role' => 'intern', 'mentor_id' => $mentorB->id]);

        $logbookB = Logbook::create([
            'user_id' => $internB->id,
            'date' => '2026-07-06',
            'time' => '10:00:00',
            'title' => 'Logbook Intern B',
            'description' => 'Deskripsi Intern B',
            'status' => 'pending',
        ]);

        // Mentor A mencoba verifikasi logbook milik Intern B (VULN-03 test)
        $response = $this->actingAs($mentorA)->post('/admin/verify-logbook/'.$logbookB->id, [
            'action' => 'approve',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('logbooks', [
            'id' => $logbookB->id,
            'status' => 'pending',
        ]);
    }

    public function test_reset_password_generates_random_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $intern = User::factory()->create([
            'role' => 'intern',
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($admin)->post('/admin/interns/'.$intern->id.'/reset-password');

        $response->assertRedirect();

        // Cek bahwa password sudah berubah (bukan lagi "oldpassword" dan bukan "password" statis)
        $intern->refresh();
        $this->assertFalse(Hash::check('oldpassword', $intern->password));
        $this->assertFalse(Hash::check('password', $intern->password));
    }
}
