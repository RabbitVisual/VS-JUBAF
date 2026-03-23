<?php

namespace Modules\Intercessor\App\Services;

use App\Models\Settings;

class IntercessorSettings
{
    public static function getAll(): array
    {
        $keys = [
            'module_enabled',
            'require_moderation',
            'allow_comments',
            'notification_days',
            'max_open_requests',
            'allow_private',
            'allow_anonymous',
            'maintenance_mode',
            'allow_requests',
            'max_requests_per_user',
            'require_approval',
            'show_intercessor_names',
            'room_label',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = self::get($key);
        }

        return $settings;
    }

    public static function get($key, $default = null)
    {
        $defaults = [
            'module_enabled' => true,
            'require_moderation' => true,
            'require_approval' => true,
            'allow_comments' => true,
            'notification_days' => 7,
            'max_open_requests' => 5,
            'max_requests_per_user' => 5,
            'allow_private' => true,
            'allow_anonymous' => true,
            'maintenance_mode' => false,
            'allow_requests' => true,
            'show_intercessor_names' => 'author_only', // author_only, intercessors_only, all
            'room_label' => 'Sala de Oração',
        ];

        // Backwards-compatible aliases
        if ($key === 'require_approval') {
            return Settings::get('intercessor_require_moderation', $defaults['require_approval']);
        }

        if ($key === 'max_requests_per_user') {
            return Settings::get('intercessor_max_open_requests', $defaults['max_requests_per_user']);
        }

        return Settings::get("intercessor_{$key}", $defaults[$key] ?? $default);
    }
}
