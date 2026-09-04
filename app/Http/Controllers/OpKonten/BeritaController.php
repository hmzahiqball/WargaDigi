<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy sesuai dengan desain Figma
        $berita = [
            [
                'id' => 1,
                'judul' => 'Vaksinasi Massal Warga 04',
                'kategori' => 'SOSIAL',
                'penulis' => 'Budi Santoso',
                'status' => 'Draft',
                'status_bg' => '#F3F4F6',
                'status_color' => '#6B7280',
                'image' => 'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=200&auto=format&fit=crop',
            ],
            [
                'id' => 2,
                'judul' => 'Renovasi Pos Ronda RT 02',
                'kategori' => 'INFRASTRUKTUR',
                'penulis' => 'Siti Aminah',
                'status' => 'Publish',
                'status_bg' => '#D1FAE5',
                'status_color' => '#065F46',
                'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=200&auto=format&fit=crop',
            ],
            [
                'id' => 3,
                'judul' => 'Persiapan Malam Tirakatan 17an',
                'kategori' => 'HIBURAN',
                'penulis' => 'Budi Santoso',
                'status' => 'Review',
                'status_bg' => '#FEE2E2',
                'status_color' => '#991B1B',
                'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=200&auto=format&fit=crop',
            ]
        ];

        return view('opKonten.berita.index', compact('berita'));
    }
}
