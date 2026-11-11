<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            // Arah transaksi yang diizinkan untuk kategori ini.
            $table->enum('tipe', ['pemasukan', 'pengeluaran', 'keduanya'])->default('keduanya');
            // Lingkup unit. NULL = kategori umum (berlaku semua unit), dikelola Admin.
            $table->enum('unit', ['RT', 'RW', 'DKM'])->nullable();
            // RT spesifik (hanya relevan bila unit = RT dan dibuat operator RT tertentu).
            $table->uuid('rt_id')->nullable();
            $table->boolean('aktif')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->foreign('rt_id')->references('id')->on('master_rt')->nullOnDelete();
            $table->index(['unit', 'tipe', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_transaksi');
    }
};
