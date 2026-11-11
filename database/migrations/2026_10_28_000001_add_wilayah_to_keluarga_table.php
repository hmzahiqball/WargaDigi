<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah atribut wilayah administrasi & status hunian/warga pada tabel keluarga.
 *
 * - alamat_domisili: alamat tempat tinggal (bisa berbeda dari alamat KK).
 * - rw, kelurahan, kecamatan, kabupaten, provinsi, kode_pos: wilayah dari KK.
 * - pemilik_rumah: apakah keluarga pemilik rumah yang ditinggali.
 * - status_warga: 'tetap' (RW 21 & Tanimulya) atau 'tidak_tetap' (di luar itu).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->string('alamat_domisili')->nullable()->after('alamat');
            $table->string('rw', 10)->nullable()->after('alamat_domisili');
            $table->string('kelurahan', 100)->nullable()->after('rw');
            $table->string('kecamatan', 100)->nullable()->after('kelurahan');
            $table->string('kabupaten', 100)->nullable()->after('kecamatan');
            $table->string('provinsi', 100)->nullable()->after('kabupaten');
            $table->string('kode_pos', 10)->nullable()->after('provinsi');
            $table->boolean('pemilik_rumah')->default(false)->after('kode_pos');
            $table->enum('status_warga', ['tetap', 'tidak_tetap'])->default('tetap')->after('pemilik_rumah');
        });
    }

    public function down(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_domisili', 'rw', 'kelurahan', 'kecamatan',
                'kabupaten', 'provinsi', 'kode_pos', 'pemilik_rumah', 'status_warga',
            ]);
        });
    }
};
