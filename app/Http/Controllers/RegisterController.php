<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function aktivasi()
    {
        return redirect()->route('register');
    }

    public function prosesAktivasi(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'digits:16'],
            'no_wa' => ['required', 'string', 'max:20'],
        ]);

        $penduduk = Penduduk::with('keluarga')->where('nik', $request->nik)->first();
        $keluarga = $penduduk?->keluarga;

        if (! $keluarga || $this->normalizeWhatsapp($keluarga->no_wa) !== $this->normalizeWhatsapp($request->no_wa)) {
            return back()->withErrors([
                'nik' => 'NIK dan Nomor WhatsApp tidak cocok dengan data keluarga.',
            ])->withInput();
        }

        // Hanya Kepala Keluarga yang boleh mengaktivasi akun warga.
        // Anggota keluarga lain tidak memiliki akun ke sistem.
        if ($penduduk->nik !== $keluarga->nik_kepala_keluarga) {
            return back()->withErrors([
                'nik' => 'Hanya Kepala Keluarga yang dapat mengaktivasi akun. Hubungi Kepala Keluarga Anda.',
            ])->withInput();
        }

        $existingUser = User::where('nik', $request->nik)->first();
        if ($existingUser?->status_akun === 'Active') {
            return back()->withErrors(['nik' => 'Akun untuk NIK tersebut sudah aktif.'])->withInput();
        }

        // Mock OTP untuk UAT; tidak ada pengiriman WhatsApp pada tahap ini.
        $request->session()->put('activation', [
            'nik' => $penduduk->nik,
            'keluarga_id' => $keluarga->id,
            'otp' => '123456',
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        return redirect()->route('aktivasi.otp')->with('status', 'Mock OTP untuk UAT: 123456');
    }

    public function showOtp(Request $request)
    {
        if (! $request->session()->has('activation')) {
            return redirect()->route('register')->withErrors(['nik' => 'Silakan mulai aktivasi terlebih dahulu.']);
        }

        return view('auth.aktivasi');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:6']]);
        $activation = $request->session()->get('activation');

        if (! $activation || now()->timestamp > $activation['expires_at'] || ! hash_equals($activation['otp'], $request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        $request->session()->put('activation.otp_verified', true);

        return redirect()->route('aktivasi.password');
    }

    public function showPassword(Request $request)
    {
        if (! $this->otpIsVerified($request)) {
            return redirect()->route('aktivasi.otp');
        }

        return view('auth.aktivasi-password');
    }

    public function complete(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! $this->otpIsVerified($request)) {
            return redirect()->route('aktivasi.otp');
        }

        $activation = $request->session()->get('activation');
        $penduduk = Penduduk::with('keluarga')->where('nik', $activation['nik'])->first();

        if (! $penduduk || $penduduk->keluarga_id !== $activation['keluarga_id']) {
            $request->session()->forget('activation');
            return redirect()->route('register')->withErrors(['nik' => 'Data aktivasi tidak lagi valid.']);
        }

        // Pastikan penduduk yang diaktivasi adalah Kepala Keluarga.
        if ($penduduk->nik !== $penduduk->keluarga->nik_kepala_keluarga) {
            $request->session()->forget('activation');
            return redirect()->route('register')->withErrors(['nik' => 'Hanya Kepala Keluarga yang dapat mengaktivasi akun.']);
        }

        $existingUser = User::where('nik', $penduduk->nik)->first();
        // Jika akun sudah ada namun TIDAK memiliki peran Warga sama sekali,
        // berarti NIK itu dikelola murni sebagai akun petugas — tolak aktivasi warga.
        if ($existingUser && ! in_array('Warga', $existingUser->roleNames(), true)) {
            return redirect()->route('register')->withErrors(['nik' => 'NIK tersebut dikelola sebagai akun petugas.']);
        }

        DB::transaction(function () use ($existingUser, $penduduk, $request): void {
            $user = $existingUser ?: new User(['nik' => $penduduk->nik]);
            // Pertahankan username yang sudah dibuat RT; hanya isi bila akun baru.
            $user->fill([
                'username' => $user->username ?: $penduduk->nama_lengkap,
                'role' => $user->role ?: 'Warga',
                'password' => $request->password,
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ]);
            $user->save();

            // Pastikan peran Warga tersemat (tanpa menghapus peran lain yang mungkin ada).
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                try {
                    \Spatie\Permission\Models\Role::findOrCreate('Warga', 'web');
                    if (! $user->hasRole('Warga')) {
                        $user->assignRole('Warga');
                    }
                } catch (\Throwable $e) {
                    // Abaikan bila tabel permission belum ada.
                }
            }

            $penduduk->keluarga->update(['status_aktivasi' => 'Active']);
        });

        $request->session()->forget('activation');

        return redirect()->route('login')->with('status', 'Aktivasi berhasil. Silakan masuk dengan password baru.');
    }

    private function otpIsVerified(Request $request): bool
    {
        $activation = $request->session()->get('activation');

        return $activation
            && ($activation['otp_verified'] ?? false)
            && now()->timestamp <= $activation['expires_at'];
    }

    private function normalizeWhatsapp(?string $number): string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);

        return str_starts_with($digits, '62') ? '0' . substr($digits, 2) : $digits;
    }

    public function login()
    {
        return view('auth.login');
    }
}
