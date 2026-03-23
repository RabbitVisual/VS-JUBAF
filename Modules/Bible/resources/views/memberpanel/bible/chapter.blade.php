@extends('memberpanel::components.layouts.master')

@section('title', $book->name . ' ' . $chapter->chapter_number . ' - ' . $version->name)

@section('content')
    <div class="min-h-screen bg-white dark:bg-slate-950 transition-colors pb-20" id="bible-chapter" style="--bible-mobile-font-size: 1.25rem">

        <!-- Sticky Navigation Bar -->
        <nav class="sticky top-0 z-40 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 transition-colors" data-tour="bible-chapter-nav">
            <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
                <!-- Back to Book -->
                <a href="{{ route('memberpanel.bible.book', ['version' => $version->abbreviation, 'book' => $book->book_number]) }}"
                   class="flex items-center gap-2 text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-slate-800 flex items-center justify-center">
                        <x-icon name="chevron-left" class="w-4 h-4" />
                    </div>
                    <span class="hidden sm:block text-sm font-bold uppercase tracking-wide">Voltar</span>
                </a>

                <!-- Title / Version Select -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                        {{ $book->name }} <span class="text-indigo-600 dark:text-indigo-400">{{ $chapter->chapter_number }}</span>
                    </h1>
                    <div class="h-6 w-px bg-gray-200 dark:bg-slate-700"></div>
                     <div class="relative group">
                        <select onchange="changeVersion(this.value)"
                                class="appearance-none bg-transparent text-sm font-bold text-gray-600 dark:text-slate-300 pr-6 outline-none cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            @foreach (\Modules\Bible\App\Models\BibleVersion::active()->get() as $v)
                                <option value="{{ $v->abbreviation }}" {{ $v->id === $version->id ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                    {{ $v->abbreviation }}
                                </option>
                            @endforeach
                        </select>
                        <x-icon name="caret-down" class="w-3 h-3 absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                     <a href="{{ route('memberpanel.bible.interlinear', ['book' => $book->name, 'chapter' => $chapter->chapter_number]) }}"
                        class="hidden sm:inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                        <x-icon name="language" class="w-3 h-3 mr-1.5" />
                        Interlinear
                    </a>
                    <button type="button" id="bible-font-size-btn" class="sm:hidden p-2 text-gray-400 hover:text-indigo-600 dark:text-slate-500 dark:hover:text-indigo-400 touch-manipulation active:scale-95" title="Aumentar tamanho da fonte" aria-label="Aumentar tamanho da fonte">
                        <x-icon name="font" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </nav>

        <!-- Reading Area -->
        <main class="max-w-3xl mx-auto px-6 py-10">
            @if (!empty($chapterAudioUrl))
                <div class="mb-8 p-4 rounded-xl bg-gray-50 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800" aria-label="Ouvir capítulo em áudio">
                    <p class="text-sm font-bold text-gray-600 dark:text-slate-400 mb-3 flex items-center gap-2">
                        <x-icon name="volume-high" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        Áudio deste capítulo ({{ $version->name }})
                    </p>
                    <audio controls class="w-full max-w-md" preload="metadata" src="{{ $chapterAudioUrl }}">
                        Seu navegador não suporta o elemento de áudio.
                    </audio>
                </div>
            @endif
            @if ($verses->isEmpty())
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                        <x-icon name="triangle-exclamation" class="w-8 h-8 text-gray-300 dark:text-slate-700" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Capítulo Indisponível</h3>
                    <p class="text-gray-500 dark:text-slate-400">Este capítulo ainda não foi carregado nesta versão.</p>
                </div>
            @else
                <div class="space-y-6" data-tour="bible-verse">
                    @foreach ($verses as $verse)
                        <div class="flex items-start gap-3 sm:gap-5 group relative p-2 sm:p-3 -mx-2 sm:-mx-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-900/50 transition-colors duration-200" id="verse-{{ $verse->verse_number }}">

                            <!-- Verse Number -->
                            <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 font-bold text-xs select-none group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 transition-colors">
                                {{ $verse->verse_number }}
                            </span>

                            <!-- Text -->
                            <div class="flex-1">
                                <p class="bible-verse-text text-xl sm:text-2xl text-gray-800 dark:text-slate-200 font-serif leading-loose tracking-wide">
                                    {{ $verse->text }}
                                </p>
                            </div>

                            <!-- Actions (Only visible on hover/focus) -->
                            <div class="md:opacity-0 md:group-hover:opacity-100 flex flex-col gap-1 transition-opacity duration-200 absolute right-2 top-2 sm:static">
                                <button onclick="toggleFavorite({{ $verse->id }})"
                                    class="p-2 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors favorite-btn"
                                    data-verse-id="{{ $verse->id }}"
                                    title="Favoritar Versículo">
                                    <x-icon name="heart" class="w-5 h-5 transition-transform active:scale-95" />
                                </button>
                                <button onclick="shareVerse('{{ $verse->text }}', '{{ $book->name }} {{ $chapter->chapter_number }}:{{ $verse->verse_number }} ({{ $version->abbreviation }})')"
                                    class="p-2 rounded-lg text-gray-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors hidden sm:block"
                                    title="Compartilhar">
                                    <x-icon name="share-nodes" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>

        <!-- Bottom Navigation Controls -->
        <div class="fixed bottom-0 left-0 right-0 bg-white/90 dark:bg-slate-950/90 backdrop-blur-lg border-t border-gray-200 dark:border-slate-800 py-4 px-6 z-30">
             <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                <!-- Previous -->
                <div class="flex-1">
                    @if ($previousChapter)
                        <a href="{{ route('memberpanel.bible.chapter', ['version' => $version->abbreviation, 'book' => $previousChapter->book->book_number, 'chapter' => $previousChapter->chapter_number]) }}"
                            class="flex items-center gap-3 text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition-colors group">
                             <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 flex items-center justify-center transition-colors">
                                 <x-icon name="chevron-left" class="w-5 h-5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                             </div>
                             <div class="hidden sm:block text-left">
                                 <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Anterior</p>
                                 <p class="text-sm font-bold">Cap. {{ $previousChapter->chapter_number }}</p>
                             </div>
                        </a>
                    @else
                        <span class="w-10 h-10 block"></span>
                    @endif
                </div>

                <!-- Current Indicator (Mobile) -->
                <div class="sm:hidden text-center">
                    <span class="text-xs font-black uppercase tracking-widest text-indigo-500">{{ $book->abbreviation }} {{ $chapter->chapter_number }}</span>
                </div>

                <!-- Next -->
                 <div class="flex-1 flex justify-end">
                    @if ($nextChapter)
                        <a href="{{ route('memberpanel.bible.chapter', ['version' => $version->abbreviation, 'book' => $nextChapter->book->book_number, 'chapter' => $nextChapter->chapter_number]) }}"
                            class="flex items-center gap-3 text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition-colors group text-right">
                             <div class="hidden sm:block">
                                 <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Próximo</p>
                                 <p class="text-sm font-bold">Cap. {{ $nextChapter->chapter_number }}</p>
                             </div>
                             <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 flex items-center justify-center transition-colors">
                                 <x-icon name="chevron-right" class="w-5 h-5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                             </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <style>
        /* Mobile: tamanho da fonte controlado pelo botão */
        @media (max-width: 639px) {
            #bible-chapter .bible-verse-text {
                font-size: var(--bible-mobile-font-size) !important;
            }
        }
    </style>

    <script>
        // Tamanho da fonte no mobile (ciclo ao clicar no botão)
        (function() {
            var SIZES = ['1rem', '1.25rem', '1.5rem', '1.75rem', '2rem'];
            var STORAGE_KEY = 'bible_mobile_font_size_index';

            function applySize(index) {
                var el = document.getElementById('bible-chapter');
                if (!el) return;
                var size = SIZES[index];
                el.style.setProperty('--bible-mobile-font-size', size);
                try { localStorage.setItem(STORAGE_KEY, String(index)); } catch (e) {}
            }

            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('bible-font-size-btn');
                if (!btn) return;
                var index = 1;
                try { index = Math.min(Math.max(0, parseInt(localStorage.getItem(STORAGE_KEY), 10) || 1), SIZES.length - 1); } catch (e) {}
                applySize(index);

                btn.addEventListener('click', function() {
                    index = (index + 1) % SIZES.length;
                    applySize(index);
                });
            });
        })();

        // Initialize Toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 px-6 py-3 rounded-xl shadow-2xl backdrop-blur-md text-sm font-bold transition-all duration-300 transform translate-y-10 opacity-0 z-50 flex items-center gap-3 ${type === 'success' ? 'bg-gray-900/90 text-white dark:bg-white/90 dark:text-gray-900' : 'bg-red-500/90 text-white'}`;

            // Icon
            const icon = type === 'success'
                ? '<svg class="w-5 h-5 text-green-400 dark:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

            toast.innerHTML = `${icon}<span>${message}</span>`;
            document.body.appendChild(toast);

            // Animate In
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            });

            // Animate Out
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function changeVersion(abbreviation) {
            window.location.href =
                '{{ route('memberpanel.bible.chapter', ['version' => ':version', 'book' => $book->book_number, 'chapter' => $chapter->chapter_number]) }}'
                .replace(':version', abbreviation);
        }

        function toggleFavorite(verseId) {
            const btn = document.querySelector(`[data-verse-id="${verseId}"]`);
            const isFavorite = btn.classList.contains('text-red-500');

            fetch(`{{ url('/social/bible/favorites') }}/${verseId}`, {
                        method: isFavorite ? 'DELETE' : 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                    })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (!isFavorite) {
                            btn.classList.remove('text-gray-300');
                            btn.classList.add('text-red-500');
                            showToast('Versículo favoritado!');
                        } else {
                            btn.classList.add('text-gray-300');
                            btn.classList.remove('text-red-500');
                             showToast('Removido dos favoritos.');
                        }
                    } else {
                         // Revert state if error
                         showToast('Erro ao atualizar favorito.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Failed to toggle favorite:', error);
                    showToast('Erro de conexão.', 'error');
                });
        }

        function shareVerse(text, reference) {
            const shareText = `"${text}" - ${reference}`;

            // Check for Mobile Share API support
            if (navigator.share) {
                navigator.share({
                    title: 'Versículo Bíblico',
                    text: shareText,
                    url: window.location.href,
                })
                .catch((error) => console.log('Error sharing', error));
            } else {
                // Fallback to Clipboard
                navigator.clipboard.writeText(`${shareText}\n${window.location.href}`).then(() => {
                    showToast('Versículo copiado para compartilhar!');
                }, () => {
                    showToast('Erro ao copiar.', 'error');
                });
            }
        }

        // Load favorites on page load
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $favoriteIds = Auth::user()->bibleFavorites()->pluck('verse_id')->toArray();
            @endphp
            const favorites = @json($favoriteIds);
            favorites.forEach(id => {
                const btn = document.querySelector(`[data-verse-id="${id}"]`);
                if (btn) {
                    btn.classList.remove('text-gray-300');
                    btn.classList.add('text-red-500');
                }
            });
        });

        // Load favorites on page load
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $favoriteIds = Auth::user()->bibleFavorites()->pluck('verse_id')->toArray();
            @endphp
            const favorites = @json($favoriteIds);
            favorites.forEach(id => {
                const btn = document.querySelector(`[data-verse-id="${id}"]`);
                if (btn) {
                    btn.classList.remove('text-gray-300');
                    btn.classList.add('text-red-500');
                }
            });
        });
    </script>
@endsection

