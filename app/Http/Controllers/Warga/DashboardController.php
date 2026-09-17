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
use App\Models\PengajuanSurat;
use App\Models\AgendaKehadiran;

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

        $userId = $user->id ?? null;

        $semuaAgenda = Agenda::where('status', 'Publish')
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->map(function ($item) use ($userId) {
                // Konversi tanggal untuk frontend JS (seperti format OP Konten)
                $dt = \Carbon\Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta');
                $item->date_str = $dt->format('Y-m-d');
                $item->time_str = $dt->format('H:i');
                $item->month_short = $dt->translatedFormat('M');
                $item->day_num = $dt->format('d');

                // RSVP data
                $item->user_rsvp_status = null;
                $item->total_hadir = 0;
                if ($item->is_rsvp_enabled && $userId) {
                    $rsvp = AgendaKehadiran::where('agenda_id', $item->id)
                        ->where('user_id', $userId)
                        ->first();
                    $item->user_rsvp_status = $rsvp ? $rsvp->status_kehadiran : null;
                    $item->total_hadir = AgendaKehadiran::where('agenda_id', $item->id)
                        ->where('status_kehadiran', 'Hadir')
                        ->count();
                }

                return $item;
            });

        // Query surat status untuk notifikasi
        $suratNotif = [];
        if ($user && $user->nik) {
            $pendudukSurat = Penduduk::where('nik', $user->nik)->first();
            if ($pendudukSurat) {
                $suratList = PengajuanSurat::where('penduduk_id', $pendudukSurat->id)
                    ->whereIn('status', ['Diajukan', 'Disetujui RT', 'Disetujui RW', 'Ditolak RT', 'Ditolak RW'])
                    ->latest()
                    ->get();
                foreach ($suratList as $s) {
                    $suratNotif[] = (object)[
                        'id' => $s->id,
                        'tipe_surat' => $s->tipe_surat,
                        'status' => $s->status,
                        'tanggal' => $s->created_at->format('d M Y'),
                    ];
                }
            }
        }

        return view('warga.dashboard', compact(
            'pengumumanPin',
            'semuaAgenda',
            'user', 
            'stats', 
            'anggotaKeluarga', 
            'beritaTerkini', 
            'agendaList',
            'pendingUmkmListCount', 
            'daftarProdukTerbaru',
            'suratNotif'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = \Illuminate\Support\Facades\Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('home'));
    }
}