<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

class RtController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_warga' => [
                'total' => '1.248',
                'change' => '+12 bulan ini',
                'laki_laki' => 610,
                'perempuan' => 638,
            ],
            'dokumen_menunggu' => [
                'total' => 24,
                'status' => 'Perlu ditinjau segera',
                'surat_domisili' => 15,
                'kartu_keluarga' => 9,
            ],
            'persetujuan_keuangan' => [
                'total' => 5,
                'status' => 'Menunggu tanda tangan RT',
                'iuran_bulanan' => 3,
                'laporan_kas' => 2,
            ],
        ];

        $chartData = [
            'labels' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov'],
            'values' => [1205, 1218, 1220, 1228, 1236, 1240, 1248],
        ];

        $aktivitasKeuangan = [
            [
                'title' => 'Laporan Iuran Keamanan Bulanan untuk Blok A diserahkan.',
                'time' => '5 jam yang lalu',
                'badge' => 'Disetujui',
                'badge_class' => 'bg-success text-white',
                'icon' => 'bi-check-circle-fill text-success',
                'bg_icon' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => 'Pengajuan Dana Perbaikan Jalan Blok C.',
                'time' => 'Kemarin',
                'badge' => 'Menunggu',
                'badge_class' => 'bg-warning bg-opacity-20 text-dark',
                'icon' => 'bi-three-dots text-warning',
                'bg_icon' => 'bg-warning bg-opacity-10',
            ],
        ];

        $aktivitasDokumen = [
            [
                'title' => 'Budi Santoso mengajukan Surat Keterangan Domisili.',
                'time' => '2 jam yang lalu',
                'badge' => 'Menunggu',
                'badge_class' => 'bg-warning bg-opacity-20 text-dark',
                'icon' => 'bi-person-vcard text-primary',
                'bg_icon' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => 'Keluarga Ahmad memperbarui Kartu Keluarga.',
                'time' => '1 hari yang lalu',
                'badge' => 'Selesai',
                'badge_class' => 'bg-success text-white',
                'icon' => 'bi-file-earmark-text text-success',
                'bg_icon' => 'bg-success bg-opacity-10',
            ],
        ];

        return view('rt.dashboard', compact('stats', 'chartData', 'aktivitasKeuangan', 'aktivitasDokumen'));
    }

    /**
     * Halaman Persetujuan Dokumen — menampilkan daftar pengajuan surat.
     */
    public function persetujuanDokumen()
    {
        // Ambil data pengajuan dari database, eager load penduduk + keluarga
        $pengajuanDb = PengajuanSurat::with(['penduduk.keluarga'])
            ->whereIn('status', ['Diajukan', 'Disetujui RT', 'Ditolak RT'])
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
            ];
        });

        return view('rt.persetujuan-dokumen', compact('pengajuan'));
    }

    /**
     * Menyetujui dokumen (status -> Disetujui RT).
     */
    public function approveDokumen(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::findOrFail($id);
        $pengajuan->update([
            'status' => 'Disetujui RT',
            'catatan_rt' => $request->input('catatan', null),
            'tanggal_disetujui_rt' => now(),
        ]);

        return redirect()->route('rt.persetujuan-dokumen')
            ->with('success', 'Dokumen berhasil disetujui dan diteruskan ke RW.');
    }

    /**
     * Menolak dokumen (status -> Ditolak RT) beserta catatan.
     */
    public function rejectDokumen(Request $request, $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|max:1000',
        ]);

        $pengajuan = PengajuanSurat::findOrFail($id);
        $pengajuan->update([
            'status' => 'Ditolak RT',
            'catatan_rt' => $request->catatan_penolakan,
        ]);

        return redirect()->route('rt.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim ke pemohon.');
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
