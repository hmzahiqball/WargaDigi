<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduk')->cascadeOnDelete();
            $table->string('tipe_surat', 100); // e.g. 'Surat Keterangan Domisili (SKD)'
            $table->text('keterangan_tambahan')->nullable();
            $table->string('file_ktp')->nullable(); // path file scan KTP
            $table->string('file_kk')->nullable();  // path file scan KK
            $table->enum('status', [
                'Diajukan',
                'Ditolak RT',
                'Disetujui RT',
                'Ditolak RW',
                'Selesai',
            ])->default('Diajukan');
            $table->text('catatan_rt')->nullable();       // catatan persetujuan / penolakan RT
            $table->text('catatan_rw')->nullable();       // catatan persetujuan / penolakan RW
            $table->string('file_surat_resmi')->nullable(); // file PDF surat yang sudah disahkan
            $table->timestamp('tanggal_disetujui_rt')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
