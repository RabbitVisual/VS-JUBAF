@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Metas')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8" data-tour="treasury-area">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Metas financeiras</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Acompanhe o progresso dos objetivos e planejamento estratégico.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    @if ($permission->canManageGoals())
                        <a href="{{ route('memberpanel.treasury.goals.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-500/20 shrink-0">
                            <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" />
                            Nova meta
                        </a>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 mb-4">
                        <x-icon name="bullseye-arrow" style="duotone" class="w-3 h-3 text-indigo-600 dark:text-indigo-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Planejamento</span>
                    </div>
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-lg">
                        Acompanhe em tempo real o progresso dos objetivos financeiros definidos para a expansão.
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="list" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Todas as metas</h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($goals as $goal)
                            @php
                                $gColor = $goal->color ?? 'indigo';
                                $gIcon = $goal->icon ?? 'flag-checkered';
                            @endphp
                            <div class="group bg-gray-50/50 dark:bg-slate-800/30 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 flex flex-col transition-all hover:shadow-md">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                            <x-icon name="{{ $gIcon }}" style="duotone" class="w-5 h-5" />
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $goal->name }}</h3>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase
                                        {{ $goal->isAchieved() ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' :
                                           ($goal->is_active ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' :
                                           'bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400') }}">
                                        @if ($goal->isAchieved()) Concluída
                                        @elseif($goal->is_active) Ativa
                                        @else Inativa
                                        @endif
                                    </span>
                                </div>
                                @if ($goal->description)
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-4 line-clamp-2">{{ Str::limit($goal->description, 80) }}</p>
                                @endif
                                <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 mb-4 border border-gray-100 dark:border-slate-700">
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Arrecadado</span>
                                        <span class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                        <span>{{ number_format($goal->progress_percentage, 1) }}%</span>
                                        <span>Alvo R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $goal->progress_percentage >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                        {{ $goal->start_date ? $goal->start_date->format('d/m/Y') : '-' }} • {{ $goal->end_date ? $goal->end_date->format('d/m/Y') : '-' }}
                                    </span>
                                    <div class="flex items-center gap-2 ml-auto">
                                        @if ($permission->canManageGoals())
                                            <a href="{{ route('memberpanel.treasury.goals.edit', $goal) }}" class="p-2 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" title="Editar">
                                                <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('memberpanel.treasury.goals.show', $goal) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-indigo-600 hover:bg-gray-800 dark:hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all">
                                            Ver detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col items-center justify-center py-16 px-8 bg-gray-50 dark:bg-slate-800/30 rounded-2xl border border-gray-100 dark:border-slate-800">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mb-6">
                                    <x-icon name="bullseye" style="duotone" class="w-10 h-10 text-gray-400 dark:text-slate-500" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma meta</h3>
                                <p class="text-gray-500 dark:text-slate-400 text-center max-w-sm text-sm mb-8">Defina objetivos financeiros para acompanhar o progresso.</p>
                                @if ($permission->canManageGoals())
                                    <a href="{{ route('memberpanel.treasury.goals.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all">
                                        <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" />
                                        Nova meta
                                    </a>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    @if ($goals->hasPages())
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                            {{ $goals->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
