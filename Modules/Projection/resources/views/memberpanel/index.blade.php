@extends('memberpanel::components.layouts.master')

@section('page-title', 'Centro de Projeção')

@section('content')
    <div class="p-6 space-y-8 animate-in fade-in duration-700">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1.5">
                    <span>Módulo de Projeção</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400 dark:text-gray-500">Live Cockpit</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">Centro de <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-cyan-600 dark:from-blue-400 dark:to-cyan-400">Projeção</span></h1>
            </div>

            <!-- Botões de Ação Rápida -->
            <div class="flex items-center gap-3" data-tour="projection-console">
                @php
                    $firstSetlist = $setlists->first();
                @endphp

                <a href="{{ $firstSetlist ? route('memberpanel.projection.console', $firstSetlist->id) : '#' }}"
                   target="_blank"
                   @if(!$firstSetlist) onclick="alert('Crie um culto primeiro para abrir o painel.'); return false;" @endif
                   class="flex items-center gap-3 px-6 py-4 bg-gray-900 dark:bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all active:scale-95 shadow-xl shadow-blue-500/20">
                    <i class="fa-solid fa-gamepad-modern text-[16px]"></i>
                    Painel de Controle
                </a>

                <a href="{{ route('memberpanel.projection.screen') }}"
                   target="_blank"
                   class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all active:scale-95 shadow-sm"
                   data-tour="projection-screen">
                    <i class="fa-solid fa-projector text-[16px]"></i>
                    Tela de Projeção
                </a>

                <a href="{{ route('memberpanel.projection.remote') }}"
                   target="_blank"
                   class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all active:scale-95 shadow-sm">
                    <i class="fa-solid fa-mobile-screen text-[16px]"></i>
                    Remote (Celular)
                </a>
            </div>
        </div>

        @if(isset($todaySetlist) && $todaySetlist)
            <div class="rounded-2xl border-2 border-blue-500/30 bg-blue-500/5 dark:bg-blue-900/20 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white">
                        <i class="fa-duotone fa-calendar-star text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">Culto de hoje</p>
                        <h2 class="text-lg font-black text-gray-900 dark:text-white">{{ $todaySetlist->title }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $todaySetlist->scheduled_at?->format('d/m/Y H:i') }} · {{ $todaySetlist->items->count() }} itens</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('memberpanel.projection.console', $todaySetlist->id) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase transition">
                        <i class="fa-solid fa-gamepad-modern"></i> Console
                    </a>
                    <a href="{{ route('memberpanel.projection.screen') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-white/10 rounded-xl text-xs font-black uppercase hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <i class="fa-solid fa-projector"></i> Tela
                    </a>
                </div>
            </div>
        @endif

        @if($setlists->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($setlists as $setlist)
                    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden flex flex-col">
                        <div class="p-8 space-y-6 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="px-4 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-100 dark:border-blue-500/20">
                                    {{ $setlist->scheduled_at->format('d/m H:i') }}
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-{{ $setlist->status->color() }}-100 text-{{ $setlist->status->color() }}-800 dark:bg-{{ $setlist->status->color() }}-900/30 dark:text-{{ $setlist->status->color() }}-400 border border-{{ $setlist->status->color() }}-200/50 dark:border-{{ $setlist->status->color() }}-500/20">
                                        {{ $setlist->status->label() }}
                                    </span>
                                    <div class="text-gray-300 dark:text-gray-700 group-hover:text-blue-500 transition-colors">
                                        <i class="fa-duotone fa-desktop text-[24px]"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight">
                                    {{ $setlist->title }}
                                </h3>
                                <p class="text-xs font-bold text-gray-400 dark:text-gray-500">
                                    {{ $setlist->items_count ?? $setlist->items->count() }} músicas no repertório
                                </p>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-white/5 border-t border-gray-100 dark:border-white/10 grid grid-cols-2 gap-3">
                            <a href="{{ route('memberpanel.projection.console', $setlist->id) }}" target="_blank"
                               class="flex items-center justify-center gap-2 py-4 bg-gray-900 dark:bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all active:scale-95 shadow-lg shadow-blue-500/10">
                                <i class="fa-solid fa-layer-group text-[14px]"></i>
                                Console
                            </a>
                            <a href="{{ route('memberpanel.projection.screen') }}" target="_blank"
                               class="flex items-center justify-center gap-2 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest border border-gray-200 dark:border-white/10 hover:bg-gray-50 transition-all active:scale-95">
                                <i class="fa-solid fa-up-right-from-square text-[14px]"></i>
                                Tela
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 px-4">
                 {{ $setlists->links('pagination::tailwind') }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 px-4 bg-white dark:bg-gray-900 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-white/5 shadow-sm">
                <div class="w-32 h-32 bg-gray-50 dark:bg-gray-950 rounded-full flex items-center justify-center mb-8">
                    <i class="fa-duotone fa-presentation-screen text-[64px] text-gray-300 dark:text-gray-700"></i>
                </div>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-3">Nenhum culto ativo</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-10 leading-relaxed">Não há cultos agendados para hoje ou datas próximas para projeção.</p>
                <a href="{{ route('worship.admin.setlists.index') }}" class="inline-flex items-center px-10 py-5 text-xs font-black text-white bg-blue-600 hover:bg-blue-700 rounded-2xl shadow-xl shadow-blue-500/40 transition-all transform hover:scale-105 active:scale-95 uppercase tracking-widest">
                    <i class="fa-solid fa-calendar-days text-[16px] mr-3"></i>
                    Gerenciar Cultos
                </a>
            </div>
        @endif
    </div>
@endsection

