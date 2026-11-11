<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Tambah status "ditolak" agar alasan penolakan tetap terlihat oleh warga. */
    public function up(): void
    {
        DB::statement("ALTER TABLE iuran_pembayaran MODIFY status ENUM('menunggu','lunas','ditolak') NOT NULL DEFAULT 'lunas'");
    }

    public function down(): void
    {
        DB::table('iuran_pembayaran')->where('status', 'ditolak')->delete();
        DB::statement("ALTER TABLE iuran_pembayaran MODIFY status ENUM('menunggu','lunas') NOT NULL DEFAULT 'lunas'");
    }
};
