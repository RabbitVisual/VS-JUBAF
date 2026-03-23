@extends('admin::components.layouts.master')

@section('title', 'Configurações do E-mail de Recuperação')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Segurança</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">E-mail</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Configurações do E-mail de Recuperação</h1>
                <p class="text-gray-300 max-w-xl">Personalize o assunto, o título interno e o rodapé da mensagem enviada quando um usuário solicita recuperação de senha.</p>
            </div>
            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="{{ route('admin.password-resets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar
                </a>
                <button type="submit" form="password-resets-settings-form" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                    <x-icon name="check" class="w-5 h-5 text-blue-600" />
                    Salvar Alterações
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
        <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
        <div class="relative p-8">
            <form id="password-resets-settings-form" action="{{ route('admin.password-resets.settings.update') }}" method="POST" class="space-y-6" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando configurações...' } }))">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="recovery_email_subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assunto do E-mail <span class="text-red-500">*</span></label>
                        <input type="text" name="recovery_email_subject" id="recovery_email_subject" value="{{ old('recovery_email_subject', $settings['recovery_email_subject']) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">O texto que o usuário verá na caixa de entrada.</p>
                    </div>

                    <div>
                        <label for="recovery_email_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título Interno (Header) <span class="text-red-500">*</span></label>
                        <input type="text" name="recovery_email_title" id="recovery_email_title" value="{{ old('recovery_email_title', $settings['recovery_email_title']) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">O título que aparece no topo do card do e-mail.</p>
                    </div>

                    <div>
                        <label for="recovery_email_footer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rodapé (Footer) <span class="text-red-500">*</span></label>
                        <input type="text" name="recovery_email_footer" id="recovery_email_footer" value="{{ old('recovery_email_footer', $settings['recovery_email_footer']) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Informações de suporte ou branding no rodapé da mensagem.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                        <x-icon name="check" class="w-5 h-5" />
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Box -->
    <div class="bg-gray-50 dark:bg-gray-800/50 p-8 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2 mb-4 text-gray-500 dark:text-gray-400">
            <x-icon name="eye" class="w-5 h-5" />
            <span class="text-xs font-bold uppercase tracking-wider">Pré-visualização do Estilo</span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Aparência aproximada do e-mail que o usuário receberá (layout ilustrativo).</p>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden max-w-sm mx-auto border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-900 dark:bg-gray-900 p-4 text-center">
                <span class="text-white font-bold text-xs">LOGOTIPO</span>
            </div>
            <div class="p-6">
                <div class="h-2 w-20 bg-gray-100 dark:bg-gray-700 rounded mb-4"></div>
                <div class="h-4 w-full bg-gray-50 dark:bg-gray-700/50 rounded mb-2"></div>
                <div class="h-4 w-2/3 bg-gray-50 dark:bg-gray-700/50 rounded mb-6"></div>
                <div class="h-10 w-full bg-blue-600 rounded-xl"></div>
            </div>
        </div>
    </div>
</div>
@endsection
