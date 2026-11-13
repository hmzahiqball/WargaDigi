<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\UmkmUsaha;
use App\Models\UmkmProduk;
use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;

class RwController extends Controller
{
    public function dashboard()
    {
        $pendingUmkmCount = \App\Models\UmkmUsaha::where('status_verifikasi', 'Pending')->count();

        // Data statistik dinamis
        $totalPenduduk = \App\Models\Penduduk::count();
        $pendudukBaruBulanIni = \App\Models\Penduduk::where('created_at', '>=', now()->startOfMonth())->count();

        $dokumenNeedReviewRw = PengajuanSurat::where('status', 'Disetujui RT')->count();
        $dokumenPendingRt = PengajuanSurat::where('status', 'Diajukan')->count();
        $totalDokumenPending = $dokumenNeedReviewRw + $dokumenPendingRt;

        // Semua konten (berita, agenda, pengumuman; RT dan RW) disetujui Admin RW. Peran lain tidak menghitungnya sebagai pekerjaannya.
        $bolehSetujuKonten = \App\Support\KontenScope::adminRw(Auth::user());
        $pendingBerita = $bolehSetujuKonten ? \App\Models\Berita::where('status', 'Review')->count() : 0;
        $pendingAgenda = $bolehSetujuKonten ? \App\Models\Agenda::where('status', 'Review')->count() : 0;
        $pendingPengumuman = $bolehSetujuKonten ? \App\Models\Pengumuman::where('status', 'Review')->count() : 0;
        $totalKontenReview = $pendingBerita + $pendingAgenda + $pendingPengumuman;

        $stats = [
            'penduduk' => [
                'total' => number_format($totalPenduduk, 0, ',', '.'),
                // Panah naik hanya bila memang ada penduduk baru bulan ini.
                'change' => $pendudukBaruBulanIni > 0
                    ? '+' . number_format($pendudukBaruBulanIni, 0, ',', '.') . ' bulan ini'
                    : 'Tidak ada penambahan bulan ini',
                'trend' => $pendudukBaruBulanIni > 0 ? 'up' : 'flat',
            ],
            'dokumen_pending' => [
                'total' => $totalDokumenPending,
                'need_review' => $dokumenNeedReviewRw,
            ],
            'umkm_baru' => [
                'total' => $pendingUmkmCount,
                'status' => $pendingUmkmCount > 0 ? 'Menunggu Verifikasi' : 'Semua Terverifikasi',
            ],
            'konten_ditinjau' => [
                'total' => $totalKontenReview,
                'status' => $totalKontenReview > 0 ? 'Perlu Ditinjau' : 'Semua sudah ditinjau',
                // Admin RW membuka antrean persetujuan; peran lain memantau konten terbit.
                'link' => $bolehSetujuKonten ? route('rw.persetujuan-konten.index') : route('rw.pusat-informasi.index'),
            ],
        ];

        // Ringkasan kas RW (hanya baca). Rincian lengkap tetap di Laporan Keuangan.
        $keuangan = \App\Services\DashboardKeuanganRwService::ringkasanKas();

        $quickActions = [
            [
                'title' => 'Tinjau Dokumen',
                'icon' => 'bi-file-earmark-text',
                'bg' => 'icon-gray',
                'link' => route('rw.persetujuan-dokumen'),
            ],
            // Disembunyikan sementara: pintasan Persetujuan Keuangan di dashboard RW (rute tetap ada).
            // [
                // 'title' => 'Persetujuan Keuangan',
                // 'icon' => 'bi-cash-coin',
                // 'bg' => 'icon-yellow',
                // 'link' => route('rw.persetujuan-keuangan.index'),
            // ],
            [
                'title' => 'Verifikasi UMKM',
                'icon' => 'bi-shop',
                'bg' => 'icon-green',
                'link' => route('rw.persetujuan-umkm'),
            ],
            // Admin RW memeriksa antrean persetujuan konten; peran lain hanya memantau konten yang sudah terbit.
            \App\Support\KontenScope::adminRw(Auth::user())
                ? [
                    'title' => 'Periksa Konten',
                    'icon' => 'bi-image',
                    'bg' => 'icon-red',
                    'link' => route('rw.persetujuan-konten.index'),
                ]
                : [
                    'title' => 'Pusat Informasi',
                    'icon' => 'bi-chat-dots',
                    'bg' => 'icon-red',
                    'link' => route('rw.pusat-informasi.index'),
                ],
        ];

        $activities = [];

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
                'time_sort' => $pUmkm->created_at,
                'link' => route('rw.persetujuan-umkm'),
                'badge' => 'PERLU VERIFIKASI',
                'badge_class' => 'bg-warning-subtle text-warning border-warning-subtle',
                'quote' => $pUmkm->deskripsi ? \Illuminate\Support\Str::limit($pUmkm->deskripsi, 80) : null,
            ];
        }

        // Pengajuan surat yang menunggu review RW
        $recentSurat = PengajuanSurat::with(['penduduk.keluarga.rt'])
            ->where('status', 'Disetujui RT')
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentSurat as $surat) {
            $namaPemohon = $surat->penduduk->nama_lengkap ?? 'Warga';
            $rtKode = $surat->penduduk->keluarga->rt->kode_rt ?? null;
            $labelRt = $rtKode ? "RT {$rtKode}" : 'RT';
            $activities[] = [
                'icon' => 'bi-file-earmark-check',
                'title' => "Surat {$surat->tipe_surat} untuk {$namaPemohon} telah disetujui {$labelRt} dan menunggu persetujuan RW.",
                'time' => $surat->updated_at ? $surat->updated_at->diffForHumans() : 'Baru saja',
                'time_sort' => $surat->updated_at,
                'link' => route('rw.persetujuan-dokumen'),
                'badge' => 'PERLU PENGESAHAN',
                'badge_class' => 'bg-info-subtle text-info border-info-subtle',
                'quote' => $surat->keterangan_tambahan ? \Illuminate\Support\Str::limit($surat->keterangan_tambahan, 80) : null,
            ];
        }

        // Disembunyikan sementara bersama menu Persetujuan Keuangan RW: laporan keuangan yang menunggu persetujuan
        // tidak lagi muncul sebagai tugas di dashboard. Aktifkan kembali dengan mengganti collect() di bawah.
        // $recentLaporan = \App\Models\LaporanKeuangan::with(['rt'])
        //     ->whereIn('unit', ['RW', 'DKM'])
        //     ->where('status', 'Submitted')
        //     ->latest()
        //     ->take(2)
        //     ->get();
        $recentLaporan = collect();

        foreach ($recentLaporan as $lap) {
            $unitLabel = $lap->unit === 'DKM' ? 'DKM' : ($lap->rt->nama_rt ?? 'RW');
            $activities[] = [
                'icon' => 'bi-file-earmark-text',
                'title' => "{$unitLabel} mengajukan {$lap->judul} untuk disetujui.",
                'time' => $lap->created_at ? $lap->created_at->diffForHumans() : 'Baru saja',
                'time_sort' => $lap->created_at,
                'badge' => 'PERLU PEMERIKSAAN',
                'badge_class' => 'bg-danger-subtle text-danger border-danger-subtle',
                'quote' => null,
            ];
        }

        // Mutasi warga terbaru yang menunggu persetujuan RW
        $recentMutasi = \App\Models\MutasiWarga::with(['keluarga.rt'])
            ->where('status', 'menunggu_rw')
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentMutasi as $mutasi) {
            $rtLabel = $mutasi->keluarga->rt->nama_rt ?? 'RT';
            $jenisLabel = $mutasi->jenis_mutasi === 'tambah' ? 'penambahan anggota keluarga' : 'perubahan data warga';
            $activities[] = [
                'icon' => 'bi-person-lines-fill',
                'title' => "Pengajuan {$jenisLabel} dari {$rtLabel} menunggu persetujuan RW.",
                'time' => $mutasi->created_at ? $mutasi->created_at->diffForHumans() : 'Baru saja',
                'time_sort' => $mutasi->created_at,
                'link' => route('rw.mutasi.index'),
                'badge' => 'PERLU PERSETUJUAN',
                'badge_class' => 'bg-primary-subtle text-primary border-primary-subtle',
                'quote' => null,
            ];
        }

        // Urutkan aktivitas berdasarkan waktu terbaru, batasi 6 item
        $activities = collect($activities)
            ->sortByDesc(fn ($a) => optional($a['time_sort'] ?? null)->getTimestamp() ?? 0)
            ->take(6)
            ->values()
            ->all();

        // Konten terbaru (berita, agenda, pengumuman; RW dan semua RT), 4 item terbaru lintas jenis.
        $recentDocs = $this->kontenTerbaruDashboard($bolehSetujuKonten);

        // Data demografi (lingkup seluruh RW + sebaran per RT).
        $demografi = app(\App\Services\DemografiService::class)->semua(null, true);

        return view('rw.dashboard', compact('stats', 'quickActions', 'activities', 'recentDocs', 'pendingUmkmCount', 'demografi', 'keuangan'));
    }

    /**
     * Empat konten terbaru lintas berita, agenda, dan pengumuman (RW dan semua RT).
     * Admin RW melihat yang terbit dan yang menunggu tinjauannya; peran lain hanya yang sudah terbit,
     * sama seperti Pusat Informasi. Draft milik operator tidak ditampilkan.
     *
     * @return array<int, array<string, mixed>>
     */
    private function kontenTerbaruDashboard(bool $adminRw): array
    {
        $status = $adminRw ? ['Publish', 'Review'] : ['Publish'];
        $jenis = [
            [\App\Models\Berita::class, 'judul_berita', 'isi_berita', 'bi-newspaper', 'Berita'],
            [\App\Models\Agenda::class, 'judul_agenda', 'detail_pengumuman', 'bi-calendar-event', 'Agenda'],
            [\App\Models\Pengumuman::class, 'judul_pengumuman', 'isi_pengumuman', 'bi-megaphone', 'Pengumuman'],
        ];

        $semua = collect();
        foreach ($jenis as [$model, $kolomJudul, $kolomIsi, $ikon, $label]) {
            $model::with('rt')->whereIn('status', $status)->latest('updated_at')->take(4)->get()
                ->each(function ($item) use ($semua, $kolomJudul, $kolomIsi, $ikon, $label, $adminRw) {
                    $menunggu = $item->status === 'Review';
                    $semua->push([
                        'title' => $item->{$kolomJudul},
                        'desc' => \Illuminate\Support\Str::limit(trim(strip_tags((string) $item->{$kolomIsi})), 60),
                        'icon' => $ikon,
                        'jenis' => $label,
                        'asal' => $item->asal_label,
                        'status' => $menunggu ? 'MENUNGGU TINJAUAN' : 'TERBIT',
                        'status_class' => $this->dashboardStatusClass($item->status),
                        'date' => optional($item->updated_at)->translatedFormat('d M'),
                        'link' => $menunggu && $adminRw
                            ? route('rw.persetujuan-konten.index')
                            : route('rw.pusat-informasi.index'),
                        'sort' => optional($item->updated_at)->getTimestamp() ?? 0,
                    ]);
                });
        }

        return $semua->sortByDesc('sort')->take(4)->values()->all();
    }

    /**
     * Pemetaan status konten ke kelas badge Bootstrap untuk kartu dokumen di dashboard.
     */
    private function dashboardStatusClass(?string $status): string
    {
        return match ($status) {
            'Publish' => 'bg-success-subtle text-success border-success-subtle',
            'Review' => 'bg-warning-subtle text-warning border-warning-subtle',
            'Revisi' => 'bg-danger-subtle text-danger border-danger-subtle',
            default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }

    /**
     * Pojok UMKM (RW): daftar usaha yang sudah disetujui, dengan pencarian dan filter.
     * Pengajuan baru dan yang ditolak dipindah ke menu Persetujuan → Persetujuan UMKM.
     */
    public function umkm(Request $request)
    {
        // Tautan lama ?tab=persetujuan|ditolak diarahkan ke halaman Persetujuan UMKM.
        if (in_array($request->get('tab'), ['persetujuan', 'ditolak'], true)) {
            return redirect()->route('rw.persetujuan-umkm', ['tab' => $request->get('tab')]);
        }

        $search = trim((string) ($request->get('q') ?? $request->get('q_terdaftar') ?? $request->get('search')));
        $kategori = $request->get('kategori');
        $status = $request->get('status');
        $isPimpinan = ! $this->adminRwAktif();

        $queryTerdaftar = UmkmUsaha::where('status_verifikasi', 'Approved')
            ->with(['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm', 'produk']);

        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (! empty($words)) {
                $queryTerdaftar->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_usaha) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(alamat_usaha) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(deskripsi) LIKE ?', [$term])
                          ->orWhereHas('kategori_umkm', function ($kq) use ($term) {
                              $kq->whereRaw('LOWER(nama_kategori) LIKE ?', [$term]);
                          })
                          ->orWhereHas('pemilik.penduduk', function ($pq) use ($term) {
                              $pq->whereRaw('LOWER(nama_lengkap) LIKE ?', [$term]);
                          })
                          ->orWhereHas('user', function ($uq) use ($term) {
                              $uq->whereRaw('LOWER(username) LIKE ?', [$term])
                                 ->orWhere('nik', 'LIKE', $term);
                          })
                          ->orWhereHas('produk', function ($prq) use ($term) {
                              $prq->whereRaw('LOWER(nama_produk) LIKE ?', [$term]);
                          });
                    }
                });
            }
        }

        if ($kategori && ! in_array($kategori, ['Semua', 'Semua Kategori'])) {
            $queryTerdaftar->whereHas('kategori_umkm', function ($q) use ($kategori) {
                if (\Illuminate\Support\Str::isUuid($kategori)) {
                    $q->where('id', $kategori)->orWhere('nama_kategori', $kategori);
                } else {
                    $q->where('nama_kategori', $kategori);
                }
            });
        }

        if ($status === 'Aktif') {
            $queryTerdaftar->where('is_active', true);
        } elseif ($status === 'Tidak Aktif' || $status === 'Non-Aktif') {
            $queryTerdaftar->where('is_active', false);
        } elseif (in_array($status, ['Stok Tersedia', 'Stok Menipis', 'Stok Habis'], true)) {
            $stok = strtolower(substr($status, 5));
            $queryTerdaftar->whereHas('produk', fn ($q) => $q->where('status_stok', $stok));
        }

        // Usaha terdaftar - 6 per halaman
        $daftarUsahaTerdaftar = $queryTerdaftar->latest()
            ->paginate(6, ['*'], 'terdaftar_page')
            ->withQueryString();
        $daftarKategoriUmkm = KategoriUmkm::all();

        return view('rw.umkm.terdaftar', compact(
            'daftarUsahaTerdaftar',
            'daftarKategoriUmkm',
            'isPimpinan',
            'search',
            'kategori',
            'status'
        ));
    }

    /**
     * Persetujuan UMKM (menu Persetujuan): tab pengajuan menunggu verifikasi dan tab ditolak.
     * Admin RW memproses; Ketua RW memantau riwayat seluruh pengajuan (hanya baca).
     */
    public function persetujuanUmkm(Request $request)
    {
        $tab = $request->get('tab') === 'ditolak' || ($request->has('ditolak_page') && ! $request->has('approval_page'))
            ? 'ditolak'
            : 'persetujuan';
        $isPimpinan = ! $this->adminRwAktif();
        $relasi = ['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm'];

        // Admin RW: antrean Pending. Ketua RW: riwayat seluruh pengajuan (Pending, Approved, Rejected).
        $pengajuan = UmkmUsaha::with($relasi)
            ->whereIn('status_verifikasi', $isPimpinan ? ['Pending', 'Approved', 'Rejected'] : ['Pending'])
            ->latest()
            ->paginate(4, ['*'], 'approval_page')
            ->withQueryString();
        $pengajuan->appends(['tab' => 'persetujuan']);

        $rejectedUsaha = UmkmUsaha::with($relasi)
            ->where('status_verifikasi', 'Rejected')
            ->latest()
            ->paginate(4, ['*'], 'ditolak_page')
            ->withQueryString();
        $rejectedUsaha->appends(['tab' => 'ditolak']);

        $pendingCount = UmkmUsaha::where('status_verifikasi', 'Pending')->count();

        return view('rw.umkm.persetujuan', compact('tab', 'isPimpinan', 'pengajuan', 'rejectedUsaha', 'pendingCount'));
    }

    /** Peran aktif Admin RW (penyetuju UMKM). Ketua RW hanya memantau. */
    private function adminRwAktif(): bool
    {
        return trim((string) Auth::user()?->activeRole()) === 'Admin RW';
    }

    public function detailUsahaUmkm(Request $request, $id = null)
    {
        $id = $id ?? $request->get('usaha_id');
        $usaha = UmkmUsaha::with(['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm', 'kategori_produk'])
            ->findOrFail($id);

        $search = trim((string)($request->get('q') ?? $request->get('search')));
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        $query = UmkmProduk::where('umkm_usaha_id', $usaha->id)
            ->with(['usaha.kategori_umkm', 'usaha.kategori_produk', 'kategori_produk']);

        $allUsahaProduk = (clone $query)->get();
        $jumlahProdukAktif = $allUsahaProduk->where('status_produk', 'Aktif')->count();
        $jumlahProdukTidakAktif = $allUsahaProduk->whereIn('status_produk', ['Tidak Aktif', 'Non-Aktif'])->count();
        $jumlahProdukPending = $jumlahProdukTidakAktif;
        $jumlahKategoriProduk = $allUsahaProduk->groupBy(fn($item) => $item->kategori_produk->nama_kategori ?? 'Lainnya')->count();
        $jumlahProdukStokMenipis = $allUsahaProduk->filter(fn($item) => strtolower($item->status_stok ?? '') === 'menipis')->count();

        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_produk) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(deskripsi) LIKE ?', [$term])
                          ->orWhereHas('kategori_produk', function ($kq) use ($term) {
                              $kq->whereRaw('LOWER(nama_kategori) LIKE ?', [$term]);
                          });
                    }
                });
            }
        }

        if ($kategori && !in_array($kategori, ['Semua', 'Semua Kategori'])) {
            $query->whereHas('kategori_produk', function ($q) use ($kategori) {
                if (\Illuminate\Support\Str::isUuid($kategori)) {
                    $q->where('id', $kategori)->orWhere('nama_kategori', $kategori);
                } else {
                    $q->where('nama_kategori', $kategori);
                }
            });
        }

        if ($status === 'Aktif') {
            $query->where('status_produk', 'Aktif');
        } elseif ($status === 'Tidak Aktif' || $status === 'Non-Aktif') {
            $query->whereIn('status_produk', ['Tidak Aktif', 'Non-Aktif']);
        } elseif ($status === 'Stok Tersedia' || $status === 'Tersedia') {
            $query->where('status_stok', 'tersedia');
        } elseif ($status === 'Stok Menipis' || $status === 'Menipis') {
            $query->where('status_stok', 'menipis');
        } elseif ($status === 'Stok Habis' || $status === 'Habis') {
            $query->where('status_stok', 'habis');
        }

        $produk = $query->latest()->paginate(8)->withQueryString();
        $kategoriProdukList = KategoriProduk::where('umkm_usaha_id', $usaha->id)->get();
        $daftarKategoriUmkm = KategoriUmkm::all();

        return view('rw.umkm.umkm_detail_usaha', compact(
            'usaha',
            'produk',
            'search',
            'kategori',
            'status',
            'jumlahProdukAktif',
            'jumlahProdukTidakAktif',
            'jumlahProdukPending',
            'jumlahKategoriProduk',
            'jumlahProdukStokMenipis',
            'kategoriProdukList',
            'daftarKategoriUmkm'
        ));
    }

    public function approveUmkm($id)
    {
        $this->pastikanAdminRwUmkm();
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);

        // Permintaan ganda (klik dua kali / browser mengirim ulang POST): sudah disetujui, cukup konfirmasi.
        if ($usaha->status_verifikasi === 'Approved') {
            return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' sudah disetujui.');
        }
        if ($usaha->status_verifikasi !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan UMKM dengan status Pending yang dapat disetujui.');
        }

        $usaha->update([
            'status_verifikasi' => 'Approved',
            'is_active' => true,
        ]);

        $pemilik = \App\Models\User::where('nik', $usaha->nik)->first();
        if ($pemilik) {
            $pemilik->notify(new \App\Notifications\UmkmStatusNotification($usaha, 'Approved'));
        }

        return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' berhasil disetujui.');
    }

    public function rejectUmkm(Request $request, $id)
    {
        $this->pastikanAdminRwUmkm();
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);

        // Permintaan ganda: sudah ditolak, cukup konfirmasi tanpa mengirim notifikasi lagi.
        if ($usaha->status_verifikasi === 'Rejected') {
            return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' sudah ditolak.');
        }
        if ($usaha->status_verifikasi !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan UMKM dengan status Pending yang dapat diproses.');
        }

        $request->validate([
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $catatan = strip_tags($request->input('catatan_verifikasi', 'Pendaftaran UMKM belum memenuhi persyaratan RW.'));
        $usaha->update([
            'status_verifikasi' => 'Rejected',
            'is_active' => false,
            'catatan_verifikasi' => $catatan,
        ]);

        $pemilik = \App\Models\User::where('nik', $usaha->nik)->first();
        if ($pemilik) {
            $pemilik->notify(new \App\Notifications\UmkmStatusNotification($usaha, 'Rejected', $catatan));
        }

        return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' telah ditolak.');
    }

    /**
     * Nonaktifkan usaha yang sudah disetujui (tidak tampil di Pojok UMKM). Hanya Admin RW.
     * Produk tetap tersimpan; pemilik melihat alasan di halaman kelola.
     */
    public function nonaktifkanUmkm(Request $request, $id)
    {
        $this->pastikanAdminRwUmkm();
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        if ($usaha->status_verifikasi !== 'Approved' || ! $usaha->is_active) {
            return redirect()->back()->with('error', 'Hanya usaha yang sudah disetujui dan sedang aktif yang dapat dinonaktifkan.');
        }

        $request->validate(['catatan_verifikasi' => 'required|string|max:500'], [
            'catatan_verifikasi.required' => 'Alasan penonaktifan wajib diisi.',
        ]);
        $catatan = strip_tags($request->input('catatan_verifikasi'));

        $usaha->update(['is_active' => false, 'catatan_verifikasi' => $catatan]);

        $pemilik = \App\Models\User::where('nik', $usaha->nik)->first();
        $pemilik?->notify(new \App\Notifications\UmkmStatusNotification($usaha, 'Nonaktif', $catatan));

        return redirect()->back()->with('success', 'Usaha ' . $usaha->nama_usaha . ' telah dinonaktifkan dan tidak tampil di Pojok UMKM.');
    }

    /** Aktifkan kembali usaha yang dinonaktifkan Admin RW. */
    public function aktifkanUmkm($id)
    {
        $this->pastikanAdminRwUmkm();
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        if ($usaha->status_verifikasi !== 'Approved' || $usaha->is_active) {
            return redirect()->back()->with('error', 'Hanya usaha disetujui yang sedang nonaktif yang dapat diaktifkan kembali.');
        }

        $usaha->update(['is_active' => true, 'catatan_verifikasi' => null]);

        $pemilik = \App\Models\User::where('nik', $usaha->nik)->first();
        $pemilik?->notify(new \App\Notifications\UmkmStatusNotification($usaha, 'Aktif'));

        return redirect()->back()->with('success', 'Usaha ' . $usaha->nama_usaha . ' telah diaktifkan kembali.');
    }

    /** Verifikasi dan status UMKM hanya oleh Admin RW; Ketua RW hanya memantau. */
    private function pastikanAdminRwUmkm(): void
    {
        abort_unless(trim((string) auth()->user()?->activeRole()) === 'Admin RW', 403, 'Verifikasi UMKM hanya dapat dilakukan oleh Admin RW.');
    }

    /**
     * Halaman Persetujuan Dokumen RW â€” menampilkan daftar surat yang sudah disetujui RT.
     */
    public function persetujuanDokumen()
    {
        // Tampilkan yang perlu penerbitan RW (Disetujui RT) dan yang masih
        // Menunggu RT (Diajukan) sebagai perhatian/pantauan. Disetujui RT di atas.
        $pengajuanDb = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Disetujui RT', 'Diajukan'])
            ->orderByRaw("CASE WHEN status = 'Disetujui RT' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        // Transform data dari database agar kompatibel dengan blade
        $pengajuan = $pengajuanDb->map(function ($item) {
            $p = $item->penduduk;
            $k = $p ? $p->keluarga : null;
            $ktpPath = $item->file_ktp ?: ($p->file_ktp ?? null);
            $kkPath = $item->file_kk ?: ($p->file_kk ?? null);

            $kodeRt = '01';
            if ($k && $k->rt_id) {
                $rt = \App\Models\MasterRt::find($k->rt_id);
                if ($rt) $kodeRt = $rt->kode_rt;
            }

            return (object) [
                'id' => $item->id,
                // Nomor urut surat (diisi saat pengesahan) dan RT pemohon dipakai di modal tanda tangan.
                'nomor_urut' => $item->nomor_urut,
                'kode_rt' => $kodeRt,
                'nama_pemohon' => $p->nama_lengkap ?? 'Tidak Diketahui',
                'nik' => $p->nik ?? '-',
                'alamat' => $k->alamat ?? '-',
                'tipe_surat' => $item->tipe_surat,
                'tanggal_pengajuan' => $item->created_at->format('d M Y'),
                'status' => $item->status,
                'catatan_rt' => $item->catatan_rt,
                'catatan_rw' => $item->catatan_rw,
                'ttd_rt' => $item->ttd_rt,
                'stempel_rt' => $item->stempel_rt,
                'ttd_rw' => $item->ttd_rw,
                'stempel_rw' => $item->stempel_rw,
                'tanggal_selesai' => $item->tanggal_selesai ? $item->tanggal_selesai->format('d M Y') : null,
                'jenis_kelamin' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'tempat_tgl_lahir' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->format('d-m-Y')) : '-',
                'agama' => $p->agama ?? '-',
                'pendidikan_terakhir' => $p->pendidikan_terakhir ?? '-',
                'pendidikan' => $p->pendidikan_terakhir ?? '-',
                'pekerjaan' => $p->pekerjaan ?? '-',
                'status_perkawinan' => $p->status_perkawinan ?? '-',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => '-',
                'file_ktp' => $item->file_ktp ? basename($item->file_ktp) : ($ktpPath ? basename($ktpPath) : 'Belum diunggah'),
                'file_kk' => $item->file_kk ? basename($item->file_kk) : ($kkPath ? basename($kkPath) : 'Belum diunggah'),
                'file_ktp_size' => $ktpPath ? $this->getFileSize($ktpPath) : '-',
                'file_kk_size' => $kkPath ? $this->getFileSize($kkPath) : '-',
                'file_ktp_url' => $ktpPath ? asset('storage/' . $ktpPath) : null,
                'file_kk_url' => $kkPath ? asset('storage/' . $kkPath) : null,
                'foto' => null,
                // Data untuk template surat
                'nama_kepala_desa' => 'Budi Santoso, S.Sos.',
                'alamat_kepala_desa' => 'RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.',
                'nama_pemohon_surat' => $p->nama_lengkap ?? '-',
                'nik_surat' => $p->nik ?? '-',
                'tempat_tgl_lahir_surat' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->format('d F Y')) : '-',
                'jenis_kelamin_surat' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'pekerjaan_surat' => $p->pekerjaan ?? '-',
                'agama_surat' => $p->agama ?? '-',
                'status_perkawinan_surat' => $p->status_perkawinan ?? '-',
                'kewarganegaraan_surat' => 'Indonesia',
                'alamat_surat' => $k->alamat ?? '-',
                'no_kk' => $k->no_kk ?? '-',
                'nama_ketua_rt' => \App\Models\Pejabat::namaKetuaRt($k->rt_id ?? null) ?? '.............................',
                'nomor_surat' => '---/SKD/VIII/' . now()->format('Y'),
            ];
        });

        return view('rw.persetujuan-dokumen', compact('pengajuan'));
    }

    /**
     * Riwayat seluruh pengajuan surat (semua status) untuk RW.
     */
    public function riwayatDokumen(Request $request)
    {
        $riwayat = PengajuanSurat::with(['penduduk.keluarga.rt'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = trim($request->q);
                $q->where(function ($sub) use ($cari) {
                    $sub->where('tipe_surat', 'like', "%{$cari}%")
                        ->orWhereHas('penduduk', fn ($p) => $p->where('nama_lengkap', 'like', "%{$cari}%")->orWhere('nik', 'like', "%{$cari}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('rw.riwayat-dokumen', compact('riwayat'));
    }

    /**
     * Menyetujui dokumen (status -> Selesai).
     */
    public function approveDokumen(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::findOrFail($id);

        $dataUpdate = [
            'status' => 'Selesai',
            'catatan_rw' => $request->input('catatan', null),
            'tanggal_selesai' => now(),
        ];
        if ($request->filled('nomor_urut')) {
            $dataUpdate['nomor_urut'] = trim(mb_substr($request->input('nomor_urut'), 0, 30));
        }

        // Proses TTD & stempel RW (mode manual atau default akun).
        $dataUpdate += \App\Services\TandaTanganPengesahan::proses($request, $request->user(), 'rw');

        $pengajuan->update($dataUpdate);
        \App\Services\SuratDataBuilder::simpanSnapshot($pengajuan);

        // Notifikasi: surat terbit â†’ pemohon.
        \App\Services\NotificationService::notifikasiSuratSelesai($pengajuan->fresh());

        return redirect()->route('rw.persetujuan-dokumen')
            ->with('success', 'Dokumen berhasil disahkan dan dikirim ke pemohon.');
    }

    /**
     * Menolak dokumen (status -> Ditolak RW) beserta catatan.
     */
    public function rejectDokumen(Request $request, $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|max:1000',
        ]);

        $pengajuan = PengajuanSurat::findOrFail($id);
        $pengajuan->update([
            'status' => 'Ditolak RW',
            'catatan_rw' => $request->catatan_penolakan,
        ]);

        // Notifikasi: surat ditolak RW â†’ pemohon.
        \App\Services\NotificationService::notifikasiSuratDitolak($pengajuan->fresh(), 'RW', $request->catatan_penolakan);

        return redirect()->route('rw.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim.');
    }

    /**
     * Preview / Pratinjau Surat untuk modal iframe RW.
     */
    public function previewSurat($id)
    {
        $item = \App\Models\PengajuanSurat::with('penduduk.keluarga.rt')->findOrFail($id);

        // Data surat disusun oleh builder bersama (langsung dari DB, sesuai jenis surat).
        $data = \App\Services\SuratDataBuilder::build($item, true);

        return view('pdf.surat', compact('data'));
    }

    /**
     * Helper: Mendapatkan ukuran file dalam format yang mudah dibaca.
     */
    private function getFileSize(string $path): string
    {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) return '-';
        $bytes = filesize($fullPath);
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }
}
