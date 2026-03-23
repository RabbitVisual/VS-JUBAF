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

    <title>{{ $title ?? $event->title ?? 'Evento' }} | {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($event->description ?? ''), 160) }}">
    @stack('meta')

    @php
        $faviconUrl = asset(\App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">

    @preloadFonts
    @vite(['resources/css/app.css', 'resources/js/app.js', 'Modules/HomePage/resources/assets/sass/app.scss', 'Modules/HomePage/resources/assets/js/app.js'])
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">
</head>
<body class="antialiased bg-slate-950 text-white">
    <x-loading-overlay />

    <div x-data="{ registrationModalOpen: {{ ($errors->any() || session('error')) ? 'true' : 'false' }} }"
         x-on:open-registration-modal.window="registrationModalOpen = true">
        {{-- Event navbar: logo/title + anchor links (only enabled sections) + CTA --}}
        <header class="fixed top-0 left-0 right-0 z-50 bg-slate-900/95 backdrop-blur border-b border-slate-800">
            <nav class="container mx-auto px-4 max-w-7xl flex flex-wrap items-center justify-between gap-4 py-3">
                <a href="#top" class="flex items-center gap-3 shrink-0">
                    @if(isset($event) && $event->logo_path)
                        <img src="{{ Storage::url($event->logo_path) }}" alt="{{ $event->title }}" class="h-9 w-auto max-w-[180px] object-contain">
                    @else
                        <span class="font-bold text-lg text-white">{{ $event->title ?? 'Evento' }}</span>
                    @endif
                </a>
                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                    @if(isset($event))
                        @if($event->showAboutEnabled())
                            <a href="#sobre" class="text-sm text-slate-300 hover:text-amber-400 transition-colors">{{ __('events::messages.about_event') ?? 'Sobre' }}</a>
                        @endif
                        @if($event->showLocationEnabled() && $event->location)
                            <a href="#local" class="text-sm text-slate-300 hover:text-amber-400 transition-colors">{{ __('events::messages.location') ?? 'Local' }}</a>
                        @endif
                        @if($event->showMapEnabled() && (is_array($event->location_data) && (!empty($event->location_data['address']) || (!empty($event->location_data['lat']) && !empty($event->location_data['lng'])))))
                            <a href="#mapa" class="text-sm text-slate-300 hover:text-amber-400 transition-colors">{{ __('events::messages.map') ?? 'Mapa' }}</a>
                        @endif
                        @if($event->showScheduleEnabled())
                            <a href="#programacao" class="text-sm text-slate-300 hover:text-amber-400 transition-colors">{{ __('events::messages.schedule_heading') ?? 'Programação' }}</a>
                        @endif
                        @if($event->showSpeakersEnabled())
                            <a href="#palestrantes" class="text-sm text-slate-300 hover:text-amber-400 transition-colors">{{ __('events::messages.speakers_heading') ?? 'Palestrantes' }}</a>
                        @endif
                        @if($event->showCapacityEnabled())
                            <a href="#vagas" class="text-sm text-slate-300 hover:text-amber-400 transition-colors">{{ __('events::messages.capacity') ?? 'Vagas' }}</a>
                        @endif
                    @endif
                    @if(isset($event) && ($event->is_active ?? false))
                        <button type="button" @click="registrationModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-bold text-sm transition-colors" style="background-color: var(--color-main); filter: brightness(1.1);">
                            <x-icon name="circle-check" style="duotone" class="w-4 h-4" />
                            {{ __('events::messages.register') ?? 'Inscreva-se' }}
                        </button>
                    @endif
                </div>
            </nav>
        </header>

        <main id="top" class="pt-16">
            @yield('content')
        </main>

        @yield('registration_modal')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href.length > 1) {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                });
            });
            if (new URLSearchParams(window.location.search).get('openRegistration') === '1') {
                window.dispatchEvent(new CustomEvent('open-registration-modal'));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
