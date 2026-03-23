@extends('pastoralpanel::components.layouts.master')

@section('title', 'Criar Sermão - Administração')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Criar Novo Sermão</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Sermon Engine 2.0 - Criação de Conteúdo Profissional</p>
            </div>
            <a href="{{ route('pastor.sermoes.sermons.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" class="-ml-1 mr-2 h-5 w-5" />
                Voltar
            </a>
        </div>

        <!-- Form -->
        <form id="sermon-form" action="{{ route('pastor.sermoes.sermons.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="information-circle" class="w-5 h-5 text-amber-500" />
                        Informações Básicas
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                            Título do Sermão <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('title') border-red-300 @enderror transition-all"
                            placeholder="Ex: A Importância do Amor na Vida Cristã">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtítulo -->
                    <div class="md:col-span-2">
                        <label for="subtitle" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                            Subtítulo
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm transition-all"
                            placeholder="Breve descrição do tema">
                    </div>

                    <!-- Category & Series -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:col-span-2">
                        <!-- Categoria -->
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Categoria
                            </label>
                            <select name="category_id" id="category_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Série -->
                        <div>
                            <label for="series_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Série Bíblica
                            </label>
                            <select name="series_id" id="series_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Selecione uma série (Opcional)</option>
                                @foreach ($series as $s)
                                    <option value="{{ $s->id }}" {{ old('series_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sugestão de Louvor -->
                        <div>
                            <label for="worship_suggestion_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Sugestão de Louvor
                            </label>
                            <select name="worship_suggestion_id" id="worship_suggestion_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Selecione um louvor (Opcional)</option>
                                @foreach ($worshipSongs as $song)
                                    <option value="{{ $song->id }}" {{ old('worship_suggestion_id') == $song->id ? 'selected' : '' }}>
                                        {{ $song->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Audio/Cover/Attachments Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                         <!-- Cover Image -->
                        <div>
                            <label for="cover_image_file" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Imagem de Capa
                            </label>
                            <div class="mt-1 flex items-center space-x-4 p-3 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50">
                                <div id="cover-preview" class="w-16 h-16 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden flex items-center justify-center flex-shrink-0">
                                    <x-icon name="photograph" class="w-8 h-8 text-gray-400" />
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="cover_image_file" id="cover_image_file" accept="image/*"
                                        class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 transition-all cursor-pointer"
                                        onchange="previewCover(event)">
                                    <p class="mt-1 text-[10px] text-gray-500">PNG, JPG ou GIF até 15MB</p>
                                </div>
                            </div>
                        </div>

                         <!-- Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Anexos (PDFs, Docs)
                            </label>
                            <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.doc,.docx,.txt"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/20 dark:file:text-amber-400">
                            <p class="mt-1 text-xs text-gray-500">Documentos de apoio para o estudo.</p>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                        <!-- Data -->
                        <div>
                            <label for="sermon_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Data do Sermão
                            </label>
                            <input type="date" name="sermon_date" id="sermon_date" value="{{ old('sermon_date', now()->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                        </div>

                        <!-- Status/Visibility (default: rascunho privado; marque "Publicar para a Igreja" para compartilhar) -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Por padrão o sermão fica <strong>privado</strong> e em <strong>rascunho</strong>. Marque "Publicar para a Igreja" para disponibilizar no painel.</p>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="publish_for_church" id="publish_for_church" value="1" class="rounded border-gray-300 dark:border-gray-600 text-amber-600 focus:ring-amber-500"
                                        onchange="document.getElementById('status').value = this.checked ? 'published' : 'draft'; document.getElementById('visibility').value = this.checked ? 'members' : 'private';">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Publicar para a Igreja</span>
                                </label>
                                <div class="flex gap-4">
                                    <div>
                                        <label for="status" class="block text-xs font-bold text-gray-500 dark:text-gray-400">Status</label>
                                        <select name="status" id="status" class="mt-0.5 block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                            <option value="draft" selected>Rascunho</option>
                                            <option value="published">Publicado</option>
                                            <option value="archived">Arquivado</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="visibility" class="block text-xs font-bold text-gray-500 dark:text-gray-400">Visibilidade</label>
                                        <select name="visibility" id="visibility" class="mt-0.5 block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                            <option value="public">Público</option>
                                            <option value="members">Membros</option>
                                            <option value="private" selected>Privado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de estrutura homilética -->
                        <div class="md:col-span-2">
                            <label for="sermon_structure_type" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tipo de estrutura</label>
                            <select name="sermon_structure_type" id="sermon_structure_type"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Nenhum (livre)</option>
                                <option value="expositivo" {{ old('sermon_structure_type') === 'expositivo' ? 'selected' : '' }}>Expositivo</option>
                                <option value="temático" {{ old('sermon_structure_type') === 'temático' ? 'selected' : '' }}>Temático</option>
                                <option value="textual" {{ old('sermon_structure_type') === 'textual' ? 'selected' : '' }}>Textual</option>
                            </select>
                            <div class="mt-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/40">
                                <p class="text-xs font-bold text-amber-800 dark:text-amber-200 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <x-icon name="scroll" class="w-3.5 h-3.5" /> Tipos de sermão
                                </p>
                                <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1.5">
                                    <li><strong>Expositivo:</strong> Foco no texto, contexto e aplicação.</li>
                                    <li><strong>Temático:</strong> Foco na doutrina e referências espalhadas.</li>
                                    <li><strong>Textual:</strong> Foco nas divisões do próprio versículo.</li>
                                </ul>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Guia visual para as seções do sermão (Isaltino Coelho).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tags</h3>
                <div class="flex items-center gap-2 mb-4">
                    <input type="text" id="tag-input"
                        class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                        placeholder="Digite uma tag e pressione Enter">
                    <button type="button" id="add-tag"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                        Adicionar
                    </button>
                </div>
                <div id="selected-tags" class="flex flex-wrap gap-2">
                    <!-- JS will populate -->
                </div>
            </div>

            <!-- Sermon Content -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="pen-fancy" class="w-5 h-5 text-amber-500" />
                        Conteúdo do Sermão
                    </h3>
                </div>

                <div class="space-y-6">
                    <x-rich-editor name="full_content" value="{{ old('full_content') }}" />
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <x-icon name="lightbulb" class="w-4 h-4 text-amber-500 shrink-0" />
                    <span>Use <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-xs font-mono">@</kbd> seguido do nome do livro para linkar versículos ao texto.</span>
                </p>

                <!-- Legacy Fields Collapsed -->
                <details class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <summary class="cursor-pointer text-sm font-medium text-gray-500 hover:text-amber-500">Mostrar Estrutura Tradicional (Opcional)</summary>
                    <div class="grid gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Introdução</label>
                            <textarea name="introduction" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700">{{ old('introduction') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desenvolvimento</label>
                            <textarea name="development" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700">{{ old('development') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conclusão</label>
                            <textarea name="conclusion" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700">{{ old('conclusion') }}</textarea>
                        </div>
                    </div>
                </details>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pb-20">
                <a href="{{ route('pastor.sermoes.sermons.index') }}"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-amber-600 hover:bg-amber-700 shadow-lg hover:shadow-xl transition-all">
                    Publicar Sermão
                </button>
            </div>
        </form>
        </div>

        <!-- Sidebar: Contexto Bíblico + Elias -->
        <aside class="space-y-4 order-first lg:order-last">
            @include('sermons::pastoralpanel.sermons.partials.contexto-biblico', ['bibleBooks' => $bibleBooks])
            @include('sermons::pastoralpanel.sermons.partials.elias-sermon-studio')
        </aside>
    </div>

    @include('sermons::pastoralpanel.sermons.partials.bible-picker')

    <!-- Tags JS Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sermonForm = document.getElementById('sermon-form');
            if (sermonForm) sermonForm.addEventListener('submit', function() {
                window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }));
            });
            const tagInput = document.getElementById('tag-input');
            const addTagBtn = document.getElementById('add-tag');
            const tagsContainer = document.getElementById('selected-tags');

            function addTag(name) {
                if(!name) return;
                const tag = document.createElement('span');
                tag.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200 mr-2 mb-2';
                tag.innerHTML = `
                    ${name}
                    <button type="button" class="ml-2 text-amber-600 hover:text-amber-800" onclick="this.parentElement.remove()">×</button>
                    <input type="hidden" name="tags[]" value="${name}">
                `;
                tagsContainer.appendChild(tag);
                tagInput.value = '';
            }

            addTagBtn.addEventListener('click', () => addTag(tagInput.value));
            tagInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTag(tagInput.value);
                }
            });
        });

        function previewCover(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('cover-preview');
                preview.innerHTML = `<img src="${reader.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    @vite(['Modules/Sermons/resources/assets/js/app.js'])
@endsection

