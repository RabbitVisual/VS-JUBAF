@extends('admin::components.layouts.master')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Ministérios – Visão do Conselho
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Quadro geral de planos, líderes e relatórios mensais.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.churchcouncil.planning.index') }}"
                   class="inline-flex items-center px-3 py-2 text-xs font-bold uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <x-icon name="clipboard-list" class="w-4 h-4 mr-1.5" />
                    Homologação de eventos
                </a>
                <a href="{{ route('admin.churchcouncil.approvals.index') }}"
                   class="inline-flex items-center px-3 py-2 text-xs font-bold uppercase tracking-widest rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition">
                    <x-icon name="inbox" class="w-4 h-4 mr-1.5" />
                    Aprovações pendentes ({{ $pendingPlanApprovals->count() }})
                </a>
            </div>
        </div>

        @if(count($types) > 0)
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="type" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tipo</label>
                    <select name="type" id="type" class="rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 text-sm">Filtrar</button>
            </form>
        @endif

        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-2">
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Relatório entregue</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Pendente / em tolerância</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Sem relatório no mês</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mt-4">
            @foreach($ministries as $ministry)
                @php
                    $plan = $planStatuses[$ministry->id] ?? null;
                    $planLabel = $plan ? (($plan->status === 'under_council_review') ? 'Em revisão' : ($plan->status === 'in_execution' ? 'Em execução' : 'Aprovado')) : 'Sem plano ativo';
                    $planClass = $plan ? (($plan->status === 'under_council_review') ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300') : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
                    $light = $trafficLights[$ministry->id] ?? 'red';
                    $dotClass = match($light) {
                        'green' => 'bg-green-500',
                        'yellow' => 'bg-amber-400',
                        default => 'bg-rose-500',
                    };
                    $dotTitle = match($light) {
                        'green' => 'Relatório do mês entregue',
                        'yellow' => 'Relatório do mês pendente (tolerância)',
                        default => 'Sem relatório enviado no mês',
                    };
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $ministry->name }}</h3>
                        <span class="shrink-0 w-3 h-3 rounded-full {{ $dotClass }}" title="{{ $dotTitle }}"></span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Líder: {{ $ministry->leader?->name ?? $ministry->coLeader?->name ?? '—' }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $planClass }}">{{ $planLabel }}</span>
                        @if($plan && $plan->status === 'under_council_review' && $plan->council_approval_id)
                            <a href="{{ url('/admin/conselho/aprovacoes/' . $plan->council_approval_id) }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Aprovar plano</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($ministries->isEmpty())
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                Nenhum ministério encontrado.
            </div>
        @endif
    </div>
@endsection
