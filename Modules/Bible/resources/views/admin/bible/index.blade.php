@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Bíblia Digital')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gerenciar Bíblia Digital</h1>
                <p class="text-gray-600 dark:text-gray-400">Gerencie e administre todas as versões da Bíblia disponíveis no
                    sistema</p>
            </div>
            <a href="{{ route('admin.bible.import') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                <span>Importar Nova Versão</span>
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:text-green-300 dark:border-green-800 flex items-center animate-fade-in"
                role="alert">
                <x-icon name="circle-check" class="w-5 h-5 mr-3 flex-shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Versions Grid -->
        @if ($versions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($versions as $version)
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 overflow-hidden">
                        <!-- Card Header -->
                        <div
                            class="p-6 bg-linear-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $version->name }}
                                    </h3>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $version->abbreviation }}
                                        </span>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $version->language }}</span>
                                        @if ($version->is_default)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-linear-to-r from-yellow-400 to-yellow-500 text-yellow-900 shadow-sm">
                                                <x-icon name="star" class="w-3 h-3 mr-1" />
                                                Padrão
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $version->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $version->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                            @if ($version->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $version->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Statistics -->
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $version->books_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Livros</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($version->total_chapters) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Capítulos</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($version->total_verses) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Versículos</div>
                                </div>
                            </div>

                            @if ($version->imported_at)
                                <div
                                    class="flex items-center text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <x-icon name="clock" style="duotone" class="w-4 h-4 mr-2" />
                                    Importado em {{ $version->imported_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.bible.show', $version) }}"
                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 transition-colors"
                                    title="Visualizar">
                                    <x-icon name="eye" style="duotone" class="w-4 h-4 mr-1.5" />
                                    Ver
                                </a>
                                <a href="{{ route('admin.bible.edit', $version) }}"
                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50 transition-colors"
                                    title="Editar">
                                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-1.5" />
                                    Editar
                                </a>
                            </div>
                            <form action="{{ route('admin.bible.destroy', $version) }}" method="POST" class="inline"
                                onsubmit="return confirm('Tem certeza que deseja excluir esta versão? Todos os dados serão perdidos.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition-colors"
                                    title="Excluir">
                                    <x-icon name="trash-can" style="duotone" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div
                class="flex flex-col items-center justify-center py-16 px-4 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <x-icon name="book-open" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nenhuma versão cadastrada</h3>
                <p class="text-gray-600 dark:text-gray-400 text-center max-w-md mb-6">Comece importando uma versão da
                    Bíblia para disponibilizar aos membros do sistema.</p>
                <a href="{{ route('admin.bible.import') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Importar Primeira Versão
                </a>
            </div>
        @endif
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endsection

