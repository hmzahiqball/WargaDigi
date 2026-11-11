<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Iuran Lainnya buatan RW dapat dikumpulkan lewat RT: warga bayar ke RT, RT menyetor kolektif ke RW.
        Schema::table('tagihan', function (Blueprint $table) {
            $table->boolean('via_rt')->default(false)->after('nominal_fix');
        });

        // Setoran kolektif RT untuk satu Iuran Lainnya (null = setoran iuran bulanan).
        Schema::table('setoran_rt', function (Blueprint $table) {
            $table->uuid('tagihan_id')->nullable()->after('rt_id');
            $table->index('tagihan_id');
        });
    }

    public function down(): void
    {
        Schema::table('setoran_rt', function (Blueprint $table) {
            $table->dropIndex(['tagihan_id']);
            $table->dropColumn('tagihan_id');
        });
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('via_rt');
        });
    }
};