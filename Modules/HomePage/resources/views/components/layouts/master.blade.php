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

    <title>{{ $title ?? 'Igreja Batista Avenida - Coração de Maria - BA' }} | {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $description ?? 'Igreja Batista Avenida - Coração de Maria - BA. Uma comunidade de fé comprometida com o Evangelho e o serviço ao próximo.' }}">
    <meta name="keywords" content="{{ $keywords ?? 'igreja batista, coração de maria, bahia, cristianismo, evangelho' }}">
    <meta name="author" content="{{ $author ?? 'Igreja Batista Avenida' }}">

    @stack('meta')

    <!-- Favicon (local, from Settings) -->
    @php
        $faviconUrl = asset(\App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <!-- Fonts (Local) -->
    @preloadFonts

    @vite(['resources/css/app.css', 'resources/js/app.js', 'Modules/HomePage/resources/assets/sass/app.scss', 'Modules/HomePage/resources/assets/js/app.js'])

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

<body class="antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <x-loading-overlay />
    <!-- Navigation -->
    @if(!isset($hideNavFooter) || !$hideNavFooter)
        @include('homepage::components.navbar')
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @if(!isset($hideNavFooter) || !$hideNavFooter)
        @hasSection('footer')
            @yield('footer')
        @else
            @include('homepage::components.footer')
        @endif
    @endif

    <script>
        // Smooth scroll for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href.length > 1) {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                            // Close mobile menu if open
                            const mobileMenu = document.getElementById('mobile-menu');
                            if (mobileMenu) {
                                mobileMenu.classList.add('hidden');
                            }
                        }
                    }
                });
            });
        });

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    const menu = document.getElementById('mobile-menu');
                    if (menu) {
                        menu.classList.toggle('hidden');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
