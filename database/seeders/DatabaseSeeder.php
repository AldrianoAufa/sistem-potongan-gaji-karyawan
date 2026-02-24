<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jabatan
        DB::table('jabatan')->insert([
            ['id' => 1, 'nama_jabatan' => 'Staff Produksi',    'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 2, 'nama_jabatan' => 'Supervisor',         'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 3, 'nama_jabatan' => 'Kepala Bagian',      'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 4, 'nama_jabatan' => 'Manager',            'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 5, 'nama_jabatan' => 'Staff Administrasi', 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 6, 'nama_jabatan' => 'Operator',           'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 7, 'nama_jabatan' => 'Quality Control',    'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 8, 'nama_jabatan' => 'Staff HRD',          'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
        ]);

        // Karyawan (was 'anggota', now renamed with kode_karyawan)
        DB::table('karyawan')->insert([
            ['id' => 1, 'kode_karyawan' => 'C001', 'nama' => 'Ahmad Suryadi',  'jabatan_id' => 1, 'departemen_id' => null, 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 2, 'kode_karyawan' => 'C002', 'nama' => 'Budi Santoso',   'jabatan_id' => 2, 'departemen_id' => null, 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 3, 'kode_karyawan' => 'C003', 'nama' => 'Citra Dewi',     'jabatan_id' => 1, 'departemen_id' => null, 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 4, 'kode_karyawan' => 'C004', 'nama' => 'Dani Pratama',   'jabatan_id' => 3, 'departemen_id' => null, 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 5, 'kode_karyawan' => 'C005', 'nama' => 'Eka Fitriani',   'jabatan_id' => 5, 'departemen_id' => null, 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
        ]);

        // Jenis Potongan
        DB::table('jenis_potongan')->insert([
            ['id' => 1,  'kode_potongan' => 'KOPER',       'nama_potongan' => 'Koperasi',        'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 2,  'kode_potongan' => 'PINJ.PANJANG','nama_potongan' => 'Pinjaman Panjang', 'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 05:15:19'],
            ['id' => 3,  'kode_potongan' => 'PINJ.PENDEK', 'nama_potongan' => 'Pinjaman Pendek',  'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 05:15:09'],
            ['id' => 6,  'kode_potongan' => 'ARISAN',      'nama_potongan' => 'Arisan',           'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 04:21:03'],
            ['id' => 7,  'kode_potongan' => 'KELONTONG',   'nama_potongan' => 'Kelontong',        'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 05:15:54'],
            ['id' => 8,  'kode_potongan' => 'DONATUR',     'nama_potongan' => 'Donatur',          'created_at' => '2026-02-19 04:21:03', 'updated_at' => '2026-02-19 05:16:47'],
            ['id' => 9,  'kode_potongan' => 'S.WAJIB',     'nama_potongan' => 'Simpanan Wajib',  'created_at' => '2026-02-19 05:16:22', 'updated_at' => '2026-02-19 05:16:22'],
            ['id' => 10, 'kode_potongan' => 'SIM',         'nama_potongan' => 'sim',              'created_at' => '2026-02-19 05:17:34', 'updated_at' => '2026-02-19 05:17:34'],
            ['id' => 11, 'kode_potongan' => 'SHR',         'nama_potongan' => 'shr',              'created_at' => '2026-02-19 05:17:48', 'updated_at' => '2026-02-19 05:17:48'],
            ['id' => 12, 'kode_potongan' => 'SPD',         'nama_potongan' => 'spd',              'created_at' => '2026-02-19 05:18:02', 'updated_at' => '2026-02-19 05:18:02'],
            ['id' => 13, 'kode_potongan' => 'SHT',         'nama_potongan' => 'sht',              'created_at' => '2026-02-19 05:20:05', 'updated_at' => '2026-02-19 05:20:05'],
            ['id' => 14, 'kode_potongan' => 'MATERIAL',    'nama_potongan' => 'material',         'created_at' => '2026-02-19 05:21:21', 'updated_at' => '2026-02-19 05:21:21'],
        ]);

        // Users (karyawan_id replaces anggota_id)
        DB::table('users')->insert([
            ['id' => 1, 'username' => 'admin',        'password' => '$2y$12$.gjPRham9wfQKYPAtHsBVOBMFnoTgigeQ9u0YEt7FMrZ3MCE1GMVu', 'role' => 'admin', 'karyawan_id' => null, 'remember_token' => null, 'created_at' => '2026-02-19 04:21:04', 'updated_at' => '2026-02-19 04:21:04'],
            ['id' => 2, 'username' => 'ahmad.suryadi','password' => '$2y$12$B1wXf3tX9JT3eN7OwEowWu.Sxz/dvLsfrAkfB2gBOtKOmDhLzrlmG', 'role' => 'user',  'karyawan_id' => 1,    'remember_token' => null, 'created_at' => '2026-02-19 04:21:04', 'updated_at' => '2026-02-19 04:21:04'],
            ['id' => 3, 'username' => 'budi.santoso', 'password' => '$2y$12$0dRexD2W3xg4fgipyP8CNeNKrjP8tflUfPiGaoxhRom7SsCg5jEw.', 'role' => 'user',  'karyawan_id' => 2,    'remember_token' => null, 'created_at' => '2026-02-19 04:21:05', 'updated_at' => '2026-02-19 04:21:05'],
            ['id' => 4, 'username' => 'citra.dewi',   'password' => '$2y$12$QXlVKcEGajD1aO1WIID75OB/wJxybDK42T0iCYD21cDru7xrhUwEW', 'role' => 'user',  'karyawan_id' => 3,    'remember_token' => null, 'created_at' => '2026-02-19 04:21:05', 'updated_at' => '2026-02-19 04:21:05'],
            ['id' => 5, 'username' => 'dani.pratama', 'password' => '$2y$12$.5qYvRWCR7pXoZljTOHh4OyWsMFzpLLhjoC0T0wJyRpeQeHUHpaJW', 'role' => 'user',  'karyawan_id' => 4,    'remember_token' => null, 'created_at' => '2026-02-19 04:21:05', 'updated_at' => '2026-02-19 04:21:05'],
            ['id' => 6, 'username' => 'eka.fitriani', 'password' => '$2y$12$CM6sAbzh0FKqLKNNVMlYru6JlkD1E5hS7HNmyUPdWY5jO0AO.LtI6', 'role' => 'user',  'karyawan_id' => 5,    'remember_token' => null, 'created_at' => '2026-02-19 04:21:06', 'updated_at' => '2026-02-19 04:21:06'],
        ]);
    }
}
