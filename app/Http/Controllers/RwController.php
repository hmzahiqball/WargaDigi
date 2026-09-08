<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UmkmUsaha;
use App\Models\UmkmProduk;
use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;

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

    public function umkm(Request $request)
    {
        $search = trim((string)($request->get('q') ?? $request->get('q_terdaftar') ?? $request->get('search')));
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        // Otomatis tetap di tab 'terdaftar' jika user melakukan pencarian atau filter
        $tab = $request->get('tab');
        if (!$tab) {
            if ($request->has('terdaftar_page') || $request->has('q') || $request->has('kategori') || $request->has('status') || $request->has('q_terdaftar') || $request->has('search')) {
                $tab = 'terdaftar';
            } elseif ($request->has('approval_page')) {
                $tab = 'persetujuan';
            } else {
                $tab = 'persetujuan';
            }
        }

        $isPimpinan = in_array(Auth::user()->role ?? '', ['Pimpinan RW', 'Pimpinan']);

        $pendingUsaha = UmkmUsaha::where('status_verifikasi', 'Pending')
            ->with(['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm'])
            ->latest()
            ->paginate(4, ['*'], 'approval_page')
            ->withQueryString();
        $pendingUsaha->appends(['tab' => 'persetujuan']);

        // Data untuk Pimpinan RW (History yang sedang pending dan sudah diapprove) - 4 per pagination
        $historyUsaha = UmkmUsaha::whereIn('status_verifikasi', ['Pending', 'Approved'])
            ->with(['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm'])
            ->latest()
            ->paginate(4, ['*'], 'approval_page')
            ->withQueryString();
        $historyUsaha->appends(['tab' => 'persetujuan']);

        $daftarKategoriUmkm = KategoriUmkm::all();

        $queryTerdaftar = UmkmUsaha::where('status_verifikasi', 'Approved')
            ->with(['pemilik.penduduk.keluarga.rt', 'user.penduduk.keluarga.rt', 'kategori_umkm', 'produk']);

        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (!empty($words)) {
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

        if ($kategori && !in_array($kategori, ['Semua', 'Semua Kategori'])) {
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
        } elseif ($status === 'Stok Tersedia') {
            $queryTerdaftar->whereHas('produk', function ($q) {
                $q->where('status_stok', 'tersedia');
            });
        } elseif ($status === 'Stok Menipis') {
            $queryTerdaftar->whereHas('produk', function ($q) {
                $q->where('status_stok', 'menipis');
            });
        } elseif ($status === 'Stok Habis') {
            $queryTerdaftar->whereHas('produk', function ($q) {
                $q->where('status_stok', 'habis');
            });
        }

        // Usaha Terdaftar - 6 per pagination
        $daftarUsahaTerdaftar = $queryTerdaftar->latest()
            ->paginate(6, ['*'], 'terdaftar_page')
            ->withQueryString();
        $daftarUsahaTerdaftar->appends(['tab' => 'terdaftar']);

        return view('rw.umkm.umkm', compact(
            'pendingUsaha', 
            'historyUsaha', 
            'daftarUsahaTerdaftar', 
            'daftarKategoriUmkm',
            'tab', 
            'isPimpinan', 
            'search',
            'kategori',
            'status'
        ));
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
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        $usaha->update([
            'status_verifikasi' => 'Approved',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Profil UMKM ' . $usaha->nama_usaha . ' berhasil disetujui.');
    }

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

    /**
     * Halaman Persetujuan Dokumen RW — menampilkan daftar surat yang sudah disetujui RT.
     */
    public function persetujuanDokumen()
    {
        // Ambil data pengajuan yang sudah disetujui RT dari database
        $pengajuanDb = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Disetujui RT', 'Ditolak RW', 'Selesai'])
            ->orderByDesc('created_at')
            ->get();

        // Transform data dari database agar kompatibel dengan blade yang sudah ada
        $pengajuan = $pengajuanDb->map(function ($item) {
            $p = $item->penduduk;
            $k = $p ? $p->keluarga : null;
            return (object) [
                'id' => $item->id,
                'nama_pemohon' => $p->nama_lengkap ?? 'Tidak Diketahui',
                'nik' => $p->nik ?? '-',
                'alamat' => $k->alamat ?? '-',
                'tipe_surat' => $item->tipe_surat,
                'tanggal_pengajuan' => $item->created_at->format('d M Y'),
                'status' => $item->status,
                'jenis_kelamin' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'tempat_tgl_lahir' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->format('d-m-Y')) : '-',
                'agama' => $p->agama ?? '-',
                'pendidikan_terakhir' => '-',
                'pekerjaan' => $p->pekerjaan ?? '-',
                'status_perkawinan' => $p->status_perkawinan ?? '-',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => '-',
                'file_ktp' => $item->file_ktp ? basename($item->file_ktp) : 'Belum diunggah',
                'file_kk' => $item->file_kk ? basename($item->file_kk) : 'Belum diunggah',
                'file_ktp_size' => $item->file_ktp ? $this->getFileSize($item->file_ktp) : '-',
                'file_kk_size' => $item->file_kk ? $this->getFileSize($item->file_kk) : '-',
                'file_ktp_url' => $item->file_ktp ? asset('storage/' . $item->file_ktp) : null,
                'file_kk_url' => $item->file_kk ? asset('storage/' . $item->file_kk) : null,
                'foto' => null,
                // Data untuk template surat
                'nama_kepala_desa' => 'Budi Santoso, S.Sos.',
                'alamat_kepala_desa' => 'RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.',
                'nama_pemohon_surat' => $p->nama_lengkap ?? '-',
                'tempat_tgl_lahir_surat' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->format('d F Y')) : '-',
                'jenis_kelamin_surat' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'pekerjaan_surat' => $p->pekerjaan ?? '-',
                'agama_surat' => $p->agama ?? '-',
                'status_perkawinan_surat' => $p->status_perkawinan ?? '-',
                'kewarganegaraan_surat' => 'Indonesia',
                'alamat_surat' => $k->alamat ?? '-',
                'nomor_surat' => '---/SKD/VIII/' . now()->format('Y'),
            ];
        });

        return view('rw.persetujuan-dokumen', compact('pengajuan'));
    }

    /**
     * Menyetujui dokumen (status -> Selesai).
     */
    public function approveDokumen(Request $request, $id)
    {
        \Illuminate\Support\Facades\Log::info('Approve RW Request:', $request->all());
        
        $pengajuan = PengajuanSurat::findOrFail($id);
        
        $dataUpdate = [
            'status' => 'Selesai',
            'catatan_rw' => $request->input('catatan', null),
            'tanggal_selesai' => now(),
        ];

        // Proses stempel (file upload)
        if ($request->hasFile('stempel')) {
            $dataUpdate['stempel_rw'] = $request->file('stempel')->store('dokumen/stempel', 'public');
        }

        // Proses tanda tangan (base64)
        if ($request->filled('signature')) {
            $signature = $request->input('signature');
            // Format base64: data:image/png;base64,iVBORw0KGgo...
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $signature = base64_decode($signature);
                    
                    if ($signature !== false) {
                        $fileName = 'dokumen/ttd/' . \Illuminate\Support\Str::random(40) . '.' . $type;
                        \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $signature);
                        $dataUpdate['ttd_rw'] = $fileName;
                    }
                }
            }
        }

        $pengajuan->update($dataUpdate);

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

        return redirect()->route('rw.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim.');
    }

    /**
     * Preview / Pratinjau Surat (JSON response untuk modal).
     */
    public function previewSurat($id)
    {
        return response()->json(['success' => true]);
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
