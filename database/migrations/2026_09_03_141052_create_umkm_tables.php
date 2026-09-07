<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_umkm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kategori');
            $table->timestamps();
        });

        Schema::create('umkm_usaha', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik', 16);
            $table->foreign('nik')->references('nik')->on('users')->cascadeOnDelete();
            $table->foreignUuid('kategori_umkm_id')->constrained('kategori_umkm')->cascadeOnDelete();
            $table->string('nama_usaha', 100);
            $table->text('deskripsi')->nullable();
            $table->string('alamat_usaha');
            $table->string('no_wa', 15);
            $table->string('foto_usaha')->nullable();
            $table->enum('status_verifikasi', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->integer('klikWA')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('umkm_usaha_id')->constrained('umkm_usaha')->cascadeOnDelete();
            $table->string('nama_kategori');
            $table->timestamps();
        });

        Schema::create('umkm_produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('umkm_usaha_id')->constrained('umkm_usaha')->cascadeOnDelete();
            $table->foreignUuid('kategori_produk_id')->constrained('kategori_produk')->cascadeOnDelete();
            $table->string('nama_produk', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2)->default(0);
            $table->enum('status_stok', ['tersedia', 'menipis', 'habis'])->default('tersedia');
            $table->string('foto_produk')->nullable();
            $table->enum('status_produk', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->unsignedBigInteger('jumlah_akses')->default(0);
            $table->string('link_wa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_produk');
        Schema::dropIfExists('kategori_produk');
        Schema::dropIfExists('umkm_usaha');
        Schema::dropIfExists('kategori_umkm');
    }
};