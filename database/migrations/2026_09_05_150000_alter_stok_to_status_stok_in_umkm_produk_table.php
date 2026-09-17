<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('umkm_produk')) {
            if (!Schema::hasColumn('umkm_produk', 'status_stok')) {
                Schema::table('umkm_produk', function (Blueprint $table) {
                    $table->enum('status_stok', ['tersedia', 'menipis', 'habis'])->default('tersedia');
                });
            }

            if (Schema::hasColumn('umkm_produk', 'stok')) {
                DB::statement("UPDATE umkm_produk SET status_stok = CASE WHEN stok <= 0 THEN 'habis' WHEN stok <= 5 THEN 'menipis' ELSE 'tersedia' END WHERE stok IS NOT NULL");
                Schema::table('umkm_produk', function (Blueprint $table) {
                    $table->dropColumn('stok');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('umkm_produk')) {
            if (!Schema::hasColumn('umkm_produk', 'stok')) {
                Schema::table('umkm_produk', function (Blueprint $table) {
                    $table->integer('stok')->default(0);
                });
            }

            if (Schema::hasColumn('umkm_produk', 'status_stok')) {
                Schema::table('umkm_produk', function (Blueprint $table) {
                    $table->dropColumn('status_stok');
                });
            }
        }
    }
};
