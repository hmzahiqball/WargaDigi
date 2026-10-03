<?php

namespace App\Http\Controllers;

use App\Models\MutasiWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MutasiDocumentController extends Controller
{
    public function show(Request $request, string $id)
    {
        $mutasi = MutasiWarga::with('keluarga')->findOrFail($id);
        $user = $request->user();

        if ($user->role === 'Ketua RT' && $mutasi->keluarga?->rt_id !== $user->rt_id) {
            abort(403);
        }

        if (! $mutasi->file_bukti || ! Storage::disk('public')->exists($mutasi->file_bukti)) {
            abort(404, 'Dokumen pengajuan tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($mutasi->file_bukti));
    }
}
