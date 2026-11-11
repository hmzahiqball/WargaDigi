<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah alamat_domisili (tempat tinggal) per penduduk.
 * Dapat diisi manual, dari OCR, atau disamakan dengan alamat domisili KK.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (! Schema::hasColumn('penduduk', 'alamat_domisili')) {
                $table->string('alamat_domisili', 255)->nullable()->after('alamat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (Schema::hasColumn('penduduk', 'alamat_domisili')) {
                $table->dropColumn('alamat_domisili');
            }
        });
    }
};
