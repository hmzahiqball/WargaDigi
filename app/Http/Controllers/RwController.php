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
        // Dummy data untuk UI prototyping (sesuai desain Figma)
        $pengajuan = collect([
            (object) [
                'id' => 1,
                'nama_pemohon' => 'Budi Santoso',
                'nik' => '3217041111111111',
                'alamat' => 'Blok A2 / No. 14',
                'tipe_surat' => 'Surat Keterangan Domisili (SKD)',
                'tanggal_pengajuan' => '12 Okt 2024',
                'status' => 'Disetujui RT',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_tgl_lahir' => 'Bandung, 15-08-1980',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'S1',
                'pekerjaan' => 'Karyawan Swasta',
                'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => 'Sutrisno / Aminah',
                'file_ktp' => 'KTP_Budi_Santoso.pdf',
                'file_kk' => 'KK_Budi_Santoso.pdf',
                'file_ktp_size' => '2.4 MB',
                'file_kk_size' => '2.4 MB',
                'foto' => null,
                // Data untuk template surat
                'nama_kepala_desa' => 'Budi Santoso, S.Sos.',
                'alamat_kepala_desa' => 'RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.',
                'nama_pemohon_surat' => 'Rahayu Lestari',
                'tempat_tgl_lahir_surat' => 'Bantul, 6 Juli 1993',
                'jenis_kelamin_surat' => 'Perempuan',
                'pekerjaan_surat' => 'Wiraswasta',
                'agama_surat' => 'Islam',
                'status_perkawinan_surat' => 'Belum Menikah',
                'kewarganegaraan_surat' => 'Indonesia',
                'alamat_surat' => 'RT 21 RW 10, Desa Tanimulya, Ngamprah, Bandung Barat.',
                'nomor_surat' => '323/SKD/VIII/2024',
            ],
            (object) [
                'id' => 2,
                'nama_pemohon' => 'Dewi Anggraeni',
                'nik' => '3217044444444444',
                'alamat' => 'Blok D1 / No. 3',
                'tipe_surat' => 'Surat Keterangan Domisili (SKD)',
                'tanggal_pengajuan' => '13 Okt 2024',
                'status' => 'Disetujui RT',
                'jenis_kelamin' => 'Perempuan',
                'tempat_tgl_lahir' => 'Jakarta, 05-07-1988',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'S2',
                'pekerjaan' => 'Dosen',
                'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => 'Supardi / Sari',
                'file_ktp' => 'KTP_Dewi_Anggraeni.pdf',
                'file_kk' => 'KK_Dewi_Anggraeni.pdf',
                'file_ktp_size' => '1.9 MB',
                'file_kk_size' => '2.2 MB',
                'foto' => null,
                'nama_kepala_desa' => 'Budi Santoso, S.Sos.',
                'alamat_kepala_desa' => 'RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.',
                'nama_pemohon_surat' => 'Dewi Anggraeni',
                'tempat_tgl_lahir_surat' => 'Jakarta, 5 Juli 1988',
                'jenis_kelamin_surat' => 'Perempuan',
                'pekerjaan_surat' => 'Dosen',
                'agama_surat' => 'Islam',
                'status_perkawinan_surat' => 'Kawin',
                'kewarganegaraan_surat' => 'Indonesia',
                'alamat_surat' => 'RT 21 RW 10, Desa Tanimulya, Ngamprah, Bandung Barat.',
                'nomor_surat' => '324/SKD/VIII/2024',
            ],
        ]);

        return view('rw.persetujuan-dokumen', compact('pengajuan'));
    }

    /**
     * Menyetujui dokumen (status -> Selesai).
     */
    public function approveDokumen(Request $request, $id)
    {
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

        return redirect()->route('rw.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim.');
    }

    /**
     * Preview / Pratinjau Surat (JSON response untuk modal).
     */
    public function previewSurat($id)
    {
        // Untuk prototyping, return dummy template data
        return response()->json(['success' => true]);
    }
}
