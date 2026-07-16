<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Division;
use App\Models\Logbook;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create Divisions
        $divisionNames = [
            'Departemen Teknik',
            'Departement Head Perbendaharaan',
            'Departement Head Pelaporan',
            'Departement Head Pemeliharaan Peralatan Pelabuhan',
            'Departement Head Pemeliharaan Fasilitas Pelabuhan',
            'Departement Head Pelayanan SDM',
        ];

        foreach ($divisionNames as $name) {
            Division::create(['name' => $name]);
        }

        // Create Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pelindo.co.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'division' => 'Departemen Teknik',
        ]);

        // Create Pembimbing
        User::create([
            'name' => 'Pembimbing Magang',
            'email' => 'pembimbing@pelindo.co.id',
            'password' => Hash::make('password'),
            'role' => 'pembimbing',
            'division' => 'Departemen Teknik',
        ]);

        User::create([
            'name' => 'Diana (Pembimbing Magang)',
            'email' => 'diana@pelindo.co.id',
            'password' => Hash::make('password'),
            'role' => 'pembimbing',
            'division' => 'Departement Head Pelayanan SDM',
        ]);

        // Create Interns (Irham, Oji, Reefat + 2 tambahan)
        $internData = [
            ['name' => 'Irham', 'email' => 'irham@pelindo.co.id', 'intern_id' => 'INT-001'],
            ['name' => 'Oji', 'email' => 'oji@pelindo.co.id', 'intern_id' => 'INT-002'],
            ['name' => 'Reefat', 'email' => 'reefat@pelindo.co.id', 'intern_id' => 'INT-003'],
            ['name' => 'Andi Pratama', 'email' => 'intern4@pelindo.co.id', 'intern_id' => 'INT-004'],
            ['name' => 'Siti Nurhaliza', 'email' => 'intern5@pelindo.co.id', 'intern_id' => 'INT-005'],
        ];

        $interns = [];
        foreach ($internData as $data) {
            $interns[] = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'intern',
                'intern_id' => $data['intern_id'],
                'division' => collect($divisionNames)->random(),
                'internship_start_date' => Carbon::now()->subDays(15)->toDateString(),
                'internship_end_date' => Carbon::now()->addDays(45)->toDateString(),
            ]);
        }

        // Create Attendances & Logbooks for the last 5 days
        foreach ($interns as $intern) {
            for ($i = 4; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // Random Attendance
                $status = collect(['hadir', 'hadir', 'hadir', 'izin', 'sakit'])->random();

                if ($status == 'hadir') {
                    // Jam masuk acak antara 07:30 - 08:15
                    $checkInHour = 7;
                    $checkInMin = rand(30, 59);
                    if (rand(0, 3) > 0) { // 75% chance masuk jam 8
                        $checkInHour = 8;
                        $checkInMin = rand(0, 15);
                    }
                    $checkInTime = sprintf('%02d:%02d:00', $checkInHour, $checkInMin);

                    // Jam pulang acak antara 16:00 - 17:00
                    $checkOutTime = sprintf('16:%02d:00', rand(0, 59));

                    $isLate = ($checkInHour > 8 || ($checkInHour == 8 && $checkInMin > 0));
                    $lateMinutes = $isLate ? (($checkInHour - 8) * 60 + $checkInMin) : 0;

                    $attendance = Attendance::create([
                        'user_id' => $intern->id,
                        'date' => $date->toDateString(),
                        'check_in' => $checkInTime,
                        'check_out' => $checkOutTime,
                        'status' => 'verified',
                        'location' => 'WFO - Kantor Pusat',
                        'notes' => $isLate ? 'Terlambat '.$lateMinutes.' menit' : 'Tepat Waktu',
                    ]);

                    // Random Logbook
                    $activities = [
                        'Mengembangkan antarmuka pengguna (Frontend)',
                        'Merancang skema database',
                        'Memperbaiki bug pada modul pembayaran',
                        'Menganalisis kebutuhan sistem',
                        'Melakukan pengujian API',
                        'Menulis dokumentasi teknis',
                    ];

                    Logbook::create([
                        'user_id' => $intern->id,
                        'date' => $date->toDateString(),
                        'time' => '08:00 - 16:30',
                        'category' => collect(['Pengembangan', 'Riset', 'Desain', 'Testing'])->random(),
                        'title' => collect($activities)->random(),
                        'description' => $faker->paragraph(3),
                        'status' => collect(['pending', 'verified'])->random(),
                    ]);
                } else {
                    $reasons = [
                        'izin' => 'Keperluan keluarga mendadak',
                        'sakit' => 'Sakit demam dan flu, surat dokter menyusul',
                    ];

                    Attendance::create([
                        'user_id' => $intern->id,
                        'date' => $date->toDateString(),
                        'check_in' => null,
                        'check_out' => null,
                        'status' => 'pending', // Pending approval by admin, or approved
                        'location' => '-',
                        'notes' => '['.ucfirst($status).'] '.$reasons[$status],
                    ]);
                }
            }
        }
    }
}
