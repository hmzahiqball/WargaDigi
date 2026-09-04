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
        // Dummy data untuk UI prototyping (sesuai desain Figma)
        $pengajuan = collect([
            (object) [
                'id' => 1,
                'nama_pemohon' => 'Budi Santoso',
                'nik' => '3217041111111111',
                'alamat' => 'Blok A2 / No. 14',
                'tipe_surat' => 'Surat Keterangan Domisili (SKD)',
                'tanggal_pengajuan' => '12 Okt 2024',
                'status' => 'Diajukan',
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
            ],
            (object) [
                'id' => 2,
                'nama_pemohon' => 'Siti Rahayu',
                'nik' => '3217042222222222',
                'alamat' => 'Blok B3 / No. 7',
                'tipe_surat' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'tanggal_pengajuan' => '13 Okt 2024',
                'status' => 'Diajukan',
                'jenis_kelamin' => 'Perempuan',
                'tempat_tgl_lahir' => 'Bandung, 22-03-1992',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'SMA',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => 'Ahmad / Fatimah',
                'file_ktp' => 'KTP_Siti_Rahayu.pdf',
                'file_kk' => 'KK_Siti_Rahayu.pdf',
                'file_ktp_size' => '1.8 MB',
                'file_kk_size' => '2.1 MB',
                'foto' => null,
            ],
            (object) [
                'id' => 3,
                'nama_pemohon' => 'Ahmad Fauzi',
                'nik' => '3217043333333333',
                'alamat' => 'Blok C1 / No. 22',
                'tipe_surat' => 'Surat Keterangan Domisili (SKD)',
                'tanggal_pengajuan' => '14 Okt 2024',
                'status' => 'Diajukan',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_tgl_lahir' => 'Cimahi, 10-12-1975',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'D3',
                'pekerjaan' => 'Wiraswasta',
                'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
                'nama_orang_tua' => 'Hasan / Maryam',
                'file_ktp' => 'KTP_Ahmad_Fauzi.pdf',
                'file_kk' => 'KK_Ahmad_Fauzi.pdf',
                'file_ktp_size' => '2.0 MB',
                'file_kk_size' => '1.9 MB',
                'foto' => null,
            ],
        ]);

        return view('rt.persetujuan-dokumen', compact('pengajuan'));
    }

    /**
     * Menyetujui dokumen (status -> Disetujui RT).
     */
    public function approveDokumen(Request $request, $id)
    {
        // Untuk saat ini menggunakan dummy response (tanpa database)
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

        // Untuk saat ini menggunakan dummy response (tanpa database)
        return redirect()->route('rt.persetujuan-dokumen')
            ->with('success', 'Catatan penolakan telah dikirim ke pemohon.');
    }
}
