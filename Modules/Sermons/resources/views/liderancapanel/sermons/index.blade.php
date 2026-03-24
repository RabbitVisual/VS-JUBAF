@extends('liderancapanel::components.layouts.master')

@section('title', 'Sermões')

@section('content')
    <div class="space-y-6">
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30">
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
            <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span
                        class="px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-200 text-xs font-bold uppercase tracking-wider">Estúdio
                        da Palavra</span>
                    <h1 class="text-2xl md:text-3xl font-bold mt-2">Sermões</h1>
                    <p class="text-slate-300 mt-1">Gerencie os sermões e estudos bíblicos compartilhados</p>
                </div>
                <a href="{{ route('lideranca.sermoes.sermons.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors shrink-0">
                    <x-icon name="plus" class="w-5 h-5" />
                    Novo Sermão
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
            <form method="GET" action="{{ route('lideranca.sermoes.sermons.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..."
                    class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                <select name="category_id"
                    class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                    <option value="">Todas as categorias</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="status"
                    class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                    <option value="">Todos os status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
                </select>
                <select name="visibility"
                    class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                    <option value="">Todas as visibilidades</option>
                    <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Público</option>
                    <option value="members" {{ request('visibility') === 'members' ? 'selected' : '' }}>Membros</option>
                    <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Privado</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium">Filtrar</button>
                    @if (request()->hasAny(['search', 'category_id', 'status', 'visibility', 'tag_id']))
                        <a href="{{ route('lideranca.sermoes.sermons.index') }}"
                            class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($sermons->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($sermons as $sermon)
                    <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 hover:shadow-xl hover:border-amber-500/30 transition-all duration-300 hover:-translate-y-1 flex flex-col h-full relative overflow-hidden">
                        <!-- Cover Image -->
                        <div class="h-40 w-full overflow-hidden relative border-b border-gray-100 dark:border-slate-700">
                            @if($sermon->cover_image)
                                <img src="{{ asset('storage/' . $sermon->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center">
                                     <x-icon name="photograph" class="w-10 h-10 text-gray-300 dark:text-slate-600" />
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3 flex gap-2 shadow-lg">
                                <span class="px-2 py-1 text-[10px] font-black rounded-lg uppercase tracking-wide
                                    {{ $sermon->status === 'published' ? 'bg-green-500 text-white' : '' }}
                                    {{ $sermon->status === 'draft' ? 'bg-yellow-500 text-white' : '' }}
                                    {{ $sermon->status === 'archived' ? 'bg-gray-500 text-white' : '' }}">
                                    {{ $sermon->status_display }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex flex-col h-full">
                            <!-- Category Badge -->
                            <div class="flex items-center gap-2 mb-3">
                                @if($sermon->category)
                                    <span class="px-2 py-0.5 text-[9px] font-black rounded-lg uppercase tracking-widest"
                                          style="background-color: {{ $sermon->category->color ?? '#F59E0B' }}15; color: {{ $sermon->category->color ?? '#F59E0B' }}">
                                        {{ $sermon->category->name }}
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 text-[9px] font-black rounded-lg uppercase tracking-widest bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-300">
                                    {{ $sermon->visibility_display }}
                                </span>
                            </div>

                            <!-- Title & Subtitle -->
                            <div class="mb-3 flex-1">
                                <h3 class="text-base font-black text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors line-clamp-2 mb-1">
                                    {{ $sermon->title }}
                                </h3>
                                @if($sermon->subtitle)
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 line-clamp-2 italic">{{ $sermon->subtitle }}</p>
                                @endif
                            </div>

                            <!-- Author & Date -->
                            <div class="pt-3 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $sermon->user->avatar_url }}" alt="{{ $sermon->user->name }}" class="w-6 h-6 rounded-full object-cover border border-gray-200 dark:border-slate-600">
                                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide truncate max-w-[100px]">
                                        {{ Str::limit($sermon->user->name, 12) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 text-[10px] font-bold text-gray-400">
                                     <x-icon name="calendar" class="w-3.5 h-3.5" />
                                     {{ $sermon->created_at->format('d/m/y') }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between gap-2 mt-auto">
                                <a href="{{ route('lideranca.sermoes.sermons.show', $sermon) }}" class="flex-1 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-500 text-xs font-bold text-center hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors border border-amber-100 dark:border-amber-900/30">
                                    Acessar
                                </a>
                                <a href="{{ route('lideranca.sermoes.sermons.edit', $sermon) }}" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors border border-gray-200 dark:border-slate-600">
                                    <x-icon name="pen" class="w-3.5 h-3.5" />
                                </a>
                                <form action="{{ route('lideranca.sermoes.sermons.destroy', $sermon) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors border border-red-100 dark:border-red-900/30">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $sermons->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-12 text-center w-full">
                <div class="w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="pen-fancy" class="w-10 h-10 text-amber-500 dark:text-amber-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum sermão encontrado</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Comece seu próximo estudo bíblico agora mesmo.</p>
                <a href="{{ route('lideranca.sermoes.sermons.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="plus" class="w-5 h-5" />
                    Criar sermão
                </a>
            </div>
        @endif
    </div>
@endsection
