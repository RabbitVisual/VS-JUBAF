<?php

namespace Modules\Intercessor\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Intercessor\App\Services\IntercessorSettings; // Assuming this service exists or we will create it

class IntercessorSettingsController extends Controller
{
    public function index()
    {
        $settings = IntercessorSettings::getAll();

        $stats = [
            'total_requests' => \Modules\Intercessor\App\Models\PrayerRequest::count(),
            'total_comments' => \Modules\Intercessor\App\Models\PrayerInteraction::where('type', 'comment')->count(),
            'active_categories' => \Modules\Intercessor\App\Models\PrayerCategory::count(),
        ];

        return view('intercessor::admin.settings.index', compact('settings', 'stats'));
    }

    public function update(Request $request)
    {
        $booleanKeys = [
            'require_moderation',
            'allow_comments',
            'allow_private',
            'allow_anonymous',
            'module_enabled',
            'allow_requests',
        ];

        foreach ($booleanKeys as $key) {
            \App\Models\Settings::set("intercessor_{$key}", $request->boolean($key), 'boolean', 'intercessor');
        }

        \App\Models\Settings::set('intercessor_notification_days', $request->input('notification_days', 7), 'integer', 'intercessor');
        \App\Models\Settings::set('intercessor_max_open_requests', $request->input('max_open_requests', 5), 'integer', 'intercessor');

        \App\Models\Settings::set(
            'intercessor_show_intercessor_names',
            $request->input('show_intercessor_names', 'author_only'),
            'string',
            'intercessor'
        );

        \App\Models\Settings::set(
            'intercessor_room_label',
            $request->input('room_label', 'Sala de Oração'),
            'string',
            'intercessor'
        );

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
