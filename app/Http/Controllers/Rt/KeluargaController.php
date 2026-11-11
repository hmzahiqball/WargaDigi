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
    public const HUBUNGAN = ['Kepala Keluarga', 'Istri', 'Anak', 'Famili Lain', 'Lainnya'];

    public function create()
    {
        return view('rt.keluarga.create', [
            'agamaOpts' => self::AGAMA,
            'pendidikanOpts' => self::PENDIDIKAN,
            'statusKawinOpts' => self::STATUS_KAWIN,
            'hubunganOpts' => self::HUBUNGAN,
        ]);
    }

    public function store(Request $request)
    {
        $rtId = $this->resolveRtId();
        if (! $rtId) {
            return back()->withErrors(['rt' => 'RT untuk akun Anda tidak terdefinisi.'])->withInput();
        }

        $validated = $request->validate([
            // Profil Keluarga.
            'no_kk'            => ['required', 'string', 'size:16', Rule::unique('keluarga', 'no_kk')],
            'alamat'           => ['required', 'string', 'max:255'],
            'alamat_domisili'  => ['nullable', 'string', 'max:255'],
            'rt'               => ['nullable', 'string', 'max:10'],
            'rw'               => ['nullable', 'string', 'max:10'],
            'kelurahan'        => ['nullable', 'string', 'max:100'],
            'kecamatan'        => ['nullable', 'string', 'max:100'],
            'kabupaten'        => ['nullable', 'string', 'max:100'],
            'provinsi'         => ['nullable', 'string', 'max:100'],
            'kode_pos'         => ['nullable', 'string', 'max:10'],
            'status_warga'     => ['nullable', 'in:tetap,tidak_tetap'],
            'pemilik_rumah'    => ['nullable', 'boolean'],
            'file_kk_path'     => ['nullable', 'string', 'max:255'],

            // Data Penduduk (array, minimal 1).
            'penduduk'                        => ['required', 'array', 'min:1'],
            'penduduk.*.nik'                  => ['required', 'string', 'size:16', 'distinct', Rule::unique('penduduk', 'nik')],
            'penduduk.*.no_wa'                => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'penduduk.*.nama_lengkap'         => ['required', 'string', 'max:100'],
            'penduduk.*.alamat'               => ['nullable', 'string', 'max:255'],
            'penduduk.*.alamat_domisili'      => ['nullable', 'string', 'max:255'],
            'penduduk.*.status_warga'         => ['nullable', 'in:tetap,tidak_tetap'],
            'penduduk.*.jenis_kelamin'        => ['required', 'in:L,P'],
            'penduduk.*.tempat_lahir'         => ['required', 'string', 'max:50'],
            'penduduk.*.tanggal_lahir'        => ['required', 'date'],
            'penduduk.*.agama'                => ['required', 'string', Rule::in(self::AGAMA)],
            'penduduk.*.pekerjaan'            => ['required', 'string', 'max:50'],
            'penduduk.*.pendidikan_terakhir'  => ['nullable', 'string', Rule::in(self::PENDIDIKAN)],
            'penduduk.*.status_hubungan_keluarga' => ['required', 'string', Rule::in(self::HUBUNGAN)],
            'penduduk.*.status_perkawinan'    => ['required', 'string', Rule::in(self::STATUS_KAWIN)],
            'penduduk.*.kewarganegaraan'      => ['nullable', 'in:WNI,WNA'],
            'penduduk.*.nama_ayah'            => ['nullable', 'string', 'max:100'],
            'penduduk.*.nama_ibu'             => ['nullable', 'string', 'max:100'],
            'penduduk.*.file_ktp_path'        => ['nullable', 'string', 'max:255'],

            // Pemegang akun keluarga (index dari array penduduk) + kredensial.
            'pemegang_akun' => ['required', 'integer'],
            // Username & password tidak diminta RT: warga menetapkannya sendiri via link aktivasi.
        ], [
            'no_kk.unique'           => 'Nomor KK sudah terdaftar.',
            'penduduk.required'      => 'Minimal satu data penduduk wajib diisi.',
            'penduduk.*.nik.unique'  => 'NIK sudah terdaftar pada data penduduk.',
            'penduduk.*.nik.distinct' => 'Terdapat NIK ganda pada daftar penduduk.',
            'penduduk.*.no_wa.regex'  => 'Nomor WA hanya boleh angka, spasi, tanda + atau -.',
            'pemegang_akun.required' => 'Tentukan satu penduduk sebagai pemegang akun keluarga.',
        ]);

        $pemegangIdx = (int) $validated['pemegang_akun'];
        if (! isset($validated['penduduk'][$pemegangIdx])) {
            return back()->withInput()->withErrors(['pemegang_akun' => 'Pemegang akun yang dipilih tidak valid.']);
        }

        // Nomor WA wajib untuk penduduk pemegang akun keluarga.
        if (empty(trim((string) ($validated['penduduk'][$pemegangIdx]['no_wa'] ?? '')))) {
            return back()->withInput()->withErrors([
                "penduduk.{$pemegangIdx}.no_wa" => 'Nomor WA wajib diisi untuk pemegang akun keluarga.',
            ]);
        }

        // Pastikan tepat satu Kepala Keluarga pada daftar penduduk.
        $jmlKepala = collect($validated['penduduk'])->where('status_hubungan_keluarga', 'Kepala Keluarga')->count();
        if ($jmlKepala !== 1) {
            return back()->withInput()->withErrors([
                'penduduk' => 'Harus ada tepat satu penduduk dengan hubungan "Kepala Keluarga".',
            ]);
        }

        $kepala = collect($validated['penduduk'])->firstWhere('status_hubungan_keluarga', 'Kepala Keluarga');

        // Status KK: pakai pilihan dropdown bila dikirim; selain itu hitung otomatis
        // (tetap bila RW 21 & kelurahan Tanimulya).
        $statusWarga = in_array($validated['status_warga'] ?? null, ['tetap', 'tidak_tetap'], true)
            ? $validated['status_warga']
            : $this->hitungStatusWarga($validated['rw'] ?? null, $validated['kelurahan'] ?? null);

        // Nomor WA keluarga diambil dari pemegang akun (kontak utama keluarga).
        $noWaKeluarga = $validated['penduduk'][$pemegangIdx]['no_wa'] ?? null;

        // Token aktivasi sekali pakai (Opsi B): warga menetapkan password via link.
        $activationToken = \Illuminate\Support\Str::random(40);

        DB::transaction(function () use ($validated, $rtId, $kepala, $pemegangIdx, $statusWarga, $noWaKeluarga, $activationToken) {
            $keluarga = Keluarga::create([
                'no_kk' => $validated['no_kk'],
                'no_wa' => $noWaKeluarga,
                'nik_kepala_keluarga' => $kepala['nik'],
                'alamat' => $validated['alamat'],
                'alamat_domisili' => $validated['alamat_domisili'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'kelurahan' => $validated['kelurahan'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,
                'pemilik_rumah' => (bool) ($validated['pemilik_rumah'] ?? false),
                'status_warga' => $statusWarga,
                'rt_id' => $rtId,
                'status_aktivasi' => 'Unverified',
            ]);

            // File KK ditautkan ke kepala keluarga.
            $fileKkPath = $validated['file_kk_path'] ?? null;

            foreach ($validated['penduduk'] as $i => $data) {
                Penduduk::create([
                    'keluarga_id' => $keluarga->id,
                    'nik' => $data['nik'],
                    'no_wa' => $data['no_wa'] ?? null,
                    'nama_lengkap' => $data['nama_lengkap'],
                    'alamat' => $data['alamat'] ?? null,
                    'alamat_domisili' => $data['alamat_domisili'] ?? null,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'agama' => $data['agama'],
                    'pekerjaan' => $data['pekerjaan'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? null,
                    'status_hubungan_keluarga' => $data['status_hubungan_keluarga'],
                    'status_perkawinan' => $data['status_perkawinan'],
                    'kewarganegaraan' => $data['kewarganegaraan'] ?? 'WNI',
                    'status_warga' => in_array($data['status_warga'] ?? null, ['tetap', 'tidak_tetap'], true)
                        ? $data['status_warga']
                        : $statusWarga,
                    'nama_ayah' => $data['nama_ayah'] ?? null,
                    'nama_ibu' => $data['nama_ibu'] ?? null,
                    'file_kk' => $data['status_hubungan_keluarga'] === 'Kepala Keluarga' ? $fileKkPath : null,
                    'file_ktp' => $data['file_ktp_path'] ?? null,
                ]);
            }

            // Akun warga untuk penduduk pemegang akun.
            $pemegang = $validated['penduduk'][$pemegangIdx];
            $user = new User();
            $user->fill([
                'nik' => $pemegang['nik'],
                // Username sementara unik; warga menetapkan username final saat aktivasi.
                'username' => 'warga_' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(12)),
                'role' => 'Warga',
                // Password sementara acak; warga akan menetapkan sendiri saat aktivasi.
                'password' => \Illuminate\Support\Str::random(32),
                'status_akun' => 'Unverified',
                'activation_token' => $activationToken,
                'activation_token_created_at' => now(),
                'rt_id' => $rtId,
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
            ->route('rt.penduduk.index')
            ->with('success', 'Data keluarga berhasil disimpan. Bagikan link aktivasi di bawah kepada pemegang akun agar dapat membuat username & password sendiri.')
            ->with('activation_link', route('aktivasi.token', ['token' => $activationToken]))
            ->with('activation_username', $kepala['nama_lengkap'] ?? 'pemegang akun');
    }

    /**
     * Tentukan status warga: 'tetap' bila RW 21 & Kelurahan Tanimulya,
     * selain itu 'tidak_tetap'.
     */
    private function hitungStatusWarga(?string $rw, ?string $kelurahan): string
    {
        $rwNorm = ltrim(trim((string) $rw), '0');
        $kelNorm = strtolower(trim((string) $kelurahan));
        $isRw21 = $rwNorm === '21';
        $isTanimulya = str_contains($kelNorm, 'tanimulya');

        return ($isRw21 && $isTanimulya) ? 'tetap' : 'tidak_tetap';
    }

    /**
     * Detail satu keluarga (KK) beserta anggota penduduknya.
     * Ketua RT hanya boleh melihat keluarga di RT-nya sendiri.
     */
    public function show(Request $request, string $keluarga)
    {
        $keluargaModel = Keluarga::with(['rt', 'anggota' => function ($q) {
            $q->orderByRaw("CASE WHEN status_hubungan_keluarga = 'Kepala Keluarga' THEN 0 ELSE 1 END")
              ->orderBy('nama_lengkap');
        }])->findOrFail($keluarga);

        $user = $request->user();
        if ($user->activeRole() === 'Ketua RT' && $keluargaModel->rt_id !== $user->rt_id) {
            abort(403, 'Keluarga ini berada di luar RT Anda.');
        }

        // Akun pemegang (kepala keluarga) untuk info status aktivasi + link.
        $akun = \App\Models\User::where('nik', $keluargaModel->nik_kepala_keluarga)->first();
        $linkAktivasi = ($akun && $akun->status_akun !== 'Active' && $akun->activation_token)
            ? route('aktivasi.token', ['token' => $akun->activation_token])
            : null;

        return view('rt.keluarga.show', [
            'keluarga'     => $keluargaModel,
            'akun'         => $akun,
            'linkAktivasi' => $linkAktivasi,
        ]);
    }

    /**
     * Buat ulang token/link aktivasi untuk akun pemegang keluarga (bila belum aktif).
     */
    public function regenerateActivation(Request $request, string $keluarga)
    {
        $keluargaModel = Keluarga::findOrFail($keluarga);
        $user = $request->user();
        if ($user->activeRole() === 'Ketua RT' && $keluargaModel->rt_id !== $user->rt_id) {
            abort(403, 'Keluarga ini berada di luar RT Anda.');
        }

        $akun = \App\Models\User::where('nik', $keluargaModel->nik_kepala_keluarga)->first();
        if (! $akun) {
            return back()->withErrors(['akun' => 'Akun pemegang keluarga tidak ditemukan.']);
        }
        if ($akun->status_akun === 'Active') {
            return back()->withErrors(['akun' => 'Akun sudah aktif; tidak perlu link aktivasi.']);
        }

        $akun->update([
            'activation_token' => \Illuminate\Support\Str::random(40),
            'activation_token_created_at' => now(),
        ]);

        return back()
            ->with('success', 'Link aktivasi berhasil dibuat ulang.')
            ->with('activation_link', route('aktivasi.token', ['token' => $akun->activation_token]))
            ->with('activation_username', $akun->username);
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
