@extends('admin::components.layouts.master')

@section('title', $book->name . ' ' . $chapter->chapter_number . ' - ' . $version->name)

@section('content')
    <div class="p-6 max-w-6xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <a href="{{ route('admin.bible.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Bíblia Digital</a>
            <x-icon name="chevron-right" style="duotone" class="w-4 h-4" />
            <a href="{{ route('admin.bible.show', $version) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $version->name }}</a>
            <x-icon name="chevron-right" style="duotone" class="w-4 h-4" />
            <a href="{{ route('admin.bible.book', ['version' => $version->id, 'book' => $book->id]) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $book->name }}</a>
            <x-icon name="chevron-right" style="duotone" class="w-4 h-4" />
            <span class="text-gray-900 dark:text-white font-medium">Capítulo {{ $chapter->chapter_number }}</span>
        </nav>

        <!-- Header Card -->
        <div class="bg-linear-to-r from-blue-500 via-indigo-500 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight mb-2">{{ $book->name }} {{ $chapter->chapter_number }}</h1>
                    <div class="flex items-center gap-3 text-blue-100">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                            {{ $version->abbreviation }}
                        </span>
                        <span>•</span>
                        <span>{{ $verses->count() }} versículos</span>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center gap-2">
                    @if($previousChapter)
                        <a href="{{ route('admin.bible.chapter', ['version' => $version->id, 'book' => $previousChapter->book_id, 'chapter' => $previousChapter->id]) }}"
                            class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg transition-all duration-200 font-medium">
                            <x-icon name="chevron-left" style="duotone" class="w-5 h-5 mr-2" />
                            Anterior
                        </a>
                    @endif

                    @if($nextChapter)
                        <a href="{{ route('admin.bible.chapter', ['version' => $version->id, 'book' => $nextChapter->book_id, 'chapter' => $nextChapter->id]) }}"
                            class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg transition-all duration-200 font-medium">
                            Próximo
                            <x-icon name="chevron-right" style="duotone" class="w-5 h-5 ml-2" />
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reading Content -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($verses->isEmpty())
                <div class="text-center py-16 px-6">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="book-open" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nenhum versículo encontrado</h3>
                    <p class="text-gray-600 dark:text-gray-400">Este capítulo ainda não possui versículos importados.</p>
                </div>
            @else
                <div class="p-8 md:p-12 lg:p-16">
                    <div class="max-w-4xl mx-auto space-y-6">
                        @foreach($verses as $verse)
                            <div class="group flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                <span class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-linear-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm shadow-md group-hover:scale-110 transition-transform duration-200">
                                    {{ $verse->verse_number }}
                                </span>
                                <p class="flex-1 text-gray-900 dark:text-white text-lg leading-relaxed font-serif" style="line-height: 1.9;">
                                    {{ $verse->text }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Bottom Navigation -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.bible.book', ['version' => $version->id, 'book' => $book->id]) }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar para {{ $book->name }}
            </a>

            <div class="flex items-center gap-3">
                @if($previousChapter)
                    <a href="{{ route('admin.bible.chapter', ['version' => $version->id, 'book' => $previousChapter->book_id, 'chapter' => $previousChapter->id]) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <x-icon name="chevron-left" style="duotone" class="w-4 h-4 mr-1" />
                        Cap. {{ $previousChapter->chapter_number }}
                    </a>
                @endif

                @if($nextChapter)
                    <a href="{{ route('admin.bible.chapter', ['version' => $version->id, 'book' => $nextChapter->book_id, 'chapter' => $nextChapter->id]) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Cap. {{ $nextChapter->chapter_number }}
                        <x-icon name="chevron-right" style="duotone" class="w-4 h-4 ml-1" />
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

