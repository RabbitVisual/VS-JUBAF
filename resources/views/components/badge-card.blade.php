@props([
    'badge',
    'size' => 'md', // sm | md | lg
])

@php
    $name = is_array($badge) ? ($badge['name'] ?? '') : $badge->name;
    $icon = is_array($badge) ? ($badge['icon'] ?? 'medal') : ($badge->icon ?? 'medal');
    $color = is_array($badge) ? ($badge['color'] ?? 'blue') : ($badge->color ?? 'blue');
    $description = is_array($badge) ? ($badge['description'] ?? '') : ($badge->description ?? '');
    $earnedAt = is_array($badge) ? ($badge['earned_at'] ?? null) : ($badge->pivot->earned_at ?? null);

    $allowedColors = ['blue', 'green', 'yellow', 'purple', 'gray', 'orange', 'indigo', 'rose', 'cyan', 'emerald', 'amber', 'red', 'pink'];
    $safeColor = in_array($color, $allowedColors) ? $color : 'blue';

    $bgClass = match($safeColor) {
        'blue' => 'bg-blue-100 dark:bg-blue-900/20',
        'green' => 'bg-green-100 dark:bg-green-900/20',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/20',
        'purple' => 'bg-purple-100 dark:bg-purple-900/20',
        'gray' => 'bg-gray-100 dark:bg-gray-900/20',
        'orange' => 'bg-orange-100 dark:bg-orange-900/20',
        'indigo' => 'bg-indigo-100 dark:bg-indigo-900/20',
        'rose' => 'bg-rose-100 dark:bg-rose-900/20',
        'cyan' => 'bg-cyan-100 dark:bg-cyan-900/20',
        'emerald' => 'bg-emerald-100 dark:bg-emerald-900/20',
        'amber' => 'bg-amber-100 dark:bg-amber-900/20',
        'red' => 'bg-red-100 dark:bg-red-900/20',
        'pink' => 'bg-pink-100 dark:bg-pink-900/20',
        default => 'bg-blue-100 dark:bg-blue-900/20',
    };
    $iconClass = match($safeColor) {
        'blue' => 'text-blue-600 dark:text-blue-400',
        'green' => 'text-green-600 dark:text-green-400',
        'yellow' => 'text-yellow-600 dark:text-yellow-400',
        'purple' => 'text-purple-600 dark:text-purple-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
        'orange' => 'text-orange-600 dark:text-orange-400',
        'indigo' => 'text-indigo-600 dark:text-indigo-400',
        'rose' => 'text-rose-600 dark:text-rose-400',
        'cyan' => 'text-cyan-600 dark:text-cyan-400',
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'red' => 'text-red-600 dark:text-red-400',
        'pink' => 'text-pink-600 dark:text-pink-400',
        default => 'text-blue-600 dark:text-blue-400',
    };

    $iconSize = match($size) {
        'sm' => 'w-5 h-5',
        'lg' => 'w-8 h-8',
        default => 'w-6 h-6',
    };
    $boxSize = match($size) {
        'sm' => 'w-10 h-10',
        'lg' => 'w-14 h-14',
        default => 'w-12 h-12',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-4 p-3 rounded-2xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-gray-100 dark:hover:border-slate-700 group']) }}>
    <div class="{{ $boxSize }} shrink-0 {{ $bgClass }} rounded-2xl flex items-center justify-center p-2 group-hover:scale-110 transition-transform shadow-sm">
        <x-icon name="{{ $icon }}" class="{{ $iconSize }} {{ $iconClass }}" />
    </div>
    <div class="flex-1 min-w-0">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $name }}</h4>
        @if($description)
            <p class="text-[10px] text-gray-500 dark:text-slate-400 truncate">{{ $description }}</p>
        @endif
        @if($earnedAt)
            <p class="text-[9px] text-gray-400 dark:text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($earnedAt)->translatedFormat('d/m/Y') }}</p>
        @endif
    </div>
</div>
