@php
    $hour = date('H');
    $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
    $quickLinks = [];
    $candidates = [
        ['route' => 'admin.settings.index', 'label' => 'Configurações', 'icon' => 'gear'],
        ['route' => 'admin.modules.index', 'label' => 'Módulos', 'icon' => 'puzzle-piece'],
        ['route' => 'admin.users.index', 'label' => 'Usuários', 'icon' => 'users'],
        ['route' => 'treasury.dashboard.index', 'label' => 'Tesouraria', 'icon' => 'sack-dollar'],
        ['route' => 'admin.events.events.index', 'label' => 'Eventos', 'icon' => 'calendar-days'],
        ['route' => 'admin.ebd.dashboard', 'label' => 'EBD', 'icon' => 'book-open'],
        ['route' => 'worship.admin.dashboard', 'label' => 'Louvor', 'icon' => 'music'],
        ['route' => 'admin.sermons.sermons.index', 'label' => 'Sermões', 'icon' => 'microphone'],
        ['route' => 'admin.notifications.control.dashboard', 'label' => 'Notificações', 'icon' => 'bell'],
        ['route' => 'admin.ministries.index', 'label' => 'Ministérios', 'icon' => 'church'],
        ['route' => 'admin.homepage.settings.index', 'label' => 'HomePage', 'icon' => 'house'],
        ['route' => 'admin.bible.plans.index', 'label' => 'Bíblia', 'icon' => 'book-bible'],
        ['route' => 'admin.transactions.index', 'label' => 'Transações', 'icon' => 'credit-card'],
    ];
    foreach ($candidates as $c) {
        if (\Illuminate\Support\Facades\Route::has($c['route'])) {
            $quickLinks[] = $c;
        }
    }
