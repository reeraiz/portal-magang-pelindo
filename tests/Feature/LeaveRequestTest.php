<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_can_view_leaves_page(): void
    {
        $intern = User::factory()->create(['role' => 'intern']);

        $response = $this->actingAs($intern)->get('/intern/leaves');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Izin & Cuti', false);
    }

    public function test_intern_can_submit_leave_request_with_date_range(): void
    {
        Storage::fake('public');
        $intern = User::factory()->create(['role' => 'intern']);

        $file = UploadedFile::fake()->image('surat_dokter.jpg');

        $response = $this->actingAs($intern)->post('/intern/leaves', [
            'type' => 'sakit',
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'notes' => 'Demam tinggi dan istirahat dokter',
            'attachment' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $intern->id,
            'type' => 'sakit',
            'start_date' => '2026-07-10 00:00:00',
            'end_date' => '2026-07-12 00:00:00',
            'notes' => 'Demam tinggi dan istirahat dokter',
            'status' => 'pending',
        ]);

        $leave = LeaveRequest::first();
        $this->assertEquals(3, $leave->duration_days);
        $this->assertNotNull($leave->attachment);
        Storage::disk('public')->assertExists($leave->attachment);
    }

    public function test_leave_request_requires_valid_date_range(): void
    {
        $intern = User::factory()->create(['role' => 'intern']);

        $response = $this->actingAs($intern)->post('/intern/leaves', [
            'type' => 'izin',
            'start_date' => '2026-07-15',
            'end_date' => '2026-07-10', // Invalid: sebelum start_date
            'notes' => 'Alasan izin',
        ]);

        $response->assertSessionHasErrors(['end_date']);
        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_admin_can_view_and_verify_leave_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $intern = User::factory()->create(['role' => 'intern']);

        $leave = LeaveRequest::create([
            'user_id' => $intern->id,
            'type' => 'cuti',
            'start_date' => '2026-07-20',
            'end_date' => '2026-07-22',
            'notes' => 'Cuti tahunan',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/leaves');
        $response->assertStatus(200);
        $response->assertSee('Cuti tahunan');

        $verifyResponse = $this->actingAs($admin)->post('/admin/verify-leave/'.$leave->id, [
            'status' => 'approved',
            'admin_note' => 'Disetujui admin',
        ]);

        $verifyResponse->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
            'admin_note' => 'Disetujui admin',
        ]);
    }

    public function test_mentor_can_only_verify_own_intern_leave_request(): void
    {
        $mentor1 = User::factory()->create(['role' => 'pembimbing']);
        $mentor2 = User::factory()->create(['role' => 'pembimbing']);

        $intern1 = User::factory()->create(['role' => 'intern', 'mentor_id' => $mentor1->id]);
        $intern2 = User::factory()->create(['role' => 'intern', 'mentor_id' => $mentor2->id]);

        $leave2 = LeaveRequest::create([
            'user_id' => $intern2->id,
            'type' => 'izin',
            'start_date' => '2026-07-25',
            'end_date' => '2026-07-25',
            'notes' => 'Izin keluarga',
            'status' => 'pending',
        ]);

        // Mentor 1 mencoba verifikasi izin dari intern 2 (bukan bimbingannya)
        $response = $this->actingAs($mentor1)->post('/admin/verify-leave/'.$leave2->id, [
            'status' => 'approved',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave2->id,
            'status' => 'pending',
        ]);
    }
}
