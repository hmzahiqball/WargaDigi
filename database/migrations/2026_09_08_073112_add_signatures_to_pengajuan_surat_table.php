<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->string('ttd_rt')->nullable()->after('catatan_rt');
            $table->string('stempel_rt')->nullable()->after('ttd_rt');
        });

        // Tambah kolom RW jika belum ada
        if (!Schema::hasColumn('pengajuan_surat', 'ttd_rw')) {
            Schema::table('pengajuan_surat', function (Blueprint $table) {
                $table->string('ttd_rw')->nullable()->after('catatan_rw');
            });
        }
        if (!Schema::hasColumn('pengajuan_surat', 'stempel_rw')) {
            Schema::table('pengajuan_surat', function (Blueprint $table) {
                $table->string('stempel_rw')->nullable()->after('file_surat_resmi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropColumn(['ttd_rt', 'stempel_rt']);
        });
    }
};
