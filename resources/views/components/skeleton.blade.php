{{--
    Skeleton loader for in-page content (lists, cards, text).
    Use when a section loads via API/fetch instead of full-page overlay.
    A11y: role="status", sr-only "Loading…". Variants: text (default), card, list.
    Design: soft pulse, stone/slate palette, rounded — aligned with loading overlay tone.
--}}
@props([
    'variant' => 'text',
])

@php
    $baseClass = 'animate-pulse bg-stone-200/80 dark:bg-stone-600/50 rounded';
@endphp

<div
    role="status"
    aria-label="{{ __('Carregando...') }}"
    {{ $attributes->merge(['class' => 'max-w-full']) }}
>
    @if($variant === 'text')
        <div class="space-y-3">
            <div class="h-3 {{ $baseClass }} rounded-full w-4/5 max-w-sm"></div>
            <div class="h-2.5 {{ $baseClass }} rounded-full max-w-[340px]"></div>
            <div class="h-2.5 {{ $baseClass }} rounded-full max-w-[300px]"></div>
            <div class="h-2.5 {{ $baseClass }} rounded-full max-w-[280px]"></div>
        </div>
    @elseif($variant === 'card')
        <div class="p-5 border border-stone-200/80 dark:border-stone-600/50 rounded-xl shadow-sm space-y-4 bg-white/50 dark:bg-stone-900/30">
            <div class="h-44 {{ $baseClass }} rounded-lg w-full"></div>
            <div class="h-3 {{ $baseClass }} rounded-full w-1/2"></div>
            <div class="h-2.5 {{ $baseClass }} rounded-full"></div>
            <div class="h-2.5 {{ $baseClass }} rounded-full"></div>
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-stone-200/80 dark:border-stone-600/50">
                <div class="w-9 h-9 {{ $baseClass }} rounded-full shrink-0"></div>
                <div class="flex-1 space-y-2 min-w-0">
                    <div class="h-2.5 {{ $baseClass }} rounded-full w-28"></div>
                    <div class="h-2 {{ $baseClass }} rounded-full w-36"></div>
                </div>
            </div>
        </div>
    @elseif($variant === 'list')
        <div class="divide-y divide-stone-200/80 dark:divide-stone-600/50 rounded-xl border border-stone-200/80 dark:border-stone-600/50 overflow-hidden bg-white/50 dark:bg-stone-900/30">
            @foreach([1, 2, 3, 4] as $i)
                <div class="flex items-center justify-between gap-4 p-4">
                    <div class="space-y-2 min-w-0 flex-1">
                        <div class="h-2.5 {{ $baseClass }} rounded-full w-24"></div>
                        <div class="h-2 {{ $baseClass }} rounded-full w-32"></div>
                    </div>
                    <div class="h-2.5 {{ $baseClass }} rounded-full w-12 shrink-0"></div>
                </div>
            @endforeach
        </div>
    @else
        {{ $slot }}
    @endif
    <span class="sr-only">{{ __('Carregando...') }}</span>
</div>
