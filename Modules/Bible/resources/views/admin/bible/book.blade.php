@extends('admin::components.layouts.master')

@section('title', $book->name . ' - ' . $version->name)

@section('content')
    <div class="p-6 space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <a href="{{ route('admin.bible.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Bíblia Digital</a>
            <x-icon name="chevron-right" style="duotone" class="w-4 h-4" />
            <a href="{{ route('admin.bible.show', $version) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $version->name }}</a>
            <x-icon name="chevron-right" style="duotone" class="w-4 h-4" />
            <span class="text-gray-900 dark:text-white font-medium">{{ $book->name }}</span>
        </nav>

        <!-- Header -->
        <div class="bg-linear-to-r from-blue-500 to-indigo-600 rounded-xl p-8 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <x-icon name="book-open" class="w-8 h-8" />
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold tracking-tight">{{ $book->name }}</h1>
                            <p class="text-blue-100 mt-1">{{ $version->name }} • {{ $book->testament == 'old' ? 'Antigo Testamento' : 'Novo Testamento' }}</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ $chapters->count() }}</div>
                    <div class="text-blue-100 text-sm">Capítulos</div>
                </div>
            </div>
        </div>

        <!-- Chapters Grid -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="file-lines" class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" />
                    Capítulos
                </h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $chapters->count() }} capítulos disponíveis</span>
            </div>

            @if($chapters->isEmpty())
                <div class="text-center py-12">
                    <x-icon name="file-lines" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhum capítulo encontrado</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Este livro ainda não possui capítulos importados.</p>
                </div>
            @else
                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-3">
                    @foreach($chapters as $chapter)
                        <a href="{{ route('admin.bible.chapter', ['version' => $version->id, 'book' => $book->id, 'chapter' => $chapter->id]) }}"
                            class="group relative flex flex-col items-center justify-center p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-linear-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-lg hover:scale-105 transition-all duration-200">
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <x-icon name="chevron-right" style="duotone" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                                {{ $chapter->chapter_number }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
                                {{ $chapter->verses_count }} vers.
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
            <a href="{{ route('admin.bible.show', $version) }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar para {{ $version->name }}
            </a>
        </div>
    </div>
@endsection

