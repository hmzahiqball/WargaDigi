<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index()
    {
        return view('warga.dashboard');
    }

    public function create()
    {
        return view('warga.dashboard');
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Permohonan surat berhasil dikirim.');
    }
}
