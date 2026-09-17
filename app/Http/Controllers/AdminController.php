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
        $stats = [
            ['label' => 'AKTIF HARI INI', 'value' => 1, 'total' => 416, 'icon' => 'bi-check-circle', 'color' => '#43A047'],
            ['label' => 'TOTAL KEPALA KELUARGA', 'value' => 416, 'icon' => 'bi-people-fill', 'color' => '#FF9800'],
            ['label' => 'TOTAL PENDUDUK', 'value' => 1446, 'icon' => 'bi-person-badge-fill', 'color' => '#0288D1'],
        ];

        $alerts = [
            [
                'title' => 'Instans Diaktifkan',
                'desc' => 'RW 21 Tanimulya telah berhasil menyelesaikan proses pembaruan DNS.',
                'time' => '2 jam yang lalu',
                'type' => 'success',
            ],
        ];

        $instances = [
            [
                'name' => 'RW 21 Tanimulya',
                'domain' => 'rw21.wargadigi.id',
                'status' => 'Active',
                'users' => 452,
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

        // Available roles come from Spatie (falls back to the users enum list).
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name')->all();
        if (empty($roles)) {
            $roles = $this->availableRoles();
        }

        return view('admin.manajemen-hak-akses', compact('users', 'roles', 'search'));
    }

    /**
     * Update a single user's role (kept in sync between the `role` column and Spatie).
     */
    public function updateHakAkses(Request $request, string $id)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in($this->availableRoles())],
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->update(['role' => $validated['role']]);
        // syncSpatieRole() is triggered automatically via the model's saved() hook.

        return redirect()
            ->route('admin.manajemen-hak-akses')
            ->with('success', 'Hak akses ' . ($user->username ?? $user->nik) . ' berhasil diperbarui menjadi ' . $validated['role'] . '.');
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
            $query->where('properties->category', $category);
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
        $name = $causer?->name ?? ($causer?->username ?? 'Sistem');
        $role = $causer?->role ?? 'SISTEM';
        $props = $activity->properties ?? collect();
        $status = $props['status'] ?? 'Sukses';
        $category = $props['category'] ?? ($activity->log_name ?: 'lainnya');

        // Icon + display type by log_name / event.
        [$activityType, $icon] = $this->activityDisplay($activity->log_name, $activity->event);

        $created = $activity->created_at;

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
            'status_class' => $status === 'Gagal' ? 'danger' : 'success',
            'category' => $category,
        ];
    }

    protected function activityDisplay(?string $logName, ?string $event): array
    {
        return match ($logName) {
            'auth'       => ['Login / Logout', 'bi-box-arrow-in-right'],
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
