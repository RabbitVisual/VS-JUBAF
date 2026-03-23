@extends('memberpanel::components.layouts.master')

@section('title', 'Versículos Favoritos')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors pb-12">

        <!-- Header -->
        <div class="sticky top-0 z-30 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-gray-200 dark:border-slate-800 transition-colors">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-500">
                        <x-icon name="heart" class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">Meus Favoritos</h1>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mt-1">Versículos Salvos</p>
                    </div>
                </div>
                <a href="{{ route('memberpanel.bible.read', \Modules\Bible\App\Models\BibleVersion::first()->abbreviation ?? 'NVI') }}"
                   class="inline-flex items-center px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-800 transition-colors uppercase tracking-widest">
                    <x-icon name="arrow-left" class="w-3 h-3 mr-2" />
                    Voltar
                </a>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 mt-8 space-y-6">

            <!-- Stats Card -->
            @if($favorites->count() > 0)
                <div class="bg-linear-to-r from-red-500 to-pink-600 rounded-3xl p-6 text-white shadow-xl shadow-red-500/20 flex items-center justify-between relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('/img/pattern.png')] opacity-10"></div>
                     <div class="relative z-10">
                        <h2 class="text-lg font-bold">Total Salvo</h2>
                        <p class="text-red-100 text-sm">Versículos que tocaram seu coração.</p>
                    </div>
                    <div class="relative z-10 bg-white/20 backdrop-blur-sm px-6 py-3 rounded-2xl">
                        <span class="text-3xl font-black">{{ $favorites->count() }}</span>
                    </div>
                </div>
            @endif

            <!-- List -->
            <div class="space-y-4">
                @forelse($favorites as $verse)
                    @php
                        $chapter = $verse->chapter;
                        $book = $chapter->book;
                        $version = $book->bibleVersion;
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-900/50 transition-all duration-300 group relative">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <a href="{{ route('memberpanel.bible.chapter', ['version' => $version->abbreviation, 'book' => $book->book_number, 'chapter' => $chapter->chapter_number]) }}#verse-{{ $verse->verse_number }}"
                               class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 transition-colors">
                                <x-icon name="bookmark" class="w-3 h-3" />
                                {{ $verse->full_reference }}
                            </a>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-gray-50 dark:bg-slate-800 text-gray-400 dark:text-slate-500 border border-gray-100 dark:border-slate-700">
                                {{ $version->abbreviation }}
                            </span>
                        </div>

                        <!-- Content -->
                        <p class="text-lg text-gray-800 dark:text-slate-200 font-serif leading-relaxed mb-4">
                            "{{ $verse->text }}"
                        </p>

                        <!-- Actions -->
                        <div class="flex justify-end pt-4 border-t border-gray-50 dark:border-slate-800/50">
                            <button onclick="removeFavorite({{ $verse->id }})"
                                class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-wider group/btn">
                                <span class="group-hover/btn:hidden">Remover</span>
                                <span class="hidden group-hover/btn:inline">Confirmar Remoção?</span>
                                <x-icon name="trash" class="w-3 h-3 ml-2" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-24">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                            <x-icon name="heart-crack" class="w-10 h-10 text-gray-300 dark:text-slate-700" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum Favorito</h3>
                        <p class="text-gray-500 dark:text-slate-400 max-w-sm mx-auto mb-8">
                            Você ainda não salvou nenhum versículo. Enquanto lê a Bíblia, clique no ícone de coração para salvar aqui.
                        </p>
                        <a href="{{ route('memberpanel.bible.index') }}"
                            class="inline-flex items-center px-6 py-3 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all">
                            Começar Leitura
                        </a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <script>
        function removeFavorite(verseId) {
            const button = event.target.closest('button');

            // Simple visual confirmation before fetch (optimistic UI could be used, but safety first here)
            // The button text changes on hover to "Confirmar Remoção?", acting as a soft confirmation.
            // But let's add a real confirm for safety.
            if(!confirm('Tem certeza que deseja remover este versículo dos favoritos?')) return;

            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            fetch(`{{ url('/social/bible/favorites') }}/${verseId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animate removal
                    const card = button.closest('.relative');
                    card.style.transform = 'scale(0.95)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        location.reload(); // Reload to refresh count/layout cleanly
                    }, 200);
                } else {
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    alert('Erro ao remover. Tente novamente.');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                console.error(error);
            });
        }
    </script>
@endsection

