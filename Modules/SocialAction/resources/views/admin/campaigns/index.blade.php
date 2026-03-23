@extends('admin::components.layouts.master')

@section('title', 'Campanhas | Ação Social')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
            <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
            {{ session('error') }}
        </div>
    @endif
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Campanhas e Mobilização</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie arrecadações e iniciativas de impacto social.</p>
        </div>
        <div>
            <a href="{{ route('socialaction.admin.campaigns.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-lg shadow-blue-500/30 transition-all hover:scale-105 active:scale-95">
                <x-icon name="plus" class="h-5 w-5 mr-2" />
                Nova Campanha
            </a>
        </div>
    </div>

    @if($campaigns->isEmpty())
        <div class="col-span-full py-20 text-center bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4 text-blue-600 dark:text-blue-400">
                <x-icon name="bullhorn" class="h-8 w-8" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhuma campanha ativa</h3>
            <p class="text-gray-500 mb-6">Crie mobilizações para arrecadar alimentos, roupas ou recursos.</p>
            <a href="{{ route('socialaction.admin.campaigns.create') }}" class="text-blue-600 hover:text-blue-700 font-medium hover:underline">Iniciar Primeira Campanha</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($campaigns as $campaign)
                @php
                    $percentage = $campaign->target_amount > 0 ? min(round(($campaign->current_amount / $campaign->target_amount) * 100, 1), 100) : 0;
                    $color = $percentage >= 100 ? 'bg-green-500' : ($percentage >= 75 ? 'bg-blue-500' : ($percentage >= 50 ? 'bg-indigo-500' : 'bg-yellow-500'));
                    $statusColors = [
                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800',
                        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800',
                    ];
                    $statusColor = $statusColors[$campaign->status] ?? $statusColors['active'];

                    $statusLabels = [
                        'active' => 'Ativa',
                        'completed' => 'Concluída',
                        'cancelled' => 'Cancelada',
                        'paused' => 'Pausada',
                    ];
                @endphp

                <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col h-full overflow-hidden">
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColor }}">
                                {{ $statusLabels[$campaign->status] ?? ucfirst($campaign->status) }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                {{ $campaign->start_date->format('d/m/Y') }}
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $campaign->title }}
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6 line-clamp-2">
                            {{ $campaign->description }}
                        </p>

                        <div class="mt-auto">
                            <div class="flex justify-between text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                                <span>Progresso</span>
                                <span>{{ $percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 mb-2 overflow-hidden">
                                <div class="{{ $color }} h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span> <strong class="text-gray-900 dark:text-white">{{ number_format($campaign->current_amount ?? 0, 0, ',', '.') }}</strong> arrecadados</span>
                                <span>Meta: {{ number_format($campaign->target_amount ?? 0, 0, ',', '.') }} {{ $campaign->goal_description }}</span>
                            </div>
                            @if($campaign->treasuryCampaign)
                                <p class="mt-2 text-xs text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                    <x-icon name="link" class="w-3.5 h-3.5" />
                                    Vinculada à Tesouraria: {{ $campaign->treasuryCampaign->name }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-2">
                        <a href="{{ route('socialaction.admin.campaigns.edit', $campaign->id) }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                            Gerenciar Campanha
                            <x-icon name="arrow-right" class="h-4 w-4 ml-1" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $campaigns->links() }}
        </div>
    @endif
@endsection

