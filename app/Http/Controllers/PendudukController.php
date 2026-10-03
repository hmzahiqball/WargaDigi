<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    public function show(Request $request, string $id)
    {
        $penduduk = Penduduk::with(['keluarga.rt'])->findOrFail($id);
        $user = $request->user();

        if ($user->role === 'Ketua RT' && $penduduk->keluarga?->rt_id !== $user->rt_id) {
            abort(403);
        }

        return view('penduduk.show', compact('penduduk'));
    }
}
