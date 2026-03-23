@extends('liderancapanel::components.layouts.master')

@section('title', 'Metas Financeiras')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif

        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('lideranca.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Metas</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Metas estratégicas</h1>
                    <p class="text-slate-300 text-sm max-w-xl">Acompanhamento de objetivos de arrecadação.</p>
                </div>
                @if ($permission->canManageGoals())
                    <a href="{{ route('lideranca.tesouraria.goals.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="plus" class="w-5 h-5" /> Nova meta
                    </a>
                @endif
            </div>
        </div>

        @if ($goals->count() > 0)
            @php
                $totalTarget = $goals->sum('target_amount');
                $totalCurrent = $goals->sum('current_amount');
                $avgProgress = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta global
                    </p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white tabular-nums">R$
                        {{ number_format($totalTarget, 2, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Arrecadado
                    </p>
                    <p class="mt-1 text-xl font-bold text-green-600 dark:text-green-400 tabular-nums">R$
                        {{ number_format($totalCurrent, 2, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Eficiência
                    </p>
                    <p class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                        {{ number_format($avgProgress, 1) }}%</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($goals as $goal)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 flex flex-col">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $goal->name }}</h3>
                        <span
                            class="px-2 py-0.5 rounded-full text-xs font-bold {{ $goal->is_active ? 'bg-green-500/20 text-green-700 dark:text-green-300' : 'bg-gray-500/20 text-gray-600 dark:text-gray-400' }}">
                            {{ $goal->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Arrecadado</span>
                            <span class="font-bold text-green-600 dark:text-green-400">R$
                                {{ number_format($goal->current_amount ?? 0, 2, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full transition-all"
                                style="width: {{ min(100, $goal->progress_percentage ?? 0) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Meta: R$
                            {{ number_format($goal->target_amount, 2, ',', '.') }}</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ $goal->start_date?->format('d/m/Y') }} –
                        {{ $goal->end_date?->format('d/m/Y') }}</p>
                    <div
                        class="mt-auto pt-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-2">
                        @if ($permission->canManageGoals())
                            <a href="{{ route('lideranca.tesouraria.goals.edit', $goal) }}"
                                class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl"
                                title="Editar"><x-icon name="pencil" class="w-4 h-4" /></a>
                        @endif
                        <a href="{{ route('lideranca.tesouraria.goals.show', $goal) }}"
                            class="inline-flex items-center gap-1 px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold hover:bg-amber-600 transition-all">Ver
                            mais</a>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700">
                    <x-icon name="bullseye" class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" />
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhuma meta</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Defina metas estratégicas de arrecadação.</p>
                    @if ($permission->canManageGoals())
                        <a href="{{ route('lideranca.tesouraria.goals.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-all">
                            <x-icon name="plus" class="w-5 h-5" /> Nova meta
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        @if ($goals->hasPages())
            <div class="flex justify-center">{{ $goals->links() }}</div>
        @endif
    </div>
@endsection
