@extends('memberpanel::components.layouts.master')

@section('title', 'Ranking - Arcade Bíblico')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-4xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header with Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Central de Jogos</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium">Ranking</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Ranking de Jogadores</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Os melhores jogadores do Arcade Bíblico</p>
            </div>
            <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span class="hidden sm:inline">Voltar</span>
            </a>
        </div>

        <!-- Hero with User Rank -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-amber-400 dark:bg-amber-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10">
                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-10">
                    <div class="shrink-0">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-amber-400 to-orange-500 p-[3px] shadow-xl shadow-amber-500/30">
                            <div class="w-full h-full rounded-[13px] sm:rounded-[17px] bg-white dark:bg-slate-900 flex items-center justify-center">
                                <x-icon name="ranking-star" style="duotone" class="w-10 h-10 sm:w-12 sm:h-12 text-amber-500" />
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800 mb-2">
                            <x-icon name="trophy" class="w-3 h-3 text-amber-600 dark:text-amber-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Hall da Fama</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                            Sua Posição no Ranking
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Continue jogando para subir de posição!</p>
                    </div>

                    <div class="shrink-0 bg-gray-50 dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-gray-100 dark:border-slate-700 text-center min-w-[140px]">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-xl shadow-indigo-500/20">
                            <span class="text-xl sm:text-2xl font-black text-white">#{{ $userRank }}</span>
                        </div>
                        <span class="block text-xl sm:text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ number_format($userScore, 0, ',', '.') }}</span>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Pontos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex justify-center">
            <div class="bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-gray-100 dark:border-slate-800 flex gap-1 shadow-sm">
                <a href="{{ route('memberpanel.ebd.arcade.leaderboard', ['filter' => 'weekly']) }}"
                   class="px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl text-[10px] sm:text-xs font-bold uppercase tracking-wider transition-all {{ $filter === 'weekly' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                   Semanal
                </a>
                <a href="{{ route('memberpanel.ebd.arcade.leaderboard', ['filter' => 'monthly']) }}"
                   class="px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl text-[10px] sm:text-xs font-bold uppercase tracking-wider transition-all {{ $filter === 'monthly' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                   Mensal
                </a>
                <a href="{{ route('memberpanel.ebd.arcade.leaderboard', ['filter' => 'all']) }}"
                   class="px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl text-[10px] sm:text-xs font-bold uppercase tracking-wider transition-all {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                   Geral
                </a>
            </div>
        </div>

        <!-- Leaderboard List -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <x-icon name="list-ol" style="duotone" class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Classificação {{ ucfirst($filter === 'all' ? 'Geral' : ($filter === 'weekly' ? 'Semanal' : 'Mensal')) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Top 50 jogadores</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse($rankings as $rank)
                    @php
                        $position = (int) ($rank->position ?? 0);
                        $isTop3 = $position >= 1 && $position <= 3;
                        $isCurrentUser = (int) $rank->user_id === (int) auth()->id();
                        $userName = $rank->user->name ?? 'Jogador';

                        $medalColors = [
                            1 => ['bg' => 'bg-amber-500', 'text' => 'text-amber-500', 'ring' => 'bg-amber-50 dark:bg-amber-900/20'],
                            2 => ['bg' => 'bg-gray-400', 'text' => 'text-gray-400', 'ring' => 'bg-gray-50 dark:bg-slate-800/50'],
                            3 => ['bg' => 'bg-amber-700', 'text' => 'text-amber-700', 'ring' => 'bg-orange-50 dark:bg-orange-900/10'],
                        ];
                        $colors = $medalColors[$position] ?? ['bg' => 'bg-gray-300 dark:bg-slate-600', 'text' => 'text-gray-400', 'ring' => ''];
                        $initials = collect(explode(' ', $userName))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('');
                    @endphp

                    <div class="flex items-center gap-4 sm:gap-6 p-4 sm:p-5 {{ $isCurrentUser ? 'bg-indigo-50 dark:bg-indigo-900/20' : ($isTop3 ? $colors['ring'] : 'hover:bg-gray-50 dark:hover:bg-slate-800/50') }} transition-colors">
                        <!-- Rank # -->
                        <div class="w-10 sm:w-12 text-center shrink-0">
                            @if($isTop3)
                                <div class="w-9 h-9 sm:w-10 sm:h-10 {{ $colors['bg'] }} rounded-xl flex items-center justify-center mx-auto shadow-lg">
                                    <x-icon name="crown" style="solid" class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                                </div>
                            @else
                                <span class="text-lg sm:text-xl font-black text-gray-400 dark:text-slate-500">#{{ $position }}</span>
                            @endif
                        </div>

                        <!-- Avatar -->
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden border-2 {{ $isCurrentUser ? 'border-indigo-400' : ($isTop3 ? 'border-white dark:border-slate-700' : 'border-gray-200 dark:border-slate-700') }} shadow-md shrink-0">
                            @if($rank->user && $rank->user->avatar_url)
                                <img src="{{ $rank->user->avatar_url }}" alt="{{ $userName }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm sm:text-base font-bold {{ $isCurrentUser ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-900 dark:text-white' }} truncate">
                                {{ $userName }}
                                @if($isCurrentUser)
                                    <span class="ml-2 px-2 py-0.5 bg-indigo-200 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-300 rounded text-[9px] sm:text-[10px] font-black uppercase">Você</span>
                                @endif
                            </h3>
                            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-slate-400 font-medium uppercase tracking-wide">Membro EBD</p>
                        </div>

                        <!-- Score -->
                        <div class="text-right shrink-0">
                            <span class="block text-lg sm:text-2xl font-black {{ $isCurrentUser ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ number_format((int) $rank->total_score, 0, ',', '.') }}</span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Pontos</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 sm:py-20 px-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                            <x-icon name="trophy" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-300 dark:text-slate-600" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum registro ainda</h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Seja o primeiro a entrar no ranking!</p>
                        <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-600/20">
                            <x-icon name="gamepad" class="w-4 h-4" />
                            Jogar Agora
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
