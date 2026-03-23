@extends('memberpanel::components.layouts.master')

@section('page-title', $ministry->name)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm font-medium text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm font-medium text-rose-800 dark:text-rose-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif
            <!-- Breadcrumb & Back -->
            <div class="flex items-center justify-between">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-slate-400 font-medium">
                <a href="{{ route('memberpanel.ministries.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors uppercase tracking-widest text-[10px] font-black">Ministérios</a>
                <x-icon name="chevron-right" class="w-3 h-3 opacity-50" />
                <span class="text-gray-900 dark:text-white font-bold">{{ $ministry->name }}</span>
            </nav>
            <a href="{{ route('memberpanel.ministries.index') }}" class="group flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl text-[10px] font-black text-gray-600 dark:text-slate-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <x-icon name="arrow-left" class="w-3.5 h-3.5 group-hover:-translate-x-1 transition-transform" />
                Voltar
            </a>
            </div>

            <!-- Ministry Hero -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-400 dark:bg-blue-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 left-1/2 w-64 h-64 bg-indigo-300 dark:bg-indigo-500 rounded-full blur-[80px]"></div>
                </div>

                <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-10 z-10">
                    @php
                        $heroIconBox = [
                            'blue' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                            'indigo' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                            'purple' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                            'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                            'amber' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                            'rose' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                        ];
                        $heroBoxClass = $heroIconBox[$ministry->color ?? ''] ?? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800';
                    @endphp
                    <div class="shrink-0 w-28 h-28 md:w-32 md:h-32 rounded-2xl border-2 {{ $heroBoxClass }} flex items-center justify-center shadow-lg">
                        @if($ministry->icon && \Str::startsWith($ministry->icon, 'fa:'))
                            <x-icon name="{{ \Str::after($ministry->icon, 'fa:') }}" class="w-16 h-16 md:w-20 md:h-20 text-current" />
                        @elseif($ministry->icon && strlen($ministry->icon) < 10)
                            <span class="text-5xl md:text-6xl">{{ $ministry->icon }}</span>
                        @else
                            <x-icon name="church" class="w-16 h-16 md:w-20 md:h-20 text-current" />
                        @endif
                    </div>

                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                                <x-icon name="sparkles" class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">Institucional</span>
                            </span>
                            @if($ministry->is_active)
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-full text-[10px] font-black uppercase tracking-widest">Ativo</span>
                            @endif
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                            {{ $ministry->name }}
                        </h1>
                        @if($ministry->description)
                            <p class="text-gray-500 dark:text-slate-300 font-medium leading-relaxed max-w-2xl">
                                {{ $ministry->description }}
                            </p>
                        @endif
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-700 rounded-xl">
                                <x-icon name="users" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                <span class="text-sm font-black text-gray-900 dark:text-white">{{ $ministry->active_members_count }} Voluntários</span>
                            </div>
                            @if($ministry->max_members)
                                <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-700 rounded-xl">
                                    <x-icon name="user-group-crown" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                    <span class="text-sm font-black text-gray-900 dark:text-white">Capacidade: {{ $ministry->max_members }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Navigation -->
            <div x-data="{ tab: 'geral' }" class="space-y-8">
                <div class="flex items-center p-1 bg-gray-100 dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 w-full overflow-x-auto">
                    <button @click="tab = 'geral'" :class="tab === 'geral' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'" class="flex-1 min-w-[120px] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                        <x-icon name="gauge-high" class="w-3.5 h-3.5" />
                        Dashboard
                    </button>
                    <button @click="tab = 'planejamento'" :class="tab === 'planejamento' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'" class="flex-1 min-w-[140px] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                        <x-icon name="clipboard-list" class="w-3.5 h-3.5" />
                        Planejamento
                    </button>
                    <button @click="tab = 'membros'" :class="tab === 'membros' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'" class="flex-1 min-w-[120px] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                        <x-icon name="users-medical" class="w-3.5 h-3.5" />
                        Equipe
                    </button>
                    <button @click="tab = 'financeiro'" :class="tab === 'financeiro' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'" class="flex-1 min-w-[120px] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                        <x-icon name="coins" class="w-3.5 h-3.5" />
                        Recursos & Assets
                    </button>
                    <button @click="tab = 'eventos'" :class="tab === 'eventos' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'" class="flex-1 min-w-[120px] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                        <x-icon name="file-chart-pie" class="w-3.5 h-3.5" />
                        Relatórios
                    </button>
                </div>

                <!-- Tab Content: Dashboard -->
                <div x-show="tab === 'geral'" x-transition class="space-y-8">
                    <!-- Leadership Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Leader -->
                        <div class="group bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="user-tie" class="w-6 h-6" />
                                </div>
                                <h3 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Direção Geral</h3>
                            </div>

                            @if($ministry->leader)
                                <div class="flex items-center gap-4">
                                    <div class="relative shrink-0">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-white dark:border-slate-800 shadow-md ring-2 ring-blue-500/20">
                                            <img class="w-full h-full object-cover" src="{{ $ministry->leader->avatar_url }}" alt="{{ $ministry->leader->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-black text-xl" style="display: none;">
                                                {{ strtoupper(substr($ministry->leader->name ?? '?', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-500 border-2 border-white dark:border-slate-900 rounded-lg flex items-center justify-center">
                                            <x-icon name="check-double" class="w-3 h-3 text-white" />
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-lg font-black text-gray-900 dark:text-white tracking-tight truncate">{{ $ministry->leader->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-slate-400 font-medium mt-0.5 truncate">{{ $ministry->leader->email }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-6 text-center text-gray-400 dark:text-slate-500 italic font-medium">
                                    <x-icon name="user-slash" class="w-8 h-8 opacity-20 mb-2" />
                                    Liderança não atribuída
                                </div>
                            @endif
                        </div>

                        <!-- Co-Leader -->
                        <div class="group bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <x-icon name="users-gear" class="w-6 h-6" />
                                </div>
                                <h3 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Co-Liderança</h3>
                            </div>

                            @if($ministry->coLeader)
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-white dark:border-slate-800 shadow-md shrink-0">
                                        <img class="w-full h-full object-cover" src="{{ $ministry->coLeader->avatar_url }}" alt="{{ $ministry->coLeader->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full bg-purple-500 flex items-center justify-center text-white font-black text-xl" style="display: none;">
                                            {{ strtoupper(substr($ministry->coLeader->name ?? '?', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-lg font-black text-gray-900 dark:text-white tracking-tight truncate">{{ $ministry->coLeader->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-slate-400 font-medium mt-0.5 truncate">{{ $ministry->coLeader->email }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-6 text-center text-gray-400 dark:text-slate-500 italic font-medium">
                                    <x-icon name="user-group" class="w-8 h-8 opacity-20 mb-2" />
                                    Suporte não atribuído
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Leader Dashboard Summary Cards -->
                    @if($isLeader)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-gray-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Voluntários ativos</p>
                                    <p class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $ministry->active_members_count }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="users" class="w-5 h-5" />
                                </div>
                            </div>
                            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-gray-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Saldo do mês</p>
                                    @if(isset($treasurySummary) && $treasurySummary)
                                        @php $balance = (float)($treasurySummary['balance'] ?? 0); @endphp
                                        <p class="mt-1 text-xl font-black {{ $balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            R$ {{ number_format($balance, 2, ',', '.') }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-xl font-black text-gray-400 dark:text-slate-600">—</p>
                                    @endif
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <x-icon name="vault" class="w-5 h-5" />
                                </div>
                            </div>
                            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-gray-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Status do relatório</p>
                                    @php
                                        $statusColor = 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
                                        $statusText = 'Pendente';
                                        $dot = 'bg-amber-500';
                                        if(isset($currentMonthReport) && $currentMonthReport && $currentMonthReport->status === 'submitted') {
                                            $statusColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300';
                                            $statusText = 'Enviado';
                                            $dot = 'bg-emerald-500';
                                        }
                                    @endphp
                                    <div class="mt-1 inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $statusColor }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                        <span>{{ $statusText }}</span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-300">
                                    <x-icon name="traffic-light" class="w-5 h-5" />
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Member Status Action Card -->
                    @if($isMember)
                        <div class="group relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm border-l-4 border-l-emerald-500">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                                <div class="flex items-center gap-4 text-center md:text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <x-icon name="crown" class="w-8 h-8" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Vínculo Ativo</h3>
                                        <div class="flex flex-wrap justify-center md:justify-start gap-x-6 gap-y-1 mt-1 text-gray-500 dark:text-slate-400 font-medium text-sm">
                                            <p class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> {{ ucfirst(str_replace('_', ' ', $memberInfo->role ?? 'member')) }}</p>
                                            @if($memberInfo->joined_at ?? null)
                                                <p class="flex items-center gap-2"><x-icon name="clock" class="w-4 h-4" /> Membro desde {{ \Carbon\Carbon::parse($memberInfo->joined_at)->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" class="px-5 py-2.5 bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-black text-gray-700 dark:text-slate-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">Meus Registros</button>
                                    @if(!$isLeader)
                                        <form action="{{ route('memberpanel.ministries.leave', $ministry) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja sair deste ministério?');">
                                            @csrf
                                            <button type="submit" class="px-5 py-2.5 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all flex items-center gap-2">
                                                <x-icon name="door-open" class="w-4 h-4" />
                                                Abandonar Equipe
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm border-l-4 border-l-blue-500">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <x-icon name="hand-holding-seedling" class="w-8 h-8" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Deseja ser parte?</h3>
                                        <p class="text-gray-500 dark:text-slate-400 font-medium mt-1 text-sm">
                                            @if($ministry->requires_approval)
                                                Sua solicitação passará por análise da diretoria do ministério.
                                            @else
                                                A participação é livre e imediata para este ministério.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('memberpanel.ministries.join', $ministry) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-sm tracking-widest uppercase transition-all shadow-lg hover:shadow-blue-500/20 active:scale-[0.98] flex items-center gap-3">
                                        <x-icon name="user-plus" class="w-5 h-5" />
                                        Participar Agora
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Tab Content: Planejamento -->
                <div x-show="tab === 'planejamento'" x-transition class="space-y-6">
                    @if($isLeader)
                        @if(isset($currentPlan) && $currentPlan)
                            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                        <x-icon name="clipboard-list" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Plano em execução</h3>
                                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $currentPlan->title }} · {{ $currentPlan->period_start->format('d/m/Y') }} – {{ $currentPlan->period_end->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-slate-300">{{ $currentPlan->objectives ? \Str::limit($currentPlan->objectives, 280) : 'Plano em execução.' }}</p>
                                @if($currentPlan->budget_requested)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">Orçamento planejado: R$ {{ number_format((float)$currentPlan->budget_requested, 2, ',', '.') }}</p>
                                @endif
                            </div>
                            @if(isset($ministryEvents) && $ministryEvents->isNotEmpty())
                                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm mt-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                            <x-icon name="calendar-star" class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Linha do tempo de atividades</h3>
                                            <p class="text-sm text-gray-500 dark:text-slate-400">Eventos aprovados e vinculados a este ministério.</p>
                                        </div>
                                    </div>
                                    <div class="relative mt-4">
                                        <div class="absolute left-3 top-0 bottom-0 w-px bg-gray-200 dark:bg-slate-700"></div>
                                        <ul class="space-y-4">
                                            @foreach($ministryEvents as $ev)
                                                @php
                                                    $statusLabel = match($ev->status ?? '') {
                                                        'published' => 'Publicado',
                                                        'waiting_approval' => 'Aguardando Conselho',
                                                        'closed' => 'Encerrado',
                                                        default => 'Rascunho',
                                                    };
                                                    $statusClass = match($ev->status ?? '') {
                                                        'published' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                        'waiting_approval' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                                        'closed' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                    };
                                                @endphp
                                                <li class="relative pl-8">
                                                    <span class="absolute left-1.5 top-2 w-3 h-3 rounded-full bg-white dark:bg-slate-900 ring-2 ring-blue-500"></span>
                                                    <div class="flex flex-wrap items-center justify-between gap-3 p-3 rounded-2xl border border-gray-100 dark:border-slate-800 bg-gray-50/60 dark:bg-slate-900/40">
                                                        <div>
                                                            <p class="font-bold text-gray-900 dark:text-white">{{ $ev->title }}</p>
                                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $ev->start_date ? $ev->start_date->format('d/m/Y H:i') : '—' }}</p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span>
                                                            @if($ev->status === 'published' && Route::has('memberpanel.events.show'))
                                                                <a href="{{ route('memberpanel.events.show', $ev) }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline">Ver</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-dashed border-gray-200 dark:border-slate-700 mt-6 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-400 dark:text-slate-500">
                                        <x-icon name="calendar-exclamation" class="w-8 h-8" />
                                    </div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-slate-300">Ainda não há atividades futuras aprovadas vinculadas a este plano.</p>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Ao gerar eventos a partir do planejamento, eles aparecerão aqui.</p>
                                </div>
                            @endif
                        @else
                            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-dashed border-gray-200 dark:border-slate-700 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-400 dark:text-slate-500">
                                    <x-icon name="clipboard-question" class="w-8 h-8" />
                                </div>
                                <p class="text-sm font-medium text-gray-600 dark:text-slate-300">Nenhum plano estratégico ativo para este ministério.</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Use o painel administrativo para cadastrar o primeiro planejamento.</p>
                            </div>
                        @endif

                        @if(isset($currentMonthReport) && $currentMonthReport)
                            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm">
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                            <x-icon name="document-text" class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Relatório do mês</h3>
                                            <p class="text-sm text-gray-500 dark:text-slate-400">
                                                @if($currentMonthReport->status === 'submitted')
                                                    Enviado em {{ $currentMonthReport->submitted_at?->format('d/m/Y') }}
                                                @else
                                                    Rascunho em andamento
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($currentMonthReport->status !== 'submitted')
                                        <a href="{{ route('memberpanel.ministries.reports.edit', [$ministry, $currentMonthReport]) }}" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-amber-600 transition-all flex items-center gap-2">
                                            <x-icon name="pen-to-square" class="w-4 h-4" />
                                            Editar / Enviar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm">
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-500 dark:text-slate-400">
                                            <x-icon name="pen-to-square" class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Relatório do mês</h3>
                                            <p class="text-sm text-gray-500 dark:text-slate-400">Relatório de {{ now()->translatedFormat('F/Y') }} ainda não enviado.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('memberpanel.ministries.reports.create', $ministry) }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-blue-700 transition-all flex items-center gap-2">
                                        <x-icon name="paper-plane" class="w-4 h-4" />
                                        Criar relatório
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-dashed border-gray-200 dark:border-slate-700 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-400 dark:text-slate-500">
                                <x-icon name="circle-info" class="w-7 h-7" />
                            </div>
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-300">Acompanhe aqui o plano e os relatórios quando estiver como líder ou co-líder.</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Por enquanto, ore e apoie sua liderança nas metas deste ministério.</p>
                        </div>
                    @endif
                </div>

                <!-- Tab Content: Membros -->
                <div x-show="tab === 'membros'" x-transition class="space-y-6">
                    @if($isLeader && ($pendingForLeader ?? collect())->isNotEmpty())
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-amber-200 dark:border-amber-800 shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-amber-100 dark:border-amber-900/50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-amber-50/50 dark:bg-amber-900/10">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                                        <x-icon name="user-clock" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Solicitações de filiação</h2>
                                        <p class="text-sm text-gray-500 dark:text-slate-400 font-medium">Aceite para submeter ao conselho ou recuse.</p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 bg-amber-100 dark:bg-amber-900/30 rounded-xl text-[10px] font-black text-amber-700 dark:text-amber-300 uppercase tracking-widest border border-amber-200 dark:border-amber-800">
                                    {{ ($pendingForLeader ?? collect())->count() }} Pendente(s)
                                </span>
                            </div>
                            <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                                @foreach($pendingForLeader as $applicant)
                                    <li class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border-2 border-amber-200 dark:border-amber-800">
                                                <img class="w-full h-full object-cover" src="{{ $applicant->avatar_url ?? '' }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-full h-full bg-amber-500 flex items-center justify-center text-white font-bold text-sm" style="display: none;">{{ strtoupper(substr($applicant->name ?? '?', 0, 1)) }}</div>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $applicant->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $applicant->email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <form action="{{ route('memberpanel.ministries.requests.accept', [$ministry, $applicant]) }}" method="POST" class="inline" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Enviando ao conselho...' } }))">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors shadow-sm">
                                                    <x-icon name="circle-check" class="w-4 h-4" />
                                                    Aceitar e submeter ao conselho
                                                </button>
                                            </form>
                                            <form action="{{ route('memberpanel.ministries.requests.reject', [$ministry, $applicant]) }}" method="POST" class="inline" onsubmit="return confirm('Recusar esta solicitação?');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 text-sm font-bold transition-colors border border-gray-200 dark:border-slate-700">
                                                    <x-icon name="circle-xmark" class="w-4 h-4" />
                                                    Recusar
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                                    <x-icon name="users-viewfinder" class="w-5 h-5" />
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Quadro de Voluntários</h2>
                                    <p class="text-sm text-gray-500 dark:text-slate-400 font-medium">Base de membros ativos neste ministério.</p>
                                </div>
                            </div>
                            <span class="px-4 py-2 bg-white dark:bg-slate-800 rounded-xl text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest border border-gray-200 dark:border-slate-700">
                                {{ $ministry->activeMembers->count() }} Ativos
                            </span>
                        </div>
                        <div class="p-6">
                            @if($ministry->activeMembers->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($ministry->activeMembers as $member)
                                        <div class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-all group">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border-2 border-white dark:border-slate-800 shadow-sm ring-1 ring-gray-200 dark:ring-slate-700">
                                                <img class="w-full h-full object-cover" src="{{ $member->avatar_url }}" alt="{{ $member->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-black text-sm" style="display: none;">
                                                    {{ strtoupper(substr($member->name ?? '?', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-black text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $member->name }}</p>
                                                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold truncate uppercase tracking-widest">Voluntário</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300 dark:text-slate-600">
                                        <x-icon name="user-group-slash" class="w-8 h-8" />
                                    </div>
                                    <p class="text-gray-500 dark:text-slate-400 font-medium">Nenhum voluntário ativo listado ainda.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Financeiro -->
                <div x-show="tab === 'financeiro'" x-transition class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <x-icon name="vial-circle-check" class="w-6 h-6" />
                                </div>
                                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Apoio Direto</h3>
                            </div>
                            <p class="text-gray-500 dark:text-slate-400 font-medium leading-relaxed text-sm mb-4">Este ministério conta com ofertas voluntárias para manutenção de suas atividades e infraestrutura.</p>
                            @if(Route::has('donation.create'))
                                <a href="{{ route('donation.create', ['category' => $ministry->name]) }}" target="_blank" class="w-full py-3.5 bg-emerald-600 text-white rounded-xl font-black text-sm uppercase tracking-widest flex items-center justify-center gap-3 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-[0.98]">
                                    <x-icon name="heart-circle-plus" class="w-5 h-5" />
                                    Fazer uma Oferta
                                </a>
                            @else
                                <a href="{{ route('memberpanel.donations.create') }}" class="w-full py-3.5 bg-emerald-600 text-white rounded-xl font-black text-sm uppercase tracking-widest flex items-center justify-center gap-3 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-[0.98]">
                                    <x-icon name="heart-circle-plus" class="w-5 h-5" />
                                    Fazer uma Oferta
                                </a>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="file-invoice-dollar" class="w-6 h-6" />
                                </div>
                                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Caixa Ministério</h3>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 mb-4">
                                <div class="flex items-end justify-between">
                                    <div class="space-y-0.5">
                                        <p class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Saldo do mês (receitas − despesas)</p>
                                        @if(isset($treasurySummary) && $treasurySummary)
                                            <p class="text-2xl font-black {{ ($treasurySummary['balance'] ?? 0) >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }}">
                                                R$ {{ number_format((float)($treasurySummary['balance'] ?? 0), 2, ',', '.') }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 dark:text-slate-400 mt-1">Receitas: R$ {{ number_format((float)($treasurySummary['total_income'] ?? 0), 2, ',', '.') }} · Despesas: R$ {{ number_format((float)($treasurySummary['total_expense'] ?? 0), 2, ',', '.') }}</p>
                                        @else
                                            <p class="text-2xl font-black text-gray-900 dark:text-white">—</p>
                                            <p class="text-[10px] text-gray-500 dark:text-slate-400">Módulo Tesouraria ou permissão não disponível</p>
                                        @endif
                                    </div>
                                    <x-icon name="vault" class="w-8 h-8 text-gray-200 dark:text-slate-600" />
                                </div>
                            </div>
                            <p class="text-[10px] text-center text-gray-400 dark:text-slate-500 font-black uppercase tracking-widest italic">Consolidado via Módulo Tesouraria</p>
                        </div>
                    </div>

                    @if($isLeader && class_exists(\Modules\Assets\App\Models\AssetReservation::class))
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                    <x-icon name="box-open" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Equipamentos</h3>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Solicite reserva de patrimônio para atividades do ministério.</p>
                                </div>
                            </div>
                            <a href="{{ route('memberpanel.ministries.reservations.create', $ministry) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-xs font-black text-amber-700 dark:text-amber-300 uppercase tracking-widest hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-all">
                                <x-icon name="plus" class="w-4 h-4" /> Solicitar equipamentos
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Tab Content: Relatórios -->
                <div x-show="tab === 'eventos'" x-transition class="space-y-6">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <x-icon name="file-chart-pie" class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Relatórios recentes do ministério</h2>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Linha do tempo dos últimos envios mensais.</p>
                            </div>
                        </div>
                        <div class="p-6">
                            @if(isset($recentReports) && $recentReports->isNotEmpty())
                                <ul class="space-y-4">
                                    @foreach($recentReports as $rep)
                                        @php
                                            $statusLabel = $rep->status === 'submitted' ? 'Enviado' : 'Rascunho';
                                            $statusClass = $rep->status === 'submitted'
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'
                                                : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
                                            $monthName = \Carbon\Carbon::createFromDate($rep->report_year, $rep->report_month, 1)->translatedFormat('F/Y');
                                        @endphp
                                        <li class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-all">
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white">{{ $monthName }}</p>
                                                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                                                    {{ \Str::limit($rep->qualitative_summary ?? 'Sem resumo preenchido.', 90) }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span>
                                                @if($isLeader)
                                                    <a href="{{ route('memberpanel.ministries.reports.edit', [$ministry, $rep]) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline">Abrir</a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300 dark:text-slate-600">
                                        <x-icon name="file-circle-question" class="w-8 h-8" />
                                    </div>
                                    <p class="text-gray-500 dark:text-slate-400 font-medium">Nenhum relatório foi enviado para este ministério ainda.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
