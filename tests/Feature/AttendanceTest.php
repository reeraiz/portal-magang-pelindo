<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_can_view_absensi_page(): void
    {
        $user = User::factory()->create(['role' => 'intern']);

        $response = $this->actingAs($user)->get('/intern/absensi');
        $response->assertStatus(200);
        $response->assertViewIs('intern.absensi');
    }

    public function test_intern_can_check_in_with_wita_timezone(): void
    {
        $user = User::factory()->create(['role' => 'intern']);

        // Simulasikan waktu jam 07:50 WITA (Asia/Makassar)
        Carbon::setTestNow(Carbon::create(2026, 7, 6, 7, 50, 0, 'Asia/Makassar'));

        $response = $this->actingAs($user)->post('/intern/absensi', [
            'type' => 'check_in',
            'location' => 'WFO - Kantor Pusat',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Absensi berhasil disimpan!');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'check_in' => '07:50:00',
            'status' => 'verified',
            'notes' => 'Tepat Waktu',
        ]);
        $this->assertEquals('2026-07-06', Attendance::first()->date->format('Y-m-d'));

        Carbon::setTestNow();
    }

    public function test_intern_check_in_late_records_selisih_menit(): void
    {
        $user = User::factory()->create(['role' => 'intern']);

        // Simulasikan waktu jam 08:15 WITA (Asia/Makassar)
        Carbon::setTestNow(Carbon::create(2026, 7, 6, 8, 15, 0, 'Asia/Makassar'));

        $response = $this->actingAs($user)->post('/intern/absensi', [
            'type' => 'check_in',
            'location' => 'WFO - Kantor Pusat',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'notes' => 'Terlambat 15 menit',
        ]);
        $this->assertEquals('2026-07-06', Attendance::first()->date->format('Y-m-d'));

        Carbon::setTestNow();
    }

    public function test_intern_can_check_out(): void
    {
        $user = User::factory()->create(['role' => 'intern']);

        Carbon::setTestNow(Carbon::create(2026, 7, 6, 8, 0, 0, 'Asia/Makassar'));
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-06',
            'check_in' => '08:00:00',
            'status' => 'verified',
        ]);

        Carbon::setTestNow(Carbon::create(2026, 7, 6, 17, 0, 0, 'Asia/Makassar'));
        $response = $this->actingAs($user)->post('/intern/absensi', [
            'type' => 'check_out',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'check_out' => '17:00:00',
        ]);
        $this->assertEquals('2026-07-06', Attendance::first()->date->format('Y-m-d'));

        Carbon::setTestNow();
    }

    public function test_intern_cannot_check_in_if_already_submitted_izin(): void
    {
        $user = User::factory()->create(['role' => 'intern']);

        Carbon::setTestNow(Carbon::create(2026, 7, 6, 8, 0, 0, 'Asia/Makassar'));
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-06',
            'status' => 'izin',
            'notes' => '[IZIN] Acara keluarga',
        ]);

        $response = $this->actingAs($user)->post('/intern/absensi', [
            'type' => 'check_in',
        ]);

        $response->assertSessionHasErrors(['error']);
        Carbon::setTestNow();
    }

    public function test_intern_can_submit_izin(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'intern']);

        Carbon::setTestNow(Carbon::create(2026, 7, 6, 7, 0, 0, 'Asia/Makassar'));
        $file = UploadedFile::fake()->image('surat_sakit.jpg');

        $response = $this->actingAs($user)->post('/intern/absensi/izin', [
            'reason' => 'sakit',
            'notes' => 'Demam tinggi',
            'attachment' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => 'pending',
            'notes' => '[SAKIT] Demam tinggi',
        ]);
        $this->assertEquals('2026-07-06', Attendance::first()->date->format('Y-m-d'));

        Carbon::setTestNow();
    }
}
