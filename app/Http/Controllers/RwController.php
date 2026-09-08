<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RwController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'penduduk' => ['total' => '12,450', 'change' => '+142 bulan ini', 'trend' => 'up'],
            'dokumen_pending' => ['total' => PengajuanSurat::where('status', 'Disetujui RT')->count(), 'need_review' => PengajuanSurat::where('status', 'Disetujui RT')->count()],
            'umkm_baru' => ['total' => 17, 'status' => 'Menunggu Verifikasi'],
            'konten_ditinjau' => ['total' => 8, 'status' => 'In progress'],
        ];
        $quickActions = [
            ['title' => 'Tinjau Dokumen', 'icon' => 'bi-file-earmark-text', 'bg' => 'icon-gray', 'link' => '#'],
            ['title' => 'Verifikasi UMKM', 'icon' => 'bi-shop', 'bg' => 'icon-green', 'link' => '#'],
            ['title' => 'Periksa Konten', 'icon' => 'bi-image', 'bg' => 'icon-red', 'link' => '#'],
        ];
        $activities = [
            ['icon' => 'bi-file-earmark-text', 'title' => 'RT 04 telah menyerahkan laporan keuangan bulanan.', 'time' => '10 menit yang lalu', 'badge' => 'PERLU PEMERIKSAAN', 'badge_class' => 'bg-danger-subtle text-danger border-danger-subtle', 'quote' => null],
        ];
        $recentDocs = [
            ['title' => 'SOP_UMKM_2023.pdf', 'desc' => 'Pedoman terbaru untuk pendaftaran bisnis lokal.', 'icon' => 'bi-file-earmark-text', 'status' => 'PUBLISHED', 'status_class' => 'bg-success-subtle text-success border-success-subtle', 'date' => 'Oct 12'],
        ];
        return view('rw.dashboard', compact('stats', 'quickActions', 'activities', 'recentDocs'));
    }

    public function persetujuanDokumen()
    {
        $pengajuanDb = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Disetujui RT', 'Ditolak RW', 'Selesai'])
            ->orderByDesc('created_at')
            ->get();

        $pengajuan = $pengajuanDb->map(function ($item) {
            $p = $item->penduduk;
            $k = $p ? $p->keluarga : null;
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
                'jenis_kelamin' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'tempat_tgl_lahir' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->format('d-m-Y')) : '-',
                'agama' => $p->agama ?? '-',
                'pekerjaan' => $p->pekerjaan ?? '-',
                'status_perkawinan' => $p->status_perkawinan ?? '-',
                'file_ktp_url' => $ktpPath ? asset('storage/' . $ktpPath) : null,
                'file_kk_url' => $kkPath ? asset('storage/' . $kkPath) : null,
                'ttd_rt_url' => $item->ttd_rt ? asset('storage/' . $item->ttd_rt) : null,
                'stempel_rt_url' => $item->stempel_rt ? asset('storage/' . $item->stempel_rt) : null,
                // Data surat untuk preview
                'nama_pemohon_surat' => $p->nama_lengkap ?? '-',
                'nik_surat' => $p->nik ?? '-',
                'tempat_tgl_lahir_surat' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->translatedFormat('d F Y')) : '-',
                'jenis_kelamin_surat' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'pekerjaan_surat' => $p->pekerjaan ?? '-',
                'agama_surat' => $p->agama ?? '-',
                'status_perkawinan_surat' => $p->status_perkawinan ?? '-',
                'alamat_surat' => $k->alamat ?? '-',
                'no_kk' => $k->no_kk ?? '-',
            ];
        });

        return view('rw.persetujuan-dokumen', compact('pengajuan'));
    }

    public function approveDokumen(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::findOrFail($id);
        
        $dataUpdate = [
            'status' => 'Selesai',
            'catatan_rw' => $request->input('catatan', null),
            'tanggal_selesai' => now(),
        ];

        if ($request->hasFile('stempel_rw')) {
            $dataUpdate['stempel_rw'] = $request->file('stempel_rw')->store('dokumen/stempel', 'public');
        }

        if ($request->filled('signature_rw')) {
            $signature = $request->input('signature_rw');
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $decoded = base64_decode($signature);
                    if ($decoded !== false) {
                        $fileName = 'dokumen/ttd/' . Str::random(40) . '.' . $type;
                        Storage::disk('public')->put($fileName, $decoded);
                        $dataUpdate['ttd_rw'] = $fileName;
                    }
                }
            }
        }

        $pengajuan->update($dataUpdate);

        return redirect()->route('rw.persetujuan-dokumen')
            ->with('success', 'Dokumen berhasil disahkan! Warga sekarang bisa mengunduh surat PDF.');
    }

    public function rejectDokumen(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        $pengajuan = PengajuanSurat::findOrFail($id);
        $pengajuan->update([
            'status' => 'Ditolak RW',
            'catatan_rw' => $request->catatan_penolakan,
        ]);
        return redirect()->route('rw.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim.');
    }

    public function previewSurat($id) { return response()->json(['success' => true]); }

    // UMKM methods
    public function umkm()
    {
        $usahaList = \App\Models\UmkmUsaha::with(['pemilik', 'kategori', 'produk'])->orderByDesc('created_at')->get();
        return view('rw.umkm', compact('usahaList'));
    }

    public function detailUsahaUmkm($id)
    {
        $usaha = \App\Models\UmkmUsaha::with(['pemilik', 'kategori', 'produk.kategori'])->findOrFail($id);
        return view('rw.umkm-detail', compact('usaha'));
    }

    public function approveUmkm(Request $request, $id)
    {
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        $usaha->update(['status_verifikasi' => 'Terverifikasi']);
        return redirect()->back()->with('success', 'UMKM berhasil diverifikasi.');
    }

    public function rejectUmkm(Request $request, $id)
    {
        $usaha = \App\Models\UmkmUsaha::findOrFail($id);
        $usaha->update(['status_verifikasi' => 'Ditolak']);
        return redirect()->back()->with('success', 'UMKM ditolak.');
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
}