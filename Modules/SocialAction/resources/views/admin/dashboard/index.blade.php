@extends('admin::components.layouts.master')

@section('title', 'Dashboard | Ação Social')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                    <x-icon name="heart-circle-bolt" class="text-xl" />
                </span>
                Ação Social
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Painel de impacto comunitário — {{ now()->format('d \d\e F \d\e Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('socialaction.admin.beneficiaries.create') }}" class="inline-flex items-center px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold shadow-lg shadow-rose-500/30 transition-all hover:scale-105 active:scale-95 gap-2">
                <x-icon name="user-plus" />
                Novo Beneficiário
            </a>
            <a href="{{ route('socialaction.admin.assistance.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all gap-2">
                <x-icon name="hand-holding-heart" />
                Registrar Assistência
            </a>
        </div>
    </div>

    {{-- Sessions --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="circle-check" /> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
        @php
            $kpis = [
                ['label' => 'Famílias Cadastradas', 'value' => $stats['total_beneficiaries'],    'icon' => 'users',                 'color' => 'indigo',  'route' => route('socialaction.admin.beneficiaries.index')],
                ['label' => 'Famílias Ativas',      'value' => $stats['active_beneficiaries'],   'icon' => 'heart',                  'color' => 'rose',    'route' => route('socialaction.admin.beneficiaries.index', ['status'=>'active'])],
                ['label' => 'Assistências/Mês',     'value' => $stats['assistances_this_month'], 'icon' => 'hand-holding-heart',     'color' => 'green',   'route' => route('socialaction.admin.assistance.index')],
                ['label' => 'Kits Entregues',        'value' => $stats['kits_delivered'],         'icon' => 'box-open',              'color' => 'purple',  'route' => route('socialaction.admin.assistance.index')],
                ['label' => 'Voluntários Ativos',    'value' => $stats['active_volunteers'],      'icon' => 'people-pulling',        'color' => 'sky',     'route' => route('socialaction.admin.volunteers.index')],
                ['label' => 'Campanhas Ativas',      'value' => $stats['active_campaigns'],       'icon' => 'bullhorn',              'color' => 'amber',   'route' => route('socialaction.admin.campaigns.index')],
                ['label' => 'Pedidos de Oração',     'value' => $stats['pending_prayers'],        'icon' => 'hands-praying',         'color' => 'teal',    'route' => route('socialaction.admin.prayer.index')],
                ['label' => 'Itens Críticos',        'value' => $stats['out_of_stock_items'] + $stats['low_stock_items'], 'icon' => 'triangle-exclamation', 'color' => 'red', 'route' => route('socialaction.admin.stock.index')],
            ];
        @endphp

        @foreach($kpis as $kpi)
            @php
                $colorMap = [
                    'indigo' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'text' => 'text-indigo-600 dark:text-indigo-400', 'hover' => 'hover:border-indigo-300 dark:hover:border-indigo-700'],
                    'rose'   => ['bg' => 'bg-rose-100 dark:bg-rose-900/30',     'text' => 'text-rose-600 dark:text-rose-400',     'hover' => 'hover:border-rose-300 dark:hover:border-rose-700'],
                    'green'  => ['bg' => 'bg-green-100 dark:bg-green-900/30',   'text' => 'text-green-600 dark:text-green-400',   'hover' => 'hover:border-green-300 dark:hover:border-green-700'],
                    'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400', 'hover' => 'hover:border-purple-300 dark:hover:border-purple-700'],
                    'sky'    => ['bg' => 'bg-sky-100 dark:bg-sky-900/30',       'text' => 'text-sky-600 dark:text-sky-400',       'hover' => 'hover:border-sky-300 dark:hover:border-sky-700'],
                    'amber'  => ['bg' => 'bg-amber-100 dark:bg-amber-900/30',   'text' => 'text-amber-600 dark:text-amber-400',   'hover' => 'hover:border-amber-300 dark:hover:border-amber-700'],
                    'teal'   => ['bg' => 'bg-teal-100 dark:bg-teal-900/30',     'text' => 'text-teal-600 dark:text-teal-400',     'hover' => 'hover:border-teal-300 dark:hover:border-teal-700'],
                    'red'    => ['bg' => 'bg-red-100 dark:bg-red-900/30',       'text' => 'text-red-600 dark:text-red-400',       'hover' => 'hover:border-red-300 dark:hover:border-red-700'],
                ];
                $c = $colorMap[$kpi['color']];
            @endphp
            <a href="{{ $kpi['route'] }}" class="group bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 {{ $c['hover'] }} transition-all duration-200 flex items-center gap-4 hover:shadow-md">
                <div class="p-3 {{ $c['bg'] }} {{ $c['text'] }} rounded-xl shrink-0 group-hover:scale-110 transition-transform">
                    <x-icon :name="$kpi['icon']" class="text-xl" />
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $kpi['value'] }}</p>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-tight">{{ $kpi['label'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Main Grid: Assistências + Estoque Crítico + Campanhas + Orações --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Últimas Assistências --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="clipboard-list" class="text-rose-500" />
                    Últimas Assistências
                </h3>
                <a href="{{ route('socialaction.admin.assistance.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Ver todas →</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($recentAssistances as $assistance)
                    @php
                        $typeBadge = match($assistance->type) {
                            'financial' => ['label' => 'Financeiro', 'color' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
                            'kit'       => ['label' => $assistance->kit?->name ?? 'Kit', 'color' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'],
                            default     => ['label' => $assistance->pantryItem?->name ?? 'Item', 'color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'],
                        };
                    @endphp
                    <div class="px-6 py-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ substr($assistance->beneficiary?->full_name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $assistance->beneficiary?->full_name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assistance->registered_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $typeBadge['color'] }} whitespace-nowrap">
                            {{ $typeBadge['label'] }}
                        </span>
                        @if($assistance->type === 'financial')
                            <span class="text-sm font-bold text-gray-900 dark:text-white">R$ {{ number_format($assistance->amount, 2, ',', '.') }}</span>
                        @else
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($assistance->quantity, 0, ',', '.') }}</span>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">Nenhuma assistência registrada ainda.</div>
                @endforelse
            </div>
        </div>

        {{-- Lateral: Estoque Crítico + Pedidos de Oração --}}
        <div class="space-y-6">
            {{-- Estoque Crítico --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="triangle-exclamation" class="text-yellow-500" />
                        Estoque Crítico
                    </h3>
                    <a href="{{ route('socialaction.admin.stock.index') }}" class="text-xs font-semibold text-yellow-600 hover:text-yellow-700 dark:text-yellow-400">Ver tudo →</a>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($criticalStockItems as $item)
                        <div class="px-5 py-3 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">Mínimo: {{ $item->min_quantity }} {{ $item->unit }}</p>
                            </div>
                            <span class="text-sm font-black {{ $item->current_quantity <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ number_format($item->current_quantity, 0) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-green-600 dark:text-green-400">
                            <x-icon name="circle-check" class="mr-1" /> Estoque normalizado!
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pedidos de Oração Pendentes --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="hands-praying" class="text-teal-500" />
                        Pedidos de Oração
                    </h3>
                    <a href="{{ route('socialaction.admin.prayer.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 dark:text-teal-400">Ver todos →</a>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($pendingPrayers as $prayer)
                        <div class="px-5 py-3">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $prayer->is_anonymous ? 'Anônimo' : $prayer->name }}
                                </p>
                                <span class="text-xs text-gray-400">{{ $prayer->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $prayer->request }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-teal-600 dark:text-teal-400">
                            <x-icon name="hands-praying" class="mr-1" /> Nenhum pedido pendente.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Campanhas Ativas --}}
    @if($activeCampaigns->isNotEmpty())
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="bullhorn" class="text-amber-500" />
                    Campanhas Ativas
                </h3>
                <a href="{{ route('socialaction.admin.campaigns.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400">Gerenciar →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-0 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                @foreach($activeCampaigns as $campaign)
                    @php
                        $pct = $campaign->target_amount > 0 ? min(round(($campaign->current_amount / $campaign->target_amount) * 100), 100) : 0;
                    @endphp
                    <div class="px-5 py-4">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate mb-2">{{ $campaign->title }}</h4>
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Progresso</span>
                            <span class="font-bold {{ $pct >= 100 ? 'text-green-600' : 'text-gray-700 dark:text-gray-300' }}">{{ $pct }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden mb-2">
                            <div class="{{ $pct >= 100 ? 'bg-green-500' : 'bg-amber-500' }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Meta: {{ $campaign->goal_description }}
                        </p>
                        @if($campaign->end_date)
                            <p class="text-xs text-gray-400 mt-1">Encerra: {{ $campaign->end_date->format('d/m/Y') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
