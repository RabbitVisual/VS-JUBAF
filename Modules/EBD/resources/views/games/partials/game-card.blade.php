@php
    $colorClasses = [
        'indigo' => 'from-indigo-500 to-indigo-700 shadow-indigo-500/30 hover:shadow-indigo-500/40 group-hover:from-indigo-400',
        'emerald' => 'from-emerald-500 to-emerald-700 shadow-emerald-500/30 hover:shadow-emerald-500/40 group-hover:from-emerald-400',
        'rose' => 'from-rose-500 to-rose-700 shadow-rose-500/30 hover:shadow-rose-500/40 group-hover:from-rose-400',
        'violet' => 'from-violet-500 to-violet-700 shadow-violet-500/30 hover:shadow-violet-500/40 group-hover:from-violet-400',
        'purple' => 'from-purple-500 to-purple-700 shadow-purple-500/30 hover:shadow-purple-500/40 group-hover:from-purple-400',
        'amber' => 'from-amber-500 to-amber-700 shadow-amber-500/30 hover:shadow-amber-500/40 group-hover:from-amber-400',
        'orange' => 'from-orange-500 to-orange-700 shadow-orange-500/30 hover:shadow-orange-500/40 group-hover:from-orange-400',
        'teal' => 'from-teal-500 to-teal-700 shadow-teal-500/30 hover:shadow-teal-500/40 group-hover:from-teal-400',
        'sky' => 'from-sky-500 to-sky-700 shadow-sky-500/30 hover:shadow-sky-500/40 group-hover:from-sky-400',
        'fuchsia' => 'from-fuchsia-500 to-fuchsia-700 shadow-fuchsia-500/30 hover:shadow-fuchsia-500/40 group-hover:from-fuchsia-400',
        'slate' => 'from-slate-600 to-slate-800 shadow-slate-500/30 hover:shadow-slate-500/40 group-hover:from-slate-500',
    ];
    $colorClass = $colorClasses[$color] ?? $colorClasses['indigo'];
    
    $badgeColors = [
        'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
        'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
        'rose' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
        'violet' => 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
        'orange' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
        'teal' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
        'sky' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400',
        'fuchsia' => 'bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-600 dark:text-fuchsia-400',
        'slate' => 'bg-slate-100 dark:bg-slate-900/30 text-slate-600 dark:text-slate-400',
    ];
    $badgeColor = $badgeColors[$color] ?? $badgeColors['indigo'];
@endphp

<a href="{{ $route }}" class="group block">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-lg hover:shadow-xl border border-gray-100 dark:border-gray-800 transition-all duration-300 hover:-translate-y-1 overflow-hidden">
        <!-- Gradient Decoration -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-gradient-to-br {{ $colorClass }} rounded-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
        
        <!-- Icon -->
        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br {{ $colorClass }} rounded-2xl md:rounded-3xl flex items-center justify-center mb-4 shadow-xl transition-all duration-300 group-hover:scale-110">
            <x-icon name="{{ $icon }}" style="duotone" class="w-7 h-7 md:w-8 md:h-8 text-white" />
        </div>

        <!-- Content -->
        <div class="relative z-10">
            <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white mb-2 group-hover:text-{{ $color }}-600 transition-colors">{{ $title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">{{ $description }}</p>
            
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                    {{ $badge }}
                </span>
                <span class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:bg-{{ $color }}-500 group-hover:text-white transition-all">
                    <x-icon name="play" style="solid" class="w-3 h-3" />
                </span>
            </div>
        </div>
    </div>
</a>
