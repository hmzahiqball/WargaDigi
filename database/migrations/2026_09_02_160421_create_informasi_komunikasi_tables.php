<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul_berita');
            $table->string('slug')->unique();
            $table->string('kategori');
            $table->text('isi_berita');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['Draft', 'Review', 'Revisi', 'Publish', 'Archive'])->default('Draft');
            $table->text('catatan_revisi')->nullable();
            $table->foreignUuid('operator_id')->constrained('users');
            $table->foreignUuid('approval_id')->nullable()->constrained('users');
            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamps();
        });

        Schema::create('pengumuman', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul_pengumuman');
            $table->text('isi_pengumuman');
            $table->boolean('is_priority')->default(false);
            $table->enum('status', ['Draft', 'Review', 'Revisi', 'Publish'])->default('Draft');
            $table->text('catatan_revisi')->nullable();
            $table->foreignUuid('operator_id')->constrained('users');
            $table->foreignUuid('approval_id')->nullable()->constrained('users');
            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamps();
        });

        Schema::create('agenda', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul_agenda');
            $table->string('kategori');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->string('lokasi');
            $table->string('link_gmaps')->nullable();
            $table->text('detail_pengumuman');
            $table->string('banner_flyer')->nullable();
            $table->boolean('is_rsvp_enabled')->default(false);
            $table->json('foto_dokumentasi')->nullable();
            $table->enum('status', ['Draft', 'Review', 'Revisi', 'Publish'])->default('Draft');
            $table->text('catatan_revisi')->nullable();
            $table->foreignUuid('operator_id')->constrained('users');
            $table->foreignUuid('approval_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('berita');
    }
};