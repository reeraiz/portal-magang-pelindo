<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InternshipType;
use App\Models\EducationLevel;
use App\Models\University;
use App\Models\Gender;

class ProfileMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Internship Types
        $internshipTypes = [
            'MSIB (Magang & Studi Independen Bersertifikat)',
            'Mandiri / Reguler',
            'PMMB (Program Magang Mahasiswa Bersertifikat)',
            'PKL (Praktek Kerja Lapangan) Sekolah',
        ];
        foreach ($internshipTypes as $type) {
            InternshipType::firstOrCreate(['name' => $type]);
        }

        // Create Education Levels
        $educationLevels = [
            'SMK / SMA',
            'Diploma 3 (D3)',
            'Diploma 4 (D4)',
            'Sarjana (S1)',
            'Pascasarjana (S2/S3)',
        ];
        foreach ($educationLevels as $level) {
            EducationLevel::firstOrCreate(['name' => $level]);
        }

        // Create Genders
        $genders = [
            'Laki-laki',
            'Perempuan',
        ];
        foreach ($genders as $gender) {
            Gender::firstOrCreate(['name' => $gender]);
        }

        // Create Universities
        $universities = [
            'Universitas Indonesia',
            'Universitas Gadjah Mada',
            'Institut Teknologi Bandung',
            'Universitas Airlangga',
            'Universitas Diponegoro',
            'Universitas Brawijaya',
            'Institut Teknologi Sepuluh Nopember',
            'Telkom University',
            'Universitas Hasanuddin',
            'Politeknik Negeri Jakarta',
            'SMK Negeri 1 Jakarta',
            'Politeknik Maritim AMI Makassar',
            'Politeknik Pariwisata Makassar',
            'Universitas Negeri Makassar',
        ];
        foreach ($universities as $uni) {
            University::firstOrCreate(['name' => $uni]);
        }
    }
}
