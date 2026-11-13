<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Pengumuman terbit tidak lagi dapat dihapus langsung oleh pembuatnya. Sebagai gantinya ada status
 * Archive (seperti berita) untuk mencabut pengumuman dari tampilan warga tanpa menghilangkan datanya.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pengumuman MODIFY status ENUM('Draft','Review','Revisi','Publish','Archive') NOT NULL DEFAULT 'Draft'");
    }

    public function down(): void
    {
        // Pengumuman berstatus Archive dikembalikan ke Publish sebelum kolom disempitkan.
        DB::table('pengumuman')->where('status', 'Archive')->update(['status' => 'Publish']);
        DB::statement("ALTER TABLE pengumuman MODIFY status ENUM('Draft','Review','Revisi','Publish') NOT NULL DEFAULT 'Draft'");
    }
};
