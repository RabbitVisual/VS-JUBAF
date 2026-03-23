@php
    $colorConfig = [
        'indigo' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'text' => 'text-indigo-600 dark:text-indigo-400', 'hover' => 'hover:bg-indigo-50 dark:hover:bg-indigo-900/20', 'ring' => 'hover:ring-indigo-200 dark:hover:ring-indigo-800'],
        'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'hover' => 'hover:bg-emerald-50 dark:hover:bg-emerald-900/20', 'ring' => 'hover:ring-emerald-200 dark:hover:ring-emerald-800'],
        'rose' => ['bg' => 'bg-rose-100 dark:bg-rose-900/30', 'text' => 'text-rose-600 dark:text-rose-400', 'hover' => 'hover:bg-rose-50 dark:hover:bg-rose-900/20', 'ring' => 'hover:ring-rose-200 dark:hover:ring-rose-800'],
        'violet' => ['bg' => 'bg-violet-100 dark:bg-violet-900/30', 'text' => 'text-violet-600 dark:text-violet-400', 'hover' => 'hover:bg-violet-50 dark:hover:bg-violet-900/20', 'ring' => 'hover:ring-violet-200 dark:hover:ring-violet-800'],
        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400', 'hover' => 'hover:bg-purple-50 dark:hover:bg-purple-900/20', 'ring' => 'hover:ring-purple-200 dark:hover:ring-purple-800'],
        'amber' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'hover' => 'hover:bg-amber-50 dark:hover:bg-amber-900/20', 'ring' => 'hover:ring-amber-200 dark:hover:ring-amber-800'],
        'orange' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-600 dark:text-orange-400', 'hover' => 'hover:bg-orange-50 dark:hover:bg-orange-900/20', 'ring' => 'hover:ring-orange-200 dark:hover:ring-orange-800'],
        'teal' => ['bg' => 'bg-teal-100 dark:bg-teal-900/30', 'text' => 'text-teal-600 dark:text-teal-400', 'hover' => 'hover:bg-teal-50 dark:hover:bg-teal-900/20', 'ring' => 'hover:ring-teal-200 dark:hover:ring-teal-800'],
        'sky' => ['bg' => 'bg-sky-100 dark:bg-sky-900/30', 'text' => 'text-sky-600 dark:text-sky-400', 'hover' => 'hover:bg-sky-50 dark:hover:bg-sky-900/20', 'ring' => 'hover:ring-sky-200 dark:hover:ring-sky-800'],
        'cyan' => ['bg' => 'bg-cyan-100 dark:bg-cyan-900/30', 'text' => 'text-cyan-600 dark:text-cyan-400', 'hover' => 'hover:bg-cyan-50 dark:hover:bg-cyan-900/20', 'ring' => 'hover:ring-cyan-200 dark:hover:ring-cyan-800'],
        'fuchsia' => ['bg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/30', 'text' => 'text-fuchsia-600 dark:text-fuchsia-400', 'hover' => 'hover:bg-fuchsia-50 dark:hover:bg-fuchsia-900/20', 'ring' => 'hover:ring-fuchsia-200 dark:hover:ring-fuchsia-800'],
        'slate' => ['bg' => 'bg-slate-200 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-400', 'hover' => 'hover:bg-slate-100 dark:hover:bg-slate-800/80', 'ring' => 'hover:ring-slate-300 dark:hover:ring-slate-700'],
    ];
    $cfg = $colorConfig[$color] ?? $colorConfig['indigo'];
@endphp

<a href="{{ $route }}" 
   class="group flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-gray-50 dark:bg-slate-800 {{ $cfg['hover'] }} rounded-xl sm:rounded-2xl border border-gray-100 dark:border-slate-700 hover:border-transparent hover:ring-2 {{ $cfg['ring'] }} transition-all duration-200 active:scale-[0.98]">
    
    <div class="shrink-0 w-11 h-11 sm:w-12 sm:h-12 {{ $cfg['bg'] }} rounded-xl sm:rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
        <x-icon name="{{ $icon }}" style="duotone" class="w-5 h-5 sm:w-6 sm:h-6 {{ $cfg['text'] }}" />
    </div>

    <div class="flex-1 min-w-0">
        <h4 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base truncate group-hover:{{ str_replace('text-', '', $cfg['text']) }} transition-colors">{{ $title }}</h4>
        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-slate-400 truncate mt-0.5">{{ $description }}</p>
    </div>

    <div class="shrink-0 flex flex-col items-end gap-1">
        <span class="text-[9px] sm:text-[10px] font-bold {{ $cfg['text'] }} {{ $cfg['bg'] }} px-2 py-0.5 rounded-md whitespace-nowrap">{{ $badge }}</span>
        <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 dark:text-slate-600 group-hover:{{ str_replace('text-', '', $cfg['text']) }} group-hover:translate-x-1 transition-all" />
    </div>
</a>
