@extends('memberpanel::components.layouts.master')

@section('title', 'Campanhas Solidárias')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white dark:bg-gray-950 border border-gray-200 dark:border-white/5 p-8 sm:p-12 shadow-sm dark:shadow-none mb-12">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-rose-600/5 dark:bg-rose-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-orange-600/5 dark:bg-orange-600/10 rounded-full blur-[100px]"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-black text-rose-600 dark:text-rose-500 uppercase tracking-[0.2em] mb-4">
                    <a href="{{ route('socialaction.member.impact.index') }}" class="hover:text-orange-500 transition-colors">Ação Social</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400">Campanhas Ativas</span>
                </nav>
                <h1 class="text-4xl sm:text-6xl font-black text-gray-900 dark:text-white tracking-tight leading-[1.1] mb-4 italic">
                    Amor em <br><span class="text-transparent bg-clip-text bg-linear-to-r from-rose-600 to-orange-500">Ação.</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-md leading-relaxed font-medium">
                    Cada campanha é uma oportunidade de manifestar o Reino de Deus de forma prática.
                </p>
            </div>

            <div class="bg-white/50 dark:bg-white/5 backdrop-blur-xl rounded-[2rem] p-8 border border-gray-200/50 dark:border-white/10 shadow-2xl">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-14 h-14 rounded-2xl bg-rose-500 shadow-lg shadow-rose-500/20 flex items-center justify-center text-white shrink-0">
                            <x-icon name="hand-holding-heart" class="text-2xl" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Impacto Global</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white leading-tight">+{{ $activeDonorsCount }} Doadores</p>
                        </div>
                    </div>

                    <div class="h-px w-full bg-gray-100 dark:bg-white/10"></div>

                    <div class="flex gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400">
                         <span class="flex-1 text-center py-2 px-3 rounded-lg bg-gray-50 dark:bg-white/5">
                            <x-icon name="check-circle" class="text-rose-500 mr-1" /> Transparente
                        </span>
                        <span class="flex-1 text-center py-2 px-3 rounded-lg bg-gray-50 dark:bg-white/5">
                            <x-icon name="shield-heart" class="text-rose-500 mr-1" /> Seguro
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-12 flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <x-icon name="magnifying-glass" class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400" />
            <input type="text" placeholder="Encontrar uma causa para apoiar..."
                   class="w-full pl-12 pr-6 py-4 bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/5 rounded-2xl focus:ring-rose-500 focus:border-rose-500 shadow-sm text-sm font-medium transition-all">
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <select class="px-6 py-4 bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/5 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-500 focus:ring-rose-500 cursor-pointer shadow-sm">
                <option>Todas Categorias</option>
                <option>Alimentos</option>
                <option>Saúde</option>
                <option>Educação</option>
            </select>
        </div>
    </div>

    @if($campaigns->isEmpty())
        <div class="py-24 text-center bg-white dark:bg-gray-950 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-white/5">
            <div class="w-24 h-24 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center mx-auto mb-6">
                 <x-icon name="heart-circle-bolt" class="text-4xl text-gray-300" />
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2 italic">Nenhuma campanha aberta</h3>
            <p class="text-gray-500 font-medium italic">Gloria a Deus! Nenhuma necessidade urgente aberta agora, mas fique atento a novos chamados.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($campaigns as $campaign)
                @inject('engagementService', 'Modules\SocialAction\App\Services\CampaignEngagementService')
                @php $progress = $engagementService->getProgressPercentage($campaign); @endphp

                <div class="group bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/5 rounded-[2.5rem] overflow-hidden hover:shadow-2xl hover:border-rose-500/20 transition-all duration-500 flex flex-col h-full">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-900 relative overflow-hidden">
                        @if($campaign->cover_image)
                            <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-linear-to-br from-rose-500 to-orange-400 opacity-20"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-rose-500/10">
                                <x-icon name="hand-holding-heart" class="text-9xl" />
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent opacity-60"></div>

                        <div class="absolute bottom-8 left-8 right-8 text-white">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2.5 py-1 bg-rose-600 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg">Ativa</span>
                                <span class="text-[10px] font-bold opacity-80 uppercase tracking-widest flex items-center gap-2">
                                    <x-icon name="hourglass-half" /> {{ $campaign->end_date ? 'Até ' . $campaign->end_date->format('d/m') : 'Sem prazo' }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-black leading-tight italic line-clamp-2">{{ $campaign->title }}</h3>
                        </div>
                    </div>

                    <div class="p-8 flex-1 flex flex-col">
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 mb-8 font-medium italic leading-relaxed">{{ $campaign->description }}</p>

                        <div class="mt-auto space-y-6">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Meta da Campanha</p>
                                        <p class="text-lg font-black text-gray-900 dark:text-white">
                                            {{ $campaign->target_amount ? 'R$ ' . number_format($campaign->target_amount, 2, ',', '.') : 'Livre' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-black text-rose-600">{{ $progress }}%</p>
                                    </div>
                                </div>
                                <div class="h-2 w-full bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-linear-to-r from-rose-600 to-orange-500 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(225,29,72,0.4)]" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            @if($campaign->treasuryCampaign)
                                <a href="{{ route('memberpanel.treasury.campaigns.show', $campaign->treasuryCampaign) }}"
                                   class="w-full flex items-center justify-center gap-2 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg hover:shadow-rose-500/20 hover:bg-rose-600 hover:text-white transition-all group-hover:scale-[1.02]">
                                    Ofertar via Tesouraria <x-icon name="arrow-right-long" class="text-xs" />
                                </a>
                            @else
                                <div class="w-full py-4 bg-gray-50 dark:bg-white/5 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-center italic border border-gray-100 dark:border-white/5">
                                    Procure a secretaria para doar
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 flex justify-center">
            {{ $campaigns->links() }}
        </div>
    @endif
</div>
@endsection
