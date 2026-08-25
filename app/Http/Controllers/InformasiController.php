<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index()
    {
        $stats = [
            ['icon' => 'bi-people', 'label' => 'Total Warga', 'value' => '1,446', 'sub' => 'Jiwa terdaftar'],
            ['icon' => 'bi-house-door', 'label' => 'Kepala Keluarga', 'value' => '416', 'sub' => 'Kartu Keluarga aktif'],
            ['icon' => 'bi-building', 'label' => 'Wilayah RT', 'value' => '6', 'sub' => 'Rukun Tetangga'],
            ['icon' => 'bi-shop', 'label' => 'Potensi UMKM', 'value' => '45', 'sub' => 'Usaha warga aktif', 'highlight' => true],
        ];

        $genderData = [
            'labels' => ['Laki-laki', 'Perempuan'],
            'data' => [713, 733],
            'colors' => ['#2E7D32', '#81C784'],
        ];

        $ageData = [
            'labels' => ['0-4', '5-14', '15-24', '25-34', '35-44', '45-54', '55+'],
            'data' => [85, 180, 210, 280, 250, 210, 231],
        ];

        $rtData = [
            ['rt' => 'RT 01', 'kk' => 102, 'jiwa' => 354, 'umkm' => '5 Usaha'],
            ['rt' => 'RT 02', 'kk' => 68, 'jiwa' => 203, 'umkm' => '8 Usaha'],
            ['rt' => 'RT 03', 'kk' => 84, 'jiwa' => 272, 'umkm' => '3 Usaha'],
            ['rt' => 'RT 04', 'kk' => 72, 'jiwa' => 227, 'umkm' => '12 Usaha'],
        ];

        $facilities = [
            ['icon' => 'bi-mosque', 'name' => 'Masjid Jami Al-Ikhlas', 'location' => 'Pusat RT 04'],
            ['icon' => 'bi-tree', 'name' => 'Taman Bermain Anak', 'location' => 'Area RT 02'],
            ['icon' => 'bi-heart-pulse', 'name' => 'Posyandu Flamboyan', 'location' => 'Balai Warga, RT 01'],
        ];

        return view('pages.informasi', compact('stats', 'genderData', 'ageData', 'rtData', 'facilities'));
    }
}
