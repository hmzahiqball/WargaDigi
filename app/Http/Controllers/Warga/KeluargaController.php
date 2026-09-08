<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeluargaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;
        
        if (!$penduduk || !$penduduk->keluarga) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        $keluarga = $penduduk->keluarga;
        $anggota = $keluarga->anggota;

        return view('warga.keluarga.index', compact('keluarga', 'anggota'));
    }

    public function edit()
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;
        
        if (!$penduduk || !$penduduk->keluarga) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        $keluarga = $penduduk->keluarga;

        return view('warga.keluarga.edit', compact('keluarga'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;
        
        if (!$penduduk || !$penduduk->keluarga) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        $keluarga = $penduduk->keluarga;

        $request->validate([
            'alamat' => 'required|string|max:255',
            'no_wa' => 'nullable|string|max:20',
        ]);

        $keluarga->update([
            'alamat' => $request->alamat,
            'no_wa' => $request->no_wa,
        ]);

        return redirect()->route('warga.keluarga.index')->with('success', 'Data keluarga berhasil diperbarui.');
    }
}
