<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iuran_pembayaran', function (Blueprint $table) {
            // Menandai sel yang dibuat dalam satu pembayaran batch (berbagi 1 transaksi kas).
            $table->uuid('batch_id')->nullable()->after('transaksi_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('iuran_pembayaran', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
