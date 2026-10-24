<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pemilihan & penggantian peran aktif untuk akun multi-role (Paket 2B).
 *
 * Satu akun dapat memiliki beberapa peran (mis. "Ketua RT" + "Warga"), namun
 * pada satu sesi hanya satu peran yang aktif. Halaman ini menampilkan daftar
 * peran yang dimiliki dan menetapkan peran aktif ke session.
 */
class RoleSwitchController extends Controller
{
    /** Label ramah untuk ditampilkan pada kartu pilihan peran. */
    private const ROLE_LABELS = [
        'Warga' => 'Kepala Keluarga',
    ];

    public function show(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roleNames();

        // Jika hanya satu peran, tidak perlu memilih: set lalu lanjut.
        if (count($roles) <= 1) {
            if (! empty($roles)) {
                $user->setActiveRole($roles[0]);
            }

            return app(AuthController::class)->afterRoleSelected($request, $user->activeRole());
        }

        $options = array_map(fn ($role) => [
            'value' => $role,
            'label' => self::ROLE_LABELS[$role] ?? $role,
        ], $roles);

        return view('auth.pilih-peran', [
            'options' => $options,
            'currentRole' => $user->activeRole(),
        ]);
    }

    /**
     * Ganti peran tanpa logout: reset peran aktif lalu kembali ke pemilihan.
     * Untuk akun satu peran, tidak ada yang bisa diganti — kembali saja.
     */
    public function switch(Request $request)
    {
        User::clearActiveRole();
        $request->session()->forget('url.intended');

        return redirect()->route('pilih-peran');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'role' => ['required', 'string'],
        ]);

        if (! $user->setActiveRole($request->input('role'))) {
            return back()->withErrors([
                'role' => 'Peran yang dipilih tidak valid untuk akun Anda.',
            ]);
        }

        // Bersihkan target "intended" lama agar redirect mengikuti peran baru.
        $request->session()->forget('url.intended');

        return app(AuthController::class)->afterRoleSelected($request, $user->activeRole());
    }
}
