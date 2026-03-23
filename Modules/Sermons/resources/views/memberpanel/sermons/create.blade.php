@extends('memberpanel::components.layouts.master')

@section('title', 'Criar Sermão')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Novo Sermão</h1>
            <p class="text-gray-600 dark:text-gray-400">Compartilhe uma nova mensagem e referências bíblicas.</p>
        </div>
        <a href="{{ route('memberpanel.sermons.index') }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all hover:-translate-y-0.5">
            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
            Voltar
        </a>
    </div>

    <!-- Main Form -->
    <form id="sermon-form" action="{{ route('memberpanel.sermons.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Info Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-6">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-700">
                        <x-icon name="document-text" class="w-4 h-4" /> Detalhes do Sermão
                    </h3>

                    <div>
                        <label for="title" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold placeholder-gray-400 text-lg"
                            placeholder="Ex: A Importância da Fé">
                        @error('title')
                            <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subtitle" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Subtítulo
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium placeholder-gray-400"
                            placeholder="Uma breve introdução ao tema">
                    </div>

                    <div>
                         <label for="description" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Resumo
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium placeholder-gray-400 resize-none"
                            placeholder="Um resumo curto do que será abordado...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Sermon Content Sections -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-6">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-700">
                        <x-icon name="pencil-alt" class="w-4 h-4" /> Estrutura do Sermão
                    </h3>

                    <div class="space-y-6">
                        <div>
                             <label for="introduction" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Introdução
                            </label>
                            <textarea name="introduction" id="introduction" rows="4"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium placeholder-gray-400"
                                placeholder="Comece seu sermão aqui...">{{ old('introduction') }}</textarea>
                        </div>

                        <div>
                             <label for="development" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Desenvolvimento
                            </label>
                            <textarea name="development" id="development" rows="8"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium placeholder-gray-400"
                                placeholder="Desenvolva os pontos principais...">{{ old('development') }}</textarea>
                        </div>

                        <div>
                             <label for="conclusion" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Conclusão
                            </label>
                            <textarea name="conclusion" id="conclusion" rows="4"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium placeholder-gray-400"
                                placeholder="Finalize com uma conclusão forte...">{{ old('conclusion') }}</textarea>
                        </div>

                         <div>
                             <label for="application" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Aplicação Prática
                            </label>
                            <textarea name="application" id="application" rows="4"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800/30 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium placeholder-gray-400"
                                placeholder="Como aplicar isso no dia a dia?">{{ old('application') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Meta -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Publishing Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-700">
                        <x-icon name="cog" class="w-4 h-4" /> Configurações
                    </h3>

                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="status" id="status" required
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium appearance-none cursor-pointer">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="visibility" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Visibilidade <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="visibility" id="visibility" required
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium appearance-none cursor-pointer">
                                <option value="public" {{ old('visibility', 'members') === 'public' ? 'selected' : '' }}>Público</option>
                                <option value="members" {{ old('visibility', 'members') === 'members' ? 'selected' : '' }}>Membros</option>
                                <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Privado</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="sermon_date" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Data do Sermão
                        </label>
                        <input type="date" name="sermon_date" id="sermon_date" value="{{ old('sermon_date') }}"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Categoria
                        </label>
                        <div class="relative">
                            <select name="category_id" id="category_id"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium appearance-none cursor-pointer">
                                <option value="">Selecione...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <label class="flex items-center gap-3 cursor-pointer group p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                             <input type="checkbox" name="is_collaborative" value="1" {{ old('is_collaborative') ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 transition-colors">Permitir colaboração</span>
                        </label>
                    </div>
                </div>

                <!-- Bible References -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <x-icon name="book-open" class="w-4 h-4" /> Referências
                        </h3>
                        <button type="button" id="add-bible-reference"
                            class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded-lg transition-colors">
                            <x-icon name="plus" class="w-3 h-3" /> Adicionar
                        </button>
                    </div>

                    <div id="bible-references-container" class="space-y-4">
                        <!-- JS injections here -->
                    </div>
                </div>

                <!-- Tags -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                     <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-700">
                        <x-icon name="hashtag" class="w-4 h-4" /> Tags
                    </h3>

                    <div class="flex items-center gap-2">
                        <input type="text" id="tag-input"
                            class="flex-1 px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium placeholder-gray-400"
                            placeholder="Nova tag...">
                        <button type="button" id="add-tag"
                            class="p-2 bg-gray-100 dark:bg-gray-700 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <x-icon name="plus" class="w-4 h-4" />
                        </button>
                    </div>
                    <div id="selected-tags" class="flex flex-wrap gap-2 min-h-[40px]"></div>
                </div>

                <!-- Actions -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-600/20 transition-all hover:-translate-y-0.5 text-lg flex items-center justify-center gap-2">
                        <x-icon name="save" class="w-5 h-5" /> Salvar Sermão
                    </button>
                    <a href="{{ route('memberpanel.sermons.index') }}"
                        class="block w-full text-center py-3 mt-3 text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Bible Reference Template (Hidden) -->
<template id="bible-reference-template">
    <div class="bible-reference-item border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-900/30 relative group hover:border-blue-200 dark:hover:border-blue-900/50 transition-colors">
        <button type="button" data-remove-reference class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
            <x-icon name="trash" class="w-4 h-4" />
        </button>

        <div class="space-y-3">
             <div class="grid grid-cols-2 gap-3">
                 <div>
                    <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Versão</label>
                    <div class="relative">
                        <select name="bible_references[INDEX][bible_version_id]"
                            class="w-full pl-2 pr-6 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                            <option value="">Selecione</option>
                            @if(!empty($bibleVersions))
                                @foreach($bibleVersions as $version)
                                    <option value="{{ $version->id }}">{{ $version->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div>
                     <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Livro</label>
                     <div class="relative">
                        <select name="bible_references[INDEX][book_id]"
                            class="w-full pl-2 pr-6 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                            <option value="">Selecione</option>
                            @if(!empty($bibleBooks))
                                @foreach($bibleBooks as $book)
                                    <option value="{{ $book->id }}">{{ $book->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                 <div>
                    <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Capítulo</label>
                    <input type="number" name="bible_references[INDEX][chapter]" min="1"
                        class="w-full px-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Versículos</label>
                    <input type="text" name="bible_references[INDEX][verses]"
                        class="w-full px-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 1-10">
                </div>
            </div>

            <div>
                 <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Tipo</label>
                 <select name="bible_references[INDEX][type]"
                    class="w-full px-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="main">Referência Principal</option>
                    <option value="support">Texto de Apoio</option>
                    <option value="illustration">Ilustração</option>
                    <option value="other">Outro context</option>
                </select>
            </div>

            <div>
                <textarea name="bible_references[INDEX][context]" rows="2"
                    class="w-full px-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="Nota de contexto (opcional)..."></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sermon-form');
    if (form) form.addEventListener('submit', function() {
        window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }));
    });
});
</script>
    @vite(['Modules/Sermons/resources/assets/js/app.js'])
@endpush

