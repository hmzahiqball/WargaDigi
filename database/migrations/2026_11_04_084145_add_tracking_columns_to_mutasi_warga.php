<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom tracking untuk audit pengajuan warga (MutasiWarga):
 * - data_lama: snapshot nilai sebelum perubahan (untuk diff lama → baru) pada edit/edit_kk.
 * - diajukan_oleh / diajukan_at: pemohon & waktu pengajuan.
 * - disetujui_rt_oleh / disetujui_rt_at: approver RT & waktu.
 * - diproses_rw_oleh / diproses_rw_at: approver/penolak final RW & waktu.
 * - ditolak_oleh: user yang menolak (RT atau RW).
 * Semua FK user nullable (data lama tidak punya jejak).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutasi_warga', function (Blueprint $table) {
            if (! Schema::hasColumn('mutasi_warga', 'data_lama')) {
                $table->json('data_lama')->nullable()->after('data_pengajuan');
            }
            if (! Schema::hasColumn('mutasi_warga', 'diajukan_oleh')) {
                $table->uuid('diajukan_oleh')->nullable()->after('keterangan_tolak');
            }
            if (! Schema::hasColumn('mutasi_warga', 'diajukan_at')) {
                $table->timestamp('diajukan_at')->nullable()->after('diajukan_oleh');
            }
            if (! Schema::hasColumn('mutasi_warga', 'disetujui_rt_oleh')) {
                $table->uuid('disetujui_rt_oleh')->nullable()->after('diajukan_at');
            }
            if (! Schema::hasColumn('mutasi_warga', 'disetujui_rt_at')) {
                $table->timestamp('disetujui_rt_at')->nullable()->after('disetujui_rt_oleh');
            }
            if (! Schema::hasColumn('mutasi_warga', 'diproses_rw_oleh')) {
                $table->uuid('diproses_rw_oleh')->nullable()->after('disetujui_rt_at');
            }
            if (! Schema::hasColumn('mutasi_warga', 'diproses_rw_at')) {
                $table->timestamp('diproses_rw_at')->nullable()->after('diproses_rw_oleh');
            }
            if (! Schema::hasColumn('mutasi_warga', 'ditolak_oleh')) {
                $table->uuid('ditolak_oleh')->nullable()->after('diproses_rw_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mutasi_warga', function (Blueprint $table) {
            foreach ([
                'data_lama', 'diajukan_oleh', 'diajukan_at', 'disetujui_rt_oleh',
                'disetujui_rt_at', 'diproses_rw_oleh', 'diproses_rw_at', 'ditolak_oleh',
            ] as $col) {
                if (Schema::hasColumn('mutasi_warga', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
