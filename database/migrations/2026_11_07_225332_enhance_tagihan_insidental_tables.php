<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Kategori kas (pemasukan) yang dipilih saat membuat tagihan.
            $table->uuid('kategori_id')->nullable()->after('nominal');
            // Nominal yang tertera bersifat fix (tidak bisa diubah saat bayar).
            $table->boolean('nominal_fix')->default(false)->after('kategori_id');
            // Daftar tanggal reminder (angka 1..31), disimpan sebagai JSON.
            $table->json('reminder_tanggal')->nullable()->after('tenggat');

            $table->foreign('kategori_id')->references('id')->on('kategori_transaksi')->nullOnDelete();
        });

        Schema::table('pembayaran_tagihan', function (Blueprint $table) {
            // Nominal aktual yang dibayar (bisa berbeda dari tagihan bila tidak fix).
            $table->unsignedBigInteger('nominal')->nullable()->after('status');
            // Tautan ke transaksi kas yang dihasilkan saat lunas.
            $table->uuid('transaksi_id')->nullable()->after('bukti_file');
            // Tanggal & pencatat pembayaran (untuk ceklis bendahara).
            $table->date('tanggal_bayar')->nullable()->after('nominal');

            $table->foreign('transaksi_id')->references('id')->on('transaksi_keuangan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn(['kategori_id', 'nominal_fix', 'reminder_tanggal']);
        });
        Schema::table('pembayaran_tagihan', function (Blueprint $table) {
            $table->dropForeign(['transaksi_id']);
            $table->dropColumn(['nominal', 'transaksi_id', 'tanggal_bayar']);
        });
    }
};
