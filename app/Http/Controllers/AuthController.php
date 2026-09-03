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
            
            foreach(config('sidebarMenu') as $item) {
                if (in_array($user->role, $item['roles'])) {
                    return redirect()->intended($item['url']);
                }
            }

            return redirect()->intended('/warga');
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