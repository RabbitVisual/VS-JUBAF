@extends('admin::components.layouts.master')

@section('title', 'Visualizar Versão: ' . $version->name)

@section('content')
    <div class="p-6 space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
            <a href="{{ route('admin.bible.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Bíblia Digital</a>
            <x-icon name="chevron-right" style="duotone" class="w-4 h-4" />
            <span class="text-gray-900 dark:text-white font-medium">{{ $version->name }}</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-linear-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <x-icon name="book-open" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $version->name }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $version->abbreviation }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $version->language }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.bible.edit', $version) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.bible.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-linear-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <x-icon name="book-open" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">{{ $version->total_books }}</div>
                <div class="text-blue-100 text-sm font-medium">Livros</div>
            </div>

            <div class="bg-linear-to-br from-indigo-500 to-indigo-600 rounded-xl p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <x-icon name="file-lines" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($version->total_chapters) }}</div>
                <div class="text-indigo-100 text-sm font-medium">Capítulos</div>
            </div>

            <div class="bg-linear-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <x-icon name="file-lines" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($version->total_verses) }}</div>
                <div class="text-purple-100 text-sm font-medium">Versículos</div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-icon name="circle-info" style="duotone" class="w-5 h-5 mr-2 text-gray-400" />
                Informações da Versão
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $version->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $version->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                        @if($version->is_default)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-linear-to-r from-yellow-400 to-yellow-500 text-yellow-900">
                                Padrão
                            </span>
                        @endif
                    </div>
                </div>
                @if($version->imported_at)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Importado em:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $version->imported_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Books Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-linear-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="book-open" class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" />
                    Livros da Bíblia
                </h2>
            </div>

            @if($books->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="book-open" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhum livro importado</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Esta versão da Bíblia ainda não possui livros importados.</p>
                    <a href="{{ route('admin.bible.import') }}"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        <x-icon name="plus" class="w-5 h-5 mr-2" />
                        Importar Bíblia
                    </a>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-6 p-6">
                    <!-- Old Testament -->
                    <div>
                        <div class="flex items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="w-1 h-8 bg-linear-to-b from-amber-400 to-amber-600 rounded-full mr-3"></div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Antigo Testamento</h3>
                            <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">{{ $oldTestament->count() }} livros</span>
                        </div>
                        <div class="space-y-1 max-h-[600px] overflow-y-auto pr-2">
                            @forelse($oldTestament as $book)
                                <a href="{{ route('admin.bible.book', ['version' => $version->id, 'book' => $book->id]) }}"
                                    class="group flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-all duration-200 border border-transparent hover:border-blue-200 dark:hover:border-gray-600">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-linear-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ $book->book_number }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $book->name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $book->chapters_count }} cap.</span>
                                        <span>•</span>
                                        <span>{{ number_format($book->verses_count) }} vers.</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Nenhum livro do Antigo Testamento</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- New Testament -->
                    <div>
                        <div class="flex items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="w-1 h-8 bg-linear-to-b from-green-400 to-green-600 rounded-full mr-3"></div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Novo Testamento</h3>
                            <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">{{ $newTestament->count() }} livros</span>
                        </div>
                        <div class="space-y-1 max-h-[600px] overflow-y-auto pr-2">
                            @forelse($newTestament as $book)
                                <a href="{{ route('admin.bible.book', ['version' => $version->id, 'book' => $book->id]) }}"
                                    class="group flex items-center justify-between p-3 rounded-lg hover:bg-green-50 dark:hover:bg-gray-700 transition-all duration-200 border border-transparent hover:border-green-200 dark:hover:border-gray-600">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-linear-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ $book->book_number }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">{{ $book->name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $book->chapters_count }} cap.</span>
                                        <span>•</span>
                                        <span>{{ number_format($book->verses_count) }} vers.</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Nenhum livro do Novo Testamento</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

