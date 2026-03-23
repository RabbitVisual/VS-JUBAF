@extends('memberpanel::components.layouts.master')

@section('title', 'Bíblia Digital')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 flex items-center justify-center p-6">
        <div class="max-w-lg w-full">

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-200 dark:border-slate-800 shadow-xl overflow-hidden relative">
                <!-- Decorative Top -->
                <div class="bg-gray-900 dark:bg-slate-950 p-8 text-center relative overflow-hidden">
                     <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
                     <div class="absolute -top-10 -left-10 w-32 h-32 bg-indigo-500 rounded-full blur-[50px] opacity-40"></div>
                     <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-purple-500 rounded-full blur-[50px] opacity-40"></div>

                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/10">
                            <x-icon name="book-bible" class="w-8 h-8 text-white" />
                        </div>
                        <h1 class="text-2xl font-black text-white tracking-tight">Bíblia Digital</h1>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-1">Sistema Indisponível</p>
                    </div>
                </div>

                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-amber-50 dark:bg-amber-900/20 rounded-full mb-4 text-amber-500">
                        <x-icon name="triangle-exclamation" class="w-6 h-6" />
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhuma versão encontrada</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-6 leading-relaxed">
                        Parece que nenhuma versão da Bíblia (NVI, ACF, etc.) foi importada para o sistema ainda. Por favor, entre em contato com a administração.
                    </p>

                    <a href="{{ route('memberpanel.dashboard') }}"
                        class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 dark:bg-slate-800 dark:text-white dark:border-slate-700 dark:hover:bg-slate-700 transition-all shadow-sm">
                        <x-icon name="house" class="w-4 h-4 mr-2" />
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection

