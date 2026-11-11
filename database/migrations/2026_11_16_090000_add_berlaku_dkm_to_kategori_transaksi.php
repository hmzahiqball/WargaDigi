<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori milik RW yang juga berlaku di DKM ("RW dan DKM") tanpa menggandakan baris.
        Schema::table('kategori_transaksi', function (Blueprint $table) {
            $table->boolean('berlaku_dkm')->default(false)->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_transaksi', function (Blueprint $table) {
            $table->dropColumn('berlaku_dkm');
        });
    }
};