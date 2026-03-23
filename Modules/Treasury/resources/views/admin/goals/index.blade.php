@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Tesouraria</span>
                            <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Metas</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Metas estratégicas</h1>
                        <p class="text-gray-300 max-w-xl">Acompanhamento de objetivos e marcos de arrecadação.</p>
                    </div>
                    @if ($permission->canManageGoals())
                        <a href="{{ route('treasury.goals.create') }}"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all">
                            <x-icon name="plus" style="duotone" class="w-5 h-5 text-blue-600 mr-2" />
                            Nova meta
                        </a>
                    @endif
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Metas' => null]])
            </div>
        </div>

        @if($goals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $totalTarget = $goals->sum('target_amount');
                $totalCurrent = $goals->sum('current_amount');
                $avgProgress = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;
            @endphp
            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-500/20">
                        <x-icon name="money-bill-trend-up" style="duotone" class="w-7 h-7" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta global</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($totalTarget, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        <x-icon name="vault" style="duotone" class="w-7 h-7" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total arrecadado</p>
                        <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">R$ {{ number_format($totalCurrent, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        <x-icon name="chart-pie-simple" style="duotone" class="w-7 h-7" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Eficiência</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums">{{ number_format($avgProgress, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($goals->count() > 0)
            <div class="flex items-center gap-4">
                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Catálogo de objetivos</h3>
                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($goals as $goal)
                @php
                    $gColor = $goal->color ?? 'blue';
                    $gIcon = $goal->icon ?? 'bullseye-arrow';
                @endphp
                <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col transition-all hover:shadow-lg relative">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                        <!-- Card Header/Icon Area -->
                        <div class="p-8 pb-0">
                            <div class="flex items-start justify-between">
                                <div class="w-14 h-14 rounded-3xl bg-{{ $gColor }}-500/10 flex items-center justify-center text-{{ $gColor }}-600 border border-{{ $gColor }}-500/20 group-hover:scale-110 transition-transform">
                                    <x-icon name="{{ $gIcon }}" style="duotone" class="w-7 h-7" />
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $goal->isAchieved() ? 'bg-emerald-500/10 text-emerald-600' : ($goal->is_active ? 'bg-'.$gColor.'-500/10 text-'.$gColor.'-600' : 'bg-slate-500/10 text-slate-600') }}">
                                        {{ $goal->isAchieved() ? 'Concluída' : ($goal->is_active ? 'Ativa' : 'Pausada') }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-700/50 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-600">
                                        {{ str_replace('_', ' ', $goal->type) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 flex-1 flex flex-col space-y-6">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white group-hover:text-{{ $gColor }}-600 transition-colors line-clamp-1 uppercase tracking-tighter">
                                    {{ $goal->name }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mt-2 font-medium">
                                    {{ $goal->description ?? 'Sem descrição estratégica definida para este objetivo.' }}
                                </p>
                            </div>

                            <!-- Progress Stats -->
                            <div class="space-y-4">
                                <div class="flex items-end justify-between">
                                    <div class="space-y-0.5">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Acumulado</p>
                                        <p class="text-lg font-black {{ $goal->isAchieved() ? 'text-emerald-600' : 'text-'.$gColor.'-600' }} tabular-nums">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alvo</p>
                                        <p class="text-sm font-black text-slate-900 dark:text-white tabular-nums">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200 dark:border-slate-700">
                                    <div class="bg-linear-to-r from-{{ $gColor }}-500 via-{{ $gColor }}-400 to-{{ $gColor }}-600 h-full rounded-full transition-all duration-1000 shadow-lg"
                                         style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ number_format($goal->progress_percentage, 1) }}% COMPLETO</span>
                                    @php $remaining = max(0, $goal->target_amount - $goal->current_amount); @endphp
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">FALTA R$ {{ number_format($remaining, 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between mt-auto">
                                <div class="flex items-center gap-2">
                                    <x-icon name="calendar-days" style="duotone" class="w-4 h-4 text-gray-400" />
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Até {{ $goal->end_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($permission->canManageGoals())
                                        <a href="{{ route('treasury.goals.edit', $goal) }}" class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 rounded-lg transition-colors" title="Editar">
                                            <x-icon name="pencil" style="duotone" class="w-4 h-4" />
                                        </a>
                                    @endif
                                    <a href="{{ route('treasury.goals.show', $goal) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-xl text-xs font-bold hover:bg-gray-800 dark:hover:bg-gray-600 transition-all">
                                        Ver análise
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20 px-8 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-6">
                            <x-icon name="bullseye-arrow" style="duotone" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma meta</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm text-sm mb-8">Estabeleça metas e acompanhe o progresso em tempo real.</p>
                        @if ($permission->canManageGoals())
                            <a href="{{ route('treasury.goals.create') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 dark:bg-blue-600 text-white rounded-xl font-bold hover:bg-gray-800 dark:hover:bg-blue-700 transition-all">
                                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" />
                                Nova meta
                            </a>
                        @endif
                    </div>
                @endforelse
        </div>

        @if ($goals->hasPages())
            <div class="flex justify-center">
                {{ $goals->links() }}
            </div>
        @endif
    </div>
@endsection
