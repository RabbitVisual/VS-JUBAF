@extends('memberpanel::components.layouts.master')

@section('title', 'Editar Sermão')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Sermão</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Sermon Studio</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('memberpanel.sermons.show', $sermon) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <x-icon name="eye" class="w-4 h-4 mr-2" />
                        Ver
                    </a>
                    <a href="{{ route('memberpanel.sermons.my-sermons') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Voltar
                    </a>
                </div>
            </div>

            <form id="sermon-form" action="{{ route('memberpanel.sermons.update', $sermon) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-6">
                        <x-icon name="information-circle" class="w-5 h-5 text-amber-500" />
                        Informações Básicas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Título <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $sermon->title) }}" required
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                            @error('title')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="subtitle" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Subtítulo</label>
                            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $sermon->subtitle) }}"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Resumo</label>
                            <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">{{ old('description', $sermon->description) }}</textarea>
                        </div>
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Categoria</label>
                            <select name="category_id" id="category_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Selecione</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $sermon->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="series_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Série</label>
                            <select name="series_id" id="series_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Selecione</option>
                                @foreach ($series as $s)
                                    <option value="{{ $s->id }}" {{ old('series_id', $sermon->series_id) == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="sermon_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Data do Sermão</label>
                            <input type="date" name="sermon_date" id="sermon_date" value="{{ old('sermon_date', $sermon->sermon_date?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        </div>
                        <div>
                            <label for="sermon_structure_type" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tipo de estrutura</label>
                            <select name="sermon_structure_type" id="sermon_structure_type" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">Nenhum (livre)</option>
                                <option value="expositivo" {{ old('sermon_structure_type', $sermon->sermon_structure_type) === 'expositivo' ? 'selected' : '' }}>Expositivo</option>
                                <option value="temático" {{ old('sermon_structure_type', $sermon->sermon_structure_type) === 'temático' ? 'selected' : '' }}>Temático</option>
                                <option value="textual" {{ old('sermon_structure_type', $sermon->sermon_structure_type) === 'textual' ? 'selected' : '' }}>Textual</option>
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
                            </div>
                        </div>
                        <div class="flex gap-4 md:col-span-2">
                            <div class="flex-1">
                                <label for="status" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                    <option value="draft" {{ old('status', $sermon->status) === 'draft' ? 'selected' : '' }}>Rascunho</option>
                                    <option value="published" {{ old('status', $sermon->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label for="visibility" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Visibilidade</label>
                                <select name="visibility" id="visibility" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                    <option value="public" {{ old('visibility', $sermon->visibility) === 'public' ? 'selected' : '' }}>Público</option>
                                    <option value="members" {{ old('visibility', $sermon->visibility) === 'members' ? 'selected' : '' }}>Membros</option>
                                    <option value="private" {{ old('visibility', $sermon->visibility) === 'private' ? 'selected' : '' }}>Privado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tags</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <input type="text" id="tag-input" class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" placeholder="Digite uma tag e Enter">
                        <button type="button" id="add-tag" class="px-4 py-2 rounded-xl text-sm font-medium text-white bg-amber-600 hover:bg-amber-700">Adicionar</button>
                    </div>
                    <div id="selected-tags" class="flex flex-wrap gap-2">
                        @foreach($sermon->tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">
                                {{ $tag->name }}
                                <button type="button" class="ml-2 text-amber-600 hover:text-amber-800 remove-tag">×</button>
                                <input type="hidden" name="tags[]" value="{{ $tag->name }}">
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <x-icon name="pen-fancy" class="w-5 h-5 text-amber-500" />
                        Conteúdo do Sermão
                    </h3>
                    <x-rich-editor name="full_content" :value="old('full_content', $sermon->full_content ?? '')" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <x-icon name="lightbulb" class="w-4 h-4 text-amber-500 shrink-0" />
                        <span>Use <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-xs font-mono">@</kbd> seguido do nome do livro para linkar versículos ao texto.</span>
                    </p>
                    <details class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <summary class="cursor-pointer text-sm font-medium text-gray-500 hover:text-amber-500">Estrutura tradicional (opcional)</summary>
                        <div class="grid gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Introdução</label>
                                <textarea name="introduction" rows="3" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('introduction', $sermon->introduction) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desenvolvimento</label>
                                <textarea name="development" rows="3" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('development', $sermon->development) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conclusão</label>
                                <textarea name="conclusion" rows="3" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('conclusion', $sermon->conclusion) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aplicação</label>
                                <textarea name="application" rows="2" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('application', $sermon->application) }}</textarea>
                            </div>
                        </div>
                    </details>
                </div>

                @if($sermon->user_id === auth()->id())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="user-group" class="w-5 h-5 text-amber-500" />
                        Co-autores
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs font-bold cursor-help" title="Convide outros para editar este sermão; apenas você pode remover colaboradores. Convites são feitos pelo painel administrativo." aria-label="Ajuda">?</span>
                    </h3>
                    <ul class="space-y-3 text-sm">
                        @forelse($sermon->collaborators as $collab)
                            @php
                                $name = $collab->user->name ?? $collab->user->email;
                                $initial = mb_strtoupper(mb_substr($name, 0, 1));
                                $color = $collab->status === 'accepted' ? 'bg-green-500' : ($collab->status === 'pending' ? 'bg-amber-500' : 'bg-gray-500');
                            @endphp
                            <li class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-9 h-9 rounded-full {{ $color }} text-white flex items-center justify-center text-sm font-bold shrink-0" aria-hidden="true">{{ $initial }}</span>
                                    <span class="text-gray-900 dark:text-white truncate">{{ $name }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium shrink-0 {{ $collab->status === 'accepted' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : ($collab->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                    @if($collab->status === 'accepted')<x-icon name="check" class="w-3 h-3" />@elseif($collab->status === 'pending')<x-icon name="clock" class="w-3 h-3" />@else<x-icon name="xmark" class="w-3 h-3" />@endif
                                    {{ $collab->status_display }}
                                </span>
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400 py-2">Nenhum co-autor. Convites podem ser feitos pelo painel administrativo.</li>
                        @endforelse
                    </ul>
                </div>
                @endif

                <div class="flex items-center justify-end gap-3 pb-8">
                    @if($sermon->canDelete(auth()->user()))
                    <button type="button" onclick="if(confirm('Excluir este sermão?')) { window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Excluindo...' } })); document.getElementById('delete-form').submit(); }"
                        class="px-4 py-2 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 text-sm font-medium rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20">
                        Excluir
                    </button>
                    @endif
                    <a href="{{ route('memberpanel.sermons.show', $sermon) }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</a>
                    <button type="submit" class="px-6 py-3 rounded-xl text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 shadow-lg">Salvar alterações</button>
                </div>
            </form>

            @if($sermon->canDelete(auth()->user()))
            <form id="delete-form" action="{{ route('memberpanel.sermons.destroy', $sermon) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </div>

        <aside class="space-y-4 order-first lg:order-last">
            @include('sermons::admin.sermons.partials.contexto-biblico', ['bibleBooks' => $bibleBooks])
            @include('sermons::admin.sermons.partials.elias-sermon-studio')
        </aside>
    </div>
</div>

@include('sermons::admin.sermons.partials.bible-picker')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sermon-form');
    if (form) {
        form.addEventListener('submit', function() {
            window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }));
        });
    }
    const tagInput = document.getElementById('tag-input');
    const addTagBtn = document.getElementById('add-tag');
    const tagsContainer = document.getElementById('selected-tags');
    function addTag(name) {
        if (!name) return;
        const tag = document.createElement('span');
        tag.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200';
        tag.innerHTML = `${name} <button type="button" class="ml-2 text-amber-600 hover:text-amber-800 remove-tag">×</button><input type="hidden" name="tags[]" value="${name}">`;
        tagsContainer.appendChild(tag);
        tagInput.value = '';
        tag.querySelector('.remove-tag').addEventListener('click', () => tag.remove());
    }
    if (addTagBtn) addTagBtn.addEventListener('click', () => addTag(tagInput?.value));
    if (tagInput) tagInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); addTag(tagInput.value); } });
    document.querySelectorAll('#selected-tags .remove-tag').forEach(btn => btn.addEventListener('click', function() { this.closest('span').remove(); }));
});
</script>
@vite(['Modules/Sermons/resources/assets/js/app.js'])
@endsection
