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

        return view('admin.manajemen-hak-akses', compact('users', 'roles', 'pengurusRoles', 'userRoles', 'search'));
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
        ], [
            'roles.required' => 'Pilih minimal satu peran.',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $roles = array_values(array_unique($validated['roles']));

        // Guard: peran Warga membutuhkan NIK kependudukan (akun warga = kepala keluarga).
        if (in_array('Warga', $roles, true) && empty($user->nik)) {
            return back()->withErrors([
                'roles' => 'Peran Warga hanya untuk akun yang tertaut NIK kependudukan.',
            ]);
        }

        // Kolom role legacy = peran pertama; Spatie menyimpan daftar lengkap.
        $user->update(['role' => $roles[0]]);
        $this->syncUserRoles($user, $roles);

        return redirect()
            ->route('admin.manajemen-hak-akses')
            ->with('success', 'Peran ' . ($user->username ?? $user->nik) . ' berhasil diperbarui: ' . implode(', ', $roles) . '.');
    }

    /**
     * Buat akun pengurus baru (tanpa NIK, login via username).
     */
    public function storePengurus(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles'    => ['required', 'array', 'min:1'],
            'roles.*'  => ['string', Rule::in($this->pengurusRoles())],
        ], [
            'username.regex'  => 'Username hanya boleh huruf, angka, titik, garis bawah, atau strip.',
            'username.unique' => 'Username sudah digunakan. Pilih username lain.',
            'roles.required'  => 'Pilih minimal satu peran pengurus.',
        ]);

        $roles = array_values(array_unique($validated['roles']));

        $user = new \App\Models\User();
        $user->fill([
            'nik' => null,
            'username' => $validated['username'],
            'role' => $roles[0],
            'password' => $validated['password'],
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);
        $user->save();

        $this->syncUserRoles($user, $roles);

        return redirect()
            ->route('admin.manajemen-hak-akses')
            ->with('success', 'Akun pengurus "' . $user->username . '" berhasil dibuat dengan peran: ' . implode(', ', $roles) . '.');
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
            'Pimpinan RW',
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

        $paginated = $query->paginate(15)->withQueryString();
        $totalLogs = $activityModel::count();

        $logs = collect($paginated->items())->map(function ($activity) {
            return $this->transformActivity($activity);
        })->all();

        return view('admin.log-aktivitas', [
            'logs' => $logs,
            'totalLogs' => $totalLogs,
            'paginator' => $paginated,
        ]);
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
            'Admin RW', 'Pimpinan RW'        => '#2E7D32',
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
