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
                            <span class="px-3 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Campanha</span>
                            <span class="px-3 py-1.5 rounded-full {{ $campaign->isActive() ? 'bg-green-500/20 border-green-400/30 text-green-300' : 'bg-gray-500/20 border-gray-400/30 text-gray-300' }} border text-xs font-bold uppercase tracking-wider">
                                {{ $campaign->isActive() ? 'Ativa' : 'Encerrada' }}
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $campaign->name }}</h1>
                        <p class="text-gray-300 max-w-xl">{{ $campaign->isActive() ? 'Campanha ativa e recebendo doações.' : 'Campanha encerrada.' }}</p>
                    </div>
                    <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                        <a href="{{ route('treasury.campaigns.index') }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                            <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" /> Voltar
                        </a>
                        @if (isset($permission) && $permission->canManageCampaigns())
                            <a href="{{ route('treasury.campaigns.edit', $campaign) }}"
                                class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all">
                                <x-icon name="pencil" style="duotone" class="w-5 h-5 mr-2 text-blue-600" /> Editar
                            </a>
                        @endif
                    </div>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Campanhas' => route('treasury.campaigns.index'), $campaign->name => null]])
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna principal -->
            <div class="lg:col-span-2 space-y-6">
                @if ($campaign->target_amount)
                <!-- Card progresso -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Arrecadado</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta</p>
                                <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400 tabular-nums">R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Percentual</p>
                                <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">{{ number_format($campaign->progress_percentage, 1) }}%</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="w-full h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                            </div>
                            <p class="text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status da arrecadação</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Card descrição -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <x-icon name="align-left" style="duotone" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Propósito e impacto</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Detalhes do projeto ministerial</p>
                        </div>
                    </div>
                    @if ($campaign->description)
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $campaign->description }}</p>
                    @else
                        <div class="py-8 text-center">
                            <x-icon name="circle-info" style="duotone" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                            <p class="text-gray-400 dark:text-gray-500 text-sm italic">Nenhum detalhe adicional fornecido para esta campanha.</p>
                        </div>
                    @endif
                </div>

                <!-- Histórico de aportes -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-2">
                        <x-icon name="list-timeline" style="duotone" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Histórico de aportes</h3>
                    </div>
                    @if ($campaign->financialEntries->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($campaign->financialEntries as $entry)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ $entry->entry_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $entry->title }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Entrada confirmada</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="text-lg font-bold text-green-600 dark:text-green-400 tabular-nums">
                                                    + R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-icon name="inbox" style="duotone" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                            </div>
                            <h4 class="text-gray-900 dark:text-white font-bold mb-1">Sem registros de doação</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm max-w-xs">Esta campanha ainda não recebeu aportes financeiros registrados.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Metadados e cronograma -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                    <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Metadados e cronograma</h4>
                    <div class="relative space-y-6">
                        @if ($campaign->start_date)
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                    <x-icon name="calendar-days" style="duotone" class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Início</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $campaign->start_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($campaign->end_date)
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-blue-500 dark:text-blue-400">
                                    <x-icon name="calendar-check" style="duotone" class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prazo final</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $campaign->end_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400">
                                    <x-icon name="infinity" style="duotone" class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Regime</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Contínuo / Sem fim</p>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <x-icon name="clock" style="duotone" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Última entrada</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $campaign->financialEntries->first() ? $campaign->financialEntries->first()->entry_date->format('d/m/Y') : 'Pendente' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($campaign->image)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-4">
                    <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover">
                    </div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-3">Capa oficial</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $campaign->name }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
