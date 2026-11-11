<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah saldo menjadi signed agar defisit (saldo negatif) dapat disimpan jujur.
        DB::statement('ALTER TABLE laporan_keuangan MODIFY saldo_awal BIGINT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE laporan_keuangan MODIFY saldo_akhir BIGINT NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE laporan_keuangan MODIFY saldo_awal BIGINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE laporan_keuangan MODIFY saldo_akhir BIGINT UNSIGNED NOT NULL DEFAULT 0');
    }
};
