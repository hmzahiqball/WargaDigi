<?php

namespace App\Http\Controllers;

use App\Models\MasterRt;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    /**
     * Manajemen Penduduk untuk Admin RW / Ketua RW: tampilan yang sama dengan RT (Profil Keluarga + Daftar Semua
     * Penduduk) untuk seluruh RW, dengan filter RT. Hanya melihat; tidak ada penambahan data.
     */
    public function index(Request $request)
    {
        $daftarRt = MasterRt::orderBy('kode_rt')->get();
        $rtId = $daftarRt->firstWhere('id', $request->get('rt_id'))?->id;

        return view('penduduk.manajemen', $this->dataManajemen($request, $rtId) + [
            'konfigurasi' => [
                'lingkup'       => $rtId ? ('RT ' . $daftarRt->firstWhere('id', $rtId)->kode_rt) : 'seluruh RW',
                'daftarRt'      => $daftarRt,
                'rtTerpilih'    => $rtId,
                'routeIndex'    => 'rw.penduduk.index',
                'routeExport'   => 'rw.penduduk.export',
                'routeKeluarga' => 'rw.keluarga.show',
                'routePenduduk' => 'rw.penduduk.show',
                'routeTambah'   => null,
                'catatanExport' => 'Export mencakup penduduk seluruh RW (atau RT yang dipilih) dan mengikuti filter pencarian aktif.',
            ],
        ]);
    }

    /**
     * Manajemen Penduduk untuk Ketua RT: hanya RT miliknya (dikunci ke user->rt_id, tidak bisa melihat RT lain).
     */
    public function indexRt(Request $request)
    {
        $rtId = $request->user()->rt_id;
        abort_if(! $rtId, 403, 'RT untuk akun Anda tidak terdefinisi.');
        $rt = MasterRt::find($rtId);

        return view('penduduk.manajemen', $this->dataManajemen($request, $rtId) + [
            'konfigurasi' => [
                'lingkup'       => $rt?->nama_rt ?? 'RT Anda',
                'daftarRt'      => null,
                'rtTerpilih'    => $rtId,
                'routeIndex'    => 'rt.penduduk.index',
                'routeExport'   => 'rt.penduduk.export',
                'routeKeluarga' => 'rt.keluarga.show',
                'routePenduduk' => 'rt.penduduk.show',
                'routeTambah'   => 'rt.keluarga.create',
                'catatanExport' => 'Export hanya mencakup penduduk di RT Anda dan mengikuti filter pencarian aktif.',
            ],
        ]);
    }

    /**
     * Data bersama Manajemen Penduduk (RT dan RW): daftar keluarga dan daftar penduduk dengan pencarian
     * masing-masing tab. $rtId null berarti seluruh RW.
     *
     * @return array{penduduk:\Illuminate\Contracts\Pagination\LengthAwarePaginator, keluarga:\Illuminate\Contracts\Pagination\LengthAwarePaginator, search:string, searchKk:string, totalPenduduk:int}
     */
    private function dataManajemen(Request $request, ?string $rtId): array
    {
        $search = trim((string) $request->get('q'));       // pencarian tab Penduduk
        $searchKk = trim((string) $request->get('q_kk'));  // pencarian tab Profil Keluarga

        $penduduk = Penduduk::with(['keluarga.rt'])
            ->leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
            ->select('penduduk.*')
            ->when($rtId, fn ($q) => $q->where('keluarga.rt_id', $rtId))
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

        $totalPenduduk = Penduduk::leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
            ->when($rtId, fn ($q) => $q->where('keluarga.rt_id', $rtId))
            ->count();

        $keluarga = \App\Models\Keluarga::with(['kepalaKeluarga', 'rt'])
            ->withCount('anggota')
            ->when($rtId, fn ($q) => $q->where('rt_id', $rtId))
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

        return compact('penduduk', 'keluarga', 'search', 'searchKk', 'totalPenduduk');
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
