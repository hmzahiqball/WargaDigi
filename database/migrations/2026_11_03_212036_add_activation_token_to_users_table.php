<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Token aktivasi sekali pakai (Opsi B): RT membuat akun warga dalam status
 * Unverified + token; warga membuka link /aktivasi/{token} untuk menetapkan
 * password sendiri tanpa OTP. Token dihapus setelah dipakai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'activation_token')) {
                $table->string('activation_token', 64)->nullable()->unique()->after('status_akun');
            }
            if (! Schema::hasColumn('users', 'activation_token_created_at')) {
                $table->timestamp('activation_token_created_at')->nullable()->after('activation_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'activation_token')) {
                $table->dropColumn('activation_token');
            }
            if (Schema::hasColumn('users', 'activation_token_created_at')) {
                $table->dropColumn('activation_token_created_at');
            }
        });
    }
};
