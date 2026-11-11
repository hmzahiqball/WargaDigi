<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tambah status 'Nonaktif' pada enum users.status_akun.
 *
 * Status ini dipakai saat pemegang akun lama dinonaktifkan melalui fitur
 * "Pindah Pemegang Akun" — akun tidak dihapus, hanya tidak dapat login lagi.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY status_akun ENUM('Unverified','Active','Nonaktif') NOT NULL DEFAULT 'Unverified'");
    }

    public function down(): void
    {
        // Kembalikan akun Nonaktif ke Unverified agar aman saat enum diciutkan.
        DB::statement("UPDATE users SET status_akun = 'Unverified' WHERE status_akun = 'Nonaktif'");
        DB::statement("ALTER TABLE users MODIFY status_akun ENUM('Unverified','Active') NOT NULL DEFAULT 'Unverified'");
    }
};
