<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SystemSetting extends Model
{
    use HasUuids, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('pengaturan')
            ->logOnly(['key', 'value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => 'Mengubah pengaturan sistem.');
    }

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected const CACHE_KEY = 'system_settings_all';

    /**
     * Default values used when a setting has not been stored yet.
     */
    public static function defaults(): array
    {
        return [
            // general
            'instance_name'    => ['value' => 'RW 21 Tanimulya', 'type' => 'string', 'group' => 'general'],
            'domain'           => ['value' => 'rw21.wargadigi.id', 'type' => 'string', 'group' => 'general'],
            'description'      => ['value' => 'Platform digital untuk warga RW 21 Desa Tanimulya', 'type' => 'string', 'group' => 'general'],
            'logo'             => ['value' => null, 'type' => 'string', 'group' => 'general'],
            'logo_kop'         => ['value' => null, 'type' => 'string', 'group' => 'general'],
            'gambar_login'     => ['value' => null, 'type' => 'string', 'group' => 'general'],
            'ikon_aplikasi'    => ['value' => null, 'type' => 'string', 'group' => 'general'],
            // notification
            'notif_whatsapp'   => ['value' => true, 'type' => 'boolean', 'group' => 'notification'],
            'notif_email'      => ['value' => true, 'type' => 'boolean', 'group' => 'notification'],
            'notif_push'       => ['value' => false, 'type' => 'boolean', 'group' => 'notification'],
            // security
            'two_factor'       => ['value' => true, 'type' => 'boolean', 'group' => 'security'],
            'session_timeout'  => ['value' => 30, 'type' => 'integer', 'group' => 'security'],
            // maintenance
            'maintenance_mode' => ['value' => false, 'type' => 'boolean', 'group' => 'maintenance'],
            // persetujuan konten: true = perlu persetujuan sebelum tayang, false = langsung terbit
            'konten_approval_berita'     => ['value' => true, 'type' => 'boolean', 'group' => 'konten'],
            'konten_approval_agenda'     => ['value' => true, 'type' => 'boolean', 'group' => 'konten'],
            'konten_approval_pengumuman' => ['value' => true, 'type' => 'boolean', 'group' => 'konten'],
            // halaman utama (banner + footer), dikelola App\Services\HalamanUtama
            'halaman_utama' => ['value' => null, 'type' => 'json', 'group' => 'halaman_utama'],
        ];
    }

    /**
     * Cast a stored string value into its declared PHP type.
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json'    => json_decode((string) $value, true),
            default   => $value,
        };
    }

    /**
     * Get a single setting value (falls back to defaults).
     */
    public static function get(string $key, $default = null)
    {
        $all = static::all_settings();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        $defaults = static::defaults();

        return $defaults[$key]['value'] ?? $default;
    }

    /**
     * Daftar gambar yang dapat diganti dari Pengaturan Sistem.
     * 'bawaan' = berkas bawaan di folder public bila belum ada unggahan.
     */
    public const GAMBAR = [
        'logo' => [
            'label'  => 'Logo Instance',
            'info'   => 'Dipakai di sidebar, navbar, ikon tab browser, dan halaman pemeliharaan.',
            'bawaan' => 'images/logo warga-digi.png',
            'aturan' => ['image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'format' => 'PNG/JPG/SVG/WEBP, maksimal 2 MB.',
            'accept' => '.png,.jpg,.jpeg,.svg,.webp',
        ],
        'logo_kop' => [
            'label'  => 'Logo Kop Surat dan Resi',
            'info'   => 'Dicetak pada kop surat keterangan dan resi pembayaran (PDF).',
            'bawaan' => 'images/logo_rw21.png',
            'aturan' => ['image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'format' => 'PNG/JPG, maksimal 2 MB (WEBP dan SVG tidak didukung pembuat PDF).',
            'accept' => '.png,.jpg,.jpeg',
        ],
        'gambar_login' => [
            'label'  => 'Gambar Halaman Login',
            'info'   => 'Foto di sisi kiri halaman masuk (tampil pada layar lebar).',
            'bawaan' => 'images/auth-hero.jpg',
            'aturan' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=800,min_height=600'],
            'format' => 'PNG/JPG/WEBP, minimal 800 x 600 piksel, maksimal 4 MB.',
            'accept' => '.png,.jpg,.jpeg,.webp',
        ],
        'ikon_aplikasi' => [
            'label'  => 'Ikon Aplikasi',
            'info'   => 'Ikon saat aplikasi dipasang di ponsel (PWA), layar offline, dan notifikasi. Dipotong persegi dari bagian tengah.',
            'bawaan' => 'icons/icon-512.png',
            'aturan' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=512,min_height=512'],
            'format' => 'PNG/JPG/WEBP, minimal 512 x 512 piksel, maksimal 4 MB.',
            'accept' => '.png,.jpg,.jpeg,.webp',
        ],
    ];

    /** Gambar unggahan untuk kunci ini bila berkasnya benar-benar ada (lewat tautan public/storage), selain itu null. */
    public static function gambarUnggahan(string $kunci): ?string
    {
        $path = static::get($kunci);
        if (empty($path) || ! is_string($path)) {
            return null;
        }

        return (Storage::disk('public')->exists($path) && file_exists(public_path('storage/' . $path))) ? $path : null;
    }

    /** True bila gambar kustom aktif (bukan bawaan). */
    public static function gambarKustom(string $kunci): bool
    {
        return static::gambarUnggahan($kunci) !== null;
    }

    /** Path gambar relatif terhadap folder public, untuk PDF (public_path) dan asset(). Bawaan bila belum ada unggahan. */
    public static function gambarPath(string $kunci): string
    {
        $unggahan = static::gambarUnggahan($kunci);

        return $unggahan ? 'storage/' . $unggahan : (static::GAMBAR[$kunci]['bawaan'] ?? '');
    }

    /** URL gambar (kustom atau bawaan), diberi versi waktu ubah agar cache browser ikut diperbarui. */
    public static function gambarUrl(string $kunci): string
    {
        $path = static::gambarPath($kunci);
        $url = asset($path);
        $file = public_path($path);

        return is_file($file) ? $url . '?v=' . filemtime($file) : $url;
    }

    /**
     * Get the URL of the current active logo (uploaded or default fallback).
     */
    public static function getLogoUrl(): string
    {
        return static::gambarUrl('logo');
    }
    /**
     * Return every setting as a flat key => (cast) value array,
     * merging stored rows over the declared defaults.
     */
    public static function all_settings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $defaults = collect(static::defaults())->map(fn ($d) => $d['value'])->all();

            $stored = static::query()->get()->mapWithKeys(function (SystemSetting $s) {
                return [$s->key => static::castValue($s->value, $s->type)];
            })->all();

            return array_merge($defaults, $stored);
        });
    }

    /**
     * Persist a single setting, inferring/keeping its type & group.
     */
    public static function set(string $key, $value): void
    {
        $defaults = static::defaults();
        $type = $defaults[$key]['type'] ?? (is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : 'string'));
        $group = $defaults[$key]['group'] ?? 'general';

        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json'    => json_encode($value),
            default   => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type, 'group' => $group]
        );

        static::flushCache();
    }

    /**
     * Persist many settings at once.
     */
    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            static::set($key, $value);
        }
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }
}
