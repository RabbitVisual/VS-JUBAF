@extends('admin::components.layouts.master')

@section('title', 'Inteligência Familiar e Demografia')

@section('content')
<div class="space-y-8" x-data="familyDemographicsDashboard()">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-900 text-white shadow-xl border border-indigo-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center">
                    <x-icon name="chart-pie" class="w-8 h-8 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Inteligência Familiar e Demografia</h1>
                    <p class="text-indigo-200 mt-1">Visão analítica baseada nos vínculos de parentesco cadastrados.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reports.family-demographics.export.pdf') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-bold transition-colors"
                   onclick="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Gerando PDF...' } }))">
                    <x-icon name="file-pdf" class="w-5 h-5" />
                    Exportar PDF
                </a>
                <a href="{{ route('admin.reports.family-demographics.export.excel') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-bold transition-colors"
                   onclick="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Gerando Excel...' } }))">
                    <x-icon name="file-excel" class="w-5 h-5" />
                    Exportar Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <x-icon name="house-chimney-user" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Núcleos identificados</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $composition['total_nuclei'] ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <x-icon name="people-group" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Membros com vínculos</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalUsersWithRelations ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                <x-icon name="link" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total de vínculos</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalRelationships ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                    <x-icon name="chart-pie" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Distribuição dos tipos de família</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Composição dos núcleos identificados</p>
                </div>
            </div>
            <div class="h-80 flex items-center justify-center">
                <canvas id="chart-family-types" width="400" height="320"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                    <x-icon name="house-chimney-user" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Famílias por bairro/região</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Quando o endereço está cadastrado</p>
                </div>
            </div>
            <div class="h-80 flex items-center justify-center">
                <canvas id="chart-by-neighborhood" width="400" height="320"></canvas>
            </div>
        </div>
    </div>

    <!-- Destaques pastorais -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                    <x-icon name="baby" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Destaques pastorais</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Insights para planejamento e conselho</p>
                </div>
            </div>
            <button type="button"
                    @click="runEliasAnalysis()"
                    :disabled="eliasLoading"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-colors disabled:opacity-50"
                    onclick="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Elias analisando dados...' } }))">
                <x-icon name="robot" class="w-5 h-5" />
                <span x-text="eliasLoading ? 'Analisando...' : 'Análise Pastoral do Elias'"></span>
            </button>
        </div>
        <div class="p-6">
            <ul class="space-y-3">
                @foreach($pastoralHighlights as $highlight)
                    <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                        <x-icon name="circle-check" class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                        <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $highlight }}</span>
                    </li>
                @endforeach
            </ul>
            <div x-show="eliasReply" x-cloak class="mt-6 p-6 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800">
                <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider mb-3">Sugestões do Elias</h3>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-line" x-html="eliasReply"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const composition = @json($composition);
    const byNeighborhood = @json($byNeighborhood);

    // Pie: tipos de família
    const ctxPie = document.getElementById('chart-family-types');
    if (ctxPie && typeof Chart !== 'undefined') {
        const total = composition.total_nuclei || 1;
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Família completa', 'Monoparental', 'Casal', 'Individual'],
                datasets: [{
                    data: [
                        composition.complete_families || 0,
                        composition.monoparental || 0,
                        composition.couples || 0,
                        composition.individuals || 0
                    ],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(156, 163, 175, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Bar: por bairro
    const ctxBar = document.getElementById('chart-by-neighborhood');
    const labels = Object.keys(byNeighborhood);
    const values = Object.values(byNeighborhood);
    if (ctxBar && typeof Chart !== 'undefined') {
        if (labels.length === 0) {
            ctxBar.parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">Nenhum dado por região. Cadastre bairro/cidade nos endereços dos membros.</p>';
        } else {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Famílias',
                        data: values,
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });
        }
    }
});

document.addEventListener('alpine:init', function() {
    Alpine.data('familyDemographicsDashboard', function() {
        return {
            eliasLoading: false,
            eliasReply: '',
            runEliasAnalysis() {
            this.eliasLoading = true;
            this.eliasReply = '';
            window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Elias analisando dados demográficos...' } }));
            fetch('{{ route("admin.reports.family-demographics.elias") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                this.eliasLoading = false;
                window.dispatchEvent(new CustomEvent('loading-overlay:hide'));
                if (data.success && data.reply) {
                    this.eliasReply = data.reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
                } else {
                    this.eliasReply = data.reply || 'Não foi possível obter a análise.';
                }
            })
            .catch(() => {
                this.eliasLoading = false;
                window.dispatchEvent(new CustomEvent('loading-overlay:hide'));
                this.eliasReply = 'Erro ao conectar com o Elias. Tente novamente.';
            });
        }
        };
    });
});
</script>
@endsection
