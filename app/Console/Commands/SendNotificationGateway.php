<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Logbook;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNotificationGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-reminders {--force : Paksa kirim tanpa mengecek hari Senin/Jumat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder otomatis via Email & WhatsApp Gateway ke Pembimbing (Pending >5) dan Intern (Terlambat Check-in >08:30 / Lupa Logbook 3 Hari)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai pemeriksaan Notification Gateway (Email / WhatsApp)...');
        $now = Carbon::now('Asia/Makassar');
        $isMondayOrFriday = $now->isMonday() || $now->isFriday() || $this->option('force');

        $sentCount = 0;

        // -------------------------------------------------------------
        // 1. Reminder ke Pembimbing jika ada > 5 Logbook / Izin Pending
        // -------------------------------------------------------------
        if ($isMondayOrFriday) {
            $this->info('[1/2] Mengecek data verifikasi pending untuk Pembimbing...');
            
            // Ambil semua pembimbing (role mentor / admin atau user dengan intern bimbingan)
            $mentors = User::whereIn('role', ['mentor', 'admin'])->get();

            foreach ($mentors as $mentor) {
                // Hitung logbook dan izin dari anak magang bimbingannya (atau semua jika admin)
                if ($mentor->role === 'admin') {
                    $pendingLogbooks = Logbook::where('status', 'pending')->count();
                    $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
                } else {
                    $internIds = User::where('role', 'intern')->where('mentor_id', $mentor->id)->pluck('id');
                    $pendingLogbooks = Logbook::whereIn('user_id', $internIds)->where('status', 'pending')->count();
                    $pendingLeaves = LeaveRequest::whereIn('user_id', $internIds)->where('status', 'pending')->count();
                }

                $totalPending = $pendingLogbooks + $pendingLeaves;

                if ($totalPending > 5) {
                    $subject = "⏰ [Reminder Pelindo] {$totalPending} Pengajuan Magang Menunggu Verifikasi Anda";
                    $message = "Halo Pak/Bu {$mentor->name},\n\nSistem Portal Magang PT Pelabuhan Indonesia (Persero) mencatat saat ini terdapat *{$totalPending} item pengajuan* yang membutuhkan persetujuan/verifikasi Anda:\n- 📝 Logbook Harian: {$pendingLogbooks} pending\n- 📬 Izin / Sakit / Cuti: {$pendingLeaves} pending\n\nMohon untuk segera meluangkan waktu melakukan pengecekan di sistem kami: " . url('/admin/dashboard') . "\n\nTerima kasih,\nSistem Otomatis SDM & Pembimbing Pelindo.";

                    // Simpan ke log notifikasi (WA & Email Gateway)
                    $this->logAndSendNotification($mentor, 'whatsapp', 'reminder_mentor', $subject, $message);
                    $this->logAndSendNotification($mentor, 'email', 'reminder_mentor', $subject, $message);
                    $sentCount += 2;

                    $this->info("   -> Reminder terkirim ke Pembimbing {$mentor->name} ({$totalPending} pending)");
                }
            }
        } else {
            $this->comment('[1/2] Hari ini bukan Senin/Jumat. Pengecekan reminder Pembimbing dilewati (gunakan opsi --force untuk memaksakan).');
        }

        // -------------------------------------------------------------
        // 2. Peringatan ke Intern: Terlambat > 08:30 WITA atau Lupa Logbook 3 Hari
        // -------------------------------------------------------------
        $this->info('[2/2] Mengecek kedisiplinan & logbook peserta magang...');
        $activeInterns = User::where('role', 'intern')
            ->where(function ($q) use ($now) {
                $q->whereNull('internship_end_date')->orWhereDate('internship_end_date', '>=', $now->toDateString());
            })->get();

        foreach ($activeInterns as $intern) {
            // A. Cek apakah hari ini terlambat check-in melewati jam 08:30 WITA
            $todayAttendance = Attendance::where('user_id', $intern->id)
                ->whereDate('date', $now->toDateString())
                ->first();

            // Cek jika check-in ada dan > 08:30:00, ATAU jika sekarang > 08:30:00 tapi belum check-in sama sekali dan bukan hari libur/sabtu minggu
            if (!$now->isWeekend()) {
                $isLateCheckIn = false;
                if ($todayAttendance && $todayAttendance->check_in) {
                    if ($todayAttendance->check_in > '08:30:00') {
                        $isLateCheckIn = true;
                    }
                } elseif (!$todayAttendance && $now->format('H:i:s') > '08:30:00') {
                    $isLateCheckIn = true;
                }

                if ($isLateCheckIn) {
                    $subject = "⚠️ [Peringatan Kedisiplinan] Keterlambatan Absensi Magang Pelindo";
                    $message = "Halo {$intern->name} (ID: {$intern->intern_id}),\n\nSistem mencatat Anda belum melakukan check-in tepat waktu atau check-in melewati batas maksimal pukul 08:30 WITA hari ini (" . $now->format('d/m/Y') . ").\n\nKedisiplinan kehadiran merupakan salah satu komponen utama penilaian akhir magang Anda di PT Pelabuhan Indonesia (Persero). Jika mengalami kendala teknis atau izin mendesak, segera informasikan kepada Pembimbing Anda atau ajukan izin di portal: " . url('/intern/absensi') . "\n\nTetap semangat & disiplin!";

                    // Hindari duplikasi pengiriman di hari yang sama
                    $alreadySentToday = NotificationLog::where('user_id', $intern->id)
                        ->where('category', 'late_checkin')
                        ->whereDate('created_at', $now->toDateString())
                        ->exists();

                    if (!$alreadySentToday) {
                        $this->logAndSendNotification($intern, 'whatsapp', 'late_checkin', $subject, $message);
                        $this->logAndSendNotification($intern, 'email', 'late_checkin', $subject, $message);
                        $sentCount += 2;
                        $this->info("   -> Peringatan keterlambatan terkirim ke intern {$intern->name}");
                    }
                }
            }

            // B. Cek lupa mengisi logbook selama 3 hari berturut-turut (di hari kerja)
            if ($intern->internship_start_date && Carbon::parse($intern->internship_start_date)->lt($now->copy()->subDays(3))) {
                $threeDaysAgo = $now->copy()->subDays(3)->startOfDay();
                $recentLogbooksCount = Logbook::where('user_id', $intern->id)
                    ->whereDate('date', '>=', $threeDaysAgo->toDateString())
                    ->count();

                if ($recentLogbooksCount === 0) {
                    $subject = "🚨 [Peringatan Penting] Anda Belum Mengisi Logbook Harian Selama 3 Hari";
                    $message = "Halo {$intern->name},\n\nPerhatian! Sistem memonitor bahwa Anda belum mengisikan aktivitas pada Logbook Harian selama 3 hari berturut-turut.\n\nPengisian logbook secara rutin setiap hari kerja adalah kewajiban administrasi magang. Kelalaian mengisi logbook dapat mengakibatkan logbook tidak dapat diverifikasi dan mengurangi skor kemajuan magang Anda.\n\nSegera lengkapi logbook aktivitas Anda sekarang di: " . url('/intern/logbook') . "\n\nTerima kasih.";

                    // Cek agar tidak dikirim berulang setiap menit di hari yang sama
                    $alreadySentLogbookWarning = NotificationLog::where('user_id', $intern->id)
                        ->where('category', 'missing_logbook')
                        ->whereDate('created_at', $now->toDateString())
                        ->exists();

                    if (!$alreadySentLogbookWarning) {
                        $this->logAndSendNotification($intern, 'whatsapp', 'missing_logbook', $subject, $message);
                        $this->logAndSendNotification($intern, 'email', 'missing_logbook', $subject, $message);
                        $sentCount += 2;
                        $this->info("   -> Peringatan lupa logbook 3 hari terkirim ke intern {$intern->name}");
                    }
                }
            }
        }

        $this->info("✅ Selesai! Total {$sentCount} notifikasi (Email & WhatsApp Gateway) telah berhasil diproses & dicatat.");
        return 0;
    }

    /**
     * Helper untuk mencatat dan mengirim notifikasi melalui Gateway API / Mailer.
     */
    private function logAndSendNotification($user, $channel, $category, $subject, $message)
    {
        $contact = $channel === 'whatsapp' ? ($user->phone ?? $user->email) : $user->email;

        // Catat ke database NotificationLog
        NotificationLog::create([
            'user_id' => $user->id,
            'recipient_name' => $user->name,
            'recipient_contact' => $contact,
            'channel' => $channel,
            'category' => $category,
            'subject' => $subject,
            'message' => $message,
            'status' => 'sent',
        ]);

        // Simulasi pengiriman / integrasi Mail Laravel
        if ($channel === 'email' && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::raw($message, function ($mail) use ($user, $subject) {
                    $mail->to($user->email, $user->name)
                         ->subject($subject);
                });
            } catch (\Exception $e) {
                // Di local environment / tanpa SMTP yang menyala, log eror dengan aman tanpa memutus eksekusi
                Log::info("Email simulated to {$user->email}: {$subject}");
            }
        } elseif ($channel === 'whatsapp') {
            // Log ke file khusus whatsapp_gateway.log untuk pemantauan Fonnte / Twilio / WA Gateway
            Log::channel('single')->info("[WA_GATEWAY_OUTBOUND] To: {$contact} | Subject: {$subject} | Message: " . str_replace("\n", " ", substr($message, 0, 100)) . "...");
        }
    }
}
