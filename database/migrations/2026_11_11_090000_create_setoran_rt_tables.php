<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nominal setoran per KK yang lunas, per tahun. rt_id null = default untuk semua RT;
        // baris dengan rt_id mengesampingkan default untuk RT tersebut.
        Schema::create('setoran_pengaturan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('tahun');
            $table->uuid('rt_id')->nullable();
            $table->unsignedBigInteger('nominal_per_kk')->default(0);
            $table->timestamps();

            $table->unique(['tahun', 'rt_id']);
            $table->foreign('rt_id')->references('id')->on('master_rt')->cascadeOnDelete();
        });

        // Setoran kolektif RT ke RW. Dicatat RT, dikonfirmasi RW; saat dikonfirmasi
        // dibuat dua transaksi kas (pengeluaran RT dan pemasukan RW).
        Schema::create('setoran_rt', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rt_id');
            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');
            $table->unsignedBigInteger('nominal');
            $table->date('tanggal_setor');
            $table->enum('metode', ['tunai', 'transfer'])->default('tunai');
            $table->string('bukti_file')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->text('alasan')->nullable();
            $table->uuid('dibuat_oleh')->nullable();
            $table->uuid('dikonfirmasi_oleh')->nullable();
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->uuid('transaksi_rt_id')->nullable();
            $table->uuid('transaksi_rw_id')->nullable();
            $table->timestamps();

            $table->index(['rt_id', 'periode_tahun', 'periode_bulan']);
            $table->index('status');
            $table->foreign('rt_id')->references('id')->on('master_rt')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_rt');
        Schema::dropIfExists('setoran_pengaturan');
    }
};