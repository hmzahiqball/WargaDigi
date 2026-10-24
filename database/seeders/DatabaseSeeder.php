<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Switch mode seeding via flag .env APP_DEMO.
        // - APP_DEMO=true  -> seed data DEMO lengkap (showcase).
        // - APP_DEMO=false -> hanya data MASTER (produksi).
        //
        // Kedua seeder juga tetap bisa dipanggil manual tanpa mengubah .env:
        //   php artisan db:seed --class=DemoSeeder
        //   php artisan db:seed --class=MasterSeeder
        $demo = filter_var(env('APP_DEMO', false), FILTER_VALIDATE_BOOLEAN);

        if ($demo) {
            $this->call(DemoSeeder::class);
        } else {
            $this->call(MasterSeeder::class);
        }
    }
}
