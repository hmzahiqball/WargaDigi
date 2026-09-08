<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

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
}
