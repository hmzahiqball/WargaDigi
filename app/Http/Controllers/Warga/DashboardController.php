<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penduduk;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $anggotaKeluarga = [];
        if ($user && $user->nik) {
            $penduduk = Penduduk::where('nik', $user->nik)->first();
            if ($penduduk && $penduduk->no_kk) {
                $anggotaKeluarga = Penduduk::where('no_kk', $penduduk->no_kk)->get();
            }
        }

        $stats = [
            'status_akun' => [
                'label' => 'STATUS AKUN',
                'value' => $user->status_akun ?? 'Active',
                'color' => '#2E7D32',
            ],
            'peran' => [
                'label' => 'HAK AKSES',
                'value' => $user->role ?? 'Warga',
                'color' => '#198754',
            ],
            'jumlah_tanggungan' => [
                'label' => 'ANGGOTA KELUARGA',
                'value' => count($anggotaKeluarga) > 0 ? count($anggotaKeluarga) : 1,
                'color' => '#0d6efd',
            ],
        ];

        $updatesTerkini = [
            [
                'title' => 'Pembersihan Saluran Air Lingkungan RW 21',
                'desc' => 'Kegiatan gotong royong warga bersama pengurus RT dan RW untuk mengantisipasi musim hujan...',
                'category' => 'GOTONG ROYONG',
                'location' => 'RW 21 Tanimulya',
                'date' => 'Hari ini',
            ]
        ];

        $agendaList = [
            [
                'title' => 'Rembug Warga & Pengurus',
                'time' => 'Balai Pertemuan RW 21',
                'icon' => 'bi-calendar-check',
            ]
        ];

        return view('warga.dashboard', compact('user', 'stats', 'anggotaKeluarga', 'updatesTerkini', 'agendaList'));
    }
}