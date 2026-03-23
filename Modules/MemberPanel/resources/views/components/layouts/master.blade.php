<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', $title ?? __('memberpanel::messages.member_panel')) | {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $description ?? __('memberpanel::messages.member_panel_description') }}">
    <meta name="author" content="Reinan Rodrigues - Vertex Solutions LTDA © 2025">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/image/logo_icon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('storage/image/logo_icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('storage/image/logo_icon.png') }}">

    <!-- Fonts (Local) -->
    @preloadFonts

    @stack('head_scripts')

    <!-- Pusher Configuration from Settings -->
    <script>
        window.Laravel = window.Laravel || {};
        @php
            $pusherConfig = \App\Helpers\SettingsHelper::getPusherConfig();
        @endphp
        window.Laravel.pusherKey = @json($pusherConfig['key']);
        window.Laravel.pusherCluster = @json($pusherConfig['cluster']);
        window.Laravel.pusherHost = @json($pusherConfig['host']);
        window.Laravel.pusherPort = @json($pusherConfig['port']);
        window.Laravel.pusherScheme = @json($pusherConfig['scheme']);
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/tours.js'])

    <!-- Font Awesome Pro -->
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <x-loading-overlay />
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('memberpanel::components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-80 transition-all duration-300" id="main-content">
            <!-- Top Navigation -->
            @include('memberpanel::components.navbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <!-- Flash Messages -->
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

    @auth
        @php $cbavInsight = $cbavBotInsight ?? []; @endphp
        <x-gamification::vertex-bot
            :insight="$cbavInsight"
            :insightKey="$cbavInsight['insight_key'] ?? null"
            :dismissUrl="route('memberpanel.cbav-bot.dismiss')"
            :tourId="$cbavBotTourId ?? null"
            :verseRecommendation="$cbavBotVerseRecommendation ?? null"
        />
    @endauth

    <div id="notification-toast-container" class="fixed bottom-4 right-4 z-[100] flex flex-col items-end max-w-sm pointer-events-none" aria-live="polite"></div>

    <script>
        // Theme Management is handled globally by resources/js/theme.js
        // No local script needed here to avoid conflicts.

        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarContainer = document.querySelector('.flex.h-screen.overflow-hidden.fixed');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            if (sidebarToggle && sidebarContainer) {
                sidebarToggle.addEventListener('click', function() {
                    sidebarContainer.classList.toggle('-translate-x-full');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.toggle('hidden');
                    }
                });
            }

            if (sidebarOverlay && sidebarContainer) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebarContainer.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                });
            }
        });

        // Fallback para dropdowns se Alpine.js não estiver disponível
        document.addEventListener('DOMContentLoaded', function() {
            // Notifications dropdown fallback
            const notificationsToggle = document.getElementById('notifications-toggle');
            const notificationsMenu = document.getElementById('notifications-menu');

            if (notificationsToggle && notificationsMenu) {
                // Aguarda um pouco para verificar se Alpine.js está disponível
                setTimeout(function() {
                    if (typeof window.Alpine === 'undefined' || !window.Alpine) {
                        // Fallback JavaScript puro
                        notificationsToggle.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const isHidden = notificationsMenu.style.display === 'none';
                            notificationsMenu.style.display = isHidden ? 'block' : 'none';
                        });

                        // Fechar ao clicar fora
                        document.addEventListener('click', function(e) {
                            if (notificationsMenu && notificationsToggle &&
                                !notificationsToggle.contains(e.target) &&
                                !notificationsMenu.contains(e.target)) {
                                notificationsMenu.style.display = 'none';
                            }
                        });
                    }
                }, 100);
            }

            // User menu dropdown fallback
            const userMenuToggle = document.getElementById('user-menu-toggle');
            const userMenu = document.getElementById('user-menu');

            if (userMenuToggle && userMenu) {
                setTimeout(function() {
                    if (typeof window.Alpine === 'undefined' || !window.Alpine) {
                        // Fallback JavaScript puro
                        userMenuToggle.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const isHidden = userMenu.style.display === 'none';
                            userMenu.style.display = isHidden ? 'block' : 'none';
                        });

                        // Fechar ao clicar fora
                        document.addEventListener('click', function(e) {
                            if (userMenu && userMenuToggle &&
                                !userMenuToggle.contains(e.target) &&
                                !userMenu.contains(e.target)) {
                                userMenu.style.display = 'none';
                            }
                        });
                    }
                }, 100);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>

