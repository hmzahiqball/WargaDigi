<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_usaha', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik', 16);
            $table->foreign('nik')->references('nik')->on('users')->cascadeOnDelete();
            $table->string('nama_usaha', 100);
            $table->enum('kategori', ['Kuliner', 'Kriya', 'Jasa', 'Fashion', 'Lainnya']);
            $table->text('deskripsi')->nullable();
            $table->string('alamat_usaha');
            $table->string('no_wa', 15);
            $table->string('foto_usaha')->nullable();
            $table->enum('status_verifikasi', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
        });

        Schema::create('umkm_produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('umkm_usaha_id')->constrained('umkm_usaha')->cascadeOnDelete();
            $table->string('nama_produk', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->string('foto_produk')->nullable();
            $table->enum('status_produk', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->boolean('is_tersedia')->default(true);
            $table->string('link_wa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_produk');
        Schema::dropIfExists('umkm_usaha');
    }
};