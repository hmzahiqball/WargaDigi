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
        $stats = [
            'total_warga' => ['total' => '1.248', 'change' => '+12 bulan ini', 'laki_laki' => 610, 'perempuan' => 638],
            'dokumen_menunggu' => ['total' => PengajuanSurat::where('status', 'Diajukan')->count(), 'status' => 'Perlu ditinjau segera', 'surat_domisili' => 15, 'kartu_keluarga' => 9],
            'persetujuan_keuangan' => ['total' => 5, 'status' => 'Menunggu tanda tangan RT', 'iuran_bulanan' => 3, 'laporan_kas' => 2],
        ];
        $chartData = ['labels' => ['Mei','Jun','Jul','Agu','Sep','Okt','Nov'], 'values' => [1205,1218,1220,1228,1236,1240,1248]];
        $aktivitasKeuangan = [
            ['title' => 'Laporan Iuran Keamanan Bulanan untuk Blok A diserahkan.', 'time' => '5 jam yang lalu', 'badge' => 'Disetujui', 'badge_class' => 'bg-success text-white', 'icon' => 'bi-check-circle-fill text-success', 'bg_icon' => 'bg-success bg-opacity-10'],
        ];
        $aktivitasDokumen = [
            ['title' => 'Pengajuan surat baru menunggu verifikasi.', 'time' => 'Hari ini', 'badge' => 'Menunggu', 'badge_class' => 'bg-warning bg-opacity-20 text-dark', 'icon' => 'bi-person-vcard text-primary', 'bg_icon' => 'bg-primary bg-opacity-10'],
        ];
        return view('rt.dashboard', compact('stats', 'chartData', 'aktivitasKeuangan', 'aktivitasDokumen'));
    }

    public function persetujuanDokumen()
    {
        $pengajuanDb = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Diajukan', 'Disetujui RT', 'Ditolak RT'])
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
                'jenis_kelamin' => $p ? ($p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-',
                'tempat_tgl_lahir' => $p ? ($p->tempat_lahir . ', ' . $p->tanggal_lahir->format('d-m-Y')) : '-',
                'agama' => $p->agama ?? '-',
                'pekerjaan' => $p->pekerjaan ?? '-',
                'status_perkawinan' => $p->status_perkawinan ?? '-',
                'file_ktp_url' => $ktpPath ? asset('storage/' . $ktpPath) : null,
                'file_kk_url' => $kkPath ? asset('storage/' . $kkPath) : null,
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
}