<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Topik Pusat Bantuan: satu baris per sub-bab Manual Book atau per pertanyaan FAQ.
 * Isi tabel dibangkitkan dari database/data/panduan/panduan.json (lihat config/panduan.php),
 * jadi aman dikosongkan dan dimuat ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panduan_topik', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('peran', 40)->index();
            $table->string('buku', 2);
            $table->string('bab_nomor', 10)->nullable();
            $table->string('bab_judul', 150);
            $table->string('nomor', 12)->nullable();
            $table->string('judul', 255);
            $table->string('slug', 160)->unique();
            $table->string('jenis', 10)->default('topik'); // topik | faq
            $table->text('ringkasan')->nullable();
            $table->longText('isi_html');
            $table->longText('isi_teks');
            $table->text('kata_kunci')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->string('versi', 40)->index();
            $table->timestamps();

            $table->index(['peran', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panduan_topik');
    }
};
