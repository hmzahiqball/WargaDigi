<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `pejabat` — penaut jabatan kepengurusan (Ketua RW; Ketua RT per-RT)
 * ke data penduduk. Menyimpan RIWAYAT lengkap: setiap pergantian membuat baris
 * baru; baris aktif ditandai is_active=true dan selesai_menjabat=null.
 *
 * Catatan desain: akun pengurus (users) tetap tanpa NIK dan login via username.
 * Identitas "siapa yang menjabat" dikelola terpisah di tabel ini agar jabatan
 * bisa berganti orang tanpa menyentuh akun, dan riwayatnya terlacak.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pejabat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('jabatan', ['ketua_rw', 'ketua_rt']);
            // RT tujuan untuk jabatan ketua_rt; null untuk ketua_rw.
            $table->foreignUuid('rt_id')->nullable()->constrained('master_rt')->nullOnDelete();
            // Penduduk yang menjabat (nik mengacu ke penduduk.nik yang unik).
            $table->string('penduduk_nik', 16);
            $table->date('mulai_menjabat')->nullable();
            $table->date('selesai_menjabat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('penduduk_nik')->references('nik')->on('penduduk')->cascadeOnDelete();
            $table->index(['jabatan', 'rt_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pejabat');
    }
};
