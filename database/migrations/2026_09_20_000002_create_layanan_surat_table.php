<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_surat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('icon');
            $table->string('judul');
            $table->text('deskripsi');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_surat');
    }
};
