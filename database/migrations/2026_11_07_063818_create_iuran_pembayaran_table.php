<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Baris dibuat hanya saat ada aktivitas pembayaran (lazy), bukan di awal.
        Schema::create('iuran_pembayaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('master_iuran_id');
            $table->uuid('keluarga_id');
            $table->unsignedTinyInteger('bulan'); // 1..12
            // menunggu = diajukan warga, belum diverifikasi; lunas = sah, masuk kas.
            $table->enum('status', ['menunggu', 'lunas'])->default('lunas');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('metode', ['tunai', 'transfer'])->nullable();
            $table->string('bukti_file')->nullable();
            // Sumber pencatatan: bendahara (ceklis) atau warga (pengajuan).
            $table->enum('sumber', ['bendahara', 'warga'])->default('bendahara');
            $table->uuid('transaksi_id')->nullable(); // tautan ke transaksi kas
            $table->uuid('dicatat_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('master_iuran_id')->references('id')->on('master_iuran')->cascadeOnDelete();
            $table->foreign('keluarga_id')->references('id')->on('keluarga')->cascadeOnDelete();
            $table->foreign('transaksi_id')->references('id')->on('transaksi_keuangan')->nullOnDelete();
            $table->foreign('dicatat_oleh')->references('id')->on('users')->nullOnDelete();
            // Satu KK hanya satu baris per bulan per master.
            $table->unique(['master_iuran_id', 'keluarga_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_pembayaran');
    }
};
