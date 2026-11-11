<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah no_wa (Nomor WhatsApp) per penduduk. Opsional, kecuali untuk
 * penduduk pemegang akun keluarga (divalidasi di controller).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (! Schema::hasColumn('penduduk', 'no_wa')) {
                $table->string('no_wa', 20)->nullable()->after('nik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (Schema::hasColumn('penduduk', 'no_wa')) {
                $table->dropColumn('no_wa');
            }
        });
    }
};
