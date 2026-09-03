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
            $table->enum('status', ['Draft', 'Review', 'Approve', 'Publish', 'Archive', 'Delete'])->default('Draft');
            $table->foreignUuid('operator_id')->constrained('users');
            $table->foreignUuid('approval_id')->nullable()->constrained('users');
            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};