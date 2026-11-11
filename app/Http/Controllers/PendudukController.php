<?php

namespace App\Http\Controllers;

use App\Models\MasterRt;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    /**
     * Daftar penduduk untuk Admin RW / Ketua RW.
     * Mendukung pencarian (nama/NIK) dan filter RT, dengan data nyata dari database.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));
        $rtId = $request->get('rt_id');

        $query = Penduduk::with(['keluarga.rt'])
            // Join ke keluarga agar bisa mengurutkan berdasarkan no_kk.
            ->leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
            ->select('penduduk.*')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('penduduk.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('penduduk.nik', 'like', "%{$search}%");
                });
            })
            ->when($rtId, function ($q) use ($rtId) {
                $q->where('keluarga.rt_id', $rtId);
            })
            // Urut berdasarkan No. KK lalu nama (ASC).
            ->orderBy('keluarga.no_kk')
            ->orderBy('penduduk.nama_lengkap');

        $penduduk = $query->paginate(15)->withQueryString();
        $daftarRt = MasterRt::orderBy('kode_rt')->get();
        $totalPenduduk = Penduduk::count();

        return view('penduduk.index', compact('penduduk', 'daftarRt', 'search', 'rtId', 'totalPenduduk'));
    }

    /**
     * Daftar penduduk untuk Ketua RT — HANYA menampilkan penduduk di RT tsb.
     * RT dikunci ke user->rt_id (tidak bisa melihat RT lain).
     */
    public function indexRt(Request $request)
    {
        $user = $request->user();
        $rtId = $user->rt_id;
        $search = trim((string) $request->get('q'));       // pencarian tab Penduduk
        $searchKk = trim((string) $request->get('q_kk'));  // pencarian tab Profil Keluarga

        abort_if(! $rtId, 403, 'RT untuk akun Anda tidak terdefinisi.');

        $penduduk = Penduduk::with(['keluarga.rt'])
            ->leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
            ->select('penduduk.*')
            ->where('keluarga.rt_id', $rtId)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('penduduk.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('penduduk.nik', 'like', "%{$search}%");
                });
            })
            ->orderBy('keluarga.no_kk')
            ->orderBy('penduduk.nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        $rt = MasterRt::find($rtId);
        $totalPenduduk = Penduduk::leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
            ->where('keluarga.rt_id', $rtId)->count();

        // Daftar keluarga (KK) di RT ini untuk tab Profil Keluarga.
        $keluarga = \App\Models\Keluarga::with(['kepalaKeluarga'])
            ->withCount('anggota')
            ->where('rt_id', $rtId)
            ->when($searchKk !== '', function ($q) use ($searchKk) {
                $q->where(function ($sub) use ($searchKk) {
                    $sub->where('no_kk', 'like', "%{$searchKk}%")
                        ->orWhere('alamat', 'like', "%{$searchKk}%")
                        ->orWhere('alamat_domisili', 'like', "%{$searchKk}%")
                        ->orWhereHas('kepalaKeluarga', fn ($kk) => $kk->where('nama_lengkap', 'like', "%{$searchKk}%"));
                });
            })
            ->orderBy('no_kk')
            ->paginate(15, ['*'], 'kk_page')
            ->withQueryString();

        return view('rt.penduduk.index', compact('penduduk', 'rt', 'search', 'searchKk', 'totalPenduduk', 'keluarga'));
    }

    /**
     * Export data penduduk ke Excel (.xlsx) dengan kolom yang dipilih user.
     * Mengikuti filter pencarian & RT yang aktif.
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'kolom'   => ['required', 'array', 'min:1'],
            'kolom.*' => ['string', \Illuminate\Validation\Rule::in(array_keys(\App\Exports\PendudukExport::KOLOM))],
        ], [
            'kolom.required' => 'Pilih minimal satu kolom untuk diekspor.',
        ]);

        $search = trim((string) $request->get('q'));
        $rtId = $request->get('rt_id');

        $namaFile = 'data-penduduk-' . now()->format('Ymd-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PendudukExport($validated['kolom'], $search, $rtId),
            $namaFile
        );
    }

    /**
     * Export penduduk ke Excel untuk Ketua RT — HANYA RT miliknya sendiri.
     * rtId dikunci ke user->rt_id (tidak menerima rt_id dari request).
     */
    public function exportRt(Request $request)
    {
        $validated = $request->validate([
            'kolom'   => ['required', 'array', 'min:1'],
            'kolom.*' => ['string', \Illuminate\Validation\Rule::in(array_keys(\App\Exports\PendudukExport::KOLOM))],
        ], [
            'kolom.required' => 'Pilih minimal satu kolom untuk diekspor.',
        ]);

        $rtId = $request->user()->rt_id;
        abort_if(! $rtId, 403, 'RT untuk akun Anda tidak terdefinisi.');

        $search = trim((string) $request->get('q'));
        $namaFile = 'data-penduduk-rt-' . now()->format('Ymd-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PendudukExport($validated['kolom'], $search, $rtId),
            $namaFile
        );
    }

    public function show(Request $request, string $id)
    {
        $penduduk = Penduduk::with(['keluarga.rt'])->findOrFail($id);
        $user = $request->user();

        if ($user->activeRole() === 'Ketua RT' && $penduduk->keluarga?->rt_id !== $user->rt_id) {
            abort(403);
        }

        return view('penduduk.show', compact('penduduk'));
    }
}
