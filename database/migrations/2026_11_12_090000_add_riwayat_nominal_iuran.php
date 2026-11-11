<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat nominal iuran bulanan: nominal berlaku mulai bulan tertentu (tanpa menimpa yang lama).
        Schema::create('master_iuran_riwayat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('master_iuran_id');
            $table->unsignedInteger('nominal');
            $table->unsignedTinyInteger('berlaku_mulai_bulan'); // 1..12
            $table->uuid('dibuat_oleh')->nullable();
            $table->timestamps();

            $table->unique(['master_iuran_id', 'berlaku_mulai_bulan'], 'miriwayat_master_bulan_unique');
            $table->foreign('master_iuran_id')->references('id')->on('master_iuran')->cascadeOnDelete();
        });

        // Nominal yang sudah ada dijadikan nominal awal yang berlaku sejak Januari.
        foreach (DB::table('master_iuran')->get(['id', 'nominal']) as $m) {
            DB::table('master_iuran_riwayat')->insert([
                'id' => (string) Str::uuid(), 'master_iuran_id' => $m->id, 'nominal' => $m->nominal,
                'berlaku_mulai_bulan' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Setoran per KK: tiap perubahan menjadi baris baru yang berlaku mulai bulan tertentu.
        // nominal_per_kk null pada baris RT = "ikut nominal untuk semua RT" sejak bulan itu.
        Schema::table('setoran_pengaturan', function (Blueprint $table) {
            $table->unsignedTinyInteger('berlaku_mulai_bulan')->default(1)->after('rt_id');
        });
        Schema::table('setoran_pengaturan', function (Blueprint $table) {
            $table->dropUnique(['tahun', 'rt_id']);
        });
        DB::statement('ALTER TABLE setoran_pengaturan MODIFY nominal_per_kk BIGINT UNSIGNED NULL');
        Schema::table('setoran_pengaturan', function (Blueprint $table) {
            $table->unique(['tahun', 'rt_id', 'berlaku_mulai_bulan'], 'setoran_pengaturan_berlaku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('setoran_pengaturan', function (Blueprint $table) {
            $table->dropUnique('setoran_pengaturan_berlaku_unique');
        });
        // Simpan hanya baris paling awal (Januari/terawal) per tahun dan RT agar unique lama dapat dipasang.
        $baris = DB::table('setoran_pengaturan')->orderBy('berlaku_mulai_bulan')->get();
        $dipakai = [];
        foreach ($baris as $b) {
            $kunci = $b->tahun . '|' . ($b->rt_id ?? '');
            if (isset($dipakai[$kunci]) || $b->nominal_per_kk === null) {
                DB::table('setoran_pengaturan')->where('id', $b->id)->delete();
            } else {
                $dipakai[$kunci] = true;
            }
        }
        DB::statement('ALTER TABLE setoran_pengaturan MODIFY nominal_per_kk BIGINT UNSIGNED NOT NULL DEFAULT 0');
        Schema::table('setoran_pengaturan', function (Blueprint $table) {
            $table->dropColumn('berlaku_mulai_bulan');
            $table->unique(['tahun', 'rt_id']);
        });
        Schema::dropIfExists('master_iuran_riwayat');
    }
};