@endphp
@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/10 rounded-full -translate-x-1/2 translate-y-1/2">
            </div>

            <div class="relative p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span
                            class="px-3 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">
                            Painel Administrativo
                        </span>
                        <span
                            class="px-3 py-1.5 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">
                            Sistema Online
                        </span>
                        <span
                            class="px-3 py-1.5 rounded-full bg-gray-500/20 border border-gray-400/30 text-gray-300 text-xs font-bold">
                            {{ $stats['enabled_modules'] ?? 0 }}/{{ $stats['total_modules'] ?? 0 }} módulos ativos
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight mb-2">
                        {{ $greeting }}, {{ auth()->user()->first_name ?? (auth()->user()->name ?? 'Admin') }}!
                    </h1>
                    <p class="text-gray-300 text-lg max-w-xl">
                        Bem-vindo ao centro de controle da sua igreja. Resumo das atividades e atalhos para as principais
                        áreas.
                    </p>

                    <!-- Quick Actions -->
                    <div class="mt-8 flex flex-wrap gap-3">
                        @if (Route::has('lideranca.dashboard') && auth()->user()->hasAdminAccess())
                            <a href="{{ route('lideranca.dashboard') }}"
                                class="px-5 py-3 rounded-xl bg-amber-500/20 backdrop-blur-md border border-amber-400/30 text-amber-200 font-bold hover:bg-amber-500/30 transition-colors flex items-center gap-2">
                                <x-icon name="church" class="w-5 h-5 text-amber-400" />
                                Ir para Gabinete de Liderança
                            </a>
                        @endif
                        @if (Route::has('admin.users.create'))
                            <a href="{{ route('admin.users.create') }}"
                                class="px-5 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-colors flex items-center gap-2 shadow-lg shadow-white/10">
                                <x-icon name="user-plus" class="w-5 h-5 text-blue-600" />
                                Novo Membro
                            </a>
                        @endif
                        @if (Route::has('treasury.entries.create'))
                            <a href="{{ route('treasury.entries.create') }}"
                                class="px-5 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors flex items-center gap-2">
                                <x-icon name="sack-dollar" class="w-5 h-5 text-green-400" />
                                Nova Entrada
                            </a>
                        @endif
                        @if (Route::has('admin.events.events.create'))
                            <a href="{{ route('admin.events.events.create') }}"
                                class="px-5 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors flex items-center gap-2">
                                <x-icon name="calendar-days" class="w-5 h-5 text-purple-400" />
                                Criar Evento
                            </a>
                        @endif
                        @if (Route::has('admin.settings.index'))
                            <a href="{{ route('admin.settings.index') }}"
                                class="px-5 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors flex items-center gap-2">
                                <x-icon name="gear" class="w-5 h-5 text-gray-300" />
                                Configurações
                            </a>
                        @endif
                    </div>
                </div>

                <div class="hidden md:block relative shrink-0">
                    <div
                        class="w-32 h-32 rounded-full bg-linear-to-tr from-blue-500 to-purple-500 p-1 shadow-2xl shadow-blue-500/30 flex items-center justify-center border-4 border-gray-800">
                        @if (auth()->user()->photo)
                            <img src="{{ Storage::url(auth()->user()->photo) }}" alt="Profile"
                                class="w-full h-full rounded-full object-cover">
                        @else
                            <span
                                class="text-3xl font-black text-white tracking-tighter">{{ strtoupper(mb_substr(auth()->user()->first_name ?? (auth()->user()->name ?? 'U'), 0, 1) . mb_substr(auth()->user()->last_name ?? '', 0, 1)) ?: strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Acesso Rápido -->
        @if (count($quickLinks) > 0)
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 shadow-sm border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-6">
                    <x-icon name="bolt" class="w-5 h-5 text-amber-500" />
                    Acesso Rápido
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($quickLinks as $link)
                        <a href="{{ route($link['route']) }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 dark:hover:border-blue-800 transition-all group">
                            <div
                                class="w-11 h-11 rounded-xl bg-gray-200 dark:bg-gray-600 group-hover:bg-blue-100 dark:group-hover:bg-blue-800/50 flex items-center justify-center text-gray-600 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <x-icon name="{{ $link['icon'] }}" class="w-5 h-5" />
                            </div>
                            <span
                                class="text-xs font-bold text-gray-700 dark:text-gray-300 text-center leading-tight">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <!-- Members -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <x-icon name="users" class="w-6 h-6" />
                        </div>
                        <span
                            class="flex items-center text-xs font-bold text-green-600 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-lg">
                            +{{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0 }}%
                            Ativos
                        </span>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Membros</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total_users'] }}</h3>
                    <p class="text-sm text-gray-400 mt-2">{{ $stats['active_users'] }} membros ativos hoje</p>
                </div>
            </div>

            <!-- Treasury -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <x-icon name="sack-dollar" class="w-6 h-6" />
                        </div>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Tesouraria</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">R$
                        {{ number_format($stats['treasury_balance'] ?? 0, 2, ',', '.') }}</h3>
                    <div class="flex items-center gap-3 mt-2 text-xs font-bold">
                        <span class="text-green-500 flex items-center"><x-icon name="arrow-up" class="w-3 h-3 mr-1" /> R$
                            {{ number_format($stats['treasury_income_month'] ?? 0, 2, ',', '.') }}</span>
                        <span class="text-red-500 flex items-center"><x-icon name="arrow-down" class="w-3 h-3 mr-1" /> R$
                            {{ number_format($stats['treasury_expense_month'] ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Events -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <x-icon name="calendar-days" class="w-6 h-6" />
                        </div>
                        <span
                            class="flex items-center text-xs font-bold text-purple-600 bg-purple-100 dark:bg-purple-900/30 px-2 py-1 rounded-lg">
                            {{ $stats['recent_registrations'] ?? 0 }} Inscrições
                        </span>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Eventos</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['upcoming_events'] ?? 0 }}
                    </h3>
                    <p class="text-sm text-gray-400 mt-2">Próximos eventos agendados</p>
                </div>
            </div>

            <!-- EBD/Classes -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-32 h-32 bg-orange-50 dark:bg-orange-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <x-icon name="book-open" class="w-6 h-6" />
                        </div>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Ensino / EBD
                    </p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">
                        {{ $stats['ebd_active_classes'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-400 mt-2">{{ $stats['ebd_students'] ?? 0 }} alunos matriculados</p>
                </div>
            </div>

            <!-- Níveis de gamificação (quando disponível) -->
            @if (isset($gamificationStats) && (($gamificationStats['total_levels'] ?? 0) > 0))
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                    <div
                        class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                    </div>
                    <div class="relative">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <x-icon name="trophy" class="w-6 h-6" />
                            </div>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                            Gamificação</p>
                        <div class="flex items-baseline gap-3 mt-1">
                            <span
                                class="text-2xl font-black text-gray-900 dark:text-white">{{ $gamificationStats['total_levels'] ?? 0 }}</span>
                            <span class="text-sm text-gray-400">níveis</span>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">{{ number_format($gamificationStats['average_points'] ?? 0, 1, ',', '.') }} pontos médios por usuário</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Indicadores por módulo (quando disponíveis) -->
        @php
            $extraIndicators = [];
            if (($stats['notifications_today'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Notificações hoje',
                    'value' => $stats['notifications_today'],
                    'icon' => 'bell',
                    'route' => 'admin.notifications.control.dashboard',
                ];
            }
            if (($stats['sermons_count'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Sermões',
                    'value' => $stats['sermons_count'],
                    'icon' => 'microphone',
                    'route' => 'admin.sermons.sermons.index',
                ];
            }
            if (($stats['worship_songs'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Músicas',
                    'value' => $stats['worship_songs'],
                    'icon' => 'music',
                    'route' => 'worship.admin.songs.index',
                ];
            }
            if (($stats['worship_setlists'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Setlists',
                    'value' => $stats['worship_setlists'],
                    'icon' => 'list',
                    'route' => 'worship.admin.setlists.index',
                ];
            }
            if (($stats['assets_count'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Patrimônio',
                    'value' => $stats['assets_count'],
                    'icon' => 'box-archive',
                    'route' => null,
                ];
            }
            if (($stats['pastoral_alerts'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Alertas pastorais',
                    'value' => $stats['pastoral_alerts'],
                    'icon' => 'bell',
                    'route' => 'admin.notifications.index',
                ];
            }
            if (($stats['council_agendas_pending'] ?? 0) > 0) {
                $extraIndicators[] = [
                    'label' => 'Pautas pendentes',
                    'value' => $stats['council_agendas_pending'],
                    'icon' => 'clipboard-list',
                    'route' => null,
                ];
            }
        @endphp
        @if (count($extraIndicators) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h2
                    class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <x-icon name="chart-mixed" class="w-4 h-4" />
                    Indicadores por módulo
                </h2>
                <div class="flex flex-wrap gap-4">
                    @foreach ($extraIndicators as $ind)
                        @if ($ind['route'] && Route::has($ind['route']))
                            <a href="{{ route($ind['route']) }}"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 transition-colors">
                                <x-icon name="{{ $ind['icon'] }}" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $ind['value'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ind['label'] }}</span>
                            </a>
                        @else
                            <div
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                                <x-icon name="{{ $ind['icon'] }}" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $ind['value'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ind['label'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Financial Overview Chart -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Visão Financeira</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Entradas vs Saídas (últimos 6 meses)</p>
                    </div>
                    @if (Route::has('treasury.dashboard.index'))
                        <a href="{{ route('treasury.dashboard.index') }}"
                            class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                            Ver Tesouraria <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <div class="h-80 w-full relative">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <!-- Member Growth Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Crescimento</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Novos membros</p>
                    </div>
                </div>
                <div class="h-60 w-full relative mb-4">
                    <canvas id="growthChart"></canvas>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300">
                        <x-icon name="arrow-trend-up" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase">Tendência</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Crescimento constante nos últimos meses.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Financial Entries -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="sack-dollar" class="w-5 h-5 text-gray-400" />
                        Movimentações Recentes
                    </h3>
                    @if (Route::has('treasury.dashboard.index'))
                        <a href="{{ route('treasury.dashboard.index') }}"
                            class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver todos</a>
                    @endif
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentEntries as $entry)
                        <div
                            class="p-4 px-8 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-full {{ $entry->type == 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center">
                                    <x-icon name="{{ $entry->type == 'income' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $entry->description }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($entry->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span
                                class="font-mono font-bold {{ $entry->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $entry->type == 'income' ? '+' : '-' }} R$
                                {{ number_format($entry->amount, 2, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">Nenhuma movimentação recente.</div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Events / Registrations -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="calendar-days" class="w-5 h-5 text-gray-400" />
                        Próximos Eventos
                    </h3>
                    @if (Route::has('admin.events.events.index'))
                        <a href="{{ route('admin.events.events.index') }}"
                            class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver todos</a>
                    @endif
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($upcomingEvents as $event)
                        <div
                            class="p-4 px-8 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex flex-col items-center justify-center w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                                    <span
                                        class="text-[10px] uppercase text-red-500 font-bold">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</span>
                                    <span
                                        class="text-lg font-bold text-gray-900 dark:text-white leading-none">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $event->title ?? ($event->name ?? 'Evento') }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} •
                                        {{ $event->location ?? 'Local à definir' }}</p>
                                </div>
                            </div>
                            @if (Route::has('admin.events.events.show') && isset($event->id))
                                <a href="{{ route('admin.events.events.show', $event->id) }}"
                                    class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">Detalhes</a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">Nenhum evento agendado.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Financial Chart
            const ctxFinancial = document.getElementById('financialChart').getContext('2d');
            new Chart(ctxFinancial, {
                type: 'bar',
                data: {
                    labels: @json($financialChart['labels'] ?? []),
                    datasets: [{
                            label: 'Entradas',
                            data: @json($financialChart['income'] ?? []),
                            backgroundColor: '#10B981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Saídas',
                            data: @json($financialChart['expense'] ?? []),
                            backgroundColor: '#EF4444',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Growth Chart
            const ctxGrowth = document.getElementById('growthChart').getContext('2d');
            new Chart(ctxGrowth, {
                type: 'line',
                data: {
                    labels: @json($growthChart['labels'] ?? []),
                    datasets: [{
                        label: 'Novos Membros',
                        data: @json($growthChart['data'] ?? []),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            display: false
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
