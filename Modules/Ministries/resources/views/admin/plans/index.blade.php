@extends('admin::components.layouts.master')

@section('title', 'Planos de Ministérios')

@section('content')
    <div class="space-y-8">
        <!-- Hero Header (padrão configuração) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Ministérios</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Planos</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Planos Estratégicos</h1>
                    <p class="text-gray-300 max-w-xl">Planos anuais/trimestrais dos ministérios e status no conselho.</p>
                </div>
                <a href="{{ route('admin.ministries.index') }}"
                    class="flex-shrink-0 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 inline-flex items-center gap-2">
                    <x-icon name="church" class="w-5 h-5 text-blue-600" />
                    Ministérios
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <form method="GET" class="flex flex-wrap gap-4 items-end mb-6">
                    <div>
                        <label for="ministry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ministério</label>
                        <select name="ministry_id" id="ministry_id" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($ministries as $m)
                        <option value="{{ $m->id }}" {{ request('ministry_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" id="status" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="under_council_review" {{ request('status') === 'under_council_review' ? 'selected' : '' }}>Em revisão (Conselho)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                    <option value="in_execution" {{ request('status') === 'in_execution' ? 'selected' : '' }}>Em execução</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
                </select>
            </div>
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 flex items-center gap-2">
                        <x-icon name="magnifying-glass" class="w-5 h-5" />
                        Filtrar
                    </button>
                </form>

                <div class="overflow-x-auto -mx-8">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Plano / Ministério</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Período</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $plan->title }}</div>
                                <div class="text-sm text-gray-500">{{ $plan->ministry->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $plan->period_start->format('d/m/Y') }} – {{ $plan->period_end->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusLabels = [
                                        'draft' => 'Rascunho',
                                        'under_council_review' => 'Em revisão (Conselho)',
                                        'approved' => 'Aprovado',
                                        'in_execution' => 'Em execução',
                                        'archived' => 'Arquivado',
                                    ];
                                    $statusClass = match($plan->status) {
                                        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'under_council_review' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'approved', 'in_execution' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 rounded text-xs font-medium {{ $statusClass }}">{{ $statusLabels[$plan->status] ?? $plan->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.ministries.plans.show', [$plan->ministry, $plan]) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-bold text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                    <x-icon name="eye" class="w-4 h-4 mr-1" /> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-500 dark:text-gray-400">
                                    <x-icon name="clipboard-question" class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" />
                                    <p class="font-medium">Nenhum plano encontrado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
                @if($plans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20 mt-4">{{ $plans->appends(request()->query())->links('pagination::tailwind') }}</div>
                @endif
                </div>
            </div>
        </div>
    </div>
@endsection
