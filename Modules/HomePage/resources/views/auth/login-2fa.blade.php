@extends('homepage::components.layouts.master')

@php
    $logoPath = \App\Models\Settings::get('logo_path', 'storage/image/logo_oficial.png');
    $siteName = \App\Models\Settings::get('site_name', 'Igreja Batista Avenida');
    $hideNavFooter = true;
@endphp

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50 dark:bg-gray-950 px-4 py-12">
        <a href="{{ route('homepage.index') }}"
           class="absolute top-6 left-6 z-50 flex items-center gap-2 py-2 px-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full shadow-lg border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all group active:scale-95">
            <x-icon name="arrow-left" style="duotone" class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
            <span class="text-xs font-bold uppercase tracking-wider">Voltar ao Início</span>
        </a>

        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-md bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden z-10 p-8 sm:p-12">
            <div class="mb-8 text-center">
                <a href="{{ route('homepage.index') }}" class="inline-flex justify-center group mb-6">
                    <img src="{{ asset($logoPath) }}"
                         alt="{{ $siteName }}"
                         class="h-14 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                         onerror="this.src='/storage/image/logo_oficial.png';">
                </a>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Código de verificação</h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Digite o código de 6 dígitos do seu aplicativo (Google ou Microsoft Authenticator).</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-700">
                    <ul class="list-disc list-inside text-xs text-red-800 dark:text-red-400 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.2fa.verify') }}" method="POST" class="space-y-6" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Verificando código...' } }))">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Código de 6 dígitos</label>
                    <input type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code" autofocus
                        class="block w-full px-4 py-3 text-center text-xl tracking-[0.4em] font-mono border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="000000">
                </div>
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold rounded-2xl transition-colors">
                    Verificar e entrar
                </button>
            </form>

            <p class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">Voltar ao login</a>
            </p>
        </div>
    </div>
@endsection
