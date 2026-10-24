<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles, LogsActivity;

    /**
     * Configure how model changes are recorded in the activity log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('pengguna')
            ->logOnly(['username', 'nik', 'role', 'status_akun', 'rt_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Menambahkan pengguna baru.',
                'updated' => 'Memperbarui data pengguna.',
                'deleted' => 'Menghapus pengguna.',
                default   => "Pengguna di-{$eventName}.",
            });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        'username',
        'nik_verified_at',
        'role',
        'password',
        'status_akun',
        'last_login',
        'rt_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Session key untuk menyimpan peran aktif (active role) per sesi login. */
    public const ACTIVE_ROLE_SESSION_KEY = 'active_role';

    /**
     * Get the user's name (resolves from Penduduk if Warga, else username).
     */
    public function getNameAttribute()
    {
        if ($this->activeRole() === 'Warga' && $this->nik) {
            $penduduk = \App\Models\Penduduk::where('nik', $this->nik)->first();
            if ($penduduk) {
                return $penduduk->nama_lengkap;
            }
        }
        return $this->username;
    }

    /**
     * Daftar seluruh peran yang dimiliki akun ini.
     *
     * Sumber utama: Spatie roles (mendukung banyak peran per akun).
     * Fallback: kolom `role` legacy bila Spatie belum tersemat (mis. saat test
     * tanpa seed permission). Hasil selalu unik dan terurut stabil.
     *
     * @return array<int, string>
     */
    public function roleNames(): array
    {
        $names = [];

        try {
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $names = $this->getRoleNames()->all();
            }
        } catch (\Throwable $e) {
            // Tabel permission mungkin belum ada; abaikan dan pakai fallback.
        }

        if (empty($names) && ! empty($this->role)) {
            $names = [$this->role];
        }

        return array_values(array_unique($names));
    }

    /**
     * Apakah akun memiliki lebih dari satu peran (butuh pemilihan saat login).
     */
    public function hasMultipleRoles(): bool
    {
        return count($this->roleNames()) > 1;
    }

    /**
     * Peran aktif untuk sesi berjalan.
     *
     * Mengambil dari session bila ada dan valid (termasuk salah satu peran
     * yang dimiliki akun). Jika tidak, fallback ke satu-satunya peran yang
     * dimiliki, lalu ke kolom `role` legacy. Dengan begitu seluruh kode lama
     * yang membaca peran tetap bekerja melalui satu sumber kebenaran.
     */
    public function activeRole(): ?string
    {
        $available = $this->roleNames();

        // Hanya baca session untuk user yang sedang login (hindari bocor antar-user).
        if (\Illuminate\Support\Facades\Auth::id() === $this->getKey()) {
            $sessionRole = session(self::ACTIVE_ROLE_SESSION_KEY);
            if ($sessionRole && in_array($sessionRole, $available, true)) {
                return $sessionRole;
            }
        }

        if (count($available) === 1) {
            return $available[0];
        }

        return $this->role ?: ($available[0] ?? null);
    }

    /**
     * Set peran aktif ke session (hanya bila peran tsb dimiliki akun).
     */
    public function setActiveRole(string $role): bool
    {
        if (! in_array($role, $this->roleNames(), true)) {
            return false;
        }

        session([self::ACTIVE_ROLE_SESSION_KEY => $role]);

        return true;
    }

    /**
     * Hapus peran aktif dari session (dipanggil saat logout / ganti peran).
     */
    public static function clearActiveRole(): void
    {
        session()->forget(self::ACTIVE_ROLE_SESSION_KEY);
    }

    /**
     * Get the user's rt_id, falling back to their Keluarga or MasterRt if null.
     */
    public function getRtIdAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->nik && in_array(trim($this->activeRole() ?? ''), ['Ketua RT', 'Op Keuangan RT', 'Op. Keuangan RT', 'Bendahara RT', 'Op Konten RT', 'Op. Konten RT', 'Warga'], true)) {
            $penduduk = \App\Models\Penduduk::where('nik', $this->nik)->first();
            if ($penduduk && $penduduk->keluarga_id) {
                $keluarga = \App\Models\Keluarga::find($penduduk->keluarga_id);
                if ($keluarga && $keluarga->rt_id) {
                    return $keluarga->rt_id;
                }
            }

            $rt01 = \App\Models\MasterRt::where('kode_rt', '01')->first() ?? \App\Models\MasterRt::first();
            if ($rt01) {
                return $rt01->id;
            }
        }

        return null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nik_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function penduduk()
    {
        return $this->hasOne(Penduduk::class, 'nik', 'nik');
    }

    public function umkmUsaha()
    {
        return $this->hasMany(UmkmUsaha::class, 'nik', 'nik');
    }

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    /**
     * Keep the Spatie role assignment in sync with the legacy `role` column.
     * The `role` column stays the source of truth used by middleware & sidebar,
     * while Spatie provides the formal RBAC layer.
     */
    protected static function booted(): void
    {
        static::saved(function (User $user): void {
            if (! class_exists(\Spatie\Permission\Models\Role::class)) {
                return;
            }

            // Only resync when the role column actually changed (or on creation).
            if (! $user->wasRecentlyCreated && ! $user->wasChanged('role')) {
                return;
            }

            $user->syncSpatieRole();
        });
    }

    /**
     * Pastikan peran pada kolom `role` legacy juga tersemat di Spatie.
     *
     * Penting: memakai assignRole (menambahkan, idempoten) — BUKAN syncRoles —
     * agar peran lain yang sudah dimiliki akun (multi-role) tidak terhapus saat
     * kolom `role` berubah. Pengelolaan daftar peran lengkap dilakukan eksplisit
     * (mis. oleh superadmin) via syncRoles di controller terkait.
     */
    public function syncSpatieRole(): void
    {
        if (empty($this->role) || ! class_exists(\Spatie\Permission\Models\Role::class)) {
            return;
        }

        try {
            \Spatie\Permission\Models\Role::findOrCreate($this->role, 'web');
            if (! $this->hasRole($this->role)) {
                $this->assignRole($this->role);
            }
        } catch (\Throwable $e) {
            // Permission tables may not exist yet (e.g. during initial migration). Ignore silently.
        }
    }
}
