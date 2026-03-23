@extends('memberpanel::components.layouts.master')

@section('title', 'Meus Sermões')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Meus Sermões</h1>
            <p class="text-gray-600 dark:text-gray-400">Gerencie e acompanhe seus sermões publicados.</p>
        </div>
        <a href="{{ route('memberpanel.sermons.create') }}"
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            Novo Sermão
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('memberpanel.sermons.my-sermons') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                 <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Buscar</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400"
                        placeholder="Título, tema...">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-icon name="search" class="w-5 h-5" />
                    </div>
                </div>
            </div>

            <div class="md:col-span-1">
                 <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</label>
                <div class="relative">
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                        <option value="">Todos</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                     <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </div>
                </div>
            </div>

            <div class="flex items-end gap-2 md:col-span-1">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 dark:bg-gray-600 text-white font-bold rounded-xl hover:bg-gray-800 dark:hover:bg-gray-500 transition-colors">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('memberpanel.sermons.my-sermons') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <x-icon name="x" class="w-5 h-5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sermons List -->
    @if($sermons->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($sermons as $sermon)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide
                                    {{ $sermon->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $sermon->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                    {{ $sermon->status === 'archived' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                    {{ $sermon->status_display }}
                                </span>

                                <span class="flex items-center gap-1 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    @if($sermon->visibility === 'public')
                                        <x-icon name="globe" class="w-3.5 h-3.5" /> Público
                                    @elseif($sermon->visibility === 'members')
                                        <x-icon name="users" class="w-3.5 h-3.5" /> Membros
                                    @else
                                        <x-icon name="lock-closed" class="w-3.5 h-3.5" /> Privado
                                    @endif
                                </span>

                                @if($sermon->is_featured)
                                    <span class="px-2 py-0.5 rounded-lg bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-bold uppercase tracking-wide flex items-center gap-1">
                                        <x-icon name="star" class="w-3 h-3" /> Destaque
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                {{ $sermon->title }}
                            </h3>

                             @if($sermon->subtitle)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-1">{{ $sermon->subtitle }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-6 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                                <span class="flex items-center gap-1.5" title="Visualizações">
                                    <x-icon name="eye" class="w-4 h-4" /> {{ number_format($sermon->views) }}
                                </span>
                                <span class="flex items-center gap-1.5" title="Curtidas">
                                     <x-icon name="thumb-up" class="w-4 h-4" /> {{ $sermon->likes }}
                                </span>
                                <span class="flex items-center gap-1.5" title="Comentários">
                                     <x-icon name="chat" class="w-4 h-4" /> {{ $sermon->comments_count }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                     <x-icon name="clock" class="w-4 h-4" /> {{ $sermon->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700 pt-4 md:pt-0 md:pl-6">
                            <a href="{{ route('memberpanel.sermons.show', $sermon) }}"
                                class="flex-1 md:flex-none px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-bold rounded-xl transition-colors text-center">
                                Ver
                            </a>

                            @if($sermon->user_id === auth()->id())
                                <a href="{{ route('admin.sermons.sermons.edit', $sermon) }}"
                                    class="flex-1 md:flex-none px-4 py-2 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-sm font-bold rounded-xl transition-colors text-center">
                                    Editar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $sermons->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm col-span-full">
            <div class="mx-auto w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-6">
                <x-icon name="microphone" class="w-12 h-12 text-blue-500" />
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Comece sua jornada</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed mb-8">
                Você ainda não publicou nenhum sermão. Compartilhe sua primeira mensagem com a comunidade hoje mesmo.
            </p>
            <a href="{{ route('memberpanel.sermons.create') }}"
                class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Criar Primeiro Sermão
            </a>
        </div>
    @endif
</div>
@endsection

