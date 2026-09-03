<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    public function index()
    {
        return view('warga.dashboard');
    }

    public function edit()
    {
        return view('warga.dashboard');
    }

    public function update(Request $request)
    {
        return back()->with('success', 'Data keluarga berhasil diperbarui.');
    }
}
