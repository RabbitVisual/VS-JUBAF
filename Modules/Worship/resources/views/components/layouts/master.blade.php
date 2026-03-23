<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

        <title>Worship Module - {{ config('app.name', 'Laravel') }}</title>

        <meta name="description" content="{{ $description ?? '' }}">
        <meta name="keywords" content="{{ $keywords ?? '' }}">
        <meta name="author" content="{{ $author ?? '' }}">

        <!-- Fonts (Local) -->`n        @preloadFonts

        {{-- Worship Module Assets --}}
        @vite(['Modules/Worship/resources/assets/sass/app.scss', 'Modules/Worship/resources/assets/js/app.js'])

        <!-- Font Awesome Pro -->
        <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">

        {{-- Main Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="antialiased">
        <x-loading-overlay />
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif


    </body>
</html>

