<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kategori buatan RW dapat berlaku di RW, RT, dan/atau DKM (kombinasi bebas) lewat tiga penanda.
     * Kategori berunit DKM lama dipindah menjadi kategori RW yang hanya berlaku di DKM.
     */
    public function up(): void
    {
        Schema::table('kategori_transaksi', function (Blueprint $table) {
            $table->boolean('berlaku_rw')->default(true)->after('unit');
            $table->boolean('berlaku_rt')->default(false)->after('berlaku_rw');
        });

        DB::table('kategori_transaksi')->where('unit', 'DKM')->update([
            'unit' => 'RW', 'berlaku_rw' => false, 'berlaku_dkm' => true,
        ]);
    }

    public function down(): void
    {
        DB::table('kategori_transaksi')->where('unit', 'RW')->where('berlaku_rw', false)->where('berlaku_dkm', true)
            ->where('berlaku_rt', false)->update(['unit' => 'DKM', 'berlaku_dkm' => false]);

        Schema::table('kategori_transaksi', function (Blueprint $table) {
            $table->dropColumn(['berlaku_rw', 'berlaku_rt']);
        });
    }
};