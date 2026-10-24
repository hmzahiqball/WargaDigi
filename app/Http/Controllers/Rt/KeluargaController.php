<?php

namespace App\Http\Controllers\Rt;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Pengelolaan data Keluarga & Kepala Keluarga oleh Ketua RT (Paket 2B).
 *
 * Ketua RT membuat data Keluarga beserta Kepala Keluarga. Kepala Keluarga
 * sekaligus memperoleh AKUN WARGA (role "Warga") dengan username yang diinput
 * manual dan unik — digunakan untuk login. Anggota keluarga lain tidak memiliki
 * akun (ditangani pada aksi terpisah).
 */
class KeluargaController extends Controller
{
    /** Pilihan referensi agar form & validasi konsisten. */
    public const AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
    public const PENDIDIKAN = ['Tidak Sekolah', 'SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
    public const STATUS_KAWIN = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];

    public function create()
    {
        return view('rt.keluarga.create', [
            'agamaOpts' => self::AGAMA,
            'pendidikanOpts' => self::PENDIDIKAN,
            'statusKawinOpts' => self::STATUS_KAWIN,
        ]);
    }

    public function store(Request $request)
    {
        $rtId = $this->resolveRtId();
        if (! $rtId) {
            return back()->withErrors(['rt' => 'RT untuk akun Anda tidak terdefinisi.'])->withInput();
        }

        $validated = $request->validate([
            // Data Kartu Keluarga.
            'no_kk'   => ['required', 'string', 'size:16', Rule::unique('keluarga', 'no_kk')],
            'alamat'  => ['required', 'string', 'max:255'],
            'no_wa'   => ['required', 'string', 'max:20'],

            // Data Kepala Keluarga (penduduk) — sesuai kebutuhan aktivasi/register.
            'nik'                 => ['required', 'string', 'size:16', Rule::unique('penduduk', 'nik')],
            'nama_lengkap'        => ['required', 'string', 'max:100'],
            'jenis_kelamin'       => ['required', 'in:L,P'],
            'tempat_lahir'        => ['required', 'string', 'max:50'],
            'tanggal_lahir'       => ['required', 'date'],
            'agama'               => ['required', 'string', Rule::in(self::AGAMA)],
            'pekerjaan'           => ['required', 'string', 'max:50'],
            'pendidikan_terakhir' => ['required', 'string', Rule::in(self::PENDIDIKAN)],
            'status_perkawinan'   => ['required', 'string', Rule::in(self::STATUS_KAWIN)],
            'kewarganegaraan'     => ['required', 'in:WNI,WNA'],
            'nama_ayah'           => ['nullable', 'string', 'max:100'],
            'nama_ibu'            => ['nullable', 'string', 'max:100'],

            // Akun login Kepala Keluarga.
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'no_kk.unique'    => 'Nomor KK sudah terdaftar.',
            'nik.unique'      => 'NIK sudah terdaftar pada data penduduk.',
            'username.unique' => 'Username sudah digunakan. Pilih username lain.',
            'username.regex'  => 'Username hanya boleh huruf, angka, titik, garis bawah, atau strip.',
        ]);

        DB::transaction(function () use ($validated, $rtId) {
            // 1) Keluarga.
            $keluarga = Keluarga::create([
                'no_kk' => $validated['no_kk'],
                'nik_kepala_keluarga' => $validated['nik'],
                'alamat' => $validated['alamat'],
                'rt_id' => $rtId,
                'no_wa' => $validated['no_wa'],
                'status_aktivasi' => 'Unverified',
            ]);

            // 2) Penduduk (Kepala Keluarga).
            Penduduk::create([
                'keluarga_id' => $keluarga->id,
                'nik' => $validated['nik'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'pekerjaan' => $validated['pekerjaan'],
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => $validated['status_perkawinan'],
                'kewarganegaraan' => $validated['kewarganegaraan'],
                'nama_ayah' => $validated['nama_ayah'] ?? null,
                'nama_ibu' => $validated['nama_ibu'] ?? null,
            ]);

            // 3) Akun warga untuk Kepala Keluarga (role "Warga").
            //    status Unverified: diaktifkan lewat alur aktivasi (Task 10).
            $user = new User();
            $user->fill([
                'nik' => $validated['nik'],
                'username' => $validated['username'],
                'role' => 'Warga',
                'password' => $validated['password'],
                'status_akun' => 'Unverified',
            ]);
            $user->save();

            if (class_exists(Role::class)) {
                try {
                    Role::findOrCreate('Warga', 'web');
                    $user->syncRoles(['Warga']);
                } catch (\Throwable $e) {
                    // Abaikan bila tabel permission belum ada.
                }
            }
        });

        return redirect()
            ->route('rt.dashboard')
            ->with('success', 'Data keluarga & kepala keluarga berhasil dibuat. Akun warga dapat diaktivasi oleh kepala keluarga.');
    }

    /**
     * Tambah anggota keluarga (penduduk) ke sebuah keluarga, TANPA membuat akun.
     * Hanya anggota non-kepala (anggota keluarga biasa) yang ditambahkan di sini.
     */
    public function storeAnggota(Request $request, string $keluarga)
    {
        $rtId = $this->resolveRtId();
        $keluargaModel = Keluarga::findOrFail($keluarga);

        // Ketua RT hanya boleh menambah anggota pada keluarga di RT-nya sendiri.
        if ($rtId && $keluargaModel->rt_id !== $rtId) {
            abort(403, 'Keluarga ini berada di luar RT Anda.');
        }

        $validated = $request->validate([
            'nik'                      => ['required', 'string', 'size:16', Rule::unique('penduduk', 'nik')],
            'nama_lengkap'             => ['required', 'string', 'max:100'],
            'status_hubungan_keluarga' => ['required', 'string', 'max:50', Rule::notIn(['Kepala Keluarga'])],
            'jenis_kelamin'            => ['required', 'in:L,P'],
            'tempat_lahir'             => ['required', 'string', 'max:50'],
            'tanggal_lahir'            => ['required', 'date'],
            'agama'                    => ['required', 'string', Rule::in(self::AGAMA)],
            'pekerjaan'                => ['required', 'string', 'max:50'],
            'pendidikan_terakhir'      => ['required', 'string', Rule::in(self::PENDIDIKAN)],
            'status_perkawinan'        => ['required', 'string', Rule::in(self::STATUS_KAWIN)],
            'kewarganegaraan'          => ['required', 'in:WNI,WNA'],
            'nama_ayah'                => ['nullable', 'string', 'max:100'],
            'nama_ibu'                 => ['nullable', 'string', 'max:100'],
        ], [
            'nik.unique'                      => 'NIK sudah terdaftar pada data penduduk.',
            'status_hubungan_keluarga.not_in' => 'Kepala Keluarga sudah ada; pilih status hubungan lain.',
        ]);

        // Anggota keluarga TIDAK memperoleh akun (hanya record penduduk).
        Penduduk::create([
            'keluarga_id' => $keluargaModel->id,
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'agama' => $validated['agama'],
            'pekerjaan' => $validated['pekerjaan'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
            'status_hubungan_keluarga' => $validated['status_hubungan_keluarga'],
            'status_perkawinan' => $validated['status_perkawinan'],
            'kewarganegaraan' => $validated['kewarganegaraan'],
            'nama_ayah' => $validated['nama_ayah'] ?? null,
            'nama_ibu' => $validated['nama_ibu'] ?? null,
        ]);

        return back()->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    /**
     * Tentukan rt_id milik Ketua RT yang sedang login.
     */
    protected function resolveRtId(): ?string
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        if ($user->rt_id) {
            return $user->rt_id;
        }

        // Fallback: dari data penduduk -> keluarga (untuk akun RT bertaut NIK).
        if ($user->nik) {
            $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();
            if ($penduduk && $penduduk->keluarga) {
                return $penduduk->keluarga->rt_id;
            }
        }

        return optional(MasterRt::first())->id;
    }
}
