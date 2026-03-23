@extends('admin::components.layouts.master')

@section('title', 'Dashboard de Louvor')

@section('content')
<div class="space-y-8">
    <!-- Hero Section (Admin pattern) -->
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>

        <div class="relative p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">
                        Módulo de Louvor
                    </span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">
                        Centro de Controle
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black tracking-tight mb-2">
                    Operação <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-300 to-indigo-300">Worship</span>
                </h1>
                <p class="text-gray-300 text-lg max-w-xl">
                    Gerencie repertório, cultos e escalas. Resumo das atividades de louvor.
                </p>

                <!-- Quick Actions -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('worship.admin.songs.create') }}" class="px-5 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-colors flex items-center gap-2 shadow-lg shadow-white/10">
                        <x-icon name="plus" class="w-5 h-5 text-blue-600" />
                        Nova Música
                    </a>
                    <a href="{{ route('worship.admin.setlists.create') }}" class="px-5 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors flex items-center gap-2">
                        <x-icon name="calendar" class="w-5 h-5 text-purple-400" />
                        Agendar Culto
                    </a>
                    <a href="{{ route('worship.admin.songs.index') }}" class="px-5 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors flex items-center gap-2">
                        <x-icon name="music-note" class="w-5 h-5 text-indigo-400" />
                        Biblioteca
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Grid (Admin pattern) -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <x-icon name="music-note" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-wider">Repertório</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['songs'] }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="calendar" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-wider">Cultos Futuros</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['upcoming_setlists'] }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <x-icon name="users" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-wider">Escalados</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['musicians_scheduled'] }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <x-icon name="check-double" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-wider">Aceitação</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['roster_acceptance_rate'] }}<span class="text-sm">%</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <x-icon name="graduation-cap" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-wider">Matrículas EAD</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['academy_enrollments'] }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Next Service (Admin card pattern) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
            <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="clock" class="w-5 h-5 text-gray-400" />
                    Evento em Destaque
                </h3>
            </div>

            <div class="p-8 flex-1">
                @if($nextService)
                    <div class="flex flex-col md:flex-row gap-8 items-center h-full">
                        <div class="flex flex-col items-center justify-center w-28 h-28 rounded-3xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 shrink-0">
                            <span class="text-[10px] font-black uppercase text-orange-600 dark:text-orange-400 mb-1">{{ $nextService->scheduled_at->translatedFormat('M') }}</span>
                            <span class="text-5xl font-black text-gray-900 dark:text-white leading-none">{{ $nextService->scheduled_at->format('d') }}</span>
                            <span class="text-[10px] font-black text-gray-400 mt-1">{{ $nextService->scheduled_at->format('H:i') }}</span>
                        </div>

                        <div class="flex-1 space-y-6 text-center md:text-left">
                            <div>
                                <h4 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $nextService->title }}</h4>
                                <div class="flex items-center justify-center md:justify-start gap-3 mt-3">
                                    <div class="h-8 w-8 rounded-full ring-2 ring-gray-200 dark:ring-gray-600 overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        @if($nextService->leader && $nextService->leader->photo)
                                            <img src="{{ Storage::url($nextService->leader->photo) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-xs font-bold text-gray-500">{{ strtoupper(mb_substr($nextService->leader->name ?? '?', 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Líder: <span class="text-gray-900 dark:text-white">{{ $nextService->leader?->name ?? '—' }}</span></span>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-center md:justify-start gap-2">
                                @foreach($nextService->items->take(4) as $item)
                                    <span class="px-4 py-2 rounded-xl bg-gray-50 dark:bg-white/5 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border border-gray-100 dark:border-gray-600">
                                        {{ $item->song->title }}
                                    </span>
                                @endforeach
                                @if($nextService->items->count() > 4)
                                    <span class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-[10px] font-black text-gray-500">+{{ $nextService->items->count() - 4 }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-center py-12">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                            <x-icon name="calendar-off" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum culto agendado</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">Agende os próximos eventos de louvor.</p>
                    </div>
                @endif
            </div>

            @if($nextService)
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <a href="{{ route('worship.admin.setlists.manage', $nextService->id) }}" class="flex items-center gap-2 text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        Painel de Missão <x-icon name="arrow-right" class="w-4 h-4" />
                    </a>
                </div>
            @endif
        </div>

        <!-- Recent Songs (Admin card pattern) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
            <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="music-note" class="w-5 h-5 text-gray-400" />
                    Últimas Inclusões
                </h3>
                <a href="{{ route('worship.admin.songs.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver Acervo</a>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700 flex-1">
                @forelse($recentSongs as $song)
                    <div class="p-4 px-8 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-sm font-bold">
                                {{ $song->original_key ?? '?' }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $song->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $song->artist ?? 'Artista não cadastrado' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('worship.admin.songs.edit', $song->id) }}" class="p-2 rounded-xl text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <x-icon name="pencil" class="w-5 h-5" />
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="music-note" class="w-12 h-12 mx-auto mb-3 opacity-50" />
                        <p class="text-sm font-medium">Nenhuma música recente.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
