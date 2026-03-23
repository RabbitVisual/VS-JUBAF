@extends('admin::components.layouts.master')

@section('title', 'Sermões - Administração')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sermões</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Gerencie os sermões e estudos bíblicos compartilhados</p>
        </div>
        <a href="{{ route('admin.sermons.sermons.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
            <x-icon name="plus" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
            Novo Sermão
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.sermons.sermons.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                placeholder="Buscar...">
            <select name="category_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Todas as categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Todos os status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
            </select>
            <select name="visibility" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Todas as visibilidades</option>
                <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Público</option>
                <option value="members" {{ request('visibility') === 'members' ? 'selected' : '' }}>Membros</option>
                <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Privado</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'category_id', 'status', 'visibility', 'tag_id']))
                    <a href="{{ route('admin.sermons.sermons.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sermons List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Autor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Visibilidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Visualizações</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sermons as $sermon)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($sermon->cover_image)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $sermon->cover_image) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                <x-icon name="photograph" class="h-5 w-5 text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $sermon->title }}</div>
                                        @if($sermon->subtitle)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($sermon->subtitle, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sermon->category)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" style="background-color: {{ $sermon->category->color ?? '#6B7280' }}20; color: {{ $sermon->category->color ?? '#6B7280' }}">
                                        {{ $sermon->category->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">Sem categoria</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $sermon->user->avatar_url }}" alt="{{ $sermon->user->name }}" class="h-6 w-6 rounded-full object-cover">
                                    <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $sermon->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $sermon->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                                    {{ $sermon->status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                                    {{ $sermon->status === 'archived' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                    {{ $sermon->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $sermon->visibility_display }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($sermon->views) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.sermons.sermons.show', $sermon) }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Ver</a>
                                    <a href="{{ route('admin.sermons.sermons.edit', $sermon) }}"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Editar</a>
                                    <form action="{{ route('admin.sermons.sermons.destroy', $sermon) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este sermão? Esta ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Deletar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4">
                                        <x-icon name="pen-fancy" class="w-10 h-10 text-amber-500" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum sermão ainda</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Comece seu próximo estudo. Crie o primeiro sermão e use o Sermon Studio para estruturar sua mensagem.</p>
                                    <a href="{{ route('admin.sermons.sermons.create') }}"
                                        class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-xl shadow-lg transition-all">
                                        <x-icon name="plus" class="w-5 h-5 mr-2" />
                                        Criar sermão
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $sermons->links() }}
        </div>
    </div>
</div>
@endsection

