<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Configuração do Bot Elias (CBAV Bot) no painel admin.
 * Permite ativar/desativar IA e escolher provedor (OpenAI / Google Gemini).
 */
class CbavBotSettingsController extends Controller
{
    public function index()
    {
        $aiEnabled = Settings::get('cbav_bot_ai_enabled', config('gamification.bot.ai_enabled', false));
        $aiProvider = Settings::get('cbav_bot_ai_provider', config('gamification.bot.ai_provider', ''));
        $aiApiKey = Settings::get('cbav_bot_ai_api_key', '');
        $maxTokens = (int) Settings::get('cbav_bot_ai_max_tokens', config('gamification.bot.max_ai_tokens', 300));

        return view('admin::cbav-bot.settings', [
            'aiEnabled' => (bool) $aiEnabled,
            'aiProvider' => $aiProvider ?: null,
            'aiApiKey' => $aiApiKey,
            'aiApiKeyMasked' => $aiApiKey !== '' ? '••••••••••••' . substr($aiApiKey, -4) : '',
            'maxTokens' => $maxTokens,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cbav_bot_ai_enabled' => 'nullable|boolean',
            'cbav_bot_ai_provider' => 'nullable|in:openai,google',
            'cbav_bot_ai_api_key' => 'nullable|string|max:500',
            'cbav_bot_ai_max_tokens' => 'nullable|integer|min:100|max:2000',
        ]);

        Settings::set('cbav_bot_ai_enabled', $request->boolean('cbav_bot_ai_enabled'), 'boolean', 'cbav_bot', 'Habilitar IA no Bot Elias');
        Settings::set('cbav_bot_ai_provider', $validated['cbav_bot_ai_provider'] ?? '', 'string', 'cbav_bot', 'Provedor de IA (openai ou google)');
        if ($request->filled('cbav_bot_ai_api_key')) {
            Settings::set('cbav_bot_ai_api_key', $request->cbav_bot_ai_api_key, 'string', 'cbav_bot', 'API Key do provedor de IA');
        }
        Settings::set('cbav_bot_ai_max_tokens', (int) ($validated['cbav_bot_ai_max_tokens'] ?? 300), 'integer', 'cbav_bot', 'Máximo de tokens na resposta da IA');

        Settings::clearCache();
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->route('admin.cbav-bot.settings.index')
            ->with('success', 'Configurações do Bot Elias atualizadas com sucesso.');
    }
}
