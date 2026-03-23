@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Tesouraria</span>
                            <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Campanhas</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Campanhas ministeriais</h1>
                        <p class="text-gray-300 max-w-xl">Mobilize recursos para grandes projetos e missões.</p>
                    </div>
                    @if ($permission->canManageCampaigns())
                        <a href="{{ route('treasury.campaigns.create') }}"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all">
                            <x-icon name="plus" style="duotone" class="w-5 h-5 text-blue-600 mr-2" />
                            Nova campanha
                        </a>
                    @endif
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Campanhas' => null]])
            </div>
        </div>

        @php $featured = $campaigns->first(); @endphp
        @if($featured)
            <!-- Destaque -->
            <div class="relative group h-[360px] md:h-[400px] rounded-3xl overflow-hidden shadow-xl border border-gray-200 dark:border-gray-700">
                @if ($featured->image)
                    <img src="{{ Storage::url($featured->image) }}" alt="{{ $featured->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>

                <div class="absolute inset-0 p-8 md:p-14 flex flex-col justify-end">
                    <div class="max-w-3xl space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="px-4 py-1.5 bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg">DESTAQUE</span>
                            <span class="px-4 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest rounded-full border border-white/20">
                                {{ $featured->isActive() ? 'Ativa' : 'Pausada' }}
                            </span>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-black text-white leading-tight uppercase tracking-tighter">{{ $featured->name }}</h2>
                        <p class="text-slate-300 font-medium line-clamp-2 text-lg leading-relaxed">
                            {{ $featured->description ?? 'Campanha estratégica para o crescimento e fortalecimento do ministério.' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-10 pt-4">
                            @if($featured->target_amount)
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alvo da Campanha</p>
                                <p class="text-2xl font-black text-white tabular-nums">R$ {{ number_format($featured->target_amount, 2, ',', '.') }}</p>
                            </div>
                            <div class="flex-1 max-w-[200px] space-y-2">
                                <div class="flex justify-between text-[10px] font-black text-blue-400 uppercase tracking-widest">
                                    <span>Progresso</span>
                                    <span>{{ number_format($featured->progress_percentage, 1) }}%</span>
                                </div>
                                <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-linear-to-r from-blue-400 to-indigo-400 rounded-full transition-all duration-1000" style="width: {{ min(100, $featured->progress_percentage) }}%"></div>
                                </div>
                            </div>
                            @endif
                            <a href="{{ route('treasury.campaigns.show', $featured) }}" class="inline-flex items-center px-6 py-3 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-100 transition-colors shadow-lg">
                                Ver detalhes
                                <x-icon name="arrow-right" style="duotone" class="ml-2 w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lista de campanhas -->
        @if($campaigns->count() > ($featured ? 1 : 0))
            <div class="flex items-center gap-4">
                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lista de campanhas</h3>
                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($campaigns as $campaign)
                @if($featured && $campaign->id === $featured->id) @continue @endif
                <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col transition-all hover:shadow-lg relative">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                        <div class="relative h-48 overflow-hidden">
                            @if ($campaign->image)
                                <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="absolute inset-0 bg-linear-to-br from-blue-500/10 to-indigo-600/10 flex items-center justify-center">
                                    <x-icon name="bullhorn" style="duotone" class="w-12 h-12 text-blue-500/20" />
                                </div>
                            @endif
                            <!-- Gradient Overlay on Image -->
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 dark:from-gray-800 via-transparent to-transparent opacity-60"></div>

                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $campaign->isActive() ? 'bg-green-500/90 text-white' : 'bg-gray-500/90 text-white' }}">
                                    {{ $campaign->isActive() ? 'Ativa' : 'Encerrada' }}
                                </span>
                            </div>
                        </div>

                        <div class="relative p-6 flex-1 flex flex-col space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1">
                                {{ $campaign->name }}
                            </h3>

                            @if ($campaign->target_amount)
                                <div class="space-y-3">
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acumulado</p>
                                            <p class="text-base font-bold text-green-600 dark:text-green-400 tabular-nums">R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progresso</p>
                                            <p class="text-sm font-bold text-blue-600 dark:text-blue-400 tabular-nums">{{ number_format($campaign->progress_percentage, 1) }}%</p>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full transition-all duration-500"
                                            style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between mt-auto">
                                <div class="flex items-center gap-2">
                                    <x-icon name="calendar-range" style="duotone" class="w-4 h-4 text-gray-400" />
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $campaign->end_date ? 'Até ' . $campaign->end_date->format('d/m/Y') : 'Contínua' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($permission->canManageCampaigns())
                                        <a href="{{ route('treasury.campaigns.edit', $campaign) }}" class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 rounded-lg transition-colors" title="Editar">
                                            <x-icon name="pencil" style="duotone" class="w-4 h-4" />
                                        </a>
                                    @endif
                                    <a href="{{ route('treasury.campaigns.show', $campaign) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-xl text-xs font-bold hover:bg-gray-800 dark:hover:bg-gray-600 transition-all">
                                        Ver mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20 px-8 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-6">
                            <x-icon name="bullhorn" style="duotone" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma campanha</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm text-sm mb-8">Mobilize sua comunidade criando campanhas de arrecadação.</p>
                        @if ($permission->canManageCampaigns())
                            <a href="{{ route('treasury.campaigns.create') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 dark:bg-blue-600 text-white rounded-xl font-bold hover:bg-gray-800 dark:hover:bg-blue-700 transition-all">
                                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" />
                                Nova campanha
                            </a>
                        @endif
                    </div>
                @endforelse
        </div>

        @if ($campaigns->hasPages())
            <div class="flex justify-center">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
@endsection
