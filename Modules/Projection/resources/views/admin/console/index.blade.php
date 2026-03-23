<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Console Automático de Projeção - VertexCBAV</title>

    <!-- Font Awesome Pro 7.1 Local -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-pro/css/all.css') }}">

    @vite(['resources/css/app.css', 'Modules/Projection/resources/assets/js/app.js'])
</head>
<body class="bg-slate-950 text-white antialiased overflow-hidden selection:bg-indigo-500/30">
    <div id="projection-app"
         data-user-id="{{ auth()->id() }}"
         data-api-token="{{ auth()->user()->createToken('projection')->plainTextToken }}"
         data-dashboard-url="{{ route('admin.projection.index') }}"
         data-sync-url="{{ url('/api/v1/projection/state') }}"
         data-setlist-id="{{ $setlist?->id ?? '' }}"
         class="h-screen w-screen overflow-hidden bg-slate-950">

        <!-- Initial Load Splash -->
        <div class="flex flex-col items-center justify-center h-screen">
            <div class="relative w-24 h-24 mb-6">
                <div class="absolute inset-0 border-4 border-indigo-500/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-duotone fa-projector text-3xl text-indigo-400"></i>
                </div>
            </div>
            <h2 class="text-xs font-black uppercase tracking-[0.4em] text-slate-500 animate-pulse">
                Carregando Holycris Engine...
            </h2>
        </div>
    </div>
</body>
</html>
