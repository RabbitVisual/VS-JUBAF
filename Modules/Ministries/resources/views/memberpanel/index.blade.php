@extends('memberpanel::components.layouts.master')


@section('page-title', 'Meus Ministérios')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8" data-tour="ministries-list">
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
            <!-- Hero Section -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
                <!-- Decorative mesh (dashboard-style) -->
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-400 dark:bg-blue-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 left-1/2 w-64 h-64 bg-indigo-300 dark:bg-indigo-500 rounded-full blur-[80px]"></div>
                </div>

                <div class="relative px-8 py-10 flex flex-col lg:flex-row items-center justify-between gap-10 z-10">
                    <div class="flex-1 text-center lg:text-left space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                            <x-icon name="hands-holding-heart" class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">Corpo de Cristo em Ação</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                            Explore seus <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-500 to-indigo-500">Talentos</span>
                        </h1>
                        <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-lg leading-relaxed mx-auto lg:mx-0">
                            Descubra onde seus dons podem fazer a diferença. Encontre um ministério, junte-se à equipe e sirva ao próximo com propósito.
                        </p>
                    </div>
                    <div class="shrink-0 bg-white/60 dark:bg-slate-800/60 backdrop-blur-xl border border-gray-100 dark:border-white/10 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                        <div class="w-14 h-14 rounded-xl bg-linear-to-br from-blue-500 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <x-icon name="church" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-white/40 text-[10px] font-black uppercase tracking-widest mb-0.5">Ministérios</p>
                            <p class="text-gray-900 dark:text-white font-black text-2xl tracking-tight">{{ $myMinistries->count() + $availableMinistries->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How It Works -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="circle-info" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Jornada do Voluntário</h3>
                </div>
                <div class="p-8">
                    <div class="grid md:grid-cols-3 gap-8">
                        @php
                            $steps = [
                                ['icon' => 'magnifying-glass-plus', 'box' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400', 'title' => 'Explore', 'desc' => 'Conheça os ministérios disponíveis e encontre aquele que mais se alinha com seus dons e interesses.'],
                                ['icon' => 'hand-pointer', 'box' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400', 'title' => 'Participe', 'desc' => 'Solicite sua entrada. Alguns ministérios requerem aprovação da liderança para garantir o alinhamento.'],
                                ['icon' => 'heart-pulse', 'box' => 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400', 'title' => 'Sirva', 'desc' => 'Após a aprovação, você participará ativamente das atividades e servirá junto com o corpo de Cristo.'],
                            ];
                        @endphp
                        @foreach($steps as $step)
                            <div class="flex flex-col items-center text-center space-y-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $step['box'] }}">
                                    <x-icon name="{{ $step['icon'] }}" class="w-6 h-6" />
                                </div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white">{{ $step['title'] }}</h4>
                                <p class="text-sm text-gray-500 dark:text-slate-400 font-medium leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- My Ministries -->
            @if($myMinistries->count() > 0)
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div class="space-y-1">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="badge-check" class="w-4 h-4" />
                                </div>
                                Meus Ministérios
                            </h2>
                            <p class="text-gray-500 dark:text-slate-400 font-medium">Equipes onde você participa ativamente.</p>
                        </div>
                        <span class="px-4 py-2 bg-gray-100 dark:bg-slate-800 rounded-xl text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest border border-gray-200 dark:border-slate-700">
                            {{ $myMinistries->count() }} Atribuição(ões)
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $ministryIconBox = [
                                'blue' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                                'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400',
                                'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
                                'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400',
                                'amber' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
                                'rose' => 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400',
                            ];
                        @endphp
                        @foreach($myMinistries as $ministry)
                            @php $iconBoxClass = $ministryIconBox[$ministry->color ?? ''] ?? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'; @endphp
                            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 {{ $iconBoxClass }}">
                                        @if($ministry->icon && \Str::startsWith($ministry->icon, 'fa:'))
                                            <x-icon name="{{ \Str::after($ministry->icon, 'fa:') }}" class="w-6 h-6 text-current" />
                                        @elseif($ministry->icon && strlen($ministry->icon) < 10)
                                            <span class="text-2xl">{{ $ministry->icon }}</span>
                                        @else
                                            <x-icon name="users-crown" class="w-6 h-6 text-current" />
                                        @endif
                                    </div>
                                    <span class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Ativo</span>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">{{ $ministry->name }}</h3>
                                        @if($ministry->leader)
                                            <div class="flex items-center gap-2 mt-2">
                                                <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-white dark:ring-slate-800 shadow-sm shrink-0">
                                                    <img src="{{ $ministry->leader->avatar_url }}" alt="{{ $ministry->leader->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-black text-[10px]" style="display: none;">
                                                        {{ strtoupper(substr($ministry->leader->name ?? '?', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest block">Liderança</span>
                                                    <span class="text-xs font-bold text-gray-700 dark:text-slate-300 truncate block">{{ $ministry->leader->name }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if($ministry->description)
                                        <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2 leading-relaxed font-medium">{{ $ministry->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800">
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800">
                                        <x-icon name="users" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                        <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400">{{ $ministry->active_members_count }} Membros</span>
                                    </div>
                                    <a href="{{ route('memberpanel.ministries.show', $ministry) }}" class="inline-flex items-center text-sm font-black text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Gerenciar
                                        <x-icon name="arrow-right" class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" />
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Available Ministries -->
            @if($availableMinistries->count() > 0)
                @php
                    $availableIconBox = [
                        'blue' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                        'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400',
                        'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
                        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400',
                        'amber' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
                        'rose' => 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400',
                    ];
                @endphp
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div class="space-y-1">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <x-icon name="bullhorn" class="w-4 h-4" />
                                </div>
                                Novas Oportunidades
                            </h2>
                            <p class="text-gray-500 dark:text-slate-400 font-medium">Explore e colabore com outros ministérios.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($availableMinistries as $ministry)
                            @php $iconBoxClass = $availableIconBox[$ministry->color ?? ''] ?? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400'; @endphp
                            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 {{ $iconBoxClass }}">
                                        @if($ministry->icon && \Str::startsWith($ministry->icon, 'fa:'))
                                            <x-icon name="{{ \Str::after($ministry->icon, 'fa:') }}" class="w-6 h-6 text-current" />
                                        @elseif($ministry->icon && strlen($ministry->icon) < 10)
                                            <span class="text-2xl">{{ $ministry->icon }}</span>
                                        @else
                                            <x-icon name="handshake" class="w-6 h-6 text-current" />
                                        @endif
                                    </div>
                                    @if($ministry->requires_approval)
                                        <span class="px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Sob Consulta</span>
                                    @else
                                        <span class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Livre</span>
                                    @endif
                                </div>
                                <div class="space-y-4">
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors truncate">{{ $ministry->name }}</h3>
                                    @if($ministry->description)
                                        <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2 leading-relaxed font-medium">{{ $ministry->description }}</p>
                                    @endif
                                    <div class="flex items-center justify-between py-2 px-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-icon name="users" class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500" />
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 whitespace-nowrap">
                                                {{ $ministry->active_members_count }}
                                                @if($ministry->max_members)
                                                    <span class="text-gray-400 dark:text-slate-500 font-medium">/ {{ $ministry->max_members }}</span>
                                                @endif
                                            </span>
                                        </div>
                                        <span class="text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Vagas</span>
                                    </div>
                                </div>
                                <form action="{{ route('memberpanel.ministries.join', $ministry) }}" method="POST" class="w-full mt-4" data-tour="ministries-join">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-black text-sm transition-all shadow-lg hover:shadow-gray-500/20 active:scale-[0.98] flex items-center justify-center gap-3 group/btn">
                                        <x-icon name="plus-circle" class="w-5 h-5 group-hover/btn:rotate-90 transition-transform" />
                                        Solicitar Participação
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if($myMinistries->count() == 0 && $availableMinistries->count() == 0)
                <div class="flex flex-col items-center justify-center py-24 px-8 text-center bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <x-icon name="user-group-slash" class="w-10 h-10 text-gray-300 dark:text-slate-600" />
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Nenhum ministério encontrado</h3>
                    <p class="text-gray-500 dark:text-slate-400 font-medium max-w-sm mx-auto leading-relaxed">Não há ministérios ativos ou disponíveis no momento. Entre em contato com a secretaria para mais informações.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
