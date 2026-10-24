<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan Pejabat (Ketua RW & Ketua RT) oleh Superadmin (Admin Aplikasi).
 *
 * Menautkan jabatan ke data penduduk, menyimpan riwayat pergantian, dan
 * menandai pejabat yang aktif. Akun pengurus tidak diubah di sini.
 */
class PejabatController extends Controller
{
    public function index()
    {
        $rtList = MasterRt::orderBy('kode_rt')->get();

        // Pejabat aktif per jabatan.
        $ketuaRwAktif = Pejabat::with('penduduk')->aktif()->where('jabatan', 'ketua_rw')->first();

        $ketuaRtAktif = Pejabat::with('penduduk')->aktif()
            ->where('jabatan', 'ketua_rt')
            ->get()
            ->keyBy('rt_id');

        // Riwayat (semua periode), diurutkan terbaru dulu.
        $riwayatRw = Pejabat::with('penduduk')
            ->where('jabatan', 'ketua_rw')
            ->orderByDesc('is_active')
            ->orderByDesc('mulai_menjabat')
            ->orderByDesc('created_at')
            ->get();

        $riwayatRt = Pejabat::with(['penduduk', 'rt'])
            ->where('jabatan', 'ketua_rt')
            ->orderByDesc('is_active')
            ->orderByDesc('mulai_menjabat')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('rt_id');

        // Daftar penduduk untuk dropdown (nama + nik). Untuk RT, dikelompokkan per RT.
        $semuaPenduduk = Penduduk::orderBy('nama_lengkap')->get(['nik', 'nama_lengkap', 'keluarga_id']);
        $pendudukPerRt = Penduduk::with('keluarga:id,rt_id')
            ->orderBy('nama_lengkap')
            ->get(['nik', 'nama_lengkap', 'keluarga_id'])
            ->groupBy(fn ($p) => optional($p->keluarga)->rt_id);

        return view('admin.pejabat', compact(
            'rtList',
            'ketuaRwAktif',
            'ketuaRtAktif',
            'riwayatRw',
            'riwayatRt',
            'semuaPenduduk',
            'pendudukPerRt'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jabatan'      => ['required', Rule::in(['ketua_rw', 'ketua_rt'])],
            'rt_id'        => ['nullable', 'required_if:jabatan,ketua_rt', 'exists:master_rt,id'],
            'penduduk_nik' => ['required', 'string', 'size:16', 'exists:penduduk,nik'],
            'mulai_menjabat' => ['nullable', 'date'],
        ], [
            'rt_id.required_if'  => 'RT wajib dipilih untuk jabatan Ketua RT.',
            'penduduk_nik.exists' => 'Penduduk tidak ditemukan.',
        ]);

        $rtId = $validated['jabatan'] === 'ketua_rw' ? null : $validated['rt_id'];

        Pejabat::setAktif(
            $validated['jabatan'],
            $rtId,
            $validated['penduduk_nik'],
            $validated['mulai_menjabat'] ?? null
        );

        $label = $validated['jabatan'] === 'ketua_rw' ? 'Ketua RW' : 'Ketua RT';

        return redirect()
            ->route('admin.pejabat.index')
            ->with('success', "Pejabat {$label} berhasil ditetapkan. Pejabat sebelumnya (jika ada) dipindah ke riwayat.");
    }
}
