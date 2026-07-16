<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogbookTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_can_view_logbook_page(): void
    {
        $user = User::factory()->create(['role' => 'intern']);

        $response = $this->actingAs($user)->get('/intern/logbook');
        $response->assertStatus(200);
        $response->assertViewIs('intern.logbook');
    }

    public function test_intern_can_create_logbook(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'role' => 'intern',
            'internship_start_date' => '2026-07-01',
        ]);

        Carbon::setTestNow(Carbon::create(2026, 7, 6, 16, 30, 0, 'Asia/Makassar'));
        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($user)->post('/intern/logbook', [
            'date' => '2026-07-06',
            'title' => 'Mengerjakan Modul Login',
            'description' => 'Membuat validasi form login dengan Laravel.',
            'attachment' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('logbooks', [
            'user_id' => $user->id,
            'title' => 'Mengerjakan Modul Login',
            'status' => 'pending',
        ]);
        $this->assertEquals('2026-07-06', Logbook::first()->date->format('Y-m-d'));

        Carbon::setTestNow();
    }

    public function test_intern_cannot_create_logbook_before_internship_start_date(): void
    {
        $user = User::factory()->create([
            'role' => 'intern',
            'internship_start_date' => '2026-07-01',
        ]);

        Carbon::setTestNow(Carbon::create(2026, 7, 6, 12, 0, 0, 'Asia/Makassar'));

        $response = $this->actingAs($user)->post('/intern/logbook', [
            'date' => '2026-06-15', // Sebelum tanggal mulai magang (VULN-06 fix test)
            'title' => 'Aktivitas Lama',
            'description' => 'Mencoba mengisi logbook masa lalu.',
        ]);

        $response->assertSessionHasErrors(['date']);
        Carbon::setTestNow();
    }

    public function test_intern_can_update_pending_logbook(): void
    {
        $user = User::factory()->create(['role' => 'intern', 'internship_start_date' => '2026-07-01']);
        $logbook = Logbook::create([
            'user_id' => $user->id,
            'date' => '2026-07-05',
            'time' => '10:00:00',
            'title' => 'Judul Lama',
            'description' => 'Deskripsi Lama',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->put('/intern/logbook/'.$logbook->id, [
            'date' => '2026-07-05',
            'title' => 'Judul Baru Diupdate',
            'description' => 'Deskripsi Baru Diupdate',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('logbooks', [
            'id' => $logbook->id,
            'title' => 'Judul Baru Diupdate',
        ]);
    }

    public function test_intern_cannot_update_verified_logbook(): void
    {
        $user = User::factory()->create(['role' => 'intern', 'internship_start_date' => '2026-07-01']);
        $logbook = Logbook::create([
            'user_id' => $user->id,
            'date' => '2026-07-05',
            'time' => '10:00:00',
            'title' => 'Judul Verified',
            'description' => 'Deskripsi Verified',
            'status' => 'verified',
        ]);

        $response = $this->actingAs($user)->put('/intern/logbook/'.$logbook->id, [
            'date' => '2026-07-05',
            'title' => 'Mencoba Hack Judul',
            'description' => 'Mencoba Hack Deskripsi',
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('logbooks', [
            'id' => $logbook->id,
            'title' => 'Judul Verified',
        ]);
    }

    public function test_intern_cannot_delete_another_interns_logbook(): void
    {
        $userA = User::factory()->create(['role' => 'intern']);
        $userB = User::factory()->create(['role' => 'intern']);

        $logbookB = Logbook::create([
            'user_id' => $userB->id,
            'date' => '2026-07-05',
            'time' => '10:00:00',
            'title' => 'Logbook User B',
            'description' => 'Deskripsi User B',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($userA)->delete('/intern/logbook/'.$logbookB->id);

        $response->assertStatus(404); // firstOrFail akan throw 404
        $this->assertDatabaseHas('logbooks', ['id' => $logbookB->id]);
    }
}
