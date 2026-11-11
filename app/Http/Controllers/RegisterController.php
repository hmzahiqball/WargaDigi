<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Opsi B — Aktivasi via token/link dari RT (tanpa OTP).
     * Menampilkan form buat password bila token valid.
     */
    public function aktivasiToken(string $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (! $user || $user->status_akun === 'Active') {
            return redirect()->route('login')->withErrors([
                'username' => 'Link aktivasi tidak valid atau akun sudah aktif.',
            ]);
        }

        $penduduk = Penduduk::where('nik', $user->nik)->first();

        return view('auth.aktivasi-token', [
            'token'    => $token,
            'username' => $user->username,
            'nama'     => $penduduk?->nama_lengkap ?? $user->username,
        ]);
    }

    /**
     * Proses aktivasi via token: set password, aktifkan akun, hapus token.
     */
    public function prosesAktivasiToken(Request $request, string $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (! $user || $user->status_akun === 'Active') {
            return redirect()->route('login')->withErrors([
                'username' => 'Link aktivasi tidak valid atau akun sudah aktif.',
            ]);
        }

        $request->validate([
            'username' => [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.unique' => 'Username sudah digunakan. Pilih username lain.',
            'username.regex'  => 'Username hanya boleh huruf, angka, titik, garis bawah, atau strip.',
        ]);

        DB::transaction(function () use ($user, $request): void {
            $user->fill([
                'username' => $request->username,
                'password' => $request->password,
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
                'activation_token' => null,
                'activation_token_created_at' => null,
            ])->save();

            $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();
            if ($penduduk?->keluarga) {
                $penduduk->keluarga->update(['status_aktivasi' => 'Active']);
            }
        });

        return redirect()->route('login')->with('status', 'Aktivasi berhasil. Silakan masuk dengan password yang Anda buat.');
    }

    public function login()
    {
        return view('auth.login');
    }
}
