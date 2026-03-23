@extends('liderancapanel::components.layouts.master')

@section('title', $goal->name)

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
                        <a href="{{ route('pastor.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <a href="{{ route('pastor.tesouraria.goals.index') }}"
                            class="hover:text-white transition-colors">Metas</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $goal->name }}</span>
                    </nav>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full {{ $goal->is_active ? 'bg-green-500/20 text-green-300' : 'bg-gray-500/20 text-gray-300' }} text-xs font-bold uppercase tracking-wider mb-2">
                        {{ $goal->is_active ? 'Ativa' : 'Inativa' }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">{{ $goal->name }}</h1>
                    <p class="text-slate-300 text-sm max-w-xl">
                        {{ $goal->description ? \Str::limit($goal->description, 120) : 'Meta estratégica.' }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('pastor.tesouraria.goals.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                    </a>
                    @if (isset($permission) && $permission->canManageGoals())
                        <a href="{{ route('pastor.tesouraria.goals.edit', $goal) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                            <x-icon name="pencil" class="w-5 h-5" /> Editar
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Arrecadado</p>
                            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white tabular-nums">R$
                                {{ number_format($goal->current_amount ?? 0, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta
                            </p>
                            <p class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">R$
                                {{ number_format($goal->target_amount, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Progresso</p>
                            <p class="mt-1 text-xl font-bold text-green-600 dark:text-green-400 tabular-nums">
                                {{ number_format($goal->progress_percentage ?? 0, 1) }}%</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all"
                            style="width: {{ min(100, $goal->progress_percentage ?? 0) }}%"></div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Propósito</h3>
                    @if ($goal->description)
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $goal->description }}</p>
                    @else
                        <p class="text-gray-400 dark:text-gray-500 text-sm italic">Sem descrição.</p>
                    @endif
                </div>

                @if ($goal->financialEntries && $goal->financialEntries->count() > 0)
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Lançamentos
                                vinculados</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                    @foreach ($goal->financialEntries->take(20) as $entry)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $entry->entry_date->format('d/m/Y') }}</td>
                                            <td class="px-5 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $entry->title }}</td>
                                            <td
                                                class="px-5 py-3 text-right text-sm font-bold text-green-600 dark:text-green-400">
                                                + R$ {{ number_format($entry->amount, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                    <h4
                        class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200 dark:border-slate-700">
                        Período</h4>
                    <div class="space-y-3">
                        <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Início:</span> <span
                                class="font-bold text-gray-900 dark:text-white">{{ $goal->start_date?->format('d/m/Y') ?? '-' }}</span>
                        </p>
                        <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Término:</span> <span
                                class="font-bold text-gray-900 dark:text-white">{{ $goal->end_date?->format('d/m/Y') ?? '-' }}</span>
                        </p>
                        <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Tipo:</span> <span
                                class="font-bold text-gray-900 dark:text-white capitalize">{{ $goal->type ?? '-' }}</span>
                        </p>
                        @if ($goal->campaign)
                            <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Campanha:</span> <a
                                    href="{{ route('pastor.tesouraria.campaigns.show', $goal->campaign) }}"
                                    class="font-bold text-amber-600 dark:text-amber-400 hover:underline">{{ $goal->campaign->name }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
