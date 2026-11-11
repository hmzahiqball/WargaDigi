<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Statistik nyata dari database
        $totalPenduduk = \App\Models\Penduduk::count();
        $totalKepalaKeluarga = \App\Models\Keluarga::count();
        $totalUser = \App\Models\User::count();
        $aktifHariIni = \App\Models\User::whereDate('last_login', today())->count();

        $stats = [
            ['label' => 'AKTIF HARI INI', 'value' => $aktifHariIni, 'total' => $totalUser, 'icon' => 'bi-check-circle', 'color' => '#43A047'],
            ['label' => 'TOTAL KEPALA KELUARGA', 'value' => $totalKepalaKeluarga, 'icon' => 'bi-people-fill', 'color' => '#FF9800'],
            ['label' => 'TOTAL PENDUDUK', 'value' => $totalPenduduk, 'icon' => 'bi-person-badge-fill', 'color' => '#0288D1'],
        ];

        // Peringatan terbaru diambil dari log aktivitas nyata (Spatie Activitylog)
        $alerts = [];
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            $recentActivities = \Spatie\Activitylog\Models\Activity::latest()->take(5)->get();

            foreach ($recentActivities as $activity) {
                $event = $activity->event ?? '';
                $type = match ($event) {
                    'failed' => 'warning',
                    'deleted' => 'warning',
                    default => 'success',
                };

                $alerts[] = [
                    'title' => ucfirst($activity->log_name ?? 'Aktivitas'),
                    'desc' => $activity->description ?? 'Aktivitas sistem.',
                    'time' => $activity->created_at
                        ? $activity->created_at->timezone('Asia/Jakarta')->diffForHumans()
                        : 'Baru saja',
                    'type' => $type,
                ];
            }
        }

        // Instance tunggal (aplikasi single-RW): data nyata dari pengaturan sistem & jumlah user
        $instances = [
            [
                'name' => SystemSetting::get('instance_name', 'RW 21 Tanimulya'),
                'domain' => SystemSetting::get('domain', 'rw21.wargadigi.id'),
                'status' => SystemSetting::get('maintenance_mode', false) ? 'Maintenance' : 'Active',
                'users' => $totalUser,
            ],
        ];

        return view('admin.dashboard', compact('stats', 'alerts', 'instances'));
    }

    public function pengaturanSistem()
    {
        $settings = SystemSetting::all_settings();

        return view('admin.pengaturan-sistem', compact('settings'));
    }

    public function updatePengaturanSistem(Request $request)
    {
        $validated = $request->validate([
            'instance_name'    => ['required', 'string', 'max:100'],
            'domain'           => ['nullable', 'string', 'max:150'],
            'description'      => ['nullable', 'string', 'max:500'],
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'session_timeout'  => ['required', 'integer', 'in:15,30,60,120'],
            'notif_whatsapp'   => ['nullable'],
            'notif_email'      => ['nullable'],
            'notif_push'       => ['nullable'],
            'two_factor'       => ['nullable'],
            'maintenance_mode' => ['nullable'],
        ]);

        SystemSetting::setMany([
            'instance_name'    => $validated['instance_name'],
            'domain'           => $validated['domain'] ?? '',
            'description'      => $validated['description'] ?? '',
            'session_timeout'  => (int) $validated['session_timeout'],
            'notif_whatsapp'   => $request->boolean('notif_whatsapp'),
            'notif_email'      => $request->boolean('notif_email'),
            'notif_push'       => $request->boolean('notif_push'),
            'two_factor'       => $request->boolean('two_factor'),
            'maintenance_mode' => $request->boolean('maintenance_mode'),
        ]);

        if ($request->hasFile('logo')) {
            // Remove previous logo if it exists.
            $oldLogo = SystemSetting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('logo')->store('settings', 'public');
            SystemSetting::set('logo', $path);
        }

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Pengaturan sistem berhasil disimpan.');
    }

    public function manajemenHakAkses(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $users = \App\Models\User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            })
            ->orderBy('username')
            ->paginate(15)
            ->withQueryString();

        // Daftar peran yang bisa ditetapkan (urut sesuai enum kanonik).
        $roles = $this->availableRoles();
        $pengurusRoles = $this->pengurusRoles();

        // Peta peran per user (untuk pre-check multi-select di UI).
        $userRoles = [];
        foreach ($users as $u) {
            $userRoles[$u->id] = $u->roleNames();
        }

        // Opsi "Ambil dari warga": semua penduduk yang BELUM memiliki akun.
        $nikBerakun = \App\Models\User::whereNotNull('nik')->pluck('nik')->all();
        $pendudukOptions = \App\Models\Penduduk::with('keluarga.rt:id,kode_rt')
            ->whereNotIn('nik', $nikBerakun)
            ->orderBy('nama_lengkap')
            ->get(['nik', 'nama_lengkap', 'status_hubungan_keluarga', 'keluarga_id'])
            ->map(fn ($p) => [
                'nik' => $p->nik,
                'nama_lengkap' => $p->nama_lengkap,
                // Tampilkan RT (bukan status hubungan keluarga).
                'rt' => optional(optional($p->keluarga)->rt)->kode_rt,
                // Saran username dari nama lengkap (bisa diubah admin).
                'username_saran' => \Illuminate\Support\Str::slug($p->nama_lengkap, ''),
            ])
            ->values();

        // Daftar RT untuk penetapan operator/ketua RT.
        $rtOptions = \App\Models\MasterRt::orderBy('kode_rt')->get(['id', 'kode_rt', 'nama_rt']);
        // rt_id per user (untuk pre-fill saat edit peran).
        $userRtId = [];
        foreach ($users as $u) {
            $userRtId[$u->id] = $u->rt_id;
        }
        // Peran yang memerlukan penetapan RT.
        $rolesButuhRt = $this->rolesButuhRt();

        return view('admin.manajemen-hak-akses', compact('users', 'roles', 'pengurusRoles', 'userRoles', 'search', 'pendudukOptions', 'rtOptions', 'userRtId', 'rolesButuhRt'));
    }

    /** Peran yang lingkup kerjanya terikat ke sebuah RT (butuh rt_id). */
    protected function rolesButuhRt(): array
    {
        return ['Ketua RT', 'Op Konten RT', 'Op Keuangan RT'];
    }

    /**
     * Perbarui daftar peran sebuah akun (mendukung multi-peran / Paket 2B).
     *
     * roles[] disinkronkan ke Spatie (sumber kebenaran daftar peran), dan kolom
     * `role` legacy diisi dengan peran pertama sebagai default/active fallback.
     */
    public function updateHakAkses(Request $request, string $id)
    {
        $validated = $request->validate([
            'roles'   => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in($this->availableRoles())],
            'rt_id'   => ['nullable', 'uuid', Rule::exists('master_rt', 'id')],
        ], [
            'roles.required' => 'Pilih minimal satu peran.',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $roles = array_values(array_unique($validated['roles']));

        // Guard: peran Warga membutuhkan NIK kependudukan (akun warga = kepala keluarga).
        if (in_array('Warga', $roles, true) && empty($user->nik)) {
            return back()
                ->with('open_edit_user', $user->id)
                ->withErrors([
                    'roles' => 'Peran Warga hanya untuk akun yang tertaut NIK kependudukan.',
                ]);
        }

        // Guard: peran Ketua RT / Ketua RW hanya untuk akun yang sudah ditetapkan
        // sebagai pejabat AKTIF pada menu Kepengurusan (menghindari role tanpa jabatan nyata).
        if ($pesan = $this->pelanggaranRolePejabat($user, $roles)) {
            return back()
                ->with('open_edit_user', $user->id)
                ->withErrors(['roles' => $pesan]);
        }

        // RT: wajib bila ada peran RT. Bila peran RT, pakai pilihan admin atau
        // pertahankan rt_id lama / RT warga; bila bukan peran RT, kosongkan.
        if (array_intersect($roles, $this->rolesButuhRt())) {
            $rtId = $validated['rt_id'] ?? $user->rt_id
                ?? optional(optional(\App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first())->keluarga)->rt_id;
            if (! $rtId) {
                return back()->with('open_edit_user', $user->id)->withErrors([
                    'rt_id' => 'Pilih RT untuk peran operator/ketua RT.',
                ]);
            }
        } else {
            $rtId = null;
        }

        // Kolom role legacy = peran pertama; Spatie menyimpan daftar lengkap.
        $user->update(['role' => $roles[0], 'rt_id' => $rtId]);
        $this->syncUserRoles($user, $roles);

        return redirect()
            ->route('admin.manajemen-hak-akses')
            ->with('success', 'Peran ' . ($user->username ?? $user->nik) . ' berhasil diperbarui: ' . implode(', ', $roles) . '.');
    }

    /**
     * Buat/akun-kan pengurus. Dua mode (field `sumber`):
     *  - 'warga': ambil dari data penduduk (akun tertaut NIK). Bila penduduk
     *    sudah punya akun, peran pengurus ditambahkan ke akun yang ada.
     *  - 'baru' : akun pengurus tanpa NIK, login via username (perilaku lama).
     */
    public function storePengurus(Request $request)
    {
        $sumber = $request->input('sumber', 'baru');

        if ($sumber === 'warga') {
            return $this->storePengurusDariWarga($request);
        }

        return $this->storePengurusUsernameBaru($request);
    }

    /**
     * Mode 'baru': akun pengurus tanpa NIK (login via username).
     * Peran Ketua RT / Ketua RW tidak diperbolehkan di sini (butuh NIK pejabat).
     */
    protected function storePengurusUsernameBaru(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles'    => ['required', 'array', 'min:1'],
            'roles.*'  => ['string', Rule::in($this->pengurusRoles())],
            'rt_id'    => ['nullable', 'uuid', Rule::exists('master_rt', 'id')],
        ], [
            'username.required'  => 'Username wajib diisi.',
            'username.regex'     => 'Username hanya boleh huruf, angka, titik, garis bawah, atau strip.',
            'username.unique'    => 'Username sudah digunakan. Pilih username lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'roles.required'     => 'Pilih minimal satu peran pengurus.',
        ]);

        $roles = array_values(array_unique($validated['roles']));

        // Guard: akun tanpa NIK tidak bisa menyandang peran Ketua RT / Ketua RW.
        $butuhPejabat = array_intersect($roles, $this->rolePejabat());
        if (! empty($butuhPejabat)) {
            return back()
                ->withInput()
                ->withErrors([
                    'roles' => 'Peran ' . implode(', ', $butuhPejabat) . ' tidak dapat diberikan ke akun tanpa NIK. '
                        . 'Gunakan opsi "Ambil dari warga" dan pastikan orangnya sudah menjadi pejabat aktif di menu Kepengurusan.',
                ]);
        }

        // RT wajib bila ada peran yang butuh RT (mis. Op Keuangan RT / Op Konten RT).
        $rtId = $validated['rt_id'] ?? null;
        if (array_intersect($roles, $this->rolesButuhRt()) && ! $rtId) {
            return back()->withInput()->withErrors([
                'rt_id' => 'Pilih RT untuk peran operator RT.',
            ]);
        }
        if (! array_intersect($roles, $this->rolesButuhRt())) {
            $rtId = null; // peran RW/DKM tidak terikat RT
        }

        $user = new \App\Models\User();
        $user->fill([
            'nik' => null,
            'username' => $validated['username'],
            'role' => $roles[0],
            'password' => $validated['password'],
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
            'rt_id' => $rtId,
        ]);
        $user->save();

        $this->syncUserRoles($user, $roles);

        return redirect()
            ->route('admin.manajemen-hak-akses')
            ->with('success', 'Akun pengurus "' . $user->username . '" berhasil dibuat dengan peran: ' . implode(', ', $roles) . '.');
    }

    /**
     * Mode 'warga': buat akun pengurus dari penduduk yang BELUM punya akun.
     *
     * Akun baru dibuat ber-NIK dengan username + password. Peran Ketua RT /
     * Ketua RW diizinkan, tetap divalidasi harus pejabat aktif.
     */
    protected function storePengurusDariWarga(Request $request)
    {
        $validated = $request->validate([
            'nik'      => ['required', 'string', 'size:16', 'exists:penduduk,nik'],
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles'    => ['required', 'array', 'min:1'],
            'roles.*'  => ['string', Rule::in($this->availableRoles())],
            'rt_id'    => ['nullable', 'uuid', Rule::exists('master_rt', 'id')],
        ], [
            'nik.required'       => 'Pilih warga terlebih dahulu.',
            'nik.exists'         => 'Penduduk tidak ditemukan.',
            'username.required'  => 'Username wajib diisi.',
            'username.regex'     => 'Username hanya boleh huruf, angka, titik, garis bawah, atau strip.',
            'username.unique'    => 'Username sudah digunakan. Pilih username lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'roles.required'     => 'Pilih minimal satu peran.',
        ]);

        $roles = array_values(array_unique($validated['roles']));

        // NIK ini tidak boleh sudah memiliki akun.
        if (\App\Models\User::where('nik', $validated['nik'])->exists()) {
            return back()->withInput()->withErrors([
                'nik' => 'Warga ini sudah memiliki akun. Gunakan Edit Peran pada akun tersebut.',
            ]);
        }

        // RT: default dari RT warga, tetapi bisa ditimpa pilihan admin. Wajib untuk peran RT.
        $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $validated['nik'])->first();
        $rtId = $validated['rt_id'] ?? optional(optional($penduduk)->keluarga)->rt_id;
        if (array_intersect($roles, $this->rolesButuhRt()) && ! $rtId) {
            return back()->withInput()->withErrors([
                'rt_id' => 'Pilih RT untuk peran operator/ketua RT.',
            ]);
        }
        if (! array_intersect($roles, $this->rolesButuhRt())) {
            $rtId = null;
        }

        $user = new \App\Models\User();
        $user->fill([
            'nik' => $validated['nik'],
            'username' => $validated['username'],
            'role' => $roles[0],
            'password' => $validated['password'],
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
            'rt_id' => $rtId,
        ]);

        // Validasi pejabat aktif untuk Ketua RT / Ketua RW sebelum menyimpan.
        if ($pesan = $this->pelanggaranRolePejabat($user, $roles)) {
            return back()->withInput()->withErrors(['roles' => $pesan]);
        }

        $user->save();
        $this->syncUserRoles($user, $roles);

        return redirect()
            ->route('admin.manajemen-hak-akses')
            ->with('success', 'Akun pengurus untuk warga "' . $user->username . '" berhasil dibuat dengan peran: ' . implode(', ', $roles) . '.');
    }

    /**
     * Sinkronkan daftar peran Spatie sebuah akun (menggantikan peran lama).
     */
    protected function syncUserRoles(\App\Models\User $user, array $roles): void
    {
        if (! class_exists(\Spatie\Permission\Models\Role::class)) {
            return;
        }

        try {
            foreach ($roles as $role) {
                \Spatie\Permission\Models\Role::findOrCreate($role, 'web');
            }
            $user->syncRoles($roles);
        } catch (\Throwable $e) {
            // Abaikan bila tabel permission belum tersedia.
        }
    }

    /**
     * Peran yang WAJIB tertaut pejabat aktif (menu Kepengurusan).
     *
     * Hanya Ketua RT dan Ketua RW. Peran lain tidak terpengaruh validasi ini.
     *
     * @return array<int,string>
     */
    protected function rolePejabat(): array
    {
        return ['Ketua RT', 'Ketua RW'];
    }

    /**
     * Periksa apakah pemberian daftar peran ke sebuah akun melanggar aturan
     * "Ketua RT / Ketua RW harus pejabat aktif".
     *
     * Mengembalikan pesan error (string) bila melanggar, atau null bila lolos.
     * Peran selain Ketua RT / Ketua RW diabaikan (selalu lolos).
     */
    protected function pelanggaranRolePejabat(\App\Models\User $user, array $roles): ?string
    {
        $perluCek = array_values(array_intersect($roles, $this->rolePejabat()));
        if (empty($perluCek)) {
            return null;
        }

        // Keduanya ditaut lewat NIK kependudukan pada tabel pejabat.
        if (empty($user->nik)) {
            return 'Peran ' . implode(', ', $perluCek) . ' hanya untuk akun yang tertaut NIK kependudukan '
                . 'dan sudah ditetapkan sebagai pejabat aktif di menu Kepengurusan.';
        }

        foreach ($perluCek as $role) {
            if ($role === 'Ketua RW') {
                $aktif = \App\Models\Pejabat::aktif()
                    ->where('jabatan', 'ketua_rw')
                    ->where('penduduk_nik', $user->nik)
                    ->exists();
                if (! $aktif) {
                    return 'Akun ini belum terdaftar sebagai Ketua RW aktif di menu Kepengurusan. '
                        . 'Tetapkan dulu yang bersangkutan sebagai Ketua RW aktif sebelum memberi peran ini.';
                }
            }

            if ($role === 'Ketua RT') {
                $aktif = \App\Models\Pejabat::aktif()
                    ->where('jabatan', 'ketua_rt')
                    ->where('penduduk_nik', $user->nik)
                    ->exists();
                if (! $aktif) {
                    return 'Akun ini belum terdaftar sebagai Ketua RT aktif di menu Kepengurusan. '
                        . 'Tetapkan dulu yang bersangkutan sebagai Ketua RT aktif sebelum memberi peran ini.';
                }
            }
        }

        return null;
    }

    /**
     * Daftar peran pengurus/petugas (tanpa Warga, karena Warga butuh NIK).
     */
    protected function pengurusRoles(): array
    {
        return array_values(array_filter(
            $this->availableRoles(),
            fn ($role) => $role !== 'Warga'
        ));
    }

    /**
     * The canonical list of assignable roles (mirrors the users.role enum).
     */
    protected function availableRoles(): array
    {
        return [
            'Admin Aplikasi',
            'Admin RW',
            'Ketua RW',
            'Op Konten RW',
            'Op Keuangan RW',
            'Ketua RT',
            'Op Konten RT',
            'Op Keuangan RT',
            'DKM',
            'Warga',
        ];
    }

    public function logAktivitas(Request $request)
    {
        $activityModel = \Spatie\Activitylog\Models\Activity::class;

        $query = $activityModel::with('causer')->latest();

        // Search on description or causer name.
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%");
            });
        }

        // Category filter (stored in properties->category or derived from log_name).
        if ($category = $request->input('category')) {
            $query->where(function ($q) use ($category) {
                $q->where('properties->category', $category)
                    ->orWhere('log_name', $category);
                if ($category === 'login') {
                    $q->orWhere('log_name', 'auth');
                }
            });
        }

        // Filter rentang tanggal. Default: 7 hari terakhir agar tidak berat
        // saat pertama kali dibuka (bila tidak ada filter tanggal dari user).
        $defaultDari = now()->subDays(6)->toDateString(); // 7 hari inklusif hari ini
        $defaultSampai = now()->toDateString();
        $dari = $request->input('tgl_dari', $defaultDari);
        $sampai = $request->input('tgl_sampai', $defaultSampai);

        if ($dari) {
            $query->where('created_at', '>=', \Illuminate\Support\Carbon::parse($dari)->startOfDay());
        }
        if ($sampai) {
            $query->where('created_at', '<=', \Illuminate\Support\Carbon::parse($sampai)->endOfDay());
        }

        $paginated = $query->paginate(15)->withQueryString();
        $totalLogs = $activityModel::count();

        $logs = collect($paginated->items())->map(function ($activity) {
            return $this->transformActivity($activity);
        })->all();

        return view('admin.log-aktivitas', [
            'logs' => $logs,
            'totalLogs' => $totalLogs,
            'paginator' => $paginated,
            'filterDari' => $dari,
            'filterSampai' => $sampai,
        ]);
    }

    /**
     * Hapus log aktivitas pada rentang tanggal (inklusif).
     *
     * Rentang berdasarkan kolom created_at. Tanggal akhir dibuat inklusif
     * sampai akhir hari (23:59:59).
     */
    public function hapusLogAktivitas(Request $request)
    {
        $validated = $request->validate([
            'dari'   => ['required', 'date'],
            'sampai' => ['required', 'date', 'after_or_equal:dari'],
        ], [
            'dari.required'         => 'Tanggal mulai wajib diisi.',
            'sampai.required'       => 'Tanggal akhir wajib diisi.',
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
        ]);

        $dari = \Illuminate\Support\Carbon::parse($validated['dari'])->startOfDay();
        $sampai = \Illuminate\Support\Carbon::parse($validated['sampai'])->endOfDay();

        $activityModel = \Spatie\Activitylog\Models\Activity::class;
        $jumlah = $activityModel::whereBetween('created_at', [$dari, $sampai])->count();

        if ($jumlah === 0) {
            return redirect()
                ->route('admin.log-aktivitas')
                ->with('error', 'Tidak ada log aktivitas pada rentang tanggal tersebut.');
        }

        $activityModel::whereBetween('created_at', [$dari, $sampai])->delete();

        // Catat aksi penghapusan sebagai log baru agar tetap akuntabel.
        $rentangLabel = $dari->translatedFormat('d M Y') . ' - ' . $sampai->translatedFormat('d M Y');
        activity('log-aktivitas')
            ->causedBy($request->user())
            ->withProperties([
                'category'  => 'log-aktivitas',
                'dari'      => $dari->toDateString(),
                'sampai'    => $sampai->toDateString(),
                'jumlah'    => $jumlah,
            ])
            ->event('deleted')
            ->log("Menghapus {$jumlah} log aktivitas periode {$rentangLabel}.");

        return redirect()
            ->route('admin.log-aktivitas')
            ->with('success', "Berhasil menghapus {$jumlah} log aktivitas pada rentang {$rentangLabel}.");
    }

    /**
     * Map a Spatie Activity record into the shape the log view expects.
     */
    protected function transformActivity($activity): array
    {
        $causer = $activity->causer;
        $props = $activity->properties ?? collect();
        $targetNik = $props['nik'] ?? null;
        $targetUser = (! $causer && $targetNik) ? \App\Models\User::where('nik', $targetNik)->first() : null;

        $name = $causer?->name ?? ($causer?->username ?? ($props['name'] ?? ($targetUser?->name ?? ($targetNik ? "NIK: {$targetNik}" : 'Sistem'))));
        $role = $causer?->role ?? ($props['role'] ?? ($targetUser?->role ?? ($targetNik ? 'Tamu' : 'SISTEM')));
        $status = $props['status'] ?? 'Sukses';
        $category = $props['category'] ?? ($activity->log_name ?: 'lainnya');

        // Icon + display type by log_name / event.
        [$activityType, $icon] = $this->activityDisplay($activity->log_name, $activity->event);

        $created = $activity->created_at ? \Carbon\Carbon::parse($activity->created_at)->timezone('Asia/Jakarta') : null;

        return [
            'date' => $created?->translatedFormat('d M Y') ?? '-',
            'time' => $created?->format('H:i:s') ?? '-',
            'name' => $name,
            'initials' => $this->initials($name),
            'avatar_bg' => '#bdbdbd',
            'role' => strtoupper($role),
            'role_bg' => $this->roleColor($role),
            'role_color' => '#ffffff',
            'activity_type' => $activityType,
            'activity_icon' => $icon,
            'description' => $activity->description,
            'status' => $status,
            'status_class' => in_array($status, ['Gagal', 'Failed']) ? 'failed' : 'success',
            'category' => $category,
        ];
    }

    protected function activityDisplay(?string $logName, ?string $event): array
    {
        return match ($logName) {
            'auth'       => match ($event) {
                'logout' => ['Logout', 'bi-box-arrow-right'],
                'failed' => ['Login Gagal', 'bi-shield-x'],
                default  => ['Login', 'bi-box-arrow-in-right'],
            },
            'pengaturan' => ['Mengubah Pengaturan', 'bi-gear'],
            'master_rt'  => ['Manajemen RT', 'bi-building'],
            'pengguna'   => ['Manajemen Pengguna', 'bi-person-gear'],
            'berita'     => ['Membuat Data Publikasi', 'bi-newspaper'],
            default      => match ($event) {
                'created' => ['Membuat Data Baru', 'bi-file-earmark-plus'],
                'updated' => ['Memperbarui Data', 'bi-pencil-square'],
                'deleted' => ['Menghapus Data', 'bi-trash'],
                default   => ['Aktivitas Sistem', 'bi-activity'],
            },
        };
    }

    protected function roleColor(?string $role): string
    {
        return match ($role) {
            'Admin Aplikasi'                 => '#1B5E20',
            'Admin RW', 'Ketua RW'        => '#2E7D32',
            'Ketua RT'                       => '#546E7A',
            'Op Keuangan RW', 'Op Keuangan RT', 'DKM' => '#43A047',
            'Op Konten RW', 'Op Konten RT'   => '#0288D1',
            'Warga'                          => '#78909c',
            default                          => '#455A64',
        };
    }

    protected function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $initials .= mb_strtoupper(mb_substr($p, 0, 1));
        }
        return $initials ?: 'SY';
    }

    public function arsipDataWarga()
    {
        $arsip = [
            [
                'name' => 'Budi Santoso',
                'nik' => '3273012345678901',
                'date' => '12 Oct 2012',
                'reason' => 'Pindah',
                'status' => '> 10 tahun',
                'status_color' => '#78909c',
            ],
            [
                'name' => 'Siti Aminah',
                'nik' => '3273019876543210',
                'date' => '05 Mar 2008',
                'reason' => 'Almarhum',
                'status' => 'Menunggu Penghapusan',
                'status_color' => '#FF9800',
            ],
            [
                'name' => 'Agus Wijaya',
                'nik' => '3273011223344 55',
                'date' => '20 Jan 2020',
                'reason' => 'Cadangkan Sistem',
                'status' => 'Dapat Dipulihkan',
                'status_color' => '#43A047',
            ],
        ];

        $storage = [
            'used' => 1.2,
            'total' => 5.0,
            'breakdown' => [
                ['label' => 'Backup Data', 'size' => '800MB', 'color' => '#43A047'],
                ['label' => 'Catatan Almarhum', 'size' => '400MB', 'color' => '#FF9800'],
            ],
        ];

        return view('admin.arsip-data-warga', compact('arsip', 'storage'));
    }

    public function manajemenData()
    {
        $systemHealth = [
            'status' => 'healthy',
            'last_backup' => 'Hari ini, 08:30 WIB',
        ];

        $backup = [
            'total_size' => '1.2 GB',
            'schedule' => 'Setiap Minggu, 00:00',
        ];

        $archiveData = [
            [
                'category' => 'Warga Meninggal (>10 Tahun)',
                'count' => 42,
                'action' => 'Penghapusan Permanen',
                'action_color' => '#E53935',
                'status' => 'Review Needed',
                'status_color' => '#E53935',
            ],
            [
                'category' => 'Warga Pindah (>5 Tahun)',
                'count' => 128,
                'action' => 'Arsip Dingin',
                'action_color' => '#555',
                'status' => 'Archived',
                'status_color' => '#43A047',
            ],
        ];

        return view('admin.manajemen-data', compact('systemHealth', 'backup', 'archiveData'));
    }
}
