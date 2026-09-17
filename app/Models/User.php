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

    /**
     * Get the user's name (resolves from Penduduk if Warga, else username).
     */
    public function getNameAttribute()
    {
        if ($this->role === 'Warga') {
            $penduduk = \App\Models\Penduduk::where('nik', $this->nik)->first();
            if ($penduduk) {
                return $penduduk->nama_lengkap;
            }
        }
        return $this->username;
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
     * Assign the Spatie role matching the current `role` column value.
     */
    public function syncSpatieRole(): void
    {
        if (empty($this->role) || ! class_exists(\Spatie\Permission\Models\Role::class)) {
            return;
        }

        try {
            \Spatie\Permission\Models\Role::findOrCreate($this->role, 'web');
            $this->syncRoles([$this->role]);
        } catch (\Throwable $e) {
            // Permission tables may not exist yet (e.g. during initial migration). Ignore silently.
        }
    }
}
