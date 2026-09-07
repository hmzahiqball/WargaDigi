<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Transaksi Iuran — Setoran warga ke RT (tunai/transfer)
        Schema::create('transaksi_iuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keluarga_id')->constrained('keluarga')->restrictOnDelete();
            $table->foreignId('rt_id')->constrained('master_rt')->restrictOnDelete();
            $table->tinyInteger('periode_bulan'); // 1-12
            $table->smallInteger('periode_tahun');
            $table->decimal('jumlah', 15, 2);
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer']);
            $table->string('bukti_pembayaran')->nullable(); // Path file bukti transfer
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Pending', 'Terverifikasi', 'Ditolak'])->default('Pending');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('operator_id')->constrained('users');
            $table->timestamps();
        });

        // 2. Kas RT — Rekapitulasi keuangan RT per periode
        Schema::create('kas_rt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('master_rt')->restrictOnDelete();
            $table->tinyInteger('periode_bulan'); // 1-12
            $table->smallInteger('periode_tahun');
            $table->decimal('total_pemasukan', 15, 2)->default(0);
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status_laporan', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak'])->default('Draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('operator_id')->constrained('users');
            $table->timestamps();

            // Satu laporan per RT per periode
            $table->unique(['rt_id', 'periode_bulan', 'periode_tahun'], 'kas_rt_periode_unique');
        });

        // 3. Kas RW — Saldo RW dari setoran rekapitulasi Bendahara RT
        Schema::create('kas_rw', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_rt_id')->nullable()->constrained('kas_rt')->nullOnDelete();
            $table->enum('jenis', ['Pemasukan', 'Pengeluaran']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_berjalan', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_transaksi');
            $table->foreignId('operator_id')->constrained('users');
            $table->timestamps();
        });

        // 4. Donasi — Pengeluaran dana RW yang didistribusikan kepada warga
        Schema::create('donasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah_dana', 15, 2);
            $table->enum('jenis_penerima', ['Semua Warga', 'RT Tertentu', 'Warga Tertentu']);
            $table->foreignId('rt_id')->nullable()->constrained('master_rt')->nullOnDelete();
            $table->foreignId('keluarga_id')->nullable()->constrained('keluarga')->nullOnDelete();
            $table->date('tanggal_donasi');
            $table->enum('status', ['Direncanakan', 'Disalurkan', 'Selesai'])->default('Direncanakan');
            $table->foreignId('operator_id')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi');
        Schema::dropIfExists('kas_rw');
        Schema::dropIfExists('kas_rt');
        Schema::dropIfExists('transaksi_iuran');
    }
};
