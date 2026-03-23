@extends('homepage::components.layouts.master')

@section('title', $event->title)

@push('meta')
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $eventUrl ?? url()->current() }}">
<meta property="og:title" content="{{ $event->title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($event->description ?? ''), 160) }}">
@if($event->showCoverEnabled() && $event->banner_path)
<meta property="og:image" content="{{ url(Storage::url($event->banner_path)) }}">
@endif
<meta property="og:locale" content="pt_BR">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $event->title }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($event->description ?? ''), 160) }}">
@if($event->showCoverEnabled() && $event->banner_path)
<meta name="twitter:image" content="{{ url(Storage::url($event->banner_path)) }}">
@endif

<style>
    /* Injeta dinamicamente as cores escolhidas pelo admin para o evento atual */
    :root {
        --color-main: {{ $event->theme_config['primary_color'] ?? '#4F46E5' }};
        --color-secondary: {{ $event->theme_config['secondary_color'] ?? '#111827' }};
    }
</style>
@endpush

@section('content')
@php
    $theme = $event->theme_config['theme'] ?? 'modern';
    // Mapeamento extra de segurança caso algum tema não exista no diretório
    if (!in_array($theme, ['modern', 'minimal', 'corporate'])) {
        $theme = 'modern';
    }
@endphp

<div x-data="{ registrationModalOpen: {{ ($errors->any() || session('error')) ? 'true' : 'false' }} }"
     x-init="if (new URLSearchParams(window.location.search).get('openRegistration') === '1') registrationModalOpen = true">
    <div class="theme-wrapper font-sans w-full" style="--tw-ring-color: var(--color-main);">
        @include("events::public.themes.{$theme}")
    </div>

    @include('events::public.partials.registration-modal', [
        'event' => $event,
        'registrationConfig' => $registrationConfig ?? ['use_segments' => false, 'form_fields' => $event->form_fields ?? []],
        'isFree' => $isFree,
        'hasBatches' => $hasBatches,
        'batches' => $batches ?? collect(),
        'gateways' => $gateways ?? collect(),
        'defaultParticipant' => $defaultParticipant ?? null,
    ])
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('eventShare', () => ({
        shared: false,
        copied: false,
        url: @json($eventUrl ?? url()->current()),

        share() {
            const title = @json($event->title);
            const appName = @json(config('app.name', 'nossa igreja'));
            const dateTime = @json($event->start_date->format('d/m/Y H:i'));
            const location = @json($event->location ?? '');
            const shortDescription = @json(Str::limit(strip_tags($event->description ?? ''), 160));

            let text = `Veja este evento de ${appName}:\n\n`;
            text += `${title}\n`;
            text += `Data e horário: ${dateTime}`;
            if (location) {
                text += `\nLocal: ${location}`;
            }
            if (shortDescription) {
                text += `\n\n${shortDescription}`;
            }
            text += `\n\nInscreva-se aqui: ${this.url}`;

            const encoded = encodeURIComponent(text);
            const waUrl = `https://wa.me/?text=${encoded}`;
            window.open(waUrl, '_blank');

            this.shared = true;
            setTimeout(() => this.shared = false, 2000);
        },

        copyLink() {
            navigator.clipboard.writeText(this.url).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },
    }));
});
</script>
@endpush
