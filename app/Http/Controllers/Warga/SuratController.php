<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    /**
     * Menampilkan halaman Layanan Permohonan Surat (indeks + form).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $penduduk = Penduduk::where('nik', $user->nik)->first();

        // Riwayat pengajuan surat milik penduduk ini
        $pengajuan = collect();
        if ($penduduk) {
            $pengajuan = PengajuanSurat::where('penduduk_id', $penduduk->id)
                ->orderByDesc('created_at')
                ->get();
        }

        // Daftar tipe surat yang bisa diajukan
        $tipeSurat = [
            'Surat Pengantar Domisili',
            'Surat Keterangan Usaha',
            'Surat Keterangan Tidak Mampu',
            'Surat Keterangan Pindah',
        ];

        return view('warga.permohonan-surat', compact('user', 'penduduk', 'pengajuan', 'tipeSurat'));
    }

    /**
     * Menyimpan pengajuan surat baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe_surat' => 'required|string|max:100',
            'keperluan' => 'required|string|max:1000',
            'file_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_kk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'pernyataan' => 'accepted',
        ], [
            'tipe_surat.required' => 'Silakan pilih jenis surat terlebih dahulu.',
            'keperluan.required' => 'Mohon jelaskan tujuan pengajuan surat.',
            'file_ktp.required' => 'Mohon unggah foto KTP Anda.',
            'file_ktp.mimes' => 'Format file KTP harus JPG, PNG, atau PDF.',
            'file_ktp.max' => 'Ukuran file KTP maksimal 5MB.',
            'file_kk.required' => 'Mohon unggah foto Kartu Keluarga.',
            'file_kk.mimes' => 'Format file KK harus JPG, PNG, atau PDF.',
            'file_kk.max' => 'Ukuran file KK maksimal 5MB.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ]);

        $user = $request->user();
        $penduduk = Penduduk::where('nik', $user->nik)->first();

        if (!$penduduk) {
            return back()->withErrors(['error' => 'Data penduduk tidak ditemukan. Silakan hubungi pengurus RT/RW.']);
        }

        // Simpan file KTP dan KK ke storage
        $fileKtp = $request->file('file_ktp')->store('dokumen/ktp', 'public');
        $fileKk = $request->file('file_kk')->store('dokumen/kk', 'public');

        // Simpan pengajuan ke database
        PengajuanSurat::create([
            'penduduk_id' => $penduduk->id,
            'tipe_surat' => $request->tipe_surat,
            'keterangan_tambahan' => $request->keperluan,
            'file_ktp' => $fileKtp,
            'file_kk' => $fileKk,
            'status' => 'Diajukan',
        ]);

        return redirect()->route('warga.surat.index')
            ->with('success', 'Pengajuan surat berhasil dikirim! Silakan tunggu proses verifikasi oleh pengurus RT.');
    }

    /**
     * Download surat yang sudah selesai dalam format PDF.
     */
    public function downloadPdf($id, Request $request)
    {
        $user = $request->user();
        $penduduk = Penduduk::where('nik', $user->nik)->first();

        if (!$penduduk) {
            abort(403, 'Unauthorized access.');
        }

        // Cari pengajuan, pastikan milik warga tersebut (jika rolenya Warga)
        $query = PengajuanSurat::with('penduduk.keluarga')->where('id', $id);
        
        if ($user->role == 'Warga') {
            $query->where('penduduk_id', $penduduk->id);
        }

        $item = $query->firstOrFail();

        if ($item->status !== 'Selesai') {
            abort(403, 'Surat belum selesai diproses.');
        }

        $p = $item->penduduk;
        $k = $p ? $p->keluarga : null;

        // Tentukan singkatan surat untuk nomor
        $singkatan = 'SK';
        if ($item->tipe_surat == 'Surat Pengantar Domisili') $singkatan = 'SKD';
        elseif ($item->tipe_surat == 'Surat Keterangan Usaha') $singkatan = 'SKU';
        elseif ($item->tipe_surat == 'Surat Keterangan Tidak Mampu') $singkatan = 'SKTM';
        elseif ($item->tipe_surat == 'Surat Keterangan Pindah') $singkatan = 'SKP';

        $data = (object) [
            'tipe_surat' => strtoupper($item->tipe_surat),
            'nomor_surat' => '---/' . $singkatan . '/VIII/' . $item->updated_at->format('Y'),
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
            'tanggal_selesai' => $item->tanggal_selesai ?? $item->updated_at,
            'ttd_rw' => $item->ttd_rw,
            'stempel_rw' => $item->stempel_rw,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat', compact('data'));
        return $pdf->download(str_replace(' ', '_', $item->tipe_surat) . '_' . str_replace(' ', '_', $data->nama_pemohon_surat) . '.pdf');
    }
}
