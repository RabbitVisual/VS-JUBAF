<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manutenção - Intercessão</title>
    <!-- Fonts (Local) -->`n        @preloadFonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome Pro -->
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
            <div>
                <div class="mx-auto h-24 w-24 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                    <x-icon name="vial-circle-check" style="duotone" class="h-12 w-12 text-yellow-600 dark:text-yellow-400" />
                </div>
                <h2 class="mt-6 text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Portal em Manutenção</h2>
                <p class="mt-4 text-sm font-medium text-gray-600 dark:text-gray-400">
                    O módulo de **Intercessão** está passando por uma atualização necessária. <br>Nossos intercessores voltarão em breve!
                </p>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('memberpanel.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                    <x-icon name="arrow-left" style="duotone" class="w-5 h-5" />
                    Voltar para o Painel
                </a>
            </div>
        </div>
    </div>
</body>
</html>

