@extends('admin::components.layouts.master')

@section('page-title', 'Categorias de Oração')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Categorias de Intercessão</h1>
            <p class="text-gray-600 dark:text-gray-400">Gerencie as categorias para organizar os pedidos de oração.</p>
        </div>
        <a href="{{ route('admin.intercessor.categories.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            <span>Nova Categoria</span>
        </a>
    </div>

    @if($categories->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-xl flex items-center justify-center text-white font-bold shadow-sm" style="background-color: {{ $category->color }}">
                                            {{ substr($category->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $category->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $category->requests_count ?? 0 }} pedidos associados
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    {{ $category->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $category->is_active ? 'bg-green-600' : 'bg-gray-600' }}"></span>
                                        {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('admin.intercessor.categories.edit', $category) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Editar">
                                            <x-icon name="pencil" class="w-5 h-5" />
                                        </a>
                                        <form action="{{ route('admin.intercessor.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir esta categoria?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Excluir">
                                                <x-icon name="trash" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
            <div class="w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-6">
                <x-icon name="tag" class="w-12 h-12 text-blue-400 dark:text-blue-500" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma categoria cadastrada</h3>
            <p class="text-gray-600 dark:text-gray-400 text-center max-w-md mb-8">Crie categorias para organizar os pedidos por temas como Família, Saúde ou Gratidão.</p>
            <a href="{{ route('admin.intercessor.categories.create') }}" class="inline-flex items-center px-8 py-4 text-base font-bold text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                <x-icon name="plus" class="w-6 h-6 mr-2" />
                Criar Minha Primeira Categoria
            </a>
        </div>
    @endif
</div>
@endsection

