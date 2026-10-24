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
        // Field tunggal "login" menerima Username ATAU Nomor WhatsApp.
        // Kompatibilitas mundur: form/test lama yang masih mengirim "no_wa" tetap didukung.
        $request->merge([
            'login' => trim((string) ($request->input('login', $request->input('no_wa')))),
        ]);

        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username atau Nomor WhatsApp wajib diisi.',
        ]);

        $identifier = $request->input('login');
        $oldInput = ['login' => $identifier, 'no_wa' => $identifier];

        // 1) Kumpulkan SEMUA kandidat user yang cocok dengan identifier
        //    (username langsung, atau nomor WA via rantai keluarga).
        $candidates = $this->findLoginCandidates($identifier);

        if ($candidates->isEmpty()) {
            event(new \Illuminate\Auth\Events\Failed('web', null, ['login' => $identifier]));

            return back()->withErrors([
                'login' => 'Username atau Nomor WhatsApp tidak terdaftar dalam sistem.',
            ])->withInput($oldInput);
        }

        // 2) Tentukan user yang benar berdasarkan PASSWORD (bukan sekadar akun pertama),
        //    agar identifier yang dipakai beberapa akun tidak salah masuk.
        $activeCandidates = $candidates->where('status_akun', 'Active');

        $matchedUser = $activeCandidates->first(function ($candidate) use ($request) {
            return \Illuminate\Support\Facades\Hash::check($request->password, $candidate->password);
        });

        if (! $matchedUser) {
            if ($activeCandidates->isEmpty()) {
                event(new \Illuminate\Auth\Events\Failed('web', $candidates->first(), ['login' => $identifier]));

                return back()->withErrors([
                    'login' => 'Akun belum diaktivasi. Silakan lakukan aktivasi terlebih dahulu.',
                ])->withInput($oldInput);
            }

            event(new \Illuminate\Auth\Events\Failed('web', $activeCandidates->first(), ['login' => $identifier]));

            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->withInput($oldInput);
        }

        // 3) Login pengguna yang cocok.
        Auth::login($matchedUser, $request->filled('remember'));
        $user = Auth::user();

        $request->session()->regenerate();
        $user->forceFill(['last_login' => now()])->saveQuietly();

        // 4) Tentukan peran aktif.
        //    - Akun satu peran: langsung set & lanjut.
        //    - Akun banyak peran: arahkan ke halaman "Pilih Peran".
        \App\Models\User::clearActiveRole();

        $roles = $user->roleNames();
        if (count($roles) <= 1) {
            if (! empty($roles)) {
                $user->setActiveRole($roles[0]);
            }

            return $this->afterRoleSelected($request, $user->activeRole());
        }

        // Multi-role: belum pilih peran. Middleware EnsureActiveRole akan
        // mengarahkan ke /pilih-peran untuk request berikutnya.
        return redirect()->route('pilih-peran');
    }

    /**
     * Finalisasi sesi setelah peran aktif ditentukan (dipakai login 1-peran
     * maupun setelah memilih peran di halaman Pilih Peran).
     */
    public function afterRoleSelected(Request $request, ?string $activeRole)
    {
        $user = Auth::user();

        // Maintenance mode: hanya Admin Aplikasi/Admin RW yang boleh masuk.
        if (\App\Models\SystemSetting::get('maintenance_mode', false)
            && ! in_array($activeRole, ['Admin Aplikasi', 'Admin RW'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->view('errors.503', [], 503);
        }

        return redirect()->intended($this->homeUrlForRole($activeRole));
    }

    /**
     * URL beranda sesuai peran aktif (berdasarkan entri pertama di sidebarMenu).
     */
    public function homeUrlForRole(?string $role): string
    {
        if ($role) {
            foreach (config('sidebarMenu') as $item) {
                if (! empty($item['roles']) && in_array($role, $item['roles'], true) && ! empty($item['url'])) {
                    return $item['url'];
                }
            }
        }

        return '/warga';
    }

    /**
     * Cari kandidat login dari satu identifier yang bisa berupa username atau
     * nomor WhatsApp.
     *
     *  - Selalu coba cocokkan sebagai username (case-insensitive).
     *  - Bila identifier mengandung digit, coba juga resolusi sebagai nomor WA.
     * Hasil digabung & di-dedupe; pemilihan akun final dilakukan via password.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    protected function findLoginCandidates(string $identifier): \Illuminate\Support\Collection
    {
        $candidates = collect();

        // Jalur username (perbandingan case-insensitive).
        $byUsername = \App\Models\User::whereRaw('LOWER(username) = ?', [mb_strtolower($identifier)])->get();
        $candidates = $candidates->merge($byUsername);

        // Jalur nomor WhatsApp (hanya bila ada digit).
        if (preg_match('/\d/', $identifier)) {
            $normalizedWa = $this->normalizeWhatsapp($identifier);
            $candidates = $candidates->merge($this->findUsersByWhatsapp($normalizedWa));
        }

        return $candidates->unique(fn ($user) => $user->getKey())->values();
    }

    /**
     * Find all Users associated with a household WhatsApp number.
     *
     * Chain: keluarga.no_wa -> (penduduk.nik + nik_kepala_keluarga) -> users.nik
     * Satu nomor WA dapat terhubung ke beberapa akun; pemanggil bertanggung jawab
     * memilih akun yang tepat (mis. berdasarkan kecocokan password).
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    protected function findUsersByWhatsapp(string $normalizedWa): \Illuminate\Support\Collection
    {
        if ($normalizedWa === '') {
            return collect();
        }

        // Find all keluarga with matching normalized WhatsApp number.
        $keluargaRecords = \App\Models\Keluarga::all()->filter(function ($k) use ($normalizedWa) {
            return $this->normalizeWhatsapp((string) $k->no_wa) === $normalizedWa;
        });

        $niks = [];
        foreach ($keluargaRecords as $keluarga) {
            foreach ($keluarga->anggota()->pluck('nik')->toArray() as $nik) {
                $niks[] = $nik;
            }
            if ($keluarga->nik_kepala_keluarga) {
                $niks[] = $keluarga->nik_kepala_keluarga;
            }
        }

        $niks = array_values(array_unique($niks));

        if (empty($niks)) {
            return collect();
        }

        return \App\Models\User::whereIn('nik', $niks)->get();
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
        // Keluar dari peran apa pun: bersihkan peran aktif lalu akhiri sesi.
        \App\Models\User::clearActiveRole();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
