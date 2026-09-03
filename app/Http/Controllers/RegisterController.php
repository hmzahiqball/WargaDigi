<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Keluarga;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }


    public function aktivasi()
    {
        return view('auth.aktivasi');
    }

 
    public function prosesAktivasi(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'string', 'size:16'],
            'no_wa' => ['required', 'string'],
        ]);

        $keluarga = Keluarga::where('no_wa', $request->no_wa)->first();

        if (!$keluarga) {
            return back()->withErrors(['nik' => 'NIK atau Nomor WhatsApp tidak terdaftar dalam data master RW. Silakan hubungi Admin RW.'])->withInput();
        }

        return redirect()->route('aktivasi.password', ['nik' => $request->nik]);
    }

    public function login()
    {
        return view('auth.login');
    }
}