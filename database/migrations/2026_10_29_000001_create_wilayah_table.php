<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel referensi wilayah administratif Indonesia (Kepmendagri, dataset
 * cahyadsn/wilayah). Satu tabel dengan kode berformat titik:
 *   '32' (provinsi), '32.04' (kabupaten/kota), '32.04.13' (kecamatan),
 *   '32.04.13.2001' (kelurahan/desa). Level ditentukan dari jumlah segmen.
 *
 * Dipakai untuk menurunkan Provinsi/Kabupaten/Kecamatan dari 6 digit awal
 * NIK/No.KK dan menyediakan daftar kelurahan per kecamatan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->string('kode', 13)->primary();
            $table->string('nama', 100);
            $table->index('nama', 'wilayah_nama_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
