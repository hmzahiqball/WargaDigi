<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('galeri_dokumentasi', function (Blueprint $table) {
            $table->integer('jumlah_peserta')->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('galeri_dokumentasi', function (Blueprint $table) {
            $table->dropColumn('jumlah_peserta');
        });
    }
};
