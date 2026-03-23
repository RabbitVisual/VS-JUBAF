<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Projection - {{ config('app.name', 'Vertex') }}</title>

        <!-- Fonts (Local) -->`n        @preloadFonts

        <!-- FontAwesome Pro -->
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-pro/css/all.css') }}">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-100 dark:bg-gray-950 dark:text-gray-100">
        {{ $slot }}
    </body>
</html>

