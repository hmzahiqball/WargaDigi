<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri_dokumentasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agenda_id')->constrained('agenda')->cascadeOnDelete();
            $table->text('deskripsi')->nullable();
            $table->json('foto')->nullable(); // Array of file paths, max 10
            $table->foreignUuid('operator_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri_dokumentasi');
    }
};
