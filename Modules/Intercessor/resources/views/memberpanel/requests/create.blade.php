@extends('memberpanel::components.layouts.master')

@section('page-title', 'Novo Pedido de Oração')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12" x-data="prayerRequestForm()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li>Intercessão</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-blue-600 dark:text-blue-400">Novo Pedido</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Novo Pedido</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Compartilhe sua causa com a comunidade.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('member.intercessor.room.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Voltar ao Mural
             </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 rounded-3xl p-8 mb-8">
            <div class="flex items-center gap-3 mb-4 text-red-600 dark:text-red-400 font-black uppercase tracking-widest text-xs">
                <x-icon name="exclamation-circle" class="w-5 h-5" />
                <h3>Atenção aos seguintes erros:</h3>
            </div>
            <ul class="text-red-500 dark:text-red-400/80 text-sm font-medium list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('member.intercessor.requests.store') }}" method="POST" class="space-y-8" data-tour="intercessor-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Primary Details -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 md:p-12 space-y-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-4 border-b border-gray-50 dark:border-gray-700">
                        <x-icon name="pencil" class="w-4 h-4" /> Detalhes do Pedido
                    </h3>

                    <div class="space-y-8">
                        <!-- Title -->
                        <div class="space-y-3">
                            <label for="title" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Título do Pedido</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
                                placeholder="Resumo do pedido (Ex: Cura para minha mãe)">
                        </div>

                        <!-- Description -->
                        <div class="space-y-3 relative">
                            <label for="description" class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex justify-between items-center">
                                Descrição Detalhada
                                <span class="text-[10px] lowercase font-bold text-gray-400">Digite <span class="text-blue-500">@</span> para citar a Bíblia</span>
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="8"
                                x-ref="descInput"
                                x-model="requestDescription"
                                @input="checkMention"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectCurrentBook"
                                class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-4xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none leading-relaxed"
                                placeholder="Descreva seu pedido de oração..."
                            ></textarea>

                            <!-- Autocomplete Dropdown -->
                            <div
                                x-show="showBooksDropdown"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute z-50 mt-1 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden"
                                style="display: none;"
                            >
                                <ul class="max-h-60 overflow-y-auto py-2">
                                    <template x-for="(book, index) in filteredBooks" :key="'ac-bk-' + book.id">
                                        <li
                                            @click="insertBook(book.name)"
                                            :class="{ 'bg-blue-600 text-white': activeIndex === index, 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50': activeIndex !== index }"
                                            class="px-4 py-2.5 cursor-pointer text-xs font-black uppercase tracking-widest transition-colors flex items-center justify-between"
                                        >
                                            <span x-text="book.name"></span>
                                            <span x-show="activeIndex === index" class="text-[8px] opacity-70">ENTER</span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Configuration Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-4 border-b border-gray-50 dark:border-gray-700">
                        <x-icon name="cog" class="w-4 h-4" /> Configurações
                    </h3>

                    <div class="space-y-6">
                        <!-- Category -->
                        <div class="space-y-3">
                            <label for="category_id" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Categoria</label>
                            <select name="category_id" id="category_id" required
                                class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none">
                                <option value="">Selecione...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Urgency -->
                        <div class="space-y-3">
                            <label for="urgency_level" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Prioridade</label>
                            <select name="urgency_level" id="urgency_level" required
                                class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none">
                                <option value="normal" {{ old('urgency_level') == 'normal' ? 'selected' : '' }}>Prioridade Normal</option>
                                <option value="high" {{ old('urgency_level') == 'high' ? 'selected' : '' }}>Alta Prioridade</option>
                                <option value="critical" {{ old('urgency_level') == 'critical' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>

                        <!-- Anonymity Toggle -->
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest" x-text="showIdentity ? 'Identidade Visível' : 'Modo Anônimo'"></span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_identity" value="1" class="peer sr-only" x-model="showIdentity">
                                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 italic leading-relaxed" x-show="!showIdentity">Seu nome será ocultado publicamente.</p>
                        </div>
                    </div>
                </div>

                <!-- Privacy & Action Card -->
                <div class="bg-indigo-600 rounded-3xl p-8 space-y-8 shadow-xl shadow-indigo-600/20">
                    <div>
                        <h3 class="text-xs font-black text-indigo-200 uppercase tracking-widest mb-6 opacity-80">Visibilidade</h3>
                        <div class="space-y-4">
                            @foreach(['public' => 'Todos', 'members_only' => 'Membros', 'intercessors_only' => 'Equipe', 'pastoral_only' => 'Pastoral'] as $level => $label)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="privacy_level" value="{{ $level }}"
                                        {{ old('privacy_level', 'members_only') == $level ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div class="w-5 h-5 border-2 border-indigo-400 rounded-full flex items-center justify-center peer-checked:border-white peer-checked:bg-white transition-all group-hover:border-white">
                                        <div class="w-2 h-2 bg-indigo-600 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                    </div>
                                    <span class="text-sm font-black text-indigo-100 peer-checked:text-white uppercase tracking-tight">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-indigo-500/30">
                        <button type="submit" class="w-full py-4 bg-white text-indigo-600 font-black text-xs rounded-2xl transition-all shadow-xl hover:scale-[1.02] active:scale-95 uppercase tracking-widest">
                            Publicar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('prayerRequestForm', () => ({
            description: @js(old('description', '')),
            requestDescription: @js(old('description', '')),
            showIdentity: {{ old('show_identity', 'true') == '1' || old('show_identity', 'true') == 'true' ? 'true' : 'false' }},

            // Autocomplete State
            showBooksDropdown: false,
            books: [],
            filteredBooks: [],
            cursorPosition: 0,
            searchQuery: '',
            activeIndex: 0,

            init() {
                this.fetchBooks();
            },

            async fetchBooks() {
                try {
                    const response = await fetch('/api/v1/bible/books');
                    if (response.ok) {
                        const resp = await response.json();
                        this.books = resp.data || [];
                    }
                } catch (e) {
                    console.error('Failed to fetch books', e);
                }
            },

            checkMention(e) {
                const input = e.target;
                this.cursorPosition = input.selectionStart;
                const textBeforeCursor = this.requestDescription.substring(0, this.cursorPosition);

                const match = textBeforeCursor.match(/@([\w\sáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ]*)$/);

                if (match) {
                    this.searchQuery = match[1];
                    this.filterBooks();

                    if (this.filteredBooks.length > 0) {
                        this.showBooksDropdown = true;
                        this.activeIndex = 0;
                    } else {
                        this.showBooksDropdown = false;
                    }
                } else {
                    this.showBooksDropdown = false;
                }
            },

            filterBooks() {
                if (this.searchQuery === '') {
                    this.filteredBooks = this.books;
                } else {
                    const q = this.searchQuery.toLowerCase();
                    this.filteredBooks = this.books.filter(b =>
                        b.name.toLowerCase().includes(q) ||
                        (b.abbreviation && b.abbreviation.toLowerCase().includes(q))
                    );
                }
            },

            moveSelection(step) {
                if (!this.showBooksDropdown) return;

                const newIndex = this.activeIndex + step;
                if (newIndex >= 0 && newIndex < this.filteredBooks.length) {
                    this.activeIndex = newIndex;
                }
            },

            selectCurrentBook() {
                if (!this.showBooksDropdown || this.filteredBooks.length === 0) return;
                this.insertBook(this.filteredBooks[this.activeIndex].name);
            },

            insertBook(bookName) {
                const textBeforeCursor = this.requestDescription.substring(0, this.cursorPosition);
                const textAfterCursor = this.requestDescription.substring(this.cursorPosition);

                const lastAt = textBeforeCursor.lastIndexOf('@');
                const newTextBefore = textBeforeCursor.substring(0, lastAt) + '@' + bookName + ' ';

                this.requestDescription = newTextBefore + textAfterCursor;
                this.showBooksDropdown = false;

                this.$nextTick(() => {
                    this.$refs.descInput.focus();
                });
            }
        }));
    });
</script>
@endsection

