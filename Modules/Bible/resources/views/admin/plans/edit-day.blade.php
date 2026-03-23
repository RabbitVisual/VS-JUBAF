@extends('admin::components.layouts.master')

@section('title', 'Studio de Conteúdo - ' . $day->title)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-20">

    <!-- Top Bar -->
    <div class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.bible.plans.show', $plan->id) }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="chevron-left" style="duotone" class="w-6 h-6" />
                </a>
                <div>
                     <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        Studio de Edição
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700">{{ $day->title }}</span>
                    </h1>
                     <p class="text-xs text-gray-500">Plano: {{ $plan->title }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                 <button onclick="resetForm()" class="hidden px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-bold transition-colors" id="topCancelBtn">
                    Cancelar Edição
                </button>
                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-2"></div>
                <button type="submit" form="editorForm" id="topSaveBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg shadow-blue-500/30 text-sm font-bold transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <x-icon name="check" style="duotone" class="w-4 h-4" />
                    Salvar Item
                </button>
            </div>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT: Timeline (WYSIWYG Preview) -->
            <div class="lg:col-span-7 space-y-6">
                 <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Timeline do Dia</h2>
                    <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">O que o usuário vê</span>
                </div>

                @if($day->contents->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <x-icon name="plus" class="w-8 h-8" />
                        </div>
                        <h3 class="text-gray-900 dark:text-white font-bold">Timeline Vazia</h3>
                        <p class="text-gray-500 text-sm mt-1">Use o painel à direita para adicionar o primeiro conteúdo deste dia.</p>
                    </div>
                @else
                    <div class="space-y-4 relative before:absolute before:inset-0 before:ml-6 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gray-200 dark:before:bg-gray-700 before:-z-10">
                        @foreach($day->contents as $content)
                            <div class="relative group pl-16 md:pl-0">
                                <!-- Timestamp Line Dot -->
                                <div class="absolute left-0 top-1/2 -mt-3 ml-3 md:mx-auto md:left-0 md:right-0 w-6 h-6 rounded-full bg-white dark:bg-gray-900 border-4 border-blue-500 z-10 shadow-sm transition-transform group-hover:scale-125"></div>

                                <!-- Card -->
                                <div class="md:w-1/2 {{ $loop->even ? 'md:ml-auto md:pl-8' : 'md:mr-auto md:pr-8' }}">
                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow relative overflow-hidden cursor-pointer" onclick='editItem(@json($content))'>
                                        <!-- Hover Actions -->
                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-100 p-1">
                                            <button onclick='editItem(@json($content)); event.stopPropagation();' class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Editar">
                                                <x-icon name="pen-to-square" class="w-4 h-4" />
                                            </button>
                                            <form action="{{ route('admin.bible.plans.content.destroy', $content->id) }}" method="POST" onsubmit="return confirm('Remover item?'); event.stopPropagation();" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Excluir">
                                                    <x-icon name="trash-can" style="duotone" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>

                                        <div class="flex items-center gap-3 mb-3">
                                            @if($content->type === 'scripture')
                                                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg"><x-icon name="book-open" class="w-5 h-5" /></div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $content->book->name }} {{ $content->chapter_start }}</h4>
                                                    <span class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ $content->book->bibleVersion->abbreviation ?? 'NVI' }}</span>
                                                </div>
                                            @elseif($content->type === 'devotional')
                                                <div class="p-2 bg-purple-100 text-purple-600 rounded-lg"><x-icon name="file-lines" class="w-5 h-5" /></div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $content->title ?: 'Devocional' }}</h4>
                                                    <span class="text-xs text-gray-500">Texto de Apoio</span>
                                                </div>
                                            @elseif($content->type === 'video')
                                                 <div class="p-2 bg-red-100 text-red-600 rounded-lg"><x-icon name="circle-play" class="w-5 h-5" /></div>
                                                 <div>
                                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $content->title ?: 'Vídeo' }}</h4>
                                                    <span class="text-xs text-gray-500 truncate block max-w-[150px]">{{ $content->body }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($content->type === 'scripture')
                                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded p-2 text-xs text-gray-600 dark:text-gray-400 italic border-l-2 border-blue-400">
                                                (Prévia do texto será carregada no app)
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIGHT: Editor Panel (Sticky) -->
            <div class="lg:col-span-5">
                <div class="sticky top-24 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden ring-1 ring-gray-900/5">

                    <!-- Editor Header -->
                    <div class="bg-gray-900 px-6 py-4 flex justify-between items-center" id="editorHeader">
                        <h3 class="text-white font-bold flex items-center gap-2" id="editorTitle">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-xs">A</span>
                            Novo Item
                        </h3>
                        <span class="text-xs text-gray-400 uppercase tracking-widest font-semibold" id="editorModeLabel">Adicionar</span>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('admin.bible.plans.content.store', $day->id) }}" method="POST" id="editorForm">
                            @csrf
                            <input type="hidden" name="_method" id="methodInput" value="POST">

                            <!-- Step 1: Tool Selector -->
                            <div class="mb-8">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">1. Tipo de Conteúdo</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="type" value="scripture" checked class="peer sr-only" onchange="toggleType('scripture')">
                                        <div class="p-4 rounded-xl border-2 border-gray-100 hover:border-blue-100 bg-white hover:bg-blue-50/50 transition-all text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">
                                            <div class="mb-2 mx-auto w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                <x-icon name="book-open" class="w-6 h-6" />
                                            </div>
                                            <span class="text-xs font-bold block">Bíblia</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="type" value="devotional" class="peer sr-only" onchange="toggleType('devotional')">
                                        <div class="p-4 rounded-xl border-2 border-gray-100 hover:border-purple-100 bg-white hover:bg-purple-50/50 transition-all text-center peer-checked:border-purple-600 peer-checked:bg-purple-50 peer-checked:text-purple-700">
                                            <div class="mb-2 mx-auto w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                <x-icon name="pen-to-square" style="duotone" class="w-6 h-6" />
                                            </div>
                                            <span class="text-xs font-bold block">Texto</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="type" value="video" class="peer sr-only" onchange="toggleType('video')">
                                        <div class="p-4 rounded-xl border-2 border-gray-100 hover:border-red-100 bg-white hover:bg-red-50/50 transition-all text-center peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700">
                                            <div class="mb-2 mx-auto w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                <x-icon name="circle-play" class="w-6 h-6" />
                                            </div>
                                            <span class="text-xs font-bold block">Vídeo</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Step 2: Details -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">2. Detalhes</label>

                                <div class="space-y-4">
                                    <input type="text" name="title" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="Título (Opcional, ex: Versículo Chave)">

                                    <!-- SCRIPTURE SECTION -->
                                    <div id="scriptureFields" class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="block text-xs font-semibold text-gray-500 mb-1">Versão Bíblica</label>
                                                <select id="versionSelect" class="w-full rounded-lg border-gray-300 text-sm" onchange="loadBooks()">
                                                    @foreach($versions as $version)
                                                        <option value="{{ $version->id }}">{{ $version->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-xs font-semibold text-gray-500 mb-1">Livro</label>
                                                <select name="book_id" id="bookSelect" class="w-full rounded-lg border-gray-300 text-sm" onchange="loadChapters()" disabled>
                                                    <option value="">Selecione...</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-500 mb-1">Capítulo</label>
                                                <select name="chapter_start" id="chapterSelect" class="w-full rounded-lg border-gray-300 text-sm" onchange="loadVerses(); syncChapterEnd()" disabled>
                                                    <option>...</option>
                                                </select>
                                                <input type="hidden" name="chapter_end" id="chapterEnd">
                                            </div>
                                            <!-- Just placeholder for visual balance -->
                                            <div class="hidden"></div>
                                        </div>

                                        <div class="border-t border-gray-200 pt-4">
                                             <label class="block text-xs font-semibold text-gray-500 mb-2">Intervalo de Versículos</label>
                                             <div class="flex items-center gap-2">
                                                <select name="verse_start" id="verseStartSelect" class="w-full rounded-lg border-gray-300 text-sm" onchange="updatePreview()" disabled>
                                                    <option value="">Do ínicio</option>
                                                </select>
                                                <span class="text-gray-400">-</span>
                                                <select name="verse_end" id="verseEndSelect" class="w-full rounded-lg border-gray-300 text-sm" onchange="updatePreview()" disabled>
                                                    <option value="">Ao fim</option>
                                                </select>
                                             </div>
                                        </div>

                                        <!-- Live Preview Widget -->
                                        <div class="mt-4 bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                                            <h6 class="text-[10px] uppercase font-bold text-gray-400 mb-1">Preview</h6>
                                            <p id="versePreview" class="text-sm text-gray-600 italic leading-relaxed min-h-[3rem]">
                                                Selecione uma passagem para visualizar...
                                            </p>
                                        </div>
                                    </div>

                                    <!-- DEVOTIONAL SECTION -->
                                    <div id="devotionalFields" class="hidden">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Conteúdo do Texto</label>
                                        <textarea name="body" rows="8" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="Escreva o devocional aqui..."></textarea>
                                    </div>

                                    <!-- VIDEO SECTION -->
                                    <div id="videoFields" class="hidden">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">URL do Vídeo</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><x-icon name="link" class="w-4 h-4" /></span>
                                            <input type="url" name="video_url" class="w-full pl-9 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="https://youtube.com/...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer Saver (Mobile) or Helper -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-400">Todas as alterações são salvas automaticamente no plano.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- STUDIO CONTROLLER ---

    // State
    let versesCache = [];
    const elements = {
        title: document.getElementById('editorTitle'),
        modeLabel: document.getElementById('editorModeLabel'),
        header: document.getElementById('editorHeader'),
        form: document.getElementById('editorForm'),
        methodInput: document.getElementById('methodInput'),
        topSaveBtn: document.getElementById('topSaveBtn'),
        topCancelBtn: document.getElementById('topCancelBtn'),

        // Fields
        titleInput: document.querySelector('input[name="title"]'),
        bodyInput: document.querySelector('textarea[name="body"]'),
        videoInput: document.querySelector('input[name="video_url"]'),

        // Selects
        versionSelect: document.getElementById('versionSelect'),
        bookSelect: document.getElementById('bookSelect'),
        chapterSelect: document.getElementById('chapterSelect'),
        verseStartSelect: document.getElementById('verseStartSelect'),
        verseEndSelect: document.getElementById('verseEndSelect'),
        versePreview: document.getElementById('versePreview'),
    };

    // --- INIT ---
    document.addEventListener('DOMContentLoaded', () => {
        loadBooks();
    });

    // --- API LOGIC (REUSED) ---
    async function loadBooks(selectedId = null) {
        setLoading(elements.bookSelect);
        try {
            const res = await fetch(`{{ route('admin.bible.api.books') }}?version_id=${elements.versionSelect.value}`);
            const data = await res.json();
            populateSelect(elements.bookSelect, data, 'id', 'name', selectedId);
        } catch(e) { console.error(e); }
    }

    async function loadChapters(selectedNum = null) {
        if(!elements.bookSelect.value) return;
        setLoading(elements.chapterSelect);
        try {
            const res = await fetch(`{{ route('admin.bible.api.chapters') }}?book_id=${elements.bookSelect.value}`);
            const data = await res.json();
            // Map for strict data-id handling if needed, but simplified here
            populateSelect(elements.chapterSelect, data, 'chapter_number', 'chapter_number', selectedNum, true); // Save ID in dataset
        } catch(e) { console.error(e); }
    }

    async function loadVerses(startVal = null, endVal = null) {
        const option = elements.chapterSelect.options[elements.chapterSelect.selectedIndex];
        if(!option || !option.dataset.id) return;

        try {
            const res = await fetch(`{{ route('admin.bible.api.verses') }}?chapter_id=${option.dataset.id}`);
            versesCache = await res.json();

            populateSelect(elements.verseStartSelect, versesCache, 'verse_number', 'verse_number', startVal);
            populateSelect(elements.verseEndSelect, versesCache, 'verse_number', 'verse_number', endVal);

            updatePreview();
        } catch(e) { console.error(e); }
    }

    function syncChapterEnd() {
        document.getElementById('chapterEnd').value = elements.chapterSelect.value;
    }

    function updatePreview() {
        const start = parseInt(elements.verseStartSelect.value) || 0;
        const end = parseInt(elements.verseEndSelect.value) || 999;
        const text = versesCache.filter(v => v.verse_number >= start && v.verse_number <= end)
            .map(v => `<span class="font-bold text-xs align-top opacity-50 mr-1">${v.verse_number}</span>${v.text}`)
            .join(' ');
        elements.versePreview.innerHTML = text || 'Selecione versículos...';
    }

    // --- EDITOR LOGIC ---

    async function editItem(item) {
        // UI Transition
        elements.header.classList.remove('bg-gray-900');
        elements.header.classList.add('bg-yellow-500');
        elements.title.innerHTML = `Editando Item #${item.id}`;
        elements.modeLabel.innerText = "Atualizar";
        elements.topSaveBtn.innerHTML = "Salvar Alterações";
        elements.topCancelBtn.classList.remove('hidden');

        // Config Form
        const updateUrl = "{{ route('admin.bible.plans.content.update', 'ID') }}".replace('ID', item.id);
        elements.form.action = updateUrl;
        elements.methodInput.value = 'PUT';

        // Set Values
        toggleType(item.type);
        elements.titleInput.value = item.title || '';
        elements.bodyInput.value = item.body || '';
        if(item.type === 'video') elements.videoInput.value = item.body;

        // Async Load Scripture
        if(item.type === 'scripture' && item.book) {
            elements.versionSelect.value = item.book.bible_version_id || elements.versionSelect.value;
            await loadBooks(item.book_id);
            await loadChapters(item.chapter_start);
            await loadVerses(item.verse_start, item.verse_end);
        }

        // Scroll
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        // UI Reset
        elements.header.classList.add('bg-gray-900');
        elements.header.classList.remove('bg-yellow-500');
        elements.title.innerHTML = `<span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-xs">A</span> Novo Item`;
        elements.modeLabel.innerText = "Adicionar";
        elements.topSaveBtn.innerHTML = ` <x-icon name="check" style="duotone" class="w-4 h-4" /> Salvar Item`;
        elements.topCancelBtn.classList.add('hidden');

        // Form Reset
        elements.form.action = "{{ route('admin.bible.plans.content.store', $day->id) }}";
        elements.methodInput.value = 'POST';
        elements.form.reset();
        toggleType('scripture');
        elements.versePreview.innerHTML = 'Selecione uma passagem...';
        loadBooks();
    }

    function toggleType(type) {
        ['scripture', 'devotional', 'video'].forEach(t => {
            const el = document.getElementById(t + 'Fields');
            if(el) el.classList.add('hidden');
        });
        document.getElementById(type + 'Fields').classList.remove('hidden');
        document.querySelector(`input[name="type"][value="${type}"]`).checked = true;
    }

    // Helpers
    function populateSelect(el, items, valKey, textKey, selectedVal, saveDataId = false) {
        el.innerHTML = '<option value="">Selecione...</option>';
        items.forEach(i => {
            const opt = document.createElement('option');
            opt.value = i[valKey];
            opt.innerText = i[textKey];
            if(selectedVal && i[valKey] == selectedVal) opt.selected = true;
            if(saveDataId) opt.dataset.id = i.id;
            el.appendChild(opt);
        });
        el.disabled = false;
    }

    function setLoading(el) {
        el.innerHTML = '<option>Carregando...</option>';
        el.disabled = true;
    }
</script>
@endsection

