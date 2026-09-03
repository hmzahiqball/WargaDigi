<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['nik' => $request->nik, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            if (in_array($user->role, ['Admin Aplikasi'])) {
                return redirect()->intended('/admin/dashboard');
            }

            if (in_array($user->role, ['Admin RW','Pimpinan RW'])) {
                return redirect()->intended('/rw/dashboard');
            }

            if (in_array($user->role, ['Ketua RT'])) {
                return redirect()->intended('/rt/dashboard');
            }

            if (in_array($user->role, ['Op. Konten RW','Op. Konten RT'])) {
                return redirect()->intended('/op-konten/dashboard');
            }

            if (in_array($user->role, ['Op. Keuangan RW', 'Op. Keuangan RT','DKM'])) {
                return redirect()->intended('/op-keuangan/dashboard');
            }

            if (in_array($user->role, ['Warga'])) {
                return redirect()->intended('/warga/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'nik' => 'NIK atau password salah.',
        ])->withInput($request->only('nik'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}