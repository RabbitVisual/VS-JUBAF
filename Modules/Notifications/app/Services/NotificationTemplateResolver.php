<?php

namespace Modules\Notifications\App\Services;

use Modules\Notifications\App\Models\NotificationTemplate;

class NotificationTemplateResolver
{
    public function resolve(string $key, array $variables = []): ?array
    {
        $template = NotificationTemplate::where('key', $key)->where('is_active', true)->first();

        if (! $template) {
            return null;
        }

        return [
            'subject' => $this->replace($template->subject ?? '', $variables),
            'body' => $template->render($variables),
        ];
    }

    protected function replace(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace(['{{ '.$key.' }}', '{{'.$key.'}}'], (string) $value, $text);
        }

        return $text;
    }
}
