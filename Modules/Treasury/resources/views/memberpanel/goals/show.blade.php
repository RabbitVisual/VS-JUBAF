@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Meta: ' . $goal->name)

@section('content')
@php
    $gColor = $goal->color ?? 'indigo';
    $gIcon = $goal->icon ?? 'flag-checkered';
@endphp
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-6xl mx-auto space-y-8 px-6 pt-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $goal->name }}</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Detalhes e progresso da meta.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    <a href="{{ route('memberpanel.treasury.goals.index') }}" class="text-sm font-bold text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">Voltar</a>
                    @if ($permission->canManageGoals())
                        <a href="{{ route('memberpanel.treasury.goals.edit', $goal) }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all">
                            <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                            Editar
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Advanced Progress Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="{{ $gIcon }}" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Acompanhamento</h3>
                </div>
                <div class="p-8 md:p-10">
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-{{ $gColor }}-500/10 flex items-center justify-center text-{{ $gColor }}-500">
                                <x-icon name="{{ $gIcon }}" style="duotone" class="w-6 h-6" />
                            </div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-wider">Acompanhamento de Realização</h3>
                        </div>
                        <div class="text-right">
                             <div class="text-4xl font-black text-{{ $gColor }}-600 dark:text-{{ $gColor }}-400 tabular-nums">
                                {{ number_format($goal->progress_percentage, 1) }}<span class="text-xl ml-0.5">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="relative pt-1">
                            <div class="overflow-hidden h-6 text-xs flex rounded-full bg-slate-100 dark:bg-slate-800 shadow-inner border border-slate-200/50 dark:border-slate-700/50 p-1">
                                <div style="width: {{ min(100, $goal->progress_percentage) }}%"
                                     class="shadow-lg flex flex-col text-center whitespace-nowrap text-white justify-center transition-all duration-[2000ms] ease-out rounded-full relative {{ $goal->progress_percentage >= 100 ? 'bg-linear-to-r from-emerald-500 to-teal-500' : 'bg-linear-to-r from-'.$gColor.'-600 via-purple-500 to-'.$gColor.'-600' }} bg-[length:200%_100%]">
                                     <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-slate-50 dark:bg-slate-800/40 rounded-4xl p-8 border border-slate-100 dark:border-slate-800">
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Arrecadação Efetiva</span>
                                <div class="text-3xl font-black text-slate-900 dark:text-white flex items-baseline gap-1 tabular-nums">
                                    <span class="text-sm font-bold text-slate-400">R$</span>
                                    {{ number_format($goal->current_amount, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="space-y-1 text-right md:border-l border-slate-200 dark:border-slate-700 md:pl-8">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest pr-1">Alvo Estratégico</span>
                                <div class="text-2xl font-black text-slate-500 dark:text-slate-400 flex items-baseline justify-end gap-1 tabular-nums">
                                    <span class="text-xs font-bold text-slate-400">R$</span>
                                    {{ number_format($goal->target_amount, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        @if ($goal->isAchieved())
                            <div class="p-6 bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/50 rounded-4xl flex items-center gap-5">
                                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-inner">
                                    <x-icon name="trophy" style="duotone" class="w-7 h-7" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-emerald-900 dark:text-emerald-300 uppercase tracking-tight">Vitoria Confirmada!</h4>
                                    <p class="text-sm font-medium text-emerald-700/80 dark:text-emerald-400/80">O Reino celebrou a conquista deste objetivo financeiro.</p>
                                </div>
                            </div>
                        @else
                            <div class="p-6 bg-slate-50 dark:bg-slate-800/20 rounded-3xl border border-slate-100 dark:border-slate-800 text-center">
                                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                    Distância para o alvo: <span class="text-slate-900 dark:text-white font-black tabular-nums scale-110 px-2 inline-block">R$ {{ number_format(max(0, $goal->target_amount - $goal->current_amount), 2, ',', '.') }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                </div>
            </div>

            @if ($goal->description)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="align-left" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Descrição</h3>
                </div>
                <div class="p-8">
                    <p class="text-gray-600 dark:text-slate-400 leading-relaxed">{{ $goal->description }}</p>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="history" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Histórico de aportes</h3>
                </div>

                @if($goal->financialEntries->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-slate-800/30 border-b border-gray-100 dark:border-slate-800">
                                    <th class="py-4 px-8 text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Data</th>
                                    <th class="py-4 px-8 text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Origem</th>
                                    <th class="py-4 px-8 text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($goal->financialEntries as $entry)
                                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all cursor-default">
                                        <td class="py-5 px-8">
                                            <div class="flex items-center text-sm font-bold text-slate-500 dark:text-slate-400">
                                                <x-icon name="calendar-day" style="duotone" class="w-3.5 h-3.5 mr-2 text-slate-300" />
                                                {{ $entry->entry_date->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="py-5 px-8">
                                            <div class="space-y-0.5">
                                                <p class="text-sm font-black text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $entry->title }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight flex items-center">
                                                    <x-icon name="user" style="duotone" class="w-2.5 h-2.5 mr-1" />
                                                    {{ $entry->user->name ?? 'Sistema' }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="py-5 px-8 text-right">
                                            <div class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">
                                                + R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-20">
                        <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <x-icon name="empty-set" style="duotone" class="w-10 h-10 text-slate-200" />
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-widest text-xs">Nenhum aporte registrado</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded-xl">
                        <x-icon name="circle-info" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Parâmetros</h3>
                </div>
                <div class="p-8">

                <div class="space-y-8">
                    <div class="space-y-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Status de Ciclo</span>
                        <div>
                             @if ($goal->isAchieved())
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 uppercase tracking-widest border border-emerald-200 dark:border-emerald-800">
                                    <x-icon name="check-double" style="duotone" class="w-3 h-3 mr-2" />
                                    Concluída
                                </span>
                            @elseif($goal->is_active)
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 uppercase tracking-widest border border-indigo-200 dark:border-indigo-800">
                                    <x-icon name="bolt" style="duotone" class="w-3 h-3 mr-2" />
                                    Ativa
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                                    <x-icon name="ban" style="duotone" class="w-3 h-3 mr-2" />
                                    Inativa
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Categoria Vinculada</span>
                        <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 font-black text-xs text-slate-900 dark:text-slate-200 uppercase tracking-tighter">
                            <x-icon name="tag" style="duotone" class="w-4 h-4 text-{{ $gColor }}-500" />
                            {{ str_replace('_', ' ', $goal->category ?? 'Patrimônio Geral') }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Janela de Realização</span>
                        <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700">
                            <x-icon name="calendar-range" style="duotone" class="w-4 h-4 text-{{ $gColor }}-500" />
                            <div class="text-[10px] font-black text-slate-900 dark:text-slate-200 tabular-nums">
                                {{ $goal->start_date->format('d/m/Y') }} <span class="text-slate-300 mx-1">à</span> {{ $goal->end_date->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    @if ($goal->campaign)
                        <div class="space-y-3">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Origem de Campanha</span>
                            <a href="{{ route('memberpanel.treasury.campaigns.show', $goal->campaign) }}"
                                class="group flex items-center justify-between p-4 bg-linear-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/20 dark:to-purple-950/20 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 transition-all hover:border-indigo-400">
                                <div class="flex items-center gap-3">
                                    <x-icon name="bullhorn" style="duotone" class="w-4 h-4 text-indigo-600" />
                                    <span class="text-xs font-black text-indigo-900 dark:text-indigo-300 uppercase tracking-tighter">{{ $goal->campaign->name }}</span>
                                </div>
                                <x-icon name="arrow-up-right" style="duotone" class="w-3 h-3 text-indigo-400 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
                            </a>
                        </div>
                    @endif
                </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden p-8">
                <h4 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Auditoria</h4>
                <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                    Objetivo monitorado pelo conselho fiscal. Aportes vinculados por protocolos únicos para transparência.
                </p>
            </div>
        </div>
        </div>
    </div>

<style>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>
@endsection
