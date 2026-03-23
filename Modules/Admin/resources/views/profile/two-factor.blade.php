@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Autenticação em Duas Etapas (2FA)';
@endphp

@section('title', 'Autenticação em Duas Etapas (2FA)')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-green-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Segurança</span>
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">2FA</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Autenticação em Duas Etapas (2FA)</h1>
                    <p class="text-gray-300 max-w-xl">Proteja sua conta com um código de 6 dígitos gerado no Google Authenticator ou Microsoft Authenticator. No login será solicitado o código além da senha.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.profile.show') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar ao Perfil
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800 flex items-center gap-3">
                <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('info'))
            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-800 flex items-center gap-3">
                <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">{{ session('info') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800 flex items-center gap-3">
                <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" />
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative">
            @if($user->hasTwoFactorEnabled())
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center">
                        <x-icon name="shield-check" class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">2FA ativo</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sua conta está protegida. No login será solicitado o código do aplicativo.</p>
                    </div>
                </div>
                <form action="{{ route('admin.profile.2fa.disable') }}" method="POST" class="mt-6" x-data="{ open: false }"
                    onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Desativando 2FA...' } }))">
                    @csrf
                    <button type="button" @click="open = true" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <x-icon name="shield-exclamation" class="w-5 h-5 mr-2" />
                        Desativar 2FA
                    </button>
                    <template x-teleport="body">
                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                            <div @click.outside="open = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 max-w-md w-full">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Desativar autenticação em duas etapas</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Digite sua senha para confirmar.</p>
                                <input type="password" name="password" required autocomplete="current-password"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white mb-4"
                                    placeholder="Sua senha">
                                @error('password')
                                    <p class="text-sm text-red-600 dark:text-red-400 mb-2">{{ $message }}</p>
                                @enderror
                                <div class="flex gap-2 justify-end">
                                    <button type="button" @click="open = false" class="px-4 py-2 rounded-xl font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700">Desativar</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            @else
                @if($qrPngBase64)
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <x-icon name="qrcode" class="w-6 h-6" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Escaneie o QR Code</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Use o Google Authenticator ou Microsoft Authenticator para escanear e depois informe o código de 6 dígitos.</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-8 items-start">
                        <div class="flex-shrink-0 p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-600">
                            <img src="{{ $qrPngBase64 }}" alt="QR Code 2FA" class="w-48 h-48">
                        </div>
                        <div class="flex-1 w-full">
                            <form action="{{ route('admin.profile.2fa.confirm') }}" method="POST" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Verificando código...' } }))">
                                @csrf
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código de 6 dígitos</label>
                                <input type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code"
                                    class="w-full max-w-xs px-4 py-3 text-center text-lg tracking-[0.4em] font-mono border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="000000">
                                @error('code')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <x-icon name="check" class="w-5 h-5 mr-2" />
                                    Confirmar e ativar 2FA
                                </button>
                            </form>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                <a href="{{ route('admin.profile.2fa.setup') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Gerar novo QR Code</a> se o anterior expirou.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center">
                            <x-icon name="shield-exclamation" class="w-6 h-6" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">2FA desativado</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Ative para exigir um código do aplicativo (Google ou Microsoft Authenticator) no login.</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.profile.2fa.setup') }}" method="POST" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Preparando QR Code...' } }))">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                            <x-icon name="shield-check" class="w-5 h-5 mr-2" />
                            Ativar 2FA (mostrar QR Code)
                        </button>
                    </form>
                @endif
            @endif
            </div>
        </div>
    </div>
@endsection
