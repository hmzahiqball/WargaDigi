<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

/**
 * Seeder MASTER (produksi): mengisi pengaturan sistem dari nilai default
 * yang didefinisikan pada SystemSetting::defaults().
 *
 * Idempoten karena SystemSetting::setMany() menggunakan updateOrCreate per key.
 * Key 'logo' dilewati bila nilai default-nya null.
 */
class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [];

        foreach (SystemSetting::defaults() as $key => $meta) {
            $value = $meta['value'] ?? null;

            // Skip 'logo' jika memang tidak ada default-nya.
            if ($key === 'logo' && $value === null) {
                continue;
            }

            $pairs[$key] = $value;
        }

        SystemSetting::setMany($pairs);
    }
}
