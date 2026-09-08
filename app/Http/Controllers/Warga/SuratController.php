<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
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
        $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();

        // Riwayat pengajuan surat milik penduduk ini
        $pengajuan = collect();
        if ($penduduk) {
            $pengajuan = PengajuanSurat::where('penduduk_id', $penduduk->id)
                ->orderByDesc('created_at')
                ->get();
        }

        // Daftar tipe surat dari model (9 kategori sesuai format RW 21)
        $tipeSurat = PengajuanSurat::TIPE_SURAT;

        // URL KTP/KK dari data penduduk (untuk preview otomatis)
        $ktpUrl = null;
        $kkUrl = null;
        if ($penduduk) {
            if ($penduduk->file_ktp) {
                $ktpUrl = asset('storage/' . $penduduk->file_ktp);
            }
            if ($penduduk->file_kk) {
                $kkUrl = asset('storage/' . $penduduk->file_kk);
            }
        }

        return view('warga.permohonan-surat', compact('user', 'penduduk', 'pengajuan', 'tipeSurat', 'ktpUrl', 'kkUrl'));
    }

    /**
     * Menyimpan pengajuan surat baru ke database.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();

        if (!$penduduk) {
            return back()->withErrors(['error' => 'Data penduduk tidak ditemukan. Silakan hubungi pengurus RT/RW.']);
        }

        $request->validate([
            'tipe_surat' => 'required|string|max:100',
            'keperluan' => 'nullable|string|max:1000',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'pernyataan' => 'accepted',
        ], [
            'tipe_surat.required' => 'Silakan pilih jenis surat terlebih dahulu.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ]);

        // Tentukan file KTP: upload baru > data penduduk
        $fileKtp = $penduduk->file_ktp;
        if ($request->hasFile('file_ktp')) {
            $fileKtp = $request->file('file_ktp')->store('dokumen/ktp', 'public');
            // Update juga di penduduk supaya tersimpan permanen
            $penduduk->update(['file_ktp' => $fileKtp]);
        }

        // Tentukan file KK: upload baru > data penduduk
        $fileKk = $penduduk->file_kk;
        if ($request->hasFile('file_kk')) {
            $fileKk = $request->file('file_kk')->store('dokumen/kk', 'public');
            $penduduk->update(['file_kk' => $fileKk]);
        }

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

        // Ambil kode RT dari keluarga
        $kodeRt = '01';
        if ($k && $k->rt_id) {
            $rt = \App\Models\MasterRt::find($k->rt_id);
            if ($rt) $kodeRt = $rt->kode_rt;
        }

        // Nama Ketua RT dan RW
        $namaKetuaRt = User::where('role', 'Ketua RT')->first()->username ?? '..........................';
        $namaKetuaRw = User::where('role', 'Pimpinan RW')->first()->username ?? '..........................';

        // Bulan Romawi
        $tanggalSelesai = $item->tanggal_selesai ?? $item->updated_at;
        $bulanRomawi = PengajuanSurat::BULAN_ROMAWI[$tanggalSelesai->format('n')] ?? 'IX';

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
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat', compact('data'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Surat_Pengantar_' . str_replace(' ', '_', $data->nama_lengkap) . '.pdf');
    }
}

