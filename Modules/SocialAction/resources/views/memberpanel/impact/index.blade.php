@extends('memberpanel::components.layouts.master')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white dark:bg-gray-950 border border-gray-200 dark:border-white/5 p-8 sm:p-12 shadow-sm dark:shadow-none mb-12">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-rose-600/5 dark:bg-rose-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-orange-600/5 dark:bg-orange-600/10 rounded-full blur-[100px]"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="flex-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-rose-600 dark:text-rose-500 uppercase tracking-[0.2em] mb-4">
                    <span>Ação Social</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400">Impacto & Compaixão</span>
                </nav>
                <h1 class="text-4xl sm:text-6xl font-black text-gray-900 dark:text-white tracking-tight leading-[1.1] mb-4 italic">
                    Nossa <span class="text-transparent bg-clip-text bg-linear-to-r from-rose-600 to-orange-500">Missão</span> <br>é Amar.
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-md leading-relaxed font-medium">
                    Transformando a realidade de famílias através do serviço voluntário e da generosidade da nossa igreja.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('socialaction.member.campaigns.index') }}" class="px-6 py-3 bg-rose-600 text-white rounded-2xl font-bold shadow-lg shadow-rose-500/30 hover:bg-rose-700 hover:scale-105 transition-all text-sm">
                        Doar para uma Campanha
                    </a>
                    <a href="{{ route('socialaction.member.prayer.index') }}" class="px-6 py-3 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 rounded-2xl font-bold hover:bg-gray-50 transition-all text-sm">
                        Pedir Oração
                    </a>
                </div>
            </div>

            {{-- Personal Stats or CTA --}}
            <div class="w-full md:w-auto">
                @if($userVolunteer)
                    <div class="bg-gray-900 dark:bg-white p-8 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-125 transition-transform">
                            <x-icon name="hands-holding-heart" class="text-6xl text-rose-500" />
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-rose-500 flex items-center justify-center text-white">
                                    <x-icon name="medal" class="text-lg" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest leading-none">Status Voluntário</p>
                                    <p class="text-sm font-bold text-white dark:text-gray-900">{{ $userStats['role'] }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-3xl font-black text-white dark:text-gray-900">{{ $userStats['hours'] }}</p>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Horas Dedicadas</p>
                                </div>
                                <div>
                                    <p class="text-3xl font-black text-white dark:text-gray-900">{{ $userStats['assistances'] }}</p>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Ações Realizadas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-rose-50 dark:bg-rose-900/10 p-8 rounded-[2rem] border border-rose-100 dark:border-rose-900/30 text-center max-w-sm">
                        <x-icon name="people-group" class="text-4xl text-rose-500 mb-4 block" />
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Seja um Voluntário</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 font-medium">Coloque seus dons a serviço do próximo. Precisamos de você!</p>
                        <button class="w-full py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-rose-600 transition-colors">
                            Quero me Inscrever
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- General Impact Stats Line -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
        @php
            $impactStats = [
                ['label' => 'Famílias Apoiadas', 'value' => $stats['families_helped'], 'icon' => 'users', 'color' => 'rose'],
                ['label' => 'Cestas Entregues', 'value' => $stats['kits_delivered'], 'icon' => 'box-open', 'color' => 'orange'],
                ['label' => 'Ações Realizadas', 'value' => $stats['total_assistances'], 'icon' => 'heart-pulse', 'color' => 'blue'],
                ['label' => 'Voluntários Ativos', 'value' => \Modules\SocialAction\App\Models\SocialVolunteer::active()->count(), 'icon' => 'people-carry-box', 'color' => 'green'],
            ];
        @endphp
        @foreach($impactStats as $stat)
            <div class="group bg-white dark:bg-gray-950 p-6 rounded-3xl border border-gray-100 dark:border-white/5 transition-all hover:bg-{{ $stat['color'] }}-50/30 dark:hover:bg-white/5">
                <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/20 text-{{ $stat['color'] }}-600 flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                    <x-icon :name="$stat['icon']" />
                </div>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $stat['value'] }}</p>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Recent Campaigns -->
    <div class="mb-10 flex items-end justify-between">
        <div>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic">Campanhas <span class="text-rose-600">Ativas</span></h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium italic">Sua oferta chega onde as mãos não alcançam.</p>
        </div>
        <a href="{{ route('socialaction.member.campaigns.index') }}" class="px-5 py-2 rounded-xl bg-gray-50 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-2">
            Ver Todas
            <x-icon name="arrow-right" />
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($recentCampaigns as $campaign)
            @inject('engagementService', 'Modules\SocialAction\App\Services\CampaignEngagementService')
            @php $progress = $engagementService->getProgressPercentage($campaign); @endphp

            <div class="group bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/5 rounded-[2.5rem] overflow-hidden hover:shadow-2xl hover:border-rose-500/20 transition-all duration-500 flex flex-col">
                <div class="aspect-square bg-gray-100 dark:bg-gray-900 relative overflow-hidden">
                    @if($campaign->cover_image)
                        <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full bg-linear-to-br from-rose-500 to-orange-400 opacity-20"></div>
                        <div class="absolute inset-0 flex items-center justify-center text-rose-500/20">
                            <x-icon name="hand-holding-heart" class="text-8xl" />
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent opacity-60"></div>

                    <div class="absolute bottom-8 left-8 right-8 text-white">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-rose-600 rounded-md text-[9px] font-black uppercase tracking-widest">Urgente</span>
                            <span class="text-[10px] font-bold opacity-80 uppercase tracking-widest flex items-center gap-1.5">
                                <x-icon name="clock" /> Até {{ $campaign->end_date->format('d/m') }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-black leading-tight italic">{{ $campaign->title }}</h3>
                    </div>
                </div>

                <div class="p-8 flex-1 flex flex-col">
                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-8 font-medium italic">{{ $campaign->description }}</p>

                    <div class="mt-auto space-y-4">
                        <div class="flex justify-between items-end mb-1">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Arrecadado</p>
                                <p class="text-lg font-black text-gray-900 dark:text-white">R$ {{ number_format($campaign->raised_amount ?? 0, 2, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-rose-600">{{ $progress }}%</p>
                            </div>
                        </div>
                        <div class="h-2 w-full bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                            <div class="h-full bg-linear-to-r from-rose-600 to-orange-500 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                        </div>

                        <a href="{{ route('socialaction.member.campaigns.index') }}" class="w-full flex items-center justify-center gap-2 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-rose-600 hover:text-white transition-all">
                            Ofertar Agora <x-icon name="arrow-right-long" class="text-xs" />
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center bg-gray-50 dark:bg-gray-900/10 rounded-[3rem] border border-dashed border-gray-200 dark:border-white/5">
                <x-icon name="heart-circle-xmark" class="text-5xl text-gray-300 mb-4 block" />
                <p class="text-gray-500 dark:text-gray-400 font-bold italic">Nenhuma campanha necessitando de oferta no momento.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
