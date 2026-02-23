<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\karyawan;
use App\Models\JenisPotongan;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jabatan
        $jabatanList = [
            'Staff Produksi',
            'Supervisor',
            'Kepala Bagian',
            'Manager',
            'Staff Administrasi',
            'Operator',
            'Quality Control',
            'Staff HRD',
        ];

        foreach ($jabatanList as $nama) {
            Jabatan::create(['nama_jabatan' => $nama]);
        }

        // Jenis Potongan
        $potonganList = [
            ['kode_potongan' => 'KOPER', 'nama_potongan' => 'Koperasi'],
            ['kode_potongan' => 'PINJ.P', 'nama_potongan' => 'Pinjaman Panjang'],
            ['kode_potongan' => 'PINJ.D', 'nama_potongan' => 'Pinjaman Pendek'],
            ['kode_potongan' => 'BPJS.K', 'nama_potongan' => 'BPJS Kesehatan'],
            ['kode_potongan' => 'BPJS.T', 'nama_potongan' => 'BPJS Ketenagakerjaan'],
            ['kode_potongan' => 'ARISAN', 'nama_potongan' => 'Arisan'],
            ['kode_potongan' => 'TOKO', 'nama_potongan' => 'Toko/Kantin'],
            ['kode_potongan' => 'LAIN', 'nama_potongan' => 'Lain-lain'],
        ];

        foreach ($potonganList as $potongan) {
            JenisPotongan::create($potongan);
        }

        // karyawan sample
        $karyawanList = [
            ['kode_karyawan' => 'C001', 'nama' => 'Ahmad Suryadi', 'jabatan_id' => 1],
            ['kode_karyawan' => 'C002', 'nama' => 'Budi Santoso', 'jabatan_id' => 2],
            ['kode_karyawan' => 'C003', 'nama' => 'Citra Dewi', 'jabatan_id' => 1],
            ['kode_karyawan' => 'C004', 'nama' => 'Dani Pratama', 'jabatan_id' => 3],
            ['kode_karyawan' => 'C005', 'nama' => 'Eka Fitriani', 'jabatan_id' => 5],
        ];

        foreach ($karyawanList as $karyawan) {
            karyawan::create($karyawan);
        }

        // Admin user
        User::create([
            'username' => 'admin',
            'password' => 'password',
            'role' => 'admin',
            'karyawan_id' => null,
        ]);

        // User accounts for sample karyawan
        $karyawanAll = karyawan::all();
        foreach ($karyawanAll as $karyawan) {
            User::create([
                'username' => strtolower(str_replace(' ', '.', $karyawan->nama)),
                'password' => 'password',
                'role' => 'user',
                'karyawan_id' => $karyawan->id,
            ]);
        }

        // Sample input_bulanan data
        $this->seedInputBulanan();
    }

    private function seedInputBulanan(): void
    {
        $karyawanIds = karyawan::pluck('id')->toArray();
        $jenisPotonganIds = JenisPotongan::pluck('id', 'kode_potongan')->toArray();

        foreach ($karyawanIds as $karyawanId) {
            // Koperasi for all members
            \App\Models\InputBulanan::create([
                'karyawan_id' => $karyawanId,
                'jenis_potongan_id' => $jenisPotonganIds['KOPER'],
                'bulan' => 1,
                'tahun' => 2026,
                'jumlah_potongan' => 500000,
                'data_rinci' => null,
            ]);

            \App\Models\InputBulanan::create([
                'karyawan_id' => $karyawanId,
                'jenis_potongan_id' => $jenisPotonganIds['BPJS.K'],
                'bulan' => 1,
                'tahun' => 2026,
                'jumlah_potongan' => 200000,
                'data_rinci' => null,
            ]);
        }

        // Pinjaman sample for first karyawan
        \App\Models\InputBulanan::create([
            'karyawan_id' => $karyawanIds[0],
            'jenis_potongan_id' => $jenisPotonganIds['PINJ.P'],
            'bulan' => 1,
            'tahun' => 2026,
            'jumlah_potongan' => 1000000,
            'data_rinci' => [
                'PINJ' => 12000000,
                'AWAL' => 8000000,
                'BULN' => 5,
                'KALI' => 12,
                'PKOK' => 1000000,
                'RPBG' => 0,
                'SALD' => 7000000,
            ],
        ]);
    }
}
