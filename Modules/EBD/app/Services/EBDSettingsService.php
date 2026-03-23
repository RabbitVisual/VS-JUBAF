<?php

namespace Modules\EBD\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EBDSettingsService
{
    private const CACHE_PREFIX = 'ebd.settings.';

    private const DB_GROUP = 'ebd';

    private const DEFAULTS = [
        'default_lesson_time' => '09:00',
        'default_bible_version' => 'nvi',
        'attendance_deadline_hours' => 24,
        'evaluation_deadline_days' => 7,
        'auto_create_attendance' => false,
        'send_lesson_reminders' => true,
        'reminder_days_before' => 1,
    ];

    public static function getAllSettings(): array
    {
        $settings = [];
        foreach (self::DEFAULTS as $key => $default) {
            $settings[$key] = self::get($key, $default);
        }

        return $settings;
    }

    public static function get(string $key, $default = null)
    {
        $fallback = $default ?? (self::DEFAULTS[$key] ?? null);

        return Cache::remember(self::CACHE_PREFIX.$key, 3600, function () use ($key, $fallback) {
            $dbKey = 'ebd_'.$key;
            $row = DB::table('settings')->where('key', $dbKey)->first();

            if ($row) {
                return self::castValue($row->value, $row->type);
            }

            return config("ebd.{$key}", $fallback);
        });
    }

    public static function set(string $key, $value): void
    {
        $dbKey = 'ebd_'.$key;
        $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : 'string');
        $storeValue = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        DB::table('settings')->updateOrInsert(
            ['key' => $dbKey],
            [
                'value' => $storeValue,
                'type' => $type,
                'group' => self::DB_GROUP,
                'description' => self::getDescription($key),
                'updated_at' => now(),
            ]
        );

        Cache::forget(self::CACHE_PREFIX.$key);
        Cache::put(self::CACHE_PREFIX.$key, $value, 3600);
    }

    public static function getDefaultLessonTime(): string
    {
        return (string) self::get('default_lesson_time', '09:00');
    }

    public static function getDefaultBibleVersion(): string
    {
        return (string) self::get('default_bible_version', 'nvi');
    }

    public static function getAttendanceDeadlineHours(): int
    {
        return (int) self::get('attendance_deadline_hours', 24);
    }

    public static function getEvaluationDeadlineDays(): int
    {
        return (int) self::get('evaluation_deadline_days', 7);
    }

    public static function shouldAutoCreateAttendance(): bool
    {
        return (bool) self::get('auto_create_attendance', false);
    }

    public static function shouldSendLessonReminders(): bool
    {
        return (bool) self::get('send_lesson_reminders', true);
    }

    public static function getReminderDaysBefore(): int
    {
        return (int) self::get('reminder_days_before', 1);
    }

    private static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            default => $value,
        };
    }

    private static function getDescription(string $key): string
    {
        return match ($key) {
            'default_lesson_time' => 'Horário padrão das lições EBD',
            'default_bible_version' => 'Versão bíblica padrão para lições',
            'attendance_deadline_hours' => 'Prazo em horas para registrar presença',
            'evaluation_deadline_days' => 'Prazo em dias para avaliações',
            'auto_create_attendance' => 'Criar presença automaticamente ao iniciar lição',
            'send_lesson_reminders' => 'Enviar lembretes de lição',
            'reminder_days_before' => 'Dias de antecedência para lembrete',
            default => 'Configuração EBD: '.$key,
        };
    }
}
