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
        $request->validate([
            'no_wa'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $normalizedWa = $this->normalizeWhatsapp($request->no_wa);

        // 1) Find the user via keluarga.no_wa -> penduduk.nik -> users.nik
        $user = $this->findUserByWhatsapp($normalizedWa);

        if (! $user) {
            event(new \Illuminate\Auth\Events\Failed('web', null, $request->only('no_wa')));

            return back()->withErrors([
                'no_wa' => 'Nomor WhatsApp tidak terdaftar dalam sistem.',
            ])->withInput($request->only('no_wa'));
        }

        // 2) Check account activation status.
        if ($user->status_akun !== 'Active') {
            event(new \Illuminate\Auth\Events\Failed('web', $user, $request->only('no_wa')));

            return back()->withErrors([
                'no_wa' => 'Akun belum diaktivasi. Silakan lakukan aktivasi terlebih dahulu.',
            ])->withInput($request->only('no_wa'));
        }

        // 3) Validate the password and log the user in.
        if (Auth::attempt(['nik' => $user->nik, 'password' => $request->password], $request->filled('remember'))) {
            $user = Auth::user();

            // If system is in maintenance mode and user is not admin, deny access with 503
            if (\App\Models\SystemSetting::get('maintenance_mode', false) && !in_array($user->role, ['Admin Aplikasi', 'Admin RW'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->view('errors.503', [], 503);
            }

            $request->session()->regenerate();
            $user->forceFill(['last_login' => now()])->saveQuietly();

            foreach (config('sidebarMenu') as $item) {
                if (in_array($user->role, $item['roles'])) {
                    return redirect()->intended($item['url']);
                }
            }

            return redirect()->intended('/warga');
        }

        return back()->withErrors([
            'password' => 'Password yang Anda masukkan salah.',
        ])->withInput($request->only('no_wa'));
    }

    /**
     * Find a User by their household WhatsApp number.
     *
     * Chain: keluarga.no_wa -> penduduk.nik -> users.nik
     * Checks all keluarga records whose normalized no_wa matches,
     * then finds a registered user among the household members.
     */
    protected function findUserByWhatsapp(string $normalizedWa): ?\App\Models\User
    {
        // Find all keluarga with matching normalized WhatsApp number.
        $keluargaRecords = \App\Models\Keluarga::all()->filter(function ($k) use ($normalizedWa) {
            return $this->normalizeWhatsapp((string) $k->no_wa) === $normalizedWa;
        });

        foreach ($keluargaRecords as $keluarga) {
            // a) Check household members (penduduk -> user).
            $niks = $keluarga->anggota()->pluck('nik')->toArray();

            // b) Also check the head of family NIK directly.
            if ($keluarga->nik_kepala_keluarga) {
                $niks[] = $keluarga->nik_kepala_keluarga;
            }

            $niks = array_unique($niks);

            $user = \App\Models\User::whereIn('nik', $niks)->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Normalize a WhatsApp/phone number for comparison:
     * strip non-digits and convert a leading 62 country code to 0.
     */
    protected function normalizeWhatsapp(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number);

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
