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
            'nik'      => ['required', 'string', 'digits:16'],
            'no_wa'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        // 1) The NIK must belong to a registered user.
        $user = \App\Models\User::where('nik', $request->nik)->first();

        if (! $user) {
            return back()->withErrors([
                'nik' => 'NIK tidak terdaftar dalam sistem.',
            ])->withInput($request->only('nik', 'no_wa'));
        }

        // 2) The WhatsApp number must match the household (keluarga) linked to this NIK.
        if (! $this->whatsappMatchesNik($request->nik, $request->no_wa)) {
            return back()->withErrors([
                'no_wa' => 'Nomor WhatsApp tidak cocok dengan data NIK tersebut.',
            ])->withInput($request->only('nik', 'no_wa'));
        }

        // 3) Validate the password and log the user in.
        if (Auth::attempt(['nik' => $request->nik, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
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
        ])->withInput($request->only('nik', 'no_wa'));
    }

    /**
     * Check whether the given WhatsApp number matches the household (keluarga)
     * associated with the given NIK.
     *
     * Chain: users.nik -> penduduk.nik -> penduduk.keluarga_id -> keluarga.no_wa
     * The head of family's NIK on the keluarga record is also accepted.
     */
    protected function whatsappMatchesNik(string $nik, string $noWa): bool
    {
        $noWa = $this->normalizeWhatsapp($noWa);

        // a) Via the resident (penduduk) -> household relationship.
        $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $nik)->first();
        if ($penduduk && $penduduk->keluarga) {
            if ($this->normalizeWhatsapp((string) $penduduk->keluarga->no_wa) === $noWa) {
                return true;
            }
        }

        // b) Fallback: the NIK is the head of a household directly.
        $asHead = \App\Models\Keluarga::where('nik_kepala_keluarga', $nik)->first();
        if ($asHead && $this->normalizeWhatsapp((string) $asHead->no_wa) === $noWa) {
            return true;
        }

        return false;
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
