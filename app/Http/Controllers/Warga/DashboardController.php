<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\KategoriUmkm;
use App\Models\KategoriProduk;
use App\Models\UmkmUsaha;
use App\Models\UmkmProduk;
use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Pengumuman;
use App\Models\Berita;
use App\Models\Agenda;

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

        $beritaTerkini = Berita::where('status', 'Publish')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $agendaList = [
            [
                'title' => 'Rembug Warga & Pengurus',
                'time' => 'Balai Pertemuan RW 21',
                'icon' => 'bi-calendar-check',
            ]
        ];

        if ($user && $user->nik) {
            $pendingUmkmListCount = UmkmUsaha::where('nik', $user->nik)
                ->where('status_verifikasi', 'Pending')
                ->latest()
                ->count();
        }

        $daftarProdukTerbaru = UmkmProduk::with(['usaha.kategori_umkm', 'kategori_produk', 'usaha.user.penduduk'])
            ->where('status_produk', 'Aktif')
            ->whereHas('usaha', function($q) {
                $q->whereIn('status_verifikasi', ['Approved', 'approved']);
            })
            ->latest()
            ->limit(8)
            ->get();
            
        $pengumumanPin = Pengumuman::where('status', 'Publish')
            ->orderBy('is_priority', 'desc')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $semuaAgenda = Agenda::where('status', 'Publish')
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->map(function ($item) {
                // Konversi tanggal untuk frontend JS (seperti format OP Konten)
                $dt = \Carbon\Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta');
                $item->date_str = $dt->format('Y-m-d');
                $item->time_str = $dt->format('H:i');
                $item->month_short = $dt->translatedFormat('M');
                $item->day_num = $dt->format('d');
                return $item;
            });

        return view('warga.dashboard', compact(
            'pengumumanPin',
            'semuaAgenda',
            'user', 
            'stats', 
            'anggotaKeluarga', 
            'beritaTerkini', 
            'agendaList',
            'pendingUmkmListCount', 
            'daftarProdukTerbaru'));
    }
}