<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->string('pendidikan_terakhir')->nullable()->after('pekerjaan');
            $table->string('kewarganegaraan')->default('WNI')->after('status_perkawinan');
            $table->string('nama_ayah')->nullable()->after('kewarganegaraan');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropColumn(['pendidikan_terakhir', 'kewarganegaraan', 'nama_ayah', 'nama_ibu']);
        });
    }
};
