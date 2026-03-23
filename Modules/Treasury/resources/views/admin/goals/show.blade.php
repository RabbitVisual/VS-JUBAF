@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full -translate-x-1/2 translate-y-1/2"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span class="px-3 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Meta</span>
                            <span class="px-3 py-1.5 rounded-full {{ $goal->is_active ? 'bg-green-500/20 border-green-400/30 text-green-300' : 'bg-gray-500/20 border-gray-400/30 text-gray-300' }} border text-xs font-bold uppercase tracking-wider">
                                {{ $goal->is_active ? 'Ativa' : 'Pausada' }}
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $goal->name }}</h1>
                        <p class="text-gray-300 max-w-xl">{{ $goal->is_active ? 'Meta ativa e monitorada.' : 'Meta pausada.' }}</p>
                    </div>
                    <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                        <a href="{{ route('treasury.goals.index') }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                            <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" /> Voltar
                        </a>
                        @if (isset($permission) && $permission->canManageGoals())
                            <a href="{{ route('treasury.goals.edit', $goal) }}"
                                class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all">
                                <x-icon name="pencil" style="duotone" class="w-5 h-5 mr-2 text-blue-600" /> Editar
                            </a>
                        @endif
                    </div>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Metas' => route('treasury.goals.index'), $goal->name => null]])
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Progresso -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total acumulado</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta</p>
                                <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400 tabular-nums">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Percentual</p>
                                <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">{{ number_format($goal->progress_percentage, 1) }}%</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="w-full h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                            </div>
                            <p class="text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status da mobilização</p>
                        </div>
                    </div>
                </div>

                <!-- Descrição -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <x-icon name="align-left" style="duotone" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Propósito e descrição</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contexto do planejamento financeiro</p>
                        </div>
                    </div>
                    @if ($goal->description)
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed relative">{{ $goal->description }}</p>
                    @else
                        <div class="py-8 text-center relative">
                            <x-icon name="circle-info" style="duotone" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                            <p class="text-gray-400 dark:text-gray-500 text-sm italic">Nenhum detalhe adicional fornecido para esta meta.</p>
                        </div>
                    @endif
                </div>

                <!-- Histórico de aportes -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="list-timeline" style="duotone" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Histórico de aportes</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-bold">{{ $goal->financialEntries->count() }} registros</span>
                    </div>
                    <div class="overflow-x-auto relative">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título / Origem</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($goal->financialEntries as $entry)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $entry->entry_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $entry->title }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Registrado por {{ $entry->user->name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($entry->amount, 2, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-16 text-center">
                                            <x-icon name="folder-open" style="duotone" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                                            <p class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">Nenhum aporte direto registrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                    <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-6 pb-3 border-b border-gray-200 dark:border-gray-700 relative">Parâmetros</h4>
                    <div class="relative space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $goal->type) }}</p>
                            </div>
                            <x-icon name="tag" style="duotone" class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prazo final</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $goal->end_date->format('d/m/Y') }}</p>
                            </div>
                            <x-icon name="calendar-clock" style="duotone" class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                        </div>
                        @if ($goal->campaign)
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Campanha</p>
                                <a href="{{ route('treasury.campaigns.show', $goal->campaign) }}" class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $goal->campaign->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ver detalhes</p>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if (isset($permission) && $permission->canManageGoals())
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-6 text-white shadow-lg border border-gray-700 relative overflow-hidden">
                    <x-icon name="lightbulb" style="duotone" class="absolute -right-4 -top-4 w-24 h-24 text-white/10" />
                    <div class="relative space-y-4">
                        <h4 class="text-lg font-bold">Sugestão de ação</h4>
                        <p class="text-sm text-gray-400">Revise os aportes e mobilize os ministérios envolvidos.</p>
                        <a href="{{ route('treasury.goals.edit', $goal) }}" class="inline-flex items-center justify-center w-full py-3 bg-white text-gray-900 rounded-xl font-bold text-sm hover:bg-gray-100 transition-all">
                            Recalibrar parâmetros
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
