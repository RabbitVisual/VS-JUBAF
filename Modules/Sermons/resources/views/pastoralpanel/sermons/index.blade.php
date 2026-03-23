@extends('pastoralpanel::components.layouts.master')

@section('title', 'Sermões')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30">
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
        <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-200 text-xs font-bold uppercase tracking-wider">Estúdio da Palavra</span>
                <h1 class="text-2xl md:text-3xl font-bold mt-2">Sermões</h1>
                <p class="text-slate-300 mt-1">Gerencie os sermões e estudos bíblicos compartilhados</p>
            </div>
            <a href="{{ route('pastor.sermoes.sermons.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors shrink-0">
                <x-icon name="plus" class="w-5 h-5" />
                Novo Sermão
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('pastor.sermoes.sermons.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..."
                class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
            <select name="category_id" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                <option value="">Todas as categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                <option value="">Todos os status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
            </select>
            <select name="visibility" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-3 py-2">
                <option value="">Todas as visibilidades</option>
                <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Público</option>
                <option value="members" {{ request('visibility') === 'members' ? 'selected' : '' }}>Membros</option>
                <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Privado</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium">Filtrar</button>
                @if (request()->hasAny(['search', 'category_id', 'status', 'visibility', 'tag_id']))
                    <a href="{{ route('pastor.sermoes.sermons.index') }}" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600">Limpar</a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Autor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visibilidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visualizações</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($sermons as $sermon)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                        @if($sermon->cover_image)
                                            <img class="h-10 w-10 object-cover" src="{{ asset('storage/' . $sermon->cover_image) }}" alt="">
                                        @else
                                            <x-icon name="photograph" class="h-5 w-5 text-gray-400" />
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $sermon->title }}</div>
                                        @if($sermon->subtitle)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($sermon->subtitle, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sermon->category)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" style="background-color: {{ $sermon->category->color ?? '#6B7280' }}20; color: {{ $sermon->category->color ?? '#6B7280' }}">{{ $sermon->category->name }}</span>
                                @else
                                    <span class="text-sm text-gray-400">Sem categoria</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $sermon->user->avatar_url }}" alt="" class="h-6 w-6 rounded-full object-cover">
                                    <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $sermon->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $sermon->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $sermon->status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $sermon->status === 'archived' ? 'bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300' : '' }}">
                                    {{ $sermon->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sermon->visibility_display }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($sermon->views) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('pastor.sermoes.sermons.show', $sermon) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium">Ver</a>
                                    <a href="{{ route('pastor.sermoes.sermons.edit', $sermon) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium">Editar</a>
                                    <form action="{{ route('pastor.sermoes.sermons.destroy', $sermon) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este sermão?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline font-medium">Deletar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                                        <x-icon name="pen-fancy" class="w-10 h-10 text-amber-500 dark:text-amber-400" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum sermão ainda</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Comece seu próximo estudo. Crie o primeiro sermão e use o Sermon Studio para estruturar sua mensagem.</p>
                                    <a href="{{ route('pastor.sermoes.sermons.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                                        <x-icon name="plus" class="w-5 h-5" />
                                        Criar sermão
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $sermons->links() }}
        </div>
    </div>
</div>
@endsection
