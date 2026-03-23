@extends('admin::components.layouts.master')

@section('title', 'Editar Versão: ' . $version->name)

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Editar Versão da Bíblia</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize as informações da versão da Bíblia</p>
            </div>
            <a href="{{ route('admin.bible.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-linear-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="pen-to-square" style="duotone" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                    Informações da Versão
                </h2>
            </div>

            <form action="{{ route('admin.bible.update', $version) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Nome da Versão <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $version->name) }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                            placeholder="Ex: Almeida Revista e Atualizada">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Abbreviation and Language -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="abbreviation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Abreviação <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $version->abbreviation) }}" required maxlength="10"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                placeholder="Ex: ARA">
                            @error('abbreviation')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="language" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Idioma <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="language" name="language" value="{{ old('language', $version->language) }}" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                placeholder="Ex: pt-BR">
                            @error('language')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Descrição
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"
                            placeholder="Descrição opcional da versão da Bíblia">{{ old('description', $version->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Audio URL template (chapter audio) -->
                    <div>
                        <label for="audio_url_template" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Template URL do áudio por capítulo
                        </label>
                        <input type="text" id="audio_url_template" name="audio_url_template" value="{{ old('audio_url_template', $version->audio_url_template) }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors font-mono text-sm"
                            placeholder="Ex: https://seu-cdn.com/bible/NAA/{book_number}_{chapter_number}.mp3">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                            Use <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">{book_number}</code> e <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">{chapter_number}</code>. Deixe vazio se não houver áudio para esta versão.
                        </p>
                        <p class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Predefinidos:</span>
                            <button type="button" onclick="document.getElementById('audio_url_template').value='https://www.wordproaudio.org/bibles/app/audio/2_BR/{book_number}/{chapter_number}.mp3'" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition-colors">
                                <x-icon name="volume-high" class="w-3.5 h-3.5" />
                                WordProject (Português BR)
                            </button>
                        </p>
                        <p class="mt-2">
                            <a href="{{ route('admin.bible.chapter-audio.index', $version) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                                <x-icon name="volume-high" class="w-4 h-4" />
                                Gerenciar áudios por capítulo (Google Drive, etc.)
                            </a>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">— prioridade sobre o template acima.</span>
                        </p>
                        @error('audio_url_template')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Checkboxes -->
                    <div class="grid md:grid-cols-2 gap-6 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $version->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                Versão Ativa
                            </label>
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $version->is_default) ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="is_default" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                Versão Padrão
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.bible.index') }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

