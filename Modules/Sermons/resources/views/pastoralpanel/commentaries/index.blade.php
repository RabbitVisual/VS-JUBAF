@extends('pastoralpanel::components.layouts.master')

@section('title', 'Comentários Bíblicos - Administração')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Comentários Bíblicos</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Gerencie comentários versículo a versículo</p>
        </div>
        <a href="{{ route('pastor.sermoes.commentaries.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
            <x-icon name="plus" class="w-5 h-5" />
            Novo Comentário
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('pastor.sermoes.commentaries.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white"
                placeholder="Buscar por conteúdo...">

            <input type="text" name="book" value="{{ request('book') }}"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white"
                placeholder="Livro (Ex: Gênesis)">

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'book']))
                    <a href="{{ route('pastor.sermoes.commentaries.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Commentaries List -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Referência</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Conteúdo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Autor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($commentaries as $comment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($comment->cover_image)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $comment->cover_image) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                                <x-icon name="photograph" class="h-5 w-5 text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $comment->reference }}</div>
                                        @if($comment->is_official)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                Oficial
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit(strip_tags($comment->content), 100) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="h-6 w-6 rounded-full object-cover">
                                    <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $comment->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $comment->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                                    {{ $comment->status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}">
                                    {{ ucfirst($comment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('pastor.sermoes.commentaries.edit', $comment) }}"
                                        class="text-amber-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Editar</a>
                                    <form action="{{ route('pastor.sermoes.commentaries.destroy', $comment) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja deletar este comentário?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Deletar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4">
                                        <x-icon name="comments" class="w-10 h-10 text-amber-500" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum comentário ainda</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Cadastre comentários bíblicos para consulta. Crie o primeiro registro.</p>
                                    <a href="{{ route('pastor.sermoes.commentaries.create') }}"
                                        class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-xl transition-all">
                                        <x-icon name="plus" class="w-5 h-5 mr-2" />
                                        Novo comentário
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $commentaries->links() }}
        </div>
    </div>
</div>
@endsection

