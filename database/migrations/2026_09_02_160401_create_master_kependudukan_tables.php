<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Data RT
        Schema::create('master_rt', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rt', 10)->unique();
            $table->string('nama_rt', 50);
            $table->timestamps();
        });

        // 2. Data Profil Keluarga (KK)[cite: 1]
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 16)->unique();
            $table->string('nik_kepala_keluarga', 16);
            $table->text('alamat');
            $table->foreignId('rt_id')->constrained('master_rt')->restrictOnDelete();
            $table->string('no_wa', 15)->nullable();
            $table->enum('status_aktivasi', ['Unverified', 'Active'])->default('Unverified');
            $table->timestamps();
        });

        // 3. Data Kependudukan (Warga)[cite: 1]
        Schema::create('penduduk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keluarga_id')->constrained('keluarga')->cascadeOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('agama', 20);
            $table->string('pekerjaan', 50);
            $table->string('status_hubungan_keluarga', 50);
            $table->string('status_perkawinan', 20);
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penduduk');
        Schema::dropIfExists('keluarga');
        Schema::dropIfExists('master_rt');
    }
};