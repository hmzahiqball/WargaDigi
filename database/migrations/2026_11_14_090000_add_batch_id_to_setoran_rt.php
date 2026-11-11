<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu setoran (satu transfer/serah terima) dapat mencakup beberapa bulan atau beberapa Iuran Lainnya.
        Schema::table('setoran_rt', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable()->after('tagihan_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('setoran_rt', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};