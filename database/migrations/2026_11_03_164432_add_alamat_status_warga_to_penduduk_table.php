<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah alamat (sesuai KTP) dan status_warga per penduduk.
 * - alamat: alamat domisili penduduk sesuai KTP (nullable).
 * - status_warga: 'tetap' bila Kelurahan Tanimulya & RW 21, selain itu 'tidak_tetap'.
 *   Dapat dikoreksi manual lewat dropdown pada form.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (! Schema::hasColumn('penduduk', 'alamat')) {
                $table->string('alamat', 255)->nullable()->after('nama_lengkap');
            }
            if (! Schema::hasColumn('penduduk', 'status_warga')) {
                $table->enum('status_warga', ['tetap', 'tidak_tetap'])
                    ->default('tidak_tetap')
                    ->after('kewarganegaraan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (Schema::hasColumn('penduduk', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('penduduk', 'status_warga')) {
                $table->dropColumn('status_warga');
            }
        });
    }
};
