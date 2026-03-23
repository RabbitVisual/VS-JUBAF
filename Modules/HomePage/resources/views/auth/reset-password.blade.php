@extends('homepage::components.layouts.master')

@php
    $logoPath = \App\Models\Settings::get('logo_path', 'storage/image/logo_oficial.png');
    $siteName = \App\Models\Settings::get('site_name', 'Igreja Batista Avenida');
    $hideNavFooter = true;
@endphp

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50 dark:bg-gray-950 px-4 py-12">
        <!-- Floating Back to Home Button -->
        <a href="{{ route('homepage.index') }}"
           class="absolute top-6 left-6 z-50 flex items-center gap-2 py-2 px-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full shadow-lg border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all group active:scale-95">
            <x-icon name="arrow-left" style="duotone" class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
            <span class="text-xs font-bold uppercase tracking-wider">Voltar ao Início</span>
        </a>

        <!-- Background Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-md bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 p-8 sm:p-12 z-10">
            <div class="mb-10 text-center">
                <a href="{{ route('homepage.index') }}" class="inline-flex items-center group mb-6">
                    <img src="{{ asset($logoPath) }}"
                         alt="{{ $siteName }}"
                         class="h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                         onerror="this.src='/storage/image/logo_oficial.png';">
                </a>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Criar Nova Senha</h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Escolha uma senha forte para sua segurança</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-700">
                    <div class="flex">
                        <div class="ml-3">
                            <ul class="list-disc list-inside text-xs text-red-800 dark:text-red-400 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 ml-1">Confirme seu E-mail</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ request()->email }}" required
                            class="block w-full pl-11 pr-4 px-3 py-3 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-gray-600 sm:text-sm"
                            placeholder="seu@email.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 ml-1">Nova Senha</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="block w-full pl-11 pr-4 px-3 py-3 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-gray-600 sm:text-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 ml-1">Confirmar Senha</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-icon name="check" style="duotone" class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="block w-full pl-11 pr-4 px-3 py-3 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-gray-600 sm:text-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-4 px-4 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl shadow-lg shadow-blue-500/20 font-bold transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                    Atualizar Senha
                </button>
            </form>
        </div>
    </div>
@endsection

