<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{
    use HasFactory;

    /** Cache TTL: 24 horas para configurações globais (performance). */
    public const CACHE_TTL_SECONDS = 86400; // 24 * 3600

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a setting value by key (cached 24h; cleared on set/clearCache).
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($key, $default) {
            try {
                $setting = self::where('key', $key)->first();
                if (! $setting) {
                    return $default;
                }

                $value = self::castValue($setting->value, $setting->type);

                // Return default if value is empty (for string and text types)
                if (($setting->type === 'string' || $setting->type === 'text') && empty($value)) {
                    return $default;
                }

                return $value;
            } catch (\Exception $e) {
                // Return default if database is not available or table doesn't exist
                return $default;
            }
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', ?string $description = null): void
    {
        try {
            $setting = self::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                    'type' => $type,
                    'group' => $group,
                    'description' => $description,
                ]
            );

            // Cache buster: limpa para mudanças refletirem imediatamente
            Cache::forget("setting_{$key}");
        } catch (\Exception $e) {
            // Log or ignore
        }
    }

    /**
     * Set multiple settings at once
     */
    public static function setMany(array $settings): void
    {
        if (empty($settings)) {
            return;
        }

        $now = now();
        $upsertData = [];
        $keys = [];

        foreach ($settings as $setting) {
            $upsertData[] = [
                'key' => $setting['key'],
                'value' => is_bool($setting['value']) ? ($setting['value'] ? '1' : '0') : (string) $setting['value'],
                'type' => $setting['type'] ?? 'string',
                'group' => $setting['group'] ?? 'general',
                'description' => $setting['description'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $keys[] = "setting_{$setting['key']}";
        }

        self::upsert($upsertData, ['key'], ['value', 'type', 'group', 'description', 'updated_at']);

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        try {
            return self::where('group', $group)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => self::castValue($setting->value, $setting->type)];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Clear all settings cache (Cache Buster: chamado após salvar configurações no Admin).
     */
    public static function clearCache(): void
    {
        try {
            $keys = self::pluck('key');
            foreach ($keys as $key) {
                Cache::forget("setting_{$key}");
            }
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }
    }
}
