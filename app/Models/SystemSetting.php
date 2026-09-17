<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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
            // notification
            'notif_whatsapp'   => ['value' => true, 'type' => 'boolean', 'group' => 'notification'],
            'notif_email'      => ['value' => true, 'type' => 'boolean', 'group' => 'notification'],
            'notif_push'       => ['value' => false, 'type' => 'boolean', 'group' => 'notification'],
            // security
            'two_factor'       => ['value' => true, 'type' => 'boolean', 'group' => 'security'],
            'session_timeout'  => ['value' => 30, 'type' => 'integer', 'group' => 'security'],
            // maintenance
            'maintenance_mode' => ['value' => false, 'type' => 'boolean', 'group' => 'maintenance'],
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
