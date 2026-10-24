<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Paket 2B — penyesuaian tabel users:
 *  - nik dibuat nullable (akun pengurus tidak punya NIK kependudukan).
 *    Unique index pada nik dipertahankan; MySQL mengizinkan banyak baris NULL
 *    pada unique index, sehingga banyak akun pengurus tanpa NIK tetap valid.
 *  - username dibuat unique (menjadi identitas login utama untuk semua akun).
 *
 * Catatan: umkm_usaha.nik memiliki FK ke users.nik, sehingga FK itu harus
 * dilepas sementara sebelum mengubah unique index pada users.nik, lalu dipasang
 * kembali dengan definisi yang sama (ON DELETE CASCADE).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 0) Lepas FK umkm_usaha.nik -> users.nik agar index unique nik bisa diubah.
        Schema::table('umkm_usaha', function (Blueprint $table) {
            $table->dropForeign('umkm_usaha_nik_foreign');
        });

        // 1) Lepas unique lama pada nik agar kolom bisa diubah jadi nullable.
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_nik_unique');
        });

        // 2) Ubah nik menjadi nullable.
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->nullable()->change();
        });

        // 3) Pasang kembali unique pada nik (NULL berganda diizinkan MySQL),
        //    dan tambahkan unique pada username.
        Schema::table('users', function (Blueprint $table) {
            $table->unique('nik', 'users_nik_unique');
            $table->unique('username', 'users_username_unique');
        });

        // 4) Pasang kembali FK umkm_usaha.nik -> users.nik (sama seperti semula).
        Schema::table('umkm_usaha', function (Blueprint $table) {
            $table->foreign('nik', 'umkm_usaha_nik_foreign')
                ->references('nik')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('umkm_usaha', function (Blueprint $table) {
            $table->dropForeign('umkm_usaha_nik_foreign');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropUnique('users_nik_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('nik', 'users_nik_unique');
        });

        Schema::table('umkm_usaha', function (Blueprint $table) {
            $table->foreign('nik', 'umkm_usaha_nik_foreign')
                ->references('nik')->on('users')
                ->cascadeOnDelete();
        });
    }
};
