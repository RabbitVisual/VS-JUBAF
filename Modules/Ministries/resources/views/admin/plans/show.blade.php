@extends('admin::components.layouts.master')

@section('title', $plan->title . ' - ' . $ministry->name)

@section('content')
    <div class="space-y-8">
        @if(session('success'))
            <div class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('error') }}
            </div>
        @endif

        <!-- Hero Header (padrão configuração) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-gray-400 font-medium mb-2">
                        <a href="{{ route('admin.ministries.index') }}" class="hover:text-white">Ministérios</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <a href="{{ route('admin.ministries.show', $ministry) }}" class="hover:text-white">{{ $ministry->name }}</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $plan->title }}</span>
                    </nav>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Plano</span>
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">{{ $plan->period_start->format('d/m/Y') }} – {{ $plan->period_end->format('d/m/Y') }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $plan->title }}</h1>
                    <p class="text-gray-300 max-w-xl">Ministério: {{ $ministry->name }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    @if($plan->status === 'draft')
                        <a href="{{ route('admin.ministries.plans.edit', [$ministry, $plan]) }}" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                            <x-icon name="pencil" class="w-5 h-5" /> Editar
                        </a>
                        <form action="{{ route('admin.ministries.plans.submit-to-council', [$ministry, $plan]) }}" method="POST" class="inline" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Enviando...' } })); return confirm('Enviar este plano ao conselho para revisão?');">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-sm inline-flex items-center gap-2">
                                <x-icon name="paper-plane" class="w-5 h-5" /> Enviar ao conselho
                            </button>
                        </form>
                        <form action="{{ route('admin.ministries.plans.destroy', [$ministry, $plan]) }}" method="POST" class="inline" onsubmit="if(confirm('Excluir este plano?')) { window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Excluindo...' } })); return true; } return false;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 text-red-300 hover:bg-red-500/20 rounded-xl font-bold border border-red-400/30">Excluir</button>
                        </form>
                    @endif
                    <a href="{{ route('admin.ministries.show', $ministry) }}" class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 inline-flex items-center gap-2">
                        <x-icon name="arrow-left" class="w-5 h-5 text-blue-600" /> Voltar
                    </a>
                </div>
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

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
            <div class="relative">
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $plan->title }}</h2>
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
                    <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ministério</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $ministry->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider">Período</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $plan->period_start->format('d/m/Y') }} – {{ $plan->period_end->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ ucfirst($plan->period_type) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider">Orçamento solicitado</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $plan->budget_requested ? 'R$ ' . number_format((float)$plan->budget_requested, 2, ',', '.') : 'Não informado' }}</dd>
                </div>
                @if($plan->approved_at)
                    <div>
                        <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider">Aprovado em</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $plan->approved_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
            @if($plan->objectives)
                <div class="mt-6">
                    <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Objetivos</dt>
                    <dd class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $plan->objectives }}</dd>
                </div>
            @endif
            @if($plan->budget_notes)
                <div class="mt-4">
                    <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Observações sobre orçamento</dt>
                    <dd class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $plan->budget_notes }}</dd>
                </div>
            @endif
            @if($plan->status === 'under_council_review' && $plan->councilApproval)
                <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Aguardando análise do conselho. <a href="{{ url('/admin/conselho/aprovacoes/'.$plan->councilApproval->id) }}" class="underline">Ver solicitação</a></p>
                </div>
            @endif

            @php $plannedActivities = $plan->plannedActivities(); @endphp
            @if($plan->isApproved() && count($plannedActivities) > 0)
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Gerar eventos a partir do plano</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Atividades com data definida podem virar eventos no calendário.</p>
                    <div class="space-y-3">
                        @foreach($plannedActivities as $idx => $act)
                            @php
                                $dateStr = $act['date'] ?? $act['start_date'] ?? '';
                                $title = $act['title'] ?? $act['name'] ?? 'Atividade #' . ($idx + 1);
                            @endphp
                            <div class="flex flex-wrap items-center justify-between gap-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $title }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $dateStr }}</span>
                                </div>
                                <form action="{{ route('admin.ministries.plans.generate-event', [$ministry, $plan]) }}" method="POST" class="inline" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Gerando evento...' } }))">
                                    @csrf
                                    <input type="hidden" name="activity_index" value="{{ $idx }}">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">Gerar evento</button>
                                </form>
                            </div>
                        @endforeach
                        <form action="{{ route('admin.ministries.plans.generate-events', [$ministry, $plan]) }}" method="POST" class="pt-2" onsubmit="if(confirm('Gerar um evento para cada atividade selecionada?')) { window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Gerando eventos...' } })); return true; } return false;">
                            @csrf
                            @foreach(array_keys($plannedActivities) as $idx)
                                <input type="hidden" name="indices[]" value="{{ $idx }}">
                            @endforeach
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Gerar todos os eventos em lote</button>
                        </form>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
@endsection
