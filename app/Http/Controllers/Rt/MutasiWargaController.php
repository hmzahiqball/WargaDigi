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
            ->where('status', 'menunggu_rt')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rt.mutasi-warga.index', compact('mutasi'));
    }

    public function approve(Request $request, $id)
    {
        $mutasi = MutasiWarga::findOrFail($id);
        $mutasi->status = 'menunggu_rw';
        $mutasi->save();

        return redirect()->back()->with('success', 'Mutasi warga disetujui. Menunggu persetujuan RW.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $mutasi = MutasiWarga::findOrFail($id);
        $mutasi->status = 'ditolak';
        $mutasi->keterangan_tolak = $request->alasan_penolakan;
        $mutasi->save();

        return redirect()->back()->with('success', 'Mutasi warga ditolak.');
    }
}
