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

    <title>@yield('title', $title ?? 'Painel Administrativo') |
        {{ \App\Models\Settings::get('site_name', config('app.name', 'Laravel')) }}</title>

    <meta name="description" content="{{ $description ?? 'Painel Administrativo - Sistema de Controle Global' }}">
    <meta name="author" content="Reinan Rodrigues - Vertex Solutions LTDA © 2025">

    <!-- Favicon -->
    @php
        $favicon = \App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ asset($favicon) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset($favicon) }}">
    <link rel="apple-touch-icon" href="{{ asset($favicon) }}">

    <!-- Fonts (Local) -->
    @preloadFonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome Pro -->
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">

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
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 overflow-hidden m-0">
    <x-loading-overlay />
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin::components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-72 transition-all duration-300">
            <!-- Top Navigation -->
            @include('admin::components.navbar')

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

    <div id="notification-toast-container" class="fixed bottom-4 right-4 z-[100] flex flex-col items-end max-w-sm pointer-events-none" aria-live="polite"></div>

    @stack('scripts')
    @yield('scripts')

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar?.classList.toggle('-translate-x-full');
                    sidebarOverlay?.classList.toggle('hidden');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar?.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                });
            }
        });
    </script>

    <script>
        // Fallback para dropdowns se Alpine.js não estiver disponível
        document.addEventListener('DOMContentLoaded', function() {
            // Admin notifications dropdown fallback
            const adminNotificationsToggle = document.getElementById('admin-notifications-toggle');
            const adminNotificationsMenu = document.getElementById('admin-notifications-menu');

            if (adminNotificationsToggle && adminNotificationsMenu) {
                // Aguarda um pouco para verificar se Alpine.js está disponível
                setTimeout(function() {
                    if (typeof window.Alpine === 'undefined' || !window.Alpine) {
                        // Fallback JavaScript puro
                        adminNotificationsToggle.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const isHidden = adminNotificationsMenu.style.display === 'none';
                            adminNotificationsMenu.style.display = isHidden ? 'block' : 'none';
                        });

                        // Fechar ao clicar fora
                        document.addEventListener('click', function(e) {
                            if (adminNotificationsMenu && adminNotificationsToggle &&
                                !adminNotificationsToggle.contains(e.target) &&
                                !adminNotificationsMenu.contains(e.target)) {
                                adminNotificationsMenu.style.display = 'none';
                            }
                        });
                    }
                }, 100);
            }

            // User menu dropdown fallback (Admin)
            const userMenuBtn = document.querySelector('button[onclick*="user-menu"]');
            const userMenuDiv = document.getElementById('user-menu');

            if (userMenuBtn && userMenuDiv) {
                // Remove o onclick inline e adiciona event listener
                userMenuBtn.removeAttribute('onclick');
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenuDiv.classList.toggle('hidden');
                });

                // Fechar ao clicar fora
                document.addEventListener('click', function(e) {
                    if (userMenuDiv && userMenuBtn &&
                        !userMenuBtn.contains(e.target) &&
                        !userMenuDiv.contains(e.target)) {
                        userMenuDiv.classList.add('hidden');
                    }
                });
            }

            // Initialize Flowbite/Alpine for dropdowns
            if (window.Flowbite) {
                window.Flowbite.init();
            }

            // Fallback para accordions se Alpine.js não estiver disponível
            setTimeout(function() {
                if (typeof window.Alpine === 'undefined' || !window.Alpine) {
                    // Treasury Accordion
                    const treasuryButton = document.querySelector('[data-treasury-toggle]');
                    const treasuryMenu = document.querySelector('[data-treasury-menu]');
                    if (treasuryButton && treasuryMenu) {
                        const isActive = '{{ request()->routeIs('treasury.*') ? 'true' : 'false' }}' ===
                            'true';
                        treasuryMenu.style.display = isActive ? 'block' : 'none';
                        treasuryButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const isHidden = treasuryMenu.style.display === 'none';
                            treasuryMenu.style.display = isHidden ? 'block' : 'none';
                            const icon = treasuryButton.querySelector('svg:last-child');
                            if (icon) {
                                icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                            }
                        });
                    }

                    // Bible Accordion
                    const bibleButtons = document.querySelectorAll('button[onclick*="bibleOpen"]');
                    bibleButtons.forEach(function(button) {
                        const menu = button.nextElementSibling;
                        if (menu && menu.classList.contains('mt-1')) {
                            const isActive =
                                '{{ request()->routeIs('admin.bible*') ? 'true' : 'false' }}' ===
                                'true';
                            menu.style.display = isActive ? 'block' : 'none';
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const isHidden = menu.style.display === 'none';
                                menu.style.display = isHidden ? 'block' : 'none';
                                const icon = button.querySelector('svg:last-child');
                                if (icon) {
                                    icon.style.transform = isHidden ? 'rotate(180deg)' :
                                        'rotate(0deg)';
                                }
                            });
                        }
                    });

                    // ChurchCouncil Accordion
                    const churchcouncilButtons = document.querySelectorAll(
                        'button[onclick*="churchcouncilOpen"]');
                    churchcouncilButtons.forEach(function(button) {
                        const menu = button.nextElementSibling;
                        if (menu && menu.classList.contains('mt-1')) {
                            const isActive =
                                '{{ request()->routeIs('admin.churchcouncil*') ? 'true' : 'false' }}' ===
                                'true';
                            menu.style.display = isActive ? 'block' : 'none';
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const isHidden = menu.style.display === 'none';
                                menu.style.display = isHidden ? 'block' : 'none';
                                const icon = button.querySelector('svg:last-child');
                                if (icon) {
                                    icon.style.transform = isHidden ? 'rotate(180deg)' :
                                        'rotate(0deg)';
                                }
                            });
                        }
                    });

                    // HomePage Accordion
                    const homepageButtons = document.querySelectorAll('button[onclick*="homepageOpen"]');
                    homepageButtons.forEach(function(button) {
                        const menu = button.nextElementSibling;
                        if (menu && menu.classList.contains('mt-1')) {
                            const isActive =
                                '{{ request()->routeIs('admin.homepage*') ? 'true' : 'false' }}' ===
                                'true';
                            menu.style.display = isActive ? 'block' : 'none';
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const isHidden = menu.style.display === 'none';
                                menu.style.display = isHidden ? 'block' : 'none';
                                const icon = button.querySelector('svg:last-child');
                                if (icon) {
                                    icon.style.transform = isHidden ? 'rotate(180deg)' :
                                        'rotate(0deg)';
                                }
                            });
                        }
                    });

                    // EBD Accordion
                    const ebdButtons = document.querySelectorAll('button[onclick*="ebdOpen"]');
                    ebdButtons.forEach(function(button) {
                        const menu = button.nextElementSibling;
                        if (menu && menu.classList.contains('mt-1')) {
                            const isActive =
                                '{{ request()->routeIs('admin.ebd*') ? 'true' : 'false' }}' ===
                                'true';
                            menu.style.display = isActive ? 'block' : 'none';
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const isHidden = menu.style.display === 'none';
                                menu.style.display = isHidden ? 'block' : 'none';
                                const icon = button.querySelector('svg:last-child');
                                if (icon) {
                                    icon.style.transform = isHidden ? 'rotate(180deg)' :
                                        'rotate(0deg)';
                                }
                            });
                        }
                    });
                }
            }, 100);
        });
    </script>


</body>

</html>
