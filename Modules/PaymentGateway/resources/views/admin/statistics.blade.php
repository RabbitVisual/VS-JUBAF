@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Dashboard Financeiro';
@endphp

@section('content')
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard Financeiro</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Análise detalhada de receitas e conversão</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-white dark:bg-gray-800 rounded-lg text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm border border-gray-200 dark:border-gray-700">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-2 animate-pulse"></span>
                    Atualizado agorinha
                </span>
                <a href="{{ route('admin.payment-gateways.index') }}"
                   class="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center">
                    <x-icon name="cog" class="w-5 h-5 mr-2" />
                    Configurar Gateways
                </a>
            </div>
        </div>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <x-icon name="currency-dollar" class="w-24 h-24" />
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Receita Total Confirmada</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        R$ {{ number_format($stats['total_amount'], 2, ',', '.') }}
                    </h3>
                    <div class="mt-4 flex items-center gap-2">
                        @if($stats['growth_percentage'] >= 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <x-icon name="trending-up" class="w-3 h-3 mr-1" />
                                +{{ number_format($stats['growth_percentage'], 1) }}%
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <x-icon name="trending-down" class="w-3 h-3 mr-1" />
                                {{ number_format($stats['growth_percentage'], 1) }}%
                            </span>
                        @endif
                        <span class="text-xs text-gray-500 font-medium">vs mês anterior</span>
                    </div>
                </div>
            </div>

            <!-- Ticket Average -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-purple-200 dark:hover:border-purple-800 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <x-icon name="chart-bar" class="w-24 h-24" />
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Ticket Médio</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        R$ {{ number_format($stats['ticket_average'], 2, ',', '.') }}
                    </h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                        Média por doação
                    </div>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <x-icon name="refresh" class="w-24 h-24" />
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Taxa de Conversão</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        {{ number_format($stats['conversion_rate'], 1) }}%
                    </h3>
                    <div class="mt-4 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['conversion_rate'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 font-medium">{{ $stats['completed_payments'] }} pagos de {{ $stats['total_payments'] }} iniciados</p>
                </div>
            </div>

            <!-- Current Month -->
            <div class="bg-linear-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 shadow-lg shadow-blue-500/20 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <x-icon name="calendar" class="w-24 h-24" />
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-blue-100 uppercase tracking-widest">Receita Este Mês</p>
                    <h3 class="text-3xl font-black mt-2">
                        R$ {{ number_format($stats['current_month_total'], 2, ',', '.') }}
                    </h3>
                    <p class="text-sm text-blue-100 mt-4 font-medium opacity-90">
                        {{ now()->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Charts and Detailed Stats Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Revenue Chart (ApexCharts) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="presentation-chart-line" class="w-5 h-5 text-blue-600" />
                        Receita - Últimos 30 Dias
                    </h3>
                </div>
                <div id="revenueChart" style="min-height: 350px;"></div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="space-y-6">
                <!-- Status Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Status dos Pagamentos</h3>
                    <div class="space-y-4">
                        <!-- Pending -->
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl border border-yellow-100 dark:border-yellow-900/20">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Pendentes</span>
                            </div>
                            <span class="font-black text-yellow-600 dark:text-yellow-400">{{ $stats['pending_payments'] }}</span>
                        </div>
                        <!-- Completed -->
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/10 rounded-xl border border-green-100 dark:border-green-900/20">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Concluídos</span>
                            </div>
                            <span class="font-black text-green-600 dark:text-green-400">{{ $stats['completed_payments'] }}</span>
                        </div>
                        <!-- Failed/Cancelled -->
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-900/20">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Falhos/Cancelados</span>
                            </div>
                            <span class="font-black text-red-600 dark:text-red-400">{{ $stats['failed_payments'] + $stats['cancelled_payments'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Gateway Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Por Gateway</h3>
                    <div class="space-y-4">
                        @forelse($stats['by_gateway'] as $gatewayStat)
                            <div class="group">
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                        {{ $gatewayStat->gateway->display_name ?? 'Desconhecido' }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        R$ {{ number_format($gatewayStat->total_amount, 2, ',', '.') }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                     <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 group-hover:bg-indigo-500"
                                          style="width: {{ $stats['total_amount'] > 0 ? ($gatewayStat->total_amount / $stats['total_amount']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Nenhum dado de gateway.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Type Breakdown Table -->
        @if(isset($stats['by_type']) && $stats['by_type']->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-bold text-gray-900 dark:text-white">Performance por Tipo de Pagamento</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-3 font-bold bg-gray-50 dark:bg-gray-900/20">Tipo</th>
                            <th class="px-6 py-3 font-bold bg-gray-50 dark:bg-gray-900/20">Transações</th>
                            <th class="px-6 py-3 font-bold bg-gray-50 dark:bg-gray-900/20">Volume Total</th>
                            <th class="px-6 py-3 font-bold bg-gray-50 dark:bg-gray-900/20 text-right">Participação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($stats['by_type'] as $typeStat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ ucfirst(str_replace('_', ' ', $typeStat->payment_type ?? 'Outros')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $typeStat->total }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                R$ {{ number_format($typeStat->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span class="text-gray-500 font-medium">
                                    {{ $stats['total_amount'] > 0 ? number_format(($typeStat->total_amount / $stats['total_amount']) * 100, 1) : 0 }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initial Scheme
        const isDark = document.documentElement.classList.contains('dark');

        var options = {
            series: [{
                name: 'Receita',
                data: @json($stats['chart_data'])
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                background: 'transparent'
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#4F46E5']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100],
                    colorStops: [
                        { offset: 0, color: '#4F46E5', opacity: 0.4 },
                        { offset: 100, color: '#4F46E5', opacity: 0 }
                    ]
                }
            },
            xaxis: {
                categories: @json($stats['chart_labels']),
                labels: {
                    style: { colors: '#9CA3AF', fontSize: '12px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
                tooltip: { enabled: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "R$ " + value.toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                    },
                    style: { colors: '#9CA3AF', fontSize: '12px' }
                }
            },
            grid: {
                borderColor: isDark ? '#374151' : '#E5E7EB',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return "R$ " + val.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
        chart.render();

        // Listen for global theme changes
        document.addEventListener('theme-change', function (e) {
            const theme = e.detail.theme;
            const isDark = theme === 'dark';

            chart.updateOptions({
                theme: { mode: theme },
                grid: {
                    borderColor: isDark ? '#374151' : '#E5E7EB'
                },
                tooltip: {
                   theme: theme
                }
            });
        });
    });
</script>
@endpush

