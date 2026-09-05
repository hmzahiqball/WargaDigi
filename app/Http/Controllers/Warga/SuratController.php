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
}
