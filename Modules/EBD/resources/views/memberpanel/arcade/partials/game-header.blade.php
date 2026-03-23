@php
    $accent = $accent ?? 'indigo';
    $accentMap = [
        'indigo' => ['from' => 'from-indigo-500', 'to' => 'to-indigo-600', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/30', 'border' => 'border-indigo-100 dark:border-indigo-800', 'text' => 'text-indigo-600 dark:text-indigo-400', 'shadow' => 'shadow-indigo-500/20'],
        'emerald' => ['from' => 'from-emerald-500', 'to' => 'to-emerald-600', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'border' => 'border-emerald-100 dark:border-emerald-800', 'text' => 'text-emerald-600 dark:text-emerald-400', 'shadow' => 'shadow-emerald-500/20'],
        'purple' => ['from' => 'from-purple-500', 'to' => 'to-purple-600', 'bg' => 'bg-purple-50 dark:bg-purple-900/30', 'border' => 'border-purple-100 dark:border-purple-800', 'text' => 'text-purple-600 dark:text-purple-400', 'shadow' => 'shadow-purple-500/20'],
        'blue' => ['from' => 'from-blue-500', 'to' => 'to-blue-600', 'bg' => 'bg-blue-50 dark:bg-blue-900/30', 'border' => 'border-blue-100 dark:border-blue-800', 'text' => 'text-blue-600 dark:text-blue-400', 'shadow' => 'shadow-blue-500/20'],
        'amber' => ['from' => 'from-amber-500', 'to' => 'to-amber-600', 'bg' => 'bg-amber-50 dark:bg-amber-900/30', 'border' => 'border-amber-100 dark:border-amber-800', 'text' => 'text-amber-600 dark:text-amber-400', 'shadow' => 'shadow-amber-500/20'],
        'rose' => ['from' => 'from-rose-500', 'to' => 'to-rose-600', 'bg' => 'bg-rose-50 dark:bg-rose-900/30', 'border' => 'border-rose-100 dark:border-rose-800', 'text' => 'text-rose-600 dark:text-rose-400', 'shadow' => 'shadow-rose-500/20'],
        'teal' => ['from' => 'from-teal-500', 'to' => 'to-teal-600', 'bg' => 'bg-teal-50 dark:bg-teal-900/30', 'border' => 'border-teal-100 dark:border-teal-800', 'text' => 'text-teal-600 dark:text-teal-400', 'shadow' => 'shadow-teal-500/20'],
        'violet' => ['from' => 'from-violet-500', 'to' => 'to-violet-600', 'bg' => 'bg-violet-50 dark:bg-violet-900/30', 'border' => 'border-violet-100 dark:border-violet-800', 'text' => 'text-violet-600 dark:text-violet-400', 'shadow' => 'shadow-violet-500/20'],
        'sky' => ['from' => 'from-sky-500', 'to' => 'to-sky-600', 'bg' => 'bg-sky-50 dark:bg-sky-900/30', 'border' => 'border-sky-100 dark:border-sky-800', 'text' => 'text-sky-600 dark:text-sky-400', 'shadow' => 'shadow-sky-500/20'],
    ];
    $c = $accentMap[$accent] ?? $accentMap['indigo'];
@endphp
{{-- Game page header: breadcrumb + hero card. Use in arcade games with same layout. --}}
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
            <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Central de Jogos</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium truncate">{{ $gameTitle }}</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $gameTitle }}</h1>
        @if(!empty($gameSubtitle))
            <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">{{ $gameSubtitle }}</p>
        @endif
    </div>
    <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shrink-0 touch-manipulation active:scale-[0.98]">
        <x-icon name="arrow-left" class="w-4 h-4" />
        <span class="hidden xs:inline">Voltar</span>
    </a>
</div>

<div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
    <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
        <div class="absolute -top-24 -left-20 w-72 h-72 {{ str_replace('500', '400', $c['from']) }} dark:{{ str_replace('500', '600', $c['from']) }} rounded-full blur-[100px]"></div>
        <div class="absolute top-1/2 -right-20 w-64 h-64 {{ str_replace('500', '400', $c['to']) }} dark:{{ str_replace('500', '600', $c['to']) }} rounded-full blur-[100px]"></div>
    </div>
    <div class="relative px-4 sm:px-6 md:px-8 py-6 sm:py-8 z-10 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }} flex items-center justify-center shadow-xl {{ $c['shadow'] }} shrink-0">
            <x-icon name="{{ $icon }}" style="duotone" class="w-7 h-7 sm:w-8 sm:h-8 text-white" />
        </div>
        <div class="flex-1 min-w-0">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $c['bg'] }} border {{ $c['border'] }} mb-2">
                <x-icon name="gamepad-modern" class="w-3 h-3 {{ $c['text'] }}" />
                <span class="text-[10px] font-black uppercase tracking-widest {{ $c['text'] }}">Arcade Bíblico</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $gameTitle }}</h2>
            @if(!empty($gameSubtitle))
                <p class="text-gray-500 dark:text-slate-300 text-sm mt-0.5">{{ $gameSubtitle }}</p>
            @endif
        </div>
    </div>
</div>
