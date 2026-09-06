<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

class RwController extends Controller
{
    public function dashboard()
    {
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
                'total' => 17,
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
                'link' => '#',
            ],
            [
                'title' => 'Periksa Konten',
                'icon' => 'bi-image',
                'bg' => 'icon-red',
                'link' => '#',
            ],
        ];

        $activities = [
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
        ];

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

        return view('rw.dashboard', compact('stats', 'quickActions', 'activities', 'recentDocs'));
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
