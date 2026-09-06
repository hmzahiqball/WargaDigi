<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RwController extends Controller
{
    public function dashboard()
    {
        $pendingUmkmCount = \App\Models\UmkmUsaha::where('status_verifikasi', 'Pending')->count();

        $stats = [
            'penduduk' => [
                'total' => '12,450',
                'change' => '+142 bulan ini',
                'trend' => 'up',
            ],
            'dokumen_pending' => [
                'total' => 48,
                'need_review' => 12,
            ],
            'umkm_baru' => [
                'total' => $pendingUmkmCount,
                'status' => 'Menunggu Verifikasi',
            ],
            'konten_ditinjau' => [
                'total' => 8,
                'status' => 'In progress',
            ],
        ];

        $quickActions = [
            [
                'title' => 'Tinjau Dokumen',
                'icon' => 'bi-file-earmark-text',
                'bg' => 'icon-gray',
                'link' => '#',
            ],
            [
                'title' => 'Verifikasi UMKM',
                'icon' => 'bi-shop',
                'bg' => 'icon-green',
                'link' => route('rw.umkm.index'),
            ],
            [
                'title' => 'Periksa Konten',
                'icon' => 'bi-image',
                'bg' => 'icon-red',
                'link' => '#',
            ],
        ];

        $activities = [];

        // Prepend pending UMKM activities if any
        $recentPendingUmkm = \App\Models\UmkmUsaha::with(['pemilik.penduduk'])
            ->where('status_verifikasi', 'Pending')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentPendingUmkm as $pUmkm) {
            $pemilikNama = $pUmkm->pemilik->penduduk->nama_lengkap ?? $pUmkm->pemilik->username ?? 'Warga';
            $activities[] = [
                'icon' => 'bi-shop',
                'title' => "Warga {$pemilikNama} mengajukan pendaftaran UMKM '{$pUmkm->nama_usaha}'.",
                'time' => $pUmkm->created_at ? $pUmkm->created_at->diffForHumans() : 'Baru saja',
                'badge' => 'PERLU VERIFIKASI',
                'badge_class' => 'bg-warning-subtle text-warning border-warning-subtle',
                'quote' => $pUmkm->deskripsi ? \Illuminate\Support\Str::limit($pUmkm->deskripsi, 80) : null,
            ];
        }

        $activities = array_merge($activities, [
            [
                'icon' => 'bi-file-earmark-text',
                'title' => 'RT 04 telah menyerahkan laporan keuangan bulanan.',
                'time' => '10 menit yang lalu',
                'badge' => 'PERLU PEMERIKSAAN',
                'badge_class' => 'bg-danger-subtle text-danger border-danger-subtle',
                'quote' => null,
            ],
            [
                'icon' => 'bi-person-plus',
                'title' => 'RT 01 telah mendaftarkan 3 warga baru.',
                'time' => '1 jam yang lalu',
                'badge' => 'DISETUJUI',
                'badge_class' => 'bg-primary-subtle text-primary border-primary-subtle',
                'quote' => null,
            ],
            [
                'icon' => 'bi-chat-left-dots',
                'title' => 'RT 07 meminta klarifikasi mengenai pedoman UMKM yang baru.',
                'time' => '3 jam yang lalu',
                'badge' => null,
                'badge_class' => null,
                'quote' => 'Apakah fotokopi KTP masih diperlukan jika sudah upload scan?',
            ],
        ]);

        $recentDocs = [
            [
                'title' => 'SOP_UMKM_2023.pdf',
                'desc' => 'Pedoman terbaru untuk pendaftaran bisnis lokal.',
                'icon' => 'bi-file-earmark-text',
                'status' => 'PUBLISHED',
                'status_class' => 'bg-success-subtle text-success border-success-subtle',
                'date' => 'Oct 12',
            ],
            [
                'title' => 'Q3_Townhall_Banner.png',
                'desc' => 'Rancangan untuk pertemuan komunitas mendatang.',
                'icon' => 'bi-image',
                'status' => 'DRAFT',
                'status_class' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                'date' => 'Today',
            ],
        ];

        return view('rw.dashboard', compact('stats', 'quickActions', 'activities', 'recentDocs', 'pendingUmkmCount'));
    }

    /**
     * Halaman Pusat Manajemen UMKM untuk Admin RW
     */
    public function umkm()
    {
        $pendingUsaha = \App\Models\UmkmUsaha::where('status_verifikasi', 'Pending')
            ->with(['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm'])
            ->latest()
            ->get();

        return view('rw.umkm', compact('pendingUsaha'));
    }

    /**
     * Approve UMKM Profile
     */
    public function approveUmkm($id)
    {
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        $usaha->update([
            'status_verifikasi' => 'Approved',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' berhasil disetujui.');
    }

    /**
     * Reject UMKM Profile
     */
    public function rejectUmkm(Request $request, $id)
    {
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        $catatan = strip_tags($request->input('catatan_verifikasi', 'Pendaftaran UMKM belum memenuhi persyaratan RW.'));
        $usaha->update([
            'status_verifikasi' => 'Rejected',
            'is_active' => false,
            'catatan_verifikasi' => $catatan,
        ]);

        return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' telah ditolak.');
    }
}
