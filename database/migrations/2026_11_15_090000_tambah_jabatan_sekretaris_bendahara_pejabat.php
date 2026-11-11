<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Tambah jabatan Sekretaris dan Bendahara (RW, RT, DKM) pada kepengurusan. */
    public function up(): void
    {
        DB::statement("ALTER TABLE pejabat MODIFY jabatan ENUM('ketua_rw','ketua_rt','sekretaris_rw','sekretaris_rt','bendahara_rw','bendahara_rt','bendahara_dkm') NOT NULL");
    }

    public function down(): void
    {
        DB::table('pejabat')->whereNotIn('jabatan', ['ketua_rw', 'ketua_rt'])->delete();
        DB::statement("ALTER TABLE pejabat MODIFY jabatan ENUM('ketua_rw','ketua_rt') NOT NULL");
    }
};