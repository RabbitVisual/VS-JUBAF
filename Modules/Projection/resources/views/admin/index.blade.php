@extends('admin::components.layouts.master')

@section('title', 'Centro de Projeção')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1.5">
                    <span>Projeção</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500">Live Cockpit</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Centro de <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-cyan-600 dark:from-blue-400 dark:to-cyan-400">Projeção</span></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Console, tela e remote. Use templates de card para banners na ordem de culto.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @php $firstSetlist = $setlists->first(); @endphp
                <a href="{{ $firstSetlist ? route('admin.projection.console', $firstSetlist->id) : '#' }}" target="_blank"
                    @if(!$firstSetlist) onclick="alert('Crie um culto em Louvor para abrir o console.'); return false;" @endif
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-900 dark:bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/20 hover:opacity-90 transition-all active:scale-95">
                    <x-icon name="gamepad" class="w-5 h-5" />
                    Painel de Controle
                </a>
                <a href="{{ route('admin.projection.screen') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="presentation-screen" class="w-5 h-5" />
                    Tela de Projeção
                </a>
                <a href="{{ route('admin.projection.remote') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="mobile-screen" class="w-5 h-5" />
                    Remote
                </a>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 text-sm">
            <a href="{{ route('admin.projection.settings.index') }}" class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-bold transition">
                <x-icon name="cog" class="w-4 h-4" /> Configurações
            </a>
            <a href="{{ route('admin.projection.themes.index') }}" class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-bold transition">
                <x-icon name="palette" class="w-4 h-4" /> Temas
            </a>
            <a href="{{ route('admin.projection.card-templates.index') }}" class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-bold transition">
                <x-icon name="image" class="w-4 h-4" /> Templates de card (banners)
            </a>
        </div>

        @if(isset($todaySetlist) && $todaySetlist)
            <div class="rounded-3xl border border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-900/20 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white">
                        <x-icon name="calendar-star" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">Culto de hoje</p>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $todaySetlist->title }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $todaySetlist->scheduled_at?->format('d/m/Y H:i') }} · {{ $todaySetlist->items->count() }} itens</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.projection.console', $todaySetlist->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition">
                        <x-icon name="gamepad" class="w-4 h-4" /> Console
                    </a>
                    <a href="{{ route('admin.projection.screen') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <x-icon name="presentation-screen" class="w-4 h-4" /> Tela
                    </a>
                </div>
            </div>
        @endif

        @if($setlists->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($setlists as $setlist)
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col">
                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1.5 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold">{{ $setlist->scheduled_at->format('d/m H:i') }}</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-{{ $setlist->status->color() }}-100 text-{{ $setlist->status->color() }}-800 dark:bg-{{ $setlist->status->color() }}-900/30 dark:text-{{ $setlist->status->color() }}-400">{{ $setlist->status->label() }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $setlist->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $setlist->items_count ?? $setlist->items->count() }} itens no repertório</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.projection.console', $setlist->id) }}" target="_blank" class="inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition">
                                <x-icon name="gamepad" class="w-4 h-4" /> Console
                            </a>
                            <a href="{{ route('admin.projection.screen') }}" target="_blank" class="inline-flex items-center justify-center gap-2 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-bold hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <x-icon name="presentation-screen" class="w-4 h-4" /> Tela
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 px-4">
                 {{ $setlists->links('pagination::tailwind') }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 px-6 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-20 h-20 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-6">
                    <x-icon name="presentation-screen" class="w-10 h-10 text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum culto ativo</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm mb-6">Agende um culto em Louvor para usar o console e a tela de projeção.</p>
                <a href="{{ route('worship.admin.setlists.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all">
                    <x-icon name="calendar" class="w-5 h-5 mr-2" />
                    Gerenciar cultos
                </a>
            </div>
        @endif
    </div>
@endsection

