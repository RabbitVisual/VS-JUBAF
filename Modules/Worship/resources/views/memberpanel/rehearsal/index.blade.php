@extends('memberpanel::components.layouts.master')

@section('page-title', 'Sala de Ensaio')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-gray-950 border border-white/5 p-8 sm:p-12 shadow-2xl">
        <div class="absolute right-0 top-0 -mr-20 -mt-20 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute left-0 bottom-0 -ml-20 -mb-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-2xl">
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-4">
                    <span>Ministério de Louvor</span>
                    <span class="w-1 h-1 rounded-full bg-gray-700"></span>
                    <span class="text-gray-500">Preparação</span>
                </nav>
                <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-[1.1] mb-4">Sala de Ensaio <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Virtual</span></h1>
                <p class="text-gray-400 text-lg leading-relaxed">Acesse os repertórios, ouça as referências originais e acompanhe as cifras sincronizadas de onde você estiver.</p>
            </div>

            @if($nextSetlist)
            <div class="bg-gray-900 border border-white/10 rounded-3xl p-6 shadow-xl shrink-0 w-full md:w-80 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase tracking-widest border border-blue-500/20 mb-4 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span> Próximo Culto
                    </span>
                    <h3 class="text-xl font-black text-white mb-1 line-clamp-1">{{ $nextSetlist->title }}</h3>
                    <p class="text-sm font-medium text-gray-500 flex items-center gap-2 mb-6">
                        <x-icon name="calendar" class="w-4 h-4" />
                        {{ $nextSetlist->scheduled_at->translatedFormat('d \d\e F') }}
                    </p>
                    <a href="{{ route('worship.member.rehearsal.show', $nextSetlist->id) }}" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition-all shadow-lg hover:shadow-blue-500/25 active:scale-95">
                        <x-icon name="headphones" class="w-4 h-4" /> Entrar na Sala
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Setlist Grid -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <x-icon name="list-music" class="w-6 h-6 text-gray-400" />
                Repertórios Agendados
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($upcomingSetlists as $set)
            <a href="{{ route('worship.member.rehearsal.show', $set->id) }}" class="group flex flex-col justify-between bg-white dark:bg-gray-900 rounded-3xl p-6 border border-gray-200 dark:border-white/5 hover:border-blue-500/30 hover:shadow-xl dark:hover:bg-gray-800 transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>

                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-colors mb-4 border border-gray-100 dark:border-white/5 group-hover:border-blue-500/20">
                        <x-icon name="music" class="w-5 h-5" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-blue-600 dark:text-blue-400 mb-2 block">{{ $set->scheduled_at->translatedFormat('d/m/Y - H:i') }}</span>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2">{{ $set->title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $set->description ?? 'Nenhuma observação geral.' }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    <span>Ensaiar Músicas</span>
                    <x-icon name="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                </div>
            </a>
            @empty
            <div class="col-span-full py-20 bg-gray-50 dark:bg-gray-900/50 rounded-[3rem] border border-dashed border-gray-200 dark:border-white/10 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-300 dark:text-gray-600 mb-4 shadow-sm">
                    <x-icon name="folder-open" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum evento agendado</h3>
                <p class="text-sm text-gray-500 max-w-sm">No momento não existem cultos ou eventos com repertório cadastrado para as próximas datas.</p>
            </div>
            @endforelse
        </div>

        @if($upcomingSetlists->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $upcomingSetlists->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
