<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan Pejabat (Ketua, Sekretaris, Bendahara) oleh Superadmin (Admin Aplikasi).
 *
 * Menautkan jabatan ke data penduduk, menyimpan riwayat pergantian, dan
 * menandai pejabat yang aktif. Akun pengurus tidak diubah di sini.
 */
class PejabatController extends Controller
{
    public function index()
    {
        $rtList = MasterRt::orderBy('kode_rt')->get();

        // Semua pejabat aktif, dikunci "jabatan|rt_id" agar tabel dapat memetakan sel per unit.
        $aktifMap = Pejabat::with('penduduk')->aktif()->get()
            ->keyBy(fn ($p) => $p->jabatan . '|' . ($p->rt_id ?? ''));

        // Daftar penduduk ringkas untuk pilihan di modal: [nik, nama, rt_id].
        $penduduk = Penduduk::with('keluarga:id,rt_id')
            ->orderBy('nama_lengkap')
            ->get(['nik', 'nama_lengkap', 'keluarga_id'])
            ->map(fn ($p) => [$p->nik, $p->nama_lengkap, optional($p->keluarga)->rt_id])
            ->values();

        return view('admin.pejabat', compact('rtList', 'aktifMap', 'penduduk'));
    }

    /** Riwayat satu jabatan (+RT) dalam bentuk potongan HTML untuk modal. */
    public function riwayat(Request $request)
    {
        $data = $request->validate([
            'jabatan' => ['required', Rule::in(array_keys(Pejabat::JABATAN))],
            'rt_id'   => ['nullable', 'exists:master_rt,id'],
        ]);
        $rtId = (Pejabat::JABATAN[$data['jabatan']]['scope'] ?? null) === 'rt' ? ($data['rt_id'] ?? null) : null;

        $riwayat = Pejabat::with('penduduk')->where('jabatan', $data['jabatan'])
            ->when($rtId, fn ($q) => $q->where('rt_id', $rtId), fn ($q) => $q->whereNull('rt_id'))
            ->orderByDesc('is_active')->orderByDesc('mulai_menjabat')->orderByDesc('created_at')->get();

        $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->translatedFormat('d M Y') : '-';

        return view('admin.partials.pejabat-riwayat', compact('riwayat', 'fmt'));
    }
    /**
     * Set Ketua RW/RT — membuat baris pejabat BARU (create).
     *
     * Tanggal mulai & selesai murni dari input (tidak di-generate otomatis).
     * Baris baru langsung dijadikan aktif; pejabat aktif lama hanya diubah
     * statusnya menjadi non-aktif (tanggal selesai mereka tidak disentuh).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jabatan'          => ['required', Rule::in(array_keys(Pejabat::JABATAN))],
            'rt_id'            => ['nullable', 'required_if:jabatan,ketua_rt,sekretaris_rt,bendahara_rt', 'exists:master_rt,id'],
            'penduduk_nik'     => ['required', 'string', 'size:16', 'exists:penduduk,nik'],
            'mulai_menjabat'   => ['required', 'date'],
            'selesai_menjabat' => ['nullable', 'date', 'after_or_equal:mulai_menjabat'],
        ], [
            'rt_id.required_if'              => 'RT wajib dipilih untuk jabatan tingkat RT.',
            'penduduk_nik.required'          => 'Pilih penduduk terlebih dahulu.',
            'penduduk_nik.exists'            => 'Penduduk tidak ditemukan.',
            'mulai_menjabat.required'        => 'Tanggal mulai menjabat wajib diisi.',
            'selesai_menjabat.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        // Hanya jabatan tingkat RT yang terikat RT; RW dan DKM tidak.
        $rtId = (Pejabat::JABATAN[$validated['jabatan']]['scope'] ?? null) === 'rt' ? $validated['rt_id'] : null;

        Pejabat::buatBaru(
            $validated['jabatan'],
            $rtId,
            $validated['penduduk_nik'],
            $validated['mulai_menjabat'],
            $validated['selesai_menjabat'] ?? null,
            true
        );

        $label = Pejabat::labelJabatan($validated['jabatan']);

        return redirect()
            ->route('admin.pejabat.index')
            ->with('success', "Pejabat {$label} berhasil ditetapkan. Pejabat sebelumnya (jika ada) dipindah ke riwayat.");
    }

    /**
     * Jadikan satu baris riwayat menjadi pejabat aktif.
     *
     * Hanya mengubah flag is_active (tidak menambah data baru, tidak mengubah
     * tanggal mulai/selesai).
     */
    public function aktifkan(string $id)
    {
        $pejabat = Pejabat::aktifkanById($id);

        $label = Pejabat::labelJabatan($pejabat->jabatan);

        return redirect()
            ->route('admin.pejabat.index')
            ->with('success', "Pejabat {$label} aktif diperbarui tanpa mengubah tanggal menjabat.");
    }
}
