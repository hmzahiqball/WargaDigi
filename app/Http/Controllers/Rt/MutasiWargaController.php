<?php

namespace App\Http\Controllers\Rt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MutasiWarga;

class MutasiWargaController extends Controller
{
    public function index()
    {
        $mutasi = MutasiWarga::with(['keluarga.kepalaKeluarga', 'penduduk'])
            ->whereHas('keluarga', fn ($query) => $query->where('rt_id', auth()->user()->rt_id))
            ->where('status', 'menunggu_rt')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rt.mutasi-warga.index', compact('mutasi'));
    }

    public function approve(Request $request, $id)
    {
        $mutasi = MutasiWarga::whereHas('keluarga', fn ($query) => $query->where('rt_id', auth()->user()->rt_id))
            ->where('id', $id)
            ->firstOrFail();
        $mutasi->status = 'menunggu_rw';
        $mutasi->save();

        return redirect()->back()->with('success', 'Mutasi warga disetujui. Menunggu persetujuan RW.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $mutasi = MutasiWarga::whereHas('keluarga', fn ($query) => $query->where('rt_id', auth()->user()->rt_id))
            ->where('id', $id)
            ->firstOrFail();
        $mutasi->status = 'ditolak';
        $mutasi->keterangan_tolak = $request->alasan_penolakan;
        $mutasi->save();

        return redirect()->back()->with('success', 'Mutasi warga ditolak.');
    }
}
