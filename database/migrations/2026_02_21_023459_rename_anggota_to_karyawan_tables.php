<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename table 'anggota' to 'karyawan' if it exists
        if (Schema::hasTable('anggota') && !Schema::hasTable('karyawan')) {
            Schema::rename('anggota', 'karyawan');
        }

        // 2. Rename column 'kode_anggota' to 'kode_karyawan' in 'karyawan' table
        if (Schema::hasTable('karyawan') && Schema::hasColumn('karyawan', 'kode_anggota')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->renameColumn('kode_anggota', 'kode_karyawan');
            });
        }

        // 3. Rename 'anggota_id' to 'karyawan_id' in 'users' table
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'anggota_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('anggota_id', 'karyawan_id');
            });
        }

        // 4. Rename 'anggota_id' to 'karyawan_id' in 'input_bulanan' table
        if (Schema::hasTable('input_bulanan') && Schema::hasColumn('input_bulanan', 'anggota_id')) {
            Schema::table('input_bulanan', function (Blueprint $table) {
                $table->renameColumn('anggota_id', 'karyawan_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('input_bulanan') && Schema::hasColumn('input_bulanan', 'karyawan_id')) {
            Schema::table('input_bulanan', function (Blueprint $table) {
                $table->renameColumn('karyawan_id', 'anggota_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'karyawan_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('karyawan_id', 'anggota_id');
            });
        }

        if (Schema::hasTable('karyawan') && Schema::hasColumn('karyawan', 'kode_karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->renameColumn('kode_karyawan', 'kode_anggota');
            });
        }

        if (Schema::hasTable('karyawan') && !Schema::hasTable('anggota')) {
            Schema::rename('karyawan', 'anggota');
        }
    }
};
