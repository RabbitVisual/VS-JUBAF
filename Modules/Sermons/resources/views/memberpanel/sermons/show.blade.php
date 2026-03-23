@extends('memberpanel::components.layouts.master')

@section('title', $sermon->title)

@push('styles')
<style>
    /* Typography Customization for "Medium" feel */
    .prose-sermon {
        font-family: 'Merriweather', 'Georgia', serif;
        font-size: 1.125rem;
        line-height: 1.8;
        color: #292929;
    }
    .dark .prose-sermon {
        color: #d1d5db;
    }
    .prose-sermon h2, .prose-sermon h3 {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        margin-top: 2em;
        margin-bottom: 0.5em;
        color: #111827;
    }
    .dark .prose-sermon h2, .dark .prose-sermon h3 {
        color: #f3f4f6;
    }
    .prose-sermon blockquote {
        border-left: 4px solid #f59e0b; /* Amber */
        padding-left: 1.5rem;
        font-style: italic;
        background: #fffbeb; /* Amber-50 */
        padding: 1rem;
        border-radius: 0.5rem;
        margin: 2rem 0;
    }
    .dark .prose-sermon blockquote {
        background: rgba(245, 158, 11, 0.1);
        color: #d1d5db;
    }

    /* Tooltip */
    .bible-tooltip {
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        max-width: 300px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
        display: none;
        border: 1px solid #374151;
    }

    .bible-ref-link {
        color: #f59e0b;
        text-decoration: none;
        border-bottom: 1px dotted #f59e0b;
        cursor: pointer;
        font-weight: 600;
    }
    .bible-ref-link:hover {
        background: rgba(245, 158, 11, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white dark:bg-slate-950 pb-32">
    <!-- Navbar Placeholder (if not in master layout) -->

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <!-- Header -->
        <header class="mb-10 text-center">
            <div class="mb-6">
                @if($sermon->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500">
                        {{ $sermon->category->name }}
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-4">
                {{ $sermon->title }}
            </h1>

            @if($sermon->subtitle)
                <p class="text-xl text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                    {{ $sermon->subtitle }}
                </p>
            @endif

            <!-- Meta/Author -->
            <div class="mt-8 flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden">
                        @if($sermon->user->profile_photo_url)
                            <img src="{{ $sermon->user->profile_photo_url }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="user" class="w-6 h-6 text-gray-400 m-2" />
                        @endif
                    </div>
                    <div class="ml-3 text-left">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $sermon->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sermon->published_at?->format('d M, Y') }} • {{ ceil(str_word_count(strip_tags($sermon->full_content ?? $sermon->development)) / 200) }} min leitura</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Cover Image -->
        @if($sermon->cover_image)
            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl">
                <img src="{{ asset('storage/' . $sermon->cover_image) }}" alt="{{ $sermon->title }}" class="w-full h-auto object-cover max-h-[500px]">
            </div>
        @endif

        <!-- Content -->
        <article class="prose-sermon relative text-base min-w-0 overflow-x-hidden sermon-content-with-refs" id="sermon-content">
            @if($sermon->full_content)
                {!! $sermon->full_content !!}
            @else
                <!-- Legacy Fallback -->
                @if($sermon->introduction)
                    <h3>Introdução</h3>
                    {!! nl2br(e($sermon->introduction)) !!}
                @endif

                @if($sermon->development)
                    <h3>Desenvolvimento</h3>
                    {!! nl2br(e($sermon->development)) !!}
                @endif

                @if($sermon->conclusion)
                    <h3>Conclusão</h3>
                    {!! nl2br(e($sermon->conclusion)) !!}
                @endif

                @if($sermon->application)
                    <h3>Aplicação</h3>
                    {!! nl2br(e($sermon->application)) !!}
                @endif
            @endif
        </article>

        @if($sermon->full_content)
        <script>
            (function() {
                var baseUrl = @json(route('memberpanel.bible.search'));
                document.querySelectorAll('.sermon-content-with-refs .bible-ref').forEach(function(el) {
                    var ref = el.getAttribute('data-bible-ref');
                    if (!ref) return;
                    var wrap = document.createElement('div');
                    wrap.className = 'mt-2';
                    var link = document.createElement('a');
                    link.href = baseUrl + '?q=' + encodeURIComponent(ref);
                    link.textContent = 'Ver na Bíblia';
                    link.className = 'text-sm text-amber-600 dark:text-amber-400 hover:underline';
                    link.target = '_blank';
                    wrap.appendChild(link);
                    el.appendChild(wrap);
                });
            })();
        </script>
        @endif

        <!-- Tags & Actions -->
        <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($sermon->tags as $tag)
                    <a href="{{ route('memberpanel.sermons.index', ['tag_id' => $tag->id]) }}" class="text-sm text-gray-500 hover:text-amber-500 dark:text-gray-400 dark:hover:text-amber-400 transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    <form action="{{ route('memberpanel.sermons.toggle-favorite', $sermon->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 min-h-[44px] min-w-[44px] py-2 pr-2 rounded-lg {{ $isFavorite ? 'text-amber-500' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }} transition-colors touch-manipulation">
                            <x-icon name="{{ $isFavorite ? 'star' : 'star' }}" class="w-6 h-6 {{ $isFavorite ? 'fill-current' : '' }}" />
                            <span class="text-sm font-bold">{{ $isFavorite ? 'Salvo' : 'Salvar' }}</span>
                        </button>
                    </form>

                    <button type="button" class="flex items-center space-x-2 min-h-[44px] py-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors touch-manipulation">
                        <x-icon name="share" class="w-6 h-6" />
                        <span class="text-sm font-bold">Compartilhar</span>
                    </button>
                </div>

                @if($canEdit)
                    <a href="{{ route('memberpanel.sermons.edit', $sermon) }}" class="inline-flex items-center min-h-[44px] py-2 text-sm font-bold text-blue-500 hover:text-blue-600 touch-manipulation">
                        Editar Sermão
                    </a>
                    <div x-data="{ exportModalOpen: false, format: 'full', size: 'a5' }" class="contents">
                        <button type="button" @click="exportModalOpen = true" class="text-sm font-bold text-amber-600 hover:text-amber-700">
                            Exportar para púlpito
                        </button>
                        <div x-show="exportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                            <div @click.outside="exportModalOpen = false" class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <x-icon name="file-pdf" class="w-5 h-5 text-amber-500" />
                                    Exportar para o púlpito
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Formato</label>
                                        <div class="flex gap-3">
                                            <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'full' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                                <input type="radio" name="member_export_format" value="full" x-model="format" class="sr-only">
                                                <span class="text-sm font-medium">Esboço completo</span>
                                            </label>
                                            <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'topics' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                                <input type="radio" name="member_export_format" value="topics" x-model="format" class="sr-only">
                                                <span class="text-sm font-medium">Apenas tópicos</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tamanho do papel</label>
                                        <div class="flex gap-4">
                                            <label class="flex-1 cursor-pointer" @click="size = 'a4'">
                                                <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a4' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                    <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 42px; height: 59px;"></div>
                                                    <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A4</span>
                                                </div>
                                            </label>
                                            <label class="flex-1 cursor-pointer" @click="size = 'a5'">
                                                <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a5' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                    <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 30px; height: 42px;"></div>
                                                    <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A5</span>
                                                </div>
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Proporção visual do documento.</p>
                                    </div>
                                </div>
                                <div class="mt-6 flex gap-3 justify-end">
                                    <button type="button" @click="exportModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">Cancelar</button>
                                    <a :href="'{{ route('memberpanel.sermons.export-pdf', $sermon) }}?format=' + format + '&size=' + size" target="_blank" @click="exportModalOpen = false"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg">
                                        <x-icon name="download" class="w-4 h-4 mr-2" />
                                        Gerar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Attachments -->
        @if(!empty($sermon->attachments))
            <div class="mt-12 bg-gray-50 dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800">
                <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <x-icon name="paper-clip" class="w-5 h-5 mr-2" />
                    Materiais de Apoio
                </h4>
                <div class="grid gap-3">
                    @foreach($sermon->attachments as $file)
                        <a href="{{ Storage::url($file['path']) }}" target="_blank" class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-amber-500 dark:hover:border-amber-500 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold uppercase">{{ pathinfo($file['name'], PATHINFO_EXTENSION) }}</span>
                            </div>
                            <div class="ml-4 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-amber-500 transition-colors">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                            </div>
                            <x-icon name="download" class="w-5 h-5 ml-auto text-gray-400 group-hover:text-amber-500" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Worship Suggestion -->
        @if($sermon->worshipSuggestion)
            <div class="mt-8 bg-slate-900 rounded-xl p-6 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-linear-to-r from-amber-600/20 to-purple-600/20"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-1">Sugestão de Louvor</p>
                        <h4 class="text-xl font-bold">{{ $sermon->worshipSuggestion->title }}</h4>
                        @if($sermon->worshipSuggestion->artist)
                            <p class="text-sm text-gray-400">{{ $sermon->worshipSuggestion->artist }}</p>
                        @endif
                    </div>
                    <a href="#" class="p-3 bg-white/10 hover:bg-white/20 rounded-full transition-colors">
                        <x-icon name="play" class="w-6 h-6 text-white" />
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Audio Player Footer (Sticky) -->
    <!-- Assuming we have an audio_url field or attachment. Since schema didn't explicitly add audio_url in the prompt list but asked for player, I'll check if exists.
         Actually, the prompt said "Player de Áudio (se houver podcast associado)".
         I'll assume 'attachments' might contain audio or 'audio_url' exists.
         Checking migration: 'audio_url' was added to bible_commentaries, not sermons.
         But let's assume if an attachment is mp3, we play it.
    -->
    @php
        $audioFile = null;
        if(!empty($sermon->attachments)) {
            foreach($sermon->attachments as $file) {
                if(str_contains($file['mime'], 'audio')) {
                    $audioFile = Storage::url($file['path']);
                    break;
                }
            }
        }
    @endphp

    @if($audioFile)
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 p-4 z-50 shadow-lg">
            <div class="max-w-3xl mx-auto flex items-center gap-4">
                <button id="play-pause-btn" type="button" class="w-12 h-12 rounded-full bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center shrink-0 transition-colors" aria-label="Reproduzir">
                    <span id="play-icon" class="flex items-center justify-center"><x-icon name="play" class="w-6 h-6 ml-1" /></span>
                    <span id="pause-icon" class="hidden"><x-icon name="pause" class="w-6 h-6" /></span>
                </button>
                <div class="flex-1">
                    <p class="text-xs font-bold text-amber-500 uppercase tracking-widest">Ouvir Sermão</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $sermon->title }}</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-2 overflow-hidden">
                        <div id="progress-bar" class="bg-amber-500 h-full w-0 transition-all duration-100"></div>
                    </div>
                </div>
                <audio id="sermon-audio" src="{{ $audioFile }}"></audio>
            </div>
        </div>
    @endif

    <!-- Tooltip Element -->
    <div id="bible-tooltip" class="bible-tooltip"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Audio Player Logic
        const audio = document.getElementById('sermon-audio');
        if (audio) {
            const btn = document.getElementById('play-pause-btn');
            const progress = document.getElementById('progress-bar');

            const playIcon = document.getElementById('play-icon');
            const pauseIcon = document.getElementById('pause-icon');
            btn.addEventListener('click', () => {
                if (audio.paused) {
                    audio.play();
                    if (playIcon) playIcon.classList.add('hidden');
                    if (pauseIcon) { pauseIcon.classList.remove('hidden'); pauseIcon.classList.add('flex', 'items-center', 'justify-center'); }
                    btn.setAttribute('aria-label', 'Pausar');
                } else {
                    audio.pause();
                    if (playIcon) playIcon.classList.remove('hidden');
                    if (pauseIcon) { pauseIcon.classList.add('hidden'); pauseIcon.classList.remove('flex', 'items-center', 'justify-center'); }
                    btn.setAttribute('aria-label', 'Reproduzir');
                }
            });

            audio.addEventListener('timeupdate', () => {
                const percent = (audio.currentTime / audio.duration) * 100;
                progress.style.width = percent + '%';
            });
        }

        // Bible Tooltip Logic
        const content = document.getElementById('sermon-content');
        const tooltip = document.getElementById('bible-tooltip');

        // Simple regex to find bible refs (e.g., Joao 3:16) and wrap them
        // Note: Replacing HTML can break event listeners attached to existing elements, but since this is a read view it's mostly safe.
        // For robustness, we target text nodes.

        // This is a simplified regex.
        const bibleRegex = /\b((?:[1-3]\s)?[A-Z][a-zçáéíóúâêôãõ]+)\s(\d+):(\d+(?:-\d+)?)\b/g;

        function linkifyBibleRefs(node) {
            if (node.nodeType === 3) { // Text node
                const text = node.nodeValue;
                if (bibleRegex.test(text)) {
                    const span = document.createElement('span');
                    span.innerHTML = text.replace(bibleRegex, '<span class="bible-ref-link" data-ref="$&">$&</span>');
                    node.parentNode.replaceChild(span, node);
                }
            } else if (node.nodeType === 1 && node.nodeName !== 'A' && node.nodeName !== 'SCRIPT' && !node.classList.contains('bible-ref-link')) {
                node.childNodes.forEach(linkifyBibleRefs);
            }
        }

        // Run linker
        linkifyBibleRefs(content);

        // Event delegation for tooltips
        content.addEventListener('mouseover', (e) => {
            if (e.target.classList.contains('bible-ref-link')) {
                const ref = e.target.getAttribute('data-ref');
                showTooltip(e.target, ref);
            }
        });

        content.addEventListener('mouseout', (e) => {
            if (e.target.classList.contains('bible-ref-link')) {
                tooltip.style.display = 'none';
            }
        });

        const verseCache = {};
        function showTooltip(element, ref) {
            const rect = element.getBoundingClientRect();
            tooltip.style.left = (rect.left + window.scrollX) + 'px';
            tooltip.style.top = (rect.bottom + window.scrollY + 5) + 'px';
            tooltip.style.display = 'block';
            tooltip.innerHTML = `<strong>${ref}</strong><br><span class="text-gray-300 italic">Carregando texto bíblico...</span>`;

            if (verseCache[ref]) {
                tooltip.innerHTML = verseCache[ref];
                return;
            }

            fetch(`/api/v1/bible/find?ref=${encodeURIComponent(ref)}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(resp => {
                    if (resp.data && resp.data.verses && resp.data.verses.length) {
                        const reference = resp.data.reference || ref;
                        const text = resp.data.verses.map(v => v.text).join(' ');
                        verseCache[ref] = `<strong>${reference}</strong><br><span class="text-gray-300">${text}</span>`;
                        tooltip.innerHTML = verseCache[ref];
                    } else {
                        tooltip.innerHTML = `<strong>${ref}</strong><br><span class="text-gray-400 italic">Referência não encontrada.</span>`;
                    }
                })
                .catch(() => {
                    tooltip.innerHTML = `<strong>${ref}</strong><br><span class="text-gray-400 italic">Erro ao carregar versículo.</span>`;
                });
        }
    });
</script>
@endsection

