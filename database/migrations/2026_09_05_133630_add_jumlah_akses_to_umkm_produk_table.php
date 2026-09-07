<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('umkm_produk')) {
            Schema::table('umkm_produk', function (Blueprint $table) {
                if (!Schema::hasColumn('umkm_produk', 'jumlah_akses')) {
                    $table->unsignedBigInteger('jumlah_akses')->default(0)->after('status_produk');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('umkm_produk')) {
            Schema::table('umkm_produk', function (Blueprint $table) {
                if (Schema::hasColumn('umkm_produk', 'jumlah_akses')) {
                    $table->dropColumn('jumlah_akses');
                }
            });
        }
    }
};
