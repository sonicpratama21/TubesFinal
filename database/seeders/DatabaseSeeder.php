<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Data awal diambil dari dump SQL lama (sistem18_smarthealth.sql).
     * Hash password dipakai apa adanya, sehingga password lama tetap berlaku.
     */
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            [
                'id' => 1, 'nama_lengkap' => 'Telko Medika', 'username' => 'Medika',
                'email' => 'telkomedika@gmail.com',
                'password' => '$2y$12$OWUK0M5rG1OwNjWtKOnqL.vwlYmfF4P5eYnt7hRkBAPAc0HVcEpBC',
                'role' => 'admin', 'created_at' => '2026-06-13 04:59:11',
            ],
            [
                'id' => 2, 'nama_lengkap' => 'Sonic Pratama', 'username' => 'Sonic',
                'email' => 'sonicpratama021@gmail.com',
                'password' => '$2y$10$NZ.qFkRWJqIHxkoZh7pDAO8Ns2bsnpW6mgC1xNPoL48KRsOSryaou',
                'role' => 'pengguna', 'created_at' => '2026-06-13 05:00:45',
            ],
        ]);

        DB::table('reservasi')->insertOrIgnore([
            [
                'id' => 5, 'user_id' => 2, 'nama_pasien' => 'Sonic Pratama', 'telpon' => '08978421889',
                'dokter' => 'dr. Agus Subianto', 'layanan' => 'Laboratorium', 'tanggal' => '2026-06-14',
                'jam' => '08.00', 'jaminan' => 'Umum / Mandiri', 'catatan' => 'Saya sakit perut',
                'foto' => null, 'status' => 'selesai', 'created_at' => '2026-06-14 04:59:10',
            ],
        ]);
    }
}
