<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nomor urut surat yang diinput manual oleh pengurus saat persetujuan.
 * Hanya bagian urut (mis. "012"); sisanya (SK/RT/RW/bulan romawi/tahun)
 * dirangkai otomatis pada template surat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            if (! Schema::hasColumn('pengajuan_surat', 'nomor_urut')) {
                $table->string('nomor_urut', 30)->nullable()->after('tipe_surat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_surat', 'nomor_urut')) {
                $table->dropColumn('nomor_urut');
            }
        });
    }
};
