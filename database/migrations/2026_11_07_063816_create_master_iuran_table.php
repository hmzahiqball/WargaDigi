<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_iuran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rt_id');
            $table->unsignedSmallInteger('tahun');
            $table->string('nama')->default('Iuran Bulanan Warga');
            $table->unsignedInteger('nominal'); // nominal per bulan per KK
            $table->uuid('kategori_id')->nullable(); // kategori transaksi (pemasukan)
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->foreign('rt_id')->references('id')->on('master_rt')->cascadeOnDelete();
            $table->foreign('kategori_id')->references('id')->on('kategori_transaksi')->nullOnDelete();
            // Satu program iuran pokok per RT per tahun.
            $table->unique(['rt_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_iuran');
    }
};
