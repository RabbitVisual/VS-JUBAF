@extends('memberpanel::components.layouts.master')

@section('title', 'Central de Jogos - Arcade Bíblico')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12"
     x-data="arcadeHub()"
     x-init="initXpAnimation()">
    <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Central de Jogos</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Aprenda a Palavra de Deus jogando e ganhe XP!</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('memberpanel.ebd.arcade.leaderboard') }}" class="px-3 sm:px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                    <x-icon name="ranking-star" class="w-4 h-4 text-amber-500" />
                    <span class="hidden sm:inline">Ranking</span>
                </a>
                <a href="{{ route('memberpanel.ebd.arcade.leaderboard') }}" class="px-3 sm:px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                    <x-icon name="trophy" class="w-4 h-4 text-indigo-500" />
                    <span class="hidden sm:inline">Meu XP</span>
                </a>
            </div>
        </div>

        <!-- Hero Section with XP -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                    <!-- User Avatar & Level Badge -->
                    <div class="flex items-center gap-4 sm:gap-6">
                        <div class="relative shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-[3px] shadow-xl shadow-indigo-500/20">
                                <div class="w-full h-full rounded-[13px] sm:rounded-[15px] overflow-hidden bg-gray-100 dark:bg-slate-800">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            @php
                                $tierClass = match($levelTier ?? 'bronze') {
                                    'silver' => 'bg-slate-400 shadow-slate-400/30',
                                    'gold' => 'bg-amber-500 shadow-amber-500/30',
                                    'platinum' => 'bg-slate-200 text-slate-900 shadow-slate-300/30',
                                    default => 'bg-amber-600 shadow-amber-500/30',
                                };
                            @endphp
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 sm:w-10 sm:h-10 {{ $tierClass }} rounded-xl flex items-center justify-center shadow-lg border-2 border-white dark:border-slate-900">
                                <span class="{{ ($levelTier ?? '') === 'platinum' ? 'text-slate-900' : 'text-white' }} font-black text-xs sm:text-sm">{{ $userLevel->level_number ?? 1 }}</span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 mb-2">
                                <x-icon name="gamepad-modern" class="w-3 h-3 text-indigo-600 dark:text-indigo-400" />
                                <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Arcade Bíblico</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight truncate">
                                {{ explode(' ', auth()->user()->name)[0] }}
                            </h2>
                            <p class="text-sm text-amber-600 dark:text-amber-400 font-bold">{{ $userLevel->name ?? 'Iniciante' }}</p>
                        </div>
                    </div>

                    <!-- XP Progress Section -->
                    <div class="flex-1 lg:max-w-md">
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <x-icon name="bolt" style="duotone" class="w-5 h-5 text-amber-500" />
                                    <span class="text-sm font-bold text-gray-700 dark:text-slate-300">Experiência</span>
                                </div>
                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded-lg">
                                    Nível {{ $userLevel->level_number ?? 1 }}
                                </span>
                            </div>

                            <!-- Animated XP Bar (progress within current level: 0% → 100%) -->
                            <div class="relative h-4 sm:h-5 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                                <div class="absolute inset-0 bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 rounded-full transition-all duration-1000 ease-out"
                                     :style="'width: ' + xpPercent + '%'"
                                     x-ref="xpBar">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-white/20"></div>
                                    <div class="absolute inset-0 animate-pulse bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-[10px] sm:text-xs font-black text-white drop-shadow-md" x-text="animatedXpInLevel.toLocaleString() + ' / ' + xpToNextLevel.toLocaleString()"></span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-3 text-xs">
                                <div class="space-y-0.5">
                                    @if($hasNextLevel ?? true)
                                    <span class="text-amber-600 dark:text-amber-400 font-bold block">Faltam <strong x-text="Math.max(0, xpToNextLevel - animatedXpInLevel)"></strong> XP para o próximo nível</span>
                                    <span class="text-gray-500 dark:text-slate-400 font-medium">Nível {{ ($userLevel->level_number ?? 1) + 1 }}: {{ number_format($nextLevelXp ?? 100) }} XP total</span>
                                    @else
                                    <span class="text-amber-600 dark:text-amber-400 font-bold">Nível máximo alcançado!</span>
                                    @endif
                                </div>
                                <span class="text-amber-600 dark:text-amber-400 font-bold shrink-0" x-text="xpPercent.toFixed(0) + '%'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 lg:w-auto">
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center border border-gray-100 dark:border-slate-700">
                            <x-icon name="trophy-star" style="duotone" class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500 mx-auto mb-1 sm:mb-2" />
                            <span class="block text-lg sm:text-xl font-black text-gray-900 dark:text-white">{{ number_format($highScore ?? 0) }}</span>
                            <span class="block text-[8px] sm:text-[9px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Record</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center border border-gray-100 dark:border-slate-700">
                            <x-icon name="gamepad" style="duotone" class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-500 mx-auto mb-1 sm:mb-2" />
                            <span class="block text-lg sm:text-xl font-black text-gray-900 dark:text-white">{{ $gamesPlayed ?? 0 }}</span>
                            <span class="block text-[8px] sm:text-[9px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Partidas</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center border border-gray-100 dark:border-slate-700">
                            <x-icon name="fire-flame-curved" style="duotone" class="w-5 h-5 sm:w-6 sm:h-6 text-orange-500 mx-auto mb-1 sm:mb-2" />
                            <span class="block text-lg sm:text-xl font-black text-gray-900 dark:text-white">{{ $userStreak ?? 0 }}</span>
                            <span class="block text-[8px] sm:text-[9px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Ofensiva</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 sm:gap-8">

            <!-- Games Section (3 columns) -->
            <div class="xl:col-span-3 space-y-6 sm:space-y-8">

                <!-- Quiz & Knowledge Games -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <x-icon name="brain-circuit" style="duotone" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Quiz & Conhecimento</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Teste seus conhecimentos bíblicos</p>
                        </div>
                        <span class="ml-auto px-2 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg text-[10px] font-bold">4 Jogos</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.quiz'), 'title' => 'Mestre do Conhecimento', 'description' => 'Responda perguntas bíblicas com combos.', 'icon' => 'brain-circuit', 'color' => 'indigo', 'badge' => '200+ Perguntas'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.whosaidit'), 'title' => 'Quem Disse?', 'description' => 'Adivinhe quem fez a citação famosa.', 'icon' => 'comment-quote', 'color' => 'rose', 'badge' => '80+ Citações'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.hero'), 'title' => 'Herói da Fé', 'description' => 'Descubra personagens por dicas.', 'icon' => 'user-crown', 'color' => 'violet', 'badge' => '30+ Heróis'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.parables'), 'title' => 'Parábolas em Ação', 'description' => 'Conecte parábolas aos ensinos.', 'icon' => 'book-open', 'color' => 'purple', 'badge' => '40 Parábolas'])
                    </div>
                </div>

                <!-- Memory & Match Games -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <x-icon name="cards" style="duotone" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Memória & Associação</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Exercite sua memória bíblica</p>
                        </div>
                        <span class="ml-auto px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-bold">3 Jogos</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.memory'), 'title' => 'Arca da Memória', 'description' => 'Encontre pares bíblicos.', 'icon' => 'cards', 'color' => 'emerald', 'badge' => '24 Pares'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.trio'), 'title' => 'Trio Bíblico', 'description' => 'Encontre grupos de 3 relacionados.', 'icon' => 'clone', 'color' => 'fuchsia', 'badge' => '30+ Trios'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.timeline'), 'title' => 'Linha do Tempo', 'description' => 'Ordene eventos cronologicamente.', 'icon' => 'hourglass-start', 'color' => 'orange', 'badge' => '10 Níveis'])
                    </div>
                </div>

                <!-- Word Games -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-xl">
                            <x-icon name="font-case" style="duotone" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Jogos de Palavras</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Descubra termos e versículos</p>
                        </div>
                        <span class="ml-auto px-2 py-1 bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 rounded-lg text-[10px] font-bold">4 Jogos</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.hangman'), 'title' => 'Forca da Fé', 'description' => 'Adivinhe palavras bíblicas.', 'icon' => 'keyboard', 'color' => 'purple', 'badge' => '100+ Palavras'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.wordsearch'), 'title' => 'Caça-Palavras', 'description' => 'Encontre palavras no grid.', 'icon' => 'magnifying-glass', 'color' => 'teal', 'badge' => '50+ Listas'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.fillblank'), 'title' => 'Complete o Versículo', 'description' => 'Preencha versículos famosos.', 'icon' => 'pen-to-square', 'color' => 'sky', 'badge' => '15+ Versículos'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.crossword'), 'title' => 'Palavras Cruzadas', 'description' => 'Resolva cruzadas bíblicas.', 'icon' => 'grid-2', 'color' => 'cyan', 'badge' => '10 Puzzles'])
                    </div>
                </div>

                <!-- Scripture & Speed Games -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                            <x-icon name="scroll" style="duotone" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Escritura & Velocidade</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Teste agilidade e conhecimento</p>
                        </div>
                        <span class="ml-auto px-2 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-lg text-[10px] font-bold">4 Jogos</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.versemaster'), 'title' => 'VerseMaster', 'description' => 'Monte versículos arrastando.', 'icon' => 'scroll', 'color' => 'amber', 'badge' => 'Memorização'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.sword'), 'title' => 'Espada Afiada', 'description' => 'Identifique livros da Bíblia.', 'icon' => 'sword', 'color' => 'slate', 'badge' => '7 Categorias'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.bookchallenge'), 'title' => 'Desafio dos Livros', 'description' => 'Categorize os 66 livros.', 'icon' => 'book-bible', 'color' => 'amber', 'badge' => '66 Livros'])
                        @include('ebd::games.partials.game-card-new', ['route' => route('memberpanel.ebd.arcade.navigator'), 'title' => 'Navegador Bíblico', 'description' => 'Encontre versículos rápido.', 'icon' => 'compass', 'color' => 'teal', 'badge' => 'Velocidade'])
                    </div>
                </div>
            </div>

            <!-- Sidebar (1 column) -->
            <div class="space-y-6 sm:space-y-8">

                <!-- Ranking Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-indigo-500 to-purple-600">
                        <div class="flex items-center gap-3">
                            <x-icon name="ranking-star" style="duotone" class="w-6 h-6 text-amber-300" />
                            <div>
                                <h3 class="font-bold text-white">Ranking Semanal</h3>
                                <p class="text-xs text-indigo-200">Top Jogadores</p>
                            </div>
                        </div>
                        <a href="{{ route('memberpanel.ebd.arcade.leaderboard') }}" class="text-xs font-bold text-indigo-200 hover:text-white transition-colors">
                            Ver Todos
                        </a>
                    </div>
                    <div class="p-4 sm:p-5 space-y-3">
                        @php
                            $rankColors = [
                                1 => ['bg' => 'bg-amber-500', 'ring' => 'bg-amber-50 dark:bg-amber-900/10', 'border' => 'border-amber-100 dark:border-amber-900/20', 'gradient' => 'from-amber-400 to-orange-500'],
                                2 => ['bg' => 'bg-gray-400 dark:bg-slate-500', 'ring' => 'bg-gray-50 dark:bg-slate-800', 'border' => 'border-gray-100 dark:border-slate-700', 'gradient' => 'from-gray-400 to-gray-500'],
                                3 => ['bg' => 'bg-amber-700', 'ring' => 'bg-gray-50 dark:bg-slate-800', 'border' => 'border-gray-100 dark:border-slate-700', 'gradient' => 'from-amber-600 to-amber-700'],
                            ];
                        @endphp

                        @forelse($weeklyRanking ?? [] as $ranking)
                            @php
                                $position = (int) ($ranking->position ?? 0);
                                $colors = $rankColors[$position] ?? ['bg' => 'bg-gray-300 dark:bg-slate-600', 'ring' => 'bg-gray-50 dark:bg-slate-800', 'border' => 'border-gray-100 dark:border-slate-700', 'gradient' => 'from-gray-400 to-gray-500'];
                                $rankUserName = $ranking->user->name ?? 'Jogador';
                                $initials = collect(explode(' ', $rankUserName))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('');
                            @endphp
                            <div class="flex items-center gap-3 p-3 {{ $colors['ring'] }} rounded-xl border {{ $colors['border'] }}">
                                <span class="w-7 h-7 {{ $colors['bg'] }} rounded-lg flex items-center justify-center text-white font-black text-sm shadow-lg">{{ $position }}</span>
                                <div class="w-9 h-9 rounded-full overflow-hidden bg-gradient-to-br {{ $colors['gradient'] }} flex items-center justify-center">
                                    @if($ranking->user && $ranking->user->avatar_url)
                                        <img src="{{ $ranking->user->avatar_url }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <span class="text-white font-bold text-xs">{{ $initials }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $rankUserName }}</p>
                                    <p class="text-[10px] text-gray-500 dark:text-slate-400">{{ number_format((int) $ranking->total_score) }} pts</p>
                                </div>
                                @if($position === 1)
                                    <x-icon name="crown" class="w-5 h-5 text-amber-500" />
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <x-icon name="trophy" class="w-10 h-10 text-gray-300 dark:text-slate-700 mx-auto mb-2" />
                                <p class="text-sm text-gray-500 dark:text-slate-400">Nenhum ranking ainda</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Seja o primeiro a jogar!</p>
                            </div>
                        @endforelse

                        <!-- Current User -->
                        <div class="border-t border-gray-100 dark:border-slate-800 pt-3 mt-3">
                            <div class="flex items-center gap-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border-2 border-indigo-200 dark:border-indigo-800">
                                <span class="w-7 h-7 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-black text-sm">#{{ $userRank ?? '-' }}</span>
                                <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-indigo-400">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover" alt="">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-indigo-700 dark:text-indigo-300 truncate">Você</p>
                                    <p class="text-[10px] text-indigo-500 dark:text-indigo-400">{{ number_format($userXp ?? 0) }} XP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-1 h-5 bg-indigo-500 rounded-full"></div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white">Resumo</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-slate-800">
                            <span class="text-sm text-gray-500 dark:text-slate-400">Total de Jogos</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">15</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-slate-800">
                            <span class="text-sm text-gray-500 dark:text-slate-400">Partidas Jogadas</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $gamesPlayed ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-slate-800">
                            <span class="text-sm text-gray-500 dark:text-slate-400">XP Total</span>
                            <span class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($userXp ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-slate-400">Melhor Pontuação</span>
                            <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($highScore ?? 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Streak & Tip Card -->
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl sm:rounded-3xl shadow-xl shadow-purple-500/20 p-5 sm:p-6 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                    <x-icon name="fire-flame-curved" class="absolute -right-2 -bottom-2 w-20 h-20 text-white/10" />

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="fire-flame-curved" class="w-6 h-6 text-orange-400" />
                            <span class="text-xs font-black uppercase tracking-widest text-purple-200">Sua Ofensiva</span>
                        </div>

                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-4xl font-black">{{ $userStreak ?? 0 }}</span>
                            <div>
                                <span class="block text-sm font-bold text-purple-200">dias</span>
                                <span class="block text-xs text-purple-300">consecutivos</span>
                            </div>
                        </div>

                        <!-- Streak Progress -->
                        <div class="flex gap-1 mb-4">
                            @for($i = 1; $i <= 7; $i++)
                                <div class="flex-1 h-2 rounded-full {{ ($userStreak ?? 0) >= $i ? 'bg-orange-400' : 'bg-white/20' }}"></div>
                            @endfor
                        </div>

                        <p class="text-sm font-medium leading-relaxed text-purple-200">
                            @if(($userStreak ?? 0) >= 7)
                                <x-icon name="sparkles" class="w-4 h-4 inline text-amber-300" />
                                Bônus de +50% XP ativo!
                            @elseif(($userStreak ?? 0) > 0)
                                Faltam {{ 7 - ($userStreak ?? 0) }} dias para +50% XP!
                            @else
                                Jogue hoje para iniciar sua ofensiva!
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function arcadeHub() {
    return {
        targetXpInLevel: {{ $xpInCurrentLevel ?? 0 }},
        xpToNextLevel: {{ $xpToNextLevel ?? 100 }},
        animatedXpInLevel: 0,
        xpPercent: 0,

        initXpAnimation() {
            this.animateXp();
        },

        animateXp() {
            const duration = 1500;
            const startTime = performance.now();
            const start = 0;
            const target = this.targetXpInLevel;
            const max = this.xpToNextLevel;

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeOut = 1 - Math.pow(1 - progress, 3);
                this.animatedXpInLevel = Math.floor(start + (target - start) * easeOut);
                this.xpPercent = max > 0 ? Math.min(100, (this.animatedXpInLevel / max) * 100) : 0;

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    this.animatedXpInLevel = target;
                    this.xpPercent = max > 0 ? Math.min(100, (target / max) * 100) : 0;
                }
            };

            requestAnimationFrame(animate);
        }
    }
}
</script>
@endsection
