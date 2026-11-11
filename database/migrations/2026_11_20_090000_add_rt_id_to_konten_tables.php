<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Konten dibedakan per tingkat: rt_id NULL = konten RW (tampil ke semua warga),
 * rt_id terisi = konten RT tersebut (hanya tampil ke warga RT itu).
 * Galeri dokumentasi mengikuti agenda induknya.
 */
return new class extends Migration
{
    private const TABEL = ['berita', 'agenda', 'pengumuman'];

    public function up(): void
    {
        foreach (self::TABEL as $tabel) {
            if (Schema::hasColumn($tabel, 'rt_id')) {
                continue;
            }
            Schema::table($tabel, function (Blueprint $table) {
                $table->foreignUuid('rt_id')->nullable()->after('operator_id')
                    ->constrained('master_rt')->nullOnDelete();
            });

            // Konten lama milik Operator Konten RT dipindahkan ke RT operatornya; selebihnya tetap konten RW.
            DB::statement("UPDATE {$tabel} k JOIN users u ON u.id = k.operator_id
                SET k.rt_id = u.rt_id
                WHERE u.rt_id IS NOT NULL AND u.role IN ('Op Konten RT', 'Op. Konten RT')");
        }
    }

    public function down(): void
    {
        foreach (self::TABEL as $tabel) {
            if (! Schema::hasColumn($tabel, 'rt_id')) {
                continue;
            }
            Schema::table($tabel, function (Blueprint $table) {
                $table->dropConstrainedForeignId('rt_id');
            });
        }
    }
};
