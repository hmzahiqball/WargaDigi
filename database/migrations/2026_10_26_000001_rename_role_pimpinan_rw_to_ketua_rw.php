<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rename role "Pimpinan RW" menjadi "Ketua RW" di seluruh data existing.
 *
 * Langkah (khusus MySQL karena kolom users.role bertipe enum):
 *  1. Perluas enum users.role agar sementara memuat BAIK 'Pimpinan RW' MAUPUN
 *     'Ketua RW', sehingga UPDATE nilai tidak melanggar definisi enum.
 *  2. Update nilai data: users.role dan tabel Spatie `roles`.name.
 *  3. Persempit enum ke daftar final (tanpa 'Pimpinan RW').
 *
 * Idempoten: bila nilai sudah 'Ketua RW', UPDATE tidak berdampak.
 */
return new class extends Migration
{
    /** Daftar enum FINAL users.role (tanpa 'Pimpinan RW'). */
    private array $finalRoles = [
        'Admin Aplikasi', 'Admin RW', 'Ketua RW', 'Op Konten RW', 'Op Keuangan RW',
        'Ketua RT', 'Op Konten RT', 'Op Keuangan RT', 'DKM', 'Warga',
    ];

    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // 1) Enum sementara = final + 'Pimpinan RW' (agar UPDATE aman).
            $this->setRoleEnum(array_merge($this->finalRoles, ['Pimpinan RW']));
        }

        // 2) Update data.
        DB::table('users')->where('role', 'Pimpinan RW')->update(['role' => 'Ketua RW']);

        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'Pimpinan RW')->update(['name' => 'Ketua RW']);
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // 3) Persempit enum ke daftar final.
            $this->setRoleEnum($this->finalRoles);
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $this->setRoleEnum(array_merge($this->finalRoles, ['Pimpinan RW']));
        }

        DB::table('users')->where('role', 'Ketua RW')->update(['role' => 'Pimpinan RW']);

        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'Ketua RW')->update(['name' => 'Pimpinan RW']);
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $rollbackRoles = array_values(array_map(
                fn ($r) => $r === 'Ketua RW' ? 'Pimpinan RW' : $r,
                $this->finalRoles
            ));
            $this->setRoleEnum($rollbackRoles);
        }
    }

    /**
     * Setel definisi enum kolom users.role untuk MySQL/MariaDB.
     *
     * @param array<int,string> $roles
     */
    private function setRoleEnum(array $roles): void
    {
        $quoted = implode(',', array_map(fn ($r) => "'" . str_replace("'", "''", $r) . "'", $roles));
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM({$quoted}) NOT NULL");
    }
};
