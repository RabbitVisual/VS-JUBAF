<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Bible\App\Services\BibleApiService;
use Modules\EBD\App\Services\EBDSettingsService;

class SettingsController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    /**
     * Display EBD settings
     */
    public function index(): View
    {
        $settings = EBDSettingsService::getAllSettings();
        $versions = $this->bibleApi->getVersions();
        $bibleVersions = $versions->map(fn ($v) => [
            'value' => strtolower($v->abbreviation),
            'label' => $v->abbreviation.' - '.$v->name,
            'default' => (bool) ($v->is_default ?? false),
        ])->values()->all();
        if (empty($bibleVersions)) {
            $bibleVersions = [
                ['value' => 'nvi', 'label' => 'NVI - Nova Versão Internacional', 'default' => true],
                ['value' => 'acf', 'label' => 'ACF - Almeida Corrigida Fiel', 'default' => false],
            ];
        }
        return view('ebd::admin.settings.index', compact('settings', 'bibleVersions'));
    }

    /**
     * Update EBD settings
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_lesson_time' => 'required|date_format:H:i',
            'default_bible_version' => 'required|string|max:10',
            'attendance_deadline_hours' => 'required|integer|min:1|max:168',
            'evaluation_deadline_days' => 'required|integer|min:1|max:30',
            'auto_create_attendance' => 'nullable|boolean',
            'send_lesson_reminders' => 'nullable|boolean',
            'reminder_days_before' => 'required|integer|min:0|max:7',
        ]);

        // Convert checkbox values
        $validated['auto_create_attendance'] = $request->has('auto_create_attendance');
        $validated['send_lesson_reminders'] = $request->has('send_lesson_reminders');

        // Store settings using service
        foreach ($validated as $key => $value) {
            EBDSettingsService::set($key, $value);
        }

        return redirect()->route('admin.ebd.settings.index')
            ->with('success', 'Configurações atualizadas com sucesso!');
    }
}
