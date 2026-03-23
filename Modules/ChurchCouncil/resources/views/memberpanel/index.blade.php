@extends('memberpanel::components.layouts.master')

@section('title', __('churchcouncil::messages.welcome_council'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
    <!-- Hero Section -->
    <div class="relative bg-linear-to-r from-blue-900 to-blue-800 dark:from-slate-900 dark:to-slate-950 border-b border-blue-200 dark:border-blue-900/30 p-6 md:p-10 transition-colors duration-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-8">
            <!-- Avatar -->
            <div class="relative group">
                 <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white dark:border-blue-500 overflow-hidden shadow-[0_0_20px_rgba(59,130,246,0.3)] bg-white dark:bg-slate-800">
                    @if (auth()->user()->photo)
                        <img class="w-full h-full object-cover" src="{{ asset('storage/' . auth()->user()->photo) }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-3xl font-black text-blue-600 dark:text-white bg-blue-100 dark:bg-slate-800">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                 <div class="absolute bottom-0 right-0 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full border border-white dark:border-slate-900">
                   {{ $member->role_display }}
                </div>
            </div>

            <!-- Header Info -->
            <div class="flex-1 w-full text-center md:text-left">
                <h1 class="text-3xl font-bold text-white mb-2">{{ __('churchcouncil::messages.welcome_user') }}, <span class="text-blue-300 dark:text-blue-500">{{ explode(' ', auth()->user()->name)[0] }}</span>!</h1>
                <p class="text-blue-100 dark:text-slate-400 max-w-2xl mx-auto md:mx-0">
                    {{ __('churchcouncil::messages.council_panel_intro') }}
                </p>

                <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-4">
                     <a href="{{ route('memberpanel.churchcouncil.profile.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 dark:bg-slate-800 dark:hover:bg-slate-700 text-white dark:text-slate-200 rounded-lg text-sm font-bold border border-white/20 dark:border-slate-700 transition-all hover:border-white/40 dark:hover:border-blue-500/50">
                        <x-icon name="circle-user" class="w-5 h-5 text-blue-300 dark:text-blue-500" />
                        {{ __('churchcouncil::messages.my_profile') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto p-6 space-y-8" data-tour="churchcouncil-area">
        @if(session('success'))
            <div class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" data-tour="churchcouncil-menu">
            <!-- Meetings -->
            <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 border border-gray-200 dark:border-slate-800 hover:border-blue-500/50 transition-all shadow-sm dark:shadow-lg group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="calendar" class="w-6 h-6 text-blue-600 dark:text-blue-500" />
                    </div>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2 py-1 rounded">Agendadas</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $upcomingMeetings->count() }}</span>
                    <span class="text-sm text-gray-500 dark:text-slate-400">Próximas Reuniões</span>
                </div>
                <a href="{{ route('memberpanel.churchcouncil.meetings.index') }}" class="absolute inset-0"></a>
            </div>

            <!-- Agendas -->
            <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 border border-gray-200 dark:border-slate-800 hover:border-emerald-500/50 transition-all shadow-sm dark:shadow-lg group">
                <div class="flex items-center justify-between mb-4">
                     <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="file-lines" class="w-6 h-6 text-emerald-600 dark:text-emerald-500" />
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded">Propostas</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $myAgendas->count() }}</span>
                    <span class="text-sm text-gray-500 dark:text-slate-400">Minhas Pautas</span>
                </div>
                <a href="{{ route('memberpanel.churchcouncil.agendas.index') }}" class="absolute inset-0"></a>
            </div>

            <!-- Approvals -->
            <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 border border-gray-200 dark:border-slate-800 hover:border-amber-500/50 transition-all shadow-sm dark:shadow-lg group">
                <div class="flex items-center justify-between mb-4">
                     <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="clock" class="w-6 h-6 text-amber-600 dark:text-amber-500" />
                    </div>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded">Pendentes</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $pendingApprovals->count() }}</span>
                    <span class="text-sm text-gray-500 dark:text-slate-400">Aprovações</span>
                </div>
                <a href="{{ route('memberpanel.churchcouncil.approvals.pending') }}" class="absolute inset-0"></a>
            </div>

            <!-- Votes -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-gray-200 dark:border-slate-800 hover:border-purple-500/50 transition-all shadow-sm dark:shadow-lg group cursor-default">
                <div class="flex items-center justify-between mb-4">
                     <div class="w-12 h-12 bg-purple-50 dark:bg-purple-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="circle-check" class="w-6 h-6 text-purple-600 dark:text-purple-500" />
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $myVotes->count() }}</span>
                    <span class="text-sm text-gray-500 dark:text-slate-400">Votos Realizados</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upcoming Meetings List -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden flex flex-col transition-colors duration-200">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex justify-between items-center bg-gray-50 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Próximas Reuniões</h3>
                    <a href="{{ route('memberpanel.churchcouncil.meetings.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-500 hover:text-blue-500 dark:hover:text-blue-400 uppercase tracking-widest">Ver Todas</a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-slate-800 flex-1">
                    @forelse($upcomingMeetings as $meeting)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $meeting->title }}</h4>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-slate-400">
                                        <div class="flex items-center gap-1">
                                            <x-icon name="calendar" class="w-3 h-3" />
                                            {{ $meeting->scheduled_date->format('d/m/Y H:i') }}
                                        </div>
                                        @if ($meeting->location)
                                            <span>•</span>
                                            <span>{{ $meeting->location }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                            @if($meeting->status == 'scheduled') text-blue-600 bg-blue-50 border-blue-200 dark:text-blue-400 dark:border-blue-500/30 dark:bg-blue-500/10
                                            @else text-gray-500 bg-gray-100 border-gray-200 dark:text-slate-400 dark:border-slate-600 dark:bg-slate-800 @endif">
                                            {{ $meeting->status_display }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('memberpanel.churchcouncil.meetings.show', $meeting) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-500/20 transition-all">
                                    <x-icon name="chevron-right" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-slate-700">
                                <x-icon name="calendar" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Nenhuma reunião agendada</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Agendas List -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden flex flex-col transition-colors duration-200">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex justify-between items-center bg-gray-50 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pautas Recentes</h3>
                    <a href="{{ route('memberpanel.churchcouncil.agendas.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-500 hover:text-emerald-500 dark:hover:text-emerald-400 uppercase tracking-widest">Ver Todas</a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-slate-800 flex-1">
                    @forelse($recentAgendas as $agenda)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                                        <x-icon name="file-lines" class="w-4 h-4 text-emerald-600 dark:text-emerald-500" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $agenda->title }}</p>
                                    <div class="flex flex-col gap-1 mt-1">
                                        @if($agenda->meeting)
                                            <span class="text-xs font-medium text-gray-500 dark:text-slate-400 truncate">Reunião: {{ $agenda->meeting->title }}</span>
                                        @endif
                                        <span class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-slate-500">{{ $agenda->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                             <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-slate-700">
                                <x-icon name="file-lines" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Nenhuma atividade recente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pl-2 border-l-4 border-blue-500">Acesso Rápido</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('memberpanel.churchcouncil.agendas.create') }}"
                    class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 border border-gray-200 dark:border-slate-800 hover:border-blue-500/50 transition-all shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="plus" class="w-6 h-6 text-blue-600 dark:text-blue-500" />
                    </div>
                    <div>
                         <span class="block text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Criar Pauta</span>
                         <span class="text-xs text-gray-500 dark:text-slate-400">Nova proposta</span>
                    </div>
                </a>

                 <a href="{{ route('memberpanel.churchcouncil.approvals.index') }}"
                    class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 border border-gray-200 dark:border-slate-800 hover:border-green-500/50 transition-all shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-lg bg-green-50 dark:bg-green-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="circle-check" class="w-6 h-6 text-green-600 dark:text-green-500" />
                    </div>
                    <div>
                         <span class="block text-sm font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Aprovações</span>
                         <span class="text-xs text-gray-500 dark:text-slate-400">Ver solicitações</span>
                    </div>
                </a>

                <a href="{{ route('memberpanel.churchcouncil.meetings.index') }}"
                    class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 border border-gray-200 dark:border-slate-800 hover:border-amber-500/50 transition-all shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="calendar" class="w-6 h-6 text-amber-600 dark:text-amber-500" />
                    </div>
                    <div>
                         <span class="block text-sm font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Reuniões</span>
                         <span class="text-xs text-gray-500 dark:text-slate-400">Ver calendário</span>
                    </div>
                </a>

                 <a href="{{ route('memberpanel.churchcouncil.projects.index') }}"
                    class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 border border-gray-200 dark:border-slate-800 hover:border-purple-500/50 transition-all shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                         <x-icon name="lightbulb" class="w-6 h-6 text-purple-600 dark:text-purple-500" />
                    </div>
                    <div>
                         <span class="block text-sm font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Projetos</span>
                         <span class="text-xs text-gray-500 dark:text-slate-400">Meus projetos</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

