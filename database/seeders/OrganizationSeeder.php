<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orgData = [
            'Divisi Komersial' => [
                'Departemen Pengusahaan Properti',
                'Departemen Pemasaran',
            ],
            'Divisi Operasi' => [
                'Departemen Pelayanan Kapal',
                'Departemen Pelayanan Petikemas dan Barang',
                'Departemen Pelayanan Roro & Penumpang',
                'Departemen HSSE',
                'Departemen Pelaporan',
            ],
            'Divisi Teknik' => [
                'Departemen Pemeliharaan Peralatan Pelabuhan',
                'Departemen Pemeliharaan Fasilitas Pelabuhan',
                'Departemen Sistem Manajemen',
                'Departemen IT',
                'Project Management Office',
            ],
            'Divisi Pelayanan SDM & Umum' => [
                'Departemen Pelayanan SDM',
                'Departemen Umum',
                'Departemen Hukum & Hubungan Masyarakat',
                'Departemen Pengadaan',
            ],
            'Divisi Anggaran, Akuntansi & Pelaporan' => [
                'Departemen Anggaran',
                'Departemen Akuntansi',
                'Departemen Pelaporan Keuangan',
                'Departemen Aset Tetap',
            ],
            'Divisi Pengelolaan Keuangan & Perpajakan' => [
                'Departemen Perpajakan',
                'Departemen Perbendaharaan',
                'Departemen Pusat Layanan Keuangan',
            ],
        ];

        foreach ($orgData as $divName => $departments) {
            $division = \App\Models\Division::firstOrCreate(['name' => $divName]);
            
            foreach ($departments as $deptName) {
                \App\Models\Department::firstOrCreate([
                    'division_id' => $division->id,
                    'name' => $deptName,
                ]);
            }
        }
    }
}
