@extends('admin::components.layouts.master')

@section('title', 'Bot Elias — Configuração')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Sistema</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Bot</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Bot Elias</h1>
                <p class="text-gray-300 max-w-xl">Configure o assistente do painel do membro: dicas, leitura do dia, tours e integração com IA (OpenAI ou Google Gemini) para respostas no chat.</p>
            </div>
            <div class="flex-shrink-0">
                <button type="submit" form="cbav-bot-form" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                    <x-icon name="check" class="w-5 h-5 text-amber-600" />
                    Salvar configurações
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
        <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12"></div>
        <div class="relative">
            <div class="p-6 md:p-8 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <x-icon name="robot" class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configuração e administração</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">O Elias exibe dicas, leitura do dia e tours. Com IA ativada, pode responder perguntas usando OpenAI ou Google (Gemini).</p>
                    </div>
                </div>
            </div>

            <form id="cbav-bot-form" action="{{ route('admin.cbav-bot.settings.update') }}" method="POST" class="p-6 md:p-8 space-y-6" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando configurações do bot...' } }))">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 rounded-xl bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/20">
                        <div>
                            <label for="cbav_bot_ai_enabled" class="text-sm font-bold text-gray-900 dark:text-white">Habilitar IA no Bot</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Quando ativado e com API key configurada, o Elias pode usar inteligência artificial (OpenAI ou Google) para respostas no chat.</p>
                        </div>
                        <label class="relative inline-block w-12 h-7 cursor-pointer shrink-0">
                            <input type="hidden" name="cbav_bot_ai_enabled" value="0">
                            <input type="checkbox" name="cbav_bot_ai_enabled" id="cbav_bot_ai_enabled" value="1" {{ old('cbav_bot_ai_enabled', $aiEnabled) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-12 h-7 bg-gray-200 dark:bg-gray-600 rounded-full peer-checked:bg-amber-500 dark:peer-checked:bg-amber-500 transition-colors"></div>
                            <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5 pointer-events-none"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cbav_bot_ai_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provedor de IA</label>
                            <select name="cbav_bot_ai_provider" id="cbav_bot_ai_provider" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                                <option value="">Nenhum (apenas base local)</option>
                                <option value="openai" {{ old('cbav_bot_ai_provider', $aiProvider) === 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                                <option value="google" {{ old('cbav_bot_ai_provider', $aiProvider) === 'google' ? 'selected' : '' }}>Google (Gemini)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Escolha o provedor para respostas com IA. Requer API key válida configurada abaixo.</p>
                        </div>
                        <div>
                            <label for="cbav_bot_ai_max_tokens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Máximo de tokens (resposta)</label>
                            <input type="number" name="cbav_bot_ai_max_tokens" id="cbav_bot_ai_max_tokens" value="{{ old('cbav_bot_ai_max_tokens', $maxTokens) }}" min="100" max="2000" step="50"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Limite de tamanho da resposta da IA (100–2000). Valores maiores permitem respostas mais longas.</p>
                        </div>
                    </div>

                    <div>
                        <label for="cbav_bot_ai_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">API Key</label>
                        <input type="password" name="cbav_bot_ai_api_key" id="cbav_bot_ai_api_key" value="" placeholder="{{ $aiApiKeyMasked ?: 'Cole sua API key aqui' }}"
                            autocomplete="off"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe em branco para não alterar a chave já configurada. Para OpenAI: chave em platform.openai.com. Para Google: chave da API Gemini no Google AI Studio.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                        <x-icon name="check" class="w-5 h-5" />
                        Salvar configurações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sobre o Bot -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8"></div>
        <div class="relative">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                <x-icon name="information-circle" class="w-5 h-5 text-amber-500" />
                Sobre o Bot Elias
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                O Elias é o assistente que aparece no painel do membro com dicas contextuais, recomendação de leitura do dia e tours. Sempre que a IA estiver desativada ou sem API key, ele usa apenas a base local (insights, Bíblia). Os membros podem desativar as dicas do bot no próprio perfil.
            </p>
        </div>
    </div>
</div>
@endsection
