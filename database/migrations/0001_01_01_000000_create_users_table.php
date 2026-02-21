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
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->timestamps();
        });

        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('kode_anggota')->unique();
            $table->string('nama');
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->foreignId('anggota_id')->nullable()->constrained('anggota')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('jenis_potongan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_potongan')->unique();
            $table->string('nama_potongan');
            $table->timestamps();
        });

        Schema::create('input_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->foreignId('jenis_potongan_id')->constrained('jenis_potongan')->onDelete('restrict');
            $table->tinyInteger('bulan'); // 1-12
            $table->year('tahun');
            $table->decimal('jumlah_potongan', 15, 2)->default(0);
            $table->json('data_rinci')->nullable(); // JSON: PINJ, AWAL, BULN, KALI, PKOK, RPBG, SALD
            $table->timestamps();

            $table->index(['anggota_id', 'bulan', 'tahun']);
            $table->index(['jenis_potongan_id', 'bulan', 'tahun']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('input_bulanan');
        Schema::dropIfExists('jenis_potongan');
        Schema::dropIfExists('users');
        Schema::dropIfExists('anggota');
        Schema::dropIfExists('jabatan');
    }
};
