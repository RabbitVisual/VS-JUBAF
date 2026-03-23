@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Campanhas')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Campanhas financeiras</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Causas e mobilizações de arrecadação da instituição.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    @if ($permission->canManageCampaigns())
                        <a href="{{ route('memberpanel.treasury.campaigns.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-500/20 shrink-0">
                            <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" />
                            Nova campanha
                        </a>
                    @endif
                </div>
            </div>

            @php $featured = $campaigns->first(); @endphp
            @if($featured)
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200" data-tour="treasury-area">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 flex flex-col md:flex-row md:items-center justify-between gap-8 z-10">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800 mb-4">
                            <x-icon name="bullhorn" style="duotone" class="w-3 h-3 text-purple-600 dark:text-purple-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">Destaque</span>
                        </div>
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $featured->name }}</h2>
                        <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl mt-2 line-clamp-2">
                            {{ $featured->description ?? 'Campanha estratégica para o crescimento e fortalecimento do ministério.' }}
                        </p>
                        @if($featured->target_amount)
                        <div class="flex flex-wrap items-center gap-6 mt-4">
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-slate-400">Alvo</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($featured->target_amount, 2, ',', '.') }}</p>
                            </div>
                            <div class="flex-1 min-w-[160px] max-w-[200px] space-y-1.5">
                                <div class="flex justify-between text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    <span>Progresso</span>
                                    <span>{{ number_format($featured->progress_percentage, 1) }}%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 dark:bg-indigo-500 rounded-full transition-all duration-500" style="width: {{ min(100, $featured->progress_percentage) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('memberpanel.treasury.campaigns.show', $featured) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 dark:bg-indigo-600 hover:bg-gray-800 dark:hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all shrink-0">
                        Ver detalhes
                        <x-icon name="arrow-right" style="solid" class="w-4 h-4 ml-2" />
                    </a>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="list" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Todas as campanhas</h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($campaigns as $campaign)
                            @if($featured && $campaign->id === $featured->id) @continue @endif
                            <div class="group bg-gray-50/50 dark:bg-slate-800/30 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 flex flex-col transition-all hover:shadow-md">
                                @if ($campaign->image)
                                    <div class="rounded-xl overflow-hidden h-36 mb-4">
                                        <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @endif
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-1">{{ $campaign->name }}</h4>
                                    <span class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $campaign->isActive() ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-400' }}">
                                        {{ $campaign->isActive() ? 'Ativa' : 'Encerrada' }}
                                    </span>
                                </div>
                                @if ($campaign->target_amount)
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-end justify-between">
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Acumulado</span>
                                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                            <div class="h-full bg-indigo-500 rounded-full transition-all" style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                        {{ $campaign->end_date ? 'Até ' . $campaign->end_date->format('d/m/Y') : 'Contínua' }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        @if ($permission->canManageCampaigns())
                                            <a href="{{ route('memberpanel.treasury.campaigns.edit', $campaign) }}" class="p-2 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="Editar">
                                                <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('memberpanel.treasury.campaigns.show', $campaign) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-indigo-600 hover:bg-gray-800 dark:hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all">
                                            Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col items-center justify-center py-16 px-8 bg-gray-50 dark:bg-slate-800/30 rounded-2xl border border-gray-100 dark:border-slate-800">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mb-6">
                                    <x-icon name="bullhorn" style="duotone" class="w-10 h-10 text-gray-400 dark:text-slate-500" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma campanha</h3>
                                <p class="text-gray-500 dark:text-slate-400 text-center max-w-sm text-sm mb-8">Mobilize a comunidade criando campanhas de arrecadação.</p>
                                @if ($permission->canManageCampaigns())
                                    <a href="{{ route('memberpanel.treasury.campaigns.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all">
                                        <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" />
                                        Nova campanha
                                    </a>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    @if ($campaigns->hasPages())
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                            {{ $campaigns->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
