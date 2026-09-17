<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekening_bendahara', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('unit', ['RT', 'RW', 'DKM']);
            $table->foreignUuid('rt_id')->nullable()->constrained('master_rt')->nullOnDelete();
            $table->string('bank');
            $table->string('no_rek');
            $table->string('nama_rek');
            $table->string('qris_file')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tagihan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['rutin', 'insidental']);
            $table->unsignedBigInteger('nominal');
            $table->tinyInteger('periode_bulan');
            $table->smallInteger('periode_tahun');
            $table->enum('unit', ['RT', 'RW', 'DKM']);
            $table->foreignUuid('rt_id')->nullable()->constrained('master_rt')->nullOnDelete();
            $table->foreignUuid('pembuat_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Active', 'Closed'])->default('Active');
            $table->date('tenggat')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran_tagihan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->foreignUuid('keluarga_id')->constrained('keluarga')->cascadeOnDelete();
            $table->foreignUuid('warga_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Unpaid', 'Pending', 'Paid'])->default('Unpaid');
            $table->enum('metode', ['Transfer', 'QRIS', 'Cash'])->nullable();
            $table->string('bukti_file')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tagihan');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('rekening_bendahara');
    }
};
