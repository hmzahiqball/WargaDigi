<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_keuangan', function (Blueprint $table) {
            // FK ke master kategori. Kolom string `kategori` tetap dipertahankan
            // sebagai snapshot nama agar rekap tidak rusak bila kategori diubah.
            $table->uuid('kategori_id')->nullable()->after('kategori');
            $table->foreign('kategori_id')->references('id')->on('kategori_transaksi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_keuangan', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });
    }
};
