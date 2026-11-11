<?php

namespace App\Http\Controllers\Rt;

use App\Http\Controllers\Controller;
use App\Models\MutasiWarga;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MutasiWargaController extends Controller
{
    public function index()
    {
        $mutasi = MutasiWarga::with(['keluarga.kepalaKeluarga', 'penduduk'])
            ->whereHas('keluarga', fn ($query) => $query->where('rt_id', auth()->user()->rt_id))
            ->where('status', 'menunggu_rt')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rt.mutasi-warga.index', compact('mutasi'));
    }

    public function approve(Request $request, $id)
    {
        $mutasi = MutasiWarga::whereHas('keluarga', fn ($query) => $query->where('rt_id', auth()->user()->rt_id))
            ->where('status', 'menunggu_rt')
            ->where('id', $id)
            ->first();
        if (! $mutasi) {
            return back()->withErrors(['mutasi' => 'Status pengajuan tidak valid untuk diproses oleh RT.']);
        }

        // Pemindahan pemegang akun: cukup approval RT (tidak diteruskan ke RW).
        if ($mutasi->jenis_mutasi === 'pindah_akun') {
            return $this->approvePindahAkun($mutasi);
        }

        $mutasi->status = 'menunggu_rw';
        $mutasi->disetujui_rt_oleh = auth()->id();
        $mutasi->disetujui_rt_at = now();
        $mutasi->save();

        // Notifikasi: lanjut ke RW untuk persetujuan.
        \App\Services\NotificationService::notifikasiMutasiMenungguRw($mutasi);

        return redirect()->back()->with('success', 'Pengajuan data disetujui. Menunggu persetujuan RW.');
    }

    /**
     * Eksekusi pemindahan pemegang akun keluarga (approval RT final).
     *
     * - Nonaktifkan akun pemegang lama (tidak dihapus).
     * - Set keluarga.nik_pemegang_akun ke NIK anggota tujuan.
     * - Siapkan akun tujuan (buat bila belum ada) dengan token aktivasi baru.
     * - Tampilkan link aktivasi agar RT dapat membagikannya.
     */
    protected function approvePindahAkun(MutasiWarga $mutasi)
    {
        $keluarga = $mutasi->keluarga;
        $data = $mutasi->data_pengajuan ?? [];
        $nikTujuan = $data['nik_tujuan'] ?? null;

        if (! $keluarga || empty($nikTujuan)) {
            return back()->withErrors(['mutasi' => 'Data pengajuan pemindahan akun tidak lengkap.']);
        }

        $pendudukTujuan = Penduduk::where('keluarga_id', $keluarga->id)
            ->where('nik', $nikTujuan)
            ->first();
        if (! $pendudukTujuan) {
            return back()->withErrors(['mutasi' => 'Anggota tujuan tidak ditemukan pada keluarga ini.']);
        }

        $nikLama = $keluarga->nikPemegangAkun();

        $hasil = DB::transaction(function () use ($mutasi, $keluarga, $nikTujuan, $nikLama) {
            // 1) Cabut akses Warga dari akun pemegang lama (bila ada).
            //    - Akun dengan peran lain (mis. Ketua RT) TETAP Active, hanya
            //      peran Warga yang dicabut agar akses lainnya tidak terkunci.
            //    - Akun yang HANYA Warga dinonaktifkan (tidak dihapus).
            $userLama = User::where('nik', $nikLama)->first();
            if ($userLama) {
                $perananLain = array_values(array_filter(
                    $userLama->roleNames(),
                    fn ($r) => $r !== 'Warga'
                ));

                if (class_exists(Role::class)) {
                    try {
                        if ($userLama->hasRole('Warga')) {
                            $userLama->removeRole('Warga');
                        }
                    } catch (\Throwable $e) {
                        // Abaikan bila tabel permission belum tersedia.
                    }
                }

                if (! empty($perananLain)) {
                    // Masih punya peran lain: akun tetap aktif, hanya role legacy
                    // dipindah ke peran yang tersisa agar middleware/sidebar konsisten.
                    $userLama->fill(['role' => $perananLain[0]])->save();
                } else {
                    // Tidak punya peran lain selain Warga: nonaktifkan akun.
                    $userLama->fill([
                        'status_akun' => 'Nonaktif',
                        'activation_token' => null,
                        'activation_token_created_at' => null,
                    ])->save();
                }

                // Sesi aktif yang sedang memakai peran Warga tidak lagi valid
                // untuk peran itu; activeRole() akan jatuh ke peran tersisa pada
                // request berikutnya karena roleNames() tidak lagi memuat Warga.
            }

            // 2) Pindahkan pemegang akun pada keluarga.
            $keluarga->update(['nik_pemegang_akun' => $nikTujuan]);

            // 3) Siapkan akun tujuan.
            $userTujuan = User::where('nik', $nikTujuan)->first();
            $butuhAktivasi = ! $userTujuan || $userTujuan->status_akun !== 'Active';

            if (! $userTujuan) {
                // Belum punya akun sama sekali: buat baru + token aktivasi.
                $userTujuan = new User();
                $userTujuan->nik = $nikTujuan;
                $userTujuan->role = 'Warga';
                $userTujuan->rt_id = $keluarga->rt_id;
                $userTujuan->username = 'warga_' . Str::lower(Str::random(12));
                $userTujuan->fill([
                    'password' => Str::random(32),
                    'status_akun' => 'Unverified',
                    'activation_token' => Str::random(40),
                    'activation_token_created_at' => now(),
                ])->save();
            } elseif ($userTujuan->status_akun === 'Active') {
                // Sudah punya akun aktif (mis. peran lain): cukup tambah peran
                // Warga, JANGAN sentuh username/password/status akun yang ada.
                // (tidak ada perubahan atribut lain yang perlu di-save di sini)
            } else {
                // Akun ada tapi belum aktif (Unverified/Nonaktif): siapkan ulang
                // token aktivasi tanpa mengubah username yang mungkin sudah di-set.
                $userTujuan->fill([
                    'status_akun' => 'Unverified',
                    'activation_token' => Str::random(40),
                    'activation_token_created_at' => now(),
                ])->save();
            }

            // Pastikan peran Warga tersemat pada akun tujuan.
            if (class_exists(Role::class)) {
                try {
                    Role::findOrCreate('Warga', 'web');
                    if (! $userTujuan->hasRole('Warga')) {
                        $userTujuan->assignRole('Warga');
                    }
                } catch (\Throwable $e) {
                    // Abaikan bila tabel permission belum tersedia.
                }
            }

            // 4) Tandai mutasi selesai (disetujui oleh RT, final tanpa RW).
            $mutasi->status = 'disetujui';
            $mutasi->disetujui_rt_oleh = auth()->id();
            $mutasi->disetujui_rt_at = now();
            $mutasi->save();

            return ['user' => $userTujuan, 'butuh_aktivasi' => $butuhAktivasi];
        });

        $userTujuan = $hasil['user'];

        // Notifikasi hasil ke pengaju.
        \App\Services\NotificationService::notifikasiMutasiHasil($mutasi, 'RT', true);

        $redirect = redirect()->back();

        if ($hasil['butuh_aktivasi']) {
            $redirect = $redirect
                ->with('success', 'Pemindahan akun disetujui. Bagikan link aktivasi kepada pemegang akun baru.')
                ->with('activation_link', route('aktivasi.token', ['token' => $userTujuan->activation_token]))
                ->with('activation_username', $pendudukTujuan->nama_lengkap);
        } else {
            $redirect = $redirect->with('success', 'Pemindahan akun disetujui. Akun tujuan sudah aktif sebelumnya, cukup diberi akses Warga tanpa perlu aktivasi ulang.');
        }

        return $redirect;
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $mutasi = MutasiWarga::whereHas('keluarga', fn ($query) => $query->where('rt_id', auth()->user()->rt_id))
            ->where('status', 'menunggu_rt')
            ->where('id', $id)
            ->first();
        if (! $mutasi) {
            return back()->withErrors(['mutasi' => 'Status pengajuan tidak valid untuk diproses oleh RT.']);
        }
        $mutasi->status = 'ditolak';
        $mutasi->keterangan_tolak = $request->alasan_penolakan;
        $mutasi->ditolak_oleh = auth()->id();
        $mutasi->save();

        // Notifikasi: ditolak RT → pengaju.
        \App\Services\NotificationService::notifikasiMutasiHasil($mutasi, 'RT', false, $request->alasan_penolakan);

        return redirect()->back()->with('success', 'Pengajuan data ditolak.');
    }
}
