<?php

namespace App\Http\Controllers;

use App\Models\MasterRt;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    /**
     * Daftar penduduk untuk Admin RW / Pimpinan RW.
     * Mendukung pencarian (nama/NIK) dan filter RT, dengan data nyata dari database.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));
        $rtId = $request->get('rt_id');

        $query = Penduduk::with(['keluarga.rt'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when($rtId, function ($q) use ($rtId) {
                $q->whereHas('keluarga', fn ($k) => $k->where('rt_id', $rtId));
            })
            ->orderBy('nama_lengkap');

        $penduduk = $query->paginate(15)->withQueryString();
        $daftarRt = MasterRt::orderBy('kode_rt')->get();
        $totalPenduduk = Penduduk::count();

        return view('penduduk.index', compact('penduduk', 'daftarRt', 'search', 'rtId', 'totalPenduduk'));
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
