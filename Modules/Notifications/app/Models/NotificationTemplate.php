<?php

namespace Modules\Notifications\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';

    protected $fillable = [
        'uuid',
        'key',
        'name',
        'subject',
        'body',
        'channels',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'channels' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Render body with given variables (Blade or simple placeholders).
     */
    public function render(array $variables = []): string
    {
        $body = $this->body;
        foreach ($variables as $key => $value) {
            $body = str_replace(['{{ '.$key.' }}', '{{'.$key.'}}'], (string) $value, $body);
        }

        return $body;
    }

    protected static function booted(): void
    {
        static::creating(function (NotificationTemplate $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
