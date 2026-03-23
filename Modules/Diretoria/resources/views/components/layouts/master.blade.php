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

    <title>{{ $title ?? 'Diretoria' }} |
        {{ \App\Models\Settings::get('site_name', config('app.name', 'Laravel')) }}</title>

    <meta name="description" content="{{ $description ?? 'Sistema de Diretoria' }}">
    <meta name="author" content="Reinan Rodrigues - Vertex Solutions LTDA © 2025">

    <!-- Favicon -->
    @php
        $favicon = \App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ asset($favicon) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset($favicon) }}">
    <link rel="apple-touch-icon" href="{{ asset($favicon) }}">

    <!-- Fonts (Local) -->`n @preloadFonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome Pro -->
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <x-loading-overlay />
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col">
            <div
                class="flex flex-col flex-grow bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4 py-6">
                    @php
                        $logo = \App\Models\Settings::get('logo_path', 'storage/image/logo.png');
                    @endphp
                    <img class="h-8 w-auto" src="{{ asset($logo) }}" alt="Logo">
                    <span class="ml-2 text-lg font-semibold text-gray-900 dark:text-white">Diretoria</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('memberpanel.Diretoria.index') }}"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('memberpanel.Diretoria.index') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-icon name="gauge-high" class="mr-3 h-5 w-5" />
                        Dashboard
                    </a>

                    <!-- Meetings -->
                    <a href="{{ route('memberpanel.Diretoria.meetings.index') }}"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('memberpanel.Diretoria.meetings.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-icon name="calendar-days" class="mr-3 h-5 w-5" />
                        Reuniões
                    </a>

                    <!-- Agendas -->
                    <a href="{{ route('memberpanel.Diretoria.agendas.index') }}"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('memberpanel.Diretoria.agendas.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-icon name="file-lines" class="mr-3 h-5 w-5" />
                        Pautas
                    </a>

                    <!-- Approvals -->
                    <a href="{{ route('memberpanel.Diretoria.approvals.index') }}"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('memberpanel.Diretoria.approvals.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-icon name="circle-check" class="mr-3 h-5 w-5" />
                        Aprovações
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('memberpanel.Diretoria.profile.index') }}"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('memberpanel.Diretoria.profile.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-icon name="user" class="mr-3 h-5 w-5" />
                        Perfil
                    </a>
                </nav>

                <!-- User Menu -->
                <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center w-full">
                        <div class="flex-shrink-0">
                            @if (auth()->user()->photo)
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ asset('storage/' . auth()->user()->photo) }}"
                                    alt="{{ auth()->user()->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span
                                        class="text-sm font-medium text-gray-700">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">Membro da Diretoria</p>
                        </div>
                        <div class="flex-shrink-0">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <x-icon name="right-from-bracket" class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button type="button"
                                class="md:hidden text-gray-500 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                                onclick="toggleMobileMenu()">
                                <x-icon name="bars" class="h-6 w-6" />
                            </button>
                            <div class="md:hidden ml-2">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">Diretoria</span>
                            </div>
                        </div>

                        <!-- User menu -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button
                                class="text-gray-500 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                                <x-icon name="bell" class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile menu -->
            <div id="mobile-menu"
                class="md:hidden hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <nav class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('memberpanel.Diretoria.index') }}"
                        class="block px-3 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">Dashboard</a>
                    <a href="{{ route('memberpanel.Diretoria.meetings.index') }}"
                        class="block px-3 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">Reuniões</a>
                    <a href="{{ route('memberpanel.Diretoria.agendas.index') }}"
                        class="block px-3 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">Pautas</a>
                    <a href="{{ route('memberpanel.Diretoria.approvals.index') }}"
                        class="block px-3 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">Aprovações</a>
                    <a href="{{ route('memberpanel.Diretoria.profile.index') }}"
                        class="block px-3 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">Perfil</a>
                </nav>
            </div>

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

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Theme toggle if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Add any theme management here if needed
        });
    </script>
</body>

</html>
