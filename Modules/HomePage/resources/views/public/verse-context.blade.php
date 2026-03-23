@extends('homepage::components.layouts.master')

@section('title', 'Contexto Bíblico - ' . $verse->chapter->book->name . ' ' . $verse->chapter->chapter_number . ':' . $verse->verse_number)

@section('meta')
    <meta property="og:title" content="Versículo Bíblico - {{ $verse->chapter->book->name }} {{ $verse->chapter->chapter_number }}:{{ $verse->verse_number }}">
    <meta property="og:description" content="{{ Str::limit($verse->text, 160) }}">
    <meta property="og:type" content="article">
@endsection

@section('content')
    <!-- Hero Section: Premium Book Header -->
    <section class="relative min-h-[45vh] flex items-center justify-center overflow-hidden bg-slate-950">
        <!-- Abstract Background Effects -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-[100px]"></div>
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/natural-paper.png')]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-12">
             <div class="inline-flex items-center px-4 py-2 bg-white/5 border border-white/10 text-blue-300 text-xs font-bold tracking-[0.2em] uppercase mb-8 backdrop-blur-md rounded-full shadow-2xl">
                <i class="fa-duotone fa-book-open-reader mr-2 text-sm"></i>
                Estudo Bíblico
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white mb-6 tracking-tight leading-tight">
                Contexto <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-indigo-400">Sagrado</span>
            </h1>
            <div class="flex items-center justify-center space-x-4 text-white/80">
                <div class="h-px w-8 bg-blue-500/50"></div>
                <p class="text-xl md:text-2xl font-serif italic tracking-wide">
                    {{ $verse->chapter->book->name }} {{ $verse->chapter->chapter_number }}
                </p>
                <div class="h-px w-8 bg-blue-500/50"></div>
            </div>
        </div>

        <!-- Wave transition -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0] transform rotate-180">
            <svg class="relative block w-[calc(100%+1.3px)] h-20 fill-gray-50 dark:fill-gray-950" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
            </svg>
        </div>
    </section>

    <!-- Reading Interaction Area -->
    <section class="pb-32 pt-12 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Floating Navigation (Context Specific) -->
            <div class="sticky top-24 z-30 mb-8 flex justify-center">
                <div class="inline-flex items-center p-1 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-full shadow-xl border border-gray-200 dark:border-gray-800">
                    <button onclick="scrollToTop()" class="p-3 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Voltar ao início">
                        <i class="fa-duotone fa-arrow-up-to-line text-lg font-bold"></i>
                    </button>
                    <div class="w-px h-6 bg-gray-200 dark:bg-gray-800 mx-1"></div>
                    <button onclick="copyVerseLink()" class="flex items-center space-x-2 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-all" id="copy-btn">
                        <i class="fa-duotone fa-link text-blue-500 mr-1"></i>
                        <span>Copiar Link</span>
                    </button>
                    <div class="w-px h-6 bg-gray-200 dark:bg-gray-800 mx-1"></div>
                    <div class="flex items-center px-2 space-x-1">
                        <button onclick="shareOnWhatsApp()" class="p-2.5 text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-full transition-colors">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                        </button>
                        <button onclick="shareOnFacebook()" class="p-2.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-full transition-colors">
                            <i class="fa-brands fa-facebook text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- The "Book" Reader Container -->
            <div class="relative bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden transform transition-all">

                <!-- Chapter Header -->
                <div class="px-8 md:px-16 pt-16 pb-12 text-center border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <span class="text-blue-600 dark:text-blue-400 font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">Capítulo {{ $verse->chapter->chapter_number }}</span>
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">{{ $verse->chapter->book->name }}</h2>
                    <div class="mt-6 flex items-center justify-center space-x-4">
                        <div class="h-[2px] w-12 bg-blue-500/20"></div>
                        <i class="fa-duotone fa-cross text-blue-500/40 text-sm"></i>
                        <div class="h-[2px] w-12 bg-blue-500/20"></div>
                    </div>
                </div>

                <!-- Bible Text Flow -->
                <div class="px-8 md:px-20 py-16 space-y-8 font-serif">
                    @foreach($contextVerses as $contextVerse)
                        @php
                            $isFeatured = $contextVerse->verse_number == $verse->verse_number;
                        @endphp

                        <div id="v-{{ $contextVerse->verse_number }}"
                             class="relative group animate-fade-in transition-all duration-500
                                    {{ $isFeatured ? 'bg-blue-50/50 dark:bg-blue-900/10 p-8 md:-mx-8 rounded-3xl border border-blue-100 dark:border-blue-900/30 shadow-sm' : '' }}">

                            @if($isFeatured)
                                <div class="absolute -left-3 top-8 md:-left-12 flex flex-col items-center">
                                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg transform scale-110">
                                        <i class="fa-duotone fa-star text-sm"></i>
                                    </div>
                                    <div class="w-px h-full bg-blue-200 dark:bg-blue-900/50 absolute top-10 -z-10"></div>
                                </div>
                            @endif

                            <div class="flex items-start gap-6">
                                <span class="text-sm font-black text-blue-500/30 group-hover:text-blue-500 transition-colors w-8 text-right shrink-0 mt-1 {{ $isFeatured ? 'text-blue-600/60 font-serif text-xl' : 'font-sans' }}">
                                    {{ $contextVerse->verse_number }}
                                </span>
                                <div class="relative">
                                    <p class="text-xl md:text-2xl leading-[1.8] tracking-wide text-gray-800 dark:text-gray-200
                                              {{ $isFeatured ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                                        {{ $contextVerse->text }}
                                    </p>
                                </div>
                            </div>

                            @if($isFeatured)
                                <div class="mt-8 pt-6 border-t border-blue-100/50 dark:border-blue-900/20 flex items-center text-xs font-bold text-blue-500/60 uppercase tracking-widest">
                                    <i class="fa-duotone fa-sparkles mr-2 font-bold"></i>
                                    Versículo em Destaque
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Footer Reader Actions -->
                <div class="px-8 md:px-16 py-12 bg-gray-50 dark:bg-gray-800/20 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400 italic text-sm font-serif">
                        <i class="fa-duotone fa-quote-left text-blue-500/40 text-xl font-bold"></i>
                        <span>Medita na palavra de dia e de noite.</span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3">
                        @php
                            $bookRel = $verse->chapter->book;
                            $versionAbbr = $bookRel->bibleVersion->abbreviation ?? 'ARA';
                        @endphp
                        <a href="{{ route('bible.public.chapter', [$versionAbbr, $bookRel->book_number, $verse->chapter->chapter_number]) }}"
                           class="group inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full text-sm font-bold shadow-md transition-all">
                            <i class="fa-duotone fa-book-open mr-2"></i>
                            Ler capítulo na Bíblia Online
                        </a>
                        <a href="{{ route('homepage.index') }}"
                           class="group inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-full text-sm font-bold shadow-sm hover:shadow-md transition-all hover:bg-blue-600 hover:text-white hover:border-blue-600">
                            <i class="fa-duotone fa-house-chimney mr-2 text-blue-500 group-hover:text-white transition-colors"></i>
                            Voltar ao Início
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation: Next/Prev Chapters -->
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 gap-6">
                @php
                    $prevChapter = \Modules\Bible\App\Models\Chapter::where('book_id', $verse->chapter->book->id)
                        ->where('chapter_number', '<', $verse->chapter->chapter_number)
                        ->orderBy('chapter_number', 'desc')
                        ->first();

                    $nextChapter = \Modules\Bible\App\Models\Chapter::where('book_id', $verse->chapter->book->id)
                        ->where('chapter_number', '>', $verse->chapter->chapter_number)
                        ->orderBy('chapter_number', 'asc')
                        ->first();
                @endphp

                @if($prevChapter)
                <a href="{{ route('verse.context', ['book_id' => $verse->chapter->book->id, 'chapter' => $prevChapter->chapter_number, 'verse' => 1]) }}"
                   class="group flex items-center p-6 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800 transition-all hover:-translate-x-2 hover:border-blue-500/30">
                    <div class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mr-4 group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-colors">
                        <i class="fa-duotone fa-caret-left text-blue-500 text-xl font-bold"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest block mb-1">Capítulo Anterior</span>
                        <span class="text-lg font-black text-gray-900 dark:text-white">Capítulo {{ $prevChapter->chapter_number }}</span>
                    </div>
                </a>
                @else
                <div class="p-6 bg-gray-100/50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800 flex items-center opacity-50">
                    <i class="fa-duotone fa-ban text-gray-400 mr-4 text-xl font-bold"></i>
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Início do Livro</span>
                </div>
                @endif

                @if($nextChapter)
                <a href="{{ route('verse.context', ['book_id' => $verse->chapter->book->id, 'chapter' => $nextChapter->chapter_number, 'verse' => 1]) }}"
                   class="group flex items-center p-6 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800 transition-all hover:translate-x-2 hover:border-blue-500/30 text-right">
                    <div class="flex-1 mr-4">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest block mb-1">Próximo Capítulo</span>
                        <span class="text-lg font-black text-gray-900 dark:text-white">Capítulo {{ $nextChapter->chapter_number }}</span>
                    </div>
                    <div class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-colors">
                        <i class="fa-duotone fa-caret-right text-blue-500 text-xl font-bold"></i>
                    </div>
                </a>
                @else
                <div class="p-6 bg-gray-100/50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800 flex items-center justify-end opacity-50">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-widest mr-4">Fim do Livro</span>
                    <i class="fa-duotone fa-flag-checkered text-gray-400 text-xl font-bold"></i>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Custom Animations for Reader -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent("{{ $verse->chapter->book->name }} {{ $verse->chapter->chapter_number }}:{{ $verse->verse_number }} - Igreja Batista Avenida");
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const text = encodeURIComponent("{{ $verse->chapter->book->name }} {{ $verse->chapter->chapter_number }}:{{ $verse->verse_number }}\n\n\"{{ $verse->text }}\"\n\nLeia mais: {{ route('verse.context', ['book_id' => $verse->chapter->book->id, 'chapter' => $verse->chapter->chapter_number, 'verse' => $verse->verse_number]) }}");
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        function copyVerseLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const button = document.getElementById('copy-btn');
                const span = button.querySelector('span');
                const originalText = span.innerText;

                span.innerText = 'Copiado!';
                button.classList.add('bg-green-600', 'text-white');
                button.classList.remove('text-gray-700', 'dark:text-gray-300');

                setTimeout(() => {
                    span.innerText = originalText;
                    button.classList.remove('bg-green-600', 'text-white');
                    button.classList.add('text-gray-700', 'dark:text-gray-300');
                }, 2000);
            });
        }

        // Smooth scroll to the featured verse on impact
        document.addEventListener('DOMContentLoaded', () => {
            const featured = document.getElementById('v-{{ $verse->verse_number }}');
            if (featured) {
                setTimeout(() => {
                    featured.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 1000);
            }
        });
    </script>
@endsection

