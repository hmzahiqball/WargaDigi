<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dukungan fitur "Pindah Pemegang Akun":
 * - keluarga.nik_pemegang_akun: NIK anggota yang memegang akun login keluarga
 *   (default = kepala keluarga). Memisahkan konsep pemegang akun dari kepala keluarga.
 * - enum jenis_mutasi mutasi_warga ditambah 'pindah_akun'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            if (! Schema::hasColumn('keluarga', 'nik_pemegang_akun')) {
                $table->string('nik_pemegang_akun', 16)->nullable()->after('nik_kepala_keluarga');
            }
        });

        // Set default pemegang akun = kepala keluarga untuk data lama.
        DB::statement('UPDATE keluarga SET nik_pemegang_akun = nik_kepala_keluarga WHERE nik_pemegang_akun IS NULL');

        DB::statement("ALTER TABLE mutasi_warga MODIFY jenis_mutasi ENUM('tambah','edit','edit_kk','pindah_akun') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE mutasi_warga SET jenis_mutasi = 'edit' WHERE jenis_mutasi = 'pindah_akun'");
        DB::statement("ALTER TABLE mutasi_warga MODIFY jenis_mutasi ENUM('tambah','edit','edit_kk') NOT NULL");

        Schema::table('keluarga', function (Blueprint $table) {
            if (Schema::hasColumn('keluarga', 'nik_pemegang_akun')) {
                $table->dropColumn('nik_pemegang_akun');
            }
        });
    }
};
