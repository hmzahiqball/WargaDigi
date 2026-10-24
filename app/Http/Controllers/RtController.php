<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RtController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $rtId = $user ? $user->rt_id : null;
        if (!$rtId && $user) {
            $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first();
            if ($penduduk && $penduduk->keluarga) {
                $rtId = $penduduk->keluarga->rt_id;
            }
            if (!$rtId) {
                $rt01 = \App\Models\MasterRt::where('kode_rt', '01')->first() ?? \App\Models\MasterRt::first();
                $rtId = $rt01 ? $rt01->id : null;
            }
        }

        // Hitung laporan keuangan RT berstatus Submitted yang menunggu persetujuan
        $pendingLaporanQuery = \App\Models\LaporanKeuangan::where('unit', 'RT')
            ->where('status', 'Submitted');
        if ($rtId) {
            $pendingLaporanQuery->where(function ($q) use ($rtId) {
                $q->where('rt_id', $rtId)->orWhereNull('rt_id');
            });
        }
        $totalLaporanPending = $pendingLaporanQuery->count();

        // Hitung tagihan/iuran aktif untuk RT
        $totalTagihanRt = \App\Models\Tagihan::where('unit', 'RT')
            ->when($rtId, fn($q) => $q->where('rt_id', $rtId))
            ->count();

        $stats = [
            'total_warga' => [
                'total' => \App\Models\Penduduk::count(),
                'change' => 'Warga terdaftar',
                'laki_laki' => \App\Models\Penduduk::where('jenis_kelamin', 'L')->count(),
                'perempuan' => \App\Models\Penduduk::where('jenis_kelamin', 'P')->count(),
            ],
            'dokumen_menunggu' => [
                'total' => PengajuanSurat::where('status', 'Diajukan')->count(),
                'status' => 'Perlu ditinjau segera',
                'surat_domisili' => PengajuanSurat::where('status', 'Diajukan')->where('tipe_surat', 'like', '%Domisili%')->count(),
                'kartu_keluarga' => PengajuanSurat::where('status', 'Diajukan')->where('tipe_surat', 'like', '%Keluarga%')->count(),
            ],
            'persetujuan_keuangan' => [
                'total' => $totalLaporanPending,
                'status' => $totalLaporanPending > 0 ? $totalLaporanPending . ' laporan menunggu persetujuan RT' : 'Semua laporan telah ditinjau',
                'iuran_bulanan' => $totalTagihanRt,
                'laporan_kas' => $totalLaporanPending,
            ],
        ];

        // Tren saldo 6 bulan terakhir dari transaksi keuangan unit RT (rt_id ketua).
        // Struktur dipertahankan: ['labels' => [...], 'values' => [...]].
        $chartLabels = [];
        $chartValues = [];
        $saldoBerjalan = 0;

        // Saldo awal sebelum jendela 6 bulan (akumulasi transaksi RT ter-Verified).
        $cursor6 = now()->startOfMonth()->subMonths(5);
        if ($rtId) {
            $saldoBerjalan = (int) \App\Models\TransaksiKeuangan::where('status', 'Verified')
                ->where('unit_sumber', 'RT')
                ->where('rt_id', $rtId)
                ->whereDate('tanggal', '<', $cursor6)
                ->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE -jumlah END), 0) as saldo")
                ->value('saldo');
        }

        for ($m = 0; $m < 6; $m++) {
            $bulan = now()->startOfMonth()->subMonths(5 - $m);
            $chartLabels[] = $bulan->translatedFormat('M');

            $netBulan = 0;
            if ($rtId) {
                $netBulan = (int) \App\Models\TransaksiKeuangan::where('status', 'Verified')
                    ->where('unit_sumber', 'RT')
                    ->where('rt_id', $rtId)
                    ->whereYear('tanggal', $bulan->year)
                    ->whereMonth('tanggal', $bulan->month)
                    ->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE -jumlah END), 0) as net")
                    ->value('net');
            }

            $saldoBerjalan += $netBulan;
            $chartValues[] = $saldoBerjalan;
        }

        $chartData = ['labels' => $chartLabels, 'values' => $chartValues];

        // Ambil aktivitas laporan keuangan riil
        $latestLaporan = \App\Models\LaporanKeuangan::where('unit', 'RT')
            ->when($rtId, fn($q) => $q->where(fn($sub) => $sub->where('rt_id', $rtId)->orWhereNull('rt_id')))
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        $aktivitasKeuangan = [];
        if ($latestLaporan->isNotEmpty()) {
            foreach ($latestLaporan as $item) {
                $badgeClass = match ($item->status) {
                    'Approved' => 'bg-success text-white',
                    'Rejected' => 'bg-danger text-white',
                    'Submitted' => 'bg-warning text-dark',
                    default => 'bg-secondary text-white',
                };
                $icon = match ($item->status) {
                    'Approved' => 'bi-check-circle-fill text-success',
                    'Rejected' => 'bi-x-circle-fill text-danger',
                    default => 'bi-hourglass-split text-warning',
                };
                $bgIcon = match ($item->status) {
                    'Approved' => 'bg-success bg-opacity-10',
                    'Rejected' => 'bg-danger bg-opacity-10',
                    default => 'bg-warning bg-opacity-10',
                };

                $aktivitasKeuangan[] = [
                    'title' => $item->judul,
                    'time' => $item->updated_at ? $item->updated_at->diffForHumans() : 'Baru saja',
                    'badge' => $item->status,
                    'badge_class' => $badgeClass,
                    'icon' => $icon,
                    'bg_icon' => $bgIcon,
                ];
            }
        } else {
            $aktivitasKeuangan = [
                [
                    'title' => 'Belum ada aktivitas laporan keuangan RT.',
                    'time' => '-',
                    'badge' => 'Nihil',
                    'badge_class' => 'bg-light text-muted',
                    'icon' => 'bi-wallet2 text-muted',
                    'bg_icon' => 'bg-light',
                ],
            ];
        }

        // Aktivitas dokumen terbaru RT (status Diajukan / Disetujui RT).
        $latestPengajuan = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Diajukan', 'Disetujui RT'])
            ->when($rtId, function ($q) use ($rtId) {
                $q->whereHas('penduduk.keluarga', fn ($sub) => $sub->where('rt_id', $rtId));
            })
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $aktivitasDokumen = [];
        foreach ($latestPengajuan as $surat) {
            $isDiajukan = $surat->status === 'Diajukan';
            $nama = $surat->penduduk->nama_lengkap ?? 'Pemohon';

            $aktivitasDokumen[] = [
                'title' => $surat->tipe_surat . ' - ' . $nama,
                'time' => $surat->created_at ? $surat->created_at->diffForHumans() : '-',
                'badge' => $isDiajukan ? 'Menunggu' : 'Disetujui RT',
                'badge_class' => $isDiajukan ? 'bg-warning bg-opacity-20 text-dark' : 'bg-success text-white',
                'icon' => $isDiajukan ? 'bi-person-vcard text-primary' : 'bi-check-circle-fill text-success',
                'bg_icon' => $isDiajukan ? 'bg-primary bg-opacity-10' : 'bg-success bg-opacity-10',
            ];
        }

        return view('rt.dashboard', compact('stats', 'chartData', 'aktivitasKeuangan', 'aktivitasDokumen'));
    }

    public function persetujuanDokumen()
    {
        $pengajuanDb = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Diajukan', 'Disetujui RT', 'Selesai', 'Ditolak RT', 'Ditolak RW'])
            ->orderByDesc('created_at')
            ->get();

        $pengajuan = $pengajuanDb->map(function ($item) {
            $p = $item->penduduk;
            $k = $p ? $p->keluarga : null;

            // Prioritas: file dari pengajuan > file dari penduduk
            $ktpPath = $item->file_ktp ?: ($p->file_ktp ?? null);
            $kkPath = $item->file_kk ?: ($p->file_kk ?? null);

            return (object) [
                'id' => $item->id,
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
                'pekerjaan' => $p->pekerjaan ?? '-',
                'status_perkawinan' => $p->status_perkawinan ?? '-',
                'file_ktp_url' => $ktpPath ? asset('storage/' . $ktpPath) : null,
                'file_kk_url' => $kkPath ? asset('storage/' . $kkPath) : null,
                'pendidikan' => $p->pendidikan ?? '-',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => '-',
                'kode_rt' => $k && $k->rt_id ? (\App\Models\MasterRt::find($k->rt_id)->kode_rt ?? '01') : '01',
            ];
        });

        return view('rt.persetujuan-dokumen', compact('pengajuan'));
    }

    public function approveDokumen(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::findOrFail($id);

        $dataUpdate = [
            'status' => 'Disetujui RT',
            'catatan_rt' => $request->input('catatan', null),
            'tanggal_disetujui_rt' => now(),
        ];

        // Proses stempel RT (file upload, opsional)
        if ($request->hasFile('stempel_rt')) {
            $dataUpdate['stempel_rt'] = $request->file('stempel_rt')->store('dokumen/stempel', 'public');
        }

        // Proses tanda tangan RT (base64 dari signature pad)
        if ($request->filled('signature_rt')) {
            $signature = $request->input('signature_rt');
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $decoded = base64_decode($signature);
                    if ($decoded !== false) {
                        $fileName = 'dokumen/ttd/' . Str::random(40) . '.' . $type;
                        Storage::disk('public')->put($fileName, $decoded);
                        $dataUpdate['ttd_rt'] = $fileName;
                    }
                }
            }
        }

        $pengajuan->update($dataUpdate);

        return redirect()->route('rt.persetujuan-dokumen')
            ->with('success', 'Dokumen berhasil disetujui dan diteruskan ke RW.');
    }

    public function rejectDokumen(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        $pengajuan = PengajuanSurat::findOrFail($id);
        $pengajuan->update([
            'status' => 'Ditolak RT',
            'catatan_rt' => $request->catatan_penolakan,
        ]);
        return redirect()->route('rt.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim ke pemohon.');
    }

    private function getFileSize(string $path): string
    {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) return '-';
        $bytes = filesize($fullPath);
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }

    public function previewSurat($id)
    {
        $item = \App\Models\PengajuanSurat::with('penduduk.keluarga')->findOrFail($id);
        $p = $item->penduduk;
        $k = $p ? $p->keluarga : null;

        $kodeRt = '01';
        if ($k && $k->rt_id) {
            $rt = \App\Models\MasterRt::find($k->rt_id);
            if ($rt) $kodeRt = $rt->kode_rt;
        }

        // Kode RT mengikuti RT pemohon; bila kosong pakai RT petugas yang login.
        $loggedInRt = auth()->user();
        $rtIdSurat = $k->rt_id ?? ($loggedInRt->rt_id ?? null);
        if ($rtIdSurat) {
            $rtSurat = \App\Models\MasterRt::find($rtIdSurat);
            if ($rtSurat) $kodeRt = $rtSurat->kode_rt;
        }

        // Nama pejabat diambil dari data Pejabat aktif (bukan username akun).
        $namaKetuaRt = \App\Models\Pejabat::namaKetuaRt($rtIdSurat) ?? '..........................';
        $namaKetuaRw = \App\Models\Pejabat::namaKetuaRw() ?? '..........................';

        $tanggalSelesai = $item->updated_at;
        $bulanRomawi = \App\Models\PengajuanSurat::BULAN_ROMAWI[$tanggalSelesai->format('n')] ?? 'IX';

        $data = (object) [
            'tipe_surat' => $item->tipe_surat,
            'kode_rt' => $kodeRt,
            'bulan_romawi' => $bulanRomawi,
            'tahun' => $tanggalSelesai->format('y'),
            'nama_lengkap' => $p->nama_lengkap ?? '-',
            'tempat_tgl_lahir' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->locale('id')->translatedFormat('j F Y')) : '-',
            'alamat' => $k->alamat ?? '-',
            'no_kk' => $k->no_kk ?? '-',
            'nik' => $p->nik ?? '-',
            'jenis_kelamin' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
            'agama' => $p->agama ?? '-',
            'status_perkawinan' => $p->status_perkawinan ?? '-',
            'status_hubungan_keluarga' => $p->status_hubungan_keluarga ?? '-',
            'pekerjaan' => $p->pekerjaan ?? '-',
            'keterangan_tambahan' => $item->keterangan_tambahan ?? '',
            'tanggal_surat' => $tanggalSelesai->locale('id')->translatedFormat('j F Y'),
            'nama_ketua_rt' => $namaKetuaRt,
            'nama_ketua_rw' => $namaKetuaRw,
            'ttd_rt' => $item->ttd_rt,
            'stempel_rt' => $item->stempel_rt,
            'ttd_rw' => $item->ttd_rw,
            'stempel_rw' => $item->stempel_rw,
            'is_preview' => true,
        ];

        return view('pdf.surat', compact('data'));
    }
}
