@extends('admin::components.layouts.master')

@section('title', 'Criar Lição EBD - Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500 pb-24">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white rounded">Matriz Pedagógica</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Novo <span class="text-amber-600">Planejamento</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Desenvolva o roteiro da aula, associando referências bíblicas e conteúdo pedagógico estruturado.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.lessons.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.ebd.lessons.store') }}" method="POST" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando lição...' } }))">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Context & Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Academic Context -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                        <x-icon name="graduation-cap" style="duotone" class="w-5 h-5 text-amber-600" />
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Contexto Acadêmico</h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Classe / Turma</label>
                                <select name="class_id" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all cursor-pointer">
                                    <option value="">Selecione a classe</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $selectedClass) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->age_group_display }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Título da Lição</label>
                                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ex: A Graça de Deus em 1 Coríntios"
                                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data da Aula</label>
                                <input type="date" name="lesson_date" value="{{ old('lesson_date', now()->next('Sunday')->format('Y-m-d')) }}" required
                                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Horário</label>
                                <input type="time" name="lesson_time" value="{{ old('lesson_time', $defaultLessonTime ?? '09:00') }}" required
                                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Breve Descrição</label>
                            <textarea name="description" rows="2" placeholder="Resumo rápido do que será abordado..."
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Pedagogical Content -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                        <x-icon name="pen-nib" style="duotone" class="w-5 h-5 text-amber-600" />
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Matriz de Conteúdo</h3>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Objetivo Principal</label>
                            <textarea name="objective" rows="2" placeholder="O que o aluno deve aprender?"
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('objective') }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Introdução</label>
                            <textarea name="introduction" rows="3" placeholder="Contextualização e quebra-gelo..."
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('introduction') }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Desenvolvimento Teológico</label>
                            <textarea name="development" rows="10" placeholder="Corpo principal da lição, explicações e pontos chave..."
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('development') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Conclusão</label>
                                <textarea name="conclusion" rows="4" placeholder="Fechamento e resumo..."
                                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('conclusion') }}</textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aplicação Prática</label>
                                <textarea name="application" rows="4" placeholder="Como viver isso no dia a dia?"
                                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('application') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Bible & Status -->
            <div class="space-y-8">
                <!-- Status Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                        <h3 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Status de Publicação</h3>
                    </div>
                    <div class="p-6">
                        <select name="status" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all cursor-pointer">
                            <option value="scheduled" {{ old('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>Agendada</option>
                            <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Concluída</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                </div>

                <!-- Bible Engine Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden sticky top-8">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-blue-50/50 dark:bg-blue-900/10 flex items-center justify-between">
                        <h3 class="text-[10px] font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                            <x-icon name="book-bible" class="w-3 h-3" />
                            Motor Bíblico
                        </h3>
                        <span class="px-2 py-0.5 bg-blue-600 text-white text-[8px] font-black rounded uppercase">API v2</span>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Versão</label>
                            <select name="bible_version" id="bible_version" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-xs font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                                <option value="">Selecione...</option>
                                @foreach ($bibleVersions as $version)
                                    <option value="{{ strtolower($version->abbreviation) }}" data-version-id="{{ $version->id }}"
                                        {{ old('bible_version', $defaultBibleVersion ?? 'nvi') === strtolower($version->abbreviation) ? 'selected' : '' }}>
                                        {{ $version->name }} ({{ $version->abbreviation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Livro</label>
                            <select name="bible_book" id="bible_book" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-xs font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                                <option value="">Selecione o livro</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Capítulo</label>
                                <select name="bible_chapter" id="bible_chapter" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-xs font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                                    <option value="">...</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Versículos</label>
                                <input type="text" name="bible_verses" id="bible_verses" value="{{ old('bible_verses') }}" placeholder="1-10"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-xs font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                            </div>
                        </div>

                        <!-- Bible Preview Area -->
                        <div id="bible_reference_preview" class="hidden mt-4 space-y-3">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/50">
                                <span class="text-[9px] font-black text-blue-400 dark:text-blue-500 uppercase tracking-widest block mb-1">Referência Ativa</span>
                                <p id="reference_text" class="text-xs font-black text-blue-900 dark:text-blue-200 uppercase"></p>
                            </div>
                            <div id="verses_text_preview" class="hidden">
                                <div id="verses_content" class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 max-h-48 overflow-y-auto border border-transparent text-[11px] font-medium leading-relaxed text-gray-600 dark:text-gray-400 italic">
                                    Carregando...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Form Actions -->
        <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-8 py-4 bg-gray-900/90 dark:bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 dark:border-black/5 animate-in slide-in-from-bottom-10 duration-700">
            <a href="{{ route('admin.ebd.lessons.index') }}" class="px-6 py-2 text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest hover:text-white dark:hover:text-black transition-colors">Cancelar</a>
            <div class="w-px h-4 bg-gray-700 dark:bg-gray-200"></div>
            <button type="submit" class="flex items-center gap-3 px-8 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black text-xs uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-lg shadow-amber-600/30">
                <x-icon name="floppy-disk" class="w-4 h-4" />
                Publicar Lição
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const versionSelect = document.getElementById('bible_version');
        const bookSelect = document.getElementById('bible_book');
        const chapterSelect = document.getElementById('bible_chapter');
        const versesInput = document.getElementById('bible_verses');
        const previewDiv = document.getElementById('bible_reference_preview');
        const referenceText = document.getElementById('reference_text');

        let currentVersionId = null;
        let currentBookId = null;

        versionSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            currentVersionId = selectedOption ? selectedOption.dataset.versionId : null;
            bookSelect.innerHTML = '<option value="">...</option>';
            chapterSelect.innerHTML = '<option value="">...</option>';
            previewDiv.classList.add('hidden');
            if (currentVersionId) loadBooks(currentVersionId);
        });

        bookSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            currentBookId = selectedOption ? selectedOption.dataset.bookId : null;
            chapterSelect.innerHTML = '<option value="">...</option>';
            previewDiv.classList.add('hidden');
            if (currentBookId) loadChapters(currentBookId);
        });

        chapterSelect.addEventListener('change', function() {
            updatePreview();
            if (versesInput.value) loadVersesText();
        });

        versesInput.addEventListener('input', function() {
            updatePreview();
            loadVersesText();
        });

        function loadBooks(versionId) {
            fetch(`{{ route('bible.api.books') }}?version_id=${versionId}`)
                .then(r => r.json())
                .then(d => {
                    const list = d.data || [];
                    bookSelect.innerHTML = '<option value="">Selecione o livro</option>';
                    list.forEach(b => {
                        const o = document.createElement('option');
                        o.value = b.name; o.textContent = b.name; o.dataset.bookId = b.id;
                        bookSelect.appendChild(o);
                    });
                });
        }

        function loadChapters(bookId) {
            fetch(`{{ route('bible.api.chapters') }}?book_id=${bookId}`)
                .then(r => r.json())
                .then(d => {
                    const list = d.data || [];
                    chapterSelect.innerHTML = '<option value="">Capítulo</option>';
                    list.forEach(c => {
                        const o = document.createElement('option');
                        o.value = c.chapter_number; o.textContent = c.chapter_number;
                        o.dataset.totalVerses = c.total_verses;
                        chapterSelect.appendChild(o);
                    });
                });
        }

        function updatePreview() {
            const book = bookSelect.value;
            const chapter = chapterSelect.value;
            const verses = versesInput.value;
            if (book && chapter) {
                referenceText.textContent = `${book} ${chapter}${verses ? ':' + verses : ''}`;
                previewDiv.classList.remove('hidden');
                if (verses) document.getElementById('verses_text_preview').classList.remove('hidden');
            } else previewDiv.classList.add('hidden');
        }

        function loadVersesText() {
            const bookId = currentBookId || bookSelect.options[bookSelect.selectedIndex]?.dataset?.bookId;
            const chapterNumber = chapterSelect.value;
            const verseRange = versesInput.value;
            if (!bookId || !chapterNumber || !verseRange) return;

            const content = document.getElementById('verses_content');
            content.innerHTML = '<span class="animate-pulse">Sincronizando...</span>';

            fetch(`{{ route('bible.api.verses') }}?book_id=${bookId}&chapter_number=${chapterNumber}&verse_range=${encodeURIComponent(verseRange)}`)
                .then(r => r.json())
                .then(d => {
                    const list = d.data || [];
                    if (list.length) {
                        content.innerHTML = list.map(v => `<span class="text-blue-500 font-black mr-1">${v.verse_number}</span>${v.text}`).join(' ');
                    } else content.innerHTML = 'Referência não encontrada.';
                });
        }
    });
</script>
@endpush
@endsection

