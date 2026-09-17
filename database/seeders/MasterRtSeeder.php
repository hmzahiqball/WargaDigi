<?php

namespace Database\Seeders;

use App\Models\MasterRt;
use Illuminate\Database\Seeder;

class MasterRtSeeder extends Seeder
{
    public function run(): void
    {
        $rtList = [
            [
                'kode_rt' => '01',
                'nama_rt' => '01',
            ],
            [
                'kode_rt' => '02',
                'nama_rt' => '02',
            ],
            [
                'kode_rt' => '03',
                'nama_rt' => '03',
            ],
            [
                'kode_rt' => '04',
                'nama_rt' => '04',
            ],
            [
                'kode_rt' => '05',
                'nama_rt' => '05',
            ],
            [
                'kode_rt' => '06',
                'nama_rt' => '06',
            ],
        ];

        foreach ($rtList as $rt) {
            MasterRt::updateOrCreate(
                ['kode_rt' => $rt['kode_rt']],
                ['nama_rt' => $rt['nama_rt']]
            );
        }
    }
}
