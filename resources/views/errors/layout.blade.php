<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ config('app.name', 'Vertex CBAV') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome Pro 7.1 -->
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">

    <style>
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="h-full antialiased bg-slate-950 text-slate-200 overflow-hidden">
    <!-- Dynamic Background -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute top-[20%] right-[10%] w-[30%] h-[30%] bg-indigo-600/10 blur-[100px] rounded-full"></div>

        <!-- Grid Pattern -->
        <div class="absolute inset-0 opacity-[0.03]"
             style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;">
        </div>
    </div>

    <div class="relative min-h-full flex items-center justify-center p-6">
        <x-loading-overlay />

        <div class="max-w-4xl w-full">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
