@extends('admin::components.layouts.master')

@section('title', 'Banco de Músicas | Worship')

@section('content')
<div class="space-y-8">
    <!-- Header (Admin pattern) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1.5">
                <span>Módulo de Louvor</span>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-gray-400 dark:text-gray-500">Biblioteca</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Banco de músicas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie o acervo musical da igreja.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('worship.admin.songs.import') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="file-import" class="w-5 h-5 mr-2" />
                Importar
            </a>
            <a href="{{ route('worship.admin.songs.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Nova música
            </a>
        </div>
    </div>

    <!-- Stats (Admin pattern) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="music-note" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Acervo</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $songs->total() }}</h3>
                <p class="text-sm text-gray-400 mt-2">músicas cadastradas</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <x-icon name="calendar" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Últimos 30 dias</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">+{{ \Modules\Worship\App\Models\WorshipSong::where('created_at', '>=', now()->subDays(30))->count() }}</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <x-icon name="chart-bar" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Uso</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">—</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <x-icon name="star" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Destaque</p>
                <h3 class="text-lg font-black text-gray-900 dark:text-white mt-1 truncate">—</h3>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('worship.admin.songs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="search" class="w-5 h-5" />
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por título, autor ou letra..."
                    class="block w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm">
                    Filtrar
                </button>
                @if(request()->has('search'))
                    <a href="{{ route('worship.admin.songs.index') }}" class="inline-flex items-center px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-red-500">
                        <x-icon name="xmark" class="w-5 h-5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($songs->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Título e autor</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">BPM / Métrica</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Tom</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($songs as $song)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                                            <x-icon name="music-note" class="w-6 h-6" />
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('worship.admin.songs.show', $song->id) }}" class="font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 block truncate">
                                                {{ $song->title }}
                                            </a>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $song->artist ?: 'Independente' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $song->bpm ?? '—' }}</span>
                                    <span class="text-gray-400 mx-1">/</span>
                                    <span class="text-sm text-gray-500">{{ $song->time_signature ?? '4/4' }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ $song->original_key->value ?? '?' }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('worship.admin.songs.show', $song->id) }}" class="p-2 rounded-xl text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700" title="Ver">
                                            <x-icon name="eye" class="w-5 h-5" />
                                        </a>
                                        <a href="{{ route('worship.admin.songs.edit', $song->id) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-700" title="Editar">
                                            <x-icon name="pencil" class="w-5 h-5" />
                                        </a>
                                        <form action="{{ route('worship.admin.songs.destroy', $song->id) }}" method="POST" class="inline" onsubmit="return confirm('Remover esta música permanentemente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700" title="Excluir">
                                                <x-icon name="trash" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $songs->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center py-16 px-6">
            <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-6">
                <x-icon name="music-note" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhuma música cadastrada</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm mb-6">Cadastre músicas para a equipe ensaiar e preparar os cultos.</p>
            <a href="{{ route('worship.admin.songs.create') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Cadastrar primeira música
            </a>
        </div>
    @endif
</div>
@endsection
