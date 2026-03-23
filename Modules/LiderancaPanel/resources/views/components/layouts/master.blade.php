<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', $title ?? 'Gabinete Pastoral') | {{ \App\Models\Settings::get('site_name', config('app.name', 'Laravel')) }}</title>

    <meta name="description" content="{{ $description ?? 'Gabinete Pastoral - Cuidado e Alimentação do Rebanho' }}">
    <meta name="author" content="Reinan Rodrigues - Vertex Solutions LTDA © 2025">

    @php
        $favicon = \App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ asset($favicon) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset($favicon) }}">
    <link rel="apple-touch-icon" href="{{ asset($favicon) }}">

    @preloadFonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">

    <script>
        window.Laravel = window.Laravel || {};
        @php
            $pusherConfig = \App\Helpers\SettingsHelper::getPusherConfig();
        @endphp
        window.Laravel.pusherKey = @json($pusherConfig['key'] ?? '');
        window.Laravel.pusherCluster = @json($pusherConfig['cluster'] ?? '');
        window.Laravel.pusherHost = @json($pusherConfig['host'] ?? '');
        window.Laravel.pusherPort = @json($pusherConfig['port'] ?? '');
        window.Laravel.pusherScheme = @json($pusherConfig['scheme'] ?? '');
    </script>
</head>

<body class="antialiased bg-slate-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 overflow-hidden m-0">
    <x-loading-overlay />
    <div class="flex h-screen overflow-hidden">
        @include('liderancapanel::components.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden lg:ml-72 transition-all duration-300">
            @include('liderancapanel::components.navbar')

            <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900 p-6">
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" class="mb-4" />
                @endif
                @if (session('error'))
                    <x-alert type="error" :message="session('error')" class="mb-4" />
                @endif
                @if (session('warning'))
                    <x-alert type="warning" :message="session('warning')" class="mb-4" />
                @endif
                @if (session('info'))
                    <x-alert type="info" :message="session('info')" class="mb-4" />
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div id="notification-toast-container"
        class="fixed bottom-4 right-4 z-[100] flex flex-col items-end max-w-sm pointer-events-none" aria-live="polite">
    </div>

    @stack('scripts')
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    if (sidebarOverlay) sidebarOverlay.classList.toggle('hidden');
                });
            }
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (sidebar) sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                });
            }
            if (document.getElementById('theme-toggle')) {
                document.getElementById('theme-toggle').addEventListener('click', function() {
                    document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ?
                        'dark' : 'light');
                });
            }
        });
    </script>
</body>

</html>
