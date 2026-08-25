<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index()
    {
        $categories = ['Semua Kategori', 'Makanan & Minuman', 'Jasa', 'Kerajinan', 'Sembako'];

        $products = [
            [
                'image' => '/images/umkm-1.jpg',
                'name' => 'Nasi Kuning Bu Tejo Komplit',
                'price' => 'Rp 25.000',
                'rt' => 'Warga RT 02',
                'desc' => 'Nasi kuning wangi dengan ayam goreng, telur balado, oreg tempe.',
                'category' => 'Makanan & Minuman',
            ],
            [
                'image' => '/images/umkm-2.jpg',
                'name' => 'Jasa Servis Elektronik Pak Budi',
                'price' => 'Mulai Rp 50k',
                'rt' => 'Warga RT 05',
                'desc' => 'Melayani perbaikan TV, Kipas Angin, Mesin Cuci dengan garansi...',
                'category' => 'Jasa',
            ],
            [
                'image' => '/images/umkm-3.jpg',
                'name' => 'Kerajinan Anyaman Bambu Sari',
                'price' => 'Rp 35.000',
                'rt' => 'Warga RT 01',
                'desc' => 'Kotak tisu dan wadah serbaguna dari anyaman bambu asli yang kuat.',
                'category' => 'Kerajinan',
            ],
            [
                'image' => '/images/umkm-4.jpg',
                'name' => 'Warung Sayur & Sembako Makmur',
                'price' => 'Bervariasi',
                'rt' => 'Warga RT 04',
                'desc' => 'Sedia sayuran segar tiap pagi, beras, minyak goreng, dan...',
                'category' => 'Sembako',
            ],
        ];

        return view('pages.umkm', compact('categories', 'products'));
    }
}
