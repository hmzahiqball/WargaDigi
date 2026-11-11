<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Rt\KeluargaController as RtKeluargaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MutasiWarga;
use App\Models\Penduduk;
use Illuminate\Validation\Rule;

class KeluargaController extends Controller
{
    /**
     * Pastikan penduduk pemegang akun (akun Warga = kepala keluarga pemegang akun).
     * Mengembalikan [penduduk, keluarga] bila valid.
     */
    protected function resolvePemegangAkun()
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;

        if (!$penduduk || !$penduduk->keluarga) {
            return [null, null];
        }

        return [$penduduk, $penduduk->keluarga];
    }

    /**
     * Opsi referensi untuk form anggota (disamakan dengan RT).
     * Hubungan tanpa 'Kepala Keluarga' karena warga hanya menambah anggota.
     */
    protected function opsiForm(): array
    {
        return [
            'agamaOpts' => RtKeluargaController::AGAMA,
            'statusKawinOpts' => RtKeluargaController::STATUS_KAWIN,
            'pendidikanOpts' => RtKeluargaController::PENDIDIKAN,
            'hubunganOpts' => ['Istri', 'Anak', 'Orang Tua', 'Mertua', 'Famili Lain', 'Lainnya'],
        ];
    }

    /**
     * Susun alamat KK lengkap + domisili untuk prefill checkbox "sama dengan KK".
     */
    protected function alamatKk($keluarga): array
    {
        $pad = fn ($v) => ($v = preg_replace('/\D/', '', (string) $v)) !== '' ? str_pad($v, 3, '0', STR_PAD_LEFT) : '';
        $kodeRt = $pad($keluarga->rt->kode_rt ?? '');
        $kodeRw = $pad($keluarga->rw ?? '');
        $bagian = [];
        if ($keluarga->alamat) $bagian[] = $keluarga->alamat;
        if ($kodeRt || $kodeRw) $bagian[] = 'RT ' . ($kodeRt ?: '000') . '/RW ' . ($kodeRw ?: '000');
        if ($keluarga->kelurahan) $bagian[] = 'Kel. ' . $keluarga->kelurahan;
        if ($keluarga->kecamatan) $bagian[] = 'Kec. ' . $keluarga->kecamatan;
        if ($keluarga->kabupaten) $bagian[] = $keluarga->kabupaten;
        $prov = $keluarga->provinsi;
        if ($keluarga->kode_pos) $prov = ($prov ? $prov . ' ' : '') . $keluarga->kode_pos;
        if ($prov) $bagian[] = $prov;
        $kkAlamatLengkap = implode(', ', $bagian);
        $kkDomisili = $keluarga->alamat_domisili ?: $kkAlamatLengkap;

        return [$kkAlamatLengkap, $kkDomisili];
    }

    /**
     * Ambil snapshot nilai LAMA dari sebuah model untuk field yang diajukan.
     * Hanya key yang juga ada di $dataPengajuan dan termasuk daftar $allowed
     * yang disimpan, agar diff relevan. Tanggal dinormalisasi ke format Y-m-d.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array<string,mixed>  $dataPengajuan
     * @param  array<int,string>  $allowed
     * @return array<string,mixed>
     */
    protected function snapshotDataLama($model, array $dataPengajuan, array $allowed): array
    {
        $snapshot = [];
        foreach ($allowed as $field) {
            if (! array_key_exists($field, $dataPengajuan)) {
                continue;
            }
            $value = $model->{$field} ?? null;

            // Normalisasi tanggal lahir agar konsisten dengan input (Y-m-d).
            if ($field === 'tanggal_lahir' && $value) {
                try {
                    $value = \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
                } catch (\Throwable $e) {
                    // Biarkan nilai asli bila gagal diparse.
                }
            }

            $snapshot[$field] = $value;
        }

        return $snapshot;
    }

    public function index()
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        // Pemegang akun keluarga (bisa kepala keluarga atau anggota lain yang
        // ditetapkan melalui pemindahan akun).
        $isPemegangAkun = $penduduk->nik === $keluarga->nikPemegangAkun();

        // Anggota: kepala keluarga paling atas, lalu nama.
        $anggota = $keluarga->anggota()
            ->orderByRaw("CASE WHEN status_hubungan_keluarga = 'Kepala Keluarga' THEN 0 ELSE 1 END")
            ->orderBy('nama_lengkap')
            ->get();

        // Akun keluarga (pemegang akun aktual).
        $akun = \App\Models\User::where('nik', $keluarga->nikPemegangAkun())->first();

        // Riwayat pengajuan (semua jenis) — tampilkan ringkas 5 terbaru di index.
        $riwayat = MutasiWarga::where('keluarga_id', $keluarga->id)
            ->with('penduduk')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $riwayatTotal = MutasiWarga::where('keluarga_id', $keluarga->id)->count();

        return view('warga.keluarga.index', compact(
            'keluarga', 'anggota', 'akun', 'isPemegangAkun', 'riwayat', 'riwayatTotal'
        ));
    }

    /**
     * Halaman riwayat pengajuan lengkap untuk keluarga pemegang akun.
     */
    public function riwayat()
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        $riwayat = MutasiWarga::where('keluarga_id', $keluarga->id)
            ->with('penduduk')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('warga.keluarga.riwayat', compact('keluarga', 'riwayat'));
    }

    /**
     * Form edit KK (identitas keluarga) sebagai PENGAJUAN jenis 'edit_kk'.
     */
    public function edit()
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Hanya pemegang akun keluarga yang dapat mengajukan perubahan KK.');
        }

        $keluarga->loadMissing('rt');

        return view('warga.keluarga.edit-kk', compact('keluarga'));
    }

    /**
     * Simpan pengajuan perubahan KK (edit_kk) — TIDAK langsung mengubah keluarga.
     */
    public function update(Request $request)
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        // Hanya pemegang akun keluarga.
        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Hanya pemegang akun keluarga yang dapat mengajukan perubahan KK.');
        }

        $validated = $request->validate([
            'alamat' => 'required|string|max:255',
            'alamat_domisili' => 'nullable|string|max:255',
            'no_wa' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'pemilik_rumah' => 'nullable|boolean',
            'status_warga' => 'nullable|in:tetap,tidak_tetap',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
        ], [
            'no_wa.regex' => 'Nomor WA hanya boleh angka, spasi, tanda + atau -.',
        ]);

        // Cegah pengajuan ganda edit_kk yang masih diproses.
        if (MutasiWarga::where('keluarga_id', $keluarga->id)
            ->where('jenis_mutasi', 'edit_kk')
            ->whereIn('status', ['menunggu_rt', 'menunggu_rw'])
            ->exists()) {
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Masih terdapat pengajuan perubahan KK yang sedang diproses.');
        }

        // Normalisasi pemilik_rumah ke boolean.
        $validated['pemilik_rumah'] = (bool) ($request->boolean('pemilik_rumah'));

        // Snapshot nilai LAMA keluarga untuk field yang diajukan.
        $dataLama = $this->snapshotDataLama($keluarga, $validated, [
            'alamat', 'alamat_domisili', 'no_wa', 'pemilik_rumah', 'status_warga',
            'rw', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'kode_pos',
        ]);

        $mutasiBaru = MutasiWarga::create([
            'keluarga_id' => $keluarga->id,
            'penduduk_id' => null,
            'jenis_mutasi' => 'edit_kk',
            'data_pengajuan' => $validated,
            'data_lama' => $dataLama,
            'file_bukti' => null,
            'status' => 'menunggu_rt',
            'diajukan_oleh' => Auth::id(),
            'diajukan_at' => now(),
        ]);

        \App\Services\NotificationService::notifikasiMutasiMenungguRt($mutasiBaru);

        return redirect()->route('warga.keluarga.index')
            ->with('success', 'Pengajuan perubahan data KK telah terkirim dan sedang diproses oleh Admin RT.');
    }

    /**
     * Halaman tambah anggota (pengajuan).
     */
    public function createAnggota()
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            abort(403, 'Hanya pemegang akun keluarga yang dapat menambah anggota.');
        }

        $keluarga->loadMissing('rt');
        [$kkAlamatLengkap, $kkDomisili] = $this->alamatKk($keluarga);

        return view('warga.keluarga.anggota-form', array_merge($this->opsiForm(), [
            'mode' => 'create',
            'keluarga' => $keluarga,
            'member' => null,
            'kkAlamatLengkap' => $kkAlamatLengkap,
            'kkDomisili' => $kkDomisili,
            'action' => route('warga.keluarga.anggota.store'),
            'method' => 'POST',
            'requireKk' => true,
        ]));
    }

    /**
     * Halaman edit anggota (pengajuan perubahan data penduduk).
     */
    public function editAnggota($id)
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            abort(403, 'Hanya pemegang akun keluarga yang dapat mengubah data anggota.');
        }

        $member = Penduduk::where('id', $id)->where('keluarga_id', $keluarga->id)->first();
        if (!$member) {
            abort(403, 'Anggota ini bukan bagian dari keluarga Anda.');
        }

        $keluarga->loadMissing('rt');
        [$kkAlamatLengkap, $kkDomisili] = $this->alamatKk($keluarga);

        return view('warga.keluarga.anggota-form', array_merge($this->opsiForm(), [
            'mode' => 'edit',
            'keluarga' => $keluarga,
            'member' => $member,
            'kkAlamatLengkap' => $kkAlamatLengkap,
            'kkDomisili' => $kkDomisili,
            'action' => route('warga.keluarga.anggota.update', $member->id),
            'method' => 'PUT',
            'requireKk' => false,
        ]));
    }

    /**
     * Detail penduduk untuk pemegang akun keluarga ybs.
     */
    public function showAnggota($id)
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (!$penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        // Hanya pemegang akun keluarga yang boleh melihat detail anggota keluarganya.
        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            abort(403, 'Hanya pemegang akun keluarga yang dapat melihat detail anggota.');
        }

        $anggota = Penduduk::with('keluarga.rt')
            ->where('id', $id)
            ->where('keluarga_id', $keluarga->id)
            ->first();
        if (!$anggota) {
            abort(403, 'Anggota ini bukan bagian dari keluarga Anda.');
        }

        return view('warga.keluarga.penduduk-show', ['penduduk' => $anggota]);
    }

    public function storeAnggota(Request $request)
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;

        if (!$penduduk || !$penduduk->keluarga) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Data keluarga tidak ditemukan.'], 404);
            }
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        $keluarga = $penduduk->keluarga;

        // Hanya pemegang akun keluarga.
        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pemegang akun keluarga yang dapat menambah anggota keluarga.',
                ], 403);
            }
            abort(403, 'Hanya pemegang akun keluarga yang dapat menambah anggota keluarga.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['required', 'string', 'size:16', Rule::unique('penduduk', 'nik')],
            'no_wa' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'status_hubungan_keluarga' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'pendidikan_terakhir' => 'nullable|string',
            'pekerjaan' => 'required|string',
            'status_perkawinan' => 'required|string',
            'kewarganegaraan' => 'required|in:WNI,WNA',
            'alamat' => 'nullable|string|max:255',
            'alamat_domisili' => 'nullable|string|max:255',
            'status_warga' => 'nullable|in:tetap,tidak_tetap',
            'file_ktp_path' => 'nullable|string|max:255',
            'nama_ayah' => 'nullable|string',
            'nama_ibu' => 'nullable|string',
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if (MutasiWarga::where('keluarga_id', $keluarga->id)
            ->where('jenis_mutasi', 'tambah')
            ->whereIn('status', ['menunggu_rt', 'menunggu_rw'])
            ->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih terdapat pengajuan penambahan anggota yang sedang diproses.',
                ], 422);
            }
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Masih terdapat pengajuan penambahan anggota yang sedang diproses.');
        }

        $filePath = null;
        if ($request->hasFile('file_kk')) {
            $filePath = $request->file('file_kk')->store('mutasi_kk', 'public');
        }

        $dataPengajuan = $request->except(['_token', 'file_kk']);

        $mutasiBaru = MutasiWarga::create([
            'keluarga_id' => $keluarga->id,
            'penduduk_id' => null, // null for new member
            'jenis_mutasi' => 'tambah',
            'data_pengajuan' => $dataPengajuan,
            'data_lama' => null, // anggota baru: tidak ada data lama
            'file_bukti' => $filePath,
            'status' => 'menunggu_rt',
            'diajukan_oleh' => Auth::id(),
            'diajukan_at' => now(),
        ]);

        // Notifikasi: perubahan data menunggu persetujuan RT.
        \App\Services\NotificationService::notifikasiMutasiMenungguRt($mutasiBaru);

        $pesan = 'Permintaan perubahan data Anda telah kami terima dan sedang diproses oleh Admin RT.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $pesan]);
        }
        return redirect()->route('warga.keluarga.index')->with('success', $pesan);
    }

    public function updateAnggota(Request $request, $id)
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;

        if (!$penduduk || !$penduduk->keluarga) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Data keluarga tidak ditemukan.'], 404);
            }
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        // Hanya pemegang akun keluarga.
        if ($penduduk->nik !== $penduduk->keluarga->nikPemegangAkun()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pemegang akun keluarga yang dapat mengubah data anggota keluarga.',
                ], 403);
            }
            abort(403, 'Hanya pemegang akun keluarga yang dapat mengubah data anggota keluarga.');
        }

        $anggota = Penduduk::where('id', $id)->where('keluarga_id', $penduduk->keluarga->id)->firstOrFail();

        $request->validate([
            'pendidikan_terakhir' => 'nullable|string',
            'pekerjaan' => 'required|string',
            'status_perkawinan' => 'required|string',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['required', 'string', 'size:16', Rule::unique('penduduk', 'nik')->ignore($anggota->id)],
            'no_wa' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string',
            'status_hubungan_keluarga' => 'nullable|string',
            'kewarganegaraan' => 'nullable|in:WNI,WNA',
            'alamat' => 'nullable|string|max:255',
            'alamat_domisili' => 'nullable|string|max:255',
            'status_warga' => 'nullable|in:tetap,tidak_tetap',
            'file_ktp_path' => 'nullable|string|max:255',
            'file_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if (MutasiWarga::where('penduduk_id', $anggota->id)
            ->where('jenis_mutasi', 'edit')
            ->whereIn('status', ['menunggu_rt', 'menunggu_rw'])
            ->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih terdapat pengajuan perubahan anggota yang sedang diproses.',
                ], 422);
            }
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Masih terdapat pengajuan perubahan anggota yang sedang diproses.');
        }

        $identityChanged = $request->input('nik') !== $anggota->nik
            || $request->input('nama_lengkap') !== $anggota->nama_lengkap;
        if ($identityChanged && ! $request->hasFile('file_kk')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen KK wajib diunggah jika NIK atau nama diubah.',
                    'errors' => ['file_kk' => ['Dokumen KK wajib diunggah jika NIK atau nama diubah.']],
                ], 422);
            }
            return redirect()->back()->withInput()
                ->withErrors(['file_kk' => 'Dokumen KK wajib diunggah jika NIK atau nama diubah.']);
        }

        $filePath = null;
        if ($request->hasFile('file_kk')) {
            $filePath = $request->file('file_kk')->store('mutasi_kk', 'public');
        }

        $dataPengajuan = $request->except(['_token', 'file_kk', '_method']);

        // Snapshot data LAMA penduduk untuk field yang relevan dengan pengajuan.
        $dataLama = $this->snapshotDataLama($anggota, $dataPengajuan, [
            'nama_lengkap', 'nik', 'no_wa', 'jenis_kelamin', 'tempat_lahir',
            'tanggal_lahir', 'agama', 'pekerjaan', 'pendidikan_terakhir',
            'status_hubungan_keluarga', 'status_perkawinan', 'kewarganegaraan',
            'alamat', 'alamat_domisili', 'status_warga', 'nama_ayah', 'nama_ibu',
        ]);

        $mutasiBaru = MutasiWarga::create([
            'keluarga_id' => $penduduk->keluarga->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'edit',
            'data_pengajuan' => $dataPengajuan,
            'data_lama' => $dataLama,
            'file_bukti' => $filePath,
            'status' => 'menunggu_rt',
            'diajukan_oleh' => Auth::id(),
            'diajukan_at' => now(),
        ]);

        // Notifikasi: perubahan data menunggu persetujuan RT.
        \App\Services\NotificationService::notifikasiMutasiMenungguRt($mutasiBaru);

        $pesan = 'Permintaan perubahan data Anda telah kami terima dan sedang diproses oleh Admin RT.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $pesan]);
        }
        return redirect()->route('warga.keluarga.index')->with('success', $pesan);
    }

    /**
     * Daftar anggota keluarga selain pemegang akun saat ini (kandidat tujuan).
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\Penduduk>
     */
    protected function anggotaTujuan(\App\Models\Keluarga $keluarga): \Illuminate\Support\Collection
    {
        return $keluarga->anggota()
            ->where('nik', '!=', $keluarga->nikPemegangAkun())
            ->orderBy('nama_lengkap')
            ->get();
    }

    /**
     * Form pemindahan pemegang akun ke anggota keluarga lain.
     * Hanya boleh diakses oleh pemegang akun saat ini.
     */
    public function pindahAkunForm()
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (! $penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Hanya pemegang akun yang dapat memindahkan akun keluarga.');
        }

        $anggotaLain = $this->anggotaTujuan($keluarga);

        // Peran lain milik pemegang akun (selain Warga). Bila ada, hanya akses
        // Warga yang dialihkan dan akun tetap aktif untuk peran tersebut.
        $perananLain = array_values(array_filter(
            Auth::user()->roleNames(),
            fn ($r) => $r !== 'Warga'
        ));

        return view('warga.keluarga.pindah-akun', compact('keluarga', 'anggotaLain', 'perananLain'));
    }

    /**
     * Simpan pengajuan pemindahan pemegang akun (jenis 'pindah_akun').
     * Tidak langsung mengubah pemegang akun — menunggu persetujuan RT.
     */
    public function pindahAkunStore(Request $request)
    {
        [$penduduk, $keluarga] = $this->resolvePemegangAkun();

        if (! $penduduk) {
            return redirect()->route('warga.dashboard')->with('error', 'Data keluarga tidak ditemukan.');
        }

        if ($penduduk->nik !== $keluarga->nikPemegangAkun()) {
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Hanya pemegang akun yang dapat memindahkan akun keluarga.');
        }

        $nikPemegang = $keluarga->nikPemegangAkun();

        // NIK kandidat tujuan yang valid: anggota keluarga ini, bukan pemegang sekarang.
        $nikKandidat = $keluarga->anggota()
            ->where('nik', '!=', $nikPemegang)
            ->pluck('nik')
            ->all();

        $validated = $request->validate([
            'nik_tujuan' => ['required', 'string', Rule::in($nikKandidat)],
        ], [
            'nik_tujuan.required' => 'Pilih anggota keluarga tujuan pemindahan akun.',
            'nik_tujuan.in'       => 'Anggota tujuan tidak valid untuk pemindahan akun.',
        ]);

        // Cegah pengajuan pindah_akun ganda yang masih diproses.
        if (MutasiWarga::where('keluarga_id', $keluarga->id)
            ->where('jenis_mutasi', 'pindah_akun')
            ->whereIn('status', ['menunggu_rt', 'menunggu_rw'])
            ->exists()) {
            return redirect()->route('warga.keluarga.index')
                ->with('error', 'Masih terdapat pengajuan pemindahan akun yang sedang diproses.');
        }

        $pendudukTujuan = Penduduk::where('keluarga_id', $keluarga->id)
            ->where('nik', $validated['nik_tujuan'])
            ->firstOrFail();

        // Nama pemegang akun lama untuk snapshot data lama.
        $pendudukLama = Penduduk::where('keluarga_id', $keluarga->id)
            ->where('nik', $nikPemegang)
            ->first();

        $mutasiBaru = MutasiWarga::create([
            'keluarga_id' => $keluarga->id,
            'penduduk_id' => $pendudukTujuan->id,
            'jenis_mutasi' => 'pindah_akun',
            'data_pengajuan' => [
                'nik_tujuan'  => $pendudukTujuan->nik,
                'nama_tujuan' => $pendudukTujuan->nama_lengkap,
                'nik_lama'    => $nikPemegang,
            ],
            'data_lama' => [
                'nik_pemegang_lama'  => $nikPemegang,
                'nama_pemegang_lama' => $pendudukLama?->nama_lengkap,
                // Peran lain pemegang lama (tetap aktif setelah pemindahan).
                'peran_lain_pemegang_lama' => array_values(array_filter(
                    Auth::user()->roleNames(),
                    fn ($r) => $r !== 'Warga'
                )),
            ],
            'status' => 'menunggu_rt',
            'diajukan_oleh' => Auth::id(),
            'diajukan_at' => now(),
        ]);

        \App\Services\NotificationService::notifikasiMutasiMenungguRt($mutasiBaru);

        return redirect()->route('warga.keluarga.index')
            ->with('success', 'Pengajuan pemindahan akun telah dikirim dan sedang diproses oleh Admin RT.');
    }
}
