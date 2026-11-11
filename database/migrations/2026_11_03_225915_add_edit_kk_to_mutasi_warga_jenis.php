<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Perluas enum jenis_mutasi mutasi_warga agar mendukung 'edit_kk'
 * (pengajuan perubahan data Kartu Keluarga / identitas keluarga oleh warga).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE mutasi_warga MODIFY jenis_mutasi ENUM('tambah','edit','edit_kk') NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan edit_kk menjadi edit sebelum menyempitkan enum agar tidak error.
        DB::statement("UPDATE mutasi_warga SET jenis_mutasi = 'edit' WHERE jenis_mutasi = 'edit_kk'");
        DB::statement("ALTER TABLE mutasi_warga MODIFY jenis_mutasi ENUM('tambah','edit') NOT NULL");
    }
};
