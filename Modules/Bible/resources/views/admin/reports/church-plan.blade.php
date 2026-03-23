@extends('admin::components.layouts.master')

@section('title', 'Relatório – Plano da Igreja')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Relatório – Plano da Igreja</h1>
                <p class="text-gray-600 dark:text-gray-400">Visão pastoral dos membros inscritos no plano oficial e engajamento na leitura</p>
            </div>
            <a href="{{ route('admin.bible.plans.index') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar aos Planos
            </a>
        </div>

        @if($churchPlans->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                <x-icon name="book-bible" class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
                <p class="text-gray-600 dark:text-gray-400">Nenhum plano marcado como &quot;Plano da Igreja&quot;. Defina um plano como oficial em <a href="{{ route('admin.bible.plans.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Planos de Leitura</a>.</p>
            </div>
        @else
            {{-- Métricas de conclusão --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="chart-pie" class="w-5 h-5" />
                    Métricas de conclusão
                </h2>
                <div class="flex flex-wrap items-baseline gap-4">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $completionPercent }}%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $totalRead }} dias lidos de {{ $totalExpected }} esperados (total da igreja)
                    </div>
                </div>
            </div>

            {{-- Visão geral: membros inscritos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                    <x-icon name="users" class="w-5 h-5" />
                    Membros inscritos no plano oficial
                </h2>
                @if(count($rows) > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Membro</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Plano</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Início</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dias lidos</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Atraso</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($rows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $row->user->name ?? '—' }}</span>
                                            <span class="text-xs text-gray-500">{{ $row->user->email ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $row->plan->title ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $row->subscription->start_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $row->days_read }} / {{ $row->expected_days }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $row->delay }} {{ $row->delay === 1 ? 'dia' : 'dias' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($row->status === 'em_dia')
                                            <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Em dia</span>
                                        @elseif($row->status === 'atraso')
                                            <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Em atraso</span>
                                        @else
                                            <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Crítico</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        @if($row->status === 'critico' && $row->subscription->prayer_request_id)
                                            <a href="{{ route('member.intercessor.room.show', $row->subscription->prayer_request_id) }}" target="_blank" rel="noopener"
                                                class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:underline">
                                                <x-icon name="hands-praying" class="w-4 h-4 mr-1" />
                                                Pedido de oração
                                            </a>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        Nenhum membro inscrito nos planos da igreja no momento.
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
