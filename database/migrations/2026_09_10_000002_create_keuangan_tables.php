<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_keuangan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_transaksi')->unique();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->string('kategori');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('jumlah');
            $table->date('tanggal');
            $table->string('bukti_file')->nullable();
            
            $table->enum('status', ['Draft', 'Pending', 'Verified', 'Approved', 'Rejected'])->default('Draft');
            $table->text('catatan_verifikasi')->nullable();
            
            $table->enum('unit_sumber', ['RT', 'RW', 'DKM']);
            $table->foreignUuid('rt_id')->nullable()->constrained('master_rt')->nullOnDelete();
            
            $table->foreignUuid('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_verifikasi')->nullable();
            
            $table->timestamps();
        });

        Schema::create('laporan_keuangan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->tinyInteger('periode_bulan');
            $table->smallInteger('periode_tahun');
            $table->enum('unit', ['RT', 'RW', 'DKM']);
            $table->foreignUuid('rt_id')->nullable()->constrained('master_rt')->nullOnDelete();
            
            $table->unsignedBigInteger('total_pemasukan')->default(0);
            $table->unsignedBigInteger('total_pengeluaran')->default(0);
            $table->unsignedBigInteger('saldo_awal')->default(0);
            $table->unsignedBigInteger('saldo_akhir')->default(0);
            
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Rejected'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->string('file_pdf')->nullable();
            
            $table->foreignUuid('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_keuangan');
        Schema::dropIfExists('transaksi_keuangan');
    }
};
