@extends('pastoralpanel::components.layouts.master')

@section('title', $plan->title . ' - ' . $ministry->name)

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ministerios.index') }}" class="hover:text-white">Ministérios</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <a href="{{ route('pastor.ministerios.show', $ministry) }}" class="hover:text-white">{{ $ministry->name }}</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $plan->title }}</span>
                    </nav>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">
                        {{ $plan->period_start->format('d/m/Y') }} – {{ $plan->period_end->format('d/m/Y') }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">{{ $plan->title }}</h1>
                    <p class="text-slate-300 text-sm">Ministério: {{ $ministry->name }}</p>
                </div>
                <a href="{{ route('pastor.ministerios.plans.index') }}?ministry_id={{ $ministry->id }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar aos planos
                </a>
            </div>
        </div>

        @php
            $statusLabels = [
                'draft' => 'Rascunho',
                'under_council_review' => 'Em revisão (Conselho)',
                'approved' => 'Aprovado',
                'in_execution' => 'Em execução',
                'archived' => 'Arquivado',
            ];
        @endphp

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 md:p-8">
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->title }}</h2>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    @if($plan->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                    @elseif($plan->status === 'under_council_review') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                    @elseif(in_array($plan->status, ['approved','in_execution'])) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                    {{ $statusLabels[$plan->status] ?? $plan->status }}
                </span>
            </div>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ministério</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $ministry->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Período</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $plan->period_start->format('d/m/Y') }} – {{ $plan->period_end->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ ucfirst($plan->period_type) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Orçamento solicitado</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $plan->budget_requested ? 'R$ ' . number_format((float)$plan->budget_requested, 2, ',', '.') : 'Não informado' }}</dd>
                </div>
                @if($plan->approved_at)
                    <div>
                        <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aprovado em</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $plan->approved_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
            @if($plan->objectives)
                <div class="mt-6">
                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Objetivos</dt>
                    <dd class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $plan->objectives }}</dd>
                </div>
            @endif
            @if($plan->budget_notes)
                <div class="mt-4">
                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Observações sobre orçamento</dt>
                    <dd class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $plan->budget_notes }}</dd>
                </div>
            @endif
            @if($plan->status === 'under_council_review' && $plan->councilApproval)
                <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                        Aguardando análise do conselho.
                        <a href="{{ route('pastor.conselho.approvals.show', $plan->councilApproval) }}" class="underline font-medium">Ver solicitação</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
