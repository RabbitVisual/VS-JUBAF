{{-- Navegação rápida Tesouraria: breadcrumb + links para todas as seções --}}
<nav class="flex flex-wrap items-center gap-2 text-sm">
    <a href="{{ route('treasury.dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors inline-flex items-center gap-1.5">
        <x-icon name="building-columns" style="duotone" class="w-4 h-4" />
        Tesouraria
    </a>
    @if(isset($breadcrumb) && is_array($breadcrumb))
        @foreach($breadcrumb as $label => $url)
            <span class="text-gray-300 dark:text-gray-600">/</span>
            @if($url)
                <a href="{{ $url }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">{{ $label }}</a>
            @else
                <span class="text-gray-900 dark:text-white font-semibold">{{ $label }}</span>
            @endif
        @endforeach
    @endif
</nav>

@if(!isset($hideQuickLinks) || !$hideQuickLinks)
<div class="flex flex-wrap items-center gap-2 mt-3">
    <a href="{{ route('treasury.entries.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 border border-gray-200 dark:border-gray-600 transition-all">
        <x-icon name="list-timeline" style="duotone" class="w-3.5 h-3.5" /> Entradas
    </a>
    <a href="{{ route('treasury.campaigns.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 border border-gray-200 dark:border-gray-600 transition-all">
        <x-icon name="bullhorn" style="duotone" class="w-3.5 h-3.5" /> Campanhas
    </a>
    <a href="{{ route('treasury.goals.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 border border-gray-200 dark:border-gray-600 transition-all">
        <x-icon name="bullseye-arrow" style="duotone" class="w-3.5 h-3.5" /> Metas
    </a>
    <a href="{{ route('treasury.reports.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 border border-gray-200 dark:border-gray-600 transition-all">
        <x-icon name="chart-bar" style="duotone" class="w-3.5 h-3.5" /> Relatórios
    </a>
    @if(isset($permission) && $permission->isAdmin())
    <a href="{{ route('treasury.permissions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 border border-gray-200 dark:border-gray-600 transition-all">
        <x-icon name="key" style="duotone" class="w-3.5 h-3.5" /> Permissões
    </a>
    @endif
</div>
@endif
