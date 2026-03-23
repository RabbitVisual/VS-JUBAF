@extends('admin::components.layouts.master')

@section('title', 'Arcade Manager | EBD')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white rounded">Arcade</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Gamificação</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Gestão de <span class="text-amber-600">Jogos</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Controle os desafios, perguntas e o ranking da temporada.</p>
        </div>
        <div class="flex items-center gap-3">
             <form action="{{ route('admin.ebd.games.reset-leaderboard') }}" method="POST" onsubmit="return confirm('ATENÇÃO: Isso irá apagar todo o histórico de pontuações e zerar o ranking. Deseja iniciar uma nova temporada?');">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 rounded-xl bg-red-600 text-white text-sm font-bold transition-all hover:bg-red-700 active:scale-95 shadow-xl shadow-red-900/20">
                    <x-icon name="trash-can" style="duotone" class="mr-2 h-4 w-4" />
                    Zerar Ranking (Nova Temporada)
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <x-icon name="gamepad" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jogos Ativos</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $games->where('is_active', true)->count() }}</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-icon name="users" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Partidas Jogadas</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalSessions) }}</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <x-icon name="star" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">XP Distribuído</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalPoints) }}</div>
        </div>
    </div>

    <!-- Games List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($games as $game)
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-lg overflow-hidden flex flex-col h-full group transition-all hover:border-amber-500/30">
            <div class="p-6 flex-grow">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-amber-500">
                        <x-icon :name="$game->icon ?? 'gamepad'" style="duotone" class="w-6 h-6" />
                    </div>
                    <div class="flex items-center gap-2">
                        @if($game->is_active)
                            <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Ativo</span>
                        @else
                            <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Inativo</span>
                        @endif
                    </div>
                </div>

                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ $game->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $game->description }}</p>

                <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <span>{{ $game->questions_count }} Perguntas</span>
                    <span>{{ $game->slug }}</span>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 border-t border-gray-100 dark:border-gray-800 flex gap-2">
                <a href="{{ route('admin.ebd.games.edit', $game->id) }}" class="flex-1 inline-flex items-center justify-center py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-colors">
                    <x-icon name="pen-to-square" style="duotone" class="mr-2 h-4 w-4" />
                    Gerenciar
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

