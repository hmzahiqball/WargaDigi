<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data Berita[cite: 1]
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul_berita');
            $table->string('slug')->unique();
            $table->string('kategori');
            $table->text('isi_berita');
            $table->string('featured_image')->nullable();
            // Siklus Status Berita[cite: 1]
            $table->enum('status', ['Draft', 'Review', 'Approve', 'Publish', 'Archive', 'Delete'])->default('Draft');
            $table->foreignId('operator_id')->constrained('users'); // Relasi ke tabel users standar Laravel
            $table->foreignId('approval_id')->nullable()->constrained('users');
            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};