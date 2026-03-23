@extends('memberpanel::components.layouts.master')

@section('page-title', 'Minhas Escalas - Louvor')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-4xl bg-white dark:bg-gray-950 border border-gray-200 dark:border-white/5 p-8 sm:p-12 shadow-sm dark:shadow-none">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-600/5 dark:bg-blue-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-purple-600/5 dark:bg-purple-600/10 rounded-full blur-[100px]"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <nav class="flex items-center gap-2 text-xs font-bold text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-4">
                    <span>Ministério de Louvor</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400 dark:text-gray-500">Minhas Escalas</span>
                </nav>
                <h1 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-[1.1] mb-3">Sua Agenda de <br><span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Ministração</span></h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-md leading-relaxed">Confira abaixo as datas em que você está escalado para servir ao Senhor com seus talentos.</p>
            </div>

            <div class="bg-gray-50 dark:bg-white/5 backdrop-blur-xl rounded-3xl p-6 border border-gray-200 dark:border-white/10 ring-1 ring-black/5 dark:ring-white/5 shadow-xl">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                        <x-icon name="calendar-days" style="duotone" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-none mb-1">Total de Escalas</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $rosters->total() }} Escalas Próximas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roster Grid -->
    <div class="grid grid-cols-1 gap-6" data-tour="worship-rosters-list">
        @forelse($rosters as $roster)
        <div class="group relative overflow-hidden bg-white dark:bg-gray-900/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-all duration-500 rounded-3xl border border-gray-200 dark:border-white/5 hover:border-blue-500/30 shadow-sm hover:shadow-xl">
            <div class="flex flex-col lg:flex-row items-stretch">
                <!-- Date column -->
                <div class="lg:w-48 bg-gray-50 dark:bg-gray-900 p-8 flex flex-row lg:flex-col items-center justify-center gap-4 lg:gap-1 text-center border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-white/5 shadow-inner dark:shadow-none">
                    <span class="text-xs font-black text-blue-600 dark:text-blue-500 uppercase tracking-[0.2em]">{{ $roster->setlist->scheduled_at->translatedFormat('M') }}</span>
                    <span class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $roster->setlist->scheduled_at->format('d') }}</span>
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase pb-1 border-b border-gray-200 dark:border-gray-800 w-12">{{ $roster->setlist->scheduled_at->translatedFormat('D') }}</span>
                </div>

                <!-- Info column -->
                <div class="flex-1 p-8">
                    <div class="flex flex-col h-full justify-between gap-4">
                        <div class="text-left">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded-md bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 text-[10px] font-black uppercase tracking-tighter border border-green-200 dark:border-green-500/20">Confirmado</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $roster->setlist->scheduled_at->format('H:i') }} • {{ $roster->setlist->leader->name }}</span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300 mb-2 leading-tight">{{ $roster->setlist->title }}</h3>
                        </div>

                        <div class="flex flex-wrap items-center gap-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-950 flex items-center justify-center text-gray-500 dark:text-gray-400 ring-1 ring-black/5 dark:ring-white/10 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors shrink-0">
                                    <x-icon name="music" class="w-5 h-5" />
                                </div>
                                <div class="text-left">
                                    <p class="text-[9px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-wider mb-1">Sua Função</p>
                                    <p class="text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide leading-normal">{{ $roster->instrument->name }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-950 flex items-center justify-center text-gray-500 dark:text-gray-400 ring-1 ring-black/5 dark:ring-white/10 shrink-0">
                                    <x-icon name="briefcase" class="w-5 h-5" />
                                </div>
                                <div class="text-left">
                                    <p class="text-[9px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-wider mb-1">Repertório</p>
                                    <p class="text-sm font-bold text-gray-600 dark:text-gray-300 leading-normal">{{ $roster->setlist->items->count() }} músicas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action column -->
                <div class="lg:w-64 p-8 flex items-center justify-center bg-gray-50 dark:bg-gray-900/30 border-t lg:border-t-0 lg:border-l border-gray-100 dark:border-white/5">
                    <a href="{{ route('worship.member.stage.view', $roster->setlist->id) }}" class="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 dark:hover:bg-blue-500 text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-blue-500/20 dark:shadow-blue-500/10">
                        Visualizar Cifras
                        <x-icon name="arrow-right" class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-24 text-center bg-gray-50 dark:bg-gray-950/50 rounded-[3rem] border border-dashed border-gray-300 dark:border-white/5 shadow-inner">
            <div class="w-24 h-24 rounded-full bg-white dark:bg-gray-900 flex items-center justify-center text-gray-300 dark:text-gray-700 mb-6 shadow-sm border border-gray-100 dark:border-none">
                <x-icon name="calendar-days" style="duotone" class="w-12 h-12 opacity-40 dark:opacity-20" />
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2 leading-tight">Sem escalas planejadas</h3>
            <p class="text-gray-500 max-w-xs mx-auto">Você não possui escalas de louvor ativas no momento. Fique atento às notificações!</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center pt-8">
        {{ $rosters->links() }}
    </div>
</div>
@endsection

