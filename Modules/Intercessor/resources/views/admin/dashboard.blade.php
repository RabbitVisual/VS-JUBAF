@extends('admin::components.layouts.master')

@section('title', 'Intercessor Dashboard')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Intercessor Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Visão geral do ministério de intercessão e métricas de atividade.</p>
        </div>
        <a href="{{ route('admin.intercessor.settings.index') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
            <x-icon name="cog" class="w-5 h-5 mr-2" />
            <span>Configurações</span>
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pedidos</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            </div>
            <div class="p-3 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                <x-icon name="view-grid" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ativos</p>
                <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
            </div>
            <div class="p-3 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                <x-icon name="check-circle" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Urgentes</p>
                <p class="mt-1 text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['urgent'] }}</p>
            </div>
            <div class="p-3 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                <x-icon name="exclamation-circle" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Interações</p>
                <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['interactions'] }}</p>
            </div>
            <div class="p-3 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400">
                <x-icon name="users" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Moderação</p>
                <p class="mt-1 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
            </div>
            <div class="p-3 rounded-full bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                <x-icon name="clock" class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Activity Chart Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Atividade de Intercessão</h3>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Últimos 30 dias</span>
        </div>
        <div id="activityChart" class="w-full" style="min-height: 350px;"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof ApexCharts === 'undefined') {
             console.error('ApexCharts not found.');
             return;
        }

        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#9ca3af' : '#4b5563';
        const gridColor = isDarkMode ? '#374151' : '#e5e7eb';

        var options = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 800 },
                fontFamily: 'Inter, sans-serif'
            },
            series: [{
                name: 'Compromissos',
                data: @json($chartData)
            }],
            xaxis: {
                categories: @json($chartLabels),
                labels: { style: { colors: textColor, fontSize: '10px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: textColor, fontSize: '10px', fontWeight: 600 } }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4,
                padding: { top: 10, right: 10, bottom: 0, left: 10 }
            },
            theme: { mode: isDarkMode ? 'dark' : 'light' },
            colors: ['#3b82f6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, lineCap: 'round' },
            markers: { size: 0, hover: { size: 5 } }
        };

        var chart = new ApexCharts(document.querySelector("#activityChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection

