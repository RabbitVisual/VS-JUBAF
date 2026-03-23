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

        <title>Sermons Module - {{ config('app.name', 'Laravel') }}</title>

        <meta name="description" content="{{ $description ?? '' }}">
        <meta name="keywords" content="{{ $keywords ?? '' }}">
        <meta name="author" content="{{ $author ?? '' }}">

        <!-- Fonts (Local) -->`n        @preloadFonts

        {{-- Vite CSS --}}
        {{-- {{ module_vite('build-sermons', 'resources/assets/sass/app.scss') }} --}}

        <!-- Font Awesome Pro -->
        <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">
    </head>

    <body>
        <x-loading-overlay />
        {{ $slot }}

        {{-- Vite JS --}}
        {{-- {{ module_vite('build-sermons', 'resources/assets/js/app.js') }} --}}
    </body>
</html>

