<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mutasi_warga', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('keluarga_id');
            $table->foreign('keluarga_id')->references('id')->on('keluarga')->onDelete('cascade');
            $table->uuid('penduduk_id')->nullable()->comment('Null jika tambah anggota baru');
            $table->foreign('penduduk_id')->references('id')->on('penduduk')->onDelete('set null');
            $table->enum('jenis_mutasi', ['tambah', 'edit']);
            $table->json('data_pengajuan');
            $table->string('file_bukti')->nullable();
            $table->enum('status', ['menunggu_rt', 'menunggu_rw', 'disetujui', 'ditolak'])->default('menunggu_rt');
            $table->text('keterangan_tolak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_warga');
    }
};
