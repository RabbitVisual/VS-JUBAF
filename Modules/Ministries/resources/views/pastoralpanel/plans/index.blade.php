@extends('pastoralpanel::components.layouts.master')

@section('title', 'Planos de Ministérios')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Planos Estratégicos</h1>
                    <p class="text-slate-300 text-sm md:text-base">Visualização dos planos anuais e trimestrais dos ministérios.</p>
                </div>
                <a href="{{ route('pastor.ministerios.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="church" class="w-5 h-5" />
                    Ministérios
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
            <form method="GET" action="{{ route('pastor.ministerios.plans.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
                <div>
                    <label for="ministry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ministério</label>
                    <select name="ministry_id" id="ministry_id" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-amber-500 w-full min-w-[180px]">
                        <option value="">Todos</option>
                        @foreach($ministries as $m)
                            <option value="{{ $m->id }}" {{ request('ministry_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" id="status" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-amber-500 w-full min-w-[160px]">
                        <option value="">Todos</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="under_council_review" {{ request('status') === 'under_council_review' ? 'selected' : '' }}>Em revisão (Conselho)</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="in_execution" {{ request('status') === 'in_execution' ? 'selected' : '' }}>Em execução</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium inline-flex items-center gap-2">
                    <x-icon name="magnifying-glass" class="w-5 h-5" />
                    Filtrar
                </button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Plano / Ministério</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Período</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @php
                            $statusLabels = [
                                'draft' => 'Rascunho',
                                'under_council_review' => 'Em revisão (Conselho)',
                                'approved' => 'Aprovado',
                                'in_execution' => 'Em execução',
                                'archived' => 'Arquivado',
                            ];
                            $statusClass = [
                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'under_council_review' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'in_execution' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'archived' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            ];
                        @endphp
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $plan->title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $plan->ministry->name ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $plan->period_start->format('d/m/Y') }} – {{ $plan->period_end->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $statusClass[$plan->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $statusLabels[$plan->status] ?? $plan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('pastor.ministerios.plans.show', [$plan->ministry, $plan]) }}" class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 hover:underline font-medium text-sm">
                                        <x-icon name="eye" class="w-4 h-4" /> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <x-icon name="clipboard-question" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-slate-600" />
                                    <p class="font-medium">Nenhum plano encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($plans->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 mt-4">
                    {{ $plans->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection
